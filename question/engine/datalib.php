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
 * Code for loading and saving question attempts to and from the database.
 *
 * A note for future reference. This code is pretty efficient but there are two
 * potential optimisations that could be contemplated, at the cost of making the
 * code more complex:
 *
 * 1. (This is the easier one, but probably not worth doing.) In the unit-of-work
 *    save method, we could get all the ids for steps due to be deleted or modified,
 *    and delete all the question_attempt_step_data for all of those steps in one
 *    query. That would save one DB query for each ->stepsupdated. However that number
 *    is 0 except when re-grading, and when regrading, there are many more inserts
 *    into question_attempt_step_data than deletes, so it is really hardly worth it.
 *
 * 2. A more significant optimisation would be to write an efficient
 *    $DB->insert_records($arrayofrecords) method (for example using functions
 *    like pg_copy_from) and then whenever we save stuff (unit_of_work->save and
 *    insert_questions_usage_by_activity) collect together all the records that
 *    need to be inserted into question_attempt_step_data, and insert them with
 *    a single call to $DB->insert_records. This is likely to be the biggest win.
 *    We do a lot of separate inserts into question_attempt_step_data.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * This class controls the loading and saving of question engine data to and from
 * the database.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_engine_data_mapper {
    /**
     * @var moodle_database normally points to global $DB, but I prefer not to
     * use globals if I can help it.
     */
    protected $db;

    /**
     * @param moodle_database $db a database connectoin. Defaults to global $DB.
     */
    public function __construct($db = null) {
        if (is_null($db)) {
            global $DB;
            $this->db = $DB;
        } else {
            $this->db = $db;
        }
    }

    /**
     * Store an entire {@link question_usage_by_activity} in the database,
     * including all the question_attempts that comprise it.
     * @param question_usage_by_activity $quba the usage to store.
     */
    public function insert_questions_usage_by_activity(question_usage_by_activity $quba) {
        $record = new stdClass();
        $record->contextid = $quba->get_owning_context()->id;
        $record->component = $quba->get_owning_component();
        $record->preferredbehaviour = $quba->get_preferred_behaviour();

        $newid = $this->db->insert_record('question_usages', $record);
        $quba->set_id_from_database($newid);

        foreach ($quba->get_attempt_iterator() as $qa) {
            $this->insert_question_attempt($qa, $quba->get_owning_context());
        }
    }

    /**
     * Store an entire {@link question_attempt} in the database,
     * including all the question_attempt_steps that comprise it.
     * @param question_attempt $qa the question attempt to store.
     * @param context $context the context of the owning question_usage_by_activity.
     */
    public function insert_question_attempt(question_attempt $qa, $context) {
        $record = new stdClass();
        $record->questionusageid = $qa->get_usage_id();
        $record->slot = $qa->get_slot();
        $record->behaviour = $qa->get_behaviour_name();
        $record->questionid = $qa->get_question()->id;
        $record->variant = $qa->get_variant();
        $record->maxmark = $qa->get_max_mark();
        $record->minfraction = $qa->get_min_fraction();
        $record->flagged = $qa->is_flagged();
        $record->questionsummary = $qa->get_question_summary();
        if (textlib_get_instance()->strlen($record->questionsummary) > question_bank::MAX_SUMMARY_LENGTH) {
            // It seems some people write very long quesions! MDL-30760
            $record->questionsummary = textlib_get_instance()->substr($record->questionsummary,
                    0, question_bank::MAX_SUMMARY_LENGTH - 3) . '...';
        }
        $record->rightanswer = $qa->get_right_answer_summary();
        $record->responsesummary = $qa->get_response_summary();
        $record->timemodified = time();
        $record->id = $this->db->insert_record('question_attempts', $record);

        foreach ($qa->get_step_iterator() as $seq => $step) {
            $this->insert_question_attempt_step($step, $record->id, $seq, $context);
        }
    }

    /**
     * Helper method used by insert_question_attempt_step and update_question_attempt_step
     * @param question_attempt_step $step the step to store.
     * @param int $questionattemptid the question attept id this step belongs to.
     * @param int $seq the sequence number of this stop.
     * @return stdClass data to insert into the database.
     */
    protected function make_step_record(question_attempt_step $step, $questionattemptid, $seq) {
        $record = new stdClass();
        $record->questionattemptid = $questionattemptid;
        $record->sequencenumber = $seq;
        $record->state = (string) $step->get_state();
        $record->fraction = $step->get_fraction();
        $record->timecreated = $step->get_timecreated();
        $record->userid = $step->get_user_id();
        return $record;
    }

    /**
     * Helper method used by insert_question_attempt_step and update_question_attempt_step
     * @param question_attempt_step $step the step to store.
     * @param int $stepid the id of the step.
     * @param context $context the context of the owning question_usage_by_activity.
     */
    protected function insert_step_data(question_attempt_step $step, $stepid, $context) {
        foreach ($step->get_all_data() as $name => $value) {
            if ($value instanceof question_file_saver) {
                $value->save_files($stepid, $context);
                $value = (string) $value;
            }

            $data = new stdClass();
            $data->attemptstepid = $stepid;
            $data->name = $name;
            $data->value = $value;
            $this->db->insert_record('question_attempt_step_data', $data, false);
        }
    }

    /**
     * Store a {@link question_attempt_step} in the database.
     * @param question_attempt_step $step the step to store.
     * @param int $questionattemptid the question attept id this step belongs to.
     * @param int $seq the sequence number of this stop.
     * @param context $context the context of the owning question_usage_by_activity.
     */
    public function insert_question_attempt_step(question_attempt_step $step,
            $questionattemptid, $seq, $context) {

        $record = $this->make_step_record($step, $questionattemptid, $seq);
        $record->id = $this->db->insert_record('question_attempt_steps', $record);

        $this->insert_step_data($step, $record->id, $context);
    }

    /**
     * Update a {@link question_attempt_step} in the database.
     * @param question_attempt_step $qa the step to store.
     * @param int $questionattemptid the question attept id this step belongs to.
     * @param int $seq the sequence number of this stop.
     * @param context $context the context of the owning question_usage_by_activity.
     */
    public function update_question_attempt_step(question_attempt_step $step,
            $questionattemptid, $seq, $context) {

        $record = $this->make_step_record($step, $questionattemptid, $seq);
        $record->id = $step->get_id();
        $this->db->update_record('question_attempt_steps', $record);

        $this->db->delete_records('question_attempt_step_data',
                array('attemptstepid' => $record->id));
        $this->insert_step_data($step, $record->id, $context);
    }

    /**
     * Load a {@link question_attempt_step} from the database.
     * @param int $stepid the id of the step to load.
     * @param question_attempt_step the step that was loaded.
     */
    public function load_question_attempt_step($stepid) {
        $records = $this->db->get_recordset_sql("
SELECT
    qas.id AS attemptstepid,
    qas.questionattemptid,
    qas.sequencenumber,
    qas.state,
    qas.fraction,
    qas.timecreated,
    qas.userid,
    qasd.name,
    qasd.value

FROM {question_attempt_steps} qas
LEFT JOIN {question_attempt_step_data} qasd ON qasd.attemptstepid = qas.id

WHERE
    qas.id = :stepid
        ", array('stepid' => $stepid));

        if (!$records->valid()) {
            throw new coding_exception('Failed to load question_attempt_step ' . $stepid);
        }

        $step = question_attempt_step::load_from_records($records, $stepid);
        $records->close();

        return $step;
    }

    /**
     * Load a {@link question_attempt} from the database, including all its
     * steps.
     * @param int $questionattemptid the id of the question attempt to load.
     * @param question_attempt the question attempt that was loaded.
     */
    public function load_question_attempt($questionattemptid) {
        $records = $this->db->get_recordset_sql("
SELECT
    quba.contextid,
    quba.preferredbehaviour,
    qa.id AS questionattemptid,
    qa.questionusageid,
    qa.slot,
    qa.behaviour,
    qa.questionid,
    qa.variant,
    qa.maxmark,
    qa.minfraction,
    qa.flagged,
    qa.questionsummary,
    qa.rightanswer,
    qa.responsesummary,
    qa.timemodified,
    qas.id AS attemptstepid,
    qas.sequencenumber,
    qas.state,
    qas.fraction,
    qas.timecreated,
    qas.userid,
    qasd.name,
    qasd.value

FROM      {question_attempts           qa
JOIN      {question_usages}            quba ON quba.id               = qa.questionusageid
LEFT JOIN {question_attempt_steps}     qas  ON qas.questionattemptid = qa.id
LEFT JOIN {question_attempt_step_data} qasd ON qasd.attemptstepid    = qas.id

WHERE
    qa.id = :questionattemptid

ORDER BY
    qas.sequencenumber
        ", array('questionattemptid' => $questionattemptid));

        if (!$records->valid()) {
            throw new coding_exception('Failed to load question_attempt ' . $questionattemptid);
        }

        $record = current($records);
        $qa = question_attempt::load_from_records($records, $questionattemptid,
                new question_usage_null_observer(), $record->preferredbehaviour);
        $records->close();

        return $qa;
    }

    /**
     * Load a {@link question_usage_by_activity} from the database, including
     * all its {@link question_attempt}s and all their steps.
     * @param int $qubaid the id of the usage to load.
     * @param question_usage_by_activity the usage that was loaded.
     */
    public function load_questions_usage_by_activity($qubaid) {
        $records = $this->db->get_recordset_sql("
SELECT
    quba.id AS qubaid,
    quba.contextid,
    quba.component,
    quba.preferredbehaviour,
    qa.id AS questionattemptid,
    qa.questionusageid,
    qa.slot,
    qa.behaviour,
    qa.questionid,
    qa.variant,
    qa.maxmark,
    qa.minfraction,
    qa.flagged,
    qa.questionsummary,
    qa.rightanswer,
    qa.responsesummary,
    qa.timemodified,
    qas.id AS attemptstepid,
    qas.sequencenumber,
    qas.state,
    qas.fraction,
    qas.timecreated,
    qas.userid,
    qasd.name,
    qasd.value

FROM      {question_usages}            quba
LEFT JOIN {question_attempts}          qa   ON qa.questionusageid    = quba.id
LEFT JOIN {question_attempt_steps}     qas  ON qas.questionattemptid = qa.id
LEFT JOIN {question_attempt_step_data} qasd ON qasd.attemptstepid    = qas.id

WHERE
    quba.id = :qubaid

ORDER BY
    qa.slot,
    qas.sequencenumber
    ", array('qubaid' => $qubaid));

        if (!$records->valid()) {
            throw new coding_exception('Failed to load questions_usage_by_activity ' . $qubaid);
        }

        $quba = question_usage_by_activity::load_from_records($records, $qubaid);
        $records->close();

        return $quba;
    }

    /**
     * Load information about the latest state of each question from the database.
     *
     * @param qubaid_condition $qubaids used to restrict which usages are included
     * in the query. See {@link qubaid_condition}.
     * @param array $slots A list of slots for the questions you want to konw about.
     * @return array of records. See the SQL in this function to see the fields available.
     */
    public function load_questions_usages_latest_steps(qubaid_condition $qubaids, $slots) {
        list($slottest, $params) = $this->db->get_in_or_equal($slots, SQL_PARAMS_NAMED, 'slot');

        $records = $this->db->get_records_sql("
SELECT
    qas.id,
    qa.id AS questionattemptid,
    qa.questionusageid,
    qa.slot,
    qa.behaviour,
    qa.questionid,
    qa.variant,
    qa.maxmark,
    qa.minfraction,
    qa.flagged,
    qa.questionsummary,
    qa.rightanswer,
    qa.responsesummary,
    qa.timemodified,
    qas.id AS attemptstepid,
    qas.sequencenumber,
    qas.state,
    qas.fraction,
    qas.timecreated,
    qas.userid

FROM {$qubaids->from_question_attempts('qa')}
JOIN {question_attempt_steps} qas ON
        qas.id = {$this->latest_step_for_qa_subquery()}

WHERE
    {$qubaids->where()} AND
    qa.slot $slottest
        ", $params + $qubaids->from_where_params());

        return $records;
    }

    /**
     * Load summary information about the state of each question in a group of
     * attempts. This is used, for example, by the quiz manual grading report,
     * to show how many attempts at each question need to be graded.
     *
     * @param qubaid_condition $qubaids used to restrict which usages are included
     * in the query. See {@link qubaid_condition}.
     * @param array $slots A list of slots for the questions you want to konw about.
     * @return array The array keys are slot,qestionid. The values are objects with
     * fields $slot, $questionid, $inprogress, $name, $needsgrading, $autograded,
     * $manuallygraded and $all.
     */
    public function load_questions_usages_question_state_summary(
            qubaid_condition $qubaids, $slots) {
        list($slottest, $params) = $this->db->get_in_or_equal($slots, SQL_PARAMS_NAMED, 'slot');

        $rs = $this->db->get_recordset_sql("
SELECT
    qa.slot,
    qa.questionid,
    q.name,
    CASE qas.state
        {$this->full_states_to_summary_state_sql()}
    END AS summarystate,
    COUNT(1) AS numattempts

FROM {$qubaids->from_question_attempts('qa')}
JOIN {question_attempt_steps} qas ON
        qas.id = {$this->latest_step_for_qa_subquery()}
JOIN {question} q ON q.id = qa.questionid

WHERE
    {$qubaids->where()} AND
    qa.slot $slottest

GROUP BY
    qa.slot,
    qa.questionid,
    q.name,
    q.id,
    CASE qas.state
        {$this->full_states_to_summary_state_sql()}
    END

ORDER BY
    qa.slot,
    qa.questionid,
    q.name,
    q.id
        ", $params + $qubaids->from_where_params());

        $results = array();
        foreach ($rs as $row) {
            $index = $row->slot . ',' . $row->questionid;

            if (!array_key_exists($index, $results)) {
                $res = new stdClass();
                $res->slot = $row->slot;
                $res->questionid = $row->questionid;
                $res->name = $row->name;
                $res->inprogress = 0;
                $res->needsgrading = 0;
                $res->autograded = 0;
                $res->manuallygraded = 0;
                $res->all = 0;
                $results[$index] = $res;
            }

            $results[$index]->{$row->summarystate} = $row->numattempts;
            $results[$index]->all += $row->numattempts;
        }
        $rs->close();

        return $results;
    }

    /**
     * Get a list of usage ids where the question with slot $slot, and optionally
     * also with question id $questionid, is in summary state $summarystate. Also
     * return the total count of such states.
     *
     * Only a subset of the ids can be returned by using $orderby, $limitfrom and
     * $limitnum. A special value 'random' can be passed as $orderby, in which case
     * $limitfrom is ignored.
     *
     * @param qubaid_condition $qubaids used to restrict which usages are included
     * in the query. See {@link qubaid_condition}.
     * @param int $slot The slot for the questions you want to konw about.
     * @param int $questionid (optional) Only return attempts that were of this specific question.
     * @param string $summarystate the summary state of interest, or 'all'.
     * @param string $orderby the column to order by.
     * @param array $params any params required by any of the SQL fragments.
     * @param int $limitfrom implements paging of the results.
     *      Ignored if $orderby = random or $limitnum is null.
     * @param int $limitnum implements paging of the results. null = all.
     * @return array with two elements, an array of usage ids, and a count of the total number.
     */
    public function load_questions_usages_where_question_in_state(
            qubaid_condition $qubaids, $summarystate, $slot, $questionid = null,
            $orderby = 'random', $params, $limitfrom = 0, $limitnum = null) {

        $extrawhere = '';
        if ($questionid) {
            $extrawhere .= ' AND qa.questionid = :questionid';
            $params['questionid'] = $questionid;
        }
        if ($summarystate != 'all') {
            list($test, $sparams) = $this->in_summary_state_test($summarystate);
            $extrawhere .= ' AND qas.state ' . $test;
            $params += $sparams;
        }

        if ($orderby == 'random') {
            $sqlorderby = '';
        } else if ($orderby) {
            $sqlorderby = 'ORDER BY ' . $orderby;
        } else {
            $sqlorderby = '';
        }

        // We always want the total count, as well as the partcular list of ids,
        // based on the paging and sort order. Becuase the list of ids is never
        // going to be too rediculously long. My worst-case scenario is
        // 10,000 students in the coures, each doing 5 quiz attempts. That
        // is a 50,000 element int => int array, which PHP seems to use 5MB
        // memeory to store on a 64 bit server.
        $params += $qubaids->from_where_params();
        $params['slot'] = $slot;
        $qubaids = $this->db->get_records_sql_menu("
SELECT
    qa.questionusageid,
    1

FROM {$qubaids->from_question_attempts('qa')}
JOIN {question_attempt_steps} qas ON
        qas.id = {$this->latest_step_for_qa_subquery()}
JOIN {question} q ON q.id = qa.questionid

WHERE
    {$qubaids->where()} AND
    qa.slot = :slot
    $extrawhere

$sqlorderby
        ", $params);

        $qubaids = array_keys($qubaids);
        $count = count($qubaids);

        if ($orderby == 'random') {
            shuffle($qubaids);
            $limitfrom = 0;
        }

        if (!is_null($limitnum)) {
            $qubaids = array_slice($qubaids, $limitfrom, $limitnum);
        }

        return array($qubaids, $count);
    }

    /**
     * Load a {@link question_usage_by_activity} from the database, including
     * all its {@link question_attempt}s and all their steps.
     * @param qubaid_condition $qubaids used to restrict which usages are included
     * in the query. See {@link qubaid_condition}.
     * @param array $slots if null, load info for all quesitions, otherwise only
     * load the averages for the specified questions.
     */
    public function load_average_marks(qubaid_condition $qubaids, $slots = null) {
        if (!empty($slots)) {
            list($slottest, $slotsparams) = $this->db->get_in_or_equal(
                    $slots, SQL_PARAMS_NAMED, 'slot');
            $slotwhere = " AND qa.slot $slottest";
        } else {
            $slotwhere = '';
            $params = array();
        }

        list($statetest, $stateparams) = $this->db->get_in_or_equal(array(
                (string) question_state::$gaveup,
                (string) question_state::$gradedwrong,
                (string) question_state::$gradedpartial,
                (string) question_state::$gradedright,
                (string) question_state::$mangaveup,
                (string) question_state::$mangrwrong,
                (string) question_state::$mangrpartial,
                (string) question_state::$mangrright), SQL_PARAMS_NAMED, 'st');

        return $this->db->get_records_sql("
SELECT
    qa.slot,
    AVG(COALESCE(qas.fraction, 0)) AS averagefraction,
    COUNT(1) AS numaveraged

FROM {$qubaids->from_question_attempts('qa')}
JOIN {question_attempt_steps} qas ON
        qas.id = {$this->latest_step_for_qa_subquery()}

WHERE
    {$qubaids->where()}
    $slotwhere
    AND qas.state $statetest

GROUP BY qa.slot

ORDER BY qa.slot
        ", $slotsparams + $stateparams + $qubaids->from_where_params());
    }

    /**
     * Load a {@link question_attempt} from the database, including all its
     * steps.
     * @param int $questionid the question to load all the attempts fors.
     * @param qubaid_condition $qubaids used to restrict which usages are included
     * in the query. See {@link qubaid_condition}.
     * @return array of question_attempts.
     */
    public function load_attempts_at_question($questionid, qubaid_condition $qubaids) {
        global $DB;

        $params = $qubaids->from_where_params();
        $params['questionid'] = $questionid;

        $records = $DB->get_recordset_sql("
SELECT
    quba.contextid,
    quba.preferredbehaviour,
    qa.id AS questionattemptid,
    qa.questionusageid,
    qa.slot,
    qa.behaviour,
    qa.questionid,
    qa.variant,
    qa.maxmark,
    qa.minfraction,
    qa.flagged,
    qa.questionsummary,
    qa.rightanswer,
    qa.responsesummary,
    qa.timemodified,
    qas.id AS attemptstepid,
    qas.sequencenumber,
    qas.state,
    qas.fraction,
    qas.timecreated,
    qas.userid,
    qasd.name,
    qasd.value

FROM {$qubaids->from_question_attempts('qa')}
JOIN {question_usages} quba ON quba.id = qa.questionusageid
LEFT JOIN {question_attempt_steps} qas ON qas.questionattemptid = qa.id
LEFT JOIN {question_attempt_step_data} qasd ON qasd.attemptstepid = qas.id

WHERE
    {$qubaids->where()} AND
    qa.questionid = :questionid

ORDER BY
    quba.id,
    qa.id,
    qas.sequencenumber
        ", $params);

        $questionattempts = array();
        while ($records->valid()) {
            $record = $records->current();
            $questionattempts[$record->questionattemptid] =
                    question_attempt::load_from_records($records,
                    $record->questionattemptid, new question_usage_null_observer(),
                    $record->preferredbehaviour);
        }
        $records->close();

        return $questionattempts;
    }

    /**
     * Update a question_usages row to refect any changes in a usage (but not
     * any of its question_attempts.
     * @param question_usage_by_activity $quba the usage that has changed.
     */
    public function update_questions_usage_by_activity(question_usage_by_activity $quba) {
        $record = new stdClass();
        $record->id = $quba->get_id();
        $record->contextid = $quba->get_owning_context()->id;
        $record->component = $quba->get_owning_component();
        $record->preferredbehaviour = $quba->get_preferred_behaviour();

        $this->db->update_record('question_usages', $record);
    }

    /**
     * Update a question_attempts row to refect any changes in a question_attempt
     * (but not any of its steps).
     * @param question_attempt $qa the question attempt that has changed.
     */
    public function update_question_attempt(question_attempt $qa) {
        $record = new stdClass();
        $record->id = $qa->get_database_id();
        $record->maxmark = $qa->get_max_mark();
        $record->minfraction = $qa->get_min_fraction();
        $record->flagged = $qa->is_flagged();
        $record->questionsummary = $qa->get_question_summary();
        $record->rightanswer = $qa->get_right_answer_summary();
        $record->responsesummary = $qa->get_response_summary();
        $record->timemodified = time();

        $this->db->update_record('question_attempts', $record);
    }

    /**
     * Delete a question_usage_by_activity and all its associated
     * {@link question_attempts} and {@link question_attempt_steps} from the
     * database.
     * @param qubaid_condition $qubaids identifies which question useages to delete.
     */
    public function delete_questions_usage_by_activities(qubaid_condition $qubaids) {
        $where = "qa.questionusageid {$qubaids->usage_id_in()}";
        $params = $qubaids->usage_id_in_params();

        $contextids = $this->db->get_records_sql_menu("
                SELECT DISTINCT contextid, 1
                FROM {question_usages}
                WHERE id {$qubaids->usage_id_in()}", $qubaids->usage_id_in_params());
        foreach ($contextids as $contextid => $notused) {
            $this->delete_response_files($contextid, "IN (
                    SELECT qas.id
                    FROM {question_attempts} qa
                    JOIN {question_attempt_steps} qas ON qas.questionattemptid = qa.id
                    WHERE $where)", $params);
        }

        if ($this->db->get_dbfamily() == 'mysql') {
            $this->delete_usage_records_for_mysql($qubaids);
            return;
        }

        $this->db->delete_records_select('question_attempt_step_data', "attemptstepid IN (
                SELECT qas.id
                FROM {question_attempts} qa
                JOIN {question_attempt_steps} qas ON qas.questionattemptid = qa.id
                WHERE $where)", $params);

        $this->db->delete_records_select('question_attempt_steps', "questionattemptid IN (
                SELECT qa.id
                FROM {question_attempts} qa
                WHERE $where)", $params);

        $this->db->delete_records_select('question_attempts',
                "{question_attempts}.questionusageid {$qubaids->usage_id_in()}",
                $qubaids->usage_id_in_params());

        $this->db->delete_records_select('question_usages',
                "{question_usages}.id {$qubaids->usage_id_in()}", $qubaids->usage_id_in_params());
    }

    /**
     * This function is a work-around for poor MySQL performance with
     * DELETE FROM x WHERE id IN (SELECT ...). We have to use a non-standard
     * syntax to get good performance. See MDL-29520.
     * @param qubaid_condition $qubaids identifies which question useages to delete.
     */
    protected function delete_usage_records_for_mysql(qubaid_condition $qubaids) {
        // TODO once MDL-29589 is fixed, eliminate this method, and instead use the new $DB API.
        $this->db->execute('
                DELETE qu, qa, qas, qasd
                  FROM {question_usages}            qu
                  JOIN {question_attempts}          qa   ON qa.questionusageid = qu.id
             LEFT JOIN {question_attempt_steps}     qas  ON qas.questionattemptid = qa.id
             LEFT JOIN {question_attempt_step_data} qasd ON qasd.attemptstepid = qas.id
                 WHERE qu.id ' . $qubaids->usage_id_in(),
                $qubaids->usage_id_in_params());
    }

    /**
     * Delete all the steps for a question attempt.
     * @param int $qaids question_attempt id.
     * @param context $context the context that the $quba belongs to.
     */
    public function delete_steps($stepids, $context) {
        if (empty($stepids)) {
            return;
        }
        list($test, $params) = $this->db->get_in_or_equal($stepids, SQL_PARAMS_NAMED);

        $this->delete_response_files($context->id, $test, $params);

        $this->db->delete_records_select('question_attempt_step_data',
                "attemptstepid $test", $params);
        $this->db->delete_records_select('question_attempt_steps',
                "id $test", $params);
    }

    /**
     * Delete all the files belonging to the response variables in the gives
     * question attempt steps.
     * @param int $contextid the context these attempts belong to.
     * @param string $itemidstest a bit of SQL that can be used in a
     *      WHERE itemid $itemidstest clause. Must use named params.
     * @param array $params any query parameters used in $itemidstest.
     */
    protected function delete_response_files($contextid, $itemidstest, $params) {
        $fs = get_file_storage();
        foreach (question_engine::get_all_response_file_areas() as $filearea) {
            $fs->delete_area_files_select($contextid, 'question', $filearea,
                    $itemidstest, $params);
        }
    }

    /**
     * Delete all the previews for a given question.
     * @param int $questionid question id.
     */
    public function delete_previews($questionid) {
        $previews = $this->db->get_records_sql_menu("
                SELECT DISTINCT quba.id, 1
                FROM {question_usages} quba
                JOIN {question_attempts} qa ON qa.questionusageid = quba.id
                WHERE quba.component = 'core_question_preview' AND
                    qa.questionid = ?", array($questionid));
        if (empty($previews)) {
            return;
        }
        $this->delete_questions_usage_by_activities(new qubaid_list($previews));
    }

    /**
     * Update the flagged state of a question in the database.
     * @param int $qubaid the question usage id.
     * @param int $questionid the question id.
     * @param int $sessionid the question_attempt id.
     * @param bool $newstate the new state of the flag. true = flagged.
     */
    public function update_question_attempt_flag($qubaid, $questionid, $qaid, $slot, $newstate) {
        if (!$this->db->record_exists('question_attempts', array('id' => $qaid,
                'questionusageid' => $qubaid, 'questionid' => $questionid, 'slot' => $slot))) {
            throw new moodle_exception('errorsavingflags', 'question');
        }

        $this->db->set_field('question_attempts', 'flagged', $newstate, array('id' => $qaid));
    }

    /**
     * Get all the WHEN 'x' THEN 'y' terms needed to convert the question_attempt_steps.state
     * column to a summary state. Use this like
     * CASE qas.state {$this->full_states_to_summary_state_sql()} END AS summarystate,
     * @param string SQL fragment.
     */
    protected function full_states_to_summary_state_sql() {
        $sql = '';
        foreach (question_state::get_all() as $state) {
            $sql .= "WHEN '$state' THEN '{$state->get_summary_state()}'\n";
        }
        return $sql;
    }

    /**
     * Get the SQL needed to test that question_attempt_steps.state is in a
     * state corresponding to $summarystate.
     * @param string $summarystate one of
     * inprogress, needsgrading, manuallygraded or autograded
     * @param bool $equal if false, do a NOT IN test. Default true.
     * @return string SQL fragment.
     */
    public function in_summary_state_test($summarystate, $equal = true, $prefix = 'summarystates') {
        $states = question_state::get_all_for_summary_state($summarystate);
        return $this->db->get_in_or_equal(array_map('strval', $states),
                SQL_PARAMS_NAMED, $prefix, $equal);
    }

    /**
     * Change the maxmark for the question_attempt with number in usage $slot
     * for all the specified question_attempts.
     * @param qubaid_condition $qubaids Selects which usages are updated.
     * @param int $slot the number is usage to affect.
     * @param number $newmaxmark the new max mark to set.
     */
    public function set_max_mark_in_attempts(qubaid_condition $qubaids, $slot, $newmaxmark) {
        $this->db->set_field_select('question_attempts', 'maxmark', $newmaxmark,
                "questionusageid {$qubaids->usage_id_in()} AND slot = :slot",
                $qubaids->usage_id_in_params() + array('slot' => $slot));
    }

    /**
     * Return a subquery that computes the sum of the marks for all the questions
     * in a usage. Which useage to compute the sum for is controlled bu the $qubaid
     * parameter.
     *
     * See {@link quiz_update_all_attempt_sumgrades()} for an example of the usage of
     * this method.
     *
     * @param string $qubaid SQL fragment that controls which usage is summed.
     * This will normally be the name of a column in the outer query. Not that this
     * SQL fragment must not contain any placeholders.
     * @return string SQL code for the subquery.
     */
    public function sum_usage_marks_subquery($qubaid) {
        // To explain the COALESCE in the following SQL: SUM(lots of NULLs) gives
        // NULL, while SUM(one 0.0 and lots of NULLS) gives 0.0. We don't want that.
        // We always want to return a number, so the COALESCE is there to turn the
        // NULL total into a 0.
        return "SELECT COALESCE(SUM(qa.maxmark * qas.fraction), 0)
            FROM {question_attempts} qa
            JOIN {question_attempt_steps} qas ON qas.id = (
                SELECT MAX(summarks_qas.id)
                  FROM {question_attempt_steps} summarks_qas
                 WHERE summarks_qas.questionattemptid = qa.id
            )
            WHERE qa.questionusageid = $qubaid
            HAVING COUNT(CASE
                WHEN qas.state = 'needsgrading' AND qa.maxmark > 0 THEN 1
                ELSE NULL
            END) = 0";
    }

    /**
     * Get a subquery that returns the latest step of every qa in some qubas.
     * Currently, this is only used by the quiz reports. See
     * {@link quiz_attempt_report_table::add_latest_state_join()}.
     * @param string $alias alias to use for this inline-view.
     * @param qubaid_condition $qubaids restriction on which question_usages we
     *      are interested in. This is important for performance.
     * @return array with two elements, the SQL fragment and any params requried.
     */
    public function question_attempt_latest_state_view($alias, qubaid_condition $qubaids) {
        return array("(
                SELECT {$alias}qa.id AS questionattemptid,
                       {$alias}qa.questionusageid,
                       {$alias}qa.slot,
                       {$alias}qa.behaviour,
                       {$alias}qa.questionid,
                       {$alias}qa.variant,
                       {$alias}qa.maxmark,
                       {$alias}qa.minfraction,
                       {$alias}qa.flagged,
                       {$alias}qa.questionsummary,
                       {$alias}qa.rightanswer,
                       {$alias}qa.responsesummary,
                       {$alias}qa.timemodified,
                       {$alias}qas.id AS attemptstepid,
                       {$alias}qas.sequencenumber,
                       {$alias}qas.state,
                       {$alias}qas.fraction,
                       {$alias}qas.timecreated,
                       {$alias}qas.userid

                  FROM {$qubaids->from_question_attempts($alias . 'qa')}
                  JOIN {question_attempt_steps} {$alias}qas ON
                           {$alias}qas.id = {$this->latest_step_for_qa_subquery($alias . 'qa.id')}
                 WHERE {$qubaids->where()}
            ) $alias", $qubaids->from_where_params());
    }

    protected function latest_step_for_qa_subquery($questionattemptid = 'qa.id') {
        return "(
                SELECT MAX(id)
                FROM {question_attempt_steps}
                WHERE questionattemptid = $questionattemptid
            )";
    }

    /**
     * @param array $questionids of question ids.
     * @param qubaid_condition $qubaids ids of the usages to consider.
     * @return boolean whether any of these questions are being used by any of
     *      those usages.
     */
    public function questions_in_use(array $questionids, qubaid_condition $qubaids) {
        list($test, $params) = $this->db->get_in_or_equal($questionids);
        return $this->db->record_exists_select('question_attempts',
                'questionid ' . $test . ' AND questionusageid ' .
                $qubaids->usage_id_in(), $params + $qubaids->usage_id_in_params());
    }
}


/**
 * Implementation of the unit of work pattern for the question engine.
 *
 * See http://martinfowler.com/eaaCatalog/unitOfWork.html. This tracks all the
 * changes to a {@link question_usage_by_activity}, and its constituent parts,
 * so that the changes can be saved to the database when {@link save()} is called.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_engine_unit_of_work implements question_usage_observer {
    /** @var question_usage_by_activity the usage being tracked. */
    protected $quba;

    /** @var boolean whether any of the fields of the usage have been changed. */
    protected $modified = false;

    /**
     * @var array list of slot => {@link question_attempt}s that
     * were already in the usage, and which have been modified.
     */
    protected $attemptsmodified = array();

    /**
     * @var array list of slot => {@link question_attempt}s that
     * have been added to the usage.
     */
    protected $attemptsadded = array();

    /**
     * @var array of array(question_attempt_step, question_attempt id, seq number)
     * of steps that have been added to question attempts in this usage.
     */
    protected $stepsadded = array();

    /**
     * @var array of array(question_attempt_step, question_attempt id, seq number)
     * of steps that have been modified in their attempt.
     */
    protected $stepsmodified = array();

    /**
     * @var array list of question_attempt_step.id => question_attempt_step of steps
     * that were previously stored in the database, but which are no longer required.
     */
    protected $stepsdeleted = array();

    /**
     * Constructor.
     * @param question_usage_by_activity $quba the usage to track.
     */
    public function __construct(question_usage_by_activity $quba) {
        $this->quba = $quba;
    }

    public function notify_modified() {
        $this->modified = true;
    }

    public function notify_attempt_modified(question_attempt $qa) {
        $slot = $qa->get_slot();
        if (!array_key_exists($slot, $this->attemptsadded)) {
            $this->attemptsmodified[$slot] = $qa;
        }
    }

    public function notify_attempt_added(question_attempt $qa) {
        $this->attemptsadded[$qa->get_slot()] = $qa;
    }

    public function notify_step_added(question_attempt_step $step, question_attempt $qa, $seq) {
        if (array_key_exists($qa->get_slot(), $this->attemptsadded)) {
            return;
        }

        if (($key = $this->is_step_added($step)) !== false) {
            return;
        }

        if (($key = $this->is_step_modified($step)) !== false) {
            throw new coding_exception('Cannot add a step that has already been modified.');
        }

        if (($key = $this->is_step_deleted($step)) !== false) {
            unset($this->stepsdeleted[$step->get_id()]);
            $this->stepsmodified[] = array($step, $qa->get_database_id(), $seq);
            return;
        }

        $stepid = $step->get_id();
        if ($stepid) {
            if (array_key_exists($stepid, $this->stepsdeleted)) {
                unset($this->stepsdeleted[$stepid]);
            }
            $this->stepsmodified[] = array($step, $qa->get_database_id(), $seq);

        } else {
            $this->stepsadded[] = array($step, $qa->get_database_id(), $seq);
        }
    }

    public function notify_step_modified(question_attempt_step $step, question_attempt $qa, $seq) {
        if (array_key_exists($qa->get_slot(), $this->attemptsadded)) {
            return;
        }

        if (($key = $this->is_step_added($step)) !== false) {
            return;
        }

        if (($key = $this->is_step_deleted($step)) !== false) {
            throw new coding_exception('Cannot modify a step after it has been deleted.');
        }

        $stepid = $step->get_id();
        if (empty($stepid)) {
            throw new coding_exception('Cannot modify a step that has never been stored in the database.');
        }

        $this->stepsmodified[] = array($step, $qa->get_database_id(), $seq);
    }

    public function notify_step_deleted(question_attempt_step $step, question_attempt $qa) {
        if (array_key_exists($qa->get_slot(), $this->attemptsadded)) {
            return;
        }

        if (($key = $this->is_step_added($step)) !== false) {
            unset($this->stepsadded[$key]);
            return;
        }

        if (($key = $this->is_step_modified($step)) !== false) {
            unset($this->stepsmodified[$key]);
        }

        $stepid = $step->get_id();
        if (empty($stepid)) {
            return; // Was never in the database.
        }

        $this->stepsdeleted[$stepid] = $step;
    }

    /**
     * @param question_attempt_step $step a step
     * @return int|false if the step is in the list of steps to be added, return
     *      the key, otherwise return false.
     */
    protected function is_step_added(question_attempt_step $step) {
        foreach ($this->stepsadded as $key => $data) {
            list($addedstep, $qaid, $seq) = $data;
            if ($addedstep === $step) {
                return $key;
            }
        }
        return false;
    }

    /**
     * @param question_attempt_step $step a step
     * @return int|false if the step is in the list of steps to be modified, return
     *      the key, otherwise return false.
     */
    protected function is_step_modified(question_attempt_step $step) {
        foreach ($this->stepsmodified as $key => $data) {
            list($modifiedstep, $qaid, $seq) = $data;
            if ($modifiedstep === $step) {
                return $key;
            }
        }
        return false;
    }

    /**
     * @param question_attempt_step $step a step
     * @return bool whether the step is in the list of steps to be deleted.
     */
    protected function is_step_deleted(question_attempt_step $step) {
        foreach ($this->stepsdeleted as $deletedstep) {
            if ($deletedstep === $step) {
                return true;
            }
        }
        return false;
    }

    /**
     * Write all the changes we have recorded to the database.
     * @param question_engine_data_mapper $dm the mapper to use to update the database.
     */
    public function save(question_engine_data_mapper $dm) {
        $dm->delete_steps(array_keys($this->stepsdeleted), $this->quba->get_owning_context());

        foreach ($this->stepsmodified as $stepinfo) {
            list($step, $questionattemptid, $seq) = $stepinfo;
            $dm->update_question_attempt_step($step, $questionattemptid, $seq,
                    $this->quba->get_owning_context());
        }

        foreach ($this->stepsadded as $stepinfo) {
            list($step, $questionattemptid, $seq) = $stepinfo;
            $dm->insert_question_attempt_step($step, $questionattemptid, $seq,
                    $this->quba->get_owning_context());
        }

        foreach ($this->attemptsadded as $qa) {
            $dm->insert_question_attempt($qa, $this->quba->get_owning_context());
        }

        foreach ($this->attemptsmodified as $qa) {
            $dm->update_question_attempt($qa);
        }

        if ($this->modified) {
            $dm->update_questions_usage_by_activity($this->quba);
        }
    }
}


/**
 * This class represents the promise to save some files from a particular draft
 * file area into a particular file area. It is used beause the necessary
 * information about what to save is to hand in the
 * {@link question_attempt::process_response_files()} method, but we don't know
 * if this question attempt will actually be saved in the database until later,
 * when the {@link question_engine_unit_of_work} is saved, if it is.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_file_saver {
    /** @var int the id of the draft file area to save files from. */
    protected $draftitemid;
    /** @var string the owning component name. */
    protected $component;
    /** @var string the file area name. */
    protected $filearea;

    /**
     * @var string the value to store in the question_attempt_step_data to
     * represent these files.
     */
    protected $value = null;

    /**
     * Constuctor.
     * @param int $draftitemid the draft area to save the files from.
     * @param string $component the component for the file area to save into.
     * @param string $filearea the name of the file area to save into.
     */
    public function __construct($draftitemid, $component, $filearea, $text = null) {
        $this->draftitemid = $draftitemid;
        $this->component = $component;
        $this->filearea = $filearea;
        $this->value = $this->compute_value($draftitemid, $text);
    }

    /**
     * Compute the value that should be stored in the question_attempt_step_data
     * table. Contains a hash that (almost) uniquely encodes all the files.
     * @param int $draftitemid the draft file area itemid.
     * @param string $text optional content containing file links.
     */
    protected function compute_value($draftitemid, $text) {
        global $USER;

        $fs = get_file_storage();
        $usercontext = get_context_instance(CONTEXT_USER, $USER->id);

        $files = $fs->get_area_files($usercontext->id, 'user', 'draft',
                $draftitemid, 'sortorder, filepath, filename', false);

        $string = '';
        foreach ($files as $file) {
            $string .= $file->get_filepath() . $file->get_filename() . '|' .
                    $file->get_contenthash() . '|';
        }

        if ($string) {
            $hash = md5($string);
        } else {
            $hash = '';
        }

        if (is_null($text)) {
            return $hash;
        }

        // We add the file hash so a simple string comparison will say if the
        // files have been changed. First strip off any existing file hash.
        $text = preg_replace('/\s*<!-- File hash: \w+ -->\s*$/', '', $text);
        $text = file_rewrite_urls_to_pluginfile($text, $draftitemid);
        if ($hash) {
            $text .= '<!-- File hash: ' . $hash . ' -->';
        }
        return $text;
    }

    public function __toString() {
        return $this->value;
    }

    /**
     * Actually save the files.
     * @param integer $itemid the item id for the file area to save into.
     */
    public function save_files($itemid, $context) {
        file_save_draft_area_files($this->draftitemid, $context->id,
                $this->component, $this->filearea, $itemid);
    }
}


/**
 * This class represents a restriction on the set of question_usage ids to include
 * in a larger database query. Depending of the how you are going to restrict the
 * list of usages, construct an appropriate subclass.
 *
 * If $qubaids is an instance of this class, example usage might be
 *
 * SELECT qa.id, qa.maxmark
 * FROM $qubaids->from_question_attempts('qa')
 * WHERE $qubaids->where() AND qa.slot = 1
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qubaid_condition {

    /**
     * @return string the SQL that needs to go in the FROM clause when trying
     * to select records from the 'question_attempts' table based on the
     * qubaid_condition.
     */
    public abstract function from_question_attempts($alias);

    /** @return string the SQL that needs to go in the where clause. */
    public abstract function where();

    /**
     * @return the params needed by a query that uses
     * {@link from_question_attempts()} and {@link where()}.
     */
    public abstract function from_where_params();

    /**
     * @return string SQL that can use used in a WHERE qubaid IN (...) query.
     * This method returns the "IN (...)" part.
     */
    public abstract function usage_id_in();

    /**
     * @return the params needed by a query that uses {@link usage_id_in()}.
     */
    public abstract function usage_id_in_params();
}


/**
 * This class represents a restriction on the set of question_usage ids to include
 * in a larger database query based on an explicit list of ids.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qubaid_list extends qubaid_condition {
    /** @var array of ids. */
    protected $qubaids;
    protected $columntotest = null;
    protected $params;

    /**
     * Constructor.
     * @param array $qubaids of question usage ids.
     */
    public function __construct(array $qubaids) {
        $this->qubaids = $qubaids;
    }

    public function from_question_attempts($alias) {
        $this->columntotest = $alias . '.questionusageid';
        return '{question_attempts} ' . $alias;
    }

    public function where() {
        global $DB;

        if (is_null($this->columntotest)) {
            throw new coding_exception('Must call from_question_attempts before where().');
        }
        if (empty($this->qubaids)) {
            $this->params = array();
            return '1 = 0';
        }

        return $this->columntotest . ' ' . $this->usage_id_in();
    }

    public function from_where_params() {
        return $this->params;
    }

    public function usage_id_in() {
        global $DB;

        if (empty($this->qubaids)) {
            $this->params = array();
            return '= 0';
        }
        list($where, $this->params) = $DB->get_in_or_equal(
                $this->qubaids, SQL_PARAMS_NAMED, 'qubaid');
        return $where;
    }

    public function usage_id_in_params() {
        return $this->params;
    }
}


/**
 * This class represents a restriction on the set of question_usage ids to include
 * in a larger database query based on JOINing to some other tables.
 *
 * The general form of the query is something like
 *
 * SELECT qa.id, qa.maxmark
 * FROM $from
 * JOIN {question_attempts} qa ON qa.questionusageid = $usageidcolumn
 * WHERE $where AND qa.slot = 1
 *
 * where $from, $usageidcolumn and $where are the arguments to the constructor.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qubaid_join extends qubaid_condition {
    public $from;
    public $usageidcolumn;
    public $where;
    public $params;

    /**
     * Constructor. The meaning of the arguments is explained in the class comment.
     * @param string $from SQL fragemnt to go in the FROM clause.
     * @param string $usageidcolumn the column in $from that should be
     * made equal to the usageid column in the JOIN clause.
     * @param string $where SQL fragment to go in the where clause.
     * @param array $params required by the SQL. You must use named parameters.
     */
    public function __construct($from, $usageidcolumn, $where = '', $params = array()) {
        $this->from = $from;
        $this->usageidcolumn = $usageidcolumn;
        $this->params = $params;
        if (empty($where)) {
            $where = '1 = 1';
        }
        $this->where = $where;
    }

    public function from_question_attempts($alias) {
        return "$this->from
                JOIN {question_attempts} {$alias} ON " .
                        "{$alias}.questionusageid = $this->usageidcolumn";
    }

    public function where() {
        return $this->where;
    }

    public function from_where_params() {
        return $this->params;
    }

    public function usage_id_in() {
        return "IN (SELECT $this->usageidcolumn FROM $this->from WHERE $this->where)";
    }

    public function usage_id_in_params() {
        return $this->params;
    }
}
