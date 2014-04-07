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
 * This contains functions that are called from within the quiz module only
 * Functions that are also called by core Moodle are in {@link lib.php}
 * This script also loads the code in {@link questionlib.php} which holds
 * the module-indpendent code for handling questions and which in turn
 * initialises all the questiontype classes.
 *
 * @package    mod_quiz
 * @copyright  1999 onwards Martin Dougiamas and others {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/locallib.php');

define('NUM_QS_TO_SHOW_IN_RANDOM', 3);

/**
 * Verify that the question exists, and the user has permission to use it.
 * Does not return. Throws an exception if the question cannot be used.
 * @param int $questionid The id of the question.
 */
function quiz_require_question_use($questionid) {
    global $DB;
    $question = $DB->get_record('question', array('id' => $questionid), '*', MUST_EXIST);
    question_require_capability_on($question, 'use');
}

/**
 * Verify that the question exists, and the user has permission to use it.
 * @param object $quiz the quiz settings.
 * @param int $slot which question in the quiz to test.
 * @return bool whether the user can use this question.
 */
function quiz_has_question_use($quiz, $slot) {
    global $DB;
    $question = $DB->get_record_sql("
            SELECT q.*
              FROM {quiz_slots} slot
              JOIN {question} q ON q.id = slot.questionid
             WHERE slot.quizid = ? AND slot.slot = ?", array($quiz->id, $slot));
    if (!$question) {
        return false;
    }
    return question_has_capability_on($question, 'use');
}

/**
 * Remove a question from a quiz
 * @param object $quiz the quiz object.
 * @param int $questionid The id of the question to be deleted.
 */
function quiz_remove_slot($quiz, $slotnumber) {
    global $DB;

    $slot = $DB->get_record('quiz_slots', array('quizid' => $quiz->id, 'slot' => $slotnumber));
    $maxslot = $DB->get_field_sql('SELECT MAX(slot) FROM {quiz_slots} WHERE quizid = ?', array($quiz->id));
    if (!$slot) {
        return;
    }

    $trans = $DB->start_delegated_transaction();
    $DB->delete_records('quiz_slots', array('id' => $slot->id));
    for ($i = $slot->slot + 1; $i <= $maxslot; $i++) {
        $DB->set_field('quiz_slots', 'slot', $i - 1,
                array('quizid' => $quiz->id, 'slot' => $i));
    }
    $trans->allow_commit();
}

/**
 * Remove an empty page from the quiz layout. If that is not possible, do nothing.
 * @param object $quiz the quiz settings.
 * @param int $pagenumber the page number to delete.
 */
function quiz_delete_empty_page($quiz, $pagenumber) {
    global $DB;

    if ($DB->record_exists('quiz_slots', array('quizid' => $quiz->id, 'page' => $pagenumber))) {
        // This was not an empty page.
        return;
    }

    $DB->execute('UPDATE {quiz_slots} SET page = page - 1 WHERE quizid = ? AND page > ?',
            array($quiz->id, $pagenumber));
}

/**
 * Add a question to a quiz
 *
 * Adds a question to a quiz by updating $quiz as well as the
 * quiz and quiz_slots tables. It also adds a page break if required.
 * @param int $questionid The id of the question to be added
 * @param object $quiz The extended quiz object as used by edit.php
 *      This is updated by this function
 * @param int $page Which page in quiz to add the question on. If 0 (default),
 *      add at the end
 * @param float $maxmark The maximum mark to set for this question. (Optional,
 *      defaults to question.defaultmark.
 * @return bool false if the question was already in the quiz
 */
function quiz_add_quiz_question($questionid, $quiz, $page = 0, $maxmark = null) {
    global $DB;
    $slots = $DB->get_records('quiz_slots', array('quizid' => $quiz->id),
            'slot', 'questionid, slot, page, id');
    if (array_key_exists($questionid, $slots)) {
        return false;
    }

    $trans = $DB->start_delegated_transaction();

    $maxpage = 1;
    $numonlastpage = 0;
    foreach ($slots as $slot) {
        if ($slot->page > $maxpage) {
            $maxpage = $slot->page;
            $numonlastpage = 1;
        } else {
            $numonlastpage += 1;
        }
    }

    // Add the new question instance.
    $slot = new stdClass();
    $slot->quizid = $quiz->id;
    $slot->questionid = $questionid;

    if ($maxmark !== null) {
        $slot->maxmark = $maxmark;
    } else {
        $slot->maxmark = $DB->get_field('question', 'defaultmark', array('id' => $questionid));
    }

    if (is_int($page) && $page >= 1) {
        // Adding on a given page.
        $lastslotbefore = 0;
        foreach (array_reverse($slots) as $otherslot) {
            if ($otherslot->page > $page) {
                $DB->set_field('quiz_slots', 'slot', $otherslot->slot + 1, array('id' => $otherslot->id));
            } else {
                $lastslotbefore = $otherslot->slot;
                break;
            }
        }
        $slot->slot = $lastslotbefore + 1;
        $slot->page = min($page, $maxpage + 1);

    } else {
        $lastslot = end($slots);
        if ($lastslot) {
            $slot->slot = $lastslot->slot + 1;
        } else {
            $slot->slot = 1;
        }
        if ($quiz->questionsperpage && $numonlastpage >= $quiz->questionsperpage) {
            $slot->page = $maxpage + 1;
        } else {
            $slot->page = $maxpage;
        }
    }

    $DB->insert_record('quiz_slots', $slot);
    $trans->allow_commit();
}

/**
 * Add a random question to the quiz at a given point.
 * @param object $quiz the quiz settings.
 * @param int $addonpage the page on which to add the question.
 * @param int $categoryid the question category to add the question from.
 * @param int $number the number of random questions to add.
 * @param bool $includesubcategories whether to include questoins from subcategories.
 */
function quiz_add_random_questions($quiz, $addonpage, $categoryid, $number,
        $includesubcategories) {
    global $DB;

    $category = $DB->get_record('question_categories', array('id' => $categoryid));
    if (!$category) {
        print_error('invalidcategoryid', 'error');
    }

    $catcontext = context::instance_by_id($category->contextid);
    require_capability('moodle/question:useall', $catcontext);

    // Find existing random questions in this category that are
    // not used by any quiz.
    if ($existingquestions = $DB->get_records_sql(
            "SELECT q.id, q.qtype FROM {question} q
            WHERE qtype = 'random'
                AND category = ?
                AND " . $DB->sql_compare_text('questiontext') . " = ?
                AND NOT EXISTS (
                        SELECT *
                          FROM {quiz_slots}
                         WHERE questionid = q.id)
            ORDER BY id", array($category->id, $includesubcategories))) {
        // Take as many of these as needed.
        while (($existingquestion = array_shift($existingquestions)) && $number > 0) {
            quiz_add_quiz_question($existingquestion->id, $quiz, $addonpage);
            $number -= 1;
        }
    }

    if ($number <= 0) {
        return;
    }

    // More random questions are needed, create them.
    for ($i = 0; $i < $number; $i += 1) {
        $form = new stdClass();
        $form->questiontext = array('text' => $includesubcategories, 'format' => 0);
        $form->category = $category->id . ',' . $category->contextid;
        $form->defaultmark = 1;
        $form->hidden = 1;
        $form->stamp = make_unique_id_code(); // Set the unique code (not to be changed).
        $question = new stdClass();
        $question->qtype = 'random';
        $question = question_bank::get_qtype('random')->save_question($question, $form);
        if (!isset($question->id)) {
            print_error('cannotinsertrandomquestion', 'quiz');
        }
        quiz_add_quiz_question($question->id, $quiz, $addonpage);
    }
}

/**
 * Add a page break after a particular slot.
 * @param object $quiz the quiz settings.
 * @param int $slot the slot to add the page break after.
 */
function quiz_add_page_break_after_slot($quiz, $slot) {
    global $DB;

    $DB->execute('UPDATE {quiz_slots} SET page = page + 1 WHERE quizid = ? AND slot > ?',
            array($quiz->id, $slot));
}

/**
 * Change the max mark for a slot.
 *
 * Saves changes to the question grades in the quiz_slots table and any
 * corresponding question_attempts.
 * It does not update 'sumgrades' in the quiz table.
 *
 * @param stdClass $slot    row from the quiz_slots table.
 * @param float    $maxmark the new maxmark.
 */
function quiz_update_slot_maxmark($slot, $maxmark) {
    global $DB;

    if (abs($maxmark - $slot->maxmark) < 1e-7) {
        // Grade has not changed. Nothing to do.
        return;
    }

    $slot->maxmark = $maxmark;
    $DB->update_record('quiz_slots', $slot);
    question_engine::set_max_mark_in_attempts(new qubaids_for_quiz($slot->quizid),
            $slot->slot, $maxmark);
}

/**
 * Private function used by the following two.
 * @param object $quiz the quiz settings.
 * @param int $slotnumber the slot to move up.
 * @param int $shift +1 means move down, -1 means move up.
 */
function _quiz_move_question($quiz, $slotnumber, $shift) {
    global $DB;

    if (!$slotnumber || !($shift == 1 || $shift == -1)) {
        return;
    }

    $slot = $DB->get_record('quiz_slots',
            array('quizid' => $quiz->id, 'slot' => $slotnumber));
    if (!$slot) {
        return;
    }

    $otherslot = $DB->get_record('quiz_slots',
            array('quizid' => $quiz->id, 'slot' => $slotnumber + $shift));
    if (!$otherslot) {
        // Must be first or last question being moved further that way if we can.
        if ($shift + $slot->page > 0) {
            $DB->set_field('quiz_slots', 'page', $slot->page + $shift, array('id' => $slot->id));
        }
        return;
    }

    if ($otherslot->page != $slot->page) {
        $DB->set_field('quiz_slots', 'page', $slot->page + $shift, array('id' => $slot->id));
        return;
    }

    $trans = $DB->start_delegated_transaction();
    $DB->set_field('quiz_slots', 'slot', -1,               array('id' => $slot->id));
    $DB->set_field('quiz_slots', 'slot', $slot->slot,      array('id' => $otherslot->id));
    $DB->set_field('quiz_slots', 'slot', $otherslot->slot, array('id' => $slot->id));
    $trans->allow_commit();
}

/**
 * Move a particular question one space earlier in the quiz.
 * If that is not possible, do nothing.
 * @param object $quiz the quiz settings.
 * @param int $slot the slot to move up.
 */
function quiz_move_question_up($quiz, $slot) {
    _quiz_move_question($quiz, $slot, -1);
}

/**
 * Move a particular question one space later in the quiz.
 * If that is not possible, do nothing.
 * @param object $quiz the quiz settings.
 * @param int $slot the slot to move down.
 */
function quiz_move_question_down($quiz, $slot) {
    return _quiz_move_question($quiz, $slot, 1);
}

/**
 * Prints a list of quiz questions for the edit.php main view for edit
 * ($reordertool = false) and order and paging ($reordertool = true) tabs
 *
 * @param object $quiz The quiz settings.
 * @param moodle_url $pageurl The url of the current page with the parameters required
 *     for links returning to the current page, as a moodle_url object
 * @param bool $allowdelete Indicates whether the delete icons should be displayed
 * @param bool $reordertool  Indicates whether the reorder tool should be displayed
 * @param bool $quiz_qbanktool  Indicates whether the question bank should be displayed
 * @param bool $hasattempts  Indicates whether the quiz has attempts
 * @param object $defaultcategoryobj
 * @param bool $canaddquestion is the user able to add and use questions anywere?
 * @param bool $canaddrandom is the user able to add random questions anywere?
 */
function quiz_print_question_list($quiz, $pageurl, $allowdelete, $reordertool,
        $quiz_qbanktool, $hasattempts, $defaultcategoryobj, $canaddquestion, $canaddrandom) {
    global $CFG, $DB, $OUTPUT;
    $strorder = get_string('order');
    $strquestionname = get_string('questionname', 'quiz');
    $strmaxmark = get_string('markedoutof', 'question');
    $strremove = get_string('remove', 'quiz');
    $stredit = get_string('edit');
    $strview = get_string('view');
    $straction = get_string('action');
    $strmove = get_string('move');
    $strmoveup = get_string('moveup');
    $strmovedown = get_string('movedown');
    $strsave = get_string('save', 'quiz');
    $strreorderquestions = get_string('reorderquestions', 'quiz');

    $strselectall = get_string('selectall', 'quiz');
    $strselectnone = get_string('selectnone', 'quiz');
    $strtype = get_string('type', 'quiz');
    $strpreview = get_string('preview', 'quiz');

    $questions = $DB->get_records_sql("SELECT slot.slot, q.*, qc.contextid, slot.page, slot.maxmark
                          FROM {quiz_slots} slot
                     LEFT JOIN {question} q ON q.id = slot.questionid
                     LEFT JOIN {question_categories} qc ON qc.id = q.category
                         WHERE slot.quizid = ?
                      ORDER BY slot.slot", array($quiz->id));

    $lastindex = count($questions) - 1;

    $disabled = '';
    $pagingdisabled = '';
    if ($hasattempts) {
        $disabled = 'disabled="disabled"';
    }
    if ($hasattempts || $quiz->shufflequestions) {
        $pagingdisabled = 'disabled="disabled"';
    }

    $reordercontrolssetdefaultsubmit = '<div style="display:none;">' .
        '<input type="submit" name="savechanges" value="' .
        $strreorderquestions . '" ' . $pagingdisabled . ' /></div>';
    $reordercontrols1 = '<div class="addnewpagesafterselected">' .
        '<input type="submit" name="addnewpagesafterselected" value="' .
        get_string('addnewpagesafterselected', 'quiz') . '"  ' .
        $pagingdisabled . ' /></div>';
    $reordercontrols1 .= '<div class="quizdeleteselected">' .
        '<input type="submit" name="quizdeleteselected" ' .
        'onclick="return confirm(\'' .
        get_string('areyousureremoveselected', 'quiz') . '\');" value="' .
        get_string('removeselected', 'quiz') . '"  ' . $disabled . ' /></div>';

    $a = '<input name="moveselectedonpagetop" type="text" size="2" ' .
        $pagingdisabled . ' />';
    $b = '<input name="moveselectedonpagebottom" type="text" size="2" ' .
        $pagingdisabled . ' />';

    $reordercontrols2top = '<div class="moveselectedonpage">' .
        '<label>' . get_string('moveselectedonpage', 'quiz', $a) . '</label>' .
        '<input type="submit" name="savechanges" value="' .
        $strmove . '"  ' . $pagingdisabled . ' />' . '
        <br /><input type="submit" name="savechanges" value="' .
        $strreorderquestions . '" /></div>';
    $reordercontrols2bottom = '<div class="moveselectedonpage">' .
        '<input type="submit" name="savechanges" value="' .
        $strreorderquestions . '" /><br />' .
        '<label>' . get_string('moveselectedonpage', 'quiz', $b) . '</label>' .
        '<input type="submit" name="savechanges" value="' .
        $strmove . '"  ' . $pagingdisabled . ' /> ' . '</div>';

    $reordercontrols3 = '<a href="javascript:select_all_in(\'FORM\', null, ' .
            '\'quizquestions\');">' .
            $strselectall . '</a> /';
    $reordercontrols3.=    ' <a href="javascript:deselect_all_in(\'FORM\', ' .
            'null, \'quizquestions\');">' .
            $strselectnone . '</a>';

    $reordercontrolstop = '<div class="reordercontrols">' .
            $reordercontrolssetdefaultsubmit .
            $reordercontrols1 . $reordercontrols2top . $reordercontrols3 . "</div>";
    $reordercontrolsbottom = '<div class="reordercontrols">' .
            $reordercontrolssetdefaultsubmit .
            $reordercontrols2bottom . $reordercontrols1 . $reordercontrols3 . "</div>";

    if ($reordertool) {
        echo '<form method="post" action="edit.php" id="quizquestions"><div>';

        echo html_writer::input_hidden_params($pageurl);
        echo '<input type="hidden" name="sesskey" value="' . sesskey() . '" />';

        echo $reordercontrolstop;
    }

    // Build fake order for backwards compatibility.
    $currentpage = 1;
    $order = array();
    foreach ($questions as $question) {
        while ($question->page > $currentpage) {
            $currentpage += 1;
            $order[] = 0;
        }
        $order[] = $question->slot;
    }
    $order[] = 0;

    // The current question ordinal (no descriptions).
    $qno = 1;
    // The current question (includes questions and descriptions).
    $questioncount = 0;
    // The current page number in iteration.
    $pagecount = 0;

    $pageopen = false;
    $lastslot = 0;

    $returnurl = $pageurl->out_as_local_url(false);
    $questiontotalcount = count($order);

    $lastquestion = new stdClass();
    $lastquestion->slot = 0; // Used to get the add page here buttons right.
    foreach ($order as $count => $qnum) { // Note: $qnum is acutally slot number, if it is not 0.

        $reordercheckbox = '';
        $reordercheckboxlabel = '';
        $reordercheckboxlabelclose = '';

        // If the questiontype is missing change the question type.
        if ($qnum && $questions[$qnum]->qtype === null) {
            $questions[$qnum]->id = $qnum;
            $questions[$qnum]->category = 0;
            $questions[$qnum]->qtype = 'missingtype';
            $questions[$qnum]->name = get_string('missingquestion', 'quiz');
            $questions[$qnum]->questiontext = ' ';
            $questions[$qnum]->questiontextformat = FORMAT_HTML;
            $questions[$qnum]->length = 1;

        } else if ($qnum && !question_bank::qtype_exists($questions[$qnum]->qtype)) {
            $questions[$qnum]->qtype = 'missingtype';
        }

        if ($qnum != 0 || ($qnum == 0 && !$pageopen)) {
            // This is either a question or a page break after another (no page is currently open).
            if (!$pageopen) {
                // If no page is open, start display of a page.
                $pagecount++;
                echo  '<div class="quizpage"><span class="pagetitle">' .
                        get_string('page') . '&nbsp;' . $pagecount .
                        '</span><div class="pagecontent">';
                $pageopen = true;
            }
            if ($qnum == 0) {
                // This is the second successive page break. Tell the user the page is empty.
                echo '<div class="pagestatus">';
                print_string('noquestionsonpage', 'quiz');
                echo '</div>';
                if ($allowdelete) {
                    echo '<div class="quizpagedelete">';
                    echo $OUTPUT->action_icon($pageurl->out(true,
                            array('deleteemptypage' => $pagecount, 'sesskey' => sesskey())),
                            new pix_icon('t/delete', $strremove),
                            new component_action('click',
                                    'M.core_scroll_manager.save_scroll_action'),
                            array('title' => $strremove));
                    echo '</div>';
                }
            }

            if ($qnum != 0) {
                $question = $questions[$qnum];
                $questionparams = array(
                        'returnurl' => $returnurl,
                        'cmid' => $quiz->cmid,
                        'id' => $question->id);
                $questionurl = new moodle_url('/question/question.php',
                        $questionparams);
                $questioncount++;

                // This is an actual question.
                ?>
<div class="question">
    <div class="questioncontainer <?php echo $question->qtype; ?>">
        <div class="qnum">
                <?php
                $reordercheckbox = '';
                $reordercheckboxlabel = '';
                $reordercheckboxlabelclose = '';
                if ($reordertool) {
                    $reordercheckbox = '<input type="checkbox" name="s' . $question->slot .
                        '" id="s' . $question->slot . '" />';
                    $reordercheckboxlabel = '<label for="s' . $question->slot . '">';
                    $reordercheckboxlabelclose = '</label>';
                }
                if ($question->length == 0) {
                    $qnodisplay = get_string('infoshort', 'quiz');
                } else if ($quiz->shufflequestions) {
                    $qnodisplay = '?';
                } else {
                    if ($qno > 999 || ($reordertool && $qno > 99)) {
                        $qnodisplay = html_writer::tag('small', $qno);
                    } else {
                        $qnodisplay = $qno;
                    }
                    $qno += $question->length;
                }
                echo $reordercheckboxlabel . $qnodisplay . $reordercheckboxlabelclose .
                        $reordercheckbox;

                ?>
        </div>
        <div class="content">
            <div class="questioncontrols">
                <?php
                if ($count != 0) {
                    if (!$hasattempts) {
                        $upbuttonclass = '';
                        echo $OUTPUT->action_icon($pageurl->out(true,
                                array('up' => $question->slot, 'sesskey' => sesskey())),
                                new pix_icon('t/up', $strmoveup),
                                new component_action('click',
                                        'M.core_scroll_manager.save_scroll_action'),
                                array('title' => $strmoveup));
                    }

                }
                if (!$hasattempts) {
                    echo $OUTPUT->action_icon($pageurl->out(true,
                            array('down' => $question->slot, 'sesskey' => sesskey())),
                            new pix_icon('t/down', $strmovedown),
                            new component_action('click',
                                    'M.core_scroll_manager.save_scroll_action'),
                            array('title' => $strmovedown));
                }
                if ($allowdelete && ($question->qtype == 'missingtype' ||
                        question_has_capability_on($question, 'use', $question->category))) {
                    // Remove from quiz, not question delete.
                    if (!$hasattempts) {
                        echo $OUTPUT->action_icon($pageurl->out(true,
                                array('remove' => $question->slot, 'sesskey' => sesskey())),
                                new pix_icon('t/delete', $strremove),
                                new component_action('click',
                                        'M.core_scroll_manager.save_scroll_action'),
                                array('title' => $strremove));
                    }
                }
                ?>
            </div><?php
                if (!in_array($question->qtype, array('description', 'missingtype')) && !$reordertool) {
                    ?>
<div class="points">
<form method="post" action="edit.php" class="quizsavegradesform"><div>
    <fieldset class="invisiblefieldset" style="display: block;">
    <label for="<?php echo 'inputq' . $question->slot; ?>"><?php echo $strmaxmark; ?></label>:<br />
    <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />
    <?php echo html_writer::input_hidden_params($pageurl); ?>
    <input type="hidden" name="savechanges" value="save" />
                    <?php
                    echo '<input type="text" name="g' . $question->slot .
                            '" id="inputq' . $question->slot .
                            '" size="' . ($quiz->decimalpoints + 2) .
                            '" value="' . (0 + $question->maxmark) .
                            '" tabindex="' . ($lastindex + $qno) . '" />';
                    ?>
        <input type="submit" class="pointssubmitbutton" value="<?php echo $strsave; ?>" />
    </fieldset>
                    <?php
                    if ($question->qtype == 'random') {
                        echo '<a href="' . $questionurl->out() .
                                '" class="configurerandomquestion">' .
                                get_string("configurerandomquestion", "quiz") . '</a>';
                    }

                    ?>
</div>
</form>

            </div>
                    <?php
                } else if ($reordertool) {
                    if ($qnum) {
                        ?>
<div class="qorder">
                        <?php
                        echo '<label class="accesshide" for="o' . $question->slot . '">' .
                                get_string('questionposition', 'quiz', $qnodisplay) . '</label>';
                        echo '<input type="text" name="o' . $question->slot .
                                '" id="o' . $question->id . '"' .
                                '" size="2" value="' . (10*$count + 10) .
                                '" tabindex="' . ($lastindex + $qno) . '" />';
                        ?>
</div>
                        <?php
                    }
                }
                ?>
            <div class="questioncontentcontainer">
                <?php
                if ($question->qtype == 'random') { // It is a random question.
                    if (!$reordertool) {
                        quiz_print_randomquestion($question, $pageurl, $quiz, $quiz_qbanktool);
                    } else {
                        quiz_print_randomquestion_reordertool($question, $pageurl, $quiz);
                    }
                } else { // It is a single question.
                    if (!$reordertool) {
                        quiz_print_singlequestion($question, $returnurl, $quiz);
                    } else {
                        quiz_print_singlequestion_reordertool($question, $returnurl, $quiz);
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

                <?php
            }
        }
        // A page break: end the existing page.
        if ($qnum == 0) {
            if ($pageopen) {
                if (!$reordertool && !($quiz->shufflequestions &&
                        $count < $questiontotalcount - 1)) {
                    quiz_print_pagecontrols($quiz, $pageurl, $pagecount,
                            $hasattempts, $defaultcategoryobj, $canaddquestion, $canaddrandom);
                } else if ($count < $questiontotalcount - 1) {
                    // Do not include the last page break for reordering
                    // to avoid creating a new extra page in the end.
                    echo '<input type="hidden" name="opg' . $pagecount . '" size="2" value="' .
                            (10*$count + 10) . '" />';
                }
                echo "</div></div>";

                if (!$reordertool && !$quiz->shufflequestions && $count < $questiontotalcount - 1) {
                    echo $OUTPUT->container_start('addpage');
                    $url = new moodle_url($pageurl->out_omit_querystring(),
                            array('cmid' => $quiz->cmid, 'courseid' => $quiz->course,
                                    'addpage' => $lastquestion->slot, 'sesskey' => sesskey()));
                    echo $OUTPUT->single_button($url, get_string('addpagehere', 'quiz'), 'post',
                            array('disabled' => $hasattempts,
                            'actions' => array(new component_action('click',
                                    'M.core_scroll_manager.save_scroll_action'))));
                    echo $OUTPUT->container_end();
                }
                $pageopen = false;
                $count++;
            }
        }

        if ($qnum != 0) {
            $lastquestion = $question;
        }

    }
    if ($reordertool) {
        echo $reordercontrolsbottom;
        echo '</div></form>';
    }
}

/**
 * Print all the controls for adding questions directly into the
 * specific page in the edit tab of edit.php
 *
 * @param object $quiz The quiz settings.
 * @param moodle_url $pageurl The url of the current page with the parameters required
 *     for links returning to the current page, as a moodle_url object
 * @param int $page the current page number.
 * @param bool $hasattempts  Indicates whether the quiz has attempts
 * @param object $defaultcategoryobj
 * @param bool $canaddquestion is the user able to add and use questions anywere?
 * @param bool $canaddrandom is the user able to add random questions anywere?
 */
function quiz_print_pagecontrols($quiz, $pageurl, $page, $hasattempts,
        $defaultcategoryobj, $canaddquestion, $canaddrandom) {
    global $CFG, $OUTPUT;
    static $randombuttoncount = 0;
    $randombuttoncount++;
    echo '<div class="pagecontrols">';

    // Get the current context.
    $thiscontext = context_course::instance($quiz->course);
    $contexts = new question_edit_contexts($thiscontext);

    // Get the default category.
    list($defaultcategoryid) = explode(',', $pageurl->param('cat'));
    if (empty($defaultcategoryid)) {
        $defaultcategoryid = $defaultcategoryobj->id;
    }

    if ($canaddquestion) {
        // Create the url the question page will return to.
        $returnurladdtoquiz = new moodle_url($pageurl, array('addonpage' => $page));

        // Print a button linking to the choose question type page.
        $returnurladdtoquiz = $returnurladdtoquiz->out_as_local_url(false);
        $newquestionparams = array('returnurl' => $returnurladdtoquiz,
                'cmid' => $quiz->cmid, 'appendqnumstring' => 'addquestion');
        create_new_question_button($defaultcategoryid, $newquestionparams,
                get_string('addaquestion', 'quiz'),
                get_string('createquestionandadd', 'quiz'), $hasattempts);
    }

    if ($hasattempts) {
        $disabled = 'disabled="disabled"';
    } else {
        $disabled = '';
    }
    if ($canaddrandom) {
    ?>
    <div class="singlebutton">
        <form class="randomquestionform" action="<?php echo $CFG->wwwroot;
                ?>/mod/quiz/addrandom.php" method="get">
            <div>
                <input type="hidden" class="addonpage_formelement" name="addonpage" value="<?php
                        echo $page; ?>" />
                <input type="hidden" name="cmid" value="<?php echo $quiz->cmid; ?>" />
                <input type="hidden" name="courseid" value="<?php echo $quiz->course; ?>" />
                <input type="hidden" name="category" value="<?php
                        echo $pageurl->param('cat'); ?>" />
                <input type="hidden" name="returnurl" value="<?php
                        echo s(str_replace($CFG->wwwroot, '', $pageurl->out(false))); ?>" />
                <input type="submit" id="addrandomdialoglaunch_<?php
                        echo $randombuttoncount; ?>" value="<?php
                        echo get_string('addarandomquestion', 'quiz'); ?>" <?php
                        echo " $disabled"; ?> />
            </div>
        </form>
    </div>
    <?php echo $OUTPUT->help_icon('addarandomquestion', 'quiz');
    }
    echo "\n</div>";
}

/**
 * Print a given single question in quiz for the edit tab of edit.php.
 * Meant to be used from quiz_print_question_list()
 *
 * @param object $question A question object from the database questions table
 * @param object $returnurl The url to get back to this page, for example after editing.
 * @param object $quiz The quiz in the context of which the question is being displayed
 */
function quiz_print_singlequestion($question, $returnurl, $quiz) {
    echo '<div class="singlequestion ' . $question->qtype . '">';
    echo quiz_question_edit_button($quiz->cmid, $question, $returnurl,
            quiz_question_tostring($question) . ' ');
    echo '<span class="questiontype">';
    echo print_question_icon($question);
    echo ' ' . question_bank::get_qtype_name($question->qtype) . '</span>';
    echo '<span class="questionpreview">' .
            quiz_question_preview_button($quiz, $question, true) . '</span>';
    echo "</div>\n";
}
/**
 * Print a given random question in quiz for the edit tab of edit.php.
 * Meant to be used from quiz_print_question_list()
 *
 * @param object $question A question object from the database questions table
 * @param object $questionurl The url of the question editing page as a moodle_url object
 * @param object $quiz The quiz in the context of which the question is being displayed
 * @param bool $quiz_qbanktool Indicate to this function if the question bank window open
 */
function quiz_print_randomquestion(&$question, &$pageurl, &$quiz, $quiz_qbanktool) {
    global $DB, $OUTPUT;
    echo '<div class="quiz_randomquestion">';

    if (!$category = $DB->get_record('question_categories',
            array('id' => $question->category))) {
        echo $OUTPUT->notification('Random question category not found!');
        return;
    }

    echo '<div class="randomquestionfromcategory">';
    echo print_question_icon($question);
    print_random_option_icon($question);
    echo ' ' . get_string('randomfromcategory', 'quiz') . '</div>';

    $a = new stdClass();
    $a->arrow = $OUTPUT->rarrow();
    $strshowcategorycontents = get_string('showcategorycontents', 'quiz', $a);

    $openqbankurl = $pageurl->out(true, array('qbanktool' => 1,
            'cat' => $category->id . ',' . $category->contextid));
    $linkcategorycontents = ' <a href="' . $openqbankurl . '">' . $strshowcategorycontents . '</a>';

    echo '<div class="randomquestioncategory">';
    echo '<a href="' . $openqbankurl . '" title="' . $strshowcategorycontents . '">' .
            $category->name . '</a>';
    echo '<span class="questionpreview">' .
            quiz_question_preview_button($quiz, $question, true) . '</span>';
    echo '</div>';

    $questionids = question_bank::get_qtype('random')->get_available_questions_from_category(
            $category->id, $question->questiontext == '1', '0');
    $questioncount = count($questionids);

    echo '<div class="randomquestionqlist">';
    if ($questioncount == 0) {
        // No questions in category, give an error plus instructions.
        echo '<span class="error">';
        print_string('noquestionsnotinuse', 'quiz');
        echo '</span>';
        echo '<br />';

        // Embed the link into the string with instructions.
        $a = new stdClass();
        $a->catname = '<strong>' . $category->name . '</strong>';
        $a->link = $linkcategorycontents;
        echo get_string('addnewquestionsqbank', 'quiz', $a);

    } else {
        // Category has questions.

        // Get a sample from the database.
        $questionidstoshow = array_slice($questionids, 0, NUM_QS_TO_SHOW_IN_RANDOM);
        $questionstoshow = $DB->get_records_list('question', 'id', $questionidstoshow,
                '', 'id, qtype, name, questiontext, questiontextformat');

        // Then list them.
        echo '<ul>';
        foreach ($questionstoshow as $question) {
            echo '<li>' . quiz_question_tostring($question, true) . '</li>';
        }

        // Finally display the total number.
        echo '<li class="totalquestionsinrandomqcategory">';
        if ($questioncount > NUM_QS_TO_SHOW_IN_RANDOM) {
            echo '... ';
        }
        print_string('totalquestionsinrandomqcategory', 'quiz', $questioncount);
        echo ' ' . $linkcategorycontents;
        echo '</li>';
        echo '</ul>';
    }

    echo '</div>';
    echo '<div class="randomquestioncategorycount">';
    echo '</div>';
    echo '</div>';
}

/**
 * Print a given single question in quiz for the reordertool tab of edit.php.
 * Meant to be used from quiz_print_question_list()
 *
 * @param object $question A question object from the database questions table
 * @param object $questionurl The url of the question editing page as a moodle_url object
 * @param object $quiz The quiz in the context of which the question is being displayed
 */
function quiz_print_singlequestion_reordertool($question, $returnurl, $quiz) {
    echo '<div class="singlequestion ' . $question->qtype . '">';
    echo '<label for="s' . $question->id . '">';
    echo print_question_icon($question);
    echo ' ' . quiz_question_tostring($question);
    echo '</label>';
    echo '<span class="questionpreview">' .
            quiz_question_action_icons($quiz, $quiz->cmid, $question, $returnurl) . '</span>';
    echo "</div>\n";
}

/**
 * Print a given random question in quiz for the reordertool tab of edit.php.
 * Meant to be used from quiz_print_question_list()
 *
 * @param object $question A question object from the database questions table
 * @param object $questionurl The url of the question editing page as a moodle_url object
 * @param object $quiz The quiz in the context of which the question is being displayed
 */
function quiz_print_randomquestion_reordertool($question, $pageurl, $quiz) {
    global $DB, $OUTPUT;

    // Load the category, and the number of available questions in it.
    if (!$category = $DB->get_record('question_categories', array('id' => $question->category))) {
        echo $OUTPUT->notification('Random question category not found!');
        return;
    }
    $questioncount = count(question_bank::get_qtype(
            'random')->get_available_questions_from_category(
            $category->id, $question->questiontext == '1', '0'));

    $reordercheckboxlabel = '<label for="s' . $question->id . '">';
    $reordercheckboxlabelclose = '</label>';

    echo '<div class="quiz_randomquestion">';
    echo '<div class="randomquestionfromcategory">';
    echo $reordercheckboxlabel;
    echo print_question_icon($question);
    print_random_option_icon($question);

    if ($questioncount == 0) {
        echo '<span class="error">';
        print_string('empty', 'quiz');
        echo '</span> ';
    }

    print_string('random', 'quiz');
    echo ": $reordercheckboxlabelclose</div>";

    echo '<div class="randomquestioncategory">';
    echo $reordercheckboxlabel . $category->name . $reordercheckboxlabelclose;
    echo '<span class="questionpreview">';
    echo quiz_question_preview_button($quiz, $question, false);
    echo '</span>';
    echo "</div>";

    echo '<div class="randomquestioncategorycount">';
    echo '</div>';
    echo '</div>';
}

/**
 * Print an icon to indicate the 'include subcategories' state of a random question.
 * @param $question the random question.
 */
function print_random_option_icon($question) {
    global $OUTPUT;
    if (!empty($question->questiontext)) {
        $icon = 'withsubcat';
        $tooltip = get_string('randomwithsubcat', 'quiz');
    } else {
        $icon = 'nosubcat';
        $tooltip = get_string('randomnosubcat', 'quiz');
    }
    echo '<img src="' . $OUTPUT->pix_url('i/' . $icon) . '" alt="' .
            $tooltip . '" title="' . $tooltip . '" class="uihint" />';
}

/**
 * Creates a textual representation of a question for display.
 *
 * @param object $question A question object from the database questions table
 * @param bool $showicon If true, show the question's icon with the question. False by default.
 * @param bool $showquestiontext If true (default), show question text after question name.
 *       If false, show only question name.
 * @param bool $return If true (default), return the output. If false, print it.
 */
function quiz_question_tostring($question, $showicon = false,
        $showquestiontext = true, $return = true) {
    global $COURSE;
    $result = '';
    $result .= '<span class="questionname">';
    if ($showicon) {
        $result .= print_question_icon($question, true);
        echo ' ';
    }
    $result .= shorten_text(format_string($question->name), 200) . '</span>';
    if ($showquestiontext) {
        $questiontext = question_utils::to_plain_text($question->questiontext,
                $question->questiontextformat, array('noclean' => true, 'para' => false));
        $questiontext = shorten_text($questiontext, 200);
        $result .= '<span class="questiontext">';
        if (!empty($questiontext)) {
            $result .= s($questiontext);
        } else {
            $result .= '<span class="error">';
            $result .= get_string('questiontextisempty', 'quiz');
            $result .= '</span>';
        }
        $result .= '</span>';
    }
    if ($return) {
        return $result;
    } else {
        echo $result;
    }
}

/**
 * A column type for the add this question to the quiz.
 *
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_bank_add_to_quiz_action_column extends question_bank_action_column_base {
    protected $stradd;

    public function init() {
        parent::init();
        $this->stradd = get_string('addtoquiz', 'quiz');
    }

    public function get_name() {
        return 'addtoquizaction';
    }

    protected function display_content($question, $rowclasses) {
        if (!question_has_capability_on($question, 'use')) {
            return;
        }
        // For RTL languages: switch right and left arrows.
        if (right_to_left()) {
            $movearrow = 't/removeright';
        } else {
            $movearrow = 't/moveleft';
        }
        $this->print_icon($movearrow, $this->stradd, $this->qbank->add_to_quiz_url($question->id));
    }

    public function get_required_fields() {
        return array('q.id');
    }
}

/**
 * A column type for the name followed by the start of the question text.
 *
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_bank_question_name_text_column extends question_bank_question_name_column {
    public function get_name() {
        return 'questionnametext';
    }

    protected function display_content($question, $rowclasses) {
        echo '<div>';
        $labelfor = $this->label_for($question);
        if ($labelfor) {
            echo '<label for="' . $labelfor . '">';
        }
        echo quiz_question_tostring($question, false, true, true);
        if ($labelfor) {
            echo '</label>';
        }
        echo '</div>';
    }

    public function get_required_fields() {
        $fields = parent::get_required_fields();
        $fields[] = 'q.questiontext';
        $fields[] = 'q.questiontextformat';
        return $fields;
    }
}

/**
 * Subclass to customise the view of the question bank for the quiz editing screen.
 *
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_question_bank_view extends question_bank_view {
    protected $quizhasattempts = false;
    /** @var object the quiz settings. */
    protected $quiz = false;
    /** @var int The maximum displayed length of the category info. */
    const MAX_TEXT_LENGTH = 200;

    /**
     * Constructor
     * @param question_edit_contexts $contexts
     * @param moodle_url $pageurl
     * @param object $course course settings
     * @param object $cm activity settings.
     * @param object $quiz quiz settings.
     */
    public function __construct($contexts, $pageurl, $course, $cm, $quiz) {
        parent::__construct($contexts, $pageurl, $course, $cm);
        $this->quiz = $quiz;
    }

    protected function known_field_types() {
        $types = parent::known_field_types();
        $types[] = new question_bank_add_to_quiz_action_column($this);
        $types[] = new question_bank_question_name_text_column($this);
        return $types;
    }

    protected function wanted_columns() {
        return array('addtoquizaction', 'checkbox', 'qtype', 'questionnametext',
                'editaction', 'copyaction', 'previewaction');
    }

    /**
     * Specify the column heading
     *
     * @return string Column name for the heading
     */
    protected function heading_column() {
        return 'questionnametext';
    }

    protected function default_sort() {
        $this->requiredcolumns['qtype'] = $this->knowncolumntypes['qtype'];
        $this->requiredcolumns['questionnametext'] = $this->knowncolumntypes['questionnametext'];
        return array('qtype' => 1, 'questionnametext' => 1);
    }

    /**
     * Let the question bank display know whether the quiz has been attempted,
     * hence whether some bits of UI, like the add this question to the quiz icon,
     * should be displayed.
     * @param bool $quizhasattempts whether the quiz has attempts.
     */
    public function set_quiz_has_attempts($quizhasattempts) {
        $this->quizhasattempts = $quizhasattempts;
        if ($quizhasattempts && isset($this->visiblecolumns['addtoquizaction'])) {
            unset($this->visiblecolumns['addtoquizaction']);
        }
    }

    public function preview_question_url($question) {
        return quiz_question_preview_url($this->quiz, $question);
    }

    public function add_to_quiz_url($questionid) {
        global $CFG;
        $params = $this->baseurl->params();
        $params['addquestion'] = $questionid;
        $params['sesskey'] = sesskey();
        return new moodle_url('/mod/quiz/edit.php', $params);
    }

    public function display($tabname, $page, $perpage, $cat,
            $recurse, $showhidden, $showquestiontext) {
        global $OUTPUT;
        if ($this->process_actions_needing_ui()) {
            return;
        }

        $editcontexts = $this->contexts->having_one_edit_tab_cap($tabname);
        array_unshift($this->searchconditions,
                new \core_question\bank\search\hidden_condition(!$showhidden));
        array_unshift($this->searchconditions,
                new \core_question\bank\search\category_condition($cat, $recurse,
                        $editcontexts, $this->baseurl, $this->course, self::MAX_TEXT_LENGTH));

        echo $OUTPUT->box_start('generalbox questionbank');
        $this->display_options_form($showquestiontext);

        // Continues with list of questions.
        $this->display_question_list($this->contexts->having_one_edit_tab_cap($tabname),
                $this->baseurl, $cat, $this->cm, $recurse, $page,
                $perpage, $showhidden, $showquestiontext,
                $this->contexts->having_cap('moodle/question:add'));

        echo $OUTPUT->box_end();
    }

    /**
     * prints a form to choose categories
     * @param string $categoryandcontext 'categoryID,contextID'.
     * @deprecated since Moodle 2.6 MDL-40313.
     * @see \core_question\bank\search\category_condition
     * @todo MDL-41978 This will be deleted in Moodle 2.8
     */
    protected function print_choose_category_message($categoryandcontext) {
        global $OUTPUT;
        debugging('print_choose_category_message() is deprecated, ' .
                'please use \core_question\bank\search\category_condition instead.', DEBUG_DEVELOPER);
        echo $OUTPUT->box_start('generalbox questionbank');
        $this->display_category_form($this->contexts->having_one_edit_tab_cap('edit'),
                $this->baseurl, $categoryandcontext);
        echo "<p style=\"text-align:center;\"><b>";
        print_string('selectcategoryabove', 'question');
        echo "</b></p>";
        echo $OUTPUT->box_end();
    }

    /**
     * Display the form with options for which questions are displayed and how they are displayed.
     * This differs from parent display_options_form only in that it does not have the checkbox to show the question text.
     * @param bool $showquestiontext Display the text of the question within the list. (Currently ignored)
     */
    protected function display_options_form($showquestiontext) {
        global $PAGE;
        echo html_writer::start_tag('form', array('method' => 'get',
                'action' => new moodle_url('/mod/quiz/edit.php'), 'id' => 'displayoptions'));
        echo html_writer::start_div();
        foreach ($this->searchconditions as $searchcondition) {
            echo $searchcondition->display_options($this);
        }
        $this->display_advanced_search_form();
        $go = html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('go')));
        echo html_writer::tag('noscript', html_writer::tag('div', $go), array('class' => 'inline'));
        echo html_writer::end_div();
        echo html_writer::end_tag('form');
        $PAGE->requires->yui_module('moodle-question-searchform', 'M.question.searchform.init');
    }

    protected function print_category_info($category) {
        $formatoptions = new stdClass();
        $formatoptions->noclean = true;
        $strcategory = get_string('category', 'quiz');
        echo '<div class="categoryinfo"><div class="categorynamefieldcontainer">' .
                $strcategory;
        echo ': <span class="categorynamefield">';
        echo shorten_text(strip_tags(format_string($category->name)), 60);
        echo '</span></div><div class="categoryinfofieldcontainer">' .
                '<span class="categoryinfofield">';
        echo shorten_text(strip_tags(format_text($category->info, $category->infoformat,
                $formatoptions, $this->course->id)), 200);
        echo '</span></div></div>';
    }

    protected function display_options($recurse, $showhidden, $showquestiontext) {
        debugging('display_options() is deprecated, see display_options_form() instead.', DEBUG_DEVELOPER);
        echo '<form method="get" action="edit.php" id="displayoptions">';
        echo "<fieldset class='invisiblefieldset'>";
        echo html_writer::input_hidden_params($this->baseurl,
                array('recurse', 'showhidden', 'qbshowtext'));
        $this->display_category_form_checkbox('recurse', $recurse,
                get_string('includesubcategories', 'question'));
        $this->display_category_form_checkbox('showhidden', $showhidden,
                get_string('showhidden', 'question'));
        echo '<noscript><div class="centerpara"><input type="submit" value="' .
                get_string('go') . '" />';
        echo '</div></noscript></fieldset></form>';
    }
}

/**
 * Prints the form for setting a quiz' overall grade
 *
 * @param object $quiz The quiz object of the quiz in question
 * @param object $pageurl The url of the current page with the parameters required
 *     for links returning to the current page, as a moodle_url object
 * @param int $tabindex The tabindex to start from for the form elements created
 * @return int The tabindex from which the calling page can continue, that is,
 *      the last value used +1.
 */
function quiz_print_grading_form($quiz, $pageurl, $tabindex) {
    global $OUTPUT;
    $strsave = get_string('save', 'quiz');
    echo '<form method="post" action="edit.php" class="quizsavegradesform"><div>';
    echo '<fieldset class="invisiblefieldset" style="display: block;">';
    echo "<input type=\"hidden\" name=\"sesskey\" value=\"" . sesskey() . "\" />";
    echo html_writer::input_hidden_params($pageurl);
    $a = '<input type="text" id="inputmaxgrade" name="maxgrade" size="' .
            ($quiz->decimalpoints + 2) . '" tabindex="' . $tabindex
         . '" value="' . quiz_format_grade($quiz, $quiz->grade) . '" />';
    echo '<label for="inputmaxgrade">' . get_string('maximumgradex', '', $a) . "</label>";
    echo '<input type="hidden" name="savechanges" value="save" />';
    echo '<input type="submit" value="' . $strsave . '" />';
    echo '</fieldset>';
    echo "</div></form>\n";
    return $tabindex + 1;
}

/**
 * Print the status bar
 *
 * @param object $quiz The quiz object of the quiz in question
 */
function quiz_print_status_bar($quiz) {
    global $DB;

    $bits = array();

    $bits[] = html_writer::tag('span',
            get_string('totalmarksx', 'quiz', quiz_format_grade($quiz, $quiz->sumgrades)),
            array('class' => 'totalpoints'));

    $bits[] = html_writer::tag('span',
            get_string('numquestionsx', 'quiz', $DB->count_records('quiz_slots', array('quizid' => $quiz->id))),
            array('class' => 'numberofquestions'));

    $timenow = time();

    // Exact open and close dates for the tool-tip.
    $dates = array();
    if ($quiz->timeopen > 0) {
        if ($timenow > $quiz->timeopen) {
            $dates[] = get_string('quizopenedon', 'quiz', userdate($quiz->timeopen));
        } else {
            $dates[] = get_string('quizwillopen', 'quiz', userdate($quiz->timeopen));
        }
    }
    if ($quiz->timeclose > 0) {
        if ($timenow > $quiz->timeclose) {
            $dates[] = get_string('quizclosed', 'quiz', userdate($quiz->timeclose));
        } else {
            $dates[] = get_string('quizcloseson', 'quiz', userdate($quiz->timeclose));
        }
    }
    if (empty($dates)) {
        $dates[] = get_string('alwaysavailable', 'quiz');
    }
    $tooltip = implode(', ', $dates);

    // Brief summary on the page.
    if ($timenow < $quiz->timeopen) {
        $currentstatus = get_string('quizisclosedwillopen', 'quiz',
                userdate($quiz->timeopen, get_string('strftimedatetimeshort', 'langconfig')));
    } else if ($quiz->timeclose && $timenow <= $quiz->timeclose) {
        $currentstatus = get_string('quizisopenwillclose', 'quiz',
                userdate($quiz->timeclose, get_string('strftimedatetimeshort', 'langconfig')));
    } else if ($quiz->timeclose && $timenow > $quiz->timeclose) {
        $currentstatus = get_string('quizisclosed', 'quiz');
    } else {
        $currentstatus = get_string('quizisopen', 'quiz');
    }

    $bits[] = html_writer::tag('span', $currentstatus,
            array('class' => 'quizopeningstatus', 'title' => implode(', ', $dates)));

    echo html_writer::tag('div', implode(' | ', $bits), array('class' => 'statusbar'));
}
