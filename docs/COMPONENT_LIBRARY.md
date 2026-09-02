# SoloToChina Frontend Component Catalog

Generated from `component-registry.v1.json`. Do not edit component capability details here by hand; update the Registry, implementations, Gallery, and tests, then run `.\scripts\generate-component-catalog.ps1`.

Registry version: `1.0.0`

CMS-usable capabilities: `19`

Internal rendering components recorded: `4`

## Contract Boundary

Frontend defines what can be rendered. CMS decides what should be rendered. Content type remains taxonomy, not layout.

```text
Frontend Component Registry
  -> CMS reads declared components and variants
  -> CMS selects supported components
  -> CMS outputs ordered page.blocks[] and explicit presentation metadata
  -> Frontend Renderer resolves the same Registry
  -> Implemented component renders
```

Unknown component IDs must be rejected by the CMS. Unknown Gutenberg blocks reaching WordPress must degrade safely. The frontend must not add components because of page type.

## Available Components

These are the only capabilities currently available for CMS selection. `page_block` entries belong in ordered content; `presentation_meta` entries are explicit page-level controls.

| ID | Name | Category | Interface | Status | Variants | Purpose |
| --- | --- | --- | --- | --- | --- | --- |
| `paragraph` | Paragraph | content | `page_block` | `stable` | default | Standard editable body copy with meaningful inline links. |
| `heading` | Heading | content | `page_block` | `stable` | section, subsection | Creates an article section in the semantic document outline. |
| `list` | List | content | `page_block` | `stable` | unordered, ordered | Presents related items or a meaningful sequence. |
| `image` | Responsive Image | media | `page_block` | `stable` | evidence, context, illustration, decorative | Renders WordPress Media with intrinsic dimensions and responsive candidates. |
| `quick_answer` | Quick Answer | information | `page_block` | `stable` | default | Places a direct answer before supporting detail for fast human and search scanning. |
| `key_takeaways` | Key Takeaways | information | `page_block` | `stable` | default | Summarizes the decisions that matter most. |
| `quick_facts` | Quick Facts | information | `page_block` | `stable` | default | Shows practical label and value pairs without dashboard styling. |
| `tip` | Tip | information | `page_block` | `stable` | default | Highlights friendly supporting advice with low urgency. |
| `warning` | Warning | information | `page_block` | `stable` | default | Highlights a travel risk, prerequisite, or failure condition. |
| `steps` | Steps | travel | `page_block` | `stable` | default | Explains an ordered setup, booking, or route process. |
| `checklist` | Checklist | travel | `page_block` | `stable` | default | Provides a scannable preparation list without saved completion state. |
| `comparison_table` | Comparison Table | information | `page_block` | `stable` | default | Compares options with native table semantics and local mobile scrolling. |
| `faq` | FAQ | information | `page_block` | `stable` | default | Presents explicit questions and complete answers through native disclosures. |
| `planner_cta` | Planner CTA | travel | `page_block` | `stable` | default | Offers a contextual trip-planning action selected by the CMS. |
| `ticket_reminder` | Ticket Reminder | travel | `page_block` | `stable` | default | Delegates attraction timing and reminder behavior to SoloToChina Tools. |
| `affiliate_cta` | Affiliate CTA | commercial | `page_block` | `stable` | default | Renders a restrained contextual commercial action with visible disclosure. |
| `article_hero` | Article Hero | layout | `presentation_meta` | `stable` | default, attraction, city, survival | Frames CMS-owned article identity and featured media without selecting body components. |
| `share_this_page` | Share This Page | utility | `presentation_meta` | `stable` | default | Shares the canonical page URL without accounts or persisted state. |
| `table_of_contents` | Table of Contents | utility | `presentation_meta` | `stable` | default | Builds article navigation from rendered H2 headings only when explicitly enabled. |

### `paragraph` — Paragraph

