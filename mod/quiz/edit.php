<?php // $Id$
/**
* Page to edit quizzes
*
* This page generally has two columns:
* The right column lists all available questions in a chosen category and
* allows them to be edited or more to be added. This column is only there if
* the quiz does not already have student attempts
* The left column lists all questions that have been added to the current quiz.
* The lecturer can add questions from the right hand list to the quiz or remove them
*
* The script also processes a number of actions:
* Actions affecting a quiz:
* up and down  Changes the order of questions and page breaks
* addquestion  Adds a single question to the quiz
* add          Adds several selected questions to the quiz
* addrandom    Adds a certain number of random questions to the quiz
* repaginate   Re-paginates the quiz
* delete       Removes a question from the quiz
* savechanges  Saves the order and grades for questions in the quiz
*
* @version $Id$
* @author Martin Dougiamas and many others. This has recently been extensively
*         rewritten by Gustav Delius and other members of the Serving Mathematics project
*         {@link http://maths.york.ac.uk/serving_maths}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package quiz
*/
    require_once("../../config.php");
    require_once($CFG->dirroot.'/mod/quiz/editlib.php');

    require_login();

    $quizid    = optional_param('quizid', 0, PARAM_INT);

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
        }

    } else if (!empty($sortorder)) {
        // no quiz or course was specified so we need to use the stored modform
        if (isset($SESSION->modform)) {
            $modform = $SESSION->modform;
        } else {
            error('cmunknown');
        }
    } else {
        // no quiz or course was specified so we need to use the stored modform
        if (isset($SESSION->modform)) {
            $modform = $SESSION->modform;
        } else {
            print_error('cmunknown');
        }
    }

    // Get the course object and related bits.
    if (! $course = get_record("course", "id", $modform->course)) {
        error("This course doesn't exist");
    }
    $coursecontext = get_context_instance(CONTEXT_COURSE, $modform->course);

    require_login($course->id, false);
    
    // Get the module and related bits.
    $cm = get_coursemodule_from_instance('quiz', $modform->instance);
    $modform->cmid = $cm->id;
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    // Log this visit.
    add_to_log($cm->course, 'quiz', 'editquestions',
            "view.php?id=$cm->id", "$quizid", $cm->id);

    require_capability('mod/quiz:manage', $context);

    if (isset($modform->instance)
        && empty($modform->grades))  // Construct an array to hold all the grades.
    {
        $modform->grades = quiz_get_all_question_grades($modform);
    }

    $SESSION->returnurl = $FULLME;

/// Now, check for commands on this page and modify variables as necessary

    if (isset($_REQUEST['up']) and confirm_sesskey()) { /// Move the given question up a slot
        $up = optional_param('up', 0, PARAM_INT);
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
        $down = optional_param('down', 0, PARAM_INT);
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
        if (! $category = get_record('question_categories', 'id', $categoryid)) {
            error('Category ID is incorrect');
        }
        $category->name = addslashes($category->name);
        // find existing random questions in this category
        $random = RANDOM;
        if ($existingquestions = get_records_select('question', "qtype = '$random' AND category = '$category->id'")) {
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
                $question = $QTYPES[RANDOM]->save_question($question, $form, $course);
                if(!isset($question->id)) {
                    error('Could not insert new random question!');
                }
                quiz_add_quiz_question($question->id, $modform);
            }
        }
    }

    if (isset($_REQUEST['repaginate']) and confirm_sesskey()) { /// Re-paginate the quiz
        if (isset($_REQUEST['questionsperpage'])) {
            $modform->questionsperpage = required_param('questionsperpage', PARAM_INT);
            if (!set_field('quiz', 'questionsperpage', $modform->questionsperpage, 'id', $modform->id)) {
                error('Could not save number of questions per page');
            }
        }
        $modform->questions = quiz_repaginate($modform->questions, $modform->questionsperpage);
        if (!set_field('quiz', 'questions', $modform->questions, 'id', $modform->id)) {
            error('Could not save layout');
        }
    }

    if (isset($_REQUEST['delete']) and confirm_sesskey()) { /// Remove a question from the quiz
        quiz_delete_quiz_question($_REQUEST['delete'], $modform);
    }

    if (isset($_REQUEST['savechanges']) and confirm_sesskey()) {
        $savequizid = required_param('savequizid', PARAM_INT);
        if ($modform->id != $savequizid) {
            error("Error saving quiz settings, please do not change two quizes from the same browser", $CFG->wwwroot.'/mod/quiz/edit.php?quizid='.$savequizid);
        }
    /// We need to save the new ordering (if given) and the new grades
        $oldquestions = explode(",", $modform->questions); // the questions in the old order
        $questions = array(); // for questions in the new order
        $rawgrades = $_POST;
        unset($modform->grades);
        foreach ($rawgrades as $key => $value) {    // Parse input for question -> grades
            if (substr($key, 0, 1) == "q") {
                $key = substr($key,1);
                $modform->grades[$key] = $value;
                quiz_update_question_instance($modform->grades[$key], $key, $modform->instance);
            } elseif (substr($key, 0, 1) == "o") {   // Parse input for ordering info
                $key = substr($key,1);
                $questions[$value] = $oldquestions[$key];
            }
        }
        
        // If ordering info was given, reorder the questions
        if ($questions) {
            ksort($questions);
            $modform->questions = implode(",", $questions);
            // Always have a page break at the end
            $modform->questions = $modform->questions . ',0';
            // Avoid duplicate page breaks
            while (strpos($modform->questions, ',0,0')) {
                $modform->questions = str_replace(',0,0', ',0', $modform->questions);
            }
            if (!set_field('quiz', 'questions', $modform->questions, 'id', $modform->instance)) {
                error('Could not save question list');
            }
        }

        // If rescaling is required save the new maximum
        if (isset($_REQUEST['maxgrade'])) {
            if (!quiz_set_grade(optional_param('maxgrade', 0), $modform)) {
                error('Could not set a new maximum grade for the quiz');
            }
        }
    }

    if(isset($_REQUEST['showbreaks'])) {
        $SESSION->quiz_showbreaks = optional_param('showbreaks', 0, PARAM_BOOL);
        $SESSION->quiz_reordertool = optional_param('reordertool', 0, PARAM_BOOL);
    }

