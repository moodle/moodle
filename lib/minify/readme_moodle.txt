Description of Minify 2.1.7 import into Moodle

Notes:
 * Do not use anything from /lib/minify/ directly, always use core_minify::*() methods.
 * In 2.7dev we will import only the minimal number of files required by new core_minify class
   and delete deprecated js_minify() and css_minify_css().

Changes:
 * Removed index.php - Is an unused entry point program and could potentially
   pose a security risk in the future.
 * Removed /builder/* - Not needed
 * Removed .htaccess - Not needed
 * Changed config.php - added moodle specific settings
 * Removed lib/JSMin.php which is not GNU GPL compatible.
