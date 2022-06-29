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
 * Class for exporting a user summary from an stdClass.
 *
 * @package    core_user
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_user\external;
defined('MOODLE_INTERNAL') || die();

use context_system;
use renderer_base;
use moodle_url;

/**
 * Class for exporting a user summary from an stdClass.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_summary_exporter extends \core\external\exporter {

    protected function get_other_values(renderer_base $output) {
        global $PAGE, $CFG;

        // Add user picture.
        $userpicture = new \user_picture($this->data);
        $userpicture->size = 1; // Size f1.
        $profileimageurl = $userpicture->get_url($PAGE)->out(false);
        $userpicture->size = 0; // Size f2.
        $profileimageurlsmall = $userpicture->get_url($PAGE)->out(false);

        $profileurl = (new moodle_url('/user/profile.php', array('id' => $this->data->id)))->out(false);

        // TODO Does not support custom user profile fields (MDL-70456).
        $identityfields = array_flip(\core_user\fields::get_identity_fields(null, false));
        $data = $this->data;
        foreach ($identityfields as $field => $index) {
            if (!empty($data->$field)) {
                $identityfields[$field] = $data->$field;
            } else {
                unset($identityfields[$field]);
            }
        }
        $identity = implode(', ', $identityfields);
        return array(
            'fullname' => fullname($this->data),
            'profileimageurl' => $profileimageurl,
            'profileimageurlsmall' => $profileimageurlsmall,
            'profileurl' => $profileurl,
            'identity' => $identity
        );
    }


    /**
     * Get the format parameters for department.
     *
     * @return array
     */
    protected function get_format_parameters_for_department() {
        return [
            'context' => context_system::instance(), // The system context is cached, so we can get it right away.
        ];
    }

    /**
     * Get the format parameters for institution.
     *
     * @return array
     */
    protected function get_format_parameters_for_institution() {
        return [
            'context' => context_system::instance(), // The system context is cached, so we can get it right away.
        ];
    }

    public static function define_properties() {
        return array(
            'id' => array(
                'type' => \core_user::get_property_type('id'),
            ),
            'email' => array(
                'type' => \core_user::get_property_type('email'),
                'default' => ''
            ),
            'idnumber' => array(
                'type' => \core_user::get_property_type('idnumber'),
                'default' => ''
            ),
            'phone1' => array(
                'type' => \core_user::get_property_type('phone1'),
                'default' => ''
            ),
            'phone2' => array(
                'type' => \core_user::get_property_type('phone2'),
                'default' => ''
            ),
            'department' => array(
                'type' => \core_user::get_property_type('department'),
                'default' => ''
            ),
            'institution' => array(
                'type' => \core_user::get_property_type('institution'),
                'default' => ''
            )
        );
    }

    public static function define_other_properties() {
        return array(
            'fullname' => array(
                'type' => PARAM_RAW
            ),
            'identity' => array(
                'type' => PARAM_RAW
            ),
            'profileurl' => array(
                'type' => PARAM_URL
            ),
            'profileimageurl' => array(
                'type' => PARAM_URL
            ),
            'profileimageurlsmall' => array(
                'type' => PARAM_URL
            ),
        );
    }
}
