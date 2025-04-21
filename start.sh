#! /bin/bash

cp .env.example .env
touch database/database.sqlite
composer install --ignore-platform-reqs

while true; do
    echo -n "Please enter your API Token: "
    read api_token

    # Trim whitespace from beginning and end of the token
    api_token=$(echo "$api_token" | xargs)

    if [ -z "$api_token" ]; then
        echo "Error: API Token cannot be empty. Please try again."
    else
        break
    fi
done

# Replace the API_TOKEN in .env file
if [[ "$OSTYPE" == "darwin"* ]]; then
    # macOS
    sed -i '' "s|^API_TOKEN=.*|API_TOKEN=$api_token|" .env
else
    # Linux and others
    sed -i "s|^API_TOKEN=.*|API_TOKEN=$api_token|" .env
fi

# Print confirmation
echo "API Token has been set to: $api_token"
echo "Checking .env file to confirm..."
grep "API_TOKEN" .env

docker compose up --build --remove-orphans -d
docker compose exec laravel.test composer install
docker compose exec laravel.test php artisan migrate:fresh
docker compose exec laravel.test php artisan key:generate
docker compose exec laravel.test php artisan config:clear
docker compose exec laravel.test php artisan app:sync-api-database
docker compose exec laravel.test npm install
docker compose exec laravel.test npm run dev
