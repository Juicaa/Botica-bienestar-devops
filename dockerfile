# Usa una imagen base de PHP con Apache
FROM php:8.0-apache

# Habilita los módulos de Apache necesarios
RUN a2enmod rewrite

# Establece el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Copia los archivos del proyecto desde el host al contenedor
COPY . .

# Instala dependencias de PHP si es necesario (por ejemplo, si usas Composer)
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Si tienes un archivo composer.json, puedes instalar las dependencias de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install

# Exponer el puerto 80 para que Apache escuche en el puerto 80
EXPOSE 80

# Comando para ejecutar Apache en primer plano
CMD ["apache2-foreground"]
