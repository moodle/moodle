<?PHP  // $Id$

/// Library of functions and constants for module chat

if (!isset($CFG->chat_refresh_room)) {
    set_config("chat_refresh_room", 5);
} 
if (!isset($CFG->chat_refresh_userlist)) {
    set_config("chat_refresh_userlist", 10);
} 
if (!isset($CFG->chat_old_ping)) {
    set_config("chat_old_ping", 30);
} 


define("CHAT_DRAWBOARD", false);  // Look into this later


// The HTML head for the message window to start with (<!-- nix --> is used to get some browsers starting with output
$CHAT_HTMLHEAD = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\"><html><head></head>\n<body bgcolor=\"#FFFFFF\">\n\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n";

// The HTML head for the message window to start with (with js scrolling)
$CHAT_HTMLHEAD_JS = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\"><html><head><script language=\"JavaScript\">\n<!--\nfunction move()\n{\nif (scroll_active) window.scroll(1,400000);\nwindow.setTimeout(\"move()\",100);\n}\nscroll_active = true;\nmove();\n//-->\n</script>\n</head>\n<body bgcolor=\"#FFFFFF\" onBlur=\"scroll_active = true\" onFocus=\"scroll_active = false\">\n\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n";

// The HTML code for standard empty pages (e.g. if a user was kicked out)
$CHAT_HTMLHEAD_OUT = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\"><html><head><title>You are out!</title></head><body bgcolor=\"$THEME->body\"></body></html>";

// The HTML head for the message input page
$CHAT_HTMLHEAD_MSGINPUT = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\"><html><head><title>Message Input</title></head><body bgcolor=\"$THEME->body\">";

// The HTML code for the message input page, with JavaScript
$CHAT_HTMLHEAD_MSGINPUT_JS = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\"><html><head><title>Message Input</title>\n<script language=\"Javascript\">\n<!--\nscroll_active = true;\nfunction empty_field_and_submit()\n{\ndocument.fdummy.arsc_message.value=document.f.arsc_message.value;\ndocument.fdummy.submit();\ndocument.f.arsc_message.focus();\ndocument.f.arsc_message.select();\nreturn false;\n}\n// -->\n</script>\n</head><body bgcolor=\"$THEME->body\" OnLoad=\"document.f.arsc_message.focus();document.f.arsc_message.select();\">";


function chat_add_instance($chat) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod.html) this function 
/// will create a new instance and return the id number 
/// of the new instance.

    $chat->timemodified = time();

    $chat->chattime = make_timestamp($chat->chatyear, $chat->chatmonth, $chat->chatday, 
                                     $chat->chathour, $chat->chatminute);
    
    if ($returnid = insert_record("chat", $chat)) {

        $event = NULL;
        $event->name        = $chat->name;
        $event->description = $chat->intro;
        $event->courseid    = $chat->course;
        $event->groupid     = 0;
        $event->userid      = 0;
        $event->modulename  = 'chat';
        $event->instance    = $returnid;
        $event->eventtype   = $chat->schedule;
        $event->timestart   = $chat->chattime;
        $event->timeduration = 0;

        add_event($event);
    }

    return $returnid;
}


function chat_update_instance($chat) {
/// Given an object containing all the necessary data, 
/// (defined by the form in mod.html) this function 
/// will update an existing instance with new data.

    $chat->timemodified = time();
    $chat->id = $chat->instance;

    $chat->chattime = make_timestamp($chat->chatyear, $chat->chatmonth, $chat->chatday, 
                                     $chat->chathour, $chat->chatminute);

    if ($returnid = update_record("chat", $chat)) {

        $event = NULL;

        if ($event->id = get_field('event', 'id', 'modulename', 'chat', 'instance', $chat->id)) {

            $event->name        = $chat->name;
            $event->description = $chat->intro;
            $event->timestart   = $chat->chattime;

            update_event($event);
        }
    }

    return $returnid;
}


function chat_delete_instance($id) {
/// Given an ID of an instance of this module, 
/// this function will permanently delete the instance 
/// and any data that depends on it.  

    if (! $chat = get_record("chat", "id", "$id")) {
        return false;
    }

    $result = true;

    # Delete any dependent records here #

    if (! delete_records("chat", "id", "$chat->id")) {
        $result = false;
    }

    if (! delete_records('event', 'modulename', 'chat', 'instance', $chat->id)) {
        $result = false;
    }

    return $result;
}

