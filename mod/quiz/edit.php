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

    /**
     * Callback function called from question_list() function (which is called from showbank()
     * Displays action icon as first action for each question.
     */
    function module_specific_actions($pageurl, $questionid, $cmid){
        global $CFG;
        if (has_capability("mod/quiz:manage", get_context_instance(CONTEXT_MODULE, $cmid))){
            $straddtoquiz = get_string("addtoquiz", "quiz");
            $out = "<a title=\"$straddtoquiz\" href=\"edit.php?".$pageurl->get_query_string()."&amp;addquestion=$questionid&amp;sesskey=".sesskey()."\"><img
                  src=\"$CFG->pixpath/t/moveleft.gif\" alt=\"$straddtoquiz\" /></a>&nbsp;";
            return $out;
        } else {
            return '';
        }
    }
    /**
     * Callback function called from question_list() function (which is called from showbank()
     * Displays button in form with checkboxes for each question.
     */
    function module_specific_buttons($cmid){
        global $THEME;
        if (has_capability("mod/quiz:manage", get_context_instance(CONTEXT_MODULE, $cmid))){
            $straddtoquiz = get_string("addtoquiz", "quiz");
            $out = "<input type=\"submit\" name=\"add\" value=\"{$THEME->larrow} $straddtoquiz\" />\n";
            $out .= '</td><td>';
            return $out;
        } else {
            return '';
        }
    }
    
    
    /**
     * Callback function called from question_list() function (which is called from showbank()
     * Displays button in form with checkboxes for each question.
     */
    function module_specific_controls($totalnumber, $recurse, $categoryid, $cmid){
        if (has_capability("mod/quiz:manage", get_context_instance(CONTEXT_MODULE, $cmid))){
            for ($i = 1;$i <= min(10, $totalnumber); $i++) {
                $randomcount[$i] = $i;
            }
            for ($i = 20;$i <= min(100, $totalnumber); $i += 10) {
                $randomcount[$i] = $i;
            }
            $out = '<br />';
            $out .= get_string('addrandom', 'quiz', choose_from_menu($randomcount, 'randomcount', '1', '', '', '', true));
            $out .= '<input type="hidden" name="recurse" value="'.$recurse.'" />';
            $out .= "<input type=\"hidden\" name=\"categoryid\" value=\"$categoryid\" />";
            $out .= ' <input type="submit" name="addrandom" value="'. get_string('add') .'" />';
            $out .= helpbutton('random', get_string('random', 'quiz'), 'quiz', true, false, '', true);
            return $out;
        } else {
            return '';
        }
    }       
        
    list($thispageurl, $courseid, $cmid, $cm, $quiz, $pagevars) = question_edit_setup(true);

    //these params are only passed from page request to request while we stay on this page
    //otherwise they would go in question_edit_setup
    $quiz_showbreaks = optional_param('showbreaks', -1, PARAM_BOOL);
    $quiz_reordertool = optional_param('reordertool', 0, PARAM_BOOL);
    if ($quiz_showbreaks > -1) {
        $thispageurl->param('showbreaks', $quiz_showbreaks);
    } else {
        $quiz_showbreaks = ($CFG->quiz_questionsperpage < 2) ? 0 : 1;
    }
    if ($quiz_reordertool != 0) {
        $thispageurl->param('reordertool', $quiz_reordertool);
    }
        
    $strquizzes = get_string('modulenameplural', 'quiz');
    $strquiz = get_string('modulename', 'quiz');
    $streditingquestions = get_string('editquestions', "quiz");
    $streditingquiz = get_string('editinga', 'moodle', $strquiz);
    



    // Get the course object and related bits.
    if (! $course = get_record("course", "id", $quiz->course)) {
        error("This course doesn't exist");
    }
    $coursecontext = get_context_instance(CONTEXT_COURSE, $quiz->course);
    $quizcontext = get_context_instance(CONTEXT_MODULE, $quiz->cmid);
    

    // Log this visit.
    add_to_log($cm->course, 'quiz', 'editquestions',
            "view.php?id=$cm->id", "$quiz->id", $cm->id);

    require_capability('mod/quiz:manage', $quizcontext);

    if (isset($quiz->instance)
        && empty($quiz->grades)){  // Construct an array to hold all the grades.
        $quiz->grades = quiz_get_all_question_grades($quiz);
    }


