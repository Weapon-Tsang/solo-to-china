$ErrorActionPreference = "Stop"

$Root = Split-Path -Parent $PSScriptRoot
$ContractPath = Join-Path $Root "wp-content/themes/solo-to-china/content-contract/content-contract.v2.json"
$RegistryPath = Join-Path $Root "wp-content/themes/solo-to-china/content-contract/component-registry.v1.json"
$RuntimePath = Join-Path $Root "wp-content/themes/solo-to-china/inc/content-contract.php"
$ComponentsRuntimePath = Join-Path $Root "wp-content/themes/solo-to-china/inc/content-components.php"
$RenderersRuntimePath = Join-Path $Root "wp-content/themes/solo-to-china/inc/content-renderers.php"
$ComponentsCssPath = Join-Path $Root "wp-content/themes/solo-to-china-child/assets/css/content-components.css"
$ParentEditorCssPath = Join-Path $Root "wp-content/themes/solo-to-china/assets/css/editor-style.css"
$ChildEditorCssPath = Join-Path $Root "wp-content/themes/solo-to-china-child/assets/css/editor-style.css"
$PreviewFixturesPath = Join-Path $Root "scripts/playground-fixtures.php"
$ThemeFunctionsPath = Join-Path $Root "wp-content/themes/solo-to-china/functions.php"
$Failures = New-Object System.Collections.Generic.List[string]

function Add-ContractFailure([string]$Message) {
    $Failures.Add($Message)
}

if (-not (Test-Path -LiteralPath $ContractPath -PathType Leaf)) {
    throw "Content Contract is missing: wp-content/themes/solo-to-china/content-contract/content-contract.v2.json"
}

try {
    $ContractRaw = Get-Content -LiteralPath $ContractPath -Raw
    $Contract = $ContractRaw | ConvertFrom-Json
    $RegistryRaw = Get-Content -LiteralPath $RegistryPath -Raw
    $Registry = $RegistryRaw | ConvertFrom-Json
} catch {
    throw "Content Contract or Component Registry is not valid JSON: $($_.Exception.Message)"
}

foreach ($TopLevelKey in @("contract_version", "theme_version", "principles", "presentation", "guide_types", "component_registry", "capabilities")) {
    if (-not $Contract.PSObject.Properties.Name.Contains($TopLevelKey)) {
        Add-ContractFailure "Content Contract is missing top-level key: $TopLevelKey"
    }
}
if ($Contract.component_registry.version -ne "1.0.0" -or $Registry.registry_version -ne "1.0.0") {
    Add-ContractFailure "Content Contract must reference Component Registry 1.0.0."
}
if ($Contract.PSObject.Properties.Name.Contains("components") -or $ContractRaw.Contains('"allowed_components"')) {
    Add-ContractFailure "Content Contract duplicates the canonical Component Registry list."
}

if ($Contract.contract_version -ne "2.0.0") {
    Add-ContractFailure "Content Contract version must be 2.0.0."
}
if ($Contract.theme_version -ne "0.25.0") {
    Add-ContractFailure "Content Contract theme_version must be 0.25.0."
}

if ($Contract.principles.frontend -ne "Render what CMS requests." -or $Contract.principles.cms -ne "Decide what the page contains." -or $Contract.principles.content_type -ne "Content type is taxonomy, not layout.") {
    Add-ContractFailure "Content Contract responsibility principles are incomplete."
}
if ($Contract.presentation.post_meta.show_share -ne "_stc_show_share" -or $Contract.presentation.post_meta.show_toc -ne "_stc_show_toc" -or $Contract.presentation.post_meta.hero_variant -ne "_stc_hero_variant") {
    Add-ContractFailure "Content Contract presentation metadata mapping is incomplete."
}

$ExpectedGuideTypes = [ordered]@{
    "survival-kit" = "survival-kit"
    "city-guide" = "city-guides"
    "attraction-guide" = "attraction-guides"
    "travel-guide" = "travel-guides"
}
$RequiredGuideKeys = @("slug", "label", "category_slug", "shell_behavior", "component_policy")

