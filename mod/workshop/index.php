<?php // $Id$

    require("../../config.php");
    require("lib.php");
    require("locallib.php");

    require_variable($id);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);
    add_to_log($course->id, "workshop", "view all", "index.php?id=$course->id", "");

    $strworkshops = get_string("modulenameplural", "workshop");
    $strworkshop = get_string("modulename", "workshop");
    $strweek = get_string("week");
    $strtopic = get_string("topic");
    $strname = get_string("name");
    if (isstudent($course->id)) {
        $strinfo = get_string("grade");
    } else {
        $strinfo = get_string("phase", "workshop");
    }
    $strdeadline = get_string("deadline", "workshop");
    $strsubmitted = get_string("submitted", "assignment");

    print_header_simple("$strworkshops", "", "$strworkshops", "", "", true, "", navmenu($course));

    if (! $workshops = get_all_instances_in_course("workshop", $course)) {
        notice("There are no workshops", "../../course/view.php?id=$course->id");
        die;
    }

    $timenow = time();

    if ($course->format == "weeks") {
        $table->head  = array ($strweek, $strname, $strinfo, $strsubmitted, $strdeadline);
        $table->align = array ("CENTER", "LEFT", "LEFT", "LEFT", "LEFT");
    } elseif ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname, $strinfo, $strsubmitted, $strdeadline);
        $table->align = array ("CENTER", "LEFT", "left", "LEFT", "LEFT");
    } else {
        $table->head  = array ($strname, $strinfo, $strsubmitted, $strdeadline);
        $table->align = array ("LEFT", "LEFT", "LEFT", "LEFT");
    }

    foreach ($workshops as $workshop) {
        switch ($workshop->phase) {
            case 0:
            case 1: $info = get_string("phase1short", "workshop");
                    break;
            case 2: $info = get_string("phase2short", "workshop");
                    break;
            case 3: $info = get_string("phase3short", "workshop");
                    break;
            case 4: $info = get_string("phase4short", "workshop");
                    break;
            case 5: $info = get_string("phase5short", "workshop");
                    break;
        }
        if ($submissions = workshop_get_user_submissions($workshop, $USER)) {
            foreach ($submissions as $submission) {
                if ($submission->timecreated <= $workshop->deadline) {
                    $submitted = userdate($submission->timecreated);
                } 
                else {
                    $submitted = "<font color=\"red\">".userdate($submission->timecreated)."</font>";
                }
                $due = userdate($workshop->deadline);
                if (!$workshop->visible) {
                    //Show dimmed if the mod is hidden
                    $link = "<a class=\"dimmed\" href=\"view.php?id=$workshop->coursemodule\">$workshop->name</a><br />";
                } else {
                    //Show normal if the mod is visible
                    $link = "<a href=\"view.php?id=$workshop->coursemodule\">$workshop->name</a><br />";
                }
                if (isstudent($course->id)) {
                    $link .= " ($submission->title)"; // show students the title of their submission(s)
                    $gradinggrade = workshop_gradinggrade($workshop, $USER);
                    $grade = workshop_submission_grade($workshop, $submission);
                    $info = get_string("gradeforassessments", "workshop").": $gradinggrade/$workshop->gradinggrade; ".
                       get_string("gradeforsubmission", "workshop").": $grade/$workshop->grade"; 
                }
                if ($course->format == "weeks" or $course->format == "topics") {
                    $table->data[] = array ($workshop->section, $link, $info, $submitted, $due);
                } 
                else {
                    $table->data[] = array ($link, $info, $submitted, $due);
                }
                if (isteacher($course->id)) {
                    // teacher only needs to see one "submission"
                    break;
                }
            }
        }
        else {
            $submitted = get_string("no");
            $due = userdate($workshop->deadline);
            if (!$workshop->visible) {
                //Show dimmed if the mod is hidden
                $link = "<a class=\"dimmed\" href=\"view.php?id=$workshop->coursemodule\">$workshop->name</a>";
            } else {
                //Show normal if the mod is visible
                $link = "<a href=\"view.php?id=$workshop->coursemodule\">$workshop->name</a>";
            }
            if ($course->format == "weeks" or $course->format == "topics") {
                    $table->data[] = array ($workshop->section, $link, $info, $submitted, $due);
            } 
            else {
                $table->data[] = array ($link, $info, $submitted, $due);
            }
        }
    }
    echo "<br />";

    print_table($table);

    print_footer($course);
?>
