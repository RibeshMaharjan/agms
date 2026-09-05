# Component Diagram — Artmandu Art Gallery Management System

## What this diagram represents

A component diagram shows the major software parts of a system and their
dependencies. Unlike a class diagram, it focuses on modules, workflows,
services, and infrastructure rather than object properties and methods.

## Why this diagram is needed

- It separates presentation, application, domain, and infrastructure concerns.
- It shows how the three web interfaces reach business data and services.
- It distinguishes the PHP application from its owned supporting microservice.

## Why PlantUML

PlantUML clearly supports UML 2 component symbols, nested architectural layers,
databases, and service boundaries. The diagram uses white components, straight
solid arrows, normal-weight connections, and CSS-based spacing. Components are
horizontal within each layer, while the four layers are stacked vertically.

## Diagram

Source: [`07-component-diagram.puml`](07-component-diagram.puml)

```bash
plantuml docs/diagrams/07-component-diagram.puml
```

## Components

| Layer and component | Responsibility | Primary implementation |
|---|---|---|
| Presentation — Storefront and Customer Pages | Browsing, login, profiles, purchases, and order history | Root PHP pages |
| Presentation — Artist Portal Pages | Artist dashboard, profile, and owned-artwork screens | `artist/` |
| Presentation — Administration Pages | Administrative dashboards and management screens | `admin/` |
| Application — Customer Workflows | Coordinates customer-facing catalogue and ordering actions | Root PHP request handlers |
| Application — Artist Artwork Workflows | Restricts artwork operations to the logged-in artist | `artist/add-art.php`, `artist/edit-art.php`, `artist/manage-art.php` |
| Application — Administrative Workflows | Coordinates catalogue, account, artwork, and order management | `admin/*.php` request handlers |
| Application — Authentication and Session Handling | Validates credentials and protects authenticated pages | Login pages and session guards |
| Domain — Accounts, Artwork Catalogue, Ordering | Groups the persistent business concepts from the class model | `tblusers`, `tbladmin`, `tblartist`, `tblartproduct`, catalogue tables, `tblorder` |
| Domain — Recommendation Service | Finds similar artworks using catalogue attributes | `includes/recommendation_functions.php` |
| Domain — AI Detection Client | Sends uploaded images to the detection API | `includes/cnn_helper.php` |
| Domain — Payment Handoff | Forms the application boundary for payment submission | `e-sewa.php` |
| Infrastructure — Database Connection | Creates the shared MySQLi connection | Both `includes/dbconnection.php` files |
| Infrastructure — MariaDB | Persists all application data | `database/newagms.sql` |
| Infrastructure — Artmandu CNN Detection Service | Artmandu-owned microservice that classifies uploaded artwork images | `cnn/` FastAPI application |

## Accuracy boundaries

- The PHP application is procedural, so the workflow components group request
  handlers rather than representing framework controllers.
- The three presentation components correspond to the root, `artist/`, and
  `admin/` page areas in the repository.
- Only the principal layer and integration dependencies are drawn; repeated
  page-to-model connections are summarized by their containing packages.
- The CNN service is an Artmandu-owned microservice. It runs separately because
  it is not included in the current Docker Compose stack.
- Payment Handoff is the endpoint of the payment path; the downstream payment
  provider is outside this diagram's boundary.
- Apache, containers, ports, and hosts are reserved for the deployment diagram.
