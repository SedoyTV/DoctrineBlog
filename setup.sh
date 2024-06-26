#!/bin/bash

cp .env.example .env

php artisan session:table

docker-compose exec php composer install

docker-compose exec php php artisan key:generate

composer require tymon/jwt-auth

php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"

php artisan jwt:secret

docker-compose exec php chown -R www-data:www-data /var/www/html/storage

docker-compose exec php chmod -R 777 /var/www/html/storage

docker-compose exec php chmod -R 777 /var/www/html/setup.sh

docker-compose exec php /var/www/html/artisan migrate

echo "Установка завершена"
