<?PHP  // $Id$

// This page prints a review of a particular quiz attempt

    require_once("../../config.php");
    require_once("lib.php");

    optional_variable($id);    // Course Module ID, or
    optional_variable($q);     // quiz ID

    require_variable($attempt);    // A particular attempt ID for review

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

    if (! $attempt = get_record("quiz_attempts", "id", $attempt)) {
        error("No such attempt ID exists");
    }


    require_login($course->id);

    if (!isteacher($course->id)) {
        if (!$quiz->review) {
            error(get_string("noreview", "quiz"));
        }
        if (time() < $quiz->timeclose) {
            error(get_string("noreviewuntil", "quiz", userdate($quiz->timeclose)));
        }
        if ($attempt->userid != $USER->id) {
            error("This is not your attempt!");
        }
    }

    add_to_log($course->id, "quiz", "review", "review.php?id=$cm->id&attempt=$attempt->id", "$quiz->id", "$cm->id");


// Print the page header

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strquizzes = get_string("modulenameplural", "quiz");
    $strquiz  = get_string("modulename", "quiz");
    $strreport  = get_string("report", "quiz");
    $strreview  = get_string("review", "quiz");
    $strname  = get_string("name");
    $strattempts  = get_string("attempts", "quiz");
    $strscore  = get_string("score", "quiz");
    $strgrade  = get_string("grade");
    $strbestgrade  = get_string("bestgrade", "quiz");
    $strtimetaken     = get_string("timetaken", "quiz");
    $strtimecompleted = get_string("timecompleted", "quiz");

    print_header("$course->shortname: $quiz->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strquizzes</A> 
                  -> <a href=\"view.php?id=$cm->id\">$quiz->name</a> -> $strreview", 
                 "", "", true);

    echo '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>'; // for overlib

    print_heading($quiz->name);


    if (! $questions = quiz_get_attempt_responses($attempt)) {
        if ($user = get_record("user", "id", $attempt->userid)) {
            $fullname = fullname($user);
        } else {
            $fullname = "????";
        }
        print_heading(get_string("attemptincomplete", "quiz", $fullname));
        print_footer($course);
        exit;
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

    $table->align  = array("right", "left");
    $table->data[] = array("$strtimetaken:", $timetaken);
    $table->data[] = array("$strtimecompleted:", userdate($attempt->timefinish));
    if ($quiz->grade) {
        $table->data[] = array("$strscore:", "$result->sumgrades/$quiz->sumgrades ($result->percentage %)");
        $table->data[] = array("$strgrade:", "$result->grade/$quiz->grade");
    }

    print_table($table);

    if (isteacher($course->id)) {
        print_continue("report.php?q=$quiz->id");
    } else {
        print_continue("view.php?q=$quiz->id");
    }

    $quiz->feedback = true;
    $quiz->correctanswers = true;
    $quiz->shuffleanswers = false;
    $quiz->shufflequestions = false;
    quiz_print_quiz_questions($quiz, $result, $questions);

    if (isteacher($course->id)) {
        print_continue("report.php?q=$quiz->id");
    } else {
        print_continue("view.php?q=$quiz->id");
    }

    print_footer($course);

?>
