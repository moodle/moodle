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
 * A page for selecting outcomes for use in a course
 *
 * @package   core_grades
 * @copyright 2007 Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once '../../../config.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->libdir.'/gradelib.php';

$courseid = required_param('id', PARAM_INT);

$PAGE->set_url('/grade/edit/outcome/course.php', array('id'=>$courseid));

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

/// Make sure they can even access this course
require_login($course);
$context = context_course::instance($course->id);
require_capability('moodle/course:update', $context);

/// return tracking object
$gpr = new grade_plugin_return(array('type'=>'edit', 'plugin'=>'outcomes', 'courseid'=>$courseid));

// first of all fix the state of outcomes_course table
$standardoutcomes    = grade_outcome::fetch_all_global();
$co_custom           = grade_outcome::fetch_all_local($courseid);
$co_standard_used    = array();
$co_standard_notused = array();

if ($courseused = $DB->get_records('grade_outcomes_courses', array('courseid' => $courseid), '', 'outcomeid')) {
    $courseused = array_keys($courseused);
} else {
    $courseused = array();
}

// fix wrong entries in outcomes_courses
foreach ($courseused as $oid) {
    if (!array_key_exists($oid, $standardoutcomes) and !array_key_exists($oid, $co_custom)) {
        $DB->delete_records('grade_outcomes_courses', array('outcomeid' => $oid, 'courseid' => $courseid));
    }
}

// fix local custom outcomes missing in outcomes_course
foreach($co_custom as $oid=>$outcome) {
    if (!in_array($oid, $courseused)) {
        $courseused[$oid] = $oid;
        $goc = new stdClass();
        $goc->courseid = $courseid;
        $goc->outcomeid = $oid;
        $DB->insert_record('grade_outcomes_courses', $goc);
    }
}

// now check all used standard outcomes are in outcomes_course too
$params = array($courseid);
$sql = "SELECT DISTINCT outcomeid
          FROM {grade_items}
         WHERE courseid=? and outcomeid IS NOT NULL";
if ($realused = $DB->get_records_sql($sql, $params)) {
    $realused = array_keys($realused);
    foreach ($realused as $oid) {
        if (array_key_exists($oid, $standardoutcomes)) {

            $co_standard_used[$oid] = $standardoutcomes[$oid];
            unset($standardoutcomes[$oid]);

            if (!in_array($oid, $courseused)) {
                $courseused[$oid] = $oid;
                $goc = new stdClass();
                $goc->courseid = $courseid;
                $goc->outcomeid = $oid;
                $DB->insert_record('grade_outcomes_courses', $goc);
            }
        }
    }
}

// find all unused standard course outcomes - candidates for removal
foreach ($standardoutcomes as $oid=>$outcome) {
    if (in_array($oid, $courseused)) {
        $co_standard_notused[$oid] = $standardoutcomes[$oid];
        unset($standardoutcomes[$oid]);
    }
}


/// form processing
if ($data = data_submitted() and confirm_sesskey()) {
    require_capability('moodle/grade:manageoutcomes', $context);
    if (!empty($data->add) && !empty($data->addoutcomes)) {
    /// add all selected to course list
        foreach ($data->addoutcomes as $add) {
            $add = clean_param($add, PARAM_INT);
            if (!array_key_exists($add, $standardoutcomes)) {
                continue;
            }
            $goc = new stdClass();
            $goc->courseid = $courseid;
            $goc->outcomeid = $add;
            $DB->insert_record('grade_outcomes_courses', $goc);
        }

    } else if (!empty($data->remove) && !empty($data->removeoutcomes)) {
    /// remove all selected from course outcomes list
        foreach ($data->removeoutcomes as $remove) {
            $remove = clean_param($remove, PARAM_INT);
            if (!array_key_exists($remove, $co_standard_notused)) {
                continue;
            }
            $DB->delete_records('grade_outcomes_courses', array('courseid' => $courseid, 'outcomeid' => $remove));
        }
    }
    redirect('course.php?id='.$courseid); // we must redirect to get fresh data
}

/// Print header
print_grade_page_head($COURSE->id, 'outcome', 'course');

require('course_form.html');

echo $OUTPUT->footer();

