GeoIP2 PHP API
==============

No changes from the upstream version have been made, it is recommended by upstream
to install these depdencies via composer - but the composer installation is bundled
with a load of test files, shell scripts etc (and we don't use composer to manage
'production dependencies') so we have to do it manually.

Information
-----------

URL: http://maxmind.github.io/GeoIP2-php/
License: Apache License, Version 2.0.

Installation
------------

1) Download the latest versions of GeoIP2-php and MaxMind-DB-Reader-php
wget https://github.com/maxmind/GeoIP2-php/archive/vX.Y.Z.zip
wget https://github.com/maxmind/MaxMind-DB-Reader-php/archive/vX.Y.Z.zip

2) Unzip the archives
unzip vX.Y.Z.zip
unzip vX.Y.Z.zip

3) Move the source code directories into place
mv GeoIP2-php-X.Y.Z/src/ /path/to/moodle/lib/maxmind/GeoIp2/
mv MaxMind-DB-Reader-php-X.Y.Z/src/MaxMind/ /path/to/moodle/lib/maxmind/MaxMind/

4) Update other MaxMind related files:
mv GeoIP2-php-X.Y.Z/CHANGELOG.md /path/to/moodle/lib/maxmind/GeoIp2/
mv GeoIP2-php-X.Y.Z/README.md /path/to/moodle/lib/maxmind/GeoIp2/
mv GeoIP2-php-X.Y.Z/composer.json /path/to/moodle/lib/maxmind/GeoIp2/
mv GeoIP2-php-X.Y.Z/LICENSE /path/to/moodle/lib/maxmind/GeoIp2/

mv MaxMind-DB-Reader-php-X.Y.Z/LICENSE /path/to/moodle/lib/maxmind/MaxMind/
mv MaxMind-DB-Reader-php-X.Y.Z/CHANGELOG.md /path/to/moodle/lib/maxmind/MaxMind/
mv MaxMind-DB-Reader-php-X.Y.Z/README.md /path/to/moodle/lib/maxmind/MaxMind/
mv MaxMind-DB-Reader-php-X.Y.Z/composer.json /path/to/moodle/lib/maxmind/MaxMind/
mv MaxMind-DB-Reader-php-X.Y.Z/autoload.php /path/to/moodle/lib/maxmind/MaxMind/

5) Run unit tests on iplookup/tests/geoip_test.php.

6) Update maxmind/GeoIp2 and maxmind/MaxMind versions in lib/thirdpartylibs.xml
