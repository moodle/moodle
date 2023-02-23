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
 * Display user completion report
 *
 * @package    report
 * @subpackage completion
 * @copyright  2009 Catalyst IT Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/report/completion/lib.php');
require_once($CFG->libdir.'/completionlib.php');

$userid   = required_param('id', PARAM_INT);
$courseid = required_param('course', PARAM_INT);

$user = $DB->get_record('user', array('id'=>$userid, 'deleted'=>0), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);

$coursecontext   = context_course::instance($course->id);
$personalcontext = context_user::instance($user->id);

if ($USER->id != $user->id and has_capability('moodle/user:viewuseractivitiesreport', $personalcontext)
        and !is_enrolled($coursecontext, $USER) and is_enrolled($coursecontext, $user)) {
    //TODO: do not require parents to be enrolled in courses - this is a hack!
    require_login();
    $PAGE->set_course($course);
} else {
    require_login($course);
}

if (!report_completion_can_access_user_report($user, $course)) {
    // this should never happen
    throw new \moodle_exception('nocapability', 'report_completion');
}

$stractivityreport = get_string('activityreport');

$PAGE->set_pagelayout('admin');
$PAGE->set_url('/report/completion/user.php', array('id'=>$user->id, 'course'=>$course->id));
$PAGE->navigation->extend_for_user($user);
$PAGE->navigation->set_userid_for_parent_checks($user->id); // see MDL-25805 for reasons and for full commit reference for reversal when fixed.
$PAGE->set_title("$course->shortname: $stractivityreport");
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();


// Display course completion user report

// Grab all courses the user is enrolled in and their completion status
$sql = "
    SELECT DISTINCT
        c.id AS id
    FROM
        {course} c
    INNER JOIN
        {context} con
     ON con.instanceid = c.id
    INNER JOIN
        {role_assignments} ra
     ON ra.contextid = con.id
    INNER JOIN
        {enrol} e
     ON c.id = e.courseid
    INNER JOIN
        {user_enrolments} ue
     ON e.id = ue.enrolid AND ra.userid = ue.userid
    AND ra.userid = {$user->id}
";

// Get roles that are tracked by course completion
if ($roles = $CFG->gradebookroles) {
    $sql .= '
        AND ra.roleid IN ('.$roles.')
    ';
}

$sql .= '
    WHERE
        con.contextlevel = '.CONTEXT_COURSE.'
    AND c.enablecompletion = 1
';


// If we are looking at a specific course
if ($course->id != 1) {
    $sql .= '
        AND c.id = '.(int)$course->id.'
    ';
}

// Check if result is empty
$rs = $DB->get_recordset_sql($sql);
if (!$rs->valid()) {

    if ($course->id != 1) {
        $error = get_string('nocompletions', 'report_completion'); // TODO: missing string
    } else {
        $error = get_string('nocompletioncoursesenroled', 'report_completion'); // TODO: missing string
    }

    echo $OUTPUT->notification($error);
    $rs->close(); // not going to loop (but break), close rs
    echo $OUTPUT->footer();
    die();
}

// Categorize courses by their status
$courses = array(
    'inprogress'    => array(),
    'complete'      => array(),
    'unstarted'     => array()
);

// Sort courses by the user's status in each
foreach ($rs as $course_completion) {
    $c_info = new completion_info((object)$course_completion);

    // Is course complete?
    $coursecomplete = $c_info->is_course_complete($user->id);

    // Has this user completed any criteria?
    $criteriacomplete = $c_info->count_course_user_data($user->id);

    if ($coursecomplete) {
        $courses['complete'][] = $c_info;
    } else if ($criteriacomplete) {
        $courses['inprogress'][] = $c_info;
    } else {
        $courses['unstarted'][] = $c_info;
    }
}
$rs->close(); // after loop, close rs

