<?PHP // $Id$

//  Asks for a course pass key, once only

    require("../config.php");
    require("lib.php");

    require_login();
    require_variable($id);


    if (match_referer() && isset($HTTP_POST_VARS)) {    // form submitted

        $actual_password = get_field("course", "password", "id", $id);

        if ($password == $actual_password) {

            enrol_student_in_course($USER->id, $id);
            add_to_log("Enrolled in course", $id);

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
                        "<BR>(Here's a hint - it starts with \"".substr($actual_password,0,1)."\")";
        }
    }

    if (! $course = get_record("course", "id", $id) ) {
        error("That's an invalid course id");
    }

    if (! $site = get_record("course", "category", "0") ) {
        error("Could not find a site!");
    }

    if ($course->password == "") {   // no password, so enrol
        if (! enrol_student_in_course($USER->id, $course->id)) {
            error("An error occurred while trying to enrol you.");
        }

        add_to_log("Enrolled in course", $id);

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

    include("login.html");

    print_footer();


//// FUNCTIONS /////////////////////////////////////////////

function enrol_student_in_course($user, $course) {
    
    global $db;

	$timenow = time();

	$rs = $db->Execute("INSERT INTO user_students (user, course, start, end, time) 
                        VALUES ($user, $course, 0, 0, $timenow)");
	if ($rs) {
		return true;
	} else {
	    return false;
	}
}

?>
