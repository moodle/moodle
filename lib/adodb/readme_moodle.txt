Description of ADODB V5.19 library import into Moodle

This library will be probably removed in Moodle 2.1,
it is now used only in enrol and auth db plugins.
The core DML drivers are not using ADODB any more.

Removed:
 * contrib/
 * cute_icons_for_site/
 * docs/
 * pear/
 * session/
 * tests/
 * composer.json
 * README.md
 * server.php
 * lang/* except en (because they were not in utf8)

Added:
 * index.html - prevent directory browsing on misconfigured servers
 * readme_moodle.txt - this file ;-)

Our changes:
 * Removed random seed initialization from lib/adodb/adodb.inc.php:177 (see 038f546 and MDL-41198).
 * Added commit to fix associative fetch mode https://github.com/ADOdb/ADOdb/commit/97c3afacb3e4f98195908101bf3621e5cc847635
   When upgrading ADODB to 5.20 or higher, check
   whether that commit was included and if not
   remove this item

skodak, iarenaza, moodler, stronk7
