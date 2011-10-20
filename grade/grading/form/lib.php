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
 * Common classes used by gradingform plugintypes are defined here
 *
 * @package    core
 * @subpackage grading
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Grading method controller represents a plugin used in a particular area
 */
abstract class gradingform_controller {

    const DEFINITION_STATUS_WORKINPROGRESS  = 0;
    const DEFINITION_STATUS_PRIVATE         = 1;
    const DEFINITION_STATUS_PUBLIC          = 2;

    /** @var stdClass the context */
    protected $context;

    /** @var string the frankenstyle name of the component */
    protected $component;

    /** @var string the name of the gradable area */
    protected $area;

    /** @var int the id of the gradable area record */
    protected $areaid;

    /** @var stdClass|false the definition structure */
    protected $definition;

    /**
     * Do not instantinate this directly, use {@link grading_manager::get_controller()}
     *
     * @param stdClass $context the context of the form
     * @param string $component the frankenstyle name of the component
     * @param string $area the name of the gradable area
     * @param int $areaid the id of the gradable area record
     */
    public function __construct(stdClass $context, $component, $area, $areaid) {
        global $DB;

        $this->context      = $context;
        list($type, $name)  = normalize_component($component);
        $this->component    = $type.'_'.$name;
        $this->area         = $area;
        $this->areaid       = $areaid;

        $this->load_definition();
    }

    /**
     * @return stdClass controller context
     */
    public function get_context() {
        return $this->context;
    }

    /**
     * @return string gradable component name
     */
    public function get_component() {
        return $this->component;
    }

    /**
     * @return string gradable area name
     */
    public function get_area() {
        return $this->area;
    }

    /**
     * @return int gradable area id
     */
    public function get_areaid() {
        return $this->areaid;
    }

    /**
     * Is the form definition record available?
     *
     * Note that this actually checks whether the process of defining the form ever started
     * and not whether the form definition should be considered as final.
     *
     * @return boolean
     */
    public function is_form_defined() {
        return !empty($this->definition);
    }

