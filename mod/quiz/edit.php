<?php // $Id$

    require_once("../../config.php");
    require_once("locallib.php");

    require_login();

    $courseid = optional_param('courseid');
    $quizid   = optional_param('quizid');
    $page     = optional_param('page', 0);
    $perpage  = optional_param('perpage', 20);

    if ($modform = data_submitted() and !empty($modform->course)) { // data submitted

        $SESSION->modform = $modform;    // Save the form in the current session

    } else if ($quizid) {
        if (! $modform = get_record('quiz', 'id', $quizid)) {
            error("The required quiz doesn't exist");
        }
        $modform->instance = $modform->id;
        $SESSION->modform = $modform;    // Save the form in the current session

        $cm = get_coursemodule_from_instance('quiz', $modform->instance);
        add_to_log($cm->course, 'quiz', 'editquestions',
                           "view.php?id=$modform->instance",
                           "$modform->name", $cm->id);

    } else if ($courseid) { // Page retrieve through "Edit Questions" link - no quiz selected
        $modform->course = $courseid;
        unset($modform->instance);
        $SESSION->modform = $modform;    // Save the form in the current session

        add_to_log($courseid, 'quiz', 'editquestions', "index.php?id=$courseid");

    } else {
        if (!isset($SESSION->modform)) {
          // We currently also get here after editing a question by
          // following the edit link on the review page. Perhaps that should be fixed.
            error('');
        }

        // The data is obtained from a $SESSION variable. This is mostly for historic reasons.
        // With the way things work now it would be just as possible to get the data from the database.
        $modform = $SESSION->modform;
    }


    if (! $course = get_record("course", "id", $modform->course)) {
        error("This course doesn't exist");
    }

    require_login($course->id);

    if (!isteacheredit($course->id)) {
        error("You can't modify this course!");
    }

    if (isset($modform->instance)
        && empty($modform->grades))  // Construct an array to hold all the grades.
    {
        $modform->grades = quiz_get_all_question_grades($modform->questions, $modform->instance);
    }


/// Now, check for commands on this page and modify variables as necessary

    if (isset($_REQUEST['up']) and confirm_sesskey()) { /// Move the given question up a slot
        $questions = explode(",", $modform->questions);
        if ($questions[0] <> $up) {
            foreach ($questions as $key => $question) {
                if ($up == $question) {
                    $swap = $questions[$key-1];
                    $questions[$key-1] = $question;
                    $questions[$key]   = $swap;
                    break;
                }
            }
            $modform->questions = implode(",", $questions);
        }
        if (!set_field('quiz', 'questions', $modform->questions, 'id', $modform->instance)) {
            error('Could not save question list');
        }
    }

    if (isset($_REQUEST['down']) and confirm_sesskey()) { /// Move the given question down a slot
        $questions = explode(",", $modform->questions);
        if ($questions[count($questions)-1] <> $down) {
            foreach ($questions as $key => $question) {
                if ($down == $question) {
                    $swap = $questions[$key+1];
                    $questions[$key+1] = $question;
                    $questions[$key]   = $swap;
                    break;
                }
            }
            $modform->questions = implode(",", $questions);
        }
        if (!set_field('quiz', 'questions', $modform->questions, 'id', $modform->instance)) {
            error('Could not save question list');
        }
    }

    if (isset($_REQUEST['add']) and confirm_sesskey()) { /// Add a question to the current quiz
        $rawquestions = $_POST;
        if (!empty($modform->questions)) {
            $questions = explode(",", $modform->questions);
        }
        foreach ($rawquestions as $key => $value) {    // Parse input for question ids
            if (substr($key, 0, 1) == "q") {
                $key = substr($key,1);
                if (!empty($questions)) {
                    foreach ($questions as $question) {
                        if ($question == $key) {
                            continue 2;
                        }
                    }
                }
                $questions[] = $key;

                $questionrecord = get_record("quiz_questions", "id", $key);

                if (!empty($questionrecord->defaultgrade)) {
                    $modform->grades[$key] = $questionrecord->defaultgrade;
                } else if ($questionrecord->qtype == DESCRIPTION){
                    $modform->grades[$key] = 0;
                } else {
                    $modform->grades[$key] = 1;
                }
            }
        }
        if (!empty($questions)) {
            $modform->questions = implode(",", $questions);
        } else {
            $modform->questions = "";
        }
        if (!set_field('quiz', 'questions', $modform->questions, 'id', $modform->instance)) {
            error('Could not save question list');
        }
        quiz_questiongrades_update($modform->grades, $modform->instance);
    }

    if (isset($_REQUEST['delete']) and confirm_sesskey()) { /// Delete a question from the list
        $questions = explode(",", $modform->questions);
        foreach ($questions as $key => $question) {
            if ($question == $delete) {
                unset($questions[$key]);
                unset($modform->grades[$question]);
                if (!delete_records('quiz_question_grades', 'quiz', $modform->instance, 'question', $question)) {
                    error("Could not delete question grade");
                }
            }
        }
        $modform->questions = implode(",", $questions);
        if (!set_field('quiz', 'questions', $modform->questions, 'id', $modform->instance)) {
            error('Could not save question list');
        }
    }

    if (isset($_REQUEST['setgrades']) and confirm_sesskey()) { /// The grades have been updated, so update our internal list
        $rawgrades = $_POST;
        unset($modform->grades);
        foreach ($rawgrades as $key => $value) {    // Parse input for question -> grades
            if (substr($key, 0, 1) == "q") {
                $key = substr($key,1);
                $modform->grades[$key] = $value;
            }
        }
        if (!set_field('quiz', 'questions', $modform->questions, 'id', $modform->instance)) {
            error('Could not save question list');
        }
        quiz_questiongrades_update($modform->grades, $modform->instance);
    }

    if (isset($_REQUEST['cat'])) { /// coming from category selection drop-down menu
        $modform->category = $cat;
    }

    if (isset($_REQUEST['recurse'])) { /// coming from checkbox below category selection form
        $modform->recurse = $recurse;
    }

