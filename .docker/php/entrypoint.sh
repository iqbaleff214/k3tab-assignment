#!/bin/bash

until pg_isready -h db -U root -d k3tab2025 --quiet; do
  echo "Waiting for database connection..."
  sleep 5
done

if [ ! -f /var/www/.migrated ]; then
  php artisan migrate:fresh --force --seed
  touch /var/www/.migrated
  php artisan storage:link
fi

php artisan optimize
php artisan config:cache

exec "$@"
