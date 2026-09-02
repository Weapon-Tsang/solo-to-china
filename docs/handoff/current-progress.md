# SoloToChina Current Progress Handoff

Date: 2026-09-02

## Current Working Boundary

The project-owned WordPress code lives in:

- `wp-content/themes/solo-to-china/`
- `wp-content/themes/solo-to-china-child/`
- `wp-content/plugins/solo-to-china-tools/`

Do not edit third-party themes, cache plugins, uploads, language files, database files, or `wp-config.php`.

## Product Constraints To Preserve

- Guest-first
- Content-first
- Mobile-first
- Utility-driven
- Low friction
- High trust

The fixed top-level IA remains:

- Home
- Survival Kit
- City Guides
- Attraction Guides
- Planner
- Tools
- FAQ

Do not add top-level Hotels, Tickets, Flights, Trains, Book, or similar transaction-first nav items.

Affiliate links remain a restrained transaction layer behind content and tools. Trip.com appears in Planner and homepage planner CTA, but should not become the visual protagonist.

## Theme Status

Current theme version: `0.24.0`.

Content Contract Phase A is complete:

- Added the canonical `content-contract/content-contract.v1.json` at Contract version `1.0.0`, independent from the Theme version.
- Defined four stable guide types, category mappings, shell behaviors, component allowlists, and optional dynamic capabilities.
- Defined 16 machine-readable core, editorial, image, and contextual component capabilities without exposing CSS values or internal research provenance.
- Added the public read-only `GET /wp-json/stc/v1/content-contract` endpoint with ETag, Last-Modified, and Cache-Control headers.
- Registered `_stc_guide_type` and `_stc_content_contract_version` with REST schemas, allowlist sanitization, and authenticated edit checks.
- Explicit guide metadata now takes precedence over existing category/tag fallback; historical posts remain compatible.
- Added `scripts/verify-content-contract.ps1` and integrated Contract validation into the primary project verifier.
- Real Playground REST checks returned Contract `1.0.0`, Theme `0.22.0`, four guide types, 16 components, both REST meta fields, and the expected cache headers.
- Existing article shell regression checks passed at 1440, 768, 390, and 375 with one H1, no horizontal overflow, and no console errors/warnings.

The custom theme implements the approved image-led homepage direction:

