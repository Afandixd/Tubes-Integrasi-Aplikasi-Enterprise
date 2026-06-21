FROM php:8.4-fpm


# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Working directory
WORKDIR /var/www

# Copy source code
COPY . .

# Install Laravel dependencies
RUN composer install --ignore-platform-reqs

# Expose port
EXPOSE 8003

# Run Laravel
CMD php artisan serve --host=0.0.0.0 --port=8003
