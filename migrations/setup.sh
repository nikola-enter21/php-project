#!/bin/bash

set -e

# Step 1: Wait for MariaDB to be ready
echo "Waiting for MariaDB to be ready..."
until docker exec fmi_db mariadb-admin ping -h "localhost" --silent; do
  sleep 1
done
echo "MariaDB is ready."

# Step 2: Run SQL migrations
echo "Running database migrations..."
docker cp ./migrations/db.sql fmi_db:/db.sql
docker exec fmi_db sh -c "mariadb -u root -proot app_db < /db.sql"
echo "Migrations applied."

# Step 3: Seeding the database
echo "Seeding the database..."
docker cp ./seeds/seed.sql fmi_db:/seed.sql
docker exec fmi_db sh -c "mariadb -u root -proot app_db < /seed.sql"
echo "Database seeded."
