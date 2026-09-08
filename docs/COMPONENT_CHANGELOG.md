# SoloToChina Component Contract Changelog

This changelog records only changes that affect the capability contract consumed by `solo-to-china-CMS`. Pure visual changes are excluded unless they change CMS-visible semantics, supported input, or rendering behavior.

Versioning follows semantic compatibility:

- Patch: compatible corrections with no required CMS output change.
- Minor: backward-compatible component, optional field, or variant additions.
- Major: removed/renamed capabilities, newly required fields, incompatible schemas, or changed semantics.

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
