<?PHP // $Id$

// This page lists all the instances of quiz in a particular course

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);

    add_to_log($course->id, "quiz", "view all", "index.php?id=$course->id", "");


// Print the header

    $strquizzes = get_string("modulenameplural", "quiz");
    $streditquestions = isteacheredit($course->id)
                        ? "<form target=\"_parent\" method=\"get\" "
                           ." action=\"$CFG->wwwroot/mod/quiz/edit.php\">"
                           ."<input type=\"hidden\" name=\"courseid\" "
                           ." value=\"$course->id\" />"
                           ."<input type=\"submit\" "
                           ." value=\"".get_string("editquestions", "quiz")."\" /></form>"

                        : "";
    $strquiz  = get_string("modulename", "quiz");

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    print_header("$course->shortname: $strquizzes", "$course->fullname", "$navigation $strquizzes",
                 "", "", true, $streditquestions, navmenu($course));

// Get all the appropriate data

    if (! $quizzes = get_all_instances_in_course("quiz", $course)) {
        notice("There are no quizzes", "../../course/view.php?id=$course->id");
        die;
    }

// Print the list of instances (your module will probably extend this)

    $timenow = time();
    $strname  = get_string("name");
    $strweek  = get_string("week");
    $strtopic  = get_string("topic");
    $strbestgrade  = get_string("bestgrade", "quiz");
    $strquizcloses = get_string("quizcloses", "quiz");
    $strattempts = get_string("attempts", "quiz");
    $strusers  = get_string("users");

    if (isteacher($course->id)) {
        $gradecol = $strattempts;
    } else {
        $gradecol = $strbestgrade;
    }
  
    if ($course->format == "weeks") {
        $table->head  = array ($strweek, $strname, $strquizcloses, $gradecol);
        $table->align = array ("center", "left", "left", "left");
        $table->size = array (10, "*", "*", "*");
    } else if ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname, $strquizcloses, $gradecol);
        $table->align = array ("center", "left", "left", "left");
        $table->size = array (10, "*", "*", "*");
    } else {
        $table->head  = array ($strname, $strquizcloses, $gradecol);
        $table->align = array ("left", "left", "left");
        $table->size = array ("*", "*", "*");
    }

    $currentsection = "";

    foreach ($quizzes as $quiz) {
        if (!$quiz->visible) { 
            //Show dimmed if the mod is hidden
            $link = "<A class=\"dimmed\" HREF=\"view.php?id=$quiz->coursemodule\">$quiz->name</A>";
        } else {
            //Show normal if the mod is visible
            $link = "<A HREF=\"view.php?id=$quiz->coursemodule\">$quiz->name</A>";
        }

        $bestgrade = quiz_get_best_grade($quiz->id, $USER->id);

        $printsection = "";
        if ($quiz->section !== $currentsection) {
            if ($quiz->section) {
                $printsection = $quiz->section;
            }
            if ($currentsection !== "") {
                $table->data[] = 'hr';
            }
            $currentsection = $quiz->section;
        }

        $closequiz = userdate($quiz->timeclose);

        if (isteacher($course->id)) {
            if ($allanswers = get_records("quiz_grades", "quiz", $quiz->id)) {
                $attemptcount = count_records_select("quiz_attempts", "quiz = '$quiz->id' AND timefinish > 0");
                $usercount = count_records("quiz_grades", "quiz", "$quiz->id");
                $strviewallanswers  = get_string("viewallanswers","quiz",$attemptcount);
                $gradecol = "<a href=\"report.php?q=$quiz->id\">$strviewallanswers ($usercount $strusers)</a>";
            } else {
                $answercount = 0;
                $gradecol = "";
            }
        } else { 
            if ($bestgrade === "" or $quiz->grade == 0) {
                $gradecol = "";
            } else {
                $gradecol = "$bestgrade / $quiz->grade";
            }
        }

        if ($course->format == "weeks" or $course->format == "topics") {
            $table->data[] = array ($printsection, $link, $closequiz, $gradecol);
        } else {
            $table->data[] = array ($link, $closequiz, $gradecol);
        }
    }

    echo "<br />";

    print_table($table);

// Finish the page

    print_footer($course);

?>
