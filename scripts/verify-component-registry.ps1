param()

$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent $PSScriptRoot
$Failures = New-Object System.Collections.Generic.List[string]

function Assert-Registry([bool]$Condition, [string]$Message) {
    if (-not $Condition) {
        $Failures.Add($Message)
    }
}

function Read-ProjectFile([string]$Path) {
    $FullPath = Join-Path $Root $Path
    Assert-Registry (Test-Path -LiteralPath $FullPath -PathType Leaf) "Missing registry artifact: $Path"
    if (-not (Test-Path -LiteralPath $FullPath -PathType Leaf)) {
        return ""
    }
    return Get-Content -LiteralPath $FullPath -Raw
}

$RegistryRaw = Read-ProjectFile "wp-content/themes/solo-to-china/content-contract/component-registry.v1.json"
$ContractRaw = Read-ProjectFile "wp-content/themes/solo-to-china/content-contract/content-contract.v2.json"
$Runtime = Read-ProjectFile "wp-content/themes/solo-to-china/inc/component-registry.php"
$Patterns = Read-ProjectFile "wp-content/themes/solo-to-china/inc/content-components.php"
$Gallery = Read-ProjectFile "wp-content/themes/solo-to-china/page-design-system.php"
$GalleryCss = Read-ProjectFile "wp-content/themes/solo-to-china-child/assets/css/component-gallery.css"
$Catalog = Read-ProjectFile "docs/COMPONENT_LIBRARY.md"
$Fixtures = Read-ProjectFile "scripts/playground-fixtures.php"
$PublishedContractRaw = Read-ProjectFile "contracts/component-registry.json"
$PageSchemaRaw = Read-ProjectFile "contracts/page-schema.json"
$CmsContractDoc = Read-ProjectFile "docs/CMS_FRONTEND_CONTRACT.md"
$ComponentChangelog = Read-ProjectFile "docs/COMPONENT_CHANGELOG.md"
$Generator = Read-ProjectFile "scripts/generate-component-catalog.ps1"

if ($RegistryRaw) {
    try {
        $Registry = $RegistryRaw | ConvertFrom-Json
    } catch {
        $Failures.Add("Component Registry is not valid JSON: $($_.Exception.Message)")
    }
}

