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
 * replicate/ (if present)
 * scripts/ (if present)
 * server.php (if present)
 * session/
 * tests/ (if present)

Added:
 * index.html - prevent directory browsing on misconfigured servers
 * readme_moodle.txt - this file ;-)

Notes:
 * 2023-05-19 To make Moodle 4.2 compatible with PHP 8.2 regarding the deprecation of dynamic properties,
   we made several changes from version 5.22.5 (https://github.com/adodb/adodb/compare/v5.22.4...v5.22.5) and
   then we applied it to Moodle 4.2.
   Below are the commit that has been applied:
   1. https://github.com/ADOdb/ADOdb/commit/8e51a88d5d37e2857298f5fa9c6f56f9b577e86f
   2. https://github.com/ADOdb/ADOdb/commit/ff2cefe7116ca29b0dee003af6fd5a8cb831c036
   3. https://github.com/ADOdb/ADOdb/commit/e475b4c610f6ee9cd103ac395ccf562e9b151b93
   4. https://github.com/ADOdb/ADOdb/commit/5766f6b17aac1da80302840416e24ac9a341742c
   5. https://github.com/ADOdb/ADOdb/commit/f52cf8a68cb6e0b702c8e7a6a8fa5da3e59ad13a
   6. https://github.com/ADOdb/ADOdb/commit/f7b91c2a45e2c89868894f73a65a096fbc4c1a0f
   Some of the commits added properties and removed some codes to avoid adding new properties.
 * 2023-09-28 Added #[AllowDynamicProperties] above the ADOFetchObj class.
