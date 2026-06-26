FROM dunglas/frankenphp:latest

RUN install-php-extensions mysqli curl gd

COPY Caddyfile /etc/frankenphp/Caddyfile

WORKDIR /app
