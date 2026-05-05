FROM thecodingmachine/php:8.4-v4-apache

# Salin semua file proyek ke dalam container
COPY --chown=docker:docker . .

# Install dependencies composer dengan flag tambahan untuk keamanan versi
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Set environment variable untuk Laravel
ENV APACHE_DOCUMENT_ROOT=public
ENV APP_ENV=production
ENV APP_DEBUG=false

# Jalankan optimasi Laravel
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Expose port 80
EXPOSE 80