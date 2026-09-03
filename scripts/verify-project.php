<?php
/**
 * Lightweight repository verification for the first SoloToChina WordPress phase.
 */

$root = dirname(__DIR__);

$requiredFiles = [
    'docs/handoff/current-progress.md',
    'docs/handoff/new-chat-handoff.md',
    'docs/deployment/wordpress-install.md',
    'docs/COMPONENT_LIBRARY.md',
    'docs/CMS_FRONTEND_CONTRACT.md',
    'docs/COMPONENT_CHANGELOG.md',
    'contracts/component-registry.json',
    'contracts/page-schema.json',
    'scripts/package-release.ps1',
    'scripts/generate-component-catalog.ps1',
    'scripts/verify-component-registry.ps1',
    'scripts/verify-content-contract.ps1',
    'scripts/verify-content-runtime.ps1',
    'scripts/verify-commercial-components.php',
    'scripts/verify-page-architecture.ps1',
    'scripts/playground-blueprint.json',
    'scripts/playground-editor-blueprint.json',
    'scripts/playground-parent-blueprint.json',
    'scripts/playground-fixtures.php',
    'scripts/start-preview.ps1',
    'wp-content/themes/solo-to-china/style.css',
    'wp-content/themes/solo-to-china/README.md',
    'wp-content/themes/solo-to-china/functions.php',
    'wp-content/themes/solo-to-china/content-contract/content-contract.v2.json',
    'wp-content/themes/solo-to-china/content-contract/component-registry.v1.json',
    'wp-content/themes/solo-to-china/inc/content-contract.php',
    'wp-content/themes/solo-to-china/inc/component-registry.php',
    'wp-content/themes/solo-to-china/inc/content-components.php',
    'wp-content/themes/solo-to-china/inc/content-renderers.php',
    'wp-content/themes/solo-to-china/inc/commercial-events.php',
    'wp-content/themes/solo-to-china/content-contract/component-registry.generated.json',
    'wp-content/themes/solo-to-china/content-contract/page-schema.generated.json',
    'wp-content/themes/solo-to-china/header.php',
    'wp-content/themes/solo-to-china/footer.php',
    'wp-content/themes/solo-to-china/index.php',
    'wp-content/themes/solo-to-china/archive.php',
    'wp-content/themes/solo-to-china/single.php',
    'wp-content/themes/solo-to-china/404.php',
    'wp-content/themes/solo-to-china/search.php',
    'wp-content/themes/solo-to-china/searchform.php',
    'wp-content/themes/solo-to-china/page.php',
    'wp-content/themes/solo-to-china/page-design-system.php',
    'wp-content/themes/solo-to-china/front-page.php',
    'wp-content/themes/solo-to-china/screenshot.png',
    'wp-content/themes/solo-to-china/assets/css/main.css',
    'wp-content/themes/solo-to-china/assets/css/editor-style.css',
    'wp-content/themes/solo-to-china/assets/js/main.js',
    'wp-content/themes/solo-to-china/assets/js/commercial-events.js',
    'wp-content/themes/solo-to-china/assets/images/hero-home.png',
    'wp-content/themes/solo-to-china/assets/images/guide-card-bg.png',
    'wp-content/themes/solo-to-china/assets/images/card-beijing.png',
    'wp-content/themes/solo-to-china/assets/images/card-shanghai.png',
    'wp-content/themes/solo-to-china/assets/images/card-guangzhou.png',
    'wp-content/themes/solo-to-china/assets/images/card-chengdu.png',
    'wp-content/themes/solo-to-china/assets/images/card-chongqing.png',
    'wp-content/themes/solo-to-china/assets/images/card-xian.png',
    'wp-content/themes/solo-to-china/assets/images/card-hangzhou.png',
    'wp-content/themes/solo-to-china/assets/images/card-zhangjiajie-city.png',
    'wp-content/themes/solo-to-china/assets/images/card-forbidden-city.png',
    'wp-content/themes/solo-to-china/assets/images/card-great-wall.png',
    'wp-content/themes/solo-to-china/assets/images/card-terracotta.png',
    'wp-content/themes/solo-to-china/assets/images/card-zhangjiajie-attraction.png',
    'wp-content/themes/solo-to-china/assets/images/card-west-lake.png',
    'wp-content/themes/solo-to-china/assets/images/card-disney.png',
    'wp-content/themes/solo-to-china/assets/images/planner-art.png',
    'wp-content/themes/solo-to-china/assets/images/ticket-art.png',
    'wp-content/themes/solo-to-china/assets/images/card-beijing-hd.webp',
    'wp-content/themes/solo-to-china/assets/images/card-shanghai-hd.webp',
    'wp-content/themes/solo-to-china/assets/images/card-guangzhou-hd.webp',
    'wp-content/themes/solo-to-china/assets/images/card-chengdu-hd.webp',
    'wp-content/themes/solo-to-china/assets/images/card-chongqing-hd.webp',
    'wp-content/themes/solo-to-china/assets/images/card-xian-hd.webp',
    'wp-content/themes/solo-to-china/assets/images/card-hangzhou-hd.webp',
    'wp-content/themes/solo-to-china/assets/images/card-zhangjiajie-city-hd.webp',
    'wp-content/themes/solo-to-china/assets/images/card-forbidden-city-hd.webp',
    'wp-content/themes/solo-to-china/assets/images/card-great-wall-hd.webp',
    'wp-content/themes/solo-to-china/assets/images/card-terracotta-hd.webp',
    'wp-content/themes/solo-to-china/assets/images/card-zhangjiajie-attraction-hd.webp',
    'wp-content/themes/solo-to-china/assets/images/card-west-lake-hd.webp',
    'wp-content/themes/solo-to-china/assets/images/card-disney-hd.webp',
    'wp-content/themes/solo-to-china-child/style.css',
    'wp-content/themes/solo-to-china-child/functions.php',
    'wp-content/themes/solo-to-china-child/header.php',
    'wp-content/themes/solo-to-china-child/README.md',
    'wp-content/themes/solo-to-china-child/assets/css/design-system.css',
    'wp-content/themes/solo-to-china-child/assets/css/site.css',
    'wp-content/themes/solo-to-china-child/assets/css/home.css',
    'wp-content/themes/solo-to-china-child/assets/css/article.css',
    'wp-content/themes/solo-to-china-child/assets/css/content-components.css',
    'wp-content/themes/solo-to-china-child/assets/css/component-gallery.css',
    'wp-content/themes/solo-to-china-child/assets/css/editor-style.css',
    'wp-content/themes/solo-to-china-child/assets/js/site.js',
    'wp-content/plugins/solo-to-china-tools/solo-to-china-tools.php',
    'wp-content/plugins/solo-to-china-tools/README.md',
    'wp-content/plugins/solo-to-china-tools/includes/attractions.php',
    'wp-content/plugins/solo-to-china-tools/includes/shortcodes.php',
    'wp-content/plugins/solo-to-china-tools/assets/css/tools.css',
    'wp-content/plugins/solo-to-china-tools/assets/js/tools.js',
];

$requiredNavLabels = [
    'Home',
    'Survival Kit',
    'City Guides',
    'Attraction Guides',
    'Planner',
    'Tools',
    'FAQ',
];

$bannedNavLabels = [
    'Hotels',
    'Tickets',
    'Flights',
    'Trains',
    'Book',
];

