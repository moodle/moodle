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

    add_to_log($course->id, "quiz", "attempt", "attempt.php?id=$cm->id", "$quiz->id");

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

/// Print the headings and so on

    print_heading($quiz->name);

    if (!$available) {
        error("Sorry, this quiz is not available", "view.php?id=$cm->id");
    }

    if ($attempts = quiz_get_user_attempts($quiz->id, $USER->id)) {
        $numattempts = count($attempts) + 1;
    } else {
        $numattempts = 1;
    }

    if ($numattempts > $quiz->attempts) {
        error("Sorry, you've had $quiz->attempts attempts already.", "view.php?id=$cm->id");
    }

    print_heading("Attempt $numattempts out of $quiz->attempts");

    print_simple_box($quiz->intro, "CENTER");


/// Print all the questions

    echo "<BR>";

    if (!$quiz->questions) {
        error("No questions have been defined!", "view.php?id=$cm->id");
    }

    $questions = explode(",", $quiz->questions);

    if (!$grades = get_records_sql("SELECT question, grade FROM quiz_question_grades WHERE question in ($quiz->questions)")) {
        error("No grades were found for these questions!");
    }

    echo "<FORM METHOD=POST ACTION=attempt.php>";
    echo "<INPUT TYPE=hidden NAME=q VALUE=\"$quiz->id\">";
    foreach ($questions as $key => $questionid) {
        print_simple_box_start("CENTER", "90%");
        quiz_print_question($key+1, $questionid, $grades[$questionid]->grade, $course->id);
        print_simple_box_end();
        echo "<BR>";
    }
    echo "<CENTER><INPUT TYPE=submit VALUE=\"".get_string("savemyanswers", "quiz")."\"></CENTER>";
    echo "</FORM>";


// Finish the page
    print_footer($course);

?>
