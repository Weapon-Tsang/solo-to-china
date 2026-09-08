# SoloToChina Current Progress Handoff

Date: 2026-09-09

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

Current theme version: `0.31.1`.

Content Contract Phase A is complete and has been superseded by the Contract 2.1 responsibility model documented below:

- The canonical contract is now `content-contract/content-contract.v2.json` at Contract version `2.1.0`, independent from the Theme version.
- Defined four stable guide types, category mappings, shell behaviors, component allowlists, and optional dynamic capabilities.
- Defined 21 ordered core, editorial, image, contextual, travel-tool, and commercial page-block capabilities without exposing CSS values or internal research provenance.
- Added the public read-only `GET /wp-json/stc/v1/content-contract` endpoint with ETag, Last-Modified, and Cache-Control headers.
- Registered `_stc_guide_type` and `_stc_content_contract_version` with REST schemas, allowlist sanitization, and authenticated edit checks.
- Explicit guide metadata now takes precedence over existing category/tag fallback; historical posts remain compatible.
- Added `scripts/verify-content-contract.ps1` and integrated Contract validation into the primary project verifier.
- Real Playground REST checks cover Contract `2.1.0`, Registry `1.2.0`, Theme `0.31.1`, four taxonomy-only guide types, 24 CMS capabilities, the generated Publish Package Schema, authenticated draft ingestion, exact-order serialization, idempotency, rejection cases, and cache headers.
- Existing article shell regression checks passed at 1440, 768, 390, and 375 with one H1, no horizontal overflow, and no console errors/warnings.

The custom theme implements the approved image-led homepage direction:

- Transparent header over the hero image.
- The homepage Header and Footer use the supplied SoloToChina artwork as a true-alpha white logo with the red seal and orange sun preserved; it blends into dark backgrounds without a card, intrinsic dimensions prevent layout shift, and the mobile menu remains isolated on the right.
- Non-home header uses the same brand/navigation language with readable dark text on a white surface.
- Reduced hero copy density.
- Mobile Hero uses bounded 75vh framing (480-580px), bottom-positioned 32px copy over a smooth dark scrim, a 46px vermilion CTA, and a glass menu button so Survival Kit begins within the first viewport.
- Survival Kit strip.
- City Guides and Attraction Guides image cards.
- Homepage section order is locked to the approved reference: Hero, Survival Kit, City Guides, Attraction Guides, Planner, Ticket Booking Window, FAQ, and Footer.
- The homepage no longer inserts Latest Guides between Attraction Guides and Planner; current posts remain available on their matching landing pages and archives.
- Mobile Survival Kit is a single-row, five-column app shortcut strip with compact labels, full-item links, 40px icon badges, and touch feedback from 360px upward.
- City Guides and Attraction Guides share a two-column mobile grid that displays four complete cards before a compact frosted capsule button placed below the second row.
- Each guide-grid button reports the remaining item count, instantly toggles cards five onward, and changes to Show fewer without measuring or animating container height, moving focus, or scrolling the page.
- Survival Kit and city subtitles stay on one line, Survival Kit dividers are centered at 60% height, and attraction badges use smaller type.
- Guide-list cards and articles do not expose guide-saving controls; Share This Page is the stateless article utility.
- Homepage and landing guide cards now use distinct 960x1200 WebP assets with centered `object-fit: cover`, smooth contrast scrims, and 3:4 mobile framing.
- Mobile City and Attraction cards share 14px corners, 12px grid gaps, 12px subtitles, glass attraction badges, compact green View all links, and tactile reveal buttons.
- WordPress featured guide cards request the responsive 960px `stc-guide-card-2x` size with `srcset`/`sizes` instead of low-resolution thumbnail presets.
- Core landing pages and guide article heroes now inherit the selected homepage visual style.
- Core landing pages render their primary guide content before category-matched latest posts.
- Attraction Guides preserves full title, subtitle, and badge space in the same four-card mobile fold used by City Guides.
- Mobile Planner and Ticket cards use concise copy, 20-24px padding, 16px module gaps, 44px CTAs, simplified one-line features, and restrained 11px disclosures.
- Planner reuses the approved homepage calendar icon, Trip.com disclosure block, and watercolor artwork on desktop and mobile.
- FAQ uses polished two-column desktop and one-column mobile accordions with related internal links.
- FAQ now uses borderless single-line dividers, 16px medium-weight titles, 18px rotating SVG chevrons, and 1.6-line-height answers.
- Footer now uses solid deep ink, real email and WhatsApp destinations, clear Explore/Tools/Help/About groups, two-column mobile navigation, and a complete legal area. Placeholder social icons were removed.
- Category-matched latest posts are limited to Survival Kit, City Guides, and Attraction Guides; Planner, Tools, and FAQ stay focused on their primary tasks.
- Planner band.
- Homepage and Planner-page CTAs use the shared `stc_get_trip_planner_url()` helper and open the Trip.Planner workspace directly at `https://www.trip.com/webapp/tripmap/tripplanner?source=seo_H5_homepage` as sponsored external links, avoiding the intermediate marketing page.
- Ticket Booking Window band.
- FAQ section.
- Footer.
- Keyboard focus styling and skip-to-content link.
- Basic single article template for future guide posts.
- One generic article layout renders City, Attraction, Survival Kit, and uncategorized guide content in its CMS-authored order.
- Reusable component patterns remain available to editors without imposing topic-wide article structures.
- Guide articles show an H2-derived On this page navigator only when the CMS explicitly enables `_stc_show_toc`.
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
- About SoloToChina
- Contact
- Privacy Policy
- Terms of Use
- Affiliate Disclosure
- Disclaimer

