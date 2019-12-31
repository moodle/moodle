GeoIP2 PHP API
==============

No changes from the upstream version have been made, it is recommended by upstream
to install these depdencies via composer - but the composer installation is bundled
with a load of test files, shell scripts etc (and we don't use composer to manage
'production depdendencies') so we have to do it manually.

Information
-----------

URL: http://maxmind.github.io/GeoIP2-php/
License: Apache License, Version 2.0.

Installation
------------

1) Download the latest versions of GeoIP2-php and MaxMind-DB-Reader-php
wget https://github.com/maxmind/GeoIP2-php/archive/v2.9.0.zip
wget https://github.com/maxmind/MaxMind-DB-Reader-php/archive/v1.4.1.zip

2) Unzip the archives
unzip v2.9.0.zip
unzip v1.4.1.zip

3) Move the source code directories into place
mv GeoIP2-php-2.9.0/src/ /path/to/moodle/lib/maxmind/GeoIp2/
mv MaxMind-DB-Reader-php-1.4.1/src/MaxMind/ /path/to/moodle/lib/maxmind/MaxMind/

4) Run unit tests on iplookup/tests/geoip_test.php.
