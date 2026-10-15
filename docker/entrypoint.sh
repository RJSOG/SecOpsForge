#!/bin/sh
set -e

cd /app

# Runtime folders Laravel expects (storage is a volume, it may start empty).
mkdir -p storage/app storage/logs storage/framework/cache/data storage/framework/sessions storage/framework/views \
    "$(dirname "$DB_DATABASE")"
[ -f "$DB_DATABASE" ] || touch "$DB_DATABASE"

if [ -z "$APP_KEY" ]; then
    echo "APP_KEY is empty. Generate one with: docker compose run --rm app php artisan key:generate --show" >&2
    exit 1
fi

for team in redteam blueteam automation; do
    [ -d "$NOTES_PATH/$team" ] || echo "WARNING: $NOTES_PATH/$team is missing, is the articles repository mounted?" >&2
done

php artisan migrate --force --no-interaction
php artisan optimize

exec "$@"
