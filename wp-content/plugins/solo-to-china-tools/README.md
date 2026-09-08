# SoloToChina Tools Plugin

Project-owned planning functionality for SoloToChina.

Current version: `0.25.0`.

This plugin owns:

- The `/tools/`, `/tools/find-this-place/`, and `/tools/taxi-card/` page bootstrap and shortcodes.
- Server-side vision/resolver provider abstractions with environment-only credentials and safe unavailable states.
- JPEG/PNG/WebP validation, immediate temporary-file cleanup, no Media Library writes, no-store responses, and anonymous HMAC-keyed rate limits.
- Canonical Chinese place/city records, verification labels, ambiguity resolution, and finder-to-taxi handoff.
- The copyable Taxi Card and keyboard-contained full-screen Driver Mode.
- The Ticket Booking Window interface and accessible result states.
- The attraction dataset and city grouping.
- Rule-based `booking_lead_days` calculations.
- Visit-date validation and the booking-not-open, booking-window-reached, and visit-date-passed states.
- Optional `attraction_slug` shortcode context, validated against Plugin-owned attraction data.

The primary shortcodes are `[solo_to_china_tools_directory]`, `[solo_to_china_place_finder]`, `[solo_to_china_taxi_card entity_key="forbidden-city"]`, and `[solo_to_china_ticket_tool]`. Theme renderers may delegate a canonical entity or attraction slug; the Plugin still owns validation, data, calculations, and frontend behavior.

Provider configuration and the exact request/privacy contract are documented in `docs/architecture/web-tools.md`. Find This Place accepts one to four photos of the same location (20 MB each, 60 MB total), processes them as one identification request, and returns a safe temporary-unavailable result when no vision provider is configured; it never invents a match. Taxi Card continues to resolve its locally verified canonical catalog.

The historical Content Contract ID and Theme shortcode `[stc_ticket_reminder]` remain compatibility adapters only. The rendered product is Ticket Booking Window.

Ticket Reminder has been removed. The plugin does not persist browser state, save reminders, import or export JSON, create ICS files, integrate calendars, or send Web Push, email, or SMS. It also does not claim live ticket inventory or real-time availability.

The optional Trip.com action uses the existing safe provider URL with `sponsored noopener` and a visible affiliate disclosure; it does not invent tracking parameters or availability.
