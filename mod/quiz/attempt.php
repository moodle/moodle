<?PHP  // $Id$

// This page prints a particular instance of quiz

    require_once("../../config.php");
    require_once("lib.php");

    optional_variable($id);    // Course Module ID, or
    optional_variable($q);     // quiz ID

    if ($id) {
        if (! $cm = get_record("course_modules", "id", $id)) {
            error("Course Module ID was incorrect");
        }
    
        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }
    
        if (! $quiz = get_record("quiz", "id", $cm->instance)) {
            error("Course module is incorrect");
        }

    } else {
        if (! $quiz = get_record("quiz", "id", $q)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $quiz->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("quiz", $quiz->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    }

    require_login($course->id);


/// Set number for next attempt:

    if ($attempts = quiz_get_user_attempts($quiz->id, $USER->id)) {
        $attemptnumber = 2;
        foreach ($attempts as $attempt) {
            if ($attempt->attempt >= $attemptnumber) {
                $attemptnumber = $attempt->attempt + 1;
            }
        }
    } else {
        $attemptnumber = 1;
    }

    $strattemptnum = get_string("attempt", "quiz", $attemptnumber);


// Print the page header

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strquizzes = get_string("modulenameplural", "quiz");
    $strquiz  = get_string("modulename", "quiz");

    print_header("$course->shortname: $quiz->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strquizzes</A> -> 
                  <A HREF=\"view.php?id=$cm->id\">$quiz->name</A> -> $strattemptnum", 
                  "", "", true);

    echo '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>'; // for overlib

/// Check availability

    if ($quiz->attempts and $attempts and count($attempts) >= $quiz->attempts) {
        error("Sorry, you've had $quiz->attempts attempts already.", "view.php?id=$cm->id");
    }

    $timenow = time();
    $available = ($quiz->timeopen < $timenow and $timenow < $quiz->timeclose);

/// Check to see if they are submitting answers
    if ($rawanswers = data_submitted()) {

        $rawanswers = (array)$rawanswers;

        $shuffleorder = NULL;

        unset($rawanswers["q"]);  // quiz id
        if (! count($rawanswers)) {
            print_heading(get_string("noanswers", "quiz"));
            print_continue("attempt.php?q=$quiz->id");
            exit;
        }   

        if (!$questions = get_records_list("quiz_questions", "id", $quiz->questions)) {
            error("No questions found!");
        }

        foreach ($rawanswers as $key => $value) {       // Parse input for question -> answers

            if (ereg('^q([0-9]+)$', $key, $keyregs)) { // It's a real question number, not a coded one
                $questions[$keyregs[1]]->answer[] = trim($value);

            } else if (ereg('^q([0-9]+)rq([0-9]+)$', $key, $keyregs)) { // Random Question information
                $questions[$keyregs[1]]->random = $keyregs[2];

            } else if (ereg('^q([0-9]+)a([0-9]+)$', $key, $keyregs)) { // Checkbox style multiple answers
                $questions[$keyregs[1]]->answer[] = $keyregs[2];

            } else if (ereg('^q([0-9]+)r([0-9]+)$', $key, $keyregs)) { // Random-style answers
                $questions[$keyregs[1]]->answer[] = "$keyregs[2]-$value";
        
            } else if (ereg('^q([0-9]+)ma([0-9]+)$', $key, $keyregs)) { // Multi-answer questions
                $questions[$keyregs[1]]->answer[] = "$keyregs[2]-$value";

            } else if ('shuffleorder' == $key) {
                $shuffleorder = explode(",", $value);   // Actual order questions were given in
            
            } else {  // Useful for debugging new question types.  Must be last.
                error("Answer received for non-existent question ($key -> $value)");
            }
        }

        if (!$result = quiz_grade_attempt_results($quiz, $questions)) {
            error("Could not grade your quiz attempt!");
        }

        if ($attempt = quiz_save_attempt($quiz, $questions, $result, $attemptnumber)) {
            add_to_log($course->id, "quiz", "submit", 
                       "review.php?id=$cm->id&attempt=$attempt->id", "$quiz->id", $cm->id);
        } else {
            notice(get_string("alreadysubmitted", "quiz"), "view.php?id=$cm->id");
            print_footer($course);
            exit;
        }

        if (! quiz_save_best_grade($quiz, $USER->id)) {
            error("Sorry! Could not calculate your best grade!");
        }

        $strgrade = get_string("grade");
        $strscore = get_string("score", "quiz");

        if ($quiz->grade) {
            print_heading("$strscore: $result->sumgrades/$quiz->sumgrades ($result->percentage %)");
            print_heading("$strgrade: $result->grade/$quiz->grade");
        }

        print_continue("view.php?id=$cm->id");

        if ($quiz->feedback) {
            $quiz->shuffleanswers = false;       // Never shuffle answers in feedback
            quiz_print_quiz_questions($quiz, $result, $questions, $shuffleorder);
            print_continue("view.php?id=$cm->id");
        }

        print_footer($course);

        exit;
    }


/// Print the quiz page

    if (isguest()) {
        print_heading(get_string("guestsno", "quiz"));
        print_footer($course);
        exit;
    }

/// Actually seeing the questions marks the start of an attempt
 
    if (!$unfinished = quiz_get_user_attempt_unfinished($quiz->id, $USER->id)) {
        if ($newattemptid = quiz_start_attempt($quiz->id, $USER->id, $attemptnumber)) {
            add_to_log($course->id, "quiz", "attempt", 
                       "review.php?id=$cm->id&attempt=$newattemptid", "$quiz->id", $cm->id);
        } else {
            error("Sorry! Could not start the quiz (could not save starting time)");
        }
    }

/// First print the headings and so on

    print_heading($quiz->name);

    if (!$available) {
        error("Sorry, this quiz is not available", "view.php?id=$cm->id");
    }

    print_heading(get_string("attempt", "quiz", $attemptnumber));
    print_simple_box(format_text($quiz->intro), "CENTER");


/// Add the javascript timer in the title bar if the closing time appears close

    $secondsleft = $quiz->timeclose - time();
    if ($secondsleft > 0 and $secondsleft < 24*3600) {  // less than a day remaining
        include("jsclock.php");
    }


/// Print all the questions

    echo "<br />";

    $result = NULL;     // Default
    $questions = NULL;  // Default
    if ($quiz->attemptonlast && !empty($attempts)) {
        $latestfinishedattempt->attempt = 0;
        foreach ($attempts as $attempt) {
            if ($attempt->timefinish
                && $attempt->attempt > $latestfinishedattempt->attempt)
            {
                $latestfinishedattempt = $attempt;
            }
        }
        if ($latestfinishedattempt->attempt > 0
            and $questions =
                    quiz_get_attempt_responses($latestfinishedattempt))
        {
            // An previous attempt to continue on is found:
            quiz_remove_unwanted_questions($questions, $quiz); // In case the quiz has been changed

            if (!($result = quiz_grade_attempt_results($quiz, $questions))) {
                // No results, reset to defaults:
                $questions = NULL;
                $result = NULL;

            } else {
                // We're on, latest attempt responses are to be included.
                // In order to have this accomplished by
                // the method quiz_print_quiz_questions we need to
                // temporarilly change some of the $quiz attributes
                // and remove some of the information from result.

                $quiz->correctanswers = false; // Not a good idea to show them, huh?
                $result->feedback = array(); // Not to be printed
                $result->attemptbuildsonthelast = true;
            }
            
        } else {
            // No latest attempt, or latest attempt was empty - Reset to defaults
            $questions = NULL;
        }
    }
    if (! quiz_print_quiz_questions($quiz, $result, $questions)) {
        print_continue("view.php?id=$cm->id");
    }


/// Finish the page
    print_footer($course);

?>
