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

// Assignment submission statuses.
define('ASSIGN_SUBMISSION_STATUS_REOPENED', 'reopened');
define('ASSIGN_SUBMISSION_STATUS_DRAFT', 'draft');
define('ASSIGN_SUBMISSION_STATUS_SUBMITTED', 'submitted');

// Search filters for grading page.
define('ASSIGN_FILTER_SUBMITTED', 'submitted');
define('ASSIGN_FILTER_SINGLE_USER', 'singleuser');
define('ASSIGN_FILTER_REQUIRE_GRADING', 'require_grading');

// Reopen attempt methods.
define('ASSIGN_ATTEMPT_REOPEN_METHOD_NONE', 'none');
define('ASSIGN_ATTEMPT_REOPEN_METHOD_MANUAL', 'manual');
define('ASSIGN_ATTEMPT_REOPEN_METHOD_UNTILPASS', 'untilpass');

// Special value means allow unlimited attempts.
define('ASSIGN_UNLIMITED_ATTEMPTS', -1);

require_once($CFG->libdir . '/accesslib.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/repository/lib.php');
require_once($CFG->dirroot . '/mod/assign/mod_form.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->dirroot . '/grade/grading/lib.php');
require_once($CFG->dirroot . '/mod/assign/feedbackplugin.php');
require_once($CFG->dirroot . '/mod/assign/submissionplugin.php');
require_once($CFG->dirroot . '/mod/assign/renderable.php');
require_once($CFG->dirroot . '/mod/assign/gradingtable.php');
require_once($CFG->libdir . '/eventslib.php');
require_once($CFG->libdir . '/portfolio/caller.php');

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

    /** @var context the context of the course module for this assign instance
     *               (or just the course if we are creating a new one)
     */
    private $context;

    /** @var stdClass the course this assign instance belongs to */
    private $course;

    /** @var stdClass the admin config for all assign instances  */
    private $adminconfig;

    /** @var assign_renderer the custom renderer for this module */
    private $output;

    /** @var stdClass the course module for this assign instance */
    private $coursemodule;

    /** @var array cache for things like the coursemodule name or the scale menu -
     *             only lives for a single request.
     */
    private $cache;

    /** @var array list of the installed submission plugins */
    private $submissionplugins;

    /** @var array list of the installed feedback plugins */
    private $feedbackplugins;

    /** @var string action to be used to return to this page
     *              (without repeating any form submissions etc).
     */
    private $returnaction = 'view';

    /** @var array params to be used to return to this page */
    private $returnparams = array();

    /** @var string modulename prevents excessive calls to get_string */
    private static $modulename = null;

    /** @var string modulenameplural prevents excessive calls to get_string */
    private static $modulenameplural = null;

    /**
     * Constructor for the base assign class.
     *
     * @param mixed $coursemodulecontext context|null the course module context
     *                                   (or the course context if the coursemodule has not been
     *                                   created yet).
     * @param mixed $coursemodule the current course module if it was already loaded,
     *                            otherwise this class will load one from the context as required.
     * @param mixed $course the current course  if it was already loaded,
     *                      otherwise this class will load one from the context as required.
     */
    public function __construct($coursemodulecontext, $coursemodule, $course) {
        global $PAGE;

        $this->context = $coursemodulecontext;
        $this->coursemodule = $coursemodule;
        $this->course = $course;

        // Temporary cache only lives for a single request - used to reduce db lookups.
        $this->cache = array();

        $this->submissionplugins = $this->load_plugins('assignsubmission');
        $this->feedbackplugins = $this->load_plugins('assignfeedback');
    }

    /**
     * Set the action and parameters that can be used to return to the current page.
     *
     * @param string $action The action for the current page
     * @param array $params An array of name value pairs which form the parameters
     *                      to return to the current page.
     * @return void
     */
    public function register_return_link($action, $params) {
        global $PAGE;
        $params['action'] = $action;
        $currenturl = $PAGE->url;

        $currenturl->params($params);
        $PAGE->set_url($currenturl);
    }

    /**
     * Return an action that can be used to get back to the current page.
     *
     * @return string action
     */
    public function get_return_action() {
        global $PAGE;

        $params = $PAGE->url->params();

        if (!empty($params['action'])) {
            return $params['action'];
        }
        return '';
    }

    /**
     * Based on the current assignment settings should we display the intro.
     *
     * @return bool showintro
     */
    protected function show_intro() {
        if ($this->get_instance()->alwaysshowdescription ||
                time() > $this->get_instance()->allowsubmissionsfromdate) {
            return true;
        }
        return false;
    }

    /**
     * Return a list of parameters that can be used to get back to the current page.
     *
     * @return array params
     */
    public function get_return_params() {
        global $PAGE;

        $params = $PAGE->url->params();
        unset($params['id']);
        unset($params['action']);
        return $params;
    }

    /**
     * Set the submitted form data.
     *
     * @param stdClass $data The form data (instance)
     */
    public function set_instance(stdClass $data) {
        $this->instance = $data;
    }

    /**
     * Set the context.
     *
     * @param context $context The new context
     */
    public function set_context(context $context) {
        $this->context = $context;
    }

    /**
     * Set the course data.
     *
     * @param stdClass $course The course data
     */
    public function set_course(stdClass $course) {
        $this->course = $course;
    }

    /**
     * Get list of feedback plugins installed.
     *
     * @return array
     */
    public function get_feedback_plugins() {
        return $this->feedbackplugins;
    }

    /**
     * Get list of submission plugins installed.
     *
     * @return array
     */
    public function get_submission_plugins() {
        return $this->submissionplugins;
    }

    /**
     * Is blind marking enabled and reveal identities not set yet?
     *
     * @return bool
     */
    public function is_blind_marking() {
        return $this->get_instance()->blindmarking && !$this->get_instance()->revealidentities;
    }

    /**
     * Does an assignment have submission(s) or grade(s) already?
     *
     * @return bool
     */
    public function has_submissions_or_grades() {
        $allgrades = $this->count_grades();
        $allsubmissions = $this->count_submissions();
        if (($allgrades == 0) && ($allsubmissions == 0)) {
            return false;
        }
        return true;
    }

    /**
     * Get a specific submission plugin by its type.
     *
     * @param string $subtype assignsubmission | assignfeedback
     * @param string $type
     * @return mixed assign_plugin|null
     */
    public function get_plugin_by_type($subtype, $type) {
        $shortsubtype = substr($subtype, strlen('assign'));
        $name = $shortsubtype . 'plugins';
        if ($name != 'feedbackplugins' && $name != 'submissionplugins') {
            return null;
        }
        $pluginlist = $this->$name;
        foreach ($pluginlist as $plugin) {
            if ($plugin->get_type() == $type) {
                return $plugin;
            }
        }
        return null;
    }

    /**
     * Get a feedback plugin by type.
     *
     * @param string $type - The type of plugin e.g comments
     * @return mixed assign_feedback_plugin|null
     */
    public function get_feedback_plugin_by_type($type) {
        return $this->get_plugin_by_type('assignfeedback', $type);
    }

    /**
     * Get a submission plugin by type.
     *
     * @param string $type - The type of plugin e.g comments
     * @return mixed assign_submission_plugin|null
     */
    public function get_submission_plugin_by_type($type) {
        return $this->get_plugin_by_type('assignsubmission', $type);
    }

    /**
     * Load the plugins from the sub folders under subtype.
     *
     * @param string $subtype - either submission or feedback
     * @return array - The sorted list of plugins
     */
    protected function load_plugins($subtype) {
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
                    while (array_key_exists($idx, $result)) {
                        $idx +=1;
                    }
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
     *
     * @param string $action The current action if any.
     * @return string - The page output.
     */
    public function view($action='') {

        $o = '';
        $mform = null;
        $notices = array();
        $nextpageparams = array();

        if (!empty($this->get_course_module()->id)) {
            $nextpageparams['id'] = $this->get_course_module()->id;
        }

        // Handle form submissions first.
        if ($action == 'savesubmission') {
            $action = 'editsubmission';
            if ($this->process_save_submission($mform, $notices)) {
                $action = 'redirect';
                $nextpageparams['action'] = 'view';
            }
        } else if ($action == 'editprevioussubmission') {
            $action = 'editsubmission';
            if ($this->process_copy_previous_attempt($notices)) {
                $action = 'redirect';
                $nextpageparams['action'] = 'editsubmission';
            }
        } else if ($action == 'lock') {
            $this->process_lock();
            $action = 'redirect';
            $nextpageparams['action'] = 'grading';
        } else if ($action == 'addattempt') {
            $this->process_add_attempt(required_param('userid', PARAM_INT));
            $action = 'redirect';
            $nextpageparams['action'] = 'grading';
        } else if ($action == 'reverttodraft') {
            $this->process_revert_to_draft();
            $action = 'redirect';
            $nextpageparams['action'] = 'grading';
        } else if ($action == 'unlock') {
            $this->process_unlock();
            $action = 'redirect';
            $nextpageparams['action'] = 'grading';
        } else if ($action == 'confirmsubmit') {
            $action = 'submit';
            if ($this->process_submit_for_grading($mform)) {
                $action = 'redirect';
                $nextpageparams['action'] = 'view';
            }
        } else if ($action == 'gradingbatchoperation') {
            $action = $this->process_grading_batch_operation($mform);
            if ($action == 'grading') {
                $action = 'redirect';
                $nextpageparams['action'] = 'grading';
            }
        } else if ($action == 'submitgrade') {
            if (optional_param('saveandshownext', null, PARAM_RAW)) {
                // Save and show next.
                $action = 'grade';
                if ($this->process_save_grade($mform)) {
                    $action = 'redirect';
                    $nextpageparams['action'] = 'grade';
                    $nextpageparams['rownum'] = optional_param('rownum', 0, PARAM_INT) + 1;
                    $nextpageparams['useridlistid'] = optional_param('useridlistid', time(), PARAM_INT);
                }
            } else if (optional_param('nosaveandprevious', null, PARAM_RAW)) {
                $action = 'redirect';
                $nextpageparams['action'] = 'grade';
                $nextpageparams['rownum'] = optional_param('rownum', 0, PARAM_INT) - 1;
                $nextpageparams['useridlistid'] = optional_param('useridlistid', time(), PARAM_INT);
            } else if (optional_param('nosaveandnext', null, PARAM_RAW)) {
                $action = 'redirect';
                $nextpageparams['action'] = 'grade';
                $nextpageparams['rownum'] = optional_param('rownum', 0, PARAM_INT) + 1;
                $nextpageparams['useridlistid'] = optional_param('useridlistid', time(), PARAM_INT);
            } else if (optional_param('savegrade', null, PARAM_RAW)) {
                // Save changes button.
                $action = 'grade';
                if ($this->process_save_grade($mform)) {
                    $message = get_string('gradingchangessaved', 'assign');
                    $action = 'savegradingresult';
                }
            } else {
                // Cancel button.
                $action = 'redirect';
                $nextpageparams['action'] = 'grading';
            }
        } else if ($action == 'quickgrade') {
            $message = $this->process_save_quick_grades();
            $action = 'quickgradingresult';
        } else if ($action == 'saveoptions') {
            $this->process_save_grading_options();
            $action = 'redirect';
            $nextpageparams['action'] = 'grading';
        } else if ($action == 'saveextension') {
            $action = 'grantextension';
            if ($this->process_save_extension($mform)) {
                $action = 'redirect';
                $nextpageparams['action'] = 'grading';
            }
        } else if ($action == 'revealidentitiesconfirm') {
            $this->process_reveal_identities();
            $action = 'redirect';
            $nextpageparams['action'] = 'grading';
        }

        $returnparams = array('rownum'=>optional_param('rownum', 0, PARAM_INT),
                              'useridlistid'=>optional_param('useridlistid', 0, PARAM_INT));
        $this->register_return_link($action, $returnparams);

        // Now show the right view page.
        if ($action == 'redirect') {
            $nextpageurl = new moodle_url('/mod/assign/view.php', $nextpageparams);
            redirect($nextpageurl);
            return;
        } else if ($action == 'savegradingresult') {
            $o .= $this->view_savegrading_result($message);
        } else if ($action == 'quickgradingresult') {
            $mform = null;
            $o .= $this->view_quickgrading_result($message);
        } else if ($action == 'grade') {
            $o .= $this->view_single_grade_page($mform);
        } else if ($action == 'viewpluginassignfeedback') {
            $o .= $this->view_plugin_content('assignfeedback');
        } else if ($action == 'viewpluginassignsubmission') {
            $o .= $this->view_plugin_content('assignsubmission');
        } else if ($action == 'editsubmission') {
            $o .= $this->view_edit_submission_page($mform, $notices);
        } else if ($action == 'grading') {
            $o .= $this->view_grading_page();
        } else if ($action == 'downloadall') {
            $o .= $this->download_submissions();
        } else if ($action == 'submit') {
            $o .= $this->check_submit_for_grading($mform);
        } else if ($action == 'grantextension') {
            $o .= $this->view_grant_extension($mform);
        } else if ($action == 'revealidentities') {
            $o .= $this->view_reveal_identities_confirm($mform);
        } else if ($action == 'plugingradingbatchoperation') {
            $o .= $this->view_plugin_grading_batch_operation($mform);
        } else if ($action == 'viewpluginpage') {
             $o .= $this->view_plugin_page();
        } else if ($action == 'viewcourseindex') {
             $o .= $this->view_course_index();
        } else {
            $o .= $this->view_submission_page();
        }

        return $o;
    }

    /**
     * Add this instance to the database.
     *
     * @param stdClass $formdata The data submitted from the form
     * @param bool $callplugins This is used to skip the plugin code
     *             when upgrading an old assignment to a new one (the plugins get called manually)
     * @return mixed false if an error occurs or the int id of the new instance
     */
    public function add_instance(stdClass $formdata, $callplugins) {
        global $DB;

        $err = '';

        // Add the database record.
        $update = new stdClass();
        $update->name = $formdata->name;
        $update->timemodified = time();
        $update->timecreated = time();
        $update->course = $formdata->course;
        $update->courseid = $formdata->course;
        $update->intro = $formdata->intro;
        $update->introformat = $formdata->introformat;
        $update->alwaysshowdescription = !empty($formdata->alwaysshowdescription);
        $update->submissiondrafts = $formdata->submissiondrafts;
        $update->requiresubmissionstatement = $formdata->requiresubmissionstatement;
        $update->sendnotifications = $formdata->sendnotifications;
        $update->sendlatenotifications = $formdata->sendlatenotifications;
        $update->duedate = $formdata->duedate;
        $update->cutoffdate = $formdata->cutoffdate;
        $update->allowsubmissionsfromdate = $formdata->allowsubmissionsfromdate;
        $update->grade = $formdata->grade;
        $update->completionsubmit = !empty($formdata->completionsubmit);
        $update->teamsubmission = $formdata->teamsubmission;
        $update->requireallteammemberssubmit = $formdata->requireallteammemberssubmit;
        $update->teamsubmissiongroupingid = $formdata->teamsubmissiongroupingid;
        $update->blindmarking = $formdata->blindmarking;
        $update->attemptreopenmethod = ASSIGN_ATTEMPT_REOPEN_METHOD_NONE;
        if (!empty($formdata->attemptreopenmethod)) {
            $update->attemptreopenmethod = $formdata->attemptreopenmethod;
        }
        if (!empty($formdata->maxattempts)) {
            $update->maxattempts = $formdata->maxattempts;
        }

        $returnid = $DB->insert_record('assign', $update);
        $this->instance = $DB->get_record('assign', array('id'=>$returnid), '*', MUST_EXIST);
        // Cache the course record.
        $this->course = $DB->get_record('course', array('id'=>$formdata->course), '*', MUST_EXIST);

        if ($callplugins) {
            // Call save_settings hook for submission plugins.
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

            // In the case of upgrades the coursemodule has not been set,
            // so we need to wait before calling these two.
            $this->update_calendar($formdata->coursemodule);
            $this->update_gradebook(false, $formdata->coursemodule);

        }

        $update = new stdClass();
        $update->id = $this->get_instance()->id;
        $update->nosubmissions = (!$this->is_any_submission_plugin_enabled()) ? 1: 0;
        $DB->update_record('assign', $update);

        return $returnid;
    }

    /**
     * Delete all grades from the gradebook for this assignment.
     *
     * @return bool
     */
    protected function delete_grades() {
        global $CFG;

        $result = grade_update('mod/assign',
                               $this->get_course()->id,
                               'mod',
                               'assign',
                               $this->get_instance()->id,
                               0,
                               null,
                               array('deleted'=>1));
        return $result == GRADE_UPDATE_OK;
    }

    /**
     * Delete this instance from the database.
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

        // Delete files associated with this assignment.
        $fs = get_file_storage();
        if (! $fs->delete_area_files($this->context->id) ) {
            $result = false;
        }

        // Delete_records will throw an exception if it fails - so no need for error checking here.
        $DB->delete_records('assign_submission', array('assignment'=>$this->get_instance()->id));
        $DB->delete_records('assign_grades', array('assignment'=>$this->get_instance()->id));
        $DB->delete_records('assign_plugin_config', array('assignment'=>$this->get_instance()->id));

        // Delete items from the gradebook.
        if (! $this->delete_grades()) {
            $result = false;
        }

        // Delete the instance.
        $DB->delete_records('assign', array('id'=>$this->get_instance()->id));

        return $result;
    }

    /**
     * Actual implementation of the reset course functionality, delete all the
     * assignment submissions for course $data->courseid.
     *
     * @param $data the data submitted from the reset course.
     * @return array status array
     */
    public function reset_userdata($data) {
        global $CFG, $DB;

        $componentstr = get_string('modulenameplural', 'assign');
        $status = array();

        $fs = get_file_storage();
        if (!empty($data->reset_assign_submissions)) {
            // Delete files associated with this assignment.
            foreach ($this->submissionplugins as $plugin) {
                $fileareas = array();
                $plugincomponent = $plugin->get_subtype() . '_' . $plugin->get_type();
                $fileareas = $plugin->get_file_areas();
                foreach ($fileareas as $filearea) {
                    $fs->delete_area_files($this->context->id, $plugincomponent, $filearea);
                }

                if (!$plugin->delete_instance()) {
                    $status[] = array('component'=>$componentstr,
                                      'item'=>get_string('deleteallsubmissions', 'assign'),
                                      'error'=>$plugin->get_error());
                }
            }

            foreach ($this->feedbackplugins as $plugin) {
                $fileareas = array();
                $plugincomponent = $plugin->get_subtype() . '_' . $plugin->get_type();
                $fileareas = $plugin->get_file_areas();
                foreach ($fileareas as $filearea) {
                    $fs->delete_area_files($this->context->id, $plugincomponent, $filearea);
                }

                if (!$plugin->delete_instance()) {
                    $status[] = array('component'=>$componentstr,
                                      'item'=>get_string('deleteallsubmissions', 'assign'),
                                      'error'=>$plugin->get_error());
                }
            }

            $assignssql = 'SELECT a.id
                             FROM {assign} a
                           WHERE a.course=:course';
            $params = array('course'=>$data->courseid);

            $DB->delete_records_select('assign_submission', "assignment IN ($assignssql)", $params);

            $status[] = array('component'=>$componentstr,
                              'item'=>get_string('deleteallsubmissions', 'assign'),
                              'error'=>false);

            if (!empty($data->reset_gradebook_grades)) {
                $DB->delete_records_select('assign_grades', "assignment IN ($assignssql)", $params);
                // Remove all grades from gradebook.
                require_once($CFG->dirroot.'/mod/assign/lib.php');
                assign_reset_gradebook($data->courseid);
            }
        }
        // Updating dates - shift may be negative too.
        if ($data->timeshift) {
            shift_course_mod_dates('assign',
                                    array('duedate', 'allowsubmissionsfromdate', 'cutoffdate'),
                                    $data->timeshift,
                                    $data->courseid);
            $status[] = array('component'=>$componentstr,
                              'item'=>get_string('datechanged'),
                              'error'=>false);
        }

        return $status;
    }

    /**
     * Update the settings for a single plugin.
     *
     * @param assign_plugin $plugin The plugin to update
     * @param stdClass $formdata The form data
     * @return bool false if an error occurs
     */
    protected function update_plugin_instance(assign_plugin $plugin, stdClass $formdata) {
        if ($plugin->is_visible()) {
            $enabledname = $plugin->get_subtype() . '_' . $plugin->get_type() . '_enabled';
            if (!empty($formdata->$enabledname)) {
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
     * Update the gradebook information for this assignment.
     *
     * @param bool $reset If true, will reset all grades in the gradbook for this assignment
     * @param int $coursemoduleid This is required because it might not exist in the database yet
     * @return bool
     */
    public function update_gradebook($reset, $coursemoduleid) {
        global $CFG;

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
     * Load and cache the admin config for this module.
     *
     * @return stdClass the plugin config
     */
    public function get_admin_config() {
        if ($this->adminconfig) {
            return $this->adminconfig;
        }
        $this->adminconfig = get_config('assign');
        return $this->adminconfig;
    }

    /**
     * Update the calendar entries for this assignment.
     *
     * @param int $coursemoduleid - Required to pass this in because it might
     *                              not exist in the database yet.
     * @return bool
     */
    public function update_calendar($coursemoduleid) {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/calendar/lib.php');

        // Special case for add_instance as the coursemodule has not been set yet.
        $instance = $this->get_instance();

        if ($instance->duedate) {
            $event = new stdClass();

            $params = array('modulename'=>'assign', 'instance'=>$instance->id);
            $event->id = $DB->get_field('event',
                                        'id',
                                        $params);

            if ($event->id) {
                $event->name        = $instance->name;
                $event->description = format_module_intro('assign', $instance, $coursemoduleid);
                $event->timestart   = $instance->duedate;

                $calendarevent = calendar_event::load($event->id);
                $calendarevent->update($event);
            } else {
                $event = new stdClass();
                $event->name        = $instance->name;
                $event->description = format_module_intro('assign', $instance, $coursemoduleid);
                $event->courseid    = $instance->course;
                $event->groupid     = 0;
                $event->userid      = 0;
                $event->modulename  = 'assign';
                $event->instance    = $instance->id;
                $event->eventtype   = 'due';
                $event->timestart   = $instance->duedate;
                $event->timeduration = 0;

                calendar_event::create($event);
            }
        } else {
            $DB->delete_records('event', array('modulename'=>'assign', 'instance'=>$instance->id));
        }
    }


    /**
     * Update this instance in the database.
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
        $update->alwaysshowdescription = !empty($formdata->alwaysshowdescription);
        $update->submissiondrafts = $formdata->submissiondrafts;
        $update->requiresubmissionstatement = $formdata->requiresubmissionstatement;
        $update->sendnotifications = $formdata->sendnotifications;
        $update->sendlatenotifications = $formdata->sendlatenotifications;
        $update->duedate = $formdata->duedate;
        $update->cutoffdate = $formdata->cutoffdate;
        $update->allowsubmissionsfromdate = $formdata->allowsubmissionsfromdate;
        $update->grade = $formdata->grade;
        if (!empty($formdata->completionunlocked)) {
            $update->completionsubmit = !empty($formdata->completionsubmit);
        }
        $update->teamsubmission = $formdata->teamsubmission;
        $update->requireallteammemberssubmit = $formdata->requireallteammemberssubmit;
        $update->teamsubmissiongroupingid = $formdata->teamsubmissiongroupingid;
        $update->blindmarking = $formdata->blindmarking;
        $update->attemptreopenmethod = ASSIGN_ATTEMPT_REOPEN_METHOD_NONE;
        if (!empty($formdata->attemptreopenmethod)) {
            $update->attemptreopenmethod = $formdata->attemptreopenmethod;
        }
        if (!empty($formdata->maxattempts)) {
            $update->maxattempts = $formdata->maxattempts;
        }

        $result = $DB->update_record('assign', $update);
        $this->instance = $DB->get_record('assign', array('id'=>$update->id), '*', MUST_EXIST);

        // Load the assignment so the plugins have access to it.

        // Call save_settings hook for submission plugins.
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

        $this->update_calendar($this->get_course_module()->id);
        $this->update_gradebook(false, $this->get_course_module()->id);

        $update = new stdClass();
        $update->id = $this->get_instance()->id;
        $update->nosubmissions = (!$this->is_any_submission_plugin_enabled()) ? 1: 0;
        $DB->update_record('assign', $update);

        return $result;
    }

    /**
     * Add elements in grading plugin form.
     *
     * @param mixed $grade stdClass|null
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @param int $userid - The userid we are grading
     * @return void
     */
    protected function add_plugin_grade_elements($grade, MoodleQuickForm $mform, stdClass $data, $userid) {
        foreach ($this->feedbackplugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {
                $mform->addElement('header', 'header_' . $plugin->get_type(), $plugin->get_name());
                $mform->setExpanded('header_' . $plugin->get_type());
                if (!$plugin->get_form_elements_for_user($grade, $mform, $data, $userid)) {
                    $mform->removeElement('header_' . $plugin->get_type());
                }
            }
        }
    }



    /**
     * Add one plugins settings to edit plugin form.
     *
     * @param assign_plugin $plugin The plugin to add the settings from
     * @param MoodleQuickForm $mform The form to add the configuration settings to.
     *                               This form is modified directly (not returned).
     * @param array $pluginsenabled A list of form elements to be added to a group.
     *                              The new element is added to this array by this function.
     * @return void
     */
    protected function add_plugin_settings(assign_plugin $plugin, MoodleQuickForm $mform, & $pluginsenabled) {
        global $CFG;
        if ($plugin->is_visible()) {

            $name = $plugin->get_subtype() . '_' . $plugin->get_type() . '_enabled';
            $label = $plugin->get_name();
            $label .= ' ' . $this->get_renderer()->help_icon('enabled', $plugin->get_subtype() . '_' . $plugin->get_type());
            $pluginsenabled[] = $mform->createElement('checkbox', $name, '', $label);

            $default = get_config($plugin->get_subtype() . '_' . $plugin->get_type(), 'default');
            if ($plugin->get_config('enabled') !== false) {
                $default = $plugin->is_enabled();
            }
            $mform->setDefault($plugin->get_subtype() . '_' . $plugin->get_type() . '_enabled', $default);

            $plugin->get_settings($mform);

        }
    }

    /**
     * Add settings to edit plugin form.
     *
     * @param MoodleQuickForm $mform The form to add the configuration settings to.
     *                               This form is modified directly (not returned).
     * @return void
     */
    public function add_all_plugin_settings(MoodleQuickForm $mform) {
        $mform->addElement('header', 'submissiontypes', get_string('submissiontypes', 'assign'));

        $submissionpluginsenabled = array();
        $group = $mform->addGroup(array(), 'submissionplugins', get_string('submissiontypes', 'assign'), array(' '), false);
        foreach ($this->submissionplugins as $plugin) {
            $this->add_plugin_settings($plugin, $mform, $submissionpluginsenabled);
        }
        $group->setElements($submissionpluginsenabled);

        $mform->addElement('header', 'feedbacktypes', get_string('feedbacktypes', 'assign'));
        $feedbackpluginsenabled = array();
        $group = $mform->addGroup(array(), 'feedbackplugins', get_string('feedbacktypes', 'assign'), array(' '), false);
        foreach ($this->feedbackplugins as $plugin) {
            $this->add_plugin_settings($plugin, $mform, $feedbackpluginsenabled);
        }
        $group->setElements($feedbackpluginsenabled);
        $mform->setExpanded('submissiontypes');
    }

    /**
     * Allow each plugin an opportunity to update the defaultvalues
     * passed in to the settings form (needed to set up draft areas for
     * editor and filemanager elements)
     *
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
            $params = array('id' => $this->get_course_module()->instance);
            $this->instance = $DB->get_record('assign', $params, '*', MUST_EXIST);
        }
        if (!$this->instance) {
            throw new coding_exception('Improper use of the assignment class. ' .
                                       'Cannot load the assignment record.');
        }
        return $this->instance;
    }

    /**
     * Get the context of the current course.
     *
     * @return mixed context|null The course context
     */
    public function get_course_context() {
        if (!$this->context && !$this->course) {
            throw new coding_exception('Improper use of the assignment class. ' .
                                       'Cannot load the course context.');
        }
        if ($this->context) {
            return $this->context->get_course_context();
        } else {
            return context_course::instance($this->course->id);
        }
    }


    /**
     * Get the current course module.
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
            $this->coursemodule = get_coursemodule_from_id('assign',
                                                           $this->context->instanceid,
                                                           0,
                                                           false,
                                                           MUST_EXIST);
            return $this->coursemodule;
        }
        return null;
    }

    /**
     * Get context module.
     *
     * @return context
     */
    public function get_context() {
        return $this->context;
    }

    /**
     * Get the current course.
     *
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
        $params = array('id' => $this->get_course_context()->instanceid);
        $this->course = $DB->get_record('course', $params, '*', MUST_EXIST);

        return $this->course;
    }

    /**
     * Return a grade in user-friendly form, whether it's a scale or not.
     *
     * @param mixed $grade int|null
     * @param boolean $editing Are we allowing changes to this grade?
     * @param int $userid The user id the grade belongs to
     * @param int $modified Timestamp from when the grade was last modified
     * @return string User-friendly representation of grade
     */
    public function display_grade($grade, $editing, $userid=0, $modified=0) {
        global $DB;

        static $scalegrades = array();

        $o = '';

        if ($this->get_instance()->grade >= 0) {
            // Normal number.
            if ($editing && $this->get_instance()->grade > 0) {
                if ($grade < 0) {
                    $displaygrade = '';
                } else {
                    $displaygrade = format_float($grade);
                }
                $o .= '<label class="accesshide" for="quickgrade_' . $userid . '">' .
                       get_string('usergrade', 'assign') .
                       '</label>';
                $o .= '<input type="text"
                              id="quickgrade_' . $userid . '"
                              name="quickgrade_' . $userid . '"
                              value="' .  $displaygrade . '"
                              size="6"
                              maxlength="10"
                              class="quickgrade"/>';
                $o .= '&nbsp;/&nbsp;' . format_float($this->get_instance()->grade, 2);
                $o .= '<input type="hidden"
                              name="grademodified_' . $userid . '"
                              value="' . $modified . '"/>';
                return $o;
            } else {
                $o .= '<input type="hidden" name="grademodified_' . $userid . '" value="' . $modified . '"/>';
                if ($grade == -1 || $grade === null) {
                    $o .= '-';
                    return $o;
                } else {
                    $o .= format_float($grade, 2) .
                          '&nbsp;/&nbsp;' .
                          format_float($this->get_instance()->grade, 2);
                    return $o;
                }
            }

        } else {
            // Scale.
            if (empty($this->cache['scale'])) {
                if ($scale = $DB->get_record('scale', array('id'=>-($this->get_instance()->grade)))) {
                    $this->cache['scale'] = make_menu_from_list($scale->scale);
                } else {
                    $o .= '-';
                    return $o;
                }
            }
            if ($editing) {
                $o .= '<label class="accesshide"
                              for="quickgrade_' . $userid . '">' .
                      get_string('usergrade', 'assign') .
                      '</label>';
                $o .= '<select name="quickgrade_' . $userid . '" class="quickgrade">';
                $o .= '<option value="-1">' . get_string('nograde') . '</option>';
                foreach ($this->cache['scale'] as $optionid => $option) {
                    $selected = '';
                    if ($grade == $optionid) {
                        $selected = 'selected="selected"';
                    }
                    $o .= '<option value="' . $optionid . '" ' . $selected . '>' . $option . '</option>';
                }
                $o .= '</select>';
                $o .= '<input type="hidden" ' .
                             'name="grademodified_' . $userid . '" ' .
                             'value="' . $modified . '"/>';
                return $o;
            } else {
                $scaleid = (int)$grade;
                if (isset($this->cache['scale'][$scaleid])) {
                    $o .= $this->cache['scale'][$scaleid];
                    return $o;
                }
                $o .= '-';
                return $o;
            }
        }
    }

    /**
     * Load a list of users enrolled in the current course with the specified permission and group.
     * 0 for no group.
     *
     * @param int $currentgroup
     * @param bool $idsonly
     * @return array List of user records
     */
    public function list_participants($currentgroup, $idsonly) {
        if ($idsonly) {
            return get_enrolled_users($this->context, 'mod/assign:submit', $currentgroup, 'u.id');
        } else {
            return get_enrolled_users($this->context, 'mod/assign:submit', $currentgroup);
        }
    }

    /**
     * Load a count of valid teams for this assignment.
     *
     * @return int number of valid teams
     */
    public function count_teams() {

        $groups = groups_get_all_groups($this->get_course()->id,
                                        0,
                                        $this->get_instance()->teamsubmissiongroupingid,
                                        'g.id');
        $count = count($groups);

        // See if there are any users in the default group.
        $defaultusers = $this->get_submission_group_members(0, true);
        if (count($defaultusers) > 0) {
            $count += 1;
        }
        return $count;
    }

    /**
     * Load a count of users enrolled in the current course with the specified permission and group.
     * 0 for no group.
     *
     * @param int $currentgroup
     * @return int number of matching users
     */
    public function count_participants($currentgroup) {
        return count_enrolled_users($this->context, 'mod/assign:submit', $currentgroup);
    }

    /**
     * Load a count of users submissions in the current module that require grading
     * This means the submission modification time is more recent than the
     * grading modification time and the status is SUBMITTED.
     *
     * @return int number of matching submissions
     */
    public function count_submissions_need_grading() {
        global $DB;

        if ($this->get_instance()->teamsubmission) {
            // This does not make sense for group assignment because the submission is shared.
            return 0;
        }

        $currentgroup = groups_get_activity_group($this->get_course_module(), true);
        list($esql, $params) = get_enrolled_sql($this->get_context(), 'mod/assign:submit', $currentgroup, false);

        $submissionmaxattempt = 'SELECT mxs.userid, MAX(mxs.attemptnumber) AS maxattempt
                                 FROM {assign_submission} mxs
                                 WHERE mxs.assignment = :assignid2 GROUP BY mxs.userid';
        $grademaxattempt = 'SELECT mxg.userid, MAX(mxg.attemptnumber) AS maxattempt
                            FROM {assign_grades} mxg
                            WHERE mxg.assignment = :assignid3 GROUP BY mxg.userid';

        $params['assignid'] = $this->get_instance()->id;
        $params['assignid2'] = $this->get_instance()->id;
        $params['assignid3'] = $this->get_instance()->id;
        $params['submitted'] = ASSIGN_SUBMISSION_STATUS_SUBMITTED;

        $sql = 'SELECT COUNT(s.userid)
                   FROM {assign_submission} s
                   LEFT JOIN ( ' . $submissionmaxattempt . ' ) smx ON s.userid = smx.userid
                   LEFT JOIN ( ' . $grademaxattempt . ' ) gmx ON s.userid = gmx.userid
                   LEFT JOIN {assign_grades} g ON
                        s.assignment = g.assignment AND
                        s.userid = g.userid AND
                        g.attemptnumber = gmx.maxattempt
                   JOIN(' . $esql . ') e ON e.id = s.userid
                   WHERE
                        s.attemptnumber = smx.maxattempt AND
                        s.assignment = :assignid AND
                        s.timemodified IS NOT NULL AND
                        s.status = :submitted AND
                        (s.timemodified > g.timemodified OR g.timemodified IS NULL)';

        return $DB->count_records_sql($sql, $params);
    }

    /**
     * Load a count of grades.
     *
     * @return int number of grades
     */
    public function count_grades() {
        global $DB;

        if (!$this->has_instance()) {
            return 0;
        }

        $currentgroup = groups_get_activity_group($this->get_course_module(), true);
        list($esql, $params) = get_enrolled_sql($this->get_context(), 'mod/assign:submit', $currentgroup, false);

        $params['assignid'] = $this->get_instance()->id;

        $sql = 'SELECT COUNT(g.userid)
                   FROM {assign_grades} g
                   JOIN(' . $esql . ') e ON e.id = g.userid
                   WHERE g.assignment = :assignid';

        return $DB->count_records_sql($sql, $params);
    }

    /**
     * Load a count of submissions.
     *
     * @return int number of submissions
     */
    public function count_submissions() {
        global $DB;

        if (!$this->has_instance()) {
            return 0;
        }

        $params = array();

        if ($this->get_instance()->teamsubmission) {
            // We cannot join on the enrolment tables for group submissions (no userid).
            $sql = 'SELECT COUNT(DISTINCT s.groupid)
                        FROM {assign_submission} s
                        WHERE
                            s.assignment = :assignid AND
                            s.timemodified IS NOT NULL AND
                            s.userid = :groupuserid';

            $params['assignid'] = $this->get_instance()->id;
            $params['groupuserid'] = 0;
        } else {
            $currentgroup = groups_get_activity_group($this->get_course_module(), true);
            list($esql, $params) = get_enrolled_sql($this->get_context(), 'mod/assign:submit', $currentgroup, false);

            $params['assignid'] = $this->get_instance()->id;

            $sql = 'SELECT COUNT(DISTINCT s.userid)
                       FROM {assign_submission} s
                       JOIN(' . $esql . ') e ON e.id = s.userid
                       WHERE
                            s.assignment = :assignid AND
                            s.timemodified IS NOT NULL';
        }

        return $DB->count_records_sql($sql, $params);
    }

    /**
     * Load a count of submissions with a specified status.
     *
     * @param string $status The submission status - should match one of the constants
     * @return int number of matching submissions
     */
    public function count_submissions_with_status($status) {
        global $DB;

        $currentgroup = groups_get_activity_group($this->get_course_module(), true);
        list($esql, $params) = get_enrolled_sql($this->get_context(), 'mod/assign:submit', $currentgroup, false);

        $params['assignid'] = $this->get_instance()->id;
        $params['assignid2'] = $this->get_instance()->id;
        $params['submissionstatus'] = $status;

        if ($this->get_instance()->teamsubmission) {
            $maxattemptsql = 'SELECT mxs.groupid, MAX(mxs.attemptnumber) AS maxattempt
                              FROM {assign_submission} mxs
                              WHERE mxs.assignment = :assignid2 GROUP BY mxs.groupid';

            $sql = 'SELECT COUNT(s.groupid)
                        FROM {assign_submission} s
                        JOIN(' . $maxattemptsql . ') smx ON s.groupid = smx.groupid
                        WHERE
                            s.attemptnumber = smx.maxattempt AND
                            s.assignment = :assignid AND
                            s.timemodified IS NOT NULL AND
                            s.userid = :groupuserid AND
                            s.status = :submissionstatus';
            $params['groupuserid'] = 0;
        } else {
            $maxattemptsql = 'SELECT mxs.userid, MAX(mxs.attemptnumber) AS maxattempt
                              FROM {assign_submission} mxs
                              WHERE mxs.assignment = :assignid2 GROUP BY mxs.userid';

            $sql = 'SELECT COUNT(s.userid)
                        FROM {assign_submission} s
                        JOIN(' . $esql . ') e ON e.id = s.userid
                        JOIN(' . $maxattemptsql . ') smx ON s.userid = smx.userid
                        WHERE
                            s.attemptnumber = smx.maxattempt AND
                            s.assignment = :assignid AND
                            s.timemodified IS NOT NULL AND
                            s.status = :submissionstatus';
        }

        return $DB->count_records_sql($sql, $params);
    }

    /**
     * Utility function to get the userid for every row in the grading table
     * so the order can be frozen while we iterate it.
     *
     * @return array An array of userids
     */
    protected function get_grading_userid_list() {
        $filter = get_user_preferences('assign_filter', '');
        $table = new assign_grading_table($this, 0, $filter, 0, false);

        $useridlist = $table->get_column_data('userid');

        return $useridlist;
    }

    /**
     * Generate zip file from array of given files.
     *
     * @param array $filesforzipping - array of files to pass into archive_to_pathname.
     *                                 This array is indexed by the final file name and each
     *                                 element in the array is an instance of a stored_file object.
     * @return path of temp file - note this returned file does
     *         not have a .zip extension - it is a temp file.
     */
    protected function pack_files($filesforzipping) {
        global $CFG;
        // Create path for new zip file.
        $tempzip = tempnam($CFG->tempdir . '/', 'assignment_');
        // Zip files.
        $zipper = new zip_packer();
        if ($zipper->archive_to_pathname($filesforzipping, $tempzip)) {
            return $tempzip;
        }
        return false;
    }

    /**
     * Finds all assignment notifications that have yet to be mailed out, and mails them.
     *
     * Cron function to be run periodically according to the moodle cron.
     *
     * @return bool
     */
    public static function cron() {
        global $DB;

        // Only ever send a max of one days worth of updates.
        $yesterday = time() - (24 * 3600);
        $timenow   = time();

        // Collect all submissions from the past 24 hours that require mailing.
        $sql = 'SELECT a.course, a.name, a.blindmarking, a.revealidentities,
                       g.*, g.id as gradeid, g.timemodified as lastmodified
                 FROM {assign} a
                 JOIN {assign_grades} g ON g.assignment = a.id
                 LEFT JOIN {assign_user_flags} uf ON uf.assignment = a.id AND uf.userid = g.userid
                WHERE g.timemodified >= :yesterday AND
                      g.timemodified <= :today AND
                      uf.mailed = 0';

        $params = array('yesterday' => $yesterday, 'today' => $timenow);
        $submissions = $DB->get_records_sql($sql, $params);

        if (empty($submissions)) {
            return true;
        }

        mtrace('Processing ' . count($submissions) . ' assignment submissions ...');

        // Preload courses we are going to need those.
        $courseids = array();
        foreach ($submissions as $submission) {
            $courseids[] = $submission->course;
        }

        // Filter out duplicates.
        $courseids = array_unique($courseids);
        $ctxselect = context_helper::get_preload_record_columns_sql('ctx');
        list($courseidsql, $params) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
        $sql = 'SELECT c.*, ' . $ctxselect .
                  ' FROM {course} c
             LEFT JOIN {context} ctx ON ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel
                 WHERE c.id ' . $courseidsql;

        $params['contextlevel'] = CONTEXT_COURSE;
        $courses = $DB->get_records_sql($sql, $params);

        // Clean up... this could go on for a while.
        unset($courseids);
        unset($ctxselect);
        unset($courseidsql);
        unset($params);

        // Simple array we'll use for caching modules.
        $modcache = array();

        // Message students about new feedback.
        foreach ($submissions as $submission) {

            mtrace("Processing assignment submission $submission->id ...");

            // Do not cache user lookups - could be too many.
            if (!$user = $DB->get_record('user', array('id'=>$submission->userid))) {
                mtrace('Could not find user ' . $submission->userid);
                continue;
            }

            // Use a cache to prevent the same DB queries happening over and over.
            if (!array_key_exists($submission->course, $courses)) {
                mtrace('Could not find course ' . $submission->course);
                continue;
            }
            $course = $courses[$submission->course];
            if (isset($course->ctxid)) {
                // Context has not yet been preloaded. Do so now.
                context_helper::preload_from_record($course);
            }

            // Override the language and timezone of the "current" user, so that
            // mail is customised for the receiver.
            cron_setup_user($user, $course);

            // Context lookups are already cached.
            $coursecontext = context_course::instance($course->id);
            if (!is_enrolled($coursecontext, $user->id)) {
                $courseshortname = format_string($course->shortname,
                                                 true,
                                                 array('context' => $coursecontext));
                mtrace(fullname($user) . ' not an active participant in ' . $courseshortname);
                continue;
            }

            if (!$grader = $DB->get_record('user', array('id'=>$submission->grader))) {
                mtrace('Could not find grader ' . $submission->grader);
                continue;
            }

            if (!array_key_exists($submission->assignment, $modcache)) {
                $mod = get_coursemodule_from_instance('assign', $submission->assignment, $course->id);
                if (empty($mod)) {
                    mtrace('Could not find course module for assignment id ' . $submission->assignment);
                    continue;
                }
                $modcache[$submission->assignment] = $mod;
            } else {
                $mod = $modcache[$submission->assignment];
            }
            // Context lookups are already cached.
            $contextmodule = context_module::instance($mod->id);

            if (!$mod->visible) {
                // Hold mail notification for hidden assignments until later.
                continue;
            }

            // Need to send this to the student.
            $messagetype = 'feedbackavailable';
            $eventtype = 'assign_notification';
            $updatetime = $submission->lastmodified;
            $modulename = get_string('modulename', 'assign');

            $uniqueid = 0;
            if ($submission->blindmarking && !$submission->revealidentities) {
                $uniqueid = self::get_uniqueid_for_user_static($submission->assignment, $user->id);
            }
            $showusers = $submission->blindmarking && !$submission->revealidentities;
            self::send_assignment_notification($grader,
                                               $user,
                                               $messagetype,
                                               $eventtype,
                                               $updatetime,
                                               $mod,
                                               $contextmodule,
                                               $course,
                                               $modulename,
                                               $submission->name,
                                               $showusers,
                                               $uniqueid);

            $flags = $DB->get_record('assign_user_flags', array('userid'=>$user->id, 'assignment'=>$submission->assignment));
            if ($flags) {
                $flags->mailed = 1;
                $DB->update_record('assign_user_flags', $flags);
            } else {
                $flags = new stdClass();
                $flags->userid = $user->id;
                $flags->assignment = $submission->assignment;
                $flags->mailed = 1;
                $DB->insert_record('assign_user_flags', $flags);
            }

            mtrace('Done');
        }
        mtrace('Done processing ' . count($submissions) . ' assignment submissions');

        cron_setup_user();

        // Free up memory just to be sure.
        unset($courses);
        unset($modcache);

        return true;
    }

    /**
     * Mark in the database that this grade record should have an update notification sent by cron.
     *
     * @param stdClass $grade a grade record keyed on id
     * @return bool true for success
     */
    public function notify_grade_modified($grade) {
        global $DB;

        $flags = $this->get_user_flags($grade->userid, true);
        if ($flags->mailed != 1) {
            $flags->mailed = 0;
        }

        return $this->update_user_flags($flags);
    }

    /**
     * Update user flags for this user in this assignment.
     *
     * @param stdClass $flags a flags record keyed on id
     * @return bool true for success
     */
    public function update_user_flags($flags) {
        global $DB;
        if ($flags->userid <= 0 || $flags->assignment <= 0 || $flags->id <= 0) {
            return false;
        }

        $result = $DB->update_record('assign_user_flags', $flags);
        return $result;
    }

    /**
     * Update a grade in the grade table for the assignment and in the gradebook.
     *
     * @param stdClass $grade a grade record keyed on id
     * @return bool true for success
     */
    public function update_grade($grade) {
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
                // This is a scale.
                if ($scale = $DB->get_record('scale', array('id' => -($this->get_instance()->grade)))) {
                    $scaleoptions = make_menu_from_list($scale->scale);
                    if (!array_key_exists((int) $grade->grade, $scaleoptions)) {
                        return false;
                    }
                }
            }
        }

        if (empty($grade->attemptnumber)) {
            // Set it to the default.
            $grade->attemptnumber = 0;
        }
        $result = $DB->update_record('assign_grades', $grade);

        // Only push to gradebook if the update is for the latest attempt.
        $submission = null;
        if ($this->get_instance()->teamsubmission) {
            $submission = $this->get_group_submission($grade->userid, 0, false);
        } else {
            $submission = $this->get_user_submission($grade->userid, false);
        }
        // Not the latest attempt.
        if ($submission && $submission->attemptnumber != $grade->attemptnumber) {
            return true;
        }

        if ($result) {
            $this->gradebook_item_update(null, $grade);
        }
        return $result;
    }

    /**
     * View the grant extension date page.
     *
     * Uses url parameters 'userid'
     * or from parameter 'selectedusers'
     *
     * @param moodleform $mform - Used for validation of the submitted data
     * @return string
     */
    protected function view_grant_extension($mform) {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/mod/assign/extensionform.php');

        $o = '';
        $batchusers = optional_param('selectedusers', '', PARAM_SEQUENCE);
        $data = new stdClass();
        $data->extensionduedate = null;
        $userid = 0;
        if (!$batchusers) {
            $userid = required_param('userid', PARAM_INT);

            $grade = $this->get_user_grade($userid, false);

            $user = $DB->get_record('user', array('id'=>$userid), '*', MUST_EXIST);

            if ($grade) {
                $data->extensionduedate = $grade->extensionduedate;
            }
            $data->userid = $userid;
        } else {
            $data->batchusers = $batchusers;
        }
        $header = new assign_header($this->get_instance(),
                                    $this->get_context(),
                                    $this->show_intro(),
                                    $this->get_course_module()->id,
                                    get_string('grantextension', 'assign'));
        $o .= $this->get_renderer()->render($header);

        if (!$mform) {
            $formparams = array($this->get_course_module()->id,
                                $userid,
                                $batchusers,
                                $this->get_instance(),
                                $data);
            $mform = new mod_assign_extension_form(null, $formparams);
        }
        $o .= $this->get_renderer()->render(new assign_form('extensionform', $mform));
        $o .= $this->view_footer();
        return $o;
    }

    /**
     * Get a list of the users in the same group as this user.
     *
     * @param int $groupid The id of the group whose members we want or 0 for the default group
     * @param bool $onlyids Whether to retrieve only the user id's
     * @return array The users (possibly id's only)
     */
    public function get_submission_group_members($groupid, $onlyids) {
        $members = array();
        if ($groupid != 0) {
            if ($onlyids) {
                $allusers = groups_get_members($groupid, 'u.id');
            } else {
                $allusers = groups_get_members($groupid);
            }
            foreach ($allusers as $user) {
                if ($this->get_submission_group($user->id)) {
                    $members[] = $user;
                }
            }
        } else {
            $allusers = $this->list_participants(null, $onlyids);
            foreach ($allusers as $user) {
                if ($this->get_submission_group($user->id) == null) {
                    $members[] = $user;
                }
            }
        }
        return $members;
    }

    /**
     * Get a list of the users in the same group as this user that have not submitted the assignment.
     *
     * @param int $groupid The id of the group whose members we want or 0 for the default group
     * @param bool $onlyids Whether to retrieve only the user id's
     * @return array The users (possibly id's only)
     */
    public function get_submission_group_members_who_have_not_submitted($groupid, $onlyids) {
        $instance = $this->get_instance();
        if (!$instance->teamsubmission || !$instance->requireallteammemberssubmit) {
            return array();
        }
        $members = $this->get_submission_group_members($groupid, $onlyids);

        foreach ($members as $id => $member) {
            $submission = $this->get_user_submission($member->id, false);
            if ($submission && $submission->status == ASSIGN_SUBMISSION_STATUS_SUBMITTED) {
                unset($members[$id]);
            } else {
                if ($this->is_blind_marking()) {
                    $members[$id]->alias = get_string('hiddenuser', 'assign') .
                                           $this->get_uniqueid_for_user($id);
                }
            }
        }
        return $members;
    }

    /**
     * Load the group submission object for a particular user, optionally creating it if required.
     *
     * @param int $userid The id of the user whose submission we want
     * @param int $groupid The id of the group for this user - may be 0 in which
     *                     case it is determined from the userid.
     * @param bool $create If set to true a new submission object will be created in the database
     * @param int $attemptnumber - -1 means the latest attempt
     * @return stdClass The submission
     */
    public function get_group_submission($userid, $groupid, $create, $attemptnumber=-1) {
        global $DB;

        if ($groupid == 0) {
            $group = $this->get_submission_group($userid);
            if ($group) {
                $groupid = $group->id;
            }
        }

        // Now get the group submission.
        $params = array('assignment'=>$this->get_instance()->id, 'groupid'=>$groupid, 'userid'=>0);
        if ($attemptnumber >= 0) {
            $params['attemptnumber'] = $attemptnumber;
        }

        // Only return the row with the highest attemptnumber.
        $submission = null;
        $submissions = $DB->get_records('assign_submission', $params, 'attemptnumber DESC', '*', 0, 1);
        if ($submissions) {
            $submission = reset($submissions);
        }

        if ($submission) {
            return $submission;
        }
        if ($create) {
            $submission = new stdClass();
            $submission->assignment = $this->get_instance()->id;
            $submission->userid = 0;
            $submission->groupid = $groupid;
            $submission->timecreated = time();
            $submission->timemodified = $submission->timecreated;
            if ($attemptnumber >= 0) {
                $submission->attemptnumber = $attemptnumber;
            } else {
                $submission->attemptnumber = 0;
            }

            $submission->status = ASSIGN_SUBMISSION_STATUS_DRAFT;
            $sid = $DB->insert_record('assign_submission', $submission);
            $submission->id = $sid;
            return $submission;
        }
        return false;
    }

    /**
     * View a summary listing of all assignments in the current course.
     *
     * @return string
     */
    private function view_course_index() {
        global $USER;

        $o = '';

        $course = $this->get_course();
        $strplural = get_string('modulenameplural', 'assign');

        if (!$cms = get_coursemodules_in_course('assign', $course->id, 'm.duedate')) {
            $o .= $this->get_renderer()->notification(get_string('thereareno', 'moodle', $strplural));
            $o .= $this->get_renderer()->continue_button(new moodle_url('/course/view.php', array('id' => $course->id)));
            return $o;
        }

        $strsectionname  = get_string('sectionname', 'format_'.$course->format);
        $usesections = course_format_uses_sections($course->format);
        $modinfo = get_fast_modinfo($course);

        if ($usesections) {
            $sections = $modinfo->get_section_info_all();
        }
        $courseindexsummary = new assign_course_index_summary($usesections, $strsectionname);

        $timenow = time();

        $currentsection = '';
        foreach ($modinfo->instances['assign'] as $cm) {
            if (!$cm->uservisible) {
                continue;
            }

            $timedue = $cms[$cm->id]->duedate;

            $sectionname = '';
            if ($usesections && $cm->sectionnum) {
                $sectionname = get_section_name($course, $sections[$cm->sectionnum]);
            }

            $submitted = '';
            $context = context_module::instance($cm->id);

            $assignment = new assign($context, $cm, $course);

            if (has_capability('mod/assign:grade', $context)) {
                $submitted = $assignment->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_SUBMITTED);

            } else if (has_capability('mod/assign:submit', $context)) {
                $usersubmission = $assignment->get_user_submission($USER->id, false);

                if (!empty($usersubmission->status)) {
                    $submitted = get_string('submissionstatus_' . $usersubmission->status, 'assign');
                } else {
                    $submitted = get_string('submissionstatus_', 'assign');
                }
            }
            $grading_info = grade_get_grades($course->id, 'mod', 'assign', $cm->instance, $USER->id);
            if (isset($grading_info->items[0]->grades[$USER->id]) &&
                    !$grading_info->items[0]->grades[$USER->id]->hidden ) {
                $grade = $grading_info->items[0]->grades[$USER->id]->str_grade;
            } else {
                $grade = '-';
            }

            $courseindexsummary->add_assign_info($cm->id, $cm->name, $sectionname, $timedue, $submitted, $grade);

        }

        $o .= $this->get_renderer()->render($courseindexsummary);
        $o .= $this->view_footer();

        return $o;
    }

    /**
     * View a page rendered by a plugin.
     *
     * Uses url parameters 'pluginaction', 'pluginsubtype', 'plugin', and 'id'.
     *
     * @return string
     */
    protected function view_plugin_page() {
        global $USER;

        $o = '';

        $pluginsubtype = required_param('pluginsubtype', PARAM_ALPHA);
        $plugintype = required_param('plugin', PARAM_TEXT);
        $pluginaction = required_param('pluginaction', PARAM_ALPHA);

        $plugin = $this->get_plugin_by_type($pluginsubtype, $plugintype);
        if (!$plugin) {
            print_error('invalidformdata', '');
            return;
        }

        $o .= $plugin->view_page($pluginaction);

        return $o;
    }


    /**
     * This is used for team assignments to get the group for the specified user.
     * If the user is a member of multiple or no groups this will return false
     *
     * @param int $userid The id of the user whose submission we want
     * @return mixed The group or false
     */
    public function get_submission_group($userid) {
        $grouping = $this->get_instance()->teamsubmissiongroupingid;
        $groups = groups_get_all_groups($this->get_course()->id, $userid, $grouping);
        if (count($groups) != 1) {
            return false;
        }
        return array_pop($groups);
    }


    /**
     * Display the submission that is used by a plugin.
     *
     * Uses url parameters 'sid', 'gid' and 'plugin'.
     *
     * @param string $pluginsubtype
     * @return string
     */
    protected function view_plugin_content($pluginsubtype) {
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

            // Check permissions.
            if ($item->userid != $USER->id) {
                require_capability('mod/assign:grade', $this->context);
            }
            $o .= $this->get_renderer()->render(new assign_header($this->get_instance(),
                                                              $this->get_context(),
                                                              $this->show_intro(),
                                                              $this->get_course_module()->id,
                                                              $plugin->get_name()));
            $o .= $this->get_renderer()->render(new assign_submission_plugin_submission($plugin,
                                                              $item,
                                                              assign_submission_plugin_submission::FULL,
                                                              $this->get_course_module()->id,
                                                              $this->get_return_action(),
                                                              $this->get_return_params()));

            $logmessage = get_string('viewsubmissionforuser', 'assign', $item->userid);
            $this->add_to_log('view submission', $logmessage);
        } else {
            $plugin = $this->get_feedback_plugin_by_type($plugintype);
            if ($gradeid <= 0) {
                throw new coding_exception('Grade id should not be 0');
            }
            $item = $this->get_grade($gradeid);
            // Check permissions.
            if ($item->userid != $USER->id) {
                require_capability('mod/assign:grade', $this->context);
            }
            $o .= $this->get_renderer()->render(new assign_header($this->get_instance(),
                                                              $this->get_context(),
                                                              $this->show_intro(),
                                                              $this->get_course_module()->id,
                                                              $plugin->get_name()));
            $o .= $this->get_renderer()->render(new assign_feedback_plugin_feedback($plugin,
                                                              $item,
                                                              assign_feedback_plugin_feedback::FULL,
                                                              $this->get_course_module()->id,
                                                              $this->get_return_action(),
                                                              $this->get_return_params()));
            $logmessage = get_string('viewfeedbackforuser', 'assign', $item->userid);
            $this->add_to_log('view feedback', $logmessage);
        }

        $o .= $this->view_return_links();

        $o .= $this->view_footer();
        return $o;
    }

    /**
     * Rewrite plugin file urls so they resolve correctly in an exported zip.
     *
     * @param stdClass $user - The user record
     * @param assign_plugin $plugin - The assignment plugin
     */
    public function download_rewrite_pluginfile_urls($text, $user, $plugin) {
        $groupmode = groups_get_activity_groupmode($this->get_course_module());
        $groupname = '';
        if ($groupmode) {
            $groupid = groups_get_activity_group($this->get_course_module(), true);
            $groupname = groups_get_group_name($groupid).'-';
        }

        if ($this->is_blind_marking()) {
            $prefix = $groupname . get_string('participant', 'assign');
            $prefix = str_replace('_', ' ', $prefix);
            $prefix = clean_filename($prefix . '_' . $this->get_uniqueid_for_user($user->id) . '_');
        } else {
            $prefix = $groupname . fullname($user);
            $prefix = str_replace('_', ' ', $prefix);
            $prefix = clean_filename($prefix . '_' . $this->get_uniqueid_for_user($user->id) . '_');
        }

        $subtype = $plugin->get_subtype();
        $type = $plugin->get_type();
        $prefix = $prefix . $subtype . '_' . $type . '_';

        $result = str_replace('@@PLUGINFILE@@/', $prefix, $text);

        return $result;
    }

    /**
     * Render the content in editor that is often used by plugin.
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

        $finaltext = file_rewrite_pluginfile_urls($text,
                                                  'pluginfile.php',
                                                  $this->get_context()->id,
                                                  $component,
                                                  $filearea,
                                                  $submissionid);
        $params = array('overflowdiv' => true, 'context' => $this->get_context());
        $result .= format_text($finaltext, $format, $params);

        if ($CFG->enableportfolios) {
            require_once($CFG->libdir . '/portfoliolib.php');

            $button = new portfolio_add_button();
            $portfolioparams = array('cmid' => $this->get_course_module()->id,
                                     'sid' => $submissionid,
                                     'plugin' => $plugintype,
                                     'editor' => $editor,
                                     'area'=>$filearea);
            $button->set_callback_options('assign_portfolio_caller', $portfolioparams, 'mod_assign');
            $fs = get_file_storage();

            if ($files = $fs->get_area_files($this->context->id,
                                             $component,
                                             $filearea,
                                             $submissionid,
                                             'timemodified',
                                             false)) {
                $button->set_formats(PORTFOLIO_FORMAT_RICHHTML);
            } else {
                $button->set_formats(PORTFOLIO_FORMAT_PLAINHTML);
            }
            $result .= $button->to_html();
        }
        return $result;
    }

    /**
     * Display a continue page.
     *
     * @return string
     */
    protected function view_savegrading_result($message) {
        $o = '';
        $o .= $this->get_renderer()->render(new assign_header($this->get_instance(),
                                                      $this->get_context(),
                                                      $this->show_intro(),
                                                      $this->get_course_module()->id,
                                                      get_string('savegradingresult', 'assign')));
        $gradingresult = new assign_gradingmessage(get_string('savegradingresult', 'assign'),
                                                   $message,
                                                   $this->get_course_module()->id);
        $o .= $this->get_renderer()->render($gradingresult);
        $o .= $this->view_footer();
        return $o;
    }
    /**
     * Display a grading error.
     *
     * @param string $message - The description of the result
     * @return string
     */
    protected function view_quickgrading_result($message) {
        $o = '';
        $o .= $this->get_renderer()->render(new assign_header($this->get_instance(),
                                                      $this->get_context(),
                                                      $this->show_intro(),
                                                      $this->get_course_module()->id,
                                                      get_string('quickgradingresult', 'assign')));
        $gradingresult = new assign_gradingmessage(get_string('quickgradingresult', 'assign'),
                                                   $message,
                                                   $this->get_course_module()->id);
        $o .= $this->get_renderer()->render($gradingresult);
        $o .= $this->view_footer();
        return $o;
    }

    /**
     * Display the page footer.
     *
     * @return string
     */
    protected function view_footer() {
        return $this->get_renderer()->render_footer();
    }

    /**
     * Does this user have grade permission for this assignment?
     *
     * @return bool
     */
    protected function can_grade() {
        // Permissions check.
        if (!has_capability('mod/assign:grade', $this->context)) {
            return false;
        }

        return true;
    }

    /**
     * Download a zip file of all assignment submissions.
     *
     * @return string - If an error occurs, this will contain the error page.
     */
    protected function download_submissions() {
        global $CFG, $DB;

        // More efficient to load this here.
        require_once($CFG->libdir.'/filelib.php');

        require_capability('mod/assign:grade', $this->context);

        // Load all users with submit.
        $students = get_enrolled_users($this->context, "mod/assign:submit");

        // Build a list of files to zip.
        $filesforzipping = array();
        $fs = get_file_storage();

        $groupmode = groups_get_activity_groupmode($this->get_course_module());
        // All users.
        $groupid = 0;
        $groupname = '';
        if ($groupmode) {
            $groupid = groups_get_activity_group($this->get_course_module(), true);
            $groupname = groups_get_group_name($groupid).'-';
        }

        // Construct the zip file name.
        $filename = clean_filename($this->get_course()->shortname . '-' .
                                   $this->get_instance()->name . '-' .
                                   $groupname.$this->get_course_module()->id . '.zip');

        // Get all the files for each student.
        foreach ($students as $student) {
            $userid = $student->id;

            if ((groups_is_member($groupid, $userid) or !$groupmode or !$groupid)) {
                // Get the plugins to add their own files to the zip.

                $submissiongroup = false;
                $groupname = '';
                if ($this->get_instance()->teamsubmission) {
                    $submission = $this->get_group_submission($userid, 0, false);
                    $submissiongroup = $this->get_submission_group($userid);
                    if ($submissiongroup) {
                        $groupname = $submissiongroup->name . '-';
                    } else {
                        $groupname = get_string('defaultteam', 'assign') . '-';
                    }
                } else {
                    $submission = $this->get_user_submission($userid, false);
                }

                if ($this->is_blind_marking()) {
                    $prefix = str_replace('_', ' ', $groupname . get_string('participant', 'assign'));
                    $prefix = clean_filename($prefix . '_' . $this->get_uniqueid_for_user($userid) . '_');
                } else {
                    $prefix = str_replace('_', ' ', $groupname . fullname($student));
                    $prefix = clean_filename($prefix . '_' . $this->get_uniqueid_for_user($userid) . '_');
                }

                if ($submission) {
                    foreach ($this->submissionplugins as $plugin) {
                        if ($plugin->is_enabled() && $plugin->is_visible()) {
                            $pluginfiles = $plugin->get_files($submission, $student);
                            foreach ($pluginfiles as $zipfilename => $file) {
                                $subtype = $plugin->get_subtype();
                                $type = $plugin->get_type();
                                $prefixedfilename = clean_filename($prefix .
                                                                   $subtype .
                                                                   '_' .
                                                                   $type .
                                                                   '_' .
                                                                   $zipfilename);
                                $filesforzipping[$prefixedfilename] = $file;
                            }
                        }
                    }
                }
            }
        }
        $result = '';
        if (count($filesforzipping) == 0) {
            $header = new assign_header($this->get_instance(),
                                        $this->get_context(),
                                        '',
                                        $this->get_course_module()->id,
                                        get_string('downloadall', 'assign'));
            $result .= $this->get_renderer()->render($header);
            $result .= $this->get_renderer()->notification(get_string('nosubmission', 'assign'));
            $url = new moodle_url('/mod/assign/view.php', array('id'=>$this->get_course_module()->id,
                                                                    'action'=>'grading'));
            $result .= $this->get_renderer()->continue_button($url);
            $result .= $this->view_footer();
        } else if ($zipfile = $this->pack_files($filesforzipping)) {
            $this->add_to_log('download all submissions', get_string('downloadall', 'assign'));
            // Send file and delete after sending.
            send_temp_file($zipfile, $filename);
            // We will not get here - send_temp_file calls exit.
        }
        return $result;
    }

    /**
     * Util function to add a message to the log.
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

        add_to_log($this->get_course()->id,
                   'assign',
                   $action,
                   $fullurl,
                   $info,
                   $this->get_course_module()->id,
                   $USER->id);
    }

    /**
     * Lazy load the page renderer and expose the renderer to plugins.
     *
     * @return assign_renderer
     */
    public function get_renderer() {
        global $PAGE;
        if ($this->output) {
            return $this->output;
        }
        $this->output = $PAGE->get_renderer('mod_assign');
        return $this->output;
    }

    /**
     * Load the submission object for a particular user, optionally creating it if required.
     *
     * For team assignments there are 2 submissions - the student submission and the team submission
     * All files are associated with the team submission but the status of the students contribution is
     * recorded separately.
     *
     * @param int $userid The id of the user whose submission we want or 0 in which case USER->id is used
     * @param bool $create optional - defaults to false. If set to true a new submission object
     *                     will be created in the database.
     * @param int $attemptnumber - -1 means the latest attempt
     * @return stdClass The submission
     */
    public function get_user_submission($userid, $create, $attemptnumber=-1) {
        global $DB, $USER;

        if (!$userid) {
            $userid = $USER->id;
        }
        // If the userid is not null then use userid.
        $params = array('assignment'=>$this->get_instance()->id, 'userid'=>$userid, 'groupid'=>0);
        if ($attemptnumber >= 0) {
            $params['attemptnumber'] = $attemptnumber;
        }

        // Only return the row with the highest attemptnumber.
        $submission = null;
        $submissions = $DB->get_records('assign_submission', $params, 'attemptnumber DESC', '*', 0, 1);
        if ($submissions) {
            $submission = reset($submissions);
        }

        if ($submission) {
            return $submission;
        }
        if ($create) {
            $submission = new stdClass();
            $submission->assignment   = $this->get_instance()->id;
            $submission->userid       = $userid;
            $submission->timecreated = time();
            $submission->timemodified = $submission->timecreated;
            $submission->status = ASSIGN_SUBMISSION_STATUS_DRAFT;
            if ($attemptnumber >= 0) {
                $submission->attemptnumber = $attemptnumber;
            } else {
                $submission->attemptnumber = 0;
            }
            $sid = $DB->insert_record('assign_submission', $submission);
            $submission->id = $sid;
            return $submission;
        }
        return false;
    }

    /**
     * Load the submission object from it's id.
     *
     * @param int $submissionid The id of the submission we want
     * @return stdClass The submission
     */
    protected function get_submission($submissionid) {
        global $DB;

        $params = array('assignment'=>$this->get_instance()->id, 'id'=>$submissionid);
        return $DB->get_record('assign_submission', $params, '*', MUST_EXIST);
    }

    /**
     * This will retrieve a user flags object from the db optionally creating it if required.
     * The user flags was split from the user_grades table in 2.5.
     *
     * @param int $userid The user we are getting the flags for.
     * @param bool $create If true the flags record will be created if it does not exist
     * @return stdClass The flags record
     */
    public function get_user_flags($userid, $create) {
        global $DB, $USER;

        // If the userid is not null then use userid.
        if (!$userid) {
            $userid = $USER->id;
        }

        $params = array('assignment'=>$this->get_instance()->id, 'userid'=>$userid);

        $flags = $DB->get_record('assign_user_flags', $params);

        if ($flags) {
            return $flags;
        }
        if ($create) {
            $flags = new stdClass();
            $flags->assignment = $this->get_instance()->id;
            $flags->userid = $userid;
            $flags->locked = 0;
            $flags->extensionduedate = 0;

            // The mailed flag can be one of 3 values: 0 is unsent, 1 is sent and 2 is do not send yet.
            // This is because students only want to be notified about certain types of update (grades and feedback).
            $flags->mailed = 2;

            $fid = $DB->insert_record('assign_user_flags', $flags);
            $flags->id = $fid;
            return $flags;
        }
        return false;
    }

    /**
     * This will retrieve a grade object from the db, optionally creating it if required.
     *
     * @param int $userid The user we are grading
     * @param bool $create If true the grade will be created if it does not exist
     * @param int $attemptnumber The attempt number to retrieve the grade for. -1 means the latest submission.
     * @return stdClass The grade record
     */
    public function get_user_grade($userid, $create, $attemptnumber=-1) {
        global $DB, $USER;

        // If the userid is not null then use userid.
        if (!$userid) {
            $userid = $USER->id;
        }

        $params = array('assignment'=>$this->get_instance()->id, 'userid'=>$userid);
        if ($attemptnumber < 0) {
            // Make sure this grade matches the latest submission attempt.
            if ($this->get_instance()->teamsubmission) {
                $submission = $this->get_group_submission($userid, 0, false);
            } else {
                $submission = $this->get_user_submission($userid, false);
            }
            if ($submission) {
                $attemptnumber = $submission->attemptnumber;
            }
        }

        if ($attemptnumber >= 0) {
            $params['attemptnumber'] = $attemptnumber;
        }

        $grades = $DB->get_records('assign_grades', $params, 'attemptnumber DESC', '*', 0, 1);

        if ($grades) {
            return reset($grades);
        }
        if ($create) {
            $grade = new stdClass();
            $grade->assignment   = $this->get_instance()->id;
            $grade->userid       = $userid;
            $grade->timecreated = time();
            $grade->timemodified = $grade->timecreated;
            $grade->grade = -1;
            $grade->grader = $USER->id;
            if ($attemptnumber >= 0) {
                $grade->attemptnumber = $attemptnumber;
            }

            $gid = $DB->insert_record('assign_grades', $grade);
            $grade->id = $gid;
            return $grade;
        }
        return false;
    }

    /**
     * This will retrieve a grade object from the db.
     *
     * @param int $gradeid The id of the grade
     * @return stdClass The grade record
     */
    protected function get_grade($gradeid) {
        global $DB;

        $params = array('assignment'=>$this->get_instance()->id, 'id'=>$gradeid);
        return $DB->get_record('assign_grades', $params, '*', MUST_EXIST);
    }

    /**
     * Print the grading page for a single user submission.
     *
     * @param moodleform $mform
     * @return string
     */
    protected function view_single_grade_page($mform) {
        global $DB, $CFG;

        $o = '';
        $instance = $this->get_instance();

        require_once($CFG->dirroot . '/mod/assign/gradeform.php');

        // Need submit permission to submit an assignment.
        require_capability('mod/assign:grade', $this->context);

        $header = new assign_header($instance,
                                    $this->get_context(),
                                    false,
                                    $this->get_course_module()->id,
                                    get_string('grading', 'assign'));
        $o .= $this->get_renderer()->render($header);

        // If userid is passed - we are only grading a single student.
        $rownum = required_param('rownum', PARAM_INT);
        $useridlistid = optional_param('useridlistid', time(), PARAM_INT);
        $userid = optional_param('userid', 0, PARAM_INT);
        $attemptnumber = optional_param('attemptnumber', -1, PARAM_INT);

        $cache = cache::make_from_params(cache_store::MODE_SESSION, 'mod_assign', 'useridlist');
        if (!$userid) {
            if (!$useridlist = $cache->get($this->get_course_module()->id . '_' . $useridlistid)) {
                $useridlist = $this->get_grading_userid_list();
            }
            $cache->set($this->get_course_module()->id . '_' . $useridlistid, $useridlist);
        } else {
            $rownum = 0;
            $useridlist = array($userid);
        }

        if ($rownum < 0 || $rownum > count($useridlist)) {
            throw new coding_exception('Row is out of bounds for the current grading table: ' . $rownum);
        }

        $last = false;
        $userid = $useridlist[$rownum];
        if ($rownum == count($useridlist) - 1) {
            $last = true;
        }
        $user = $DB->get_record('user', array('id' => $userid));
        if ($user) {
            $viewfullnames = has_capability('moodle/site:viewfullnames', $this->get_course_context());
            $usersummary = new assign_user_summary($user,
                                                   $this->get_course()->id,
                                                   $viewfullnames,
                                                   $this->is_blind_marking(),
                                                   $this->get_uniqueid_for_user($user->id),
                                                   get_extra_user_fields($this->get_context()));
            $o .= $this->get_renderer()->render($usersummary);
        }
        $submission = $this->get_user_submission($userid, false, $attemptnumber);
        $submissiongroup = null;
        $submissiongroupmemberswhohavenotsubmitted = array();
        $teamsubmission = null;
        $notsubmitted = array();
        if ($instance->teamsubmission) {
            $teamsubmission = $this->get_group_submission($userid, 0, false, $attemptnumber);
            $submissiongroup = $this->get_submission_group($userid);
            $groupid = 0;
            if ($submissiongroup) {
                $groupid = $submissiongroup->id;
            }
            $notsubmitted = $this->get_submission_group_members_who_have_not_submitted($groupid, false);

        }

        // Get the requested grade.
        $grade = $this->get_user_grade($userid, false, $attemptnumber);
        $flags = $this->get_user_flags($userid, false);
        if ($this->can_view_submission($userid)) {
            $gradelocked = ($flags && $flags->locked) || $this->grading_disabled($userid);
            $extensionduedate = null;
            if ($flags) {
                $extensionduedate = $flags->extensionduedate;
            }
            $showedit = $this->submissions_open($userid) && ($this->is_any_submission_plugin_enabled());

            if ($teamsubmission) {
                $showsubmit = $showedit &&
                              $teamsubmission &&
                              ($teamsubmission->status == ASSIGN_SUBMISSION_STATUS_DRAFT);
            } else {
                $showsubmit = $showedit &&
                              $submission &&
                              ($submission->status == ASSIGN_SUBMISSION_STATUS_DRAFT);
            }
            if (!$this->get_instance()->submissiondrafts) {
                $showsubmit = false;
            }
            $viewfullnames = has_capability('moodle/site:viewfullnames', $this->get_course_context());

            $submissionstatus = new assign_submission_status($instance->allowsubmissionsfromdate,
                                                             $instance->alwaysshowdescription,
                                                             $submission,
                                                             $instance->teamsubmission,
                                                             $teamsubmission,
                                                             $submissiongroup,
                                                             $notsubmitted,
                                                             $this->is_any_submission_plugin_enabled(),
                                                             $gradelocked,
                                                             $this->is_graded($userid),
                                                             $instance->duedate,
                                                             $instance->cutoffdate,
                                                             $this->get_submission_plugins(),
                                                             $this->get_return_action(),
                                                             $this->get_return_params(),
                                                             $this->get_course_module()->id,
                                                             $this->get_course()->id,
                                                             assign_submission_status::GRADER_VIEW,
                                                             $showedit,
                                                             $showsubmit,
                                                             $viewfullnames,
                                                             $extensionduedate,
                                                             $this->get_context(),
                                                             $this->is_blind_marking(),
                                                             '',
                                                             $instance->attemptreopenmethod,
                                                             $instance->maxattempts);
            $o .= $this->get_renderer()->render($submissionstatus);
        }

        if ($grade) {
            $data = new stdClass();
            if ($grade->grade !== null && $grade->grade >= 0) {
                $data->grade = format_float($grade->grade, 2);
            }
        } else {
            $data = new stdClass();
            $data->grade = '';
        }
        // Warning if required.
        $allsubmissions = $this->get_all_submissions($userid);

        if ($attemptnumber != -1) {
            $params = array('attemptnumber'=>$attemptnumber + 1,
                            'totalattempts'=>count($allsubmissions));
            $message = get_string('editingpreviousfeedbackwarning', 'assign', $params);
            $o .= $this->get_renderer()->notification($message);
        }

        // Now show the grading form.
        if (!$mform) {
            $pagination = array('rownum'=>$rownum,
                                'useridlistid'=>$useridlistid,
                                'last'=>$last,
                                'userid'=>optional_param('userid', 0, PARAM_INT),
                                'attemptnumber'=>$attemptnumber);
            $formparams = array($this, $data, $pagination);
            $mform = new mod_assign_grade_form(null,
                                               $formparams,
                                               'post',
                                               '',
                                               array('class'=>'gradeform'));
        }
        $o .= $this->get_renderer()->heading(get_string('grade'), 3);
        $o .= $this->get_renderer()->render(new assign_form('gradingform', $mform));

        if (count($allsubmissions) > 1 && $attemptnumber == -1) {
            $allgrades = $this->get_all_grades($userid);
            $history = new assign_attempt_history($allsubmissions,
                                                  $allgrades,
                                                  $this->get_submission_plugins(),
                                                  $this->get_feedback_plugins(),
                                                  $this->get_course_module()->id,
                                                  $this->get_return_action(),
                                                  $this->get_return_params(),
                                                  true);

            $o .= $this->get_renderer()->render($history);
        }

        $msg = get_string('viewgradingformforstudent',
                          'assign',
                          array('id'=>$user->id, 'fullname'=>fullname($user)));
        $this->add_to_log('view grading form', $msg);

        $o .= $this->view_footer();
        return $o;
    }

    /**
     * Show a confirmation page to make sure they want to release student identities.
     *
     * @return string
     */
    protected function view_reveal_identities_confirm() {
        global $CFG, $USER;

        require_capability('mod/assign:revealidentities', $this->get_context());

        $o = '';
        $header = new assign_header($this->get_instance(),
                                    $this->get_context(),
                                    false,
                                    $this->get_course_module()->id);
        $o .= $this->get_renderer()->render($header);

        $urlparams = array('id'=>$this->get_course_module()->id,
                           'action'=>'revealidentitiesconfirm',
                           'sesskey'=>sesskey());
        $confirmurl = new moodle_url('/mod/assign/view.php', $urlparams);

        $urlparams = array('id'=>$this->get_course_module()->id,
                           'action'=>'grading');
        $cancelurl = new moodle_url('/mod/assign/view.php', $urlparams);

        $o .= $this->get_renderer()->confirm(get_string('revealidentitiesconfirm', 'assign'),
                                             $confirmurl,
                                             $cancelurl);
        $o .= $this->view_footer();
        $this->add_to_log('view', get_string('viewrevealidentitiesconfirm', 'assign'));
        return $o;
    }

    /**
     * View a link to go back to the previous page. Uses url parameters returnaction and returnparams.
     *
     * @return string
     */
    protected function view_return_links() {
        $returnaction = optional_param('returnaction', '', PARAM_ALPHA);
        $returnparams = optional_param('returnparams', '', PARAM_TEXT);

        $params = array();
        $returnparams = str_replace('&amp;', '&', $returnparams);
        parse_str($returnparams, $params);
        $newparams = array('id' => $this->get_course_module()->id, 'action' => $returnaction);
        $params = array_merge($newparams, $params);

        $url = new moodle_url('/mod/assign/view.php', $params);
        return $this->get_renderer()->single_button($url, get_string('back'), 'get');
    }

    /**
     * View the grading table of all submissions for this assignment.
     *
     * @return string
     */
    protected function view_grading_table() {
        global $USER, $CFG;

        // Include grading options form.
        require_once($CFG->dirroot . '/mod/assign/gradingoptionsform.php');
        require_once($CFG->dirroot . '/mod/assign/quickgradingform.php');
        require_once($CFG->dirroot . '/mod/assign/gradingbatchoperationsform.php');
        $o = '';
        $cmid = $this->get_course_module()->id;

        $links = array();
        if (has_capability('gradereport/grader:view', $this->get_course_context()) &&
                has_capability('moodle/grade:viewall', $this->get_course_context())) {
            $gradebookurl = '/grade/report/grader/index.php?id=' . $this->get_course()->id;
            $links[$gradebookurl] = get_string('viewgradebook', 'assign');
        }
        if ($this->is_any_submission_plugin_enabled() && $this->count_submissions()) {
            $downloadurl = '/mod/assign/view.php?id=' . $cmid . '&action=downloadall';
            $links[$downloadurl] = get_string('downloadall', 'assign');
        }
        if ($this->is_blind_marking() &&
                has_capability('mod/assign:revealidentities', $this->get_context())) {
            $revealidentitiesurl = '/mod/assign/view.php?id=' . $cmid . '&action=revealidentities';
            $links[$revealidentitiesurl] = get_string('revealidentities', 'assign');
        }
        foreach ($this->get_feedback_plugins() as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {
                foreach ($plugin->get_grading_actions() as $action => $description) {
                    $url = '/mod/assign/view.php' .
                           '?id=' .  $cmid .
                           '&plugin=' . $plugin->get_type() .
                           '&pluginsubtype=assignfeedback' .
                           '&action=viewpluginpage&pluginaction=' . $action;
                    $links[$url] = $description;
                }
            }
        }

        $gradingactions = new url_select($links);
        $gradingactions->set_label(get_string('choosegradingaction', 'assign'));

        $gradingmanager = get_grading_manager($this->get_context(), 'mod_assign', 'submissions');

        $perpage = get_user_preferences('assign_perpage', 10);
        $filter = get_user_preferences('assign_filter', '');
        $controller = $gradingmanager->get_active_controller();
        $showquickgrading = empty($controller);
        $quickgrading = get_user_preferences('assign_quickgrading', false);

        // Print options for changing the filter and changing the number of results per page.
        $gradingoptionsformparams = array('cm'=>$cmid,
                                          'contextid'=>$this->context->id,
                                          'userid'=>$USER->id,
                                          'submissionsenabled'=>$this->is_any_submission_plugin_enabled(),
                                          'showquickgrading'=>$showquickgrading,
                                          'quickgrading'=>$quickgrading);

        $classoptions = array('class'=>'gradingoptionsform');
        $gradingoptionsform = new mod_assign_grading_options_form(null,
                                                                  $gradingoptionsformparams,
                                                                  'post',
                                                                  '',
                                                                  $classoptions);

        $batchformparams = array('cm'=>$cmid,
                                 'submissiondrafts'=>$this->get_instance()->submissiondrafts,
                                 'duedate'=>$this->get_instance()->duedate,
                                 'attemptreopenmethod'=>$this->get_instance()->attemptreopenmethod,
                                 'feedbackplugins'=>$this->get_feedback_plugins(),
                                 'context'=>$this->get_context());
        $classoptions = array('class'=>'gradingbatchoperationsform');

        $gradingbatchoperationsform = new mod_assign_grading_batch_operations_form(null,
                                                                                   $batchformparams,
                                                                                   'post',
                                                                                   '',
                                                                                   $classoptions);

        $gradingoptionsdata = new stdClass();
        $gradingoptionsdata->perpage = $perpage;
        $gradingoptionsdata->filter = $filter;
        $gradingoptionsform->set_data($gradingoptionsdata);

        $actionformtext = $this->get_renderer()->render($gradingactions);
        $header = new assign_header($this->get_instance(),
                                    $this->get_context(),
                                    false,
                                    $this->get_course_module()->id,
                                    get_string('grading', 'assign'),
                                    $actionformtext);
        $o .= $this->get_renderer()->render($header);

        $currenturl = $CFG->wwwroot .
                      '/mod/assign/view.php?id=' .
                      $this->get_course_module()->id .
                      '&action=grading';

        $o .= groups_print_activity_menu($this->get_course_module(), $currenturl, true);

        // Plagiarism update status apearring in the grading book.
        if (!empty($CFG->enableplagiarism)) {
            require_once($CFG->libdir . '/plagiarismlib.php');
            $o .= plagiarism_update_status($this->get_course(), $this->get_course_module());
        }

        // Load and print the table of submissions.
        if ($showquickgrading && $quickgrading) {
            $gradingtable = new assign_grading_table($this, $perpage, $filter, 0, true);
            $table = $this->get_renderer()->render($gradingtable);
            $quickformparams = array('cm'=>$this->get_course_module()->id, 'gradingtable'=>$table);
            $quickgradingform = new mod_assign_quick_grading_form(null, $quickformparams);

            $o .= $this->get_renderer()->render(new assign_form('quickgradingform', $quickgradingform));
        } else {
            $gradingtable = new assign_grading_table($this, $perpage, $filter, 0, false);
            $o .= $this->get_renderer()->render($gradingtable);
        }

        $currentgroup = groups_get_activity_group($this->get_course_module(), true);
        $users = array_keys($this->list_participants($currentgroup, true));
        if (count($users) != 0) {
            // If no enrolled user in a course then don't display the batch operations feature.
            $assignform = new assign_form('gradingbatchoperationsform', $gradingbatchoperationsform);
            $o .= $this->get_renderer()->render($assignform);
        }
        $assignform = new assign_form('gradingoptionsform',
                                      $gradingoptionsform,
                                      'M.mod_assign.init_grading_options');
        $o .= $this->get_renderer()->render($assignform);
        return $o;
    }

    /**
     * View entire grading page.
     *
     * @return string
     */
    protected function view_grading_page() {
        global $CFG;

        $o = '';
        // Need submit permission to submit an assignment.
        require_capability('mod/assign:grade', $this->context);
        require_once($CFG->dirroot . '/mod/assign/gradeform.php');

        // Only load this if it is.

        $o .= $this->view_grading_table();

        $o .= $this->view_footer();

        $logmessage = get_string('viewsubmissiongradingtable', 'assign');
        $this->add_to_log('view submission grading table', $logmessage);
        return $o;
    }

    /**
     * Capture the output of the plagiarism plugins disclosures and return it as a string.
     *
     * @return void
     */
    protected function plagiarism_print_disclosure() {
        global $CFG;
        $o = '';

        if (!empty($CFG->enableplagiarism)) {
            require_once($CFG->libdir . '/plagiarismlib.php');

            $o .= plagiarism_print_disclosure($this->get_course_module()->id);
        }

        return $o;
    }

    /**
     * Message for students when assignment submissions have been closed.
     *
     * @return string
     */
    protected function view_student_error_message() {
        global $CFG;

        $o = '';
        // Need submit permission to submit an assignment.
        require_capability('mod/assign:submit', $this->context);

        $header = new assign_header($this->get_instance(),
                                    $this->get_context(),
                                    $this->show_intro(),
                                    $this->get_course_module()->id,
                                    get_string('editsubmission', 'assign'));
        $o .= $this->get_renderer()->render($header);

        $o .= $this->get_renderer()->notification(get_string('submissionsclosed', 'assign'));

        $o .= $this->view_footer();

        return $o;

    }

    /**
     * View edit submissions page.
     *
     * @param moodleform $mform
     * @param array $notices A list of notices to display at the top of the
     *                       edit submission form (e.g. from plugins).
     * @return string The page output.
     */
    protected function view_edit_submission_page($mform, $notices) {
        global $CFG;

        $o = '';
        require_once($CFG->dirroot . '/mod/assign/submission_form.php');
        // Need submit permission to submit an assignment.
        require_capability('mod/assign:submit', $this->context);

        if (!$this->submissions_open()) {
            return $this->view_student_error_message();
        }
        $o .= $this->get_renderer()->render(new assign_header($this->get_instance(),
                                                      $this->get_context(),
                                                      $this->show_intro(),
                                                      $this->get_course_module()->id,
                                                      get_string('editsubmission', 'assign')));
        $o .= $this->plagiarism_print_disclosure();
        $data = new stdClass();

        if (!$mform) {
            $mform = new mod_assign_submission_form(null, array($this, $data));
        }

        foreach ($notices as $notice) {
            $o .= $this->get_renderer()->notification($notice);
        }

        $o .= $this->get_renderer()->render(new assign_form('editsubmissionform', $mform));

        $o .= $this->view_footer();
        $this->add_to_log('view submit assignment form', get_string('viewownsubmissionform', 'assign'));

        return $o;
    }

    /**
     * See if this assignment has a grade yet.
     *
     * @param int $userid
     * @return bool
     */
    protected function is_graded($userid) {
        $grade = $this->get_user_grade($userid, false);
        if ($grade) {
            return ($grade->grade !== null && $grade->grade >= 0);
        }
        return false;
    }

    /**
     * Perform an access check to see if the current $USER can view this group submission.
     *
     * @param int $groupid
     * @return bool
     */
    public function can_view_group_submission($groupid) {
        global $USER;

        if (!is_enrolled($this->get_course_context(), $USER->id)) {
            return false;
        }
        if (has_capability('mod/assign:grade', $this->context)) {
            return true;
        }
        $members = $this->get_submission_group_members($groupid, true);
        foreach ($members as $member) {
            if ($member->id == $USER->id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Perform an access check to see if the current $USER can view this users submission.
     *
     * @param int $userid
     * @return bool
     */
    public function can_view_submission($userid) {
        global $USER;

        if (is_siteadmin()) {
            return true;
        }
        if (!is_enrolled($this->get_course_context(), $userid)) {
            return false;
        }
        if ($userid == $USER->id && has_capability('mod/assign:submit', $this->context)) {
            return true;
        }
        if (has_capability('mod/assign:grade', $this->context)) {
            return true;
        }
        return false;
    }

    /**
     * Allows the plugin to show a batch grading operation page.
     *
     * @return none
     */
    protected function view_plugin_grading_batch_operation($mform) {
        require_capability('mod/assign:grade', $this->context);
        $prefix = 'plugingradingbatchoperation_';

        if ($data = $mform->get_data()) {
            $tail = substr($data->operation, strlen($prefix));
            list($plugintype, $action) = explode('_', $tail, 2);

            $plugin = $this->get_feedback_plugin_by_type($plugintype);
            if ($plugin) {
                $users = $data->selectedusers;
                $userlist = explode(',', $users);
                echo $plugin->grading_batch_operation($action, $userlist);
                return;
            }
        }
        print_error('invalidformdata', '');
    }

    /**
     * Ask the user to confirm they want to perform this batch operation
     *
     * @param moodleform $mform Set to a grading batch operations form
     * @return string - the page to view after processing these actions
     */
    protected function process_grading_batch_operation(& $mform) {
        global $CFG;
        require_once($CFG->dirroot . '/mod/assign/gradingbatchoperationsform.php');
        require_sesskey();

        $batchformparams = array('cm'=>$this->get_course_module()->id,
                                 'submissiondrafts'=>$this->get_instance()->submissiondrafts,
                                 'duedate'=>$this->get_instance()->duedate,
                                 'attemptreopenmethod'=>$this->get_instance()->attemptreopenmethod,
                                 'feedbackplugins'=>$this->get_feedback_plugins(),
                                 'context'=>$this->get_context());
        $formclasses = array('class'=>'gradingbatchoperationsform');
        $mform = new mod_assign_grading_batch_operations_form(null,
                                                              $batchformparams,
                                                              'post',
                                                              '',
                                                              $formclasses);

        if ($data = $mform->get_data()) {
            // Get the list of users.
            $users = $data->selectedusers;
            $userlist = explode(',', $users);

            $prefix = 'plugingradingbatchoperation_';

            if ($data->operation == 'grantextension') {
                // Reset the form so the grant extension page will create the extension form.
                $mform = null;
                return 'grantextension';
            } else if (strpos($data->operation, $prefix) === 0) {
                $tail = substr($data->operation, strlen($prefix));
                list($plugintype, $action) = explode('_', $tail, 2);

                $plugin = $this->get_feedback_plugin_by_type($plugintype);
                if ($plugin) {
                    return 'plugingradingbatchoperation';
                }
            }

            foreach ($userlist as $userid) {
                if ($data->operation == 'lock') {
                    $this->process_lock($userid);
                } else if ($data->operation == 'unlock') {
                    $this->process_unlock($userid);
                } else if ($data->operation == 'reverttodraft') {
                    $this->process_revert_to_draft($userid);
                } else if ($data->operation == 'addattempt') {
                    if (!$this->get_instance()->teamsubmission) {
                        $this->process_add_attempt($userid);
                    }
                }
            }
            if ($this->get_instance()->teamsubmission && $data->operation == 'addattempt') {
                // This needs to be handled separately so that each team submission is only re-opened one time.
                $this->process_add_attempt_group($userlist);
            }
        }

        return 'grading';
    }

    /**
     * Ask the user to confirm they want to submit their work for grading.
     *
     * @param $mform moodleform - null unless form validation has failed
     * @return string
     */
    protected function check_submit_for_grading($mform) {
        global $USER, $CFG;

        require_once($CFG->dirroot . '/mod/assign/submissionconfirmform.php');

        // Check that all of the submission plugins are ready for this submission.
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

        $data = new stdClass();
        $adminconfig = $this->get_admin_config();
        $requiresubmissionstatement = (!empty($adminconfig->requiresubmissionstatement) ||
                                       $this->get_instance()->requiresubmissionstatement) &&
                                       !empty($adminconfig->submissionstatement);

        $submissionstatement = '';
        if (!empty($adminconfig->submissionstatement)) {
            $submissionstatement = $adminconfig->submissionstatement;
        }

        if ($mform == null) {
            $mform = new mod_assign_confirm_submission_form(null, array($requiresubmissionstatement,
                                                                        $submissionstatement,
                                                                        $this->get_course_module()->id,
                                                                        $data));
        }
        $o = '';
        $o .= $this->get_renderer()->header();
        $submitforgradingpage = new assign_submit_for_grading_page($notifications,
                                                                   $this->get_course_module()->id,
                                                                   $mform);
        $o .= $this->get_renderer()->render($submitforgradingpage);
        $o .= $this->view_footer();

        $logmessage = get_string('viewownsubmissionform', 'assign');
        $this->add_to_log('view confirm submit assignment form', $logmessage);

        return $o;
    }

    /**
     * Print 2 tables of information with no action links -
     * the submission summary and the grading summary.
     *
     * @param stdClass $user the user to print the report for
     * @param bool $showlinks - Return plain text or links to the profile
     * @return string - the html summary
     */
    public function view_student_summary($user, $showlinks) {
        global $CFG, $DB, $PAGE;

        $instance = $this->get_instance();
        $grade = $this->get_user_grade($user->id, false);
        $flags = $this->get_user_flags($user->id, false);
        $submission = $this->get_user_submission($user->id, false);
        $o = '';

        $teamsubmission = null;
        $submissiongroup = null;
        $notsubmitted = array();
        if ($instance->teamsubmission) {
            $teamsubmission = $this->get_group_submission($user->id, 0, false);
            $submissiongroup = $this->get_submission_group($user->id);
            $groupid = 0;
            if ($submissiongroup) {
                $groupid = $submissiongroup->id;
            }
            $notsubmitted = $this->get_submission_group_members_who_have_not_submitted($groupid, false);
        }

        if ($this->can_view_submission($user->id)) {
            $showedit = has_capability('mod/assign:submit', $this->context) &&
                        $this->submissions_open($user->id) &&
                        ($this->is_any_submission_plugin_enabled()) &&
                        $showlinks;

            $gradelocked = ($flags && $flags->locked) || $this->grading_disabled($user->id);

            // Grading criteria preview.
            $gradingmanager = get_grading_manager($this->context, 'mod_assign', 'submissions');
            $gradingcontrollerpreview = '';
            if ($gradingmethod = $gradingmanager->get_active_method()) {
                $controller = $gradingmanager->get_controller($gradingmethod);
                if ($controller->is_form_defined()) {
                    $gradingcontrollerpreview = $controller->render_preview($PAGE);
                }
            }

            $showsubmit = ($submission || $teamsubmission) && $showlinks;
            if ($teamsubmission && ($teamsubmission->status != ASSIGN_SUBMISSION_STATUS_DRAFT)) {
                $showsubmit = false;
            }
            if ($submission && ($submission->status != ASSIGN_SUBMISSION_STATUS_DRAFT)) {
                $showsubmit = false;
            }
            if (!$this->get_instance()->submissiondrafts) {
                $showsubmit = false;
            }
            $extensionduedate = null;
            if ($flags) {
                $extensionduedate = $flags->extensionduedate;
            }
            $viewfullnames = has_capability('moodle/site:viewfullnames', $this->get_course_context());

            $submissionstatus = new assign_submission_status($instance->allowsubmissionsfromdate,
                                                              $instance->alwaysshowdescription,
                                                              $submission,
                                                              $instance->teamsubmission,
                                                              $teamsubmission,
                                                              $submissiongroup,
                                                              $notsubmitted,
                                                              $this->is_any_submission_plugin_enabled(),
                                                              $gradelocked,
                                                              $this->is_graded($user->id),
                                                              $instance->duedate,
                                                              $instance->cutoffdate,
                                                              $this->get_submission_plugins(),
                                                              $this->get_return_action(),
                                                              $this->get_return_params(),
                                                              $this->get_course_module()->id,
                                                              $this->get_course()->id,
                                                              assign_submission_status::STUDENT_VIEW,
                                                              $showedit,
                                                              $showsubmit,
                                                              $viewfullnames,
                                                              $extensionduedate,
                                                              $this->get_context(),
                                                              $this->is_blind_marking(),
                                                              $gradingcontrollerpreview,
                                                              $instance->attemptreopenmethod,
                                                              $instance->maxattempts);
            $o .= $this->get_renderer()->render($submissionstatus);

            require_once($CFG->libdir.'/gradelib.php');
            require_once($CFG->dirroot.'/grade/grading/lib.php');

            $gradinginfo = grade_get_grades($this->get_course()->id,
                                        'mod',
                                        'assign',
                                        $instance->id,
                                        $user->id);

            $gradingitem = null;
            $gradebookgrade = null;
            if (isset($gradinginfo->items[0])) {
                $gradingitem = $gradinginfo->items[0];
                $gradebookgrade = $gradingitem->grades[$user->id];
            }

            // Check to see if all feedback plugins are empty.
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

            $cangrade = has_capability('mod/assign:grade', $this->get_context());
            // If there is a visible grade, show the summary.
            if ((!empty($gradebookgrade->grade) || !$emptyplugins)
                    && ($cangrade || !$gradebookgrade->hidden)) {

                $gradefordisplay = null;
                $gradeddate = null;
                $grader = null;
                $gradingmanager = get_grading_manager($this->get_context(), 'mod_assign', 'submissions');

                // Only show the grade if it is not hidden in gradebook.
                if (!empty($gradebookgrade->grade) && ($cangrade || !$gradebookgrade->hidden)) {
                    if ($controller = $gradingmanager->get_active_controller()) {
                        $controller->set_grade_range(make_grades_menu($this->get_instance()->grade));
                        $gradefordisplay = $controller->render_grade($PAGE,
                                                                     $grade->id,
                                                                     $gradingitem,
                                                                     $gradebookgrade->str_long_grade,
                                                                     $cangrade);
                    } else {
                        $gradefordisplay = $this->display_grade($gradebookgrade->grade, false);
                    }
                    $gradeddate = $gradebookgrade->dategraded;
                    if (isset($grade->grader)) {
                        $grader = $DB->get_record('user', array('id'=>$grade->grader));
                    }
                }

                $feedbackstatus = new assign_feedback_status($gradefordisplay,
                                                      $gradeddate,
                                                      $grader,
                                                      $this->get_feedback_plugins(),
                                                      $grade,
                                                      $this->get_course_module()->id,
                                                      $this->get_return_action(),
                                                      $this->get_return_params());

                $o .= $this->get_renderer()->render($feedbackstatus);
            }

            $allsubmissions = $this->get_all_submissions($user->id);

            if (count($allsubmissions) > 1) {
                $allgrades = $this->get_all_grades($user->id);
                $history = new assign_attempt_history($allsubmissions,
                                                      $allgrades,
                                                      $this->get_submission_plugins(),
                                                      $this->get_feedback_plugins(),
                                                      $this->get_course_module()->id,
                                                      $this->get_return_action(),
                                                      $this->get_return_params(),
                                                      false);

                $o .= $this->get_renderer()->render($history);
            }

        }
        return $o;
    }

    /**
     * Get the grades for all previous attempts.
     * For each grade - the grader is a full user record,
     * and gradefordisplay is added (rendered from grading manager).
     *
     * @param int $userid If not set, $USER->id will be used.
     * @return array $grades All grade records for this user.
     */
    protected function get_all_grades($userid) {
        global $DB, $USER, $PAGE;

        // If the userid is not null then use userid.
        if (!$userid) {
            $userid = $USER->id;
        }

        $params = array('assignment'=>$this->get_instance()->id, 'userid'=>$userid);

        $grades = $DB->get_records('assign_grades', $params, 'attemptnumber ASC');

        $gradercache = array();
        $cangrade = has_capability('mod/assign:grade', $this->get_context());

        // Need gradingitem and gradingmanager.
        $gradingmanager = get_grading_manager($this->get_context(), 'mod_assign', 'submissions');
        $controller = $gradingmanager->get_active_controller();

        $gradinginfo = grade_get_grades($this->get_course()->id,
                                        'mod',
                                        'assign',
                                        $this->get_instance()->id,
                                        $userid);

        $gradingitem = null;
        if (isset($gradinginfo->items[0])) {
            $gradingitem = $gradinginfo->items[0];
        }

        foreach ($grades as $grade) {
            // First lookup the grader info.
            if (isset($gradercache[$grade->grader])) {
                $grade->grader = $gradercache[$grade->grader];
            } else {
                // Not in cache - need to load the grader record.
                $grade->grader = $DB->get_record('user', array('id'=>$grade->grader));
                $gradercache[$grade->grader->id] = $grade->grader;
            }

            // Now get the gradefordisplay.
            if ($controller) {
                $controller->set_grade_range(make_grades_menu($this->get_instance()->grade));
                $grade->gradefordisplay = $controller->render_grade($PAGE,
                                                                     $grade->id,
                                                                     $gradingitem,
                                                                     $grade->grade,
                                                                     $cangrade);
            } else {
                $grade->gradefordisplay = $this->display_grade($grade->grade, false);
            }

        }

        return $grades;
    }

    /**
     * Get the submissions for all previous attempts.
     *
     * @param int $userid If not set, $USER->id will be used.
     * @return array $submissions All submission records for this user (or group).
     */
    protected function get_all_submissions($userid) {
        global $DB, $USER;

        // If the userid is not null then use userid.
        if (!$userid) {
            $userid = $USER->id;
        }

        $params = array();

        if ($this->get_instance()->teamsubmission) {
            $groupid = 0;
            $group = $this->get_submission_group($userid);
            if ($group) {
                $groupid = $group->id;
            }

            // Params to get the group submissions.
            $params = array('assignment'=>$this->get_instance()->id, 'groupid'=>$groupid, 'userid'=>0);
        } else {
            // Params to get the user submissions.
            $params = array('assignment'=>$this->get_instance()->id, 'userid'=>$userid);
        }

        // Return the submissions ordered by attempt.
        $submissions = $DB->get_records('assign_submission', $params, 'attemptnumber ASC');

        return $submissions;
    }

    /**
     * View submissions page (contains details of current submission).
     *
     * @return string
     */
    protected function view_submission_page() {
        global $CFG, $DB, $USER, $PAGE;

        $instance = $this->get_instance();

        $o = '';
        $o .= $this->get_renderer()->render(new assign_header($instance,
                                                      $this->get_context(),
                                                      $this->show_intro(),
                                                      $this->get_course_module()->id));

        if ($this->can_grade()) {
            $draft = ASSIGN_SUBMISSION_STATUS_DRAFT;
            $submitted = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
            if ($instance->teamsubmission) {
                $summary = new assign_grading_summary($this->count_teams(),
                                                      $instance->submissiondrafts,
                                                      $this->count_submissions_with_status($draft),
                                                      $this->is_any_submission_plugin_enabled(),
                                                      $this->count_submissions_with_status($submitted),
                                                      $instance->cutoffdate,
                                                      $instance->duedate,
                                                      $this->get_course_module()->id,
                                                      $this->count_submissions_need_grading(),
                                                      $instance->teamsubmission);
                $o .= $this->get_renderer()->render($summary);
            } else {
                $summary = new assign_grading_summary($this->count_participants(0),
                                                      $instance->submissiondrafts,
                                                      $this->count_submissions_with_status($draft),
                                                      $this->is_any_submission_plugin_enabled(),
                                                      $this->count_submissions_with_status($submitted),
                                                      $instance->cutoffdate,
                                                      $instance->duedate,
                                                      $this->get_course_module()->id,
                                                      $this->count_submissions_need_grading(),
                                                      $instance->teamsubmission);
                $o .= $this->get_renderer()->render($summary);
            }
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
     * Convert the final raw grade(s) in the grading table for the gradebook.
     *
     * @param stdClass $grade
     * @return array
     */
    protected function convert_grade_for_gradebook(stdClass $grade) {
        $gradebookgrade = array();
        if ($grade->grade >= 0) {
            $gradebookgrade['rawgrade'] = $grade->grade;
        }
        $gradebookgrade['userid'] = $grade->userid;
        $gradebookgrade['usermodified'] = $grade->grader;
        $gradebookgrade['datesubmitted'] = null;
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
     * Convert submission details for the gradebook.
     *
     * @param stdClass $submission
     * @return array
     */
    protected function convert_submission_for_gradebook(stdClass $submission) {
        $gradebookgrade = array();

        $gradebookgrade['userid'] = $submission->userid;
        $gradebookgrade['usermodified'] = $submission->userid;
        $gradebookgrade['datesubmitted'] = $submission->timemodified;

        return $gradebookgrade;
    }

    /**
     * Update grades in the gradebook.
     *
     * @param mixed $submission stdClass|null
     * @param mixed $grade stdClass|null
     * @return bool
     */
    protected function gradebook_item_update($submission=null, $grade=null) {

        // Do not push grade to gradebook if blind marking is active as
        // the gradebook would reveal the students.
        if ($this->is_blind_marking()) {
            return false;
        }
        if ($submission != null) {
            if ($submission->userid == 0) {
                // This is a group submission update.
                $team = groups_get_members($submission->groupid, 'u.id');

                foreach ($team as $member) {
                    $submission->groupid = 0;
                    $submission->userid = $member->id;
                    $this->gradebook_item_update($submission, null);
                }
                return;
            }

            $gradebookgrade = $this->convert_submission_for_gradebook($submission);

        } else {
            $gradebookgrade = $this->convert_grade_for_gradebook($grade);
        }
        // Grading is disabled, return.
        if ($this->grading_disabled($gradebookgrade['userid'])) {
            return false;
        }
        $assign = clone $this->get_instance();
        $assign->cmidnumber = $this->get_course_module()->idnumber;

        return assign_grade_item_update($assign, $gradebookgrade);
    }

    /**
     * Update team submission.
     *
     * @param stdClass $submission
     * @param int $userid
     * @param bool $updatetime
     * @return bool
     */
    protected function update_team_submission(stdClass $submission, $userid, $updatetime) {
        global $DB;

        if ($updatetime) {
            $submission->timemodified = time();
        }

        // First update the submission for the current user.
        $mysubmission = $this->get_user_submission($userid, true, $submission->attemptnumber);
        $mysubmission->status = $submission->status;

        $this->update_submission($mysubmission, 0, $updatetime, false);

        // Now check the team settings to see if this assignment qualifies as submitted or draft.
        $team = $this->get_submission_group_members($submission->groupid, true);

        $allsubmitted = true;
        $anysubmitted = false;
        $result = true;
        if ($submission->status != ASSIGN_SUBMISSION_STATUS_REOPENED) {
            foreach ($team as $member) {
                $membersubmission = $this->get_user_submission($member->id, false, $submission->attemptnumber);

                if (!$membersubmission || $membersubmission->status != ASSIGN_SUBMISSION_STATUS_SUBMITTED) {
                    $allsubmitted = false;
                    if ($anysubmitted) {
                        break;
                    }
                } else {
                    $anysubmitted = true;
                }
            }
            if ($this->get_instance()->requireallteammemberssubmit) {
                if ($allsubmitted) {
                    $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
                } else {
                    $submission->status = ASSIGN_SUBMISSION_STATUS_DRAFT;
                }
                $result = $DB->update_record('assign_submission', $submission);
            } else {
                if ($anysubmitted) {
                    $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
                } else {
                    $submission->status = ASSIGN_SUBMISSION_STATUS_DRAFT;
                }
                $result = $DB->update_record('assign_submission', $submission);
            }
        } else {
            // Set the group submission to reopened.
            foreach ($team as $member) {
                $membersubmission = $this->get_user_submission($member->id, true, $submission->attemptnumber);
                $membersubmission->status = ASSIGN_SUBMISSION_STATUS_REOPENED;
                $result = $DB->update_record('assign_submission', $membersubmission) && $result;
            }
            $result = $DB->update_record('assign_submission', $submission) && $result;
        }

        $this->gradebook_item_update($submission);
        return $result;
    }


    /**
     * Update grades in the gradebook based on submission time.
     *
     * @param stdClass $submission
     * @param int $userid
     * @param bool $updatetime
     * @param bool $teamsubmission
     * @return bool
     */
    protected function update_submission(stdClass $submission, $userid, $updatetime, $teamsubmission) {
        global $DB;

        if ($teamsubmission) {
            return $this->update_team_submission($submission, $userid, $updatetime);
        }

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
     * @param int $userid - Optional userid so we can see if a different user can submit
     * @return bool
     */
    public function submissions_open($userid = 0) {
        global $USER;

        if (!$userid) {
            $userid = $USER->id;
        }

        $time = time();
        $dateopen = true;
        $finaldate = false;
        if ($this->get_instance()->cutoffdate) {
            $finaldate = $this->get_instance()->cutoffdate;
        }

        $flags = $this->get_user_flags($userid, false);
        if ($flags && $flags->locked) {
            return false;
        }

        // User extensions.
        if ($finaldate) {
            if ($flags && $flags->extensionduedate) {
                // Extension can be before cut off date.
                if ($flags->extensionduedate > $finaldate) {
                    $finaldate = $flags->extensionduedate;
                }
            }
        }

        if ($finaldate) {
            $dateopen = ($this->get_instance()->allowsubmissionsfromdate <= $time && $time <= $finaldate);
        } else {
            $dateopen = ($this->get_instance()->allowsubmissionsfromdate <= $time);
        }

        if (!$dateopen) {
            return false;
        }

        // Now check if this user has already submitted etc.
        if (!is_enrolled($this->get_course_context(), $userid)) {
            return false;
        }
        $submission = false;
        if ($this->get_instance()->teamsubmission) {
            $submission = $this->get_group_submission($userid, 0, false);
        } else {
            $submission = $this->get_user_submission($userid, false);
        }
        if ($submission) {

            if ($this->get_instance()->submissiondrafts && $submission->status == ASSIGN_SUBMISSION_STATUS_SUBMITTED) {
                // Drafts are tracked and the student has submitted the assignment.
                return false;
            }
        }

        if ($this->grading_disabled($userid)) {
            return false;
        }

        return true;
    }

    /**
     * Render the files in file area.
     *
     * @param string $component
     * @param string $area
     * @param int $submissionid
     * @return string
     */
    public function render_area_files($component, $area, $submissionid) {
        global $USER;

        $fs = get_file_storage();
        $browser = get_file_browser();
        $files = $fs->get_area_files($this->get_context()->id,
                                     $component,
                                     $area,
                                     $submissionid,
                                     'timemodified',
                                     false);
        return $this->get_renderer()->assign_files($this->context, $submissionid, $area, $component);

    }

    /**
     * Returns a list of teachers that should be grading given submission.
     *
     * @param int $userid The submission to grade
     * @return array
     */
    protected function get_graders($userid) {
        $potentialgraders = get_enrolled_users($this->context, 'mod/assign:grade');

        $graders = array();
        if (groups_get_activity_groupmode($this->get_course_module()) == SEPARATEGROUPS) {
            if ($groups = groups_get_all_groups($this->get_course()->id, $userid)) {
                foreach ($groups as $group) {
                    foreach ($potentialgraders as $grader) {
                        if ($grader->id == $userid) {
                            // Do not send self.
                            continue;
                        }
                        if (groups_is_member($group->id, $grader->id)) {
                            $graders[$grader->id] = $grader;
                        }
                    }
                }
            } else {
                // User not in group, try to find graders without group.
                foreach ($potentialgraders as $grader) {
                    if ($grader->id == $userid) {
                        // Do not send self.
                        continue;
                    }
                    if (!groups_has_membership($this->get_course_module(), $grader->id)) {
                        $graders[$grader->id] = $grader;
                    }
                }
            }
        } else {
            foreach ($potentialgraders as $grader) {
                if ($grader->id == $userid) {
                    // Do not send self.
                    continue;
                }
                // Must be enrolled.
                if (is_enrolled($this->get_course_context(), $grader->id)) {
                    $graders[$grader->id] = $grader;
                }
            }
        }
        return $graders;
    }

    /**
     * Format a notification for plain text.
     *
     * @param string $messagetype
     * @param stdClass $info
     * @param stdClass $course
     * @param stdClass $context
     * @param string $modulename
     * @param string $assignmentname
     */
    protected static function format_notification_message_text($messagetype,
                                                             $info,
                                                             $course,
                                                             $context,
                                                             $modulename,
                                                             $assignmentname) {
        $formatparams = array('context' => $context->get_course_context());
        $posttext  = format_string($course->shortname, true, $formatparams) .
                     ' -> ' .
                     $modulename .
                     ' -> ' .
                     format_string($assignmentname, true, $formatparams) . "\n";
        $posttext .= '---------------------------------------------------------------------' . "\n";
        $posttext .= get_string($messagetype . 'text', 'assign', $info)."\n";
        $posttext .= "\n---------------------------------------------------------------------\n";
        return $posttext;
    }

    /**
     * Format a notification for HTML.
     *
     * @param string $messagetype
     * @param stdClass $info
     * @param stdClass $course
     * @param stdClass $context
     * @param string $modulename
     * @param stdClass $coursemodule
     * @param string $assignmentname
     * @param stdClass $info
     */
    protected static function format_notification_message_html($messagetype,
                                                             $info,
                                                             $course,
                                                             $context,
                                                             $modulename,
                                                             $coursemodule,
                                                             $assignmentname) {
        global $CFG;
        $formatparams = array('context' => $context->get_course_context());
        $posthtml  = '<p><font face="sans-serif">' .
                     '<a href="' . $CFG->wwwroot . '/course/view.php?id=' . $course->id . '">' .
                     format_string($course->shortname, true, $formatparams) .
                     '</a> ->' .
                     '<a href="' . $CFG->wwwroot . '/mod/assign/index.php?id=' . $course->id . '">' .
                     $modulename .
                     '</a> ->' .
                     '<a href="' . $CFG->wwwroot . '/mod/assign/view.php?id=' . $coursemodule->id . '">' .
                     format_string($assignmentname, true, $formatparams) .
                     '</a></font></p>';
        $posthtml .= '<hr /><font face="sans-serif">';
        $posthtml .= '<p>' . get_string($messagetype . 'html', 'assign', $info) . '</p>';
        $posthtml .= '</font><hr />';
        return $posthtml;
    }

    /**
     * Message someone about something (static so it can be called from cron).
     *
     * @param stdClass $userfrom
     * @param stdClass $userto
     * @param string $messagetype
     * @param string $eventtype
     * @param int $updatetime
     * @param stdClass $coursemodule
     * @param stdClass $context
     * @param stdClass $course
     * @param string $modulename
     * @param string $assignmentname
     * @return void
     */
    public static function send_assignment_notification($userfrom,
                                                        $userto,
                                                        $messagetype,
                                                        $eventtype,
                                                        $updatetime,
                                                        $coursemodule,
                                                        $context,
                                                        $course,
                                                        $modulename,
                                                        $assignmentname,
                                                        $blindmarking,
                                                        $uniqueidforuser) {
        global $CFG;

        $info = new stdClass();
        if ($blindmarking) {
            $info->username = get_string('participant', 'assign') . ' ' . $uniqueidforuser;
            $userfrom->firstname = get_string('participant', 'assign');
            $userfrom->lastname = $uniqueidforuser;
            $userfrom->email = $CFG->noreplyaddress;
        } else {
            $info->username = fullname($userfrom, true);
        }
        $info->assignment = format_string($assignmentname, true, array('context'=>$context));
        $info->url = $CFG->wwwroot.'/mod/assign/view.php?id='.$coursemodule->id;
        $info->timeupdated = userdate($updatetime, get_string('strftimerecentfull'));

        $postsubject = get_string($messagetype . 'small', 'assign', $info);
        $posttext = self::format_notification_message_text($messagetype,
                                                           $info,
                                                           $course,
                                                           $context,
                                                           $modulename,
                                                           $assignmentname);
        $posthtml = '';
        if ($userto->mailformat == 1) {
            $posthtml = self::format_notification_message_html($messagetype,
                                                               $info,
                                                               $course,
                                                               $context,
                                                               $modulename,
                                                               $coursemodule,
                                                               $assignmentname);
        }

        $eventdata = new stdClass();
        $eventdata->modulename       = 'assign';
        $eventdata->userfrom         = $userfrom;
        $eventdata->userto           = $userto;
        $eventdata->subject          = $postsubject;
        $eventdata->fullmessage      = $posttext;
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml  = $posthtml;
        $eventdata->smallmessage     = $postsubject;

        $eventdata->name            = $eventtype;
        $eventdata->component       = 'mod_assign';
        $eventdata->notification    = 1;
        $eventdata->contexturl      = $info->url;
        $eventdata->contexturlname  = $info->assignment;

        message_send($eventdata);
    }

    /**
     * Message someone about something.
     *
     * @param stdClass $userfrom
     * @param stdClass $userto
     * @param string $messagetype
     * @param string $eventtype
     * @param int $updatetime
     * @return void
     */
    public function send_notification($userfrom,
                                      $userto,
                                      $messagetype,
                                      $eventtype,
                                      $updatetime) {
        self::send_assignment_notification($userfrom,
                                           $userto,
                                           $messagetype,
                                           $eventtype,
                                           $updatetime,
                                           $this->get_course_module(),
                                           $this->get_context(),
                                           $this->get_course(),
                                           $this->get_module_name(),
                                           $this->get_instance()->name,
                                           $this->is_blind_marking(),
                                           $this->get_uniqueid_for_user($userfrom->id));
    }

    /**
     * Notify student upon successful submission copy.
     *
     * @param stdClass $submission
     * @return void
     */
    protected function notify_student_submission_copied(stdClass $submission) {
        global $DB, $USER;

        $adminconfig = $this->get_admin_config();
        // Use the same setting for this - no need for another one.
        if (empty($adminconfig->submissionreceipts)) {
            // No need to do anything.
            return;
        }
        if ($submission->userid) {
            $user = $DB->get_record('user', array('id'=>$submission->userid), '*', MUST_EXIST);
        } else {
            $user = $USER;
        }
        $this->send_notification($user,
                                 $user,
                                 'submissioncopied',
                                 'assign_notification',
                                 $submission->timemodified);
    }
    /**
     * Notify student upon successful submission.
     *
     * @param stdClass $submission
     * @return void
     */
    protected function notify_student_submission_receipt(stdClass $submission) {
        global $DB, $USER;

        $adminconfig = $this->get_admin_config();
        if (empty($adminconfig->submissionreceipts)) {
            // No need to do anything.
            return;
        }
        if ($submission->userid) {
            $user = $DB->get_record('user', array('id'=>$submission->userid), '*', MUST_EXIST);
        } else {
            $user = $USER;
        }
        $this->send_notification($user,
                                 $user,
                                 'submissionreceipt',
                                 'assign_notification',
                                 $submission->timemodified);
    }

    /**
     * Send notifications to graders upon student submissions.
     *
     * @param stdClass $submission
     * @return void
     */
    protected function notify_graders(stdClass $submission) {
        global $DB, $USER;

        $instance = $this->get_instance();

        $late = $instance->duedate && ($instance->duedate < time());

        if (!$instance->sendnotifications && !($late && $instance->sendlatenotifications)) {
            // No need to do anything.
            return;
        }

        if ($submission->userid) {
            $user = $DB->get_record('user', array('id'=>$submission->userid), '*', MUST_EXIST);
        } else {
            $user = $USER;
        }
        if ($teachers = $this->get_graders($user->id)) {
            foreach ($teachers as $teacher) {
                $this->send_notification($user,
                                         $teacher,
                                         'gradersubmissionupdated',
                                         'assign_notification',
                                         $submission->timemodified);
            }
        }
    }

    /**
     * Assignment submission is processed before grading.
     *
     * @param $mform If validation failed when submitting this form - this is the moodleform.
     *               It can be null.
     * @return bool Return false if the validation fails. This affects which page is displayed next.
     */
    protected function process_submit_for_grading($mform) {
        global $USER, $CFG;

        // Need submit permission to submit an assignment.
        require_capability('mod/assign:submit', $this->context);
        require_once($CFG->dirroot . '/mod/assign/submissionconfirmform.php');
        require_sesskey();

        $instance = $this->get_instance();
        $data = new stdClass();
        $adminconfig = $this->get_admin_config();
        $requiresubmissionstatement = (!empty($adminconfig->requiresubmissionstatement) ||
                                       $instance->requiresubmissionstatement) &&
                                       !empty($adminconfig->submissionstatement);

        $submissionstatement = '';
        if (!empty($adminconfig->submissionstatement)) {
            $submissionstatement = $adminconfig->submissionstatement;
        }

        if ($mform == null) {
            $mform = new mod_assign_confirm_submission_form(null, array($requiresubmissionstatement,
                                                                    $submissionstatement,
                                                                    $this->get_course_module()->id,
                                                                    $data));
        }

        $data = $mform->get_data();
        if (!$mform->is_cancelled()) {
            if ($mform->get_data() == false) {
                return false;
            }
            if ($instance->teamsubmission) {
                $submission = $this->get_group_submission($USER->id, 0, true);
            } else {
                $submission = $this->get_user_submission($USER->id, true);
            }

            if ($submission->status != ASSIGN_SUBMISSION_STATUS_SUBMITTED) {
                // Give each submission plugin a chance to process the submission.
                $plugins = $this->get_submission_plugins();
                foreach ($plugins as $plugin) {
                    if ($plugin->is_enabled() && $plugin->is_visible()) {
                        $plugin->submit_for_grading($submission);
                    }
                }

                $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
                $this->update_submission($submission, $USER->id, true, $instance->teamsubmission);
                $completion = new completion_info($this->get_course());
                if ($completion->is_enabled($this->get_course_module()) && $instance->completionsubmit) {
                    $completion->update_state($this->get_course_module(), COMPLETION_COMPLETE, $USER->id);
                }

                if (isset($data->submissionstatement)) {
                    $logmessage = get_string('submissionstatementacceptedlog',
                                             'mod_assign',
                                             fullname($USER));
                    $this->add_to_log('submission statement accepted', $logmessage);
                }
                $this->add_to_log('submit for grading', $this->format_submission_for_log($submission));
                $this->notify_graders($submission);
                $this->notify_student_submission_receipt($submission);

                // Trigger assessable_submitted event on submission.
                $eventdata = new stdClass();
                $eventdata->modulename   = 'assign';
                $eventdata->cmid         = $this->get_course_module()->id;
                $eventdata->itemid       = $submission->id;
                $eventdata->courseid     = $this->get_course()->id;
                $eventdata->userid       = $USER->id;
                $eventdata->params       = array( 'submission_editable' => false);
                events_trigger('assessable_submitted', $eventdata);
            }
        }
        return true;
    }

    /**
     * Save the extension date for a single user.
     *
     * @param int $userid The user id
     * @param mixed $extensionduedate Either an integer date or null
     * @return boolean
     */
    protected function save_user_extension($userid, $extensionduedate) {
        global $DB;

        $flags = $this->get_user_flags($userid, true);
        $flags->extensionduedate = $extensionduedate;

        $result = $this->update_user_flags($flags);

        if ($result) {
            $this->add_to_log('grant extension', $userid);
        }
        return $result;
    }

    /**
     * Save extension date.
     *
     * @param moodleform $mform The submitted form
     * @return boolean
     */
    protected function process_save_extension(& $mform) {
        global $DB, $CFG;

        // Include extension form.
        require_once($CFG->dirroot . '/mod/assign/extensionform.php');

        // Need submit permission to submit an assignment.
        require_capability('mod/assign:grantextension', $this->context);

        $batchusers = optional_param('selectedusers', '', PARAM_SEQUENCE);
        $userid = 0;
        if (!$batchusers) {
            $userid = required_param('userid', PARAM_INT);
            $user = $DB->get_record('user', array('id'=>$userid), '*', MUST_EXIST);
        }
        $mform = new mod_assign_extension_form(null, array($this->get_course_module()->id,
                                                           $userid,
                                                           $batchusers,
                                                           $this->get_instance(),
                                                           null));

        if ($mform->is_cancelled()) {
            return true;
        }

        if ($formdata = $mform->get_data()) {
            if ($batchusers) {
                $users = explode(',', $batchusers);
                $result = true;
                foreach ($users as $userid) {
                    $result = $this->save_user_extension($userid, $formdata->extensionduedate) && $result;
                }
                return $result;
            } else {
                return $this->save_user_extension($userid, $formdata->extensionduedate);
            }
        }
        return false;
    }


    /**
     * Save quick grades.
     *
     * @return string The result of the save operation
     */
    protected function process_save_quick_grades() {
        global $USER, $DB, $CFG;

        // Need grade permission.
        require_capability('mod/assign:grade', $this->context);

        // Make sure advanced grading is disabled.
        $gradingmanager = get_grading_manager($this->get_context(), 'mod_assign', 'submissions');
        $controller = $gradingmanager->get_active_controller();
        if (!empty($controller)) {
            return get_string('errorquickgradingvsadvancedgrading', 'assign');
        }

        $users = array();
        // First check all the last modified values.
        $currentgroup = groups_get_activity_group($this->get_course_module(), true);
        $participants = $this->list_participants($currentgroup, true);

        // Gets a list of possible users and look for values based upon that.
        foreach ($participants as $userid => $unused) {
            $modified = optional_param('grademodified_' . $userid, -1, PARAM_INT);
            // Gather the userid, updated grade and last modified value.
            $record = new stdClass();
            $record->userid = $userid;
            if ($modified >= 0) {
                $record->grade = unformat_float(optional_param('quickgrade_' . $record->userid, -1, PARAM_TEXT));
            } else {
                // This user was not in the grading table.
                continue;
            }
            $record->lastmodified = $modified;
            $record->gradinginfo = grade_get_grades($this->get_course()->id,
                                                    'mod',
                                                    'assign',
                                                    $this->get_instance()->id,
                                                    array($userid));
            $users[$userid] = $record;
        }

        list($userids, $params) = $DB->get_in_or_equal(array_keys($users), SQL_PARAMS_NAMED);
        $params['assignid1'] = $this->get_instance()->id;
        $params['assignid2'] = $this->get_instance()->id;

        // Check them all for currency.
        $grademaxattempt = 'SELECT mxg.userid, MAX(mxg.attemptnumber) AS maxattempt
                            FROM {assign_grades} mxg
                            WHERE mxg.assignment = :assignid1 GROUP BY mxg.userid';

        $sql = 'SELECT u.id as userid, g.grade as grade, g.timemodified as lastmodified
                    FROM {user} u
                LEFT JOIN ( ' . $grademaxattempt . ' ) gmx ON u.id = gmx.userid
                LEFT JOIN {assign_grades} g ON
                    u.id = g.userid AND
                    g.assignment = :assignid2 AND
                    g.attemptnumber = gmx.maxattempt
                WHERE u.id ' . $userids;
        $currentgrades = $DB->get_recordset_sql($sql, $params);

        $modifiedusers = array();
        foreach ($currentgrades as $current) {
            $modified = $users[(int)$current->userid];
            $grade = $this->get_user_grade($modified->userid, false);

            // Check to see if the outcomes were modified.
            if ($CFG->enableoutcomes) {
                foreach ($modified->gradinginfo->outcomes as $outcomeid => $outcome) {
                    $oldoutcome = $outcome->grades[$modified->userid]->grade;
                    $paramname = 'outcome_' . $outcomeid . '_' . $modified->userid;
                    $newoutcome = optional_param($paramname, -1, PARAM_FLOAT);
                    if ($oldoutcome != $newoutcome) {
                        // Can't check modified time for outcomes because it is not reported.
                        $modifiedusers[$modified->userid] = $modified;
                        continue;
                    }
                }
            }

            // Let plugins participate.
            foreach ($this->feedbackplugins as $plugin) {
                if ($plugin->is_visible() && $plugin->is_enabled() && $plugin->supports_quickgrading()) {
                    if ($plugin->is_quickgrading_modified($modified->userid, $grade)) {
                        if ((int)$current->lastmodified > (int)$modified->lastmodified) {
                            return get_string('errorrecordmodified', 'assign');
                        } else {
                            $modifiedusers[$modified->userid] = $modified;
                            continue;
                        }
                    }
                }
            }

            if (($current->grade < 0 || $current->grade === null) &&
                ($modified->grade < 0 || $modified->grade === null)) {
                // Different ways to indicate no grade.
                continue;
            }
            // Treat 0 and null as different values.
            if ($current->grade !== null) {
                $current->grade = floatval($current->grade);
            }
            if ($current->grade !== $modified->grade) {
                // Grade changed.
                if ($this->grading_disabled($modified->userid)) {
                    continue;
                }
                if ((int)$current->lastmodified > (int)$modified->lastmodified) {
                    // Error - record has been modified since viewing the page.
                    return get_string('errorrecordmodified', 'assign');
                } else {
                    $modifiedusers[$modified->userid] = $modified;
                }
            }

        }
        $currentgrades->close();

        $adminconfig = $this->get_admin_config();
        $gradebookplugin = $adminconfig->feedback_plugin_for_gradebook;

        // Ok - ready to process the updates.
        foreach ($modifiedusers as $userid => $modified) {
            $grade = $this->get_user_grade($userid, true);
            $grade->grade= grade_floatval(unformat_float($modified->grade));
            $grade->grader= $USER->id;

            // Save plugins data.
            foreach ($this->feedbackplugins as $plugin) {
                if ($plugin->is_visible() && $plugin->is_enabled() && $plugin->supports_quickgrading()) {
                    $plugin->save_quickgrading_changes($userid, $grade);
                    if (('assignfeedback_' . $plugin->get_type()) == $gradebookplugin) {
                        // This is the feedback plugin chose to push comments to the gradebook.
                        $grade->feedbacktext = $plugin->text_for_gradebook($grade);
                        $grade->feedbackformat = $plugin->format_for_gradebook($grade);
                    }
                }
            }

            $this->update_grade($grade);
            $this->notify_grade_modified($grade);

            // Save outcomes.
            if ($CFG->enableoutcomes) {
                $data = array();
                foreach ($modified->gradinginfo->outcomes as $outcomeid => $outcome) {
                    $oldoutcome = $outcome->grades[$modified->userid]->grade;
                    $paramname = 'outcome_' . $outcomeid . '_' . $modified->userid;
                    $newoutcome = optional_param($paramname, -1, PARAM_INT);
                    if ($oldoutcome != $newoutcome) {
                        $data[$outcomeid] = $newoutcome;
                    }
                }
                if (count($data) > 0) {
                    grade_update_outcomes('mod/assign',
                                          $this->course->id,
                                          'mod',
                                          'assign',
                                          $this->get_instance()->id,
                                          $userid,
                                          $data);
                }
            }

            $this->add_to_log('grade submission', $this->format_grade_for_log($grade));
        }

        return get_string('quickgradingchangessaved', 'assign');
    }

    /**
     * Reveal student identities to markers (and the gradebook).
     *
     * @return void
     */
    protected function process_reveal_identities() {
        global $DB, $CFG;

        require_capability('mod/assign:revealidentities', $this->context);
        if (!confirm_sesskey()) {
            return false;
        }

        // Update the assignment record.
        $update = new stdClass();
        $update->id = $this->get_instance()->id;
        $update->revealidentities = 1;
        $DB->update_record('assign', $update);

        // Refresh the instance data.
        $this->instance = null;

        // Release the grades to the gradebook.
        // First create the column in the gradebook.
        $this->update_gradebook(false, $this->get_course_module()->id);

        // Now release all grades.

        $adminconfig = $this->get_admin_config();
        $gradebookplugin = $adminconfig->feedback_plugin_for_gradebook;
        $grades = $DB->get_records('assign_grades', array('assignment'=>$this->get_instance()->id));

        $plugin = $this->get_feedback_plugin_by_type($gradebookplugin);

        foreach ($grades as $grade) {
            // Fetch any comments for this student.
            if ($plugin && $plugin->is_enabled() && $plugin->is_visible()) {
                $grade->feedbacktext = $plugin->text_for_gradebook($grade);
                $grade->feedbackformat = $plugin->format_for_gradebook($grade);
            }
            $this->gradebook_item_update(null, $grade);
        }

        $this->add_to_log('reveal identities', get_string('revealidentities', 'assign'));
    }


    /**
     * Save grading options.
     *
     * @return void
     */
    protected function process_save_grading_options() {
        global $USER, $CFG;

        // Include grading options form.
        require_once($CFG->dirroot . '/mod/assign/gradingoptionsform.php');

        // Need submit permission to submit an assignment.
        require_capability('mod/assign:grade', $this->context);

        // Is advanced grading enabled?
        $gradingmanager = get_grading_manager($this->get_context(), 'mod_assign', 'submissions');
        $controller = $gradingmanager->get_active_controller();
        $showquickgrading = empty($controller);

        $gradingoptionsparams = array('cm'=>$this->get_course_module()->id,
                                      'contextid'=>$this->context->id,
                                      'userid'=>$USER->id,
                                      'submissionsenabled'=>$this->is_any_submission_plugin_enabled(),
                                      'showquickgrading'=>$showquickgrading,
                                      'quickgrading'=>false);

        $mform = new mod_assign_grading_options_form(null, $gradingoptionsparams);
        if ($formdata = $mform->get_data()) {
            set_user_preference('assign_perpage', $formdata->perpage);
            if (isset($formdata->filter)) {
                set_user_preference('assign_filter', $formdata->filter);
            }
            if ($showquickgrading) {
                set_user_preference('assign_quickgrading', isset($formdata->quickgrading));
            }
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
    public function format_grade_for_log(stdClass $grade) {
        global $DB;

        $user = $DB->get_record('user', array('id' => $grade->userid), '*', MUST_EXIST);

        $info = get_string('gradestudent', 'assign', array('id'=>$user->id, 'fullname'=>fullname($user)));
        if ($grade->grade != '') {
            $info .= get_string('grade') . ': ' . $this->display_grade($grade->grade, false) . '. ';
        } else {
            $info .= get_string('nograde', 'assign');
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
    protected function format_submission_for_log(stdClass $submission) {
        $info = '';
        $info .= get_string('submissionstatus', 'assign') .
                 ': ' .
                 get_string('submissionstatus_' . $submission->status, 'assign') .
                 '. <br>';

        foreach ($this->submissionplugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {
                $info .= '<br>' . $plugin->format_for_log($submission);
            }
        }

        return $info;
    }

    /**
     * Copy the current assignment submission from the last submitted attempt.
     *
     * @param  array $notices Any error messages that should be shown
     *                        to the user at the top of the edit submission form.
     * @return bool
     */
    protected function process_copy_previous_attempt(&$notices) {
        global $USER, $CFG;

        $instance = $this->get_instance();
        if ($instance->teamsubmission) {
            $submission = $this->get_group_submission($USER->id, 0, true);
        } else {
            $submission = $this->get_user_submission($USER->id, true);
        }
        if (!$submission || $submission->status != ASSIGN_SUBMISSION_STATUS_REOPENED) {
            $notices[] = get_string('submissionnotcopiedinvalidstatus', 'assign');
            return false;
        }
        $flags = $this->get_user_flags($USER->id, false);

        // Get the flags to check if it is locked.
        if ($flags && $flags->locked) {
            $notices[] = get_string('submissionslocked', 'assign');
            return false;
        }
        if ($instance->submissiondrafts) {
            $submission->status = ASSIGN_SUBMISSION_STATUS_DRAFT;
        } else {
            $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        }
        $this->update_submission($submission, $USER->id, true, $instance->teamsubmission);

        // Find the previous submission.
        if ($instance->teamsubmission) {
            $previoussubmission = $this->get_group_submission($USER->id, 0, true, $submission->attemptnumber - 1);
        } else {
            $previoussubmission = $this->get_user_submission($USER->id, true, $submission->attemptnumber - 1);
        }

        if (!$previoussubmission) {
            // There was no previous submission so there is nothing else to do.
            return true;
        }

        $pluginerror = false;
        foreach ($this->get_submission_plugins() as $plugin) {
            if ($plugin->is_visible() && $plugin->is_enabled()) {
                if (!$plugin->copy_submission($previoussubmission, $submission)) {
                    $notices[] = $plugin->get_error();
                    $pluginerror = true;
                }
            }
        }
        if ($pluginerror) {
            return false;
        }

        $this->add_to_log('submissioncopied', $this->format_submission_for_log($submission));

        $complete = COMPLETION_INCOMPLETE;
        if ($submission->status == ASSIGN_SUBMISSION_STATUS_SUBMITTED) {
            $complete = COMPLETION_COMPLETE;
        }
        $completion = new completion_info($this->get_course());
        if ($completion->is_enabled($this->get_course_module()) && $instance->completionsubmit) {
            $completion->update_state($this->get_course_module(), $complete, $USER->id);
        }

        if (!$instance->submissiondrafts) {
            // There is a case for not notifying the student about the submission copy,
            // but it provides a record of the event and if they then cancel editing it
            // is clear that the submission was copied.
            $this->notify_student_submission_copied($submission);
            $this->notify_graders($submission);
            // Trigger assessable_submitted event on submission.
            // The same logic applies here - we could not notify teachers,
            // but then they would wonder why there are submitted assignments
            // and they haven't been notified.
            $eventdata = new stdClass();
            $eventdata->modulename   = 'assign';
            $eventdata->cmid         = $this->get_course_module()->id;
            $eventdata->itemid       = $submission->id;
            $eventdata->courseid     = $this->get_course()->id;
            $eventdata->userid       = $USER->id;
            $eventdata->params       = array(
                'submission_editable' => true,
            );
            events_trigger('assessable_submitted', $eventdata);
        }
        return true;
    }

    /**
     * Save assignment submission.
     *
     * @param  moodleform $mform
     * @param  array $notices Any error messages that should be shown
     *                        to the user at the top of the edit submission form.
     * @return bool
     */
    protected function process_save_submission(&$mform, &$notices) {
        global $USER, $CFG;

        // Include submission form.
        require_once($CFG->dirroot . '/mod/assign/submission_form.php');

        // Need submit permission to submit an assignment.
        require_capability('mod/assign:submit', $this->context);
        require_sesskey();
        $instance = $this->get_instance();

        $data = new stdClass();
        $mform = new mod_assign_submission_form(null, array($this, $data));
        if ($mform->is_cancelled()) {
            return true;
        }
        if ($data = $mform->get_data()) {
            if ($instance->teamsubmission) {
                $submission = $this->get_group_submission($USER->id, 0, true);
            } else {
                $submission = $this->get_user_submission($USER->id, true);
            }
            if ($instance->submissiondrafts) {
                $submission->status = ASSIGN_SUBMISSION_STATUS_DRAFT;
            } else {
                $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
            }

            $flags = $this->get_user_flags($USER->id, false);

            // Get the flags to check if it is locked.
            if ($flags && $flags->locked) {
                print_error('submissionslocked', 'assign');
                return true;
            }

            $allempty = true;
            $pluginerror = false;
            foreach ($this->submissionplugins as $plugin) {
                if ($plugin->is_enabled() && $plugin->is_visible()) {
                    if (!$plugin->save($submission, $data)) {
                        $notices[] = $plugin->get_error();
                        $pluginerror = true;
                    }
                    if (!$allempty || !$plugin->is_empty($submission)) {
                        $allempty = false;
                    }
                }
            }
            if ($pluginerror || $allempty) {
                if ($allempty) {
                    $notices[] = get_string('submissionempty', 'mod_assign');
                }
                return false;
            }

            $this->update_submission($submission, $USER->id, true, $instance->teamsubmission);

            // Logging.
            if (isset($data->submissionstatement)) {
                $logmessage = get_string('submissionstatementacceptedlog',
                                         'mod_assign',
                                         fullname($USER));
                $this->add_to_log('submission statement accepted', $logmessage);
            }
            $this->add_to_log('submit', $this->format_submission_for_log($submission));

            $complete = COMPLETION_INCOMPLETE;
            if ($submission->status == ASSIGN_SUBMISSION_STATUS_SUBMITTED) {
                $complete = COMPLETION_COMPLETE;
            }
            $completion = new completion_info($this->get_course());
            if ($completion->is_enabled($this->get_course_module()) && $instance->completionsubmit) {
                $completion->update_state($this->get_course_module(), $complete, $USER->id);
            }

            if (!$instance->submissiondrafts) {
                $this->notify_student_submission_receipt($submission);
                $this->notify_graders($submission);
                // Trigger assessable_submitted event on submission.
                $eventdata = new stdClass();
                $eventdata->modulename   = 'assign';
                $eventdata->cmid         = $this->get_course_module()->id;
                $eventdata->itemid       = $submission->id;
                $eventdata->courseid     = $this->get_course()->id;
                $eventdata->userid       = $USER->id;
                $eventdata->params       = array(
                    'submission_editable' => true,
                );
                events_trigger('assessable_submitted', $eventdata);
            }
            return true;
        }
        return false;
    }


    /**
     * Determine if this users grade is locked or overridden.
     *
     * @param int $userid - The student userid
     * @return bool $gradingdisabled
     */
    public function grading_disabled($userid) {
        global $CFG;

        $gradinginfo = grade_get_grades($this->get_course()->id,
                                        'mod',
                                        'assign',
                                        $this->get_instance()->id,
                                        array($userid));
        if (!$gradinginfo) {
            return false;
        }

        if (!isset($gradinginfo->items[0]->grades[$userid])) {
            return false;
        }
        $gradingdisabled = $gradinginfo->items[0]->grades[$userid]->locked ||
                           $gradinginfo->items[0]->grades[$userid]->overridden;
        return $gradingdisabled;
    }


    /**
     * Get an instance of a grading form if advanced grading is enabled.
     * This is specific to the assignment, marker and student.
     *
     * @param int $userid - The student userid
     * @param bool $gradingdisabled
     * @return mixed gradingform_instance|null $gradinginstance
     */
    protected function get_grading_instance($userid, $grade, $gradingdisabled) {
        global $CFG, $USER;

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
                    $gradinginstance = $controller->get_current_instance($USER->id, $itemid);
                } else if (!$gradingdisabled) {
                    $instanceid = optional_param('advancedgradinginstanceid', 0, PARAM_INT);
                    $gradinginstance = $controller->get_or_create_instance($instanceid,
                                                                           $USER->id,
                                                                           $itemid);
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
     * Add elements to grade form.
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
        $useridlistid = $params['useridlistid'];
        $userid = $params['userid'];
        $attemptnumber = $params['attemptnumber'];
        if (!$userid) {
            $cache = cache::make_from_params(cache_store::MODE_SESSION, 'mod_assign', 'useridlist');
            if (!$useridlist = $cache->get($this->get_course_module()->id . '_' . $useridlistid)) {
                $useridlist = $this->get_grading_userid_list();
                $cache->set($this->get_course_module()->id . '_' . $useridlistid, $useridlist);
            }
        } else {
            $useridlist = array($userid);
            $rownum = 0;
            $useridlistid = '';
        }

        $userid = $useridlist[$rownum];
        $grade = $this->get_user_grade($userid, false, $attemptnumber);

        $submission = null;
        if ($this->get_instance()->teamsubmission) {
            $submission = $this->get_group_submission($userid, 0, false, $attemptnumber);
        } else {
            $submission = $this->get_user_submission($userid, false, $attemptnumber);
        }

        // Add advanced grading.
        $gradingdisabled = $this->grading_disabled($userid);
        $gradinginstance = $this->get_grading_instance($userid, $grade, $gradingdisabled);

        $mform->addElement('header', 'gradeheader', get_string('grade'));
        if ($gradinginstance) {
            $gradingelement = $mform->addElement('grading',
                                                 'advancedgrading',
                                                 get_string('grade').':',
                                                 array('gradinginstance' => $gradinginstance));
            if ($gradingdisabled) {
                $gradingelement->freeze();
            } else {
                $mform->addElement('hidden', 'advancedgradinginstanceid', $gradinginstance->get_id());
                $mform->setType('advancedgradinginstanceid', PARAM_INT);
            }
        } else {
            // Use simple direct grading.
            if ($this->get_instance()->grade > 0) {
                $name = get_string('gradeoutof', 'assign', $this->get_instance()->grade);
                $gradingelement = $mform->addElement('text', 'grade', $name);
                $mform->addHelpButton('grade', 'gradeoutofhelp', 'assign');
                $mform->setType('grade', PARAM_TEXT);
                if ($gradingdisabled) {
                    $gradingelement->freeze();
                }
            } else {
                $grademenu = make_grades_menu($this->get_instance()->grade);
                if (count($grademenu) > 0) {
                    $gradingelement = $mform->addElement('select', 'grade', get_string('grade') . ':', $grademenu);

                    // The grade is already formatted with format_float so it needs to be converted back to an integer.
                    if (!empty($data->grade)) {
                        $data->grade = (int)unformat_float($data->grade);
                    }
                    $mform->setType('grade', PARAM_INT);
                    if ($gradingdisabled) {
                        $gradingelement->freeze();
                    }
                }
            }
        }

        $gradinginfo = grade_get_grades($this->get_course()->id,
                                        'mod',
                                        'assign',
                                        $this->get_instance()->id,
                                        $userid);
        if (!empty($CFG->enableoutcomes)) {
            foreach ($gradinginfo->outcomes as $index => $outcome) {
                $options = make_grades_menu(-$outcome->scaleid);
                if ($outcome->grades[$userid]->locked) {
                    $options[0] = get_string('nooutcome', 'grades');
                    $mform->addElement('static',
                                       'outcome_' . $index . '[' . $userid . ']',
                                       $outcome->name . ':',
                                       $options[$outcome->grades[$userid]->grade]);
                } else {
                    $options[''] = get_string('nooutcome', 'grades');
                    $attributes = array('id' => 'menuoutcome_' . $index );
                    $mform->addElement('select',
                                       'outcome_' . $index . '[' . $userid . ']',
                                       $outcome->name.':',
                                       $options,
                                       $attributes);
                    $mform->setType('outcome_' . $index . '[' . $userid . ']', PARAM_INT);
                    $mform->setDefault('outcome_' . $index . '[' . $userid . ']',
                                       $outcome->grades[$userid]->grade);
                }
            }
        }

        $capabilitylist = array('gradereport/grader:view', 'moodle/grade:viewall');
        if (has_all_capabilities($capabilitylist, $this->get_course_context())) {
            $urlparams = array('id'=>$this->get_course()->id);
            $url = new moodle_url('/grade/report/grader/index.php', $urlparams);
            $usergrade = '-';
            if (isset($gradinginfo->items[0]->grades[$userid]->str_grade)) {
                $usergrade = $gradinginfo->items[0]->grades[$userid]->str_grade;
            }
            $gradestring = $this->get_renderer()->action_link($url, $usergrade);
        } else {
            $usergrade = '-';
            if (isset($gradinginfo->items[0]->grades[$userid]) &&
                    !$gradinginfo->items[0]->grades[$userid]->hidden) {
                $usergrade = $gradinginfo->items[0]->grades[$userid]->str_grade;
            }
            $gradestring = $usergrade;
        }
        $mform->addElement('static', 'currentgrade', get_string('currentgrade', 'assign'), $gradestring);

        if (count($useridlist) > 1) {
            $strparams = array('current'=>$rownum+1, 'total'=>count($useridlist));
            $name = get_string('outof', 'assign', $strparams);
            $mform->addElement('static', 'gradingstudent', get_string('gradingstudent', 'assign'), $name);
        }

        // Let feedback plugins add elements to the grading form.
        $this->add_plugin_grade_elements($grade, $mform, $data, $userid);

        // Hidden params.
        $mform->addElement('hidden', 'id', $this->get_course_module()->id);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'rownum', $rownum);
        $mform->setType('rownum', PARAM_INT);
        $mform->setConstant('rownum', $rownum);
        $mform->addElement('hidden', 'useridlistid', $useridlistid);
        $mform->setType('useridlistid', PARAM_INT);
        $mform->addElement('hidden', 'attemptnumber', $attemptnumber);
        $mform->setType('attemptnumber', PARAM_INT);
        $mform->addElement('hidden', 'ajax', optional_param('ajax', 0, PARAM_INT));
        $mform->setType('ajax', PARAM_INT);

        if ($this->get_instance()->teamsubmission) {
            $mform->addElement('header', 'groupsubmissionsettings', get_string('groupsubmissionsettings', 'assign'));
            $mform->addElement('selectyesno', 'applytoall', get_string('applytoteam', 'assign'));
            $mform->setDefault('applytoall', 1);
        }

        // Do not show if we are editing a previous attempt.
        if ($attemptnumber == -1 && $this->get_instance()->attemptreopenmethod != ASSIGN_ATTEMPT_REOPEN_METHOD_NONE) {
            $mform->addElement('header', 'attemptsettings', get_string('attemptsettings', 'assign'));
            $attemptreopenmethod = get_string('attemptreopenmethod_' . $this->get_instance()->attemptreopenmethod, 'assign');
            $mform->addElement('static', 'attemptreopenmethod', get_string('attemptreopenmethod', 'assign'), $attemptreopenmethod);

            $attemptnumber = 0;
            if ($submission) {
                $attemptnumber = $submission->attemptnumber;
            }
            $maxattempts = $this->get_instance()->maxattempts;
            if ($maxattempts == ASSIGN_UNLIMITED_ATTEMPTS) {
                $maxattempts = get_string('unlimitedattempts', 'assign');
            }
            $mform->addelement('static', 'maxattemptslabel', get_string('maxattempts', 'assign'), $maxattempts);
            $mform->addelement('static', 'attemptnumberlabel', get_string('attemptnumber', 'assign'), $attemptnumber + 1);

            $ismanual = $this->get_instance()->attemptreopenmethod == ASSIGN_ATTEMPT_REOPEN_METHOD_MANUAL;
            $issubmission = !empty($submission);
            $isunlimited = $this->get_instance()->maxattempts == ASSIGN_UNLIMITED_ATTEMPTS;
            $islessthanmaxattempts = $issubmission && ($submission->attemptnumber < ($this->get_instance()->maxattempts-1));

            if ($ismanual && (!$issubmission || $isunlimited || $islessthanmaxattempts)) {
                $mform->addElement('selectyesno', 'addattempt', get_string('addattempt', 'assign'));
                $mform->setDefault('addattempt', 0);
            }
        }

        $mform->addElement('hidden', 'action', 'submitgrade');
        $mform->setType('action', PARAM_ALPHA);

        $buttonarray=array();
        $name = get_string('savechanges', 'assign');
        $buttonarray[] = $mform->createElement('submit', 'savegrade', $name);
        if (!$last) {
            $name = get_string('savenext', 'assign');
            $buttonarray[] = $mform->createElement('submit', 'saveandshownext', $name);
        }
        $buttonarray[] = $mform->createElement('cancel', 'cancelbutton', get_string('cancel'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
        $buttonarray=array();

        if ($rownum > 0) {
            $name = get_string('previous', 'assign');
            $buttonarray[] = $mform->createElement('submit', 'nosaveandprevious', $name);
        }

        if (!$last) {
            $name = get_string('nosavebutnext', 'assign');
            $buttonarray[] = $mform->createElement('submit', 'nosaveandnext', $name);
        }
        if (!empty($buttonarray)) {
            $mform->addGroup($buttonarray, 'navar', '', array(' '), false);
        }
        // The grading form does not work well with shortforms.
        $mform->setDisableShortforms();
    }

    /**
     * Add elements in submission plugin form.
     *
     * @param mixed $submission stdClass|null
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @param int $userid The current userid (same as $USER->id)
     * @return void
     */
    protected function add_plugin_submission_elements($submission,
                                                    MoodleQuickForm $mform,
                                                    stdClass $data,
                                                    $userid) {
        foreach ($this->submissionplugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible() && $plugin->allow_submissions()) {
                $mform->addElement('header', 'header_' . $plugin->get_type(), $plugin->get_name());
                if (!$plugin->get_form_elements_for_user($submission, $mform, $data, $userid)) {
                    $mform->removeElement('header_' . $plugin->get_type());
                }
            }
        }
    }

    /**
     * Check if feedback plugins installed are enabled.
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
     * Check if submission plugins installed are enabled.
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
     * Add elements to submission form.
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @return void
     */
    public function add_submission_form_elements(MoodleQuickForm $mform, stdClass $data) {
        global $USER;

        // Team submissions.
        if ($this->get_instance()->teamsubmission) {
            $submission = $this->get_group_submission($USER->id, 0, false);
        } else {
            $submission = $this->get_user_submission($USER->id, false);
        }

        // Submission statement.
        $adminconfig = $this->get_admin_config();

        $requiresubmissionstatement = (!empty($adminconfig->requiresubmissionstatement) ||
                                       $this->get_instance()->requiresubmissionstatement) &&
                                       !empty($adminconfig->submissionstatement);

        $draftsenabled = $this->get_instance()->submissiondrafts;

        if ($requiresubmissionstatement && !$draftsenabled) {

            $submissionstatement = '';
            if (!empty($adminconfig->submissionstatement)) {
                $submissionstatement = $adminconfig->submissionstatement;
            }
            $mform->addElement('checkbox', 'submissionstatement', '', '&nbsp;' . $submissionstatement);
            $mform->addRule('submissionstatement', get_string('required'), 'required', null, 'client');
        }

        $this->add_plugin_submission_elements($submission, $mform, $data, $USER->id);

        // Hidden params.
        $mform->addElement('hidden', 'id', $this->get_course_module()->id);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'action', 'savesubmission');
        $mform->setType('action', PARAM_TEXT);
    }

    /**
     * Revert to draft.
     * Uses url parameter userid
     *
     * @param int $userid
     * @return void
     */
    protected function process_revert_to_draft($userid = 0) {
        global $DB, $USER;

        // Need grade permission.
        require_capability('mod/assign:grade', $this->context);
        require_sesskey();

        if (!$userid) {
            $userid = required_param('userid', PARAM_INT);
        }

        if ($this->get_instance()->teamsubmission) {
            $submission = $this->get_group_submission($userid, 0, false);
        } else {
            $submission = $this->get_user_submission($userid, false);
        }

        if (!$submission) {
            return;
        }
        $submission->status = ASSIGN_SUBMISSION_STATUS_DRAFT;
        $this->update_submission($submission, $userid, true, $this->get_instance()->teamsubmission);

        // Give each submission plugin a chance to process the reverting to draft.
        $plugins = $this->get_submission_plugins();
        foreach ($plugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {
                $plugin->revert_to_draft($submission);
            }
        }
        // Update the modified time on the grade (grader modified).
        $grade = $this->get_user_grade($userid, true);
        $grade->grader = $USER->id;
        $this->update_grade($grade);

        $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);

        $completion = new completion_info($this->get_course());
        if ($completion->is_enabled($this->get_course_module()) &&
                $this->get_instance()->completionsubmit) {
            $completion->update_state($this->get_course_module(), COMPLETION_INCOMPLETE, $userid);
        }
        $logmessage = get_string('reverttodraftforstudent',
                                 'assign',
                                 array('id'=>$user->id, 'fullname'=>fullname($user)));
        $this->add_to_log('revert submission to draft', $logmessage);
    }

    /**
     * Lock the process.
     * Uses url parameter userid
     *
     * @param int $userid
     * @return void
     */
    protected function process_lock($userid = 0) {
        global $USER, $DB;

        // Need grade permission.
        require_capability('mod/assign:grade', $this->context);
        require_sesskey();

        if (!$userid) {
            $userid = required_param('userid', PARAM_INT);
        }

        // Give each submission plugin a chance to process the locking.
        $plugins = $this->get_submission_plugins();
        $submission = $this->get_user_submission($userid, false);
        foreach ($plugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {
                $plugin->lock($submission);
            }
        }

        $flags = $this->get_user_flags($userid, true);
        $flags->locked = 1;
        $this->update_user_flags($flags);

        $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);

        $logmessage = get_string('locksubmissionforstudent',
                                 'assign',
                                 array('id'=>$user->id, 'fullname'=>fullname($user)));
        $this->add_to_log('lock submission', $logmessage);
    }

    /**
     * Unlock the process.
     *
     * @param int $userid
     * @return void
     */
    protected function process_unlock($userid = 0) {
        global $USER, $DB;

        // Need grade permission.
        require_capability('mod/assign:grade', $this->context);
        require_sesskey();

        if (!$userid) {
            $userid = required_param('userid', PARAM_INT);
        }
        // Give each submission plugin a chance to process the unlocking.
        $plugins = $this->get_submission_plugins();
        $submission = $this->get_user_submission($userid, false);
        foreach ($plugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {
                $plugin->unlock($submission);
            }
        }

        $flags = $this->get_user_flags($userid, true);
        $flags->locked = 0;
        $this->update_user_flags($flags);

        $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);

        $logmessage = get_string('unlocksubmissionforstudent',
                                 'assign',
                                 array('id'=>$user->id, 'fullname'=>fullname($user)));
        $this->add_to_log('unlock submission', $logmessage);
    }

    /**
     * Apply a grade from a grading form to a user (may be called multiple times for a group submission).
     *
     * @param stdClass $formdata - the data from the form
     * @param int $userid - the user to apply the grade to
     * @param int attemptnumber - The attempt number to apply the grade to.
     * @return void
     */
    protected function apply_grade_to_user($formdata, $userid, $attemptnumber) {
        global $USER, $CFG, $DB;

        $grade = $this->get_user_grade($userid, true, $attemptnumber);
        $gradingdisabled = $this->grading_disabled($userid);
        $gradinginstance = $this->get_grading_instance($userid, $grade, $gradingdisabled);
        if (!$gradingdisabled) {
            if ($gradinginstance) {
                $grade->grade = $gradinginstance->submit_and_get_grade($formdata->advancedgrading,
                                                                       $grade->id);
            } else {
                // Handle the case when grade is set to No Grade.
                if (isset($formdata->grade)) {
                    $grade->grade = grade_floatval(unformat_float($formdata->grade));
                }
            }
        }
        $grade->grader= $USER->id;

        $adminconfig = $this->get_admin_config();
        $gradebookplugin = $adminconfig->feedback_plugin_for_gradebook;

        // Call save in plugins.
        foreach ($this->feedbackplugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {
                if (!$plugin->save($grade, $formdata)) {
                    $result = false;
                    print_error($plugin->get_error());
                }
                if (('assignfeedback_' . $plugin->get_type()) == $gradebookplugin) {
                    // This is the feedback plugin chose to push comments to the gradebook.
                    $grade->feedbacktext = $plugin->text_for_gradebook($grade);
                    $grade->feedbackformat = $plugin->format_for_gradebook($grade);
                }
            }
        }
        $this->update_grade($grade);
        $this->notify_grade_modified($grade);
        $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);

        $this->add_to_log('grade submission', $this->format_grade_for_log($grade));
    }


    /**
     * Save outcomes submitted from grading form
     *
     * @param int $userid
     * @param stdClass $formdata
     */
    protected function process_outcomes($userid, $formdata) {
        global $CFG, $USER;

        if (empty($CFG->enableoutcomes)) {
            return;
        }
        if ($this->grading_disabled($userid)) {
            return;
        }

        require_once($CFG->libdir.'/gradelib.php');

        $data = array();
        $gradinginfo = grade_get_grades($this->get_course()->id,
                                        'mod',
                                        'assign',
                                        $this->get_instance()->id,
                                        $userid);

        if (!empty($gradinginfo->outcomes)) {
            foreach ($gradinginfo->outcomes as $index => $oldoutcome) {
                $name = 'outcome_'.$index;
                if (isset($formdata->{$name}[$userid]) &&
                        $oldoutcome->grades[$userid]->grade != $formdata->{$name}[$userid]) {
                    $data[$index] = $formdata->{$name}[$userid];
                }
            }
        }
        if (count($data) > 0) {
            grade_update_outcomes('mod/assign',
                                  $this->course->id,
                                  'mod',
                                  'assign',
                                  $this->get_instance()->id,
                                  $userid,
                                  $data);
        }
    }


    /**
     * Save grade.
     *
     * @param  moodleform $mform
     * @return bool - was the grade saved
     */
    protected function process_save_grade(&$mform) {
        global $CFG;
        // Include grade form.
        require_once($CFG->dirroot . '/mod/assign/gradeform.php');

        // Need submit permission to submit an assignment.
        require_capability('mod/assign:grade', $this->context);
        require_sesskey();

        $instance = $this->get_instance();
        $rownum = required_param('rownum', PARAM_INT);
        $attemptnumber = optional_param('attemptnumber', -1, PARAM_INT);
        $useridlistid = optional_param('useridlistid', time(), PARAM_INT);
        $userid = optional_param('userid', 0, PARAM_INT);
        $cache = cache::make_from_params(cache_store::MODE_SESSION, 'mod_assign', 'useridlist');
        if (!$userid) {
            if (!$useridlist = $cache->get($this->get_course_module()->id . '_' . $useridlistid)) {
                $useridlist = $this->get_grading_userid_list();
                $cache->set($this->get_course_module()->id . '_' . $useridlistid, $useridlist);
            }
        } else {
            $useridlist = array($userid);
            $rownum = 0;
        }

        $last = false;
        $userid = $useridlist[$rownum];
        if ($rownum == count($useridlist) - 1) {
            $last = true;
        }

        $data = new stdClass();

        $gradeformparams = array('rownum'=>$rownum,
                                 'useridlistid'=>$useridlistid,
                                 'last'=>false,
                                 'attemptnumber'=>$attemptnumber,
                                 'userid'=>optional_param('userid', 0, PARAM_INT));
        $mform = new mod_assign_grade_form(null,
                                           array($this, $data, $gradeformparams),
                                           'post',
                                           '',
                                           array('class'=>'gradeform'));

        if ($formdata = $mform->get_data()) {
            $submission = null;
            if ($instance->teamsubmission) {
                $submission = $this->get_group_submission($userid, 0, false, $attemptnumber);
            } else {
                $submission = $this->get_user_submission($userid, false, $attemptnumber);
            }
            if ($instance->teamsubmission && $formdata->applytoall) {
                $groupid = 0;
                if ($this->get_submission_group($userid)) {
                    $group = $this->get_submission_group($userid);
                    if ($group) {
                        $groupid = $group->id;
                    }
                }
                $members = $this->get_submission_group_members($groupid, true);
                foreach ($members as $member) {
                    // User may exist in multple groups (which should put them in the default group).
                    $this->apply_grade_to_user($formdata, $member->id, $attemptnumber);
                    $this->process_outcomes($member->id, $formdata);
                }
            } else {
                $this->apply_grade_to_user($formdata, $userid, $attemptnumber);

                $this->process_outcomes($userid, $formdata);
            }
            $maxattemptsreached = !empty($submission) &&
                                  $submission->attemptnumber >= ($instance->maxattempts - 1) &&
                                  $instance->maxattempts != ASSIGN_UNLIMITED_ATTEMPTS;
            $shouldreopen = false;
            if ($instance->attemptreopenmethod == ASSIGN_ATTEMPT_REOPEN_METHOD_UNTILPASS) {
                // Check the gradetopass from the gradebook.
                $gradinginfo = grade_get_grades($this->get_course()->id,
                                                'mod',
                                                'assign',
                                                $instance->id,
                                                $userid);

                // What do we do if the grade has not been added to the gradebook (e.g. blind marking)?
                $gradingitem = null;
                $gradebookgrade = null;
                if (isset($gradinginfo->items[0])) {
                    $gradingitem = $gradinginfo->items[0];
                    $gradebookgrade = $gradingitem->grades[$userid];
                }

                if ($gradebookgrade) {
                    // TODO: This code should call grade_grade->is_passed().
                    $shouldreopen = true;
                    if (is_null($gradebookgrade->grade)) {
                        $shouldreopen = false;
                    }
                    if (empty($gradingitem->gradepass) || $gradingitem->gradepass == $gradingitem->grademin) {
                        $shouldreopen = false;
                    }
                    if ($gradebookgrade->grade >= $gradingitem->gradepass) {
                        $shouldreopen = false;
                    }
                }
            }
            if ($instance->attemptreopenmethod == ASSIGN_ATTEMPT_REOPEN_METHOD_MANUAL &&
                    !empty($formdata->addattempt)) {
                $shouldreopen = true;
            }
            // Never reopen if we are editing a previous attempt.
            if ($attemptnumber != -1) {
                $shouldreopen = false;
            }
            if ($shouldreopen && !$maxattemptsreached) {
                $this->process_add_attempt($userid);
            }
        } else {
            return false;
        }
        return true;
    }

    /**
     * This function is a static wrapper around can_upgrade.
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
     * This function returns true if it can upgrade an assignment from the 2.2 module.
     *
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
    public function copy_area_files_for_upgrade($oldcontextid,
                                                $oldcomponent,
                                                $oldfilearea,
                                                $olditemid,
                                                $newcontextid,
                                                $newcomponent,
                                                $newfilearea,
                                                $newitemid) {
        // Note, this code is based on some code in filestorage - but that code
        // deleted the old files (which we don't want).
        $count = 0;

        $fs = get_file_storage();

        $oldfiles = $fs->get_area_files($oldcontextid,
                                        $oldcomponent,
                                        $oldfilearea,
                                        $olditemid,
                                        'id',
                                        false);
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
     * Add a new attempt for each user in the list - but reopen each group assignment
     * at most 1 time.
     *
     * @param array $useridlist Array of userids to reopen.
     * @return bool
     */
    protected function process_add_attempt_group($useridlist) {
        $groupsprocessed = array();
        $result = true;

        foreach ($useridlist as $userid) {
            $groupid = 0;
            $group = $this->get_submission_group($userid);
            if ($group) {
                $groupid = $group->id;
            }

            if (empty($groupsprocessed[$groupid])) {
                $result = $this->process_add_attempt($userid) && $result;
                $groupsprocessed[$groupid] = true;
            }
        }
        return $result;
    }

    /**
     * Add a new attempt for a user.
     *
     * @param int $userid int The user to add the attempt for
     * @return bool - true if successful.
     */
    protected function process_add_attempt($userid) {
        require_capability('mod/assign:grade', $this->context);
        require_sesskey();

        if ($this->get_instance()->attemptreopenmethod == ASSIGN_ATTEMPT_REOPEN_METHOD_NONE) {
            return false;
        }

        if ($this->get_instance()->teamsubmission) {
            $submission = $this->get_group_submission($userid, 0, false);
        } else {
            $submission = $this->get_user_submission($userid, false);
        }

        if (!$submission) {
            return false;
        }

        // No more than max attempts allowed.
        if ($this->get_instance()->maxattempts != ASSIGN_UNLIMITED_ATTEMPTS &&
            $submission->attemptnumber >= ($this->get_instance()->maxattempts - 1)) {
            return false;
        }

        // Create the new submission record for the group/user.
        if ($this->get_instance()->teamsubmission) {
            $submission = $this->get_group_submission($userid, 0, true, $submission->attemptnumber+1);
        } else {
            $submission = $this->get_user_submission($userid, true, $submission->attemptnumber+1);
        }

        // Set the status of the new attempt to reopened.
        $submission->status = ASSIGN_SUBMISSION_STATUS_REOPENED;
        $this->update_submission($submission, $userid, false, $this->get_instance()->teamsubmission);
        return true;
    }

    /**
     * Get an upto date list of user grades and feedback for the gradebook.
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

        $adminconfig = $this->get_admin_config();
        $gradebookpluginname = $adminconfig->feedback_plugin_for_gradebook;
        $gradebookplugin = null;

        // Find the gradebook plugin.
        foreach ($this->feedbackplugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {
                if (('assignfeedback_' . $plugin->get_type()) == $gradebookpluginname) {
                    $gradebookplugin = $plugin;
                }
            }
        }
        if ($userid) {
            $where = ' WHERE u.id = :userid ';
        } else {
            $where = ' WHERE u.id != :userid ';
        }

        $submissionmaxattempt = 'SELECT mxs.userid, MAX(mxs.attemptnumber) AS maxattempt
                                 FROM {assign_submission} mxs
                                 WHERE mxs.assignment = :assignid1 GROUP BY mxs.userid';
        $grademaxattempt = 'SELECT mxg.userid, MAX(mxg.attemptnumber) AS maxattempt
                            FROM {assign_grades} mxg
                            WHERE mxg.assignment = :assignid2 GROUP BY mxg.userid';

        // When the gradebook asks us for grades - only return the last attempt for each user.
        $params = array('assignid1'=>$assignmentid,
                        'assignid2'=>$assignmentid,
                        'assignid3'=>$assignmentid,
                        'assignid4'=>$assignmentid,
                        'userid'=>$userid);
        $graderesults = $DB->get_recordset_sql('SELECT
                                                    u.id as userid,
                                                    s.timemodified as datesubmitted,
                                                    g.grade as rawgrade,
                                                    g.timemodified as dategraded,
                                                    g.grader as usermodified
                                                FROM {user} u
                                                LEFT JOIN ( ' . $submissionmaxattempt . ' ) smx ON u.id = smx.userid
                                                LEFT JOIN ( ' . $grademaxattempt . ' ) gmx ON u.id = gmx.userid
                                                LEFT JOIN {assign_submission} s
                                                    ON u.id = s.userid and s.assignment = :assignid3 AND
                                                    s.attemptnumber = smx.maxattempt
                                                JOIN {assign_grades} g
                                                    ON u.id = g.userid and g.assignment = :assignid4 AND
                                                    g.attemptnumber = gmx.maxattempt' .
                                                $where, $params);

        foreach ($graderesults as $result) {
            $gradebookgrade = clone $result;
            // Now get the feedback.
            if ($gradebookplugin) {
                $grade = $this->get_user_grade($result->userid, false);
                if ($grade) {
                    $gradebookgrade->feedbacktext = $gradebookplugin->text_for_gradebook($grade);
                    $gradebookgrade->feedbackformat = $gradebookplugin->format_for_gradebook($grade);
                }
            }
            $grades[$gradebookgrade->userid] = $gradebookgrade;
        }

        $graderesults->close();
        return $grades;
    }

    /**
     * Call the static version of this function
     *
     * @param int $userid The userid to lookup
     * @return int The unique id
     */
    public function get_uniqueid_for_user($userid) {
        return self::get_uniqueid_for_user_static($this->get_instance()->id, $userid);
    }

    /**
     * Foreach participant in the course - assign them a random id.
     *
     * @param int $assignid The assignid to lookup
     */
    public static function allocate_unique_ids($assignid) {
        global $DB;

        $cm = get_coursemodule_from_instance('assign', $assignid, 0, false, MUST_EXIST);
        $context = context_module::instance($cm->id);

        $currentgroup = groups_get_activity_group($cm, true);
        $users = get_enrolled_users($context, "mod/assign:submit", $currentgroup, 'u.id');

        // Shuffle the users.
        shuffle($users);

        $record = new stdClass();
        $record->assignment = $assignid;
        foreach ($users as $user) {
            $record = $DB->get_record('assign_user_mapping',
                                      array('assignment'=>$assignid, 'userid'=>$user->id),
                                     'id');
            if (!$record) {
                $record = new stdClass();
                $record->userid = $user->id;
                $DB->insert_record('assign_user_mapping', $record);
            }
        }
    }

    /**
     * Lookup this user id and return the unique id for this assignment.
     *
     * @param int $userid The userid to lookup
     * @return int The unique id
     */
    public static function get_uniqueid_for_user_static($assignid, $userid) {
        global $DB;

        // Search for a record.
        $params = array('assignment'=>$assignid, 'userid'=>$userid);
        if ($record = $DB->get_record('assign_user_mapping', $params, 'id')) {
            return $record->id;
        }

        // Be a little smart about this - there is no record for the current user.
        // We should ensure any unallocated ids for the current participant
        // list are distrubited randomly.
        self::allocate_unique_ids($assignid);

        // Retry the search for a record.
        if ($record = $DB->get_record('assign_user_mapping', $params, 'id')) {
            return $record->id;
        }

        // The requested user must not be a participant. Add a record anyway.
        $record = new stdClass();
        $record->assignment = $assignid;
        $record->userid = $userid;

        return $DB->insert_record('assign_user_mapping', $record);
    }

    /**
     * Call the static version of this function.
     *
     * @param int $uniqueid The uniqueid to lookup
     * @return int The user id or false if they don't exist
     */
    public function get_user_id_for_uniqueid($uniqueid) {
        return self::get_user_id_for_uniqueid_static($this->get_instance()->id, $uniqueid);
    }

    /**
     * Lookup this unique id and return the user id for this assignment.
     *
     * @param int $assignid The id of the assignment this user mapping is in
     * @param int $uniqueid The uniqueid to lookup
     * @return int The user id or false if they don't exist
     */
    public static function get_user_id_for_uniqueid_static($assignid, $uniqueid) {
        global $DB;

        // Search for a record.
        if ($record = $DB->get_record('assign_user_mapping',
                                      array('assignment'=>$assignid, 'id'=>$uniqueid),
                                      'userid',
                                      IGNORE_MISSING)) {
            return $record->userid;
        }

        return false;
    }

}

/**
 * Portfolio caller class for mod_assign.
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_portfolio_caller extends portfolio_module_caller_base {

    /** @var int callback arg - the id of submission we export */
    protected $sid;

    /** @var string component of the submission files we export*/
    protected $component;

    /** @var string callback arg - the area of submission files we export */
    protected $area;

    /** @var int callback arg - the id of file we export */
    protected $fileid;

    /** @var int callback arg - the cmid of the assignment we export */
    protected $cmid;

    /** @var string callback arg - the plugintype of the editor we export */
    protected $plugin;

    /** @var string callback arg - the name of the editor field we export */
    protected $editor;

    /**
     * Callback arg for a single file export.
     */
    public static function expected_callbackargs() {
        return array(
            'cmid' => true,
            'sid' => false,
            'area' => false,
            'component' => false,
            'fileid' => false,
            'plugin' => false,
            'editor' => false,
        );
    }

    /**
     * The constructor.
     *
     * @param array $callbackargs
     */
    public function __construct($callbackargs) {
        parent::__construct($callbackargs);
        $this->cm = get_coursemodule_from_id('assign', $this->cmid, 0, false, MUST_EXIST);
    }

    /**
     * Load data needed for the portfolio export.
     *
     * If the assignment type implements portfolio_load_data(), the processing is delegated
     * to it. Otherwise, the caller must provide either fileid (to export single file) or
     * submissionid and filearea (to export all data attached to the given submission file area)
     * via callback arguments.
     *
     * @throws     portfolio_caller_exception
     */
    public function load_data() {

        $context = context_module::instance($this->cmid);

        if (empty($this->fileid)) {
            if (empty($this->sid) || empty($this->area)) {
                throw new portfolio_caller_exception('invalidfileandsubmissionid', 'mod_assign');
            }

        }

        // Export either an area of files or a single file (see function for more detail).
        // The first arg is an id or null. If it is an id, the rest of the args are ignored.
        // If it is null, the rest of the args are used to load a list of files from get_areafiles.
        $this->set_file_and_format_data($this->fileid,
                                        $context->id,
                                        $this->component,
                                        $this->area,
                                        $this->sid,
                                        'timemodified',
                                        false);

    }

    /**
     * Prepares the package up before control is passed to the portfolio plugin.
     *
     * @throws portfolio_caller_exception
     * @return mixed
     */
    public function prepare_package() {

        if ($this->plugin && $this->editor) {
            $options = portfolio_format_text_options();
            $context = context_module::instance($this->cmid);
            $options->context = $context;

            $plugin = $this->get_submission_plugin();

            $text = $plugin->get_editor_text($this->editor, $this->sid);
            $format = $plugin->get_editor_format($this->editor, $this->sid);

            $html = format_text($text, $format, $options);
            $html = portfolio_rewrite_pluginfile_urls($html,
                                                      $context->id,
                                                      'mod_assign',
                                                      $this->area,
                                                      $this->sid,
                                                      $this->exporter->get('format'));

            $exporterclass = $this->exporter->get('formatclass');
            if (in_array($exporterclass, array(PORTFOLIO_FORMAT_PLAINHTML, PORTFOLIO_FORMAT_RICHHTML))) {
                if ($files = $this->exporter->get('caller')->get('multifiles')) {
                    foreach ($files as $file) {
                        $this->exporter->copy_existing_file($file);
                    }
                }
                return $this->exporter->write_new_file($html, 'assignment.html', !empty($files));
            } else if ($this->exporter->get('formatclass') == PORTFOLIO_FORMAT_LEAP2A) {
                $leapwriter = $this->exporter->get('format')->leap2a_writer();
                $entry = new portfolio_format_leap2a_entry($this->area . $this->cmid,
                                                           print_context_name($context),
                                                           'resource',
                                                           $html);

                $entry->add_category('web', 'resource_type');
                $entry->author = $this->user;
                $leapwriter->add_entry($entry);
                if ($files = $this->exporter->get('caller')->get('multifiles')) {
                    $leapwriter->link_files($entry, $files, $this->area . $this->cmid . 'file');
                    foreach ($files as $file) {
                        $this->exporter->copy_existing_file($file);
                    }
                }
                return $this->exporter->write_new_file($leapwriter->to_xml(),
                                                       $this->exporter->get('format')->manifest_name(),
                                                       true);
            } else {
                debugging('invalid format class: ' . $this->exporter->get('formatclass'));
            }

        }

        if ($this->exporter->get('formatclass') == PORTFOLIO_FORMAT_LEAP2A) {
            $leapwriter = $this->exporter->get('format')->leap2a_writer();
            $files = array();
            if ($this->singlefile) {
                $files[] = $this->singlefile;
            } else if ($this->multifiles) {
                $files = $this->multifiles;
            } else {
                throw new portfolio_caller_exception('invalidpreparepackagefile',
                                                     'portfolio',
                                                     $this->get_return_url());
            }

            $entryids = array();
            foreach ($files as $file) {
                $entry = new portfolio_format_leap2a_file($file->get_filename(), $file);
                $entry->author = $this->user;
                $leapwriter->add_entry($entry);
                $this->exporter->copy_existing_file($file);
                $entryids[] = $entry->id;
            }
            if (count($files) > 1) {
                $baseid = 'assign' . $this->cmid . $this->area;
                $context = context_module::instance($this->cmid);

                // If we have multiple files, they should be grouped together into a folder.
                $entry = new portfolio_format_leap2a_entry($baseid . 'group',
                                                           print_context_name($context),
                                                           'selection');
                $leapwriter->add_entry($entry);
                $leapwriter->make_selection($entry, $entryids, 'Folder');
            }
            return $this->exporter->write_new_file($leapwriter->to_xml(),
                                                   $this->exporter->get('format')->manifest_name(),
                                                   true);
        }
        return $this->prepare_package_file();
    }

    /**
     * Fetch the plugin by its type.
     *
     * @return assign_submission_plugin
     */
    protected function get_submission_plugin() {
        global $CFG;
        if (!$this->plugin || !$this->cmid) {
            return null;
        }

        require_once($CFG->dirroot . '/mod/assign/locallib.php');

        $context = context_module::instance($this->cmid);

        $assignment = new assign($context, null, null);
        return $assignment->get_submission_plugin_by_type($this->plugin);
    }

    /**
     * Calculate a sha1 has of either a single file or a list
     * of files based on the data set by load_data.
     *
     * @return string
     */
    public function get_sha1() {

        if ($this->plugin && $this->editor) {
            $plugin = $this->get_submission_plugin();
            $options = portfolio_format_text_options();
            $options->context = context_module::instance($this->cmid);

            $text = format_text($plugin->get_editor_text($this->editor, $this->sid),
                                $plugin->get_editor_format($this->editor, $this->sid),
                                $options);
            $textsha1 = sha1($text);
            $filesha1 = '';
            try {
                $filesha1 = $this->get_sha1_file();
            } catch (portfolio_caller_exception $e) {
                // No files.
            }
            return sha1($textsha1 . $filesha1);
        }
        return $this->get_sha1_file();
    }

    /**
     * Calculate the time to transfer either a single file or a list
     * of files based on the data set by load_data.
     *
     * @return int
     */
    public function expected_time() {
        return $this->expected_time_file();
    }

    /**
     * Checking the permissions.
     *
     * @return bool
     */
    public function check_permissions() {
        $context = context_module::instance($this->cmid);
        return has_capability('mod/assign:exportownsubmission', $context);
    }

    /**
     * Display a module name.
     *
     * @return string
     */
    public static function display_name() {
        return get_string('modulename', 'assign');
    }

    /**
     * Return array of formats supported by this portfolio call back.
     *
     * @return array
     */
    public static function base_supported_formats() {
        return array(PORTFOLIO_FORMAT_FILE, PORTFOLIO_FORMAT_LEAP2A);
    }
}
