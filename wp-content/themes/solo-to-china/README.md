# SoloToChina Theme

Project-owned WordPress Parent Theme for SoloToChina.

Current version: `0.25.0`.

## Responsibility

The Parent Theme owns:

- The versioned Content Contract and public read-only REST endpoint.
- The canonical Frontend Component Registry and public read-only `/wp-json/stc/v1/component-registry` endpoint.
- A generic article shell that renders CMS/Gutenberg content in stored order.
- Reusable semantic components and safe dynamic renderer adapters.
- Explicit CMS presentation metadata for Share, TOC, and Hero variant.
- Taxonomy, URL, breadcrumb, SEO, archive, search, and site-shell infrastructure.
- Accessible Share This Page behavior with Web Share API, canonical-link copy fallback, and graceful errors.
- Parent-only fallback presentation.
- The fixed top-level information architecture and current core landing-page presentation.

The governing boundary is:

- Frontend responsibility: “Render what CMS requests.”
- CMS responsibility: “Decide what the page contains.”
- Content type is taxonomy, not layout.

City, Attraction, Survival, and Travel guide types may inform labels, URLs, breadcrumbs, discovery, and visual context. They do not inject FAQ, checklist, ticket, TOC, Share, or other editorial modules. The CMS chooses components and order through post content, and explicitly controls page utilities through registered REST metadata.

The old topic-wide article patterns and guide-saving system have been removed. Share This Page replaces Save Guide without accounts, browser storage, or saved state.

Registry `1.0` currently exposes 19 stable CMS capabilities: 16 ordered content blocks and three explicit presentation capabilities (`article_hero`, `share_this_page`, and `table_of_contents`). `page-design-system.php` provides an internal Gallery when a page deliberately selects that template; the Theme never creates that page in production.

`docs/COMPONENT_LIBRARY.md` is generated from the Registry. Change the Registry and implementation together, update the Gallery/tests, then regenerate the catalog with `scripts/generate-component-catalog.ps1`.

## Tool boundary

The Theme does not own Ticket data, booking-window calculations, reminder storage, scheduling, consent, or validation. Those responsibilities remain in `wp-content/plugins/solo-to-china-tools/`.

The theme should not own tool business logic.

Do not edit third-party Theme source directly for SoloToChina-specific work.
