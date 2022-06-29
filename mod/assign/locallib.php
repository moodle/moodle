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
define('ASSIGN_SUBMISSION_STATUS_NEW', 'new');
define('ASSIGN_SUBMISSION_STATUS_REOPENED', 'reopened');
define('ASSIGN_SUBMISSION_STATUS_DRAFT', 'draft');
define('ASSIGN_SUBMISSION_STATUS_SUBMITTED', 'submitted');

// Search filters for grading page.
define('ASSIGN_FILTER_NONE', 'none');
define('ASSIGN_FILTER_SUBMITTED', 'submitted');
define('ASSIGN_FILTER_NOT_SUBMITTED', 'notsubmitted');
define('ASSIGN_FILTER_SINGLE_USER', 'singleuser');
define('ASSIGN_FILTER_REQUIRE_GRADING', 'requiregrading');
define('ASSIGN_FILTER_GRANTED_EXTENSION', 'grantedextension');
define('ASSIGN_FILTER_DRAFT', 'draft');

// Marker filter for grading page.
define('ASSIGN_MARKER_FILTER_NO_MARKER', -1);

// Reopen attempt methods.
define('ASSIGN_ATTEMPT_REOPEN_METHOD_NONE', 'none');
define('ASSIGN_ATTEMPT_REOPEN_METHOD_MANUAL', 'manual');
define('ASSIGN_ATTEMPT_REOPEN_METHOD_UNTILPASS', 'untilpass');

// Special value means allow unlimited attempts.
define('ASSIGN_UNLIMITED_ATTEMPTS', -1);

// Special value means no grade has been set.
define('ASSIGN_GRADE_NOT_SET', -1);

// Grading states.
define('ASSIGN_GRADING_STATUS_GRADED', 'graded');
define('ASSIGN_GRADING_STATUS_NOT_GRADED', 'notgraded');

// Marking workflow states.
define('ASSIGN_MARKING_WORKFLOW_STATE_NOTMARKED', 'notmarked');
define('ASSIGN_MARKING_WORKFLOW_STATE_INMARKING', 'inmarking');
define('ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW', 'readyforreview');
define('ASSIGN_MARKING_WORKFLOW_STATE_INREVIEW', 'inreview');
define('ASSIGN_MARKING_WORKFLOW_STATE_READYFORRELEASE', 'readyforrelease');
define('ASSIGN_MARKING_WORKFLOW_STATE_RELEASED', 'released');

/** ASSIGN_MAX_EVENT_LENGTH = 432000 ; 5 days maximum */
define("ASSIGN_MAX_EVENT_LENGTH", "432000");

// Name of file area for intro attachments.
define('ASSIGN_INTROATTACHMENT_FILEAREA', 'introattachment');

// Name of file area for activity attachments.
define('ASSIGN_ACTIVITYATTACHMENT_FILEAREA', 'activityattachment');

// Event types.
define('ASSIGN_EVENT_TYPE_DUE', 'due');
define('ASSIGN_EVENT_TYPE_GRADINGDUE', 'gradingdue');
define('ASSIGN_EVENT_TYPE_OPEN', 'open');
define('ASSIGN_EVENT_TYPE_CLOSE', 'close');

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
require_once($CFG->libdir . '/portfolio/caller.php');

