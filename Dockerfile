# syntax=docker/dockerfile:1
FROM harbor2.unpad.dev/images/php:8.4@sha256:98856b64569d561af5748b81dceb4d04642af83743a488380f646ffd2094cc2e AS base

ENV COMPOSER_HOME=/tmp/composer

USER root

COPY composer.json composer.lock ./
RUN --mount=type=cache,id=composer,target=/tmp/composer-cache,uid=33,gid=33 \
    composer config cache-dir /tmp/composer-cache && \
    composer install --no-scripts --no-dev --prefer-dist

COPY . .

RUN laravel-setup-permissions

USER www-data
