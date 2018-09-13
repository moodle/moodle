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
 * Import attendance sessions.
 *
 * @package mod_attendance
 * @author Chris Wharton <chriswharton@catalyst.net.nz>
 * @copyright 2017 Catalyst IT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/mod/attendance/lib.php');
require_once($CFG->dirroot . '/mod/attendance/locallib.php');

admin_externalpage_setup('managemodules');
$pagetitle = get_string('importsessions', 'attendance');

$context = context_system::instance();

$url = new moodle_url('/mod/attendance/import/sessions.php');

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title($pagetitle);
$PAGE->set_pagelayout('admin');
$PAGE->set_heading($pagetitle);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('importsessions', 'attendance'));
$tabmenu = attendance_print_settings_tabs('importsessions');
echo $tabmenu;

$form = null;
if (optional_param('needsconfirm', 0, PARAM_BOOL)) {
    $form = new \mod_attendance\form\import\sessions($url->out(false));
} else if (optional_param('confirm', 0, PARAM_BOOL)) {
    $importer = new \mod_attendance\import\sessions();
    $form = new \mod_attendance\form\import\sessions_confirm(null, $importer);
} else {
    $form = new \mod_attendance\form\import\sessions($url->out(false));
}

if ($form->is_cancelled()) {
    $form = new \mod_attendance\form\import\sessions($url->out(false));
} else if ($data = $form->get_data()) {
    require_sesskey();
    if ($data->confirm) {
        $importid = $data->importid;
        $importer = new \mod_attendance\import\sessions(null, null, null, $importid, $data, true);

        $error = $importer->get_error();
        if ($error) {
            $form = new \mod_attendance\form\import\sessions($url->out(false));
            $form->set_import_error($error);
        } else {
            $sessions = $importer->import();
            mod_attendance_notifyqueue::show();
            echo $OUTPUT->continue_button($url);
            die();
        }
    } else {
        $text = $form->get_file_content('importfile');
        $encoding = $data->encoding;
        $delimiter = $data->delimiter_name;
        $importer = new \mod_attendance\import\sessions($text, $encoding, $delimiter, 0, null, true);
        $confirmform = new \mod_attendance\form\import\sessions_confirm(null, $importer);
        $form = $confirmform;
        $pagetitle = get_string('confirmcolumnmappings', 'attendance');
    }
}

echo $OUTPUT->heading($pagetitle);

$form->display();

echo $OUTPUT->footer();
