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
 * This class contains information about the attempt parameters
 *
 * @copyright  2013 onwards Remote-Learner {@link http://www.remote-learner.ca/}
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\local;

use coding_exception;
use dml_exception;
use mod_adaptivequiz\local\attempt\attempt_state;
use moodle_exception;
use question_bank;
use question_engine;
use question_state_gaveup;
use question_state_gradedpartial;
use question_state_gradedright;
use question_state_gradedwrong;
use question_usage_by_activity;
use stdClass;

class attempt {

    private const TABLE = 'adaptivequiz_attempt';

    /**
     * The name of the module
     */
    const MODULENAME = 'mod_adaptivequiz';

    /**
     * The behaviour to use be default
     */
    const ATTEMPTBEHAVIOUR = 'deferredfeedback';

    /**
     * @var attempt_state $attemptstate
     */
    private $attemptstate;

    /**
     * Flag to denote developer debugging is enabled and this class should write message to the debug
     * wrap on multiple lines
     * @var bool
     */
    protected $debugenabled = false;

    /** @var array $debug debugging array of messages */
    protected $debug = array();

    /**
     * @var stdClass $adaptivequiz object, properties come from the adaptivequiz table.
     * This property also contains the context and cm objects
     */
    protected $adaptivequiz;

    /** @var stdClass $adpqattempt object, properties come from the adaptivequiz_attempt table */
    protected $adpqattempt;

    /** @var int $userid user id */
    protected $userid;

    /** @var int $uniqueid a unique number identifying the activity usage of questions */
    protected $uniqueid;

    /** @var int $questionsattempted the total of question attempted */
    protected $questionsattempted;

    /** @var float $standarderror the standard error of the attempt  */
    protected $standarderror;

    /** @var question_usage_by_activity $quba - A question usage by activity object */
    protected $quba = null;

    /** @var int $slot - a question slot number */
    protected $slot = 0;

    /** @var array $tags an array of tags that used to identify eligible questions for the attempt */
    protected $tags = array();

    /** @var array $status status message storing the reason why the attempt was stopped */
    protected $status = '';

    /** @var int $level the difficulty level the attempt is currently set at */
    protected $level = 0;

    /** @var int $lastdifficultylevel the last difficulty level used in the attempt if any */
    protected $lastdifficultylevel = null;

    /**
     * Constructor initializes required data to process the attempt
     * @param stdClass $adaptivequiz adaptivequiz record object from adaptivequiz table
     * @param int $userid user id
     * @param array $tags an array of acceptible tags
     */
    public function __construct($adaptivequiz, $userid, $tags = array()) {
        $this->adaptivequiz = $adaptivequiz;
        $this->userid = $userid;
        $this->tags = $tags;
        $this->tags[] = ADAPTIVEQUIZ_QUESTION_TAG;

        if (debugging('', DEBUG_DEVELOPER)) {
            $this->debugenabled = true;
        }
    }

    /**
     * This function returns the debug array
     * @return array array of debugging messages
     */
    public function get_debug() {
        return $this->debug;
    }

    /**
     * This function returns the adaptivequiz property
     * @return stdClass adaptivequiz record
     */
    public function get_adaptivequiz() {
        return $this->adaptivequiz;
    }

    /**
     * This function returns the $level property
     * @return int level property
     */
    public function get_level() {
        return $this->level;
    }

    /**
     * This function sets the $level property
     * @param int $level difficulty level to fetch
     */
    public function set_level($level) {
        $this->level = $level;
    }

    /**
     * Set the last difficulty level that was used.
     * This may influence the next question chosing process.
     *
     * @param int $lastdifficultylevel
     * @return void
     */
    public function set_last_difficulty_level($lastdifficultylevel) {
        if (is_null($lastdifficultylevel)) {
            $this->lastdifficultylevel = null;
        } else {
            $this->lastdifficultylevel = (int) $lastdifficultylevel;
        }
    }

    /**
     * This function returns the current slot number set for the attempt
     * @return int question slot number
     */
    public function get_question_slot_number() {
        return $this->slot;
    }

    /**
     * This function sets the current slot number set for the attempt
     * @throws coding_exception - exception is thrown the argument is not a positive integer
     * @param int $slot slot number
     */
    public function set_question_slot_number($slot) {
        if (!is_int($slot) || 0 >= $slot) {
            throw new coding_exception('adaptiveattempt: Argument 1 is not an positive integer', 'Slot must be a positive integer');
        }

        $this->slot = $slot;
    }

    /**
     * This function returns the current question usage by activity object
     * @return question_usage_by_activity a question usage by activity object loaded with the attempt unique id
     */
    public function get_quba() {
        return $this->quba;
    }

    /**
     * This function sets the current question usage by activity object.
     * @throws coding_exception - exception is thrown argument is not an instance of question_usage_by_activity class
     * @param question_usage_by_activity $quba an object loaded with the unique id of the attempt
     */
    public function set_quba($quba) {
        if (!$quba instanceof question_usage_by_activity) {
            throw new coding_exception('adaptiveattempt: Argument 1 is not a question_usage_by_activity object',
                    'Question usage by activity must be an  instance of question_usage_by_activity');
        }

        $this->quba = $quba;
    }

    /**
     * This function checks to see if the difficulty level is out of the boundries set for the attempt
     * @param int $level the difficulty level requested
     * @param stdClass $adaptivequiz an adaptivequiz record
     * @return bool true if the level is in bounds, otherwise false
     */
    public function level_in_bounds($level, $adaptivequiz) {
        if ($adaptivequiz->lowestlevel <= $level && $adaptivequiz->highestlevel >= $level) {
            return true;
        }

        return false;
    }

    /**
     * This function returns the currently set status message.
     *
     * @return string The status message property.
     */
    public function get_status() {
        return $this->status;
    }

    /**
     * This function does the work of initializing data required to fetch a new question for the attempt.
     *
     * @return bool True if attempt started okay otherwise false.
     */
    public function start_attempt() {
        // Get most recent attempt or start a new one.
        $adpqattempt = $this->get_attempt();

        // Check if the level requested is out of the minimum/maximum boundries for the attempt.
        if (!$this->level_in_bounds($this->level, $this->adaptivequiz)) {
            $var = new stdClass();
            $var->level = $this->level;
            $this->status = get_string('leveloutofbounds', 'adaptivequiz', $var);
            return false;
        }

        // Check if the attempt has reached the maximum number of questions attempted.
        if ($this->max_questions_answered()) {
            $this->status = get_string('maxquestattempted', 'adaptivequiz');
            return false;
        }

        // Initialize the question usage by activity property.
        $this->initialize_quba();
        // Find the last question viewed/answered by the user.
        $this->slot = $this->find_last_quest_used_by_attempt($this->quba);
        // Create a an instance of the fetchquestion class.
        $fetchquestion = new fetchquestion($this->adaptivequiz, 1, $this->adaptivequiz->lowestlevel,
                $this->adaptivequiz->highestlevel);

        // Check if this is the beginning of an attempt (and pass the starting level) or the continuation of an attempt.
        if (empty($this->slot) && 0 == $adpqattempt->questionsattempted) {
            // Set the starting difficulty level.
            $fetchquestion->set_level((int) $this->adaptivequiz->startinglevel);
            // Sets the level class property.
            $this->level = $this->adaptivequiz->startinglevel;
            // Set the rebuild flag for fetchquestion class.
            $fetchquestion->rebuild = true;

            $this->print_debug("start_attempt() - Brand new attempt.  Set starting level: {$this->adaptivequiz->startinglevel}.");

        } else if (!empty($this->slot) && $this->was_answer_submitted_to_question($this->quba, $this->slot)) {
            // If the attempt already has a question attached to it, check if an answer was submitted to the question.
            // If so fetch a new question.

            // Provide the question-fetching process with limits based on our last question.
            // If the last question was correct...
            if ($this->quba->get_question_mark($this->slot) > 0) {
                // Only ask questions harder than the last question unless we are already at the top of the ability scale.
                if (!is_null($this->lastdifficultylevel) && $this->lastdifficultylevel < $this->adaptivequiz->highestlevel) {
                    $fetchquestion->set_minimum_level($this->lastdifficultylevel + 1);
                    // Do not ask a question of the same level unless we are already at the max.
                    if ($this->lastdifficultylevel == $this->level) {
                        $this->print_debug("start_attempt() - Last difficulty is the same as the new difficulty, ".
                                "incrementing level from {$this->level} to ".($this->level + 1).".");
                        $this->level++;
                    }
                }
            } else {
                // If the last question was wrong...
                // Only ask questions easier than the last question unless we are already at the bottom of the ability scale.
                if (!is_null($this->lastdifficultylevel) && $this->lastdifficultylevel > $this->adaptivequiz->lowestlevel) {
                    $fetchquestion->set_maximum_level($this->lastdifficultylevel - 1);
                    // Do not ask a question of the same level unless we are already at the min.
                    if ($this->lastdifficultylevel == $this->level) {
                        $this->print_debug("start_attempt() - Last difficulty is the same as the new difficulty, ".
                                "decrementing level from {$this->level} to ".($this->level - 1).".");
                        $this->level--;
                    }
                }
            }

            // Reset the slot number back to zero, since we are going to fetch a new question.
            $this->slot = 0;
            // Set the level of difficulty to fetch.
            $fetchquestion->set_level((int) $this->level);

            $this->print_debug("start_attempt() - Continuing attempt.  Set level: {$this->level}.");

        } else if (empty($this->slot) && 0 < $adpqattempt->questionsattempted) {
            // If this condition is met, then something went wrong because the slot id is empty BUT the questions attempted is
            // Greater than zero.  Stop attempt.
            $this->print_debug('start_attempt() - something went horribly wrong since the quba has no slot number AND the number '.
                    'of question answered is greater than 0');
            $this->status = get_string('errorattemptstate', 'adaptivequiz');
            return false;
        }

        // If the slot property is set, then we have a question that is ready to be attempted.  No more process is required.
        if (!empty($this->slot)) {
            return true;
        }

        // If we are here, then the slot property was unset and a new question needs to prepared for display.
        $status = $this->get_question_ready($fetchquestion);

        if (empty($status)) {
            $var = new stdClass();
            $var->level = $this->level;
            $this->status = get_string('errorfetchingquest', 'adaptivequiz', $var);
            return false;
        }

        return $status;
    }

    /**
     * This function returns a random array element
     * @param array $questions an array of question ids.  Array key values are question ids
     * @return int a question id
     */
    public function return_random_question($questions) {
        if (empty($questions)) {
            return 0;
        }

        $questionid = array_rand($questions);
        $this->print_debug('return_random_question() - random question chosen questionid: '.$questionid);

        return (int) $questionid;
    }

    /**
     * This function checks to see if the student answered the maximum number of questions
     * @return bool true if the attempt is starting for the first time. Otherwise false
     */
    public function max_questions_answered() {
        if ($this->adpqattempt->questionsattempted >= $this->adaptivequiz->maximumquestions) {
            $this->print_debug('max_questions_answered() - maximum number of questions answered');
            return true;
        }

        return false;
    }

    /**
     * This function checks to see if the student answered the minimum number of questions
     * @return bool true if the attempt is starting for the first time. Otherwise false
     */
    public function min_questions_answered() {
        if ($this->adpqattempt->questionsattempted > $this->adaptivequiz->minimumquestions) {
            $this->print_debug('min_questions_answered() - minimum number of questions answered');
            return true;
        }

        return false;
    }

    /**
     * This function retrieves the last question that was used in the attempt
     * @throws moodle_exception - exception is thrown function parameter is not an instance of question_usage_by_activity class
     * @param question_usage_by_activity $quba an object loaded with the unique id of the attempt
     * @return int question slot or 0 if no unmarked question could be found
     */
    public function find_last_quest_used_by_attempt($quba) {
        if (!$quba instanceof question_usage_by_activity) {
            throw new coding_exception('find_last_quest_used_by_attempt() - Argument was not a question_usage_by_activity object',
                $this->vardump($quba));
        }

        // The last slot in the array should be the last question that was attempted (meaning it was either shown to the user
        // or the user submitted an answer to it).
        $questslots = $quba->get_slots();

        if (empty($questslots) || !is_array($questslots)) {
            $this->print_debug('find_last_quest_used_by_attempt() - No question slots found for this '.
                'question_usage_by_activity object');
            return 0;
        }

        $questslot = end($questslots);
        $this->print_debug('find_last_quest_used_by_attempt() - Found a question slot: '.$questslot);

        return $questslot;
    }

    /**
     * This function determines if the user submitted an answer to the question
     * @param question_usage_by_activity $quba an object loaded with the unique id of the attempt
     * @param int $slot question slot id
     * @return bool true if an answer to the question was submitted, otherwise false
     */
    public function was_answer_submitted_to_question($quba, $slotid) {
        $state = $quba->get_question_state($slotid);

        // Check if the state of the quesiton attempted was graded right, partially right, wrong or gave up, count the question has
        // having an answer submitted.
        $marked = $state instanceof question_state_gradedright || $state instanceof question_state_gradedpartial
            || $state instanceof question_state_gradedwrong || $state instanceof question_state_gaveup;

        if ($marked) {
            return true;
        } else {
            // Save some debugging information.
            $this->print_debug('was_answer_submitted_to_question() - question state is unrecognized state: '.get_class($state).'
                    question slotid: '.$slotid.' quba id: '.$quba->get_id());
        }

        return false;
    }

    /**
     * This function initializes the question_usage_by_activity object.  If an attempt unfinished attempt
     * has a usage id, a question_usage_by_activity object will be loaded using the usage id.  Otherwise a new
     * question_usage_by_activity object is created.
     *
     * @throws moodle_exception Exception is thrown when required behaviour could not be found.
     * @return question_usage_by_activity|null Returns a question usage by activity object or null.
     */
    public function initialize_quba() {
        if (!$this->behaviour_exists()) {
            throw new moodle_exception('Missing '.self::ATTEMPTBEHAVIOUR.' behaviour', 'Behaviour: '.self::ATTEMPTBEHAVIOUR.
                ' must exist in order to use this activity');
        }

        if (0 == $this->adpqattempt->uniqueid) {
            // Init question usage and set default behaviour of usage.
            $quba = question_engine::make_questions_usage_by_activity(self::MODULENAME, $this->adaptivequiz->context);
            $quba->set_preferred_behaviour(self::ATTEMPTBEHAVIOUR);

            $this->quba = $quba;
            $this->print_debug('initialized_quba() - question usage created');
        } else {
            // Load a previously used question by usage object.
            $quba = question_engine::load_questions_usage_by_activity($this->adpqattempt->uniqueid);
            $this->print_debug('initialized_quba() - Re-using unfinishd attempt');
        }

        // Set class property.
        $this->quba = $quba;

        return $quba;
    }

    /**
     * This function retrieves the most recent attempt, whose state is 'inprogress'. If no attempt is found
     * it creates a new attempt.  Lastly $adpqattempt instance property gets set.
     *
     * @return stdClass adaptivequiz_attempt data object
     */
    public function get_attempt() {
        global $DB;

        $param = ['instance' => $this->adaptivequiz->id, 'userid' => $this->userid, 'attemptstate' => attempt_state::IN_PROGRESS];
        $attempt = $DB->get_records(self::TABLE, $param, 'timemodified DESC', '*', 0, 1);

        if (empty($attempt)) {
            $time = time();
            $attempt = new stdClass();
            $attempt->instance = $this->adaptivequiz->id;
            $attempt->userid = $this->userid;
            $attempt->uniqueid = 0;
            $attempt->attemptstate = attempt_state::IN_PROGRESS;
            $attempt->questionsattempted = 0;
            $attempt->standarderror = 999;
            $attempt->timecreated = $time;
            $attempt->timemodified = $time;

            $id = $DB->insert_record(self::TABLE, $attempt);

            $attempt->id = $id;
            $this->adpqattempt = $attempt;

            $this->print_debug('get_attempt() - new attempt created: '.$this->vardump($attempt));
        } else {
            $attempt = current($attempt);
            $this->adpqattempt = $attempt;

            $this->print_debug('get_attempt() - previous attempt loaded: '.$this->vardump($attempt));
        }

        return $attempt;
    }

    /**
     * This function determins whether the user answered the question correctly or incorrectly.
     * If the answer is partially correct it is seen as correct.
     * @param question_usage_by_activity $quba an object loaded with the unique id of the attempt
     * @param int $slotid the slot id of the question
     * @return float a float representing the user's mark.  Or null if there was no mark
     */
    public function get_question_mark($quba, $slotid) {
        $mark = $quba->get_question_mark($slotid);

        if (is_float($mark)) {
            return $mark;
        }

        $this->print_debug('get_question_mark() - Question mark was not a float slot id: '.$slotid.'.  Returning zero');

        return 0;
    }

    /**
     * This functions returns an array of all question ids that have been used in this attempt
     *
     * @return array an array of question ids
     */
    public function get_all_questions_in_attempt($uniqueid) {
        global $DB;

        $questions = $DB->get_records_menu('question_attempts', array('questionusageid' => $uniqueid), 'id ASC', 'id,questionid');

        return $questions;
    }

    /**
     * @throws dml_exception
     */
    public static function user_has_completed_on_quiz(int $adaptivequizid, int $userid): bool {
        global $DB;

        return $DB->record_exists(self::TABLE,
            ['userid' => $userid, 'instance' => $adaptivequizid, 'attemptstate' => attempt_state::COMPLETED]);
    }

    /**
     * This function adds a message to the debugging array
     * @param string $message details of the debugging message
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
     * This function gets the question ready for display to the user.
     * @param fetchquestion $fetchquestion a fetchquestion object initialized to the activity instance of the attempt
     * @return bool true if everything went okay, otherwise false
     */
    protected function get_question_ready($fetchquestion) {
        // Fetch questions already attempted.
        $exclude = $this->get_all_questions_in_attempt($this->adpqattempt->uniqueid);
        // Fetch questions for display.
        $questionids = $fetchquestion->fetch_questions($exclude);

        if (empty($questionids)) {
            $this->print_debug('get_question_ready() - Unable to fetch a question $questionsids:'.$this->vardump($questionids));

            return false;
        }
        // Select one random question.
        $questiontodisplay = $this->return_random_question($questionids);

        if (empty($questiontodisplay)) {
            $this->print_debug('get_question_ready() - Unable to randomly select a question $questionstodisplay:'.
                    $questiontodisplay);

            return false;
        }

        // Load basic question data.
        $questionobj = question_preload_questions(array($questiontodisplay));
        get_question_options($questionobj);
        $this->print_debug('get_question_ready() - setup question options');

        // Make a copy of the array and pop off the first (and only) element (current() didn't work for some reason).
        $quest = $questionobj;
        $quest = array_pop($quest);

        // Create the question_definition object.
        $question = question_bank::load_question($quest->id);
        // Add the question to the usage question_usable_by_activity object.
        $this->slot = $this->quba->add_question($question);
        // Start the question attempt.
        $this->quba->start_question($this->slot);
        // Save the question usage and question attempt state to the DB.
        question_engine::save_questions_usage_by_activity($this->quba);
        // Update the attempt unique id.
        $this->set_attempt_uniqueid();

        // Set class level property to the difficulty level of the question returned from fetchquestion class.
        $this->level = $fetchquestion->get_level();
        $this->print_debug('get_question_ready() - Question: '.$this->vardump($question).' loaded and attempt started. '.
                'Question_usage_by_activity saved.');

        return true;
    }

    /**
     * This function updates the current attempt with the question_usage_by_activity id.
     */
    protected function set_attempt_uniqueid(): void {
        global $DB;

        $this->adpqattempt->uniqueid = $this->quba->get_id();
        $DB->update_record(self::TABLE, $this->adpqattempt);

        $this->print_debug('set_attempt_uniqueid() - attempt uniqueid set: '.$this->adpqattempt->uniqueid);
    }

    /**
     * This function retrives archetypal behaviours and sets the attempt behavour to to manual grade
     * @return bool true if the behaviour exists, else false
     */
    protected function behaviour_exists() {
        $exists = false;
        $behaviours = question_engine::get_archetypal_behaviours();

        if (!empty($behaviours)) {
            foreach ($behaviours as $key => $behaviour) {
                if (0 == strcmp(self::ATTEMPTBEHAVIOUR, $key)) {
                    // Behaviour found, exit the loop.
                    $exists = true;
                    break;
                }
            }
        }

        return $exists;
    }
}
