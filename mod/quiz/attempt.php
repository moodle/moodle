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


/// Check number of attempts

    if ($attempts = quiz_get_user_attempts($quiz->id, $USER->id)) {
        $numattempts = count($attempts) + 1;
    } else {
        $numattempts = 1;
    }

    $strattemptnum = get_string("attempt", "quiz", $numattempts);


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

/// Check availability

    if ($quiz->attempts) {
        if ($numattempts > $quiz->attempts) {
            error("Sorry, you've had $quiz->attempts attempts already.", "view.php?id=$cm->id");
        }
    }

    $timenow = time();
    $available = ($quiz->timeopen < $timenow and $timenow < $quiz->timeclose);

/// Check to see if they are submitting answers
    if ($rawanswers = data_submitted()) {
        add_to_log($course->id, "quiz", "submit", "attempt.php?id=$cm->id", "$quiz->id");

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
            if (substr($key, 0, 1) == "q") {
                $key = substr($key,1);
                if (isset($questions[$key])) {          // It's a real question number, not a coded one
                    $questions[$key]->answer[] = trim($value);

                } else if (substr_count($key, "rq")) {  // Random Question information
                    $check = explode("rq", $key);
                    $key   = $check[0];                 // The random question id
                    $real  = $check[1];                 // The real question id
                    $questions[$key]->random = $real;  

                } else if (substr_count($key, "a")) {   // Checkbox style multiple answers
                    $check = explode("a", $key);
                    $key   = $check[0];                 // The main question number
                    $value = $check[1];                 // The actual answer
                    $questions[$key]->answer[] = trim($value);  

                } else if (substr_count($key, "r")) {   // Random-style answers
                    $check = explode("r", $key);
                    $key   = $check[0];                 // The main question
                    $rand  = $check[1];                 // The random sub-question
                    $questions[$key]->answer[] = "$rand-$value";

                } else {
                    error("Answer received for non-existent question ($key)!");
                }
            } else if ($key == "shuffleorder") {
                $shuffleorder = explode(",", $value);   // Actual order questions were given in
            }
        }

        if (!$result = quiz_grade_attempt_results($quiz, $questions)) {
            error("Could not grade your quiz attempt!");
        }

        if (! $attempt = quiz_save_attempt($quiz, $questions, $result, $numattempts)) {
            notice(get_string("alreadysubmitted", "quiz"), "view.php?id=$cm->id");
            print_footer($course);
            exit;
        }

        if (! quiz_save_best_grade($quiz, $USER->id)) {
            error("Sorry! Could not calculate your best grade!");
        }

        $strgrade = get_string("grade");
        $strscore = get_string("score", "quiz");

        print_heading("$strscore: $result->sumgrades/$quiz->sumgrades ($result->percentage %)");
        print_heading("$strgrade: $result->grade/$quiz->grade");

        print_continue("view.php?id=$cm->id");

        if ($quiz->feedback) {
            $quiz->shuffleanswers = false;       // Never shuffle answers in feedback
            quiz_print_quiz_questions($quiz, $result, $questions, $shuffleorder);
            print_continue("view.php?id=$cm->id");
        }

        print_footer($course);

        exit;
    }

    add_to_log($course->id, "quiz", "attempt", "attempt.php?id=$cm->id", "$quiz->id");

/// Print the quiz page

    if (isguest()) {
        print_heading(get_string("guestsno", "quiz"));
        print_footer($course);
        exit;
    }

/// Actually seeing the questions marks the start of an attempt
 
    if (!$unfinished = quiz_get_user_attempt_unfinished($quiz->id, $USER->id)) {
        if (! quiz_start_attempt($quiz->id, $USER->id, $numattempts)) {
            error("Sorry! Could not start the quiz (could not save starting time)");
        }
    }

/// First print the headings and so on

    print_heading($quiz->name);

    if (!$available) {
        error("Sorry, this quiz is not available", "view.php?id=$cm->id");
    }

    print_heading(get_string("attempt", "quiz", $numattempts));

    print_simple_box(text_to_html($quiz->intro), "CENTER");


/// Print all the questions

    echo "<BR>";

    if (! quiz_print_quiz_questions($quiz)) {
        print_continue("view.php?id=$cm->id");
    }


/// Finish the page
    print_footer($course);

?>
