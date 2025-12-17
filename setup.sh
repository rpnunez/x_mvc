#!/bin/bash
set -e

# Define a variable for the container name
APP_CONTAINER_NAME="xmvc-app"

# 1. Install necessary VM tools
echo "Updating VM packages..."
sudo apt-get update && sudo apt-get install -y php-cli php-zip unzip docker-compose-plugin

# 2. Start the Docker containers
echo "Building and starting containers..."
docker compose up -d --build

# 3. Health Check Loop for the application container
echo "Waiting for $APP_CONTAINER_NAME container to be ready..."
MAX_RETRIES=30
COUNT=0

while [ "$(docker inspect -f '{{.State.Running}}' $APP_CONTAINER_NAME 2>/dev/null)" != "true" ]; do
    if [ $COUNT -ge $MAX_RETRIES ]; then
        echo "Error: $APP_CONTAINER_NAME container failed to start within 60 seconds."
        exit 1
    fi

    echo "Waiting for $APP_CONTAINER_NAME service... ($COUNT/$MAX_RETRIES)"
    sleep 2
    ((COUNT++))
done

echo "Container $APP_CONTAINER_NAME is running!"

echo "Setup complete!"