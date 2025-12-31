#!/usr/bin/env bash
set -o errexit

composer install --no-dev --optimize-autoloader --no-scripts
chmod -R 777 var/cache var/log
php bin/console cache:clear --env=prod --no-warmup --no-debug
php bin/console cache:warmup --env=prod --no-debug
