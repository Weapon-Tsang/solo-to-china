param()

$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent $PSScriptRoot
$RegistryPath = Join-Path $Root "wp-content/themes/solo-to-china/content-contract/component-registry.v1.json"
$OutputPath = Join-Path $Root "docs/COMPONENT_LIBRARY.md"
$Registry = Get-Content -LiteralPath $RegistryPath -Raw | ConvertFrom-Json
$CmsComponents = @($Registry.components | Where-Object { $_.cms_usable -eq $true })
$InternalComponents = @($Registry.components | Where-Object { $_.cms_usable -ne $true })
$Builder = [System.Text.StringBuilder]::new()

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
Write-Host "Generated docs/COMPONENT_LIBRARY.md from Component Registry $($Registry.registry_version)."
