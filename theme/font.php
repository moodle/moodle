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
 * This file is responsible for serving the fonts used in CSS.
 *
 * Note: it is recommended to use only WOFF2 (Web Open Font Format v2) fonts.
 *
 * @package   core
 * @copyright 2013 Petr Skoda (skodak)  {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Disable moodle specific debug messages and any errors in output,
// comment out when debugging or better look into error log!
define('NO_DEBUG_DISPLAY', true);

define('ABORT_AFTER_CONFIG', true);
require('../config.php');

if ($slashargument = min_get_slash_argument()) {
    $slashargument = ltrim($slashargument, '/');
    if (substr_count($slashargument, '/') < 3) {
        font_not_found();
    }
    list($themename, $component, $rev, $font) = explode('/', $slashargument, 4);
    $themename = min_clean_param($themename, 'SAFEDIR');
    $component = min_clean_param($component, 'SAFEDIR');
    $rev       = min_clean_param($rev, 'INT');
    $font      = min_clean_param($font, 'RAW');

} else {
    $themename = min_optional_param('theme', 'standard', 'SAFEDIR');
    $component = min_optional_param('component', 'core', 'SAFEDIR');
    $rev       = min_optional_param('rev', -1, 'INT');
    $font      = min_optional_param('font', '', 'RAW');
}

if (!$font) {
    font_not_found();
}

if ($to = strpos($font, '?')) {
    $font = substr($font, 0, $to);
}

if (empty($component) or $component === 'moodle' or $component === 'core') {
    $component = 'core';
}

if (preg_match('/^[a-z0-9_-]+\.woff2$/i', $font, $matches)) {
    $font = $matches[0];
    $mimetype = 'font/woff2';

} else if (preg_match('/^[a-z0-9_-]+\.woff$/i', $font, $matches)) {
    // This is the real standard!
    $font = $matches[0];
    $mimetype = 'font/woff';

} else if (preg_match('/^[a-z0-9_-]+\.ttf$/i', $font, $matches)) {
    $font = $matches[0];
    $mimetype = 'font/ttf';

} else if (preg_match('/^[a-z0-9_-]+\.otf$/i', $font, $matches)) {
    $font = $matches[0];
    $mimetype = 'font/otf';

} else if (preg_match('/^[a-z0-9_-]+\.eot$/i', $font, $matches)) {
    // IE8 must die!!!
    $font = $matches[0];
    $mimetype = 'application/vnd.ms-fontobject';
} else if (preg_match('/^[a-z0-9_-]+\.svg$/i', $font, $matches)) {
    $font = $matches[0];
    $mimetype = 'image/svg+xml';

} else {
    font_not_found();
}

if (file_exists("$CFG->dirroot/theme/$themename/config.php")) {
    // Normal theme exists.
} else if (!empty($CFG->themedir) and file_exists("$CFG->themedir/$themename/config.php")) {
    // Theme exists in alternative location.
} else {
    font_not_found();
}

$candidatelocation = "$CFG->localcachedir/theme/$rev/$themename/fonts/$component";
$etag = sha1("$rev/$themename/$component/$font");

if ($rev > 0) {
    if (file_exists("$candidatelocation/$font.error")) {
        font_not_found();
    }

    if (file_exists("$candidatelocation/$font")) {
        if (!empty($_SERVER['HTTP_IF_NONE_MATCH']) || !empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            // We do not actually need to verify the etag value because our files
            // never change in cache because we increment the rev parameter.
            // 90 days only - based on Moodle point release cadence being every 3 months.
            $lifetime = 60 * 60 * 24 * 90;
            header('HTTP/1.1 304 Not Modified');
            header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
            header('Cache-Control: public, max-age='.$lifetime);
            header('Content-Type: '.$mimetype);
            header('Etag: "'.$etag.'"');
            die;
        }
        send_cached_font("$candidatelocation/$font", $etag, $font, $mimetype);
    }
}

// Ok, now we need to start normal moodle script, we need to load all libs and $DB.
define('ABORT_AFTER_CONFIG_CANCEL', true);

define('NO_MOODLE_COOKIES', true); // Session not used here.
define('NO_UPGRADE_CHECK', true);  // Ignore upgrade check.

require("$CFG->dirroot/lib/setup.php");

$theme = theme_config::load($themename);
$themerev = theme_get_revision();

$fontfile = $theme->resolve_font_location($font, $component);

if ($themerev <= 0 or $rev != $themerev) {
    // Do not send caching headers if they do not request current revision,
    // we do not want to pollute browser caches with outdated fonts.
    if (empty($fontfile) or !is_readable($fontfile)) {
        font_not_found();
    }
    send_uncached_font($fontfile, $font, $mimetype);
}

make_localcache_directory('theme', false);

if (empty($fontfile) or !is_readable($fontfile)) {
    if (!file_exists($candidatelocation)) {
        @mkdir($candidatelocation, $CFG->directorypermissions, true);
    }
    // Make note we can not find this file.
    $cachefont = "$candidatelocation/$font.error";
    $fp = fopen($cachefont, 'w');
    fclose($fp);
    font_not_found();
}

$cachefont = cache_font($font, $fontfile, $candidatelocation);
if (connection_aborted()) {
    die;
}
// Make sure nothing failed.
clearstatcache();
if (file_exists($cachefont)) {
    send_cached_font($cachefont, $etag, $font, $mimetype);
}

send_uncached_font($fontfile, $font, $mimetype);



// Utility functions.

function send_cached_font($fontpath, $etag, $font, $mimetype) {
    global $CFG;
    require("$CFG->dirroot/lib/xsendfilelib.php");

    // 90 days only - based on Moodle point release cadence being every 3 months.
    $lifetime = 60 * 60 * 24 * 90;

    header('Etag: "'.$etag.'"');
    header('Content-Disposition: inline; filename="'.$font.'"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', filemtime($fontpath)) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
    header('Pragma: ');
    header('Cache-Control: public, max-age='.$lifetime.', immutable');
    header('Accept-Ranges: none');
    header('Content-Type: '.$mimetype);
    header('Content-Length: '.filesize($fontpath));

    if (xsendfile($fontpath)) {
        die;
    }

    // No need to gzip already compressed fonts.

    readfile($fontpath);
    die;
}

function send_uncached_font($fontpath, $font, $mimetype) {
    header('Content-Disposition: inline; filename="'.$font.'"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + 15) .' GMT');
    header('Pragma: ');
    header('Accept-Ranges: none');
    header('Content-Type: '.$mimetype);
    header('Content-Length: '.filesize($fontpath));

    readfile($fontpath);
    die;
}

function font_not_found() {
    header('HTTP/1.0 404 not found');
    die('font was not found, sorry.');
}

/**
 * Caches a given font file.
 *
 * @param string $font The name of the font that was requested.
 * @param string $fontfile The location of the font file we want to cache.
 * @param string $candidatelocation The location to cache it in.
 * @return string The path to the cached font.
 */
function cache_font($font, $fontfile, $candidatelocation) {
    global $CFG;
    $cachefont = "$candidatelocation/$font";

    clearstatcache();
    if (!file_exists($candidatelocation)) {
        @mkdir($candidatelocation, $CFG->directorypermissions, true);
    }

    // Prevent serving of incomplete file from concurrent request,
    // the rename() should be more atomic than copy().
    ignore_user_abort(true);
    if (@copy($fontfile, $cachefont.'.tmp')) {
        rename($cachefont.'.tmp', $cachefont);
        @chmod($cachefont, $CFG->filepermissions);
        @unlink($cachefont.'.tmp'); // Just in case anything fails.
    }
    return $cachefont;
}
