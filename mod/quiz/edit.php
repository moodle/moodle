<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas and others                //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

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
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 *//** */

require_once('../../config.php');
require_once($CFG->dirroot . '/mod/quiz/editlib.php');
require_once($CFG->dirroot . '/question/category_class.php');
require_js(array('yui_yahoo', 'yui_dom-event', 'yui_container', 'yui_dragdrop'));

/**
 * Callback function called from question_list() function
 * (which is called from showbank())
 * Displays button in form with checkboxes for each question.
 */
function module_specific_buttons($cmid, $cmoptions) {
    global $THEME;
    if ($cmoptions->hasattempts) {
        $disabled = 'disabled="disabled" ';
    } else {
        $disabled = '';
    }
    $straddtoquiz = get_string('addtoquiz', 'quiz');
    $out = '<input type="submit" name="add" value="' . $THEME->larrow . ' ' . $straddtoquiz .
            '" ' . $disabled . "/>\n";
    return $out;
}

/**
 * Callback function called from question_list() function
 * (which is called from showbank())
 */
function module_specific_controls($totalnumber, $recurse, $category, $cmid, $cmoptions) {
    global $THEME, $QTYPES;
    $out = '';
    $catcontext = get_context_instance_by_id($category->contextid);
    if (has_capability('moodle/question:useall', $catcontext)) {
        if ($cmoptions->hasattempts) {
            $disabled = 'disabled="disabled"';
        } else {
            $disabled = '';
        }
        $randomusablequestions =
                $QTYPES['random']->get_usable_questions_from_category($category->id, $recurse, '0');
        $maxrand = count($randomusablequestions);
        if ($maxrand > 0) {
            for ($i = 1; $i <= min(10, $maxrand); $i++) {
                $randomcount[$i] = $i;
            }
            for ($i = 20; $i <= min(100, $maxrand); $i += 10) {
                $randomcount[$i] = $i;
            }
            $straddtoquiz = get_string('addtoquiz', 'quiz');
            $out = '<strong><label for="menurandomcount">'.get_string('addrandomfromcategory', 'quiz').
                    '</label></strong><br />';
            $out .= get_string('addrandom', 'quiz', choose_from_menu($randomcount,
                    'randomcount', '1', '', '', '', true, $cmoptions->hasattempts));
            $out .= '<input type="hidden" name="recurse" value="'.$recurse.'" />';
            $out .= '<input type="hidden" name="categoryid" value="' . $category->id . '" />';
            $out .= ' <input type="submit" name="addrandom" value="'.
                    $straddtoquiz.'" '.$disabled.' />';
            $out .= helpbutton('random', get_string('random', 'quiz'), 'quiz',
                    true, false, '', true);
        }
    }
    return $out;
}

list($thispageurl, $contexts, $cmid, $cm, $quiz, $pagevars) =
        question_edit_setup('editq', true);

$defaultcategoryobj = question_make_default_categories($contexts->all());
$defaultcategoryid = $defaultcategoryobj->id;
$defaultcategorycontext = $defaultcategoryobj->contextid;
$defaultcategory = $defaultcategoryid . ',' . $defaultcategorycontext;

//these params are only passed from page request to request while we stay on
//this page otherwise they would go in question_edit_setup
$quiz_reordertool = optional_param('reordertool', 0, PARAM_BOOL);
$quiz_qbanktool = optional_param('qbanktool', -1, PARAM_BOOL);
if ($quiz_qbanktool > -1) {
    $thispageurl->param('qbanktool', $quiz_qbanktool);
    set_user_preference('quiz_qbanktool_open', $quiz_qbanktool);
} else {
    $quiz_qbanktool = get_user_preferences('quiz_qbanktool_open', 0);
}

//will be set further down in the code
$quiz_has_attempts = false;

if ($quiz_reordertool != 0) {
    $thispageurl->param('reordertool', $quiz_reordertool);
}

$strquizzes = get_string('modulenameplural', 'quiz');
$strquiz = get_string('modulename', 'quiz');
$streditingquestions = get_string('editquestions', 'quiz');

