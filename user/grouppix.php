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
 * This function fetches group pictures from the data directory.
 *
 * Syntax:   pix.php/groupid/f1.jpg or pix.php/groupid/f2.jpg
 *     OR:   ?file=groupid/f1.jpg or ?file=groupid/f2.jpg
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

// Disable moodle specific debug messages and any errors in output.
define('NO_DEBUG_DISPLAY', true);
define('NO_MOODLE_COOKIES', true); // Session not used here.

require_once('../config.php');
require_once($CFG->libdir.'/filelib.php');

$relativepath = get_file_argument();

$args = explode('/', trim($relativepath, '/'));

if (count($args) == 2) {
    $groupid  = (integer)$args[0];
    $image    = $args[1];
    $pathname = $CFG->dataroot.'/groups/'.$groupid.'/'.$image;
} else {
    $image    = 'f1.png';
    $pathname = $CFG->dirroot.'/pix/g/f1.png';
}

if (file_exists($pathname) and !is_dir($pathname)) {
    send_file($pathname, $image);
} else {
    header('HTTP/1.0 404 not found');
    print_error('filenotfound', 'error'); // This is not displayed on IIS??
}
