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
        global $CFG;
        require_once($CFG->dirroot . '/user/profile/lib.php');

        return array_merge(\core_user::AUTHSYNCFIELDS, ['picture', 'username'], get_profile_field_names());
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
        return array_merge(['' => $internalfields], get_profile_field_list());
    }

    /**
     * Return the list of internal fields with flat array
     *
     * Profile fields element has its array based on profile category.
     * These elements need to be turned flat to make it easier to read.
     *
     * @return array
     */
    public function get_internalfields() {
        $userfieldlist = $this->get_internalfield_list();
        $userfields = [];
        array_walk_recursive($userfieldlist,
            function($value, $key) use (&$userfields) {
                $userfields[] = $key;
            }
        );
        return $userfields;
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

}
