$ErrorActionPreference = "Stop"

$Root = Split-Path -Parent $PSScriptRoot

$RequiredFiles = @(
    "docs/handoff/current-progress.md",
    "docs/handoff/new-chat-handoff.md",
    "docs/deployment/wordpress-install.md",
    "docs/architecture/content-component-system.md",
    "docs/COMPONENT_LIBRARY.md",
    "scripts/package-release.ps1",
    "scripts/generate-component-catalog.ps1",
    "scripts/verify-component-registry.ps1",
    "scripts/verify-content-contract.ps1",
    "scripts/verify-content-runtime.ps1",
    "scripts/verify-page-architecture.ps1",
    "scripts/playground-blueprint.json",
    "scripts/playground-editor-blueprint.json",
    "scripts/playground-parent-blueprint.json",
    "scripts/playground-fixtures.php",
    "scripts/start-preview.ps1",
    "wp-content/themes/solo-to-china/style.css",
    "wp-content/themes/solo-to-china/README.md",
    "wp-content/themes/solo-to-china/functions.php",
    "wp-content/themes/solo-to-china/inc/content-contract.php",
    "wp-content/themes/solo-to-china/inc/component-registry.php",
    "wp-content/themes/solo-to-china/inc/content-components.php",
    "wp-content/themes/solo-to-china/inc/content-renderers.php",
    "wp-content/themes/solo-to-china/content-contract/content-contract.v2.json",
    "wp-content/themes/solo-to-china/content-contract/component-registry.v1.json",
    "wp-content/themes/solo-to-china/header.php",
    "wp-content/themes/solo-to-china/footer.php",
    "wp-content/themes/solo-to-china/index.php",
    "wp-content/themes/solo-to-china/archive.php",
    "wp-content/themes/solo-to-china/single.php",
    "wp-content/themes/solo-to-china/404.php",
    "wp-content/themes/solo-to-china/search.php",
    "wp-content/themes/solo-to-china/searchform.php",
    "wp-content/themes/solo-to-china/page.php",
    "wp-content/themes/solo-to-china/page-design-system.php",
    "wp-content/themes/solo-to-china/front-page.php",
    "wp-content/themes/solo-to-china/screenshot.png",
    "wp-content/themes/solo-to-china/assets/css/main.css",
    "wp-content/themes/solo-to-china/assets/css/editor-style.css",
    "wp-content/themes/solo-to-china/assets/js/main.js",
    "wp-content/themes/solo-to-china/assets/images/hero-home.png",
    "wp-content/themes/solo-to-china/assets/images/guide-card-bg.png",
    "wp-content/themes/solo-to-china/assets/images/card-beijing.png",
    "wp-content/themes/solo-to-china/assets/images/card-shanghai.png",
    "wp-content/themes/solo-to-china/assets/images/card-guangzhou.png",
    "wp-content/themes/solo-to-china/assets/images/card-chengdu.png",
    "wp-content/themes/solo-to-china/assets/images/card-chongqing.png",
    "wp-content/themes/solo-to-china/assets/images/card-xian.png",
    "wp-content/themes/solo-to-china/assets/images/card-hangzhou.png",
    "wp-content/themes/solo-to-china/assets/images/card-zhangjiajie-city.png",
    "wp-content/themes/solo-to-china/assets/images/card-forbidden-city.png",
    "wp-content/themes/solo-to-china/assets/images/card-great-wall.png",
    "wp-content/themes/solo-to-china/assets/images/card-terracotta.png",
    "wp-content/themes/solo-to-china/assets/images/card-zhangjiajie-attraction.png",
    "wp-content/themes/solo-to-china/assets/images/card-west-lake.png",
    "wp-content/themes/solo-to-china/assets/images/card-disney.png",
    "wp-content/themes/solo-to-china/assets/images/planner-art.png",
    "wp-content/themes/solo-to-china/assets/images/ticket-art.png",
    "wp-content/themes/solo-to-china/assets/images/card-beijing-hd.webp",
    "wp-content/themes/solo-to-china/assets/images/card-shanghai-hd.webp",
    "wp-content/themes/solo-to-china/assets/images/card-guangzhou-hd.webp",
    "wp-content/themes/solo-to-china/assets/images/card-chengdu-hd.webp",
    "wp-content/themes/solo-to-china/assets/images/card-chongqing-hd.webp",
    "wp-content/themes/solo-to-china/assets/images/card-xian-hd.webp",
    "wp-content/themes/solo-to-china/assets/images/card-hangzhou-hd.webp",
    "wp-content/themes/solo-to-china/assets/images/card-zhangjiajie-city-hd.webp",
    "wp-content/themes/solo-to-china/assets/images/card-forbidden-city-hd.webp",
    "wp-content/themes/solo-to-china/assets/images/card-great-wall-hd.webp",
    "wp-content/themes/solo-to-china/assets/images/card-terracotta-hd.webp",
    "wp-content/themes/solo-to-china/assets/images/card-zhangjiajie-attraction-hd.webp",
    "wp-content/themes/solo-to-china/assets/images/card-west-lake-hd.webp",
    "wp-content/themes/solo-to-china/assets/images/card-disney-hd.webp",
    "wp-content/themes/solo-to-china-child/style.css",
    "wp-content/themes/solo-to-china-child/functions.php",
    "wp-content/themes/solo-to-china-child/header.php",
    "wp-content/themes/solo-to-china-child/README.md",
    "wp-content/themes/solo-to-china-child/assets/css/design-system.css",
    "wp-content/themes/solo-to-china-child/assets/css/site.css",
    "wp-content/themes/solo-to-china-child/assets/css/home.css",
    "wp-content/themes/solo-to-china-child/assets/css/article.css",
    "wp-content/themes/solo-to-china-child/assets/css/content-components.css",
    "wp-content/themes/solo-to-china-child/assets/css/component-gallery.css",
    "wp-content/themes/solo-to-china-child/assets/css/editor-style.css",
    "wp-content/themes/solo-to-china-child/assets/js/site.js",
    "wp-content/plugins/solo-to-china-tools/solo-to-china-tools.php",
    "wp-content/plugins/solo-to-china-tools/README.md",
    "wp-content/plugins/solo-to-china-tools/includes/attractions.php",
    "wp-content/plugins/solo-to-china-tools/includes/shortcodes.php",
    "wp-content/plugins/solo-to-china-tools/assets/css/tools.css",
    "wp-content/plugins/solo-to-china-tools/assets/js/tools.js"
)

$RequiredNavLabels = @(
    "Home",
    "Survival Kit",
    "City Guides",
    "Attraction Guides",
    "Planner",
    "Tools",
    "FAQ"
)

$BannedNavLabels = @(
    "Hotels",
    "Tickets",
    "Flights",
    "Trains",
    "Book"
)

$Failures = New-Object System.Collections.Generic.List[string]

foreach ($RelativePath in $RequiredFiles) {
    $AbsolutePath = Join-Path $Root $RelativePath
    if (-not (Test-Path -LiteralPath $AbsolutePath -PathType Leaf)) {
        $Failures.Add("Missing required file: $RelativePath")
    }
}

$GitignorePath = Join-Path $Root ".gitignore"
if (Test-Path -LiteralPath $GitignorePath -PathType Leaf) {
    $Gitignore = Get-Content -LiteralPath $GitignorePath -Raw
    if (-not $Gitignore.Contains("dist/")) {
        $Failures.Add(".gitignore does not ignore generated release artifacts.")
    }
    if (-not $Gitignore.Contains("*.zip")) {
        $Failures.Add(".gitignore does not ignore generated zip archives.")
    }
}

$PackageScriptPath = Join-Path $Root "scripts/package-release.ps1"
if (Test-Path -LiteralPath $PackageScriptPath -PathType Leaf) {
    $PackageScript = Get-Content -LiteralPath $PackageScriptPath -Raw
    if (-not $PackageScript.Contains("release-manifest.txt")) {
        $Failures.Add("Package script does not create a release manifest.")
    }
    if (-not $PackageScript.Contains("Get-FileHash")) {
        $Failures.Add("Package script does not record zip checksums.")
    }
    foreach ($ChildPackageToken in @("solo-to-china-child", "solo-to-china-child-theme.zip", "Child Theme SHA256")) {
        if (-not $PackageScript.Contains($ChildPackageToken)) {
            $Failures.Add("Package script does not include the Child Theme artifact token: $ChildPackageToken")
        }
    }
    if (-not $PackageScript.Contains("Theme version: 0.25.0") -or (-not $PackageScript.Contains("Child Theme version: 0.7.0")) -or (-not $PackageScript.Contains("Plugin version: 0.22.0"))) {
        $Failures.Add("Package script does not write artifact versions to the release manifest.")
    }
}

