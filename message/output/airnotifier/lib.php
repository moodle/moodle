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
 * Airnotifier related functions
 *
 * @package    message_airnotifier
 * @category   external
 * @copyright  2012 Jerome Mouneyrac <jerome@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.4
 */
class airnotifier_manager {

    /**
     * Include the relevant javascript and language strings for the device
     * toolbox YUI module
     *
     * @return bool
     */
    function include_device_ajax() {
        global $PAGE, $CFG;

        if (!$CFG->enableajax) {
            return false;
        }

        $config = new stdClass();

        // The URL to use for resource changes
        if (!isset($config->resturl)) {
            $config->resturl = '/message/output/airnotifier/rest.php';
        }

        // Any additional parameters which need to be included on page submission
        if (!isset($config->pageparams)) {
            $config->pageparams = array();
        }

        // Include toolboxes
        $PAGE->requires->yui_module('moodle-message_airnotifier-toolboxes', 'M.message.init_device_toolbox', array(array(
                'ajaxurl' => $config->resturl,
                'config' => $config,
                ))
        );

        // Required strings for the javascript
        $PAGE->requires->strings_for_js(array('deletecheckdevicename'), 'message_airnotifier');
        $PAGE->requires->strings_for_js(array('show','hide'), 'moodle');

        return true;
    }

    /**
     * Add device to the user device list.
     *
     * @param object $device
     * @param int $userid if empty take the current user
     * @return int device id (in Moodle database)
     */
    public function add_user_device($device, $userid = null) {
        global $DB, $USER;

        if (empty($user)) {
            $userid = $USER->id;
        }

        $device = (object) $device;

        // Check if the device token already exist - Note that we look for user in case several users use the same device
        $existingdevice = $DB->get_record('airnotifier_user_devices', array('appname' => $device->appname,
            'devicenotificationtoken' => $device->devicenotificationtoken, 'userid' => $userid));

        // trim the data (some field should be in lowercase to make the search on them quickly)
        $attributstolower = array('devicetype', 'deviceos', 'deviceosversion', 'devicebrand');
        if ($existingdevice) {
            foreach ($device as $attributname => $value) {
                if (in_array($attributname, $attributstolower)) {
                    $existingdevice->{$attributname} = strtolower($value);
                } else {
                    $existingdevice->{$attributname} = $value;
                }
            }
        } else {
            foreach ($attributstolower as $attribut) {
                if (!empty($device->{$attribut})) {
                    $device->{$attribut} = strtolower($device->{$attribut});
                }
            }
        }

        if ($existingdevice) {
            $DB->update_record('airnotifier_user_devices', $existingdevice);
            return $existingdevice->id;
        } else {
            return $DB->insert_record('airnotifier_user_devices', $device);
        }
    }

    /**
     * Return the user devices for a specific app.
     *
     * @param string $appname the app name .
     * @param int $userid if empty take the current user.
     * @param int $enabled if 1 returned only enabled devices, if 0 only disabled devices, if null all devices.
     * @return array all the devices
     */
    public function get_user_devices($appname, $userid = null, $enabled = null) {
        global $USER, $DB;

        if (empty($userid)) {
            $userid = $USER->id;
        }

        $params = array('appname' => $appname, 'userid' => $userid);
        if ($enabled !== null) {
            $params['enable'] = $enabled;
        }

        return $DB->get_records('airnotifier_user_devices', $params);
    }

}

?>
