# State Diagram — Artmandu Art Gallery Management System

## What this diagram represents

This diagram presents the main states of the Artmandu application from access
through its authenticated features. It combines the overall session state with
nested feature states for purchasing, artwork management, and order review.

## Why this diagram is needed

- It shows how a visitor enters and leaves the authenticated system.
- It groups the principal workflows available after login.
- It records the important alternatives in purchasing and order review.
- It provides one readable behavioral overview for the project report.

## Why PlantUML

PlantUML supports nested states, initial and final nodes, guards, and labeled
transitions. Each workflow runs downward from its own initial node, while the
three feature groups align horizontally. The documentation style uses white
states, CSS-based padding, lighter straight connections, compact labels with
white backing, and no text inside individual state boxes.

## Diagram

Source: [`03-state-diagram.puml`](03-state-diagram.puml)

```bash
plantuml docs/diagrams/03-state-diagram.puml
```

## State groups

### System access

The application begins unauthenticated. Login opens one generalized
authenticated system state, and logout ends the session. Registration is not
shown because creating an account does not change the current session state.
Customer, artist, and administrator sessions are intentionally generalized;
role guards appear only where access differs.

### Gallery and purchase

An authenticated user can browse artworks, inspect details, submit a purchase,
and continue to eSewa. A valid gateway response completes the payment; failure
or invalid verification returns the user to the gallery. Viewing My Orders is
an optional action after successful payment rather than an automatic redirect.

### Artwork management

Artists manage their own artworks, while administrators manage the wider
catalogue. Both workflows support listing, adding, editing, and deleting
artworks, so they share one generalized state group.

### Order review

A submitted purchase creates a pending order. An administrator can approve or
cancel it while storing a remark. These are the final implemented order
outcomes.

## Accuracy boundaries

- Payment completion does not update `tblorder.Status`; administrator review
  remains a separate feature state.
- Registration does not automatically log the new user in.
- The customer order page contains display rules for `Accepted` and `Rejected`,
  but administrator actions actually store `Approved` and `Cancelled`.
- The diagram generalizes role-specific dashboards and session variables into
  one authenticated state without claiming their implementations are identical.
- AI classification is intentionally excluded from this state diagram.

## Code traceability

| State group | Implementation source |
|---|---|
| Registration, login, and logout | `register.php`, `login.php`, `admin/login.php`, `logout.php` |
| Gallery and purchase | `index.php`, `single-product.php`, `art-enquiry.php`, `e-sewa.php`, `esewa/success.php` |
| Customer order history | `my-orders.php` |
| Artwork management | `artist/manage-art.php`, `artist/add-art.php`, `artist/edit-art.php`, `admin/manage-art-product.php` |
| Order review | `admin/view-order-detail.php` |
