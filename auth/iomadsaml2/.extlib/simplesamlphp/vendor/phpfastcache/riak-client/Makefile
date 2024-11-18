.PHONY: all unit-test integration-test security-test
.PHONY: install-composer install-deps help
.PHONY: release

all: test

test: unit-test integration-test scenario-test

unit-test:
	@php ./vendor/bin/phpunit --testsuite=unit-tests

integration-test:
	@php ./vendor/bin/phpunit --testsuite=functional-tests

security-test:
	@php ./vendor/bin/phpunit  --testsuite=security-tests

scenario-test:
	@php ./vendor/bin/phpunit  --testsuite=scenario-tests

timeseries-test:
	@php ./vendor/bin/phpunit tests/functional/TimeSeriesOperationsTest.php

install-deps:
	@./composer install

install-composer:
	@rm -f ./composer.phar ./composer
	@php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
	@php ./composer-setup.php --filename=composer
	@rm -f ./composer-setup.php

release:
ifeq ($(VERSION),)
	$(error VERSION must be set to deploy this code)
endif
ifeq ($(RELEASE_GPG_KEYNAME),)
	$(error RELEASE_GPG_KEYNAME must be set to deploy this code)
endif
	@./tools/build/publish $(VERSION) master validate
	@git tag --sign -a "$(VERSION)" -m "riak-php-client $(VERSION)" --local-user "$(RELEASE_GPG_KEYNAME)"
	@git push --tags
	@./tools/build/publish $(VERSION) master 'Riak PHP Client' 'riak-php-client'

help:
	@echo ''
	@echo ' Targets:'
	@echo '-----------------------------------------------------------------'
	@echo ' all                          - Run all tests                    '
	@echo ' install-deps                 - Install required dependencies    '
	@echo ' install-composer             - Installs composer                '
	@echo ' test                         - Run unit & integration tests     '
	@echo ' unit-test                    - Run unit tests                   '
	@echo ' integration-test             - Run integration tests            '
	@echo ' security-test                - Run security tests               '
	@echo '-----------------------------------------------------------------'
	@echo ''