- Category: `content`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: Standard editable body copy with meaningful inline links.
- Variants: `default`
- Required fields: `content`
- Optional fields: `anchor`
- Implementation: `wp-content/themes/solo-to-china/inc/content-components.php`, `core/paragraph`
- Accessibility: Use visible text and meaningful link labels.
- Responsive behavior: Text follows the article reading measure and wraps without horizontal overflow.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "content"
  ],
  "properties": {
    "content": {
      "type": "string",
      "contentMediaType": "text/html"
    },
    "anchor": {
      "type": "string"
    }
  }
}
```

Example:

```json
{
  "type": "paragraph",
  "variant": "default",
  "content": "Carry the passport used for the reservation."
}
```

### `heading` — Heading

- Category: `content`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: Creates an article section in the semantic document outline.
- Variants: `section, subsection`
- Required fields: `text`, `level`
- Optional fields: `anchor`
- Implementation: `wp-content/themes/solo-to-china/inc/content-components.php`, `core/heading`
- Accessibility: Keep heading levels sequential and never create a second H1.
- Responsive behavior: Heading scale and anchor offset adapt below tablet width.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "text",
    "level"
  ],
  "properties": {
    "text": {
      "type": "string",
      "minLength": 1
    },
    "level": {
      "type": "integer",
      "enum": [
        2,
        3
      ]
    },
    "anchor": {
      "type": "string"
    }
  }
}
```

Example:

```json
{
  "type": "heading",
  "variant": "section",
  "text": "How to visit",
  "level": 2,
  "anchor": "how-to-visit"
}
```

### `list` — List

- Category: `content`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: Presents related items or a meaningful sequence.
- Variants: `unordered, ordered`
- Required fields: `items`
- Optional fields: `ordered`, `anchor`
- Implementation: `wp-content/themes/solo-to-china/inc/content-components.php`, `core/list`
- Accessibility: Use ordered semantics only when sequence carries meaning.
- Responsive behavior: Items wrap within the reading column.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "items"
  ],
  "properties": {
    "items": {
      "type": "array",
      "minItems": 1,
      "items": {
        "type": "string"
      }
    },
    "ordered": {
      "type": "boolean"
    },
    "anchor": {
      "type": "string"
    }
  }
}
```

Example:

```json
{
  "type": "list",
  "variant": "ordered",
  "items": [
    "Confirm the date",
    "Bring the original passport"
  ],
  "ordered": true
}
```

### `image` — Responsive Image

- Category: `media`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: Renders WordPress Media with intrinsic dimensions and responsive candidates.
- Variants: `evidence, context, illustration, decorative`
- Required fields: `media_id`, `alt`
- Optional fields: `caption`, `role`, `anchor`, `after_block_id`
- Implementation: `wp-content/themes/solo-to-china/inc/content-components.php`, `wp-content/themes/solo-to-china-child/assets/css/content-components.css`, `core/image`
- Accessibility: Alt may be empty only when role is decorative; captions stay visible and semantic.
- Responsive behavior: Uses srcset, sizes, lazy loading, async decoding, and stable intrinsic dimensions.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "media_id",
    "alt"
  ],
  "properties": {
    "media_id": {
      "type": "integer",
      "minimum": 1
    },
    "alt": {
      "type": "string"
    },
    "caption": {
      "type": "string"
    },
    "role": {
      "type": "string",
      "enum": [
        "evidence",
        "context",
        "illustration",
        "decorative"
      ],
      "default": "context"
    },
    "anchor": {
      "type": "string"
    },
    "after_block_id": {
      "type": "string"
    }
  }
}
```

Example:

```json
{
  "type": "image",
  "variant": "context",
  "media_id": 123,
  "alt": "Visitors approaching the Forbidden City",
  "role": "context"
}
```

### `quick_answer` — Quick Answer

