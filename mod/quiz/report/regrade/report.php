<?php  // $Id$

// This script regrades all attempts at this quiz
require_once($CFG->libdir.'/tablelib.php');

class quiz_report extends quiz_default_report {

    function display($quiz, $cm, $course) {
        global $CFG;

        // Print header
        $this->print_header_and_tabs($cm, $course, $quiz, $reportmode="regrade");

        // Check permissions
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        if (!has_capability('mod/quiz:grade', $context)) {
            notify(get_string('regradenotallowed', 'quiz'));
            return true;
        }

        // Fetch all attempts
        if (!$attempts = get_records_select('quiz_attempts', "quiz = '$quiz->id' AND preview = 0")) {
            print_heading(get_string('noattempts', 'quiz'));
            return true;
        }

        // Fetch all questions
        $sql = "SELECT q.*, i.grade AS maxgrade FROM {$CFG->prefix}question q,
                                         {$CFG->prefix}quiz_question_instances i
                WHERE i.quiz = $quiz->id
                AND i.question = q.id";

        if (! $questions = get_records_sql($sql)) {
            error("Failed to get questions for regrading!");
        }
        get_question_options($questions);

        // Print heading
        print_heading(get_string('regradingquiz', 'quiz', format_string($quiz->name)));
        echo '<div class="boxaligncenter">';
        print_string('regradedisplayexplanation', 'quiz');
        echo '</div>';

        // Loop through all questions and all attempts and regrade while printing progress info
        foreach ($questions as $question) {
            echo '<strong>'.get_string('regradingquestion', 'quiz', $question->name).'</strong> '.get_string('attempts', 'quiz').": \n";
            foreach ($attempts as $attempt) {
                set_time_limit(30);
                $changed = regrade_question_in_attempt($question, $attempt, $quiz, true);
                if ($changed) {
                    link_to_popup_window ('/mod/quiz/reviewquestion.php?attempt='.$attempt->id.'&amp;question='.$question->id,
                     'reviewquestion', ' #'.$attempt->id, 450, 550, get_string('reviewresponse', 'quiz'));
                } else {
                    echo ' #'.$attempt->id;
                }
            }
            echo '<br />';
            // the following makes sure that the output is sent immediately.
            @flush();@ob_flush();
        }

        // Loop through all questions and recalculate $attempt->sumgrade
        $attemptschanged = 0;
        foreach ($attempts as $attempt) {
            $sumgrades = 0;
            $questionids = explode(',', quiz_questions_in_quiz($attempt->layout));
            foreach($questionids as $questionid) {
                $lastgradedid = get_field('question_sessions', 'newgraded', 'attemptid', $attempt->uniqueid, 'questionid', $questionid);
                $sumgrades += get_field('question_states', 'grade', 'id', $lastgradedid);
            }
            if ($attempt->sumgrades != $sumgrades) {
                $attemptschanged++;
                set_field('quiz_attempts', 'sumgrades', $sumgrades, 'id', $attempt->id);
            }
        }

        // Update the overall quiz grades
        if ($grades = get_records('quiz_grades', 'quiz', $quiz->id)) {
            foreach($grades as $grade) {
                quiz_save_best_grade($quiz, $grade->userid);
            }
        }

        return true;
    }
}

?>
