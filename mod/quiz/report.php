<?PHP  // $Id$

// This page prints a particular instance of quiz

    require_once("../../config.php");
    require_once("lib.php");

    optional_variable($id);    // Course Module ID, or
    optional_variable($q);     // quiz ID

    optional_variable($attempt);     // A particular attempt ID
    optional_variable($review);      // A particular attempt ID for review by student
    optional_variable($regrade);     // Regrade all attempts

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


    if (!isteacher($course->id)) {
        if (!$quiz->review) {
            error(get_string("noreview", "quiz"));
        }
        if (time() < $quiz->timeclose) {
            error(get_string("noreviewuntil", "quiz", userdate($quiz->timeclose)));
        }
        if (empty($review)) {
            error("You are using this script wrongly.");
        }
    }

    if (!empty($review)) {
        $attempt = $review;
    }

    add_to_log($course->id, "quiz", "report", "report.php?id=$cm->id", "$quiz->id");

// Print the page header

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strquizzes = get_string("modulenameplural", "quiz");
    $strquiz  = get_string("modulename", "quiz");
    $strreport  = get_string("report", "quiz");
    $strname  = get_string("name");
    $strattempts  = get_string("attempts", "quiz");
    $strscore  = get_string("score", "quiz");
    $strgrade  = get_string("grade");
    $strbestgrade  = get_string("bestgrade", "quiz");
    $strtimetaken     = get_string("timetaken", "quiz");
    $strtimecompleted = get_string("timecompleted", "quiz");

    print_header("$course->shortname: $quiz->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strquizzes</A> 
                  -> <A HREF=\"view.php?id=$cm->id\">$quiz->name</A> -> $strreport", 
                 "", "", true);

    print_heading($quiz->name);


    if (!empty($attempt)) {  // Show a particular attempt

        if (! $attempt = get_record("quiz_attempts", "id", $attempt)) {
            error("No such attempt ID exists");
        }

        if (!isteacher($course->id)) {
            if ($attempt->userid != $USER->id) {
                error("This is not your attempt!");
            }
        }

        if (! $questions = quiz_get_attempt_responses($attempt, $quiz)) {
            error("Could not reconstruct quiz results for attempt $attempt->id!");
        }

        quiz_remove_unwanted_questions($questions, $quiz);

        if (!$result = quiz_grade_attempt_results($quiz, $questions)) {
            error("Could not re-grade this quiz attempt!");
        }

        if ($timetaken = ($attempt->timefinish - $attempt->timestart)) {
            $timetaken = format_time($timetaken);
        } else {
            $timetaken = "-";
        }

        $table->align  = array("RIGHT", "LEFT");
        $table->data[] = array("$strtimetaken:", $timetaken);
        $table->data[] = array("$strtimecompleted:", userdate($attempt->timefinish));
        $table->data[] = array("$strscore:", "$result->sumgrades/$quiz->sumgrades ($result->percentage %)");
        $table->data[] = array("$strgrade:", "$result->grade/$quiz->grade");
        print_table($table);

        if (empty($review)) {
            print_continue("report.php?q=$quiz->id");
        } else {
            print_continue("view.php?q=$quiz->id");
        }

        $quiz->feedback = true;
        $quiz->correctanswers = true;
        quiz_print_quiz_questions($quiz, $result, $questions);

        if (empty($review)) {
            print_continue("report.php?q=$quiz->id");
        } else {
            print_continue("view.php?q=$quiz->id");
        }
        print_footer($course);
        exit;
    }

    if (!empty($regrade)) {
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
        print_continue("report.php?id=$cm->id");
        print_footer($course);
        exit;
    }

    if (!$grades = quiz_get_grade_records($quiz)) {
        print_footer($course);
        exit;
    }

    $table->head = array("&nbsp;", $strname, $strattempts, "$strbestgrade /$quiz->grade");
    $table->align = array("CENTER", "LEFT", "LEFT", "CENTER");
    $table->width = array(10, "*", "*", 20);

    foreach ($grades as $grade) {
        $picture = print_user_picture($grade->userid, $course->id, $grade->picture, false, true);

        if ($attempts = quiz_get_user_attempts($quiz->id, $grade->userid)) {
            $userattempts = quiz_get_user_attempts_string($quiz, $attempts, $grade->grade);
        }

        $table->data[] = array ($picture, 
                                "<A HREF=\"$CFG->wwwroot/user/view.php?id=$grade->userid&course=$course->id\">$grade->firstname $grade->lastname</A>", 
                                "$userattempts", round($grade->grade,0));
    }

    print_table($table);

    echo "<CENTER><P>";
    $options["regrade"] = "true";
    $options["id"] = $cm->id;
    print_single_button("report.php", $options, get_string("regrade", "quiz"));
    echo "</P></CENTER>";

// Finish the page
    print_footer($course);


function quiz_remove_unwanted_questions(&$questions, $quiz) {

    $quizquestions = array();
    $quizids = explode(",", $quiz->questions);
    foreach ($quizids as $quizid) {
        $quizquestions[$quizid] = true;
    }
    foreach ($questions as $key => $question) {
        if (!isset($quizquestions[$question->id])) {
            unset($questions[$key]);
        }
    }
}




?>
