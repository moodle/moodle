<?php

    require_once('../../../config.php');
    require_once('../lib.php');

    require_variable($id);
    optional_variable($groupid, -1);

    if (!$chat = get_record("chat", "id", $id)) {
        error("Could not find that chat room!");
    }
    
    if (!$course = get_record("course", "id", $chat->course)) {
        error("Could not find the course this belongs to!");
    }

    if (!$cm = get_coursemodule_from_instance("chat", $chat->id, $course->id)) {
        error("Course Module ID was incorrect");
    }
    
    require_login($course->id);
    
    if (isguest()) {
        error("Guest does not have access to chat rooms");
    }

/// Check to see if groups are being used here
    if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
        if ($currentgroup = get_and_set_current_group($course, $groupmode, $groupid)) {
            if (!$group = get_record('groups', 'id', $currentgroup)) {
                error("That group (id $currentgroup) doesn't exist!");
            }
            $groupname = ': '.$group->name;
        } else {
            $groupname = ': '.get_string('allparticipants');
        }
    } else {
        $currentgroup = false;
        $groupname = '';
    }

    if (!$chat_sid = chat_login_user($chat->id, "header_js", $currentgroup)) {
        error("Could not log in to chat room!!");
    }

    if ($currentgroup !== false) {
        $params = "chat_enter=true&chat_sid=$chat_sid&groupid=$currentgroup";
    } else {
        $params = "chat_enter=true&chat_sid=$chat_sid";
    }

    $strchat = get_string("modulename", "chat");
    

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
 <head>
  <title>
   <?php echo "$strchat: $course->shortname: $chat->name$groupname" ?>
  </title>
 </head>
 <frameset cols="*,200" border="5" framespacing="no" frameborder="yes" marginwidth="2" marginheight="1">
  <frameset rows="0,0,*,40" border="0" framespacing="no" frameborder="no" marginwidth="2" marginheight="1">
   <frame src="../empty.php" NAME="empty" scrolling="no" marginwidth="0" marginheight="0">
   <frame src="jsupdate.php?<?php echo $params ?>" scrolling="no" marginwidth="0" marginheight="0">
   <frame src="chatmsg.php" NAME="msg" scrolling="auto" marginwidth="2" marginheight="1">
   <frame src="chatinput.php?<?php echo $params ?>" name="input" scrolling="no" marginwidth="2" marginheight="1">
  </frameset>
  <frame src="../users.php?<?php echo $params ?>" name="users" scrolling="auto" marginwidth="5" marginheight="5">
 </frameset>
 <noframes>
  Sorry, this version of Moodle Chat needs a browser that handles frames.
 </noframes>
</html>
