<?PHP // $Id$

//  Allows a student to "unenrol" from a class
//  This will not delete any of their data from the course, 
//  but will remove them from the student list and prevent 
//  any course email being sent to them.

    require("../config.php");
    require("lib.php");

    require_variable($id);    //course

    if (! $course = get_record("course", "id", $id) ) {
        error("That's an invalid course id");
    }

    require_login($course->id);

    if (isset($confirm)) {
        if (! unenrol_student_in_course($USER->id, $course->id)) {
            error("An error occurred while trying to unenrol you.");
        }
        add_to_log($course->id, "course", "unenrol", "view.php?id=$course->id", "$USER->id");

        unset($USER->student["$id"]);
        
        redirect("$CFG->wwwroot");
    }


    print_header("Unenrol from $course->shortname", "$course->shortname", "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> -> Unenrol"); 

    notice_yesno ("Are you sure you want to remove yourself from this course?", 
                  "unenrol.php?id=$id&confirm=yes", 
                  "$HTTP_REFERER");

    print_footer();


?>
