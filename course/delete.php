<?PHP // $Id$
      // Admin-only code to delete a course utterly

	require_once("../config.php");

    optional_variable($id);       // course id
    optional_variable($delete);   // delete confirmation

    require_login();

    if (!isadmin()) {
        error("You must be an administrator to use this page.");
    }

    if (!$site = get_site()) {
        error("Site not found!");
    }

    $strdeletecourse = get_string("deletecourse");
    $stradministration = get_string("administration");

    if (!$id) {
	    print_header("$site->shortname: $strdeletecourse", $site->fullname, 
                     "<A HREF=\"../$CFG->admin/index.php\">$stradministration</A> -> $strdeletecourse");

        if ($courses = get_courses()) {
            print_heading(get_string("choosecourse"));
            print_simple_box_start("CENTER");
            foreach ($courses as $course) {
                echo "<A HREF=\"delete.php?id=$course->id\">$course->fullname ($course->shortname)</A><BR>";
            }
            print_simple_box_end();
        } else {
            print_heading(get_string("nocoursesyet"));
            print_continue("../$CFG->admin/index.php");
        }
        print_footer();
        exit;
    }

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID was incorrect (can't find it)");
    }

    if (! $delete) {
        $strdeletecheck = get_string("deletecheck", "", $course->shortname);
        $strdeletecoursecheck = get_string("deletecoursecheck");
	    print_header("$site->shortname: $strdeletecheck", $site->fullname, 
                     "<A HREF=\"../$CFG->admin/index.php\">$stradministration</A> -> 
                      <A HREF=\"delete.php\">$strdeletecourse</A> -> $strdeletecheck");

        notice_yesno("$strdeletecoursecheck<BR><BR>$course->fullname ($course->shortname)", 
                     "delete.php?id=$course->id&delete=".md5($course->timemodified), 
                     "delete.php");
        exit;
    }

    if ($delete != md5($course->timemodified)) {
        error("The check variable was wrong - try again");
    }

    // OK checks done, delete the course now.
    $strdeletingcourse = get_string("deletingcourse", "", $course->shortname);

	print_header("$site->shortname: $strdeletingcourse", $site->fullname, 
                 "<A HREF=\"../$CFG->admin/index.php\">$stradministration</A> -> 
                  <A HREF=\"delete.php\">$strdeletecourse</A> -> $strdeletingcourse");

    print_heading($strdeletingcourse);

    $strdeleted = get_string("deleted");
    // First delete every instance of every module

    if ($allmods = get_records("modules") ) {
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
            notify("$strdeleted $count instances of $modname");
        } 
    } else {
        error("No modules are installed!");
    } 

    // Delete any user stuff

    if (delete_records("user_students", "course", $course->id)) {
        notify("$strdeleted student enrolments");
    }

    if (delete_records("user_teachers", "course", $course->id)) {
        notify("$strdeleted teachers");
    }

    // Delete logs

    if (delete_records("log", "course", $course->id)) {
        notify("$strdeleted logs");
    }

    // Delete any course stuff

    if (delete_records("course_sections", "course", $course->id)) {
        notify("$strdeleted course sections");
    }
    if (delete_records("course_modules", "course", $course->id)) {
        notify("$strdeleted course modules");
    }
    if (delete_records("course", "id", $course->id)) {
        notify("$strdeleted the main course record");
    }

    print_heading( get_string("deletedcourse", "", $course->shortname) );

    print_continue("delete.php");

    print_footer();

?>
