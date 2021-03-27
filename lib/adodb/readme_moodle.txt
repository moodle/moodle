Description of ADOdb v5.21.0 library import into Moodle 3.11 and up

Source: https://github.com/ADOdb/ADOdb

This library will be probably removed sometime in the future
because it is now used only by enrol and auth db plugins.

Removed:
 * Any invisible file (dot suffixed)
 * composer.json
 * contrib/ (if present)
 * cute_icons_for_site/ (if present)
 * docs/
 * lang/* everything but adodb-en.inc.php (originally because they were not utf-8, now because of not used)
 * nbproject/ (if present)
 * pear/
 * replicate/
 * scripts/
 * server.php
 * session/
 * tests/

Added:
 * index.html - prevent directory browsing on misconfigured servers
 * readme_moodle.txt - this file ;-)

Our changes (to be checked on next update if they are already applied upstream)::
 * https://github.com/ADOdb/ADOdb/issues/711
