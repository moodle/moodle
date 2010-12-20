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
 * @package moodlecore
 * @subpackage questionengine
 * @copyright 2010 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


global $CFG;
require_once($CFG->libdir . '/questionlib.php');


/**
 * This class serves to record all the assumptions that the code had to make
 * during the question engine database database upgrade, to facilitate reviewing them.
 *
 * @copyright 2010 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_engine_assumption_logger {
    protected $handle;
    protected $attemptid;

    public function __construct() {
        global $CFG;
        make_upload_directory('upgradelogs');
        $this->handle = fopen($CFG->dataroot . '/upgradelogs/qe_' .
                date('Ymd-Hi') . '.html', 'a');
        fwrite($this->handle, '<html><head><title>Question engine upgrade assumptions ' .
                date('Ymd-Hi') . '</title></head><body><h2>Question engine upgrade assumptions ' .
                date('Ymd-Hi') . "</h2>\n\n");
    }

    public function set_current_attempt_id($id) {
        $this->attemptid = $id;
    }

    public function log_assumption($description, $quizattemptid = null) {
        global $CFG;
        $message = '<p>' . $description;
        if (!$quizattemptid) {
            $quizattemptid = $this->attemptid;
        }
        if ($quizattemptid) {
        $message .= ' (<a href="' . $CFG->wwwroot . '/mod/quiz/review.php?attempt=' .
                $quizattemptid . '">Review this attempt</a>)';
        }
        $message .= "</p>\n";
        fwrite($this->handle, $message);
    }

    public function __destruct() {
        fwrite($this->handle, '</body></html>');
        fclose($this->handle);
    }
}


/**
 * This class manages upgrading all the question attempts from the old database
 * structure to the new question engine.
 *
 * @copyright 2010 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
                $record->$field = addslashes($value);
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

    public function format_var($name, $var) {
        $out = var_export($var, true);
        $out = str_replace('<', '&lt;', $out);
        $out = str_replace('ADOFetchObj::__set_state(array(', '(object) array(', $out);
        $out = str_replace('stdClass::__set_state(array(', '(object) array(', $out);
        $out = str_replace('array (', 'array(', $out);
        $out = preg_replace('/=> \n\s*/', '=> ', $out);
        $out = str_replace(')),', '),', $out);
        $out = str_replace('))', ')', $out);
        $out = preg_replace('/\n         (?! )/', "\n                        ", $out);
        $out = preg_replace('/\n       (?! )/',   "\n                        ", $out);
        $out = preg_replace('/\n      (?! )/',    "\n                    ", $out);
        $out = preg_replace('/\n     (?! )/',     "\n                ", $out);
        $out = preg_replace('/\n    (?! )/',      "\n                ", $out);
        $out = preg_replace('/\n   (?! )/',       "\n            ", $out);
        $out = preg_replace('/\n  (?! )/',        "\n            ", $out);
        $out = preg_replace('/\n(?! )/',          "\n        ", $out);
        return "        $name = $out;\n";
    }

    public function display_convert_attempt_input($quiz, $attempt, $question, $qsession, $qstates) {
        echo $this->format_var('$quiz', $quiz);
        echo $this->format_var('$attempt', $attempt);
        echo $this->format_var('$question', $question);
        echo $this->format_var('$qsession', $qsession);
        echo $this->format_var('$qstates', $qstates);
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
 * @copyright 2010 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_engine_upgrade_question_loader {
    private $cache = array();

    public function __construct($logger) {
        $this->logger = $logger;
    }

    protected function load_question($questionid, $quizid) {
        global $CFG, $QTYPES;

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

        if (!array_key_exists($question->qtype, $QTYPES)) {
            $this->logger->log_assumption("Dealing with question id {$question->id}
                    that is of an unknown type {$question->qtype}.");
            $question->qtype = 'missingtype';
            $question->questiontext = '<p>' . get_string('warningmissingtype', 'quiz') . '</p>' . $question->questiontext;
        }

        $QTYPES[$question->qtype]->get_question_options($question);

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


abstract class qbehaviour_converter {
    protected $qtypeupdater;
    protected $logger;

    protected $qa;

    protected $quiz;
    protected $attempt;
    protected $question;
    protected $qsession;
    protected $qstates;

    protected $sequencenumber;
    protected $finishstate;
    protected $alreadystarted;

    public function __construct($quiz, $attempt, $question, $qsession, $qstates, $logger) {
        $this->quiz = $quiz;
        $this->attempt = $attempt;
        $this->question = $question;
        $this->qsession = $qsession;
        $this->qstates = $qstates;
        $this->logger = $logger;
    }

    public function discard() {
        // Help the garbage collector, which seems to be struggling.
        $this->quiz = null;
        $this->attempt = null;
        $this->question = null;
        $this->qsession = null;
        $this->qstates = null;
        $this->qa = null;
        $this->qtypeupdater->discard();
        $this->qtypeupdater = null;
        $this->logger = null;
    }

    protected abstract function behaviour_name();

    public function get_converted_qa() {
        $this->initialise_qa();
        $this->convert_steps();
        return $this->qa;
    }

    protected function create_missing_first_step() {
        $step = new stdClass();
        $step->state = 'todo';
        $step->data = array();
        $step->fraction = null;
        $step->timecreated = $this->attempt->timestart;
        $step->userid = $this->attempt->userid;
        $this->qtypeupdater->supply_missing_first_step_data($step->data);
        return $step;
    }

    public function supply_missing_qa() {
        $this->initialise_qa();
        $this->qa->timemodified = $this->attempt->timestart;
        $this->sequencenumber = 0;
        $this->add_step($this->create_missing_first_step());
        return $this->qa;
    }

    protected function initialise_qa() {
        $this->qtypeupdater = $this->make_qtype_updater();

        $qa = new stdClass();
        $qa->questionid = $this->question->id;
        $qa->behaviour = $this->behaviour_name();
        $qa->maxmark = $this->question->maxmark;
        $qa->minfraction = 0;
        $qa->flagged = 0;
        $qa->questionsummary = $this->qtypeupdater->question_summary($this->question);
        $qa->rightanswer = $this->qtypeupdater->right_answer($this->question);
        $qa->responsesummary = '';
        $qa->timemodified = 0;
        $qa->steps = array();

        $this->qa = $qa;
    }

    protected function convert_steps() {
        $this->finishstate = null;
        $this->startstate = null;
        $this->sequencenumber = 0;
        foreach ($this->qstates as $state) {
            $this->process_state($state);
        }
        $this->finish_up();
    }

    protected function process_state($state) {
        $step = $this->make_step($state);
        $method = 'process' . $state->event;
        $this->$method($step, $state);
    }

    protected function finish_up() {
    }

    protected function add_step($step) {
        $step->sequencenumber = $this->sequencenumber;
        $this->qa->steps[] = $step;
        $this->sequencenumber++;
    }

    protected function discard_last_state() {
        array_pop($this->qa->steps);
        $this->sequencenumber--;
    }

    protected function unexpected_event($state) {
        throw new coding_exception("Unexpected event {$state->event} in state {$state->id} in question session {$this->qsession->id}.");
    }

    protected function process0($step, $state) {
        if ($this->startstate) {
            if ($state->answer == reset($this->qstates)->answer) {
                return;
            } else if ($this->quiz->attemptonlast && $this->sequencenumber == 1) {
                // There was a bug in attemptonlast in the past, which meant that
                // it created two inconsistent open states, with the second taking
                // priority. Simulate that be discarding the first open state, then
                // continuing.
                $this->logger->log_assumption("Ignoring bogus state in attempt at question {$state->question}");
                $this->sequencenumber = 0;
                $this->qa->steps = array();
            } else if ($state->answer == '') {
                $this->logger->log_assumption("Ignoring second start state with blank answer in attempt at question {$state->question}");
                return;
            } else {
                throw new coding_exception("Two inconsistent open states for question session {$this->qsession->id}.");
            }
        }
        $step->state = 'todo';
        $this->startstate = $state;
        $this->add_step($step);
    }

    protected function process1($step, $state) {
        $this->unexpected_event($state);
    }

    protected function process2($step, $state) {
        if ($this->qtypeupdater->was_answered($state)) {
            $step->state = 'complete';
        } else {
            $step->state = 'todo';
        }
        $this->add_step($step);
    }

    protected function process3($step, $state) {
        return $this->process6($step, $state);
    }

    protected function process4($step, $state) {
        $this->unexpected_event($state);
    }

    protected function process5($step, $state) {
        $this->unexpected_event($state);
    }

    protected abstract function process6($step, $state);
    protected abstract function process7($step, $state);

    protected function process8($step, $state) {
        return $this->process6($step, $state);
    }

    protected function process9($step, $state) {
        if (!$this->finishstate) {
            $submitstate = clone($state);
            $submitstate->event = 8;
            $submitstate->grade = 0;
            $this->process_state($submitstate);
        }

        $step->data['-comment'] = addslashes($this->qsession->manualcomment);
        if ($this->question->maxmark > 0) {
            $step->fraction = $state->grade / $this->question->maxmark;
            $step->state = $this->manual_graded_state_for_fraction($step->fraction);
            $step->data['-mark'] = $state->grade;
            $step->data['-maxmark'] = $this->question->maxmark;
        } else {
            $step->state = 'manfinished';
        }
        unset($step->data['answer']);
        $step->userid = null;
        $this->add_step($step);
    }

    protected function process10($step, $state) {
        $this->unexpected_event($state);
    }

    /**
     * @param stdClass $question a question definition
     * @return qtype_updater
     */
    protected function make_qtype_updater() {
        $class = 'qtype_' . $this->question->qtype . '_updater';
        return new $class($this, $this->question, $this->logger);
    }

    public function to_text($html) {
        return trim(html_to_text($html, 0, false));
    }

    protected function graded_state_for_fraction($fraction) {
        if ($fraction < 0.000001) {
            return 'gradedwrong';
        } else if ($fraction > 0.999999) {
            return 'gradedright';
        } else {
            return 'gradedpartial';
        }
    }

    protected function manual_graded_state_for_fraction($fraction) {
        if ($fraction < 0.000001) {
            return 'mangrwrong';
        } else if ($fraction > 0.999999) {
            return 'mangrright';
        } else {
            return 'mangrpartial';
        }
    }

    protected function make_step($state){
        $step = new stdClass();
        $step->data = array();

        if ($state->event == 0 || $this->sequencenumber == 0) {
            $this->qtypeupdater->set_first_step_data_elements($state, $step->data);
        } else {
            $this->qtypeupdater->set_data_elements_for_step($state, $step->data);
        }

        $step->fraction = null;
        $step->timecreated = $state->timestamp;
        $step->userid = $this->attempt->userid;

        $summary = $this->qtypeupdater->response_summary($state);
        if (!is_null($summary)) {
            $this->qa->responsesummary = $summary;
        }
        $this->qa->timemodified = max($this->qa->timemodified, $state->timestamp);

        return $step;
    }
}


class qbehaviour_informationitem_converter extends qbehaviour_converter {
    protected function behaviour_name() {
        return 'informationitem';
    }

    protected function process0($step, $state) {
        if ($this->startstate) {
            return;
        }
        $step->state = 'todo';
        $this->startstate = $state;
        $this->add_step($step);
    }

    protected function process2($step, $state) {
        $this->unexpected_event($state);
    }

    protected function process3($step, $state) {
        $this->unexpected_event($state);
    }

    protected function process6($step, $state) {
        if ($this->finishstate) {
            return;
        }

        $step->state = 'finished';
        $step->data['-finish'] = '1';
        $this->finishstate = $state;
        $this->add_step($step);
    }

    protected function process7($step, $state) {
        return $this->process6($step, $state);
    }

    protected function process8($step, $state) {
        return $this->process6($step, $state);
    }
}


class qbehaviour_opaque_converter extends qbehaviour_converter {
    protected function behaviour_name() {
        return 'opaque';
    }

    protected function create_missing_first_step() {
        global $CFG;
        $step = parent::create_missing_first_step();
        $step->data['-_preferredbehaviour'] = $this->quiz->preferredbehaviour;
        $step->data['-_language'] = $CFG->lang;
        $step->data['-_userid'] = $step->userid;
        $step->data['-_statestring'] = 'You have [N] attempts.';
        return $step;
    }

    protected function process0($step, $state) {
        global $CFG;
        $ok = parent::process0($step, $state);
        $step->data['-_preferredbehaviour'] = $this->quiz->preferredbehaviour;
        $step->data['-_language'] = $CFG->lang;
        $step->data['-_userid'] = $step->userid;
        $step->data['-_statestring'] = 'You have [N] attempts.';
        return $ok;
    }

    protected function process2($step, $state) {
        $step->state = 'todo';
        $this->add_step($step);
    }

    protected function process3($step, $state) {
        return $this->process2($step, $state);
    }

    protected function process6($step, $state) {
        if ($this->finishstate) {
            throw new coding_exception("Two finish states found for opaque question session {$this->qsession->id}.");
        }

        if (array_key_exists('-_actionSummary', $step->data) &&
                $step->data['-_actionSummary'] == '[Not completed]') {
            $this->finish_up();
            return;
        }

        if ($this->question->maxmark > 0) {
            $step->fraction = $state->grade / $this->question->maxmark;
            $step->state = $this->graded_state_for_fraction($step->fraction);
        } else {
            $step->state = 'finished';
        }
        $this->finishstate = $state;
        $this->add_step($step);
    }

    protected function process7($step, $state) {
        $this->unexpected_event($state);
    }

    protected function process8($step, $state) {
        $this->process6($step, $state);
    }

    protected function finish_up() {
        if ($this->finishstate || !$this->attempt->timefinish) {
            return;
        }

        $state = end($this->qstates);
        $step = $this->make_step($state);
        $step->data = array('-finish' => 1);
        $step->state = 'gaveup';
        $this->qa->responsesummary = '[Not completed]';
        $this->finishstate = $state;
        $this->add_step($step);
    }
}


class qbehaviour_manualgraded_converter extends qbehaviour_converter {
    protected function behaviour_name() {
        return 'manualgraded';
    }

    protected function process6($step, $state) {
        $step->state = 'needsgrading';
        if (!$this->finishstate) {
            $step->data['-finish'] = '1';
            $this->finishstate = $state;
        }
        $this->add_step($step);
    }

    protected function process7($step, $state) {
        return $this->process6($step, $state);
    }
}


class qbehaviour_interactive_converter extends qbehaviour_converter {
    protected $triesleft;

    protected function behaviour_name() {
        return 'interactive';
    }

    protected function finish_up() {
        if ($this->triesleft == 0 || !$this->attempt->timefinish) {
            return;
        }

        $state = end($this->qstates);
        $step = $this->make_step($state);
        $step->data['-finish'] = 1;

        if ($this->question->maxmark > 0) {
            $step->fraction = $state->grade / $this->question->maxmark;
            $step->state = $this->graded_state_for_fraction($step->fraction);
        } else {
            $step->state = 'finished';
        }

        $this->add_step($step);
    }

    protected function process0($step, $state) {
        $this->triesleft = 1;
        if (!empty($this->question->hints)) {
            $this->triesleft += count($this->question->hints);
        }
        $step->data['-_triesleft'] = $this->triesleft;
        parent::process0($step, $state);
    }

    protected function process2($step, $state) {
        if ($this->finishstate) {
            $this->logger->log_assumption("Ignoring bogus save after submit, and before try again, in interactive attempt at question {$state->question} (question session {$this->qsession->id})");
            return;
        }
        parent::process2($step, $state);
    }

    protected function process3($step, $state) {
        if ($state->id == $this->qsession->newgraded) {
            return $this->process6($step, $state);
        } else {
            return;
        }
    }

    protected function process6($step, $state) {
        if ($this->finishstate) {
            if (!$this->qtypeupdater->compare_answers($this->finishstate->answer, $state->answer) ||
                    $this->finishstate->grade != $state->grade ||
                    $this->finishstate->raw_grade != $state->raw_grade ||
                    $this->finishstate->penalty != $state->penalty) {
                throw new coding_exception("Two inconsistent finish states found for question session {$this->qsession->id}.");
            } else if ($this->triesleft) {
                $step->data = array('-finish' => '1');
                if ($this->question->maxmark > 0) {
                    $step->fraction = $state->grade / $this->question->maxmark;
                    $step->state = $this->graded_state_for_fraction($step->fraction);
                } else {
                    $step->state = 'finished';
                }
                $this->finishstate = $state;
                $this->add_step($step);
                $this->triesleft = 0;
                return;
            } else {
                $this->logger->log_assumption("Ignoring extra finish states in attempt at question {$state->question}");
                return;
            }
        }

        if ($this->question->maxmark > 0) {
            $step->fraction = $state->grade / $this->question->maxmark;
            $step->state = $this->graded_state_for_fraction($state->raw_grade / $this->question->maxmark);
        } else {
            $step->state = 'finished';
        }

        $this->triesleft--;
        $step->data['-submit'] = '1';
        if ($this->triesleft && $step->state != 'gradedright') {
            $step->state = 'todo';
            $step->fraction = null;
            $step->data['-_triesleft'] = $this->triesleft;
        } else {
            $this->triesleft = 0;
        }
        $this->finishstate = $state;
        $this->add_step($step);
    }

    protected function process7($step, $state) {
        $this->unexpected_event($state);
    }

    protected function process10($step, $state) {
        if (!$this->finishstate) {
            $oldcount = $this->sequencenumber;
            $this->process6($step, $state);
            if ($this->sequencenumber != $oldcount + 1) {
                throw new coding_exception('Submit before try again did not keep the step.');
            }
            $step = $this->make_step($state);
        }

        $step->state = 'todo';
        $step->data = array('-tryagain' => 1);
        $this->finishstate = null;
        $this->add_step($step);
    }
}


class qbehaviour_deferredfeedback_converter extends qbehaviour_converter {
    protected function behaviour_name() {
        return 'deferredfeedback';
    }

    protected function process6($step, $state) {
        if (!$this->startstate) {
            $this->logger->log_assumption("Ignoring bogus submit before open in attempt at question {$state->question}");
            // WTF, but this has happened a few times in our DB. It seems it is safe to ignore.
            return;
        }

        if ($this->finishstate) {
            if ($this->finishstate->answer != $state->answer ||
                    $this->finishstate->grade != $state->grade ||
                    $this->finishstate->raw_grade != $state->raw_grade ||
                    $this->finishstate->penalty != $state->penalty) {
                $this->logger->log_assumption("Two inconsistent finish states found for question session {$this->qsession->id} in attempt at question {$state->question} keeping the later one.");
                $this->discard_last_state();
            } else {
                $this->logger->log_assumption("Ignoring extra finish states in attempt at question {$state->question}");
                return;
            }
        }

        if ($this->question->maxmark > 0) {
            $step->fraction = $state->grade / $this->question->maxmark;
            $step->state = $this->graded_state_for_fraction($step->fraction);
        } else {
            $step->state = 'finished';
        }
        $step->data['-finish'] = '1';
        $this->finishstate = $state;
        $this->add_step($step);
    }

    protected function process7($step, $state) {
        $this->unexpected_event($state);
    }
}


abstract class qtype_updater {
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

class qtype_multichoice_updater extends qtype_updater {
    protected $order;

    public function right_answer() {
        if ($this->question->options->single) {
            foreach ($this->question->options->answers as $ans) {
                if ($ans->fraction > 0.999) {
                    return $this->to_text($ans->answer);
                }
            }

        } else {
            $rightbits = array();
            foreach ($this->question->options->answers as $ans) {
                if ($ans->fraction >= 0.000001) {
                    $rightbits[] = $this->to_text($ans->answer);
                }
            }
            return implode('; ', $rightbits);
        }
    }

    protected function explode_answer($answer) {
        if (strpos($answer, ':') !== false) {
            list($order, $responses) = explode(':', $answer);
            return $responses;
        } else {
            // Sometimes, a bug means that a state is missing the <order>: bit,
            // We need to deal with that.
            $this->logger->log_assumption("Dealing with missing order information
                    in attempt at multiple choice question {$this->question->id}");
            return $answer;
        }
    }

    public function response_summary($state) {
        $responses = $this->explode_answer($state->answer);
        if ($this->question->options->single) {
            if (is_numeric($responses)) {
                if (array_key_exists($responses, $this->question->options->answers)) {
                    return $this->to_text($this->question->options->answers[$responses]->answer);
                } else {
                    $this->logger->log_assumption("Dealing with a place where the
                            student selected a choice that was later deleted for
                            multiple choice question {$this->question->id}");
                    return '[CHOICE THAT WAS LATER DELETED]';
                }
            } else {
                return null;
            }

        } else {
            if (!empty($responses)) {
                $responses = explode(',', $responses);
                $bits = array();
                foreach ($responses as $response) {
                    if (array_key_exists($response, $this->question->options->answers)) {
                        $bits[] = $this->to_text($this->question->options->answers[$response]->answer);
                    } else {
                        $this->logger->log_assumption("Dealing with a place where the
                                student selected a choice that was later deleted for
                                multiple choice question {$this->question->id}");
                        $bits[] = '[CHOICE THAT WAS LATER DELETED]';
                    }
                }
                return implode('; ', $bits);
            } else {
                return null;
            }
        }
    }

    public function was_answered($state) {
        $responses = $this->explode_answer($state->answer);
        if ($this->question->options->single) {
            return is_numeric($responses);
        } else {
            return !empty($responses);
        }
    }

    public function set_first_step_data_elements($state, &$data) {
        if (!$state->answer) {
            return;
        }
        list($order, $responses) = explode(':', $state->answer);
        $data['_order'] = $order;
        $this->order = explode(',', $order);
    }

    public function supply_missing_first_step_data(&$data) {
        $data['_order'] = implode(',', array_keys($this->question->options->answers));
    }

    public function set_data_elements_for_step($state, &$data) {
        $responses = $this->explode_answer($state->answer);
        if ($this->question->options->single) {
            if (is_numeric($responses)) {
                $flippedorder = array_combine(array_values($this->order), array_keys($this->order));
                if (array_key_exists($responses, $flippedorder)) {
                    $data['answer'] = $flippedorder[$responses];
                } else {
                    $data['answer'] = '-1';
                }
            }

        } else {
            $responses = explode(',', $responses);
            foreach ($this->order as $key => $ansid) {
                if (in_array($ansid, $responses)) {
                    $data['choice' . $key] = 1;
                } else {
                    $data['choice' . $key] = 0;
                }
            }
        }
    }
}


class qtype_shortanswer_updater extends qtype_updater {
    public function right_answer() {
        foreach ($this->question->options->answers as $ans) {
            if ($ans->fraction > 0.999) {
                return $ans->answer;
            }
        }
    }

    public function was_answered($state) {
        return !empty($state->answer);
    }

    public function response_summary($state) {
        if (!empty($state->answer)) {
            return $state->answer;
        } else {
            return null;
        }
    }

    public function set_first_step_data_elements($state, &$data) {
    }

    public function supply_missing_first_step_data(&$data) {
    }

    public function set_data_elements_for_step($state, &$data) {
        if (!empty($state->answer)) {
            $data['answer'] = $state->answer;
        }
    }
}


class qtype_essay_updater extends qtype_updater {
    public function right_answer() {
        return '';
    }

    public function response_summary($state) {
        if (!empty($state->answer)) {
            return $this->to_text($state->answer);
        } else {
            return null;
        }
    }

    public function was_answered($state) {
        return !empty($state->answer);
    }

    public function set_first_step_data_elements($state, &$data) {
    }

    public function supply_missing_first_step_data(&$data) {
    }

    public function set_data_elements_for_step($state, &$data) {
        if (!empty($state->answer)) {
            $data['answer'] = $state->answer;
        }
    }
}


class qtype_numerical_updater extends qtype_updater {
    public function right_answer() {
        foreach ($this->question->options->answers as $ans) {
            if ($ans->fraction > 0.999) {
                return $ans->answer;
            }
        }
    }

    public function response_summary($state) {
        if (!empty($state->answer)) {
            return $state->answer;
        } else {
            return null;
        }
    }

    public function was_answered($state) {
        return !empty($state->answer);
    }

    public function set_first_step_data_elements($state, &$data) {
        $data['_separators'] = '.$,';
    }

    public function supply_missing_first_step_data(&$data) {
        $data['_separators'] = '.$,';
    }

    public function set_data_elements_for_step($state, &$data) {
        if (!empty($state->answer)) {
            $data['answer'] = $state->answer;
        }
    }
}


class qtype_description_updater extends qtype_updater {
    public function right_answer() {
        return '';
    }

    public function was_answered($state) {
        return false;
    }

    public function response_summary($state) {
        return '';
    }

    public function set_first_step_data_elements($state, &$data) {
    }

    public function supply_missing_first_step_data(&$data) {
    }

    public function set_data_elements_for_step($state, &$data) {
    }
}


class qtype_truefalse_updater extends qtype_updater {
    public function right_answer() {
        foreach ($this->question->options->answers as $ans) {
            if ($ans->fraction > 0.999) {
                return $ans->answer;
            }
        }
    }

    public function response_summary($state) {
        if (is_numeric($state->answer)) {
            if (array_key_exists($state->answer, $this->question->options->answers)) {
                return $this->question->options->answers[$state->answer]->answer;
            } else {
                $this->logger->log_assumption("Dealing with a place where the
                        student selected a choice that was later deleted for
                        true/false question {$this->question->id}");
                return null;
            }
        } else {
            return null;
        }
    }

    public function was_answered($state) {
        return !empty($state->answer);
    }

    public function set_first_step_data_elements($state, &$data) {
    }

    public function supply_missing_first_step_data(&$data) {
    }

    public function set_data_elements_for_step($state, &$data) {
        if (is_numeric($state->answer)) {
            $data['answer'] = (int) ($state->answer == $this->question->options->trueanswer);
        }
    }
}


class qtype_match_updater extends qtype_updater {
    protected $stems;
    protected $choices;
    protected $right;
    protected $stemorder;
    protected $choiceorder;
    protected $flippedchoiceorder;

    public function question_summary() {
        $this->stems = array();
        $this->choices = array();
        $this->right = array();

        foreach ($this->question->options->subquestions as $matchsub) {
            $ans = $matchsub->answertext;
            $key = array_search($matchsub->answertext, $this->choices);
            if ($key === false) {
                $key = $matchsub->id;
                $this->choices[$key] = $matchsub->answertext;
            }

            if ($matchsub->questiontext !== '') {
                $this->stems[$matchsub->id] = $this->to_text($matchsub->questiontext);
                $this->right[$matchsub->id] = $key;
            }
        }

        return $this->to_text($this->question->questiontext) . ' {' .
                implode('; ', $this->stems) . '} -> {' . implode('; ', $this->choices) . '}';
    }

    public function right_answer() {
        $answer = array();
        foreach ($this->stems as $key => $stem) {
            $answer[$stem] = $this->choices[$this->right[$key]];
        }
        return $this->make_summary($answer);
    }

    protected function explode_answer($answer) {
        if (!$answer) {
            return array();
        }
        $bits = explode(',', $answer);
        $selections = array();
        foreach ($bits as $bit) {
            list($stem, $choice) = explode('-', $bit);
            $selections[$stem] = $choice;
        }
        return $selections;
    }

    protected function make_summary($pairs) {
        $bits = array();
        foreach ($pairs as $stem => $answer) {
            $bits[] = $stem . ' -> ' . $answer;
        }
        return implode('; ', $bits);
    }

    protected function lookup_choice($choice) {
        foreach ($this->question->options->subquestions as $matchsub) {
            if ($matchsub->code == $choice) {
                if (array_key_exists($matchsub->id, $this->choices)) {
                    return $matchsub->id;
                } else {
                    return array_search($matchsub->answertext, $this->choices);
                }
            }
        }
        return null;
    }

    public function response_summary($state) {
        $choices = $this->explode_answer($state->answer);
        if (empty($choices)) {
            return null;
        }

        $pairs = array();
        foreach ($choices as $stemid => $choicekey) {
            if (array_key_exists($stemid, $this->stems) && $choices[$stemid]) {
                $choiceid = $this->lookup_choice($choicekey);
                if ($choiceid) {
                    $pairs[$this->stems[$stemid]] = $this->choices[$choiceid];
                } else {
                    $this->logger->log_assumption("Dealing with a place where the
                            student selected a choice that was later deleted for
                            match question {$this->question->id}");
                    $pairs[$this->stems[$stemid]] = '[CHOICE THAT WAS LATER DELETED]';
                }
            }
        }

        if ($pairs) {
            return $this->make_summary($pairs);
        } else {
            return '';
        }
    }

    public function was_answered($state) {
        $choices = $this->explode_answer($state->answer);
        foreach ($choices as $choice) {
            if ($choice) {
                return true;
            }
        }
        return false;
    }

    public function set_first_step_data_elements($state, &$data) {
        $choices = $this->explode_answer($state->answer);
        foreach ($choices as $key => $notused) {
            if (array_key_exists($key, $this->stems)) {
                $this->stemorder[] = $key;
            }
        }

        $this->choiceorder = array_keys($this->choices);
        shuffle($this->choiceorder);
        $this->flippedchoiceorder = array_combine(array_values($this->choiceorder), array_keys($this->choiceorder));

        $data['_stemorder'] = implode(',', $this->stemorder);
        $data['_choiceorder'] = implode(',', $this->choiceorder);
    }

    public function supply_missing_first_step_data(&$data) {
        throw new coding_exception('qtype_match_updater::supply_missing_first_step_data not tested');
        $data['_stemorder'] = array_keys($this->stems);
        $data['_choiceorder'] = shuffle(array_keys($this->choices));
    }

    public function set_data_elements_for_step($state, &$data) {
        $choices = $this->explode_answer($state->answer);

        foreach ($this->stemorder as $i => $key) {
            if (empty($choices[$key])) {
                $data['sub' . $i] = 0;
                continue;
            }
            $choice = $this->lookup_choice($choices[$key]);

            if (array_key_exists($choice, $this->flippedchoiceorder)) {
                $data['sub' . $i] = $this->flippedchoiceorder[$choice] + 1;
            } else {
                $data['sub' . $i] = 0;
            }
        }
    }
}


class qtype_oumultiresponse_updater extends qtype_updater {
    public function question_summary() {
        $bits = array();
        foreach ($this->question->options->answers as $ans) {
            $bits[] = $this->to_text($ans->answer);
        }
        return parent::question_summary() . ': ' . implode('; ', $bits);
    }

    public function right_answer() {
        $rightbits = array();
        foreach ($this->question->options->answers as $ans) {
            if ($ans->fraction >= 0.5) {
                $rightbits[] = $this->to_text($ans->answer);
            }
        }
        return implode('; ', $rightbits);
    }

    protected function parse_response($answer) {
        if (strpos($answer, ':') === false) {
            $this->logger->log_assumption("Dealing with missing order information
                    in attempt at oumultiresponse question {$this->question->id}");
            return array(null, $responses);
        }

        list($order, $responsepart) = explode(':', $answer);
        $bits = explode(',', $responsepart);

        $responses = array();
        if ($responsepart) {
            foreach ($bits as $bit) {
                if (strpos($bit, 'h')) {
                    list($choice, $history) = explode('h', $bit);
                    if (substr($history, -1) === '1') {
                        $responses[] = $choice;
                    }
                } else {
                    // Very old code did this.
                    list($choice, $grade) = explode('g', $bit);
                    if ($grade > 0) {
                        $responses[] = $choice;
                    }
                }
            }
        }

        return array($order, $responses);
    }

    public function response_summary($state) {
        list($order, $responses) = $this->parse_response($state->answer);

        $bits = array();
        foreach ($responses as $response) {
            $bits[] = $this->to_text($this->question->options->answers[$response]->answer);
        }
        return implode('; ', $bits);
    }

    public function was_answered($state) {
        list($order, $responses) = explode(':', $state->answer);
        return !empty($responses);
    }

    public function set_first_step_data_elements($state, &$data) {
        list($order, $responses) = $this->parse_response($state->answer);
        $data['_order'] = $order;
    }

    public function supply_missing_first_step_data(&$data) {
        throw new coding_exception('qtype_oumultiresponse_updater::supply_missing_first_step_data not tested');
        $data['_order'] = implode(',', array_keys($this->question->option->answers));
    }

    public function set_data_elements_for_step($state, &$data) {
        list($order, $responses) = $this->parse_response($state->answer);
        $order = explode(',', $order);

        foreach ($order as $key => $ans) {
            if (in_array($ans, $responses)) {
                $data['choice' . $key] = 1;
            } else {
                $data['choice' . $key] = 0;
            }
        }
    }
}


class qtype_ddwtos_updater extends qtype_updater {
    protected $choices;
    protected $rightchoices;
    protected $places;
    protected $choiceindexmap;
    protected $shuffleorders;

    public function question_summary() {
        $this->choices = array();
        $choiceindexmap = array();

        // Store the choices in arrays by group.
        $i = 1;
        foreach ($this->question->options->answers as $choicedata) {
            $options = unserialize($choicedata->feedback);

            if (array_key_exists($options->draggroup, $this->choices)) {
                $this->choices[$options->draggroup][] = $choicedata->answer;
            } else {
                $this->choices[$options->draggroup][1] = $choicedata->answer;
            }

            end($this->choices[$options->draggroup]);
            $this->choiceindexmap[$i] = array($options->draggroup, $choicedata->answer,
                    key($this->choices[$options->draggroup]));
            $i += 1;
        }

        $this->places = array();
        $this->rightchoices = array();

        // Break up the question text, and store the fragments, places and right answers.
        $bits = preg_split('/\[\[(\d+)]]/', $this->question->questiontext, null, PREG_SPLIT_DELIM_CAPTURE);
        array_shift($bits);
        $i = 1;

        while (!empty($bits)) {
            $choice = array_shift($bits);

            list($group, $choicetext, $choiceindex) = $this->choiceindexmap[$choice];
            $this->places[$i] = $group;
            $this->rightchoices[$i] = $choicetext;

            array_shift($bits);
            $i += 1;
        }

        $bits = array(parent::question_summary());
        foreach ($this->places as $place => $group) {
            $bits[] = '[[' . $place . ']] -> {' .
                    implode(' / ', $this->choices[$group]) . '}';
        }
        return implode('; ', $bits);
    }

    public function right_answer() {
        return $this->make_summary($this->rightchoices);
    }

    public function compare_answers($answer1, $answer2) {
        list($answer1) = explode('=', $answer1);
        list($answer2) = explode('=', $answer2);
        return $answer1 == $answer2;
    }

    protected function explode_answer($answer) {
        list($answer) = explode('=', $answer);
        if (!$answer) {
            return array();
        }

        $bits = explode(';', $answer);

        $selections = array();
        foreach ($bits as $bit) {
            list($place, $choice) = explode('-', $bit);
            if ($place === '' || $choice === '0') {
                continue;
            }

            $selections[$place + 1] = $choice;
        }

        return $selections;
    }

    protected function make_summary($choices) {
        $answers = array();
        $allblank = true;
        foreach ($choices as $group => $ans) {
            $answers[] = '{' . $ans . '}';
            $allblank = $allblank && ($ans === '');
        }
        if ($allblank) {
            return '';
        } else {
            return implode(' ', $answers);
        }
    }

    public function response_summary($state) {
        $choices = $this->explode_answer($state->answer);

        $answers = array();
        $allblank = true;
        foreach ($this->places as $place => $group) {
            if (array_key_exists($place, $choices) && $choices[$place]) {
                list($notused, $choicetext, $choiceindex) =
                        $this->choiceindexmap[$choices[$place]];
                $answers[$place] = $choicetext;
            } else {
                $answers[$place] = '';
            }
        }
        return $this->make_summary($answers);
    }

    public function was_answered($state) {
        $choices = $this->explode_answer($state->answer);
        foreach ($choices as $choice) {
            if ($choice) {
                return true;
            }
        }
        return false;
    }

    public function set_first_step_data_elements($state, &$data) {
        foreach ($this->choices as $group => $notused) {
            $this->shuffleorders[$group] = array_keys($this->choices[$group]);
            if ($this->question->options->shuffleanswers) {
                srand($state->attempt);
                shuffle($this->shuffleorders[$group]);
            }
            $this->shuffleorders[$group] = array_combine(
                    array_values($this->shuffleorders[$group]), array_keys($this->shuffleorders[$group]));
        }

        foreach ($this->choices as $group => $choices) {
            $indices = array();
            foreach ($this->shuffleorders[$group] as $key => $notused) {
                $indices[] = $key;
            }
            $data['_choiceorder' . $group] = implode(',', $indices);
        }
    }

    public function supply_missing_first_step_data(&$data) {
        throw new coding_exception('qtype_ddwtos_updater::supply_missing_first_step_data not implemented');
    }

    public function set_data_elements_for_step($state, &$data) {
        $choices = $this->explode_answer($state->answer);

        foreach ($this->places as $place => $group) {
            if (array_key_exists($place, $choices) &&
                    array_key_exists($choices[$place], $this->choiceindexmap)) {
                list($notused, $choicetext, $choiceindex) =
                        $this->choiceindexmap[$choices[$place]];
                $data['p' . $place] = $this->shuffleorders[$this->places[$place]][$choiceindex] + 1;
            } else {
                $data['p' . $place] = 0;
            }
        }
    }
}


class qtype_opaque_updater extends qtype_updater {
    public function question_summary() {
        return $this->question->options->remoteid . '.' . $this->question->options->remoteversion;
    }

    public function right_answer() {
        return '[UNKNOWN]';
    }

    protected function explode_answer($answer) {
        // We store the reponses by turning the associative array $state->responses
        // into a string as follows. For example, array('f2' => 'No, never - ever', 'f1' => '10')
        // becomes 'f1-10,f2-No\, never - ever'. That is, comma separated pairs, sorted by key,
        // key and value linked with a '-', commas in vales escaped with '\'. 

        // Deal with special case: no responses at all.
        if (empty($answer)) {
            return array();
        }

        // Split the responses on non-backslash-escaped commas.
        $bits = preg_split('/(?<!\\\\)\\,/', $answer);

        // Now set $state->responses properly.
        $responses = array();
        foreach ($bits as $reponse) {
            list($key, $value) = explode('-', $reponse, 2);
            $responses[$key] = str_replace('\,', ',', $value);
        }

        return $responses;
    }

    public function response_summary($state) {
        $responses = $this->explode_answer($state->answer);

        if (!empty($responses['__answerLine'])) {
            return $responses['__answerLine'];

        } else if (!empty($responses['__actionSummary'])) {
            return $responses['__actionSummary'];

        } else {
            return implode(', ', $responses);
        }
    }

    public function was_answered($state) {
        return false;
    }

    public function set_first_step_data_elements($state, &$data) {
        $responses = $this->explode_answer($state->answer);
        foreach ($responses as $name => $value) {
            if ($name == '__randomseed') {
                $data['-_randomseed'] = $value;
            } else {
                $data[$name] = $value;
            }
        }
    }

    public function supply_missing_first_step_data(&$data) {
        $data['-_randomseed'] = rand();
    }

    public function set_data_elements_for_step($state, &$data) {
        $responses = $this->explode_answer($state->answer);
        foreach ($responses as $name => $value) {
            if ($name == '__questionLine') {
                continue;
            } else if ($name == '__actionSummary') {
                $name = '-_actionSummary';
            }
            $data[$name] = $value;
        }
    }
}

class qtype_deleted_updater extends qtype_updater {
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
