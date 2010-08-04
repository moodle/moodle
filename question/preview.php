<?php
/**
 * This page displays a preview of a question
 *
 * The preview uses the option settings from the activity within which the question
 * is previewed or the default settings if no activity is specified. The question session
 * information is stored in the session as an array of subsequent states rather
 * than in the database.
 *
 * TODO: make this work with activities other than quiz
 *
 * @author Alex Smith as part of the Serving Mathematics project
 *         {@link http://maths.york.ac.uk/serving_maths}
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 */

    require_once("../config.php");
    require_once($CFG->libdir.'/questionlib.php');
    require_once($CFG->dirroot.'/mod/quiz/locallib.php'); // We really want to get rid of this

    $id = required_param('id', PARAM_INT);        // question id
    // if no quiz id is specified then a dummy quiz with default options is used
    $quizid = optional_param('quizid', 0, PARAM_INT);
    // if no quiz id is specified then tell us the course

    $pageurl = new moodle_url('/question/preview.php', array('id' => $id, 'continue' => 1));
    if (empty($quizid)) {
        $courseid = required_param('courseid', PARAM_INT);
        $pageurl->param('courseid', $courseid);
    } else {
        $pageurl->param('quizid', $quizid);
    }
    $PAGE->set_url($pageurl);
    $PAGE->set_pagelayout('popup');

    // Test if we are continuing an attempt at a question
    $continue = optional_param('continue', 0, PARAM_BOOL);
    // Check for any of the submit buttons
    $fillcorrect = optional_param('fillcorrect', 0, PARAM_BOOL);
    $markall = optional_param('markall', 0, PARAM_BOOL);
    $finishattempt = optional_param('finishattempt', 0, PARAM_BOOL);
    $back = optional_param('back', 0, PARAM_BOOL);
    $startagain = optional_param('startagain', 0, PARAM_BOOL);
    // We are always continuing an attempt if a submit button was pressed with the
    // exception of the start again button
    if ($fillcorrect || $markall || $finishattempt || $back) {
        $continue = true;
    } else if ($startagain) {
        $continue = false;
    }

    $url = new moodle_url('/question/preview.php');
    $url->param('id', $id);
    if ($quizid) {
        $url->param('quizid', $quizid);
    } else {
        $url->param('courseid', $courseid);
    }
    $url->param('continue', 1);
    if (!$continue) {
        // Start a new attempt; delete the old session
        unset($SESSION->quizpreview);
        // Redirect to ourselves but with continue=1; prevents refreshing the page
        // from restarting an attempt (needed so that random questions don't change)
        redirect($url);
    }
    // Load the question information
    if (!$questions = $DB->get_records('question', array('id' =>  $id))) {
        print_error('cannotloadquestion', 'question');
    }
    if (empty($quizid)) {
        $quiz = new cmoptions;
        $quiz->id = 0;
        $quiz->review = get_config('quiz', 'review');
        require_login($courseid, false);
        $quiz->course = $courseid;
        $quiz->decimalpoints = get_config('quiz', 'decimalpoints');
        $context = get_context_instance(CONTEXT_COURSE, $courseid);
    } else if (!$quiz = $DB->get_record('quiz', array('id' => $quizid))) {
        print_error('invalidquizid', 'quiz', '', $quizid);
    } else {
        $cm = get_coursemodule_from_instance('quiz', $quizid, $quiz->course);
        require_login($quiz->course, false, $cm);
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    }

    if ($maxgrade = $DB->get_field('quiz_question_instances', 'grade', array('quiz' => $quiz->id, 'question' => $id))) {
        $questions[$id]->maxgrade = $maxgrade;
    } else {
        $questions[$id]->maxgrade = $questions[$id]->defaultgrade;
    }

    $quiz->id = 0; // just for safety
    $quiz->questions = $id;

    if (!$category = $DB->get_record("question_categories", array("id" => $questions[$id]->category))) {
        print_error('invalidquestionid', 'quiz');
    }

    if (!question_has_capability_on($questions[$id], 'use', $questions[$id]->category)){
        print_error('cannotpreview', 'question');
    }
    if (isset($COURSE)){
        $quiz->course = $COURSE->id;
    }

    // Load the question type specific information
    if (!get_question_options($questions)) {
        print_error('newattemptfail', 'quiz');
    }

    // Create a dummy quiz attempt
    // TODO: find out what of the following we really need. What is $attempt
    //       really used for?
    $timenow = time();
    $attempt->quiz = $quiz->id;
    $attempt->userid = $USER->id;
    $attempt->attempt = 0;
    $attempt->sumgrades = 0;
    $attempt->timestart = $timenow;
    $attempt->timefinish = 0;
    $attempt->timemodified = $timenow;
    $attempt->uniqueid = 0;
    $attempt->id = 0;
    $attempt->layout = $id;

    // Restore the history of question sessions from the moodle session or create
    // new sessions. Make $states a reference to the states array in the moodle
    // session.
    if (isset($SESSION->quizpreview->states) and $SESSION->quizpreview->questionid == $id) {
        // Reload the question session history from the moodle session
        $states =& $SESSION->quizpreview->states;
        $historylength = count($states) - 1;
        if ($back && $historylength > 0) {
            // Go back one step in the history
            unset($states[$historylength]);
            $historylength--;
        }
    } else {
        // Record the question id in the moodle session
        $SESSION->quizpreview->questionid = $id;
        // Create an empty session for the question
        if (!$newstates = get_question_states($questions, $quiz, $attempt)) {
            print_error('newattemptfail', 'quiz');
        }
        $newstates[$id]->questionsessionid = 0;
        $SESSION->quizpreview->states = array($newstates);
        $states =& $SESSION->quizpreview->states;
        $historylength = 0;
    }

    if (!$fillcorrect && !$back && ($form = data_submitted())) {
        $form = (array)$form;
        $submitted = true;

        // Create a new item in the history of question states (don't simplify!)
        $states[$historylength + 1] = array();
        $states[$historylength + 1][$id] = clone($states[$historylength][$id]);
        $historylength++;
        $curstate =& $states[$historylength][$id];
        $curstate->changed = false;

        // Process the responses
        unset($form['id']);
        unset($form['quizid']);
        unset($form['continue']);
        unset($form['markall']);
        unset($form['finishattempt']);
        unset($form['back']);
        unset($form['startagain']);

        if ($finishattempt) {
            $event = QUESTION_EVENTCLOSE;
        } else if ($markall) {
            $event = QUESTION_EVENTSUBMIT;
        } else {
            $event = QUESTION_EVENTSAVE;
        }
        if ($actions = question_extract_responses($questions, $form, $event)) {
            $actions[$id]->timestamp = 0; // We do not care about timelimits here
            if (!question_process_responses($questions[$id], $curstate, $actions[$id], $quiz, $attempt)) {
                unset($SESSION->quizpreview);
                print_error('errorprocessingresponses', 'question', $url->out());
            }
            if (!$curstate->changed) {
                // Update the current state rather than creating a new one
                $historylength--;
                unset($states[$historylength]);
                $states = array_values($states);
                $curstate =& $states[$historylength][$id];
            }
        }
    } else {
        $submitted = false;
        $curstate =& $states[$historylength][$id];
    }

    // TODO: should not use quiz-specific function here
    $options = quiz_get_renderoptions($quiz, $attempt, $context, $curstate);
    $options->noeditlink = true;

    // Fill in the correct responses (unless the question is in readonly mode)
    if ($fillcorrect && !$options->readonly) {
        $curstate->responses = $QTYPES[$questions[$id]->qtype]->get_correct_responses($questions[$id], $curstate);
    }

    $strpreview = get_string('preview', 'quiz').' '.format_string($questions[$id]->name);
    $questionlist = array($id);
    question_get_html_head_contributions($questionlist, $questions, $states[$historylength]);
    $PAGE->set_title($strpreview);
    $PAGE->set_heading($COURSE->fullname);
    echo $OUTPUT->header();
    
    if (!empty($quizid)) {
        echo '<p class="quemodname">'.get_string('modulename', 'quiz') . ': ';
        p(format_string($quiz->name));
        echo "</p>\n";
    }
    $number = 1;
    echo '<form method="post" action="'.$url->out_omit_querystring().'" enctype="multipart/form-data" id="responseform">', "\n";
    print_question($questions[$id], $curstate, $number, $quiz, $options);

    echo '<div class="controls">';
    echo html_writer::input_hidden_params($url);

    // Print the mark and finish attempt buttons
    echo '<input name="markall" type="submit" value="' . get_string('markall',
     'quiz') . "\" />\n";
    echo '<input name="finishattempt" type="submit" value="' .
     get_string('finishattempt', 'quiz') . "\" />\n";
    echo '<br />';
    echo '<br />';
    // Print the fill correct button (unless the question is in readonly mode)
    if (!$options->readonly) {
        echo '<input name="fillcorrect" type="submit" value="' .
         get_string('fillcorrect', 'quiz') . "\" />\n";
    }
    // Print the navigation buttons
    if ($historylength > 0) {
        echo '<input name="back" type="submit" value="' . get_string('previous',
         'quiz') . "\" />\n";
    }
    // Print the start again button
    echo '<input name="startagain" type="submit" value="' .
     get_string('startagain', 'quiz') . "\" />\n";
    // Print the close window button
    echo '<input type="button" onclick="window.close()" value="' .
     get_string('closepreview', 'quiz') . "\" />";
    echo '</div>';
    echo '</form>';
    echo $OUTPUT->footer();

