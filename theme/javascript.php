<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file is responsible for serving the one huge CSS of each theme.
 *
 * @package   core
 * @copyright 2009 Petr Skoda (skodak)  {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


// disable moodle specific debug messages and any errors in output,
// comment out when debugging or better look into error log!
define('NO_DEBUG_DISPLAY', true);

// we need just the values from config.php and minlib.php
define('ABORT_AFTER_CONFIG', true);
require('../config.php'); // this stops immediately at the beginning of lib/setup.php
require_once("$CFG->dirroot/lib/jslib.php");

if ($slashargument = min_get_slash_argument()) {
    $slashargument = ltrim($slashargument, '/');
    if (substr_count($slashargument, '/') < 2) {
        header('HTTP/1.0 404 not found');
        die('Slash argument must contain both a revision and a file path');
    }
    // image must be last because it may contain "/"
    list($themename, $rev, $type) = explode('/', $slashargument, 3);
    $themename = min_clean_param($themename, 'SAFEDIR');
    $rev       = min_clean_param($rev, 'INT');
    $type      = min_clean_param($type, 'SAFEDIR');

} else {
    $themename = min_optional_param('theme', 'standard', 'SAFEDIR');
    $rev       = min_optional_param('rev', -1, 'INT');
    $type      = min_optional_param('type', 'head', 'RAW');
}

if ($type !== 'head' and $type !== 'footer') {
    header('HTTP/1.0 404 not found');
    die('Theme was not found, sorry.');
}

if (file_exists("$CFG->dirroot/theme/$themename/config.php")) {
    // exists
} else if (!empty($CFG->themedir) and file_exists("$CFG->themedir/$themename/config.php")) {
    // exists
} else {
    header('HTTP/1.0 404 not found');
    die('Theme was not found, sorry.');
}

$candidate = "$CFG->localcachedir/theme/$rev/$themename/javascript_$type.js";
$etag = sha1("$rev/$themename/$type");

if ($rev > 0 and file_exists($candidate)) {
    if (!empty($_SERVER['HTTP_IF_NONE_MATCH']) || !empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
        // we do not actually need to verify the etag value because our files
        // never change in cache because we increment the rev parameter
        js_send_unmodified(filemtime($candidate), $etag);
    }
    js_send_cached($candidate, $etag);
}

//=================================================================================
// ok, now we need to start normal moodle script, we need to load all libs and $DB
define('ABORT_AFTER_CONFIG_CANCEL', true);

define('NO_MOODLE_COOKIES', true); // Session not used here
define('NO_UPGRADE_CHECK', true);  // Ignore upgrade check

require("$CFG->dirroot/lib/setup.php");

$theme = theme_config::load($themename);
$themerev = theme_get_revision();

if ($themerev <= 0 or $rev != $themerev) {
    // Do not send caching headers if they do not request current revision,
    // we do not want to pollute browser caches with outdated JS.
    js_send_uncached($theme->javascript_content($type));
}

make_localcache_directory('theme', false);

js_write_cache_file_content($candidate, core_minify::js_files($theme->javascript_files($type)));
// Verify nothing failed in cache file creation.
clearstatcache();
if (file_exists($candidate)) {
    js_send_cached($candidate, $etag);
}

js_send_uncached($theme->javascript_content($type));
