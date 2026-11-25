FROM php:8.3-cli
RUN apt-get update && apt-get install -y git unzip libzip-dev libonig-dev && docker-php-ext-install pdo_mysql mbstring zip bcmath && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction && mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache && chmod -R 777 storage bootstrap/cache
EXPOSE 8080
CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --force && php artisan config:cache && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]
