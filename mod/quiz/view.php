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

    add_to_log($course->id, "quiz", "view", "view.php?id=$cm->id", "$quiz->id");

    if ($course->format == "weeks" and $quiz->days) {
        $timenow = time();
        $timestart = $course->startdate + (($cw->section - 1) * 608400);
        $timefinish = $timestart + (3600 * 24 * $quiz->days);
        $available = ($timestart < $timenow and $timenow < $timefinish);
    } else {
        $available = true;
    }

// Print the page header

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strquizzes = get_string("modulenameplural", "quiz");
    $strquiz  = get_string("modulename", "quiz");

    print_header("$course->shortname: $quiz->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strquizzes</A> -> $quiz->name", 
                 "", "", true, update_module_icon($cm->id, $course->id));

    if (isteacher($course->id)) {
        if ($allanswers = get_records("quiz_grades", "quiz", $quiz->id)) {
            $answercount = count($allanswers);
        } else {
            $answercount = 0;
        }
        echo "<P align=right><A HREF=\"report.php?id=$cm->id\">".get_string("viewallanswers","quiz",$answercount)."</A></P>";
    }

// Print the main part of the page

    print_heading($quiz->name);

    print_simple_box($quiz->intro, "CENTER");

    if (isset($timestart) and isset($timefinish)) {
        if ($available) {
            echo "<P ALIGN=CENTER>The quiz is available: ";
        } else {
            echo "<P ALIGN=CENTER>The quiz is not available: ";
        }
        echo userdate($timestart)." - ".userdate($timefinish)." </P>";
    }

    if ($attempts = quiz_get_user_attempts($quiz->id, $USER->id)) {
        $numattempts = count($attempts);
    } else {
        $numattempts = 0;
    }

    if ($quiz->attempts > 1) {
        echo "<P ALIGN=CENTER>".get_string("attemptsallowed", "quiz").": $quiz->attempts</P>";
        echo "<P ALIGN=CENTER>".get_string("grademethod", "quiz").": ".$QUIZ_GRADE_METHOD[$quiz->grademethod]."</P>";
    }
    if ($numattempts) { 
        $table->head = array("Attempt", "Time", "Grade");
        $table->align = array("CENTER", "LEFT", "RIGHT");
        foreach ($attempts as $attempt) {
            $table->data[] = array( $attempt->attempt, 
                                    userdate($attempt->timemodified), 
                                    ($attempt->sumgrades/$quiz->sumgrades)*$quiz->grade );
        }
        print_table($table);
    }

    $mygrade = quiz_get_best_grade($quiz->id, $USER->id);

    if ($numattempts < $quiz->attempts or !$quiz->attempts) { 
        if ($available) {
            $options["id"] = $cm->id;
            if ($numattempts) {
                print_heading("Your best grade so far is $mygrade / $quiz->grade.");
            }
            echo "<BR>";
            echo "<DIV align=CENTER>";
            print_single_button("attempt.php", $options, $label="Attempt quiz now");
            echo "</P>";
        }
    } else {
        print_heading(get_string("nomoreattempts", "quiz"));
        print_heading(get_string("yourfinalgradeis", "quiz", "$mygrade / $quiz->grade"));
    }


// Finish the page
    print_footer($course);

?>
