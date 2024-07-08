#!/bin/bash
rm -rf /app/.env

touch /app/.env
echo APP_NAME=Laravel >> /app/.env
echo APP_ENV=local >> /app/.env
echo APP_KEY=base64:IaJeDtrfp/ZXw4jtsUdUE+fo9nr8zMhFSiDiV4n5dZ4= >> /app/.env
echo APP_DEBUG=true >> /app/.env
echo APP_URL=https://api.unityunsrat.dev >> /app/.env

echo LOG_CHANNEL=stack >> /app/.env
echo LOG_DEPRECATIONS_CHANNEL=null >> /app/.env
echo LOG_LEVEL=debug >> /app/.env

echo DB_CONNECTION=mysql >> /app/.env
echo DB_HOST=backend-db >> /app/.env
echo DB_PORT=3306 >> /app/.env
echo DB_DATABASE=unity-db >> /app/.env
echo DB_USERNAME=root >> /app/.env
echo DB_PASSWORD= >> /app/.env

echo BROADCAST_DRIVER=log >> /app/.env
echo CACHE_DRIVER=file >> /app/.env
echo FILESYSTEM_DISK=local >> /app/.env
echo QUEUE_CONNECTION=sync >> /app/.env
echo SESSION_DRIVER=file >> /app/.env
echo SESSION_LIFETIME=120 >> /app/.env

echo MEMCACHED_HOST=127.0.0.1 >> /app/.env

echo REDIS_HOST=127.0.0.1 >> /app/.env
echo REDIS_PASSWORD=null >> /app/.env
echo REDIS_PORT=6379 >> /app/.env

echo MAIL_MAILER=smtp >> /app/.env
echo MAIL_HOST=mailpit >> /app/.env
echo MAIL_PORT=1025 >> /app/.env
echo MAIL_USERNAME=null >> /app/.env
echo MAIL_PASSWORD=null >> /app/.env
echo MAIL_ENCRYPTION=null >> /app/.env
echo MAIL_FROM_ADDRESS="hello@example.com" >> /app/.env
echo MAIL_FROM_NAME="${APP_NAME}" >> /app/.env

echo AWS_ACCESS_KEY_ID= >> /app/.env
echo AWS_SECRET_ACCESS_KEY= >> /app/.env
echo AWS_DEFAULT_REGION=us-east-1 >> /app/.env
echo AWS_BUCKET= >> /app/.env
echo AWS_USE_PATH_STYLE_ENDPOINT=false >> /app/.env

echo PUSHER_APP_ID= >> /app/.env
echo PUSHER_APP_KEY= >> /app/.env
echo PUSHER_APP_SECRET= >> /app/.env
echo PUSHER_HOST= >> /app/.env
echo PUSHER_PORT=443 >> /app/.env
echo PUSHER_SCHEME=https >> /app/.env
echo PUSHER_APP_CLUSTER=mt1 >> /app/.env

echo VITE_APP_NAME="${APP_NAME}" >> /app/.env
echo VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}" >> /app/.env
echo VITE_PUSHER_HOST="${PUSHER_HOST}" >> /app/.env
echo VITE_PUSHER_PORT="${PUSHER_PORT}" >> /app/.env
echo VITE_PUSHER_SCHEME="${PUSHER_SCHEME}" >> /app/.env
echo VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}" >> /app/.env

echo JWT_PUBLIC_KEY=public.pem >> /app/.env
echo JWT_PRIVATE_KEY=private.pem >> /app/.env

echo GOOGLE_CLOUD_PROJECT_ID=central-lock-395716 >> /app/.env
echo GOOGLE_APPLICATION_CREDENTIALS=keyfile.json >> /app/.env
echo GOOGLE_CLOUD_STORAGE_BUCKET=rf_bucket1 >> /app/.env

composer install --no-interaction --no-scripts
echo Starting server 🚀

php artisan key:generate
php artisan migrate
php artisan db:seed

php-fpm -D -R
nginx -g 'daemon off; error_log /dev/stdout info;'

#rr serve