default: ./vendor/autoload.php

test: ./vendor/autoload.php
	./bin/phpunit -c phpunit.xml

lint: ./vendor/autoload.php
	./bin/php-cs-fixer fix --config=.php_cs --dry-run --diff

fix: ./vendor/autoload.php
	./bin/php-cs-fixer fix --config=.php_cs || true

.PONY: default test lint fix

./vendor/autoload.php: ./composer.phar
	php composer.phar install -o --no-suggest -n

./composer.phar:
	curl -q https://getcomposer.org/installer | php
