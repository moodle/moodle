<?PHP // $Id$

    require("../../config.php");
    require("lib.php");

    require_variable($id);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);
    add_to_log($course->id, "assignment", "view all", "index.php?id=$course->id", "");

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strassignments = get_string("modulenameplural", "assignment");
    $strassignment = get_string("modulename", "assignment");

    print_header("$course->shortname: $strassignments", "$course->fullname", "$navigation $strassignments", "");

    if (! $assignments = get_all_instances_in_course("assignment", $course->id, "cw.section ASC")) {
        notice("There are no assignments", "../../course/view.php?id=$course->id");
        die;
    }

    $timenow = time();

    if ($course->format == "weeks") {
        $table->head  = array ("Week", "Name", "Due", "Submitted");
        $table->align = array ("CENTER", "LEFT", "LEFT", "LEFT");
    } else if ($course->format == "topics") {
        $table->head  = array ("Topic", "Name", "Due", "Submitted");
        $table->align = array ("CENTER", "LEFT", "LEFT", "LEFT");
    } else {
        $table->head  = array ("Name", "Due", "Submitted");
        $table->align = array ("LEFT", "LEFT", "LEFT");
    }

    foreach ($assignments as $assignment) {
        if ($submission = assignment_get_submission($assignment->id, $USER->id)) {
            if ($submission->timemodified <= $assignment->timedue) {
                $submitted = userdate($submission->timemodified);
            } else {
                $submitted = "<FONT COLOR=red>".userdate($submission->timemodified)."</FONT>";
            }
        } else {
            $submitted = get_string("no");
        }
        $due = userdate($assignment->timedue);
        $link = "<A HREF=\"view.php?id=$assignment->coursemodule\">$assignment->name</A>";

        if ($course->format == "weeks" or $course->format == "topics") {
            $table->data[] = array ($assignment->section, $link, $due, $submitted);
        } else {
            $table->data[] = array ($link, $due, $submitted);
        }
    }

    echo "<BR>";

    print_table($table);

    print_footer($course);
?>
