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
 * Import attendance sessions
 *
 * @package   mod_attendance
 * @author    Simon Thornett <simon.thornett@catalyst-eu.net>
 * @copyright Catalyst IT, 2022
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once(dirname(__FILE__) . '/renderables.php');
require_once(dirname(__FILE__) . '/renderhelpers.php');
require_once($CFG->libdir . '/formslib.php');

$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('attendance', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$att = $DB->get_record('attendance', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/attendance:import', $context);

$att = new mod_attendance_structure($att, $cm, $course, $context);

$PAGE->set_url($att->url_import());
$PAGE->set_title($course->shortname . ": " . $att->name);
$PAGE->set_heading($course->fullname);
$PAGE->force_settings_menu(true);
$PAGE->set_cacheable(true);
$PAGE->navbar->add(get_string('import', 'attendance'));

$formparams = array('course' => $course, 'cm' => $cm, 'modcontext' => $context);

$form = null;
if (optional_param('confirm', 0, PARAM_BOOL)) {
    $importer = new \mod_attendance\import\sessions(null, null, null, 0, null, false, $course->shortname, $att->id);
    $form = new \mod_attendance\form\import\sessions_confirm($att->url_import(), $importer);
} else {
    $form = new \mod_attendance\form\import\sessions($att->url_import(), $formparams);
}

if ($form->is_cancelled()) {
    $form = new \mod_attendance\form\import\sessions($att->url_import(), $formparams);
} else if ($data = $form->get_data()) {
    require_sesskey();
    if ($data->confirm) {
        $importid = $data->importid;
        $importer = new \mod_attendance\import\sessions(
            $att->url_import(),
            null,
            null,
            $importid,
            $data,
            false,
            $course->shortname,
            $att->id
        );

        $error = $importer->get_error();
        if ($error) {
            $form = new \mod_attendance\form\import\sessions($att->url_import(), $formparams);
            $form->set_import_error($error);
        } else {
            $importer->import();
            redirect($att->url_import());
        }
    } else {
        $text = $form->get_file_content('importfile');
        $encoding = $data->encoding;
        $delimiter = $data->delimiter_name;
        $importer = new \mod_attendance\import\sessions($text, $encoding, $delimiter, 0, null, false, $course->shortname, $att->id);
        $confirmform = new \mod_attendance\form\import\sessions_confirm($att->url_import(), $importer);
        $form = $confirmform;
        $pagetitle = get_string('confirmcolumnmappings', 'attendance');
    }
}

$output = $PAGE->get_renderer('mod_attendance');
echo $output->header();
mod_attendance_notifyqueue::show();

$form->display();

echo $OUTPUT->footer();


