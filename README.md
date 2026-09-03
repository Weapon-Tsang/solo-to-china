# SoloToChina WordPress Project

SoloToChina is planned as a practical China independent travel platform for international travelers, especially solo travelers and first-time non-Chinese visitors.

Core product direction:

- Content-first travel guides
- Utility-driven tools
- Guest-first access
- Low-friction personalization later

Current repository status:

- The live WordPress site is online at `https://solotochina.com`.
- The current live site uses third-party WordPress theme/plugin packages.
- No custom SoloToChina theme or custom SoloToChina plugin was present in the exported `wp-content` audit package.
- Future development should happen in project-owned theme/plugin directories rather than editing third-party theme/plugin source directly.

Recommended future code ownership:

- `wp-content/themes/solo-to-china/` for the custom site theme.
- `wp-content/plugins/solo-to-china-tools/` for project-owned tools such as the Attraction Ticket Reservation & Reminder.

Current development branch deliverables:

- Custom SoloToChina theme `0.26.0` in `wp-content/themes/solo-to-china/`.
- SoloToChina Child Theme `0.8.0` in `wp-content/themes/solo-to-china-child/`.
- SoloToChina Tools plugin `0.22.0` in `wp-content/plugins/solo-to-china-tools/`.
- Release packaging script in `scripts/package-release.ps1`.
- WordPress/aaPanel install notes in `docs/deployment/wordpress-install.md`.
- Current progress handoff in `docs/handoff/current-progress.md`.
- Content Component System architecture in `docs/architecture/content-component-system.md`.
- CMS-facing generated Component Contract in `contracts/component-registry.json`.
- CMS page payload schema in `contracts/page-schema.json`.
- Registry-generated human catalog in `docs/COMPONENT_LIBRARY.md`, repository boundary in `docs/CMS_FRONTEND_CONTRACT.md`, capability history in `docs/COMPONENT_CHANGELOG.md`, and an internal Playground Gallery at `/design-system/`.

The Theme Registry at `wp-content/themes/solo-to-china/content-contract/component-registry.v1.json` is the authoring source. Run `scripts/generate-component-catalog.ps1` after an approved capability change; do not maintain the published Contract, Page Schema, or Catalog as separate manual component lists.

Registry `1.1.0` adds four QA-selected Commercial Blocks: Affiliate Booking Card, Search Card, Banner, and Promotion Card. WordPress publishes CMS-ready generated shapes at `GET /wp-json/stc/v1/component-registry/generated` and `GET /wp-json/stc/v1/page-schema`. Privacy-minimal impression/click events post only to the same-origin `POST /wp-json/stc/v1/commercial-events` relay; server forwarding is disabled until its environment variables are configured.

Generate install artifacts with:

```powershell
.\scripts\package-release.ps1
```

Run a real local WordPress preview with Parent Theme, Child Theme, and Plugin mounted from the repository:

```powershell
.\scripts\start-preview.ps1
```

The preview uses the official WordPress Playground CLI and defaults to `http://127.0.0.1:9400`.

Content Component System verification:

```powershell
.\scripts\verify-page-architecture.ps1
.\scripts\verify-component-registry.ps1
.\scripts\verify-content-contract.ps1
.\scripts\verify-content-runtime.ps1 -BaseUrl http://127.0.0.1:9400
.\scripts\start-preview.ps1 -Port 9402 -ParentOnly
.\scripts\verify-content-runtime.ps1 -BaseUrl http://127.0.0.1:9402 -ParentOnly
```

Use `.\scripts\start-preview.ps1 -Port 9403 -Editor` for the authenticated Gutenberg editor fixture. Preview modes are disposable and do not create production content.

The script creates:

- `dist/solo-to-china-theme.zip`
- `dist/solo-to-china-child-theme.zip`
- `dist/solo-to-china-tools-plugin.zip`
- `dist/release-manifest.txt`

The three zip files are intended for WordPress upload. Install the Parent Theme first, install and activate the Child Theme second, then install and activate the Tools plugin. The manifest records artifact versions and SHA256 hashes for verification.

Do not commit production secrets, database credentials, cache files, uploads, or server backups.
