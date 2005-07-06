<?php // $Id$
/**
* Page to edit quizzes
*
* This page generally has two columns:
* The right column lists all available questions in a chosen category and
* allows them to be edited or more to be added. This column is only there if
* the quiz does not already have student attempts
* The left column lists all questions that have been added to the current quiz.
* This column is only there if this page has been called in the context of a quiz.
* The lecturer can add questions from the right hand list to the quiz or remove them
*
* The script also processes a number of actions:
* Actions affecting a quiz:
* up and down  Changes the order of questions and page breaks
* addquestion  Adds a single question to the quiz
* add          Adds several selected questions to the quiz
* addrandom    Adds a certain number of random questions to the quiz
* delete       Removes a question from the quiz
* setgrades    Changes the maximum grades for questions in the quiz
* repaginate   Re-paginates the quiz
* Actions affecting the question pool:
* move         Moves a question to a different category
* deleteselected Deletes the selected questions from the category
* Other actions:
* cat          Chooses the category
* displayoptions Sets display options
*
* @version $Id$
* @author Martin Dougiamas and many others. This has recently been extensively
*         rewritten by Gustav Delius and other members of the Serving Mathematics project
*         {@link http://maths.york.ac.uk/serving_maths}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package quiz
*/
    require_once("../../config.php");
    require_once("editlib.php");

    require_login();

    $courseid = optional_param('courseid');
    $quizid   = optional_param('quizid');
    $page     = optional_param('page', -1);
    $perpage  = optional_param('perpage', 20);

    $strquizzes = get_string('modulenameplural', 'quiz');
    $strquiz = get_string('modulename', 'quiz');
    $streditingquestions = get_string('editquestions', "quiz");
    $streditingquiz = get_string("editinga", "moodle", $strquiz);

    if ($modform = data_submitted() and !empty($modform->course)) { // data submitted

        $SESSION->modform = $modform;    // Save the form in the current session

    } else if ($quizid) {
        if (isset($SESSION->modform->id) and $SESSION->modform->id == $quizid) {
            // modform for this quiz already exists, use it
            $modform = $SESSION->modform;
        } else {
            // create new modform from database
            if (! $modform = get_record('quiz', 'id', $quizid)) {
                error("The required quiz doesn't exist");
            }
            $modform->instance = $modform->id;
            $SESSION->modform = $modform;    // Save the form in the current session

            $cm = get_coursemodule_from_instance('quiz', $modform->instance);
            $modform->cmid = $cm->id;
            // We don't log all visits to this page but only those that recreate modform
            add_to_log($cm->course, 'quiz', 'editquestions',
                               "view.php?id=$cm->id",
                               "$quizid", $cm->id);
        }

    } else if ($courseid) { // Page retrieve through "Edit Questions" link - no quiz selected
        $modform->course = $courseid;
        unset($modform->instance);
        $SESSION->modform = $modform;    // Save the form in the current session

        add_to_log($courseid, 'quiz', 'editquestions', "index.php?id=$courseid");

    } else {
        // we might get here after editing a question in
        // a popup window. So close window automatically.
?>
<script type="text/javascript">
<!--
if (self.name == 'editquestion') {
    self.close();
}
-->
</script>
<noscript>
<?php notify(get_string('pleaseclose', 'quiz')); ?>
</noscript>
<?php
        // no quiz or course was specified so we need to use the stored modform
        if (isset($SESSION->modform)) { 
            $modform = $SESSION->modform;
        } else {
            exit;
        }
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
        $modform->grades = quiz_get_all_question_grades($modform);
    }

    if ($page > -1) {
        $modform->page = $page;
    } else {
        $page = isset($modform->page) ? $modform->page : 0;
    }

