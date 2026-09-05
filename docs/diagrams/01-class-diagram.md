# Class Diagram — Artmandu Art Gallery Management System

## What this diagram represents

A class diagram describes a system's static object model: the important
concepts, the data each concept carries, and the relationships between them.
It provides a shared vocabulary for the object, state, sequence, activity, and
use-case diagrams that follow.

This is a **conceptual model reverse-engineered from the current codebase**.
The application uses procedural PHP rather than PHP domain classes, so each UML
class represents a domain concept backed by persisted data, not an existing PHP
class declaration. Its methods are conceptual responsibilities evidenced by the
PHP workflows; they are not claims that matching PHP class methods exist.

## Why this diagram is needed

- It identifies the system's core business objects before their behavior is
  modeled.
- It records multiplicities such as one artist having many artworks.
- It exposes implementation gaps that later refined diagrams must handle,
  especially logical relationships that lack database constraints.
- It prevents later behavioral diagrams from using inconsistent names or
  relationships.

## Why the classes include methods

Methods are optional in a high-level conceptual class diagram, but they are
useful here because they connect each domain object to the behavior implemented
by the application. Only core operations are shown so the diagram remains
readable. Complete CRUD handlers, controllers, services, and exact technical
signatures remain part of the later refined class diagram.

## Why PlantUML

PlantUML has stronger support than Mermaid for UML class details such as method
compartments, enums, multiplicities, and centered relationship labels. The
portrait layout groups related classes into Accounts, Artwork Catalogue, and
Ordering, using straight, arrowless associations and slightly heavier borders
and connections.

## Diagram

Source: [`01-class-diagram.puml`](01-class-diagram.puml)

Render it with a PlantUML plugin, compatible online renderer, or the PlantUML
CLI:

```bash
plantuml docs/diagrams/01-class-diagram.puml
```

## How to read the model

- `User` represents registered customer and artist login accounts.
- `ArtistProfile` stores artist-specific information. An artist account may
  link to one profile through `ArtistProfileID`.
- `Artwork` belongs logically to one artist, one art type, and one art medium.
- `Order` contains a snapshot of customer contact details and references one
  artwork.
- `Administrator` remains separate because the admin login reads `tbladmin`.
- Solid lines show conceptual UML associations. Some are implemented through
  PHP queries rather than database foreign-key constraints, as recorded below.
- `0..1` is used only where a value or relationship is optional. `1` means
  exactly one, and `0..*` means zero or more.

## Verified implementation details

- `UserRole` comes from `migration_add_artist_role.sql`: `user`, `artist`, and
  `admin`.
- The implemented order outcomes are `Pending`, `Approved`, and `Cancelled`.
  Some legacy admin queries also treat a null or empty status as pending.
- `Artwork.isAIGenerated` is nullable: `true` means AI-generated, `false` means
  human-created, and null means detection was not completed.
- Orders do not store a user ID. `my-orders.php` associates them with the logged
  in user by matching email addresses.
- The initial schema indexes `Order.Artpdid`, but it does not declare foreign
  keys for artwork, artist, type, medium, or order relationships.

## Code traceability

| Model area | Primary evidence |
|---|---|
| Entities and attributes | `database/newagms.sql` and both database migrations |
| Users, roles, and artist profiles | `login.php`, `register.php`, `admin/add-artist.php`, `artist/profile.php` |
| Artwork ownership and classification | Artist/admin artwork pages and `includes/cnn_helper.php` |
| Orders and statuses | `art-enquiry.php`, `my-orders.php`, `admin/view-order-detail.php` |

## Refinement boundary

The diagram includes only core domain operations. Detailed controllers,
services, authentication flow, recommendation internals, eSewa integration,
the AI-detection API, and detector implementations remain intentionally
excluded. They belong in the later refined class, sequence, component, and
deployment diagrams after the behavioral models establish their
responsibilities.
