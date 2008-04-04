Description of ADODB v4.98 library import into Moodle

Removed:
 * contrib/
 * cute_icons_for_site/
 * docs/
 * pear/
 * tests/
 * server.php

Added:
 * index.html - prevent directory browsing on misconfigured servers
 * readme_moodle.txt - this file ;-)

Our changes:
 * adodb-lib.inc.php - added support for "F" and "L" types in  _adodb_column_sql()
 * adodb-lib.inc.php - modify some debug output to be correct XHTML. MDL-12378.
       Reported to ADOdb at: http://phplens.com/lens/lensforum/msgs.php?id=17133
       Once fixed by adodb guys, we'll return to their official distro.
 * drivers/adodb-mysqli.inc.php - fixed problem with driver not detecting enums
       in the MetaColumns() function. MDL-14215.
       Reported to ADOdb at: http://phplens.com/lens/lensforum/msgs.php?id=17383
       Once fixed by adodb guys, we'll return to their official distro.

skodak, iarenaza, moodler, stronk7

$Id$