- Category: `information`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: Places a direct answer before supporting detail for fast human and search scanning.
- Variants: `default`
- Required fields: `answer`
- Optional fields: `title`, `anchor`
- Implementation: `wp-content/themes/solo-to-china/inc/content-components.php`, `wp-content/themes/solo-to-china-child/assets/css/content-components.css`
- Accessibility: Keep the answer indexable and use a heading only when it fits the outline.
- Responsive behavior: Uses a single readable column at all widths.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "answer"
  ],
  "properties": {
    "answer": {
      "type": "string",
      "contentMediaType": "text/html"
    },
    "title": {
      "type": "string"
    },
    "anchor": {
      "type": "string"
    }
  }
}
```

Example:

```json
{
  "type": "quick_answer",
  "variant": "default",
  "title": "Quick answer",
  "answer": "Reserve before arrival and carry the booking passport."
}
```

### `key_takeaways` — Key Takeaways

- Category: `information`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: Summarizes the decisions that matter most.
- Variants: `default`
- Required fields: `items`
- Optional fields: `title`, `anchor`
- Implementation: `wp-content/themes/solo-to-china/inc/content-components.php`, `wp-content/themes/solo-to-china-child/assets/css/content-components.css`
- Accessibility: Render takeaways as a semantic list.
- Responsive behavior: Stacks as a compact single-column list.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "items"
  ],
  "properties": {
    "items": {
      "type": "array",
      "minItems": 1,
      "items": {
        "type": "string"
      }
    },
    "title": {
      "type": "string"
    },
    "anchor": {
      "type": "string"
    }
  }
}
```

Example:

```json
{
  "type": "key_takeaways",
  "variant": "default",
  "items": [
    "Book early",
    "Use the correct passport"
  ]
}
```

### `quick_facts` — Quick Facts

- Category: `information`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: Shows practical label and value pairs without dashboard styling.
- Variants: `default`
- Required fields: `items`
- Optional fields: `title`, `anchor`
- Implementation: `wp-content/themes/solo-to-china/inc/content-components.php`, `wp-content/themes/solo-to-china-child/assets/css/content-components.css`
- Accessibility: Keep every label adjacent to its value in logical reading order.
- Responsive behavior: Grid collapses to one column on narrow screens.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "items"
  ],
  "properties": {
    "items": {
      "type": "array",
      "minItems": 1,
      "items": {
        "type": "object",
        "required": [
          "label",
          "value"
        ],
        "properties": {
          "label": {
            "type": "string"
          },
          "value": {
            "type": "string"
          }
        }
      }
    },
    "title": {
      "type": "string"
    },
    "anchor": {
      "type": "string"
    }
  }
}
```

Example:

```json
{
  "type": "quick_facts",
  "variant": "default",
  "items": [
    {
      "label": "Time needed",
      "value": "3–4 hours"
    }
  ]
}
```

### `tip` — Tip

- Category: `information`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: Highlights friendly supporting advice with low urgency.
- Variants: `default`
- Required fields: `content`
- Optional fields: `title`, `anchor`
- Implementation: `wp-content/themes/solo-to-china/inc/content-components.php`, `wp-content/themes/solo-to-china-child/assets/css/content-components.css`
- Accessibility: Meaning must remain explicit without color or iconography.
- Responsive behavior: Maintains readable padding and wraps long text.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "content"
  ],
  "properties": {
    "content": {
      "type": "string",
      "contentMediaType": "text/html"
    },
    "title": {
      "type": "string"
    },
    "anchor": {
      "type": "string"
    }
  }
}
```

Example:

```json
{
  "type": "tip",
  "variant": "default",
  "title": "Solo traveler tip",
  "content": "Save an offline screenshot of the reservation."
}
```

### `warning` — Warning

