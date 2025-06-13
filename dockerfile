# Usa la imagen base de PHP con Apache
FROM php:8.0-apache

# Habilita el módulo rewrite de Apache
RUN a2enmod rewrite

# Copia tus archivos del proyecto al contenedor
COPY . /var/www/html/

# Configuración para manejar index.php correctamente
RUN echo "DirectoryIndex index.php" >> /etc/apache2/mods-enabled/dir.conf

# Exponer el puerto
EXPOSE 80
