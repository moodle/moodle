<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


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
 * @package    mod_quiz
 * @copyright  1999 onwards Martin Dougiamas and others {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once('../../config.php');
require_once($CFG->dirroot . '/mod/quiz/editlib.php');
require_once($CFG->dirroot . '/mod/quiz/addrandomform.php');
require_once($CFG->dirroot . '/question/category_class.php');


/**
 * Callback function called from question_list() function
 * (which is called from showbank())
 * Displays button in form with checkboxes for each question.
 */
function module_specific_buttons($cmid, $cmoptions) {
    global $OUTPUT;
    $params = array(
        'type' => 'submit',
        'name' => 'add',
        'value' => $OUTPUT->larrow() . ' ' . get_string('addtoquiz', 'quiz'),
    );
    if ($cmoptions->hasattempts) {
        $params['disabled'] = 'disabled';
    }
    return html_writer::empty_tag('input', $params);
}

/**
 * Callback function called from question_list() function
 * (which is called from showbank())
 */
function module_specific_controls($totalnumber, $recurse, $category, $cmid, $cmoptions) {
    global $OUTPUT;
    $out = '';
    $catcontext = context::instance_by_id($category->contextid);
    if (has_capability('moodle/question:useall', $catcontext)) {
        if ($cmoptions->hasattempts) {
            $disabled = ' disabled="disabled"';
        } else {
            $disabled = '';
        }
        $randomusablequestions =
                question_bank::get_qtype('random')->get_available_questions_from_category(
                        $category->id, $recurse);
        $maxrand = count($randomusablequestions);
        if ($maxrand > 0) {
            for ($i = 1; $i <= min(10, $maxrand); $i++) {
                $randomcount[$i] = $i;
            }
            for ($i = 20; $i <= min(100, $maxrand); $i += 10) {
                $randomcount[$i] = $i;
            }
        } else {
            $randomcount[0] = 0;
            $disabled = ' disabled="disabled"';
        }

        $out = '<strong><label for="menurandomcount">'.get_string('addrandomfromcategory', 'quiz').
                '</label></strong><br />';
        $attributes = array();
        $attributes['disabled'] = $disabled ? 'disabled' : null;
        $select = html_writer::select($randomcount, 'randomcount', '1', null, $attributes);
        $out .= get_string('addrandom', 'quiz', $select);
        $out .= '<input type="hidden" name="recurse" value="'.$recurse.'" />';
        $out .= '<input type="hidden" name="categoryid" value="' . $category->id . '" />';
        $out .= ' <input type="submit" name="addrandom" value="'.
                get_string('addtoquiz', 'quiz').'"' . $disabled . ' />';
        $out .= $OUTPUT->help_icon('addarandomquestion', 'quiz');
    }
    return $out;
}

// These params are only passed from page request to request while we stay on
// this page otherwise they would go in question_edit_setup.
$quiz_reordertool = optional_param('reordertool', -1, PARAM_BOOL);
$quiz_qbanktool = optional_param('qbanktool', -1, PARAM_BOOL);
$scrollpos = optional_param('scrollpos', '', PARAM_INT);

list($thispageurl, $contexts, $cmid, $cm, $quiz, $pagevars) =
        question_edit_setup('editq', '/mod/quiz/edit.php', true);

$defaultcategoryobj = question_make_default_categories($contexts->all());
$defaultcategory = $defaultcategoryobj->id . ',' . $defaultcategoryobj->contextid;

if ($quiz_qbanktool > -1) {
    $thispageurl->param('qbanktool', $quiz_qbanktool);
    set_user_preference('quiz_qbanktool_open', $quiz_qbanktool);
} else {
    $quiz_qbanktool = get_user_preferences('quiz_qbanktool_open', 0);
}

if ($quiz_reordertool > -1) {
    $thispageurl->param('reordertool', $quiz_reordertool);
    set_user_preference('quiz_reordertab', $quiz_reordertool);
} else {
    $quiz_reordertool = get_user_preferences('quiz_reordertab', 0);
}

$canaddrandom = $contexts->have_cap('moodle/question:useall');
$canaddquestion = (bool) $contexts->having_add_and_use();

$quizhasattempts = quiz_has_attempts($quiz->id);

$PAGE->set_url($thispageurl);

// Get the course object and related bits.
$course = $DB->get_record('course', array('id' => $quiz->course));
if (!$course) {
    print_error('invalidcourseid', 'error');
}

$questionbank = new quiz_question_bank_view($contexts, $thispageurl, $course, $cm, $quiz);
$questionbank->set_quiz_has_attempts($quizhasattempts);

// You need mod/quiz:manage in addition to question capabilities to access this page.
require_capability('mod/quiz:manage', $contexts->lowest());

// Log this visit.
$params = array(
    'courseid' => $course->id,
    'context' => $contexts->lowest(),
    'other' => array(
        'quizid' => $quiz->id
    )
);
$event = \mod_quiz\event\edit_page_viewed::create($params);
$event->trigger();

// Process commands ============================================================

// Get the list of question ids had their check-boxes ticked.
$selectedslots = array();
$params = (array) data_submitted();
foreach ($params as $key => $value) {
    if (preg_match('!^s([0-9]+)$!', $key, $matches)) {
        $selectedslots[] = $matches[1];
    }
}

$afteractionurl = new moodle_url($thispageurl);
if ($scrollpos) {
    $afteractionurl->param('scrollpos', $scrollpos);
}
if (($up = optional_param('up', false, PARAM_INT)) && confirm_sesskey()) {
    quiz_move_question_up($quiz, $up);
    quiz_delete_previews($quiz);
    redirect($afteractionurl);
}

if (($down = optional_param('down', false, PARAM_INT)) && confirm_sesskey()) {
    quiz_move_question_down($quiz, $down);
    quiz_delete_previews($quiz);
    redirect($afteractionurl);
}

if (optional_param('repaginate', false, PARAM_BOOL) && confirm_sesskey()) {
    // Re-paginate the quiz.
    $questionsperpage = optional_param('questionsperpage', $quiz->questionsperpage, PARAM_INT);
    quiz_repaginate_questions($quiz->id, $questionsperpage );
    quiz_delete_previews($quiz);
    redirect($afteractionurl);
}

if (($addquestion = optional_param('addquestion', 0, PARAM_INT)) && confirm_sesskey()) {
    // Add a single question to the current quiz.
    quiz_require_question_use($addquestion);
    $addonpage = optional_param('addonpage', 0, PARAM_INT);
    quiz_add_quiz_question($addquestion, $quiz, $addonpage);
    quiz_delete_previews($quiz);
    quiz_update_sumgrades($quiz);
    $thispageurl->param('lastchanged', $addquestion);
    redirect($afteractionurl);
}

if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
    // Add selected questions to the current quiz.
    $rawdata = (array) data_submitted();
    foreach ($rawdata as $key => $value) { // Parse input for question ids.
        if (preg_match('!^q([0-9]+)$!', $key, $matches)) {
            $key = $matches[1];
            quiz_require_question_use($key);
            quiz_add_quiz_question($key, $quiz);
        }
    }
    quiz_delete_previews($quiz);
    quiz_update_sumgrades($quiz);
    redirect($afteractionurl);
}

