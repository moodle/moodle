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
 * @package    mod
 * @subpackage quiz
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
 * @param int $questionid The id of the question.
 * @return bool whether the user can use this question.
 */
function quiz_has_question_use($questionid) {
    global $DB;
    $question = $DB->get_record('question', array('id' => $questionid), '*', MUST_EXIST);
    return question_has_capability_on($question, 'use');
}

/**
 * Remove a question from a quiz
 * @param object $quiz the quiz object.
 * @param int $questionid The id of the question to be deleted.
 */
function quiz_remove_question($quiz, $questionid) {
    global $DB;

    $questionids = explode(',', $quiz->questions);
    $key = array_search($questionid, $questionids);
    if ($key === false) {
        return;
    }

    unset($questionids[$key]);
    $quiz->questions = implode(',', $questionids);
    $DB->set_field('quiz', 'questions', $quiz->questions, array('id' => $quiz->id));
    $DB->delete_records('quiz_question_instances',
            array('quiz' => $quiz->instance, 'question' => $questionid));
}

/**
 * Remove an empty page from the quiz layout. If that is not possible, do nothing.
 * @param string $layout the existinng layout, $quiz->questions.
 * @param int $index the position into $layout where the empty page should be removed.
 * @return the updated layout
 */
function quiz_delete_empty_page($layout, $index) {
    $questionids = explode(',', $layout);

    if ($index < -1 || $index >= count($questionids) - 1) {
        return $layout;
    }

    if (($index >= 0 && $questionids[$index] != 0) || $questionids[$index + 1] != 0) {
        return $layout; // This was not an empty page.
    }

    unset($questionids[$index + 1]);

    return implode(',', $questionids);
}

/**
 * Add a question to a quiz
 *
 * Adds a question to a quiz by updating $quiz as well as the
 * quiz and quiz_question_instances tables. It also adds a page break
 * if required.
 * @param int $id The id of the question to be added
 * @param object $quiz The extended quiz object as used by edit.php
 *      This is updated by this function
 * @param int $page Which page in quiz to add the question on. If 0 (default),
 *      add at the end
 * @return bool false if the question was already in the quiz
 */
function quiz_add_quiz_question($id, $quiz, $page = 0) {
    global $DB;
    $questions = explode(',', quiz_clean_layout($quiz->questions));
    if (in_array($id, $questions)) {
        return false;
    }

    // Remove ending page break if it is not needed.
    if ($breaks = array_keys($questions, 0)) {
        // Determine location of the last two page breaks.
        $end = end($breaks);
        $last = prev($breaks);
        $last = $last ? $last : -1;
        if (!$quiz->questionsperpage || (($end - $last - 1) < $quiz->questionsperpage)) {
            array_pop($questions);
        }
    }
    if (is_int($page) && $page >= 1) {
        $numofpages = quiz_number_of_pages($quiz->questions);
        if ($numofpages<$page) {
            // The page specified does not exist in quiz.
            $page = 0;
        } else {
            // Add ending page break - the following logic requires doing
            // this at this point.
            $questions[] = 0;
            $currentpage = 1;
            $addnow = false;
            foreach ($questions as $question) {
                if ($question == 0) {
                    $currentpage++;
                    // The current page is the one after the one we want to add on,
                    // so we add the question before adding the current page.
                    if ($currentpage == $page + 1) {
                        $questions_new[] = $id;
                    }
                }
                $questions_new[] = $question;
            }
            $questions = $questions_new;
        }
    }
    if ($page == 0) {
        // Add question.
        $questions[] = $id;
        // Add ending page break.
        $questions[] = 0;
    }

    // Save new questionslist in database.
    $quiz->questions = implode(',', $questions);
    $DB->set_field('quiz', 'questions', $quiz->questions, array('id' => $quiz->id));

    // Add the new question instance.
    $instance = new stdClass();
    $instance->quiz = $quiz->id;
    $instance->question = $id;
    $instance->grade = $DB->get_field('question', 'defaultmark', array('id' => $id));
    $DB->insert_record('quiz_question_instances', $instance);
}

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
                          FROM {quiz_question_instances}
                         WHERE question = q.id)
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
 * Add a page break after at particular position$.
 * @param string $layout the existinng layout, $quiz->questions.
 * @param int $index the position into $layout where the empty page should be removed.
 * @return the updated layout
 */
function quiz_add_page_break_at($layout, $index) {
    $questionids = explode(',', $layout);
    if ($index < 0 || $index >= count($questionids)) {
        return $layout;
    }

    array_splice($questionids, $index, 0, '0');

    return implode(',', $questionids);
}

/**
 * Add a page break after a particular question.
 * @param string $layout the existinng layout, $quiz->questions.
 * @param int $qustionid the question to add the page break after.
 * @return the updated layout
 */
function quiz_add_page_break_after($layout, $questionid) {
    $questionids = explode(',', $layout);
    $key = array_search($questionid, $questionids);
    if ($key === false || !$questionid) {
        return $layout;
    }

    array_splice($questionids, $key + 1, 0, '0');

    return implode(',', $questionids);
}

