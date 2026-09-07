param()

$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent $PSScriptRoot
$RegistryPath = Join-Path $Root "wp-content/themes/solo-to-china/content-contract/component-registry.v1.json"
$OutputPath = Join-Path $Root "docs/COMPONENT_LIBRARY.md"
$PublishedContractPath = Join-Path $Root "contracts/component-registry.json"
$PageSchemaPath = Join-Path $Root "contracts/page-schema.json"
$ThemePublishedContractPath = Join-Path $Root "wp-content/themes/solo-to-china/content-contract/component-registry.generated.json"
$ThemePageSchemaPath = Join-Path $Root "wp-content/themes/solo-to-china/content-contract/page-schema.generated.json"
$PublishPackageSchemaPath = Join-Path $Root "contracts/cms-publish-package.schema.json"
$ThemePublishPackageSchemaPath = Join-Path $Root "wp-content/themes/solo-to-china/content-contract/cms-publish-package.generated.json"
$Registry = Get-Content -LiteralPath $RegistryPath -Raw | ConvertFrom-Json
$CmsComponents = @($Registry.components | Where-Object { $_.cms_usable -eq $true })
$InternalComponents = @($Registry.components | Where-Object { $_.cms_usable -ne $true })
$Builder = [System.Text.StringBuilder]::new()

function ConvertTo-CanonicalJson($Value, [int]$Level = 0) {
    if ($null -eq $Value) {
        return "null"
    }
    if ($Value -is [bool]) {
        return $(if ($Value) { "true" } else { "false" })
    }
    if ($Value -is [string] -or $Value -is [char]) {
        return ($Value.ToString() | ConvertTo-Json -Compress)
    }
    # PowerShell decorates primitive values with PSObject properties. Test numeric
    # primitives and arrays before PSCustomObject or numbers can serialize as `{}`
    # and arrays can expose Count/SyncRoot implementation details.
    if ($Value -is [byte] -or $Value -is [sbyte] -or $Value -is [int16] -or $Value -is [uint16] -or
        $Value -is [int32] -or $Value -is [uint32] -or $Value -is [int64] -or $Value -is [uint64] -or
        $Value -is [single] -or $Value -is [double] -or $Value -is [decimal]) {
        return $Value.ToString($null, [System.Globalization.CultureInfo]::InvariantCulture)
    }
    if ($Value -is [System.Collections.IDictionary]) {
        $Keys = @($Value.Keys | ForEach-Object { $_.ToString() } | Sort-Object)
        if ($Keys.Count -eq 0) {
            return "{}"
        }
        $Indent = "  " * $Level
        $ChildIndent = "  " * ($Level + 1)
        $Entries = @(
            foreach ($Key in $Keys) {
                $EncodedKey = $Key | ConvertTo-Json -Compress
                "$ChildIndent${EncodedKey}: $(ConvertTo-CanonicalJson $Value[$Key] ($Level + 1))"
            }
        )
        return "{`n$($Entries -join ",`n")`n$Indent}"
    }
    if ($Value -is [System.Collections.IEnumerable]) {
        $Items = @($Value)
        if ($Items.Count -eq 0) {
            return "[]"
        }
        $Indent = "  " * $Level
        $ChildIndent = "  " * ($Level + 1)
        $Entries = @($Items | ForEach-Object { "$ChildIndent$(ConvertTo-CanonicalJson $_ ($Level + 1))" })
        return "[`n$($Entries -join ",`n")`n$Indent]"
    }
    if ($Value -is [pscustomobject]) {
        $Properties = @($Value.PSObject.Properties | Sort-Object Name)
        if ($Properties.Count -eq 0) {
            return "{}"
        }
        $Indent = "  " * $Level
        $ChildIndent = "  " * ($Level + 1)
        $Entries = @(
            foreach ($Property in $Properties) {
                $EncodedKey = $Property.Name | ConvertTo-Json -Compress
                "$ChildIndent${EncodedKey}: $(ConvertTo-CanonicalJson $Property.Value ($Level + 1))"
            }
        )
        return "{`n$($Entries -join ",`n")`n$Indent}"
    }
    return ($Value.ToString() | ConvertTo-Json -Compress)
}

