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
 * This class performs the simple algorithm to determine the next level of difficulty a student should attempt.
 * It also recommends whether the calculation has reached an acceptable level of error.
 *
 * @copyright  2013 onwards Remote-Learner {@link http://www.remote-learner.ca/}
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\local;

use coding_exception;
use dml_missing_record_exception;
use moodle_exception;
use question_state_gradedpartial;
use question_state_gradedright;
use question_state_gradedwrong;
use question_state_todo;
use question_usage_by_activity;
use stdClass;

class catalgo {
    /** @var $quba a question_usage_by_activity object */
    protected $quba = null;

    /** @var $attemptid an adaptivequiz_attempt attempt id */
    protected $attemptid = 0;

    /**
     * @var bool $debugenabled flag to denote developer debugging is enabled and this class should write message to the debug array
     */
    protected $debugenabled = false;

    /** @var array $debug debugging array of messages */
    protected $debug = array();

    /** @var int $level level of difficulty of the most recently attempted question */
    protected $level = 0;

    /**
     * @var float $levelogit the logit value of the difficulty level represented as a percentage of the minimum and maximum
     *      difficulty @see compute_next_difficulty()
     */
    protected $levellogit = 0.0;

    /** @var bool $readytostop flag to denote whether to assume the student has met the minimum requirements */
    protected $readytostop = true;

    /** @var int $questattempted the sum number of questions attempted */
    protected $questattempted = 0;

    /** @var $difficultysum the sum of the difficulty levels attempted */
    protected $difficultysum = 0;

    /** @var int $nextdifficulty the next dificulty level to administer */
    protected $nextdifficulty = 0;

    /** @var int $sumofcorrectanswers the sum of questions answered correctly */
    protected $sumofcorrectanswers;

    /** @var int @sumofincorrectanswers the sum of questions answered incorretly */
    protected $sumofincorrectanswers;

    /** @var float $measure the ability measure */
    protected $measure = 0.0;

    /** @var float $standarderror the standard error of the measure */
    protected $standarderror = 0.0;

    /** @var string $status status message storing the reason why the attempt needs to be stopped */
    protected $status = '';

    /**
     * Constructor to initialize the parameters needed by the adaptive alrogithm
     * @throws moodle_exception - exception is thrown if first argument is not an instance of question_usage_by_activity class or
     *      second argument is not a positive integer.
     * @param question_usage_by_activity $quba an object loaded using the unique id of the attempt
     * @param int $attemptid the adaptivequiz_attempt attempt id
     * @param bool $readytostop true of the algo should assume the user has answered the minimum number of question and should
     *      compare the results againts the standard error
     * @param int $level the level of difficulty for the most recently attempted question
     * @return void
     */
    public function __construct($quba, $attemptid, $readytostop = true, $level = 0) {
        if (!$quba instanceof question_usage_by_activity) {
            throw new coding_exception('catalgo: Argument 1 is not a question_usage_by_activity object',
                    'Question usage by activity must be a question_usage_by_activity object');
        }

        if (!is_int($attemptid) || 0 >= $attemptid) {
            throw new coding_exception('catalgo: Argument 2 not a positive integer',
                'Attempt id argument must be a positive integer');
        }

        if (!is_int($level) || 0 >= $level) {
            throw new coding_exception('catalgo: Argument 4 not a positive integer', 'level must be a positive integer');
        }

        $this->quba = $quba;
        $this->attemptid = $attemptid;
        $this->readytostop = $readytostop;
        $this->level = $level;

        if (debugging('', DEBUG_DEVELOPER)) {
            $this->debugenabled = true;
        }
    }

    /**
     * This function adds a message to the debugging array
     * @param string $message details of the debugging message
     * @return void
     */
    protected function print_debug($message = '') {
        if ($this->debugenabled) {
            $this->debug[] = $message;
        }
    }

    /**
     * Answer a string view of a variable for debugging purposes
     * @param mixed $variable
     */
    protected function vardump($variable) {
        ob_start();
        var_dump($variable);
        return ob_get_clean();
    }

    /**
     * This function returns the debug array
     * @return array array of debugging messages
     */
    public function get_debug() {
        return $this->debug;
    }

    /**
     * This function returns the $difficultysum property
     * @return int returns the $difficultysum property
     */
    public function get_difficultysum() {
        return $this->difficultysum;
    }

    /**
     * This function returns the $levellogit property
     * @return float retuns the $levellogit property
     */
    public function get_levellogit() {
        return $this->levellogit;
    }

    /**
     * This function returns the $standarderror property
     * @return float retuns the $standarderror property
     */
    public function get_standarderror() {
        return $this->standarderror;
    }

    /**
     * This function returns the $measure property
     * @return float retuns the $measure property
     */
    public function get_measure() {
        return $this->measure;
    }

    /**
     * This functions retrieves the attempt record, the highest and lowest difficulty level set for the attempt
     * @throws dml_missing_record_exception
     * @param int $attemptid the attempt id record
     * @return stdClass adaptivequiz_attempt record
     */
    public function retrieve_attempt_record($attemptid) {
        global $DB;

        $param = array('id' => $attemptid);
        $sql = "SELECT aa.id, aa.questionsattempted, aa.difficultysum, aa.standarderror, a.highestlevel, a.lowestlevel, aa.measure
                  FROM {adaptivequiz_attempt} aa
                  JOIN {adaptivequiz} a ON a.id = aa.instance
                 WHERE aa.id = :id
              ORDER BY id DESC";
        $record = $DB->get_record_sql($sql, $param, MUST_EXIST);
        return $record;
    }

    /**
     * Refactored code from adaptiveattempt.class.php @see find_last_quest_used_by_attempt()
     * This function retrieves the last question that was used in the attempt
     * @return int question slot or 0 if no unmarked question could be found
     */
    protected function find_last_quest_used_by_attempt() {
        if (!$this->quba instanceof question_usage_by_activity) {
            $this->print_debug('find_last_quest_used_by_attempt() - Argument was not a question_usage_by_activity object');
            return 0;
        }

        // The last slot in the array should be the last question that was attempted (meaning it was either shown to the user or the
        // user submitted an answer to it).
        $questslots = $this->quba->get_slots();

        if (empty($questslots) || !is_array($questslots)) {
            $this->print_debug('find_last_quest_used_by_attempt() - No question slots found for this question_usage_by_activity '.
                'object');
            return 0;
        }

        $questslot = end($questslots);
        $this->print_debug('find_last_quest_used_by_attempt() - Found a question slot: '.$questslot);
        return $questslot;
    }

    /**
     * Refactored code from adaptiveattempt.class.php @see was_answer_submitted_to_question()
     * This function determines if the user submitted an answer to the question
     * @param int $slot question slot id
     * @return bool true if an answer to the question was submitted, otherwise false
     */
    protected function was_answer_submitted_to_question($slotid) {
        if (empty($slotid)) {
            $this->print_debug('was_answer_submitted_to_question() refactored - slot id was zero');
            return false;
        }

        $state = $this->quba->get_question_state($slotid);

        // Check if the state of the quesiton attempted was graded right, partially right or wrong.
        $marked = $state instanceof question_state_gradedright || $state instanceof question_state_gradedpartial
            || $state instanceof question_state_gradedwrong;
        if ($marked) {
            return true;
        } else {
            // Save some debugging information.
            $debugmsg = 'was_answer_submitted_to_question() refactored - question state is unrecognized state: '.get_class($state);
            $debugmsg .= ' questionslotid: '.$slotid.' quba id: '.$this->quba->get_id();
            $this->print_debug($debugmsg);
        }

        return false;
    }

    /**
     * This function determins whether the user answered the question correctly or incorrectly.
     * If the answer is partially correct it is seen as correct.
     * @param question_usage_by_activity $quba an object loaded using the unique id of the attempt
     * @param int $slotid the slot id of the question
     * @return float|null a float representing the user's mark.  Or null if there was no mark
     */
    public function get_question_mark($quba, $slotid) {
        $mark = $quba->get_question_mark($slotid);

        if (is_float($mark)) {
            return $mark;
        }

        $this->print_debug('get_question_mark() - Question mark was not a float slot id: '.$slotid);
        return null;
    }

    /**
     * This function retreives the mark received from the student's submission to the question
     * @return bool|null true if the question was marked correct. False if the question was marked incorrect or null if there is an
     *      error determining mark
     */
    public function question_was_marked_correct() {
        // Find the last question attempted by the user.
        $slotid = $this->find_last_quest_used_by_attempt();

        if (empty($slotid)) {
            return null;
        }

        // Check if the question was marked.
        if (!$this->was_answer_submitted_to_question($slotid)) {
            // If no answer was submitted then the question muast be marked as incorrect.  Increment questions attempted.
            $this->questattempted++;
            return false;
        }

        // Retrieve the mark received.
        $mark = $this->get_question_mark($this->quba, $slotid);

        if (is_null($mark)) {
            return null;
        }

        // Return true if the question was marked correct.
        if ((float) 0 < $mark) {
            // Increment questions attempted.
            $this->questattempted++;
            return true;
        }

        // Increment questions attempted.
        $this->questattempted++;
        return false;
    }

    /**
     * This function retrieves the allowed standard error (as a percent) for the attempt
     * @throws dml_missing_record_exception
     * @param int $attemptid adaptivequiz_attempt id
     * @return float the standard error allowed
     */
    public function retrieve_standard_error($attemptid) {
        global $DB;

        $param = array('aaid' => $attemptid);
        $sql = "SELECT a.standarderror
                  FROM {adaptivequiz} a
                  JOIN {adaptivequiz_attempt} aa ON a.id = aa.instance
                 WHERE aa.id = :aaid
              ORDER BY a.standarderror ASC";

        return (float) $DB->get_field_sql($sql, $param, MUST_EXIST);
    }

    /**
     * This function performs the different steps in the CAT simple algorithm
     * @return int returns the next difficulty level or 0 if there was an error
     */
    public function perform_calculation_steps() {
        // Retrieve attempt record.
        $record = $this->retrieve_attempt_record($this->attemptid);

        $this->difficultysum = $record->difficultysum;
        $this->questattempted = $record->questionsattempted;

        // If the user answered the previous question correctly, calculate the sum of correct answers.
        $correct = $this->question_was_marked_correct();

        if (true === $correct) {
            // Compute the next difficulty level for the next question.
            $this->nextdifficulty = $this->compute_next_difficulty($this->level, $this->questattempted, true, $record);
        } else if (false === $correct) {
            // Compute the next difficulty level for the next question.
            $this->nextdifficulty = $this->compute_next_difficulty($this->level, $this->questattempted, false, $record);
        } else {
            $this->status = get_string('errorlastattpquest', 'adaptivequiz');
            $this->print_debug('perform_calculation_steps() - Last question attempted returned a null as an answer');
            return 0;
        }

        // If he user hasn't met the minimum requirements to end the attempt, then return with the next difficulty level.
        if (empty($this->readytostop)) {
            $this->print_debug('perform_calculation_steps() - Not ready to stop the attempt, returning next difficulty number');
            return $this->nextdifficulty;
        }

        // Calculate the sum of correct answers and the sum of incorrect answers.
        $this->sumofcorrectanswers = $this->compute_right_answers($this->quba);
        $this->sumofincorrectanswers = $this->compute_wrong_answers($this->quba);

        if (0 == $this->questattempted) {
            $this->status = get_string('errornumattpzero', 'adaptivequiz');
            $this->print_debug('perform_calculation_steps() - number of questions attempted equals zero');
            return 0;
        }

        // Test that the sum of incorrect and correct answers equal to the sum of question attempted.
        $validatenumbers = $this->sumofcorrectanswers + $this->sumofincorrectanswers;

        if ($validatenumbers != $this->questattempted) {
            $this->status = get_string('errorsumrightwrong', 'adaptivequiz');
            $this->print_debug('perform_calculation_steps() - Sum of correct and incorrect answers ('.$validatenumbers.') '.
                    'doesn\'t equals the total number of questions attempted ('.$this->questattempted.')');
            return 0;
        }

        // Get the measure estimate.
        $this->measure = self::estimate_measure($this->difficultysum, $this->questattempted, $this->sumofcorrectanswers,
            $this->sumofincorrectanswers);

        // Get the standard error estimate.
        $this->standarderror = self::estimate_standard_error($this->questattempted, $this->sumofcorrectanswers,
            $this->sumofincorrectanswers);

        $this->print_debug('perform_calculation_steps() - difficultysum: '.$this->difficultysum.', questattempted: '.
                $this->questattempted.', sumofcorrectanswers: '.$this->sumofcorrectanswers.', sumofincorrectanswers: '.
                $this->sumofincorrectanswers.' =&gt; measure: '.$this->measure.', standard error: '.$this->standarderror);

        // Retrieve the standard error (as a percent) set for the attempt, convert it into a decimal percent then
        // convert to a logit.
        $quizdefinederror = $this->retrieve_standard_error($this->attemptid);
        $quizdefinederror = $quizdefinederror / 100;
        $quizdefinederror = self::convert_percent_to_logit($quizdefinederror);

        // If the calculated standard error is within the parameters of the attempt then populate the status message.
        if ($this->standard_error_within_parameters($this->standarderror, $quizdefinederror)) {
            // Convert logits to percent for display.
            $val = new stdClass();
            $val->calerror = self::convert_logit_to_percent($this->standarderror);
            $val->calerror = 100 * round($val->calerror, 2);
            $val->definederror = self::convert_logit_to_percent($quizdefinederror);
            $val->definederror = 100 * round($val->definederror, 2);
            $this->status = get_string('calcerrorwithinlimits', 'adaptivequiz', $val);
        }

        $this->print_debug('perform_calculation_steps() - measure: '.$this->measure.' standard error: '.$this->standarderror);

        return $this->nextdifficulty;
    }

    /**
     * This function returns the currently set status message
     * @return string the status message property
     */
    public function get_status() {
        return $this->status;
    }

    /**
     * This function takes a percent as a float between 0 and less than 0.5 and converts it into a logit value
     * @throws coding_exception if percent is out of bounds
     * @param float $percent percent represented as a decimal 15% = 0.15
     * @return float logit value of percent
     */
    public static function convert_percent_to_logit($percent) {
        if ($percent < 0 || $percent >= 0.5) {
            throw new coding_exception('convert_percent_to_logit: percent is out of bounds', 'Percent must be 0 >= and < 0.5');
        }
        return log( (0.5 + $percent) / (0.5 - $percent) );
    }

    /**
     * This function takes a logit as a float greater than or equal to 0 and converts it into a percent
     * @throws coding_exception if logit is out of bounds
     * @param float $logit logit value
     * @return float logit value of percent
     */
    public static function convert_logit_to_percent($logit) {
        if ($logit < 0) {
            throw new coding_exception('convert_logit_to_percent: logit is out of bounds',
                'logit must be greater than or equal to 0');
        }
        return ( 1 / ( 1 + exp(0 - $logit) ) ) - 0.5;
    }

    /**
     * Convert a logit value to a fraction between 0 and 1.
     * @param float $logit logit value
     * @return float the logit value mapped as a fraction
     */
    public static function convert_logit_to_fraction($logit) {
        return exp($logit) / ( 1 + exp($logit) );
    }

    /**
     * This function takes the inverse of a logit value, then maps the value onto the scale defined for the attempt
     * @param float $logit logit value
     * @param int $max the maximum value of the scale
     * @param int $min the minimum value of the scale
     * @return float the logit value mapped onto the scale
     */
    public static function map_logit_to_scale($logit, $max, $min) {
        $fraction = self::convert_logit_to_fraction($logit);
        $scaledvalue = ( ( $max - $min ) * $fraction ) + $min;
        return $scaledvalue;
    }

    /**
     * This function compares the calulated standard error with the activity defined standard error allowd for the attempt
     * @param float $calculatederror the error calculated from the parameters of the user's current attempt
     * @param float $definederror the allowed error set for the activity instance
     * @return bool true if the calulated error is less than or equal to the defined error, otherwise false
     */
    public function standard_error_within_parameters($calculatederror, $definederror) {
        if ($calculatederror <= $definederror) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * This function estimates the standard error in the measurement
     * @param int $questattempt the number of question attempted
     * @param int $sumcorrect the sum of correct answers
     * @param int $sumincorrect the sum of incorrect answers
     * @return float a decimal rounded to 5 places is returned
     */
    public static function estimate_standard_error($questattempt, $sumcorrect, $sumincorrect) {
        if ($sumincorrect == 0) {
            $standarderror = sqrt($questattempt / ( ($sumcorrect - 0.5) * ($sumincorrect + 0.5) ) );
        } else if ($sumcorrect == 0) {
            $standarderror = sqrt($questattempt / ( ($sumcorrect + 0.5) * ($sumincorrect - 0.5) ) );
        } else {
            $standarderror = sqrt($questattempt / ( $sumcorrect * $sumincorrect ) );
        }

        return round($standarderror, 5);
    }

    /**
     * This function estimates the measure of ability
     * @param float $diffsum the sum of difficulty levels expressed as logits
     * @param int $questattempt the number of question attempted
     * @param int $sumcorrect the sum of correct answers
     * @param int $sumincorrect the sum of incorrect answers
     * @return float an estimate of the measure of ability
     */
    public static function estimate_measure($diffsum, $questattempt, $sumcorrect, $sumincorrect) {
        if ($sumincorrect == 0) {
            $measure = ($diffsum / $questattempt) + log( ($sumcorrect - 0.5) / ($sumincorrect + 0.5) );
        } else if ($sumcorrect == 0) {
            $measure = ($diffsum / $questattempt) + log( ($sumcorrect + 0.5) / ($sumincorrect - 0.5) );
        } else {
            $measure = ($diffsum / $questattempt) + log( $sumcorrect / $sumincorrect );
        }
        return round($measure, 5, PHP_ROUND_HALF_UP);
    }

    /**
     * This function counts the total number of correct answers for the attempt
     * @param question_usage_by_activity $quba an object loaded using the unique id of the attempt
     * @return int the number of correct answer submission
     */
    public function compute_right_answers($quba) {
        $correctanswers = 0;

        // Get question slots for the attempt.
        $slots = $quba->get_slots();

        // Iterate over slots and count correct answers.
        foreach ($slots as $slot) {
            $mark = $this->get_question_mark($quba, $slot);

            if (!is_null($mark) && 0.0 < $mark) {
                $correctanswers++;
            }
        }

        $this->print_debug('compute_right_answers() - Sum of correct answers: '.$correctanswers);
        return $correctanswers;
    }

    /**
     * This function counts the total number of incorrect answers for the attempt
     * @param question_usage_by_activity $quba an object loaded using the unique id of the attempt
     * @return int the number of correct answer submission
     */
    public function compute_wrong_answers($quba) {
        $incorrectanswers = 0;

        // Get question slots for the attempt.
        $slots = $quba->get_slots();

        // Iterate over slots and count correct answers.
        foreach ($slots as $slot) {
            $mark = $this->get_question_mark($quba, $slot);

            if (is_null($mark) || 0.0 >= $mark) {
                $incorrectanswers++;
            }
        }

        $this->print_debug('compute_right_answers() - Sum of incorrect answers: '.$incorrectanswers);
        return $incorrectanswers;
    }

    /**
     * This function is a helper method to compute the current difficult level the attempt is at
     * @throws coding_exception if any of the parameters contain invalid data
     * @param question_usage_by_activity $quba a question usage by activity set to an attempt id
     * @param int $startinglevel the starting level of difficulty for the attempt
     * @param stdClass $attemptobj an object with the following properties: lowestlevel and highestlevel
     * @return int the current level of difficulty
     */
    public function get_current_diff_level($quba, $level, $attemptobj) {
        // Check if level is a positive integer.
        if (!is_int($level) || 0 >= $level) {
            throw new coding_exception('get_current_diff_level: Arg 2 needs to be a positive integer',
                'Invalid level of :'.$level.' was passed');
        }
        // Check if quba is a valid instance of question_usage_by_activity.
        if (!$quba instanceof question_usage_by_activity) {
            throw new coding_exception('get_current_diff_level: Arg 1 needs to be an instance of question_usage_by_activity',
                'Invalid quba of :'.get_class($quba));
        }
        // Check if attempt object has required properties defined.
        if (!isset($attemptobj->lowestlevel) || !isset($attemptobj->highestlevel)) {
            throw new coding_exception('get_current_diff_level: Arg 3 needs to have lowestlevel and highestlevel properties',
                'Invalid attemptobj of :'.$this->vardump($attemptobj));
        }
        // Check if attempt object has required property value types.
        $conditions = !is_int($attemptobj->lowestlevel) || 0 >= $attemptobj->lowestlevel || !is_int($attemptobj->highestlevel)
                || 0 >= $attemptobj->highestlevel || $attemptobj->lowestlevel >= $attemptobj->highestlevel;
        if ($conditions) {
            throw new coding_exception('get_current_diff_level: Arg 3 lowestlevel and highestlevel properties must be positive '.
                'integers', 'Invalid attemptobj of :'.$this->vardump($attemptobj));
        }

        return $this->return_current_diff_level($quba, $level, $attemptobj);
    }

    /**
     * This function calculates the currently difficulty level of the attempt.
     * @param question_usage_by_activity $quba a question usage by activity set to an attempt id
     * @param int $level the starting level of difficulty for the attempt
     * @param stdClass $attemptobj an object with the following properties: lowestlevel and highestlevel
     * @return int the current level of difficulty
     */
    protected function return_current_diff_level($quba, $level, $attemptobj) {
        $questattempted = 0;
        $correct = false;
        // Set current difficulty to the starting level.
        $currdiff = $level;

        // Get question slots for the attempt.
        $slots = $quba->get_slots();

        if (empty($slots)) {
            return 0;
        }

        // Get the last question's state.
        $state = $quba->get_question_state(end($slots));
        // If the state of the last question in the attempt is 'todo' remove it from the array, as the user never submitted their
        // answer.
        if ($state instanceof question_state_todo) {
            array_pop($slots);
        }

        // Reset the array pointer back to the beginning.
        reset($slots);

        // Iterate over slots and count correct answers.
        foreach ($slots as $slot) {
            $mark = $this->get_question_mark($quba, $slot);

            if (is_null($mark) || 0.0 >= $mark) {
                $correct = false;
            } else {
                $correct = true;
            }

            $questattempted++;
            $currdiff = $this->compute_next_difficulty($currdiff, $questattempted, $correct, $attemptobj);
        }

        return $currdiff;
    }

    /**
     * This function does the work to determine the next difficulty level
     * @param int $level the difficulty level of the last question attempted
     * @param int $questattempted the sum of questions attempted
     * @param bool $correct true of the user got the previous question correct, otherwise false
     * @param stdClass $attempt a data record returned from @see retrieve_attempt_record()
     * @return int the next difficult level
     */
    public function compute_next_difficulty($level, $questattempted, $correct, $attempt) {
        $nextdifficulty = 0;

        // Map the linear scale to a logrithmic logit scale.
        $ls = self::convert_linear_to_logit($level, $attempt->lowestlevel, $attempt->highestlevel);

        // Set the logit value of the previously attempted question's difficulty level.
        $this->levellogit = $ls;
        $this->difficultysum = $this->difficultysum + $this->levellogit;

        // Check if the last question was marked correctly.
        if ($correct) {
            $nextdifficulty = $ls + 2 / $questattempted;
        } else {
            $nextdifficulty = $ls - 2 / $questattempted;
        }

        // Calculate the inverse to translate the value into a difficulty level.
        $invps = 1 / ( 1 + exp( (-1 * $nextdifficulty) ) );
        $invps = round($invps, 2);
        $difflevel = $attempt->lowestlevel + ( $invps * ($attempt->highestlevel - $attempt->lowestlevel) );
        $difflevel = round($difflevel);

        $this->print_debug('compute_next_difficulty() - Next difficulty level is: '.$difflevel);
        return (int) $difflevel;
    }

    /**
     * Map an linear-scale difficulty/ability level to a logit scale
     *
     * @param int $level An integer level
     * @param int $min The lower bound of the scale
     * @param int $max The upper bound of the scale
     * @return float
     */
    public static function convert_linear_to_logit($level, $min, $max) {
        // Map the level on a linear percentage scale.
        $percent = ($level - $min) / ($max - $min);

        // We will use a limit that is 1/2th the granularity of the question levels as our base.
        // For example, for levels 1-100, we will use a base of 0.5% (5.3 logits),
        // for levels 1-1000 we will use a base of 0.05% (7.6 logits).
        //
        // Note that the choice of 1/2 the granularity is somewhat arbitrary.
        // The floor value for the ends of the scale is being chosen so that answers
        // at the end of the scale do not excessively weight the ability measure
        // in ways that are not recoverable by subsequent answers.
        //
        // For example, lets say that on a scale of 1-10, a user of level 5 makes
        // a dumb mistake and answers two level 1 questions wrong, but then continues
        // the test and answers 20 more questions with every question up to level 5
        // right and those above wrong. The test should likely score the user somewhere
        // a bit below 5 with 5 being included in the Standard Error.
        //
        // Several test runs with different floors showed that 1/1000 gave far too
        // much weight to answers at the edge of the scale. 1/10 did ok, but
        // 1/2 seemed to allow recovery from spurrious answers at the edges while
        // still allowing consistent answers at the edges to trend the ability measure to
        // the top/bottom level.
        $granularity = 1 / ($max - $min);
        $percentfloor = $granularity / 2;

        // Avoid a division by zero error.
        if ($percent == 1) {
            $percent = 1 - $percentfloor;
        }

        // Map the percentage scale to a logrithmic logit scale.
        $logit = log( $percent / (1 - $percent) );

        // Check if result is inifinite.
        if (is_infinite($logit)) {
            $logitfloor = log( $percentfloor / (1 - $percentfloor) );
            if ($logit > 0) {
                return -1 * $logitfloor;
            } else {
                return $logitfloor;
            }
        }
        return $logit;
    }
}
