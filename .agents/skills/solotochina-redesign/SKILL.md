---
name: solotochina-redesign
description: Improve the existing SoloToChina WordPress frontend without changing editorial facts, repository boundaries, or the CMS-owned page contract.
---

# SoloToChina redesign

Use this skill only for the public WordPress Parent Theme, Child Theme, and Tools Plugin in this repository.

## Sequence

1. Inspect the real renderer, design tokens, responsive rules, and an actual local WordPress page before changing presentation.
2. Preserve the current warm-white and blue brand, information architecture, logo, URLs, content order, and accessible semantic HTML.
3. Diagnose hierarchy, typography, spacing, responsive reflow, focus, error, empty, loading, hover, pressed, reduced-motion, and no-JavaScript states.
4. Make focused improvements in the existing PHP, CSS, and vanilla JavaScript stack. Do not migrate frameworks or add runtime design libraries.
5. Verify Parent+Child+Tools, Parent-only, and Tools-disabled combinations when the changed surface can appear in them.

## Project constraints

- Content correctness, accessibility, and performance take priority over decoration.
- Use the existing system font and icon assets. Do not add external font requests, stock imagery, fake reviews, random dates, discounts, statistics, places, or people.
- CMS chooses page blocks, order, commercial placement, and content. The frontend renders declared capabilities and never infers new modules from taxonomy, keywords, or location.
- Do not rewrite article facts, affiliate destinations, tracking parameters, legal copy, or WordPress IDs for visual polish.
- Keep body copy readable at roughly 62–70ch. Controls start at a 44px touch target and must survive 320px width and 200% text zoom.
- Prefer restrained borders, hierarchy, spacing, real images, and light-blue accents over generic card walls, heavy shadows, gradients, glow, texture, or forced asymmetry.
- Motion is optional and local: use transform/opacity, keep feedback short, make it interruptible, and honor `prefers-reduced-motion`. No scroll hijacking, inertia, parallax, cursor tracking, looping motion, or height animation for long content.
- Mobile On this page uses native `details`/`summary` and a vertical, fully wrapping list. Do not use horizontal pills, ellipsis, tiny type, or page-wide overflow clipping.
- Commercial cards remain clear editorial utilities: one real primary link, true provider, visible compact relationship label (default `Paid link`), no fake urgency or offer language, and no hidden disclosure.
- Do not use this skill for CMS production logic, content strategy, factual rewriting, provider configuration, deployment, or paid-model calls.

## Upstream basis

This project-specific skill is a deliberately narrowed adaptation of Leonxlnx/taste-skill `skills/redesign-skill/SKILL.md`, pinned and documented in `docs/vendor/taste-skill/README.md`. Upstream suggestions that conflict with this repository—external fonts, placeholder imagery, random data, inertial scroll, parallax, blanket skeletons, and automatic content changes—are excluded.
