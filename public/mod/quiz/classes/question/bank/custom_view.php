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

use core\output\datafilter;
use core\output\html_writer;
use core_question\local\bank\column_base;
use core_question\local\bank\condition;
use core_question\local\bank\column_manager_base;
use core_question\local\bank\filter_condition_manager;
use core_question\local\bank\question_version_status;

require_once($CFG->dirroot . '/mod/quiz/locallib.php');
/**
 * Subclass to customise the view of the question bank for the quiz editing screen.
 *
 * @copyright  2009 Tim Hunt
 * @author     2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_view extends \core_question\local\bank\view {
    /** @var int number of questions per page to show in the add from question bank modal. */
    const DEFAULT_PAGE_SIZE = 20;

    /** @var bool $quizhasattempts whether the quiz this is used by has been attemptd. */
    protected $quizhasattempts = false;

    /** @var \stdClass $quiz the quiz settings. */
    protected $quiz = false;

    /**
     * @var string $component the component the api is used from.
     */
    public $component = 'mod_quiz';

    /**
     * Determine if the 'switch question bank' button must be displayed.
     *
     * @var bool
     */
    protected bool $requirebankswitch;

    /**
     * Constructor.
     * @param \core_question\local\bank\question_edit_contexts $contexts
     * @param \moodle_url $pageurl
     * @param \stdClass $course course settings
     * @param \stdClass $cm activity settings.
     * @param \stdClass $quiz quiz settings.
     */
    public function __construct($contexts, $pageurl, $course, $cm, $params, $extraparams) {
        // Default filter condition.
        if (!isset($params['filter'])) {
            $params['filter']  = filter_condition_manager::get_default_filter($params['cat']);
            // The quiz question bank modal doesn't include a hidden filter option.
            // Therefore, the default filter hidden condition is unnecessary.
            unset($params['filter']['hidden']);
        }

        $this->init_columns($this->wanted_columns(), $this->heading_column());
        $this->pagesize = self::DEFAULT_PAGE_SIZE;
        parent::__construct($contexts, $pageurl, $course, $cm, $params, $extraparams);
        [$this->quiz, ] = get_module_from_cmid($extraparams['quizcmid']);
        $this->set_quiz_has_attempts(quiz_has_attempts($this->quiz->id));
        $this->requirebankswitch = $extraparams['requirebankswitch'] ?? true;
    }

    /**
     * Just use the base column manager in this view.
     *
     * @return void
     */
    protected function init_column_manager(): void {
        $this->columnmanager = new column_manager_base();
    }

    /**
     * Don't display plugin controls.
     *
     * @param \core\context $context
     * @param int $categoryid
     * @return string
     */
    protected function get_plugin_controls(\core\context $context, int $categoryid): string {
        return '';
    }

    protected function get_question_bank_plugins(): array {
        $questionbankclasscolumns = [];
        $customviewcolumns = [
            'mod_quiz\question\bank\add_action_column' . column_base::ID_SEPARATOR  . 'add_action_column',
            'core_question\local\bank\checkbox_column' . column_base::ID_SEPARATOR . 'checkbox_column',
            'qbank_viewquestiontype\question_type_column' . column_base::ID_SEPARATOR . 'question_type_column',
            'mod_quiz\question\bank\question_name_text_column' . column_base::ID_SEPARATOR . 'question_name_text_column',
            'mod_quiz\question\bank\preview_action_column'  . column_base::ID_SEPARATOR  . 'preview_action_column',
        ];

        foreach ($customviewcolumns as $columnid) {
            [$columnclass, $columnname] = explode(column_base::ID_SEPARATOR, $columnid, 2);
            if (class_exists($columnclass)) {
                $questionbankclasscolumns[$columnid] = $columnclass::from_column_name($this, $columnname);
            }
        }

        return $questionbankclasscolumns;
    }

    protected function heading_column(): string {
        return 'mod_quiz\\question\\bank\\question_name_text_column';
    }

    protected function default_sort(): array {
        // Using the extended class for quiz specific sort.
        return [
            'qbank_viewquestiontype__question_type_column' => SORT_ASC,
            'mod_quiz__question__bank__question_name_text_column' => SORT_ASC,
        ];
    }

    /**
     * Let the question bank display know whether the quiz has been attempted,
     * hence whether some bits of UI, like the add this question to the quiz icon,
     * should be displayed.
     *
     * @param bool $quizhasattempts whether the quiz has attempts.
     */
    private function set_quiz_has_attempts($quizhasattempts): void {
        $this->quizhasattempts = $quizhasattempts;
        if ($quizhasattempts && isset($this->visiblecolumns['addtoquizaction'])) {
            unset($this->visiblecolumns['addtoquizaction']);
        }
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
        $params['cmid'] = $this->cm->id;
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
        $this->display();
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }

    protected function display_bottom_controls(\context $catcontext): void {
        $cmoptions = new \stdClass();
        $cmoptions->hasattempts = !empty($this->quizhasattempts);

        $canuseall = has_capability('moodle/question:useall', $catcontext);

        echo \html_writer::start_tag('div', ['class' => 'pt-2']);
        if ($canuseall) {
            // Add selected questions to the quiz.
            $params = [
                'type' => 'submit',
                'name' => 'add',
                'class' => 'btn btn-primary',
                'value' => get_string('addselectedquestionstoquiz', 'quiz'),
                'data-action' => 'toggle',
                'data-togglegroup' => 'qbank',
                'data-toggle' => 'action',
                'disabled' => true,
            ];
            echo \html_writer::empty_tag('input', $params);
        }
        echo \html_writer::end_tag('div');
    }

    /**
     * Override the base implementation in \core_question\local\bank\view
     * because we don't want to print new question form in the fragment
     * for the modal.
     *
     * @param false|mixed|\stdClass $category
     * @param bool $canadd
     */
    protected function create_new_question_form($category, $canadd): void {
    }

    /**
     * Override the base implementation in \core_question\local\bank\view
     * because we don't want to print the headers in the fragment
     * for the modal.
     */
    protected function display_question_bank_header(): void {
    }

    protected function build_query(): void {
        // Get the required tables and fields.
        [$fields, $joins] = $this->get_component_requirements(array_merge($this->requiredcolumns, $this->questionactions));

        // Build the order by clause.
        $sorts = [];
        foreach ($this->sort as $sortname => $sortorder) {
            [$colname, $subsort] = $this->parse_subsort($sortname);
            $sorts[] = $this->requiredcolumns[$colname]->sort_expression($sortorder == SORT_DESC, $subsort);
        }

        // Build the where clause.
        $latestversion = 'qv.version = (SELECT MAX(v.version)
                                          FROM {question_versions} v
                                          JOIN {question_bank_entries} be
                                            ON be.id = v.questionbankentryid
                                         WHERE be.id = qbe.id AND v.status <> :substatus)';

        // An additional condition is required in the subquery to account for scenarios
        // where the latest version is hidden. This ensures we retrieve the previous
        // "Ready" version instead of the hidden latest version.
        $onlyready = '((qv.status = :status))';
        $this->sqlparams = [
            'status' => question_version_status::QUESTION_STATUS_READY,
            'substatus' => question_version_status::QUESTION_STATUS_HIDDEN,
        ];
        $conditions = [];
        foreach ($this->searchconditions as $searchcondition) {
            if ($searchcondition->where()) {
                $conditions[] = '((' . $searchcondition->where() .'))';
            }
            if ($searchcondition->params()) {
                $this->sqlparams = array_merge($this->sqlparams, $searchcondition->params());
            }
        }
        $majorconditions = ['q.parent = 0', $latestversion, $onlyready];
        // Get higher level filter condition.
        $jointype = isset($this->pagevars['jointype']) ? (int)$this->pagevars['jointype'] : condition::JOINTYPE_DEFAULT;
        $nonecondition = ($jointype === datafilter::JOINTYPE_NONE) ? ' NOT ' : '';
        $separator = ($jointype === datafilter::JOINTYPE_ALL) ? ' AND ' : ' OR ';
        // Build the SQL.
        $sql = ' FROM {question} q ' . implode(' ', $joins);
        $sql .= ' WHERE ' . implode(' AND ', $majorconditions);
        if (!empty($conditions)) {
            $sql .= ' AND ' . $nonecondition . ' ( ';
            $sql .= implode($separator, $conditions);
            $sql .= ' ) ';
        }
        $this->countsql = 'SELECT count(1)' . $sql;
        $this->loadsql = 'SELECT ' . implode(', ', $fields) . $sql . ' ORDER BY ' . implode(', ', $sorts);
    }

    public function add_standard_search_conditions(): void {
        foreach ($this->plugins as $componentname => $plugin) {
            if (\core\plugininfo\qbank::is_plugin_enabled($componentname)) {
                $pluginentrypointobject = new $plugin();
                if ($componentname === 'qbank_managecategories') {
                    $pluginentrypointobject = new quiz_managecategories_feature();
                }
                if ($componentname === 'qbank_viewquestiontext' || $componentname === 'qbank_deletequestion') {
                    continue;
                }
                $pluginobjects = $pluginentrypointobject->get_question_filters($this);
                foreach ($pluginobjects as $pluginobject) {
                    $this->add_searchcondition($pluginobject, $pluginobject->get_condition_key());
                }
            }
        }
    }

    /**
     * Return the quiz settings for the quiz this question bank is displayed in.
     *
     * @return bool|\stdClass
     */
    public function get_quiz() {
        return $this->quiz;
    }

    /**
     * Shows the question bank interface.
     *
     * @return void
     */
    public function display(): void {

        echo \html_writer::start_div('questionbankwindow boxwidthwide boxaligncenter', [
            'data-component' => 'core_question',
            'data-callback' => 'display_question_bank',
            'data-contextid' => $this->contexts->lowest()->id,
        ]);

        // Show the 'switch question bank' button.
        echo $this->display_bank_switch();

        // Show the filters and search options.
        $this->wanted_filters();
        // Continues with list of questions.
        $this->display_question_list();
        echo \html_writer::end_div();
    }

    /**
     * Get the current bank header and bank switch button.
     *
     * @return string
     */
    protected function display_bank_switch(): string {
        global $OUTPUT;

        if (!$this->requirebankswitch) {
            return '';
        }

        $cminfo = \cm_info::create($this->cm);

        return $OUTPUT->render_from_template('mod_quiz/switch_bank_header', ['currentbank' => $cminfo->get_formatted_name()]);
    }
}
