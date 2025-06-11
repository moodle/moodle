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
 * Event observers of local_o365 plugin.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365;

use auth_oidc\event\user_authed;
use auth_oidc\event\user_connected;
use auth_oidc\event\user_disconnected;
use auth_oidc\event\user_loggedin;
use auth_oidc\jwt;
use backup;
use context_system;
use core\event\capability_assigned;
use core\event\capability_unassigned;
use core\event\config_log_created;
use core\event\course_created;
use core\event\course_deleted;
use core\event\course_restored;
use core\event\course_updated;
use core\event\enrol_instance_updated;
use core\event\role_assigned;
use core\event\role_deleted;
use core\event\role_unassigned;
use core\event\user_created;
use core\event\user_deleted;
use core\event\user_enrolment_updated;
use core\event\cohort_deleted;
use core\task\manager;
use core_user;
use local_o365\feature\coursesync\main;
use local_o365\oauth2\clientdata;
use local_o365\oauth2\token;
use local_o365\obj\o365user;
use local_o365\rest\unified;
use local_o365\task\groupmembershipsync;
use local_o365\task\processcourserequestapproval;
use moodle_exception;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/lib/filelib.php');
require_once($CFG->dirroot . '/auth/oidc/lib.php');
require_once($CFG->dirroot . '/local/o365/lib.php');

/**
 * Handles events.
 */
class observers {
    /**
     * Handle an authentication-only OIDC event.
     *
     * Does the following:
     *  - Set the system API user, so store the received token appropriately.
     *
     * @param user_authed $event The triggered event.
     * @return bool Success/Failure.
     */
    public static function handle_oidc_user_authed(user_authed $event): bool {
        require_login();
        require_capability('moodle/site:config', context_system::instance());

        $eventdata = $event->get_data();

        $action = (!empty($eventdata['other']['statedata']['action'])) ? $eventdata['other']['statedata']['action'] : null;

        switch ($action) {
            case 'adminconsent':
                // Get tenant.
                if (isset($eventdata['other']['tokenparams']['id_token'])) {
                    $idtoken = $eventdata['other']['tokenparams']['id_token'];
                    $idtoken = jwt::instance_from_encoded($idtoken);
                    if (!empty($idtoken)) {
                        $tenant = utils::get_tenant_from_idtoken($idtoken);
                        if (!empty($tenant)) {
                            $existingentratenantid = get_config('local_o365', 'entratenantid');
                            if ($existingentratenantid != $tenant) {
                                add_to_config_log('entratenantid', $existingentratenantid, $tenant, 'local_o365');
                            }
                            set_config('entratenantid', $tenant, 'local_o365');
                        }
                    }
                }

                redirect(new moodle_url('/admin/settings.php?section=local_o365'));
                break;

            case 'addtenant':
                $clientdata = clientdata::instance_from_oidc();
                $httpclient = new httpclient();
                switch (get_config('auth_oidc', 'idptype')) {
                    case AUTH_OIDC_IDP_TYPE_MICROSOFT_IDENTITY_PLATFORM:
                        $token = $eventdata['other']['tokenparams']['access_token'];
                        $expiry = time() + $eventdata['other']['tokenparams']['expires_in'];
                        $rtoken = '';
                        $scope = '';
                        $res = '';
                        $token = new token($token, $expiry, $rtoken, $scope, $res, null, $clientdata, $httpclient);
                        break;

                    default:
                        $token = $eventdata['other']['tokenparams']['access_token'];
                        $expiry = $eventdata['other']['tokenparams']['expires_on'];
                        $rtoken = $eventdata['other']['tokenparams']['refresh_token'];
                        $scope = $eventdata['other']['tokenparams']['scope'];
                        $res = $eventdata['other']['tokenparams']['resource'];
                        $token = new token($token, $expiry, $rtoken, $scope, $res, null, $clientdata, $httpclient);
                        $tokenresource = unified::get_tokenresource();
                        $token = token::jump_tokenresource($token, $tokenresource, $clientdata, $httpclient);
                        break;
                }

                $apiclient = new unified($token, $httpclient);
                $domainsfetched = false;
                $domainnames = [];
                try {
                    $domainnames = $apiclient->get_all_domain_names_in_tenant();
                    if ($domainnames) {
                        $domainsfetched = true;

                    }
                } catch (moodle_exception $e) {
                    // Do nothing.
                    $domainsfetched = false;
                }

                if (!$domainsfetched) {
                    $domainnames[] = $apiclient->get_default_domain_name_in_tenant();
                }

                $idtoken = jwt::instance_from_encoded($token->get_token());
                $tenantid = utils::get_tenant_from_idtoken($idtoken);
                utils::enableadditionaltenant($tenantid, $domainnames);

                redirect(new moodle_url('/local/o365/acp.php', ['mode' => 'tenants']));
                break;

            default:
                return true;
        }

        return false;
    }