/// Delete any teacher preview attempts if the quiz has been modified
    if (isset($_REQUEST['savechanges']) or isset($_REQUEST['delete']) or isset($_REQUEST['repaginate']) or isset($_REQUEST['addrandom']) or isset($_REQUEST['addquestion']) or isset($_REQUEST['up']) or isset($_REQUEST['down']) or isset($_REQUEST['add'])) {
        delete_records('quiz_attempts', 'preview', '1', 'quiz', $modform->id);
    }

/// all commands have been dealt with, now print the page

    if (empty($modform->category) or !record_exists('question_categories', 'id', $modform->category)) {
        $category = get_default_question_category($course->id);
        $modform->category = $category->id;
    }
    if (!isset($SESSION->quiz_showbreaks)) {
        $SESSION->quiz_showbreaks = ($CFG->quiz_questionsperpage < 2) ? 0 : 1;
    }
    if (!isset($SESSION->quiz_reordertool)) {
        $SESSION->quiz_reordertool = 0;
    }

    $SESSION->modform = $modform;

    // Print basic page layout.

    if (isset($modform->instance) and record_exists_select('quiz_attempts', "quiz = '$modform->instance' AND preview = '0'")){
        // one column layout with table of questions used in this quiz
        $strupdatemodule = has_capability('moodle/course:manageactivities', $coursecontext)
                    ? update_module_button($modform->cmid, $course->id, get_string('modulename', 'quiz'))
                    : "";
        print_header_simple($streditingquiz, '',
                 "<a href=\"index.php?id=$course->id\">$strquizzes</a>".
                 " -> <a href=\"view.php?q=$modform->instance\">".format_string($modform->name).'</a>'.
                 " -> $streditingquiz", "", "",
                 true, $strupdatemodule);

        $currenttab = 'edit';
        $mode = 'editq';
        $quiz = &$modform;
        include('tabs.php');

        print_simple_box_start("center");

        $a->attemptnum = count_records('quiz_attempts', 'quiz', $quiz->id, 'preview', 0);
        $a->studentnum = count_records_select('quiz_attempts', "quiz = '$quiz->id' AND preview = '0'", 'COUNT(DISTINCT userid)');
        $a->studentstring  = $course->students;
        if (! $cm = get_coursemodule_from_instance("quiz", $modform->instance, $course->id)) {
            error("Course Module ID was incorrect");
        }
        echo "<center>\n";
        echo "<a href=\"report.php?mode=overview&amp;id=$cm->id\">".get_string('numattempts', 'quiz', $a)."</a><br />".get_string("attemptsexist","quiz");
        echo "</center><br/ >\n";

        $sumgrades = quiz_print_question_list($modform, false, $SESSION->quiz_showbreaks, $SESSION->quiz_reordertool);
        if (!set_field('quiz', 'sumgrades', $sumgrades, 'id', $modform->instance)) {
            error('Failed to set sumgrades');
        }

        print_simple_box_end();
        print_footer($course);
        exit;
    }

    // two column layout with quiz info in left column
    $strupdatemodule = has_capability('moodle/course:manageactivities', $coursecontext)
        ? update_module_button($modform->cmid, $course->id, get_string('modulename', 'quiz'))
        : "";
    print_header_simple($streditingquiz, '',
             "<a href=\"index.php?id=$course->id\">$strquizzes</a>".
             " -> <a href=\"view.php?q=$modform->instance\">".format_string($modform->name).'</a>'.
             " -> $streditingquiz",
             "", "", true, $strupdatemodule);

    $currenttab = 'edit';
    $mode = 'editq';
    $quiz = &$modform;
    include('tabs.php');

    echo '<table border="0" width="100%" cellpadding="2" cellspacing="0">';
    echo '<tr><td width="50%" valign="top">';
    print_simple_box_start("center", "100%");

    $sumgrades = quiz_print_question_list($modform, true, $SESSION->quiz_showbreaks, $SESSION->quiz_reordertool);
    if (!set_field('quiz', 'sumgrades', $sumgrades, 'id', $modform->instance)) {
        error('Failed to set sumgrades');
    }

    print_simple_box_end();

    echo '</td><td valign="top" width="50%">';

    require($CFG->dirroot.'/question/showbank.php');

    echo '</td></tr>';
    echo '</table>';

    print_footer($course);
?>
