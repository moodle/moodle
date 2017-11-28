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
 * Defines the custom question bank view used on the Edit quiz page.
 *
 * @package   mod_quiz
 * @category  question
 * @copyright 1999 onwards Martin Dougiamas and others {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_quiz\question\bank;
defined('MOODLE_INTERNAL') || die();


/**
 * Subclass to customise the view of the question bank for the quiz editing screen.
 *
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_view extends \core_question\bank\view {
    /** @var bool whether the quiz this is used by has been attemptd. */
    protected $quizhasattempts = false;
    /** @var \stdClass the quiz settings. */
    protected $quiz = false;
    /** @var int The maximum displayed length of the category info. */
    const MAX_TEXT_LENGTH = 200;

    /**
     * Constructor
     * @param \question_edit_contexts $contexts
     * @param \moodle_url $pageurl
     * @param \stdClass $course course settings
     * @param \stdClass $cm activity settings.
     * @param \stdClass $quiz quiz settings.
     */
    public function __construct($contexts, $pageurl, $course, $cm, $quiz) {
        parent::__construct($contexts, $pageurl, $course, $cm);
        $this->quiz = $quiz;
    }

    protected function wanted_columns() {
        global $CFG;

        if (empty($CFG->quizquestionbankcolumns)) {
            $quizquestionbankcolumns = array(
                'add_action_column',
                'checkbox_column',
                'question_type_column',
                'question_name_text_column',
                'preview_action_column',
            );
        } else {
            $quizquestionbankcolumns = explode(',', $CFG->quizquestionbankcolumns);
        }

        foreach ($quizquestionbankcolumns as $fullname) {
            if (!class_exists($fullname)) {
                if (class_exists('mod_quiz\\question\\bank\\' . $fullname)) {
                    $fullname = 'mod_quiz\\question\\bank\\' . $fullname;
                } else if (class_exists('core_question\\bank\\' . $fullname)) {
                    $fullname = 'core_question\\bank\\' . $fullname;
                } else if (class_exists('question_bank_' . $fullname)) {
                    debugging('Legacy question bank column class question_bank_' .
                            $fullname . ' should be renamed to mod_quiz\\question\\bank\\' .
                            $fullname, DEBUG_DEVELOPER);
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
        return 'mod_quiz\\question\\bank\\question_name_text_column';
    }

    protected function default_sort() {
        return array(
            'core_question\\bank\\question_type_column' => 1,
            'mod_quiz\\question\\bank\\question_name_text_column' => 1,
        );
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
        return new \moodle_url('/mod/quiz/edit.php', $params);
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
     * @param int       $totalnumber Total number of questions that might be shown (if it was not for paging).
     * @param bool      $recurse     Whether to include subcategories.
     * @param \stdClass $category    The question_category row from the database.
     * @param \context  $catcontext  The context of the category being displayed.
     * @param array     $addcontexts contexts where the user is allowed to add new questions.
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
                    'class' => 'btn btn-primary',
                    'value' => get_string('addselectedquestionstoquiz', 'quiz'),
            );
            if ($cmoptions->hasattempts) {
                $params['disabled'] = 'disabled';
            }
            echo \html_writer::empty_tag('input', $params);
        }
        echo "</div>\n";
    }

    /**
     * Prints a form to choose categories.
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
        echo \html_writer::input_hidden_params($this->baseurl,
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
