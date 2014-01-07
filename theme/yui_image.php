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
 * This file is responsible for serving of yui images
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
    $path = ltrim($slashargument, '/');
} else {
    $path = min_optional_param('file', '', 'SAFEPATH');
}

$etag = sha1($path);
$parts = explode('/', $path);
$version = array_shift($parts);
if ($version == 'moodle' && count($parts) >= 3) {
    if (!defined('ABORT_AFTER_CONFIG_CANCEL')) {
        define('ABORT_AFTER_CONFIG_CANCEL', true);
        define('NO_UPGRADE_CHECK', true);
        define('NO_MOODLE_COOKIES', true);
        require($CFG->libdir.'/setup.php');
    }
    $frankenstyle = array_shift($parts);
    $module = array_shift($parts);
    $image = array_pop($parts);
    $subdir = join('/', $parts);
    $dir = get_component_directory($frankenstyle);

    // For shifted YUI modules, we need the YUI module name in frankenstyle format.
    $frankenstylemodulename = join('-', array($version, $frankenstyle, $module));

    // By default, try and use the /yui/build directory.
    $imagepath = $dir . '/yui/build/' . $frankenstylemodulename . '/assets/skins/sam/' . $image;

    // If the shifted versions don't exist, fall back to the non-shifted file.
    if (!file_exists($imagepath) or !is_file($imagepath)) {
        $imagepath = $dir . '/yui/' . $module . '/assets/skins/sam/' . $image;
    }
} else if ($version == 'gallery' && count($parts)==3) {
    list($module, $version, $image) = $parts;
    $imagepath = "$CFG->dirroot/lib/yui/gallery/$module/$version/assets/skins/sam/$image";
} else if (count($parts) == 1 && ($version == $CFG->yui3version || $version == $CFG->yui2version)) {
    list($image) = $parts;
    if ($version == $CFG->yui3version) {
        $imagepath = "$CFG->dirroot/lib/yuilib/$CFG->yui3version/build/assets/skins/sam/$image";
    } else  {
        $imagepath = "$CFG->dirroot/lib/yuilib/2in3/$CFG->yui2version/build/assets/skins/sam/$image";
    }
} else {
    yui_image_not_found();
}

if (!file_exists($imagepath)) {
    yui_image_not_found();
}

$pathinfo = pathinfo($imagepath);
$imagename = $pathinfo['filename'].'.'.$pathinfo['extension'];

switch($pathinfo['extension']) {
    case 'gif'  : $mimetype = 'image/gif'; break;
    case 'png'  : $mimetype = 'image/png'; break;
    case 'jpg'  : $mimetype = 'image/jpeg'; break;
    case 'jpeg' : $mimetype = 'image/jpeg'; break;
    case 'ico'  : $mimetype = 'image/vnd.microsoft.icon'; break;
    default: $mimetype = 'document/unknown';
}

// if they are requesting a revision that's not -1, and they have supplied an
// If-Modified-Since header, we can send back a 304 Not Modified since the
// content never changes (the rev number is increased any time the content changes)
if (strpos($path, '/-1/') === false and (!empty($_SERVER['HTTP_IF_NONE_MATCH']) || !empty($_SERVER['HTTP_IF_MODIFIED_SINCE']))) {
    $lifetime = 60*60*24*360; // 1 year, we do not change YUI versions often, there are a few custom yui modules
    header('HTTP/1.1 304 Not Modified');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', filemtime($imagepath)) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
    header('Cache-Control: public, max-age='.$lifetime.', no-transform');
    header('Content-Type: '.$mimetype);
    header('Etag: "'.$etag.'"');
    die;
}

yui_image_cached($imagepath, $imagename, $mimetype, $etag);


function yui_image_cached($imagepath, $imagename, $mimetype, $etag) {
    global $CFG;
    require("$CFG->dirroot/lib/xsendfilelib.php");

    $lifetime = 60*60*24*360; // 1 year, we do not change YUI versions often, there are a few custom yui modules

    header('Content-Disposition: inline; filename="'.$imagename.'"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', filemtime($imagepath)) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
    header('Pragma: ');
    header('Cache-Control: public, max-age=315360000, no-transform');
    header('Accept-Ranges: none');
    header('Content-Type: '.$mimetype);
    header('Content-Length: '.filesize($imagepath));
    header('Etag: "'.$etag.'"');

    if (xsendfile($imagepath)) {
        die;
    }

    // no need to gzip already compressed images ;-)

    readfile($imagepath);
    die;
}

function yui_image_not_found() {
    header('HTTP/1.0 404 not found');
    die('Image was not found, sorry.');
}
