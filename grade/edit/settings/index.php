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

require_once '../../../config.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->libdir.'/gradelib.php';
require_once 'form.php';

$courseid  = optional_param('id', SITEID, PARAM_INT);

$PAGE->set_url('/grade/edit/settings/index.php', array('id'=>$courseid));
$PAGE->set_pagelayout('admin');

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}
require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);

require_capability('moodle/grade:manage', $context);

$gpr = new grade_plugin_return(array('type'=>'edit', 'plugin'=>'settings', 'courseid'=>$courseid));

$strgrades = get_string('grades');
$pagename  = get_string('coursesettings', 'grades');

$navigation = grade_build_nav(__FILE__, $pagename, $courseid);

$returnurl = $CFG->wwwroot.'/grade/index.php?id='.$course->id;

$mform = new course_settings_form();

$settings = grade_get_settings($course->id);

$mform->set_data($settings);

if ($mform->is_cancelled()) {
    redirect($returnurl);

} else if ($data = $mform->get_data()) {
    $data = (array)$data;
    $general = array('displaytype', 'decimalpoints', 'aggregationposition');
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
    }

    redirect($returnurl);
}

print_grade_page_head($courseid, 'settings', 'coursesettings', get_string('coursesettings', 'grades'));

echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthnormal centerpara');
echo get_string('coursesettingsexplanation', 'grades');
echo $OUTPUT->box_end();

$mform->display();

echo $OUTPUT->footer();


