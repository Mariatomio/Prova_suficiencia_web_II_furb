Para rodar o projeto podes realizar esses passos abaixo:

composer install
cp .env.example .env

exemplo para a .env necessário

<!--
    APP_NAME=Laravel
    APP_ENV=local
    APP_KEY=base64:ImLvMx7YfcY5H8KfCzSnCkDVaZHcuwVS3pXKS31nfyc=
    APP_DEBUG=true
    APP_URL=http://127.0.0.1:8001

    APP_LOCALE=en
    APP_FALLBACK_LOCALE=en
    APP_FAKER_LOCALE=en_US

    APP_MAINTENANCE_DRIVER=file
    # APP_MAINTENANCE_STORE=database

    PHP_CLI_SERVER_WORKERS=4

    BCRYPT_ROUNDS=12

    LOG_CHANNEL=stack
    LOG_STACK=single
    LOG_DEPRECATIONS_CHANNEL=null
    LOG_LEVEL=debug

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=prova_suficiencia
    DB_USERNAME=root
    DB_PASSWORD=root

    SESSION_DRIVER=database
    SESSION_LIFETIME=120
    SESSION_ENCRYPT=false
    SESSION_PATH=/
    SESSION_DOMAIN=null

    BROADCAST_CONNECTION=log
    FILESYSTEM_DISK=local
    QUEUE_CONNECTION=database

    CACHE_STORE=database
    # CACHE_PREFIX=

    MEMCACHED_HOST=127.0.0.1

    REDIS_CLIENT=phpredis
    REDIS_HOST=127.0.0.1
    REDIS_PASSWORD=null
    REDIS_PORT=6379

    MAIL_MAILER=log
    MAIL_SCHEME=null
    MAIL_HOST=127.0.0.1
    MAIL_PORT=2525
    MAIL_USERNAME=null
    MAIL_PASSWORD=null
    MAIL_FROM_ADDRESS="hello@example.com"
    MAIL_FROM_NAME="${APP_NAME}"

    AWS_ACCESS_KEY_ID=
    AWS_SECRET_ACCESS_KEY=
    AWS_DEFAULT_REGION=us-east-1
    AWS_BUCKET=
    AWS_USE_PATH_STYLE_ENDPOINT=false

    VITE_APP_NAME="${APP_NAME}"

    L5_SWAGGER_GENERATE_ALWAYS=true
    L5_SWAGGER_CONST_HOST=http://127.0.0.1:8001
 -->

php artisan key:generate
php artisan migrate
php artisan l5-swagger:generate
php artisan serve --port=8001 <!-- Configurei o projeto nessa porta por isso do 8001 -->


<!-- Vai rodar tanto no:
http://localhost:8001/api quanto no
http://127.0.0.1:8001/api -->


Exemplos das requests mas podes pegar pelo swagger: 
POST /api/login - Login e obtenção do token
POST /api/logout - Logout precisa do token do login
GET /api/comandas/minha - Lista as comandas e produtos do usuário autenticado precisa do token do login
GET /api/comandas - Lista todos os usuários (comandas)
GET /api/comandas/{id} - Detalhes da comanda e produtos por usuário
POST /api/comandas - Cria nova comanda com produtos
PUT /api/comandas/{id} - Atualiza produtos da comanda
DELETE /api/comandas/{id} - Remove uma comanda

Authorization: Bearer {token}

Swagger
http://localhost:8001/api/documentation