- Transparent header over the hero image.
- Non-home header uses the same brand/navigation language with readable dark text on a white surface.
- Reduced hero copy density.
- Mobile Hero uses bounded 75vh framing (480-580px), bottom-positioned 32px copy over a smooth dark scrim, a 46px vermilion CTA, and a glass menu button so Survival Kit begins within the first viewport.
- Survival Kit strip.
- City Guides and Attraction Guides image cards.
- Homepage section order is locked to the approved reference: Hero, Survival Kit, City Guides, Attraction Guides, Planner, Ticket Date & Availability, FAQ, and Footer.
- The homepage no longer inserts Latest Guides between Attraction Guides and Planner; current posts remain available on their matching landing pages and archives.
- Mobile Survival Kit is a single-row, five-column app shortcut strip with compact labels, full-item links, 40px icon badges, and touch feedback from 360px upward.
- City Guides and Attraction Guides share a two-column mobile grid that displays four complete cards before a compact frosted capsule button placed below the second row.
- Each guide-grid button reports the remaining item count, moves focus to the first newly revealed card, and fades away as the remaining cards expand smoothly.
- Survival Kit and city subtitles stay on one line, Survival Kit dividers are centered at 60% height, and attraction badges use smaller type.
- Guide-list cards do not show save controls; the local Save Guide action appears only after a guide article is opened.
- Homepage and landing guide cards now use distinct 960x1200 WebP assets with centered `object-fit: cover`, smooth contrast scrims, and 3:4 mobile framing.
- Mobile City and Attraction cards share 14px corners, 12px grid gaps, 12px subtitles, glass attraction badges, compact green View all links, and tactile reveal buttons.
- WordPress featured guide cards request the responsive 960px `stc-guide-card-2x` size with `srcset`/`sizes` instead of low-resolution thumbnail presets.
- Core landing pages and guide article heroes now inherit the selected homepage visual style.
- Core landing pages render their primary guide content before Saved Guides and category-matched latest posts.
- Attraction Guides preserves full title, subtitle, and badge space in the same four-card mobile fold used by City Guides.
- Mobile Planner and Ticket cards use concise copy, 20-24px padding, 16px module gaps, 44px CTAs, simplified one-line features, and restrained 11px disclosures.
- Planner reuses the approved homepage calendar icon, Trip.com disclosure block, and watercolor artwork on desktop and mobile.
- FAQ uses polished two-column desktop and one-column mobile accordions with related internal links.
- FAQ now uses borderless single-line dividers, 16px medium-weight titles, 18px rotating SVG chevrons, and 1.6-line-height answers.
- Footer now uses solid deep ink, higher-contrast text, 36px social icon circles, two-column mobile navigation, and a centered legal/copyright area.
- Saved Guides and category-matched latest posts are limited to Survival Kit, City Guides, and Attraction Guides; Planner, Tools, and FAQ stay focused on their primary tasks.
- Planner band.
- Homepage Planner CTA opens Trip.com as a sponsored external link.
- Ticket Date & Availability band.
- FAQ section.
- Footer.
- Keyboard focus styling and skip-to-content link.
- Basic single article template for future guide posts.
- Attraction Guide article layout for scenic spot strategy pages.
- Attraction Guide editor content pattern covering best time, transport, ticket prices, opening and booking timing, where to stay, and common mistakes.
- Attraction Guide editor content pattern now includes structured quick facts, reservation window, passport note, best base area, before-booking warning, and suggested route modules.
- City Guide article layout for city strategy pages.
- City Guide editor content pattern covering where to stay, getting around, first-time itineraries, food, neighborhoods, day trips, and common city mistakes.
- Survival Kit article layout for practical setup and troubleshooting pages.
- Survival Kit editor content pattern covering quick answers, pre-arrival setup, step-by-step setup, failure cases, backup plans, and FAQ.
- Guide articles automatically show an On this page table of contents generated from H2 sections: a compact horizontal navigator before content on mobile and the existing sidebar navigator on desktop.
- Shared Guide card rendering for archive, search, and default post lists, with guide type badges and consistent CTAs.
- Survival Kit, City Guides, and Attraction Guides landing pages automatically show latest published posts from their matching categories.
- Core guide categories are created on theme activation if missing.
- Basic 404 template with links back to core travel sections.
- Search results template without adding Search to top-level navigation.
- Custom search form template for controlled search label and button copy.
- Styled default WordPress search form for search fallback states.
- Theme and plugin README files updated to match current version and responsibility boundaries.
- WordPress title tag, featured images, wide alignment, HTML5 markup support, and automatic feed links enabled.

Core IA pages are created on theme activation if missing:

- Survival Kit
- City Guides
- Attraction Guides
- Planner
- Tools
- FAQ

Core guide categories are created on theme activation if missing:

- Survival Kit
- City Guides
- Attraction Guides

Core guide pages support local Saved Guides:

- Share the current page without login, using native share where available and link-copy fallback.
- Save an opened guide article without login.
- View saved guides on the current device.
- Export saved guides to `solotochina-saved-guides.json`.
- Import saved guides from the exported JSON format.
- Clear saved guides from the current device.
- Show local-only saved-data copy near the Saved Guides controls.
- Clamp imported saved-guide text before writing to browser storage.
- Validate imported saved-guide types before writing to browser storage.

Saved Guides use browser `localStorage` only. They do not write to WordPress users, custom tables, post meta, or remote services.

## Child Theme Status

Current Child Theme version: `0.6.0`.

Content Component System Phase B is complete:

- Parent Theme registers nine reusable Gutenberg component patterns: Quick Answer, Key Takeaways, Quick Facts, Tip, Warning, Steps, Checklist, Comparison Table, and FAQ.
- Patterns use core Group, Heading, Paragraph, List, Table, and Details blocks with stable semantic classes; they contain no Custom HTML blocks, inline styles, or fixed topic/article structures.
- Child Theme `assets/css/content-components.css` provides token-driven editorial styling, local narrow-screen table scrolling, native FAQ disclosure treatment, long-text safeguards, and responsive behavior at 840px and 599px.
- Child Theme assets remain ordered Parent -> Child base -> Design System -> Site -> Article -> Content Components.
- Playground fixtures moved from a giant inline Blueprint string to `scripts/playground-fixtures.php`, with three disposable Gutenberg articles and explicit guide type/Contract metadata.
- Real rendering checks passed at 1440, 768, 390, and 375: one H1, six representative components, zero page overflow, local table overflow at phone widths, 61px FAQ summary targets, no inline style attributes, and zero console errors/warnings.
- Verification now rejects missing semantic components, Custom HTML/inline-style patterns, raw colors in component CSS, incomplete fixture coverage, and a preview that omits the fixture mount.

