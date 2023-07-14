FROM php:7.4-fpm-alpine

RUN docker-php-ext-install bcmath pdo pdo_mysql sockets

RUN echo "memory_limit=-1" > /usr/local/etc/php/conf.d/memory-limit.ini

RUN php -r "copy('https://getcomposer.org/download/1.8.6/composer.phar', '/usr/local/bin/composer');"
RUN chmod +x /usr/local/bin/composer

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .
#RUN php -d memory_limit=-1 composer update
RUN composer update
RUN composer install

# Expose port 9000 and start php-fpm server
#EXPOSE 9000
#CMD ["php-fpm"]

EXPOSE 80 443