/// Now, check for commands on this page and modify variables as necessary

    if (isset($_REQUEST['up']) and confirm_sesskey()) { /// Move the given question up a slot
        $up = optional_param('up', 0, PARAM_INT);
        $questions = explode(",", $quiz->questions);
        if ($up > 0 and isset($questions[$up])) {
            $prevkey = ($questions[$up-1] == 0) ? $up-2 : $up-1;
            $swap = $questions[$prevkey];
            $questions[$prevkey] = $questions[$up];
            $questions[$up]   = $swap;
            $quiz->questions = implode(",", $questions);
            // Always have a page break at the end
            $quiz->questions = $quiz->questions . ',0';
            // Avoid duplicate page breaks
            $quiz->questions = str_replace(',0,0', ',0', $quiz->questions);
            if (!set_field('quiz', 'questions', $quiz->questions, 'id', $quiz->instance)) {
                error('Could not save question list');
            }
        }
    }

    if (isset($_REQUEST['down']) and confirm_sesskey()) { /// Move the given question down a slot
        $down = optional_param('down', 0, PARAM_INT);
        $questions = explode(",", $quiz->questions);
        if ($down < count($questions)) {
            $nextkey = ($questions[$down+1] == 0) ? $down+2 : $down+1;
            $swap = $questions[$nextkey];
            $questions[$nextkey] = $questions[$down];
            $questions[$down]   = $swap;
            $quiz->questions = implode(",", $questions);
            // Avoid duplicate page breaks
            $quiz->questions = str_replace(',0,0', ',0', $quiz->questions);
            if (!set_field('quiz', 'questions', $quiz->questions, 'id', $quiz->instance)) {
                error('Could not save question list');
            }
        }
    }

    if (isset($_REQUEST['addquestion']) and confirm_sesskey()) { /// Add a single question to the current quiz
        quiz_add_quiz_question($_REQUEST['addquestion'], $quiz);
    }

    if (isset($_REQUEST['add']) and confirm_sesskey()) { /// Add selected questions to the current quiz
        foreach ($_POST as $key => $value) {    // Parse input for question ids
            if (preg_match('!^q([0-9]+)$!', $key, $matches)) {
                $key = $matches[1];
                quiz_add_quiz_question($key, $quiz);
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
            if ($questionids = explode(',', $quiz->questions)) {
                foreach ($questionids as $questionid) {
                    unset($existingquestions[$questionid]);
                }
            }
            // now take as many of these as needed
            $i = 0;
            while (($existingquestion = array_pop($existingquestions)) and ($i < $randomcount)) {
                if ($existingquestion->questiontext == $recurse) {
                    // this question has the right recurse property, so use it
                    quiz_add_quiz_question($existingquestion->id, $quiz);
                    $i++;
                }
            }
            $randomcreate = $randomcount - $i; // the number of additional random questions needed.
        } else {
            $randomcreate = $randomcount;
        }

        if ($randomcreate > 0) {

            $form->name = get_string('random', 'quiz') .' ('. $category->name .')';
            $form->category = $category->id;
            $form->questiontext = $recurse; // we use the questiontext field to store the info
                                            // on whether to include questions in subcategories
            $form->questiontextformat = 0;
            $form->image = '';
            $form->defaultgrade = 1;
            $form->hidden = 1;
            for ($i=0; $i<$randomcreate; $i++) {
                $form->stamp = make_unique_id_code();  // Set the unique code (not to be changed)
                $question = new stdClass;
                $question->qtype = RANDOM;
                $question = $QTYPES[RANDOM]->save_question($question, $form, $course);
                if(!isset($question->id)) {
                    error('Could not insert new random question!');
                }
                quiz_add_quiz_question($question->id, $quiz);
            }
        }
    }

    if (isset($_REQUEST['repaginate']) and confirm_sesskey()) { /// Re-paginate the quiz
        if (isset($_REQUEST['questionsperpage'])) {
            $quiz->questionsperpage = required_param('questionsperpage', PARAM_INT);
            if (!set_field('quiz', 'questionsperpage', $quiz->questionsperpage, 'id', $quiz->id)) {
                error('Could not save number of questions per page');
            }
        }
        $quiz->questions = quiz_repaginate($quiz->questions, $quiz->questionsperpage);
        if (!set_field('quiz', 'questions', $quiz->questions, 'id', $quiz->id)) {
            error('Could not save layout');
        }
    }

    if (isset($_REQUEST['delete']) and confirm_sesskey()) { /// Remove a question from the quiz
        quiz_delete_quiz_question($_REQUEST['delete'], $quiz);
    }

    if (isset($_REQUEST['savechanges']) and confirm_sesskey()) {
    /// We need to save the new ordering (if given) and the new grades
        $oldquestions = explode(",", $quiz->questions); // the questions in the old order
        $questions = array(); // for questions in the new order
        $rawgrades = $_POST;
        unset($quiz->grades);
        foreach ($rawgrades as $key => $value) {    // Parse input for question -> grades
            if (preg_match('!^q([0-9]+)$!', $key, $matches)) {
                $key = $matches[1];
                $quiz->grades[$key] = $value;
                quiz_update_question_instance($quiz->grades[$key], $key, $quiz->instance);
            } elseif (preg_match('!^q([0-9]+)$!', $key, $matches)) {   // Parse input for ordering info
                $key = $matches[1];
                $questions[$value] = $oldquestions[$key];
            }
        }
        
        // If ordering info was given, reorder the questions
        if ($questions) {
            ksort($questions);
            $quiz->questions = implode(",", $questions);
            // Always have a page break at the end
            $quiz->questions = $quiz->questions . ',0';
            // Avoid duplicate page breaks
            while (strpos($quiz->questions, ',0,0')) {
                $quiz->questions = str_replace(',0,0', ',0', $quiz->questions);
            }
            if (!set_field('quiz', 'questions', $quiz->questions, 'id', $quiz->instance)) {
                error('Could not save question list');
            }
        }

        // If rescaling is required save the new maximum
        if (isset($_REQUEST['maxgrade'])) {
            if (!quiz_set_grade(optional_param('maxgrade', 0), $quiz)) {
                error('Could not set a new maximum grade for the quiz');
            }
        }
    }

/// Delete any teacher preview attempts if the quiz has been modified
    if (isset($_REQUEST['savechanges']) or isset($_REQUEST['delete']) or isset($_REQUEST['repaginate']) or isset($_REQUEST['addrandom']) or isset($_REQUEST['addquestion']) or isset($_REQUEST['up']) or isset($_REQUEST['down']) or isset($_REQUEST['add'])) {
        delete_records('quiz_attempts', 'preview', '1', 'quiz', $quiz->id);
    }

/// all commands have been dealt with, now print the page

    if (empty($quiz->category) or !record_exists('question_categories', 'id', $quiz->category)) {
        $category = get_default_question_category($course->id);
        $quiz->category = $category->id;
    }

    // Print basic page layout.

    if (isset($quiz->instance) and record_exists_select('quiz_attempts', "quiz = '$quiz->instance' AND preview = '0'")){
        // one column layout with table of questions used in this quiz
        $strupdatemodule = has_capability('moodle/course:manageactivities', $coursecontext)
                    ? update_module_button($cm->id, $course->id, get_string('modulename', 'quiz'))
                    : "";
        $navlinks = array();
        $navlinks[] = array('name' => $strquizzes, 'link' => "index.php?id=$course->id", 'type' => 'activity');    
        $navlinks[] = array('name' => format_string($quiz->name), 'link' => "view.php?q=$quiz->instance", 'type' => 'activityinstance');    
        $navlinks[] = array('name' => $streditingquiz, 'link' => '', 'type' => 'title');
        $navigation = build_navigation($navlinks);
           
        print_header_simple($streditingquiz, '', $navigation, "", "",
                 true, $strupdatemodule);

        $currenttab = 'edit';
        $mode = 'editq';

        include('tabs.php');

        print_box_start();

        $a->attemptnum = count_records('quiz_attempts', 'quiz', $quiz->id, 'preview', 0);
        $a->studentnum = count_records_select('quiz_attempts', "quiz = '$quiz->id' AND preview = '0'", 'COUNT(DISTINCT userid)');
        $a->studentstring  = $course->students;
        if (! $cm = get_coursemodule_from_instance("quiz", $quiz->instance, $course->id)) {
            error("Course Module ID was incorrect");
        }
        echo "<div class=\"attemptsnotice\">\n";
        echo "<a href=\"report.php?mode=overview&amp;id=$cm->id\">".get_string('numattempts', 'quiz', $a)."</a><br />".get_string("attemptsexist","quiz");
        echo "</div><br />\n";

        $sumgrades = quiz_print_question_list($quiz,  $thispageurl, false, $quiz_showbreaks, $quiz_reordertool);
        if (!set_field('quiz', 'sumgrades', $sumgrades, 'id', $quiz->instance)) {
            error('Failed to set sumgrades');
        }

        print_box_end();
        print_footer($course);
        exit;
    }

    // two column layout with quiz info in left column
    $strupdatemodule = has_capability('moodle/course:manageactivities', $coursecontext)
        ? update_module_button($cm->id, $course->id, get_string('modulename', 'quiz'))
        : "";
    $navlinks = array();
    $navlinks[] = array('name' => $strquizzes, 'link' => "index.php?id=$course->id", 'type' => 'activity');    
    $navlinks[] = array('name' => format_string($quiz->name), 'link' => "view.php?q=$quiz->instance", 'type' => 'activityinstance');    
    $navlinks[] = array('name' => $streditingquiz, 'link' => '', 'type' => 'title');
    $navigation = build_navigation($navlinks);
    
    print_header_simple($streditingquiz, '', $navigation, "", "", true, $strupdatemodule);

    $currenttab = 'edit';
    $mode = 'editq';

    include('tabs.php');

    echo '<table border="0" style="width:100%" cellpadding="2" cellspacing="0">';
    echo '<tr><td style="width:50%" valign="top">';
    print_box_start('generalbox quizquestions');
    print_heading(get_string('questionsinthisquiz', 'quiz'), '', 2);

    $sumgrades = quiz_print_question_list($quiz, $thispageurl, true, $quiz_showbreaks, $quiz_reordertool);
    if (!set_field('quiz', 'sumgrades', $sumgrades, 'id', $quiz->instance)) {
        error('Failed to set sumgrades');
    }

    print_box_end();

    echo '</td><td style="width:50%" valign="top">';

    question_showbank($thispageurl, $cm, $pagevars['qpage'], $pagevars['qperpage'], $pagevars['qsortorder'], $pagevars['qsortorderdecoded'],
                    $pagevars['cat'], $pagevars['recurse'], $pagevars['showhidden'], $pagevars['showquestiontext']);

    echo '</td></tr>';
    echo '</table>';

    print_footer($course);
?>
