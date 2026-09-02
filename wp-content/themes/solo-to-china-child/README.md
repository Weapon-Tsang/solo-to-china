# SoloToChina Child Theme

Current version: `0.7.0`

This Child Theme is the presentation layer for the SoloToChina `0.25.0` Parent Theme. It owns the visual system, responsive layout, editor parity, and restrained interaction styling without duplicating Parent templates or Plugin business logic.

## Installation

1. Install `solo-to-china-theme.zip` and keep the Parent Theme installed.
2. Install `solo-to-china-child-theme.zip`.
3. Activate SoloToChina Child.
4. Install and activate `solo-to-china-tools-plugin.zip` for Ticket Date & Availability.

## Responsibility boundary

- Parent Theme: Contract, generic semantic shell, reusable renderers, metadata, and fallback presentation.
- Child Theme: design tokens, component appearance, responsive behavior, and editor styling.
- CMS: content structure, component selection, component order, variants, Share visibility, and TOC visibility.
- SoloToChina Tools Plugin: Ticket and Reminder data, calculation, storage, scheduling, and validation.

The article stylesheet targets the generic `.stc-article-hero`, `.stc-article-layout`, and reusable component classes. Content type modifiers provide visual context only. They never add content modules.

Share This Page is styled as a high-trust utility action rather than a booking CTA. It prioritizes the native Web Share sheet and provides an accessible, lightweight fallback panel with WhatsApp, email, and canonical-link copy options.

## Asset order

`functions.php` loads Parent, Child base, Design System, shared site styles, page-specific styles, Content Components, and Child interactions in deterministic order. Editor styles use the same Design System and component layer. `assets/css/component-gallery.css` loads only for the deliberate internal Component Gallery template or `/design-system/` slug.
