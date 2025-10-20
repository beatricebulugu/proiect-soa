FROM php:8.2-apache

# instalăm extensii PHP (inclusiv MySQL)
RUN docker-php-ext-install pdo pdo_mysql mysqli && a2enmod rewrite

# permitem .htaccess (opțional)
RUN sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# copiem codul aplicației
COPY . /var/www/html/

# setăm permisiunile
RUN chown -R www-data:www-data /var/www/html
