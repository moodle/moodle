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
 *
 * @package   core
 * @copyright 2010 Dongsheng Cai {@link http://dongsheng.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('AJAX_SCRIPT', true);

require_once('../config.php');
require_once($CFG->dirroot . '/comment/lib.php');

$contextid = optional_param('contextid', SYSCONTEXTID, PARAM_INT);
$action    = optional_param('action', '', PARAM_ALPHA);

if (empty($CFG->usecomments)) {
    throw new comment_exception('commentsnotenabled', 'moodle');
}

list($context, $course, $cm) = get_context_info_array($contextid);

$PAGE->set_url('/comment/comment_ajax.php');

// Allow anonymous user to view comments providing forcelogin now enabled
require_course_login($course, true, $cm);
$PAGE->set_context($context);
if (!empty($cm)) {
    $PAGE->set_cm($cm, $course);
} else if (!empty($course)) {
    $PAGE->set_course($course);
}

if (!confirm_sesskey()) {
    $error = array('error'=>get_string('invalidsesskey', 'error'));
    die(json_encode($error));
}

$client_id = required_param('client_id', PARAM_ALPHANUM);
$area      = optional_param('area',      '', PARAM_AREA);
$commentid = optional_param('commentid', -1, PARAM_INT);
$content   = optional_param('content',   '', PARAM_RAW);
$itemid    = optional_param('itemid',    '', PARAM_INT);
$page      = optional_param('page',      0,  PARAM_INT);
$component = optional_param('component', '',  PARAM_COMPONENT);

// initilising comment object
$args = new stdClass;
$args->context   = $context;
$args->course    = $course;
$args->cm        = $cm;
$args->area      = $area;
$args->itemid    = $itemid;
$args->client_id = $client_id;
$args->component = $component;
$manager = new comment($args);

echo $OUTPUT->header(); // send headers

// process ajax request
switch ($action) {
    case 'add':
        if ($manager->can_post()) {
            $result = $manager->add($content);
            if (!empty($result) && is_object($result)) {
                $result->count = $manager->count();
                $result->client_id = $client_id;
                echo json_encode($result);
                die();
            }
        }
        break;
    case 'delete':
        $comment_record = $DB->get_record('comments', array('id'=>$commentid));
        if ($manager->can_delete($commentid) || $comment_record->userid == $USER->id) {
            if ($manager->delete($commentid)) {
                $result = array(
                    'client_id' => $client_id,
                    'commentid' => $commentid
                );
                echo json_encode($result);
                die();
            }
        }
        break;
    case 'get':
    default:
        if ($manager->can_view()) {
            $comments = $manager->get_comments($page);
            $result = array(
                'list'       => $comments,
                'count'      => $manager->count(),
                'pagination' => $manager->get_pagination($page),
                'client_id'  => $client_id
            );
            echo json_encode($result);
            die();
        }
        break;
}

if (!isloggedin()) {
    // tell user to log in to view comments
    echo json_encode(array('error'=>'require_login'));
}
// ignore request
die;
