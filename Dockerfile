FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY --chown=www-data:www-data . .

RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

RUN chmod -R 775 storage bootstrap/cache

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

EXPOSE 80

CMD ["sh", "-c", "php artisan config:cache && php artisan route:cache && php artisan view:cache && apache2-foreground"]