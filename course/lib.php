<? // $Id$

$MAXNEWSDISPLAY = 4;

$FORMATS = array (
             "1" => "Weekly layout",
             "2" => "Social layout"
           );


function logdate($date) {
    return date("l, j F Y, g:i A", $date);
}

function print_log_selector_form($course, $selecteduser=0, $selecteddate="today") {

    // Get all the possible users
    $users = array();
    if ($students = get_records_sql("SELECT u.* FROM user u, user_students s 
                                     WHERE s.course = '$course->id' AND s.user = u.id
                                     ORDER BY u.lastaccess DESC")) {
        foreach ($students as $student) {
            $users["$student->id"] = "$student->firstname $student->lastname";
        }
    }
    if ($teachers = get_records_sql("SELECT u.* FROM user u, user_teachers t 
                                     WHERE t.course = '$course->id' AND t.user = u.id
                                     ORDER BY u.lastaccess DESC")) {
        foreach ($teachers as $teacher) {
            $users["$teacher->id"] = "$teacher->firstname $teacher->lastname";
        }
    }

    asort($users);

    // Get all the possible dates
    $tt = getdate(time());
    $timemidnight = $today = mktime (0, 0, 0, $tt["mon"], $tt["mday"], $tt["year"]);
    $dates = array("$today" => "Today, ".date("j F Y", $today) );

    while ($timemidnight > $course->startdate) {
        $timemidnight = $timemidnight - 86400;
        $dates["$timemidnight"] = date("l, j F Y", $timemidnight);
    }

    if ($selecteddate == "today") {
        $selecteddate = $today;
    }

    echo "<CENTER>";
    echo "<FORM ACTION=log.php METHOD=get>";
    echo "<INPUT TYPE=hidden NAME=id VALUE=\"$course->id\">";
    choose_from_menu ($users, "user", $selecteduser, "All participants");
    choose_from_menu ($dates, "date", $selecteddate, "Any day");
    echo "<INPUT TYPE=submit VALUE=\"Show these logs\">";
    echo "</FORM>";
    echo "</CENTER>";
}

function make_log_url($module, $url) {
    switch ($module) {
        case "course":
        case "user":
        case "file":
        case "login":
        case "lib":
        case "admin":
            return "/$module/$url";
            break;
        default:
            return "/mod/$module/$url";
            break;
    }
}


function print_log($course, $user=0, $date=0, $order="ORDER BY l.time ASC") {

    $selector = "WHERE l.course='$course->id' AND l.user = u.id";

    if ($user) {
        $selector .= " AND l.user = '$user'";
    }

    if ($date) {
        $enddate = $date + 86400;
        $selector .= " AND l.time > '$date' AND l.time < '$enddate'";
    }

    if (!$logs = get_records_sql("SELECT l.*, u.firstname, u.lastname, u.picture 
                                  FROM log l, user u $selector $order")){
        notify("No logs found!");
        print_footer($course);
        exit;
    }

    $count=0;
    $tt = getdate(time());
    $today = mktime (0, 0, 0, $tt["mon"], $tt["mday"], $tt["year"]);
    echo "<P ALIGN=CENTER>Displaying ".count($logs)." records</P>";
    echo "<TABLE BORDER=0 ALIGN=center CELLPADDING=3 CELLSPACING=3>";
    foreach ($logs as $log) {

        if ($ld = get_record_sql("SELECT * FROM log_display WHERE module='$log->module' AND action='$log->action'")) {
            $log->info = get_field($ld->table, $ld->field, "id", $log->info);
        }

        echo "<TR>";
        echo "<TD ALIGN=right><FONT SIZE=2>".date("l", $log->time)."</TD>";
        echo "<TD><FONT SIZE=2>".date("j M Y, h:i A", $log->time)."</TD>";
        echo "<TD><FONT SIZE=2><B>$log->firstname $log->lastname</B></TD>";
        echo "<TD><FONT SIZE=2>";
        link_to_popup_window( make_log_url($log->module,$log->url), "fromloglive","$log->module $log->action", 400, 600);
        echo "</TD>";
        echo "<TD><FONT SIZE=2>$log->info</TD>";
        echo "</TR>";
    }
    echo "</TABLE>";
}


function print_course($course) {

    if (! $site = get_record("course", "category", "0") ) {
        error("Could not find a site!");
    }

    print_simple_box_start("CENTER", "80%");

    echo "<TABLE WIDTH=100%>";
    echo "<TR VALIGN=top><TD VALIGN=top WIDTH=50%>";
    echo "<P><FONT SIZE=3><B><A HREF=\"view.php?id=$course->id\">$course->fullname</A></B></FONT></P>";
    if ($teachers = get_records_sql("SELECT u.* FROM user u, user_teachers t 
                                     WHERE u.id = t.user AND t.course = '$course->id' 
                                     ORDER BY t.authority ASC")) {

        echo "<P><FONT SIZE=1>\n";
        foreach ($teachers as $teacher) {
            echo "$course->teacher: <A HREF=\"../user/view.php?id=$teacher->id&course=$site->id\">$teacher->firstname $teacher->lastname</A><BR>";
        }
        echo "</FONT></P>";
     }
     echo "</TD><TD VALIGN=top WIDTH=50%>";
     echo "<P><FONT SIZE=2>".text_to_html($course->summary)."</FONT></P>";
     echo "</TD></TR>";
     echo "</TABLE>";

     print_simple_box_end();
}

function print_headline($text, $size=2) {
    echo "<B><FONT SIZE=\"$size\">$text</FONT></B><BR>\n";
}

function print_recent_activity($course) {
    // $course is an object
    // This function trawls through the logs looking for 
    // anything new since the user's last login

    global $CFG, $USER;

    if (! $USER->lastlogin ) {
        echo "<P>Welcome to the course! Here you will find a list of what's new since your last login.</P>";
        return;
    }

    if (! $logs = get_records_sql("SELECT * FROM log WHERE time > '$USER->lastlogin' AND course = '$course->id' ORDER BY time ASC")) {
        return;
    }


    // Firstly, have there been any new enrolments?

    $heading = false;
    $content = false;
    foreach ($logs as $log) {
        if ($log->module == "course" and $log->action == "enrol") {
            if (! $heading) {
                print_headline("New users");
                $heading = true;
                $content = true;
            }
            $user = get_record("user", "id", $log->info);
            echo "<LI><FONT SIZE=1><A HREF=\"../user/view.php?id=$user->id&course=$course->id\">$user->firstname $user->lastname</A></FONT></LI>";
        }
    }

    // Next, have there been any changes to the course structure?

    if ($heading) {
        echo "<BR>";
        $heading = false;
    }
    foreach ($logs as $log) {
        if ($log->module == "course") {
            if ($log->action == "add mod" or $log->action == "update mod" or $log->action == "delete mod") {
                if (! $heading) {
                    print_headline("Changes");
                    $heading = true;
                    $content = true;
                }
                $info = split(" ", $log->info);
                $modname = get_field($info[0], "name", "id", $info[1]);
            
                if ($info[0] == "discuss") {
                    $info[0] == "discussion";  // nasty hack, really.
                }

                echo "<LI><FONT SIZE=1>";
                switch ($log->action) {
                    case "add mod":
                       echo "Added a ".$info[0].": $modname";
                    break;
                    case "update mod":
                       echo "Updated the ".$info[0].": <A HREF=\"$CFG->wwwroot/course/$log->url\">$modname</A>";
                    break;
                    case "delete mod":
                       echo "Deleted a ".$info[0];
                    break;
                }
                echo "</FONT></LI>";
            }
        }
    }


    // Now all we need to know are the new posts.

    if ($heading) {
        echo "<BR>";
        $heading = false;
        $content = true;
    }
    foreach ($logs as $log) {
        
        if ($log->module == "discuss") {
            $post = NULL;

            if ($log->action == "add post") {
                $post = get_record_sql("SELECT p.*, u.firstname, u.lastname, 
                                               u.email, u.picture, u.id as userid
                                        FROM discuss_posts p, user u 
                                        WHERE p.id = '$log->info' AND p.user = u.id");

            } else if ($log->action == "add") {
                $post = get_record_sql("SELECT p.*, u.firstname, u.lastname, 
                                               u.email, u.picture, u.id as userid
                                        FROM discuss d, discuss_posts p, user u 
                                        WHERE d.id = '$log->info' AND d.firstpost = p.id AND p.user = u.id");
            }

            if ($post) {
                if (! $heading) {
                    print_headline("Discussion Posts");
                    $heading = true;
                    $content = true;
                }
                if ($log->action == "add") {
                    echo "<LI><FONT SIZE=1>\"<A HREF=\"$CFG->wwwroot/mod/discuss/$log->url\"><B>$post->subject</B></A>\" by $post->firstname $post->lastname</FONT></LI>";
                } else {
                    echo "<LI><FONT SIZE=1>\"<A HREF=\"$CFG->wwwroot/mod/discuss/$log->url\">$post->subject</A>\" by $post->firstname $post->lastname</FONT></LI>";
                }
            }

        }
    }

    if (! $content) {
        echo "<FONT SIZE=2>Nothing new since your last login</FONT>";
    }

}

?>
