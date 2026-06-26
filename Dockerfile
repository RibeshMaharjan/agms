FROM dunglas/frankenphp:latest

RUN install-php-extensions mysqli curl gd

COPY caddyfile.d/ /etc/frankenphp/Caddyfile.d/

WORKDIR /app