/**
 * Update the database after $quiz->questions has been changed. For example,
 * this deletes preview attempts and updates $quiz->sumgrades.
 * @param $quiz the quiz object.
 */
function quiz_save_new_layout($quiz) {
    global $DB;
    $DB->set_field('quiz', 'questions', $quiz->questions, array('id' => $quiz->id));
    quiz_delete_previews($quiz);
    quiz_update_sumgrades($quiz);
}

/**
 * Save changes to question instance
 *
 * Saves changes to the question grades in the quiz_question_instances table.
 * It does not update 'sumgrades' in the quiz table.
 *
 * @param int grade    The maximal grade for the question
 * @param int $questionid  The id of the question
 * @param int $quizid  The id of the quiz to update / add the instances for.
 */
function quiz_update_question_instance($grade, $questionid, $quiz) {
    global $DB;
    $instance = $DB->get_record('quiz_question_instances', array('quiz' => $quiz->id,
            'question' => $questionid));
    $slot = quiz_get_slot_for_question($quiz, $questionid);

    if (!$instance || !$slot) {
        throw new coding_exception('Attempt to change the grade of a quesion not in the quiz.');
    }

    if (abs($grade - $instance->grade) < 1e-7) {
        // Grade has not changed. Nothing to do.
        return;
    }

    $instance->grade = $grade;
    $DB->update_record('quiz_question_instances', $instance);
    question_engine::set_max_mark_in_attempts(new qubaids_for_quiz($quiz->id),
            $slot, $grade);
}

// Private function used by the following two.
function _quiz_move_question($layout, $questionid, $shift) {
    if (!$questionid || !($shift == 1 || $shift == -1)) {
        return $layout;
    }

    $questionids = explode(',', $layout);
    $key = array_search($questionid, $questionids);
    if ($key === false) {
        return $layout;
    }

    $otherkey = $key + $shift;
    if ($otherkey < 0 || $otherkey >= count($questionids) - 1) {
        return $layout;
    }

    $temp = $questionids[$otherkey];
    $questionids[$otherkey] = $questionids[$key];
    $questionids[$key] = $temp;

    return implode(',', $questionids);
}

/**
 * Move a particular question one space earlier in the $quiz->questions list.
 * If that is not possible, do nothing.
 * @param string $layout the existinng layout, $quiz->questions.
 * @param int $questionid the id of a question.
 * @return the updated layout
 */
function quiz_move_question_up($layout, $questionid) {
    return _quiz_move_question($layout, $questionid, -1);
}

/**
 * Move a particular question one space later in the $quiz->questions list.
 * If that is not possible, do nothing.
 * @param string $layout the existinng layout, $quiz->questions.
 * @param int $questionid the id of a question.
 * @return the updated layout
 */
function quiz_move_question_down($layout, $questionid) {
    return _quiz_move_question($layout, $questionid, +1);
}

