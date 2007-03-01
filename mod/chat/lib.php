<?php  // $Id$

/// Library of functions and constants for module chat

require_once($CFG->libdir.'/pagelib.php');

if (!isset($CFG->chat_refresh_room)) {
    set_config("chat_refresh_room", 5);
}
if (!isset($CFG->chat_refresh_userlist)) {
    set_config("chat_refresh_userlist", 10);
}
if (!isset($CFG->chat_old_ping)) {
    set_config("chat_old_ping", 35);
}
if (!isset($CFG->chat_method)) {
    set_config("chat_method", "header_js");
}
if (!isset($CFG->chat_normal_updatemode)) {
    set_config("chat_normal_updatemode", 'jsupdate');
}
if (!isset($CFG->chat_serverhost)) {
    set_config("chat_serverhost", $_SERVER['HTTP_HOST']);
}
if (!isset($CFG->chat_serverip)) {
    set_config("chat_serverip", '127.0.0.1');
}
if (!isset($CFG->chat_serverport)) {
    set_config("chat_serverport", 9111);
}
if (!isset($CFG->chat_servermax)) {
    set_config("chat_servermax", 100);
}

// The HTML head for the message window to start with (<!-- nix --> is used to get some browsers starting with output
$CHAT_HTMLHEAD = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\"><html><head></head>\n<body bgcolor=\"#FFFFFF\">\n\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n";

// The HTML head for the message window to start with (with js scrolling)
$CHAT_HTMLHEAD_JS = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\"><html><head><script language=\"JavaScript\">\n<!--\nfunction move()\n{\nif (scroll_active) window.scroll(1,400000);\nwindow.setTimeout(\"move()\",100);\n}\nscroll_active = true;\nmove();\n//-->\n</script>\n</head>\n<body bgcolor=\"#FFFFFF\" onBlur=\"scroll_active = true\" onFocus=\"scroll_active = false\">\n\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n";

// The HTML code for standard empty pages (e.g. if a user was kicked out)
$CHAT_HTMLHEAD_OUT = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\"><html><head><title>You are out!</title></head><body bgcolor=\"#FFFFFF\"></body></html>";

// The HTML head for the message input page
$CHAT_HTMLHEAD_MSGINPUT = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\"><html><head><title>Message Input</title></head><body bgcolor=\"#FFFFFF\">";

// The HTML code for the message input page, with JavaScript
$CHAT_HTMLHEAD_MSGINPUT_JS = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" \"http://www.w3.org/TR/REC-html40/loose.dtd\"><html><head><title>Message Input</title>\n<script language=\"Javascript\">\n<!--\nscroll_active = true;\nfunction empty_field_and_submit()\n{\ndocument.fdummy.arsc_message.value=document.f.arsc_message.value;\ndocument.fdummy.submit();\ndocument.f.arsc_message.focus();\ndocument.f.arsc_message.select();\nreturn false;\n}\n// -->\n</script>\n</head><body bgcolor=\"#FFFFFF\" OnLoad=\"document.f.arsc_message.focus();document.f.arsc_message.select();\">";

