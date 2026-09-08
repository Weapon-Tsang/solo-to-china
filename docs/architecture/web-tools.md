# SoloToChina Web Tools Architecture

Current Tools Plugin version: `0.25.0`

This release adds two guest-first utilities while preserving Ticket Booking Window:

- `/tools/find-this-place/` accepts one to four photos or screenshots of the same location and asks a configured server-side vision provider for one structured, uncertainty-aware place result.
- `/tools/taxi-card/` resolves a destination to a canonical Chinese place name and renders a copyable, full-screen Driver Mode card.
- `/tools/` lists both utilities and Ticket Booking Window.

## Ownership

The Tools Plugin owns page bootstrap, shortcodes, canonical place records, input validation, rate limiting, provider adapters, REST responses, destination resolution, temporary-image handling, and tool interaction JavaScript. The Parent Theme owns the generic page shell and the Registry-driven `destination_card` adapter. The Child Theme owns visual tokens, responsive presentation, and Driver Mode styling. The independent CMS may select `destination_card`; it does not call a provider or supply unverified Chinese address data.

## Endpoints and shortcodes

The public same-origin endpoints are:

- `POST /wp-json/stc/v1/place-finder` with one to four multipart `images[]` fields and optional `city_hint`. The legacy single `image` field remains accepted for compatibility.
- `POST /wp-json/stc/v1/taxi-card` with JSON `query` or canonical `entity_key`.

The shortcodes are:

- `[solo_to_china_tools_directory]`
- `[solo_to_china_place_finder]`
- `[solo_to_china_taxi_card entity_key="forbidden-city"]`
- `[solo_to_china_ticket_tool]`

The Registry page-block adapter is:

```json
{
  "type": "destination_card",
  "variant": "default",
  "data": {
    "entity_key": "forbidden-city",
    "title": "Show the Forbidden City to a driver"
  }
}
```

`entity_key` is required and resolves only against Plugin-owned canonical data. Unknown keys fail closed with an honest unavailable message.

## Provider boundary

No browser credential is used. The default adapters read server process environment variables (or deployment-defined PHP constants):

```text
STC_PLACE_VISION_ENDPOINT=https://provider.example/v1/identify
STC_PLACE_VISION_TOKEN=<server-only token>
STC_PLACE_RESOLVER_ENDPOINT=https://provider.example/v1/resolve
STC_PLACE_RESOLVER_TOKEN=<server-only token>
```

Endpoints must be HTTPS. Tokens must remain in the hosting secret store and must never be written to Theme files, JavaScript, WordPress options, post content, the database, Git, response bodies, or logs. The adapters can be replaced with the `stc_tools_place_vision_provider` and `stc_tools_place_resolver_provider` filters. With no configured provider, image identification returns a safe `503`; Taxi Card continues to resolve the built-in verified catalog.

The provider response uses semantic confidence (`high`, `medium`, or `low`) and match level (`exact_viewpoint`, `attraction`, `neighborhood`, `city`, or `unknown`). The UI never fabricates a probability. High confidence may show one primary candidate, medium confidence requires the traveler to choose among candidates, and low confidence shows an uncertainty state with retry/city-hint guidance. Provider-returned coordinates and street addresses are discarded. An address is displayed only when it comes from a locally verified canonical record.

## Verification labels and sources

Each field has a provenance label:

- `VERIFIED`: curated source-backed canonical data.
- `AI_INFERRED`: a provider observation or name not independently verified by the site.
- `UNKNOWN`: absent or not safe to claim.

The Forbidden City record uses the Palace Museum's official visitor address, `北京市东城区景山前街4号`. Other catalog records omit street addresses unless independently verified; Taxi Card falls back to the canonical Chinese city rather than inventing an address or drop-off point.

Guide links are emitted only for a currently published WordPress post matched by private `_stc_entity_key` metadata or an allowlisted fallback slug. Drafts and missing posts do not produce links.

## Privacy and abuse controls

Each request accepts one to four same-location JPEG, PNG, or WebP photos, limited to 20 MB each and 60 MB total. Every photo is also limited to 12,000 pixels per side and 40 megapixels. Client checks improve feedback, but the server repeats count, type, MIME, byte-size, total-size, and dimension validation. Each request copy is read directly from PHP's temporary upload and unlinked immediately; no file enters the WordPress Media Library or permanent uploads directory. Responses use `private, no-store` and no image data is returned. For production, configure PHP `upload_max_filesize` to at least `20M`, `post_max_size` (and any reverse-proxy body limit) to at least `64M`, and `memory_limit` to at least `256M`.

Anonymous rate limits use a short-lived WordPress transient keyed by an HMAC of the request IP. The raw IP is not stored. Vision requests allow 8 attempts per 10 minutes; destination resolution allows 30. Public errors contain actionable user language but no provider URL, token, stack trace, request payload, or upstream response body.

The finder-to-taxi handoff uses a canonical `entity_key` query parameter, so the traveler never needs to type the verified place again. A provider-only candidate cannot create a Taxi Card until it maps to a canonical record; unverified AI text is never promoted into driver instructions. No handoff state is stored in the browser or WordPress.

## Accessibility and responsive behavior

Upload works by multi-file picker, drag-and-drop, or clipboard paste. The interface tells travelers to use photos from one location and recommends complementary evidence such as a wide view, entrance/sign, nearby street, and different angles. Status and copy messages use live regions. Candidate choices and Driver Mode are keyboard-operable. Driver Mode is a real dialog with Escape handling, focus containment, focus return, high-contrast Chinese text, and a copy action.

The layouts are designed and tested at 375, 390, 430, 768, 840, and desktop widths. Controls retain touch targets, long names wrap, grids collapse without horizontal overflow, and reduced-motion users do not receive decorative transitions.

## Verification

Run static and Contract checks from the repository root:

```powershell
.\scripts\verify-web-tools.ps1
.\scripts\verify-component-registry.ps1
.\scripts\verify-content-contract.ps1
.\scripts\verify-project.ps1
```

Start a disposable WordPress Playground and run live checks:

```powershell
.\scripts\start-preview.ps1 -Port 9400
.\scripts\verify-content-runtime.ps1 -BaseUrl http://127.0.0.1:9400
.\scripts\verify-web-tools-runtime.ps1 -BaseUrl http://127.0.0.1:9400
```

Playground is intentionally provider-free: Taxi Card resolution is fully testable, while a valid finder upload must fail safely rather than pretend to identify a place.
