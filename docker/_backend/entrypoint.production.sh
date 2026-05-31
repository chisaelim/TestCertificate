#!/bin/bash
set -e

composer install
php artisan key:generate --force
php artisan migrate --force
php artisan storage:link
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
exec supervisord -c /etc/supervisor/conf.d/supervisord.production.conf
