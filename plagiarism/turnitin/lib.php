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

/*
 * @package   plagiarism_turnitin
 * @copyright 2013 iParadigms LLC
 */

use Integrations\PhpSdk\TiiClass;
use Integrations\PhpSdk\TiiSubmission;
use Integrations\PhpSdk\TiiAssignment;
use Integrations\PhpSdk\TiiLTI;

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from a Moodle page.
}

// Constants.
define('PLAGIARISM_TURNITIN_MAX_FILE_UPLOAD_SIZE', 104857600);
define('PLAGIARISM_TURNITIN_NUM_RECORDS_RETURN', 500);
define('PLAGIARISM_TURNITIN_CRON_SUBMISSIONS_LIMIT', 100);
define('PLAGIARISM_TURNITIN_REPORT_GEN_SPEED_NUM_RESUBMISSIONS', 3);
define('PLAGIARISM_TURNITIN_REPORT_GEN_SPEED_NUM_HOURS', 24);
define('PLAGIARISM_TURNITIN_MAX_FILENAME_LENGTH', 180);
define('PLAGIARISM_TURNITIN_COURSE_TITLE_LIMIT', 300);

// Admin Repository constants.
define('PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_STANDARD', 0);
define('PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_EXPANDED', 1);
define('PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_FORCE_STANDARD', 2);
define('PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_FORCE_NO', 3);
define('PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_FORCE_INSTITUTIONAL', 4);

// Submit Papers to Repository constants.
define('PLAGIARISM_TURNITIN_SUBMIT_TO_NO_REPOSITORY', 0);
define('PLAGIARISM_TURNITIN_SUBMIT_TO_STANDARD_REPOSITORY', 1);
define('PLAGIARISM_TURNITIN_SUBMIT_TO_INSTITUTIONAL_REPOSITORY', 2);

// Student privacy constants.
define('PLAGIARISM_TURNITIN_DEFAULT_PSEUDO_DOMAIN', '@tiimoodle.com');
define('PLAGIARISM_TURNITIN_DEFAULT_PSEUDO_FIRSTNAME', get_string('defaultcoursestudent'));

// Define accepted files if the module is not accepting any file type.
global $turnitinacceptedfiles;
$turnitinacceptedfiles = array('.doc', '.docx', '.ppt', '.pptx', '.pps', '.ppsx',
                                '.pdf', '.txt', '.htm', '.html', '.hwp', '.odt',
                                '.wpd', '.ps', '.rtf', '.xls', '.xlsx');

require_once($CFG->libdir.'/gradelib.php');

// Get global class.
require_once($CFG->dirroot.'/plagiarism/lib.php');

// Get helper methods.
require_once($CFG->dirroot.'/plagiarism/turnitin/locallib.php');

// Include plugin classes.
require_once($CFG->dirroot.'/plagiarism/turnitin/classes/turnitin_assignment.class.php');
require_once($CFG->dirroot.'/plagiarism/turnitin/classes/turnitin_view.class.php');
require_once($CFG->dirroot.'/plagiarism/turnitin/classes/turnitin_class.class.php');
require_once($CFG->dirroot.'/plagiarism/turnitin/classes/turnitin_submission.class.php');
require_once($CFG->dirroot.'/plagiarism/turnitin/classes/turnitin_comms.class.php');
require_once($CFG->dirroot.'/plagiarism/turnitin/classes/turnitin_user.class.php');
require_once($CFG->dirroot.'/plagiarism/turnitin/classes/digitalreceipt/pp_receipt_message.php');
require_once($CFG->dirroot.'/plagiarism/turnitin/classes/forms/turnitin_form.class.php');

// Include supported module specific code.
require_once($CFG->dirroot.'/plagiarism/turnitin/classes/modules/turnitin_assign.class.php');
require_once($CFG->dirroot.'/plagiarism/turnitin/classes/modules/turnitin_forum.class.php');
require_once($CFG->dirroot.'/plagiarism/turnitin/classes/modules/turnitin_quiz.class.php');
require_once($CFG->dirroot.'/plagiarism/turnitin/classes/modules/turnitin_workshop.class.php');
require_once($CFG->dirroot.'/plagiarism/turnitin/classes/modules/turnitin_coursework.class.php');

class plagiarism_plugin_turnitin extends plagiarism_plugin {

    /**
     * Static variable to load the function from js files by using js_call_amd only once.
     */
    private static $amdcomponentsloaded = false;

    /**
     * Get the fields to be used in the form to configure each activities Turnitin settings.
     *
     * @return array of settings fields.
     */
    public function get_settings_fields() {
        return array('use_turnitin', 'plagiarism_show_student_report', 'plagiarism_draft_submit',
            'plagiarism_allow_non_or_submissions', 'plagiarism_submitpapersto', 'plagiarism_compare_student_papers',
            'plagiarism_compare_internet', 'plagiarism_compare_journals', 'plagiarism_report_gen',
            'plagiarism_compare_institution', 'plagiarism_exclude_biblio', 'plagiarism_exclude_quoted',
            'plagiarism_exclude_matches', 'plagiarism_exclude_matches_value', 'plagiarism_rubric', 'plagiarism_transmatch');
    }

    /**
     * Get the configuration settings for the plagiarism plugin
     *
     * @return mixed if plugin is enabled then an array of config settings is returned or false if not
     */
    public static function get_config_settings($modulename) {
        $pluginconfig = get_config('plagiarism_turnitin', 'plagiarism_turnitin_'.$modulename);

        return $pluginconfig;
    }

    /**
     * @return mixed the admin config settings for the plugin
     */
    public static function plagiarism_turnitin_admin_config() {
        return get_config('plagiarism_turnitin');
    }

    /**
     * Get the Turnitin settings for a module
     *
     * @param int $cmid - the course module id, if this is 0 the default settings will be retrieved
     * @param bool $uselockedvalues - use locked values in place of saved values
     * @return array of Turnitin settings for a module
     */
    public function get_settings($cmid = null, $uselockedvalues = true) {
        global $DB;
        $defaults = $DB->get_records_menu('plagiarism_turnitin_config', array('cm' => null),     '', 'name,value');
        $settings = $DB->get_records_menu('plagiarism_turnitin_config', array('cm' => $cmid), '', 'name,value');

        // Don't overwrite settings with locked values (only relevant on inital module creation).
        if ($uselockedvalues == false) {
            return $settings;
        }

        // Enforce site wide config locking.
        foreach ($defaults as $key => $value) {
            if (substr($key, -5) !== '_lock') {
                continue;
            }
            if ($value != 1) {
                continue;
            }
            $setting = substr($key, 0, -5);
            $settings[$setting] = $defaults[$setting];
        }

        return $settings;
    }

    /**
     * Get a list of the file upload errors.
     *
     * @param int $offset Number of records to skip.
     * @param int $limit  Max records to return.
     * @param bool $count If true, returns a count of the total number of
     *                    records.
     * @access public
     * @return array|int A list of records, or count when $count is true.
     */
    public function get_file_upload_errors($offset = 0, $limit = 0, $count = false) {
        global $DB;

        $sql = "FROM {plagiarism_turnitin_files} PTF
                LEFT JOIN {user} U ON U.id = PTF.userid
                LEFT JOIN {course_modules} CM ON CM.id = PTF.cm
                LEFT JOIN {modules} M ON CM.module = M.id
                LEFT JOIN {course} C ON CM.course = C.id
                WHERE PTF.statuscode = 'error'";
        $countsql = "SELECT count(1) $sql";
        $selectsql = "SELECT PTF.id, U.firstname, U.lastname, U.email, PTF.cm, M.name AS moduletype,
                            C.id AS courseid, C.fullname AS coursename, PTF.identifier, PTF.submissiontype,
                            PTF.errorcode, PTF.errormsg
                      $sql
                      ORDER BY PTF.id DESC";

        if ($count) {
            return $DB->count_records_sql($countsql);
        }
        return $DB->get_records_sql($selectsql, array(), $offset, $limit);
    }

    /**
     * Check if plugin has been configured with Turnitin account details.
     * @return boolean whether the plugin is configured for Turnitin.
     **/
    public function is_plugin_configured() {
        $config = $this->plagiarism_turnitin_admin_config();

        if (empty($config->plagiarism_turnitin_accountid) ||
            empty($config->plagiarism_turnitin_apiurl) ||
            empty($config->plagiarism_turnitin_secretkey)) {
            return false;
        }

        return true;
    }

    /**
     * Save the form data associated with the plugin
     *
     * @global type $DB
     * @param object $data the form data to save
     */
    public function save_form_data($data) {
        global $DB;

        $moduletiienabled = $this->get_config_settings('mod_'.$data->modulename);
        if (empty($moduletiienabled)) {
            return;
        }

        $settingsfields = $this->get_settings_fields();
        // Get current values.
        $plagiarismvalues = $this->get_settings($data->coursemodule, false);

        foreach ($settingsfields as $field) {
            if (isset($data->$field)) {
                $optionfield = new stdClass();
                $optionfield->cm = $data->coursemodule;
                $optionfield->name = $field;
                $optionfield->value = $data->$field;

                if (isset($plagiarismvalues[$field])) {
                    $optionfield->id = $DB->get_field('plagiarism_turnitin_config', 'id',
                                                 (array('cm' => $data->coursemodule, 'name' => $field)));
                    if (!$DB->update_record('plagiarism_turnitin_config', $optionfield)) {
                        plagiarism_turnitin_print_error('defaultupdateerror', 'plagiarism_turnitin', null, null, __FILE__, __LINE__);
                    }
                } else {
                    $optionfield->config_hash = $optionfield->cm."_".$optionfield->name;
                    if (!$DB->insert_record('plagiarism_turnitin_config', $optionfield)) {
                        plagiarism_turnitin_print_error('defaultinserterror', 'plagiarism_turnitin', null, null, __FILE__, __LINE__);
                    }
                }
            }
        }
    }

    /**
     * Add the Turnitin settings form to an add/edit activity page
     *
     * @param object $mform
     * @param object $context
     * @return type
     */
    public function add_settings_form_to_activity_page($mform, $context, $modulename = "") {
        global $DB, $PAGE, $COURSE;

        if (has_capability('plagiarism/turnitin:enable', $context)) {
            // Get Course module id and values.
            $cmid = optional_param('update', null, PARAM_INT);

            // Return no form if the plugin isn't configured.
            if (!$this->is_plugin_configured()) {
                return;
            }

            // Check if plagiarism plugin is enabled for this module if provided.
            if (!empty($modulename)) {
                $moduletiienabled = $this->get_config_settings($modulename);
                if (empty($moduletiienabled)) {
                    return;
                }
            }

            // Get assignment settings, use default settings on assignment creation.
            $plagiarismvalues = $this->get_settings($cmid);

            /* If Turnitin is disabled and we don't have settings (we're editing an existing assignment
             * that was created without Turnitin enabled)
             * Then we pass NULL for the $cmid to ensure we have the default settings should they enable Turnitin.
             */
            if (empty($plagiarismvalues["use_turnitin"]) && count($plagiarismvalues) <= 2) {
                $savedvalues = $plagiarismvalues;
                $plagiarismvalues = $this->get_settings(null);

                // Ensure we reuse the saved setting for use Turnitin.
                if (isset($savedvalues["use_turnitin"])) {
                    $plagiarismvalues["use_turnitin"] = $savedvalues["use_turnitin"];
                }
            }

            $plagiarismelements = $this->get_settings_fields();

            $turnitinview = new turnitin_view();
            $plagiarismvalues["plagiarism_rubric"] = ( !empty($plagiarismvalues["plagiarism_rubric"]) ) ? $plagiarismvalues["plagiarism_rubric"] : 0;

            // We don't require the settings form on Moodle 3.3's bulk completion feature.
            // We also don't require the settings form on Moodle 4.3's bulk completion feature (MDL-78528).
            if ($PAGE->pagetype != 'course-editbulkcompletion' &&
                $PAGE->pagetype != 'course-editdefaultcompletion' &&
                $PAGE->pagetype != 'course-defaultcompletion') {
                // Check for existing settings and add the form
                $course = turnitin_assignment::get_course_data($COURSE->id, "site");
                $turnitinview->add_elements_to_settings_form($mform, $course, "activity", $modulename, $cmid, $plagiarismvalues["plagiarism_rubric"]);
            }
            $settingsdisplayed = true;

            // Disable all plagiarism elements if turnitin is not enabled.
            foreach ($plagiarismelements as $element) {
                if ($element <> 'use_turnitin') { // Ignore this var.
                    $mform->disabledIf($element, 'use_turnitin', 'eq', 0);
                }
            }

            // Check if files have already been submitted and disable exclude biblio and quoted if turnitin is enabled.
            if ($cmid != 0) {
                if ($DB->record_exists('plagiarism_turnitin_files', array('cm' => $cmid))) {
                    $mform->disabledIf('plagiarism_exclude_biblio', 'use_turnitin');
                    $mform->disabledIf('plagiarism_exclude_quoted', 'use_turnitin');
                }
            }

            // Set the default value for each option as the value we have stored.
            foreach ($plagiarismelements as $element) {
                if (isset($plagiarismvalues[$element])) {
                    $mform->setDefault($element, $plagiarismvalues[$element]);
                }
            }
        }
    }

    /**
     * Remove Turnitin class and assignment links from database
     * so that new classes and assignments will be created.
     *
     * @param type $eventdata
     * @return boolean
     */
    public static function course_reset($eventdata) {
        global $DB, $CFG;
        $data = $eventdata->get_data();
        $courseid = (int)$data['other']['reset_options']['courseid'];
        $resetcourse = true;

        $resetassign = 0;
        $resetassignsubmissions = 0;
        if (!empty($data['other']['reset_options']['reset_assign_submissions'])) {
            $resetassign = $data['other']['reset_options']['reset_assign_submissions'];
            $resetassignsubmissions = $resetassign;
        }
        $resetforumall = 0;
        $resetforum = 0;
        if (!empty($data['other']['reset_options']['reset_forum_all'])) {
            $resetforumall = $data['other']['reset_options']['reset_forum_all'];
            $resetforum = $resetforumall;
        }

        // Get the modules that support the Plagiarism plugin by whether they have a class file.
        $supportedmods = array();
        foreach (scandir($CFG->dirroot.'/plagiarism/turnitin/classes/modules/') as $filename) {
            if (!in_array($filename, array(".", ".."))) {
                $filenamear = explode('.', $filename);
                $classnamear = explode('_', $filenamear[0]); // Split the class name.
                $supportedmods[] = $classnamear[1]; // Set the module name.
            }
        }

        foreach ($supportedmods as $supportedmod) {
            $module = $DB->get_record('modules', array('name' => $supportedmod));
            if ($module === false) {
                continue;
            }

            // Get all the course modules that have Turnitin enabled.
            $sql = "SELECT cm.id
                    FROM {course_modules} cm
                    RIGHT JOIN {plagiarism_turnitin_config} ptc ON cm.id = ptc.cm
                    WHERE cm.module = :moduleid
                    AND cm.course = :courseid
                    AND ptc.name = 'turnitin_assignid'";
            $params = array('courseid' => $courseid, 'moduleid' => $module->id);
            $modules = $DB->get_records_sql($sql, $params);

            if (count($modules) > 0) {
                $reset = "reset".$supportedmod;
                if (!empty($$reset)) {
                    // Remove Plagiarism plugin submissions and assignment id from DB for this module.
                    foreach ($modules as $mod) {
                        $DB->delete_records('plagiarism_turnitin_files', array('cm' => $mod->id));
                        $DB->delete_records('plagiarism_turnitin_config', array('cm' => $mod->id, 'name' => 'turnitin_assignid'));
                    }
                } else {
                    $resetcourse = false;
                }
            }
        }

        // If all turnitin enabled modules for this course have been reset.
        // then remove the Turnitin course id from the database.
        if ($resetcourse) {
            $DB->delete_records('plagiarism_turnitin_courses', array('courseid' => $courseid));
        }

        return true;
    }

    /**
     * Test whether we can connect to Turnitin.
     *
     * Initially only being used if a student is logged in before checking whether they have accepted the EULA.
     */
    public function test_turnitin_connection($workflowcontext = 'site') {
        $turnitincomms = new turnitin_comms();
        $tiiapi = $turnitincomms->initialise_api();

        $class = new TiiClass();
        $class->setTitle('Test finding a class to see if connection works');

        try {
            $tiiapi->findClasses($class);
            return true;
        } catch (Exception $e) {
            $turnitincomms->handle_exceptions($e, 'connecttesterror', false);
            if ($workflowcontext == 'cron') {
                mtrace(get_string('ppeventsfailedconnection', 'plagiarism_turnitin'));
            }
            return false;
        }
    }

