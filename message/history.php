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
 * For listing message histories between any two users
 *
 * @author Luis Rodrigues and Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package message
 */

require('../config.php');
require('lib.php');

require_login();

if (isguestuser()) {
    redirect($CFG->wwwroot);
}

if (empty($CFG->messaging)) {
    print_error('disabled', 'message');
}

$PAGE->set_title(get_string('messagehistory', 'message'));

/// Script parameters
$userid1 = required_param('user1', PARAM_INT);
$PAGE->set_url('/message/history.php', array('user1'=>$userid1));
if (! $user1 = $DB->get_record("user", array("id"=>$userid1))) {  // Check it's correct
    print_error('invaliduserid');
}

if ($user1->deleted) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('userdeleted').': '.$userid1, 1);
    echo $OUTPUT->footer();
    die;
}

if (has_capability('moodle/site:readallmessages', get_context_instance(CONTEXT_SYSTEM))) {             // Able to see any discussion
    $userid2 = optional_param('user2', $USER->id, PARAM_INT);
    $PAGE->url->param('user2', $userid2);
    if (! $user2 = $DB->get_record("user", array("id"=>$userid2))) {  // Check
        print_error('invaliduserid');
    }
    if ($user2->deleted) {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('userdeleted').': '.$userid2, 1);
        echo $OUTPUT->footer();
        die;
    }
} else {
    $userid2 = $USER->id;    // Can only see messages involving yourself
    $user2 = $USER;
}
$search = optional_param('search', '', PARAM_CLEAN);

add_to_log(SITEID, 'message', 'history', 'history.php?user1='.$userid1.'&amp;user2='.$userid2, $userid1);

/// Our two users are defined - let's set up the page

echo $OUTPUT->header();

/// Print out a heading including the users we are looking at

echo $OUTPUT->box_start('center');
echo '<table align="center" cellpadding="10"><tr>';
echo '<td align="center">';
echo $OUTPUT->user_picture($user1, array('size'=>100, 'courseid'=>SITEID)).'<br />';
echo fullname($user1);
echo '</td>';
echo '<td align="center">';
echo '<img src="'.$CFG->wwwroot.'/pix/t/left.gif" alt="'.get_string('from').'" />';
echo '<img src="'.$CFG->wwwroot.'/pix/t/right.gif" alt="'.get_string('to').'" />';
echo '</td>';
echo '<td align="center">';
echo $OUTPUT->user_picture($user2, array('size'=>100, 'courseid'=>SITEID)).'<br />';
echo fullname($user2);
echo '</td>';
echo '</tr></table>';
echo $OUTPUT->box_end();


/// Get all the messages and print them

if ($messages = message_get_history($user1, $user2)) {
    $current->mday = '';
    $current->month = '';
    $current->year = '';
    $messagedate = get_string('strftimetime');
    $blockdate   = get_string('strftimedaydate');
    foreach ($messages as $message) {
        $date = usergetdate($message->timecreated);
        if ($current->mday != $date['mday'] | $current->month != $date['month'] | $current->year != $date['year']) {
            $current->mday = $date['mday'];
            $current->month = $date['month'];
            $current->year = $date['year'];
            echo '<a name="'.$date['year'].$date['mon'].$date['mday'].'"></a>';
            echo $OUTPUT->heading(userdate($message->timecreated, $blockdate), 4, 'center');
        }
        if ($message->useridfrom == $user1->id) {
            echo message_format_message($message, $user1, $messagedate, $search, 'other');
        } else {
            echo message_format_message($message, $user2, $messagedate, $search, 'me');
        }
    }
} else {
    echo $OUTPUT->heading(get_string('nomessagesfound', 'message'), 1);
}

echo $OUTPUT->footer();

