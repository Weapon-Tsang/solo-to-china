# SoloToChina Frontend Component Catalog

Generated from `component-registry.v1.json`. Do not edit component capability details here by hand; update the Registry, implementations, Gallery, and tests, then run `.\scripts\generate-component-catalog.ps1`.

Registry version: `1.4.0`

CMS-usable capabilities: `29`

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
| `quick_facts` | Quick Facts | information | `page_block` | `stable` | default, compact, detailed | Shows practical label and value pairs without dashboard styling. |
| `tip` | Tip | information | `page_block` | `stable` | default | Highlights friendly supporting advice with low urgency. |
| `warning` | Warning | information | `page_block` | `stable` | default | Highlights a travel risk, prerequisite, or failure condition. |
| `steps` | Steps | travel | `page_block` | `stable` | default, screenshots | Explains an ordered setup, booking, or route process. |
| `route_timeline` | Route Timeline | travel | `page_block` | `stable` | default | Shows a sequence of travel stops with concise supporting details. |
| `checklist` | Checklist | travel | `page_block` | `stable` | default | Provides a scannable preparation list without saved completion state. |
| `comparison_table` | Comparison Table | information | `page_block` | `stable` | default | Compares options with native table semantics and local mobile scrolling. |
| `pros_cons` | Pros and Cons | information | `page_block` | `stable` | default | Balances the advantages and tradeoffs of one travel choice. |
| `faq` | FAQ | information | `page_block` | `stable` | default | Presents explicit questions and complete answers through native disclosures. |
| `planner_cta` | Planner CTA | travel | `page_block` | `stable` | default | Offers a contextual trip-planning action selected by the CMS. |
| `ticket_reminder` | Ticket Booking Window | travel | `page_block` | `stable` | default | Delegates rule-based booking-window timing to SoloToChina Tools while retaining the historical identifier as a compatibility alias. |
| `destination_card` | Destination / Taxi Card | travel | `page_block` | `stable` | default | Renders a verified Chinese destination card that travelers can show or copy for a driver. |
| `affiliate_cta` | Affiliate CTA | commercial | `page_block` | `stable` | default | Renders a restrained contextual commercial action with visible disclosure. |
| `affiliate_booking_card` | Affiliate Booking Card | commercial | `page_block` | `stable` | default | Renders a CMS-selected high-intent Trip.com deep or category link after QA. |
| `affiliate_search_card` | Affiliate Search Card | commercial | `page_block` | `stable` | link, search_box | Renders a CMS-selected Trip.com search link or allowlisted structured search box. |
| `affiliate_banner` | Affiliate Banner | commercial | `page_block` | `stable` | static, dynamic | Renders a CMS-selected static or allowlisted dynamic Trip.com banner as a restrained fallback. |
| `affiliate_promotion_card` | Affiliate Promotion Card | commercial | `page_block` | `stable` | default | Renders a time-bounded CMS-selected Trip.com promotion without changing editorial conclusions. |
| `article_hero` | Article Hero | layout | `presentation_meta` | `stable` | default, attraction, city, survival, compact, image-led | Frames CMS-owned article identity and featured media without selecting body components. |
| `share_this_page` | Share This Page | utility | `presentation_meta` | `stable` | default | Shares the canonical page URL without accounts or persisted state. |
| `table_of_contents` | Table of Contents | utility | `presentation_meta` | `stable` | default | Builds article navigation from rendered H2 headings only when explicitly enabled. |
| `place_info_card` | Place Information Card | contextual | `page_block` | `stable` | default | A canonical place, optional editorial media, published guide and existing Taxi Card link. |
| `annotated_image` | Annotated Image | media | `page_block` | `stable` | default | An uncropped image with normalized numbered markers and always-visible descriptions. |
| `related_guides` | Related Guides | contextual | `page_block` | `stable` | default | Explicitly related public guides, deduplicated and excluding the current post. |

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
- Optional fields: `caption`, `role`, `anchor`, `after_block_id`, `focal_point`, `frame`, `enlarge`
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
    },
    "focal_point": {
      "type": "string",
      "enum": [
        "center",
        "top",
        "bottom",
        "left",
        "right"
      ],
      "default": "center"
    },
    "frame": {
      "type": "string",
      "enum": [
        "natural",
        "landscape",
        "portrait"
      ],
      "default": "natural"
    },
    "enlarge": {
      "type": "boolean",
      "default": false
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
- Variants: `default, compact, detailed`
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
- Variants: `default, screenshots`
- Required fields: `items`
- Optional fields: `title`, `anchor`, `screenshots`
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
    },
    "screenshots": {
      "type": "array",
      "maxItems": 20,
      "items": {
        "type": "object",
        "additionalProperties": false,
        "required": [
          "step",
          "media_id",
          "alt"
        ],
        "properties": {
          "step": {
            "type": "integer",
            "minimum": 1
          },
          "media_id": {
            "type": "integer",
            "minimum": 1
          },
          "alt": {
            "type": "string",
            "maxLength": 200
          },
          "caption": {
            "type": "string",
            "maxLength": 300
          }
        }
      }
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

### `route_timeline` — Route Timeline

- Category: `travel`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: Shows a sequence of travel stops with concise supporting details.
- Variants: `default`
- Required fields: `items`
- Optional fields: `title`, `anchor`
- Implementation: `wp-content/themes/solo-to-china/inc/content-components.php`, `wp-content/themes/solo-to-china/inc/cms-articles.php`, `wp-content/themes/solo-to-china-child/assets/css/content-components.css`
- Accessibility: Renders an ordered list so the sequence remains meaningful without visual styling.
- Responsive behavior: Timeline markers and copy stay aligned in one readable column.

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
      "minItems": 2,
      "maxItems": 12,
      "items": {
        "type": "object",
        "additionalProperties": false,
        "required": [
          "title"
        ],
        "properties": {
          "title": {
            "type": "string",
            "minLength": 1
          },
          "detail": {
            "type": "string"
          },
          "transport_mode": {
            "type": "string",
            "maxLength": 300
          },
          "travel_time": {
            "type": "string",
            "maxLength": 300
          },
          "note": {
            "type": "string",
            "maxLength": 300
          },
          "entity_key": {
            "type": "string",
            "maxLength": 300
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
  "type": "route_timeline",
  "variant": "default",
  "title": "A simple first day",
  "items": [
    {
      "title": "Arrive at Beijing South",
      "detail": "Follow Metro signs after the ticket gates."
    },
    {
      "title": "Check in near Qianmen",
      "detail": "Keep the Chinese hotel address ready."
    }
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

### `pros_cons` — Pros and Cons

- Category: `information`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: Balances the advantages and tradeoffs of one travel choice.
- Variants: `default`
- Required fields: `pros`, `cons`
- Optional fields: `title`, `pros_title`, `cons_title`, `anchor`
- Implementation: `wp-content/themes/solo-to-china/inc/content-components.php`, `wp-content/themes/solo-to-china/inc/cms-articles.php`, `wp-content/themes/solo-to-china-child/assets/css/content-components.css`
- Accessibility: Uses explicit headings and semantic lists; meaning never depends on color or icons alone.
- Responsive behavior: The two columns stack in logical reading order on narrow screens.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "pros",
    "cons"
  ],
  "properties": {
    "pros": {
      "type": "array",
      "minItems": 1,
      "maxItems": 8,
      "items": {
        "type": "string",
        "minLength": 1
      }
    },
    "cons": {
      "type": "array",
      "minItems": 1,
      "maxItems": 8,
      "items": {
        "type": "string",
        "minLength": 1
      }
    },
    "title": {
      "type": "string"
    },
    "pros_title": {
      "type": "string"
    },
    "cons_title": {
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
  "type": "pros_cons",
  "variant": "default",
  "title": "Staying near the old city",
  "pros_title": "Works well when",
  "cons_title": "Plan around",
  "pros": [
    "You want to walk to major sights",
    "You prefer an atmospheric base"
  ],
  "cons": [
    "Metro transfers may take longer",
    "Rooms can be smaller"
  ]
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

### `ticket_reminder` — Ticket Booking Window

- Category: `travel`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: Delegates rule-based booking-window timing to SoloToChina Tools while retaining the historical identifier as a compatibility alias.
- Variants: `default`
- Required fields: `attraction_slug`
- Optional fields: `title`, `description`, `anchor`
- Implementation: `wp-content/themes/solo-to-china/functions.php`, `wp-content/plugins/solo-to-china-tools/solo-to-china-tools.php`
- Accessibility: The Plugin owns labels, date validation, status announcements, and booking-timing output.
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

### `destination_card` — Destination / Taxi Card

- Category: `travel`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: Renders a verified Chinese destination card that travelers can show or copy for a driver.
- Variants: `default`
- Required fields: `entity_key`
- Optional fields: `title`, `description`, `anchor`
- Implementation: `wp-content/themes/solo-to-china/inc/content-renderers.php`, `wp-content/plugins/solo-to-china-tools/includes/shortcodes.php`, `wp-content/themes/solo-to-china-child/assets/css/content-components.css`
- Accessibility: Uses semantic headings, a live copy status, keyboard-operable actions, and a focus-contained full-screen dialog.
- Responsive behavior: The destination card scales Chinese text for mobile portrait and provides a distraction-free full-screen Driver Mode.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "entity_key"
  ],
  "properties": {
    "entity_key": {
      "type": "string",
      "minLength": 1,
      "maxLength": 120
    },
    "title": {
      "type": "string",
      "maxLength": 160
    },
    "description": {
      "type": "string",
      "maxLength": 300
    },
    "anchor": {
      "type": "string",
      "maxLength": 120
    }
  }
}
```

Example:

```json
{
  "type": "destination_card",
  "variant": "default",
  "entity_key": "forbidden-city",
  "title": "Show the Forbidden City to a driver"
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

### `affiliate_booking_card` — Affiliate Booking Card

- Category: `commercial`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: Renders a CMS-selected high-intent Trip.com deep or category link after QA.
- Variants: `default`
- Required fields: `affiliate_asset_id`, `provider`, `asset_type`, `product_category`, `title`, `description`, `cta_label`, `target_url`, `disclosure`, `scope_type`, `scope_key`, `slot_key`, `placement`, `strategy_version`
- Optional fields: `price_text`, `entity`, `route`, `destination`, `anchor`
- Implementation: `wp-content/themes/solo-to-china/inc/content-renderers.php`, `wp-content/themes/solo-to-china-child/assets/css/content-components.css`, `wp-content/themes/solo-to-china/assets/js/commercial-events.js`
- Accessibility: Uses a labeled aside, visible disclosure, and a descriptive sponsored link.
- Responsive behavior: Stacks copy and action without horizontal overflow or urgency treatment.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "affiliate_asset_id",
    "provider",
    "asset_type",
    "product_category",
    "title",
    "description",
    "cta_label",
    "target_url",
    "disclosure",
    "scope_type",
    "scope_key",
    "slot_key",
    "placement",
    "strategy_version"
  ],
  "properties": {
    "affiliate_asset_id": {
      "type": "string",
      "minLength": 1,
      "maxLength": 120
    },
    "provider": {
      "type": "string",
      "minLength": 1,
      "maxLength": 80
    },
    "asset_type": {
      "type": "string",
      "enum": [
        "DEEP_LINK",
        "CATEGORY_LINK"
      ]
    },
    "product_category": {
      "type": "string",
      "enum": [
        "HOTEL",
        "FLIGHT",
        "TRAIN",
        "ATTRACTION",
        "TOUR_ACTIVITY",
        "FLIGHT_HOTEL",
        "CAR_RENTAL",
        "AIRPORT_TRANSFER",
        "PLANNER"
      ]
    },
    "title": {
      "type": "string",
      "minLength": 1,
      "maxLength": 160
    },
    "description": {
      "type": "string",
      "minLength": 1,
      "maxLength": 500
    },
    "price_text": {
      "type": "string",
      "maxLength": 120
    },
    "cta_label": {
      "type": "string",
      "minLength": 1,
      "maxLength": 80
    },
    "target_url": {
      "type": "string",
      "format": "uri",
      "pattern": "^https://"
    },
    "disclosure": {
      "type": "string",
      "minLength": 1,
      "maxLength": 300
    },
    "scope_type": {
      "type": "string",
      "enum": [
        "ENTITY",
        "ROUTE",
        "AREA",
        "DESTINATION",
        "COUNTRY",
        "CATEGORY",
        "GLOBAL"
      ]
    },
    "scope_key": {
      "type": "string",
      "minLength": 1,
      "maxLength": 160
    },
    "slot_key": {
      "type": "string",
      "minLength": 1,
      "maxLength": 120
    },
    "placement": {
      "type": "string",
      "enum": [
        "contextual",
        "end_resource"
      ]
    },
    "strategy_version": {
      "type": "string",
      "minLength": 1,
      "maxLength": 40
    },
    "entity": {
      "type": "string",
      "maxLength": 160
    },
    "route": {
      "type": "string",
      "maxLength": 160
    },
    "destination": {
      "type": "string",
      "maxLength": 160
    },
    "anchor": {
      "type": "string",
      "maxLength": 120
    }
  }
}
```

Example:

```json
{
  "type": "affiliate_booking_card",
  "variant": "default",
  "affiliate_asset_id": "trip-forbidden-city",
  "provider": "Trip.com",
  "asset_type": "DEEP_LINK",
  "product_category": "ATTRACTION",
  "title": "Check Forbidden City ticket options",
  "description": "Review the official listing details before booking.",
  "cta_label": "View ticket options",
  "target_url": "https://www.trip.com/",
  "disclosure": "Affiliate link. SoloToChina may earn a commission at no extra cost to you.",
  "scope_type": "ENTITY",
  "scope_key": "forbidden-city",
  "slot_key": "tickets-contextual-1",
  "placement": "contextual",
  "strategy_version": "commercial-v1"
}
```

### `affiliate_search_card` — Affiliate Search Card

- Category: `commercial`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: Renders a CMS-selected Trip.com search link or allowlisted structured search box.
- Variants: `link, search_box`
- Required fields: `affiliate_asset_id`, `provider`, `asset_type`, `product_category`, `title`, `description`, `cta_label`, `disclosure`, `scope_type`, `scope_key`, `slot_key`, `placement`, `strategy_version`
- Optional fields: `target_url`, `embed_config`, `entity`, `route`, `destination`, `anchor`
- Implementation: `wp-content/themes/solo-to-china/inc/content-renderers.php`, `wp-content/themes/solo-to-china-child/assets/css/content-components.css`, `wp-content/themes/solo-to-china/assets/js/commercial-events.js`
- Accessibility: Keeps instructions and disclosure visible; structured embeds include a descriptive title.
- Responsive behavior: Uses a bounded responsive iframe or a full-width link action on phones.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "affiliate_asset_id",
    "provider",
    "asset_type",
    "product_category",
    "title",
    "description",
    "cta_label",
    "disclosure",
    "scope_type",
    "scope_key",
    "slot_key",
    "placement",
    "strategy_version"
  ],
  "oneOf": [
    {
      "required": [
        "target_url"
      ]
    },
    {
      "required": [
        "embed_config"
      ]
    }
  ],
  "properties": {
    "affiliate_asset_id": {
      "type": "string",
      "minLength": 1,
      "maxLength": 120
    },
    "provider": {
      "type": "string",
      "minLength": 1,
      "maxLength": 80
    },
    "asset_type": {
      "const": "SEARCH_BOX"
    },
    "product_category": {
      "type": "string",
      "enum": [
        "HOTEL",
        "FLIGHT",
        "TRAIN",
        "ATTRACTION",
        "TOUR_ACTIVITY",
        "FLIGHT_HOTEL",
        "CAR_RENTAL",
        "AIRPORT_TRANSFER",
        "PLANNER"
      ]
    },
    "title": {
      "type": "string",
      "minLength": 1,
      "maxLength": 160
    },
    "description": {
      "type": "string",
      "minLength": 1,
      "maxLength": 500
    },
    "cta_label": {
      "type": "string",
      "minLength": 1,
      "maxLength": 80
    },
    "target_url": {
      "type": "string",
      "format": "uri",
      "pattern": "^https://"
    },
    "embed_config": {
      "type": "object",
      "additionalProperties": false,
      "required": [
        "embed_type",
        "src",
        "width",
        "height",
        "language",
        "theme",
        "variant"
      ],
      "properties": {
        "embed_type": {
          "const": "search_box"
        },
        "src": {
          "type": "string",
          "format": "uri",
          "pattern": "^https://"
        },
        "width": {
          "type": "integer",
          "minimum": 240,
          "maximum": 1600
        },
        "height": {
          "type": "integer",
          "minimum": 80,
          "maximum": 800
        },
        "language": {
          "type": "string",
          "enum": [
            "en",
            "zh-CN",
            "zh-TW"
          ]
        },
        "theme": {
          "type": "string",
          "enum": [
            "light",
            "dark"
          ]
        },
        "variant": {
          "type": "string",
          "enum": [
            "compact",
            "standard"
          ]
        }
      }
    },
    "disclosure": {
      "type": "string",
      "minLength": 1,
      "maxLength": 300
    },
    "scope_type": {
      "type": "string",
      "enum": [
        "ENTITY",
        "ROUTE",
        "AREA",
        "DESTINATION",
        "COUNTRY",
        "CATEGORY",
        "GLOBAL"
      ]
    },
    "scope_key": {
      "type": "string",
      "minLength": 1,
      "maxLength": 160
    },
    "slot_key": {
      "type": "string",
      "minLength": 1,
      "maxLength": 120
    },
    "placement": {
      "type": "string",
      "enum": [
        "contextual",
        "end_resource"
      ]
    },
    "strategy_version": {
      "type": "string",
      "minLength": 1,
      "maxLength": 40
    },
    "entity": {
      "type": "string",
      "maxLength": 160
    },
    "route": {
      "type": "string",
      "maxLength": 160
    },
    "destination": {
      "type": "string",
      "maxLength": 160
    },
    "anchor": {
      "type": "string",
      "maxLength": 120
    }
  }
}
```

Example:

```json
{
  "type": "affiliate_search_card",
  "variant": "link",
  "affiliate_asset_id": "trip-hotel-search",
  "provider": "Trip.com",
  "asset_type": "SEARCH_BOX",
  "product_category": "HOTEL",
  "title": "Search hotel options",
  "description": "Compare location and arrival access for your dates.",
  "cta_label": "Search hotels",
  "target_url": "https://www.trip.com/hotels/",
  "disclosure": "Affiliate link. SoloToChina may earn a commission at no extra cost to you.",
  "scope_type": "DESTINATION",
  "scope_key": "beijing",
  "slot_key": "hotel-search-1",
  "placement": "contextual",
  "strategy_version": "commercial-v1"
}
```

### `affiliate_banner` — Affiliate Banner

- Category: `commercial`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: Renders a CMS-selected static or allowlisted dynamic Trip.com banner as a restrained fallback.
- Variants: `static, dynamic`
- Required fields: `affiliate_asset_id`, `provider`, `asset_type`, `product_category`, `title`, `description`, `cta_label`, `target_url`, `disclosure`, `scope_type`, `scope_key`, `slot_key`, `placement`, `strategy_version`
- Optional fields: `image_url`, `alt_text`, `embed_config`, `entity`, `route`, `destination`, `anchor`
- Implementation: `wp-content/themes/solo-to-china/inc/content-renderers.php`, `wp-content/themes/solo-to-china-child/assets/css/content-components.css`, `wp-content/themes/solo-to-china/assets/js/commercial-events.js`
- Accessibility: Static images require alt text; dynamic embeds have titles and disclosure outside the frame.
- Responsive behavior: Banner media is width-bounded and cannot overflow the article.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "affiliate_asset_id",
    "provider",
    "asset_type",
    "product_category",
    "title",
    "description",
    "cta_label",
    "target_url",
    "disclosure",
    "scope_type",
    "scope_key",
    "slot_key",
    "placement",
    "strategy_version"
  ],
  "oneOf": [
    {
      "properties": {
        "asset_type": {
          "const": "STATIC_BANNER"
        }
      },
      "required": [
        "image_url",
        "alt_text"
      ]
    },
    {
      "properties": {
        "asset_type": {
          "const": "DYNAMIC_BANNER"
        }
      },
      "required": [
        "embed_config"
      ]
    }
  ],
  "properties": {
    "affiliate_asset_id": {
      "type": "string",
      "minLength": 1,
      "maxLength": 120
    },
    "provider": {
      "type": "string",
      "minLength": 1,
      "maxLength": 80
    },
    "asset_type": {
      "type": "string",
      "enum": [
        "STATIC_BANNER",
        "DYNAMIC_BANNER"
      ]
    },
    "product_category": {
      "type": "string",
      "enum": [
        "HOTEL",
        "FLIGHT",
        "TRAIN",
        "ATTRACTION",
        "TOUR_ACTIVITY",
        "FLIGHT_HOTEL",
        "CAR_RENTAL",
        "AIRPORT_TRANSFER",
        "PLANNER"
      ]
    },
    "title": {
      "type": "string",
      "minLength": 1,
      "maxLength": 160
    },
    "description": {
      "type": "string",
      "minLength": 1,
      "maxLength": 500
    },
    "cta_label": {
      "type": "string",
      "minLength": 1,
      "maxLength": 80
    },
    "target_url": {
      "type": "string",
      "format": "uri",
      "pattern": "^https://"
    },
    "image_url": {
      "type": "string",
      "format": "uri",
      "pattern": "^https://"
    },
    "alt_text": {
      "type": "string",
      "maxLength": 200
    },
    "embed_config": {
      "type": "object",
      "additionalProperties": false,
      "required": [
        "embed_type",
        "src",
        "width",
        "height",
        "language",
        "theme",
        "variant"
      ],
      "properties": {
        "embed_type": {
          "const": "dynamic_banner"
        },
        "src": {
          "type": "string",
          "format": "uri",
          "pattern": "^https://"
        },
        "width": {
          "type": "integer",
          "minimum": 240,
          "maximum": 1600
        },
        "height": {
          "type": "integer",
          "minimum": 80,
          "maximum": 800
        },
        "language": {
          "type": "string",
          "enum": [
            "en",
            "zh-CN",
            "zh-TW"
          ]
        },
        "theme": {
          "type": "string",
          "enum": [
            "light",
            "dark"
          ]
        },
        "variant": {
          "type": "string",
          "enum": [
            "compact",
            "standard"
          ]
        }
      }
    },
    "disclosure": {
      "type": "string",
      "minLength": 1,
      "maxLength": 300
    },
    "scope_type": {
      "type": "string",
      "enum": [
        "ENTITY",
        "ROUTE",
        "AREA",
        "DESTINATION",
        "COUNTRY",
        "CATEGORY",
        "GLOBAL"
      ]
    },
    "scope_key": {
      "type": "string",
      "minLength": 1,
      "maxLength": 160
    },
    "slot_key": {
      "type": "string",
      "minLength": 1,
      "maxLength": 120
    },
    "placement": {
      "const": "end_resource"
    },
    "strategy_version": {
      "type": "string",
      "minLength": 1,
      "maxLength": 40
    },
    "entity": {
      "type": "string",
      "maxLength": 160
    },
    "route": {
      "type": "string",
      "maxLength": 160
    },
    "destination": {
      "type": "string",
      "maxLength": 160
    },
    "anchor": {
      "type": "string",
      "maxLength": 120
    }
  }
}
```

Example:

```json
{
  "type": "affiliate_banner",
  "variant": "static",
  "affiliate_asset_id": "trip-planner-banner",
  "provider": "Trip.com",
  "asset_type": "STATIC_BANNER",
  "product_category": "PLANNER",
  "title": "Continue planning with Trip.com",
  "description": "Check current options and provider terms.",
  "cta_label": "Open Trip.com",
  "target_url": "https://www.trip.com/",
  "image_url": "https://pages.trip.com/banner.jpg",
  "alt_text": "Trip.com travel planning",
  "disclosure": "Affiliate link. SoloToChina may earn a commission at no extra cost to you.",
  "scope_type": "GLOBAL",
  "scope_key": "global",
  "slot_key": "end-resource-banner-1",
  "placement": "end_resource",
  "strategy_version": "commercial-v1"
}
```

### `affiliate_promotion_card` — Affiliate Promotion Card

- Category: `commercial`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: Renders a time-bounded CMS-selected Trip.com promotion without changing editorial conclusions.
- Variants: `default`
- Required fields: `affiliate_asset_id`, `provider`, `asset_type`, `product_category`, `title`, `description`, `cta_label`, `target_url`, `disclosure`, `scope_type`, `scope_key`, `slot_key`, `placement`, `strategy_version`
- Optional fields: `price_text`, `valid_from`, `valid_until`, `entity`, `route`, `destination`, `anchor`
- Implementation: `wp-content/themes/solo-to-china/inc/content-renderers.php`, `wp-content/themes/solo-to-china-child/assets/css/content-components.css`, `wp-content/themes/solo-to-china/assets/js/commercial-events.js`
- Accessibility: States the provider relationship and validity without false urgency.
- Responsive behavior: Stacks copy and action while preserving readable conditions and dates.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "affiliate_asset_id",
    "provider",
    "asset_type",
    "product_category",
    "title",
    "description",
    "cta_label",
    "target_url",
    "disclosure",
    "scope_type",
    "scope_key",
    "slot_key",
    "placement",
    "strategy_version"
  ],
  "properties": {
    "affiliate_asset_id": {
      "type": "string",
      "minLength": 1,
      "maxLength": 120
    },
    "provider": {
      "type": "string",
      "minLength": 1,
      "maxLength": 80
    },
    "asset_type": {
      "const": "PROMOTION"
    },
    "product_category": {
      "type": "string",
      "enum": [
        "HOTEL",
        "FLIGHT",
        "TRAIN",
        "ATTRACTION",
        "TOUR_ACTIVITY",
        "FLIGHT_HOTEL",
        "CAR_RENTAL",
        "AIRPORT_TRANSFER",
        "PLANNER"
      ]
    },
    "title": {
      "type": "string",
      "minLength": 1,
      "maxLength": 160
    },
    "description": {
      "type": "string",
      "minLength": 1,
      "maxLength": 500
    },
    "price_text": {
      "type": "string",
      "maxLength": 120
    },
    "cta_label": {
      "type": "string",
      "minLength": 1,
      "maxLength": 80
    },
    "target_url": {
      "type": "string",
      "format": "uri",
      "pattern": "^https://"
    },
    "disclosure": {
      "type": "string",
      "minLength": 1,
      "maxLength": 300
    },
    "scope_type": {
      "type": "string",
      "enum": [
        "ENTITY",
        "ROUTE",
        "AREA",
        "DESTINATION",
        "COUNTRY",
        "CATEGORY",
        "GLOBAL"
      ]
    },
    "scope_key": {
      "type": "string",
      "minLength": 1,
      "maxLength": 160
    },
    "slot_key": {
      "type": "string",
      "minLength": 1,
      "maxLength": 120
    },
    "placement": {
      "type": "string",
      "enum": [
        "contextual",
        "end_resource"
      ]
    },
    "strategy_version": {
      "type": "string",
      "minLength": 1,
      "maxLength": 40
    },
    "valid_from": {
      "type": "string",
      "format": "date-time"
    },
    "valid_until": {
      "type": "string",
      "format": "date-time"
    },
    "entity": {
      "type": "string",
      "maxLength": 160
    },
    "route": {
      "type": "string",
      "maxLength": 160
    },
    "destination": {
      "type": "string",
      "maxLength": 160
    },
    "anchor": {
      "type": "string",
      "maxLength": 120
    }
  }
}
```

Example:

```json
{
  "type": "affiliate_promotion_card",
  "variant": "default",
  "affiliate_asset_id": "trip-autumn-campaign",
  "provider": "Trip.com",
  "asset_type": "PROMOTION",
  "product_category": "HOTEL",
  "title": "Review the current campaign",
  "description": "Confirm dates and provider terms before booking.",
  "cta_label": "View promotion",
  "target_url": "https://www.trip.com/",
  "disclosure": "Affiliate promotion. SoloToChina may earn a commission at no extra cost to you.",
  "scope_type": "COUNTRY",
  "scope_key": "china",
  "slot_key": "promotion-end-1",
  "placement": "end_resource",
  "strategy_version": "commercial-v1"
}
```

### `article_hero` — Article Hero

- Category: `layout`
- Status: `stable`
- CMS usable: `true` via `presentation_meta`
- Purpose: Frames CMS-owned article identity and featured media without selecting body components.
- Variants: `default, attraction, city, survival, compact, image-led`
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
        "survival",
        "compact",
        "image-led"
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

### `place_info_card` — Place Information Card

- Category: `contextual`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: A canonical place, optional editorial media, published guide and existing Taxi Card link.
- Variants: `default`
- Required fields: `entity_key`
- Optional fields: `media_id`, `alt`, `caption`, `description`, `title`, `anchor`
- Implementation: `wp-content/themes/solo-to-china/inc/experience-components.php`, `wp-content/themes/solo-to-china/assets/css/experience-components.css`
- Accessibility: Semantic text and links remain available without JavaScript; interactive enlargement supports keyboard close and focus return.
- Responsive behavior: Wraps at narrow widths; images reserve intrinsic dimensions.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "entity_key"
  ],
  "properties": {
    "entity_key": {
      "type": "string",
      "maxLength": 120
    },
    "media_id": {
      "type": "integer",
      "minimum": 1
    },
    "alt": {
      "type": "string",
      "maxLength": 200
    },
    "caption": {
      "type": "string",
      "maxLength": 300
    },
    "description": {
      "type": "string",
      "maxLength": 500
    },
    "title": {
      "type": "string",
      "maxLength": 160
    },
    "anchor": {
      "type": "string",
      "maxLength": 120
    }
  }
}
```

Example:

```json
{
  "type": "place_info_card",
  "variant": "default",
  "entity_key": "forbidden-city",
  "title": "Forbidden City"
}
```

### `annotated_image` — Annotated Image

- Category: `media`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: An uncropped image with normalized numbered markers and always-visible descriptions.
- Variants: `default`
- Required fields: `media_id`, `alt`, `annotations`
- Optional fields: `caption`, `title`, `anchor`
- Implementation: `wp-content/themes/solo-to-china/inc/experience-components.php`, `wp-content/themes/solo-to-china/assets/css/experience-components.css`
- Accessibility: Semantic text and links remain available without JavaScript; interactive enlargement supports keyboard close and focus return.
- Responsive behavior: Wraps at narrow widths; images reserve intrinsic dimensions.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [
    "media_id",
    "alt",
    "annotations"
  ],
  "properties": {
    "media_id": {
      "type": "integer",
      "minimum": 1
    },
    "alt": {
      "type": "string",
      "maxLength": 200
    },
    "caption": {
      "type": "string",
      "maxLength": 300
    },
    "annotations": {
      "type": "array",
      "minItems": 1,
      "maxItems": 20,
      "items": {
        "type": "object",
        "additionalProperties": false,
        "required": [
          "x",
          "y",
          "text"
        ],
        "properties": {
          "x": {
            "type": "number",
            "minimum": 0,
            "maximum": 1
          },
          "y": {
            "type": "number",
            "minimum": 0,
            "maximum": 1
          },
          "text": {
            "type": "string",
            "maxLength": 800
          }
        }
      }
    },
    "title": {
      "type": "string",
      "maxLength": 160
    },
    "anchor": {
      "type": "string",
      "maxLength": 120
    }
  }
}
```