$failures = [];

foreach ($requiredFiles as $relativePath) {
    $absolutePath = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
    if (!is_file($absolutePath)) {
        $failures[] = "Missing required file: {$relativePath}";
    }
}

$gitignorePath = $root . DIRECTORY_SEPARATOR . '.gitignore';
if (is_file($gitignorePath)) {
    $gitignore = file_get_contents($gitignorePath);
    if (strpos($gitignore, 'dist/') === false) {
        $failures[] = '.gitignore does not ignore generated release artifacts.';
    }
    if (strpos($gitignore, '*.zip') === false) {
        $failures[] = '.gitignore does not ignore generated zip archives.';
    }
}

$packageScriptPath = $root . DIRECTORY_SEPARATOR . 'scripts/package-release.ps1';
if (is_file($packageScriptPath)) {
    $packageScript = file_get_contents($packageScriptPath);
    if (strpos($packageScript, 'release-manifest.txt') === false) {
        $failures[] = 'Package script does not create a release manifest.';
    }
    if (strpos($packageScript, 'Get-FileHash') === false) {
        $failures[] = 'Package script does not record zip checksums.';
    }
    foreach (['solo-to-china-child', 'solo-to-china-child-theme.zip', 'Child Theme SHA256'] as $childPackageToken) {
        if (strpos($packageScript, $childPackageToken) === false) {
            $failures[] = "Package script does not include the Child Theme artifact token: {$childPackageToken}";
        }
    }
    if (strpos($packageScript, 'Theme version: 0.26.0') === false || strpos($packageScript, 'Child Theme version: 0.8.0') === false || strpos($packageScript, 'Plugin version: 0.22.0') === false) {
        $failures[] = 'Package script does not write artifact versions to the release manifest.';
    }
}

$installDocPath = $root . DIRECTORY_SEPARATOR . 'docs/deployment/wordpress-install.md';
if (is_file($installDocPath)) {
    $installDoc = file_get_contents($installDocPath);
    if (strpos($installDoc, 'Post-Install Check') === false) {
        $failures[] = 'WordPress install handoff is missing the post-install check list.';
    }
    if (strpos($installDoc, 'Do not extract either zip directly inside') === false) {
        $failures[] = 'WordPress install handoff is missing aaPanel extraction warning.';
    }
    foreach (['Install SoloToChina Parent Theme first', 'Activate SoloToChina Child'] as $childInstallToken) {
        if (strpos($installDoc, $childInstallToken) === false) {
            $failures[] = "WordPress install handoff is missing Child Theme install order: {$childInstallToken}";
        }
    }
}

$previewScriptPath = $root . DIRECTORY_SEPARATOR . 'scripts/start-preview.ps1';
$previewBlueprintPath = $root . DIRECTORY_SEPARATOR . 'scripts/playground-blueprint.json';
if (is_file($previewScriptPath)) {
    $previewScript = file_get_contents($previewScriptPath);
    foreach (['@wp-playground/cli@latest', 'solo-to-china-child', 'solo-to-china-tools', '--mount-dir', 'playground-blueprint.json'] as $previewToken) {
        if (strpos($previewScript, $previewToken) === false) {
            $failures[] = "WordPress Playground preview script is missing: {$previewToken}";
        }
    }
}
if (is_file($previewBlueprintPath)) {
    $previewBlueprint = file_get_contents($previewBlueprintPath);
    foreach (['activateTheme', 'solo-to-china-child', 'activatePlugin', 'solo-to-china-tools/solo-to-china-tools.php'] as $blueprintToken) {
        if (strpos($previewBlueprint, $blueprintToken) === false) {
            $failures[] = "WordPress Playground blueprint is missing: {$blueprintToken}";
        }
    }
}

$newChatHandoffPath = $root . DIRECTORY_SEPARATOR . 'docs/handoff/new-chat-handoff.md';
if (is_file($newChatHandoffPath)) {
    $newChatHandoff = file_get_contents($newChatHandoffPath);
    foreach (['Suggested New Chat Opening Message', 'Fixed Information Architecture', 'Do Not Start Without Explicit Approval', 'Development Style For Next Chat'] as $requiredHandoffText) {
        if (strpos($newChatHandoff, $requiredHandoffText) === false) {
            $failures[] = "New chat handoff is missing section: {$requiredHandoffText}";
        }
    }
}

$themeStylePath = $root . DIRECTORY_SEPARATOR . 'wp-content/themes/solo-to-china/style.css';
if (is_file($themeStylePath)) {
    $themeStyle = file_get_contents($themeStylePath);
    if (strpos($themeStyle, 'Version: 0.26.0') === false) {
        $failures[] = 'Theme stylesheet header version is not 0.26.0.';
    }
    if (strpos($themeStyle, 'Requires at least: 6.5') === false) {
        $failures[] = 'Theme stylesheet header is missing the minimum WordPress version.';
    }
    if (strpos($themeStyle, 'Requires PHP: 7.4') === false) {
        $failures[] = 'Theme stylesheet header is missing the minimum PHP version.';
    }
}

$themeReadmePath = $root . DIRECTORY_SEPARATOR . 'wp-content/themes/solo-to-china/README.md';
if (is_file($themeReadmePath)) {
    $themeReadme = file_get_contents($themeReadmePath);
    if (strpos($themeReadme, 'Current version: `0.26.0`') === false) {
        $failures[] = 'Theme README does not document the current theme version.';
    }
    if (strpos($themeReadme, 'The theme should not own tool business logic') === false) {
        $failures[] = 'Theme README does not preserve the theme/plugin responsibility boundary.';
    }
}

