FROM devilbox/php-fpm-8.0:latest

RUN apt-get update \
	&& apt-get install -y --no-install-recommends \
        zlib1g-dev \
		#libicu52 \
        libicu-dev \
		libpng-dev \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libmcrypt4 \
		libmcrypt-dev \
		libtool \
		libwebp-dev \
		libzip-dev \
	&& docker-php-ext-install \
		intl \
		zip \
		exif \
		gd \
		pdo_mysql \
        mysqli \
		#mcrypt \
	&& docker-php-ext-enable \
		opcache \
		pdo_mysql \
        mysqli \
	&& apt-get purge -y \
		zlib1g-dev \
		libicu-dev \
		libpng-dev \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libmcrypt-dev \
	&& apt-get clean all

RUN docker-php-ext-install sysvsem
RUN docker-php-ext-enable sysvsem

RUN docker-php-ext-install pcntl
RUN docker-php-ext-enable pcntl

RUN docker-php-ext-install shmop
RUN docker-php-ext-enable shmop

RUN apt-get update && apt-get install -y libpq-dev
RUN docker-php-ext-install pdo_pgsql
RUN docker-php-ext-install pgsql

RUN apt-get update \
    && apt-get install -y \
        librabbitmq-dev \
        libssh-dev \
    && pecl install amqp \
    && docker-php-ext-enable amqp

RUN mkdir /var/php/
RUN mkdir /var/php/log

EXPOSE 9003

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet


# Add crontab file in the cron directory
ADD ./cron/crontab /etc/cron.d/parser-cron
# Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/parser-cron
# Create the log file to be able to run tail
RUN touch /var/log/cron.log
#Install Cron
RUN apt-get update
RUN apt-get -y install cron
# Run the command on container startup
CMD cron && tail -f /var/log/cron.log


WORKDIR /app