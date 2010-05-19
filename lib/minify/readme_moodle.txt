Description of Minify 2.1.3 import into Moodle

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
 * Removed .htaccess - Not needed
 * Changed config.php - Changed settings to Moodle specific settings incase this
   ever gets accidentally used.
