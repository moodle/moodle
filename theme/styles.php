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
$type      = min_optional_param('type', 'all', 'SAFEDIR');
$rev       = min_optional_param('rev', 0, 'INT');

if (!in_array($type, array('all', 'ie', 'editor', 'plugins', 'parents', 'theme'))) {
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

if ($type === 'ie') {
    send_ie_css($themename, $rev);
}

$candidatesheet = "$CFG->dataroot/cache/theme/$themename/css/$type.css";

if (file_exists($candidatesheet)) {
    if (!empty($_SERVER['HTTP_IF_NONE_MATCH'])) {
        // we do not actually need to verify the etag value because our files
        // never change in cache because we increment the rev parameter
        header('HTTP/1.1 304 Not Modified');
        die;
    }
    send_cached_css($candidatesheet, $rev);
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

if ($type === 'editor') {
    $files = $theme->editor_css_files();
    store_css($theme, $candidatesheet, $files);
} else {
    $css = $theme->css_files();
    $allfiles = array();
    foreach ($css as $key=>$value) {
        $cssfiles = array();
        foreach($value as $val) {
            if (is_array($val)) {
                foreach ($val as $k=>$v) {
                    $cssfiles[] = $v;
                }
            } else {
                $cssfiles[] = $val;
            }
        }
        $cssfile = "$CFG->dataroot/cache/theme/$themename/css/$key.css";
        store_css($theme, $cssfile, $cssfiles);
        $allfiles = array_merge($allfiles, $cssfiles);
    }
    $cssfile = "$CFG->dataroot/cache/theme/$themename/css/all.css";
    store_css($theme, $cssfile, $allfiles);
}
send_cached_css($candidatesheet, $rev);

//=================================================================================
//=== utility functions ==
// we are not using filelib because we need to fine tune all header
// parameters to get the best performance.

function store_css(theme_config $theme, $csspath, $cssfiles) {
    $css = $theme->post_process(minify($cssfiles));
    check_dir_exists(dirname($csspath));
    $fp = fopen($csspath, 'w');
    fwrite($fp, $css);
    fclose($fp);
}

function send_ie_css($themename, $rev) {
    $lifetime = 60*60*24*3;

    $css = <<<EOF
/** Unfortunately IE6/7 does not support more than 4096 selectors in one CSS file, which means we have to use some ugly hacks :-( **/
@import url(styles.php?theme=$themename&rev=$rev&type=plugins);
@import url(styles.php?theme=$themename&rev=$rev&type=parents);
@import url(styles.php?theme=$themename&rev=$rev&type=theme);

EOF;

    header('Etag: '.md5($rev));
    header('Content-Disposition: inline; filename="styles.php"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
    header('Pragma: ');
    header('Accept-Ranges: none');
    header('Content-Type: text/css; charset=utf-8');
    header('Content-Length: '.strlen($css));

    echo $css;
    die;
}

function send_cached_css($csspath, $rev) {
    $lifetime = 60*60*24*20;

    header('Content-Disposition: inline; filename="styles.php"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', filemtime($csspath)) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
    header('Pragma: ');
    header('Accept-Ranges: none');
    header('Content-Type: text/css; charset=utf-8');
    if (!min_enable_zlib_compression()) {
        header('Content-Length: '.filesize($csspath));
    }

    readfile($csspath);
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
        'maxAge' => (60*60*24*20),
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
