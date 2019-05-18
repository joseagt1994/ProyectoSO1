FROM phpdockerio/php71-cli
MAINTAINER Diego Gullo <diego_gullo@bizmate.biz>

RUN apt-get update && apt-get install -y  php7.1-bcmath
#\
#	&& docker-php-ext-install curl \
#	&& apt-get clean \
#    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

#RUN apk update && apk add -f libcurl \
#	&& docker-php-ext-install curl bcmath

# RUN docker-php-ext-install bcmath

RUN apt-get install -y php7.1-dev openssl libssl-dev libcurl4-openssl-dev
RUN pecl install -f mongodb 
RUN echo "extension=mongodb.so" >> /etc/php/7.1/cli/php.ini 
RUN pecl install -f redis 
RUN echo "extension=redis.so" >> /etc/php/7.1/cli/php.ini

COPY ./docker/php_receiver/bcmath.ini /etc/php/7.1/cli/conf.d/20-bcmath.ini
