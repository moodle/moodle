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
 * This file defines the question attempt step class, and a few related classes.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Stores one step in a {@link question_attempt}.
 *
 * The most important attributes of a step are the state, which is one of the
 * {@link question_state} constants, the fraction, which may be null, or a
 * number bewteen the attempt's minfraction and maxfraction, and the array of submitted
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
    /**
     * @var integer if this attempts is stored in the question_attempts table,
     * the id of that row.
     */
    private $id = null;

    /**
     * @var question_state one of the {@link question_state} constants.
     * The state after this step.
     */
    private $state;

    /**
     * @var null|number the fraction (grade on a scale of
     * minfraction .. maxfraction, normally 0..1) or null.
     */
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
     * @param int $existingstepid if this step is going to replace an existing step
     *      (for example, during a regrade) this is the id of the previous step we are replacing.
     */
    public function __construct($data = array(), $timecreated = null, $userid = null,
            $existingstepid = null) {
        global $USER;

        if (!is_array($data)) {
            throw new coding_exception('$data must be an array when constructing a question_attempt_step.');
        }
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

        if (!is_null($existingstepid)) {
            $this->id = $existingstepid;
        }
    }

    /**
     * @return int|null The id of this step in the database. null if this step
     * is not stored in the database.
     */
    public function get_id() {
        return $this->id;
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
     * @return null|number the fraction (grade on a scale of
     * minfraction .. maxfraction, normally 0..1),
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
            throw new coding_exception('Cannot set question type data ' . $name .
                    ' on an attempt step. You can only set variables with names begining with _.');
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
     * @param string $name the name of a behaviour variable to look for in the submitted data.
     * @return bool whether a variable with this name exists in the question type data.
     */
    public function has_behaviour_var($name) {
        return array_key_exists('-' . $name, $this->data);
    }

    /**
     * @param string $name the name of a behaviour variable to look for in the submitted data.
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
            throw new coding_exception('Cannot set question type data ' . $name .
                    ' on an attempt step. You can only set variables with names begining with _.');
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
     * however, it can occasionally be useful in test code. It should not be
     * considered part of the public API of this class.
     * @param array name => value pairs.
     */
    public function get_all_data() {
        return $this->data;
    }

    /**
     * Create a question_attempt_step from records loaded from the database.
     * @param Iterator $records Raw records loaded from the database.
     * @param int $stepid The id of the records to extract.
     * @param string $qtype The question type of which this is an attempt.
     *      If not given, each record must include a qtype field.
     * @return question_attempt_step The newly constructed question_attempt_step.
     */
    public static function load_from_records($records, $attemptstepid, $qtype = null) {
        $currentrec = $records->current();
        while ($currentrec->attemptstepid != $attemptstepid) {
            $records->next();
            if (!$records->valid()) {
                throw new coding_exception('Question attempt step ' . $attemptstepid .
                        ' not found in the database.');
            }
            $currentrec = $records->current();
        }

        $record = $currentrec;
        $contextid = null;
        $data = array();
        while ($currentrec && $currentrec->attemptstepid == $attemptstepid) {
            if (!is_null($currentrec->name)) {
                $data[$currentrec->name] = $currentrec->value;
            }
            $records->next();
            if ($records->valid()) {
                $currentrec = $records->current();
            } else {
                $currentrec = false;
            }
        }

        $step = new question_attempt_step_read_only($data, $record->timecreated, $record->userid);
        $step->state = question_state::get($record->state);
        $step->id = $record->attemptstepid;
        if (!is_null($record->fraction)) {
            $step->fraction = $record->fraction + 0;
        }

        // This next chunk of code requires getting $contextid and $qtype here.
        // Somehow, we need to get that information to this point by modifying
        // all the paths by which this method can be called.
        // Can we only return files when it's possible? Should there be some kind of warning?
        if (is_null($qtype)) {
            $qtype = $record->qtype;
        }
        foreach (question_bank::get_qtype($qtype)->response_file_areas() as $area) {
            if (empty($step->data[$area])) {
                continue;
            }

            $step->data[$area] = new question_file_loader($step, $area, $step->data[$area], $record->contextid);
        }

        return $step;
    }
}


