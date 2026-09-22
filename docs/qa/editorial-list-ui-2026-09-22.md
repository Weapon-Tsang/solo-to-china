# Editorial guide list UI — local acceptance

Date: 2026-09-22. Branch: `codex/published-media-refresh`. Starting HEAD: `b231b1d54915ceef6a7f52b907ba5d7c1d79463d`. The pre-existing untracked `output/` directory was preserved. Remote: `origin` → `Weapon-Tsang/solo-to-china`.

## Scope and baseline

- Production baseline: Parent Theme `0.33.8`. This release: Parent `0.33.9`, Child `0.13.1`, Tools `0.26.0`, Registry `1.4.1`, Content Contract `2.1.0`, Publish Package `1.0.0`.
- `stc_render_guide_card()` in Parent `functions.php` supplies archive, category, search, and core-page Latest Guides cards. WordPress featured media comes from `get_post_thumbnail_id()` and `wp_get_attachment_image()`; the CMS publish adapter sets it from `featuredMediaId` when supplied. No separate card title or card excerpt field is declared, so the renderer uses the WordPress title and excerpt.
- Before the edit, cards had a 4:5 image, type badge, date, border, and 28-word excerpt trim. The local category fixture had no featured image. A local-only WordPress REST update assigned the existing Forbidden City media attachment to its matching fixture to exercise the image state; no repository fixture or production content was changed.

## Changed files

- `wp-content/themes/solo-to-china/functions.php`: remove badge/date markup and excerpt truncation; use the existing WordPress responsive attachment image with layout-aligned `sizes`; add the inline Read guide arrow.
- `wp-content/themes/solo-to-china/assets/css/main.css`: 16:9 editorial cards, natural text flow, one/two/three-column grid, and 20/28/32px list gutters.
- `scripts/verify-project.ps1`: replace obsolete badge requirements with checks for their removal, responsive media, grid, and untrimmed excerpt.
- `wp-content/themes/solo-to-china/style.css`, `functions.php`, and `README.md`: mark the Parent Theme release as `0.33.9` for production upgrade and asset versioning.
- `scripts/package-release.ps1` and `docs/deployment/wordpress-install.md`: align the release manifest and installation handoff with Parent `0.33.9`.
- `docs/qa/editorial-list-ui-2026-09-22.md`: record local acceptance and evidence.

## Acceptance gates

| Gate | Result | Evidence |
|---|---|---|
| UI-001 | Pass | Card DOM has no type badge. |
| UI-002 | Pass | Card DOM has no time/date. |
| UI-003 | Pass | Card DOM has no author/avatar. |
| UI-004 | Pass | Post metadata and taxonomy remain in WordPress; only list presentation changed. |
| UI-005 | Pass | Card media computes to approximately 16:9 at tested widths. |
| UI-006 | Pass | `object-fit: cover`, centered crop, 8px radius. |
| UI-007 | Pass | 375/390/430px widths compute to approximately 1.78 image ratio. |
| UI-008 | Pass | Image card is 372/381/403px tall at 375/390/430px; no fixed card height. |
| UI-009 | Pass | One column below 640px, two at 768px, three at 1024/1440px. |
| UI-010 | Pass | Single article occupies one normal grid cell. |
| UI-011 | Pass | WordPress attachment emits `srcset`, `sizes`, `width`, `height`, `alt`, lazy loading, and async decoding; 390px browser selected the 768w intermediate file. |
| UI-012 | Pass | No-image card renders text and link with no empty media box. |
| UI-013 | Pass | No model call, CMS image selection, or live transcoding added. |
| UI-014 | Pass | Simulated 200% card text makes the card grow to 712px; CTA remains visible and page has no horizontal overflow. |
| UI-015 | Pass | 320px page has no horizontal overflow. |
| UI-016 | Pass | Tab reaches card link; focus outline is solid 3px. |
| UI-017 | Pass | Home image-card CSS and rendering were not changed. |
| UI-018 | Pass | Archive and core-page Latest Guides use the same card renderer; both image and no-image states checked. |
| UI-019 | Pass | H1, SEO title, slug, canonical, robots, and schema code were not changed. |
| UI-020 | Pass | No new runtime dependency or font request. |

## Verification

- `git diff --check`: pass.
- `./scripts/verify-upgrade.ps1 -BaseUrl http://127.0.0.1:9400`: pass (page architecture, Registry, Contract, Web Tools, project checks, experience contracts, Child content runtime, Web Tools runtime). PHP CLI is unavailable; live WordPress Playground executed the changed PHP successfully.
- `./scripts/verify-content-runtime.ps1 -BaseUrl http://127.0.0.1:9402 -ParentOnly`: pass.
- Tools-disabled preview on port 9404: category, core guide page, and article return HTTP 200 with one H1 and normal main content.
- Chromium local preview: 320, 375, 390, 430, 768, 1024, 1440px had no horizontal overflow. Search results with seven cards verified populated grid columns. Mobile menu opened, closed with Escape, and returned focus. Homepage, Survival Kit, City Guides, Attraction Guides, article, Tools, navigation, footer, and Share markup remained present.

## Visual evidence

- `output/playwright/editorial-list-390-image.png`
- `output/playwright/editorial-list-390-no-image.png`
- `output/playwright/editorial-list-768-image.png`
- `output/playwright/editorial-list-1440-image.png`
- `output/playwright/editorial-latest-390-image.png`

These screenshots use disposable local WordPress fixtures. Physical-device and production-browser acceptance has not been performed.

## Boundaries

CMS dependencies: none. No CMS repository, schema, Publish Package, or image pipeline was modified. At this local-acceptance checkpoint, no production WordPress content had been modified and no commit, push, or deployment had been performed.
