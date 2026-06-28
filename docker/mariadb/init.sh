#!/bin/bash
set -e

echo "Waiting for MariaDB to be ready (via Unix socket)..."
until mariadb-admin ping --silent 2>/dev/null; do
    sleep 1
done

echo "Importing database schema..."
mariadb -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE" < /docker-entrypoint-initdb.d/sql/newagms.sql

echo "Running AI column migration..."
COLUMN_EXISTS=$(mariadb -u root -p"$MYSQL_ROOT_PASSWORD" -N -B \
    -e "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='$MYSQL_DATABASE' AND TABLE_NAME='tblartproduct' AND COLUMN_NAME='IsAIGenerated'")
if [ "$COLUMN_EXISTS" -eq 0 ]; then
    mariadb -u root -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE" < /docker-entrypoint-initdb.d/sql/migration_add_ai_column.sql
    echo "AI column migration applied."
else
    echo "AI column already exists, skipping migration."
fi

echo "Database initialization complete."
