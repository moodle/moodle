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
 * Utility class for the group / team sync feature.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\feature\coursesync;

use context_course;
use Exception;
use local_o365\httpclient;
use local_o365\oauth2\clientdata;
use local_o365\rest\unified;
use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/enrollib.php');

/**
 * A utility class for the group / team sync feature.
 */
class utils {
    /**
     * Determine whether the course sync feature is enabled or not.
     *
     * @return bool True if group creation is enabled. False otherwise.
     */
    public static function is_enabled() : bool {
        $coursesyncsetting = get_config('local_o365', 'coursesync');
        return $coursesyncsetting === 'oncustom' || $coursesyncsetting === 'onall';
    }

    /**
     * Get an array of enabled courses.
     *
     * @return bool|array Array of course IDs, or TRUE if all courses enabled.
     */
    public static function get_enabled_courses() {
        $coursesyncsetting = get_config('local_o365', 'coursesync');
        if ($coursesyncsetting === 'onall') {
            return true;
        } else if ($coursesyncsetting === 'oncustom') {
            $coursesenabled = get_config('local_o365', 'coursesynccustom');
            $coursesenabled = @json_decode($coursesenabled, true);
            if (!empty($coursesenabled) && is_array($coursesenabled)) {
                return array_keys($coursesenabled);
            }
        }
        return [];
    }