Content Component System Phase C is complete:

- Parent Theme adds safe renderers for Planner CTA, Ticket Reminder, and Affiliate CTA, registered as the Contract shortcodes.
- Planner and Affiliate destinations must be absolute HTTPS URLs; output is escaped, externally opened links use `sponsored nofollow noopener`, and Affiliate CTA always includes visible relationship disclosure.
- Ticket Reminder accepts only a sanitized attraction slug and delegates the form to the Plugin shortcode. The Theme contains no attraction data, booking lead days, reminder calculation, storage, remote request, or scheduling logic.
- Plugin `0.22.0` validates optional `attraction_slug` context against Plugin-owned data and preselects the matching option; invalid slugs fall back to the ordinary first option.
- Plugin asset detection now covers posts containing the Theme-level Ticket Reminder shortcode, so the delegated form loads its existing CSS/JavaScript without Theme coupling.
- Child Theme `0.5.0` provides restrained dynamic-component layouts and contextual Ticket form containment using Design System tokens.
- Playwright confirmed the Plugin form is delegated exactly once, Forbidden City is preselected, controls retain 46px height, Planner/Affiliate links carry the required safety attributes and disclosures, and checked desktop/mobile pages have no overflow or console errors.

Content Component System Phase D is complete:

- Parent Theme now enables editor styles and provides an independent editor fallback; Child Theme `0.6.0` replaces it with a Design System/Content Component editor canvas.
- Server-side H2 anchor generation preserves explicit public anchors, adds readable IDs when absent, and suffixes duplicates before JavaScript enhancement.
- Child Theme adds responsive WordPress Media styling for context, evidence, illustration, and decorative roles without forcing image ratios or publishing internal provenance.
- Playground creates a real ephemeral Media attachment from a project image and stores Gutenberg-valid Core Image markup, rather than hardcoding an external image or Custom HTML block.
- Raw response checks confirmed five server-rendered unique H2 IDs and Media output with intrinsic 819x1024 dimensions, responsive candidates, `sizes`, `loading="lazy"`, `decoding="async"`, alt text, and caption.
- Playwright confirmed the browser selected a 768px candidate for a 670px desktop render and a 350px candidate for a 350px mobile render, with zero page overflow and zero console errors/warnings.

Content Component System Phase E is complete:

- Added `scripts/verify-content-runtime.ps1` for real WordPress integration checks against both the active Child Theme stack and the independently activated Parent Theme fallback.
- Added dedicated Parent-only and authenticated Gutenberg editor Playground blueprints plus `-ParentOnly` and `-Editor` preview modes.
- Runtime coverage protects Contract JSON/version/cache validators, guide mappings and required fields, REST metadata sanitization boundaries, historical category-only routing, semantic component classes, contextual renderer safety, Plugin-owned Ticket form delegation, responsive Media output, stable H2 anchors, and the absence of internal provenance or raw inline presentation.
- The Media fixture now stores valid native Core Image block markup. The Parent render filter adds lazy/async defaults without invalidating Gutenberg block serialization.
- Child editor assets are registered individually in Parent -> Child Design System -> Content Components -> editor order, so the iframe receives the real visual system without unresolved CSS imports.
- Real Gutenberg checks found 24 valid blocks and four component families in both Parent and Child editor modes, with no project block-validation errors. The only editor warning is a WordPress core global-styles iframe notice.
- Final frontend checks covered all three fixtures at 1440, 768, 390, and 375. Every viewport had one H1 and zero page overflow; mobile comparison tables scroll locally, FAQ/ticket controls retain 44px-class targets, Forbidden City is preselected through the Plugin, and reduced-motion/focus/keyboard behavior remains intact.

