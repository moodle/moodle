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
 * jQuery serving script.
 *
 * Do not include jQuery scripts or CSS directly, always use
 * $PAGE->requires->jquery() or $PAGE->requires->jquery_plugin('xx', 'yy').
 *
 * @package    core
 * @copyright  2013 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Disable moodle specific debug messages and any errors in output,
// comment out when debugging or better look into error log!
define('NO_DEBUG_DISPLAY', true);

// We need just the values from config.php and minlib.php.
define('ABORT_AFTER_CONFIG', true);
require('../config.php'); // This stops immediately at the beginning of lib/setup.php.

if ($slashargument = min_get_slash_argument()) {
    $path = ltrim($slashargument, '/');
} else {
    $path = min_optional_param('file', '', 'SAFEPATH');
    $path = ltrim($path, '/');
}

if (strpos($path, '/') === false) {
    jquery_file_not_found();
}

list($component, $path) = explode('/', $path, 2);

if (empty($path) or empty($component)) {
    jquery_file_not_found();
}

// Find the jQuery dir for this component.
if ($component === 'core') {
    $componentdir = "$CFG->dirroot/lib";

} else if (strpos($component, 'theme_')) {
    if (!empty($CFG->themedir)) {
        $componentdir = "$CFG->themedir/$component";
    } else {
        $componentdir = "$CFG->dirroot/theme/$component";
    }

} else {
    $componentdir = core_component::get_component_directory($component);
}

if (!file_exists($componentdir) or !file_exists("$componentdir/jquery/plugins.php")) {
    jquery_file_not_found();
}

$file = realpath("$componentdir/jquery/$path");

if (!$file or is_dir($file)) {
    jquery_file_not_found();
}

$etag = sha1("$component/$path");
$lifetime = 60*60*24*120; // 120 days should be enough.
$pathinfo = pathinfo($path);

if (empty($pathinfo['extension'])) {
    jquery_file_not_found();
}

$filename = $pathinfo['filename'].'.'.$pathinfo['extension'];

switch($pathinfo['extension']) {
    case 'gif'  : $mimetype = 'image/gif';
        break;
    case 'png'  : $mimetype = 'image/png';
        break;
    case 'jpg'  : $mimetype = 'image/jpeg';
        break;
    case 'jpeg' : $mimetype = 'image/jpeg';
        break;
    case 'ico'  : $mimetype = 'image/vnd.microsoft.icon';
        break;
    case 'svg'  : $mimetype = 'image/svg+xml';
        break;
    case 'js'   : $mimetype = 'application/javascript';
        break;
    case 'css'  : $mimetype = 'text/css';
        break;
    case 'php'  : jquery_file_not_found();
        break;
    default     : $mimetype = 'document/unknown';
}

if (!empty($_SERVER['HTTP_IF_NONE_MATCH']) || !empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
    // We do not actually need to verify the etag value because these files
    // never change, devs need to change file names on update!
    header('HTTP/1.1 304 Not Modified');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
    header('Cache-Control: public, max-age='.$lifetime);
    header('Content-Type: '.$mimetype);
    header('Etag: "'.$etag.'"');
    die;
}

require_once("$CFG->dirroot/lib/xsendfilelib.php");

header('Etag: "'.$etag.'"');
header('Content-Disposition: inline; filename="'.$filename.'"');
header('Last-Modified: '. gmdate('D, d M Y H:i:s', filemtime($file)) .' GMT');
header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
header('Pragma: ');
header('Cache-Control: public, max-age='.$lifetime);
header('Accept-Ranges: none');
header('Content-Type: '.$mimetype);

if (xsendfile($file)) {
    die;
}

if ($mimetype === 'text/css' or $mimetype === 'application/javascript') {
    if (!min_enable_zlib_compression()) {
        header('Content-Length: '.filesize($file));
    }
} else {
    // No need to compress images.
    header('Content-Length: '.filesize($file));
}

readfile($file);
die;



function jquery_file_not_found() {
    // Note: we can not disclose the exact file path here, sorry.
    header('HTTP/1.0 404 not found');
    die('File was not found, sorry.');
}
