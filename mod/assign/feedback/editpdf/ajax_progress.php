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
 * Process concurrent ajax request.
 * ALL RETURNED INFO IS PUBLIC.
 *
 * @package assignfeedback_editpdf
 * @copyright  2013 Jerome Mouneyrac
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);
// To be able to process concurrent ajax request with the generate pdf ajax request we can not use cookie.
define('NO_MOODLE_COOKIES', true);

use \assignfeedback_editpdf\document_services;
require_once('../../../../config.php');

try {
    $assignmentid = required_param('assignmentid', PARAM_INT);
    $userid = required_param('userid', PARAM_INT);
    $attemptnumber = required_param('attemptnumber', PARAM_INT);

    // Retrieve the assignments.
    require_once($CFG->dirroot . '/mod/assign/locallib.php');
    $cm = get_coursemodule_from_instance('assign', $assignmentid, 0, false, MUST_EXIST);
    $context = context_module::instance($cm->id);
    $assignment = new assign($context, null, null);

    // Get the generated images from file API call.
    $grade = $assignment->get_user_grade($userid, false, $attemptnumber);

    // Check we found a grade.
    if (empty($grade)) {
        throw new coding_exception('grade not found');
    }

    // No need to handle the readonly files here, the should be already generated.
    $component = 'assignfeedback_editpdf';
    $filearea = document_services::PAGE_IMAGE_FILEAREA;
    $filepath = '/';
    $fs = get_file_storage();
    $files = $fs->get_directory_files($context->id, $component, $filearea, $grade->id, $filepath);

    // The important security part: we ONLY RETURN the total NUMBER of generated images.
    echo $OUTPUT->header();
    echo json_encode(count($files));
    echo $OUTPUT->footer();
} catch (Exception $e) {
    // This should never happened!
    // Return a 500 HTTP header so Y.io gets it as a failure.
    if (substr(php_sapi_name(), 0, 3) == 'cgi') {
        header("Status: 500 Internal Server Error");
    } else {
        header('HTTP/1.0 500 Internal Server Error');
    }
    throw new moodle_exception('An exception was caught but can not be returned for security purpose.
        To easily debug, comment the try catch.');
}
