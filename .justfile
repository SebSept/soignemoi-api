docker_php_exec := "docker compose exec -it -u climber php"
symfony := docker_php_exec + " symfony "
# peut-être utiliser symfony + "composer"
composer := docker_php_exec + " composer "
console := symfony + "console "
docker_exec_nginx := "docker compose exec -it -u root nginx"

up:
    docker-compose up -d
#    docker exec -it -u climber {{container}} composer install

[private]
up-build:
    docker-compose up -d --build

reload_nginx:
   {{docker_exec_nginx}} nginx -s reload

# open a fish shell on the container
fish:
    {{docker_php_exec}} fish

[private]
fish_root:
    docker compose exec -it -u root php fish

[confirm("Démarrer le serveur symfony (et pas le serveur nginx), êtes-vous sûr ?")]
serve:
    {{symfony}} server:start --no-tls --daemon

new-controller:
    {{console}} make:controller

new-api:
    {{console}} make:entity --api-resource
    {{console}} make:migration
    {{console}} doctrine:migrations:migrate --no-interaction

# recréer une base de données
db-drop-schema:
    {{console}} doctrine:database:drop --force
    {{console}} doctrine:database:create
    # on ne doit pas avoir besoin de lancer les migrations dans le cas d'une création
    {{console}} doctrine:migrations:migrate --no-interaction
    echo "Base de données recréée"

db-create-test-schema:
    {{console}} doctrine:database:create --env=test
    {{console}} doctrine:schema:create --env=test

# Création des classes de fixtures
db-fixtures-make entity:
    {{console}} make:fixtures {{entity}}Fixtures

# Insertion des fixtures en base de données
db-fixtures-load:
    {{console}} doctrine:fixture:load --no-interaction
    # {{console}} doctrine:fixture:load --append

# composer require
req package:
    {{composer}} req {{package}}

# composer require --dev
req-dev package:
    {{composer}} req {{package}} --dev

# Lancement scripts d'outil de qualité via composer
quality:
    {{composer}} quality