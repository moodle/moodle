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

namespace report_lsusql\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

/**
 * Web service used by form autocomplete to get a list of users with a given capability.
 *
 * @package   report_lsusql
 * @copyright 2020 The Open University
 * @copyright 2022 Louisiana State University
 * @copyright 2022 Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_users extends \external_api {
    /**
     * Parameter declaration.
     *
     * @return \external_function_parameters Parameters
     */
    public static function execute_parameters(): \external_function_parameters {
        return new \external_function_parameters([
            'query' => new \external_value(PARAM_RAW, 'Contents of the search box.'),
            'capability' => new \external_value(PARAM_CAPABILITY, 'Return only users with this capability in the system context.'),
        ]);
    }

    /**
     * Get a list of users with the given capability at system level.
     *
     * @param string $query Content of the autocomplete search box.
     * @param string $capability Return only users with this capability in the system context.
     *
     * @return array Of data about users.
     */
    public static function execute(string $query, string $capability): array {
        global $CFG, $DB;

        [$query, $capability] = array_values(self::validate_parameters(self::execute_parameters(),
                ['query' => $query, 'capability' => $capability]));

        $context = \context_system::instance();
        self::validate_context($context);
        require_capability('report/lsusql:definequeries', $context);

        if (class_exists('\core_user\fields')) {
            $extrafields = \core_user\fields::for_identity($context, false)->get_required_fields();
            $fields = \core_user\fields::for_identity($context,
                    false)->with_userpic()->get_sql('u', false, '', '', false)->selects;
        } else {
            $extrafields = get_extra_user_fields($context);
            $fields = \user_picture::fields('u', $extrafields);
        }

        $withcapabilityjoin = get_with_capability_join($context, $capability, 'u.id');
        [$wherecondition, $whereparams] = users_search_sql($query, 'u', true, $extrafields);
        [$sort, $sortparams] = users_order_by_sql('u', $query, $context);

        $users = $DB->get_records_sql("
                SELECT $fields
                  FROM {user} u

                  JOIN (

                     SELECT id
                      FROM {user} u
                      $withcapabilityjoin->joins
                     WHERE $withcapabilityjoin->wheres

                     UNION

                     SELECT id
                      FROM {user} u
                     WHERE u.id IN ($CFG->siteadmins)

                     ) userids ON userids.id = u.id

                 WHERE $wherecondition

                 ORDER BY $sort
             ", array_merge($whereparams, $withcapabilityjoin->params, $sortparams));

        $results = [];
        foreach ($users as $user) {
            $results[] = self::prepare_result_object($user, $extrafields);
        }

        return $results;
    }

    /**
     * Convert a user object into the form required to return.
     *
     * @param \stdClass $user data on a user.
     * @param array $extrafields extra profile fields to show.
     * @return \stdClass return object.
     */
    public static function prepare_result_object(\stdClass $user, array $extrafields): \stdClass {
        global $PAGE;
        $userpicture = new \user_picture($user);
        $userpicture->size = 0; // Size f2.

        $identity = [];
        foreach ($extrafields as $field) {
            if ($user->$field) {
                $identity[] = $user->$field;
            }
        }
        $identity = implode(', ', $identity);

        return (object) [
                'id' => $user->id,
                'fullname' => fullname($user),
                'identity' => $identity,
                'hasidentity' => (bool) $identity,
                'profileimageurlsmall' => $userpicture->get_url($PAGE)->out(false),
        ];
    }

    /**
     * Returns type for declaration.
     *
     * @return \external_description Result type
     */
    public static function execute_returns(): \external_description {
        return new \external_multiple_structure(
            new \external_single_structure([
                'id' => new \external_value(PARAM_INT, 'User id.'),
                'fullname' => new \external_value(PARAM_RAW, 'User full name.'),
                'identity' => new \external_value(PARAM_RAW, 'Additional user identifying info.'),
                'hasidentity' => new \external_value(PARAM_BOOL, 'Whether identity is non-blank.'),
                'profileimageurlsmall' => new \external_value(PARAM_RAW, 'URL of the user profile image.'),
            ]));
    }
}
