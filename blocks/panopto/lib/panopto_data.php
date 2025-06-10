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
 * Contains main Panopto getters
 *
 * @package block_panopto
 * @copyright  Panopto 2009 - 2018 /With contributions from Spenser Jones (sjones@ambrose.edu), and Tim Lock
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

global $CFG;
if (empty($CFG)) {
    // @codingStandardsIgnoreLine
    require_once('../../config.php');
}
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->libdir . '/dmllib.php');
require_once($CFG->libdir .'/filelib.php');
require_once(dirname(__FILE__) . '/lti/panoptoblock_lti_utility.php');
require_once(dirname(__FILE__) . '/block_panopto_lib.php');
require_once(dirname(__FILE__) . '/panopto_category_data.php');
require_once(dirname(__FILE__) . '/panopto_auth_soap_client.php');
require_once(dirname(__FILE__) . '/panopto_user_soap_client.php');
require_once(dirname(__FILE__) . '/panopto_session_soap_client.php');

/**
 * Panopto data object. Contains info required for provisioning a course with Panopto.
 *
 * @package block_panopto
 * @copyright  Panopto 2009 - 2015
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class panopto_data {

    /**
     * @var string $instancename course id class is being provisioned for
     */
    public $instancename;

    /**
     * @var int $moodlecourseid current active Moodle course id
     */
    public $moodlecourseid;

    /**
     * @var string $servername
     */
    public $servername;

    /**
     * @var int $applicationkey
     */
    public $applicationkey;

    /**
     * @var object $sessionmanager instance of the session soap client
     */
    public $sessionmanager;

    /**
     * @var object $usermanager instance of the user soap client
     */
    public $usermanager;

    /**
     * @var object $authmanager instance of the auth soap client
     */
    public $authmanager;

    /**
     * @var int $sessiongroupid is for the current session
     */
    public $sessiongroupid;

    /**
     * @var string $uname username
     */
    public $uname;

    /**
     * @var string $currentcoursename The name of the current course in Panopto folder name format
     */
    public $currentcoursename;

    /**
     * @var int $maxloglength the maximum length we will allow logs to be when adding a log to Panopto.
     */
    private static $maxloglength = 1500;

    /**
     * @var int $requireversion Panopto only supports versions of Moodle newer than v2.7(2014051200).
     */
    private static $requiredversion = 2014051200;

    /**
     * @var string $requiredpanoptoversion Any block_panopto newer than 2017061000
     * will require a Panopto server to be at least this version to succeed.
     */
    public static $requiredpanoptoversion = '5.4.0';

    /**
     * @var string $unprovisionrequiredpanoptoversion The UnprovisionExternalCourse endpoint was only added in 7.0.0 so
     * anyone using an older Panopto server should not be able to attempt to use this endpoint.
     */
    public static $unprovisionrequiredpanoptoversion = '7.0.0';

    /**
     * @var string $ccv2requiredpanoptoversion if the Panopto server is using this version then all course copy calls must
     *   use the new endpoint.
     */
    public static $ccv2requiredpanoptoversion = '12.0.0';

    /**
     * @var string $apiassignmentfolderspanoptoversion if the Panopto server is using this version then we can filter
     *   assignment folders.
     */
    public static $apiassignmentfolderspanoptoversion = '13.14.0.00000';

    /**
     * Returns an array of possible values for the Panopto folder name style
     *
     * @return array
     */
    public static function getpossiblefoldernamestyles() {
        return [
            'fullname' => get_string('name_style_fullname', 'block_panopto'),
            'shortname' => get_string('name_style_shortname', 'block_panopto'),
            'combination' => get_string('name_style_combination', 'block_panopto'),
        ];
    }
    /**
     * Returns an array of possible values for the Panopto folder name style
     *
     * @return array
     */
    public static function getpossibleprovisiontypes() {
        return [
            'off' => get_string('autoprovision_off', 'block_panopto'),
            'oncoursecreation' => get_string('autoprovision_new_courses', 'block_panopto'),
            'onblockview' => get_string('autoprovision_on_block_view', 'block_panopto'),
        ];
    }
    /**
     * Returns an array of possible values for copy provisioning.
     *
     * @return array
     */
    public static function getpossiblecopyprovisiontypes() {
        return [
            'both' => get_string('provision_both_on_copy', 'block_panopto'),
            'onlytarget' => get_string('provision_only_target_on_copy', 'block_panopto'),
        ];
    }
    /**
     * Return the possible list of SSO sync types
     *
     * @return array
     */
    public static function getpossiblessosynctypes() {
        return [
            'nosync' => get_string('sso_type_nosync', 'block_panopto'),
            'sync' => get_string('sso_type_sync', 'block_panopto'),
            'asyncsync' => get_string('sso_type_asyncsync', 'block_panopto'),
        ];
    }

    /**
     * Delete any existing ashoc tasks
     *
     * @return $DB
     */
    public static function remove_all_panopto_adhoc_tasks() {
        global $DB;

        return $DB->delete_records_select('task_adhoc', $DB->sql_like('classname', '?'), ['%block_panopto%task%']);
    }

    /**
     * Main constructor
     *
     * @param int $moodlecourseid course id class is being provisioned for.
     * Can be null for bulk provisioning and manual provisioning.
     */
    public function __construct($moodlecourseid) {
        global $USER, $DB;

        // Fetch global settings from DB.
        $this->instancename = get_config('block_panopto', 'instance_name');

        // Get servername and application key specific to Moodle course if ID is specified.
        if (isset($moodlecourseid) && !empty($moodlecourseid)) {
            $foldermapdata = $DB->get_record('block_panopto_foldermap',
                ['moodleid' => $moodlecourseid], 'panopto_server,panopto_id');
            if (!empty($foldermapdata)) {
                $this->servername = $foldermapdata->panopto_server;
                $this->sessiongroupid = $foldermapdata->panopto_id;
                $this->applicationkey = panopto_get_app_key($this->servername);
            }

            $this->moodlecourseid = $moodlecourseid;
        }

        if (isset($USER->username)) {
            $username = $USER->username;
        } else {
            $username = 'guest';
        }

        $this->uname = $username;
    }

    /**
     * Returns SystemInfo.
     *
     * @return object
     */
    public function get_recorder_download_urls() {

        $this->ensure_session_manager();

        return $this->sessionmanager->get_recorder_download_urls();
    }

    /**
     * Returns if the logged in user can provision.
     *
     * @param int $courseid the Moodle id of the course we are checking
     * @return bool
     */
    public function can_user_provision($courseid) {
        global $USER;

        // Get the context of the course so we can get capabilities.
        $context = context_course::instance($courseid, MUST_EXIST);

        return (has_capability('block/panopto:provision_aspublisher', $context, $USER->id) ||
            has_capability('block/panopto:provision_asteacher', $context, $USER->id) ||
            has_capability('moodle/course:update', $context, $USER->id)) &&
            has_capability('block/panopto:provision_course', $context, $USER->id);
    }

    /**
     * Return the correct role for a user, given a context.
     *
     * @param context_course $context context id
     * @param int $userid user id for which we are getting the role.
     * @return string
     */
    public static function get_role_from_context($context, $userid) {
        $role = 'Viewer';

        $canprovisionaspublisher = has_capability('block/panopto:provision_aspublisher', $context, $userid);
        $canprovisionasteacher = has_capability('block/panopto:provision_asteacher', $context, $userid);

        if ($canprovisionaspublisher) {
            if ($canprovisionasteacher) {
                $role = 'Creator/Publisher';
            } else {
                $role = 'Publisher';
            }
        } else if ($canprovisionasteacher) {
            $role = 'Creator';
        }

        return $role;
    }

    /**
     * Return the session manager, if it does not yet exist try to create it.
     */
    public function ensure_session_manager() {
        // If no session soap client exists instantiate one.
        if (!isset($this->sessionmanager)) {
            $this->sessionmanager = panopto_instantiate_session_soap_client(
                $this->uname,
                $this->servername,
                $this->applicationkey
            );

            if (!isset($this->sessionmanager)) {
                self::print_log(get_string('api_manager_unavailable', 'block_panopto', 'session'));
            }
        }
    }

    /**
     * Return the auth manager, if it does not yet exist try to create it.
     */
    public function ensure_auth_manager() {
        // If no session soap client exists instantiate one.
        if (!isset($this->authmanager)) {
            // If no auth soap client for this instance, instantiate one.
            $this->authmanager = panopto_instantiate_auth_soap_client(
                $this->uname,
                $this->servername,
                $this->applicationkey
            );

            if (!isset($this->authmanager)) {
                self::print_log(get_string('api_manager_unavailable', 'block_panopto', 'auth'));
            }
        }
    }

    /**
     * Return the user manager, if it does not yet exist try to create it.
     *
     * @param string $usertomanage since the User management works on the user passed in
     * through the auth param we need to pass the uname for the user we are managing.
     */
    public function ensure_user_manager($usertomanage) {
        // If no session soap client exists instantiate one.
        if (!isset($this->usermanager) ||
            ($this->usermanager->authparam->UserKey !== $this->panopto_decorate_username($usertomanage))) {

            // If no auth soap client for this instance, instantiate one.
            $this->usermanager = panopto_instantiate_user_soap_client(
                $usertomanage,
                $this->servername,
                $this->applicationkey
            );

            if (!isset($this->usermanager)) {
                self::print_log(get_string('api_manager_unavailable', 'block_panopto', 'user'));
            }
        }
    }

    /**
     * Create the Panopto course folder and populate its ACLs.
     *
     * @param object $provisioninginfo info for course being provisioned
     * @param bool $skipusersync should we skip user sync or not
     * @return object
     */
    public function provision_course($provisioninginfo, $skipusersync) {
        global $CFG, $USER, $DB;

        if (isset($provisioninginfo->fullname) && !empty($provisioninginfo->fullname) &&
            isset($provisioninginfo->externalcourseid) && !empty($provisioninginfo->externalcourseid)) {

            self::print_log_verbose(get_string('attempt_provision_course', 'block_panopto', $provisioninginfo->externalcourseid));

            $this->ensure_session_manager();

            if (isset($this->sessiongroupid) && !empty($this->sessiongroupid) && ($this->sessiongroupid !== false)) {

                self::print_log_verbose(get_string('course_already_provisioned', 'block_panopto', $this->sessiongroupid));

                $courseinfo = $this->sessionmanager->set_external_course_access_for_roles(
                    $provisioninginfo->fullname,
                    $provisioninginfo->externalcourseid,
                    $this->sessiongroupid
                );
            } else {
                $courseinfo = $this->sessionmanager->provision_external_course_with_roles(
                    $provisioninginfo->fullname,
                    $provisioninginfo->externalcourseid
                );
            }

            if (isset($courseinfo->Id) && !isset($courseinfo->errormessage)) {

                // Store the Panopto folder Id in the foldermap table so we know it exists.
                self::set_course_foldermap(
                    $this->moodlecourseid,
                    $courseinfo->Id,
                    $this->servername,
                    $this->applicationkey,
                    $provisioninginfo->externalcourseid
                );

                $this->sessiongroupid = $courseinfo->Id;

                self::print_log_verbose(get_string('provision_successful', 'block_panopto', $this->moodlecourseid));

                $this->ensure_auth_manager();

                $currentblockversion = $DB->get_record(
                    'config_plugins',
                    ['plugin' => 'block_panopto', 'name' => 'version'],
                    'value'
                );

                // If we succeeded in provisioning lets send the Panopto server some updated integration information.
                $this->authmanager->report_integration_info(
                    get_config('block_panopto', 'instance_name'),
                    $currentblockversion->value,
                    $CFG->version
                );

                $coursecontext = context_course::instance($this->moodlecourseid);
                $enrolledusers = get_enrolled_users($coursecontext);

                $courseinfo->viewers = [];
                $courseinfo->creators = [];
                $courseinfo->publishers = [];

                // Sync every user enrolled in the course.
                foreach ($enrolledusers as $enrolleduser) {
                    $userrole = self::get_role_from_context($coursecontext, $enrolleduser->id);
                    $panoptousername = $this->instancename . '\\' . $enrolleduser->username;

                    if (strpos($userrole, 'Publisher') !== false) {
                        $courseinfo->publishers[] = $panoptousername;
                        if (strpos($userrole, 'Creator') !== false) {
                            $courseinfo->creators[] = $panoptousername;
                        }
                    } else if (strpos($userrole, 'Creator') !== false) {
                        $courseinfo->creators[] = $panoptousername;
                    } else {
                        $courseinfo->viewers[] = $panoptousername;
                    }

                    // Syncs every user enrolled in the course, this is fairly expensive so it should be normally turned off.
                    if (get_config('block_panopto', 'sync_after_provisioning')) {
                        $this->sync_external_user($enrolleduser->id);
                    }
                }

                if (!$skipusersync && $this->uname !== 'guest') {
                    // This is intended to make sure provisioning teachers get access without relogging,
                    // so we only need to perform this if we aren't syncing all enrolled users.

                    // Update permissions so user can see everything they should.
                    $this->sync_external_user($USER->id);
                }

                if (get_config('block_panopto', 'sync_category_after_course_provision')) {
                    $targetcategory = $DB->get_field(
                        'course',
                        'category',
                        ['id' => $this->moodlecourseid]
                    );

                    if (isset($targetcategory) && !empty($targetcategory)) {
                        $categorydata = new \panopto_category_data($targetcategory, $this->servername, $this->applicationkey);

                        $newcategories = $categorydata->ensure_category_branch(false, $this);
                    }
                }
            } else {
                $provisionresponse = $courseinfo;
                // Give the user some basic info they can use to debug or send to AE.
                $courseinfo->moodlecourseid = $this->moodlecourseid;
                $courseinfo->servername = $this->servername;
                $courseinfo->applicationkey = $this->applicationkey;
            }
        } else {
            // Give the user some basic info they can use to debug or send to AE.
            $courseinfo = new stdClass;
            $courseinfo->moodlecourseid = $this->moodlecourseid;
            $courseinfo->servername = $this->servername;

            if (isset($provisioninginfo->accesserror) && $provisioninginfo->accesserror === true) {
                $courseinfo->accesserror = true;
            } else {
                self::print_log(get_string('unknown_provisioning_error', 'block_panopto'));
                $courseinfo->unknownerror = true;
            }
        }

        return $courseinfo;
    }


    /**
     * Removes the external context and group mappings from a folder in Panopto
     *
     * @return bool
     */
    public function unprovision_course() {
        global $CFG, $USER, $DB;

        $this->ensure_auth_manager();
        $activepanoptoserverversion = $this->authmanager->get_server_version();
        $hasvalidpanoptoversion = version_compare(
            $activepanoptoserverversion,
            self::$unprovisionrequiredpanoptoversion,
            '>='
        );

        if (!$hasvalidpanoptoversion) {
            self::print_log(get_string('unprovision_requires_newer_server', 'block_panopto'));
            return false;
        } else {
            self::print_log_verbose(get_string('attempt_unprovision_course', 'block_panopto', $this->moodlecourseid));

            try {
                if (!empty($this->moodlecourseid)) {
                    $this->ensure_session_manager();
                    $this->sessionmanager->unprovision_external_course(
                        $this->moodlecourseid
                    );
                }
            } catch (Exception $e) {
                self::print_log($e->getMessage());
                return false;
            }

            // Delete the relation on Moodle if the Panopto side link was succesfully removed.
            self::delete_panopto_relation($this->moodlecourseid, true);
            return true;
        }
    }

    /**
     * Fetch course name and membership info from DB in preparation for provisioning operation.
     *
     * @return object
     */
    public function get_provisioning_info() {
        global $DB;

        self::print_log_verbose(get_string('get_provisioning_info', 'block_panopto', $this->moodlecourseid));

        $this->check_course_role_mappings();

        $provisioninginfo = new stdClass;

        // If we are provisioning a course with a panopto_id set we should provision that folder.
        // We need to keep this because users can map to folders that weren't created in Moodle,
        // so Moodle has no knowledge of the externalcourseid.
        $hasvalidpanoptoid = isset($this->sessiongroupid) && !empty($this->sessiongroupid);

        if ($hasvalidpanoptoid) {
            $mappedpanoptocourse = $this->get_folders_by_id_no_sync();
        }

        if (isset($mappedpanoptocourse) && !empty($mappedpanoptocourse->Name)) {
            $provisioninginfo->sessiongroupid = $this->sessiongroupid;
            $provisioninginfo->fullname = $mappedpanoptocourse->Name;
        } else if (isset($mappedpanoptocourse) &&
                isset($mappedpanoptocourse->noaccess) &&
                $mappedpanoptocourse->noaccess == true) {

            $provisioninginfo->accesserror = true;
            return $provisioninginfo;
        } else {
            if (isset($mappedpanoptocourse) &&
                isset($mappedpanoptocourse->notfound) &&
                $mappedpanoptocourse->notfound == true) {

                // If we had a sessiongroupid set from a previous folder, but that folder was not found on Panopto.
                // Set the current sessiongroupid to null to allow for a fresh provisioning/folder.
                // Provisioning will fail if this is not done, the wrong API endpoint will be called.
                self::print_log(get_string('folder_not_found_error', 'block_panopto'));
                $this->sessiongroupid = null;
                $provisioninginfo->couldnotfindmappedfolder = true;
            }

            $coursenameinfo = $DB->get_record(
                'course',
                ['id' => $this->moodlecourseid],
                'fullname,shortname'
            );

            if (!empty($coursenameinfo)) {
                $provisioninginfo->shortname = $coursenameinfo->shortname;
                $provisioninginfo->longname = $coursenameinfo->fullname;

                $provisioninginfo->fullname = $this->get_new_folder_name(
                    $provisioninginfo->shortname,
                    $provisioninginfo->longname
                );
            }
        }

        // Always set this, even in the case of an already existing folder we will overwrite the old Id with this one.
        $provisioninginfo->externalcourseid = $this->instancename . ':' . $this->moodlecourseid;

        return $provisioninginfo;
    }

    /**
     * Renamed the associated Panopto folder to match the Moodle course name
     *
     */
    public function update_folder_name() {
        $this->ensure_session_manager();
        return $this->sessionmanager->update_folder_name($this->sessiongroupid, $this->currentcoursename);
    }

    /**
     * Attempts to map the externalId(moodle course Id) to the currently assigned Panopto folder.
     * A properly mapped externalId is necessary for most non-plugin LTI based workflows so we need to make sure
     *   this is kept up-to-date when a user custom maps a new folder to the course.
     */
    public function update_folder_external_id_with_provider() {
        $this->ensure_session_manager();
        return $this->sessionmanager->update_folder_external_id_with_provider(
            $this->sessiongroupid,
            $this->moodlecourseid,
            $this->instancename
        );
    }

    /**
     * Generates the name for a Panopto folder depending on the course name and chosen folder name style
     *
     * @param string $shortname course short name
     * @param string $longname course long name
     * @return string
     */
    public function get_new_folder_name($shortname, $longname) {
        global $DB;

        if (empty($shortname) || empty($longname)) {

            $coursenameinfo = $DB->get_record(
                'course',
                ['id' => $this->moodlecourseid],
                'fullname,shortname'
            );

            if (!empty($coursenameinfo)) {
                $shortname = $coursenameinfo->shortname;
                $longname = $coursenameinfo->fullname;
            }
        }

        if (!isset($shortname) || empty($shortname)) {
            $shortname = substr($longname, 0, 5);
        }

        $fullname = '';

        $selectednamestyle = get_config('block_panopto', 'folder_name_style');

        switch ($selectednamestyle) {
            case 'combination':
                $fullname .= $shortname . ': ' . $longname;
            break;
            case 'shortname':
                $fullname .= $shortname;
            break;
            case 'fullname':
            default:
                $fullname .= $longname;
            break;
        }

        $this->currentcoursename = $fullname;
        return $fullname;
    }

    /**
     * This will copy Panopto content from one course panopto folder to another course panopto folder
     *
     * @param int $originalcourseid - the moodle id of the course we are trying to copy Panopto content from
     * @return array
     */
    public function copy_panopto_content($originalcourseid) {
        global $USER;

        $importresults = [];

        $coursecopytask = new stdClass;
        $coursecopytask->IdProviderName = $this->instancename;
        $coursecopytask->SourceCourseContexts = [$originalcourseid];
        $coursecopytask->TargetCourseContext = $this->moodlecourseid;

        // The api call takes an array but moodle logging can't handle this well so an extra variable is needed for logging.
        $coursecopylogdata = new stdClass;
        $coursecopylogdata->SourceCourseContext = $originalcourseid;
        $coursecopylogdata->TargetCourseContext = $this->moodlecourseid;

        self::print_log_verbose(get_string('copy_course_init', 'block_panopto', $coursecopylogdata));

        $this->sync_external_user($USER->id);

        $this->add_new_course_import($this->moodlecourseid, $originalcourseid);

        $importpanopto = new \panopto_data($originalcourseid);
        $provisioninginfo = $this->get_provisioning_info();

        if (!isset($importpanopto->sessiongroupid)) {
            self::print_log(get_string('import_not_mapped', 'block_panopto'));
        } else if (!isset($provisioninginfo->accesserror)) {
            $this->ensure_auth_manager();

            // This call will log the user into Panopto using the SOAP API and it will also store the Panopto cookies.
            $this->authmanager->log_on_with_external_provider();

            // Only do this code if we have proper access to the target Panopto course folder.
            $location = 'https://'. $this->servername . '/Panopto/api/v1.0-beta/course/copy';

            $curl = new \curl();
            $aspxauthcookie = "";
            foreach ($this->authmanager->panoptoauthcookies as $key => $value) {
                if (strpos(strtolower($key), 'aspxauth') !== false) {
                    $aspxauthcookie = $value;
                    break;
                }
            }

            if (empty($aspxauthcookie)) {
                $importresult = new stdClass;
                $importresult->importedcourseid = $originalcourseid;
                $importresult->errormessage = get_string('copy_api_error_auth', 'block_panopto', $this->servername);
                $importresults[] = $importresult;
                self::print_log(get_string('copy_api_error_auth', 'block_panopto', $importresult));
                return $importresults;
            }

            $options = [
                'CURLOPT_VERBOSE' => false,
                'CURLOPT_RETURNTRANSFER' => true,
                'CURLOPT_HEADER' => false,
                'CURLOPT_HTTPHEADER' => ['Content-Type: application/json',
                                              'Cookie: .ASPXAUTH='.$aspxauthcookie],
            ];

            $sockettimeout = get_config('block_panopto', 'panopto_socket_timeout');
            $connectiontimeout = get_config('block_panopto', 'panopto_connection_timeout');

            if (!!$sockettimeout) {
                $options['CURLOPT_TIMEOUT'] = $sockettimeout;
            }

            if (!!$connectiontimeout) {
                $options['CURLOPT_CONNECTTIMEOUT'] = $connectiontimeout;
            }

            $proxyhost = get_config('block_panopto', 'wsdl_proxy_host');
            $proxyport = get_config('block_panopto', 'wsdl_proxy_port');

            if (!empty($proxyhost)) {
                $options['CURLOPT_PROXY'] = $proxyhost;
            }

            if (!empty($proxyport)) {
                $options['CURLOPT_PROXYPORT'] = $proxyport;
            }

            $response = json_decode($curl->post($location, json_encode($coursecopytask), $options));

            if (empty($response)) {
                $importresult = new stdClass;
                $importresult->importedcourseid = $originalcourseid;
                $importresults[] = $importresult;
            } else {
                $importresult = new stdClass;
                $importresult->importedcourseid = $originalcourseid;
                $importresult->errormessage = get_string('copy_api_error', 'block_panopto', $importresult);
                $importresults[] = $importresult;
                self::print_log(get_string('copy_api_error_response', 'block_panopto', $response));
            }
        } else {
            $importresult = new stdClass;
            $importresult->importedcourseid = $originalcourseid;
            $importresult->errormessage = get_string('copy_access_error', 'block_panopto', $importresult);
            $importresults[] = $importresult;
        }

        return $importresults;
    }

    /**
     * Initializes and syncs a possible new import
     *
     * @param int $newimportid the id of the course being imported
     * @return array
     */
    public function init_and_sync_import_ccv1($newimportid) {
        $importresults = [];
        $handledimports = [];

        self::print_log_verbose(get_string('init_import_target', 'block_panopto', $this->moodlecourseid));
        self::print_log_verbose(get_string('init_import_source', 'block_panopto', $newimportid));

        $currentimportsources = self::get_import_list($this->moodlecourseid);
        $this->ensure_session_manager();
        $importinarray = in_array($newimportid, $currentimportsources);

        if (!$importinarray) {
            // If a course is already listed as an import we don't need to add it to the import array,
            // but we can still resync the groups.
            self::add_new_course_import($this->moodlecourseid, $newimportid);
        }

        $importpanopto = new \panopto_data($newimportid);
        $provisioninginfo = $this->get_provisioning_info();

        if (!isset($importpanopto->sessiongroupid)) {
            self::print_log(get_string('import_not_mapped', 'block_panopto'));
        } else if (!isset($provisioninginfo->accesserror)) {
            $sessiongroupids = [];
            $sessiongroupids[] = $importpanopto->sessiongroupid;
            // We need to make sure this course gets access to anything the course it imported had access to.
            $nestedimports = self::get_import_list($newimportid);
            $nestedimportresults = [];
            foreach ($nestedimports as $nestedimportid) {
                $nestedimportpanopto = new \panopto_data($nestedimportid);
                // If we are importing a nested child make sure we have not already imported.
                if (isset($nestedimportpanopto->sessiongroupid) && !in_array($nestedimportid, $handledimports)) {
                    $handledimports[] = $nestedimportid;
                    $sessiongroupids[] = $nestedimportpanopto->sessiongroupid;

                    $importresult = new stdClass;
                    $importresult->importedcourseid = $nestedimportid;
                    $nestedimportresults[] = $importresult;
                }
            }

            // Only do this code if we have proper access to the target Panopto course folder.
            $this->sessionmanager->set_copied_external_course_access_for_roles(
                $provisioninginfo->fullname,
                $provisioninginfo->externalcourseid,
                $sessiongroupids
            );

            $importresult = new stdClass;
            $importresult->importedcourseid = $newimportid;
            $importresults[] = $importresult;
            $importresults = array_merge($importresults, $nestedimportresults);

        } else {
            $importresult = new stdClass;
            $importresult->importedcourseid = $newimportid;
            $importresult->errormessage = get_string('import_access_error', 'block_panopto', $importresult);
            $importresults[] = $importresult;
        }

        return $importresults;
    }

    /**
     * Attempts to get a folder by it's external id
     *
     */
    public function get_folders_by_external_id() {
        global $USER;
        $ret = false;

        if (isset($this->sessiongroupid)) {
            // Update permissions so user can see everything they should.
            $this->sync_external_user($USER->id);

            $this->ensure_session_manager();
            $provisioninginfo = $this->get_provisioning_info();
            $ret = $this->sessionmanager->get_folders_by_external_id($provisioninginfo->externalcourseid);
        }

        return $ret;
    }

    /**
     * Attempts to get a folder by it's public Guid
     *
     */
    public function get_folders_by_id() {
        global $USER;

        $this->sync_external_user($USER->id);

        $ret = $this->get_folders_by_id_no_sync();

        return $ret;
    }

    /**
     * Attempts to get a folder by it's public Guid without syncing it to Panopto.
     *
     */
    public function get_folders_by_id_no_sync() {

        if (isset($this->sessiongroupid)) {
            $this->ensure_session_manager();

            $ret = $this->sessionmanager->get_folders_by_id($this->sessiongroupid);

        } else {
            // In this case the course is not mapped and the folder does not exist.
            $ret = null;
        }

        return $ret;
    }

    /**
     * Attempts to get all folders the user has access to.
     *
     */
    public function get_folders_list() {
        global $USER;
        $ret = false;

        // Update permissions so user can see everything they should.
        $this->sync_external_user($USER->id);

        $this->ensure_session_manager();

        $ret = $this->sessionmanager->get_folders_list();

        return $ret;
    }

    /**
     * Attempts to get all folders the user has creator access to.
     *
     */
    public function get_creator_folders_list() {
        global $USER;
        $ret = false;

        // Update permissions so user can see everything they should.
        $this->sync_external_user($USER->id);

        $this->ensure_session_manager();

        // We are checking if we can get extended folder or not here based on Panopto version.
        // Extended folder will have information if folder is assignment or not.
        $this->ensure_auth_manager();
        $activepanoptoserverversion = $this->authmanager->get_server_version();
        $canwegetextendedfolder = version_compare(
            $activepanoptoserverversion,
            self::$apiassignmentfolderspanoptoversion,
            '>='
        );

        $ret = $canwegetextendedfolder
            ? $this->sessionmanager->get_extended_creator_folders_list()
            : $this->sessionmanager->get_creator_folders_list();

        return $ret;
    }

    /**
     * Sync a user with all of the courses he is enrolled in on the current Panopto server
     *
     * @param int $userid external user id
     */
    public function sync_external_user($userid) {
        global $DB, $CFG;

        self::print_log_verbose(get_string('attempt_sync_user', 'block_panopto', $userid));
        self::print_log_verbose(get_string('attempt_sync_user_server', 'block_panopto', $this->servername));

        $userinfo = $DB->get_record('user', ['id' => $userid]);
        $istempuser = $this->is_temp_user(isset($userinfo) ? $userinfo->username : "", isset($userinfo) ? $userinfo->email : "");

        // Only sync if we find an existing user with the given id, and if not temp user.
        if (isset($userinfo) && ($userinfo !== false) && !$istempuser) {
            $instancename = get_config('block_panopto', 'instance_name');

            $currentcourses = enrol_get_users_courses($userid, true);

            // Go through each course.
            $groupstosync = [];
            foreach ($currentcourses as $course) {
                $coursecontext = context_course::instance($course->id);

                $coursepanopto = new \panopto_data($course->id);

                // Check to see if we are already going to provision a specific Panopto server,
                // if we are just add the groups to the already made array. If not add the server to the list of servers.
                if (isset($coursepanopto->servername) && !empty($coursepanopto->servername) &&
                    $coursepanopto->servername === $this->servername &&
                    isset($coursepanopto->applicationkey) && !empty($coursepanopto->applicationkey) &&
                    isset($coursepanopto->sessiongroupid) && !empty($coursepanopto->sessiongroupid)) {

                    $role = self::get_role_from_context($coursecontext, $userid);

                    // Build a list of ExternalGroupIds using a specific format.
                    // E.g. moodle31:courseId_viewers/moodle31:courseId_creators.
                    $groupname = $coursepanopto->instancename . ':' . $course->id;
                    if (strpos($role, 'Viewer') !== false) {
                        $groupstosync[] = $groupname . "_viewers";
                    }

                    if (strpos($role, 'Creator') !== false) {
                        $groupstosync[] = $groupname . "_creators";
                    }

                    if (strpos($role, 'Publisher') !== false) {
                        $groupstosync[] = $groupname . "_publishers";
                    }
                }
            }

            self::print_log_verbose(get_string('groups_getting_synced', 'block_panopto', implode(', ', $groupstosync)));

            // Only try to sync the users if he Panopto server is up.
            if (self::is_server_alive('https://' . $this->servername . '/Panopto')) {

                $this->ensure_user_manager($userinfo->username);

                $this->usermanager->sync_external_user(
                    $userinfo->firstname,
                    $userinfo->lastname,
                    $userinfo->email,
                    $groupstosync,
                    $userinfo->username
                );
            } else {
                self::print_log(get_string('panopto_server_error', 'block_panopto', $this->servername));
            }
        }

        return;
    }

    /**
     * Create the provisioning information needed to create permissions on Panopto for the new course
     *
     * @param int $courseid the id of the course being updated
     * @param int $newimportid courseid that the target course imports from
     */
    public static function add_new_course_import($courseid, $newimportid) {
        global $DB;
        $rowarray = ['target_moodle_id' => $courseid, 'import_moodle_id' => $newimportid];

        $currentrow = $DB->get_record('block_panopto_importmap', $rowarray);
        if (!$currentrow) {
            $row = (object) $rowarray;
            return $DB->insert_record('block_panopto_importmap', $row);
        }

        return;
    }

    /**
     * Get the courseid's of the courses being imported to this course
     *
     * @param int $courseid
     */
    public static function get_import_list($courseid) {
        global $DB;

        $courseimports = $DB->get_records(
            'block_panopto_importmap',
            ['target_moodle_id' => $courseid],
            null,
            'id,import_moodle_id'
        );

        $retarray = [];
        if (isset($courseimports) && !empty($courseimports)) {
            foreach ($courseimports as $courseimport) {
                $retarray[] = $courseimport->import_moodle_id;
            }
        }

        return $retarray;
    }

    /**
     * Get the courseid's of the courses importing the given course
     *
     * @param int $courseid
     */
    public static function get_import_target_list($courseid) {
        global $DB;

        $courseimports = $DB->get_records(
            'block_panopto_importmap',
            ['import_moodle_id' => $courseid],
            null,
            'id,target_moodle_id'
        );

        $retarray = [];
        if (isset($courseimports) && !empty($courseimports)) {
            foreach ($courseimports as $courseimport) {
                $retarray[] = $courseimport->target_moodle_id;
            }
        }

        return $retarray;
    }

    /**
     * Get ongoing Panopto sessions for the currently mapped course.
     *
     * @param string $sessionshavespecificorder session ordering
     */
    public function get_session_list($sessionshavespecificorder) {
        $sessionlist = [];
        if ($this->servername && $this->applicationkey && $this->sessiongroupid) {
            $this->ensure_session_manager();
        }

        $sessionlist = $this->sessionmanager->get_session_list($this->sessiongroupid, $sessionshavespecificorder);

        return $sessionlist;
    }

    /**
     * Get a Panopto user by their user key
     *
     * @param string $userkey the username/key for the user being searched.
     */
    public function get_user_by_key($userkey) {
        global $USER;

        if (!empty($this->servername) && !empty($this->applicationkey)) {
            $this->ensure_user_manager($USER->username);
        }

        $panoptouser = $this->usermanager->get_user_by_key($userkey);

        return $panoptouser;
    }

    /**
     * Sends a request to Panopto to delete a user, requires the calling user to be an Admin in Panopto.
     *
     * @param array $userids the Guid user Ids for the users being deleted.
     */
    public function delete_users_from_panopto($userids) {
        global $USER;

        if (!empty($this->servername) && !empty($this->applicationkey)) {
            $this->ensure_user_manager($USER->username);
        }

        $result = $this->usermanager->delete_users($userids);

        return $result;
    }

    /**
     * Sends a request to Panopto to update the information of a user, assumes the user manager has already been ensured
     *
     * @param string $userid the id of the target user;
     * @param string $firstname the new first name of the user
     * @param string $lastname the new last name of the user
     * @param string $email the new user email
     * @param boolean $sendemailnotifications Tells panopto if the user wants email notifications sent to them.
     */
    public function update_contact_info($userid, $firstname, $lastname, $email, $sendemailnotifications) {
        global $USER;

        if (!empty($this->servername) && !empty($this->applicationkey)) {
            $this->ensure_user_manager($USER->username);
        }

        $result = $this->usermanager->update_contact_info(
            $userid,
            $firstname,
            $lastname,
            $email,
            $sendemailnotifications
        );

        return $result;
    }

    /**
     * Instance method caches Moodle instance name from DB (vs. block_panopto_lib version).
     *
     * @param string $moodleusername name of the Moodle user
     */
    public function panopto_decorate_username($moodleusername) {
        return ($this->instancename . "\\" . $moodleusername);
    }

    /**
     * Lets us know if we have a value inside the config for a Panopto server,
     * we don't want any of our events to fire on an unconfigured block.
     *
     */
    public static function is_main_block_configured() {

        $numservers = get_config('block_panopto', 'server_number');
        $numservers = isset($numservers) ? $numservers : 0;

        // Increment numservers by 1 to take into account starting at 0.
        ++$numservers;

        $isconfigured = false;
        if ($numservers > 0) {
            for ($serverwalker = 1; $serverwalker <= $numservers; ++$serverwalker) {
                $possibleserver = get_config('block_panopto', 'server_name' . $serverwalker);
                $possibleappkey = get_config('block_panopto', 'application_key' . $serverwalker);

                if (isset($possibleserver) && !empty($possibleserver) &&
                    isset($possibleappkey) && !empty($possibleappkey)) {
                    $isconfigured = true;
                    break;
                }
            }
        }

        return $isconfigured;
    }

    /**
     * Lets us know if we have a value inside the config for a Panopto block,
     * we don't want any of our events to fire on a disabled block.
     *
     */
    public static function is_block_disabled() {
        global $DB;

        $sql = "SELECT * " .
                "FROM {block} b " .
                "WHERE b.name = :name AND b.visible = 0";
        $isblockdisabled = $DB->get_record_sql($sql, ['name' => 'panopto']);

        return $isblockdisabled ? true : false;
    }

    /**
     * Lets us know is we are using at least the minumum required version for the Panopto block
     *
     */
    public static function has_minimum_version() {
        global $CFG;

        $hasminversion = true;
        $versionobject = new stdClass;
        $versionobject->requiredversion = self::$requiredversion;
        $versionobject->currentversion = $CFG->version;

        if ($CFG->version < self::$requiredversion) {
            $hasminversion = false;
            self::print_log(get_string('missing_moodle_required_version', 'block_panopto', $versionobject));
        }

        return $hasminversion;
    }

    /**
     * We need to retrieve the current course mapping in the constructor, so this must be static.
     *
     * @param int $sessiongroupid id of the Panopto folder we are trying to get the Moodle courses associated with.
     */
    public static function get_moodle_course_id($sessiongroupid) {
        global $DB;
        return $DB->get_records(
            'block_panopto_foldermap',
            ['panopto_id' => $sessiongroupid],
            null,
            'id,moodleid'
        );
    }

    /**
     * We need to retrieve the current course mapping in the constructor, so this must be static.
     *
     * @param int $moodlecourseid id of the current Moodle course
     */
    public static function get_panopto_course_id($moodlecourseid) {
        global $DB;
        return $DB->get_field('block_panopto_foldermap', 'panopto_id', ['moodleid' => $moodlecourseid]);
    }

    /**
     *  Retrieve the servername for the current course
     *
     * @param int $moodlecourseid id of the current Moodle course
     */
    public static function get_panopto_servername($moodlecourseid) {
        global $DB;
        return $DB->get_field('block_panopto_foldermap', 'panopto_server', ['moodleid' => $moodlecourseid]);
    }

    /**
     *  Checks for course role mappings with Panopto. If none exist then set to the defaults.
     *
     */
    public function check_course_role_mappings() {
        // If old role mappings exists, do not remap. Otherwise, set role mappings to defaults.
        $mappings = self::get_course_role_mappings($this->moodlecourseid);
        if (empty($mappings['creator']) && empty($mappings['publisher'])) {

            // These settings are returned as a comma seperated string of role Id's.
            $defaultpublishermapping = explode("," , get_config('block_panopto', 'publisher_role_mapping'));
            $defaultcreatormapping = explode("," , get_config('block_panopto', 'creator_role_mapping'));

            // Set the role mappings for the course to the defaults.
            self::set_course_role_mappings(
                $this->moodlecourseid,
                $defaultpublishermapping,
                $defaultcreatormapping
            );

            // Grant course users the proper Panopto permissions based on the default role mappings.
            // This will make the role mappings be recognized when provisioning.
            self::set_course_role_permissions(
                $this->moodlecourseid,
                $defaultpublishermapping,
                $defaultcreatormapping
            );
        }
    }

    /**
     * Get the current role mappings set for the current course from the db.
     *
     * @param int $moodlecourseid id of the current Moodle course
     */
    public static function get_course_role_mappings($moodlecourseid) {
        global $DB;

        $pubroles = [];
        $creatorroles = [];

         // Get creator roles as an array.
        $creatorrolesraw = $DB->get_records(
            'block_panopto_creatormap',
            ['moodle_id' => $moodlecourseid],
            'id,role_id'
        );

        if (isset($creatorrolesraw) && !empty($creatorrolesraw)) {
            foreach ($creatorrolesraw as $creatorrole) {
                $creatorroles[] = $creatorrole->role_id;
            }
        }

         // Get publisher roles as an array.
        $pubrolesraw = $DB->get_records(
            'block_panopto_publishermap',
            ['moodle_id' => $moodlecourseid],
            'id,role_id'
        );

        if (isset($pubrolesraw) && !empty($pubrolesraw)) {
            foreach ($pubrolesraw as $pubrole) {
                $pubroles[] = $pubrole->role_id;
            }
        }

        return ['publisher' => $pubroles, 'creator' => $creatorroles];
    }

    /**
     *  Set the Panopto ID in the db for the current course
     *  Called by Moodle block instance config save method, so must be static.
     *
     * @param int $moodlecourseid id of the current Moodle course.
     * @param int $sessiongroupid the id of the current session group.
     * @param int $servername name of the server the sessiongroup is located on.
     * @param int $appkey the appkey needed to access the Identity provider on the server.
     * @param int $externalcourseid id of the external course.
     */
    public static function set_course_foldermap($moodlecourseid, $sessiongroupid, $servername, $appkey, $externalcourseid) {
        global $DB;
        $row = (object) [
            'moodleid' => $moodlecourseid,
            'panopto_id' => $sessiongroupid,
            'panopto_server' => $servername,
            'panopto_app_key' => $appkey,
        ];

        $oldrecord = $DB->get_record('block_panopto_foldermap', ['moodleid' => $moodlecourseid]);

        if ($oldrecord) {
            $row->id = $oldrecord->id;
            return $DB->update_record('block_panopto_foldermap', $row);
        } else {
            return $DB->insert_record('block_panopto_foldermap', $row);
        }
    }

    /**
     * Set the Panopto ID in the db for the current course
     * Called by Moodle block instance config save method, so must be static.
     *
     * @param int $moodlecourseid id of the current Moodle course
     * @param int $sessiongroupid the id of the current session group
     */
    public static function set_panopto_course_id($moodlecourseid, $sessiongroupid) {
        global $DB;
        if ($DB->get_records('block_panopto_foldermap', ['moodleid' => $moodlecourseid])) {
            return $DB->set_field(
                'block_panopto_foldermap',
                'panopto_id',
                $sessiongroupid,
                ['moodleid' => $moodlecourseid]
            );
        } else {
            $row = (object) ['moodleid' => $moodlecourseid, 'panopto_id' => $sessiongroupid];
            return $DB->insert_record('block_panopto_foldermap', $row);
        }
    }

    /**
     * Set the Panopto server name in the db for the current course
     *
     * @param int $moodlecourseid id of the current Moodle course
     * @param string $panoptoservername the name of the Panopto server
     */
    public static function set_panopto_server_name($moodlecourseid, $panoptoservername) {
        global $DB;
        if ($DB->get_records('block_panopto_foldermap', ['moodleid' => $moodlecourseid])) {
            return $DB->set_field(
                'block_panopto_foldermap',
                'panopto_server',
                $panoptoservername,
                ['moodleid' => $moodlecourseid]
            );
        } else {
            $row = (object) ['moodleid' => $moodlecourseid, 'panopto_server' => $panoptoservername];
            return $DB->insert_record('block_panopto_foldermap', $row);
        }
    }

    /**
     * Set the Panopto app key associated with the current course on the db
     *
     * @param int $moodlecourseid id of the current Moodle course
     * @param string $panoptoappkey
     */
    public static function set_panopto_app_key($moodlecourseid, $panoptoappkey) {
        global $DB;
        if ($DB->get_records('block_panopto_foldermap', ['moodleid' => $moodlecourseid])) {
            return $DB->set_field(
                'block_panopto_foldermap',
                'panopto_app_key',
                $panoptoappkey,
                ['moodleid' => $moodlecourseid]
            );
        } else {
            $row = (object) ['moodleid' => $moodlecourseid, 'panopto_app_key' => $panoptoappkey];
            return $DB->insert_record('block_panopto_foldermap', $row);
        }
    }

    /**
     * Set the selected Panopto role mappings for the current course on the db
     *
     * @param int $moodlecourseid id of the current Moodle course
     * @param array $publisherroles a list of publisher roles
     * @param array $creatorroles a list of creator roles
     */
    public static function set_course_role_mappings($moodlecourseid, $publisherroles, $creatorroles) {
        global $DB;

        // Delete all old records to prevent non-existant mapping staying when they shouldn't.
        $DB->delete_records('block_panopto_publishermap', ['moodle_id' => $moodlecourseid]);

        foreach ($publisherroles as $pubrole) {
            if (!empty($pubrole)) {
                $row = (object) ['moodle_id' => $moodlecourseid, 'role_id' => $pubrole];
                $DB->insert_record('block_panopto_publishermap', $row);
            }
        }

        // Delete all old records to prevent non-existant mapping staying when they shouldn't.
        $DB->delete_records('block_panopto_creatormap', ['moodle_id' => $moodlecourseid]);

        foreach ($creatorroles as $creatorrole) {
            if (!empty($creatorrole)) {
                $row = (object) ['moodle_id' => $moodlecourseid, 'role_id' => $creatorrole];
                $DB->insert_record('block_panopto_creatormap', $row);
            }
        }
    }

    /**
     * Delete the Panopto foldermap row, called when a course is deleted.
     * This function is unused but kept in case we decide to reintroduce the cleaning of table rows.
     *
     * @param int $moodlecourseid id of the target Moodle course
     * @param bool $movetoinactivetable should we insert to inactive ClientData or not
     */
    public static function delete_panopto_relation($moodlecourseid, $movetoinactivetable) {
        global $DB;
        $deletedrecords = [];
        $existingrecords = $DB->get_records('block_panopto_foldermap', ['moodleid' => $moodlecourseid]);
        if ($existingrecords) {
            if ($movetoinactivetable) {
                $DB->insert_records('block_panopto_old_foldermap', $existingrecords);
            }

            $deletedrecords['foldermap'] = $DB->delete_records(
                'block_panopto_foldermap',
                ['moodleid' => $moodlecourseid]
            );
        }

        // Clean up any creator role mappings.
        if ($DB->get_records('block_panopto_creatormap', ['moodle_id' => $moodlecourseid])) {
            $DB->delete_records(
                'block_panopto_creatormap',
                ['moodle_id' => $moodlecourseid]
            );
        }

        // Clean up any publisher role mappings.
        if ($DB->get_records('block_panopto_publishermap', ['moodle_id' => $moodlecourseid])) {
            $DB->delete_records(
                'block_panopto_publishermap',
                ['moodle_id' => $moodlecourseid]
            );
        }

        if ($DB->get_records('block_panopto_importmap', ['target_moodle_id' => $moodlecourseid])) {
            $deletedrecords['imports'] = $DB->delete_records(
                'block_panopto_importmap',
                ['target_moodle_id' => $moodlecourseid]
            );
        }

        if ($DB->get_records('block_panopto_importmap', ['import_moodle_id' => $moodlecourseid])) {
            $deletedrecords['exports'] = $DB->delete_records(
                'block_panopto_importmap',
                ['import_moodle_id' => $moodlecourseid]
            );
        }

        return $deletedrecords;
    }

    /**
     * Check if has valid panopto params
     *
     * @return bool
     */
    public function has_valid_panopto() {
        return isset($this->sessiongroupid) && !empty($this->sessiongroupid) &&
               isset($this->servername) && !empty($this->servername) &&
               isset($this->applicationkey) && !empty($this->applicationkey);
    }

    /**
     * Get list of available folders from db based on user's access level on course.
     * Only get unmapped folders, and the current course folder
     *
     * @return array
     */
    public function get_course_options() {
        global $DB;

        $panoptofolders = $this->get_creator_folders_list();
        $options = [];
        $containsmappedfolder = false;

        if (!empty($panoptofolders)) {

            // We are checking if we can get extended folder or not here based on Panopto version.
            // Extended folder will have information if folder is assignment or not.
            $this->ensure_auth_manager();
            $canwegetextendedfolder = version_compare(
                $this->authmanager->get_server_version(),
                self::$apiassignmentfolderspanoptoversion,
                '>='
            );

            foreach ($panoptofolders as $folderinfo) {

                // Filter folders based on the following criteria.
                // 1/ Only add a folder to the course options if it is not already mapped to a course on moodle.
                // 2/ Unless its the current course.
                // 3/ If it is not assignment folder, but only after Panopto version 13.14.0.00000.

                $isassignmentfolder = $canwegetextendedfolder
                    ? $folderinfo->IsAssignmentFolder
                    : false;

                if ((!$DB->get_records('block_panopto_foldermap', ['panopto_id' => $folderinfo->Id])
                    || ($this->sessiongroupid === $folderinfo->Id))
                    && !$isassignmentfolder) {

                    if ($this->sessiongroupid === $folderinfo->Id) {
                        $containsmappedfolder = true;
                    }

                    $options[$folderinfo->Id] = $folderinfo->Name;
                }
            }
        }

        if (!$containsmappedfolder && !empty($this->sessiongroupid)) {
            $currentfolder = $this->get_folders_by_id_no_sync();
            $options[$this->sessiongroupid] = $currentfolder->Name;
        }

        if (empty($options)) {
            if (isset($panoptofolders) && empty($this->sessiongroupid)) {
                $options = ['Error' => '-- No Courses Available --'];
            } else if (!isset($panoptofolders) && empty($this->sessiongroupid)) {
                $options = ['Error' => '-- Unable to retrieve course list --'];
            }
        }

        return ['courses' => $options, 'selected' => $this->sessiongroupid];
    }

    /**
     * Build a list of capabilities to be assigned for a specified roles given a context.
     *
     * @param array $roles an array of roles to be given the capability
     * @param string $capability The capability being given to the roles
     * @return array
     */
    public static function build_capability_to_roles($roles, $capability) {
        $assigncaps = [];
        foreach ($roles as $role) {
            if (isset($role) && trim($role) !== '') {
                $assigncaps[$role] = $capability;
            }
        }
        return $assigncaps;
    }

    /**
     * Gives selected capabilities to specified roles given a context, verify that there are capabilities
     * to be added or remove insteaad of rebuilding every page load.
     *
     * @param int $context the context of the roles being given the capability
     * @param array $roles an array of roles to be given the capability
     * @param string $capability The capability being given to the roles
     * @return bool
     */
    public static function build_and_assign_context_capability_to_roles($context, $roles, $capability) {
        global $DB;

        $processed = false;
        $assigned = self::build_capability_to_roles($roles, $capability);
        $existing = [];

        // Extract the existing capabilities that have been assigned for context, role and capability.
        foreach ($roles as $roleid) {
            // Only query the DB if $roleid is not null.
            if ($roleid && $DB->record_exists('role_capabilities',
                ['contextid' => $context->id, 'roleid' => $roleid, 'capability' => $capability])) {
                $existing[$roleid] = $capability;
            }
        }

        // Remove existing capabilities that are no longer needed. This needs to be assoc to take into account the keys.
        $assignnew = array_diff_assoc($existing, $assigned);
        if (!empty($assignnew)) {
            foreach ($assignnew as $roleid => $cap) {
                unassign_capability($capability, $roleid, $context);
                $processed = true;
            }
        }

        // Add new capabilities that don't exist yet.
        $existingnew = array_diff_assoc($assigned, $existing);

        if (!empty($existingnew)) {
            foreach ($existingnew as $roleid => $cap) {
                if (isset($roleid) && trim($roleid) !== '') {
                    assign_capability(
                        $capability,
                        CAP_ALLOW,
                        $roleid,
                        $context,
                        $overwrite = false
                    );
                }
                $processed = true;
            }
        }

        return $processed;
    }

    /**
     * Gives selected capabilities to specified roles.
     *
     * @param int $courseid the id of the course being focused for this operation
     * @param array $publisherroles an array of roles to be made publishers
     * @param array $creatorroles an array of roles to be made creators for the course
     */
    public static function set_course_role_permissions($courseid, $publisherroles, $creatorroles) {
        $coursecontext = context_course::instance($courseid);

        // Build and process new/old changes to capabilities to be applied to roles and capabilities.
        $capability = 'block/panopto:provision_aspublisher';
        $publisherprocessed = self::build_and_assign_context_capability_to_roles($coursecontext, $publisherroles, $capability);
        $capability = 'block/panopto:provision_asteacher';
        $creatorprocessed = self::build_and_assign_context_capability_to_roles($coursecontext, $creatorroles, $capability);

        // If any changes where made, context needs to be flagged as dirty to be re-cached.
        if ($publisherprocessed || $creatorprocessed) {
            $coursecontext->mark_dirty();
        }

        self::set_course_role_mappings($courseid, $publisherroles, $creatorroles);
    }

    /**
     * If a role was unset from a capability we need to reflect that change on Moodle.
     *
     * @param int $courseid the id of the course being focused for this operation
     * @param array $oldpublisherroles an array of roles to be made publishers
     * @param array $oldcreatorroles an array of roles to be made creators for the course
     */
    public static function unset_course_role_permissions($courseid, $oldpublisherroles, $oldcreatorroles) {
        $coursecontext = context_course::instance($courseid);

        foreach ($oldpublisherroles as $publisherrole) {
            unassign_capability('block/panopto:provision_aspublisher', $publisherrole, $coursecontext);
        }

        foreach ($oldcreatorroles as $creatorrole) {
            unassign_capability('block/panopto:provision_asteacher', $creatorrole, $coursecontext);
        }

        if (!empty($oldpublisherroles) || !empty($oldcreatorroles)) {
            $coursecontext->mark_dirty();
        }
    }

    /**
     * Check to determine if server is live or not
     *
     * @param object $url server url
     * @return bool
     */
    public static function is_server_alive($url = null) {
        if ($url == null) {
            return false;
        }

        // Only proceed with the cURL check if this toggle is true. This code is dependent on platform/OS specific calls.
        if (!get_config('block_panopto', 'check_server_status')) {
            return true;
        }

        $timenow = time();
        $nextservercheck = (int)get_config('block_panopto', 'next_server_check');

        if (is_null($nextservercheck) || $nextservercheck < $timenow) {
            $curl = new \curl();
            $options = [
                'CURLOPT_TIMEOUT' => get_config('block_panopto', 'panopto_socket_timeout'),
                'CURLOPT_CONNECTTIMEOUT' => get_config('block_panopto', 'panopto_connection_timeout'),
            ];
            $curl->get($url, null, $options);
            $httpcode = !empty($curl->get_info()['http_code']) ? $curl->get_info()['http_code'] : 'Not found.';

            $result = !$curl->get_errno();

            $checkserverinterval = get_config('block_panopto', 'check_server_interval');
            $nextservercheck = $timenow + $checkserverinterval;
            set_config('check_server_result', $result, 'block_panopto');
            set_config('next_server_check', $nextservercheck, 'block_panopto');

            if (!$result) {
                self::print_log('ERROR: failed to check Panopto server health. URL: ' . $url . ' HTTP code: ' . $httpcode);
            }

             return $result;
        }

        return get_config('block_panopto', 'check_server_result');
    }

    /**
     * Check to determine if folder is inheriting permissions
     *
     * @param string $folderid folder id
     * @return bool
     */
    public function is_folder_inheriting_permissions($folderid) {
        $this->ensure_auth_manager();

        // This call will log the user into Panopto using the SOAP API and it will also store the Panopto cookies.
        $this->authmanager->log_on_with_external_provider();

        // Only do this code if we have proper access to the target Panopto course folder.
        $location = 'https://'. $this->servername . '/Panopto/api/v1/folders/'. $folderid . '/settings/access';

        $curl = new \curl();
        $aspxauthcookie = "";
        foreach ($this->authmanager->panoptoauthcookies as $key => $value) {
            if (strpos(strtolower($key), 'aspxauth') !== false) {
                $aspxauthcookie = $value;
                break;
            }
        }

        if (empty($aspxauthcookie)) {
            self::print_log(get_string('copy_api_error_auth', 'block_panopto', $this->servername));
            return false;
        }

        $options = [
            'CURLOPT_VERBOSE' => false,
            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_HEADER' => false,
            'CURLOPT_HTTPHEADER' => ['Content-Type: application/json',
                                          'Cookie: .ASPXAUTH='.$aspxauthcookie],
        ];

        $sockettimeout = get_config('block_panopto', 'panopto_socket_timeout');
        $connectiontimeout = get_config('block_panopto', 'panopto_connection_timeout');

        if (!!$sockettimeout) {
            $options['CURLOPT_TIMEOUT'] = $sockettimeout;
        }

        if (!!$connectiontimeout) {
            $options['CURLOPT_CONNECTTIMEOUT'] = $connectiontimeout;
        }

        $proxyhost = get_config('block_panopto', 'wsdl_proxy_host');
        $proxyport = get_config('block_panopto', 'wsdl_proxy_port');

        if (!empty($proxyhost)) {
            $options['CURLOPT_PROXY'] = $proxyhost;
        }

        if (!empty($proxyport)) {
            $options['CURLOPT_PROXYPORT'] = $proxyport;
        }

        $response = json_decode($curl->get($location, null, $options));

        if (!empty($response) && isset($response->IsInherited)) {
            return $response->IsInherited;
        } else {
            self::print_log(get_string('copy_api_error_response', 'block_panopto', $response));
            return false;
        }
    }

    /**
     * Get cm for course
     *
     * @param string $courseid course id
     */
    public function get_cm_for_course($courseid) {
        global $DB;

        $sql = "SELECT cm.id " .
                "FROM {course_modules} cm " .
                "JOIN {modules} md ON (md.id = cm.module) " .
                "JOIN {lti} m ON (m.id = cm.instance) " .
                "WHERE md.name = :name AND cm.course = :course";
        return $DB->get_records_sql($sql,
            ['name' => 'lti', 'course' => $courseid]);
    }

    /**
     * Check if username format is from a temp user.
     *
     * @param string $username User name
     * @param string $email User email
     */
    public function is_temp_user($username, $email) {
        // Match the following pattern for username "adfake@panopto.com.1690123065".
        // Temp users are expected to have a GUID instead of a valid email address.
        $usernamepattern = '/^([a-zA-Z0-9._%+-]+)@[a-zA-Z0-9.-]+\.\d+$/';
        $isinvalidemail = filter_var($email, FILTER_VALIDATE_EMAIL) === false;
        $matchesusernamepattern = preg_match($usernamepattern, $username) === 1;

        return $isinvalidemail && $matchesusernamepattern;
    }

    /**
     * Print log
     *
     * @param string $logmessage log message
     */
    public static function print_log($logmessage) {
        global $CFG;

        $logmessage = substr($logmessage, 0, self::$maxloglength);

        if (CLI_SCRIPT) {
            mtrace($logmessage);
        } else {
            if (get_config('block_panopto', 'print_log_to_file')) {
                $currenttime = time();
                file_put_contents(
                    $CFG->dirroot . '/PanoptoLogs.txt', date("Y-m-d-h:i:sA", $currenttime) . ": " . $logmessage . "\n",
                    FILE_APPEND
                );
            } else {
                debugging($logmessage);

                // These flush's are needed for longer processes like the Moodle upgrade process and import process.

                // If the oblength are false then there is no active outbut buffer.
                // If we call ob_flush without an output buffer (e.g. from the cli) it will spit out an error.
                // This doesn't break the execution of the script, but it's ugly and a lot of bloat.
                $obstatus = ob_get_status();
                if (isset($obstatus) && !empty($obstatus)) {
                    ob_flush();
                }
                flush();
            }
        }
    }

    /**
     * Print log level verbose
     *
     * @param string $logmessage log message
     */
    public static function print_log_verbose($logmessage) {
        if (get_config('block_panopto', 'print_verbose_logs')) {
            self::print_log($logmessage);
        }
    }

    /**
     * Checks if the course has any enrolled users.
     *
     * @param panopto_data $courseid The copied course for which we need to check enrollment.
     * @return bool True if there are enrolled users, false otherwise.
     */
    public function has_enrolled_users($courseid) {
        $coursecontext = context_course::instance($courseid);
        $enrolledusers = get_enrolled_users($coursecontext);
        return !empty($enrolledusers);
    }

    /**
     * Enrolls a specific user as a teacher in a given course.
     *
     * This method retrieves the manual enrolment instance for the specified course
     * and assigns the user the "editing teacher" role if it exists.
     *
     * @param int $userid The ID of the user to enroll.
     * @param int $newcourseid The ID of the course where the user will be enrolled.
     */
    public function enroll_user_as_teacher($userid, $newcourseid) {
        global $DB, $CFG;
        require_once($CFG->libdir . '/accesslib.php');
        require_once($CFG->libdir . '/enrollib.php');

        // Retrieve the manual enrolment instance for the new course.
        $instances = enrol_get_instances($newcourseid, true);
        $manualinstance = null;
        foreach ($instances as $instance) {
            if ($instance->enrol === 'manual') {
                $manualinstance = $instance;
                break;
            }
        }

        if (!$manualinstance) {
            self::print_log('ERROR: No manual enrolment method in the new course.');
            return;
        }

        // Retrieve the editing teacher role ID.
        $teacherroleid = $DB->get_field('role', 'id', ['shortname' => 'editingteacher']);
        if (!$teacherroleid) {
            self::print_log('ERROR: Teacher role not found during enroll_user_as_teacher');
            return;
        }

        // Enroll the specified user as a teacher in the new course.
        $enrolplugin = enrol_get_plugin('manual');
        $enrolplugin->enrol_user($manualinstance, $userid, $teacherroleid);
    }
}
