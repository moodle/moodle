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
 * This view provides a summary for the teacher
 *
 * @package mod_flashcard
 * @category mod
 * @author Valery Fremaux, Gustav Delius, Tomasz Muras
 * @contributors
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @version Moodle 2.0
 */
defined('MOODLE_INTERNAL') || die();

echo $out; // Deffered header.

// Small controller here...
if ($action == 'reset') {
    $userid = required_param('userid', PARAM_INT);
    $DB->delete_records('flashcard_card', array('flashcardid' => $flashcard->id, 'userid' => $userid));

    $completion = new completion_info($course);
    if (($flashcard->completionallgood || $flashcard->completionallviewed) && $completion->is_enabled($cm)) {
        // Unmark completion state.
        $completion->update_state($cm, COMPLETION_INCOMPLETE, $userid);
    }
}

require_once($CFG->dirroot.'/enrol/locallib.php');

$coursecontext = context_course::instance($COURSE->id);
$course = $DB->get_record('course', array('id' => $COURSE->id), '*', MUST_EXIST);

$groupmode = groups_get_activity_groupmode($cm, $COURSE);
if ($groupmode != NOGROUPS) {
    $groupid = groups_get_activity_group($cm, true);
    groups_print_activity_menu($cm, $url.'&view=summary&page=byusers');
} else {
    $groupid = 0;
}
$courseusers = get_enrolled_users($coursecontext, '', $groupid);

$struser = get_string('username');
$strdeckstates = get_string('deckstates', 'flashcard');
$strcounts = get_string('counters', 'flashcard');

$table = new html_table();
$table->head = array("<b>$struser</b>", "<b>$strdeckstates</b>", "<b>$strcounts</b>");
$table->size = array('30%', '50%', '20%');
$table->width = '100%';

if (!empty($courseusers)) {
    foreach ($courseusers as $auser) {
        $status = flashcard_get_deck_status($flashcard, $auser->id);
        $userbox = $OUTPUT->user_picture($auser);
        $userbox .= fullname($auser);
        if ($status) {
            $flashcard->cm = &$cm;
            $deckbox = $renderer->print_deck_status($flashcard, $auser->id, $status, true);
            $countbox = $renderer->print_deckcounts($flashcard, $auser->id);
        } else {
            $deckbox = get_string('notinitialized', 'flashcard');
            $countbox = '';
        }
        $table->data[] = array($userbox, $deckbox, $countbox);
    }
    echo html_writer::table($table);
} else {
    echo '<center>';
    echo $OUTPUT->box(get_string('nousers', 'flashcard'));
    echo '</center>';
}

