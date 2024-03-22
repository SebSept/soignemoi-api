# name of php docker container - get it using `docker ps`
container := "api-php-1"
docker_exec := "docker exec -it -u climber " + container
symfony := docker_exec + " symfony "
console := symfony + "console "

init:
    docker-compose up -d
#    docker exec -it -u climber {{container}} composer install
#    docker exec -it -u climber {{container}} yarn install
#    docker exec -it -u climber {{container}} yarn encore dev

fish:
    docker exec -it -u climber {{container}} fish

serve:
    {{symfony}} server:start --no-tls --daemon

new-controller:
    {{console}} make:controller

new-api:
    {{console}} make:entity --api-resource
    {{console}} make:migration
    {{console}} doctrine:migrations:migrate

fixtures:
    {{console}} make:fixtures
    {{console}} doctrine:fixture:load
    # {{console}} doctrine:fixture:load --append

# composer require
req package:
    {{symfony}} composer req {{package}}

test:
    #!/bin/bash

    # Récupérer les IDs des conteneurs créés par Docker Compose
    container_ids=$(docker-compose ps -q)

    # Parcourir les IDs et obtenir les noms des conteneurs
    for container_id in $container_ids
    do
        container_name=$(docker inspect --format='{{{{.Name}}' $container_id)
        # Supprimer le slash "/" ajouté par Docker dans le nom du conteneur
        container_name=${container_name:1}
        echo "Nom du conteneur: $container_name"
    done