$headerPath = $root . DIRECTORY_SEPARATOR . 'wp-content/themes/solo-to-china/header.php';
$functionsPath = $root . DIRECTORY_SEPARATOR . 'wp-content/themes/solo-to-china/functions.php';
if (is_file($headerPath) && is_file($functionsPath)) {
    $header = file_get_contents($headerPath);
    $functions = file_get_contents($functionsPath);
    $navigationSource = $header . "\n" . $functions;
    foreach ($requiredNavLabels as $label) {
        if (strpos($navigationSource, $label) === false) {
            $failures[] = "Header is missing navigation label: {$label}";
        }
    }
    foreach ($bannedNavLabels as $label) {
        if (preg_match('/>' . preg_quote($label, '/') . '</', $navigationSource)) {
            $failures[] = "Header includes banned top-level navigation label: {$label}";
        }
    }

    if (strpos($header, 'stc-menu-toggle') === false) {
        $failures[] = 'Header is missing the mobile navigation toggle button.';
    }
    if (strpos($header, 'aria-expanded="false"') === false) {
        $failures[] = 'Mobile navigation toggle is missing the default collapsed ARIA state.';
    }
    if (strpos($header, 'Skip to content') === false) {
        $failures[] = 'Header is missing a skip-to-content link.';
    }
    if (strpos($functions, 'stc_ensure_core_pages') === false) {
        $failures[] = 'Theme setup does not create missing core IA pages on activation.';
    }
    if (strpos($functions, 'stc_ensure_core_categories') === false) {
        $failures[] = 'Theme setup does not create missing core guide categories on activation.';
    }
    if (strpos($functions, 'automatic-feed-links') === false) {
        $failures[] = 'Theme setup is missing automatic feed links support.';
    }
    if (strpos($functions, 'align-wide') === false) {
        $failures[] = 'Theme setup is missing wide alignment support.';
    }
    if (strpos($functions, 'STC_THEME_VERSION') === false) {
        $failures[] = 'Theme functions are missing a single theme version constant.';
    }
    if (strpos($functions, "add_image_size( 'stc-guide-card-2x', 960") === false) {
        $failures[] = 'Theme setup is missing the 960px Retina guide card image size.';
    }
    if (strpos($functions, 'wp_get_attachment_image') === false || strpos($functions, "'stc-guide-card-2x'") === false) {
        $failures[] = 'WordPress guide cards do not request the responsive Retina image size.';
    }
    if (strpos($functions, 'stc_render_guide_card_media') === false) {
        $failures[] = 'Theme functions are missing the shared high-resolution guide card media renderer.';
    }
    if (strpos($functions, "'0.26.0'") === false) {
        $failures[] = 'Theme asset version is not 0.26.0.';
    }
    foreach (['stc_render_share_this_page', 'data-stc-share', 'data-stc-share-trigger', 'data-stc-share-panel'] as $shareRendererToken) {
        if (strpos($functions, $shareRendererToken) === false) {
            $failures[] = 'Theme functions are missing the reusable ShareThisPage renderer: ' . $shareRendererToken;
        }
    }
    if (strpos($functions, 'stc_is_attraction_guide_post') === false) {
        $failures[] = 'Theme functions are missing the Attraction Guide post detector.';
    }
    if (strpos($functions, 'stc_is_city_guide_post') === false) {
        $failures[] = 'Theme functions are missing the City Guide post detector.';
    }
    if (strpos($functions, 'stc_is_survival_kit_post') === false) {
        $failures[] = 'Theme functions are missing the Survival Kit post detector.';
    }
    if (strpos($functions, 'stc_get_guide_type_label') === false) {
        $failures[] = 'Theme functions are missing the guide type label helper.';
    }
    if (strpos($functions, 'stc_render_guide_card') === false) {
        $failures[] = 'Theme functions are missing the shared Guide card renderer.';
    }
    if (strpos($functions, 'stc_render_guide_toc') === false) {
        $failures[] = 'Theme functions are missing the shared Guide table of contents renderer.';
    }
    if (strpos($functions, 'stc_render_core_page_latest_guides') === false) {
        $failures[] = 'Theme functions are missing the core page latest guides renderer.';
    }
    if (strpos($functions, 'stc_render_home_latest_guides') !== false) {
        $failures[] = 'Theme functions still include the removed homepage latest guides renderer.';
    }
}

$childThemeStylePath = $root . DIRECTORY_SEPARATOR . 'wp-content/themes/solo-to-china-child/style.css';
$childThemeFunctionsPath = $root . DIRECTORY_SEPARATOR . 'wp-content/themes/solo-to-china-child/functions.php';
$childThemeDesignSystemPath = $root . DIRECTORY_SEPARATOR . 'wp-content/themes/solo-to-china-child/assets/css/design-system.css';
if (is_file($childThemeStylePath)) {
    $childThemeStyle = file_get_contents($childThemeStylePath);
    foreach (['Theme Name: SoloToChina Child', 'Template: solo-to-china', 'Version: 0.8.0', 'Text Domain: solo-to-china-child'] as $childHeaderToken) {
        if (strpos($childThemeStyle, $childHeaderToken) === false) {
            $failures[] = "Child Theme stylesheet header is missing: {$childHeaderToken}";
        }
    }
}

if (is_file($childThemeFunctionsPath)) {
    $childThemeFunctions = file_get_contents($childThemeFunctionsPath);
    foreach (['STC_CHILD_VERSION', 'wp_enqueue_scripts', 'stc-main', 'get_stylesheet_uri', 'get_stylesheet_directory_uri', 'stc-child-design-system'] as $childFunctionToken) {
        if (strpos($childThemeFunctions, $childFunctionToken) === false) {
            $failures[] = "Child Theme resource loading is missing: {$childFunctionToken}";
        }
    }
    foreach (['stc-child-site', 'stc-child-home', 'is_front_page', 'stc-child-interactions', 'stc_child_render_primary_navigation'] as $childStageToken) {
        if (strpos($childThemeFunctions, $childStageToken) === false) {
            $failures[] = "Child Theme shared/home asset stage is missing: {$childStageToken}";
        }
    }
}

if (is_file($childThemeDesignSystemPath)) {
    $childThemeDesignSystem = file_get_contents($childThemeDesignSystemPath);
    foreach (['--stc-color-ink', '--stc-font-sans', '--stc-space-', '--stc-container-max', '--stc-grid-gap', '--stc-radius-', '--stc-shadow-', '--stc-control-height', '--stc-image-ratio', '--stc-motion-duration', ':focus-visible', 'prefers-reduced-motion'] as $designSystemToken) {
        if (strpos($childThemeDesignSystem, $designSystemToken) === false) {
            $failures[] = "Child Theme design system is missing: {$designSystemToken}";
        }
    }
}

$childThemeHeaderPath = $root . DIRECTORY_SEPARATOR . 'wp-content/themes/solo-to-china-child/header.php';
if (is_file($childThemeHeaderPath)) {
    $childThemeHeader = file_get_contents($childThemeHeaderPath);
    foreach (['Skip to content', 'stc_child_render_primary_navigation', 'aria-expanded="false"', 'data-open-label', 'data-close-label'] as $childHeaderMarkupToken) {
        if (strpos($childThemeHeader, $childHeaderMarkupToken) === false) {
            $failures[] = "Child Theme Header override is missing: {$childHeaderMarkupToken}";
        }
    }
}

$childThemeSiteCssPath = $root . DIRECTORY_SEPARATOR . 'wp-content/themes/solo-to-china-child/assets/css/site.css';
if (is_file($childThemeSiteCssPath)) {
    $childThemeSiteCss = file_get_contents($childThemeSiteCssPath);
    foreach (['.stc-header', '.stc-nav__link[aria-current="page"]', '.stc-menu-toggle__line', '.stc-image-card', '.stc-footer', '.stc-footer__socials', '@media (max-width: 840px)'] as $childSiteCssToken) {
        if (strpos($childThemeSiteCss, $childSiteCssToken) === false) {
            $failures[] = "Child Theme shared site CSS is missing: {$childSiteCssToken}";
        }
    }
}

$childThemeHomeCssPath = $root . DIRECTORY_SEPARATOR . 'wp-content/themes/solo-to-china-child/assets/css/home.css';
if (is_file($childThemeHomeCssPath)) {
    $childThemeHomeCss = file_get_contents($childThemeHomeCssPath);
    foreach (['.home .stc-hero', '.home .stc-survival', '.home .stc-card-grid--cities', '.home .stc-card-grid--attractions', '.home .stc-planner', '.home .stc-ticket-band', '.home .stc-faq', '@media (max-width: 599px)'] as $childHomeCssToken) {
        if (strpos($childThemeHomeCss, $childHomeCssToken) === false) {
            $failures[] = "Child Theme homepage CSS is missing: {$childHomeCssToken}";
        }
    }
}

