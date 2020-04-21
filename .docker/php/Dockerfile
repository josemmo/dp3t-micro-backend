FROM php:7.4-fpm-alpine

# Install dependencies
ADD https://github.com/ufoscout/docker-compose-wait/releases/download/2.7.3/wait /wait
RUN apk add --no-cache composer \
    && docker-php-ext-install mysqli \
    && chmod +x /wait

# Copy app to image and build
WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader

# Set command to run on startup
CMD /wait && php /app/install.php && php-fpm
