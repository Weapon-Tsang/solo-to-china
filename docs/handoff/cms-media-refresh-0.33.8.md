# CMS media refresh integration: Parent Theme 0.33.8

The Parent Theme accepts the same Publish Package for draft delivery and for a
guarded image refresh of a CMS-owned, already published post. A published
refresh requires the exact post ID, CMS draft ID and page ID. It compares the
stored CMS page with live WordPress content and permits only image-block changes
while preserving the post's published status, prose, title and slug. A private,
authenticated receipt route lets the CMS resolve a timed-out request without
blindly sending the write again.

The schema is generated from `scripts/generate-component-catalog.ps1`, and
the runtime fixture `scripts/playground-cms-publish.php` tests the successful
published media update, receipt, title rewrite rejection and status mismatch.
Use `scripts/start-preview.ps1 -ParentOnly` to run the full fixture on a pinned
WordPress 6.8.3 Playground runtime. The CLI previously failed to fetch the
blueprint's WordPress 7.0.4 version on this workstation; the pinned version
completed the runtime tests.

Install the new Parent Theme before the CMS 2.0.49 worker is allowed to send
published media refreshes. The Child Theme 0.13.1 and Tools Plugin 0.26.0 do not
change. Keep the prior Parent Theme ZIP for rollback. A published post edited
outside the CMS is intentionally blocked and needs review before media refresh.
