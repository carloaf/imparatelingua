# Use uma imagem oficial do PHP 8.1 com FPM (FastCGI Process Manager)
FROM php:8.1-fpm

# Instala dependências do sistema e extensões PHP comuns para o Laravel
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libzip-dev \
    libxml2-dev \
    poppler-utils \
    tesseract-ocr \
    tesseract-ocr-ita \
    tesseract-ocr-por \
    imagemagick \
    ghostscript \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Limpa o cache para manter a imagem leve
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala o Composer (gerenciador de pacotes do PHP) globalmente
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho padrão dentro do container
WORKDIR /var/www

# Expõe a porta 9000, que é a porta padrão do PHP-FPM
EXPOSE 9000
