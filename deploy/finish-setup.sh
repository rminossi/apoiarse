#!/bin/bash
set -euo pipefail
APP_DIR="/var/www/apoiarse"
DOMAIN="apoiarse.ramitec.com.br"
cd "${APP_DIR}"

DB_PASS="$(grep '^DB_PASSWORD=' .env | cut -d= -f2-)"
ADMIN_PASS="$(grep '^ADMIN_PASSWORD=' .env | cut -d= -f2-)"

mysql -e "ALTER USER 'apoiarse'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "FLUSH PRIVILEGES;"

php artisan migrate --force
php artisan db:seed --force
php artisan storage:link --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

cp "${APP_DIR}/deploy/nginx/apoiarse.conf" /etc/nginx/sites-available/apoiarse
ln -sf /etc/nginx/sites-available/apoiarse /etc/nginx/sites-enabled/apoiarse
nginx -t
systemctl reload nginx

cp "${APP_DIR}/deploy/supervisor/apoiarse-worker.conf" /etc/supervisor/conf.d/apoiarse-worker.conf
supervisorctl reread
supervisorctl update
supervisorctl start apoiarse-worker:* 2>/dev/null || supervisorctl restart apoiarse-worker:*

cat > /root/apoiarse-credentials.txt <<EOF
Apoiar-se — credenciais de produção
Gerado em: $(date -Iseconds)

URL: https://${DOMAIN} (HTTP ativo; HTTPS após Certbot)
Diretório: ${APP_DIR}

MySQL:
  database: apoiarse
  username: apoiarse
  password: ${DB_PASS}

Admin inicial:
  email: admin@${DOMAIN}
  password: ${ADMIN_PASS}

Pendente no .env:
  ASAAS_API_KEY, ASAAS_WEBHOOK_TOKEN, MP_ACCESS_TOKEN, MAIL_*
EOF
chmod 600 /root/apoiarse-credentials.txt

if certbot --nginx -d "${DOMAIN}" --non-interactive --agree-tos --register-unsafely-without-email --redirect; then
  echo "CERTBOT_OK"
else
  echo "CERTBOT_FAILED - configure DNS A ${DOMAIN} -> 177.153.51.59 e rode certbot"
fi

echo "FINISH_OK"
