# SoloToChina Tools Plugin

Project-owned WordPress functionality for SoloToChina.

Current version: `0.22.0`.

This plugin owns:

- Attraction Ticket Reservation & Reminder
- Guest-first reminder flows with no login requirement
- Local/browser-based reminder state
- Reminder export/import/clear actions
- Individual reminder delete action
- `.ics` calendar download for saved reminders
- Expanded and city-grouped static first-phase attraction data
- Optional `attraction_slug` shortcode context, validated against Plugin-owned attraction data, for guide-level preselection

The shortcode remains `[solo_to_china_ticket_tool]`. A Theme renderer may delegate an attraction context with `[solo_to_china_ticket_tool attraction_slug="forbidden-city"]`; the Plugin still owns validation, data, calculations, frontend behavior, and local reminder state.

The first tool should be limited to Attraction Ticket Reservation & Reminder. Do not add unrelated calculators, checklists, app selectors, or other tools until real user demand supports them.

Email/SMS delivery, cross-device sync, custom tables, and real ticket inventory checks are intentionally out of scope for this phase.