    /**
     * Print the Turnitin student disclosure inside the submission page for students to see
     *
     * @global $OUTPUT
     * @global $USER
     * @global $CFG
     * @param $cmid
     * @return string
     */
    public function print_disclosure($cmid) {
        global $OUTPUT, $USER, $DB;

        static $tiiconnection;

        $config = $this->plagiarism_turnitin_admin_config();
        $output = '';

        // Get course details.
        $cm = get_coursemodule_from_id('', $cmid);

        $moduletiienabled = $this->get_config_settings('mod_'.$cm->modname);
        // Exit if Turnitin is not being used for this activity type.
        if (empty($moduletiienabled)) {
            return '';
        }

        $plagiarismsettings = $this->get_settings($cmid);
        // Check Turnitin is enabled for this current module.
        if (empty($plagiarismsettings['use_turnitin'])) {
            return '';
        }

        $this->load_page_components();

        // Show resubmission warning - but not for mod forum.
        if ($cm->modname != 'forum') {
            $tiisubmissions = $DB->get_records('plagiarism_turnitin_files', array('userid' => $USER->id, 'cm' => $cm->id));
            $tiisubmissions = current($tiisubmissions);

            if ($tiisubmissions) {
                $genparams = $this->plagiarism_get_report_gen_speed_params();
                $output .= html_writer::tag('div', get_string('reportgenspeed_resubmission', 'plagiarism_turnitin', $genparams), array('class' => 'tii_genspeednote'));
            }
        }

        // Show agreement.
        if (!empty($config->plagiarism_turnitin_agreement)) {
            $contents = format_text($config->plagiarism_turnitin_agreement, FORMAT_MOODLE, array("noclean" => true));
            $output .= $OUTPUT->box($contents, 'generalbox boxaligncenter', 'intro');
        }

        // Exit here if the plugin is not configured for Turnitin.
        if (!$this->is_plugin_configured()) {
            return $output;
        }

        // Show EULA if necessary and we have a connection to Turnitin.
        if (empty($tiiconnection)) {
            $tiiconnection = $this->test_turnitin_connection();
        }
        if ($tiiconnection) {
            $coursedata = $this->get_course_data($cm->id, $cm->course);

            $user = new turnitin_user($USER->id, "Learner");
            $user->join_user_to_class($coursedata->turnitin_cid);
            $eulaaccepted = ($user->useragreementaccepted == 0) ? $user->get_accepted_user_agreement() : $user->useragreementaccepted;

            if ($eulaaccepted != 1) {
                $eulalink = html_writer::tag('span',
                    get_string('turnitinppulapre', 'plagiarism_turnitin'),
                    array('class' => 'pp_turnitin_eula_link tii_tooltip', 'id' => 'rubric_manager_form')
                );
                $eulaignoredclass = ($eulaaccepted == 0) ? ' pp_turnitin_eula_ignored' : '';
                $eula = html_writer::tag('div', $eulalink, array('class' => 'pp_turnitin_eula'.$eulaignoredclass,
                                            'data-userid' => $user->id));

                $form = turnitin_view::output_launch_form(
                    "useragreement",
                    0,
                    $user->tiiuserid,
                    "Learner",
                    get_string('turnitinppulapre', 'plagiarism_turnitin'),
                    false
                );
                $form .= " ".get_string('noscriptula', 'plagiarism_turnitin');

                $noscripteula = html_writer::tag('noscript', $form, array('class' => 'warning turnitin_ula_noscript'));
            }

            // Show EULA launcher and form placeholder.
            if (!empty($eula)) {
                $output .= $eula.$noscripteula;

                $turnitincomms = new turnitin_comms();
                $turnitincall = $turnitincomms->initialise_api();

                $customdata = array("disable_form_change_checker" => true,
                                    "elements" => array(array('html', $OUTPUT->box('', '', 'useragreement_inputs'))));

                $eulaform = new turnitin_form($turnitincall->getApiBaseUrl().TiiLTI::EULAENDPOINT, $customdata,
                                                        'POST', $target = 'eulaWindow', array('id' => 'eula_launch'));
                $output .= $OUTPUT->box($eulaform->display(), 'tii_useragreement_form', 'useragreement_form');
            }
        }

        if ($config->plagiarism_turnitin_usegrademark && !empty($plagiarismsettings["plagiarism_rubric"])) {

            // Update assignment in case rubric is not stored in Turnitin yet.
            $this->sync_tii_assignment($cm, $coursedata->turnitin_cid);

            $rubricviewlink = html_writer::tag('span',
                get_string('launchrubricview', 'plagiarism_turnitin'),
                array('class' => 'rubric_view rubric_view_pp_launch_upload tii_tooltip',
                    'data-courseid' => $cm->course,
                    'data-cmid' => $cm->id,
                    'title' => get_string('launchrubricview',
                        'plagiarism_turnitin'), 'id' => 'rubric_manager_form'
                )
            );
            $rubricviewlink = html_writer::tag('div', $rubricviewlink, array('class' => 'row_rubric_view'));

            $output .= html_writer::tag('div', $rubricviewlink, array('class' => 'tii_links_container tii_disclosure_links'));
        }

        return $output;
    }

    /**
     * Load JS needed by the page.
     */
    public function load_page_components() {
        global $PAGE, $CFG;
        // The function from js files by using js_call_amd will be loaded only once.
        if (static::$amdcomponentsloaded) {
            return;
        }

        $PAGE->requires->string_for_js('turnitin_score_refresh_alert', 'plagiarism_turnitin');
        
        $PAGE->requires->js_call_amd('plagiarism_turnitin/open_viewer', 'origreport_open');
        $PAGE->requires->js_call_amd('plagiarism_turnitin/open_viewer', 'grademark_open');
        // Moodle 4.3 uses a new Modal dialog that is not compatible with older versions of Moodle. Depending on the user's
        // version of Moodle, we will use the supported versin of Modal dialog
        if ($CFG->version >= 2023100900) {
            $PAGE->requires->js_call_amd('plagiarism_turnitin/new_eula_modal', 'newEulaLaunch');
            $PAGE->requires->js_call_amd('plagiarism_turnitin/new_peermark', 'newPeermarkLaunch');

        } else {
            $PAGE->requires->js_call_amd('plagiarism_turnitin/eula', 'eulaLaunch');
            $PAGE->requires->js_call_amd('plagiarism_turnitin/peermark', 'peermarkLaunch');
            $PAGE->requires->js_call_amd('plagiarism_turnitin/rubric', 'rubric');
        }

        $PAGE->requires->js_call_amd('plagiarism_turnitin/resend_submission', 'resendSubmission');

        $PAGE->requires->string_for_js('closebutton', 'plagiarism_turnitin');
        $PAGE->requires->string_for_js('loadingdv', 'plagiarism_turnitin');
        if (!static::$amdcomponentsloaded) {
            static::$amdcomponentsloaded = true;
        }
    }

    /**
     * Get Moodle and Turnitin Course data
     */
    public function get_course_data($cmid, $courseid, $workflowcontext = 'site') {
        $coursedata = turnitin_assignment::get_course_data($courseid, $workflowcontext);

        // Get add from querystring to work out module type.
        $add = optional_param('add', '', PARAM_TEXT);

        if (empty($coursedata->turnitin_cid)) {
            // Course may have existed in a previous incarnation of this plugin.
            // Get this and save it in courses table if so.
            if ($turnitincid = $this->get_previous_course_id($cmid, $courseid)) {
                $coursedata->turnitin_cid = $turnitincid;
                $coursedata = $this->migrate_previous_course($coursedata, $turnitincid);
            } else {
                // Otherwise create new course in Turnitin if it doesn't exist.
                if ($cmid == 0) {
                    $tiicoursedata = $this->create_tii_course($cmid, $add, $coursedata, $workflowcontext);
                } else {
                    $cm = get_coursemodule_from_id('', $cmid);
                    $tiicoursedata = $this->create_tii_course($cmid, $cm->modname, $coursedata, $workflowcontext);
                }
                $coursedata->turnitin_cid = (!empty($tiicoursedata->turnitin_cid)) ? $tiicoursedata->turnitin_cid : null;
                $coursedata->turnitin_ctl = (!empty($tiicoursedata->turnitin_ctl)) ? $tiicoursedata->turnitin_ctl : "";
            }
        }

        return $coursedata;
    }