if ($Registry) {
    Assert-Registry ($Registry.registry_version -eq "1.0.0") "Component Registry version must be 1.0.0."
    Assert-Registry ($Registry.principles.frontend -eq "Frontend defines what can be rendered.") "Frontend component capability principle is missing."
    Assert-Registry ($Registry.principles.cms -eq "CMS decides what should be rendered.") "CMS selection principle is missing."

    $ExpectedCmsIds = @(
        "paragraph", "heading", "list", "image", "quick_answer", "key_takeaways", "quick_facts", "tip", "warning",
        "steps", "checklist", "comparison_table", "faq", "planner_cta", "ticket_reminder", "affiliate_cta",
        "article_hero", "share_this_page", "table_of_contents"
    )
    $CmsComponents = @($Registry.components | Where-Object { $_.cms_usable -eq $true })
    $ActualIds = @($Registry.components | ForEach-Object { $_.id })
    $DuplicateIds = @($ActualIds | Group-Object | Where-Object { $_.Count -gt 1 })

    Assert-Registry ($DuplicateIds.Count -eq 0) "Component IDs must be unique stable API values."
    Assert-Registry ($CmsComponents.Count -eq 19) "Registry must expose exactly the 19 capabilities that currently exist for CMS selection."
    foreach ($ExpectedId in $ExpectedCmsIds) {
        Assert-Registry ($CmsComponents.id -contains $ExpectedId) "CMS component is missing from Registry: $ExpectedId"
    }

    foreach ($Component in $Registry.components) {
        Assert-Registry (-not [string]::IsNullOrWhiteSpace([string]$Component.id)) "A component is missing id."
        Assert-Registry (-not [string]::IsNullOrWhiteSpace([string]$Component.name)) "Component $($Component.id) is missing name."
        Assert-Registry (-not [string]::IsNullOrWhiteSpace([string]$Component.category)) "Component $($Component.id) is missing category."
        Assert-Registry (@("stable", "experimental", "deprecated") -contains $Component.status) "Component $($Component.id) has an unsupported status."
        Assert-Registry ($Component.variants.Count -ge 1) "Component $($Component.id) must declare supported variants."
        Assert-Registry ($null -ne $Component.schema -and $Component.schema.type -eq "object") "Component $($Component.id) must declare an object input schema."
        Assert-Registry ($null -ne $Component.example) "Component $($Component.id) must include an example."
        Assert-Registry ($Component.implementation_paths.Count -ge 1) "Component $($Component.id) must identify its implementation path."
        Assert-Registry (@("page_block", "presentation_meta", "internal") -contains $Component.cms_interface) "Component $($Component.id) has an unsupported CMS interface."
        if ($Component.cms_usable) {
            Assert-Registry ($Component.cms_interface -ne "internal") "CMS component $($Component.id) cannot use the internal interface."
        }
    }

    Assert-Registry (-not ($RegistryRaw -match "large-red-card|green-box|rounded-shadow-card")) "Registry variants must express semantics, not visual implementation."

    if ($PublishedContractRaw) {
        try {
            $PublishedContract = $PublishedContractRaw | ConvertFrom-Json
        } catch {
            $Failures.Add("Published Component Contract is not valid JSON: $($_.Exception.Message)")
        }
    }

    if ($PublishedContract) {
        Assert-Registry ($PublishedContract.contractVersion -eq $Registry.registry_version) "Published Contract version must match the canonical Registry."
        Assert-Registry ($PublishedContract.schemaVersion -eq "2020-12") "Published Contract must declare schemaVersion 2020-12."
        Assert-Registry ($PublishedContract.components.Count -eq 19) "Published Contract must contain only the 19 CMS-usable capabilities."
        Assert-Registry (-not ($PublishedContract.components.cmsUsable -contains $false)) "Published Contract must not include internal components."
        Assert-Registry ((@($PublishedContract.components.id) -join ",") -eq (@($CmsComponents.id) -join ",")) "Published Contract component IDs/order must derive from the canonical Registry."

        foreach ($PublishedComponent in $PublishedContract.components) {
            $SourceComponent = $CmsComponents | Where-Object { $_.id -eq $PublishedComponent.id } | Select-Object -First 1
            $ExpectedRequired = @($SourceComponent.schema.required)
            $ExpectedOptional = @($SourceComponent.schema.properties.PSObject.Properties.Name | Where-Object { $ExpectedRequired -notcontains $_ })
            Assert-Registry ($PublishedComponent.deprecated -eq ($SourceComponent.status -eq "deprecated")) "Published deprecation state drifted for $($PublishedComponent.id)."
            Assert-Registry ((@($PublishedComponent.requiredFields) -join ",") -eq ($ExpectedRequired -join ",")) "Published required fields drifted for $($PublishedComponent.id)."
            Assert-Registry ((@($PublishedComponent.optionalFields) -join ",") -eq ($ExpectedOptional -join ",")) "Published optional fields drifted for $($PublishedComponent.id)."
            Assert-Registry ($PublishedComponent.example.PSObject.Properties.Name -contains "data") "Published example must use type/variant/data for $($PublishedComponent.id)."
        }
    }

    if ($PageSchemaRaw) {
        try {
            $PageSchema = $PageSchemaRaw | ConvertFrom-Json
        } catch {
            $Failures.Add("Page Schema Contract is not valid JSON: $($_.Exception.Message)")
        }
    }

    if ($PageSchema) {
        $PageBlockComponents = @($CmsComponents | Where-Object { $_.cms_interface -eq "page_block" })
        $BlockSchemas = @($PageSchema.properties.blocks.items.oneOf)
        $BlockTypeIds = @($BlockSchemas | ForEach-Object { $_.properties.type.const })
        Assert-Registry ($PageSchema.contractVersion -eq $Registry.registry_version) "Page Schema Contract version must match the Registry."
        Assert-Registry ($PageSchema.schemaVersion -eq "2020-12") "Page Schema must declare schemaVersion 2020-12."
        Assert-Registry ($BlockSchemas.Count -eq 16) "Page Schema must expose exactly the 16 ordered page-block components."
        Assert-Registry (($BlockTypeIds -join ",") -eq (@($PageBlockComponents.id) -join ",")) "Page Schema block types must derive from the Registry."
        Assert-Registry ($PageSchema.properties.blocks.description -match "final render order") "Page Schema must define blocks array order as final render order."
        Assert-Registry ($PageSchema.properties.metadata.properties.contentType.description -match "taxonomy, not layout") "Page Schema must define content type as taxonomy, not layout."
    }
}

