#!/bin/bash

set -e

# Step 1: Wait for PostgreSQL to be ready
echo "Waiting for PostgreSQL to be ready..."
until docker exec fmi_db pg_isready -U postgres > /dev/null 2>&1; do
  sleep 1
done
echo "PostgreSQL is ready."

# Step 2: Run SQL migrations
echo "Running database migrations..."
docker cp ./migrations/db.sql fmi_db:/db.sql
docker exec -u postgres fmi_db psql -U postgres -d postgres -f /db.sql
echo "Migrations applied."