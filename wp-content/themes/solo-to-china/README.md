# SoloToChina Theme

Project-owned WordPress Parent Theme for SoloToChina.

Current version: `0.29.0`.

## Responsibility

The Parent Theme owns:

- The versioned Content Contract and public read-only REST endpoint.
- The canonical Frontend Component Registry and public read-only `/wp-json/stc/v1/component-registry` endpoint.
- A generic article shell that renders CMS/Gutenberg content in stored order.
- Reusable semantic components and safe dynamic renderer adapters.
- Explicit CMS presentation metadata for Share, TOC, and Hero variant.
- An authenticated, Contract-aware CMS Article API that writes draft-only, editable Gutenberg content and private provenance/SEO/GEO metadata.
- Taxonomy, URL, breadcrumb, SEO, archive, search, and site-shell infrastructure.
- Accessible Share This Page behavior with a branded desktop popover, native mobile Web Share, canonical-link fallback bottom sheet, and graceful errors.
- Parent-only fallback presentation.
- The fixed top-level information architecture and current core landing-page presentation.

The governing boundary is:

- Frontend responsibility: “Render what CMS requests.”
- CMS responsibility: “Decide what the page contains.”
- Content type is taxonomy, not layout.

City, Attraction, Survival, and Travel guide types may inform labels, URLs, breadcrumbs, discovery, and visual context. They do not inject FAQ, checklist, ticket, TOC, Share, or other editorial modules. The CMS chooses components and order through post content, and explicitly controls page utilities through registered REST metadata.

The old topic-wide article patterns and guide-saving system have been removed. Share This Page replaces Save Guide without accounts, browser storage, or saved state. Desktop/fine-pointer devices never invoke the system share sheet.

Registry `1.1` exposes 23 stable CMS capabilities: 20 ordered content blocks and three explicit presentation capabilities (`article_hero`, `share_this_page`, and `table_of_contents`). The four new commercial blocks render only explicit CMS data, enforce official affiliate host and structured-embed allowlists, expose privacy-minimal event attributes, and fail closed on incomplete input. `page-design-system.php` provides an internal Gallery when a page deliberately selects that template; the Theme never creates that page in production.

`contracts/component-registry.json`, `contracts/page-schema.json`, and `docs/COMPONENT_LIBRARY.md` are generated from the Theme Registry. Change the Registry and implementation together, update the Gallery/tests and Component Changelog, then run `scripts/generate-component-catalog.ps1`. The independent CMS repository should read the root `contracts/` files instead of reverse-engineering Theme source.

The CMS delivery adapter accepts `contracts/cms-publish-package.schema.json` at `POST /wp-json/stc/v1/cms-articles`, supports explicit draft updates at `PUT /wp-json/stc/v1/cms-articles/{post_id}`, and publishes its schema at `GET /wp-json/stc/v1/cms-publish-package-schema`. WordPress Application Password authentication and `edit_posts`/`edit_post` capabilities are required. Public Contract endpoints remain read-only.

## Tool boundary

The Theme does not own Ticket data, booking-window calculations, or validation. Those responsibilities remain in `wp-content/plugins/solo-to-china-tools/`. The legacy `ticket_reminder` component ID is retained only as a compatibility adapter for the stateless Ticket Booking Window.

Version `0.29.0` also provides a reusable, non-measuring Guide Grid controller; a single Trip.Planner URL helper; the redesigned Footer; and an idempotent, versioned admin migration for About, Contact, Privacy Policy, Terms of Use, Affiliate Disclosure, and Disclaimer pages. Existing pages and administrator-edited content are never overwritten. If an unpublished page already reserves one of those slugs, the public URL uses a read-only Theme fallback until the administrator publishes the page.

The theme should not own tool business logic.

Do not edit third-party Theme source directly for SoloToChina-specific work.
