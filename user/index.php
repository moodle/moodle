<?PHP // $Id$

//  Lists all the users within a given course

    require("../config.php");
    require("../lib/countries.php");
    require("lib.php");

    require_variable($id);   //course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);

    add_to_log($course->id, "user", "view all", "index.php?id=$course->id", "");

    if ($course->category) {
        print_header("$course->shortname: Participants", "$course->fullname",
                     "<A HREF=../course/view.php?id=$course->id>$course->shortname</A> -> Participants", "");
    } else {
        print_header("$course->shortname: Participants", "$course->fullname", "Participants", "");
    }


    $teacherlinks = isteacher($course->id);

    echo "<H2 align=center>".$course->teacher."s</H2>";

    if ( $teachers = get_records_sql("SELECT u.* FROM user u, user_teachers t 
                                       WHERE t.course = '$course->id' AND t.user = u.id
                                       ORDER BY t.authority")) {
        foreach ($teachers as $teacher) {
            print_user($teacher, $course, $teacherlinks);
        }
    } else {
        notify("None yet");
    }

    echo "<H2 align=center>".$course->student."s</H2>";
    if ($students = get_records_sql("SELECT u.* FROM user u, user_students s 
                                       WHERE s.course = '$course->id' AND s.user = u.id
                                       ORDER BY u.lastaccess DESC")) {
        foreach ($students as $student) {
            print_user($student, $course, $teacherlinks);
        }
    } else {
        notify("None yet");
    }

    print_footer($course);

 
/// FUNCTIONS //////////////////

function print_user($user, $course, $teacherlinks) {

    global $COUNTRIES;
    
    echo "<TABLE WIDTH=80% ALIGN=CENTER BORDER=0 CELLPADDING=1 CELLSPACING=1><TR><TD BGCOLOR=#888888>";
    echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0><TR>";
    echo "<TD WIDTH=100 BGCOLOR=#FFFFFF VALIGN=top>";
    echo "<A HREF=\"view.php?id=$user->id&course=$course->id\">";
    if ($user->picture) {
        echo "<IMG BORDER=0 ALIGN=left WIDTH=100 SRC=\"pix.php/$user->id/f1.jpg\">";
    } else {
        echo "<IMG BORDER=0 ALIGN=left WIDTH=100 SRC=\"default/f1.jpg\">";
    }
    echo "</A>";
    echo "</TD><TD WIDTH=100% BGCOLOR=#FFFFFF VALIGN=top>";
    echo "<FONT SIZE=-1>";
    echo "<FONT SIZE=3><B>$user->firstname $user->lastname</B></FONT>";
    echo "<P>Email: <A HREF=\"mailto:$user->email\">$user->email</A><BR>";
    echo "Location: $user->city, ".$COUNTRIES["$user->country"]."<BR>";
    echo "Last access: ".userdate($user->lastaccess);
    echo "&nbsp (".format_time(time() - $user->lastaccess).")";
    echo "</TD><TD VALIGN=bottom BGCOLOR=#FFFFFF NOWRAP>";

    echo "<FONT SIZE=1>";
    if ($teacherlinks) {
        $tt = usergetdate(time());
        $today = mktime (0, 0, 0, $tt["mon"], $tt["mday"], $tt["year"]);
        echo "<A HREF=\"../course/user.php?id=$course->id&user=$user->id\">Contributions</A><BR>";
        echo "<A HREF=\"../course/log.php?id=$course->id&user=$user->id&date=$today\">Today's logs</A><BR>";
        echo "<A HREF=\"../course/log.php?id=$course->id&user=$user->id\">All logs</A><BR>";
        if (isstudent($course->id, $user->id)) {
            echo "<A HREF=\"../course/loginas.php?id=$course->id&user=$user->id\">Login as</A><BR>";
        }
    }
    echo "<A HREF=\"view.php?id=$user->id&course=$course->id\">Full profile...</A>";
    echo "</FONT>";

    echo "</TD></TR></TABLE></TD></TR></TABLE>";
}

?>
