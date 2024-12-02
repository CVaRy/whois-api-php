FROM php:8.1.0-apache

# Gerekli bağımlılıkları yükleyin
RUN apt-get update \
    && apt-get install -y nano zip unzip git libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install mysqli \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer'ı yükleyin
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# Çalışma dizinini belirleyin
WORKDIR /var/www/html

# Projenizin dosyalarını Docker konteynerine kopyalayın
COPY . /var/www/html

# Gerekli izinleri ayarlayın
RUN chown -R www-data:www-data /var/www/html \
    && composer install --no-dev --no-interaction

# Apache yapılandırmasını basit tutarak varsayılan siteyi aktif edin
RUN a2dissite 000-default.conf && a2enmod rewrite && a2ensite 000-default.conf \
    && service apache2 reload || true

# Apache'yi başlatın
EXPOSE 80
CMD ["apache2-foreground"]
