#!/bin/bash
set -e

# Clear and warmup cache at runtime (when env vars are available)
php bin/console cache:clear --env=prod --no-warmup --no-debug || true
php bin/console cache:warmup --env=prod --no-debug || true

# Start Apache
exec apache2-foreground
