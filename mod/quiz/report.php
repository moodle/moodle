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
    $strgrade  = get_string("grade");

    print_header("$course->shortname: $quiz->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strquizzes</A> 
                  -> <A HREF=\"view.php?id=$cm->id\">$quiz->name</A> -> $strreport", 
                 "", "", true);

    print_heading($quiz->name);

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
