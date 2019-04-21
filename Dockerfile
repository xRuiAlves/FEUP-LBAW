FROM ubuntu:18.04

# Install dependencies
RUN apt-get update
RUN apt-get install -y --no-install-recommends libpq-dev vim nginx php7.2-fpm php7.2-mbstring php7.2-xml php7.2-pgsql

# Copy project code and install project dependencies
COPY . /var/www/
RUN chown -R www-data:www-data /var/www/

# Copy project configurations
COPY ./etc/php/php.ini /usr/local/etc/php/conf.d/php.ini
COPY ./etc/nginx/default.conf /etc/nginx/sites-enabled/default
COPY .env_production /var/www/.env
COPY docker_run.sh /docker_run.sh
RUN mkdir /var/run/php

WORKDIR /var/www
RUN pwd
RUN php artisan config:clear
RUN php artisan cache:clear
RUN php artisan config:cache

# Start command
CMD sh /docker_run.sh