- Category: `information`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: Highlights a travel risk, prerequisite, or failure condition.
- Variants: `default`
- Required fields: `content`
- Optional fields: `title`, `anchor`
- Implementation: `wp-content/themes/solo-to-china/inc/content-components.php`, `wp-content/themes/solo-to-china-child/assets/css/content-components.css`
- Accessibility: State severity in text and never rely on color alone.
- Responsive behavior: Keeps the accent and copy visible without overflow.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "content"
  ],
  "properties": {
    "content": {
      "type": "string",
      "contentMediaType": "text/html"
    },
    "title": {
      "type": "string"
    },
    "anchor": {
      "type": "string"
    }
  }
}
```

Example:

```json
{
  "type": "warning",
  "variant": "default",
  "title": "Passport check",
  "content": "The reservation passport must match the visitor."
}
```

### `steps` — Steps

- Category: `travel`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: Explains an ordered setup, booking, or route process.
- Variants: `default`
- Required fields: `items`
- Optional fields: `title`, `anchor`
- Implementation: `wp-content/themes/solo-to-china/inc/content-components.php`, `wp-content/themes/solo-to-china-child/assets/css/content-components.css`
- Accessibility: Render as an ordered list with visible numbering.
- Responsive behavior: Number and copy remain aligned on narrow screens.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "items"
  ],
  "properties": {
    "items": {
      "type": "array",
      "minItems": 1,
      "items": {
        "type": "string"
      }
    },
    "title": {
      "type": "string"
    },
    "anchor": {
      "type": "string"
    }
  }
}
```

Example:

```json
{
  "type": "steps",
  "variant": "default",
  "items": [
    "Choose the visit date",
    "Confirm traveler details",
    "Save the confirmation"
  ]
}
```

### `checklist` — Checklist

- Category: `travel`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: Provides a scannable preparation list without saved completion state.
- Variants: `default`
- Required fields: `items`
- Optional fields: `title`, `anchor`
- Implementation: `wp-content/themes/solo-to-china/inc/content-components.php`, `wp-content/themes/solo-to-china-child/assets/css/content-components.css`
- Accessibility: Use list semantics and do not imply persisted completion.
- Responsive behavior: Uses compact touch-safe spacing without controls.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "items"
  ],
  "properties": {
    "items": {
      "type": "array",
      "minItems": 1,
      "items": {
        "type": "string"
      }
    },
    "title": {
      "type": "string"
    },
    "anchor": {
      "type": "string"
    }
  }
}
```

Example:

```json
{
  "type": "checklist",
  "variant": "default",
  "items": [
    "Passport",
    "Reservation screenshot",
    "Offline map"
  ]
}
```

### `comparison_table` — Comparison Table

- Category: `information`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: Compares options with native table semantics and local mobile scrolling.
- Variants: `default`
- Required fields: `columns`, `rows`
- Optional fields: `caption`, `anchor`
- Implementation: `wp-content/themes/solo-to-china/inc/content-components.php`, `wp-content/themes/solo-to-china-child/assets/css/content-components.css`
- Accessibility: Preserve caption and header cells; scrolling remains keyboard reachable.
- Responsive behavior: Overflow is contained within the table component, not the page.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "columns",
    "rows"
  ],
  "properties": {
    "columns": {
      "type": "array",
      "minItems": 2,
      "items": {
        "type": "string"
      }
    },
    "rows": {
      "type": "array",
      "minItems": 1,
      "items": {
        "type": "array",
        "items": {
          "type": "string"
        }
      }
    },
    "caption": {
      "type": "string"
    },
    "anchor": {
      "type": "string"
    }
  }
}
```

Example:

```json
{
  "type": "comparison_table",
  "variant": "default",
  "columns": [
    "Option",
    "Best for"
  ],
  "rows": [
    [
      "Metro",
      "Predictable city trips"
    ]
  ],
  "caption": "Transport comparison"
}
```

### `faq` — FAQ

