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
require (dirname(__FILE__) . '/classes/forms/upload_form.php');
require (dirname(__FILE__) . '/classes/models/upload_model.php');

// Require the user is logged in.
require_login();

// Set the context.
$context = \context_system::instance();

// Build the return url.
$returnurl = new moodle_url('/');

// Check to see if the user is admin.
if (!has_capability('block/pu:admin', $context)) {
    redirect($returnurl, get_string('no_upload_permissions', 'block_pu'), null, \core\output\notification::NOTIFY_ERROR);
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
$url = new moodle_url($CFG->wwwroot . '/blocks/pu/uploader.php');
$viewlink = new moodle_url($CFG->wwwroot . '/blocks/pu/view.php');

// Set this for sanity's sake.
$uploadfile = null;

// If we have an id provided for a Moodle file, grab the file info.
if ($id) {
    $uploadfile = $DB->get_record('block_pu_file', array('id' => $cm->instance), '*', MUST_EXIST);
// If we have an id provided for a non-Moodle file, grab the file info.
} else if ($n) {
    $uploadfile = $DB->get_record('block_pu_file', array('id' => $n), '*', MUST_EXIST);
// Otherwise we prepare for an upload.
} else {
    $uploadfile = new stdClass();
    $uploadfile->name = get_string('pu_uploadstring', 'block_pu');
    $uploadfile->id = 0;
}    

// Set up the page.
$PAGE->set_context($context);
$PAGE->set_url($url, $params);
$PAGE->set_title(format_string($uploadfile->name));
$PAGE->navbar->add(get_string('pu_settings', 'block_pu'), new moodle_url($CFG->wwwroot. "/admin/settings.php?section=blocksettingpu"));
$PAGE->set_heading(get_string('pu_uploadstring','block_pu'));

// $event = \block_pu\event\course_module_viewed::create(array(
//             'objectid' => $PAGE->cm->instance,
//             'context' => $PAGE->context,
//         ));
// $event->add_record_snapshot('course', $PAGE->course);
// $event->add_record_snapshot($PAGE->cm->modname, $uploadfile);
// $event->trigger();

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
    file_prepare_draft_area($file->block_pu_file, $context->id, 'block_pu', 'block_pu_file', $file->block_pu_files, null);

    // Set form data: This will load the file manager with your previous files
    $mform->set_data($file); 

} else {
    $action = 'ADD';
    $params = null;
    $mform = new upload_form();
}


// Build a button for viewing files.
$htmltidbits = html_writer::start_tag( 'a', array( 'href' => "./view.php" ) )
        .html_writer::start_tag( 'button', array( 'type' => 'button', 'class' => 'btn btn-primary', 'style' =>'margin:3%; width:20%' ) )
        .format_string( get_string('manage_viewer', 'block_pu') )
        .html_writer::end_tag('button')
        .html_writer::end_tag( 'a' );

// IF we cancel, redirect.
if ( $mform->is_cancelled() ) {
     redirect($viewlink);

} else if ( $formdata = $mform->get_data() ) {

    // Saves the form loaded file to the database in the files table.
    file_save_draft_area_files(
        // $formdata->attachments,
        $formdata->pu_file,
        $context->id,
        'block_pu',
        'pu_file',
        // $formdata->attachments,
        $formdata->pu_file,
        $mform->get_filemanager_options_array()
    );

    $content = $mform->get_file_content('pu_file');

    // To get the name of the uploaded file
    $name = $mform->get_new_filename('pu_file');

    // Get the path from the PU settings.
    $pupath = get_config('moodle', "block_pu_copy_file");

    // Make sure the folder is there.
    if (!is_dir($pupath)) {
        mkdir($pupath, 0777, true);
    }

    // Build the stored file from the form.
    $storedfile = $mform->save_stored_file(
        'pu_file',
        $context->id,
        'pu_file2',
        "",
        "",
        $pupath,
        null,
        false
    );

    // Save or update in local table uploadfile_files.
    if ($action == 'ADD') {
        $model->save($formdata);
    } else {
        $formdata->id = $fileid;
        $model->update($formdata);
    }
    redirect ($viewlink);
}

// Output the header.
echo $OUTPUT->header();

// Output the custom html.
echo $htmltidbits;

// Output the form.
$mform->display();

// output the footer.
echo $OUTPUT->footer();
