# SoloToChina Component Contract Changelog

This changelog records only changes that affect the capability contract consumed by `solo-to-china-CMS`. Pure visual changes are excluded unless they change CMS-visible semantics, supported input, or rendering behavior.

Versioning follows semantic compatibility:

- Patch: compatible corrections with no required CMS output change.
- Minor: backward-compatible component, optional field, or variant additions.
- Major: removed/renamed capabilities, newly required fields, incompatible schemas, or changed semantics.

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