Theme `0.29.0` also runs this idempotent bootstrap through a versioned one-time admin migration for already-active upgrades. Repeated runs do not duplicate pages, existing page content/status is never overwritten, and an existing WordPress privacy-page setting is preserved. When an unpublished page already reserves a required slug, a read-only Theme fallback keeps the public URL available until the administrator publishes the page.

Core guide categories are created on theme activation if missing:

- Survival Kit
- City Guides
- Attraction Guides

Share This Page is available without login when the CMS explicitly enables `_stc_show_share`. Desktop/fine-pointer devices always use the branded popover; mobile/coarse-pointer devices try native Web Share first and use the branded bottom sheet on unsupported or failed calls. The overflow-safe sheet uses recognizable branded icons for WhatsApp, Facebook, Reddit, X, and Instagram, plus system sharing and Copy link. Instagram invokes native sharing where available; otherwise it copies the canonical link before opening Instagram. Clipboard fallback, Escape, outside-click, focus-return, and focus-loop behavior remain available without account or browser-storage state.

## Child Theme Status

Current Child Theme version: `0.10.1`.

Content Component System Phase B is complete:

- Parent Theme registers nine reusable Gutenberg component patterns: Quick Answer, Key Takeaways, Quick Facts, Tip, Warning, Steps, Checklist, Comparison Table, and FAQ.
- Patterns use core Group, Heading, Paragraph, List, Table, and Details blocks with stable semantic classes; they contain no Custom HTML blocks, inline styles, or fixed topic/article structures.
- Child Theme `assets/css/content-components.css` provides token-driven editorial styling, local narrow-screen table scrolling, native FAQ disclosure treatment, long-text safeguards, and responsive behavior at 840px and 599px.
- Child Theme assets remain ordered Parent -> Child base -> Design System -> Site -> Article -> Content Components.
- Playground fixtures moved from a giant inline Blueprint string to `scripts/playground-fixtures.php`, with three disposable Gutenberg articles and explicit guide type/Contract metadata.
- Real rendering checks passed at 1440, 768, 390, and 375: one H1, six representative components, zero page overflow, local table overflow at phone widths, 61px FAQ summary targets, no inline style attributes, and zero console errors/warnings.
- Verification now rejects missing semantic components, Custom HTML/inline-style patterns, raw colors in component CSS, incomplete fixture coverage, and a preview that omits the fixture mount.

