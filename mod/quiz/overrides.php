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
 * This page handles listing of quiz overrides
 *
 * @package mod_quiz
 * @copyright 2010 Matt Petro
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/mod/quiz/lib.php');
require_once($CFG->dirroot.'/mod/quiz/locallib.php');
require_once($CFG->dirroot.'/mod/quiz/override_form.php');


$cmid = required_param('cmid', PARAM_INT);  // course module ID, or
$mode = optional_param('mode', 'group', PARAM_ALPHA); // one of 'user' or 'group'

$groupmode = ($mode == "group");

if (! $cm = get_coursemodule_from_id('quiz', $cmid)) {
    print_error('invalidcoursemodule');
}
if (! $quiz = $DB->get_record('quiz', array('id' => $cm->instance))) {
    print_error('invalidcoursemodule');
}

$url = new moodle_url('/mod/quiz/overrides.php', array('cmid'=>$cm->id, 'mode'=>$mode));

$PAGE->set_url($url);

require_login($cm->course, false, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);

// Check the user has the required capabilities to list overrides
require_capability('mod/quiz:manageoverrides', $context);

// Display a list of overrides

$PAGE->set_title(get_string('overrides', 'quiz'));
echo $OUTPUT->header();

// Print heading and tabs (if there is more than one).
$currenttab = 'overrides';
include('tabs.php');

// Fetch all overrides
$conds = array('quiz' => $quiz->id);
if ($groupmode) {
    $colname = get_string('group');
    $sql = 'SELECT o.*, g.name
                FROM {quiz_overrides} o LEFT JOIN {groups} g
                ON o.groupid = g.id
                WHERE o.groupid IS NOT NULL
                  AND o.quiz = ?
                ORDER BY g.name';
}
else {
    $colname = get_string('user');
    $sql = 'SELECT o.*, u.firstname, u.lastname, u.id as uid
                FROM {quiz_overrides} o LEFT JOIN {user} u
                ON o.userid = u.id
                WHERE o.userid IS NOT NULL
                  AND o.quiz = ?
                ORDER BY u.lastname, u.firstname';
}

$params = array($quiz->id);
$overrides = $DB->get_records_sql($sql, $params);

// Initialise table
$table = new html_table();
$table->headspan = array(1,2,1);
$table->colclasses = array('colname','colsetting','colvalue','colaction');
$table->head = array(
        $colname,
        get_string('overrides', 'quiz'),
        get_string('action'),
);

$userurl = new moodle_url('/user/view.php', array());
$groupurl = new moodle_url('/group/overview.php', array('id' => $cm->course));

$overridedeleteurl = new moodle_url('/mod/quiz/overridedelete.php');
$overrideediturl = new moodle_url('/mod/quiz/overrideedit.php');

foreach ($overrides as $override) {

    $fields = array();
    $values = array();

    // check for orphaned overrides
    if (!isset($override->name) && !isset($override->uid)) {
        // no corresponding user/group record, so remove the override
        quiz_delete_override($quiz, $override->id);
        continue;
    }

    // Format timeopen
    if (isset($override->timeopen)) {
        $fields[] = get_string('quizopens', 'quiz');
        $values[] = ($override->timeopen > 0)? userdate($override->timeopen) : get_string('noopen', 'quiz');
    }

    // Format timeclose
    if (isset($override->timeclose)) {
        $fields[] = get_string('quizcloses', 'quiz');
        $values[] = ($override->timeclose > 0)? userdate($override->timeclose) : get_string('noclose', 'quiz');
    }

    // Format timelimit
    if (isset($override->timelimit)) {
        $fields[] = get_string('timelimit', 'quiz');
        $values[] = ($override->timelimit > 0)? format_time($override->timelimit) : get_string('none', 'quiz');
    }

    // Format number of attempts
    if (isset($override->attempts)) {
        $fields[] = get_string('attempts', 'quiz');
        $values[] = ($override->attempts > 0)? $override->attempts : get_string('unlimited');
    }

    // Format password
    if (isset($override->password)) {
        $fields[] = get_string('requirepassword', 'quiz');
        $values[] = ($override->password !== '')? get_string('enabled', 'quiz') : get_string('none', 'quiz');
    }

    // Icons:

    // edit
    $editurlstr = $overrideediturl->out(true, array('id' => $override->id));
    $iconstr = '<a title="' . get_string('edit') . '" href="'. $editurlstr . '">' .
            '<img src="' . $OUTPUT->pix_url('t/edit') . '" class="iconsmall" alt="' . get_string('edit') . '" /></a> ';
    // duplicate
    $copyurlstr = $overrideediturl->out(true, array('id' => $override->id, 'action' => 'duplicate'));
    $iconstr .= '<a title="' . get_string('copy') . '" href="' . $copyurlstr . '">' .
            '<img src="' . $OUTPUT->pix_url('t/copy') . '" class="iconsmall" alt="' . get_string('copy') . '" /></a> ';
    // delete
    $deleteurlstr = $overridedeleteurl->out(true, array('id' => $override->id, 'sesskey' => sesskey()));
    $iconstr .= '<a title="' . get_string('delete') . '" href="' . $deleteurlstr . '">' .
            '<img src="' . $OUTPUT->pix_url('t/delete') . '" class="iconsmall" alt="' . get_string('delete') . '" /></a> ';

    if ($groupmode) {
        $usergroupstr = '<a href="' . $groupurl->out(true, array('group' => $override->groupid)) . '" >' . $override->name . '</a>';
    }
    else {
        $usergroupstr = '<a href="' . $userurl->out(true, array('id' => $override->userid)) . '" >' . fullname($override) . '</a>';
    }

    if (!empty($table->data)) {
        $table->data[] = 'hr';
    }

    $usergroupcell = new html_table_cell();
    $usergroupcell->rowspan = count($fields);
    $usergroupcell->text = $usergroupstr;
    $actioncell = new html_table_cell();
    $actioncell->rowspan = count($fields);
    $actioncell->text = $iconstr;

    for ($i = 0; $i < count($fields); ++$i) {
        $row = new html_table_row();
        if ($i == 0) {
            $row->cells[] = $usergroupcell;
        }
        $cell1 = new html_table_cell();
        $cell1->text = $fields[$i];
        $row->cells[] = $cell1;
        $cell2 = new html_table_cell();
        $cell2->text = $values[$i];
        $row->cells[] = $cell2;
        if ($i == 0) {
            $row->cells[] = $actioncell;
        }
        $table->data[] = $row;
    }
}

// Output the table and button

echo html_writer::start_tag('div', array('id' => 'quizoverrides'));
if (count($table->data)) {
    echo html_writer::table($table);
}

echo html_writer::start_tag('div', array('class' => 'buttons'));
if ($groupmode) {
    echo $OUTPUT->single_button($overrideediturl->out(true, array('action' => 'addgroup', 'cmid' => $cm->id)),
                                get_string('addnewgroupoverride', 'quiz'));
} else {
    echo $OUTPUT->single_button($overrideediturl->out(true, array('action' => 'adduser', 'cmid' => $cm->id)),
                                get_string('addnewuseroverride', 'quiz'));
}
echo html_writer::end_tag('div');
echo html_writer::end_tag('div');

// Finish the page
echo $OUTPUT->footer();