$InstallDocPath = Join-Path $Root "docs/deployment/wordpress-install.md"
if (Test-Path -LiteralPath $InstallDocPath -PathType Leaf) {
    $InstallDoc = Get-Content -LiteralPath $InstallDocPath -Raw
    if (-not $InstallDoc.Contains("Post-Install Check")) {
        $Failures.Add("WordPress install handoff is missing the post-install check list.")
    }
    if (-not $InstallDoc.Contains("Do not extract either zip directly inside")) {
        $Failures.Add("WordPress install handoff is missing aaPanel extraction warning.")
    }
    foreach ($ChildInstallToken in @("Install SoloToChina Parent Theme first", "Activate SoloToChina Child")) {
        if (-not $InstallDoc.Contains($ChildInstallToken)) {
            $Failures.Add("WordPress install handoff is missing Child Theme install order: $ChildInstallToken")
        }
    }
}

$PreviewScriptPath = Join-Path $Root "scripts/start-preview.ps1"
$PreviewBlueprintPath = Join-Path $Root "scripts/playground-blueprint.json"
if (Test-Path -LiteralPath $PreviewScriptPath -PathType Leaf) {
    $PreviewScript = Get-Content -LiteralPath $PreviewScriptPath -Raw
    foreach ($PreviewToken in @('@wp-playground/cli@latest', 'solo-to-china-child', 'solo-to-china-tools', 'solo-to-china-scripts', '--mount-dir', 'playground-blueprint.json', 'playground-editor-blueprint.json', 'playground-parent-blueprint.json', 'ParentOnly', 'Editor')) {
        if (-not $PreviewScript.Contains($PreviewToken)) {
            $Failures.Add("WordPress Playground preview script is missing: $PreviewToken")
        }
    }
}

$EditorPreviewBlueprintPath = Join-Path $Root "scripts/playground-editor-blueprint.json"
if (Test-Path -LiteralPath $EditorPreviewBlueprintPath -PathType Leaf) {
    $EditorPreviewBlueprint = Get-Content -LiteralPath $EditorPreviewBlueprintPath -Raw
    foreach ($EditorBlueprintToken in @('"login": true', 'activateTheme', 'solo-to-china-child', 'activatePlugin', 'playground-fixtures.php', '/wp-admin/edit.php')) {
        if (-not $EditorPreviewBlueprint.Contains($EditorBlueprintToken)) {
            $Failures.Add("Child editor Playground blueprint is missing: $EditorBlueprintToken")
        }
    }
}

$ParentPreviewBlueprintPath = Join-Path $Root "scripts/playground-parent-blueprint.json"
if (Test-Path -LiteralPath $ParentPreviewBlueprintPath -PathType Leaf) {
    $ParentPreviewBlueprint = Get-Content -LiteralPath $ParentPreviewBlueprintPath -Raw
    foreach ($ParentBlueprintToken in @('activateTheme', '"themeFolderName": "solo-to-china"', 'activatePlugin', 'playground-fixtures.php', '/%postname%/')) {
        if (-not $ParentPreviewBlueprint.Contains($ParentBlueprintToken)) {
            $Failures.Add("Parent fallback Playground blueprint is missing: $ParentBlueprintToken")
        }
    }
}

$RuntimeVerificationPath = Join-Path $Root "scripts/verify-content-runtime.ps1"
if (Test-Path -LiteralPath $RuntimeVerificationPath -PathType Leaf) {
    $RuntimeVerification = Get-Content -LiteralPath $RuntimeVerificationPath -Raw
    foreach ($RuntimeVerificationToken in @('stc/v1/content-contract', 'stc/v1/component-registry', 'contract_version', 'registry_version', 'guide_types', 'schema.required', 'design-system', '_stc_guide_type', 'china-mobile-payment-setup', 'beijing-first-time-city-guide', 'forbidden-city-first-time-visitor-guide', 'stc-content-block--quick-answer', 'stc-content-block--warning', 'stc-content-block--faq', 'stc-dynamic-component--planner', 'stc-dynamic-component--ticket', 'stc-dynamic-component--affiliate', 'sponsored nofollow noopener', 'srcset=', 'loading="lazy"', 'claim_keys', 'ParentOnly')) {
        if (-not $RuntimeVerification.Contains($RuntimeVerificationToken)) {
            $Failures.Add("Content runtime verification is missing: $RuntimeVerificationToken")
        }
    }
}
if (Test-Path -LiteralPath $PreviewBlueprintPath -PathType Leaf) {
    $PreviewBlueprint = Get-Content -LiteralPath $PreviewBlueprintPath -Raw
    foreach ($BlueprintToken in @('activateTheme', 'solo-to-china-child', 'activatePlugin', 'solo-to-china-tools/solo-to-china-tools.php', 'runPHP', 'playground-fixtures.php', '/%postname%/')) {
        if (-not $PreviewBlueprint.Contains($BlueprintToken)) {
            $Failures.Add("WordPress Playground blueprint is missing: $BlueprintToken")
        }
    }
}

$PreviewFixturesPath = Join-Path $Root "scripts/playground-fixtures.php"
if (Test-Path -LiteralPath $PreviewFixturesPath -PathType Leaf) {
    $PreviewFixtures = Get-Content -LiteralPath $PreviewFixturesPath -Raw
    foreach ($FixtureToken in @('forbidden-city-first-time-visitor-guide', 'beijing-first-time-city-guide', 'china-mobile-payment-setup', '_stc_guide_type', '_stc_content_contract_version', 'stc-content-block--quick-answer', 'stc-content-block--quick-facts', 'stc-content-block--warning', 'stc-content-block--steps', 'stc-content-block--checklist', 'stc-content-block--comparison', 'stc-content-block--faq', '[stc_planner_cta', '[stc_ticket_reminder')) {
        if (-not $PreviewFixtures.Contains($FixtureToken)) {
            $Failures.Add("WordPress Playground content fixtures are missing: $FixtureToken")
        }
    }
    foreach ($ForbiddenFixtureToken in @('<!-- wp:html', '<style', ' style=')) {
        if ($PreviewFixtures.IndexOf($ForbiddenFixtureToken, [System.StringComparison]::OrdinalIgnoreCase) -ge 0) {
            $Failures.Add("WordPress Playground content fixtures include forbidden markup: $ForbiddenFixtureToken")
        }
    }
}

$NewChatHandoffPath = Join-Path $Root "docs/handoff/new-chat-handoff.md"
if (Test-Path -LiteralPath $NewChatHandoffPath -PathType Leaf) {
    $NewChatHandoff = Get-Content -LiteralPath $NewChatHandoffPath -Raw
    foreach ($RequiredHandoffText in @("Suggested New Chat Opening Message", "Fixed Information Architecture", "Do Not Start Without Explicit Approval", "Development Style For Next Chat")) {
        if (-not $NewChatHandoff.Contains($RequiredHandoffText)) {
            $Failures.Add("New chat handoff is missing section: $RequiredHandoffText")
        }
    }
}

$ThemeStylePath = Join-Path $Root "wp-content/themes/solo-to-china/style.css"
if (Test-Path -LiteralPath $ThemeStylePath -PathType Leaf) {
    $ThemeStyle = Get-Content -LiteralPath $ThemeStylePath -Raw
    if (-not $ThemeStyle.Contains("Version: 0.25.0")) {
		$Failures.Add("Theme stylesheet header version is not 0.25.0.")
    }
    if (-not $ThemeStyle.Contains("Requires at least: 6.5")) {
        $Failures.Add("Theme stylesheet header is missing the minimum WordPress version.")
    }
    if (-not $ThemeStyle.Contains("Requires PHP: 7.4")) {
        $Failures.Add("Theme stylesheet header is missing the minimum PHP version.")
    }
}

