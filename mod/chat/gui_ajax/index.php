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

require_once('../../../config.php');
require_once('../lib.php');

$id      = required_param('id', PARAM_INT);
$groupid = optional_param('groupid', 0, PARAM_INT); // Only for teachers.
$theme   = optional_param('chat_theme', 'course_theme', PARAM_SAFEDIR); // The value course_theme == the current theme.

$url = new moodle_url('/mod/chat/gui_ajax/index.php', array('id' => $id));
if ($groupid !== 0) {
    $url->param('groupid', $groupid);
}
$PAGE->set_url($url);
$PAGE->set_popup_notification_allowed(false); // No popup notifications in the chat window.
$PAGE->requires->strings_for_js(array('coursetheme', 'bubble', 'compact'), 'mod_chat');

$chat = $DB->get_record('chat', array('id' => $id), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id' => $chat->course), '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('chat', $chat->id, $course->id, false, MUST_EXIST);

$context = context_module::instance($cm->id);
require_login($course, false, $cm);
require_capability('mod/chat:chat', $context);

// Check to see if groups are being used here.
if ($groupmode = groups_get_activity_groupmode($cm)) {   // Groups are being used.
    if ($groupid = groups_get_activity_group($cm)) {
        if (!$group = groups_get_group($groupid)) {
            print_error('invalidgroupid');
        }
        $groupname = ': '.$group->name;
    } else {
        $groupname = ': '.get_string('allparticipants');
    }
} else {
    $groupid = 0;
    $groupname = '';
}
// If requested theme doesn't exist, use default 'bubble' theme.
if ($theme != 'course_theme' and !file_exists(__DIR__ . '/theme/'.$theme.'/chat.css')) {
    $theme = 'compact';
}

// Log into the chat room.
if (!$chatsid = chat_login_user($chat->id, 'ajax', $groupid, $course)) {
    print_error('cantlogin', 'chat');
}
$courseshortname = format_string($course->shortname, true, array('context' => context_course::instance($course->id)));
$module = array(
    'name'      => 'mod_chat_ajax', // Chat gui's are not real plugins, we have to break the naming standards for JS modules here.
    'fullpath'  => '/mod/chat/gui_ajax/module.js',
    'requires'  => array('base', 'dom', 'event', 'event-mouseenter', 'event-key', 'json-parse', 'io', 'overlay', 'yui2-resize',
                         'yui2-layout', 'yui2-menu'),
    'strings'   => array(array('send', 'chat'), array('sending', 'chat'), array('inputarea', 'chat'), array('userlist', 'chat'),
                         array('modulename', 'chat'), array('beep', 'chat'), array('talk', 'chat'))
);
$modulecfg = array(
    'home' => $CFG->wwwroot.'/mod/chat/view.php?id='.$cm->id,
    'chaturl' => $CFG->wwwroot.'/mod/chat/gui_ajax/index.php?id='.$id,
    'theme' => $theme,
    'userid' => $USER->id,
    'sid' => $chatsid,
    'timer' => 3000,
    'chat_lasttime' => 0,
    'chat_lastrow' => null,
    'chatroom_name' => $courseshortname . ": " . format_string($chat->name, true) . $groupname
);
$PAGE->requires->js_init_call('M.mod_chat_ajax.init', array($modulecfg), false, $module);

$PAGE->set_title(get_string('modulename', 'chat').": $courseshortname: ".format_string($chat->name, true)."$groupname");
$PAGE->add_body_class('yui-skin-sam');
$PAGE->set_pagelayout('embedded');
if ( $theme != 'course_theme') {
    $PAGE->requires->css('/mod/chat/gui_ajax/theme/'.$theme.'/chat.css');
}

echo $OUTPUT->header();
echo $OUTPUT->box(html_writer::tag('h2',  get_string('participants'), array('class' => 'accesshide')) .
        '<ul id="users-list" class="list-group"></ul>', '', 'chat-userlist');
echo $OUTPUT->box('', '', 'chat-options');
echo $OUTPUT->box(html_writer::tag('h2',  get_string('messages', 'chat'), array('class' => 'accesshide')) .
        '<ul id="messages-list"></ul>', '', 'chat-messages');
$table = new html_table();
$table->data = array(
    array('<div class="form-inline"><div class="d-flex"><label class="accesshide" for="input-message">'.
          get_string('entermessage', 'chat').' </label>'.
          '<span class="form-group"><input type="text" disabled="true" class="form-control" ' .
          'id="input-message" value="Loading..." size="48" /></span>'.
          '<span class="form-group"><input type="button" id="button-send" class="btn btn-secondary mx-1" ' .
          'value="'.get_string('send', 'chat').'" />' .$OUTPUT->help_icon('usingchat', 'chat'). '</span></div>' .
          ' <div class="form-group d-flex ml-auto"><a id="choosetheme" href="###">'.
          get_string('themes').
          ' &raquo; </a></div></div>'));
echo $OUTPUT->box(html_writer::tag('h2',  get_string('composemessage', 'chat'), array('class' => 'accesshide')) .
        html_writer::table($table), '', 'chat-input-area');
echo $OUTPUT->box('', '', 'chat-notify');
echo $OUTPUT->footer();
