<?PHP // $Id$

//  Removes a student from a class
//  This will not delete any of their data from the course, 
//  but will remove them from the student list and prevent 
//  any course email being sent to them.

    require("../config.php");
    require("lib.php");

    require_variable($id);               //course
    optional_variable($user, $USER->id); //user

    if (! $course = get_record("course", "id", $id) ) {
        error("That's an invalid course id");
    }
    if (! $user = get_record("user", "id", $user) ) {
        error("That's an invalid user id");
    }

    require_login($course->id);

    if ($user->id != $USER->id and !isteacher($course->id)) {
        error("You must be a teacher to do this");
    }

    if (isset($confirm)) {

        if (! unenrol_student_in_course($user->id, $course->id)) {
            error("An error occurred while trying to unenrol you.");
        }

        // remove some other things
        delete_records("forum_subscriptions", "user", $user->id);

        add_to_log($course->id, "course", "unenrol", "view.php?id=$course->id", "$user->id");

        if ($user->id == $USER->id) {
            unset($USER->student["$id"]);
            redirect("$CFG->wwwroot");
        }
        
        redirect("$CFG->wwwroot/user/index.php?id=$course->id");
    }


    print_header("Unenrol from $course->shortname", "$course->shortname", "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> -> Unenrol"); 

    if ($user->id == $USER->id) {
        notice_yesno ("Are you sure you want to remove yourself from this course?", 
                      "unenrol.php?id=$id&user=$user->id&confirm=yes", "$HTTP_REFERER");
    } else {
        notice_yesno ("Are you sure you want to remove $user->firstname $user->lastname from this course?", 
                      "unenrol.php?id=$id&user=$user->id&confirm=yes", "$HTTP_REFERER");
    }

    print_footer();


?>
