<?PHP // $Id$

    require_once("../../config.php");
    require_once("lib.php");

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
    $strweek = get_string("week");
    $strtopic = get_string("topic");
    $strname = get_string("name");
    $strduedate = get_string("duedate", "assignment");
    $strsubmitted = get_string("submitted", "assignment");


    print_header("$course->shortname: $strassignments", "$course->fullname", "$navigation $strassignments", "", "", true, "", navmenu($course));

    if (! $assignments = get_all_instances_in_course("assignment", $course)) {
        notice("There are no assignments", "../../course/view.php?id=$course->id");
        die;
    }

    $timenow = time();

    if ($course->format == "weeks") {
        $table->head  = array ($strweek, $strname, $strduedate, $strsubmitted);
        $table->align = array ("center", "left", "left", "left");
    } else if ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname, $strduedate, $strsubmitted);
        $table->align = array ("center", "left", "left", "left");
    } else {
        $table->head  = array ($strname, $strduedate, $strsubmitted);
        $table->align = array ("left", "left", "left");
    }

    $currentgroup = get_current_group($course->id);
    if ($currentgroup and isteacheredit($course->id)) {
        $group = get_record("groups", "id", $currentgroup);
        $groupname = " ($group->name)";
    } else {
        $groupname = "";
    }

    $currentsection = "";

    foreach ($assignments as $assignment) {
        if (isteacher($course->id)) {
            if ($assignment->type == OFFLINE) {
                $submitted =  "<a href=\"submissions.php?id=$assignment->id\">" .
                                get_string("viewfeedback", "assignment") . "</a>";
            } else {
                $count = assignment_count_real_submissions($assignment, $currentgroup);
                $submitted = "<a href=\"submissions.php?id=$assignment->id\">" .
                             get_string("viewsubmissions", "assignment", $count) . "</a>$groupname";
            }
        } else {
            if ($submission = assignment_get_submission($assignment, $USER)) {
                if ($submission->timemodified <= $assignment->timedue) {
                    $submitted = userdate($submission->timemodified);
                } else {
                    $submitted = "<font color=red>".userdate($submission->timemodified)."</font>";
                }
            } else {
                $submitted = get_string("no");
            }
        }

        $due = userdate($assignment->timedue);
        if (!$assignment->visible) {
            //Show dimmed if the mod is hidden
            $link = "<a class=\"dimmed\" href=\"view.php?id=$assignment->coursemodule\">$assignment->name</a>";
        } else {
            //Show normal if the mod is visible
            $link = "<a href=\"view.php?id=$assignment->coursemodule\">$assignment->name</a>";
        }

        $printsection = "";
        if ($assignment->section !== $currentsection) {
            if ($assignment->section) {
                $printsection = $assignment->section;
            }
            if ($currentsection !== "") {
                $table->data[] = 'hr';
            }
            $currentsection = $assignment->section;
        }

        if ($course->format == "weeks" or $course->format == "topics") {
            $table->data[] = array ($printsection, $link, $due, $submitted);
        } else {
            $table->data[] = array ($link, $due, $submitted);
        }
    }

    echo "<br />";

    print_table($table);

    print_footer($course);
?>
