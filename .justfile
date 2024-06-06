docker_php_exec := "docker compose -f compose-dev.yaml exec -it -u www-data php"
composer := docker_php_exec + " composer "
console := docker_php_exec + " ./bin/console "
docker_exec_nginx := "docker compose exec -it -u root nginx"

up:
    docker compose -f compose-dev.yaml up -d

update: && tests
    git pull
    docker compose -f compose-dev.yaml down
    docker compose -f compose-dev.yaml pull
    docker compose -f compose-dev.yaml up -d --build
    {{composer}} install

reload_nginx:
   {{docker_exec_nginx}} nginx -s reload

# open a fish shell on the container
fish:
    {{docker_php_exec}} fish

[private]
fish_root:
    docker compose -f compose-dev.yaml exec -it -u root php fish

new-controller classname='':
    {{console}} make:controller {{classname}}

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

[private]
db-dump-with-fixtures: db-fixtures-load
    docker exec -it api-postgres-1 pg_dump -d soignemoi -U postgres --clean | gzip -c > db.sql.gz

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
    echo "docker compose -f compose-dev.yaml exec php composer run-script pre-commit" > .git/hooks/pre-commit
    chmod +x .git/hooks/pre-commit