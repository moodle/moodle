Instructions to import/update guzzle library into Moodle:

Update Guzzle and associated libraries.

```
installdir=`mktemp -d`
cd "$installdir"
composer require guzzlehttp/guzzle kevinrob/guzzle-cache-middleware

cd -
rm -rf lib/guzzlehttp/guzzle lib/guzzlehttp/psr7 lib/guzzlehttp/promises lib/guzzlehttp/kevinrob/guzzlecache
cp -rf "$installdir"/vendor/guzzlehttp/guzzle lib/guzzlehttp/guzzle
cp -rf "$installdir"/vendor/guzzlehttp/psr7 lib/guzzlehttp/psr7
cp -rf "$installdir"/vendor/guzzlehttp/promises lib/guzzlehttp/promises
cp -rf "$installdir"/vendor/kevinrob/guzzle-cache-middleware lib/guzzlehttp/kevinrob/guzzlecache
rm -rf lib/guzzlehttp/kevinrob/guzzlecache/*.png

echo "See instructions in lib/guzzlehttp/readme_moodle.md" > lib/guzzlehttp/guzzle/readme_moodle.txt
echo "See instructions in lib/guzzlehttp/readme_moodle.md" > lib/guzzlehttp/promises/readme_moodle.txt
echo "See instructions in lib/guzzlehttp/readme_moodle.md" > lib/guzzlehttp/psr7/readme_moodle.txt
echo "See instructions in lib/guzzlehttp/readme_moodle.md" > lib/guzzlehttp/kevinrob/guzzlecache/readme_moodle.txt
git add lib/guzzlehttp/guzzle lib/guzzlehttp/psr7 lib/guzzlehttp/promises
```

Now update `lib/thirdpartylibs.xml`