function chat_user_outline($course, $user, $mod, $chat) {
/// Return a small object with summary information about what a 
/// user has done with a given particular instance of this module
/// Used for user activity reports.
/// $return->time = the time they did it
/// $return->info = a short text description

    return $return;
}

function chat_user_complete($course, $user, $mod, $chat) {
/// Print a detailed representation of what a  user has done with 
/// a given particular instance of this module, for user activity reports.

    return true;
}

function chat_print_recent_activity($course, $isteacher, $timestart) {
/// Given a course and a date, prints a summary of all chat rooms
/// that currently have people in them.
/// This function is called from course/lib.php: print_recent_activity()

    global $CFG;

    $timeold = time() - $CFG->chat_old_ping;

    if (!$chatusers = get_records_sql("SELECT u.id, cu.chatid, u.firstname, u.lastname
                                        FROM {$CFG->prefix}chat_users as cu,
                                             {$CFG->prefix}chat as ch,
                                             {$CFG->prefix}user as u
                                       WHERE cu.userid = u.id 
                                         AND cu.chatid = ch.id
                                         AND cu.lastping > '$timeold'
                                         AND ch.course = '$course->id'
                                       ORDER BY cu.chatid ASC") ) {
        return false;
    }

    $isteacher = isteacher($course->id);

    $outputstarted = false;
    $current = 0;
    foreach ($chatusers as $chatuser) {
        if ($current != $chatuser->chatid) {
            if ($current) {
                echo "</p>";
            }
            if ($chat = get_record("chat", "id", $chatuser->chatid)) {
                if (!($isteacher or instance_is_visible('chat', $chat))) {  // Chat hidden to students
                    continue;
                }
                if (!$outputstarted) {
                    print_headline(get_string("currentchats", "chat").":");
                    $outputstarted = true;
                }
                echo "<p><font size=1><a href=\"$CFG->wwwroot/mod/chat/view.php?c=$chat->id\">$chat->name</a></font><br />";
            }
            $current = $chatuser->chatid;
        }
        $fullname = fullname($chatuser);
        echo "&nbsp;&nbsp;&nbsp;<font size=1>- $fullname</font><br />";
    }
    echo "<br />";

    return true;
}

function chat_cron () {
/// Function to be run periodically according to the moodle cron
/// This function searches for things that need to be done, such 
/// as sending out mail, toggling flags etc ... 

    global $CFG;

    chat_update_chat_times();

    chat_delete_old_users();

    /// Delete old messages
    if ($chats = get_records("chat")) {
        foreach ($chats as $chat) {
            if ($chat->keepdays) {
                $timeold = time() - ($chat->keepdays * 24 * 3600);
                delete_records_select("chat_messages", "chatid = '$chat->id' AND timestamp < '$timeold'");
            }
        }
    }

    return true;
}

function chat_get_participants($chatid, $groupid=0) {
//Returns the users with data in one chat
//(users with records in chat_messages, students)

    global $CFG;

    if ($groupid) {
        $groupselect = " AND (c.groupid='$groupid' OR c.groupid='0')";
    } else {
        $groupselect = "";
    }

    //Get students
    $students = get_records_sql("SELECT DISTINCT u.*
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}chat_messages c
                                 WHERE c.chatid = '$chatid' $groupselect
                                   AND u.id = c.userid");

    //Return students array (it contains an array of unique users)
    return ($students);
}

function chat_refresh_events($courseid = 0) {
// This standard function will check all instances of this module
// and make sure there are up-to-date events created for each of them.
// If courseid = 0, then every chat event in the site is checked, else
// only chat events belonging to the course specified are checked.
// This function is used, in its new format, by restore_refresh_events()

    if ($courseid) {
        if (! $chats = get_records("chat", "course", $courseid)) {
            return true;
        }
    } else {
        if (! $chats = get_records("chat")) {
            return true;
        }
    }
    $moduleid = get_field('modules', 'id', 'name', 'chat');

    foreach ($chats as $chat) {
        $event = NULL;
        $event->name        = addslashes($chat->name);
        $event->description = addslashes($chat->intro);
        $event->timestart   = $chat->chattime;

        if ($event->id = get_field('event', 'id', 'modulename', 'chat', 'instance', $chat->id)) {
            update_event($event);

        } else {
            $event->courseid    = $chat->course;
            $event->groupid     = 0;
            $event->userid      = 0;
            $event->modulename  = 'chat';
            $event->instance    = $chat->id;
            $event->eventtype   = $chat->schedule;
            $event->timeduration = 0;
            $event->visible     = get_field('course_modules', 'visible', 'module', $moduleid, 'instance', $chat->id);
            
            add_event($event);
        }
    }
    return true;
}

//////////////////////////////////////////////////////////////////////
/// Functions that require some SQL

function chat_get_users($chatid, $groupid=0) {

    global $CFG;

    if ($groupid) {
        $groupselect = " AND (c.groupid='$groupid' OR c.groupid='0')";
    } else {
        $groupselect = "";
    }
   
    return get_records_sql("SELECT DISTINCT u.id, u.firstname, u.lastname, u.picture, c.lastmessageping, c.firstping
                              FROM {$CFG->prefix}chat_users c,
                                   {$CFG->prefix}user u
                             WHERE c.chatid = '$chatid'
                               AND u.id = c.userid $groupselect
                             ORDER BY c.firstping ASC");
}

function chat_get_latest_message($chatid, $groupid=0) {
/// Efficient way to extract just the latest message
/// Uses ADOdb directly instead of get_record_sql()
/// because the LIMIT command causes problems with 
/// the developer debugging in there.

    global $db, $CFG;

    if ($groupid) {
        $groupselect = " AND (groupid='$groupid' OR groupid='0')";
    } else {
        $groupselect = "";
    }

    if (!$rs = $db->Execute("SELECT *
                               FROM {$CFG->prefix}chat_messages 
                              WHERE chatid = '$chatid' $groupselect
                           ORDER BY timestamp DESC LIMIT 1")) {
        return false;
    }
    if ($rs->RecordCount() == 1) {
        return (object)$rs->fields;
    } else {
        return false;                 // Found no records
    }
}


//////////////////////////////////////////////////////////////////////

function chat_login_user($chatid, $version="header_js", $groupid=0) {
    global $USER;

    $chatuser->chatid   = $chatid;
    $chatuser->userid   = $USER->id;
    $chatuser->groupid  = $groupid;
    $chatuser->version  = $version;
    $chatuser->ip       = $USER->lastIP;
    $chatuser->lastping = $chatuser->firstping = $chatuser->lastmessageping = time();
    $chatuser->sid      = random_string(32);

    if (!insert_record("chat_users", $chatuser)) {
        return false;
    }

    return $chatuser->sid;
}

function chat_delete_old_users() {
// Delete the old and in the way

    global $CFG;

    $timeold = time() - $CFG->chat_old_ping;

    if ($oldusers = get_records_select("chat_users", "lastping < '$timeold'") ) {
        delete_records_select("chat_users", "lastping < '$timeold'");
        foreach ($oldusers as $olduser) {
            $message->chatid = $olduser->chatid;
            $message->userid = $olduser->userid;
            $message->groupid = $olduser->groupid;
            $message->message = "exit";
            $message->system = 1;
            $message->timestamp = time();
     
            if (!insert_record("chat_messages", $message)) {
                error("Could not insert a chat message!");
            }
        }
    }
}


function chat_update_chat_times($chatid=0) {
/// Updates chat records so that the next chat time is correct

    $timenow = time();
    if ($chatid) {
        if (!$chats[] = get_record_select("chat", "id = '$chatid' AND chattime <= '$timenow' AND schedule > '0'")) {
            return;
        }
    } else {
        if (!$chats = get_records_select("chat", "chattime <= '$timenow' AND schedule > '0'")) {
            return;
        }
    }

    foreach ($chats as $chat) {
        unset($chat->name);
        unset($chat->intro);
        switch ($chat->schedule) {
            case 1: // Single event - turn off schedule and disable
                    $chat->chattime = 0;
                    $chat->schedule = 0;
                    break;
            case 2: // Repeat daily
                    $chat->chattime += 24 * 3600;
                    break;
            case 3: // Repeat weekly
                    $chat->chattime += 7 * 24 * 3600;
                    break;
        }
        update_record("chat", $chat);

        $event = NULL;           // Update calendar too
        if ($event->id = get_field('event', 'id', 'modulename', 'chat', 'instance', $chat->id)) {
            $event->timestart   = $chat->chattime;
            update_event($event);
        }
    }
}


function chat_browser_detect($HTTP_USER_AGENT) {

 if(eregi("(opera) ([0-9]{1,2}.[0-9]{1,3}){0,1}", $HTTP_USER_AGENT, $match)
 || eregi("(opera/)([0-9]{1,2}.[0-9]{1,3}){0,1}", $HTTP_USER_AGENT, $match))
 {
  $BName = "Opera"; $BVersion=$match[2];
 }
 elseif( eregi("(konqueror)/([0-9]{1,2}.[0-9]{1,3})", $HTTP_USER_AGENT, $match) )
 {
  $BName = "Konqueror"; $BVersion=$match[2];
 }
 elseif( eregi("(lynx)/([0-9]{1,2}.[0-9]{1,2}.[0-9]{1,2})", $HTTP_USER_AGENT, $match) )
 {
  $BName = "Lynx"; $BVersion=$match[2];
 }
 elseif( eregi("(links) \(([0-9]{1,2}.[0-9]{1,3})", $HTTP_USER_AGENT, $match) )
 {
  $BName = "Links"; $BVersion=$match[2];
 }
 elseif( eregi("(msie) ([0-9]{1,2}.[0-9]{1,3})", $HTTP_USER_AGENT, $match) )
 {
  $BName = "MSIE"; $BVersion=$match[2];
 }
 elseif( eregi("(netscape6)/(6.[0-9]{1,3})", $HTTP_USER_AGENT, $match) )
 {
  $BName = "Netscape"; $BVersion=$match[2];
 }
 elseif( eregi("mozilla/5", $HTTP_USER_AGENT) )
 {
  $BName = "Netscape"; $BVersion="Unknown";
 }
 elseif( eregi("(mozilla)/([0-9]{1,2}.[0-9]{1,3})", $HTTP_USER_AGENT, $match) )
 {
  $BName = "Netscape"; $BVersion=$match[2];
 }
 elseif( eregi("w3m", $HTTP_USER_AGENT) )
 {
  $BName = "w3m"; $BVersion="Unknown";
 }
 else
 {
  $BName = "Unknown"; $BVersion="Unknown";
 }

 if(eregi("linux", $HTTP_USER_AGENT))
 {
  $BPlatform = "Linux";
 }
 elseif( eregi("win32", $HTTP_USER_AGENT) )
 {
  $BPlatform = "Windows";
 }
 elseif( (eregi("(win)([0-9]{2})", $HTTP_USER_AGENT, $match) )
 ||      (eregi("(windows) ([0-9]{2})", $HTTP_USER_AGENT, $match) ))
 {
  $BPlatform = "Windows $match[2]";
 }
 elseif( eregi("(winnt)([0-9]{1,2}.[0-9]{1,2}){0,1}", $HTTP_USER_AGENT, $match) )
 {
  $BPlatform = "Windows NT $match[2]";
 }
 elseif( eregi("(windows nt)( ){0,1}([0-9]{1,2}.[0-9]{1,2}){0,1}", $HTTP_USER_AGENT, $match) )
 {
  $BPlatform = "Windows NT $match[3]";
 }
 elseif( eregi("mac", $HTTP_USER_AGENT) )
 {
  $BPlatform = "Macintosh";
 }
 elseif( eregi("(sunos) ([0-9]{1,2}.[0-9]{1,2}){0,1}", $HTTP_USER_AGENT, $match) )
 {
  $BPlatform = "SunOS $match[2]";
 }
 elseif( eregi("(beos) r([0-9]{1,2}.[0-9]{1,2}){0,1}", $HTTP_USER_AGENT, $match) )
 {
  $BPlatform = "BeOS $match[2]";
 }
 elseif( eregi("freebsd", $HTTP_USER_AGENT) )
 {
  $BPlatform = "FreeBSD";
 }
 elseif( eregi("openbsd", $HTTP_USER_AGENT) )
 {
  $BPlatform = "OpenBSD";
 }
 elseif( eregi("irix", $HTTP_USER_AGENT) )
 {
  $BPlatform = "IRIX";
 }
 elseif( eregi("os/2", $HTTP_USER_AGENT) )
 {
  $BPlatform = "OS/2";
 }
 elseif( eregi("plan9", $HTTP_USER_AGENT) )
 {
  $BPlatform = "Plan9";
 }
 elseif( eregi("unix", $HTTP_USER_AGENT)
 ||      eregi("hp-ux", $HTTP_USER_AGENT) )
 {
  $BPlatform = "Unix";
 }
 elseif( eregi("osf", $HTTP_USER_AGENT) )
 {
  $BPlatform = "OSF";
 }
 else
 {
  $BPlatform = "Unknown";
 }
 
 $return["name"] = $BName;
 $return["version"] = $BVersion;
 $return["platform"] = $BPlatform;
 return $return;
}

function chat_display_version($version, $browser)
{
 GLOBAL $CFG;

 $checked = "";
 if (($version == "sockets") OR ($version == "push_js"))
 {
  $checked = "checked";
 }
 if (($version == "sockets" OR $version == "push_js")
     AND
     ($browser["name"] == "Lynx"
      OR
      $browser["name"] == "Links"
      OR
      $browser["name"] == "w3m"
      OR
      $browser["name"] == "Konqueror"
      OR
      ($browser["name"] == "Netscape" AND substr($browser["version"], 0, 1) == "2")))
 {
  $checked = "";
 }
 if (($version == "text")
     AND
     ($browser["name"] == "Lynx"
      OR
      $browser["name"] == "Links"
      OR
      $browser["name"] == "w3m"))
 {
  $checked = "checked";
 }
 if (($version == "header")
     AND
     ($browser["name"] == "Konqueror"))
 {
  $checked = "checked";
 }
 if (($version == "header_js")
     AND
     ($browser["name"] == "Netscape" AND substr($browser["version"], 0, 1) == "2"))
 {
  $checked = "checked";
 }
  ?>
  <tr>
   <td valign="top">
    <input type="radio" name="chat_chatversion" value="<?php echo $version; ?>"<?php echo $checked; ?>>
   </td>
   <td valign="top" align="left">
    <font face="Arial" size="2">
     <?php echo $chat_lang["gui_".$version]; ?>
    </font>
   </td>
  </tr>
  <?php

}


function chat_format_message($message, $courseid=0) {
/// Given a message object full of information, this function 
/// formats it appropriately into text and html, then 
/// returns the formatted data.

    global $CFG, $USER;

    $output = new object;

    if (!$user = get_record("user", "id", $message->userid)) {
        return "Error finding user id = $message->userid";
    }

    $picture = print_user_picture($user->id, 0, $user->picture, false, true, false);
    if ($courseid) {
        $picture = "<a target=\"_new\" href=\"$CFG->wwwroot/user/view.php?id=$user->id&course=$courseid\">$picture</a>";
    }

    $strtime = userdate($message->timestamp, get_string("strftimemessage", "chat"));

    $output->beep = false;   // by default

    $text = $message->message;

    if (!empty($message->system)) {             /// It's a system message
        $output->text = get_string("message$text", "chat", fullname($user));
        $output->text = "$strtime: $output->text";
        $output->html  = "<table><tr><td valign=top>$picture</td><td>";
        $output->html .= "<font size=2 color=\"#CCAAAA\">$output->text</font>";
        $output->html .= "</td></tr></table>";
        return $output;
    }

    convert_urls_into_links($text);
    replace_smilies($text);
    $text = filter_text($text, $courseid);

    if (substr($text, 0, 5) == "beep ") {          /// It's a beep!
        $beepwho = trim(substr($text, 5));

        if ($beepwho == "all") {   // everyone
            $outinfo = "$strtime: ". get_string("messagebeepseveryone", "chat", fullname($user));
            $outmain = "";
            $output->beep = true;  // (eventually this should be set to 
                                   //  to a filename uploaded by the user)

        } else if ($beepwho == $USER->id) {  // current user
            $outinfo = "$strtime: ". get_string("messagebeepsyou", "chat", fullname($user));
            $outmain = "";
            $output->beep = true;

        } else {
            return false;
        }

    } else if (substr($text, 0, 1) == ":") {              /// It's an MOO emote
        $outinfo = $strtime;
        $outmain = "$user->firstname ".substr($text, 1);

    } else if (substr($text, 0, 1) == "/") {     /// It's a user command

        if (substr($text, 0, 4) == "/me ") {
            $outinfo = $strtime;
            $outmain = "$user->firstname ".substr($text, 4);
        } else {
            $outinfo = $strtime;
            $outmain = $text;
        }

    } else {                                          /// It's a normal message
        $outinfo = "$strtime $user->firstname";
        $outmain = $text;
    }

    /// Format the message as a small table

    $output->text  = strip_tags("$outinfo: $outmain");

    $output->html  = "<table><tr><td valign=top>$picture</td><td><font size=2>";
    $output->html .= "<font color=\"#888888\">$outinfo</font>";
    if ($outmain) {
        $output->html .= ": $outmain";
    }
    $output->html .= "</font></td></tr></table>";

    return $output;

}

?>