$ThemeReadmePath = Join-Path $Root "wp-content/themes/solo-to-china/README.md"
if (Test-Path -LiteralPath $ThemeReadmePath -PathType Leaf) {
    $ThemeReadme = Get-Content -LiteralPath $ThemeReadmePath -Raw
    if ((-not $ThemeReadme.Contains("Current version")) -or (-not $ThemeReadme.Contains("0.25.0"))) {
        $Failures.Add("Theme README does not document the current theme version.")
    }
    if (-not $ThemeReadme.Contains("The theme should not own tool business logic")) {
        $Failures.Add("Theme README does not preserve the theme/plugin responsibility boundary.")
    }
}

$HeaderPath = Join-Path $Root "wp-content/themes/solo-to-china/header.php"
$FunctionsPath = Join-Path $Root "wp-content/themes/solo-to-china/functions.php"
if ((Test-Path -LiteralPath $HeaderPath -PathType Leaf) -and (Test-Path -LiteralPath $FunctionsPath -PathType Leaf)) {
    $Header = Get-Content -LiteralPath $HeaderPath -Raw
    $Functions = Get-Content -LiteralPath $FunctionsPath -Raw
    $NavigationSource = $Header + "`n" + $Functions

    foreach ($Label in $RequiredNavLabels) {
        if (-not $NavigationSource.Contains($Label)) {
            $Failures.Add("Header is missing navigation label: $Label")
        }
    }

    foreach ($Label in $BannedNavLabels) {
        if ($NavigationSource -match (">" + [regex]::Escape($Label) + "<")) {
            $Failures.Add("Header includes banned top-level navigation label: $Label")
        }
    }

    if (-not $Header.Contains("stc-menu-toggle")) {
        $Failures.Add("Header is missing the mobile navigation toggle button.")
    }
    if (-not $Header.Contains("aria-expanded=""false""")) {
        $Failures.Add("Mobile navigation toggle is missing the default collapsed ARIA state.")
    }
    if (-not $Header.Contains("Skip to content")) {
        $Failures.Add("Header is missing a skip-to-content link.")
    }
    if (-not $Functions.Contains("stc_ensure_core_pages")) {
        $Failures.Add("Theme setup does not create missing core IA pages on activation.")
    }
    if (-not $Functions.Contains("stc_ensure_core_categories")) {
        $Failures.Add("Theme setup does not create missing core guide categories on activation.")
    }
    if (-not $Functions.Contains("automatic-feed-links")) {
        $Failures.Add("Theme setup is missing automatic feed links support.")
    }
    if (-not $Functions.Contains("align-wide")) {
        $Failures.Add("Theme setup is missing wide alignment support.")
    }
    if (-not $Functions.Contains("STC_THEME_VERSION")) {
        $Failures.Add("Theme functions are missing a single theme version constant.")
    }
    if (-not $Functions.Contains("add_image_size( 'stc-guide-card-2x', 960")) {
        $Failures.Add("Theme setup is missing the 960px Retina guide card image size.")
    }
    if (-not $Functions.Contains("wp_get_attachment_image") -or (-not $Functions.Contains("'stc-guide-card-2x'"))) {
        $Failures.Add("WordPress guide cards do not request the responsive Retina image size.")
    }
    if (-not $Functions.Contains("stc_render_guide_card_media")) {
        $Failures.Add("Theme functions are missing the shared high-resolution guide card media renderer.")
    }
    if (-not $Functions.Contains("'0.25.0'")) {
		$Failures.Add("Theme asset version is not 0.25.0.")
    }
    foreach ($ContentEditorToken in @("editor-styles", "add_editor_style", "assets/css/editor-style.css", "stc_add_stable_content_heading_ids", "sanitize_title", "preg_replace_callback")) {
        if (-not $Functions.Contains($ContentEditorToken)) {
            $Failures.Add("Theme is missing stable content anchors or editor style support: $ContentEditorToken")
        }
    }
    foreach ($ShareRendererToken in @("stc_render_share_this_page", "data-stc-share", "data-stc-share-trigger", "data-stc-share-panel")) {
        if (-not $Functions.Contains($ShareRendererToken)) {
            $Failures.Add("Theme functions are missing the reusable ShareThisPage renderer: $ShareRendererToken")
        }
    }
    if (-not $Functions.Contains("stc_is_attraction_guide_post")) {
        $Failures.Add("Theme functions are missing the Attraction Guide post detector.")
    }
    if (-not $Functions.Contains("stc_is_city_guide_post")) {
        $Failures.Add("Theme functions are missing the City Guide post detector.")
    }
    if (-not $Functions.Contains("stc_is_survival_kit_post")) {
        $Failures.Add("Theme functions are missing the Survival Kit post detector.")
    }
    if (-not $Functions.Contains("stc_get_guide_type_label")) {
        $Failures.Add("Theme functions are missing the guide type label helper.")
    }
    if (-not $Functions.Contains("stc_render_guide_card")) {
        $Failures.Add("Theme functions are missing the shared Guide card renderer.")
    }
    if (-not $Functions.Contains("stc_render_guide_toc")) {
        $Failures.Add("Theme functions are missing the shared Guide table of contents renderer.")
    }
    if (-not $Functions.Contains("stc_render_core_page_latest_guides")) {
        $Failures.Add("Theme functions are missing the core page latest guides renderer.")
    }
    if ($Functions.Contains("stc_render_home_latest_guides")) {
        $Failures.Add("Theme functions still include the removed homepage latest guides renderer.")
    }
}

$ChildThemeStylePath = Join-Path $Root "wp-content/themes/solo-to-china-child/style.css"
$ChildThemeFunctionsPath = Join-Path $Root "wp-content/themes/solo-to-china-child/functions.php"
$ChildThemeDesignSystemPath = Join-Path $Root "wp-content/themes/solo-to-china-child/assets/css/design-system.css"
if (Test-Path -LiteralPath $ChildThemeStylePath -PathType Leaf) {
    $ChildThemeStyle = Get-Content -LiteralPath $ChildThemeStylePath -Raw
    foreach ($ChildHeaderToken in @("Theme Name: SoloToChina Child", "Template: solo-to-china", "Version: 0.7.0", "Text Domain: solo-to-china-child")) {
        if (-not $ChildThemeStyle.Contains($ChildHeaderToken)) {
            $Failures.Add("Child Theme stylesheet header is missing: $ChildHeaderToken")
        }
    }
}

if (Test-Path -LiteralPath $ChildThemeFunctionsPath -PathType Leaf) {
    $ChildThemeFunctions = Get-Content -LiteralPath $ChildThemeFunctionsPath -Raw
    foreach ($ChildFunctionToken in @("STC_CHILD_VERSION", "wp_enqueue_scripts", "stc-main", "get_stylesheet_uri", "get_stylesheet_directory_uri", "stc-child-design-system")) {
        if (-not $ChildThemeFunctions.Contains($ChildFunctionToken)) {
            $Failures.Add("Child Theme resource loading is missing: $ChildFunctionToken")
        }
    }
    foreach ($ChildStageToken in @("stc-child-site", "stc-child-home", "is_front_page", "stc-child-article", "stc-child-content-components", "is_single", "stc-child-interactions", "stc_child_render_primary_navigation", "stc_child_prepend_guide_breadcrumbs")) {
        if (-not $ChildThemeFunctions.Contains($ChildStageToken)) {
            $Failures.Add("Child Theme shared/home asset stage is missing: $ChildStageToken")
        }
    }
    foreach ($ChildEditorToken in @("stc_child_add_editor_styles", "add_editor_style", "assets/css/design-system.css", "assets/css/content-components.css", "assets/css/editor-style.css")) {
        if (-not $ChildThemeFunctions.Contains($ChildEditorToken)) {
            $Failures.Add("Child Theme editor visual parity is missing: $ChildEditorToken")
        }
    }
    if ($ChildThemeFunctions.Contains("str_contains(")) {
        $Failures.Add("Child Theme functions use str_contains(), which is incompatible with the declared PHP 7.4 minimum.")
    }
}

if (Test-Path -LiteralPath $ChildThemeDesignSystemPath -PathType Leaf) {
    $ChildThemeDesignSystem = Get-Content -LiteralPath $ChildThemeDesignSystemPath -Raw
    foreach ($DesignSystemToken in @("--stc-color-ink", "--stc-font-sans", "--stc-space-", "--stc-container-max", "--stc-grid-gap", "--stc-radius-", "--stc-shadow-", "--stc-control-height", "--stc-image-ratio", "--stc-motion-duration", ":focus-visible", "prefers-reduced-motion")) {
        if (-not $ChildThemeDesignSystem.Contains($DesignSystemToken)) {
            $Failures.Add("Child Theme design system is missing: $DesignSystemToken")
        }
    }
}

