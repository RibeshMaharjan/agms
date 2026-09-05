# Sequence Diagram — Artmandu Art Gallery Management System

## What this diagram represents

This sequence diagram summarizes the principal interactions between a user,
browser, Artmandu PHP application, MySQL database, and eSewa gateway. Time moves
from top to bottom through authentication, dashboard viewing, artwork
operations, and purchasing.

## Why this diagram is needed

- It identifies which participant handles each request.
- It shows the chronological order of browser, application, and database calls.
- It connects the project's main features in one A4-friendly overview.
- It complements the state diagram by showing messages instead of conditions.

## Why PlantUML

PlantUML provides native participants, lifelines, message arrows, and named
groups. A wider layout keeps participant and message labels on one line, while
white participants and thin solid messages keep the sequence readable.

## Diagram

Source: [`04-sequence-diagram.puml`](04-sequence-diagram.puml)

```bash
plantuml docs/diagrams/04-sequence-diagram.puml
```

## Participants

| Participant | Responsibility |
|---|---|
| User | Represents a customer, artist, or administrator according to the active operation |
| Browser | Sends forms and page requests and displays application responses |
| Artmandu PHP Application | Performs authentication, validation, routing, and business operations |
| MySQL Database | Stores accounts, artworks, and orders |
| eSewa Gateway | Processes customer payment requests |

## Sequence groups

### Authentication

The browser submits credentials to the application. The application checks the
database, loads the account role, and redirects to the appropriate interface.

### Dashboard view

The application receives a dashboard request, loads the appropriate summary
data, and renders the result. The exact data differs by role: the artist
dashboard summarizes owned artwork, while the administrator dashboard includes
catalogue and order totals.

### Artwork operations

An artist or administrator can add, edit, or delete artwork. The application
validates the submitted values and, for artist edits or deletions, verifies
ownership before changing the database.

### Purchase and payment

A customer submits purchase details. The application validates the request,
creates a `Pending` order, reads its artwork price, and signs the eSewa form.
The browser submits the payment and forwards the returned callback for status
and signature verification.

## Accuracy boundaries

- The single `User` actor generalizes customer, artist, and administrator
  behavior; the relevant role is stated in each group description.
- The procedural PHP application has no separate Model or Livewire component,
  so the diagram does not invent those layers from the reference diagram.
- Success and failure alternatives are summarized as single response messages
  to keep the overview short enough for A4 documentation.
- Order creation and artwork-price lookup are shown as one database interaction,
  although the implementation executes two queries.
- Administrator order review remains excluded as requested.

## Code traceability

| Sequence group | Implementation source |
|---|---|
| Authentication | `login.php`, `admin/login.php` |
| Dashboard view | `index.php`, `artist/dashboard.php`, `admin/dashboard.php` |
| Artwork operations | `artist/add-art.php`, `artist/edit-art.php`, `artist/manage-art.php`, `admin/manage-art-product.php` |
| Purchase and payment | `art-enquiry.php`, `e-sewa.php`, `esewa/success.php`, `esewa/failure.php` |