/// all commands have been dealt with, now print the page

    if (empty($modform->category) or !record_exists('quiz_categories', 'id', $modform->category)) {
        $category = quiz_get_default_category($course->id);
        $modform->category = $category->id;
    }
    if (!isset($modform->recurse)) {
        $modform->recurse = 1;
    }

    $SESSION->modform = $modform;

    $strquizzes = get_string('modulenameplural', 'quiz');
    $strediting = get_string('editquestions', "quiz");

    // Print basic page layout.

    if (!isset($modform->instance)) {
        // one column layout for non-quiz-specific editing page
        print_header_simple($strediting, '',
                 "<a href=\"index.php?id=$course->id\">$strquizzes</a>".
                 " -> $strediting");
        echo '<table align="center" border="0" cellpadding="2" cellspacing="0">';
        echo '<tr><td valign="top">';

    } else {
        // two column layout with quiz info in left column
        print_header_simple($strediting, '',
                 "<a href=\"index.php?id=$course->id\">$strquizzes</a>".
                 " -> <a href=\"view.php?q=$modform->instance\">$modform->name</a>".
                 " -> $strediting");
        echo '<table border="0" width="100%" cellpadding="2" cellspacing="0">';
        echo '<tr><td width="50%" valign="top">';
        print_simple_box_start("center", "100%");
        print_heading($modform->name);
        $sumgrades = quiz_print_question_list($modform->questions, $modform->grades);
        if (!set_field('quiz', 'sumgrades', $sumgrades, 'id', $modform->instance)) {
            error('Failed to set sumgrades');
        }

        if ($attemptcount = count_records_select("quiz_attempts", "quiz = '$modform->instance' AND timefinish > 0"))  {
            $strviewallanswers  = get_string("viewallanswers","quiz",$attemptcount);
            $strattemptsexist  = get_string("attemptsexist","quiz");
            $usercount = count_records("quiz_grades", "quiz", "$modform->instance");
            $strusers  = get_string("users");
            if (! $cm = get_coursemodule_from_instance("quiz", $modform->instance, $course->id)) {
                error("Course Module ID was incorrect");
            }
            notify("$strattemptsexist<br /><a href=\"report.php?id=$cm->id\">$strviewallanswers ($usercount $strusers)</a>");
        }

        print_simple_box_end();
        echo '</td><td valign="top" width="50%">';
    }
    // non-quiz-specific column
    print_simple_box_start("center", "100%");
    // starts with category selection form
    quiz_print_category_form($course, $modform->category, $modform->recurse);
    print_simple_box_end();

    print_spacer(5,1);
    // continues with list of questions
    print_simple_box_start("center", "100%");
    quiz_print_cat_question_list($modform->category,
                                 isset($modform->instance), $modform->recurse, $page, $perpage);
    print_simple_box_end();

    echo '</td></tr>';
    echo '</table>';

    if (!isset($modform->instance)) {
        print_continue("index.php?id=$modform->course");
    } else {
        print_continue('view.php?q='.$modform->instance);
    }

    print_footer($course);
?>