$childThemeSiteJsPath = $root . DIRECTORY_SEPARATOR . 'wp-content/themes/solo-to-china-child/assets/js/site.js';
if (is_file($childThemeSiteJsPath)) {
    $childThemeSiteJs = file_get_contents($childThemeSiteJsPath);
    foreach (['Escape', 'aria-expanded', 'data-open-label', 'data-close-label', 'matchMedia'] as $childSiteJsToken) {
        if (strpos($childThemeSiteJs, $childSiteJsToken) === false) {
            $failures[] = "Child Theme navigation enhancement is missing: {$childSiteJsToken}";
        }
    }
}

$themePhpFiles = glob($root . DIRECTORY_SEPARATOR . 'wp-content/themes/solo-to-china/*.php');
foreach ($themePhpFiles as $themePhpFile) {
    $themePhp = file_get_contents($themePhpFile);
    if (strpos($themePhp, 'the_permalink();') !== false) {
        $failures[] = 'Theme template uses unescaped the_permalink output: ' . basename($themePhpFile);
    }
}

$footerPath = $root . DIRECTORY_SEPARATOR . 'wp-content/themes/solo-to-china/footer.php';
if (is_file($footerPath)) {
    $footer = file_get_contents($footerPath);
    foreach (['stc-footer__inner', 'stc-footer__socials', 'stc-footer__bottom', 'stc-footer__legal', 'Privacy Policy', 'Terms of Use', 'Guest-first. Practical. Independent.'] as $footerToken) {
        if (strpos($footer, $footerToken) === false) {
            $failures[] = "Footer does not preserve the selected homepage-reference footer token: {$footerToken}";
        }
    }
}

$pageTemplatePath = $root . DIRECTORY_SEPARATOR . 'wp-content/themes/solo-to-china/page.php';
if (is_file($pageTemplatePath)) {
    $pageTemplate = file_get_contents($pageTemplatePath);
    foreach (['survival-kit', 'city-guides', 'attraction-guides', 'planner', 'tools', 'faq'] as $slug) {
        if (strpos($pageTemplate, $slug) === false) {
            $failures[] = "Page template does not handle core IA slug: {$slug}";
        }
    }
    if (strpos($pageTemplate, 'solo_to_china_ticket_tool') === false) {
        $failures[] = 'Tools page template does not render the guest-first ticket tool shortcode.';
    }
    foreach (['data-stc-save-guide', 'data-stc-saved-guides', 'data-stc-export-guides', 'data-stc-import-guides', 'data-stc-clear-guides', 'Saved on this device'] as $removedGuideSaveToken) {
        if (strpos($pageTemplate, $removedGuideSaveToken) !== false) {
            $failures[] = 'Core page template still contains removed guide-saving UI: ' . $removedGuideSaveToken;
        }
    }
    if (strpos($pageTemplate, 'stc_render_share_this_page') === false) {
        $failures[] = 'Core page template is missing reusable no-account page sharing.';
    }
    if (strpos($pageTemplate, 'stc_render_faq_chevron') === false || strpos($pageTemplate, 'stc-faq__answer') === false) {
        $failures[] = 'FAQ page template is missing the shared SVG chevron or answer wrapper.';
    }
    if (strpos($pageTemplate, 'stc_render_core_page_latest_guides') === false) {
        $failures[] = 'Core guide pages do not render latest published guide posts.';
    }
    if (strpos($pageTemplate, 'stc-card-grid--cities') === false || strpos($pageTemplate, 'stc-card-grid--attractions') === false) {
        $failures[] = 'City Guides and Attraction Guides landing pages do not use the homepage-reference image card grids.';
    }
    foreach (['stc-guide-grid-shell', 'data-stc-guide-grid-shell', 'data-stc-guide-grid', 'data-stc-guide-reveal', 'data-stc-guide-label'] as $guideLandingToken) {
        if (strpos($pageTemplate, $guideLandingToken) === false) {
            $failures[] = "Guide landing pages are missing shared four-card fold markup: {$guideLandingToken}";
        }
    }
    foreach (['$guide_landing_slugs', 'stc-planner--page', 'stc-planner__icon', 'stc-planner__art', 'stc-faq--page', 'stc-faq__answer-link'] as $utilityPageToken) {
        if (strpos($pageTemplate, $utilityPageToken) === false) {
            $failures[] = "Core page template is missing utility-page presentation token: {$utilityPageToken}";
        }
    }
    foreach (['stc-page-primary', 'stc_render_core_page_latest_guides'] as $landingToken) {
        if (strpos($pageTemplate, $landingToken) === false) {
            $failures[] = "Core landing page is missing content-first structure token: {$landingToken}";
        }
    }
    $primaryContentIndex = strpos($pageTemplate, 'stc-page-primary');
    $latestGuidesIndex = strpos($pageTemplate, 'stc_render_core_page_latest_guides');
    if ($primaryContentIndex === false || $latestGuidesIndex === false || $latestGuidesIndex <= $primaryContentIndex) {
        $failures[] = 'Core landing page does not keep primary content before latest posts.';
    }
}

$singleTemplatePath = $root . DIRECTORY_SEPARATOR . 'wp-content/themes/solo-to-china/single.php';
if (is_file($singleTemplatePath)) {
    $singleTemplate = file_get_contents($singleTemplatePath);
    foreach (['stc-article-hero', 'stc-article-layout', 'stc-entry-content--guide', 'stc_get_hero_variant', "stc_page_presentation_enabled( 'share'", "stc_page_presentation_enabled( 'toc'", 'stc_render_share_this_page', 'stc_render_guide_toc', 'the_content()'] as $genericShellToken) {
        if (strpos($singleTemplate, $genericShellToken) === false) {
            $failures[] = 'Generic single article shell is missing: ' . $genericShellToken;
        }
    }
    foreach (['stc_is_attraction_guide_post', 'stc_is_city_guide_post', 'stc_is_survival_kit_post', 'Planning checklist', 'City planning checklist', 'Setup checklist', 'stc_render_article_save_button'] as $fixedEditorialToken) {
        if (strpos($singleTemplate, $fixedEditorialToken) !== false) {
            $failures[] = 'Single template still binds editorial layout to content type: ' . $fixedEditorialToken;
        }
    }
}

foreach ([
    'wp-content/themes/solo-to-china/archive.php',
    'wp-content/themes/solo-to-china/search.php',
    'wp-content/themes/solo-to-china/index.php',
] as $listingTemplatePath) {
    $absoluteListingTemplatePath = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $listingTemplatePath);
    if (is_file($absoluteListingTemplatePath)) {
        $listingTemplate = file_get_contents($absoluteListingTemplatePath);
        if (strpos($listingTemplate, 'stc_render_guide_card') === false) {
            $failures[] = "Listing template does not use the shared Guide card renderer: {$listingTemplatePath}";
        }
    }
}

