# Frontend upgrade v1 — actual QA record

Date: 2026-09-12. Release: Parent 0.33.0 / Child 0.12.0 / Tools 0.26.0 / Registry 1.4.0. The 42-item [implementation ledger](../upgrades/frontend-experience-v1.md) covers phases 0–4. Local implementation and regression work is complete; production and external-service acceptance is not complete.

## Environment and scope

- Windows host; PowerShell **7.6.5**, Node **24.14.0**, Python **3.13.5**. Python QA dependencies are isolated under ignored `.tools/qa-python`.
- WordPress Playground CLI **3.1.52**, WordPress **7.0.4**, actual PHP-WASM **8.3.32**. Three independent disposable installations: `9411` parent+child+plugin, `9412` parent+plugin, `9413` parent+child with plugin inactive. Fixtures are not packaged.
- Headless Chromium **147** and Playwright WebKit **26.5**, on this Windows host. WebKit's Safari/macOS user-agent string is emulation, not a physical Mac/iPhone test. Viewports and touch are simulated.
- Real local HTTP/WordPress database, multipart processing, Media and Gutenberg were exercised. Paid recognition/resolver providers were **local controlled stubs**; no paid provider calls, production writes or external CMS publish occurred.
- Initial branch `main`, HEAD `f44ce1092ced93dfb47d9b3eae83d0d5e4b97086`, 22 existing tracked dirty files preserved. Baseline static checks passed. Initial mobile screenshot/resource observations exist under ignored `output/playwright/upgrade-baseline/`. No trustworthy old-version LCP/CLS/INP baseline was captured.

## Results

| Check | Actual result | Evidence |
|---|---|---|
| Existing page architecture, registry, content contract, web tools and project static checks | PASS, with assertions updated for adopted new behavior | `scripts/verify-*.ps1`; original checks retained |
| Native `php -l` | NOT RUN: PHP CLI unavailable | Existing project verifier explicitly reports skip |
| Equivalent owned PHP syntax validation | PASS: 42 PHP files parsed with `TOKEN_PARSE` on PHP 8.3.32 | [invariants.json](evidence/invariants.json) |
| Existing PHP commercial component harness | PASS during clean Playground boot | All three blueprints execute `verify-commercial-components.php` |
| Existing content runtime | PASS: child integration and parent-only integration | `verify-content-runtime.ps1` |
| Existing tools runtime | PASS after resetting disposable fixture rate counters | `verify-web-tools-runtime.ps1`; local final runtime log |
| JSON schemas, catalog and responsive files | PASS: 9 sourced catalog entries, 45 image variants, 4 new CMS examples | `verify-experience-contracts.py` |
| Page/width/SSR regression | PASS: 16 routes, one H1 each, correct 200/404, no horizontal overflow; 10 widths | [experience-browser.json](evidence/experience-browser.json) |
| Chromium tool interactions | PASS: 60 assertions, no recorded page errors | [tool-interactions.json](evidence/tool-interactions.json) |
| WebKit tool interactions | PASS: same 60 assertions, no recorded page errors | [webkit-tool-interactions.json](evidence/webkit-tool-interactions.json) |
| Native share, live REST errors, fallback combinations | PASS: 46 assertions, 10 real multipart requests to WordPress with local stub | [experience-edges.json](evidence/experience-edges.json) |
| Date, long names, sticky Driver controls, scoped paste, return navigation, More focus | PASS: 18 assertions | [final-states.json](evidence/final-states.json) |
| Slow-image More, decoded screenshots and navigation contrast | PASS: 11 assertions | [release-boundaries.json](evidence/release-boundaries.json) |
| Fresh catalog/lease/rate/TOC/resolver invariants | PASS: 19 assertions in each plugin-enabled installation; 2 when inactive | [invariants.json](evidence/invariants.json) |
| Real Gutenberg editor | PASS: 41 blocks, zero invalid blocks, two editable image blocks including nested step image | [editor-browser.json](evidence/editor-browser.json) |
| Release ZIP contents and SHA-256 | PASS: all 115 parent / 13 child / 15 plugin files byte-identical to owned source | [release-artifacts.json](evidence/release-artifacts.json), [manifest](evidence/release-manifest.txt) |
| Physical devices / production gateway / CDN / external CMS | NOT RUN | Requires user-controlled environment, credentials or devices |

