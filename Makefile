build: ; docker-compose build --no-cache

start: ; docker-compose up -d

stop: ; docker-compose down

install: ; docker-compose run --rm app composer install --no-interaction

update: ; docker-compose run --rm app composer update --no-interaction

composer-self-update: ; docker-compose run --rm app composer self-update --no-interaction

test: ; docker-compose run --rm app ./vendor/bin/phpunit -c ./phpunit.xml

infection: ; docker-compose run --rm app ./vendor/bin/infection

sh: ; docker-compose run --rm app bash