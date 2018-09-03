Description of ADODB V5.20.9 library import into Moodle

This library will be probably removed in Moodle 2.1,
it is now used only in enrol and auth db plugins.
The core DML drivers are not using ADODB any more.

Removed:
 * contrib/
 * cute_icons_for_site/
 * docs/
 * nbproject/
 * pear/
 * replicate/
 * scripts/
 * session/
 * tests/
 * composer.json
 * README.md
 * server.php
 * lang/* except en (because they were not in utf8)

Renamed:
 * LICENSE.md -> license.txt

Added:
 * index.html - prevent directory browsing on misconfigured servers
 * readme_moodle.txt - this file ;-)

Our changes:
 * Removed random seed initialization from lib/adodb/adodb.inc.php:216 (see 038f546 and MDL-41198).
 * MDL-52286 Added muting erros in ADORecordSet::__destruct().
   Check if fixed upstream during the next upgrade and remove this note. (8638b3f1441d4b928)
 * MDL-58546 replaced each() with foreach for PHP 7.2 compatibility.
   pull request upstream: https://github.com/ADOdb/ADOdb/pull/373

skodak, iarenaza, moodler, stronk7, abgreeve, lameze, ankitagarwal, marinaglancy
