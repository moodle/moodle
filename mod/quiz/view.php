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

    // Check login and get context.
    require_login($course->id, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    // if no questions have been set up yet redirect to edit.php
    if (!$quiz->questions and has_capability('mod/quiz:manage', $context)) {
        redirect('edit.php?quizid='.$quiz->id);
    }

    add_to_log($course->id, "quiz", "view", "view.php?id=$cm->id", $quiz->id, $cm->id);

    // Initialize $PAGE, compute blocks
    $PAGE       = page_create_instance($quiz->id);
    $pageblocks = blocks_setup($PAGE);
    $blocks_preferred_width = bounded_number(180, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]), 210);

    // Print the page header
    if ($edit != -1 and $PAGE->user_allowed_editing()) {
        $USER->editing = $edit;
    }

    //only check pop ups if the user is not a teacher, and popup is set

    $bodytags = (has_capability('mod/quiz:attempt', $context) && $quiz->popup)?'onload="popupchecker(\'' . get_string('popupblockerwarning', 'quiz') . '\');"':'';
    $PAGE->print_header($course->shortname.': %fullname%','',$bodytags);

    echo '<table id="layout-table"><tr>';

    if(!empty($CFG->showblocksonmodpages) && (blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $PAGE->user_is_editing())) {
        echo '<td style="width: '.$blocks_preferred_width.'px;" id="left-column">';
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
        echo '</td>';
    }

    echo '<td id="middle-column">';

    // Print the main part of the page

    // Print heading and tabs (if there is more than one).
    $currenttab = 'info';
    include('tabs.php');

    // Print quiz name

    print_heading(format_string($quiz->name));

    if (has_capability('mod/quiz:view', $context)) {

        // Print quiz description
        if (trim(strip_tags($quiz->intro))) {
            $formatoptions->noclean = true;
            print_box(format_text($quiz->intro, FORMAT_MOODLE, $formatoptions), 'generalbox', 'intro');
        }

        echo '<div class="quizinfo">';

        // Print information about number of attempts and grading method.
        if ($quiz->attempts > 1) {
            echo "<p>".get_string("attemptsallowed", "quiz").": $quiz->attempts</p>";
        }
        if ($quiz->attempts != 1) {
            echo "<p>".get_string("grademethod", "quiz").": ".quiz_get_grading_option_name($quiz->grademethod)."</p>";
        }

        // Print information about timings.
        $timenow = time();
        $available = ($quiz->timeopen < $timenow and ($timenow < $quiz->timeclose or !$quiz->timeclose));
        if ($available) {
            if ($quiz->timelimit) {
                echo "<p>".get_string("quiztimelimit","quiz", format_time($quiz->timelimit * 60))."</p>";
            }
            quiz_view_dates($quiz);
        } else if ($timenow < $quiz->timeopen) {
            echo "<p>".get_string("quiznotavailable", "quiz", userdate($quiz->timeopen))."</p>";
        } else {
            echo "<p>".get_string("quizclosed", "quiz", userdate($quiz->timeclose))."</p>";
        }
        echo '</div>';
    } else {
        $available = false;
    }

    // Show number of attempts summary to those who can view reports.
    if (has_capability('mod/quiz:viewreports', $context)) {
        if ($a->attemptnum = count_records('quiz_attempts', 'quiz', $quiz->id, 'preview', 0)) {
            $a->studentnum = count_records_select('quiz_attempts', "quiz = '$quiz->id' AND preview = '0'", 'COUNT(DISTINCT userid)');
            $a->studentstring  = $course->students;

            notify("<a href=\"report.php?mode=overview&amp;id=$cm->id\">".get_string('numattempts', 'quiz', $a).'</a>');
        }
    }

    // Guests can't do a quiz, so offer them a choice of logging in or going back.

    // TODO, work out what to do about this under roles and permissions.
    // You have to be logged in to do a quiz, because attempts are tied to
    // userid, and so if guests were allowed to attempt quizzes, all guests
    // would see all attempts, and it would be confusing.
    //
    // So for courses that allow guest access, it is good to offer people an easy
    // way to log in at this point if they have got this far before logging in.
    if (isguestuser()) {
        $loginurl = $CFG->wwwroot.'/login/index.php';
        if (!empty($CFG->loginhttps)) {
            $loginurl = str_replace('http:','https:', $loginurl);
        }

        notice_yesno('<p>' . get_string('guestsno', 'quiz') . "</p>\n\n</p>" .
                get_string('liketologin') . '</p>', $loginurl, get_referer(false));
    }

    if (has_capability('mod/quiz:attempt', $context)) {

        // Get this user's attempts.
        $attempts = quiz_get_user_attempts($quiz->id, $USER->id);
        $unfinished = false;
        if ($unfinishedattempt = quiz_get_user_attempt_unfinished($quiz->id, $USER->id)) {
            $attempts[] = $unfinishedattempt;
            $unfinished = true;
        }
        $numattempts = count($attempts);

        $mygrade = quiz_get_best_grade($quiz, $USER->id);

        // Get some strings.
        $strattempt       = get_string("attempt", "quiz");
        $strtimetaken     = get_string("timetaken", "quiz");
        $strtimecompleted = get_string("timecompleted", "quiz");
        $strgrade         = get_string("grade");
        $strmarks         = get_string('marks', 'quiz');
        $strfeedback      = get_string('feedback', 'quiz');

        // Print table with existing attempts
        if ($attempts) {

            // Work out which columns we need, taking account what data is available in each attempt.
            list($someoptions, $alloptions) = quiz_get_combined_reviewoptions($quiz, $attempts, $context);

            $gradecolumn = $someoptions->scores && $quiz->grade && $quiz->sumgrades;
            $markcolumn = $gradecolumn && ($quiz->grade != $quiz->sumgrades);
            $overallstats = $alloptions->scores;

            $feedbackcolumn = quiz_has_feedback($quiz->id);
            $overallfeedback = $feedbackcolumn && $alloptions->overallfeedback;

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
                $attemptoptions = quiz_get_reviewoptions($quiz, $attempt, $context);
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

                // Ouside the if because we may be showing feedback but not grades.
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
                    if ($attemptoptions->overallfeedback) {
                        $row[] = quiz_feedback_for_grade($attemptgrade, $quiz->id);
                    } else {
                        $row[] = '';
                    }
                }

                if (isset($quiz->showtimetaken)) {
                    $row[] = $timetaken;
                }

                $table->data[] = $row;
            } // End of loop over attempts.
            print_table($table);
        }

        // Print information about the student's best score for this quiz if possible.
        $moreattempts = $unfinished || $numattempts < $quiz->attempts || $quiz->attempts == 0;
        if (!$moreattempts) {
            print_heading(get_string("nomoreattempts", "quiz"));
        }

        if ($numattempts && $quiz->sumgrades && !is_null($mygrade)) {
            if ($overallstats) {
                if ($available && $moreattempts) {
                    $a = new stdClass;
                    $a->method = quiz_get_grading_option_name($quiz->grademethod);
                    $a->mygrade = $mygrade;
                    $a->quizgrade = $quiz->grade;
                    print_heading(get_string('gradesofar', 'quiz', $a));
                } else {
                    print_heading(get_string('yourfinalgradeis', 'quiz', "$mygrade / $quiz->grade"));
                }
            }

            if ($overallfeedback) {
                echo '<p class="quizgradefeedback">'.quiz_feedback_for_grade($mygrade, $quiz->id).'</p>';
            }
        }

        // Print a button to start/continue an attempt, if appropriate.

        if (!$quiz->questions) {
            print_heading(get_string("noquestions", "quiz"));

        } else if ($available && $moreattempts) {
            echo "<br />";
            echo "<div class=\"quizattempt\">";

            if ($unfinished) {
                if (has_capability('mod/quiz:preview', $context)) {
                    $buttontext = get_string('continuepreview', 'quiz');
                } else {
                    $buttontext = get_string('continueattemptquiz', 'quiz');
                }
            } else {

                // Work out the appropriate button caption.
                if (has_capability('mod/quiz:preview', $context)) {
                    $buttontext = get_string('previewquiznow', 'quiz');
                } else if ($numattempts == 0) {
                    $buttontext = get_string('attemptquiznow', 'quiz');
                } else {
                    $buttontext = get_string('reattemptquiz', 'quiz');
                }

                // Work out if the quiz is temporarily unavailable because of the delay option.
                if (!empty($attempts)) {
                    $tempunavailable = '';
                    $lastattempt = end($attempts);
                    $lastattempttime = $lastattempt->timefinish;
                    if ($numattempts == 1 && $quiz->delay1 && $timenow <= $lastattempttime + $quiz->delay1) {
                        $tempunavailable = get_string('temporaryblocked', 'quiz') .
                                ' <strong>'. userdate($lastattempttime + $quiz->delay1). '</strong>';
                    } else if ($numattempts > 1 && $quiz->delay2 && $timenow <= $lastattempttime +  $quiz->delay2) {
                        $tempunavailable = get_string('temporaryblocked', 'quiz') .
                                ' <strong>'. userdate($lastattempttime + $quiz->delay2). '</strong>';
                    }

                    // If so, display a message and prevent the start button from appearing.
                    if ($tempunavailable) {
                        print_simple_box($tempunavailable, "center");
                        print_continue($CFG->wwwroot . '/course/view.php?id=' . $course->id);
                        $buttontext = '';
                    }
                }
            }

            // Actually print the start button.
            if ($buttontext) {
                $buttontext = htmlspecialchars($buttontext, ENT_QUOTES);

                // Do we need a confirm javascript alert?
                if ($unfinished) {
                    $strconfirmstartattempt =  '';
                } else if ($quiz->timelimit && $quiz->attempts) {
                    $strconfirmstartattempt = addslashes(get_string('confirmstartattempttimelimit','quiz', $quiz->attempts));
                } else if ($quiz->timelimit) {
                    $strconfirmstartattempt = addslashes(get_string('confirmstarttimelimit','quiz'));
                } else if ($quiz->attempts) {
                    $strconfirmstartattempt = addslashes(get_string('confirmstartattemptlimit','quiz', $quiz->attempts));
                } else {
                    $strconfirmstartattempt =  '';
                }

                // Prepare options depending on whether the quiz should be a popup.
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

                // Determine the URL to use.
                $attempturl = "attempt.php?id=$cm->id";
                if (!empty($CFG->usesid) && !isset($_COOKIE[session_name()])) {
                    $attempturl = sid_process_url($attempturl);
                }

                // TODO eliminate this nasty JavaScript that prints the button.
?>
<script type="text/javascript">
//<![CDATA[
document.write('<input type="button" value="<?php echo $buttontext ?>" onclick="javascript: <?php
                if ($strconfirmstartattempt) {
                    echo "if (confirm(\\'".addslashes_js($strconfirmstartattempt)."\\'))";
                }
?> window.open(\'<?php echo $attempturl ?>\', \'<?php echo $window ?>\', \'<?php echo $windowoptions ?>\'); " />');
//]]>
</script>
<noscript>
<div>
    <?php print_heading(get_string('noscript', 'quiz')); ?>
</div>
</noscript>
<?php
            }

            echo "</div>\n";
        } else {
            print_continue($CFG->wwwroot . '/course/view.php?id=' . $course->id);
        }
    }

    // Should we not be seeing if we need to print right-hand-side blocks?

    // Finish the page.
    echo '</td></tr></table>';
    print_footer($course);

