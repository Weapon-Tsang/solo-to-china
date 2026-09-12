# SoloToChina WordPress Project

SoloToChina is planned as a practical China independent travel platform for international travelers, especially solo travelers and first-time non-Chinese visitors.

Core product direction:

- Content-first travel guides
- Utility-driven tools
- Guest-first access
- Low-friction personalization later

Original exported-site audit context (historical, not a live deployment check):

- The audited WordPress site was `https://solotochina.com`.
- That exported site used third-party WordPress theme/plugin packages.
- No custom SoloToChina theme or custom SoloToChina plugin was present in the exported `wp-content` audit package.
- Future development should happen in project-owned theme/plugin directories rather than editing third-party theme/plugin source directly.

Current code ownership:

- `wp-content/themes/solo-to-china/` for the custom site theme.
- `wp-content/plugins/solo-to-china-tools/` for project-owned Find This Place, Taxi Card, and Ticket Booking Window logic.

Current development branch deliverables:

- Custom SoloToChina theme `0.33.0` in `wp-content/themes/solo-to-china/`.
- SoloToChina Child Theme `0.12.0` in `wp-content/themes/solo-to-china-child/`.
- SoloToChina Tools plugin `0.26.0` in `wp-content/plugins/solo-to-china-tools/`.
- Release packaging script in `scripts/package-release.ps1`.
- WordPress/aaPanel install notes in `docs/deployment/wordpress-install.md`.
- Current progress handoff in `docs/handoff/current-progress.md`.
- Content Component System architecture in `docs/architecture/content-component-system.md`.
- Web Tools provider, privacy, verification, and handoff architecture in `docs/architecture/web-tools.md`.
- CMS-facing generated Component Contract in `contracts/component-registry.json`.
- CMS page payload schema in `contracts/page-schema.json`.
- CMS WordPress Publish Package schema in `contracts/cms-publish-package.schema.json`.
- Registry-generated human catalog in `docs/COMPONENT_LIBRARY.md`, repository boundary in `docs/CMS_FRONTEND_CONTRACT.md`, capability history in `docs/COMPONENT_CHANGELOG.md`, and an internal Playground Gallery at `/design-system/`.

The Theme Registry at `wp-content/themes/solo-to-china/content-contract/component-registry.v1.json` is the authoring source. Run `scripts/generate-component-catalog.ps1` after an approved capability change; do not maintain the published Contract, Page Schema, or Catalog as separate manual component lists.

Current release: Parent **0.33.0**, Child **0.12.0**, Tools **0.26.0**, Registry **1.4.0** (29 capabilities / 26 page block types). Content Contract **2.1.0** and Publish Package **1.0.0** remain compatible. The existing Planner, Survival Kit, FAQ and Footer work was preserved.

This upgrade adds published entity links, responsive WebP images, a warm-white/blue visual system, server-rendered TOC, accessible Share/More, new optional editorial components, sequential image preparation with Worker support, cancellation guards, versioned destination data and partial-accuracy Taxi cards. Homepage tool entries no longer load tool execution code. Ticket estimates remain auxiliary and require a current reviewed rule.

- [Requirement ledger](docs/upgrades/frontend-experience-v1.md)
- [Design system](docs/design/frontend-experience-v1.md)
- [Tools and privacy contracts](docs/architecture/tools-upgrade-v1.md)
- [CMS integration notes](docs/CMS_UPGRADE_1_4.md)
- [QA and performance evidence](docs/qa/frontend-upgrade-v1.md)
- [Deployment and rollback](docs/deployment/frontend-upgrade-v1.md)

Development and local verification are complete only to the extent recorded in the ledger. Production deployment, real gateway/retention verification, wider editorial coverage and the separate CMS integration require external access. No production deployment or paid recognition call was performed.

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
.\scripts\verify-web-tools.ps1
.\scripts\verify-web-tools-runtime.ps1 -BaseUrl http://127.0.0.1:9400
.\scripts\verify-content-runtime.ps1 -BaseUrl http://127.0.0.1:9400
.\scripts\start-preview.ps1 -Port 9402 -ParentOnly
.\scripts\verify-content-runtime.ps1 -BaseUrl http://127.0.0.1:9402 -ParentOnly
```

Use `.\scripts\start-preview.ps1 -Port 9404 -NoTools` to test plugin deactivation. Use `.\scripts\start-preview.ps1 -Port 9403 -Editor` for the authenticated Gutenberg editor fixture. Preview modes are disposable and do not create production content.

The script creates:

- `dist/solo-to-china-theme.zip`
- `dist/solo-to-china-child-theme.zip`
- `dist/solo-to-china-tools-plugin.zip`
- `dist/release-manifest.txt`

The three zip files are intended for WordPress upload. Install the Parent Theme first, install and activate the Child Theme second, then install and activate the Tools plugin. The manifest records artifact versions and SHA256 hashes for verification.

Do not commit production secrets, database credentials, cache files, uploads, or server backups.
