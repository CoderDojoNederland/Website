FROM phpdockerio/php72-cli:latest
WORKDIR "/application"

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

RUN curl -sL https://deb.nodesource.com/setup_8.x | bash

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install nodejs curl php7.2-mysql php-xdebug php7.2-bcmath php7.2-gd php-imagick php7.2-intl php-yaml \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install git
RUN apt-get update \
    && apt-get -y install git \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*


