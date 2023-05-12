FROM php:8.1.18-fpm

RUN apt-get update
RUN curl -sS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/local/bin --filename=composer
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash -

RUN apt-get install nodejs -y
RUN apt-get install openssl zip unzip git -y
RUN apt-get install libzip-dev libonig-dev libicu-dev pkg-config libssl-dev -y
RUN pecl install mongodb
RUN docker-php-ext-enable mongodb

WORKDIR /app
COPY . .
RUN composer install
RUN npm install
RUN npm run build
CMD php artisan serve --host=0.0.0.0