//this just does not work for at least finnish, where words are conjugated:
//$streditingquiz = get_string('editinga', 'moodle', $strquiz);
$streditingquiz = get_string('editingquiz', 'quiz');
$strorderingquiz = get_string('orderingquiz', 'quiz');
$pagetitle = $streditingquiz;
if ($quiz_reordertool) {
    $pagetitle = $strorderingquiz;
}
// Get the course object and related bits.
$course = $DB->get_record('course', array('id' => $quiz->course));
if (!$course) {
    print_error('invalidcourseid', 'error');
}

$questionbank = new quiz_question_bank_view($contexts, $thispageurl, $course, $cm);

// Log this visit.
add_to_log($cm->course, 'quiz', 'editquestions',
            "view.php?id=$cm->id", "$quiz->id", $cm->id);

// You need mod/quiz:manage in addition to question capabilities to access this page.
require_capability('mod/quiz:manage', $contexts->lowest());

if (isset($quiz->instance) && empty($quiz->grades)) {  // Construct an array to hold all the grades.
    $quiz->grades = quiz_get_all_question_grades($quiz);
}

// Process commands ============================================================

// Get the list of question ids had their check-boxes ticked.
$selectedquestionids = array();
$params = (array) data_submitted();
foreach ($params as $key => $value) {
    if (preg_match('!^s([0-9]+)$!', $key, $matches)) {
        $selectedquestionids[] = $matches[1];
    }
}

if (($up = optional_param('up', false, PARAM_INT)) && confirm_sesskey()) {
    $quiz->questions = quiz_move_question_up($quiz->questions, $up);
    quiz_save_new_layout($quiz);
    redirect($thispageurl->out());
}

if (($down = optional_param('down', false, PARAM_INT)) && confirm_sesskey()) {
    $quiz->questions = quiz_move_question_down($quiz->questions, $down);
    quiz_save_new_layout($quiz);
    redirect($thispageurl->out());
}

if (optional_param('repaginate', false, PARAM_BOOL) && confirm_sesskey()) {
    // Re-paginate the quiz
    $questionsperpage = optional_param('questionsperpage', $quiz->questionsperpage, PARAM_INT);
    $quiz->questions = quiz_repaginate($quiz->questions, $questionsperpage );
    quiz_save_new_layout($quiz);
}

if (($addquestion = optional_param('addquestion', 0, PARAM_INT)) && confirm_sesskey()) {
/// Add a single question to the current quiz
    $addonpage = optional_param('addonpage', 0, PARAM_INT);
    quiz_add_quiz_question($addquestion, $quiz, $addonpage);
    quiz_update_sumgrades($quiz);
    quiz_delete_previews($quiz);
    redirect($thispageurl->out());
}

if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
/// Add selected questions to the current quiz
    $rawdata = (array) data_submitted();
    foreach ($rawdata as $key => $value) {    // Parse input for question ids
        if (preg_match('!^q([0-9]+)$!', $key, $matches)) {
            $key = $matches[1];
            quiz_add_quiz_question($key, $quiz);
        }
    }
    quiz_update_sumgrades($quiz);
    quiz_delete_previews($quiz);
    redirect($thispageurl->out());
}

$qcobject = new question_category_object($pagevars['cpage'], $thispageurl,
        $contexts->having_one_edit_tab_cap('categories'), $defaultcategoryid,
        $defaultcategory, null, $contexts->having_cap('moodle/question:add'));

$newrandomcategory = false;
$newquestioninfo = quiz_process_randomquestion_formdata($qcobject);
if ($newquestioninfo) {
    $newrandomcategory = $newquestioninfo->newrandomcategory;
    if (!$newrandomcategory) {
        print_error('cannotcreatecategory');
    } else {
        add_to_log($quiz->course, 'quiz', 'addcategory',
                "view.php?id=$cm->id", $newrandomcategory, $cm->id);
    }
}

