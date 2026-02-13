#!/bin/bash
set -e

echo "🚀 Deployment started..."

# 1. Run Migrations
echo "📦 Running database migrations..."
php artisan migrate --force

# 2. Cache Configuration (Optional but recommended for production)
# echo "⚙️ Caching configuration..."
# php artisan config:cache
# php artisan route:cache
# php artisan view:cache

# 3. Start Supervisor (Apache + Reverb)
echo "🔥 Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
