<? // $Id$

$MAXNEWSDISPLAY = 4;

$FORMATS = array (
             "weeks" => "Weekly layout",
             "social" => "Social layout",
             "topics" => "Topics layout"
           );

$SECTION = array (
             "weeks" => "week",
             "social" => "section",
             "topics" => "topic"
           );


function print_log_selector_form($course, $selecteduser=0, $selecteddate="today") {

    global $USER, $CFG;

    // Get all the possible users
    $users = array();

    if ($course->category) {
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
    }

    if (isadmin()) {
        if ($ccc = get_records_sql("SELECT * FROM course ORDER BY fullname")) {
            foreach ($ccc as $cc) {
                if ($cc->category) {
                    $courses["$cc->id"] = "$cc->fullname";
                } else {
                    $courses["$cc->id"] = " $cc->fullname (Site)";
                }
            }
        }
        asort($courses);
    }

    asort($users);

    // Get all the possible dates
    // Note that we are keeping track of real (GMT) time and user time
    // User time is only used in displays - all calcs and passing is GMT

    $timenow = time(); // GMT

    // What day is it now for the user, and when is midnight that day (in GMT).
    $timemidnight = $today = usergetmidnight($timenow);

    // Put today up the top of the list
    $dates = array("$timemidnight" => "Today, ".userdate($timenow, "%e %B %Y") );

    if (! $course->startdate) {
        $course->startdate = $course->timecreated;
    }

    $numdates = 1;
    while ($timemidnight > $course->startdate and $numdates < 365) {
        $timemidnight = $timemidnight - 86400;
        $timenow = $timenow - 86400;
        $dates["$timemidnight"] = userdate($timenow, "%A, %e %B %Y");
        $numdates++;
    }

    if ($selecteddate == "today") {
        $selecteddate = $today;
    }

    echo "<CENTER>";
    echo "<FORM ACTION=log.php METHOD=get>";
    if (isadmin()) {
        choose_from_menu ($courses, "id", $course->id, "");
    } else {
        echo "<INPUT TYPE=hidden NAME=id VALUE=\"$course->id\">";
    }
    if ($course->category) {
        choose_from_menu ($users, "user", $selecteduser, "All participants");
    }
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
// It is assumed that $date is the GMT time of midnight for that day, 
// and so the next 86400 seconds worth of logs are printed.

    if ($course->category) {
        $selector = "WHERE l.course='$course->id' AND l.user = u.id";
    } else {
        $selector = "WHERE l.user = u.id";  // Show all courses
        if ($ccc = get_records_sql("SELECT * FROM course ORDER BY fullname")) {
            foreach ($ccc as $cc) {
                $courses[$cc->id] = "$cc->shortname";
            }
        }
    }

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
        if (! $course->category) {
            echo "<TD><FONT SIZE=2><A HREF=\"view.php?id=$log->course\">".$courses[$log->course]."</A></TD>";
        }
        echo "<TD ALIGN=right><FONT SIZE=2>".userdate($log->time, "%A")."</TD>";
        echo "<TD><FONT SIZE=2>".userdate($log->time, "%e %B %Y, %I:%M %p")."</TD>";
        echo "<TD><FONT SIZE=2><A TITLE=\"$log->ip\" HREF=\"../user/view.php?id=$log->user&course=$log->course\"><B>$log->firstname $log->lastname</B></TD>";
        echo "<TD><FONT SIZE=2>";
        link_to_popup_window( make_log_url($log->module,$log->url), "fromloglive","$log->module $log->action", 400, 600);
        echo "</TD>";
        echo "<TD><FONT SIZE=2>$log->info</TD>";
        echo "</TR>";
    }
    echo "</TABLE>";
}


function print_all_courses($cat=1) {

    if ($courses = get_records("course", "category", $cat, "fullname ASC")) {
        foreach ($courses as $course) {
            print_course($course);
            echo "<BR>\n";
        }

    } else {
        echo "<H3>No courses have been defined yet</H3>";
    }
}


