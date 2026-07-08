# Γ.Α.Σ. Μαχητές Μεσολογγίου — production image
# PHP 8.2 + Apache with the extensions used by the app.

FROM php:8.2-apache

# System dependencies + PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
      libicu-dev \
      libzip-dev \
      libpng-dev \
      libjpeg-dev \
      libwebp-dev \
      zip \
      unzip \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql mysqli intl mbstring zip gd \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# Recommended production PHP config
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
 && sed -ri 's|^;?upload_max_filesize = .*|upload_max_filesize = 10M|' "$PHP_INI_DIR/php.ini" \
 && sed -ri 's|^;?post_max_size = .*|post_max_size = 12M|' "$PHP_INI_DIR/php.ini" \
 && sed -ri 's|^;?expose_php = .*|expose_php = Off|' "$PHP_INI_DIR/php.ini"

# App files
WORKDIR /var/www/html
COPY . /var/www/html/

# Persistent uploads: make sure the tree exists + permissions
RUN mkdir -p \
      /var/www/html/uploads/gallery \
      /var/www/html/uploads/posts \
      /var/www/html/uploads/athletes \
      /var/www/html/uploads/trophies \
      /var/www/html/uploads/programs \
 && chown -R www-data:www-data /var/www/html \
 && find /var/www/html -type d -exec chmod 755 {} \; \
 && find /var/www/html -type f -exec chmod 644 {} \; \
 && chmod -R 775 /var/www/html/uploads

# Apache: use the shipped .htaccess (AllowOverride All on default vhost)
RUN sed -ri 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf

EXPOSE 80

# Healthcheck — Apache serving the front page
HEALTHCHECK --interval=30s --timeout=5s --start-period=15s --retries=3 \
  CMD php -r 'exit(@file_get_contents("http://127.0.0.1/") !== false ? 0 : 1);' \
      || exit 1
