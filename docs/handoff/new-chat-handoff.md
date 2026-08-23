# SoloToChina New Chat Handoff

Date: 2026-08-23

Use this document to continue the SoloToChina WordPress project in a new Codex/ChatGPT conversation.

## Suggested New Chat Opening Message

Continue the SoloToChina WordPress project from `docs/handoff/new-chat-handoff.md`. First read `README.md`, `docs/handoff/current-progress.md`, `docs/handoff/new-chat-handoff.md`, and `docs/deployment/wordpress-install.md`. Preserve all product, IA, guest-first, affiliate, Ticket Reminder, theme/plugin responsibility, and handoff constraints. Continue with small verified development increments; do not start high-risk backend/account/deployment changes without explicit approval.

## Project Goal

SoloToChina is a practical China independent travel platform for international travelers, especially solo travelers and first-time non-Chinese visitors.

Long-term product formula:

```text
CONTENT
+
UTILITY
+
PERSONALIZATION
```

Core principles:

- Guest-first
- Content-first
- Mobile-first
- Utility-driven
- Low friction
- High trust

Users should be able to browse content, use the first tool, save locally, share, and set local reminders without registering.

Accounts are not an access gate. Accounts may be added later only for cross-device saved guides, reminder management, comment history, and personal libraries after a separate product/storage/consent design.

## Fixed Information Architecture

Top-level navigation is fixed:

- Home
- Survival Kit
- City Guides
- Attraction Guides
- Planner
- Tools
- FAQ

Do not add top-level:

- Hotels
- Tickets
- Flights
- Trains
- Book

Affiliate and booking links are a transaction layer behind content/tools, not a top-level navigation system.

## Content Structure Rules

Survival Kit:

- Payment
- Essential Apps
- eSIM
- Visa
- VPN / Internet

City Guides:

- City Guides is a city hub/card grid.
- Each city should later have child guide content such as itinerary, food, where to stay, transport, neighborhoods, and other practical guides.
- Do not turn City Guides into one huge generic article.

Attraction Guides:

- Attraction content supports practical booking timing, passport notes, best time, and planning reminders.
- Real inventory checking is out of scope.

Planner:

- For now, Planner is a Trip.com CTA/landing layer.
- Do not build a custom itinerary engine yet.

Tools:

- The first and only current tool is Attraction Ticket Reservation & Reminder.
- Do not add unrelated calculators, checklists, app selectors, or new tools until real user demand or explicit approval.

## Current Repository And Code Boundary

Work from:

```text
repo-staging/
```

Project-owned WordPress code lives only in:

```text
wp-content/themes/solo-to-china/
wp-content/plugins/solo-to-china-tools/
```

Do not edit:

- WordPress core
- third-party themes
- third-party cache plugins
- `wp-config.php`
- uploads
- cache folders
- language files
- database dumps/backups
- server-local files

`sources/` in the ChatGPT project mirror is read-only reference material.

## Current Theme Status

Theme: `SoloToChina`

Version: `0.3.0`

Location:

```text
wp-content/themes/solo-to-china/
```

Implemented:

- Approved image-led homepage direction.
- Transparent header over selected banner image.
- Reduced hero text density.
- Survival Kit strip.
- City Guides image cards.
- Attraction Guides image cards.
- Planner band.
- Homepage Planner CTA opens Trip.com externally with `rel="sponsored noopener"`.
- Ticket Tool / Reminder homepage band.
- FAQ section.
- Footer.
- Mobile menu.
- Keyboard focus styling.
- Skip-to-content link.
- Local Saved Guides UI.
- No-account page sharing on core guide pages.
- Attraction Guide article layout and editor content pattern.
- Core IA page rendering by slug.
- Core IA page auto-creation on theme activation if missing.
- `single.php` for guide/article posts.
- `archive.php` for category/tag archives.
- `search.php` for search results.
- `searchform.php` for controlled search form copy.
- `404.php` with links back to key travel sections.
- WordPress support for title tag, featured images, wide alignment, HTML5 markup, and automatic feed links.

Homepage image assets:

```text
wp-content/themes/solo-to-china/assets/images/hero-home.png
wp-content/themes/solo-to-china/assets/images/guide-card-bg.png
```

The current homepage design should not be replaced casually. User approved this direction after multiple visual iterations.

## Current Plugin Status

Plugin: `SoloToChina Tools`

Version: `0.3.0`

Location:

```text
wp-content/plugins/solo-to-china-tools/
```

Shortcode:

```text
[solo_to_china_ticket_tool]
```

Current tool:

- Attraction Ticket Reservation & Reminder.

Current behavior:

- Select attraction.
- Attraction select is grouped by city for easier scanning on mobile.
- Select visit date.
- Visit date is required.
- Calculate recommended ticket-check/reminder date from `booking_lead_days`.
- Show booking-window status:
  - `Book now`
  - `Set reminder`
  - `Date has passed`
- Prevent saving reminders for past visit dates.
- Save reminders locally without login.
- View saved reminders on the current device.
- Export saved reminders to `solotochina-ticket-reminders.json`.
- Import reminders from exported JSON.
- Clear reminders from the current device.
- Delete individual reminders.
- Download an `.ics` calendar file for individual reminders.
- Clamp imported reminder text before saving locally.
- Validate imported reminder dates before saving locally.
- Load frontend assets only on the homepage, Tools page, or pages containing the shortcode.

Static first-phase attraction data currently covers 18 attractions across:

- Beijing
- Shanghai
- Xi'an
- Zhangjiajie
- Hangzhou
- Chengdu
- Guangzhou
- Luoyang
- Dunhuang
- Leshan
- Huangshan
- Jiuzhaigou
- Guilin

Ticket reminders use browser `localStorage` only. They do not send email/SMS and do not write to the database.

