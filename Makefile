default: ./vendor/autoload.php

test: ./vendor/autoload.php
	./bin/phpunit -c phpunit.xml

testreport: ./vendor/autoload.php
	./bin/phpunit -c phpunit.xml --coverage-html report/coverage --testdox-text report/result.txt --testdox-html report/result.html

lint: ./vendor/autoload.php
	./bin/php-cs-fixer fix --config=.php_cs --dry-run --diff

fix: ./vendor/autoload.php
	./bin/php-cs-fixer fix --config=.php_cs || true

clobber:
	rm -rf .php_cs.cache
	rm -rf composer.lock
	rm -rf composer.phar
	rm -rf bin
	rm -rf report
	rm -rf vendor

.PONY: default test lint fix testreport clobber

./vendor/autoload.php: ./composer.phar
	php composer.phar install -o --no-suggest -n

./composer.phar:
	curl -q https://getcomposer.org/installer | php
