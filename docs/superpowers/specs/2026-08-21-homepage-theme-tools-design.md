# SoloToChina Homepage Theme And Tools Design

Date: 2026-08-21

## Goal

Build the first project-owned WordPress code layer for SoloToChina without editing third-party themes or plugins.

The first implementation phase creates:

- A custom `solo-to-china` WordPress theme.
- A custom `solo-to-china-tools` WordPress plugin.
- A static, production-oriented homepage template based on the approved revised Direction 4 visual concept.
- A Ticket Tool / Reminder shortcode boundary owned by the plugin.

## Non-Goals

This phase does not:

- Modify GeneratePress, Akismet, Redis Object Cache, Rank Math, or default WordPress themes.
- Build the full reminder backend.
- Send reminder emails.
- Add user accounts.
- Build a custom itinerary planner.
- Add extra tools beyond Attraction Ticket Reservation & Reminder.
- Add top-level navigation items outside the approved IA.
- Push generated images, uploads, cache files, database exports, or secrets.

## Product Constraints

SoloToChina must stay:

- Guest-first
- Content-first
- Mobile-first
- Utility-driven
- Low friction
- High trust

Top-level navigation is fixed:

- Home
- Survival Kit
- City Guides
- Attraction Guides
- Planner
- Tools
- FAQ

Do not add top-level navigation items such as Hotels, Tickets, Flights, Trains, or Book.

Affiliate placement stays restrained:

- Homepage affiliate intensity is low.
- Planner may link to Trip.com Trip.Planner.
- Trip.com must not become the visual protagonist.
- Booking CTAs belong behind content and tools, not in the primary IA.

## Theme Responsibility

The `wp-content/themes/solo-to-china/` theme owns presentation:

- Theme metadata and WordPress support setup.
- Header/navigation markup.
- Footer markup.
- Homepage template.
- Static section rendering for the first visual implementation.
- CSS design system for the approved homepage direction.
- Basic theme assets and scripts.

The approved homepage direction is the revised Direction 4:

- Image-led destination hero.
- Transparent header over the hero.
- Reduced hero/subtitle text density.
- City and attraction cards follow the stronger image-card style from Direction 2.
- Planner and Ticket Reminder modules keep the revised Direction 4 structure.

The first implementation may use CSS gradient/image placeholders for theme layout only if no licensed production image has been committed. Production images should later be handled through WordPress media or committed licensed assets.

## Plugin Responsibility

The `wp-content/plugins/solo-to-china-tools/` plugin owns project functionality:

- Ticket Tool / Reminder shortcode registration.
- Attraction data used by the first shortcode.
- Stateless first-version UI for selecting attraction and travel date.
- Guest-first copy: no login required, free to use.

The first implementation does not persist reminders. It creates the markup and PHP boundary so persistence/email can be added safely in a later phase.

## Data Model For First Phase

The first phase uses static PHP arrays, not database tables.

Attraction records include:

- `slug`
- `name`
- `city`
- `booking_note`
- `passport_note`
- `best_time`

This keeps the first implementation auditable and avoids premature custom database schema.

## Testing

Because this repository is WordPress PHP code without a configured WordPress test runner yet, the first phase uses focused static checks:

- PHP syntax checks for every created PHP file.
- Text checks that confirm fixed navigation labels exist.
- Text checks that banned top-level nav labels are absent from theme navigation.
- Text checks that the tools plugin registers the expected shortcode.

These checks are not a substitute for browser QA after the theme is installed on a WordPress environment. They are the minimum reliable checks available in the current repository state.

## First Implementation Files

Theme files:

- `wp-content/themes/solo-to-china/style.css`
- `wp-content/themes/solo-to-china/functions.php`
- `wp-content/themes/solo-to-china/header.php`
- `wp-content/themes/solo-to-china/footer.php`
- `wp-content/themes/solo-to-china/front-page.php`
- `wp-content/themes/solo-to-china/assets/css/main.css`
- `wp-content/themes/solo-to-china/assets/js/main.js`

Plugin files:

- `wp-content/plugins/solo-to-china-tools/solo-to-china-tools.php`
- `wp-content/plugins/solo-to-china-tools/includes/attractions.php`
- `wp-content/plugins/solo-to-china-tools/includes/shortcodes.php`
- `wp-content/plugins/solo-to-china-tools/assets/css/tools.css`
- `wp-content/plugins/solo-to-china-tools/assets/js/tools.js`

Verification files:

- `scripts/verify-project.php`

## Handoff Boundary

After this phase, the repository should contain an installable custom theme and plugin skeleton. The live site is not modified automatically. Deployment to the aaPanel WordPress site should be a separate step after local/repository verification.
