<?PHP  // $Id$

// This page prints a particular instance of quiz

    require("../../config.php");
    require("lib.php");

    optional_variable($id);    // Course Module ID, or
    optional_variable($q);     // quiz ID

    optional_variable($attempt);     // A particular attempt ID

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
        error("Only teachers can see this page");
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
    $strtimetaken     = get_string("timetaken", "quiz");
    $strtimecompleted = get_string("timecompleted", "quiz");

    print_header("$course->shortname: $quiz->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strquizzes</A> 
                  -> <A HREF=\"view.php?id=$cm->id\">$quiz->name</A> -> $strreport", 
                 "", "", true);

    print_heading($quiz->name);

    if ($attempt) {  // Show a particular attempt

        if (! $attempt = get_record("quiz_attempts", "id", $attempt)) {
            error("No such attempt ID exists");
        }

        if (! $questions = quiz_get_attempt_responses($attempt)) {
            error("Could not reconstruct quiz results for attempt $attempt->id!");
        }

        if (!$result = quiz_grade_attempt_results($quiz, $questions)) {
            error("Could not re-grade this quiz attempt!");
        }

        $table->align  = array("RIGHT", "LEFT");
        $table->data[] = array("$strtimetaken:", format_time($attempt->timefinish - $attempt->timestart));
        $table->data[] = array("$strtimecompleted:", userdate($attempt->timefinish));
        $table->data[] = array("$strscore:", "$result->sumgrades/$quiz->sumgrades ($result->percentage %)");
        $table->data[] = array("$strgrade:", "$result->grade/$quiz->grade");
        print_table($table);

        print_continue("report.php?q=$quiz->id");

        $quiz->feedback = true;
        $quiz->correctanswers = true;
        quiz_print_quiz_questions($quiz, $result);

        print_continue("report.php?q=$quiz->id");
        print_footer($course);
        exit;
    }

    if (!$grades = quiz_get_grade_records($quiz)) {
        print_footer($course);
        exit;
    }

    $table->head = array("", $strname, $strattempts, $strgrade);
    $table->align = array("CENTER", "LEFT", "LEFT", "RIGHT");
    $table->width = array(10, "*", "*", 20);

    foreach ($grades as $grade) {
        $picture = print_user_picture($grade->user, $course->id, $grade->picture, false, true);

        if ($attempts = quiz_get_user_attempts($quiz->id, $grade->user)) {
            $userattempts = quiz_get_user_attempts_string($quiz, $attempts, $grade->grade);
        }

        $table->data[] = array ($picture, 
                                "<A HREF=\"$CFG->wwwroot/user/view.php?id=$grade->user&course=$course->id\">$grade->firstname $grade->lastname</A>", 
                                "$userattempts", round($grade->grade,0));
    }

    print_table($table);

// Finish the page
    print_footer($course);

?>