if ((optional_param('addrandom', false, PARAM_BOOL)) && confirm_sesskey()) {
    // Add random questions to the quiz.
    $recurse = optional_param('recurse', 0, PARAM_BOOL);
    $addonpage = optional_param('addonpage', 0, PARAM_INT);
    $categoryid = required_param('categoryid', PARAM_INT);
    $randomcount = required_param('randomcount', PARAM_INT);
    quiz_add_random_questions($quiz, $addonpage, $categoryid, $randomcount, $recurse);

    quiz_delete_previews($quiz);
    quiz_update_sumgrades($quiz);
    redirect($afteractionurl);
}

if (optional_param('addnewpagesafterselected', null, PARAM_CLEAN) &&
        !empty($selectedslots) && confirm_sesskey()) {
    foreach ($selectedslots as $slot) {
        quiz_add_page_break_after_slot($quiz, $slot);
    }
    quiz_delete_previews($quiz);
    redirect($afteractionurl);
}

$addpage = optional_param('addpage', false, PARAM_INT);
if ($addpage !== false && confirm_sesskey()) {
    quiz_add_page_break_after_slot($quiz, $addpage);
    quiz_delete_previews($quiz);
    redirect($afteractionurl);
}

$deleteemptypage = optional_param('deleteemptypage', false, PARAM_INT);
if (($deleteemptypage !== false) && confirm_sesskey()) {
    quiz_delete_empty_page($quiz, $deleteemptypage);
    quiz_delete_previews($quiz);
    redirect($afteractionurl);
}

