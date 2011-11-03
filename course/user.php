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
 * Display user activity reports for a course
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course
 */

require_once("../config.php");
require_once("lib.php");
require_once($CFG->libdir.'/completionlib.php');

$id      = required_param('id',PARAM_INT);       // course id
$user    = required_param('user',PARAM_INT);     // user id
$mode    = optional_param('mode', "todaylogs", PARAM_ALPHA);
$page    = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 100, PARAM_INT);

$url = new moodle_url('/course/user.php', array('id'=>$id,'user'=>$user, 'mode'=>$mode));
if ($page !== 0) {
    $url->param('page', $page);
}
if ($perpage !== 100) {
    $url->param('perpage', $perpage);
}
$PAGE->set_url($url);

if (!$course = $DB->get_record('course', array('id'=>$id))) {
    print_error('invalidcourseid', 'error');
}

if (! $user = $DB->get_record("user", array("id"=>$user))) {
    print_error('invaliduserid', 'error');
}

if ($mode === 'outline' or $mode === 'complete') {
    $url = new moodle_url('/report/outline/user.php', array('id'=>$user->id, 'course'=>$course->id, 'mode'=>$mode));
    redirect($url);

} else if ($mode === 'todaylogs' or $mode === 'alllogs') {
    $logmode = ($mode === 'todaylogs') ? 'today' : 'all';
    $url = new moodle_url('/report/log/user.php', array('id'=>$user->id, 'course'=>$course->id, 'mode'=>$logmode));
    redirect($url);
} else if ($mode === 'stats') {
    $url = new moodle_url('/report/stats/user.php', array('id'=>$user->id, 'course'=>$course->id));
    redirect($url);
}


require_login();
$coursecontext   = get_context_instance(CONTEXT_COURSE, $course->id);
$personalcontext = get_context_instance(CONTEXT_USER, $user->id);

require_login();
$PAGE->set_pagelayout('admin');
if (has_capability('moodle/user:viewuseractivitiesreport', $personalcontext) and !is_enrolled($coursecontext)) {
    // do not require parents to be enrolled in courses ;-)
    $PAGE->set_course($course);
} else {
    require_login($course);
}

if ($user->deleted) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('userdeleted'));
    echo $OUTPUT->footer();
    die;
}

//TODO: all this is a hack - we can not link to plugins like this - all this must be abstracted to plugin callbacks!

// prepare list of allowed modes
$myreports  = ($course->showreports and $USER->id == $user->id);
$anyreport  = has_capability('moodle/user:viewuseractivitiesreport', $personalcontext);

$modes = array();

if (has_capability('moodle/grade:viewall', $coursecontext)) {
    //ok - can view all course grades
    $modes[] = 'grade';

} else if ($course->showgrades and $user->id == $USER->id and has_capability('moodle/grade:view', $coursecontext)) {
    //ok - can view own grades
    $modes[] = 'grade';

} else if ($course->showgrades and has_capability('moodle/grade:viewall', $personalcontext)) {
    // ok - can view grades of this user - parent most probably
    $modes[] = 'grade';

} else if ($course->showgrades and $anyreport) {
    // ok - can view grades of this user - parent most probably
    $modes[] = 'grade';
}

// Course completion tab
if (!empty($CFG->enablecompletion) && ($course->id == SITEID || !empty($course->enablecompletion)) && // completion enabled
    ($myreports || $anyreport || ($course->id == SITEID || has_capability('report/completion:view', $coursecontext)))) { // permissions to view the report

    // Decide if singular or plural
    if ($course->id == SITEID) {
        $modes[] = 'coursecompletions';
    } else {
        $modes[] = 'coursecompletion';
    }
}


if (empty($modes)) {
    require_capability('moodle/user:viewuseractivitiesreport', $personalcontext);
}

if (!in_array($mode, $modes)) {
    // forbidden or non-existent mode
    $mode = reset($modes);
}

add_to_log($course->id, "course", "user report", "user.php?id=$course->id&amp;user=$user->id&amp;mode=$mode", "$user->id");

$stractivityreport = get_string("activityreport");
$strparticipants   = get_string("participants");
$strcomplete       = get_string("complete");
$strmode           = get_string($mode);
$fullname          = fullname($user, true);

$link = null;
if ($course->id != SITEID && has_capability('moodle/course:viewparticipants', $coursecontext)) {
    $link = new moodle_url('/user/index.php', array('id'=>$course->id));
}

$PAGE->navigation->extend_for_user($user);
$PAGE->navigation->set_userid_for_parent_checks($user->id); // see MDL-25805 for reasons and for full commit reference for reversal when fixed.
$PAGE->set_title("$course->shortname: $stractivityreport ($mode)");
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

switch ($mode) {
    case "grade":
        if (empty($CFG->grade_profilereport) or !file_exists($CFG->dirroot.'/grade/report/'.$CFG->grade_profilereport.'/lib.php')) {
            $CFG->grade_profilereport = 'user';
        }
        require_once $CFG->libdir.'/gradelib.php';
        require_once $CFG->dirroot.'/grade/lib.php';
        require_once $CFG->dirroot.'/grade/report/'.$CFG->grade_profilereport.'/lib.php';

        $functionname = 'grade_report_'.$CFG->grade_profilereport.'_profilereport';
        if (function_exists($functionname)) {
            $functionname($course, $user);
        }
        break;

    case "coursecompletion":
    case "coursecompletions":

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
                $error = get_string('nocompletions', 'report_completion');
            } else {
                $error = get_string('nocompletioncoursesenroled', 'report_completion');
            }

            echo $OUTPUT->notification($error);
            $rs->close(); // not going to loop (but break), close rs
            break;
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
                echo '<table class="generalbox boxaligncenter">';
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
                    $course_context = get_context_instance(CONTEXT_COURSE, $c_course->id, MUST_EXIST);
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

        break;
    default:
        // can not be reached ;-)
}


echo $OUTPUT->footer();
