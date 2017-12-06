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
 * @package    core_grading
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class represents a grading form definition used in a particular area
 *
 * General data about definition is stored in the standard DB table
 * grading_definitions. A separate entry is created for each grading area
 * (i.e. for each module). Plugins may define and use additional tables
 * to store additional data about definitions.
 *
 * Advanced grading plugins must declare a class gradingform_xxxx_controller
 * extending this class and put it in lib.php in the plugin folder.
 *
 * See {@link gradingform_rubric_controller} as an example
 *
 * Except for overwriting abstract functions, plugin developers may want
 * to overwrite functions responsible for loading and saving of the
 * definition to include additional data stored.
 *
 * @package    core_grading
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @category   grading
 */
abstract class gradingform_controller {

    /** undefined definition status */
    const DEFINITION_STATUS_NULL = 0;
    /** the form is currently being edited and is not ready for usage yet */
    const DEFINITION_STATUS_DRAFT = 10;
    /** the form was marked as ready for actual usage */
    const DEFINITION_STATUS_READY = 20;

    /** @var stdClass the context */
    protected $context;

    /** @var string the frankenstyle name of the component */
    protected $component;

    /** @var string the name of the gradable area */
    protected $area;

    /** @var int the id of the gradable area record */
    protected $areaid;

    /** @var stdClass|false the definition structure */
    protected $definition = false;

    /** @var array graderange array of valid grades for this area. Use set_grade_range and get_grade_range to access this */
    private $graderange = null;

    /** @var bool if decimal values are allowed as grades. */
    private $allowgradedecimals = false;

    /** @var boolean|null cached result of function has_active_instances() */
    protected $hasactiveinstances = null;

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
        list($type, $name)  = core_component::normalize_component($component);
        $this->component    = $type.'_'.$name;
        $this->area         = $area;
        $this->areaid       = $areaid;

