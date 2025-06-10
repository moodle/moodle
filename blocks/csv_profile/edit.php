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
 * CSV profile field import/update/delete block.
 *
 * @package   block_csv_profile
 * @copyright 2012 onwared Ted vd Brink, Brightally custom code
 * @copyright 2018 onwards Robert Russo, Louisiana State University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once("$CFG->dirroot/blocks/csv_profile/edit_form.php");
require_once("$CFG->dirroot/blocks/csv_profile/locallib.php");
require_once("$CFG->dirroot/repository/lib.php");

global $USER;
require_login();

$context = context_system::instance();
require_capability('block/csv_profile:uploadcsv', $context, $USER->id, true, "nopermissions");

$title = get_string('csvprofile', 'block_csv_profile');
$struser = get_string('user');

$PAGE->set_context($context);
$PAGE->set_url('/blocks/csv_profile/edit.php');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('standard');

$data = new stdClass();
$options = array('subdirs' => 1,
                 'maxbytes' => $CFG->userquota,
                 'maxfiles' => -1,
                 'accepted_types' => '*',
                 'return_types' => FILE_INTERNAL);

file_prepare_standard_filemanager($data, 'files', $options, $context, 'user', 'csvprofile', 0);

$mform = new block_csv_profile_form(null, array('data' => $data, 'options' => $options));

$formdata = $mform->get_data();
// 3 options: file uploaded, cancelled, or saved.
if ($mform->is_cancelled()) {
    redirect(new moodle_url($CFG->wwwroot . '/blocks/csv_profile/edit.php'));
} else if ($formdata && $mform->get_file_content('userfile')) {
    // Upload file, store, and process csv.
    $content = $mform->get_file_content('userfile'); // Save uploaded file.
    $fs = get_file_storage();

    $profilefield = $formdata->profilefield;

    // Cleanup old files:
    // First, create target directory.
    if (!$fs->file_exists($context->id, 'user', 'csvprofile', 0, '/', 'History')) {
        $fs->create_directory($context->id, 'user', 'csvprofile', 0, '/History/', $USER->id);
    }

    // Second, create logs directory.
    if (!$fs->file_exists($context->id, 'user', 'csvprofile', 0, '/', 'Logs')) {
        $fs->create_directory($context->id, 'user', 'csvprofile', 0, '/Logs/', $USER->id);
    }

    // Third, move all files to created dir.
    $areafiles = $fs->get_area_files($context->id, 'user', 'csvprofile', false, "filename", false);
    $filechanges = array('filepath' => '/History/');
    foreach ($areafiles as $key => $areafile) {
        if ($areafile->get_filepath() == '/') {
            $fs->create_file_from_storedfile($filechanges, $areafile); // Copy file to new location.
            $areafile->delete(); // Remove old copy.
        }
    }

    $filename = 'upload_' . date('Ymd_His') . '.csv';

    // Prepare file record object
    $fileinfo = array('contextid' => $context->id, // ID of context.
                      'component' => 'user', // usually = table name.
                      'filearea' => 'csvprofile', // usually = table name.
                      'itemid' => 0, // usually = ID of row in table.
                      'filepath' => '/', // Any path beginning and ending in /.
                      'filename' => $filename, // Any filename.
                      'userid' => $USER->id);

    // Create file containing uploaded file content.
    $newfile = $fs->create_file_from_string($fileinfo, $content);

    // Read CSV and get results.
    $log = block_csv_profile_update_users($content, $profilefield);

    // Save log file, reuse fileinfo from csv file.
    $fileinfo['filename'] = "upload_".date("Ymd_His")."_log.txt";

    // Change path for log storage.
    $fileinfo['filepath'] = "/Logs/";

    $newfile = $fs->create_file_from_string($fileinfo, $log);

    // Back to main page.
    redirect(new moodle_url($CFG->wwwroot . ('/blocks/csv_profile/edit.php')));
} else if ($formdata && !$mform->get_file_content('userfile')) {
    // Just show the updated filemanager.
    $formdata = file_postupdate_standard_filemanager($formdata, 'files', $options, $context, 'user', 'csvprofile', 0);
}

echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox');
$mform->display();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
