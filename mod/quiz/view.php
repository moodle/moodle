<?php  // $Id$

// This page prints a particular instance of quiz

    require_once("../../config.php");
    require_once("locallib.php");
    require_once($CFG->dirroot.'/lib/blocklib.php');

    optional_variable($id);    // Course Module ID, or
    optional_variable($q);     // quiz ID
    $edit = optional_param('edit', '');

    if ($id) {
        if (! $cm = get_record("course_modules", "id", $id)) {
            error("Course Module ID was incorrect");
        }
    
        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }
    
        if (! $quiz = get_record("quiz", "id", $cm->instance)) {
            error("Course module is incorrect");
        }

    } else {
        if (! $quiz = get_record("quiz", "id", $q)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $quiz->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("quiz", $quiz->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    }

    require_login($course->id);
    
    // if no questions have been set up yet redirect to edit.php
    if (!$quiz->questions and isteacheredit($course->id)) {
        redirect('edit.php?quizid='.$quiz->id);
    }

    add_to_log($course->id, "quiz", "view", "view.php?id=$cm->id", $quiz->id, $cm->id);

    $timenow = time();

// Initialize $PAGE, compute blocks

    $PAGE = page_create_instance($quiz->id);
    $pageblocks = blocks_get_by_page($PAGE);

    if (!empty($blockaction)) {
        blocks_execute_url_action($PAGE, $pageblocks);
        $pageblocks = blocks_get_by_page($PAGE);
    }
    
    $blocks_preferred_width = bounded_number(180, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]), 210);

// Print the page header

    if (!empty($edit) && $PAGE->user_allowed_editing()) {
        if ($edit == 'on') {
            $USER->editing = true;
        } else if ($edit == 'off') {
            $USER->editing = false;
        }
    }

    $strquizzes = get_string("modulenameplural", "quiz");
    $strquiz  = get_string("modulename", "quiz");
    $stredit = get_string('editquestions', 'quiz');
    if ($PAGE->user_allowed_editing()) {
        $buttons = "<table><tr><td><form target=\"$CFG->framename\" method=\"get\" action=\"edit.php\">".
               "<input type=\"hidden\" name=\"quizid\" value=\"$quiz->id\" />".
               "<input type=\"submit\" value=\"$stredit\" /></form></td><td>".
               update_module_button($cm->id, $course->id, $strquiz).
               '</td>'.
               '<td><form target="'.$CFG->framename.'" method="get" action="view.php">'.
               '<input type="hidden" name="id" value="'.$cm->id.'" />'.
               '<input type="hidden" name="edit" value="'.($PAGE->user_is_editing()?'off':'on').'" />'.
               '<input type="submit" value="'.get_string($PAGE->user_is_editing()?'turneditingoff':'blocksaddedit').'" /></form></td></tr></table>';
    } else {
        $buttons = '';
    }

    print_header_simple("$quiz->name", "",
                 "<a href=\"index.php?id=$course->id\">$strquizzes</a> -> $quiz->name", 
                 "", "", true, $buttons, navmenu($course, $cm));

    echo '<table border="0" cellpadding="3" cellspacing="0" width="100%" id=\"layout-table\">';
    echo '<tr valign="top">';

    if(blocks_have_content($pageblocks[BLOCK_POS_LEFT]) || $PAGE->user_is_editing()) {
        echo '<td style="vertical-align: top; width: '.$blocks_preferred_width.'px;" id="left-column">';
        blocks_print_group($PAGE, $pageblocks[BLOCK_POS_LEFT]);
        if ($PAGE->user_is_editing()) {
            blocks_print_adminblock($PAGE, $pageblocks);
        }
        echo '</td>';
    }

    echo '<td valign="top" width="*" id="middle-column">';

    if (isteacher($course->id)) {
        $attemptcount = count_records_select("quiz_attempts", "quiz = '$quiz->id' AND timefinish > 0");
        $usercount = count_records("quiz_grades", "quiz", "$quiz->id");
        $strusers  = get_string("users");
        $strviewallanswers  = get_string("viewallanswers","quiz",$attemptcount);
        echo "<p align=\"right\"><a href=\"report.php?id=$cm->id\">$strviewallanswers ($usercount $strusers)</a></p>";
    } else if (!$cm->visible) {
        notice(get_string("activityiscurrentlyhidden"));
    }

    $available = ($quiz->timeopen < $timenow and $timenow < $quiz->timeclose) || isteacher($course->id);

