# Component Changelog

## Commercial coupon B correction / Parent 0.33.7 - 2026-09-21

- Render CMS-selected commercial assets as illustrated B-style coupon tickets with a perforated claim stub, Limited offer badge, and Claim bonus button.
- Cut small semicircular and curved stamp perforations of varied sizes from the left and right sides only; keep the top and bottom straight and preserve the dotted tear line. The pale Good Trips Ahead postmark uses an uneven printed edge, faint ink marks, and waving cancellation lines near the icon group.
- Center the reference's filled airplane, bed, and train pictograms with Flights, Hotels, and Tickets labels above every button. Keep all three visible on mobile, with the postmark allowed to touch Tickets.
- Replace the monochrome scene treatments with the Temple of Heaven's cobalt roof and warm architecture, the Great Wall's stone and green hills, and Zhangjiajie's sandstone and foliage, fading each into the light-blue ticket background.
- Fix visible headlines by category: new-user hotels up to 20% off; flights, trains, attractions and tours 10% off; airport transfers 15% off; Trip.Planner retains its requested itinerary headline. Stored links and sponsored relation attributes remain intact.
- Use the same final line on every ticket: `Explore more of China for less- your next adventure awaits.`
- Remove the repeated commission sentence and disclosure-page link from rendered commercial placements and the homepage planner block. The standalone disclosure page remains available.
- Keep the Registry schema and CMS payloads compatible. Coupon values are presentation copy supplied by the site owner; the frontend does not verify provider redemption terms.

## Compatible commercial B presentation / Parent 0.33.6, Child 0.13.1 - 2026-09-21

- Present commercial cards as blue/white/pink offer tickets, with an explicit numeric headline only when the payload supplies a valid offer. Existing generic links stay non-discount cards; no category-derived discount is invented.
- Replace per-card commission copy with one visible relationship notice adjacent to the first commercial placement and a link to the independent disclosure page. Link destination, attribution, and sponsored/nofollow relation remain intact.
- Display-only normalization removes exact historical category-title and booking-filler templates while preserving edited copy and stored content. Legacy CTA and Planner placements use the same relationship notice; parent-only rendering remains supported.
- Registry component IDs and schema shapes are unchanged. Generated contract checksums changed with documentation/examples; CMS consumers must sync the matching contract before delivery.

## 1.4.1 / Parent Theme 0.33.5 - 2026-09-19

- Make disclosure optional on every commercial Registry component so older and leaner CMS payloads remain renderable.
- Preserve a visible compact `Paid link` fallback in the renderer; explicit disclosure text remains supported.
- Replace horizontal mobile TOC pills with a native vertical disclosure list and add Parent-only dynamic-component styling parity.

## Registry 1.4.0 / Parent Theme 0.33.3 - 2026-09-16

- Keep the identity boundary fail-closed while accepting a page fingerprint rotation only when the explicit WordPress post and stored CMS draft ID both match the incoming package.

## Registry 1.4.0 / Parent Theme 0.33.2 - 2026-09-15

- Preserve Registry 1.4.0 while returning exact commercial slot/page-hash receipts from CMS draft delivery.
- Add a 15-minute opaque single-draft preview capability; a new ticket revokes the old one, and preview requests are no-store/noindex with commercial event collection disabled.

## Registry 1.4.0 / Parent Theme 0.33.1 - 2026-09-14

This compatible runtime patch stores the CMS schema language, renders English CMS article dates independently of the administrator locale, maps CMS SEO/social/canonical fields into Rank Math, and derives robots from the actual WordPress post status. CMS delivery remains draft-only and therefore noindex/nofollow; an explicit later WordPress publication becomes index/follow. No component ID, field, variant, Content Contract, Page Schema, Publish Package schema or Frontend rendering ownership changes.

## 1.4.0 — 2026-09-12

