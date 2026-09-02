# SoloToChina New Chat Handoff

Date: 2026-09-03

## Product Boundary

SoloToChina is a guest-first, content-first, mobile-first independent China travel guide for international visitors.

Fixed top-level navigation:

- Home
- Survival Kit
- City Guides
- Attraction Guides
- Planner
- Tools
- FAQ

Do not add top-level Hotels, Tickets, Flights, Trains, or Book.

Project-owned code:

- wp-content/themes/solo-to-china/
- wp-content/themes/solo-to-china-child/
- wp-content/plugins/solo-to-china-tools/

Do not edit WordPress Core, third-party themes/plugins, uploads, language/cache/database files, or wp-config.php.

## Current Versions

- Parent Theme: 0.25.0
- Child Theme: 0.7.0
- SoloToChina Tools Plugin: 0.22.0
- Content Contract: 2.0.0
- Component Registry: 1.0.0

## Architecture Rule

Frontend responsibility: “Render what CMS requests.”

CMS responsibility: “Decide what the page contains.”

Content type is taxonomy, not layout.

The Parent Theme now uses one generic single.php article shell. It renders post content in CMS/Gutenberg order and does not infer or inject FAQ, checklist, quick facts, warning, steps, ticket reminder, affiliate CTA, Share, or TOC from guide taxonomy.

Guide type remains useful for category/archive routing, URL hierarchy, breadcrumbs, labels, related-content discovery, and restrained visual context.

CMS-facing presentation metadata:

- _stc_guide_type
- _stc_content_contract_version
- _stc_show_share
- _stc_show_toc
- _stc_hero_variant

Share and TOC are off when their explicit metadata is absent. Hero variant supports default, attraction, city, and survival. Featured image remains the CMS-owned Hero media source.

Canonical Contract:

    wp-content/themes/solo-to-china/content-contract/content-contract.v2.json
    GET /wp-json/stc/v1/content-contract

Canonical Component Registry:

    wp-content/themes/solo-to-china/content-contract/component-registry.v1.json
    GET /wp-json/stc/v1/component-registry

Unknown Gutenberg blocks must degrade safely instead of breaking the page.

## Share This Page

Share This Page replaced the discontinued Save Guide system.

Current behavior:

- No account, localStorage, saved state, or cross-device implication
- Uses page title, excerpt, and canonical URL
- Prioritizes navigator.share()
- Falls back to WhatsApp, email, and Copy link
- Includes clipboard fallback and manual-selection error path
- Uses ARIA live status, busy state, Escape, outside click, close button, and focus return
- Appears as a refined translucent Hero utility
- Uses a lightweight desktop popover and mobile bottom panel
- Remains visually quieter than booking, availability, and planner CTAs

The Theme no longer contains Save guide, Saved, Unsave, Saved Guides, guide export/import/clear/delete, or guide localStorage behavior.

## Component System

Registry 1.0 publishes 19 stable CMS capabilities: 16 ordered page blocks and three explicit presentation controls.

- Core: Paragraph, Heading, List, Image
- Editorial: Quick Answer, Key Takeaways, Quick Facts, Tip, Warning, Steps, Checklist, Comparison Table, FAQ
- Contextual: Planner CTA, Ticket Reminder, Affiliate CTA
- Presentation: Article Hero, Share This Page, Table of Contents

All components are available independently of content type. The CMS decides their presence, order, data, and variants. Four additional renderer components are documented as internal and are not valid CMS types: Article Shell, Guide Breadcrumb, Guide Card, and Latest Guides List. The Parent Theme keeps small reusable component patterns; topic-wide Attraction, City, and Survival article patterns were removed.

`docs/COMPONENT_LIBRARY.md` is generated from the Registry. Playground exposes an ephemeral `/design-system/` Gallery with all 19 capability records, the major Hero variants, and real examples for all 16 page-block components. The Theme does not auto-create this page in production.

Responsive Media, server-rendered stable H2 IDs, editor parity, semantic HTML, long-text containment, keyboard focus, reduced motion, and safe affiliate rel/disclosure behavior remain in place.

## Plugin Boundary

SoloToChina Tools continues to own Attraction Ticket Reservation & Reminder:

- Attraction data
- Booking lead days and date calculations
- Form validation
- Reminder localStorage
- Reminder import/export/delete/clear
- Calendar file output

The Theme only renders contextual presentation and delegates the form shortcode. Do not move Plugin data or reminder logic into either Theme.

## Local Fixtures And QA

scripts/playground-fixtures.php creates disposable Survival, City, and Attraction articles.

- All three explicitly enable Share.
- Survival and Attraction explicitly enable TOC.
- City explicitly disables TOC, proving taxonomy does not dictate layout.
- City retains category-only historical classification coverage.
- Attraction contains the Plugin-delegated Ticket Reminder and responsive Media fixture.

Static verification:

    .\scripts\verify-page-architecture.ps1
    .\scripts\verify-component-registry.ps1
    .\scripts\verify-content-contract.ps1
    .\scripts\verify-project.ps1
    .\.tools\php\php.exe scripts\verify-project.php

Runtime preview:

    .\scripts\start-preview.ps1 -Port 9400
    .\scripts\verify-content-runtime.ps1 -BaseUrl http://127.0.0.1:9400

Parent-only fallback:

    .\scripts\start-preview.ps1 -Port 9402 -ParentOnly
    .\scripts\verify-content-runtime.ps1 -BaseUrl http://127.0.0.1:9402 -ParentOnly

Playground content is disposable and is not a static demo or production content.

## Installation

Generate packages with:

    .\scripts\package-release.ps1

Install in this order:

1. Install SoloToChina Parent Theme and keep it installed.
2. Install and activate SoloToChina Child Theme.
3. Install and activate SoloToChina Tools Plugin.

Artifacts:

- dist/solo-to-china-theme.zip
- dist/solo-to-china-child-theme.zip
- dist/solo-to-china-tools-plugin.zip
- dist/release-manifest.txt

No production deployment is authorized by this handoff.

## Next Safe Work

- Connect the external CMS to Contract 2.0 in a non-production environment.
- Verify CMS writes the explicit Share, TOC, Hero variant, guide type, and Contract version metadata.
- Import three deliberately different block combinations and confirm exact order.
- Run staging screenshots and accessibility checks with representative real content.

Do not start accounts, cross-device sync, email/SMS reminders, custom tables, real inventory checks, payments, new tools, or new top-level navigation without a separate approved design.

## Fixed Information Architecture

Keep Home / Survival Kit / City Guides / Attraction Guides / Planner / Tools / FAQ as the complete top-level navigation. Content types classify and route content; they must not select fixed article layouts.

## Development Style For Next Chat

Work in small, independently verifiable commits. Update verification first, implement within the Parent/Child/Plugin ownership boundary, run static and real Playground checks, inspect 1440 / 768 / 390 / 375 layouts, package only after validation, and update the existing handoff instead of starting a parallel status document.

## Do Not Start Without Explicit Approval

Do not deploy to production, change WordPress Core or third-party packages, introduce accounts or cross-device sync, add email/SMS delivery, create database tables, add live inventory checks, add tools, or expand the top-level navigation without a separate approved scope.

## Suggested New Chat Opening Message

Continue the existing SoloToChina WordPress repository from Contract 2.0. Read the current handoff and architecture document first, preserve the generic CMS-driven article shell and explicit Share/TOC metadata, keep Ticket Reminder logic in the Plugin, verify changes in real WordPress Playground at all required widths, and do not create a static replacement project.
