# Use-Case Diagram — Artmandu Art Gallery Management System

## What this diagram represents

This use-case diagram shows the services Artmandu provides to three human
actors. It describes available capabilities without showing page order,
database structure, or implementation classes.

## Why this diagram is needed

- It defines the functional boundary of the system.
- It separates visitor, customer, and administrator responsibilities.
- It provides a concise requirements overview for the project report.

## Why PlantUML

PlantUML supports standard use-case ellipses, actors, system boundaries, and
grouped capabilities. The diagram uses white elements, straight solid
associations, and three vertically organized feature areas without dotted
dependencies.

## Diagram

Source: [`06-use-case-diagram.puml`](06-use-case-diagram.puml)

```bash
plantuml docs/diagrams/06-use-case-diagram.puml
```

## Participants

| Participant | Type | Main responsibilities |
|---|---|---|
| Visitor | Human actor | Browse and search artworks, inspect details, register, and login |
| Customer | Human actor | Maintain a profile, purchase artwork, pay, and view personal orders |
| Administrator | Human actor | Manage accounts, artists, catalogue data, artworks, and orders |

## Use-case groups

### Public access

Public pages allow visitors to browse the gallery, search, view artwork details,
register, and log in.

### Customer services

Authenticated customers can update profile information, submit an artwork
purchase, complete payment, and review their orders.

### Administration

Administrators can view system totals; manage users, artists, art types, art
mediums, and artworks; and approve or cancel pending orders. Art types and
mediums are grouped under `Manage Catalogue` to keep the diagram compact.

## Accuracy boundaries

- Human-actor capabilities are connected directly without inheritance so the
  diagram does not repeat public associations for every authenticated role.
- `Purchase Artwork` and `Process Payment` are separate use cases because the
  application creates the pending order before external payment processing.
- No `include` or `extend` dependencies are drawn; their dotted notation would
  add clutter without improving this high-level requirements view.
- AI classification and Site Content are intentionally excluded.

## Code traceability

| Capability group | Implementation source |
|---|---|
| Public access | `index.php`, `search.php`, `single-product.php`, `register.php`, `login.php` |
| Customer services | `my-profile.php`, `art-enquiry.php`, `my-orders.php` |
| Administration | `admin/dashboard.php`, `admin/manage-users.php`, `admin/manage-artist.php`, `admin/manage-art-product.php`, `admin/view-order-detail.php` |
