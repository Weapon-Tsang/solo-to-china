# Header menu and search — Parent 0.33.14

Verified on 2026-09-28 with local WordPress Playground and Chromium through Playwright CLI.

- Parent + Child + Tools, Parent-only, and Tools-disabled: widths 320, 390, 768, 1024, 1440px.
- Menu has equal viewport side insets; opening it does not change header height. Closed menus leave no animated layout row.
- Homepage inline search is hidden; header search is an unbordered 44px icon at every width.
- Search opens a native modal, focuses its input, closes with Escape, and restores focus. Reduced-motion mode disables its entrance animation.
- Homepage to City Guides navigation, browser back, and a Beijing search submission passed.
- Navigation remains visible without JavaScript; search retains its native URL fallback.
- Header stays within the 320px viewport with a 200% root font size on home and City Guides. The same zoom check exposed separate homepage section-heading/View all overflow outside the header; that existing surface was not changed.
- JavaScript syntax checks, git diff whitespace checks, and release registry verification passed.
- Follow-up: all inline guide search fields are hidden with JavaScript; homepage header overlays the Hero with a transparent background. The search dialog expands from the icon center and reverses on Close or Escape. Home, City Guides, and results were exercised at 390px and 1440px, including repeated open/close and reduced motion.

Visual evidence is in `output/playwright/header-menu-fixed.png`, `collection-menu-fixed.png`, and `header-search-fixed.png`.

Only the Parent package needs updating for this fix. Child remains 0.13.2 and Tools remains unchanged. At this local test checkpoint, no production deployment or physical iPhone/Safari verification was performed. Removing the menu's layout animation fixes the local blank-header behavior; production server response time was not measured.