foreach ($GuideType in $ExpectedGuideTypes.Keys) {
    $GuideProperty = $Contract.guide_types.PSObject.Properties[$GuideType]
    if (-not $GuideProperty) {
        Add-ContractFailure "Content Contract is missing guide type: $GuideType"
        continue
    }

    $Guide = $GuideProperty.Value
    foreach ($GuideKey in $RequiredGuideKeys) {
        if (-not $Guide.PSObject.Properties.Name.Contains($GuideKey)) {
            Add-ContractFailure "Guide type $GuideType is missing: $GuideKey"
        }
    }
    if ($Guide.slug -ne $GuideType) {
        Add-ContractFailure "Guide type $GuideType has an unstable slug."
    }
    if ($Guide.category_slug -ne $ExpectedGuideTypes[$GuideType]) {
        Add-ContractFailure "Guide type $GuideType has the wrong category_slug."
    }
    if ($Guide.shell_behavior -ne "taxonomy-only") {
        Add-ContractFailure "Guide type $GuideType still encodes a page layout."
    }
}

$ExpectedComponents = @(
    "paragraph", "heading", "list", "image",
    "quick_answer", "key_takeaways", "quick_facts", "tip", "warning",
    "steps", "checklist", "comparison_table", "faq",
    "planner_cta", "ticket_reminder", "affiliate_cta"
)
$RequiredComponentKeys = @("id", "name", "category", "purpose", "status", "variants", "schema", "implementation_paths", "cms_usable", "cms_interface", "render_mode", "accessibility", "responsive", "example")
$Components = @($Registry.components | Where-Object { $_.cms_usable -eq $true -and $_.cms_interface -eq "page_block" })

foreach ($ComponentType in $ExpectedComponents) {
    $Matches = @($Components | Where-Object { $_.id -eq $ComponentType })
    if ($Matches.Count -ne 1) {
        Add-ContractFailure "Content Contract must define component exactly once: $ComponentType"
    }
}

foreach ($Component in $Components) {
    foreach ($ComponentKey in $RequiredComponentKeys) {
        if (-not $Component.PSObject.Properties.Name.Contains($ComponentKey)) {
            Add-ContractFailure "Component $($Component.id) is missing: $ComponentKey"
        }
    }

    $FieldNames = @($Component.schema.properties.PSObject.Properties.Name)
    foreach ($FieldName in @($Component.schema.required)) {
        if (-not $FieldNames.Contains([string]$FieldName)) {
            Add-ContractFailure "Component $($Component.id) references undefined required field: $FieldName"
        }
    }
}

foreach ($DynamicType in @("planner_cta", "ticket_reminder", "affiliate_cta")) {
    $DynamicComponent = $Components | Where-Object { $_.id -eq $DynamicType } | Select-Object -First 1
    if ($DynamicComponent.render_mode -ne "shortcode") {
        Add-ContractFailure "Dynamic component $DynamicType must declare its shortcode render mode."
    }
}

foreach ($ForbiddenContractToken in @("--stc-", "font-size", "padding", "margin", "box-shadow", "breakpoint", "claim_keys", "source_ids", "source_asset_id")) {
    if ($ContractRaw.IndexOf($ForbiddenContractToken, [System.StringComparison]::OrdinalIgnoreCase) -ge 0) {
        Add-ContractFailure "Content Contract exposes forbidden presentation or provenance token: $ForbiddenContractToken"
    }
}

