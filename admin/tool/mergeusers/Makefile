# This file assumes exists a .env with the necessary environment variables.
current-dir := $(dir $(realpath $(lastword $(MAKEFILE_LIST))))

# By default, only "container_name=name_of_container" is required. See below.
# If this file does not exists during development, the make command fails.
# This file should define at least:
# ===========================
# container_name=name_of_container
# start=command to start the whole stack (web server, database, etc) to test the plugin
# stop=command to stop th whole stack
# ===========================
# On my setup, both "start" and "stop" commands must be placed without colon nor double colons
# to work properly.
# I decided to proceed this way, since I work with custom shell scripts over the moodle-docker setup.
include $(current-dir).env

# This file assumes dockerized development environment and moodle-local_codechecker plugin installed.
# However, you can redefine these variables into the .env file to meet your needs.
ifndef docker:
docker := docker exec -it $(container_name)
endif
ifndef docker-with-xdebug:
docker-with-xdebug := docker exec -e XDEBUG_SESSION=1 -it $(container_name)
endif
ifndef phpcs:
phpcs := $(docker) php local/codechecker/vendor/bin/phpcs
endif
ifndef phpcbf:
phpcbf := $(docker) php local/codechecker/vendor/bin/phpcbf
endif

.PHONY: start
start:
	bash -c "$(start)"

.PHONY: stop
stop:
	bash -c "$(stop)"

.PHONY: pass-tests
pass-tests: options =
pass-tests:
	$(docker) php vendor/bin/phpunit -c admin/tool/mergeusers --testdox $(options)

.PHONY: pass-tests-with-xdebug
pass-tests-with-xdebug:
	$(docker-with-xdebug) php vendor/bin/phpunit -c admin/tool/mergeusers --testdox $(options)

.PHONY: build-phpunit-xml
build-phpunit-xml:
	$(docker)  php admin/tool/phpunit/cli/util.php --buildcomponentconfig

.PHONY: init-phpunit
init-phpunit:
	$(docker) php admin/tool/phpunit/cli/init.php

.PHONY: phpcs
phpcs: options = admin/tool/mergeusers --ignore=tests/
phpcs:
	$(phpcs) $(options)

.PHONY: phpcs
phpcs-for-staged-files:
	$(phpcs) $$( echo $$(git diff --cached --name-only | xargs -I {} -n 1 echo 'admin/tool/mergeusers/{}')) --ignore=tests/


.PHONY: phpcs-list-sniffs
phpcs-list-sniffs: options = -e
phpcs-list-sniffs:
	$(phpcs) $(options)

.PHONY: phpcbf
phpcbf: options = admin/tool/mergeusers
phpcbf:
	$(phpcbf) $(options)

.PHONY: phpcbf-for-staged-files
phpcbf-for-staged-files:
	$(phpcbf) $$( echo $$(git diff --cached --name-only | xargs -I {} -n 1 echo 'admin/tool/mergeusers/{}')) --ignore=tests/


.PHONY: purgecaches
purgecaches:
	$(docker) php admin/cli/purge_caches.php

.PHONY: upgrade
upgrade:
	$(docker) php admin/cli/upgrade.php --non-interactive

.PHONY: run-cli-merge
run-cli-merge:
	$(docker) php admin/tool/mergeusers/cli/climerger.php

.PHONY: list-user-fields
list-user-fields:
	$(docker) php admin/tool/mergeusers/cli/listuserfields.php
