<?php  // $Id$
/**
* This page prints a review of a particular question attempt
*
* @version $Id$
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

    $number = required_param('number', PARAM_INT);  // question number

    if ($stateid) {
        if (! $state = get_record('quiz_states', 'id', $stateid)) {
            error('Invalid state id');
        }
        if (! $attempt = get_record('quiz_attempts', 'id', $state->attempt)) {
            error('No such attempt ID exists');
        }
    } elseif ($attemptid) {
        if (! $attempt = get_record('quiz_attempts', 'id', $attemptid)) {
            error('No such attempt ID exists');
        }
        if (! $neweststate = get_field('quiz_newest_states', 'newest', 'attemptid', $attemptid, 'questionid', $questionid)) {
            // newest_state not set, probably because this is an old attempt from the old quiz module code
            if (! $state = get_record('quiz_states', 'question', $questionid, 'attempt', $attemptid)) {
                error('Invalid question id');
            }
        } else {
            if (! $state = get_record('quiz_states', 'id', $neweststate->newest)) {
                error('Invalid state id');
            }
        }
    } else {
        error('Parameter missing');
    }
    if (! $question = get_record('quiz_questions', 'id', $state->question)) {
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
    $isteacher = isteacher($course->id);

    if (!$isteacher) {
        if (!$attempt->timefinish) {
            redirect('attempt.php?q='.$quiz->id);
        }
        // If not even responses are to be shown in review then we
        // don't allow any review
        if (!($quiz->review & QUIZ_REVIEW_RESPONSES)) {
            error(get_string("noreview", "quiz"));
        }
        if ((time() - $attempt->timefinish) > 120) { // always allow review right after attempt
            if (time() < $quiz->timeclose and !($quiz->review & QUIZ_REVIEW_OPEN)) {
                error(get_string("noreviewuntil", "quiz", userdate($quiz->timeclose)));
            }
            if (time() >= $quiz->timeclose and !($quiz->review & QUIZ_REVIEW_CLOSED)) {
                error(get_string("noreview", "quiz"));
            }
        }
        if ($attempt->userid != $USER->id) {
            error('This is not your attempt!');
        }
    }

    //add_to_log($course->id, 'quiz', 'review', "review.php?id=$cm->id&amp;attempt=$attempt->id", "$quiz->id", "$cm->id");

/// Print the page header

    $strquizzes = get_string('modulenameplural', 'quiz');
    $strreviewquestion  = get_string('reviewquestion', 'quiz');

    print_header();

    echo '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>'; // for overlib

/// Print heading
    print_heading(format_string($quiz->name));

    $instance = get_record('quiz_question_instances', 'quiz', $quiz->id, 'question', $question->id);
    $question->instance = $instance->id;
    $question->maxgrade = $instance->grade;
    $question->name_prefix = 'r';
    $QUIZ_QTYPES[$question->qtype]->get_question_options($question);

    quiz_restore_state($question, $state);
    $state->last_graded = $state;

    $options = quiz_get_reviewoptions($quiz, $attempt, $isteacher);
    $options->validation = ($state->event == QUIZ_EVENTVALIDATE);
    $options->history = 'all';

    quiz_print_quiz_question($question, $state, $number, $quiz, $options);

    print_footer();

?>
