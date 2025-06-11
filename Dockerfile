FROM richarvey/nginx-php-fpm:3.1.6

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1
COPY . .
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader && \
    chown -R www-data:www-data /var/www

EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
