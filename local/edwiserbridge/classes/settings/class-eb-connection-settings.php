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

/**
 * form shown while adding Edwiser Bridge settings.
 */
class edwiserbridge_connection_form extends moodleform {

    /**
     * Defining connection settings form.
     */
    public function definition() {
        $defaultvalues = get_connection_settings();
        $mform = $this->_form;
        $repeatarray = array();

        $repeatarray[] = $mform->createElement('header', 'wp_header', get_string('wp_site_settings_title', 'local_edwiserbridge')
        . "<div class ='eb_each_site_container'> </div>");

        $repeatarray[] = $mform->createElement(
            'text',
            'wp_name',
            get_string('wordpress_site_name', 'local_edwiserbridge'),
            'size="35"'
        );
        $repeatarray[] = $mform->createElement('text', 'wp_url', get_string('wordpress_url', 'local_edwiserbridge'), 'size="35"');
        $repeatarray[] = $mform->createElement('text', 'wp_token', get_string('wp_token', 'local_edwiserbridge'), 'size="35"');
        $repeatarray[] = $mform->createElement('hidden', 'wp_remove', 'no');

        $buttonarray = array();
        $buttonarray[] = $mform->createElement(
            'button',
            'eb_test_connection',
            get_string("wp_test_conn_btn", "local_edwiserbridge"),
            "",
            ""
        );
        $buttonarray[] = $mform->createElement(
            'button',
            'eb_remove_site',
            get_string("wp_test_remove_site", "local_edwiserbridge")
        );

        $buttonarray[] = $mform->createElement('html', '<div id="eb_test_conne_response_old"> </div>');
        $repeatarray[] = $mform->createElement("group", "eb_buttons", "", $buttonarray);
        $repeatarray[] = $mform->createElement('html', '<div id="eb_test_conne_response"> </div>');

        /*
        * Data type of each field.
        */
        $repeateloptions = array();
        $repeateloptions['wp_name']['type']   = PARAM_TEXT;
        $repeateloptions['wp_url']['type']    = PARAM_TEXT;
        $repeateloptions['wp_token']['type']  = PARAM_TEXT;
        $repeateloptions['wp_remove']['type'] = PARAM_TEXT;

        /*
        * Name of each field.
        */
        $repeateloptions['wp_name']['helpbutton']  = array("wordpress_site_name", "local_edwiserbridge");
        $repeateloptions['wp_token']['helpbutton'] = array("token", "local_edwiserbridge");
        $repeateloptions['wp_url']['helpbutton']   = array("wordpress_url", "local_edwiserbridge");

        /*
        * Adding rule for each field.
        */
        $count = 1;
        if (!empty($defaultvalues) && !empty($defaultvalues["eb_connection_settings"])) {
            $count = count($defaultvalues["eb_connection_settings"]);
            $siteno = 0;
            foreach ($defaultvalues["eb_connection_settings"] as $value) {
                $mform->setDefault("wp_name[" . $siteno . "]", $value["wp_name"]);
                $mform->setDefault("wp_url[" . $siteno . "]", $value["wp_url"]);
                $mform->setDefault("wp_token[" . $siteno . "]", $value["wp_token"]);
                $siteno++;
            }
        }

        $this->repeat_elements(
            $repeatarray,
            $count,
            $repeateloptions,
            'eb_connection_setting_repeats',
            'eb_option_add_fields',
            1,
            get_string("add_more_sites", "local_edwiserbridge"),
            true
        );

        // Closing header section.
        $mform->closeHeaderBefore('eb_option_add_fields');

        $mform->addElement(
            'html',
            '<div class="eb_connection_btns">
                <input type="submit" class="btn btn-primary eb_setting_btn" id="conne_submit" name="conne_submit"
                value="' . get_string("save", "local_edwiserbridge") . '">
                <input type="submit" class="btn btn-primary eb_setting_btn" id="conne_submit_continue" name="conne_submit_continue"
                value="' . get_string("save_cont", "local_edwiserbridge") . '">
            </div>'
        );

        // Fill form with the existing values.
    }

    /**
     * Defining connection settings form.
     *
     * @param object $data formdata.
     * @param object $files if any files uploaded.
     *
     * @return array array of errors.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $processeddata = $data;
        for ($i = count($data["wp_name"]) - 1; $i >= 0; $i--) {
            // Delete the current values from the copy of the data array.
            unset($processeddata["wp_name"][$i]);
            unset($processeddata["wp_url"][$i]);

            if (empty($data["wp_name"][$i])) {
                $errors['wp_name[' . $i . ']'] = get_string('required', 'local_edwiserbridge');
            } else if (in_array($data["wp_name"][$i], $processeddata["wp_name"])) {
                // Checking if the current name value exitsts in array.
                $errors['wp_name[' . $i . ']'] = get_string('sitename-duplicate-value', 'local_edwiserbridge');
            }

            if (empty($data["wp_url"][$i])) {
                $errors['wp_url[' . $i . ']'] = get_string('required', 'local_edwiserbridge');
            } else if (in_array($data["wp_url"][$i], $processeddata["wp_url"])) {
                // Checking if the current URL value exitsts in array.
                $errors['wp_url[' . $i . ']'] = get_string('url-duplicate-value', 'local_edwiserbridge');
            }

            if (empty($data["wp_token"][$i])) {
                $errors['wp_token[' . $i . ']'] = get_string('required', 'local_edwiserbridge');
            }

            // If the site settings is removed then remove the validation errors also.
            if (
                isset($errors['wp_name[' . $i . ']']) &&
                isset($errors['wp_url[' . $i . ']']) &&
                isset($errors['wp_token[' . $i . ']']) &&
                isset($data['wp_remove'][$i]) &&
                'yes' == $data['wp_remove'][$i]
            ) {
                unset($errors['wp_name[' . $i . ']']);
                unset($errors['wp_url[' . $i . ']']);
                unset($errors['wp_token[' . $i . ']']);
            }
        }
        return $errors;
    }
}
