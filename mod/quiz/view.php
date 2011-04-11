<?php

/// This page prints a particular instance of quiz

    require_once(dirname(__FILE__) . '/../../config.php');
    require_once($CFG->libdir.'/gradelib.php');
    require_once($CFG->dirroot.'/mod/quiz/locallib.php');
    require_once($CFG->libdir . '/completionlib.php');

    $id = optional_param('id', 0, PARAM_INT); // Course Module ID, or
    $q = optional_param('q',  0, PARAM_INT);  // quiz ID

    if ($id) {
        if (! $cm = get_coursemodule_from_id('quiz', $id)) {
            print_error('invalidcoursemodule');
        }
        if (! $course = $DB->get_record('course', array('id' => $cm->course))) {
            print_error('coursemisconf');
        }
        if (! $quiz = $DB->get_record('quiz', array('id' => $cm->instance))) {
            print_error('invalidcoursemodule');
        }
    } else {
        if (! $quiz = $DB->get_record('quiz', array('id' => $q))) {
            print_error('invalidquizid', 'quiz');
        }
        if (! $course = $DB->get_record('course', array('id' => $quiz->course))) {
            print_error('invalidcourseid');
        }
        if (! $cm = get_coursemodule_from_instance("quiz", $quiz->id, $course->id)) {
            print_error('invalidcoursemodule');
        }
    }

/// Check login and get context.
    require_login($course->id, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('mod/quiz:view', $context);

/// Cache some other capabilities we use several times.
    $canattempt = has_capability('mod/quiz:attempt', $context);
    $canreviewmine = has_capability('mod/quiz:reviewmyattempts', $context);
    $canpreview = has_capability('mod/quiz:preview', $context);

/// Create an object to manage all the other (non-roles) access rules.
    $timenow = time();
    $accessmanager = new quiz_access_manager(quiz::create($quiz->id, $USER->id), $timenow,
            has_capability('mod/quiz:ignoretimelimits', $context, NULL, false));

/// If no questions have been set up yet redirect to edit.php
    if (!$quiz->questions && has_capability('mod/quiz:manage', $context)) {
        redirect($CFG->wwwroot . '/mod/quiz/edit.php?cmid=' . $cm->id);
    }

/// Log this request.
    add_to_log($course->id, "quiz", "view", "view.php?id=$cm->id", $quiz->id, $cm->id);

    // Mark module as viewed
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);

/// Initialize $PAGE, compute blocks
    $PAGE->set_url('/mod/quiz/view.php', array('id' => $cm->id));

    $edit = optional_param('edit', -1, PARAM_BOOL);
    if ($edit != -1 && $PAGE->user_allowed_editing()) {
        $USER->editing = $edit;
    }

    $PAGE->requires->yui2_lib('event');

    // Note: MDL-19010 there will be further changes to printing header and blocks.
    // The code will be much nicer than this eventually.
    $title = $course->shortname . ': ' . format_string($quiz->name);

    if ($PAGE->user_allowed_editing()) {
        $buttons = '<table><tr><td><form method="get" action="view.php"><div>'.
            '<input type="hidden" name="id" value="'.$cm->id.'" />'.
            '<input type="hidden" name="edit" value="'.($PAGE->user_is_editing()?'off':'on').'" />'.
            '<input type="submit" value="'.get_string($PAGE->user_is_editing()?'blockseditoff':'blocksediton').'" /></div></form></td></tr></table>';
        $PAGE->set_button($buttons);
    }

    $PAGE->set_title($title);
    $PAGE->set_heading($course->fullname);

    echo $OUTPUT->header();

/// Print quiz name and description
    echo $OUTPUT->heading(format_string($quiz->name));
    if (trim(strip_tags($quiz->intro))) {
        echo $OUTPUT->box(format_module_intro('quiz', $quiz, $cm->id), 'generalbox', 'intro');
    }

/// Display information about this quiz.
    $messages = $accessmanager->describe_rules();
    if ($quiz->attempts != 1) {
        $messages[] = get_string('gradingmethod', 'quiz', quiz_get_grading_option_name($quiz->grademethod));
    }
    echo $OUTPUT->box_start('quizinfo');
    $accessmanager->print_messages($messages);
    echo $OUTPUT->box_end();

/// Show number of attempts summary to those who can view reports.
    if (has_capability('mod/quiz:viewreports', $context)) {
        if ($strattemptnum = quiz_attempt_summary_link_to_reports($quiz, $cm, $context)) {
            echo '<div class="quizattemptcounts">' . $strattemptnum . "</div>\n";
        }
    }

