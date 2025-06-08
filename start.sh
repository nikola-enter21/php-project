#!/bin/bash

set -e

# Step 1: Start the PostgreSQL container
echo "Starting PostgreSQL container..."
docker-compose up -d FMI_DB

# Step 2: Wait for PostgreSQL to be ready
echo "Waiting for PostgreSQL to be ready..."
until docker exec fmi_db pg_isready -U postgres > /dev/null 2>&1; do
  sleep 1
done
echo "PostgreSQL is ready."

# Step 3: Run SQL migrations
echo "Running database migrations from ./setup/db.sql..."
docker cp ./setup/db.sql fmi_db:/db.sql
docker exec -u postgres fmi_db psql -U postgres -d postgres -f /db.sql
echo "Migrations applied."

# Step 4: Start PHP server
echo "Starting PHP server on localhost:8000..."
php -S localhost:8000
