<?PHP  // $Id$

/// This page prints reports and info about chats

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);      // Chat Module ID, or
    optional_variable($start, 0);  // Start of period
    optional_variable($end, 0);    // End of period

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

/// Print a session if once has been specified

    if ($start and $end) {   // Show a full transcript

        print_header("$course->shortname: $chat->name: $strchatreport", "$course->fullname",
                     "$navigation <a href=\"index.php?id=$course->id\">$strchats</a> -> 
                     <a href=\"view.php?id=$cm->id\">$chat->name</a> -> 
                     <a href=\"report.php?id=$chat->id\">$strchatreport</a>", 
                      "", "", true, "", navmenu($course, $cm));

        if (!$messages = get_records_select("chat_messages", "chatid = $chat->id AND 
                                                              timestamp > '$start' AND 
                                                              timestamp < '$end'", "timestamp ASC")) {
            print_heading(get_string("nomessages", "chat"));

        } else {
            echo "<p align=\"center\">".userdate($start)." --> ". userdate($end)."</p>";

            print_simple_box_start("center");
            foreach ($messages as $message) {  // We are walking FORWARDS through messages
                $formatmessage = chat_format_message($message);
                echo $formatmessage->html;
            }
            print_simple_box_end("center");
        }

        print_continue("report.php?id=$chat->id");
        print_footer($course);
        exit;
    }


/// Print the Sessions display

    print_header("$course->shortname: $chat->name: $strchatreport", "$course->fullname",
                 "$navigation <a href=\"index.php?id=$course->id\">$strchats</a> -> 
                 <a href=\"view.php?id=$cm->id\">$chat->name</a> -> $strchatreport", 
                  "", "", true, "", navmenu($course, $cm));


    print_heading($chat->name.": ".get_string("sessions", "chat"));

    if (!$messages = get_records("chat_messages", "chatid", $chat->id, "timestamp DESC")) {
        print_heading(get_string("nomessages", "chat"));
        print_footer($course);
        exit;
    }

    /// Show all the sessions

    $sessiongap = 5 * 60;    // 5 minutes silence means a new session
    $sessionend = 0;
    $sessionstart   = 0;
    $sessionusers = array();
    $lasttime   = 0;

    foreach ($messages as $message) {  // We are walking BACKWARDS through the messages
        if (!$lasttime) {
            $lasttime = $message->timestamp;
        }
        if (!$sessionend) {
            $sessionend = $message->timestamp;
        }
        if (($lasttime - $message->timestamp) < $sessiongap) {  // Same session
            if ($message->userid and !$message->system) {       // Remember user and count messages
                if (empty($sessionusers[$message->userid])) {
                    $sessionusers[$message->userid] = 1;
                } else {
                    $sessionusers[$message->userid] ++;
                }
            }
        } else {  
            $sessionstart = $lasttime;

            if ($sessionend - $sessionstart > 60 and count($sessionusers) > 1) {

                echo "<p align=\"center\">".userdate($sessionstart)." --> ". userdate($sessionend)."</p>";

                print_simple_box_start("center");

                arsort($sessionusers);
                foreach ($sessionusers as $sessionuser => $usermessagecount) {
                    if ($user = get_record("user", "id", $sessionuser)) {
                        print_user_picture($user->id, $course->id, $user->picture);
                        echo "&nbsp;$user->firstname $user->lastname";
                        echo "&nbsp;($usermessagecount) <br />";
                    }
                }

                echo "<p align=\"right\"><a href=\"report.php?id=$chat->id&start=$sessionstart&end=$sessionend\">see chat</a>";
                print_simple_box_end();
            }

            $sessionend = $message->timestamp;
            $sessionusers = array();
            $sessionusers[$message->userid] = 1;
        }
        $lasttime = $message->timestamp;
    }

/// Finish the page
    print_footer($course);

?>