// Loop through course status groups
foreach ($courses as $type => $infos) {

    // If there are courses with this status
    if (!empty($infos)) {

        echo '<h1 align="center">'.get_string($type, 'report_completion').'</h1>';
        echo '<table class="generaltable boxaligncenter">';
        echo '<tr class="ccheader">';
        echo '<th class="c0 header" scope="col">'.get_string('course').'</th>';
        echo '<th class="c1 header" scope="col">'.get_string('requiredcriteria', 'completion').'</th>';
        echo '<th class="c2 header" scope="col">'.get_string('status').'</th>';
        echo '<th class="c3 header" scope="col" width="15%">'.get_string('info').'</th>';

        if ($type === 'complete') {
            echo '<th class="c4 header" scope="col">'.get_string('completiondate', 'report_completion').'</th>';
        }

        echo '</tr>';

        // For each course
        foreach ($infos as $c_info) {

            // Get course info
            $c_course = $DB->get_record('course', array('id' => $c_info->course_id));
            $course_context = context_course::instance($c_course->id, MUST_EXIST);
            $course_name = format_string($c_course->fullname, true, array('context' => $course_context));

            // Get completions
            $completions = $c_info->get_completions($user->id);

            // Save row data
            $rows = array();

            // For aggregating activity completion
            $activities = array();
            $activities_complete = 0;

            // For aggregating prerequisites
            $prerequisites = array();
            $prerequisites_complete = 0;

            // Loop through course criteria
            foreach ($completions as $completion) {
                $criteria = $completion->get_criteria();
                $complete = $completion->is_complete();

                // Activities are a special case, so cache them and leave them till last
                if ($criteria->criteriatype == COMPLETION_CRITERIA_TYPE_ACTIVITY) {
                    $activities[$criteria->moduleinstance] = $complete;

                    if ($complete) {
                        $activities_complete++;
                    }

                    continue;
                }

                // Prerequisites are also a special case, so cache them and leave them till last
                if ($criteria->criteriatype == COMPLETION_CRITERIA_TYPE_COURSE) {
                    $prerequisites[$criteria->courseinstance] = $complete;

                    if ($complete) {
                        $prerequisites_complete++;
                    }

                    continue;
                }

                $row = array();
                $row['title'] = $criteria->get_title();
                $row['status'] = $completion->get_status();
                $rows[] = $row;
            }

            // Aggregate activities
            if (!empty($activities)) {

                $row = array();
                $row['title'] = get_string('activitiescomplete', 'report_completion');
                $row['status'] = $activities_complete.' of '.count($activities);
                $rows[] = $row;
            }

            // Aggregate prerequisites
            if (!empty($prerequisites)) {

                $row = array();
                $row['title'] = get_string('prerequisitescompleted', 'completion');
                $row['status'] = $prerequisites_complete.' of '.count($prerequisites);
                array_splice($rows, 0, 0, array($row));
            }

            $first_row = true;

            // Print table
            foreach ($rows as $row) {

                // Display course name on first row
                if ($first_row) {
                    echo '<tr><td class="c0"><a href="'.$CFG->wwwroot.'/course/view.php?id='.$c_course->id.'">'.$course_name.'</a></td>';
                } else {
                    echo '<tr><td class="c0"></td>';
                }

                echo '<td class="c1">';
                echo $row['title'];
                echo '</td><td class="c2">';

                switch ($row['status']) {
                    case 'Yes':
                        echo get_string('complete');
                        break;

                    case 'No':
                        echo get_string('incomplete', 'report_completion');
                        break;

                    default:
                        echo $row['status'];
                }

                // Display link on first row
                echo '</td><td class="c3">';
                if ($first_row) {
                    echo '<a href="'.$CFG->wwwroot.'/blocks/completionstatus/details.php?course='.$c_course->id.'&user='.$user->id.'">'.get_string('detailedview', 'report_completion').'</a>';
                }
                echo '</td>';

                // Display completion date for completed courses on first row
                if ($type === 'complete' && $first_row) {
                    $params = array(
                        'userid'    => $user->id,
                        'course'  => $c_course->id
                    );

                    $ccompletion = new completion_completion($params);
                    echo '<td class="c4">'.userdate($ccompletion->timecompleted, '%e %B %G').'</td>';
                }

                $first_row = false;
                echo '</tr>';
            }
        }

        echo '</table>';
    }
}


echo $OUTPUT->footer();
// Trigger a user report viewed event.
$event = \report_completion\event\user_report_viewed::create(array('context' => $coursecontext, 'relateduserid' => $userid));
$event->trigger();
