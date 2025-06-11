FROM php:8.2-fpm

# Install system tools and clear cache
RUN rm -rf /var/lib/apt/lists/* && \
    apt-get update --allow-releaseinfo-change && \
    apt-get install -y --no-install-recommends \
      build-essential \
      libpng-dev libjpeg62-turbo-dev libonig-dev libxml2-dev \
      zip unzip curl git libzip-dev mysql-client sqlite3 && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo_sqlite mbstring bcmath gd zip

WORKDIR /var/www
COPY . .
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader && \
    chown -R www-data:www-data /var/www

EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
