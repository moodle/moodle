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

use mod_data\local\importer\preset_importer;
use mod_data\local\importer\preset_upload_importer;
use mod_data\manager;
use mod_data\preset;
use mod_data\output\action_bar;
use mod_data\output\preset_preview;

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/data/lib.php');
require_once($CFG->dirroot.'/mod/data/preset_form.php');

// The course module id.
$id = optional_param('id', 0, PARAM_INT);

$manager = null;
if ($id) {
    list($course, $cm) = get_course_and_cm_from_cmid($id, manager::MODULE);
    $manager = manager::create_from_coursemodule($cm);
    $data = $manager->get_instance();
} else {
    // We must have the database activity id.
    $d = required_param('d', PARAM_INT);
    $data = $DB->get_record('data', ['id' => $d], '*', MUST_EXIST);
    $manager = manager::create_from_instance($data);
    $cm = $manager->get_coursemodule();
    $course = get_course($cm->course);
}

$action = optional_param('action', 'view', PARAM_ALPHA); // The page action.
$allowedactions = ['view', 'importzip', 'finishimport',
    'export', 'preview'];
if (!in_array($action, $allowedactions)) {
    throw new moodle_exception('invalidaccess');
}

$context = $manager->get_context();

require_login($course, false, $cm);
require_capability('mod/data:managetemplates', $context);

$url = new moodle_url('/mod/data/preset.php', array('d' => $data->id));

$PAGE->add_body_class('limitedwidth');
$PAGE->set_url($url);
$titleparts = [
    get_string('presets', 'data'),
    format_string($cm->name),
    format_string($course->fullname),
];
$PAGE->set_title(implode(moodle_page::TITLE_SEPARATOR, $titleparts));
$PAGE->set_heading($course->fullname);
$PAGE->force_settings_menu(true);
$PAGE->activityheader->disable();
$PAGE->requires->js_call_amd('mod_data/deletepreset', 'init');

// fill in missing properties needed for updating of instance
$data->course     = $cm->course;
$data->cmidnumber = $cm->idnumber;
$data->instance   = $cm->instance;

$renderer = $manager->get_renderer();
$presets = $manager->get_available_presets();

if ($action === 'export') {
    if (headers_sent()) {
        throw new \moodle_exception('headersent');
    }

    // Check if we should export a given preset or the current one.
    $presetname = optional_param('presetname', $data->name, PARAM_FILE);

    $preset = preset::create_from_instance($manager, $presetname);
    $exportfile = $preset->export();
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


if ($action == 'importzip') {
    $filepath = optional_param('filepath', '', PARAM_PATH);
    $importer = new preset_upload_importer($manager, $CFG->tempdir . $filepath);
    if ($importer->needs_mapping()) {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('fieldmappings', 'data'), 2, 'mb-4');
        echo $renderer->importing_preset($data, $importer);
        echo $OUTPUT->footer();
        exit(0);
    }
    $importer->import(false);
    core\notification::success(get_string('importsuccess', 'mod_data'));
    redirect(new moodle_url('/mod/data/field.php', ['id' => $cm->id]));
    exit(0);
}

// Preset preview injects CSS and JS to the page and should be done before the page header.
if ($action === 'preview') {
    $fullname = optional_param('fullname', '', PARAM_PATH); // The directory the preset is in.
    $templatename = optional_param('template', 'listtemplate', PARAM_ALPHA);
    // Find out preset owner userid and shortname.
    $preset = preset::create_from_fullname($manager, $fullname);
    // Validate if the user can view this preset.
    if (!$manager->can_view_preset($preset)) {
        throw new \moodle_exception('cannotaccesspresentsother', manager::PLUGINNAME);
    }
    $preview = new preset_preview($manager, $preset, $templatename);
    $preview->prepare_page($PAGE);
    $url->params([
        'fullname' => $fullname,
        'template' => $templatename,
    ]);
    $PAGE->set_url($url);
    $titleparts = [
        get_string('preview', 'data', $preset->name),
        format_string($cm->name),
        format_string($course->fullname),
    ];
    $PAGE->set_title(implode(moodle_page::TITLE_SEPARATOR, $titleparts));
    // Print the preview screen.
    echo $OUTPUT->header();
    $actionbar = new action_bar($data->id, $url);
    echo $actionbar->get_presets_preview_action_bar($manager, $fullname, $templatename);
    echo $renderer->render($preview);
    echo $OUTPUT->footer();
    exit(0);
}

if ($action === 'finishimport') {
    if (!confirm_sesskey()) {
        throw new moodle_exception('invalidsesskey');
    }
    $overwritesettings = optional_param('overwritesettings', false, PARAM_BOOL);
    $importer = preset_importer::create_from_parameters($manager);
    $importer->finish_import_process($overwritesettings, $data);
}

echo $OUTPUT->header();

$actionbar = new \mod_data\output\action_bar($data->id, $url);
echo $actionbar->get_presets_action_bar();
$presets = new \mod_data\output\presets($manager, $presets, new \moodle_url('/mod/data/field.php'), true);
echo $renderer->render_presets($presets);

echo $OUTPUT->footer();
