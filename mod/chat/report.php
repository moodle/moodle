<?PHP  // $Id$

/// This page prints reports and info about chats

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);          // Course module ID
    optional_variable($groupid, "");  // Group
    optional_variable($start, "");  // Start of period
    optional_variable($end, "");    // End of period
    optional_variable($deletesession, "");    // Delete a session
    optional_variable($confirmdelete, "");    // End of period

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }
    if (! $chat = get_record("chat", "id", $cm->instance)) {
        error("Course module is incorrect");
    }
    if (! $course = get_record("course", "id", $chat->course)) {
        error("Course is misconfigured");
    }

    require_login($course->id);

    $isteacher = isteacher($course->id);
    $isteacheredit = isteacheredit($course->id);

    if (isguest() or (!$isteacher and !$chat->studentlogs)) {
        error("You can not view these chat reports");
    }

    add_to_log($course->id, "chat", "report", "report.php?id=$cm->id", "$chat->id", "$cm->id");

/// Print the page header

    if ($course->category) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
    }

    $strchats = get_string("modulenameplural", "chat");
    $strchat  = get_string("modulename", "chat");
    $strchatreport  = get_string("chatreport", "chat");
    $strseesession  = get_string("seesession", "chat");
    $strdeletesession  = get_string("deletesession", "chat");


/// Print a session if once has been specified

    if ($start and $end and !$confirmdelete) {   // Show a full transcript

        if ($groupid) {
            $groupselect = " AND groupid = '$currentgroup'";
            $groupparam = "&groupid=$currentgroup";
        } else {
            $groupselect = "";
            $groupparam = "";
        }

        print_header("$course->shortname: $chat->name: $strchatreport", "$course->fullname",
                     "$navigation <a href=\"index.php?id=$course->id\">$strchats</a> -> 
                     <a href=\"view.php?id=$cm->id\">$chat->name</a> -> 
                     <a href=\"report.php?id=$cm->id\">$strchatreport</a>", 
                      "", "", true, "", navmenu($course, $cm));

        if ($deletesession and $isteacheredit) {
            notice_yesno(get_string("deletesessionsure", "chat"), 
                         "report.php?id=$cm->id&deletesession=1&confirmdelete=1&start=$start&end=$end$groupparam", 
                         "report.php?id=$cm->id");
        }

        if (!$messages = get_records_select("chat_messages", "chatid = $chat->id AND 
                                                              timestamp >= '$start' AND 
                                                              timestamp <= '$end' $groupselect", "timestamp ASC")) {
            print_heading(get_string("nomessages", "chat"));

        } else {
            echo "<p align=\"center\">".userdate($start)." --> ". userdate($end)."</p>";

            print_simple_box_start("center");
            foreach ($messages as $message) {  // We are walking FORWARDS through messages
                $formatmessage = chat_format_message($message, $course->id);
                echo $formatmessage->html;
            }
            print_simple_box_end("center");
        }

        if (!$deletesession or !$isteacheredit) {
            print_continue("report.php?id=$cm->id");
        }

        print_footer($course);
        exit;
    }


/// Print the Sessions display

    print_header("$course->shortname: $chat->name: $strchatreport", "$course->fullname",
                 "$navigation <a href=\"index.php?id=$course->id\">$strchats</a> -> 
                 <a href=\"view.php?id=$cm->id\">$chat->name</a> -> $strchatreport", 
                  "", "", true, "", navmenu($course, $cm));

    print_heading($chat->name.": ".get_string("sessions", "chat"));


/// Check to see if groups are being used here
    if ($groupmode = groupmode($course, $cm)) {   // Groups are being used
        $currentgroup = setup_and_print_groups($course, $groupmode, "report.php?id=$cm->id");
    } else {
        $currentgroup = false;
    }

    if ($currentgroup) {
        $groupselect = " AND groupid = '$currentgroup'";
        $groupparam = "&groupid=$currentgroup";
    } else {
        $groupselect = "";
        $groupparam = "";
    }

/// Delete a session if one has been specified

    if ($deletesession and $isteacheredit and $confirmdelete and $start and $end) {
        delete_records_select("chat_messages", "chatid = $chat->id AND 
                                            timestamp >= '$start' AND 
                                            timestamp <= '$end' $groupselect");
        $strdeleted  = get_string("deleted");
        notify("$strdeleted: ".userdate($start)." --> ". userdate($end));
        unset($deletesession);
    }


/// Get the messages

    if (empty($messages)) {   /// May have already got them above
        if (!$messages = get_records_select("chat_messages", "chatid = '$chat->id' $groupselect", "timestamp DESC")) {
            print_heading(get_string("nomessages", "chat"));
            print_footer($course);
            exit;
        }
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
                        echo "&nbsp;".fullname($user, $isteacher);
                        echo "&nbsp;($usermessagecount)<br />";
                    }
                }

                echo "<p align=\"right\">";
                echo "<a href=\"report.php?id=$cm->id&start=$sessionstart&end=$sessionend$groupparam\">$strseesession</a>";
                if ($isteacheredit) {
                    echo "<br /><a href=\"report.php?id=$cm->id&start=$sessionstart&end=$sessionend&deletesession=1$groupparam\">$strdeletesession</a>";
                }
                echo "</p>";
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
