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
 * This file contains the definition for the class assignment
 *
 * This class provides all the functionality for the new assign module.
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Assignment submission statuses
 */
define('ASSIGN_SUBMISSION_STATUS_DRAFT', 'draft'); // student thinks it is a draft
define('ASSIGN_SUBMISSION_STATUS_SUBMITTED', 'submitted'); // student thinks it is finished

/**
 * Search filters for grading page
 */
define('ASSIGN_FILTER_SUBMITTED', 'submitted');
define('ASSIGN_FILTER_SINGLE_USER', 'singleuser');
define('ASSIGN_FILTER_REQUIRE_GRADING', 'require_grading');

/**
 * File areas for assignment portfolio if enabled
 */
define('ASSIGN_FILEAREA_PORTFOLIO_FILES', 'portfolio_files');


/** Include accesslib.php */
require_once($CFG->libdir.'/accesslib.php');
/** Include formslib.php */
require_once($CFG->libdir.'/formslib.php');
/** Include repository/lib.php */
require_once($CFG->dirroot . '/repository/lib.php');
/** Include local mod_form.php */
require_once($CFG->dirroot.'/mod/assign/mod_form.php');
/** Include portfoliolib.php */
require_once($CFG->libdir . '/portfoliolib.php');
/** gradelib.php */
require_once($CFG->libdir.'/gradelib.php');
/** grading lib.php */
require_once($CFG->dirroot.'/grade/grading/lib.php');
/** Include feedbackplugin.php */
require_once($CFG->dirroot.'/mod/assign/feedbackplugin.php');
/** Include submissionplugin.php */
require_once($CFG->dirroot.'/mod/assign/submissionplugin.php');
/** Include renderable.php */
require_once($CFG->dirroot.'/mod/assign/renderable.php');
/** Include gradingtable.php */
require_once($CFG->dirroot.'/mod/assign/gradingtable.php');
/** Include eventslib.php */
require_once($CFG->libdir.'/eventslib.php');


