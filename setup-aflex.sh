#!/usr/bin/env bash
set -euo pipefail

if [[ ! -f .env ]]; then
    cp .env.example .env
    echo "Created .env. Change the example passwords before exposing this site publicly."
fi

set -a
source .env
set +a

docker compose up -d --build
docker compose exec -T web chown -R www-data:www-data /var/www/moodledata
docker compose exec -T web mkdir -p /var/www/moodledata/lang/en_local
docker compose cp branding/lang/en_local/moodle.php web:/var/www/moodledata/lang/en_local/moodle.php
docker compose exec -T web chown -R www-data:www-data /var/www/moodledata/lang

if ! docker compose exec -T --user www-data web php admin/cli/isinstalled.php >/dev/null 2>&1; then
    docker compose exec -T --user www-data web php admin/cli/install_database.php \
        --agree-license \
        --fullname="AFLEX Learning Platform" \
        --shortname="AFLEX" \
        --adminuser="${MOODLE_ADMIN_USER:-admin}" \
        --adminpass="${MOODLE_ADMIN_PASSWORD:-ChangeMe-Now-123!}" \
        --adminemail="${MOODLE_ADMIN_EMAIL:-admin@example.com}"
fi

docker compose exec -T --user www-data web php admin/cli/cfg.php --name=theme --set=aflex
docker compose exec -T --user www-data web php admin/cli/cfg.php --name=fullname --set="AFLEX Learning Platform"
docker compose exec -T --user www-data web php admin/cli/cfg.php --name=shortname --set="AFLEX"
docker compose exec -T --user www-data web php admin/cli/purge_caches.php

echo "AFLEX is ready at ${MOODLE_URL:-http://localhost:8080}"
