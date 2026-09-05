# Refined Class Diagram — Artmandu AGMS

## What was refined

This diagram preserves the original domain model and adds controller
responsibilities derived from the procedural PHP request handlers. Classes are
grouped into Account Management, Artwork Catalogue, and Order Management so
most relationships remain inside one feature section.

## Why a separate refined diagram

The conceptual class diagram remains unchanged as the analysis model. This
separate diagram adds implementation responsibilities without replacing or
overloading the original artifact.

## Diagram

Source: [`09-refined-class-diagram.puml`](09-refined-class-diagram.puml)

```bash
plantuml docs/diagrams/09-refined-class-diagram.puml
```

## Implementation mapping

| Refined class | Code evidence |
|---|---|
| AccountController | Registration, login, profile, password, and logout PHP pages |
| ArtworkController | Root catalogue pages plus artist and administrator artwork handlers |
| OrderController | `art-enquiry.php`, `my-orders.php`, and administrator order review |
| RecommendationService | `includes/recommendation_functions.php` |
| AIDetectionClient | `includes/cnn_helper.php` |

## Accuracy boundaries

- Controller classes are UML representations of responsibilities distributed
  across procedural PHP files; the codebase does not declare these PHP classes.
- `RecommendationService` and `AIDetectionClient` retain the names of their
  implemented functions.
- Each controller is placed directly beside its related domain classes. Only
  artist ownership, customer ordering, and the ordered artwork cross package
  boundaries.
- Feature packages are stacked vertically. Related services and domain classes
  are aligned in horizontal rows inside their package.
- `ArtistProfile`, `Artwork`, and `Order` form the central vertical routing
  spine. Other classes remain in horizontal rows around that spine.
- The output is constrained to a `1000 × 1414` portrait canvas so the complete
  three-section diagram can be placed on one A4 portrait page.
- Method arguments and return types are intentionally omitted for readability.
- Optional values use `0..1`; relationship multiplicities match the conceptual
  class model.
- Straight polyline, solid, arrowless connections keep relationship labels on
  their lines; white inline backgrounds prevent connectors crossing the text.
