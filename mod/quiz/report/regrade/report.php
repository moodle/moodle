<?PHP  // $Id$

/// Overview report just displays a big table of all the attempts

class quiz_report extends quiz_default_report {

    function display($quiz, $cm, $course) {     /// This function just displays the report

        global $CFG;

        if (!$attempts = get_records("quiz_attempts", "quiz", $quiz->id)) {
            print_header(get_string("noattempts", "quiz"));
            print_continue("report.php?id=$cm->id");
            print_footer($course);
            exit;
        }

        $users = array();
        $count->attempt = 0;
        $count->changed = 0;

        foreach ($attempts as $attempt) {

            set_time_limit(120);

            if (!$attempt->timefinish) {  // Skip incomplete attempts
                continue;
            }

            $count->attempt++;

            if (! $questions = quiz_get_attempt_responses($attempt, $quiz)) {
                error("Could not reconstruct quiz results for attempt $attempt->id!");
            }
            quiz_remove_unwanted_questions($questions, $quiz);

            if (!$result = quiz_grade_attempt_results($quiz, $questions)) {
                error("Could not re-grade this quiz attempt!");
            }

            if ($attempt->sumgrades != $result->sumgrades) {
                $attempt->sumgrades = $result->sumgrades;
                $count->changed++;

                if (! update_record("quiz_attempts", $attempt)) {
                    notify("Could not regrade attempt $attempt->id");
                }
            }

            $users[$attempt->userid] = $attempt->userid;
        }

        if ($users) {
            foreach ($users as $userid) {
                if (! quiz_save_best_grade($quiz, $userid)) {
                    notify("Could not save best grade for user $userid!");
                }
            }
        }   

        print_heading(get_string("regradecomplete", "quiz"));
        print_heading(get_string("regradecount", "quiz", $count));

        return true;

    }
}

?>
