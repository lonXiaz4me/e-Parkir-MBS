#!/bin/sh
set -e

# Render tells us which port to listen on
PORT="${PORT:-10000}"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/:80>/:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# SQLite file lives on the ephemeral disk, so it is recreated on every boot
mkdir -p database storage/framework/cache storage/framework/sessions storage/framework/views storage/logs
touch database/database.sqlite
chown -R www-data:www-data database storage bootstrap/cache
chmod -R 775 database storage bootstrap/cache

php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Fresh database + demo data on every boot
php artisan migrate --force --seed

exec apache2-foreground