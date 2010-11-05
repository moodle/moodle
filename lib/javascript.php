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
 * @package    core
 * @subpackage lib
 * @copyright  2010 Petr Skoda (skodak)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// we need just the values from config.php and minlib.php
define('ABORT_AFTER_CONFIG', true);
require('../config.php'); // this stops immediately at the beginning of lib/setup.php

ini_set('zlib.output_compression', 'Off');

// setup include path
set_include_path($CFG->libdir . '/minify/lib' . PATH_SEPARATOR . get_include_path());
require_once('Minify.php');

$file = min_optional_param('file', '', 'RAW');
$rev  = min_optional_param('rev', 0, 'INT');

// some security first - pick only files with .js extension in dirroot
$jsfiles = array();
$files = explode(',', $file);
foreach ($files as $fsfile) {
    $jsfile = realpath($CFG->dirroot.$fsfile);
    if ($jsfile === false) {
        // does not exist
        continue;
    }
    if (strpos($jsfile, $CFG->dirroot . DIRECTORY_SEPARATOR) !== 0) {
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
    die();
}

minify($jsfiles);

function minify($files) {
    global $CFG;

    $cachedir = $CFG->dataroot.'/cache/js';
    // make sure the cache dir exist
    if (!file_exists($cachedir)) {
        @mkdir($cachedir, $CFG->directorypermissions, true);
    }

    if (0 === stripos(PHP_OS, 'win')) {
        Minify::setDocRoot(); // IIS may need help
    }
    Minify::setCache($cachedir, true);

    $options = array(
        // Maximum age to cache
        'maxAge' => (60*60*24*20),
        // The files to minify
        'files' => $files
    );

    Minify::serve('Files', $options);
    die();
}
