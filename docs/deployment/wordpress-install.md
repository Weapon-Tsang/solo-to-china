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

It only creates missing pages. It does not overwrite existing WordPress page content.

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
- Planner: Trip.com CTA opens in a new tab.
- Tools: Ticket Date & Availability renders, requires a visit date, and can save a local reminder.
- FAQ: FAQ items open and close normally.

For featured images uploaded before version `0.17.0`, regenerate WordPress thumbnails once so the `stc-guide-card-2x` 960px size is available. New uploads receive the size automatically. Source featured images should be at least 960px wide; 1200px or wider is preferred for high-DPI screens.

Also check:

- Mobile menu opens and closes.
- SoloToChina Child is the active theme and SoloToChina remains installed as its Parent Theme.
- Mobile Hero stays within 480-580px at 75vh, keeps its title to two readable lines, and reveals the top of the Survival Kit shortcuts in the first viewport.
- Mobile Hero uses the vermilion Start Exploring CTA and the translucent glass menu button.
- Keyboard Tab shows visible focus states.
- Share This Page uses the canonical URL, prefers the device share sheet, and exposes the channel/copy fallback when native sharing is unavailable or fails.
- Share fallback supports keyboard focus, Escape close, visible status, and manual URL selection if clipboard access fails.
- Saved Reminders show local-only copy; their export/import/clear actions work only on the current browser.
- City and Attraction cards are sharp on a high-DPI phone, use centered 3:4 framing, and retain a readable smooth bottom scrim without image blur.
- Article reading width stays controlled on desktop; the mobile article has no horizontal overflow at 375-390px, and TOC links stop below the Header.

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

This release includes the approved homepage direction, the renovated Child Theme Guide / Article presentation, and a guest-first Ticket Reservation & Reminder shortcode.

The reminder feature is currently local-device only. It shows a simple booking-window status, stores saved reminders in the visitor's browser, can export/import a `.json` backup, can clear saved reminders from the current device, and can download an `.ics` calendar file for each reminder date. It does not require login, email, SMS, or database storage.

Share This Page is stateless. It does not save a guide, create an account, imply cross-device persistence, or write page state to browser storage or WordPress. It shares the canonical URL through `navigator.share()` when available and otherwise offers WhatsApp, email, and Copy link actions.

Email/SMS reminder delivery and cross-device sync should be added later as plugin features, after deciding storage, notification provider, consent copy, and anti-spam rules.
