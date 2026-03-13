#!/bin/sh

set -e

echo "Iniciando Supreal API..."

# Carregar .env montado como volume
if [ -f /var/www/html/.env ]; then
    echo "Carregando variaveis do .env..."
    set -a
    . /var/www/html/.env
    set +a
fi

# Criar diretorios
mkdir -p /var/log/supervisor
mkdir -p /var/log/nginx
mkdir -p /var/run
mkdir -p /run
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

# Permissoes
chown -R www:www /var/www/html/storage
chown -R www:www /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache
chmod -R 777 /var/www/html/storage/framework/views
chmod -R 777 /var/www/html/storage/framework/cache
chmod -R 777 /var/www/html/storage/logs

# .env fallback
if [ ! -f /var/www/html/.env ]; then
    echo ".env nao encontrado. Criando vazio..."
    touch /var/www/html/.env
fi

# APP_KEY
if [ -n "$APP_KEY" ] && [ "$APP_KEY" != "" ]; then
    echo "APP_KEY encontrada nas variaveis de ambiente"
elif [ -f /var/www/html/.env ]; then
    APP_KEY_VALUE=$(grep '^APP_KEY=' /var/www/html/.env 2>/dev/null | cut -d'=' -f2 | tr -d ' ' || echo "")
    if [ -z "$APP_KEY_VALUE" ] || [ "$APP_KEY_VALUE" = "" ]; then
        echo "Gerando APP_KEY..."
        php artisan key:generate --force --no-interaction 2>/dev/null || echo "Nao foi possivel gerar APP_KEY"
    fi
fi

# Aguardar Oracle
if [ -n "$ORACLE_HOST" ] && [ -n "$ORACLE_PORT" ]; then
    echo "Aguardando Oracle em $ORACLE_HOST:$ORACLE_PORT..."
    timeout=60
    while ! nc -z "$ORACLE_HOST" "$ORACLE_PORT"; do
        if [ $timeout -le 0 ]; then
            echo "Timeout: Oracle nao disponivel"
            break
        fi
        sleep 2
        timeout=$((timeout-2))
    done
    echo "Oracle conectado!"
fi

# Aguardar MySQL (banco principal)
if [ -n "$DB_HOST" ] && [ -n "$DB_PORT" ]; then
    echo "Aguardando MySQL em $DB_HOST:$DB_PORT..."
    timeout=60
    while ! nc -z "$DB_HOST" "$DB_PORT"; do
        if [ $timeout -le 0 ]; then
            echo "Timeout: MySQL nao disponivel"
            break
        fi
        sleep 2
        timeout=$((timeout-2))
    done
    echo "MySQL conectado!"
fi

# Cache Laravel
echo "Configurando cache..."
if [ "$APP_ENV" = "production" ]; then
    if [ ! -f bootstrap/cache/config.php ] || [ "$FORCE_CACHE_REBUILD" = "true" ]; then
        php artisan config:cache
        php artisan route:cache
        echo "Cache gerado!"
    fi
else
    php artisan config:clear
    php artisan cache:clear
    php artisan route:clear
    php artisan config:cache
    php artisan route:cache
    echo "Cache atualizado (desenvolvimento)!"
fi

# Migrations
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Executando migrations..."
    php artisan migrate --force
fi

# Permissoes finais
chown -R www:www /var/www/html/storage
chmod -R 775 /var/www/html/storage
chmod -R 777 /var/www/html/storage/logs

echo "Supreal API pronta!"

# Supervisor
exec /usr/bin/supervisord -c /etc/supervisord.conf