// Dummy data that gets output to the browser as needed, in order to make it show output
$CHAT_DUMMY_DATA = "<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n<!-- nix -->\n";

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

    if (! $chat = get_record('chat', 'id', $id)) {
        return false;
    }

    $result = true;

    # Delete any dependent records here #

    if (! delete_records('chat', 'id', $chat->id)) {
        $result = false;
    }
    if (! delete_records('chat_messages', 'chatid', $chat->id)) {
        $result = false;
    }
    if (! delete_records('chat_users', 'chatid', $chat->id)) {
        $result = false;
    }

    $pagetypes = page_import_types('mod/chat/');
    foreach($pagetypes as $pagetype) {
        if(!delete_records('block_instance', 'pageid', $chat->id, 'pagetype', $pagetype)) {
            $result = false;
        }
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

    $return = NULL;
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

    $lastpingsearch = ($CFG->chat_method == 'sockets') ? '': 'AND cu.lastping > \''.$timeold.'\'';

    if (!$chatusers = get_records_sql("SELECT u.id, cu.chatid, u.firstname, u.lastname
                                        FROM {$CFG->prefix}chat_users as cu,
                                             {$CFG->prefix}chat as ch,
                                             {$CFG->prefix}user as u
                                       WHERE cu.userid = u.id
                                         AND cu.chatid = ch.id $lastpingsearch
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
                echo '</ul></div>';  // room
                $current = 0;
            }
            if ($chat = get_record('chat', 'id', $chatuser->chatid)) {
                if (!($isteacher or instance_is_visible('chat', $chat))) {  // Chat hidden to students
                    continue;
                }
                if (!$outputstarted) {
                    print_headline(get_string('currentchats', 'chat').':');
                    $outputstarted = true;
                }
                echo '<div class="room"><p class="head"><a href="'.$CFG->wwwroot.'/mod/chat/view.php?c='.$chat->id.'">'.format_string($chat->name,true).'</a></p><ul>';
            }
            $current = $chatuser->chatid;
        }
        $fullname = fullname($chatuser, $isteacher);
        echo '<li class="info name">'.$fullname.'</li>';
    }

    if ($current) {
        echo '</ul></div>';  // room
    }

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
    $students = get_records_sql("SELECT DISTINCT u.id, u.id
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

function chat_force_language($lang) {
/// This function prepares moodle to operate in given language
/// usable when $nomoodlecookie = true;
/// BEWARE: there must be no $course, $USER or $SESSION
    global $CFG;

    if(!empty($CFG->courselang)) {
        unset($CFG->courselang);
    }
    $CFG->lang = $lang;
    moodle_setlocale();
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
// login if not already logged in

function chat_login_user($chatid, $version, $groupid, $course) {
    global $USER;
    if (($version != 'sockets') and $chatuser = get_record_select('chat_users', "chatid='$chatid' AND userid='$USER->id' AND groupid='$groupid'")) {
        $chatuser->version  = $version;
        $chatuser->ip       = $USER->lastip;
        $chatuser->lastping = time();
        $chatuser->lang     = current_language();

        // Sometimes $USER->lastip is not setup properly
        // during login. Update with current value if possible
        // or provide a dummy value for the db
        if (empty($chatuser->ip)) {
            $chatuser->ip = getremoteaddr();
            if (empty($chatuser->ip)) {
                $chatuser->ip = '';
            }
        }

        if (($chatuser->course != $course->id)
         or ($chatuser->userid != $USER->id)) {
            return false;
        }
        if (!update_record('chat_users', $chatuser)) {
            return false;
        }
    } else {
        $chatuser->chatid   = $chatid;
        $chatuser->userid   = $USER->id;
        $chatuser->groupid  = $groupid;
        $chatuser->version  = $version;
        $chatuser->ip       = $USER->lastip;
        $chatuser->lastping = $chatuser->firstping = $chatuser->lastmessageping = time();
        $chatuser->sid      = random_string(32);
        $chatuser->course   = $course->id; //caching - needed for current_language too
        $chatuser->lang     = current_language(); //caching - to resource intensive to find out later

        // Sometimes $USER->lastip is not setup properly
        // during login. Update with current value if possible
        // or provide a dummy value for the db
        if (empty($chatuser->ip)) {
            $chatuser->ip = getremoteaddr();
            if (empty($chatuser->ip)) {
                $chatuser->ip = '';
            }
        }


        if (!insert_record('chat_users', $chatuser)) {
            return false;
        }

        if ($version == 'sockets') {
            // do not send 'enter' message, chatd will do it
        } else {
            $message->chatid    = $chatuser->chatid;
            $message->userid    = $chatuser->userid;
            $message->groupid   = $groupid;
            $message->message   = 'enter';
            $message->system    = 1;
            $message->timestamp = time();

            if (!insert_record('chat_messages', $message)) {
                error('Could not insert a chat message!');
            }
        }
    }

    return $chatuser->sid;
}

function chat_delete_old_users() {
// Delete the old and in the way

    global $CFG;

    $timeold = time() - $CFG->chat_old_ping;

    $query = "lastping < '$timeold'";

    if ($oldusers = get_records_select('chat_users', $query) ) {
        delete_records_select('chat_users', $query);
        foreach ($oldusers as $olduser) {
            $message->chatid    = $olduser->chatid;
            $message->userid    = $olduser->userid;
            $message->groupid   = $olduser->groupid;
            $message->message   = 'exit';
            $message->system    = 1;
            $message->timestamp = time();

            if (!insert_record('chat_messages', $message)) {
                error('Could not insert a chat message!');
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


function chat_format_message_manually($message, $courseid, $sender, $currentuser, $chat_lastrow=NULL) {
    global $CFG, $USER;

    $output = New stdClass;
    $output->beep = false;       // by default
    $output->refreshusers = false; // by default

    // Use get_user_timezone() to find the correct timezone for displaying this message:
    // It's either the current user's timezone or else decided by some Moodle config setting
    // First, "reset" $USER->timezone (which could have been set by a previous call to here)
    // because otherwise the value for the previous $currentuser will take precedence over $CFG->timezone
    $USER->timezone = 99;
    $tz = get_user_timezone($currentuser->timezone);

    // Before formatting the message time string, set $USER->timezone to the above.
    // This will allow dst_offset_on (called by userdate) to work correctly, otherwise the
    // message times appear off because DST is not taken into account when it should be.
    $USER->timezone = $tz;
    $message->strtime = userdate($message->timestamp, get_string('strftimemessage', 'chat'), $tz);

    $message->picture = print_user_picture($sender->id, 0, $sender->picture, false, true, false);
    if ($courseid) {
        $message->picture = "<a target=\"_new\" href=\"$CFG->wwwroot/user/view.php?id=$sender->id&amp;course=$courseid\">$message->picture</a>";
    }

    //Calculate the row class
    if ($chat_lastrow !== NULL) {
        $rowclass = ' class="r'.$chat_lastrow.'" ';
    } else {
        $rowclass = '';
    }

    // Start processing the message

    if(!empty($message->system)) {
        // System event
        $output->text = $message->strtime.': '.get_string('message'.$message->message, 'chat', fullname($sender));
        $output->html  = '<table class="chat-event"><tr'.$rowclass.'><td class="picture">'.$message->picture.'</td><td class="text">';
        $output->html .= '<span class="event">'.$output->text.'</span></td></tr></table>';

        if($message->message == 'exit' or $message->message == 'enter') {
            $output->refreshusers = true; //force user panel refresh ASAP
        }
        return $output;
    }

    // It's not a system event

    $text = $message->message;

    /// Parse the text to clean and filter it

    $options->para = false;
    $text = format_text($text, FORMAT_MOODLE, $options, $courseid);

    // And now check for special cases
    $special = false;

    if (substr($text, 0, 5) == 'beep ') {
        /// It's a beep! 
        $special = true;
        $beepwho = trim(substr($text, 5));

        if ($beepwho == 'all') {   // everyone
            $outinfo = $message->strtime.': '.get_string('messagebeepseveryone', 'chat', fullname($sender));
            $outmain = '';
            $output->beep = true;  // (eventually this should be set to
                                   //  to a filename uploaded by the user)

        } else if ($beepwho == $currentuser->id) {  // current user
            $outinfo = $message->strtime.': '.get_string('messagebeepsyou', 'chat', fullname($sender));
            $outmain = '';
            $output->beep = true;
          
        } else {  //something is not caught?
            return false;
        }
    } else if (substr($text, 0, 1) == '/') {     /// It's a user command
        if (trim(substr($text, 0, 4)) == '/me') {
            $special = true;
            $outinfo = $message->strtime;
            $outmain = $sender->firstname.' '.substr($text, 4);
        }
    }

    if(!$special) {
        $outinfo = $message->strtime.' '.$sender->firstname;
        $outmain = $text;
    }

    /// Format the message as a small table

    $output->text  = strip_tags($outinfo.': '.$outmain);

    $output->html  = "<table class=\"chat-message\"><tr$rowclass><td class=\"picture\" valign=\"top\">$message->picture</td><td class=\"text\">";
    $output->html .= "<span class=\"title\">$outinfo</span>";
    if ($outmain) {
        $output->html .= ": $outmain";
    }
    $output->html .= "</td></tr></table>";
    return $output;
}

function chat_format_message($message, $courseid, $currentuser, $chat_lastrow=NULL) {
/// Given a message object full of information, this function
/// formats it appropriately into text and html, then
/// returns the formatted data.

    static $users;     // Cache user lookups

    if (isset($users[$message->userid])) {
        $user = $users[$message->userid];
    } else if ($user = get_record('user', 'id', $message->userid, '','','','','id,picture,firstname,lastname')) {
        $users[$message->userid] = $user;
    } else {
        return NULL;
    }
    return chat_format_message_manually($message, $courseid, $user, $currentuser, $chat_lastrow);
}

if (!function_exists('ob_get_clean')) {
/// Compatibility function for PHP < 4.3.0
    function ob_get_clean() {
        $cont = ob_get_contents();
        if ($cont !== false) {
            ob_end_clean();
            return $cont;
        } else {
            return $cont;
        }
    }
}

function chat_get_view_actions() {
    return array('view','view all','report');
}

function chat_get_post_actions() {
    return array('talk');
}

function chat_print_overview($courses, &$htmlarray) {
    global $USER, $CFG;

    if (empty($courses) || !is_array($courses) || count($courses) == 0) {
        return array();
    }

    if (!$chats = get_all_instances_in_courses('chat',$courses)) {
        return;
    }

    $strchat = get_string('modulename', 'chat');
    $strnextsession  = get_string('nextsession', 'chat');

    foreach ($chats as $chat) {
        if ($chat->chattime and $chat->schedule) {  // A chat is scheduled
            $str = '<div class="chat overview"><div class="name">'.
                   $strchat.': <a '.($chat->visible?'':' class="dimmed"').
                   ' href="'.$CFG->wwwroot.'/mod/chat/view.php?id='.$chat->coursemodule.'">'.
                   $chat->name.'</a></div>';
            $str .= '<div class="info">'.$strnextsession.': '.userdate($chat->chattime).'</div></div>';

            if (empty($htmlarray[$chat->course]['chat'])) {
                $htmlarray[$chat->course]['chat'] = $str;
            } else {
                $htmlarray[$chat->course]['chat'] .= $str;
            }
        }
    }
}

?>