Adds place_info_card, annotated_image and related_guides. Extends Quick Facts, screenshot Steps, route details, image framing/enlargement and explicit compact/image-led heroes. Existing IDs and payloads remain supported. Generated Page Schema includes 26 ordered blocks and 29 total capabilities. CMS integration in the other repository is pending.

This changelog records only changes that affect the capability contract consumed by `solo-to-china-CMS`. Pure visual changes are excluded unless they change CMS-visible semantics, supported input, or rendering behavior.

Versioning follows semantic compatibility:

- Patch: compatible corrections with no required CMS output change.
- Minor: backward-compatible component, optional field, or variant additions.
- Major: removed/renamed capabilities, newly required fields, incompatible schemas, or changed semantics.

## 1.3.0 / Parent Theme 0.32.0 / Child Theme 0.11.0 / Tools Plugin 0.25.1 - 2026-09-09

### Added

- Added `route_timeline` for ordered, mobile-friendly travel sequences with optional stop details.
- Added `pros_cons` for balanced two-sided guidance with configurable headings and concise item lists.
- Added reusable Stack, Cluster, Outline/Quiet Button, Badge, Field, and Panel visual primitives, documented through a new Foundations section in the internal Component Gallery.

### Changed

- Reworked the Component Gallery with sticky navigation, category counts and accents, stronger card hierarchy, clearer live-example surfaces, and responsive foundation samples.

### Compatibility

- Content Contract remains `2.1.0`; Component Registry advances to `1.3.0` because both CMS capabilities are additive.
- The new visual primitives are implementation-level library additions and do not expand the CMS page-block API.
- Existing component IDs, fields, variants, and stored content remain valid.

## 1.2.0 / Parent Theme 0.31.0 / Child Theme 0.10.0 / Tools Plugin 0.25.0 - 2026-09-09

### Added

- Added `destination_card` as a backward-compatible CMS-selectable page block requiring a canonical `entity_key`.
- Added the Plugin-delegated Taxi Card renderer with verified Chinese destination output, ambiguity handling, copy action, and full-screen Driver Mode.
- Added privacy-first Find This Place and Taxi Card public tools without expanding the CMS into provider or place-data ownership.
- Added one-to-four same-location photo identification with 20 MB per-photo and 60 MB per-request limits, multi-preview guidance, and a combined provider payload.
- Added branded Facebook, Reddit, X, and Instagram share choices alongside WhatsApp, native system sharing, and Copy link; removed Email from the share panel.

### Compatibility

- Content Contract remains `2.1.0`; Component Registry advances to `1.2.0` because the new capability is additive.
- Existing component IDs, fields, variants, and stored content remain valid.

## Parent Theme 0.29.1 / Child Theme 0.9.1 - 2026-09-08

### Changed

- Updated the shared Trip.Planner destination so Homepage and Planner-page CTAs open the planner workspace directly instead of requiring a second click on the Trip.com introduction page.

- Isolated Share panel typography and controls from page Hero selectors, replaced the cramped three-column action row with an overflow-safe two-column layout, and made Copy Link span the full row.
- Simplified the Share trigger and panel copy, standardized channel icons, and retained the native-first mobile flow plus keyboard and clipboard behavior.

### Compatibility

- Content Contract remains `2.1.0` and Component Registry remains `1.1.0`; CMS output does not need to change.

## Parent Theme 0.29.0 / Child Theme 0.9.0 / Tools Plugin 0.23.0 - 2026-09-08

### Changed

- Replaced measured mobile Guide Grid max-height transitions with a reusable first-four card toggle. No-JS and desktop continue to show every card; newly revealed cards use only a short opacity/translate animation.
- Reworked Share This Page into a branded desktop popover and native-first mobile flow with a branded fallback bottom sheet, while preserving canonical channels, clipboard fallback, Escape, outside-click, focus return, and keyboard containment.
- Repositioned the Plugin tool as stateless Ticket Booking Window. The historical `ticket_reminder` Registry ID remains stable as a compatibility adapter.
- Centralized Homepage and Planner-page Trip.Planner links in `stc_get_trip_planner_url()`.
- Added versioned, idempotent bootstrap content and a shared reading shell for About, Contact, Privacy Policy, Terms of Use, Affiliate Disclosure, and Disclaimer. A read-only public fallback handles slugs already reserved by unpublished pages without overwriting or publishing them.
- Reorganized the Footer around Brand, Explore, Tools, Help, About, and Legal; removed placeholder social icons and added real contact destinations.

