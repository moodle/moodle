<?PHP // $Id$

    require_once("../../config.php");
    require_once("lib.php");

    require_login();

    optional_variable($courseid);

    if (empty($destination)) {
        $destination = "";
    }

    $modform = data_submitted($destination);

    if ($modform and !empty($modform->course)) {    // form submitted from mod.html

        if (empty($modform->name) or empty($modform->intro)) {
            error(get_string("filloutallfields"), $_SERVER["HTTP_REFERER"]);
        }

        $SESSION->modform = $modform;    // Save the form in the current session

    } else if ($courseid) { // Page retrieve through "Edit Questions" link - no quiz selected
        $modform->course = $courseid;
        unset($modform->instance);

        $SESSION->modform = $modform;    // Save the form in the current session

    } else {
        if (!isset($SESSION->modform)) {
            error("You have used this page incorrectly!");
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

    if (!empty($cat)) { //-----------------------------------------------------------
        $modform->category = $cat;
    }

    if (empty($modform->category)) {
        $modform->category = "";
    }

    $modform->sumgrades = 0;
    if (!empty($modform->grades)) {
        foreach ($modform->grades as $grade) {
            $modform->sumgrades += $grade;
        }
    }

    $SESSION->modform = $modform;

    $strediting = get_string(isset($modform->instance) ? "editingquiz"
                                                       : "editquestions",
                             "quiz");
    $strname    = get_string("name");

    print_header("$course->shortname: $strediting", "$course->shortname: $strediting",
                 "<A HREF=\"$CFG->wwwroot/course/view.php?id=$course->id\">$course->shortname</A> -> $strediting");

    // Print basic page layout.

    echo "<TABLE BORDER=0 WIDTH=\"100%\" CELLPADDING=2 CELLSPACING=0>";
    echo "<TR><TD WIDTH=50% VALIGN=TOP>";
        if (isset($modform->instance)) {
            print_simple_box_start("CENTER", "100%", $THEME->cellcontent2);        
            print_heading($modform->name);
            quiz_print_question_list($modform->questions, $modform->grades); 
            ?>
            <CENTER>
            <P>&nbsp;</P>
            <FORM  NAME=theform METHOD=post ACTION=<?php echo $modform->destination ?>>
            <INPUT TYPE="hidden" NAME=course  VALUE="<?php  p($modform->course) ?>">
            <INPUT TYPE="submit" VALUE="<?php  print_string("savequiz", "quiz") ?>">
            <INPUT type="submit" name=cancel value="<?php  print_string("cancel") ?>">
            </FORM>
            </CENTER>
            <?php
            print_simple_box_end();
            echo "</TD><TD VALIGN=top WIDTH=50%>";
        }
        print_simple_box_start("CENTER", "100%", $THEME->cellcontent2);
        quiz_print_category_form($course, $modform->category);
        print_simple_box_end();
        
        print_spacer(5,1);

        print_simple_box_start("CENTER", "100%", $THEME->cellcontent2);
        quiz_print_cat_question_list($modform->category,
                                     isset($modform->instance));
        print_simple_box_end();
    echo "</TD></TR>";
    echo "</TABLE>";

    if (false == isset($modform->instance)) {
        print_continue("index.php?id=$modform->course");
    }

    print_footer($course);
?>
