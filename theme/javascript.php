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
 * @package   moodlecore
 * @copyright 2009 Petr Skoda (skodak)  {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


// we need just the values from config.php and minlib.php
define('ABORT_AFTER_CONFIG', true);
require('../config.php'); // this stops immediately at the beginning of lib/setup.php

$themename = min_optional_param('theme', 'standard', 'SAFEDIR');
$rev       = min_optional_param('rev', 0, 'INT');
$type      = min_optional_param('type', 'head', 'RAW');

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

$candidate = "$CFG->dataroot/cache/theme/$themename/javascript_$type.js";

if ($rev > -1 and file_exists($candidate)) {
    if (!empty($_SERVER['HTTP_IF_NONE_MATCH'])) {
        // we do not actually need to verify the etag value because our files
        // never change in cache because we increment the rev parameter
        header('HTTP/1.1 304 Not Modified');
        die;
    }
    send_cached_js($candidate, $rev);
}

//=================================================================================
// ok, now we need to start normal moodle script, we need to load all libs and $DB
define('ABORT_AFTER_CONFIG_CANCEL', true);

define('NO_MOODLE_COOKIES', true); // Session not used here
define('NO_UPGRADE_CHECK', true);  // Ignore upgrade check

require("$CFG->dirroot/lib/setup.php");
// setup include path
set_include_path($CFG->libdir . '/minify/lib' . PATH_SEPARATOR . get_include_path());
require_once('Minify.php');

$theme = theme_config::load($themename);

if ($rev > -1) {
    check_dir_exists(dirname($candidate));
    $fp = fopen($candidate, 'w');
    fwrite($fp, minify($theme->javascript_files($type)));
    fclose($fp);
    send_cached_js($candidate);
} else {
    send_uncached_js($theme->javascript_content($type));
}

//=================================================================================
//=== utility functions ==
// we are not using filelib because we need to fine tune all header
// parameters to get the best performance.

function send_cached_js($jspath) {
    $lifetime = 60*60*24*20;

    header('Content-Disposition: inline; filename="javascript.php"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', filemtime($jspath)) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
    header('Pragma: ');
    header('Accept-Ranges: none');
    header('Content-Type: application/javascript; charset=utf-8');
    if (!min_enable_zlib_compression()) {
        header('Content-Length: '.filesize($jspath));
    }

    readfile($jspath);
    die;
}

function send_uncached_js($js) {
    header('Content-Disposition: inline; filename="javascript.php"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + 2) .' GMT');
    header('Pragma: ');
    header('Accept-Ranges: none');
    header('Content-Type: application/javascript; charset=utf-8');
    header('Content-Length: '.strlen($js));

    echo $js;
    die;
}

function minify($files) {
    if (0 === stripos(PHP_OS, 'win')) {
        Minify::setDocRoot(); // IIS may need help
    }
    // disable all caching, we do it in moodle
    Minify::setCache(null, false);

    $options = array(
        'bubbleCssImports' => false,
        // Don't gzip content we just want text for storage
        'encodeOutput' => false,
        // Maximum age to cache, not used but required
        'maxAge' => 1800,
        // The files to minify
        'files' => $files,
        // Turn orr URI rewriting
        'rewriteCssUris' => false,
        // This returns the CSS rather than echoing it for display
        'quiet' => true
    );

    $result = Minify::serve('Files', $options);
    return $result['content'];
}
