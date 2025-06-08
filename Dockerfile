FROM php:8.4-cli

# Системни зависимости
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libpq-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo pdo_pgsql

# Инсталирай Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Работна директория
WORKDIR /var/www/html

# Копиране на проекта
COPY . .

# Инсталиране на зависимости
RUN composer install && composer dump-autoload

# Стартиране на PHP сървъра
CMD ["php", "-S", "0.0.0.0:8000"]
