<?php  // $Id$

// This page prints a particular instance of quiz

    require_once("../../config.php");
    require_once("locallib.php");
    require_once($CFG->libdir.'/blocklib.php');
    require_once('pagelib.php');

    $id   = optional_param('id', 0, PARAM_INT); // Course Module ID, or
    $q    = optional_param('q',  0, PARAM_INT);  // quiz ID
    $edit = optional_param('edit', -1, PARAM_BOOL);
    
    if ($id) {
        if (! $cm = get_coursemodule_from_id('quiz', $id)) {
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
    if ($edit != -1 and $PAGE->user_allowed_editing()) {
        $USER->editing = $edit;
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
    
    // Print quiz name and description.
    print_heading(format_string($quiz->name));

    if (trim(strip_tags($quiz->intro))) {
        $formatoptions->noclean = true;
        print_simple_box(format_text($quiz->intro, FORMAT_MOODLE, $formatoptions), "center");
    }

    // Print information about number of attempts and grading method.
    if ($quiz->attempts > 1) {
        echo "<p align=\"center\">".get_string("attemptsallowed", "quiz").": $quiz->attempts</p>";
    } 
    if ($quiz->attempts != 1) {
        echo "<p align=\"center\">".get_string("grademethod", "quiz").": ".$QUIZ_GRADE_METHOD[$quiz->grademethod]."</p>";
    }
    
    // Print information about timings.
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
        if ($a->attemptnum = count_records('quiz_attempts', 'quiz', $quiz->id, 'preview', 0)) {
            $a->studentnum = count_records_select('quiz_attempts', "quiz = '$quiz->id' AND preview = '0'", 'COUNT(DISTINCT userid)');
            $a->studentstring  = $course->students;
    
            notify("<a href=\"report.php?mode=overview&amp;id=$cm->id\">".get_string('numattempts', 'quiz', $a).'</a>');
        }
        
        end_page($course);
        exit;
    }

    // Guests can't do a quiz, so offer them a choice of logging in going back.
    if (isguest()) {
        $loginurl = $CFG->wwwroot.'/login/index.php';
        if (!empty($CFG->loginhttps)) {
            $loginurl = str_replace('http:','https:', $loginurl);
        }

        notice_yesno('<p>' . get_string('guestsno', 'quiz') . "</p>\n\n</p>" . 
                get_string('liketologin') . '</p>', $loginurl, $_SERVER['HTTP_REFERER']);
        
        end_page($course);
        exit;
    }

    // Get this user's attempts.
    $attempts = quiz_get_user_attempts($quiz->id, $USER->id);
    $unfinished = false;
    if ($unfinishedattempt = quiz_get_user_attempt_unfinished($quiz->id, $USER->id)) {
        $attempts[] = $unfinishedattempt;
        $unfinished = true;
    }
    $numattempts = count($attempts);

    $strattempt       = get_string("attempt", "quiz");
    $strtimetaken     = get_string("timetaken", "quiz");
    $strtimecompleted = get_string("timecompleted", "quiz");
    $strgrade         = get_string("grade");
    $strmarks         = get_string('marks', 'quiz');
    $strfeedback      = get_string('feedback', 'quiz');

    $mygrade = quiz_get_best_grade($quiz, $USER->id);

    if ($attempts) {
        // Print table with existing attempts

        // Work out which columns we need, taking account what data is available in each attempt.
        $gradecolumn = 0;
        $overallstats = 1;
        foreach ($attempts as $attempt) {            
            $attemptoptions = quiz_get_reviewoptions($quiz, $attempt, $isteacher);
            if ($attemptoptions->scores) {
                $gradecolumn = 1;
            } else {
                $overallstats = 0;
            }
        }
        $gradecolumn = $gradecolumn && $quiz->grade && $quiz->sumgrades;
        $markcolumn = $gradecolumn && ($quiz->grade <> $quiz->sumgrades);
        $feedbackcolumn = quiz_has_feedback($quiz->id);

        // prepare table header
        $table->head = array($strattempt, $strtimecompleted);
        $table->align = array("center", "left");
        $table->size = array("", "");
        if ($markcolumn) {
            $table->head[] = "$strmarks / $quiz->sumgrades";
            $table->align[] = 'right';
            $table->size[] = '';
        }
        if ($gradecolumn) {
            $table->head[] = "$strgrade / $quiz->grade";
            $table->align[] = 'right';
            $table->size[] = '';
        }
        if ($feedbackcolumn) {
            $table->head[] = $strfeedback;
            $table->align[] = 'left';
            $table->size[] = '';
        }
        if (isset($quiz->showtimetaken)) {
            $table->head[] = $strtimetaken;
            $table->align[] = 'left';
            $table->size[] = '';
        }

        // One row for each attempt
        foreach ($attempts as $attempt) {
            $attemptoptions = quiz_get_reviewoptions($quiz, $attempt, $isteacher);
            $row = array();
            
            // Add the attempt number, making it a link, if appropriate.
            $row[] = make_review_link('#' . $attempt->attempt, $quiz, $attempt);
            
            // prepare strings for time taken and date completed
            $timetaken = '';
            $datecompleted = '';
            if ($attempt->timefinish > 0) {
                // attempt has finished
                $timetaken = format_time($attempt->timefinish - $attempt->timestart);
                $datecompleted = userdate($attempt->timefinish);
            } else if ($available) {
                // The attempt is still in progress.
                $timetaken = format_time(time() - $attempt->timestart);
                $datecompleted = '';
            } else if ($quiz->timeclose) {
                // The attempt was not completed but is also not available any more becuase the quiz is closed.
                $timetaken = format_time($quiz->timeclose - $attempt->timestart);
                $datecompleted = userdate($quiz->timeclose);
            } else {
                // Something wheird happened.
                $timetaken = '';
                $datecompleted = '';
            }
            $row[] = $datecompleted;
            
            if ($markcolumn) {
                if ($attemptoptions->scores) {
                    $row[] = make_review_link(round($attempt->sumgrades, $quiz->decimalpoints), $quiz, $attempt);
                } else {
                    $row[] = '';
                }
            } 

            // Ouside the if becuase we may be showing feedback but not grades.
            $attemptgrade = quiz_rescale_grade($attempt->sumgrades, $quiz);
            if ($gradecolumn) {
                if ($attemptoptions->scores) {
                    // highlight the highest grade if appropriate
                    if ($overallstats && !is_null($mygrade) && $attemptgrade == $mygrade && $quiz->grademethod == QUIZ_GRADEHIGHEST) {
                        $formattedgrade = "<span class='highlight'>$attemptgrade</span>";
                    } else {
                        $formattedgrade = $attemptgrade;
                    }
                    
                    $row[] = make_review_link($formattedgrade, $quiz, $attempt);
                } else {
                    $row[] = '';
                }
            }
            
            if ($feedbackcolumn) {
                if ($attemptoptions->feedback) {
                    $row[] = quiz_feedback_for_grade($attemptgrade, $quiz->id);
                } else {
                    $row[] = '';
                }
            } 
            
            if (isset($quiz->showtimetaken)) {
                $row[] = $timetaken;
            }
            
            $table->data[] = $row;
        }
        print_table($table);
    }

    // Print information about the student's best score for this quiz if possible.
    $moreattempts = $numattempts < $quiz->attempts || $quiz->attempts == 0;
    if (!$moreattempts) {
        print_heading(get_string("nomoreattempts", "quiz"));
    }
    
    if ($numattempts && $quiz->sumgrades) {
        if (!is_null($mygrade)) {
            if ($available && $moreattempts) {
                $strbestgrade = $QUIZ_GRADE_METHOD[$quiz->grademethod];
                $grademessage = "$strbestgrade: $mygrade / $quiz->grade.";
            } else {
                $grademessage = get_string("yourfinalgradeis", "quiz", "$mygrade / $quiz->grade");
            }
        
            if ($overallstats) {
                print_heading($grademessage);
            }
            
            if ($feedbackcolumn) {
                echo '<p align="center">', quiz_feedback_for_grade($mygrade, $quiz->id), '</p>';
            }
        }

        if (!($moreattempts && $available)) {
            print_continue($CFG->webroot . '/course/view.php?id=' . $course->id);
        }
    }
    
    if ($quiz->questions) {
        // Print a button to start the quiz if appropriate.
        if ($available && $moreattempts) {
            echo "<br />";
            echo "<div align=\"center\">";
            if ($quiz->delay1 or $quiz->delay2) {
                 //quiz enforced time delay
                 $lastattempt_obj = get_record_select('quiz_attempts', "quiz = $quiz->id AND attempt = $numattempts AND userid = $USER->id", 'timefinish');
                 if ($lastattempt_obj) {
                     $lastattempt = $lastattempt_obj->timefinish;
                 }
                 if($numattempts == 1 && $quiz->delay1) {
                     if ($timenow - $quiz->delay1 > $lastattempt) {
                          print_start_quiz_button($quiz, $attempts, $numattempts, $unfinished, $cm);
                     } else {
                         $notify_msg = get_string('temporaryblocked', 'quiz') . '<b>'. userdate($lastattempt + $quiz->delay1). '<b>';
                         print_simple_box($notify_msg, "center");
                     }
                 } else if($numattempts > 1 && $quiz->delay2) {
                     if ($timenow - $quiz->delay2 > $lastattempt) {
                          print_start_quiz_button($quiz, $attempts, $numattempts, $unfinished, $cm);
                     } else {
                          $notify_msg = get_string('temporaryblocked', 'quiz') . '<b>'. userdate($lastattempt + $quiz->delay2). '<b>';
                          print_simple_box($notify_msg, "center");
                     }
                 } else {
                     print_start_quiz_button($quiz, $attempts, $numattempts, $unfinished, $cm);
                 }
            } else {
                 print_start_quiz_button($quiz, $attempts, $numattempts, $unfinished, $cm);
            }     
            echo "</div>\n";
        }
    } else {
        // No questions in quiz.
        print_heading(get_string("noquestions", "quiz"));
    }

    // Finish the page - this needs to be the same as in the if teacher block above.
    echo '</td></tr></table>';
    print_footer($course);

// Utility functions =================================================================

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


function print_start_quiz_button($quiz, $attempts, $numattempts, $unfinished, $cm) {
    $strconfirmstartattempt =  '';
    
    if ($unfinished) {
        $buttontext = get_string('continueattemptquiz', 'quiz');
    } else {
        if ($numattempts) {
            $buttontext = get_string('reattemptquiz', 'quiz');
        } else {
            $buttontext = get_string('attemptquiznow', 'quiz');
        }
        if ($quiz->timelimit && $quiz->attempts) {
            $strconfirmstartattempt = addslashes(get_string('confirmstartattempttimelimit','quiz', $quiz->attempts));
        } else if ($quiz->timelimit) {
            $strconfirmstartattempt = addslashes(get_string('confirmstarttimelimit','quiz'));
        } else if ($quiz->attempts) {
            $strconfirmstartattempt = addslashes(get_string('confirmstartattemptlimit','quiz', $quiz->attempts));
        }
    }
    $buttontext = htmlspecialchars($buttontext, ENT_QUOTES);

    if (!empty($quiz->popup)) {
        $window = 'quizpopup';
        $windowoptions = "left=0, top=0, height='+window.screen.height+', " .
                "width='+window.screen.width+', channelmode=yes, fullscreen=yes, " .
                "scrollbars=yes, resizeable=no, directories=no, toolbar=no, " .
                "titlebar=no, location=no, status=no, menubar=no";
    } else {
        $window = '_self';
        $windowoptions = '';
    }

    $attempturl = "attempt.php?id=$cm->id";
    if (!empty($CFG->usesid) && !isset($_COOKIE[session_name()])) {
        $attempturl = sid_process_url($attempturl);
    }
?>
<script language="javascript" type="text/javascript">
<!--
document.write('<input type="button" value="<?php echo $buttontext ?>" onclick="javascript: <?php 
        if ($strconfirmstartattempt) {
            echo "if (confirm(\\'".addslashes($strconfirmstartattempt)."\\'))"; 
        } 
?> window.open(\'<?php echo $attempturl ?>\', \'<?php echo $window ?>\', \'<?php echo $windowoptions ?>\'); " />');
// -->
</script>
<noscript>
    <strong><?php print_string('noscript', 'quiz'); ?></strong>
</noscript>
<?php
}

function make_review_link($linktext, $quiz, $attempt) {
    $windowoptions = "left=0, top=0, channelmode=yes, fullscreen=yes, scrollbars=yes, resizeable=no, directories=no, toolbar=no, titlebar=no, location=no, status=no, menubar=no";

    $link = $linktext;

    if ($attempt->timefinish && quiz_review_allowed($quiz)) {
        $url = "review.php?q=$quiz->id&amp;attempt=$attempt->id";
        if ($quiz->popup) {
            $link = link_to_popup_window('/mod/quiz/' . $url, 'quizpopup', $linktext, '+window.screen.height+', '+window.screen.width+', '', $windowoptions, true);
        } else {
            $link = "<a href='$url'>$linktext</a>";
        }
    }

    return $link;
}

function end_page($course) {
    echo '</td></tr></table>';
    print_footer($course);
}
?>
