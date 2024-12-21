# Use the latest PHP image with Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install dependencies and Composer
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install required PHP extensions (mysqli, pdo, pdo_mysql) and GD
RUN apt-get update && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install mysqli pdo pdo_mysql gd && \
    pecl install mongodb && \
    docker-php-ext-enable mongodb && \
    a2enmod rewrite

# Copy your project files
COPY . /var/www/html/

# Install PHP dependencies via Composer (this will install phpdotenv)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Expose Apache's default port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
