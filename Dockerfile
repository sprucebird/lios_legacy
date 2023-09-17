FROM php:7.4-fpm-alpine

RUN docker-php-ext-install bcmath pdo pdo_mysql sockets

# Setup GD extension
# Install dependencies for GD and install GD with support for jpeg, png webp and freetype
# Info about installing GD in PHP https://www.php.net/manual/en/image.installation.php
RUN apk add --no-cache \
        libjpeg-turbo-dev \
        libpng-dev \
        libwebp-dev \
        freetype-dev

RUN docker-php-ext-install exif
RUN docker-php-ext-configure exif \
            --enable-exif

# As of PHP 7.4 we don't need to add --with-png
RUN docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype
RUN docker-php-ext-install gd

RUN echo "memory_limit=-1" > /usr/local/etc/php/conf.d/memory-limit.ini

RUN php -r "copy('https://getcomposer.org/download/1.8.6/composer.phar', '/usr/local/bin/composer');"
RUN chmod +x /usr/local/bin/composer

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . /app
#RUN php -d memory_limit=-1 composer update

# RUN composer update
RUN composer install
# disabled because of Windows 404 WSL bs
# RUN composer update --ignore-platform-reqs 

# git required by some composer packages
RUN apk update && apk add git

RUN php artisan config:clear

# install composer packages
# RUN composer install Ęs

# generate php autoload files
RUN composer dump-autoload

# migrate tables to the database
RUN php artisan migrate

# Expose port 9000 and start php-fpm server
# EXPOSE 9000
# CMD ["php-fpm"]

CMD php artisan serve --host=0.0.0.0 --port=80
EXPOSE 80

# EXPOSE 80 443