function print_course($course) {

    global $CFG;

    if (! $site = get_record("course", "category", "0") ) {
        error("Could not find a site!");
    }

    print_simple_box_start("CENTER", "100%");

    echo "<TABLE WIDTH=100%>";
    echo "<TR VALIGN=top>";
    echo "<TD VALIGN=top WIDTH=50%>";
    echo "<P><FONT SIZE=3><B><A TITLE=\"".get_string("entercourse")."\" 
              HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->fullname</A></B></FONT></P>";
    if ($teachers = get_records_sql("SELECT u.* FROM user u, user_teachers t 
                                     WHERE u.id = t.user AND t.course = '$course->id' 
                                     ORDER BY t.authority ASC")) {

        echo "<P><FONT SIZE=1>\n";
        foreach ($teachers as $teacher) {
            echo "$course->teacher: <A HREF=\"$CFG->wwwroot/user/view.php?id=$teacher->id&course=$site->id\">$teacher->firstname $teacher->lastname</A><BR>";
        }
        echo "</FONT></P>";
    }
    if ($course->guest or ($course->password == "")) {
        echo "<A TITLE=\"This course allows guest users\" HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">";
        echo "<IMG VSPACE=4 ALT=\"\" HEIGHT=16 WIDTH=16 BORDER=0 SRC=\"$CFG->wwwroot/user/user.gif\"></A>&nbsp;&nbsp;";
    }
    if ($course->password) {
        echo "<A TITLE=\"This course requires an enrolment key\" HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">";
        echo "<IMG VSPACE=4 ALT=\"\" HEIGHT=16 WIDTH=16 BORDER=0 SRC=\"$CFG->wwwroot/pix/i/key.gif\"></A>";
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
                print_headline("New users:");
                $heading = true;
                $content = true;
            }
            $user = get_record("user", "id", $log->info);
            echo "<P><FONT SIZE=1><A HREF=\"../user/view.php?id=$user->id&course=$course->id\">$user->firstname $user->lastname</A></FONT></P>";
        }
    }

    // Next, have there been any changes to the course structure?

    foreach ($logs as $log) {
        if ($log->module == "course") {
            if ($log->action == "add mod" or $log->action == "update mod" or $log->action == "delete mod") {
                $info = split(" ", $log->info);
                $modname = get_field($info[0], "name", "id", $info[1]);
            
                if ($info[0] == "discuss") {
                    $info[0] = "discussion";  // nasty hack, really.
                }

                switch ($log->action) {
                    case "add mod":
                       $changelist["$log->info"] = array ("operation" => "add", "text" => "Added a ".$info[0].":<BR><A HREF=\"$CFG->wwwroot/course/$log->url\">$modname</A>");
                    break;
                    case "update mod":
                       if (! $changelist["$log->info"]) {
                           $changelist["$log->info"] = array ("operation" => "update", "text" => "Updated the ".$info[0].":<BR><A HREF=\"$CFG->wwwroot/course/$log->url\">$modname</A>");
                       }
                    break;
                    case "delete mod":
                       if ($changelist["$log->info"]["operation"] == "add") {
                           $changelist["$log->info"] = NULL;
                       } else {
                           $changelist["$log->info"] = array ("operation" => "delete", "text" => "Deleted a ".$info[0]);
                       }
                    break;
                }
            }
        }
    }

    if ($changelist) {
        foreach ($changelist as $changeinfo => $change) {
            if ($change) {
                $changes[$changeinfo] = $change;
            }
        }
        if (count($changes) > 0) {
            print_headline("Course changes:");
            $content = true;
            foreach ($changes as $changeinfo => $change) {
                echo "<P><FONT SIZE=1>".$change["text"]."</FONT></P>";
            }
        }
    }


    // Now all we need to know are the new posts.

    $heading = false;
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
                    print_headline("Discussion Posts:");
                    $heading = true;
                    $content = true;
                }
                echo "<P><FONT SIZE=1>$post->firstname $post->lastname:<BR>";
                echo "\"<A HREF=\"$CFG->wwwroot/mod/discuss/$log->url\">";
                if ($log->action == "add") {
                    echo "<B>$post->subject</B>";
                } else {
                    echo "$post->subject";
                }
                echo "</A>\"</FONT></P>";
            }

        }
    }

    if (! $content) {
        echo "<FONT SIZE=2>Nothing new since your last login</FONT>";
    }

}


function unenrol_student_in_course($user, $course) {
    global $db;

    return $db->Execute("DELETE FROM user_students WHERE user = '$user' AND course = '$course'");
}



function enrol_student_in_course($user, $course) {
    global $db;

	$timenow = time();

	$rs = $db->Execute("INSERT INTO user_students (user, course, start, end, time) 
                        VALUES ($user, $course, 0, 0, $timenow)");
	if ($rs) {
		return true;
	} else {
	    return false;
	}
}

?>