$ChildThemeHeaderPath = Join-Path $Root "wp-content/themes/solo-to-china-child/header.php"
if (Test-Path -LiteralPath $ChildThemeHeaderPath -PathType Leaf) {
    $ChildThemeHeader = Get-Content -LiteralPath $ChildThemeHeaderPath -Raw
    foreach ($ChildHeaderMarkupToken in @('Skip to content', 'stc_child_render_primary_navigation', 'aria-expanded="false"', 'data-open-label', 'data-close-label')) {
        if (-not $ChildThemeHeader.Contains($ChildHeaderMarkupToken)) {
            $Failures.Add("Child Theme Header override is missing: $ChildHeaderMarkupToken")
        }
    }
}

$ChildThemeSiteCssPath = Join-Path $Root "wp-content/themes/solo-to-china-child/assets/css/site.css"
if (Test-Path -LiteralPath $ChildThemeSiteCssPath -PathType Leaf) {
    $ChildThemeSiteCss = Get-Content -LiteralPath $ChildThemeSiteCssPath -Raw
    foreach ($ChildSiteCssToken in @('.stc-header', '.stc-nav__link[aria-current="page"]', '.stc-menu-toggle__line', '.stc-image-card', '.stc-footer', '.stc-footer__socials', '@media (max-width: 840px)')) {
        if (-not $ChildThemeSiteCss.Contains($ChildSiteCssToken)) {
            $Failures.Add("Child Theme shared site CSS is missing: $ChildSiteCssToken")
        }
    }
}

$ChildThemeHomeCssPath = Join-Path $Root "wp-content/themes/solo-to-china-child/assets/css/home.css"
if (Test-Path -LiteralPath $ChildThemeHomeCssPath -PathType Leaf) {
    $ChildThemeHomeCss = Get-Content -LiteralPath $ChildThemeHomeCssPath -Raw
    foreach ($ChildHomeCssToken in @(".home .stc-hero", ".home .stc-survival", ".home .stc-card-grid--cities", ".home .stc-card-grid--attractions", ".home .stc-planner", ".home .stc-ticket-band", ".home .stc-faq", "@media (max-width: 599px)")) {
        if (-not $ChildThemeHomeCss.Contains($ChildHomeCssToken)) {
            $Failures.Add("Child Theme homepage CSS is missing: $ChildHomeCssToken")
        }
    }
}

$ChildThemeArticleCssPath = Join-Path $Root "wp-content/themes/solo-to-china-child/assets/css/article.css"
if (Test-Path -LiteralPath $ChildThemeArticleCssPath -PathType Leaf) {
    $ChildThemeArticleCss = Get-Content -LiteralPath $ChildThemeArticleCssPath -Raw
    foreach ($ChildArticleCssToken in @(".single .stc-content", ".stc-article-hero", ".stc-article-layout", ".stc-entry-content--guide", ".stc-guide-breadcrumb", ".stc-guide-toc", ".stc-guide-quick-facts", ".stc-guide-warning", ".stc-guide-route", ".stc-share__trigger", ".stc-share__panel", "card-forbidden-city-hd.webp", "card-beijing-hd.webp", "card-hangzhou-hd.webp", "@media (max-width: 840px)", "@media (max-width: 599px)")) {
        if (-not $ChildThemeArticleCss.Contains($ChildArticleCssToken)) {
            $Failures.Add("Child Theme article CSS is missing: $ChildArticleCssToken")
        }
    }
}

$ChildThemeSiteJsPath = Join-Path $Root "wp-content/themes/solo-to-china-child/assets/js/site.js"
if (Test-Path -LiteralPath $ChildThemeSiteJsPath -PathType Leaf) {
    $ChildThemeSiteJs = Get-Content -LiteralPath $ChildThemeSiteJsPath -Raw
    foreach ($ChildSiteJsToken in @("Escape", "aria-expanded", "data-open-label", "data-close-label", "matchMedia")) {
        if (-not $ChildThemeSiteJs.Contains($ChildSiteJsToken)) {
            $Failures.Add("Child Theme navigation enhancement is missing: $ChildSiteJsToken")
        }
    }
}

foreach ($RelativePath in $RequiredFiles | Where-Object { $_.EndsWith("-hd.webp") }) {
    $AbsolutePath = Join-Path $Root $RelativePath
    if ((Test-Path -LiteralPath $AbsolutePath -PathType Leaf) -and (Get-Item -LiteralPath $AbsolutePath).Length -lt 100000) {
        $Failures.Add("Retina guide image is unexpectedly small: $RelativePath")
    }
}

$ThemePhpFiles = Get-ChildItem -LiteralPath (Join-Path $Root "wp-content/themes/solo-to-china") -Filter "*.php" -File
foreach ($ThemePhpFile in $ThemePhpFiles) {
    $ThemePhp = Get-Content -LiteralPath $ThemePhpFile.FullName -Raw
    if ($ThemePhp.Contains("the_permalink();")) {
        $Failures.Add("Theme template uses unescaped the_permalink output: $($ThemePhpFile.Name)")
    }
}

$FooterPath = Join-Path $Root "wp-content/themes/solo-to-china/footer.php"
if (Test-Path -LiteralPath $FooterPath -PathType Leaf) {
    $Footer = Get-Content -LiteralPath $FooterPath -Raw
    foreach ($FooterToken in @("stc-footer__inner", "stc-footer__socials", "stc-footer__bottom", "stc-footer__legal", "Privacy Policy", "Terms of Use", "Guest-first. Practical. Independent.")) {
        if (-not $Footer.Contains($FooterToken)) {
            $Failures.Add("Footer does not preserve the selected homepage-reference footer token: $FooterToken")
        }
    }
}

$PageTemplatePath = Join-Path $Root "wp-content/themes/solo-to-china/page.php"
if (Test-Path -LiteralPath $PageTemplatePath -PathType Leaf) {
    $PageTemplate = Get-Content -LiteralPath $PageTemplatePath -Raw
    foreach ($Slug in @("survival-kit", "city-guides", "attraction-guides", "planner", "tools", "faq")) {
        if (-not $PageTemplate.Contains($Slug)) {
            $Failures.Add("Page template does not handle core IA slug: $Slug")
        }
    }
    if (-not $PageTemplate.Contains("solo_to_china_ticket_tool")) {
        $Failures.Add("Tools page template does not render the guest-first ticket tool shortcode.")
    }
    foreach ($RemovedGuideSaveToken in @("data-stc-save-guide", "data-stc-saved-guides", "data-stc-export-guides", "data-stc-import-guides", "data-stc-clear-guides", "Saved on this device")) {
        if ($PageTemplate.Contains($RemovedGuideSaveToken)) {
            $Failures.Add("Core page template still contains removed guide-saving UI: $RemovedGuideSaveToken")
        }
    }
    if (-not $PageTemplate.Contains("stc_render_share_this_page")) {
        $Failures.Add("Core page template is missing reusable no-account page sharing.")
    }
    if (-not $PageTemplate.Contains("stc_render_faq_chevron") -or (-not $PageTemplate.Contains("stc-faq__answer"))) {
        $Failures.Add("FAQ page template is missing the shared SVG chevron or answer wrapper.")
    }
    if (-not $PageTemplate.Contains("stc_render_core_page_latest_guides")) {
        $Failures.Add("Core guide pages do not render latest published guide posts.")
    }
    if (-not $PageTemplate.Contains("stc-card-grid--cities") -or (-not $PageTemplate.Contains("stc-card-grid--attractions"))) {
        $Failures.Add("City Guides and Attraction Guides landing pages do not use the homepage-reference image card grids.")
    }
    foreach ($GuideLandingToken in @("stc-guide-grid-shell", "data-stc-guide-grid-shell", "data-stc-guide-grid", "data-stc-guide-reveal", "data-stc-guide-label")) {
        if (-not $PageTemplate.Contains($GuideLandingToken)) {
            $Failures.Add("Guide landing pages are missing shared four-card fold markup: $GuideLandingToken")
        }
    }
    foreach ($UtilityPageToken in @("`$guide_landing_slugs", "stc-planner--page", "stc-planner__icon", "stc-planner__art", "stc-faq--page", "stc-faq__answer-link")) {
        if (-not $PageTemplate.Contains($UtilityPageToken)) {
            $Failures.Add("Core page template is missing utility-page presentation token: $UtilityPageToken")
        }
    }
    foreach ($LandingToken in @("stc-page-primary", "stc_render_core_page_latest_guides")) {
        if (-not $PageTemplate.Contains($LandingToken)) {
            $Failures.Add("Core landing page is missing content-first structure token: $LandingToken")
        }
    }
    $PrimaryContentIndex = $PageTemplate.IndexOf("stc-page-primary")
    $LatestGuidesIndex = $PageTemplate.IndexOf("stc_render_core_page_latest_guides")
    if ($PrimaryContentIndex -lt 0 -or $LatestGuidesIndex -le $PrimaryContentIndex) {
        $Failures.Add("Core landing page does not keep primary content before latest posts.")
    }
}

