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

namespace mod_quiz\local\reports;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

use coding_exception;
use context_module;
use html_writer;
use mod_quiz\quiz_attempt;
use mod_quiz\quiz_settings;
use moodle_url;
use popup_action;
use question_state;
use qubaid_condition;
use qubaid_join;
use qubaid_list;
use question_engine_data_mapper;
use stdClass;

/**
 * Base class for the table used by a {@see attempts_report}.
 *
 * @package   mod_quiz
 * @copyright 2010 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class attempts_report_table extends \table_sql {
    public $useridfield = 'userid';

    /** @var moodle_url the URL of this report. */
    protected $reporturl;

    /** @var array the display options. */
    protected $displayoptions;

    /**
     * @var array information about the latest step of each question.
     * Loaded by {@see load_question_latest_steps()}, if applicable.
     */
    protected $lateststeps = null;

    /**
     * @var float[][]|null total mark for each grade item. Array question_usage.id => quiz_grade_item.id => mark.
     * Loaded by {@see load_grade_item_marks()}, if applicable.
     */
    protected ?array $gradeitemtotals = null;

    /** @var stdClass the quiz settings for the quiz we are reporting on. */
    protected $quiz;

    /** @var quiz_settings quiz settings object for this quiz. Gets set in {@see attempts_report::et_up_table_columns()}. */
    protected quiz_settings $quizobj;

    /** @var context_module the quiz context. */
    protected $context;

    /** @var string HTML fragment to select the first/best/last attempt, if appropriate. */
    protected $qmsubselect;

    /** @var stdClass attempts_report_options the options affecting this report. */
    protected $options;

    /** @var \core\dml\sql_join Contains joins, wheres, params to find students
     * in the currently selected group, if applicable.
     */
    protected $groupstudentsjoins;

    /** @var \core\dml\sql_join Contains joins, wheres, params to find the students in the course. */
    protected $studentsjoins;

    /** @var array the questions that comprise this quiz. */
    protected $questions;

    /** @var bool whether to include the column with checkboxes to select each attempt. */
    protected $includecheckboxes;

    /** @var string The toggle group name for the checkboxes in the checkbox column. */
    protected $togglegroup = 'quiz-attempts';

    /** @var string strftime format. */
    protected $strtimeformat;

    /** @var bool|null used by {@see col_state()} to cache the has_capability result. */
    protected $canreopen = null;

    /**
     * Constructor.
     *
     * @param string $uniqueid
     * @param stdClass $quiz
     * @param context_module $context
     * @param string $qmsubselect
     * @param attempts_report_options $options
     * @param \core\dml\sql_join $groupstudentsjoins Contains joins, wheres, params
     * @param \core\dml\sql_join $studentsjoins Contains joins, wheres, params
     * @param array $questions
     * @param moodle_url $reporturl
     */
    public function __construct($uniqueid, $quiz, $context, $qmsubselect,
            attempts_report_options $options, \core\dml\sql_join $groupstudentsjoins, \core\dml\sql_join $studentsjoins,
            $questions, $reporturl) {
        parent::__construct($uniqueid);
        $this->quiz = $quiz;
        $this->context = $context;
        $this->qmsubselect = $qmsubselect;
        $this->groupstudentsjoins = $groupstudentsjoins;
        $this->studentsjoins = $studentsjoins;
        $this->questions = $questions;
        $this->includecheckboxes = $options->checkboxcolumn;
        $this->reporturl = $reporturl;
        $this->options = $options;
    }

    /**
     * A way for the report to pass in the quiz settings object. Currently done in {@see attempts_report::set_up_table_columns()}.
     *
     * @param quiz_settings $quizobj
     */
    public function set_quiz_setting(quiz_settings $quizobj): void {
        $this->quizobj = $quizobj;
    }

    /**
     * Generate the display of the checkbox column.
     *
     * @param stdClass $attempt the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_checkbox($attempt) {
        global $OUTPUT;

        if ($attempt->attempt) {
            $checkbox = new \core\output\checkbox_toggleall($this->togglegroup, false, [
                'id' => "attemptid_{$attempt->attempt}",
                'name' => 'attemptid[]',
                'value' => $attempt->attempt,
                'label' => get_string('selectattempt', 'quiz'),
                'labelclasses' => 'accesshide',
            ]);
            return $OUTPUT->render($checkbox);
        } else {
            return '';
        }
    }

    /**
     * Generate the display of the user's picture column.
     *
     * @param stdClass $attempt the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_picture($attempt) {
        global $OUTPUT;
        $user = new stdClass();
        $additionalfields = explode(',', implode(',', \core_user\fields::get_picture_fields()));
        $user = username_load_fields_from_object($user, $attempt, null, $additionalfields);
        $user->id = $attempt->userid;
        return $OUTPUT->user_picture($user);
    }

    /**
     * Generate the display of the user's full name column.
     *
     * @param stdClass $attempt the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_fullname($attempt) {
        $html = parent::col_fullname($attempt);
        if ($this->is_downloading() || empty($attempt->attempt)) {
            return $html;
        }

        return $html . html_writer::empty_tag('br') . html_writer::link(
                new moodle_url('/mod/quiz/review.php', ['attempt' => $attempt->attempt]),
                get_string('reviewattempt', 'quiz'), ['class' => 'reviewlink']);
    }

    /**
     * Generate the display of the attempt state column.
     *
     * @param stdClass $attempt the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_state($attempt) {
        if (is_null($attempt->attempt)) {
            return '-';
        }

        $display = quiz_attempt::state_name($attempt->state);
        if ($this->is_downloading()) {
            return $display;
        }

        $this->canreopen ??= has_capability('mod/quiz:reopenattempts', $this->context);
        if ($attempt->state == quiz_attempt::ABANDONED && $this->canreopen) {
            $display .= ' ' . html_writer::tag('button', get_string('reopenattempt', 'quiz'), [
                'type' => 'button',
                'class' => 'btn btn-secondary',
                'data-action' => 'reopen-attempt',
                'data-attempt-id' => $attempt->attempt,
                'data-after-action-url' => $this->reporturl->out_as_local_url(false),
            ]);
        }

        return $display;
    }

    /**
     * Generate the display of the start time column.
     *
     * @param stdClass $attempt the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_timestart($attempt) {
        if ($attempt->attempt) {
            return userdate($attempt->timestart, $this->strtimeformat);
        } else {
            return  '-';
        }
    }

    /**
     * Generate the display of the finish time column.
     *
     * @param stdClass $attempt the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_timefinish($attempt) {
        if ($attempt->attempt && $attempt->timefinish) {
            return userdate($attempt->timefinish, $this->strtimeformat);
        } else {
            return  '-';
        }
    }

    /**
     * Generate the display of the duration column.
     *
     * @param stdClass $attempt the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_duration($attempt) {
        if ($attempt->timefinish) {
            return format_time($attempt->timefinish - $attempt->timestart);
        } else {
            return '-';
        }
    }

    /**
     * Generate the display of the feedback column.
     *
     * @param stdClass $attempt the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_feedbacktext($attempt) {
        if ($attempt->state != quiz_attempt::FINISHED) {
            return '-';
        }

        $feedback = quiz_report_feedback_for_grade(
                quiz_rescale_grade($attempt->sumgrades, $this->quiz, false),
                $this->quiz->id, $this->context);

        if ($this->is_downloading()) {
            $feedback = strip_tags($feedback);
        }

        return $feedback;
    }

    public function other_cols($colname, $attempt) {
        $gradeitemid = $this->is_grade_item_column($colname);

        if (!$gradeitemid) {
            return parent::other_cols($colname, $attempt);
        }
        if (isset($this->gradeitemtotals[$attempt->usageid][$gradeitemid])) {
            $grade = quiz_format_grade($this->quiz, $this->gradeitemtotals[$attempt->usageid][$gradeitemid]);
            return $grade;
        } else {
            return '-';
        }
    }

    /**
     * Is this the column key for an extra grade item column?
     *
     * @param string $columnname e.g. 'marks123' or 'duration'.
     * @return int grade item id if this is a column for showing that grade item grade, else, 0.
     */
    protected function is_grade_item_column(string $columnname): int {
        if (preg_match('/^marks(\d+)$/', $columnname, $matches)) {
            return $matches[1];
        }
        return 0;
    }

    public function get_row_class($attempt) {
        if ($this->qmsubselect && $attempt->gradedattempt) {
            return 'gradedattempt';
        } else {
            return '';
        }
    }

    /**
     * Make a link to review an individual question in a popup window.
     *
     * @param string $data HTML fragment. The text to make into the link.
     * @param stdClass $attempt data for the row of the table being output.
     * @param int $slot the number used to identify this question within this usage.
     */
    public function make_review_link($data, $attempt, $slot) {
        global $OUTPUT, $CFG;

        $flag = '';
        if ($this->is_flagged($attempt->usageid, $slot)) {
            $flag = $OUTPUT->pix_icon('i/flagged', get_string('flagged', 'question'),
                    'moodle', ['class' => 'questionflag']);
        }

        $feedbackimg = '';
        $state = $this->slot_state($attempt, $slot);
        if ($state && $state->is_finished() && $state != question_state::$needsgrading) {
            $feedbackimg = $this->icon_for_fraction($this->slot_fraction($attempt, $slot));
        }

        $output = html_writer::tag('span', $feedbackimg . html_writer::tag('span',
                $data, ['class' => $state->get_state_class(true)]) . $flag, ['class' => 'que']);

        $reviewparams = ['attempt' => $attempt->attempt, 'slot' => $slot];
        if (isset($attempt->try)) {
            $reviewparams['step'] = $this->step_no_for_try($attempt->usageid, $slot, $attempt->try);
        }
        $url = new moodle_url('/mod/quiz/reviewquestion.php', $reviewparams);
        $output = $OUTPUT->action_link($url, $output,
                new popup_action('click', $url, 'reviewquestion',
                        ['height' => 450, 'width' => 650]),
                ['title' => get_string('reviewresponse', 'quiz')]);

        if (!empty($CFG->enableplagiarism)) {
            require_once($CFG->libdir . '/plagiarismlib.php');
            $output .= plagiarism_get_links([
                'context' => $this->context->id,
                'component' => 'qtype_'.$this->questions[$slot]->qtype,
                'cmid' => $this->context->instanceid,
                'area' => $attempt->usageid,
                'itemid' => $slot,
                'userid' => $attempt->userid]);
        }
        return $output;
    }

    /**
     * Get the question attempt state for a particular question in a particular quiz attempt.
     *
     * @param stdClass $attempt the row data.
     * @param int $slot indicates which question.
     * @return question_state the state of that question.
     */
    protected function slot_state($attempt, $slot) {
        $stepdata = $this->lateststeps[$attempt->usageid][$slot];
        return question_state::get($stepdata->state);
    }

    /**
     * Work out if a particular question in a particular attempt has been flagged.
     *
     * @param int $questionusageid used to identify the attempt of interest.
     * @param int $slot identifies which question in the attempt to check.
     * @return bool true if the question is flagged in the attempt.
     */
    protected function is_flagged($questionusageid, $slot) {
        $stepdata = $this->lateststeps[$questionusageid][$slot];
        return $stepdata->flagged;
    }

    /**
     * Get the mark (out of 1) for the question in a particular slot.
     *
     * @param stdClass $attempt the row data
     * @param int $slot which slot to check.
     * @return float the score for this question on a scale of 0 - 1.
     */
    protected function slot_fraction($attempt, $slot) {
        $stepdata = $this->lateststeps[$attempt->usageid][$slot];
        return $stepdata->fraction;
    }

    /**
     * Return an appropriate icon (green tick, red cross, etc.) for a grade.
     *
     * @param float $fraction grade on a scale 0..1.
     * @return string html fragment.
     */
    protected function icon_for_fraction($fraction) {
        global $OUTPUT;

        $feedbackclass = question_state::graded_state_for_fraction($fraction)->get_feedback_class();
        return $OUTPUT->pix_icon('i/grade_' . $feedbackclass, get_string($feedbackclass, 'question'),
                'moodle', ['class' => 'icon']);
    }

    /**
     * Load any extra data after main query.
     *
     * At this point you can call {@see get_qubaids_condition} to get the condition
     * that limits the query to just the question usages shown in this report page or
     * alternatively for all attempts if downloading a full report.
     */
    protected function load_extra_data() {
        $this->lateststeps = $this->load_question_latest_steps();
    }

    /**
     * Load the total mark for each grade item for each attempt.
     */
    protected function load_grade_item_marks(): void {
        if (count($this->rawdata) === 0) {
            $this->gradeitemtotals = [];
            return;
        }

        $this->gradeitemtotals = $this->quizobj->get_grade_calculator()->load_grade_item_totals(
                $this->get_qubaids_condition());
    }

    /**
     * Load information about the latest state of selected questions in selected attempts.
     *
     * The results are returned as a two-dimensional array $qubaid => $slot => $dataobject.
     *
     * @param qubaid_condition|null $qubaids used to restrict which usages are included
     *      in the query. See {@see qubaid_condition}.
     * @return array of records. See the SQL in this function to see the fields available.
     */
    protected function load_question_latest_steps(?qubaid_condition $qubaids = null) {
        if ($qubaids === null) {
            $qubaids = $this->get_qubaids_condition();
        }
        $dm = new question_engine_data_mapper();
        $latesstepdata = $dm->load_questions_usages_latest_steps(
                $qubaids, array_keys($this->questions));

        $lateststeps = [];
        foreach ($latesstepdata as $step) {
            $lateststeps[$step->questionusageid][$step->slot] = $step;
        }

        return $lateststeps;
    }

    /**
     * Does this report require loading any more data after the main query.
     *
     * @return bool should {@see query_db()} call {@see load_extra_data}?
     */
    protected function requires_extra_data() {
        return $this->requires_latest_steps_loaded();
    }

    /**
     * Does this report require the detailed information for each question from the question_attempts_steps table?
     *
     * @return bool should {@see load_extra_data} call {@see load_question_latest_steps}?
     */
    protected function requires_latest_steps_loaded() {
        return false;
    }

    /**
     * Is this a column that depends on joining to the latest state information?
     *
     * If so, return the corresponding slot. If not, return false.
     *
     * @param string $column a column name
     * @return int|false false if no, else a slot.
     */
    protected function is_latest_step_column($column) {
        return false;
    }

    /**
     * Get any fields that might be needed when sorting on date for a particular slot.
     *
     * Note: these values are only used for sorting. The values displayed are taken
     * from $this->lateststeps loaded in load_extra_data().
     *
     * @param int $slot the slot for the column we want.
     * @param string $alias the table alias for latest state information relating to that slot.
     * @return string definitions of extra fields to add to the SELECT list of the query.
     */
    protected function get_required_latest_state_fields($slot, $alias) {
        return '';
    }

    /**
     * Contruct all the parts of the main database query.
     *
     * @param \core\dml\sql_join $allowedstudentsjoins (joins, wheres, params) defines allowed users for the report.
     * @return array with 4 elements [$fields, $from, $where, $params] that can be used to
     *     build the actual database query.
     */
    public function base_sql(\core\dml\sql_join $allowedstudentsjoins) {
        global $DB;

        // Please note this uniqueid column is not the same as quiza.uniqueid.
        $fields = 'DISTINCT ' . $DB->sql_concat('u.id', "'#'", 'COALESCE(quiza.attempt, 0)') . ' AS uniqueid,';

        if ($this->qmsubselect) {
            $fields .= "\n(CASE WHEN $this->qmsubselect THEN 1 ELSE 0 END) AS gradedattempt,";
        }

        $userfieldsapi = \core_user\fields::for_identity($this->context)->with_name()
                ->excluding('id', 'idnumber', 'picture', 'imagealt', 'institution', 'department', 'email');
        $userfields = $userfieldsapi->get_sql('u', true, '', '', false);

        $fields .= '
                quiza.uniqueid AS usageid,
                quiza.id AS attempt,
                u.id AS userid,
                u.idnumber,
                u.picture,
                u.imagealt,
                u.institution,
                u.department,
                u.email,' . $userfields->selects . ',
                quiza.state,
                quiza.sumgrades,
                quiza.timefinish,
                quiza.timestart,
                CASE WHEN quiza.timefinish = 0 THEN null
                     WHEN quiza.timefinish > quiza.timestart THEN quiza.timefinish - quiza.timestart
                     ELSE 0 END AS duration';
            // To explain that last bit, timefinish can be non-zero and less
            // than timestart when you have two load-balanced servers with very
            // badly synchronised clocks, and a student does a really quick attempt.

        // This part is the same for all cases. Join the users and quiz_attempts tables.
        $from = " {user} u";
        $from .= "\n{$userfields->joins}";
        $from .= "\nLEFT JOIN {quiz_attempts} quiza ON
                                    quiza.userid = u.id AND quiza.quiz = :quizid";
        $params = array_merge($userfields->params, ['quizid' => $this->quiz->id]);

        if ($this->qmsubselect && $this->options->onlygraded) {
            $from .= " AND (quiza.state <> :finishedstate OR $this->qmsubselect)";
            $params['finishedstate'] = quiz_attempt::FINISHED;
        }

        switch ($this->options->attempts) {
            case attempts_report::ALL_WITH:
                // Show all attempts, including students who are no longer in the course.
                $where = 'quiza.id IS NOT NULL AND quiza.preview = 0';
                break;
            case attempts_report::ENROLLED_WITH:
                // Show only students with attempts.
                $from .= "\n" . $allowedstudentsjoins->joins;
                $where = "quiza.preview = 0 AND quiza.id IS NOT NULL AND " . $allowedstudentsjoins->wheres;
                $params = array_merge($params, $allowedstudentsjoins->params);
                break;
            case attempts_report::ENROLLED_WITHOUT:
                // Show only students without attempts.
                $from .= "\n" . $allowedstudentsjoins->joins;
                $where = "quiza.id IS NULL AND " . $allowedstudentsjoins->wheres;
                $params = array_merge($params, $allowedstudentsjoins->params);
                break;
            case attempts_report::ENROLLED_ALL:
                // Show all students with or without attempts.
                $from .= "\n" . $allowedstudentsjoins->joins;
                $where = "(quiza.preview = 0 OR quiza.preview IS NULL) AND " . $allowedstudentsjoins->wheres;
                $params = array_merge($params, $allowedstudentsjoins->params);
                break;
        }

        if ($this->options->states) {
            [$statesql, $stateparams] = $DB->get_in_or_equal($this->options->states,
                    SQL_PARAMS_NAMED, 'state');
            $params += $stateparams;
            $where .= " AND (quiza.state $statesql OR quiza.state IS NULL)";
        }

        return [$fields, $from, $where, $params];
    }

    /**
     * Lets subclasses modify the SQL after the count query has been created and before the full query is.
     *
     * @param string $fields SELECT list.
     * @param string $from JOINs part of the SQL.
     * @param string $where WHERE clauses.
     * @param array $params Query params.
     * @return array with 4 elements ($fields, $from, $where, $params) as from base_sql.
     */
    protected function update_sql_after_count($fields, $from, $where, $params) {
        return [$fields, $from, $where, $params];
    }

    /**
     * Set up the SQL queries (count rows, and get data).
     *
     * @param \core\dml\sql_join $allowedjoins (joins, wheres, params) defines allowed users for the report.
     */
    public function setup_sql_queries($allowedjoins) {
        [$fields, $from, $where, $params] = $this->base_sql($allowedjoins);

        // The WHERE clause is vital here, because some parts of tablelib.php will expect to
        // add bits like ' AND x = 1' on the end, and that needs to leave to valid SQL.
        $this->set_count_sql("SELECT COUNT(1) FROM (SELECT $fields FROM $from WHERE $where) temp WHERE 1 = 1", $params);

        [$fields, $from, $where, $params] = $this->update_sql_after_count($fields, $from, $where, $params);
        $this->set_sql($fields, $from, $where, $params);
    }

    /**
     * Add the information about the latest state of the question with slot
     * $slot to the query.
     *
     * The extra information is added as a join to a
     * 'table' with alias qa$slot, with columns that are a union of
     * the columns of the question_attempts and question_attempts_states tables.
     *
     * @param int $slot the question to add information for.
     */
    protected function add_latest_state_join($slot) {
        $alias = 'qa' . $slot;

        $fields = $this->get_required_latest_state_fields($slot, $alias);
        if (!$fields) {
            return;
        }

        // This condition roughly filters the list of attempts to be considered.
        // It is only used in a sub-select to help database query optimisers (see MDL-30122).
        // Therefore, it is better to use a very simple  which may include
        // too many records, than to do a super-accurate join.
        $qubaids = new qubaid_join("{quiz_attempts} {$alias}quiza", "{$alias}quiza.uniqueid",
                "{$alias}quiza.quiz = :{$alias}quizid", ["{$alias}quizid" => $this->sql->params['quizid']]);

        $dm = new question_engine_data_mapper();
        [$inlineview, $viewparams] = $dm->question_attempt_latest_state_view($alias, $qubaids);

        $this->sql->fields .= ",\n$fields";
        $this->sql->from .= "\nLEFT JOIN $inlineview ON " .
                "$alias.questionusageid = quiza.uniqueid AND $alias.slot = :{$alias}slot";
        $this->sql->params[$alias . 'slot'] = $slot;
        $this->sql->params = array_merge($this->sql->params, $viewparams);
    }

    /**
     * Add a field marks$gradeitemid to the query, with the total score for that grade item.
     *
     * @param int $gradeitemid the grade item to add information for.
     */
    protected function add_grade_item_mark(int $gradeitemid): void {
        $dm = new question_engine_data_mapper();

        $alias = 'gimarks' . $gradeitemid;

        $this->sql->fields .= ",\n(
                SELECT SUM({$alias}qas.fraction * {$alias}qa.maxmark) AS summarks

                  FROM {quiz_slots} {$alias}slot
                  JOIN {question_attempts} {$alias}qa ON {$alias}qa.slot = {$alias}slot.slot
                  JOIN {question_attempt_steps} {$alias}qas ON {$alias}qas.questionattemptid = {$alias}qa.id
                            AND {$alias}qas.sequencenumber = {$dm->latest_step_for_qa_subquery("{$alias}qa.id")}
                 WHERE {$alias}qa.questionusageid = quiza.uniqueid
                   AND {$alias}slot.quizgradeitemid = :{$alias}gradeitemid
            ) AS marks$gradeitemid";
        $this->sql->params[$alias . 'gradeitemid'] = $gradeitemid;
    }

    /**
     * Get an appropriate qubaid_condition for loading more data about the attempts we are displaying.
     *
     * @return qubaid_condition
     */
    protected function get_qubaids_condition() {
        if (is_null($this->rawdata)) {
            throw new coding_exception(
                    'Cannot call get_qubaids_condition until the main data has been loaded.');
        }

        if ($this->is_downloading()) {
            // We want usages for all attempts.
            return new qubaid_join("(
                SELECT DISTINCT quiza.uniqueid
                  FROM " . $this->sql->from . "
                 WHERE " . $this->sql->where . "
                    ) quizasubquery", 'quizasubquery.uniqueid',
                    "1 = 1", $this->sql->params);
        }

        $qubaids = [];
        foreach ($this->rawdata as $attempt) {
            if ($attempt->usageid > 0) {
                $qubaids[] = $attempt->usageid;
            }
        }

        return new qubaid_list($qubaids);
    }

    public function query_db($pagesize, $useinitialsbar = true) {
        $doneslots = [];
        $donegradeitems = [];
        foreach ($this->get_sort_columns() as $column => $notused) {
            $slot = $this->is_latest_step_column($column);
            if ($slot && !in_array($slot, $doneslots)) {
                $this->add_latest_state_join($slot);
                $doneslots[] = $slot;
            }

            $gradeitemid = $this->is_grade_item_column($column);
            if ($gradeitemid && !in_array($gradeitemid, $donegradeitems)) {
                $this->add_grade_item_mark($gradeitemid);
                $donegradeitems[] = $gradeitemid;
            }
        }

        parent::query_db($pagesize, $useinitialsbar);

        // Load grade-item totals if required.
        foreach ($this->columns as $columnname => $notused) {
            if ($this->is_grade_item_column($columnname)) {
                $this->load_grade_item_marks();
                break;
            }
        }

        if ($this->requires_extra_data()) {
            $this->load_extra_data();
        }
    }

    public function get_sort_columns() {
        // Add attemptid as a final tie-break to the sort. This ensures that
        // Attempts by the same student appear in order when just sorting by name.
        $sortcolumns = parent::get_sort_columns();
        $sortcolumns['quiza.id'] = SORT_ASC;
        return $sortcolumns;
    }

    public function wrap_html_start() {
        if ($this->is_downloading() || !$this->includecheckboxes) {
            return;
        }

        $url = $this->options->get_url();
        $url->param('sesskey', sesskey());

        echo '<div id="tablecontainer">';
        echo '<form id="attemptsform" method="post" action="' . $url->out_omit_querystring() . '">';

        echo html_writer::input_hidden_params($url);
        echo '<div>';
    }

    public function wrap_html_finish() {
        global $PAGE;
        if ($this->is_downloading() || !$this->includecheckboxes) {
            return;
        }

        echo '<div id="commands">';
        $this->submit_buttons();
        echo '</div>';

        // Close the form.
        echo '</div>';
        echo '</form></div>';
    }

    /**
     * Output any submit buttons required by the $this->includecheckboxes form.
     */
    protected function submit_buttons() {
        global $PAGE;
        if (has_capability('mod/quiz:deleteattempts', $this->context)) {
            $deletebuttonparams = [
                'type'  => 'submit',
                'class' => 'btn btn-secondary me-1',
                'id'    => 'deleteattemptsbutton',
                'name'  => 'delete',
                'value' => get_string('deleteselected', 'quiz_overview'),
                'data-action' => 'toggle',
                'data-togglegroup' => $this->togglegroup,
                'data-toggle' => 'action',
                'disabled' => true,
                'data-modal' => 'confirmation',
                'data-modal-type' => 'delete',
                'data-modal-content-str' => json_encode(['deleteattemptcheck', 'quiz']),
            ];
            echo html_writer::empty_tag('input', $deletebuttonparams);
        }
    }

    /**
     * Generates the contents for the checkbox column header.
     *
     * It returns the HTML for a master \core\output\checkbox_toggleall component that selects/deselects all quiz attempts.
     *
     * @param string $columnname The name of the checkbox column.
     * @return string
     */
    public function checkbox_col_header(string $columnname) {
        global $OUTPUT;

        // Make sure to disable sorting on this column.
        $this->no_sorting($columnname);

        // Build the select/deselect all control.
        $selectallid = $this->uniqueid . '-selectall-attempts';
        $selectalltext = get_string('selectall', 'quiz');
        $deselectalltext = get_string('selectnone', 'quiz');
        $mastercheckbox = new \core\output\checkbox_toggleall($this->togglegroup, true, [
            'id' => $selectallid,
            'name' => $selectallid,
            'value' => 1,
            'label' => $selectalltext,
            'labelclasses' => 'accesshide',
            'selectall' => $selectalltext,
            'deselectall' => $deselectalltext,
        ]);

        return $OUTPUT->render($mastercheckbox);
    }
}
