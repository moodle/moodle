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

    if (!$id) {
        $courses = get_records_sql("SELECT * from course WHERE category > 0 ORDER BY fullname");

	    print_header("Add teachers to a course", "Add teachers to a course", "<A HREF=\"$CFG->wwwroot/admin\">Admin</A> -> Add teachers", "");
        print_heading("Choose a course to add teachers to");
        print_simple_box_start("CENTER");
        foreach ($courses as $course) {
            echo "<A HREF=\"teacher.php?id=$course->id\">$course->fullname</A><BR>";
        }
        print_simple_box_end();
        print_footer();
        exit;
    }

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect (can't find it)");
    }


	print_header("Add teachers to $course->shortname", "Add teachers to a course", "<A HREF=\"$CFG->wwwroot/admin\">Admin</A> -> Add teachers to $course->shortname", "");


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

        foreach ($teachers as $tt) {
            if ($tt->id == $user->id) {
                error("That user is already a teacher for this course.", "teacher.php?id=$course->id");
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
        foreach ($teachers as $tt) {
            if ($tt->id == $user->id) {
                delete_records("user_teachers", "id", "$tt->teachid");
            }
        }
        $teachers = get_records_sql("SELECT u.*,t.authority,t.id as teachid FROM user u, user_teachers t 
                                 WHERE t.course = '$course->id' 
                                   AND t.user = u.id 
                                   ORDER BY t.authority ASC");
    }


/// Show existing teachers for this course

    if ($teachers) {
        print_simple_box_start("center", "", "$THEME->cellheading");
        print_heading("Existing teachers");
        foreach ($teachers as $teacher) {
            echo "<LI>$teacher->firstname $teacher->lastname, $teacher->email &nbsp;&nbsp; <A HREF=\"teacher.php?id=$course->id&remove=$teacher->id\">remove</A>";
        }
        print_simple_box_end();
    }

/// Print list of potential teachers

    echo "<BR>";
    print_simple_box_start("center", "", "$THEME->cellcontent");
    print_heading("Potential teachers");

    if ($search) {
        $users = get_records_sql("SELECT * from user WHERE confirmed = 1 
                                  AND (firstname LIKE '%$search%' OR 
                                       lastname LIKE '%$search%' OR 
                                       email LIKE '%$search%')");
    } else {
        $users = get_records("user", "confirmed", "1");
    }

    
    foreach ($users as $user) {  // Remove users who are already teachers
        foreach ($teachers as $teacher) {
            if ($teacher->id == $user->id) {
                continue 2;
            }
        }
        $potential[] = $user;
    }

    if (! $potential) { 
        echo "No potential teachers";

    } else {
        if (count($potential) <= 20) {
            foreach ($potential as $user) {
                echo "<LI>$user->firstname $user->lastname, $user->email &nbsp;&nbsp; <A HREF=\"teacher.php?id=$course->id&add=$user->id\">add</A>";
            }
        } else {
            echo "There are too many users to show.<BR>Enter a search word here.";
            echo "<FORM ACTION=teacher.php METHOD=GET>";
            echo "<INPUT TYPE=hidden NAME=id VALUE=\"$course->id\">";
            echo "<INPUT TYPE=text NAME=search SIZE=20>";
            echo "<INPUT TYPE=submit VALUE=Search>";
            echo "</FORM>";
        }
    }

    print_simple_box_end();

    print_footer();

?>
