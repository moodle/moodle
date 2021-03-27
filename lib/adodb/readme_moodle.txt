Description of ADODB V5.20.16 library import into Moodle

This library will be probably removed in Moodle 2.1,
it is now used only in enrol and auth db plugins.
The core DML drivers are not using ADODB any more.

Removed:
 * Any invisible file (dot suffixed)
 * composer.json
 * contrib/ (if present)
 * cute_icons_for_site/ (if present)
 * docs/
 * lang/* everything but adodb-en.inc.php (originally because they were not utf-8, now because of not used)
 * pear/



 * nbproject/
 * replicate/
 * scripts/
 * session/
 * tests/
 * server.php
 * lang/* except en (because they were not in utf8)

Added:
 * index.html - prevent directory browsing on misconfigured servers
 * readme_moodle.txt - this file ;-)

Our changes:
 * MDL-67034 Fixes to make the library php74 compliant.
