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
 * External course participation api.
 *
 * This api is mostly read only, the actual enrol and unenrol
 * support is in each enrol plugin.
 *
 * @package    core
 * @subpackage enrol
 * @copyright  2009 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

/**
 * Enrol functions
 */
class core_enrol_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_users_courses_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'user id'),
            )
        );
    }

    /**
     * Get list of courses user is enrolled in (only active enrolments are returned).
     *
     * Please note the current user must be able to access the course, otherwise the course is not included.
     *
     * @param int $userid
     * @return array of courses
     */
    public static function get_users_courses($userid) {
        global $USER, $DB;

        // Do basic automatic PARAM checks on incoming data, using params description
        // If any problems are found then exceptions are thrown with helpful error messages
        $params = self::validate_parameters(self::get_users_courses_parameters(), array('userid'=>$userid));

        $courses = enrol_get_users_courses($params['userid'], true, 'id, shortname, fullname, idnumber, visible');
        $result = array();

        foreach ($courses as $course) {
            $context = get_context_instance(CONTEXT_COURSE, $course->id);
            try {
                self::validate_context($context);
            } catch (Exception $e) {
                // current user can not access this course, sorry we can not disclose who is enrolled in this course!
                continue;
            }

            if ($userid != $USER->id and !has_capability('moodle/course:viewparticipants', $context)) {
                // we need capability to view participants
                continue;
            }

            list($enrolledsqlselect, $enrolledparams) = get_enrolled_sql($context);
            $enrolledsql = "SELECT COUNT(*) FROM ($enrolledsqlselect) AS enrolleduserids";
            $enrolledusercount = $DB->count_records_sql($enrolledsql, $enrolledparams);

            $result[] = array('id'=>$course->id, 'shortname'=>$course->shortname, 'fullname'=>$course->fullname, 'idnumber'=>$course->idnumber,'visible'=>$course->visible, 'enrolledusercount'=>$enrolledusercount);
        }

        return $result;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_users_courses_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id'        => new external_value(PARAM_INT, 'id of course'),
                    'shortname' => new external_value(PARAM_RAW, 'short name of course'),
                    'fullname'  => new external_value(PARAM_RAW, 'long name of course'),
                    'enrolledusercount' => new external_value(PARAM_INT, 'Number of enrolled users in this course'),
                    'idnumber'  => new external_value(PARAM_RAW, 'id number of course'),
                    'visible'   => new external_value(PARAM_INT, '1 means visible, 0 means hidden course'),
                )
            )
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_enrolled_users_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'course id'),
                'options'  => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name'  => new external_value(PARAM_ALPHANUMEXT, 'option name'),
                            'value' => new external_value(PARAM_RAW, 'option value')
                        )
                    ), 'Option names:
                            * withcapability (string) return only users with this capability. This option requires \'moodle/role:review\' on the course context.
                            * groupid (integer) return only users in this group id. This option requires \'moodle/site:accessallgroups\' on the course context.
                            * onlyactive (integer) return only users with active enrolments and matching time restrictions. This option requires \'moodle/course:enrolreview\' on the course context.
                            * userfields (\'string, string, ...\') return only the values of these user fields.
                            * limitfrom (integer) sql limit from.
                            * limitnumber (integer) maximum number of returned users.', VALUE_DEFAULT, array()),
            )
        );
    }

    /**
     * Get course participants details
     * @param int $courseid  course id
     * @param array $options options {
     *          'name' => option name
     *          'value' => option value
     * }
     * @return array An array of users
     */
    public static function get_enrolled_users($courseid, $options) {
        global $CFG, $USER, $DB;
        require_once($CFG->dirroot . "/user/lib.php");

        $params = self::validate_parameters(
            self::get_enrolled_users_parameters(),
            array(
                'courseid'=>$courseid,
                'options'=>$options
            )
        );
        $withcapability = '';
        $groupid        = 0;
        $onlyactive     = false;
        $userfields     = array();
        $limitfrom = 0;
        $limitnumber = 0;
        foreach ($options as $option) {
            switch ($option['name']) {
            case 'withcapability':
                $withcapability = $option['value'];
                break;
            case 'groupid':
                $groupid = (int)$option['value'];
                break;
            case 'onlyactive':
                $onlyactive = !empty($option['value']);
                break;
            case 'userfields':
                $thefields = explode(',', $option['value']);
                foreach ($thefields as $f) {
                    $userfields[] = clean_param($f, PARAM_ALPHANUMEXT);
                }
            case 'limitfrom' :
                $limitfrom = clean_param($option['value'], PARAM_INT);
                break;
            case 'limitnumber' :
                $limitnumber = clean_param($option['value'], PARAM_INT);
                break;
            }
        }

        $course = $DB->get_record('course', array('id'=>$courseid), '*', MUST_EXIST);
        $coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);
        if ($courseid == SITEID) {
            $context = get_system_context();
        } else {
            $context = $coursecontext;
        }
        try {
            self::validate_context($context);
        } catch (Exception $e) {
            $exceptionparam = new stdClass();
            $exceptionparam->message = $e->getMessage();
            $exceptionparam->courseid = $params['courseid'];
            throw new moodle_exception(get_string('errorcoursecontextnotvalid' , 'webservice', $exceptionparam));
        }

        if ($courseid == SITEID) {
            require_capability('moodle/site:viewparticipants', $context);
        } else {
            require_capability('moodle/course:viewparticipants', $context);
        }
        // to overwrite this parameter, you need role:review capability
        if ($withcapability) {
            require_capability('moodle/role:review', $coursecontext);
        }
        // need accessallgroups capability if you want to overwrite this option
        if (!empty($groupid) && groups_is_member($groupid)) {
            require_capability('moodle/site:accessallgroups', $coursecontext);
        }
        // to overwrite this option, you need course:enrolereview permission
        if ($onlyactive) {
            require_capability('moodle/course:enrolreview', $coursecontext);
        }

        list($enrolledsql, $enrolledparams) = get_enrolled_sql($coursecontext, $withcapability, $groupid, $onlyactive);
        list($ctxselect, $ctxjoin) = context_instance_preload_sql('u.id', CONTEXT_USER, 'ctx');
        $sqlparams['courseid'] = $courseid;
        $sql = "SELECT u.* $ctxselect
                  FROM {user} u $ctxjoin
                 WHERE u.id IN ($enrolledsql)
                 ORDER BY u.id ASC";
        $enrolledusers = $DB->get_recordset_sql($sql, $enrolledparams, $limitfrom, $limitnumber);
        $users = array();
        foreach ($enrolledusers as $user) {
            context_instance_preload($user);
            if ($userdetails = user_get_user_details($user, $course, $userfields)) {
                $users[] = $userdetails;
            }
        }
        $enrolledusers->close();

        return $users;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_enrolled_users_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id'    => new external_value(PARAM_NUMBER, 'ID of the user'),
                    'username'    => new external_value(PARAM_RAW, 'Username policy is defined in Moodle security config', VALUE_OPTIONAL),
                    'firstname'   => new external_value(PARAM_NOTAGS, 'The first name(s) of the user', VALUE_OPTIONAL),
                    'lastname'    => new external_value(PARAM_NOTAGS, 'The family name of the user', VALUE_OPTIONAL),
                    'fullname'    => new external_value(PARAM_NOTAGS, 'The fullname of the user'),
                    'email'       => new external_value(PARAM_TEXT, 'An email address - allow email as root@localhost', VALUE_OPTIONAL),
                    'address'     => new external_value(PARAM_MULTILANG, 'Postal address', VALUE_OPTIONAL),
                    'phone1'      => new external_value(PARAM_NOTAGS, 'Phone 1', VALUE_OPTIONAL),
                    'phone2'      => new external_value(PARAM_NOTAGS, 'Phone 2', VALUE_OPTIONAL),
                    'icq'         => new external_value(PARAM_NOTAGS, 'icq number', VALUE_OPTIONAL),
                    'skype'       => new external_value(PARAM_NOTAGS, 'skype id', VALUE_OPTIONAL),
                    'yahoo'       => new external_value(PARAM_NOTAGS, 'yahoo id', VALUE_OPTIONAL),
                    'aim'         => new external_value(PARAM_NOTAGS, 'aim id', VALUE_OPTIONAL),
                    'msn'         => new external_value(PARAM_NOTAGS, 'msn number', VALUE_OPTIONAL),
                    'department'  => new external_value(PARAM_TEXT, 'department', VALUE_OPTIONAL),
                    'institution' => new external_value(PARAM_TEXT, 'institution', VALUE_OPTIONAL),
                    'interests'   => new external_value(PARAM_TEXT, 'user interests (separated by commas)', VALUE_OPTIONAL),
                    'firstaccess' => new external_value(PARAM_INT, 'first access to the site (0 if never)', VALUE_OPTIONAL),
                    'lastaccess'  => new external_value(PARAM_INT, 'last access to the site (0 if never)', VALUE_OPTIONAL),
                    'description' => new external_value(PARAM_RAW, 'User profile description', VALUE_OPTIONAL),
                    'descriptionformat' => new external_value(PARAM_INT, 'User profile description format', VALUE_OPTIONAL),
                    'city'        => new external_value(PARAM_NOTAGS, 'Home city of the user', VALUE_OPTIONAL),
                    'url'         => new external_value(PARAM_URL, 'URL of the user', VALUE_OPTIONAL),
                    'country'     => new external_value(PARAM_ALPHA, 'Home country code of the user, such as AU or CZ', VALUE_OPTIONAL),
                    'profileimageurlsmall' => new external_value(PARAM_URL, 'User image profile URL - small version', VALUE_OPTIONAL),
                    'profileimageurl' => new external_value(PARAM_URL, 'User image profile URL - big version', VALUE_OPTIONAL),
                    'customfields' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'type'  => new external_value(PARAM_ALPHANUMEXT, 'The type of the custom field - text field, checkbox...'),
                                'value' => new external_value(PARAM_RAW, 'The value of the custom field'),
                                'name' => new external_value(PARAM_RAW, 'The name of the custom field'),
                                'shortname' => new external_value(PARAM_RAW, 'The shortname of the custom field - to be able to build the field class in the code'),
                            )
                        ), 'User custom fields (also known as user profil fields)', VALUE_OPTIONAL),
                    'groups' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'id'  => new external_value(PARAM_INT, 'group id'),
                                'name' => new external_value(PARAM_RAW, 'group name'),
                                'description' => new external_value(PARAM_RAW, 'group description'),
                            )
                        ), 'user groups', VALUE_OPTIONAL),
                    'roles' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'roleid'       => new external_value(PARAM_INT, 'role id'),
                                'name'         => new external_value(PARAM_RAW, 'role name'),
                                'shortname'    => new external_value(PARAM_ALPHANUMEXT, 'role shortname'),
                                'sortorder'    => new external_value(PARAM_INT, 'role sortorder')
                            )
                        ), 'user roles', VALUE_OPTIONAL),
                    'preferences' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'name'  => new external_value(PARAM_ALPHANUMEXT, 'The name of the preferences'),
                                'value' => new external_value(PARAM_RAW, 'The value of the custom field'),
                            )
                    ), 'User preferences', VALUE_OPTIONAL),
                    'enrolledcourses' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'id'  => new external_value(PARAM_INT, 'Id of the course'),
                                'fullname' => new external_value(PARAM_RAW, 'Fullname of the course'),
                                'shortname' => new external_value(PARAM_RAW, 'Shortname of the course')
                            )
                    ), 'Courses where the user is enrolled - limited by which courses the user is able to see', VALUE_OPTIONAL)
                )
            )
        );
    }

}

