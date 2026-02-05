FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    nodejs \
    npm \
    supervisor

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Configure Apache Document Root to /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Enable Apache mod_rewrite and proxy modules for Reverb
RUN a2enmod rewrite proxy proxy_http proxy_wstunnel

# Copy custom Apache config
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Copy Supervisor config
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set environment variables for Reverb and Pusher
ENV REVERB_APP_ID=100001
ENV REVERB_APP_KEY=saferide_key
ENV REVERB_APP_SECRET=saferide_secret
ENV REVERB_HOST=saferidekids.onrender.com
ENV REVERB_PORT=443
ENV REVERB_SCHEME=https
ENV REVERB_SERVER_PORT=8080

ENV VITE_PUSHER_APP_KEY=saferide_key
ENV VITE_PUSHER_HOST=saferidekids.onrender.com
ENV VITE_PUSHER_PORT=443
ENV VITE_PUSHER_SCHEME=https

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node dependencies and build assets
RUN npm install && npm run build

# Expose port 80
EXPOSE 80

# Run migration and start Supervisor
CMD php artisan migrate --force && /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
