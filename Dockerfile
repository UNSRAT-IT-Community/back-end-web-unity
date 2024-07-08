FROM php:8.3.9-fpm

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Install dependencies
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions intl gd zip sodium sockets mysqli pdo_pgsql pgsql

RUN mkdir /app
WORKDIR /app
COPY . .

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get update && apt-get install -y \
    nginx \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip && \
    rm -rf /etc/nginx/sites-enabled/default

COPY ./docker/nginx/unity.conf /etc/nginx/sites-enabled/unity.conf

RUN cp /app/start.sh /start.sh && chmod +x /start.sh

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

CMD ["/start.sh"]