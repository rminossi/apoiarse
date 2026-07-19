#!/bin/bash
set -euo pipefail
APP_DIR="/var/www/apoiarse"
TARBALL="/tmp/apoiarse-deploy.tar.gz"

echo "==> Backup .env"
cp "${APP_DIR}/.env" /tmp/apoiarse.env.bak

echo "==> Extract update"
tar -xzf "${TARBALL}" -C "${APP_DIR}"

echo "==> Restore .env"
cp /tmp/apoiarse.env.bak "${APP_DIR}/.env"

cd "${APP_DIR}"
rm -f vite.config.js
mkdir -p storage/framework/{cache/data,sessions,views} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

echo "==> Composer"
COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --no-interaction

echo "==> NPM build"
npm ci --no-audit --no-fund
npm run build

echo "==> Artisan"
php artisan migrate --force
php artisan db:seed --class=CategorySeeder --force
php artisan storage:link --force 2>/dev/null || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

chown -R www-data:www-data storage bootstrap/cache public/build

supervisorctl restart apoiarse-worker:* 2>/dev/null || true
systemctl reload php8.4-fpm
systemctl reload nginx

echo "==> Health check"
curl -sS -o /dev/null -w "HTTP %{http_code}\n" http://127.0.0.1/health -H "Host: apoiarse.ramitec.com.br"
curl -sS -o /dev/null -w "HTTPS %{http_code}\n" https://apoiarse.ramitec.com.br/health
echo "DEPLOY_OK"
