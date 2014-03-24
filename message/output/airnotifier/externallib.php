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
 * external API for airnotifier web services
 *
 * @package    message_airnotifier
 * @category   external
 * @copyright  2012 Jerome Mouneyrac <jerome@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.4
 */
class message_airnotifier_external extends external_api {

    /**
     * Returns description of add_user_device() parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.4
     */
    public static function add_user_device_parameters() {
        return new external_function_parameters(
            array('device' =>  new external_single_structure(
                array(
                    'appname' => new external_value( PARAM_TEXT, 'the app name'),
                    'devicename' => new external_value( PARAM_TEXT, 'Device name: "Jerome\'s iPhone"', VALUE_OPTIONAL),
                    'devicetype' => new external_value( PARAM_TEXT, 'iPhone 3GS, Google Nexus S, ...', VALUE_OPTIONAL),
                    'deviceos' => new external_value( PARAM_TEXT, 'iOS, Android, ...', VALUE_OPTIONAL),
                    'deviceosversion' => new external_value( PARAM_TEXT, 'OS version number', VALUE_OPTIONAL),
                    'devicebrand' => new external_value( PARAM_TEXT, 'the device brand (Apple, Samsung, ...)', VALUE_OPTIONAL),
                    'devicenotificationtoken' => new external_value( PARAM_RAW, 'the device token used to send notification for the specified app'),
                    'deviceuid' => new external_value( PARAM_RAW, 'the device unique device id if it exists', VALUE_OPTIONAL),
                ), 'the device information - Important: type, os, osversion and brand will be saved in lowercase for fast searching'
            )
        ));
    }

    /**
     * Add a device to the user device list
     *
     * @param array $device
     * @return int device id
     * @since Moodle 2.4
     */
    public static function add_user_device($device) {
        global $USER, $CFG;

        $params = self::validate_parameters(self::add_user_device_parameters(),
                      array('device'=>$device));

        // Ensure the current user is allowed to run this function
        $context = context_user::instance($USER->id);
        self::validate_context($context);
        require_capability('message/airnotifier:managedevice', $context);

        $device['userid'] = $USER->id;

        require_once($CFG->dirroot . "/message/output/airnotifier/lib.php");
        $airnotifiermanager = new airnotifier_manager();
        $device['id'] = $airnotifiermanager->add_user_device($device);

        return $device['id'];
    }

    /**
     * Returns description of add_user_device() result value
     *
     * @return external_single_structure
     * @since Moodle 2.4
     */
    public static function add_user_device_returns() {
        return new external_value( PARAM_INT, 'Device id in the Moodle database');
    }

    /**
     * Returns description of get_access_key() parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.4
     */
    public static function get_access_key_parameters() {
        return new external_function_parameters(
            array('permissions' =>  new external_multiple_structure(
                    new external_value( PARAM_ALPHA, 'the permission'),
                    'Allowed permissions: createtoken (not yet implemented: deletetoken, accessobjects,
                        sendnotification, sendbroadcast)',
                    VALUE_DEFAULT, array()
            )
        ));
    }

    /**
     * Get access key with specified permissions
     *
     * @param array $permissions the permission that the access key should
     * @return string access key
     * @since Moodle 2.4
     */
    public static function get_access_key($permissions = array()) {
        global $CFG;

        $params = self::validate_parameters(self::get_access_key_parameters(),
                      array('permissions'=>$permissions));

        // Check that user can use the requested permission.
        foreach ($params['permissions'] as $perm) {
            switch ($perm) {
                case 'createtoken':
                    // Any mobile device / user should have this permission.
                    // No need to check anything for this permission.

                    break;

                default:
                    throw new moodle_exception('permissionnotimplemented');
                    break;
            }
        }

        // Look for access key that have exactly the same permissions.
        // TODO: This mobile device access key should be retrieve by web service from
        //       moodle.org or airnotifer when the admin enables mobile on Moodle.
        $accesskey = $CFG->airnotifierdeviceaccesskey;

        return $accesskey;
    }

    /**
     * Returns description of add_user_device() result value
     *
     * @return external_single_structure
     * @since Moodle 2.4
     */
    public static function get_access_key_returns() {
        return new external_value( PARAM_ALPHANUMEXT, 'access key');
    }
}
