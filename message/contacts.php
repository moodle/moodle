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
 * A page displaying the user's contacts. Similar to index.php but not a popup.
 *
 * @package   moodlecore
 * @copyright 2010 Andrew Davis
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');
require('lib.php');

require_login(0, false);

if (isguestuser()) {
    redirect($CFG->wwwroot);
}

if (empty($CFG->messaging)) {
    print_error('disabled', 'message');
}

/// Optional variables that may be passed in
$addcontact     = optional_param('addcontact',     0, PARAM_INT); // adding a contact
$removecontact  = optional_param('removecontact',  0, PARAM_INT); // removing a contact
$blockcontact   = optional_param('blockcontact',   0, PARAM_INT); // blocking a contact
$unblockcontact = optional_param('unblockcontact', 0, PARAM_INT); // unblocking a contact
$advancedsearch = optional_param('advanced', 0, PARAM_INT);
$usergroup = optional_param('usergroup', VIEW_UNREAD_MESSAGES, PARAM_ALPHANUMEXT);

$url = new moodle_url('/message/contacts.php');
/*if ($addcontact !== 0) {
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
}*/
if ($usergroup !== 0) {
    $url->param('usergroup', $usergroup);
}
if ($advancedsearch !== 0) {
    $url->param('advanced', $advancedsearch);
}
$PAGE->set_url($url);

/// Process any contact maintenance requests there may be
if ($addcontact and confirm_sesskey()) {
    add_to_log(SITEID, 'message', 'add contact', 'history.php?user1='.$addcontact.'&amp;user2='.$USER->id, $addcontact);
    message_add_contact($addcontact);
    redirect($CFG->wwwroot . '/message/index.php?usergroup=contacts&id='.$addcontact);
}
if ($removecontact and confirm_sesskey()) {
    add_to_log(SITEID, 'message', 'remove contact', 'history.php?user1='.$removecontact.'&amp;user2='.$USER->id, $removecontact);
    message_remove_contact($removecontact);
}
if ($blockcontact and confirm_sesskey()) {
    add_to_log(SITEID, 'message', 'block contact', 'history.php?user1='.$blockcontact.'&amp;user2='.$USER->id, $blockcontact);
    message_block_contact($blockcontact);
}
if ($unblockcontact and confirm_sesskey()) {
    add_to_log(SITEID, 'message', 'unblock contact', 'history.php?user1='.$unblockcontact.'&amp;user2='.$USER->id, $unblockcontact);
    message_unblock_contact($unblockcontact);
}

//$PAGE->blocks->add_region('content');
$PAGE->set_context(get_context_instance(CONTEXT_USER, $USER->id));
$PAGE->navigation->extend_for_user($USER);
$PAGE->set_pagelayout('course');

$context = get_context_instance(CONTEXT_SYSTEM);

$strmycontacts = get_string('mycontacts', 'message');
$strcontacts = get_string('contacts', 'message');

$PAGE->navbar->add(get_string('myprofile'));
$PAGE->navbar->add(get_string('messages','message'), 'index.php');
$PAGE->navbar->add($strcontacts);

$PAGE->set_title(fullname($USER).': '.$strcontacts);
$PAGE->set_heading("$SITE->shortname: $strcontacts");

//now the page contents
echo $OUTPUT->header();

echo $OUTPUT->box_start('message');

$user1 = $USER;//we'll need a way to specify this if we want to view this page as a different user
$user2 = null;

$countunreadtotal = message_count_unread_messages($user1);
$blockedusers = message_get_blocked_users($user1, $user2);
list($onlinecontacts, $offlinecontacts, $strangers) = message_get_contacts($user1, $user2);
$showcontactactionlinks = true;
message_print_contact_selector($countunreadtotal, $usergroup, $user1, $user2, $blockedusers, $onlinecontacts, $offlinecontacts, $strangers, $showcontactactionlinks);

echo html_writer::start_tag('div', array('class'=>'messagearea mdl-align'));
    message_print_search($advancedsearch, $user1);
echo html_writer::end_tag('div');

echo $OUTPUT->box_end();

echo $OUTPUT->footer();

