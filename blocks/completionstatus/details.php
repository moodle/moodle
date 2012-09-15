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
 * Block for displayed logged in user's course completion status
 *
 * @package    block
 * @subpackage completion
 * @copyright  2009-2012 Catalyst IT Ltd
 * @author     Aaron Barnes <aaronb@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../config.php');
require_once("{$CFG->libdir}/completionlib.php");


///
/// Load data
///
$id = required_param('course', PARAM_INT);
$userid = optional_param('user', 0, PARAM_INT);

// Load course
$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

// Load user
if ($userid) {
    $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
} else {
    $user = $USER;
}


// Check permissions
require_login($course);

$coursecontext   = context_course::instance($course->id);
$personalcontext = context_user::instance($user->id);

$can_view = false;

// Can view own report
if ($USER->id == $user->id) {
    $can_view = true;
} else if (has_capability('moodle/user:viewuseractivitiesreport', $personalcontext)) {
    $can_view = true;
} else if (has_capability('report/completion:view', $coursecontext)) {
    $can_view = true;
} else if (has_capability('report/completion:view', $personalcontext)) {
    $can_view = true;
}

if (!$can_view) {
    print_error('cannotviewreport');
}


// Load completion data
$info = new completion_info($course);

$returnurl = new moodle_url('/course/view.php', array('id' => $id));

// Don't display if completion isn't enabled!
if (!$info->is_enabled()) {
    print_error('completionnotenabled', 'completion', $returnurl);
}

// Check this user is enroled
if (!$info->is_tracked_user($user->id)) {
    if ($USER->id == $user->id) {
        print_error('notenroled', 'completion', $returnurl);
    } else {
        print_error('usernotenroled', 'completion', $returnurl);
    }
}


///
/// Display page
///
$PAGE->set_context(context_course::instance($course->id));

// Print header
$page = get_string('completionprogressdetails', 'block_completionstatus');
$title = format_string($course->fullname) . ': ' . $page;

$PAGE->navbar->add($page);
$PAGE->set_pagelayout('standard');
$PAGE->set_url('/blocks/completionstatus/details.php', array('course' => $course->id, 'user' => $user->id));
$PAGE->set_title(get_string('course') . ': ' . $course->fullname);
$PAGE->set_heading($title);
echo $OUTPUT->header();


// Display completion status
echo '<table class="generalbox boxaligncenter"><tbody>';

// If not display logged in user, show user name
if ($USER->id != $user->id) {
    echo '<tr><td colspan="2"><b>'.get_string('showinguser', 'completion').'</b>: ';
    echo '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&course='.$course->id.'">'.fullname($user).'</a>';
    echo '</td></tr>';
}

echo '<tr><td colspan="2"><b>'.get_string('status').':</b> ';

// Is course complete?
$coursecomplete = $info->is_course_complete($user->id);

// Has this user completed any criteria?
$criteriacomplete = $info->count_course_user_data($user->id);

// Load course completion
$params = array(
    'userid' => $user->id,
    'course' => $course->id,
);
$ccompletion = new completion_completion($params);

if ($coursecomplete) {
    echo get_string('complete');
} else if (!$criteriacomplete && !$ccompletion->timestarted) {
    echo '<i>'.get_string('notyetstarted', 'completion').'</i>';
} else {
    echo '<i>'.get_string('inprogress','completion').'</i>';
}

echo '</td></tr>';

// Load criteria to display
$completions = $info->get_completions($user->id);

// Check if this course has any criteria
if (empty($completions)) {
    echo '<tr><td colspan="2"><br />';
    echo $OUTPUT->box(get_string('err_nocriteria', 'completion'), 'noticebox');
    echo '</td></tr></tbody></table>';
} else {
    echo '<tr><td colspan="2"><b>'.get_string('required').':</b> ';

    // Get overall aggregation method
    $overall = $info->get_aggregation_method();

    if ($overall == COMPLETION_AGGREGATION_ALL) {
        echo get_string('criteriarequiredall', 'completion');
    } else {
        echo get_string('criteriarequiredany', 'completion');
    }

    echo '</td></tr></tbody></table>';

    // Generate markup for criteria statuses
    echo '<table class="generalbox logtable boxaligncenter" id="criteriastatus" width="100%"><tbody>';
    echo '<tr class="ccheader">';
    echo '<th class="c0 header" scope="col">'.get_string('criteriagroup', 'block_completionstatus').'</th>';
    echo '<th class="c1 header" scope="col">'.get_string('criteria', 'completion').'</th>';
    echo '<th class="c2 header" scope="col">'.get_string('requirement', 'block_completionstatus').'</th>';
    echo '<th class="c3 header" scope="col">'.get_string('status').'</th>';
    echo '<th class="c4 header" scope="col">'.get_string('complete').'</th>';
    echo '<th class="c5 header" scope="col">'.get_string('completiondate', 'report_completion').'</th>';
    echo '</tr>';

    // Save row data
    $rows = array();

    // Loop through course criteria
    foreach ($completions as $completion) {
        $criteria = $completion->get_criteria();

        $row = array();
        $row['type'] = $criteria->criteriatype;
        $row['title'] = $criteria->get_title();
        $row['status'] = $completion->get_status();
        $row['complete'] = $completion->is_complete();
        $row['timecompleted'] = $completion->timecompleted;
        $row['details'] = $criteria->get_details($completion);
        $rows[] = $row;
    }

    // Print table
    $last_type = '';
    $agg_type = false;
    $oddeven = 0;

    foreach ($rows as $row) {

        echo '<tr class="r' . $oddeven . '">';

        // Criteria group
        echo '<td class="cell c0">';
        if ($last_type !== $row['details']['type']) {
            $last_type = $row['details']['type'];
            echo $last_type;

            // Reset agg type
            $agg_type = true;
        } else {
            // Display aggregation type
            if ($agg_type) {
                $agg = $info->get_aggregation_method($row['type']);

                echo '(<i>';

                if ($agg == COMPLETION_AGGREGATION_ALL) {
                    echo strtolower(get_string('aggregateall', 'completion'));
                } else {
                    echo strtolower(get_string('aggregateany', 'completion'));
                }

                echo '</i> '.strtolower(get_string('required')).')';
                $agg_type = false;
            }
        }
        echo '</td>';

        // Criteria title
        echo '<td class="cell c1">';
        echo $row['details']['criteria'];
        echo '</td>';

        // Requirement
        echo '<td class="cell c2">';
        echo $row['details']['requirement'];
        echo '</td>';

        // Status
        echo '<td class="cell c3">';
        echo $row['details']['status'];
        echo '</td>';

        // Is complete
        echo '<td class="cell c4">';
        echo $row['complete'] ? get_string('yes') : get_string('no');
        echo '</td>';

        // Completion data
        echo '<td class="cell c5">';
        if ($row['timecompleted']) {
            echo userdate($row['timecompleted'], get_string('strftimedate', 'langconfig'));
        } else {
            echo '-';
        }
        echo '</td>';
        echo '</tr>';
        // for row striping
        $oddeven = $oddeven ? 0 : 1;
    }

    echo '</tbody></table>';
}

echo '<div class="buttons">';
$courseurl = new moodle_url("/course/view.php", array('id' => $course->id));
echo $OUTPUT->single_button($courseurl, get_string('returntocourse', 'block_completionstatus'), 'get');
echo '</div>';

echo $OUTPUT->footer();
