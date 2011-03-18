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
 * This defines the core classes of the Moodle question engine.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/states.php');
require_once(dirname(__FILE__) . '/datalib.php');
require_once(dirname(__FILE__) . '/renderer.php');
require_once(dirname(__FILE__) . '/bank.php');
require_once(dirname(__FILE__) . '/../type/questiontype.php');
require_once(dirname(__FILE__) . '/../type/questionbase.php');
require_once(dirname(__FILE__) . '/../type/rendererbase.php');
require_once(dirname(__FILE__) . '/../behaviour/behaviourbase.php');
require_once(dirname(__FILE__) . '/../behaviour/rendererbase.php');
require_once($CFG->libdir . '/questionlib.php');


/**
 * This static class provides access to the other question engine classes.
 *
 * It provides functions for managing question behaviours), and for
 * creating, loading, saving and deleting {@link question_usage_by_activity}s,
 * which is the main class that is used by other code that wants to use questions.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class question_engine {
    /** @var array behaviour name => 1. Records which behaviours have been loaded. */
    private static $loadedbehaviours = array();

    /**
     * Create a new {@link question_usage_by_activity}. The usage is
     * created in memory. If you want it to persist, you will need to call
     * {@link save_questions_usage_by_activity()}.
     *
     * @param string $component the plugin creating this attempt. For example mod_quiz.
     * @param object $context the context this usage belongs to.
     * @return question_usage_by_activity the newly created object.
     */
    public static function make_questions_usage_by_activity($component, $context) {
        return new question_usage_by_activity($component, $context);
    }

    /**
     * Load a {@link question_usage_by_activity} from the database, based on its id.
     * @param int $qubaid the id of the usage to load.
     * @return question_usage_by_activity loaded from the database.
     */
    public static function load_questions_usage_by_activity($qubaid) {
        $dm = new question_engine_data_mapper();
        return $dm->load_questions_usage_by_activity($qubaid);
    }

    /**
     * Save a {@link question_usage_by_activity} to the database. This works either
     * if the usage was newly created by {@link make_questions_usage_by_activity()}
     * or loaded from the database using {@link load_questions_usage_by_activity()}
     * @param question_usage_by_activity the usage to save.
     */
    public static function save_questions_usage_by_activity(question_usage_by_activity $quba) {
        $dm = new question_engine_data_mapper();
        $observer = $quba->get_observer();
        if ($observer instanceof question_engine_unit_of_work) {
            $observer->save($dm);
        } else {
            $dm->insert_questions_usage_by_activity($quba);
        }
    }

    /**
     * Delete a {@link question_usage_by_activity} from the database, based on its id.
     * @param int $qubaid the id of the usage to delete.
     */
    public static function delete_questions_usage_by_activity($qubaid) {
        global $CFG;
        self::delete_questions_usage_by_activities(new qubaid_list(array($qubaid)));
    }

    /**
     * Delete {@link question_usage_by_activity}s from the database.
     * @param qubaid_condition $qubaids identifies which questions usages to delete.
     */
    public static function delete_questions_usage_by_activities(qubaid_condition $qubaids) {
        $dm = new question_engine_data_mapper();
        $dm->delete_questions_usage_by_activities($qubaids);
    }

    /**
     * Change the maxmark for the question_attempt with number in usage $slot
     * for all the specified question_attempts.
     * @param qubaid_condition $qubaids Selects which usages are updated.
     * @param int $slot the number is usage to affect.
     * @param number $newmaxmark the new max mark to set.
     */
    public static function set_max_mark_in_attempts(qubaid_condition $qubaids,
            $slot, $newmaxmark) {
        $dm = new question_engine_data_mapper();
        $dm->set_max_mark_in_attempts($qubaids, $slot, $newmaxmark);
    }

    /**
     * @param array $questionids of question ids.
     * @param qubaid_condition $qubaids ids of the usages to consider.
     * @return boolean whether any of these questions are being used by any of
     *      those usages.
     */
    public static function questions_in_use(array $questionids, qubaid_condition $qubaids = null) {
        if (is_null($qubaids)) {
            return false;
        }
        $dm = new question_engine_data_mapper();
        return $dm->questions_in_use($questionids, $qubaids);
    }

    /**
     * Create an archetypal behaviour for a particular question attempt.
     * Used by {@link question_definition::make_behaviour()}.
     *
     * @param string $preferredbehaviour the type of model required.
     * @param question_attempt $qa the question attempt the model will process.
     * @return question_behaviour an instance of appropriate behaviour class.
     */
    public static function make_archetypal_behaviour($preferredbehaviour, question_attempt $qa) {
        question_engine::load_behaviour_class($preferredbehaviour);
        $class = 'qbehaviour_' . $preferredbehaviour;
        if (!constant($class . '::IS_ARCHETYPAL')) {
            throw new coding_exception('The requested behaviour is not actually an archetypal one.');
        }
        return new $class($qa, $preferredbehaviour);
    }

    /**
     * @param string $behaviour the name of a behaviour.
     * @return array of {@link question_display_options} field names, that are
     * not relevant to this behaviour before a 'finish' action.
     */
    public static function get_behaviour_unused_display_options($behaviour) {
        self::load_behaviour_class($behaviour);
        $class = 'qbehaviour_' . $behaviour;
        if (!method_exists($class, 'get_unused_display_options')) {
            return question_behaviour::get_unused_display_options();
        }
        return call_user_func(array($class, 'get_unused_display_options'));
    }

    /**
     * Create an behaviour for a particular type. If that type cannot be
     * found, return an instance of qbehaviour_missing.
     *
     * Normally you should use {@link make_archetypal_behaviour()}, or
     * call the constructor of a particular model class directly. This method
     * is only intended for use by {@link question_attempt::load_from_records()}.
     *
     * @param string $behaviour the type of model to create.
     * @param question_attempt $qa the question attempt the model will process.
     * @param string $preferredbehaviour the preferred behaviour for the containing usage.
     * @return question_behaviour an instance of appropriate behaviour class.
     */
    public static function make_behaviour($behaviour, question_attempt $qa, $preferredbehaviour) {
        try {
            self::load_behaviour_class($behaviour);
        } catch (Exception $e) {
            question_engine::load_behaviour_class('missing');
            return new qbehaviour_missing($qa, $preferredbehaviour);
        }
        $class = 'qbehaviour_' . $behaviour;
        return new $class($qa, $preferredbehaviour);
    }

    /**
     * Load the behaviour class(es) belonging to a particular model. That is,
     * include_once('/question/behaviour/' . $behaviour . '/behaviour.php'), with a bit
     * of checking.
     * @param string $qtypename the question type name. For example 'multichoice' or 'shortanswer'.
     */
    public static function load_behaviour_class($behaviour) {
        global $CFG;
        if (isset(self::$loadedbehaviours[$behaviour])) {
            return;
        }
        $file = $CFG->dirroot . '/question/behaviour/' . $behaviour . '/behaviour.php';
        if (!is_readable($file)) {
            throw new coding_exception('Unknown question behaviour ' . $behaviour);
        }
        include_once($file);
        self::$loadedbehaviours[$behaviour] = 1;
    }

    /**
     * Return an array where the keys are the internal names of the archetypal
     * behaviours, and the values are a human-readable name. An
     * archetypal behaviour is one that is suitable to pass the name of to
     * {@link question_usage_by_activity::set_preferred_behaviour()}.
     *
     * @return array model name => lang string for this behaviour name.
     */
    public static function get_archetypal_behaviours() {
        $archetypes = array();
        $behaviours = get_list_of_plugins('question/behaviour');
        foreach ($behaviours as $path) {
            $behaviour = basename($path);
            self::load_behaviour_class($behaviour);
            $plugin = 'qbehaviour_' . $behaviour;
            if (constant($plugin . '::IS_ARCHETYPAL')) {
                $archetypes[$behaviour] = self::get_behaviour_name($behaviour);
            }
        }
        asort($archetypes, SORT_LOCALE_STRING);
        return $archetypes;
    }

    /**
     * Return an array where the keys are the internal names of the behaviours
     * in preferred order and the values are a human-readable name.
     *
     * @param array $archetypes, array of behaviours
     * @param string $questionbehavioursorder, a comma separated list of behaviour names
     * @param string $questionbehavioursdisabled, a comma separated list of behaviour names
     * @param string $currentbahaviour, current behaviour name
     * @return array model name => lang string for this behaviour name.
     */
    public static function sort_behaviours($archetypes, $questionbehavioursorder,
            $questionbehavioursdisabled, $currentbahaviour) {
        $behaviourorder = array();
        $behaviourdisabled = array();

        // Get disabled behaviours
        if ($questionbehavioursdisabled) {
            $behaviourdisabledtemp = preg_split('/[\s,;]+/', $questionbehavioursdisabled);
        } else {
            $behaviourdisabledtemp = array();
        }

        if ($questionbehavioursorder) {
            $behaviourordertemp = preg_split('/[\s,;]+/', $questionbehavioursorder);
        } else {
            $behaviourordertemp = array();
        }

        foreach ($behaviourdisabledtemp as $key) {
            if (array_key_exists($key, $archetypes)) {
                // Do not disable the current behaviour
                if ($key != $currentbahaviour) {
                    $behaviourdisabled[$key] = $archetypes[$key];
                }
            }
        }

        // Get behaviours in preferred order
        foreach ($behaviourordertemp as $key) {
            if (array_key_exists($key, $archetypes)) {
                $behaviourorder[$key] = $archetypes[$key];
            }
        }
        // Get the rest of behaviours and sort them alphabetically
        $leftover = array_diff_key($archetypes, $behaviourdisabled, $behaviourorder);
        asort($leftover, SORT_LOCALE_STRING);

        // Set up the final order to be displayed
        $finalorder = $behaviourorder + $leftover;
        return $finalorder;
    }

    /**
     * Return an array where the keys are the internal names of the behaviours
     * in preferred order and the values are a human-readable name.
     *
     * @param string $currentbahaviour
     * @return array model name => lang string for this behaviour name.
     */
    public static function get_behaviour_options($currentbahaviour) {
        global $CFG;
        $archetypes = self::get_archetypal_behaviours();

        // If no admin setting return all behavious
        if (empty($CFG->questionbehavioursdisabled) && empty($CFG->questionbehavioursorder)) {
            return $archetypes;
        }

        return self::sort_behaviours($archetypes, $CFG->questionbehavioursorder,
                $CFG->questionbehavioursdisabled, $currentbahaviour);
    }

    /**
     * Get the translated name of an behaviour, for display in the UI.
     * @param string $behaviour the internal name of the model.
     * @return string name from the current language pack.
     */
    public static function get_behaviour_name($behaviour) {
        return get_string('pluginname', 'qbehaviour_' . $behaviour);
    }

    /**
     * Returns the valid choices for the number of decimal places for showing
     * question marks. For use in the user interface.
     * @return array suitable for passing to {@link choose_from_menu()} or similar.
     */
    public static function get_dp_options() {
        return question_display_options::get_dp_options();
    }

    /**
     * Initialise the JavaScript required on pages where questions will be displayed.
     */
    public static function initialise_js() {
        return question_flags::initialise_js();
    }
}


