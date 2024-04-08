docker_php_exec := "docker compose exec -it -u climber php"
symfony := docker_php_exec + " symfony "
# peut-être utiliser symfony + "composer"
composer := docker_php_exec + " composer "
console := symfony + "console "
docker_exec_nginx := "docker compose exec -it -u root nginx"

up:
    docker compose up -d
#    docker exec -it -u climber {{container}} composer install

update: && tests
    git pull
    docker compose down
    docker compose up -d --build
    {{composer}} install


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
db-create:
    {{console}} doctrine:database:drop --quiet --no-interaction --if-exists --force
    {{console}} doctrine:database:create --quiet --no-interaction
    {{console}} doctrine:schema:create --quiet --no-interaction
    echo "Base de données recréée"

db-migrate:
    {{console}} doctrine:migrations:migrate --no-interaction

db-create-test:
    {{console}} doctrine:database:drop --env=test --force --if-exists
    {{console}} doctrine:database:create --env=test
    {{console}} doctrine:schema:create --env=test

# Création des classes de fixtures
db-fixtures-make entity:
    {{console}} make:fixtures {{entity}}Fixtures

# Insertion des fixtures en base de données
db-fixtures-load:
    {{console}} doctrine:fixture:load --no-interaction

console command:
    {{console}} {{command}}
# composer require
req package:
    {{composer}} req {{package}}

# composer require --dev
req-dev package:
    {{composer}} req {{package}} --dev

# Lancement scripts d'outil de qualité via composer
composer script:
    {{composer}} {{script}}

rector:
    {{docker_php_exec}} vendor/bin/rector

phpstan:
    {{docker_php_exec}} vendor/bin/phpstan

cs-fix:
    {{docker_php_exec}} php-cs-fixer fix

tests format='--testdox':
    # {{docker_php_exec}} php vendor/bin/phpunit {{format}}
    {{docker_php_exec}} php vendor/bin/paratest --runner WrapperRunner {{format}}

test filter:
    {{docker_php_exec}} php bin/phpunit --filter {{filter}}

# création d'un test
# The test type must be one of "TestCase", "KernelTestCase", "WebTestCase", "ApiTestCase", "PantherTestCase"
make-test name type='ApiTestCase':
    {{console}} make:test {{type}} {{name}}

# exécution d'une requête SQL
sql query env='dev':
    {{console}} dbal:run-sql "{{query}}" --env {{env}}

# interactive php shell
psysh:
    {{docker_php_exec}} psysh

[confirm("Écraser .git/hooks/pre-commit ?")]
install-pre-commit-hook:
    echo "docker compose exec php symfony composer run-script pre-commit" > .git/hooks/pre-commit
    {{docker_php_exec}} chmod +x .git/hooks/pre-commit