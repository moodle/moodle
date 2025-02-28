Instructions for importing composer/pcre into Moodle.

Note: This package is used by phpoffice/phpspreadsheet.

```sh
cp lib/composer/pcre/readme_moodle.txt ./
rm -rf lib/composer/pcre
tempdir=`mktemp -d`
cd $tempdir
composer init -n --require composer/pcre:*
composer install
cd -
cp -r $tempdir/vendor/composer/pcre lib/composer/pcre
mv readme_moodle.txt lib/composer/pcre
rm -rf $tempdir
```
