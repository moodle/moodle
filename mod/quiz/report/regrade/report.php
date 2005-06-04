<?php  // $Id$

// This script regrades all attempts at this quiz

    require_once($CFG->libdir.'/tablelib.php');

class quiz_report extends quiz_default_report {

    function display($quiz, $cm, $course) {     /// This function just displays the report
        global $CFG, $SESSION, $db, $QUIZ_QTYPES;

        $strheading    = get_string('regradequiz', 'quiz', $quiz);
        $strnoattempts = get_string('noattempts');
        // 'There are no attempts for this quiz that could be regraded.';

    /// Print header
        $this->print_header_and_tabs($cm, $course, $quiz, $reportmode="regrade");

        if (!$attempts = get_records('quiz_attempts', 'quiz', $quiz->id)) {
            notify($strnoattempts);
            return true;
        }

        $sql = "SELECT q.*, i.grade AS maxgrade FROM {$CFG->prefix}quiz_questions q,
                                         {$CFG->prefix}quiz_question_instances i
                WHERE i.quiz = $quiz->id
                AND i.question = q.id";

        if (! $questions = get_records_sql($sql)) {
            error("Failed to get questions for regrading!");
        }
        quiz_get_question_options($questions);


        print_heading($strheading);
        print_simple_box_start('center', '70%');
        foreach ($attempts as $attempt) {
            foreach ($questions as $question) {
                quiz_regrade_question_in_attempt($question, $attempt, $quiz, true);
            }
        }
        print_simple_box_end();

        return true;
    }
}

?>