function Write-JsonContract([string]$Path, $Value) {
    $Directory = Split-Path -Parent $Path
    if (-not (Test-Path -LiteralPath $Directory -PathType Container)) {
        New-Item -ItemType Directory -Path $Directory | Out-Null
    }
    $Json = ConvertTo-CanonicalJson $Value
    [System.IO.File]::WriteAllText($Path, $Json + [Environment]::NewLine, [System.Text.UTF8Encoding]::new($false))
}

function Add-Line([string]$Line = "") {
    [void]$Builder.AppendLine($Line)
}

function Add-ComponentSection($Component) {
    $Required = @($Component.schema.required)
    $PropertyNames = @($Component.schema.properties.PSObject.Properties.Name)
    $Optional = @($PropertyNames | Where-Object { $Required -notcontains $_ })
    $Variants = @($Component.variants) -join ", "
    $Paths = @($Component.implementation_paths | ForEach-Object { "``$_``" }) -join ", "
    $RequiredText = if ($Required.Count) { ($Required | ForEach-Object { "``$_``" }) -join ", " } else { "None" }
    $OptionalText = if ($Optional.Count) { ($Optional | ForEach-Object { "``$_``" }) -join ", " } else { "None" }
    $Example = $Component.example | ConvertTo-Json -Depth 20

    Add-Line "### ``$($Component.id)`` — $($Component.name)"
    Add-Line
    Add-Line "- Category: ``$($Component.category)``"
    Add-Line "- Status: ``$($Component.status)``"
    Add-Line "- CMS usable: ``$($Component.cms_usable.ToString().ToLowerInvariant())`` via ``$($Component.cms_interface)``"
    Add-Line "- Purpose: $($Component.purpose)"
    Add-Line "- Variants: ``$Variants``"
    Add-Line "- Required fields: $RequiredText"
    Add-Line "- Optional fields: $OptionalText"
    Add-Line "- Implementation: $Paths"
    Add-Line "- Accessibility: $($Component.accessibility)"
    Add-Line "- Responsive behavior: $($Component.responsive)"
    Add-Line
    Add-Line "Schema:"
    Add-Line
    Add-Line '```json'
    Add-Line ($Component.schema | ConvertTo-Json -Depth 20)
    Add-Line '```'
    Add-Line
    Add-Line "Example:"
    Add-Line
    Add-Line '```json'
    Add-Line $Example
    Add-Line '```'
    Add-Line
}

Add-Line "# SoloToChina Frontend Component Catalog"
Add-Line
Add-Line "Generated from ``component-registry.v1.json``. Do not edit component capability details here by hand; update the Registry, implementations, Gallery, and tests, then run ``.\scripts\generate-component-catalog.ps1``."
Add-Line
Add-Line "Registry version: ``$($Registry.registry_version)``"
Add-Line
Add-Line "CMS-usable capabilities: ``$($CmsComponents.Count)``"
Add-Line
Add-Line "Internal rendering components recorded: ``$($InternalComponents.Count)``"
Add-Line
Add-Line "## Contract Boundary"
Add-Line
Add-Line "Frontend defines what can be rendered. CMS decides what should be rendered. Content type remains taxonomy, not layout."
Add-Line
Add-Line '```text'
Add-Line "Frontend Component Registry"
Add-Line "  -> CMS reads declared components and variants"
Add-Line "  -> CMS selects supported components"
Add-Line "  -> CMS outputs ordered page.blocks[] and explicit presentation metadata"
Add-Line "  -> Frontend Renderer resolves the same Registry"
Add-Line "  -> Implemented component renders"
Add-Line '```'
Add-Line
Add-Line "Unknown component IDs must be rejected by the CMS. Unknown Gutenberg blocks reaching WordPress must degrade safely. The frontend must not add components because of page type."
Add-Line
Add-Line "## Available Components"
Add-Line
Add-Line "These are the only capabilities currently available for CMS selection. ``page_block`` entries belong in ordered content; ``presentation_meta`` entries are explicit page-level controls."
Add-Line
Add-Line "| ID | Name | Category | Interface | Status | Variants | Purpose |"
Add-Line "| --- | --- | --- | --- | --- | --- | --- |"
foreach ($Component in $CmsComponents) {
    Add-Line "| ``$($Component.id)`` | $($Component.name) | $($Component.category) | ``$($Component.cms_interface)`` | ``$($Component.status)`` | $(@($Component.variants) -join ', ') | $($Component.purpose) |"
}
Add-Line
foreach ($Component in $CmsComponents) {
    Add-ComponentSection $Component
}

