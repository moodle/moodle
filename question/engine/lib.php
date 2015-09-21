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

require_once($CFG->libdir . '/filelib.php');
require_once(dirname(__FILE__) . '/questionusage.php');
require_once(dirname(__FILE__) . '/questionattempt.php');
require_once(dirname(__FILE__) . '/questionattemptstep.php');
require_once(dirname(__FILE__) . '/states.php');
require_once(dirname(__FILE__) . '/datalib.php');
require_once(dirname(__FILE__) . '/renderer.php');
require_once(dirname(__FILE__) . '/bank.php');
require_once(dirname(__FILE__) . '/../type/questiontypebase.php');
require_once(dirname(__FILE__) . '/../type/questionbase.php');
require_once(dirname(__FILE__) . '/../type/rendererbase.php');
require_once(dirname(__FILE__) . '/../behaviour/behaviourtypebase.php');
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

    /** @var array behaviour name => question_behaviour_type for this behaviour. */
    private static $behaviourtypes = array();

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
     * @param moodle_database $db a database connectoin. Defaults to global $DB.
     * @return question_usage_by_activity loaded from the database.
     */
    public static function load_questions_usage_by_activity($qubaid, moodle_database $db = null) {
        $dm = new question_engine_data_mapper($db);
        return $dm->load_questions_usage_by_activity($qubaid);
    }

    /**
     * Save a {@link question_usage_by_activity} to the database. This works either
     * if the usage was newly created by {@link make_questions_usage_by_activity()}
     * or loaded from the database using {@link load_questions_usage_by_activity()}
     * @param question_usage_by_activity the usage to save.
     * @param moodle_database $db a database connectoin. Defaults to global $DB.
     */
    public static function save_questions_usage_by_activity(question_usage_by_activity $quba, moodle_database $db = null) {
        $dm = new question_engine_data_mapper($db);
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
     * Validate that the manual grade submitted for a particular question is in range.
     * @param int $qubaid the question_usage id.
     * @param int $slot the slot number within the usage.
     * @return bool whether the submitted data is in range.
     */
    public static function is_manual_grade_in_range($qubaid, $slot) {
        $prefix = 'q' . $qubaid . ':' . $slot . '_';
        $mark = question_utils::optional_param_mark($prefix . '-mark');
        $maxmark = optional_param($prefix . '-maxmark', null, PARAM_FLOAT);
        $minfraction = optional_param($prefix . ':minfraction', null, PARAM_FLOAT);
        $maxfraction = optional_param($prefix . ':maxfraction', null, PARAM_FLOAT);
        return $mark === '' ||
                ($mark !== null && $mark >= $minfraction * $maxmark && $mark <= $maxfraction * $maxmark) ||
                ($mark === null && $maxmark === null);
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
     * Get the number of times each variant has been used for each question in a list
     * in a set of usages.
     * @param array $questionids of question ids.
     * @param qubaid_condition $qubaids ids of the usages to consider.
     * @return array questionid => variant number => num uses.
     */
    public static function load_used_variants(array $questionids, qubaid_condition $qubaids) {
        $dm = new question_engine_data_mapper();
        return $dm->load_used_variants($questionids, $qubaids);
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
        if (!self::is_behaviour_archetypal($preferredbehaviour)) {
            throw new coding_exception('The requested behaviour is not actually ' .
                    'an archetypal one.');
        }

        self::load_behaviour_class($preferredbehaviour);
        $class = 'qbehaviour_' . $preferredbehaviour;
        return new $class($qa, $preferredbehaviour);
    }

    /**
     * @param string $behaviour the name of a behaviour.
     * @return array of {@link question_display_options} field names, that are
     * not relevant to this behaviour before a 'finish' action.
     */
    public static function get_behaviour_unused_display_options($behaviour) {
        return self::get_behaviour_type($behaviour)->get_unused_display_options();
    }

    /**
     * With this behaviour, is it possible that a question might finish as the student
     * interacts with it, without a call to the {@link question_attempt::finish()} method?
     * @param string $behaviour the name of a behaviour. E.g. 'deferredfeedback'.
     * @return bool whether with this behaviour, questions may finish naturally.
     */
    public static function can_questions_finish_during_the_attempt($behaviour) {
        return self::get_behaviour_type($behaviour)->can_questions_finish_during_the_attempt();
    }

    /**
     * Create a behaviour for a particular type. If that type cannot be
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
            self::load_behaviour_class('missing');
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

        $class = 'qbehaviour_' . $behaviour;
        if (!class_exists($class)) {
            throw new coding_exception('Question behaviour ' . $behaviour .
                    ' does not define the required class ' . $class . '.');
        }

        self::$loadedbehaviours[$behaviour] = 1;
    }

    /**
     * Create a behaviour for a particular type. If that type cannot be
     * found, return an instance of qbehaviour_missing.
     *
     * Normally you should use {@link make_archetypal_behaviour()}, or
     * call the constructor of a particular model class directly. This method
     * is only intended for use by {@link question_attempt::load_from_records()}.
     *
     * @param string $behaviour the type of model to create.
     * @param question_attempt $qa the question attempt the model will process.
     * @param string $preferredbehaviour the preferred behaviour for the containing usage.
     * @return question_behaviour_type an instance of appropriate behaviour class.
     */
    public static function get_behaviour_type($behaviour) {

        if (array_key_exists($behaviour, self::$behaviourtypes)) {
            return self::$behaviourtypes[$behaviour];
        }

        self::load_behaviour_type_class($behaviour);

        $class = 'qbehaviour_' . $behaviour . '_type';
        if (class_exists($class)) {
            self::$behaviourtypes[$behaviour] = new $class();
        } else {
            debugging('Question behaviour ' . $behaviour .
                    ' does not define the required class ' . $class . '.', DEBUG_DEVELOPER);
            self::$behaviourtypes[$behaviour] = new question_behaviour_type_fallback($behaviour);
        }

        return self::$behaviourtypes[$behaviour];
    }

    /**
     * Load the behaviour type class for a particular behaviour. That is,
     * include_once('/question/behaviour/' . $behaviour . '/behaviourtype.php').
     * @param string $behaviour the behaviour name. For example 'interactive' or 'deferredfeedback'.
     */
    protected static function load_behaviour_type_class($behaviour) {
        global $CFG;
        if (isset(self::$behaviourtypes[$behaviour])) {
            return;
        }
        $file = $CFG->dirroot . '/question/behaviour/' . $behaviour . '/behaviourtype.php';
        if (!is_readable($file)) {
            debugging('Question behaviour ' . $behaviour .
                    ' is missing the behaviourtype.php file.', DEBUG_DEVELOPER);
        }
        include_once($file);
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
        $behaviours = core_component::get_plugin_list('qbehaviour');
        foreach ($behaviours as $behaviour => $notused) {
            if (self::is_behaviour_archetypal($behaviour)) {
                $archetypes[$behaviour] = self::get_behaviour_name($behaviour);
            }
        }
        asort($archetypes, SORT_LOCALE_STRING);
        return $archetypes;
    }

    /**
     * @param string $behaviour the name of a behaviour. E.g. 'deferredfeedback'.
     * @return bool whether this is an archetypal behaviour.
     */
    public static function is_behaviour_archetypal($behaviour) {
        return self::get_behaviour_type($behaviour)->is_archetypal();
    }

    /**
     * Return an array where the keys are the internal names of the behaviours
     * in preferred order and the values are a human-readable name.
     *
     * @param array $archetypes, array of behaviours
     * @param string $orderlist, a comma separated list of behaviour names
     * @param string $disabledlist, a comma separated list of behaviour names
     * @param string $current, current behaviour name
     * @return array model name => lang string for this behaviour name.
     */
    public static function sort_behaviours($archetypes, $orderlist, $disabledlist, $current=null) {

        // Get disabled behaviours
        if ($disabledlist) {
            $disabled = explode(',', $disabledlist);
        } else {
            $disabled = array();
        }

        if ($orderlist) {
            $order = explode(',', $orderlist);
        } else {
            $order = array();
        }

        foreach ($disabled as $behaviour) {
            if (array_key_exists($behaviour, $archetypes) && $behaviour != $current) {
                unset($archetypes[$behaviour]);
            }
        }

        // Get behaviours in preferred order
        $behaviourorder = array();
        foreach ($order as $behaviour) {
            if (array_key_exists($behaviour, $archetypes)) {
                $behaviourorder[$behaviour] = $archetypes[$behaviour];
            }
        }
        // Get the rest of behaviours and sort them alphabetically
        $leftover = array_diff_key($archetypes, $behaviourorder);
        asort($leftover, SORT_LOCALE_STRING);

        // Set up the final order to be displayed
        return $behaviourorder + $leftover;
    }

    /**
     * Return an array where the keys are the internal names of the behaviours
     * in preferred order and the values are a human-readable name.
     *
     * @param string $currentbehaviour
     * @return array model name => lang string for this behaviour name.
     */
    public static function get_behaviour_options($currentbehaviour) {
        $config = question_bank::get_config();
        $archetypes = self::get_archetypal_behaviours();

        // If no admin setting return all behavious
        if (empty($config->disabledbehaviours) && empty($config->behavioursortorder)) {
            return $archetypes;
        }

        if (empty($config->behavioursortorder)) {
            $order = '';
        } else {
            $order = $config->behavioursortorder;
        }
        if (empty($config->disabledbehaviours)) {
            $disabled = '';
        } else {
            $disabled = $config->disabledbehaviours;
        }

        return self::sort_behaviours($archetypes, $order, $disabled, $currentbehaviour);
    }

    /**
     * Get the translated name of a behaviour, for display in the UI.
     * @param string $behaviour the internal name of the model.
     * @return string name from the current language pack.
     */
    public static function get_behaviour_name($behaviour) {
        return get_string('pluginname', 'qbehaviour_' . $behaviour);
    }

    /**
     * @return array all the file area names that may contain response files.
     */
    public static function get_all_response_file_areas() {
        $variables = array();
        foreach (question_bank::get_all_qtypes() as $qtype) {
            $variables += $qtype->response_file_areas();
        }

        $areas = array();
        foreach (array_unique($variables) as $variable) {
            $areas[] = 'response_' . $variable;
        }
        return $areas;
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
     * @since 2.9
     * @var string extra HTML to include in the info box of the question display.
     * This is normally shown after the information about the question, and before
     * any controls like the flag or the edit icon.
     */
    public $extrainfocontent = '';

    /**
     * @since 2.9
     * @var string extra HTML to include in the history box of the question display,
     * if it is shown.
     */
    public $extrahistorycontent = '';

    /**
     * If not empty, then a link to edit the question will be included in
     * the info box for the question.
     *
     * If used, this array must contain an element courseid or cmid.
     *
     * It shoudl also contain a parameter returnurl => moodle_url giving a
     * sensible URL to go back to when the editing form is submitted or cancelled.
     *
     * @var array url parameter for the edit link. id => questiosnid will be
     * added automatically.
     */
    public $editquestionparams = array();

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
    protected static function get_toggle_checksum($qubaid, $questionid,
            $qaid, $slot, $user = null) {
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
        return "qaid={$qaid}&qubaid={$qubaid}&qid={$qid}&slot={$slot}&checksum={$checksum}&sesskey=" .
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
        if ($checksum != self::get_toggle_checksum($qubaid, $questionid, $qaid, $slot)) {
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
        $flagtext = array(
            0 => get_string('clickflag', 'question'),
            1 => get_string('clickunflag', 'question')
        );
        $flagattributes = array(
            0 => array(
                'src' => $OUTPUT->pix_url('i/unflagged') . '',
                'title' => get_string('clicktoflag', 'question'),
                'alt' => get_string('notflagged', 'question'),
              //  'text' => get_string('clickflag', 'question'),
            ),
            1 => array(
                'src' => $OUTPUT->pix_url('i/flagged') . '',
                'title' => get_string('clicktounflag', 'question'),
                'alt' => get_string('flagged', 'question'),
               // 'text' => get_string('clickunflag', 'question'),
            ),
        );
        $PAGE->requires->js_init_call('M.core_question_flags.init',
                array($actionurl, $flagattributes, $flagtext), false, $module);
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
                "QUBAid: {$qubaid}, slot: {$slot}, post data: " . print_r($postdata, true));
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
            $value1 = (int) $array1[$key];
        } else {
            $value1 = 0;
        }
        if (array_key_exists($key, $array2)) {
            $value2 = (int) $array2[$key];
        } else {
            $value2 = 0;
        }
        return $value1 === $value2;
    }

    private static $units     = array('', 'i', 'ii', 'iii', 'iv', 'v', 'vi', 'vii', 'viii', 'ix');
    private static $tens      = array('', 'x', 'xx', 'xxx', 'xl', 'l', 'lx', 'lxx', 'lxxx', 'xc');
    private static $hundreds  = array('', 'c', 'cc', 'ccc', 'cd', 'd', 'dc', 'dcc', 'dccc', 'cm');
    private static $thousands = array('', 'm', 'mm', 'mmm');

    /**
     * Convert an integer to roman numerals.
     * @param int $number an integer between 1 and 3999 inclusive. Anything else
     *      will throw an exception.
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

    /**
     * Typically, $mark will have come from optional_param($name, null, PARAM_RAW_TRIMMED).
     * This method copes with:
     *  - keeping null or '' input unchanged - important to let teaches set a question back to requries grading.
     *  - numbers that were typed as either 1.00 or 1,00 form.
     *  - invalid things, which get turned into null.
     *
     * @param string|null $mark raw use input of a mark.
     * @return float|string|null cleaned mark as a float if possible. Otherwise '' or null.
     */
    public static function clean_param_mark($mark) {
        if ($mark === '' || is_null($mark)) {
            return $mark;
        }

        $mark = str_replace(',', '.', $mark);
        // This regexp should match the one in validate_param.
        if (!preg_match('/^[\+-]?[0-9]*\.?[0-9]*(e[-+]?[0-9]+)?$/i', $mark)) {
            return null;
        }

        return clean_param($mark, PARAM_FLOAT);
    }

    /**
     * Get a sumitted variable (from the GET or POST data) that is a mark.
     * @param string $parname the submitted variable name.
     * @return float|string|null cleaned mark as a float if possible. Otherwise '' or null.
     */
    public static function optional_param_mark($parname) {
        return self::clean_param_mark(
                optional_param($parname, null, PARAM_RAW_TRIMMED));
    }

    /**
     * Convert part of some question content to plain text.
     * @param string $text the text.
     * @param int $format the text format.
     * @param array $options formatting options. Passed to {@link format_text}.
     * @return float|string|null cleaned mark as a float if possible. Otherwise '' or null.
     */
    public static function to_plain_text($text, $format, $options = array('noclean' => 'true')) {
        // The following call to html_to_text uses the option that strips out
        // all URLs, but format_text complains if it finds @@PLUGINFILE@@ tokens.
        // So, we need to replace @@PLUGINFILE@@ with a real URL, but it doesn't
        // matter what. We use http://example.com/.
        $text = str_replace('@@PLUGINFILE@@/', 'http://example.com/', $text);
        return html_to_text(format_text($text, $format, $options), 0, false);
    }
}


/**
 * The interface for strategies for controlling which variant of each question is used.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface question_variant_selection_strategy {
    /**
     * @param int $maxvariants the num
     * @param string $seed data that can be used to controls how the variant is selected
     *      in a semi-random way.
     * @return int the variant to use, a number betweeb 1 and $maxvariants inclusive.
     */
    public function choose_variant($maxvariants, $seed);
}


