# Commercial B v4.1 verification ledger — 2026-09-21

This is a code-only presentation/contract change. It does not rewrite WordPress article bodies, affiliate URLs, attribution, or media. Numeric discount examples in the local Gallery are explicitly TEST DATA, not validated production offers.

| Area | Evidence | Remaining boundary |
| --- | --- | --- |
| Parent and child | Local WordPress Playground loads parent 0.33.6, child 0.13.1 and Tools 0.26.0. `scripts/verify-upgrade.ps1 -BaseUrl http://127.0.0.1:9400` PASS, including registry, content runtime and Tools runtime. | PHP CLI lint unavailable; PHP executes in real local WordPress. |
| Historical copy | `scripts/verify-commercial-components.php` exercises a nonempty old long commission sentence, the exact generated TOUR_ACTIVITY title/filler, preserved manual copy, legacy CTA, Planner and numeric eligibility. Local Gallery has ten commercial examples, one adjacent relationship notice, no visible `Paid link`, and unchanged sponsored/nofollow/noopener attribution. | Production drafts must be read back after installation. |
| Responsive and operation | Playwright at 320/390/768/1440 showed ten cards, one notice, no document overflow and CTA height at least 46px. Parent-only fallback, no-JS links, keyboard focus, hover and reduced-motion operation were exercised locally. | The 200% Gallery navigation can scroll horizontally; card content itself did not extend beyond the viewport. Physical device and three-run before/after performance comparison not completed. |
| Cross-repository | CMS `npm run check`, 753 tests, and working-tree cross-repo contract gate PASS. CMS-to-local-WordPress test delivery opens draft preview #40 from CMS, with parent/child assets loaded locally. | No paid model or production WordPress write used in local verification. |
| Production baseline, read-only | Eleven synced WordPress drafts contain eighteen commercial blocks. Three active assets have no `price_text` or verified campaign validity; there is no basis to render a real percentage. | Numeric production offer acceptance is pending reviewed campaign URLs/terms. Historical draft display can be corrected by runtime without body rewrite. |

Validation levels: L1 PASS; L2 PASS; L3 read-only shape audit PASS, production database replay NOT TESTED; L4 local browser PASS with noted 200%/performance limits; L5 NOT REQUIRED; L6 production-like release replay NOT TESTED. No production deployment or public-live acceptance is implied by this ledger.
