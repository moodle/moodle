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
                                  FROM logs l, user u $selector $order")){
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
        $count++;
        
        echo "<TR>";
        echo "<TD ALIGN=right><FONT SIZE=2>".date("l", $log->time)."</TD>";
        echo "<TD><FONT SIZE=2>".date("j M Y, h:i A", $log->time)."</TD>";
        echo "<TD><FONT SIZE=2><B>$log->firstname $log->lastname</B></TD>";
        echo "<TD><FONT SIZE=2>";
        $log->message = addslashes($log->message);
        link_to_popup_window("$log->url","popup","$log->message", 400, 600);
        echo "</TD>";
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

?>
