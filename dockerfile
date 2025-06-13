FROM php:8.0-apache

# Habilita el módulo rewrite de Apache
RUN a2enmod rewrite

# Configura el directorio de trabajo para Apache
WORKDIR /var/www/html

# Copia tu código al contenedor
COPY . /var/www/html/

# Asegura que Apache reconozca index.php como archivo de índice
RUN echo "DirectoryIndex index.php index.html" >> /etc/apache2/mods-enabled/dir.conf

# Exponer el puerto 80
EXPOSE 80

# Inicia Apache en primer plano
CMD ["apache2ctl", "-D", "FOREGROUND"]
