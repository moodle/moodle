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
 * Collection of helper functions for the data privacy tool.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy\local;
defined('MOODLE_INTERNAL') || die();

use coding_exception;
use moodle_exception;
use tool_dataprivacy\api;

/**
 * Class containing helper functions for the data privacy tool.
 *
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Retrieves the human-readable text value of a data request type.
     *
     * @param int $requesttype The request type.
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    public static function get_request_type_string($requesttype) {
        switch ($requesttype) {
            case api::DATAREQUEST_TYPE_EXPORT:
                return get_string('requesttypeexport', 'tool_dataprivacy');
            case api::DATAREQUEST_TYPE_DELETE:
                return get_string('requesttypedelete', 'tool_dataprivacy');
            case api::DATAREQUEST_TYPE_OTHERS:
                return get_string('requesttypeothers', 'tool_dataprivacy');
            default:
                throw new moodle_exception('errorinvalidrequesttype', 'tool_dataprivacy');
        }
    }

    /**
     * Retrieves the human-readable shortened text value of a data request type.
     *
     * @param int $requesttype The request type.
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    public static function get_shortened_request_type_string($requesttype) {
        switch ($requesttype) {
            case api::DATAREQUEST_TYPE_EXPORT:
                return get_string('requesttypeexportshort', 'tool_dataprivacy');
            case api::DATAREQUEST_TYPE_DELETE:
                return get_string('requesttypedeleteshort', 'tool_dataprivacy');
            case api::DATAREQUEST_TYPE_OTHERS:
                return get_string('requesttypeothersshort', 'tool_dataprivacy');
            default:
                throw new moodle_exception('errorinvalidrequesttype', 'tool_dataprivacy');
        }
    }

    /**
     * Retrieves the human-readable value of a data request status.
     *
     * @param int $status The request status.
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    public static function get_request_status_string($status) {
        switch ($status) {
            case api::DATAREQUEST_STATUS_PENDING:
                return get_string('statuspending', 'tool_dataprivacy');
            case api::DATAREQUEST_STATUS_PREPROCESSING:
                return get_string('statuspreprocessing', 'tool_dataprivacy');
            case api::DATAREQUEST_STATUS_AWAITING_APPROVAL:
                return get_string('statusawaitingapproval', 'tool_dataprivacy');
            case api::DATAREQUEST_STATUS_APPROVED:
                return get_string('statusapproved', 'tool_dataprivacy');
            case api::DATAREQUEST_STATUS_PROCESSING:
                return get_string('statusprocessing', 'tool_dataprivacy');
            case api::DATAREQUEST_STATUS_COMPLETE:
                return get_string('statuscomplete', 'tool_dataprivacy');
            case api::DATAREQUEST_STATUS_CANCELLED:
                return get_string('statuscancelled', 'tool_dataprivacy');
            case api::DATAREQUEST_STATUS_REJECTED:
                return get_string('statusrejected', 'tool_dataprivacy');
            default:
                throw new moodle_exception('errorinvalidrequeststatus', 'tool_dataprivacy');
        }
    }

    /**
     * Get the users that a user can make data request for.
     *
     * E.g. User having a parent role and has the 'tool/dataprivacy:makedatarequestsforchildren' capability.
     * @param int $userid The user's ID.
     * @return array
     */
    public static function get_children_of_user($userid) {
        global $DB;

        // Get users that the user has role assignments to.
        $allusernames = get_all_user_name_fields(true, 'u');
        $sql = "SELECT u.id, $allusernames
                  FROM {role_assignments} ra, {context} c, {user} u
                 WHERE ra.userid = :userid
                       AND ra.contextid = c.id
                       AND c.instanceid = u.id
                       AND c.contextlevel = :contextlevel";
        $params = [
            'userid' => $userid,
            'contextlevel' => CONTEXT_USER
        ];

        // The final list of users that we will return;
        $finalresults = [];

        // Our prospective list of users.
        if ($candidates = $DB->get_records_sql($sql, $params)) {
            foreach ($candidates as $key => $child) {
                $childcontext = \context_user::instance($child->id);
                if (has_capability('tool/dataprivacy:makedatarequestsforchildren', $childcontext, $userid)) {
                    $finalresults[$key] = $child;
                }
            }
        }
        return $finalresults;
    }
}