$pluginPath = $root . DIRECTORY_SEPARATOR . 'wp-content/plugins/solo-to-china-tools/solo-to-china-tools.php';
if (is_file($pluginPath)) {
    $plugin = file_get_contents($pluginPath);
    $shortcodesPath = $root . DIRECTORY_SEPARATOR . 'wp-content/plugins/solo-to-china-tools/includes/shortcodes.php';
    $attractionsPath = $root . DIRECTORY_SEPARATOR . 'wp-content/plugins/solo-to-china-tools/includes/attractions.php';
    $shortcodes = is_file($shortcodesPath) ? file_get_contents($shortcodesPath) : '';
    $attractions = is_file($attractionsPath) ? file_get_contents($attractionsPath) : '';
    $pluginSource = $plugin . "\n" . $shortcodes . "\n" . $attractions;

    $pluginReadmePath = $root . DIRECTORY_SEPARATOR . 'wp-content/plugins/solo-to-china-tools/README.md';
    if (is_file($pluginReadmePath)) {
        $pluginReadme = file_get_contents($pluginReadmePath);
        if (strpos($pluginReadme, 'Current version: `0.22.0`') === false) {
            $failures[] = 'Tools plugin README does not document the current plugin version.';
        }
        if (strpos($pluginReadme, 'limited to Attraction Ticket Reservation & Reminder') === false) {
            $failures[] = 'Tools plugin README does not preserve the first-tool boundary.';
        }
    }

    if (strpos($plugin, 'Version: 0.22.0') === false) {
        $failures[] = 'Tools plugin header version is not 0.22.0.';
    }
    if (strpos($plugin, "STC_TOOLS_VERSION', '0.22.0'") === false) {
        $failures[] = 'Tools plugin version constant is not 0.22.0.';
    }
    if (strpos($plugin, 'Requires at least: 6.5') === false) {
        $failures[] = 'Tools plugin header is missing the minimum WordPress version.';
    }
    if (strpos($plugin, 'Requires PHP: 7.4') === false) {
        $failures[] = 'Tools plugin header is missing the minimum PHP version.';
    }
    if (strpos($plugin, 'has_shortcode') === false) {
        $failures[] = 'Tools plugin assets are not conditionally loaded by shortcode presence.';
    }
    if (strpos($plugin, "is_page( 'tools' )") === false || strpos($plugin, 'is_front_page()') === false) {
        $failures[] = 'Tools plugin conditional assets do not cover template-rendered ticket tools.';
    }
    if (strpos($plugin, 'solo_to_china_ticket_tool') === false) {
        $failures[] = 'Tools plugin does not register the solo_to_china_ticket_tool shortcode boundary.';
    }
    if (strpos($pluginSource, 'data-stc-ticket-tool') === false) {
        $failures[] = 'Ticket tool markup is missing the frontend behavior hook.';
    }
    if (strpos($pluginSource, 'booking_lead_days') === false) {
        $failures[] = 'Attraction ticket data is missing booking lead day metadata.';
    }
    foreach ([
        'West Lake',
        'Shanghai Disney Resort',
        'Summer Palace',
        'Chengdu Research Base of Giant Panda Breeding',
        'Temple of Heaven',
        'The Bund',
        'Longmen Grottoes',
        'Mogao Caves',
        'Leshan Giant Buddha',
        'Huangshan Scenic Area',
        'Jiuzhaigou Valley',
        'Li River Cruise',
    ] as $attractionName) {
        if (strpos($attractions, $attractionName) === false) {
            $failures[] = "Attraction ticket data is missing planned coverage for: {$attractionName}";
        }
    }
    if (strpos($pluginSource, 'data-stc-save-reminder') === false) {
        $failures[] = 'Ticket tool markup is missing the guest reminder save action.';
    }
    if (strpos($pluginSource, 'data-stc-reminder-list') === false) {
        $failures[] = 'Ticket tool markup is missing the local saved reminders list.';
    }
    if (strpos($pluginSource, 'data-stc-export-reminders') === false) {
        $failures[] = 'Ticket tool markup is missing the saved reminders export action.';
    }
    if (strpos($pluginSource, 'data-stc-clear-reminders') === false) {
        $failures[] = 'Ticket tool markup is missing the saved reminders clear action.';
    }
    if (strpos($pluginSource, 'data-stc-import-reminders') === false) {
        $failures[] = 'Ticket tool markup is missing the saved reminders import action.';
    }
    if (strpos($pluginSource, 'Saved reminders stay in this browser') === false) {
        $failures[] = 'Ticket tool markup is missing local-only reminder privacy copy.';
    }
    if (strpos($shortcodes, 'name="stc_visit_date" required') === false) {
        $failures[] = 'Ticket tool visit date input is not required.';
    }
    if (strpos($shortcodes, 'stc_tools_group_attractions_by_city') === false) {
        $failures[] = 'Ticket tool shortcode does not group attractions by city.';
    }
    if (strpos($shortcodes, '<optgroup') === false) {
        $failures[] = 'Ticket tool attraction select does not render city optgroups.';
    }
}

$pluginJsPath = $root . DIRECTORY_SEPARATOR . 'wp-content/plugins/solo-to-china-tools/assets/js/tools.js';
if (is_file($pluginJsPath)) {
    $pluginJs = file_get_contents($pluginJsPath);
    if (strpos($pluginJs, 'stcTicketTool') === false) {
        $failures[] = 'Ticket tool JavaScript is missing the submit handler boundary.';
    }
    if (strpos($pluginJs, 'localStorage') === false) {
        $failures[] = 'Ticket reminder JavaScript does not persist reminders locally.';
    }
    if (strpos($pluginJs, 'stcRenderReminders') === false) {
        $failures[] = 'Ticket reminder JavaScript is missing the saved reminders renderer.';
    }
    if (strpos($pluginJs, 'data-stc-delete-reminder') === false) {
        $failures[] = 'Ticket reminder JavaScript is missing the delete reminder action.';
    }
    if (strpos($pluginJs, 'data-stc-export-reminders') === false) {
        $failures[] = 'Ticket reminder JavaScript is missing the reminders export binding.';
    }
    if (strpos($pluginJs, 'data-stc-clear-reminders') === false) {
        $failures[] = 'Ticket reminder JavaScript is missing the reminders clear binding.';
    }
    if (strpos($pluginJs, 'stcExportReminders') === false) {
        $failures[] = 'Ticket reminder JavaScript is missing the reminders JSON exporter.';
    }
    if (strpos($pluginJs, 'data-stc-import-reminders') === false) {
        $failures[] = 'Ticket reminder JavaScript is missing the reminders import binding.';
    }
    if (strpos($pluginJs, 'stcImportReminders') === false) {
        $failures[] = 'Ticket reminder JavaScript is missing the reminders JSON importer.';
    }
    if (strpos($pluginJs, 'stcClampText') === false) {
        $failures[] = 'Ticket reminder JavaScript does not clamp imported reminder text.';
    }
    if (strpos($pluginJs, 'stcDateValue') === false) {
        $failures[] = 'Ticket reminder JavaScript does not validate imported reminder dates.';
    }
    if (strpos($pluginJs, 'data-stc-download-calendar') === false) {
        $failures[] = 'Ticket reminder JavaScript is missing the calendar download action.';
    }
    if (strpos($pluginJs, 'stcDownloadCalendar') === false) {
        $failures[] = 'Ticket reminder JavaScript is missing the ICS calendar exporter.';
    }
    if (strpos($pluginJs, 'text/calendar') === false) {
        $failures[] = 'Ticket reminder JavaScript does not create a calendar file download.';
    }
    if (strpos($pluginJs, 'stcBookingWindowStatus') === false) {
        $failures[] = 'Ticket tool JavaScript is missing booking window status logic.';
    }
    foreach (['Book now', 'Set reminder', 'Date has passed'] as $statusLabel) {
        if (strpos($pluginJs, $statusLabel) === false) {
            $failures[] = "Ticket tool JavaScript is missing booking window label: {$statusLabel}";
        }
    }
    if (strpos($pluginJs, "plan.bookingStatus === 'passed'") === false) {
        $failures[] = 'Ticket reminder JavaScript allows saving reminders for past visit dates.';
    }
}