/**
 * This class contains all the options that controls how a question is displayed.
 *
 * Normally, what will happen is that the calling code will set up some display
 * options to indicate what sort of question display it wants, and then before the
 * question is rendered, the behaviour will be given a chance to modify the
 * display options, so that, for example, A question that is finished will only
 * be shown read-only, and a question that has not been submitted will not have
 * any sort of feedback displayed.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_display_options {
    /**#@+ @var integer named constants for the values that most of the options take. */
    const HIDDEN = 0;
    const VISIBLE = 1;
    const EDITABLE = 2;
    /**#@-*/

    /**#@+ @var integer named constants for the {@link $marks} option. */
    const MAX_ONLY = 1;
    const MARK_AND_MAX = 2;
    /**#@-*/

    /**
     * @var integer maximum value for the {@link $markpd} option. This is
     * effectively set by the database structure, which uses NUMBER(12,7) columns
     * for question marks/fractions.
     */
    const MAX_DP = 7;

    /**
     * @var boolean whether the question should be displayed as a read-only review,
     * or in an active state where you can change the answer.
     */
    public $readonly = false;

    /**
     * @var boolean whether the question type should output hidden form fields
     * to reset any incorrect parts of the resonse to blank.
     */
    public $clearwrong = false;

    /**
     * Should the student have what they got right and wrong clearly indicated.
     * This includes the green/red hilighting of the bits of their response,
     * whether the one-line summary of the current state of the question says
     * correct/incorrect or just answered.
     * @var integer {@link question_display_options::HIDDEN} or
     * {@link question_display_options::VISIBLE}
     */
    public $correctness = self::VISIBLE;

    /**
     * The the mark and/or the maximum available mark for this question be visible?
     * @var integer {@link question_display_options::HIDDEN},
     * {@link question_display_options::MAX_ONLY} or {@link question_display_options::MARK_AND_MAX}
     */
    public $marks = self::MARK_AND_MAX;

    /** @var number of decimal places to use when formatting marks for output. */
    public $markdp = 2;

    /**
     * Should the flag this question UI element be visible, and if so, should the
     * flag state be changable?
     * @var integer {@link question_display_options::HIDDEN},
     * {@link question_display_options::VISIBLE} or {@link question_display_options::EDITABLE}
     */
    public $flags = self::VISIBLE;

    /**
     * Should the specific feedback be visible.
     * @var integer {@link question_display_options::HIDDEN} or
     * {@link question_display_options::VISIBLE}
     */
    public $feedback = self::VISIBLE;

    /**
     * For questions with a number of sub-parts (like matching, or
     * multiple-choice, multiple-reponse) display the number of sub-parts that
     * were correct.
     * @var integer {@link question_display_options::HIDDEN} or
     * {@link question_display_options::VISIBLE}
     */
    public $numpartscorrect = self::VISIBLE;

    /**
     * Should the general feedback be visible?
     * @var integer {@link question_display_options::HIDDEN} or
     * {@link question_display_options::VISIBLE}
     */
    public $generalfeedback = self::VISIBLE;

    /**
     * Should the automatically generated display of what the correct answer is
     * be visible?
     * @var integer {@link question_display_options::HIDDEN} or
     * {@link question_display_options::VISIBLE}
     */
    public $rightanswer = self::VISIBLE;

    /**
     * Should the manually added marker's comment be visible. Should the link for
     * adding/editing the comment be there.
     * @var integer {@link question_display_options::HIDDEN},
     * {@link question_display_options::VISIBLE}, or {@link question_display_options::EDITABLE}.
     * Editable means that form fields are displayed inline.
     */
    public $manualcomment = self::VISIBLE;

    /**
     * Should we show a 'Make comment or override grade' link?
     * @var string base URL for the edit comment script, which will be shown if
     * $manualcomment = self::VISIBLE.
     */
    public $manualcommentlink = null;

    /**
     * Used in places like the question history table, to show a link to review
     * this question in a certain state. If blank, a link is not shown.
     * @var string base URL for a review question script.
     */
    public $questionreviewlink = null;

    /**
     * Should the history of previous question states table be visible?
     * @var integer {@link question_display_options::HIDDEN} or
     * {@link question_display_options::VISIBLE}
     */
    public $history = self::HIDDEN;

    /**
     * @var int the context the attempt being output belongs to.
     */
    public $context;

    /**
     * Set all the feedback-related fields {@link $feedback}, {@link generalfeedback},
     * {@link rightanswer} and {@link manualcomment} to
     * {@link question_display_options::HIDDEN}.
     */
    public function hide_all_feedback() {
        $this->feedback = self::HIDDEN;
        $this->numpartscorrect = self::HIDDEN;
        $this->generalfeedback = self::HIDDEN;
        $this->rightanswer = self::HIDDEN;
        $this->manualcomment = self::HIDDEN;
        $this->correctness = self::HIDDEN;
    }

    /**
     * Returns the valid choices for the number of decimal places for showing
     * question marks. For use in the user interface.
     *
     * Calling code should probably use {@link question_engine::get_dp_options()}
     * rather than calling this method directly.
     *
     * @return array suitable for passing to {@link choose_from_menu()} or similar.
     */
    public static function get_dp_options() {
        $options = array();
        for ($i = 0; $i <= self::MAX_DP; $i += 1) {
            $options[$i] = $i;
        }
        return $options;
    }
}


/**
 * Contains the logic for handling question flags.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class question_flags {
    /**
     * Get the checksum that validates that a toggle request is valid.
     * @param int $qubaid the question usage id.
     * @param int $questionid the question id.
     * @param int $sessionid the question_attempt id.
     * @param object $user the user. If null, defaults to $USER.
     * @return string that needs to be sent to question/toggleflag.php for it to work.
     */
    protected static function get_toggle_checksum($qubaid, $questionid, $qaid, $slot, $user = null) {
        if (is_null($user)) {
            global $USER;
            $user = $USER;
        }
        return md5($qubaid . "_" . $user->secret . "_" . $questionid . "_" . $qaid . "_" . $slot);
    }

    /**
     * Get the postdata that needs to be sent to question/toggleflag.php to change the flag state.
     * You need to append &newstate=0/1 to this.
     * @return the post data to send.
     */
    public static function get_postdata(question_attempt $qa) {
        $qaid = $qa->get_database_id();
        $qubaid = $qa->get_usage_id();
        $qid = $qa->get_question()->id;
        $slot = $qa->get_slot();
        $checksum = self::get_toggle_checksum($qubaid, $qid, $qaid, $slot);
        return "qaid=$qaid&qubaid=$qubaid&qid=$qid&slot=$slot&checksum=$checksum&sesskey=" .
                sesskey() . '&newstate=';
    }

    /**
     * If the request seems valid, update the flag state of a question attempt.
     * Throws exceptions if this is not a valid update request.
     * @param int $qubaid the question usage id.
     * @param int $questionid the question id.
     * @param int $sessionid the question_attempt id.
     * @param string $checksum checksum, as computed by {@link get_toggle_checksum()}
     *      corresponding to the last three arguments.
     * @param bool $newstate the new state of the flag. true = flagged.
     */
    public static function update_flag($qubaid, $questionid, $qaid, $slot, $checksum, $newstate) {
        // Check the checksum - it is very hard to know who a question session belongs
        // to, so we require that checksum parameter is matches an md5 hash of the
        // three ids and the users username. Since we are only updating a flag, that
        // probably makes it sufficiently difficult for malicious users to toggle
        // other users flags.
        if ($checksum != question_flags::get_toggle_checksum($qubaid, $questionid, $qaid, $slot)) {
            throw new moodle_exception('errorsavingflags', 'question');
        }

        $dm = new question_engine_data_mapper();
        $dm->update_question_attempt_flag($qubaid, $questionid, $qaid, $slot, $newstate);
    }

    public static function initialise_js() {
        global $CFG, $PAGE, $OUTPUT;
        static $done = false;
        if ($done) {
            return;
        }
        $module = array(
            'name' => 'core_question_flags',
            'fullpath' => '/question/flags.js',
            'requires' => array('base', 'dom', 'event-delegate', 'io-base'),
        );
        $actionurl = $CFG->wwwroot . '/question/toggleflag.php';
        $flagattributes = array(
            0 => array(
                'src' => $OUTPUT->pix_url('i/unflagged') . '',
                'title' => get_string('clicktoflag', 'question'),
                'alt' => get_string('notflagged', 'question'),
            ),
            1 => array(
                'src' => $OUTPUT->pix_url('i/flagged') . '',
                'title' => get_string('clicktounflag', 'question'),
                'alt' => get_string('flagged', 'question'),
            ),
        );
        $PAGE->requires->js_init_call('M.core_question_flags.init',
                array($actionurl, $flagattributes), false, $module);
        $done = true;
    }
}