$SingleTemplatePath = Join-Path $Root "wp-content/themes/solo-to-china/single.php"
if (Test-Path -LiteralPath $SingleTemplatePath -PathType Leaf) {
    $SingleTemplate = Get-Content -LiteralPath $SingleTemplatePath -Raw
    foreach ($GenericShellToken in @("stc-article-hero", "stc-article-layout", "stc-entry-content--guide", "stc_get_hero_variant", "stc_page_presentation_enabled( 'share'", "stc_page_presentation_enabled( 'toc'", "stc_render_share_this_page", "stc_render_guide_toc", "the_content()")) {
        if (-not $SingleTemplate.Contains($GenericShellToken)) {
            $Failures.Add("Generic single article shell is missing: $GenericShellToken")
        }
    }
    foreach ($FixedEditorialToken in @("stc_is_attraction_guide_post", "stc_is_city_guide_post", "stc_is_survival_kit_post", "Planning checklist", "City planning checklist", "Setup checklist", "stc_render_article_save_button")) {
        if ($SingleTemplate.Contains($FixedEditorialToken)) {
            $Failures.Add("Single template still binds editorial layout to content type: $FixedEditorialToken")
        }
    }
}

foreach ($ListingTemplatePath in @(
    "wp-content/themes/solo-to-china/archive.php",
    "wp-content/themes/solo-to-china/search.php",
    "wp-content/themes/solo-to-china/index.php"
)) {
    $AbsoluteListingTemplatePath = Join-Path $Root $ListingTemplatePath
    if (Test-Path -LiteralPath $AbsoluteListingTemplatePath -PathType Leaf) {
        $ListingTemplate = Get-Content -LiteralPath $AbsoluteListingTemplatePath -Raw
        if (-not $ListingTemplate.Contains("stc_render_guide_card")) {
            $Failures.Add("Listing template does not use the shared Guide card renderer: $ListingTemplatePath")
        }
    }
}

$PluginPath = Join-Path $Root "wp-content/plugins/solo-to-china-tools/solo-to-china-tools.php"
if (Test-Path -LiteralPath $PluginPath -PathType Leaf) {
    $Plugin = Get-Content -LiteralPath $PluginPath -Raw
    $ShortcodesPath = Join-Path $Root "wp-content/plugins/solo-to-china-tools/includes/shortcodes.php"
    $AttractionsPath = Join-Path $Root "wp-content/plugins/solo-to-china-tools/includes/attractions.php"
    $Shortcodes = if (Test-Path -LiteralPath $ShortcodesPath -PathType Leaf) { Get-Content -LiteralPath $ShortcodesPath -Raw } else { "" }
    $Attractions = if (Test-Path -LiteralPath $AttractionsPath -PathType Leaf) { Get-Content -LiteralPath $AttractionsPath -Raw } else { "" }
    $PluginSource = $Plugin + "`n" + $Shortcodes + "`n" + $Attractions

    $PluginReadmePath = Join-Path $Root "wp-content/plugins/solo-to-china-tools/README.md"
    if (Test-Path -LiteralPath $PluginReadmePath -PathType Leaf) {
        $PluginReadme = Get-Content -LiteralPath $PluginReadmePath -Raw
        if ((-not $PluginReadme.Contains("Current version")) -or (-not $PluginReadme.Contains("0.22.0"))) {
            $Failures.Add("Tools plugin README does not document the current plugin version.")
        }
        if (-not $PluginReadme.Contains("limited to Attraction Ticket Reservation & Reminder")) {
            $Failures.Add("Tools plugin README does not preserve the first-tool boundary.")
        }
    }

    if (-not $Plugin.Contains("Version: 0.22.0")) {
		$Failures.Add("Tools plugin header version is not 0.22.0.")
    }
    if (-not $Plugin.Contains("STC_TOOLS_VERSION', '0.22.0'")) {
		$Failures.Add("Tools plugin version constant is not 0.22.0.")
    }
    if (-not $Plugin.Contains("Requires at least: 6.5")) {
        $Failures.Add("Tools plugin header is missing the minimum WordPress version.")
    }
    if (-not $Plugin.Contains("Requires PHP: 7.4")) {
        $Failures.Add("Tools plugin header is missing the minimum PHP version.")
    }
    if (-not $Plugin.Contains("has_shortcode")) {
        $Failures.Add("Tools plugin assets are not conditionally loaded by shortcode presence.")
    }
    if (-not $Plugin.Contains("is_page( 'tools' )") -or (-not $Plugin.Contains("is_front_page()"))) {
        $Failures.Add("Tools plugin conditional assets do not cover template-rendered ticket tools.")
    }
    if (-not $Plugin.Contains("solo_to_china_ticket_tool")) {
        $Failures.Add("Tools plugin does not register the solo_to_china_ticket_tool shortcode boundary.")
    }
    foreach ($TicketContextToken in @("stc_ticket_reminder", "attraction_slug", "shortcode_atts", "selected(")) {
        if (-not $PluginSource.Contains($TicketContextToken)) {
            $Failures.Add("Tools plugin is missing contextual Ticket Reminder support: $TicketContextToken")
        }
    }
    if (-not $PluginSource.Contains("data-stc-ticket-tool")) {
        $Failures.Add("Ticket tool markup is missing the frontend behavior hook.")
    }
    if (-not $PluginSource.Contains("booking_lead_days")) {
        $Failures.Add("Attraction ticket data is missing booking lead day metadata.")
    }
    foreach ($AttractionName in @(
        "West Lake",
        "Shanghai Disney Resort",
        "Summer Palace",
        "Chengdu Research Base of Giant Panda Breeding",
        "Temple of Heaven",
        "The Bund",
        "Longmen Grottoes",
        "Mogao Caves",
        "Leshan Giant Buddha",
        "Huangshan Scenic Area",
        "Jiuzhaigou Valley",
        "Li River Cruise"
    )) {
        if (-not $Attractions.Contains($AttractionName)) {
            $Failures.Add("Attraction ticket data is missing planned coverage for: $AttractionName")
        }
    }
    if (-not $PluginSource.Contains("data-stc-save-reminder")) {
        $Failures.Add("Ticket tool markup is missing the guest reminder save action.")
    }
    if (-not $PluginSource.Contains("data-stc-reminder-list")) {
        $Failures.Add("Ticket tool markup is missing the local saved reminders list.")
    }
    if (-not $PluginSource.Contains("data-stc-export-reminders")) {
        $Failures.Add("Ticket tool markup is missing the saved reminders export action.")
    }
    if (-not $PluginSource.Contains("data-stc-clear-reminders")) {
        $Failures.Add("Ticket tool markup is missing the saved reminders clear action.")
    }
    if (-not $PluginSource.Contains("data-stc-import-reminders")) {
        $Failures.Add("Ticket tool markup is missing the saved reminders import action.")
    }
    if (-not $PluginSource.Contains("Saved reminders stay in this browser")) {
        $Failures.Add("Ticket tool markup is missing local-only reminder privacy copy.")
    }
    if (-not $Shortcodes.Contains("name=""stc_visit_date"" required")) {
        $Failures.Add("Ticket tool visit date input is not required.")
    }
    if (-not $Shortcodes.Contains("stc_tools_group_attractions_by_city")) {
        $Failures.Add("Ticket tool shortcode does not group attractions by city.")
    }
    if (-not $Shortcodes.Contains("<optgroup")) {
        $Failures.Add("Ticket tool attraction select does not render city optgroups.")
    }
}