        $this->load_definition();
    }

    /**
     * Returns controller context
     *
     * @return stdClass controller context
     */
    public function get_context() {
        return $this->context;
    }

    /**
     * Returns gradable component name
     *
     * @return string gradable component name
     */
    public function get_component() {
        return $this->component;
    }

    /**
     * Returns gradable area name
     *
     * @return string gradable area name
     */
    public function get_area() {
        return $this->area;
    }

    /**
     * Returns gradable area id
     *
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
        return ($this->definition !== false);
    }

    /**
     * Is the grading form defined and ready for usage?
     *
     * @return boolean
     */
    public function is_form_available() {
        return ($this->is_form_defined() && $this->definition->status == self::DEFINITION_STATUS_READY);
    }

    /**
     * Is the grading form saved as a shared public template?
     *
     * @return boolean
     */
    public function is_shared_template() {
        return ($this->get_context()->id == context_system::instance()->id
            and $this->get_component() == 'core_grading');
    }

    /**
     * Is the grading form owned by the given user?
     *
     * The form owner is the user who created this instance of the form.
     *
     * @param int $userid the user id to check, defaults to the current user
     * @return boolean|null null if the form not defined yet, boolean otherwise
     */
    public function is_own_form($userid = null) {
        global $USER;

        if (!$this->is_form_defined()) {
            return null;
        }
        if (is_null($userid)) {
            $userid = $USER->id;
        }
        return ($this->definition->usercreated == $userid);
    }

    /**
     * Returns a message why this form is unavailable. Maybe overriden by plugins to give more details.
     * @see is_form_available()
     *
     * @return string
     */
    public function form_unavailable_notification() {
        if ($this->is_form_available()) {
            return null;
        }
        return get_string('gradingformunavailable', 'grading');
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
     * Extends the module navigation
     *
     * This function is called when the context for the page is an activity module with the
     * FEATURE_ADVANCED_GRADING and there is an area with the active grading method set to the given plugin.
     *
     * @param global_navigation $navigation {@link global_navigation}
     * @param navigation_node $node {@link navigation_node}
     */
    public function extend_navigation(global_navigation $navigation, navigation_node $node=null) {
        // do not extend by default
    }

    /**
     * Returns the grading form definition structure
     *
     * @param boolean $force whether to force loading from DB even if it was already loaded
     * @return stdClass|false definition data or false if the form is not defined yet
     */
    public function get_definition($force = false) {
        if ($this->definition === false || $force) {
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
        $new->status = $old->status;

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
            if (empty($record->status)) {
                $record->status = self::DEFINITION_STATUS_DRAFT;
            }
            if (empty($record->descriptionformat)) {
                $record->descriptionformat = FORMAT_MOODLE; // field can not be empty
            }

            $DB->insert_record('grading_definitions', $record);

        } else {
            throw new coding_exception('Unknown status of the cached definition record.');
        }
    }

    /**
     * Formats the definition description for display on page
     *
     * @return string
     */
    public function get_formatted_description() {
        if (!isset($this->definition->description)) {
            return '';
        }
        return format_text($this->definition->description, $this->definition->descriptionformat);
    }

    /**
     * Returns the current instance (either with status ACTIVE or NEEDUPDATE) for this definition for the
     * specified $raterid and $itemid (if multiple raters are allowed, or only for $itemid otherwise).
     *
     * @param int $raterid
     * @param int $itemid
     * @param boolean $idonly
     * @return mixed if $idonly=true returns id of the found instance, otherwise returns the instance object
     */
    public function get_current_instance($raterid, $itemid, $idonly = false) {
        global $DB;
        $params = array(
                'definitionid'  => $this->definition->id,
                'itemid' => $itemid,
                'status1'  => gradingform_instance::INSTANCE_STATUS_ACTIVE,
                'status2'  => gradingform_instance::INSTANCE_STATUS_NEEDUPDATE);
        $select = 'definitionid=:definitionid and itemid=:itemid and (status=:status1 or status=:status2)';
        if (false) {
            // TODO MDL-31237 should be: if ($manager->allow_multiple_raters())
            $select .= ' and raterid=:raterid';
            $params['raterid'] = $raterid;
        }
        if ($idonly) {
            if ($current = $DB->get_record_select('grading_instances', $select, $params, 'id', IGNORE_MISSING)) {
                return $current->id;
            }
        } else {
            if ($current = $DB->get_record_select('grading_instances', $select, $params, '*', IGNORE_MISSING)) {
                return $this->get_instance($current);
            }
        }
        return null;
    }

    /**
     * Returns list of ACTIVE instances for the specified $itemid
     * (intentionally does not return instances with status NEEDUPDATE)
     *
     * @param int $itemid
     * @return array of gradingform_instance objects
     */
    public function get_active_instances($itemid) {
        global $DB;
        $conditions = array('definitionid'  => $this->definition->id,
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
     * Returns an array of all active instances for this definition.
     * (intentionally does not return instances with status NEEDUPDATE)
     *
     * @param int since only return instances with timemodified >= since
     * @return array of gradingform_instance objects
     */
    public function get_all_active_instances($since = 0) {
        global $DB;
        $conditions = array ($this->definition->id,
                             gradingform_instance::INSTANCE_STATUS_ACTIVE,
                             $since);
        $where = "definitionid = ? AND status = ? AND timemodified >= ?";
        $records = $DB->get_records_select('grading_instances', $where, $conditions);
        $rv = array();
        foreach ($records as $record) {
            $rv[] = $this->get_instance($record);
        }
        return $rv;
    }

    /**
     * Returns true if there are already people who has been graded on this definition.
     * In this case plugins may restrict changes of the grading definition
     *
     * @return boolean
     */
    public function has_active_instances() {
        global $DB;
        if (empty($this->definition->id)) {
            return false;
        }
        if ($this->hasactiveinstances === null) {
            $conditions = array('definitionid'  => $this->definition->id,
                        'status'  => gradingform_instance::INSTANCE_STATUS_ACTIVE);
            $this->hasactiveinstances = $DB->record_exists('grading_instances', $conditions);
        }
        return $this->hasactiveinstances;
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
    public function create_instance($raterid, $itemid = null) {

        // first find if there is already an active instance for this itemid
        if ($itemid && $current = $this->get_current_instance($raterid, $itemid)) {
            return $this->get_instance($current->copy($raterid, $itemid));
        } else {
            $class = 'gradingform_'. $this->get_method_name(). '_instance';
            return $this->get_instance($class::create_new($this->definition->id, $raterid, $itemid));
        }
    }

    /**
     * If instanceid is specified and grading instance exists and it is created by this rater for
     * this item, this instance is returned.
     * Otherwise new instance is created for the specified rater and itemid
     *
     * @param int $instanceid
     * @param int $raterid
     * @param int $itemid
     * @return gradingform_instance
     */
    public function get_or_create_instance($instanceid, $raterid, $itemid) {
        global $DB;
        if ($instanceid &&
                $instance = $DB->get_record('grading_instances', array('id'  => $instanceid, 'raterid' => $raterid, 'itemid' => $itemid), '*', IGNORE_MISSING)) {
            return $this->get_instance($instance);
        }
        return $this->create_instance($raterid, $itemid);
    }

    /**
     * Returns the HTML code displaying the preview of the grading form
     *
     * Plugins are forced to override this. Ideally they should delegate
     * the task to their own renderer.
     *
     * @param moodle_page $page the target page
     * @return string
     */
    abstract public function render_preview(moodle_page $page);

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
        $DB->delete_records('grading_instances', array('definitionid' => $this->definition->id));
        // finally, delete the main definition record
        $DB->delete_records('grading_definitions', array('id' => $this->definition->id));

        $this->definition = false;
    }

    /**
     * Prepare the part of the search query to append to the FROM statement
     *
     * @param string $gdid the alias of grading_definitions.id column used by the caller
     * @return string
     */
    public static function sql_search_from_tables($gdid) {
        return '';
    }

    /**
     * Prepare the parts of the SQL WHERE statement to search for the given token
     *
     * The returned array cosists of the list of SQL comparions and the list of
     * respective parameters for the comparisons. The returned chunks will be joined
     * with other conditions using the OR operator.
     *
     * @param string $token token to search for
     * @return array
     */
    public static function sql_search_where($token) {

        $subsql = array();
        $params = array();

        return array($subsql, $params);
    }

    // //////////////////////////////////////////////////////////////////////////

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
     * Returns the name of the grading method plugin, eg 'rubric'
     *
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
     * @param array $gradinginfo result of function grade_get_grades if plugin want to use some of their info
     * @param string $defaultcontent default string to be returned if no active grading is found or for some reason can not be shown to a user
     * @param boolean $cangrade whether current user has capability to grade in this context
     * @return string
     */
    public function render_grade($page, $itemid, $gradinginfo, $defaultcontent, $cangrade) {
        return $defaultcontent;
    }

    /**
     * Sets the range of grades used in this area. This is usually either range like 0-100
     * or the scale where keys start from 1.
     *
     * Typically modules will call it:
     * $controller->set_grade_range(make_grades_menu($gradingtype), $gradingtype > 0);
     * Negative $gradingtype means that scale is used and the grade must be rounded
     * to the nearest int. Positive $gradingtype means that range 0..$gradingtype
     * is used for the grades and in this case grade does not have to be rounded.
     *
     * Sometimes modules always expect grade to be rounded (like mod_assignment does).
     *
     * @param array $graderange array where first _key_ is the minimum grade and the
     *     last key is the maximum grade.
     * @param bool $allowgradedecimals if decimal values are allowed as grades.
     */
    public final function set_grade_range(array $graderange, $allowgradedecimals = false) {
        $this->graderange = $graderange;
        $this->allowgradedecimals = $allowgradedecimals;
    }

    /**
     * Returns the range of grades used in this area
     *
     * @return array
     */
    public final function get_grade_range() {
        if (empty($this->graderange)) {
            return array();
        }
        return $this->graderange;
    }

    /**
     * Returns if decimal values are allowed as grades
     *
     * @return bool
     */
    public final function get_allow_grade_decimals() {
        return $this->allowgradedecimals;
    }

    /**
     * Overridden by sub classes that wish to make definition details available to web services.
     * When not overridden, only definition data common to all grading methods is made available.
     * When overriding, the return value should be an array containing one or more key/value pairs.
     * These key/value pairs should match the definition returned by the get_definition() function.
     * For examples, look at:
     *    $gradingform_rubric_controller->get_external_definition_details()
     *    $gradingform_guide_controller->get_external_definition_details()
     * @return array An array of one or more key/value pairs containing the external_multiple_structure/s
     * corresponding to the definition returned by $controller->get_definition()
     * @since Moodle 2.5
     */
    public static function get_external_definition_details() {
        return null;
    }

    /**
     * Overridden by sub classes that wish to make instance filling details available to web services.
     * When not overridden, only instance filling data common to all grading methods is made available.
     * When overriding, the return value should be an array containing one or more key/value pairs.
     * These key/value pairs should match the filling data returned by the get_<method>_filling() function
     * in the gradingform_instance subclass.
     * For examples, look at:
     *    $gradingform_rubric_controller->get_external_instance_filling_details()
     *    $gradingform_guide_controller->get_external_instance_filling_details()
     *
     * @return array An array of one or more key/value pairs containing the external_multiple_structure/s
     * corresponding to the definition returned by $gradingform_<method>_instance->get_<method>_filling()
     * @since Moodle 2.6
     */
    public static function get_external_instance_filling_details() {
        return null;
    }
}

/**
 * Class to manage one gradingform instance.
 *
 * Gradingform instance is created for each evaluation of a student, using advanced grading.
 * It is stored as an entry in the DB table gradingform_instance.
 *
 * One instance (usually the latest) has the status INSTANCE_STATUS_ACTIVE. Sometimes it may
 * happen that a teacher wants to change the definition when some students have already been
 * graded. In this case their instances change status to INSTANCE_STATUS_NEEDUPDATE.
 *
 * To support future use of AJAX for background saving of incomplete evaluations the
 * status INSTANCE_STATUS_INCOMPLETE is introduced. If 'Cancel' is pressed this entry
 * is deleted.
 * When grade is updated the previous active instance receives status INSTANCE_STATUS_ACTIVE.
 *
 * Advanced grading plugins must declare a class gradingform_xxxx_instance
 * extending this class and put it in lib.php in the plugin folder.
 *
 * The reference to an instance of this class is passed to an advanced grading form element
 * included in the grading form, so this class must implement functions for rendering
 * and validation of this form element. See {@link MoodleQuickForm_grading}
 *
 * @package    core_grading
 * @copyright  2011 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @category   grading
 */
abstract class gradingform_instance {
    /** Valid istance status */
    const INSTANCE_STATUS_ACTIVE = 1;
    /** The grade needs to be updated by grader (usually because of changes is grading method) */
    const INSTANCE_STATUS_NEEDUPDATE = 2;
    /** The grader started grading but did clicked neither submit nor cancel */
    const INSTANCE_STATUS_INCOMPLETE = 0;
    /** Grader re-graded the student and this is the status for previous grade stored as history */
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
     * @param int $definitionid
     * @param int $raterid
     * @param int $itemid
     * @return int id of the created instance
     */
    public static function create_new($definitionid, $raterid, $itemid) {
        global $DB;
        $instance = new stdClass();
        $instance->definitionid = $definitionid;
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
     * Returns the current (active or needupdate) instance for the same raterid and itemid as this
     * instance. This function is useful to find the status of the currently modified instance
     *
     * @return gradingform_instance
     */
    public function get_current_instance() {
        if ($this->get_status() == self::INSTANCE_STATUS_ACTIVE || $this->get_status() == self::INSTANCE_STATUS_NEEDUPDATE) {
            return $this;
        }
        return $this->get_controller()->get_current_instance($this->data->raterid, $this->data->itemid);
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
     * Returns the specified element from object $this->data
     *
     * @param string $key
     * @return mixed
     */
    public function get_data($key) {
        if (isset($this->data->$key)) {
            return $this->data->$key;
        }
        return null;
    }

    /**
     * Returns instance id
     *
     * @return int
     */
    public function get_id() {
        return $this->get_data('id');
    }

    /**
     * Returns instance status
     *
     * @return int
     */
    public function get_status() {
        return $this->get_data('status');
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
        if (empty($this->data->itemid)) {
            throw new coding_exception('You cannot mark active the grading instance without itemid');
        }
        $currentid = $this->get_controller()->get_current_instance($this->data->raterid, $this->data->itemid, true);
        if ($currentid && $currentid != $this->get_id()) {
            $DB->update_record('grading_instances', array('id' => $currentid, 'status' => self::INSTANCE_STATUS_ARCHIVE));
        }
        $DB->update_record('grading_instances', array('id' => $this->get_id(), 'status' => self::INSTANCE_STATUS_ACTIVE));
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
        // TODO MDL-31239 throw exception if status is not INSTANCE_STATUS_INCOMPLETE
        $DB->delete_records('grading_instances', array('id' => $this->get_id()));
    }

    /**
     * Updates the instance with the data received from grading form. This function may be
     * called via AJAX when grading is not yet completed, so it does not change the
     * status of the instance.
     *
     * @param array $elementvalue
     */
    public function update($elementvalue) {
        global $DB;
        $newdata = new stdClass();
        $newdata->id = $this->get_id();
        $newdata->timemodified = time();
        if (isset($elementvalue['itemid']) && $elementvalue['itemid'] != $this->data->itemid) {
            $newdata->itemid = $elementvalue['itemid'];
        }
        // TODO MDL-31087 also update: rawgrade, feedback, feedbackformat
        $DB->update_record('grading_instances', $newdata);
        foreach ($newdata as $key => $value) {
            $this->data->$key = $value;
        }
    }

    /**
     * Calculates the grade to be pushed to the gradebook
     *
     * Returned grade must be in range $this->get_controller()->get_grade_range()
     * Plugins must returned grade converted to int unless
     * $this->get_controller()->get_allow_grade_decimals() is true.
     *
     * @return float|int
     */
    abstract public function get_grade();

    /**
     * Determines whether the submitted form was empty.
     *
     * @param array $elementvalue value of element submitted from the form
     * @return boolean true if the form is empty
     */
    public function is_empty_form($elementvalue) {
        return false;
    }

    /**
     * Removes the attempt from the gradingform_*_fillings table.
     * This function is not abstract as to not break plugins that might
     * use advanced grading.
     * @param array $data the attempt data
     */
    public function clear_attempt($data) {
        // This function is empty because the way to clear a grade
        // attempt will be different depending on the grading method.
        return;
    }

    /**
     * Called when teacher submits the grading form:
     * updates the instance in DB, marks it as ACTIVE and returns the grade to be pushed to the gradebook.
     * $itemid must be specified here (it was not required when the instance was
     * created, because it might not existed in draft)
     *
     * @param array $elementvalue
     * @param int $itemid
     * @return int the grade on 0-100 scale
     */
    public function submit_and_get_grade($elementvalue, $itemid) {
        $elementvalue['itemid'] = $itemid;
        if ($this->is_empty_form($elementvalue)) {
            $this->clear_attempt($elementvalue);
            $this->make_active();
            return -1;
        }
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
     * ( {NAME} is a shortcut for $gradingformelement->getName() )
     * After submitting the form the value of $_POST[{NAME}] is passed to the functions
     * validate_grading_element() and submit_and_get_grade()
     *
     * Plugins may use $gradingformelement->getValue() to get the value passed on previous
     * form submit
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
     * Please note that in assignments grading in 2.2 the grading form is not validated
     * properly and this message is not being displayed.
     *
     * @see validate_grading_element()
     * @return string
     */
    public function default_validation_error_message() {
        return '';
    }
}
