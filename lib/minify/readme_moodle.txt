Description of Minify 2.1.5 import into Moodle

Notes:
 * Uses are required to add minify/lib to the include path
 * We ever actually use things within minify/lib/*

Usage:
 * /lib/javascript.php
 * /theme/javascript.php
 * /theme/styles.php

Changes:
 * Removed index.php - Is an unused entry point program and could potentially
   pose a security risk in the future.
 * Removed /builder/* - Not needed
 * Removed .htaccess - Not needed
 * Changed config.php - added moodle specific settings
 * Removed lib/JSMin.php which is not GNU GPL compatible.