$PluginJsPath = Join-Path $Root "wp-content/plugins/solo-to-china-tools/assets/js/tools.js"
if (Test-Path -LiteralPath $PluginJsPath -PathType Leaf) {
    $PluginJs = Get-Content -LiteralPath $PluginJsPath -Raw
    if (-not $PluginJs.Contains("stcTicketTool")) {
        $Failures.Add("Ticket tool JavaScript is missing the submit handler boundary.")
    }
    if (-not $PluginJs.Contains("localStorage")) {
        $Failures.Add("Ticket reminder JavaScript does not persist reminders locally.")
    }
    if (-not $PluginJs.Contains("stcRenderReminders")) {
        $Failures.Add("Ticket reminder JavaScript is missing the saved reminders renderer.")
    }
    if (-not $PluginJs.Contains("data-stc-delete-reminder")) {
        $Failures.Add("Ticket reminder JavaScript is missing the delete reminder action.")
    }
    if (-not $PluginJs.Contains("data-stc-export-reminders")) {
        $Failures.Add("Ticket reminder JavaScript is missing the reminders export binding.")
    }
    if (-not $PluginJs.Contains("data-stc-clear-reminders")) {
        $Failures.Add("Ticket reminder JavaScript is missing the reminders clear binding.")
    }
    if (-not $PluginJs.Contains("stcExportReminders")) {
        $Failures.Add("Ticket reminder JavaScript is missing the reminders JSON exporter.")
    }
    if (-not $PluginJs.Contains("data-stc-import-reminders")) {
        $Failures.Add("Ticket reminder JavaScript is missing the reminders import binding.")
    }
    if (-not $PluginJs.Contains("stcImportReminders")) {
        $Failures.Add("Ticket reminder JavaScript is missing the reminders JSON importer.")
    }
    if (-not $PluginJs.Contains("stcClampText")) {
        $Failures.Add("Ticket reminder JavaScript does not clamp imported reminder text.")
    }
    if (-not $PluginJs.Contains("stcDateValue")) {
        $Failures.Add("Ticket reminder JavaScript does not validate imported reminder dates.")
    }
    if (-not $PluginJs.Contains("data-stc-download-calendar")) {
        $Failures.Add("Ticket reminder JavaScript is missing the calendar download action.")
    }
    if (-not $PluginJs.Contains("stcDownloadCalendar")) {
        $Failures.Add("Ticket reminder JavaScript is missing the ICS calendar exporter.")
    }
    if (-not $PluginJs.Contains("text/calendar")) {
        $Failures.Add("Ticket reminder JavaScript does not create a calendar file download.")
    }
    if (-not $PluginJs.Contains("stcBookingWindowStatus")) {
        $Failures.Add("Ticket tool JavaScript is missing booking window status logic.")
    }
    foreach ($StatusLabel in @("Book now", "Set reminder", "Date has passed")) {
        if (-not $PluginJs.Contains($StatusLabel)) {
            $Failures.Add("Ticket tool JavaScript is missing booking window label: $StatusLabel")
        }
    }
    if (-not $PluginJs.Contains("plan.bookingStatus === 'passed'")) {
        $Failures.Add("Ticket reminder JavaScript allows saving reminders for past visit dates.")
    }
}

$PluginCssPath = Join-Path $Root "wp-content/plugins/solo-to-china-tools/assets/css/tools.css"
if (Test-Path -LiteralPath $PluginCssPath -PathType Leaf) {
    $PluginCss = Get-Content -LiteralPath $PluginCssPath -Raw
    if (-not $PluginCss.Contains(".stc-reminder-list")) {
        $Failures.Add("Ticket tool CSS is missing saved reminder list styling.")
    }
    if (-not $PluginCss.Contains(".stc-reminder-actions")) {
        $Failures.Add("Ticket tool CSS is missing reminder action button styling.")
    }
    if (-not $PluginCss.Contains(".stc-reminder-list__actions")) {
        $Failures.Add("Ticket tool CSS is missing saved reminder list action styling.")
    }
    if (-not $PluginCss.Contains(".stc-tool-local-note")) {
        $Failures.Add("Ticket tool CSS is missing local-only reminder note styling.")
    }
    if (-not $PluginCss.Contains(".stc-ticket-status")) {
        $Failures.Add("Ticket tool CSS is missing booking window status styling.")
    }
    if (-not $PluginCss.Contains(":focus-visible")) {
        $Failures.Add("Ticket tool CSS is missing keyboard focus styling.")
    }
}

$FrontPagePath = Join-Path $Root "wp-content/themes/solo-to-china/front-page.php"
if (Test-Path -LiteralPath $FrontPagePath -PathType Leaf) {
    $FrontPage = Get-Content -LiteralPath $FrontPagePath -Raw
    if (-not $FrontPage.Contains("stc_render_guide_card_media")) {
        $Failures.Add("Homepage image cards are missing a dedicated media layer.")
    }
    if (-not $FrontPage.Contains("stc_render_survival_icon")) {
        $Failures.Add("Survival Kit cards are still missing real icon rendering.")
    }
    foreach ($SurvivalLabel in @("Payment", "Apps", "eSIM", "Visa", "VPN")) {
        if (-not $FrontPage.Contains("'title' => '$SurvivalLabel'")) {
            $Failures.Add("Homepage Survival Kit is missing compact label: $SurvivalLabel")
        }
    }
    foreach ($RemovedSurvivalCopy in @("Cards & mobile payments", "Essential Apps", "Stay connected anywhere.", "VPN / Internet")) {
        if ($FrontPage.Contains($RemovedSurvivalCopy)) {
            $Failures.Add("Homepage Survival Kit still includes superseded copy: $RemovedSurvivalCopy")
        }
    }
    if ($FrontPage.Contains("data-stc-save-guide") -or $FrontPage.Contains("stc-save-guide--image-card")) {
        $Failures.Add("Homepage cards still expose save actions before the guide is opened.")
    }
    if (-not $FrontPage.Contains("stc_render_faq_chevron") -or (-not $FrontPage.Contains("stc-faq__answer"))) {
        $Failures.Add("Homepage FAQ is missing the shared SVG chevron or answer wrapper.")
    }
    if ($FrontPage.Contains("stc_render_home_latest_guides")) {
        $Failures.Add("Homepage still inserts Latest Guides outside the approved reference layout.")
    }
    foreach ($HomepageToken in @("stc-survival", "stc-card-grid--cities", "stc-card-grid--attractions", "stc-planner", "stc-ticket-band", "stc-faq")) {
        if (-not $FrontPage.Contains($HomepageToken)) {
            $Failures.Add("Homepage is missing approved reference section: $HomepageToken")
        }
    }
    $HomepageOrder = @("stc-survival", "stc-card-grid--cities", "stc-card-grid--attractions", "stc-planner", "stc-ticket-band", "stc-faq")
    $PreviousHomepageIndex = -1
    foreach ($HomepageToken in $HomepageOrder) {
        $HomepageIndex = $FrontPage.IndexOf($HomepageToken)
        if ($HomepageIndex -le $PreviousHomepageIndex) {
            $Failures.Add("Homepage reference sections are not in the approved order near: $HomepageToken")
        }
        $PreviousHomepageIndex = $HomepageIndex
    }
    foreach ($HomepageMarkupToken in @("stc-planner__icon", "stc-ticket-band__icon", "stc-ticket-band__step-icon", "stc-guide-grid-shell", "data-stc-guide-grid-shell", "data-stc-guide-grid", "data-stc-guide-reveal", "stc-guide-grid-reveal__chevron")) {
        if (-not $FrontPage.Contains($HomepageMarkupToken)) {
            $Failures.Add("Homepage is missing responsive reference markup: $HomepageMarkupToken")
        }
    }
    if (-not $FrontPage.Contains("Start Exploring") -or $FrontPage.Contains("Start your China journey")) {
        $Failures.Add("Homepage Hero CTA does not use the refined Start Exploring copy.")
    }
    if (([regex]::Matches($FrontPage, 'class="stc-section__view-all"')).Count -ne 2 -or (-not $FrontPage.Contains('&rsaquo;'))) {
        $Failures.Add("Homepage guide sections are missing the unified View all arrow links.")
    }
    if ($FrontPage.Contains("data-stc-collapsible-grid") -or $FrontPage.Contains("data-stc-grid-toggle")) {
        $Failures.Add("Homepage still includes the removed vertical city-card expansion control.")
    }
    if (-not $FrontPage.Contains("https://www.trip.com/") -or (-not $FrontPage.Contains("rel=""sponsored noopener"""))) {
        $Failures.Add("Homepage Planner CTA is not a sponsored Trip.com external link.")
    }
    foreach ($ToolCopy in @("Plan Your Trip", "Book hotels, trains &amp; flights with confidence.", "Explore on Trip.com", "Ticket Date &amp; Availability", "Check booking windows &amp; set free alerts before your visit.", "Real-time Dates", "Free Alerts", "Check Dates &amp; Set Alerts", "Free to use", "No login required")) {
        if (-not $FrontPage.Contains($ToolCopy)) {
            $Failures.Add("Homepage tool cards are missing refined copy: $ToolCopy")
        }
    }
    foreach ($RemovedToolCopy in @("Start planning on", "Build your itinerary and book with confidence.", "Ticket Tool / Reminder", "See availability and important notes.", "Get notified before your visit.", "Check ticket date / Set reminder")) {
        if ($FrontPage.Contains($RemovedToolCopy)) {
            $Failures.Add("Homepage tool cards still include superseded copy: $RemovedToolCopy")
        }
    }
}

