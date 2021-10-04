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

use coding_exception;

/**
 * Subclass to customise the view of the question bank for the quiz editing screen.
 *
 * @copyright  2009 Tim Hunt
 * @author     2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_view extends \core_question\local\bank\view {

    /** @var bool $quizhasattempts whether the quiz this is used by has been attemptd. */
    protected $quizhasattempts = false;

    /** @var \stdClass $quiz the quiz settings. */
    protected $quiz = false;

    /** @var int The maximum displayed length of the category info. */
    const MAX_TEXT_LENGTH = 200;

    /**
     * Constructor for custom_view.
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

    protected function wanted_columns(): array {
        $quizquestionbankcolumns = [
            'mod_quiz\\question\\bank\\add_action_column',
            'core_question\\local\\bank\\checkbox_column',
            'qbank_viewquestiontype\\question_type_column',
            'mod_quiz\\question\\bank\\question_name_text_column',
            'qbank_previewquestion\\preview_action_column',
        ];

        foreach ($quizquestionbankcolumns as $fullname) {
            if (!class_exists($fullname)) {
                throw new coding_exception('Invalid quiz question bank column', $fullname);
            }
            $this->requiredcolumns[$fullname] = new $fullname($this);
        }
        return $this->requiredcolumns;
    }

    protected function heading_column(): string {
        return 'mod_quiz\\question\\bank\\question_name_text_column';
    }

    protected function default_sort(): array {
        return [
            'qbank_viewquestiontype\\question_type_column' => 1,
            'mod_quiz\\question\\bank\\question_name_text_column' => 1,
        ];
    }

    /**
     * Let the question bank display know whether the quiz has been attempted,
     * hence whether some bits of UI, like the add this question to the quiz icon,
     * should be displayed.
     *
     * @param bool $quizhasattempts whether the quiz has attempts.
     */
    public function set_quiz_has_attempts($quizhasattempts): void {
        $this->quizhasattempts = $quizhasattempts;
        if ($quizhasattempts && isset($this->visiblecolumns['addtoquizaction'])) {
            unset($this->visiblecolumns['addtoquizaction']);
        }
    }

    /**
     * Question preview url.
     *
     * @param \stdClass $question
     * @return \moodle_url
     */
    public function preview_question_url($question) {
        return quiz_question_preview_url($this->quiz, $question);
    }

    /**
     * URL of add to quiz.
     *
     * @param $questionid
     * @return \moodle_url
     */
    public function add_to_quiz_url($questionid) {
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
     * @param array $pagevars
     * @param string $tabname
     * @return string HTML code for the form
     */
    public function render($pagevars, $tabname): string {
        ob_start();
        $this->display($pagevars, $tabname);
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }

    protected function display_bottom_controls($totalnumber, $recurse, $category, \context $catcontext, array $addcontexts): void {
        $cmoptions = new \stdClass();
        $cmoptions->hasattempts = !empty($this->quizhasattempts);

        $canuseall = has_capability('moodle/question:useall', $catcontext);

        echo \html_writer::start_tag('div', ['class' => 'modulespecificbuttonscontainer']);
        if ($canuseall) {
            // Add selected questions to the quiz.
            $params = array(
                'type' => 'submit',
                'name' => 'add',
                'class' => 'btn btn-primary',
                'value' => get_string('addselectedquestionstoquiz', 'quiz'),
                'data-action' => 'toggle',
                'data-togglegroup' => 'qbank',
                'data-toggle' => 'action',
                'disabled' => true,
            );
            echo \html_writer::empty_tag('input', $params);
        }
        echo \html_writer::end_tag('div');
    }

    protected function create_new_question_form($category, $canadd): void {
        // Don't display this.
    }

    /**
     * Override the base implementation in \core_question\local\bank\view
     * because we don't want to print the headers in the fragment
     * for the modal.
     */
    protected function display_question_bank_header(): void {
    }

    /**
     * Override the base implementation in \core_question\bank\view
     * because we don't want it to read from the $_POST global variables
     * for the sort parameters since they are not present in a fragment.
     *
     * Unfortunately the best we can do is to look at the URL for
     * those parameters (only marginally better really).
     */
    protected function init_sort_from_params(): void {
        $this->sort = [];
        for ($i = 1; $i <= self::MAX_SORTS; $i++) {
            if (!$sort = $this->baseurl->param('qbs' . $i)) {
                break;
            }
            // Work out the appropriate order.
            $order = 1;
            if ($sort[0] == '-') {
                $order = -1;
                $sort = substr($sort, 1);
                if (!$sort) {
                    break;
                }
            }
            // Deal with subsorts.
            list($colname) = $this->parse_subsort($sort);
            $this->get_column_type($colname);
            $this->sort[$sort] = $order;
        }
    }
}
