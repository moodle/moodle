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
 * External enrol API
 *
 * @package    moodlecore
 * @subpackage webservice
 * @copyright  2009 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/externallib.php");

class moodle_enrol_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function role_assign_parameters() {
        global $CFG;

        return new external_function_parameters(
            array(
                'enrolments' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'roleid'    => new external_value(PARAM_RAW, 'Role to assign to the user'),
                            'userid'    => new external_value(PARAM_RAW, 'The user that is going to be assigned'),
                            'contextid' => new external_value(PARAM_NOTAGS, 'The context to assign the user into '),
                            'timestart' => new external_value(PARAM_EMAIL, 'A valid and unique email address', VALUE_DEFAULT, 0, NULL_NOT_ALLOWED),
                            'timeend'   => new external_value(PARAM_SAFEDIR, 'Auth plugins include manual, ldap, imap, etc', VALUE_DEFAULT, 0, NULL_NOT_ALLOWED)
                        )
                    )
                )
            )
        );
    }

    /**
     * Assign roles to users
     *
     * @param array $enrolment  An array of enrolment
     * @return null
     */
    public static function role_assign($enrolments) {
        global $CFG, $DB;

        // Do basic automatic PARAM checks on incoming data, using params description
        // If any problems are found then exceptions are thrown with helpful error messages
        $params = self::validate_parameters(self::role_assign_parameters(), array('enrolments'=>$enrolments));

        $transaction = $DB->start_delegated_transaction();

        $success = true;

        foreach ($params['enrolments'] as $enrolment) {
            // Ensure the current user is allowed to run this function in the enrolment context
            $context = get_context_instance_by_id($enrolment['contextid']);
            self::validate_context($context);
            require_capability('moodle/role:assign', $context);

            if(!role_assign($enrolment['roleid'], $enrolment['userid'], null, $enrolment['contextid'], $enrolment['timestart'], $enrolment['timeend'])) {
                $success = false;
            }
        }

        $transaction->allow_commit();

        return $success;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function role_assign_returns() {
        return new external_value(PARAM_BOOL, 'If all assignement succeed returns true');
    }


    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function role_unassign_parameters() {
        return new external_function_parameters(
            array(
               'unenrolments' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'roleid'    => new external_value(PARAM_RAW, 'Role to assign to the user'),
                            'userid'    => new external_value(PARAM_RAW, 'The user that is going to be assigned'),
                            'contextid' => new external_value(PARAM_NOTAGS, 'The context to assign the user into '),
                            )
                    )
            )
        )
       );
    }

     /**
     * Unassign roles to users
     *
     * @param array $unenrolment  An array of unenrolment
     * @return null
     */
    public static function role_unassign($unenrolments) {
         global $CFG, $DB;

        // Do basic automatic PARAM checks on incoming data, using params description
        // If any problems are found then exceptions are thrown with helpful error messages
        $params = self::validate_parameters(self::role_unassign_parameters(), array('unenrolments'=>$unenrolments));

        $transaction = $DB->start_delegated_transaction();

        $success = true;

        foreach ($params['unenrolments'] as $unenrolment) {
            // Ensure the current user is allowed to run this function in the unenrolment context
            $context = get_context_instance_by_id($unenrolment['contextid']);
            self::validate_context($context);
            require_capability('moodle/role:assign', $context);

            if (!role_unassign($unenrolment['roleid'], $unenrolment['userid'], null, $unenrolment['contextid'])) {
                $success = false;
            }
        }

        $transaction->allow_commit();

        return $success;
    }

   /**
     * Returns description of method result value
     * @return external_description
     */
    public static function role_unassign_returns() {
        return new external_value(PARAM_BOOL, 'If all unassignement succeed returns true');
    }
   
}
