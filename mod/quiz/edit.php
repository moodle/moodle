<?php // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_login();

    optional_variable($courseid);
    optional_variable($quizid);
    optional_variable($page, 0);
    optional_variable($perpage, "20"); 

    if (empty($destination)) {
        $destination = "";
    }

    $modform = data_submitted($destination);

    if ($modform and !empty($modform->course)) { // data submitted

        $modform->name = trim($modform->name);

        if (empty($modform->name)) {
            if (empty($modform->intro)) {
                $modform->name = get_string('modulename', 'quiz');
            } else {
                $modform->name = strip_tags($modform->intro);
            }
        }

        $SESSION->modform = $modform;    // Save the form in the current session

    } else if ($quizid) {
        if (! $modform = get_record('quiz', 'id', $quizid)) {
            error("The required quiz doesn't exist");
        }
        
        $modform->instance = $modform->id;
        
        $SESSION->modform = $modform;    // Save the form in the current session

    } else if ($courseid) { // Page retrieve through "Edit Questions" link - no quiz selected
        $modform->course = $courseid;
        unset($modform->instance);

        $SESSION->modform = $modform;    // Save the form in the current session

    } else {
        if (!isset($SESSION->modform)) {
          // We currently also get here after editing a question by
          // following the edit link on the review page. Perhaps that should be fixed.
            error('');
        }

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


    // Now, check for commands on this page and modify variables as necessary
    
    if (isset($cancel)) {
        redirect('view.php?q='.$modform->instance);
    }
    
    if (isset($recurse)) {
        $modform->recurse = $recurse;
    }

    if (!empty($up)) { /// Move the given question up a slot
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
    }

    if (!empty($down)) { /// Move the given question down a slot
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
    }

    if (!empty($add)) { /// Add a question to the current quiz
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
    }

    if (!empty($delete)) { /// Delete a question from the list 
        $questions = explode(",", $modform->questions);
        foreach ($questions as $key => $question) {
            if ($question == $delete) {
                unset($questions[$key]);
                unset($modform->grades[$question]);
            }
        }
        $modform->questions = implode(",", $questions);
    }

    if (!empty($setgrades)) { /// The grades have been updated, so update our internal list
        $rawgrades = $_POST;
        unset($modform->grades);
        foreach ($rawgrades as $key => $value) {    // Parse input for question -> grades
            if (substr($key, 0, 1) == "q") {
                $key = substr($key,1);
                $modform->grades[$key] = $value;
            }
        }
    }
    
    if (!empty($save)) {  // Save the list of questions and grades in the database and return
    
        //If caller is correct, $SESSION->sesskey must exist and coincide
        if (empty($SESSION->sesskey) or !confirm_sesskey($SESSION->sesskey)) {
            error(get_string('confirmsesskeybad', 'error'));
        }
        //Unset this, check done
        unset($SESSION->sesskey);
        
        quiz_update_instance($modform);
        $coursemodule = get_coursemodule_from_instance('quiz', $modform->instance);
        add_to_log($course->id, 'quiz', 'editquestions', 
                           "view.php?id=$coursemodule", 
                           "$modform->instance", $coursemodule); 
        redirect('view.php?q='.$modform->instance);
        die;
    }
 

    if (!empty($cat)) { //-----------------------------------------------------------
        $modform->category = $cat;
    }

    if (empty($modform->category)) {
        $category = quiz_get_default_category($course->id);
        $modform->category = $category->id;
    }
    if (!isset($modform->recurse)) {
        $modform->recurse = 1;
    }

    $modform->sumgrades = 0;
    if (!empty($modform->grades)) {
        foreach ($modform->grades as $grade) {
            $modform->sumgrades += $grade;
        }
    }

    $SESSION->modform = $modform;

    $strname    = get_string('name');
    $strquizzes = get_string('modulenameplural', 'quiz');
    $strediting = get_string(isset($modform->instance) ? "editingquiz" : "editquestions", "quiz");
    $strheading = empty($modform->name) ? $strediting : $modform->name;

    // Print basic page layout.

    if (!isset($modform->instance)) {
        print_header_simple($strediting, '',
                 "<a href=\"index.php?id=$course->id\">$strquizzes</a>".
                 " -> $strediting");
        echo '<table align="center" border="0" cellpadding="2" cellspacing="0">';
        echo '<tr><td valign="top">';

    } else {
        print_header_simple($strediting, '',
                 "<a href=\"index.php?id=$course->id\">$strquizzes</a>".
                 " -> <a href=\"view.php?q=$modform->instance\">$modform->name</a>".
                 " -> $strediting");
        echo '<table border="0" width="100%" cellpadding="2" cellspacing="0">';
        echo '<tr><td width="50%" valign="top">';
        print_simple_box_start("center", "100%", $THEME->cellcontent2);        
        print_heading($modform->name);
        quiz_print_question_list($modform->questions, $modform->grades); 
        ?>
        <center>
        <p>&nbsp;</p>
        <?php

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
        
        $SESSION->sesskey = !empty($USER->id) ? $USER->sesskey : '';
        ?>
        <form method="post" action="edit.php">
        <input type="hidden" name="sesskey" value="save" />
        <input type="submit" name="save" value="<?php  print_string("savequiz", "quiz") ?>" />
        <input type="submit" name="cancel" value="<?php  print_string("cancel") ?>" />
        </form>
        </center>
        <?php


        print_simple_box_end();
        echo '</td><td valign="top" width="50%">';
    }
    print_simple_box_start("center", "100%", $THEME->cellcontent2);
    quiz_print_category_form($course, $modform->category, $modform->recurse);
    print_simple_box_end();
    
    print_spacer(5,1);

    print_simple_box_start("center", "100%", $THEME->cellcontent2);
    quiz_print_cat_question_list($modform->category,
                                 isset($modform->instance), $modform->recurse, $page, $perpage);
    print_simple_box_end();

    echo '</td></tr>';
    echo '</table>';

    if (!isset($modform->instance)) {
        print_continue("index.php?id=$modform->course");
    }

    print_footer($course);
?>
