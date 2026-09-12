# Tools upgrade: contracts, privacy and operations

Current plugin: **0.26.0**. This document supersedes conflicting implementation details in earlier snapshots. The original guest-only product boundary remains: no accounts, saved history, ICS/JSON downloads, calendar import, PWA history or reminders.

## Find This Place

1–4 JPG/PNG/WebP source images, at most 20MiB each, 40 million pixels and 12,000px on an edge. Each selected image is processed sequentially. Supporting browsers use a short-lived Worker with OffscreenCanvas; other browsers use the same bounded canvas strategy on the main thread with yields between images. Cancel terminates a running worker. No original file is submitted by the browser.

Photos become at most 2048px on their longest edge. PNG/text-like and long images use up to 4096px, constrained to six million output pixels. The prepared blob must be at most 2.5MB. JPEG quality is .86, WebP text-image quality .92. EXIF orientation is applied; metadata is removed by pixel re-encoding; transparency is composited on white. Blob URLs, image bitmaps and canvas allocations are released. Unsupported HEIC gets a screenshot suggestion. A failed image does not discard other prepared files.

States: idle → processing → identifying → success / ambiguous / unknown / error. Cancellation is explicit. Every selection, removal, reorder, hint edit or cancellation advances a generation. Responses and `finally` clauses must match that generation before updating UI. A 45-second client deadline aborts the request; retry is manual, with no hidden paid retry. Paste is scoped to a focused finder and excludes text fields. The city hint is optional and visible from the start. Status announcements are short; results are outside the live region.

Before submission, PHP reports configuration availability from `STC_Place_Vision_Provider::is_available()` without a paid probe. This is a configuration check, **not a promise of live upstream health**. A gateway failure produces a service error and retry path. Purge cached finder HTML when changing provider configuration.

The server checks upload count, total size (60MiB), MIME and dimensions before decoding, checks configuration before re-encoding, then uses the WordPress image editor to create a metadata-free JPEG. Input upload and generated temporary files are removed in handled success/error paths; PHP/server temporary-directory cleanup remains necessary for killed processes. No WordPress Media attachment or persistent visitor image record is created.

## Provider boundary

Vision adapter: `STC_PLACE_VISION_ENDPOINT` and `STC_PLACE_VISION_TOKEN` (server constants or environment). HTTPS only; zero redirects; response body capped at 512KiB; request deadline 20–35 seconds depending on image count. Outbound body contains prepared image base64, count, optional city hint and `response_schema: stc-place-v2`. Tokens/endpoints are not localized into browser code.

`contracts/place-vision-response.schema.json` documents required status, confidence, match level and candidate collections. High confidence produces one likely match; medium requires candidate selection; low does not promote a place. Match precision (city/neighborhood/attraction/photo position) is separate from confidence. Unknown provider fields are discarded. Provider addresses never become verified by being returned by the model. A canonical entity key selects the local directory's identity/arrival fields. Even a verified photo spot's text must come from the directory, not the provider. Empty/invalid/non-JSON results and exceptions are sanitized service errors. HTTP 413/415/429/503/504 are preserved where appropriate; upstream diagnostic messages are never returned.

Resolver adapter: optional `STC_PLACE_RESOLVER_ENDPOINT` / `STC_PLACE_RESOLVER_TOKEN`; 12-second deadline. Known aliases resolve locally first. Legacy `stc-place-v1` `{schema,status:"resolved",entity_key}` is explicitly adapted; it is not silently treated as a vision-v2 body. The resolver can select an existing canonical record; it cannot publish an AI address.

## Rate and cost boundaries

- Per visitor: 8 vision requests / 10 minutes; 30 Taxi lookups / 10 minutes. Identity is an HMAC of `REMOTE_ADDR`; raw IP and query text are not stored by the plugin.
- Fixed expiry windows; atomic database leases serialize counter updates. Lease release checks the owner's value so a late request cannot unlock a newer request.
- Global vision concurrency defaults to 2; `STC_VISION_CONCURRENCY` accepts 1–8. Slots expire after 50 seconds, beyond the built-in HTTP deadline.
- Daily UTC request budget defaults to 100, configured by `STC_VISION_DAILY_LIMIT`. Failed upstream attempts count; frontend retries are explicit. This is a request ceiling, not a currency-denominated vendor spend guarantee. Configure the provider's own spend cap as well.
- Trust only server-normalized `REMOTE_ADDR`. Configure the web server's trusted reverse proxies correctly; arbitrary browser-supplied forwarding headers are deliberately ignored. An incorrectly configured proxy can collapse visitors into one quota.

## Destination catalog and Taxi Card

The bundled compatibility directory is `data/destinations-v1.json`, schema 1.0, data version 1.0.0, nine existing entities. It is now structured data rather than an inline PHP dictionary. Source URLs and review date 2026-09-12 support the identity/city fields; the Palace Museum has a sourced official contact address. All visitor entrances, vehicle drop-offs and exact viewpoints remain UNKNOWN because those fields were not verified. This directory does not claim nationwide coverage. Source pages are listed within every record.

Admin-only REST interfaces (normal WordPress authentication, `manage_options`):

```text
POST /wp-json/stc/v1/destination-catalog           full catalog JSON
POST /wp-json/stc/v1/destination-catalog/rollback  previous revision
```

Imports validate schema version, record types, keys, aliases, allowed fields and per-field provenance before storage. VERIFIED fields require nonempty values, HTTPS evidence and a valid nonfuture review date. The immutable revision uses SHA-256 of the accepted JSON; a guarded active pointer switches only after storage. Failed imports retain the active dataset; rollback returns to the previous revision, including the built-in fallback. Keep an external export/backup before making successive updates; this is a two-position rollback, not a full catalog administration product. `contracts/destination-catalog.schema.json` is the CMS/editor contract; server validation additionally checks URL safety and dates.

Stable `entity_key` is shared by collection, result, route, place card and related-guide rendering. Renames preserve the key and previous name as an alias. For mergers, migrate references and retain old keys/aliases until consumers are updated; `related_entity_key` is a relationship, not an automatic redirect. Published article relations are resolved at request time, exclude password-protected/draft posts, respect guide type and disappear on withdrawal. Local aliases accept name+city and punctuation normalization for disambiguation.

Taxi Card shows a confirmed Chinese name and clearly labels incomplete arrival data. Official postal/contact address, visitor entrance and vehicle drop-off are distinct fields. Native Driver Mode gives Chinese text priority and supports safe-area padding, long-name wrapping, scrolling, Escape and focus restoration. Copy includes Chinese city and available verified arrival details; refusal reveals a selectable textarea, including inside Driver Mode. Entity links prefill server-rendered cards; no JS still leaves the known destination readable.

## Tickets and privacy

Ticket Booking Window retains old shortcodes and the Registry `ticket_reminder` ID. It calculates calendar dates on a China-time basis. A rule needs `rule_checked_at`, `rule_source_url`, `rule_valid_until` and `booking_lead_days` to produce a current estimate. No rule or an expired rule produces an honest unknown state. The estimate is a suggested checking date, not inventory or an exact sale time. A ticket action appears only when a supported entity-specific `ticket_url` is configured; no generic Trip.com homepage is disguised as a ticket page.

Recognition and resolver REST success/error responses use `private, no-store`; administrative catalog responses are also private. Ordinary anonymous editorial HTML need not disable site caching. Do not log multipart bodies, base64, hints, raw queries, Authorization or gateway payloads at the reverse proxy/APM. The site can describe its own temporary-file behavior; it cannot promise provider deletion/retention until the actual gateway/vendor policy is confirmed. Confirm that policy and the deployed Privacy Policy before enabling real uploads.
