# Usamos la imagen oficial de PHP 8.2 con Apache integrado
FROM php:8.2-apache

# ─── 1. MÓDULOS DE APACHE ────────────────────────────────────
# mod_rewrite: Necesario para que el .htaccess redirija todo a index.php
RUN a2enmod rewrite

# ─── 2. DEPENDENCIAS DEL SISTEMA ─────────────────────────────
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# ─── 3. EXTENSIONES PHP ──────────────────────────────────────
# PDO + pdo_mysql: Nuestra nueva capa de base de datos (reemplaza mysqli)
# mysqli: Lo mantenemos temporalmente por compatibilidad con el código viejo
RUN docker-php-ext-install pdo pdo_mysql mysqli

# ─── 4. COMPOSER ─────────────────────────────────────────────
# Instalamos Composer desde su imagen oficial (multi-stage copy)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ─── 5. DEPENDENCIAS PHP (Composer) ──────────────────────────
# Primero copiamos solo composer.json para aprovechar la cache de Docker.
# Si composer.json no cambia, Docker reutiliza la capa sin re-instalar.
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

# ─── 6. DOCUMENT ROOT ────────────────────────────────────────
# Cambiamos el DocumentRoot de Apache a /var/www/html/public
# Esto es CRÍTICO para la seguridad: solo la carpeta public/ es accesible vía web.
# Los archivos de src/, .env, vendor/ quedan FUERA del acceso web.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/apache2.conf

# ─── 7. CONFIGURACIÓN FINAL ──────────────────────────────────
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html