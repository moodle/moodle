<?php  // $Id$

// This page prints a particular instance of quiz

    require_once("../../config.php");
    require_once("locallib.php");
    require_once($CFG->libdir.'/blocklib.php');
    require_once('pagelib.php');

    $id          = optional_param('id', 0, PARAM_INT); // Course Module ID, or
    $q           = optional_param('q',  0, PARAM_INT);  // quiz ID
    $edit        = optional_param('edit', '');

    if ($id) {
        if (! $cm = get_record("course_modules", "id", $id)) {
            error("There is no coursemodule with id $id");
        }

        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }

        if (! $quiz = get_record("quiz", "id", $cm->instance)) {
            error("The quiz with id $cm->instance corresponding to this coursemodule $id is missing");
        }

    } else {
        if (! $quiz = get_record("quiz", "id", $q)) {
            error("There is no quiz with id $q");
        }
        if (! $course = get_record("course", "id", $quiz->course)) {
            error("The course with id $quiz->course that the quiz with id $q belongs to is missing");
        }
        if (! $cm = get_coursemodule_from_instance("quiz", $quiz->id, $course->id)) {
            error("The course module for the quiz with id $q is missing");
        }
    }

    require_login($course->id, false, $cm);
    $isteacher = isteacher($course->id);

    // if no questions have been set up yet redirect to edit.php
    if (!$quiz->questions and isteacheredit($course->id)) {
        redirect('edit.php?quizid='.$quiz->id);
    }

    add_to_log($course->id, "quiz", "view", "view.php?id=$cm->id", $quiz->id, $cm->id);

    $timenow = time();

// Initialize $PAGE, compute blocks

    $PAGE       = page_create_instance($quiz->id);
    $pageblocks = blocks_setup($PAGE);
    $blocks_preferred_width = bounded_number(180, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]), 210);

// Print the page header

    if (!empty($edit) && $PAGE->user_allowed_editing()) {
        if ($edit == 'on') {
            $USER->editing = true;
        } else if ($edit == 'off') {
            $USER->editing = false;
        }
    }

    //only check pop ups if the user is not a teacher, and popup is set
    
    $bodytags = (isteacher($course->id) or !$quiz->popup)?'':'onload="popupchecker(\'This section of the test is in secure mode, this means that you need to take the quiz in a secure window. Please turn off your popup blocker. Thank you.\');"';
    $PAGE->print_header($course->shortname.': %fullname%','',$bodytags);

    echo '<table id="layout-table"><tr>';

    if(!empty($CFG->showblocksonmodpages) && (blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $PAGE->user_is_editing())) {
        echo '<td style="width: '.$blocks_preferred_width.'px;" id="left-column">';
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
        echo '</td>';
    }

    echo '<td id="middle-column">';

    $available = ($quiz->timeopen < $timenow and ($timenow < $quiz->timeclose or !$quiz->timeclose)) || $isteacher;