$ThemeCssPath = Join-Path $Root "wp-content/themes/solo-to-china/assets/css/main.css"
if (Test-Path -LiteralPath $ThemeCssPath -PathType Leaf) {
    $ThemeCss = Get-Content -LiteralPath $ThemeCssPath -Raw
    if (-not $ThemeCss.Contains("../images/hero-home.png")) {
        $Failures.Add("Homepage hero does not reference the generated hero image asset.")
    }
    foreach ($ImageReference in @("planner-art.png", "ticket-art.png")) {
        if (-not $ThemeCss.Contains($ImageReference)) {
            $Failures.Add("Theme CSS is missing reference-style visual asset: $ImageReference")
        }
    }
    foreach ($GuideImageStyle in @(".stc-image-card__media img", "object-fit: cover", "object-position: center", ".stc-image-card__media::after", "aspect-ratio: 3 / 4")) {
        if (-not $ThemeCss.Contains($GuideImageStyle)) {
            $Failures.Add("Theme CSS is missing high-resolution guide image behavior: $GuideImageStyle")
        }
    }
    if ($ThemeCss.Contains("var(--stc-card-image") -or $ThemeCss.Contains(".stc-image-card:hover .stc-image-card__media {")) {
        $Failures.Add("Guide card media still uses the old combined background or whole-layer zoom.")
    }
    foreach ($ToolCardStyle in @(".stc-affiliate-disclosure", "color: #8c9ba5", "font-size: 11px", ".stc-ticket-band__trust", "min-height: 44px", "opacity: .08")) {
        if (-not $ThemeCss.Contains($ToolCardStyle)) {
            $Failures.Add("Theme CSS is missing refined tool-card styling: $ToolCardStyle")
        }
    }
    foreach ($StyleToken in @(".home .stc-header", ".stc-header", "box-shadow", ".stc-page-hero--visual", ".stc-planner__icon", ".stc-ticket-band__icon", "scroll-snap-type")) {
        if (-not $ThemeCss.Contains($StyleToken)) {
            $Failures.Add("Theme CSS is missing selected homepage visual style token: $StyleToken")
        }
    }
    foreach ($FooterStyleToken in @(".stc-footer__inner", ".stc-footer__socials", ".stc-footer__bottom")) {
        if (-not $ThemeCss.Contains($FooterStyleToken)) {
            $Failures.Add("Theme CSS is missing selected homepage-reference footer style token: $FooterStyleToken")
        }
    }
    foreach ($FaqRefactorToken in @(".stc-faq__chevron", "border-bottom: 1px solid #e8ece9", "font-weight: 600", "color: #4a5568", "line-height: 1.6", "transform: rotate(180deg)")) {
        if (-not $ThemeCss.Contains($FaqRefactorToken)) {
            $Failures.Add("Theme CSS is missing refined FAQ accordion styling: $FaqRefactorToken")
        }
    }
    if ($ThemeCss.Contains(".stc-faq summary::after")) {
        $Failures.Add("FAQ still uses the old text pseudo-element icon instead of the SVG chevron.")
    }
    foreach ($ModernFooterToken in @("background: #0d1714", "color: #9eb0a7", "color: #8a9c94", "background: rgba(255, 255, 255, .08)", "height: 36px", ".stc-footer__legal")) {
        if (-not $ThemeCss.Contains($ModernFooterToken)) {
            $Failures.Add("Theme CSS is missing modern footer styling: $ModernFooterToken")
        }
    }
    if ($ThemeCss.Contains("rgba(0, 0, 0, .72)")) {
        $Failures.Add("Homepage hero still uses the old heavy left-side black overlay.")
    }
    if (-not $ThemeCss.Contains(".stc-header.is-menu-open")) {
        $Failures.Add("Theme CSS is missing the mobile navigation open state.")
    }
    foreach ($RemovedGuideSaveStyle in @(".stc-saved-guides", ".stc-save-guide", ".stc-article-save")) {
        if ($ThemeCss.Contains($RemovedGuideSaveStyle)) {
            $Failures.Add("Theme CSS still includes removed guide-saving presentation: $RemovedGuideSaveStyle")
        }
    }
    if (-not $ThemeCss.Contains(".stc-page-actions")) {
        $Failures.Add("Theme CSS is missing page sharing action styling.")
    }
    if (-not $ThemeCss.Contains(".stc-feature-panel--gold .stc-ticket-tool")) {
        $Failures.Add("Theme CSS is missing Tools page ticket tool layout containment.")
    }
    if (-not $ThemeCss.Contains(":focus-visible")) {
        $Failures.Add("Theme CSS is missing keyboard focus styling.")
    }
    if (-not $ThemeCss.Contains(".stc-skip-link")) {
        $Failures.Add("Theme CSS is missing skip-link styling.")
    }
    if (-not $ThemeCss.Contains(".search-form")) {
        $Failures.Add("Theme CSS is missing WordPress search form styling.")
    }
    if (-not $ThemeCss.Contains(".search-submit") -or (-not $ThemeCss.Contains("max-width: none"))) {
        $Failures.Add("Theme CSS is missing mobile search form stacking.")
    }
    if (-not $ThemeCss.Contains(".stc-post-card__meta")) {
        $Failures.Add("Theme CSS is missing Guide card metadata styling.")
    }
    if (-not $ThemeCss.Contains(".stc-post-card__type")) {
        $Failures.Add("Theme CSS is missing Guide card type badge styling.")
    }
    if (-not $ThemeCss.Contains(".stc-post-card__cta")) {
        $Failures.Add("Theme CSS is missing Guide card CTA styling.")
    }
    if (-not $ThemeCss.Contains(".stc-latest-guides")) {
        $Failures.Add("Theme CSS is missing core page latest guides styling.")
    }
    if ($ThemeCss.Contains(".stc-home-latest")) {
        $Failures.Add("Theme CSS still includes the removed homepage latest guides section.")
    }
    if (-not $ThemeCss.Contains(".stc-guide-toc")) {
        $Failures.Add("Theme CSS is missing Guide table of contents styling.")
    }
    foreach ($SecondaryPageStyleToken in @(".stc-page-primary .stc-card-grid--attractions", ".stc-guide-toc--mobile", ".stc-guide-toc--desktop")) {
        if (-not $ThemeCss.Contains($SecondaryPageStyleToken)) {
            $Failures.Add("Theme CSS is missing secondary-page responsive style: $SecondaryPageStyleToken")
        }
    }
    foreach ($MobileGridStyleToken in @(".stc-survival-card::after", ".stc-share__trigger", ".stc-guide-grid-shell", ".stc-guide-grid-reveal", "grid-template-columns: repeat(2", "max-height: var(--stc-guide-collapsed-height)", "max-height: var(--stc-guide-expanded-height)", "backdrop-filter: blur")) {
        if (-not $ThemeCss.Contains($MobileGridStyleToken)) {
            $Failures.Add("Theme CSS is missing requested four-card fold or page-utility style: $MobileGridStyleToken")
        }
    }
    foreach ($RefinedGuideCardToken in @(".stc-section__view-all", "linear-gradient(to top, rgba(0, 0, 0, .8) 0%, rgba(0, 0, 0, .35) 40%, transparent 100%)", "background: rgba(0, 0, 0, .45)", "-webkit-backdrop-filter: blur(8px)", "border: 1px solid rgba(255, 255, 255, .18)", "aspect-ratio: 3 / 4", "border-radius: 14px", "gap: 12px", "color: rgba(255, 255, 255, .85)", "font-size: 12px", "text-shadow: 0 2px 8px rgba(0, 0, 0, .65)", "box-shadow: 0 2px 8px rgba(0, 0, 0, .06)", "transform: scale(.96)")) {
        if (-not $ThemeCss.Contains($RefinedGuideCardToken)) {
            $Failures.Add("Theme CSS is missing refined mobile guide-card styling: $RefinedGuideCardToken")
        }
    }
    foreach ($MobileHeroToken in @("height: 75vh", "min-height: 480px", "max-height: 580px", "font-size: 32px", "line-height: 1.15", "font-size: 13px", "color: rgba(255, 255, 255, .9)", "margin-bottom: 20px", "linear-gradient(to top, rgba(0, 0, 0, .7) 0%, rgba(0, 0, 0, .3) 50%, rgba(0, 0, 0, .1) 100%)", "background: #c84832", "height: 46px", "border-radius: 10px", "box-shadow: 0 4px 16px rgba(0, 0, 0, .18)", "transform: scale(.97)", "background: rgba(255, 255, 255, .15)", "border: 1px solid rgba(255, 255, 255, .25)")) {
        if (-not $ThemeCss.Contains($MobileHeroToken)) {
            $Failures.Add("Theme CSS is missing refined mobile Hero styling: $MobileHeroToken")
        }
    }
    foreach ($MobileToolTypographyToken in @(".stc-planner__intro p", ".stc-ticket-band__intro p", ".stc-ticket-band__steps strong", ".stc-ticket-band__action p")) {
        if (-not $ThemeCss.Contains($MobileToolTypographyToken)) {
            $Failures.Add("Theme CSS is missing mobile tool typography tuning: $MobileToolTypographyToken")
        }
    }
    foreach ($SurvivalResponsiveToken in @(".home .stc-survival__grid", "grid-template-columns: repeat(5, minmax(0, 1fr))", "gap: 4px", "padding: 16px 8px", "background: rgba(20, 83, 45, .08)", "color: #14532d", "font-size: 11px", "font-weight: 500", "text-overflow: ellipsis", "transform: scale(.95)")) {
        if (-not $ThemeCss.Contains($SurvivalResponsiveToken)) {
            $Failures.Add("Theme CSS is missing five-column Survival Kit behavior: $SurvivalResponsiveToken")
        }
    }
    foreach ($RemovedSurvivalStyle in @(".home .stc-survival-card span:last-child", ".home .stc-survival-card:nth-child(even)::after", ".home .stc-survival-card:last-child")) {
        if ($ThemeCss.Contains($RemovedSurvivalStyle)) {
            $Failures.Add("Theme CSS still includes obsolete Survival Kit layout styling: $RemovedSurvivalStyle")
        }
    }
    foreach ($RemovedGuideRailStyle in @("grid-auto-columns: 75vw", "grid-auto-columns: 42vw", ".stc-city-grid-shell", ".stc-city-grid-reveal")) {
        if ($ThemeCss.Contains($RemovedGuideRailStyle)) {
            $Failures.Add("Theme CSS still includes a removed guide rail or gradient-overlay style: $RemovedGuideRailStyle")
        }
    }
    foreach ($RemovedCardControlStyle in @(".stc-grid-toggle", ".stc-save-guide--image-card", ".is-collapsible")) {
        if ($ThemeCss.Contains($RemovedCardControlStyle)) {
            $Failures.Add("Theme CSS still includes removed card control style: $RemovedCardControlStyle")
        }
    }
    foreach ($UtilityPageStyleToken in @(".stc-planner--page", ".stc-faq--page", ".stc-faq__chevron", ".stc-faq__answer-link")) {
        if (-not $ThemeCss.Contains($UtilityPageStyleToken)) {
            $Failures.Add("Theme CSS is missing utility-page responsive style: $UtilityPageStyleToken")
        }
    }
    foreach ($GuideClass in @(".stc-guide-quick-facts", ".stc-guide-fact", ".stc-guide-warning", ".stc-guide-route")) {
        if (-not $ThemeCss.Contains($GuideClass)) {
            $Failures.Add("Theme CSS is missing structured Attraction Guide content styling: $GuideClass")
        }
    }
    foreach ($GenericArticleStyle in @(".stc-article-hero", ".stc-article-layout--with-toc", ".stc-article-sidebar", ".stc-share__panel")) {
        if (-not $ThemeCss.Contains($GenericArticleStyle)) {
            $Failures.Add("Theme CSS is missing generic article or ShareThisPage styling: $GenericArticleStyle")
        }
    }
}

