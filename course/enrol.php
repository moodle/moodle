<?PHP // $Id$

//  Asks for a course pass key, once only

    require("../config.php");
    require("lib.php");

    require_login();
    require_variable($id);

    if (! $course = get_record("course", "id", $id) ) {
        error("That's an invalid course id");
    }

    if (match_referer() && isset($HTTP_POST_VARS)) {    // form submitted

        if ($password == $course->password) {

            if (isguest()) {
                add_to_log($course->id, "course", "guest", "view.php?id=$course->id", "$USER->id");
            } else {
                if (! enrol_student_in_course($USER->id, $course->id)) {
                    error("An error occurred while trying to enrol you.");
                }
                add_to_log($course->id, "course", "enrol", "view.php?id=$course->id", "$USER->id");
            }

            $USER->student["$id"] = true;
            
            if ($SESSION->wantsurl) {
                $destination = $SESSION->wantsurl;
                unset($SESSION->wantsurl);
            } else {
                $destination = "$CFG->wwwroot/course/view.php?id=$id";
            }

    	    redirect($destination);
    
        } else {
            $errormsg = "That entry key was incorrect, please try again".
                        "<BR>(Here's a hint - it starts with \"".substr($course->password,0,1)."\")";
        }
    }


    if (! $site = get_record("course", "category", "0") ) {
        error("Could not find a site!");
    }

    if ($course->password == "") {   // no password, so enrol
        
        if (isguest()) {
            add_to_log($course->id, "course", "guest", "view.php?id=$course->id", "$USER->id");
        } else {
            if (! enrol_student_in_course($USER->id, $course->id)) {
                error("An error occurred while trying to enrol you.");
            }
            add_to_log($course->id, "course", "enrol", "view.php?id=$course->id", "$USER->id");
        }

        $USER->student["$id"] = true;
        
        if ($SESSION->wantsurl) {
            $destination = $SESSION->wantsurl;
            unset($SESSION->wantsurl);
        } else {
            $destination = "$CFG->wwwroot/course/view.php?id=$id";
        }

        redirect($destination);
    }

    $teacher = get_teacher($course->id);

    print_header("Login to $course->shortname", "Login to $course->shortname", "<A HREF=\".\">Courses</A> -> Login to $course->shortname", "form.password"); 

    print_course($course); 

    include("enrol.html");

    print_footer();


?>
