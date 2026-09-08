# SoloToChina Content Component System

Current Content Contract version: 2.1.0
Current Component Registry version: 1.1.0
Compatible Parent Theme version: 0.29.1

## Governing Boundary

Frontend responsibility: “Render what CMS requests.”

CMS responsibility: “Decide what the page contains.”

Content type is taxonomy, not layout.

The frontend owns the component library, Design System, responsive behavior, accessibility, interactions, generic page rendering, WordPress site shell, SEO rendering infrastructure, and safe mapping from CMS data to WordPress output.

Frontend capability rule: "Frontend defines what can be rendered." The CMS may select only IDs and semantic variants published by the canonical Component Registry.

The CMS owns content strategy, article structure, component selection, component order, component data, component variants, and whether optional utilities such as Share or TOC appear.

## Rendering Pipeline

The implemented CMS-to-WordPress path is:

    CMS Publish Package
    -> POST /wp-json/stc/v1/cms-articles
    -> WordPress Contract validation
    -> exact-order Gutenberg/shortcode serialization
    -> idempotent WordPress draft + private provenance metadata
    -> generic single.php article shell
    -> Theme/component renderer
    -> preview

    CMS
    → post content and presentation metadata
    → WordPress generic article shell
    → Gutenberg/component renderer
    → reusable component library
    → rendered page

WordPress post_content is the ordered page schema in the current integration. Native Gutenberg blocks and approved shortcode adapters are rendered in exactly the stored order. The frontend does not automatically insert, reorder, or infer FAQ, checklist, ticket reminder, quick facts, warning, steps, comparison, affiliate, or other editorial components.

There are no separate CityGuideRenderer, AttractionGuideRenderer, or SurvivalGuideRenderer implementations. single.php provides one semantic shell. Guide type may influence taxonomy labels, archive routing, breadcrumbs, related-content discovery, and visual context, but never the page’s component composition.

Unknown or newer Gutenberg block types use WordPress’s safe block fallback behavior. A component mismatch must not crash the article.

## Canonical Contract And Registry

The authoring machine-readable sources have separate responsibilities:

    wp-content/themes/solo-to-china/content-contract/content-contract.v2.json
    wp-content/themes/solo-to-china/content-contract/component-registry.v1.json

CMS clients read it from:

    GET /wp-json/stc/v1/content-contract
    GET /wp-json/stc/v1/component-registry
    GET /wp-json/stc/v1/component-registry/generated
    GET /wp-json/stc/v1/page-schema
    GET /wp-json/stc/v1/cms-publish-package-schema

Authenticated CMS delivery uses:

    POST /wp-json/stc/v1/cms-articles
    PUT /wp-json/stc/v1/cms-articles/{post_id}

The independent CMS repository should consume these generated repository contracts instead of scanning Theme source:

    contracts/component-registry.json
    contracts/page-schema.json
    contracts/cms-publish-package.schema.json

The CMS Article API is the Contract-aware publication boundary; generic `wp-json/wp/v2/posts` is not the primary CMS delivery interface. WordPress Application Passwords authenticate requests, then the adapter requires `edit_posts` or `edit_post`. The endpoint accepts only `publication.status=draft`, uses CMS page/draft IDs for idempotency, and refuses to overwrite a non-draft post.

The published Component Contract contains only CMS-usable capabilities. The Page Schema defines `{ type, variant, data }` blocks and makes `blocks[]` order the final render order. Both are generated from the authoring Registry by `scripts/generate-component-catalog.ps1`.

The REST endpoints remain public and read-only and include ETag, Last-Modified, and public cache headers. Contract and Theme versions remain independent; Contract 2.0 is a deliberate major change because content-type-specific layout behavior was removed.

The Contract publishes page/type rules and CMS-facing metadata. The Registry is the single source of truth for stable component IDs, names, categories, status, semantic variants, JSON input schemas, examples, implementation paths, accessibility, responsive behavior, and CMS availability. Contract REST output derives its backward-compatible component and per-guide allowlists from the Registry at runtime; the Contract JSON does not duplicate those lists.

The human-readable catalog is generated from the Registry at `docs/COMPONENT_LIBRARY.md`. The repository contract boundary and CMS-visible change history live in `docs/CMS_FRONTEND_CONTRACT.md` and `docs/COMPONENT_CHANGELOG.md`. Run `scripts/generate-component-catalog.ps1` after an approved Registry change.

## CMS Presentation Metadata

The CMS explicitly controls optional article presentation through REST-enabled post metadata:

| Purpose | Post meta | Values |
| --- | --- | --- |
| Guide taxonomy | _stc_guide_type | Stable guide-type slug |
| Contract version | _stc_content_contract_version | 2.1.0 |
| Share utility | _stc_show_share | Boolean |
| On This Page | _stc_show_toc | Boolean |
| Hero visual variant | _stc_hero_variant | default, attraction, city, or survival |