    /**
     * Determine whether a course is enabled for sync.
     *
     * @param int $courseid The Moodle course ID to check.
     * @return bool Whether the course is enabled for sync.
     */
    public static function is_course_sync_enabled(int $courseid) : bool {
        $coursesyncsetting = get_config('local_o365', 'coursesync');
        if ($coursesyncsetting === 'onall') {
            return true;
        } else if ($coursesyncsetting === 'oncustom') {
            $coursesenabled = get_config('local_o365', 'coursesynccustom');
            $coursesenabled = @json_decode($coursesenabled, true);
            if (!empty($coursesenabled) && is_array($coursesenabled) && isset($coursesenabled[$courseid])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Create connection to graph.
     * @return false|object Graph api.
     */
    public static function get_graphclient() {
        if (\local_o365\utils::is_configured() !== true) {
            return false;
        }

        if (static::is_enabled() !== true) {
            return false;
        }

        $httpclient = new httpclient();
        $clientdata = clientdata::instance_from_oidc();
        $tokenresource = unified::get_tokenresource();
        $unifiedtoken = \local_o365\utils::get_app_or_system_token($tokenresource, $clientdata, $httpclient);

        if (empty($unifiedtoken)) {
            return false;
        }

        return new unified($unifiedtoken, $httpclient);
    }

    /**
     * Get local o365 object for course with the given ID.
     *
     * @param int $courseid
     * @return false|object Array containing o365 object data.
     */
    public static function get_o365_object(int $courseid) {
        global $DB;

        $params = [
            'type' => 'group',
            'subtype' => 'course',
            'moodleid' => $courseid,
        ];
        $object = $DB->get_record('local_o365_objects', $params);
        if (empty($object)) {
            return false;
        }

        return $object;
    }

    /**
     * Get URLs of Microsoft 365 services for the Moodle course with the given ID.
     *
     * @param int $courseid
     * @return string[]|null
     */
    public static function get_course_microsoft_365_urls(int $courseid) : ?array {
        $object = static::get_o365_object($courseid);
        if (empty($object->objectid)) {
            return null;
        }
        try {
            $graphapi = static::get_graphclient();
            $urls = $graphapi->get_group_urls($object->objectid);
            if (!empty($urls)) {
                return $urls;
            } else {
                return null;
            }
        } catch (Exception $e) {
            \local_o365\utils::debug('Exception while retrieving group urls: groupid ' . $object->objectid . ' ' .
                $e->getMessage(), __METHOD__);
            return null;
        }
    }

    /**
     * (Soft) delete a Microsoft 365 group.
     *
     * @param int $courseid The ID of the course.
     */
    public static function delete_microsoft_365_group(int $courseid) {
        global $DB;

        $params = ['type' => 'group', 'subtype' => 'course', 'moodleid' => $courseid];
        $objectrec = $DB->get_record('local_o365_objects', $params);
        if (!empty($objectrec)) {
            $graphclient = unified::instance_for_user();
            $result = $graphclient->delete_group($objectrec->objectid);

            if ($result === true) {
                $metadata = (!empty($objectrec->metadata)) ? @json_decode($objectrec->metadata, true) : [];
                if (empty($metadata) || !is_array($metadata)) {
                    $metadata = [];
                }
                $metadata['softdelete'] = true;
                $updatedobject = new stdClass;
                $updatedobject->id = $objectrec->id;
                $updatedobject->metadata = json_encode($metadata);
                $DB->update_record('local_o365_objects', $updatedobject);
            }
        }
    }

    /**
     * Change whether groups are enabled for a course.
     *
     * @param int $courseid The ID of the course.
     * @param bool $enabled Whether to enable or disable.
     */
    public static function set_course_sync_enabled(int $courseid, bool $enabled = true) {
        $customcoursesyncsetting = get_config('local_o365', 'coursesynccustom');
        $customcoursesyncsetting = @json_decode($customcoursesyncsetting, true);
        if (empty($customcoursesyncsetting) || !is_array($customcoursesyncsetting)) {
            $customcoursesyncsetting = [];
        }

        if ($enabled === true) {
            $customcoursesyncsetting[$courseid] = $enabled;
        } else {
            if (isset($customcoursesyncsetting[$courseid])) {
                unset($customcoursesyncsetting[$courseid]);
                if (get_config('local_o365', 'delete_group_on_course_sync_disabled')) {
                    static::delete_microsoft_365_group($courseid);
                }
            }
        }

        set_config('coursesynccustom', json_encode($customcoursesyncsetting), 'local_o365');
    }

    /**
     * Return a list of unmatched teams, which can be used as connecting options.
     *
     * @param string $currentoid
     * @return array
     */
    public static function get_matching_team_options(string $currentoid = '') : array {
        global $DB;

        $teamsoptions = [];
        $teamnamecache = [];
        $matchedid = 0;

        $matchedoids = $DB->get_fieldset_select('local_o365_objects', 'objectid', 'type = ? AND subtype = ?', ['group', 'course']);

        $teamcacherecords = $DB->get_records('local_o365_teams_cache');
        foreach ($teamcacherecords as $key => $teamcacherecord) {
            if ($teamcacherecord->objectid == $currentoid || !in_array($teamcacherecord->objectid, $matchedoids)) {
                if (!array_key_exists($teamcacherecord->name, $teamnamecache)) {
                    $teamnamecache[$teamcacherecord->name] = [];
                }
                $teamnamecache[$teamcacherecord->name][] = $teamcacherecord->objectid;
            } else {
                unset($teamcacherecords[$key]);
            }
        }

        foreach ($teamcacherecords as $teamcacherecord) {
            $teamoidpart = '';
            if (count($teamnamecache[$teamcacherecord->name]) > 1) {
                $teamoidpart = ' [' . $teamcacherecord->objectid . '] ';
            }
            if ($teamcacherecord->objectid == $currentoid) {
                $teamsoptions[$teamcacherecord->id] = $teamcacherecord->name . $teamoidpart . ' - ' .
                    get_string('acp_teamconnections_current_connection', 'local_o365');
                $matchedid = $teamcacherecord->id;
            } else {
                $teamsoptions[$teamcacherecord->id] = $teamcacherecord->name . $teamoidpart;
            }
        }

        natcasesort($teamsoptions);

        if (!$currentoid) {
            $teamsoptions = ['0' => get_string('acp_teamconnections_not_connected', 'local_o365')] + $teamsoptions;
        }

        return [$teamsoptions, $matchedid];
    }

    /**
     * Return the display name of Team for the given course according to configuration.
     *
     * @param stdClass $course
     * @param string $forcedprefix
     * @param stdClass $group
     *
     * @return string
     */
    public static function get_team_display_name(stdClass $course, string $forcedprefix = '', stdClass $group = null) {
        if ($forcedprefix) {
            $teamdisplayname = $forcedprefix;
        } else {
            $teamdisplayname = '';
        }

        $teamnameprefix = get_config('local_o365', 'team_name_prefix');
        if ($teamnameprefix) {
            $teamdisplayname .= $teamnameprefix;
        }

        $teamnamecourse = get_config('local_o365', 'team_name_course');
        switch ($teamnamecourse) {
            case main::NAME_OPTION_FULL_NAME:
                $teamdisplayname .= $course->fullname;
                break;
            case main::NAME_OPTION_SHORT_NAME:
                $teamdisplayname .= $course->shortname;
                break;
            case main::NAME_OPTION_ID:
                $teamdisplayname .= $course->id;
                break;
            case main::NAME_OPTION_ID_NUMBER:
                $teamdisplayname .= $course->idnumber;
                break;
            default:
                $teamdisplayname .= $course->fullname;
        }

        if ($group) {
            $teamdisplayname .= $group->name;
        }

        $teamnamesuffix = get_config('local_o365', 'team_name_suffix');
        if ($teamnamesuffix) {
            $teamdisplayname .= $teamnamesuffix;
        }

        return substr($teamdisplayname, 0, 256);
    }

    /**
     * Return the team display name and group mail alias to be used on the sample course according to the current settings.
     *
     * @return array
     */
    public static function get_sample_team_group_names() : array {
        $teamgroupamesamplecourse = static::get_team_group_name_sample_course();

        return [static::get_team_display_name($teamgroupamesamplecourse), static::get_group_mail_alias($teamgroupamesamplecourse)];
    }

    /**
     * Return the email alias of group for the given course according to configuration.
     *
     * @param stdClass $course
     *
     * @return string
     */
    public static function get_group_mail_alias(stdClass $course) : string {
        $groupmailaliasprefix = get_config('local_o365', 'group_mail_alias_prefix');
        if ($groupmailaliasprefix) {
            $groupmailaliasprefix = static::clean_up_group_mail_alias($groupmailaliasprefix);
        }

        $groupmailaliassuffix = get_config('local_o365', 'group_mail_alias_suffix');
        if ($groupmailaliassuffix) {
            $groupmailaliassuffix = static::clean_up_group_mail_alias($groupmailaliassuffix);
        }

        $groupmailaliascourse = get_config('local_o365', 'group_mail_alias_course');
        switch ($groupmailaliascourse) {
            case main::NAME_OPTION_FULL_NAME:
                $coursepart = $course->fullname;
                break;
            case main::NAME_OPTION_SHORT_NAME:
                $coursepart = $course->shortname;
                break;
            case main::NAME_OPTION_ID:
                $coursepart = $course->id;
                break;
            case main::NAME_OPTION_ID_NUMBER:
                $coursepart = $course->idnumber;
                break;
            default:
                $coursepart = $course->shortname;
        }

        $coursepart = static::clean_up_group_mail_alias($coursepart);

        $coursepartmaxlength = 59 - strlen($groupmailaliasprefix) - strlen($groupmailaliassuffix);
        if (strlen($coursepart) > $coursepartmaxlength) {
            $coursepart = substr($coursepart, 0, $coursepartmaxlength);
        }

        return $groupmailaliasprefix . $coursepart . $groupmailaliassuffix;
    }

    /**
     * Remove unsupported characters from the mail alias parts, and return the result.
     *
     * @param string $mailalias
     * @return string
     */
    public static function clean_up_group_mail_alias(string $mailalias) {
        $notallowedbasicchars = ['@', '(', ')', "\\", '[', ']', '"', ';', ':', '.', '<', '>', ' '];
        $chars = preg_split( '//u', $mailalias, null, PREG_SPLIT_NO_EMPTY);
        foreach($chars as $key => $char){
            $charorder = ord($char);
            if ($charorder < 0 || $charorder > 127 || in_array($char, $notallowedbasicchars)) {
                unset($chars[$key]);
            }
        }

        return implode($chars);
    }

    /**
     * Return a stdClass object representing a course object to be used for Team / group naming convention example.
     *
     * @return stdClass
     */
    public static function get_team_group_name_sample_course() : stdClass {
        $samplecourse = new stdClass();
        $samplecourse->fullname = 'Sample course 15';
        $samplecourse->shortname = 'sample 15';
        $samplecourse->id = 2;
        $samplecourse->idnumber = 'Sample ID 15';

        return $samplecourse;
    }

    /**
     * Return the list of o365_object IDs for the users with the given IDs.
     *
     * @param array $userids
     * @return array
     */
    public static function get_user_object_ids_by_user_ids(array $userids) : array {
        global $DB;

        if ($userids) {
            [$idsql, $idparams] = $DB->get_in_or_equal($userids);
            $sql = "SELECT objectid
                      FROM {local_o365_objects}
                     WHERE type = ?
                       AND moodleid $idsql";
            $params = array_merge(['user'], $idparams);

            return $DB->get_fieldset_sql($sql, $params);
        } else {
            return [];
        }
    }

    /**
     * Return the object ID of the Moodle course with the given ID, or false if not found.
     *
     * @param int $courseid
     * @return false|string
     */
    public static function get_group_object_id_by_course_id(int $courseid) {
        global $DB;

        return $DB->get_field('local_o365_objects', 'objectid',
            ['type' => 'group', 'subtype' => 'course', 'moodleid' => $courseid], IGNORE_MISSING);
    }

    /**
     * Return the object record ID of the Moodle course with the given ID, or false if not found.
     *
     * @param int $courseid
     * @return false|id
     */
    public static function get_group_object_record_id_by_course_id(int $courseid) {
        global $DB;

        return $DB->get_field('local_o365_objects', 'id',
            ['type' => 'group', 'subtype' => 'course', 'moodleid' => $courseid], IGNORE_MISSING);
    }

    /**
     * Return the object ID of the Moodle user with the given ID, or false if not found.
     *
     * @param int $userid
     * @return false|string
     */
    public static function get_user_object_id_by_user_id(int $userid) {
        global $DB;

        $sql = "SELECT objs.objectid as userobjectid
                  FROM {user} u
                  JOIN {local_o365_objects} objs ON objs.moodleid = u.id
                 WHERE u.deleted = 0 AND objs.type = :user AND u.id = :userid";
        return $DB->get_field_sql($sql, ['user' => 'user', 'userid' => $userid]);
    }

    /**
     * Return the object ID of the Moodle user with the given ID, or false if not found.
     *
     * @param int $userid
     * @return false|string
     */
    public static function get_user_object_record_id_by_user_id(int $userid) {
        global $DB;

        $sql = "SELECT objs.id as userobjectrecordid
                  FROM {user} u
                  JOIN {local_o365_objects} objs ON objs.moodleid = u.id
                 WHERE u.deleted = 0 AND objs.type = :user AND u.id = :userid";
        return $DB->get_field_sql($sql, ['user' => 'user', 'userid' => $userid]);
    }

    /**
     * Return the object IDs of users who have Team owner capability in the course with the given ID.
     *
     * @param int $courseid
     * @return array
     */
    public static function get_team_owner_object_ids_by_course_id(int $courseid) : array {
        $teamownerobjectids = [];
        $teamowneruserids = static::get_team_owner_user_ids_by_course_id($courseid);
        if ($teamowneruserids) {
            $teamownerobjectids = static::get_user_object_ids_by_user_ids($teamowneruserids);
        }

        return $teamownerobjectids;
    }

    /**
     * Helper function to retrieve users who have Team owner capability in the course with the given ID.
     *
     * @param int $courseid ID of Moodle course
     * @return array array containing IDs of teachers.
     */
    public static function get_team_owner_user_ids_by_course_id(int $courseid) : array {
        $context = context_course::instance($courseid);
        $teamownerusers = get_enrolled_users($context, 'local/o365:teamowner', 0, 'u.*', null, 0, 0, true);
        $teamowneruserids = [];
        foreach ($teamownerusers as $user) {
            if (!$user->deleted) {
                $teamowneruserids[] = $user->id;
            }
        }

        return $teamowneruserids;
    }

    /**
     * Return the object IDs of users who have Team member capability in the course with the given ID.
     *
     * @param int $courseid
     * @param array $teamownerobjectids
     * @return array
     */
    public static function get_team_member_object_ids_by_course_id(int $courseid, array $teamownerobjectids = []) : array {
        $teammemberobjectids = [];
        $teammemberuserids = static::get_team_member_user_ids_by_course_id($courseid);
        if ($teammemberuserids) {
            $teammemberobjectids = static::get_user_object_ids_by_user_ids($teammemberuserids);
        }

        $teammemberobjectids = array_diff($teammemberobjectids, $teamownerobjectids);

        return $teammemberobjectids;
    }

    /**
     * Helper function to retrieve users who have Team member capability in the course with the given ID.
     *
     * @param int $courseid ID of the Moodle course
     * @return array
     */
    public static function get_team_member_user_ids_by_course_id(int $courseid) : array {
        $context = context_course::instance($courseid);
        $teammemberusers = get_enrolled_users($context, 'local/o365:teammember', 0, 'u.*', null, 0, 0, true);
        $teammemberuserids = [];
        foreach ($teammemberusers as $user) {
            if (!$user->deleted) {
                $teammemberuserids[] = $user->id;
            }
        }

        return $teammemberuserids;
    }

    /**
     * Put lists of owners / members received into chunks of 20 users.
     *
     * @param array $owners
     * @param array $members
     * @return array
     */
    public static function arrange_group_users_in_chunks(array $owners, array $members) : array {
        $userchunks = [];

        $ownerchunks = array_chunk($owners, 20);
        foreach ($ownerchunks as $ownerchunk) {
            $userchunk = ['owner' => $ownerchunk];
            $userchunks[] = $userchunk;
        }
        $members = array_merge($owners, $members);
        $memberchunks = array_chunk($members, 20);
        foreach ($memberchunks as $memberchunk) {
            $userchunk = ['member' => $memberchunk];
            $userchunks[] = $userchunk;
        }

        return $userchunks;
    }

    /**
     * Get a Microsoft Graph API instance.
     *
     * @param string $caller The calling function, used for logging.
     * @return unified|bool A Microsoft Graph API instance.
     */
    public static function get_unified_api(string $caller = 'local_o365/feature/coursesync/get_unified_api') {
        $clientdata = clientdata::instance_from_oidc();
        $httpclient = new httpclient();
        $tokenresource = unified::get_tokenresource();
        $token = \local_o365\utils::get_app_or_system_token($tokenresource, $clientdata, $httpclient);
        if (!empty($token)) {
            return new unified($token, $httpclient);
        } else {
            $msg = 'Couldn\'t construct Microsoft Graph API client because we didn\'t have a system API user token.';
            \local_o365\utils::debug($msg, $caller);
            return false;
        }
    }

    /**
     * Migrate existing groups by adding education specific parameters.
     *
     * @return void
     */
    public static function migrate_existing_groups() {
        global $DB;

        try {
            $graphclient = static::get_unified_api();
            if ($graphclient) {
                $coursesync = new main($graphclient);
                if ($coursesync) {
                    if ($coursesync->has_education_license()) {
                        $syncedcourses = static::get_enabled_courses();
                        // Exclude SDS synced courses.
                        $sdscourseids = $DB->get_fieldset_select('local_o365_objects', 'moodleid',
                            'type = ? AND subtype = ?', ['sdssection', 'course']);
                        foreach ($syncedcourses as $courseid) {
                            if (in_array($courseid, $sdscourseids)) {
                                continue;
                            }
                            if ($course = $DB->get_record('course', ['id' => $courseid])) {
                                if ($groupobject = $DB->get_record('local_o365_objects',
                                    ['type' => 'group', 'subtype' => 'course', 'moodleid' => $courseid])) {
                                    $coursesync->set_lti_properties_in_education_group($groupobject->objectid, $course);
                                }
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            // Cannot get graph client, nothing to do.
        }
    }

    /**
     * Determine the Microsoft 365 group role that the user with the given ID should have in the Microsoft 365 group connected to
     * the Moodle course with the given ID.
     *
     * @param int $userid
     * @param int $courseid
     * @param int $excluderoleid
     * @return string
     */
    public static function get_user_group_role_by_moodle_ids(int $userid, int $courseid, int $excluderoleid = 0) : string {
        $grouprole = '';

        $coursecontext = context_course::instance($courseid);

        // Get user roles in the course.
        $userroles = get_user_roles($coursecontext, $userid, false);
        $userroleids = array_column($userroles, 'roleid');
        if ($excluderoleid) {
            unset($userroleids[$excluderoleid]);
        }

        // Get group owner and member roles.
        $ownerroles = get_roles_with_capability('local/o365:teamowner', CAP_ALLOW, $coursecontext);
        $ownerroleids = array_keys($ownerroles);
        $memberroles = get_roles_with_capability('local/o365:teammember', CAP_ALLOW, $coursecontext);
        $memberroleids = array_keys($memberroles);

        if (!empty(array_intersect($userroleids, $ownerroleids))) {
            $grouprole = MICROSOFT365_GROUP_ROLE_OWNER;
        } else if (!empty(array_intersect($userroleids, $memberroleids))) {
            $grouprole = MICROSOFT365_GROUP_ROLE_MEMBER;
        }

        return $grouprole;
    }

    /**
     * Sync the user's role in the Microsoft 365 group connected the course.
     *
     * @param int $userid
     * @param int $courseid
     * @param int $userobjectrecordid
     * @param int $coursegroupobjectrecordid
     * @param bool $sdscoursechecked
     * @param int $excluderoleid
     * @return bool
     */
    public static function sync_user_role_in_course_group(int $userid, int $courseid, int $userobjectrecordid = 0,
        int $coursegroupobjectrecordid = 0, bool $sdscoursechecked = false, int $excluderoleid = 0) : bool {
        global $DB;

        if (empty($userid) || empty($courseid)) {
            return false;
        }

        $apiclient = static::get_unified_api(__METHOD__);
        $coursesync = new main($apiclient);

        // Ensure course is connected.
        if (!$coursegroupobjectrecordid) {
            $coursegroupobjectrecordid = static::get_group_object_record_id_by_course_id($courseid);
            if (!$coursegroupobjectrecordid) {
                return false;
            }
        }

        // Ensure user is connected.
        if (!$userobjectrecordid) {
            $userobjectrecordid = static::get_user_object_record_id_by_user_id($userid);
            if (!$userobjectrecordid) {
                return false;
            }
        }

        // If the course is an SDS course and the SDS enrolment sync option is off, don't update enrolment.
        if (!$sdscoursechecked) {
            if ($DB->record_exists('local_o365_objects', ['type' => 'sdssection', 'moodleid' => $courseid])) {
                // For SDS courses, only perform sync if advanced enrolment sync option is enabled.
                if (!get_config('local_o365', 'sdsenrolmentenabled') || !get_config('local_o365', 'sdssyncenrolmenttosds')) {
                    return false;
                }
            }
        }

        // Determine user group role.
        $grouprole = static::get_user_group_role_by_moodle_ids($userid, $courseid, $excluderoleid);

        // Get group and user object IDs.
        $groupobjectid = utils::get_object_id_by_record_id($coursegroupobjectrecordid);
        $userobjectid = utils::get_object_id_by_record_id($userobjectrecordid);

        // If the user doesn't have any group role, remove the user from the connected group.
        if (!$grouprole) {
            try {
                // Remove owner and member from the Microsoft 365 group.
                $coursesync->remove_member_from_group($groupobjectid, $userobjectid);
                $coursesync->remove_owner_from_group($groupobjectid, $userobjectid);
            } catch (Exception $e) {
                \local_o365\utils::debug('Exception: ' . $e->getMessage(), __METHOD__, $e);
                return false;
            }
        } else {
            // Check if the user was suspended, or unsuspended. Add or remove them in the Microsoft 365 group as appropriate.
            if (!is_enrolled(context_course::instance($courseid), $userid, null, true)) {
                // The user doesn't have a valid enrolment any more - we need to remove the user.
                try {
                    // Remove user as owner and member from the Microsoft 365 group.
                    $coursesync->remove_member_from_group($groupobjectid, $userobjectid);
                    $coursesync->remove_owner_from_group($groupobjectid, $userobjectid);
                } catch (Exception $e) {
                    \local_o365\utils::debug('Exception: ' . $e->getMessage(), __METHOD__, $e);
                    return false;
                }
            } else {
                // The user has a valid enrolment, we need to add the user.
                try {
                    switch ($grouprole) {
                        case MICROSOFT365_GROUP_ROLE_OWNER:
                            // Add user to the Microsoft 365 group as owner.
                            $coursesync->add_owner_to_group($groupobjectid, $userobjectid);
                            break;
                        case MICROSOFT365_GROUP_ROLE_MEMBER:
                            // Add user to the Microsoft 365 group as member.
                            $coursesync->remove_owner_from_group($groupobjectid, $userobjectid);
                            $coursesync->add_member_to_group($groupobjectid, $userobjectid);
                            break;
                    }
                } catch (Exception $e) {
                    \local_o365\utils::debug('Exception: ' . $e->getMessage(), __METHOD__, $e);
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Determine if the team is created from the group for the group with the object ID provided.
     *
     * @param string $groupobjectid
     * @return bool
     */
    public static function is_team_created_from_group(string $groupobjectid) : bool {
        global $DB;

        return $DB->record_exists('local_o365_objects',
            ['objectid' => $groupobjectid, 'type' => 'group', 'subtype' => 'teamfromgroup']);
    }

    /**
     * Return the object ID of the local_o365_objects record with the given ID.
     *
     * @param int $objectrecordid
     * @return false|string
     */
    public static function get_object_id_by_record_id(int $objectrecordid) {
        global $DB;

        $objectid = false;
        if ($objectrecord = $DB->get_record('local_o365_objects', ['id' => $objectrecordid])) {
            $objectid = $objectrecord->objectid;
        }

        return $objectid;
    }
}