The first independent Child Theme foundation stage is complete:

- Added a standards-compliant `solo-to-china-child` with `Template: solo-to-china`.
- Preserved the Parent Theme as an independently installable, upgradable fallback.
- Added deterministic Parent -> Child -> design-system stylesheet loading without loading the Parent stylesheet twice.
- Added design tokens for color, typography, spacing, containers, grids, radii, shadows, buttons, forms, image ratios, breakpoints, motion, and focus states.
- Added reduced-motion and forced-colors safeguards.
- Recorded the KEEP / REFACTOR / REPLACE / REMOVE audit in `docs/audit/2026-08-31-child-theme-foundation.md`.
- No Parent template or Plugin business logic was copied into the Child Theme.

The shared-component and homepage stage is also complete:

- Added a small Child `header.php` override with `aria-current="page"` for Home, core pages, matching guide posts, and matching category archives.
- Added shared Header, navigation, menu, button, card, FAQ, and Footer styling in `assets/css/site.css`.
- Added homepage-specific responsive styling in `assets/css/home.css` without copying `front-page.php`.
- Preserved the image-led approved direction while improving the Hero overlay, two-line title, CTA hierarchy, section rhythm, 4-column City / 3-column Attraction desktop grids, and 2-column mobile guide folds.
- Improved mobile menu motion, label synchronization, Escape close, link close, and desktop-breakpoint cleanup in `assets/js/site.js`.
- Hid non-interactive placeholder social glyphs until real destinations exist, improving trust without inventing accounts.
- Added a real WordPress Playground preview (`scripts/start-preview.ps1`) that mounts Parent, Child, and Plugin; no static demo or production deployment is used.
- Playwright QA passed at 1440, 768, 390, and 375 widths with no horizontal overflow, no browser console errors/warnings, working menu/Escape behavior, current-page navigation semantics, guide reveal focus transfer, and FAQ expansion.
- Final screenshots are stored locally in ignored `output/playwright/`.

The Guide / Article template stage is complete:

- Added `assets/css/article.css`, loaded only on single posts after the shared Child assets.
- Renovated Attraction Guide, City Guide, and Survival Kit article heroes with distinct restrained accents and existing high-resolution destination imagery.
- Added a measured desktop reading column, sticky On this page sidebar, differentiated planning checklists, and editorial quick-fact, warning, and route modules.
- Added a horizontal mobile On this page navigator, edge-to-edge mobile article rhythm, single-column fact modules, 44px-class controls, and heading anchor offsets below the Header.
- Added semantic Home / Hub / Current Article breadcrumbs through a narrowly scoped Child `the_content` filter, avoiding a copied Parent `single.php` and avoiding duplicate SEO-plugin schema or metadata.
- Preserved Parent Theme TOC generation and local Save Guide logic; the Theme still owns presentation only and the Plugin remains unchanged.
- Added temporary Playground-only Attraction, City, and Survival Kit article fixtures to the preview Blueprint. They write only to the ephemeral preview database and are not production content or a static demo.
- Playwright QA passed for the Attraction article at 1440, 768, 390, and 375 widths; City and Survival Kit branches were also checked at 390. All checked pages had one H1, correct guide navigation state, correct Breadcrumb hub, no horizontal overflow, and zero browser console errors/warnings.
- Save Guide correctly reached `aria-pressed="true"`; mobile TOC navigation reached the target heading with the Header offset preserved.
- Child PHP remains compatible with the declared PHP 7.4 minimum.

## Plugin Status

Current tools plugin version: `0.22.0`.

The custom plugin owns the first tool only:

- Attraction Ticket Reservation & Reminder.

The shortcode is:

- `[solo_to_china_ticket_tool]`

Current Ticket Tool behavior:

- Select attraction.
- Attraction select is grouped by city for easier scanning on mobile.
- Select visit date.
- Calculate recommended ticket-check/reminder date from `booking_lead_days`.
- Static first-phase attraction data currently covers 18 attractions across Beijing, Shanghai, Xi'an, Zhangjiajie, Hangzhou, Chengdu, Guangzhou, Luoyang, Dunhuang, Leshan, Huangshan, Jiuzhaigou, and Guilin.
- Frontend assets load on the homepage, Tools page, or pages that contain the ticket tool shortcode.
- Show booking-window status:
  - `Book now`
  - `Set reminder`
  - `Date has passed`