## Local Personalization Status

Saved Guides:

- Available on homepage/core guide pages.
- Stored in browser `localStorage` only.
- No login required.
- Export to `solotochina-saved-guides.json`.
- Import from exported JSON.
- Clear all on current device.
- Delete individual saved guides.
- Imported guide text is clamped.
- Imported guide types are validated against current allowed types:
  - Survival Kit
  - City Guide
  - Attraction Guide

Page sharing:

- Available on core guide pages.
- Uses native share where supported.
- Falls back to copying the page link.
- No account required.

## Install Artifacts

Current generated artifacts:

```text
dist/solo-to-china-theme.zip
dist/solo-to-china-tools-plugin.zip
dist/release-manifest.txt
```

Latest manifest at the time of this handoff:

```text
Generated: 2026-08-23 15:53:51 +08:00
Theme version: 0.3.0
Theme SHA256: 913444F0828B2DC6B54FC81051F985C6ACC4CDEC901D5389B9D828A79E9502B3
Plugin version: 0.3.0
Plugin SHA256: 440E6D7E3450D251A9CAEDC9F5A14D23272BCB9DFAA416B62A39CFBB8077D3D8
```

Regenerate artifacts with:

```powershell
.\scripts\package-release.ps1
```

## Install Path

Preferred install:

1. WordPress Admin > Appearance > Themes > Add New > Upload Theme.
2. Upload `dist/solo-to-china-theme.zip`.
3. Activate SoloToChina theme.
4. WordPress Admin > Plugins > Add New > Upload Plugin.
5. Upload `dist/solo-to-china-tools-plugin.zip`.
6. Activate SoloToChina Tools plugin.

aaPanel fallback if WordPress upload fails:

```text
/www/wwwroot/solotochina.com/wp-content/themes/
/www/wwwroot/solotochina.com/wp-content/plugins/
```

Final directories must be:

```text
/www/wwwroot/solotochina.com/wp-content/themes/solo-to-china/
/www/wwwroot/solotochina.com/wp-content/plugins/solo-to-china-tools/
```

Do not extract either zip directly inside `/www/wwwroot/solotochina.com/wp-content/`.

## Verification

Primary verification command:

```powershell
.\scripts\verify-project.ps1
```

Expected current result:

```text
PHP CLI not found; skipped PHP syntax checks.
SoloToChina project verification passed.
```

Local environment note:

- PHP CLI is not installed locally, so the PowerShell verifier skips PHP syntax checks.
- Static project checks pass.

The verification scripts protect:

- required files
- fixed IA labels
- banned transaction-first nav labels
- theme/plugin version consistency
- theme/plugin responsibility boundaries
- local saved guide behavior
- ticket reminder behavior
- import/export/clear behavior
- keyboard accessibility basics
- aaPanel install warnings
- release manifest generation
- artifact version/hash output
- generated artifact ignore rules

## GitHub / Source Control Status

This continuation was cloned from:

```text
https://github.com/Weapon-Tsang/solo-to-china
```

At the start of the 2026-08-23 continuation, the local checkout was clean on `main` and `.\scripts\verify-project.ps1` passed. Regenerate local zip artifacts after plugin or theme changes and use the fresh `dist/` output for deployment.

## Post-Install Manual QA

After installing on WordPress, check:

- Home loads with the approved image-led banner and transparent header.
- Mobile menu opens and closes.
- Header does not obscure content on mobile.
- Survival Kit page renders and guide cards can be saved.
- City Guides page renders and guide cards can be saved.
- Attraction Guides page renders and guide cards can be saved.
- Saved Guides export/import/clear works in current browser.
- Share page works or copies link.
- Planner page Trip.com CTA opens in a new tab.
- Homepage Planner CTA opens Trip.com in a new tab.
- Tools page renders Ticket Tool / Reminder.
- Ticket Tool requires visit date.
- Past visit date cannot be saved as reminder.
- Saved reminders export/import/clear/delete works in current browser.
- `.ics` calendar download works.
- FAQ details open and close.
- Keyboard Tab shows visible focus states.
- Search page/search form works.
- 404 page links back to core sections.

## Next Safe Development Steps

Good next increments:

1. Install the theme/plugin on the live WordPress site and capture desktop/mobile screenshots.
2. Fix real install spacing, header, and mobile issues based on screenshots.
3. Continue refining or expanding static attraction data inside the existing Ticket Tool only.
4. Add more realistic guide-card metadata once actual content pages exist.
5. Add basic content templates for Survival Kit article pages if real posts/categories are ready.
6. Improve FAQ content and internal links.
7. Add lightweight analytics/event tracking only after deciding privacy approach.

## Do Not Start Without Explicit Approval

Do not start these in the next chat unless the user explicitly approves a separate design/spec:

- Account system.
- Cross-device sync.
- Email/SMS/push reminder delivery.
- Custom database tables.
- Real ticket inventory checking.
- Payment processing.
- New top-level navigation.
- New tools beyond Attraction Ticket Reservation & Reminder.
- Heavy affiliate marketplace or booking-first redesign.
- Replacing the approved homepage direction.
- Editing third-party themes/plugins directly.
- Deploying to production without user approval.

## Development Style For Next Chat

- Continue small bounded increments.
- Before each feature, add/update verification checks, then implement, then verify.
- Run `.\scripts\verify-project.ps1` after each meaningful change.
- Run `.\scripts\package-release.ps1` after changes that affect installable artifacts.
- Keep updating `docs/handoff/current-progress.md`.
- Keep this `new-chat-handoff.md` updated if major decisions change.
- Preserve guest-first behavior.
- Preserve theme/plugin responsibility split:
  - Theme owns presentation/layout/templates.
  - Plugin owns Ticket Tool logic and data.

