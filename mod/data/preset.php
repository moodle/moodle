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
 * Preset Menu
 *
 * This is the page that is the menu item in the config database
 * pages.
 *
 * This file is part of the Database module for Moodle
 *
 * @copyright 2005 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package mod_data
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/data/lib.php');
require_once($CFG->dirroot.'/mod/data/preset_form.php');

$id = optional_param('id', 0, PARAM_INT); // The course module id.

if ($id) {
    $cm = get_coursemodule_from_id('data', $id, null, null, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $data = $DB->get_record('data', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    $d = required_param('d', PARAM_INT);     // database activity id
    $data = $DB->get_record('data', array('id' => $d), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $data->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('data', $data->id, $course->id, null, MUST_EXIST);
}

$action = optional_param('action', 'view', PARAM_ALPHA); // The page action.
$allowedactions = ['view', 'delete', 'confirmdelete', 'import', 'importzip', 'finishimport',
    'export'];
if (!in_array($action, $allowedactions)) {
    throw new moodle_exception('invalidaccess');
}

$context = context_module::instance($cm->id, MUST_EXIST);
require_login($course, false, $cm);
require_capability('mod/data:managetemplates', $context);

$url = new moodle_url('/mod/data/preset.php', array('d' => $data->id));

$PAGE->set_url($url);
$PAGE->set_title(get_string('course') . ': ' . $course->fullname);
$PAGE->set_heading($course->fullname);
$PAGE->force_settings_menu(true);
$PAGE->activityheader->disable();

// fill in missing properties needed for updating of instance
$data->course     = $cm->course;
$data->cmidnumber = $cm->idnumber;
$data->instance   = $cm->instance;

$renderer = $PAGE->get_renderer('mod_data');
$presets = data_get_available_presets($context);

if ($action === 'export') {
    if (headers_sent()) {
        print_error('headersent');
    }

    $exportfile = data_presets_export($course, $cm, $data);
    $exportfilename = basename($exportfile);
    header("Content-Type: application/download\n");
    header("Content-Disposition: attachment; filename=\"$exportfilename\"");
    header('Expires: 0');
    header('Cache-Control: must-revalidate,post-check=0,pre-check=0');
    header('Pragma: public');

    // If this file was requested from a form, then mark download as complete.
    \core_form\util::form_download_complete();

    $exportfilehandler = fopen($exportfile, 'rb');
    print fread($exportfilehandler, filesize($exportfile));
    fclose($exportfilehandler);
    unlink($exportfile);
    exit(0);
}

$formimportzip = new data_import_preset_zip_form();
$formimportzip->set_data(array('d' => $data->id));

if ($formimportzip->is_cancelled()) {
    redirect(new moodle_url('/mod/data/preset.php', ['d' => $data->id]));
}

echo $OUTPUT->header();

if ($formdata = $formimportzip->get_data()) {
    echo $OUTPUT->heading(get_string('importpreset', 'data'), 2, 'mb-4');
    $file = new stdClass;
    $file->name = $formimportzip->get_new_filename('importfile');
    $file->path = $formimportzip->save_temp_file('importfile');
    $importer = new data_preset_upload_importer($course, $cm, $data, $file->path);
    echo $renderer->import_setting_mappings($data, $importer);
    echo $OUTPUT->footer();
    exit(0);
}

if (in_array($action, ['confirmdelete', 'delete', 'finishimport'])) {
    $fullname = optional_param('fullname', '' , PARAM_PATH); // The directory the preset is in.
    // Find out preset owner userid and shortname.
    $parts = explode('/', $fullname, 2);
    $userid = empty($parts[0]) ? 0 : (int)$parts[0];
    $shortname = empty($parts[1]) ? '' : $parts[1];
    echo html_writer::start_div('overflow-hidden');

    if ($action === 'confirmdelete') {
        $path = data_preset_path($course, $userid, $shortname);
        $strwarning = get_string('deletewarning', 'data').'<br />'.$shortname;
        $optionsyes = [
            'fullname' => $fullname,
            'action' => 'delete',
            'd' => $data->id,
        ];
        $optionsno = ['d' => $data->id];
        echo $OUTPUT->confirm($strwarning, new moodle_url('/mod/data/preset.php', $optionsyes),
            new moodle_url('/mod/data/preset.php', $optionsno));
        echo $OUTPUT->footer();
        exit(0);
    } else if ($action === 'delete') {
        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey');
        }
        $selectedpreset = new stdClass();
        foreach ($presets as $preset) {
            if ($preset->shortname == $shortname) {
                $selectedpreset = $preset;
            }
        }
        if (!isset($selectedpreset->shortname) || !data_user_can_delete_preset($context, $selectedpreset)) {
            print_error('invalidrequest');
        }

        data_delete_site_preset($shortname);
        $strdeleted = get_string('deleted', 'data');
        echo $OUTPUT->notification("$shortname $strdeleted", 'notifysuccess');
    } else if ($action === 'finishimport') {
        if (!confirm_sesskey()) {
            throw new moodle_exception('invalidsesskey');
        }
        $overwritesettings = optional_param('overwritesettings', false, PARAM_BOOL);
        if (!$fullname) {
            $presetdir = $CFG->tempdir.'/forms/'.required_param('directory', PARAM_FILE);
            if (!file_exists($presetdir) || !is_dir($presetdir)) {
                print_error('cannotimport');
            }
            $importer = new data_preset_upload_importer($course, $cm, $data, $presetdir);
        } else {
            $importer = new data_preset_existing_importer($course, $cm, $data, $fullname);
        }
        $importer->import($overwritesettings);
        $strimportsuccess = get_string('importsuccess', 'data');
        $straddentries = get_string('addentries', 'data');
        $strtodatabase = get_string('todatabase', 'data');
        if (!$DB->get_records('data_records', array('dataid'=>$data->id))) {
            echo $OUTPUT->notification("$strimportsuccess <a href='edit.php?d=$data->id'>$straddentries</a> $strtodatabase", 'notifysuccess');
        } else {
            echo $OUTPUT->notification("$strimportsuccess", 'notifysuccess');
        }
    }
    echo $OUTPUT->continue_button(new moodle_url('/mod/data/preset.php', ['d' => $data->id]));
    echo html_writer::end_div();
    echo $OUTPUT->footer();
    exit(0);
}

if ($action === 'import') {
    echo $OUTPUT->heading(get_string('importpreset', 'data'), 2, 'mb-4');
    echo $formimportzip->display();
} else {
    $actionbar = new \mod_data\output\action_bar($data->id, $url);
    echo $actionbar->get_presets_action_bar();
    echo $OUTPUT->heading(get_string('presets', 'data'), 2, 'mb-4');
    $presets = new \mod_data\output\presets($data->id, $presets, new \moodle_url('/mod/data/field.php'), true);
    echo $renderer->render_presets($presets);
}

echo $OUTPUT->footer();
