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
 * This file is serving the javascript source map.
 *
 * @package    core
 * @copyright  2019 Ryan Wyllie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Disable moodle specific debug messages and any errors in output,
// comment out when debugging or better look into error log!
define('NO_DEBUG_DISPLAY', true);

// We need just the values from config.php and minlib.php.
define('ABORT_AFTER_CONFIG', true);
require('../config.php'); // This stops immediately at the beginning of lib/setup.php.
require_once("$CFG->dirroot/lib/classes/requirejs.php");

$slashargument = min_get_slash_argument();
if (!$slashargument) {
    // The above call to min_get_slash_argument should always work.
    die('Invalid request');
}

$slashargument = ltrim($slashargument, '/');
// Split into revision and module name.
[$file] = explode('/', $slashargument, 1);
$file = '/' . min_clean_param($file, 'SAFEPATH');

// Only load js files from the js modules folder from the components.
[$unused, $component, $module] = explode('/', $file, 3);

// When running a lazy load, we only deal with one file so we can just return the working sourcemap.
$jsfiles = core_requirejs::find_one_amd_module($component, $module);
$jsfile = reset($jsfiles);

$mapfile = $jsfile . '.map';

if (file_exists($mapfile)) {
    $mapdata = file_get_contents($mapfile);
    $mapdata = json_decode($mapdata, true);

    $shortfilename = str_replace($CFG->dirroot, '', $jsfile);
    $srcfilename = str_replace('/amd/build/', '/amd/src/', $shortfilename);
    $srcfilename = str_replace('.min.js', '.js', $srcfilename);
    $fullsrcfilename = $CFG->wwwroot . $srcfilename;
    $mapdata['sources'][0] = $fullsrcfilename;

    echo json_encode($mapdata);
} else {
    // If there is no source map file, then we will not generate one for you, sorry.
    header('HTTP/1.0 404 not found');
}
