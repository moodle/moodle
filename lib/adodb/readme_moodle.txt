Description of ADODB v4.95a library import into Moodle

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
 * drivers/adodb-mssql_n.inc.php - Fixed one bug in the N' parser when one value start by '
        Once fixed by adodb guys, we'll return to their official distro.
 * drivers/adodb-odbc_mssql.inc.php - Fixed one buggy function (ServerInfo) that was not
       working properly. Simplified logic (now that we are FETCH_ASSOC). Work in progress
       for the annoying http://tracker.moodle.org/browse/MDL-6877.
       Once fixed by adodb guys, we'll return to their official distro.
 * lang/adodb-ar.inc.php lang/adodb-bg.inc.php lang/adodb-bgutf8.inc.php 
   lang/adodb-en.inc.php lang/adodb-pl.inc.php lang/adodb-ro.inc.php
   lang/adodb_th.inc.php - Removed leading white space outside PHP open/close tags
   (see http://tracker.moodle.org/browse/MDL-11632).


skodak,iarenaza

$Id$
