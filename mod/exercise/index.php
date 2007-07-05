<?php // $Id$

    require_once("../../config.php");
    require_once("lib.php");
    require_once("locallib.php");

    $id = required_param('id', PARAM_INT); // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);
    add_to_log($course->id, "exercise", "view all", "index.php?id=$course->id", "");

    $strexercises = get_string("modulenameplural", "exercise");
    $strexercise = get_string("modulename", "exercise");
    $strweek = get_string("week");
    $strtopic = get_string("topic");
    $strname = get_string("name");
    $strtitle = get_string("title", "exercise");
    $strphase = get_string("phase", "exercise");
    $strgrade = get_string("grade");
    $strdeadline = get_string("deadline", "exercise");
    $strsubmitted = get_string("submitted", "assignment");
    
    $navlinks = array();
    $navlinks[] = array('name' => $strexercises, 'link' => '', 'type' => 'activity');
    $navigation = build_navigation($navlinks);
    
    print_header_simple("$strexercises", "", $navigation, "", "", true, "", navmenu($course));

    if (! $exercises = get_all_instances_in_course("exercise", $course)) {
        notice("There are no exercises", "../../course/view.php?id=$course->id");
        die;
    }

    $timenow = time();

    if ($course->format == "weeks") {
        if (isteacher($course->id)) {
            $table->head  = array ($strweek, $strname, $strtitle, $strphase, $strsubmitted, $strdeadline);
        } else {
            $table->head  = array ($strweek, $strname, $strtitle, $strgrade, $strsubmitted, $strdeadline);
        }
        $table->align = array ("center", "left", "left","center","left", "left");
    } else if ($course->format == "topics") {
        if (isteacher($course->id)) {
            $table->head  = array ($strtopic, $strname, $strtitle, $strphase, $strsubmitted, $strdeadline);
        } else {
            $table->head  = array ($strtopic, $strname, $strtitle, $strgrade, $strsubmitted, $strdeadline);
        }
        $table->align = array ("center", "left", "left", "center", "left", "left");
    } else {
        $table->head  = array ($strname, $strsubmitted, $strdeadline);
        $table->align = array ("left", "left", "left");
    }

    foreach ($exercises as $exercise) {
        if ($exercise->deadline > $timenow) {
            $due = userdate($exercise->deadline);
        } else {
            $due = "<font color=\"red\">".userdate($exercise->deadline)."</font>";
        }
        if ($submissions = exercise_get_user_submissions($exercise, $USER)) {
            foreach ($submissions as $submission) {
                if ($submission->late) {
                    $submitted = "<font color=\"red\">".userdate($submission->timecreated)."</font>";
                    } 
                else {
                    $submitted = userdate($submission->timecreated);
                    }
                $link = "<a href=\"view.php?id=$exercise->coursemodule\">".format_string($exercise->name,true)."</a>";
                $title = $submission->title;
                if ($course->format == "weeks" or $course->format == "topics") {
                    if (isteacher($course->id)) {
                        $phase = '';
                        switch ($exercise->phase) {
                            case 0:
                            case 1: $phase = get_string("phase1short", "exercise");
                                    break;
                            case 2: $phase = get_string("phase2short", "exercise");
                                    if ($num = exercise_count_unassessed_student_submissions($exercise)) {
                                        $phase .= " [".get_string("unassessed", "exercise", $num)."]";
                                    }
                                    break;
                            case 3: $phase = get_string("phase3short", "exercise");
                                    if ($num = exercise_count_unassessed_student_submissions($exercise)) {
                                        $phase .= " [".get_string("unassessed", "exercise", $num)."]";
                                    }
                                    break;
                        }
                        $table->data[] = array ($exercise->section, $link, $title, $phase, 
                                $submitted, $due);
                    } else { // it's a student
                        if ($assessments = exercise_get_user_assessments($exercise, $USER)) { // should be only one...
                            foreach ($assessments as $studentassessment) {
                                break;
                            }
                            if ($studentassessment->timegraded) { // it's been assessed
                                if ($teacherassessment = exercise_get_submission_assessment($submission)) {
                                    $actualgrade = number_format(($studentassessment->gradinggrade * 
                                        $exercise->gradinggrade / 100.0) + ($teacherassessment->grade * 
                                        $exercise->grade / 100.0), 1);
                                    if ($submission->late) {
                                        $actualgrade = "<font color=\"red\">(".$actualgrade.")<font color=\"red\">";
                                    }
                                    $actualgrade .= " (".get_string("maximumshort").": ".
                                        number_format($exercise->gradinggrade + $exercise->grade, 0).")";
                                    $table->data[] = array ($exercise->section, $link, $title, $actualgrade, 
                                        $submitted, $due);
                                }
                            } else {
                                $table->data[] = array ($exercise->section, $link, $title, 
                                    "-", $submitted, $due);
                            }
                        }
                    } 
                } else {
                    $table->data[] = array ($link, $submitted, $due);
                }
            }
        } else {
            $submitted = get_string("no");
            $title = '';
            $link = "<a href=\"view.php?id=$exercise->coursemodule\">".format_string($exercise->name,true)."</a>";
            if ($course->format == "weeks" or $course->format == "topics") {
                if (isteacher($course->id)) {
                    $table->data[] = array ($exercise->section, $link, $title, $exercise->phase, 
                            $submitted, $due);
                } else {
                    $table->data[] = array ($exercise->section, $link, $title, "-", $submitted, $due);
                } 
            } else {
                $table->data[] = array ($link, $submitted, $due);
            }
        }
    }
    echo "<br />";

    print_table($table);

    print_footer($course);
?>
