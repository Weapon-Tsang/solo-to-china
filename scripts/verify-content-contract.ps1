$ErrorActionPreference = "Stop"

$Root = Split-Path -Parent $PSScriptRoot
$ContractPath = Join-Path $Root "wp-content/themes/solo-to-china/content-contract/content-contract.v1.json"
$RuntimePath = Join-Path $Root "wp-content/themes/solo-to-china/inc/content-contract.php"
$ThemeFunctionsPath = Join-Path $Root "wp-content/themes/solo-to-china/functions.php"
$Failures = New-Object System.Collections.Generic.List[string]

function Add-ContractFailure([string]$Message) {
    $Failures.Add($Message)
}

if (-not (Test-Path -LiteralPath $ContractPath -PathType Leaf)) {
    throw "Content Contract is missing: wp-content/themes/solo-to-china/content-contract/content-contract.v1.json"
}

try {
    $ContractRaw = Get-Content -LiteralPath $ContractPath -Raw
    $Contract = $ContractRaw | ConvertFrom-Json
} catch {
    throw "Content Contract is not valid JSON: $($_.Exception.Message)"
}

foreach ($TopLevelKey in @("contract_version", "theme_version", "guide_types", "components", "capabilities")) {
    if (-not $Contract.PSObject.Properties.Name.Contains($TopLevelKey)) {
        Add-ContractFailure "Content Contract is missing top-level key: $TopLevelKey"
    }
}

if ($Contract.contract_version -ne "1.0.0") {
    Add-ContractFailure "Content Contract version must be 1.0.0."
}
if ($Contract.theme_version -ne "0.22.0") {
    Add-ContractFailure "Content Contract theme_version must be 0.22.0."
}

$ExpectedGuideTypes = [ordered]@{
    "survival-kit" = "survival-kit"
    "city-guide" = "city-guides"
    "attraction-guide" = "attraction-guides"
    "travel-guide" = "travel-guides"
}
$RequiredGuideKeys = @("slug", "label", "category_slug", "shell_behavior", "allowed_components", "optional_dynamic_components")

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
}

$ExpectedComponents = @(
    "paragraph", "heading", "list", "image",
    "quick_answer", "key_takeaways", "quick_facts", "tip", "warning",
    "steps", "checklist", "comparison_table", "faq",
    "planner_cta", "ticket_reminder", "affiliate_cta"
)
$RequiredComponentKeys = @("type", "description", "fields", "required_fields", "optional_fields", "allowed_guide_types", "render_mode", "anchorable", "accessibility_notes")
$Components = @($Contract.components)

foreach ($ComponentType in $ExpectedComponents) {
    $Matches = @($Components | Where-Object { $_.type -eq $ComponentType })
    if ($Matches.Count -ne 1) {
        Add-ContractFailure "Content Contract must define component exactly once: $ComponentType"
    }
}

foreach ($Component in $Components) {
    foreach ($ComponentKey in $RequiredComponentKeys) {
        if (-not $Component.PSObject.Properties.Name.Contains($ComponentKey)) {
            Add-ContractFailure "Component $($Component.type) is missing: $ComponentKey"
        }
    }

    $FieldNames = @($Component.fields.PSObject.Properties.Name)
    foreach ($FieldName in @($Component.required_fields) + @($Component.optional_fields)) {
        if (-not $FieldNames.Contains([string]$FieldName)) {
            Add-ContractFailure "Component $($Component.type) references undefined field: $FieldName"
        }
    }

    foreach ($AllowedGuideType in @($Component.allowed_guide_types)) {
        if (-not $ExpectedGuideTypes.Contains([string]$AllowedGuideType)) {
            Add-ContractFailure "Component $($Component.type) allows unknown guide type: $AllowedGuideType"
        }
    }

    if (-not $Component.PSObject.Properties.Name.Contains("semantic_class") -and -not $Component.PSObject.Properties.Name.Contains("block_name") -and -not $Component.PSObject.Properties.Name.Contains("renderer")) {
        Add-ContractFailure "Component $($Component.type) needs a semantic_class, block_name, or renderer."
    }
}

foreach ($DynamicType in @("planner_cta", "ticket_reminder", "affiliate_cta")) {
    $DynamicComponent = $Components | Where-Object { $_.type -eq $DynamicType } | Select-Object -First 1
    if (-not $DynamicComponent.dynamic -or -not $DynamicComponent.renderer) {
        Add-ContractFailure "Dynamic component $DynamicType must declare dynamic=true and a renderer."
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
    foreach ($RuntimeToken in @("STC_CONTENT_CONTRACT_VERSION", "register_rest_route", "stc/v1", "content-contract", "WP_REST_Server::READABLE", "permission_callback", "__return_true", "ETag", "Last-Modified", "Cache-Control", "register_post_meta", "_stc_guide_type", "_stc_content_contract_version", "show_in_rest", "sanitize_callback", "auth_callback", "current_user_can", "edit_post", "stc_get_explicit_guide_type")) {
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
}

if ($Failures.Count -gt 0) {
    throw ($Failures -join [Environment]::NewLine)
}

Write-Host "SoloToChina Content Contract verification passed."
