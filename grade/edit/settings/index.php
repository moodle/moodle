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
 * A page for editing course grade settings
 *
 * @package   core_grades
 * @copyright 2007 Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once '../../../config.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->libdir.'/gradelib.php';
require_once 'form.php';

$courseid  = optional_param('id', SITEID, PARAM_INT);

$PAGE->set_url('/grade/edit/settings/index.php', array('id'=>$courseid));
$PAGE->set_pagelayout('admin');

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    throw new \moodle_exception('invalidcourseid');
}
require_login($course);
$context = context_course::instance($course->id);

require_capability('moodle/grade:manage', $context);

$gpr = new grade_plugin_return(array('type'=>'edit', 'plugin'=>'settings', 'courseid'=>$courseid));

$strgrades = get_string('grades');
$pagename  = get_string('coursesettings', 'grades');

$mform = new course_settings_form();

$settings = grade_get_settings($course->id);

$mform->set_data($settings);

if ($data = $mform->get_data()) {
    $data = (array)$data;
    $general = array('displaytype', 'decimalpoints', 'aggregationposition', 'minmaxtouse');
    foreach ($data as $key=>$value) {
        if (!in_array($key, $general) and strpos($key, 'report_') !== 0
                                      and strpos($key, 'import_') !== 0
                                      and strpos($key, 'export_') !== 0) {
            continue;
        }
        if ($value == -1) {
            $value = null;
        }
        grade_set_setting($course->id, $key, $value);

        $previousvalue = isset($settings->{$key}) ? $settings->{$key} : null;
        if ($key == 'minmaxtouse' && $previousvalue != $value) {
            // The min max has changed, we need to regrade the grades.
            grade_force_full_regrading($courseid);
        }
    }
}

print_grade_page_head($courseid, 'settings', 'coursesettings');

// The settings could have been changed due to a notice shown in print_grade_page_head, we need to refresh them.
$settings = grade_get_settings($course->id);
$mform->set_data($settings);

echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthnormal centerpara');
echo get_string('coursesettingsexplanation', 'grades');
echo $OUTPUT->box_end();

$mform->display();

echo $OUTPUT->footer();


