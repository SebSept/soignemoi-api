docker_php_exec := "docker compose exec -it -u climber php"
symfony := docker_php_exec + " symfony "
console := symfony + "console "

docker_exec_nginx := "docker compose exec -it -u root nginx"

up:
    docker-compose up -d
#    docker exec -it -u climber {{container}} composer install
#    docker exec -it -u climber {{container}} yarn install
#    docker exec -it -u climber {{container}} yarn encore dev

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

fixtures:
    {{console}} make:fixtures
    {{console}} doctrine:fixture:load
    # {{console}} doctrine:fixture:load --append

# composer require
req package:
    {{symfony}} composer req {{package}}