/**
 * Role functions
 */
class core_role_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function assign_roles_parameters() {
        return new external_function_parameters(
            array(
                'assignments' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'roleid'    => new external_value(PARAM_INT, 'Role to assign to the user'),
                            'userid'    => new external_value(PARAM_INT, 'The user that is going to be assigned'),
                            'contextid' => new external_value(PARAM_INT, 'The context to assign the user role in'),
                        )
                    )
                )
            )
        );
    }

    /**
     * Manual role assignments to users
     *
     * @param array $assignment  An array of manual role assignment
     * @return null
     */
    public static function assign_roles($assignments) {
        global $DB;

        // Do basic automatic PARAM checks on incoming data, using params description
        // If any problems are found then exceptions are thrown with helpful error messages
        $params = self::validate_parameters(self::assign_roles_parameters(), array('assignments'=>$assignments));

        $transaction = $DB->start_delegated_transaction();

        foreach ($params['assignments'] as $assignment) {
            // Ensure the current user is allowed to run this function in the enrolment context
            $context = get_context_instance_by_id($assignment['contextid']);
            self::validate_context($context);
            require_capability('moodle/role:assign', $context);

            // throw an exception if user is not able to assign the role in this context
            $roles = get_assignable_roles($context, ROLENAME_SHORT);

            if (!key_exists($assignment['roleid'], $roles)) {
                throw new invalid_parameter_exception('Can not assign roleid='.$assignment['roleid'].' in contextid='.$assignment['contextid']);
            }

            role_assign($assignment['roleid'], $assignment['userid'], $assignment['contextid']);
        }

        $transaction->allow_commit();
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function assign_roles_returns() {
        return null;
    }


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function unassign_roles_parameters() {
        return new external_function_parameters(
            array(
                'unassignments' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'roleid'    => new external_value(PARAM_INT, 'Role to assign to the user'),
                            'userid'    => new external_value(PARAM_INT, 'The user that is going to be assigned'),
                            'contextid' => new external_value(PARAM_INT, 'The context to unassign the user role from'),
                        )
                    )
                )
            )
        );
    }

     /**
     * Unassign roles from users
     *
     * @param array $unassignment  An array of unassignment
     * @return null
     */
    public static function unassign_roles($unassignments) {
         global $DB;

        // Do basic automatic PARAM checks on incoming data, using params description
        // If any problems are found then exceptions are thrown with helpful error messages
        $params = self::validate_parameters(self::unassign_roles_parameters(), array('unassignments'=>$unassignments));

        $transaction = $DB->start_delegated_transaction();

        foreach ($params['unassignments'] as $unassignment) {
            // Ensure the current user is allowed to run this function in the unassignment context
            $context = get_context_instance_by_id($unassignment['contextid']);
            self::validate_context($context);
            require_capability('moodle/role:assign', $context);

            // throw an exception if user is not able to unassign the role in this context
            $roles = get_assignable_roles($context, ROLENAME_SHORT);
            if (!key_exists($unassignment['roleid'], $roles)) {
                throw new invalid_parameter_exception('Can not unassign roleid='.$unassignment['roleid'].' in contextid='.$unassignment['contextid']);
            }

            role_unassign($unassignment['roleid'], $unassignment['userid'], $unassignment['contextid']);
        }

        $transaction->allow_commit();
    }

   /**
     * Returns description of method result value
     * @return null
     */
    public static function unassign_roles_returns() {
        return null;
    }
}


