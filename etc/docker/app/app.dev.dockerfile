# Start from this image
FROM php:7.1-apache

# Author of this image
MAINTAINER Jan Czernin <jan.czernin@autodevelo.cz>

# Copy apache config files
ADD ./.docker/app/config/000-default.conf /etc/apache2/sites-available/000-default.conf

# Copy custom apache configs
COPY ./.docker/app/config/docker-php-ext-date_timezone.ini /usr/local/etc/php/conf.d/docker-php-ext-date_timezone.ini
COPY ./.docker/app/config/docker-php-ext-memory_limit.ini /usr/local/etc/php/conf.d/docker-php-ext-memory_limit.ini

# Copy all files to www folder
COPY . /var/www/html/

# Remove and create dir for temporary files
RUN rm -rf /var/www/html/var
USER www-data
RUN mkdir /var/www/html/var

# Base packages
USER root
RUN apt-get update
RUN apt-get install -y zlib1g-dev libicu-dev g++ libpng-dev apt-transport-https git

# PHP extension
RUN docker-php-ext-install mbstring
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl
RUN docker-php-ext-install zip
RUN docker-php-ext-install gd
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install opcache
RUN docker-php-ext-install exif
RUN pecl install apcu
RUN echo "extension=apcu.so" > /usr/local/etc/php/conf.d/apcu.ini

# Enable apache mod_rewrite module
RUN a2enmod rewrite

# Enable apache mod_headers module
RUN a2enmod headers

# Enable apache deflate module
RUN a2enmod deflate

# Install composer and use it as "composer install"
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install yarn
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
RUN curl -sL https://deb.nodesource.com/setup_8.x | bash -
RUN apt-get install -y nodejs
RUN apt-get install yarn

# Compsoer install
RUN composer install

# Fix privileges
RUN chown -R www-data /var/www/html/var/*

# Yarn assets
RUN yarn install && yarn run gulp

# Restart server for apply changes
RUN service apache2 restart
