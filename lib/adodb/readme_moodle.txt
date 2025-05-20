Description of ADOdb library import into Moodle

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
 * server.php (if present)
 * session/

Added:
 * index.html - prevent directory browsing on misconfigured servers
 * readme_moodle.txt - this file ;-)

Changelog:
 * MDL-85375:
   - Fix SQL injection in pg_insert_id() - https://github.com/ADOdb/ADOdb/commit/11107d6d6e5160b62e05dff8a3a2678cf0e3a426
