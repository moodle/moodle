<?PHP  // $Id$

// This page prints a particular instance of quiz

    require("../../config.php");
    require("lib.php");

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

    if ($course->format == "weeks" and $quiz->days) {
        $timenow = time();
        $timestart = $course->startdate + (($cw->section - 1) * 608400);
        $timefinish = $timestart + (3600 * 24 * $quiz->days);
        $available = ($timestart < $timenow and $timenow < $timefinish);
    } else {
        $available = true;
    }

/// Check to see if they are submitting answers
    if (match_referer() && isset($HTTP_POST_VARS)) {
        add_to_log($course->id, "quiz", "submit", "attempt.php?id=$cm->id", "$quiz->id");

        $rawanswers = $HTTP_POST_VARS;
        unset($rawanswers["q"]);  // quiz id
        if (! count($rawanswers)) {
            print_heading(get_string("noanswers", "quiz"));
            print_continue("attempt.php?q=$quiz->id");
            exit;
        }   

        if (!$questions = get_records_list("quiz_questions", "id", $quiz->questions)) {
            error("No questions found!");
        }

        foreach ($rawanswers as $key => $value) {    // Parse input for question -> answers
            if (substr($key, 0, 1) == "q") {
                $key = substr($key,1);
                if (!isset($questions[$key])) {
                    if (substr_count($key, "a")) {   // checkbox style multiple answers
                        $check = explode("a", $key);
                        $key   = $check[0];
                        $value = $check[1];
                    } else {
                        error("Answer received for non-existent question ($key)!");
                    }
                }
                $questions[$key]->answer[] = $value;  // Store answers in array
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
            quiz_print_quiz_questions($quiz, $result);
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