/// Guests can't do a quiz, so offer them a choice of logging in or going back.
    if (isguestuser()) {
        echo $OUTPUT->confirm('<p>' . get_string('guestsno', 'quiz') . "</p>\n\n<p>" .
                get_string('liketologin') . "</p>\n", get_login_url(), get_referer(false));
        echo $OUTPUT->footer();
        exit;
    }

/// If they are not enrolled in this course in a good enough role, tell them to enrol.
    if (!($canattempt || $canpreview || $canreviewmine)) {
        echo $OUTPUT->box('<p>' . get_string('youneedtoenrol', 'quiz') . "</p>\n\n<p>" .
                $OUTPUT->continue_button($CFG->wwwroot . '/course/view.php?id=' . $course->id) .
                "</p>\n", 'generalbox', 'notice');
        echo $OUTPUT->footer();
        exit;
    }

/// Update the quiz with overrides for the current user
    $quiz = quiz_update_effective_access($quiz, $USER->id);

/// Get this user's attempts.
    $attempts = quiz_get_user_attempts($quiz->id, $USER->id);
    $lastfinishedattempt = end($attempts);
    $unfinished = false;
    if ($unfinishedattempt = quiz_get_user_attempt_unfinished($quiz->id, $USER->id)) {
        $attempts[] = $unfinishedattempt;
        $unfinished = true;
    }
    $numattempts = count($attempts);

/// Work out the final grade, checking whether it was overridden in the gradebook.
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

/// Print table with existing attempts
    if ($attempts) {

        echo $OUTPUT->heading(get_string('summaryofattempts', 'quiz'));

        // Work out which columns we need, taking account what data is available in each attempt.
        list($someoptions, $alloptions) = quiz_get_combined_reviewoptions($quiz, $attempts, $context);

        $attemptcolumn = $quiz->attempts != 1;

        $gradecolumn = $someoptions->scores && quiz_has_grades($quiz);
        $markcolumn = $gradecolumn && ($quiz->grade != $quiz->sumgrades);
        $overallstats = $alloptions->scores;

        $feedbackcolumn = quiz_has_feedback($quiz) && $alloptions->overallfeedback;

        // Prepare table header
        $table = new html_table();
        $table->attributes['class'] = 'generaltable quizattemptsummary';
        $table->head = array();
        $table->align = array();
        $table->size = array();
        if ($attemptcolumn) {
            $table->head[] = get_string('attemptnumber', 'quiz');
            $table->align[] = 'center';
            $table->size[] = '';
        }
        $table->head[] = get_string('timecompleted', 'quiz');
        $table->align[] = 'left';
        $table->size[] = '';
        if ($markcolumn) {
            $table->head[] = get_string('marks', 'quiz') . ' / ' . quiz_format_grade($quiz, $quiz->sumgrades);
            $table->align[] = 'center';
            $table->size[] = '';
        }
        if ($gradecolumn) {
            $table->head[] = get_string('grade') . ' / ' . quiz_format_grade($quiz, $quiz->grade);
            $table->align[] = 'center';
            $table->size[] = '';
        }
        if ($canreviewmine) {
            $table->head[] = get_string('review', 'quiz');
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
            if ($attemptcolumn) {
                if ($attempt->preview) {
                    $row[] = get_string('preview', 'quiz');
                } else {
                    $row[] = $attempt->attempt;
                }
            }

            // prepare strings for time taken and date completed
            $timetaken = '';
            $datecompleted = '';
            if ($attempt->timefinish > 0) {
                // attempt has finished
                $timetaken = format_time($attempt->timefinish - $attempt->timestart);
                $datecompleted = userdate($attempt->timefinish);
            } else if (!$quiz->timeclose || $timenow < $quiz->timeclose) {
                // The attempt is still in progress.
                $timetaken = format_time($timenow - $attempt->timestart);
                $datecompleted = '';
            } else {
                $timetaken = format_time($quiz->timeclose - $attempt->timestart);
                $datecompleted = userdate($quiz->timeclose);
            }
            $row[] = $datecompleted;

            if ($markcolumn) {
                if ($attemptoptions->scores && $attempt->timefinish > 0) {
                    $row[] = quiz_format_grade($quiz, $attempt->sumgrades);
                } else {
                    $row[] = '';
                }
            }

            // Ouside the if because we may be showing feedback but not grades.
            $attemptgrade = quiz_rescale_grade($attempt->sumgrades, $quiz, false);

            if ($gradecolumn) {
                if ($attemptoptions->scores && $attempt->timefinish > 0) {
                    $formattedgrade = quiz_format_grade($quiz, $attemptgrade);
                    // highlight the highest grade if appropriate
                    if ($overallstats && !$attempt->preview && $numattempts > 1 && !is_null($mygrade) &&
                            $attemptgrade == $mygrade && $quiz->grademethod == QUIZ_GRADEHIGHEST) {
                        $table->rowclasses[$attempt->attempt] = 'bestrow';
                    }

                    $row[] = $formattedgrade;
                } else {
                    $row[] = '';
                }
            }

            if ($canreviewmine) {
                $row[] = $accessmanager->make_review_link($attempt, $canpreview, $attemptoptions);
            }

            if ($feedbackcolumn && $attempt->timefinish > 0) {
                if ($attemptoptions->overallfeedback) {
                    $row[] = quiz_feedback_for_grade($attemptgrade, $quiz, $context, $cm);
                } else {
                    $row[] = '';
                }
            }

            if (isset($quiz->showtimetaken)) {
                $row[] = $timetaken;
            }

            if ($attempt->preview) {
                $table->data['preview'] = $row;
            } else {
                $table->data[$attempt->attempt] = $row;
            }
        } // End of loop over attempts.
        echo html_writer::table($table);
    }