$pluginCssPath = $root . DIRECTORY_SEPARATOR . 'wp-content/plugins/solo-to-china-tools/assets/css/tools.css';
if (is_file($pluginCssPath)) {
    $pluginCss = file_get_contents($pluginCssPath);
    if (strpos($pluginCss, '.stc-reminder-list') === false) {
        $failures[] = 'Ticket tool CSS is missing saved reminder list styling.';
    }
    if (strpos($pluginCss, '.stc-reminder-actions') === false) {
        $failures[] = 'Ticket tool CSS is missing reminder action button styling.';
    }
    if (strpos($pluginCss, '.stc-reminder-list__actions') === false) {
        $failures[] = 'Ticket tool CSS is missing saved reminder list action styling.';
    }
    if (strpos($pluginCss, '.stc-tool-local-note') === false) {
        $failures[] = 'Ticket tool CSS is missing local-only reminder note styling.';
    }
    if (strpos($pluginCss, '.stc-ticket-status') === false) {
        $failures[] = 'Ticket tool CSS is missing booking window status styling.';
    }
    if (strpos($pluginCss, ':focus-visible') === false) {
        $failures[] = 'Ticket tool CSS is missing keyboard focus styling.';
    }
}

$frontPagePath = $root . DIRECTORY_SEPARATOR . 'wp-content/themes/solo-to-china/front-page.php';
if (is_file($frontPagePath)) {
    $frontPage = file_get_contents($frontPagePath);
    if (strpos($frontPage, 'stc_render_guide_card_media') === false) {
        $failures[] = 'Homepage image cards are missing a dedicated media layer.';
    }
    if (strpos($frontPage, 'stc_render_survival_icon') === false) {
        $failures[] = 'Survival Kit cards are still missing real icon rendering.';
    }
    foreach (['Payment', 'Apps', 'eSIM', 'Visa', 'VPN'] as $survivalLabel) {
        if (strpos($frontPage, "'title' => '{$survivalLabel}'") === false) {
            $failures[] = "Homepage Survival Kit is missing compact label: {$survivalLabel}";
        }
    }
    foreach (['Cards & mobile payments', 'Essential Apps', 'Stay connected anywhere.', 'VPN / Internet'] as $removedSurvivalCopy) {
        if (strpos($frontPage, $removedSurvivalCopy) !== false) {
            $failures[] = "Homepage Survival Kit still includes superseded copy: {$removedSurvivalCopy}";
        }
    }
    if (strpos($frontPage, 'data-stc-save-guide') !== false || strpos($frontPage, 'stc-save-guide--image-card') !== false) {
        $failures[] = 'Homepage cards still expose save actions before the guide is opened.';
    }
    if (strpos($frontPage, 'stc_render_faq_chevron') === false || strpos($frontPage, 'stc-faq__answer') === false) {
        $failures[] = 'Homepage FAQ is missing the shared SVG chevron or answer wrapper.';
    }
    if (strpos($frontPage, 'stc_render_home_latest_guides') !== false) {
        $failures[] = 'Homepage still inserts Latest Guides outside the approved reference layout.';
    }
    foreach (['stc-survival', 'stc-card-grid--cities', 'stc-card-grid--attractions', 'stc-planner', 'stc-ticket-band', 'stc-faq'] as $homepageToken) {
        if (strpos($frontPage, $homepageToken) === false) {
            $failures[] = "Homepage is missing approved reference section: {$homepageToken}";
        }
    }
    $homepageOrder = ['stc-survival', 'stc-card-grid--cities', 'stc-card-grid--attractions', 'stc-planner', 'stc-ticket-band', 'stc-faq'];
    $previousHomepageIndex = -1;
    foreach ($homepageOrder as $homepageToken) {
        $homepageIndex = strpos($frontPage, $homepageToken);
        if ($homepageIndex === false || $homepageIndex <= $previousHomepageIndex) {
            $failures[] = "Homepage reference sections are not in the approved order near: {$homepageToken}";
        }
        $previousHomepageIndex = $homepageIndex === false ? $previousHomepageIndex : $homepageIndex;
    }
    foreach (['stc-planner__icon', 'stc-ticket-band__icon', 'stc-ticket-band__step-icon', 'stc-guide-grid-shell', 'data-stc-guide-grid-shell', 'data-stc-guide-grid', 'data-stc-guide-reveal', 'stc-guide-grid-reveal__chevron'] as $homepageMarkupToken) {
        if (strpos($frontPage, $homepageMarkupToken) === false) {
            $failures[] = "Homepage is missing responsive reference markup: {$homepageMarkupToken}";
        }
    }
    if (strpos($frontPage, 'Start Exploring') === false || strpos($frontPage, 'Start your China journey') !== false) {
        $failures[] = 'Homepage Hero CTA does not use the refined Start Exploring copy.';
    }
    if (substr_count($frontPage, 'class="stc-section__view-all"') !== 2 || strpos($frontPage, '&rsaquo;') === false) {
        $failures[] = 'Homepage guide sections are missing the unified View all arrow links.';
    }
    if (strpos($frontPage, 'data-stc-collapsible-grid') !== false || strpos($frontPage, 'data-stc-grid-toggle') !== false) {
        $failures[] = 'Homepage still includes the removed vertical city-card expansion control.';
    }
    if (strpos($frontPage, 'https://www.trip.com/') === false || strpos($frontPage, 'rel="sponsored noopener"') === false) {
        $failures[] = 'Homepage Planner CTA is not a sponsored Trip.com external link.';
    }
    foreach (['Plan Your Trip', 'Book hotels, trains &amp; flights with confidence.', 'Explore on Trip.com', 'Ticket Date &amp; Availability', 'Check booking windows &amp; set free alerts before your visit.', 'Real-time Dates', 'Free Alerts', 'Check Dates &amp; Set Alerts', 'Free to use', 'No login required'] as $toolCopy) {
        if (strpos($frontPage, $toolCopy) === false) {
            $failures[] = "Homepage tool cards are missing refined copy: {$toolCopy}";
        }
    }
    foreach (['Start planning on', 'Build your itinerary and book with confidence.', 'Ticket Tool / Reminder', 'See availability and important notes.', 'Get notified before your visit.', 'Check ticket date / Set reminder'] as $removedToolCopy) {
        if (strpos($frontPage, $removedToolCopy) !== false) {
            $failures[] = "Homepage tool cards still include superseded copy: {$removedToolCopy}";
        }
    }
}

