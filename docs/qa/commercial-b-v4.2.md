# Commercial coupon B: local verification (2026-09-21)

The Parent Theme renders CMS-selected commercial assets as Option B coupon tickets. The left and right edges use small, varied semicircular and curved cutouts across the full card height; the upper and lower edges remain straight. The claim stub keeps the dotted tear line and centered filled airplane, bed, and train pictograms labeled Flights, Hotels, and Tickets. A pale Good Trips Ahead postmark overlaps the Tickets side with uneven printed edges, faint ink marks, and waving cancellation lines. All cards end with `Explore more of China for less- your next adventure awaits.`

Hotel, flight, train, transfer, attraction, and tour offers use the site owner's requested amounts; planning cards keep `Build your itinerary with Trip.Planner.` The Temple of Heaven, Zhangjiajie, and Great Wall appear in natural color and fade into the pale blue ticket background. Static banner media is retained as a small source thumbnail; embedded commercial assets retain their functional media. No repeated commission sentence or disclosure-page link appears in the cards or homepage planner block.

| Check | Result |
| --- | --- |
| Remote branches | Three obsolete `origin/codex/*` heads were confirmed ancestors of `origin/main` and deleted; only `origin/main` remains. |
| PHP and project runtime | WordPress Playground ran the standalone commercial renderer checks during bootstrap. `scripts/verify-upgrade.ps1 -BaseUrl http://127.0.0.1:9400` passed. |
| Browser | At desktop, 390 px, and 320 px, each card showed the three icon labels in order and a 44 px button. There was no document horizontal overflow. The browser resolved the left/right edge masks, and the console reported zero errors or warnings. |
| Theme combinations | Parent + Child + Tools (9400) passed the runtime checks. Parent-only (9401) gallery and Tools-disabled (9402) long-guide page returned 200 with the icon trio and fixed copy, without the old notice. |
| Visual previews | `output/playwright/commercial-b-desktop.png`, `commercial-b-mobile.png`, and `commercial-b-mobile-320.png` show the final hotel ticket. The same directory contains flight, attractions, planner, and static-banner variants. Gallery: `http://127.0.0.1:9400/design-system/#gallery-commercial`. |

The discount figures are the site owner's requested display copy. Provider landing pages were not checked for redemption eligibility. No production deployment or CMS draft rewrite was performed.
