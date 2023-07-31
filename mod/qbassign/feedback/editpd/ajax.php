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
 * @package qbassignfeedback_editpd
 * @copyright  2012 Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use \qbassignfeedback_editpd\document_services;
use \qbassignfeedback_editpd\combined_document;
use \qbassignfeedback_editpd\page_editor;
use \qbassignfeedback_editpd\comments_quick_list;

define('AJAX_SCRIPT', true);

require('../../../../config.php');
require_once($CFG->dirroot . '/mod/qbassign/locallib.php');

require_sesskey();

$action = optional_param('action', '', PARAM_ALPHANUM);
$qbassignmentid = required_param('qbassignmentid', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$attemptnumber = required_param('attemptnumber', PARAM_INT);
$readonly = optional_param('readonly', false, PARAM_BOOL);

$cm = \get_coursemodule_from_instance('qbassign', $qbassignmentid, 0, false, MUST_EXIST);
$context = \context_module::instance($cm->id);

$qbassignment = new \qbassign($context, null, null);

require_login($qbassignment->get_course(), false, $cm);

if (!$qbassignment->can_view_submission($userid)) {
    throw new \moodle_exception('nopermission');
}

if ($action === 'pollconversions') {
    // Poll conversions does not require session lock.
    \core\session\manager::write_close();

    $draft = true;
    if (!has_capability('mod/qbassign:grade', $context)) {
        // A student always sees the readonly version.
        $readonly = true;
        $draft = false;
        require_capability('mod/qbassign:submit', $context);
    }

    if ($readonly) {
        // Whoever is viewing the readonly version should not use the drafts, but the actual annotations.
        $draft = false;
    }

    // Get a lock for the PDF/Image conversion of the qbassignment files.
    $lockfactory = \core\lock\lock_config::get_lock_factory('qbassignfeedback_editpd_pollconversions');
    $resource = "user:${userid},qbassignmentid:${qbassignmentid},attemptnumber:${attemptnumber}";
    $lock = $lockfactory->get_lock($resource, 0);

    // Could not get lock, send back JSON to poll again.
    if (!$lock) {
        echo json_encode([
            'status' => 0
        ]);
        die();
    }

    // Obtained lock, now process the qbassignment conversion.
    try {
        $response = (object) [
            'status' => -1,
            'filecount' => 0,
            'pagecount' => 0,
            'pageready' => 0,
            'partial' => false,
            'pages' => [],
        ];

        $combineddocument = document_services::get_combined_document_for_attempt($qbassignment, $userid, $attemptnumber);
        $response->status = $combineddocument->get_status();
        $response->filecount = $combineddocument->get_document_count();

        $readystatuslist = [combined_document::STATUS_READY, combined_document::STATUS_READY_PARTIAL];
        $completestatuslist = [combined_document::STATUS_COMPLETE, combined_document::STATUS_FAILED];

        if (in_array($response->status, $readystatuslist)) {
            // It seems that the files for this submission haven't been combined in cron yet.
            // Try to combine them in the user session.
            $combineddocument = document_services::get_combined_pdf_for_attempt($qbassignment, $userid, $attemptnumber);
            $response->status = $combineddocument->get_status();
            $response->filecount = $combineddocument->get_document_count();
        }

        if (in_array($response->status, $completestatuslist)) {
            $pages = document_services::get_page_images_for_attempt($qbassignment,
                                                                    $userid,
                                                                    $attemptnumber,
                                                                    $readonly);

            $response->pagecount = $combineddocument->get_page_count();

            $grade = $qbassignment->get_user_grade($userid, true, $attemptnumber);

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
                                                            'qbassignfeedback_editpd',
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

            $component = 'qbassignfeedback_editpd';
            $filearea = document_services::PAGE_IMAGE_FILEAREA;
            $filepath = '/';
            $fs = get_file_storage();
            $files = $fs->get_directory_files($context->id, $component, $filearea, $grade->id, $filepath);
            $response->pageready = count($files);
        }
    } catch (\Throwable $e) {
        // Release lock, and re-throw exception.
        $lock->release();
        throw $e;
    }

    echo json_encode($response);
    $lock->release();
    die();
} else if ($action == 'savepage') {
    require_capability('mod/qbassign:grade', $context);

    $response = new stdClass();
    $response->errors = array();

    $grade = $qbassignment->get_user_grade($userid, true, $attemptnumber);

    $pagejson = required_param('page', PARAM_RAW);
    $page = json_decode($pagejson);
    $index = required_param('index', PARAM_INT);

    $added = page_editor::set_comments($grade->id, $index, $page->comments);
    if ($added != count($page->comments)) {
        array_push($response->errors, get_string('couldnotsavepage', 'qbassignfeedback_editpd', $index+1));
    }
    $added = page_editor::set_annotations($grade->id, $index, $page->annotations);
    if ($added != count($page->annotations)) {
        array_push($response->errors, get_string('couldnotsavepage', 'qbassignfeedback_editpd', $index+1));
    }
    echo json_encode($response);
    die();

} else if ($action == 'generatepdf') {

    require_capability('mod/qbassign:grade', $context);
    $response = new stdClass();
    $grade = $qbassignment->get_user_grade($userid, true, $attemptnumber);
    $file = document_services::generate_feedback_document($qbassignment, $userid, $attemptnumber);

    $response->url = '';
    if ($file) {
        $url = moodle_url::make_pluginfile_url($qbassignment->get_context()->id,
                                               'qbassignfeedback_editpd',
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
    require_capability('mod/qbassign:grade', $context);

    $result = comments_quick_list::get_comments();

    echo json_encode($result);
    die();

} else if ($action == 'addtoquicklist') {
    require_capability('mod/qbassign:grade', $context);

    $comment = required_param('commenttext', PARAM_RAW);
    $width = required_param('width', PARAM_INT);
    $colour = required_param('colour', PARAM_ALPHA);

    $result = comments_quick_list::add_comment($comment, $width, $colour);

    echo json_encode($result);
    die();
} else if ($action == 'revertchanges') {
    require_capability('mod/qbassign:grade', $context);

    $grade = $qbassignment->get_user_grade($userid, true, $attemptnumber);

    $result = page_editor::revert_drafts($gradeid);

    echo json_encode($result);
    die();
} else if ($action == 'removefromquicklist') {
    require_capability('mod/qbassign:grade', $context);

    $commentid = required_param('commentid', PARAM_INT);

    $result = comments_quick_list::remove_comment($commentid);

    echo json_encode($result);
    die();
} else if ($action == 'deletefeedbackdocument') {
    require_capability('mod/qbassign:grade', $context);

    $grade = $qbassignment->get_user_grade($userid, true, $attemptnumber);
    $result = document_services::delete_feedback_document($qbassignment, $userid, $attemptnumber);

    $result = $result && page_editor::unrelease_drafts($grade->id);
    echo json_encode($result);
    die();
} else if ($action == 'rotatepage') {
    require_capability('mod/qbassign:grade', $context);
    $response = new stdClass();
    $index = required_param('index', PARAM_INT);
    $grade = $qbassignment->get_user_grade($userid, true, $attemptnumber);
    $rotateleft = required_param('rotateleft', PARAM_BOOL);
    $filearea = document_services::PAGE_IMAGE_FILEAREA;
    $pagefile = document_services::rotate_page($qbassignment, $userid, $attemptnumber, $index, $rotateleft);
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

