PHP SDK Package
===============

[Documentation](https://ghe.iparadigms.com/pages/Integrations/phpsdk-package)

A version of the PHP SDK updated to include namespaces and loadable using composer.

install the package using composer:

```
composer install
```


Using the Sandbox examples
--------------------------------------------

Prerequisites for running integration tests and using example / sandbox php files
require the following environment variables to be set:

> NOTE: *These values are examples, substitute your actual base URL and credentials*

```
export TII_ACCOUNT=12345
export TII_APIBASEURL=https://sandbox.turnitin.com
export TII_SECRET=password
export TII_APIPRODUCT=26
```

Once the above is set you can use / adapt the example.php file in the `sandbox`
directory to test out the SDK.

`sandbox` examples can be tested with the built in PHP dev server like so:

```
php -S localhost:3000 sandbox/example.php
```

Running tests
-------------

Tests can be ran from the base directory by using `phpunit`

```
./vendor/bin/phpunit
```

You can run individual tests like so:

```
./vendor/bin/phpunit --filter UserTest::testCreateUserWithoutFirstName
```

Or a set of tests like so:

```
./vendor/bin/phpunit --filter UserTest
```

Docker Container
------------

You can use the included Dockerfile to create a testing environment that will save you from needing a local PHP install.

Copy and update the `.envfile` using `.envfile.template` as a template:
```
cp .envfile.template .envfile
```

Build the image:
```
docker build -t phpsdk-package .
```

Install composer dependencies:
```
docker run -v ${PWD}:/root/phpsdk --env-file ${PWD}/.envfile -it phpsdk-package composer install
```

Run the tests:
```bash
# Full suite
docker run -v ${PWD}:/root/phpsdk --env-file ${PWD}/.envfile -it phpsdk-package phpdbg -qrr ./vendor/bin/phpunit

# Smoke test suite
docker run -v ${PWD}:/root/phpsdk --env-file ${PWD}/.envfile -it phpsdk-package phpdbg -qrr ./vendor/bin/phpunit --group smoke

# Sanity test suite
docker run -v ${PWD}:/root/phpsdk --env-file ${PWD}/.envfile -it phpsdk-package phpdbg -qrr ./vendor/bin/phpunit --group sanity
```

To view generated coverage run:
```
docker run -v ${PWD}:/root/phpsdk --env-file ${PWD}/.envfile -p 3040:3040 -it phpsdk-package php -S 0.0.0.0:3040 -t tests/_reports/coverage
```
Then visit http://localhost:3040

To run in parallel (no coverage):
```
docker run -v ${PWD}:/root/phpsdk --env-file ${PWD}/.envfile -it phpsdk-package phpdbg -qrr ./vendor/bin/paratest -p 19
```

To execute a php file in the sandbox directory (for example `example.php`):
```
docker run -v ${PWD}:/root/phpsdk --env-file ${PWD}/.envfile -p 3040:3040 -it phpsdk-package php -S 0.0.0.0:3040 -t sandbox
```
Then visit http://localhost:3040/example.php
