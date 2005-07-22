<?php  // $Id$

// This script regrades all attempts at this quiz

    require_once($CFG->libdir.'/tablelib.php');

class quiz_report extends quiz_default_report {

    function display($quiz, $cm, $course) {     /// This function just displays the report
        global $CFG, $SESSION, $db, $QUIZ_QTYPES;

    /// Print header
        $this->print_header_and_tabs($cm, $course, $quiz, $reportmode="regrade");

    /// Fetch all attempts
        if (!$attempts = get_records_select('quiz_attempts', "quiz = '$quiz->id' AND preview = 0")) {
            print_heading(get_string('noattempts', 'quiz'));
            return true;
        }

    /// Fetch all questions
        $sql = "SELECT q.*, i.grade AS maxgrade FROM {$CFG->prefix}quiz_questions q,
                                         {$CFG->prefix}quiz_question_instances i
                WHERE i.quiz = $quiz->id
                AND i.question = q.id";

        if (! $questions = get_records_sql($sql)) {
            error("Failed to get questions for regrading!");
        }
        quiz_get_question_options($questions);

    /// Print heading
        print_heading(get_string('regradingquiz', 'quiz', $quiz));
        echo '<center>';
        print_string('regradedisplayexplanation', 'quiz');
        echo '<center>';

    /// Loop through all questions and all attempts and regrade while printing progress info
        foreach ($questions as $question) {
            echo '<b>'.get_string('regradingquestion', 'quiz', $question->name).'</b> '.get_string('attempts', 'quiz').": \n";
            foreach ($attempts as $attempt) {
                set_time_limit(30);
                quiz_regrade_question_in_attempt($question, $attempt, $quiz, true);
            }
            echo '<br/ >';
            // the following makes sure that the output is sent immediately.
            flush();ob_flush();
        }

    /// Loop through all questions and recalculate $attempt->sumgrade
        $attemptschanged = 0;
        foreach ($attempts as $attempt) {
            $sumgrades = 0;
            $questionids = explode(',', quiz_questions_in_quiz($attempt->layout));
            foreach($questionids as $questionid) {
                $lastgradedid = get_field('quiz_newest_states', 'newgraded', 'attemptid', $attempt->uniqueid, 'questionid', $questionid);
                $sumgrades += get_field('quiz_states', 'grade', 'id', $lastgradedid);
            }
            if ($attempt->sumgrades != $sumgrades) {
                $attemptschanged++;
                set_field('quiz_attempts', 'sumgrades', $sumgrades, 'id', $attempt->id);
            }
        }

    /// Update the overall quiz grades
        if ($grades = get_records('quiz_grades', 'quiz', $quiz->id)) {
            foreach($grades as $grade) {
                quiz_save_best_grade($quiz, $grade->userid);
            }
        }

        return true;
    }
}

?>