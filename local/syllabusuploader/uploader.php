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

// Require some stuffs.
require(dirname(dirname(dirname(__FILE__))) . '/config.php');
require(dirname(__FILE__) . '/lib.php');
require(dirname(__FILE__) . '/classes/forms/upload_form.php');
require(dirname(__FILE__) . '/classes/models/upload_model.php');

// Require the user is logged in.
require_login();

// Build the return url.
$returnurl = new moodle_url('/');

// Set the bool for access permission.
$allowed = \syllabusuploader_helpers::syllabusuploader_user($USER);

// Check to see if the user is admin.
if (!$allowed) {
    redirect($returnurl,
        get_string('no_upload_permissions', 'local_syllabusuploader'),
        null,
        \core\output\notification::NOTIFY_ERROR
    );
}

// Set these parms up for future use.
$id = optional_param('id', 0, PARAM_INT);
$n = optional_param('n', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_RAW);
$delete = optional_param('delete', '', PARAM_RAW);
$fileid = optional_param('idfile', 0, PARAM_INT);

// Build an array to store the parms.
$params = array();

// Build the urls for the page.
$url = new moodle_url($CFG->wwwroot . '/local/syllabusuploader/uploader.php');
$viewlink = new moodle_url($CFG->wwwroot . '/local/syllabusuploader/view.php');

// Set this for sanity's sake.
$uploadfile = null;

// If we have an id provided for a Moodle file, grab the file info.
if ($id) {
    $uploadfile = $DB->get_record('local_syllabusuploader_file', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    // If we have an id provided for a non-Moodle file, grab the file info.
    $uploadfile = $DB->get_record('local_syllabusuploader_file', array('id' => $n), '*', MUST_EXIST);
} else {
    // Otherwise we prepare for an upload.
    $uploadfile = new stdClass();
    $uploadfile->name = get_string('syllabusuploader_uploadstring', 'local_syllabusuploader');
    $uploadfile->id = 0;
}

// Set the context.
$context = context_system::instance();
// Set up the page.
$PAGE->set_context($context);
$PAGE->set_url($url, $params);
$PAGE->set_title(format_string($uploadfile->name));
if (is_siteadmin()) {
    $PAGE->navbar->add(
        get_string('settings', 'local_syllabusuploader'),
        new moodle_url($CFG->wwwroot. "/admin/settings.php?section=syllabusuploader")
    );
}
$PAGE->navbar->add(
    get_string('manage_uploader', 'local_syllabusuploader'),
    new moodle_url($CFG->wwwroot. "/local/syllabusuploader/uploader.php")
);
$PAGE->set_heading(get_string('syllabusuploader_uploadstring', 'local_syllabusuploader'));
$PAGE->set_pagelayout('base');

// If we want to push any data to javascript then we can add it here.
debugging() ? $debugjs = true : $debugjs = false;
$initialload = array(
    "debugging" => $debugjs,
);
$initialload = json_encode($initialload, JSON_HEX_APOS | JSON_HEX_QUOT);
$xtras = "<script>window.__SERVER__=true</script>".
    "<script>window.__INITIAL_STATE__='".$initialload."'</script>";

/*
TODO: Set up logging.
$event = \local_syllabusuploader\event\course_module_viewed::create(array(
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $uploadfile);
$event->trigger();
*/

// Build the model.
$model = new upload_model();

// If we have a file uploaded, set the id to the uploaded file id.
if ($uploadfile->id != 0) {
    $params = array(
        'id' => $uploadfile->id
    );

    // Get the file from the id.
    $file = $model->get( $uploadfile->id );

    // Prepare the data to pass into the form with instance.
    $action = 'UPDATE';

    // Build the form.
    $mform = new upload_form('./uploader.php?id='.$id. "&action={$action}&idfile={$file->id}");

    // Copy all the files from the 'real' area, into the draft area.
    file_prepare_draft_area(
        $file->local_syllabusuploader_file,
        $context->id,
        'local_syllabusuploader',
        'local_syllabusuploader_file',
        $file->local_syllabusuploader_files,
        null
    );

    // Set form data: This will load the file manager with your previous files.
    $mform->set_data($file);

} else {
    // Upload a new file.
    $action = 'ADD';
    $params = null;
    $mform = new upload_form();
}


// Build a button for viewing files.
$htmltidbits = html_writer::start_tag('a', array('href' => "./view.php"))
    .html_writer::start_tag('button', array('type' => 'button', 'class' => 'btn btn-primary', 'style' => 'margin:3%; width:20%'))
    .format_string(get_string('manage_viewer', 'local_syllabusuploader'))
    .html_writer::end_tag('button')
    .html_writer::end_tag('a');

// IF we cancel, redirect.
if ( $mform->is_cancelled() ) {
     redirect($viewlink);

} else if ( $formdata = $mform->get_data() ) {

    // Saves the form loaded file to the database in the files table.
    file_save_draft_area_files(
        $formdata->syllabusuploader_file,
        $context->id,
        'local_syllabusuploader',
        'syllabusuploader_file',
        $formdata->syllabusuploader_file,
        $mform->get_filemanager_options_array()
    );

    // Gets the content.
    $content = $mform->get_file_content('syllabusuploader_file');

    // To get the name of the uploaded file.
    $name = $mform->get_new_filename('syllabusuploader_file');

    // Get the path from the settings.
    $supath = get_config('moodle', "local_syllabusuploader_copy_file");

    // Make sure the folder is there.
    \syllabusuploader_helpers::upsert_system_folder();

    // Build the stored file from the form.
    $storedfile = $mform->save_stored_file(
        'syllabusuploader_file',
        $context->id,
        'syllabusuploader_file2',
        "",
        "",
        $supath,
        null,
        false
    );

    // Save or update in local table uploadfile_files.
    if ($action == 'ADD') {
        if (!$model->save($formdata)) {
            // Something failed when trying to save files.
            \core\notification::error("Error: There was an issue saving one of the files. Please try again.");
        }
    } else {
        $formdata->id = $fileid;
        $model->update($formdata);
    }

    // Redirect to view.
    redirect ($viewlink);
}

// Output the header.
echo $OUTPUT->header();
echo $xtras;
// Output the custom html.
echo $htmltidbits;

// Output the form.
$mform->display();

// Output the footer.
echo $OUTPUT->footer();
