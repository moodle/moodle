<?PHP // $Id$
      // Script to assign students to courses

	require_once("../config.php");

    define("MAX_USERS_PER_PAGE", 50);

    require_variable($id);         // course id
    optional_variable($add, "");
    optional_variable($remove, "");
    optional_variable($search, ""); // search string

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect (can't find it)");
    }

    require_login($course->id);

    if (!isteacheredit($course->id)) {
        error("You must be an editing teacher in this course, or an admin");
    }

    $strassignstudents = get_string("assignstudents");
    $strexistingstudents   = get_string("existingstudents");
    $strnoexistingstudents = get_string("noexistingstudents");
    $strpotentialstudents  = get_string("potentialstudents");
    $strnopotentialstudents  = get_string("nopotentialstudents");
    $straddstudent    = get_string("addstudent");
    $strremovestudent = get_string("removestudent");
    $strsearch        = get_string("search");
    $strsearchresults  = get_string("searchresults");
    $strsearchagain   = get_string("searchagain");
    $strtoomanytoshow   = get_string("toomanytoshow");
    $strstudents   = get_string("students");
    $strunenrolallstudents  = get_string("unenrolallstudents");
    $strunenrolallstudentssure  = get_string("unenrolallstudentssure");


    if ($search) {
        $searchstring = $strsearchagain;
    } else {
        $searchstring = $strsearch;
    }

    if ($course->students != $strstudents) {
        $parastudents = " ($course->students)";
    } else {
        $parastudents = "";
    }

	print_header("$course->shortname: $strassignstudents", 
                 "$site->fullname", 
                 "<a href=\"view.php?id=$course->id\">$course->shortname</a> -> $strassignstudents", "");

/// Add a student if one is specified

    if (!empty($add)) {
        check_for_restricted_user($USER->username, "$CFG->wwwroot/course/student.php?id=$course->id");
        if (! enrol_student($add, $course->id)) {
            error("Could not add that student to this course!");
        }
    }

/// Remove a student if one is specified.

    if (!empty($remove)) {
        check_for_restricted_user($USER->username, "$CFG->wwwroot/course/student.php?id=$course->id");
        if (! unenrol_student($remove, $course->id)) {
            error("Could not remove that student from this course!");
        }
    }

/// Remove all students from specified course

    if (!empty($removeall)) {
        check_for_restricted_user($USER->username, "$CFG->wwwroot/course/student.php?id=$course->id");
        $students = get_course_students($course->id, "u.lastname ASC, u.firstname ASC");
        foreach ($students as $student) {
            if (! unenrol_student($student->id, $course->id)) {
                $fullname = fullname($student, true);
                notify("Could not remove $fullname from this course!");
            }
        }
    }

/// Print a help notice about the need to use this page

    if (empty($add) and empty($remove) and empty($search)) {
        $note = get_string("assignstudentsnote");
        if ($course->password) {
            $note .= "<p>".get_string("assignstudentspass", "", "<a href=\"edit.php?id=$course->id\">$course->password</a>");
        }
        print_simple_box($note, "center", "50%");
    }

/// Get all existing students for this course.
    $students = get_course_students($course->id, "u.lastname ASC, u.firstname ASC");

/// Print the lists of existing and potential students

    echo "<table cellpadding=1 cellspacing=5 align=center>";
    echo "<tr><th width=50%>$strexistingstudents$parastudents</th><td>&nbsp;</td><th width=50%>$strpotentialstudents</th></tr>";
    echo "<tr><td width=50% nowrap valign=top>";

/// First, show existing students for this course

    if (empty($students)) { 
        echo "<p align=center>$strnoexistingstudents</a>";
        $studentlist = "";

    } else {
        $studentarray = array();
        foreach ($students as $student) {
            $studentarray[] = $student->id;
            $fullname = fullname($student, true);
            echo "<p align=right>$fullname, $student->email &nbsp;&nbsp; <a href=\"student.php?id=$course->id&remove=$student->id\" title=\"$strremovestudent\"><img src=\"../pix/t/right.gif\" border=0></a></p>";
        }
        $studentlist = implode(",",$studentarray);
        unset($studentarray);

        // button to unenrol all students from course

        echo "<p>&nbsp;</p>\n";
        echo "<p align=\"center\">\n";
        echo "<input type=\"button\" value=\"$strunenrolallstudents\" ".
             " OnClick=\"ctemp = window.confirm('".addslashes($strunenrolallstudentssure)."'); ".
             " if(ctemp) window.location.href='student.php?id=$course->id&removeall=1';\"/>\n";
        echo "</p>\n";
    }

    echo "<td>&nbsp;</td>";
    echo "<td width=50% nowrap valign=top>";

/// Print list of potential students

    $usercount = get_users(false, $search, true, $studentlist, "lastname ASC, firstname ASC");

    if ($usercount == 0) {
        echo "<p align=center>$strnopotentialstudents</p>";

    } else if ($usercount > MAX_USERS_PER_PAGE) {
        echo "<p align=center>$strtoomanytoshow ($usercount) </p>";

    } else {

        if ($search) {
            echo "<p align=center>($strsearchresults : $search)</p>";
        }

        if (!$users = get_users(true, $search, true, $studentlist)) {
            error("Could not get users!");
        }

        foreach ($users as $user) {
            $fullname = fullname($user, true);
            echo "<p align=left><a href=\"student.php?id=$course->id&add=$user->id\"".
                   "title=\"$straddstudent\"><img src=\"../pix/t/left.gif\"".
                   "border=0></a>&nbsp;&nbsp;$fullname, $user->email";
        }
    }

    if ($search or $usercount > MAX_USERS_PER_PAGE) {
        echo "<form action=student.php method=post>";
        echo "<input type=hidden name=id value=\"$course->id\">";
        echo "<input type=text name=search size=20>";
        echo "<input type=submit value=\"$searchstring\">";
        echo "</form>";
    }

    echo "</tr></table>";

    print_footer();

?>
