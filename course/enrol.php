<?PHP // $Id$
      // Asks for a course pass key, once only, and enrols that user

    require("../config.php");
    require("lib.php");

    require_login();
    require_variable($id);

    if (! $course = get_record("course", "id", $id) ) {
        error("That's an invalid course id");
    }

    if ($form = data_submitted()) {

        if ($form->password == $course->password) {

            if (isguest()) {
                add_to_log($course->id, "course", "guest", "view.php?id=$course->id", "$REMOTE_ADDR, $REMOTE_HOST");

            } else {
                if (! enrol_student($USER->id, $course->id)) {
                    error("An error occurred while trying to enrol you.");
                }

                $subject = get_string("welcometocourse", "", $course->fullname);

                $a->coursename = $course->fullname;
                $a->profileurl = "$CFG->wwwroot/user/view.php?id=$USER->id&course=$course->id";
                $message = get_string("welcometocoursetext", "", $a);

                if (! $teacher = get_teacher($course->id)) {
                    $teacher = get_admin();
                }
                email_to_user($USER, $teacher, $subject, $message);

                add_to_log($course->id, "course", "enrol", "view.php?id=$course->id", "$USER->id");
            }

            $USER->student["$id"] = true;
            save_session("USER");
            
            if ($SESSION->wantsurl) {
                $destination = $SESSION->wantsurl;
                unset($SESSION->wantsurl);
                save_session("SESSION");
            } else {
                $destination = "$CFG->wwwroot/course/view.php?id=$id";
            }

    	    redirect($destination);
    
        } else {
            $errormsg = get_string("enrolmentkeyhint", "", substr($course->password,0,1));
        }
    }


    if (! $site = get_site()) {
        error("Could not find a site!");
    }

    if ($course->password == "") {   // no password, so enrol
        
        if (isguest()) {
            add_to_log($course->id, "course", "guest", "view.php?id=$course->id", "$USER->id");
        } else {
            if (! enrol_student($USER->id, $course->id)) {
                error("An error occurred while trying to enrol you.");
            }
            add_to_log($course->id, "course", "enrol", "view.php?id=$course->id", "$USER->id");
        }

        $USER->student["$id"] = true;
        save_session("USER");
        
        if ($SESSION->wantsurl) {
            $destination = $SESSION->wantsurl;
            unset($SESSION->wantsurl);
            save_session("SESSION");
        } else {
            $destination = "$CFG->wwwroot/course/view.php?id=$id";
        }

        redirect($destination);
    }

    $teacher = get_teacher($course->id);

    $strloginto = get_string("loginto", "", $course->shortname);
    $strcourses = get_string("courses");

    print_header($strloginto, $strloginto, "<A HREF=\".\">$strcourses</A> -> $strloginto", "form.password"); 

    print_course($course); 

    include("enrol.html");

    print_footer();


?>
