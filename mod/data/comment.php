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
 * This file is part of the Database module for Moodle
 *
 * @copyright 2005 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package mod-data
 */

require_once('../../config.php');
require_once('lib.php');
require_once('comment_form.php');

//param needed to go back to view.php
$rid  = required_param('rid', PARAM_INT);   // Record ID
$page = optional_param('page', 0, PARAM_INT);   // Page ID

//param needed for comment operations
$mode = optional_param('mode','add',PARAM_ALPHA);
$commentid = optional_param('commentid','',PARAM_INT);
$confirm = optional_param('confirm','',PARAM_INT);

$url = new moodle_url($CFG->wwwroot.'/mod/data/comment.php', array('rid'=>$rid));
if ($page !== 0) {
    $url->param('page', $page);
}
if ($mode !== 'add') {
    $url->param('mode', $mode);
}
if ($commentid !== '') {
    $url->param('commentid', $commentid);
}
if ($confirm !== '') {
    $url->param('confirm', $confirm);
}
$PAGE->set_url($url);

if (! $record = $DB->get_record('data_records', array('id'=>$rid))) {
    print_error('invalidrecord', 'data');
}
if (! $data = $DB->get_record('data', array('id'=>$record->dataid))) {
    print_error('invalidid', 'data');
}
if (! $course = $DB->get_record('course', array('id'=>$data->course))) {
    print_error('coursemisconf');
}
if (! $cm = get_coursemodule_from_instance('data', $data->id, $course->id)) {
    print_error('invalidcoursemodule');
}

require_login($course->id, false, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/data:comment', $context);

if ($commentid) {
    if (! $comment = $DB->get_record('data_comments', array('id'=>$commentid))) {
        print_error('commentmisconf');
    }
    if ($comment->recordid != $record->id) {
        print_error('commentmisconf');
    }
    if (!has_capability('mod/data:managecomments', $context) && $comment->userid != $USER->id) {
        print_error('cannoteditcomment');
    }
} else {
    $comment = false;
}


$mform = new mod_data_comment_form();
$mform->set_data(array('mode'=>$mode, 'page'=>$page, 'rid'=>$record->id, 'commentid'=>$commentid));
if ($comment) {
    $format = $comment->format;
    $content = $comment->content;
    if (can_use_html_editor()) {
        $options = new object();
        $options->smiley = false;
        $options->filter = false;
        $content = format_text($content, $format, $options);
        $format = FORMAT_HTML;
    }
    $mform->set_data(array('content'=>$content, 'format'=>$format));
}


if ($mform->is_cancelled()) {
    redirect('view.php?rid='.$record->id.'&amp;page='.$page);
}

switch ($mode) {
    case 'add':
        if (!$formadata = $mform->get_data()) {
            break; // something is wrong here, try again
        }

        $newcomment = new object();
        $newcomment->userid   = $USER->id;
        $newcomment->created  = time();
        $newcomment->modified = time();
        $newcomment->content  = $formadata->content;
        $newcomment->recordid = $formadata->rid;
        if ($DB->insert_record('data_comments',$newcomment)) {
            redirect('view.php?rid='.$record->id.'&amp;page='.$page);
        } else {
            print_error('cannotsavecomment');
        }

    break;

    case 'edit':    //print edit form
        if (!$formadata = $mform->get_data()) {
            break; // something is wrong here, try again
        }

        $updatedcomment = new object();
        $updatedcomment->id       = $formadata->commentid;
        $updatedcomment->content  = $formadata->content;
        $updatedcomment->format   = $formadata->format;
        $updatedcomment->modified = time();

        if ($DB->update_record('data_comments', $updatedcomment)) {
            redirect('view.php?rid='.$record->id.'&amp;page='.$page);
        } else {
            print_error('cannotsavecomment');
        }
    break;

    case 'delete':    //deletes single comment from db
        if ($confirm and confirm_sesskey() and $comment) {
            $DB->delete_records('data_comments', array('id'=>$comment->id));
            redirect('view.php?rid='.$record->id.'&amp;page='.$page, get_string('commentdeleted', 'data'));

        } else {    //print confirm delete form
            echo $OUTPUT->header();
            data_print_comment($data, $comment, $page);

            echo $OUTPUT->confirm(get_string('deletecomment','data'),
                    'comment.php?rid='.$record->id.'&commentid='.$comment->id.'&page='.$page.'&mode=delete&confirm=1',
                    'view.php?rid='.$record->id.'&page='.$page);
            echo $OUTPUT->footer();
        }
        die;
    break;

}

echo $OUTPUT->header();
data_print_comments($data, $record, $page, $mform);
echo $OUTPUT->footer();