$themeCssPath = $root . DIRECTORY_SEPARATOR . 'wp-content/themes/solo-to-china/assets/css/main.css';
if (is_file($themeCssPath)) {
    $themeCss = file_get_contents($themeCssPath);
    if (strpos($themeCss, '../images/hero-home.png') === false) {
        $failures[] = 'Homepage hero does not reference the generated hero image asset.';
    }
    foreach (['planner-art.png', 'ticket-art.png'] as $imageReference) {
        if (strpos($themeCss, $imageReference) === false) {
            $failures[] = "Theme CSS is missing reference-style visual asset: {$imageReference}";
        }
    }
    foreach (['.stc-image-card__media img', 'object-fit: cover', 'object-position: center', '.stc-image-card__media::after', 'aspect-ratio: 3 / 4'] as $guideImageStyle) {
        if (strpos($themeCss, $guideImageStyle) === false) {
            $failures[] = "Theme CSS is missing high-resolution guide image behavior: {$guideImageStyle}";
        }
    }
    if (strpos($themeCss, 'var(--stc-card-image') !== false || strpos($themeCss, '.stc-image-card:hover .stc-image-card__media {') !== false) {
        $failures[] = 'Guide card media still uses the old combined background or whole-layer zoom.';
    }
    foreach (['.stc-affiliate-disclosure', 'color: #8c9ba5', 'font-size: 11px', '.stc-ticket-band__trust', 'min-height: 44px', 'opacity: .08'] as $toolCardStyle) {
        if (strpos($themeCss, $toolCardStyle) === false) {
            $failures[] = "Theme CSS is missing refined tool-card styling: {$toolCardStyle}";
        }
    }
    foreach (['.home .stc-header', '.stc-header', 'box-shadow', '.stc-page-hero--visual', '.stc-planner__icon', '.stc-ticket-band__icon', 'scroll-snap-type'] as $styleToken) {
        if (strpos($themeCss, $styleToken) === false) {
            $failures[] = "Theme CSS is missing selected homepage visual style token: {$styleToken}";
        }
    }
    foreach (['.stc-footer__inner', '.stc-footer__socials', '.stc-footer__bottom'] as $footerStyleToken) {
        if (strpos($themeCss, $footerStyleToken) === false) {
            $failures[] = "Theme CSS is missing selected homepage-reference footer style token: {$footerStyleToken}";
        }
    }
    foreach (['.stc-faq__chevron', 'border-bottom: 1px solid #e8ece9', 'font-weight: 600', 'color: #4a5568', 'line-height: 1.6', 'transform: rotate(180deg)'] as $faqRefactorToken) {
        if (strpos($themeCss, $faqRefactorToken) === false) {
            $failures[] = "Theme CSS is missing refined FAQ accordion styling: {$faqRefactorToken}";
        }
    }
    if (strpos($themeCss, '.stc-faq summary::after') !== false) {
        $failures[] = 'FAQ still uses the old text pseudo-element icon instead of the SVG chevron.';
    }
    foreach (['background: #0d1714', 'color: #9eb0a7', 'color: #8a9c94', 'background: rgba(255, 255, 255, .08)', 'height: 36px', '.stc-footer__legal'] as $modernFooterToken) {
        if (strpos($themeCss, $modernFooterToken) === false) {
            $failures[] = "Theme CSS is missing modern footer styling: {$modernFooterToken}";
        }
    }
    if (strpos($themeCss, 'rgba(0, 0, 0, .72)') !== false) {
        $failures[] = 'Homepage hero still uses the old heavy left-side black overlay.';
    }
    if (strpos($themeCss, '.stc-header.is-menu-open') === false) {
        $failures[] = 'Theme CSS is missing the mobile navigation open state.';
    }
    foreach (['.stc-saved-guides', '.stc-save-guide', '.stc-article-save'] as $removedGuideSaveStyle) {
        if (strpos($themeCss, $removedGuideSaveStyle) !== false) {
            $failures[] = 'Theme CSS still includes removed guide-saving presentation: ' . $removedGuideSaveStyle;
        }
    }
    if (strpos($themeCss, '.stc-page-actions') === false) {
        $failures[] = 'Theme CSS is missing page sharing action styling.';
    }
    if (strpos($themeCss, '.stc-feature-panel--gold .stc-ticket-tool') === false) {
        $failures[] = 'Theme CSS is missing Tools page ticket tool layout containment.';
    }
    if (strpos($themeCss, ':focus-visible') === false) {
        $failures[] = 'Theme CSS is missing keyboard focus styling.';
    }
    if (strpos($themeCss, '.stc-skip-link') === false) {
        $failures[] = 'Theme CSS is missing skip-link styling.';
    }
    if (strpos($themeCss, '.search-form') === false) {
        $failures[] = 'Theme CSS is missing WordPress search form styling.';
    }
    if (strpos($themeCss, '.search-submit') === false || strpos($themeCss, 'max-width: none') === false) {
        $failures[] = 'Theme CSS is missing mobile search form stacking.';
    }
    if (strpos($themeCss, '.stc-post-card__meta') === false) {
        $failures[] = 'Theme CSS is missing Guide card metadata styling.';
    }
    if (strpos($themeCss, '.stc-post-card__type') === false) {
        $failures[] = 'Theme CSS is missing Guide card type badge styling.';
    }
    if (strpos($themeCss, '.stc-post-card__cta') === false) {
        $failures[] = 'Theme CSS is missing Guide card CTA styling.';
    }
    if (strpos($themeCss, '.stc-latest-guides') === false) {
        $failures[] = 'Theme CSS is missing core page latest guides styling.';
    }
    if (strpos($themeCss, '.stc-home-latest') !== false) {
        $failures[] = 'Theme CSS still includes the removed homepage latest guides section.';
    }
    if (strpos($themeCss, '.stc-guide-toc') === false) {
        $failures[] = 'Theme CSS is missing Guide table of contents styling.';
    }
    foreach (['.stc-page-primary .stc-card-grid--attractions', '.stc-guide-toc--mobile', '.stc-guide-toc--desktop'] as $secondaryPageStyleToken) {
        if (strpos($themeCss, $secondaryPageStyleToken) === false) {
            $failures[] = "Theme CSS is missing secondary-page responsive style: {$secondaryPageStyleToken}";
        }
    }
    foreach (['.stc-survival-card::after', '.stc-share__trigger', '.stc-guide-grid-shell', '.stc-guide-grid-reveal', 'grid-template-columns: repeat(2', 'max-height: var(--stc-guide-collapsed-height)', 'max-height: var(--stc-guide-expanded-height)', 'backdrop-filter: blur'] as $mobileGridStyleToken) {
        if (strpos($themeCss, $mobileGridStyleToken) === false) {
            $failures[] = "Theme CSS is missing requested four-card fold or page-utility style: {$mobileGridStyleToken}";
        }
    }
    foreach (['.stc-section__view-all', 'linear-gradient(to top, rgba(0, 0, 0, .8) 0%, rgba(0, 0, 0, .35) 40%, transparent 100%)', 'background: rgba(0, 0, 0, .45)', '-webkit-backdrop-filter: blur(8px)', 'border: 1px solid rgba(255, 255, 255, .18)', 'aspect-ratio: 3 / 4', 'border-radius: 14px', 'gap: 12px', 'color: rgba(255, 255, 255, .85)', 'font-size: 12px', 'text-shadow: 0 2px 8px rgba(0, 0, 0, .65)', 'box-shadow: 0 2px 8px rgba(0, 0, 0, .06)', 'transform: scale(.96)'] as $refinedGuideCardToken) {
        if (strpos($themeCss, $refinedGuideCardToken) === false) {
            $failures[] = "Theme CSS is missing refined mobile guide-card styling: {$refinedGuideCardToken}";
        }
    }
    foreach (['height: 75vh', 'min-height: 480px', 'max-height: 580px', 'font-size: 32px', 'line-height: 1.15', 'font-size: 13px', 'color: rgba(255, 255, 255, .9)', 'margin-bottom: 20px', 'linear-gradient(to top, rgba(0, 0, 0, .7) 0%, rgba(0, 0, 0, .3) 50%, rgba(0, 0, 0, .1) 100%)', 'background: #c84832', 'height: 46px', 'border-radius: 10px', 'box-shadow: 0 4px 16px rgba(0, 0, 0, .18)', 'transform: scale(.97)', 'background: rgba(255, 255, 255, .15)', 'border: 1px solid rgba(255, 255, 255, .25)'] as $mobileHeroToken) {
        if (strpos($themeCss, $mobileHeroToken) === false) {
            $failures[] = "Theme CSS is missing refined mobile Hero styling: {$mobileHeroToken}";
        }
    }
    foreach (['.stc-planner__intro p', '.stc-ticket-band__intro p', '.stc-ticket-band__steps strong', '.stc-ticket-band__action p'] as $mobileToolTypographyToken) {
        if (strpos($themeCss, $mobileToolTypographyToken) === false) {
            $failures[] = "Theme CSS is missing mobile tool typography tuning: {$mobileToolTypographyToken}";
        }
    }
    foreach (['.home .stc-survival__grid', 'grid-template-columns: repeat(5, minmax(0, 1fr))', 'gap: 4px', 'padding: 16px 8px', 'background: rgba(20, 83, 45, .08)', 'color: #14532d', 'font-size: 11px', 'font-weight: 500', 'text-overflow: ellipsis', 'transform: scale(.95)'] as $survivalResponsiveToken) {
        if (strpos($themeCss, $survivalResponsiveToken) === false) {
            $failures[] = "Theme CSS is missing five-column Survival Kit behavior: {$survivalResponsiveToken}";
        }
    }
    foreach (['.home .stc-survival-card span:last-child', '.home .stc-survival-card:nth-child(even)::after', '.home .stc-survival-card:last-child'] as $removedSurvivalStyle) {
        if (strpos($themeCss, $removedSurvivalStyle) !== false) {
            $failures[] = "Theme CSS still includes obsolete Survival Kit layout styling: {$removedSurvivalStyle}";
        }
    }
    foreach (['grid-auto-columns: 75vw', 'grid-auto-columns: 42vw', '.stc-city-grid-shell', '.stc-city-grid-reveal'] as $removedGuideRailStyle) {
        if (strpos($themeCss, $removedGuideRailStyle) !== false) {
            $failures[] = "Theme CSS still includes a removed guide rail or gradient-overlay style: {$removedGuideRailStyle}";
        }
    }
    foreach (['.stc-grid-toggle', '.stc-save-guide--image-card', '.is-collapsible'] as $removedCardControlStyle) {
        if (strpos($themeCss, $removedCardControlStyle) !== false) {
            $failures[] = "Theme CSS still includes removed card control style: {$removedCardControlStyle}";
        }
    }
    foreach (['.stc-planner--page', '.stc-faq--page', '.stc-faq__chevron', '.stc-faq__answer-link'] as $utilityPageStyleToken) {
        if (strpos($themeCss, $utilityPageStyleToken) === false) {
            $failures[] = "Theme CSS is missing utility-page responsive style: {$utilityPageStyleToken}";
        }
    }
    foreach (['.stc-guide-quick-facts', '.stc-guide-fact', '.stc-guide-warning', '.stc-guide-route'] as $guideClass) {
        if (strpos($themeCss, $guideClass) === false) {
            $failures[] = "Theme CSS is missing structured Attraction Guide content styling: {$guideClass}";
        }
    }
    foreach (['.stc-article-hero', '.stc-article-layout--with-toc', '.stc-article-sidebar', '.stc-share__panel'] as $genericArticleStyle) {
        if (strpos($themeCss, $genericArticleStyle) === false) {
            $failures[] = 'Theme CSS is missing generic article or ShareThisPage styling: ' . $genericArticleStyle;
        }
    }
}

