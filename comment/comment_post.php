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
 * Handling new comments from non-js comments interface
 *
 * @package   core
 * @copyright 2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../config.php');
require_once($CFG->dirroot . '/comment/lib.php');

if (empty($CFG->usecomments)) {
    throw new comment_exception('commentsnotenabled', 'moodle');
}

$contextid = optional_param('contextid', SYSCONTEXTID, PARAM_INT);
list($context, $course, $cm) = get_context_info_array($contextid);

require_login($course, true, $cm);
require_sesskey();

$action    = optional_param('action',    '',  PARAM_ALPHA);
$area      = optional_param('area',      '',  PARAM_AREA);
$content   = optional_param('content',   '',  PARAM_RAW);
$itemid    = optional_param('itemid',    '',  PARAM_INT);
$returnurl = optional_param('returnurl', '/', PARAM_LOCALURL);
$component = optional_param('component', '',  PARAM_COMPONENT);

// Currently this script can only add comments
if ($action !== 'add') {
    redirect($returnurl);
}

$cmt = new stdClass;
$cmt->contextid = $contextid;
$cmt->courseid  = $course->id;
$cmt->cm        = $cm;
$cmt->area      = $area;
$cmt->itemid    = $itemid;
$cmt->component = $component;
$comment = new comment($cmt);

if ($comment->can_post()) {
    $cmt = $comment->add($content);
    if (!empty($cmt) && is_object($cmt)) {
        redirect($returnurl);
    }
}
