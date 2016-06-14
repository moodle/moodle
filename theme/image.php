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
 * This file is responsible for serving the one theme and plugin images.
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

if ($slashargument = min_get_slash_argument()) {
    $slashargument = ltrim($slashargument, '/');
    if (substr_count($slashargument, '/') < 3) {
        image_not_found();
    }
    if (strpos($slashargument, '_s/') === 0) {
        // Can't use SVG
        $slashargument = substr($slashargument, 3);
        $usesvg = false;
    } else {
        $usesvg = true;
    }
    // image must be last because it may contain "/"
    list($themename, $component, $rev, $image) = explode('/', $slashargument, 4);
    $themename = min_clean_param($themename, 'SAFEDIR');
    $component = min_clean_param($component, 'SAFEDIR');
    $rev       = min_clean_param($rev, 'INT');
    $image     = min_clean_param($image, 'SAFEPATH');

} else {
    $themename = min_optional_param('theme', 'standard', 'SAFEDIR');
    $component = min_optional_param('component', 'core', 'SAFEDIR');
    $rev       = min_optional_param('rev', -1, 'INT');
    $image     = min_optional_param('image', '', 'SAFEPATH');
    $usesvg    = (bool)min_optional_param('svg', '1', 'INT');
}

if (empty($component) or $component === 'moodle' or $component === 'core') {
    $component = 'core';
}

if (empty($image)) {
    image_not_found();
}

if (file_exists("$CFG->dirroot/theme/$themename/config.php")) {
    // exists
} else if (!empty($CFG->themedir) and file_exists("$CFG->themedir/$themename/config.php")) {
    // exists
} else {
    image_not_found();
}

$candidatelocation = "$CFG->localcachedir/theme/$rev/$themename/pix/$component";
$etag = sha1("$rev/$themename/$component/$image");

if ($rev > 0) {
    if (file_exists("$candidatelocation/$image.error")) {
        // This is a major speedup if there are multiple missing images,
        // the only problem is that random requests may pollute our cache.
        image_not_found();
    }
    $cacheimage = false;
    if ($usesvg && file_exists("$candidatelocation/$image.svg")) {
        $cacheimage = "$candidatelocation/$image.svg";
        $ext = 'svg';
    } else if (file_exists("$candidatelocation/$image.png")) {
        $cacheimage = "$candidatelocation/$image.png";
        $ext = 'png';
    } else if (file_exists("$candidatelocation/$image.gif")) {
        $cacheimage = "$candidatelocation/$image.gif";
        $ext = 'gif';
    } else if (file_exists("$candidatelocation/$image.jpg")) {
        $cacheimage = "$candidatelocation/$image.jpg";
        $ext = 'jpg';
    } else if (file_exists("$candidatelocation/$image.jpeg")) {
        $cacheimage = "$candidatelocation/$image.jpeg";
        $ext = 'jpeg';
    } else if (file_exists("$candidatelocation/$image.ico")) {
        $cacheimage = "$candidatelocation/$image.ico";
        $ext = 'ico';
    }
    if ($cacheimage) {
        if (!empty($_SERVER['HTTP_IF_NONE_MATCH']) || !empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            // we do not actually need to verify the etag value because our files
            // never change in cache because we increment the rev parameter
            $lifetime = 60*60*24*60; // 60 days only - the revision may get incremented quite often
            $mimetype = get_contenttype_from_ext($ext);
            header('HTTP/1.1 304 Not Modified');
            header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
            header('Cache-Control: public, max-age='.$lifetime.', no-transform');
            header('Content-Type: '.$mimetype);
            header('Etag: "'.$etag.'"');
            die;
        }
        send_cached_image($cacheimage, $etag);
    }
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
    // we do not want to pollute browser caches with outdated images.
    $imagefile = $theme->resolve_image_location($image, $component, $usesvg);
    if (empty($imagefile) or !is_readable($imagefile)) {
        image_not_found();
    }
    send_uncached_image($imagefile);
}

make_localcache_directory('theme', false);

// At this stage caching is enabled, and either:
// * we have no cached copy of the image in any format (either SVG, or non-SVG); or
// * we have a cached copy of the SVG, but the non-SVG was requested by the browser.
//
// Because of the way in which the cache return code works above:
// * if we are allowed to return SVG, we do not need to cache the non-SVG version; however
// * if the browser has requested the non-SVG version, we *must* cache _both_ the SVG, and the non-SVG versions.

// First get all copies - including, potentially, the SVG version.
$imagefile = $theme->resolve_image_location($image, $component, true);

if (empty($imagefile) || !is_readable($imagefile)) {
    // Unable to find a copy of the image file in any format.
    // We write a .error file for the image now - this will be used above when searching for cached copies to prevent
    // trying to find the image in the future.
    if (!file_exists($candidatelocation)) {
        @mkdir($candidatelocation, $CFG->directorypermissions, true);
    }
    // Make note we can not find this file.
    $cacheimage = "$candidatelocation/$image.error";
    $fp = fopen($cacheimage, 'w');
    fclose($fp);
    image_not_found();
}

