# Usamos la imagen oficial de PHP con Apache integrado
FROM php:8.2-apache

# Habilitamos el módulo rewrite de Apache (Crucial para tu Router y URLs amigables)
RUN a2enmod rewrite

# Actualizamos repositorios e instalamos dependencias básicas
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Instalamos las extensiones de PHP que usas (mysqli para tu GenericModel)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Configuramos ServerName para evitar advertencias de Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Damos permisos al usuario de Apache (www-data) sobre la carpeta de trabajo
RUN chown -R www-data:www-data /var/www/html

# Definimos el directorio de trabajo
WORKDIR /var/www/html