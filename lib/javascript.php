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
 * @package   moodlecore
 * @copyright 2010 Petr Skoda (skodak)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// we need just the values from config.php and minlib.php
define('ABORT_AFTER_CONFIG', true);
require('../config.php'); // this stops immediately at the beginning of lib/setup.php

$file = min_optional_param('file', '', 'SAFEPATH');
$rev  = min_optional_param('rev', 0, 'INT');

if (empty($file) or strpos($file, '/') !== 0 or !preg_match('/\.js$/', $file)) {
    die;
}

$jspath = $CFG->dirroot.$file;

if (file_exists($jspath)) {
    send_cached_js($jspath);
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
    header('Content-Type: application/x-javascript');
    if (!min_enable_zlib_compression()) {
        header('Content-Length: '.filesize($jspath));
    }

    readfile($jspath);
    die;
}
