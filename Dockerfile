# Use the official PHP 7.4 image as the base image
FROM php:7.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    default-mysql-client

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/html

# Copy the composer.json and composer.lock files
#
COPY composer.json composer.lock ./

# Install project dependencies
RUN composer install --prefer-dist --no-scripts --no-autoloader

# Copy the rest of the application code
COPY . .

# Generate optimized autoload files
RUN composer dump-autoload --optimize

# Set file permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Set up environment variables
ENV APP_ENV=production
ENV APP_KEY=Tzs/I6Ro9/A4U0KTCpgXKVTmjdWa7WPX/3hT7y8OPYY=
ENV APP_DEBUG=false

# Generate application key
RUN php artisan key:generate

# Run migrations and seeders (if needed)
# RUN php artisan migrate --seed

# Expose port 9000 (or any other port you've configured in your PHP-FPM pool configuration)
EXPOSE 9000

# Start PHP-FPM server
CMD ["php-fpm"]