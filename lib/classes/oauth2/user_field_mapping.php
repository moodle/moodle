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
 * Class for loading/storing oauth2 endpoints from the DB.
 *
 * @package    core
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\oauth2;

defined('MOODLE_INTERNAL') || die();

use core\persistent;
use lang_string;
/**
 * Class for loading/storing oauth2 user field mappings from the DB
 *
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_field_mapping extends persistent {

    const TABLE = 'oauth2_user_field_mapping';

    /**
     * Return the list of valid internal user fields.
     *
     * @return array
     */
    private static function get_user_fields() {
        return array_merge(\core_user::AUTHSYNCFIELDS, ['picture', 'username'], self::get_profile_field_names());
    }

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'issuerid' => array(
                'type' => PARAM_INT
            ),
            'externalfield' => array(
                'type' => PARAM_RAW_TRIMMED,
            ),
            'internalfield' => array(
                'type' => PARAM_ALPHANUMEXT,
                'choices' => self::get_user_fields()
            )
        );
    }

    /**
     * Return the list of internal fields
     * in a format they can be used for choices in a select menu
     * @return array
     */
    public function get_internalfield_list() {
        $userfields = array_merge(\core_user::AUTHSYNCFIELDS, ['picture', 'username']);
        $internalfields = array_combine($userfields, $userfields);
        return array_merge(['' => $internalfields], self::get_profile_field_list());
    }

    /**
     * Ensures that no HTML is saved to externalfield field
     * but preserves all special characters that can be a part of the claim
     * @return boolean true if validation is successful, string error if externalfield is not validated
     */
    protected function validate_externalfield($value){
        // This parameter type is set to PARAM_RAW_TRIMMED and HTML check is done here.
        if (clean_param($value, PARAM_NOTAGS) !== $value){
            return new lang_string('userfieldexternalfield_error', 'tool_oauth2');
        }
        return true;
    }

    /**
     * Return the list of valid custom profile user fields.
     *
     * @return array array of profile field names
     */
    private static function get_profile_field_names(): array {
        $profilefields = profile_get_user_fields_with_data(0);
        $profilefieldnames = [];
        foreach ($profilefields as $field) {
            $profilefieldnames[] = $field->inputname;
        }
        return $profilefieldnames;
    }

    /**
     * Return the list of profile fields
     * in a format they can be used for choices in a group select menu.
     *
     * @return array array of category name with its profile fields
     */
    private function get_profile_field_list(): array {
        $customfields = profile_get_user_fields_with_data_by_category(0);
        $data = [];
        foreach ($customfields as $category) {
            foreach ($category as $field) {
                $categoryname = $field->get_category_name();
                if (!isset($data[$categoryname])) {
                    $data[$categoryname] = [];
                }
                $data[$categoryname][$field->inputname] = $field->field->name;
            }
        }
        return $data;
    }
}
