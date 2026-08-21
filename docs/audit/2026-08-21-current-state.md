# SoloToChina Current State Audit

Date: 2026-08-21

Source files reviewed:

- `wp-content-audit.zip`
- `solotochina.WordPress.2026-08-21.xml`
- `htaccess`

## Repository

The GitHub repository `Weapon-Tsang/solo-to-china` was private and initially empty. GitHub access is now authorized.

## WordPress

The content export reports:

- Site title: SoloToChina
- Site URL: `https://solotochina.com`
- Export generator: WordPress 7.1
- Language: `zh-Hans`

The export is very early-stage. It mainly contains default/sample content, a privacy policy draft, navigation/global style records, one reusable block, and one image attachment.

## Theme

Installed themes found in the audit package:

- GeneratePress 3.6.1
- Twenty Twenty-Five 1.5
- Twenty Twenty-Four 1.5
- Twenty Twenty-Three 1.6

The export references GeneratePress as the active theme through global styles. No custom SoloToChina child theme or custom SoloToChina theme was found.

## Plugins

Installed plugins found in the audit package:

- Akismet Anti-spam: Spam Protection 5.7
- Redis Object Cache 2.8.0
- Rank Math SEO 1.0.276

The audit package also includes `object-cache.php`, which is the Redis object-cache drop-in.

## .htaccess

The provided `htaccess` file is effectively empty: it contains only whitespace and no active rewrite/cache/security rules.

## Security And Repository Hygiene

No production `wp-config.php`, database dump, uploads directory, or obvious real secret file was provided in the audit package.

The original WordPress XML export includes an author email address. A sanitized copy was prepared locally for audit, but the XML body is not committed to the repository by default to avoid carrying user/account metadata into long-term source control.

## Product Constraints To Preserve

SoloToChina should remain:

- Guest-first
- Content-first
- Mobile-first
- Utility-driven
- Low friction
- High trust

Fixed top-level information architecture:

- Home
- Survival Kit
- City Guides
- Attraction Guides
- Planner
- Tools
- FAQ

Do not add top-level navigation items such as Hotels, Tickets, Flights, Trains, or Book. Affiliate links should remain a restrained transaction layer behind content and tools.

## Recommended Next Step

Create a project-owned theme and plugin instead of editing GeneratePress, default WordPress themes, or third-party plugin source directly:

- `wp-content/themes/solo-to-china/`
- `wp-content/plugins/solo-to-china-tools/`

The custom plugin should own the Attraction Ticket Reservation & Reminder. The theme should own homepage layout, guide hubs, visual system, templates, and reusable front-end components.
