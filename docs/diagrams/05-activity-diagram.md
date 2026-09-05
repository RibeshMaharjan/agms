# Activity Diagram — Artmandu Art Gallery Management System

## What this diagram represents

This activity diagram models the artwork-purchase business process from gallery
browsing through authentication, order creation, eSewa payment, administrator
review, and customer status viewing.

## Why this diagram is needed

- It shows the actions and decisions that form the complete process.
- It identifies which participant is responsible for each activity.
- It documents validation, payment, approval, and cancellation branches.
- It complements the sequence diagram by emphasizing workflow rather than
  participant messages.

## Why PlantUML

PlantUML has native activity syntax for actions, decisions, start and end nodes,
and swimlanes. The diagram uses a vertical process with white activities, thin
arrows, and four clearly named responsibility lanes.

## Diagram

Source: [`05-activity-diagram.puml`](05-activity-diagram.puml)

```bash
plantuml docs/diagrams/05-activity-diagram.puml
```

## Swimlanes

| Swimlane | Responsibility |
|---|---|
| Customer | Selects artwork, supplies purchase details, and views outcomes |
| Artmandu System | Authenticates, validates, creates orders, and stores decisions |
| eSewa Gateway | Processes the external payment |
| Administrator | Reviews pending orders and chooses an outcome |

## Process summary

1. The customer browses the gallery and selects an artwork.
2. An unauthenticated customer must provide valid login credentials.
3. The system validates the purchase details and creates a `Pending` order.
4. The artwork price is loaded and the eSewa payment fields are signed.
5. The gateway processes payment and the system verifies its response.
6. The administrator reviews the pending order and records `Approved` or
   `Cancelled` with a remark.
7. The customer can view the stored order status.

## Accuracy boundaries

- The order is created before payment and remains pending even when payment
  fails or verification is invalid.
- Payment verification does not update `tblorder.Status`.
- Administrator review is shown after the payment branch for readability, but
  the implementation does not enforce payment success before review.
- Login and purchase validation failures end the depicted attempt; the user may
  begin another attempt through the interface.

## Code traceability

| Process section | Implementation source |
|---|---|
| Gallery and artwork selection | `index.php`, `single-product.php` |
| Authentication | `login.php` |
| Purchase validation and pending order | `art-enquiry.php` |
| eSewa processing and verification | `e-sewa.php`, `esewa/success.php`, `esewa/failure.php` |
| Administrator review | `admin/view-order-detail.php` |
| Customer order status | `my-orders.php` |
