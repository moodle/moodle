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

// This page prints a particular instance of chat.

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot . '/mod/chat/lib.php');
require_once($CFG->libdir . '/completionlib.php');

$id   = optional_param('id', 0, PARAM_INT);
$c    = optional_param('c', 0, PARAM_INT);
$edit = optional_param('edit', -1, PARAM_BOOL);

if ($id) {
    if (! $cm = get_coursemodule_from_id('chat', $id)) {
        print_error('invalidcoursemodule');
    }

    if (! $course = $DB->get_record('course', array('id' => $cm->course))) {
        print_error('coursemisconf');
    }

    chat_update_chat_times($cm->instance);

    if (! $chat = $DB->get_record('chat', array('id' => $cm->instance))) {
        print_error('invalidid', 'chat');
    }

} else {
    chat_update_chat_times($c);

    if (! $chat = $DB->get_record('chat', array('id' => $c))) {
        print_error('coursemisconf');
    }
    if (! $course = $DB->get_record('course', array('id' => $chat->course))) {
        print_error('coursemisconf');
    }
    if (! $cm = get_coursemodule_from_instance('chat', $chat->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
}

require_course_login($course, true, $cm);

$context = context_module::instance($cm->id);
$PAGE->set_context($context);

// Show some info for guests.
if (isguestuser()) {
    $PAGE->set_title($chat->name);
    echo $OUTPUT->header();
    echo $OUTPUT->confirm('<p>'.get_string('noguests', 'chat').'</p>'.get_string('liketologin'),
            get_login_url(), $CFG->wwwroot.'/course/view.php?id='.$course->id);

    echo $OUTPUT->footer();
    exit;
}

// Completion and trigger events.
chat_view($chat, $course, $cm, $context);

$strenterchat    = get_string('enterchat', 'chat');
$stridle         = get_string('idle', 'chat');
$strcurrentusers = get_string('currentusers', 'chat');
$strnextsession  = get_string('nextsession', 'chat');

$courseshortname = format_string($course->shortname, true, array('context' => context_course::instance($course->id)));
$title = $courseshortname . ': ' . format_string($chat->name);

// Initialize $PAGE.
$PAGE->set_url('/mod/chat/view.php', array('id' => $cm->id));
$PAGE->set_title($title);
$PAGE->set_heading($course->fullname);

// Print the page header.
echo $OUTPUT->header();

// Check to see if groups are being used here.
$groupmode = groups_get_activity_groupmode($cm);
$currentgroup = groups_get_activity_group($cm, true);

// URL parameters.
$params = array();
if ($currentgroup) {
    $groupselect = " AND groupid = '$currentgroup'";
    $groupparam = "_group{$currentgroup}";
    $params['groupid'] = $currentgroup;
} else {
    $groupselect = "";
    $groupparam = "";
}

echo $OUTPUT->heading(format_string($chat->name), 2);

if ($chat->intro) {
    echo $OUTPUT->box(format_module_intro('chat', $chat, $cm->id), 'generalbox', 'intro');
}

groups_print_activity_menu($cm, $CFG->wwwroot . "/mod/chat/view.php?id=$cm->id");

if (has_capability('mod/chat:chat', $context)) {
    // Print the main part of the page.
    echo $OUTPUT->box_start('generalbox', 'enterlink');

    $now = time();
    $span = $chat->chattime - $now;
    if ($chat->chattime and $chat->schedule and ($span > 0)) {  // A chat is scheduled.
        echo '<p>';
        echo get_string('sessionstart', 'chat', format_time($span));
        echo '</p>';
    }

    $params['id'] = $chat->id;
    $chattarget = new moodle_url("/mod/chat/gui_$CFG->chat_method/index.php", $params);
    echo '<p>';
    echo $OUTPUT->action_link($chattarget,
                              $strenterchat,
                              new popup_action('click', $chattarget, "chat{$course->id}_{$chat->id}{$groupparam}",
                                               array('height' => 500, 'width' => 700)));
    echo '</p>';

    $params['id'] = $chat->id;
    $link = new moodle_url('/mod/chat/gui_basic/index.php', $params);
    $action = new popup_action('click', $link, "chat{$course->id}_{$chat->id}{$groupparam}",
                               array('height' => 500, 'width' => 700));
    echo '<p>';
    echo $OUTPUT->action_link($link, get_string('noframesjs', 'message'), $action,
                              array('title' => get_string('modulename', 'chat')));
    echo '</p>';

    if ($chat->studentlogs or has_capability('mod/chat:readlog', $context)) {
        if ($msg = $DB->get_records_select('chat_messages', "chatid = ? $groupselect", array($chat->id))) {
            echo '<p>';
            echo html_writer::link(new moodle_url('/mod/chat/report.php', array('id' => $cm->id)),
                                   get_string('viewreport', 'chat'));
            echo '</p>';
        }
    }

    echo $OUTPUT->box_end();

} else {
    echo $OUTPUT->box_start('generalbox', 'notallowenter');
    echo '<p>'.get_string('notallowenter', 'chat').'</p>';
    echo $OUTPUT->box_end();
}

chat_delete_old_users();

if ($chatusers = chat_get_users($chat->id, $currentgroup, $cm->groupingid)) {
    $timenow = time();
    echo $OUTPUT->box_start('generalbox', 'chatcurrentusers');
    echo $OUTPUT->heading($strcurrentusers, 3);
    echo '<table>';
    foreach ($chatusers as $chatuser) {
        $lastping = $timenow - $chatuser->lastmessageping;
        echo '<tr><td class="chatuserimage">';
        $url = new moodle_url('/user/view.php', array('id' => $chatuser->id, 'course' => $chat->course));
        echo html_writer::link($url, $OUTPUT->user_picture($chatuser));
        echo '</td><td class="chatuserdetails">';
        echo '<p>'.fullname($chatuser).'</p>';
        echo '<span class="idletime">'.$stridle.': '.format_time($lastping).'</span>';
        echo '</td></tr>';
    }
    echo '</table>';
    echo $OUTPUT->box_end();
}

echo $OUTPUT->footer();