    /**
     *
     * @global type $CFG
     * @param type $linkarray
     * @return type
     */
    public function get_links($linkarray) {
        global $CFG, $DB, $OUTPUT, $USER;

        $output = "";

        // Don't show links for certain file types as they won't have been submitted to Turnitin.
        if (!empty($linkarray["file"])) {
            $file = $linkarray["file"];
            $filearea = $file->get_filearea();
            $nonsubmittingareas = array("feedback_files", "introattachment");
            if (in_array($filearea, $nonsubmittingareas)) {
                return $output;
            }
        }

        $component = (!empty($linkarray['component'])) ? $linkarray['component'] : "";

        // Exit if this is a quiz and quizzes are disabled.
        if ($component == "qtype_essay" && empty($this->get_config_settings('mod_quiz'))) {
            return $output;
        }

        // If this is a quiz, retrieve the cmid
        if ($component == "qtype_essay" && !empty($linkarray['area']) && empty($linkarray['cmid'])) {
            $questions = question_engine::load_questions_usage_by_activity($linkarray['area']);

            // Try to get cm using the questions owning context.
            $context = $questions->get_owning_context();
            if (empty($linkarray['cmid']) && $context->contextlevel == CONTEXT_MODULE) {
                $linkarray['cmid'] = $context->instanceid;
            }
        }

        // Set static variables.
        static $cm;
        static $forum;
        if (empty($cm)) {
            $cm = get_coursemodule_from_id('', $linkarray["cmid"]);

            if ($cm->modname == 'forum') {
                if (! $forum = $DB->get_record("forum", array("id" => $cm->instance))) {
                    print_error('invalidforumid', 'forum');
                }
            }
        }

        static $config;
        if (empty($config)) {
            $config = $this->plagiarism_turnitin_admin_config();
        }

        // Retrieve the plugin settings for this module.
        static $plagiarismsettings = null;
        if (is_null($plagiarismsettings)) {
            $plagiarismsettings = $this->get_settings($linkarray["cmid"]);
        }

        // Is this plugin enabled for this activity type.
        static $moduletiienabled;
        if (empty($moduletiienabled)) {
            $moduletiienabled = $this->get_config_settings('mod_'.$cm->modname);
        }

        // Exit if Turnitin is not being used for this module or activity type.
        if (empty($moduletiienabled) || empty($plagiarismsettings['use_turnitin'])) {
            return $output;
        }

        static $moduledata;
        if (empty($moduledata)) {
            $moduledata = $DB->get_record($cm->modname, array('id' => $cm->instance));
        }

        static $context;
        if (empty($context)) {
            $context = context_course::instance($cm->course);
        }

        static $coursedata;
        if (empty($coursedata)) {
            $coursedata = $this->get_course_data($cm->id, $cm->course);
        }

        // Create module object.
        $moduleclass = "turnitin_".$cm->modname;
        $moduleobject = new $moduleclass;

        // Work out if logged in user is a tutor on this activity module.
        static $istutor;
        if (empty($istutor)) {
            $ctx_module = context_module::instance($cm->id);
            $istutor = $moduleobject->is_tutor($ctx_module);
        }

        // Define the timestamp for updating Peermark Assignments.
        if (empty($_SESSION["updated_pm"][$cm->id]) && $config->plagiarism_turnitin_enablepeermark) {
            $_SESSION["updated_pm"][$cm->id] = (time() - (60 * 5));
        }

        // If a text submission has been made, we can only display links for current attempts so don't show links previous attempts.
        // This will need to be reworked when linkarray contains submission id.
        static $contentdisplayed;
        if ($cm->modname == 'assign' && !empty($linkarray["content"]) && $contentdisplayed == true) {
            return $output;
        }

        if ((!empty($linkarray["file"]) || !empty($linkarray["content"])) && !empty($linkarray["cmid"])) {

            $this->load_page_components();

            $identifier = '';
            $itemid = 0;

            // Get File or Content information.
            $submittinguser = $linkarray['userid'];
            if (!empty($linkarray["file"])) {
                $identifier = $file->get_pathnamehash();
                $itemid = $file->get_itemid();
                $submissiontype = 'file';
            } else if (!empty($linkarray["content"])) {
                // Get turnitin text content details.
                $submissiontype = 'text_content';
                if ($cm->modname == 'forum') {
                    $submissiontype = 'forum_post';
                } else if ($cm->modname == 'quiz') {
                    $submissiontype = 'quiz_answer';
                }
                $content = $moduleobject->set_content($linkarray, $cm);
                $identifier = ($submissiontype === 'quiz_answer') ? sha1($content.$linkarray["itemid"]) : sha1($content);
            }

            // Group submissions where all students have to submit sets userid to 0.
            if ($linkarray['userid'] == 0 && !$istutor) {
                $linkarray['userid'] = $USER->id;
            }

            /*
               The author will be incorrect if an instructor submits on behalf of a student who is in a group.
               To get around this, we get the group ID, get the group members and set the author as the first student in the group.
            */
            $plagiarismfile = null;
            $moodlesubmission = $DB->get_record('assign_submission', array('id' => $itemid), 'id, groupid');
            if ((!empty($moodlesubmission->groupid)) && ($cm->modname == "assign")) {
                $plagiarismfiles = $DB->get_records('plagiarism_turnitin_files', ['itemid' => $itemid, 'cm' => $cm->id, 'identifier' => $identifier],
                    'lastmodified DESC', '*', 0, 1);
                $plagiarismfile = reset($plagiarismfiles);
                $author = $plagiarismfile->userid;
                $linkarray['userid'] = $author;
            } else {
                // Get correct user id that submission is for rather than who submitted, this only affects file submissions
                // post Moodle 2.7 which is problematic as teachers can submit on behalf of students.
                $author = $linkarray['userid'];
                if ($itemid != 0) {
                    $author = $moduleobject->get_author($itemid);
                    $linkarray['userid'] = (!empty($author)) ? $author : $linkarray['userid'];
                }
            }

            // Show the EULA for a student if necessary.
            if ($linkarray["userid"] == $USER->id) {
                $eula = "";

                static $userid;
                if (empty($userid)) {
                    $userid = 0;
                }

                // Show EULA if necessary and we have a connection to Turnitin.
                if ($userid != $linkarray["userid"]) {
                    static $eulashown;
                    if (empty($eulashown)) {
                        $eulashown = false;
                    }

                    $user = new turnitin_user($USER->id, "Learner");
                    $success = $user->join_user_to_class($coursedata->turnitin_cid);

                    // Variable $success is false if there is no Turnitin connection and null if user has previously been enrolled.
                    if ((is_null($success) || $success === true) && $eulashown == false) {
                        $eulaaccepted = ($user->useragreementaccepted == 0) ? $user->get_accepted_user_agreement() : $user->useragreementaccepted;
                        $userid = $linkarray["userid"];

                        // Uncomment if ability to submit to Turnitin previously uploaded files will be implemented.
                        // if ($eulaaccepted != 1) {
                        //     $eulalink = html_writer::tag('span',
                        //         get_string('turnitinppulapost', 'plagiarism_turnitin'),
                        //         array('class' => 'pp_turnitin_eula_link tii_tooltip', 'id' => 'rubric_manager_form')
                        //     );
                        //     $eula = html_writer::tag('div', $eulalink, array('class' => 'pp_turnitin_eula', 'data-userid' => $user->id));
                        // }

                        // Show EULA launcher and form placeholder.
                        if (!empty($eula)) {
                            $output .= $eula;

                            $turnitincomms = new turnitin_comms();
                            $turnitincall = $turnitincomms->initialise_api();

                            $customdata = array("disable_form_change_checker" => true,
                                    "elements" => array(array('html', $OUTPUT->box('', '', 'useragreement_inputs'))));
                            $eulaform = new turnitin_form(
                                $turnitincall->getApiBaseUrl().TiiLTI::EULAENDPOINT,
                                $customdata,
                                'POST',
                                $target = 'eulaWindow',
                                array('id' => 'eula_launch')
                            );
                            $output .= $OUTPUT->box($eulaform->display(), 'tii_useragreement_form', 'useragreement_form');
                            $eulashown = true;
                        }
                    }
                }
            }

            // Check whether submission is a group submission - only applicable to assignment and coursework module.
            // If it's a group submission then other users in the group should be able to see the originality score
            // They can not open the DV though.
            $submissionusers = array($linkarray["userid"]);
            switch ($cm->modname) {
                case "assign":
                    if ($moduledata->teamsubmission) {
                        $assignment = new assign($context, $cm, null);
                        if ($group = $assignment->get_submission_group($linkarray["userid"])) {
                            $users = groups_get_members($group->id);
                            $submissionusers = array_keys($users);
                        }
                    }
                    break;

                case "coursework":
                    if ($moduledata->use_groups) {
                        $coursework = new \mod_coursework\models\coursework($moduledata->id);

                        $user = $DB->get_record('user', array('id' => $linkarray["userid"]));
                        $user = mod_coursework\models\user::find($user);
                        if ($group = $coursework->get_student_group($user)) {
                            $users = groups_get_members($group->id);
                            $submissionusers = array_keys($users);
                        }
                    }
            }

            // Proceed to displaying links for submissions.
            if ($istutor || in_array($USER->id, $submissionusers)) {

                // Prevent text content links being displayed for previous attempts as we have no way of getting the data.
                if (!empty($linkarray["content"]) && $linkarray["userid"] == $USER->id) {
                    $contentdisplayed = true;
                }

                // Get turnitin file details.
                if (is_null($plagiarismfile)) {
                    $plagiarismfiles = $DB->get_records('plagiarism_turnitin_files', array('userid' => $linkarray["userid"],
                            'cm' => $linkarray["cmid"], 'identifier' => $identifier),
                            'lastmodified DESC', '*', 0, 1);
                    $plagiarismfile = current($plagiarismfiles);
                }

                // Populate gradeitem query.
                $gradeitemqueryarray = array(
                                    'iteminstance' => $cm->instance,
                                    'itemmodule' => $cm->modname,
                                    'courseid' => $cm->course,
                                    'itemnumber' => 0
                                );

                // Get grade item and work out whether grades have been released for viewing.
                $gradesreleased = true;
                if ($gradeitem = $DB->get_record('grade_items', $gradeitemqueryarray)) {
                    switch ($gradeitem->hidden) {
                        case 1:
                            $gradesreleased = false;
                            break;
                        default:
                            $gradesreleased = ($gradeitem->hidden >= time()) ? false : true;
                            break;
                    }

                    // Give Marking workflow higher priority than gradebook hidden date.
                    if ($cm->modname == 'assign' && !empty($moduledata->markingworkflow)) {
                        $gradesreleased = $DB->record_exists(
                                                    'assign_user_flags',
                                                    array(
                                                        'userid' => $linkarray["userid"],
                                                        'assignment' => $cm->instance,
                                                        'workflowstate' => 'released'
                                                    ));
                    }
                }

                $currentgradequery = false;
                if ($gradeitem) {
                    $currentgradequery = $moduleobject->get_current_gradequery($linkarray["userid"], $cm->instance, $gradeitem->id);
                }

                // Display links to OR, GradeMark and show relevant errors.
                if ($plagiarismfile) {

                    if ($plagiarismfile->statuscode == 'success' || ($plagiarismfile->statuscode == 'error' && $plagiarismfile->errorcode == 13)) {
                        if ($istutor || $linkarray["userid"] == $USER->id) {
                            $output .= html_writer::tag('div',
                                            $OUTPUT->pix_icon('turnitin-icon',
                                                get_string('turnitinid', 'plagiarism_turnitin').': '.$plagiarismfile->externalid,
                                                'plagiarism_turnitin', array('class' => 'icon_size')).
                                                get_string('turnitinid', 'plagiarism_turnitin').': '.$plagiarismfile->externalid,
                                            array('class' => 'turnitin_status'));
                        }

                        // Show Originality Report score and link.
                        if (($istutor || (in_array($USER->id, $submissionusers) && $plagiarismsettings["plagiarism_show_student_report"])) &&
                            ((is_null($plagiarismfile->orcapable) || $plagiarismfile->orcapable == 1) && !is_null($plagiarismfile->similarityscore))) {

                            // Show score.
                            if ($plagiarismfile->statuscode == "pending") {
                                $orscorehtml = html_writer::tag('div', '&nbsp;', array('title' => get_string('pending', 'plagiarism_turnitin'),
                                                                        'class' => 'tii_tooltip origreport_score score_colour score_colour_'));
                            } else {
                                // Put EN flag if translated matching is on and that is the score used.
                                $transmatch = ($plagiarismfile->transmatch == 1) ? ' EN' : '';

                                if (is_null($plagiarismfile->similarityscore)) {
                                    $score = '&nbsp;';
                                    $titlescore = get_string('pending', 'plagiarism_turnitin');
                                    $class = 'score_colour_';
                                } else {
                                    $score = $plagiarismfile->similarityscore.'%';
                                    $titlescore = $plagiarismfile->similarityscore.'% '.get_string('similarity', 'plagiarism_turnitin');
                                    $roundup = function($n, $x=25) {
                                        return (ceil($n)%$x === 0) ? ceil($n) : round(($n+$x/2)/$x)*$x;
                                    };

                                    $class = 'score_colour_'.$roundup($plagiarismfile->similarityscore);
                                }

                                $orscorehtml = html_writer::tag('div', $score.$transmatch,
                                                array('title' => $titlescore, 'class' => 'tii_tooltip origreport_score score_colour '.$class));
                            }
                            // Put in div placeholder for DV launch form.
                            $orscorehtml .= html_writer::tag('div', '', array('class' => 'launch_form origreport_form_'.$plagiarismfile->externalid));

                            // Add url for launching DV from Forum post.
                            if ($cm->modname == 'forum') {
                                $orscorehtml .= html_writer::tag('div', $CFG->wwwroot.'/plagiarism/turnitin/extras.php?cmid='.$linkarray["cmid"],
                                                            array('class' => 'origreport_forum_launch origreport_forum_launch_'.$plagiarismfile->externalid));
                            }

                            // This class is applied so that only the user who submitted or a tutor can open the DV.
                            $useropenclass = ($USER->id == $linkarray["userid"] || $istutor) ? 'pp_origreport_open' : '';

                            // Output container for OR Score.
                            $ordivclass = 'row_score pp_origreport '.$useropenclass.' origreport_'.$plagiarismfile->externalid.'_'.$linkarray["cmid"];
                            $output .= html_writer::tag('div', $orscorehtml, array('class' => $ordivclass, 'tabindex' => '0', 'role' => 'link'));
                        }

                        if (($plagiarismfile->orcapable == 0 && !is_null($plagiarismfile->orcapable))) {
                            $notorlink = html_writer::tag('div', 'x', array('title' => get_string('notorcapable', 'plagiarism_turnitin'),
                                                                        'class' => 'tii_tooltip score_colour score_colour_ score_no_orcapable'));
                            // This class is applied so that only the user who submitted or a tutor can open the DV.
                            $useropenclass = ($USER->id == $linkarray["userid"] || $istutor) ? 'pp_origreport_open' : '';
                            $output .= html_writer::tag('div', $notorlink, array('class' => 'row_score pp_origreport '.$useropenclass));
                        }

                        // Check if blind marking is on and revealidentities is not set yet.
                        $blindon = (!empty($moduledata->blindmarking) && empty($moduledata->revealidentities));

                        // Check if a grade exists - as $currentgradequery->grade defaults to -1.
                        $gradeexists = false;
                        if (isset($currentgradequery->grade)) {
                            if ($currentgradequery->grade >= 0) {
                                $gradeexists = true;
                            }
                        }

                        // Can grade and feedback be released to this student yet?
                        $released = ((!$blindon) && ($gradesreleased && (!empty($plagiarismfile->gm_feedback) || $gradeexists)));

                        // Show link to open grademark.
                        if ($config->plagiarism_turnitin_usegrademark && ($istutor || ($linkarray["userid"] == $USER->id && $released))) {

                            // Output grademark icon.
                            $gmicon = html_writer::tag('div', $OUTPUT->pix_icon('icon-edit',
                                                                get_string('grademark', 'plagiarism_turnitin'), 'plagiarism_turnitin'),
                                                    array('title' => get_string('grademark', 'plagiarism_turnitin'),
                                                        'class' => 'pp_grademark_open tii_tooltip grademark_'.$plagiarismfile->externalid.
                                                                        '_'.$linkarray["cmid"], 'tabindex' => '0', 'role' => 'link'
                                                    ));

                            // Put in div placeholder for DV launch form.
                            $gmicon .= html_writer::tag('div', '', array('class' => 'launch_form grademark_form_'.$plagiarismfile->externalid));
                            $output .= html_writer::tag('div', $gmicon, array('class' => 'grade_icon'));
                        }

                        // Indicate whether student has viewed the feedback.
                        if ($istutor) {
                            $readicon = "--";
                            if (isset($plagiarismfile->externalid)) {
                                $studentread = (!empty($plagiarismfile->student_read)) ? $plagiarismfile->student_read : 0;
                                if ($studentread > 0) {
                                    $readicon = $OUTPUT->pix_icon('icon-student-read',
                                                        get_string('student_read', 'plagiarism_turnitin').' '.userdate($studentread),
                                                        'plagiarism_turnitin');
                                } else {
                                    $readicon = $OUTPUT->pix_icon('icon-dot', get_string('student_notread', 'plagiarism_turnitin'),
                                                        'plagiarism_turnitin');
                                }
                            }
                            $output .= html_writer::tag('div', $readicon, array('class' => 'student_read_icon'));
                        }

                        // Show link to view rubric for student.
                        if (!$istutor && $config->plagiarism_turnitin_usegrademark && !empty($plagiarismsettings["plagiarism_rubric"])) {
                            // Update assignment in case rubric is not stored in Turnitin yet.
                            $this->sync_tii_assignment($cm, $coursedata->turnitin_cid);

                            $rubricviewlink = html_writer::tag('span', '',
                                array('class' => 'rubric_view rubric_view_pp_launch tii_tooltip',
                                    'data-courseid' => $cm->course,
                                    'data-cmid' => $cm->id,
                                    'title' => get_string('launchrubricview',
                                        'plagiarism_turnitin'), 'id' => 'rubric_view_launch'
                                )
                            );
                            $rubricviewlink = html_writer::tag('div', $rubricviewlink, array('class' => 'row_rubric_view'));

                            $output .= $rubricviewlink;
                        }

                        if ($config->plagiarism_turnitin_enablepeermark) {
                            // If this module is already on Turnitin then refresh and get Peermark Assignments.
                            if (!empty($plagiarismsettings['turnitin_assignid'])) {
                                if ($_SESSION["updated_pm"][$cm->id] <= (time() - (60 * 2))) {
                                    $this->refresh_peermark_assignments($cm, $plagiarismsettings['turnitin_assignid']);
                                    $turnitinassignment = new turnitin_assignment($cm->instance);
                                    $_SESSION["peermark_assignments"][$cm->id] = $turnitinassignment->get_peermark_assignments($plagiarismsettings['turnitin_assignid']);
                                    $_SESSION["updated_pm"][$cm->id] = time();
                                }

                                // Determine if we have any active Peermark Assignments.
                                static $peermarksactive;
                                if (!isset($peermarksactive)) {
                                    $peermarksactive = false;
                                    foreach ($_SESSION["peermark_assignments"][$cm->id] as $peermarkassignment) {
                                        if (time() > $peermarkassignment->dtstart) {
                                            $peermarksactive = true;
                                            break;
                                        }
                                    }
                                }

                                // Show Peermark Reviews link.
                                if (($istutor && count($_SESSION["peermark_assignments"][$cm->id]) > 0) ||
                                                            (!$istutor && $peermarksactive)) {
                                    $peermarkreviewslink = html_writer::tag('span', '',
                                        array('title' => get_string('launchpeermarkreviews', 'plagiarism_turnitin'),
                                            'class' => 'peermark_reviews_pp_launch tii_tooltip', 'id' => 'peermark_reviews_form')
                                    );
                                    $output .= html_writer::tag('div', $peermarkreviewslink, array('class' => 'row_peermark_reviews'));

                                }
                            }
                        }
                    } else if ($plagiarismfile->statuscode == 'error') {

                        // Deal with legacy error issues.
                        $errorcode = (isset($plagiarismfile->errorcode)) ? $plagiarismfile->errorcode : 0;
                        if ($errorcode == 0 && $submissiontype == 'file') {
                            if ($file->get_filesize() > PLAGIARISM_TURNITIN_MAX_FILE_UPLOAD_SIZE) {
                                $errorcode = 2;
                                $plagiarismfile->errorcode = 2;
                            }
                        }

                        // Show error message if there is one.
                        if ($errorcode == 0) {
                            $langstring = ($istutor) ? 'ppsubmissionerrorseelogs' : 'ppsubmissionerrorstudent';
                            $errorstring = empty($plagiarismfile->errormsg) ? get_string($langstring, 'plagiarism_turnitin') : $plagiarismfile->errormsg;
                        } else {
                            $errorstring = get_string(
                                'errorcode'.$plagiarismfile->errorcode,
                                'plagiarism_turnitin',
                                array(
                                    'maxfilesize' => display_size(PLAGIARISM_TURNITIN_MAX_FILE_UPLOAD_SIZE),
                                    'externalid' => $plagiarismfile->externalid
                                ));
                        }

                        $erroricon = html_writer::tag('div', $OUTPUT->pix_icon('x-red', $errorstring, 'plagiarism_turnitin'),
                                                                array('title' => $errorstring,
                                                                        'class' => 'tii_tooltip tii_error_icon'));

                        // Attach error text or resubmit link after icon depending on whether user is a student/teacher.
                        // Don't attach resubmit link if the user has not accepted the EULA.
                        if (!$istutor) {
                            $output .= html_writer::tag('div', $erroricon.' '.$errorstring, array('class' => 'warning clear'));
                        } else if ($errorcode == 3) {
                            $output .= html_writer::tag('div', $erroricon, array('class' => 'clear'));
                        } else {
                            $output .= html_writer::tag('div', $erroricon.' '.get_string('resubmittoturnitin', 'plagiarism_turnitin'),
                                                        array('class' => 'clear plagiarism_turnitin_resubmit_link',
                                                                'id' => 'pp_resubmit_'.$plagiarismfile->id));

                            $output .= html_writer::tag('div',
                                                        $OUTPUT->pix_icon('loading', $errorstring, 'plagiarism_turnitin').' '.
                                                        get_string('resubmitting', 'plagiarism_turnitin'),
                                                        array('class' => 'pp_resubmitting hidden'));

                            // Pending status for after resubmission.
                            $statusstr = get_string('turnitinstatus', 'plagiarism_turnitin').': '.get_string('pending', 'plagiarism_turnitin');
                            $output .= html_writer::tag('div', $OUTPUT->pix_icon('turnitin-icon', $statusstr, 'plagiarism_turnitin', array('class' => 'icon_size')).$statusstr,
                                                        array('class' => 'turnitin_status hidden'));

                            // Show hidden data for potential forum post resubmissions.
                            if ($submissiontype == 'forum_post' && !empty($linkarray["content"])) {
                                $output .= html_writer::tag('div', $linkarray["content"],
                                                            array('class' => 'hidden', 'id' => 'content_'.$plagiarismfile->id));
                            }

                            if ($cm->modname == 'forum') {
                                // Get forum data from the query string as we'll need this to recreate submission event.
                                $querystrid = optional_param('id', 0, PARAM_INT);
                                $discussionid = optional_param('d', 0, PARAM_INT);
                                $reply   = optional_param('reply', 0, PARAM_INT);
                                $edit    = optional_param('edit', 0, PARAM_INT);
                                $delete  = optional_param('delete', 0, PARAM_INT);
                                $output .= html_writer::tag('div', $querystrid.'_'.$discussionid.'_'.$reply.'_'.$edit.'_'.$delete,
                                                            array('class' => 'hidden', 'id' => 'forumdata_'.$plagiarismfile->id));
                            }
                        }
                    } else if ($plagiarismfile->statuscode == 'deleted') {
                        $errorcode = (isset($plagiarismfile->errorcode)) ? $plagiarismfile->errorcode : 0;
                        if ($errorcode == 0) {
                            $langstring = ($istutor) ? 'ppsubmissionerrorseelogs' : 'ppsubmissionerrorstudent';
                            $errorstring = empty($plagiarismfile->errormsg) ? get_string($langstring, 'plagiarism_turnitin') : $plagiarismfile->errormsg;
                        } else {
                            $errorstring = get_string('errorcode'.$plagiarismfile->errorcode,
                                            'plagiarism_turnitin', display_size(PLAGIARISM_TURNITIN_MAX_FILE_UPLOAD_SIZE));
                        }
                        $statusstr = get_string('turnitinstatus', 'plagiarism_turnitin').': '.get_string('deleted', 'plagiarism_turnitin').'<br />';
                        $statusstr .= get_string('because', 'plagiarism_turnitin').'<br />"'.$errorstring.'"';
                        $output .= html_writer::tag('div', $OUTPUT->pix_icon('turnitin-icon', $statusstr, 'plagiarism_turnitin', array('class' => 'icon_size')).$statusstr,
                            array('class' => 'turnitin_status'));

                    } else if ($plagiarismfile->statuscode == 'queued') {
                        $statusstr = get_string('turnitinstatus', 'plagiarism_turnitin').': '.get_string('queued', 'plagiarism_turnitin');
                        $output .= html_writer::tag('div', $OUTPUT->pix_icon('turnitin-icon', $statusstr, 'plagiarism_turnitin', array('class' => 'icon_size')).$statusstr,
                                                        array('class' => 'turnitin_status'));
                    } else {
                        $statusstr = get_string('turnitinstatus', 'plagiarism_turnitin').': '.get_string('pending', 'plagiarism_turnitin');
                        $output .= html_writer::tag('div', $OUTPUT->pix_icon('turnitin-icon', $statusstr, 'plagiarism_turnitin', array('class' => 'icon_size')).$statusstr,
                                                    array('class' => 'turnitin_status'));
                    }

                } else {
                    // Add Error if the user has not accepted EULA for submissions made before instant submission was removed.
                    $eulaerror = "";
                    if ($linkarray["userid"] != $USER->id && $submittinguser == $author && $istutor) {
                        // There is a moodle plagiarism bug where get_links is called twice, the first loop is incorrect and is killing
                        // this functionality. Have to check that user exists here first else there will be a fatal error.
                        if ($DB->get_record('user', array('id' => $linkarray["userid"]))) {
                            // We need to check for security that the user is actually on the course.
                            if ($moduleobject->user_enrolled_on_course($context, $linkarray["userid"])) {
                                $user = new turnitin_user($linkarray["userid"], "Learner");
                                if ($user->useragreementaccepted != 1) {
                                    $erroricon = html_writer::tag('div', $OUTPUT->pix_icon('doc-x-grey', get_string('errorcode3', 'plagiarism_turnitin'),
                                                                            'plagiarism_turnitin'),
                                                                            array('title' => get_string('errorcode3', 'plagiarism_turnitin'),
                                                                                    'class' => 'tii_tooltip tii_error_icon'));
                                    $eulaerror = html_writer::tag('div', $erroricon, array('class' => 'clear'));
                                }
                            }
                        }
                    }

                    // Show EULA error.
                    if (!empty($eulaerror)) {
                        $output .= $eulaerror;
                    }
                }

                $output .= html_writer::tag('div', '', array('class' => 'clear'));
            }

            $output = html_writer::tag('div', $output, array('class' => 'tii_links_container'));
        }

        // This comment is here as it is useful for product support.
        $plagiarismsettings = $this->get_settings($cm->id);
        $turnitinassignid = (empty($plagiarismsettings['turnitin_assignid'])) ? '' : $plagiarismsettings['turnitin_assignid'];
        $output .= html_writer::tag(
            'span', '<!-- Turnitin Plagiarism plugin Version: '.get_config('plagiarism_turnitin', 'version').
            ' Course ID: '.$coursedata->turnitin_cid.' TII assignment ID: '.$turnitinassignid.' -->');

        return $output;
    }

    // Query Turnitin for the papers that need updated locally.
    public function fetch_updated_paper_ids_from_turnitin($cm) {
        $plagiarismvalues = $this->get_settings($cm->id);

        // Initialise Comms Object.
        $turnitincomms = new turnitin_comms();
        $turnitincall = $turnitincomms->initialise_api();

        // Get the submission ids from Turnitin that have been updated.
        try {
            $submission = new TiiSubmission();
            $submission->setAssignmentId($plagiarismvalues["turnitin_assignid"]);

            // Only update submissions that have been modified since last update.
            if (!empty($plagiarismvalues["grades_last_synced"])) {
                $submission->setDateFrom(gmdate("Y-m-d\TH:i:s\Z", $plagiarismvalues["grades_last_synced"]));
            }

            $response = $turnitincall->findSubmissions($submission);
            $findsubmission = $response->getSubmission();

            return $findsubmission->getSubmissionIds();
        } catch (Exception $e) {
            $turnitincomms->handle_exceptions($e, 'tiisubmissionsgeterror', false);
            return false;
        }
    }

