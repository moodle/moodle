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
 * This script serves files from dataroot/codecoverage
 *
 * Syntax:      coveragefile.php/path/to/file/file.html
 *              coveragefile.php?file=path/to/file/file.html
 *
 * @package    moodlecore
 * @subpackage simpletestcoverage
 * @copyright  2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// disable moodle specific debug messages and any errors in output
define('NO_DEBUG_DISPLAY', true);

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/filelib.php');

// basic security, require login + require site config cap
require_login();
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

// get file requested
$relativepath  = get_file_argument();

// basic check, start by slash
if (!$relativepath) {
    print_error('invalidargorconf');
} else if ($relativepath{0} != '/') {
    print_error('pathdoesnotstartslash');
}

// determine which disk file is going to be served
// and how it's going to be named
$filepath = $CFG->dataroot . '/codecoverage' . $relativepath;
$filename = basename($filepath);

// extract relative path components
$args = explode('/', ltrim($relativepath, '/'));

// only serve from some controlled subdirs
$alloweddirs = array('dbtest', 'unittest');
if (!isset($args[0]) || !in_array($args[0], $alloweddirs)) {
    print_error('invalidarguments');
}

// only serve some controlled extensions
$allowedextensions = array('text/html', 'text/css', 'image/gif', 'application/x-javascript');
if (!in_array(mimeinfo('type', $filepath), $allowedextensions)) {
    print_error('invalidarguments');
}

// arrived here, send the file
session_get_instance()->write_close(); // unlock session during fileserving
send_file($filepath, $filename, 0, false);

