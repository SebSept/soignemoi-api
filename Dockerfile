# syntax=docker/dockerfile:labs
# You can redefined theses values in the compose.yml file.
ARG COMPOSER_VERSION=2.6.5
ARG PHP_VERSION=8.3.4
ARG BOX_VERSION=4.5.1
ARG BOX_CHECKSUM=c24c400c424a68041d7af146c71943bf1acc0c5abafa45297c503b832b9c6b16
ARG GIT_EMAIL="seb@local.fr"
ARG GIT_USERNAME="seb"
ARG ALPINE_VERSION=3.18
ARG PHP_CS_FIXER_VERSION=3.52.1

FROM php:${PHP_VERSION}-fpm-alpine${ALPINE_VERSION} AS php

# config system
RUN apk update \
    && apk add fish bash git

# Add php extension installer
# available extensions : https://github.com/mlocati/docker-php-extension-installer#supported-php-extensions
ADD --chmod=700 \
    https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
    /usr/local/bin/

# runtime extensions - https://symfony.com/doc/current/setup.html#technical-requirements
# already bundled : Ctype , iconv, PCRE, Session, Tokenizer, simplexml
# json, mbstring (bundled)
RUN install-php-extensions intl pdo_pgsql
# dev extensions
# To start xdebug for a interactive cli use this :
# XDEBUG_MODE=debug XDEBUG_SESSION=1 XDEBUG_CONFIG="client_host=172.17.0.1 client_port=9003" PHP_IDE_CONFIG="serverName=myrepl" php /app/hello.php
# A phpstorm server with the appropriate name is also needed ( Config : PHP > Servers )
RUN install-php-extensions xdebug

RUN adduser -D -s /usr/bin/fish -h /home/climber -u 1000 climber

# Add composer
# We may also use `install-php-extensions @composer` (not tested)
# or get the binary from the composer docker image
ARG COMPOSER_VERSION
ADD --chown=climber:climber \
    --chmod=744 \
    https://github.com/composer/composer/releases/download/${COMPOSER_VERSION}/composer.phar \
    /usr/local/bin/composer
RUN composer --version

# Add box to make phar - https://github.com/box-project/box/
ARG BOX_VERSION
ARG BOX_CHECKSUM
ADD --chown=climber:climber \
    --chmod=744 \
    --checksum=sha256:${BOX_CHECKSUM} \
    https://github.com/box-project/box/releases/download/${BOX_VERSION}/box.phar \
    /usr/local/bin/box

# Add phive - not yet available, gpg not installed
# Phive require a gpg key
# to choose the version, do not use RUN ["phive", "install phpmd --trust-gpg-keys 9093F8B32E4815AA"]
# But use RUN ["phive", "install https://github.com/infection/infection/releases/download/0.27.8/infection.phar --trust-gpg-keys C5095986493B4AA0"]
ADD --chown=climber:climber \
    --chmod=744 \
    https://github.com/phar-io/phive/releases/download/0.15.2/phive-0.15.2.phar \
    /usr/local/bin/phive


# Create app directory & vendor/bin (needed ?)
WORKDIR /app
RUN chown climber /app && mkdir -p /app/vendor/bin/

# Add psysh - https://github.com/bobthecow/psysh
RUN wget https://github.com/bobthecow/psysh/releases/download/v0.12.0/psysh-v0.12.0.tar.gz --output-document  /tmp/psysh.tar.gz \
    && tar -xvf /tmp/psysh.tar.gz -C /usr/local/bin/ \
    && chmod 500 /usr/local/bin/psysh \
    && chown climber /usr/local/bin/psysh \
    && psysh --version

# Add symfony cli
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash \
    && apk add symfony-cli \
    && symfony local:check:requirements

USER climber
# configure git (needed for symnfony cli)
RUN git config --global user.email "${GIT_EMAIL}" \
    && git config --global user.name "${GIT_USERNAME}"

# Add php-cs-fixer
ARG PHP_CS_FIXER_VERSION
ADD --chown=climber:climber \
    --chmod=744 \
    https://github.com/PHP-CS-Fixer/PHP-CS-Fixer/releases/download/v${PHP_CS_FIXER_VERSION}/php-cs-fixer.phar \
    /usr/local/bin/php-cs-fixer

# Add composer binaries to path
RUN ["fish", "-c fish_add_path /app/vendor/bin"]

# switch back to www-data user ?