// Print the main part of the page

    print_heading($quiz->name);

    if (trim(strip_tags($quiz->intro))) {
        print_simple_box(format_text($quiz->intro), "center");
    }


    if (isguest()) {
        print_heading(get_string("guestsno", "quiz"));
        print_footer($course);
        exit;
    }

    if ($attempts = quiz_get_user_attempts($quiz->id, $USER->id)) {
        $numattempts = count($attempts);
    } else {
        $numattempts = 0;
    }

    if ($quiz->attempts > 1) {
        echo "<p align=\"center\">".get_string("attemptsallowed", "quiz").": $quiz->attempts</p>";
        echo "<p align=\"center\">".get_string("grademethod", "quiz").": ".$QUIZ_GRADE_METHOD[$quiz->grademethod]."</p>";
    } else {
        echo "<br />";
    }

    $strattempt       = get_string("attempt", "quiz");
    $strtimetaken     = get_string("timetaken", "quiz");
    $strtimecompleted = get_string("timecompleted", "quiz");
    $strgrade         = get_string("grade");
    $strbestgrade     = $QUIZ_GRADE_METHOD[$quiz->grademethod];

    $mygrade = quiz_get_best_grade($quiz->id, $USER->id);

    if ($numattempts) { 
        if ($quiz->grade) {
            $table->head = array($strattempt, $strtimetaken, $strtimecompleted, "$strgrade / $quiz->grade");
            $table->align = array("center", "center", "left", "right");
            $table->size = array("", "", "", "");
        } else {  // No grades are being used
            $table->head = array($strattempt, $strtimetaken, $strtimecompleted);
            $table->align = array("center", "center", "left");
            $table->size = array("", "", "");
        }
        foreach ($attempts as $attempt) {
            if ($timetaken = ($attempt->timefinish - $attempt->timestart)) {
                $timetaken = format_time($timetaken);
            } else {
                $timetaken = "-";
            }
            if ($quiz->grade and $quiz->sumgrades) {
                $attemptgrade = format_float(($attempt->sumgrades/$quiz->sumgrades)*$quiz->grade);
                if ($attemptgrade == $mygrade) {
                    $attemptgrade = "<span class=\"highlight\">$attemptgrade</span>";
                }
                if (quiz_review_allowed($quiz)) {
                    $attemptgrade = "<a href=\"review.php?q=$quiz->id&amp;attempt=$attempt->id\">$attemptgrade</a>";
                    $attempt->attempt = "<a href=\"review.php?q=$quiz->id&amp;attempt=$attempt->id\">#$attempt->attempt</a>";
                }
                $table->data[] = array( $attempt->attempt, 
                                        format_time($attempt->timefinish - $attempt->timestart),
                                        userdate($attempt->timefinish), 
                                        $attemptgrade);
            } else {  // No grades are being used
                if (quiz_review_allowed($quiz)) {
                    $attempt->attempt = "<a href=\"review.php?q=$quiz->id&amp;attempt=$attempt->id\">#$attempt->attempt</a>";
                }
                $table->data[] = array( $attempt->attempt, 
                                        format_time($attempt->timefinish - $attempt->timestart),
                                        userdate($attempt->timefinish));
            }
        }
        print_table($table);
    }

    if ($available) {
        if ($quiz->timelimit) {
            echo "<p align=\"center\">".get_string("quiztimelimit","quiz", format_time($quiz->timelimit * 60))."</p>";
        }
        echo "<p align=\"center\">".get_string("quizavailable", "quiz", userdate($quiz->timeclose));
    } else if ($timenow < $quiz->timeopen) {
        echo "<p align=\"center\">".get_string("quiznotavailable", "quiz", userdate($quiz->timeopen));
    } else {
        echo "<p align=\"center\">".get_string("quizclosed", "quiz", userdate($quiz->timeclose));
    }


    if (!$quiz->questions) {
        print_heading(get_string("noquestions", "quiz"));
    } else {
        if ($numattempts < $quiz->attempts or !$quiz->attempts) { 
            if ($available) {
                $options["id"] = $cm->id;
                if ($numattempts and $quiz->grade) {
                    print_heading("$strbestgrade: $mygrade / $quiz->grade.");
                }
                $strconfirmstartattempt = addslashes(get_string("confirmstartattempt","quiz"));
                echo "<br />";
                echo "</p>";
                echo "<div align=\"center\">";

                include("view_js.php");

                echo "</div>\n";
            }
        } else {
            print_heading(get_string("nomoreattempts", "quiz"));
            if ($quiz->grade) {
                print_heading(get_string("yourfinalgradeis", "quiz", "$mygrade / $quiz->grade"));
            }
        }
    }

// Finish the page
    echo '</td></tr></table>';

    print_footer($course);
    
    function quiz_review_allowed($quiz) {
        if (!$quiz->review) {
            return false;
        }
        if ((time() < $quiz->timeclose) and ($quiz->review == QUIZ_REVIEW_AFTER)) {
            return false;
        }
        if ((time() > $quiz->timeclose) and ($quiz->review == QUIZ_REVIEW_BEFORE)) {
            return false;
        }
        return true;
    }

?>
