<?PHP // $Id$
      // Asks for a course pass key, once only, and enrols that user

    require_once("../config.php");
    require_once("lib.php");

    require_variable($id);

    require_login();

    $strloginto = get_string("loginto", "", $course->shortname);
    $strcourses = get_string("courses");

    if (! $course = get_record("course", "id", $id) ) {
        error("That's an invalid course id");
    }

    if (! $site = get_site()) {
        error("Could not find a site!");
    }

    check_for_restricted_user($USER->username);

/// Check the submitted enrollment key if there is one

    if ($form = data_submitted()) {

        if ($form->password == $course->password) {

            if (isguest()) {
                add_to_log($course->id, "course", "guest", "view.php?id=$course->id", $_SERVER['REMOTE_ADDR']);

            } else if (!record_exists("user_students", "userid", $USER->id, "course", $course->id)) {

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

            $USER->student[$course->id] = true;
            
            if ($SESSION->wantsurl) {
                $destination = $SESSION->wantsurl;
                unset($SESSION->wantsurl);
            } else {
                $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
            }

    	    redirect($destination);
    
        } else {
            $errormsg = get_string("enrolmentkeyhint", "", substr($course->password,0,1));
        }
    }


/// Double check just in case they are actually enrolled already 
/// This might occur if they were manually enrolled during this session
    
    if (record_exists("user_students", "userid", $USER->id, "course", $course->id)) {
        $USER->student[$course->id] = true;

        if ($SESSION->wantsurl) {
            $destination = $SESSION->wantsurl;
            unset($SESSION->wantsurl);
        } else {
            $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
        }
   
        redirect($destination);
    }


/// Automatically enrol into courses without password

    if ($course->password == "") {   // no password, so enrol

        if (isguest()) {
            add_to_log($course->id, "course", "guest", "view.php?id=$course->id", "$USER->id");

        } else if (empty($confirm)) {

            print_header($strloginto, $course->fullname, "<a href=\".\">$strcourses</a> -> $strloginto"); 
            echo "<br />";
            notice_yesno(get_string("enrolmentconfirmation"), "enrol.php?id=$course->id&confirm=1", $CFG->wwwroot);
            print_footer();
            exit;

        } else {

            if (! enrol_student($USER->id, $course->id)) {
                error("An error occurred while trying to enrol you.");
            }
            add_to_log($course->id, "course", "enrol", "view.php?id=$course->id", "$USER->id");

            $USER->student[$course->id] = true;
        
            if ($SESSION->wantsurl) {
                $destination = $SESSION->wantsurl;
                unset($SESSION->wantsurl);
            } else {
                $destination = "$CFG->wwwroot/course/view.php?id=$course->id";
            }
    
            redirect($destination);
        }
    }

    $teacher = get_teacher($course->id);
    if (!isset($password)) {
        $password = "";
    }


    print_header($strloginto, $course->fullname, "<A HREF=\".\">$strcourses</A> -> $strloginto", "form.password"); 

    print_course($course); 

    include("enrol.html");

    print_footer();


?>