    public function update_grades_from_tii($cm) {
        global $DB;

        $submissionids = $this->fetch_updated_paper_ids_from_turnitin($cm);
        if ($submissionids === false || count($submissionids) < 1) {
            return false;
        }
        // Refresh updated submissions.
        $return = true;
        // Initialise Comms Object.
        $turnitincomms = new turnitin_comms();
        $turnitincall = $turnitincomms->initialise_api();

        // Process submissions in batches, depending on the max. number of submissions the Turnitin API returns.
        $submissionbatches = array_chunk($submissionids, PLAGIARISM_TURNITIN_NUM_RECORDS_RETURN);

        foreach ($submissionbatches as $submissionsbatch) {
            try {
                $submission = new TiiSubmission();
                $submission->setSubmissionIds($submissionsbatch);

                $response = $turnitincall->readSubmissions($submission);
                $readsubmissions = $response->getSubmissions();

                foreach ($readsubmissions as $readsubmission) {
                    $submissiondata = $DB->get_record('plagiarism_turnitin_files',
                                                        array('externalid' => $readsubmission->getSubmissionId()), 'id');
                    $return = $this->update_submission($cm, $submissiondata->id, $readsubmission);
                }

            } catch (Exception $e) {
                $turnitincomms->handle_exceptions($e, 'tiisubmissiongeterror', false);
                $return = false;
            }
        }

        return $return;
    }

    public function update_grade_from_tii($cm, $submissionid) {
        global $DB;
        $return = true;

        // Initialise Comms Object.
        $turnitincomms = new turnitin_comms();
        $turnitincall = $turnitincomms->initialise_api();

        try {
            $submission = new TiiSubmission();
            $submission->setSubmissionId($submissionid);

            $response = $turnitincall->readSubmission($submission);

            $readsubmission = $response->getSubmission();

            $submissiondata = $DB->get_record('plagiarism_turnitin_files',
                                                array('externalid' => $readsubmission->getSubmissionId()), 'id');

            $this->update_submission($cm, $submissiondata->id, $readsubmission);

        } catch (Exception $e) {
            $turnitincomms->handle_exceptions($e, 'tiisubmissionsgeterror', false);
            $return = false;
        }

        return $return;
    }

    private function update_submission($cm, $submissionid, $tiisubmission) {
        global $DB;

        $return = true;
        $updaterequired = false;

        $fields = 'id, cm, userid, identifier, itemid, similarityscore, grade, submissiontype, orcapable,';
        $fields .= 'student_read, gm_feedback, errorcode';
        if ($submissiondata = $DB->get_record('plagiarism_turnitin_files', array('id' => $submissionid), $fields)) {

            // Build Plagiarism file object.
            $plagiarismfile = new stdClass();
            $plagiarismfile->id = $submissiondata->id;
            $plagiarismfile->similarityscore = (is_numeric($tiisubmission->getOverallSimilarity())) ? $tiisubmission->getOverallSimilarity() : null;
            $plagiarismfile->transmatch = 0;
            if ((int)$tiisubmission->getTranslatedOverallSimilarity() > $tiisubmission->getOverallSimilarity()) {
                $plagiarismfile->similarityscore = $tiisubmission->getTranslatedOverallSimilarity();
                $plagiarismfile->transmatch = 1;
            }
            $plagiarismfile->grade = ($tiisubmission->getGrade() == '') ? null : $tiisubmission->getGrade();
            $plagiarismfile->orcapable = ($tiisubmission->getOriginalityReportCapable() == 1) ? 1 : 0;
            $plagiarismfile->gm_feedback = $tiisubmission->getFeedbackExists();

            // If error code is 13, set the status to success otherwise resetting the errorcode will hide the submission.
            if ($submissiondata->errorcode == 13) {
                $plagiarismfile->statuscode = 'success';
            }

            // Reset Error Values.
            $plagiarismfile->errorcode = null;
            $plagiarismfile->errormsg = null;

            // Update feedback timestamp.
            $plagiarismfile->student_read = ($tiisubmission->getAuthorLastViewedFeedback() > 0) ? strtotime($tiisubmission->getAuthorLastViewedFeedback()) : 0;

            // Identify if an update is required for the similarity score and grade.
            if ($submissiondata->similarityscore != $plagiarismfile->similarityscore ||
                $submissiondata->grade != $plagiarismfile->grade ||
                $submissiondata->orcapable != $plagiarismfile->orcapable ||
                $submissiondata->student_read != $plagiarismfile->student_read ||
                $submissiondata->gm_feedback != $plagiarismfile->gm_feedback) {
                $updaterequired = true;
            }

            // Don't update grademark if the submission is not part of the latest attempt.
            $gbupdaterequired = $updaterequired;
            if ($cm->modname == "assign") {
                if ($submissiondata->submissiontype == "file") {
                    $fs = get_file_storage();
                    if ($file = $fs->get_file_by_hash($submissiondata->identifier)) {
                        $itemid = $file->get_itemid();

                        $assignmentdata = array("assignment" => $cm->instance);

                        // Check whether submission is a group submission.
                        $groupid = $this->check_group_submission($cm, $submissiondata->userid);
                        if ($groupid) {
                            $assignmentdata['groupid'] = $groupid;
                        } else {
                            $assignmentdata['userid'] = $submissiondata->userid;
                        }
                        $submission = $DB->get_records('assign_submission', $assignmentdata, 'id DESC', 'id, attemptnumber', '0', '1');

                        $item = current($submission);
                        if ($item->id != $itemid) {
                             $gbupdaterequired = false;
                        }
                    } else {
                        $gbupdaterequired = false;
                    }
                } else if ($submissiondata->submissiontype == "text_content") {
                    // Get latest submission.
                    $moduleobject = new turnitin_assign();
                    $latesttext = $moduleobject->get_onlinetext($submissiondata->userid, $cm);
                    $latestidentifier = sha1($latesttext->onlinetext);
                    // Check submission being graded is latest.
                    if ($submissiondata->identifier != $latestidentifier) {
                        $gbupdaterequired = false;
                    }
                }
            }

            // Only update as necessary.
            if ($updaterequired) {
                $DB->update_record('plagiarism_turnitin_files', $plagiarismfile);

                // Coursework grading would be broken by syncing grades as Turnitin doesn't support Double marking.
                if ($cm->modname == "coursework") {
                    return true;
                }

                // Update grades, for the quiz we update marks for questions instead.
                if ($cm->modname == "quiz") {
                    $quiz = $DB->get_record('quiz', array('id' => $cm->instance));
                    $tq = new turnitin_quiz();
                    if (!is_null($plagiarismfile->grade)) {
                        $tq->update_mark(
                            $submissiondata->itemid,
                            $submissiondata->identifier,
                            $submissiondata->userid,
                            $plagiarismfile->grade,
                            $quiz->grade
                        );
                    }
                } else {
                    $gradeitem = $DB->get_record('grade_items',
                        array('iteminstance' => $cm->instance, 'itemmodule' => $cm->modname,
                            'courseid' => $cm->course, 'itemnumber' => 0));

                    if (!is_null($plagiarismfile->grade) && !empty($gradeitem) && $gbupdaterequired) {
                        $return = $this->update_grade($cm, $tiisubmission, $submissiondata->userid);
                    }
                }
            }
        }

        return $return;
    }

    /**
     * Update module grade and gradebook.
     */
    private function update_grade($cm, $submission, $userid, $cron = FALSE) {
        global $DB, $USER, $CFG;
        $return = true;

        if (!is_null($submission->getGrade()) && $cm->modname != 'forum') {
            // Module grade object.
            $grade = new stdClass();
            // If submission has multiple content/files in it then get average grade.
            // Ignore NULL grades and files no longer part of submission.

            // Create module object.
            $moduleclass = "turnitin_".$cm->modname;
            $moduleobject = new $moduleclass;

            // Get file from pathname hash.
            $submissiondata = $DB->get_record('plagiarism_turnitin_files', array('externalid' => $submission->getSubmissionId()), 'identifier');

            // Get file as we need item id for discounting files that are no longer in submission.
            $fs = get_file_storage();
            if ($file = $fs->get_file_by_hash($submissiondata->identifier)) {
                $moodlefiles = $DB->get_records_select('files', " component = ? AND itemid = ? AND source IS NOT null ",
                                                    array($moduleobject->filecomponent, $file->get_itemid()), 'id DESC', 'pathnamehash');

                list($insql, $inparams) = $DB->get_in_or_equal(array_keys($moodlefiles), SQL_PARAMS_QM, 'param', true);
                $tiisubmissions = $DB->get_records_select('plagiarism_turnitin_files', " userid = ? AND cm = ? AND identifier ".$insql,
                                                        array_merge(array($userid, $cm->id), $inparams));
            } else {
                $tiisubmissions = $DB->get_records('plagiarism_turnitin_files', array('userid' => $userid, 'cm' => $cm->id));
                $tiisubmissions = current($tiisubmissions);
            }

            if (is_array($tiisubmissions) && count($tiisubmissions) > 1) {
                $averagegrade = null;
                $gradescounted = 0;
                foreach ($tiisubmissions as $tiisubmission) {
                    if (!is_null($tiisubmission->grade)) {
                        $averagegrade = $averagegrade + $tiisubmission->grade;
                        $gradescounted += 1;
                    }
                }
                $grade->grade = (!is_null($averagegrade) && $gradescounted > 0) ? (int)round(($averagegrade / $gradescounted)) : null;
            } else {
                $grade->grade = $submission->getGrade();
            }

            // Check whether submission is a group submission - only applicable to assignment module.
            // If it's a group submission we will update the grade for everyone in the group.
            // Note: This will not work if the submitting user is in multiple groups.
            $userids = array($userid);
            $moduledata = $DB->get_record($cm->modname, array('id' => $cm->instance));
            if ($cm->modname == "assign" && !empty($moduledata->teamsubmission)) {
                require_once($CFG->dirroot . '/mod/assign/locallib.php');
                $context = context_course::instance($cm->course);
                $assignment = new assign($context, $cm, null);

                if ($group = $assignment->get_submission_group($userid)) {
                    $users = groups_get_members($group->id);
                    $userids = array_keys($users);
                }
            }

            // Loop through all users and update grade.
            foreach ($userids as $userid) {
                // Get gradebook data.
                switch ($cm->modname) {
                    case 'assign':

                        // Query grades based on attempt number.
                        $gradesquery = array('userid' => $userid, 'assignment' => $cm->instance);

                        $usersubmissions = $DB->get_records('assign_submission', $gradesquery, 'attemptnumber DESC', 'attemptnumber', 0, 1);
                        $usersubmission = current($usersubmissions);
                        $attemptnumber = ($usersubmission) ? $usersubmission->attemptnumber : 0;
                        $gradesquery['attemptnumber'] = $attemptnumber;

                        $currentgrades = $DB->get_records('assign_grades', $gradesquery, 'id DESC');
                        $currentgrade = current($currentgrades);
                        break;
                    case 'workshop':
                        if ($gradeitem = $DB->get_record('grade_items', array('iteminstance' => $cm->instance,
                                                        'itemmodule' => $cm->modname, 'itemnumber' => 0))) {
                            $currentgrade = $DB->get_record('grade_grades', array('userid' => $userid, 'itemid' => $gradeitem->id));
                        }
                        break;
                }

                // Configure grade object and save to db.
                $table = $moduleobject->gradestable;
                $grade->timemodified = time();

                if ($currentgrade) {
                    $grade->id = $currentgrade->id;

                    if ($cm->modname == 'assign') {
                        $context = context_course::instance($cm->course);
                        if (has_capability('mod/assign:grade', $context, $USER->id)) {
                            // If the grade has changed and the change is not from a cron task then update the grader.
                            if ($currentgrade->grade != $grade->grade && $cron == FALSE) {
                                $grade->grader = $USER->id;
                            }
                        }
                    }

                    $return = $DB->update_record($table, $grade);
                } else {
                    $grade->userid = $userid;
                    $grade->timecreated = time();
                    switch ($cm->modname) {
                        case 'workshop':
                            $grade->itemid = $gradeitem->id;
                            $grade->usermodified = $USER->id;
                            break;

                        case 'assign':
                            $grade->assignment = $cm->instance;
                            $grade->grader = $USER->id;
                            $grade->attemptnumber = $attemptnumber;
                            break;
                    }

                    $return = $DB->insert_record($table, $grade);
                }

                // Gradebook object.
                if ($grade) {
                    $grades = new stdClass();
                    $grades->userid = $userid;
                    $grades->rawgrade = $grade->grade;

                    // Check marking workflow state for assignments and only update gradebook if released.
                    if ($cm->modname == 'assign' && !empty($moduledata->markingworkflow)) {
                        $gradesreleased = $DB->record_exists('assign_user_flags',
                                                                array(
                                                                    'userid' => $userid,
                                                                    'assignment' => $cm->instance,
                                                                    'workflowstate' => 'released'
                                                                    ));
                        // Remove any existing grade from gradebook if not released.
                        if (!$gradesreleased) {
                            $grades->rawgrade = null;
                        }
                    }

                    // Prevent grades being passed to gradebook before identities have been revealed when blind marking is on.
                    if ($cm->modname == 'assign' && !empty($moduledata->blindmarking) && empty($moduledata->revealidentities)) {
                        return false;
                    }

                    // Update gradebook - Grade update returns 1 on failure and 0 if successful.
                    $gradeupdate = $cm->modname."_grade_item_update";
                    require_once($CFG->dirroot . '/mod/' . $cm->modname . '/lib.php');
                    if (is_callable($gradeupdate)) {
                        $moduledata->cmidnumber = $cm->id;
                        $return = ($gradeupdate($moduledata, $grades)) ? false : true;
                    }
                }
            }
        }

        return $return;
    }

    /**
     * Check if this is a group submission.
     */
    public function check_group_submission($cm, $userid) {
        global $CFG, $DB;

        $moduledata = $DB->get_record($cm->modname, array('id' => $cm->instance));
        if (!empty($moduledata->teamsubmission)) {
            require_once($CFG->dirroot . '/mod/assign/locallib.php');
            $context = context_course::instance($cm->course);

            $assignment = new assign($context, $cm, null);
            $group = $assignment->get_submission_group($userid);

            return $group->id;
        }

        return false;
    }

    /*
     * Related user ID will be NULL if an instructor submits on behalf of a student who is in a group.
     * To get around this, we get the group ID, get the group members and set the author as the first student in the group.

     * @param int $cmid - The course ID.
     * @param int $groupid - The ID of the Moodle group that we're getting from.
     * @return int $author The Moodle user ID that we'll be using for the author.
    */
    private function get_first_group_author($cmid, $groupid) {
        static $context;
        if (empty($context)) {
            $context = context_course::instance($cmid);
        }

        $groupmembers = groups_get_members($groupid, "u.id");
        foreach ($groupmembers as $author) {
            if (!has_capability('mod/assign:grade', $context, $author->id)) {
                return $author->id;
            }
        }
    }

    /**
     * Create a course within Turnitin
     */
    public function create_tii_course($cmid, $modname, $coursedata, $workflowcontext = "site") {
        global $CFG;

        // Create module object.
        $moduleclass = "turnitin_".$modname;
        $moduleobject = new $moduleclass;

        $turnitinassignment = new turnitin_assignment(0);
        $turnitincourse = $turnitinassignment->create_tii_course($coursedata, $workflowcontext);

        // Join all admins and instructors to the course in Turnitin if it was created.
        if (!empty($turnitincourse->turnitin_cid)) {
            $admins = explode(",", $CFG->siteadmins);

            // Grab all instructors and extract the ids.
            $capability = $moduleobject->get_tutor_capability();
            if (!empty($cmid)) {
                $tutors = get_enrolled_users(context_module::instance($cmid), $capability, 0, 'u.id', 'u.id');
            } else {
                $tutors = get_enrolled_users(context_course::instance($coursedata->id), $capability, 0, 'u.id', 'u.id');
            }
            $tutorids = array_column((array)$tutors, 'id');

            $allinstructors = array_merge($admins, $tutorids);
            foreach ($allinstructors as $instructor) {
                // Create the admin as a user within Turnitin.
                $user = new turnitin_user($instructor, 'Instructor');
                $user->join_user_to_class($turnitincourse->turnitin_cid);
            }
        }

        return $turnitincourse;
    }

    /**
     * Get Peermark Assignments for this module from Turnitin.
     */
    public function refresh_peermark_assignments($cm, $tiiassignmentid) {
        global $DB;

        // Return here if the plugin is not configured for Turnitin.
        if (!$this->is_plugin_configured()) {
            return;
        }

        // Initialise Comms Object.
        $turnitincomms = new turnitin_comms();
        $turnitincall = $turnitincomms->initialise_api();

        $assignment = new TiiAssignment();
        $assignment->setAssignmentId($tiiassignmentid);

        try {
            $response = $turnitincall->readAssignment($assignment);
            $readassignment = $response->getAssignment();

            // Get Peermark Assignments.
            $peermarkassignments = $readassignment->getPeermarkAssignments();
            if ($peermarkassignments) {
                foreach ($peermarkassignments as $peermarkassignment) {
                    $peermark = new stdClass();
                    $peermark->tiiassignid = $peermarkassignment->getAssignmentId();
                    $peermark->parent_tii_assign_id = $readassignment->getAssignmentId();
                    $peermark->dtstart = strtotime($peermarkassignment->getStartDate());
                    $peermark->dtdue = strtotime($peermarkassignment->getDueDate());
                    $peermark->dtpost = strtotime($peermarkassignment->getFeedbackReleaseDate());
                    $peermark->maxmarks = (int)$peermarkassignment->getMaxGrade();
                    $peermark->title = $peermarkassignment->getTitle();

                    $currentpeermark = $DB->get_record('plagiarism_turnitin_peermark',
                                                array('tiiassignid' => $peermark->tiiassignid));

                    if ($currentpeermark) {
                        $peermark->id = $currentpeermark->id;
                        $DB->update_record('plagiarism_turnitin_peermark', $peermark);
                    } else {
                        $DB->insert_record('plagiarism_turnitin_peermark', $peermark);
                    }
                }
            }
        } catch (Exception $e) {
            // We will use the locally stored assignment data if we can't connect to Turnitin.
            $turnitincomms->handle_exceptions($e, 'tiiassignmentgeterror', false);
        }
    }

