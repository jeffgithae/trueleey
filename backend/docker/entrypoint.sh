#!/bin/bash
set -e

# Write .env from environment variables injected by Docker/Coolify
cat > /var/www/html/.env <<EOF
APP_NAME=${APP_NAME:-Laravel}
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}

LOG_CHANNEL=stack
LOG_LEVEL=${LOG_LEVEL:-error}

DB_CONNECTION=mysql
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT:-5434}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

BE_DB_HOST=${BE_DB_HOST}
BE_DB_PORT=${BE_DB_PORT:-3306}
BE_DB_DATABASE=${BE_DB_DATABASE}
BE_DB_USERNAME=${BE_DB_USERNAME}
BE_DB_PASSWORD=${BE_DB_PASSWORD}
BE_JWT_SECRET=${BE_JWT_SECRET}

CACHE_DRIVER=file
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=${MAIL_MAILER:-smtp}
MAIL_HOST=${MAIL_HOST}
MAIL_PORT=${MAIL_PORT:-587}
MAIL_USERNAME=${MAIL_USERNAME}
MAIL_PASSWORD=${MAIL_PASSWORD}
MAIL_ENCRYPTION=${MAIL_ENCRYPTION:-TLS}
MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS}
MAIL_FROM_NAME="${MAIL_FROM_NAME:-Trueleey}"
EOF

# Clear config cache so Laravel picks up the new .env
php /var/www/html/artisan config:clear 2>/dev/null || true
php /var/www/html/artisan config:cache 2>/dev/null || true

# Fix permissions on storage (volume mount may reset them)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Hand off to Apache
exec apache2-foreground