These counts belong to individual suites; they overlap and should not be summed as independent product requirements. Two runtime suites were rerun after relevant changes. A later combined run exhausted the intentional 8-attempt vision limit and failed upload expectations with rate limiting; after resetting only the disposable test counter, the tools runtime passed again. Rate-limit assertions themselves passed; the product limit was not weakened to satisfy repeated tests.

Coverage includes 1–4 image append/delete/reorder, local paste scope, EXIF rotation, transparent PNG, long screenshots, low-resolution text, invalid formats/corrupt images/large bytes or pixels; cancel/A→B/late response/late finally/retry and timeout; missing provider configuration; 413/415/429/503/504, malformed JSON/missing fields/wrong schema; confidence and canonical provenance; partial/unknown destination, same-name city selection, Driver Mode, clipboard denial/no API, multi-instance isolation; unknown/expired/past ticket dates and Los Angeles device versus China date; old IDs/variants/payloads, no-JS reading/navigation, 200% text and reduced motion. The 45-second timeout test accelerates its clock only in the fixture.

## Performance observations

Same local Chromium session, 390×844 viewport, device scale 1, no CPU/network throttle, three page navigations after earlier browsing. Browser/OS cache state was not forced cold. PerformanceObserver results are laboratory samples, not field p75 or Lighthouse scores.

| Observation | Value |
|---|---|
| Home LCP, three samples | **768 / 796 / 772 ms** (median 772 ms) |
| Home CLS, three samples | **0 / 0 / 0** within the observation window |
| Long tasks observed during those windows | None |
| Mobile selected hero | `hero-home-640.webp`, **32,260 bytes** |
| Original retained hero source | `hero-home.png`, **2,009,572 bytes** |
| 960 / 1600 WebP hero | 60,358 / 118,874 bytes |
| Observed resource bodies per sample | 357,280 bytes; transfer accounting 361,780 bytes; 15–16 entries |
| Homepage tool execution | No `tools.js` / `place-finder.js` requests; no original PNG hero request |
| Actual image preparation example | Source hero PNG ~2 MB → UI reports **215 KB**; **260 ms** final harness wall time (earlier sample 236 ms), Worker supported, no observed main-thread long task |

The resource totals exclude the HTML navigation document, cover the observation window only, and include browser/WordPress assets and near-viewport lazy cards. One sample contains a zero-byte 1600px hero resource entry; the painted `currentSrc` is the 640px variant. Original mobile observations included the 2 MB PNG and Tools execution/CSS (21,064/14,881 bytes). This supports the asset-selection and loading changes, **not** a before/after CWV percentage claim. Parent main CSS grew to 51,982 bytes for shared fallbacks; child home CSS reduced to 3,758 bytes. No claim of global CSS shrinkage is made.

No field INP, physical slow-device memory profile, Lighthouse run, production network benchmark or old-version comparable CWV run is available. The Worker timing is one example, not INP or a latency guarantee. Delayed-image testing held actual image responses until More expand/collapse completed; this is a race test, not a simulated 3G throughput score.

## Visual evidence and review

Representative screenshots were opened and visually inspected for hierarchy, line width, clipping, blank areas and tool readability. Home, city listing, long article and both main tools are saved at 390 and 1440px. Desktop listing/tool evidence uses actual 1440×1000 viewports to avoid full-page capture paint artifacts. The long guide is deliberately repetitive **fixture content**, not production travel advice.

| Page/state | Mobile | Desktop |
|---|---|---|
| Home | [390](evidence/upgrade-home-390.png) | [1440](evidence/upgrade-home-1440.png) |
| City collection | [390](evidence/upgrade-cities-390.png) | [1440](evidence/upgrade-cities-1440.png) |
| Long guide | [390](evidence/upgrade-article-390.png) | [Header viewport](evidence/upgrade-article-header-1440.png), [media viewport](evidence/upgrade-article-media-1440.png) |
| Finder, configuration unavailable | [390](evidence/upgrade-finder-390.png) | [1440](evidence/upgrade-finder-1440.png) |
| Taxi, verified name with partial arrival data | [390](evidence/upgrade-taxi-390.png) | [1440](evidence/upgrade-taxi-1440.png) |
| Extra states | [Share manual copy](evidence/upgrade-share-fallback-390.png), [step screenshot](evidence/upgrade-steps-390.png) | [Long Driver name](evidence/upgrade-driver-long-landscape.png), [Gutenberg](evidence/upgrade-editor-1440.png) |

