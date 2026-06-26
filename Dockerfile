FROM php:8.2-cli

RUN apt-get update \
    && apt-get install -y libzip-dev libsqlite3-dev unzip git \
    && docker-php-ext-install pdo_mysql pdo_sqlite zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --optimize-autoloader --no-interaction --no-progress

COPY . .

RUN composer dump-autoload --optimize \
    && if [ ! -f .env ]; then cp .env.example .env; fi \
    && touch database/database.sqlite \
    && php artisan key:generate --force \
    && php artisan migrate --force \
    && php artisan l5-swagger:generate

CMD ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"]
