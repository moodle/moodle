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
 * The class responsible for retrieving a user based on identifier.
 *
 * @package    auth_iomadsaml2
 * @author     Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright  2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_iomadsaml2;

/**
 * The class responsible for retrieving a user based on identifier.
 *
 * @package    auth_iomadsaml2
 * @author     Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright  2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_extractor {

    /**
     * Get extracted from DB user.
     *
     * @param string $fieldname Field name to search by.
     * @param string $fieldvalue Field value to search by.
     * @param bool $insensitive Whether to use case insensitive match.
     * @param bool $accentsensitive Whether to use accent sensitive match.
     *
     * @return mixed False, or A {$USER} object.
     */
    public static function get_user(
        string $fieldname,
        string $fieldvalue,
        bool $insensitive = false,
        bool $accentsensitive = true
    ) {
        global $DB, $CFG;

        $user = false;
        $joins = '';
        $params['fieldvalue'] = $fieldvalue;
        $params['mnethostid'] = $CFG->mnet_localhost_id;

        if (user_fields::is_custom_profile_field($fieldname)) {

            $fieldname = user_fields::get_field_short_name($fieldname);

            $joins = " LEFT JOIN {user_info_field} f ON f.shortname = :fieldname ";
            $joins .= " LEFT JOIN {user_info_data} d ON d.fieldid = f.id AND d.userid = u.id ";

            $fieldsql = " AND " . $DB->sql_equal('d.data', ':fieldvalue', !$insensitive, $accentsensitive);
            $params['fieldname'] = $fieldname;

        } else {
            // Check if requested field exists, required for Totara compatibility.
            $fields = array_merge(\core_user::AUTHSYNCFIELDS, ['id', 'username']);
            if (in_array($fieldname, $fields)) {
                $fieldsql = " AND " . $DB->sql_equal('u.' . $fieldname, ':fieldvalue', !$insensitive, $accentsensitive);
                $params['fieldname'] = $fieldname;
            }
        }

        if (!empty($fieldsql)) {
            $sql = "SELECT u.id
                      FROM {user} u $joins
                     WHERE u.deleted <> 1
                       AND u.mnethostid = :mnethostid $fieldsql";

            if ($records = $DB->get_records_sql($sql, $params)) {
                if (count($records) == 1) {
                    $record = reset($records);
                    $user = get_complete_user_data('id', $record->id);
                }
            }
        }
        return $user;
    }
}
