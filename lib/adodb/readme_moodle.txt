Description of ADODB V5.20.16 library import into Moodle

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
 * server.php
 * lang/* except en (because they were not in utf8)

Added:
 * index.html - prevent directory browsing on misconfigured servers
 * readme_moodle.txt - this file ;-)

Our changes:
 * MDL-67034 Fixes to make the library php74 compliant.