    /**
     * Handle an existing Moodle user connecting to OpenID Connect.
     *
     * @param user_connected $event The triggered event.
     * @return bool Success/Failure.
     */
    public static function handle_oidc_user_connected(user_connected $event): bool {
        global $DB;

        if (utils::is_connected() !== true) {
            return false;
        }

        // Get additional tokens for the user.
        $eventdata = $event->get_data();
        if (!empty($eventdata['userid'])) {
            try {
                $userid = $eventdata['userid'];
                // Create local_o365_objects record.
                if (!empty($eventdata['other']['oidcuniqid'])) {
                    $userobject = $DB->get_record('local_o365_objects', ['type' => 'user', 'moodleid' => $userid]);
                    $userrecord = core_user::get_user($userid);
                    $isguestuser = false;
                    if (stripos($userrecord->username, '_ext_') !== false) {
                        $isguestuser = true;
                    }
                    if (empty($userobject)) {
                        try {
                            $apiclient = utils::get_api();
                            $userdata = $apiclient->get_user($eventdata['other']['oidcuniqid'], $isguestuser);
                            if (is_null($userdata)) {
                                utils::debug('Failed to get user data using Graph API.', __METHOD__);
                                return true;
                            }
                        } catch (moodle_exception $e) {
                            utils::debug('Exception: '.$e->getMessage(), __METHOD__, $e);
                            return true;
                        }

                        $tenant = utils::get_tenant_for_user($eventdata['userid']);
                        $metadata = '';
                        if (!empty($tenant)) {
                            // Additional tenant - get ODB url.
                            $odburl = utils::get_odburl_for_user($eventdata['userid']);
                            if (!empty($odburl)) {
                                $metadata = json_encode(['odburl' => $odburl]);
                            }
                        }

                        // Create userobject if it does not exist.
                        $now = time();
                        $userobjectdata = (object)[
                            'type' => 'user',
                            'subtype' => '',
                            'objectid' => $userdata['objectId'],
                            'o365name' => $userdata['userPrincipalName'],
                            'moodleid' => $userid,
                            'tenant' => $tenant,
                            'metadata' => $metadata,
                            'timecreated' => $now,
                            'timemodified' => $now,
                        ];
                        $userobjectdata->id = $DB->insert_record('local_o365_objects', $userobjectdata);

                        // Enrol user to all courses he was enrolled prior to connecting.
                        // Do nothing if sync direction is Teams to Moodle.
                        $courseusersyncdirection = get_config('local_o365', 'courseusersyncdirection');
                        if ($courseusersyncdirection != COURSE_USER_SYNC_DIRECTION_TEAMS_TO_MOODLE) {
                            if ($userobjectdata->id && \local_o365\feature\coursesync\utils::is_enabled() === true) {
                                $courses = enrol_get_users_courses($userid, true);

                                foreach ($courses as $courseid => $course) {
                                    if (\local_o365\feature\coursesync\utils::is_course_sync_enabled($courseid) == true) {
                                        \local_o365\feature\coursesync\utils::sync_user_role_in_course_group($userid, $courseid,
                                                $userobjectdata->id);
                                    }
                                }
                            }
                        }
                    }
                } else {
                    utils::debug('no oidcuniqid received', __METHOD__, $eventdata);
                }

                return true;
            } catch (moodle_exception $e) {
                utils::debug($e->getMessage(), __METHOD__, $e);
                return false;
            }
        }
        return false;
    }

