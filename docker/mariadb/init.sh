#!/bin/bash
set -e

echo "Waiting for MariaDB to be ready..."
until mysqladmin ping -h localhost --silent 2>/dev/null; do
    sleep 1
done

echo "Importing database schema..."
mysql -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE" < /docker-entrypoint-initdb.d/sql/newagms.sql

echo "Running AI column migration..."
mysql -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE" < /docker-entrypoint-initdb.d/sql/migration_add_ai_column.sql

echo "Database initialization complete."
