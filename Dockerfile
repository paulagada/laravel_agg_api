FROM php:8.2-fpm

RUN rm -rf /var/lib/apt/lists/* && \
    apt-get update && \
    apt-get install -y --no-install-recommends \
      build-essential \
      libpng-dev libjpeg-dev libonig-dev libxml2-dev \
      zip unzip curl git libzip-dev mysql-client sqlite3 && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql pdo_sqlite mbstring bcmath gd zip

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www

# Generate Laravel app key
RUN php artisan config:clear && php artisan key:generate

# Expose port
EXPOSE 8000

# Start Laravel server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]