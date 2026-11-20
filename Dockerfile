# syntax=docker/dockerfile:1
FROM harbor2.unpad.dev/images/php:8.4@sha256:99a818babc76f5ba18a6297e1f5f11cf055fd810e4f5b4759454ce4fcdcde35e AS base

ENV COMPOSER_HOME=/tmp/composer

USER root

COPY composer.json composer.lock ./
RUN --mount=type=cache,id=composer,target=/tmp/composer-cache,uid=33,gid=33 \
    composer config cache-dir /tmp/composer-cache && \
    composer install --no-scripts --no-dev --prefer-dist

COPY . .

RUN laravel-setup-permissions

USER www-data
