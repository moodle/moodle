<?php // $Id$

    require_once("../../config.php");
    require_once("locallib.php");

    require_login();

    $courseid = optional_param('courseid');
    $quizid   = optional_param('quizid');
    $page     = optional_param('page', 0);
    $perpage  = optional_param('perpage', 20);

    $strquizzes = get_string('modulenameplural', 'quiz');
    $strquiz = get_string('modulename', 'quiz');
    $streditingquestions = get_string('editquestions', "quiz");
    $streditingquiz = get_string("editinga", "moodle", $strquiz);

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
                           "view.php?id=$cm->id",
                           "$quizid", $cm->id);

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

    require_login($course->id, false);

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

    if (isset($_REQUEST['addquestion']) and confirm_sesskey()) { /// Add a single question to the current quiz

        // add question to quiz->questions
        $key = $_REQUEST['addquestion'];
        $questions = array();
        if (!empty($modform->questions)) {
            $questions = explode(",", $modform->questions);
        }
        if (!in_array($key, $questions)) {
            $questions[] = $key;
        }
        $modform->questions = implode(",", $questions);
        if (!set_field('quiz', 'questions', $modform->questions, 'id', $modform->instance)) {
            error('Could not save question list');
        }

        // update question grades
        $questionrecord = get_record("quiz_questions", "id", $key);
        if (!empty($questionrecord->defaultgrade)) {
            $modform->grades[$key] = $questionrecord->defaultgrade;
        } else if ($questionrecord->qtype == DESCRIPTION){
            $modform->grades[$key] = 0;
        } else {
            $modform->grades[$key] = 1;
        }
        quiz_questiongrades_update($modform->grades, $modform->instance);
    }

    if (isset($_REQUEST['add']) and confirm_sesskey()) { /// Add selected questions to the current quiz
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

    if (isset($_REQUEST['move']) and confirm_sesskey()) { /// Move selected questions to new category
        if (!$tocategory = get_record('quiz_categories', 'id', $_REQUEST['category'])) {
            error('Invalid category');
        }
        if (!isteacheredit($tocategory->course)) {
            error(get_string('categorynoedit', 'quiz', $tocategory->name), 'edit.php');
        }
        $rawquestions = $_POST;
        foreach ($rawquestions as $key => $value) {    // Parse input for question ids
            if (substr($key, 0, 1) == "q") {
                $key = substr($key,1);
                if (!set_field('quiz_questions', 'category', $tocategory->id, 'id', $key)) {
                    error('Could not update category field');
                }
            }
        }
    }

    if (isset($_REQUEST['delete']) and confirm_sesskey()) { /// Remove a question from the quiz
        $questions = explode(",", $modform->questions);
        foreach ($questions as $key => $question) {
            if ($question == $delete) {
                unset($questions[$key]);
                unset($modform->grades[$question]);
                // Delete all responses associated with all attempts for this question in the quiz.
                if ($attempts = get_records('quiz_attempts', 'quiz', $modform->instance)) {
                    foreach ($attempts as $attempt) {
                        if (!delete_records('quiz_responses', 'question', $question, 'attempt', $attempt->id)) {
                            error('Could not delete all responses for this question');
                        }
                    }
                }
                // Delete question from quiz_question_grades table
                if (!delete_records('quiz_question_grades', 'quiz', $modform->instance, 'question', $question)) {
                    error('Could not delete the question from this quiz');
                }
            }
        }
        $modform->questions = implode(",", $questions);
        if (!set_field('quiz', 'questions', $modform->questions, 'id', $modform->instance)) {
            error('Could not save question list');
        }
    }

    if (isset($_REQUEST['deleteselected'])) { // delete selected questions from the category

        if (isset($confirm) and confirm_sesskey()) { // teacher has already confirmed the action
            if ($confirm == md5($deleteselected)) {
                if ($questionlist = explode(',', $deleteselected)) {
                    // for each question either hide it if it is in use or delete it
                    foreach ($questionlist as $questionid) {
                        if (record_exists('quiz_responses', 'question', $questionid) or
                            record_exists('quiz_responses', 'originalquestion', $questionid) or
                            record_exists('quiz_question_grades', 'question', $questionid)) {
                            if (!set_field('quiz_questions', 'hidden', 1, 'id', $questionid)) {
                                error('Was not able to hide question');
                            }
                        } else {
                            if (!delete_records("quiz_questions", "id", $questionid)) {
                                error("An error occurred trying to delete question (id $questionid)");
                            }
                        }
                    }
                }
                redirect("edit.php");
            } else {
                error("Confirmation string was incorrect");
            }

        } else { // teacher still has to confirm
            // make a list of all the questions that are selected
            $rawquestions = $_POST;
            $questionlist = '';  // comma separated list of ids of questions to be deleted
            $questionnames = ''; // string with names of questions separated by <br /> with
                                 // an asterix in front of those that are in use
            $inuse = false;      // set to true if at least one of the questions is in use
            foreach ($rawquestions as $key => $value) {    // Parse input for question ids
                if (substr($key, 0, 1) == "q") {
                    $key = substr($key,1);
                    $questionlist .= $key.',';
                    if (record_exists('quiz_responses', 'question', $key) or
                            record_exists('quiz_responses', 'originalquestion', $key) or
                            record_exists('quiz_question_grades', 'question', $key)) {
                        $questionnames .= '* ';
                        $inuse = true;
                    }
                    $questionnames .= get_field('quiz_questions', 'name', 'id', $key).'<br />';
                }
            }
            if (!$questionlist) { // no questions were selected
                redirect('edit.php');
            }
            $questionlist = rtrim($questionlist, ',');

            // Add an explanation about questions in use
            if ($inuse) {
                $questionnames .= get_string('questionsinuse', 'quiz');
            }
            print_header_simple($streditingquestions, '',
                 "<a href=\"index.php?id=$course->id\">$strquizzes</a>".
                 " -> $streditingquestions");
            notice_yesno(get_string("deletequestionscheck", "quiz", $questionnames),
                        "edit.php?sesskey=$USER->sesskey&amp;deleteselected=$questionlist&amp;confirm=".md5($questionlist), "edit.php");
            print_footer($course);
            exit;
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

    if(isset($_REQUEST['displayoptions'])) {
        $modform->recurse = isset($_REQUEST['recurse']) ? 1 : 0;
        $modform->showhidden = isset($_REQUEST['showhidden']);
    }

/// all commands have been dealt with, now print the page

    if (empty($modform->category) or !record_exists('quiz_categories', 'id', $modform->category)) {
        $category = quiz_get_default_category($course->id);
        $modform->category = $category->id;
    }
    if (!isset($modform->recurse)) {
        $modform->recurse = 1;
    }
    if (!isset($modform->showhidden)) {
        $modform->showhidden = false;
    }

    $SESSION->modform = $modform;

    // Print basic page layout.

    if (isset($modform->instance) and record_exists_sql("SELECT * FROM {$CFG->prefix}quiz_attempts WHERE quiz = '$modform->instance' AND NOT (userid = '$USER->id') LIMIT 1")){
    // one column layout with table of questions used in this quiz
        print_header_simple($streditingquiz, '',
                 "<a href=\"index.php?id=$course->id\">$strquizzes</a>".
                 " -> <a href=\"view.php?q=$modform->instance\">".format_string($modform->name,true)."</a>".
                 " -> $streditingquiz");
        print_simple_box_start("center");
        print_heading(format_string($modform->name));
        $attemptcount = count_records_select("quiz_attempts", "quiz = '$modform->instance' AND timefinish > 0");

        $strviewallanswers  = get_string("viewallanswers","quiz",$attemptcount);
        $strattemptsexist  = get_string("attemptsexist","quiz");
        $usercount = count_records("quiz_grades", "quiz", "$modform->instance");
        $strusers  = get_string("users");
        if (! $cm = get_coursemodule_from_instance("quiz", $modform->instance, $course->id)) {
            error("Course Module ID was incorrect");
        }
        notify("$strattemptsexist<br /><a href=\"report.php?id=$cm->id\">$strviewallanswers ($usercount $strusers)</a>");

        $sumgrades = quiz_print_question_list($modform->questions, $modform->grades, false, $modform->instance);
        if (!set_field('quiz', 'sumgrades', $sumgrades, 'id', $modform->instance)) {
            error('Failed to set sumgrades');
        }

        print_simple_box_end();
        print_continue('view.php?q='.$modform->instance);
        print_footer($course);
        exit;
    }

    if (!isset($modform->instance)) {
        // one column layout for non-quiz-specific editing page
        print_header_simple($streditingquestions, '',
                 "<a href=\"index.php?id=$course->id\">$strquizzes</a>".
                 " -> $streditingquestions");
        echo '<table align="center" border="0" cellpadding="2" cellspacing="0">';
        echo '<tr><td valign="top">';

    } else {
        // two column layout with quiz info in left column
        print_header_simple($streditingquiz, '',
                 "<a href=\"index.php?id=$course->id\">$strquizzes</a>".
                 " -> <a href=\"view.php?q=$modform->instance\">".format_string($modform->name,true)."</a>".
                 " -> $streditingquiz");
        echo '<table border="0" width="100%" cellpadding="2" cellspacing="0">';
        echo '<tr><td width="50%" valign="top">';
        print_simple_box_start("center", "100%");
        print_heading(format_string($modform->name));
        $sumgrades = quiz_print_question_list($modform->questions, $modform->grades, true, $modform->instance);
        if (!set_field('quiz', 'sumgrades', $sumgrades, 'id', $modform->instance)) {
            error('Failed to set sumgrades');
        }

        print_simple_box_end();
        print_continue('view.php?q='.$modform->instance);
        echo '</td><td valign="top" width="50%">';
    }
    // non-quiz-specific column
    print_simple_box_start("center", "100%");
    // starts with category selection form
    quiz_print_category_form($course, $modform->category, $modform->recurse, $modform->showhidden);
    print_simple_box_end();

    print_spacer(5,1);
    // continues with list of questions
    print_simple_box_start("center", "100%");
    quiz_print_cat_question_list($course, $modform->category,
                                 isset($modform->instance), $modform->recurse, $page, $perpage, $modform->showhidden);
    print_simple_box_end();
    if (!isset($modform->instance)) {
        print_continue("index.php?id=$modform->course");
    } else {
        print_continue('view.php?q='.$modform->instance);
    }
    echo '</td></tr>';
    echo '</table>';



    print_footer($course);
?>
