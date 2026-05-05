FROM php:8.4-apache

# 1. Install ekstensi yang dibutuhkan Laravel (Wajib ada libpq-dev untuk PostgreSQL/Supabase)
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Aktifkan Apache Rewrite Module (Penting untuk routing Laravel)
RUN a2enmod rewrite

# 3. Set Working Directory
WORKDIR /var/www/html

# 4. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Salin file proyek dan atur kepemilikan ke www-data (User standar Apache)
COPY --chown=www-data:www-data . .

# 6. Install dependencies tanpa sudo
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# 7. Atur izin folder storage agar bisa ditulis oleh web server
RUN chmod -R 775 storage bootstrap/cache

# 8. Konfigurasi Apache Document Root ke /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 9. Jalankan optimasi Laravel
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

EXPOSE 80

# Jalankan Apache di foreground
CMD ["apache2-foreground"]