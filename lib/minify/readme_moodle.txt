Description of Minify 2.1.7 import into Moodle

Notes:
 * Do not use things within minify/lib/*

Usage:
 * js_minify() from /lib/jslib.php
 * css_minify_css() from /lib/csslib.php

Changes:
 * Removed index.php - Is an unused entry point program and could potentially
   pose a security risk in the future.
 * Removed /builder/* - Not needed
 * Removed .htaccess - Not needed
 * Changed config.php - added moodle specific settings
 * Removed lib/JSMin.php which is not GNU GPL compatible.
