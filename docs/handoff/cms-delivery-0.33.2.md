# CMS delivery handoff: parent theme 0.33.2

Date: 2026-09-15
Status: local DEVELOPMENT changes only; not committed, pushed or deployed.

## Compatibility

- Parent theme: 0.33.2
- Child theme: 0.12.0
- Tools plugin: 0.26.0
- Component Registry: 1.4.0 (unchanged)
- Content Contract: 2.1.0 (unchanged)
- CMS partner: 2.0.29 / content strategy 3.4 / database schema 70

The frontend continues to own Registry validation, Gutenberg serialization, SSR rendering and styles. The CMS chooses content, component order and presentation flags.

## Changes

- `inc/cms-articles.php` returns the exact stored commercial slot receipt and page-payload hash after create/update.
- The scoped final-preview capability is bound to one draft, CMS draft identity and current page hash, expires after 15 minutes, revokes the prior ticket and never grants an editor session.
- The raw preview bearer travels in a URL fragment, is exchanged by same-origin POST, and becomes an HttpOnly `SameSite=Lax` scoped cookie. The final preview URL contains no bearer token.
- Preview responses are private/no-store/noindex/no-referrer; the main query is forced to the bound draft and the admin bar is hidden.
- Commercial impression/click JavaScript is not enqueued for scoped preview traffic.
- Existing official affiliate URLs and query parameters are not rewritten. Renderers keep disclosure plus `sponsored`, `nofollow` and `noopener` behavior.
- Verification scripts and version surfaces expect parent theme 0.33.2. UTF-8 literal checks are safe under Windows PowerShell 5.1.

## Local verification

- `scripts/verify-project.ps1`: PASS (PHP CLI unavailable, so PHP CLI lint was skipped).
- `scripts/verify-content-contract.ps1`: PASS.
- `scripts/verify-page-architecture.ps1`: PASS.
- CMS cross-repository Registry/checksum check: PASS.
- Earlier active-theme Playground and Playwright run: long guide at 360/390/430/1440 had no page overflow; fixture commercial modules rendered visible CTA and expected links; Gutenberg showed no invalid-block warning.
- Fresh PHP 8.3 / WordPress Playground boot after the fragment exchange: PASS in child/editor and ParentOnly modes; the harness reported 42 / 40 PHP files syntactically valid and rejected stale/tampered tokens.
- Anonymous Playwright fragment exchange: PASS in child and ParentOnly themes; it reached post 25 with the expected H1, no bearer in the final URL, four visible commercial modules, no admin bar and no commercial event script. A `p=1` attempt redirected to the capability-bound post 25, and an attempted REST edit returned 401 `rest_cannot_edit`.
- The browser audit exposed tool/commercial headings in the article TOC; those renderer headings are now explicitly excluded and the corrected preview was rechecked.

The pre-existing modification to `docs/qa/evidence/release-artifacts.json` is user-owned and must not be overwritten or included as repair evidence.

## Authorized release order (future only)

1. Re-run frontend verification with PHP available and boot both child/editor and ParentOnly Playgrounds.
2. Validate a successful and rejected preview-ticket exchange, no raw token in proxy access logs, no commercial event emission, and no access to another post.
3. Package and retain immutable frontend 0.33.2 artifacts/checksums.
4. Deploy frontend before CMS 2.0.29 so delivery receipts and preview exchange exist when CMS begins requesting them.
5. Canary one draft, then release CMS. Production data repair remains a separate explicit action.

## Rollback

Switch the active frontend artifact back to retained 0.33.1, invalidate theme asset caches, and switch CMS back to retained 2.0.28 if needed. Do not delete shared media or affiliate assets. For a single repaired draft, restore its saved pre-repair Gutenberg content/meta and commercial overlay mapping only; stop if it was published or edited by a human.
