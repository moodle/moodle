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

    add_to_log($course->id, "quiz", "view", "view.php?id=$cm->id", $quiz->id, $cm->id);

    $timenow = time();


// Print the page header

    if ($course->category) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
    }

    $strquizzes = get_string("modulenameplural", "quiz");
    $strquiz  = get_string("modulename", "quiz");

    print_header("$course->shortname: $quiz->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strquizzes</A> -> $quiz->name", 
                 "", "", true, update_module_button($cm->id, $course->id, $strquiz), navmenu($course, $cm));

    if (isteacher($course->id)) {
        $attemptcount = count_records_select("quiz_attempts", "quiz = '$quiz->id' AND timefinish > 0");
        $usercount = count_records("quiz_grades", "quiz", "$quiz->id");
        $strusers  = get_string("users");
        $strviewallanswers  = get_string("viewallanswers","quiz",$attemptcount);
        echo "<p align=right><a href=\"report.php?id=$cm->id\">$strviewallanswers ($usercount $strusers)</a></p>";
    } else if (!$cm->visible) {
        notice(get_string("activityiscurrentlyhidden"));
    }

    $available = ($quiz->timeopen < $timenow and $timenow < $quiz->timeclose);

// Print the main part of the page

    print_heading($quiz->name);

    print_simple_box(format_text($quiz->intro), "CENTER");


    if (isguest()) {
        print_heading(get_string("guestsno", "quiz"));
        print_footer($course);
        exit;
    }

    if ($attempts = quiz_get_user_attempts($quiz->id, $USER->id)) {
        $numattempts = count($attempts);
    } else {
        $numattempts = 0;
    }

    if ($quiz->attempts > 1) {
        echo "<p align=center>".get_string("attemptsallowed", "quiz").": $quiz->attempts</p>";
        echo "<p align=center>".get_string("grademethod", "quiz").": ".$QUIZ_GRADE_METHOD[$quiz->grademethod]."</p>";
    } else {
        echo "<br />";
    }

    $strattempt       = get_string("attempt", "quiz");
    $strtimetaken     = get_string("timetaken", "quiz");
    $strtimecompleted = get_string("timecompleted", "quiz");
    $strgrade         = get_string("grade");
    $strbestgrade     = $QUIZ_GRADE_METHOD[$quiz->grademethod];

    $mygrade = quiz_get_best_grade($quiz->id, $USER->id);

    if ($numattempts) { 
        if ($quiz->grade) {
            $table->head = array($strattempt, $strtimetaken, $strtimecompleted, "$strgrade / $quiz->grade");
            $table->align = array("center", "center", "left", "right");
            $table->size = array("", "", "", "");
        } else {  // No grades are being used
            $table->head = array($strattempt, $strtimetaken, $strtimecompleted);
            $table->align = array("center", "center", "left");
            $table->size = array("", "", "");
        }
        foreach ($attempts as $attempt) {
            if ($timetaken = ($attempt->timefinish - $attempt->timestart)) {
                $timetaken = format_time($timetaken);
            } else {
                $timetaken = "-";
            }
            if ($quiz->grade) {
                $attemptgrade = format_float(($attempt->sumgrades/$quiz->sumgrades)*$quiz->grade);
                if ($attemptgrade == $mygrade) {
                    $attemptgrade = "<span class=highlight>$attemptgrade</span>";
                }
                if (!$available and $quiz->review) {
                    $attemptgrade = "<a href=\"review.php?q=$quiz->id&attempt=$attempt->id\">$attemptgrade</a>";
                    $attempt->attempt = "<a href=\"review.php?q=$quiz->id&attempt=$attempt->id\">$attempt->attempt</a>";
                }
                $table->data[] = array( $attempt->attempt, 
                                        format_time($attempt->timefinish - $attempt->timestart),
                                        userdate($attempt->timefinish), 
                                        $attemptgrade);
            } else {  // No grades are being used
                if (!$available and $quiz->review) {
                    $attempt->attempt = "<a href=\"review.php?q=$quiz->id&attempt=$attempt->id\">$attempt->attempt</a>";
                }
                $table->data[] = array( $attempt->attempt, 
                                        format_time($attempt->timefinish - $attempt->timestart),
                                        userdate($attempt->timefinish));
            }
        }
        print_table($table);
    }

    if ($available) {
        echo "<p align=center>".get_string("quizavailable", "quiz", userdate($quiz->timeclose));
    } else if ($timenow < $quiz->timeopen) {
        echo "<p align=center>".get_string("quiznotavailable", "quiz", userdate($quiz->timeopen));
    } else {
        echo "<p align=center>".get_string("quizclosed", "quiz", userdate($quiz->timeclose));
    }


    if (!$quiz->questions) {
        print_heading(get_string("noquestions", "quiz"));
    } else {
        if ($numattempts < $quiz->attempts or !$quiz->attempts) { 
            if ($available) {
                $options["id"] = $cm->id;
                if ($numattempts and $quiz->grade) {
                    print_heading("$strbestgrade: $mygrade / $quiz->grade.");
                }
                echo "<BR>";
                echo "<DIV align=CENTER>";
                print_single_button("attempt.php", $options, get_string("attemptquiznow","quiz"));
                echo "</P>";
            }
        } else {
            print_heading(get_string("nomoreattempts", "quiz"));
            if ($quiz->grade) {
                print_heading(get_string("yourfinalgradeis", "quiz", "$mygrade / $quiz->grade"));
            }
        }
    }


// Finish the page
    print_footer($course);

?>