Content Component System Phase C is complete:

- Parent Theme adds safe renderers for Planner CTA, the legacy Ticket Booking Window adapter, and Affiliate CTA, registered as the Contract shortcodes.
- Planner and Affiliate destinations must be absolute HTTPS URLs; output is escaped, externally opened links use `sponsored nofollow noopener`, and Affiliate CTA always includes visible relationship disclosure.
- The historical `ticket_reminder` ID accepts only a sanitized attraction slug and delegates the stateless Ticket Booking Window to the Plugin shortcode. The Theme contains no attraction data, booking lead days, or date calculation.
- Plugin `0.25.0` validates optional `attraction_slug` context against Plugin-owned data and preselects the matching option; invalid slugs fall back to the ordinary first option.
- Plugin asset detection covers posts containing the Theme-level compatibility shortcode, so the delegated form loads without Theme coupling.
- Child Theme `0.5.0` provides restrained dynamic-component layouts and contextual Ticket form containment using Design System tokens.
- Playwright confirmed the Plugin form is delegated exactly once, Forbidden City is preselected, controls retain 46px height, Planner/Affiliate links carry the required safety attributes and disclosures, and checked desktop/mobile pages have no overflow or console errors.

Content Component System Phase D is complete:

- Parent Theme now enables editor styles and provides an independent editor fallback; Child Theme `0.9.1` replaces it with a Design System/Content Component editor canvas.
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

Page Architecture Responsibility Refactor is complete:

- Contract `2.1.0` records the governing boundary: Frontend renders what the CMS requests; CMS decides what the page contains; content type is taxonomy, not layout.
- Parent Theme `single.php` is one generic article shell. It no longer branches by City, Attraction, or Survival type and no longer injects checklists, CTA modules, Share, or TOC based on taxonomy.
- REST-enabled `_stc_show_share`, `_stc_show_toc`, and `_stc_hero_variant` metadata explicitly control optional presentation. Missing flags render no optional utility.
- Topic-wide fixed Gutenberg article patterns were removed; reusable component patterns remain.
- The complete Save/Saved Guides and Ticket Reminder systems are removed, including localStorage, JSON import/export, ICS/calendar output, and browser reminder management.
- Share This Page replaces Save Guide as a stateless utility. Desktop uses the branded popover; mobile uses `navigator.share()` first and falls back to the accessible bottom sheet.
- Child Theme `0.9.1` uses generic Hero/Layout/TOC selectors and a compact, overflow-safe Share utility with desktop popover and mobile bottom-sheet presentation.

Frontend Component Registry and Catalog are complete:

- Added `component-registry.v1.json` as the single capability source for 24 stable CMS-callable IDs: 21 ordered `page_block` components plus Article Hero, Share This Page, and Table of Contents as explicit `presentation_meta` capabilities.
- Each capability declares purpose, category, status, supported semantic variants, JSON Schema, example, implementation paths, accessibility, responsive behavior, and CMS availability.
- The Contract REST response derives its component definitions and guide allowlists from the Registry; `content-contract.v2.json` does not maintain a duplicate list.
- Added the cached public read-only `GET /wp-json/stc/v1/component-registry` endpoint.
- Added generated `docs/COMPONENT_LIBRARY.md`, including available/internal/legacy/not-yet-componentized/proposed sections and the nine-step publication workflow.
- Added an ephemeral Playground `/design-system/` Component Gallery with all capability records, all Hero variants, and real content examples. It is not auto-created in production.
- Added `scripts/verify-component-registry.ps1`; static and runtime verification reject registry drift and unknown Contract component definitions.

Formal Frontend to CMS Capability Contract is complete:

