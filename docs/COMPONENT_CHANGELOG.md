# SoloToChina Component Contract Changelog

This changelog records only changes that affect the capability contract consumed by `solo-to-china-CMS`. Pure visual changes are excluded unless they change CMS-visible semantics, supported input, or rendering behavior.

Versioning follows semantic compatibility:

- Patch: compatible corrections with no required CMS output change.
- Minor: backward-compatible component, optional field, or variant additions.
- Major: removed/renamed capabilities, newly required fields, incompatible schemas, or changed semantics.

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