use \mod_assign\output\grading_app;
use \mod_assign\output\assign_header;
use \mod_assign\output\assign_submission_status;
use mod_assign\output\timelimit_panel;

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

    /** @var array $var array an array containing per-user assignment records, each having calculated properties (e.g. dates) */
    private $userinstances = [];

    /** @var grade_item the grade_item record for this assign instance's primary grade item. */
    private $gradeitem;

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

    /** @var cm_info the course module for this assign instance */
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

    /** @var array of marking workflow states for the current user */
    private $markingworkflowstates = null;

    /** @var bool whether to exclude users with inactive enrolment */
    private $showonlyactiveenrol = null;

    /** @var string A key used to identify userlists created by this object. */
    private $useridlistid = null;

    /** @var array cached list of participants for this assignment. The cache key will be group, showactive and the context id */
    private $participants = array();

    /** @var array cached list of user groups when team submissions are enabled. The cache key will be the user. */
    private $usersubmissiongroups = array();

    /** @var array cached list of user groups. The cache key will be the user. */
    private $usergroups = array();

    /** @var array cached list of IDs of users who share group membership with the user. The cache key will be the user. */
    private $sharedgroupmembers = array();

    /**
     * @var stdClass The most recent team submission. Used to determine additional attempt numbers and whether
     * to update the gradebook.
     */
    private $mostrecentteamsubmission = null;

    /** @var array Array of error messages encountered during the execution of assignment related operations. */
    private $errors = array();

    /**
     * Constructor for the base assign class.
     *
     * Note: For $coursemodule you can supply a stdclass if you like, but it
     * will be more efficient to supply a cm_info object.
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
        global $SESSION;

        $this->context = $coursemodulecontext;
        $this->course = $course;

        // Ensure that $this->coursemodule is a cm_info object (or null).
        $this->coursemodule = cm_info::create($coursemodule);

        // Temporary cache only lives for a single request - used to reduce db lookups.
        $this->cache = array();

        $this->submissionplugins = $this->load_plugins('assignsubmission');
        $this->feedbackplugins = $this->load_plugins('assignfeedback');

        // Extra entropy is required for uniqid() to work on cygwin.
        $this->useridlistid = clean_param(uniqid('', true), PARAM_ALPHANUM);

        if (!isset($SESSION->mod_assign_useridlist)) {
            $SESSION->mod_assign_useridlist = [];
        }
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
        $cm = $this->get_course_module();
        if ($cm) {
            $currenturl = new moodle_url('/mod/assign/view.php', array('id' => $cm->id));
        } else {
            $currenturl = new moodle_url('/mod/assign/index.php', array('id' => $this->get_course()->id));
        }

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

        // Web services don't set a URL, we should avoid debugging when ussing the url object.
        if (!WS_SERVER) {
            $params = $PAGE->url->params();
        }

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
    public function show_intro() {
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

        $params = array();
        if (!WS_SERVER) {
            $params = $PAGE->url->params();
        }
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
     * Set error message.
     *
     * @param string $message The error message
     */
    protected function set_error_message(string $message) {
        $this->errors[] = $message;
    }

    /**
     * Get error messages.
     *
     * @return array The array of error messages
     */
    protected function get_error_messages(): array {
        return $this->errors;
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
     * Is hidden grading enabled?
     *
     * This just checks the assignment settings. Remember to check
     * the user has the 'showhiddengrader' capability too
     *
     * @return bool
     */
    public function is_hidden_grader() {
        return $this->get_instance()->hidegrader;
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
    public function load_plugins($subtype) {
        global $CFG;
        $result = array();

        $names = core_component::get_plugin_list($subtype);

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
     * @param array $args Optional arguments to pass to the view (instead of getting them from GET and POST).
     * @return string - The page output.
     */
    public function view($action='', $args = array()) {
        global $PAGE;

        $o = '';
        $mform = null;
        $notices = array();
        $nextpageparams = array();

        if (!empty($this->get_course_module()->id)) {
            $nextpageparams['id'] = $this->get_course_module()->id;
        }

        if (empty($action)) {
            $PAGE->add_body_class('limitedwidth');
        }

        // Handle form submissions first.
        if ($action == 'savesubmission') {
            $action = 'editsubmission';
            if ($this->process_save_submission($mform, $notices)) {
                $action = 'redirect';
                if ($this->can_grade()) {
                    $nextpageparams['action'] = 'grading';
                } else {
                    $nextpageparams['action'] = 'view';
                }
            }
        } else if ($action == 'editprevioussubmission') {
            $action = 'editsubmission';
            if ($this->process_copy_previous_attempt($notices)) {
                $action = 'redirect';
                $nextpageparams['action'] = 'editsubmission';
            }
        } else if ($action == 'lock') {
            $this->process_lock_submission();
            $action = 'redirect';
            $nextpageparams['action'] = 'grading';
        } else if ($action == 'removesubmission') {
            $this->process_remove_submission();
            $action = 'redirect';
            if ($this->can_grade()) {
                $nextpageparams['action'] = 'grading';
            } else {
                $nextpageparams['action'] = 'view';
            }
        } else if ($action == 'addattempt') {
            $this->process_add_attempt(required_param('userid', PARAM_INT));
            $action = 'redirect';
            $nextpageparams['action'] = 'grading';
        } else if ($action == 'reverttodraft') {
            $this->process_revert_to_draft();
            $action = 'redirect';
            $nextpageparams['action'] = 'grading';
        } else if ($action == 'unlock') {
            $this->process_unlock_submission();
            $action = 'redirect';
            $nextpageparams['action'] = 'grading';
        } else if ($action == 'setbatchmarkingworkflowstate') {
            $this->process_set_batch_marking_workflow_state();
            $action = 'redirect';
            $nextpageparams['action'] = 'grading';
        } else if ($action == 'setbatchmarkingallocation') {
            $this->process_set_batch_marking_allocation();
            $action = 'redirect';
            $nextpageparams['action'] = 'grading';
        } else if ($action == 'confirmsubmit') {
            $action = 'submit';
            if ($this->process_submit_for_grading($mform, $notices)) {
                $action = 'redirect';
                $nextpageparams['action'] = 'view';
            } else if ($notices) {
                $action = 'viewsubmitforgradingerror';
            }
        } else if ($action == 'submitotherforgrading') {
            if ($this->process_submit_other_for_grading($mform, $notices)) {
                $action = 'redirect';
                $nextpageparams['action'] = 'grading';
            } else {
                $action = 'viewsubmitforgradingerror';
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
                    $nextpageparams['useridlistid'] = optional_param('useridlistid', $this->get_useridlist_key_id(), PARAM_ALPHANUM);
                }
            } else if (optional_param('nosaveandprevious', null, PARAM_RAW)) {
                $action = 'redirect';
                $nextpageparams['action'] = 'grade';
                $nextpageparams['rownum'] = optional_param('rownum', 0, PARAM_INT) - 1;
                $nextpageparams['useridlistid'] = optional_param('useridlistid', $this->get_useridlist_key_id(), PARAM_ALPHANUM);
            } else if (optional_param('nosaveandnext', null, PARAM_RAW)) {
                $action = 'redirect';
                $nextpageparams['action'] = 'grade';
                $nextpageparams['rownum'] = optional_param('rownum', 0, PARAM_INT) + 1;
                $nextpageparams['useridlistid'] = optional_param('useridlistid', $this->get_useridlist_key_id(), PARAM_ALPHANUM);
            } else if (optional_param('savegrade', null, PARAM_RAW)) {
                // Save changes button.
                $action = 'grade';
                if ($this->process_save_grade($mform)) {
                    $action = 'redirect';
                    $nextpageparams['action'] = 'savegradingresult';
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
                              'useridlistid' => optional_param('useridlistid', $this->get_useridlist_key_id(), PARAM_ALPHANUM));
        $this->register_return_link($action, $returnparams);

        // Include any page action as part of the body tag CSS id.
        if (!empty($action)) {
            $PAGE->set_pagetype('mod-assign-' . $action);
        }
        // Now show the right view page.
        if ($action == 'redirect') {
            $nextpageurl = new moodle_url('/mod/assign/view.php', $nextpageparams);
            $messages = '';
            $messagetype = \core\output\notification::NOTIFY_INFO;
            $errors = $this->get_error_messages();
            if (!empty($errors)) {
                $messages = html_writer::alist($errors, ['class' => 'mb-1 mt-1']);
                $messagetype = \core\output\notification::NOTIFY_ERROR;
            }
            redirect($nextpageurl, $messages, null, $messagetype);
            return;
        } else if ($action == 'savegradingresult') {
            $message = get_string('gradingchangessaved', 'assign');
            $o .= $this->view_savegrading_result($message);
        } else if ($action == 'quickgradingresult') {
            $mform = null;
            $o .= $this->view_quickgrading_result($message);
        } else if ($action == 'gradingpanel') {
            $o .= $this->view_single_grading_panel($args);
        } else if ($action == 'grade') {
            $o .= $this->view_single_grade_page($mform);
        } else if ($action == 'viewpluginassignfeedback') {
            $o .= $this->view_plugin_content('assignfeedback');
        } else if ($action == 'viewpluginassignsubmission') {
            $o .= $this->view_plugin_content('assignsubmission');
        } else if ($action == 'editsubmission') {
            $PAGE->add_body_class('limitedwidth');
            $o .= $this->view_edit_submission_page($mform, $notices);
        } else if ($action == 'grader') {
            $o .= $this->view_grader();
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
        } else if ($action == 'removesubmissionconfirm') {
            $o .= $this->view_remove_submission_confirm();
        } else if ($action == 'plugingradingbatchoperation') {
            $o .= $this->view_plugin_grading_batch_operation($mform);
        } else if ($action == 'viewpluginpage') {
             $o .= $this->view_plugin_page();
        } else if ($action == 'viewcourseindex') {
             $o .= $this->view_course_index();
        } else if ($action == 'viewbatchsetmarkingworkflowstate') {
             $o .= $this->view_batch_set_workflow_state($mform);
        } else if ($action == 'viewbatchmarkingallocation') {
            $o .= $this->view_batch_markingallocation($mform);
        } else if ($action == 'viewsubmitforgradingerror') {
            $o .= $this->view_error_page(get_string('submitforgrading', 'assign'), $notices);
        } else if ($action == 'fixrescalednullgrades') {
            $o .= $this->view_fix_rescaled_null_grades();
        } else {
            $PAGE->add_body_class('limitedwidth');
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
        $adminconfig = $this->get_admin_config();

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
        if (isset($formdata->activityeditor)) {
            $update->activity = $this->save_editor_draft_files($formdata);
            $update->activityformat = $formdata->activityeditor['format'];
        }
        if (isset($formdata->submissionattachments)) {
            $update->submissionattachments = $formdata->submissionattachments;
        }
        $update->submissiondrafts = $formdata->submissiondrafts;
        $update->requiresubmissionstatement = $formdata->requiresubmissionstatement;
        $update->sendnotifications = $formdata->sendnotifications;
        $update->sendlatenotifications = $formdata->sendlatenotifications;
        $update->sendstudentnotifications = $adminconfig->sendstudentnotifications;
        if (isset($formdata->sendstudentnotifications)) {
            $update->sendstudentnotifications = $formdata->sendstudentnotifications;
        }
        $update->duedate = $formdata->duedate;
        $update->cutoffdate = $formdata->cutoffdate;
        $update->gradingduedate = $formdata->gradingduedate;
        if (isset($formdata->timelimit)) {
            $update->timelimit = $formdata->timelimit;
        }
        $update->allowsubmissionsfromdate = $formdata->allowsubmissionsfromdate;
        $update->grade = $formdata->grade;
        $update->completionsubmit = !empty($formdata->completionsubmit);
        $update->teamsubmission = $formdata->teamsubmission;
        $update->requireallteammemberssubmit = $formdata->requireallteammemberssubmit;
        if (isset($formdata->teamsubmissiongroupingid)) {
            $update->teamsubmissiongroupingid = $formdata->teamsubmissiongroupingid;
        }
        $update->blindmarking = $formdata->blindmarking;
        if (isset($formdata->hidegrader)) {
            $update->hidegrader = $formdata->hidegrader;
        }
        $update->attemptreopenmethod = ASSIGN_ATTEMPT_REOPEN_METHOD_NONE;
        if (!empty($formdata->attemptreopenmethod)) {
            $update->attemptreopenmethod = $formdata->attemptreopenmethod;
        }
        if (!empty($formdata->maxattempts)) {
            $update->maxattempts = $formdata->maxattempts;
        }
        if (isset($formdata->preventsubmissionnotingroup)) {
            $update->preventsubmissionnotingroup = $formdata->preventsubmissionnotingroup;
        }
        $update->markingworkflow = $formdata->markingworkflow;
        $update->markingallocation = $formdata->markingallocation;
        if (empty($update->markingworkflow)) { // If marking workflow is disabled, make sure allocation is disabled.
            $update->markingallocation = 0;
        }

        $returnid = $DB->insert_record('assign', $update);
        $this->instance = $DB->get_record('assign', array('id'=>$returnid), '*', MUST_EXIST);
        // Cache the course record.
        $this->course = $DB->get_record('course', array('id'=>$formdata->course), '*', MUST_EXIST);

        $this->save_intro_draft_files($formdata);
        $this->save_editor_draft_files($formdata);

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
            if (!empty($formdata->completionexpected)) {
                \core_completion\api::update_completion_date_event($formdata->coursemodule, 'assign', $this->instance,
                        $formdata->completionexpected);
            }
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

        $this->delete_all_overrides();

        // Delete_records will throw an exception if it fails - so no need for error checking here.
        $DB->delete_records('assign_submission', array('assignment' => $this->get_instance()->id));
        $DB->delete_records('assign_grades', array('assignment' => $this->get_instance()->id));
        $DB->delete_records('assign_plugin_config', array('assignment' => $this->get_instance()->id));
        $DB->delete_records('assign_user_flags', array('assignment' => $this->get_instance()->id));
        $DB->delete_records('assign_user_mapping', array('assignment' => $this->get_instance()->id));

        // Delete items from the gradebook.
        if (! $this->delete_grades()) {
            $result = false;
        }

        // Delete the instance.
        // We must delete the module record after we delete the grade item.
        $DB->delete_records('assign', array('id'=>$this->get_instance()->id));

        return $result;
    }

    /**
     * Deletes a assign override from the database and clears any corresponding calendar events
     *
     * @param int $overrideid The id of the override being deleted
     * @return bool true on success
     */
    public function delete_override($overrideid) {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/calendar/lib.php');

        $cm = $this->get_course_module();
        if (empty($cm)) {
            $instance = $this->get_instance();
            $cm = get_coursemodule_from_instance('assign', $instance->id, $instance->course);
        }

        $override = $DB->get_record('assign_overrides', array('id' => $overrideid), '*', MUST_EXIST);

        // Delete the events.
        $conds = array('modulename' => 'assign', 'instance' => $this->get_instance()->id);
        if (isset($override->userid)) {
            $conds['userid'] = $override->userid;
            $cachekey = "{$cm->instance}_u_{$override->userid}";
        } else {
            $conds['groupid'] = $override->groupid;
            $cachekey = "{$cm->instance}_g_{$override->groupid}";
        }
        $events = $DB->get_records('event', $conds);
        foreach ($events as $event) {
            $eventold = calendar_event::load($event);
            $eventold->delete();
        }

        $DB->delete_records('assign_overrides', array('id' => $overrideid));
        cache::make('mod_assign', 'overrides')->delete($cachekey);

        // Set the common parameters for one of the events we will be triggering.
        $params = array(
            'objectid' => $override->id,
            'context' => context_module::instance($cm->id),
            'other' => array(
                'assignid' => $override->assignid
            )
        );
        // Determine which override deleted event to fire.
        if (!empty($override->userid)) {
            $params['relateduserid'] = $override->userid;
            $event = \mod_assign\event\user_override_deleted::create($params);
        } else {
            $params['other']['groupid'] = $override->groupid;
            $event = \mod_assign\event\group_override_deleted::create($params);
        }

        // Trigger the override deleted event.
        $event->add_record_snapshot('assign_overrides', $override);
        $event->trigger();

        return true;
    }

    /**
     * Deletes all assign overrides from the database and clears any corresponding calendar events
     */
    public function delete_all_overrides() {
        global $DB;

        $overrides = $DB->get_records('assign_overrides', array('assignid' => $this->get_instance()->id), 'id');
        foreach ($overrides as $override) {
            $this->delete_override($override->id);
        }
    }

    /**
     * Updates the assign properties with override information for a user.
     *
     * Algorithm:  For each assign setting, if there is a matching user-specific override,
     *   then use that otherwise, if there are group-specific overrides, return the most
     *   lenient combination of them.  If neither applies, leave the assign setting unchanged.
     *
     * @param int $userid The userid.
     */
    public function update_effective_access($userid) {

        $override = $this->override_exists($userid);

        // Merge with assign defaults.
        $keys = array('duedate', 'cutoffdate', 'allowsubmissionsfromdate', 'timelimit');
        foreach ($keys as $key) {
            if (isset($override->{$key})) {
                $this->get_instance($userid)->{$key} = $override->{$key};
            }
        }

    }

    /**
     * Returns whether an assign has any overrides.
     *
     * @return true if any, false if not
     */
    public function has_overrides() {
        global $DB;

        $override = $DB->record_exists('assign_overrides', array('assignid' => $this->get_instance()->id));

        if ($override) {
            return true;
        }

        return false;
    }

    /**
     * Returns user override
     *
     * Algorithm:  For each assign setting, if there is a matching user-specific override,
     *   then use that otherwise, if there are group-specific overrides, use the one with the
     *   lowest sort order. If neither applies, leave the assign setting unchanged.
     *
     * @param int $userid The userid.
     * @return stdClass The override
     */
    public function override_exists($userid) {
        global $DB;

        // Gets an assoc array containing the keys for defined user overrides only.
        $getuseroverride = function($userid) use ($DB) {
            $useroverride = $DB->get_record('assign_overrides', ['assignid' => $this->get_instance()->id, 'userid' => $userid]);
            return $useroverride ? get_object_vars($useroverride) : [];
        };

        // Gets an assoc array containing the keys for defined group overrides only.
        $getgroupoverride = function($userid) use ($DB) {
            $groupings = groups_get_user_groups($this->get_instance()->course, $userid);

            if (empty($groupings[0])) {
                return [];
            }

            // Select all overrides that apply to the User's groups.
            list($extra, $params) = $DB->get_in_or_equal(array_values($groupings[0]));
            $sql = "SELECT * FROM {assign_overrides}
                    WHERE groupid $extra AND assignid = ? ORDER BY sortorder ASC";
            $params[] = $this->get_instance()->id;
            $groupoverride = $DB->get_record_sql($sql, $params, IGNORE_MULTIPLE);

            return $groupoverride ? get_object_vars($groupoverride) : [];
        };

        // Later arguments clobber earlier ones with array_merge. The two helper functions
        // return arrays containing keys for only the defined overrides. So we get the
        // desired behaviour as per the algorithm.
        return (object)array_merge(
            ['timelimit' => null, 'duedate' => null, 'cutoffdate' => null, 'allowsubmissionsfromdate' => null],
            $getgroupoverride($userid),
            $getuseroverride($userid)
        );
    }

    /**
     * Check if the given calendar_event is either a user or group override
     * event.
     *
     * @return bool
     */
    public function is_override_calendar_event(\calendar_event $event) {
        global $DB;

        if (!isset($event->modulename)) {
            return false;
        }

        if ($event->modulename != 'assign') {
            return false;
        }

        if (!isset($event->instance)) {
            return false;
        }

        if (!isset($event->userid) && !isset($event->groupid)) {
            return false;
        }

        $overrideparams = [
            'assignid' => $event->instance
        ];

        if (isset($event->groupid)) {
            $overrideparams['groupid'] = $event->groupid;
        } else if (isset($event->userid)) {
            $overrideparams['userid'] = $event->userid;
        }

        if ($DB->get_record('assign_overrides', $overrideparams)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * This function calculates the minimum and maximum cutoff values for the timestart of
     * the given event.
     *
     * It will return an array with two values, the first being the minimum cutoff value and
     * the second being the maximum cutoff value. Either or both values can be null, which
     * indicates there is no minimum or maximum, respectively.
     *
     * If a cutoff is required then the function must return an array containing the cutoff
     * timestamp and error string to display to the user if the cutoff value is violated.
     *
     * A minimum and maximum cutoff return value will look like:
     * [
     *     [1505704373, 'The due date must be after the sbumission start date'],
     *     [1506741172, 'The due date must be before the cutoff date']
     * ]
     *
     * If the event does not have a valid timestart range then [false, false] will
     * be returned.
     *
     * @param calendar_event $event The calendar event to get the time range for
     * @return array
     */
    function get_valid_calendar_event_timestart_range(\calendar_event $event) {
        $instance = $this->get_instance();
        $submissionsfromdate = $instance->allowsubmissionsfromdate;
        $cutoffdate = $instance->cutoffdate;
        $duedate = $instance->duedate;
        $gradingduedate = $instance->gradingduedate;
        $mindate = null;
        $maxdate = null;

        if ($event->eventtype == ASSIGN_EVENT_TYPE_DUE) {
            // This check is in here because due date events are currently
            // the only events that can be overridden, so we can save a DB
            // query if we don't bother checking other events.
            if ($this->is_override_calendar_event($event)) {
                // This is an override event so there is no valid timestart
                // range to set it to.
                return [false, false];
            }

            if ($submissionsfromdate) {
                $mindate = [
                    $submissionsfromdate,
                    get_string('duedatevalidation', 'assign'),
                ];
            }

            if ($cutoffdate) {
                $maxdate = [
                    $cutoffdate,
                    get_string('cutoffdatevalidation', 'assign'),
                ];
            }

            if ($gradingduedate) {
                // If we don't have a cutoff date or we've got a grading due date
                // that is earlier than the cutoff then we should use that as the
                // upper limit for the due date.
                if (!$cutoffdate || $gradingduedate < $cutoffdate) {
                    $maxdate = [
                        $gradingduedate,
                        get_string('gradingdueduedatevalidation', 'assign'),
                    ];
                }
            }
        } else if ($event->eventtype == ASSIGN_EVENT_TYPE_GRADINGDUE) {
            if ($duedate) {
                $mindate = [
                    $duedate,
                    get_string('gradingdueduedatevalidation', 'assign'),
                ];
            } else if ($submissionsfromdate) {
                $mindate = [
                    $submissionsfromdate,
                    get_string('gradingduefromdatevalidation', 'assign'),
                ];
            }
        }

        return [$mindate, $maxdate];
    }

    /**
     * Actual implementation of the reset course functionality, delete all the
     * assignment submissions for course $data->courseid.
     *
     * @param stdClass $data the data submitted from the reset course.
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
                foreach ($fileareas as $filearea => $notused) {
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
                foreach ($fileareas as $filearea => $notused) {
                    $fs->delete_area_files($this->context->id, $plugincomponent, $filearea);
                }

                if (!$plugin->delete_instance()) {
                    $status[] = array('component'=>$componentstr,
                                      'item'=>get_string('deleteallsubmissions', 'assign'),
                                      'error'=>$plugin->get_error());
                }
            }

            $assignids = $DB->get_records('assign', array('course' => $data->courseid), '', 'id');
            list($sql, $params) = $DB->get_in_or_equal(array_keys($assignids));

            $DB->delete_records_select('assign_submission', "assignment $sql", $params);
            $DB->delete_records_select('assign_user_flags', "assignment $sql", $params);

            $status[] = array('component'=>$componentstr,
                              'item'=>get_string('deleteallsubmissions', 'assign'),
                              'error'=>false);

            if (!empty($data->reset_gradebook_grades)) {
                $DB->delete_records_select('assign_grades', "assignment $sql", $params);
                // Remove all grades from gradebook.
                require_once($CFG->dirroot.'/mod/assign/lib.php');
                assign_reset_gradebook($data->courseid);
            }

            // Reset revealidentities for assign if blindmarking is enabled.
            if ($this->get_instance()->blindmarking) {
                $DB->set_field('assign', 'revealidentities', 0, array('id' => $this->get_instance()->id));
            }
        }

        $purgeoverrides = false;

        // Remove user overrides.
        if (!empty($data->reset_assign_user_overrides)) {
            $DB->delete_records_select('assign_overrides',
                'assignid IN (SELECT id FROM {assign} WHERE course = ?) AND userid IS NOT NULL', array($data->courseid));
            $status[] = array(
                'component' => $componentstr,
                'item' => get_string('useroverridesdeleted', 'assign'),
                'error' => false);
            $purgeoverrides = true;
        }
        // Remove group overrides.
        if (!empty($data->reset_assign_group_overrides)) {
            $DB->delete_records_select('assign_overrides',
                'assignid IN (SELECT id FROM {assign} WHERE course = ?) AND groupid IS NOT NULL', array($data->courseid));
            $status[] = array(
                'component' => $componentstr,
                'item' => get_string('groupoverridesdeleted', 'assign'),
                'error' => false);
            $purgeoverrides = true;
        }

        // Updating dates - shift may be negative too.
        if ($data->timeshift) {
            $DB->execute("UPDATE {assign_overrides}
                         SET allowsubmissionsfromdate = allowsubmissionsfromdate + ?
                       WHERE assignid = ? AND allowsubmissionsfromdate <> 0",
                array($data->timeshift, $this->get_instance()->id));
            $DB->execute("UPDATE {assign_overrides}
                         SET duedate = duedate + ?
                       WHERE assignid = ? AND duedate <> 0",
                array($data->timeshift, $this->get_instance()->id));
            $DB->execute("UPDATE {assign_overrides}
                         SET cutoffdate = cutoffdate + ?
                       WHERE assignid =? AND cutoffdate <> 0",
                array($data->timeshift, $this->get_instance()->id));

            $purgeoverrides = true;

            // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
            // See MDL-9367.
            shift_course_mod_dates('assign',
                                    array('duedate', 'allowsubmissionsfromdate', 'cutoffdate'),
                                    $data->timeshift,
                                    $data->courseid, $this->get_instance()->id);
            $status[] = array('component'=>$componentstr,
                              'item'=>get_string('datechanged'),
                              'error'=>false);
        }

        if ($purgeoverrides) {
            cache::make('mod_assign', 'overrides')->purge();
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

        // Set assign gradebook feedback plugin status (enabled and visible).
        $assign->gradefeedbackenabled = $this->is_gradebook_feedback_enabled();

        $param = null;
        if ($reset) {
            $param = 'reset';
        }

        return assign_grade_item_update($assign, $param);
    }

    /**
     * Get the marking table page size
     *
     * @return integer
     */
    public function get_assign_perpage() {
        $perpage = (int) get_user_preferences('assign_perpage', 10);
        $adminconfig = $this->get_admin_config();
        $maxperpage = -1;
        if (isset($adminconfig->maxperpage)) {
            $maxperpage = $adminconfig->maxperpage;
        }
        if (isset($maxperpage) &&
            $maxperpage != -1 &&
            ($perpage == -1 || $perpage > $maxperpage)) {
            $perpage = $maxperpage;
        }
        return $perpage;
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

        // Start with creating the event.
        $event = new stdClass();
        $event->modulename  = 'assign';
        $event->courseid = $instance->course;
        $event->groupid = 0;
        $event->userid  = 0;
        $event->instance  = $instance->id;
        $event->type = CALENDAR_EVENT_TYPE_ACTION;

        // Convert the links to pluginfile. It is a bit hacky but at this stage the files
        // might not have been saved in the module area yet.
        $intro = $instance->intro;
        if ($draftid = file_get_submitted_draft_itemid('introeditor')) {
            $intro = file_rewrite_urls_to_pluginfile($intro, $draftid);
        }

        // We need to remove the links to files as the calendar is not ready
        // to support module events with file areas.
        $intro = strip_pluginfile_content($intro);
        if ($this->show_intro()) {
            $event->description = array(
                'text' => $intro,
                'format' => $instance->introformat
            );
        } else {
            $event->description = array(
                'text' => '',
                'format' => $instance->introformat
            );
        }

        $eventtype = ASSIGN_EVENT_TYPE_DUE;
        if ($instance->duedate) {
            $event->name = get_string('calendardue', 'assign', $instance->name);
            $event->eventtype = $eventtype;
            $event->timestart = $instance->duedate;
            $event->timesort = $instance->duedate;
            $select = "modulename = :modulename
                       AND instance = :instance
                       AND eventtype = :eventtype
                       AND groupid = 0
                       AND courseid <> 0";
            $params = array('modulename' => 'assign', 'instance' => $instance->id, 'eventtype' => $eventtype);
            $event->id = $DB->get_field_select('event', 'id', $select, $params);

            // Now process the event.
            if ($event->id) {
                $calendarevent = calendar_event::load($event->id);
                $calendarevent->update($event, false);
            } else {
                calendar_event::create($event, false);
            }
        } else {
            $DB->delete_records('event', array('modulename' => 'assign', 'instance' => $instance->id,
                'eventtype' => $eventtype));
        }

        $eventtype = ASSIGN_EVENT_TYPE_GRADINGDUE;
        if ($instance->gradingduedate) {
            $event->name = get_string('calendargradingdue', 'assign', $instance->name);
            $event->eventtype = $eventtype;
            $event->timestart = $instance->gradingduedate;
            $event->timesort = $instance->gradingduedate;
            $event->id = $DB->get_field('event', 'id', array('modulename' => 'assign',
                'instance' => $instance->id, 'eventtype' => $event->eventtype));

            // Now process the event.
            if ($event->id) {
                $calendarevent = calendar_event::load($event->id);
                $calendarevent->update($event, false);
            } else {
                calendar_event::create($event, false);
            }
        } else {
            $DB->delete_records('event', array('modulename' => 'assign', 'instance' => $instance->id,
                'eventtype' => $eventtype));
        }

        return true;
    }

    /**
     * Update this instance in the database.
     *
     * @param stdClass $formdata - the data submitted from the form
     * @return bool false if an error occurs
     */
    public function update_instance($formdata) {
        global $DB;
        $adminconfig = $this->get_admin_config();

        $update = new stdClass();
        $update->id = $formdata->instance;
        $update->name = $formdata->name;
        $update->timemodified = time();
        $update->course = $formdata->course;
        $update->intro = $formdata->intro;
        $update->introformat = $formdata->introformat;
        $update->alwaysshowdescription = !empty($formdata->alwaysshowdescription);
        if (isset($formdata->activityeditor)) {
            $update->activity = $this->save_editor_draft_files($formdata);
            $update->activityformat = $formdata->activityeditor['format'];
        }
        if (isset($formdata->submissionattachments)) {
            $update->submissionattachments = $formdata->submissionattachments;
        }
        $update->submissiondrafts = $formdata->submissiondrafts;
        $update->requiresubmissionstatement = $formdata->requiresubmissionstatement;
        $update->sendnotifications = $formdata->sendnotifications;
        $update->sendlatenotifications = $formdata->sendlatenotifications;
        $update->sendstudentnotifications = $adminconfig->sendstudentnotifications;
        if (isset($formdata->sendstudentnotifications)) {
            $update->sendstudentnotifications = $formdata->sendstudentnotifications;
        }
        $update->duedate = $formdata->duedate;
        $update->cutoffdate = $formdata->cutoffdate;
        if (isset($formdata->timelimit)) {
            $update->timelimit = $formdata->timelimit;
        }
        $update->gradingduedate = $formdata->gradingduedate;
        $update->allowsubmissionsfromdate = $formdata->allowsubmissionsfromdate;
        $update->grade = $formdata->grade;
        if (!empty($formdata->completionunlocked)) {
            $update->completionsubmit = !empty($formdata->completionsubmit);
        }
        $update->teamsubmission = $formdata->teamsubmission;
        $update->requireallteammemberssubmit = $formdata->requireallteammemberssubmit;
        if (isset($formdata->teamsubmissiongroupingid)) {
            $update->teamsubmissiongroupingid = $formdata->teamsubmissiongroupingid;
        }
        if (isset($formdata->hidegrader)) {
            $update->hidegrader = $formdata->hidegrader;
        }
        $update->blindmarking = $formdata->blindmarking;
        $update->attemptreopenmethod = ASSIGN_ATTEMPT_REOPEN_METHOD_NONE;
        if (!empty($formdata->attemptreopenmethod)) {
            $update->attemptreopenmethod = $formdata->attemptreopenmethod;
        }
        if (!empty($formdata->maxattempts)) {
            $update->maxattempts = $formdata->maxattempts;
        }
        if (isset($formdata->preventsubmissionnotingroup)) {
            $update->preventsubmissionnotingroup = $formdata->preventsubmissionnotingroup;
        }
        $update->markingworkflow = $formdata->markingworkflow;
        $update->markingallocation = $formdata->markingallocation;
        if (empty($update->markingworkflow)) { // If marking workflow is disabled, make sure allocation is disabled.
            $update->markingallocation = 0;
        }

        $result = $DB->update_record('assign', $update);
        $this->instance = $DB->get_record('assign', array('id'=>$update->id), '*', MUST_EXIST);

        $this->save_intro_draft_files($formdata);

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
        $completionexpected = (!empty($formdata->completionexpected)) ? $formdata->completionexpected : null;
        \core_completion\api::update_completion_date_event($this->get_course_module()->id, 'assign', $this->instance,
                $completionexpected);
        $this->update_gradebook(false, $this->get_course_module()->id);

        $update = new stdClass();
        $update->id = $this->get_instance()->id;
        $update->nosubmissions = (!$this->is_any_submission_plugin_enabled()) ? 1: 0;
        $DB->update_record('assign', $update);

        return $result;
    }

    /**
     * Save the attachments in the intro description.
     *
     * @param stdClass $formdata
     */
    protected function save_intro_draft_files($formdata) {
        if (isset($formdata->introattachments)) {
            file_save_draft_area_files($formdata->introattachments, $this->get_context()->id,
                                       'mod_assign', ASSIGN_INTROATTACHMENT_FILEAREA, 0);
        }
    }

    /**
     * Save the attachments in the editor description.
     *
     * @param stdClass $formdata
     */
    protected function save_editor_draft_files($formdata): string {
        $text = '';
        if (isset($formdata->activityeditor)) {
            $text = $formdata->activityeditor['text'];
            if (isset($formdata->activityeditor['itemid'])) {
                $text = file_save_draft_area_files($formdata->activityeditor['itemid'], $this->get_context()->id,
                    'mod_assign', ASSIGN_ACTIVITYATTACHMENT_FILEAREA,
                    0, array('subdirs' => true), $formdata->activityeditor['text']);
            }
        }
        return $text;
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
                $plugin->get_form_elements_for_user($grade, $mform, $data, $userid);
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
        if ($plugin->is_visible() && !$plugin->is_configurable() && $plugin->is_enabled()) {
            $name = $plugin->get_subtype() . '_' . $plugin->get_type() . '_enabled';
            $pluginsenabled[] = $mform->createElement('hidden', $name, 1);
            $mform->setType($name, PARAM_BOOL);
            $plugin->get_settings($mform);
        } else if ($plugin->is_visible() && $plugin->is_configurable()) {
            $name = $plugin->get_subtype() . '_' . $plugin->get_type() . '_enabled';
            $label = $plugin->get_name();
            $pluginsenabled[] = $mform->createElement('checkbox', $name, '', $label);
            $helpicon = $this->get_renderer()->help_icon('enabled', $plugin->get_subtype() . '_' . $plugin->get_type());
            $pluginsenabled[] = $mform->createElement('static', '', '', $helpicon);

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
     * Get the settings for the current instance of this assignment.
     *
     * @return stdClass The settings
     * @throws dml_exception
     */
    public function get_default_instance() {
        global $DB;
        if (!$this->instance && $this->get_course_module()) {
            $params = array('id' => $this->get_course_module()->instance);
            $this->instance = $DB->get_record('assign', $params, '*', MUST_EXIST);

            $this->userinstances = [];
        }
        return $this->instance;
    }

    /**
     * Get the settings for the current instance of this assignment
     * @param int|null $userid the id of the user to load the assign instance for.
     * @return stdClass The settings
     */
    public function get_instance(int $userid = null) : stdClass {
        global $USER;
        $userid = $userid ?? $USER->id;

        $this->instance = $this->get_default_instance();

        // If we have the user instance already, just return it.
        if (isset($this->userinstances[$userid])) {
            return $this->userinstances[$userid];
        }

        // Calculate properties which vary per user.
        $this->userinstances[$userid] = $this->calculate_properties($this->instance, $userid);
        return $this->userinstances[$userid];
    }

    /**
     * Calculates and updates various properties based on the specified user.
     *
     * @param stdClass $record the raw assign record.
     * @param int $userid the id of the user to calculate the properties for.
     * @return stdClass a new record having calculated properties.
     */
    private function calculate_properties(\stdClass $record, int $userid) : \stdClass {
        $record = clone ($record);

        // Relative dates.
        if (!empty($record->duedate)) {
            $course = $this->get_course();
            $usercoursedates = course_get_course_dates_for_user_id($course, $userid);
            if ($usercoursedates['start']) {
                $userprops = ['duedate' => $record->duedate + $usercoursedates['startoffset']];
                $record = (object) array_merge((array) $record, (array) $userprops);
            }
        }
        return $record;
    }

    /**
     * Get the primary grade item for this assign instance.
     *
     * @return grade_item The grade_item record
     */
    public function get_grade_item() {
        if ($this->gradeitem) {
            return $this->gradeitem;
        }
        $instance = $this->get_instance();
        $params = array('itemtype' => 'mod',
                        'itemmodule' => 'assign',
                        'iteminstance' => $instance->id,
                        'courseid' => $instance->course,
                        'itemnumber' => 0);
        $this->gradeitem = grade_item::fetch($params);
        if (!$this->gradeitem) {
            throw new coding_exception('Improper use of the assignment class. ' .
                                       'Cannot load the grade item.');
        }
        return $this->gradeitem;
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
     * @return cm_info|null The course module or null if not known
     */
    public function get_course_module() {
        if ($this->coursemodule) {
            return $this->coursemodule;
        }
        if (!$this->context) {
            return null;
        }

        if ($this->context->contextlevel == CONTEXT_MODULE) {
            $modinfo = get_fast_modinfo($this->get_course());
            $this->coursemodule = $modinfo->get_cm($this->context->instanceid);
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

        if ($this->course && is_object($this->course)) {
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
     * Count the number of intro attachments.
     *
     * @return int
     */
    protected function count_attachments() {

        $fs = get_file_storage();
        $files = $fs->get_area_files($this->get_context()->id, 'mod_assign', ASSIGN_INTROATTACHMENT_FILEAREA,
                        0, 'id', false);

        return count($files);
    }

    /**
     * Are there any intro attachments to display?
     *
     * @return boolean
     */
    protected function has_visible_attachments() {
        return ($this->count_attachments() > 0);
    }

    /**
     * Check if the intro attachments should be provided to the user.
     *
     * @param int $userid User id.
     * @return bool
     */
    public function should_provide_intro_attachments(int $userid): bool {
        $instance = $this->get_instance($userid);

        // Check if user has permission to view attachments regardless of assignment settings.
        if (has_capability('moodle/course:manageactivities', $this->get_context())) {
            return true;
        }

        // If assignment does not show intro, we never provide intro attachments.
        if (!$this->show_intro()) {
            return false;
        }

        // If intro attachments should only be shown when submission is started, check if there is an open submission.
        if (!empty($instance->submissionattachments) && !$this->submissions_open($userid, true)) {
            return false;
        }

        return true;
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
                    $displaygrade = format_float($grade, $this->get_grade_item()->get_decimals());
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
                $o .= '&nbsp;/&nbsp;' . format_float($this->get_instance()->grade, $this->get_grade_item()->get_decimals());
                return $o;
            } else {
                if ($grade == -1 || $grade === null) {
                    $o .= '-';
                } else {
                    $item = $this->get_grade_item();
                    $o .= grade_format_gradevalue($grade, $item);
                    if ($item->get_displaytype() == GRADE_DISPLAY_TYPE_REAL) {
                        // If displaying the raw grade, also display the total value.
                        $o .= '&nbsp;/&nbsp;' . format_float($this->get_instance()->grade, $item->get_decimals());
                    }
                }
                return $o;
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
     * Get the submission status/grading status for all submissions in this assignment for the
     * given paticipants.
     *
     * These statuses match the available filters (requiregrading, submitted, notsubmitted, grantedextension).
     * If this is a group assignment, group info is also returned.
     *
     * @param array $participants an associative array where the key is the participant id and
     *                            the value is the participant record.
     * @return array an associative array where the key is the participant id and the value is
     *               the participant record.
     */
    private function get_submission_info_for_participants($participants) {
        global $DB;

        if (empty($participants)) {
            return $participants;
        }

        list($insql, $params) = $DB->get_in_or_equal(array_keys($participants), SQL_PARAMS_NAMED);

        $assignid = $this->get_instance()->id;
        $params['assignmentid1'] = $assignid;
        $params['assignmentid2'] = $assignid;
        $params['assignmentid3'] = $assignid;

        $fields = 'SELECT u.id, s.status, s.timemodified AS stime, g.timemodified AS gtime, g.grade, uf.extensionduedate';
        $from = ' FROM {user} u
                         LEFT JOIN {assign_submission} s
                                ON u.id = s.userid
                               AND s.assignment = :assignmentid1
                               AND s.latest = 1
                         LEFT JOIN {assign_grades} g
                                ON u.id = g.userid
                               AND g.assignment = :assignmentid2
                               AND g.attemptnumber = s.attemptnumber
                         LEFT JOIN {assign_user_flags} uf
                                ON u.id = uf.userid
                               AND uf.assignment = :assignmentid3
            ';
        $where = ' WHERE u.id ' . $insql;

        if (!empty($this->get_instance()->blindmarking)) {
            $from .= 'LEFT JOIN {assign_user_mapping} um
                             ON u.id = um.userid
                            AND um.assignment = :assignmentid4 ';
            $params['assignmentid4'] = $assignid;
            $fields .= ', um.id as recordid ';
        }

        $sql = "$fields $from $where";

        $records = $DB->get_records_sql($sql, $params);

        if ($this->get_instance()->teamsubmission) {
            // Get all groups.
            $allgroups = groups_get_all_groups($this->get_course()->id,
                                               array_keys($participants),
                                               $this->get_instance()->teamsubmissiongroupingid,
                                               'DISTINCT g.id, g.name');

        }
        foreach ($participants as $userid => $participant) {
            $participants[$userid]->fullname = $this->fullname($participant);
            $participants[$userid]->submitted = false;
            $participants[$userid]->requiregrading = false;
            $participants[$userid]->grantedextension = false;
            $participants[$userid]->submissionstatus = '';
        }

        foreach ($records as $userid => $submissioninfo) {
            // These filters are 100% the same as the ones in the grading table SQL.
            $submitted = false;
            $requiregrading = false;
            $grantedextension = false;
            $submissionstatus = !empty($submissioninfo->status) ? $submissioninfo->status : '';

            if (!empty($submissioninfo->stime) && $submissioninfo->status == ASSIGN_SUBMISSION_STATUS_SUBMITTED) {
                $submitted = true;
            }

            if ($submitted && ($submissioninfo->stime >= $submissioninfo->gtime ||
                    empty($submissioninfo->gtime) ||
                    $submissioninfo->grade === null)) {
                $requiregrading = true;
            }

            if (!empty($submissioninfo->extensionduedate)) {
                $grantedextension = true;
            }

            $participants[$userid]->submitted = $submitted;
            $participants[$userid]->requiregrading = $requiregrading;
            $participants[$userid]->grantedextension = $grantedextension;
            $participants[$userid]->submissionstatus = $submissionstatus;
            if ($this->get_instance()->teamsubmission) {
                $group = $this->get_submission_group($userid);
                if ($group) {
                    $participants[$userid]->groupid = $group->id;
                    $participants[$userid]->groupname = $group->name;
                }
            }
        }
        return $participants;
    }

    /**
     * Get the submission status/grading status for all submissions in this assignment.
     * These statuses match the available filters (requiregrading, submitted, notsubmitted, grantedextension).
     * If this is a group assignment, group info is also returned.
     *
     * @param int $currentgroup
     * @param boolean $tablesort Apply current user table sorting preferences.
     * @return array List of user records with extra fields 'submitted', 'notsubmitted', 'requiregrading', 'grantedextension',
     *               'groupid', 'groupname'
     */
    public function list_participants_with_filter_status_and_group($currentgroup, $tablesort = false) {
        $participants = $this->list_participants($currentgroup, false, $tablesort);

        if (empty($participants)) {
            return $participants;
        } else {
            return $this->get_submission_info_for_participants($participants);
        }
    }

    /**
     * Return a valid order by segment for list_participants that matches
     * the sorting of the current grading table. Not every field is supported,
     * we are only concerned with a list of users so we can't search on anything
     * that is not part of the user information (like grading statud or last modified stuff).
     *
     * @return string Order by clause for list_participants
     */
    private function get_grading_sort_sql() {
        $usersort = flexible_table::get_sort_for_table('mod_assign_grading');
        // TODO Does not support custom user profile fields (MDL-70456).
        $userfieldsapi = \core_user\fields::for_identity($this->context, false)->with_userpic();
        $userfields = $userfieldsapi->get_required_fields();
        $orderfields = explode(',', $usersort);
        $validlist = [];

        foreach ($orderfields as $orderfield) {
            $orderfield = trim($orderfield);
            foreach ($userfields as $field) {
                $parts = explode(' ', $orderfield);
                if ($parts[0] == $field) {
                    // Prepend the user table prefix and count this as a valid order field.
                    array_push($validlist, 'u.' . $orderfield);
                }
            }
        }
        // Produce a final list.
        $result = implode(',', $validlist);
        if (empty($result)) {
            // Fall back ordering when none has been set.
            $result = 'u.lastname, u.firstname, u.id';
        }

        return $result;
    }

    /**
     * Returns array with sql code and parameters returning all ids of users who have submitted an assignment.
     *
     * @param int $group The group that the query is for.
     * @return array list($sql, $params)
     */
    protected function get_submitted_sql($group = 0) {
        // We need to guarentee unique table names.
        static $i = 0;
        $i++;
        $prefix = 'sa' . $i . '_';
        $params = [
            "{$prefix}assignment" => (int) $this->get_instance()->id,
            "{$prefix}status" => ASSIGN_SUBMISSION_STATUS_NEW,
        ];
        $capjoin = get_enrolled_with_capabilities_join($this->context, $prefix, '', $group, $this->show_only_active_users());
        $params += $capjoin->params;
        $sql = "SELECT {$prefix}s.userid
                  FROM {assign_submission} {$prefix}s
                  JOIN {user} {$prefix}u ON {$prefix}u.id = {$prefix}s.userid
                  $capjoin->joins
                 WHERE {$prefix}s.assignment = :{$prefix}assignment
                   AND {$prefix}s.status <> :{$prefix}status
                   AND $capjoin->wheres";
        return array($sql, $params);
    }

    /**
     * Load a list of users enrolled in the current course with the specified permission and group.
     * 0 for no group.
     * Apply any current sort filters from the grading table.
     *
     * @param int $currentgroup
     * @param bool $idsonly
     * @param bool $tablesort
     * @return array List of user records
     */
    public function list_participants($currentgroup, $idsonly, $tablesort = false) {
        global $DB, $USER;

        // Get the last known sort order for the grading table.

        if (empty($currentgroup)) {
            $currentgroup = 0;
        }

        $key = $this->context->id . '-' . $currentgroup . '-' . $this->show_only_active_users();
        if (!isset($this->participants[$key])) {
            list($esql, $params) = get_enrolled_sql($this->context, 'mod/assign:submit', $currentgroup,
                    $this->show_only_active_users());
            list($ssql, $sparams) = $this->get_submitted_sql($currentgroup);
            $params += $sparams;

            $fields = 'u.*';
            $orderby = 'u.lastname, u.firstname, u.id';

            $additionaljoins = '';
            $additionalfilters = '';
            $instance = $this->get_instance();
            if (!empty($instance->blindmarking)) {
                $additionaljoins .= " LEFT JOIN {assign_user_mapping} um
                                  ON u.id = um.userid
                                 AND um.assignment = :assignmentid1
                           LEFT JOIN {assign_submission} s
                                  ON u.id = s.userid
                                 AND s.assignment = :assignmentid2
                                 AND s.latest = 1
                        ";
                $params['assignmentid1'] = (int) $instance->id;
                $params['assignmentid2'] = (int) $instance->id;
                $fields .= ', um.id as recordid ';

                // Sort by submission time first, then by um.id to sort reliably by the blind marking id.
                // Note, different DBs have different ordering of NULL values.
                // Therefore we coalesce the current time into the timecreated field, and the max possible integer into
                // the ID field.
                if (empty($tablesort)) {
                    $orderby = "COALESCE(s.timecreated, " . time() . ") ASC, COALESCE(s.id, " . PHP_INT_MAX . ") ASC, um.id ASC";
                }
            }

            if ($instance->markingworkflow &&
                    $instance->markingallocation &&
                    !has_capability('mod/assign:manageallocations', $this->get_context()) &&
                    has_capability('mod/assign:grade', $this->get_context())) {

                $additionaljoins .= ' LEFT JOIN {assign_user_flags} uf
                                     ON u.id = uf.userid
                                     AND uf.assignment = :assignmentid3';

                $params['assignmentid3'] = (int) $instance->id;

                $additionalfilters .= ' AND uf.allocatedmarker = :markerid';
                $params['markerid'] = $USER->id;
            }

            $sql = "SELECT $fields
                      FROM {user} u
                      JOIN ($esql UNION $ssql) je ON je.id = u.id
                           $additionaljoins
                     WHERE u.deleted = 0
                           $additionalfilters
                  ORDER BY $orderby";

            $users = $DB->get_records_sql($sql, $params);

            $cm = $this->get_course_module();
            $info = new \core_availability\info_module($cm);
            $users = $info->filter_user_list($users);

            $this->participants[$key] = $users;
        }

        if ($tablesort) {
            // Resort the user list according to the grading table sort and filter settings.
            $sortedfiltereduserids = $this->get_grading_userid_list(true, '');
            $sortedfilteredusers = [];
            foreach ($sortedfiltereduserids as $nextid) {
                $nextid = intval($nextid);
                if (isset($this->participants[$key][$nextid])) {
                    $sortedfilteredusers[$nextid] = $this->participants[$key][$nextid];
                }
            }
            $this->participants[$key] = $sortedfilteredusers;
        }

        if ($idsonly) {
            $idslist = array();
            foreach ($this->participants[$key] as $id => $user) {
                $idslist[$id] = new stdClass();
                $idslist[$id]->id = $id;
            }
            return $idslist;
        }
        return $this->participants[$key];
    }

    /**
     * Load a user if they are enrolled in the current course. Populated with submission
     * status for this assignment.
     *
     * @param int $userid
     * @return null|stdClass user record
     */
    public function get_participant($userid) {
        global $DB, $USER;

        if ($userid == $USER->id) {
            $participant = clone ($USER);
        } else {
            $participant = $DB->get_record('user', array('id' => $userid));
        }
        if (!$participant) {
            return null;
        }

        if (!is_enrolled($this->context, $participant, '', $this->show_only_active_users())) {
            return null;
        }

        $result = $this->get_submission_info_for_participants(array($participant->id => $participant));

        $submissioninfo = $result[$participant->id];
        if (!$submissioninfo->submitted && !has_capability('mod/assign:submit', $this->context, $userid)) {
            return null;
        }

        return $submissioninfo;
    }

    /**
     * Load a count of valid teams for this assignment.
     *
     * @param int $activitygroup Activity active group
     * @return int number of valid teams
     */
    public function count_teams($activitygroup = 0) {

        $count = 0;

        $participants = $this->list_participants($activitygroup, true);

        // If a team submission grouping id is provided all good as all returned groups
        // are the submission teams, but if no team submission grouping was specified
        // $groups will contain all participants groups.
        if ($this->get_instance()->teamsubmissiongroupingid) {

            // We restrict the users to the selected group ones.
            $groups = groups_get_all_groups($this->get_course()->id,
                                            array_keys($participants),
                                            $this->get_instance()->teamsubmissiongroupingid,
                                            'DISTINCT g.id, g.name');

            $count = count($groups);

            // When a specific group is selected we don't count the default group users.
            if ($activitygroup == 0) {
                if (empty($this->get_instance()->preventsubmissionnotingroup)) {
                    // See if there are any users in the default group.
                    $defaultusers = $this->get_submission_group_members(0, true);
                    if (count($defaultusers) > 0) {
                        $count += 1;
                    }
                }
            } else if ($activitygroup != 0 && empty($groups)) {
                // Set count to 1 if $groups returns empty.
                // It means the group is not part of $this->get_instance()->teamsubmissiongroupingid.
                $count = 1;
            }
        } else {
            // It is faster to loop around participants if no grouping was specified.
            $groups = array();
            foreach ($participants as $participant) {
                if ($group = $this->get_submission_group($participant->id)) {
                    $groups[$group->id] = true;
                } else if (empty($this->get_instance()->preventsubmissionnotingroup)) {
                    $groups[0] = true;
                }
            }

            $count = count($groups);
        }

        return $count;
    }

    /**
     * Load a count of active users enrolled in the current course with the specified permission and group.
     * 0 for no group.
     *
     * @param int $currentgroup
     * @return int number of matching users
     */
    public function count_participants($currentgroup) {
        return count($this->list_participants($currentgroup, true));
    }

    /**
     * Load a count of active users submissions in the current module that require grading
     * This means the submission modification time is more recent than the
     * grading modification time and the status is SUBMITTED.
     *
     * @param mixed $currentgroup int|null the group for counting (if null the function will determine it)
     * @return int number of matching submissions
     */
    public function count_submissions_need_grading($currentgroup = null) {
        global $DB;

        if ($this->get_instance()->teamsubmission) {
            // This does not make sense for group assignment because the submission is shared.
            return 0;
        }

        if ($currentgroup === null) {
            $currentgroup = groups_get_activity_group($this->get_course_module(), true);
        }
        list($esql, $params) = get_enrolled_sql($this->get_context(), '', $currentgroup, true);

        $params['assignid'] = $this->get_instance()->id;
        $params['submitted'] = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $sqlscalegrade = $this->get_instance()->grade < 0 ? ' OR g.grade = -1' : '';

        $sql = 'SELECT COUNT(s.userid)
                   FROM {assign_submission} s
                   LEFT JOIN {assign_grades} g ON
                        s.assignment = g.assignment AND
                        s.userid = g.userid AND
                        g.attemptnumber = s.attemptnumber
                   JOIN(' . $esql . ') e ON e.id = s.userid
                   WHERE
                        s.latest = 1 AND
                        s.assignment = :assignid AND
                        s.timemodified IS NOT NULL AND
                        s.status = :submitted AND
                        (s.timemodified >= g.timemodified OR g.timemodified IS NULL OR g.grade IS NULL '
                            . $sqlscalegrade . ')';

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
        list($esql, $params) = get_enrolled_sql($this->get_context(), 'mod/assign:submit', $currentgroup, true);

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
     * @param bool $includenew When true, also counts the submissions with status 'new'.
     * @return int number of submissions
     */
    public function count_submissions($includenew = false) {
        global $DB;

        if (!$this->has_instance()) {
            return 0;
        }

        $params = array();
        $sqlnew = '';

        if (!$includenew) {
            $sqlnew = ' AND s.status <> :status ';
            $params['status'] = ASSIGN_SUBMISSION_STATUS_NEW;
        }

        if ($this->get_instance()->teamsubmission) {
            // We cannot join on the enrolment tables for group submissions (no userid).
            $sql = 'SELECT COUNT(DISTINCT s.groupid)
                        FROM {assign_submission} s
                        WHERE
                            s.assignment = :assignid AND
                            s.timemodified IS NOT NULL AND
                            s.userid = :groupuserid' .
                            $sqlnew;

            $params['assignid'] = $this->get_instance()->id;
            $params['groupuserid'] = 0;
        } else {
            $currentgroup = groups_get_activity_group($this->get_course_module(), true);
            list($esql, $enrolparams) = get_enrolled_sql($this->get_context(), 'mod/assign:submit', $currentgroup, true);

            $params = array_merge($params, $enrolparams);
            $params['assignid'] = $this->get_instance()->id;

            $sql = 'SELECT COUNT(DISTINCT s.userid)
                       FROM {assign_submission} s
                       JOIN(' . $esql . ') e ON e.id = s.userid
                       WHERE
                            s.assignment = :assignid AND
                            s.timemodified IS NOT NULL ' .
                            $sqlnew;

        }

        return $DB->count_records_sql($sql, $params);
    }

    /**
     * Load a count of submissions with a specified status.
     *
     * @param string $status The submission status - should match one of the constants
     * @param mixed $currentgroup int|null the group for counting (if null the function will determine it)
     * @return int number of matching submissions
     */
    public function count_submissions_with_status($status, $currentgroup = null) {
        global $DB;

        if ($currentgroup === null) {
            $currentgroup = groups_get_activity_group($this->get_course_module(), true);
        }
        list($esql, $params) = get_enrolled_sql($this->get_context(), '', $currentgroup, true);

        $params['assignid'] = $this->get_instance()->id;
        $params['assignid2'] = $this->get_instance()->id;
        $params['submissionstatus'] = $status;

        if ($this->get_instance()->teamsubmission) {

            $groupsstr = '';
            if ($currentgroup != 0) {
                // If there is an active group we should only display the current group users groups.
                $participants = $this->list_participants($currentgroup, true);
                $groups = groups_get_all_groups($this->get_course()->id,
                                                array_keys($participants),
                                                $this->get_instance()->teamsubmissiongroupingid,
                                                'DISTINCT g.id, g.name');
                if (empty($groups)) {
                    // If $groups is empty it means it is not part of $this->get_instance()->teamsubmissiongroupingid.
                    // All submissions from students that do not belong to any of teamsubmissiongroupingid groups
                    // count towards groupid = 0. Setting to true as only '0' key matters.
                    $groups = [true];
                }
                list($groupssql, $groupsparams) = $DB->get_in_or_equal(array_keys($groups), SQL_PARAMS_NAMED);
                $groupsstr = 's.groupid ' . $groupssql . ' AND';
                $params = $params + $groupsparams;
            }
            $sql = 'SELECT COUNT(s.groupid)
                        FROM {assign_submission} s
                        WHERE
                            s.latest = 1 AND
                            s.assignment = :assignid AND
                            s.timemodified IS NOT NULL AND
                            s.userid = :groupuserid AND '
                            . $groupsstr . '
                            s.status = :submissionstatus';
            $params['groupuserid'] = 0;
        } else {
            $sql = 'SELECT COUNT(s.userid)
                        FROM {assign_submission} s
                        JOIN(' . $esql . ') e ON e.id = s.userid
                        WHERE
                            s.latest = 1 AND
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
     * @param boolean $cached If true, the cached list from the session could be returned.
     * @param string $useridlistid String value used for caching the participant list.
     * @return array An array of userids
     */
    protected function get_grading_userid_list($cached = false, $useridlistid = '') {
        global $SESSION;

        if ($cached) {
            if (empty($useridlistid)) {
                $useridlistid = $this->get_useridlist_key_id();
            }
            $useridlistkey = $this->get_useridlist_key($useridlistid);
            if (empty($SESSION->mod_assign_useridlist[$useridlistkey])) {
                $SESSION->mod_assign_useridlist[$useridlistkey] = $this->get_grading_userid_list(false, '');
            }
            return $SESSION->mod_assign_useridlist[$useridlistkey];
        }
        $filter = get_user_preferences('assign_filter', '');
        $table = new assign_grading_table($this, 0, $filter, 0, false);

        $useridlist = $table->get_column_data('userid');

        return $useridlist;
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
        $task = \core\task\manager::get_scheduled_task(mod_assign\task\cron_task::class);
        $lastruntime = $task->get_last_run_time();

        // Collect all submissions that require mailing.
        // Submissions are included if all are true:
        //   - The assignment is visible in the gradebook.
        //   - No previous notification has been sent.
        //   - The grader was a real user, not an automated process.
        //   - The grade was updated in the past 24 hours.
        //   - If marking workflow is enabled, the workflow state is at 'released'.
        $sql = "SELECT g.id as gradeid, a.course, a.name, a.blindmarking, a.revealidentities, a.hidegrader,
                       g.*, g.timemodified as lastmodified, cm.id as cmid, um.id as recordid
                 FROM {assign} a
                 JOIN {assign_grades} g ON g.assignment = a.id
            LEFT JOIN {assign_user_flags} uf ON uf.assignment = a.id AND uf.userid = g.userid
                 JOIN {course_modules} cm ON cm.course = a.course AND cm.instance = a.id
                 JOIN {modules} md ON md.id = cm.module AND md.name = 'assign'
                 JOIN {grade_items} gri ON gri.iteminstance = a.id AND gri.courseid = a.course AND gri.itemmodule = md.name
            LEFT JOIN {assign_user_mapping} um ON g.id = um.userid AND um.assignment = a.id
                 WHERE (a.markingworkflow = 0 OR (a.markingworkflow = 1 AND uf.workflowstate = :wfreleased)) AND
                       g.grader > 0 AND uf.mailed = 0 AND gri.hidden = 0 AND
                       g.timemodified >= :yesterday AND g.timemodified <= :today
              ORDER BY a.course, cm.id";

        $params = array(
            'yesterday' => $yesterday,
            'today' => $timenow,
            'wfreleased' => ASSIGN_MARKING_WORKFLOW_STATE_RELEASED,
        );
        $submissions = $DB->get_records_sql($sql, $params);

        if (!empty($submissions)) {

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

                $modinfo = get_fast_modinfo($course, $user->id);
                $cm = $modinfo->get_cm($submission->cmid);
                // Context lookups are already cached.
                $contextmodule = context_module::instance($cm->id);

                if (!$cm->uservisible) {
                    // Hold mail notification for assignments the user cannot access until later.
                    continue;
                }

                // Notify the student. Default to the non-anon version.
                $messagetype = 'feedbackavailable';
                // Message type needs 'anon' if "hidden grading" is enabled and the student
                // doesn't have permission to see the grader.
                if ($submission->hidegrader && !has_capability('mod/assign:showhiddengrader', $contextmodule, $user)) {
                    $messagetype = 'feedbackavailableanon';
                    // There's no point in having an "anonymous grader" if the notification email
                    // comes from them. Send the email from the noreply user instead.
                    $grader = core_user::get_noreply_user();
                }

                $eventtype = 'assign_notification';
                $updatetime = $submission->lastmodified;
                $modulename = get_string('modulename', 'assign');

                $uniqueid = 0;
                if ($submission->blindmarking && !$submission->revealidentities) {
                    if (empty($submission->recordid)) {
                        $uniqueid = self::get_uniqueid_for_user_static($submission->assignment, $grader->id);
                    } else {
                        $uniqueid = $submission->recordid;
                    }
                }
                $showusers = $submission->blindmarking && !$submission->revealidentities;
                self::send_assignment_notification($grader,
                                                   $user,
                                                   $messagetype,
                                                   $eventtype,
                                                   $updatetime,
                                                   $cm,
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
        }

        // Update calendar events to provide a description.
        $sql = 'SELECT id
                    FROM {assign}
                    WHERE
                        allowsubmissionsfromdate >= :lastruntime AND
                        allowsubmissionsfromdate <= :timenow AND
                        alwaysshowdescription = 0';
        $params = array('lastruntime' => $lastruntime, 'timenow' => $timenow);
        $newlyavailable = $DB->get_records_sql($sql, $params);
        foreach ($newlyavailable as $record) {
            $cm = get_coursemodule_from_instance('assign', $record->id, 0, false, MUST_EXIST);
            $context = context_module::instance($cm->id);

            $assignment = new assign($context, null, null);
            $assignment->update_calendar($cm->id);
        }

        return true;
    }

    /**
     * Mark in the database that this grade record should have an update notification sent by cron.
     *
     * @param stdClass $grade a grade record keyed on id
     * @param bool $mailedoverride when true, flag notification to be sent again.
     * @return bool true for success
     */
    public function notify_grade_modified($grade, $mailedoverride = false) {
        global $DB;

        $flags = $this->get_user_flags($grade->userid, true);
        if ($flags->mailed != 1 || $mailedoverride) {
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
     * @param bool $reopenattempt If the attempt reopen method is manual, allow another attempt at this assignment.
     * @return bool true for success
     */
    public function update_grade($grade, $reopenattempt = false) {
        global $DB;

        $grade->timemodified = time();

        if (!empty($grade->workflowstate)) {
            $validstates = $this->get_marking_workflow_states_for_current_user();
            if (!array_key_exists($grade->workflowstate, $validstates)) {
                return false;
            }
        }

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
        $DB->update_record('assign_grades', $grade);

        $submission = null;
        if ($this->get_instance()->teamsubmission) {
            if (isset($this->mostrecentteamsubmission)) {
                $submission = $this->mostrecentteamsubmission;
            } else {
                $submission = $this->get_group_submission($grade->userid, 0, false);
            }
        } else {
            $submission = $this->get_user_submission($grade->userid, false);
        }

        // Only push to gradebook if the update is for the most recent attempt.
        if ($submission && $submission->attemptnumber != $grade->attemptnumber) {
            return true;
        }

        if ($this->gradebook_item_update(null, $grade)) {
            \mod_assign\event\submission_graded::create_from_grade($this, $grade)->trigger();
        }

        // If the conditions are met, allow another attempt.
        if ($submission) {
            $this->reopen_submission_if_required($grade->userid,
                    $submission,
                    $reopenattempt);
        }

        return true;
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
        global $CFG;
        require_once($CFG->dirroot . '/mod/assign/extensionform.php');

        $o = '';

        $data = new stdClass();
        $data->id = $this->get_course_module()->id;

        $formparams = array(
            'instance' => $this->get_instance(),
            'assign' => $this
        );

        $users = optional_param('userid', 0, PARAM_INT);
        if (!$users) {
            $users = required_param('selectedusers', PARAM_SEQUENCE);
        }
        $userlist = explode(',', $users);

        $keys = array('duedate', 'cutoffdate', 'allowsubmissionsfromdate');
        $maxoverride = array('allowsubmissionsfromdate' => 0, 'duedate' => 0, 'cutoffdate' => 0);
        foreach ($userlist as $userid) {
            // To validate extension date with users overrides.
            $override = $this->override_exists($userid);
            foreach ($keys as $key) {
                if ($override->{$key}) {
                    if ($maxoverride[$key] < $override->{$key}) {
                        $maxoverride[$key] = $override->{$key};
                    }
                } else if ($maxoverride[$key] < $this->get_instance()->{$key}) {
                    $maxoverride[$key] = $this->get_instance()->{$key};
                }
            }
        }
        foreach ($keys as $key) {
            if ($maxoverride[$key]) {
                $this->get_instance()->{$key} = $maxoverride[$key];
            }
        }

        $formparams['userlist'] = $userlist;

        $data->selectedusers = $users;
        $data->userid = 0;

        if (empty($mform)) {
            $mform = new mod_assign_extension_form(null, $formparams);
        }
        $mform->set_data($data);
        $header = new assign_header($this->get_instance(),
                                    $this->get_context(),
                                    $this->show_intro(),
                                    $this->get_course_module()->id,
                                    get_string('grantextension', 'assign'));
        $o .= $this->get_renderer()->render($header);
        $o .= $this->get_renderer()->render(new assign_form('extensionform', $mform));
        $o .= $this->view_footer();
        return $o;
    }

    /**
     * Get a list of the users in the same group as this user.
     *
     * @param int $groupid The id of the group whose members we want or 0 for the default group
     * @param bool $onlyids Whether to retrieve only the user id's
     * @param bool $excludesuspended Whether to exclude suspended users
     * @return array The users (possibly id's only)
     */
    public function get_submission_group_members($groupid, $onlyids, $excludesuspended = false) {
        $members = array();
        if ($groupid != 0) {
            $allusers = $this->list_participants($groupid, $onlyids);
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
        // Exclude suspended users, if user can't see them.
        if ($excludesuspended || !has_capability('moodle/course:viewsuspendedusers', $this->context)) {
            foreach ($members as $key => $member) {
                if (!$this->is_active_user($member->id)) {
                    unset($members[$key]);
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
     *                     with the status set to "new".
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
            if ($create) {
                $action = optional_param('action', '', PARAM_TEXT);
                if ($action == 'editsubmission') {
                    if (empty($submission->timestarted) && $this->get_instance()->timelimit) {
                        $submission->timestarted = time();
                        $DB->update_record('assign_submission', $submission);
                    }
                }
            }
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
            // Work out if this is the latest submission.
            $submission->latest = 0;
            $params = array('assignment'=>$this->get_instance()->id, 'groupid'=>$groupid, 'userid'=>0);
            if ($attemptnumber == -1) {
                // This is a new submission so it must be the latest.
                $submission->latest = 1;
            } else {
                // We need to work this out.
                $result = $DB->get_records('assign_submission', $params, 'attemptnumber DESC', 'attemptnumber', 0, 1);
                if ($result) {
                    $latestsubmission = reset($result);
                }
                if (!$latestsubmission || ($attemptnumber == $latestsubmission->attemptnumber)) {
                    $submission->latest = 1;
                }
            }
            if ($submission->latest) {
                // This is the case when we need to set latest to 0 for all the other attempts.
                $DB->set_field('assign_submission', 'latest', 0, $params);
            }
            $submission->status = ASSIGN_SUBMISSION_STATUS_NEW;
            $sid = $DB->insert_record('assign_submission', $submission);
            return $DB->get_record('assign_submission', array('id' => $sid));
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

        $strsectionname = '';
        $usesections = course_format_uses_sections($course->format);
        $modinfo = get_fast_modinfo($course);

        if ($usesections) {
            $strsectionname = get_string('sectionname', 'format_'.$course->format);
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

            // Apply overrides.
            $assignment->update_effective_access($USER->id);
            $timedue = $assignment->get_instance()->duedate;

            if (has_capability('mod/assign:submit', $context) &&
                !has_capability('moodle/site:config', $context)) {
                $cangrade = false;
                if ($assignment->get_instance()->teamsubmission) {
                    $usersubmission = $assignment->get_group_submission($USER->id, 0, false);
                } else {
                    $usersubmission = $assignment->get_user_submission($USER->id, false);
                }

                if (!empty($usersubmission->status)) {
                    $submitted = get_string('submissionstatus_' . $usersubmission->status, 'assign');
                } else {
                    $submitted = get_string('submissionstatus_', 'assign');
                }

                $gradinginfo = grade_get_grades($course->id, 'mod', 'assign', $cm->instance, $USER->id);
                if (isset($gradinginfo->items[0]->grades[$USER->id]) &&
                        !$gradinginfo->items[0]->grades[$USER->id]->hidden ) {
                    $grade = $gradinginfo->items[0]->grades[$USER->id]->str_grade;
                } else {
                    $grade = '-';
                }
            } else if (has_capability('mod/assign:grade', $context)) {
                $submitted = $assignment->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_SUBMITTED);
                $grade = $assignment->count_submissions_need_grading();
                $cangrade = true;
            }

            $courseindexsummary->add_assign_info($cm->id, $cm->get_formatted_name(),
                $sectionname, $timedue, $submitted, $grade, $cangrade);
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
        $plugintype = required_param('plugin', PARAM_PLUGIN);
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

        if (isset($this->usersubmissiongroups[$userid])) {
            return $this->usersubmissiongroups[$userid];
        }

        $groups = $this->get_all_groups($userid);
        if (count($groups) != 1) {
            $return = false;
        } else {
            $return = array_pop($groups);
        }

        // Cache the user submission group.
        $this->usersubmissiongroups[$userid] = $return;

        return $return;
    }

    /**
     * Gets all groups the user is a member of.
     *
     * @param int $userid Teh id of the user who's groups we are checking
     * @return array The group objects
     */
    public function get_all_groups($userid) {
        if (isset($this->usergroups[$userid])) {
            return $this->usergroups[$userid];
        }

        $grouping = $this->get_instance()->teamsubmissiongroupingid;
        $return = groups_get_all_groups($this->get_course()->id, $userid, $grouping);

        $this->usergroups[$userid] = $return;

        return $return;
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
        $o = '';

        $submissionid = optional_param('sid', 0, PARAM_INT);
        $gradeid = optional_param('gid', 0, PARAM_INT);
        $plugintype = required_param('plugin', PARAM_PLUGIN);
        $item = null;
        if ($pluginsubtype == 'assignsubmission') {
            $plugin = $this->get_submission_plugin_by_type($plugintype);
            if ($submissionid <= 0) {
                throw new coding_exception('Submission id should not be 0');
            }
            $item = $this->get_submission($submissionid);

            // Check permissions.
            if (empty($item->userid)) {
                // Group submission.
                $this->require_view_group_submission($item->groupid);
            } else {
                $this->require_view_submission($item->userid);
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

            // Trigger event for viewing a submission.
            \mod_assign\event\submission_viewed::create_from_submission($this, $item)->trigger();

        } else {
            $plugin = $this->get_feedback_plugin_by_type($plugintype);
            if ($gradeid <= 0) {
                throw new coding_exception('Grade id should not be 0');
            }
            $item = $this->get_grade($gradeid);
            // Check permissions.
            $this->require_view_submission($item->userid);
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

            // Trigger event for viewing feedback.
            \mod_assign\event\feedback_viewed::create_from_grade($this, $item)->trigger();
        }

        $o .= $this->view_return_links();

        $o .= $this->view_footer();

        return $o;
    }

    /**
     * Rewrite plugin file urls so they resolve correctly in an exported zip.
     *
     * @param string $text - The replacement text
     * @param stdClass $user - The user record
     * @param assign_plugin $plugin - The assignment plugin
     */
    public function download_rewrite_pluginfile_urls($text, $user, $plugin) {
        // The groupname prefix for the urls doesn't depend on the group mode of the assignment instance.
        // Rather, it should be determined by checking the group submission settings of the instance,
        // which is what download_submission() does when generating the file name prefixes.
        $groupname = '';
        if ($this->get_instance()->teamsubmission) {
            $submissiongroup = $this->get_submission_group($user->id);
            if ($submissiongroup) {
                $groupname = $submissiongroup->name . '-';
            } else {
                $groupname = get_string('defaultteam', 'assign') . '-';
            }
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

        // Only prefix files if downloadasfolders user preference is NOT set.
        if (!get_user_preferences('assign_downloadasfolders', 1)) {
            $subtype = $plugin->get_subtype();
            $type = $plugin->get_type();
            $prefix = $prefix . $subtype . '_' . $type . '_';
        } else {
            $prefix = "";
        }
        $result = str_replace('@@PLUGINFILE@@/', $prefix, $text);

        return $result;
    }

    /**
     * Render the content in editor that is often used by plugin.
     *
     * @param string $filearea
     * @param int $submissionid
     * @param string $plugintype
     * @param string $editor
     * @param string $component
     * @param bool $shortentext Whether to shorten the text content.
     * @return string
     */
    public function render_editor_content($filearea, $submissionid, $plugintype, $editor, $component, $shortentext = false) {
        global $CFG;

        $result = '';

        $plugin = $this->get_submission_plugin_by_type($plugintype);

        $text = $plugin->get_editor_text($editor, $submissionid);
        if ($shortentext) {
            $text = shorten_text($text, 140);
        }
        $format = $plugin->get_editor_format($editor, $submissionid);

        $finaltext = file_rewrite_pluginfile_urls($text,
                                                  'pluginfile.php',
                                                  $this->get_context()->id,
                                                  $component,
                                                  $filearea,
                                                  $submissionid);
        $params = array('overflowdiv' => true, 'context' => $this->get_context());
        $result .= format_text($finaltext, $format, $params);

        if ($CFG->enableportfolios && has_capability('mod/assign:exportownsubmission', $this->context)) {
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
            $result .= $button->to_html(PORTFOLIO_ADD_TEXT_LINK);
        }
        return $result;
    }

    /**
     * Display a continue page after grading.
     *
     * @param string $message - The message to display.
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
     * Display a continue page after quickgrading.
     *
     * @param string $message - The message to display.
     * @return string
     */
    protected function view_quickgrading_result($message) {
        $o = '';
        $o .= $this->get_renderer()->render(new assign_header($this->get_instance(),
                                                      $this->get_context(),
                                                      $this->show_intro(),
                                                      $this->get_course_module()->id,
                                                      get_string('quickgradingresult', 'assign')));
        $gradingerror = in_array($message, $this->get_error_messages());
        $lastpage = optional_param('lastpage', null, PARAM_INT);
        $gradingresult = new assign_gradingmessage(get_string('quickgradingresult', 'assign'),
                                                   $message,
                                                   $this->get_course_module()->id,
                                                   $gradingerror,
                                                   $lastpage);
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
        // When viewing the footer during PHPUNIT tests a set_state error is thrown.
        if (!PHPUNIT_TEST) {
            return $this->get_renderer()->render_footer();
        }

        return '';
    }

    /**
     * Throw an error if the permissions to view this users' group submission are missing.
     *
     * @param int $groupid Group id.
     * @throws required_capability_exception
     */
    public function require_view_group_submission($groupid) {
        if (!$this->can_view_group_submission($groupid)) {
            throw new required_capability_exception($this->context, 'mod/assign:viewgrades', 'nopermission', '');
        }
    }

    /**
     * Throw an error if the permissions to view this users submission are missing.
     *
     * @throws required_capability_exception
     * @return none
     */
    public function require_view_submission($userid) {
        if (!$this->can_view_submission($userid)) {
            throw new required_capability_exception($this->context, 'mod/assign:viewgrades', 'nopermission', '');
        }
    }

    /**
     * Throw an error if the permissions to view grades in this assignment are missing.
     *
     * @throws required_capability_exception
     * @return none
     */
    public function require_view_grades() {
        if (!$this->can_view_grades()) {
            throw new required_capability_exception($this->context, 'mod/assign:viewgrades', 'nopermission', '');
        }
    }

    /**
     * Does this user have view grade or grade permission for this assignment?
     *
     * @param mixed $groupid int|null when is set to a value, use this group instead calculating it
     * @return bool
     */
    public function can_view_grades($groupid = null) {
        // Permissions check.
        if (!has_any_capability(array('mod/assign:viewgrades', 'mod/assign:grade'), $this->context)) {
            return false;
        }
        // Checks for the edge case when user belongs to no groups and groupmode is sep.
        if ($this->get_course_module()->effectivegroupmode == SEPARATEGROUPS) {
            if ($groupid === null) {
                $groupid = groups_get_activity_allowed_groups($this->get_course_module());
            }
            $groupflag = has_capability('moodle/site:accessallgroups', $this->get_context());
            $groupflag = $groupflag || !empty($groupid);
            return (bool)$groupflag;
        }
        return true;
    }

    /**
     * Does this user have grade permission for this assignment?
     *
     * @param int|stdClass $user The object or id of the user who will do the editing (default to current user).
     * @return bool
     */
    public function can_grade($user = null) {
        // Permissions check.
        if (!has_capability('mod/assign:grade', $this->context, $user)) {
            return false;
        }

        return true;
    }

    /**
     * Download a zip file of all assignment submissions.
     *
     * @param array $userids Array of user ids to download assignment submissions in a zip file
     * @return string - If an error occurs, this will contain the error page.
     */
    protected function download_submissions($userids = false) {
        global $CFG, $DB;

        // More efficient to load this here.
        require_once($CFG->libdir.'/filelib.php');

        // Increase the server timeout to handle the creation and sending of large zip files.
        core_php_time_limit::raise();

        $this->require_view_grades();

        // Load all users with submit.
        $students = get_enrolled_users($this->context, "mod/assign:submit", null, 'u.*', null, null, null,
                        $this->show_only_active_users());

        // Build a list of files to zip.
        $filesforzipping = array();
        $fs = get_file_storage();

        $groupmode = groups_get_activity_groupmode($this->get_course_module());
        // All users.
        $groupid = 0;
        $groupname = '';
        if ($groupmode) {
            $groupid = groups_get_activity_group($this->get_course_module(), true);
            if (!empty($groupid)) {
                $groupname = groups_get_group_name($groupid) . '-';
            }
        }

        // Construct the zip file name.
        $filename = clean_filename($this->get_course()->shortname . '-' .
                                   $this->get_instance()->name . '-' .
                                   $groupname.$this->get_course_module()->id . '.zip');

        // Get all the files for each student.
        foreach ($students as $student) {
            $userid = $student->id;
            // Download all assigments submission or only selected users.
            if ($userids and !in_array($userid, $userids)) {
                continue;
            }

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
                    $prefix = clean_filename($prefix . '_' . $this->get_uniqueid_for_user($userid));
                } else {
                    $fullname = fullname($student, has_capability('moodle/site:viewfullnames', $this->get_context()));
                    $prefix = str_replace('_', ' ', $groupname . $fullname);
                    $prefix = clean_filename($prefix . '_' . $this->get_uniqueid_for_user($userid));
                }

                if ($submission) {
                    $downloadasfolders = get_user_preferences('assign_downloadasfolders', 1);
                    foreach ($this->submissionplugins as $plugin) {
                        if ($plugin->is_enabled() && $plugin->is_visible()) {
                            if ($downloadasfolders) {
                                // Create a folder for each user for each assignment plugin.
                                // This is the default behavior for version of Moodle >= 3.1.
                                $submission->exportfullpath = true;
                                $pluginfiles = $plugin->get_files($submission, $student);
                                foreach ($pluginfiles as $zipfilepath => $file) {
                                    $subtype = $plugin->get_subtype();
                                    $type = $plugin->get_type();
                                    $zipfilename = basename($zipfilepath);
                                    $prefixedfilename = clean_filename($prefix .
                                                                       '_' .
                                                                       $subtype .
                                                                       '_' .
                                                                       $type .
                                                                       '_');
                                    if ($type == 'file') {
                                        $pathfilename = $prefixedfilename . $file->get_filepath() . $zipfilename;
                                    } else if ($type == 'onlinetext') {
                                        $pathfilename = $prefixedfilename . '/' . $zipfilename;
                                    } else {
                                        $pathfilename = $prefixedfilename . '/' . $zipfilename;
                                    }
                                    $pathfilename = clean_param($pathfilename, PARAM_PATH);
                                    $filesforzipping[$pathfilename] = $file;
                                }
                            } else {
                                // Create a single folder for all users of all assignment plugins.
                                // This was the default behavior for version of Moodle < 3.1.
                                $submission->exportfullpath = false;
                                $pluginfiles = $plugin->get_files($submission, $student);
                                foreach ($pluginfiles as $zipfilename => $file) {
                                    $subtype = $plugin->get_subtype();
                                    $type = $plugin->get_type();
                                    $prefixedfilename = clean_filename($prefix .
                                                                       '_' .
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

            return $result;
        }

        // Log zip as downloaded.
        \mod_assign\event\all_submissions_downloaded::create_from_assign($this)->trigger();

        // Close the session.
        \core\session\manager::write_close();

        $zipwriter = \core_files\archive_writer::get_stream_writer($filename, \core_files\archive_writer::ZIP_WRITER);

        // Stream the files into the zip.
        foreach ($filesforzipping as $pathinzip => $file) {
            if ($file instanceof \stored_file) {
                // Most of cases are \stored_file.
                $zipwriter->add_file_from_stored_file($pathinzip, $file);
            } else if (is_array($file)) {
                // Save $file as contents, from onlinetext subplugin.
                $content = reset($file);
                $zipwriter->add_file_from_string($pathinzip, $content);
            }
        }

        // Finish the archive.
        $zipwriter->finish();
        exit();
    }

    /**
     * Util function to add a message to the log.
     *
     * @deprecated since 2.7 - Use new events system instead.
     *             (see http://docs.moodle.org/dev/Migrating_logging_calls_in_plugins).
     *
     * @param string $action The current action
     * @param string $info A detailed description of the change. But no more than 255 characters.
     * @param string $url The url to the assign module instance.
     * @param bool $return If true, returns the arguments, else adds to log. The purpose of this is to
     *                     retrieve the arguments to use them with the new event system (Event 2).
     * @return void|array
     */
    public function add_to_log($action = '', $info = '', $url='', $return = false) {
        global $USER;

        $fullurl = 'view.php?id=' . $this->get_course_module()->id;
        if ($url != '') {
            $fullurl .= '&' . $url;
        }

        $args = array(
            $this->get_course()->id,
            'assign',
            $action,
            $fullurl,
            $info,
            $this->get_course_module()->id
        );

        if ($return) {
            // We only need to call debugging when returning a value. This is because the call to
            // call_user_func_array('add_to_log', $args) will trigger a debugging message of it's own.
            debugging('The mod_assign add_to_log() function is now deprecated.', DEBUG_DEVELOPER);
            return $args;
        }
        call_user_func_array('add_to_log', $args);
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
        $this->output = $PAGE->get_renderer('mod_assign', null, RENDERER_TARGET_GENERAL);
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
     * @param bool $create If set to true a new submission object will be created in the database with the status set to "new".
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
            if ($create) {
                $action = optional_param('action', '', PARAM_TEXT);
                if ($action == 'editsubmission') {
                    if (empty($submission->timestarted) && $this->get_instance()->timelimit) {
                        $submission->timestarted = time();
                        $DB->update_record('assign_submission', $submission);
                    }
                }
            }
            return $submission;
        }
        if ($create) {
            $submission = new stdClass();
            $submission->assignment   = $this->get_instance()->id;
            $submission->userid       = $userid;
            $submission->timecreated = time();
            $submission->timemodified = $submission->timecreated;
            $submission->status = ASSIGN_SUBMISSION_STATUS_NEW;
            if ($attemptnumber >= 0) {
                $submission->attemptnumber = $attemptnumber;
            } else {
                $submission->attemptnumber = 0;
            }
            // Work out if this is the latest submission.
            $submission->latest = 0;
            $params = array('assignment'=>$this->get_instance()->id, 'userid'=>$userid, 'groupid'=>0);
            if ($attemptnumber == -1) {
                // This is a new submission so it must be the latest.
                $submission->latest = 1;
            } else {
                // We need to work this out.
                $result = $DB->get_records('assign_submission', $params, 'attemptnumber DESC', 'attemptnumber', 0, 1);
                $latestsubmission = null;
                if ($result) {
                    $latestsubmission = reset($result);
                }
                if (empty($latestsubmission) || ($attemptnumber > $latestsubmission->attemptnumber)) {
                    $submission->latest = 1;
                }
            }
            if ($submission->latest) {
                // This is the case when we need to set latest to 0 for all the other attempts.
                $DB->set_field('assign_submission', 'latest', 0, $params);
            }
            $sid = $DB->insert_record('assign_submission', $submission);
            return $DB->get_record('assign_submission', array('id' => $sid));
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
            $flags->workflowstate = '';
            $flags->allocatedmarker = 0;

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
        $submission = null;

        $params = array('assignment'=>$this->get_instance()->id, 'userid'=>$userid);
        if ($attemptnumber < 0 || $create) {
            // Make sure this grade matches the latest submission attempt.
            if ($this->get_instance()->teamsubmission) {
                $submission = $this->get_group_submission($userid, 0, true, $attemptnumber);
            } else {
                $submission = $this->get_user_submission($userid, true, $attemptnumber);
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
            // If we are "auto-creating" a grade - and there is a submission
            // the new grade should not have a more recent timemodified value
            // than the submission.
            if ($submission) {
                $grade->timemodified = $submission->timemodified;
            } else {
                $grade->timemodified = $grade->timecreated;
            }
            $grade->grade = -1;
            // Do not set the grader id here as it would be the admin users which is incorrect.
            $grade->grader = -1;
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
     * @param array $args Optional args array (better than pulling args from _GET and _POST)
     * @return string
     */
    protected function view_single_grading_panel($args) {
        global $DB, $CFG;

        $o = '';

        require_once($CFG->dirroot . '/mod/assign/gradeform.php');

        // Need submit permission to submit an assignment.
        require_capability('mod/assign:grade', $this->context);

        // If userid is passed - we are only grading a single student.
        $userid = $args['userid'];
        $attemptnumber = $args['attemptnumber'];
        $instance = $this->get_instance($userid);

        // Apply overrides.
        $this->update_effective_access($userid);

        $rownum = 0;
        $useridlist = array($userid);

        $last = true;
        // This variation on the url will link direct to this student, with no next/previous links.
        // The benefit is the url will be the same every time for this student, so Atto autosave drafts can match up.
        $returnparams = array('userid' => $userid, 'rownum' => 0, 'useridlistid' => 0);
        $this->register_return_link('grade', $returnparams);

        $user = $DB->get_record('user', array('id' => $userid));
        $submission = $this->get_user_submission($userid, false, $attemptnumber);
        $submissiongroup = null;
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
            $submissionlocked = ($flags && $flags->locked);
            $extensionduedate = null;
            if ($flags) {
                $extensionduedate = $flags->extensionduedate;
            }
            $showedit = $this->submissions_open($userid) && ($this->is_any_submission_plugin_enabled());
            $viewfullnames = has_capability('moodle/site:viewfullnames', $this->get_context());
            $usergroups = $this->get_all_groups($user->id);

            $submissionstatus = new assign_submission_status_compact($instance->allowsubmissionsfromdate,
                                                                     $instance->alwaysshowdescription,
                                                                     $submission,
                                                                     $instance->teamsubmission,
                                                                     $teamsubmission,
                                                                     $submissiongroup,
                                                                     $notsubmitted,
                                                                     $this->is_any_submission_plugin_enabled(),
                                                                     $submissionlocked,
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
                                                                     false,
                                                                     $viewfullnames,
                                                                     $extensionduedate,
                                                                     $this->get_context(),
                                                                     $this->is_blind_marking(),
                                                                     '',
                                                                     $instance->attemptreopenmethod,
                                                                     $instance->maxattempts,
                                                                     $this->get_grading_status($userid),
                                                                     $instance->preventsubmissionnotingroup,
                                                                     $usergroups,
                                                                     $instance->timelimit);
            $o .= $this->get_renderer()->render($submissionstatus);
        }

        if ($grade) {
            $data = new stdClass();
            if ($grade->grade !== null && $grade->grade >= 0) {
                $data->grade = format_float($grade->grade, $this->get_grade_item()->get_decimals());
            }
        } else {
            $data = new stdClass();
            $data->grade = '';
        }

        if (!empty($flags->workflowstate)) {
            $data->workflowstate = $flags->workflowstate;
        }
        if (!empty($flags->allocatedmarker)) {
            $data->allocatedmarker = $flags->allocatedmarker;
        }

        // Warning if required.
        $allsubmissions = $this->get_all_submissions($userid);

        if ($attemptnumber != -1 && ($attemptnumber + 1) != count($allsubmissions)) {
            $params = array('attemptnumber' => $attemptnumber + 1,
                            'totalattempts' => count($allsubmissions));
            $message = get_string('editingpreviousfeedbackwarning', 'assign', $params);
            $o .= $this->get_renderer()->notification($message);
        }

        $pagination = array('rownum' => $rownum,
                            'useridlistid' => 0,
                            'last' => $last,
                            'userid' => $userid,
                            'attemptnumber' => $attemptnumber,
                            'gradingpanel' => true);

        if (!empty($args['formdata'])) {
            $data = (array) $data;
            $data = (object) array_merge($data, $args['formdata']);
        }
        $formparams = array($this, $data, $pagination);
        $mform = new mod_assign_grade_form(null,
                                           $formparams,
                                           'post',
                                           '',
                                           array('class' => 'gradeform'));

        if (!empty($args['formdata'])) {
            // If we were passed form data - we want the form to check the data
            // and show errors.
            $mform->is_validated();
        }
        $o .= $this->get_renderer()->heading(get_string('gradenoun'), 3);
        $o .= $this->get_renderer()->render(new assign_form('gradingform', $mform));

        if (count($allsubmissions) > 1) {
            $allgrades = $this->get_all_grades($userid);
            $history = new assign_attempt_history_chooser($allsubmissions,
                                                          $allgrades,
                                                          $this->get_course_module()->id,
                                                          $userid);

            $o .= $this->get_renderer()->render($history);
        }

        \mod_assign\event\grading_form_viewed::create_from_user($this, $user)->trigger();

        return $o;
    }

    /**
     * Print the grading page for a single user submission.
     *
     * @param moodleform $mform
     * @return string
     */
    protected function view_single_grade_page($mform) {
        global $DB, $CFG, $SESSION;

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
        $rownum = optional_param('rownum', 0, PARAM_INT);
        $useridlistid = optional_param('useridlistid', $this->get_useridlist_key_id(), PARAM_ALPHANUM);
        $userid = optional_param('userid', 0, PARAM_INT);
        $attemptnumber = optional_param('attemptnumber', -1, PARAM_INT);

        if (!$userid) {
            $useridlist = $this->get_grading_userid_list(true, $useridlistid);
        } else {
            $rownum = 0;
            $useridlistid = 0;
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
        // This variation on the url will link direct to this student, with no next/previous links.
        // The benefit is the url will be the same every time for this student, so Atto autosave drafts can match up.
        $returnparams = array('userid' => $userid, 'rownum' => 0, 'useridlistid' => 0);
        $this->register_return_link('grade', $returnparams);

        $user = $DB->get_record('user', array('id' => $userid));
        if ($user) {
            $this->update_effective_access($userid);
            $viewfullnames = has_capability('moodle/site:viewfullnames', $this->get_context());
            $usersummary = new assign_user_summary($user,
                                                   $this->get_course()->id,
                                                   $viewfullnames,
                                                   $this->is_blind_marking(),
                                                   $this->get_uniqueid_for_user($user->id),
                                                   // TODO Does not support custom user profile fields (MDL-70456).
                                                   \core_user\fields::get_identity_fields($this->get_context(), false),
                                                   !$this->is_active_user($userid));
            $o .= $this->get_renderer()->render($usersummary);
        }
        $submission = $this->get_user_submission($userid, false, $attemptnumber);
        $submissiongroup = null;
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
            $submissionlocked = ($flags && $flags->locked);
            $extensionduedate = null;
            if ($flags) {
                $extensionduedate = $flags->extensionduedate;
            }
            $showedit = $this->submissions_open($userid) && ($this->is_any_submission_plugin_enabled());
            $viewfullnames = has_capability('moodle/site:viewfullnames', $this->get_context());
            $usergroups = $this->get_all_groups($user->id);
            $submissionstatus = new assign_submission_status($instance->allowsubmissionsfromdate,
                                                             $instance->alwaysshowdescription,
                                                             $submission,
                                                             $instance->teamsubmission,
                                                             $teamsubmission,
                                                             $submissiongroup,
                                                             $notsubmitted,
                                                             $this->is_any_submission_plugin_enabled(),
                                                             $submissionlocked,
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
                                                             false,
                                                             $viewfullnames,
                                                             $extensionduedate,
                                                             $this->get_context(),
                                                             $this->is_blind_marking(),
                                                             '',
                                                             $instance->attemptreopenmethod,
                                                             $instance->maxattempts,
                                                             $this->get_grading_status($userid),
                                                             $instance->preventsubmissionnotingroup,
                                                             $usergroups,
                                                             $instance->timelimit);
            $o .= $this->get_renderer()->render($submissionstatus);
        }

        if ($grade) {
            $data = new stdClass();
            if ($grade->grade !== null && $grade->grade >= 0) {
                $data->grade = format_float($grade->grade, $this->get_grade_item()->get_decimals());
            }
        } else {
            $data = new stdClass();
            $data->grade = '';
        }

        if (!empty($flags->workflowstate)) {
            $data->workflowstate = $flags->workflowstate;
        }
        if (!empty($flags->allocatedmarker)) {
            $data->allocatedmarker = $flags->allocatedmarker;
        }

        // Warning if required.
        $allsubmissions = $this->get_all_submissions($userid);

        if ($attemptnumber != -1 && ($attemptnumber + 1) != count($allsubmissions)) {
            $params = array('attemptnumber'=>$attemptnumber + 1,
                            'totalattempts'=>count($allsubmissions));
            $message = get_string('editingpreviousfeedbackwarning', 'assign', $params);
            $o .= $this->get_renderer()->notification($message);
        }

        // Now show the grading form.
        if (!$mform) {
            $pagination = array('rownum' => $rownum,
                                'useridlistid' => $useridlistid,
                                'last' => $last,
                                'userid' => $userid,
                                'attemptnumber' => $attemptnumber);
            $formparams = array($this, $data, $pagination);
            $mform = new mod_assign_grade_form(null,
                                               $formparams,
                                               'post',
                                               '',
                                               array('class'=>'gradeform'));
        }
        $o .= $this->get_renderer()->heading(get_string('gradenoun'), 3);
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
                                                  true,
                                                  $useridlistid,
                                                  $rownum);

            $o .= $this->get_renderer()->render($history);
        }

        \mod_assign\event\grading_form_viewed::create_from_user($this, $user)->trigger();

        $o .= $this->view_footer();
        return $o;
    }

    /**
     * Show a confirmation page to make sure they want to remove submission data.
     *
     * @return string
     */
    protected function view_remove_submission_confirm() {
        global $USER;

        $userid = optional_param('userid', $USER->id, PARAM_INT);

        if (!$this->can_edit_submission($userid, $USER->id)) {
            print_error('nopermission');
        }
        $user = core_user::get_user($userid, '*', MUST_EXIST);

        $o = '';
        $header = new assign_header($this->get_instance(),
                                    $this->get_context(),
                                    false,
                                    $this->get_course_module()->id);
        $o .= $this->get_renderer()->render($header);

        $urlparams = array('id' => $this->get_course_module()->id,
                           'action' => 'removesubmission',
                           'userid' => $userid,
                           'sesskey' => sesskey());
        $confirmurl = new moodle_url('/mod/assign/view.php', $urlparams);

        $urlparams = array('id' => $this->get_course_module()->id,
                           'action' => 'view');
        $cancelurl = new moodle_url('/mod/assign/view.php', $urlparams);

        if ($userid == $USER->id) {
            if ($this->is_time_limit_enabled($userid)) {
                $confirmstr = get_string('removesubmissionconfirmwithtimelimit', 'assign');
            } else {
                $confirmstr = get_string('removesubmissionconfirm', 'assign');
            }
        } else {
            if ($this->is_time_limit_enabled($userid)) {
                $confirmstr = get_string('removesubmissionconfirmforstudentwithtimelimit', 'assign', $this->fullname($user));
            } else {
                $confirmstr = get_string('removesubmissionconfirmforstudent', 'assign', $this->fullname($user));
            }
        }
        $o .= $this->get_renderer()->confirm($confirmstr,
                                             $confirmurl,
                                             $cancelurl);
        $o .= $this->view_footer();

        \mod_assign\event\remove_submission_form_viewed::create_from_user($this, $user)->trigger();

        return $o;
    }


    /**
     * Show a confirmation page to make sure they want to release student identities.
     *
     * @return string
     */
    protected function view_reveal_identities_confirm() {
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

        \mod_assign\event\reveal_identities_confirmation_page_viewed::create_from_assign($this)->trigger();

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
        global $USER, $CFG, $SESSION, $PAGE;

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

        // Sort links alphabetically based on the link description.
        core_collator::asort($links);

        $gradingactions = new url_select($links);
        $gradingactions->set_label(get_string('choosegradingaction', 'assign'));
        $gradingactions->class .= ' mb-1';

        $gradingmanager = get_grading_manager($this->get_context(), 'mod_assign', 'submissions');

        $perpage = $this->get_assign_perpage();
        $filter = get_user_preferences('assign_filter', '');
        $markerfilter = get_user_preferences('assign_markerfilter', '');
        $workflowfilter = get_user_preferences('assign_workflowfilter', '');
        $controller = $gradingmanager->get_active_controller();
        $showquickgrading = empty($controller) && $this->can_grade();
        $quickgrading = get_user_preferences('assign_quickgrading', false);
        $showonlyactiveenrolopt = has_capability('moodle/course:viewsuspendedusers', $this->context);
        $downloadasfolders = get_user_preferences('assign_downloadasfolders', 1);

        $markingallocation = $this->get_instance()->markingworkflow &&
            $this->get_instance()->markingallocation &&
            has_capability('mod/assign:manageallocations', $this->context);
        // Get markers to use in drop lists.
        $markingallocationoptions = array();
        if ($markingallocation) {
            list($sort, $params) = users_order_by_sql('u');
            // Only enrolled users could be assigned as potential markers.
            $markers = get_enrolled_users($this->context, 'mod/assign:grade', 0, 'u.*', $sort);
            $markingallocationoptions[''] = get_string('filternone', 'assign');
            $markingallocationoptions[ASSIGN_MARKER_FILTER_NO_MARKER] = get_string('markerfilternomarker', 'assign');
            $viewfullnames = has_capability('moodle/site:viewfullnames', $this->context);
            foreach ($markers as $marker) {
                $markingallocationoptions[$marker->id] = fullname($marker, $viewfullnames);
            }
        }

        $markingworkflow = $this->get_instance()->markingworkflow;
        // Get marking states to show in form.
        $markingworkflowoptions = $this->get_marking_workflow_filters();

        // Print options for changing the filter and changing the number of results per page.
        $gradingoptionsformparams = array('cm'=>$cmid,
                                          'contextid'=>$this->context->id,
                                          'userid'=>$USER->id,
                                          'submissionsenabled'=>$this->is_any_submission_plugin_enabled(),
                                          'showquickgrading'=>$showquickgrading,
                                          'quickgrading'=>$quickgrading,
                                          'markingworkflowopt'=>$markingworkflowoptions,
                                          'markingallocationopt'=>$markingallocationoptions,
                                          'showonlyactiveenrolopt'=>$showonlyactiveenrolopt,
                                          'showonlyactiveenrol' => $this->show_only_active_users(),
                                          'downloadasfolders' => $downloadasfolders);

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
                                 'context'=>$this->get_context(),
                                 'markingworkflow'=>$markingworkflow,
                                 'markingallocation'=>$markingallocation);
        $classoptions = array('class'=>'gradingbatchoperationsform');

        $gradingbatchoperationsform = new mod_assign_grading_batch_operations_form(null,
                                                                                   $batchformparams,
                                                                                   'post',
                                                                                   '',
                                                                                   $classoptions);

        $gradingoptionsdata = new stdClass();
        $gradingoptionsdata->perpage = $perpage;
        $gradingoptionsdata->filter = $filter;
        $gradingoptionsdata->markerfilter = $markerfilter;
        $gradingoptionsdata->workflowfilter = $workflowfilter;
        $gradingoptionsform->set_data($gradingoptionsdata);

        $buttons = new \mod_assign\output\grading_actionmenu($this->get_course_module()->id,
             $this->is_any_submission_plugin_enabled(), $this->count_submissions());
        $actionformtext = $this->get_renderer()->render($buttons);
        $PAGE->activityheader->set_attrs(['hidecompletion' => true]);

        $currenturl = new moodle_url('/mod/assign/view.php', ['id' => $this->get_course_module()->id, 'action' => 'grading']);

        $header = new assign_header($this->get_instance(),
                                    $this->get_context(),
                                    false,
                                    $this->get_course_module()->id,
                                    get_string('grading', 'assign'),
                                    '',
                                    '',
                                    $currenturl);
        $o .= $this->get_renderer()->render($header);

        $o .= $actionformtext;

        $o .= $this->get_renderer()->heading(get_string('gradeitem:submissions', 'mod_assign'), 2);
        $o .= $this->get_renderer()->render($gradingactions);

        $o .= groups_print_activity_menu($this->get_course_module(), $currenturl, true);

        // Plagiarism update status apearring in the grading book.
        if (!empty($CFG->enableplagiarism)) {
            require_once($CFG->libdir . '/plagiarismlib.php');
            $o .= plagiarism_update_status($this->get_course(), $this->get_course_module());
        }

        if ($this->is_blind_marking() && has_capability('mod/assign:viewblinddetails', $this->get_context())) {
            $o .= $this->get_renderer()->notification(get_string('blindmarkingenabledwarning', 'assign'), 'notifymessage');
        }

        // Load and print the table of submissions.
        if ($showquickgrading && $quickgrading) {
            $gradingtable = new assign_grading_table($this, $perpage, $filter, 0, true);
            $table = $this->get_renderer()->render($gradingtable);
            $page = optional_param('page', null, PARAM_INT);
            $quickformparams = array('cm'=>$this->get_course_module()->id,
                                     'gradingtable'=>$table,
                                     'sendstudentnotifications' => $this->get_instance()->sendstudentnotifications,
                                     'page' => $page);
            $quickgradingform = new mod_assign_quick_grading_form(null, $quickformparams);

            $o .= $this->get_renderer()->render(new assign_form('quickgradingform', $quickgradingform));
        } else {
            $gradingtable = new assign_grading_table($this, $perpage, $filter, 0, false);
            $o .= $this->get_renderer()->render($gradingtable);
        }

        if ($this->can_grade()) {
            // We need to store the order of uses in the table as the person may wish to grade them.
            // This is done based on the row number of the user.
            $useridlist = $gradingtable->get_column_data('userid');
            $SESSION->mod_assign_useridlist[$this->get_useridlist_key()] = $useridlist;
        }

        $currentgroup = groups_get_activity_group($this->get_course_module(), true);
        $users = array_keys($this->list_participants($currentgroup, true));
        if (count($users) != 0 && $this->can_grade()) {
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
     * View entire grader app.
     *
     * @return string
     */
    protected function view_grader() {
        global $USER, $PAGE;

        $o = '';
        // Need submit permission to submit an assignment.
        $this->require_view_grades();

        $PAGE->set_pagelayout('embedded');

        $PAGE->activityheader->disable();

        $courseshortname = $this->get_context()->get_course_context()->get_context_name(false, true);
        $args = [
            'contextname' => $this->get_context()->get_context_name(false, true),
            'subpage' => get_string('grading', 'assign')
        ];
        $title = get_string('subpagetitle', 'assign', $args);
        $title = $courseshortname . ': ' . $title;
        $PAGE->set_title($title);

        $o .= $this->get_renderer()->header();

        $userid = optional_param('userid', 0, PARAM_INT);
        $blindid = optional_param('blindid', 0, PARAM_INT);

        if (!$userid && $blindid) {
            $userid = $this->get_user_id_for_uniqueid($blindid);
        }

        $currentgroup = groups_get_activity_group($this->get_course_module(), true);
        $framegrader = new grading_app($userid, $currentgroup, $this);

        $this->update_effective_access($userid);

        $o .= $this->get_renderer()->render($framegrader);

        $o .= $this->view_footer();

        \mod_assign\event\grading_table_viewed::create_from_assign($this)->trigger();

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
        $this->require_view_grades();
        require_once($CFG->dirroot . '/mod/assign/gradeform.php');

        $this->add_grade_notices();

        // Only load this if it is.
        $o .= $this->view_grading_table();

        $o .= $this->view_footer();

        \mod_assign\event\grading_table_viewed::create_from_assign($this)->trigger();

        return $o;
    }

    /**
     * Capture the output of the plagiarism plugins disclosures and return it as a string.
     *
     * @return string
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
     * @param string $title The page title
     * @param array $notices The array of notices to show.
     * @return string
     */
    protected function view_notices($title, $notices) {
        global $CFG;

        $o = '';

        $header = new assign_header($this->get_instance(),
                                    $this->get_context(),
                                    $this->show_intro(),
                                    $this->get_course_module()->id,
                                    $title);
        $o .= $this->get_renderer()->render($header);

        foreach ($notices as $notice) {
            $o .= $this->get_renderer()->notification($notice);
        }

        $url = new moodle_url('/mod/assign/view.php', array('id'=>$this->get_course_module()->id, 'action'=>'view'));
        $o .= $this->get_renderer()->continue_button($url);

        $o .= $this->view_footer();

        return $o;
    }

    /**
     * Get the name for a user - hiding their real name if blind marking is on.
     *
     * @param stdClass $user The user record as required by fullname()
     * @return string The name.
     */
    public function fullname($user) {
        if ($this->is_blind_marking()) {
            $hasviewblind = has_capability('mod/assign:viewblinddetails', $this->get_context());
            if (empty($user->recordid)) {
                $uniqueid = $this->get_uniqueid_for_user($user->id);
            } else {
                $uniqueid = $user->recordid;
            }
            if ($hasviewblind) {
                return get_string('participant', 'assign') . ' ' . $uniqueid . ' (' .
                        fullname($user, has_capability('moodle/site:viewfullnames', $this->get_context())) . ')';
            } else {
                return get_string('participant', 'assign') . ' ' . $uniqueid;
            }
        } else {
            return fullname($user, has_capability('moodle/site:viewfullnames', $this->get_context()));
        }
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
        global $CFG, $USER, $DB, $PAGE;

        $o = '';
        require_once($CFG->dirroot . '/mod/assign/submission_form.php');
        // Need submit permission to submit an assignment.
        $userid = optional_param('userid', $USER->id, PARAM_INT);
        $user = $DB->get_record('user', array('id'=>$userid), '*', MUST_EXIST);
        $timelimitenabled = get_config('assign', 'enabletimelimit');

        // This variation on the url will link direct to this student.
        // The benefit is the url will be the same every time for this student, so Atto autosave drafts can match up.
        $returnparams = array('userid' => $userid, 'rownum' => 0, 'useridlistid' => 0);
        $this->register_return_link('editsubmission', $returnparams);

        if ($userid == $USER->id) {
            if (!$this->can_edit_submission($userid, $USER->id)) {
                print_error('nopermission');
            }
            // User is editing their own submission.
            require_capability('mod/assign:submit', $this->context);
            $title = get_string('editsubmission', 'assign');
        } else {
            // User is editing another user's submission.
            if (!$this->can_edit_submission($userid, $USER->id)) {
                print_error('nopermission');
            }

            $name = $this->fullname($user);
            $title = get_string('editsubmissionother', 'assign', $name);
        }

        if (!$this->submissions_open($userid)) {
            $message = array(get_string('submissionsclosed', 'assign'));
            return $this->view_notices($title, $message);
        }

        $postfix = '';
        if ($this->has_visible_attachments()) {
            $postfix = $this->render_area_files('mod_assign', ASSIGN_INTROATTACHMENT_FILEAREA, 0);
        }

        $data = new stdClass();
        $data->userid = $userid;
        if (!$mform) {
            $mform = new mod_assign_submission_form(null, array($this, $data));
        }

        if ($this->get_instance()->teamsubmission) {
            $submission = $this->get_group_submission($userid, 0, false);
        } else {
            $submission = $this->get_user_submission($userid, false);
        }

        if ($timelimitenabled && !empty($submission->timestarted) && $this->get_instance()->timelimit) {
            $navbc = $this->get_timelimit_panel($submission);
            $regions = $PAGE->blocks->get_regions();
            $bc = new \block_contents();
            $bc->attributes['id'] = 'mod_assign_timelimit_block';
            $bc->attributes['role'] = 'navigation';
            $bc->attributes['aria-labelledby'] = 'mod_assign_timelimit_block_title';
            $bc->title = get_string('assigntimeleft', 'assign');
            $bc->content = $navbc;
            $PAGE->blocks->add_fake_block($bc, reset($regions));
        }

        $o .= $this->get_renderer()->render(
            new assign_header($this->get_instance(),
                              $this->get_context(),
                              $this->show_intro(),
                              $this->get_course_module()->id,
                              $title,
                              '',
                              $postfix,
                              null,
                              true
            )
        );

        // Show plagiarism disclosure for any user submitter.
        $o .= $this->plagiarism_print_disclosure();

        foreach ($notices as $notice) {
            $o .= $this->get_renderer()->notification($notice);
        }

        $o .= $this->get_renderer()->render(new assign_form('editsubmissionform', $mform));
        $o .= $this->view_footer();

        \mod_assign\event\submission_form_viewed::create_from_user($this, $user)->trigger();

        return $o;
    }

    /**
     * Get the time limit panel object for this submission attempt.
     *
     * @param stdClass $submission assign submission.
     * @return string the panel output.
     */
    public function get_timelimit_panel(stdClass $submission): string {
        global $USER;

        // Apply overrides.
        $this->update_effective_access($USER->id);
        $panel = new timelimit_panel($submission, $this->get_instance());
        return $this->get_renderer()->render($panel);
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
     * Perform an access check to see if the current $USER can edit this group submission.
     *
     * @param int $groupid
     * @return bool
     */
    public function can_edit_group_submission($groupid) {
        global $USER;

        $members = $this->get_submission_group_members($groupid, true);
        foreach ($members as $member) {
            // If we can edit any members submission, we can edit the submission for the group.
            if ($this->can_edit_submission($member->id)) {
                return true;
            }
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

        $members = $this->get_submission_group_members($groupid, true);
        foreach ($members as $member) {
            // If we can view any members submission, we can view the submission for the group.
            if ($this->can_view_submission($member->id)) {
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

        if (!$this->is_active_user($userid) && !has_capability('moodle/course:viewsuspendedusers', $this->context)) {
            return false;
        }
        if (!is_enrolled($this->get_course_context(), $userid)) {
            return false;
        }
        if (has_any_capability(array('mod/assign:viewgrades', 'mod/assign:grade'), $this->context)) {
            return true;
        }
        if ($userid == $USER->id) {
            return true;
        }
        return false;
    }

    /**
     * Allows the plugin to show a batch grading operation page.
     *
     * @param moodleform $mform
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

        $markingallocation = $this->get_instance()->markingworkflow &&
            $this->get_instance()->markingallocation &&
            has_capability('mod/assign:manageallocations', $this->context);

        $batchformparams = array('cm'=>$this->get_course_module()->id,
                                 'submissiondrafts'=>$this->get_instance()->submissiondrafts,
                                 'duedate'=>$this->get_instance()->duedate,
                                 'attemptreopenmethod'=>$this->get_instance()->attemptreopenmethod,
                                 'feedbackplugins'=>$this->get_feedback_plugins(),
                                 'context'=>$this->get_context(),
                                 'markingworkflow'=>$this->get_instance()->markingworkflow,
                                 'markingallocation'=>$markingallocation);
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
            } else if ($data->operation == 'setmarkingworkflowstate') {
                return 'viewbatchsetmarkingworkflowstate';
            } else if ($data->operation == 'setmarkingallocation') {
                return 'viewbatchmarkingallocation';
            } else if (strpos($data->operation, $prefix) === 0) {
                $tail = substr($data->operation, strlen($prefix));
                list($plugintype, $action) = explode('_', $tail, 2);

                $plugin = $this->get_feedback_plugin_by_type($plugintype);
                if ($plugin) {
                    return 'plugingradingbatchoperation';
                }
            }

            if ($data->operation == 'downloadselected') {
                $this->download_submissions($userlist);
            } else {
                foreach ($userlist as $userid) {
                    if ($data->operation == 'lock') {
                        $this->process_lock_submission($userid);
                    } else if ($data->operation == 'unlock') {
                        $this->process_unlock_submission($userid);
                    } else if ($data->operation == 'reverttodraft') {
                        $this->process_revert_to_draft($userid);
                    } else if ($data->operation == 'removesubmission') {
                        $this->process_remove_submission($userid);
                    } else if ($data->operation == 'addattempt') {
                        if (!$this->get_instance()->teamsubmission) {
                            $this->process_add_attempt($userid);
                        }
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
     * Shows a form that allows the workflow state for selected submissions to be changed.
     *
     * @param moodleform $mform Set to a grading batch operations form
     * @return string - the page to view after processing these actions
     */
    protected function view_batch_set_workflow_state($mform) {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/mod/assign/batchsetmarkingworkflowstateform.php');

        $o = '';

        $submitteddata = $mform->get_data();
        $users = $submitteddata->selectedusers;
        $userlist = explode(',', $users);

        $formdata = array('id' => $this->get_course_module()->id,
                          'selectedusers' => $users);

        $usershtml = '';

        $usercount = 0;
        // TODO Does not support custom user profile fields (MDL-70456).
        $extrauserfields = \core_user\fields::get_identity_fields($this->get_context(), false);
        $viewfullnames = has_capability('moodle/site:viewfullnames', $this->get_context());
        foreach ($userlist as $userid) {
            if ($usercount >= 5) {
                $usershtml .= get_string('moreusers', 'assign', count($userlist) - 5);
                break;
            }
            $user = $DB->get_record('user', array('id'=>$userid), '*', MUST_EXIST);

            $usershtml .= $this->get_renderer()->render(new assign_user_summary($user,
                                                                $this->get_course()->id,
                                                                $viewfullnames,
                                                                $this->is_blind_marking(),
                                                                $this->get_uniqueid_for_user($user->id),
                                                                $extrauserfields,
                                                                !$this->is_active_user($userid)));
            $usercount += 1;
        }

        $formparams = array(
            'userscount' => count($userlist),
            'usershtml' => $usershtml,
            'markingworkflowstates' => $this->get_marking_workflow_states_for_current_user()
        );

        $mform = new mod_assign_batch_set_marking_workflow_state_form(null, $formparams);
        $mform->set_data($formdata);    // Initialises the hidden elements.
        $header = new assign_header($this->get_instance(),
            $this->get_context(),
            $this->show_intro(),
            $this->get_course_module()->id,
            get_string('setmarkingworkflowstate', 'assign'));
        $o .= $this->get_renderer()->render($header);
        $o .= $this->get_renderer()->render(new assign_form('setworkflowstate', $mform));
        $o .= $this->view_footer();

        \mod_assign\event\batch_set_workflow_state_viewed::create_from_assign($this)->trigger();

        return $o;
    }

    /**
     * Shows a form that allows the allocated marker for selected submissions to be changed.
     *
     * @param moodleform $mform Set to a grading batch operations form
     * @return string - the page to view after processing these actions
     */
    public function view_batch_markingallocation($mform) {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/mod/assign/batchsetallocatedmarkerform.php');

        $o = '';

        $submitteddata = $mform->get_data();
        $users = $submitteddata->selectedusers;
        $userlist = explode(',', $users);

        $formdata = array('id' => $this->get_course_module()->id,
                          'selectedusers' => $users);

        $usershtml = '';

        $usercount = 0;
        // TODO Does not support custom user profile fields (MDL-70456).
        $extrauserfields = \core_user\fields::get_identity_fields($this->get_context(), false);
        $viewfullnames = has_capability('moodle/site:viewfullnames', $this->get_context());
        foreach ($userlist as $userid) {
            if ($usercount >= 5) {
                $usershtml .= get_string('moreusers', 'assign', count($userlist) - 5);
                break;
            }
            $user = $DB->get_record('user', array('id'=>$userid), '*', MUST_EXIST);

            $usershtml .= $this->get_renderer()->render(new assign_user_summary($user,
                $this->get_course()->id,
                $viewfullnames,
                $this->is_blind_marking(),
                $this->get_uniqueid_for_user($user->id),
                $extrauserfields,
                !$this->is_active_user($userid)));
            $usercount += 1;
        }

        $formparams = array(
            'userscount' => count($userlist),
            'usershtml' => $usershtml,
        );

        list($sort, $params) = users_order_by_sql('u');
        // Only enrolled users could be assigned as potential markers.
        $markers = get_enrolled_users($this->get_context(), 'mod/assign:grade', 0, 'u.*', $sort);
        $markerlist = array();
        foreach ($markers as $marker) {
            $markerlist[$marker->id] = fullname($marker);
        }

        $formparams['markers'] = $markerlist;

        $mform = new mod_assign_batch_set_allocatedmarker_form(null, $formparams);
        $mform->set_data($formdata);    // Initialises the hidden elements.
        $header = new assign_header($this->get_instance(),
            $this->get_context(),
            $this->show_intro(),
            $this->get_course_module()->id,
            get_string('setmarkingallocation', 'assign'));
        $o .= $this->get_renderer()->render($header);
        $o .= $this->get_renderer()->render(new assign_form('setworkflowstate', $mform));
        $o .= $this->view_footer();

        \mod_assign\event\batch_set_marker_allocation_viewed::create_from_assign($this)->trigger();

        return $o;
    }

    /**
     * Ask the user to confirm they want to submit their work for grading.
     *
     * @param moodleform $mform - null unless form validation has failed
     * @return string
     */
    protected function check_submit_for_grading($mform) {
        global $USER, $CFG;

        require_once($CFG->dirroot . '/mod/assign/submissionconfirmform.php');

        // Check that all of the submission plugins are ready for this submission.
        // Also check whether there is something to be submitted as well against atleast one.
        $notifications = array();
        $submission = $this->get_user_submission($USER->id, false);
        if ($this->get_instance()->teamsubmission) {
            $submission = $this->get_group_submission($USER->id, 0, false);
        }

        $plugins = $this->get_submission_plugins();
        $hassubmission = false;
        foreach ($plugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {
                $check = $plugin->precheck_submission($submission);
                if ($check !== true) {
                    $notifications[] = $check;
                }

                if (is_object($submission) && !$plugin->is_empty($submission)) {
                    $hassubmission = true;
                }
            }
        }

        // If there are no submissions and no existing notifications to be displayed the stop.
        if (!$hassubmission && !$notifications) {
            $notifications[] = get_string('addsubmission_help', 'assign');
        }

        $data = new stdClass();
        $adminconfig = $this->get_admin_config();
        $requiresubmissionstatement = $this->get_instance()->requiresubmissionstatement;
        $submissionstatement = '';

        if ($requiresubmissionstatement) {
            $submissionstatement = $this->get_submissionstatement($adminconfig, $this->get_instance(), $this->get_context());
        }

        // If we get back an empty submission statement, we have to set $requiredsubmisisonstatement to false to prevent
        // that the submission statement checkbox will be displayed.
        if (empty($submissionstatement)) {
            $requiresubmissionstatement = false;
        }

        if ($mform == null) {
            $mform = new mod_assign_confirm_submission_form(null, array($requiresubmissionstatement,
                                                                        $submissionstatement,
                                                                        $this->get_course_module()->id,
                                                                        $data));
        }
        $o = '';
        $o .= $this->get_renderer()->render(new assign_header($this->get_instance(),
                                                              $this->get_context(),
                                                              $this->show_intro(),
                                                              $this->get_course_module()->id,
                                                              get_string('confirmsubmissionheading', 'assign')));
        $submitforgradingpage = new assign_submit_for_grading_page($notifications,
                                                                   $this->get_course_module()->id,
                                                                   $mform);
        $o .= $this->get_renderer()->render($submitforgradingpage);
        $o .= $this->view_footer();

        \mod_assign\event\submission_confirmation_form_viewed::create_from_assign($this)->trigger();

        return $o;
    }

    /**
     * Creates an assign_submission_status renderable.
     *
     * @param stdClass $user the user to get the report for
     * @param bool $showlinks return plain text or links to the profile
     * @return assign_submission_status renderable object
     */
    public function get_assign_submission_status_renderable($user, $showlinks) {
        global $PAGE;

        $instance = $this->get_instance();
        $flags = $this->get_user_flags($user->id, false);
        $submission = $this->get_user_submission($user->id, false);

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

        $showedit = $showlinks &&
                    ($this->is_any_submission_plugin_enabled()) &&
                    $this->can_edit_submission($user->id);

        $submissionlocked = ($flags && $flags->locked);

        // Grading criteria preview.
        $gradingmanager = get_grading_manager($this->context, 'mod_assign', 'submissions');
        $gradingcontrollerpreview = '';
        if ($gradingmethod = $gradingmanager->get_active_method()) {
            $controller = $gradingmanager->get_controller($gradingmethod);
            if ($controller->is_form_defined()) {
                $gradingcontrollerpreview = $controller->render_preview($PAGE);
            }
        }

        $showsubmit = ($showlinks && $this->submissions_open($user->id));
        $showsubmit = ($showsubmit && $this->show_submit_button($submission, $teamsubmission, $user->id));

        $extensionduedate = null;
        if ($flags) {
            $extensionduedate = $flags->extensionduedate;
        }
        $viewfullnames = has_capability('moodle/site:viewfullnames', $this->get_context());

        $gradingstatus = $this->get_grading_status($user->id);
        $usergroups = $this->get_all_groups($user->id);
        $submissionstatus = new assign_submission_status($instance->allowsubmissionsfromdate,
                                                          $instance->alwaysshowdescription,
                                                          $submission,
                                                          $instance->teamsubmission,
                                                          $teamsubmission,
                                                          $submissiongroup,
                                                          $notsubmitted,
                                                          $this->is_any_submission_plugin_enabled(),
                                                          $submissionlocked,
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
                                                          $instance->maxattempts,
                                                          $gradingstatus,
                                                          $instance->preventsubmissionnotingroup,
                                                          $usergroups,
                                                          $instance->timelimit);
        return $submissionstatus;
    }


    /**
     * Creates an assign_feedback_status renderable.
     *
     * @param stdClass $user the user to get the report for
     * @return assign_feedback_status renderable object
     */
    public function get_assign_feedback_status_renderable($user) {
        global $CFG, $DB, $PAGE;

        require_once($CFG->libdir.'/gradelib.php');
        require_once($CFG->dirroot.'/grade/grading/lib.php');

        $instance = $this->get_instance();
        $grade = $this->get_user_grade($user->id, false);
        $gradingstatus = $this->get_grading_status($user->id);

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

        if ($this->get_instance()->markingworkflow && $gradingstatus != ASSIGN_MARKING_WORKFLOW_STATE_RELEASED) {
            $emptyplugins = true; // Don't show feedback plugins until released either.
        }

        $cangrade = has_capability('mod/assign:grade', $this->get_context());
        $hasgrade = $this->get_instance()->grade != GRADE_TYPE_NONE &&
                        !is_null($gradebookgrade) && !is_null($gradebookgrade->grade);
        $gradevisible = $cangrade || $this->get_instance()->grade == GRADE_TYPE_NONE ||
                        (!is_null($gradebookgrade) && !$gradebookgrade->hidden);
        // If there is a visible grade, show the summary.
        if (($hasgrade || !$emptyplugins) && $gradevisible) {

            $gradefordisplay = null;
            $gradeddate = null;
            $grader = null;
            $gradingmanager = get_grading_manager($this->get_context(), 'mod_assign', 'submissions');

            if ($hasgrade) {
                if ($controller = $gradingmanager->get_active_controller()) {
                    $menu = make_grades_menu($this->get_instance()->grade);
                    $controller->set_grade_range($menu, $this->get_instance()->grade > 0);
                    $gradefordisplay = $controller->render_grade($PAGE,
                                                                 $grade->id,
                                                                 $gradingitem,
                                                                 $gradebookgrade->str_long_grade,
                                                                 $cangrade);
                } else {
                    $gradefordisplay = $this->display_grade($gradebookgrade->grade, false);
                }
                $gradeddate = $gradebookgrade->dategraded;

                // Only display the grader if it is in the right state.
                if (in_array($gradingstatus, [ASSIGN_GRADING_STATUS_GRADED, ASSIGN_MARKING_WORKFLOW_STATE_RELEASED])){
                    if (isset($grade->grader) && $grade->grader > 0) {
                        $grader = $DB->get_record('user', array('id' => $grade->grader));
                    } else if (isset($gradebookgrade->usermodified)
                        && $gradebookgrade->usermodified > 0
                        && has_capability('mod/assign:grade', $this->get_context(), $gradebookgrade->usermodified)) {
                        // Grader not provided. Check that usermodified is a user who can grade.
                        // Case 1: When an assignment is reopened an empty assign_grade is created so the feedback
                        // plugin can know which attempt it's referring to. In this case, usermodifed is a student.
                        // Case 2: When an assignment's grade is overrided via the gradebook, usermodified is a grader
                        $grader = $DB->get_record('user', array('id' => $gradebookgrade->usermodified));
                    }
                }
            }

            $viewfullnames = has_capability('moodle/site:viewfullnames', $this->get_context());

            if ($grade) {
                \mod_assign\event\feedback_viewed::create_from_grade($this, $grade)->trigger();
            }
            $feedbackstatus = new assign_feedback_status($gradefordisplay,
                                                  $gradeddate,
                                                  $grader,
                                                  $this->get_feedback_plugins(),
                                                  $grade,
                                                  $this->get_course_module()->id,
                                                  $this->get_return_action(),
                                                  $this->get_return_params(),
                                                  $viewfullnames);

            // Show the grader's identity if 'Hide Grader' is disabled or has the 'Show Hidden Grader' capability.
            $showgradername = (
                    has_capability('mod/assign:showhiddengrader', $this->context) or
                    !$this->is_hidden_grader()
            );

            if (!$showgradername) {
                $feedbackstatus->grader = false;
            }

            return $feedbackstatus;
        }
        return;
    }

    /**
     * Creates an assign_attempt_history renderable.
     *
     * @param stdClass $user the user to get the report for
     * @return assign_attempt_history renderable object
     */
    public function get_assign_attempt_history_renderable($user) {

        $allsubmissions = $this->get_all_submissions($user->id);
        $allgrades = $this->get_all_grades($user->id);

        $history = new assign_attempt_history($allsubmissions,
                                              $allgrades,
                                              $this->get_submission_plugins(),
                                              $this->get_feedback_plugins(),
                                              $this->get_course_module()->id,
                                              $this->get_return_action(),
                                              $this->get_return_params(),
                                              false,
                                              0,
                                              0);
        return $history;
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

        $o = '';

        if ($this->can_view_submission($user->id)) {
            if (has_capability('mod/assign:viewownsubmissionsummary', $this->get_context(), $user, false)) {
                // The user can view the submission summary.
                $submissionstatus = $this->get_assign_submission_status_renderable($user, $showlinks);
                $o .= $this->get_renderer()->render($submissionstatus);
            }

            // If there is a visible grade, show the feedback.
            $feedbackstatus = $this->get_assign_feedback_status_renderable($user);
            if ($feedbackstatus) {
                $o .= $this->get_renderer()->render($feedbackstatus);
            }

            // If there is more than one submission, show the history.
            $history = $this->get_assign_attempt_history_renderable($user);
            if (count($history->submissions) > 1) {
                $o .= $this->get_renderer()->render($history);
            }
        }
        return $o;
    }

    /**
     * Returns true if the submit subsission button should be shown to the user.
     *
     * @param stdClass $submission The users own submission record.
     * @param stdClass $teamsubmission The users team submission record if there is one
     * @param int $userid The user
     * @return bool
     */
    protected function show_submit_button($submission = null, $teamsubmission = null, $userid = null) {
        if (!has_capability('mod/assign:submit', $this->get_context(), $userid, false)) {
            // The user does not have the capability to submit.
            return false;
        }
        if ($teamsubmission) {
            if ($teamsubmission->status === ASSIGN_SUBMISSION_STATUS_SUBMITTED) {
                // The assignment submission has been completed.
                return false;
            } else if ($this->submission_empty($teamsubmission)) {
                // There is nothing to submit yet.
                return false;
            } else if ($submission && $submission->status === ASSIGN_SUBMISSION_STATUS_SUBMITTED) {
                // The user has already clicked the submit button on the team submission.
                return false;
            } else if (
                !empty($this->get_instance()->preventsubmissionnotingroup)
                && $this->get_submission_group($userid) == false
            ) {
                return false;
            }
        } else if ($submission) {
            if ($submission->status === ASSIGN_SUBMISSION_STATUS_SUBMITTED) {
                // The assignment submission has been completed.
                return false;
            } else if ($this->submission_empty($submission)) {
                // There is nothing to submit.
                return false;
            }
        } else {
            // We've not got a valid submission or team submission.
            return false;
        }
        // Last check is that this instance allows drafts.
        return $this->get_instance()->submissiondrafts;
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

        // Show the grader's identity if 'Hide Grader' is disabled or has the 'Show Hidden Grader' capability.
        $showgradername = (
            has_capability('mod/assign:showhiddengrader', $this->context, $userid) or
            !$this->is_hidden_grader()
        );

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
            if (!$showgradername) {
                $grade->grader = null;
            } else if (isset($gradercache[$grade->grader])) {
                $grade->grader = $gradercache[$grade->grader];
            } else if ($grade->grader > 0) {
                // Not in cache - need to load the grader record.
                $grade->grader = $DB->get_record('user', array('id'=>$grade->grader));
                if ($grade->grader) {
                    $gradercache[$grade->grader->id] = $grade->grader;
                }
            }

            // Now get the gradefordisplay.
            if ($controller) {
                $controller->set_grade_range(make_grades_menu($this->get_instance()->grade), $this->get_instance()->grade > 0);
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
    public function get_all_submissions($userid) {
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
     * Creates an assign_grading_summary renderable.
     *
     * @param mixed $activitygroup int|null the group for calculating the grading summary (if null the function will determine it)
     * @return assign_grading_summary renderable object
     */
    public function get_assign_grading_summary_renderable($activitygroup = null) {

        $instance = $this->get_default_instance(); // Grading summary requires the raw dates, regardless of relativedates mode.
        $cm = $this->get_course_module();
        $course = $this->get_course();

        $draft = ASSIGN_SUBMISSION_STATUS_DRAFT;
        $submitted = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        $isvisible = $cm->visible;

        if ($activitygroup === null) {
            $activitygroup = groups_get_activity_group($cm);
        }

        if ($instance->teamsubmission) {
            $warnofungroupedusers = assign_grading_summary::WARN_GROUPS_NO;
            $defaultteammembers = $this->get_submission_group_members(0, true);
            if (count($defaultteammembers) > 0) {
                if ($instance->preventsubmissionnotingroup) {
                    $warnofungroupedusers = assign_grading_summary::WARN_GROUPS_REQUIRED;
                } else {
                    $warnofungroupedusers = assign_grading_summary::WARN_GROUPS_OPTIONAL;
                }
            }

            $summary = new assign_grading_summary(
                $this->count_teams($activitygroup),
                $instance->submissiondrafts,
                $this->count_submissions_with_status($draft, $activitygroup),
                $this->is_any_submission_plugin_enabled(),
                $this->count_submissions_with_status($submitted, $activitygroup),
                $instance->cutoffdate,
                $this->get_duedate($activitygroup),
                $instance->timelimit,
                $this->get_course_module()->id,
                $this->count_submissions_need_grading($activitygroup),
                $instance->teamsubmission,
                $warnofungroupedusers,
                $course->relativedatesmode,
                $course->startdate,
                $this->can_grade(),
                $isvisible,
                $this->get_course_module()
            );
        } else {
            // The active group has already been updated in groups_print_activity_menu().
            $countparticipants = $this->count_participants($activitygroup);
            $summary = new assign_grading_summary(
                $countparticipants,
                $instance->submissiondrafts,
                $this->count_submissions_with_status($draft, $activitygroup),
                $this->is_any_submission_plugin_enabled(),
                $this->count_submissions_with_status($submitted, $activitygroup),
                $instance->cutoffdate,
                $this->get_duedate($activitygroup),
                $instance->timelimit,
                $this->get_course_module()->id,
                $this->count_submissions_need_grading($activitygroup),
                $instance->teamsubmission,
                assign_grading_summary::WARN_GROUPS_NO,
                $course->relativedatesmode,
                $course->startdate,
                $this->can_grade(),
                $isvisible,
                $this->get_course_module()
            );
        }

        return $summary;
    }

    /**
     * Return group override duedate.
     *
     * @param int $activitygroup Activity active group
     * @return int $duedate
     */
    private function  get_duedate($activitygroup = null) {
        global $DB;

        if ($activitygroup === null) {
            $activitygroup = groups_get_activity_group($this->get_course_module());
        }
        if ($this->can_view_grades()) {
            $params = array('groupid' => $activitygroup, 'assignid' => $this->get_instance()->id);
            $groupoverride = $DB->get_record('assign_overrides', $params);
            if (!empty($groupoverride->duedate)) {
                return $groupoverride->duedate;
            }
        }
        return $this->get_instance()->duedate;
    }

    /**
     * View submissions page (contains details of current submission).
     *
     * @return string
     */
    protected function view_submission_page() {
        global $CFG, $DB, $USER, $PAGE;

        $instance = $this->get_instance();

        $this->add_grade_notices();

        $o = '';

        $postfix = '';
        if ($this->has_visible_attachments() && (!$this->get_instance($USER->id)->submissionattachments)) {
            $postfix = $this->render_area_files('mod_assign', ASSIGN_INTROATTACHMENT_FILEAREA, 0);
        }

        $o .= $this->get_renderer()->render(new assign_header($instance,
                                                      $this->get_context(),
                                                      $this->show_intro(),
                                                      $this->get_course_module()->id,
                                                      '', '', $postfix));

        // Display plugin specific headers.
        $plugins = array_merge($this->get_submission_plugins(), $this->get_feedback_plugins());
        foreach ($plugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {
                $o .= $this->get_renderer()->render(new assign_plugin_header($plugin));
            }
        }

        if ($this->can_view_grades()) {
            $actionbuttons = new \mod_assign\output\actionmenu($this->get_course_module()->id);
            $o .= $this->get_renderer()->submission_actionmenu($actionbuttons);

            $summary = $this->get_assign_grading_summary_renderable();
            $o .= $this->get_renderer()->render($summary);
        }

        if ($this->can_view_submission($USER->id)) {
            $o .= $this->view_submission_action_bar($instance, $USER);
            $o .= $this->view_student_summary($USER, true);
        }

        $o .= $this->view_footer();

        \mod_assign\event\submission_status_viewed::create_from_assign($this)->trigger();

        return $o;
    }

    /**
     * The action bar displayed in the submissions page.
     *
     * @param stdClass $instance The settings for the current instance of this assignment
     * @param stdClass $user The user to print the action bar for
     * @return string
     */
    public function view_submission_action_bar(stdClass $instance, stdClass $user): string {
        $submission = $this->get_user_submission($user->id, false);
        // Figure out if we are team or solitary submission.
        $teamsubmission = null;
        if ($instance->teamsubmission) {
            $teamsubmission = $this->get_group_submission($user->id, 0, false);
        }

        $showsubmit = ($this->submissions_open($user->id)
            && $this->show_submit_button($submission, $teamsubmission, $user->id));
        $showedit = ($this->is_any_submission_plugin_enabled()) && $this->can_edit_submission($user->id);

        // The method get_group_submission() says that it returns a stdClass, but it can return false >_>.
        if ($teamsubmission === false) {
            $teamsubmission = new stdClass();
        }
        // Same goes for get_user_submission().
        if ($submission === false) {
            $submission = new stdClass();
        }
        $actionbuttons = new \mod_assign\output\user_submission_actionmenu(
            $this->get_course_module()->id,
            $showsubmit,
            $showedit,
            $submission,
            $teamsubmission,
            $instance->timelimit
        );

        return $this->get_renderer()->render($actionbuttons);
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
        // Allow "no grade" to be chosen.
        if ($grade->grade == -1) {
            $gradebookgrade['rawgrade'] = NULL;
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
        if (isset($grade->feedbackfiles)) {
            $gradebookgrade['feedbackfiles'] = $grade->feedbackfiles;
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
        global $CFG;

        require_once($CFG->dirroot.'/mod/assign/lib.php');
        // Do not push grade to gradebook if blind marking is active as
        // the gradebook would reveal the students.
        if ($this->is_blind_marking()) {
            return false;
        }

        // If marking workflow is enabled and grade is not released then remove any grade that may exist in the gradebook.
        if ($this->get_instance()->markingworkflow && !empty($grade) &&
                $this->get_grading_status($grade->userid) != ASSIGN_MARKING_WORKFLOW_STATE_RELEASED) {
            // Remove the grade (if it exists) from the gradebook as it is not 'final'.
            $grade->grade = -1;
            $grade->feedbacktext = '';
            $grade->feebackfiles = [];
        }

        if ($submission != null) {
            if ($submission->userid == 0) {
                // This is a group submission update.
                $team = groups_get_members($submission->groupid, 'u.id');

                foreach ($team as $member) {
                    $membersubmission = clone $submission;
                    $membersubmission->groupid = 0;
                    $membersubmission->userid = $member->id;
                    $this->gradebook_item_update($membersubmission, null);
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
        // Set assign gradebook feedback plugin status (enabled and visible).
        $assign->gradefeedbackenabled = $this->is_gradebook_feedback_enabled();
        return assign_grade_item_update($assign, $gradebookgrade) == GRADE_UPDATE_OK;
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
        if (!in_array($submission->status, [ASSIGN_SUBMISSION_STATUS_NEW, ASSIGN_SUBMISSION_STATUS_REOPENED])) {
            foreach ($team as $member) {
                $membersubmission = $this->get_user_submission($member->id, false, $submission->attemptnumber);

                // If no submission found for team member and member is active then everyone has not submitted.
                if (!$membersubmission || $membersubmission->status != ASSIGN_SUBMISSION_STATUS_SUBMITTED
                        && ($this->is_active_user($member->id))) {
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
                $membersubmission->status = $submission->status;
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
     * @param bool $skipenrolled - Skip enrollment checks (because they have been done already)
     * @param stdClass $submission - Pre-fetched submission record (or false to fetch it)
     * @param stdClass $flags - Pre-fetched user flags record (or false to fetch it)
     * @param stdClass $gradinginfo - Pre-fetched user gradinginfo record (or false to fetch it)
     * @return bool
     */
    public function submissions_open($userid = 0,
                                     $skipenrolled = false,
                                     $submission = false,
                                     $flags = false,
                                     $gradinginfo = false) {
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

        if ($flags === false) {
            $flags = $this->get_user_flags($userid, false);
        }
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
        if (!$skipenrolled && !is_enrolled($this->get_course_context(), $userid)) {
            return false;
        }
        // Note you can pass null for submission and it will not be fetched.
        if ($submission === false) {
            if ($this->get_instance()->teamsubmission) {
                $submission = $this->get_group_submission($userid, 0, false);
            } else {
                $submission = $this->get_user_submission($userid, false);
            }
        }
        if ($submission) {

            if ($this->get_instance()->submissiondrafts && $submission->status == ASSIGN_SUBMISSION_STATUS_SUBMITTED) {
                // Drafts are tracked and the student has submitted the assignment.
                return false;
            }
        }

        // See if this user grade is locked in the gradebook.
        if ($gradinginfo === false) {
            $gradinginfo = grade_get_grades($this->get_course()->id,
                                            'mod',
                                            'assign',
                                            $this->get_instance()->id,
                                            array($userid));
        }
        if ($gradinginfo &&
                isset($gradinginfo->items[0]->grades[$userid]) &&
                $gradinginfo->items[0]->grades[$userid]->locked) {
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

        return $this->get_renderer()->assign_files($this->context, $submissionid, $area, $component,
                                                   $this->course, $this->coursemodule);

    }

    /**
     * Capability check to make sure this grader can edit this submission.
     *
     * @param int $userid - The user whose submission is to be edited
     * @param int $graderid (optional) - The user who will do the editing (default to $USER->id).
     * @return bool
     */
    public function can_edit_submission($userid, $graderid = 0) {
        global $USER;

        if (empty($graderid)) {
            $graderid = $USER->id;
        }

        $instance = $this->get_instance();
        if ($userid == $graderid &&
            $instance->teamsubmission &&
            $instance->preventsubmissionnotingroup &&
            $this->get_submission_group($userid) == false) {
            return false;
        }

        if ($userid == $graderid) {
            if ($this->submissions_open($userid) &&
                    has_capability('mod/assign:submit', $this->context, $graderid)) {
                // User can edit their own submission.
                return true;
            } else {
                // We need to return here because editothersubmission should never apply to a users own submission.
                return false;
            }
        }

        if (!has_capability('mod/assign:editothersubmission', $this->context, $graderid)) {
            return false;
        }

        $cm = $this->get_course_module();
        if (groups_get_activity_groupmode($cm) == SEPARATEGROUPS) {
            $sharedgroupmembers = $this->get_shared_group_members($cm, $graderid);
            return in_array($userid, $sharedgroupmembers);
        }
        return true;
    }

    /**
     * Returns IDs of the users who share group membership with the specified user.
     *
     * @param stdClass|cm_info $cm Course-module
     * @param int $userid User ID
     * @return array An array of ID of users.
     */
    public function get_shared_group_members($cm, $userid) {
        if (!isset($this->sharedgroupmembers[$userid])) {
            $this->sharedgroupmembers[$userid] = array();
            if ($members = groups_get_activity_shared_group_members($cm, $userid)) {
                $this->sharedgroupmembers[$userid] = array_keys($members);
            }
        }

        return $this->sharedgroupmembers[$userid];
    }

    /**
     * Returns a list of teachers that should be grading given submission.
     *
     * @param int $userid The submission to grade
     * @return array
     */
    protected function get_graders($userid) {
        // Potential graders should be active users only.
        $potentialgraders = get_enrolled_users($this->context, "mod/assign:grade", null, 'u.*', null, null, null, true);

        $graders = array();
        if (groups_get_activity_groupmode($this->get_course_module()) == SEPARATEGROUPS) {
            if ($groups = groups_get_all_groups($this->get_course()->id, $userid, $this->get_course_module()->groupingid)) {
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
     * Returns a list of users that should receive notification about given submission.
     *
     * @param int $userid The submission to grade
     * @return array
     */
    protected function get_notifiable_users($userid) {
        // Potential users should be active users only.
        $potentialusers = get_enrolled_users($this->context, "mod/assign:receivegradernotifications",
                                             null, 'u.*', null, null, null, true);

        $notifiableusers = array();
        if (groups_get_activity_groupmode($this->get_course_module()) == SEPARATEGROUPS) {
            if ($groups = groups_get_all_groups($this->get_course()->id, $userid, $this->get_course_module()->groupingid)) {
                foreach ($groups as $group) {
                    foreach ($potentialusers as $potentialuser) {
                        if ($potentialuser->id == $userid) {
                            // Do not send self.
                            continue;
                        }
                        if (groups_is_member($group->id, $potentialuser->id)) {
                            $notifiableusers[$potentialuser->id] = $potentialuser;
                        }
                    }
                }
            } else {
                // User not in group, try to find graders without group.
                foreach ($potentialusers as $potentialuser) {
                    if ($potentialuser->id == $userid) {
                        // Do not send self.
                        continue;
                    }
                    if (!groups_has_membership($this->get_course_module(), $potentialuser->id)) {
                        $notifiableusers[$potentialuser->id] = $potentialuser;
                    }
                }
            }
        } else {
            foreach ($potentialusers as $potentialuser) {
                if ($potentialuser->id == $userid) {
                    // Do not send self.
                    continue;
                }
                // Must be enrolled.
                if (is_enrolled($this->get_course_context(), $potentialuser->id)) {
                    $notifiableusers[$potentialuser->id] = $potentialuser;
                }
            }
        }
        return $notifiableusers;
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
     * @param bool $blindmarking
     * @param int $uniqueidforuser
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
        global $CFG, $PAGE;

        $info = new stdClass();
        if ($blindmarking) {
            $userfrom = clone($userfrom);
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

        $eventdata = new \core\message\message();
        $eventdata->courseid         = $course->id;
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
        $customdata = [
            'cmid' => $coursemodule->id,
            'instance' => $coursemodule->instance,
            'messagetype' => $messagetype,
            'blindmarking' => $blindmarking,
            'uniqueidforuser' => $uniqueidforuser,
        ];
        // Check if the userfrom is real and visible.
        if (!empty($userfrom->id) && core_user::is_real_user($userfrom->id)) {
            $userpicture = new user_picture($userfrom);
            $userpicture->size = 1; // Use f1 size.
            $userpicture->includetoken = $userto->id; // Generate an out-of-session token for the user receiving the message.
            $customdata['notificationiconurl'] = $userpicture->get_url($PAGE)->out(false);
        }
        $eventdata->customdata = $customdata;

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
    public function send_notification($userfrom, $userto, $messagetype, $eventtype, $updatetime) {
        global $USER;
        $userid = core_user::is_real_user($userfrom->id) ? $userfrom->id : $USER->id;
        $uniqueid = $this->get_uniqueid_for_user($userid);
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
                                           $uniqueid);
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
        if ($submission->userid == $USER->id) {
            $this->send_notification(core_user::get_noreply_user(),
                                     $user,
                                     'submissionreceipt',
                                     'assign_notification',
                                     $submission->timemodified);
        } else {
            $this->send_notification($USER,
                                     $user,
                                     'submissionreceiptother',
                                     'assign_notification',
                                     $submission->timemodified);
        }
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

        if ($notifyusers = $this->get_notifiable_users($user->id)) {
            foreach ($notifyusers as $notifyuser) {
                $this->send_notification($user,
                                         $notifyuser,
                                         'gradersubmissionupdated',
                                         'assign_notification',
                                         $submission->timemodified);
            }
        }
    }

    /**
     * Submit a submission for grading.
     *
     * @param stdClass $data - The form data
     * @param array $notices - List of error messages to display on an error condition.
     * @return bool Return false if the submission was not submitted.
     */
    public function submit_for_grading($data, $notices) {
        global $USER;

        $userid = $USER->id;
        if (!empty($data->userid)) {
            $userid = $data->userid;
        }
        // Need submit permission to submit an assignment.
        if ($userid == $USER->id) {
            require_capability('mod/assign:submit', $this->context);
        } else {
            if (!$this->can_edit_submission($userid, $USER->id)) {
                print_error('nopermission');
            }
        }

        $instance = $this->get_instance();

        if ($instance->teamsubmission) {
            $submission = $this->get_group_submission($userid, 0, true);
        } else {
            $submission = $this->get_user_submission($userid, true);
        }

        if (!$this->submissions_open($userid)) {
            $notices[] = get_string('submissionsclosed', 'assign');
            return false;
        }

        if ($instance->requiresubmissionstatement && empty($data->submissionstatement) && $USER->id == $userid) {
            return false;
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
            $this->update_submission($submission, $userid, true, $instance->teamsubmission);
            $completion = new completion_info($this->get_course());
            if ($completion->is_enabled($this->get_course_module()) && $instance->completionsubmit) {
                $this->update_activity_completion_records($instance->teamsubmission,
                                                          $instance->requireallteammemberssubmit,
                                                          $submission,
                                                          $userid,
                                                          COMPLETION_COMPLETE,
                                                          $completion);
            }

            if (!empty($data->submissionstatement) && $USER->id == $userid) {
                \mod_assign\event\statement_accepted::create_from_submission($this, $submission)->trigger();
            }
            $this->notify_graders($submission);
            $this->notify_student_submission_receipt($submission);

            \mod_assign\event\assessable_submitted::create_from_submission($this, $submission, false)->trigger();

            return true;
        }
        $notices[] = get_string('submissionsclosed', 'assign');
        return false;
    }

    /**
     * A students submission is submitted for grading by a teacher.
     *
     * @return bool
     */
    protected function process_submit_other_for_grading($mform, $notices) {
        global $USER, $CFG;

        require_sesskey();

        $userid = optional_param('userid', $USER->id, PARAM_INT);

        if (!$this->submissions_open($userid)) {
            $notices[] = get_string('submissionsclosed', 'assign');
            return false;
        }
        $data = new stdClass();
        $data->userid = $userid;
        return $this->submit_for_grading($data, $notices);
    }

    /**
     * Assignment submission is processed before grading.
     *
     * @param moodleform|null $mform If validation failed when submitting this form - this is the moodleform.
     *               It can be null.
     * @return bool Return false if the validation fails. This affects which page is displayed next.
     */
    protected function process_submit_for_grading($mform, $notices) {
        global $CFG;

        require_once($CFG->dirroot . '/mod/assign/submissionconfirmform.php');
        require_sesskey();

        if (!$this->submissions_open()) {
            $notices[] = get_string('submissionsclosed', 'assign');
            return false;
        }

        $data = new stdClass();
        $adminconfig = $this->get_admin_config();
        $requiresubmissionstatement = $this->get_instance()->requiresubmissionstatement;

        $submissionstatement = '';
        if ($requiresubmissionstatement) {
            $submissionstatement = $this->get_submissionstatement($adminconfig, $this->get_instance(), $this->get_context());
        }

        // If we get back an empty submission statement, we have to set $requiredsubmisisonstatement to false to prevent
        // that the submission statement checkbox will be displayed.
        if (empty($submissionstatement)) {
            $requiresubmissionstatement = false;
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
            return $this->submit_for_grading($data, $notices);
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
    public function save_user_extension($userid, $extensionduedate) {
        global $DB;

        // Need submit permission to submit an assignment.
        require_capability('mod/assign:grantextension', $this->context);

        if (!is_enrolled($this->get_course_context(), $userid)) {
            return false;
        }
        if (!has_capability('mod/assign:submit', $this->context, $userid)) {
            return false;
        }

        if ($this->get_instance()->duedate && $extensionduedate) {
            if ($this->get_instance()->duedate > $extensionduedate) {
                return false;
            }
        }
        if ($this->get_instance()->allowsubmissionsfromdate && $extensionduedate) {
            if ($this->get_instance()->allowsubmissionsfromdate > $extensionduedate) {
                return false;
            }
        }

        $flags = $this->get_user_flags($userid, true);
        $flags->extensionduedate = $extensionduedate;

        $result = $this->update_user_flags($flags);

        if ($result) {
            \mod_assign\event\extension_granted::create_from_assign($this, $userid)->trigger();
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
        require_sesskey();

        $users = optional_param('userid', 0, PARAM_INT);
        if (!$users) {
            $users = required_param('selectedusers', PARAM_SEQUENCE);
        }
        $userlist = explode(',', $users);

        $keys = array('duedate', 'cutoffdate', 'allowsubmissionsfromdate');
        $maxoverride = array('allowsubmissionsfromdate' => 0, 'duedate' => 0, 'cutoffdate' => 0);
        foreach ($userlist as $userid) {
            // To validate extension date with users overrides.
            $override = $this->override_exists($userid);
            foreach ($keys as $key) {
                if ($override->{$key}) {
                    if ($maxoverride[$key] < $override->{$key}) {
                        $maxoverride[$key] = $override->{$key};
                    }
                } else if ($maxoverride[$key] < $this->get_instance()->{$key}) {
                    $maxoverride[$key] = $this->get_instance()->{$key};
                }
            }
        }
        foreach ($keys as $key) {
            if ($maxoverride[$key]) {
                $this->get_instance()->{$key} = $maxoverride[$key];
            }
        }

        $formparams = array(
            'instance' => $this->get_instance(),
            'assign' => $this,
            'userlist' => $userlist
        );

        $mform = new mod_assign_extension_form(null, $formparams);

        if ($mform->is_cancelled()) {
            return true;
        }

        if ($formdata = $mform->get_data()) {
            if (!empty($formdata->selectedusers)) {
                $users = explode(',', $formdata->selectedusers);
                $result = true;
                foreach ($users as $userid) {
                    $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
                    $result = $this->save_user_extension($user->id, $formdata->extensionduedate) && $result;
                }
                return $result;
            }
            if (!empty($formdata->userid)) {
                $user = $DB->get_record('user', array('id' => $formdata->userid), '*', MUST_EXIST);
                return $this->save_user_extension($user->id, $formdata->extensionduedate);
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
        require_sesskey();

        // Make sure advanced grading is disabled.
        $gradingmanager = get_grading_manager($this->get_context(), 'mod_assign', 'submissions');
        $controller = $gradingmanager->get_active_controller();
        if (!empty($controller)) {
            $message = get_string('errorquickgradingvsadvancedgrading', 'assign');
            $this->set_error_message($message);
            return $message;
        }

        $users = array();
        // First check all the last modified values.
        $currentgroup = groups_get_activity_group($this->get_course_module(), true);
        $participants = $this->list_participants($currentgroup, true);

        // Gets a list of possible users and look for values based upon that.
        foreach ($participants as $userid => $unused) {
            $modified = optional_param('grademodified_' . $userid, -1, PARAM_INT);
            $attemptnumber = optional_param('gradeattempt_' . $userid, -1, PARAM_INT);
            // Gather the userid, updated grade and last modified value.
            $record = new stdClass();
            $record->userid = $userid;
            if ($modified >= 0) {
                $record->grade = unformat_float(optional_param('quickgrade_' . $record->userid, -1, PARAM_TEXT));
                $record->workflowstate = optional_param('quickgrade_' . $record->userid.'_workflowstate', false, PARAM_ALPHA);
                $record->allocatedmarker = optional_param('quickgrade_' . $record->userid.'_allocatedmarker', false, PARAM_INT);
            } else {
                // This user was not in the grading table.
                continue;
            }
            $record->attemptnumber = $attemptnumber;
            $record->lastmodified = $modified;
            $record->gradinginfo = grade_get_grades($this->get_course()->id,
                                                    'mod',
                                                    'assign',
                                                    $this->get_instance()->id,
                                                    array($userid));
            $users[$userid] = $record;
        }

        if (empty($users)) {
            $message = get_string('nousersselected', 'assign');
            $this->set_error_message($message);
            return $message;
        }

        list($userids, $params) = $DB->get_in_or_equal(array_keys($users), SQL_PARAMS_NAMED);
        $params['assignid1'] = $this->get_instance()->id;
        $params['assignid2'] = $this->get_instance()->id;

        // Check them all for currency.
        $grademaxattempt = 'SELECT s.userid, s.attemptnumber AS maxattempt
                              FROM {assign_submission} s
                             WHERE s.assignment = :assignid1 AND s.latest = 1';

        $sql = 'SELECT u.id AS userid, g.grade AS grade, g.timemodified AS lastmodified,
                       uf.workflowstate, uf.allocatedmarker, gmx.maxattempt AS attemptnumber
                  FROM {user} u
             LEFT JOIN ( ' . $grademaxattempt . ' ) gmx ON u.id = gmx.userid
             LEFT JOIN {assign_grades} g ON
                       u.id = g.userid AND
                       g.assignment = :assignid2 AND
                       g.attemptnumber = gmx.maxattempt
             LEFT JOIN {assign_user_flags} uf ON uf.assignment = g.assignment AND uf.userid = g.userid
                 WHERE u.id ' . $userids;
        $currentgrades = $DB->get_recordset_sql($sql, $params);

        $modifiedusers = array();
        foreach ($currentgrades as $current) {
            $modified = $users[(int)$current->userid];
            $grade = $this->get_user_grade($modified->userid, false);
            // Check to see if the grade column was even visible.
            $gradecolpresent = optional_param('quickgrade_' . $modified->userid, false, PARAM_INT) !== false;

            // Check to see if the outcomes were modified.
            if ($CFG->enableoutcomes) {
                foreach ($modified->gradinginfo->outcomes as $outcomeid => $outcome) {
                    $oldoutcome = $outcome->grades[$modified->userid]->grade;
                    $paramname = 'outcome_' . $outcomeid . '_' . $modified->userid;
                    $newoutcome = optional_param($paramname, -1, PARAM_FLOAT);
                    // Check to see if the outcome column was even visible.
                    $outcomecolpresent = optional_param($paramname, false, PARAM_FLOAT) !== false;
                    if ($outcomecolpresent && ($oldoutcome != $newoutcome)) {
                        // Can't check modified time for outcomes because it is not reported.
                        $modifiedusers[$modified->userid] = $modified;
                        continue;
                    }
                }
            }

            // Let plugins participate.
            foreach ($this->feedbackplugins as $plugin) {
                if ($plugin->is_visible() && $plugin->is_enabled() && $plugin->supports_quickgrading()) {
                    // The plugins must handle is_quickgrading_modified correctly - ie
                    // handle hidden columns.
                    if ($plugin->is_quickgrading_modified($modified->userid, $grade)) {
                        if ((int)$current->lastmodified > (int)$modified->lastmodified) {
                            $message = get_string('errorrecordmodified', 'assign');
                            $this->set_error_message($message);
                            return $message;
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
                $modified->grade = $current->grade; // Keep existing grade.
            }
            // Treat 0 and null as different values.
            if ($current->grade !== null) {
                $current->grade = floatval($current->grade);
            }
            $gradechanged = $gradecolpresent && grade_floats_different($current->grade, $modified->grade);
            $markingallocationchanged = $this->get_instance()->markingworkflow &&
                                        $this->get_instance()->markingallocation &&
                                            ($modified->allocatedmarker !== false) &&
                                            ($current->allocatedmarker != $modified->allocatedmarker);
            $workflowstatechanged = $this->get_instance()->markingworkflow &&
                                            ($modified->workflowstate !== false) &&
                                            ($current->workflowstate != $modified->workflowstate);
            if ($gradechanged || $markingallocationchanged || $workflowstatechanged) {
                // Grade changed.
                if ($this->grading_disabled($modified->userid)) {
                    continue;
                }
                $badmodified = (int)$current->lastmodified > (int)$modified->lastmodified;
                $badattempt = (int)$current->attemptnumber != (int)$modified->attemptnumber;
                if ($badmodified || $badattempt) {
                    // Error - record has been modified since viewing the page.
                    $message = get_string('errorrecordmodified', 'assign');
                    $this->set_error_message($message);
                    return $message;
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
            $flags = $this->get_user_flags($userid, true);
            $grade->grade= grade_floatval(unformat_float($modified->grade));
            $grade->grader= $USER->id;
            $gradecolpresent = optional_param('quickgrade_' . $userid, false, PARAM_INT) !== false;

            // Save plugins data.
            foreach ($this->feedbackplugins as $plugin) {
                if ($plugin->is_visible() && $plugin->is_enabled() && $plugin->supports_quickgrading()) {
                    $plugin->save_quickgrading_changes($userid, $grade);
                    if (('assignfeedback_' . $plugin->get_type()) == $gradebookplugin) {
                        // This is the feedback plugin chose to push comments to the gradebook.
                        $grade->feedbacktext = $plugin->text_for_gradebook($grade);
                        $grade->feedbackformat = $plugin->format_for_gradebook($grade);
                        $grade->feedbackfiles = $plugin->files_for_gradebook($grade);
                    }
                }
            }

            // These will be set to false if they are not present in the quickgrading
            // form (e.g. column hidden).
            $workflowstatemodified = ($modified->workflowstate !== false) &&
                                        ($flags->workflowstate != $modified->workflowstate);

            $allocatedmarkermodified = ($modified->allocatedmarker !== false) &&
                                        ($flags->allocatedmarker != $modified->allocatedmarker);

            if ($workflowstatemodified) {
                $flags->workflowstate = $modified->workflowstate;
            }
            if ($allocatedmarkermodified) {
                $flags->allocatedmarker = $modified->allocatedmarker;
            }
            if ($workflowstatemodified || $allocatedmarkermodified) {
                if ($this->update_user_flags($flags) && $workflowstatemodified) {
                    $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
                    \mod_assign\event\workflow_state_updated::create_from_user($this, $user, $flags->workflowstate)->trigger();
                }
            }
            $this->update_grade($grade);

            // Allow teachers to skip sending notifications.
            if (optional_param('sendstudentnotifications', true, PARAM_BOOL)) {
                $this->notify_grade_modified($grade, true);
            }

            // Save outcomes.
            if ($CFG->enableoutcomes) {
                $data = array();
                foreach ($modified->gradinginfo->outcomes as $outcomeid => $outcome) {
                    $oldoutcome = $outcome->grades[$modified->userid]->grade;
                    $paramname = 'outcome_' . $outcomeid . '_' . $modified->userid;
                    // This will be false if the input was not in the quickgrading
                    // form (e.g. column hidden).
                    $newoutcome = optional_param($paramname, false, PARAM_INT);
                    if ($newoutcome !== false && ($oldoutcome != $newoutcome)) {
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
        }

        return get_string('quickgradingchangessaved', 'assign');
    }

    /**
     * Reveal student identities to markers (and the gradebook).
     *
     * @return void
     */
    public function reveal_identities() {
        global $DB;

        require_capability('mod/assign:revealidentities', $this->context);

        if ($this->get_instance()->revealidentities || empty($this->get_instance()->blindmarking)) {
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
        $gradebookplugin = str_replace('assignfeedback_', '', $gradebookplugin);
        $grades = $DB->get_records('assign_grades', array('assignment'=>$this->get_instance()->id));

        $plugin = $this->get_feedback_plugin_by_type($gradebookplugin);

        foreach ($grades as $grade) {
            // Fetch any comments for this student.
            if ($plugin && $plugin->is_enabled() && $plugin->is_visible()) {
                $grade->feedbacktext = $plugin->text_for_gradebook($grade);
                $grade->feedbackformat = $plugin->format_for_gradebook($grade);
                $grade->feedbackfiles = $plugin->files_for_gradebook($grade);
            }
            $this->gradebook_item_update(null, $grade);
        }

        \mod_assign\event\identities_revealed::create_from_assign($this)->trigger();
    }

    /**
     * Reveal student identities to markers (and the gradebook).
     *
     * @return void
     */
    protected function process_reveal_identities() {

        if (!confirm_sesskey()) {
            return false;
        }

        return $this->reveal_identities();
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
        $this->require_view_grades();
        require_sesskey();

        // Is advanced grading enabled?
        $gradingmanager = get_grading_manager($this->get_context(), 'mod_assign', 'submissions');
        $controller = $gradingmanager->get_active_controller();
        $showquickgrading = empty($controller);
        if (!is_null($this->context)) {
            $showonlyactiveenrolopt = has_capability('moodle/course:viewsuspendedusers', $this->context);
        } else {
            $showonlyactiveenrolopt = false;
        }

        $markingallocation = $this->get_instance()->markingworkflow &&
            $this->get_instance()->markingallocation &&
            has_capability('mod/assign:manageallocations', $this->context);
        // Get markers to use in drop lists.
        $markingallocationoptions = array();
        if ($markingallocation) {
            $markingallocationoptions[''] = get_string('filternone', 'assign');
            $markingallocationoptions[ASSIGN_MARKER_FILTER_NO_MARKER] = get_string('markerfilternomarker', 'assign');
            list($sort, $params) = users_order_by_sql('u');
            // Only enrolled users could be assigned as potential markers.
            $markers = get_enrolled_users($this->context, 'mod/assign:grade', 0, 'u.*', $sort);
            foreach ($markers as $marker) {
                $markingallocationoptions[$marker->id] = fullname($marker);
            }
        }

        // Get marking states to show in form.
        $markingworkflowoptions = $this->get_marking_workflow_filters();

        $gradingoptionsparams = array('cm'=>$this->get_course_module()->id,
                                      'contextid'=>$this->context->id,
                                      'userid'=>$USER->id,
                                      'submissionsenabled'=>$this->is_any_submission_plugin_enabled(),
                                      'showquickgrading'=>$showquickgrading,
                                      'quickgrading'=>false,
                                      'markingworkflowopt' => $markingworkflowoptions,
                                      'markingallocationopt' => $markingallocationoptions,
                                      'showonlyactiveenrolopt'=>$showonlyactiveenrolopt,
                                      'showonlyactiveenrol' => $this->show_only_active_users(),
                                      'downloadasfolders' => get_user_preferences('assign_downloadasfolders', 1));
        $mform = new mod_assign_grading_options_form(null, $gradingoptionsparams);
        if ($formdata = $mform->get_data()) {
            set_user_preference('assign_perpage', $formdata->perpage);
            if (isset($formdata->filter)) {
                set_user_preference('assign_filter', $formdata->filter);
            }
            if (isset($formdata->markerfilter)) {
                set_user_preference('assign_markerfilter', $formdata->markerfilter);
            }
            if (isset($formdata->workflowfilter)) {
                set_user_preference('assign_workflowfilter', $formdata->workflowfilter);
            }
            if ($showquickgrading) {
                set_user_preference('assign_quickgrading', isset($formdata->quickgrading));
            }
            if (isset($formdata->downloadasfolders)) {
                set_user_preference('assign_downloadasfolders', 1); // Enabled.
            } else {
                set_user_preference('assign_downloadasfolders', 0); // Disabled.
            }
            if (!empty($showonlyactiveenrolopt)) {
                $showonlyactiveenrol = isset($formdata->showonlyactiveenrol);
                set_user_preference('grade_report_showonlyactiveenrol', $showonlyactiveenrol);
                $this->showonlyactiveenrol = $showonlyactiveenrol;
            }
        }
    }

    /**
     * Take a grade object and print a short summary for the log file.
     * The size limit for the log file is 255 characters, so be careful not
     * to include too much information.
     *
     * @deprecated since 2.7
     *
     * @param stdClass $grade
     * @return string
     */
    public function format_grade_for_log(stdClass $grade) {
        global $DB;

        $user = $DB->get_record('user', array('id' => $grade->userid), '*', MUST_EXIST);

        $info = get_string('gradestudent', 'assign', array('id'=>$user->id, 'fullname'=>fullname($user)));
        if ($grade->grade != '') {
            $info .= get_string('gradenoun') . ': ' . $this->display_grade($grade->grade, false) . '. ';
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
     * @deprecated since 2.7
     *
     * @param stdClass $submission
     * @return string
     */
    public function format_submission_for_log(stdClass $submission) {
        global $DB;

        $info = '';
        if ($submission->userid) {
            $user = $DB->get_record('user', array('id' => $submission->userid), '*', MUST_EXIST);
            $name = fullname($user);
        } else {
            $group = $this->get_submission_group($submission->userid);
            if ($group) {
                $name = $group->name;
            } else {
                $name = get_string('defaultteam', 'assign');
            }
        }
        $status = get_string('submissionstatus_' . $submission->status, 'assign');
        $params = array('id'=>$submission->userid, 'fullname'=>$name, 'status'=>$status);
        $info .= get_string('submissionlog', 'assign', $params) . ' <br>';

        foreach ($this->submissionplugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {
                $info .= '<br>' . $plugin->format_for_log($submission);
            }
        }

        return $info;
    }

    /**
     * Require a valid sess key and then call copy_previous_attempt.
     *
     * @param  array $notices Any error messages that should be shown
     *                        to the user at the top of the edit submission form.
     * @return bool
     */
    protected function process_copy_previous_attempt(&$notices) {
        require_sesskey();

        return $this->copy_previous_attempt($notices);
    }

    /**
     * Copy the current assignment submission from the last submitted attempt.
     *
     * @param  array $notices Any error messages that should be shown
     *                        to the user at the top of the edit submission form.
     * @return bool
     */
    public function copy_previous_attempt(&$notices) {
        global $USER, $CFG;

        require_capability('mod/assign:submit', $this->context);

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

        \mod_assign\event\submission_duplicated::create_from_submission($this, $submission)->trigger();

        $complete = COMPLETION_INCOMPLETE;
        if ($submission->status == ASSIGN_SUBMISSION_STATUS_SUBMITTED) {
            $complete = COMPLETION_COMPLETE;
        }
        $completion = new completion_info($this->get_course());
        if ($completion->is_enabled($this->get_course_module()) && $instance->completionsubmit) {
            $this->update_activity_completion_records($instance->teamsubmission,
                                                      $instance->requireallteammemberssubmit,
                                                      $submission,
                                                      $USER->id,
                                                      $complete,
                                                      $completion);
        }

        if (!$instance->submissiondrafts) {
            // There is a case for not notifying the student about the submission copy,
            // but it provides a record of the event and if they then cancel editing it
            // is clear that the submission was copied.
            $this->notify_student_submission_copied($submission);
            $this->notify_graders($submission);

            // The same logic applies here - we could not notify teachers,
            // but then they would wonder why there are submitted assignments
            // and they haven't been notified.
            \mod_assign\event\assessable_submitted::create_from_submission($this, $submission, true)->trigger();
        }
        return true;
    }

    /**
     * Determine if the current submission is empty or not.
     *
     * @param submission $submission the students submission record to check.
     * @return bool
     */
    public function submission_empty($submission) {
        $allempty = true;

        foreach ($this->submissionplugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {
                if (!$allempty || !$plugin->is_empty($submission)) {
                    $allempty = false;
                }
            }
        }
        return $allempty;
    }

    /**
     * Determine if a new submission is empty or not
     *
     * @param stdClass $data Submission data
     * @return bool
     */
    public function new_submission_empty($data) {
        foreach ($this->submissionplugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible() && $plugin->allow_submissions() &&
                    !$plugin->submission_is_empty($data)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Save assignment submission for the current user.
     *
     * @param  stdClass $data
     * @param  array $notices Any error messages that should be shown
     *                        to the user.
     * @return bool
     */
    public function save_submission(stdClass $data, & $notices) {
        global $CFG, $USER, $DB;

        $userid = $USER->id;
        if (!empty($data->userid)) {
            $userid = $data->userid;
        }

        $user = clone($USER);
        if ($userid == $USER->id) {
            require_capability('mod/assign:submit', $this->context);
        } else {
            $user = $DB->get_record('user', array('id'=>$userid), '*', MUST_EXIST);
            if (!$this->can_edit_submission($userid, $USER->id)) {
                print_error('nopermission');
            }
        }
        $instance = $this->get_instance();

        if ($instance->teamsubmission) {
            $submission = $this->get_group_submission($userid, 0, true);
        } else {
            $submission = $this->get_user_submission($userid, true);
        }

        if ($this->new_submission_empty($data)) {
            $notices[] = get_string('submissionempty', 'mod_assign');
            return false;
        }

        // Check that no one has modified the submission since we started looking at it.
        if (isset($data->lastmodified) && ($submission->timemodified > $data->lastmodified)) {
            // Another user has submitted something. Notify the current user.
            if ($submission->status !== ASSIGN_SUBMISSION_STATUS_NEW) {
                $notices[] = $instance->teamsubmission ? get_string('submissionmodifiedgroup', 'mod_assign')
                                                       : get_string('submissionmodified', 'mod_assign');
                return false;
            }
        }

        if ($instance->submissiondrafts) {
            $submission->status = ASSIGN_SUBMISSION_STATUS_DRAFT;
        } else {
            $submission->status = ASSIGN_SUBMISSION_STATUS_SUBMITTED;
        }

        $flags = $this->get_user_flags($userid, false);

        // Get the flags to check if it is locked.
        if ($flags && $flags->locked) {
            print_error('submissionslocked', 'assign');
            return true;
        }

        $pluginerror = false;
        foreach ($this->submissionplugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {
                if (!$plugin->save($submission, $data)) {
                    $notices[] = $plugin->get_error();
                    $pluginerror = true;
                }
            }
        }

        $allempty = $this->submission_empty($submission);
        if ($pluginerror || $allempty) {
            if ($allempty) {
                $notices[] = get_string('submissionempty', 'mod_assign');
            }
            return false;
        }

        $this->update_submission($submission, $userid, true, $instance->teamsubmission);
        $users = [$userid];

        if ($instance->teamsubmission && !$instance->requireallteammemberssubmit) {
            $team = $this->get_submission_group_members($submission->groupid, true);

            foreach ($team as $member) {
                if ($member->id != $userid) {
                    $membersubmission = clone($submission);
                    $this->update_submission($membersubmission, $member->id, true, $instance->teamsubmission);
                    $users[] = $member->id;
                }
            }
        }

        $complete = COMPLETION_INCOMPLETE;
        if ($submission->status == ASSIGN_SUBMISSION_STATUS_SUBMITTED) {
            $complete = COMPLETION_COMPLETE;
        }

        $completion = new completion_info($this->get_course());
        if ($completion->is_enabled($this->get_course_module()) && $instance->completionsubmit) {
            foreach ($users as $id) {
                $completion->update_state($this->get_course_module(), $complete, $id);
            }
        }

        // Logging.
        if (isset($data->submissionstatement) && ($userid == $USER->id)) {
            \mod_assign\event\statement_accepted::create_from_submission($this, $submission)->trigger();
        }

        if (!$instance->submissiondrafts) {
            $this->notify_student_submission_receipt($submission);
            $this->notify_graders($submission);
            \mod_assign\event\assessable_submitted::create_from_submission($this, $submission, true)->trigger();
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
        global $CFG, $USER;

        // Include submission form.
        require_once($CFG->dirroot . '/mod/assign/submission_form.php');

        $userid = optional_param('userid', $USER->id, PARAM_INT);
        // Need submit permission to submit an assignment.
        require_sesskey();
        if (!$this->submissions_open($userid)) {
            $notices[] = get_string('duedatereached', 'assign');
            return false;
        }
        $instance = $this->get_instance();

        $data = new stdClass();
        $data->userid = $userid;
        $mform = new mod_assign_submission_form(null, array($this, $data));
        if ($mform->is_cancelled()) {
            return true;
        }
        if ($data = $mform->get_data()) {
            return $this->save_submission($data, $notices);
        }
        return false;
    }


    /**
     * Determine if this users grade can be edited.
     *
     * @param int $userid - The student userid
     * @param bool $checkworkflow - whether to include a check for the workflow state.
     * @param stdClass $gradinginfo - optional, allow gradinginfo to be passed for performance.
     * @return bool $gradingdisabled
     */
    public function grading_disabled($userid, $checkworkflow = true, $gradinginfo = null) {
        if ($checkworkflow && $this->get_instance()->markingworkflow) {
            $grade = $this->get_user_grade($userid, false);
            $validstates = $this->get_marking_workflow_states_for_current_user();
            if (!empty($grade) && !empty($grade->workflowstate) && !array_key_exists($grade->workflowstate, $validstates)) {
                return true;
            }
        }

        if (is_null($gradinginfo)) {
            $gradinginfo = grade_get_grades($this->get_course()->id,
                'mod',
                'assign',
                $this->get_instance()->id,
                array($userid));
        }

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
     * @param stdClass|false $grade - The grade record
     * @param bool $gradingdisabled
     * @return mixed gradingform_instance|null $gradinginstance
     */
    protected function get_grading_instance($userid, $grade, $gradingdisabled) {
        global $CFG, $USER;

        $grademenu = make_grades_menu($this->get_instance()->grade);
        $allowgradedecimals = $this->get_instance()->grade > 0;

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
            $gradinginstance->get_controller()->set_grade_range($grademenu, $allowgradedecimals);
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
        global $USER, $CFG, $SESSION;
        $settings = $this->get_instance();

        $rownum = isset($params['rownum']) ? $params['rownum'] : 0;
        $last = isset($params['last']) ? $params['last'] : true;
        $useridlistid = isset($params['useridlistid']) ? $params['useridlistid'] : 0;
        $userid = isset($params['userid']) ? $params['userid'] : 0;
        $attemptnumber = isset($params['attemptnumber']) ? $params['attemptnumber'] : 0;
        $gradingpanel = !empty($params['gradingpanel']);
        $bothids = ($userid && $useridlistid);

        if (!$userid || $bothids) {
            $useridlist = $this->get_grading_userid_list(true, $useridlistid);
        } else {
            $useridlist = array($userid);
            $rownum = 0;
            $useridlistid = '';
        }

        $userid = $useridlist[$rownum];
        // We need to create a grade record matching this attempt number
        // or the feedback plugin will have no way to know what is the correct attempt.
        $grade = $this->get_user_grade($userid, true, $attemptnumber);

        $submission = null;
        if ($this->get_instance()->teamsubmission) {
            $submission = $this->get_group_submission($userid, 0, false, $attemptnumber);
        } else {
            $submission = $this->get_user_submission($userid, false, $attemptnumber);
        }

        // Add advanced grading.
        $gradingdisabled = $this->grading_disabled($userid);
        $gradinginstance = $this->get_grading_instance($userid, $grade, $gradingdisabled);

        $mform->addElement('header', 'gradeheader', get_string('gradenoun'));
        if ($gradinginstance) {
            $gradingelement = $mform->addElement('grading',
                                                 'advancedgrading',
                                                 get_string('gradenoun') . ':',
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
                if (!$gradingdisabled) {
                    $gradingelement = $mform->addElement('text', 'grade', $name);
                    $mform->addHelpButton('grade', 'gradeoutofhelp', 'assign');
                    $mform->setType('grade', PARAM_RAW);
                } else {
                    $strgradelocked = get_string('gradelocked', 'assign');
                    $mform->addElement('static', 'gradedisabled', $name, $strgradelocked);
                    $mform->addHelpButton('gradedisabled', 'gradeoutofhelp', 'assign');
                }
            } else {
                $grademenu = array(-1 => get_string("nograde")) + make_grades_menu($this->get_instance()->grade);
                if (count($grademenu) > 1) {
                    $gradingelement = $mform->addElement('select', 'grade', get_string('gradenoun') . ':', $grademenu);

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
                $options[0] = get_string('nooutcome', 'grades');
                if ($outcome->grades[$userid]->locked) {
                    $mform->addElement('static',
                                       'outcome_' . $index . '[' . $userid . ']',
                                       $outcome->name . ':',
                                       $options[$outcome->grades[$userid]->grade]);
                } else {
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
        $usergrade = get_string('notgraded', 'assign');
        if (has_all_capabilities($capabilitylist, $this->get_course_context())) {
            $urlparams = array('id'=>$this->get_course()->id);
            $url = new moodle_url('/grade/report/grader/index.php', $urlparams);
            if (isset($gradinginfo->items[0]->grades[$userid]->grade)) {
                $usergrade = $gradinginfo->items[0]->grades[$userid]->str_grade;
            }
            $gradestring = $this->get_renderer()->action_link($url, $usergrade);
        } else {
            if (isset($gradinginfo->items[0]->grades[$userid]) &&
                    !$gradinginfo->items[0]->grades[$userid]->hidden) {
                $usergrade = $gradinginfo->items[0]->grades[$userid]->str_grade;
            }
            $gradestring = $usergrade;
        }

        if ($this->get_instance()->markingworkflow) {
            $states = $this->get_marking_workflow_states_for_current_user();
            $options = array('' => get_string('markingworkflowstatenotmarked', 'assign')) + $states;
            $mform->addElement('select', 'workflowstate', get_string('markingworkflowstate', 'assign'), $options);
            $mform->addHelpButton('workflowstate', 'markingworkflowstate', 'assign');
            $gradingstatus = $this->get_grading_status($userid);
            if ($gradingstatus != ASSIGN_MARKING_WORKFLOW_STATE_RELEASED) {
                if ($grade->grade && $grade->grade != -1) {
                    $assigngradestring = html_writer::span(
                        make_grades_menu($settings->grade)[grade_floatval($grade->grade)], 'currentgrade'
                    );
                    $label = get_string('currentassigngrade', 'assign');
                    $mform->addElement('static', 'currentassigngrade', $label, $assigngradestring);
                }
            }
        }

        if ($this->get_instance()->markingworkflow &&
            $this->get_instance()->markingallocation &&
            has_capability('mod/assign:manageallocations', $this->context)) {

            list($sort, $params) = users_order_by_sql('u');
            // Only enrolled users could be assigned as potential markers.
            $markers = get_enrolled_users($this->context, 'mod/assign:grade', 0, 'u.*', $sort);
            $markerlist = array('' =>  get_string('choosemarker', 'assign'));
            $viewfullnames = has_capability('moodle/site:viewfullnames', $this->context);
            foreach ($markers as $marker) {
                $markerlist[$marker->id] = fullname($marker, $viewfullnames);
            }
            $mform->addElement('select', 'allocatedmarker', get_string('allocatedmarker', 'assign'), $markerlist);
            $mform->addHelpButton('allocatedmarker', 'allocatedmarker', 'assign');
            $mform->disabledIf('allocatedmarker', 'workflowstate', 'eq', ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW);
            $mform->disabledIf('allocatedmarker', 'workflowstate', 'eq', ASSIGN_MARKING_WORKFLOW_STATE_INREVIEW);
            $mform->disabledIf('allocatedmarker', 'workflowstate', 'eq', ASSIGN_MARKING_WORKFLOW_STATE_READYFORRELEASE);
            $mform->disabledIf('allocatedmarker', 'workflowstate', 'eq', ASSIGN_MARKING_WORKFLOW_STATE_RELEASED);
        }

        $gradestring = '<span class="currentgrade">' . $gradestring . '</span>';
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
        $mform->setType('useridlistid', PARAM_ALPHANUM);
        $mform->addElement('hidden', 'attemptnumber', $attemptnumber);
        $mform->setType('attemptnumber', PARAM_INT);
        $mform->addElement('hidden', 'ajax', optional_param('ajax', 0, PARAM_INT));
        $mform->setType('ajax', PARAM_INT);
        $mform->addElement('hidden', 'userid', optional_param('userid', 0, PARAM_INT));
        $mform->setType('userid', PARAM_INT);

        if ($this->get_instance()->teamsubmission) {
            $mform->addElement('header', 'groupsubmissionsettings', get_string('groupsubmissionsettings', 'assign'));
            $mform->addElement('selectyesno', 'applytoall', get_string('applytoteam', 'assign'));
            $mform->setDefault('applytoall', 1);
        }

        // Do not show if we are editing a previous attempt.
        if (($attemptnumber == -1 ||
            ($attemptnumber + 1) == count($this->get_all_submissions($userid))) &&
            $this->get_instance()->attemptreopenmethod != ASSIGN_ATTEMPT_REOPEN_METHOD_NONE) {
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
        if (!$gradingpanel) {
            $mform->addElement('selectyesno', 'sendstudentnotifications', get_string('sendstudentnotifications', 'assign'));
        } else {
            $mform->addElement('hidden', 'sendstudentnotifications', get_string('sendstudentnotifications', 'assign'));
            $mform->setType('sendstudentnotifications', PARAM_BOOL);
        }
        // Get assignment visibility information for student.
        $modinfo = get_fast_modinfo($settings->course, $userid);
        $cm = $modinfo->get_cm($this->get_course_module()->id);

        // Don't allow notification to be sent if the student can't access the assignment,
        // or until in "Released" state if using marking workflow.
        if (!$cm->uservisible) {
            $mform->setDefault('sendstudentnotifications', 0);
            $mform->freeze('sendstudentnotifications');
        } else if ($this->get_instance()->markingworkflow) {
            $mform->setDefault('sendstudentnotifications', 0);
            if (!$gradingpanel) {
                $mform->disabledIf('sendstudentnotifications', 'workflowstate', 'neq', ASSIGN_MARKING_WORKFLOW_STATE_RELEASED);
            }
        } else {
            $mform->setDefault('sendstudentnotifications', $this->get_instance()->sendstudentnotifications);
        }

        $mform->addElement('hidden', 'action', 'submitgrade');
        $mform->setType('action', PARAM_ALPHA);

        if (!$gradingpanel) {

            $buttonarray = array();
            $name = get_string('savechanges', 'assign');
            $buttonarray[] = $mform->createElement('submit', 'savegrade', $name);
            if (!$last) {
                $name = get_string('savenext', 'assign');
                $buttonarray[] = $mform->createElement('submit', 'saveandshownext', $name);
            }
            $buttonarray[] = $mform->createElement('cancel', 'cancelbutton', get_string('cancel'));
            $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
            $mform->closeHeaderBefore('buttonar');
            $buttonarray = array();

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
                $plugin->get_form_elements_for_user($submission, $mform, $data, $userid);
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

        $userid = $data->userid;
        // Team submissions.
        if ($this->get_instance()->teamsubmission) {
            $submission = $this->get_group_submission($userid, 0, false);
        } else {
            $submission = $this->get_user_submission($userid, false);
        }

        // Submission statement.
        $adminconfig = $this->get_admin_config();
        $requiresubmissionstatement = $this->get_instance()->requiresubmissionstatement;

        $draftsenabled = $this->get_instance()->submissiondrafts;
        $submissionstatement = '';

        if ($requiresubmissionstatement) {
            $submissionstatement = $this->get_submissionstatement($adminconfig, $this->get_instance(), $this->get_context());
        }

        // If we get back an empty submission statement, we have to set $requiredsubmisisonstatement to false to prevent
        // that the submission statement checkbox will be displayed.
        if (empty($submissionstatement)) {
            $requiresubmissionstatement = false;
        }

        $mform->addElement('header', 'submission header', get_string('addsubmission', 'mod_assign'));

        // Only show submission statement if we are editing our own submission.
        if ($requiresubmissionstatement && !$draftsenabled && $userid == $USER->id) {
            $mform->addElement('checkbox', 'submissionstatement', '', $submissionstatement);
            $mform->addRule('submissionstatement', get_string('required'), 'required', null, 'client');
        }

        $this->add_plugin_submission_elements($submission, $mform, $data, $userid);

        // Hidden params.
        $mform->addElement('hidden', 'id', $this->get_course_module()->id);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'userid', $userid);
        $mform->setType('userid', PARAM_INT);

        $mform->addElement('hidden', 'action', 'savesubmission');
        $mform->setType('action', PARAM_ALPHA);
    }

    /**
     * Remove any data from the current submission.
     *
     * @param int $userid
     * @return boolean
     */
    public function remove_submission($userid) {
        global $USER;

        if (!$this->can_edit_submission($userid, $USER->id)) {
            $user = core_user::get_user($userid);
            $message = get_string('usersubmissioncannotberemoved', 'assign', fullname($user));
            $this->set_error_message($message);
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
        $submission->status = $submission->attemptnumber ? ASSIGN_SUBMISSION_STATUS_REOPENED : ASSIGN_SUBMISSION_STATUS_NEW;
        $this->update_submission($submission, $userid, false, $this->get_instance()->teamsubmission);

        // Tell each submission plugin we were saved with no data.
        $plugins = $this->get_submission_plugins();
        foreach ($plugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {
                $plugin->remove($submission);
            }
        }

        $completion = new completion_info($this->get_course());
        if ($completion->is_enabled($this->get_course_module()) &&
                $this->get_instance()->completionsubmit) {
            $completion->update_state($this->get_course_module(), COMPLETION_INCOMPLETE, $userid);
        }

        if ($submission->userid != 0) {
            \mod_assign\event\submission_status_updated::create_from_submission($this, $submission)->trigger();
        }
        return true;
    }

    /**
     * Revert to draft.
     *
     * @param int $userid
     * @return boolean
     */
    public function revert_to_draft($userid) {
        global $DB, $USER;

        // Need grade permission.
        require_capability('mod/assign:grade', $this->context);

        if ($this->get_instance()->teamsubmission) {
            $submission = $this->get_group_submission($userid, 0, false);
        } else {
            $submission = $this->get_user_submission($userid, false);
        }

        if (!$submission) {
            return false;
        }
        $submission->status = ASSIGN_SUBMISSION_STATUS_DRAFT;
        $this->update_submission($submission, $userid, false, $this->get_instance()->teamsubmission);

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

        $completion = new completion_info($this->get_course());
        if ($completion->is_enabled($this->get_course_module()) &&
                $this->get_instance()->completionsubmit) {
            $completion->update_state($this->get_course_module(), COMPLETION_INCOMPLETE, $userid);
        }
        \mod_assign\event\submission_status_updated::create_from_submission($this, $submission)->trigger();
        return true;
    }

    /**
     * Remove the current submission.
     *
     * @param int $userid
     * @return boolean
     */
    protected function process_remove_submission($userid = 0) {
        require_sesskey();

        if (!$userid) {
            $userid = required_param('userid', PARAM_INT);
        }

        return $this->remove_submission($userid);
    }

    /**
     * Revert to draft.
     * Uses url parameter userid if userid not supplied as a parameter.
     *
     * @param int $userid
     * @return boolean
     */
    protected function process_revert_to_draft($userid = 0) {
        require_sesskey();

        if (!$userid) {
            $userid = required_param('userid', PARAM_INT);
        }

        return $this->revert_to_draft($userid);
    }

    /**
     * Prevent student updates to this submission
     *
     * @param int $userid
     * @return bool
     */
    public function lock_submission($userid) {
        global $USER, $DB;
        // Need grade permission.
        require_capability('mod/assign:grade', $this->context);

        // Give each submission plugin a chance to process the locking.
        $plugins = $this->get_submission_plugins();
        $submission = $this->get_user_submission($userid, false);

        $flags = $this->get_user_flags($userid, true);
        $flags->locked = 1;
        $this->update_user_flags($flags);

        foreach ($plugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {
                $plugin->lock($submission, $flags);
            }
        }

        $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
        \mod_assign\event\submission_locked::create_from_user($this, $user)->trigger();
        return true;
    }


    /**
     * Set the workflow state for multiple users
     *
     * @return void
     */
    protected function process_set_batch_marking_workflow_state() {
        global $CFG, $DB;

        // Include batch marking workflow form.
        require_once($CFG->dirroot . '/mod/assign/batchsetmarkingworkflowstateform.php');

        $formparams = array(
            'userscount' => 0,  // This form is never re-displayed, so we don't need to
            'usershtml' => '',  // initialise these parameters with real information.
            'markingworkflowstates' => $this->get_marking_workflow_states_for_current_user()
        );

        $mform = new mod_assign_batch_set_marking_workflow_state_form(null, $formparams);

        if ($mform->is_cancelled()) {
            return true;
        }

        if ($formdata = $mform->get_data()) {
            $useridlist = explode(',', $formdata->selectedusers);
            $state = $formdata->markingworkflowstate;

            foreach ($useridlist as $userid) {
                $flags = $this->get_user_flags($userid, true);

                $flags->workflowstate = $state;

                // Clear the mailed flag if notification is requested, the student hasn't been
                // notified previously, the student can access the assignment, and the state
                // is "Released".
                $modinfo = get_fast_modinfo($this->course, $userid);
                $cm = $modinfo->get_cm($this->get_course_module()->id);
                if ($formdata->sendstudentnotifications && $cm->uservisible &&
                        $state == ASSIGN_MARKING_WORKFLOW_STATE_RELEASED) {
                    $flags->mailed = 0;
                }

                $gradingdisabled = $this->grading_disabled($userid);

                // Will not apply update if user does not have permission to assign this workflow state.
                if (!$gradingdisabled && $this->update_user_flags($flags)) {
                    // Update Gradebook.
                    $grade = $this->get_user_grade($userid, true);
                    // Fetch any feedback for this student.
                    $gradebookplugin = $this->get_admin_config()->feedback_plugin_for_gradebook;
                    $gradebookplugin = str_replace('assignfeedback_', '', $gradebookplugin);
                    $plugin = $this->get_feedback_plugin_by_type($gradebookplugin);
                    if ($plugin && $plugin->is_enabled() && $plugin->is_visible()) {
                        $grade->feedbacktext = $plugin->text_for_gradebook($grade);
                        $grade->feedbackformat = $plugin->format_for_gradebook($grade);
                        $grade->feedbackfiles = $plugin->files_for_gradebook($grade);
                    }
                    $this->update_grade($grade);
                    $assign = clone $this->get_instance();
                    $assign->cmidnumber = $this->get_course_module()->idnumber;
                    // Set assign gradebook feedback plugin status.
                    $assign->gradefeedbackenabled = $this->is_gradebook_feedback_enabled();

                    $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
                    \mod_assign\event\workflow_state_updated::create_from_user($this, $user, $state)->trigger();
                }
            }
        }
    }

    /**
     * Set the marking allocation for multiple users
     *
     * @return void
     */
    protected function process_set_batch_marking_allocation() {
        global $CFG, $DB;

        // Include batch marking allocation form.
        require_once($CFG->dirroot . '/mod/assign/batchsetallocatedmarkerform.php');

        $formparams = array(
            'userscount' => 0,  // This form is never re-displayed, so we don't need to
            'usershtml' => ''   // initialise these parameters with real information.
        );

        list($sort, $params) = users_order_by_sql('u');
        // Only enrolled users could be assigned as potential markers.
        $markers = get_enrolled_users($this->get_context(), 'mod/assign:grade', 0, 'u.*', $sort);
        $markerlist = array();
        foreach ($markers as $marker) {
            $markerlist[$marker->id] = fullname($marker);
        }

        $formparams['markers'] = $markerlist;

        $mform = new mod_assign_batch_set_allocatedmarker_form(null, $formparams);

        if ($mform->is_cancelled()) {
            return true;
        }

        if ($formdata = $mform->get_data()) {
            $useridlist = explode(',', $formdata->selectedusers);
            $marker = $DB->get_record('user', array('id' => $formdata->allocatedmarker), '*', MUST_EXIST);

            foreach ($useridlist as $userid) {
                $flags = $this->get_user_flags($userid, true);
                if ($flags->workflowstate == ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW ||
                    $flags->workflowstate == ASSIGN_MARKING_WORKFLOW_STATE_INREVIEW ||
                    $flags->workflowstate == ASSIGN_MARKING_WORKFLOW_STATE_READYFORRELEASE ||
                    $flags->workflowstate == ASSIGN_MARKING_WORKFLOW_STATE_RELEASED) {

                    continue; // Allocated marker can only be changed in certain workflow states.
                }

                $flags->allocatedmarker = $marker->id;

                if ($this->update_user_flags($flags)) {
                    $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
                    \mod_assign\event\marker_updated::create_from_marker($this, $user, $marker)->trigger();
                }
            }
        }
    }


    /**
     * Prevent student updates to this submission.
     * Uses url parameter userid.
     *
     * @param int $userid
     * @return void
     */
    protected function process_lock_submission($userid = 0) {

        require_sesskey();

        if (!$userid) {
            $userid = required_param('userid', PARAM_INT);
        }

        return $this->lock_submission($userid);
    }

    /**
     * Unlock the student submission.
     *
     * @param int $userid
     * @return bool
     */
    public function unlock_submission($userid) {
        global $USER, $DB;

        // Need grade permission.
        require_capability('mod/assign:grade', $this->context);

        // Give each submission plugin a chance to process the unlocking.
        $plugins = $this->get_submission_plugins();
        $submission = $this->get_user_submission($userid, false);

        $flags = $this->get_user_flags($userid, true);
        $flags->locked = 0;
        $this->update_user_flags($flags);

        foreach ($plugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {
                $plugin->unlock($submission, $flags);
            }
        }

        $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
        \mod_assign\event\submission_unlocked::create_from_user($this, $user)->trigger();
        return true;
    }

    /**
     * Unlock the student submission.
     * Uses url parameter userid.
     *
     * @param int $userid
     * @return bool
     */
    protected function process_unlock_submission($userid = 0) {

        require_sesskey();

        if (!$userid) {
            $userid = required_param('userid', PARAM_INT);
        }

        return $this->unlock_submission($userid);
    }

    /**
     * Apply a grade from a grading form to a user (may be called multiple times for a group submission).
     *
     * @param stdClass $formdata - the data from the form
     * @param int $userid - the user to apply the grade to
     * @param int $attemptnumber - The attempt number to apply the grade to.
     * @return void
     */
    protected function apply_grade_to_user($formdata, $userid, $attemptnumber) {
        global $USER, $CFG, $DB;

        $grade = $this->get_user_grade($userid, true, $attemptnumber);
        $originalgrade = $grade->grade;
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
            if (isset($formdata->workflowstate) || isset($formdata->allocatedmarker)) {
                $flags = $this->get_user_flags($userid, true);
                $oldworkflowstate = $flags->workflowstate;
                $flags->workflowstate = isset($formdata->workflowstate) ? $formdata->workflowstate : $flags->workflowstate;
                $flags->allocatedmarker = isset($formdata->allocatedmarker) ? $formdata->allocatedmarker : $flags->allocatedmarker;
                if ($this->update_user_flags($flags) &&
                        isset($formdata->workflowstate) &&
                        $formdata->workflowstate !== $oldworkflowstate) {
                    $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
                    \mod_assign\event\workflow_state_updated::create_from_user($this, $user, $formdata->workflowstate)->trigger();
                }
            }
        }
        $grade->grader= $USER->id;

        $adminconfig = $this->get_admin_config();
        $gradebookplugin = $adminconfig->feedback_plugin_for_gradebook;

        $feedbackmodified = false;

        // Call save in plugins.
        foreach ($this->feedbackplugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {
                $gradingmodified = $plugin->is_feedback_modified($grade, $formdata);
                if ($gradingmodified) {
                    if (!$plugin->save($grade, $formdata)) {
                        $result = false;
                        print_error($plugin->get_error());
                    }
                    // If $feedbackmodified is true, keep it true.
                    $feedbackmodified = $feedbackmodified || $gradingmodified;
                }
                if (('assignfeedback_' . $plugin->get_type()) == $gradebookplugin) {
                    // This is the feedback plugin chose to push comments to the gradebook.
                    $grade->feedbacktext = $plugin->text_for_gradebook($grade);
                    $grade->feedbackformat = $plugin->format_for_gradebook($grade);
                    $grade->feedbackfiles = $plugin->files_for_gradebook($grade);
                }
            }
        }

        // We do not want to update the timemodified if no grade was added.
        if (!empty($formdata->addattempt) ||
                ($originalgrade !== null && $originalgrade != -1) ||
                ($grade->grade !== null && $grade->grade != -1) ||
                $feedbackmodified) {
            $this->update_grade($grade, !empty($formdata->addattempt));
        }

        // We never send notifications if we have marking workflow and the grade is not released.
        if ($this->get_instance()->markingworkflow &&
                isset($formdata->workflowstate) &&
                $formdata->workflowstate != ASSIGN_MARKING_WORKFLOW_STATE_RELEASED) {
            $formdata->sendstudentnotifications = false;
        }

        // Note the default if not provided for this option is true (e.g. webservices).
        // This is for backwards compatibility.
        if (!isset($formdata->sendstudentnotifications) || $formdata->sendstudentnotifications) {
            $this->notify_grade_modified($grade, true);
        }
    }


    /**
     * Save outcomes submitted from grading form.
     *
     * @param int $userid
     * @param stdClass $formdata
     * @param int $sourceuserid The user ID under which the outcome data is accessible. This is relevant
     *                          for an outcome set to a user but applied to an entire group.
     */
    protected function process_outcomes($userid, $formdata, $sourceuserid = null) {
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
                $sourceuserid = $sourceuserid !== null ? $sourceuserid : $userid;
                if (isset($formdata->{$name}[$sourceuserid]) &&
                        $oldoutcome->grades[$userid]->grade != $formdata->{$name}[$sourceuserid]) {
                    $data[$index] = $formdata->{$name}[$sourceuserid];
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
     * If the requirements are met - reopen the submission for another attempt.
     * Only call this function when grading the latest attempt.
     *
     * @param int $userid The userid.
     * @param stdClass $submission The submission (may be a group submission).
     * @param bool $addattempt - True if the "allow another attempt" checkbox was checked.
     * @return bool - true if another attempt was added.
     */
    protected function reopen_submission_if_required($userid, $submission, $addattempt) {
        $instance = $this->get_instance();
        $maxattemptsreached = !empty($submission) &&
                              $submission->attemptnumber >= ($instance->maxattempts - 1) &&
                              $instance->maxattempts != ASSIGN_UNLIMITED_ATTEMPTS;
        $shouldreopen = false;
        if ($instance->attemptreopenmethod == ASSIGN_ATTEMPT_REOPEN_METHOD_UNTILPASS) {
            // Check the gradetopass from the gradebook.
            $gradeitem = $this->get_grade_item();
            if ($gradeitem) {
                $gradegrade = grade_grade::fetch(array('userid' => $userid, 'itemid' => $gradeitem->id));

                // Do not reopen if is_passed returns null, e.g. if there is no pass criterion set.
                if ($gradegrade && ($gradegrade->is_passed() === false)) {
                    $shouldreopen = true;
                }
            }
        }
        if ($instance->attemptreopenmethod == ASSIGN_ATTEMPT_REOPEN_METHOD_MANUAL &&
                !empty($addattempt)) {
            $shouldreopen = true;
        }
        if ($shouldreopen && !$maxattemptsreached) {
            $this->add_attempt($userid);
            return true;
        }
        return false;
    }

    /**
     * Save grade update.
     *
     * @param int $userid
     * @param  stdClass $data
     * @return bool - was the grade saved
     */
    public function save_grade($userid, $data) {

        // Need grade permission.
        require_capability('mod/assign:grade', $this->context);

        $instance = $this->get_instance();
        $submission = null;
        if ($instance->teamsubmission) {
            // We need to know what the most recent group submission is.
            // Specifically when determining if we are adding another attempt (we only want to add one attempt per team),
            // and when deciding if we need to update the gradebook with an edited grade.
            $mostrecentsubmission = $this->get_group_submission($userid, 0, false, -1);
            $this->set_most_recent_team_submission($mostrecentsubmission);
            // Get the submission that we are saving grades for. The data attempt number determines which submission attempt.
            $submission = $this->get_group_submission($userid, 0, false, $data->attemptnumber);
        } else {
            $submission = $this->get_user_submission($userid, false, $data->attemptnumber);
        }
        if ($instance->teamsubmission && !empty($data->applytoall)) {
            $groupid = 0;
            if ($this->get_submission_group($userid)) {
                $group = $this->get_submission_group($userid);
                if ($group) {
                    $groupid = $group->id;
                }
            }
            $members = $this->get_submission_group_members($groupid, true, $this->show_only_active_users());
            foreach ($members as $member) {
                // We only want to update the grade for this group submission attempt. The data attempt number could be
                // -1 which may end up in additional attempts being created for each group member instead of just one
                // additional attempt for the group.
                $this->apply_grade_to_user($data, $member->id, $submission->attemptnumber);
                $this->process_outcomes($member->id, $data, $userid);
            }
        } else {
            $this->apply_grade_to_user($data, $userid, $data->attemptnumber);

            $this->process_outcomes($userid, $data);
        }

        return true;
    }

    /**
     * Save grade.
     *
     * @param  moodleform $mform
     * @return bool - was the grade saved
     */
    protected function process_save_grade(&$mform) {
        global $CFG, $SESSION;
        // Include grade form.
        require_once($CFG->dirroot . '/mod/assign/gradeform.php');

        require_sesskey();

        $instance = $this->get_instance();
        $rownum = required_param('rownum', PARAM_INT);
        $attemptnumber = optional_param('attemptnumber', -1, PARAM_INT);
        $useridlistid = optional_param('useridlistid', $this->get_useridlist_key_id(), PARAM_ALPHANUM);
        $userid = optional_param('userid', 0, PARAM_INT);
        if (!$userid) {
            if (empty($SESSION->mod_assign_useridlist[$this->get_useridlist_key($useridlistid)])) {
                // If the userid list is not stored we must not save, as it is possible that the user in a
                // given row position may not be the same now as when the grading page was generated.
                $url = new moodle_url('/mod/assign/view.php', array('id' => $this->get_course_module()->id));
                throw new moodle_exception('useridlistnotcached', 'mod_assign', $url);
            }
            $useridlist = $SESSION->mod_assign_useridlist[$this->get_useridlist_key($useridlistid)];
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

        $gradeformparams = array('rownum' => $rownum,
                                 'useridlistid' => $useridlistid,
                                 'last' => $last,
                                 'attemptnumber' => $attemptnumber,
                                 'userid' => $userid);
        $mform = new mod_assign_grade_form(null,
                                           array($this, $data, $gradeformparams),
                                           'post',
                                           '',
                                           array('class'=>'gradeform'));

        if ($formdata = $mform->get_data()) {
            return $this->save_grade($userid, $formdata);
        } else {
            return false;
        }
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
                // We need to know what the most recent group submission is.
                // Specifically when determining if we are adding another attempt (we only want to add one attempt per team),
                // and when deciding if we need to update the gradebook with an edited grade.
                $currentsubmission = $this->get_group_submission($userid, 0, false, -1);
                $this->set_most_recent_team_submission($currentsubmission);
                $result = $this->process_add_attempt($userid) && $result;
                $groupsprocessed[$groupid] = true;
            }
        }
        return $result;
    }

    /**
     * Check for a sess key and then call add_attempt.
     *
     * @param int $userid int The user to add the attempt for
     * @return bool - true if successful.
     */
    protected function process_add_attempt($userid) {
        require_sesskey();

        return $this->add_attempt($userid);
    }

    /**
     * Add a new attempt for a user.
     *
     * @param int $userid int The user to add the attempt for
     * @return bool - true if successful.
     */
    protected function add_attempt($userid) {
        require_capability('mod/assign:grade', $this->context);

        if ($this->get_instance()->attemptreopenmethod == ASSIGN_ATTEMPT_REOPEN_METHOD_NONE) {
            return false;
        }

        if ($this->get_instance()->teamsubmission) {
            $oldsubmission = $this->get_group_submission($userid, 0, false);
        } else {
            $oldsubmission = $this->get_user_submission($userid, false);
        }

        if (!$oldsubmission) {
            return false;
        }

        // No more than max attempts allowed.
        if ($this->get_instance()->maxattempts != ASSIGN_UNLIMITED_ATTEMPTS &&
            $oldsubmission->attemptnumber >= ($this->get_instance()->maxattempts - 1)) {
            return false;
        }

        // Create the new submission record for the group/user.
        if ($this->get_instance()->teamsubmission) {
            if (isset($this->mostrecentteamsubmission)) {
                // Team submissions can end up in this function for each user (via save_grade). We don't want to create
                // more than one attempt for the whole team.
                if ($this->mostrecentteamsubmission->attemptnumber == $oldsubmission->attemptnumber) {
                    $newsubmission = $this->get_group_submission($userid, 0, true, $oldsubmission->attemptnumber + 1);
                } else {
                    $newsubmission = $this->get_group_submission($userid, 0, false, $oldsubmission->attemptnumber);
                }
            } else {
                debugging('Please use set_most_recent_team_submission() before calling add_attempt', DEBUG_DEVELOPER);
                $newsubmission = $this->get_group_submission($userid, 0, true, $oldsubmission->attemptnumber + 1);
            }
        } else {
            $newsubmission = $this->get_user_submission($userid, true, $oldsubmission->attemptnumber + 1);
        }

        // Set the status of the new attempt to reopened.
        $newsubmission->status = ASSIGN_SUBMISSION_STATUS_REOPENED;

        // Give each submission plugin a chance to process the add_attempt.
        $plugins = $this->get_submission_plugins();
        foreach ($plugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {
                $plugin->add_attempt($oldsubmission, $newsubmission);
            }
        }

        $this->update_submission($newsubmission, $userid, false, $this->get_instance()->teamsubmission);
        $flags = $this->get_user_flags($userid, false);
        if (isset($flags->locked) && $flags->locked) { // May not exist.
            $this->process_unlock_submission($userid);
        }
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

        // When the gradebook asks us for grades - only return the last attempt for each user.
        $params = array('assignid1'=>$assignmentid,
                        'assignid2'=>$assignmentid,
                        'userid'=>$userid);
        $graderesults = $DB->get_recordset_sql('SELECT
                                                    u.id as userid,
                                                    s.timemodified as datesubmitted,
                                                    g.grade as rawgrade,
                                                    g.timemodified as dategraded,
                                                    g.grader as usermodified
                                                FROM {user} u
                                                LEFT JOIN {assign_submission} s
                                                    ON u.id = s.userid and s.assignment = :assignid1 AND
                                                    s.latest = 1
                                                JOIN {assign_grades} g
                                                    ON u.id = g.userid and g.assignment = :assignid2 AND
                                                    g.attemptnumber = s.attemptnumber' .
                                                $where, $params);

        foreach ($graderesults as $result) {
            $gradingstatus = $this->get_grading_status($result->userid);
            if (!$this->get_instance()->markingworkflow || $gradingstatus == ASSIGN_MARKING_WORKFLOW_STATE_RELEASED) {
                $gradebookgrade = clone $result;
                // Now get the feedback.
                if ($gradebookplugin) {
                    $grade = $this->get_user_grade($result->userid, false);
                    if ($grade) {
                        $feedbacktext = $gradebookplugin->text_for_gradebook($grade);
                        if (!empty($feedbacktext)) {
                            $gradebookgrade->feedback = $feedbacktext;
                        }
                        $gradebookgrade->feedbackformat = $gradebookplugin->format_for_gradebook($grade);
                        $gradebookgrade->feedbackfiles = $gradebookplugin->files_for_gradebook($grade);
                    }
                }
                $grades[$gradebookgrade->userid] = $gradebookgrade;
            }
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

        foreach ($users as $user) {
            $record = $DB->get_record('assign_user_mapping',
                                      array('assignment'=>$assignid, 'userid'=>$user->id),
                                     'id');
            if (!$record) {
                $record = new stdClass();
                $record->assignment = $assignid;
                $record->userid = $user->id;
                $DB->insert_record('assign_user_mapping', $record);
            }
        }
    }

    /**
     * Lookup this user id and return the unique id for this assignment.
     *
     * @param int $assignid The assignment id
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

    /**
     * Get the list of marking_workflow states the current user has permission to transition a grade to.
     *
     * @return array of state => description
     */
    public function get_marking_workflow_states_for_current_user() {
        if (!empty($this->markingworkflowstates)) {
            return $this->markingworkflowstates;
        }
        $states = array();
        if (has_capability('mod/assign:grade', $this->context)) {
            $states[ASSIGN_MARKING_WORKFLOW_STATE_INMARKING] = get_string('markingworkflowstateinmarking', 'assign');
            $states[ASSIGN_MARKING_WORKFLOW_STATE_READYFORREVIEW] = get_string('markingworkflowstatereadyforreview', 'assign');
        }
        if (has_any_capability(array('mod/assign:reviewgrades',
                                     'mod/assign:managegrades'), $this->context)) {
            $states[ASSIGN_MARKING_WORKFLOW_STATE_INREVIEW] = get_string('markingworkflowstateinreview', 'assign');
            $states[ASSIGN_MARKING_WORKFLOW_STATE_READYFORRELEASE] = get_string('markingworkflowstatereadyforrelease', 'assign');
        }
        if (has_any_capability(array('mod/assign:releasegrades',
                                     'mod/assign:managegrades'), $this->context)) {
            $states[ASSIGN_MARKING_WORKFLOW_STATE_RELEASED] = get_string('markingworkflowstatereleased', 'assign');
        }
        $this->markingworkflowstates = $states;
        return $this->markingworkflowstates;
    }

    /**
     * Check is only active users in course should be shown.
     *
     * @return bool true if only active users should be shown.
     */
    public function show_only_active_users() {
        global $CFG;

        if (is_null($this->showonlyactiveenrol)) {
            $defaultgradeshowactiveenrol = !empty($CFG->grade_report_showonlyactiveenrol);
            $this->showonlyactiveenrol = get_user_preferences('grade_report_showonlyactiveenrol', $defaultgradeshowactiveenrol);

            if (!is_null($this->context)) {
                $this->showonlyactiveenrol = $this->showonlyactiveenrol ||
                            !has_capability('moodle/course:viewsuspendedusers', $this->context);
            }
        }
        return $this->showonlyactiveenrol;
    }

    /**
     * Return true is user is active user in course else false
     *
     * @param int $userid
     * @return bool true is user is active in course.
     */
    public function is_active_user($userid) {
        return !in_array($userid, get_suspended_userids($this->context, true));
    }

    /**
     * Returns true if gradebook feedback plugin is enabled
     *
     * @return bool true if gradebook feedback plugin is enabled and visible else false.
     */
    public function is_gradebook_feedback_enabled() {
        // Get default grade book feedback plugin.
        $adminconfig = $this->get_admin_config();
        $gradebookplugin = $adminconfig->feedback_plugin_for_gradebook;
        $gradebookplugin = str_replace('assignfeedback_', '', $gradebookplugin);

        // Check if default gradebook feedback is visible and enabled.
        $gradebookfeedbackplugin = $this->get_feedback_plugin_by_type($gradebookplugin);

        if (empty($gradebookfeedbackplugin)) {
            return false;
        }

        if ($gradebookfeedbackplugin->is_visible() && $gradebookfeedbackplugin->is_enabled()) {
            return true;
        }

        // Gradebook feedback plugin is either not visible/enabled.
        return false;
    }

    /**
     * Returns the grading status.
     *
     * @param int $userid the user id
     * @return string returns the grading status
     */
    public function get_grading_status($userid) {
        if ($this->get_instance()->markingworkflow) {
            $flags = $this->get_user_flags($userid, false);
            if (!empty($flags->workflowstate)) {
                return $flags->workflowstate;
            }
            return ASSIGN_MARKING_WORKFLOW_STATE_NOTMARKED;
        } else {
            $attemptnumber = optional_param('attemptnumber', -1, PARAM_INT);
            $grade = $this->get_user_grade($userid, false, $attemptnumber);

            if (!empty($grade) && $grade->grade !== null && $grade->grade >= 0) {
                return ASSIGN_GRADING_STATUS_GRADED;
            } else {
                return ASSIGN_GRADING_STATUS_NOT_GRADED;
            }
        }
    }

    /**
     * The id used to uniquily identify the cache for this instance of the assign object.
     *
     * @return string
     */
    public function get_useridlist_key_id() {
        return $this->useridlistid;
    }

    /**
     * Generates the key that should be used for an entry in the useridlist cache.
     *
     * @param string $id Generate a key for this instance (optional)
     * @return string The key for the id, or new entry if no $id is passed.
     */
    public function get_useridlist_key($id = null) {
        if ($id === null) {
            $id = $this->get_useridlist_key_id();
        }
        return $this->get_course_module()->id . '_' . $id;
    }

    /**
     * Updates and creates the completion records in mdl_course_modules_completion.
     *
     * @param int $teamsubmission value of 0 or 1 to indicate whether this is a group activity
     * @param int $requireallteammemberssubmit value of 0 or 1 to indicate whether all group members must click Submit
     * @param obj $submission the submission
     * @param int $userid the user id
     * @param int $complete
     * @param obj $completion
     *
     * @return null
     */
    protected function update_activity_completion_records($teamsubmission,
                                                          $requireallteammemberssubmit,
                                                          $submission,
                                                          $userid,
                                                          $complete,
                                                          $completion) {

        if (($teamsubmission && $submission->groupid > 0 && !$requireallteammemberssubmit) ||
            ($teamsubmission && $submission->groupid > 0 && $requireallteammemberssubmit &&
             $submission->status == ASSIGN_SUBMISSION_STATUS_SUBMITTED)) {

            $members = groups_get_members($submission->groupid);

            foreach ($members as $member) {
                $completion->update_state($this->get_course_module(), $complete, $member->id);
            }
        } else {
            $completion->update_state($this->get_course_module(), $complete, $userid);
        }

        return;
    }

    /**
     * Update the module completion status (set it viewed) and trigger module viewed event.
     *
     * @since Moodle 3.2
     */
    public function set_module_viewed() {
        $completion = new completion_info($this->get_course());
        $completion->set_module_viewed($this->get_course_module());

        // Trigger the course module viewed event.
        $assigninstance = $this->get_instance();
        $params = [
            'objectid' => $assigninstance->id,
            'context' => $this->get_context()
        ];
        if ($this->is_blind_marking()) {
            $params['anonymous'] = 1;
        }

        $event = \mod_assign\event\course_module_viewed::create($params);

        $event->add_record_snapshot('assign', $assigninstance);
        $event->trigger();
    }

    /**
     * Checks for any grade notices, and adds notifications. Will display on assignment main page and grading table.
     *
     * @return void The notifications API will render the notifications at the appropriate part of the page.
     */
    protected function add_grade_notices() {
        if (has_capability('mod/assign:grade', $this->get_context()) && get_config('assign', 'has_rescaled_null_grades_' . $this->get_instance()->id)) {
            $link = new \moodle_url('/mod/assign/view.php', array('id' => $this->get_course_module()->id, 'action' => 'fixrescalednullgrades'));
            \core\notification::warning(get_string('fixrescalednullgrades', 'mod_assign', ['link' => $link->out()]));
        }
    }

    /**
     * View fix rescaled null grades.
     *
     * @return bool True if null all grades are now fixed.
     */
    protected function fix_null_grades() {
        global $DB;
        $result = $DB->set_field_select(
            'assign_grades',
            'grade',
            ASSIGN_GRADE_NOT_SET,
            'grade <> ? AND grade < 0',
            [ASSIGN_GRADE_NOT_SET]
        );
        $assign = clone $this->get_instance();
        $assign->cmidnumber = $this->get_course_module()->idnumber;
        assign_update_grades($assign);
        return $result;
    }

    /**
     * View fix rescaled null grades.
     *
     * @return void The notifications API will render the notifications at the appropriate part of the page.
     */
    protected function view_fix_rescaled_null_grades() {
        global $OUTPUT;

        $o = '';

        require_capability('mod/assign:grade', $this->get_context());

        $instance = $this->get_instance();

        $o .= $this->get_renderer()->render(
            new assign_header(
                $instance,
                $this->get_context(),
                $this->show_intro(),
                $this->get_course_module()->id
            )
        );

        $confirm = optional_param('confirm', 0, PARAM_BOOL);

        if ($confirm) {
            confirm_sesskey();

            // Fix the grades.
            $this->fix_null_grades();
            unset_config('has_rescaled_null_grades_' . $instance->id, 'assign');

            // Display the notice.
            $o .= $this->get_renderer()->notification(get_string('fixrescalednullgradesdone', 'assign'), 'notifysuccess');
            $url = new moodle_url(
                '/mod/assign/view.php',
                array(
                    'id' => $this->get_course_module()->id,
                    'action' => 'grading'
                )
            );
            $o .= $this->get_renderer()->continue_button($url);
        } else {
            // Ask for confirmation.
            $continue = new \moodle_url('/mod/assign/view.php', array('id' => $this->get_course_module()->id, 'action' => 'fixrescalednullgrades', 'confirm' => true, 'sesskey' => sesskey()));
            $cancel = new \moodle_url('/mod/assign/view.php', array('id' => $this->get_course_module()->id));
            $o .= $OUTPUT->confirm(get_string('fixrescalednullgradesconfirm', 'mod_assign'), $continue, $cancel);
        }

        $o .= $this->view_footer();

        return $o;
    }

    /**
     * Set the most recent submission for the team.
     * The most recent team submission is used to determine if another attempt should be created when allowing another
     * attempt on a group assignment, and whether the gradebook should be updated.
     *
     * @since Moodle 3.4
     * @param stdClass $submission The most recent submission of the group.
     */
    public function set_most_recent_team_submission($submission) {
        $this->mostrecentteamsubmission = $submission;
    }

    /**
     * Return array of valid grading allocation filters for the grading interface.
     *
     * @param boolean $export Export the list of filters for a template.
     * @return array
     */
    public function get_marking_allocation_filters($export = false) {
        $markingallocation = $this->get_instance()->markingworkflow &&
            $this->get_instance()->markingallocation &&
            has_capability('mod/assign:manageallocations', $this->context);
        // Get markers to use in drop lists.
        $markingallocationoptions = array();
        if ($markingallocation) {
            list($sort, $params) = users_order_by_sql('u');
            // Only enrolled users could be assigned as potential markers.
            $markers = get_enrolled_users($this->context, 'mod/assign:grade', 0, 'u.*', $sort);
            $markingallocationoptions[''] = get_string('filternone', 'assign');
            $markingallocationoptions[ASSIGN_MARKER_FILTER_NO_MARKER] = get_string('markerfilternomarker', 'assign');
            $viewfullnames = has_capability('moodle/site:viewfullnames', $this->context);
            foreach ($markers as $marker) {
                $markingallocationoptions[$marker->id] = fullname($marker, $viewfullnames);
            }
        }
        if ($export) {
            $allocationfilter = get_user_preferences('assign_markerfilter', '');
            $result = [];
            foreach ($markingallocationoptions as $option => $label) {
                array_push($result, [
                    'key' => $option,
                    'name' => $label,
                    'active' => ($allocationfilter == $option),
                ]);
            }
            return $result;
        }
        return $markingworkflowoptions;
    }

    /**
     * Return array of valid grading workflow filters for the grading interface.
     *
     * @param boolean $export Export the list of filters for a template.
     * @return array
     */
    public function get_marking_workflow_filters($export = false) {
        $markingworkflow = $this->get_instance()->markingworkflow;
        // Get marking states to show in form.
        $markingworkflowoptions = array();
        if ($markingworkflow) {
            $notmarked = get_string('markingworkflowstatenotmarked', 'assign');
            $markingworkflowoptions[''] = get_string('filternone', 'assign');
            $markingworkflowoptions[ASSIGN_MARKING_WORKFLOW_STATE_NOTMARKED] = $notmarked;
            $markingworkflowoptions = array_merge($markingworkflowoptions, $this->get_marking_workflow_states_for_current_user());
        }
        if ($export) {
            $workflowfilter = get_user_preferences('assign_workflowfilter', '');
            $result = [];
            foreach ($markingworkflowoptions as $option => $label) {
                array_push($result, [
                    'key' => $option,
                    'name' => $label,
                    'active' => ($workflowfilter == $option),
                ]);
            }
            return $result;
        }
        return $markingworkflowoptions;
    }

    /**
     * Return array of valid search filters for the grading interface.
     *
     * @return array
     */
    public function get_filters() {
        $filterkeys = [
            ASSIGN_FILTER_NOT_SUBMITTED,
            ASSIGN_FILTER_DRAFT,
            ASSIGN_FILTER_SUBMITTED,
            ASSIGN_FILTER_REQUIRE_GRADING,
            ASSIGN_FILTER_GRANTED_EXTENSION
        ];

        $current = get_user_preferences('assign_filter', '');

        $filters = [];
        // First is always "no filter" option.
        array_push($filters, [
            'key' => 'none',
            'name' => get_string('filternone', 'assign'),
            'active' => ($current == '')
        ]);

        foreach ($filterkeys as $key) {
            array_push($filters, [
                'key' => $key,
                'name' => get_string('filter' . $key, 'assign'),
                'active' => ($current == $key)
            ]);
        }
        return $filters;
    }

    /**
     * Get the correct submission statement depending on single submisison, team submission or team submission
     * where all team memebers must submit.
     *
     * @param array $adminconfig
     * @param assign $instance
     * @param context $context
     *
     * @return string
     */
    protected function get_submissionstatement($adminconfig, $instance, $context) {
        $submissionstatement = '';

        if (!($context instanceof context)) {
            return $submissionstatement;
        }

        // Single submission.
        if (!$instance->teamsubmission) {
            // Single submission statement is not empty.
            if (!empty($adminconfig->submissionstatement)) {
                // Format the submission statement before its sent. We turn off para because this is going within
                // a form element.
                $options = array(
                    'context' => $context,
                    'para'    => false
                );
                $submissionstatement = format_text($adminconfig->submissionstatement, FORMAT_MOODLE, $options);
            }
        } else { // Team submission.
            // One user can submit for the whole team.
            if (!empty($adminconfig->submissionstatementteamsubmission) && !$instance->requireallteammemberssubmit) {
                // Format the submission statement before its sent. We turn off para because this is going within
                // a form element.
                $options = array(
                    'context' => $context,
                    'para'    => false
                );
                $submissionstatement = format_text($adminconfig->submissionstatementteamsubmission,
                    FORMAT_MOODLE, $options);
            } else if (!empty($adminconfig->submissionstatementteamsubmissionallsubmit) &&
                $instance->requireallteammemberssubmit) {
                // All team members must submit.
                // Format the submission statement before its sent. We turn off para because this is going within
                // a form element.
                $options = array(
                    'context' => $context,
                    'para'    => false
                );
                $submissionstatement = format_text($adminconfig->submissionstatementteamsubmissionallsubmit,
                    FORMAT_MOODLE, $options);
            }
        }

        return $submissionstatement;
    }

    /**
     * Check if time limit for assignment enabled and set up.
     *
     * @param int|null $userid User ID. If null, use global user.
     * @return bool
     */
    public function is_time_limit_enabled(?int $userid = null): bool {
        $instance = $this->get_instance($userid);
        return get_config('assign', 'enabletimelimit') && !empty($instance->timelimit);
    }

    /**
     * Check if an assignment submission is already started and not yet submitted.
     *
     * @param int|null $userid User ID. If null, use global user.
     * @param int $groupid Group ID. If 0, use user id to determine group.
     * @param int $attemptnumber Attempt number. If -1, check latest submission.
     * @return bool
     */
    public function is_attempt_in_progress(?int $userid = null, int $groupid = 0, int $attemptnumber = -1): bool {
        if ($this->get_instance($userid)->teamsubmission) {
            $submission = $this->get_group_submission($userid, $groupid, false, $attemptnumber);
        } else {
            $submission = $this->get_user_submission($userid, false, $attemptnumber);
        }

        // If time limit is enabled, we only assume it is in progress if there is a start time for submission.
        $timedattemptstarted = true;
        if ($this->is_time_limit_enabled($userid)) {
            $timedattemptstarted = !empty($submission) && !empty($submission->timestarted);
        }

        return !empty($submission) && $submission->status !== ASSIGN_SUBMISSION_STATUS_SUBMITTED && $timedattemptstarted;
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
        global $DB;

        $context = context_module::instance($this->cmid);

        if (empty($this->fileid)) {
            if (empty($this->sid) || empty($this->area)) {
                throw new portfolio_caller_exception('invalidfileandsubmissionid', 'mod_assign');
            }

            $submission = $DB->get_record('assign_submission', array('id' => $this->sid));
        } else {
            $submissionid = $DB->get_field('files', 'itemid', array('id' => $this->fileid, 'contextid' => $context->id));
            if ($submissionid) {
                $submission = $DB->get_record('assign_submission', array('id' => $submissionid));
            }
        }

        if (empty($submission)) {
            throw new portfolio_caller_exception('filenotfound');
        } else if ($submission->userid == 0) {
            // This must be a group submission.
            if (!groups_is_member($submission->groupid, $this->user->id)) {
                throw new portfolio_caller_exception('filenotfound');
            }
        } else if ($this->user->id != $submission->userid) {
            throw new portfolio_caller_exception('filenotfound');
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
                                                           $context->get_context_name(),
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
                                                           $context->get_context_name(),
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

/**
 * Logic to happen when a/some group(s) has/have been deleted in a course.
 *
 * @param int $courseid The course ID.
 * @param int $groupid The group id if it is known
 * @return void
 */
function assign_process_group_deleted_in_course($courseid, $groupid = null) {
    global $DB;

    $params = array('courseid' => $courseid);
    if ($groupid) {
        $params['groupid'] = $groupid;
        // We just update the group that was deleted.
        $sql = "SELECT o.id, o.assignid, o.groupid
                  FROM {assign_overrides} o
                  JOIN {assign} assign ON assign.id = o.assignid
                 WHERE assign.course = :courseid
                   AND o.groupid = :groupid";
    } else {
        // No groupid, we update all orphaned group overrides for all assign in course.
        $sql = "SELECT o.id, o.assignid, o.groupid
                  FROM {assign_overrides} o
                  JOIN {assign} assign ON assign.id = o.assignid
             LEFT JOIN {groups} grp ON grp.id = o.groupid
                 WHERE assign.course = :courseid
                   AND o.groupid IS NOT NULL
                   AND grp.id IS NULL";
    }
    $records = $DB->get_records_sql($sql, $params);
    if (!$records) {
        return; // Nothing to do.
    }
    $DB->delete_records_list('assign_overrides', 'id', array_keys($records));
    $cache = cache::make('mod_assign', 'overrides');
    foreach ($records as $record) {
        $cache->delete("{$record->assignid}_g_{$record->groupid}");
    }
}

/**
 * Change the sort order of an override
 *
 * @param int $id of the override
 * @param string $move direction of move
 * @param int $assignid of the assignment
 * @return bool success of operation
 */
function move_group_override($id, $move, $assignid) {
    global $DB;

    // Get the override object.
    if (!$override = $DB->get_record('assign_overrides', ['id' => $id, 'assignid' => $assignid], 'id, sortorder, groupid')) {
        return false;
    }
    // Count the number of group overrides.
    $overridecountgroup = $DB->count_records('assign_overrides', array('userid' => null, 'assignid' => $assignid));

    // Calculate the new sortorder.
    if ( ($move == 'up') and ($override->sortorder > 1)) {
        $neworder = $override->sortorder - 1;
    } else if (($move == 'down') and ($override->sortorder < $overridecountgroup)) {
        $neworder = $override->sortorder + 1;
    } else {
        return false;
    }

    // Retrieve the override object that is currently residing in the new position.
    $params = ['sortorder' => $neworder, 'assignid' => $assignid];
    if ($swapoverride = $DB->get_record('assign_overrides', $params, 'id, sortorder, groupid')) {

        // Swap the sortorders.
        $swapoverride->sortorder = $override->sortorder;
        $override->sortorder     = $neworder;

        // Update the override records.
        $DB->update_record('assign_overrides', $override);
        $DB->update_record('assign_overrides', $swapoverride);

        // Delete cache for the 2 records we updated above.
        $cache = cache::make('mod_assign', 'overrides');
        $cache->delete("{$assignid}_g_{$override->groupid}");
        $cache->delete("{$assignid}_g_{$swapoverride->groupid}");
    }

    reorder_group_overrides($assignid);
    return true;
}

/**
 * Reorder the overrides starting at the override at the given startorder.
 *
 * @param int $assignid of the assigment
 */
function reorder_group_overrides($assignid) {
    global $DB;

    $i = 1;
    if ($overrides = $DB->get_records('assign_overrides', array('userid' => null, 'assignid' => $assignid), 'sortorder ASC')) {
        $cache = cache::make('mod_assign', 'overrides');
        foreach ($overrides as $override) {
            $f = new stdClass();
            $f->id = $override->id;
            $f->sortorder = $i++;
            $DB->update_record('assign_overrides', $f);
            $cache->delete("{$assignid}_g_{$override->groupid}");

            // Update priorities of group overrides.
            $params = [
                'modulename' => 'assign',
                'instance' => $override->assignid,
                'groupid' => $override->groupid
            ];
            $DB->set_field('event', 'priority', $f->sortorder, $params);
        }
    }
}

/**
 * Get the information about the standard assign JavaScript module.
 * @return array a standard jsmodule structure.
 */
function assign_get_js_module() {
    return array(
        'name' => 'mod_assign',
        'fullpath' => '/mod/assign/module.js',
    );
}