- Published `contracts/component-registry.json` for the independent CMS repository. It contains only the 24 implemented `cms_usable` capabilities and exposes Contract/schema versions, status, deprecation state, semantic variants, input schemas, derived required/optional fields, and `{ type, variant, data }` examples.
- Published `contracts/page-schema.json`, whose 21 allowed ordered block shapes and variants are generated from the same Registry. Its `blocks[]` sequence is explicitly the final render order; `contentType` is taxonomy, not layout.

Affiliate Capability Upgrade is complete in the working tree:

- Component Registry `1.2.0`, Content Contract `2.1.0`, Parent Theme `0.31.1`, and Child Theme `0.10.1` publish 24 CMS capabilities, including 21 ordered page blocks.
- Publish Package `1.0.0` is available at `contracts/cms-publish-package.schema.json` and `GET /wp-json/stc/v1/cms-publish-package-schema`.
- Authenticated `POST /wp-json/stc/v1/cms-articles` and `PUT /wp-json/stc/v1/cms-articles/{post_id}` now create or update draft-only WordPress articles from CMS Page Payloads.
- The adapter revalidates the deployed Contract, serializes static/semantic content to editable Gutenberg blocks and dynamic content to renderer-owned shortcode blocks, maps presentation metadata, stores SEO/GEO/provenance, and rejects non-draft overwrites.
- Added real renderers for `affiliate_booking_card`, `affiliate_search_card`, `affiliate_banner`, and `affiliate_promotion_card`; the historical `affiliate_cta` remains unchanged and stable.
- New renderers fail closed on incomplete, unknown, HTML-bearing, non-HTTPS, credential-bearing, or non-allowlisted URL input. Search Box and Dynamic Banner accept only structured fixed-field embeds.
- Added generated CMS-shape REST endpoints at `/wp-json/stc/v1/component-registry/generated` and `/wp-json/stc/v1/page-schema` with reproducible JSON and stable cache validators.
- Added privacy-minimal 50%-visibility impressions, non-blocking click delivery, and a same-origin `/wp-json/stc/v1/commercial-events` relay with payload/field/enum limits, rate limiting, deduplication, and environment-only forwarding credentials.
- Updated the Playground Gallery, generated contracts/catalog, standalone behavior tests, deployment notes, and CMS integration documentation. No production configuration, commit, push, or deployment was performed.
- A read-only compatibility run against the local CMS 1.11.x `FrontendContractConsumer` reached `healthy`, validated legacy `affiliate_cta` plus all four new examples, rejected JavaScript URLs/unknown HTML, kept pre-QA resolution commercial-free, and reported the intentionally unimplemented `affiliate_product_card` as missing. Live deployed sync and authenticated Engine forwarding remain production-configuration tasks.
- Added `docs/CMS_FRONTEND_CONTRACT.md` with repository ownership, CMS consumption paths, versioning, rejection rules, and the publication workflow.
- Added `docs/COMPONENT_CHANGELOG.md` for CMS-visible capability changes only; visual-only changes are explicitly excluded.
- `scripts/generate-component-catalog.ps1` now generates both root contracts and the Component Catalog from the Theme Registry. Packaging regenerates and verifies these artifacts before creating ZIP files.

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

The original Guide / Article styling stage below is historical and has been superseded by the generic Contract 2.0 article shell:

