<?PHP // $Id$
      // Admin-only script to assign teachers to courses

	require_once("../config.php");
	require_once("../user/lib.php");

    optional_variable($id);       // course id

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
    $strsearchagain   = get_string("searchagain");
    $strtoomanytoshow   = get_string("toomanytoshow");

    if (!$id) {
	    print_header("$site->shortname: $strassignteachers", "$site->fullname", 
                     "<A HREF=\"../$CFG->admin/index.php\">$stradministration</A> -> $strassignteachers");
        
        $isadmin = isadmin(); /// cache value
        $courses = get_courses();
        

		print_heading(get_string("choosecourse"));
		print_simple_box_start("CENTER");
        
		foreach ($courses as $course) {
		    if ($isadmin OR isteacher($course->id, $USER->id)){
			    echo "<A HREF=\"teacher.php?id=$course->id\">$course->fullname</A><BR>\n";
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
                 "<A HREF=\"../$CFG->admin/index.php\">$stradministration</A> -> 
                  <A HREF=\"teacher.php\">$strassignteachers</A> -> $course->shortname", "");
    print_heading("<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->fullname</A>");


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

    echo "<TABLE CELLPADDING=2 CELLSPACING=10 ALIGN=CENTER>";
    echo "<TR><TH WIDTH=50%>$strexistingteachers</TH><TH WIDTH=50%>$strpotentialteachers</TH></TR>";
    echo "<TR><TD WIDTH=50% NOWRAP VALIGN=TOP>";

/// First, show existing teachers for this course

    if (empty($teachers)) { 
        echo "<P ALIGN=CENTER>$strnoexistingteachers</A>";

    } else {
        foreach ($teachers as $teacher) {
            echo "<P ALIGN=right>$teacher->firstname $teacher->lastname, $teacher->email &nbsp;&nbsp; <A HREF=\"teacher.php?id=$course->id&remove=$teacher->id\" TITLE=\"$strremoveteacher\"><IMG SRC=\"../pix/t/right.gif\" BORDER=0></A></P>";
        }
    }

    echo "<TD WIDTH=50% NOWRAP VALIGN=TOP>";

/// Print list of potential teachers

    if (!empty($search)) {
        $users = get_users_search($search);

    } else {
        $users = get_users_confirmed();
    }

    
    if (!empty($users)) {
        foreach ($users as $user) {  // Remove users who are already teachers
            if (!empty($teachers)) {
                foreach ($teachers as $teacher) {
                    if ($teacher->id == $user->id) {
                        continue 2;
                    }
                }
            }
            $potential[] = $user;
        }
    }

    if (empty($potential)) { 
        echo "<P ALIGN=CENTER>$strnopotentialteachers</A>";
        if ($search) {
            echo "<FORM ACTION=teacher.php METHOD=GET>";
            echo "<INPUT TYPE=hidden NAME=id VALUE=\"$course->id\">";
            echo "<INPUT TYPE=text NAME=search SIZE=20>";
            echo "<INPUT TYPE=submit VALUE=\"$strsearchagain\">";
            echo "</FORM>";
        }

    } else {
        if (!empty($search)) {
            echo "<P ALIGN=CENTER>($strsearchresults)</P>";
        }
        if (count($potential) <= 20) {
            foreach ($potential as $user) {
                echo "<P ALIGN=LEFT><A HREF=\"teacher.php?id=$course->id&add=$user->id\" TITLE=\"$straddteacher\"><IMG SRC=\"../pix/t/left.gif\" BORDER=0></A>&nbsp;&nbsp;$user->firstname $user->lastname, $user->email";
            }
        } else {
            echo "<P ALIGN=CENTER>There are too many users to show.<BR>";
            echo "Enter a search word here.";
            echo "<FORM ACTION=teacher.php METHOD=GET>";
            echo "<INPUT TYPE=hidden NAME=id VALUE=\"$course->id\">";
            echo "<INPUT TYPE=text NAME=search SIZE=20>";
            echo "<INPUT TYPE=submit VALUE=\"$strsearch\">";
            echo "</FORM>";
        }
    }

    echo "</TR></TABLE>";

    print_footer();

?>
