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

RUN cd src/sat/limmat-1.3 && \
    CC=gcc ./configure && \
    make clean && \
    make && \
    chmod +x limmat

RUN cd src/sat/limboole-0.2 && \
    make clean && \
    make && \
    chmod +x ../limboole  # Change to chmod the moved binary

RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    a2enmod rewrite && \
    echo "display_errors = On" >> /usr/local/etc/php/conf.d/errors.ini

RUN echo "RewriteEngine On" > .htaccess && \
    echo "RewriteCond %{REQUEST_FILENAME} !-f" >> .htaccess && \
    echo "RewriteRule ^ index.php [QSA,L]" >> .htaccess

EXPOSE 80
CMD ["apache2-foreground"]
