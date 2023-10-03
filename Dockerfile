FROM php:8.1-fpm-buster
LABEL maintainer="renaldiyulvianda@yahoo.com"

# Install system dependencies
RUN apt-get update && apt-get install -y \
  zip \
  unzip \
  nginx \
  curl \
  git \
  build-essential \
  libssl-dev \
  zlib1g-dev \
  libpng-dev \
  libjpeg-dev \
  libfreetype6-dev \
  # zip
  libzip-dev

# OpenTelemetry
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
  install-php-extensions gd xdebug
  
RUN install-php-extensions opentelemetry

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
  mysqli \
  pdo_mysql \
  gd \
  opcache \
  zip

# Ensure PHP logs are captured by the container
ENV LOG_CHANNEL=stderr

# add config file
COPY ./docker-config/entrypoint.sh /etc/entrypoint.sh
COPY ./docker-config/nginx-default.conf /etc/nginx/conf.d/default.conf
COPY ./docker-config/nginx-default.conf /etc/nginx/sites-available/default

# Get latest Composer and run composer inst
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ADD ./ /var/www/html
RUN cd /var/www/html && composer install --no-dev

# Otel
RUN compose require open-telemetry/opentelemetry-auto-laravel

# chown
RUN chown -R www-data:www-data /var/www/html/storage

# Set working directory
WORKDIR /var/www/html

#list
RUN ls -lha

EXPOSE 80 443

ENTRYPOINT ["sh", "/etc/entrypoint.sh"] 