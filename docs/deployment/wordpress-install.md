# WordPress Install Handoff

This handoff is for the current SoloToChina WordPress setup on aaPanel.

## Build Artifacts

Run the release package script from the repository root:

```powershell
.\scripts\package-release.ps1
```

It creates:

- `dist/solo-to-china-theme.zip`
- `dist/solo-to-china-child-theme.zip`
- `dist/solo-to-china-tools-plugin.zip`
- `dist/release-manifest.txt`

The three `.zip` files are intended for WordPress upload. The manifest records artifact versions and SHA256 hashes for checking the generated files.

## Install Through WordPress Admin

Recommended path for the first install:

1. Go to WordPress Admin.
2. Open Appearance > Themes > Add New > Upload Theme.
3. Install SoloToChina Parent Theme first by uploading `solo-to-china-theme.zip`. Keep it installed; do not activate it yet if you are continuing directly to the Child Theme.
4. Upload `solo-to-china-child-theme.zip`.
5. Activate SoloToChina Child. WordPress automatically uses the installed Parent Theme for inherited templates and functionality.
6. Open Plugins > Add New > Upload Plugin.
7. Upload `solo-to-china-tools-plugin.zip`.
8. Activate the SoloToChina Tools plugin.

When the theme is activated, it creates any missing core IA pages:

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

When Tools Plugin `0.25.0` is activated, it creates missing child pages at `/tools/find-this-place/` and `/tools/taxi-card/`. It never overwrites existing page content.

It only creates missing pages. It does not overwrite existing WordPress page content or publish drafts. Theme `0.29.0` also runs this idempotent bootstrap once from the WordPress admin after an upgrade, so the six support/legal pages are created even when the Theme was already active. If an unpublished page already reserves one of the required slugs—as a fresh WordPress install commonly does for Privacy Policy—the public URL uses a read-only Theme fallback until an administrator publishes that page. If WordPress has no configured privacy page, the new or existing `/privacy-policy/` page is assigned; an existing privacy-page setting is preserved.

## Install Through aaPanel Files

Use this only when WordPress upload is blocked by file size or permissions.

In aaPanel Files, go to:

- `/www/wwwroot/solotochina.com/wp-content/themes/`

Upload `solo-to-china-theme.zip` there, then extract it in the same `themes` directory.

Upload `solo-to-china-child-theme.zip` to the same `themes` directory, then extract it there. Keep both theme directories installed and activate SoloToChina Child in WordPress Admin.

Next go to:

- `/www/wwwroot/solotochina.com/wp-content/plugins/`

Upload `solo-to-china-tools-plugin.zip` there, then extract it in the same `plugins` directory.

After extraction, the final directories should be:

- `/www/wwwroot/solotochina.com/wp-content/themes/solo-to-china/`
- `/www/wwwroot/solotochina.com/wp-content/themes/solo-to-china-child/`
- `/www/wwwroot/solotochina.com/wp-content/plugins/solo-to-china-tools/`

Do not extract either zip directly inside `/www/wwwroot/solotochina.com/wp-content/`. Both theme zips belong in `themes/`; the plugin zip belongs in `plugins/`.

Then activate the theme and plugin inside WordPress Admin.

## Post-Install Check

After activation, check these pages in WordPress:

- Home: image-led homepage loads with transparent header.
- Survival Kit: guide articles use the shared article shell and render CMS-authored blocks in stored order.
- City Guides: guide articles use the same shared shell; guide type affects taxonomy and breadcrumb context, not layout.
- Attraction Guides: guide articles use the same shared shell and retain responsive featured-image output.
- Guide articles: verify one H1, Home / Hub / Article Breadcrumb, CMS-authored component order, and no automatically injected checklist, FAQ, CTA, Share, or TOC.
- Planner: the shared CTA opens `https://www.trip.com/tripplanner` in a new tab with sponsored/noopener semantics.
- Tools: the directory lists Find This Place, Taxi Card, and Ticket Booking Window without clipping or overflow.
- Find This Place: multi-file picker, drag/drop, and paste all reach the preview state; one to four same-location photos are accepted at 20 MB each and 60 MB total; unsupported/oversized files show actionable errors; an unconfigured provider fails safely without a fabricated location.
- Taxi Card: Forbidden City resolves to `故宫博物院`, West Lake asks the traveler to choose a city, Copy destination works, and Driver Mode supports Escape and keyboard focus containment.
- Ticket Booking Window: requires a visit date, calculates the estimated date from Plugin-owned lead days, and shows all three timing states without saving browser data or claiming live availability.
- FAQ: FAQ items open and close normally.
- About, Contact, Privacy Policy, Terms of Use, Affiliate Disclosure, and Disclaimer: each URL resolves with one H1 and the shared reading-width static-page shell.

For featured images uploaded before version `0.17.0`, regenerate WordPress thumbnails once so the `stc-guide-card-2x` 960px size is available. New uploads receive the size automatically. Source featured images should be at least 960px wide; 1200px or wider is preferred for high-DPI screens.

Also check:

