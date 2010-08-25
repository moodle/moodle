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

$PAGE->set_context($context);
$PAGE->set_url('/comment/comment_ajax.php');

$action    = optional_param('action',    '', PARAM_ALPHA);

$ignore_permission = false;
// XXX: display comments in frontpage without login
if ($context->id != get_context_instance(CONTEXT_COURSE, SITEID)->id
    or $action == 'add'
    or $action == 'delete') {
    $ignore_permission = true;
    require_login($course, true, $cm);
}
require_sesskey();

$area      = optional_param('area',      '', PARAM_ALPHAEXT);
$client_id = optional_param('client_id', '', PARAM_RAW);
$commentid = optional_param('commentid', -1, PARAM_INT);
$content   = optional_param('content',   '', PARAM_RAW);
$itemid    = optional_param('itemid',    '', PARAM_INT);
$page      = optional_param('page',      0,  PARAM_INT);
$component = optional_param('component', '',  PARAM_ALPHAEXT);

echo $OUTPUT->header(); // send headers

// initilising comment object
if (!empty($client_id)) {
    $args = new stdclass;
    $args->context   = $context;
    $args->course    = $course;
    $args->cm        = $cm;
    $args->area      = $area;
    $args->itemid    = $itemid;
    $args->client_id = $client_id;
    $args->component = $component;
    // only for comments in frontpage
    $args->ignore_permission = $ignore_permission;
    $manager = new comment($args);
} else {
    die;
}

// process ajax request
switch ($action) {
    case 'add':
        $result = $manager->add($content);
        if (!empty($result) && is_object($result)) {
            $result->count = $manager->count();
            $result->client_id = $client_id;
            echo json_encode($result);
        }
        break;
    case 'delete':
        $result = $manager->delete($commentid);
        if ($result === true) {
            echo json_encode(array('client_id'=>$client_id, 'commentid'=>$commentid));
        }
        break;
    case 'get':
    default:
        $result = array();
        $comments = $manager->get_comments($page);
        $result['list'] = $comments;
        $result['count'] = $manager->count();
        $result['pagination'] = $manager->get_pagination($page);
        $result['client_id']  = $client_id;
        echo json_encode($result);
}
