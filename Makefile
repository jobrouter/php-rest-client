.PHONY: qa
qa: cs tests mutation-tests psalm rector-dry

.PHONY: code-coverage
code-coverage: vendor
	XDEBUG_MODE=coverage vendor/bin/phpunit -c tests/phpunit.xml.dist --log-junit logs/phpunit.xml --coverage-text --coverage-clover logs/clover.xml

.PHONY: cs
cs: vendor
	vendor/bin/ecs check --fix

.PHONY: mutation-tests
mutation-tests: vendor
	XDEBUG_MODE=coverage vendor/bin/infection --min-msi=94 --threads=4 --no-ansi

.PHONY: psalm
psalm: vendor
	vendor/bin/psalm

.PHONY: rector
rector: vendor
	vendor/bin/rector

.PHONY: rector-dry
rector-dry: vendor
	vendor/bin/rector --dry-run

.PHONY: tests
tests: vendor
	vendor/bin/phpunit --configuration=tests/phpunit.xml.dist

vendor: composer.json composer.lock
	composer validate
	composer install
	composer normalize
