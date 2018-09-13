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

require_once("../../config.php");
require_once("lib.php");


$id = required_param('id', PARAM_INT);   // Course module.

if (! $cm = get_coursemodule_from_id('journal', $id)) {
    print_error("Course Module ID was incorrect");
}

if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
    print_error("Course module is misconfigured");
}

require_login($course, false, $cm);

$context = context_module::instance($cm->id);

require_capability('mod/journal:manageentries', $context);


if (! $journal = $DB->get_record("journal", array("id" => $cm->instance))) {
    print_error("Course module is incorrect");
}

// Header.
$PAGE->set_url('/mod/journal/report.php', array('id' => $id));

$PAGE->navbar->add(get_string("entries", "journal"));
$PAGE->set_title(get_string("modulenameplural", "journal"));
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string("entries", "journal"));


// Make some easy ways to access the entries.
if ( $eee = $DB->get_records("journal_entries", array("journal" => $journal->id))) {
    foreach ($eee as $ee) {
        $entrybyuser[$ee->userid] = $ee;
        $entrybyentry[$ee->id]  = $ee;
    }

} else {
    $entrybyuser  = array ();
    $entrybyentry = array ();
}

// Group mode.
$groupmode = groups_get_activity_groupmode($cm);
$currentgroup = groups_get_activity_group($cm, true);


// Process incoming data if there is any.
if ($data = data_submitted()) {

    confirm_sesskey();

    $feedback = array();
    $data = (array)$data;

    // Peel out all the data from variable names.
    foreach ($data as $key => $val) {
        if (strpos($key, 'r') === 0 || strpos($key, 'c') === 0) {
            $type = substr($key, 0, 1);
            $num  = substr($key, 1);
            $feedback[$num][$type] = $val;
        }
    }

    $timenow = time();
    $count = 0;
    foreach ($feedback as $num => $vals) {
        $entry = $entrybyentry[$num];
        // Only update entries where feedback has actually changed.
        $ratingchanged = false;

        $studentrating = clean_param($vals['r'], PARAM_INT);
        $studentcomment = clean_text($vals['c'], FORMAT_PLAIN);

        if ($studentrating != $entry->rating && !($studentrating == '' && $entry->rating == "0")) {
            $ratingchanged = true;
        }

        if ($ratingchanged || $studentcomment != $entry->entrycomment) {
            $newentry = new StdClass();
            $newentry->rating       = $studentrating;
            $newentry->entrycomment = $studentcomment;
            $newentry->teacher      = $USER->id;
            $newentry->timemarked   = $timenow;
            $newentry->mailed       = 0;           // Make sure mail goes out (again, even).
            $newentry->id           = $num;
            if (!$DB->update_record("journal_entries", $newentry)) {
                echo $OUTPUT->notification("Failed to update the journal feedback for user $entry->userid");
            } else {
                $count++;
            }
            $entrybyuser[$entry->userid]->rating     = $studentrating;
            $entrybyuser[$entry->userid]->entrycomment    = $studentcomment;
            $entrybyuser[$entry->userid]->teacher    = $USER->id;
            $entrybyuser[$entry->userid]->timemarked = $timenow;

            $journal = $DB->get_record("journal", array("id" => $entrybyuser[$entry->userid]->journal));
            $journal->cmidnumber = $cm->idnumber;

            journal_update_grades($journal, $entry->userid);
        }
    }

    // Trigger module feedback updated event.
    $event = \mod_journal\event\feedback_updated::create(array(
        'objectid' => $journal->id,
        'context' => $context
    ));
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('journal', $journal);
    $event->trigger();

    echo $OUTPUT->notification(get_string("feedbackupdated", "journal", "$count"), "notifysuccess");

} else {

    // Trigger module viewed event.
    $event = \mod_journal\event\entries_viewed::create(array(
        'objectid' => $journal->id,
        'context' => $context
    ));
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('journal', $journal);
    $event->trigger();
}

// Print out the journal entries.

if ($currentgroup) {
    $groups = $currentgroup;
} else {
    $groups = '';
}
$users = get_users_by_capability($context, 'mod/journal:addentries', '', '', '', '', $groups);

if (!$users) {
    echo $OUTPUT->heading(get_string("nousersyet"));

} else {

    groups_print_activity_menu($cm, $CFG->wwwroot . "/mod/journal/report.php?id=$cm->id");

    $grades = make_grades_menu($journal->grade);
    if (!$teachers = get_users_by_capability($context, 'mod/journal:manageentries')) {
        print_error('noentriesmanagers', 'journal');
    }

    echo '<form action="report.php" method="post">';

    if ($usersdone = journal_get_users_done($journal, $currentgroup)) {
        foreach ($usersdone as $user) {
            journal_print_user_entry($course, $user, $entrybyuser[$user->id], $teachers, $grades);
            unset($users[$user->id]);
        }
    }

    foreach ($users as $user) {       // Remaining users.
        journal_print_user_entry($course, $user, null, $teachers, $grades);
    }

    echo "<p class=\"feedbacksave\">";
    echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\" />";
    echo "<input type=\"hidden\" name=\"sesskey\" value=\"" . sesskey() . "\" />";
    echo "<input type=\"submit\" value=\"".get_string("saveallfeedback", "journal")."\" />";
    echo "</p>";
    echo "</form>";
}

echo $OUTPUT->footer();