Example:

```json
{
  "type": "annotated_image",
  "variant": "default",
  "media_id": 123,
  "alt": "Entrance diagram",
  "annotations": [
    {
      "x": 0.5,
      "y": 0.5,
      "text": "The numbered entrance shown in this illustration."
    }
  ]
}
```

### `related_guides` — Related Guides

- Category: `contextual`
- Status: `stable`
- CMS usable: `true` via `page_block`
- Purpose: Explicitly related public guides, deduplicated and excluding the current post.
- Variants: `default`
- Required fields: None
- Optional fields: `entity_keys`, `post_ids`, `guide_type`, `title`, `anchor`
- Implementation: `wp-content/themes/solo-to-china/inc/experience-components.php`, `wp-content/themes/solo-to-china/assets/css/experience-components.css`
- Accessibility: Semantic text and links remain available without JavaScript; interactive enlargement supports keyboard close and focus return.
- Responsive behavior: Wraps at narrow widths; images reserve intrinsic dimensions.

Schema:

```json
{
  "type": "object",
  "additionalProperties": false,
  "required": [],
  "properties": {
    "entity_keys": {
      "type": "array",
      "maxItems": 12,
      "items": {
        "type": "string",
        "maxLength": 120
      }
    },
    "post_ids": {
      "type": "array",
      "maxItems": 12,
      "items": {
        "type": "integer",
        "minimum": 1
      }
    },
    "guide_type": {
      "type": "string",
      "enum": [
        "city-guide",
        "attraction-guide",
        "survival-kit",
        "travel-guide"
      ],
      "default": "attraction-guide"
    },
    "title": {
      "type": "string",
      "maxLength": 160
    },
    "anchor": {
      "type": "string",
      "maxLength": 120
    }
  }
}
```

Example:

```json
{
  "type": "related_guides",
  "variant": "default",
  "entity_keys": [
    "forbidden-city"
  ],
  "guide_type": "attraction-guide",
  "title": "Read next"
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
- `affiliate_product_card` for a future structured single-product offer.
- `affiliate_comparison_card` for a future CMS-authored comparison of commercial options.
- `affiliate_disclosure` for a future standalone disclosure when composition requires one.

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