Add-Line "## Internal Components"
Add-Line
Add-Line "These implementations exist and are maintained, but ``cms_usable`` is false. They are not valid ``page.blocks[].type`` values."
Add-Line
foreach ($Component in $InternalComponents) {
    Add-ComponentSection $Component
}

Add-Line "## Legacy"
Add-Line
Add-Line "No published Registry ID is currently deprecated. The former topic-wide Attraction, City, and Survival article patterns and the Save Guide / Saved Guides browser-state UI were removed before Registry 1.0 and are not available compatibility IDs. Historical Gutenberg content still receives WordPress safe fallback rendering."
Add-Line
Add-Line "## Not Yet Componentized"
Add-Line
Add-Line "The following current UI remains frontend-owned page composition rather than CMS-callable components:"
Add-Line
Add-Line "- Site Header, primary navigation, mobile navigation, and Footer."
Add-Line "- Homepage Hero, Survival Kit shortcut strip, City/Attraction grids, Planner band, Ticket band, and homepage FAQ composition."
Add-Line "- Core landing-page Hero copy and per-page section composition in ``page.php``."
Add-Line "- Category/archive/search query composition around the internal Guide Card."
Add-Line
Add-Line "These must not be emitted as CMS component types until they are deliberately implemented, added to the Registry, shown in the Gallery, and tested."
Add-Line
Add-Line "## Proposed, Not Available"
Add-Line
Add-Line "Possible future content needs, intentionally not implemented or registered in this release:"
Add-Line
Add-Line "- ``source_citations`` for structured public references and last-checked dates."
Add-Line "- ``related_guides`` for CMS-curated internal reading paths."
Add-Line "- ``transport_option`` for repeated route comparisons that need more structure than a table."
Add-Line "- ``affiliate_product_card`` for a future structured single-product offer."
Add-Line "- ``affiliate_comparison_card`` for a future CMS-authored comparison of commercial options."
Add-Line "- ``affiliate_disclosure`` for a future standalone disclosure when composition requires one."
Add-Line
Add-Line "These names are proposals, not stable IDs, and the CMS must not use them."
Add-Line
Add-Line "## Adding A Component"
Add-Line
Add-Line "1. Frontend designs and implements the component against a real content need."
Add-Line "2. Assign a stable semantic component ID."
Add-Line "3. Define the input JSON Schema, including required and optional fields."
Add-Line "4. Define the finite semantic variants supported by the Design System."
Add-Line "5. Add the implementation and capability record to the Component Registry."
Add-Line "6. Add representative content and every major variant to the Component Gallery."
Add-Line "7. Regenerate this Component Catalog from the Registry."
Add-Line "8. Test rendering, responsive behavior, accessibility, safe fallback, and Contract output."
Add-Line "9. Only then may the CMS begin emitting the component ID."
Add-Line
Add-Line "If an ID must be retired, mark it ``deprecated`` and document the compatibility renderer before changing CMS output. Visual refactors alone never justify changing a stable component ID."

[System.IO.File]::WriteAllText($OutputPath, $Builder.ToString(), [System.Text.UTF8Encoding]::new($false))

