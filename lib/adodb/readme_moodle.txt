Description of ADODB v4.9 library import into Moodle

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

our bug fixes:
 * drivers/adodb-postgres7.inc.php: line 33 $zthis typo - should be $this

skodak
29 August 2006