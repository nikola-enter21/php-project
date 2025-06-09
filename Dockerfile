FROM php:8.4-cli

# Системни зависимости
RUN apt-get update && apt-get install -y \
    # Използва се от Composer за разархивиране на ZIP архиви на PHP пакети
    unzip \
    # Необходимо е за Composer, за да изтегля пакети от Git хранилища
    git \
    # Заглавни файлове за PostgreSQL – нужни за компилиране на разширението pdo_pgsql
    libpq-dev \
    # Нужни за компилиране на PHP zip разширението
    libzip-dev \
    # Позволява създаване на ZIP архиви чрез PHP или скриптове
    zip && \
    # Инсталираме PHP разширенията за PDO и PostgreSQL
    docker-php-ext-install pdo pdo_pgsql

# Инсталиране на Composer (мениджър на зависимости за PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# Копираме последната версия на Composer от официалния Docker образ

# Работна директория
WORKDIR /var/www/html
# Настройваме текущата директория вътре в контейнера

# Копиране на проектните файлове
COPY . .
# Копираме всички файлове от текущата директория на хоста в контейнера

# Инсталиране на PHP зависимости
RUN composer install && composer dump-autoload
# composer install – Инсталира зависимостите, описани в composer.json
# composer dump-autoload – Генерира файл за автоматично зареждане на класове (autoload)

# Стартиране на вградения PHP сървър
CMD ["php", "-S", "0.0.0.0:8000"]
