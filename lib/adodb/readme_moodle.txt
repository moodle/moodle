Description of ADODB V5.10 library import into Moodle

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
 * server.php

Added:
 * index.html - prevent directory browsing on misconfigured servers
 * readme_moodle.txt - this file ;-)

Our changes:
 * none

skodak, iarenaza, moodler, stronk7
