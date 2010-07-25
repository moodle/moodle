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
 * @package   moodlecore
 * @copyright 2009 Petr Skoda (skodak)  {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


// we need just the values from config.php and minlib.php
define('ABORT_AFTER_CONFIG', true);
require('../config.php'); // this stops immediately at the beginning of lib/setup.php

$path = min_optional_param('file', '', 'SAFEPATH');

$parts = explode('/', $path);
$version = array_shift($parts);

if ($version == 'moodle' && count($parts) >= 3) {
    //TODO: this is a ugly hack because we should not load any libs here!
    define('MOODLE_INTERNAL', true);
    require_once($CFG->libdir.'/moodlelib.php');
    $frankenstyle = array_shift($parts);
    $module = array_shift($parts);
    $image = array_pop($parts);
    $subdir = join('/', $parts);
    $dir = get_component_directory($frankenstyle);
    $imagepath = $dir.'/yui/'.$module.'/assets/skins/sam/'.$image;
} else if ($version == 'gallery' && count($parts)==3) {
    list($module, $version, $image) = $parts;
    $imagepath = "$CFG->dirroot/lib/yui/gallery/$module/$version/assets/skins/sam/$image";
} else if (count($parts) == 1 && ($version == $CFG->yui3version || $version == $CFG->yui2version)) {
    list($image) = $parts;
    if ($version == $CFG->yui3version) {
        $imagepath = "$CFG->dirroot/lib/yui/$CFG->yui3version/build/assets/skins/sam/$image";
    } else  {
        $imagepath = "$CFG->dirroot/lib/yui/$CFG->yui2version/build/assets/skins/sam/$image";
    }
} else {
    yui_image_not_found();
}

if (!file_exists($imagepath)) {
    yui_image_not_found();
}

yui_image_cached($imagepath);



function yui_image_cached($imagepath) {
    $lifetime = 60*60*24*300; // 300 days === forever
    $pathinfo = pathinfo($imagepath);
    $imagename = $pathinfo['filename'].'.'.$pathinfo['extension'];

    switch($pathinfo['extension']) {
        case 'gif' : $mimetype = 'image/gif'; break;
        case 'png' : $mimetype = 'image/png'; break;
        case 'jpg' : $mimetype = 'image/jpeg'; break;
        case 'jpeg' : $mimetype = 'image/jpeg'; break;
        case 'ico' : $mimetype = 'image/vnd.microsoft.icon'; break;
        default: $mimetype = 'document/unknown';
    }

    header('Content-Disposition: inline; filename="'.$imagename.'"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', filemtime($imagepath)) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
    header('Pragma: ');
    header('Cache-Control: max-age=315360000');
    header('Accept-Ranges: none');
    header('Content-Type: '.$mimetype);
    header('Content-Length: '.filesize($imagepath));

    // no need to gzip already compressed images ;-)

    readfile($imagepath);
    die;
}

function yui_image_not_found() {
    header('HTTP/1.0 404 not found');
    die('Image was not found, sorry.');
}
