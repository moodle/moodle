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
 * Grader report preferences configuration page
 *
 * @package   gradereport_grader
 * @copyright 2007 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once '../../../config.php';
require_once $CFG->libdir . '/gradelib.php';
require_once '../../lib.php';
core_php_time_limit::raise();

$courseid      = required_param('id', PARAM_INT);

$PAGE->set_url(new moodle_url('/grade/report/grader/preferences.php', array('id'=>$courseid)));
$PAGE->set_pagelayout('admin');

/// Make sure they can even access this course

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}

require_login($course);

$context = context_course::instance($course->id);
$systemcontext = context_system::instance();
require_capability('gradereport/grader:view', $context);

require('preferences_form.php');
$mform = new grader_report_preferences_form('preferences.php', compact('course'));

// If data submitted, then process and store.
if (!$mform->is_cancelled() && $data = $mform->get_data()) {
    foreach ($data as $preference => $value) {
        if (substr($preference, 0, 6) !== 'grade_') {
            continue;
        }

        if ($value == GRADE_REPORT_PREFERENCE_DEFAULT || strlen($value) == 0) {
            unset_user_preference($preference);
        } else {
            set_user_preference($preference, $value);
        }
    }

    redirect($CFG->wwwroot . '/grade/report/grader/index.php?id='.$courseid); // message here breaks accessability and is sloooowww
    exit;
}

if ($mform->is_cancelled()){
    redirect($CFG->wwwroot . '/grade/report/grader/index.php?id='.$courseid);
}

print_grade_page_head($courseid, 'preferences', 'grader', get_string('preferences', 'gradereport_grader'));

// If USER has admin capability, print a link to the site config page for this report
if (has_capability('moodle/site:config', $systemcontext)) {
    echo '<div id="siteconfiglink"><a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/settings.php?section=gradereportgrader">';
    echo get_string('changereportdefaults', 'grades');
    echo "</a></div>\n";
}

echo $OUTPUT->box_start();

$mform->display();
echo $OUTPUT->box_end();

echo $OUTPUT->footer();

