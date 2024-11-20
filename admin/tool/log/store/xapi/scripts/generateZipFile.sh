#!/usr/bin/env sh

# Installs composer production dependencies.
rm -rf vendor
composer install --no-interaction --no-dev

# Creates folder to zip.
rm -rf xapi
mkdir -p ./xapi/classes && cp -r ./classes ./xapi
mkdir -p ./xapi/db && cp -r ./db ./xapi
mkdir -p ./xapi/lang && cp -r ./lang ./xapi
mkdir -p ./xapi/src && cp -r ./src ./xapi
mkdir -p ./xapi/vendor && cp -r ./vendor ./xapi
cp ./LICENSE ./xapi
cp ./README.md ./xapi
cp ./settings.php ./xapi
cp ./version.php ./xapi

# Creates the zip file.
zip -r xapi.zip xapi
