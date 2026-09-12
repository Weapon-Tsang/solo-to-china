# SoloToChina Theme

Project-owned WordPress Parent Theme for SoloToChina.

Current version: `0.33.0`.

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

Registry `1.3` exposes 26 stable CMS capabilities: 23 ordered content blocks and three explicit presentation capabilities (`article_hero`, `share_this_page`, and `table_of_contents`). The new `route_timeline` and `pros_cons` blocks provide reusable travel sequencing and balanced decision support. The `destination_card` block delegates a canonical `entity_key` to the Tools Plugin; the Theme never owns place data or resolution. `page-design-system.php` provides an internal Gallery when a page deliberately selects that template; the Theme never creates that page in production.

`contracts/component-registry.json`, `contracts/page-schema.json`, and `docs/COMPONENT_LIBRARY.md` are generated from the Theme Registry. Change the Registry and implementation together, update the Gallery/tests and Component Changelog, then run `scripts/generate-component-catalog.ps1`. The independent CMS repository should read the root `contracts/` files instead of reverse-engineering Theme source.

The CMS delivery adapter accepts `contracts/cms-publish-package.schema.json` at `POST /wp-json/stc/v1/cms-articles`, supports explicit draft updates at `PUT /wp-json/stc/v1/cms-articles/{post_id}`, and publishes its schema at `GET /wp-json/stc/v1/cms-publish-package-schema`. WordPress Application Password authentication and `edit_posts`/`edit_post` capabilities are required. Public Contract endpoints remain read-only.

## Tool boundary

The Theme does not own vision providers, place/address data, destination resolution, Ticket data, booking-window calculations, or tool validation. Those responsibilities remain in `wp-content/plugins/solo-to-china-tools/`. The `destination_card` and legacy `ticket_reminder` component IDs are thin Plugin adapters.

Version `0.32.1` turns Planner into a focused AI-itinerary landing page, applies the approved affiliate URL with sponsored/nofollow semantics, and removes low-value Footer links and slogans. Version `0.32.0` added Route Timeline and Pros and Cons as registered, CMS-serializable Gutenberg components and expanded the internal Gallery with reusable foundations. The homepage and Footer continue to use the true-alpha white artwork introduced in `0.31.1`. Existing pages and administrator-edited content are never overwritten.

The theme should not own tool business logic.

Do not edit third-party Theme source directly for SoloToChina-specific work.
