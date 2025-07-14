PHP CSS Parser
--------------

Import procedure:
```
tempdir=`mktemp -d`
cd $tempdir
composer require sabberworm/php-css-parser
cd -
cp lib/php-css-parser/readme_moodle* $tempdir
rm -rf lib/php-css-parser
cp -rf $tempdir/vendor/sabberworm/php-css-parser lib/php-css-parser
cp -rf $tempdir/readme* lib/php-css-parser
```

Apply the following patches:
- https://github.com/sabberworm/PHP-CSS-Parser/pull/173
