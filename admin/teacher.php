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

    $strassignteachers = get_string("assignteachers");
    $stradministration = get_string("administration");
    $strexistingteachers   = get_string("existingteachers");
    $strnoexistingteachers = get_string("noexistingteachers");
    $strpotentialteachers  = get_string("potentialteachers");
    $strnopotentialteachers  = get_string("nopotentialteachers");
    $straddteacher    = get_string("addteacher");
    $strremoveteacher = get_string("removeteacher");
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
	    print_header("$site->shortname: $strassignteachers", "$site->fullname", 
                     "<a href=\"index.php\">$stradministration</a> -> $strassignteachers");
        
        $isadmin = isadmin(); /// cache value
        $courses = get_courses();

		print_heading(get_string("choosecourse"));
		print_simple_box_start("center");
        
		foreach ($courses as $course) {
		    if ($isadmin or isteacher($course->id, $USER->id)){
			    echo "<a href=\"teacher.php?id=$course->id\">$course->fullname ($course->shortname)</a><br>\n";
				$coursesfound = TRUE;
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


	print_header("$site->shortname: $course->shortname: $strassignteachers", 
                 "$site->fullname", 
                 "<a href=\"index.php\">$stradministration</a> -> 
                  <a href=\"teacher.php\">$strassignteachers</a> -> $course->shortname", "");

    print_heading("<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->fullname ($course->shortname)</a>");


/// Get all existing teachers for this course.
    $teachers = get_course_teachers($course->id);

/// Add a teacher if one is specified

    if (!empty($add)) {
	    if (!isteacher($course->id)){
		    error("You must be an administrator or teacher to modify this course.");
        }

        if (! $user = get_record("user", "id", $add)) {
            error("That teacher (id = $add) doesn't exist", "teacher.php?id=$course->id");
        }

        if (!empty($teachers)) {
            foreach ($teachers as $tt) {
                if ($tt->id == $user->id) {
                    error("That user is already a teacher for this course.", "teacher.php?id=$course->id");
                }
            }
        }

        $teacher->userid   = $user->id;
        $teacher->course = $course->id;
        if (!empty($teachers)) {
            $teacher->authority = 2;
        } else {
            $teacher->authority = 1;   // First teacher is the main teacher
        }
        $teacher->id = insert_record("user_teachers", $teacher);
        if (empty($teacher->id)) {
            error("Could not add that teacher to this course!");
        }
        $teachers[] = $user;
    }

/// Remove a teacher if one is specified.

    if (!empty($remove)) {

        if (!isteacher($course->id)){
        	error("You must be an administrator or teacher to modify this course.");
		}
        if (! $user = get_record("user", "id", $remove)) {
            error("That teacher (id = $remove) doesn't exist", "teacher.php?id=$course->id");
        }
        if (!empty($teachers)) {
            foreach ($teachers as $key => $tt) {
                if ($tt->id == $user->id) {
                    remove_teacher($user->id, $course->id);
                    unset($teachers[$key]);
                }
            }
        }
    }


/// Print the lists of existing and potential teachers

    echo "<table cellpadding=2 cellspacing=10 align=center>";
    echo "<tr><th width=50%>$strexistingteachers</th><th width=50%>$strpotentialteachers</th></tr>";
    echo "<tr><td width=50% nowrap valign=top>";

/// First, show existing teachers for this course

    if (empty($teachers)) { 
        echo "<p align=center>$strnoexistingteachers</a>";
        $teacherlist = "";

    } else {
        $teacherarray = array();
        foreach ($teachers as $teacher) {
            $teacherarray[] = $teacher->id;
            echo "<p align=right>$teacher->firstname $teacher->lastname, $teacher->email &nbsp;&nbsp; <a href=\"teacher.php?id=$course->id&remove=$teacher->id\" title=\"$strremoveteacher\"><img src=\"../pix/t/right.gif\" border=0></a></p>";
        }
        $teacherlist = implode(",",$teacherarray);
        unset($teacherarray);
    }

    echo "<td width=50% nowrap valign=top>";

/// Print list of potential teachers

    $usercount = get_users(false, $search, true, $teacherlist);

    if ($usercount == 0) {
        echo "<p align=center>$strnopotentialteachers</p>";

    } else if ($usercount > MAX_USERS_PER_PAGE) {
        echo "<p align=center>$strtoomanytoshow</p>";

    } else {

        if ($search) {
            echo "<p align=center>($strsearchresults : $search)</p>";
        }

        if (!$users = get_users(true, $search, true, $teacherlist)) {
            error("Could not get users!");
        }

        foreach ($users as $user) {
            echo "<p align=left><a href=\"{$_SERVER['PHP_SELF']}?id=$course->id&add=$user->id\"".
                   "title=\"$straddteacher\"><img src=\"../pix/t/left.gif\"".
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