if ((optional_param('addrandom', false, PARAM_BOOL) || $newrandomcategory) && confirm_sesskey()) {

    /// Add random questions to the quiz
    $recurse = optional_param('recurse', 0, PARAM_BOOL);
    $addonpage = optional_param('addonpage', 0, PARAM_INT);
    if ($newrandomcategory) {
        $categoryid = $newrandomcategory;
        $randomcount = optional_param('randomcount', 1, PARAM_INT);
    } else {
        $categoryid = required_param('categoryid', PARAM_INT);
        $randomcount = required_param('randomcount', PARAM_INT);
    }
    // load category
    $category = $DB->get_record('question_categories', array('id' => $categoryid));
    if (!$category) {
        print_error('invalidcategoryid', 'error');
    }
    $catcontext = get_context_instance_by_id($category->contextid);
    require_capability('moodle/question:useall', $catcontext);
    $category->name = $category->name;
    // Find existing random questions in this category that are
    // not used by any quiz.
    if ($existingquestions = $DB->get_records_sql(
            "SELECT q.id,q.qtype FROM {question} q
            WHERE qtype = '" . RANDOM . "'
                AND category = ?
                AND " . $DB->sql_compare_text('questiontext') . " = ?
                AND NOT EXISTS (SELECT * FROM {quiz_question_instances} WHERE question = q.id)
            ORDER BY id", array($category->id, $recurse))) {
        // Take as many of these as needed.
        while (($existingquestion = array_shift($existingquestions)) && $randomcount > 0) {
            quiz_add_quiz_question($existingquestion->id, $quiz, $addonpage);
            $randomcount--;
        }
    }

    // If more are needed, create them.
    if ($randomcount > 0) {
        $form->questiontext = $recurse; // we use the questiontext field
                // to store the info on whether to include
                // questions in subcategories
        $form->questiontextformat = 0;
        $form->image = '';
        $form->defaultgrade = 1;
        $form->hidden = 1;
        for ($i = 0; $i < $randomcount; $i++) {
            $form->category = $category->id . ',' . $category->contextid;
            $form->stamp = make_unique_id_code(); // Set the unique
                    //code (not to be changed)
            $question = new stdClass;
            $question->qtype = RANDOM;
            $question = $QTYPES[RANDOM]->save_question($question, $form,
                    $course);
            if(!isset($question->id)) {
                print_error('cannotinsertrandomquestion', 'quiz');
            }
            quiz_add_quiz_question($question->id, $quiz, $addonpage);
        }
    }

    quiz_update_sumgrades($quiz);
    quiz_delete_previews($quiz);
    redirect($thispageurl->out());
}

if (optional_param('addnewpagesafterselected', null) && !empty($selectedquestionids) && confirm_sesskey()) {
    foreach ($selectedquestionids as $questionid) {
        $quiz->questions = quiz_add_page_break_after($quiz->questions, $questionid);
    }
    quiz_save_new_layout($quiz);
    redirect($thispageurl->out());
}

$addpage = optional_param('addpage', false, PARAM_INT);
if ($addpage !== false && confirm_sesskey()) {
    $quiz->questions = quiz_add_page_break_at($quiz->questions, $addpage);
    quiz_save_new_layout($quiz);
    redirect($thispageurl->out());
}

$deleteemptypage = optional_param('deleteemptypage', false, PARAM_INT);
if (($deleteemptypage !== false) && confirm_sesskey()) {
    $quiz->questions = quiz_delete_empty_page($quiz->questions, $deleteemptypage);
    quiz_save_new_layout($quiz);
    redirect($thispageurl->out());
}

$remove = optional_param('remove', false, PARAM_INT);
if (($remove = optional_param('remove', false, PARAM_INT)) && confirm_sesskey()) {
    quiz_remove_question($quiz, $remove);
    quiz_update_sumgrades($quiz);
    quiz_delete_previews($quiz);
    redirect($thispageurl->out());
}

if (optional_param('quizdeleteselected', false, PARAM_BOOL) && !empty($selectedquestionids) && confirm_sesskey()) {
    foreach ($selectedquestionids as $questionid) {
        quiz_remove_question($quiz, $questionid);
    }
    quiz_update_sumgrades($quiz);
    quiz_delete_previews($quiz);
    redirect($thispageurl->out());
}

if (optional_param('savechanges', false, PARAM_BOOL) && confirm_sesskey()) {
    $oldquestions = explode(',', $quiz->questions); // the questions in the old order
    $questions = array(); // for questions in the new order
    $rawgrades = (array) data_submitted();
    $moveonpagequestions = array();
    $moveselectedonpage = 0;
    $moveselectedonpagetop = optional_param('moveselectedonpagetop', 0, PARAM_INT);
    $moveselectedonpagebottom = optional_param("moveselectedonpagebottom", 0, PARAM_INT);
    if ($moveselectedonpagetop) {
        $moveselectedonpage = $moveselectedonpagetop;
    } else if($moveselectedonpagebottom) {
        $moveselectedonpage = $moveselectedonpagebottom;
    }

    foreach ($rawgrades as $key => $value) {
        if (preg_match('!^g([0-9]+)$!', $key, $matches)) {
            /// Parse input for question -> grades
            $key = $matches[1];
            $quiz->grades[$key] = $value;
            quiz_update_question_instance($quiz->grades[$key], $key, $quiz->instance);

        } else if (preg_match('!^o([0-9]+)$!', $key, $matches)) {
            /// Parse input for ordering info
            $key = $matches[1];
            // Make sure two questions don't overwrite each other.
            // If we get a second
            // question with the same position, shift the second one
            // along to the next gap.
            while (array_key_exists($value, $questions)) {
                $value++;
            }
            $questions[$value] = $oldquestions[$key];
        }
    }

    // If ordering info was given, reorder the questions
    if ($questions) {
        ksort($questions);
        $quiz->questions = implode(',', $questions) . ',0';
        $quiz->questions = quiz_clean_layout($quiz->questions);
    }

    //get a list of questions to move, later to be added in the appropriate
    //place in the string
    if ($moveselectedonpage) {
        $questions = explode(',', $quiz->questions);
        foreach ($selectedquestionids as $page => $question) {
            //remove the questions from their original positions first
            while (($delpos = array_search($question, $questions)) !== FALSE) {
                //in case there are multiple instances because of an error, remove all
                unset($questions[$delpos]);
            }
        }
        //reindex
        foreach ($questions as $question) {
            $newquestions[] = $question;
        }
        $questions = $newquestions;

        //find all pagebreaks
        $pagecount = quiz_number_of_pages($quiz->questions);
        if ($moveselectedonpage > $pagecount) {
            // move to the last page is a page beyond last page was requested
            $moveselectedonpage = $pagecount;
        }
        if ($moveselectedonpage < 1) {
            $moveselectedonpage = 1;
        }
        $pagebreakpositions = array_keys($questions, 0);
        //move to the end of the selected page
        $moveselectedpos = $pagebreakpositions[$moveselectedonpage - 1];
        foreach ($selectedquestionids as $question) {
            array_splice($questions, $moveselectedpos, 0, $question);
            //place the next one after this one:
            $moveselectedpos++;
        }
        $quiz->questions = implode(',', $questions);
    }
    if ($moveselectedonpage || $questions) {
        if (!$DB->set_field('quiz', 'questions', $quiz->questions,
                array('id' => $quiz->instance))) {
            print_error('cannotsavequestion', 'quiz');
        }
    }
    // If rescaling is required save the new maximum
    $maxgrade = optional_param('maxgrade', -1, PARAM_NUMBER);
    if ($maxgrade >= 0) {
        if (!quiz_set_grade($maxgrade, $quiz)) {
            print_error('cannotsetgrade', 'quiz');
        }
    }

    quiz_update_sumgrades($quiz);
    quiz_delete_previews($quiz);
    redirect($thispageurl->out());
}

$questionbank->process_actions($thispageurl, $cm);

// End of process commands =====================================================

if (isset($quiz->instance) && $DB->record_exists_select('quiz_attempts',
        'quiz = ? AND preview = 0', array($quiz->instance))) {
    $questionbank->set_quiz_has_attempts(true);
}

// Print the header.
$questionbankmanagement = '<a href="'.$CFG->wwwroot.
        '/question/edit.php?courseid='.$course->id.'">'.
        get_string('questionbankmanagement', 'quiz').'</a> ';
$strupdatemodule = has_capability('moodle/course:manageactivities',
        $contexts->lowest()) ?
        update_module_button($cm->id, $course->id,
        get_string('modulename', 'quiz')) :
        "";
$navigation = build_navigation($pagetitle, $cm);
$localcss = '<link rel="stylesheet" type="text/css" href="'.$CFG->wwwroot.
        '/lib/yui/container/assets/container.css" />';
print_header_simple($pagetitle, '', $navigation, "", $localcss, true,
        $questionbankmanagement.$strupdatemodule);
//TODO: these skip links really need to be right after the opening of the body element,
// and preferably implemented in an <ul> element. See MDL-17730.
echo '<a href="#questionbank" class="skip">Question bank</a> '.
        '<a href="#quizcontentsblock" class="skip">Quiz contents</a>';
// Initialise the JavaScript.
$quizeditconfig = new stdClass;
$quizeditconfig->url = $thispageurl->out(false, array('qbanktool' => '0'));
$quizeditconfig->dialoglisteners =array();
$numberoflisteners = max(quiz_number_of_pages($quiz->questions), 1);
for ($pageiter = 1; $pageiter <= $numberoflisteners; $pageiter++) {
    $quizeditconfig->dialoglisteners[] = 'addrandomdialoglaunch_' . $pageiter;
}
print_js_config($quizeditconfig, 'quiz_edit_config');
require_js('mod/quiz/edit.js');

// Print the tabs.
$currenttab = 'edit';
$mode = 'editq';
if ($quiz_reordertool) {
    $mode = 'reorder';
}
include('tabs.php');

if ($quiz_qbanktool) {
    $bankclass = '';
    $quizcontentsclass = '';
} else {
    $bankclass = 'collapsed';
    $quizcontentsclass = 'quizwhenbankcollapsed';
}
print_side_block_start(get_string('questionbankcontents', 'quiz') .
        ' <a href="' . $thispageurl->out(false, array('qbanktool' => '1')) .
       '" id="showbankcmd">[' . get_string('show').
       ']</a>
       <a href="' . $thispageurl->out(false, array('qbanktool' => '0')) .
       '" id="hidebankcmd">[' . get_string('hide').
       ']</a>
       ', array('class' => 'questionbankwindow ' . $bankclass));

echo '<span id="questionbank"></span>';
echo '<div class="container">';
echo '<div id="module" class="module">';
echo '<div class="bd">';
$questionbank->display('editq',
        $pagevars['qpage'],
        $pagevars['qperpage'], $pagevars['qsortorder'],
        $pagevars['qsortorderdecoded'],
        $pagevars['cat'], $pagevars['recurse'], $pagevars['showhidden'],
        $pagevars['showquestiontext']);
echo '</div>';
echo '</div>';
echo '</div>';
print_side_block_end();

if (!$quizname = $DB->get_field($cm->modname, 'name', array('id' => $cm->instance))) {
    print_error('cannotmodulename');
}

echo '<div class="quizcontents ' . $quizcontentsclass . '" id="quizcontentsblock">';
$questionsperpagebool = ($quiz->questionsperpage < 1) ? 0 : 1;
if ($questionsperpagebool) {
    $repaginatingdisabledhtml = 'disabled="disabled"';
    $repaginatingdisabled = true;
} else {
    $repaginatingdisabledhtml = '';
    $repaginatingdisabled = false;
}
if ($quiz_reordertool) {
    echo '<div class="repaginatecommand"><button id="repaginatecommand" '.$repaginatingdisabledhtml.'>'.
            get_string('repaginatecommand', 'quiz').'...</button>';
    echo '</div>';
}
print_heading($pagetitle.": ".$quizname, 'left', 2);
helpbutton('editconcepts', get_string('basicideasofquiz', 'quiz'), 'quiz',
        true, get_string('basicideasofquiz', 'quiz'));

$notifystring = '';
if ($quiz_has_attempts) {
    $string = get_string('cannoteditafterattempts', 'quiz');
    $string .= '<br /><a href="report.php?mode=overview&amp;id=' . $cm->id . '">' .
        quiz_num_attempt_summary($quiz, $cm) . '</a><br />' ;
    $notifystring .= notify($string, 'notifyproblem', 'center', true);
}
if ($questionsperpagebool && $quiz_reordertool) {
    $updateurl = new moodle_url("$CFG->wwwroot/course/mod.php",
            array('return' => 'true', 'update' => $quiz->cmid, 'sesskey' => sesskey()));
    $linkstring = '<a href="'.$updateurl->out().'">';
    $linkstring .= get_string('updatethis', '', get_string('modulename', 'quiz'));
    $linkstring .= '</a>';
    $string = get_string('questionsperpageselected', 'quiz', $linkstring);
    $notifystring .= notify($string, 'notifyproblem', 'center', true);
}
if ($quiz->shufflequestions && $quiz_reordertool) {
    $updateurl = new moodle_url("$CFG->wwwroot/course/mod.php",
            array('return' => 'true', 'update' => $quiz->cmid, 'sesskey' => sesskey()));
    $linkstring = '<a href="'.$updateurl->out().'">';
    $linkstring .= get_string('updatethis', '', get_string('modulename', 'quiz'));
    $linkstring .= '</a>';
    $string = get_string('shufflequestionsselected', 'quiz', $linkstring);
    $notifystring .= notify($string, 'notifyproblem', 'center', true);
}
if (!empty($notifystring)) {
    //TODO: make the box closable so it is not in the way
    print_box_start();
    echo $notifystring;
    print_box_end();
}

if ($quiz_reordertool) {
    $perpage= array();
    $perpage[0] = get_string('allinone', 'quiz');
    for ($i = 1; $i <= 50; ++$i) {
        $perpage[$i] = $i;
    }
    $gostring = get_string('go');
    echo '<div id="repaginatedialog"><div class="hd">';
    echo get_string('repaginatecommand', 'quiz');
    echo '</div><div class="bd">';
    echo '<form action="edit.php" method="post">';
    echo '<fieldset class="invisiblefieldset">';
    echo $thispageurl->hidden_params_out();
    echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
    //YUI does not submit the value of the submit button so
            //we need to add the value:
    echo '<input type="hidden" name="repaginate" value="'.$gostring.'" />';
    print_string('repaginate', 'quiz',
            choose_from_menu($perpage, 'questionsperpage',
            $quiz->questionsperpage, '', '', '', true, $repaginatingdisabled));
    echo '<div class="quizquestionlistcontrols">';
    echo ' <input type="submit" name="repaginate" value="'. $gostring .'" '.$repaginatingdisabledhtml.' />';
    echo '</div></fieldset></form></div></div>';
}
$tabindex = 0;

if (!$quiz_reordertool) {
    quiz_print_grading_form($quiz, $thispageurl, $tabindex);
}
quiz_print_status_bar($quiz);
?>
<div class="<?php echo $currenttab; ?>">
<?php quiz_print_question_list($quiz, $thispageurl, true,
        $quiz_reordertool, $quiz_qbanktool, $quiz_has_attempts); ?>
</div>
<?php

// Close <div class="quizcontents">:
echo '</div>';

if (!$quiz_reordertool) {
    // display category adding UI
    ?>
<div id="randomquestiondialog">
<div class="hd"><?php print_string('addrandomquestiontoquiz', 'quiz', $quizname); ?>
<span id="pagenumber"><!-- TODO: insert pagenumber here via javascript -->
</span>
</div>
<div class="bd"><?php
$qcobject->display_randomquestion_user_interface();
?></div>
</div>
    <?php
}
print_footer($course);
?>