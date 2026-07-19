#!/bin/bash
set -euo pipefail

APP_DIR="/var/www/apoiarse"
DOMAIN="apoiarse.ramitec.com.br"
TARBALL="${1:-/tmp/apoiarse-deploy.tar.gz}"
CREDS_FILE="/root/apoiarse-credentials.txt"

DB_NAME="apoiarse"
DB_USER="apoiarse"
DB_PASS="$(openssl rand -hex 12)Aa1!$(openssl rand -hex 4)"
ADMIN_PASS="$(openssl rand -hex 8)Bb2@$(openssl rand -hex 4)"

echo "==> Preparando diretório ${APP_DIR}"
mkdir -p "${APP_DIR}"
rm -rf "${APP_DIR:?}"/*
tar -xzf "${TARBALL}" -C "${APP_DIR}"

echo "==> Criando banco de dados MySQL"
mysql -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "ALTER USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

echo "==> Configurando .env"
cp "${APP_DIR}/.env.example" "${APP_DIR}/.env"
sed -i "s|^APP_URL=.*|APP_URL=https://${DOMAIN}|" "${APP_DIR}/.env"
sed -i "s|^DB_DATABASE=.*|DB_DATABASE=${DB_NAME}|" "${APP_DIR}/.env"
sed -i "s|^DB_USERNAME=.*|DB_USERNAME=${DB_USER}|" "${APP_DIR}/.env"
sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASS}|" "${APP_DIR}/.env"
sed -i "s|^MAIL_FROM_ADDRESS=.*|MAIL_FROM_ADDRESS=\"contato@${DOMAIN}\"|" "${APP_DIR}/.env"
sed -i "s|^ADMIN_PASSWORD=.*|ADMIN_PASSWORD=${ADMIN_PASS}|" "${APP_DIR}/.env"
sed -i "s|^ADMIN_EMAIL=.*|ADMIN_EMAIL=admin@${DOMAIN}|" "${APP_DIR}/.env"
sed -i "s|^QUEUE_CONNECTION=.*|QUEUE_CONNECTION=redis|" "${APP_DIR}/.env"

cd "${APP_DIR}"

echo "==> Preparando storage e cache"
mkdir -p storage/framework/{cache/data,sessions,views} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

echo "==> Instalando dependências PHP"
COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Gerando APP_KEY"
php artisan key:generate --force

if [ -f package-lock.json ]; then
  echo "==> Build frontend"
  npm ci --no-audit --no-fund
  npm run build
fi

echo "==> Permissões storage"
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

echo "==> Migrations e cache"
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Nginx"
cp "${APP_DIR}/deploy/nginx/apoiarse.conf" /etc/nginx/sites-available/apoiarse
ln -sf /etc/nginx/sites-available/apoiarse /etc/nginx/sites-enabled/apoiarse
nginx -t
systemctl reload nginx

echo "==> Supervisor"
cp "${APP_DIR}/deploy/supervisor/apoiarse-worker.conf" /etc/supervisor/conf.d/apoiarse-worker.conf
supervisorctl reread
supervisorctl update
supervisorctl start apoiarse-worker:* || supervisorctl restart apoiarse-worker:*

cat > "${CREDS_FILE}" <<EOF
Apoiar-se — credenciais de produção
Gerado em: $(date -Iseconds)

URL: https://${DOMAIN}
Diretório: ${APP_DIR}

MySQL:
  database: ${DB_NAME}
  username: ${DB_USER}
  password: ${DB_PASS}

Admin inicial (seeder):
  email: admin@${DOMAIN}
  password: ${ADMIN_PASS}

Pendente configurar no .env:
  ASAAS_API_KEY
  ASAAS_WEBHOOK_TOKEN
  MP_ACCESS_TOKEN
  MAIL_* (SMTP)
EOF
chmod 600 "${CREDS_FILE}"

echo "==> Tentando Certbot (requer DNS A ${DOMAIN} -> VPS)"
if certbot --nginx -d "${DOMAIN}" --non-interactive --agree-tos --register-unsafely-without-email --redirect; then
  echo "Certbot OK"
else
  echo "Certbot falhou — configure DNS A ${DOMAIN} e rode:"
  echo "  certbot --nginx -d ${DOMAIN}"
fi

echo "==> Deploy concluído"
echo "Credenciais salvas em ${CREDS_FILE}"
