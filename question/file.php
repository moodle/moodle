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
 * This script fetches files from the dataroot/questionattempt directory
 * It is based on the top-level file.php
 *
 * On a module-by-module basis (currently only implemented for quiz), it checks
 * whether the user has permission to view the file.
 *
 * Syntax:      question/file.php/attemptid/questionid/filename.ext
 * Workaround:  question/file.php?file=/attemptid/questionid/filename.ext
 *
 * @package moodlecore
 * @subpackage questionengine
 * @copyright 2007 Adriane Boyd
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


// disable moodle specific debug messages and any errors in output
define('NO_DEBUG_DISPLAY', true);


require_once('../config.php');
require_once('../lib/filelib.php');

$relativepath = get_file_argument();
// force download for any student-submitted files to prevent XSS attacks.
$forcedownload = 1;

// relative path must start with '/', because of backup/restore!!!
if (!$relativepath) {
    print_error('invalidarguments');
} else if ($relativepath{0} != '/') {
    print_error('pathdoesnotstartslash');
}

$pathname = $CFG->dataroot.'/questionattempt'.$relativepath;

// extract relative path components
$args = explode('/', trim($relativepath, '/'));

// check for the right number of directories in the path
if (count($args) != 3) {
    print_error('invalidarguments');
}

// security: require login
require_login();

// security: do not return directory node!
if (is_dir($pathname)) {
    question_attempt_not_found();
}

$lifetime = 0;  // do not cache because students may reupload files

// security: check that the user has permission to access this file
$haspermission = false;
if ($attempt = $DB->get_record("question_attempts", array("id" => $args[0]))) {
    $modfile = $CFG->dirroot .'/mod/'. $attempt->modulename .'/lib.php';
    $modcheckfileaccess = $attempt->modulename .'_check_file_access';
    if (file_exists($modfile)) {
        @require_once($modfile);
        if (function_exists($modcheckfileaccess)) {
            $haspermission = $modcheckfileaccess($args[0], $args[1]);
        }
    }
} else if ($args[0][0] == 0) {
    global $USER;
    $list = explode('_', $args[0]);
    if ($list[1] == $USER->id) {
        $haspermission = true;
    }
}

if ($haspermission) {
    // check that file exists
    if (!file_exists($pathname)) {
        question_attempt_not_found();
    }

    // send the file
    session_get_instance()->write_close(); // unlock session during fileserving
    $filename = $args[count($args)-1];
    send_file($pathname, $filename, $lifetime, $CFG->filteruploadedfiles, false, $forcedownload);
} else {
    question_attempt_not_found();
}

function question_attempt_not_found() {
    global $CFG;
    header('HTTP/1.0 404 not found');
    print_error('filenotfound', 'error', $CFG->wwwroot); //this is not displayed on IIS??
}
