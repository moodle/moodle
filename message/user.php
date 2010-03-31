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

/// Script parameters
$userid = required_param('id', PARAM_INT);

$addcontact     = optional_param('addcontact',     0, PARAM_INT); // adding a contact
$removecontact  = optional_param('removecontact',  0, PARAM_INT); // removing a contact
$blockcontact   = optional_param('blockcontact',   0, PARAM_INT); // blocking a contact
$unblockcontact = optional_param('unblockcontact', 0, PARAM_INT); // unblocking a contact

$url = new moodle_url('/message/user.php', array('id'=>$userid));
if ($addcontact !== 0) {
    $url->param('addcontact', $addcontact);
}
if ($removecontact !== 0) {
    $url->param('removecontact', $removecontact);
}
if ($blockcontact !== 0) {
    $url->param('blockcontact', $blockcontact);
}
if ($unblockcontact !== 0) {
    $url->param('unblockcontact', $unblockcontact);
}
$PAGE->set_url($url);

/// Check the user we are talking to is valid
if (! $user = $DB->get_record('user', array('id'=>$userid))) {
    print_error('invaliduserid');
}

/// Possibly change some contacts if requested

if ($addcontact and confirm_sesskey()) {
    add_to_log(SITEID, 'message', 'add contact',
               'history.php?user1='.$addcontact.'&amp;user2='.$USER->id, $addcontact);
    message_add_contact($addcontact);
}
if ($removecontact and confirm_sesskey()) {
    add_to_log(SITEID, 'message', 'remove contact',
               'history.php?user1='.$removecontact.'&amp;user2='.$USER->id, $removecontact);
    message_remove_contact($removecontact);
}
if ($blockcontact and confirm_sesskey()) {
    add_to_log(SITEID, 'message', 'block contact',
               'history.php?user1='.$blockcontact.'&amp;user2='.$USER->id, $blockcontact);
    message_block_contact($blockcontact);
}
if ($unblockcontact and confirm_sesskey()) {
    add_to_log(SITEID, 'message', 'unblock contact',
               'history.php?user1='.$unblockcontact.'&amp;user2='.$USER->id, $unblockcontact);
    message_unblock_contact($unblockcontact);
}

//$PAGE->set_title('Message History');
$PAGE->set_pagelayout('popup');
echo $OUTPUT->header();
echo '<table width="100%" cellpadding="0" cellspacing="0"><tr>';
echo '<td width="100">';
echo $OUTPUT->user_picture($user, array('size'=>48, 'courseid'=>SITEID)) .'</td>';
echo '<td valign="middle" align="center">';

echo '<div class="name">'.fullname($user).'</div>';

echo '<div class="commands">';
if ($contact = $DB->get_record('message_contacts', array('userid'=>$USER->id, 'contactid'=>$user->id))) {
     if ($contact->blocked) {
         message_contact_link($user->id, 'add', false, 'user.php?id='.$user->id, true);
         message_contact_link($user->id, 'unblock', false, 'user.php?id='.$user->id, true);
     } else {
         message_contact_link($user->id, 'remove', false, 'user.php?id='.$user->id, true);
         message_contact_link($user->id, 'block', false, 'user.php?id='.$user->id, true);
     }
} else {
     message_contact_link($user->id, 'add', false, 'user.php?id='.$user->id, true);
     message_contact_link($user->id, 'block', false, 'user.php?id='.$user->id, true);
}
message_history_link($user->id, 0, false, '', '', 'both');
echo '</div>';

echo '</td></tr></table>';

echo $OUTPUT->footer();