// The image was found, and it is readable.
$pathinfo = pathinfo($imagefile);

// Attempt to cache it if necessary.
// We don't really want to overwrite any existing cache items just for the sake of it.
$cacheimage = "$candidatelocation/$image.{$pathinfo['extension']}";
if (!file_exists($cacheimage)) {
    // We don't already hold a cached copy of this image. Cache it now.
    $cacheimage = cache_image($image, $imagefile, $candidatelocation);
}

if (!$usesvg && $pathinfo['extension'] === 'svg') {
    // The browser has requested that a non-SVG version be returned.
    // The version found so far is the SVG version - try and find the non-SVG version.
    $imagefile = $theme->resolve_image_location($image, $component, false);
    if (empty($imagefile) || !is_readable($imagefile)) {
        // A non-SVG file could not be found at all.
        // The browser has requested a non-SVG version, so we must return image_not_found().
        // We must *not* write an .error file because the SVG is available.
        image_not_found();
    }

    // An non-SVG version of image was found - cache it.
    // This will be used below in the image serving code.
    $cacheimage = cache_image($image, $imagefile, $candidatelocation);
}

if (connection_aborted()) {
    // Request was cancelled - do not send anything.
    die;
}

// Make sure nothing failed.
clearstatcache();
if (file_exists($cacheimage)) {
    // The cached copy was found, and is accessible. Serve it.
    send_cached_image($cacheimage, $etag);
}

send_uncached_image($imagefile);

//=================================================================================
//=== utility functions ==
// we are not using filelib because we need to fine tune all header
// parameters to get the best performance.

function send_cached_image($imagepath, $etag) {
    global $CFG;
    require("$CFG->dirroot/lib/xsendfilelib.php");

    $lifetime = 60*60*24*60; // 60 days only - the revision may get incremented quite often
    $pathinfo = pathinfo($imagepath);
    $imagename = $pathinfo['filename'].'.'.$pathinfo['extension'];

    $mimetype = get_contenttype_from_ext($pathinfo['extension']);

    header('Etag: "'.$etag.'"');
    header('Content-Disposition: inline; filename="'.$imagename.'"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', filemtime($imagepath)) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
    header('Pragma: ');
    header('Cache-Control: public, max-age='.$lifetime.', no-transform');
    header('Accept-Ranges: none');
    header('Content-Type: '.$mimetype);
    header('Content-Length: '.filesize($imagepath));

    if (xsendfile($imagepath)) {
        die;
    }

    // no need to gzip already compressed images ;-)

    readfile($imagepath);
    die;
}

function send_uncached_image($imagepath) {
    $pathinfo = pathinfo($imagepath);
    $imagename = $pathinfo['filename'].'.'.$pathinfo['extension'];

    $mimetype = get_contenttype_from_ext($pathinfo['extension']);

    header('Content-Disposition: inline; filename="'.$imagename.'"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + 15) .' GMT');
    header('Pragma: ');
    header('Accept-Ranges: none');
    header('Content-Type: '.$mimetype);
    header('Content-Length: '.filesize($imagepath));

    readfile($imagepath);
    die;
}

function image_not_found() {
    header('HTTP/1.0 404 not found');
    die('Image was not found, sorry.');
}

function get_contenttype_from_ext($ext) {
    switch ($ext) {
        case 'svg':
            return 'image/svg+xml';
        case 'png':
            return 'image/png';
        case 'gif':
            return 'image/gif';
        case 'jpg':
        case 'jpeg':
            return 'image/jpeg';
        case 'ico':
            return 'image/vnd.microsoft.icon';
    }
    return 'document/unknown';
}

/**
 * Caches a given image file.
 *
 * @param string $image The name of the image that was requested.
 * @param string $imagefile The location of the image file we want to cache.
 * @param string $candidatelocation The location to cache it in.
 * @return string The path to the cached image.
 */
function cache_image($image, $imagefile, $candidatelocation) {
    global $CFG;
    $pathinfo = pathinfo($imagefile);
    $cacheimage = "$candidatelocation/$image.".$pathinfo['extension'];

    clearstatcache();
    if (!file_exists(dirname($cacheimage))) {
        @mkdir(dirname($cacheimage), $CFG->directorypermissions, true);
    }

    // Prevent serving of incomplete file from concurrent request,
    // the rename() should be more atomic than copy().
    ignore_user_abort(true);
    if (@copy($imagefile, $cacheimage.'.tmp')) {
        rename($cacheimage.'.tmp', $cacheimage);
        @chmod($cacheimage, $CFG->filepermissions);
        @unlink($cacheimage.'.tmp'); // just in case anything fails
    }
    return $cacheimage;
}
