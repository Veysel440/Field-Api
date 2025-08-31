FROM php:8.4-cli

RUN apt-get update && apt-get install -y git unzip libzip-dev libicu-dev zlib1g-dev \
 && pecl install redis \
 && docker-php-ext-enable redis \
 && docker-php-ext-install pcntl intl zip pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app

CMD ["php","artisan","horizon"]
