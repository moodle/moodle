Description of ADODB V5.08 library import into Moodle

Removed:
 * contrib/
 * cute_icons_for_site/
 * docs/
 * pear/
 * session/
 * tests/
 * server.php

Added:
 * index.html - prevent directory browsing on misconfigured servers
 * readme_moodle.txt - this file ;-)

Our changes: /// Look for "moodle" in adodb code
 * adodb-lib.inc.php - added support for "F" and "L" types in  _adodb_column_sql()
 * adodb-lib.inc.php - modify some debug output to be correct XHTML. MDL-12378.
       Reported to ADOdb at: http://phplens.com/lens/lensforum/msgs.php?id=17133
       Once fixed by adodb guys, we'll return to their official distro.
 * drivers/adodb-mssql.inc.php, drivers/adodb-oci8.inc.php (qstr) and
       adodb.inc.php (addq and qstr) - fixed wrong "undo magic quotes" that was
       ignoring "magic_quotes_sybase" and leading to wrongly escaped contents. MDL-19452
       Reported privately to John Lim, will be added to upstream soon. Once fixed
       we'll return to their official distro.

skodak, iarenaza, moodler, stronk7
