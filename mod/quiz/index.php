<?php // $Id$
/**
* This page lists all the instances of quiz in a particular course
*
* @version $Id$
* @author Martin Dougiamas and many others.
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package quiz
*/

    require_once("../../config.php");
    require_once("locallib.php");

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

    print_header_simple("$strquizzes", "", "$strquizzes",
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
    $strusers  = $course->students;

    if (isteacher($course->id)) {
        $gradecol = $strattempts;
    } else {
        $gradecol = $strbestgrade;
    }

    if ($course->format == "weeks") {
        $table->head  = array ($strweek, $strname, $strquizcloses, $gradecol);
        $table->align = array ("center", "left", "left", "left");
        $table->size = array (10, "", "", "");
    } else if ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname, $strquizcloses, $gradecol);
        $table->align = array ("center", "left", "left", "left");
        $table->size = array (10, "", "", "");
    } else {
        $table->head  = array ($strname, $strquizcloses, $gradecol);
        $table->align = array ("left", "left", "left");
        $table->size = array ("", "", "");
    }

    $currentsection = "";

    foreach ($quizzes as $quiz) {
        if (!$quiz->visible) {
            //Show dimmed if the mod is hidden
            $link = "<a class=\"dimmed\" href=\"view.php?id=$quiz->coursemodule\">".format_string($quiz->name,true)."</a>";
        } else {
            //Show normal if the mod is visible
            $link = "<a href=\"view.php?id=$quiz->coursemodule\">".format_string($quiz->name,true)."</a>";
        }

        $bestgrade = quiz_get_best_grade($quiz, $USER->id);

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

        $closequiz = $quiz->timeclose ? userdate($quiz->timeclose) : '';

        if (isteacher($course->id)) {
            if ($usercount = count_records_select('quiz_attempts', "quiz = '$quiz->id' AND preview = '0'", 'COUNT(DISTINCT userid)')) {
                $attemptcount = count_records('quiz_attempts', 'quiz', $quiz->id, 'preview', 0);
                $strviewallreports  = get_string('viewallreports', 'quiz', $attemptcount);
                $gradecol = "<a href=\"report.php?mode=overview&amp;q=$quiz->id\">$strviewallreports ($usercount $strusers)</a>";
            } else {
                $answercount = 0;
                $gradecol = "";
            }
        } else {
            if ($bestgrade === NULL || $quiz->grade == 0) {
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
