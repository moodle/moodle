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


class moodle_enrol_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_enrolled_users_parameters() {
        return new external_function_parameters(
            array(
                'courseid'       => new external_value(PARAM_INT, 'Course id'),
                'withcapability' => new external_value(PARAM_CAPABILITY, 'User should have this capability'),
                'groupid'        => new external_value(PARAM_INT, 'Group id, null means all groups'),
                'onlyactive'     => new external_value(PARAM_INT, 'True means only active, false means all participants'),
            )
        );
    }

    /**
     * Get list of course participants.
     *
     * @param int $courseid
     * @param text $withcapability
     * @param int $groupid
     * @param bool $onlyactive
     * @return array of course participants
     */
    public static function get_enrolled_users($courseid, $withcapability, $groupid, $onlyactive) {
        global $DB;

        // Do basic automatic PARAM checks on incoming data, using params description
        // If any problems are found then exceptions are thrown with helpful error messages
        $params = self::validate_parameters(self::get_enrolled_users_parameters(), array('courseid'=>$courseid, 'withcapability'=>$withcapability, 'groupid'=>$groupid, 'onlyactive'=>$onlyactive));

        $coursecontext = get_context_instance(CONTEXT_COURSE, $params['courseid']);
        if ($courseid == SITEID) {
            $systemcontext = get_context_instance(CONTEXT_SYSTEM);
        } else {
            $context = $coursecontext;
        }

        try {
            self::validate_context($context);
        } catch (Exception $e) {
                $exceptionparam = new stdClass();
                $exceptionparam->message = $e->getMessage();
                $exceptionparam->courseid = $params['courseid'];
                throw new moodle_exception(
                        get_string('errorcoursecontextnotvalid' , 'webservice', $exceptionparam));
        }

        if ($courseid == SITEID) {
            require_capability('moodle/site:viewparticipants', $context);
        } else {
            require_capability('moodle/course:viewparticipants', $context);
        }

        if ($withcapability) {
            require_capability('moodle/role:review', $coursecontext);
        }
        if ($groupid) {
            if (groups_is_member($groupid)) {
                require_capability('moodle/site:accessallgroups', $coursecontext);
            }
        }
        if ($onlyactive) {
            require_capability('moodle/course:enrolreview', $coursecontext);
        }

        list($sql, $params) =  get_enrolled_sql($coursecontext, $withcapability, $groupid, $onlyactive);
        $sql = "SELECT e.courseid, ue.userid
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON (e.id = ue.enrolid)
                 WHERE e.courseid = :courseid AND ue.userid IN ($sql)";
        $params['courseid'] = $courseid;

        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_enrolled_users_returns() {
        return new external_single_structure(
            array(
                'courseid' => new external_value(PARAM_INT, 'id of course'),
                'userid' => new external_value(PARAM_INT, 'id of user'),
            )
        );
    }


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function role_assign_parameters() {
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
    public static function role_assign($assignments) {
        global $DB;

        // Do basic automatic PARAM checks on incoming data, using params description
        // If any problems are found then exceptions are thrown with helpful error messages
        $params = self::validate_parameters(self::role_assign_parameters(), array('assignments'=>$assignments));

        $transaction = $DB->start_delegated_transaction();

        foreach ($params['assignments'] as $assignment) {
            // Ensure the current user is allowed to run this function in the enrolment context
            $context = get_context_instance_by_id($assignment['contextid']);
            self::validate_context($context);
            require_capability('moodle/role:assign', $context);

            role_assign($assignment['roleid'], $assignment['userid'], $assignment['contextid']);
        }

        $transaction->allow_commit();
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function role_assign_returns() {
        return null;
    }


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function role_unassign_parameters() {
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
    public static function role_unassign($unassignments) {
         global $DB;

        // Do basic automatic PARAM checks on incoming data, using params description
        // If any problems are found then exceptions are thrown with helpful error messages
        $params = self::validate_parameters(self::role_unassign_parameters(), array('unassignments'=>$unassignments));

        $transaction = $DB->start_delegated_transaction();

        foreach ($params['unassignments'] as $unassignment) {
            // Ensure the current user is allowed to run this function in the unassignment context
            $context = get_context_instance_by_id($unassignment['contextid']);
            self::validate_context($context);
            require_capability('moodle/role:assign', $context);

            role_unassign($unassignment['roleid'], $unassignment['userid'], $unassignment['contextid']);
        }

        $transaction->allow_commit();
    }

   /**
     * Returns description of method result value
     * @return external_description
     */
    public static function role_unassign_returns() {
        return null;
    }
}
