<?PHP // $Id$
      // Script to assign students to courses

	require_once("../config.php");

    define("MAX_USERS_PER_PAGE", 30);

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
        error("You must be able to edit this course to assign students");
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

    if ($search) {
        $searchstring = $strsearchagain;
    } else {
        $searchstring = $strsearch;
    }

	print_header("$course->shortname: $strassignstudents", 
                 "$site->fullname", 
                 "<a href=\"view.php?id=$course->id\">$course->shortname</a> -> $strassignstudents", "");

/// Add a student if one is specified

    if (!empty($add)) {
        if (! enrol_student($add, $course->id)) {
            error("Could not add that student to this course!");
        }
    }

/// Remove a student if one is specified.

    if (!empty($remove)) {
        if (! unenrol_student($remove, $course->id)) {
            error("Could not add that student to this course!");
        }
    }

/// Print a help notice about the need to use this page

    if (empty($add) and empty($remove)) {
        $note = get_string("assignstudentsnote");
        if ($course->password) {
            $note .= "<p>".get_string("assignstudentspass", "", $course->password);
        }
        print_simple_box($note, "center", "50%");
    }

/// Get all existing students for this course.
    $students = get_course_students($course->id);

/// Print the lists of existing and potential students

    echo "<table cellpadding=2 cellspacing=10 align=center>";
    echo "<tr><th width=50%>$strexistingstudents</th><th width=50%>$strpotentialstudents</th></tr>";
    echo "<tr><td width=50% nowrap valign=top>";

/// First, show existing students for this course

    if (empty($students)) { 
        echo "<p align=center>$strnoexistingstudents</a>";
        $studentlist = "";

    } else {
        $studentarray = array();
        foreach ($students as $student) {
            $studentarray[] = $student->id;
            echo "<p align=right>$student->firstname $student->lastname, $student->email &nbsp;&nbsp; <a href=\"student.php?id=$course->id&remove=$student->id\" title=\"$strremovestudent\"><img src=\"../pix/t/right.gif\" border=0></a></p>";
        }
        $studentlist = implode(",",$studentarray);
        unset($studentarray);
    }

    echo "<td width=50% nowrap valign=top>";

/// Print list of potential students

    $usercount = get_users(false, $search, true, $studentlist);

    if ($usercount == 0) {
        echo "<p align=center>$strnopotentialstudents</p>";

    } else if ($usercount > MAX_USERS_PER_PAGE) {
        echo "<p align=center>$strtoomanytoshow</p>";

    } else {

        if ($search) {
            echo "<p align=center>($strsearchresults : $search)</p>";
        }

        if (!$users = get_users(true, $search, true, $studentlist)) {
            error("Could not get users!");
        }

        foreach ($users as $user) {
            echo "<p align=left><a href=\"{$_SERVER['PHP_SELF']}?id=$course->id&add=$user->id\"".
                   "title=\"$straddstudent\"><img src=\"../pix/t/left.gif\"".
                   "border=0></a>&nbsp;&nbsp;$user->firstname $user->lastname, $user->email";
        }
    }

    if ($search or $usercount > MAX_USERS_PER_PAGE) {
        echo "<form action={$_SERVER['PHP_SELF']} method=post>";
        echo "<input type=hidden name=id value=\"$course->id\">";
        echo "<input type=text name=search size=20>";
        echo "<input type=submit value=\"$searchstring\">";
        echo "</form>";
    }

    echo "</tr></table>";

    print_footer();

?>
