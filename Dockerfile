# Use the official PHP 8.3.9 FPM image
FROM php:8.3.9-fpm

# Add the PHP extension installer script
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Install PHP extensions and dependencies
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions intl gd zip sodium sockets mysqli pdo_pgsql pgsql

# Create and set the working directory
RUN mkdir /app
WORKDIR /app

# Copy the application code into the container
COPY . .

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install additional system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip && \
    rm -rf /etc/nginx/sites-enabled/default

# Copy the nginx configuration file into the container
COPY ./docker/nginx/unity.conf /etc/nginx/sites-enabled/unity.conf

# Copy the start script and make it executable
RUN cp /app/start.sh /start.sh && chmod +x /start.sh

# Rename .env.example to .env
RUN mv /app/.env.example /app/.env

# Set permissions for the storage and cache directories
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Set the default command to run the start script
CMD ["/start.sh"]

# Set permissions for the private and public keys
RUN chown -R /app/private_key.pem /app/public_key.pem