<?PHP // $Id$
      // Admin-only script to assign teachers to courses

	require_once("../config.php");

    define("MAX_USERS_PER_PAGE", 30);

    optional_variable($id);         // course id
    optional_variable($add, "");
    optional_variable($remove, "");
    optional_variable($search, ""); // search string

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }

    require_login();

    if (!iscreator()) {
        error("You must be an administrator or course creator to use this page.");
    }

    $stradministration = get_string("administration");
    $strassignstudents = "Assign students";
    $strexistingstudents   = "Existing students";
    $strnoexistingstudents = "No existing students";
    $strpotentialstudents  = "Potential students";
    $strnopotentialstudents  = "No potential students";
    $straddstudent    = "Add student";
    $strremovestudent = "Remove student";
    $strsearch        = get_string("search");
    $strsearchresults  = get_string("searchresults");
    $strsearchagain   = get_string("searchagain");
    $strtoomanytoshow   = get_string("toomanytoshow");

    if ($search) {
        $searchstring = $strsearchagain;
    } else {
        $searchstring = $strsearch;
    }


    if (!$id) {
	    print_header("$site->shortname: $strassignstudents", "$site->fullname", 
                     "<a href=\"index.php\">$stradministration</a> -> $strassignstudents");
        
        $isadmin = isadmin(); /// cache value
        $courses = get_courses();

		print_heading(get_string("choosecourse"));
		print_simple_box_start("center");
        
        if (!empty($courses)) {
		    foreach ($courses as $course) {
		        if ($isadmin or isteacher($course->id, $USER->id)){
			        echo "<a href=\"students.php?id=$course->id\">$course->fullname ($course->shortname)</a><br>\n";
				    $coursesfound = TRUE;
			    }
		    }	
        }
		
        print_simple_box_end();
        
        if ($coursesfound == FALSE) {         
            print_heading(get_string("nocoursesyet"));
            print_continue("../$CFG->admin/index.php");
        }

        print_footer();
        exit;
    }

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect (can't find it)");
    }


	print_header("$site->shortname: $course->shortname: $strassignstudents", 
                 "$site->fullname", 
                 "<a href=\"index.php\">$stradministration</a> -> 
                  <a href=\"students.php\">$strassignstudents</a> -> $course->shortname", "");

    print_heading("<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->fullname ($course->shortname)</a>");


/// Get all existing students for this course.
    $students = get_course_students($course->id);

/// Add a student if one is specified

    if (!empty($add)) {
	    if (!isteacher($course->id)){
		    error("You must be an administrator or teacher to modify this course.");
        }

        if (! $user = get_record("user", "id", $add)) {
            error("That student (id = $add) doesn't exist", "students.php?id=$course->id");
        }

        if (!empty($students)) {
            foreach ($students as $ss) {
                if ($ss->id == $user->id) {
                    error("That user is already a student for this course.", "students.php?id=$course->id");
                }
            }
        }

        $student->userid   = $user->id;
        $student->course = $course->id;
        $student->id = insert_record("user_students", $student);
        if (empty($student->id)) {
            error("Could not add that student to this course!");
        }
        $students[] = $user;
    }

/// Remove a student if one is specified.

    if (!empty($remove)) {

        if (!isteacher($course->id)){
        	error("You must be an administrator or teacher to modify this course.");
		}
        if (! $user = get_record("user", "id", $remove)) {
            error("That student (id = $remove) doesn't exist", "students.php?id=$course->id");
        }
        if (!empty($students)) {
            foreach ($students as $key => $ss) {
                if ($ss->id == $user->id) {
                    unenrol_student($user->id, $course->id);
                    unset($students[$key]);
                }
            }
        }
    }


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
            echo "<p align=right>$student->firstname $student->lastname, $student->email &nbsp;&nbsp; <a href=\"students.php?id=$course->id&remove=$student->id\" title=\"$strremovestudent\"><img src=\"../pix/t/right.gif\" border=0></a></p>";
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
