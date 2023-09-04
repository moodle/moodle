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
 * User fields class.
 *
 * @package    auth_iomadsaml2
 * @author     Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright  2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_iomadsaml2;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/user/profile/lib.php');

/**
 * User fields class.
 *
 * @package    auth_iomadsaml2
 * @author     Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright  2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_fields {

    /**
     * A list of user matching fields from {user} table
     */
    const MATCH_FIELDS_FROM_USER_TABLE = [
        'username',
        'idnumber',
        'email',
        'alternatename',
    ];

    /**
     * A list of supported types of profile fields.
     */
    const SUPPORTED_TYPES_OF_PROFILE_FIELDS = [
        'text'
    ];

    /**
     * Prefix for profile fields in the config.
     */
    const PROFILE_FIELD_PREFIX = 'profile_field_';

    /**
     * Get a list of fields to be able to match by.
     *
     * @return string[]
     */
    public static function get_supported_fields() : array {
        $choices = [];

        foreach (self::MATCH_FIELDS_FROM_USER_TABLE as $name) {
            $choices[$name] = get_string($name);
        }

        $customfields = profile_get_custom_fields(true);

        if (!empty($customfields)) {
            $result = array_filter($customfields, function($customfield) {
                return in_array($customfield->datatype, self::SUPPORTED_TYPES_OF_PROFILE_FIELDS) &&
                    $customfield->forceunique == 1;
            });

            $customfieldoptions = array_column($result, 'name', 'shortname');

            foreach ($customfieldoptions as $key => $value) {
                $customfieldoptions[self::prefix_custom_profile_field($key)] = $value;
                unset($customfieldoptions[$key]);
            }

            $choices = array_merge($choices, $customfieldoptions);
        }

        return $choices;
    }

    /**
     * Build setting value for a  user profile field.
     *
     * @param string $shortname Short name of the profile field.
     * @return string
     */
    protected static function prefix_custom_profile_field(string $shortname) : string {
        return self::PROFILE_FIELD_PREFIX . $shortname;
    }

    /**
     * Check if provided field name is considered as profile field.
     *
     * @param string $fieldname User field name.
     * @return bool
     */
    public static function is_custom_profile_field(string $fieldname) : bool {
        return strpos($fieldname, self::PROFILE_FIELD_PREFIX) === 0;
    }

    /**
     * Get shortname from the profile field.
     *
     * @param string $fieldname Profile field name from config.
     * @return string
     */
    public static function get_field_short_name(string $fieldname) : string {
        if (self::is_custom_profile_field($fieldname)) {
            $fieldname = substr($fieldname, strlen(self::PROFILE_FIELD_PREFIX), strlen($fieldname));
        }

        return $fieldname;
    }
}
