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
 * Settings mod form
 *
 * @package     local_edwiserbridge
 * @copyright   2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author      Wisdmlabs
 */

defined('MOODLE_INTERNAL') || die();
require_once("$CFG->libdir/formslib.php");
require_once(dirname(__FILE__) . '/classes/settings/class-eb-connection-settings.php');
require_once(dirname(__FILE__) . '/classes/settings/class-eb-navigation.php');
require_once(dirname(__FILE__) . '/classes/settings/class-eb-service-settings.php');
require_once(dirname(__FILE__) . '/classes/settings/class-eb-summary.php');
require_once(dirname(__FILE__) . '/classes/settings/class-eb-synchronization-settings.php');

/**
 * Used to create web service.
 */
class edwiserbridge_settings_form extends moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        global $CFG;
        $mform         = $this->_form;
        $defaultvalues = get_required_settings();

        // 1st field.
        $mform->addElement(
            'advcheckbox',
            'rest_protocol',
            get_string('web_rest_protocol_cb', 'local_edwiserbridge'),
            get_string("web_rest_protocol_cb_desc", "local_edwiserbridge"),
            array('group' => 1),
            array(0, 1)
        );

        // 2nd field.
        $mform->addElement(
            'advcheckbox',
            'web_service',
            get_string('web_service_cb', 'local_edwiserbridge'),
            get_string("web_service_cb_desc", "local_edwiserbridge"),
            array('group' => 1),
            array(0, 1)
        );

        // 3rd field.
        $mform->addElement(
            'advcheckbox',
            'pass_policy',
            get_string('password_policy_cb', 'local_edwiserbridge'),
            get_string("password_policy_cb_desc", "local_edwiserbridge"),
            array('group' => 1),
            array(0, 1)
        );

        // 4th field.
        $mform->addElement(
            'advcheckbox',
            'extended_username',
            get_string(
                'extended_char_username_cb',
                'local_edwiserbridge'
            ),
            get_string("extended_char_username_cb_desc", "local_edwiserbridge"),
            array('group' => 1),
            array(0, 1)
        );

        // Fill form with the existing values.
        if (!empty($defaultvalues)) {
            $mform->setDefault("rest_protocol", $defaultvalues["rest_protocol"]);
            $mform->setDefault("web_service", $defaultvalues["web_service"]);
            $mform->setDefault("pass_policy", $defaultvalues["pass_policy"]);
            $mform->setDefault("extended_username", $defaultvalues["extended_username"]);
        }

        $mform->addElement(
            'html',
            '<div class="eb_connection_btns">
				<input type="submit" class="btn btn-primary eb_setting_btn" id="settings_submit"
                name="settings_submit" value="' . get_string("save", "local_edwiserbridge") . '">
				<input type="submit" class="btn btn-primary eb_setting_btn" id="settings_submit_continue"
                name="settings_submit_continue" value="' . get_string("save_cont", "local_edwiserbridge") . '">
			</div>'
        );
    }

    /**
     * Validate form data.
     *
     * @param array $data  Submitted data
     * @param array $files Submitted files
     *
     * @return void
     */
    public function validation($data, $files) {
        return array();
    }
}