$themeJsPath = $root . DIRECTORY_SEPARATOR . 'wp-content/themes/solo-to-china/assets/js/main.js';
if (is_file($themeJsPath)) {
    $themeJs = file_get_contents($themeJsPath);
    if (strpos($themeJs, 'stcMobileNav') === false) {
        $failures[] = 'Theme JavaScript is missing the mobile navigation controller.';
    }
    foreach (['stcSavedGuides', 'data-stc-delete-guide', 'data-stc-save-guide', 'data-stc-saved-guides', 'data-stc-export-guides', 'data-stc-import-guides', 'data-stc-clear-guides', 'window.localStorage'] as $removedGuideSaveScript) {
        if (strpos($themeJs, $removedGuideSaveScript) !== false) {
            $failures[] = 'Theme JavaScript still contains removed guide-saving behavior: ' . $removedGuideSaveScript;
        }
    }
    if (strpos($themeJs, 'stcGuideToc') === false) {
        $failures[] = 'Theme JavaScript is missing the automatic Guide table of contents controller.';
    }
    if (strpos($themeJs, 'data-stc-guide-toc-list') === false) {
        $failures[] = 'Theme JavaScript is missing the Guide table of contents list target.';
    }
    if (strpos($themeJs, "querySelectorAll('[data-stc-guide-toc]')") === false) {
        $failures[] = 'Theme JavaScript does not populate both desktop and mobile Guide tables of contents.';
    }
    foreach (['navigator.share', 'navigator.clipboard', 'data-stc-share-trigger', 'data-stc-share-panel', 'data-stc-share-copy', 'data-stc-share-close', 'AbortError', 'Link copied', 'Escape'] as $shareScriptToken) {
        if (strpos($themeJs, $shareScriptToken) === false) {
            $failures[] = 'Theme JavaScript is missing accessible ShareThisPage behavior: ' . $shareScriptToken;
        }
    }
}

$phpFiles = [];
$phpExecutable = PHP_BINARY ?: 'php';
foreach ($requiredFiles as $relativePath) {
    if (substr($relativePath, -4) === '.php') {
        $phpFiles[] = $relativePath;
    }
}

foreach (array_filter($requiredFiles, static fn ($path) => substr($path, -8) === '-hd.webp') as $relativePath) {
    $absolutePath = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
    if (!is_file($absolutePath)) {
        continue;
    }

    $dimensions = getimagesize($absolutePath);
    if ($dimensions === false || $dimensions[0] < 960 || $dimensions[1] < 1200) {
        $failures[] = "Retina guide image must be at least 960x1200: {$relativePath}";
    }
}

foreach ($phpFiles as $relativePath) {
    $absolutePath = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
    if (!is_file($absolutePath)) {
        continue;
    }

    $command = escapeshellarg($phpExecutable) . ' -l ' . escapeshellarg($absolutePath);
    exec($command, $output, $exitCode);
    if ($exitCode !== 0) {
        $failures[] = "PHP syntax check failed: {$relativePath}";
    }
}

if ($failures) {
    fwrite(STDERR, implode(PHP_EOL, $failures) . PHP_EOL);
    exit(1);
}

echo 'SoloToChina project verification passed.' . PHP_EOL;