    /**
     * Handle a user being created.
     *
     * Does the following:
     *  - Check if user is using OpenID Connect auth plugin.
     *  - If so, gets additional information from Microsoft Entra ID and updates the user.
     *
     * @param user_created $event The triggered event.
     * @return bool Success/Failure.
     */
    public static function handle_user_created(user_created $event): bool {
        global $DB;

        if (utils::is_connected() !== true) {
            return false;
        }

        $eventdata = $event->get_data();

        if (empty($eventdata['objectid'])) {
            return false;
        }
        $createduserid = $eventdata['objectid'];

        $user = $DB->get_record('user', ['id' => $createduserid]);
        if (!empty($user) && isset($user->auth) && $user->auth === 'oidc') {
            static::get_additional_user_info($createduserid);
        }

        return true;
    }

    /**
     * Handle an existing Moodle user disconnecting from OpenID Connect.
     *
     * @param user_disconnected $event The triggered event.
     * @return bool Success/Failure.
     */
    public static function handle_oidc_user_disconnected(user_disconnected $event): bool {
        global $DB;

        $eventdata = $event->get_data();
        if (!empty($eventdata['userid'])) {
            $DB->delete_records('local_o365_token', ['user_id' => $eventdata['userid']]);
            $DB->delete_records('local_o365_objects', ['type' => 'user', 'moodleid' => $eventdata['userid']]);
            $DB->delete_records('local_o365_connections', ['muserid' => $eventdata['userid']]);
            $DB->delete_records('local_o365_appassign', ['muserid' => $eventdata['userid']]);
        }

        return true;
    }

    /**
     * Handle user logins.
     *
     * Does the following:
     *  - If the user uses auth_oidc, uses the received auth code to get tokens for the other resources we use.
     *  - If the user is connected to Microsoft 365, sync user profiles.
     *
     * @param user_loggedin $event The triggered event.
     * @return bool Success/Failure.
     */
    public static function handle_oidc_user_loggedin(user_loggedin $event): bool {
        if (utils::is_connected() !== true) {
            return false;
        }

        // Get additional tokens for the user.
        $eventdata = $event->get_data();
        if (!empty($eventdata['other']['username']) && !empty($eventdata['userid'])) {
            static::get_additional_user_info($eventdata['userid']);
        }

        return true;
    }

    /**
     * Get additional information about a user from Microsoft Entra ID.
     *
     * @param int $userid The ID of the user we want more information about.
     * @return bool Success/Failure.
     */
    public static function get_additional_user_info(int $userid): bool {
        global $DB;

        try {
            // Graph API must be configured for us to fetch data.
            if (unified::is_configured() !== true) {
                return true;
            }

            $o365user = o365user::instance_from_muserid($userid);
            if (empty($o365user)) {
                // No OIDC token for this user and resource - maybe not a Microsoft Entra ID user.
                return false;
            }

            if (!$existinguserdata = core_user::get_user($userid)) {
                // Moodle user doesn't exist, nothing to do.
                return false;
            }

            $userobject = $DB->get_record('local_o365_objects', ['type' => 'user', 'moodleid' => $userid]);

            if (empty($userobject)) {
                // Create o365_object record if it does not exist.
                $tenant = utils::get_tenant_for_user($userid);
                $metadata = '';
                if (!empty($tenant)) {
                    // Additional tenant - get ODB url.
                    $odburl = utils::get_odburl_for_user($userid);
                    if (!empty($odburl)) {
                        $metadata = json_encode(['odburl' => $odburl]);
                    }
                }
                $now = time();
                $userobjectdata = (object)[
                    'type' => 'user',
                    'subtype' => '',
                    'objectid' => $o365user->objectid,
                    'o365name' => str_replace('#ext#', '#EXT#', $o365user->useridentifier),
                    'moodleid' => $userid,
                    'tenant' => $tenant,
                    'metadata' => $metadata,
                    'timecreated' => $now,
                    'timemodified' => $now,
                ];
                $userobjectdata->id = $DB->insert_record('local_o365_objects', $userobjectdata);
            }

            // Sync profile photo and timezone.
            $usersyncsettings = get_config('local_o365', 'usersync');
            $usersyncsettings = array_flip(explode(',', $usersyncsettings));
            $usersync = new feature\usersync\main();
            if (isset($usersyncsettings['photosynconlogin'])) {
                $usersync->assign_photo($userid);
            }
            if (isset($usersyncsettings['tzsynconlogin'])) {
                $usersync->sync_timezone($userid);
            }

            return true;
        } catch (moodle_exception $e) {
            utils::debug($e->getMessage(), __METHOD__, $e);
        }
        return false;
    }

