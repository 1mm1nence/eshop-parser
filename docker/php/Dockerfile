FROM php:8.4-apache

# Install system packages and PHP extensions
RUN apt-get update && apt-get install -y \
    libssl-dev \
    libsasl2-dev \
    libcurl4-openssl-dev \
    libz-dev \
    libxml2-dev \
    libonig-dev \
    unzip \
    zip \
    curl \
    git \
    libzip-dev \
    librabbitmq-dev \
    && docker-php-ext-install pdo pdo_mysql xml zip

# Install AMQP using pecl
RUN apt-get install -y libtool autoconf gcc make \
    && pecl install amqp \
    && docker-php-ext-enable amqp

# Enable Apache mod_rewrite, set default path
RUN a2enmod rewrite
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html