    /**
     * Create the module as an assignment within Turnitin if it does not exist,
     * if we have a Turnitin id for the module then edit it
     */
    public function sync_tii_assignment($cm, $coursetiiid, $workflowcontext = "site", $submittoturnitin = false) {
        global $DB;

        $config = $this->plagiarism_turnitin_admin_config();
        $modulepluginsettings = $this->get_settings($cm->id);
        $moduledata = $DB->get_record($cm->modname, array('id' => $cm->instance));

        // Configure assignment object to send to Turnitin.
        $assignment = new TiiAssignment();
        $assignment->setClassId($coursetiiid);

        // We need to truncate the moodle assignment title to be compatible with a Turnitin
        // assignment title (max length 99) and account for non English multibyte strings.
        $title = $moduledata->name;
        if ( mb_strlen( $moduledata->name, 'UTF-8' ) > 80 ) {
            $title = mb_substr( $moduledata->name, 0, 80, 'UTF-8' ) . "...";
        }
        $assignment->setTitle($title);

        // Configure repository setting.
        $reposetting = (isset($modulepluginsettings["plagiarism_submitpapersto"])) ? $modulepluginsettings["plagiarism_submitpapersto"] : 1;

        // Override if necessary when admin is forcing standard/no repository.
        $reposetting = plagiarism_turnitin_override_repository($reposetting);

        $assignment->setSubmitPapersTo($reposetting);
        $assignment->setSubmittedDocumentsCheck($modulepluginsettings["plagiarism_compare_student_papers"]);
        $assignment->setInternetCheck($modulepluginsettings["plagiarism_compare_internet"]);
        $assignment->setPublicationsCheck($modulepluginsettings["plagiarism_compare_journals"]);
        if ($config->plagiarism_turnitin_repositoryoption == PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_EXPANDED ||
            $config->plagiarism_turnitin_repositoryoption == PLAGIARISM_TURNITIN_ADMIN_REPOSITORY_OPTION_FORCE_INSTITUTIONAL) {
            $institutioncheck = (isset($modulepluginsettings["plagiarism_compare_institution"])) ? $modulepluginsettings["plagiarism_compare_institution"] : 0;
            $assignment->setInstitutionCheck($institutioncheck);
        }

        $assignment->setAuthorOriginalityAccess($modulepluginsettings["plagiarism_show_student_report"]);
        $assignment->setResubmissionRule((int)$modulepluginsettings["plagiarism_report_gen"]);
        $assignment->setBibliographyExcluded($modulepluginsettings["plagiarism_exclude_biblio"]);
        $assignment->setQuotedExcluded($modulepluginsettings["plagiarism_exclude_quoted"]);
        $assignment->setSmallMatchExclusionType($modulepluginsettings["plagiarism_exclude_matches"]);

        if (empty($modulepluginsettings["plagiarism_exclude_matches_value"])) {
            $modulepluginsettings["plagiarism_exclude_matches_value"] = 0;
        }
        $assignment->setSmallMatchExclusionThreshold($modulepluginsettings["plagiarism_exclude_matches_value"]);

        // Don't set anonymous marking if there have been submissions.
        $previoussubmissions = $DB->record_exists('plagiarism_turnitin_files',
                                                            array('cm' => $cm->id, 'statuscode' => 'success'));

        // Use Moodle's blind marking setting for anonymous marking.
        if (isset($config->plagiarism_turnitin_useanon) && $config->plagiarism_turnitin_useanon && !$previoussubmissions) {
            $anonmarking = (!empty($moduledata->blindmarking)) ? 1 : 0;
            $assignment->setAnonymousMarking($anonmarking);
        }

        $assignment->setAllowNonOrSubmissions(!empty($modulepluginsettings["plagiarism_allow_non_or_submissions"]) ? 1 : 0);
        $assignment->setTranslatedMatching(!empty($modulepluginsettings["plagiarism_transmatch"]) ? 1 : 0);

        // Moodle handles submissions and whether they are allowed so this should always be true.
        // Otherwise, the Turnitin setting is incompatible with Moodle due to multiple files and resubmission rules.
        $assignment->setLateSubmissionsAllowed(1);
        $assignment->setMaxGrade(0);
        $assignment->setRubricId((!empty($modulepluginsettings["plagiarism_rubric"])) ? $modulepluginsettings["plagiarism_rubric"] : '');

        if (!empty($moduledata->grade)) {
            $assignment->setMaxGrade(($moduledata->grade < 0) ? 100 : (int)$moduledata->grade);
        }

        if (!empty($moduledata->allowsubmissionsfromdate)) {
            $dtstart = $moduledata->allowsubmissionsfromdate;
        } else if (!empty($moduledata->timeavailable)) {
            $dtstart = $moduledata->timeavailable;
        } else {
            $dtstart = $cm->added;
        }
        $dtstart = ($dtstart <= strtotime('-1 year')) ? strtotime('-11 months') : $dtstart;
        $assignment->setStartDate(gmdate("Y-m-d\TH:i:s\Z", $dtstart));

        // Set post date. If "hidden until" has been set in gradebook then we will use that value, otherwise we will
        // use start date. If the grades are to be completely hidden then we will set post date in the future.
        // From 2.6, if grading markflow is enabled and no grades have been released, we will use due date +4 weeks.
        $dtpost = 0;
        if ($cm->modname != "forum") {
            if ($gradeitem = $DB->get_record(
                                            'grade_items',
                                            array(
                                                'iteminstance' => $cm->instance,
                                                'itemmodule' => $cm->modname,
                                                'courseid' => $cm->course,
                                                'itemnumber' => 0)
                                            )) {

                switch ($gradeitem->hidden) {
                    case 1:
                        $dtpost = strtotime('+6 months');
                        break;
                    case 0:
                        $dtpost = $dtstart;
                        // If any grades have been released early via marking workflow, set post date to have passed.
                        if ($cm->modname == 'assign' && !empty($moduledata->markingworkflow)) {
                            $gradesreleased = $DB->record_exists('assign_user_flags',
                                                            array('assignment' => $cm->instance,
                                                                    'workflowstate' => 'released'));

                            $dtpost = ($gradesreleased) ? strtotime('-5 minutes') : strtotime('+1 month');
                        }
                        break;
                    default:
                        $dtpost = $gradeitem->hidden;
                        break;
                }
            }
        }

        // If blind marking is being used and identities have not been revealed then push out post date.
        if ($cm->modname == 'assign' && !empty($moduledata->blindmarking) && empty($moduledata->revealidentities)) {
            $dtpost = strtotime('+6 months');
        }

        // If blind marking is being used for coursework then push out post date.
        if ($cm->modname == 'coursework' && !empty($moduledata->blindmarking)) {
            $dtpost = strtotime('+6 months');
        }

        // Ensure post date can't be before start date.
        if ($dtpost < $dtstart) {
            $dtpost = $dtstart;
        }

        // Set due date, dependent on various things.
        $dtdue = (!empty($moduledata->duedate)) ? $moduledata->duedate : 0;

        // If the due date has been set more than a year ahead then restrict it to 1 year from now.
        if ($dtdue > strtotime('+1 year')) {
            $dtdue = strtotime('+1 year');
        }

        // Ensure due date can't be before start date.
        if ($dtdue <= $dtstart) {
            $dtdue = strtotime('+1 month', $dtstart);
        }

        // Ensure due date is always in the future for submissions.
        if ($dtdue <= time() && $submittoturnitin) {
            $dtdue = strtotime('+1 day');
        }

        $assignment->setDueDate(gmdate("Y-m-d\TH:i:s\Z", $dtdue));

        // If the duedate is in the future then set any submission duedate_report_refresh flags that
        // are 2 to 1 to make sure they are re-examined in the next cron run.
        if ($dtdue > time()) {
            $DB->set_field('plagiarism_turnitin_files', 'duedate_report_refresh', 1, array('cm' => $cm->id, 'duedate_report_refresh' => 2));
        }

        $assignment->setFeedbackReleaseDate(gmdate("Y-m-d\TH:i:s\Z", $dtpost));

        // If we have a turnitin id then edit the assignment otherwise create it.
        if ($tiiassignment = $DB->get_record('plagiarism_turnitin_config',
                                    array('cm' => $cm->id, 'name' => 'turnitin_assignid'), 'value')) {
            $assignment->setAssignmentId($tiiassignment->value);
            $turnitinassignment = new turnitin_assignment(0);

            $return = $turnitinassignment->edit_tii_assignment($assignment, $workflowcontext);
            $return['errorcode'] = ($return['success']) ? 0 : 6;

            return $return;
        } else {
            $turnitinassignment = new turnitin_assignment(0);
            $turnitinassignid = $turnitinassignment->create_tii_assignment($assignment, $workflowcontext);

            if (!$turnitinassignid) {
                $return = array('success' => false, 'tiiassignmentid' => '', 'errorcode' => 5);
            } else {
                $moduleconfigvalue = new stdClass();
                $moduleconfigvalue->cm = $cm->id;
                $moduleconfigvalue->name = 'turnitin_assignid';
                $moduleconfigvalue->value = $turnitinassignid;
                $moduleconfigvalue->config_hash = $moduleconfigvalue->cm."_".$moduleconfigvalue->name;
                $DB->insert_record('plagiarism_turnitin_config', $moduleconfigvalue);

                $return = array('success' => true, 'tiiassignmentid' => $turnitinassignid);
            }

            return $return;
        }
    }

    /**
     * Check for rubric and save to assignment.
     */
    public function update_rubric_from_tii($cm) {
        global $DB;

        $turnitincomms = new turnitin_comms();
        $turnitincall = $turnitincomms->initialise_api();
        $assignment = new TiiAssignment();

        if ($tiimoduledata = $DB->get_record('plagiarism_turnitin_config',
            array('cm' => $cm->id, 'name' => 'turnitin_assignid'), 'value')) {

            $assignment->setAssignmentId($tiimoduledata->value);

            try {
                // Retrieve assignment from Turnitin.
                $response = $turnitincall->readAssignment($assignment);
                $tiiassignment = $response->getAssignment();

                // Create rubric config field with rubric value from Turnitin.
                $rubricfield = new stdClass();
                $rubricfield->cm = $cm->id;
                $rubricfield->name = 'plagiarism_rubric';
                $rubricfield->value = $tiiassignment->getRubricId();

                // Check if rubric already exists for this module.
                if ($configfield = $DB->get_field('plagiarism_turnitin_config', 'id',
                    (array('cm' => $cm->id, 'name' => 'plagiarism_rubric')))) {

                    // Use current configfield to update rubric value.
                    $rubricfield->id = $configfield;

                    $DB->update_record('plagiarism_turnitin_config', $rubricfield);
                } else {
                    // Otherwise create rubric entry for this module.
                    $rubricfield->config_hash = $rubricfield->cm."_".$rubricfield->name;
                    $DB->create_record('plagiarism_turnitin_config', $rubricfield);
                }
            }
            catch (Exception $e) {
                $turnitincomms->handle_exceptions($e, 'tiiassignmentgeterror', false);
            }
         }
    }

    /**
     * Updates the database field duedate_report_refresh for any given submission ID.
     * @param int $id - the ID of the submission to update.
     * @param int $newvalue - the value to which the field should be set.
     */
    public function set_duedate_report_refresh($id, $newvalue) {
        global $DB;

        $updatedata = new stdClass();
        $updatedata->id = $id;
        $updatedata->duedate_report_refresh = $newvalue;
        $DB->update_record('plagiarism_turnitin_files', $updatedata);
    }

    /**
     * Update simliarity scores.
     * @param array $submissions - the submissions to be processed
     * @return boolean
     */
    public function cron_update_scores() {
        global $DB;

        $submissionids = array();
        $reportsexpected = array();
        $assignmentids = array();
        
        // Grab all plagiarism files where all the following conditions are met:
        // 1. The file has been successfully sent to TII
        // 2. The submission is ready to recieve a similarity score (either it doesn't already have a similarity score or it's set to regenerate)
        // 3. The course or activity module associated with the submission hasn't been deleted
        $submissions = $DB->get_records_sql(
          'SELECT PTF.*, 
          CM.instance AS instance,
          M.name AS modname
          FROM {plagiarism_turnitin_files} PTF
          INNER JOIN {course_modules} CM ON CM.id = PTF.cm
          INNER JOIN {modules} M ON M.id = CM.module
          WHERE statuscode = ?
          AND ( similarityscore IS NULL OR duedate_report_refresh = 1 )
          AND ( orcapable = ? OR orcapable IS NULL )
          ORDER BY externalid DESC',
          ['success', 1]
        );

        // Cache module settings
        $modulesettings = [];
        foreach ($submissions as $tiisubmission) {
          if (!array_key_exists($tiisubmission->cm, $modulesettings)) {
            $modulesettings[$tiisubmission->cm] = $this->get_settings($tiisubmission->cm);
          }
        }

        // Cache module data
        $moduledata = [];
        foreach ($submissions as $submission) {
            if (!array_key_exists($tiisubmission->modname, $moduledata)) {
                $moduledata[$tiisubmission->modname] = $DB->get_record($tiisubmission->modname, array('id' => $tiisubmission->instance));
            }
        }

        // Add submission ids to the request.
        foreach ($submissions as $tiisubmission) {
            // Updates the db field 'duedate_report_refresh' if the due date has passed within the last twenty four hours.
            $now = strtotime('now');
            $dtdue = (!empty($moduledata[$tiisubmission->modname]->duedate)) ? $moduledata[$tiisubmission->modname]->duedate : 0;
            if ($tiisubmission->duedate_report_refresh != 1 && $now >= $dtdue && $now < strtotime('+1 day', $dtdue)) {
                $this->set_duedate_report_refresh($tiisubmission->id, 1);
            }

            if (!isset($reportsexpected[$tiisubmission->cm])) {

                $reportsexpected[$tiisubmission->cm] = 1;

                if (!isset($modulesettings[$tiisubmission->cm]['plagiarism_compare_institution'])) {
                    $modulesettings[$tiisubmission->cm]['plagiarism_compare_institution'] = 0;
                }

                // Don't add the submission to the request if module settings mean we will not get a report back.
                if (array_key_exists('plagiarism_compare_student_papers', $modulesettings[$tiisubmission->cm]) &&
                    $modulesettings[$tiisubmission->cm]['plagiarism_compare_student_papers'] == 0 &&
                    $modulesettings[$tiisubmission->cm]['plagiarism_compare_internet'] == 0 &&
                    $modulesettings[$tiisubmission->cm]['plagiarism_compare_journals'] == 0 &&
                    $modulesettings[$tiisubmission->cm]['plagiarism_compare_institution'] == 0) {
                    $reportsexpected[$tiisubmission->cm] = 0;
                }
            }

            // Only add the submission to the request if we are expecting an originality report.
            if ($reportsexpected[$tiisubmission->cm] == 1) {
                $submissionids[] = $tiisubmission->externalid;

                // If submission is added to the request, add the corresponding assign id in the assignids array.
                $moduleturnitinconfig = $DB->get_record('plagiarism_turnitin_config',
                    [ 'cm' => $tiisubmission->cm, 'name' => 'turnitin_assignid' ]);
           
                if (!isset(array_flip($assignmentids)[$moduleturnitinconfig->value])) {
                    $assignmentids[] = $moduleturnitinconfig->value;
                }
            }
        }

        $validatedsubmissions = $this->check_local_submission_state($assignmentids, $submissionids);

        // At this point update missingTiiSubmissions state to error.
        if (count($validatedsubmissions['missingTiiSubmissions']) > 0) {
            foreach ($validatedsubmissions['missingTiiSubmissions'] as $missingsubmission) {
                try {
                    $this->invalidate_missing_submission($missingsubmission);
                } catch (Exception $e) {
                    mtrace("An exception was thrown while attempting to update plagiarism turnitin file submission: $missingsubmission "
                        . $e->getMessage() . '(' . $e->getFile() . ':' . $e->getLine() . ')');
                }
            }
        }

        if (count($validatedsubmissions['trimmedSubmissions']) > 0) {

            // Process submissions in batches, depending on the max. number of submissions the Turnitin API returns.
            $submissionbatches = array_chunk($validatedsubmissions['trimmedSubmissions'], PLAGIARISM_TURNITIN_NUM_RECORDS_RETURN);
            foreach ($submissionbatches as $submissionsbatch) {

                // Initialise Comms Object.
                $turnitincomms = new turnitin_comms();
                $turnitincall = $turnitincomms->initialise_api();

                try {
                    $submission = new TiiSubmission();

                    // Use $submissionsbatch array instead of original $submissionids.
                    $submission->setSubmissionIds($submissionsbatch);
                    $response = $turnitincall->readSubmissions($submission);
                    $readsubmissions = $response->getSubmissions();

                    foreach ($readsubmissions as $readsubmission) {

                        // Catch exceptions thrown by getSubmissionId to allow rest of the
                        // submissions to get processed.
                        try {
                            $tiisubmissionid = (int)$readsubmission->getSubmissionId();

                            $currentsubmission = $DB->get_record('plagiarism_turnitin_files', array('externalid' => $tiisubmissionid), 'id, cm, externalid, userid');
                            if ($cm = get_coursemodule_from_id('', $currentsubmission->cm)) {

                                $plagiarismfile = new stdClass();
                                $plagiarismfile->id = $currentsubmission->id;
                                $plagiarismfile->externalid = $tiisubmissionid;
                                $plagiarismfile->similarityscore = (is_numeric($readsubmission->getOverallSimilarity())) ? $readsubmission->getOverallSimilarity() : null;
                                $plagiarismfile->grade = (is_numeric($readsubmission->getGrade())) ? $readsubmission->getGrade() : null;
                                $plagiarismfile->orcapable = ($readsubmission->getOriginalityReportCapable() == 1) ? 1 : 0;
                                $plagiarismfile->transmatch = 0;
                                if (is_int($readsubmission->getTranslatedOverallSimilarity()) &&
                                        $readsubmission->getTranslatedOverallSimilarity() > $readsubmission->getOverallSimilarity()) {
                                    $plagiarismfile->similarityscore = $readsubmission->getTranslatedOverallSimilarity();
                                    $plagiarismfile->transmatch = 1;
                                }

                                if (!$DB->update_record('plagiarism_turnitin_files', $plagiarismfile)) {
                                    mtrace("File failed to update: ".$plagiarismfile->id);
                                } else {
                                    mtrace("File updated: ".$plagiarismfile->id);
                                }

                                // at the moment TII doesn't support double marking so we won't synchronise grades from Grade Mark as it would destroy the workflow
                                if (!is_null($plagiarismfile->grade) && $cm->modname != "coursework") {
                                    $this->update_grade($cm, $readsubmission, $currentsubmission->userid, TRUE);
                                }
                            }
                        } catch (Exception $e) {
                            mtrace("An exception was thrown while attempting to read submission $tiisubmissionid: "
                                   . $e->getMessage() . '(' . $e->getFile() . ':' . $e->getLine() . ')');
                        }
                    }
                } catch (Exception $e) {
                    mtrace(get_string('tiisubmissionsgeterror', 'plagiarism_turnitin'));
                    $turnitincomms->handle_exceptions($e, 'tiisubmissionsgeterror', false);
                    // Do not return false if a batch fails - another one might work.
                }
            }
        }

        // Sets the duedate_report_refresh flag for each processed submission to 2 to prevent them being processed again in the next cron run.
        foreach ($submissions as $tiisubmission) {
            $this->set_duedate_report_refresh($tiisubmission->id, 2);
        }

        return true;
    }

