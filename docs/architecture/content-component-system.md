# SoloToChina Content Component System

Current Content Contract version: `1.0.0`  
Compatible Parent Theme version: `0.22.0`

## Ownership

WordPress owns component capability, rendering, design, the article shell, responsive behavior, and accessibility. The external `solo-to-china-CMS` owns article content, structure, component selection/order/data, and canonical content types.

The Tools plugin continues to own Ticket data, date calculations, reminder storage, scheduling, consent, and any future notification delivery. Neither the Parent nor Child Theme may duplicate that business logic.

## Canonical Contract

The single machine-readable source of truth is:

```text
wp-content/themes/solo-to-china/content-contract/content-contract.v1.json
```

It contains only semantic capability information:

- `contract_version`
- compatible `theme_version`
- stable guide types and category routing
- component definitions, fields, required/optional fields, render modes, renderer keys, anchor support, and accessibility notes
- runtime capability flags

It deliberately excludes design token values, CSS decisions, arbitrary CMS classes, internal research identifiers, source authorization records, and publishing history.

## Public REST Endpoint

CMS clients read the Contract from:

```text
GET /wp-json/stc/v1/content-contract
```

The endpoint is public and read-only. It returns the canonical JSON without configuration or mutation APIs. Responses include `ETag`, `Last-Modified`, and a short public cache policy so a CMS can synchronize capabilities without reading the GitHub repository or copying CSS.

## Versioning

Contract and Theme versions are independent:

- PATCH: non-breaking corrections, descriptions, or visual implementation changes.
- MINOR: backward-compatible components or optional fields.
- MAJOR: removed/renamed components, required-field changes, or field-type changes.

The Theme version identifies the runtime release that implements the Contract; it is not the Contract version.

## Guide Types And Routing

| Guide type | WordPress category | Shell behavior |
| --- | --- | --- |
| `survival-kit` | `survival-kit` | Survival Kit shell |
| `city-guide` | `city-guides` | City Guide shell |
| `attraction-guide` | `attraction-guides` | Attraction Guide shell |
| `travel-guide` | `travel-guides` | Default article shell |

REST-enabled post metadata:

- `_stc_guide_type`
- `_stc_content_contract_version`

Both fields have allowlist schemas, sanitization, and authenticated edit capability checks. `_stc_guide_type` is checked before taxonomy fallback. Existing category/tag-based articles therefore remain compatible, while new CMS posts can select a guide shell explicitly without title guessing.

## Component Definitions

Contract v1 exposes 16 capabilities:

- Core Gutenberg: `paragraph`, `heading`, `list`, `image`
- Editorial/GEO: `quick_answer`, `key_takeaways`, `quick_facts`, `tip`, `warning`, `steps`, `checklist`, `comparison_table`, `faq`
- Dynamic/contextual: `planner_cta`, `ticket_reminder`, `affiliate_cta`

Core and editorial components use native Gutenberg blocks plus stable `stc-content-block--*` semantic classes. Dynamic components declare renderer keys; the CMS stores only intent and safe parameters, never copied feature HTML.

## Block Anchors And Images

Components marked `anchorable` accept stable public anchors suitable for section navigation and media placement such as `getting-there`. The image capability accepts a WordPress Media ID, alt text, optional caption/role, and an optional `after_block_id` relation.

Internal evidence, claim, source, or authorization data must never be serialized into public HTML attributes. WordPress only renders already-ingested Media through normal responsive image and caption behavior.

## Backward Compatibility

- The existing `single.php` and unrestricted `the_content()` flow remain in place.
- Historical Gutenberg posts require no Contract metadata and continue using taxonomy fallback.
- The Parent Theme remains a functional fallback without the Child Theme.
- Content structure is intentionally free; components may appear in different counts and orders without creating topic-specific templates.
