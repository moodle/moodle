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

$url = new moodle_url('/mod/chat/gui_sockets/index.php', array('id' => $id));
if ($groupid !== 0) {
    $url->param('groupid', $groupid);
}
$PAGE->set_url($url);

if (!$chat = $DB->get_record('chat', array('id' => $id))) {
    throw new \moodle_exception('invalidid', 'chat');
}

if (!$course = $DB->get_record('course', array('id' => $chat->course))) {
    throw new \moodle_exception('invalidcourseid');
}

if (!$cm = get_coursemodule_from_instance('chat', $chat->id, $course->id)) {
    throw new \moodle_exception('invalidcoursemodule');
}

require_login($course, false, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/chat:chat', $context);

// Check to see if groups are being used here
if ($groupmode = groups_get_activity_groupmode($cm)) {   // Groups are being used.
    if ($groupid = groups_get_activity_group($cm)) {
        if (!$group = groups_get_group($groupid)) {
            throw new \moodle_exception('invalidgroupid');
        }
        $groupname = ': '.$group->name;
    } else {
        $groupname = ': '.get_string('allparticipants');
    }
} else {
    $groupid = 0;
    $groupname = '';
}

$strchat = get_string('modulename', 'chat'); // Must be before current_language() in chat_login_user() to force course language!

if (!$chatsid = chat_login_user($chat->id, 'sockets', $groupid, $course)) {
    throw new \moodle_exception('cantlogin');
}

$params = "chat_sid=$chatsid";
$courseshortname = format_string($course->shortname, true, array('context' => context_course::instance($course->id)));

$chatname = format_string($chat->name, true, array('context' => $context));
$winchaturl = "http://$CFG->chat_serverhost:$CFG->chat_serverport?win=chat&amp;$params";
$winusersurl = "http://$CFG->chat_serverhost:$CFG->chat_serverport?win=users&amp;$params"

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
 <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <title>
   <?php echo "$strchat: " . $courseshortname . ": " . $chatname . "$groupname" ?>
  </title>
 </head>
 <frameset cols="*,200" border="5" framespacing="no" frameborder="yes" marginwidth="2" marginheight="1">
  <frameset rows="0,*,70" border="0" framespacing="no" frameborder="no" marginwidth="2" marginheight="1">
   <frame src="../empty.php" name="empty" scrolling="auto" noresize marginwidth="2" marginheight="0">
   <frame src="<?php echo $winchaturl; ?>" scrolling="auto" name="msg" noresize marginwidth="2" marginheight="0">
   <frame src="chatinput.php?<?php echo $params ?>" name="input" scrolling="no" marginwidth="2" marginheight="1">
  </frameset>
  <frame src="<?php echo $winusersurl; ?>" name="users" scrolling="auto" marginwidth="5" marginheight="5">
 </frameset>
 <noframes>
  Sorry, this version of Moodle Chat needs a browser that handles frames.
 </noframes>
</html>
