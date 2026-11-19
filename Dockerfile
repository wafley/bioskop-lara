FROM dunglas/frankenphp:1-php8.2

# Install extensions needed by Laravel, PostgreSQL, and dragon-code/support
RUN install-php-extensions \
    pdo_pgsql \
    pgsql \
    bcmath \
    pcntl \
    zip \
    intl \
    opcache

# Copy composer from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy all project files
COPY . .

# Install dependencies (without dev packages)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Prepare directories and execution permissions
RUN chmod +x start.sh && \
    mkdir -p storage/framework/sessions \
             storage/framework/views \
             storage/framework/cache/data \
             storage/logs \
             bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

ENV PORT=8000
EXPOSE 8000

CMD ["sh", "start.sh"]
