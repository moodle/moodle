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

    $timenow = time();


// Print the page header

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strquizzes = get_string("modulenameplural", "quiz");
    $strquiz  = get_string("modulename", "quiz");

    print_header("$course->shortname: $quiz->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strquizzes</A> -> $quiz->name", 
                 "", "", true, update_module_button($cm->id, $course->id, $strquiz));

    if (isteacher($course->id)) {
        if ($allanswers = get_records("quiz_grades", "quiz", $quiz->id)) {
            $answercount = count($allanswers);
        } else {
            $answercount = 0;
        }
        echo "<P align=right><A HREF=\"report.php?id=$cm->id\">".get_string("viewallanswers","quiz",$answercount)."</A></P>";
    }

    $available = ($quiz->timeopen < $timenow and $timenow < $quiz->timeclose);

// Print the main part of the page

    print_heading($quiz->name);

    print_simple_box(text_to_html($quiz->intro), "CENTER");

    if ($available) {
        echo "<P ALIGN=CENTER>".get_string("quizavailable", "quiz", userdate($quiz->timeclose));
    } else if ($timenow < $quiz->timeopen) {
        echo "<P ALIGN=CENTER>".get_string("quiznotavailable", "quiz", userdate($quiz->timeopen));
    } else {
        echo "<P ALIGN=CENTER>".get_string("quizclosed", "quiz", userdate($quiz->timeclose));
    }

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
        echo "<P ALIGN=CENTER>".get_string("attemptsallowed", "quiz").": $quiz->attempts</P>";
        echo "<P ALIGN=CENTER>".get_string("grademethod", "quiz").": ".$QUIZ_GRADE_METHOD[$quiz->grademethod]."</P>";
    } else {
        echo "<BR>";
    }

    $strattempt       = get_string("attempt", "quiz");
    $strtimetaken     = get_string("timetaken", "quiz");
    $strtimecompleted = get_string("timecompleted", "quiz");
    $strgrade         = get_string("grade");
    $strbestgrade     = get_string("bestgrade", "quiz");

    if ($numattempts) { 
        $table->head = array($strattempt, $strtimetaken, $strtimecompleted, "$strgrade / $quiz->grade");
        $table->align = array("CENTER", "CENTER", "LEFT", "RIGHT");
        $table->width = array("", "", "", "");
        foreach ($attempts as $attempt) {
            $table->data[] = array( $attempt->attempt, 
                                    format_time($attempt->timefinish - $attempt->timestart),
                                    userdate($attempt->timefinish), 
                                    format_float(($attempt->sumgrades/$quiz->sumgrades)*$quiz->grade) );
        }
        print_table($table);
    }

    $mygrade = quiz_get_best_grade($quiz->id, $USER->id);

    if (!$quiz->questions) {
        print_heading(get_string("noquestions", "quiz"));
    } else {
        if ($numattempts < $quiz->attempts or !$quiz->attempts) { 
            if ($available) {
                $options["id"] = $cm->id;
                if ($numattempts) {
                    print_heading("$strbestgrade: $mygrade / $quiz->grade.");
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
    }


// Finish the page
    print_footer($course);

?>
