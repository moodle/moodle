<?php  // $Id$

// This page prints a particular instance of quiz

    require_once("../../config.php");
    require_once($CFG->libdir.'/blocklib.php');
    require_once($CFG->libdir.'/gradelib.php');
    require_once($CFG->dirroot.'/mod/quiz/locallib.php');
    require_once($CFG->dirroot.'/mod/quiz/pagelib.php');

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
        redirect($CFG->wwwroot . '/mod/quiz/edit.php?cmid=' . $cm->id);
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

    $bodytags = (has_capability('mod/quiz:attempt', $context) && $quiz->popup == 1)?'onload="popupchecker(\'' . get_string('popupblockerwarning', 'quiz') . '\');"':'';
    $PAGE->print_header($course->shortname.': %fullname%','',$bodytags);

    echo '<table id="layout-table"><tr>';

    if(!empty($CFG->showblocksonmodpages) && (blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $PAGE->user_is_editing())) {
        echo '<td style="width: '.$blocks_preferred_width.'px;" id="left-column">';
        print_container_start();
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
        print_container_end();
        echo '</td>';
    }

    echo '<td id="middle-column">';
    print_container_start();

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
            $formatoptions->para    = false;
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
        $available = $quiz->timeopen < $timenow && ($timenow < $quiz->timeclose || !$quiz->timeclose);
        if ($available) {
            if ($quiz->timelimit) {
                echo "<p>".get_string("quiztimelimit","quiz", format_time($quiz->timelimit * 60))."</p>";
            }
            if ($quiz->timeopen) {
                echo '<p>', get_string('quizopens', 'quiz'), ': ', userdate($quiz->timeopen), '</p>';
            }
            if ($quiz->timeclose) {
                echo '<p>', get_string('quizcloses', 'quiz'), ': ', userdate($quiz->timeclose), '</p>';
            }
        } else if ($timenow < $quiz->timeopen) {
            echo "<p>".get_string("quiznotavailable", "quiz", userdate($quiz->timeopen))."</p>";
        } else {
            echo "<p>".get_string("quizclosed", "quiz", userdate($quiz->timeclose))."</p>";
        }
        echo '</div>';
        $available = $available && has_any_capability(array('mod/quiz:attempt', 'mod/quiz:preview'), $context);
    } else {
        $available = false;
    }

    // Show number of attempts summary to those who can view reports.
    if (has_capability('mod/quiz:viewreports', $context)) {
        if ($strattemptnum = quiz_num_attempt_summary($quiz, $cm)) {
            echo '<div class="quizattemptcounts"><a href="report.php?mode=overview&amp;id=' .
                    $cm->id . '">' . $strattemptnum . '</a></div>';
        }
    }

    // Guests can't do a quiz, so offer them a choice of logging in or going back.
    if (isguestuser()) {
        $loginurl = $CFG->wwwroot.'/login/index.php';
        if (!empty($CFG->loginhttps)) {
            $loginurl = str_replace('http:','https:', $loginurl);
        }

        notice_yesno('<p>' . get_string('guestsno', 'quiz') . "</p>\n\n</p>" .
                get_string('liketologin') . '</p>', $loginurl, get_referer(false));
        finish_page($course);
    }

    if (!has_any_capability(array('mod/quiz:reviewmyattempts', 'mod/quiz:attempt', 'mod/quiz:preview'), $context)) {
        print_box('<p>' . get_string('youneedtoenrol', 'quiz') . '</p><p>' .
                print_continue($CFG->wwwroot . '/course/view.php?id=' . $course->id, true) .
                '</p>', 'generalbox', 'notice');
        finish_page($course);
    }

    // Get this user's attempts.
    $attempts = quiz_get_user_attempts($quiz->id, $USER->id);
    $unfinished = false;
    if ($unfinishedattempt = quiz_get_user_attempt_unfinished($quiz->id, $USER->id)) {
        $attempts[] = $unfinishedattempt;
        $unfinished = true;
    }
    $numattempts = count($attempts);

    // Work out the final grade, checking whether it was overridden in the gradebook.
    $mygrade = quiz_get_best_grade($quiz, $USER->id);
    $mygradeoverridden = false;
    $gradebookfeedback = '';

    $grading_info = grade_get_grades($course->id, 'mod', 'quiz', $quiz->id, $USER->id);
    if (!empty($grading_info->items)) {
        $item = $grading_info->items[0];
        if (isset($item->grades[$USER->id])) {
            $grade = $item->grades[$USER->id];

            if ($grade->overridden) {
                $mygrade = $grade->grade + 0; // Convert to number.
                $mygradeoverridden = true;
            }
            if (!empty($grade->str_feedback)) {
                $gradebookfeedback = $grade->str_feedback;
            }
        }
    }

    // Print table with existing attempts
    if ($attempts) {

        print_heading(get_string('summaryofattempts', 'quiz'));

        // Work out which columns we need, taking account what data is available in each attempt.
        list($someoptions, $alloptions) = quiz_get_combined_reviewoptions($quiz, $attempts, $context);

        $gradecolumn = $someoptions->scores && $quiz->grade && $quiz->sumgrades;
        $markcolumn = $gradecolumn && ($quiz->grade != $quiz->sumgrades);
        $overallstats = $alloptions->scores;

        $feedbackcolumn = quiz_has_feedback($quiz->id);
        $overallfeedback = $feedbackcolumn && $alloptions->overallfeedback;

        // Prepare table header
        $table->class = 'generaltable quizattemptsummary';
        $table->head = array(get_string('attempt', 'quiz'), get_string('timecompleted', 'quiz'));
        $table->align = array('center', 'left');
        $table->size = array('', '');
        if ($markcolumn) {
            $table->head[] = get_string('marks', 'quiz') . " / $quiz->sumgrades";
            $table->align[] = 'center';
            $table->size[] = '';
        }
        if ($gradecolumn) {
            $table->head[] = get_string('grade') . " / $quiz->grade";
            $table->align[] = 'center';
            $table->size[] = '';
        }
        if ($feedbackcolumn) {
            $table->head[] = get_string('feedback', 'quiz');
            $table->align[] = 'left';
            $table->size[] = '';
        }
        if (isset($quiz->showtimetaken)) {
            $table->head[] = get_string('timetaken', 'quiz');
            $table->align[] = 'left';
            $table->size[] = '';
        }

        // One row for each attempt
        foreach ($attempts as $attempt) {
            $attemptoptions = quiz_get_reviewoptions($quiz, $attempt, $context);
            $row = array();

            // Add the attempt number, making it a link, if appropriate.
            if ($attempt->preview) {
                $row[] = make_review_link(get_string('preview', 'quiz'), $quiz, $attempt, $context);
            } else {
                $row[] = make_review_link($attempt->attempt, $quiz, $attempt, $context);
            }

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
                // Something weird happened.
                $timetaken = '';
                $datecompleted = '';
            }
            $row[] = $datecompleted;

            if ($markcolumn && $attempt->timefinish > 0) {
                if ($attemptoptions->scores) {
                    $row[] = make_review_link(round($attempt->sumgrades, $quiz->decimalpoints), $quiz, $attempt, $context);
                } else {
                    $row[] = '';
                }
            }

            // Ouside the if because we may be showing feedback but not grades.
            $attemptgrade = quiz_rescale_grade($attempt->sumgrades, $quiz);

            if ($gradecolumn) {
                if ($attemptoptions->scores && $attempt->timefinish > 0) {
                    $formattedgrade = $attemptgrade;
                    // highlight the highest grade if appropriate
                    if ($overallstats && $numattempts > 1 && !is_null($mygrade) && $attemptgrade == $mygrade && $quiz->grademethod == QUIZ_GRADEHIGHEST) {
                        $table->rowclass[$attempt->attempt] = 'bestrow';
                    }

                    $row[] = make_review_link($formattedgrade, $quiz, $attempt, $context);
                } else {
                    $row[] = '';
                }
            }

            if ($feedbackcolumn && $attempt->timefinish > 0) {
                if ($attemptoptions->overallfeedback) {
                    $row[] = quiz_feedback_for_grade($attemptgrade, $quiz->id);
                } else {
                    $row[] = '';
                }
            }

            if (isset($quiz->showtimetaken)) {
                $row[] = $timetaken;
            }

            $table->data[$attempt->attempt] = $row;
        } // End of loop over attempts.
        print_table($table);
    }

    // Print information about the student's best score for this quiz if possible.
    $moreattempts = $unfinished || $numattempts < $quiz->attempts || $quiz->attempts == 0;
    if (!$moreattempts) {
        print_heading(get_string("nomoreattempts", "quiz"));
    }

    if ($numattempts && $quiz->sumgrades && !is_null($mygrade)) {
        $resultinfo = '';

        if ($overallstats) {
            if ($available && $moreattempts) {
                $a = new stdClass;
                $a->method = quiz_get_grading_option_name($quiz->grademethod);
                $a->mygrade = $mygrade;
                $a->quizgrade = $quiz->grade;
                $resultinfo .= print_heading(get_string('gradesofar', 'quiz', $a), '', 2, 'main', true);
            } else {
                $resultinfo .= print_heading(get_string('yourfinalgradeis', 'quiz', "$mygrade / $quiz->grade"), '', 2, 'main', true);
            }
        }

        if ($mygradeoverridden) {
            $resultinfo .= '<p class="overriddennotice">'.get_string('overriddennotice', 'grades').'</p>';
        }
        if ($gradebookfeedback) {
            $resultinfo .= print_heading(get_string('comment', 'quiz'), '', 3, 'main', true);
            $resultinfo .= '<p class="quizteacherfeedback">'.$gradebookfeedback.'</p>';
        }
        if ($overallfeedback) {
            $resultinfo .= print_heading(get_string('overallfeedback', 'quiz'), '', 3, 'main', true);
            $resultinfo .= '<p class="quizgradefeedback">'.quiz_feedback_for_grade($mygrade, $quiz->id).'</p>';
        }

        if ($resultinfo) {
            print_box($resultinfo, 'generalbox', 'feedback');
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
                $strconfirmstartattempt = '';
            } else if ($quiz->timelimit && $quiz->attempts) {
                $strconfirmstartattempt = get_string('confirmstartattempttimelimit','quiz', $quiz->attempts);
            } else if ($quiz->timelimit) {
                $strconfirmstartattempt = get_string('confirmstarttimelimit','quiz');
            } else if ($quiz->attempts) {
                $strconfirmstartattempt = get_string('confirmstartattemptlimit','quiz', $quiz->attempts);
            } else {
                $strconfirmstartattempt =  '';
            }
            // Determine the URL to use.
            $attempturl = "attempt.php?id=$cm->id";

            // Prepare options depending on whether the quiz should be a popup.
            if ($quiz->popup == 1) {
                $window = 'quizpopup';
                $windowoptions = "left=0, top=0, height='+window.screen.height+', " .
                        "width='+window.screen.width+', channelmode=yes, fullscreen=yes, " .
                        "scrollbars=yes, resizeable=no, directories=no, toolbar=no, " .
                        "titlebar=no, location=no, status=no, menubar=no";
                if (!empty($CFG->usesid) && !isset($_COOKIE[session_name()])) {
                    $attempturl = sid_process_url($attempturl);
                }

                echo '<input type="button" value="'.$buttontext.'" onclick="javascript:';
                if ($strconfirmstartattempt) {
                    $strconfirmstartattempt = addslashes($strconfirmstartattempt);
                    echo "if (confirm('".addslashes_js($strconfirmstartattempt)."')) ";
                }
                echo "window.open('$attempturl','$window','$windowoptions');", '" />';
            } else if ($quiz->popup == 2 && !quiz_check_safe_browser()) {
                notify(get_string('safebrowsererror', 'quiz'));
            }else {
                print_single_button("attempt.php", array('id'=>$cm->id), $buttontext, 'get', '', false, '', false, $strconfirmstartattempt);
            }


?>
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

    // Should we not be seeing if we need to print right-hand-side blocks?

    finish_page($course);

// Utility functions =================================================================

function finish_page($course) {
    global $THEME;
    print_container_end();
    echo '</td></tr></table>';
    print_footer($course);
    exit;
}

/** Make some text into a link to review the quiz, if that is appropriate. */
function make_review_link($linktext, $quiz, $attempt, $context) {
    static $canreview = null;
    if (is_null($canreview)) {
        $canreview = has_capability('mod/quiz:reviewmyattempts', $context);
    }
    // If not even responses are to be shown in review then we don't allow any review, or does not have review capability.
    if (!$canreview || !($quiz->review & QUIZ_REVIEW_RESPONSES)) {
        return $linktext;
    }

    // If the quiz is still open, are reviews allowed?
    if ((!$quiz->timeclose or time() < $quiz->timeclose) and !($quiz->review & QUIZ_REVIEW_OPEN & QUIZ_REVIEW_RESPONSES)) {
        // If not, don't link.
        return $linktext;
    }

    // If the quiz is closed, are reviews allowed?
    if (($quiz->timeclose and time() > $quiz->timeclose) and !($quiz->review & QUIZ_REVIEW_CLOSED & QUIZ_REVIEW_RESPONSES)) {
        // If not, don't link.
        return $linktext;
    }

    // If the attempt is still open, don't link.
    if (!$attempt->timefinish) {
        return $linktext;
    }

    $url = "review.php?q=$quiz->id&amp;attempt=$attempt->id";
    if ($quiz->popup == 1) {
        $windowoptions = "left=0, top=0, channelmode=yes, fullscreen=yes, scrollbars=yes, resizeable=no, directories=no, toolbar=no, titlebar=no, location=no, status=no, menubar=no";
        return link_to_popup_window('/mod/quiz/' . $url, 'quizpopup', $linktext, '+window.screen.height+', '+window.screen.width+', '', $windowoptions, true);
    } else {
        return "<a href='$url'>$linktext</a>";
    }
}
?>