$ThemeJsPath = Join-Path $Root "wp-content/themes/solo-to-china/assets/js/main.js"
if (Test-Path -LiteralPath $ThemeJsPath -PathType Leaf) {
    $ThemeJs = Get-Content -LiteralPath $ThemeJsPath -Raw
    if (-not $ThemeJs.Contains("stcMobileNav")) {
        $Failures.Add("Theme JavaScript is missing the mobile navigation controller.")
    }
    foreach ($RemovedGuideSaveScript in @("stcSavedGuides", "data-stc-delete-guide", "data-stc-save-guide", "data-stc-saved-guides", "data-stc-export-guides", "data-stc-import-guides", "data-stc-clear-guides", "window.localStorage")) {
        if ($ThemeJs.Contains($RemovedGuideSaveScript)) {
            $Failures.Add("Theme JavaScript still contains removed guide-saving behavior: $RemovedGuideSaveScript")
        }
    }
    if (-not $ThemeJs.Contains("stcGuideToc")) {
        $Failures.Add("Theme JavaScript is missing the automatic Guide table of contents controller.")
    }
    if (-not $ThemeJs.Contains("data-stc-guide-toc-list")) {
        $Failures.Add("Theme JavaScript is missing the Guide table of contents list target.")
    }
    if (-not $ThemeJs.Contains("querySelectorAll('[data-stc-guide-toc]')")) {
        $Failures.Add("Theme JavaScript does not populate both desktop and mobile Guide tables of contents.")
    }
    foreach ($ShareScriptToken in @("navigator.share", "navigator.clipboard", "data-stc-share-trigger", "data-stc-share-panel", "data-stc-share-copy", "data-stc-share-close", "AbortError", "Link copied", "Escape")) {
        if (-not $ThemeJs.Contains($ShareScriptToken)) {
            $Failures.Add("Theme JavaScript is missing accessible ShareThisPage behavior: $ShareScriptToken")
        }
    }
}

$PhpExecutable = $null
$PhpCommand = Get-Command php -ErrorAction SilentlyContinue
$LocalPhpPath = Join-Path $Root ".tools/php/php.exe"
if ($PhpCommand) {
    $PhpExecutable = $PhpCommand.Source
} elseif (Test-Path -LiteralPath $LocalPhpPath -PathType Leaf) {
    $PhpExecutable = $LocalPhpPath
}

if ($PhpExecutable) {
    foreach ($RelativePath in $RequiredFiles | Where-Object { $_.EndsWith(".php") }) {
        $AbsolutePath = Join-Path $Root $RelativePath
        if (Test-Path -LiteralPath $AbsolutePath -PathType Leaf) {
            & $PhpExecutable -l $AbsolutePath | Out-Null
            if ($LASTEXITCODE -ne 0) {
                $Failures.Add("PHP syntax check failed: $RelativePath")
            }
        }
    }
} else {
    Write-Host "PHP CLI not found; skipped PHP syntax checks."
}

if ($Failures.Count -gt 0) {
    $Failures | ForEach-Object { Write-Error $_ }
    exit 1
}

Write-Host "SoloToChina project verification passed."
