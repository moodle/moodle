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
        print_header("$course->shortname: ".get_string("participants"), "$course->fullname",
                     "<A HREF=../course/view.php?id=$course->id>$course->shortname</A> -> ".
                      get_string("participants"), "");
    } else {
        print_header("$course->shortname: ".get_string("participants"), "$course->fullname", 
                      get_string("participants"), "");
    }


    $teacherlinks = isteacher($course->id);


    if ( $teachers = get_course_teachers($course->id)) {
        echo "<H2 align=center>".$course->teacher."s</H2>";
        foreach ($teachers as $teacher) {
            print_user($teacher, $course, $teacherlinks);
        }
    }

    if ($students = get_course_students($course->id)) {
        echo "<H2 align=center>".$course->student."s</H2>";
        foreach ($students as $student) {
            print_user($student, $course, $teacherlinks);
        }
    } 

    print_footer($course);

 
/// FUNCTIONS //////////////////

function print_user($user, $course, $teacherlinks) {

    global $USER, $COUNTRIES;
    
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
    echo "<P>";
    echo get_string("email").": <A HREF=\"mailto:$user->email\">$user->email</A><BR>";
    echo get_string("location").": $user->city, ".$COUNTRIES["$user->country"]."<BR>";
    echo get_string("lastaccess").": ".userdate($user->lastaccess);
    echo "&nbsp (".format_time(time() - $user->lastaccess).")";
    echo "</TD><TD VALIGN=bottom BGCOLOR=#FFFFFF NOWRAP>";

    echo "<FONT SIZE=1>";
    if ($teacherlinks) {
        $timemidnight = usergetmidnight(time());
        echo "<A HREF=\"../course/user.php?id=$course->id&user=$user->id\">".get_string("activity")."</A><BR>";
        echo "<A HREF=\"../course/unenrol.php?id=$course->id&user=$user->id\">".get_string("unenrol")."</A><BR>";
        if (isstudent($course->id, $user->id)) {
            echo "<A HREF=\"../course/loginas.php?id=$course->id&user=$user->id\">".get_string("loginas")."</A><BR>";
        }
    } 
    echo "<A HREF=\"view.php?id=$user->id&course=$course->id\">".get_string("fullprofile")."...</A>";
    echo "</FONT>";

    echo "</TD></TR></TABLE></TD></TR></TABLE>";
}

?>
