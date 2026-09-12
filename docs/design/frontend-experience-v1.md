# Frontend experience v1 — 2026-09-12

Applies to Parent 0.33.0, Child 0.12.0, Tools 0.26.0. The original user work on Planner, Survival Kit, FAQ and Footer was retained and refined. The existing logo artwork, name and approved Trip.Planner affiliate destination were preserved.

## Design tokens and roles

The authoring source for presentation tokens is `wp-content/themes/solo-to-china-child/assets/css/design-system.css`. Parent and plugin styles provide usable defaults without the child theme.

| Role | Value / behavior |
|---|---|
| Canvas / surface | Warm white `#fafaf7` / white |
| Primary / hover | Blue `#2f66e8` / `#2053c5` |
| Main / secondary text | `#202832` / `#586577` |
| Muted panel / line | `#f1f4f9` / pale neutral border |
| Body | System sans, generally 16–18px, generous line height; article measure at most 70ch |
| Display | Existing serif for the home photograph and selected editorial headings; compact article title uses sans |
| Small labels | At least 12px; no tiny disclosure text |
| Controls | At least 44px touch target; primary actions 48px; visible focus outline |
| Corners | Mostly 12px panels, 6–8px controls; pill shapes reserved for small utilities |
| Motion | 140ms feedback, 220ms state changes, 200ms movement; reduced-motion removes nonessential movement |
| Responsive boundary | Compact through 840px; desktop from 841px |

Legacy `jade` / `green` token names remain compatibility aliases to blue; they are not instructions to use a green primary palette. Muted accent colors inside contextual warning components remain semantic accents.

## Card and page behavior

- Collection cards use recognizable destination photographs, actual published guide URLs, short names and useful subtitles. Missing, draft or password-protected entities disappear. Home and collection pages share one resolver and dataset.
- Guide recommendations use an editorial text row rather than another large photographic banner. They are rendered only from real published posts.
- Tool entries are light bordered cards. Homepage entries navigate to Find This Place and Taxi Card without loading their executable code. Ticket Booking Window stays in Tools and explicit article embeds.
- Commercial modules identify the provider, open the configured destination, and disclose affiliate status. The Planner work already present at baseline remains the planning entry point.
- An article without a real featured image gets a compact warm-white header. No default city photograph is substituted. Image-led headers use the article's own WordPress Media attachment.
- Quick Facts compact/detailed variants, screenshot steps, routes and place cards support different reading jobs. These are CMS-selected blocks, never automatically inserted from guide type.

## Interaction and image rules

The parent owns mobile navigation, More, Share, TOC enhancement and image enlargement. The child no longer attaches duplicate menu handlers. More preserves its state across 840/841px; collapsed cards are hidden before paint when JS is available, and all cards remain reachable without JS. Focus returns to the disclosure before its content is hidden.

Share is a nonmodal utility. One panel is open at a time. Native cancellation does not open another panel; copy refusal exposes an actual selectable URL. Extra platforms are in a secondary disclosure. Instagram is not presented as direct website sharing. Taxi Driver Mode and enlarged images use native modal dialogs with close/Escape/focus restoration; no system fullscreen permission is required.

TOC links come from the same single `the_content` render that supplies the article body. Deterministic H2 IDs avoid collisions; JS only marks the current section. The mobile TOC is horizontal/compact and the desktop TOC follows the article layout.

The home hero uses HTML `img`, width/height, `srcset`, eager loading and high fetch priority. Generated hero WebP widths are 640/960/1600; card widths are 320/480/720. The original PNGs remain source assets and are not referenced by hero CSS. WordPress editorial images use attachment metadata/srcset, native dimensions and lazy loading below the fold. Annotation coordinates are normalized to the uncropped image; descriptions remain visible without hover, and enlargement preserves the markers and legend.

Reproduce bundled image generation with `python scripts/prepare-responsive-images.py`. The manifest records exact dimensions and bytes; do not manually change generated asset names without regenerating it.