    private function check_local_submission_state($assignmentids, $submissionids) {
        // Initialise Comms Object.
        $turnitincomms = new turnitin_comms();
        $turnitincall = $turnitincomms->initialise_api();
        $tiisubmissionids = array();

        foreach ($assignmentids as $assignmentid) {
            $submission = new TiiSubmission();
            $submission->setAssignmentId($assignmentid);

            try {
                $response = $turnitincall->findSubmissions($submission);
                $tiisubmissionids = array_merge($tiisubmissionids, $response->getSubmission()->getSubmissionIds());
            } catch (Exception $e) {
                mtrace("An exception was thrown while attempting to find submissions for Turnitin assignment: $assignmentid. "
                    . $e->getMessage() . '(' . $e->getFile() . ':' . $e->getLine() . ')');
            }
        }

        return array(
            'trimmedSubmissions' => array_intersect($submissionids, $tiisubmissionids),
            'missingTiiSubmissions' => array_diff($submissionids, $tiisubmissionids)
        );
    }

    private function invalidate_missing_submission($missingsubmission) {
        global $DB;
        $currentsubmission = $DB->get_record('plagiarism_turnitin_files',
            array('externalid' => $missingsubmission),
            'id, cm, externalid, userid'
        );
        $plagiarismfile = new stdClass();
        $plagiarismfile->id = $currentsubmission->id;
        $plagiarismfile->externalid = $currentsubmission->externalid;
        $plagiarismfile->userid = $currentsubmission->userid;
        $plagiarismfile->statuscode = 'error';
        $plagiarismfile->errorcode = 13;

        if (!$DB->update_record('plagiarism_turnitin_files', $plagiarismfile)) {
            mtrace("File failed to update: ".$plagiarismfile->id);
        } else {
            mtrace("File updated: ".$plagiarismfile->id);
        }
    }

    /**
     * Get a class Id from Turnitin if you only have an assignment id.
     */
    private function get_course_id_from_assignment_id($assignmentid) {
        // Initialise Comms Object.
        $turnitincomms = new turnitin_comms();
        $turnitincall = $turnitincomms->initialise_api();

        try {
            $assignment = new TiiAssignment();
            $assignment->setAssignmentId($assignmentid);

            $response = $turnitincall->readAssignment($assignment);
            $readassignment = $response->getAssignment();

            return $readassignment->getClassId();
        } catch (Exception $e) {
            $turnitincomms->handle_exceptions($e, 'assigngeterror', false);
        }
    }

    /**
     * Previous incarnations of this plugin did not store the turnitin course id so we have to get this using the assignment id.
     * If that wasn't linked with turnitin then we have to check all the modules on this course.
     */
    public function get_previous_course_id($cmid, $courseid) {
        global $DB;
        $tiicourseid = 0;

        if ($tiiassignment = $DB->get_record('plagiarism_turnitin_config', array('cm' => $cmid,
                                                    'name' => 'turnitin_assignid'))) {
            $tiicourseid = $this->get_course_id_from_assignment_id($tiiassignment->value);
        } else {
            $coursemods = get_course_mods($courseid);
            foreach ($coursemods as $coursemod) {
                if ($coursemod->modname != 'turnitintooltwo') {
                    if ($tiiassignment = $DB->get_record('plagiarism_turnitin_config', array('cm' => $coursemod->id,
                                                                                        'name' => 'turnitin_assignid'))) {
                        $tiicourseid = $this->get_course_id_from_assignment_id($tiiassignment->value);
                    }
                }
            }
        }

        return ($tiicourseid > 0) ? $tiicourseid : false;
    }

    /**
     * Migrate course from previous version of plugin to this
     */
    public function migrate_previous_course($coursedata, $turnitincid, $workflowcontext = "site") {
        global $DB;

        $turnitincourse = new stdClass();
        $turnitincourse->courseid = $coursedata->id;
        $turnitincourse->turnitin_cid = $turnitincid;
        $turnitincourse->turnitin_ctl = $coursedata->fullname . " (Moodle PP)";

        if (empty($coursedata->tii_rel_id)) {
            $method = "insert_record";
        } else {
            $method = "update_record";
            $turnitincourse->id = $coursedata->tii_rel_id;
        }

        if (!$DB->$method('plagiarism_turnitin_courses', $turnitincourse)) {
            if ($workflowcontext != "cron") {
                plagiarism_turnitin_print_error('classupdateerror', 'plagiarism_turnitin', null, null, __FILE__, __LINE__);
                exit();
            }
        }

        $turnitinassignment = new turnitin_assignment(0);
        $turnitinassignment->edit_tii_course($coursedata);

        $coursedata->turnitin_cid = $turnitincid;
        $coursedata->turnitin_ctl = $turnitincourse->turnitin_ctl;

        return $coursedata;
    }


    /**
     * Queue submissions to send to Turnitin
     *
     * @param $cm
     * @param $author
     * @param $submitter
     * @param $identifier
     * @param $submissiontype
     * @param int $itemid
     * @return bool
     */
    public function queue_submission_to_turnitin($cm, $author, $submitter, $identifier, $submissiontype, $itemid = 0, $eventtype = null) {
        global $CFG, $DB, $turnitinacceptedfiles;
        $errorcode = 0;
        $attempt = 0;
        $tiisubmissionid = null;

        // If the EULA hasn't been accepted, don't save submission and don't submit to Tii
        $coursedata = $this->get_course_data($cm->id, $cm->course);
        $user = new turnitin_user($author, "Learner");
        $user->join_user_to_class($coursedata->turnitin_cid);
        $eula_accepted = ($user->useragreementaccepted == 0) ? $user->get_accepted_user_agreement() : $user->useragreementaccepted;
        if ($eula_accepted != 1) {
            return true;
        }

        // If the submission is already in the queue in an error state, remove it
        $DB->delete_records('plagiarism_turnitin_files', [
                'cm' => $cm->id,
                'userid' => $author,
                'itemid' => $itemid,
                'statuscode' => 'error',
            ]
        );

        // Check if file has been submitted before.
        $plagiarismfiles = plagiarism_turnitin_retrieve_successful_submissions($author, $cm->id, $identifier);
        if (count($plagiarismfiles) > 0) {
            return true;
        }

        $settings = $this->get_settings($cm->id);

        // Get module data.
        $moduledata = $DB->get_record($cm->modname, array('id' => $cm->instance));
        $moduledata->resubmission_allowed = false;

        if ($cm->modname == 'assign') {
            // Group submissions require userid = 0 when checking assign_submission.
            $userid = ($moduledata->teamsubmission) ? 0 : $author;

            if (!isset($_SESSION["moodlesubmissionstatus"])) {
                $_SESSION["moodlesubmissionstatus"] = null;
            }

            if ($eventtype == "content_uploaded" || $eventtype == "file_uploaded") {
                $moodlesubmission = $DB->get_record('assign_submission',
                    array('assignment' => $cm->instance,
                        'userid' => $userid,
                        'id' => $itemid), 'status');

                $_SESSION["moodlesubmissionstatus"] = $moodlesubmission->status;
            }

            $turnitinassign = new turnitin_assign();
            $moduledata->resubmission_allowed = $turnitinassign->is_resubmission_allowed(
                $cm->instance,
                $settings["plagiarism_report_gen"],
                $submissiontype,
                $moduledata->attemptreopenmethod,
                $_SESSION["moodlesubmissionstatus"]
            );

            if ($eventtype != "content_uploaded" && $eventtype != "file_uploaded") {
                unset($_SESSION["moodlesubmissionstatus"]);
            }
        } else {
            $userid = $author;
        }

        // Work out submission method.
        // If this file has successfully submitted in the past then break, text content is to be submitted.
        switch ($submissiontype) {
            case 'file':
            case 'text_content':

                // Get file data or prepare text submission.
                if ($submissiontype == 'file') {
                    $fs = get_file_storage();
                    $file = $fs->get_file_by_hash($identifier);

                    $timemodified = $file->get_timemodified();
                    $filename = $file->get_filename();
                } else {
                    // Check when text submission was last modified.
                    switch ($cm->modname) {
                        case 'assign':
                            $moodlesubmission = $DB->get_record('assign_submission',
                                                    array('assignment' => $cm->instance,
                                                                'userid' => $userid,
                                                                'id' => $itemid), 'timemodified');
                            break;
                        case 'workshop':
                            $moodlesubmission = $DB->get_record('workshop_submissions',
                                                    array('workshopid' => $cm->instance,
                                                            'authorid' => $userid), 'timemodified');
                            break;
                    }

                    $timemodified = $moodlesubmission->timemodified;
                }

                // Get submission method depending on whether there has been a previous submission.
                $submissionfields = 'id, cm, externalid, identifier, statuscode, lastmodified, attempt';
                $typefield = ($CFG->dbtype == "oci") ? " to_char(submissiontype) " : " submissiontype ";

                // Check if this content/file has been submitted previously.
                $previoussubmissions = $DB->get_records_select('plagiarism_turnitin_files',
                                                    " cm = ? AND userid = ? AND ".$typefield." = ? AND identifier = ?",
                                                array($cm->id, $author, $submissiontype, $identifier),
                                                    'id', $submissionfields);
                $previoussubmission = end($previoussubmissions);

                if ($previoussubmission) {
                    // Don't submit if submission hasn't changed.
                    if (in_array($previoussubmission->statuscode, array("success", "error"))
                            && $timemodified <= $previoussubmission->lastmodified) {
                        return true;
                    } else if ($moduledata->resubmission_allowed) {
                        // Replace submission in the specific circumstance where Turnitin can accommodate resubmissions.
                        $submissionid = $previoussubmission->id;
                        $this->reset_tii_submission($cm, $author, $identifier, $previoussubmission, $submissiontype);
                        $tiisubmissionid = $previoussubmission->externalid;
                    } else {
                        if ($previoussubmission->statuscode != "success") {
                            $submissionid = $previoussubmission->id;
                            $this->reset_tii_submission($cm, $author, $identifier, $previoussubmission, $submissiontype);
                        } else {
                            $submissionid = $this->create_new_tii_submission($cm, $author, $identifier, $submissiontype);
                            $tiisubmissionid = $previoussubmission->externalid;
                        }
                    }
                    $attempt = $previoussubmission->attempt;
                } else {
                    // Check if there is previous submission of different content which we may be able to replace.
                    $typefield = ($CFG->dbtype == "oci") ? " to_char(submissiontype) " : " submissiontype ";
                    if ($previoussubmission = $DB->get_record_select('plagiarism_turnitin_files',
                                                    " cm = ? AND userid = ? AND ".$typefield." = ?",
                                                array($cm->id, $author, $submissiontype),
                                                    'id, cm, externalid, identifier, statuscode, lastmodified, attempt')) {

                        $submissionid = $previoussubmission->id;
                        $attempt = $previoussubmission->attempt;
                        // Delete old text content submissions from Turnitin if resubmissions aren't allowed.
                        if ($submissiontype == 'text_content' && $settings["plagiarism_report_gen"] == 0 && !is_null($previoussubmission->externalid)) {
                            $this->delete_tii_submission($cm, $previoussubmission->externalid, $author);
                        }

                        // Replace submission in the specific circumstance where Turnitin can accomodate resubmissions.
                        if ($moduledata->resubmission_allowed || $submissiontype == 'text_content') {
                            $this->reset_tii_submission($cm, $author, $identifier, $previoussubmission, $submissiontype);
                            $tiisubmissionid = $previoussubmission->externalid;
                        } else {
                            $submissionid = $this->create_new_tii_submission($cm, $author, $identifier, $submissiontype);
                        }

                    } else {
                        $submissionid = $this->create_new_tii_submission($cm, $author, $identifier, $submissiontype);
                    }
                }

                break;

            case 'forum_post':
            case 'quiz_answer':
                if ($previoussubmissions = $DB->get_records_select('plagiarism_turnitin_files',
                                                    " cm = ? AND userid = ? AND identifier = ? ",
                                                    array($cm->id, $author, $identifier),
                                                    'id DESC', 'id, cm, externalid, identifier, statuscode, attempt', 0, 1)) {

                    $previoussubmission = current($previoussubmissions);
                    if ($previoussubmission->statuscode == "success") {
                        return true;
                    } else {
                        $submissionid = $previoussubmission->id;
                        $attempt = $previoussubmission->attempt;
                        $tiisubmissionid = $previoussubmission->externalid;
                        $this->reset_tii_submission($cm, $author, $identifier, $previoussubmission, $submissiontype);
                    }
                } else {
                    $submissionid = $this->create_new_tii_submission($cm, $author, $identifier, $submissiontype);
                }
                break;
        }

        // Check file is less than maximum allowed size.
        if ($submissiontype == 'file') {
            if ($file->get_filesize() > PLAGIARISM_TURNITIN_MAX_FILE_UPLOAD_SIZE) {
                $errorcode = 2;
            }
        }

        // If applicable, check whether file type is accepted.
        $acceptanyfiletype = (!empty($settings["plagiarism_allow_non_or_submissions"])) ? 1 : 0;
        if (!$acceptanyfiletype && $submissiontype == 'file') {
            $filenameparts = explode('.', $filename);
            $fileext = strtolower(end($filenameparts));
            if (!in_array(".".$fileext, $turnitinacceptedfiles)) {
                $errorcode = 4;
            }
        }

        // Save submission as queued or errored if we have an errorcode.
        $statuscode = ($errorcode != 0) ? 'error' : 'queued';
        return $this->save_submission($cm, $author, $submissionid, $identifier, $statuscode, $tiisubmissionid, $submitter, $itemid,
                        $submissiontype, $attempt, $errorcode);
    }

