# libphonenumber-for-php-lite

## Installation / Update

1. Run the following commands

```
installdir=$(mktemp -d)
cd "$installdir" || exit
composer require giggsey/libphonenumber-for-php-lite

cd - || exit
rm -rf public/lib/giggsey/libphonenumber-for-php-lite/src
cp -rf "$installdir"/vendor/giggsey/libphonenumber-for-php-lite/src public/lib/giggsey/libphonenumber-for-php-lite/src
cp -rf "$installdir"/vendor/giggsey/libphonenumber-for-php-lite/{composer.json,LICENSE.txt,README.md} public/lib/giggsey/libphonenumber-for-php-lite

git add public/lib/giggsey/libphonenumber-for-php-lite

rm -rf "$installdir"
```

2. Check for any new dependencies
3. Update `thirdpartylibs.xml`