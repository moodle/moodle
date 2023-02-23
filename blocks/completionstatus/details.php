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
 * Block for displaying logged in user's course completion status
 *
 * @package    block_completionstatus
 * @copyright  2009-2012 Catalyst IT Ltd
 * @author     Aaron Barnes <aaronb@catalyst.net.nz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../config.php');
require_once("{$CFG->libdir}/completionlib.php");

// Load data.
$id = required_param('course', PARAM_INT);
$userid = optional_param('user', 0, PARAM_INT);

// Load course.
$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

// Load user.
if ($userid) {
    $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
} else {
    $user = $USER;
}

// Check permissions.
require_login($course);

if (!completion_can_view_data($user->id, $course)) {
    throw new \moodle_exception('cannotviewreport');
}

// Load completion data.
$info = new completion_info($course);

$returnurl = new moodle_url('/course/view.php', array('id' => $id));

// Don't display if completion isn't enabled.
if (!$info->is_enabled()) {
    throw new \moodle_exception('completionnotenabled', 'completion', $returnurl);
}

// Check this user is enroled.
if (!$info->is_tracked_user($user->id)) {
    if ($USER->id == $user->id) {
        throw new \moodle_exception('notenroled', 'completion', $returnurl);
    } else {
        throw new \moodle_exception('usernotenroled', 'completion', $returnurl);
    }
}

// Print header.
$page = get_string('completionprogressdetails', 'block_completionstatus');
$title = format_string($course->fullname) . ': ' . $page;

$PAGE->navbar->add($page);
$PAGE->set_pagelayout('report');
$PAGE->set_url('/blocks/completionstatus/details.php', array('course' => $course->id, 'user' => $user->id));
$PAGE->set_title(get_string('course') . ': ' . $course->fullname);
$PAGE->set_heading($title);
echo $OUTPUT->header();


// Display completion status.
echo html_writer::start_tag('table', array('class' => 'generalbox boxaligncenter'));
echo html_writer::start_tag('tbody');

// If not display logged in user, show user name.
if ($USER->id != $user->id) {
    echo html_writer::start_tag('tr');
    echo html_writer::start_tag('td', array('colspan' => '2'));
    echo html_writer::tag('b', get_string('showinguser', 'completion') . ' ');
    $url = new moodle_url('/user/view.php', array('id' => $user->id, 'course' => $course->id));
    echo html_writer::link($url, fullname($user));
    echo html_writer::end_tag('td');
    echo html_writer::end_tag('tr');
}

echo html_writer::start_tag('tr');
echo html_writer::start_tag('td', array('colspan' => '2'));
echo html_writer::tag('b', get_string('status') . ' ');

// Is course complete?
$coursecomplete = $info->is_course_complete($user->id);

// Has this user completed any criteria?
$criteriacomplete = $info->count_course_user_data($user->id);

// Load course completion.
$params = array(
    'userid' => $user->id,
    'course' => $course->id,
);
$ccompletion = new completion_completion($params);

// Save row data.
$rows = array();

// Flag to set if current completion data is inconsistent with what is stored in the database.
$pendingupdate = false;

// Load criteria to display.
$completions = $info->get_completions($user->id);

// Loop through course criteria.
foreach ($completions as $completion) {
    $criteria = $completion->get_criteria();

    if (!$pendingupdate && $criteria->is_pending($completion)) {
        $pendingupdate = true;
    }

    $row = array();
    $row['type'] = $criteria->criteriatype;
    $row['title'] = $criteria->get_title();
    $row['status'] = $completion->get_status();
    $row['complete'] = $completion->is_complete();
    $row['timecompleted'] = $completion->timecompleted;
    $row['details'] = $criteria->get_details($completion);
    $rows[] = $row;
}

if ($pendingupdate) {
    echo html_writer::tag('i', get_string('pending', 'completion'));
} else if ($coursecomplete) {
    echo get_string('complete');
} else if (!$criteriacomplete && !$ccompletion->timestarted) {
    echo html_writer::tag('i', get_string('notyetstarted', 'completion'));
} else {
    echo html_writer::tag('i', get_string('inprogress', 'completion'));
}

echo html_writer::end_tag('td');
echo html_writer::end_tag('tr');