- Category: `information`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: Presents explicit questions and complete answers through native disclosures.
- Variants: `default`
- Required fields: `items`
- Optional fields: `title`, `anchor`
- Implementation: `wp-content/themes/solo-to-china/inc/content-components.php`, `wp-content/themes/solo-to-china-child/assets/css/content-components.css`, `core/details`
- Accessibility: Uses native details and summary with complete visible answer text.
- Responsive behavior: Summary targets retain at least touch-class height.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "items"
  ],
  "properties": {
    "items": {
      "type": "array",
      "minItems": 1,
      "items": {
        "type": "object",
        "required": [
          "question",
          "answer"
        ],
        "properties": {
          "question": {
            "type": "string"
          },
          "answer": {
            "type": "string",
            "contentMediaType": "text/html"
          }
        }
      }
    },
    "title": {
      "type": "string"
    },
    "anchor": {
      "type": "string"
    }
  }
}
```

Example:

```json
{
  "type": "faq",
  "variant": "default",
  "items": [
    {
      "question": "Can I buy at the entrance?",
      "answer": "Advance reservation is the safer default."
    }
  ]
}
```

### `planner_cta` — Planner CTA

- Category: `travel`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: Offers a contextual trip-planning action selected by the CMS.
- Variants: `default`
- Required fields: `title`, `description`, `cta_label`, `target_url`
- Optional fields: `provider`, `disclosure`, `anchor`
- Implementation: `wp-content/themes/solo-to-china/functions.php`, `wp-content/themes/solo-to-china-child/assets/css/content-components.css`
- Accessibility: Use a descriptive label and disclose external commercial relationships.
- Responsive behavior: CTA content and action stack at phone widths.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "title",
    "description",
    "cta_label",
    "target_url"
  ],
  "properties": {
    "title": {
      "type": "string"
    },
    "description": {
      "type": "string"
    },
    "cta_label": {
      "type": "string"
    },
    "target_url": {
      "type": "string",
      "format": "uri",
      "pattern": "^https://"
    },
    "provider": {
      "type": "string"
    },
    "disclosure": {
      "type": "string"
    },
    "anchor": {
      "type": "string"
    }
  }
}
```

Example:

```json
{
  "type": "planner_cta",
  "variant": "default",
  "title": "Build a realistic route",
  "description": "Keep travel time visible.",
  "cta_label": "Open planner",
  "target_url": "https://example.com/planner"
}
```

### `ticket_reminder` — Ticket Reminder

- Category: `travel`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: Delegates attraction timing and reminder behavior to SoloToChina Tools.
- Variants: `default`
- Required fields: `attraction_slug`
- Optional fields: `title`, `description`, `anchor`
- Implementation: `wp-content/themes/solo-to-china/functions.php`, `wp-content/plugins/solo-to-china-tools/solo-to-china-tools.php`
- Accessibility: The Plugin owns labels, validation, status announcements, and reminder controls.
- Responsive behavior: Delegated form controls stack and retain touch targets on mobile.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "attraction_slug"
  ],
  "properties": {
    "attraction_slug": {
      "type": "string",
      "minLength": 1
    },
    "title": {
      "type": "string"
    },
    "description": {
      "type": "string"
    },
    "anchor": {
      "type": "string"
    }
  }
}
```

Example:

```json
{
  "type": "ticket_reminder",
  "variant": "default",
  "attraction_slug": "forbidden-city",
  "title": "Check ticket timing"
}
```

### `affiliate_cta` — Affiliate CTA

- Category: `commercial`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: Renders a restrained contextual commercial action with visible disclosure.
- Variants: `default`
- Required fields: `category`, `provider`, `title`, `description`, `cta_label`, `target_url`
- Optional fields: `price_text`, `disclosure`, `anchor`
- Implementation: `wp-content/themes/solo-to-china/functions.php`, `wp-content/themes/solo-to-china-child/assets/css/content-components.css`
- Accessibility: External links open safely and relationship disclosure remains visible.
- Responsive behavior: Copy and action stack without becoming a deal wall.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "category",
    "provider",
    "title",
    "description",
    "cta_label",
    "target_url"
  ],
  "properties": {
    "category": {
      "type": "string"
    },
    "provider": {
      "type": "string"
    },
    "title": {
      "type": "string"
    },
    "description": {
      "type": "string"
    },
    "price_text": {
      "type": "string"
    },
    "cta_label": {
      "type": "string"
    },
    "target_url": {
      "type": "string",
      "format": "uri",
      "pattern": "^https://"
    },
    "disclosure": {
      "type": "string"
    },
    "anchor": {
      "type": "string"
    }
  }
}
```

