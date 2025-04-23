FROM php:8.2-apache

# Por favor herdar variáveis de ambiente vindas do Makefile, agradeço
ENV LIMBOOLE_PATH=/var/www/html/src/sat/limboole1.2/limboole

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    bash \
    gcc \
    make \
    cmake \
    git \
    && rm -rf /var/lib/apt/lists/*

COPY . .

RUN make build

RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    a2enmod rewrite && \
    echo "display_errors = On" >> /usr/local/etc/php/conf.d/errors.ini

RUN echo "RewriteEngine On" > .htaccess && \
    echo "RewriteCond %{REQUEST_FILENAME} !-f" >> .htaccess && \
    echo "RewriteRule ^ index.php [QSA,L]" >> .htaccess

RUN chmod +x /var/www/html/src/sat/limboole1.2/limboole

RUN echo "SetEnv LIMBOOLE_PATH ${LIMBOOLE_PATH}" >> /etc/apache2/conf-available/environment.conf && \
    a2enconf environment

EXPOSE 80
CMD ["apache2-foreground"]
