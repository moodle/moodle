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
 * This file contains the code required to upgrade all the attempt data from
 * old versions of Moodle into the tables used by the new question engine.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/engine/upgrade/logger.php');
require_once($CFG->dirroot . '/question/engine/upgrade/behaviourconverters.php');


/**
 * This class manages upgrading all the question attempts from the old database
 * structure to the new question engine.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_engine_attempt_upgrader {
    /** @var question_engine_upgrade_question_loader */
    protected $questionloader;
    protected $logger;

    protected function print_progress($done, $outof, $quizid) {
        gc_collect_cycles();
        print_progress($done, $outof);
    }

    protected function prevent_timeout() {
        set_time_limit(300);
    }

    protected function table_exists($tablename) {
        global $db;
        $metatables = $db->MetaTables();
        $metatables = array_flip($metatables);
        $metatables = array_change_key_case($metatables, CASE_LOWER);
        return array_key_exists($tablename, $metatables);
    }

    protected function get_quiz_ids() {
        global $CFG;
        if ($this->table_exists('vl_v_crs_version_pres')) {
            $quizmoduleid = get_field('modules', 'id', 'name', 'quiz');

            return get_records_sql_menu("
                SELECT quiz.id,1

                FROM {$CFG->prefix}quiz quiz
                JOIN {$CFG->prefix}course_modules cm ON cm.module = {$quizmoduleid}
                        AND cm.instance = quiz.id
                JOIN {$CFG->prefix}course c ON quiz.course = c.id
                LEFT JOIN vl_v_crs_version_pres v ON v.vle_course_short_name = c.shortname
                        AND v.vle_control_course = 'Y'

                WHERE cm.idnumber <> ''
                   OR v.vle_student_close_date IS NULL
                   OR (v.vle_student_close_date > '2010-12-01' AND c.shortname <> 'MU123-10B')
            ");

        } else {
            return get_records_menu('quiz', '', '', 'id', 'id,1');
        }
    }

    public function convert_all_quiz_attempts() {
        $quizids = $this->get_quiz_ids();
        if (!$quizids) {
            return true;
        }

        $done = 0;
        $outof = count($quizids);
        $this->logger = new question_engine_assumption_logger();

        $success = true;
        foreach ($quizids as $quizid => $notused) {
            $this->print_progress($done, $outof, $quizid);

            $quiz = get_record('quiz', 'id', $quizid);
            $success = $success && $this->update_all_attemtps_at_quiz($quiz);
            if (!$success) {
                return false;
            }

            $done += 1;
        }

        $this->print_progress($outof, $outof, 'All done!');
        $this->logger = null;
        return $success;
    }

    public function get_attemtps_where($quizid) {
        return "quiz = {$quizid} AND preview = 0 AND needsupgradetonewqe = 1";
    }

    public function update_all_attemtps_at_quiz($quiz) {
        global $CFG;

        // Wipe question loader cache.
        $this->questionloader = new question_engine_upgrade_question_loader($this->logger);

        begin_sql();

        $where = $this->get_attemtps_where($quiz->id);

        $quizattemptsrs = get_recordset_select('quiz_attempts', $where, 'uniqueid');
        $questionsessionsrs = get_recordset_sql("
                SELECT *
                FROM {$CFG->prefix}question_sessions
                WHERE attemptid IN (
                    SELECT uniqueid FROM {$CFG->prefix}quiz_attempts WHERE $where)
                ORDER BY attemptid, questionid
        ");

        $questionsstatesrs = get_recordset_sql("
                SELECT *
                FROM {$CFG->prefix}question_states
                WHERE attempt IN (
                    SELECT uniqueid FROM {$CFG->prefix}quiz_attempts WHERE $where)
                ORDER BY attempt, question, seq_number, id
        ");

        $success = $quizattemptsrs && $questionsessionsrs && $questionsstatesrs;
        while ($success && ($attempt = rs_fetch_next_record($quizattemptsrs))) {
            $success = $success && $this->convert_quiz_attempt(
                    $quiz, $attempt, $questionsessionsrs, $questionsstatesrs);
        }

        rs_close($quizattemptsrs);
        rs_close($questionsessionsrs);
        rs_close($questionsstatesrs);

        if ($success) {
            commit_sql();
        } else {
            rollback_sql();
        }

        return $success;
    }

    protected function convert_quiz_attempt($quiz, $attempt, $questionsessionsrs, $questionsstatesrs) {
        $qas = array();
        $this->logger->set_current_attempt_id($attempt->id);
        while ($qsession = $this->get_next_question_session($attempt, $questionsessionsrs)) {
            $question = $this->load_question($qsession->questionid, $quiz->id);
            $qstates = $this->get_question_states($attempt, $question, $questionsstatesrs);
            try {
                $qas[$qsession->questionid] = $this->convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates);
            } catch (Exception $e) {
                notify($e->getMessage());
            }
        }
        $this->logger->set_current_attempt_id(null);

        if (empty($qas)) {
            $this->logger->log_assumption("All the question attempts for
                    attempt {$attempt->id} at quiz {$attempt->quiz} were missing.
                    Deleting this attempt", $attempt->id);
            // Somehow, all the question attempt data for this quiz attempt
            // was lost. (This seems to have happened on labspace.)
            // Delete the corresponding quiz attempt.
            return $this->delete_quiz_attempt($attempt->uniqueid);
        }

        $questionorder = array();
        foreach (explode(',', $quiz->questions) as $questionid) {
            if ($questionid == 0) {
                continue;
            }
            if (!array_key_exists($questionid, $qas)) {
                $this->logger->log_assumption("Supplying minimal open state for
                        question {$questionid} in attempt {$attempt->id} at quiz
                        {$attempt->quiz}, since the session was missing.", $attempt->id);
                try {
                    $qas[$questionid] = $this->supply_missing_question_attempt(
                            $quiz, $attempt, $question);
                } catch (Exception $e) {
                    notify($e->getMessage());
                }
            }
        }

        return $this->save_usage($quiz->preferredbehaviour, $attempt, $qas, $quiz->questions);
    }

    protected function save_usage($preferredbehaviour, $attempt, $qas, $quizlayout) {
        $missing = array();
        $success = true;

        $layout = explode(',', $attempt->layout);
        $questionkeys = array_combine(array_values($layout), array_keys($layout));

        $success = $success && $this->set_quba_preferred_behaviour($attempt->uniqueid, $preferredbehaviour);
        if (!$success) {
            return false;
        }

        $i = 0;
        foreach (explode(',', $quizlayout) as $questionid) {
            if ($questionid == 0) {
                continue;
            }
            $i++;

            if (!array_key_exists($questionid, $qas)) {
                $missing[] = $questionid;
                continue;
            }

            $qa = $qas[$questionid];
            $qa->questionusageid = $attempt->uniqueid;
            $qa->slot = $i;
            $success = $success && $this->insert_record('question_attempts', $qa);
            if (!$success) {
                return false;
            }
            $layout[$questionkeys[$questionid]] = $qa->slot;

            foreach ($qa->steps as $step) {
                $step->questionattemptid = $qa->id;
                $success = $success && $this->insert_record('question_attempt_steps', $step);
                if (!$success) {
                    return false;
                }

                foreach ($step->data as $name => $value) {
                    $datum = new stdClass();
                    $datum->attemptstepid = $step->id;
                    $datum->name = $name;
                    $datum->value = $value;
                    $success = $success && $this->insert_record(
                            'question_attempt_step_data', $datum, false);
                }
            }
        }

        $success = $success && $this->set_quiz_attempt_layout($attempt->uniqueid, implode(',', $layout));

        if ($missing) {
            notify("Question sessions for questions " .
                    implode(', ', $missing) .
                    " were missing when upgrading question usage {$attempt->uniqueid}.");
        }

        return $success;
    }

    protected function set_quba_preferred_behaviour($qubaid, $preferredbehaviour) {
        return set_field('question_usages', 'preferredbehaviour', $preferredbehaviour, 'id', $qubaid);
    }

    protected function set_quiz_attempt_layout($qubaid, $layout) {
        $success = true;
        $success = $success && set_field('quiz_attempts', 'layout', $layout, 'uniqueid', $qubaid);
        $success = $success && set_field('quiz_attempts', 'needsupgradetonewqe', 0, 'uniqueid', $qubaid);
        return $success;
    }

    protected function delete_quiz_attempt($qubaid) {
        $success = true;
        $success = $success && delete_records('quiz_attempts', 'uniqueid', $qubaid);
        $success = $success && delete_records('question_attempts', 'id', $qubaid);
        return $success;
    }

    protected function escape_fields($record) {
        foreach (get_object_vars($record) as $field => $value) {
            if (is_string($value)) {
                $record->$field = $value;
            }
        }
    }

    protected function insert_record($table, $record, $saveid = true) {
        $this->escape_fields($record);
        $newid = insert_record($table, $record, $saveid);
        if ($saveid) {
            $record->id = $newid;
        }
        return $newid;
    }

    public function load_question($questionid, $quizid = null) {
        return $this->questionloader->get_question($questionid, $quizid);
    }

    public function get_next_question_session($attempt, $questionsessionsrs) {
        $qsession = rs_fetch_record($questionsessionsrs);

        if (!$qsession || $qsession->attemptid != $attempt->uniqueid) {
            // No more question sessions belonging to this attempt.
            return false;
        }

        // Session found, move the pointer in the RS and return the record.
        rs_next_record($questionsessionsrs);
        return $qsession;
    }

    public function get_question_states($attempt, $question, $questionsstatesrs) {
        $qstates = array();

        while ($state = rs_fetch_record($questionsstatesrs)) {
            if (!$state || $state->attempt != $attempt->uniqueid ||
                    $state->question != $question->id) {
                // We have found all the states for this attempt. Stop.
                break;
            }

            // Add the new state to the array, and advance.
            $qstates[$state->seq_number] = $state;
            rs_next_record($questionsstatesrs);
        }

        return $qstates;
    }

    protected function get_converter_class_name($question, $quiz, $qsessionid) {
        if (in_array($question->qtype, array('calculated', 'multianswer', 'randomsamatch'))) {
            throw new coding_exception("Question session {$qsessionid} uses unsupported question type {$question->qtype}.");
        } else if ($question->qtype == 'essay') {
            return 'qbehaviour_manualgraded_converter';
        } else if ($question->qtype == 'description') {
            return 'qbehaviour_informationitem_converter';
        } else if ($question->qtype == 'opaque') {
            return 'qbehaviour_opaque_converter';
        } else if ($quiz->preferredbehaviour == 'interactive') {
            return 'qbehaviour_interactive_converter';
        } else if ($quiz->preferredbehaviour == 'deferredfeedback') {
            return 'qbehaviour_deferredfeedback_converter';
        } else {
            throw new coding_exception("Question session {$qsessionid} has an unexpected preferred behaviour {$quiz->preferredbehaviour}.");
        }
    }

    public function supply_missing_question_attempt($quiz, $attempt, $question) {
        if ($question->qtype == 'random') {
            throw new coding_exception("Cannot supply a missing qsession for question {$question->id} in attempt {$attempt->id}.");
        }

        $converterclass = $this->get_converter_class_name($question, $quiz, 'missing');

        $qbehaviourupdater = new $converterclass($quiz, $attempt, $question, null, null, $this->logger);
        $qa = $qbehaviourupdater->supply_missing_qa();
        $qbehaviourupdater->discard();
        return $qa;
    }

    public function convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates) {
        $this->prevent_timeout();

        if ($question->qtype == 'random') {
            list($question, $qstates) = $this->decode_random_attempt($qstates, $question->maxmark);
            $qsession->questionid = $question->id;
        }

        $converterclass = $this->get_converter_class_name($question, $quiz, $qsession->id);

        $qbehaviourupdater = new $converterclass($quiz, $attempt, $question, $qsession, $qstates, $this->logger);
        $qa = $qbehaviourupdater->get_converted_qa();
        $qbehaviourupdater->discard();
        return $qa;
    }

    protected function decode_random_attempt($qstates, $maxmark) {
        $realquestionid = null;
        foreach ($qstates as $i => $state) {
            if (strpos($state->answer, '-') < 6) {
                // Broken state, skip it.
                $this->logger->log_assumption("Had to skip brokes state {$state->id}
                        for question {$state->question}.");
                unset($qstates[$i]);
                continue;
            }
            list($randombit, $realanswer) = explode('-', $state->answer, 2);
            $newquestionid = substr($randombit, 6);
            if ($realquestionid && $realquestionid != $newquestionid) {
                throw new coding_exception("Question session {$this->qsession->id} for random question points to two different real questions {$realquestionid} and {$newquestionid}.");
            }
            $qstates[$i]->answer = $realanswer;
        }

        if (empty($newquestionid)) {
            // This attempt only had broken states. Set a fake $newquestionid to
            // prevent a null DB error later.
            $newquestionid = 0;
        }

        $newquestion = $this->load_question($newquestionid);
        $newquestion->maxmark = $maxmark;
        return array($newquestion, $qstates);
    }
}


/**
 * This class deals with loading (and caching) question definitions during the
 * question engine upgrade.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_engine_upgrade_question_loader {
    private $cache = array();

    public function __construct($logger) {
        $this->logger = $logger;
    }

    protected function load_question($questionid, $quizid) {
        global $CFG;

        if ($quizid) {
            $question = get_record_sql("
                SELECT q.*, qqi.grade AS maxmark
                FROM {$CFG->prefix}question q
                JOIN {$CFG->prefix}quiz_question_instances qqi ON qqi.question = q.id
                WHERE q.id = $questionid AND qqi.quiz = $quizid");
        } else {
            $question = get_record('question', 'id', $questionid);
        }

        if (!$question) {
            return null;
        }

        if (empty($question->defaultmark)) {
            if (!empty($question->defaultgrade)) {
                $question->defaultmark = $question->defaultgrade;
            } else {
                $question->defaultmark = 0;
            }
            unset($question->defaultgrade);
        }

        $qtype = question_bank::get_qtype($question->qtype, false);
        if ($qtype->name() === 'missingtype') {
            $this->logger->log_assumption("Dealing with question id {$question->id}
                    that is of an unknown type {$question->qtype}.");
            $question->questiontext = '<p>' . get_string('warningmissingtype', 'quiz') . '</p>' . $question->questiontext;
        }

        $qtype->get_question_options($question);

        return $question;
    }

    public function get_question($questionid, $quizid) {
        if (isset($this->cache[$questionid])) {
            return $this->cache[$questionid];
        }

        $question = $this->load_question($questionid, $quizid);

        if (!$question) {
            $this->logger->log_assumption("Dealing with question id {$questionid}
                    that was missing from the database.");
            $question = new stdClass();
            $question->id = $questionid;
            $question->qtype = 'deleted';
            $question->maxmark = 1; // Guess, but that is all we can do.
            $question->questiontext = get_string('deletedquestiontext', 'qtype_missingtype');
        }

        $this->cache[$questionid] = $question;
        return $this->cache[$questionid];
    }
}


abstract class question_qtype_attempt_updater {
    /** @var question_engine_attempt_upgrader */
    protected $question;
    protected $updater;
    /** @var question_engine_assumption_logger */
    protected $logger;

    public function __construct($updater, $question, $logger) {
        $this->updater = $updater;
        $this->question = $question;
        $this->logger = $logger;
    }

    public function discard() {
        // Help the garbage collector, which seems to be struggling.
        $this->updater = null;
        $this->question = null;
        $this->logger = null;
    }

    protected function to_text($html) {
        return $this->updater->to_text($html);
    }

    public function question_summary() {
        return $this->to_text($this->question->questiontext);
    }

    public function compare_answers($answer1, $answer2) {
        return $answer1 == $answer2;
    }

    public abstract function right_answer();
    public abstract function response_summary($state);
    public abstract function was_answered($state);
    public abstract function set_first_step_data_elements($state, &$data);
    public abstract function set_data_elements_for_step($state, &$data);
    public abstract function supply_missing_first_step_data(&$data);
}


class question_deleted_question_attempt_updater extends question_qtype_attempt_updater {
    public function right_answer() {
        return '';
    }

    public function response_summary($state) {
        return $state->answer;
    }

    public function was_answered($state) {
        return !empty($state->answer);
    }

    public function set_first_step_data_elements($state, &$data) {
        $data['upgradedfromdeletedquestion'] = $state->answer;
    }

    public function supply_missing_first_step_data(&$data) {
    }

    public function set_data_elements_for_step($state, &$data) {
        $data['upgradedfromdeletedquestion'] = $state->answer;
    }
}