Example:

```json
{
  "type": "affiliate_cta",
  "variant": "default",
  "category": "hotel",
  "provider": "Example",
  "title": "Compare practical bases",
  "description": "Check location before price.",
  "cta_label": "View options",
  "target_url": "https://example.com",
  "disclosure": "Sponsored link."
}
```

### `article_hero` — Article Hero

- Category: `layout`
- Status: `stable`
- CMS usable: `true` via `presentation_meta`
- Purpose: Frames CMS-owned article identity and featured media without selecting body components.
- Variants: `default, attraction, city, survival`
- Required fields: `title`
- Optional fields: `excerpt`, `featured_media_id`, `variant`
- Implementation: `wp-content/themes/solo-to-china/single.php`, `wp-content/themes/solo-to-china-child/assets/css/article.css`
- Accessibility: Provides the only H1 and preserves responsive image alt text.
- Responsive behavior: Uses a bounded image-led desktop frame and edge-to-edge mobile composition.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "title"
  ],
  "properties": {
    "title": {
      "type": "string"
    },
    "excerpt": {
      "type": "string"
    },
    "featured_media_id": {
      "type": "integer"
    },
    "variant": {
      "type": "string",
      "enum": [
        "default",
        "attraction",
        "city",
        "survival"
      ]
    }
  }
}
```

Example:

```json
{
  "type": "article_hero",
  "variant": "attraction",
  "title": "Forbidden City: First-Time Visitor Guide",
  "featured_media_id": 123
}
```

### `share_this_page` — Share This Page

- Category: `utility`
- Status: `stable`
- CMS usable: `true` via `presentation_meta`
- Purpose: Shares the canonical page URL without accounts or persisted state.
- Variants: `default`
- Required fields: `enabled`
- Optional fields: None
- Implementation: `wp-content/themes/solo-to-china/functions.php`, `wp-content/themes/solo-to-china/assets/js/main.js`, `wp-content/themes/solo-to-china-child/assets/css/article.css`
- Accessibility: Supports native share, dialog labeling, live status, Escape, outside click, and focus return.
- Responsive behavior: Uses a desktop popover and a mobile bottom panel.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "enabled"
  ],
  "properties": {
    "enabled": {
      "type": "boolean"
    }
  }
}
```

Example:

```json
{
  "type": "share_this_page",
  "variant": "default",
  "enabled": true
}
```

### `table_of_contents` — Table of Contents

- Category: `utility`
- Status: `stable`
- CMS usable: `true` via `presentation_meta`
- Purpose: Builds article navigation from rendered H2 headings only when explicitly enabled.
- Variants: `default`
- Required fields: `enabled`
- Optional fields: None
- Implementation: `wp-content/themes/solo-to-china/functions.php`, `wp-content/themes/solo-to-china/assets/js/main.js`, `wp-content/themes/solo-to-china-child/assets/css/article.css`
- Accessibility: Uses a labeled nav and stable public heading anchors.
- Responsive behavior: Renders as a sticky sidebar on desktop and a horizontal navigator on mobile.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "enabled"
  ],
  "properties": {
    "enabled": {
      "type": "boolean"
    }
  }
}
```

Example:

```json
{
  "type": "table_of_contents",
  "variant": "default",
  "enabled": true
}
```

## Internal Components

These implementations exist and are maintained, but `cms_usable` is false. They are not valid `page.blocks[].type` values.

### `article_shell` — Article Shell

- Category: `layout`
- Status: `stable`
- CMS usable: `false` via `internal`
- Purpose: Provides the generic single-article document frame around CMS content.
- Variants: `default`
- Required fields: None
- Optional fields: ``
- Implementation: `wp-content/themes/solo-to-china/single.php`
- Accessibility: Maintains main, article, header, content, and optional aside landmarks.
- Responsive behavior: Controls reading measure and optional navigation columns.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [],
  "properties": {}
}
```

