docker_php_exec := "docker compose exec -it -u climber php"
symfony := docker_php_exec + " symfony "
console := symfony + "console "
composer := docker_php_exec + " composer "
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
    docker compose exec -it -u climber php fish

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
drop-schema:
    {{console}} doctrine:database:drop --force
    {{console}} doctrine:database:create
    {{console}} doctrine:migrations:migrate --no-interaction
    echo "Base de données recréée"

# Création des classes de fixtures
fixtures-make entity:
    {{console}} make:fixtures {{entity}}Fixtures

# Insertion des fixtures en base de données
fixtures-load:
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