/**
 * A {@link question_variant_selection_strategy} that is completely random.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_variant_random_strategy implements question_variant_selection_strategy {
    public function choose_variant($maxvariants, $seed) {
        return rand(1, $maxvariants);
    }
}


/**
 * A {@link question_variant_selection_strategy} that is effectively random
 * for the first attempt, and then after that cycles through the available
 * variants so that the students will not get a repeated variant until they have
 * seen them all.
 *
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_variant_pseudorandom_no_repeats_strategy
        implements question_variant_selection_strategy {

    /** @var int the number of attempts this users has had, including the curent one. */
    protected $attemptno;

    /** @var int the user id the attempt belongs to. */
    protected $userid;

    /** @var string extra input fed into the pseudo-random code. */
    protected $extrarandomness = '';

    /**
     * Constructor.
     * @param int $attemptno The attempt number.
     * @param int $userid the user the attempt is for (defaults to $USER->id).
     */
    public function __construct($attemptno, $userid = null, $extrarandomness = '') {
        $this->attemptno = $attemptno;
        if (is_null($userid)) {
            global $USER;
            $this->userid = $USER->id;
        } else {
            $this->userid = $userid;
        }

        if ($extrarandomness) {
            $this->extrarandomness = '|' . $extrarandomness;
        }
    }

    public function choose_variant($maxvariants, $seed) {
        if ($maxvariants == 1) {
            return 1;
        }

        $hash = sha1($seed . '|user' . $this->userid . $this->extrarandomness);
        $randint = hexdec(substr($hash, 17, 7));

        return ($randint + $this->attemptno) % $maxvariants + 1;
    }
}