/**
 * Prints a list of quiz questions for the edit.php main view for edit
 * ($reordertool = false) and order and paging ($reordertool = true) tabs
 *
 * @param object $quiz This is not the standard quiz object used elsewhere but
 *     it contains the quiz layout in $quiz->questions and the grades in
 *     $quiz->grades
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

    if ($quiz->questions) {
        list($usql, $params) = $DB->get_in_or_equal(explode(',', $quiz->questions));
        $params[] = $quiz->id;
        $questions = $DB->get_records_sql("SELECT q.*, qc.contextid, qqi.grade as maxmark
                              FROM {question} q
                              JOIN {question_categories} qc ON qc.id = q.category
                              JOIN {quiz_question_instances} qqi ON qqi.question = q.id
                             WHERE q.id $usql AND qqi.quiz = ?", $params);
    } else {
        $questions = array();
    }

    $layout = quiz_clean_layout($quiz->questions);
    $order = explode(',', $layout);
    $lastindex = count($order) - 1;

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

    // The current question ordinal (no descriptions).
    $qno = 1;
    // The current question (includes questions and descriptions).
    $questioncount = 0;
    // The current page number in iteration.
    $pagecount = 0;

    $pageopen = false;

    $returnurl = $pageurl->out_as_local_url(false);
    $questiontotalcount = count($order);

    foreach ($order as $count => $qnum) {

        $reordercheckbox = '';
        $reordercheckboxlabel = '';
        $reordercheckboxlabelclose = '';

        // If the questiontype is missing change the question type.
        if ($qnum && !array_key_exists($qnum, $questions)) {
            $fakequestion = new stdClass();
            $fakequestion->id = $qnum;
            $fakequestion->category = 0;
            $fakequestion->qtype = 'missingtype';
            $fakequestion->name = get_string('missingquestion', 'quiz');
            $fakequestion->questiontext = ' ';
            $fakequestion->questiontextformat = FORMAT_HTML;
            $fakequestion->length = 1;
            $questions[$qnum] = $fakequestion;
            $quiz->grades[$qnum] = 0;

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
            if ($qnum == 0  && $count < $questiontotalcount) {
                // This is the second successive page break. Tell the user the page is empty.
                echo '<div class="pagestatus">';
                print_string('noquestionsonpage', 'quiz');
                echo '</div>';
                if ($allowdelete) {
                    echo '<div class="quizpagedelete">';
                    echo $OUTPUT->action_icon($pageurl->out(true,
                            array('deleteemptypage' => $count - 1, 'sesskey'=>sesskey())),
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
                    $reordercheckbox = '<input type="checkbox" name="s' . $question->id .
                        '" id="s' . $question->id . '" />';
                    $reordercheckboxlabel = '<label for="s' . $question->id . '">';
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
                        if ($count >= $lastindex - 1) {
                            $upbuttonclass = 'upwithoutdown';
                        }
                        echo $OUTPUT->action_icon($pageurl->out(true,
                                array('up' => $question->id, 'sesskey'=>sesskey())),
                                new pix_icon('t/up', $strmoveup),
                                new component_action('click',
                                        'M.core_scroll_manager.save_scroll_action'),
                                array('title' => $strmoveup));
                    }

                }
                if ($count < $lastindex - 1) {
                    if (!$hasattempts) {
                        echo $OUTPUT->action_icon($pageurl->out(true,
                                array('down' => $question->id, 'sesskey'=>sesskey())),
                                new pix_icon('t/down', $strmovedown),
                                new component_action('click',
                                        'M.core_scroll_manager.save_scroll_action'),
                                array('title' => $strmovedown));
                    }
                }
                if ($allowdelete && ($question->qtype == 'missingtype' ||
                        question_has_capability_on($question, 'use', $question->category))) {
                    // Remove from quiz, not question delete.
                    if (!$hasattempts) {
                        echo $OUTPUT->action_icon($pageurl->out(true,
                                array('remove' => $question->id, 'sesskey'=>sesskey())),
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
    <label for="<?php echo "inputq$question->id" ?>"><?php echo $strmaxmark; ?></label>:<br />
    <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />
    <?php echo html_writer::input_hidden_params($pageurl); ?>
    <input type="hidden" name="savechanges" value="save" />
                    <?php
                    echo '<input type="text" name="g' . $question->id .
                            '" id="inputq' . $question->id .
                            '" size="' . ($quiz->decimalpoints + 2) .
                            '" value="' . (0 + $quiz->grades[$qnum]) .
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
                        echo '<label class="accesshide" for="o' . $question->id . '">' .
                                get_string('questionposition', 'quiz', $qnodisplay) . '</label>';
                        echo '<input type="text" name="o' . $question->id .
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

                if (!$reordertool && !$quiz->shufflequestions) {
                    echo $OUTPUT->container_start('addpage');
                    $url = new moodle_url($pageurl->out_omit_querystring(),
                            array('cmid' => $quiz->cmid, 'courseid' => $quiz->course,
                                    'addpage' => $count, 'sesskey' => sesskey()));
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
 * @param object $quiz This is not the standard quiz object used elsewhere but
 *     it contains the quiz layout in $quiz->questions and the grades in
 *     $quiz->grades
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
function quiz_print_randomquestion_reordertool(&$question, &$pageurl, &$quiz) {
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
        $formatoptions = new stdClass();
        $formatoptions->noclean = true;
        $formatoptions->para = false;
        $questiontext = strip_tags(format_text($question->questiontext,
                $question->questiontextformat,
                $formatoptions, $COURSE->id));
        $questiontext = shorten_text($questiontext, 200);
        $result .= '<span class="questiontext">';
        if (!empty($questiontext)) {
            $result .= $questiontext;
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
                'editaction', 'previewaction');
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

        // Display the current category.
        if (!$category = $this->get_current_category($cat)) {
            return;
        }
        $this->print_category_info($category);

        echo $OUTPUT->box_start('generalbox questionbank');

        $this->display_category_form($this->contexts->having_one_edit_tab_cap($tabname),
                $this->baseurl, $cat);

        // Continues with list of questions.
        $this->display_question_list($this->contexts->having_one_edit_tab_cap($tabname),
                $this->baseurl, $cat, $this->cm, $recurse, $page,
                $perpage, $showhidden, $showquestiontext,
                $this->contexts->having_cap('moodle/question:add'));

        $this->display_options($recurse, $showhidden, $showquestiontext);
        echo $OUTPUT->box_end();
    }

    protected function print_choose_category_message($categoryandcontext) {
        global $OUTPUT;
        echo $OUTPUT->box_start('generalbox questionbank');
        $this->display_category_form($this->contexts->having_one_edit_tab_cap('edit'),
                $this->baseurl, $categoryandcontext);
        echo "<p style=\"text-align:center;\"><b>";
        print_string('selectcategoryabove', 'question');
        echo "</b></p>";
        echo $OUTPUT->box_end();
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
    global $CFG;

    $bits = array();

    $bits[] = html_writer::tag('span',
            get_string('totalmarksx', 'quiz', quiz_format_grade($quiz, $quiz->sumgrades)),
            array('class' => 'totalpoints'));

    $bits[] = html_writer::tag('span',
            get_string('numquestionsx', 'quiz', quiz_number_of_questions_in_quiz($quiz->questions)),
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
    $tooltip = implode(', ', $dates);;

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
