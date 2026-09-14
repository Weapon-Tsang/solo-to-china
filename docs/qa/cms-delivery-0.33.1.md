# CMS delivery 0.33.1 QA

Date: 2026-09-14.

- WordPress Playground: WordPress 7.0.4 / PHP 8.3, CMS create/update REST path PASS.
- Draft protection: delivery status remains `draft`; an overwrite after explicit publication returns `POST_NOT_DRAFT` PASS.
- Document semantics: `lang=en-US` and English article date PASS.
- SEO/social/canonical filters: CMS title, description and permalink mapping PASS.
- Robots: draft `noindex,nofollow`; explicitly published `index,follow` PASS.
- Structured output: JSON-LD Article markup and semantic Contract components PASS.
- Desktop authenticated preview: one H1, no raw Markdown, images carry alt text, no horizontal overflow PASS.
- Mobile authenticated preview at 390x844: one H1, responsive navigation/content/images, no horizontal overflow PASS.

This local WordPress layer does not substitute for production theme installation, CDN/cache verification, or final production Draft preview acceptance.
