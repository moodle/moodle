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
 * This file is serving optimised JS
 *
 * @package    core_lib
 * @copyright  2010 Petr Skoda (skodak)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
    if (substr_count($slashargument, '/') < 1) {
        header('HTTP/1.0 404 not found');
        die('Slash argument must contain both a revision and a file path');
    }
    // image must be last because it may contain "/"
    list($rev, $file) = explode('/', $slashargument, 2);
    $rev  = min_clean_param($rev, 'INT');
    $file = '/'.min_clean_param($file, 'SAFEPATH');

} else {
    $rev  = min_optional_param('rev', -1, 'INT');
    $file = min_optional_param('jsfile', '', 'RAW'); // 'file' would collide with URL rewriting!
}

if (!min_is_revision_valid_and_current($rev)) {
    // If the rev is invalid, normalise it to -1 to disable all caching.
    $rev = -1;
}

// some security first - pick only files with .js extension in dirroot
$jsfiles = array();
$files = explode(',', $file);
foreach ($files as $fsfile) {
    $jsfile = realpath($CFG->dirroot.$fsfile);
    if ($jsfile === false) {
        // does not exist
        continue;
    }
    if ($CFG->dirroot === '/') {
        // Some shared hosting sites serve files directly from '/',
        // this is NOT supported, but at least allow JS when showing
        // errors and warnings.
    } else if (strpos($jsfile, $CFG->dirroot . DIRECTORY_SEPARATOR) !== 0) {
        // hackers - not in dirroot
        continue;
    }
    if (substr($jsfile, -3) !== '.js') {
        // hackers - not a JS file
        continue;
    }
    $jsfiles[] = $jsfile;
}

if (!$jsfiles) {
    // bad luck - no valid files
    header('HTTP/1.0 404 not found');
    die('No valid javascript files found');
}

$etag = sha1($rev.implode(',', $jsfiles));

if ($rev > 0) {
    $candidate = $CFG->localcachedir.'/js/'.$etag;

    if (file_exists($candidate)) {
        if (!empty($_SERVER['HTTP_IF_NONE_MATCH']) || !empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            // we do not actually need to verify the etag value because our files
            // never change in cache because we increment the rev parameter
            js_send_unmodified(filemtime($candidate), $etag);
        }
        js_send_cached($candidate, $etag);

    } else {
        // The JS needs minfifying, so we're gonna have to load our full Moodle
        // environment to process it..
        define('ABORT_AFTER_CONFIG_CANCEL', true);

        define('NO_MOODLE_COOKIES', true); // Session not used here.
        define('NO_UPGRADE_CHECK', true);  // Ignore upgrade check.

        require("$CFG->dirroot/lib/setup.php");

        js_write_cache_file_content($candidate, core_minify::js_files($jsfiles));
        // verify nothing failed in cache file creation
        clearstatcache();
        if (file_exists($candidate)) {
            js_send_cached($candidate, $etag);
        }
    }
}

$content = '';
foreach ($jsfiles as $jsfile) {
    $content .= file_get_contents($jsfile)."\n";
}
js_send_uncached($content);