/**
 * Standard base class for mod_assign (assignment types).
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign {


    /** @var stdClass the assignment record that contains the global settings for this assign instance */
    private $instance;

    /** @var context the context of the course module for this assign instance (or just the course if we are
        creating a new one) */
    private $context;

    /** @var stdClass the course this assign instance belongs to */
    private $course;

    /** @var assign_renderer the custom renderer for this module */
    private $output;

    /** @var stdClass the course module for this assign instance */
    private $coursemodule;

    /** @var array cache for things like the coursemodule name or the scale menu - only lives for a single
        request */
    private $cache;

    /** @var array list of the installed submission plugins */
    private $submissionplugins;

    /** @var array list of the installed feedback plugins */
    private $feedbackplugins;

    /** @var string action to be used to return to this page (without repeating any form submissions etc.) */
    private $returnaction = 'view';

    /** @var array params to be used to return to this page */
    private $returnparams = array();

    /** @var string modulename prevents excessive calls to get_string */
    private static $modulename = '';

    /** @var string modulenameplural prevents excessive calls to get_string */
    private static $modulenameplural = '';

    /**
     * Constructor for the base assign class
     *
     * @param mixed $coursemodulecontext context|null the course module context (or the course context if the coursemodule has not been created yet)
     * @param mixed $coursemodule the current course module if it was already loaded - otherwise this class will load one from the context as required
     * @param mixed $course the current course  if it was already loaded - otherwise this class will load one from the context as required
     */
    public function __construct($coursemodulecontext, $coursemodule, $course) {
        global $PAGE;

        $this->context = $coursemodulecontext;
        $this->coursemodule = $coursemodule;
        $this->course = $course;
        $this->cache = array(); // temporary cache only lives for a single request - used to reduce db lookups

        $this->submissionplugins = $this->load_plugins('assignsubmission');
        $this->feedbackplugins = $this->load_plugins('assignfeedback');
        $this->output = $PAGE->get_renderer('mod_assign');
    }

    /**
     * Set the action and parameters that can be used to return to the current page
     *
     * @param string $action The action for the current page
     * @param array $params An array of name value pairs which form the parameters to return to the current page
     * @return void
     */
    public function register_return_link($action, $params) {
        $this->returnaction = $action;
        $this->returnparams = $params;
    }

    /**
     * Return an action that can be used to get back to the current page
     * @return string action
     */
    public function get_return_action() {
        return $this->returnaction;
    }

    /**
     * Based on the current assignment settings should we display the intro
     * @return bool showintro
     */
    private function show_intro() {
        if ($this->get_instance()->alwaysshowdescription ||
                time() > $this->get_instance()->allowsubmissionsfromdate) {
            return true;
        }
        return false;
    }

    /**
     * Return a list of parameters that can be used to get back to the current page
     * @return array params
     */
    public function get_return_params() {
        return $this->returnparams;
    }

    /**
     * Set the submitted form data
     * @param stdClass $data The form data (instance)
     */
    public function set_instance(stdClass $data) {
        $this->instance = $data;
    }

    /**
     * Set the context
     * @param context $context The new context
     */
    public function set_context(context $context) {
        $this->context = $context;
    }

    /**
     * Set the course data
     * @param stdClass $course The course data
     */
    public function set_course(stdClass $course) {
        $this->course = $course;
    }

    /**
     * get list of feedback plugins installed
     * @return array
     */
    public function get_feedback_plugins() {
        return $this->feedbackplugins;
    }

    /**
     * get list of submission plugins installed
     * @return array
     */
    public function get_submission_plugins() {
        return $this->submissionplugins;
    }


    /**
     * get a specific submission plugin by its type
     * @param string $subtype assignsubmission | assignfeedback
     * @param string $type
     * @return mixed assign_plugin|null
     */
    private function get_plugin_by_type($subtype, $type) {
        $shortsubtype = substr($subtype, strlen('assign'));
        $name = $shortsubtype . 'plugins';
        $pluginlist = $this->$name;
        foreach ($pluginlist as $plugin) {
            if ($plugin->get_type() == $type) {
                return $plugin;
            }
        }
        return null;
    }

    /**
     * Get a feedback plugin by type
     * @param string $type - The type of plugin e.g comments
     * @return mixed assign_feedback_plugin|null
     */
    public function get_feedback_plugin_by_type($type) {
        return $this->get_plugin_by_type('assignfeedback', $type);
    }

    /**
     * Get a submission plugin by type
     * @param string $type - The type of plugin e.g comments
     * @return mixed assign_submission_plugin|null
     */
    public function get_submission_plugin_by_type($type) {
        return $this->get_plugin_by_type('assignsubmission', $type);
    }

    /**
     * Load the plugins from the sub folders under subtype
     * @param string $subtype - either submission or feedback
     * @return array - The sorted list of plugins
     */
    private function load_plugins($subtype) {
        global $CFG;
        $result = array();

        $names = get_plugin_list($subtype);

        foreach ($names as $name => $path) {
            if (file_exists($path . '/locallib.php')) {
                require_once($path . '/locallib.php');

                $shortsubtype = substr($subtype, strlen('assign'));
                $pluginclass = 'assign_' . $shortsubtype . '_' . $name;

                $plugin = new $pluginclass($this, $name);

                if ($plugin instanceof assign_plugin) {
                    $idx = $plugin->get_sort_order();
                    while (array_key_exists($idx, $result)) $idx +=1;
                    $result[$idx] = $plugin;
                }
            }
        }
        ksort($result);
        return $result;
    }


    /**
     * Display the assignment, used by view.php
     *
     * The assignment is displayed differently depending on your role,
     * the settings for the assignment and the status of the assignment.
     * @param string $action The current action if any.
     * @return void
     */
    public function view($action='') {

        $o = '';
        $mform = null;

        // handle form submissions first
        if ($action == 'savesubmission') {
            $action = 'editsubmission';
            if ($this->process_save_submission($mform)) {
                $action = 'view';
            }
         } else if ($action == 'lock') {
            $this->process_lock();
            $action = 'grading';
         } else if ($action == 'reverttodraft') {
            $this->process_revert_to_draft();
            $action = 'grading';
         } else if ($action == 'unlock') {
            $this->process_unlock();
            $action = 'grading';
         } else if ($action == 'confirmsubmit') {
            $this->process_submit_for_grading();
            // save and show next button
         } else if ($action == 'batchgradingoperation') {
            $this->process_batch_grading_operation();
            $action = 'grading';
         } else if ($action == 'submitgrade') {
            if (optional_param('saveandshownext', null, PARAM_ALPHA)) {
                //save and show next
                $action = 'grade';
                if ($this->process_save_grade($mform)) {
                    $action = 'nextgrade';
                }
            } else if (optional_param('nosaveandprevious', null, PARAM_ALPHA)) {
                $action = 'previousgrade';
            } else if (optional_param('nosaveandnext', null, PARAM_ALPHA)) {
                //show next button
                $action = 'nextgrade';
            } else if (optional_param('savegrade', null, PARAM_ALPHA)) {
                //save changes button
                $action = 'grade';
                if ($this->process_save_grade($mform)) {
                    $action = 'grading';
                }
            } else {
                //cancel button
                $action = 'grading';
            }
        }else if ($action == 'saveoptions') {
            $this->process_save_grading_options();
            $action = 'grading';
        }

        $returnparams = array('rownum'=>optional_param('rownum', 0, PARAM_INT));
        $this->register_return_link($action, $returnparams);

        // now show the right view page
        if ($action == 'previousgrade') {
            $mform = null;
            $o .= $this->view_single_grade_page($mform, -1);
        } else if ($action == 'nextgrade') {
            $mform = null;
            $o .= $this->view_single_grade_page($mform, 1);
        } else if ($action == 'redirect') {
            redirect(required_param('url', PARAM_TEXT));
        } else if ($action == 'grade') {
            $o .= $this->view_single_grade_page($mform);
        } else if ($action == 'viewpluginassignfeedback') {
            $o .= $this->view_plugin_content('assignfeedback');
        } else if ($action == 'viewpluginassignsubmission') {
            $o .= $this->view_plugin_content('assignsubmission');
        } else if ($action == 'editsubmission') {
            $o .= $this->view_edit_submission_page($mform);
        } else if ($action == 'grading') {
            $o .= $this->view_grading_page();
        } else if ($action == 'downloadall') {
            $o .= $this->download_submissions();
        } else if ($action == 'submit') {
            $o .= $this->check_submit_for_grading();
        } else {
            $o .= $this->view_submission_page();
        }

        return $o;
    }


    /**
     * Add this instance to the database
     *
     * @param stdClass $formdata The data submitted from the form
     * @param bool $callplugins This is used to skip the plugin code
     *             when upgrading an old assignment to a new one (the plugins get called manually)
     * @return mixed false if an error occurs or the int id of the new instance
     */
    public function add_instance(stdClass $formdata, $callplugins) {
        global $DB;

        $err = '';

        // add the database record
        $update = new stdClass();
        $update->name = $formdata->name;
        $update->timemodified = time();
        $update->timecreated = time();
        $update->course = $formdata->course;
        $update->courseid = $formdata->course;
        $update->intro = $formdata->intro;
        $update->introformat = $formdata->introformat;
        $update->alwaysshowdescription = $formdata->alwaysshowdescription;
        $update->preventlatesubmissions = $formdata->preventlatesubmissions;
        $update->submissiondrafts = $formdata->submissiondrafts;
        $update->sendnotifications = $formdata->sendnotifications;
        $update->duedate = $formdata->duedate;
        $update->allowsubmissionsfromdate = $formdata->allowsubmissionsfromdate;
        $update->grade = $formdata->grade;
        $returnid = $DB->insert_record('assign', $update);
        $this->instance = $DB->get_record('assign', array('id'=>$returnid), '*', MUST_EXIST);
        // cache the course record
        $this->course = $DB->get_record('course', array('id'=>$formdata->course), '*', MUST_EXIST);

        if ($callplugins) {
            // call save_settings hook for submission plugins
            foreach ($this->submissionplugins as $plugin) {
                if (!$this->update_plugin_instance($plugin, $formdata)) {
                    print_error($plugin->get_error());
                    return false;
                }
            }
            foreach ($this->feedbackplugins as $plugin) {
                if (!$this->update_plugin_instance($plugin, $formdata)) {
                    print_error($plugin->get_error());
                    return false;
                }
            }

            // in the case of upgrades the coursemodule has not been set so we need to wait before calling these two
            // TODO: add event to the calendar
            $this->update_calendar($formdata->coursemodule);
            // TODO: add the item in the gradebook
            $this->update_gradebook(false, $formdata->coursemodule);

        }

        $update = new stdClass();
        $update->id = $this->get_instance()->id;
        $update->nosubmissions = (!$this->is_any_submission_plugin_enabled()) ? 1: 0;
        $DB->update_record('assign', $update);

        return $returnid;
    }

    /**
     * Delete all grades from the gradebook for this assignment
     *
     * @return bool
     */
    private function delete_grades() {
        global $CFG;

        return grade_update('mod/assign', $this->get_course()->id, 'mod', 'assign', $this->get_instance()->id, 0, NULL, array('deleted'=>1)) == GRADE_UPDATE_OK;
    }

    /**
     * Delete this instance from the database
     *
     * @return bool false if an error occurs
     */
    public function delete_instance() {
        global $DB;
        $result = true;

        foreach ($this->submissionplugins as $plugin) {
            if (!$plugin->delete_instance()) {
                print_error($plugin->get_error());
                $result = false;
            }
        }
        foreach ($this->feedbackplugins as $plugin) {
            if (!$plugin->delete_instance()) {
                print_error($plugin->get_error());
                $result = false;
            }
        }

        // delete files associated with this assignment
        $fs = get_file_storage();
        if (! $fs->delete_area_files($this->context->id) ) {
            $result = false;
        }

        // delete_records will throw an exception if it fails - so no need for error checking here

        $DB->delete_records('assign_submission', array('assignment'=>$this->get_instance()->id));
        $DB->delete_records('assign_grades', array('assignment'=>$this->get_instance()->id));
        $DB->delete_records('assign_plugin_config', array('assignment'=>$this->get_instance()->id));

        // delete items from the gradebook
        if (! $this->delete_grades()) {
            $result = false;
        }

        // delete the instance
        $DB->delete_records('assign', array('id'=>$this->get_instance()->id));

        return $result;
    }

    /**
     * Update the settings for a single plugin
     *
     * @param assign_plugin $plugin The plugin to update
     * @param stdClass $formdata The form data
     * @return bool false if an error occurs
     */
    private function update_plugin_instance(assign_plugin $plugin, stdClass $formdata) {
        if ($plugin->is_visible()) {
            $enabledname = $plugin->get_subtype() . '_' . $plugin->get_type() . '_enabled';
            if ($formdata->$enabledname) {
                $plugin->enable();
                if (!$plugin->save_settings($formdata)) {
                    print_error($plugin->get_error());
                    return false;
                }
            } else {
                $plugin->disable();
            }
        }
        return true;
    }

    /**
     * Update the gradebook information for this assignment
     *
     * @param bool $reset If true, will reset all grades in the gradbook for this assignment
     * @param int $coursemoduleid This is required because it might not exist in the database yet
     * @return bool
     */
    public function update_gradebook($reset, $coursemoduleid) {
         global $CFG;
        /** Include lib.php */
        require_once($CFG->dirroot.'/mod/assign/lib.php');
        $assign = clone $this->get_instance();
        $assign->cmidnumber = $coursemoduleid;
        $param = null;
        if ($reset) {
            $param = 'reset';
        }

        return assign_grade_item_update($assign, $param);
    }


    /**
     * Update the calendar entries for this assignment
     *
     * @param int $coursemoduleid - Required to pass this in because it might not exist in the database yet
     * @return bool
     */
    public function update_calendar($coursemoduleid) {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/calendar/lib.php');

        // special case for add_instance as the coursemodule has not been set yet.

        if ($this->get_instance()->duedate) {
            $event = new stdClass();

            if ($event->id = $DB->get_field('event', 'id', array('modulename'=>'assign', 'instance'=>$this->get_instance()->id))) {

                $event->name        = $this->get_instance()->name;

                $event->description = format_module_intro('assign', $this->get_instance(), $coursemoduleid);
                $event->timestart   = $this->get_instance()->duedate;

                $calendarevent = calendar_event::load($event->id);
                $calendarevent->update($event);
            } else {
                $event = new stdClass();
                $event->name        = $this->get_instance()->name;
                $event->description = format_module_intro('assign', $this->get_instance(), $coursemoduleid);
                $event->courseid    = $this->get_instance()->course;
                $event->groupid     = 0;
                $event->userid      = 0;
                $event->modulename  = 'assign';
                $event->instance    = $this->get_instance()->id;
                $event->eventtype   = 'due';
                $event->timestart   = $this->get_instance()->duedate;
                $event->timeduration = 0;

                calendar_event::create($event);
            }
        } else {
            $DB->delete_records('event', array('modulename'=>'assign', 'instance'=>$this->get_instance()->id));
        }
    }


    /**
     * Update this instance in the database
     *
     * @param stdClass $formdata - the data submitted from the form
     * @return bool false if an error occurs
     */
    public function update_instance($formdata) {
        global $DB;

        $update = new stdClass();
        $update->id = $formdata->instance;
        $update->name = $formdata->name;
        $update->timemodified = time();
        $update->course = $formdata->course;
        $update->intro = $formdata->intro;
        $update->introformat = $formdata->introformat;
        $update->alwaysshowdescription = $formdata->alwaysshowdescription;
        $update->preventlatesubmissions = $formdata->preventlatesubmissions;
        $update->submissiondrafts = $formdata->submissiondrafts;
        $update->sendnotifications = $formdata->sendnotifications;
        $update->duedate = $formdata->duedate;
        $update->allowsubmissionsfromdate = $formdata->allowsubmissionsfromdate;
        $update->grade = $formdata->grade;

        $result = $DB->update_record('assign', $update);
        $this->instance = $DB->get_record('assign', array('id'=>$update->id), '*', MUST_EXIST);

        // load the assignment so the plugins have access to it

        // call save_settings hook for submission plugins
        foreach ($this->submissionplugins as $plugin) {
            if (!$this->update_plugin_instance($plugin, $formdata)) {
                print_error($plugin->get_error());
                return false;
            }
        }
        foreach ($this->feedbackplugins as $plugin) {
            if (!$this->update_plugin_instance($plugin, $formdata)) {
                print_error($plugin->get_error());
                return false;
            }
        }


        // update the database record


        // update all the calendar events
        $this->update_calendar($this->get_course_module()->id);

        $this->update_gradebook(false, $this->get_course_module()->id);

        $update = new stdClass();
        $update->id = $this->get_instance()->id;
        $update->nosubmissions = (!$this->is_any_submission_plugin_enabled()) ? 1: 0;
        $DB->update_record('assign', $update);





        return $result;
    }

    /**
     * add elements in grading plugin form
     *
     * @param mixed $grade stdClass|null
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @return void
     */
    private function add_plugin_grade_elements($grade, MoodleQuickForm $mform, stdClass $data) {
        foreach ($this->feedbackplugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {
                $mform->addElement('header', 'header_' . $plugin->get_type(), $plugin->get_name());
                if (!$plugin->get_form_elements($grade, $mform, $data)) {
                    $mform->removeElement('header_' . $plugin->get_type());
                }
            }
        }
    }



    /**
     * Add one plugins settings to edit plugin form
     *
     * @param assign_plugin $plugin The plugin to add the settings from
     * @param MoodleQuickForm $mform The form to add the configuration settings to. This form is modified directly (not returned)
     * @return void
     */
    private function add_plugin_settings(assign_plugin $plugin, MoodleQuickForm $mform) {
        global $CFG;
        if ($plugin->is_visible()) {
            // enabled
            //tied disableIf rule to this select element
            $mform->addElement('selectyesno', $plugin->get_subtype() . '_' . $plugin->get_type() . '_enabled', $plugin->get_name());
            $mform->addHelpButton($plugin->get_subtype() . '_' . $plugin->get_type() . '_enabled', 'enabled', $plugin->get_subtype() . '_' . $plugin->get_type());

            $setting = $plugin->get_subtype() . '_' . $plugin->get_type() . '_default';

            $default = $CFG->$setting;
            if ($plugin->get_config('enabled') !== false) {
                $default = $plugin->is_enabled();
            }
            $mform->setDefault($plugin->get_subtype() . '_' . $plugin->get_type() . '_enabled', $default);

            $plugin->get_settings($mform);

        }

    }


    /**
     * Add settings to edit plugin form
     *
     * @param MoodleQuickForm $mform The form to add the configuration settings to. This form is modified directly (not returned)
     * @return void
     */
    public function add_all_plugin_settings(MoodleQuickForm $mform) {
        $mform->addElement('header', 'general', get_string('submissionsettings', 'assign'));

        foreach ($this->submissionplugins as $plugin) {
            $this->add_plugin_settings($plugin, $mform);

        }
        $mform->addElement('header', 'general', get_string('feedbacksettings', 'assign'));
        foreach ($this->feedbackplugins as $plugin) {
            $this->add_plugin_settings($plugin, $mform);
        }
    }

    /**
     * Allow each plugin an opportunity to update the defaultvalues
     * passed in to the settings form (needed to set up draft areas for
     * editor and filemanager elements)
     * @param array $defaultvalues
     */
    public function plugin_data_preprocessing(&$defaultvalues) {
        foreach ($this->submissionplugins as $plugin) {
            if ($plugin->is_visible()) {
                $plugin->data_preprocessing($defaultvalues);
            }
        }
        foreach ($this->feedbackplugins as $plugin) {
            if ($plugin->is_visible()) {
                $plugin->data_preprocessing($defaultvalues);
            }
        }
    }

    /**
     * Get the name of the current module.
     *
     * @return string the module name (Assignment)
     */
    protected function get_module_name() {
        if (isset(self::$modulename)) {
            return self::$modulename;
        }
        self::$modulename = get_string('modulename', 'assign');
        return self::$modulename;
    }

    /**
     * Get the plural name of the current module.
     *
     * @return string the module name plural (Assignments)
     */
    protected function get_module_name_plural() {
        if (isset(self::$modulenameplural)) {
            return self::$modulenameplural;
        }
        self::$modulenameplural = get_string('modulenameplural', 'assign');
        return self::$modulenameplural;
    }

    /**
     * Has this assignment been constructed from an instance?
     *
     * @return bool
     */
    public function has_instance() {
        return $this->instance || $this->get_course_module();
    }

    /**
     * Get the settings for the current instance of this assignment
     *
     * @return stdClass The settings
     */
    public function get_instance() {
        global $DB;
        if ($this->instance) {
            return $this->instance;
        }
        if ($this->get_course_module()) {
            $this->instance = $DB->get_record('assign', array('id' => $this->get_course_module()->instance), '*', MUST_EXIST);
        }
        if (!$this->instance) {
            throw new coding_exception('Improper use of the assignment class. Cannot load the assignment record.');
        }
        return $this->instance;
    }

    /**
     * Get the context of the current course
     * @return mixed context|null The course context
     */
    public function get_course_context() {
        if (!$this->context && !$this->course) {
            throw new coding_exception('Improper use of the assignment class. Cannot load the course context.');
        }
        if ($this->context) {
            return $this->context->get_course_context();
        } else {
            return context_course::instance($this->course->id);
        }
    }


    /**
     * Get the current course module
     *
     * @return mixed stdClass|null The course module
     */
    public function get_course_module() {
        if ($this->coursemodule) {
            return $this->coursemodule;
        }
        if (!$this->context) {
            return null;
        }

        if ($this->context->contextlevel == CONTEXT_MODULE) {
            $this->coursemodule = get_coursemodule_from_id('assign', $this->context->instanceid, 0, false, MUST_EXIST);
            return $this->coursemodule;
        }
        return null;
    }

    /**
     * Get context module
     *
     * @return context
     */
    public function get_context() {
        return $this->context;
    }

    /**
     * Get the current course
     * @return mixed stdClass|null The course
     */
    public function get_course() {
        global $DB;
        if ($this->course) {
            return $this->course;
        }

        if (!$this->context) {
            return null;
        }
        $this->course = $DB->get_record('course', array('id' => $this->get_course_context()->instanceid), '*', MUST_EXIST);
        return $this->course;
    }

    /**
     * Return a grade in user-friendly form, whether it's a scale or not
     *
     * @param mixed $grade int|null
     * @return string User-friendly representation of grade
     */
    public function display_grade($grade) {
        global $DB;

        static $scalegrades = array();



        if ($this->get_instance()->grade >= 0) {    // Normal number
            if ($grade == -1 || $grade === null) {
                return '-';
            } else {
                return format_float(($grade),2) .'&nbsp;/&nbsp;'. format_float($this->get_instance()->grade,2);
            }

        } else {                                // Scale
            if (empty($this->cache['scale'])) {
                if ($scale = $DB->get_record('scale', array('id'=>-($this->get_instance()->grade)))) {
                    $this->cache['scale'] = make_menu_from_list($scale->scale);
                } else {
                    return '-';
                }
            }
            $scaleid = (int)$grade;
            if (isset($this->cache['scale'][$scaleid])) {
                return $this->cache['scale'][$scaleid];
            }
            return '-';
        }
    }

    /**
     * Load a list of users enrolled in the current course with the specified permission and group (0 for no group)
     *
     * @param int $currentgroup
     * @param bool $idsonly
     * @return array List of user records
     */
    public function list_participants($currentgroup, $idsonly) {
        if ($idsonly) {
            return get_enrolled_users($this->context, "mod/assign:submit", $currentgroup, 'u.id');
        } else {
            return get_enrolled_users($this->context, "mod/assign:submit", $currentgroup);
        }
    }

    /**
     * Load a count of users enrolled in the current course with the specified permission and group (0 for no group)
     *
     * @param int $currentgroup
     * @return int number of matching users
     */
    public function count_participants($currentgroup) {
        return count_enrolled_users($this->context, "mod/assign:submit", $currentgroup);
    }

    /**
     * Load a count of users enrolled in the current course with the specified permission and group (optional)
     *
     * @param string $status The submission status - should match one of the constants
     * @return int number of matching submissions
     */
    public function count_submissions_with_status($status) {
        global $DB;
        return $DB->count_records_sql("SELECT COUNT('x')
                                     FROM {assign_submission}
                                    WHERE assignment = ? AND
                                          status = ?", array($this->get_course_module()->instance, $status));
    }

    /**
     * Utility function to get the userid for every row in the grading table
     * so the order can be frozen while we iterate it
     *
     * @return array An array of userids
     */
    private function get_grading_userid_list(){
        $filter = get_user_preferences('assign_filter', '');
        $table = new assign_grading_table($this, 0, $filter);

        $useridlist = $table->get_column_data('userid');

        return $useridlist;
    }


    /**
     * Utility function get the userid based on the row number of the grading table.
     * This takes into account any active filters on the table.
     *
     * @param int $num The row number of the user
     * @param bool $last This is set to true if this is the last user in the table
     * @return mixed The user id of the matching user or false if there was an error
     */
    private function get_userid_for_row($num, $last){
        if (!array_key_exists('userid_for_row', $this->cache)) {
            $this->cache['userid_for_row'] = array();
        }
        if (array_key_exists($num, $this->cache['userid_for_row'])) {
            list($userid, $last) = $this->cache['userid_for_row'][$num];
            return $userid;
        }

        $filter = get_user_preferences('assign_filter', '');
        $table = new assign_grading_table($this, 0, $filter);

        $userid = $table->get_cell_data($num, 'userid', $last);

        $this->cache['userid_for_row'][$num] = array($userid, $last);
        return $userid;
    }

    /**
     * Return all assignment submissions by ENROLLED students (even empty)
     *
     * @param string $sort optional field names for the ORDER BY in the sql query
     * @param string $dir optional specifying the sort direction, defaults to DESC
     * @return array The submission objects indexed by id
     */
    private function get_all_submissions( $sort="", $dir="DESC") {
        global $CFG, $DB;

        if ($sort == "lastname" or $sort == "firstname") {
            $sort = "u.$sort $dir";
        } else if (empty($sort)) {
            $sort = "a.timemodified DESC";
        } else {
            $sort = "a.$sort $dir";
        }

        return $DB->get_records_sql("SELECT a.*
                                       FROM {assign_submission} a, {user} u
                                      WHERE u.id = a.userid
                                            AND a.assignment = ?
                                   ORDER BY $sort", array($this->get_instance()->id));

    }

    /**
     * Generate zip file from array of given files
     *
     * @param array $filesforzipping - array of files to pass into archive_to_pathname - this array is indexed by the final file name and each element in the array is an instance of a stored_file object
     * @return path of temp file - note this returned file does not have a .zip extension - it is a temp file.
     */
     private function pack_files($filesforzipping) {
         global $CFG;
         //create path for new zip file.
         $tempzip = tempnam($CFG->tempdir.'/', 'assignment_');
         //zip files
         $zipper = new zip_packer();
         if ($zipper->archive_to_pathname($filesforzipping, $tempzip)) {
             return $tempzip;
         }
         return false;
    }


    /**
     *  Cron function to be run periodically according to the moodle cron
     *  Finds all assignment notifications that have yet to be mailed out, and mails them
     *
     * @return bool
     */
    static function cron() {
        global $CFG, $USER, $DB;


        return true;
    }

    /**
     * Update a grade in the grade table for the assignment and in the gradebook
     *
     * @param stdClass $grade a grade record keyed on id
     * @return bool true for success
     */
    private function update_grade($grade) {
        global $DB;

        $grade->timemodified = time();

        if ($grade->grade && $grade->grade != -1) {
            if ($this->get_instance()->grade > 0) {
                if (!is_numeric($grade->grade)) {
                    return false;
                } else if ($grade->grade > $this->get_instance()->grade) {
                    return false;
                } else if ($grade->grade < 0) {
                    return false;
                }
            } else {
                // this is a scale
                if ($scale = $DB->get_record('scale', array('id' => -($this->get_instance()->grade)))) {
                    $scaleoptions = make_menu_from_list($scale->scale);
                    if (!array_key_exists((int) $grade->grade, $scaleoptions)) {
                        return false;
                    }
                }
            }
        }

        $result = $DB->update_record('assign_grades', $grade);
        if ($result) {
            $this->gradebook_item_update(null, $grade);
        }
        return $result;
    }

    /**
     * display the submission that is used by a plugin
     * Uses url parameters 'sid', 'gid' and 'plugin'
     * @param string $pluginsubtype
     * @return string
     */
    private function view_plugin_content($pluginsubtype) {
        global $USER;

        $o = '';

        $submissionid = optional_param('sid', 0, PARAM_INT);
        $gradeid = optional_param('gid', 0, PARAM_INT);
        $plugintype = required_param('plugin', PARAM_TEXT);
        $item = null;
        if ($pluginsubtype == 'assignsubmission') {
            $plugin = $this->get_submission_plugin_by_type($plugintype);
            if ($submissionid <= 0) {
                throw new coding_exception('Submission id should not be 0');
            }
            $item = $this->get_submission($submissionid);

            // permissions
            if ($item->userid != $USER->id) {
                require_capability('mod/assign:grade', $this->context);
            }
            $o .= $this->output->render(new assign_header($this->get_instance(),
                                                              $this->get_context(),
                                                              $this->show_intro(),
                                                              $this->get_course_module()->id,
                                                              $plugin->get_name()));
            $o .= $this->output->render(new assign_submission_plugin_submission($plugin,
                                                              $item,
                                                              assign_submission_plugin_submission::FULL,
                                                              $this->get_course_module()->id,
                                                              $this->get_return_action(),
                                                              $this->get_return_params()));

            $this->add_to_log('view submission', get_string('viewsubmissionforuser', 'assign', $item->userid));
        } else {
            $plugin = $this->get_feedback_plugin_by_type($plugintype);
            if ($gradeid <= 0) {
                throw new coding_exception('Grade id should not be 0');
            }
            $item = $this->get_grade($gradeid);
            // permissions
            if ($item->userid != $USER->id) {
                require_capability('mod/assign:grade', $this->context);
            }
            $o .= $this->output->render(new assign_header($this->get_instance(),
                                                              $this->get_context(),
                                                              $this->show_intro(),
                                                              $this->get_course_module()->id,
                                                              $plugin->get_name()));
            $o .= $this->output->render(new assign_feedback_plugin_feedback($plugin,
                                                              $item,
                                                              assign_feedback_plugin_feedback::FULL,
                                                              $this->get_course_module()->id,
                                                              $this->get_return_action(),
                                                              $this->get_return_params()));
            $this->add_to_log('view feedback', get_string('viewfeedbackforuser', 'assign', $item->userid));
        }


        $o .= $this->view_return_links();

        $o .= $this->view_footer();
        return $o;
    }

    /**
     * render the content in editor that is often used by plugin
     *
     * @param string $filearea
     * @param int  $submissionid
     * @param string $plugintype
     * @param string $editor
     * @param string $component
     * @return string
     */
    public function render_editor_content($filearea, $submissionid, $plugintype, $editor, $component) {
        global $CFG;

        $result = '';

        $plugin = $this->get_submission_plugin_by_type($plugintype);

        $text = $plugin->get_editor_text($editor, $submissionid);
        $format = $plugin->get_editor_format($editor, $submissionid);

        $finaltext = file_rewrite_pluginfile_urls($text, 'pluginfile.php', $this->get_context()->id, $component, $filearea, $submissionid);
        $result .= format_text($finaltext, $format, array('overflowdiv' => true, 'context' => $this->get_context()));



        if ($CFG->enableportfolios) {
            require_once($CFG->libdir . '/portfoliolib.php');

            $button = new portfolio_add_button();
            $button->set_callback_options('assign_portfolio_caller', array('cmid' => $this->get_course_module()->id, 'sid' => $submissionid, 'plugin' => $plugintype, 'editor' => $editor, 'area'=>$filearea), '/mod/assign/portfolio_callback.php');
            $fs = get_file_storage();

            if ($files = $fs->get_area_files($this->context->id, $component,$filearea, $submissionid, "timemodified", false)) {
                $button->set_formats(PORTFOLIO_FORMAT_RICHHTML);
            } else {
                $button->set_formats(PORTFOLIO_FORMAT_PLAINHTML);
            }
            $result .= $button->to_html();
        }
        return $result;
    }


    /**
     * Display the page footer
     *
     * @return None
     */
    private function view_footer() {
        return $this->output->render_footer();
    }

    /**
     * Does this user have grade permission for this assignment
     *
     * @return bool
     */
    private function can_grade() {
        // Permissions check
        if (!has_capability('mod/assign:grade', $this->context)) {
            return false;
        }

        return true;
    }

    /**
     * Download a zip file of all assignment submissions
     *
     * @return void
     */
    private function download_submissions() {
        global $CFG,$DB;

        // more efficient to load this here
        require_once($CFG->libdir.'/filelib.php');

        // load all submissions
        $submissions = $this->get_all_submissions('','');

        if (empty($submissions)) {
            print_error('errornosubmissions', 'assign');
            return;
        }

        // build a list of files to zip
        $filesforzipping = array();
        $fs = get_file_storage();

        $groupmode = groups_get_activity_groupmode($this->get_course_module());
        $groupid = 0;   // All users
        $groupname = '';
        if ($groupmode) {
            $groupid = groups_get_activity_group($this->get_course_module(), true);
            $groupname = groups_get_group_name($groupid).'-';
        }

        // construct the zip file name
        $filename = str_replace(' ', '_', clean_filename($this->get_course()->shortname.'-'.$this->get_instance()->name.'-'.$groupname.$this->get_course_module()->id.".zip")); //name of new zip file.

        // get all the files for each submission
        foreach ($submissions as $submission) {
            $userid = $submission->userid; //get userid
            if ((groups_is_member($groupid,$userid) or !$groupmode or !$groupid)) {
                // get the plugins to add their own files to the zip

                $user = $DB->get_record("user", array("id"=>$userid),'id,username,firstname,lastname', MUST_EXIST);

                $prefix = clean_filename(fullname($user) . "_" .$userid . "_");

                foreach ($this->submissionplugins as $plugin) {
                    if ($plugin->is_enabled() && $plugin->is_visible()) {
                        $pluginfiles = $plugin->get_files($submission);


                        foreach ($pluginfiles as $zipfilename => $file) {
                            $filesforzipping[$prefix . $zipfilename] = $file;
                        }
                    }
                }

            }
        } // end of foreach loop
        if ($zipfile = $this->pack_files($filesforzipping)) {
            $this->add_to_log('download all submissions', get_string('downloadall', 'assign'));
            send_temp_file($zipfile, $filename); //send file and delete after sending.
        }
    }

    /**
     * Util function to add a message to the log
     *
     * @param string $action The current action
     * @param string $info A detailed description of the change. But no more than 255 characters.
     * @param string $url The url to the assign module instance.
     * @return void
     */
    public function add_to_log($action = '', $info = '', $url='') {
        global $USER;

        $fullurl = 'view.php?id=' . $this->get_course_module()->id;
        if ($url != '') {
            $fullurl .= '&' . $url;
        }

        add_to_log($this->get_course()->id, 'assign', $action, $fullurl, $info, $this->get_course_module()->id, $USER->id);
    }

    /**
     * Load the submission object for a particular user, optionally creating it if required
     *
     * @param int $userid The id of the user whose submission we want or 0 in which case USER->id is used
     * @param bool $create optional Defaults to false. If set to true a new submission object will be created in the database
     * @return stdClass The submission
     */
    private function get_user_submission($userid, $create) {
        global $DB, $USER;

        if (!$userid) {
            $userid = $USER->id;
        }
        // if the userid is not null then use userid
        $submission = $DB->get_record('assign_submission', array('assignment'=>$this->get_instance()->id, 'userid'=>$userid));

        if ($submission) {
            return $submission;
        }
        if ($create) {
            $submission = new stdClass();
            $submission->assignment   = $this->get_instance()->id;
            $submission->userid       = $userid;
            $submission->timecreated = time();
            $submission->timemodified = $submission->timecreated;

            if ($this->get_instance()->submissiondrafts) {
                $submission->status = ASSIGN_SUBMISSION_STATUS_DRAFT;
            } else {
                $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
            }
            $sid = $DB->insert_record('assign_submission', $submission);
            $submission->id = $sid;
            return $submission;
        }
        return false;
    }

    /**
     * Load the submission object from it's id
     *
     * @param int $submissionid The id of the submission we want
     * @return stdClass The submission
     */
    private function get_submission($submissionid) {
        global $DB;

        return $DB->get_record('assign_submission', array('assignment'=>$this->get_instance()->id, 'id'=>$submissionid), '*', MUST_EXIST);
    }

    /**
     * This will retrieve a grade object from the db, optionally creating it if required
     *
     * @param int $userid The user we are grading
     * @param bool $create If true the grade will be created if it does not exist
     * @return stdClass The grade record
     */
    private function get_user_grade($userid, $create) {
        global $DB, $USER;

        if (!$userid) {
            $userid = $USER->id;
        }

        // if the userid is not null then use userid
        $grade = $DB->get_record('assign_grades', array('assignment'=>$this->get_instance()->id, 'userid'=>$userid));

        if ($grade) {
            return $grade;
        }
        if ($create) {
            $grade = new stdClass();
            $grade->assignment   = $this->get_instance()->id;
            $grade->userid       = $userid;
            $grade->timecreated = time();
            $grade->timemodified = $grade->timecreated;
            $grade->locked = 0;
            $grade->grade = -1;
            $grade->grader = $USER->id;
            $gid = $DB->insert_record('assign_grades', $grade);
            $grade->id = $gid;
            return $grade;
        }
        return false;
    }

    /**
     * This will retrieve a grade object from the db
     *
     * @param int $gradeid The id of the grade
     * @return stdClass The grade record
     */
    private function get_grade($gradeid) {
        global $DB;

        return $DB->get_record('assign_grades', array('assignment'=>$this->get_instance()->id, 'id'=>$gradeid), '*', MUST_EXIST);
    }

    /**
     * Print the grading page for a single user submission
     *
     * @param moodleform $mform
     * @param int $offset
     * @return string
     */
    private function view_single_grade_page($mform, $offset=0) {
        global $DB, $CFG;

        $o = '';

        // Include grade form
        require_once($CFG->dirroot . '/mod/assign/gradeform.php');

        // Need submit permission to submit an assignment
        require_capability('mod/assign:grade', $this->context);

        $o .= $this->output->render(new assign_header($this->get_instance(),
                                                      $this->get_context(), false, $this->get_course_module()->id,get_string('grading', 'assign')));

        $rownum = required_param('rownum', PARAM_INT) + $offset;
        $useridlist = optional_param('useridlist', '', PARAM_TEXT);
        if ($useridlist) {
            $useridlist = explode(',', $useridlist);
        } else {
            $useridlist = $this->get_grading_userid_list();
        }
        $last = false;
        $userid = $useridlist[$rownum];
        if ($rownum == count($useridlist) - 1) {
            $last = true;
        }
        // the placement of this is important so can pass the list of userids above
        if ($offset) {
            $_POST = array();
        }
        if(!$userid){
            throw new coding_exception('Row is out of bounds for the current grading table: ' . $rownum);
        }
        $user = $DB->get_record('user', array('id' => $userid));
        if ($user) {
            $o .= $this->output->render(new assign_user_summary($user, $this->get_course()->id, has_capability('moodle/site:viewfullnames', $this->get_course_context())));
        }
        $submission = $this->get_user_submission($userid, false);
        // get the current grade
        $grade = $this->get_user_grade($userid, false);
        if ($this->can_view_submission($userid)) {
            $gradelocked = ($grade && $grade->locked) || $this->grading_disabled($userid);
            $o .= $this->output->render(new assign_submission_status($this->get_instance()->allowsubmissionsfromdate,
                                                              $this->get_instance()->alwaysshowdescription,
                                                              $submission,
                                                              $this->is_any_submission_plugin_enabled(),
                                                              $gradelocked,
                                                              $this->is_graded($userid),
                                                              $this->get_instance()->duedate,
                                                              $this->get_submission_plugins(),
                                                              $this->get_return_action(),
                                                              $this->get_return_params(),
                                                              $this->get_course_module()->id,
                                                              assign_submission_status::GRADER_VIEW,
                                                              false,
                                                              false));
        }
        if ($grade) {
            $data = new stdClass();
            if ($grade->grade >= 0) {
                $data->grade = format_float($grade->grade,2);
            }
        } else {
            $data = new stdClass();
            $data->grade = '';
        }

        // now show the grading form
        if (!$mform) {
            $mform = new mod_assign_grade_form(null, array($this, $data, array('rownum'=>$rownum, 'useridlist'=>$useridlist, 'last'=>$last)), 'post', '', array('class'=>'gradeform'));
        }
        $o .= $this->output->render(new assign_form('gradingform',$mform));

        $this->add_to_log('view grading form', get_string('viewgradingformforstudent', 'assign', array('id'=>$user->id, 'fullname'=>fullname($user))));

        $o .= $this->view_footer();
        return $o;
    }



    /**
     * View a link to go back to the previous page. Uses url parameters returnaction and returnparams.
     *
     * @return string
     */
    private function view_return_links() {

        $returnaction = optional_param('returnaction','', PARAM_ALPHA);
        $returnparams = optional_param('returnparams','', PARAM_TEXT);

        $params = array();
        parse_str($returnparams, $params);
        $params = array_merge( array('id' => $this->get_course_module()->id, 'action' => $returnaction), $params);

        return $this->output->single_button(new moodle_url('/mod/assign/view.php', $params), get_string('back'), 'get');

    }

    /**
     * View the grading table of all submissions for this assignment
     *
     * @return string
     */
    private function view_grading_table() {
        global $USER, $CFG;
        // Include grading options form
        require_once($CFG->dirroot . '/mod/assign/gradingoptionsform.php');
        require_once($CFG->dirroot . '/mod/assign/gradingactionsform.php');
        require_once($CFG->dirroot . '/mod/assign/gradingbatchoperationsform.php');
        $o = '';

        $links = array();
        $selecturl = (string)(new moodle_url('/mod/assign/view.php',
                                             array('action'=>'grading', 'id'=>$this->get_course_module()->id)));
        $links[$selecturl] = get_string('selectlink', 'assign');
        if (has_capability('gradereport/grader:view', $this->get_course_context()) &&
                has_capability('moodle/grade:viewall', $this->get_course_context())) {
            $gradebookurl = (string) (new moodle_url('/grade/report/grader/index.php',
                                                     array('id' => $this->get_course()->id)));
            $links[$gradebookurl] = get_string('viewgradebook', 'assign');
        }
        if ($this->is_any_submission_plugin_enabled()) {
            $downloadurl = (string) (new moodle_url('/mod/assign/view.php',
                                                    array('id' => $this->get_course_module()->id,
                                                          'action' => 'downloadall')));
            $links[$downloadurl] = get_string('downloadall', 'assign');
        }
        $gradingactionsform = new mod_assign_grading_actions_form(null,
                                                                  array('links'=>$links,
                                                                        'cm'=>$this->get_course_module()->id),
                                                                  'post', '',
                                                                  array('class'=>'gradingactionsform'));

        $perpage = get_user_preferences('assign_perpage', 10);
        $filter = get_user_preferences('assign_filter', '');
        // print options  for changing the filter and changing the number of results per page
        $gradingoptionsform = new mod_assign_grading_options_form(null,
                                                                  array('cm'=>$this->get_course_module()->id,
                                                                        'contextid'=>$this->context->id,
                                                                        'userid'=>$USER->id),
                                                                  'post', '',
                                                                  array('class'=>'gradingoptionsform'));

        $gradingbatchoperationsform = new mod_assign_grading_batch_operations_form(null,
                                                                  array('cm'=>$this->get_course_module()->id,
                                                                        'submissiondrafts'=>$this->get_instance()->submissiondrafts),
                                                                  'post', '',
                                                                  array('class'=>'gradingbatchoperationsform'));

        $gradingoptionsdata = new stdClass();
        $gradingoptionsdata->perpage = $perpage;
        $gradingoptionsdata->filter = $filter;
        $gradingoptionsform->set_data($gradingoptionsdata);

        // plagiarism update status apearring in the grading book
        if (!empty($CFG->enableplagiarism)) {
            /** Include plagiarismlib.php */
            require_once($CFG->libdir . '/plagiarismlib.php');
            plagiarism_update_status($this->get_course(), $this->get_course_module());
        }

        $o .= $this->output->render(new assign_form('gradingactionsform', $gradingactionsform));
        $o .= $this->output->render(new assign_form('gradingoptionsform', $gradingoptionsform));

        // load and print the table of submissions
        $o .= $this->output->render(new assign_grading_table($this, $perpage, $filter));

        $currentgroup = groups_get_activity_group($this->get_course_module(), true);
        $users = array_keys($this->list_participants($currentgroup, true));
        if (count($users) != 0) {
            // if no enrolled user in a course then don't display the batch operations feature
            $o .= $this->output->render(new assign_form('gradingbatchoperationsform', $gradingbatchoperationsform));
        }
        return $o;
    }

    /**
     * View entire grading page.
     *
     * @return string
     */
    private function view_grading_page() {
        global $CFG;

        $o = '';
        // Need submit permission to submit an assignment
        require_capability('mod/assign:grade', $this->context);
        require_once($CFG->dirroot . '/mod/assign/gradeform.php');

        // only load this if it is

        $o .= $this->output->render(new assign_header($this->get_instance(),
                                                      $this->get_context(), false, $this->get_course_module()->id, get_string('grading', 'assign')));
        $o .= groups_print_activity_menu($this->get_course_module(), $CFG->wwwroot . '/mod/assign/view.php?id=' . $this->get_course_module()->id.'&action=grading', true);


        $o .= $this->view_grading_table();

        $o .= $this->view_footer();
        $this->add_to_log('view submission grading table', get_string('viewsubmissiongradingtable', 'assign'));
        return $o;
    }

    /**
     * Capture the output of the plagiarism plugins disclosures and return it as a string
     *
     * @return void
     */
    private function plagiarism_print_disclosure() {
        global $CFG;
        $o = '';

        if (!empty($CFG->enableplagiarism)) {
            /** Include plagiarismlib.php */
            require_once($CFG->libdir . '/plagiarismlib.php');
            ob_start();

            plagiarism_print_disclosure($this->get_course_module()->id);
            $o = ob_get_contents();
            ob_end_clean();
        }

        return $o;
    }

    /**
     * message for students when assignment submissions have been closed
     *
     * @return string
     */
    private function view_student_error_message() {
        global $CFG;

        $o = '';
        // Need submit permission to submit an assignment
        require_capability('mod/assign:submit', $this->context);

        $o .= $this->output->render(new assign_header($this->get_instance(),
                                                      $this->get_context(),
                                                      $this->show_intro(),
                                                      $this->get_course_module()->id,
                                                      get_string('editsubmission', 'assign')));

        $o .= $this->output->notification('This assignment is no longer accepting submissions');

        $o .= $this->view_footer();

        return $o;

    }

    /**
     * View edit submissions page.
     *
     * @param moodleform $mform
     * @return void
     */
    private function view_edit_submission_page($mform) {
        global $CFG;

        $o = '';
        // Include submission form
        require_once($CFG->dirroot . '/mod/assign/submission_form.php');
        // Need submit permission to submit an assignment
        require_capability('mod/assign:submit', $this->context);

        if (!$this->submissions_open()) {
            $subclosed  = '';
            $subclosed .= $this->view_student_error_message();
            return $subclosed;
        }
        $o .= $this->output->render(new assign_header($this->get_instance(),
                                                      $this->get_context(),
                                                      $this->show_intro(),
                                                      $this->get_course_module()->id,
                                                      get_string('editsubmission', 'assign')));
        $o .= $this->plagiarism_print_disclosure();
        $data = new stdClass();

        if (!$mform) {
            $mform = new mod_assign_submission_form(null, array($this, $data));
        }

        $o .= $this->output->render(new assign_form('editsubmissionform',$mform));

        $o .= $this->view_footer();
        $this->add_to_log('view submit assignment form', get_string('viewownsubmissionform', 'assign'));

        return $o;
    }

    /**
     * See if this assignment has a grade yet
     *
     * @param int $userid
     * @return bool
     */
    private function is_graded($userid) {
        $grade = $this->get_user_grade($userid, false);
        if ($grade) {
            return ($grade->grade != '');
        }
        return false;
    }


    /**
     * Perform an access check to see if the current $USER can view this users submission
     *
     * @param int $userid
     * @return bool
     */
    public function can_view_submission($userid) {
        global $USER;

        if (!is_enrolled($this->get_course_context(), $userid)) {
            return false;
        }
        if ($userid == $USER->id && !has_capability('mod/assign:submit', $this->context)) {
            return false;
        }
        if ($userid != $USER->id && !has_capability('mod/assign:grade', $this->context)) {
            return false;
        }
        return true;
    }

    /**
     * Ask the user to confirm they want to perform this batch operation
     * @return string
     */
    private function process_batch_grading_operation() {
        global $CFG;
        require_once($CFG->dirroot . '/mod/assign/gradingbatchoperationsform.php');
        require_sesskey();

        $gradingbatchoperationsform = new mod_assign_grading_batch_operations_form(null,
                                                                  array('cm'=>$this->get_course_module()->id,
                                                                        'submissiondrafts'=>$this->get_instance()->submissiondrafts),
                                                                  'post', '',
                                                                  array('class'=>'gradingbatchoperationsform'));

        if ($data = $gradingbatchoperationsform->get_data()) {
            // get the list of users
            $users = $data->selectedusers;
            $userlist = explode(',', $users);

            foreach ($userlist as $userid) {
                if ($data->operation == 'lock') {
                    $this->process_lock($userid);
                } else if ($data->operation == 'unlock') {
                    $this->process_unlock($userid);
                } else if ($data->operation == 'reverttodraft') {
                    $this->process_revert_to_draft($userid);
                }
            }
        }

        return true;
    }

    /**
     * Ask the user to confirm they want to submit their work for grading
     * @return string
     */
    private function check_submit_for_grading() {
        global $USER;
        // Check that all of the submission plugins are ready for this submission
        $notifications = array();
        $submission = $this->get_user_submission($USER->id, false);
        $plugins = $this->get_submission_plugins();
        foreach ($plugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {
                $check = $plugin->precheck_submission($submission);
                if ($check !== true) {
                    $notifications[] = $check;
                }
            }
        }

        $o = '';
        $o .= $this->output->header();
        $o .= $this->output->render(new assign_submit_for_grading_page($notifications, $this->get_course_module()->id));
        $o .= $this->view_footer();
        return $o;
    }

    /**
     * Print 2 tables of information with no action links -
     * the submission summary and the grading summary
     *
     * @param stdClass $user the user to print the report for
     * @param bool $showlinks - Return plain text or links to the profile
     * @return string - the html summary
     */
    public function view_student_summary($user, $showlinks) {
        global $CFG, $DB, $PAGE;

        $grade = $this->get_user_grade($user->id, false);
        $submission = $this->get_user_submission($user->id, false);
        $o = '';

        if ($this->can_view_submission($user->id)) {
            $showedit = has_capability('mod/assign:submit', $this->context) &&
                         $this->submissions_open() && ($this->is_any_submission_plugin_enabled()) && $showlinks;
            $showsubmit = $submission && ($submission->status == ASSIGN_SUBMISSION_STATUS_DRAFT) && $showlinks;
            $gradelocked = ($grade && $grade->locked) || $this->grading_disabled($user->id);

            $o .= $this->output->render(new assign_submission_status($this->get_instance()->allowsubmissionsfromdate,
                                                              $this->get_instance()->alwaysshowdescription,
                                                              $submission,
                                                              $this->is_any_submission_plugin_enabled(),
                                                              $gradelocked,
                                                              $this->is_graded($user->id),
                                                              $this->get_instance()->duedate,
                                                              $this->get_submission_plugins(),
                                                              $this->get_return_action(),
                                                              $this->get_return_params(),
                                                              $this->get_course_module()->id,
                                                              assign_submission_status::STUDENT_VIEW,
                                                              $showedit,
                                                              $showsubmit));
            require_once($CFG->libdir.'/gradelib.php');
            require_once($CFG->dirroot.'/grade/grading/lib.php');

            $gradinginfo = grade_get_grades($this->get_course()->id,
                                        'mod',
                                        'assign',
                                        $this->get_instance()->id,
                                        $user->id);

            $gradingitem = $gradinginfo->items[0];
            $gradebookgrade = $gradingitem->grades[$user->id];

            // check to see if all feedback plugins are empty
            $emptyplugins = true;
            if ($grade) {
                foreach ($this->get_feedback_plugins() as $plugin) {
                    if ($plugin->is_visible() && $plugin->is_enabled()) {
                        if (!$plugin->is_empty($grade)) {
                            $emptyplugins = false;
                        }
                    }
                }
            }


            if (!($gradebookgrade->hidden) && ($gradebookgrade->grade !== null || !$emptyplugins)) {

                $gradefordisplay = '';
                $gradingmanager = get_grading_manager($this->get_context(), 'mod_assign', 'submissions');

                if ($controller = $gradingmanager->get_active_controller()) {
                    $controller->set_grade_range(make_grades_menu($this->get_instance()->grade));
                    $gradefordisplay = $controller->render_grade($PAGE,
                                                                 $grade->id,
                                                                 $gradingitem,
                                                                 $gradebookgrade->str_long_grade,
                                                                 has_capability('mod/assign:grade', $this->get_context()));
                } else {
                    $gradefordisplay = $this->display_grade($gradebookgrade->grade);
                }

                $gradeddate = $gradebookgrade->dategraded;
                $grader = $DB->get_record('user', array('id'=>$gradebookgrade->usermodified));

                $feedbackstatus = new assign_feedback_status($gradefordisplay,
                                                      $gradeddate,
                                                      $grader,
                                                      $this->get_feedback_plugins(),
                                                      $grade,
                                                      $this->get_course_module()->id,
                                                      $this->get_return_action(),
                                                      $this->get_return_params());

                $o .= $this->output->render($feedbackstatus);
            }

        }
        return $o;
    }

    /**
     * View submissions page (contains details of current submission).
     *
     * @return string
     */
    private function view_submission_page() {
        global $CFG, $DB, $USER, $PAGE;

        $o = '';
        $o .= $this->output->render(new assign_header($this->get_instance(),
                                                      $this->get_context(),
                                                      $this->show_intro(),
                                                      $this->get_course_module()->id));

        if ($this->can_grade()) {
            $o .= $this->output->render(new assign_grading_summary($this->count_participants(0),
                                                            $this->get_instance()->submissiondrafts,
                                                            $this->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_DRAFT),
                                                            $this->is_any_submission_plugin_enabled(),
                                                            $this->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_SUBMITTED),
                                                            $this->get_instance()->duedate,
                                                            $this->get_course_module()->id
                                                            ));
        }
        $grade = $this->get_user_grade($USER->id, false);
        $submission = $this->get_user_submission($USER->id, false);

        if ($this->can_view_submission($USER->id)) {
            $o .= $this->view_student_summary($USER, true);
        }


        $o .= $this->view_footer();
        $this->add_to_log('view', get_string('viewownsubmissionstatus', 'assign'));
        return $o;
    }

    /**
     * convert the final raw grade(s) in the  grading table for the gradebook
     *
     * @param stdClass $grade
     * @return array
     */
    private function convert_grade_for_gradebook(stdClass $grade) {
        $gradebookgrade = array();
        // trying to match those array keys in grade update function in gradelib.php
        // with keys in th database table assign_grades
        // starting around line 262
        $gradebookgrade['rawgrade'] = $grade->grade;
        $gradebookgrade['userid'] = $grade->userid;
        $gradebookgrade['usermodified'] = $grade->grader;
        $gradebookgrade['datesubmitted'] = NULL;
        $gradebookgrade['dategraded'] = $grade->timemodified;
        if (isset($grade->feedbackformat)) {
            $gradebookgrade['feedbackformat'] = $grade->feedbackformat;
        }
        if (isset($grade->feedbacktext)) {
            $gradebookgrade['feedback'] = $grade->feedbacktext;
        }

        return $gradebookgrade;
    }

    /**
     * convert submission details for the gradebook
     *
     * @param stdClass $submission
     * @return array
     */
    private function convert_submission_for_gradebook(stdClass $submission) {
        $gradebookgrade = array();


        $gradebookgrade['userid'] = $submission->userid;
        $gradebookgrade['usermodified'] = $submission->userid;
        $gradebookgrade['datesubmitted'] = $submission->timemodified;

        return $gradebookgrade;
    }

    /**
     * update grades in the gradebook
     *
     * @param mixed $submission stdClass|null
     * @param mixed $grade stdClass|null
     * @return bool
     */
    private function gradebook_item_update($submission=NULL, $grade=NULL) {

        if($submission != NULL){

            $gradebookgrade = $this->convert_submission_for_gradebook($submission);


        }else{


            $gradebookgrade = $this->convert_grade_for_gradebook($grade);
        }
        $assign = clone $this->get_instance();
        $assign->cmidnumber = $this->get_course_module()->id;

        return assign_grade_item_update($assign, $gradebookgrade);
    }

    /**
     * update grades in the gradebook based on submission time
     *
     * @param stdClass $submission
     * @param bool $updatetime
     * @return bool
     */
    private function update_submission(stdClass $submission, $updatetime=true) {
        global $DB;

        if ($updatetime) {
            $submission->timemodified = time();
        }
        $result= $DB->update_record('assign_submission', $submission);
        if ($result) {
            $this->gradebook_item_update($submission);
        }
        return $result;
    }

    /**
     * Is this assignment open for submissions?
     *
     * Check the due date,
     * prevent late submissions,
     * has this person already submitted,
     * is the assignment locked?
     *
     * @return bool
     */
    private function submissions_open() {
        global $USER;

        $time = time();
        $dateopen = true;
        if ($this->get_instance()->preventlatesubmissions && $this->get_instance()->duedate) {
            $dateopen = ($this->get_instance()->allowsubmissionsfromdate <= $time && $time <= $this->get_instance()->duedate);
        } else {
            $dateopen = ($this->get_instance()->allowsubmissionsfromdate <= $time);
        }

        if (!$dateopen) {
            return false;
        }

        // now check if this user has already submitted etc.
        if (!is_enrolled($this->get_course_context(), $USER)) {
            return false;
        }
        if ($submission = $this->get_user_submission($USER->id, false)) {
            if ($this->get_instance()->submissiondrafts && $submission->status == ASSIGN_SUBMISSION_STATUS_SUBMITTED) {
                // drafts are tracked and the student has submitted the assignment
                return false;
            }
        }
        if ($grade = $this->get_user_grade($USER->id, false)) {
            if ($grade->locked) {
                return false;
            }
        }

        if ($this->grading_disabled($USER->id)) {
            return false;
        }

        return true;
    }

    /**
     * render the files in file area
     * @param string $component
     * @param string $area
     * @param int $submissionid
     * @return string
     */
    public function render_area_files($component, $area, $submissionid) {
        global $USER;

        if (!$submissionid) {
            $submission = $this->get_user_submission($USER->id,false);
            $submissionid = $submission->id;
        }



        $fs = get_file_storage();
        $browser = get_file_browser();
        $files = $fs->get_area_files($this->get_context()->id, $component, $area , $submissionid , "timemodified", false);
        return $this->output->assign_files($this->context, $submissionid, $area, $component);

    }

    /**
     * Returns a list of teachers that should be grading given submission
     *
     * @param stdClass $user
     * @return array
     */
    private function get_graders(stdClass $user) {
        //potential graders
        $potentialgraders = get_users_by_capability($this->context, 'mod/assign:grade', '', '', '', '', '', '', false, false);

        $graders = array();
        if (groups_get_activity_groupmode($this->get_course_module()) == SEPARATEGROUPS) {   // Separate groups are being used
            if ($groups = groups_get_all_groups($this->get_course()->id, $user->id)) {  // Try to find all groups
                foreach ($groups as $group) {
                    foreach ($potentialgraders as $grader) {
                        if ($grader->id == $user->id) {
                            continue; // do not send self
                        }
                        if (groups_is_member($group->id, $grader->id)) {
                            $graders[$grader->id] = $grader;
                        }
                    }
                }
            } else {
                // user not in group, try to find graders without group
                foreach ($potentialgraders as $grader) {
                    if ($grader->id == $user->id) {
                        continue; // do not send self
                    }
                    if (!groups_has_membership($this->get_course_module(), $grader->id)) {
                        $graders[$grader->id] = $grader;
                    }
                }
            }
        } else {
            foreach ($potentialgraders as $grader) {
                if ($grader->id == $user->id) {
                    continue; // do not send self
                }
                // must be enrolled
                if (is_enrolled($this->get_course_context(), $grader->id)) {
                    $graders[$grader->id] = $grader;
                }
            }
        }
        return $graders;
    }

    /**
     * Creates the text content for emails to grader
     *
     * @param array $info The info used by the 'emailgradermail' language string
     * @return string
     */
    private function format_email_grader_text($info) {
        $posttext  = format_string($this->get_course()->shortname, true, array('context' => $this->get_course_context())).' -> '.
                     $this->get_module_name().' -> '.
                     format_string($this->get_instance()->name, true, array('context' => $this->context))."\n";
        $posttext .= '---------------------------------------------------------------------'."\n";
        $posttext .= get_string("emailgradermail", "assign", $info)."\n";
        $posttext .= "\n---------------------------------------------------------------------\n";
        return $posttext;
    }

     /**
     * Creates the html content for emails to graders
     *
     * @param array $info The info used by the 'emailgradermailhtml' language string
     * @return string
     */
    private function format_email_grader_html($info) {
        global $CFG;
        $posthtml  = '<p><font face="sans-serif">'.
                     '<a href="'.$CFG->wwwroot.'/course/view.php?id='.$this->get_course()->id.'">'.format_string($this->get_course()->shortname, true, array('context' => $this->get_course_context())).'</a> ->'.
                     '<a href="'.$CFG->wwwroot.'/mod/assignment/index.php?id='.$this->get_course()->id.'">'.$this->get_module_name().'</a> ->'.
                     '<a href="'.$CFG->wwwroot.'/mod/assignment/view.php?id='.$this->get_course_module()->id.'">'.format_string($this->get_instance()->name, true, array('context' => $this->context)).'</a></font></p>';
        $posthtml .= '<hr /><font face="sans-serif">';
        $posthtml .= '<p>'.get_string('emailgradermailhtml', 'assign', $info).'</p>';
        $posthtml .= '</font><hr />';
        return $posthtml;
    }

    /**
     * email graders upon student submissions
     *
     * @param stdClass $submission
     * @return void
     */
    private function email_graders(stdClass $submission) {
        global $CFG, $DB;

        if (empty($this->get_instance()->sendnotifications)) {          // No need to do anything
            return;
        }

        $user = $DB->get_record('user', array('id'=>$submission->userid), '*', MUST_EXIST);

        if ($teachers = $this->get_graders($user)) {

            $strassignments = $this->get_module_name_plural();
            $strassignment  = $this->get_module_name();
            $strsubmitted  = get_string('submitted', 'assign');

            foreach ($teachers as $teacher) {
                $info = new stdClass();
                $info->username = fullname($user, true);
                $info->assignment = format_string($this->get_instance()->name,true, array('context'=>$this->get_context()));
                $info->url = $CFG->wwwroot.'/mod/assign/view.php?id='.$this->get_course_module()->id;
                $info->timeupdated = strftime('%c',$submission->timemodified);

                $postsubject = $strsubmitted.': '.$info->username.' -> '.$this->get_instance()->name;
                $posttext = $this->format_email_grader_text($info);
                $posthtml = ($teacher->mailformat == 1) ? $this->format_email_grader_html($info) : '';

                $eventdata = new stdClass();
                $eventdata->modulename       = 'assign';
                $eventdata->userfrom         = $user;
                $eventdata->userto           = $teacher;
                $eventdata->subject          = $postsubject;
                $eventdata->fullmessage      = $posttext;
                $eventdata->fullmessageformat = FORMAT_PLAIN;
                $eventdata->fullmessagehtml  = $posthtml;
                $eventdata->smallmessage     = $postsubject;

                $eventdata->name            = 'assign_updates';
                $eventdata->component       = 'mod_assign';
                $eventdata->notification    = 1;
                $eventdata->contexturl      = $info->url;
                $eventdata->contexturlname  = $info->assignment;

                message_send($eventdata);
            }
        }
    }

    /**
     * assignment submission is processed before grading
     *
     * @return void
     */
    private function process_submit_for_grading() {
        global $USER;

        // Need submit permission to submit an assignment
        require_capability('mod/assign:submit', $this->context);
        require_sesskey();

        $submission = $this->get_user_submission($USER->id,true);
        if ($submission->status != ASSIGN_SUBMISSION_STATUS_SUBMITTED) {
            // Give each submission plugin a chance to process the submission
            $plugins = $this->get_submission_plugins();
            foreach ($plugins as $plugin) {
                $plugin->submit_for_grading();
            }

            $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
            $this->update_submission($submission);
            $this->add_to_log('submit for grading', $this->format_submission_for_log($submission));
            $this->email_graders($submission);
        }
    }

    /**
     * save grading options
     *
     * @return void
     */
    private function process_save_grading_options() {
        global $USER, $CFG;

        // Include grading options form
        require_once($CFG->dirroot . '/mod/assign/gradingoptionsform.php');

        // Need submit permission to submit an assignment
        require_capability('mod/assign:grade', $this->context);



        $mform = new mod_assign_grading_options_form(null, array('cm'=>$this->get_course_module()->id, 'contextid'=>$this->context->id, 'userid'=>$USER->id));

        if ($formdata = $mform->get_data()) {
            set_user_preference('assign_perpage', $formdata->perpage);
            set_user_preference('assign_filter', $formdata->filter);
        }
    }

   /**
    * Take a grade object and print a short summary for the log file.
    * The size limit for the log file is 255 characters, so be careful not
    * to include too much information.
    *
    * @param stdClass $grade
    * @return string
    */
    private function format_grade_for_log(stdClass $grade) {
        global $DB;

        $user = $DB->get_record('user', array('id' => $grade->userid), '*', MUST_EXIST);

        $info = get_string('gradestudent', 'assign', array('id'=>$user->id, 'fullname'=>fullname($user)));
        if ($grade->grade != '') {
            $info .= get_string('grade') . ': ' . $this->display_grade($grade->grade) . '. ';
        } else {
            $info .= get_string('nograde', 'assign');
        }
        if ($grade->locked) {
            $info .= get_string('submissionslocked', 'assign') . '. ';
        }
        return $info;
    }

    /**
     * Take a submission object and print a short summary for the log file.
     * The size limit for the log file is 255 characters, so be careful not
     * to include too much information.
     *
     * @param stdClass $submission
     * @return string
     */
    private function format_submission_for_log(stdClass $submission) {
        $info = '';
        $info .= get_string('submissionstatus', 'assign') . ': ' . get_string('submissionstatus_' . $submission->status, 'assign') . '. <br>';
        // format_for_log here iterating every single log INFO  from either submission or grade in every assignment plugin

        foreach ($this->submissionplugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {


                $info .= "<br>" . $plugin->format_for_log($submission);
            }
        }


        return $info;
    }

    /**
     * save assignment submission
     *
     * @param  moodleform $mform
     * @return bool
     */
    private function process_save_submission(&$mform) {
        global $USER, $CFG;

        // Include submission form
        require_once($CFG->dirroot . '/mod/assign/submission_form.php');

        // Need submit permission to submit an assignment
        require_capability('mod/assign:submit', $this->context);
        require_sesskey();

        $data = new stdClass();
        $mform = new mod_assign_submission_form(null, array($this, $data));
        if ($mform->is_cancelled()) {
            return true;
        }
        if ($data = $mform->get_data()) {
            $submission = $this->get_user_submission($USER->id, true); //create the submission if needed & its id
            $grade = $this->get_user_grade($USER->id, false); // get the grade to check if it is locked
            if ($grade && $grade->locked) {
                print_error('submissionslocked', 'assign');
                return true;
            }


            foreach ($this->submissionplugins as $plugin) {
                if ($plugin->is_enabled()) {
                    if (!$plugin->save($submission, $data)) {
                        print_error($plugin->get_error());
                    }
                }
            }

            $this->update_submission($submission);

            // Logging
            $this->add_to_log('submit', $this->format_submission_for_log($submission));

            if (!$this->get_instance()->submissiondrafts) {
                $this->email_graders($submission);
            }
            return true;
        }
        return false;
    }

    /**
     * count the number of files in the file area
     *
     * @param int $userid
     * @param string $area
     * @return int
     */
    private function count_files($userid = 0, $area = ASSIGN_FILEAREA_SUBMISSION_FILES) {
        global $USER;

        if (!$userid) {
            $userid = $USER->id;
        }

        $fs = get_file_storage();
        $files = $fs->get_area_files($this->context->id, 'mod_assign', $area, $userid, "id", false);

        return count($files);
    }

    /**
     * Determine if this users grade is locked or overridden
     *
     * @param int $userid - The student userid
     * @return bool $gradingdisabled
     */
    private function grading_disabled($userid) {
        global $CFG;

        $gradinginfo = grade_get_grades($this->get_course()->id, 'mod', 'assign', $this->get_instance()->id, array($userid));
        if (!$gradinginfo) {
            return false;
        }

        if (!isset($gradinginfo->items[0]->grades[$userid])) {
            return false;
        }
        $gradingdisabled = $gradinginfo->items[0]->grades[$userid]->locked || $gradinginfo->items[0]->grades[$userid]->overridden;
        return $gradingdisabled;
    }


    /**
     * Get an instance of a grading form if advanced grading is enabled
     * This is specific to the assignment, marker and student
     *
     * @param int $userid - The student userid
     * @param bool $gradingdisabled
     * @return mixed gradingform_instance|null $gradinginstance
     */
    private function get_grading_instance($userid, $gradingdisabled) {
        global $CFG, $USER;

        $grade = $this->get_user_grade($userid, false);
        $grademenu = make_grades_menu($this->get_instance()->grade);

        $advancedgradingwarning = false;
        $gradingmanager = get_grading_manager($this->context, 'mod_assign', 'submissions');
        $gradinginstance = null;
        if ($gradingmethod = $gradingmanager->get_active_method()) {
            $controller = $gradingmanager->get_controller($gradingmethod);
            if ($controller->is_form_available()) {
                $itemid = null;
                if ($grade) {
                    $itemid = $grade->id;
                }
                if ($gradingdisabled && $itemid) {
                    $gradinginstance = ($controller->get_current_instance($USER->id, $itemid));
                } else if (!$gradingdisabled) {
                    $instanceid = optional_param('advancedgradinginstanceid', 0, PARAM_INT);
                    $gradinginstance = ($controller->get_or_create_instance($instanceid, $USER->id, $itemid));
                }
            } else {
                $advancedgradingwarning = $controller->form_unavailable_notification();
            }
        }
        if ($gradinginstance) {
            $gradinginstance->get_controller()->set_grade_range($grademenu);
        }
        return $gradinginstance;
    }

    /**
     * add elements to grade form
     *
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @param array $params
     * @return void
     */
    public function add_grade_form_elements(MoodleQuickForm $mform, stdClass $data, $params) {
        global $USER, $CFG;
        $settings = $this->get_instance();

        $rownum = $params['rownum'];
        $last = $params['last'];
        $useridlist = $params['useridlist'];
        $userid = $useridlist[$rownum];
        $grade = $this->get_user_grade($userid, false);

        // add advanced grading
        $gradingdisabled = $this->grading_disabled($userid);
        $gradinginstance = $this->get_grading_instance($userid, $gradingdisabled);

        if ($gradinginstance) {
            $gradingelement = $mform->addElement('grading', 'advancedgrading', get_string('grade').':', array('gradinginstance' => $gradinginstance));
            if ($gradingdisabled) {
                $gradingelement->freeze();
            } else {
                $mform->addElement('hidden', 'advancedgradinginstanceid', $gradinginstance->get_id());
            }
        } else {
            // use simple direct grading
            if ($this->get_instance()->grade > 0) {
                $mform->addElement('text', 'grade', get_string('gradeoutof', 'assign',$this->get_instance()->grade));
                $mform->addHelpButton('grade', 'gradeoutofhelp', 'assign');
                $mform->setType('grade', PARAM_TEXT);
            } else {
                $grademenu = make_grades_menu($this->get_instance()->grade);
                if (count($grademenu) > 0) {
                    $mform->addElement('select', 'grade', get_string('grade').':', $grademenu);
                    $mform->setType('grade', PARAM_INT);
                }
            }
        }
        $mform->addElement('static', 'progress', '', get_string('gradingstudentprogress', 'assign', array('index'=>$rownum+1, 'count'=>count($useridlist))));


        // plugins
        $this->add_plugin_grade_elements($grade, $mform, $data);

        // hidden params
        $mform->addElement('hidden', 'id', $this->get_course_module()->id);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'rownum', $rownum);
        $mform->setType('rownum', PARAM_INT);
        $mform->addElement('hidden', 'useridlist', implode(',', $useridlist));
        $mform->setType('useridlist', PARAM_TEXT);
        $mform->addElement('hidden', 'ajax', optional_param('ajax', 0, PARAM_INT));
        $mform->setType('ajax', PARAM_INT);

        $mform->addElement('hidden', 'action', 'submitgrade');
        $mform->setType('action', PARAM_ALPHA);


        $buttonarray=array();
        $buttonarray[] = $mform->createElement('submit', 'savegrade', get_string('savechanges', 'assign'));
        if (!$last){
            $buttonarray[] = $mform->createElement('submit', 'saveandshownext', get_string('savenext','assign'));
        }
        $buttonarray[] = $mform->createElement('cancel', 'cancelbutton', get_string('cancel'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
        $buttonarray=array();

        if ($rownum > 0) {
            $buttonarray[] = $mform->createElement('submit', 'nosaveandprevious', get_string('previous','assign'));
        }

        if (!$last){
            $buttonarray[] = $mform->createElement('submit', 'nosaveandnext', get_string('nosavebutnext', 'assign'));
        }
        $mform->addGroup($buttonarray, 'navar', '', array(' '), false);
    }


    /**
     * add elements in submission plugin form
     *
     * @param mixed $submission stdClass|null
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @return void
     */
    private function add_plugin_submission_elements($submission, MoodleQuickForm $mform, stdClass $data) {
        foreach ($this->submissionplugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible() && $plugin->allow_submissions()) {
                $mform->addElement('header', 'header_' . $plugin->get_type(), $plugin->get_name());
                if (!$plugin->get_form_elements($submission, $mform, $data)) {
                    $mform->removeElement('header_' . $plugin->get_type());
                }
            }
        }
    }

    /**
     * check if feedback plugins installed are enabled
     *
     * @return bool
     */
    public function is_any_feedback_plugin_enabled() {
        if (!isset($this->cache['any_feedback_plugin_enabled'])) {
            $this->cache['any_feedback_plugin_enabled'] = false;
            foreach ($this->feedbackplugins as $plugin) {
                if ($plugin->is_enabled() && $plugin->is_visible()) {
                    $this->cache['any_feedback_plugin_enabled'] = true;
                    break;
                }
            }
        }

        return $this->cache['any_feedback_plugin_enabled'];

    }

    /**
     * check if submission plugins installed are enabled
     *
     * @return bool
     */
    public function is_any_submission_plugin_enabled() {
        if (!isset($this->cache['any_submission_plugin_enabled'])) {
            $this->cache['any_submission_plugin_enabled'] = false;
            foreach ($this->submissionplugins as $plugin) {
                if ($plugin->is_enabled() && $plugin->is_visible() && $plugin->allow_submissions()) {
                    $this->cache['any_submission_plugin_enabled'] = true;
                    break;
                }
            }
        }

        return $this->cache['any_submission_plugin_enabled'];

    }

    /**
     * add elements to submission form
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @return void
     */
    public function add_submission_form_elements(MoodleQuickForm $mform, stdClass $data) {
        global $USER;

        // online text submissions

        $submission = $this->get_user_submission($USER->id, false);

        $this->add_plugin_submission_elements($submission, $mform, $data);

        // hidden params
        $mform->addElement('hidden', 'id', $this->get_course_module()->id);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'action', 'savesubmission');
        $mform->setType('action', PARAM_TEXT);
        // buttons

    }

    /**
     * revert to draft
     * Uses url parameter userid
     *
     * @param int $userid
     * @return void
     */
    private function process_revert_to_draft($userid = 0) {
        global $USER, $DB;

        // Need grade permission
        require_capability('mod/assign:grade', $this->context);
        require_sesskey();

        if (!$userid) {
            $userid = required_param('userid', PARAM_INT);
        }

        $submission = $this->get_user_submission($userid, false);
        if (!$submission) {
            return;
        }
        $submission->status = ASSIGN_SUBMISSION_STATUS_DRAFT;
        $this->update_submission($submission, false);

        // update the modified time on the grade (grader modified)
        $grade = $this->get_user_grade($userid, true);
        $this->update_grade($grade);

        $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);

        $this->add_to_log('revert submission to draft', get_string('reverttodraftforstudent', 'assign', array('id'=>$user->id, 'fullname'=>fullname($user))));

    }

    /**
     * lock  the process
     * Uses url parameter userid
     * @param int $userid
     * @return void
     */
    private function process_lock($userid = 0) {
        global $USER, $DB;

        // Need grade permission
        require_capability('mod/assign:grade', $this->context);
        require_sesskey();

        if (!$userid) {
            $userid = required_param('userid', PARAM_INT);
        }

        $grade = $this->get_user_grade($userid, true);
        $grade->locked = 1;
        $grade->grader = $USER->id;
        $this->update_grade($grade);

        $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);

        $this->add_to_log('lock submission', get_string('locksubmissionforstudent', 'assign', array('id'=>$user->id, 'fullname'=>fullname($user))));
    }

    /**
     * unlock the process
     *
     * @param int $userid
     * @return void
     */
    private function process_unlock($userid = 0) {
        global $USER, $DB;

        // Need grade permission
        require_capability('mod/assign:grade', $this->context);
        require_sesskey();

        if (!$userid) {
            $userid = required_param('userid', PARAM_INT);
        }

        $grade = $this->get_user_grade($userid, true);
        $grade->locked = 0;
        $grade->grader = $USER->id;
        $this->update_grade($grade);

        $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);

        $this->add_to_log('unlock submission', get_string('unlocksubmissionforstudent', 'assign', array('id'=>$user->id, 'fullname'=>fullname($user))));
    }

    /**
     * save grade
     *
     * @param  moodleform $mform
     * @return bool - was the grade saved
     */
    private function process_save_grade(&$mform) {
        global $USER, $DB, $CFG;
        // Include grade form
        require_once($CFG->dirroot . '/mod/assign/gradeform.php');

        // Need submit permission to submit an assignment
        require_capability('mod/assign:grade', $this->context);
        require_sesskey();

        $rownum = required_param('rownum', PARAM_INT);
        $useridlist = optional_param('useridlist', '', PARAM_TEXT);
        if ($useridlist) {
            $useridlist = explode(',', $useridlist);
        } else {
            $useridlist = $this->get_grading_userid_list();
        }
        $last = false;
        $userid = $useridlist[$rownum];
        if ($rownum == count($useridlist) - 1) {
            $last = true;
        }

        $data = new stdClass();
        $mform = new mod_assign_grade_form(null, array($this, $data, array('rownum'=>$rownum, 'useridlist'=>$useridlist, 'last'=>false)), 'post', '', array('class'=>'gradeform'));

        if ($formdata = $mform->get_data()) {
            $grade = $this->get_user_grade($userid, true);
            $gradingdisabled = $this->grading_disabled($userid);
            $gradinginstance = $this->get_grading_instance($userid, $gradingdisabled);
            if ($gradinginstance) {
                $grade->grade = $gradinginstance->submit_and_get_grade($formdata->advancedgrading, $grade->id);
            } else {
                // handle the case when grade is set to No Grade
                if (isset($formdata->grade)) {
                    $grade->grade= grade_floatval($formdata->grade);
                }
            }
            $grade->grader= $USER->id;

            $gradebookplugin = $CFG->assign_feedback_plugin_for_gradebook;

            // call save in plugins
            foreach ($this->feedbackplugins as $plugin) {
                if ($plugin->is_enabled() && $plugin->is_visible()) {
                    if (!$plugin->save($grade, $formdata)) {
                        $result = false;
                        print_error($plugin->get_error());
                    }
                    if (('assignfeedback_' . $plugin->get_type()) == $gradebookplugin) {
                        // this is the feedback plugin chose to push comments to the gradebook
                        $grade->feedbacktext = $plugin->text_for_gradebook($grade);
                        $grade->feedbackformat = $plugin->format_for_gradebook($grade);
                    }
                }
            }
            $this->update_grade($grade);

            $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);

            $this->add_to_log('grade submission', $this->format_grade_for_log($grade));


        } else {
            return false;
        }
        return true;
    }

    /**
     * This function is a static wrapper around can_upgrade
     *
     * @param string $type The plugin type
     * @param int $version The plugin version
     * @return bool
     */
    public static function can_upgrade_assignment($type, $version) {
        $assignment = new assign(null, null, null);
        return $assignment->can_upgrade($type, $version);
    }

    /**
     * This function returns true if it can upgrade an assignment from the 2.2
     * module.
     * @param string $type The plugin type
     * @param int $version The plugin version
     * @return bool
     */
    public function can_upgrade($type, $version) {
        if ($type == 'offline' && $version >= 2011112900) {
            return true;
        }
        foreach ($this->submissionplugins as $plugin) {
            if ($plugin->can_upgrade($type, $version)) {
                return true;
            }
        }
        foreach ($this->feedbackplugins as $plugin) {
            if ($plugin->can_upgrade($type, $version)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Copy all the files from the old assignment files area to the new one.
     * This is used by the plugin upgrade code.
     *
     * @param int $oldcontextid The old assignment context id
     * @param int $oldcomponent The old assignment component ('assignment')
     * @param int $oldfilearea The old assignment filearea ('submissions')
     * @param int $olditemid The old submissionid (can be null e.g. intro)
     * @param int $newcontextid The new assignment context id
     * @param int $newcomponent The new assignment component ('assignment')
     * @param int $newfilearea The new assignment filearea ('submissions')
     * @param int $newitemid The new submissionid (can be null e.g. intro)
     * @return int The number of files copied
     */
    public function copy_area_files_for_upgrade($oldcontextid, $oldcomponent, $oldfilearea, $olditemid, $newcontextid, $newcomponent, $newfilearea, $newitemid) {
        // Note, this code is based on some code in filestorage - but that code
        // deleted the old files (which we don't want)
        $count = 0;

        $fs = get_file_storage();

        $oldfiles = $fs->get_area_files($oldcontextid, $oldcomponent, $oldfilearea, $olditemid, 'id', false);
        foreach ($oldfiles as $oldfile) {
            $filerecord = new stdClass();
            $filerecord->contextid = $newcontextid;
            $filerecord->component = $newcomponent;
            $filerecord->filearea = $newfilearea;
            $filerecord->itemid = $newitemid;
            $fs->create_file_from_storedfile($filerecord, $oldfile);
            $count += 1;
        }

        return $count;
    }

    /**
     * Get an upto date list of user grades and feedback for the gradebook
     *
     * @param int $userid int or 0 for all users
     * @return array of grade data formated for the gradebook api
     *         The data required by the gradebook api is userid,
     *                                                   rawgrade,
     *                                                   feedback,
     *                                                   feedbackformat,
     *                                                   usermodified,
     *                                                   dategraded,
     *                                                   datesubmitted
     */
    public function get_user_grades_for_gradebook($userid) {
        global $DB, $CFG;
        $grades = array();
        $assignmentid = $this->get_instance()->id;

        $gradebookpluginname = $CFG->assign_feedback_plugin_for_gradebook;
        $gradebookplugin = null;

        // find the gradebook plugin
        foreach ($this->feedbackplugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {
                if (('assignfeedback_' . $plugin->get_type()) == $gradebookpluginname) {
                    $gradebookplugin = $plugin;
                }
            }
        }
        if ($userid) {
            $where = ' WHERE u.id = ? ';
        } else {
            $where = ' WHERE u.id != ? ';
        }

        $graderesults = $DB->get_recordset_sql('SELECT u.id as userid, s.timemodified as datesubmitted, g.grade as rawgrade, g.timemodified as dategraded, g.grader as usermodified
                            FROM {user} u
                            LEFT JOIN {assign_submission} s ON u.id = s.userid and s.assignment = ?
                            LEFT JOIN {assign_grades} g ON u.id = g.userid and g.assignment = ?
                            ' . $where, array($assignmentid, $assignmentid, $userid));


        foreach ($graderesults as $result) {
            $gradebookgrade = clone $result;
            // now get the feedback
            if ($gradebookplugin) {
                $grade = $this->get_user_grade($result->userid, false);
                $gradebookgrade->feedbacktext = $gradebookplugin->text_for_gradebook($grade);
                $gradebookgrade->feedbackformat = $gradebookplugin->format_for_gradebook($grade);
            }
            $grades[$gradebookgrade->userid] = $gradebookgrade;
        }

        $graderesults->close();
        return $grades;
    }

}

