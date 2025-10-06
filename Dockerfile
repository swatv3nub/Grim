# Use the official PHP image with Composer
FROM php:8.3-cli

# Install system dependencies and PHP extensions
RUN apt-get update \
    && apt-get install -y git unzip libzip-dev libpng-dev libonig-dev \
    && docker-php-ext-install mbstring zip pdo pdo_mysql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy project files
COPY . /app

# Install PHP dependencies
RUN composer install --no-interaction --no-ansi --no-progress --optimize-autoloader

# Default command
CMD ["php", "grim.php", "--help"]
