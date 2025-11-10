Description of PhpSpreadsheet import into Moodle

```sh
mv lib/phpspreadsheet/readme_moodle.txt ./
mv lib/phpspreadsheet/moodle.diff ./
rm -rf lib/phpspreadsheet/*
tempdir=`mktemp -d`
cd "${tempdir}"
composer init --require phpoffice/phpspreadsheet:^5 -n
cat composer.json | jq '.replace."composer/pcre"="*"' --indent 4 > composer.json.tmp; mv composer.json.tmp composer.json
cat composer.json | jq '.replace."maennchen/zipstream-php"="*"' --indent 4 > composer.json.tmp; mv composer.json.tmp composer.json
cat composer.json | jq '.replace."psr/http-client"="*"' --indent 4 > composer.json.tmp; mv composer.json.tmp composer.json
cat composer.json | jq '.replace."psr/http-factory"="*"' --indent 4 > composer.json.tmp; mv composer.json.tmp composer.json
cat composer.json | jq '.replace."psr/http-message"="*"' --indent 4 > composer.json.tmp; mv composer.json.tmp composer.json
cat composer.json | jq '.replace."psr/simple-cache"="*"' --indent 4 > composer.json.tmp; mv composer.json.tmp composer.json
composer install
rm -rf vendor/composer
rm vendor/autoload.php
# Delete all hidden files and phpstan.neon
find vendor -name '.*' | xargs rm -rf {} \;
find vendor -name 'phpstan*' | xargs rm -rf {} \;
# Delete legacy Xls (Excel5) files.
find vendor -name 'Xls.php' | xargs rm -rf {} \;
find vendor -name 'Xls' | xargs rm -rf {} \;
# Delete legacy OLE files.
find vendor -name 'OLE' | xargs rm -rf {} \;
find vendor -name 'OLERead.php' | xargs rm -rf {} \;
find vendor -name 'OLE.php' | xargs rm -rf {} \;
find vendor -name '*.dist' | xargs rm -rf {} \;
# Remove examples.
find vendor -name 'examples' | xargs rm -rf {} \;
cd -
cp -rf "${tempdir}/vendor/"* lib/phpspreadsheet/
mv readme_moodle.txt lib/phpspreadsheet/
mv moodle.diff ./lib/phpspreadsheet/
mv lib/phpspreadsheet/phpoffice/phpspreadsheet lib/phpspreadsheet
rm -rf $tempdir
git add .
```

Now update the lib/thirpartylibs.xml with the upgrades, and commit the changes.
Now apply the local Moodle customisations diff:

```sh
git apply lib/phpspreadsheet/moodle.diff
git add .
git commit
```

Now verify the changes:

Go to http://<your moodle root>/lib/tests/other/spreadsheettestpage.php and test the generated files
