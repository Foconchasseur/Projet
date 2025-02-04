#!/usr/bin/env bash

CURRENT_DIRECTORY=$(basename "$PWD")

echo "Stop docker stack"
docker-compose down
echo "Remove database volume"
docker volume rm "${CURRENT_DIRECTORY,,}_database"
echo "Rebuild images"
docker-compose build --no-cache
echo "Done !"
exit 0