#!/bin/bash
# Atualização de produção — Apoiar-se (redesign UI)
set -euo pipefail
APP_DIR="/var/www/apoiarse"
cd "${APP_DIR}"

git pull origin main 2>/dev/null || true

composer install --no-dev --optimize-autoloader
npm ci
npm run build

php artisan migrate --force
php artisan db:seed --class=CategorySeeder --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

supervisorctl restart apoiarse-worker:* 2>/dev/null || true
systemctl reload php8.4-fpm
systemctl reload nginx

echo "DEPLOY_OK"
