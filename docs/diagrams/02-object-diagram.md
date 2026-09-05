# Object Diagram — Artmandu Art Gallery Management System

## What this diagram represents

An object diagram is a snapshot of class instances and their values at one
moment. While the class diagram says that users can place orders for artworks,
this diagram shows one concrete example of those objects and links.

## Why this diagram is needed

- It verifies that the class model can represent a real purchase scenario.
- It makes abstract relationships easier to understand using sample values.
- It distinguishes a class, such as `Artwork`, from an instance, such as
  `dreamscape : Artwork`.
- It provides concrete objects that later sequence and state diagrams can use.

## Why PlantUML

PlantUML has native object syntax and supports named instances, attribute
values, packages, and labeled links. The styling follows the approved class
diagram: straight bold connections, no arrowheads, centered labels, and spaced
attribute rows.

## Diagram

Source: [`02-object-diagram.puml`](02-object-diagram.puml)

```bash
plantuml docs/diagrams/02-object-diagram.puml
```

## Snapshot described

The diagram shows an example customer placing a pending order for the seeded
artwork **Dreamscape**:

- `dreamscape : Artwork` is product ID 9, priced at NPR 9000.
- `uma : ArtistProfile` is artist ID 11 and created the artwork.
- `animeArt : ArtType` is type ID 6 and classifies the artwork.
- `acrylic : ArtMedium` is medium ID 2 and is used by the artwork.
- `customer1 : User` and `order1 : Order` are illustrative runtime objects.

## Accuracy boundary

The artist, artwork, art type, and art medium values come directly from
`database/newagms.sql`. The dump contains no seeded order or user rows, so the
customer identity and order number are deliberately labeled as runtime/example
values rather than existing records. The pending status follows the implemented
purchase flow in `art-enquiry.php`; its NPR 9000 payment amount comes from the
linked artwork's selling price.

Object diagrams normally show instance values, not methods. The available
operations remain documented in the class diagram.
