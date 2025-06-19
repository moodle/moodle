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
 * The gradebook builder report
 *
 * @package   gradereport_gradebook_builder
 * @copyright 
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once '../../../config.php';
require_once $CFG->libdir . '/gradelib.php';
require_once $CFG->dirroot . '/grade/lib.php';
require_once $CFG->dirroot . '/grade/report/gradebook_builder/lib.php';

$courseid = required_param('id', PARAM_INT);
$template_id = optional_param('template', null, PARAM_INT);

$PAGE->requires->jquery();
$PAGE->requires->js('/grade/report/gradebook_builder/app.js');
$PAGE->requires->css('/grade/report/gradebook_builder/grid.css');
$PAGE->requires->css('/grade/report/gradebook_builder/app.css');

$PAGE->set_url(new moodle_url('/grade/report/gradebook_builder/index.php', array('id' => $courseid)));

/// basic access checks
if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}
require_login($course);
$PAGE->set_pagelayout('report');

$context = context_course::instance($course->id);
require_capability('gradereport/gradebook_builder:view', $context);
require_capability('moodle/grade:edit', $context);

$gpr = new grade_plugin_return(array(
    'type' => 'report',
    'plugin' => 'gradebook_builder',
    'courseid' => $courseid
));

/// last selected report session tracking
if (!isset($USER->grade_last_report)) {
    $USER->grade_last_report = array();
}
$USER->grade_last_report[$course->id] = 'gradebook_builder';

$template = $DB->get_record('gradereport_builder_template', array(
    'id' => $template_id
));

$report = new grade_report_gradebook_builder($courseid, $gpr, $context, $template);

if ($data = data_submitted() and !$report::is_gradebook_established($courseid)) {
    $report->process_data($data);
}

print_grade_page_head($course->id, 'report', 'gradebook_builder', get_string('pluginname', 'gradereport_gradebook_builder'));

if ($report::is_gradebook_established($courseid)) {
    $gradebook_url = new moodle_url('/grade/edit/tree/index.php', array(
        'id' => $course->id
    ));
    echo $OUTPUT->notification(get_string('items', 'gradereport_gradebook_builder'));
    echo $OUTPUT->continue_button($gradebook_url);
} else {
    $report->output();
}

echo $OUTPUT->footer();
