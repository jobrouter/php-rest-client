.PHONY: qa
qa: cs tests mutation phpstan rector-dry changelog

# See: https://github.com/crossnox/m2r2
.PHONY: changelog
changelog:
	m2r2 CHANGELOG.md && \
	echo ".. _changelog:" | cat - CHANGELOG.rst > /tmp/CHANGELOG.rst && \
	mv /tmp/CHANGELOG.rst docs/changelog.rst && \
	rm CHANGELOG.rst

.PHONY: code-coverage
code-coverage: vendor
	XDEBUG_MODE=coverage vendor/bin/phpunit -c tests/phpunit.xml.dist --log-junit logs/phpunit.xml --coverage-text --coverage-clover logs/clover.xml

.PHONY: cs
cs: vendor
	vendor/bin/ecs check --fix

.PHONY: mutation
mutation: vendor
	XDEBUG_MODE=coverage vendor/bin/infection --min-msi=95 --threads=4 --no-ansi

.PHONY: phpstan
phpstan: vendor
	vendor/bin/phpstan analyse

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
