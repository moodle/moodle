<?php  // $Id$
/**
 * This page prints a review of a particular question attempt
 *
 * @author Martin Dougiamas and many others. This has recently been completely
 *         rewritten by Alex Smith, Julian Sedding and Gustav Delius as part of
 *         the Serving Mathematics project
 *         {@link http://maths.york.ac.uk/serving_maths}
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */

    require_once('../../config.php');
    require_once('locallib.php');

    // Either stateid or (attemptid AND questionid) must be given
    $stateid = optional_param('state', 0, PARAM_INT); // state id
    $attemptid = optional_param('attempt', 0, PARAM_INT); // attempt id
    $questionid = optional_param('question', 0, PARAM_INT); // attempt id
    $number = optional_param('number', 0, PARAM_INT);  // question number

    if ($stateid) {
        if (! $state = get_record('question_states', 'id', $stateid)) {
            error('Invalid state id');
        }
        if (! $attempt = get_record('quiz_attempts', 'uniqueid', $state->attempt)) {
            error('No such attempt ID exists');
        }
    } elseif ($attemptid) {
        if (! $attempt = get_record('quiz_attempts', 'id', $attemptid)) {
            error('No such attempt ID exists');
        }
        if (! $neweststateid = get_field('question_sessions', 'newest', 'attemptid', $attempt->uniqueid, 'questionid', $questionid)) {
            // newest_state not set, probably because this is an old attempt from the old quiz module code
            if (! $state = get_record('question_states', 'question', $questionid, 'attempt', $attempt->uniqueid)) {
                error('Invalid question id');
            }
        } else {
            if (! $state = get_record('question_states', 'id', $neweststateid)) {
                error('Invalid state id');
            }
        }
    } else {
        error('Parameter missing');
    }
    if (! $question = get_record('question', 'id', $state->question)) {
        error('Question for this state is missing');
    }
    if (! $quiz = get_record('quiz', 'id', $attempt->quiz)) {
        error('Course module is incorrect');
    }
    if (! $course = get_record('course', 'id', $quiz->course)) {
        error('Course is misconfigured');
    }
    if (! $cm = get_coursemodule_from_instance('quiz', $quiz->id, $course->id)) {
        error('Course Module ID was incorrect');
    }

    require_login($course->id, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    if (!has_capability('mod/quiz:viewreports', $context)) {
        if (!$attempt->timefinish) {
            redirect('attempt.php?q='.$quiz->id);
        }
        require_capability('mod/quiz:reviewmyattempts', $context);
        // If not even responses are to be shown in review then we
        // don't allow any review
        if (!($quiz->review & QUIZ_REVIEW_RESPONSES)) {
            print_error("noreview", "quiz");
        }
        if ((time() - $attempt->timefinish) > 120) { // always allow review right after attempt
            if ((!$quiz->timeclose or time() < $quiz->timeclose) and !($quiz->review & QUIZ_REVIEW_OPEN)) {
                print_error("noreviewuntil", "quiz", '', userdate($quiz->timeclose));
            }
            if ($quiz->timeclose and time() >= $quiz->timeclose and !($quiz->review & QUIZ_REVIEW_CLOSED)) {
                print_error("noreview", "quiz");
            }
        }
        if ($attempt->userid != $USER->id) {
            error('This is not your attempt!');
        }
    }

    //add_to_log($course->id, 'quiz', 'review', "review.php?id=$cm->id&amp;attempt=$attempt->id", "$quiz->id", "$cm->id");

/// Print the page header

    $strquizzes = get_string('modulenameplural', 'quiz');

    $question->maxgrade = get_field('quiz_question_instances', 'grade', 'quiz', $quiz->id, 'question', $question->id);
    // Some of the questions code is optimised to work with several questions
    // at once so it wants the question to be in an array. 
    $questions = array($question->id => &$question);
    // Add additional questiontype specific information to the question objects.
    if (!get_question_options($questions)) {
        error("Unable to load questiontype specific question information");
    }

    $baseurl = $CFG->wwwroot . '/mod/quiz/reviewquestion.php?question=' . $question->id . '&amp;number=' . $number . '&amp;attempt=';
    $quiz->thispageurl = $baseurl . $attempt->id;
    $quiz->cmid = $cm->id;

    $session = get_record('question_sessions', 'attemptid', $attempt->uniqueid, 'questionid', $question->id);
    $state->sumpenalty = $session->sumpenalty;
    $state->manualcomment = $session->manualcomment;
    restore_question_state($question, $state);
    $state->last_graded = $state;

    $options = quiz_get_reviewoptions($quiz, $attempt, $context);
    $options->validation = ($state->event == QUESTION_EVENTVALIDATE);
    $options->history = (has_capability('mod/quiz:viewreports', $context) and !$attempt->preview) ? 'all' : 'graded';

    $questionids = array($question->id);
    $states = array($question->id => &$state);
    $headtags = get_html_head_contributions($questionids, $questions, $states);
    print_header('', '', '', '', $headtags);

    echo '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>'; // for overlib

/// Print heading
    print_heading(format_string($question->name));

    /// Print infobox
    $table->align  = array("right", "left");
    if ($attempt->userid <> $USER->id) {
        // Print user picture and name
        $student = get_record('user', 'id', $attempt->userid);
        $picture = print_user_picture($student, $course->id, $student->picture, false, true);
        $table->data[] = array($picture, fullname($student, true));
    }
    // print quiz name
    $table->data[] = array(get_string('modulename', 'quiz').':', format_string($quiz->name));
    if (has_capability('mod/quiz:viewreports', $context) and count($attempts = get_records_select('quiz_attempts', "quiz = '$quiz->id' AND userid = '$attempt->userid'", 'attempt ASC')) > 1) {
        // print list of attempts
        $attemptlist = '';
        foreach ($attempts as $at) {
            $attemptlist .= ($at->id == $attempt->id)
                ? '<b>'.$at->attempt.'</b>, '
                : '<a href="' . $baseurl . $at->id . '">'.$at->attempt.'</a>, ';
        }
        $table->data[] = array(get_string('attempts', 'quiz').':', trim($attemptlist, ' ,'));
    }
    if ($state->timestamp) {
        // print time stamp
        $table->data[] = array(get_string("completedon", "quiz").':', userdate($state->timestamp));
    }
    // Print info box unless it is empty
    if ($table->data) {
        print_table($table);
    }

    print_question($question, $state, $number, $quiz, $options);

    print_footer();

?>
