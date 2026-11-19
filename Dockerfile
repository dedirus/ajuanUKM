# syntax=docker/dockerfile:1
FROM harbor2.unpad.dev/images/php:8.4@sha256:a741e59112f60d3d4dfa4bf136416fea9545aea9870a491ac741952877578832 AS base

ENV COMPOSER_HOME=/tmp/composer

USER root

COPY composer.json composer.lock ./
RUN --mount=type=cache,id=composer,target=/tmp/composer-cache,uid=33,gid=33 \
    composer config cache-dir /tmp/composer-cache && \
    composer install --no-scripts --no-dev --prefer-dist

COPY . .

RUN laravel-setup-permissions

USER www-data
