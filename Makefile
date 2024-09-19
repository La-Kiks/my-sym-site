# Variables
DOCKER = docker
DOCKER_COMPOSE = docker compose
EXEC = $(DOCKER) exec -w /var/www/project www_oneblog
PHP = $(EXEC) php
COMPOSER = $(EXEC) composer
NPM = $(EXEC) npm
SYMFONY_CONSOLE = $(PHP) bin/console
SYMFONY = $(EXEC) symfony console
NAME = placeholder

# Colors
GREEN = /bin/echo -e "\x1b[32m\#\# $1\x1b[0m"
RED = /bin/echo -e "\x1b[31m\#\# $1\x1b[0m"

## —— 🔥 App ——
init: ## Init the project for dev
	$(MAKE) start-dev
	$(MAKE) composer-install
	$(MAKE) npm-install
	$(MAKE) database-init-dev
	$(MAKE) database-fixtures-load
	@$(call GREEN,"The application is available for dev http://127.0.0.1:8000/")
	$(MAKE) npm-watch

prod: ## For production
	$(MAKE) start
	$(MAKE) composer-install-prod
	$(MAKE) npm-install
	$(MAKE) database-init-prod
	$(MAKE) npm-build
	$(MAKE) cache-clear
	@$(call GREEN,"Production ready !")

cache-clear: ## Clear cache
	$(SYMFONY_CONSOLE) cache:clear

controller: ## Create Symfony Controller with NAME=Controller
	$(SYMFONY) make:controller $(NAME)

translations: ## Create Symfony Translation FR & EN
	$(SYMFONY_CONSOLE) translation:extract --force fr  --domain=messages
	$(SYMFONY_CONSOLE) translation:extract --force en  --domain=messages

## —— ✅ Test ——
.PHONY: tests
tests: ## Run all tests
	$(MAKE) database-init-test
	$(PHP) bin/phpunit --testdox tests/Unit/
	$(PHP) bin/phpunit --testdox tests/Functional/
	$(PHP) bin/phpunit --testdox tests/E2E/

database-init-test: ## Init database for test
	$(SYMFONY_CONSOLE) doctrine:database:drop --force --if-exists --env=test
	$(SYMFONY_CONSOLE) doctrine:database:create --env=test
	$(SYMFONY_CONSOLE) doctrine:migrations:migrate --no-interaction --env=test
	$(SYMFONY_CONSOLE) doctrine:fixtures:load --no-interaction --env=test

unit-test: ## Run unit tests
	$(MAKE) database-init-test
	$(PHP) bin/phpunit --testdox tests/Unit/

functional-test: ## Run functional tests
	$(MAKE) database-init-test
	$(PHP) bin/phpunit --testdox tests/Functional/

# PANTHER_NO_HEADLESS=1 ./bin/phpunit --filter LikeTest --debug to debug with Chrome
e2e-test: ## Run E2E tests
	$(MAKE) database-init-test
	$(PHP) bin/phpunit --testdox tests/E2E/

## —— 🐳 Docker ——
start: ## Start app
	$(MAKE) docker-start
start-dev: ## Start app dev
	$(DOCKER_COMPOSE) -f docker-compose-dev.yml up -d
docker-start:
	$(DOCKER_COMPOSE) up -d

build: ## Build images and then start app
	$(MAKE) docker-build 
docker-build:
	$(DOCKER_COMPOSE) up -d --build --remove-orphans

stop: ## Stop app
	$(MAKE) docker-stop
docker-stop:
	$(DOCKER_COMPOSE) stop
	@$(call RED,"The containers are now stopped.")
stop-dev:
	$(DOCKER_COMPOSE) -f docker-compose-dev.yml stop
	@$(call RED,"The containers are now stopped.")

## —— 🎻 Composer ——
composer-install: ## Install dependencies
	$(COMPOSER) install

composer-install-prod: ## Install dependencies
	$(COMPOSER) install --no-dev --optimize-autoloader

composer-update: ## Update dependencies
	$(COMPOSER) update

## —— 🐈 NPM —————————————————————————————————————————————————————————————————
npm-install: ## Install all npm dependencies
	$(NPM) init -y
	$(NPM) install

npm-update: ## Update all npm dependencies
	$(NPM) update

npm-watch: ## Update all npm dependencies
	$(NPM) run watch

npm-dev: ## Equivalent to npm run dev
	$(NPM) run dev

npm-build: ## Equivalent to npm run dev
	$(NPM) run build

## —— 📊 Database ——
database-init-dev: ## Init database
	$(MAKE) database-drop
	$(MAKE) delete-migrations
	$(MAKE) database-create
	$(MAKE) database-migration
	$(MAKE) database-migrate

database-init-prod: ## Init database
	$(MAKE) database-drop
	$(MAKE) delete-migrations
	$(MAKE) database-create
	$(MAKE) database-migration-diff
	$(MAKE) database-migrate

database-drop: ## Create database
	$(SYMFONY_CONSOLE) doctrine:database:drop --force --if-exists

database-create: ## Create database
	$(SYMFONY_CONSOLE) doctrine:database:create --if-not-exists

database-remove: ## Drop database
	$(SYMFONY_CONSOLE) d:d:d --force --if-exists

database-migration: ## Make migration doctrine:migrations:generate ?
	$(SYMFONY_CONSOLE) make:migration

database-migration-diff: ## Make migration doctrine:migrations:generate ?
	$(SYMFONY_CONSOLE) doctrine:migrations:diff

migration: ## Alias : database-migration
	$(MAKE) database-migration

database-migrate: ## Migrate migrations
	$(SYMFONY_CONSOLE) doctrine:migrations:migrate --no-interaction

delete-migrations: ## Delete the content of migrations folder
	$(EXEC) find ./migrations -type f -name 'Version*.php' -delete

migrate: ## Alias : database-migrate
	$(MAKE) database-migrate

database-fixtures-load: ## Load fixtures
	$(SYMFONY_CONSOLE) doctrine:fixtures:load --no-interaction

fixtures: ## Alias : database-fixtures-load
	$(MAKE) database-fixtures-load

## —— 🛠️  Others ——
help: ## List of commands
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