    /**
     * Handle user_enrolment_updated event.
     *
     * Does the following:
     *  - remove user from Microsoft Teams when they are suspended but still enrolled.
     *  - add user to Microsoft Teams when they are unsuspended.
     *
     * @param user_enrolment_updated $event The triggered event.
     * @return bool Success/Failure.
     */
    public static function handle_user_enrolment_updated(user_enrolment_updated $event): bool {
        // Do nothing if sync direction is Teams to Moodle.
        $courseusersyncdirection = get_config('local_o365', 'courseusersyncdirection');
        if ($courseusersyncdirection == COURSE_USER_SYNC_DIRECTION_TEAMS_TO_MOODLE) {
            return false;
        }

        if (utils::is_connected() !== true || \local_o365\feature\coursesync\utils::is_enabled() !== true ||
            \local_o365\feature\coursesync\utils::is_course_sync_enabled($event->courseid) !== true) {
            return false;
        }

        return \local_o365\feature\coursesync\utils::sync_user_role_in_course_group($event->relateduserid, $event->courseid);
    }

    /**
     * Handle enrol_instance_updated event.
     *
     * Does the following:
     *  - check all users enrolled using the updated enrolment method, and update ownership/membership in the Microsoft 365 group
     * connected to the Moodle course.
     *
     * @param enrol_instance_updated $event
     * @return bool
     */
    public static function handle_enrol_instance_updated(enrol_instance_updated $event): bool {
        global $DB;

        // Do nothing if sync direction is Teams to Moodle.
        $courseusersyncdirection = get_config('local_o365', 'courseusersyncdirection');
        if ($courseusersyncdirection == COURSE_USER_SYNC_DIRECTION_TEAMS_TO_MOODLE) {
            return false;
        }

        $courseid = $event->courseid;

        if (empty($courseid)) {
            return false;
        }

        if (utils::is_connected() !== true || \local_o365\feature\coursesync\utils::is_enabled() !== true ||
            \local_o365\feature\coursesync\utils::is_course_sync_enabled($courseid) !== true) {
            return false;
        }

        // Ensure course is connected.
        $coursegroupobjectrecordid = \local_o365\feature\coursesync\utils::get_group_object_record_id_by_course_id($courseid);
        if (!$coursegroupobjectrecordid) {
            return false;
        }

        // If the course is an SDS course and the SDS enrolment sync option is off, don't update enrolment.
        if ($DB->record_exists('local_o365_objects', ['type' => 'sdssection', 'moodleid' => $courseid])) {
            // SDS course.
            if (!get_config('local_o365', 'sdsenrolmentenabled') || !get_config('local_o365', 'sdssyncenrolmenttosds')) {
                return false;
            }
        }

        $userenrolments = $DB->get_records('user_enrolments', ['enrolid' => $event->objectid]);

        foreach ($userenrolments as $userenrolment) {
            \local_o365\feature\coursesync\utils::sync_user_role_in_course_group($userenrolment->userid, $courseid, 0,
                $coursegroupobjectrecordid, true);
        }

        return true;
    }

