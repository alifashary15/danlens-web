FROM thecodingmachine/php:8.4-v4-apache

# Salin file proyek
COPY --chown=docker:docker . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Pastikan folder storage bisa ditulis oleh user docker (bukan root)
RUN chmod -R 775 storage bootstrap/cache

# Set environment
ENV APACHE_DOCUMENT_ROOT=public
ENV APP_ENV=production
ENV APP_DEBUG=false

# Jalankan optimasi
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

EXPOSE 80