Example:

```json
{
  "internal": true
}
```

### `breadcrumb` — Guide Breadcrumb

- Category: `utility`
- Status: `stable`
- CMS usable: `false` via `internal`
- Purpose: Shows Home, guide hub, and current article hierarchy.
- Variants: `default`
- Required fields: None
- Optional fields: ``
- Implementation: `wp-content/themes/solo-to-china-child/functions.php`
- Accessibility: Uses a labeled nav, list semantics, and aria-current.
- Responsive behavior: Wraps long hierarchy labels without overflow.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [],
  "properties": {}
}
```

Example:

```json
{
  "internal": true
}
```

### `guide_card` — Guide Card

- Category: `travel`
- Status: `stable`
- CMS usable: `false` via `internal`
- Purpose: Renders guide summaries in archives, search, and latest-guide lists.
- Variants: `default`
- Required fields: None
- Optional fields: ``
- Implementation: `wp-content/themes/solo-to-china/functions.php`
- Accessibility: Uses meaningful titles, image alt text, and a single clear destination.
- Responsive behavior: Grid density and image ratio adapt by viewport.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [],
  "properties": {}
}
```

Example:

```json
{
  "internal": true
}
```

### `latest_guides_list` — Latest Guides List

- Category: `travel`
- Status: `stable`
- CMS usable: `false` via `internal`
- Purpose: Queries category-matched posts for selected core landing pages.
- Variants: `default`
- Required fields: None
- Optional fields: ``
- Implementation: `wp-content/themes/solo-to-china/functions.php`, `wp-content/themes/solo-to-china/page.php`
- Accessibility: Uses a labeled section and semantic guide cards.
- Responsive behavior: Uses the shared responsive guide-card grid.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [],
  "properties": {}
}
```

Example:

```json
{
  "internal": true
}
```

## Legacy

No published Registry ID is currently deprecated. The former topic-wide Attraction, City, and Survival article patterns and the Save Guide / Saved Guides browser-state UI were removed before Registry 1.0 and are not available compatibility IDs. Historical Gutenberg content still receives WordPress safe fallback rendering.

## Not Yet Componentized

The following current UI remains frontend-owned page composition rather than CMS-callable components:

- Site Header, primary navigation, mobile navigation, and Footer.
- Homepage Hero, Survival Kit shortcut strip, City/Attraction grids, Planner band, Ticket band, and homepage FAQ composition.
- Core landing-page Hero copy and per-page section composition in `page.php`.
- Category/archive/search query composition around the internal Guide Card.

These must not be emitted as CMS component types until they are deliberately implemented, added to the Registry, shown in the Gallery, and tested.

## Proposed, Not Available

Possible future content needs, intentionally not implemented or registered in this release:

- `source_citations` for structured public references and last-checked dates.
- `related_guides` for CMS-curated internal reading paths.
- `transport_option` for repeated route comparisons that need more structure than a table.

These names are proposals, not stable IDs, and the CMS must not use them.

## Adding A Component

1. Frontend designs and implements the component against a real content need.
2. Assign a stable semantic component ID.
3. Define the input JSON Schema, including required and optional fields.
4. Define the finite semantic variants supported by the Design System.
5. Add the implementation and capability record to the Component Registry.
6. Add representative content and every major variant to the Component Gallery.
7. Regenerate this Component Catalog from the Registry.
8. Test rendering, responsive behavior, accessibility, safe fallback, and Contract output.
9. Only then may the CMS begin emitting the component ID.

If an ID must be retired, mark it `deprecated` and document the compatibility renderer before changing CMS output. Visual refactors alone never justify changing a stable component ID.
