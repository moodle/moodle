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
 * Main interface window for messaging
 *
 * @author Luis Rodrigues and Martin Dougiamas
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package message
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
$tab            = optional_param('tab', 'contacts', PARAM_ALPHA); // current tab - default to contacts
$addcontact     = optional_param('addcontact',     0, PARAM_INT); // adding a contact
$removecontact  = optional_param('removecontact',  0, PARAM_INT); // removing a contact
$blockcontact   = optional_param('blockcontact',   0, PARAM_INT); // blocking a contact
$unblockcontact = optional_param('unblockcontact', 0, PARAM_INT); // unblocking a contact
$popup          = optional_param('popup', false, PARAM_ALPHANUM);    // If set then starts a new popup window

$url = new moodle_url('/message/index.php');
if ($tab !== 'contacts') {
    $url->param('tab', $tab);
}
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
if ($popup !== false) {
    $url->param('popup', $popup);
}
$PAGE->set_url($url);


/// Popup a window if required and quit (usually from external links).
if ($popup) {
    $PAGE->set_pagelayout('popup');
    echo $OUTPUT->header();
    echo html_writer::script(js_writer::function_call('openpopup', Array('/message/index.php', 'message', 'menubar=0,location=0,scrollbars,status,resizable,width=400,height=500', 0)));
    redirect("$CFG->wwwroot/", '', 0);
    exit;
}

/// Process any contact maintenance requests there may be
if ($addcontact and confirm_sesskey()) {
    add_to_log(SITEID, 'message', 'add contact', 'history.php?user1='.$addcontact.'&amp;user2='.$USER->id, $addcontact);
    message_add_contact($addcontact);
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


/// Header on this page
if ($tab == 'contacts') {
    $PAGE->set_periodic_refresh_delay($CFG->message_contacts_refresh);
}

$PAGE->set_pagelayout('popup');
$PAGE->set_title(get_string('messages', 'message').' - '.format_string($SITE->fullname));
echo $OUTPUT->header();
echo '<table cellspacing="2" cellpadding="2" border="0" width="95%" class="boxaligncenter">';
echo '<tr>';

/// Print out the tabs
echo '<td>';
$tabrow = array();
$tabrow[] = new tabobject('contacts', $CFG->wwwroot.'/message/index.php?tab=contacts',
                           get_string('contacts', 'message'));
$tabrow[] = new tabobject('search', $CFG->wwwroot.'/message/index.php?tab=search',
                           get_string('search', 'message'));
$tabrow[] = new tabobject('settings', $CFG->wwwroot.'/message/index.php?tab=settings',
                           get_string('settings', 'message'));
$tabrows = array($tabrow);

print_tabs($tabrows, $tab);

echo '</td>';


echo '</tr><tr>';

/// Print out contents of the tab
echo '<td>';

/// a print function is associated with each tab
$tabprintfunction = 'message_print_'.$tab;
if (function_exists($tabprintfunction)) {
    $tabprintfunction();
}

echo '</td> </tr> </table>';
echo $OUTPUT->footer();

