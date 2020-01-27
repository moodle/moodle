Description of ADODB V5.20.14 library import into Moodle

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
 * MDL-67034 Fixes to make the library php74 compliant.
 * MDL-67414 Fix to make the library PostgreSQL 12.x compliant (upstream: a4876f100602c2ce4).

skodak, iarenaza, moodler, stronk7, abgreeve, lameze, ankitagarwal, marinaglancy, matteo
