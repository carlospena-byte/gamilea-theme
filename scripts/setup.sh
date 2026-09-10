#!/bin/sh
set -eu
cd "$(dirname "$0")/.."
if [ ! -f .env ]; then
  umask 077
  cp .env.example .env
  for key in WP_ADMIN_PASSWORD DB_PASSWORD DB_ROOT_PASSWORD; do
    secret=$(openssl rand -hex 24)
    sed "s/^${key}=.*/${key}=${secret}/" .env > .env.tmp
    mv .env.tmp .env
  done
fi
docker compose up -d --wait db wordpress
docker compose run --rm cli sh /usr/local/bin/install-shop
printf '\nGA·MI·LEA instalada. URL y credenciales disponibles en .env\n'
