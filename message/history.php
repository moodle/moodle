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

/// Script parameters
$userid1 = optional_param('user1', $USER->id, PARAM_INT);
$userid2 = required_param('user2', PARAM_INT);
$popup   = optional_param('popup', 0, PARAM_INT);

$url = new moodle_url('/message/history.php');
$url->param('user1', $userid1);
if (!empty($userid2)) {
    $url->param('user2', $userid2);
}
$PAGE->set_url($url);

$PAGE->set_context(get_context_instance(CONTEXT_USER, $USER->id));

$iscurrentuser = $USER->id == $userid1;

if ($iscurrentuser) {
    $PAGE->navigation->extend_for_user($USER);
} else {
    $PAGE->navigation->extend_for_user($DB->get_record('user',array('id'=>$userid1)));
}
$PAGE->navigation->extend_for_user($DB->get_record('user',array('id'=>$userid2)));


$strmessagehistory = get_string('messagehistory', 'message');
if (!$popup) {
    if ($iscurrentuser) {
        $PAGE->navigation->get('myprofile')->get('messages')->make_active();
    } else {
        $PAGE->navigation->find($userid1,navigation_node::TYPE_USER)->make_active();
    }

    $PAGE->navbar->add($strmessagehistory);

    $PAGE->set_pagelayout('course');
    $PAGE->set_heading($strmessagehistory);
}
$PAGE->set_title($strmessagehistory);


// Are we able to see other user's discussions?
if (has_capability('moodle/site:readallmessages', get_context_instance(CONTEXT_SYSTEM))) {
    if (! $user1 = $DB->get_record("user", array("id"=>$userid1))) {
        print_error('invaliduserid');
    }
    if ($user1->deleted) {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('userdeleted').': '.$userid1, 1);
        echo $OUTPUT->footer();
        die;
    }
} else {
    //User can only see their own discussions
    $userid1 = $USER->id;
    $user1 = $USER;
}

if (! $user2 = $DB->get_record("user", array("id"=>$userid2))) {  // Check
    print_error('invaliduserid');
}
if ($user2->deleted) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('userdeleted').': '.$userid2, 1);
    echo $OUTPUT->footer();
    die;
}

$search = optional_param('search', '', PARAM_CLEAN);

add_to_log(SITEID, 'message', 'history', 'history.php?user1='.$userid1.'&amp;user2='.$userid2, $userid1);

/// Our two users are defined - let's set up the page

echo $OUTPUT->header();

/// Print out a heading including the users we are looking at
message_print_message_history($user1, $user2, $search);

echo $OUTPUT->footer();

