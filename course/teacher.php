<?PHP // $Id$

	require("../config.php");
	require("../user/lib.php");

    optional_variable($id);       // course id

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/admin/");
    }

    require_login();

    if (!isadmin()) {
        error("You must be an administrator to use this page.");
    }

    $strassignteachers = get_string("assignteachers");
    $stradministration = get_string("administration");

    if (!$id) {
	    print_header("$site->fullname: $strassignteachers", "$site->fullname", 
                     "<A HREF=\"$CFG->wwwroot/admin\">$stradministration</A> -> $strassignteachers");

        if ($courses = get_records_sql("SELECT * from course WHERE category > 0 ORDER BY fullname")) {

            print_heading("Choose a course to add teachers to");
            print_simple_box_start("CENTER");
            foreach ($courses as $course) {
                echo "<A HREF=\"teacher.php?id=$course->id\">$course->fullname</A><BR>";
            }
            print_simple_box_end();
        } else {
            print_heading(get_string("nocoursesyet"));
            print_continue("$CFG->wwwroot/admin/");
        }
        print_footer();
        exit;
    }

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect (can't find it)");
    }


	print_header("$site->fullname: $course->shortname: $strassignteachers", 
                 "$site->fullname", 
                 "<A HREF=\"$CFG->wwwroot/admin\">$stradministration</A> -> 
                  <A HREF=\"teacher.php\">$strassignteachers</A> ->
                  $course->shortname", "");
    print_heading($course->fullname);


/// Get all existing teachers for this course.
    $teachers = get_records_sql("SELECT u.*,t.authority,t.id as teachid FROM user u, user_teachers t 
                                 WHERE t.course = '$course->id' 
                                   AND t.user = u.id 
                                   ORDER BY t.authority ASC");

/// Add a teacher if one is specified

    if ($add) {
        if (! $user = get_record("user", "id", $add)) {
            error("That teacher (id = $add) doesn't exist", "teacher.php?id=$course->id");
        }

        if ($teachers) {
            foreach ($teachers as $tt) {
                if ($tt->id == $user->id) {
                    error("That user is already a teacher for this course.", "teacher.php?id=$course->id");
                }
            }
        }

        $teacher->user   = $user->id;
        $teacher->course = $course->id;
        if ($teachers) {
            $teacher->authority = 2;
        } else {
            $teacher->authority = 1;   // First teacher is the main teacher
        }
        $teacher->id = insert_record("user_teachers", $teacher);
        if (! $teacher->id) {
            error("Could not add that teacher to this course!");
        }
        $user->authority = $teacher->authority;
        $teachers[] = $user;
    }

/// Remove a teacher if one is specified.

    if ($remove) {
        if (! $user = get_record("user", "id", $remove)) {
            error("That teacher (id = $remove) doesn't exist", "teacher.php?id=$course->id");
        }
        if ($teachers) {
            foreach ($teachers as $tt) {
                if ($tt->id == $user->id) {
                    delete_records("user_teachers", "id", "$tt->teachid");
                }
            }
        }
        $teachers = get_records_sql("SELECT u.*,t.authority,t.id as teachid FROM user u, user_teachers t 
                                 WHERE t.course = '$course->id' 
                                   AND t.user = u.id 
                                   ORDER BY t.authority ASC");
    }


/// Print the lists of existing and potential teachers

    echo "<TABLE CELLPADDING=2 CELLSPACING=10 ALIGN=CENTER>";
    echo "<TR><TH WIDTH=50%>Existing Teachers</TH><TH WIDTH=50%>Potential Teachers</TH></TR>";
    echo "<TR><TD WIDTH=50% NOWRAP VALIGN=TOP>";

/// First, show existing teachers for this course

    if (! $teachers) { 
        echo "<P ALIGN=CENTER>No existing teachers</A>";

    } else {
        foreach ($teachers as $teacher) {
            echo "<P ALIGN=right>$teacher->firstname $teacher->lastname, $teacher->email &nbsp;&nbsp; <A HREF=\"teacher.php?id=$course->id&remove=$teacher->id\" TITLE=\"Remove teacher\"><IMG SRC=\"../pix/t/right.gif\" BORDER=0></A></P>";
        }
    }

    echo "<TD WIDTH=50% NOWRAP VALIGN=TOP>";

/// Print list of potential teachers

    if ($search) {
        $users = get_records_sql("SELECT * from user WHERE confirmed = 1 
                                  AND (firstname LIKE '%$search%' OR 
                                       lastname LIKE '%$search%' OR 
                                       email LIKE '%$search%')
                                  AND username <> 'guest'");
    } else {
        $users = get_records_sql("SELECT * from user WHERE confirmed = 1 
                                  AND username <> 'guest'");
    }

    
    if ($users) {
        foreach ($users as $user) {  // Remove users who are already teachers
            if ($teachers) {
                foreach ($teachers as $teacher) {
                    if ($teacher->id == $user->id) {
                        continue 2;
                    }
                }
            }
            $potential[] = $user;
        }
    }

    if (! $potential) { 
        echo "<P ALIGN=CENTER>No potential teachers</A>";
        if ($search) {
            echo "<FORM ACTION=teacher.php METHOD=GET>";
            echo "<INPUT TYPE=hidden NAME=id VALUE=\"$course->id\">";
            echo "<INPUT TYPE=text NAME=search SIZE=20>";
            echo "<INPUT TYPE=submit VALUE=\"Search again\">";
            echo "</FORM>";
        }

    } else {
        if ($search) {
            echo "<P ALIGN=CENTER>(Search results)</P>";
        }
        if (count($potential) <= 20) {
            foreach ($potential as $user) {
                echo "<P ALIGN=LEFT><A HREF=\"teacher.php?id=$course->id&add=$user->id\" TITLE=\"Add teacher\"><IMG SRC=\"../pix/t/left.gif\" BORDER=0></A>&nbsp;&nbsp;$user->firstname $user->lastname, $user->email";
            }
        } else {
            echo "<P ALIGN=CENTER>There are too many users to show.<BR>";
            echo "Enter a search word here.";
            echo "<FORM ACTION=teacher.php METHOD=GET>";
            echo "<INPUT TYPE=hidden NAME=id VALUE=\"$course->id\">";
            echo "<INPUT TYPE=text NAME=search SIZE=20>";
            echo "<INPUT TYPE=submit VALUE=Search>";
            echo "</FORM>";
        }
    }

    echo "</TR></TABLE>";

    print_footer();

?>