    /**
     * Amalgamated handler for Moodle cron events.
     *
     * @param object $eventdata
     * @return bool result
     */
    public function event_handler($eventdata) {
        global $DB;

        $result = true;

        // Get the coursemodule, use a different method if in a quiz as we have the quiz id.
        if ($eventdata['other']['modulename'] == 'quiz') {
            $cm = get_coursemodule_from_instance($eventdata['other']['modulename'], $eventdata['other']['quizid']);
        } else {
            $cm = get_coursemodule_from_id($eventdata['other']['modulename'], $eventdata['contextinstanceid']);
        }

        // Remove the event if the course module no longer exists.
        if (!$cm) {
            return true;
        }
        $context = context_module::instance($cm->id);

        // Initialise module settings.
        $plagiarismsettings = $this->get_settings($cm->id);
        $moduletiienabled = $this->get_config_settings('mod_'.$cm->modname);
        if ($cm->modname == 'assign') {
            $plagiarismsettings["plagiarism_draft_submit"] = (isset($plagiarismsettings["plagiarism_draft_submit"])) ? $plagiarismsettings["plagiarism_draft_submit"] : 0;
        }

        // Either module not using Turnitin or Turnitin not being used at all so return true to remove event from queue.
        if (empty($plagiarismsettings['use_turnitin']) || empty($moduletiienabled)) {
            return true;
        }

        // Get module data.
        $moduledata = $DB->get_record($cm->modname, array('id' => $cm->instance));
        if ($cm->modname != 'assign') {
            $moduledata->submissiondrafts = 0;
        }

        // If draft submissions are turned on then only send to Turnitin if the draft submit setting is set.
        if ($moduledata->submissiondrafts && $plagiarismsettings["plagiarism_draft_submit"] == 1 &&
            ($eventdata['eventtype'] == 'file_uploaded' || $eventdata['eventtype'] == 'content_uploaded')) {
            return true;
        }

        // Set the author and submitter.
        $submitter = $eventdata['userid'];
        $author = (!empty($eventdata['relateduserid'])) ? $eventdata['relateduserid'] : $eventdata['userid'];

        /*
           Related user ID will be NULL if an instructor submits on behalf of a student who is in a group.
           To get around this, we get the group ID, get the group members and set the author as the first student in the group.
        */
        if ((empty($eventdata['relateduserid'])) && ($cm->modname == 'assign')
                && has_capability('mod/assign:editothersubmission', $context, $submitter)) {
            $moodlesubmission = $DB->get_record('assign_submission', array('id' => $eventdata['objectid']), 'id, groupid');
            if (!empty($moodlesubmission->groupid)) {
                $author = $this->get_first_group_author($cm->course, $moodlesubmission->groupid);
            }
        }

        // Get actual text content and files to be submitted for draft submissions.
        // As this won't be present in eventdata for certain event types.
        if ($eventdata['other']['modulename'] == 'assign' && $eventdata['eventtype'] == "assessable_submitted") {
            // Get content.
            $moodlesubmission = $DB->get_record('assign_submission', array('id' => $eventdata['objectid']), 'id');
            if ($moodletextsubmission = $DB->get_record('assignsubmission_onlinetext',
                                        array('submission' => $moodlesubmission->id), 'onlinetext')) {
                $eventdata['other']['content'] = $moodletextsubmission->onlinetext;
            }

            // Get Files.
            $eventdata['other']['pathnamehashes'] = array();
            $filesconditions = array('component' => 'assignsubmission_file',
                                    'itemid' => $moodlesubmission->id, 'userid' => $author);
            if ($moodlefiles = $DB->get_records('files', $filesconditions)) {
                foreach ($moodlefiles as $moodlefile) {
                    $eventdata['other']['pathnamehashes'][] = $moodlefile->pathnamehash;
                }
            }
        }

        // Remove submission from Turnitin queue if it is removed from Moodle.
        if ($eventdata['other']['modulename'] == 'assign' && $eventdata['eventtype'] == "submission_removed") {
            $params = [
                'cm' => $eventdata['contextinstanceid'],
                'userid' => $eventdata['relateduserid'],
                'itemid' => $eventdata['objectid'],
                'statuscode' => 'queued',
            ];
            $DB->delete_records('plagiarism_turnitin_files', $params);
        }

        // Queue every question submitted in a quiz attempt.
        if ($eventdata['eventtype'] == 'quiz_submitted') {

            if (class_exists('\mod_quiz\quiz_attempt')) {
                $quizattemptclass = '\mod_quiz\quiz_attempt';
            } else {
                $quizattemptclass = 'quiz_attempt';
            }
            $attempt = $quizattemptclass::create($eventdata['objectid']);
            
            foreach ($attempt->get_slots() as $slot) {
                $qa = $attempt->get_question_attempt($slot);
                if ($qa->get_question()->get_type_name() != 'essay') {
                    continue;
                }
                $eventdata['other']['content'] = $qa->get_response_summary();

                // Queue text content.
                // adding slot to sha hash to create unique assignments for duplicate text based on it's id
                $identifier = sha1($eventdata['other']['content'].$slot);
                $result = $this->queue_submission_to_turnitin(
                        $cm, $author, $submitter, $identifier, 'quiz_answer',
                        $eventdata['objectid'], $eventdata['eventtype']);

                $files = $qa->get_last_qt_files('attachments', $context->id);
                foreach ($files as $file) {
                    // Queue file for sending to Turnitin.
                    $identifier = $file->get_pathnamehash();
                    $result = $this->queue_submission_to_turnitin(
                            $cm, $author, $submitter, $identifier, 'file',
                            $eventdata['objectid'], $eventdata['eventtype']);
                }
            }
        }

        // Queue text content and forum posts to send to Turnitin.
        if (in_array($eventdata['eventtype'], array("content_uploaded", "assessable_submitted"))
                && !empty($eventdata['other']['content'])) {

            $submissiontype = ($cm->modname == 'forum') ? 'forum_post' : 'text_content';

            // The content inside the event data will not always correspond to the content we will look up later, e.g.
            // because URLs have been converted to use @@PLUGINFILE@@ etc. Therefore to calculate the same hash, we need to
            // do a lookup to get the file content
            if ($cm->modname == 'workshop') {
                $moodlesubmission = $DB->get_record('workshop_submissions', array('id' => $eventdata['objectid']));
                $eventdata['other']['content'] = $moodlesubmission->content;
            }
            else if ($cm->modname == 'forum') {
              $moodlesubmission = $DB->get_record('forum_posts', array('id' => $eventdata['objectid']));
              $eventdata['other']['content'] = $moodlesubmission->message;
            }

            $identifier = sha1($eventdata['other']['content']);

            // Check if content has been submitted before and return if so.
            $result = $this->queue_submission_to_turnitin(
                            $cm, $author, $submitter, $identifier, $submissiontype,
                            $eventdata['objectid'], $eventdata['eventtype']);
        }

        // Queue files to submit to Turnitin.
        $result = $result && true;
        if (!empty($eventdata['other']['pathnamehashes'])) {
            foreach ($eventdata['other']['pathnamehashes'] as $pathnamehash) {
                $fs = get_file_storage();
                $file = $fs->get_file_by_hash($pathnamehash);

                if (!$file) {
                    plagiarism_turnitin_activitylog('File not found: '.$pathnamehash, 'PP_NO_FILE');
                    $result = true;
                    continue;
                } else if ($file->get_filename() === '.') {
                    continue;
                } else {
                    try {
                        $fh = $file->get_content_file_handle();
                        fclose($fh);
                    } catch (Exception $e) {
                        plagiarism_turnitin_activitylog('File content not found: '.$pathnamehash, 'PP_NO_FILE');
                        mtrace($e);
                        mtrace('File content not found. pathnamehash: '.$pathnamehash);
                        $result = true;
                        continue;
                    }
                }

                $result = $result && $this->queue_submission_to_turnitin(
                            $cm, $author, $submitter, $pathnamehash, 'file', $eventdata['objectid'], $eventdata['eventtype']);
            }
        }

        return $result;
    }

    /**
     * Initialise submission values
     *
     **/
    private function create_new_tii_submission($cm, $userid, $identifier, $submissiontype) {
        global $DB;

        $plagiarismfile = new stdClass();
        $plagiarismfile->cm = $cm->id;
        $plagiarismfile->userid = $userid;
        $plagiarismfile->identifier = $identifier;
        $plagiarismfile->statuscode = "queued";
        $plagiarismfile->similarityscore = null;
        $plagiarismfile->attempt = 0; // This will be incremented when saved.
        $plagiarismfile->transmatch = 0;
        $plagiarismfile->submissiontype = $submissiontype;

        if (!$fileid = $DB->insert_record('plagiarism_turnitin_files', $plagiarismfile)) {
            plagiarism_turnitin_activitylog("Insert record failed (CM: ".$cm->id.", User: ".$userid.")", "PP_NEW_SUB");
            $fileid = 0;
        }

        return $fileid;
    }

    /**
     * Reset submission values
     *
     **/
    private function reset_tii_submission($cm, $userid, $identifier, $currentsubmission, $submissiontype) {
        global $DB;

        $plagiarismfile = new stdClass();
        $plagiarismfile->id = $currentsubmission->id;
        $plagiarismfile->identifier = $identifier;
        $plagiarismfile->statuscode = "pending";
        $plagiarismfile->similarityscore = null;
        if ($currentsubmission->statuscode != "error") {
            $plagiarismfile->attempt = 1;
        }
        $plagiarismfile->transmatch = 0;
        $plagiarismfile->submissiontype = $submissiontype;
        $plagiarismfile->orcapable = null;
        $plagiarismfile->errormsg = null;
        $plagiarismfile->errorcode = null;

        if (!$DB->update_record('plagiarism_turnitin_files', $plagiarismfile)) {
            plagiarism_turnitin_activitylog("Update record failed (CM: ".$cm->id.", User: ".$userid.")", "PP_REPLACE_SUB");
        }
    }

    /**
     * Clean up previous file submissions.
     * Moodle will remove any old files or drafts during cron execution and file submission.
     */
    public function clean_old_turnitin_submissions($cm, $userid, $itemid, $submissiontype, $identifier) {
        global $DB, $CFG;
        $deletestr = '';

        // Create module object.
        $moduleclass = "turnitin_".$cm->modname;
        $moduleobject = new $moduleclass;

        if ($submissiontype == 'file') {
            // If this is an assignment then we need to account for previous attempts so get other items ids.
            if ($cm->modname == 'assign') {
                $itemids = $DB->get_records('assign_submission', array(
                                                                    'assignment' => $cm->instance,
                                                                    'userid' => $userid
                                                                    ), '', 'id');

                // Only proceed if we have item ids.
                if (empty($itemids)) {
                    return true;
                } else {
                    list($itemidsinsql, $itemidsparams) = $DB->get_in_or_equal(array_keys($itemids));
                    $itemidsinsql = ' itemid '.$itemidsinsql;
                    $params = array_merge(array($moduleobject->filecomponent, $userid), $itemidsparams);
                }

            } else {
                $itemidsinsql = ' itemid = ? ';
                $params = array($moduleobject->filecomponent, $userid, $itemid);
            }

            if ($moodlefiles = $DB->get_records_select('files', " component = ? AND userid = ? AND source IS NOT null AND ".$itemidsinsql,
                                                    $params, 'id DESC', 'pathnamehash')) {
                list($notinsql, $notinparams) = $DB->get_in_or_equal(array_keys($moodlefiles), SQL_PARAMS_QM, 'param', false);
                $typefield = ($CFG->dbtype == "oci") ? " to_char(submissiontype) " : " submissiontype ";
                $oldfiles = $DB->get_records_select('plagiarism_turnitin_files', " userid = ? AND cm = ? ".
                                                                            " AND ".$typefield." = ? AND identifier ".$notinsql,
                                                        array_merge(array($userid, $cm->id, 'file'), $notinparams));

                if (!empty($oldfiles)) {
                    foreach ($oldfiles as $oldfile) {
                        // Delete submission from Turnitin if we have an external id.
                        if (!is_null($oldfile->externalid)) {
                            $this->delete_tii_submission($cm, $oldfile->externalid, $userid);
                        }
                        $deletestr .= $oldfile->id.', ';
                    }

                    list($insql, $deleteparams) = $DB->get_in_or_equal(explode(',', substr($deletestr, 0, -2)));
                    $deletestr = " id ".$insql;
                }
            }

        } else if ($submissiontype == 'text_content') {
            $typefield = ($CFG->dbtype == "oci") ? " to_char(submissiontype) " : " submissiontype ";
            $deletestr = " userid = ? AND cm = ? AND ".$typefield." = ? AND identifier != ? ";
            $deleteparams = array($userid, $cm->id, 'text_content', $identifier);
        }

        // Delete from database.
        if (!empty($deletestr)) {
            $DB->delete_records_select('plagiarism_turnitin_files', $deletestr, $deleteparams);
        }
    }

    /**
     * Update an errored submission in the files table.
     */
    public function save_errored_submission($submissionid, $attempt, $errorcode) {
        global $DB;

        $plagiarismfile = new stdClass();
        $plagiarismfile->id = $submissionid;
        $plagiarismfile->statuscode = 'error';
        $plagiarismfile->attempt = $attempt + 1;
        $plagiarismfile->errorcode = $errorcode;

        if (!$DB->update_record('plagiarism_turnitin_files', $plagiarismfile)) {
            plagiarism_turnitin_activitylog("Update record failed (Submission: ".$submissionid.") - ", "PP_UPDATE_SUB_ERROR");
        }

        return true;
    }

    /**
     * Save the submission data to the files table.
     */
    public function save_submission($cm, $userid, $submissionid, $identifier, $statuscode, $tiisubmissionid, $submitter, $itemid,
                                    $submissiontype, $attempt, $errorcode = null, $errormsg = null) {
        global $DB;

        $plagiarismfile = new stdClass();
        if ($submissionid != 0) {
            $plagiarismfile->id = $submissionid;
        }
        $plagiarismfile->cm = $cm->id;
        $plagiarismfile->userid = $userid;
        $plagiarismfile->identifier = $identifier;
        $plagiarismfile->statuscode = $statuscode;
        $plagiarismfile->similarityscore = null;
        $plagiarismfile->externalid = $tiisubmissionid;
        $plagiarismfile->errorcode = (empty($errorcode)) ? null : $errorcode;
        $plagiarismfile->errormsg = (empty($errormsg)) ? null : $errormsg;
        $plagiarismfile->attempt = $attempt + 1;
        $plagiarismfile->transmatch = 0;
        $plagiarismfile->lastmodified = time();
        $plagiarismfile->submissiontype = $submissiontype;
        $plagiarismfile->itemid = $itemid;
        $plagiarismfile->submitter = $submitter;

        if ($submissionid != 0) {
            if (!$DB->update_record('plagiarism_turnitin_files', $plagiarismfile)) {
                plagiarism_turnitin_activitylog("Update record failed (CM: ".$cm->id.", User: ".$userid.") - ", "PP_UPDATE_SUB_ERROR");
            }
        } else {
            if (!$DB->insert_record('plagiarism_turnitin_files', $plagiarismfile)) {
                plagiarism_turnitin_activitylog("Insert record failed (CM: ".$cm->id.", User: ".$userid.") - ", "PP_INSERT_SUB_ERROR");
            }
        }

        return true;
    }

    /**
     * Delete a submission from Turnitin
     */
    public function delete_tii_submission($cm, $submissionid, $userid) {
        global $DB;
        $user = $DB->get_record('user', array('id' => $userid));

        // Initialise Comms Object.
        $turnitincomms = new turnitin_comms();
        $turnitincall = $turnitincomms->initialise_api();

        $submission = new TiiSubmission();
        $submission->setSubmissionId($submissionid);

        try {
            $turnitincall->deleteSubmission($submission);
        } catch (Exception $e) {
            $turnitincomms->handle_exceptions($e, 'turnitindeletionerror', false);

            mtrace('-------------------------');
            mtrace(get_string('turnitindeletionerror', 'plagiarism_turnitin').': '.$e->getMessage());
            mtrace('User:  '.$user->id.' - '.$user->firstname.' '.$user->lastname.' ('.$user->email.')');
            mtrace('Course Module: '.$cm->id.'');
            mtrace('-------------------------');
        }
    }

    /**
     * @return object The parameters for report gen speed.
     */
    public function plagiarism_get_report_gen_speed_params() {
        $genparams = new stdClass();
        $genparams->num_resubmissions = PLAGIARISM_TURNITIN_REPORT_GEN_SPEED_NUM_RESUBMISSIONS;
        $genparams->num_hours = PLAGIARISM_TURNITIN_REPORT_GEN_SPEED_NUM_HOURS;

        return $genparams;
    }

    /**
     * Set a config value for the admin settings.
     */
    public static function plagiarism_set_config($data, $property) {
        // Scenario when performing the upgrade script to copy settings from V2 to PP.
        if (strpos($property, 'plagiarism_turnitin') === false) {
            $field = "plagiarism_turnitin_".$property;
        } else {
            $field = $property;
        }

        if (isset($data->$property)) {
            set_config($field, $data->$property, 'plagiarism_turnitin');
        }
    }
}

/**
 * Add the Turnitin settings form to an add/edit activity page
 *
 * @param moodleform $formwrapper
 * @param MoodleQuickForm $mform
 * @return type
 */
function plagiarism_turnitin_coursemodule_standard_elements($formwrapper, $mform) {
    $pluginturnitin = new plagiarism_plugin_turnitin();

    $context = context_course::instance($formwrapper->get_course()->id);

    $pluginturnitin->add_settings_form_to_activity_page(
        $mform,
        $context,
        isset($formwrapper->get_current()->modulename) ? 'mod_'.$formwrapper->get_current()->modulename : '');
}

/**
 * Handle saving data from the Turnitin settings form..
 *
 * @param stdClass $data
 * @param stdClass $course
 */
function plagiarism_turnitin_coursemodule_edit_post_actions($data, $course) {
    $pluginturnitin = new plagiarism_plugin_turnitin();

    $pluginturnitin->save_form_data($data);

    return $data;
}

/**
 * Handle Scheduled Task to Update Report Scores from Turnitin.
 */
function plagiarism_turnitin_update_reports() {
    $pluginturnitin = new plagiarism_plugin_turnitin();
    return $pluginturnitin->cron_update_scores();
}

/**
 * Handle Scheduled Task to Send Queued Submissions to Turnitin.
 */
