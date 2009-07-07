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


require_once '../../config.php';

$courseid = required_param('id', PARAM_INT);

/// basic access checks
if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}
require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);

/// find all accessible reports
if ($reports = get_list_of_plugins('grade/report', 'CVS')) {     // Get all installed reports
    foreach ($reports as $key => $plugin) {                      // Remove ones we can't see
        if (!has_capability('gradereport/'.$plugin.':view', $context)) {
            unset($reports[$key]);
        }
    }
}

if (empty($reports)) {
    error('No reports accessible', $CFG->wwwroot.'/course/view.php?id='.$course->id); // TODO: localize
}

if (!isset($USER->grade_last_report)) {
    $USER->grade_last_report = array();
}

if (!empty($USER->grade_last_report[$course->id])) {
    $last = $USER->grade_last_report[$course->id];
} else {
    $last = null;
}

if (!in_array($last, $reports)) {
    $last = null;
}

if (empty($last)) {
    if (in_array('grader', $reports)) {
        $last = 'grader';

    } else if (in_array('user', $reports)) {
        $last = 'user';

    } else {
        $last = reset($reports);
    }
}

//redirect to last or guessed report
redirect($CFG->wwwroot.'/grade/report/'.$last.'/index.php?id='.$course->id);

?>
