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

$url = new moodle_url('/mod/chat/gui_header_js/index.php', array('id' => $id));
if ($groupid !== 0) {
    $url->param('groupid', $groupid);
}
$PAGE->set_url($url);

if (!$chat = $DB->get_record('chat', array('id' => $id))) {
    print_error('invalidid', 'chat');
}

if (!$course = $DB->get_record('course', array('id' => $chat->course))) {
    print_error('invalidcourseid');
}

if (!$cm = get_coursemodule_from_instance('chat', $chat->id, $course->id)) {
    print_error('invalidcoursemodule');
}

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

$strchat = get_string('modulename', 'chat'); // Must be before current_language() in chat_login_user() to force course language!

if (!$chatsid = chat_login_user($chat->id, 'header_js', $groupid, $course)) {
    print_error('cantlogin', 'chat');
}

$params = "chat_id=$id&chat_sid={$chatsid}";

// Fallback to the old jsupdate, but allow other update modes.
$updatemode = 'jsupdate';
if (!empty($CFG->chat_normal_updatemode)) {
    $updatemode = $CFG->chat_normal_updatemode;
}

$courseshortname = format_string($course->shortname, true, array('context' => context_course::instance($course->id)));
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
 <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <title>
   <?php echo "$strchat: " . $courseshortname . ": ".
              format_string($chat->name, true, array('context' => $context)) . "$groupname" ?>
  </title>
 </head>
 <frameset cols="*,200" border="5" framespacing="no" frameborder="yes" marginwidth="2" marginheight="1">
  <frameset rows="0,0,*,50" border="0" framespacing="no" frameborder="no" marginwidth="2" marginheight="1">
   <frame src="../empty.php" name="empty" scrolling="no" marginwidth="0" marginheight="0">
   <frame src="<?php echo $updatemode ?>.php?<?php echo $params ?>" name="jsupdate" scrolling="no" marginwidth="0" marginheight="0">
   <frame src="chatmsg.php?<?php echo $params ?>" name="msg" scrolling="auto" marginwidth="2" marginheight="1">
   <frame src="chatinput.php?<?php echo $params ?>" name="input" scrolling="no" marginwidth="2" marginheight="1">
  </frameset>
  <frame src="users.php?<?php echo $params ?>" name="users" scrolling="auto" marginwidth="5" marginheight="5">
 </frameset>
 <noframes>
  Sorry, this version of Moodle Chat needs a browser that handles frames.
 </noframes>
</html>
