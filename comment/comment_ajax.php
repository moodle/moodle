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
define('AJAX_SCRIPT', true);

require_once('../config.php');
require_once($CFG->dirroot . '/comment/lib.php');

$contextid = optional_param('contextid', SYSCONTEXTID, PARAM_INT);
list($context, $course, $cm) = get_context_info_array($contextid);

require_login($course, true, $cm);
require_sesskey();

$action    = optional_param('action',    '',     PARAM_ALPHA);
$area      = optional_param('area',      '',     PARAM_ALPHAEXT);
$client_id = optional_param('client_id', '',     PARAM_RAW);
$commentid = optional_param('commentid', -1,     PARAM_INT);
$content   = optional_param('content',   '',     PARAM_RAW);
$itemid    = optional_param('itemid',    '',     PARAM_INT);
$page      = optional_param('page',      0,      PARAM_INT);

if (!empty($client_id)) {
    $cmt = new stdclass;
    $cmt->context   = $context;
    $cmt->course    = $course;
    $cmt->cm        = $cm;
    $cmt->area      = $area;
    $cmt->itemid    = $itemid;
    $cmt->client_id = $client_id;
    $comment = new comment($cmt);
} else {
    die;
}

switch ($action) {
    case 'add':
        $cmt = $comment->add($content);
        if (!empty($cmt) && is_object($cmt)) {
            $cmt->count = $comment->count();
            $cmt->client_id = $client_id;
            echo json_encode($cmt);
        }
        break;
    case 'delete':
        $result = $comment->delete($commentid);
        if ($result === true) {
            echo json_encode(array('client_id'=>$client_id, 'commentid'=>$commentid));
        }
        break;
    case 'get':
    default:
        $ret = array();
        $comments = $comment->get_comments($page);
        $ret['list'] = $comments;
        $ret['count'] = $comment->count();
        $ret['pagination'] = $comment->get_pagination($page);
        $ret['client_id']  = $client_id;
        echo json_encode($ret);
}