if (-not (Test-Path -LiteralPath $RuntimePath -PathType Leaf)) {
    Add-ContractFailure "Content Contract runtime is missing."
} else {
    $Runtime = Get-Content -LiteralPath $RuntimePath -Raw
    foreach ($RuntimeToken in @("STC_CONTENT_CONTRACT_VERSION", "register_rest_route", "stc/v1", "content-contract", "WP_REST_Server::READABLE", "permission_callback", "__return_true", "ETag", "Last-Modified", "Cache-Control", "register_post_meta", "_stc_guide_type", "_stc_content_contract_version", "_stc_show_share", "_stc_show_toc", "_stc_hero_variant", "stc_page_presentation_enabled", "show_in_rest", "sanitize_callback", "auth_callback", "current_user_can", "edit_post", "stc_get_explicit_guide_type")) {
        if (-not $Runtime.Contains($RuntimeToken)) {
            Add-ContractFailure "Content Contract runtime is missing: $RuntimeToken"
        }
    }
}

if (Test-Path -LiteralPath $ThemeFunctionsPath -PathType Leaf) {
    $ThemeFunctions = Get-Content -LiteralPath $ThemeFunctionsPath -Raw
    if (-not $ThemeFunctions.Contains("inc/content-contract.php")) {
        Add-ContractFailure "Parent Theme does not load the Content Contract runtime."
    }
    if (-not $ThemeFunctions.Contains("inc/component-registry.php")) {
        Add-ContractFailure "Parent Theme does not load the Component Registry runtime."
    }
    if (-not $ThemeFunctions.Contains("inc/content-components.php")) {
        Add-ContractFailure "Parent Theme does not load the Content Component runtime."
    }
    if (-not $ThemeFunctions.Contains("inc/content-renderers.php")) {
        Add-ContractFailure "Parent Theme does not load the dynamic Content Component renderers."
    }
}

if (-not (Test-Path -LiteralPath $RenderersRuntimePath -PathType Leaf)) {
    Add-ContractFailure "Dynamic Content Component renderers are missing."
} else {
    $RenderersRuntime = Get-Content -LiteralPath $RenderersRuntimePath -Raw
    foreach ($RendererToken in @("stc_render_planner_cta_component", "stc_render_ticket_reminder_component", "stc_render_affiliate_cta_component", "stc_planner_cta", "stc_ticket_reminder", "stc_affiliate_cta", "wp_parse_url", "https", "sponsored nofollow noopener", "shortcode_exists", "solo_to_china_ticket_tool", "do_shortcode", "esc_html", "esc_attr", "esc_url")) {
        if (-not $RenderersRuntime.Contains($RendererToken)) {
            Add-ContractFailure "Dynamic Content Component runtime is missing: $RendererToken"
        }
    }
    foreach ($ForbiddenRendererToken in @("stc_tools_get_attractions", "booking_lead_days", "localStorage", "wp_insert_post", "wp_remote_get")) {
        if ($RenderersRuntime.Contains($ForbiddenRendererToken)) {
            Add-ContractFailure "Theme renderer crosses the Ticket Plugin or data boundary: $ForbiddenRendererToken"
        }
    }
}

if (-not (Test-Path -LiteralPath $ComponentsRuntimePath -PathType Leaf)) {
    Add-ContractFailure "Content Component Gutenberg runtime is missing."
} else {
    $ComponentsRuntime = Get-Content -LiteralPath $ComponentsRuntimePath -Raw
    foreach ($PatternToken in @("register_block_pattern", "solo-to-china/content-components", "stc-content-block--quick-answer", "stc-content-block--key-takeaways", "stc-content-block--quick-facts", "stc-content-block--tip", "stc-content-block--warning", "stc-content-block--steps", "stc-content-block--checklist", "stc-content-block--comparison", "stc-content-block--faq", "<!-- wp:group", "<!-- wp:details", "<!-- wp:table")) {
        if (-not $ComponentsRuntime.Contains($PatternToken)) {
            Add-ContractFailure "Content Component Gutenberg runtime is missing: $PatternToken"
        }
    }
    foreach ($MediaRuntimeToken in @("render_block_core/image", "stc_render_content_image_attributes", "WP_HTML_Tag_Processor", "loading", "lazy", "decoding", "async")) {
        if (-not $ComponentsRuntime.Contains($MediaRuntimeToken)) {
            Add-ContractFailure "Content Component Media runtime is missing: $MediaRuntimeToken"
        }
    }
    foreach ($ForbiddenMarkupToken in @("<!-- wp:html", "<style", " style=")) {
        if ($ComponentsRuntime.IndexOf($ForbiddenMarkupToken, [System.StringComparison]::OrdinalIgnoreCase) -ge 0) {
            Add-ContractFailure "Content Component patterns include forbidden markup: $ForbiddenMarkupToken"
        }
    }
}

