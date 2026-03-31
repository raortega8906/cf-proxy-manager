FROM serversideup/php:8.3-fpm-nginx@sha256:59c6f82b2af6c82517f3a71702411400577631694986bb8265bbf4ffba269129

USER root

RUN install-php-extensions gd

COPY --chown=www-data:www-data . /var/www/html

RUN composer install --no-dev --optimize-autoloader

RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache
