Description of ADODB v4.92a library import into Moodle

Removed:
 * contrib/
 * cute_icons_for_site/
 * docs/
 * pear/
 * tests/
 * server.php

Added:
 * index.html - prevent directory browsing on misconfigured servers
 * tests/tmssql.php - detection of attack attempts and overwrite on incorrectly upgraded sites
 * readme_moodle.txt - this file ;-)

Our changes:
 * adodb-lib.inc.php - forced conversion to proper numeric type in _adodb_column_sql()
 * drivers-adodb-postgres7.inc.php - removed (commented) one buggy ServerInfo() call that
       was preventing PG version detection to work. http://tracker.moodle.org/browse/MDL-6647
       Once solved by adodb guys, we'll undo this (affects to version v4.92a of ADOdb)
 * drivers/adodb-odbc_mssql.inc.php - added one missing function (MetaIndexes) that is
       required in order to support the driver properly. Work in progress for the annoying
       http://tracker.moodle.org/browse/MDL-6877.
       Once implemented by adodb guys, we'll return to their official distro.

 
skodak
30 August 2006
