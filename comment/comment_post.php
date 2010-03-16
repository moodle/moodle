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

/*
 * Handling all ajax request for comments API
 */
require_once('../config.php');
require_once($CFG->dirroot . '/comment/lib.php');

$contextid = optional_param('contextid', SYSCONTEXTID, PARAM_INT);
list($context, $course, $cm) = get_context_info_array($contextid);

$context   = get_context_instance_by_id($contextid);
if ($context->contextlevel == CONTEXT_MODULE) {
    $cm = get_coursemodule_from_id('', $context->instanceid);
} else {
    $cm = null;
}
require_login($course, true, $cm);

$err = new stdclass;

if (!confirm_sesskey()) {
    print_error('invalidsesskey');
}

if (!isloggedin()){
    print_error('loggedinnot');
}

if (isguestuser()) {
    print_error('loggedinnot');
}

$action    = optional_param('action',    '',     PARAM_ALPHA);
$area      = optional_param('area',      '',     PARAM_ALPHAEXT);
$commentid = optional_param('commentid', -1,     PARAM_INT);
$content   = optional_param('content',   '',     PARAM_RAW);
$itemid    = optional_param('itemid',    '',     PARAM_INT);
$returnurl = optional_param('returnurl', '',     PARAM_URL);

$cmt = new stdclass;
$cmt->contextid = $contextid;
$cmt->courseid  = $course->id;
$cmt->area      = $area;
$cmt->itemid    = $itemid;
$comment = new comment($cmt);

switch ($action) {
case 'add':
    try {
        $cmt = $comment->add($content);
        if (!empty($cmt) && is_object($cmt)) {
            redirect($returnurl, get_string('pageshouldredirect'), 0);
        }
    } catch(comment_exception $e) {
        print_error($e->errorcode);
    }
    break;
default:
    exit;
}
