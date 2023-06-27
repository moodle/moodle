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
 * Provides local_edwiserbridge\external\course_progress_data trait.
 *
 * @package     local_edwiserbridge
 * @category    external
 * @copyright   2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author      Wisdmlabs
 */

namespace local_edwiserbridge\external;

defined('MOODLE_INTERNAL') || die();

use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use core_completion\progress;

// require_once($CFG->libdir.'/externallib.php');

/**
 * Trait implementing the external function local_edwiserbridge_course_progress_data
 */
trait eb_get_users {

    /**
     * functionality to get users in chunk.
     * @param  int $offset offset
     * @param  int $limit  limit
     * @param  string $searchstring searchstring
     * @param  int $totalusers totalusers
     * @return array array of users.
     */
    public static function eb_get_users($offset, $limit, $searchstring, $totalusers) {
        global $DB;

        $params = self::validate_parameters(
            self::eb_get_users_parameters(),
            array('offset' => $offset, "limit" => $limit, "search_string" => $searchstring, "total_users" => $totalusers)
        );

        $query = "SELECT id, username, firstname, lastname, email FROM {user} WHERE
        deleted = 0 AND confirmed = 1 AND username != 'guest' ";

        if (!empty($params['search_string'])) {
            $searchstring = "%" . $params['search_string'] . "%";
            $query .= " AND (firstname LIKE '$searchstring' OR lastname LIKE '$searchstring' OR username LIKE '$searchstring')";
        }

        $users = $DB->get_records_sql($query, null, $offset, $limit);
        $usercount = 0;
        if (!empty($params['total_users'])) {
            $usercount = $DB->get_record_sql("SELECT count(*) total_count FROM {user} WHERE
            deleted = 0 AND confirmed = 1 AND username != 'guest' ");
            $usercount = $usercount->total_count;
        }

        return array("total_users" => $usercount, "users" => $users);
    }

    /**
     * paramters defined for get users function.
     */
    public static function eb_get_users_parameters() {
        return new external_function_parameters(
            array(
                'offset'        => new external_value(
                    PARAM_INT,
                    get_string('web_service_offset', 'local_edwiserbridge')
                ),
                'limit'         => new external_value(
                    PARAM_INT,
                    get_string('web_service_limit', 'local_edwiserbridge')
                ),
                'search_string' => new external_value(
                    PARAM_TEXT,
                    get_string('web_service_search_string', 'local_edwiserbridge')
                ),
                'total_users'   => new external_value(
                    PARAM_INT,
                    get_string('web_service_total_users', 'local_edwiserbridge')
                ),
            )
        );
    }

    /**
     * paramters which will be returned from get users function.
     */
    public static function eb_get_users_returns() {
        return new external_function_parameters(
            array(
                'total_users' => new external_value(PARAM_INT, ''),
                'users' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id'        => new external_value(
                                PARAM_INT,
                                get_string('web_service_id', 'local_edwiserbridge')
                            ),
                            'username'  => new external_value(
                                PARAM_TEXT,
                                get_string('web_service_username', 'local_edwiserbridge')
                            ),
                            'firstname' => new external_value(
                                PARAM_TEXT,
                                get_string('web_service_firstname', 'local_edwiserbridge')
                            ),
                            'lastname'  => new external_value(
                                PARAM_TEXT,
                                get_string('web_service_lastname', 'local_edwiserbridge')
                            ),
                            'email'     => new external_value(
                                PARAM_TEXT,
                                get_string('web_service_email', 'local_edwiserbridge')
                            )
                        )
                    )
                )
            )
        );
    }
}
