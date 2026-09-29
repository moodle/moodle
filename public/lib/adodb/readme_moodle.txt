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

Modifications:
 * MDL-81555: Declared the $_nestedSQL property on the ADODB_pdo base class
   (drivers/adodb-pdo.inc.php). The pdo_pgsql and pdo_oci drivers'
   _init() methods set $parentDriver->_nestedSQL, but nothing in the PDO
   class chain declared it, so PHP 8.2 raised a "Creation of dynamic
   property" deprecation notice.
   Submitted upstream as https://github.com/ADOdb/ADOdb/pull/1253 - not
   yet merged. Remove this patch once that lands in a release Moodle
   upgrades to.