- Mobile menu opens and closes.
- SoloToChina Child is the active theme and SoloToChina remains installed as its Parent Theme.
- Mobile Hero stays within 480-580px at 75vh, keeps its title to two readable lines, and reveals the top of the Survival Kit shortcuts in the first viewport.
- Mobile Hero uses the vermilion Start Exploring CTA and the translucent glass menu button.
- Keyboard Tab shows visible focus states.
- Share This Page uses the canonical URL. Desktop/fine-pointer devices always open the branded popover; mobile/coarse-pointer devices prefer the native share sheet and use the branded bottom sheet when native sharing is unavailable or fails.
- Share fallback supports keyboard focus, Escape close, visible status, and manual URL selection if clipboard access fails.
- Ticket Booking Window retains no reminder state and exposes no Save, JSON, ICS, or calendar controls.
- Mobile Guide Grids at 375, 390, 430, 768, and 840 show four cards initially, reveal immediately, toggle back with Show fewer, and have no clipping or overlap. Desktop shows all cards and no More button.
- City and Attraction cards are sharp on a high-DPI phone, use centered 3:4 framing, and retain a readable smooth bottom scrim without image blur.
- Article reading width stays controlled on desktop; the mobile article has no horizontal overflow at 375-390px, and TOC links stop below the Header.
- Tool layouts have no horizontal overflow at 375, 390, 430, 768, 840, or desktop widths; long place names wrap and all primary controls retain touch targets.

## Generated Contract And Commercial Event Configuration

After installing Parent Theme `0.31.0`, verify these public read-only endpoints:

- `/wp-json/stc/v1/component-registry/generated`
- `/wp-json/stc/v1/page-schema`
- `/wp-json/stc/v1/cms-publish-package-schema`

Configure the independent CMS with the deployed frontend commit and these generated endpoints. Do not point CMS synchronization at PHP/React source files or the internal authoring Registry shape.

## CMS Draft Delivery Configuration

Create a dedicated least-privilege WordPress user that can edit posts, then create a WordPress Application Password for the CMS. Send credentials only over HTTPS and keep them in the CMS server secret store.

Configure the CMS to use:

```text
WORDPRESS_BASE_URL=https://solotochina.com
WORDPRESS_CMS_ARTICLE_ENDPOINT=https://solotochina.com/wp-json/stc/v1/cms-articles
WORDPRESS_CMS_PUBLISH_PACKAGE_SCHEMA=https://solotochina.com/wp-json/stc/v1/cms-publish-package-schema
WORDPRESS_APPLICATION_USERNAME=<dedicated CMS user>
WORDPRESS_APPLICATION_PASSWORD=<server-only Application Password>
```

The CMS must fetch the deployed generated Component Contract, strip quotes from its ETag and use that SHA256 as `contract.contractChecksum`, set `pageSchemaVersion` to the deployed Page Schema `contractVersion`, and send `publication.status=draft`. Do not send the package to generic `/wp-json/wp/v2/posts`; that route does not run the SoloToChina Contract validator or serializer.

After configuration, send one validated package and confirm:

- HTTP `201` creates a WordPress draft.
- The response contains non-empty `edit_url` and `preview_url`.
- Repeating the same `page.metadata.pageId`/`publication.cms_draft_id` returns HTTP `200` with `updated: true` and does not create a duplicate.
- The Gutenberg editor shows native blocks/groups plus individual Shortcode blocks for dynamic components.
- Attempts to send `publication.status=publish` or overwrite a published post return `POST_NOT_DRAFT`.

The public browser event endpoint is `/wp-json/stc/v1/commercial-events`. Forwarding is disabled safely unless both server process environment variables are present:

```text
STC_COMMERCIAL_EVENTS_ENDPOINT=https://cms-engine.example/api/commercial/events
STC_COMMERCIAL_EVENTS_TOKEN=<server-only token>
```

Set them in the PHP-FPM/hosting process environment, never in Theme files, JavaScript, WordPress options, the database, Git, response bodies, or logs. Restart PHP-FPM after changing the service environment, then send one same-origin test event and confirm the endpoint returns `202` without exposing the token.

Find This Place is also disabled safely until the server process has `STC_PLACE_VISION_ENDPOINT` and `STC_PLACE_VISION_TOKEN`. Optional external destination resolution uses `STC_PLACE_RESOLVER_ENDPOINT` and `STC_PLACE_RESOLVER_TOKEN`. Use HTTPS endpoints and keep all four values out of WordPress options, the database, frontend code, Git, responses, and logs. For multi-photo requests, set PHP `upload_max_filesize` to at least `20M`, `post_max_size` and any reverse-proxy body limit to at least `64M`, and `memory_limit` to at least `256M`. See `docs/architecture/web-tools.md` for the provider schema and privacy boundary.

Rollback by reinstalling the previous Parent and Child Theme packages together and restoring the CMS Contract endpoint/commit settings to their previous values. Remove the two event environment variables only after the previous frontend is active; no database rollback is required because this feature adds no custom tables or persisted commercial payloads.

## Do Not Overwrite

Do not upload the whole repository into the server root.

Do not overwrite:

- `wp-config.php`
- `wp-content/uploads/`
- `wp-content/cache/`
- `wp-content/languages/`
- database backups or SQL files

The project-owned code boundaries are only:

- `wp-content/themes/solo-to-china/`
- `wp-content/themes/solo-to-china-child/`
- `wp-content/plugins/solo-to-china-tools/`

## Current Scope

This release includes the approved homepage direction, the renovated Child Theme Guide / Article presentation, privacy-first Find This Place and Taxi Card tools, the stateless Ticket Booking Window, the support/legal page migration, and the redesigned Footer.

Ticket Booking Window uses Plugin-owned attraction data and lead-day rules. It stores no reminder or visit state, does not create calendar files, and does not claim live inventory.

Share This Page is stateless. It does not save a guide, create an account, imply cross-device persistence, or write page state to browser storage or WordPress. Desktop uses the branded popover; mobile attempts `navigator.share()` first. The fallback sheet offers branded WhatsApp, Facebook, Reddit, X, and Instagram choices plus Copy link; Instagram uses native sharing when available and otherwise copies the canonical link before opening Instagram.

Reminder delivery and browser reminder management are not part of the SoloToChina product direction.