/**
 * Exception thrown when the system detects that a student has done something
 * out-of-order to a question. This can happen, for example, if they click
 * the browser's back button in a quiz, then try to submit a different response.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_out_of_sequence_exception extends moodle_exception {
    public function __construct($qubaid, $slot, $postdata) {
        if ($postdata == null) {
            $postdata = data_submitted();
        }
        parent::__construct('submissionoutofsequence', 'question', '', null,
                "QUBAid: $qubaid, slot: $slot, post data: " . print_r($postdata, true));
    }
}


/**
 * This class keeps track of a group of questions that are being attempted,
 * and which state, and so on, each one is currently in.
 *
 * A quiz attempt or a lesson attempt could use an instance of this class to
 * keep track of all the questions in the attempt and process student submissions.
 * It is basically a collection of {@question_attempt} objects.
 *
 * The questions being attempted as part of this usage are identified by an integer
 * that is passed into many of the methods as $slot. ($question->id is not
 * used so that the same question can be used more than once in an attempt.)
 *
 * Normally, calling code should be able to do everything it needs to be calling
 * methods of this class. You should not normally need to get individual
 * {@question_attempt} objects and play around with their inner workind, in code
 * that it outside the quetsion engine.
 *
 * Instances of this class correspond to rows in the question_usages table.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_usage_by_activity {
    /**
     * @var integer|string the id for this usage. If this usage was loaded from
     * the database, then this is the database id. Otherwise a unique random
     * string is used.
     */
    protected $id = null;

    /**
     * @var string name of an archetypal behaviour, that should be used
     * by questions in this usage if possible.
     */
    protected $preferredbehaviour = null;

    /** @var object the context this usage belongs to. */
    protected $context;

    /** @var string plugin name of the plugin this usage belongs to. */
    protected $owningcomponent;

    /** @var array {@link question_attempt}s that make up this usage. */
    protected $questionattempts = array();

    /** @var question_usage_observer that tracks changes to this usage. */
    protected $observer;

    /**
     * Create a new instance. Normally, calling code should use
     * {@link question_engine::make_questions_usage_by_activity()} or
     * {@link question_engine::load_questions_usage_by_activity()} rather than
     * calling this constructor directly.
     *
     * @param string $component the plugin creating this attempt. For example mod_quiz.
     * @param object $context the context this usage belongs to.
     */
    public function __construct($component, $context) {
        $this->owningcomponent = $component;
        $this->context = $context;
        $this->observer = new question_usage_null_observer();
    }

    /**
     * @param string $behaviour the name of an archetypal behaviour, that should
     * be used by questions in this usage if possible.
     */
    public function set_preferred_behaviour($behaviour) {
        $this->preferredbehaviour = $behaviour;
        $this->observer->notify_modified();
    }

    /** @return string the name of the preferred behaviour. */
    public function get_preferred_behaviour() {
        return $this->preferredbehaviour;
    }

    /** @return object the context this usage belongs to. */
    public function get_owning_context() {
        return $this->context;
    }

    /** @return string the name of the plugin that owns this attempt. */
    public function get_owning_component() {
        return $this->owningcomponent;
    }

    /** @return int|string If this usage came from the database, then the id
     * from the question_usages table is returned. Otherwise a random string is
     * returned. */
    public function get_id() {
        if (is_null($this->id)) {
            $this->id = random_string(10);
        }
        return $this->id;
    }

    /** @return question_usage_observer that is tracking changes made to this usage. */
    public function get_observer() {
        return $this->observer;
    }

    /**
     * For internal use only. Used by {@link question_engine_data_mapper} to set
     * the id when a usage is saved to the database.
     * @param int $id the newly determined id for this usage.
     */
    public function set_id_from_database($id) {
        $this->id = $id;
        foreach ($this->questionattempts as $qa) {
            $qa->set_usage_id($id);
        }
    }

    /**
     * Add another question to this usage.
     *
     * The added question is not started until you call {@link start_question()}
     * on it.
     *
     * @param question_definition $question the question to add.
     * @param number $maxmark the maximum this question will be marked out of in
     *      this attempt (optional). If not given, $question->defaultmark is used.
     * @return int the number used to identify this question within this usage.
     */
    public function add_question(question_definition $question, $maxmark = null) {
        $qa = new question_attempt($question, $this->get_id(), $this->observer, $maxmark);
        if (count($this->questionattempts) == 0) {
            $this->questionattempts[1] = $qa;
        } else {
            $this->questionattempts[] = $qa;
        }
        $qa->set_number_in_usage(end(array_keys($this->questionattempts)));
        $this->observer->notify_attempt_added($qa);
        return $qa->get_slot();
    }

    /**
     * Get the question_definition for a question in this attempt.
     * @param int $slot the number used to identify this question within this usage.
     * @return question_definition the requested question object.
     */
    public function get_question($slot) {
        return $this->get_question_attempt($slot)->get_question();
    }

    /** @return array all the identifying numbers of all the questions in this usage. */
    public function get_slots() {
        return array_keys($this->questionattempts);
    }

    /** @return int the identifying number of the first question that was added to this usage. */
    public function get_first_question_number() {
        reset($this->questionattempts);
        return key($this->questionattempts);
    }

    /** @return int the number of questions that are currently in this usage. */
    public function question_count() {
        return count($this->questionattempts);
    }

    /**
     * Note the part of the {@link question_usage_by_activity} comment that explains
     * that {@link question_attempt} objects should be considered part of the inner
     * workings of the question engine, and should not, if possible, be accessed directly.
     *
     * @return question_attempt_iterator for iterating over all the questions being
     * attempted. as part of this usage.
     */
    public function get_attempt_iterator() {
        return new question_attempt_iterator($this);
    }

    /**
     * Check whether $number actually corresponds to a question attempt that is
     * part of this usage. Throws an exception if not.
     *
     * @param int $slot a number allegedly identifying a question within this usage.
     */
    protected function check_slot($slot) {
        if (!array_key_exists($slot, $this->questionattempts)) {
            throw new coding_exception("There is no question_attempt number $slot in this attempt.");
        }
    }

    /**
     * Note the part of the {@link question_usage_by_activity} comment that explains
     * that {@link question_attempt} objects should be considered part of the inner
     * workings of the question engine, and should not, if possible, be accessed directly.
     *
     * @param int $slot the number used to identify this question within this usage.
     * @return question_attempt the corresponding {@link question_attempt} object.
     */
    public function get_question_attempt($slot) {
        $this->check_slot($slot);
        return $this->questionattempts[$slot];
    }

    /**
     * Get the current state of the attempt at a question.
     * @param int $slot the number used to identify this question within this usage.
     * @return question_state.
     */
    public function get_question_state($slot) {
        return $this->get_question_attempt($slot)->get_state();
    }

    /**
     * @param int $slot the number used to identify this question within this usage.
     * @param bool $showcorrectness Whether right/partial/wrong states should
     * be distinguised.
     * @return string A brief textual description of the current state.
     */
    public function get_question_state_string($slot, $showcorrectness) {
        return $this->get_question_attempt($slot)->get_state_string($showcorrectness);
    }

    /**
     * Get the time of the most recent action performed on a question.
     * @param int $slot the number used to identify this question within this usage.
     * @return int timestamp.
     */
    public function get_question_action_time($slot) {
        return $this->get_question_attempt($slot)->get_last_action_time();
    }

    /**
     * Get the current fraction awarded for the attempt at a question.
     * @param int $slot the number used to identify this question within this usage.
     * @return number|null The current fraction for this question, or null if one has
     * not been assigned yet.
     */
    public function get_question_fraction($slot) {
        return $this->get_question_attempt($slot)->get_fraction();
    }

    /**
     * Get the current mark awarded for the attempt at a question.
     * @param int $slot the number used to identify this question within this usage.
     * @return number|null The current mark for this question, or null if one has
     * not been assigned yet.
     */
    public function get_question_mark($slot) {
        return $this->get_question_attempt($slot)->get_mark();
    }

    /**
     * Get the maximum mark possible for the attempt at a question.
     * @param int $slot the number used to identify this question within this usage.
     * @return number the available marks for this question.
     */
    public function get_question_max_mark($slot) {
        return $this->get_question_attempt($slot)->get_max_mark();
    }

    /**
     * Get the current mark awarded for the attempt at a question.
     * @param int $slot the number used to identify this question within this usage.
     * @return number|null The current mark for this question, or null if one has
     * not been assigned yet.
     */
    public function get_total_mark() {
        $mark = 0;
        foreach ($this->questionattempts as $qa) {
            if ($qa->get_max_mark() > 0 && $qa->get_state() == question_state::$needsgrading) {
                return null;
            }
            $mark += $qa->get_mark();
        }
        return $mark;
    }

    /**
     * @return string a simple textual summary of the question that was asked.
     */
    public function get_question_summary($slot) {
        return $this->get_question_attempt($slot)->get_question_summary();
    }

    /**
     * @return string a simple textual summary of response given.
     */
    public function get_response_summary($slot) {
        return $this->get_question_attempt($slot)->get_response_summary();
    }

    /**
     * @return string a simple textual summary of the correct resonse.
     */
    public function get_right_answer_summary($slot) {
        return $this->get_question_attempt($slot)->get_right_answer_summary();
    }

    /**
     * Get the {@link core_question_renderer}, in collaboration with appropriate
     * {@link qbehaviour_renderer} and {@link qtype_renderer} subclasses, to generate the
     * HTML to display this question.
     * @param int $slot the number used to identify this question within this usage.
     * @param question_display_options $options controls how the question is rendered.
     * @param string|null $number The question number to display. 'i' is a special
     *      value that gets displayed as Information. Null means no number is displayed.
     * @return string HTML fragment representing the question.
     */
    public function render_question($slot, $options, $number = null) {
        $options->context = $this->context;
        return $this->get_question_attempt($slot)->render($options, $number);
    }

    /**
     * Generate any bits of HTML that needs to go in the <head> tag when this question
     * is displayed in the body.
     * @param int $slot the number used to identify this question within this usage.
     * @return string HTML fragment.
     */
    public function render_question_head_html($slot) {
        $options->context = $this->context;
        return $this->get_question_attempt($slot)->render_head_html();
    }

    /**
     * Like {@link render_question()} but displays the question at the past step
     * indicated by $seq, rather than showing the latest step.
     *
     * @param int $slot the number used to identify this question within this usage.
     * @param int $seq the seq number of the past state to display.
     * @param question_display_options $options controls how the question is rendered.
     * @param string|null $number The question number to display. 'i' is a special
     *      value that gets displayed as Information. Null means no number is displayed.
     * @return string HTML fragment representing the question.
     */
    public function render_question_at_step($slot, $seq, $options, $number = null) {
        $options->context = $this->context;
        return $this->get_question_attempt($slot)->render_at_step($seq, $options, $number, $this->preferredbehaviour);
    }

    /**
     * Checks whether the users is allow to be served a particular file.
     * @param int $slot the number used to identify this question within this usage.
     * @param question_display_options $options the options that control display of the question.
     * @param string $component the name of the component we are serving files for.
     * @param string $filearea the name of the file area.
     * @param array $args the remaining bits of the file path.
     * @param bool $forcedownload whether the user must be forced to download the file.
     * @return bool true if the user can access this file.
     */
    public function check_file_access($slot, $options, $component, $filearea, $args, $forcedownload) {
        return $this->get_question_attempt($slot)->check_file_access($options, $component, $filearea, $args, $forcedownload);
    }

    /**
     * Replace a particular question_attempt with a different one.
     *
     * For internal use only. Used when reloading the state of a question from the
     * database.
     *
     * @param array $records Raw records loaded from the database.
     * @param int $questionattemptid The id of the question_attempt to extract.
     * @return question_attempt The newly constructed question_attempt_step.
     */
    public function replace_loaded_question_attempt_info($slot, $qa) {
        $this->check_slot($slot);
        $this->questionattempts[$slot] = $qa;
    }

    /**
     * You should probably not use this method in code outside the question engine.
     * The main reason for exposing it was for the benefit of unit tests.
     * @param int $slot the number used to identify this question within this usage.
     * @return string return the prefix that is pre-pended to field names in the HTML
     * that is output.
     */
    public function get_field_prefix($slot) {
        return $this->get_question_attempt($slot)->get_field_prefix();
    }

    /**
     * Start the attempt at a question that has been added to this usage.
     * @param int $slot the number used to identify this question within this usage.
     */
    public function start_question($slot) {
        $qa = $this->get_question_attempt($slot);
        $qa->start($this->preferredbehaviour);
        $this->observer->notify_attempt_modified($qa);
    }

    /**
     * Start the attempt at all questions that has been added to this usage.
     */
    public function start_all_questions() {
        foreach ($this->questionattempts as $qa) {
            $qa->start($this->preferredbehaviour);
            $this->observer->notify_attempt_modified($qa);
        }
    }

    /**
     * Start the attempt at a question, starting from the point where the previous
     * question_attempt $oldqa had reached. This is used by the quiz 'Each attempt
     * builds on last' mode.
     * @param int $slot the number used to identify this question within this usage.
     * @param question_attempt $oldqa a previous attempt at this quetsion that
     *      defines the starting point.
     */
    public function start_question_based_on($slot, question_attempt $oldqa) {
        $qa = $this->get_question_attempt($slot);
        $qa->start_based_on($oldqa);
        $this->observer->notify_attempt_modified($qa);
    }

    /**
     * Process all the question actions in the current request.
     *
     * If there is a parameter slots included in the post data, then only
     * those question numbers will be processed, otherwise all questions in this
     * useage will be.
     *
     * This function also does {@link update_question_flags()}.
     *
     * @param int $timestamp optional, use this timestamp as 'now'.
     * @param array $postdata optional, only intended for testing. Use this data
     * instead of the data from $_POST.
     */
    public function process_all_actions($timestamp = null, $postdata = null) {
        $slots = question_attempt::get_submitted_var('slots', PARAM_SEQUENCE, $postdata);
        if (is_null($slots)) {
            $slots = $this->get_slots();
        } else if (!$slots) {
            $slots = array();
        } else {
            $slots = explode(',', $slots);
        }
        foreach ($slots as $slot) {
            if (!$this->validate_sequence_number($slot, $postdata)) {
                continue;
            }
            $submitteddata = $this->extract_responses($slot, $postdata);
            $this->process_action($slot, $submitteddata, $timestamp);
        }
        $this->update_question_flags($postdata);
    }

    /**
     * Get the submitted data from the current request that belongs to this
     * particular question.
     *
     * @param int $slot the number used to identify this question within this usage.
     * @param $postdata optional, only intended for testing. Use this data
     * instead of the data from $_POST.
     * @return array submitted data specific to this question.
     */
    public function extract_responses($slot, $postdata = null) {
        return $this->get_question_attempt($slot)->get_submitted_data($postdata);
    }

    /**
     * Process a specific action on a specific question.
     * @param int $slot the number used to identify this question within this usage.
     * @param $submitteddata the submitted data that constitutes the action.
     */
    public function process_action($slot, $submitteddata, $timestamp = null) {
        $qa = $this->get_question_attempt($slot);
        $qa->process_action($submitteddata, $timestamp);
        $this->observer->notify_attempt_modified($qa);
    }

    /**
     * Check that the sequence number, that detects weird things like the student
     * clicking back, is OK. If the sequence check variable is not present, returns
     * false. If the check variable is present and correct, returns true. If the
     * variable is present and wrong, throws an exception.
     * @param int $slot the number used to identify this question within this usage.
     * @param array $submitteddata the submitted data that constitutes the action.
     * @return bool true if the check variable is present and correct. False if it
     * is missing. (Throws an exception if the check fails.)
     */
    public function validate_sequence_number($slot, $postdata = null) {
        $qa = $this->get_question_attempt($slot);
        $sequencecheck = $qa->get_submitted_var(
                $qa->get_control_field_name('sequencecheck'), PARAM_INT, $postdata);
        if (is_null($sequencecheck)) {
            return false;
        } else if ($sequencecheck != $qa->get_num_steps()) {
            throw new question_out_of_sequence_exception($this->id, $slot, $postdata);
        } else {
            return true;
        }
    }
    /**
     * Update the flagged state for all question_attempts in this usage, if their
     * flagged state was changed in the request.
     *
     * @param $postdata optional, only intended for testing. Use this data
     * instead of the data from $_POST.
     */
    public function update_question_flags($postdata = null) {
        foreach ($this->questionattempts as $qa) {
            $flagged = $qa->get_submitted_var(
                    $qa->get_flag_field_name(), PARAM_BOOL, $postdata);
            if (!is_null($flagged) && $flagged != $qa->is_flagged()) {
                $qa->set_flagged($flagged);
            }
        }
    }

    /**
     * Get the correct response to a particular question. Passing the results of
     * this method to {@link process_action()} will probably result in full marks.
     * @param int $slot the number used to identify this question within this usage.
     * @return array that constitutes a correct response to this question.
     */
    public function get_correct_response($slot) {
        return $this->get_question_attempt($slot)->get_correct_response();
    }

    /**
     * Finish the active phase of an attempt at a question.
     *
     * This is an external act of finishing the attempt. Think, for example, of
     * the 'Submit all and finish' button in the quiz. Some behaviours,
     * (for example, immediatefeedback) give a way of finishing the active phase
     * of a question attempt as part of a {@link process_action()} call.
     *
     * After the active phase is over, the only changes possible are things like
     * manual grading, or changing the flag state.
     *
     * @param int $slot the number used to identify this question within this usage.
     */
    public function finish_question($slot, $timestamp = null) {
        $qa = $this->get_question_attempt($slot);
        $qa->finish($timestamp);
        $this->observer->notify_attempt_modified($qa);
    }

    /**
     * Finish the active phase of an attempt at a question. See {@link finish_question()}
     * for a fuller description of what 'finish' means.
     */
    public function finish_all_questions($timestamp = null) {
        foreach ($this->questionattempts as $qa) {
            $qa->finish($timestamp);
            $this->observer->notify_attempt_modified($qa);
        }
    }

    /**
     * Perform a manual grading action on a question attempt.
     * @param int $slot the number used to identify this question within this usage.
     * @param string $comment the comment being added to the question attempt.
     * @param number $mark the mark that is being assigned. Can be null to just
     * add a comment.
     */
    public function manual_grade($slot, $comment, $mark) {
        $qa = $this->get_question_attempt($slot);
        $qa->manual_grade($comment, $mark);
        $this->observer->notify_attempt_modified($qa);
    }

    /**
     * Regrade a question in this usage. This replays the sequence of submitted
     * actions to recompute the outcomes.
     * @param int $slot the number used to identify this question within this usage.
     * @param bool $finished whether the question attempt should be forced to be finished
     *      after the regrade, or whether it may still be in progress (default false).
     * @param number $newmaxmark (optional) if given, will change the max mark while regrading.
     */
    public function regrade_question($slot, $finished = false, $newmaxmark = null) {
        $oldqa = $this->get_question_attempt($slot);
        if (is_null($newmaxmark)) {
            $newmaxmark = $oldqa->get_max_mark();
        }

        $this->observer->notify_delete_attempt_steps($oldqa);

        $newqa = new question_attempt($oldqa->get_question(), $oldqa->get_usage_id(),
                $this->observer, $newmaxmark);
        $newqa->set_database_id($oldqa->get_database_id());
        $newqa->regrade($oldqa, $finished);

        $this->questionattempts[$slot] = $newqa;
        $this->observer->notify_attempt_modified($newqa);
    }

    /**
     * Regrade all the questions in this usage (without changing their max mark).
     * @param bool $finished whether each question should be forced to be finished
     *      after the regrade, or whether it may still be in progress (default false).
     */
    public function regrade_all_questions($finished = false) {
        foreach ($this->questionattempts as $slot => $notused) {
            $this->regrade_question($slot, $finished);
        }
    }

    /**
     * Create a question_usage_by_activity from records loaded from the database.
     *
     * For internal use only.
     *
     * @param array $records Raw records loaded from the database.
     * @param int $questionattemptid The id of the question_attempt to extract.
     * @return question_attempt The newly constructed question_attempt_step.
     */
    public static function load_from_records(&$records, $qubaid) {
        $record = current($records);
        while ($record->qubaid != $qubaid) {
            $record = next($records);
            if (!$record) {
                throw new coding_exception("Question usage $qubaid not found in the database.");
            }
        }

        $quba = new question_usage_by_activity($record->component,
            get_context_instance_by_id($record->contextid));
        $quba->set_id_from_database($record->qubaid);
        $quba->set_preferred_behaviour($record->preferredbehaviour);

        $quba->observer = new question_engine_unit_of_work($quba);

        while ($record && $record->qubaid == $qubaid && !is_null($record->slot)) {
            $quba->questionattempts[$record->slot] =
                    question_attempt::load_from_records($records,
                    $record->questionattemptid, $quba->observer,
                    $quba->get_preferred_behaviour());
            $record = current($records);
        }

        return $quba;
    }
}


