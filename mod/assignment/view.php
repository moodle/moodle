<?PHP  // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    optional_variable($id);    // Course Module ID
    optional_variable($a);    // Assignment ID

    if ($id) {
        if (! $cm = get_record("course_modules", "id", $id)) {
            error("Course Module ID was incorrect");
        }

        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }
    
        if (! $assignment = get_record("assignment", "id", $cm->instance)) {
            error("Course module is incorrect");
        }

    } else {
        if (! $assignment = get_record("assignment", "id", $a)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $assignment->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("assignment", $assignment->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    }

    require_login($course->id);

    add_to_log($course->id, "assignment", "view", "view.php?id=$cm->id", "$assignment->id");

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strassignments = get_string("modulenameplural", "assignment");
    $strassignment  = get_string("modulename", "assignment");

    print_header("$course->shortname: $assignment->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strassignments</A> -> $assignment->name", 
                  "", "", true, update_module_button($cm->id, $course->id, $strassignment), navmenu($course, $cm));

    if (isteacher($course->id)) {
        if ($assignment->type == OFFLINE) {
            echo "<P align=right><A HREF=\"submissions.php?id=$assignment->id\">".
                  get_string("viewfeedback", "assignment")."</A></P>";
        } else {
            $count = count_records_select("assignment_submissions", 
                                          "assignment = '$assignment->id' AND timemodified > 0");
            echo "<P align=right><A HREF=\"submissions.php?id=$assignment->id\">".
                  get_string("viewsubmissions", "assignment", $count)."</A></P>";
        }
    } else if (!$cm->visible) {
        notice(get_string("activityiscurrentlyhidden"));
    }


    $strdifference = format_time($assignment->timedue - time());
    if (($assignment->timedue - time()) < 0) {
        $strdifference = "<FONT COLOR=RED>$strdifference</FONT>";
    }
    $strduedate = userdate($assignment->timedue)." ($strdifference)";

    print_simple_box_start("CENTER");
    print_heading($assignment->name, "CENTER");
    print_simple_box_start("CENTER");
    echo "<b>".get_string("duedate", "assignment")."</b>: $strduedate<br />";

    if ($assignment->grade < 0) {
        $scaleid = - ($assignment->grade);
        if ($scale = get_record("scale", "id", $scaleid)) {
            $scalegrades = make_menu_from_list($scale->scale);
            echo "<b>".get_string("grade")."</b>: $scale->name ";
            print_scale_menu_helpbutton($course->id, $scale);
            echo "<br />";
        }
    } else if ($assignment->grade < 0) {
        echo "<b>".get_string("maximumgrade")."</b>: $assignment->grade<br>";
    }

    print_simple_box_end();
    echo "<br />";
    echo format_text($assignment->description, $assignment->format);
    print_simple_box_end();
    echo "<br />";

    if (!isteacher($course->id) and !isguest()) {
        $submission = assignment_get_submission($assignment, $USER);

        if ($assignment->type == OFFLINE) {
            if ($submission->timemarked) {
                assignment_print_feedback($course, $submission);
            }
        } else {
            if ($submission and $submission->timemodified) {
                print_simple_box_start("center");
                echo "<center>";
                print_heading(get_string("yoursubmission","assignment").":", "center");
                echo "<p><font size=-1><b>".get_string("lastmodified")."</b>: ".userdate($submission->timemodified)."</font></p>";
                assignment_print_user_files($assignment, $USER);
                print_simple_box_end();
            } else {
                print_heading(get_string("notsubmittedyet","assignment"));
            }
        
            echo "<hr size=1 noshade>";
        
            if ($submission and $submission->timemarked) {
                print_heading(get_string("submissionfeedback", "assignment").":", "center");
                if (isset($scalegrades)) {
                    $submission->grade = $scalegrades[$submission->grade];
                }
                assignment_print_feedback($course, $submission);
            }
            if (!$submission->timemarked or $assignment->resubmit) {
                if ($submission and $submission->timemodified) {
                    echo "<P ALIGN=CENTER>".get_string("overwritewarning", "assignment")."</P>";
                }
                print_heading(get_string("submitassignment", "assignment").":", "center");
                assignment_print_upload_form($assignment);
            }
        }
    }
    
    print_footer($course);

?>
