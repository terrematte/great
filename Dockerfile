FROM php:8.2-apache

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

EXPOSE 80
CMD ["apache2-foreground"]
