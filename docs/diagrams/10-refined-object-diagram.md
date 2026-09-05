# Refined Object Diagram — Artmandu AGMS

## What was refined

This object diagram instantiates the classes introduced by the refined class
diagram. It retains the verified catalogue snapshot and runtime purchase, then
adds the controller and service objects responsible for those instances.

## Why a separate refined diagram

The original object diagram remains the conceptual domain snapshot. This
separate version demonstrates how application-logic objects connect to the
same runtime domain objects.

## Diagram

Source: [`10-refined-object-diagram.puml`](10-refined-object-diagram.puml)

```bash
plantuml docs/diagrams/10-refined-object-diagram.puml
```

## Snapshot interpretation

- `accountRequest` manages the example customer account.
- `artworkRequest` manages the seeded `Dreamscape` artwork and uses the
  recommendation and AI-detection helpers.
- `orderRequest` creates a runtime pending order for that artwork.
- The customer and order use the same email because the application retrieves
  personal orders through an email match rather than a stored user ID.

## Accuracy boundaries

- Controller and service objects represent runtime responsibilities in the
  procedural PHP application; they are UML instances, not declared PHP objects.
- Seeded catalogue values come from `database/newagms.sql`; customer and order
  values are labeled as runtime examples.
- The three runtime sections are stacked vertically for A4 portrait output.
- Account and order request controllers are positioned vertically above the
  domain objects they manage.
- Straight orthogonal, solid, arrowless links are used throughout. Short labels
  that PlantUML otherwise displaces are anchored directly to association ends.
