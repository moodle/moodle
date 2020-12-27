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
 * Process ajax requests
 *
 * @package assignfeedback_editpdf
 * @copyright  2012 Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use \assignfeedback_editpdf\document_services;
use \assignfeedback_editpdf\combined_document;
use \assignfeedback_editpdf\page_editor;
use \assignfeedback_editpdf\comments_quick_list;

define('AJAX_SCRIPT', true);

require('../../../../config.php');
require_once($CFG->dirroot . '/mod/assign/locallib.php');

require_sesskey();

$action = optional_param('action', '', PARAM_ALPHANUM);
$assignmentid = required_param('assignmentid', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$attemptnumber = required_param('attemptnumber', PARAM_INT);
$readonly = optional_param('readonly', false, PARAM_BOOL);

$cm = \get_coursemodule_from_instance('assign', $assignmentid, 0, false, MUST_EXIST);
$context = \context_module::instance($cm->id);

$assignment = new \assign($context, null, null);

require_login($assignment->get_course(), false, $cm);

if (!$assignment->can_view_submission($userid)) {
    print_error('nopermission');
}

if ($action === 'pollconversions') {
    $draft = true;
    if (!has_capability('mod/assign:grade', $context)) {
        // A student always sees the readonly version.
        $readonly = true;
        $draft = false;
        require_capability('mod/assign:submit', $context);
    }

    if ($readonly) {
        // Whoever is viewing the readonly version should not use the drafts, but the actual annotations.
        $draft = false;
    }

    $response = (object) [
            'status' => -1,
            'filecount' => 0,
            'pagecount' => 0,
            'pageready' => 0,
            'partial' => false,
            'pages' => [],
        ];

    $combineddocument = document_services::get_combined_document_for_attempt($assignment, $userid, $attemptnumber);
    $response->status = $combineddocument->get_status();
    $response->filecount = $combineddocument->get_document_count();

    $readystatuslist = [combined_document::STATUS_READY, combined_document::STATUS_READY_PARTIAL];
    $completestatuslist = [combined_document::STATUS_COMPLETE, combined_document::STATUS_FAILED];

    if (in_array($response->status, $readystatuslist)) {
        $combineddocument = document_services::get_combined_pdf_for_attempt($assignment, $userid, $attemptnumber);
        $response->status = $combineddocument->get_status();
        $response->filecount = $combineddocument->get_document_count();
    }

    if (in_array($response->status, $completestatuslist)) {
        $pages = document_services::get_page_images_for_attempt($assignment,
                                                                $userid,
                                                                $attemptnumber,
                                                                $readonly);

        $response->pagecount = $combineddocument->get_page_count();

        $grade = $assignment->get_user_grade($userid, true, $attemptnumber);

        // The readonly files are stored in a different file area.
        $filearea = document_services::PAGE_IMAGE_FILEAREA;
        if ($readonly) {
            $filearea = document_services::PAGE_IMAGE_READONLY_FILEAREA;
        }
        $response->partial = $combineddocument->is_partial_conversion();

        foreach ($pages as $id => $pagefile) {
            $index = count($response->pages);
            $page = new stdClass();
            $comments = page_editor::get_comments($grade->id, $index, $draft);
            $page->url = moodle_url::make_pluginfile_url($context->id,
                                                        'assignfeedback_editpdf',
                                                        $filearea,
                                                        $grade->id,
                                                        '/',
                                                        $pagefile->get_filename())->out();
            $page->comments = $comments;
            if ($imageinfo = $pagefile->get_imageinfo()) {
                $page->width = $imageinfo['width'];
                $page->height = $imageinfo['height'];
            } else {
                $page->width = 0;
                $page->height = 0;
            }
            $annotations = page_editor::get_annotations($grade->id, $index, $draft);
            $page->annotations = $annotations;
            $response->pages[] = $page;
        }

        $component = 'assignfeedback_editpdf';
        $filearea = document_services::PAGE_IMAGE_FILEAREA;
        $filepath = '/';
        $fs = get_file_storage();
        $files = $fs->get_directory_files($context->id, $component, $filearea, $grade->id, $filepath);
        $response->pageready = count($files);
    }

    echo json_encode($response);
    die();
} else if ($action == 'savepage') {
    require_capability('mod/assign:grade', $context);

    $response = new stdClass();
    $response->errors = array();

    $grade = $assignment->get_user_grade($userid, true, $attemptnumber);

    $pagejson = required_param('page', PARAM_RAW);
    $page = json_decode($pagejson);
    $index = required_param('index', PARAM_INT);

    $added = page_editor::set_comments($grade->id, $index, $page->comments);
    if ($added != count($page->comments)) {
        array_push($response->errors, get_string('couldnotsavepage', 'assignfeedback_editpdf', $index+1));
    }
    $added = page_editor::set_annotations($grade->id, $index, $page->annotations);
    if ($added != count($page->annotations)) {
        array_push($response->errors, get_string('couldnotsavepage', 'assignfeedback_editpdf', $index+1));
    }
    echo json_encode($response);
    die();

} else if ($action == 'generatepdf') {

    require_capability('mod/assign:grade', $context);
    $response = new stdClass();
    $grade = $assignment->get_user_grade($userid, true, $attemptnumber);
    $file = document_services::generate_feedback_document($assignment, $userid, $attemptnumber);

    $response->url = '';
    if ($file) {
        $url = moodle_url::make_pluginfile_url($assignment->get_context()->id,
                                               'assignfeedback_editpdf',
                                               document_services::FINAL_PDF_FILEAREA,
                                               $grade->id,
                                               '/',
                                               $file->get_filename(),
                                               false);
        $response->url = $url->out(true);
        $response->filename = $file->get_filename();
    }

    echo json_encode($response);
    die();
} else if ($action == 'loadquicklist') {
    require_capability('mod/assign:grade', $context);

    $result = comments_quick_list::get_comments();

    echo json_encode($result);
    die();

} else if ($action == 'addtoquicklist') {
    require_capability('mod/assign:grade', $context);

    $comment = required_param('commenttext', PARAM_RAW);
    $width = required_param('width', PARAM_INT);
    $colour = required_param('colour', PARAM_ALPHA);

    $result = comments_quick_list::add_comment($comment, $width, $colour);

    echo json_encode($result);
    die();
} else if ($action == 'revertchanges') {
    require_capability('mod/assign:grade', $context);

    $grade = $assignment->get_user_grade($userid, true, $attemptnumber);

    $result = page_editor::revert_drafts($gradeid);

    echo json_encode($result);
    die();
} else if ($action == 'removefromquicklist') {
    require_capability('mod/assign:grade', $context);

    $commentid = required_param('commentid', PARAM_INT);

    $result = comments_quick_list::remove_comment($commentid);

    echo json_encode($result);
    die();
} else if ($action == 'deletefeedbackdocument') {
    require_capability('mod/assign:grade', $context);

    $grade = $assignment->get_user_grade($userid, true, $attemptnumber);
    $result = document_services::delete_feedback_document($assignment, $userid, $attemptnumber);

    $result = $result && page_editor::unrelease_drafts($grade->id);
    echo json_encode($result);
    die();
} else if ($action == 'rotatepage') {
    require_capability('mod/assign:grade', $context);
    $response = new stdClass();
    $index = required_param('index', PARAM_INT);
    $grade = $assignment->get_user_grade($userid, true, $attemptnumber);
    $rotateleft = required_param('rotateleft', PARAM_BOOL);
    $filearea = document_services::PAGE_IMAGE_FILEAREA;
    $pagefile = document_services::rotate_page($assignment, $userid, $attemptnumber, $index, $rotateleft);
    $page = new stdClass();
    $page->url = moodle_url::make_pluginfile_url($context->id, document_services::COMPONENT, $filearea,
        $grade->id, '/', $pagefile->get_filename())->out();
    if ($imageinfo = $pagefile->get_imageinfo()) {
        $page->width = $imageinfo['width'];
        $page->height = $imageinfo['height'];
    } else {
        $page->width = 0;
        $page->height = 0;
    }
    $response = (object)['page' => $page];
    echo json_encode($response);
    die();
}

