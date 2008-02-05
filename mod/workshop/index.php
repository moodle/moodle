<?php // $Id$

    require("../../config.php");
    require("lib.php");
    require("locallib.php");

    $id = required_param('id',PARAM_INT);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_course_login($course);

    add_to_log($course->id, "workshop", "view all", "index.php?id=$course->id", "");

    $strworkshops = get_string("modulenameplural", "workshop");
    $strworkshop = get_string("modulename", "workshop");
    $strweek = get_string("week");
    $strtopic = get_string("topic");
    $strname = get_string("name");
    $strinfo = get_string("grade")."/".$strinfo = get_string("phase", "workshop");
    $strdeadline = get_string("deadline", "workshop");
    $strsubmitted = get_string("submitted", "assignment");

    $navlinks = array();
    $navlinks[] = array('name' => $strworkshops, 'link' => '', 'type' => 'activity');
    $navigation = build_navigation($navlinks);

    print_header_simple("$strworkshops", "", $navigation, "", "", true, "", navmenu($course));

    if (! $workshops = get_all_instances_in_course("workshop", $course)) {
        notice(get_string('thereareno', 'moodle', $strworkshops), "../../course/view.php?id=$course->id");
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
        if (workshop_is_teacher($workshop, $USER->id)) { // teacher see info (students see grade)
            $info = workshop_phase($workshop, 'short');
            if (time() > $workshop->submissionstart) {
                if ($num = workshop_count_student_submissions_for_assessment($workshop, $USER)) {
                    $info .= " [".get_string("unassessed", "workshop", $num)."]";
                }
            }
        }

        $due = userdate($workshop->submissionend);

        if ($submissions = workshop_get_user_submissions($workshop, $USER)) {
            foreach ($submissions as $submission) {
                if ($submission->timecreated <= $workshop->submissionend) {
                    $submitted = userdate($submission->timecreated);
                }
                else {
                    $submitted = "<span class=\"redfont\">".userdate($submission->timecreated)."</span>";
                }
                if (!$workshop->visible) {
                    //Show dimmed if the mod is hidden
                    $link = "<a class=\"dimmed\" href=\"view.php?id=$workshop->coursemodule\">".format_string($workshop->name,true)."</a><br />";
                } else {
                    //Show normal if the mod is visible
                    $link = "<a href=\"view.php?id=$workshop->coursemodule\">".format_string($workshop->name,true)."</a><br />";
                }
                if (workshop_is_student($workshop)) {
                    $link .= " ($submission->title)"; // show students the title of their submission(s)
                    $gradinggrade = workshop_gradinggrade($workshop, $USER);
                    $grade = workshop_submission_grade($workshop, $submission);
                    if ($workshop->wtype) {
                        if (workshop_count_assessments($submission)) {
                            $info = get_string("gradeforassessments", "workshop").
                                ": $gradinggrade/$workshop->gradinggrade; ".get_string("gradeforsubmission",
                                "workshop").": $grade/$workshop->grade";
                        } else {
                            $info = get_string("gradeforassessments", "workshop").
                                ": $gradinggrade/$workshop->gradinggrade; ".get_string("gradeforsubmission",
                                "workshop").": ".get_string("noassessments", "workshop");
                        }
                     } else { // simple assignemnt, don't show grading grade
                        $info = get_string("gradeforsubmission", "workshop").": $grade/$workshop->grade";
                    }
                    if ($workshop->releasegrades > $timenow) {
                        $info = get_string("notavailable", "workshop");
                    }
                }
                if ($course->format == "weeks" or $course->format == "topics") {
                    $table->data[] = array ($workshop->section, $link, $info, $submitted, $due);
                }
                else {
                    $table->data[] = array ($link, $info, $submitted, $due);
                }
                if (workshop_is_teacher($workshop)) {
                    // teacher only needs to see one "submission"
                    break;
                }
            }
        }
        else { // no submission
            $submitted = get_string("no");
            if (!$workshop->visible) {
                //Show dimmed if the mod is hidden
                $link = "<a class=\"dimmed\" href=\"view.php?id=$workshop->coursemodule\">".format_string($workshop->name,true)."</a>";
            } else {
                //Show normal if the mod is visible
                $link = "<a href=\"view.php?id=$workshop->coursemodule\">".format_string($workshop->name,true)."</a>";
            }
            if (workshop_is_student($workshop)) {
                $info = '0';
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
