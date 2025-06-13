FROM php:8.0-apache

RUN a2enmod rewrite

# COPIA TU CÓDIGO CORRECTAMENTE
WORKDIR /var/www/html

COPY Botica/frontend/ /var/www/html/

# OTROS COMANDOS OPCIONALES
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html
