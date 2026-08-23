# SoloToChina Current Progress Handoff

Date: 2026-08-23

## Current Working Boundary

The project-owned WordPress code lives in:

- `wp-content/themes/solo-to-china/`
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

Current theme version: `0.14.0`.

The custom theme implements the approved image-led homepage direction:

- Transparent header over the hero image.
- Non-home header uses the same brand/navigation language with readable dark text on a white surface.
- Reduced hero copy density.
- Survival Kit strip.
- City Guides and Attraction Guides image cards.
- Homepage section order is locked to the approved reference: Hero, Survival Kit, City Guides, Attraction Guides, Planner, Ticket Tool / Reminder, FAQ, and Footer.
- The homepage no longer inserts Latest Guides between Attraction Guides and Planner; current posts remain available on their matching landing pages and archives.
- Mobile Survival Kit uses a compact horizontal rail, City Guides shows four cards with an animated expand/collapse control, and Attraction Guides uses a horizontal snap rail.
- City subtitles stay on one line, image-card save actions use compact bookmark controls, and Planner/Ticket content columns include dedicated icons without overlap.
- Homepage-reference visual assets now provide distinct city and attraction card images, plus Planner and Ticket band art.
- Core landing pages and guide article heroes now inherit the selected homepage visual style.
- Core landing pages render their primary guide content before Saved Guides and category-matched latest posts.
- Attraction Guides uses a readable two-column grid on phones and three-column grid on tablets instead of shrinking six cards into one row.
- Planner reuses the approved homepage calendar icon, Trip.com disclosure block, and watercolor artwork on desktop and mobile.
- FAQ uses polished two-column desktop and one-column mobile accordions with related internal links.
- Saved Guides and category-matched latest posts are limited to Survival Kit, City Guides, and Attraction Guides; Planner, Tools, and FAQ stay focused on their primary tasks.
- Planner band.
- Homepage Planner CTA opens Trip.com as a sponsored external link.
- Ticket Tool / Reminder band.
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
- Save guide cards without login.
- View saved guides on the current device.
- Export saved guides to `solotochina-saved-guides.json`.
- Import saved guides from the exported JSON format.
- Clear saved guides from the current device.
- Show local-only saved-data copy near the Saved Guides controls.
- Clamp imported saved-guide text before writing to browser storage.
- Validate imported saved-guide types before writing to browser storage.

Saved Guides use browser `localStorage` only. They do not write to WordPress users, custom tables, post meta, or remote services.

## Plugin Status

Current tools plugin version: `0.14.0`.

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
- `dist/solo-to-china-tools-plugin.zip`
- `dist/release-manifest.txt` with artifact versions and SHA256 hashes for both zip files.

New chat handoff:

- `docs/handoff/new-chat-handoff.md`

Preferred install path:

1. WordPress Admin > Appearance > Themes > Add New > Upload Theme.
2. Upload and activate `solo-to-china-theme.zip`.
3. WordPress Admin > Plugins > Add New > Upload Plugin.
4. Upload and activate `solo-to-china-tools-plugin.zip`.

aaPanel file install path if WordPress upload fails:

- Extract theme zip to `/www/wwwroot/solotochina.com/wp-content/themes/`
- Extract plugin zip to `/www/wwwroot/solotochina.com/wp-content/plugins/`

Final directories should be:

- `/www/wwwroot/solotochina.com/wp-content/themes/solo-to-china/`
- `/www/wwwroot/solotochina.com/wp-content/plugins/solo-to-china-tools/`

## Verification

Primary local verification command:

```powershell
.\scripts\verify-project.ps1
```

Packaging command:

```powershell
.\scripts\package-release.ps1
```

Current environment note:

- PHP CLI is installed locally in `.tools/php/`.
- The PowerShell verifier uses local PHP for syntax checks when a global `php` command is not available.
- Static project checks and PHP syntax checks pass before packaging.
- Browser preview checks protect homepage reference alignment, landing-page content order and mobile card widths, article table-of-contents breakpoints, utility-page content scope, FAQ expansion, and horizontal overflow.

## GitHub / Source Control Status

Normal `git push origin main` works in the current clone.

For deployment, use the local zip artifacts as the source of truth.

## Next Safe Small Steps

Reasonable next bounded increments:

- Improve mobile spacing and touch targets after installing on WordPress and checking real screenshots.
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