    /**
     * Handle course_created event.
     *
     * Does the following:
     *  - process custom course request from Microsoft Teams approval.
     *  - enable sync on the new courses if course sync is "custom", and the option to enable sync on new courses by default is set.
     *
     * @param course_created $event The triggered event.
     * @return bool Success/Failure.
     */
    public static function handle_course_created(course_created $event): bool {
        global $DB;

        if (utils::is_connected() !== true) {
            return false;
        }

        $coursecreatedfromcustomcourserequest = false;

        // Process course request approval.
        $courseid = $event->objectid;
        $course = get_course($courseid);
        $shortnametocheck = $course->shortname;

        // First, try to get a record with an exact match on shortname.
        $customrequest = $DB->get_record('local_o365_course_request',
            ['courseshortname' => $shortnametocheck, 'requeststatus' => feature\courserequest\main::COURSE_REQUEST_STATUS_PENDING]);

        // If no exact match, try removing the suffix _(number).
        if (!$customrequest && preg_match('/^(.+)_(\d+)$/', $course->shortname, $matches)) {
            $shortnametocheck = $matches[1];
            $customrequest = $DB->get_record('local_o365_course_request', ['courseshortname' => $shortnametocheck,
                'requeststatus' => feature\courserequest\main::COURSE_REQUEST_STATUS_PENDING]);
        }

        if ($customrequest && $DB->record_exists('course_request', ['shortname' => $shortnametocheck])) {
            $coursecreatedfromcustomcourserequest = true;
            $task = new processcourserequestapproval();
            $task->set_custom_data(['customrequest' => $customrequest, 'courseid' => $courseid, 'shortname' => $shortnametocheck]);
            $task->set_next_run_time(time() + 1);
            manager::queue_adhoc_task($task);
        }

        // Enable team sync for newly created courses if the create teams setting is "custom", and the option to enable sync on
        // new courses by default is on.
        if (!$coursecreatedfromcustomcourserequest) {
            $syncnewcoursesetting = get_config('local_o365', 'sync_new_course');
            if ((get_config('local_o365', 'coursesync') === 'oncustom') && $syncnewcoursesetting) {
                \local_o365\feature\coursesync\utils::set_course_sync_enabled($event->objectid, true);
            }
        }

        return true;
    }

    /**
     * Handle course_restored event.
     *
     * Does the following:
     *  - enable sync on new courses if course sync is "custom", and the option to enable sync on new courses by default is set.
     *
     * @param course_restored $event
     *
     * @return bool
     */
    public static function handle_course_restored(course_restored $event): bool {
        if (utils::is_connected() !== true) {
            return false;
        }

        $eventdata = $event->get_data();

        // Enable team sync for newly restored courses if the create teams setting is "custom", and the option to enable sync on
        // new courses by default is on.
        $syncnewcoursesetting = get_config('local_o365', 'sync_new_course');
        if ((get_config('local_o365', 'coursesync') === 'oncustom') && $syncnewcoursesetting) {
            if (isset($eventdata['other']) && isset($eventdata['other']['target']) &&
                $eventdata['other']['target'] == backup::TARGET_NEW_COURSE) {
                \local_o365\feature\coursesync\utils::set_course_sync_enabled($event->objectid, true);
            }
        }

        return true;
    }

    /**
     * Handle course_updated event.
     *
     * Does the following:
     *  - update Teams name, if the options are enabled.
     *
     * @param course_updated $event The triggered event.
     * @return bool Success/Failure.
     */
    public static function handle_course_updated(course_updated $event): bool {
        if (utils::is_connected() !== true) {
            return false;
        }
        $courseid = $event->objectid;
        $eventdata = $event->get_data();
        if (!empty($eventdata['other'])) {
            // Update Teams names.
            $teamsyncenabled = get_config('local_o365', 'team_name_sync');

            if ($teamsyncenabled && \local_o365\feature\coursesync\utils::is_enabled() === true) {
                $apiclient = \local_o365\feature\coursesync\utils::get_graphclient();
                $coursesycnmain = new main($apiclient, true);

                if (\local_o365\feature\coursesync\utils::is_course_sync_enabled($courseid)) {
                    $coursesycnmain->update_team_name($courseid);
                }
            }
        }

        return true;
    }

