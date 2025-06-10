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
 * @package    block_pu
 * @copyright  2021 onwards LSU Online & Continuing Education
 * @copyright  2021 onwards Tim Hunt, Robert Russo, David Lowe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require (dirname(dirname(dirname(__FILE__))) . '/config.php');
require (dirname(__FILE__) . '/classes/models/upload_model.php');

// Require the user is logged in.
require_login();

// Set the context.
$context = \context_system::instance();

// Set the return url.
$returnurl = new moodle_url('/');

// Check to see if the user is admin.
if (!has_capability('block/pu:admin', $context)) {
    redirect($returnurl, get_string('no_upload_permissions', 'block_pu'), null, \core\output\notification::NOTIFY_ERROR);
}

// Set the url for the page.
$url = new moodle_url($CFG->wwwroot . '/blocks/pu/view.php');

// Set up the rest of the page.
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title(get_string('manage_viewer', 'block_pu'));
$PAGE->navbar->add(get_string('pu_settings', 'block_pu'), new moodle_url($CFG->wwwroot. "/admin/settings.php?section=blocksettingpu"));

// Use the upload model to manage pu files.
$model = new upload_model();

// This is the mdl_pu_files id NOT mdl_file id.
$id = optional_param('id', 0, PARAM_INT);

// Set up some parms for future use.
$action = optional_param('action', 0, PARAM_TEXT);
$mfileid = optional_param('mdl_file_id', 0, PARAM_INT);
$pfileid = optional_param('pu_file_id', 0, PARAM_INT);
$filetype = optional_param('pu_or_nonmood', '', PARAM_TEXT);
$nonmood_filename = optional_param('nonmood_filename', '', PARAM_TEXT);
$fpath = get_config('moodle', 'block_pu_copy_file');

// Copy the file to destination or delete the file?
if ($action === "copy") {
    // We are copying the file, check to see if there's a destination configured.
    if (!isset($fpath)) {
        debugging("PU - FAIL, no destination set for this file.");
    } else {
        $fs = get_file_storage();
        $file = $fs->get_file_by_id($mfileid);
        $fname = $file->get_filename();
    }

} else if ($action === "delete") {
    // We are deleting the file.
    if ($filetype === "pu") {
        $model->delete($pfileid, $mfileid);
    } else if ($filetype === "nonmood") {
        unlink($fpath.$nonmood_filename);
    }
}

// TODO: Add this for logging purposes, 
// $event = \block_pu\event\course_module_viewed::create(array(
//             'objectid' => $PAGE->cm->instance,
//             'context' => $PAGE->context,
//         ));
// $event->add_record_snapshot('course', $PAGE->course);
// $event->add_record_snapshot($PAGE->cm->modname, $uploadfile);
// $event->trigger();

// Set the page heading.
$PAGE->set_heading(get_string('manage_viewer', 'block_pu'));

// Output the header.
echo $OUTPUT->header();

// Build the uploader button.
echo html_writer::start_tag( 'a', array( 'href' => "./uploader.php" ) )
        .html_writer::start_tag( 'button', array( 'type' => 'button', 'class' => 'btn btn-primary', 'style' =>'margin:3%; width:20%' ) )
        .format_string( get_string('manage_uploader', 'block_pu') )
        .html_writer::end_tag('button')
        .html_writer::end_tag( 'a' );

// Build the renderable.
$renderable = new \block_pu\output\files_view();

// output the page.
echo $OUTPUT->render($renderable);

// Output the footer.
echo $OUTPUT->footer();