// Utility functions =================================================================

function quiz_review_allowed($quiz) {
    return true;
}

/** Make some text into a link to review the quiz, if that is appropriate. */
function make_review_link($linktext, $quiz, $attempt) {
    // If not even responses are to be shown in review then we don't allow any review
    if (!($quiz->review & QUIZ_REVIEW_RESPONSES)) {
        return $linktext;
    }

    // If the quiz is still open, are reviews allowed?
    if ((!$quiz->timeclose or time() < $quiz->timeclose) and !($quiz->review & QUIZ_REVIEW_OPEN)) {
        // If not, don't link.
        return $linktext;
    }

    // If the quiz is closed, are reviews allowed?
    if (($quiz->timeclose and time() > $quiz->timeclose) and !($quiz->review & QUIZ_REVIEW_CLOSED)) {
        // If not, don't link.
        return $linktext;
    }

    // If the attempt is still open, don't link.
    if (!$attempt->timefinish) {
        return $linktext;
    }

    $url = "review.php?q=$quiz->id&amp;attempt=$attempt->id";
    if ($quiz->popup) {
        $windowoptions = "left=0, top=0, channelmode=yes, fullscreen=yes, scrollbars=yes, resizeable=no, directories=no, toolbar=no, titlebar=no, location=no, status=no, menubar=no";
        return link_to_popup_window('/mod/quiz/' . $url, 'quizpopup', $linktext, '+window.screen.height+', '+window.screen.width+', '', $windowoptions, true);
    } else {
        return "<a href='$url'>$linktext</a>";
    }
}
?>
