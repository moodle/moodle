<?php
// This file is part of the Checklist plugin for Moodle - http://moodle.org/
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

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/importexportfields.php');
global $DB, $PAGE, $CFG;
require_once($CFG->libdir.'/csvlib.class.php');
$id = required_param('id', PARAM_INT); // Course module id.

$cm = get_coursemodule_from_id('checklist', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$checklist = $DB->get_record('checklist', array('id' => $cm->instance), '*', MUST_EXIST);

$url = new moodle_url('/mod/checklist/export.php', array('id' => $cm->id));
$PAGE->set_url($url);
require_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/checklist:edit', $context);

$items = $DB->get_records_select('checklist_item', "checklist = ? AND userid = 0", array($checklist->id), 'position');
if (!$items) {
    print_error('noitems', 'mod_checklist');
}

$csv = new csv_export_writer();
$strchecklist = get_string('checklist', 'checklist');
$csv->filename = clean_filename("{$course->shortname} $strchecklist {$checklist->name}").'.csv';

// Output the headings.
$csv->add_data($fields);

foreach ($items as $item) {
    $output = array();
    foreach ($fields as $field => $unused) {
        $output[] = $item->$field;
    }
    $csv->add_data($output);
}

$csv->download_file();