- Added `assets/css/article.css`, loaded only on single posts after the shared Child assets.
- Renovated the shared article Hero with explicit, CMS-selected restrained visual variants and existing high-resolution destination imagery.
- Added a measured desktop reading column, an explicitly enabled On this page sidebar, and reusable editorial component styling.
- Added a horizontal mobile On this page navigator, edge-to-edge mobile article rhythm, single-column fact modules, 44px-class controls, and heading anchor offsets below the Header.
- Added semantic Home / Hub / Current Article breadcrumbs through a narrowly scoped Child `the_content` filter, avoiding a copied Parent `single.php` and avoiding duplicate SEO-plugin schema or metadata.
- The current implementation no longer contains guide-saving logic; Share and TOC render only when explicitly enabled by CMS metadata.
- Added temporary Playground-only Attraction, City, and Survival Kit article fixtures to the preview Blueprint. They write only to the ephemeral preview database and are not production content or a static demo.
- Playwright QA passed for the Attraction article at 1440, 768, 390, and 375 widths; City and Survival Kit branches were also checked at 390. All checked pages had one H1, correct guide navigation state, correct Breadcrumb hub, no horizontal overflow, and zero browser console errors/warnings.
- The current Share utility is stateless; mobile TOC navigation reaches the target heading with the Header offset preserved when `_stc_show_toc` is enabled.
- Child PHP remains compatible with the declared PHP 7.4 minimum.

## Plugin Status

Current tools plugin version: `0.25.0`.

The custom plugin owns three tools:

- Find This Place.
- Show This to a Driver / Taxi Card.
- Ticket Booking Window.

The shortcodes are:

- `[solo_to_china_tools_directory]`
- `[solo_to_china_place_finder]`
- `[solo_to_china_taxi_card entity_key="forbidden-city"]`
- `[solo_to_china_ticket_tool]`

Find This Place accepts one to four photos of the same location through click, drag/drop, or paste; validates JPEG/PNG/WebP, count, per-file and total bytes, MIME, and dimensions on both client and server; immediately removes every PHP temporary file; and never creates a Media Library attachment. The UI recommends complementary angles, signs, entrances, streets, and a wide view. Its external vision adapter processes the set as one request using only server environment credentials, semantic confidence levels, and safe unavailable/error states. Provider addresses and coordinates are discarded.

Taxi Card resolves canonical Chinese names/cities, includes a street address only when locally verified, asks the traveler to choose among ambiguous places such as West Lake, copies a driver-ready destination, and provides keyboard-contained full-screen Driver Mode. Canonical finder results hand off by `entity_key`; provider-only text is never promoted into driver instructions, and no handoff state is persisted.

Current Ticket Booking Window behavior:

- Select attraction.
- Attraction select is grouped by city for easier scanning on mobile.
- Select visit date.
- Calculate the recommended date to start checking tickets from `booking_lead_days`.
- Static first-phase attraction data currently covers 18 attractions across Beijing, Shanghai, Xi'an, Zhangjiajie, Hangzhou, Chengdu, Guangzhou, Luoyang, Dunhuang, Leshan, Huangshan, Jiuzhaigou, and Guilin.
- Frontend assets load on the homepage, Tools page, or pages that contain the ticket tool shortcode.
- Show `BOOKING NOT OPEN YET`, `BOOKING WINDOW REACHED`, or `VISIT DATE PASSED`.
- Show the visit date, estimated booking date, and lead-day rule without claiming live availability.
- Offer the existing safe Trip.com destination with `sponsored noopener` and visible disclosure when the visit date is valid.
- Keep no state after the page closes. There is no localStorage, JSON, ICS, calendar, reminder, push, email, SMS, or account behavior.

Theme `0.29.0` also creates missing About, Contact, Privacy Policy, Terms of Use, Affiliate Disclosure, and Disclaimer pages through an idempotent versioned admin migration. Existing pages and administrator-edited content/status are not overwritten; an existing privacy-page setting is preserved. Reserved draft slugs receive a public, read-only Theme fallback instead of being changed.

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
- Browser preview checks protect homepage reference alignment, both four-card guide folds and expansion, responsive Survival Kit columns, mobile tool-band line heights, explicit Share/TOC behavior, semantic breadcrumbs, utility-page content scope, FAQ expansion, and horizontal overflow.

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
- Reminder delivery or browser reminder management.
- Custom database tables.
- Real ticket inventory checking.
- New top-level navigation.
- New tools beyond Ticket Booking Window.
