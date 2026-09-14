# CMS delivery patch 0.33.1 handoff

Parent Theme `0.33.1`, Child `0.12.0`, Tools `0.26.0`, Registry `1.4.0`, Content Contract `2.1.0`, and Publish Package `1.0.0` form this compatible release set.

The parent patch stores the CMS schema language, uses English reader-facing dates for English CMS articles, maps CMS SEO title/description/canonical and social fields into Rank Math, and derives robots from the real WordPress post status. CMS delivery remains draft-only and previews are `noindex,nofollow`; an explicit later WordPress publication becomes `index,follow`.

No component ID, payload field, variant, Registry version, Page Schema, Publish Package version, child theme, Tools plugin or Frontend rendering ownership changes. The CMS still sends semantic payloads; WordPress owns HTML, CSS and responsive presentation.

WordPress Playground runtime and authenticated desktop/mobile Chromium preview acceptance cover the CMS REST serializer, strict draft overwrite protection, language, dates, SEO, robots, JSON-LD, one H1, image alt/lazy behavior, raw-Markdown absence and horizontal overflow. Production preview acceptance remains a separate post-deployment check.
