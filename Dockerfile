FROM thecodingmachine/php:8.2-v4-apache

# Salin semua file proyek ke dalam container
COPY --chown=docker:docker . .

# Install dependencies composer
RUN composer install --no-dev --optimize-autoloader

# Set environment variable untuk Laravel
ENV APACHE_DOCUMENT_ROOT public/
ENV APP_ENV production
ENV APP_DEBUG false

# Beri izin akses untuk folder storage dan cache
RUN sudo chown -R docker:docker storage bootstrap/cache