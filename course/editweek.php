<?PHP // $Id$

    require("../config.php");

    require_variable($id);    // Week ID

    if (! $week = get_record("course_weeks", "id", $id)) {
        error("Course week is incorrect");
    }

    if (! $course = get_record("course", "id", $week->course)) {
        error("Could not find the course!");
    }

    require_login($course->id);

    add_to_log("Edit week", $course->id);
    
    if (!isteacher($course->id)) {
        error("Only teachers can edit this!");
    }


/// If data submitted, then process and store.

    if (match_referer() && isset($HTTP_POST_VARS)) {

        $timenow = time();

        if (! set_field("course_weeks", "summary", $summary, "id", $week->id)) {
            error("Could not update the summary!");
        }
        
        redirect("view.php?id=$course->id");
        exit;
    }

/// Otherwise fill and print the form.

    if (! $form ) {
        $form = $week;
    }

    print_header("Edit week $week->week", "Edit week $week->week", "", "form.summary");

    include("editweek.html");

    print_footer($course);

?>
