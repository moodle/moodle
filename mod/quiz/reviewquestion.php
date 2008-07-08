<?php  // $Id$
/**
 * This page prints a review of a particular question attempt
 *
 * @author Martin Dougiamas and many others.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */

    require_once(dirname(__FILE__) . '/../../config.php');
    require_once('locallib.php');

    // Either stateid or (attemptid AND questionid) must be given
    $stateid = optional_param('state', 0, PARAM_INT); // state id
    $attemptid = optional_param('attempt', 0, PARAM_INT); // attempt id
    $questionid = optional_param('question', 0, PARAM_INT); // attempt id
    $number = optional_param('number', 0, PARAM_INT);  // question number

    if ($stateid) {
        if (! $state = $DB->get_record('question_states', array('id' => $stateid))) {
            print_error('invalidstateid', 'quiz');
        }
        if (! $attempt = $DB->get_record('quiz_attempts', array('uniqueid' => $state->attempt))) {
            print_error('invalidattemptid', 'quiz');
        }
    } elseif ($attemptid) {
        if (! $attempt = $DB->get_record('quiz_attempts', array('id' => $attemptid))) {
            print_error('invalidattemptid', 'quiz');
        }
        if (! $neweststateid = $DB->get_field('question_sessions', 'newest', array('attemptid' => $attempt->uniqueid, 'questionid' => $questionid))) {
            // newest_state not set, probably because this is an old attempt from the old quiz module code
            if (! $state = $DB->get_record('question_states', array('question' => $questionid, 'attempt' => $attempt->uniqueid))) {
                print_error('invalidquestionid', 'quiz');
            }
        } else {
            if (! $state = $DB->get_record('question_states', array('id' => $neweststateid))) {
                print_error('invalidstateid', 'quiz');
            }
        }
    } else {
        print_error('missingparameter');
    }
    if (! $question = $DB->get_record('question', array('id' => $state->question))) {
        print_error('questionmissing', 'quiz');
    }
    if (! $quiz = $DB->get_record('quiz', array('id' => $attempt->quiz))) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record('course', array('id' => $quiz->course))) {
        print_error('coursemisconf');
    }
    if (! $cm = get_coursemodule_from_instance('quiz', $quiz->id, $course->id)) {
        print_error('invalidcoursemodule');
    }

    require_login($course->id, false, $cm);
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    if (!has_capability('mod/quiz:viewreports', $context)) {
        if (!$attempt->timefinish) {
            redirect('attempt.php?q='.$quiz->id);
        }
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
            print_error('notyourattempt', 'quiz');
        }
    }

    //add_to_log($course->id, 'quiz', 'review', "review.php?id=$cm->id&amp;attempt=$attempt->id", "$quiz->id", "$cm->id");

/// Print the page header

    $strquizzes = get_string('modulenameplural', 'quiz');

    print_header();

    echo '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>'; // for overlib

/// Print heading
    print_heading(format_string($question->name));

    $question->maxgrade = $DB->get_field('quiz_question_instances', 'grade', array('quiz' => $quiz->id, 'question' => $question->id));
    // Some of the questions code is optimised to work with several questions
    // at once so it wants the question to be in an array.
    $key = $question->id;
    $questions[$key] = &$question;
    // Add additional questiontype specific information to the question objects.
    if (!get_question_options($questions)) {
        print_error('cannotloadtypeinfo', 'quiz');
    }

    $session = $DB->get_record('question_sessions', array('attemptid' => $attempt->uniqueid, 'questionid' => $question->id));
    $state->sumpenalty = $session->sumpenalty;
    $state->manualcomment = $session->manualcomment;
    restore_question_state($question, $state);
    $state->last_graded = $state;

    $options = quiz_get_reviewoptions($quiz, $attempt, $context);

/// Print infobox
    $table->align  = array("right", "left");
    if ($attempt->userid <> $USER->id) {
        // Print user picture and name
        $student = $DB->get_record('user', array('id' => $attempt->userid));
        $picture = print_user_picture($student, $course->id, $student->picture, false, true);
        $table->data[] = array($picture, fullname($student, true));
    }
    // print quiz name
    $table->data[] = array(get_string('modulename', 'quiz').':', format_string($quiz->name));
    if (has_capability('mod/quiz:viewreports', $context) and
            count($attempts = $DB->get_records_select('quiz_attempts', "quiz = ? AND userid =?", array($quiz->id, $attempt->userid), 'attempt ASC')) > 1) {
        // print list of attempts
        $attemptlist = '';
        foreach ($attempts as $at) {
            $attemptlist .= ($at->id == $attempt->id)
                ? '<b>'.$at->attempt.'</b>, '
                : '<a href="reviewquestion.php?attempt='.$at->id.'&amp;question='.$question->id.'&amp;number='.$number.'">'.$at->attempt.'</a>, ';
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
