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
require_once($CFG->dirroot . '/question/engine/bank.php');
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
    /** @var question_engine_assumption_logger */
    protected $logger;

    public function save_usage($preferredbehaviour, $attempt, $qas, $quizlayout) {
        $missing = array();

        $layout = explode(',', $attempt->layout);
        $questionkeys = array_combine(array_values($layout), array_keys($layout));

        $this->set_quba_preferred_behaviour($attempt->uniqueid, $preferredbehaviour);

        $i = 0;
        foreach (explode(',', $quizlayout) as $questionid) {
            if ($questionid == 0) {
                continue;
            }
            $i++;

            if (!array_key_exists($questionid, $qas)) {
                $missing[] = $questionid;
                $layout[$questionkeys[$questionid]] = $questionid;
                continue;
            }

            $qa = $qas[$questionid];
            $qa->questionusageid = $attempt->uniqueid;
            $qa->slot = $i;
            if (core_text::strlen($qa->questionsummary) > question_bank::MAX_SUMMARY_LENGTH) {
                // It seems some people write very long quesions! MDL-30760
                $qa->questionsummary = core_text::substr($qa->questionsummary,
                        0, question_bank::MAX_SUMMARY_LENGTH - 3) . '...';
            }
            $this->insert_record('question_attempts', $qa);
            $layout[$questionkeys[$questionid]] = $qa->slot;

            foreach ($qa->steps as $step) {
                $step->questionattemptid = $qa->id;
                $this->insert_record('question_attempt_steps', $step);

                foreach ($step->data as $name => $value) {
                    $datum = new stdClass();
                    $datum->attemptstepid = $step->id;
                    $datum->name = $name;
                    $datum->value = $value;
                    $this->insert_record('question_attempt_step_data', $datum, false);
                }
            }
        }

        $this->set_quiz_attempt_layout($attempt->uniqueid, implode(',', $layout));

        if ($missing) {
            notify("Question sessions for questions " .
                    implode(', ', $missing) .
                    " were missing when upgrading question usage {$attempt->uniqueid}.");
        }
    }

    protected function set_quba_preferred_behaviour($qubaid, $preferredbehaviour) {
        global $DB;
        $DB->set_field('question_usages', 'preferredbehaviour', $preferredbehaviour,
                array('id' => $qubaid));
    }

    protected function set_quiz_attempt_layout($qubaid, $layout) {
        global $DB;
        $DB->set_field('quiz_attempts', 'layout', $layout, array('uniqueid' => $qubaid));
    }

    protected function delete_quiz_attempt($qubaid) {
        global $DB;
        $DB->delete_records('quiz_attempts', array('uniqueid' => $qubaid));
        $DB->delete_records('question_attempts', array('id' => $qubaid));
    }

    protected function insert_record($table, $record, $saveid = true) {
        global $DB;
        $newid = $DB->insert_record($table, $record, $saveid);
        if ($saveid) {
            $record->id = $newid;
        }
        return $newid;
    }

    public function load_question($questionid, $quizid = null) {
        return $this->questionloader->get_question($questionid, $quizid);
    }

    public function load_dataset($questionid, $selecteditem) {
        return $this->questionloader->load_dataset($questionid, $selecteditem);
    }

    public function get_next_question_session($attempt, moodle_recordset $questionsessionsrs) {
        if (!$questionsessionsrs->valid()) {
            return false;
        }

        $qsession = $questionsessionsrs->current();
        if ($qsession->attemptid != $attempt->uniqueid) {
            // No more question sessions belonging to this attempt.
            return false;
        }

        // Session found, move the pointer in the RS and return the record.
        $questionsessionsrs->next();
        return $qsession;
    }

    public function get_question_states($attempt, $question, moodle_recordset $questionsstatesrs) {
        $qstates = array();

        while ($questionsstatesrs->valid()) {
            $state = $questionsstatesrs->current();
            if ($state->attempt != $attempt->uniqueid ||
                    $state->question != $question->id) {
                // We have found all the states for this attempt. Stop.
                break;
            }

            // Add the new state to the array, and advance.
            $qstates[] = $state;
            $questionsstatesrs->next();
        }

        return $qstates;
    }

    protected function get_converter_class_name($question, $quiz, $qsessionid) {
        global $DB;
        if ($question->qtype == 'deleted') {
            $where = '(question = :questionid OR '.$DB->sql_like('answer', ':randomid').') AND event = 7';
            $params = array('questionid'=>$question->id, 'randomid'=>"random{$question->id}-%");
            if ($DB->record_exists_select('question_states', $where, $params)) {
                $this->logger->log_assumption("Assuming that deleted question {$question->id} was manually graded.");
                return 'qbehaviour_manualgraded_converter';
            }
        }
        $qtype = question_bank::get_qtype($question->qtype, false);
        if ($qtype->is_manual_graded()) {
            return 'qbehaviour_manualgraded_converter';
        } else if ($question->qtype == 'description') {
            return 'qbehaviour_informationitem_converter';
        } else if ($quiz->preferredbehaviour == 'deferredfeedback') {
            return 'qbehaviour_deferredfeedback_converter';
        } else if ($quiz->preferredbehaviour == 'adaptive') {
            return 'qbehaviour_adaptive_converter';
        } else if ($quiz->preferredbehaviour == 'adaptivenopenalty') {
            return 'qbehaviour_adaptivenopenalty_converter';
        } else {
            throw new coding_exception("Question session {$qsessionid}
                    has an unexpected preferred behaviour {$quiz->preferredbehaviour}.");
        }
    }

    public function supply_missing_question_attempt($quiz, $attempt, $question) {
        if ($question->qtype == 'random') {
            throw new coding_exception("Cannot supply a missing qsession for question
                    {$question->id} in attempt {$attempt->id}.");
        }

        $converterclass = $this->get_converter_class_name($question, $quiz, 'missing');

        $qbehaviourupdater = new $converterclass($quiz, $attempt, $question,
                null, null, $this->logger, $this);
        $qa = $qbehaviourupdater->supply_missing_qa();
        $qbehaviourupdater->discard();
        return $qa;
    }

    public function convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates) {

        if ($question->qtype == 'random') {
            list($question, $qstates) = $this->decode_random_attempt($qstates, $question->maxmark);
            $qsession->questionid = $question->id;
        }

        $converterclass = $this->get_converter_class_name($question, $quiz, $qsession->id);

        $qbehaviourupdater = new $converterclass($quiz, $attempt, $question, $qsession,
                $qstates, $this->logger, $this);
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
                throw new coding_exception("Question session {$this->qsession->id}
                        for random question points to two different real questions
                        {$realquestionid} and {$newquestionid}.");
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

    public function prepare_to_restore() {
        $this->logger = new dummy_question_engine_assumption_logger();
        $this->questionloader = new question_engine_upgrade_question_loader($this->logger);
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
    protected $cache = array();
    protected $datasetcache = array();

    public function __construct($logger) {
        $this->logger = $logger;
    }

    protected function load_question($questionid, $quizid) {
        global $DB;

        if ($quizid) {
            $question = $DB->get_record_sql("
                SELECT q.*, slot.maxmark
                FROM {question} q
                JOIN {quiz_slots} slot ON slot.questionid = q.id
                WHERE q.id = ? AND slot.quizid = ?", array($questionid, $quizid));
        } else {
            $question = $DB->get_record('question', array('id' => $questionid));
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
            $question->questiontext = '<p>' . get_string('warningmissingtype', 'quiz') .
                    '</p>' . $question->questiontext;
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

    public function load_dataset($questionid, $selecteditem) {
        global $DB;

        if (isset($this->datasetcache[$questionid][$selecteditem])) {
            return $this->datasetcache[$questionid][$selecteditem];
        }

        $this->datasetcache[$questionid][$selecteditem] = $DB->get_records_sql_menu('
                SELECT qdd.name, qdi.value
                  FROM {question_dataset_items} qdi
                  JOIN {question_dataset_definitions} qdd ON qdd.id = qdi.definition
                  JOIN {question_datasets} qd ON qdd.id = qd.datasetdefinition
                 WHERE qd.question = ?
                   AND qdi.itemnumber = ?
                ', array($questionid, $selecteditem));
        return $this->datasetcache[$questionid][$selecteditem];
    }
}


/**
 * Base class for the classes that convert the question-type specific bits of
 * the attempt data.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class question_qtype_attempt_updater {
    /** @var object the question definition data. */
    protected $question;
    /** @var question_behaviour_attempt_updater */
    protected $updater;
    /** @var question_engine_assumption_logger */
    protected $logger;
    /** @var question_engine_attempt_upgrader */
    protected $qeupdater;

    public function __construct($updater, $question, $logger, $qeupdater) {
        $this->updater = $updater;
        $this->question = $question;
        $this->logger = $logger;
        $this->qeupdater = $qeupdater;
    }

    public function discard() {
        // Help the garbage collector, which seems to be struggling.
        $this->updater = null;
        $this->question = null;
        $this->logger = null;
        $this->qeupdater = null;
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

    public function is_blank_answer($state) {
        return $state->answer == '';
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

/**
 * This check verifies that all quiz attempts were upgraded since following
 * the question engine upgrade in Moodle 2.1.
 *
 * @param environment_results object to update, if relevant.
 * @return environment_results updated results object, or null if this test is not relevant.
 */
function quiz_attempts_upgraded(environment_results $result) {
    global $DB;

    $dbman = $DB->get_manager();
    $table = new xmldb_table('quiz_attempts');
    $field = new xmldb_field('needsupgradetonewqe');

    if (!$dbman->table_exists($table) || !$dbman->field_exists($table, $field)) {
        // DB already upgraded. This test is no longer relevant.
        return null;
    }

    if (!$DB->record_exists('quiz_attempts', array('needsupgradetonewqe' => 1))) {
        // No 1s present in that column means there are no problems.
        return null;
    }

    // Only display anything if the admins need to be aware of the problem.
    $result->setStatus(false);
    return $result;
}
