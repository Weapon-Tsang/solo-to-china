# Child Theme Foundation Audit

Date: 2026-08-31

Scope: SoloToChina Parent Theme `0.21.0`, SoloToChina Tools `0.21.0`, existing templates/assets, validation, packaging, and local preview capability.

## KEEP

- Fixed guest-first information architecture and the seven approved top-level navigation items.
- Parent Theme semantic templates, WordPress feature support, core-page/category creation, guide classification, responsive featured-image output, explicit article utilities, native share fallback, and reusable content block patterns. The local Saved Guides behavior recorded in the initial audit was removed by the Contract 2.0 responsibility refactor.
- Plugin ownership of attraction data, booking-window calculations, local reminders, import/export, calendar downloads, validation, and storage behavior.
- Existing high-resolution destination imagery: 14 distinct 960x1200 WebP card assets plus the approved hero/planner/ticket artwork.
- Existing page order and content-first separation between guides, restrained affiliate CTA, tools, FAQ, and footer.
- Keyboard skip link, focus behavior, semantic heading baseline, lazy loading, stable image dimensions, `srcset`/`sizes` support for WordPress featured images, and reduced-motion direction.

## REFACTOR

- Move visual evolution into `solo-to-china-child` with explicit Parent -> Child -> design-system resource order.
- Consolidate color, typography, spacing, container, grid, radius, shadow, control, image-ratio, breakpoint, motion, and focus values into named Child Theme tokens.
- Refine Header, Footer, button, form, card, and content rhythm in bounded Child Theme stages instead of growing the Parent stylesheet.
- Update verification, release packaging, root documentation, install order, and handoff state for three independently installable artifacts.
- Perform page-by-page mobile-first QA at 375, 390, 430, 768, 1280, and 1440 widths once a WordPress preview runtime is available.

## REPLACE

- Replace individual Parent visual declarations only through targeted Child Theme overrides as each page family is renovated.
- Prefer the existing high-resolution WebP card images over matching lower-resolution PNG variants when a later template/CSS override touches that image.
- Replace ad-hoc component values with the Child Theme token contract; do not replace semantic templates or working content logic without a specific markup need.

## REMOVE

- Nothing in this foundation phase. No dead template, script, or image has enough evidence for deletion.
- Lower-resolution image variants remain as Parent fallbacks until every reference is audited in the page-specific stages.

## Preview capability

At foundation audit time the repository contained installable theme/plugin code and static/PHP verification, but no WordPress runtime or local preview URL. A static HTML reconstruction was deliberately rejected because it would not verify WordPress template loading, enqueue order, responsive images, shortcodes, or plugin integration.

This gap was resolved in the next stage with `scripts/start-preview.ps1` and `scripts/playground-blueprint.json`. They run the official WordPress Playground CLI, mount the Parent Theme, Child Theme, and Plugin directly from this repository, and activate the Child Theme and Plugin. Playwright artifacts are stored in the ignored `output/playwright/` path.

## SEO / GEO baseline

The Parent Theme already provides semantic page landmarks, one primary H1 per core template, ordered H2/H3 content patterns, title-tag support, feed links, responsive featured images, internal links, and accessible navigation labels. Later Child overrides must preserve these behaviors. Canonical, Open Graph, and schema output should remain compatible with the site's SEO plugin; the Child Theme must not emit competing duplicate metadata.