- Prevent saving reminders for past visit dates.
- Save reminders locally without login.
- View saved reminders on the current device.
- Export saved reminders to `solotochina-ticket-reminders.json`.
- Import saved reminders from the exported JSON format.
- Clear saved reminders from the current device.
- Download an `.ics` calendar file for individual saved reminders.
- Delete individual reminders.
- Show local-only saved-data copy near the reminder controls.
- Clamp imported reminder text before writing to browser storage.
- Validate imported reminder dates before writing to browser storage.

Ticket reminders use browser `localStorage` only. They do not send email or SMS, and do not write to the database.

## Install Artifacts

Current generated install zips:

- `dist/solo-to-china-theme.zip`
- `dist/solo-to-china-child-theme.zip`
- `dist/solo-to-china-tools-plugin.zip`
- `dist/release-manifest.txt` with artifact versions and SHA256 hashes for all three zip files.

New chat handoff:

- `docs/handoff/new-chat-handoff.md`

Preferred install path:

1. WordPress Admin > Appearance > Themes > Add New > Upload Theme.
2. Upload `solo-to-china-theme.zip` and keep the Parent Theme installed.
3. Upload and activate `solo-to-china-child-theme.zip`.
4. WordPress Admin > Plugins > Add New > Upload Plugin.
5. Upload and activate `solo-to-china-tools-plugin.zip`.

aaPanel file install path if WordPress upload fails:

- Extract theme zip to `/www/wwwroot/solotochina.com/wp-content/themes/`
- Extract plugin zip to `/www/wwwroot/solotochina.com/wp-content/plugins/`

Final directories should be:

- `/www/wwwroot/solotochina.com/wp-content/themes/solo-to-china/`
- `/www/wwwroot/solotochina.com/wp-content/themes/solo-to-china-child/`
- `/www/wwwroot/solotochina.com/wp-content/plugins/solo-to-china-tools/`

## Verification

Primary local verification command:

```powershell
.\scripts\verify-project.ps1
```

Content system verification commands:

```powershell
.\scripts\verify-content-contract.ps1
.\scripts\verify-content-runtime.ps1 -BaseUrl http://127.0.0.1:9400
.\scripts\start-preview.ps1 -Port 9402 -ParentOnly
.\scripts\verify-content-runtime.ps1 -BaseUrl http://127.0.0.1:9402 -ParentOnly
.\scripts\start-preview.ps1 -Port 9403 -Editor
```

Packaging command:

```powershell
.\scripts\package-release.ps1
```

Current environment note:

- PHP CLI is installed locally in `.tools/php/`.
- The PowerShell verifier uses local PHP for syntax checks when a global `php` command is not available.
- Static project checks and PHP syntax checks pass before packaging.
- Browser preview checks protect homepage reference alignment, both four-card guide folds and expansion, responsive Survival Kit columns, mobile tool-band line heights, article-only save behavior, semantic breadcrumbs, article table-of-contents breakpoints, utility-page content scope, FAQ expansion, and horizontal overflow.

## GitHub / Source Control Status

Normal `git push origin main` works in the current clone.

For deployment, use the local zip artifacts as the source of truth.

## Next Safe Small Steps

Reasonable next bounded increments:

- Connect the external CMS to the public Contract endpoint and validate a non-production import round trip against the documented guide types.
- Populate representative real guide posts on staging and re-run the runtime/browser checks against real editorial content.
- Later renovate the City Guides / Attraction Guides / Survival Kit hub family while preserving content-first card behavior and responsive featured images.
- Improve article spacing and touch targets after installing the Child Theme on WordPress and checking real-content screenshots.
- Continue refining or expanding static attraction data inside the existing Ticket Tool only.
- Populate real guide posts in the matching categories and check landing-page/archive/search card density on mobile screenshots.

Do not start yet without a new design/spec:

- Accounts.
- Cross-device sync.
- Email/SMS reminder delivery.
- Custom database tables.
- Real ticket inventory checking.
- New top-level navigation.
- New tools beyond Attraction Ticket Reservation & Reminder.
