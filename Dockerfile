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

# Работна директория
WORKDIR /var/www/html

# Копиране на проектните файлове
COPY . .

# Инсталиране на TCPDF (вътре в /var/www/html)
RUN curl -L -o tcpdf.zip https://github.com/tecnickcom/tcpdf/archive/refs/heads/main.zip && \
    unzip tcpdf.zip -d tcpdf-temp && \
    mv tcpdf-temp/* tcpdf && \
    rm -rf tcpdf.zip tcpdf-temp

# Стартиране на вградения PHP сървър
CMD ["php", "-S", "0.0.0.0:8000"]