Missing Share or TOC metadata means the utility is not rendered. The Theme does not infer either flag from category, tag, title, or content type. When TOC is explicitly enabled, the frontend may derive its links from the H2 elements in the CMS-provided content.

The Hero consumes title, excerpt, date, featured image, taxonomy label, and explicit visual variant. It never adds ticket, checklist, booking, Save, or Share actions implicitly.

## Component Library

Registry 1.1 exposes 23 CMS-selectable capabilities: 20 ordered page blocks plus three explicit presentation capabilities.

- Core: Paragraph, Heading, List, Image
- Editorial: Quick Answer, Key Takeaways, Quick Facts, Tip, Warning, Steps, Checklist, Comparison Table, FAQ
- Contextual: Planner CTA, Ticket Booking Window compatibility adapter (`ticket_reminder`), Affiliate CTA
- Commercial: Affiliate Booking Card, Affiliate Search Card, Affiliate Banner, Affiliate Promotion Card
- Presentation: Article Hero, Share This Page, and TOC through explicit page metadata

Every editorial/contextual component is available to every guide taxonomy. The CMS chooses whether and where it appears. The Parent Theme registers small component patterns only; the former topic-wide Attraction, City, and Survival article patterns were removed.

Commercial components are available only as explicit post-QA CMS blocks. Their renderers require complete typed data, visible disclosure, official HTTPS destinations, and structured allowlisted embeds; invalid or incomplete input renders nothing. The frontend never inserts or reorders a commercial component. Impression and click attribution is privacy-minimal, client-side failures are non-blocking, and the public browser talks only to a same-origin WordPress relay whose forwarding credentials live in server environment variables.

Four additional frontend components are recorded with `cms_usable: false`: Article Shell, Guide Breadcrumb, Guide Card, and Latest Guides List. They exist in the renderer but are not valid `page.blocks[].type` values.

Images remain native WordPress Media with intrinsic dimensions, srcset, sizes, lazy loading, async decoding, alt text, and captions. Public roles affect presentation only and never expose research provenance.

## Share This Page

Share This Page replaces the discontinued Save Guide feature. It has no login requirement, account state, local storage, saved state, or cross-device implication.

On desktop/fine-pointer devices, the utility always opens the SoloToChina branded popover and never invokes `navigator.share()`. On mobile/coarse-pointer devices, it first calls `navigator.share()` with the page title, optional excerpt, and canonical URL; unsupported or unsuccessful native sharing opens the branded bottom sheet with:

- WhatsApp and email links
- A read-only canonical URL
- Copy link with clipboard and legacy-copy fallback
- Visible success/error status through an ARIA live region
- Escape, outside-click, close-button, and focus-return behavior
- Busy state while the mobile native share sheet is opening
- Focus-loop keyboard navigation inside the open fallback

Its visual weight stays below the H1 and primary planner or booking actions. The Child Theme presents it as a one-line utility trigger and a restrained 340-380px-class desktop popover/mobile bottom sheet.

## Ticket Boundary

The Parent Theme’s historical `ticket_reminder` adapter delegates to `[solo_to_china_ticket_tool]`. The rendered product is Ticket Booking Window. The SoloToChina Tools Plugin owns attraction data, booking lead days, date calculations, validation, and booking-timing states. The Theme does not move or duplicate Ticket logic.

## Removed Behavior

The Theme no longer contains:

- Save guide, Saved, Unsave, or Saved Guides interfaces
- stcSavedGuides browser storage
- Saved-guide export/import/clear/delete behavior
- Content-type-specific article markup branches
- Automatically injected planning checklists
- Topic-wide fixed Gutenberg article templates
- Automatic Share or TOC decisions based on guide type

Ticket Reminder is fully removed. No layer stores reminders, uses localStorage, imports or exports JSON, creates ICS/calendar files, or offers push, email, SMS, or account reminders. The Plugin is a stateless decision tool and does not claim real-time inventory.

## Integration Fixtures And Verification

scripts/playground-fixtures.php creates disposable Survival, City, and Attraction articles. Each explicitly enables Share and selects a Hero variant. Survival and Attraction enable TOC; City disables it to prove that taxonomy does not control layout. City also retains historical category-only guide classification coverage.

The same fixture creates `/design-system/` only inside Playground. Its Component Gallery reads the Registry for all 23 capability cards and renders real Gutenberg/shortcode examples for the 20 page-block components, plus the three presentation capabilities and every published Hero variant. The Theme does not create this page in production.

Verification commands:

    .\scripts\verify-page-architecture.ps1
    .\scripts\verify-component-registry.ps1
    .\scripts\verify-content-contract.ps1
    .\scripts\verify-project.ps1
    .\scripts\start-preview.ps1 -Port 9400
    .\scripts\verify-content-runtime.ps1 -BaseUrl http://127.0.0.1:9400

Parent-only fallback remains available through -ParentOnly. All Playground data is ephemeral and excluded from release packages.