/**
 * Deprecated enroll and role functions
 * @deprecated since Moodle 2.2 please use core_enrol_external or core_role_external instead
 */
class moodle_enrol_external extends external_api {


    /**
     * Returns description of method parameters
     * @deprecated since Moodle 2.2 please use core_enrol_external::get_enrolled_users_parameters() instead
     * @return external_function_parameters
     */
    public static function get_enrolled_users_parameters() {
        return new external_function_parameters(
            array(
                'courseid'       => new external_value(PARAM_INT, 'Course id'),
                'withcapability' => new external_value(PARAM_CAPABILITY, 'User should have this capability', VALUE_DEFAULT, null),
                'groupid'        => new external_value(PARAM_INT, 'Group id, null means all groups', VALUE_DEFAULT, null),
                'onlyactive'     => new external_value(PARAM_INT, 'True means only active, false means all participants', VALUE_DEFAULT, 0),
            )
        );
    }

    /**
     * Get list of course participants.
     * @deprecated since Moodle 2.2 please use core_enrol_external::get_enrolled_users() instead
     * @param int $courseid
     * @param text $withcapability
     * @param int $groupid
     * @param bool $onlyactive
     * @return array of course participants
     */
    public static function get_enrolled_users($courseid, $withcapability = null, $groupid = null, $onlyactive = false) {
        global $DB, $CFG, $USER;

        // Do basic automatic PARAM checks on incoming data, using params description
        // If any problems are found then exceptions are thrown with helpful error messages
        $params = self::validate_parameters(self::get_enrolled_users_parameters(), array(
            'courseid'=>$courseid,
            'withcapability'=>$withcapability,
            'groupid'=>$groupid,
            'onlyactive'=>$onlyactive)
        );

        $coursecontext = get_context_instance(CONTEXT_COURSE, $params['courseid']);
        if ($courseid == SITEID) {
            $context = get_context_instance(CONTEXT_SYSTEM);
        } else {
            $context = $coursecontext;
        }

        try {
            self::validate_context($context);
        } catch (Exception $e) {
            $exceptionparam = new stdClass();
            $exceptionparam->message = $e->getMessage();
            $exceptionparam->courseid = $params['courseid'];
            throw new moodle_exception(get_string('errorcoursecontextnotvalid' , 'webservice', $exceptionparam));
        }

        if ($courseid == SITEID) {
            require_capability('moodle/site:viewparticipants', $context);
        } else {
            require_capability('moodle/course:viewparticipants', $context);
        }

        if ($withcapability) {
            require_capability('moodle/role:review', $coursecontext);
        }
        if ($groupid && groups_is_member($groupid)) {
            require_capability('moodle/site:accessallgroups', $coursecontext);
        }
        if ($onlyactive) {
            require_capability('moodle/course:enrolreview', $coursecontext);
        }

        list($sqlparams, $params) =  get_enrolled_sql($coursecontext, $withcapability, $groupid, $onlyactive);
        $sql = "SELECT ue.userid, e.courseid, u.firstname, u.lastname, u.username, c.id as usercontextid
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON (e.id = ue.enrolid)
                  JOIN {user} u ON (ue.userid = u.id)
                  JOIN {context} c ON (u.id = c.instanceid AND contextlevel = " . CONTEXT_USER . ")
                  WHERE e.courseid = :courseid AND ue.userid IN ($sqlparams)
                  GROUP BY ue.userid, e.courseid, u.firstname, u.lastname, u.username, c.id";
        $params['courseid'] = $courseid;
        $enrolledusers = $DB->get_records_sql($sql, $params);
        $result = array();
        $isadmin = is_siteadmin($USER);
        $canviewfullnames = has_capability('moodle/site:viewfullnames', $context);
        foreach ($enrolledusers as $enrolleduser) {
            $profilimgurl = moodle_url::make_pluginfile_url($enrolleduser->usercontextid, 'user', 'icon', NULL, '/', 'f1');
            $profilimgurlsmall = moodle_url::make_pluginfile_url($enrolleduser->usercontextid, 'user', 'icon', NULL, '/', 'f2');
            $resultuser = array(
                'courseid' => $enrolleduser->courseid,
                'userid' => $enrolleduser->userid,
                'fullname' => fullname($enrolleduser),
                'profileimgurl' => $profilimgurl->out(false),
                'profileimgurlsmall' => $profilimgurlsmall->out(false)
            );
            // check if we can return username
            if ($isadmin) {
                $resultuser['username'] = $enrolleduser->username;
            }
            // check if we can return first and last name
            if ($isadmin or $canviewfullnames) {
                $resultuser['firstname'] = $enrolleduser->firstname;
                $resultuser['lastname'] = $enrolleduser->lastname;
            }
            $result[] = $resultuser;
        }

        return $result;
    }