Assert-Registry ($ContractRaw.Contains('"component_registry"')) "Content Contract must reference the Component Registry."
if ($ContractRaw) {
    $Contract = $ContractRaw | ConvertFrom-Json
    Assert-Registry (-not $Contract.PSObject.Properties.Name.Contains("components")) "Content Contract must not duplicate the Component Registry component list."
}
Assert-Registry (-not $ContractRaw.Contains('"allowed_components"')) "Guide types must not duplicate Registry component IDs."

foreach ($Token in @("STC_COMPONENT_REGISTRY_VERSION", "stc_get_component_registry", "stc_get_cms_component_definitions", "component-registry", "register_rest_route")) {
    Assert-Registry ($Runtime.Contains($Token)) "Component Registry runtime is missing: $Token"
}
Assert-Registry ($Patterns.Contains("stc_get_component_definition")) "Gutenberg component registration must read metadata from the Registry."
Assert-Registry ($Gallery.Contains("stc_get_cms_component_definitions")) "Component Gallery must enumerate the Registry."
Assert-Registry ($Gallery.Contains("the_content()")) "Component Gallery must render real CMS-authored examples."
Assert-Registry ($GalleryCss.Contains(".stc-component-gallery")) "Component Gallery stylesheet is missing."
Assert-Registry ($Fixtures.Contains("'design-system'")) "Playground must create the internal Component Gallery fixture."
Assert-Registry ($Catalog.Contains('Generated from `component-registry.v1.json`')) "Human-readable Component Catalog must identify its Registry source."
Assert-Registry ($Catalog.Contains("Available Components")) "Component Catalog is missing its available component section."
Assert-Registry ($Catalog.Contains("Not Yet Componentized")) "Component Catalog must distinguish hardcoded page composition."
Assert-Registry ($Catalog.Contains("Proposed, Not Available")) "Component Catalog must distinguish proposed capabilities."
foreach ($Token in @("Frontend defines what CAN be rendered", "CMS decides what SHOULD be rendered", "contracts/component-registry.json", "contracts/page-schema.json", "docs/COMPONENT_LIBRARY.md", "docs/COMPONENT_CHANGELOG.md")) {
    Assert-Registry ($CmsContractDoc.Contains($Token)) "CMS/Frontend Contract documentation is missing: $Token"
}
Assert-Registry ($ComponentChangelog.Contains("## 1.0.0")) "Component Changelog must record Registry 1.0.0."
Assert-Registry ($ComponentChangelog.Contains("Pure visual changes are excluded")) "Component Changelog must exclude visual-only changes."
foreach ($Token in @("contracts/component-registry.json", "contracts/page-schema.json", "docs/COMPONENT_LIBRARY.md")) {
    Assert-Registry ($Generator.Contains($Token)) "Registry generator must publish: $Token"
}

if ($Failures.Count -gt 0) {
    throw ($Failures -join [Environment]::NewLine)
}

Write-Host "SoloToChina Component Registry verification passed."