/**
 * A class abstracting access to the
 * {@link question_usage_by_activity::$questionattempts} array.
 *
 * This class snapshots the list of {@link question_attempts} to iterate over
 * when it is created. If a question is added to the usage mid-iteration, it
 * will now show up.
 *
 * To create an instance of this class, use
 * {@link question_usage_by_activity::get_attempt_iterator()}
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_attempt_iterator implements Iterator, ArrayAccess {
    /** @var question_usage_by_activity that we are iterating over. */
    protected $quba;
    /** @var array of question numbers. */
    protected $slots;

    /**
     * To create an instance of this class, use {@link question_usage_by_activity::get_attempt_iterator()}.
     * @param $quba the usage to iterate over.
     */
    public function __construct(question_usage_by_activity $quba) {
        $this->quba = $quba;
        $this->slots = $quba->get_slots();
        $this->rewind();
    }

    /** @return question_attempt_step */
    public function current() {
        return $this->offsetGet(current($this->slots));
    }
    /** @return int */
    public function key() {
        return current($this->slots);
    }
    public function next() {
        next($this->slots);
    }
    public function rewind() {
        reset($this->slots);
    }
    /** @return bool */
    public function valid() {
        return current($this->slots) !== false;
    }

    /** @return bool */
    public function offsetExists($slot) {
        return in_array($slot, $this->slots);
    }
    /** @return question_attempt_step */
    public function offsetGet($slot) {
        return $this->quba->get_question_attempt($slot);
    }
    public function offsetSet($slot, $value) {
        throw new coding_exception('You are only allowed read-only access to question_attempt::states through a question_attempt_step_iterator. Cannot set.');
    }
    public function offsetUnset($slot) {
        throw new coding_exception('You are only allowed read-only access to question_attempt::states through a question_attempt_step_iterator. Cannot unset.');
    }
}