if (-not (Test-Path -LiteralPath $ComponentsCssPath -PathType Leaf)) {
    Add-ContractFailure "Child Theme Content Component CSS is missing."
} else {
    $ComponentsCss = Get-Content -LiteralPath $ComponentsCssPath -Raw
    foreach ($CssToken in @(".stc-content-block", ".stc-content-block--quick-answer", ".stc-content-block--key-takeaways", ".stc-content-block--quick-facts", ".stc-content-block--tip", ".stc-content-block--warning", ".stc-content-block--steps", ".stc-content-block--checklist", ".stc-content-block--comparison", ".stc-content-block--faq", ".stc-content-image", ".stc-content-image--evidence", ".stc-content-image--context", "figcaption", "height: auto", "max-width: 100%", ".stc-dynamic-component", ".stc-dynamic-component--planner", ".stc-dynamic-component--ticket", ".stc-dynamic-component--affiliate", "overflow-x: auto", "details", "summary", "--stc-color-", "--stc-space-", "--stc-radius-", "--stc-motion-", "@media (max-width: 840px)", "@media (max-width: 599px)")) {
        if (-not $ComponentsCss.Contains($CssToken)) {
            Add-ContractFailure "Content Component CSS is missing: $CssToken"
        }
    }
    if ($ComponentsCss -match "#[0-9a-fA-F]{3,8}") {
        Add-ContractFailure "Content Component CSS introduces raw color values instead of Design System tokens."
    }
}

foreach ($EditorCssDefinition in @(
    @{ Path = $ParentEditorCssPath; Name = "Parent"; Tokens = @(".editor-styles-wrapper", ".stc-content-block", ".stc-dynamic-component", ".stc-content-image") },
    @{ Path = $ChildEditorCssPath; Name = "Child"; Tokens = @(".editor-styles-wrapper", "--stc-color-", "--stc-container-reading") }
)) {
    if (-not (Test-Path -LiteralPath $EditorCssDefinition.Path -PathType Leaf)) {
        Add-ContractFailure "$($EditorCssDefinition.Name) Theme editor stylesheet is missing."
        continue
    }
    $EditorCss = Get-Content -LiteralPath $EditorCssDefinition.Path -Raw
    foreach ($EditorCssToken in $EditorCssDefinition.Tokens) {
        if (-not $EditorCss.Contains($EditorCssToken)) {
            Add-ContractFailure "$($EditorCssDefinition.Name) Theme editor stylesheet is missing: $EditorCssToken"
        }
    }
}

if (Test-Path -LiteralPath $PreviewFixturesPath -PathType Leaf) {
    $PreviewFixtures = Get-Content -LiteralPath $PreviewFixturesPath -Raw
    foreach ($MediaFixtureToken in @("wp_upload_bits", "wp_insert_attachment", "wp_generate_attachment_metadata", "wp_get_attachment_image_url", "stc-content-image--context", "wp-image-", "wp-element-caption")) {
        if (-not $PreviewFixtures.Contains($MediaFixtureToken)) {
            Add-ContractFailure "Playground Media fixture is missing: $MediaFixtureToken"
        }
    }
}

if ($Failures.Count -gt 0) {
    throw ($Failures -join [Environment]::NewLine)
}

Write-Host "SoloToChina Content Contract verification passed."