    /**
     * Handle course_deleted event.
     *
     * Does the following:
     *  - delete course connection records.
     *  - delete SDS connection records.
     *  - delete connect group if the option is enabled.
     *
     * @param course_deleted $event The triggered event.
     * @return bool Success/Failure.
     */
    public static function handle_course_deleted(course_deleted $event): bool {
        global $DB;

        if (utils::is_connected() !== true) {
            return false;
        }
        $courseid = $event->objectid;

        // Delete SDS section record, or delete connected group.
        if ($DB->record_exists('local_o365_objects', ['type' => 'sdssection', 'moodleid' => $courseid])) {
            $DB->delete_records('local_o365_objects', ['type' => 'sdssection', 'moodleid' => $courseid]);
        } else {
            if (get_config('local_o365', 'delete_group_on_course_deletion')) {
                \local_o365\feature\coursesync\utils::delete_microsoft_365_group($courseid);
            }
        }

        // Delete group mapping records.
        $DB->delete_records_select('local_o365_objects',
            "type = 'group' AND subtype in ('course', 'courseteam', 'teamfromgroup') AND moodleid = ?", [$courseid]);

        return true;
    }

    /**
     * Handle role_assigned event.
     *
     * Does the following:
     *  - sync group ownership/membership if the course is connected to a Microsoft 365 group.
     *
     * @param role_assigned $event The triggered event.
     * @return bool Success/Failure.
     */
    public static function handle_role_assigned(role_assigned $event): bool {
        // Do nothing if sync direction is Teams to Moodle.
        $courseusersyncdirection = get_config('local_o365', 'courseusersyncdirection');
        if ($courseusersyncdirection == COURSE_USER_SYNC_DIRECTION_TEAMS_TO_MOODLE) {
            return false;
        }

        if (utils::is_connected() !== true) {
            return false;
        }

        // Update group membership.
        if (\local_o365\feature\coursesync\utils::is_enabled() === true &&
            \local_o365\feature\coursesync\utils::is_course_sync_enabled($event->courseid) === true) {
            return \local_o365\feature\coursesync\utils::sync_user_role_in_course_group($event->relateduserid, $event->courseid);
        }

        return true;
    }

    /**
     * Handle role_unassigned event.
     *
     * Does the following:
     *  - check if group sync is enabled for the course. If it does, remove the user as group owner if the user is a teacher.
     *
     * @param role_unassigned $event The triggered event.
     * @return bool Success/Failure.
     */
    public static function handle_role_unassigned(role_unassigned $event): bool {
        // Do nothing if sync direction is Teams to Moodle.
        $courseusersyncdirection = get_config('local_o365', 'courseusersyncdirection');
        if ($courseusersyncdirection == COURSE_USER_SYNC_DIRECTION_TEAMS_TO_MOODLE) {
            return false;
        }
        if (utils::is_connected() !== true) {
            return false;
        }

        // Update group membership.
        if (\local_o365\feature\coursesync\utils::is_enabled() === true &&
            \local_o365\feature\coursesync\utils::is_course_sync_enabled($event->courseid) === true) {
            return \local_o365\feature\coursesync\utils::sync_user_role_in_course_group($event->relateduserid, $event->courseid, 0,
                0, false, $event->objectid);
        }

        return true;
    }

    /**
     * Handle capability_assigned or capability_unassigned events.
     * Does the following:
     *  - check if capabilities related to users' team roles in connected teams are made, queue ad-hoc task to update user team
     * roles if needed.
     *
     * @param capability_assigned|capability_unassigned $event
     * @return bool
     */
    public static function handle_capability_change($event): bool {
        $roleid = $event->objectid;

        // Resync owners and members in the groups connected to enabled Moodle courses.
        if (utils::is_connected() === true) {
            $data = $event->get_data();
            if (isset($data['other']['capability']) && in_array($data['other']['capability'],
                    ['local/o365:teammember', 'local/o365:teamowner'])) {
                $existingtasks = manager::get_adhoc_tasks('\local_o365\task\groupmembershipsync');
                if (empty($existingtasks)) {
                    $groupmembershipsync = new groupmembershipsync();
                    manager::queue_adhoc_task($groupmembershipsync);
                }
            }
        }

        return true;
    }