/// Now, check for commands on this page and modify variables as necessary

    if (isset($_REQUEST['up']) and confirm_sesskey()) { /// Move the given question up a slot
        $questions = explode(",", $modform->questions);
        if ($up > 0 and isset($questions[$up])) {
            $prevkey = ($questions[$up-1] == 0) ? $up-2 : $up-1;
            $swap = $questions[$prevkey];
            $questions[$prevkey] = $questions[$up];
            $questions[$up]   = $swap;
            $modform->questions = implode(",", $questions);
            // Always have a page break at the end
            $modform->questions = $modform->questions . ',0';
            // Avoid duplicate page breaks
            $modform->questions = str_replace(',0,0', ',0', $modform->questions);
            if (!set_field('quiz', 'questions', $modform->questions, 'id', $modform->instance)) {
                error('Could not save question list');
            }
        }
    }

    if (isset($_REQUEST['down']) and confirm_sesskey()) { /// Move the given question down a slot
        $questions = explode(",", $modform->questions);
        if ($down < count($questions)) {
            $nextkey = ($questions[$down+1] == 0) ? $down+2 : $down+1;
            $swap = $questions[$nextkey];
            $questions[$nextkey] = $questions[$down];
            $questions[$down]   = $swap;
            $modform->questions = implode(",", $questions);
            // Avoid duplicate page breaks
            $modform->questions = str_replace(',0,0', ',0', $modform->questions);
            if (!set_field('quiz', 'questions', $modform->questions, 'id', $modform->instance)) {
                error('Could not save question list');
            }
        }
    }

    if (isset($_REQUEST['addquestion']) and confirm_sesskey()) { /// Add a single question to the current quiz
        quiz_add_quiz_question($_REQUEST['addquestion'], $modform);
    }

    if (isset($_REQUEST['add']) and confirm_sesskey()) { /// Add selected questions to the current quiz
        foreach ($_POST as $key => $value) {    // Parse input for question ids
            if (substr($key, 0, 1) == "q") {
                quiz_add_quiz_question(substr($key,1), $modform);
            }
        }
    }

    if (isset($_REQUEST['addrandom']) and confirm_sesskey()) { /// Add random questions to the quiz
        $recurse = optional_param('recurse', 0, PARAM_BOOL);
        $categoryid = required_param('categoryid', PARAM_INT);
        $randomcount = required_param('randomcount', PARAM_INT);
        // load category
        if (! $category = get_record('quiz_categories', 'id', $categoryid)) {
            error('Category ID is incorrect');
        }
        // find existing random questions in this category
        $random = RANDOM;
        if ($existingquestions = get_records_select('quiz_questions', "qtype = '$random' AND category = '$category->id'")) {
            // now remove the ones that are already used in this quiz
            if ($questionids = explode(',', $modform->questions)) {
                foreach ($questionids as $questionid) {
                    unset($existingquestions[$questionid]);
                }
            }
            // now take as many of these as needed
            $i = 0;
            while (($existingquestion = array_pop($existingquestions)) and ($i < $randomcount)) {
                if ($existingquestion->questiontext == $recurse) {
                    // this question has the right recurse property, so use it
                    quiz_add_quiz_question($existingquestion->id, $modform);
                    $i++;
                }
            }
            $randomcreate = $randomcount - $i; // the number of additional random questions needed.
        } else {
            $randomcreate = $randomcount;
        }

        if ($randomcreate > 0) {

            $form->name = get_string('random', 'quiz') .' ('. $category->name .')';
            $form->questiontext = $recurse; // we use the questiontext field to store the info
                                            // on whether to include questions in subcategories
            $form->questiontextformat = 0;
            $form->image = '';
            $form->defaultgrade = 1;
            $form->hidden = 1;
            for ($i=0; $i<$randomcreate; $i++) {
                $form->stamp = make_unique_id_code();  // Set the unique code (not to be changed)
                $question = new stdClass;
                $question->category = $category->id;
                $question->qtype = RANDOM;
                $question = $QUIZ_QTYPES[RANDOM]->save_question($question, $form, $course);
                if(!isset($question->id)) {
                    error('Could not insert new random question!');
                }
                quiz_add_quiz_question($question->id, $modform);
            }
        }
    }

    if (isset($_REQUEST['repaginate']) and confirm_sesskey()) { /// Re-paginate the quiz
        if (isset($_REQUEST['questionsperpage'])) {
            $modform->questionsperpage = required_param('questionsperpage', 1, PARAM_INT);
            if (!set_field('quiz', 'questionsperpage', $modform->questionsperpage, 'id', $modform->id)) {
                error('Could not save number of questions per page');
            }
        }
        $modform->questions = quiz_repaginate($modform->questions, $modform->questionsperpage);
        if (!set_field('quiz', 'questions', $modform->questions, 'id', $modform->id)) {
            error('Could not save layout');
        }
    }

    if (isset($_REQUEST['move']) and confirm_sesskey()) { /// Move selected questions to new category
        if (!$tocategory = get_record('quiz_categories', 'id', $_REQUEST['category'])) {
            error('Invalid category');
        }
        if (!isteacheredit($tocategory->course)) {
            error(get_string('categorynoedit', 'quiz', $tocategory->name), 'edit.php');
        }
        foreach ($_POST as $key => $value) {    // Parse input for question ids
            if (substr($key, 0, 1) == "q") {
                $key = substr($key,1);
                if (!set_field('quiz_questions', 'category', $tocategory->id, 'id', $key)) {
                    error('Could not update category field');
                }
            }
        }
    }

    if (isset($_REQUEST['delete']) and confirm_sesskey()) { /// Remove a question from the quiz
        quiz_delete_quiz_question($_REQUEST['delete'], $modform);
    }

    if (isset($_REQUEST['deleteselected'])) { // delete selected questions from the category

        if (isset($confirm) and confirm_sesskey()) { // teacher has already confirmed the action
            if ($confirm == md5($deleteselected)) {
                if ($questionlist = explode(',', $deleteselected)) {
                    // for each question either hide it if it is in use or delete it
                    foreach ($questionlist as $questionid) {
                        if (record_exists('quiz_question_instances', 'question', $questionid) or
                            record_exists('quiz_states', 'originalquestion', $questionid)) {
                            if (!set_field('quiz_questions', 'hidden', 1, 'id', $questionid)) {
                               error('Was not able to hide question');
                            }
                        } else {
                            delete_records("quiz_questions", "id", $questionid);
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
                    if (record_exists('quiz_question_instances', 'question', $key) or
                        record_exists('quiz_states', 'originalquestion', $key)) {
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

    if (isset($_REQUEST['setgrades']) and confirm_sesskey()) {
    /// The grades have been updated, so update our internal list
        $rawgrades = $_POST;
        unset($modform->grades);
        foreach ($rawgrades as $key => $value) {    // Parse input for question -> grades
            if (substr($key, 0, 1) == "q") {
                $key = substr($key,1);
                $modform->grades[$key] = $value;
                quiz_update_question_instance($modform->grades[$key], $key, $modform->instance);
            }
        }

        // If rescaling is required save the new maximum
        if (isset($_REQUEST['maxgrade'])) {
            $modform->grade = optional_param('maxgrade', 0);
            if (!set_field('quiz', 'grade', $modform->grade, 'id', $modform->instance)) {
                error('Could not set new maximal grade for quiz');
            }
        }
    }

    if (isset($_REQUEST['cat'])) { /// coming from category selection drop-down menu
        $modform->category = $cat;
        $page = 0;
        $modform->page = 0;
    }

    if(isset($_REQUEST['recurse'])) {
        $SESSION->quiz_recurse = optional_param('recurse', 0, PARAM_BOOL);
    }
    if(isset($_REQUEST['showbreaks'])) {
        $SESSION->quiz_showbreaks = optional_param('showbreaks', 0, PARAM_BOOL);
    }
    if(isset($_REQUEST['showhidden'])) {
        $SESSION->quiz_showhidden = optional_param('showhidden', 0, PARAM_BOOL);
    }

/// Delete any teacher preview attempts if the quiz has been modified
    if (isset($_REQUEST['setgrades']) or isset($_REQUEST['delete']) or isset($_REQUEST['repaginate']) or isset($_REQUEST['addrandom']) or isset($_REQUEST['addquestion']) or isset($_REQUEST['up']) or isset($_REQUEST['down']) or isset($_REQUEST['add'])) {
        delete_records('quiz_attempts', 'preview', '1', 'quiz', $modform->id);
    }

/// all commands have been dealt with, now print the page

    if (empty($modform->category) or !record_exists('quiz_categories', 'id', $modform->category)) {
        $category = quiz_get_default_category($course->id);
        $modform->category = $category->id;
    }
    if (!isset($SESSION->quiz_recurse)) {
        $SESSION->quiz_recurse = 1;
    }
    if (!isset($SESSION->quiz_showhidden)) {
        $SESSION->quiz_showhidden = false;
    }
    if (!isset($SESSION->quiz_showbreaks)) {
        $SESSION->quiz_showbreaks = ($CFG->quiz_questionsperpage < 2) ? 0 : 1;
    }

    $SESSION->modform = $modform;

    // Print basic page layout.

    if (isset($modform->instance) and record_exists_sql("SELECT * FROM {$CFG->prefix}quiz_attempts WHERE quiz = '$modform->instance' AND preview = '0' LIMIT 1")){
        // one column layout with table of questions used in this quiz
        $strupdatemodule = isteacheredit($course->id)
                    ? update_module_button($modform->cmid, $course->id, get_string('modulename', 'quiz'))
                    : "";
        print_header_simple($streditingquiz, '',
                 "<a href=\"index.php?id=$course->id\">$strquizzes</a>".
                 " -> <a href=\"view.php?q=$modform->instance\">".format_string($modform->name).'</a>'.
                 " -> $streditingquiz", "", "",
                 true, $strupdatemodule);

        $currenttab = 'edit';
        $quiz = &$modform;
        include('tabs.php');

        print_simple_box_start("center");

        $attemptcount = count_records('quiz_attempts', 'quiz', $modform->instance, 'preview', 0);

        $strviewallanswers  = get_string("viewallanswers","quiz",$attemptcount);
        $strattemptsexist  = get_string("attemptsexist","quiz");
        $usercount = count_records_select('quiz_attempts', "quiz = '$modform->id' AND preview = '0'", 'COUNT(DISTINCT userid)');
        $strusers  = $course->students;
        if (! $cm = get_coursemodule_from_instance("quiz", $modform->instance, $course->id)) {
            error("Course Module ID was incorrect");
        }
        echo "<center>\n";
        echo "$strattemptsexist<br /><a href=\"report.php?mode=overview&amp;id=$cm->id\">$strviewallanswers ($usercount $strusers)</a>";
        echo "<form target=\"_parent\" method=\"get\" action=\"$CFG->wwwroot/mod/quiz/edit.php\">\n";
        echo "    <input type=\"hidden\" name=\"courseid\" value=\"$course->id\" />\n";
        echo "    <input type=\"submit\" value=\"".get_string("editcatquestions", "quiz")."\" />\n";
        echo "</form>";
        echo "</center><br/ >\n";

        $sumgrades = quiz_print_question_list($modform, false, $SESSION->quiz_showbreaks);
        if (!set_field('quiz', 'sumgrades', $sumgrades, 'id', $modform->instance)) {
            error('Failed to set sumgrades');
        }

        print_simple_box_end();
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
        $strupdatemodule = isteacheredit($course->id)
            ? update_module_button($modform->cmid, $course->id, get_string('modulename', 'quiz'))
            : "";
        print_header_simple($streditingquiz, '',
                 "<a href=\"index.php?id=$course->id\">$strquizzes</a>".
                 " -> <a href=\"view.php?q=$modform->instance\">".format_string($modform->name).'</a>'.
                 " -> $streditingquiz",
                 "", "", true, $strupdatemodule);

        $currenttab = 'edit';
        $quiz = &$modform;
        include('tabs.php');

        echo '<table border="0" width="100%" cellpadding="2" cellspacing="0">';
        echo '<tr><td width="50%" valign="top">';
        print_simple_box_start("center", "100%");

        $sumgrades = quiz_print_question_list($modform, true, $SESSION->quiz_showbreaks);
        if (!set_field('quiz', 'sumgrades', $sumgrades, 'id', $modform->instance)) {
            error('Failed to set sumgrades');
        }

        print_simple_box_end();

        echo '</td><td valign="top" width="50%">';
    }
    // non-quiz-specific column
    print_simple_box_start("center", "100%");
    // starts with category selection form
    quiz_print_category_form($course, $modform->category, $SESSION->quiz_recurse, $SESSION->quiz_showhidden);
    print_simple_box_end();

    print_spacer(5,1);
    // continues with list of questions
    print_simple_box_start("center", "100%");
    quiz_print_cat_question_list($course, $modform->category,
                                 isset($modform->instance) ? $modform->instance : 0, $SESSION->quiz_recurse, $page, $perpage, $SESSION->quiz_showhidden);
    print_simple_box_end();
    if (!isset($modform->instance)) {
        print_continue("index.php?id=$modform->course");
    }
    echo '</td></tr>';
    echo '</table>';



    print_footer($course);
?>
