# SoloToChina Web Tools Architecture

Current Tools Plugin version: `0.26.0`.

The current implementation, provider contracts, image preparation, request state machine, cost controls, destination catalog and privacy boundaries are documented in [Tools upgrade v1](tools-upgrade-v1.md).

The Plugin owns business logic and source-backed destination data. The Parent owns generic rendering and optional CMS adapters; the Child owns presentation. All three retain guest-only operation and the existing `destination_card` / `ticket_reminder` IDs.

The previous architecture snapshot is retained as [historical context](../handoff/archive/2026-09-09-web-tools.md); its inline-dictionary, unprocessed-image and unconditional arrival descriptions are superseded.

See [deployment and rollback](../deployment/frontend-upgrade-v1.md), [CMS synchronization](../CMS_UPGRADE_1_4.md), and [QA report](../qa/frontend-upgrade-v1.md).