/**
 * A {@link question_variant_selection_strategy} designed ONLY for testing.
 * For selected questions it wil return a specific variants. For the other
 * slots it will use a fallback strategy.
 *
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_variant_forced_choices_selection_strategy
    implements question_variant_selection_strategy {

    /** @var array seed => variant to select. */
    protected $forcedchoices;

    /** @var question_variant_selection_strategy strategy used to make the non-forced choices. */
    protected $basestrategy;

    /**
     * Constructor.
     * @param array $forcedchoices array seed => variant to select.
     * @param question_variant_selection_strategy $basestrategy strategy used
     *      to make the non-forced choices.
     */
    public function __construct(array $forcedchoices, question_variant_selection_strategy $basestrategy) {
        $this->forcedchoices = $forcedchoices;
        $this->basestrategy  = $basestrategy;
    }

    public function choose_variant($maxvariants, $seed) {
        if (array_key_exists($seed, $this->forcedchoices)) {
            if ($this->forcedchoices[$seed] > $maxvariants) {
                throw new coding_exception('Forced variant out of range.');
            }
            return $this->forcedchoices[$seed];
        } else {
            return $this->basestrategy->choose_variant($maxvariants, $seed);
        }
    }

    /**
     * Helper method for preparing the $forcedchoices array.
     * @param array                      $variantsbyslot slot number => variant to select.
     * @param question_usage_by_activity $quba           the question usage we need a strategy for.
     * @throws coding_exception when variant cannot be forced as doesn't work.
     * @return array that can be passed to the constructor as $forcedchoices.
     */
    public static function prepare_forced_choices_array(array $variantsbyslot,
                                                        question_usage_by_activity $quba) {

        $forcedchoices = array();

        foreach ($variantsbyslot as $slot => $varianttochoose) {
            $question = $quba->get_question($slot);
            $seed = $question->get_variants_selection_seed();
            if (array_key_exists($seed, $forcedchoices) && $forcedchoices[$seed] != $varianttochoose) {
                throw new coding_exception('Inconsistent forced variant detected at slot ' . $slot);
            }
            if ($varianttochoose > $question->get_num_variants()) {
                throw new coding_exception('Forced variant out of range at slot ' . $slot);
            }
            $forcedchoices[$seed] = $varianttochoose;
        }
        return $forcedchoices;
    }
}
