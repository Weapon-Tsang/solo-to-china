# SoloToChina Child Theme

Current version: `0.9.0`

This Child Theme is the presentation layer for the SoloToChina `0.29.0` Parent Theme. It owns the visual system, responsive layout, editor parity, and restrained interaction styling without duplicating Parent templates or Plugin business logic.

## Installation

1. Install `solo-to-china-theme.zip` and keep the Parent Theme installed.
2. Install `solo-to-china-child-theme.zip`.
3. Activate SoloToChina Child.
4. Install and activate `solo-to-china-tools-plugin.zip` for Ticket Booking Window.

## Responsibility boundary

- Parent Theme: Contract, generic semantic shell, reusable renderers, metadata, and fallback presentation.
- Child Theme: design tokens, component appearance, responsive behavior, and editor styling.
- CMS: content structure, component selection, component order, variants, Share visibility, and TOC visibility.
- SoloToChina Tools Plugin: attraction data, booking-window calculation, date states, and validation.

The article stylesheet targets the generic `.stc-article-hero`, `.stc-article-layout`, and reusable component classes. Content type modifiers provide visual context only. They never add content modules.

Share This Page is styled as a compact utility action rather than a booking CTA. Desktop/fine-pointer devices use the branded popover; mobile/coarse-pointer devices try native Web Share first and use the branded bottom sheet if unavailable or unsuccessful.

Version `0.9.0` adds the restrained 60-75ch support/legal page shell, the responsive Footer information architecture, and the short per-card Guide Grid reveal motion. Container height is never animated and reduced-motion users receive no reveal animation.

## Asset order

`functions.php` loads Parent, Child base, Design System, shared site styles, page-specific styles, Content Components, and Child interactions in deterministic order. Editor styles use the same Design System and component layer. `assets/css/component-gallery.css` loads only for the deliberate internal Component Gallery template or `/design-system/` slug.
