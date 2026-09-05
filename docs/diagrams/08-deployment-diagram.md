# Deployment Diagram — Artmandu Art Gallery Management System

## What this diagram represents

A deployment diagram shows where software artifacts run and how the runtime
nodes communicate. It maps the logical components to client devices,
containers, hosts, ports, and persistent storage.

## Why this diagram is needed

- It separates the browser, PHP application, database, and CNN runtime nodes.
- It documents container boundaries and network ports used during deployment.
- It shows which data survives container replacement.
- It identifies the CNN detector as an Artmandu-owned microservice.

## Why PlantUML

PlantUML supports UML deployment nodes, nested execution environments,
artifacts, databases, storage, and labeled communication paths. The diagram
uses a white background, vertical host organization, and thin straight arrows.

## Diagram

Source: [`08-deployment-diagram.puml`](08-deployment-diagram.puml)

```bash
plantuml docs/diagrams/08-deployment-diagram.puml
```

## Deployment nodes

| Node | Deployed artifact or service | Verified configuration |
|---|---|---|
| Client Device | Web Browser | Connects to the web application over HTTP |
| Web Container | Apache server and mounted PHP application | `php:8.4-apache`; container port `80`, host port `6767` |
| Database Container | MariaDB server and AGMS tables | `mariadb:11`; internal port `3306`, optional host port `3308` |
| Named Volume | Persistent MariaDB files | `mariadb_data` |
| Artmandu CNN Host | FastAPI/Uvicorn, AI detector, and detection model | Separately started owned service on port `7070` |

## Communication paths

- The browser reaches Apache through host port `6767`.
- Docker bind-mounts the project directory at `/var/www/html` in the web
  container.
- Apache serves the PHP application from `/var/www/html`.
- PHP connects to the `mariadb` service on port `3306` inside the Compose
  network.
- MariaDB stores its files in the `mariadb_data` named volume.
- The PHP AI-detection client calls the owned FastAPI `/detect` endpoint on
  port `7070`.
- FastAPI passes the uploaded image to the detector, which loads the configured
  detection model.

## Accuracy boundaries

- The web and MariaDB containers are defined in `docker-compose.yml`.
- The CNN microservice belongs to Artmandu but is not currently defined in the
  Compose stack, so it is shown on a separately managed host.
- The configured CNN hostname can vary by environment; the stable deployment
  interface shown here is HTTP port `7070`.
- Third-party payment infrastructure is outside this diagram; Payment Handoff
  is the application boundary documented in the component diagram.