function plagiarism_turnitin_send_queued_submissions() {
    global $CFG, $DB;

    $config = plagiarism_plugin_turnitin::plagiarism_turnitin_admin_config();
    $pluginturnitin = new plagiarism_plugin_turnitin();

    // Don't attempt to call Turnitin if a connection to Turnitin could not be established.
    if (!$pluginturnitin->test_turnitin_connection()) {
        mtrace(get_string('ppeventsfailedconnection', 'plagiarism_turnitin'));
        return;
    }

    $queueditems = $DB->get_records_select("plagiarism_turnitin_files", "statuscode = 'queued' OR statuscode = 'pending'",
                                            null, 'lastmodified', '*', 0, PLAGIARISM_TURNITIN_CRON_SUBMISSIONS_LIMIT);

    // Submit each file individually to Turnitin.
    foreach ($queueditems as $queueditem) {

        // Don't proceed if we can not find a cm.
        $cm = get_coursemodule_from_id('', $queueditem->cm);
        if (empty($cm)) {
            $pluginturnitin->save_errored_submission($queueditem->id, $queueditem->attempt, 12);

            // Output a message in the cron for failed submission to Turnitin.
            $outputvars = new stdClass();
            $outputvars->id = $queueditem->id;
            $outputvars->cm = $queueditem->cm;
            $outputvars->userid = $queueditem->userid;

            plagiarism_turnitin_activitylog(get_string('errorcode12', 'plagiarism_turnitin', $outputvars), "PP_NO_COURSE");
            continue;
        }

        // Get various settings that we need.
        $errorcode = 0;
        $settings = $pluginturnitin->get_settings($cm->id);

        // Create module object.
        if (empty($cm->modname)) {
          $pluginturnitin->save_errored_submission($queueditem->id, $queueditem->attempt, 15);

          // Output a message in the cron for failed submission to Turnitin.
          $outputvars = new stdClass();
          $outputvars->id = $queueditem->id;
          $outputvars->cm = $queueditem->cm;
          $outputvars->userid = $queueditem->userid;

          plagiarism_turnitin_activitylog(get_string('errorcode15', 'plagiarism_turnitin', $outputvars), "PP_NO_ACTIVITY_MODULE");
          continue;
        }
        $moduleclass = "turnitin_".$cm->modname;
        $moduleobject = new $moduleclass;

        // Get module data.
        $moduledata = $DB->get_record($cm->modname, array('id' => $cm->instance));
        $moduledata->resubmission_allowed = false;

        if ($cm->modname == 'assign') {
            // Group submissions require userid = 0 when checking assign_submission.
            $userid = ($moduledata->teamsubmission) ? 0 : $queueditem->userid;

            $moodlesubmission = $DB->get_record('assign_submission',
                array('assignment' => $cm->instance,
                    'userid' => $userid,
                    'id' => $queueditem->itemid), 'status');

            $moduledata->resubmission_allowed = $moduleobject->is_resubmission_allowed(
                $cm->instance, $settings["plagiarism_report_gen"],
                $queueditem->submissiontype,
                $moduledata->attemptreopenmethod,
                $moodlesubmission->status
            );
        }

        // Get course data.
        $coursedata = $pluginturnitin->get_course_data($cm->id, $cm->course, 'cron');
        // Save failed submission if class can not be created.
        if (empty($coursedata->turnitin_cid)) {
            $pluginturnitin->save_errored_submission($queueditem->id, $queueditem->attempt, 10);
            continue;
        }
        // Update course data in Turnitin.
        $turnitinassignment = new turnitin_assignment(0);
        $turnitinassignment->edit_tii_course($coursedata);

        // Previously failed submissions may not have a value for submitter.
        if (empty($queueditem->submitter)) {
            $queueditem->submitter = $queueditem->userid;
        }

        // User Id should never be 0 but save as errored for old submissions where this may be the case.
        if (empty($queueditem->userid)) {
            $pluginturnitin->save_errored_submission($queueditem->id, $queueditem->attempt, 7);
            continue;
        }

        // Join User to course.
        try {
            $user = new turnitin_user($queueditem->userid, 'Learner', true, 'cron');
            $user->edit_tii_user();
            $user->join_user_to_class($coursedata->turnitin_cid);
        } catch (Exception $e) {
            $user = new turnitin_user($queueditem->userid, 'Learner', 'false', 'cron', 'false');
            $errorcode = 7;
        }

        // Update assignment details in Turnitin.
        $syncassignment = $pluginturnitin->sync_tii_assignment($cm, $coursedata->turnitin_cid, "cron", true);

        // Any errorcode from assignment sync needs to be saved.
        if (!empty($syncassignment['errorcode'])) {
            $errorcode = $syncassignment['errorcode'];
        }

        // Don't submit if a user has not accepted the eula.
        if ($queueditem->userid == $queueditem->submitter && $user->useragreementaccepted != 1) {
            $errorcode = 3;
        }

        // There should never not be a submission type, handle if there isn't just in case.
        if (!in_array($queueditem->submissiontype, array('file', 'text_content', 'forum_post', 'quiz_answer'))) {
            $errorcode = 11;
        }

        if (!empty($errorcode)) {
            // Save failed submission if user can not be joined to class or there was an error with the assignment.
            $pluginturnitin->save_errored_submission($queueditem->id, $queueditem->attempt, $errorcode);
            continue;
        }

        // Clean up old Turnitin submission files.
        if ($queueditem->itemid != 0 && $queueditem->submissiontype == 'file' && $cm->modname != 'forum') {
            $pluginturnitin->clean_old_turnitin_submissions($cm, $user->id, $queueditem->itemid, $queueditem->submissiontype,
                                                    $queueditem->identifier);
        }

        // Get more Submission Details as required.
        $apimethod = "createSubmission";
        switch ($queueditem->submissiontype) {
            case 'file':
            case 'text_content':

                // Get file data or prepare text submission.
                if ($queueditem->submissiontype == 'file') {
                    $fs = get_file_storage();
                    $file = $fs->get_file_by_hash($queueditem->identifier);

                    if (!$file) {
                        plagiarism_turnitin_activitylog('File not found for submission: '.$queueditem->id, 'PP_NO_FILE');
                        mtrace('File not found for submission. Identifier: '.$queueditem->id);
                        $errorcode = 9;
                        break;
                    }

                    // Prevent submissions queue breaking if file is too large and a larger size limit has been set in Moodle
                    if ($file->get_filesize() > PLAGIARISM_TURNITIN_MAX_FILE_UPLOAD_SIZE) {
                        $errorstring = 'File with ID '.$queueditem->id.' cannot be sent to turnitin: File size is '.$file->get_filesize().
                            ' bytes, and the max filesize that Turnitin can accept is '.PLAGIARISM_TURNITIN_MAX_FILE_UPLOAD_SIZE.' bytes.';
                        plagiarism_turnitin_activitylog($errorstring, 'PP_FILE_TOO_LARGE');
                        mtrace($errorstring);
                        $errorcode = 2;
                        break;
                    }

                    $title = $file->get_filename();
                    $filename = $file->get_filename();

                    try {
                        $textcontent = $file->get_content();
                    } catch (Exception $e) {
                        plagiarism_turnitin_activitylog('File content not found on submission: '.$queueditem->identifier, 'PP_NO_FILE');
                        mtrace($e);
                        mtrace('File content not found on submission. Identifier: '.$queueditem->identifier);
                        $errorcode = 9;
                        break;
                    }
                } else {
                    // Get the actual text content for a submission.
                    switch ($cm->modname) {
                        case 'assign':
                            $moodlesubmission = $DB->get_record('assign_submission', array('assignment' => $cm->instance,
                                            'userid' => $queueditem->userid, 'id' => $queueditem->itemid), 'id');
                            $moodletextsubmission = $DB->get_record('assignsubmission_onlinetext',
                                            array('submission' => $moodlesubmission->id), 'onlinetext');
                            $textcontent = $moodletextsubmission->onlinetext;
                            break;

                        case 'workshop':
                            $moodlesubmission = $DB->get_record('workshop_submissions',
                                                        array('id' => $queueditem->itemid), 'content');
                            $textcontent = $moodlesubmission->content;
                            break;
                    }

                    $title = 'onlinetext_'.$user->id."_".$cm->id."_".$cm->instance.'.txt';
                    $filename = $title;
                    $textcontent = html_to_text($textcontent);
                }

                // Use Replace submission method if resubmissions are allowed or create if we have no Turnitin Id.
                if (!is_null($queueditem->externalid)) {
                    $apimethod = ($moduledata->resubmission_allowed) ? "replaceSubmission" : "createSubmission";

                    // Delete old text content submissions from Turnitin if not replacing.
                    if ($settings["plagiarism_report_gen"] == 0 && $queueditem->submissiontype == 'text_content') {
                        $pluginturnitin->delete_tii_submission($cm, $queueditem->externalid, $queueditem->userid);
                    }
                }

                // Remove any old text submissions from Moodle DB if there are any as there is only one per submission.
                if (!empty($queueditem->itemid) && $queueditem->submissiontype == "text_content") {
                    $pluginturnitin->clean_old_turnitin_submissions($cm, $user->id, $queueditem->itemid,
                                                                    $queueditem->submissiontype, $queueditem->identifier);
                }

                break;

            case 'forum_post':
                if (!is_null($queueditem->externalid)) {
                    $apimethod = ($settings["plagiarism_report_gen"] == 0) ? "createSubmission" : "replaceSubmission";
                }

                $forumpost = $DB->get_record_select('forum_posts', " userid = ? AND id = ? ", array($user->id, $queueditem->itemid));

                if ($forumpost) {
                    $textcontent = strip_tags($forumpost->message);
                    $title = 'forumpost_'.$user->id."_".$cm->id."_".$cm->instance."_".$queueditem->itemid.'.txt';
                    $filename = $title;
                } else {
                    $errorcode = 9;
                }

                break;

            case 'quiz_answer':
                if (!is_null($queueditem->externalid)) {
                    $apimethod = ($settings["plagiarism_report_gen"] == 0) ? "createSubmission" : "replaceSubmission";
                }

                require_once($CFG->dirroot . '/mod/quiz/locallib.php');
                try {
                    if (class_exists('\mod_quiz\quiz_attempt')) {
                        $quizattemptclass = '\mod_quiz\quiz_attempt';
                    } else {
                        $quizattemptclass = 'quiz_attempt';
                    }
                    $attempt = $quizattemptclass::create($queueditem->itemid);

                } catch (Exception $e) {
                    plagiarism_turnitin_activitylog(get_string('errorcode14', 'plagiarism_turnitin'), "PP_NO_ATTEMPT");
                    $errorcode = 14;
                    break;
                }
                foreach ($attempt->get_slots() as $slot) {
                    $qa = $attempt->get_question_attempt($slot);
                    if ($queueditem->identifier == sha1($qa->get_response_summary().$slot)) {
                        $textcontent = $qa->get_response_summary();
                        break;
                    }
                }

                if (!empty($textcontent)) {
                    $textcontent = strip_tags($textcontent);
                    $title = 'quizanswer_'.$user->id."_".$cm->id."_".$cm->instance."_".$queueditem->itemid.'.txt';
                    $filename = $title;
                } else {
                    $errorcode = 9;
                }

                break;
        }

        // Save failed submission and don't process any further.
        if ($errorcode != 0) {
            $pluginturnitin->save_errored_submission($queueditem->id, $queueditem->attempt, $errorcode);
            continue;
        }

        // Read the stored file/content into a temp file for submitting.
        $submissiontitle = explode('.', $title);

        // Initialise file string array for naming the file.
        $filestring = array($submissiontitle[0], $cm->id);

        // Only include user's name and id if we're not using blind marking and student privacy.
        if ( empty($moduledata->blindmarking) && empty($config->plagiarism_turnitin_enablepseudo) ) {
            $userdetails = array(
                $user->id,
                $user->firstname,
                $user->lastname
            );

            $filestring = array_merge($userdetails, $filestring);
        }

        // Don't proceed if we can not create a tempfile.
        try {
            $tempfile = plagiarism_turnitin_tempfile($filestring, $filename);
        } catch (Exception $e) {
            $pluginturnitin->save_errored_submission($queueditem->id, $queueditem->attempt, 8);
            continue;
        }

        $fh = fopen($tempfile, "w");
        fwrite($fh, $textcontent);
        fclose($fh);

        // Create submission object.
        $submission = new TiiSubmission();
        $submission->setAssignmentId($syncassignment['tiiassignmentid']);
        if ($apimethod == "replaceSubmission") {
            $submission->setSubmissionId($queueditem->externalid);
        }
        $submission->setTitle($title);
        $submission->setAuthorUserId($user->tiiuserid);

        // Account for submission by teacher in assignment module.
        $submission->setSubmitterUserId($user->tiiuserid);
        $submission->setRole('Learner');

        if ($queueditem->userid != $queueditem->submitter) {

            $instructor = new turnitin_user($queueditem->submitter, 'Instructor');

            // These should be true but in case of an edge case where a user has been deleted in Tii.
            if ($instructor->edit_tii_user() && $instructor->join_user_to_class($coursedata->turnitin_cid)) {

                $submission->setSubmitterUserId($instructor->tiiuserid);
                $submission->setRole('Instructor');
            }
        }

        $submission->setSubmissionDataPath($tempfile);

        // Initialise Comms Object.
        $turnitincomms = new turnitin_comms();
        $turnitincall = $turnitincomms->initialise_api();

        try {
            $response = $turnitincall->$apimethod($submission);
            $newsubmission = $response->getSubmission();
            $tiisubmissionid = $newsubmission->getSubmissionId();

            $pluginturnitin->save_submission($cm, $user->id, $queueditem->id, $queueditem->identifier, 'success', $tiisubmissionid,
                                    $queueditem->submitter, $queueditem->itemid, $queueditem->submissiontype, $queueditem->attempt);

            // Delete the tempfile.
            if (!is_null($tempfile)) {
                unlink($tempfile);
            }

            plagiarism_turnitin_lock_anonymous_marking($cm->id);

            // Send a message to the user's Moodle inbox with the digital receipt.
            $receipt = new pp_receipt_message();
            $input = array(
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'submission_title' => $title,
                'assignment_name' => $moduledata->name,
                'course_fullname' => $coursedata->turnitin_ctl,
                'submission_date' => date('d-M-Y h:iA'),
                'submission_id' => $tiisubmissionid
            );

            $message = $receipt->build_message($input);
            $receipt->send_message($user->id, $message, $cm->course);

            // Output a message in the cron for successfull submission to Turnitin.
            $outputvars = new stdClass();
            $outputvars->title = $title;
            $outputvars->submissionid = $tiisubmissionid;
            $outputvars->assignmentname = $moduledata->name;
            $outputvars->coursename = $coursedata->turnitin_ctl;

            mtrace(get_string('cronsubmittedsuccessfully', 'plagiarism_turnitin', $outputvars));
        } catch (Exception $e) {

            // Save that submission errored.
            $submissionerrormsg = get_string('pp_submission_error', 'plagiarism_turnitin').' '.$e->getMessage();
            $pluginturnitin->save_submission($cm, $user->id, $queueditem->id, $queueditem->identifier, 'error', null,
                                    $queueditem->submitter, $queueditem->itemid, $queueditem->submissiontype,
                                    $queueditem->attempt, 0, $submissionerrormsg);

            $errorstring = (empty($queueditem->externalid)) ? "pp_createsubmissionerror" : "pp_updatesubmissionerror";
            $turnitincomms->handle_exceptions($e, $errorstring, false);

            // Output error in the cron.
            mtrace('-------------------------');
            mtrace(get_string('pp_submission_error', 'plagiarism_turnitin').': '.$e->getMessage());
            mtrace('User:  '.$user->id.' - '.$user->firstname.' '.$user->lastname.' ('.$user->email.')');
            mtrace('Course Module: '.$cm->id.'');
            mtrace('-------------------------');
        }
    }
}

/**
 * Creates a temp file for submission to Turnitin, uses a random number suffixed with the stored filename
 *
 * @param array $filename Used to build a more readable filename
 * @param string $suffix The file extension for the upload
 * @return string $file The filepath of the temp file
 */
function plagiarism_turnitin_tempfile(array $filename, $suffix) {
    $filename = implode('_', $filename);
    $filename = str_replace(' ', '_', $filename);
    $filename = clean_param(strip_tags($filename), PARAM_FILE);

    $tempdir = make_temp_directory('plagiarism_turnitin');

    // Get the file extension (if there is one).
    $pathparts = explode('.', $suffix);
    $ext = '';
    if (count($pathparts) > 1) {
        $ext = '.' . array_pop($pathparts);
    }

    $permittedstrlength = PLAGIARISM_TURNITIN_MAX_FILENAME_LENGTH - mb_strlen($tempdir.DIRECTORY_SEPARATOR, 'UTF-8');
    $extlength = mb_strlen('_' . mt_getrandmax() . $ext, 'UTF-8');
    if ($extlength > $permittedstrlength) {
        // Someone has likely used a long filename or the tempdir path is huge, so preserve the extension if possible.
        $extlength = $permittedstrlength;
    }

    // Shorten the filename as needed, taking the extension into consideration.
    $permittedstrlength -= $extlength;
    $filename = mb_substr($filename, 0, $permittedstrlength, 'UTF-8');

    // Ensure the filename doesn't have any characters that are invalid for the fs.
    $filename = clean_param($filename . mb_substr('_' . mt_rand() . $ext, 0, $extlength, 'UTF-8'), PARAM_FILE);

    $tries = 0;
    do {
        if ($tries == 10) {
            throw new invalid_dataroot_permissions("Turnitin plagiarism plugin temporary file cannot be created.");
        }
        $tries++;

        $file = $tempdir . DIRECTORY_SEPARATOR . $filename;
    } while ( !touch($file) );

    return $file;
}

/**
 * Abstracted version of print_error()
 *
 * @param string $input The error string if module = null otherwise the language string called by get_string()
 * @param string $module The module string
 * @param string $param The parameter to send to use as the $a optional object in get_string()
 * @param string $file The file where the error occured
 * @param string $line The line number where the error occured
 */
function plagiarism_turnitin_print_error($input, $module = 'plagiarism_turnitin',
                                         $link = null, $param = null, $file = __FILE__, $line = __LINE__) {
    global $CFG;

    // This is to be changed in INT-10691.
    plagiarism_turnitin_activitylog($input, "PRINT_ERROR");

    $message = (is_null($module)) ? $input : get_string($input, $module, $param);
    $linkid = optional_param('id', 0, PARAM_INT);

    if (is_null($link)) {
        if (substr_count($_SERVER["PHP_SELF"], "assign/view.php") > 0) {
            $mod = "assign";
        } else if (substr_count($_SERVER["PHP_SELF"], "forum/view.php") > 0) {
            $mod = "forum";
        } else if (substr_count($_SERVER["PHP_SELF"], "workshop/view.php") > 0) {
            $mod = "workshop";
        }
        $link = (!empty($linkid)) ? $CFG->wwwroot.'/'.$mod.'/view.php?id='.$linkid : $CFG->wwwroot;
    }

    if (basename($file) != "lib.php") {
        $message .= ' ('.basename($file).' | '.$line.')';
    }

    print_error($input, 'plagiarism_turnitin', $link, $message);
    exit();
}

/**
 * Override Moodle's mtrace function for methods shared with tasks.
 */
function plagiarism_turnitin_mtrace($string, $eol) {
    return true;
}

/**
 * Log activity / errors
 *
 * @param string $string The string describing the activity
 * @param string $activity The activity prompting the log
 * e.g. PRINT_ERROR (default), API_ERROR, INCLUDE, REQUIRE_ONCE, REQUEST, REDIRECT
 */
function plagiarism_turnitin_activitylog($string, $activity) {
    global $CFG;

    static $config;
    if (empty($config)) {
        $config = plagiarism_plugin_turnitin::plagiarism_turnitin_admin_config();
    }

    if (isset($config->plagiarism_turnitin_enablediagnostic)) {
        // We only keep 10 log files, delete any additional files.
        $prefix = "activitylog_";

        $dirpath = $CFG->tempdir."/plagiarism_turnitin/logs";
        if (!file_exists($dirpath)) {
            mkdir($dirpath, 0777, true);
        }
        $dir = opendir($dirpath);
        $files = array();
        while ($entry = readdir($dir)) {
            if (substr(basename($entry), 0, 1) != "." AND substr_count(basename($entry), $prefix) > 0) {
                $files[] = basename($entry);
            }
        }
        sort($files);
        for ($i = 0; $i < count($files) - 10; $i++) {
            unlink($dirpath."/".$files[$i]);
        }

        // Replace <br> tags with new line character.
        $string = str_replace("<br/>", "\r\n", $string);

        // Write to log file.
        $filepath = $dirpath."/".$prefix.gmdate('Y-m-d', time()).".txt";
        $file = fopen($filepath, 'a');
        $output = date('Y-m-d H:i:s O')." (".$activity.")"." - ".$string."\r\n";
        fwrite($file, $output);
        fclose($file);
    }
}