/**
 * Tracks an attempt at one particular question in a {@link question_usage_by_activity}.
 *
 * Most calling code should need to access objects of this class. They should be
 * able to do everything through the usage interface. This class is an internal
 * implementation detail of the question engine.
 *
 * Instances of this class correspond to rows in the question_attempts table, and
 * a collection of {@link question_attempt_steps}. Question inteaction models and
 * question types do work with question_attempt objects.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_attempt {
    /**
     * @var string this is a magic value that question types can return from
     * {@link question_definition::get_expected_data()}.
     */
    const USE_RAW_DATA = 'use raw data';

    /**
     * @var string special value used by manual grading because {@link PARAM_NUMBER}
     * converts '' to 0.
     */
    const PARAM_MARK = 'parammark';

    /**
     * @var string special value to indicate a response variable that is uploaded
     * files.
     */
    const PARAM_FILES = 'paramfiles';

    /**
     * @var string special value to indicate a response variable that is uploaded
     * files.
     */
    const PARAM_CLEANHTML_FILES = 'paramcleanhtmlfiles';

    /** @var integer if this attempts is stored in the question_attempts table, the id of that row. */
    protected $id = null;

    /** @var integer|string the id of the question_usage_by_activity we belong to. */
    protected $usageid;

    /** @var integer the number used to identify this question_attempt within the usage. */
    protected $slot = null;

    /**
     * @var question_behaviour the behaviour controlling this attempt.
     * null until {@link start()} is called.
     */
    protected $behaviour = null;

    /** @var question_definition the question this is an attempt at. */
    protected $question;

    /** @var number the maximum mark that can be scored at this question. */
    protected $maxmark;

    /**
     * @var number the minimum fraction that can be scored at this question, so
     * the minimum mark is $this->minfraction * $this->maxmark.
     */
    protected $minfraction = null;

    /**
     * @var string plain text summary of the variant of the question the
     * student saw. Intended for reporting purposes.
     */
    protected $questionsummary = null;

    /**
     * @var string plain text summary of the response the student gave.
     * Intended for reporting purposes.
     */
    protected $responsesummary = null;

    /**
     * @var string plain text summary of the correct response to this question
     * variant the student saw. The format should be similar to responsesummary.
     * Intended for reporting purposes.
     */
    protected $rightanswer = null;

    /** @var array of {@link question_attempt_step}s. The steps in this attempt. */
    protected $steps = array();

    /** @var boolean whether the user has flagged this attempt within the usage. */
    protected $flagged = false;

    /** @var question_usage_observer tracks changes to the useage this attempt is part of.*/
    protected $observer;

    /**#@+
     * Constants used by the intereaction models to indicate whether the current
     * pending step should be kept or discarded.
     */
    const KEEP = true;
    const DISCARD = false;
    /**#@-*/

    /**
     * Create a new {@link question_attempt}. Normally you should create question_attempts
     * indirectly, by calling {@link question_usage_by_activity::add_question()}.
     *
     * @param question_definition $question the question this is an attempt at.
     * @param int|string $usageid The id of the
     *      {@link question_usage_by_activity} we belong to. Used by {@link get_field_prefix()}.
     * @param question_usage_observer $observer tracks changes to the useage this
     *      attempt is part of. (Optional, a {@link question_usage_null_observer} is
     *      used if one is not passed.
     * @param number $maxmark the maximum grade for this question_attempt. If not
     * passed, $question->defaultmark is used.
     */
    public function __construct(question_definition $question, $usageid,
            question_usage_observer $observer = null, $maxmark = null) {
        $this->question = $question;
        $this->usageid = $usageid;
        if (is_null($observer)) {
            $observer = new question_usage_null_observer();
        }
        $this->observer = $observer;
        if (!is_null($maxmark)) {
            $this->maxmark = $maxmark;
        } else {
            $this->maxmark = $question->defaultmark;
        }
    }

    /** @return question_definition the question this is an attempt at. */
    public function get_question() {
        return $this->question;
    }

    /**
     * Set the number used to identify this question_attempt within the usage.
     * For internal use only.
     * @param int $slot
     */
    public function set_number_in_usage($slot) {
        $this->slot = $slot;
    }

    /** @return int the number used to identify this question_attempt within the usage. */
    public function get_slot() {
        return $this->slot;
    }

    /**
     * @return int the id of row for this question_attempt, if it is stored in the
     * database. null if not.
     */
    public function get_database_id() {
        return $this->id;
    }

    /**
     * For internal use only. Set the id of the corresponding database row.
     * @param int $id the id of row for this question_attempt, if it is
     * stored in the database.
     */
    public function set_database_id($id) {
        $this->id = $id;
    }

    /** @return int|string the id of the {@link question_usage_by_activity} we belong to. */
    public function get_usage_id() {
        return $this->usageid;
    }

    /**
     * Set the id of the {@link question_usage_by_activity} we belong to.
     * For internal use only.
     * @param int|string the new id.
     */
    public function set_usage_id($usageid) {
        $this->usageid = $usageid;
    }

    /** @return string the name of the behaviour that is controlling this attempt. */
    public function get_behaviour_name() {
        return $this->behaviour->get_name();
    }

    /**
     * For internal use only.
     * @return question_behaviour the behaviour that is controlling this attempt.
     */
    public function get_behaviour() {
        return $this->behaviour;
    }

    /**
     * Set the flagged state of this question.
     * @param bool $flagged the new state.
     */
    public function set_flagged($flagged) {
        $this->flagged = $flagged;
        $this->observer->notify_attempt_modified($this);
    }

    /** @return bool whether this question is currently flagged. */
    public function is_flagged() {
        return $this->flagged;
    }

    /**
     * Get the name (in the sense a HTML name="" attribute, or a $_POST variable
     * name) to use for the field that indicates whether this question is flagged.
     *
     * @return string  The field name to use.
     */
    public function get_flag_field_name() {
        return $this->get_control_field_name('flagged');
    }

    /**
     * Get the name (in the sense a HTML name="" attribute, or a $_POST variable
     * name) to use for a question_type variable belonging to this question_attempt.
     *
     * See the comment on {@link question_attempt_step} for an explanation of
     * question type and behaviour variables.
     *
     * @param $varname The short form of the variable name.
     * @return string  The field name to use.
     */
    public function get_qt_field_name($varname) {
        return $this->get_field_prefix() . $varname;
    }

    /**
     * Get the name (in the sense a HTML name="" attribute, or a $_POST variable
     * name) to use for a question_type variable belonging to this question_attempt.
     *
     * See the comment on {@link question_attempt_step} for an explanation of
     * question type and behaviour variables.
     *
     * @param $varname The short form of the variable name.
     * @return string  The field name to use.
     */
    public function get_behaviour_field_name($varname) {
        return $this->get_field_prefix() . '-' . $varname;
    }

    /**
     * Get the name (in the sense a HTML name="" attribute, or a $_POST variable
     * name) to use for a control variables belonging to this question_attempt.
     *
     * Examples are :sequencecheck and :flagged
     *
     * @param $varname The short form of the variable name.
     * @return string  The field name to use.
     */
    public function get_control_field_name($varname) {
        return $this->get_field_prefix() . ':' . $varname;
    }

    /**
     * Get the prefix added to variable names to give field names for this
     * question attempt.
     *
     * You should not use this method directly. This is an implementation detail
     * anyway, but if you must access it, use {@link question_usage_by_activity::get_field_prefix()}.
     *
     * @param $varname The short form of the variable name.
     * @return string  The field name to use.
     */
    public function get_field_prefix() {
        return 'q' . $this->usageid . ':' . $this->slot . '_';
    }

    /**
     * Get one of the steps in this attempt.
     * For internal/test code use only.
     * @param int $i the step number.
     * @return question_attempt_step
     */
    public function get_step($i) {
        if ($i < 0 || $i >= count($this->steps)) {
            throw new coding_exception('Index out of bounds in question_attempt::get_step.');
        }
        return $this->steps[$i];
    }

    /**
     * Get the number of steps in this attempt.
     * For internal/test code use only.
     * @return int the number of steps we currently have.
     */
    public function get_num_steps() {
        return count($this->steps);
    }

    /**
     * Return the latest step in this question_attempt.
     * For internal/test code use only.
     * @return question_attempt_step
     */
    public function get_last_step() {
        if (count($this->steps) == 0) {
            return new question_null_step();
        }
        return end($this->steps);
    }

    /**
     * @return question_attempt_step_iterator for iterating over the steps in
     * this attempt, in order.
     */
    public function get_step_iterator() {
        return new question_attempt_step_iterator($this);
    }

    /**
     * The same as {@link get_step_iterator()}. However, for a
     * {@link question_attempt_with_restricted_history} this returns the full
     * list of steps, while {@link get_step_iterator()} returns only the
     * limited history.
     * @return question_attempt_step_iterator for iterating over the steps in
     * this attempt, in order.
     */
    public function get_full_step_iterator() {
        return $this->get_step_iterator();
    }

    /**
     * @return question_attempt_reverse_step_iterator for iterating over the steps in
     * this attempt, in reverse order.
     */
    public function get_reverse_step_iterator() {
        return new question_attempt_reverse_step_iterator($this);
    }

    /**
     * Get the qt data from the latest step that has any qt data. Return $default
     * array if it is no step has qt data.
     *
     * @param string $name the name of the variable to get.
     * @param mixed default the value to return no step has qt data.
     *      (Optional, defaults to an empty array.)
     * @return array|mixed the data, or $default if there is not any.
     */
    public function get_last_qt_data($default = array()) {
        foreach ($this->get_reverse_step_iterator() as $step) {
            $response = $step->get_qt_data();
            if (!empty($response)) {
                return $response;
            }
        }
        return $default;
    }

    /**
     * Get the last step with a particular question type varialbe set.
     * @param string $name the name of the variable to get.
     * @return question_attempt_step the last step, or a step with no variables
     * if there was not a real step.
     */
    public function get_last_step_with_qt_var($name) {
        foreach ($this->get_reverse_step_iterator() as $step) {
            if ($step->has_qt_var($name)) {
                return $step;
            }
        }
        return new question_attempt_step_read_only();
    }

    /**
     * Get the latest value of a particular question type variable. That is, get
     * the value from the latest step that has it set. Return null if it is not
     * set in any step.
     *
     * @param string $name the name of the variable to get.
     * @param mixed default the value to return in the variable has never been set.
     *      (Optional, defaults to null.)
     * @return mixed string value, or $default if it has never been set.
     */
    public function get_last_qt_var($name, $default = null) {
        $step = $this->get_last_step_with_qt_var($name);
        if ($step->has_qt_var($name)) {
            return $step->get_qt_var($name);
        } else {
            return $default;
        }
    }

    /**
     * Get the latest set of files for a particular question type variable of
     * type question_attempt::PARAM_FILES.
     *
     * @param string $name the name of the associated variable.
     * @return array of {@link stored_files}.
     */
    public function get_last_qt_files($name, $contextid) {
        foreach ($this->get_reverse_step_iterator() as $step) {
            if ($step->has_qt_var($name)) {
                return $step->get_qt_files($name, $contextid);
            }
        }
        return array();
    }

    /**
     * Get the URL of a file that belongs to a response variable of this
     * question_attempt.
     * @param stored_file $file the file to link to.
     * @return string the URL of that file.
     */
    public function get_response_file_url(stored_file $file) {
        return file_encode_url(new moodle_url('/pluginfile.php'), '/' . implode('/', array(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $this->usageid,
                $this->slot,
                $file->get_itemid())) .
                $file->get_filepath() . $file->get_filename(), true);
    }

    /**
     * Prepare a draft file are for the files belonging the a response variable
     * of this question attempt. The draft area is populated with the files from
     * the most recent step having files.
     *
     * @param string $name the variable name the files belong to.
     * @param int $contextid the id of the context the quba belongs to.
     * @return int the draft itemid.
     */
    public function prepare_response_files_draft_itemid($name, $contextid) {
        foreach ($this->get_reverse_step_iterator() as $step) {
            if ($step->has_qt_var($name)) {
                return $step->prepare_response_files_draft_itemid($name, $contextid);
            }
        }

        // No files yet.
        $draftid = 0; // Will be filled in by file_prepare_draft_area.
        file_prepare_draft_area($draftid, $contextid, 'question', 'response_' . $name, null);
        return $draftid;
    }

    /**
     * Get the latest value of a particular behaviour variable. That is,
     * get the value from the latest step that has it set. Return null if it is
     * not set in any step.
     *
     * @param string $name the name of the variable to get.
     * @param mixed default the value to return in the variable has never been set.
     *      (Optional, defaults to null.)
     * @return mixed string value, or $default if it has never been set.
     */
    public function get_last_behaviour_var($name, $default = null) {
        foreach ($this->get_reverse_step_iterator() as $step) {
            if ($step->has_behaviour_var($name)) {
                return $step->get_behaviour_var($name);
            }
        }
        return $default;
    }

    /**
     * Get the current state of this question attempt. That is, the state of the
     * latest step.
     * @return question_state
     */
    public function get_state() {
        return $this->get_last_step()->get_state();
    }

    /**
     * @param bool $showcorrectness Whether right/partial/wrong states should
     * be distinguised.
     * @return string A brief textual description of the current state.
     */
    public function get_state_string($showcorrectness) {
        return $this->behaviour->get_state_string($showcorrectness);
    }

    /**
     * @return int the timestamp of the most recent step in this question attempt.
     */
    public function get_last_action_time() {
        return $this->get_last_step()->get_timecreated();
    }

    /**
     * Get the current fraction of this question attempt. That is, the fraction
     * of the latest step, or null if this question has not yet been graded.
     * @return number the current fraction.
     */
    public function get_fraction() {
        return $this->get_last_step()->get_fraction();
    }

    /** @return bool whether this question attempt has a non-zero maximum mark. */
    public function has_marks() {
        // Since grades are stored in the database as NUMBER(12,7).
        return $this->maxmark >= 0.00000005;
    }

    /**
     * @return number the current mark for this question.
     * {@link get_fraction()} * {@link get_max_mark()}.
     */
    public function get_mark() {
        return $this->fraction_to_mark($this->get_fraction());
    }

    /**
     * This is used by the manual grading code, particularly in association with
     * validation. If there is a mark submitted in the request, then use that,
     * otherwise use the latest mark for this question.
     * @return number the current mark for this question.
     * {@link get_fraction()} * {@link get_max_mark()}.
     */
    public function get_current_manual_mark() {
        $mark = $this->get_submitted_var($this->get_behaviour_field_name('mark'), question_attempt::PARAM_MARK);
        if (is_null($mark)) {
            return $this->get_mark();
        } else {
            return $mark;
        }
    }

    /**
     * @param number|null $fraction a fraction.
     * @return number|null the corresponding mark.
     */
    public function fraction_to_mark($fraction) {
        if (is_null($fraction)) {
            return null;
        }
        return $fraction * $this->maxmark;
    }

    /** @return number the maximum mark possible for this question attempt. */
    public function get_max_mark() {
        return $this->maxmark;
    }

    /** @return number the maximum mark possible for this question attempt. */
    public function get_min_fraction() {
        if (is_null($this->minfraction)) {
            throw new coding_exception('This question_attempt has not been started yet, the min fraction is not yet konwn.');
        }
        return $this->minfraction;
    }

    /**
     * The current mark, formatted to the stated number of decimal places. Uses
     * {@link format_float()} to format floats according to the current locale.
     * @param int $dp number of decimal places.
     * @return string formatted mark.
     */
    public function format_mark($dp) {
        return $this->format_fraction_as_mark($this->get_fraction(), $dp);
    }

    /**
     * The current mark, formatted to the stated number of decimal places. Uses
     * {@link format_float()} to format floats according to the current locale.
     * @param int $dp number of decimal places.
     * @return string formatted mark.
     */
    public function format_fraction_as_mark($fraction, $dp) {
        return format_float($this->fraction_to_mark($fraction), $dp);
    }

    /**
     * The maximum mark for this question attempt, formatted to the stated number
     * of decimal places. Uses {@link format_float()} to format floats according
     * to the current locale.
     * @param int $dp number of decimal places.
     * @return string formatted maximum mark.
     */
    public function format_max_mark($dp) {
        return format_float($this->maxmark, $dp);
    }

    /**
     * Return the hint that applies to the question in its current state, or null.
     * @return question_hint|null
     */
    public function get_applicable_hint() {
        return $this->behaviour->get_applicable_hint();
    }

    /**
     * Produce a plain-text summary of what the user did during a step.
     * @param question_attempt_step $step the step in quetsion.
     * @return string a summary of what was done during that step.
     */
    public function summarise_action(question_attempt_step $step) {
        return $this->behaviour->summarise_action($step);
    }

    /**
     * Helper function used by {@link rewrite_pluginfile_urls()} and
     * {@link rewrite_response_pluginfile_urls()}.
     * @return array ids that need to go into the file paths.
     */
    protected function extra_file_path_components() {
        return array($this->get_usage_id(), $this->get_slot());
    }

    /**
     * Calls {@link question_rewrite_question_urls()} with appropriate parameters
     * for content belonging to this question.
     * @param string $text the content to output.
     * @param string $component the component name (normally 'question' or 'qtype_...')
     * @param string $filearea the name of the file area.
     * @param int $itemid the item id.
     * @return srting the content with the URLs rewritten.
     */
    public function rewrite_pluginfile_urls($text, $component, $filearea, $itemid) {
        return question_rewrite_question_urls($text, 'pluginfile.php',
                $this->question->contextid, $component, $filearea,
                $this->extra_file_path_components(), $itemid);
    }

    /**
     * Calls {@link question_rewrite_question_urls()} with appropriate parameters
     * for content belonging to responses to this question.
     *
     * @param string $text the text to update the URLs in.
     * @param int $contextid the id of the context the quba belongs to.
     * @param string $name the variable name the files belong to.
     * @param question_attempt_step $step the step the response is coming from.
     * @return srting the content with the URLs rewritten.
     */
    public function rewrite_response_pluginfile_urls($text, $contextid, $name,
            question_attempt_step $step) {
        return $step->rewrite_response_pluginfile_urls($text, $contextid, $name,
                $this->extra_file_path_components());
    }

    /**
     * Get the {@link core_question_renderer}, in collaboration with appropriate
     * {@link qbehaviour_renderer} and {@link qtype_renderer} subclasses, to generate the
     * HTML to display this question attempt in its current state.
     * @param question_display_options $options controls how the question is rendered.
     * @param string|null $number The question number to display.
     * @return string HTML fragment representing the question.
     */
    public function render($options, $number, $page = null) {
        if (is_null($page)) {
            global $PAGE;
            $page = $PAGE;
        }
        $qoutput = $page->get_renderer('core', 'question');
        $qtoutput = $this->question->get_renderer($page);
        return $this->behaviour->render($options, $number, $qoutput, $qtoutput);
    }

    /**
     * Generate any bits of HTML that needs to go in the <head> tag when this question
     * attempt is displayed in the body.
     * @return string HTML fragment.
     */
    public function render_head_html($page = null) {
        if (is_null($page)) {
            global $PAGE;
            $page = $PAGE;
        }
        // TODO go via behaviour.
        return $this->question->get_renderer($page)->head_code($this) .
                $this->behaviour->get_renderer($page)->head_code($this);
    }

    /**
     * Like {@link render_question()} but displays the question at the past step
     * indicated by $seq, rather than showing the latest step.
     *
     * @param int $seq the seq number of the past state to display.
     * @param question_display_options $options controls how the question is rendered.
     * @param string|null $number The question number to display. 'i' is a special
     *      value that gets displayed as Information. Null means no number is displayed.
     * @return string HTML fragment representing the question.
     */
    public function render_at_step($seq, $options, $number, $preferredbehaviour) {
        $restrictedqa = new question_attempt_with_restricted_history($this, $seq, $preferredbehaviour);
        return $restrictedqa->render($options, $number);
    }

    /**
     * Checks whether the users is allow to be served a particular file.
     * @param question_display_options $options the options that control display of the question.
     * @param string $component the name of the component we are serving files for.
     * @param string $filearea the name of the file area.
     * @param array $args the remaining bits of the file path.
     * @param bool $forcedownload whether the user must be forced to download the file.
     * @return bool true if the user can access this file.
     */
    public function check_file_access($options, $component, $filearea, $args, $forcedownload) {
        return $this->behaviour->check_file_access($options, $component, $filearea, $args, $forcedownload);
    }

    /**
     * Add a step to this question attempt.
     * @param question_attempt_step $step the new step.
     */
    protected function add_step(question_attempt_step $step) {
        $this->steps[] = $step;
        end($this->steps);
        $this->observer->notify_step_added($step, $this, key($this->steps));
    }

    /**
     * Start this question attempt.
     *
     * You should not call this method directly. Call
     * {@link question_usage_by_activity::start_question()} instead.
     *
     * @param string|question_behaviour $preferredbehaviour the name of the
     *      desired archetypal behaviour, or an actual model instance.
     * @param array $submitteddata optional, used when re-starting to keep the same initial state.
     * @param int $timestamp optional, the timstamp to record for this action. Defaults to now.
     * @param int $userid optional, the user to attribute this action to. Defaults to the current user.
     */
    public function start($preferredbehaviour, $submitteddata = array(), $timestamp = null, $userid = null) {
        // Initialise the behaviour.
        if (is_string($preferredbehaviour)) {
            $this->behaviour =
                    $this->question->make_behaviour($this, $preferredbehaviour);
        } else {
            $class = get_class($preferredbehaviour);
            $this->behaviour = new $class($this, $preferredbehaviour);
        }

        // Record the minimum fraction.
        $this->minfraction = $this->behaviour->get_min_fraction();

        // Initialise the first step.
        $firststep = new question_attempt_step($submitteddata, $timestamp, $userid);
        $firststep->set_state(question_state::$todo);
        if ($submitteddata) {
            $this->question->apply_attempt_state($firststep);
        } else {
            $this->behaviour->init_first_step($firststep);
        }
        $this->add_step($firststep);

        // Record questionline and correct answer.
        $this->questionsummary = $this->behaviour->get_question_summary();
        $this->rightanswer = $this->behaviour->get_right_answer_summary();
    }

    /**
     * Start this question attempt, starting from the point that the previous
     * attempt $oldqa had reached.
     *
     * You should not call this method directly. Call
     * {@link question_usage_by_activity::start_question_based_on()} instead.
     *
     * @param question_attempt $oldqa a previous attempt at this quetsion that
     *      defines the starting point.
     */
    public function start_based_on(question_attempt $oldqa) {
        $this->start($oldqa->behaviour, $oldqa->get_resume_data());
    }

    /**
     * Used by {@link start_based_on()} to get the data needed to start a new
     * attempt from the point this attempt has go to.
     * @return array name => value pairs.
     */
    protected function get_resume_data() {
        return $this->behaviour->get_resume_data();
    }

    /**
     * Get a particular parameter from the current request. A wrapper round
     * {@link optional_param()}, except that the results is returned without
     * slashes.
     * @param string $name the paramter name.
     * @param int $type one of the standard PARAM_... constants, or one of the
     *      special extra constands defined by this class.
     * @param array $postdata (optional, only inteded for testing use) take the
     *      data from this array, instead of from $_POST.
     * @return mixed the requested value.
     */
    public function get_submitted_var($name, $type, $postdata = null) {
        switch ($type) {
            case self::PARAM_MARK:
                // Special case to work around PARAM_NUMBER converting '' to 0.
                $mark = $this->get_submitted_var($name, PARAM_RAW_TRIMMED, $postdata);
                if ($mark === '') {
                    return $mark;
                } else {
                    return $this->get_submitted_var($name, PARAM_NUMBER, $postdata);
                }

            case self::PARAM_FILES:
                return $this->process_response_files($name, $name, $postdata);

            case self::PARAM_CLEANHTML_FILES:
                $var = $this->get_submitted_var($name, PARAM_CLEANHTML, $postdata);
                return $this->process_response_files($name, $name . ':itemid', $postdata, $var);

            default:
                if (is_null($postdata)) {
                    $var = optional_param($name, null, $type);
                } else if (array_key_exists($name, $postdata)) {
                    $var = clean_param($postdata[$name], $type);
                } else {
                    $var = null;
                }

                return $var;
        }
    }

    /**
     * Handle a submitted variable representing uploaded files.
     * @param string $name the field name.
     * @param string $draftidname the field name holding the draft file area id.
     * @param array $postdata (optional, only inteded for testing use) take the
     *      data from this array, instead of from $_POST. At the moment, this
     *      behaves as if there were no files.
     * @param string $text optional reponse text.
     * @return question_file_saver that can be used to save the files later.
     */
    protected function process_response_files($name, $draftidname, $postdata = null, $text = null) {
        if ($postdata) {
            // There can be no files with test data (at the moment).
            return null;
        }

        $draftitemid = file_get_submitted_draft_itemid($draftidname);
        if (!$draftitemid) {
            return null;
        }

        return new question_file_saver($draftitemid, 'question', 'response_' .
                str_replace($this->get_field_prefix(), '', $name), $text);
    }

    /**
     * Get any data from the request that matches the list of expected params.
     * @param array $expected variable name => PARAM_... constant.
     * @param string $extraprefix '-' or ''.
     * @return array name => value.
     */
    protected function get_expected_data($expected, $postdata, $extraprefix) {
        $submitteddata = array();
        foreach ($expected as $name => $type) {
            $value = $this->get_submitted_var(
                    $this->get_field_prefix() . $extraprefix . $name, $type, $postdata);
            if (!is_null($value)) {
                $submitteddata[$extraprefix . $name] = $value;
            }
        }
        return $submitteddata;
    }

    /**
     * Get all the submitted question type data for this question, whithout checking
     * that it is valid or cleaning it in any way.
     * @return array name => value.
     */
    protected function get_all_submitted_qt_vars($postdata) {
        if (is_null($postdata)) {
            $postdata = $_POST;
        }

        $pattern = '/^' . preg_quote($this->get_field_prefix()) . '[^-:]/';
        $prefixlen = strlen($this->get_field_prefix());

        $submitteddata = array();
        foreach ($_POST as $name => $value) {
            if (preg_match($pattern, $name)) {
                $submitteddata[substr($name, $prefixlen)] = $value;
            }
        }

        return $submitteddata;
    }

    /**
     * Get all the sumbitted data belonging to this question attempt from the
     * current request.
     * @param array $postdata (optional, only inteded for testing use) take the
     *      data from this array, instead of from $_POST.
     * @return array name => value pairs that could be passed to {@link process_action()}.
     */
    public function get_submitted_data($postdata = null) {
        $submitteddata = $this->get_expected_data(
                $this->behaviour->get_expected_data(), $postdata, '-');

        $expected = $this->behaviour->get_expected_qt_data();
        if ($expected === self::USE_RAW_DATA) {
            $submitteddata += $this->get_all_submitted_qt_vars($postdata);
        } else {
            $submitteddata += $this->get_expected_data($expected, $postdata, '');
        }
        return $submitteddata;
    }

    /**
     * Get a set of response data for this question attempt that would get the
     * best possible mark.
     * @return array name => value pairs that could be passed to {@link process_action()}.
     */
    public function get_correct_response() {
        $response = $this->question->get_correct_response();
        $imvars = $this->behaviour->get_correct_response();
        foreach ($imvars as $name => $value) {
            $response['-' . $name] = $value;
        }
        return $response;
    }

    /**
     * Change the quetsion summary. Note, that this is almost never necessary.
     * This method was only added to work around a limitation of the Opaque
     * protocol, which only sends questionLine at the end of an attempt.
     * @param $questionsummary the new summary to set.
     */
    public function set_question_summary($questionsummary) {
        $this->questionsummary = $questionsummary;
        $this->observer->notify_attempt_modified($this);
    }

    /**
     * @return string a simple textual summary of the question that was asked.
     */
    public function get_question_summary() {
        return $this->questionsummary;
    }

    /**
     * @return string a simple textual summary of response given.
     */
    public function get_response_summary() {
        return $this->responsesummary;
    }

    /**
     * @return string a simple textual summary of the correct resonse.
     */
    public function get_right_answer_summary() {
        return $this->rightanswer;
    }

    /**
     * Perform the action described by $submitteddata.
     * @param array $submitteddata the submitted data the determines the action.
     * @param int $timestamp the time to record for the action. (If not given, use now.)
     * @param int $userid the user to attribute the aciton to. (If not given, use the current user.)
     */
    public function process_action($submitteddata, $timestamp = null, $userid = null) {
        $pendingstep = new question_attempt_pending_step($submitteddata, $timestamp, $userid);
        if ($this->behaviour->process_action($pendingstep) == self::KEEP) {
            $this->add_step($pendingstep);
            if ($pendingstep->response_summary_changed()) {
                $this->responsesummary = $pendingstep->get_new_response_summary();
            }
        }
    }

    /**
     * Perform a finish action on this question attempt. This corresponds to an
     * external finish action, for example the user pressing Submit all and finish
     * in the quiz, rather than using one of the controls that is part of the
     * question.
     *
     * @param int $timestamp the time to record for the action. (If not given, use now.)
     * @param int $userid the user to attribute the aciton to. (If not given, use the current user.)
     */
    public function finish($timestamp = null, $userid = null) {
        $this->process_action(array('-finish' => 1), $timestamp, $userid);
    }

    /**
     * Perform a regrade. This replays all the actions from $oldqa into this
     * attempt.
     * @param question_attempt $oldqa the attempt to regrade.
     * @param bool $finished whether the question attempt should be forced to be finished
     *      after the regrade, or whether it may still be in progress (default false).
     */
    public function regrade(question_attempt $oldqa, $finished) {
        $first = true;
        foreach ($oldqa->get_step_iterator() as $step) {
            if ($first) {
                $first = false;
                $this->start($oldqa->behaviour, $step->get_all_data(),
                        $step->get_timecreated(), $step->get_user_id());
            } else {
                $this->process_action($step->get_submitted_data(),
                        $step->get_timecreated(), $step->get_user_id());
            }
        }
        if ($finished) {
            $this->finish();
        }
    }

    /**
     * Perform a manual grading action on this attempt.
     * @param $comment the comment being added.
     * @param $mark the new mark. (Optional, if not given, then only a comment is added.)
     * @param int $timestamp the time to record for the action. (If not given, use now.)
     * @param int $userid the user to attribute the aciton to. (If not given, use the current user.)
     * @return unknown_type
     */
    public function manual_grade($comment, $mark, $timestamp = null, $userid = null) {
        $submitteddata = array('-comment' => $comment);
        if (!is_null($mark)) {
            $submitteddata['-mark'] = $mark;
            $submitteddata['-maxmark'] = $this->maxmark;
        }
        $this->process_action($submitteddata, $timestamp, $userid);
    }

    /** @return bool Whether this question attempt has had a manual comment added. */
    public function has_manual_comment() {
        foreach ($this->steps as $step) {
            if ($step->has_behaviour_var('comment')) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array(string, int) the most recent manual comment that was added
     * to this question, and the FORMAT_... it is.
     */
    public function get_manual_comment() {
        foreach ($this->get_reverse_step_iterator() as $step) {
            if ($step->has_behaviour_var('comment')) {
                return array($step->get_behaviour_var('comment'),
                        $step->get_behaviour_var('commentformat'));
            }
        }
        return array(null, null);
    }

    /**
     * @return array subpartid => object with fields
     *      ->responseclassid matches one of the values returned from quetion_type::get_possible_responses.
     *      ->response the actual response the student gave to this part, as a string.
     *      ->fraction the credit awarded for this subpart, may be null.
     *      returns an empty array if no analysis is possible.
     */
    public function classify_response() {
        return $this->behaviour->classify_response();
    }

    /**
     * Create a question_attempt_step from records loaded from the database.
     *
     * For internal use only.
     *
     * @param array $records Raw records loaded from the database.
     * @param int $questionattemptid The id of the question_attempt to extract.
     * @return question_attempt The newly constructed question_attempt_step.
     */
    public static function load_from_records(&$records, $questionattemptid,
            question_usage_observer $observer, $preferredbehaviour) {
        $record = current($records);
        while ($record->questionattemptid != $questionattemptid) {
            $record = next($records);
            if (!$record) {
                throw new coding_exception("Question attempt $questionattemptid not found in the database.");
            }
        }

        try {
            $question = question_bank::load_question($record->questionid);
        } catch (Exception $e) {
            // The question must have been deleted somehow. Create a missing
            // question to use in its place.
            $question = question_bank::get_qtype('missingtype')->make_deleted_instance(
                    $record->questionid, $record->maxmark + 0);
        }

        $qa = new question_attempt($question, $record->questionusageid,
                null, $record->maxmark + 0);
        $qa->set_database_id($record->questionattemptid);
        $qa->set_number_in_usage($record->slot);
        $qa->minfraction = $record->minfraction + 0;
        $qa->set_flagged($record->flagged);
        $qa->questionsummary = $record->questionsummary;
        $qa->rightanswer = $record->rightanswer;
        $qa->responsesummary = $record->responsesummary;
        $qa->timemodified = $record->timemodified;

        $qa->behaviour = question_engine::make_behaviour(
                $record->behaviour, $qa, $preferredbehaviour);

        $i = 0;
        while ($record && $record->questionattemptid == $questionattemptid && !is_null($record->attemptstepid)) {
            $qa->steps[$i] = question_attempt_step::load_from_records($records, $record->attemptstepid);
            if ($i == 0) {
                $question->apply_attempt_state($qa->steps[0]);
            }
            $i++;
            $record = current($records);
        }

        $qa->observer = $observer;

        return $qa;
    }
}


/**
 * This subclass of question_attempt pretends that only part of the step history
 * exists. It is used for rendering the question in past states.
 *
 * All methods that try to modify the question_attempt throw exceptions.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_attempt_with_restricted_history extends question_attempt {
    /**
     * @var question_attempt the underlying question_attempt.
     */
    protected $baseqa;

    /**
     * Create a question_attempt_with_restricted_history
     * @param question_attempt $baseqa The question_attempt to make a restricted version of.
     * @param int $lastseq the index of the last step to include.
     * @param string $preferredbehaviour the preferred behaviour. It is slightly
     *      annoyting that this needs to be passed, but unavoidable for now.
     */
    public function __construct(question_attempt $baseqa, $lastseq, $preferredbehaviour) {
        if ($lastseq < 0 || $lastseq >= $baseqa->get_num_steps()) {
            throw new coding_exception('$seq out of range', $seq);
        }

        $this->baseqa = $baseqa;
        $this->steps = array_slice($baseqa->steps, 0, $lastseq + 1);
        $this->observer = new question_usage_null_observer();

        // This should be a straight copy of all the remaining fields.
        $this->id = $baseqa->id;
        $this->usageid = $baseqa->usageid;
        $this->slot = $baseqa->slot;
        $this->question = $baseqa->question;
        $this->maxmark = $baseqa->maxmark;
        $this->minfraction = $baseqa->minfraction;
        $this->questionsummary = $baseqa->questionsummary;
        $this->responsesummary = $baseqa->responsesummary;
        $this->rightanswer = $baseqa->rightanswer;
        $this->flagged = $baseqa->flagged;

        // Except behaviour, where we need to create a new one.
        $this->behaviour = question_engine::make_behaviour(
                $baseqa->get_behaviour_name(), $this, $preferredbehaviour);
    }

    public function get_full_step_iterator() {
        return $this->baseqa->get_step_iterator();
    }

    protected function add_step(question_attempt_step $step) {
        coding_exception('Cannot modify a question_attempt_with_restricted_history.');
    }
    public function process_action($submitteddata, $timestamp = null, $userid = null) {
        coding_exception('Cannot modify a question_attempt_with_restricted_history.');
    }
    public function start($preferredbehaviour, $submitteddata = array(), $timestamp = null, $userid = null) {
        coding_exception('Cannot modify a question_attempt_with_restricted_history.');
    }

    public function set_database_id($id) {
        coding_exception('Cannot modify a question_attempt_with_restricted_history.');
    }
    public function set_flagged($flagged) {
        coding_exception('Cannot modify a question_attempt_with_restricted_history.');
    }
    public function set_number_in_usage($slot) {
        coding_exception('Cannot modify a question_attempt_with_restricted_history.');
    }
    public function set_question_summary($questionsummary) {
        coding_exception('Cannot modify a question_attempt_with_restricted_history.');
    }
    public function set_usage_id($usageid) {
        coding_exception('Cannot modify a question_attempt_with_restricted_history.');
    }
}


