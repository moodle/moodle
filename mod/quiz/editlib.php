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
            ORDER BY id", array($category->id, ($includesubcategories ? '1' : '0')))) {
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
        $form->questiontext = array('text' => ($includesubcategories ? '1' : '0'), 'format' => 0);
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
        $this->print_icon('t/add', $this->stradd, $this->qbank->add_to_quiz_url($question->id));
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
        echo quiz_question_tostring($question);
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
class quiz_question_bank_view extends core_question\bank\view {
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

    protected function wanted_columns() {
        global $CFG;

        if (empty($CFG->quizquestionbankcolumns)) {
            $quizquestionbankcolumns = array('add_to_quiz_action_column', 'checkbox_column', 'question_type_column',
                    'question_name_column', 'preview_action_column');
        } else {
             $quizquestionbankcolumns = explode(',', $CFG->quizquestionbankcolumns);
        }

        foreach ($quizquestionbankcolumns as $fullname) {
            if (! class_exists($fullname)) {
                if (class_exists('question_bank_' . $fullname)) {
                    $fullname = 'question_bank_' . $fullname;
                } else {
                    throw new coding_exception("No such class exists: $fullname");
                }
            }
            $this->requiredcolumns[$fullname] = new $fullname($this);
        }
        return $this->requiredcolumns;
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
        return array('question_bank_question_type_column' => 1, 'question_bank_question_name_column' => 1);
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

    /**
     * Renders the html question bank (same as display, but returns the result).
     *
     * Note that you can only output this rendered result once per page, as
     * it contains IDs which must be unique.
     *
     * @return string HTML code for the form
     */
    public function render($tabname, $page, $perpage, $cat, $recurse, $showhidden, $showquestiontext) {
        ob_start();
        $this->display($tabname, $page, $perpage, $cat, $recurse, $showhidden, $showquestiontext);
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }

    /**
     * Display the controls at the bottom of the list of questions.
     * @param int      $totalnumber Total number of questions that might be shown (if it was not for paging).
     * @param bool     $recurse     Whether to include subcategories.
     * @param stdClass $category    The question_category row from the database.
     * @param context  $catcontext  The context of the category being displayed.
     * @param array    $addcontexts contexts where the user is allowed to add new questions.
     */
    protected function display_bottom_controls($totalnumber, $recurse, $category, \context $catcontext, array $addcontexts) {
        $cmoptions = new \stdClass();
        $cmoptions->hasattempts = !empty($this->quizhasattempts);

        $canuseall = has_capability('moodle/question:useall', $catcontext);

        echo '<div class="modulespecificbuttonscontainer">';
        if ($canuseall) {

            // Add selected questions to the quiz.
            $params = array(
                    'type' => 'submit',
                    'name' => 'add',
                    'value' => get_string('addselectedquestionstoquiz', 'quiz'),
            );
            if ($cmoptions->hasattempts) {
                $params['disabled'] = 'disabled';
            }
            echo html_writer::empty_tag('input', $params);
        }
        echo "</div>\n";
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

    protected function display_options_form($showquestiontext, $scriptpath = '/mod/quiz/edit.php',
            $showtextoption = false) {
        // Overridden just to change the default values of the arguments.
        parent::display_options_form($showquestiontext, $scriptpath, $showtextoption);
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

    protected function create_new_question_form($category, $canadd) {
        // Don't display this.
    }
}