    /**
     * Returns description of method result value
     * @deprecated since Moodle 2.2 please use core_enrol_external::get_enrolled_users_returns() instead
     * @return external_description
     */
    public static function get_enrolled_users_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'courseid' => new external_value(PARAM_INT, 'id of course'),
                    'userid' => new external_value(PARAM_INT, 'id of user'),
                    'firstname' => new external_value(PARAM_RAW, 'first name of user', VALUE_OPTIONAL),
                    'lastname' => new external_value(PARAM_RAW, 'last name of user', VALUE_OPTIONAL),
                    'fullname' => new external_value(PARAM_RAW, 'fullname of user'),
                    'username' => new external_value(PARAM_RAW, 'username of user', VALUE_OPTIONAL),
                    'profileimgurl' => new external_value(PARAM_URL, 'url of the profile image'),
                    'profileimgurlsmall' => new external_value(PARAM_URL, 'url of the profile image (small version)')
                )
            )
        );
    }

    /**
     * Returns description of method parameters
     * @deprecated since Moodle 2.2 please use core_enrol_external::get_users_courses_parameters() instead
     * @return external_function_parameters
     */
    public static function get_users_courses_parameters() {
        return core_enrol_external::get_users_courses_parameters();
    }

    /**
     * Get list of courses user is enrolled in (only active enrolments are returned).
     *
     * Please note the current user must be able to access the course, otherwise the course is not included.
     * @deprecated since Moodle 2.2 please use core_enrol_external::get_users_courses() instead
     * @param int $userid
     * @return array of courses
     */
    public static function get_users_courses($userid) {
        return core_enrol_external::get_users_courses($userid);
    }

    /**
     * Returns description of method result value
     * @deprecated since Moodle 2.2 please use core_enrol_external::get_users_courses_returns() instead
     * @return external_description
     */
    public static function get_users_courses_returns() {
        return core_enrol_external::get_users_courses_returns();
    }


    /**
     * Returns description of method parameters
     * @deprecated since Moodle 2.2 please use core_role_external::assign_roles_parameters() instead
     * @return external_function_parameters
     */
    public static function role_assign_parameters() {
        return core_role_external::assign_roles_parameters();
    }

    /**
     * Manual role assignments to users
     * @deprecated since Moodle 2.2 please use core_role_external::assign_roles() instead
     * @param array $assignment  An array of manual role assignment
     * @return null
     */
    public static function role_assign($assignments) {
        return core_role_external::assign_roles($assignments);
    }

    /**
     * Returns description of method result value
     * @deprecated since Moodle 2.2 please use core_role_external::assign_roles_returns() instead
     * @return external_description
     */
    public static function role_assign_returns() {
        return core_role_external::assign_roles_returns();
    }


    /**
     * Returns description of method parameters
     * @deprecated since Moodle 2.2 please use core_role_external::unassign_roles_parameters() instead
     * @return external_function_parameters
     */
    public static function role_unassign_parameters() {
        return core_role_external::unassign_roles_parameters();
    }

     /**
     * Unassign roles from users
     * @deprecated since Moodle 2.2 please use core_role_external::unassign_roles() instead
     * @param array $unassignment  An array of unassignment
     * @return null
     */
    public static function role_unassign($unassignments) {
         return core_role_external::unassign_roles($unassignments);
    }

   /**
     * Returns description of method result value
    * @deprecated since Moodle 2.2 please use core_role_external::unassign_roles_returns() instead
     * @return external_description
     */
    public static function role_unassign_returns() {
        return core_role_external::unassign_roles_returns();
    }
}