/**
 * A class abstracting access to the {@link question_attempt::$states} array.
 *
 * This is actively linked to question_attempt. If you add an new step
 * mid-iteration, then it will be included.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_attempt_step_iterator implements Iterator, ArrayAccess {
    /** @var question_attempt the question_attempt being iterated over. */
    protected $qa;
    /** @var integer records the current position in the iteration. */
    protected $i;

    /**
     * Do not call this constructor directly.
     * Use {@link question_attempt::get_step_iterator()}.
     * @param question_attempt $qa the attempt to iterate over.
     */
    public function __construct(question_attempt $qa) {
        $this->qa = $qa;
        $this->rewind();
    }

    /** @return question_attempt_step */
    public function current() {
        return $this->offsetGet($this->i);
    }
    /** @return int */
    public function key() {
        return $this->i;
    }
    public function next() {
        ++$this->i;
    }
    public function rewind() {
        $this->i = 0;
    }
    /** @return bool */
    public function valid() {
        return $this->offsetExists($this->i);
    }

    /** @return bool */
    public function offsetExists($i) {
        return $i >= 0 && $i < $this->qa->get_num_steps();
    }
    /** @return question_attempt_step */
    public function offsetGet($i) {
        return $this->qa->get_step($i);
    }
    public function offsetSet($offset, $value) {
        throw new coding_exception('You are only allowed read-only access to question_attempt::states through a question_attempt_step_iterator. Cannot set.');
    }
    public function offsetUnset($offset) {
        throw new coding_exception('You are only allowed read-only access to question_attempt::states through a question_attempt_step_iterator. Cannot unset.');
    }
}


