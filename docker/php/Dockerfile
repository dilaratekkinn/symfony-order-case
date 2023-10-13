FROM php:7.3.33-fpm-alpine3.14

#intl için icu-dev gereklidir
#libzip-dev zip/unzip için gerekli C kütüphaneleridir.
#opcache önceden derlenmiş olan php betiğini cacheleyip tekrar çağrıldığında hızlı cevap vermesini sağlayan teknolojidir.

# MYSQL için gerekli bağımlıklar ve eklentiler Not: mysqli gerekli olmayabilir.
#mysqli \
#pdo \
#pdo_mysql \
#&& docker-php-ext-enable \
#pdo_mysql

# Postgresql için bağımlıklar ve eklentiler
#apk add postgresql-libs postgresql-dev \
#&& docker-php-ext-install pgsql pdo_pgsql \
#&& apk del postgresql-dev

#Composer Yüklemek için
#ÖNERİLEN:
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer
# VEYA ayrı bir RUN olarak çalışması gerekmektedir.
RUN curl -s https://getcomposer.org/installer | php

RUN apk update \
    && apk add --no-cache \
    bash \
    libzip-dev \
    zip \
    unzip \
    icu-dev \
    postgresql-libs \
    postgresql-dev \
    && docker-php-ext-configure \
    intl \
    && docker-php-ext-install \
    intl \
    opcache \
    mysqli \
    pdo \
    pdo_mysql \
    && docker-php-ext-enable \
    pdo_mysql


# docker sıfırdan kaldırılırken çalışacak komutlar,bdf6adb440be docker ps ile kontrol et. docker exec -it
# docker exec -it bdf6adb440be /usr/local/bin/php /var/www/html/bin/console make:migration
# docker exec -it bdf6adb440be /usr/local/bin/php /var/www/html/bin/console doctrine:migrations:migrate
# docker exec -it bdf6adb440be /usr/local/bin/php /var/www/html/bin/console doctrine:fixtures:load