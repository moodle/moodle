<?PHP // $Id$

    require("../config.php");

    require_variable($id);    // Course ID

    if (! $course = get_record("course", "id", $id)) {
        error("Could not find the course!");
    }

    require_login($course->id);

    if (!isteacher($course->id)) {
        error("Only teachers can send mail this way!");
    }


/// If data submitted, then process and store.

    if (match_referer() && isset($HTTP_POST_VARS)) {

        $link = "$CFG->wwwroot/course/view.php?id=$course->id";

        //XXXX The following function is now wrong - needs fixing
        //if (! email_to_course($USER, $course, true, $subject, $message, "$link")) {
        //    error("An error occurred while trying to send mail!");
        //}

        add_to_log($course->id, "course", "email", "email.php?id=$course->id", "");
        
        redirect("view.php?id=$course->id", "Email sent", 1);
        exit;
    }


    $form->id = $course->id;

    print_header("$course->shortname: Mail", "$course->fullname", 
                 "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> -> Send mail");
    
    print_heading("Send an email to all participants");
        
    include("email.html");

    print_footer($course);


?>