/**
 * A variant of {@link question_attempt_step_iterator} that iterates through the
 * steps in reverse order.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_attempt_reverse_step_iterator extends question_attempt_step_iterator {
    public function next() {
        --$this->i;
    }

    public function rewind() {
        $this->i = $this->qa->get_num_steps() - 1;
    }
}


/**
 * Stores one step in a {@link question_attempt}.
 *
 * The most important attributes of a step are the state, which is one of the
 * {@link question_state} constants, the fraction, which may be null, or a
 * number bewteen the attempt's minfraction and 1.0, and the array of submitted
 * data, about which more later.
 *
 * A step also tracks the time it was created, and the user responsible for
 * creating it.
 *
 * The submitted data is basically just an array of name => value pairs, with
 * certain conventions about the to divide the variables into four = two times two
 * categories.
 *
 * Variables may either belong to the behaviour, in which case the
 * name starts with a '-', or they may belong to the question type in which case
 * they name does not start with a '-'.
 *
 * Second, variables may either be ones that came form the original request, in
 * which case the name does not start with an _, or they are cached values that
 * were created during processing, in which case the name does start with an _.
 *
 * That is, each name will start with one of '', '_'. '-' or '-_'. The remainder
 * of the name should match the regex [a-z][a-z0-9]*.
 *
 * These variables can be accessed with {@link get_behaviour_var()} and {@link get_qt_var()},
 * - to be clear, ->get_behaviour_var('x') gets the variable with name '-x' -
 * and values whose names start with '_' can be set using {@link set_behaviour_var()}
 * and {@link set_qt_var()}. There are some other methods like {@link has_behaviour_var()}
 * to check wether a varaible with a particular name is set, and {@link get_behaviour_data()}
 * to get all the behaviour data as an associative array.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_attempt_step {
    /** @var integer if this attempts is stored in the question_attempts table, the id of that row. */
    private $id = null;

    /** @var question_state one of the {@link question_state} constants. The state after this step. */
    private $state;

    /** @var null|number the fraction (grade on a scale of minfraction .. 1.0) or null. */
    private $fraction = null;

    /** @var integer the timestamp when this step was created. */
    private $timecreated;

    /** @var integer the id of the user resonsible for creating this step. */
    private $userid;

    /** @var array name => value pairs. The submitted data. */
    private $data;

    /** @var array name => array of {@link stored_file}s. Caches the contents of file areas. */
    private $files = array();

    /**
     * You should not need to call this constructor in your own code. Steps are
     * normally created by {@link question_attempt} methods like
     * {@link question_attempt::process_action()}.
     * @param array $data the submitted data that defines this step.
     * @param int $timestamp the time to record for the action. (If not given, use now.)
     * @param int $userid the user to attribute the aciton to. (If not given, use the current user.)
     */
    public function __construct($data = array(), $timecreated = null, $userid = null) {
        global $USER;
        $this->state = question_state::$unprocessed;
        $this->data = $data;
        if (is_null($timecreated)) {
            $this->timecreated = time();
        } else {
            $this->timecreated = $timecreated;
        }
        if (is_null($userid)) {
            $this->userid = $USER->id;
        } else {
            $this->userid = $userid;
        }
    }

    /** @return question_state The state after this step. */
    public function get_state() {
        return $this->state;
    }

    /**
     * Set the state. Normally only called by behaviours.
     * @param question_state $state one of the {@link question_state} constants.
     */
    public function set_state($state) {
        $this->state = $state;
    }

    /**
     * @return null|number the fraction (grade on a scale of minfraction .. 1.0)
     * or null if this step has not been marked.
     */
    public function get_fraction() {
        return $this->fraction;
    }

    /**
     * Set the fraction. Normally only called by behaviours.
     * @param null|number $fraction the fraction to set.
     */
    public function set_fraction($fraction) {
        $this->fraction = $fraction;
    }

    /** @return int the id of the user resonsible for creating this step. */
    public function get_user_id() {
        return $this->userid;
    }

    /** @return int the timestamp when this step was created. */
    public function get_timecreated() {
        return $this->timecreated;
    }

    /**
     * @param string $name the name of a question type variable to look for in the submitted data.
     * @return bool whether a variable with this name exists in the question type data.
     */
    public function has_qt_var($name) {
        return array_key_exists($name, $this->data);
    }

    /**
     * @param string $name the name of a question type variable to look for in the submitted data.
     * @return string the requested variable, or null if the variable is not set.
     */
    public function get_qt_var($name) {
        if (!$this->has_qt_var($name)) {
            return null;
        }
        return $this->data[$name];
    }

    /**
     * Set a cached question type variable.
     * @param string $name the name of the variable to set. Must match _[a-z][a-z0-9]*.
     * @param string $value the value to set.
     */
    public function set_qt_var($name, $value) {
        if ($name[0] != '_') {
            throw new coding_exception('Cannot set question type data ' . $name . ' on an attempt step. You can only set variables with names begining with _.');
        }
        $this->data[$name] = $value;
    }

    /**
     * Get the latest set of files for a particular question type variable of
     * type question_attempt::PARAM_FILES.
     *
     * @param string $name the name of the associated variable.
     * @return array of {@link stored_files}.
     */
    public function get_qt_files($name, $contextid) {
        if (array_key_exists($name, $this->files)) {
            return $this->files[$name];
        }

        if (!$this->has_qt_var($name)) {
            $this->files[$name] = array();
            return array();
        }

        $fs = get_file_storage();
        $this->files[$name] = $fs->get_area_files($contextid, 'question',
                'response_' . $name, $this->id, 'sortorder', false);

        return $this->files[$name];
    }

    /**
     * Prepare a draft file are for the files belonging the a response variable
     * of this step.
     *
     * @param string $name the variable name the files belong to.
     * @param int $contextid the id of the context the quba belongs to.
     * @return int the draft itemid.
     */
    public function prepare_response_files_draft_itemid($name, $contextid) {
        list($draftid, $notused) = $this->prepare_response_files_draft_itemid_with_text(
                $name, $contextid, null);
        return $draftid;
    }

    /**
     * Prepare a draft file are for the files belonging the a response variable
     * of this step, while rewriting the URLs in some text.
     *
     * @param string $name the variable name the files belong to.
     * @param int $contextid the id of the context the quba belongs to.
     * @param string $text the text to update the URLs in.
     * @return array(int, string) the draft itemid and the text with URLs rewritten.
     */
    public function prepare_response_files_draft_itemid_with_text($name, $contextid, $text) {
        $draftid = 0; // Will be filled in by file_prepare_draft_area.
        $newtext = file_prepare_draft_area($draftid, $contextid, 'question',
                'response_' . $name, $this->id, null, $text);
        return array($draftid, $newtext);
    }

    /**
     * Rewrite the @@PLUGINFILE@@ tokens in a response variable from this step
     * that contains links to file. Normally you should probably call
     * {@link question_attempt::rewrite_response_pluginfile_urls()} instead of
     * calling this method directly.
     *
     * @param string $text the text to update the URLs in.
     * @param int $contextid the id of the context the quba belongs to.
     * @param string $name the variable name the files belong to.
     * @param array $extra extra file path components.
     * @return string the rewritten text.
     */
    public function rewrite_response_pluginfile_urls($text, $contextid, $name, $extras) {
        return question_rewrite_question_urls($text, 'pluginfile.php', $contextid,
                'question', 'response_' . $name, $extras, $this->id);
    }

    /**
     * Get all the question type variables.
     * @param array name => value pairs.
     */
    public function get_qt_data() {
        $result = array();
        foreach ($this->data as $name => $value) {
            if ($name[0] != '-' && $name[0] != ':') {
                $result[$name] = $value;
            }
        }
        return $result;
    }

    /**
     * @param string $name the name of an behaviour variable to look for in the submitted data.
     * @return bool whether a variable with this name exists in the question type data.
     */
    public function has_behaviour_var($name) {
        return array_key_exists('-' . $name, $this->data);
    }

    /**
     * @param string $name the name of an behaviour variable to look for in the submitted data.
     * @return string the requested variable, or null if the variable is not set.
     */
    public function get_behaviour_var($name) {
        if (!$this->has_behaviour_var($name)) {
            return null;
        }
        return $this->data['-' . $name];
    }

    /**
     * Set a cached behaviour variable.
     * @param string $name the name of the variable to set. Must match _[a-z][a-z0-9]*.
     * @param string $value the value to set.
     */
    public function set_behaviour_var($name, $value) {
        if ($name[0] != '_') {
            throw new coding_exception('Cannot set question type data ' . $name . ' on an attempt step. You can only set variables with names begining with _.');
        }
        return $this->data['-' . $name] = $value;
    }

    /**
     * Get all the behaviour variables.
     * @param array name => value pairs.
     */
    public function get_behaviour_data() {
        $result = array();
        foreach ($this->data as $name => $value) {
            if ($name[0] == '-') {
                $result[substr($name, 1)] = $value;
            }
        }
        return $result;
    }

    /**
     * Get all the submitted data, but not the cached data. behaviour
     * variables have the - at the start of their name. This is only really
     * intended for use by {@link question_attempt::regrade()}, it should not
     * be considered part of the public API.
     * @param array name => value pairs.
     */
    public function get_submitted_data() {
        $result = array();
        foreach ($this->data as $name => $value) {
            if ($name[0] == '_' || ($name[0] == '-' && $name[1] == '_')) {
                continue;
            }
            $result[$name] = $value;
        }
        return $result;
    }

    /**
     * Get all the data. behaviour variables have the - at the start of
     * their name. This is only intended for internal use, for example by
     * {@link question_engine_data_mapper::insert_question_attempt_step()},
     * however, it can ocasionally be useful in test code. It should not be
     * considered part of the public API of this class.
     * @param array name => value pairs.
     */
    public function get_all_data() {
        return $this->data;
    }

    /**
     * Create a question_attempt_step from records loaded from the database.
     * @param array $records Raw records loaded from the database.
     * @param int $stepid The id of the records to extract.
     * @return question_attempt_step The newly constructed question_attempt_step.
     */
    public static function load_from_records(&$records, $attemptstepid) {
        $currentrec = current($records);
        while ($currentrec->attemptstepid != $attemptstepid) {
            $currentrec = next($records);
            if (!$currentrec) {
                throw new coding_exception("Question attempt step $attemptstepid not found in the database.");
            }
        }

        $record = $currentrec;
        $data = array();
        while ($currentrec && $currentrec->attemptstepid == $attemptstepid) {
            if ($currentrec->name) {
                $data[$currentrec->name] = $currentrec->value;
            }
            $currentrec = next($records);
        }

        $step = new question_attempt_step_read_only($data, $record->timecreated, $record->userid);
        $step->state = question_state::get($record->state);
        $step->id = $record->attemptstepid;
        if (!is_null($record->fraction)) {
            $step->fraction = $record->fraction + 0;
        }
        return $step;
    }
}


