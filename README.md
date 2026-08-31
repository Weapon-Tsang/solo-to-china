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

- Custom SoloToChina theme `0.21.0` in `wp-content/themes/solo-to-china/`.
- SoloToChina Child Theme `0.3.0` in `wp-content/themes/solo-to-china-child/`.
- SoloToChina Tools plugin `0.21.0` in `wp-content/plugins/solo-to-china-tools/`.
- Release packaging script in `scripts/package-release.ps1`.
- WordPress/aaPanel install notes in `docs/deployment/wordpress-install.md`.
- Current progress handoff in `docs/handoff/current-progress.md`.

Generate install artifacts with:

```powershell
.\scripts\package-release.ps1
```

Run a real local WordPress preview with Parent Theme, Child Theme, and Plugin mounted from the repository:

```powershell
.\scripts\start-preview.ps1
```

The preview uses the official WordPress Playground CLI and defaults to `http://127.0.0.1:9400`.

The script creates:

- `dist/solo-to-china-theme.zip`
- `dist/solo-to-china-child-theme.zip`
- `dist/solo-to-china-tools-plugin.zip`
- `dist/release-manifest.txt`

The three zip files are intended for WordPress upload. Install the Parent Theme first, install and activate the Child Theme second, then install and activate the Tools plugin. The manifest records artifact versions and SHA256 hashes for verification.

Do not commit production secrets, database credentials, cache files, uploads, or server backups.
