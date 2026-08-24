# WordPress Install Handoff

This handoff is for the current SoloToChina WordPress setup on aaPanel.

## Build Artifacts

Run the release package script from the repository root:

```powershell
.\scripts\package-release.ps1
```

It creates:

- `dist/solo-to-china-theme.zip`
- `dist/solo-to-china-tools-plugin.zip`
- `dist/release-manifest.txt`

The two `.zip` files are intended for WordPress upload. The manifest records artifact versions and SHA256 hashes for checking the generated files.

## Install Through WordPress Admin

Recommended path for the first install:

1. Go to WordPress Admin.
2. Open Appearance > Themes > Add New > Upload Theme.
3. Upload `solo-to-china-theme.zip`.
4. Activate the SoloToChina theme.
5. Open Plugins > Add New > Upload Plugin.
6. Upload `solo-to-china-tools-plugin.zip`.
7. Activate the SoloToChina Tools plugin.

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

Next go to:

- `/www/wwwroot/solotochina.com/wp-content/plugins/`

Upload `solo-to-china-tools-plugin.zip` there, then extract it in the same `plugins` directory.

After extraction, the final directories should be:

- `/www/wwwroot/solotochina.com/wp-content/themes/solo-to-china/`
- `/www/wwwroot/solotochina.com/wp-content/plugins/solo-to-china-tools/`

Do not extract either zip directly inside `/www/wwwroot/solotochina.com/wp-content/`.

Then activate the theme and plugin inside WordPress Admin.

## Post-Install Check

After activation, check these pages in WordPress:

- Home: image-led homepage loads with transparent header.
- Survival Kit: opened guide articles can be saved locally; listing cards do not show save controls.
- City Guides: opened city-guide articles can be saved locally; listing cards do not show save controls.
- Attraction Guides: opened attraction-guide articles can be saved locally; listing cards do not show save controls.
- Planner: Trip.com CTA opens in a new tab.
- Tools: Ticket Date & Availability renders, requires a visit date, and can save a local reminder.
- FAQ: FAQ items open and close normally.

For featured images uploaded before version `0.17.0`, regenerate WordPress thumbnails once so the `stc-guide-card-2x` 960px size is available. New uploads receive the size automatically. Source featured images should be at least 960px wide; 1200px or wider is preferred for high-DPI screens.

Also check:

- Mobile menu opens and closes.
- Keyboard Tab shows visible focus states.
- Saved Guides and Saved Reminders show local-only copy.
- Export/import/clear actions work only on the current browser.
- City and Attraction cards are sharp on a high-DPI phone, use centered 3:4 framing, and retain a readable smooth bottom scrim without image blur.

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
- `wp-content/plugins/solo-to-china-tools/`

## Current Scope

This release includes the approved homepage direction as a custom theme scaffold and a guest-first Ticket Reservation & Reminder shortcode.

The reminder feature is currently local-device only. It shows a simple booking-window status, stores saved reminders in the visitor's browser, can export/import a `.json` backup, can clear saved reminders from the current device, and can download an `.ics` calendar file for each reminder date. It does not require login, email, SMS, or database storage.

Saved Guides are also local-device only. They store guides selected after opening an article in the visitor's browser, can export/import a `.json` backup, and can be cleared from the current device. They do not require login or database storage.

Email/SMS reminder delivery and cross-device sync should be added later as plugin features, after deciding storage, notification provider, consent copy, and anti-spam rules.
