# SoloToChina Tools Plugin

Project-owned planning functionality for SoloToChina.

Current version: `0.23.0`.

This plugin owns:

- The Ticket Booking Window interface and accessible result states.
- The attraction dataset and city grouping.
- Rule-based `booking_lead_days` calculations.
- Visit-date validation and the booking-not-open, booking-window-reached, and visit-date-passed states.
- Optional `attraction_slug` shortcode context, validated against Plugin-owned attraction data.

The shortcode remains `[solo_to_china_ticket_tool]`. A Theme renderer may delegate context with `[solo_to_china_ticket_tool attraction_slug="forbidden-city"]`; the Plugin still owns validation, data, calculations, and frontend behavior.

The historical Content Contract ID and Theme shortcode `[stc_ticket_reminder]` remain compatibility adapters only. The rendered product is Ticket Booking Window.

Ticket Reminder has been removed. The plugin does not persist browser state, save reminders, import or export JSON, create ICS files, integrate calendars, or send Web Push, email, or SMS. It also does not claim live ticket inventory or real-time availability.

The optional Trip.com action uses the existing safe provider URL with `sponsored noopener` and a visible affiliate disclosure; it does not invent tracking parameters or availability.