$PublishedComponents = @(
    foreach ($Component in $CmsComponents) {
        $Required = @($Component.schema.required)
        $PropertyNames = @($Component.schema.properties.PSObject.Properties.Name)
        $Optional = @($PropertyNames | Where-Object { $Required -notcontains $_ })
        $ExampleData = @{}
        foreach ($ExampleProperty in $Component.example.PSObject.Properties) {
            if (@("type", "variant") -notcontains $ExampleProperty.Name) {
                $ExampleData[$ExampleProperty.Name] = $ExampleProperty.Value
            }
        }

        @{
            id = $Component.id
            name = $Component.name
            category = $Component.category
            purpose = $Component.purpose
            status = $Component.status
            deprecated = ($Component.status -eq "deprecated")
            variants = @($Component.variants)
            cmsUsable = $true
            interface = $Component.cms_interface
            renderMode = $Component.render_mode
            inputSchema = $Component.schema
            requiredFields = $Required
            optionalFields = $Optional
            example = @{
                type = $Component.id
                variant = if ($Component.example.variant) { $Component.example.variant } else { $Component.variants[0] }
                data = $ExampleData
            }
        }
    }
)

$PublishedContract = @{
    contractVersion = $Registry.registry_version
    schemaVersion = "2020-12"
    generatedFrom = "wp-content/themes/solo-to-china/content-contract/component-registry.v1.json"
    generatedBy = "scripts/generate-component-catalog.ps1"
    principles = @{
        frontend = "Frontend defines what can be rendered."
        cms = "CMS decides what should be rendered."
    }
    components = $PublishedComponents
}

$PageBlockComponents = @($CmsComponents | Where-Object { $_.cms_interface -eq "page_block" })
$BlockSchemas = @(
    foreach ($Component in $PageBlockComponents) {
        @{
            title = $Component.name
            description = $Component.purpose
            type = "object"
            additionalProperties = $false
            required = @("type", "variant", "data")
            properties = @{
                type = @{ const = $Component.id }
                variant = @{ type = "string"; enum = @($Component.variants) }
                data = $Component.schema
            }
        }
    }
)

$HeroComponent = $CmsComponents | Where-Object { $_.id -eq "article_hero" } | Select-Object -First 1
$PageSchema = @{
    '$schema' = "https://json-schema.org/draft/2020-12/schema"
    '$id' = "https://solotochina.com/contracts/page-schema.json"
    title = "SoloToChina CMS Page Payload"
    description = "CMS-authored page metadata and ordered content blocks for the SoloToChina frontend."
    contractVersion = $Registry.registry_version
    schemaVersion = "2020-12"
    type = "object"
    additionalProperties = $false
    required = @("metadata", "blocks")
    properties = @{
        metadata = @{
            type = "object"
            additionalProperties = $false
            required = @("pageId", "title", "slug", "contentType")
            properties = @{
                pageId = @{ type = @("string", "integer"); description = "Stable CMS page identifier." }
                title = @{ type = "string"; minLength = 1 }
                slug = @{ type = "string"; minLength = 1; pattern = "^[a-z0-9]+(?:-[a-z0-9]+)*$" }
                contentType = @{ type = "string"; minLength = 1; description = "Taxonomy, not layout. It must never cause the frontend to inject editorial blocks." }
                excerpt = @{ type = "string" }
                canonicalUrl = @{ type = "string"; format = "uri" }
                featuredMediaId = @{ type = @("integer", "null"); minimum = 1 }
                taxonomy = @{ type = "object"; additionalProperties = @{ type = "array"; items = @{ type = "string" } } }
                seo = @{
                    type = "object"
                    additionalProperties = $false
                    properties = @{
                        title = @{ type = "string" }
                        description = @{ type = "string" }
                        canonicalUrl = @{ type = "string"; format = "uri" }
                        robots = @{ type = "string" }
                    }
                }
                presentation = @{
                    type = "object"
                    additionalProperties = $false
                    properties = @{
                        article_hero = @{
                            type = "object"
                            additionalProperties = $false
                            required = @("variant")
                            properties = @{ variant = @{ type = "string"; enum = @($HeroComponent.variants) } }
                        }
                        share_this_page = @{ type = "boolean"; default = $false }
                        table_of_contents = @{ type = "boolean"; default = $false }
                    }
                }
            }
        }
        blocks = @{
            type = "array"
            description = "Array order is the final render order. The frontend must not reorder or inject editorial blocks based on contentType."
            items = @{ oneOf = $BlockSchemas }
        }
    }
}

