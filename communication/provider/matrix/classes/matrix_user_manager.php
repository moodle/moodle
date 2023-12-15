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

namespace communication_matrix;

/**
 * class matrix_user_manager to handle specific actions.
 *
 * @package    communication_matrix
 * @copyright  2023 Stevani Andolo <stevani.andolo@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class matrix_user_manager {
    /**
     * Gets matrix user id from moodle.
     *
     * @param int $userid Moodle user id
     * @return string|null
     */
    public static function get_matrixid_from_moodle(
        int $userid,
    ): ?string {
        self::load_requirements();
        $field = profile_user_record($userid);
        $matrixprofilefield = get_config('communication_matrix', 'matrixuserid_field');

        if ($matrixprofilefield === false) {
            return null;
        }

        return $field->{$matrixprofilefield} ?? null;
    }

    /**
     * Get a qualified matrix user id based on a Moodle username.
     *
     * @param string $username The moodle username to turn into a Matrix username
     * @return string
     */
    public static function get_formatted_matrix_userid(
        string $username,
    ): string {
        $username = preg_replace('/[@#$%^&*()+{}|<>?!,]/i', '.', $username);
        $username = ltrim(rtrim($username, '.'), '.');

        $homeserver = self::get_formatted_matrix_home_server();

        return "@{$username}:{$homeserver}";
    }

    /**
     * Add user's Matrix user id.
     *
     * @param int $userid Moodle user id
     * @param string $matrixuserid Matrix user id
     */
    public static function set_matrix_userid_in_moodle(
        int $userid,
        string $matrixuserid,
    ): void {
        $matrixprofilefield = self::get_profile_field_name();
        $field = profile_get_custom_field_data_by_shortname($matrixprofilefield);

        if ($field === null) {
            return;
        }
        $userinfodata = (object) [
            'id' => $userid,
            'data' => $matrixuserid,
            'fieldid' => $field->id,
            "profile_field_{$matrixprofilefield}" => $matrixuserid,
        ];
        profile_save_data($userinfodata);
    }

    /**
     * Sets home server for user matrix id
     *
     * @return string
     */
    public static function get_formatted_matrix_home_server(): string {
        $homeserver = get_config('communication_matrix', 'matrixhomeserverurl');
        if ($homeserver === false) {
            throw new \moodle_exception('Unknown matrix homeserver url');
        }

        $homeserver = parse_url($homeserver)['host'];

        if (str_starts_with($homeserver, 'www.')) {
            $homeserver = str_replace('www.', '', $homeserver);
        }

        return $homeserver;
    }

    /**
     * Insert "Communication" category and "matrixuserid" field.
     *
     * @return string
     */
    public static function create_matrix_user_profile_fields(): string {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/user/profile/definelib.php');
        require_once($CFG->dirroot . '/user/profile/field/text/define.class.php');

        // Check if communication category exists.
        $categoryname = get_string('communication', 'core_communication');
        $category = $DB->count_records('user_info_category', ['name' => $categoryname]);

        if ($category < 1) {
            $data = new \stdClass();
            $data->sortorder = $DB->count_records('user_info_category') + 1;
            $data->name = $categoryname;
            $data->id = $DB->insert_record('user_info_category', $data);

            $createdcategory = $DB->get_record('user_info_category', ['id' => $data->id]);
            $categoryid = $createdcategory->id;
            \core\event\user_info_category_created::create_from_category($createdcategory)->trigger();
        } else {
            $category = $DB->get_record('user_info_category', ['name' => $categoryname]);
            $categoryid = $category->id;
        }

        set_config('communication_category_field', $categoryname, 'core_communication');

        // Check if matrixuserid exists in user_info_field table.
        $matrixuserid = $DB->count_records('user_info_field', [
            'shortname' => 'matrixuserid',
            'categoryid' => $categoryid,
        ]);

        if ($matrixuserid < 1) {
            $profileclass = new \profile_define_text();

            $data = (object) [
                'shortname' => 'matrixuserid',
                'name' => get_string('matrixuserid', 'communication_matrix'),
                'datatype' => 'text',
                'categoryid' => $categoryid,
                'forceunique' => 1,
                'visible' => 0,
                'locked' => 1,
                'param1' => 30,
                'param2' => 2048,
            ];

            $profileclass->define_save($data);
            set_config('matrixuserid_field', 'matrixuserid', 'communication_matrix');
            return 'matrixuserid';
        }
    }

    /**
     * Get the profile field name, creating the profiel field if it does not exist.
     *
     * @return string
     */
    protected static function get_profile_field_name(): string {
        self::load_requirements();
        $matrixprofilefield = get_config('communication_matrix', 'matrixuserid_field');
        if ($matrixprofilefield === false) {
            $matrixprofilefield = self::create_matrix_user_profile_fields();
        }

        return $matrixprofilefield;
    }

    /**
     * Load requirements for profile field management.
     *
     * This is just a helper to keep loading legacy files isolated.
     */
    protected static function load_requirements(): void {
        global $CFG;

        require_once("{$CFG->dirroot}/user/profile/lib.php");
    }
}