### Removed

- Removed the full Ticket Reminder product surface: Save/Saved reminders, localStorage, JSON import/export, delete/clear, ICS/calendar output, and all related UI and tests.
- Removed dynamic Guide Grid height measurement, height custom properties, max-height reveal transitions, forced focus, and automatic scroll risk.

### Compatibility

- Content Contract remains `2.1.0` and Component Registry remains `1.1.0`; there is no schema-breaking ID change.

## Parent Theme 0.28.0 - 2026-09-08

### Changed

- Serialized concurrent CMS create requests with a per-identity WordPress database lock and persisted CMS identifiers immediately after the draft row is created.
- Bound canonical and structured-data output to the final WordPress permalink/title, suppressed duplicate SEO output when a supported SEO plugin owns it, and withheld stale JSON-LD after a manual post edit.
- Kept Component Registry `1.1.0` and Content Contract `2.1.0`; the public component interface remains backward compatible.

## CMS Publish Adapter 1.0.0 - 2026-09-06

### Added

- Added a formal CMS WordPress Publish Package Schema generated with the current Page Schema and exact Component Contract checksum.
- Added authenticated `POST /wp-json/stc/v1/cms-articles` upsert and `PUT /wp-json/stc/v1/cms-articles/{post_id}` draft-update endpoints.
- Added strict PHP Contract validation, Registry-driven Gutenberg/shortcode serialization, presentation mapping, SEO/GEO/provenance storage, structured JSON-LD output, and CMS page/draft ID idempotency.
- Added real WordPress Playground E2E coverage for all 20 page-block strategies, successful draft creation/update/rendering, invalid package rejection, and published-post protection.

### Changed

- Generated JSON artifacts now use deterministic canonical key ordering so checksum provenance is reproducible.

### Deprecated

- None.

### Removed

- None.

## 1.1.0 - 2026-09-03

### Added

- Added `affiliate_booking_card`, `affiliate_search_card`, `affiliate_banner`, and `affiliate_promotion_card` as backward-compatible CMS-selectable page blocks.
- Added generated-shape WordPress endpoints for the Component Contract and Page Schema with stable cache validators.
- Added strict Trip.com-family hostname validation, structured search/banner embeds, visible disclosures, promotion validity windows, and privacy-minimal impression/click attribution.
- Added a same-origin public WordPress event relay with payload limits, field/enum allowlists, rate limiting, deduplication, environment-only server credentials, and non-blocking failure behavior.

### Changed

- Parent Theme is now `0.26.0`, Child Theme is `0.8.0`, and Content Contract is `2.1.0`.

### Deprecated

- None. The existing `affiliate_cta` remains stable for historical content and simple-link fallback.

### Removed

- None.

## 1.0.0 - 2026-09-03

### Added

- Published the first formal Frontend to CMS capability contract at `contracts/component-registry.json`.
- Published the CMS page payload contract at `contracts/page-schema.json`.
- Declared 19 stable CMS-callable capabilities: 16 ordered page blocks and three explicit presentation capabilities.
- Defined stable IDs, categories, purposes, statuses, variants, input schemas, required/optional fields, and deprecation state.
- Defined `{ type, variant, data }` as the block envelope and `blocks[]` order as final render order.
- Documented the independent repository ownership and consumption boundary.

### Changed

- None.

### Deprecated

- None.

### Removed

- None. Topic-wide article patterns and Save Guide behavior were removed before Contract 1.0 and were never published as CMS capability IDs.
