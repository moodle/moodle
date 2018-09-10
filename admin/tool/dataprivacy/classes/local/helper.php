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
    /** The default number of results to be shown per page. */
    const DEFAULT_PAGE_SIZE = 20;

    /** Filter constant associated with the request type filter. */
    const FILTER_TYPE = 1;

    /** Filter constant associated with the request status filter. */
    const FILTER_STATUS = 2;

    /** The request filters preference key. */
    const PREF_REQUEST_FILTERS = 'tool_dataprivacy_request-filters';

    /** The number of data request records per page preference key. */
    const PREF_REQUEST_PERPAGE = 'tool_dataprivacy_request-perpage';

    /**
     * Retrieves the human-readable text value of a data request type.
     *
     * @param int $requesttype The request type.
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    public static function get_request_type_string($requesttype) {
        $types = self::get_request_types();
        if (!isset($types[$requesttype])) {
            throw new moodle_exception('errorinvalidrequesttype', 'tool_dataprivacy');
        }
        return $types[$requesttype];
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
        $types = self::get_request_types_short();
        if (!isset($types[$requesttype])) {
            throw new moodle_exception('errorinvalidrequesttype', 'tool_dataprivacy');
        }
        return $types[$requesttype];
    }

    /**
     * Returns the key value-pairs of request type code and their string value.
     *
     * @return array
     */
    public static function get_request_types() {
        return [
            api::DATAREQUEST_TYPE_EXPORT => get_string('requesttypeexport', 'tool_dataprivacy'),
            api::DATAREQUEST_TYPE_DELETE => get_string('requesttypedelete', 'tool_dataprivacy'),
            api::DATAREQUEST_TYPE_OTHERS => get_string('requesttypeothers', 'tool_dataprivacy'),
        ];
    }

    /**
     * Returns the key value-pairs of request type code and their shortened string value.
     *
     * @return array
     */
    public static function get_request_types_short() {
        return [
            api::DATAREQUEST_TYPE_EXPORT => get_string('requesttypeexportshort', 'tool_dataprivacy'),
            api::DATAREQUEST_TYPE_DELETE => get_string('requesttypedeleteshort', 'tool_dataprivacy'),
            api::DATAREQUEST_TYPE_OTHERS => get_string('requesttypeothersshort', 'tool_dataprivacy'),
        ];
    }

    /**
     * Retrieves the human-readable value of a data request status.
     *
     * @param int $status The request status.
     * @return string
     * @throws moodle_exception
     */
    public static function get_request_status_string($status) {
        $statuses = self::get_request_statuses();
        if (!isset($statuses[$status])) {
            throw new moodle_exception('errorinvalidrequeststatus', 'tool_dataprivacy');
        }

        return $statuses[$status];
    }

    /**
     * Returns the key value-pairs of request status code and string value.
     *
     * @return array
     */
    public static function get_request_statuses() {
        return [
            api::DATAREQUEST_STATUS_PENDING => get_string('statuspending', 'tool_dataprivacy'),
            api::DATAREQUEST_STATUS_PREPROCESSING => get_string('statuspreprocessing', 'tool_dataprivacy'),
            api::DATAREQUEST_STATUS_AWAITING_APPROVAL => get_string('statusawaitingapproval', 'tool_dataprivacy'),
            api::DATAREQUEST_STATUS_APPROVED => get_string('statusapproved', 'tool_dataprivacy'),
            api::DATAREQUEST_STATUS_PROCESSING => get_string('statusprocessing', 'tool_dataprivacy'),
            api::DATAREQUEST_STATUS_COMPLETE => get_string('statuscomplete', 'tool_dataprivacy'),
            api::DATAREQUEST_STATUS_DOWNLOAD_READY => get_string('statusready', 'tool_dataprivacy'),
            api::DATAREQUEST_STATUS_EXPIRED => get_string('statusexpired', 'tool_dataprivacy'),
            api::DATAREQUEST_STATUS_CANCELLED => get_string('statuscancelled', 'tool_dataprivacy'),
            api::DATAREQUEST_STATUS_REJECTED => get_string('statusrejected', 'tool_dataprivacy'),
            api::DATAREQUEST_STATUS_DELETED => get_string('statusdeleted', 'tool_dataprivacy'),
        ];
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

        // The final list of users that we will return.
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

    /**
     * Get options for the data requests filter.
     *
     * @return array
     * @throws coding_exception
     */
    public static function get_request_filter_options() {
        $filters = [
            self::FILTER_TYPE => (object)[
                'name' => get_string('requesttype', 'tool_dataprivacy'),
                'options' => self::get_request_types_short()
            ],
            self::FILTER_STATUS => (object)[
                'name' => get_string('requeststatus', 'tool_dataprivacy'),
                'options' => self::get_request_statuses()
            ],
        ];
        $options = [];
        foreach ($filters as $category => $filtercategory) {
            foreach ($filtercategory->options as $key => $name) {
                $option = (object)[
                    'category' => $filtercategory->name,
                    'name' => $name
                ];
                $options["{$category}:{$key}"] = get_string('filteroption', 'tool_dataprivacy', $option);
            }
        }
        return $options;
    }
}
