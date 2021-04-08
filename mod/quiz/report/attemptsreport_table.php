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
 * Base class for the table used by a {@link quiz_attempts_report}.
 *
 * @package   mod_quiz
 * @copyright 2010 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');


/**
 * Base class for the table used by a {@link quiz_attempts_report}.
 *
 * @copyright 2010 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class quiz_attempts_report_table extends table_sql {
    public $useridfield = 'userid';

    /** @var moodle_url the URL of this report. */
    protected $reporturl;

    /** @var array the display options. */
    protected $displayoptions;

    /**
     * @var array information about the latest step of each question.
     * Loaded by {@link load_question_latest_steps()}, if applicable.
     */
    protected $lateststeps = null;

    /** @var object the quiz settings for the quiz we are reporting on. */
    protected $quiz;

    /** @var context the quiz context. */
    protected $context;

    /** @var string HTML fragment to select the first/best/last attempt, if appropriate. */
    protected $qmsubselect;

    /** @var object mod_quiz_attempts_report_options the options affecting this report. */
    protected $options;

    /** @var \core\dml\sql_join Contains joins, wheres, params to find students
     * in the currently selected group, if applicable.
     */
    protected $groupstudentsjoins;

    /** @var \core\dml\sql_join Contains joins, wheres, params to find the students in the course. */
    protected $studentsjoins;

    /** @var object the questions that comprise this quiz.. */
    protected $questions;

    /** @var bool whether to include the column with checkboxes to select each attempt. */
    protected $includecheckboxes;

    /** @var string The toggle group name for the checkboxes in the checkbox column. */
    protected $togglegroup = 'quiz-attempts';

    /**
     * Constructor
     * @param string $uniqueid
     * @param object $quiz
     * @param context $context
     * @param string $qmsubselect
     * @param mod_quiz_attempts_report_options $options
     * @param \core\dml\sql_join $groupstudentsjoins Contains joins, wheres, params
     * @param \core\dml\sql_join $studentsjoins Contains joins, wheres, params
     * @param array $questions
     * @param moodle_url $reporturl
     */
    public function __construct($uniqueid, $quiz, $context, $qmsubselect,
            mod_quiz_attempts_report_options $options, \core\dml\sql_join $groupstudentsjoins, \core\dml\sql_join $studentsjoins,
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
     * Generate the display of the checkbox column.
     * @param object $attempt the table row being output.
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
     * @param object $attempt the table row being output.
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
     * @param object $attempt the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_fullname($attempt) {
        $html = parent::col_fullname($attempt);
        if ($this->is_downloading() || empty($attempt->attempt)) {
            return $html;
        }

        return $html . html_writer::empty_tag('br') . html_writer::link(
                new moodle_url('/mod/quiz/review.php', array('attempt' => $attempt->attempt)),
                get_string('reviewattempt', 'quiz'), array('class' => 'reviewlink'));
    }

    /**
     * Generate the display of the attempt state column.
     * @param object $attempt the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_state($attempt) {
        if (!is_null($attempt->attempt)) {
            return quiz_attempt::state_name($attempt->state);
        } else {
            return  '-';
        }
    }

    /**
     * Generate the display of the start time column.
     * @param object $attempt the table row being output.
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
     * @param object $attempt the table row being output.
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
     * Generate the display of the time taken column.
     * @param object $attempt the table row being output.
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
     * @param object $attempt the table row being output.
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
     * @param object $attempt data for the row of the table being output.
     * @param int $slot the number used to identify this question within this usage.
     */
    public function make_review_link($data, $attempt, $slot) {
        global $OUTPUT, $CFG;

        $flag = '';
        if ($this->is_flagged($attempt->usageid, $slot)) {
            $flag = $OUTPUT->pix_icon('i/flagged', get_string('flagged', 'question'),
                    'moodle', array('class' => 'questionflag'));
        }

        $feedbackimg = '';
        $state = $this->slot_state($attempt, $slot);
        if ($state->is_finished() && $state != question_state::$needsgrading) {
            $feedbackimg = $this->icon_for_fraction($this->slot_fraction($attempt, $slot));
        }

        $output = html_writer::tag('span', $feedbackimg . html_writer::tag('span',
                $data, array('class' => $state->get_state_class(true))) . $flag, array('class' => 'que'));

        $reviewparams = array('attempt' => $attempt->attempt, 'slot' => $slot);
        if (isset($attempt->try)) {
            $reviewparams['step'] = $this->step_no_for_try($attempt->usageid, $slot, $attempt->try);
        }
        $url = new moodle_url('/mod/quiz/reviewquestion.php', $reviewparams);
        $output = $OUTPUT->action_link($url, $output,
                new popup_action('click', $url, 'reviewquestion',
                        array('height' => 450, 'width' => 650)),
                array('title' => get_string('reviewresponse', 'quiz')));

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
     * @param object $attempt the row data
     * @param int $slot
     * @return question_state
     */
    protected function slot_state($attempt, $slot) {
        $stepdata = $this->lateststeps[$attempt->usageid][$slot];
        return question_state::get($stepdata->state);
    }

    /**
     * @param int $questionusageid
     * @param int $slot
     * @return bool
     */
    protected function is_flagged($questionusageid, $slot) {
        $stepdata = $this->lateststeps[$questionusageid][$slot];
        return $stepdata->flagged;
    }


    /**
     * @param object $attempt the row data
     * @param int $slot
     * @return float
     */
    protected function slot_fraction($attempt, $slot) {
        $stepdata = $this->lateststeps[$attempt->usageid][$slot];
        return $stepdata->fraction;
    }

    /**
     * Return an appropriate icon (green tick, red cross, etc.) for a grade.
     * @param float $fraction grade on a scale 0..1.
     * @return string html fragment.
     */
    protected function icon_for_fraction($fraction) {
        global $OUTPUT;

        $feedbackclass = question_state::graded_state_for_fraction($fraction)->get_feedback_class();
        return $OUTPUT->pix_icon('i/grade_' . $feedbackclass, get_string($feedbackclass, 'question'),
                'moodle', array('class' => 'icon'));
    }

    /**
     * Load any extra data after main query. At this point you can call {@link get_qubaids_condition} to get the condition that
     * limits the query to just the question usages shown in this report page or alternatively for all attempts if downloading a
     * full report.
     */
    protected function load_extra_data() {
        $this->lateststeps = $this->load_question_latest_steps();
    }

    /**
     * Load information about the latest state of selected questions in selected attempts.
     *
     * The results are returned as an two dimensional array $qubaid => $slot => $dataobject
     *
     * @param qubaid_condition|null $qubaids used to restrict which usages are included
     * in the query. See {@link qubaid_condition}.
     * @return array of records. See the SQL in this function to see the fields available.
     */
    protected function load_question_latest_steps(qubaid_condition $qubaids = null) {
        if ($qubaids === null) {
            $qubaids = $this->get_qubaids_condition();
        }
        $dm = new question_engine_data_mapper();
        $latesstepdata = $dm->load_questions_usages_latest_steps(
                $qubaids, array_keys($this->questions));

        $lateststeps = array();
        foreach ($latesstepdata as $step) {
            $lateststeps[$step->questionusageid][$step->slot] = $step;
        }

        return $lateststeps;
    }

    /**
     * Does this report require loading any more data after the main query. After the main query then
     * you can use $this->get
     *
     * @return bool should {@link query_db()} call {@link load_extra_data}?
     */
    protected function requires_extra_data() {
        return $this->requires_latest_steps_loaded();
    }

    /**
     * Does this report require the detailed information for each question from the
     * question_attempts_steps table?
     * @return bool should {@link load_extra_data} call {@link load_question_latest_steps}?
     */
    protected function requires_latest_steps_loaded() {
        return false;
    }

    /**
     * Is this a column that depends on joining to the latest state information?
     * If so, return the corresponding slot. If not, return false.
     * @param string $column a column name
     * @return int false if no, else a slot.
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
     * @param \core\dml\sql_join $allowedstudentsjoins (joins, wheres, params) defines allowed users for the report.
     * @return array with 4 elements ($fields, $from, $where, $params) that can be used to
     *     build the actual database query.
     */
    public function base_sql(\core\dml\sql_join $allowedstudentsjoins) {
        global $DB;

        // Please note this uniqueid column is not the same as quiza.uniqueid.
        $fields = 'DISTINCT ' . $DB->sql_concat('u.id', "'#'", 'COALESCE(quiza.attempt, 0)') . ' AS uniqueid,';

        if ($this->qmsubselect) {
            $fields .= "\n(CASE WHEN $this->qmsubselect THEN 1 ELSE 0 END) AS gradedattempt,";
        }

        // TODO Does not support custom user profile fields (MDL-70456).
        $userfields = \core_user\fields::for_identity($this->context, false)->with_name()
                ->excluding('id', 'idnumber', 'picture', 'imagealt', 'institution', 'department', 'email');
        $extrafields = $userfields->get_sql('u')->selects;
        $fields .= '
                quiza.uniqueid AS usageid,
                quiza.id AS attempt,
                u.id AS userid,
                u.idnumber,
                u.picture,
                u.imagealt,
                u.institution,
                u.department,
                u.email' . $extrafields . ',
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
        $from .= "\nLEFT JOIN {quiz_attempts} quiza ON
                                    quiza.userid = u.id AND quiza.quiz = :quizid";
        $params = array('quizid' => $this->quiz->id);

        if ($this->qmsubselect && $this->options->onlygraded) {
            $from .= " AND (quiza.state <> :finishedstate OR $this->qmsubselect)";
            $params['finishedstate'] = quiz_attempt::FINISHED;
        }

        switch ($this->options->attempts) {
            case quiz_attempts_report::ALL_WITH:
                // Show all attempts, including students who are no longer in the course.
                $where = 'quiza.id IS NOT NULL AND quiza.preview = 0';
                break;
            case quiz_attempts_report::ENROLLED_WITH:
                // Show only students with attempts.
                $from .= "\n" . $allowedstudentsjoins->joins;
                $where = "quiza.preview = 0 AND quiza.id IS NOT NULL AND " . $allowedstudentsjoins->wheres;
                $params = array_merge($params, $allowedstudentsjoins->params);
                break;
            case quiz_attempts_report::ENROLLED_WITHOUT:
                // Show only students without attempts.
                $from .= "\n" . $allowedstudentsjoins->joins;
                $where = "quiza.id IS NULL AND " . $allowedstudentsjoins->wheres;
                $params = array_merge($params, $allowedstudentsjoins->params);
                break;
            case quiz_attempts_report::ENROLLED_ALL:
                // Show all students with or without attempts.
                $from .= "\n" . $allowedstudentsjoins->joins;
                $where = "(quiza.preview = 0 OR quiza.preview IS NULL) AND " . $allowedstudentsjoins->wheres;
                $params = array_merge($params, $allowedstudentsjoins->params);
                break;
        }

        if ($this->options->states) {
            list($statesql, $stateparams) = $DB->get_in_or_equal($this->options->states,
                    SQL_PARAMS_NAMED, 'state');
            $params += $stateparams;
            $where .= " AND (quiza.state $statesql OR quiza.state IS NULL)";
        }

        return array($fields, $from, $where, $params);
    }

    /**
     * A chance for subclasses to modify the SQL after the count query has been generated,
     * and before the full query is constructed.
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
        list($fields, $from, $where, $params) = $this->base_sql($allowedjoins);

        // The WHERE clause is vital here, because some parts of tablelib.php will expect to
        // add bits like ' AND x = 1' on the end, and that needs to leave to valid SQL.
        $this->set_count_sql("SELECT COUNT(1) FROM (SELECT $fields FROM $from WHERE $where) temp WHERE 1 = 1", $params);

        list($fields, $from, $where, $params) = $this->update_sql_after_count($fields, $from, $where, $params);
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
        // It is only used in a subselect to help crappy databases (see MDL-30122)
        // therefore, it is better to use a very simple join, which may include
        // too many records, than to do a super-accurate join.
        $qubaids = new qubaid_join("{quiz_attempts} {$alias}quiza", "{$alias}quiza.uniqueid",
                "{$alias}quiza.quiz = :{$alias}quizid", array("{$alias}quizid" => $this->sql->params['quizid']));

        $dm = new question_engine_data_mapper();
        list($inlineview, $viewparams) = $dm->question_attempt_latest_state_view($alias, $qubaids);

        $this->sql->fields .= ",\n$fields";
        $this->sql->from .= "\nLEFT JOIN $inlineview ON " .
                "$alias.questionusageid = quiza.uniqueid AND $alias.slot = :{$alias}slot";
        $this->sql->params[$alias . 'slot'] = $slot;
        $this->sql->params = array_merge($this->sql->params, $viewparams);
    }

    /**
     * Get an appropriate qubaid_condition for loading more data about the
     * attempts we are displaying.
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

        $qubaids = array();
        foreach ($this->rawdata as $attempt) {
            if ($attempt->usageid > 0) {
                $qubaids[] = $attempt->usageid;
            }
        }

        return new qubaid_list($qubaids);
    }

    public function query_db($pagesize, $useinitialsbar = true) {
        $doneslots = array();
        foreach ($this->get_sort_columns() as $column => $notused) {
            $slot = $this->is_latest_step_column($column);
            if ($slot && !in_array($slot, $doneslots)) {
                $this->add_latest_state_join($slot);
                $doneslots[] = $slot;
            }
        }

        parent::query_db($pagesize, $useinitialsbar);

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
                'class' => 'btn btn-secondary mr-1',
                'id'    => 'deleteattemptsbutton',
                'name'  => 'delete',
                'value' => get_string('deleteselected', 'quiz_overview'),
                'data-action' => 'toggle',
                'data-togglegroup' => $this->togglegroup,
                'data-toggle' => 'action',
                'disabled' => true
            ];
            echo html_writer::empty_tag('input', $deletebuttonparams);
            $PAGE->requires->event_handler('#deleteattemptsbutton', 'click', 'M.util.show_confirm_dialog',
                    array('message' => get_string('deleteattemptcheck', 'quiz')));
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
