# SoloToChina Child Theme

Current version: `0.3.0`

This Child Theme is the presentation layer for the SoloToChina `0.22.0` Parent Theme. It is intentionally small: Parent templates and project-owned helpers continue to provide the working content structure, while the Child Theme owns visual tokens, incremental template overrides, responsive layout refinements, and restrained interaction styling.

## Installation

1. Install `solo-to-china-theme.zip` and keep the SoloToChina Parent Theme installed.
2. Install `solo-to-china-child-theme.zip`.
3. Activate SoloToChina Child.
4. Install and activate `solo-to-china-tools-plugin.zip` for the Ticket Date & Availability feature.

The Parent Theme is required but should not be active when the Child Theme is active.

## Responsibility boundary

- Parent Theme: stable WordPress support, semantic templates, content helpers, and fallback presentation.
- Child Theme: visual design, layout, template presentation, responsive behavior, and presentational assets.
- SoloToChina Tools plugin: Ticket Tool and Reminder data, calculation, storage, scheduling, and validation logic.

Do not move Ticket Tool or Reminder business logic into this Child Theme. Do not copy the entire Parent Theme; add a template override only when the Child Theme needs different markup.

## Asset order

`functions.php` loads the Child Theme stylesheet after the Parent `stc-main` handle, then loads `assets/css/design-system.css` and `assets/css/site.css`. The homepage additionally loads `assets/css/home.css`, while single posts load `assets/css/article.css`. The article layer also inserts semantic guide breadcrumbs without copying the Parent single template. `assets/js/site.js` enhances the existing Parent navigation with Escape, label synchronization, and responsive close behavior.