Write-JsonContract $PublishedContractPath $PublishedContract
Write-JsonContract $PageSchemaPath $PageSchema
Write-JsonContract $ThemePublishedContractPath $PublishedContract
Write-JsonContract $ThemePageSchemaPath $PageSchema
$ContractChecksum = (Get-FileHash -LiteralPath $ThemePublishedContractPath -Algorithm SHA256).Hash.ToLowerInvariant()
$PublishPackageSchema = @{
    '$schema' = "https://json-schema.org/draft/2020-12/schema"
    '$id' = "https://solotochina.com/contracts/cms-publish-package.schema.json"
    title = "SoloToChina CMS WordPress Publish Package"
    description = "Authenticated CMS-to-WordPress draft delivery package. WordPress validates and serializes page.blocks[] in the supplied order."
    publishPackageVersion = "1.0.0"
    contractVersion = $Registry.registry_version
    schemaVersion = "2020-12"
    type = "object"
    additionalProperties = $false
    required = @("contract", "page", "seo", "schema_jsonld", "media", "publication")
    properties = @{
        contract = @{
            type = "object"
            additionalProperties = $false
            required = @("componentContractVersion", "pageSchemaVersion", "contractChecksum")
            properties = @{
                componentContractVersion = @{ const = $Registry.registry_version }
                pageSchemaVersion = @{ const = $Registry.registry_version }
                contractChecksum = @{ type = "string"; const = $ContractChecksum; pattern = "^[a-f0-9]{64}$" }
            }
        }
        page = $PageSchema
        seo = @{
            type = "object"
            additionalProperties = $false
            required = @("meta_title", "meta_description")
            properties = @{
                meta_title = @{ type = "string"; maxLength = 200 }
                meta_description = @{ type = "string"; maxLength = 500 }
                focus_keyword = @{ type = "string"; maxLength = 160 }
                secondary_keywords = @{ type = "array"; maxItems = 30; items = @{ type = "string"; maxLength = 160 } }
                search_intent = @{ type = "string"; maxLength = 160 }
                strategy_version = @{ type = "string"; maxLength = 80 }
            }
        }
        schema_jsonld = @{
            type = "object"
            description = "Structured JSON only. Executable HTML and JavaScript are never accepted."
        }
        media = @{
            type = "array"
            maxItems = 200
            items = @{
                type = "object"
                additionalProperties = $false
                required = @("media_id", "alt", "role", "placement")
                properties = @{
                    media_id = @{ type = "integer"; minimum = 1 }
                    url = @{ type = "string"; format = "uri" }
                    alt = @{ type = "string"; maxLength = 500 }
                    caption = @{ type = "string"; maxLength = 2000 }
                    role = @{ type = "string"; enum = @("featured", "evidence", "context", "illustration", "decorative") }
                    placement = @{ type = "string"; maxLength = 160 }
                }
            }
        }
        publication = @{
            type = "object"
            additionalProperties = $false
            required = @("status")
            properties = @{
                status = @{ const = "draft" }
                existing_post_id = @{ type = @("integer", "null"); minimum = 1 }
                cms_draft_id = @{ type = @("string", "integer"); description = "Optional stable CMS draft identifier used with page.metadata.pageId for idempotent upserts." }
            }
        }
    }
}
Write-JsonContract $PublishPackageSchemaPath $PublishPackageSchema
Write-JsonContract $ThemePublishPackageSchemaPath $PublishPackageSchema
Write-Host "Generated Catalog and CMS contracts from Component Registry $($Registry.registry_version)."