/**
 * A subclass with a bit of additional funcitonality, for pending steps.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_attempt_pending_step extends question_attempt_step {
    /** @var string . */
    protected $newresponsesummary = null;

    /**
     * If as a result of processing this step, the response summary for the
     * question attempt should changed, you should call this method to set the
     * new summary.
     * @param string $responsesummary the new response summary.
     */
    public function set_new_response_summary($responsesummary) {
        $this->newresponsesummary = $responsesummary;
    }

    /** @return string the new response summary, if any. */
    public function get_new_response_summary() {
        return $this->newresponsesummary;
    }

    /** @return string whether this step changes the response summary. */
    public function response_summary_changed() {
        return !is_null($this->newresponsesummary);
    }
}


/**
 * A subclass of {@link question_attempt_step} that cannot be modified.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_attempt_step_read_only extends question_attempt_step {
    public function set_state($state) {
        throw new coding_exception('Cannot modify a question_attempt_step_read_only.');
    }
    public function set_fraction($fraction) {
        throw new coding_exception('Cannot modify a question_attempt_step_read_only.');
    }
    public function set_qt_var($name, $value) {
        throw new coding_exception('Cannot modify a question_attempt_step_read_only.');
    }
    public function set_behaviour_var($name, $value) {
        throw new coding_exception('Cannot modify a question_attempt_step_read_only.');
    }
}


/**
 * A null {@link question_attempt_step} returned from
 * {@link question_attempt::get_last_step()} etc. when a an attempt has just been
 * created and there is no acutal step.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_null_step {
    public function get_state() {
        return question_state::$notstarted;
    }

    public function set_state($state) {
        throw new coding_exception('This question has not been started.');
    }

    public function get_fraction() {
        return null;
    }
}


/**
 * Interface for things that want to be notified of signficant changes to a
 * {@link question_usage_by_activity}.
 *
 * A question behaviour controls the flow of actions a student can
 * take as they work through a question, and later, as a teacher manually grades it.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface question_usage_observer {
    /** Called when a field of the question_usage_by_activity is changed. */
    public function notify_modified();

    /**
     * Called when the fields of a question attempt in this usage are modified.
     * @param question_attempt $qa the newly added question attempt.
     */
    public function notify_attempt_modified(question_attempt $qa);

    /**
     * Called when a new question attempt is added to this usage.
     * @param question_attempt $qa the newly added question attempt.
     */
    public function notify_attempt_added(question_attempt $qa);

    /**
     * Called we want to delete the old step records for an attempt, prior to
     * inserting newones. This is used by regrading.
     * @param question_attempt $qa the question attempt to delete the steps for.
     */
    public function notify_delete_attempt_steps(question_attempt $qa);

    /**
     * Called when a new step is added to a question attempt in this usage.
     * @param $step the new step.
     * @param $qa the usage it is being added to.
     * @param $seq the sequence number of the new step.
     */
    public function notify_step_added(question_attempt_step $step, question_attempt $qa, $seq);
}


/**
 * Null implmentation of the {@link question_usage_watcher} interface.
 * Does nothing.
 *
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_usage_null_observer implements question_usage_observer {
    public function notify_modified() {
    }
    public function notify_attempt_modified(question_attempt $qa) {
    }
    public function notify_attempt_added(question_attempt $qa) {
    }
    public function notify_delete_attempt_steps(question_attempt $qa) {
    }
    public function notify_step_added(question_attempt_step $step, question_attempt $qa, $seq) {
    }
}


/**
 * Useful functions for writing question types and behaviours.
 *
 * @copyright 2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class question_utils {
    /**
     * Tests to see whether two arrays have the same keys, with the same values
     * (as compared by ===) for each key. However, the order of the arrays does
     * not have to be the same.
     * @param array $array1 the first array.
     * @param array $array2 the second array.
     * @return bool whether the two arrays have the same keys with the same
     *      corresponding values.
     */
    public static function arrays_have_same_keys_and_values(array $array1, array $array2) {
        if (count($array1) != count($array2)) {
            return false;
        }
        foreach ($array1 as $key => $value1) {
            if (!array_key_exists($key, $array2)) {
                return false;
            }
            if (((string) $value1) !== ((string) $array2[$key])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Tests to see whether two arrays have the same value at a particular key.
     * This method will return true if:
     * 1. Neither array contains the key; or
     * 2. Both arrays contain the key, and the corresponding values compare
     *      identical when cast to strings and compared with ===.
     * @param array $array1 the first array.
     * @param array $array2 the second array.
     * @param string $key an array key.
     * @return bool whether the two arrays have the same value (or lack of
     *      one) for a given key.
     */
    public static function arrays_same_at_key(array $array1, array $array2, $key) {
        if (array_key_exists($key, $array1) && array_key_exists($key, $array2)) {
            return ((string) $array1[$key]) === ((string) $array2[$key]);
        }
        if (!array_key_exists($key, $array1) && !array_key_exists($key, $array2)) {
            return true;
        }
        return false;
    }

    /**
     * Tests to see whether two arrays have the same value at a particular key.
     * Missing values are replaced by '', and then the values are cast to
     * strings and compared with ===.
     * @param array $array1 the first array.
     * @param array $array2 the second array.
     * @param string $key an array key.
     * @return bool whether the two arrays have the same value (or lack of
     *      one) for a given key.
     */
    public static function arrays_same_at_key_missing_is_blank(
            array $array1, array $array2, $key) {
        if (array_key_exists($key, $array1)) {
            $value1 = $array1[$key];
        } else {
            $value1 = '';
        }
        if (array_key_exists($key, $array2)) {
            $value2 = $array2[$key];
        } else {
            $value2 = '';
        }
        return ((string) $value1) === ((string) $value2);
    }

    /**
     * Tests to see whether two arrays have the same value at a particular key.
     * Missing values are replaced by 0, and then the values are cast to
     * integers and compared with ===.
     * @param array $array1 the first array.
     * @param array $array2 the second array.
     * @param string $key an array key.
     * @return bool whether the two arrays have the same value (or lack of
     *      one) for a given key.
     */
    public static function arrays_same_at_key_integer(
            array $array1, array $array2, $key) {
        if (array_key_exists($key, $array1)) {
            $value1 = $array1[$key];
        } else {
            $value1 = 0;
        }
        if (array_key_exists($key, $array2)) {
            $value2 = $array2[$key];
        } else {
            $value2 = 0;
        }
        return ((integer) $value1) === ((integer) $value2);
    }

    private static $units = array('', 'i', 'ii', 'iii', 'iv', 'v', 'vi', 'vii', 'viii', 'ix');
    private static $tens = array('', 'x', 'xx', 'xxx', 'xl', 'l', 'lx', 'lxx', 'lxxx', 'xc');
    private static $hundreds = array('', 'c', 'cc', 'ccc', 'cd', 'd', 'dc', 'dcc', 'dccc', 'cm');
    private static $thousands = array('', 'm', 'mm', 'mmm');

    /**
     * Convert an integer to roman numerals.
     * @param int $number an integer between 1 and 3999 inclusive. Anything else will throw an exception.
     * @return string the number converted to lower case roman numerals.
     */
    public static function int_to_roman($number) {
        if (!is_integer($number) || $number < 1 || $number > 3999) {
            throw new coding_exception('Only integers between 0 and 3999 can be ' .
                    'converted to roman numerals.', $number);
        }

        return self::$thousands[$number / 1000 % 10] . self::$hundreds[$number / 100 % 10] .
                self::$tens[$number / 10 % 10] . self::$units[$number % 10];
    }
}