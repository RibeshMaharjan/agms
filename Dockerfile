FROM dunglas/frankenphp:latest

RUN install-php-extensions mysqli curl gd

WORKDIR /app