// Check if this course has any criteria.
if (empty($completions)) {
    echo html_writer::start_tag('tr');
    echo html_writer::start_tag('td', array('colspan' => '2'));
    echo html_writer::start_tag('br');
    echo $OUTPUT->box(get_string('nocriteriaset', 'completion'), 'noticebox');
    echo html_writer::end_tag('td');
    echo html_writer::end_tag('tr');
    echo html_writer::end_tag('tbody');
    echo html_writer::end_tag('table');
} else {
    echo html_writer::start_tag('tr');
    echo html_writer::start_tag('td', array('colspan' => '2'));
    echo html_writer::tag('b', get_string('required') . ' ');

    // Get overall aggregation method.
    $overall = $info->get_aggregation_method();

    if ($overall == COMPLETION_AGGREGATION_ALL) {
        echo get_string('criteriarequiredall', 'completion');
    } else {
        echo get_string('criteriarequiredany', 'completion');
    }

    echo html_writer::end_tag('td');
    echo html_writer::end_tag('tr');
    echo html_writer::end_tag('tbody');
    echo html_writer::end_tag('table');

    // Generate markup for criteria statuses.
    echo html_writer::start_tag('table',
            array('class' => 'generalbox logtable boxaligncenter', 'id' => 'criteriastatus', 'width' => '100%'));
    echo html_writer::start_tag('tbody');
    echo html_writer::start_tag('tr', array('class' => 'ccheader'));
    echo html_writer::tag('th', get_string('criteriagroup', 'block_completionstatus'), array('class' => 'c0 header', 'scope' => 'col'));
    echo html_writer::tag('th', get_string('criteria', 'completion'), array('class' => 'c1 header', 'scope' => 'col'));
    echo html_writer::tag('th', get_string('requirement', 'block_completionstatus'), array('class' => 'c2 header', 'scope' => 'col'));
    echo html_writer::tag('th', get_string('status'), array('class' => 'c3 header', 'scope' => 'col'));
    echo html_writer::tag('th', get_string('complete'), array('class' => 'c4 header', 'scope' => 'col'));
    echo html_writer::tag('th', get_string('completiondate', 'report_completion'), array('class' => 'c5 header', 'scope' => 'col'));
    echo html_writer::end_tag('tr');

    // Print table.
    $last_type = '';
    $agg_type = false;
    $oddeven = 0;

    foreach ($rows as $row) {

        echo html_writer::start_tag('tr', array('class' => 'r' . $oddeven));
        // Criteria group.
        echo html_writer::start_tag('td', array('class' => 'cell c0'));
        if ($last_type !== $row['details']['type']) {
            $last_type = $row['details']['type'];
            echo $last_type;

            // Reset agg type.
            $agg_type = true;
        } else {
            // Display aggregation type.
            if ($agg_type) {
                $agg = $info->get_aggregation_method($row['type']);
                echo '('. html_writer::start_tag('i');
                if ($agg == COMPLETION_AGGREGATION_ALL) {
                    echo core_text::strtolower(get_string('all', 'completion'));
                } else {
                    echo core_text::strtolower(get_string('any', 'completion'));
                }

                echo ' ' . html_writer::end_tag('i') .core_text::strtolower(get_string('required')).')';
                $agg_type = false;
            }
        }
        echo html_writer::end_tag('td');

        // Criteria title.
        echo html_writer::start_tag('td', array('class' => 'cell c1'));
        echo $row['details']['criteria'];
        echo html_writer::end_tag('td');

        // Requirement.
        echo html_writer::start_tag('td', array('class' => 'cell c2'));
        echo $row['details']['requirement'];
        echo html_writer::end_tag('td');

        // Status.
        echo html_writer::start_tag('td', array('class' => 'cell c3'));
        echo $row['details']['status'];
        echo html_writer::end_tag('td');

        // Is complete.
        echo html_writer::start_tag('td', array('class' => 'cell c4'));
        echo $row['complete'] ? get_string('yes') : get_string('no');
        echo html_writer::end_tag('td');

        // Completion data.
        echo html_writer::start_tag('td', array('class' => 'cell c5'));
        if ($row['timecompleted']) {
            echo userdate($row['timecompleted'], get_string('strftimedatemonthtimeshort', 'langconfig'));
        } else {
            echo '-';
        }
        echo html_writer::end_tag('td');
        echo html_writer::end_tag('tr');
        // For row striping.
        $oddeven = $oddeven ? 0 : 1;
    }

    echo html_writer::end_tag('tbody');
    echo html_writer::end_tag('table');
}

echo $OUTPUT->footer();
