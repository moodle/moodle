Description of ADODB v4.92a library import into Moodle

Removed:
 * contrib
 * cute_icons_for_site
 * docs
 * pear
 * tests

Added:
 * index.html - prevent directory browsing on misconfigured servers
 * tests/tmssql.php - detection of attack attempts and overwrite on incorrectly upgraded sites
 * readme_moodle.txt - this file ;-)

Our changes:
 * adodb-lib.inc.php - forced conversion to proper numeric type in _adodb_column_sql()
 
skodak
30 August 2006