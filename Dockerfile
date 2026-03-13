# Dockerfile para Supreal API - Laravel 12 + Oracle PDO
# Multi-stage build otimizado para API pura (sem frontend assets)

# ============================================
# Stage 1: Composer para dependencias PHP
# ============================================
FROM php:8.2-cli AS composer-builder

RUN apt-get update && apt-get install -y \
    libxml2-dev \
    libzip-dev \
    git \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install zip

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock* ./

RUN --mount=type=cache,target=/root/.composer \
    if [ -f composer.lock ]; then \
        composer install --no-dev --optimize-autoloader --no-scripts --no-interaction --prefer-dist \
        --ignore-platform-req=ext-oci8; \
    else \
        composer update --no-dev --optimize-autoloader --no-scripts --no-interaction --prefer-dist \
        --ignore-platform-req=ext-oci8; \
    fi

# ============================================
# Stage 2: Imagem final de producao
# ============================================
FROM php:8.2-fpm AS production

ARG APP_ENV=production

# Timezone
ENV TZ=America/Sao_Paulo
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Dependencias do sistema + extensoes PHP
RUN --mount=type=cache,target=/var/cache/apt,sharing=locked \
    --mount=type=cache,target=/var/lib/apt/lists,sharing=locked \
    apt-get update && apt-get install -y --no-install-recommends \
    libaio1t64 \
    nginx \
    supervisor \
    curl \
    unzip \
    netcat-openbsd \
    libonig-dev \
    libxml2-dev \
    build-essential \
    && ldconfig \
    && ln -sf /usr/lib/x86_64-linux-gnu/libaio.so.1t64 /usr/lib/libaio.so.1 \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        xml \
        bcmath \
        opcache \
    && apt-get remove -y libonig-dev libxml2-dev build-essential \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Oracle Instant Client 21 + OCI8 + PDO_OCI
COPY docker/oracle/instantclient-basic-linux.x64-21.21.0.0.0.zip /tmp/oracle/
COPY docker/oracle/instantclient-sdk-linux.x64-21.21.0.0.0.zip /tmp/oracle/
COPY docker/oracle/oci8-3.3.0.tgz /tmp/oracle/
RUN mkdir -p /opt/oracle && cd /opt/oracle \
    && unzip -oq /tmp/oracle/instantclient-basic-linux.x64-21.21.0.0.0.zip \
    && unzip -oq /tmp/oracle/instantclient-sdk-linux.x64-21.21.0.0.0.zip \
    && rm -f /tmp/oracle/*.zip \
    && ORACLE_HOME=$(find /opt/oracle -maxdepth 1 -type d -name 'instantclient*' | head -1) \
    && test -n "$ORACLE_HOME" \
    && ln -sf "$ORACLE_HOME" /opt/oracle/instantclient \
    && echo "$ORACLE_HOME" > /etc/ld.so.conf.d/oracle-instantclient.conf \
    && echo "/usr/lib" > /etc/ld.so.conf.d/usrlib.conf \
    && ldconfig

# OCI8 a partir do tarball local
RUN bash -c 'ORACLE_HOME=$(find /opt/oracle -maxdepth 1 -type d -name "instantclient*" | head -1) \
    && test -n "$ORACLE_HOME" \
    && echo "instantclient,$ORACLE_HOME" | pecl install /tmp/oracle/oci8-3.3.0.tgz \
    && rm -rf /tmp/oracle \
    && docker-php-ext-enable oci8 \
    && php -m | grep -q oci8'

# PDO_OCI (necessario para new PDO('oci:...'))
RUN bash -c 'ORACLE_HOME=$(find /opt/oracle -maxdepth 1 -type d -name "instantclient*" | head -1) \
    && docker-php-ext-configure pdo_oci --with-pdo-oci=instantclient,$ORACLE_HOME \
    && docker-php-ext-install pdo_oci' \
    && docker-php-ext-enable pdo_oci \
    && php -m | grep pdo_oci

ENV ORACLE_HOME=/opt/oracle/instantclient
ENV LD_LIBRARY_PATH=/usr/lib/x86_64-linux-gnu:$ORACLE_HOME

# OPcache para producao
RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.enable_cli=0'; \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=10000'; \
        echo 'opcache.revalidate_freq=0'; \
        echo 'opcache.validate_timestamps=0'; \
        echo 'opcache.fast_shutdown=1'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

# PHP memory/limits
RUN { \
        echo 'memory_limit = 256M'; \
        echo 'max_execution_time = 120'; \
        echo 'max_input_time = 120'; \
        echo 'upload_max_filesize = 10M'; \
        echo 'post_max_size = 12M'; \
    } > /usr/local/etc/php/conf.d/api.ini

# Usuario nao-root
RUN groupadd -g 1000 www && \
    useradd -u 1000 -g www -m -d /home/www -s /bin/bash www && \
    mkdir -p /home/www && \
    chown -R www:www /home/www

WORKDIR /var/www/html

# Configs (mudam raramente)
COPY --chown=www:www docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY --chown=www:www docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY --chown=www:www docker/supervisor/supervisord.conf /etc/supervisord.conf

# Codigo da aplicacao
COPY --chown=www:www composer.json composer.lock ./
COPY --chown=www:www config ./config
COPY --chown=www:www routes ./routes
COPY --chown=www:www bootstrap ./bootstrap
COPY --chown=www:www database ./database
COPY --chown=www:www app ./app
COPY --chown=www:www public ./public
COPY --chown=www:www storage ./storage
COPY --chown=www:www artisan ./

# Dependencias do Composer
COPY --from=composer-builder --chown=www:www /app/vendor ./vendor

# Diretorios e permissoes
RUN mkdir -p storage/framework/{cache,sessions,views} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache \
    && mkdir -p /var/lib/nginx/tmp \
    && chown -R www:www /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# PHP-FPM config (clear_env = no para herdar LD_LIBRARY_PATH)
RUN { \
        echo '[www]'; \
        echo 'user = www'; \
        echo 'group = www'; \
        echo 'listen = 127.0.0.1:9000'; \
        echo 'clear_env = no'; \
        echo 'php_admin_value[error_log] = /var/log/php_errors.log'; \
        echo 'php_admin_flag[log_errors] = on'; \
    } >> /usr/local/etc/php-fpm.d/www.conf

# Script de inicializacao
COPY --chown=www:www docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=10s --start-period=30s --retries=3 \
    CMD curl -sf http://localhost/api/health || exit 1

CMD ["/start.sh"]
