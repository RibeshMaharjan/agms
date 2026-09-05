# Initial Conceptual Class Diagram Plan

## Summary

Create the first requested documentation artifact only: a conceptual class
diagram for **Artmandu — Art Gallery Management System**. Use PlantUML because
it supports UML classes, enums, multiplicities, stereotypes, and relationship
notes directly.

## Implementation Changes

- Refine `docs/diagrams/01-class-diagram.puml` to contain only verified domain
  concepts: `User`, `Administrator`, `ArtistProfile`, `Artwork`, `ArtType`,
  `ArtMedium`, `Order`, `UserRole`, and `OrderStatus`.
- Show entity attributes and concise, code-backed business operations. Defer
  service classes, controllers, complete CRUD APIs, payment internals, and CNN
  implementation classes to the later refined class diagram.
- Model the verified relationships:
  - An artist profile owns artworks.
  - An artwork belongs to one artist, art type, and art medium.
  - An order references an artwork.
  - An artist user can optionally link to an artist profile.
  - User-to-order ownership is inferred through email because an order does not
    store a user ID.
- Use the implemented order states: `Pending`, `Approved`, and `Cancelled`.
- Group classes into Accounts, Artwork Catalogue, and Ordering so the result
  fits a portrait report page.
- Use straight, solid, arrowless associations with centered labels, slightly
  heavier entity borders and connections, and an unlabeled divider between
  attributes and methods. Document relationships that are not enforced by
  database foreign keys in the companion explanation.
- Update `docs/diagrams/01-class-diagram.md` to explain the diagram's purpose,
  why it is needed, why PlantUML was selected, its boundaries, traceability to
  the code, and known schema inconsistencies.

## Interfaces and Compatibility

- This is documentation-only work; do not change PHP, database, API, or
  deployment behavior.
- Preserve the nullable `IsAIGenerated` meaning: human-created, AI-generated,
  or not checked.
- Document the overlap between the separate administrator table and the
  `admin` user role without inventing inheritance.

## Validation

- Check every modeled attribute against the SQL schema and migrations.
- Check relationship multiplicities against PHP joins and CRUD workflows.
- Check each displayed operation against an implemented PHP workflow.
- Validate the PlantUML declarations and relationship syntax.
- Confirm that no dotted relationship or attached note is visible.
- Confirm the Markdown references the correct `.puml` source and includes a
  rendering command.
- Ensure the result is readable as a standalone conceptual model and excludes
  implementation classes reserved for refinement.

## Assumptions

- The existing class-diagram draft may be revised in place.
- Deliverables are PlantUML source plus Markdown explanation; no rendered SVG
  will be committed.
- Diagram refinement and all subsequent diagrams remain untouched until the
  use-case diagram is reviewed.
