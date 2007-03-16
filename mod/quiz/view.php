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

    if (($edit != -1) and $PAGE->user_allowed_editing()) {
        $USER->editing = $edit;
    }

    //only check pop ups if the user is not a teacher, and popup is set
    
    $bodytags = (isteacher($course->id) or !$quiz->popup)?'':'onload="popupchecker(\'' . get_string('popupblockerwarning', 'quiz') . '\');"';
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
        $formatoptions->noclean = true;
        print_simple_box(format_text($quiz->intro, FORMAT_MOODLE, $formatoptions), "center");
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
        echo "<p align=\"center\">".get_string("quiznotavailable", "quiz", userdate($quiz->timeopen))."</p>";
    } else {
        echo "<p align=\"center\">".get_string("quizclosed", "quiz", userdate($quiz->timeclose))."</p>";
    }


    // This is all the teacher will get
    if ($isteacher) {
        if ($a->attemptnum = count_records('quiz_attempts', 'quiz', $quiz->id, 'preview', 0)) {
            $a->studentnum = count_records_select('quiz_attempts', "quiz = '$quiz->id' AND preview = '0'", 'COUNT(DISTINCT userid)');
            $a->studentstring  = $course->students;
    
            notify("<a href=\"report.php?mode=overview&amp;id=$cm->id\">".get_string('numattempts', 'quiz', $a).'</a>');
        }
        
        echo '</td></tr></table>';
        print_footer($course);
        exit;
    }

    if (isguest()) {

        $wwwroot = $CFG->wwwroot.'/login/index.php';
        if (!empty($CFG->loginhttps)) {
            $wwwroot = str_replace('http:','https:', $wwwroot);
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
    $strmarks         = get_string('marks', 'quiz');
    $strbestgrade     = $QUIZ_GRADE_METHOD[$quiz->grademethod];

    $windowoptions = "left=0, top=0, channelmode=yes, fullscreen=yes, scrollbars=yes, resizeable=no, directories=no, toolbar=no, titlebar=no, location=no, status=no, menubar=no";

    $mygrade = quiz_get_best_grade($quiz, $USER->id);

/// Now print table with existing attempts
    $gradecolumn=0;
    $overallstats=1;

    if ($attempts) {
                    
        //step thru each attempt, checking there are any attempts
        //for which the score can be displayed (need grade columns),
        //and checking if overall grades can be displayed - no attempts for 
        //which the score cannot be displayed
        foreach ($attempts as $attempt) {            
            $attemptoptions = quiz_get_reviewoptions($quiz, $attempt, $isteacher);
            $attemptoptions->scores ? $gradecolumn=1 : $overallstats=0;                    
        }
    /// prepare table header
        $table->head = array($strattempt, $strtimecompleted);
        $table->align = array("center", "left");
        $table->size = array("", "");
        if ($gradecolumn && $quiz->grade and $quiz->sumgrades) { // Grades used so have more columns in table
            if ($quiz->grade <> $quiz->sumgrades) {
                $table->head[] = "$strmarks / $quiz->sumgrades";
                $table->align[] = 'right';
                $table->size[] = '';
            }
            $table->head[] = "$strgrade / $quiz->grade";
            $table->align[] = 'right';
            $table->size[] = '';
        }
        if (isset($quiz->showtimetaken)) {
            $table->head[] = $strtimetaken;
            $table->align[] = 'center';
            $table->size[] = '';
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
                $datecompleted  = "\n".'<script language="javascript" type="text/javascript">';
                $datecompleted .= "\n<!--\n"; // -->
                if (!empty($CFG->usesid) && !isset($_COOKIE[session_name()])) {
                    $attempturl=sid_process_url("attempt.php?id=$cm->id");
                } else {
                    $attempturl="attempt.php?id=$cm->id";
                };
                if (!empty($quiz->popup)) {
                    $datecompleted .= "var windowoptions = 'left=0, top=0, height='+window.screen.height+
                            ', width='+window.screen.width+', channelmode=yes, fullscreen=yes, scrollbars=yes, '+
                            'resizeable=no, directories=no, toolbar=no, titlebar=no, location=no, status=no, '+
                            'menubar=no';\n";
                    $jslink  = "javascript:var popup = window.open(\\'$attempturl\\', \\'quizpopup\\', windowoptions);";
                } else {
                    $jslink = $attempturl;
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

            $attemptoptions = quiz_get_reviewoptions($quiz, $attempt, $isteacher);
        /// prepare strings for attempt number, mark and grade
            //if attempt's score is allowed to be viewed, & qz->sumgrades and qz->sumgrades defined:
            if ($attemptoptions->scores && $quiz->grade and $quiz->sumgrades) {
                $attemptmark  = round($attempt->sumgrades,$quiz->decimalpoints);
                $attemptgrade = round(($attempt->sumgrades/$quiz->sumgrades)*$quiz->grade,$quiz->decimalpoints);

                // highlight the highest grade if appropriate
                if ($overallstats && $attemptgrade == $mygrade and ($quiz->grademethod == QUIZ_GRADEHIGHEST)) {
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
                                            $datecompleted,
                                            $attemptmark, $attemptgrade);
                } else {
                    $table->data[] = array( $attempt->attempt,
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

                $helpbutton=helpbutton('missing\ grade', get_string('wheregrade', 'quiz'), 'quiz', true, false, '',true);
                if($gradecolumn) {
                    $table->data[] = array( $attempt->attempt,
                                            $datecompleted,
                                            $helpbutton);
                     
                } else {
                    $table->data[] = array( $attempt->attempt,
                                            $datecompleted);
                }
            }
            if (isset($quiz->showtimetaken)) {
                $table->data[] = $timetaken;
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
                //if overall stats are allowed (no attemps' grade not visible),
                //and there is at least one attempt, and quiz->grade:
                if ($overallstats and $numattempts and $quiz->grade) {
                    print_heading("$strbestgrade: $mygrade / $quiz->grade.");
                }
                
                echo "<br />";
                echo "</p>";
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
            print_heading(get_string("nomoreattempts", "quiz"));
            //if $quiz->grade and $quiz->sumgrades, and student is allowed to 
            //see summary statistics (no attempt's grade is concealed),
            //show the student their final grade
            if ($quiz->grade and $quiz->sumgrades and $overallstats) {
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

?>
