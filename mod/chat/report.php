<?PHP  // $Id$

/// This page prints reports and info about chats

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);    // Chat Module ID, or

    if (! $chat = get_record("chat", "id", $id)) {
        error("Course module is incorrect");
    }
    if (! $course = get_record("course", "id", $chat->course)) {
        error("Course is misconfigured");
    }
    if (! $cm = get_coursemodule_from_instance("chat", $chat->id, $course->id)) {
        error("Course Module ID was incorrect");
    }

    require_login($course->id);

    if (!isteacher($course->id) and !$chat->studentlogs) {
        error("Only teachers are allowed to view these chat reports");
    }

    add_to_log($course->id, "chat", "view", "view.php?id=$cm->id", "$chat->id");

/// Print the page header

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strchats = get_string("modulenameplural", "chat");
    $strchat  = get_string("modulename", "chat");
    $strchatreport  = get_string("chatreport", "chat");

    print_header("$course->shortname: $chat->name: $strchatreport", "$course->fullname",
                 "$navigation <a href=\"index.php?id=$course->id\">$strchats</a> -> 
                 <a href=\"view.php?id=$cm->id\">$chat->name</a> -> $strchatreport", 
                  "", "", true, "", navmenu($course, $cm));

/// Print the main part of the page

    print_heading($chat->name);

    print_simple_box( get_string("sessions", "chat"), "center");

    if (!$messages = get_records("chat_messages", "chatid", $chat->id, "timestamp DESC")) {
        print_heading(get_string("nomessages", "chat"));
        print_footer($course);
        exit;
    }

    $sessiongap = 5 * 60;    // 5 minutes
    $sessionstart = 0;
    $sessionend   = 0;
    $sessionusers = array();
    $timelast   = 0;

    foreach ($messages as $message) {
        if (!$timelast) {
            $timelast = $message->timestamp;
        }
        if (!$sessionstart) {
            $sessionstart = $message->timestamp;
        }
        if ($message->timestamp - $timelast < $sessiongap) {  // Same session
            $sessionusers[$message->user] = $message->timestamp;  // Remember user
        } else {  
            $sessionend = $lasttime;

            print_heading(usertime($sessionstart)." --> ". usertime($sessionend));

            print_simple_box_start("center");

            foreach ($sessionusers as $sessionuser => $lastusertime) {
                $user = get_record("user", "id", $sessionuser);
                print_user_picture($user->id, $course->id, $user->picture);
            }

            print_simple_box_end();

            $sessionstart = $message->timestamp;
            $sessionusers = array();
            $sessionusers[$message->user] = $message->timestamp;  // Remember user
        }
        $timelast = $message->timestamp;
    }

/// Finish the page
    print_footer($course);

?>