// Print the main part of the page

    // Print heading and tabs for teacher
    if ($isteacher) {
        $currenttab = 'info';
        include('tabs.php');
    }
    print_heading(format_string($quiz->name));

    if (trim(strip_tags($quiz->intro))) {
        print_simple_box(format_text($quiz->intro), "center");
    }

    if ($quiz->attempts > 1) {
        echo "<p align=\"center\">".get_string("attemptsallowed", "quiz").": $quiz->attempts</p>";
        echo "<p align=\"center\">".get_string("grademethod", "quiz").": ".$QUIZ_GRADE_METHOD[$quiz->grademethod]."</p>";
    } else {
        echo "<br />";
    }
    if ($available) {
        if ($quiz->timelimit) {
            echo "<p align=\"center\">".get_string("quiztimelimit","quiz", format_time($quiz->timelimit * 60))."</p>";
        }
        quiz_view_dates($quiz);
    } else if ($timenow < $quiz->timeopen) {
        echo "<p align=\"center\">".get_string("quiznotavailable", "quiz", userdate($quiz->timeopen));
    } else {
        echo "<p align=\"center\">".get_string("quizclosed", "quiz", userdate($quiz->timeclose));
    }


    // This is all the teacher will get
    if ($isteacher) {
        
        if ($attemptcount = count_records('quiz_attempts', 'quiz', $quiz->id, 'preview', 0)) {

            $strviewallanswers  = get_string("viewallanswers", "quiz", $attemptcount);
            $usercount = count_records_select('quiz_attempts', "quiz = '$quiz->id' AND preview = '0'", 'COUNT(DISTINCT userid)');
            $strusers  = $course->students;
    
            notify("<a href=\"report.php?mode=overview&amp;id=$cm->id\">$strviewallanswers ($usercount $strusers)</a>");
        }
        echo '</td></tr></table>';
        print_footer($course);
        exit;
    }

    if (isguest()) {

        $wwwroot = $CFG->wwwroot.'/login/index.php';
        if (!empty($CFG->loginhttps)) {
            $wwwroot = str_replace('http','https', $wwwroot);
        }

        notice_yesno(get_string('guestsno', 'quiz').'<br /><br />'.get_string('liketologin'),
                     $wwwroot, $_SERVER['HTTP_REFERER']);
        print_footer($course);
        echo '</td></tr></table>';
        exit;
    }

    if ($attempts = quiz_get_user_attempts($quiz->id, $USER->id)) {
        $numattempts = count($attempts);
    } else {
        $numattempts = 0;
    }

    $unfinished = false;
    if ($unfinishedattempt =  quiz_get_user_attempt_unfinished($quiz->id, $USER->id)) {
        $attempts[] = $unfinishedattempt;
        $unfinished = true;
    }

    $strattempt       = get_string("attempt", "quiz");
    $strtimetaken     = get_string("timetaken", "quiz");
    $strtimecompleted = get_string("timecompleted", "quiz");
    $strgrade         = get_string("grade");
    $strmarks          = get_string('marks', 'quiz');
    $strbestgrade     = $QUIZ_GRADE_METHOD[$quiz->grademethod];

    $windowoptions = "left=0, top=0, channelmode=yes, fullscreen=yes, scrollbars=yes, resizeable=no, directories=no, toolbar=no, titlebar=no, location=no, status=no, menubar=no";

    $mygrade = quiz_get_best_grade($quiz, $USER->id);

