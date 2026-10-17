# syntax=docker/dockerfile:1

# ---- PHP dependencies ------------------------------------------------------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
# Extensions are checked in the runtime image, not in this build stage.
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --no-dev --optimize --no-scripts

# ---- Front-end assets ------------------------------------------------------
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts --no-audit --no-fund
COPY . .
RUN npm run build

# ---- Runtime: FrankenPHP (Caddy + PHP) -------------------------------------
FROM dunglas/frankenphp:php8.4

ARG USER=secopsforge
# Fixed IDs so the host can give the articles folder to this user (DEPLOY.md).
ARG UID=1000
ARG GID=1000

# pdo_sqlite and opcache ship with the base image.
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && printf 'expose_php=Off\nopcache.validate_timestamps=0\n' > "$PHP_INI_DIR/conf.d/zz-secopsforge.ini"

WORKDIR /app
COPY --from=vendor /app /app
COPY --from=assets /app/public/build /app/public/build
COPY docker/Caddyfile /etc/frankenphp/Caddyfile
COPY docker/entrypoint.sh /usr/local/bin/entrypoint

# Run as an unprivileged user; the capability still lets Caddy bind 80/443.
RUN chmod +x /usr/local/bin/entrypoint \
    && rm -rf bootstrap/cache/*.php tests node_modules \
    && php artisan package:discover --ansi \
    && groupadd --gid ${GID} ${USER} \
    && useradd --uid ${UID} --gid ${GID} --create-home ${USER} \
    && setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp \
    && mkdir -p storage database/data content \
    && chown -R ${USER}:${USER} /app/storage /app/bootstrap/cache /app/database/data /app/content /config/caddy /data/caddy

USER ${USER}

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/app/database/data/database.sqlite \
    NOTES_PATH=/app/content

EXPOSE 80 443 443/udp
VOLUME ["/app/database/data", "/app/storage"]

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD curl -fsS http://127.0.0.1:2019/metrics >/dev/null || exit 1

ENTRYPOINT ["entrypoint"]
CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]
