<?php  // $Id$

    require_once('../../../config.php');
    require_once('../lib.php');

    $id      = required_param('id', PARAM_INT);
    $groupid = optional_param('groupid', 0, PARAM_INT); //only for teachers

    if (!$chat = get_record('chat', 'id', $id)) {
        error('Could not find that chat room!');
    }

    if (!$course = get_record('course', 'id', $chat->course)) {
        error('Could not find the course this belongs to!');
    }

    if (!$cm = get_coursemodule_from_instance('chat', $chat->id, $course->id)) {
        error('Course Module ID was incorrect');
    }
    
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    
    require_login($course->id, false, $cm);

    require_capability('mod/chat:chat',$context);

    if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', get_context_instance(CONTEXT_MODULE, $cm->id))) {
        print_header();
        notice(get_string("activityiscurrentlyhidden"));
    }

/// Check to see if groups are being used here
     if ($groupmode = groups_get_activity_groupmode($cm)) {   // Groups are being used
        if ($groupid = groups_get_activity_group($cm)) {
            if (!$group = groups_get_group($groupid, false)) {
                error("That group (id $groupid) doesn't exist!");
            }
            $groupname = ': '.$group->name;
        } else {
            $groupname = ': '.get_string('allparticipants');
        }
    } else {
        $groupid = 0;
        $groupname = '';
    }

    $strchat = get_string('modulename', 'chat'); // must be before current_language() in chat_login_user() to force course language!!!

    if (!$chat_sid = chat_login_user($chat->id, 'header_js', $groupid, $course)) {
        error('Could not log in to chat room!!');
    }

    $params = "chat_id=$id&chat_sid={$chat_sid}";

    // fallback to the old jsupdate, but allow other update modes
    $updatemode = 'jsupdate';
    if (!empty($CFG->chat_normal_updatemode)) {
        $updatemode = $CFG->chat_normal_updatemode;
    }
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
 <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <title>
   <?php echo "$strchat: " . format_string($course->shortname) . ": ".format_string($chat->name,true)."$groupname" ?>
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
