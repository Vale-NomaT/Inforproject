#!/bin/bash
set -e

echo "🚀 Deployment started..."

# 1. Run Migrations & Safety Checks
echo "📦 Running database migrations..."
php artisan migrate --force

echo "🏥 Checking for missing tables..."
php artisan db:ensure-tables

# 2. Cache Configuration (Optional but recommended for production)
# echo "⚙️ Caching configuration..."
# php artisan config:cache
# php artisan route:cache
# php artisan view:cache

# 2.5 Adapt Apache to Render-provided PORT if present
if [ -n "$PORT" ]; then
  echo "🌐 Configuring Apache to listen on PORT=${PORT}..."
  sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf || true
  sed -i "s/<VirtualHost \\*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf || true
fi

# 3. Start Supervisor (Apache + Reverb)
echo "🔥 Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