/// Now print table with existing attempts

    if ($numattempts) {
    /// prepare table header
        if ($quiz->grade and $quiz->sumgrades) { // Grades used so have more columns in table
            if ($quiz->grade <> $quiz->sumgrades) {
                $table->head = array($strattempt, $strtimetaken, $strtimecompleted, "$strmarks / $quiz->sumgrades", "$strgrade / $quiz->grade");
                $table->align = array("center", "center", "left", "right", "right");
                $table->size = array("", "", "", "", "");
            } else {
                $table->head = array($strattempt, $strtimetaken, $strtimecompleted, "$strgrade / $quiz->grade");
                $table->align = array("center", "center", "left", "right");
                $table->size = array("", "", "", "");
            }

        } else {  // No grades are being used
            $table->head = array($strattempt, $strtimetaken, $strtimecompleted);
            $table->align = array("center", "center", "left");
            $table->size = array("", "", "");
        }

    /// One row for each attempt
        foreach ($attempts as $attempt) {

        /// prepare strings for time taken and date completed
            $timetaken = '';
            $datecompleted = '';
            if ($attempt->timefinish > 0) { // attempt has finished
                $timetaken = format_time($attempt->timefinish - $attempt->timestart);
                $datecompleted = userdate($attempt->timefinish);
            } else if ($available) { // The student can continue this attempt, so put appropriate link
                $timetaken = format_time(time() - $attempt->timestart);
                $strconfirmstartattempt = addslashes(get_string("confirmstartattempt","quiz"));
                $datecompleted  = "\n".'<script language="javascript" type="text/javascript">';
                $datecompleted .= "\n<!--\n"; // -->
                if (!empty($quiz->popup)) {
                    $datecompleted .= "var windowoptions = 'left=0, top=0, height='+window.screen.height+
                     ', width='+window.screen.width+', channelmode=yes, fullscreen=yes, scrollbars=yes, '+
                     'resizeable=no, directories=no, toolbar=no, titlebar=no, location=no, status=no, '+
                     'menubar=no';\n";
                    $jslink  = 'javascript:';
                    if ($quiz->timelimit) {
                        $jslink .=  "if (confirm('$strconfirmstartattempt')) ";
                    }
                    $jslink .= "var popup = window.open(\\'attempt.php?id=$cm->id\\', \\'quizpopup\\', windowoptions);";
                } else {
                    $jslink = "attempt.php?id=$cm->id";
                }

                $linktext = get_string('continueattemptquiz', 'quiz');
                $datecompleted .= "document.write('<a href=\"$jslink\" alt=\"$linktext\">$linktext</a>');";
                $datecompleted .= "\n-->\n";
                $datecompleted .= '</script>';
                $datecompleted .= '<noscript>';
                $datecompleted .= '<strong>'.get_string('noscript', 'quiz').'</strong>';
                $datecompleted .= '</noscript>';
            } else { // attempt was not completed but is also not available any more.
                $timetaken = format_time($quiz->timeclose - $attempt->timestart);
                $datecompleted = $quiz->timeclose ? userdate($quiz->timeclose) : '';
            }

        /// prepare strings for attempt number, mark and grade
            if ($quiz->grade and $quiz->sumgrades) {
                $attemptmark  = round($attempt->sumgrades,$quiz->decimalpoints);
                $attemptgrade = round(($attempt->sumgrades/$quiz->sumgrades)*$quiz->grade,$quiz->decimalpoints);

                // highlight the highest grade if appropriate
                if ($attemptgrade == $mygrade and ($quiz->grademethod == QUIZ_GRADEHIGHEST)) {
                    $attemptgrade = "<span class=\"highlight\">$attemptgrade</span>";
                }

                // if attempt is closed and review is allowed then make attemptnumber and
                // mark and grade into links to review page
                if (quiz_review_allowed($quiz) and $attempt->timefinish > 0) {
                    if ($quiz->popup) { // need to link to popup window
                        $attemptmark = link_to_popup_window ("/mod/quiz/review.php?q=$quiz->id&amp;attempt=$attempt->id", 'quizpopup', round($attempt->sumgrades,$quiz->decimalpoints), '+window.screen.height+', '+window.screen.width+', '', $windowoptions, true);
                        $attemptgrade = link_to_popup_window ("/mod/quiz/review.php?q=$quiz->id&amp;attempt=$attempt->id", 'quizpopup', $attemptgrade, '+window.screen.height+', '+window.screen.width+', '', $windowoptions, true);
                        $attempt->attempt = link_to_popup_window ("/mod/quiz/review.php?q=$quiz->id&amp;attempt=$attempt->id", 'quizpopup', "#$attempt->attempt", '+window.screen.height+', '+window.screen.width+', '', $windowoptions, true);
                    } else {
                        $attemptmark = "<a href=\"review.php?q=$quiz->id&amp;attempt=$attempt->id\">".round($attempt->sumgrades,$quiz->decimalpoints).'</a>';
                        $attemptgrade = "<a href=\"review.php?q=$quiz->id&amp;attempt=$attempt->id\">$attemptgrade</a>";
                        $attempt->attempt = "<a href=\"review.php?q=$quiz->id&amp;attempt=$attempt->id\">#$attempt->attempt</a>";
                    }
                }

                if ($quiz->grade <> $quiz->sumgrades) {
                    $table->data[] = array( $attempt->attempt,
                                            $timetaken,
                                            $datecompleted,
                                            $attemptmark, $attemptgrade);
                } else {
                    $table->data[] = array( $attempt->attempt,
                                            $timetaken,
                                            $datecompleted,
                                            $attemptgrade);
                }
            } else {  // No grades are being used
                if (quiz_review_allowed($quiz)) {
                    if($attempt->timefinish > 0) {
                        $attempt->attempt = "<a href=\"review.php?q=$quiz->id&amp;attempt=$attempt->id\">#$attempt->attempt</a>";
                    } else {
                        $attempt->attempt = "<a href=\"attempt.php?id=$id\">#$attempt->attempt</a>";
                    }
                }
                $table->data[] = array( $attempt->attempt,
                                        $timetaken,
                                        $datecompleted);
            }
        }
        print_table($table);
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
            print_continue('../../course/view.php?id='.$course->id);
        }
    }
// Finish the page
    echo '</td></tr></table>';

    print_footer($course);

    function quiz_review_allowed($quiz) {
        // If not even responses are to be shown in review then we
        // don't allow any review
        if (!($quiz->review & QUIZ_REVIEW_RESPONSES)) {
            return false;
        }
        if ((!$quiz->timeclose or time() < $quiz->timeclose) and !($quiz->review & QUIZ_REVIEW_OPEN)) {
            return false;
        }
        if (($quiz->timeclose and time() > $quiz->timeclose) and !($quiz->review & QUIZ_REVIEW_CLOSED)) {
            return false;
        }
        return true;
    }

?>
