param()

$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent $PSScriptRoot
$Failures = New-Object System.Collections.Generic.List[string]

function Assert-Architecture([bool]$Condition, [string]$Message) {
    if (-not $Condition) {
        $Failures.Add($Message)
    }
}

function Read-ProjectFile([string]$Path) {
    return Get-Content -LiteralPath (Join-Path $Root $Path) -Raw
}

$Single = Read-ProjectFile "wp-content/themes/solo-to-china/single.php"
$Functions = Read-ProjectFile "wp-content/themes/solo-to-china/functions.php"
$ContractRuntime = Read-ProjectFile "wp-content/themes/solo-to-china/inc/content-contract.php"
$ThemeJs = Read-ProjectFile "wp-content/themes/solo-to-china/assets/js/main.js"
$PageTemplate = Read-ProjectFile "wp-content/themes/solo-to-china/page.php"
$ParentCss = Read-ProjectFile "wp-content/themes/solo-to-china/assets/css/main.css"
$ChildCss = Read-ProjectFile "wp-content/themes/solo-to-china-child/assets/css/article.css"
$Fixtures = Read-ProjectFile "scripts/playground-fixtures.php"
$Contract = (Read-ProjectFile "wp-content/themes/solo-to-china/content-contract/content-contract.v2.json") | ConvertFrom-Json

foreach ($Forbidden in @("stc_render_article_save_button", "data-stc-save-guide", "data-stc-saved-guides", "stcSavedGuides", "Save guide", "Saved Guides")) {
    Assert-Architecture (-not ($Single + $Functions + $ThemeJs + $PageTemplate).Contains($Forbidden)) "Removed guide-saving behavior is still present: $Forbidden"
}
Assert-Architecture (-not $ThemeJs.Contains("window.localStorage")) "Parent Theme JavaScript must not retain guide localStorage behavior."

foreach ($FixedTemplateToken in @("stc_register_block_patterns", "solo-to-china/attraction-guide-v1", "solo-to-china/city-guide-v1", "solo-to-china/survival-kit-v1", "Planning checklist", "City planning checklist", "Setup checklist")) {
    Assert-Architecture (-not ($Functions + $Single).Contains($FixedTemplateToken)) "Fixed editorial template logic is still present: $FixedTemplateToken"
}

foreach ($SingleToken in @("stc-article-hero", "stc-article-layout", "stc-entry-content--guide", "stc_page_presentation_enabled( 'share'", "stc_page_presentation_enabled( 'toc'", "the_content()")) {
    Assert-Architecture ($Single.Contains($SingleToken)) "Generic article shell is missing: $SingleToken"
}
foreach ($BranchToken in @("is_attraction_guide", "is_city_guide", "is_survival_kit", "elseif (")) {
    Assert-Architecture (-not $Single.Contains($BranchToken)) "single.php still branches editorial layout by content type: $BranchToken"
}

foreach ($MetaToken in @("_stc_show_share", "_stc_show_toc", "_stc_hero_variant", "register_post_meta", "show_in_rest", "stc_page_presentation_enabled")) {
    Assert-Architecture ($ContractRuntime.Contains($MetaToken)) "CMS presentation metadata support is missing: $MetaToken"
}

foreach ($ShareToken in @("stc_render_share_this_page", "data-stc-share", "data-stc-share-trigger", "data-stc-share-panel", 'role="dialog"', "aria-labelledby", "canonical", "aria-live")) {
    Assert-Architecture ($Functions.Contains($ShareToken)) "ShareThisPage renderer is missing: $ShareToken"
}
foreach ($ShareJsToken in @("navigator.share", "navigator.clipboard", "data-stc-share-trigger", "data-stc-share-copy", "data-stc-share-close", "AbortError", "aria-expanded", "Escape", "Link copied")) {
    Assert-Architecture ($ThemeJs.Contains($ShareJsToken)) "ShareThisPage interaction is missing: $ShareJsToken"
}
Assert-Architecture ($ThemeJs.Contains("document.addEventListener('keydown'")) "ShareThisPage Escape handling must work after a control becomes disabled and loses focus."
foreach ($ShareStyleToken in @(".stc-share", ".stc-share__trigger", ".stc-share__panel", ".stc-share__copy", ".stc-share__status", "@media (max-width: 599px)", "prefers-reduced-motion")) {
    Assert-Architecture (($ParentCss + $ChildCss).Contains($ShareStyleToken)) "ShareThisPage presentation is missing: $ShareStyleToken"
}

Assert-Architecture ($Contract.contract_version -eq "2.0.0") "Content Contract must be version 2.0.0 after the layout-boundary change."
Assert-Architecture ($Contract.presentation.post_meta.show_share -eq "_stc_show_share") "Contract does not expose explicit Share metadata."
Assert-Architecture ($Contract.presentation.post_meta.show_toc -eq "_stc_show_toc") "Contract does not expose explicit TOC metadata."
Assert-Architecture ($Contract.principles.frontend -eq "Render what CMS requests.") "Frontend responsibility principle is missing."
Assert-Architecture ($Contract.principles.cms -eq "Decide what the page contains.") "CMS responsibility principle is missing."
Assert-Architecture ($Contract.principles.content_type -eq "Content type is taxonomy, not layout.") "Content type boundary principle is missing."

foreach ($FixtureToken in @("'show_share'", "'show_toc'", "'hero_variant'")) {
    Assert-Architecture ($Fixtures.Contains($FixtureToken)) "Fixture presentation metadata is missing: $FixtureToken"
}

if ($Failures.Count -gt 0) {
    throw ($Failures -join [Environment]::NewLine)
}

Write-Host "SoloToChina page architecture verification passed."
