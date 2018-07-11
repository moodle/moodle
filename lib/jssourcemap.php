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
require_once("$CFG->dirroot/lib/jslib.php");
require_once("$CFG->dirroot/lib/classes/requirejs.php");

$slashargument = min_get_slash_argument();
if (!$slashargument) {
    // The above call to min_get_slash_argument should always work.
    die('Invalid request');
}

$slashargument = ltrim($slashargument, '/');
if (substr_count($slashargument, '/') < 1) {
    header('HTTP/1.0 404 not found');
    die('Slash argument must contain both a revision and a file path');
}
// Split into revision and module name.
list($rev, $file) = explode('/', $slashargument, 2);
$rev  = min_clean_param($rev, 'INT');
$file = '/' . min_clean_param($file, 'SAFEPATH');

// Only load js files from the js modules folder from the components.
$jsfiles = array();
list($unused, $component, $module) = explode('/', $file, 3);

// No subdirs allowed - only flat module structure please.
if (strpos('/', $module) !== false) {
    die('Invalid module');
}

// Some (huge) modules are better loaded lazily (when they are used). If we are requesting
// one of these modules, only return the one module, not the combo.
$lazysuffix = "-lazy.js";
$lazyload = (strpos($module, $lazysuffix) !== false);

if ($lazyload) {
    $jsfiles = core_requirejs::find_one_amd_module($component, $module, false);
} else {
    $jsfiles = core_requirejs::find_all_amd_modules(false);
}

// Create the empty source map.
$map = [
    'version' => 3,
    'file' => $CFG->wwwroot . '/lib/requirejs.php/' . $slashargument,
    'sections' => []
];

$line = 0;
// Sort the files to ensure consistent ordering for source map generation.
asort($jsfiles);

foreach ($jsfiles as $modulename => $jsfile) {
    $shortfilename = str_replace($CFG->dirroot, '', $jsfile);
    $srcfilename = str_replace('/amd/build/', '/amd/src/', $shortfilename);
    $srcfilename = str_replace('.min.js', '.js', $srcfilename);

    $mapfile = $jsfile . '.map';
    if (file_exists($mapfile)) {
        $mapdata = file_get_contents($mapfile);
        $mapdata = json_decode($mapdata, true);
        unset($mapdata['sourcesContent']);
        $mapdata['sources'][0] = $CFG->wwwroot . $srcfilename;

        $map['sections'][] = [
            'offset' => [
                'line' => $line,
                'column' => 0
            ],
            'map' => $mapdata
        ];

        $js = file_get_contents($jsfile);
        // Remove source map link.
        $js = preg_replace('~//# sourceMappingURL.*$~s', '', $js);
    } else {
        // No sourcemap for this section which means we will have returned the original
        // source file to the browser. We have to provide an empty source map to
        // ensure that this section is not treated as part of the previous map.
        $map['sections'][] = [
            'offset' => [
                'line' => $line,
                'column' => 0,
            ],
            'map' => [
                'version' => 3,
                'file' => $shortfilename,
                'sources' => [$CFG->wwwroot . $srcfilename],
                'sourcesContent' => [null],
                'names' => [],
                'mappings' => ''
            ]
        ];
        // Load the original source file to calculate the number of lines we'll need to
        // skip forward for the next source map section.
        $js = file_get_contents($CFG->dirroot . $srcfilename);
    }

    $js = rtrim($js);

    $line += substr_count($js, "\n") + 1;
}

js_send_uncached(json_encode($map), 'jssourcemap.php');