$remove = optional_param('remove', false, PARAM_INT);
if ($remove && confirm_sesskey() && quiz_has_question_use($quiz, $remove)) {
    // Remove a question from the quiz.
    // We require the user to have the 'use' capability on the question,
    // so that then can add it back if they remove the wrong one by mistake,
    // but, if the question is missing, it can always be removed.
    quiz_remove_slot($quiz, $remove);
    quiz_delete_previews($quiz);
    quiz_update_sumgrades($quiz);
    redirect($afteractionurl);
}

if (optional_param('quizdeleteselected', false, PARAM_BOOL) &&
        !empty($selectedslots) && confirm_sesskey()) {
    // Work backwards, since removing a question renumbers following slots.
    foreach (array_reverse($selectedslots) as $slot) {
        if (quiz_has_question_use($quiz, $slot)) {
            quiz_remove_slot($quiz, $slot);
        }
    }
    quiz_delete_previews($quiz);
    quiz_update_sumgrades($quiz);
    redirect($afteractionurl);
}

if (optional_param('savechanges', false, PARAM_BOOL) && confirm_sesskey()) {
    $deletepreviews = false;
    $recomputesummarks = false;

    $rawdata = (array) data_submitted();
    $moveonpagequestions = array();
    $moveselectedonpage = optional_param('moveselectedonpagetop', 0, PARAM_INT);
    if (!$moveselectedonpage) {
        $moveselectedonpage = optional_param('moveselectedonpagebottom', 0, PARAM_INT);
    }

    $newslotorder = array();
    foreach ($rawdata as $key => $value) {
        if (preg_match('!^g([0-9]+)$!', $key, $matches)) {
            // Parse input for question -> grades.
            $slotnumber = $matches[1];
            $newgrade = unformat_float($value);
            quiz_update_slot_maxmark($DB->get_record('quiz_slots',
                    array('quizid' => $quiz->id, 'slot' => $slotnumber), '*', MUST_EXIST), $newgrade);
            $deletepreviews = true;
            $recomputesummarks = true;

        } else if (preg_match('!^o(pg)?([0-9]+)$!', $key, $matches)) {
            // Parse input for ordering info.
            $slotnumber = $matches[2];
            // Make sure two questions don't overwrite each other. If we get a second
            // question with the same position, shift the second one along to the next gap.
            $value = clean_param($value, PARAM_INT);
            while (array_key_exists($value, $newslotorder)) {
                $value++;
            }
            if ($matches[1]) {
                // This is a page-break entry.
                $newslotorder[$value] = 0;
            } else {
                $newslotorder[$value] = $slotnumber;
            }
            $deletepreviews = true;
        }
    }

    if ($moveselectedonpage) {

        // Make up a $newslotorder, then let the next if statement do the work.
        $oldslots = $DB->get_records('quiz_slots', array('quizid' => $quiz->id), 'slot');

        $beforepage = array();
        $onpage = array();
        $afterpage = array();
        foreach ($oldslots as $oldslot) {
            if (in_array($oldslot->slot, $selectedslots)) {
                $onpage[] = $oldslot;
            } else if ($oldslot->page <= $moveselectedonpage) {
                $beforepage[] = $oldslot;
            } else {
                $afterpage[] = $oldslot;
            }
        }

        $newslotorder = array();
        $currentpage = 1;
        $index = 10;
        foreach ($beforepage as $slot) {
            while ($currentpage < $slot->page) {
                $newslotorder[$index] = 0;
                $index += 10;
                $currentpage += 1;
            }
            $newslotorder[$index] = $slot->slot;
            $index += 10;
        }

        while ($currentpage < $moveselectedonpage) {
            $newslotorder[$index] = 0;
            $index += 10;
            $currentpage += 1;
        }
        foreach ($onpage as $slot) {
            $newslotorder[$index] = $slot->slot;
            $index += 10;
        }

        foreach ($afterpage as $slot) {
            while ($currentpage < $slot->page) {
                $newslotorder[$index] = 0;
                $index += 10;
                $currentpage += 1;
            }
            $newslotorder[$index] = $slot->slot;
            $index += 10;
        }
    }

    // If ordering info was given, reorder the questions.
    if ($newslotorder) {
        ksort($newslotorder);
        $currentpage = 1;
        $currentslot = 1;
        $slotreorder = array();
        $slotpages = array();
        foreach ($newslotorder as $slotnumber) {
            if ($slotnumber == 0) {
                $currentpage += 1;
                continue;
            }
            $slotreorder[$slotnumber] = $currentslot;
            $slotpages[$currentslot] = $currentpage;
            $currentslot += 1;
        }
        $trans = $DB->start_delegated_transaction();
        update_field_with_unique_index('quiz_slots',
                'slot', $slotreorder, array('quizid' => $quiz->id));
        foreach ($slotpages as $slotnumber => $page) {
            $DB->set_field('quiz_slots', 'page', $page, array('quizid' => $quiz->id, 'slot' => $slotnumber));
        }
        $trans->allow_commit();
        $deletepreviews = true;
    }

    // If rescaling is required save the new maximum.
    $maxgrade = unformat_float(optional_param('maxgrade', -1, PARAM_RAW));
    if ($maxgrade >= 0) {
        quiz_set_grade($maxgrade, $quiz);
    }

    if ($deletepreviews) {
        quiz_delete_previews($quiz);
    }
    if ($recomputesummarks) {
        quiz_update_sumgrades($quiz);
        quiz_update_all_attempt_sumgrades($quiz);
        quiz_update_all_final_grades($quiz);
        quiz_update_grades($quiz, 0, true);
    }
    redirect($afteractionurl);
}