    /**
     * Handle role_deleted event
     *
     * Does the following:
     *  - Unfortunately the role has already been deleted when we hear about it here, and have no way to determine the affected
     *    users. Therefore, we have to do a global sync.
     *
     * @param role_deleted $event The triggered event.
     * @return bool Success/Failure.
     */
    public static function handle_role_deleted(role_deleted $event): bool {
        if (utils::is_connected() !== true) {
            return false;
        }

        // Resync owners and members in the groups connected to enabled Moodle courses.
        if (utils::is_connected() === true) {
            $existingtasks = manager::get_adhoc_tasks('\local_o365\task\groupmembershipsync');
            if (empty($existingtasks)) {
                $groupmembershipsync = new groupmembershipsync();
                manager::queue_adhoc_task($groupmembershipsync);
            }
        }

        return true;
    }

    /**
     * Handle user_deleted event.
     *
     * @param user_deleted $event The triggered event.
     * @return bool Success/Failure.
     */
    public static function handle_user_deleted(user_deleted $event): bool {
        global $DB;
        $userid = $event->objectid;
        $DB->delete_records('local_o365_token', ['user_id' => $userid]);
        $DB->delete_records('local_o365_objects', ['type' => 'user', 'moodleid' => $userid]);
        $DB->delete_records('local_o365_connections', ['muserid' => $userid]);
        $DB->delete_records('local_o365_appassign', ['muserid' => $userid]);
        return true;
    }

    /**
     * Action when certain configuration are changed.
     *
     * @param config_log_created $event
     *
     * @return bool
     */
    public static function handle_config_log_created(config_log_created $event): bool {
        global $DB;

        $eventdata = $event->get_data();

        $cachespurgeneeded = false;

        if ($eventdata['other']['plugin'] == 'auth_oidc') {
            switch ($eventdata['other']['name']) {
                case 'clientid':
                    // Clear local_o365_token table.
                    $DB->delete_records('local_o365_token');

                    // Clear auth_oidc_token table.
                    $DB->delete_records('auth_oidc_token');

                    // Clear local_o365_connections table.
                    $DB->delete_records('local_o365_connections');

                    // Clear user records in local_o365_objects table.
                    $DB->delete_records('local_o365_objects', ['type' => 'user']);

                    // Delete delta user token, and force a user sync task run.
                    unset_config('local_o365', 'task_usersync_lastdeltatoken');
                    if ($usersynctask = $DB->get_record('task_scheduled',
                        ['component' => 'local_o365', 'classname' => '\local_o365\task\usersync'])) {
                        $usersynctask->nextruntime = time();
                        $DB->update_record('task_scheduled', $usersynctask);
                    }

                    // No call to "break;" on purpose.
                case 'idptype':
                case 'clientauthmethod':
                    // If client ID, IdP type, or authentication method has changed, unset token and verify setup results.
                    // Azure admin needs to provide consent again.
                    unset_config('apptokens', 'local_o365');
                    unset_config('adminconsent', 'local_o365');
                    unset_config('verifysetupresult', 'local_o365');

                    $cachespurgeneeded = true;
            }
        }

        // If Entra tenant is changed, user manual matching and connection records need to be deleted.
        if ($eventdata['other']['plugin'] == 'local_o365' && $eventdata['other']['name'] == 'entratenant') {
            // Clear local_o365_connections table.
            $DB->delete_records('local_o365_connections');

            // Clear user records in local_o365_objects table.
            $DB->delete_records('local_o365_objects', ['type' => 'user']);

            $cachespurgeneeded = true;
        }

        // Purge caches if needed.
        if ($cachespurgeneeded) {
            purge_all_caches();
        }

        return true;
    }

    /**
     * Handles actions to be performed when a cohort is deleted.
     *
     * @param cohort_deleted $event
     *
     * @return bool
     */
    public static function handle_cohort_deleted(cohort_deleted $event): bool {
        global $DB;

        $cohortid = $event->objectid;
        $DB->delete_records('local_o365_objects', ['type' => 'group', 'subtype' => 'cohort', 'moodleid' => $cohortid]);

        return true;
    }
}
