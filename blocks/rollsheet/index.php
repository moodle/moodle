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

//
// FILE MANAGER EXAMPLE
//
// @Author: Andy Normore
// @Author: Davo Smith
// https://github.com/AndyNormore/MoodleFileManager.

// The point of this file is to demonstrate how to manage files within Moodle 2.3
// Why? Because file management is incredibly hard for some reason.
// This file is built to run as STANDALONE, no external files or strings. Just 100% easy to understand! (Noob friendly)
// Thanks to Davo Smith for helping to create this project.

// Standard Moodle Setup.
require_once( '../../config.php' );
global $CFG, $USER, $DB, $OUTPUT, $PAGE;

$PAGE->set_url('/local/filemanager/index.php');
require_login();

$PAGE->set_pagelayout( 'admin' );

// Choose the most appropriate context for your file manager - e.g. block, course, course module.
// This example uses the system context (as we are in a 'local' plugin without any other context).
// This is VERY important, the filemanager MUST have a valid context!
$context = context_system::instance();
$PAGE->set_context( $context );

// Setup the page.
$PAGE->set_title(get_string('uploadimage', 'block_rollsheet'));
$PAGE->set_heading(get_string('uploadimage', 'block_rollsheet'));

// DEFINITIONS.
require_once($CFG->libdir.'/formslib.php');
require_once('lib.php');

// PAGE LOGIC.
// Create some options for the file manager.
$filemanageropts = array('subdirs' => 0, 'maxbytes' => '0', 'maxfiles' => 1, 'context' => $context);
$customdata = array('filemanageropts' => $filemanageropts);

// Create a new form object (found in lib.php).
$mform = new simplehtml_form(null, $customdata);

// CONFIGURE FILE MANAGER.
// From http://docs.moodle.org/dev/Using_the_File_API_in_Moodle_forms#filemanager.
$itemid = 0; // This is used to distinguish between multiple file areas.

// Fetches the file manager draft area, called 'attachments'.
$draftitemid = file_get_submitted_draft_itemid('attachments');

// Copy all the files from the 'real' area, into the draft area.
file_prepare_draft_area($draftitemid, $context->id, 'block_rollsheet', 'attachment', $itemid, $filemanageropts);

// Prepare the data to pass into the form. Normally we would load this from a database, but, here, we have no 'real' record to load.
$entry = new stdClass();
$entry->attachments = $draftitemid; // Add the draftitemid to the form, so that 'file_get_submitted_draft_itemid' can retrieve it.

// Set form data. This will load the file manager with your previous files.
$mform->set_data($entry);

// PAGE OUTPUT.
echo $OUTPUT->header();

// Form Submit Status.
if ($mform->is_cancelled()) {
    // CANCELLED.
    echo '<h1>Cancelled</h1>';
} else if ($data = $mform->get_data()) {
    // Save the files submitted.
    file_save_draft_area_files($draftitemid, $context->id, 'block_rollsheet', 'attachment', $itemid, $filemanageropts);
    $fs = get_file_storage();
    if ($files = $fs->get_area_files($context->id, 'block_rollsheet', 'attachment', '0', 'sortorder', false)) {

        // Look through each file being managed.
        foreach ($files as $file) {
            // Build the File URL. Long process! But extremely accurate.
            $fileurl = moodle_url::make_pluginfile_url($file->get_contextid()
                                                    , $file->get_component()
                                                    , $file->get_filearea()
                                                    , $file->get_itemid()
                                                    , $file->get_filepath()
                                                    , $file->get_filename());
            $cleanpath = (string)$fileurl;
        }
    }

    global $DB, $CFG;

    $recordexists = $DB->get_record('block_rollsheet', array('id' => 1));

    if ($recordexists) {

            $record = new stdClass();
            $record->id = 1;
            $record->fieldvarname = 'imgurl';
            $record->field_value = $cleanpath;

            $DB->update_record('block_rollsheet', $record, $bulk = false);
    } else {
                    $record = new stdClass();
                    $record->fieldvarname = 'imgurl';
                    $record->field_value = $cleanpath;
                    $DB->insert_record('block_rollsheet', $record, false);
    }
    echo '<script>window.location="../../admin/settings.php?section=blocksettingrollsheet";</script>';
} else {
    $mform->display();
}
echo $OUTPUT->footer();