The 8,133px-tall desktop full-page article capture has offscreen image/navigation paint omissions despite decoded image dimensions and correct computed styles. It is retained as a capture limitation, **not** marked as visual proof of those areas. Actual scrolled viewport screenshots above show the rendered images, marker legend, dark header links and sticky TOC. No image editing was used to repair evidence. Step screenshots previously collapsed into the number column; the product CSS was fixed and real viewport/width checks now pass. No generic overflow clipping was added to hide layout errors.

Long-name Driver Mode now uses a readable 28–48px range and a sticky top action bar. Tests scroll to the end at 320/390/844px and verify that Close remains in the viewport; Copy shares that same toolbar. The landscape screenshot intentionally shows the beginning of scrollable content, without truncating the actual destination string.

## Reproduction

Use **PowerShell 7 (`pwsh`)**. Windows PowerShell 5.1 was tried and failed parsing UTF-8 source strings; that interpreter is not claimed supported by these verification scripts. The consolidated entry requires version 7 and stops clearly on failure.

```powershell
python -m pip install --target .tools/qa-python -r scripts/requirements-qa.txt
python scripts/prepare-test-images.py
pwsh -File scripts/verify-upgrade.ps1

# Separate local terminals; keep each server alive.
pwsh -File scripts/start-preview.ps1 -Port 9411
pwsh -File scripts/start-preview.ps1 -Port 9412 -ParentOnly
pwsh -File scripts/start-preview.ps1 -Port 9413 -NoTools

pwsh -File scripts/verify-upgrade.ps1 -BaseUrl http://127.0.0.1:9411
pwsh -File scripts/verify-content-runtime.ps1 -BaseUrl http://127.0.0.1:9412 -ParentOnly
```

Use fresh fixtures or reset the disposable vision counter between repeated upload suites: `Invoke-RestMethod -Method Post http://127.0.0.1:9411/wp-json/stc-test/v1/reset`. This route exists **only** in local MU fixtures and must never be deployed. Parent preview auto-login is bypassed in the evidence collector with its local Playground cookie flag; no production authentication is bypassed.

```powershell
npx --yes --package @playwright/cli playwright-cli -s=stc-upgrade open http://127.0.0.1:9411 --browser=chrome
npx --yes --package @playwright/cli playwright-cli -s=stc-upgrade run-code --filename=scripts/verify-experience-browser.js
npx --yes --package @playwright/cli playwright-cli -s=stc-upgrade run-code --filename=scripts/verify-tool-interactions.js
npx --yes --package @playwright/cli playwright-cli -s=stc-upgrade run-code --filename=scripts/verify-experience-edges.js
npx --yes --package @playwright/cli playwright-cli -s=stc-upgrade run-code --filename=scripts/verify-final-states.js
npx --yes --package @playwright/cli playwright-cli -s=stc-upgrade run-code --filename=scripts/verify-editor-browser.js
npx --yes --package @playwright/cli playwright-cli -s=stc-upgrade run-code --filename=scripts/verify-release-boundaries.js
npx --yes --package @playwright/cli playwright-cli -s=stc-webkit open http://127.0.0.1:9411 --browser=webkit
npx --yes --package @playwright/cli playwright-cli -s=stc-webkit run-code --filename=scripts/verify-tool-interactions.js
```

The scripts contain this workspace's fixture image path and local ports; adjust them when moving the checkout. WebKit installation may be required by the CLI. Inspect `### Result` versus `### Error`, not only shell exit codes. Raw logs/screenshots remain under ignored `output/playwright/`; curated JSON and unmodified PNG are tracked here. `scripts/collect-upgrade-evidence.py` refuses missing/failed logs and refreshes actual PHP invariants. See [deployment/rollback](../deployment/frontend-upgrade-v1.md) for the remaining user-controlled work.