    /**
     * Is the grading form defined and released for usage by the given user?
     *
     * @param int $foruserid the id of the user who attempts to work with the form
     * @return boolean
     */
    public function is_form_available($foruserid = null) {
        global $USER;

        if (is_null($foruserid)) {
            $foruserid = $USER->id;
        }

        if (!$this->is_form_defined()) {
            return false;
        }

        if ($this->definition->status == self::DEFINITION_STATUS_PUBLIC) {
            return true;
        }

        if ($this->definition->status == self::DEFINITION_STATUS_PRIVATE) {
            if ($this->definition->usercreated == $foruserid) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns URL of a page where the grading form can be defined and edited.
     *
     * @param moodle_url $returnurl optional URL of a page where the user should be sent once they are finished with editing
     * @return moodle_url
     */
    public function get_editor_url(moodle_url $returnurl = null) {

        $params = array('areaid' => $this->areaid);

        if (!is_null($returnurl)) {
            $params['returnurl'] = $returnurl->out(false);
        }

        return new moodle_url('/grade/grading/form/'.$this->get_method_name().'/edit.php', $params);
    }

    /**
     * Extends the module settings navigation
     *
     * This function is called when the context for the page is an activity module with the
     * FEATURE_ADVANCED_GRADING, the user has the permission moodle/grade:managegradingforms
     * and there is an area with the active grading method set to the given plugin.
     *
     * @param settings_navigation $settingsnav {@link settings_navigation}
     * @param navigation_node $node {@link navigation_node}
     */
    public function extend_settings_navigation(settings_navigation $settingsnav, navigation_node $node=null) {
        // do not extend by default
    }

    /**
     * Returns the grading form definition structure
     *
     * @return stdClass|false definition data or false if the form is not defined yet
     */
    public function get_definition() {
        if (is_null($this->definition)) {
            $this->load_definition();
        }
        return $this->definition;
    }

    /**
     * Returns the form definition suitable for cloning into another area
     *
     * @param gradingform_controller $target the controller of the new copy
     * @return stdClass definition structure to pass to the target's {@link update_definition()}
     */
    public function get_definition_copy(gradingform_controller $target) {

        if (get_class($this) != get_class($target)) {
            throw new coding_exception('The source and copy controller mismatch');
        }

        if ($target->is_form_defined()) {
            throw new coding_exception('The target controller already contains a form definition');
        }

        $old = $this->get_definition();
        // keep our id
        $new = new stdClass();
        $new->copiedfromid = $old->id;
        $new->name = $old->name;
        // once we support files embedded into the description, we will want to
        // relink them into the new file area here (that is why we accept $target)
        $new->description = $old->description;
        $new->descriptionformat = $old->descriptionformat;
        $new->options = $old->options;

        return $new;
    }

    /**
     * Saves the defintion data into the database
     *
     * The implementation in this base class stores the common data into the record
     * into the {grading_definition} table. The plugins are likely to extend this
     * and save their data into own tables, too.
     *
     * @param stdClass $definition data containing values for the {grading_definition} table
     * @param int|null $usermodified optional userid of the author of the definition, defaults to the current user
     */
    public function update_definition(stdClass $definition, $usermodified = null) {
        global $DB, $USER;

        if (is_null($usermodified)) {
            $usermodified = $USER->id;
        }

        if (!empty($this->definition->id)) {
            // prepare a record to be updated
            $record = new stdClass();
            // populate it with scalar values from the passed definition structure
            foreach ($definition as $prop => $val) {
                if (is_array($val) or is_object($val)) {
                    // probably plugin's data
                    continue;
                }
                $record->{$prop} = $val;
            }
            // make sure we do not override some crucial values by accident
            if (!empty($record->id) and $record->id != $this->definition->id) {
                throw new coding_exception('Attempting to update other definition record.');
            }
            $record->id = $this->definition->id;
            unset($record->areaid);
            unset($record->method);
            unset($record->timecreated);
            // set the modification flags
            $record->timemodified = time();
            $record->usermodified = $usermodified;

            $DB->update_record('grading_definitions', $record);

        } else if ($this->definition === false) {
            // prepare a record to be inserted
            $record = new stdClass();
            // populate it with scalar values from the passed definition structure
            foreach ($definition as $prop => $val) {
                if (is_array($val) or is_object($val)) {
                    // probably plugin's data
                    continue;
                }
                $record->{$prop} = $val;
            }
            // make sure we do not override some crucial values by accident
            if (!empty($record->id)) {
                throw new coding_exception('Attempting to create a new record while there is already one existing.');
            }
            unset($record->id);
            $record->areaid       = $this->areaid;
            $record->method       = $this->get_method_name();
            $record->timecreated  = time();
            $record->usercreated  = $usermodified;
            $record->timemodified = $record->timecreated;
            $record->usermodified = $record->usercreated;
            $record->status       = self::DEFINITION_STATUS_WORKINPROGRESS;

            $DB->insert_record('grading_definitions', $record);

        } else {
            throw new coding_exception('Unknown status of the cached definition record.');
        }
    }

    /**
     * Returns the ACTIVE instance for this definition for the specified $raterid and $itemid
     * (if multiple raters are allowed, or only for $itemid otherwise).
     *
     * @param int $raterid
     * @param int $itemid
     * @param boolean $idonly
     * @return mixed if $idonly=true returns id of the found instance, otherwise returns the instance object
     */
    public function get_current_instance($raterid, $itemid, $idonly = false) {
        global $DB;
        $select = array(
                'formid'  => $this->definition->id,
                'itemid' => $itemid,
                'status'  => gradingform_instance::INSTANCE_STATUS_ACTIVE);
        if (false /* TODO $manager->allow_multiple_raters() */) {
            $select['raterid'] = $raterid;
        }
        if ($idonly) {
            if ($current = $DB->get_record('grading_instances', $select, 'id', IGNORE_MISSING)) {
                return $current->id;
            }
        } else {
            if ($current = $DB->get_record('grading_instances', $select, '*', IGNORE_MISSING)) {
                return $this->get_instance($current);
            }
        }
        return null;
    }

    /**
     * Returns list of active instances for the specified $itemid
     *
     * @param int $itemid
     * @return array of gradingform_instance objects
     */
    public function get_current_instances($itemid) {
        global $DB;
        $conditions = array('formid'  => $this->definition->id,
                    'itemid' => $itemid,
                    'status'  => gradingform_instance::INSTANCE_STATUS_ACTIVE);
        $records = $DB->get_recordset('grading_instances', $conditions);
        $rv = array();
        foreach ($records as $record) {
            $rv[] = $this->get_instance($record);
        }
        return $rv;
    }

    /**
     * Returns the object of type gradingform_XXX_instance (where XXX is the plugin method name)
     *
     * @param mixed $instance id or row from grading_isntances table
     * @return gradingform_instance
     */
    protected function get_instance($instance) {
        global $DB;
        if (is_scalar($instance)) {
            // instance id is passed as parameter
            $instance = $DB->get_record('grading_instances', array('id'  => $instance), '*', MUST_EXIST);
        }
        if ($instance) {
            $class = 'gradingform_'. $this->get_method_name(). '_instance';
            return new $class($this, $instance);
        }
        return null;
    }

    /**
     * This function is invoked when user (teacher) starts grading.
     * It creates and returns copy of the current ACTIVE instance if it exists. If this is the
     * first grading attempt, a new instance is created.
     * The status of the returned instance is INCOMPLETE
     *
     * @param int $raterid
     * @param int $itemid
     * @return gradingform_instance
     */
    public function create_instance($raterid, $itemid) {
        global $DB;
        // first find if there is already an active instance for this itemid
        if ($current = $this->get_current_instance($raterid, $itemid)) {
            return $this->get_instance($current->copy($raterid, $itemid));
        } else {
            $class = 'gradingform_'. $this->get_method_name(). '_instance';
            return $this->get_instance($class::create_new($this->definition->id, $raterid, $itemid));
        }
    }

    /**
     * Returns the HTML code displaying the preview of the grading form
     *
     * Plugins are supposed to override/extend this. Ideally they should delegate
     * the task to their own renderer.
     *
     * @param moodle_page $page the target page
     * @return string
     */
    public function render_preview(moodle_page $page) {

        if (!$this->is_form_defined()) {
            throw new coding_exception('It is the caller\'s responsibility to make sure that the form is actually defined');
        }

        $output = $page->get_renderer('core_grading');

        return $output->preview_definition_header($this);
    }

    /**
     * Deletes the form definition and all the associated data
     *
     * @see delete_plugin_definition()
     * @return void
     */
    public function delete_definition() {
        global $DB;

        if (!$this->is_form_defined()) {
            // nothing to do
            return;
        }

        // firstly, let the plugin delete everything from their own tables
        $this->delete_plugin_definition();
        // then, delete all instances left
        $DB->delete_records('grading_instances', array('formid' => $this->definition->id));
        // finally, delete the main definition record
        $DB->delete_records('grading_definitions', array('id' => $this->definition->id));

        $this->definition = false;
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * Loads the form definition if it exists
     *
     * The default implementation just tries to load the record from the {grading_definitions}
     * table. The plugins are likely to override this with a more complex query that loads
     * all required data at once.
     */
    protected function load_definition() {
        global $DB;
        $this->definition = $DB->get_record('grading_definitions', array(
            'areaid' => $this->areaid,
            'method' => $this->get_method_name()), '*', IGNORE_MISSING);
    }

    /**
     * Deletes all plugin data associated with the given form definiton
     *
     * @see delete_definition()
     */
    abstract protected function delete_plugin_definition();

    /**
     * @return string the name of the grading method plugin, eg 'rubric'
     * @see PARAM_PLUGIN
     */
    protected function get_method_name() {
        if (preg_match('/^gradingform_([a-z][a-z0-9_]*[a-z0-9])_controller$/', get_class($this), $matches)) {
            return $matches[1];
        } else {
            throw new coding_exception('Invalid class name');
        }
    }

    /**
     * Returns html code to be included in student's feedback.
     *
     * @param moodle_page $page
     * @param int $itemid
     * @param string $defaultcontent default string to be returned if no active grading is found
     * @return string
     */
    public function render_grade($page, $itemid, $defaultcontent) {
        return $defaultcontent;
    }
}

/**
 * Class to manage one grading instance. Stores information and performs actions like
 * update, copy, validate, submit, etc.
 *
 * @copyright  2011 Marina Glancy
 */
abstract class gradingform_instance {
    const INSTANCE_STATUS_ACTIVE = 1;
    const INSTANCE_STATUS_INCOMPLETE = 0;
    const INSTANCE_STATUS_ARCHIVE = 3;

    /** @var stdClass record from table grading_instances */
    protected $data;
    /** @var gradingform_controller link to the corresponding controller */
    protected $controller;

    /**
     * Creates an instance
     *
     * @param gradingform_controller $controller
     * @param stdClass $data
     */
    public function __construct($controller, $data) {
        $this->data = (object)$data;
        $this->controller = $controller;
    }

    /**
     * Creates a new empty instance in DB and mark its status as INCOMPLETE
     *
     * @param int $formid
     * @param int $raterid
     * @param int $itemid
     * @return int id of the created instance
     */
    public static function create_new($formid, $raterid, $itemid) {
        global $DB;
        $instance = new stdClass();
        $instance->formid = $formid;
        $instance->raterid = $raterid;
        $instance->itemid = $itemid;
        $instance->status = self::INSTANCE_STATUS_INCOMPLETE;
        $instance->timemodified = time();
        $instance->feedbackformat = FORMAT_MOODLE;
        $instanceid = $DB->insert_record('grading_instances', $instance);
        return $instanceid;
    }

    /**
     * Duplicates the instance before editing (optionally substitutes raterid and/or itemid with
     * the specified values)
     * Plugins may want to override this function to copy data from additional tables as well
     *
     * @param int $raterid value for raterid in the duplicate
     * @param int $itemid value for itemid in the duplicate
     * @return int id of the new instance
     */
    public function copy($raterid, $itemid) {
        global $DB;
        $data = (array)$this->data; // Cast to array to make a copy
        unset($data['id']);
        $data['raterid'] = $raterid;
        $data['itemid'] = $itemid;
        $data['timemodified'] = time();
        $data['status'] = self::INSTANCE_STATUS_INCOMPLETE;
        $instanceid = $DB->insert_record('grading_instances', $data);
        return $instanceid;
    }

    /**
     * Returns the controller
     *
     * @return gradingform_controller
     */
    public function get_controller() {
        return $this->controller;
    }

    /**
     * Returns instance id
     *
     * @return int
     */
    public function get_id() {
        return $this->data->id;
    }

    /**
     * Marks the instance as ACTIVE and current active instance (if exists) as ARCHIVE
     */
    protected function make_active() {
        global $DB;
        if ($this->data->status == self::INSTANCE_STATUS_ACTIVE) {
            // already active
            return;
        }
        $currentid = $this->get_controller()->get_current_instance($this->data->raterid, $this->data->itemid, true);
        if ($currentid) {
            if ($currentid != $this->get_id()) {
                $DB->update_record('grading_instances', array('id' => $currentid, 'status' => self::INSTANCE_STATUS_ARCHIVE));
                $DB->update_record('grading_instances', array('id' => $this->get_id(), 'status' => self::INSTANCE_STATUS_ACTIVE));
            }
        } else {
            $DB->update_record('grading_instances', array('id' => $this->get_id(), 'status' => self::INSTANCE_STATUS_ACTIVE));
        }
        $this->data->status = self::INSTANCE_STATUS_ACTIVE;
    }

    /**
     * Deletes this (INCOMPLETE) instance from database. This function is invoked on cancelling the
     * grading form and/or during cron cleanup.
     * Plugins using additional tables must override this method to remove additional data.
     * Note that if the teacher just closes the window or presses 'Back' button of the browser,
     * this function is not invoked.
     */
    public function cancel() {
        global $DB;
        // TODO what if we happen delete the ACTIVE instance, shall we rollback to the last ARCHIVE? or throw an exception?
        // TODO create cleanup cron
        $DB->delete_records('grading_instances', array('id' => $this->get_id()));
    }

    /**
     * Updates the instance with the data received from grading form. This function may be
     * called via AJAX when grading is not yet completed, so it does not change the
     * status of the instance.
     */
    public function update($elementvalue) {
        // TODO update timemodified at least
    }

    /**
     * Calculates the grade to be pushed to the gradebook
     * @return int the grade on 0-100 scale
     */
    abstract public function get_grade();

    /**
     * Called when teacher submits the grading form:
     * updates the instance in DB, marks it as ACTIVE and returns the grade to be pushed to the gradebook
     * @return int the grade on 0-100 scale
     */
    public function submit_and_get_grade($elementvalue) {
        $this->update($elementvalue);
        $this->make_active();
        return $this->get_grade();
    }


    /**
     * Returns html for form element of type 'grading'. If there is a form input element
     * it must have the name $gradingformelement->getName().
     * If there are more than one input elements they MUST be elements of array with
     * name $gradingformelement->getName().
     * Example: {NAME}[myelement1], {NAME}[myelement2][sub1], {NAME}[myelement2][sub2], etc.
     * After submitting the form the value of $_POST[{NAME}] is passed to the functions
     * validate_grading_element() and submit_and_get_grade()
     *
     * Plugins may use $gradingformelement->getValue() to get the value passed on previous
     * from submit
     *
     * When forming html it is a plugin's responsibility to analyze flags
     * $gradingformelement->_flagFrozen and $gradingformelement->_persistantFreeze:
     *
     * (_flagFrozen == false) => form element is editable
     *
     * (_flagFrozen == false && _persistantFreeze == true) => form element is not editable
     * but all values are passed as hidden elements
     *
     * (_flagFrozen == false && _persistantFreeze == false) => form element is not editable
     * and no values are passed as hidden elements
     *
     * Plugins are welcome to use AJAX in the form element. But it is strongly recommended
     * that the grading only becomes active when teacher presses 'Submit' button (the
     * method submit_and_get_grade() is invoked)
     *
     * Also client-side JS validation may be implemented here
     *
     * @see MoodleQuickForm_grading in lib/form/grading.php
     *
     * @param moodle_page $page
     * @param MoodleQuickForm_grading $gradingformelement
     * @return string
     */
    abstract function render_grading_element($page, $gradingformelement);

    /**
     * Server-side validation of the data received from grading form.
     *
     * @param mixed $elementvalue is the scalar or array received in $_POST
     * @return boolean true if the form data is validated and contains no errors
     */
    public function validate_grading_element($elementvalue) {
        return true;
    }

    /**
     * Returns the error message displayed if validation failed.
     * If plugin wants to display custom message, the empty string should be returned here
     * and the custom message should be output in render_grading_element()
     *
     * @see validate_grading_element()
     * @return string
     */
    public function default_validation_error_message() {
        return '';
    }
}