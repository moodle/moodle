<?PHP // $Id$

	require("../config.php");

    optional_variable($id);       // course id
    optional_variable($delete);   // delete confirmation

    require_login();

    if (!isadmin()) {
        error("You must be an administrator to use this page.");
    }

    if (!$id) {
	    print_header("Delete a course", "Delete a course", 
                     "<A HREF=\"$CFG->wwwroot/admin\">Admin</A> -> Delete a course");
        if ($courses = get_records_sql("SELECT * from course WHERE category > 0 ORDER BY fullname")) {
            print_heading("Choose a course to delete");
            print_simple_box_start("CENTER");
            foreach ($courses as $course) {
                echo "<A HREF=\"delete.php?id=$course->id\">$course->fullname</A><BR>";
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

    if (! $delete) {
	    print_header("Delete $course->shortname ?", "Delete $course->shortname ?", 
                     "<A HREF=\"$CFG->wwwroot/admin\">Admin</A> -> Delete $course->shortname ?");
        notice_yesno("Are you absolutely sure you want to completely delete this course and all the data it contains?<BR><BR>$course->fullname", "delete.php?id=$course->id&delete=".md5($course->timemodified), "delete.php");
        exit;
    }

    if ($delete != md5($course->timemodified)) {
        error("The check variable was wrong - try again");
    }

    // OK checks done, delete the course now.
	print_header("Deleting $course->shortname", "Deleting $course->shortname", 
                 "<A HREF=\"$CFG->wwwroot/admin\">Admin</A> -> Deleting $course->shortname");
    print_heading("Deleting $course->fullname");

    // First delete every instance of every module

    if ($allmods = get_records_sql("SELECT * FROM modules") ) {
        foreach ($allmods as $mod) {
            $modname = $mod->name;
            $modfile = "../mod/$modname/lib.php";
            $moddelete = $modname."_delete_instance";
            $count=0;
            if (file_exists($modfile)) {
                include_once($modfile);
                if (function_exists($moddelete)) {
                    if ($instances = get_records($modname, "course", $course->id)) {
                        foreach ($instances as $instance) {
                            if ($moddelete($instance->id)) {
                                $count++;
                            } else {
                                notify("Could not delete $modname instance $instance->id ($instance->name)");
                            }
                        }
                    }
                } else {
                    notify("Function $moddelete() doesn't exist!");
                }

            }
            notify("Deleted $count instances of $modname");
        } 
    } else {
        error("No modules are installed!");
    } 

    // Delete any user stuff

    if (delete_records("user_students", "course", $course->id)) {
        notify("Deleted student enrolments");
    }

    if (delete_records("user_teachers", "course", $course->id)) {
        notify("Deleted teachers");
    }

    // Delete logs

    if (delete_records("log", "course", $course->id)) {
        notify("Deleted logs");
    }

    // Delete any course stuff

    if (delete_records("course_sections", "course", $course->id)) {
        notify("Deleted course sections");
    }
    if (delete_records("course_modules", "course", $course->id)) {
        notify("Deleted course modules");
    }
    if (delete_records("course", "id", $course->id)) {
        notify("Deleted the main course record");
    }

    print_heading("$course->shortname has been completely deleted");

    print_continue("delete.php");

    print_footer();

?>