/**
 * A subclass of {@link question_attempt_step} used when processing a new submission.
 *
 * When we are processing some new submitted data, which may or may not lead to
 * a new step being added to the {@link question_usage_by_activity} we create an
 * instance of this class. which is then passed to the question behaviour and question
 * type for processing. At the end of processing we then may, or may not, keep it.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_attempt_pending_step extends question_attempt_step {
    /** @var string the new response summary, if there is one. */
    protected $newresponsesummary = null;

    /** @var int the new variant number, if there is one. */
    protected $newvariant = null;

    /**
     * If as a result of processing this step, the response summary for the
     * question attempt should changed, you should call this method to set the
     * new summary.
     * @param string $responsesummary the new response summary.
     */
    public function set_new_response_summary($responsesummary) {
        $this->newresponsesummary = $responsesummary;
    }

    /**
     * Get the new response summary, if there is one.
     * @return string the new response summary, or null if it has not changed.
     */
    public function get_new_response_summary() {
        return $this->newresponsesummary;
    }

    /**
     * Whether this processing this step has changed the response summary.
     * @return bool true if there is a new response summary.
     */
    public function response_summary_changed() {
        return !is_null($this->newresponsesummary);
    }

    /**
     * If as a result of processing this step, you identify that this variant of the
     * question is acutally identical to the another one, you may change the
     * variant number recorded, in order to give better statistics. For an example
     * see qbehaviour_opaque.
     * @param int $variant the new variant number.
     */
    public function set_new_variant_number($variant) {
        $this->newvariant = $variant;
    }

    /**
     * Get the new variant number, if there is one.
     * @return int the new variant number, or null if it has not changed.
     */
    public function get_new_variant_number() {
        return $this->newvariant;
    }

    /**
     * Whether this processing this step has changed the variant number.
     * @return bool true if there is a new variant number.
     */
    public function variant_number_changed() {
        return !is_null($this->newvariant);
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
 * This is an adapter class that wraps a {@link question_attempt_step} and
 * modifies the get/set_*_data methods so that they operate only on the parts
 * that belong to a particular subquestion, as indicated by an extra prefix.
 *
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_attempt_step_subquestion_adapter extends question_attempt_step {
    /** @var question_attempt_step the step we are wrapping. */
    protected $realstep;
    /** @var string the exta prefix on fields we work with. */
    protected $extraprefix;

    /**
     * Constructor.
     * @param question_attempt_step $realqas the step to wrap. (Can be null if you
     *      just want to call add/remove.prefix.)
     * @param unknown_type $extraprefix the extra prefix that is used for date fields.
     */
    public function __construct($realqas, $extraprefix) {
        $this->realqas = $realqas;
        $this->extraprefix = $extraprefix;
    }

    /**
     * Add the extra prefix to a field name.
     * @param string $field the plain field name.
     * @return string the field name with the extra bit of prefix added.
     */
    public function add_prefix($field) {
        if (substr($field, 0, 2) === '!_') {
            return '-_' . $this->extraprefix . substr($field, 2);
        } else if (substr($field, 0, 1) === '-') {
            return '-' . $this->extraprefix . substr($field, 1);
        } else if (substr($field, 0, 1) === '_') {
            return '_' . $this->extraprefix . substr($field, 1);
        } else {
            return $this->extraprefix . $field;
        }
    }

    /**
     * Remove the extra prefix from a field name if it is present.
     * @param string $field the extended field name.
     * @return string the field name with the extra bit of prefix removed, or
     * null if the extre prefix was not present.
     */
    public function remove_prefix($field) {
        if (preg_match('~^(-?_?)' . preg_quote($this->extraprefix, '~') . '(.*)$~', $field, $matches)) {
            return $matches[1] . $matches[2];
        } else {
            return null;
        }
    }

    /**
     * Filter some data to keep only those entries where the key contains
     * extraprefix, and remove the extra prefix from the reutrned arrary.
     * @param array $data some of the data stored in this step.
     * @return array the data with the keys ajusted using {@link remove_prefix()}.
     */
    public function filter_array($data) {
        $result = array();
        foreach ($data as $fullname => $value) {
            if ($name = $this->remove_prefix($fullname)) {
                $result[$name] = $value;
            }
        }
        return $result;
    }

    public function get_state() {
        return $this->realqas->get_state();
    }

    public function set_state($state) {
        throw new coding_exception('Cannot modify a question_attempt_step_subquestion_adapter.');
    }

    public function get_fraction() {
        return $this->realqas->get_fraction();
    }

    public function set_fraction($fraction) {
        throw new coding_exception('Cannot modify a question_attempt_step_subquestion_adapter.');
    }

    public function get_user_id() {
        return $this->realqas->get_user_id;
    }

    public function get_timecreated() {
        return $this->realqas->get_timecreated();
    }

    public function has_qt_var($name) {
        return $this->realqas->has_qt_var($this->add_prefix($name));
    }

    public function get_qt_var($name) {
        return $this->realqas->get_qt_var($this->add_prefix($name));
    }

    public function set_qt_var($name, $value) {
        return $this->realqas->set_qt_var($this->add_prefix($name), $value);
    }

    public function get_qt_data() {
        return $this->filter_array($this->realqas->get_qt_data());
    }

    public function has_behaviour_var($name) {
        return $this->realqas->has_im_var($this->add_prefix($name));
    }

    public function get_behaviour_var($name) {
        return $this->realqas->get_im_var($this->add_prefix($name));
    }

    public function set_behaviour_var($name, $value) {
        return $this->realqas->set_im_var($this->add_prefix($name), $value);
    }

    public function get_behaviour_data() {
        return $this->filter_array($this->realqas->get_behaviour_data());
    }

    public function get_submitted_data() {
        return $this->filter_array($this->realqas->get_submitted_data());
    }

    public function get_all_data() {
        return $this->filter_array($this->realqas->get_all_data());
    }

    public function get_qt_files($name, $contextid) {
        throw new coding_exception('No attempt has yet been made to implement files support in ' .
                'question_attempt_step_subquestion_adapter.');
    }

    public function prepare_response_files_draft_itemid($name, $contextid) {
        throw new coding_exception('No attempt has yet been made to implement files support in ' .
                'question_attempt_step_subquestion_adapter.');
    }

    public function prepare_response_files_draft_itemid_with_text($name, $contextid, $text) {
        throw new coding_exception('No attempt has yet been made to implement files support in ' .
                'question_attempt_step_subquestion_adapter.');
    }

    public function rewrite_response_pluginfile_urls($text, $contextid, $name, $extras) {
        throw new coding_exception('No attempt has yet been made to implement files support in ' .
                'question_attempt_step_subquestion_adapter.');
    }
}
