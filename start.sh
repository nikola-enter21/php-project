#!/bin/bash

set -e

echo "Стартиране на Docker контейнерите..."
docker compose up --build -d

echo "Изчакване контейнерите да тръгнат..."
sleep 15

echo "Изпълнение на миграциите..."
./migrations/setup.sh

echo "Сървърът трябва да е достъпен на http://localhost:8000"
