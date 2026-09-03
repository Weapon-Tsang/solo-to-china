# SoloToChina CMS / Frontend Capability Contract

Current Component Contract version: `1.1.0`

This document defines the integration boundary between the independent `solo-to-china` frontend repository and `solo-to-china-CMS` repository.

## Governing Rules

Frontend defines what CAN be rendered.

CMS decides what SHOULD be rendered.

Content type is taxonomy, not layout.

The CMS must only emit component types and variants published in `contracts/component-registry.json`. The frontend must not automatically add, remove, or reorder editorial blocks because of `contentType`.

## Ownership

Frontend owns:

- Component implementation and rendering behavior.
- Stable component IDs and supported semantic variants.
- Input schemas and compatibility handling.
- Styling and Design System decisions.
- Responsive behavior, accessibility, interaction, and safe fallback behavior.

CMS owns:

- Page content and metadata.
- Component selection.
- Component ordering.
- Selection from frontend-published variants.
- Complete page composition.

The CMS must not send CSS classes, color names, spacing tokens, shadow names, or invented visual variants such as `large-red-card`. Those details remain frontend-owned.

## Files The CMS Should Read

When the CMS needs the current machine-readable frontend capability list, read:

    contracts/component-registry.json

When the CMS needs the page payload and ordered block schema, read:

    contracts/page-schema.json

When a developer or content designer needs component purposes, variants, schemas, examples, accessibility notes, and responsive behavior, read:

    docs/COMPONENT_LIBRARY.md

When the CMS needs recent compatibility-impacting capability changes, read:

    docs/COMPONENT_CHANGELOG.md

When either repository needs the responsibility boundary, read this file:

    docs/CMS_FRONTEND_CONTRACT.md

The CMS should not scan or reverse-engineer Theme PHP, JavaScript, CSS, or Gutenberg patterns to discover component capabilities.

## Single Source Of Truth

The authoring source is:

    wp-content/themes/solo-to-china/content-contract/component-registry.v1.json

It includes implemented CMS capabilities and explicitly marked internal-only renderer components. The following published artifacts are generated from that source by `scripts/generate-component-catalog.ps1`:

- `contracts/component-registry.json`, filtered to `cms_usable: true` capabilities only.
- `contracts/page-schema.json`, with its `blocks[].type`, variants, and `data` schemas derived from the page-block capabilities.
- `docs/COMPONENT_LIBRARY.md`, containing both published and internal implementation records.

The PHP renderer and REST Contract read the same authoring Registry. The repository does not maintain a separate handwritten renderer list or TypeScript component union. A consumer may generate types from the published JSON contracts.

Verification compares IDs, order, versions, fields, variants, deprecation state, and Page Schema entries against the authoring Registry. Generated artifacts must be regenerated and committed whenever that source changes.

## Published Component Contract

`contracts/component-registry.json` contains only implemented capabilities that the CMS is allowed to use. Each record publishes:

- `id`, `name`, `category`, and `purpose`.
- `status` and explicit `deprecated` state.
- Supported semantic `variants`.
- `inputSchema` for `data`.
- Derived `requiredFields` and `optionalFields`.
- A canonical `{ type, variant, data }` example.

Internal components and proposed future components are not included. An unknown component ID or variant is invalid CMS output and must be rejected before publication.

The live WordPress runtime exposes the richer authoring Registry for frontend inspection at:

    GET /wp-json/stc/v1/component-registry

CMS synchronization should consume the generated shape from:

    GET /wp-json/stc/v1/component-registry/generated
    GET /wp-json/stc/v1/page-schema

Both generated endpoints return the same shapes as the repository artifacts and include stable ETag, Last-Modified, and public Cache-Control headers. The repository Contract remains the preferred build-time source; the generated endpoints support deployed runtime synchronization.

Production CMS configuration should use the deployed frontend origin and exact deployed commit:

