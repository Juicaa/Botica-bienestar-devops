# Usa una imagen base de PHP con Apache
FROM php:8.0-apache

# Habilita los módulos de Apache necesarios
RUN a2enmod rewrite

# Establece el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Copia los archivos del proyecto desde el host al contenedor
COPY . .


# Exponer el puerto 80 para que Apache escuche en el puerto 80
EXPOSE 80

# Comando para ejecutar Apache en primer plano
CMD ["apache2-foreground"]
