<?PHP  // $Id$

    require("../../config.php");
    require("lib.php");

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
                  "", "", true, update_module_icon($cm->id, $course->id));

    if (isteacher($course->id)) {
        if ($submissions = assignment_get_all_submissions($assignment)) {
            $count = count($submissions);
        } else {
            $count = 0;
        }
        echo "<P align=right><A HREF=\"submissions.php?id=$assignment->id\">".
              get_string("viewsubmissions", "assignment", $count)."</A></P>";
    }

    $strdifference = format_time($assignment->timedue - time());
    $strduedate = userdate($assignment->timedue)." ($strdifference)";

    print_simple_box_start("CENTER");
    print_heading(get_string("assignmentdetails", "assignment").":", "CENTER");
    print_simple_box_start("CENTER");
    echo "<B>".get_string("duedate", "assignment")."</B>: $strduedate<BR>";
    echo "<B>".get_string("maximumgrade")."</B>: $assignment->grade<BR>";
    print_simple_box_end();
    echo "<BR>";
    echo text_to_html($assignment->description);
    print_simple_box_end();
    echo "<BR>";

    if (!isteacher($course->id)) {
        if ($submission = assignment_get_submission($assignment, $USER)) {
            print_simple_box_start("center");
            echo "<CENTER>";
            print_heading(get_string("yoursubmission","assignment").":", "CENTER");
            echo "<P><FONT SIZE=-1><B>".get_string("lastmodified")."</B>: ".userdate($submission->timemodified)."</FONT></P>";
            assignment_print_user_files($assignment, $USER);
            print_simple_box_end();
        } else {
            print_heading(get_string("notsubmittedyet","assignment"));
        }
    
        echo "<HR SIZE=1 NOSHADE>";
    
        if ($submission->grade) {
            print_heading(get_string("submissionfeedback", "assignment").":", "CENTER");
            assignment_print_feedback($course, $submission);
        } else {
            if ($submission) {
                echo "<P ALIGN=CENTER>".get_string("overwritewarning", "assignment")."</P>";
            }
            assignment_print_upload_form($assignment);
        }
    }
    
    print_footer($course);

?>
