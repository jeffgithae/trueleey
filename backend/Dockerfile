FROM php:8.0-apache

# Debian bullseye is past end of life and its security mirror now lists packages it
# no longer serves, which breaks apt-get install. By default, install from the main
# bullseye repo instead; pass APT_SKIP_SECURITY=0 to keep the security mirror.
ARG APT_SKIP_SECURITY=1
RUN if [ "$APT_SKIP_SECURITY" = "1" ]; then sed -i '/debian-security/d' /etc/apt/sources.list; fi

# Install system dependencies and PHP extensions in one layer
# Using pre-compiled approach to keep build time minimal
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Remove any local .env so the entrypoint always writes a fresh one from env vars
RUN rm -f .env

# Install PHP dependencies (no dev, optimised autoloader)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions
# public/assets/product-images receives uploads from ProductsController (new/edit product)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/assets/product-images \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/assets/product-images

# Apache config
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Entrypoint: writes .env from env vars, caches config, starts Apache
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