```text
FRONTEND_CONTRACT_SOURCE_REPOSITORY=https://github.com/Weapon-Tsang/solo-to-china
FRONTEND_COMPONENT_REGISTRY_SOURCE=https://solotochina.com/wp-json/stc/v1/component-registry/generated
FRONTEND_PAGE_SCHEMA_SOURCE=https://solotochina.com/wp-json/stc/v1/page-schema
FRONTEND_CONTRACT_COMMIT_SHA=<deployed frontend commit>
```

Do not set the commit value to an uncommitted working tree or a commit that has not been deployed.

## Commercial Capability Boundary

Registry 1.1 adds `affiliate_booking_card`, `affiliate_search_card`, `affiliate_banner`, and `affiliate_promotion_card`. The CMS may select them only after QA and must pass a complete Commercial Block; the frontend never infers commercial placement from title, content type, body text, taxonomy, or template.

New affiliate destinations and embed sources must be HTTPS and resolve to `trip.com`, `tripcdn.com`, `ctrip.com`, or a legitimate subdomain. Raw HTML, script, srcdoc, data/javascript URLs, credentials in URLs, inline event handlers, and unknown fields are rejected. Search boxes and dynamic banners accept only the documented structured `embed_config` fields and enums.

The browser sends privacy-minimal impression/click events only to the same-origin `POST /wp-json/stc/v1/commercial-events` relay. The relay validates a 4 KiB maximum payload, allowlisted fields/enums, same-origin requests, rate limits, and deduplication before forwarding. Configure `STC_COMMERCIAL_EVENTS_ENDPOINT` and `STC_COMMERCIAL_EVENTS_TOKEN` only in the server process environment. The relay does not persist or return the token, and forwarding failures do not interrupt visitor navigation.

## Page Payload

`contracts/page-schema.json` defines this shape:

```json
{
  "metadata": {
    "pageId": "guide-123",
    "title": "Forbidden City for first-time visitors",
    "slug": "forbidden-city-first-time-visitors",
    "contentType": "attraction-guide",
    "presentation": {
      "article_hero": { "variant": "attraction" },
      "share_this_page": true,
      "table_of_contents": true
    }
  },
  "blocks": [
    {
      "type": "paragraph",
      "variant": "default",
      "data": { "text": "Reserve before arrival and carry the booking passport." }
    },
    {
      "type": "tip",
      "variant": "default",
      "data": { "body": "Use the signed entrance shown on your reservation." }
    }
  ]
}
```

The order of `blocks[]` is the final page render order. The frontend renders that order and must not infer a checklist, FAQ, warning, ticket reminder, affiliate CTA, Share action, or TOC from `contentType`.

Presentation capabilities remain explicit page metadata because they belong to the page shell rather than the ordered editorial body. Their IDs and variants still come from the Component Registry.

## Versioning

The Component Contract follows semantic compatibility rules:

- Patch: compatible corrections that do not require CMS output changes.
- Minor: backward-compatible component, optional field, or variant additions.
- Major: removed or renamed IDs/variants, newly required fields, incompatible schema changes, or changed semantics.

Every version change must be recorded in `docs/COMPONENT_CHANGELOG.md`. Pure visual work does not change this Contract unless it alters CMS-visible semantics, input, or supported behavior.

Deprecated IDs remain published with `status: deprecated` and `deprecated: true` until a documented compatibility and removal path is complete. Visual refactoring alone never changes a stable component ID.

## Capability Publication Flow

1. Frontend implements and tests a component for a real content need.
2. Frontend adds its stable ID, variants, schema, status, and implementation metadata to the authoring Registry.
3. Frontend adds it to the Component Gallery and responsive/accessibility coverage.
4. Frontend regenerates the published Contract, Page Schema, and Component Library.
5. Frontend updates the Component Changelog and version when CMS-visible capability changes.
6. Verification proves generated artifacts match the Registry.
7. Only after the frontend release is available may the CMS emit the new component or variant.

