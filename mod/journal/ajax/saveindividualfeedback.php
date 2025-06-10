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
 * AJAX save individual feedback functionality for mod_journal
 *
 * @package mod_journal
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

defined('MOODLE_INTERNAL') || die();

$sesskey = required_param('sesskey', PARAM_ALPHANUM);
$cmid = required_param('cmid', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$entryid = required_param('entryid', PARAM_INT);
$feedback = optional_param('feedback', null, PARAM_NOTAGS);
$grade = optional_param('grade', '', PARAM_RAW);

if ($grade === '') {
    $grade = -1;
} else {
    $grade = (int)$grade;
}

if (! $cm = get_coursemodule_from_id('journal', $cmid)) {
    throw new \moodle_exception(get_string('incorrectcmid', 'journal'));
}

if (! $course = $DB->get_record('course', array('id' => $cm->course))) {
    throw new \moodle_exception(get_string('incorrectcourseid', 'journal'));
}

if (! $user = $DB->get_record('user', array('id' => $userid))) {
    throw new \moodle_exception(get_string('incorrectuserid', 'journal'));
}

require_login($course, false, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/journal:manageentries', $context);

if (! $journal = $DB->get_record('journal', array('id' => $cm->instance))) {
    throw new \moodle_exception(get_string('incorrectjournalid', 'journal'));
}
$journal->cmidnumber = $cm->idnumber;

if (! $entry = $DB->get_record('journal_entries', array('journal' => $journal->id, 'id' => $entryid))) {
    throw new \moodle_exception(get_string('incorrectjournalentry', 'journal'));
}

confirm_sesskey($sesskey);

// Only update entries where feedback has actually changed.
$ratingchanged = false;
if ($grade !== null && $grade !== (int)$entry->rating) {
    $ratingchanged = true;
}

if ($ratingchanged || $feedback !== $entry->entrycomment) {
    try {
        $transaction = $DB->start_delegated_transaction();
        $newentry = new stdClass();
        $newentry->rating       = $grade;
        $newentry->entrycomment = $feedback;
        $newentry->teacher      = $USER->id;
        $newentry->timemarked   = time();
        $newentry->mailed       = 0;           // Make sure mail goes out (again, even).
        $newentry->id           = $entry->id;
        if (!$DB->update_record('journal_entries', $newentry)) {
            throw new Exception(get_string('failedupdate', 'journal', $entry->userid));
        }
        journal_update_grades($journal, $entry->userid);

        // Trigger module entry updated event.
        $event = \mod_journal\event\entry_updated::create(array(
            'objectid' => $journal->id,
            'context' => $context
        ));

        $event->add_record_snapshot('course_modules', $cm);
        $event->add_record_snapshot('course', $course);
        $event->add_record_snapshot('journal', $journal);
        $event->trigger();

        $result['status'] = 'ok';
        $result['content'] = get_string('feedbackupdatedforuser', 'journal', fullname($user));

        $transaction->allow_commit();
    } catch (Exception $e) {
        $transaction->rollback($e);
        throw $e;
    }
} else {
    $result['status'] = 'ok';
    $result['content'] = get_string('nodatachanged', 'journal');
}