$questionbank->process_actions($thispageurl, $cm);

// End of process commands =====================================================

$PAGE->requires->skip_link_to('questionbank',
        get_string('skipto', 'access', get_string('questionbank', 'question')));
$PAGE->requires->skip_link_to('quizcontentsblock',
        get_string('skipto', 'access', get_string('questionsinthisquiz', 'quiz')));
$PAGE->set_title(get_string('editingquizx', 'quiz', format_string($quiz->name)));
$PAGE->set_heading($course->fullname);
$node = $PAGE->settingsnav->find('mod_quiz_edit', navigation_node::TYPE_SETTING);
if ($node) {
    $node->make_active();
}
echo $OUTPUT->header();

// Initialise the JavaScript.
$quizeditconfig = new stdClass();
$quizeditconfig->url = $thispageurl->out(true, array('qbanktool' => '0'));
$quizeditconfig->dialoglisteners = array();
$numberoflisteners = $DB->get_field_sql("
    SELECT COALESCE(MAX(page), 1)
      FROM {quiz_slots}
     WHERE quizid = ?", array($quiz->id));

for ($pageiter = 1; $pageiter <= $numberoflisteners; $pageiter++) {
    $quizeditconfig->dialoglisteners[] = 'addrandomdialoglaunch_' . $pageiter;
}
$PAGE->requires->data_for_js('quiz_edit_config', $quizeditconfig);
$PAGE->requires->js('/question/qengine.js');
$module = array(
    'name'      => 'mod_quiz_edit',
    'fullpath'  => '/mod/quiz/edit.js',
    'requires'  => array('yui2-dom', 'yui2-event', 'yui2-container'),
    'strings'   => array(),
    'async'     => false,
);
$PAGE->requires->js_init_call('quiz_edit_init', null, false, $module);

// Print the tabs to switch mode.
if ($quiz_reordertool) {
    $currenttab = 'reorder';
} else {
    $currenttab = 'edit';
}
$tabs = array(array(
    new tabobject('edit', new moodle_url($thispageurl,
            array('reordertool' => 0)), get_string('editingquiz', 'quiz')),
    new tabobject('reorder', new moodle_url($thispageurl,
            array('reordertool' => 1)), get_string('orderingquiz', 'quiz')),
));
print_tabs($tabs, $currenttab);

if ($quiz_qbanktool) {
    $bankclass = '';
    $quizcontentsclass = '';
} else {
    $bankclass = 'collapsed ';
    $quizcontentsclass = 'quizwhenbankcollapsed';
}

echo '<div class="questionbankwindow ' . $bankclass . 'block">';
echo '<div class="header"><div class="title"><h2>';
echo get_string('questionbankcontents', 'quiz') .
       '&nbsp;[<a href="' . $thispageurl->out(true, array('qbanktool' => '1')) .
       '" id="showbankcmd">' . get_string('show').
       '</a><a href="' . $thispageurl->out(true, array('qbanktool' => '0')) .
       '" id="hidebankcmd">' . get_string('hide').
       '</a>]';
echo '</h2></div></div><div class="content">';

echo '<span id="questionbank"></span>';
echo '<div class="container">';
echo '<div id="module" class="module">';
echo '<div class="bd">';
$questionbank->display('editq',
        $pagevars['qpage'],
        $pagevars['qperpage'],
        $pagevars['cat'], $pagevars['recurse'], $pagevars['showhidden'],
        $pagevars['qbshowtext']);
echo '</div>';
echo '</div>';
echo '</div>';

echo '</div></div>';

echo '<div class="quizcontents ' . $quizcontentsclass . '" id="quizcontentsblock">';
if ($quiz->shufflequestions) {
    $repaginatingdisabledhtml = 'disabled="disabled"';
    $repaginatingdisabled = true;
} else {
    $repaginatingdisabledhtml = '';
    $repaginatingdisabled = false;
}
if ($quiz_reordertool) {
    echo '<div class="repaginatecommand"><button id="repaginatecommand" ' .
            $repaginatingdisabledhtml.'>'.
            get_string('repaginatecommand', 'quiz').'...</button>';
    echo '</div>';
}

if ($quiz_reordertool) {
    echo $OUTPUT->heading_with_help(get_string('orderingquizx', 'quiz', format_string($quiz->name)),
            'orderandpaging', 'quiz');
} else {
    echo $OUTPUT->heading(get_string('editingquizx', 'quiz', format_string($quiz->name)), 2);
    echo $OUTPUT->help_icon('editingquiz', 'quiz', get_string('basicideasofquiz', 'quiz'));
}
quiz_print_status_bar($quiz);

$tabindex = 0;
quiz_print_grading_form($quiz, $thispageurl, $tabindex);

$notifystrings = array();
if ($quizhasattempts) {
    $reviewlink = quiz_attempt_summary_link_to_reports($quiz, $cm, $contexts->lowest());
    $notifystrings[] = get_string('cannoteditafterattempts', 'quiz', $reviewlink);
}
if ($quiz->shufflequestions) {
    $updateurl = new moodle_url("$CFG->wwwroot/course/mod.php",
            array('return' => 'true', 'update' => $quiz->cmid, 'sesskey' => sesskey()));
    $updatelink = '<a href="'.$updateurl->out().'">' . get_string('updatethis', '',
            get_string('modulename', 'quiz')) . '</a>';
    $notifystrings[] = get_string('shufflequestionsselected', 'quiz', $updatelink);
}
if (!empty($notifystrings)) {
    echo $OUTPUT->box('<p>' . implode('</p><p>', $notifystrings) . '</p>', 'statusdisplay');
}

if ($quiz_reordertool) {
    $perpage = array();
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
    echo html_writer::input_hidden_params($thispageurl);
    echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
    // YUI does not submit the value of the submit button so we need to add the value.
    echo '<input type="hidden" name="repaginate" value="'.$gostring.'" />';
    $attributes = array();
    $attributes['disabled'] = $repaginatingdisabledhtml ? 'disabled' : null;
    $select = html_writer::select(
            $perpage, 'questionsperpage', $quiz->questionsperpage, null, $attributes);
    print_string('repaginate', 'quiz', $select);
    echo '<div class="quizquestionlistcontrols">';
    echo ' <input type="submit" name="repaginate" value="'. $gostring . '" ' .
            $repaginatingdisabledhtml.' />';
    echo '</div></fieldset></form></div></div>';
}

if ($quiz_reordertool) {
    echo '<div class="reorder">';
} else {
    echo '<div class="editq">';
}

quiz_print_question_list($quiz, $thispageurl, true, $quiz_reordertool, $quiz_qbanktool,
        $quizhasattempts, $defaultcategoryobj, $canaddquestion, $canaddrandom);
echo '</div>';

// Close <div class="quizcontents">.
echo '</div>';

if (!$quiz_reordertool && $canaddrandom) {
    $randomform = new quiz_add_random_form(new moodle_url('/mod/quiz/addrandom.php'), $contexts);
    $randomform->set_data(array(
        'category' => $pagevars['cat'],
        'returnurl' => $thispageurl->out_as_local_url(false),
        'cmid' => $cm->id,
    ));
    ?>
    <div id="randomquestiondialog">
    <div class="hd"><?php print_string('addrandomquestiontoquiz', 'quiz', $quiz->name); ?>
    <span id="pagenumber"><!-- JavaScript will insert the page number here. -->
    </span>
    </div>
    <div class="bd"><?php
    $randomform->display();
    ?></div>
    </div>
    <?php
}
echo $OUTPUT->footer();