/// Print information about the student's best score for this quiz if possible.
    $moreattempts = $unfinished || !$accessmanager->is_finished($numattempts, $lastfinishedattempt);
    if (!$moreattempts) {
        echo $OUTPUT->heading(get_string("nomoreattempts", "quiz"));
    }

    if ($numattempts && $gradecolumn && !is_null($mygrade)) {
        $resultinfo = '';

        if ($overallstats) {
            if ($moreattempts) {
                $a = new stdClass;
                $a->method = quiz_get_grading_option_name($quiz->grademethod);
                $a->mygrade = quiz_format_grade($quiz, $mygrade);
                $a->quizgrade = quiz_format_grade($quiz, $quiz->grade);
                $resultinfo .= $OUTPUT->heading(get_string('gradesofar', 'quiz', $a), 2, 'main');
            } else {
                $a = new stdClass;
                $a->grade = quiz_format_grade($quiz, $mygrade);
                $a->maxgrade = quiz_format_grade($quiz, $quiz->grade);
                $a = get_string('outofshort', 'quiz', $a);
                $resultinfo .= $OUTPUT->heading(get_string('yourfinalgradeis', 'quiz', $a), 2, 'main');
            }
        }

        if ($mygradeoverridden) {
            $resultinfo .= '<p class="overriddennotice">'.get_string('overriddennotice', 'grades')."</p>\n";
        }
        if ($gradebookfeedback) {
            $resultinfo .= $OUTPUT->heading(get_string('comment', 'quiz'), 3, 'main');
            $resultinfo .= '<p class="quizteacherfeedback">'.$gradebookfeedback."</p>\n";
        }
        if ($feedbackcolumn) {
            $resultinfo .= $OUTPUT->heading(get_string('overallfeedback', 'quiz'), 3, 'main');
            $resultinfo .= '<p class="quizgradefeedback">'.quiz_feedback_for_grade($mygrade, $quiz, $context, $cm)."</p>\n";
        }

        if ($resultinfo) {
            echo $OUTPUT->box($resultinfo, 'generalbox', 'feedback');
        }
    }

/// Determine if we should be showing a start/continue attempt button,
/// or a button to go back to the course page.
    echo $OUTPUT->box_start('quizattempt');
    $buttontext = ''; // This will be set something if as start/continue attempt button should appear.
    if (!quiz_clean_layout($quiz->questions, true)) {
        echo $OUTPUT->heading(get_string("noquestions", "quiz"));
    } else {
        if ($unfinished) {
            if ($canpreview) {
                $buttontext = get_string('continuepreview', 'quiz');
            } else if ($canattempt) {
                $buttontext = get_string('continueattemptquiz', 'quiz');
            }
        } else {
            if ($canpreview) {
                $buttontext = get_string('previewquiznow', 'quiz');
            } else if ($canattempt) {
                $messages = $accessmanager->prevent_new_attempt($numattempts, $lastfinishedattempt);
                if ($messages) {
                    $accessmanager->print_messages($messages);
                } else if ($numattempts == 0) {
                    $buttontext = get_string('attemptquiznow', 'quiz');
                } else {
                    $buttontext = get_string('reattemptquiz', 'quiz');
                }
            }
        }

        // If, so far, we think a button should be printed, so check if they will be allowed to access it.
        if ($buttontext) {
            if (!$moreattempts) {
                $buttontext = '';
            } else if ($canattempt && $messages = $accessmanager->prevent_access()) {
                $accessmanager->print_messages($messages);
                $buttontext = '';
            }
        }
    }

/// Now actually print the appropriate button.
    if ($buttontext) {
        $accessmanager->print_start_attempt_button($canpreview, $buttontext, $unfinished);
    } else {
        echo $OUTPUT->continue_button($CFG->wwwroot . '/course/view.php?id=' . $course->id);
    }
    echo $OUTPUT->box_end();

    echo $OUTPUT->footer();
