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

    $strquizzes = get_string("modulenameplural", "quiz");
    $strquiz  = get_string("modulename", "quiz");

    print_header_simple("$quiz->name", "",
                 "<A HREF=index.php?id=$course->id>$strquizzes</A> ->
                  <A HREF=\"view.php?id=$cm->id\">$quiz->name</A> -> $strattemptnum",
                  "", "", true);

    echo '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>'; // for overlib

/// Check availability

    if ($quiz->attempts and $attempts and count($attempts) >= $quiz->attempts) {
        error("Sorry, you've had $quiz->attempts attempts already.", "view.php?id=$cm->id");
    }

/// Check subnet access
    if ($quiz->subnet and !address_in_subnet(getremoteaddr(), $quiz->subnet)) {
        error(get_string("subneterror", "quiz"), "view.php?id=$cm->id");
    }

/// Check password access
    if ($quiz->password and empty($_POST['q'])) {
        if (empty($_POST['quizpassword'])) {
    
            print_heading($quiz->name);
            print_heading(get_string("attempt", "quiz", $attemptnumber));
            if (trim(strip_tags($quiz->intro))) {
                print_simple_box(format_text($quiz->intro), "CENTER");
            }
            echo "<br />\n";
        
            echo "<form name=\"passwordform\" method=\"post\" action=\"attempt.php?id=$cm->id\">\n";
            print_simple_box_start("center");
            
            echo "<div align=\"center\">\n";
            print_string("requirepasswordmessage", "quiz");
            echo "<br /><br />\n";
            echo " <input name=\"quizpassword\" type=\"password\" value=\"\">";
            echo " <input type=\"submit\" value=\"".get_string("ok")."\">\n";
            echo "</div>\n";

            print_simple_box_end();
            echo "</form>\n";
            
            print_footer();
            exit;

        } else {
            if (strcmp($quiz->password, $_POST['quizpassword']) !== 0) {
                error(get_string("passworderror", "quiz"), "view.php?id=$cm->id");
            }
            unset($_POST['quizpassword']); /// needed so as not to confuse later code dealing with submitted answers
        }
    }
    

/// BEGIN EDIT Get time limit if any.

    $timelimit = $quiz->timelimit * 60;

    $unattempt = quiz_get_user_attempt_unfinished($quiz->id, $USER->id);
    if($timelimit > 0) {
        $timestart = $unattempt->timestart;
        if($timestart) {
            $timesincestart = time() - $timestart;
            $timerstartvalue = $timelimit - $timesincestart;
        } else {
            $timerstartvalue = $timelimit;
        }
    }

    if($timelimit and $timerstartvalue <= 0) {
        $timerstartvalue = 1;
    }
/// END EDIT
    $timenow = time();
    $available = ($quiz->timeopen < $timenow and $timenow < $quiz->timeclose);

/// Check to see if they are submitting answers
    if ($rawanswers = data_submitted()) {

        $rawanswers = (array)$rawanswers;

        $shuffleorder = NULL;

        unset($rawanswers["q"]);  // quiz id
        if (! count($rawanswers) and ! $timelimit) {
            print_heading(get_string("noanswers", "quiz"));
            print_continue("attempt.php?q=$quiz->id");
            exit;
        }

        if (!$questions = get_records_list("quiz_questions", "id", $quiz->questions)) {
            error("No questions found!");
        }

        foreach ($rawanswers as $key => $value) { // Parse input for question->response

            if ($postedquestionid = quiz_extract_posted_id($key)) {
                $questions[$postedquestionid]->response[$key] = trim($value);

            } else if ('shuffleorder' == $key) {
                $shuffleorder = explode(",", $value);   // Actual order questions were given in

            } else {  // Useful for debugging new question types.  Must be last.
                error("Unrecognizable input has been posted ($key -> $value)");
            }
        }

        if($timelimit > 0) {
            if(($timelimit + 60) <= $timesincestart) {
                $quiz->timesincestart = $timesincestart;
            }
        }

        /// Retrieve ->maxgrade for all questions
        If (!($grades = quiz_get_question_grades($quiz->id, $quiz->questions))) {
            $grades = array();
        }
        foreach ($grades as $qid => $grade) {
            $questions[$qid]->maxgrade = $grade->grade;
        }

        if (!$result = quiz_grade_responses($quiz, $questions)) {
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
            quiz_print_quiz_questions($quiz, $questions, $result, $shuffleorder);
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

    if (isset($unattempt) && $unattempt) {
        $attempt = $unattempt;

    } else if ($attempt = quiz_start_attempt($quiz->id, $USER->id, $attemptnumber)) {
        add_to_log($course->id, "quiz", "attempt", 
                "review.php?id=$cm->id&attempt=$attempt->id", "$quiz->id", $cm->id);
    } else {
        error("Sorry! Could not start the quiz (could not save starting time)");
    }

/// First print the headings and so on

    print_heading($quiz->name);

    if (!$available) {
        error("Sorry, this quiz is not available", "view.php?id=$cm->id");
    }

    print_heading(get_string("attempt", "quiz", $attemptnumber));
    if (trim(strip_tags($quiz->intro))) {
        print_simple_box(format_text($quiz->intro), "CENTER");
    }


/// Add the javascript timer in the title bar if the closing time appears close

    $secondsleft = $quiz->timeclose - time();
    if ($secondsleft > 0 and $secondsleft < 24*3600) {  // less than a day remaining
        include("jsclock.php");
    }


/// Print all the questions

    echo "<br />";

    $questions = quiz_get_attempt_questions($quiz, $attempt, true);
    if ($quiz->attemptonlast && $attemptnumber >= 2 and
            $quiz->attempts == 0 || !unattempt) {
        // There are unlimited attempts or it is a new attempt.
        // As the attempt also builds on the last, we can here
        // have the student see the scores of the pre-entered
        // responses that we here will have graded:
        $result = quiz_grade_responses($quiz, $questions);
        $result->attemptbuildsonthelast = true;
    } else {
        $result = NULL;
    }
    
    // We do not show feedback or correct answers during an attempt:
    $quiz->feedback = $quiz->correctanswers = false;
    
    if (!quiz_print_quiz_questions($quiz, $questions, $result)) {
        print_continue("view.php?id=$cm->id");
    }

/// If quiz is available and time limit is set include floating timer.

    if ($available and $timelimit > 0) {
        require('jstimer.php');
    }

    print_footer($course);

?>
