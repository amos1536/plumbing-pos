
#!/bin/sh

set -e

echo "Starting Laravel application..."

# Use Render's assigned port
PORT="${PORT:-10000}"

sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf

sed -i "s/*:80/*:${PORT}/" \
    /etc/apache2/sites-available/000-default.conf

# Set Laravel permissions
chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

# Cache Laravel configuration and views
php artisan config:cache
php artisan view:cache

echo "Starting Apache..."

exec apache2-foreground
