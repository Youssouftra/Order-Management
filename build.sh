#!/usr/bin/env bash
# exit on error
set -o errexit

composer install --no-dev --optimize-autoloader
php bin/console cache:clear --env=prod --no-warmup
php bin/console cache:warmup --env=prod
