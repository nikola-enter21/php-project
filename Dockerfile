FROM php:8.4-cli

# Системни зависимости
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    zip \
    mariadb-client \
    default-libmysqlclient-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo pdo_mysql zip gd && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Инсталиране на Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Работна директория
WORKDIR /var/www/html

# Копиране на проектните файлове
COPY . .

# Нямам идея тва за какво го иска ама без него не тръгва
RUN git config --global --add safe.directory /var/www/html

# Инсталиране на PHP зависимости
RUN composer install && composer dump-autoload

# Стартиране на вградения PHP сървър
CMD ["php", "-S", "0.0.0.0:8000"]