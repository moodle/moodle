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
 * This file contains classes for handling the different question behaviours
 * during upgrade.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Base class for managing the upgrade of a question using a particular behaviour.
 *
 * This class takes as input:
 * 1. Various backgroud data like $quiz, $attempt and $question.
 * 2. The data about the question session to upgrade $qsession and $qstates.
 * Working through that data, it builds up
 * 3. The equivalent new data $qa. This has roughly the same data as a
 * question_attempt object belonging to the new question engine would have, but
 * $this->qa is built up from stdClass objects.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class question_behaviour_attempt_updater {
    /** @var question_qtype_attempt_updater */
    protected $qtypeupdater;
    /** @var question_engine_assumption_logger */
    protected $logger;
    /** @var question_engine_attempt_upgrader */
    protected $qeupdater;

    /**
     * @var object this is the data for the upgraded questions attempt that
     * we are building.
     */
    public $qa;

    /** @var object the quiz settings. */
    protected $quiz;
    /** @var object the quiz attempt data. */
    protected $attempt;
    /** @var object the question definition data. */
    protected $question;
    /** @var object the question session to be upgraded. */
    protected $qsession;
    /** @var array the question states for the session to be upgraded. */
    protected $qstates;
    /** @var stdClass */
    protected $startstate;

    /**
     * @var int counts the question_steps as they are converted to
     * question_attempt_steps.
     */
    protected $sequencenumber;
    /** @var object pointer to the state that has already finished this attempt. */
    protected $finishstate;

    public function __construct($quiz, $attempt, $question, $qsession, $qstates, $logger, $qeupdater) {
        $this->quiz = $quiz;
        $this->attempt = $attempt;
        $this->question = $question;
        $this->qsession = $qsession;
        $this->qstates = $qstates;
        $this->logger = $logger;
        $this->qeupdater = $qeupdater;
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
        $this->qeupdater = null;
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
        $step->timecreated = $this->attempt->timestart ? $this->attempt->timestart : time();
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
        $qa->variant = 1;
        $qa->behaviour = $this->behaviour_name();
        $qa->questionsummary = $this->qtypeupdater->question_summary($this->question);
        $qa->rightanswer = $this->qtypeupdater->right_answer($this->question);
        $qa->maxmark = $this->question->maxmark;
        $qa->minfraction = 0;
        $qa->maxfraction = 1;
        $qa->flagged = 0;
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
            } else if ($this->qtypeupdater->is_blank_answer($state)) {
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

        $step->data['-comment'] = $this->qsession->manualcomment;
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

    /**
     * @param object $question a question definition
     * @return qtype_updater
     */
    protected function make_qtype_updater() {
        global $CFG;

        if ($this->question->qtype == 'deleted') {
            return new question_deleted_question_attempt_updater(
                    $this, $this->question, $this->logger, $this->qeupdater);
        }

        $path = $CFG->dirroot . '/question/type/' . $this->question->qtype . '/db/upgradelib.php';
        if (!is_readable($path)) {
            throw new coding_exception("Question type {$this->question->qtype}
                    is missing important code (the file {$path})
                    required to run the upgrade to the new question engine.");
        }
        include_once($path);
        $class = 'qtype_' . $this->question->qtype . '_qe2_attempt_updater';
        if (!class_exists($class)) {
            throw new coding_exception("Question type {$this->question->qtype}
                    is missing important code (the class {$class})
                    required to run the upgrade to the new question engine.");
        }
        return new $class($this, $this->question, $this->logger, $this->qeupdater);
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
        $step->timecreated = $state->timestamp ? $state->timestamp : time();
        $step->userid = $this->attempt->userid;

        $summary = $this->qtypeupdater->response_summary($state);
        if (!is_null($summary)) {
            $this->qa->responsesummary = $summary;
        }
        $this->qa->timemodified = max($this->qa->timemodified, $state->timestamp);

        return $step;
    }
}


class qbehaviour_deferredfeedback_converter extends question_behaviour_attempt_updater {

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


class qbehaviour_manualgraded_converter extends question_behaviour_attempt_updater {
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
        return $this->process2($step, $state);
    }
}


class qbehaviour_informationitem_converter extends question_behaviour_attempt_updater {

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


class qbehaviour_adaptive_converter extends question_behaviour_attempt_updater {
    protected $try;
    protected $laststepwasatry = false;
    protected $finished = false;
    protected $bestrawgrade = 0;

    protected function behaviour_name() {
        return 'adaptive';
    }

    protected function finish_up() {
        parent::finish_up();
        if ($this->finishstate || !$this->attempt->timefinish) {
            return;
        }

        $state = end($this->qstates);
        $step = $this->make_step($state);
        $this->process6($step, $state);
    }

    protected function process0($step, $state) {
        $this->try = 1;
        $this->laststepwasatry = false;
        parent::process0($step, $state);
    }

    protected function process2($step, $state) {
        if ($this->finishstate) {
            $this->logger->log_assumption("Ignoring bogus save after submit in an " .
                    "adaptive attempt at question {$state->question} " .
                    "(question session {$this->qsession->id})");
            return;
        }

        if ($this->question->maxmark > 0) {
            $step->fraction = $state->grade / $this->question->maxmark;
        }

        $this->laststepwasatry = false;
        parent::process2($step, $state);
    }

    protected function process3($step, $state) {
        if ($this->question->maxmark > 0) {
            $step->fraction = $state->grade / $this->question->maxmark;
            if ($this->graded_state_for_fraction($step->fraction) == 'gradedright') {
                $step->state = 'complete';
            } else {
                $step->state = 'todo';
            }
        } else {
            $step->state = 'complete';
        }

        $this->bestrawgrade = max($state->raw_grade, $this->bestrawgrade);

        $step->data['-_try'] = $this->try;
        $this->try += 1;
        $this->laststepwasatry = true;
        if ($this->question->maxmark > 0) {
            $step->data['-_rawfraction'] = $state->raw_grade / $this->question->maxmark;
        } else {
            $step->data['-_rawfraction'] = 0;
        }
        $step->data['-submit'] = 1;

        $this->add_step($step);
    }

    protected function process6($step, $state) {
        if ($this->finishstate) {
            if (!$this->qtypeupdater->compare_answers($this->finishstate->answer, $state->answer) ||
                    $this->finishstate->grade != $state->grade ||
                    $this->finishstate->raw_grade != $state->raw_grade ||
                    $this->finishstate->penalty != $state->penalty) {
                throw new coding_exception("Two inconsistent finish states found for question session {$this->qsession->id}.");
            } else {
                $this->logger->log_assumption("Ignoring extra finish states in attempt at question {$state->question}");
                return;
            }
        }

        $this->bestrawgrade = max($state->raw_grade, $this->bestrawgrade);

        if ($this->question->maxmark > 0) {
            $step->fraction = $state->grade / $this->question->maxmark;
            $step->state = $this->graded_state_for_fraction(
                    $this->bestrawgrade / $this->question->maxmark);
        } else {
            $step->state = 'finished';
        }

        $step->data['-finish'] = 1;
        if ($this->laststepwasatry) {
            $this->try -= 1;
        }
        $step->data['-_try'] = $this->try;
        if ($this->question->maxmark > 0) {
            $step->data['-_rawfraction'] = $state->raw_grade / $this->question->maxmark;
        } else {
            $step->data['-_rawfraction'] = 0;
        }

        $this->finishstate = $state;
        $this->add_step($step);
    }

    protected function process7($step, $state) {
        $this->unexpected_event($state);
    }
}


class qbehaviour_adaptivenopenalty_converter extends qbehaviour_adaptive_converter {
    protected function behaviour_name() {
        return 'adaptivenopenalty';
    }
}
