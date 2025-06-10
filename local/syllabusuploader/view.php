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
 * @package    local_syllabusuploader
 * @copyright  2023 onwards LSU Online & Continuing Education
 * @copyright  2023 onwards Tim Hunt, Robert Russo, David Lowe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Require what we need.
require(dirname(dirname(dirname(__FILE__))) . '/config.php');
require(dirname(__FILE__) . '/lib.php');
require(dirname(__FILE__) . '/classes/models/upload_model.php');

// Require the user is logged in.
require_login();

// Set the return url.
$returnurl = new moodle_url('/');

// Set the bool for access permission.
$allowed = \syllabusuploader_helpers::syllabusuploader_user($USER);

// Check to see if the user is admin.
if (!$allowed) {
    redirect(
        $returnurl,
        get_string('no_upload_permissions', 'local_syllabusuploader'),
        null,
        \core\output\notification::NOTIFY_ERROR
    );
}

// Set the url for the page.
$url = new moodle_url($CFG->wwwroot . '/local/syllabusuploader/view.php');

// Set the context.
$context = context_system::instance();

// Set up the rest of the page.
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title(get_string('manage_viewer', 'local_syllabusuploader'));
if (is_siteadmin()) {
    $PAGE->navbar->add(
        get_string('settings', 'local_syllabusuploader'),
        new moodle_url($CFG->wwwroot. "/admin/settings.php?section=syllabusuploader")
    );
}
$PAGE->navbar->add(
    get_string('manage_viewer', 'local_syllabusuploader'),
    new moodle_url($CFG->wwwroot. "/local/syllabusuploader/view.php")
);

// If we want to push any data to javascript then we can add it here.
debugging() ? $debugjs = true : $debugjs = false;
$initialload = array(
    "debugging" => $debugjs,
);
$initialload = json_encode($initialload, JSON_HEX_APOS | JSON_HEX_QUOT);
$xtras = "<script>window.__SERVER__=true</script>".
    "<script>window.__INITIAL_STATE__='".$initialload."'</script>";


// Use the upload model to manage syllabus files.
$model = new upload_model();

// This is the mdl_syllabusuploader_files id NOT mdl_file id.
$id = optional_param('id', 0, PARAM_INT);

// Set up some parms for future use.
$action = optional_param('action', 0, PARAM_TEXT);
$mfileid = optional_param('mdl_file_id', 0, PARAM_INT);
$pfileid = optional_param('syllabusuploader_file_id', 0, PARAM_INT);
$filetype = optional_param('syllabusuploader_or_nonmood', '', PARAM_TEXT);
$nonmoodfilename = optional_param('nonmood_filename', '', PARAM_TEXT);

// Get the publicly accessible path.
$fpath = get_config('moodle', 'local_syllabusuploader_copy_file');

// Copy the file to destination or delete the file?
if ($action === "copy") {
    // We are copying the file, check to see if there's a destination configured.
    if (!isset($fpath)) {
        // I have no clie how we get here as if it's configured, we create it anyway.
        debugging("FAIL, no destination set for this file.");
    } else {
        // Get the storage info.
        $fs = get_file_storage();
        $file = $fs->get_file_by_id($mfileid);
        $fname = $file->get_filename();
    }

} else if ($action === "delete") {
    // We are deleting the file.
    if ($filetype === "su") {
        // Delete the moodle file.
        $model->delete($pfileid, $mfileid);
    } else if ($filetype === "nonmood") {
        // Delete the non-Moodle file.
        unlink($fpath.$nonmoodfilename);
    }
}
/*
TODO: Add this for logging purposes,
$event = \local_syllabusuploader\event\course_module_viewed::create(array(
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $uploadfile);
$event->trigger();
*/
// Set the page heading.
$PAGE->set_heading(get_string('manage_viewer', 'local_syllabusuploader'));
$PAGE->set_pagelayout('base');

// Output the header.
echo $OUTPUT->header();
echo $xtras;
// Build the uploader button.
echo html_writer::start_tag('a', array('href' => "./uploader.php"))
    .html_writer::start_tag('button', array('type' => 'button', 'class' => 'btn btn-primary', 'style' => 'margin:3%; width:20%'))
    .format_string(get_string('manage_uploader', 'local_syllabusuploader'))
    .html_writer::end_tag('button')
    .html_writer::end_tag('a');

// Build the renderable.
$renderable = new \local_syllabusuploader\output\files_view();

// Output the page.
echo $OUTPUT->render($renderable);

// Output the footer.
echo $OUTPUT->footer();
