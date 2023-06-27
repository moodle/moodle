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
 * Used to create web service.
 */
class edwiserbridge_service_form extends moodleform {

    /**
     * Defining web services form.
     */
    public function definition() {
        global $CFG;

        $mform            = $this->_form;
        $existingservices = eb_get_existing_services();
        $authusers        = eb_get_administrators();
        $token            = isset($CFG->edwiser_bridge_last_created_token) ? $CFG->edwiser_bridge_last_created_token : ' - ';
        $service          = isset($CFG->ebexistingserviceselect) ? $CFG->ebexistingserviceselect : '';
        $tokenfield       = '';

        // 1st Field Service list
        $select = $mform->addElement(
            'select',
            'eb_sevice_list',
            get_string('existing_serice_lbl', 'local_edwiserbridge'),
            $existingservices
        );
        $mform->addHelpButton('eb_sevice_list', 'eb_mform_service_desc', 'local_edwiserbridge');
        $select->setMultiple(false);

        // 2nd Field Service input name
        $mform->addElement(
            'text',
            'eb_service_inp',
            get_string('new_service_inp_lbl', 'local_edwiserbridge'),
            array('class' => 'eb_service_field')
        );
        $mform->setType('eb_service_inp', PARAM_TEXT);

        // 3rd field Users List.
        $select = $mform->addElement(
            'select',
            'eb_auth_users_list',
            get_string('new_serivce_user_lbl', 'local_edwiserbridge'),
            $authusers,
            array('class' => '')
        );
        $select->setMultiple(false);

        $sitelang = '<div class="eb_copy_txt_wrap eb_copy_div"> <div style="width:60%;"> <b class="eb_copy" id="eb_mform_lang">'
            . $CFG->lang . '</b> </div> <div>  <button class="btn btn-primary eb_primary_copy_btn">'
            . get_string('copy', 'local_edwiserbridge') . '</button></div></div>';

        $mform->addElement(
            'static',
            'eb_mform_lang_wrap',
            get_string('lang_label', 'local_edwiserbridge'),
            $sitelang
        );
        $mform->addHelpButton('eb_mform_lang_wrap', 'eb_mform_lang_desc', 'local_edwiserbridge');

        $siteurl = '<div class="eb_copy_txt_wrap eb_copy_div"> <div style="width:60%;"> <b class="eb_copy" id="eb_mform_site_url">'
            . $CFG->wwwroot . '</b> </div> <div> <button class="btn btn-primary eb_primary_copy_btn">'
            . get_string('copy', 'local_edwiserbridge')
            . '</button></div></div>';
        // 4th field Site Url
        $mform->addElement(
            'static',
            'eb_mform_site_url_wrap',
            get_string('site_url', 'local_edwiserbridge'),
            $siteurl
        );
        $mform->addHelpButton('eb_mform_site_url_wrap', 'eb_mform_ur_desc', 'local_edwiserbridge');

        // If service is empty then show just the blank text with dash.
        $tokenfield = $token;

        if (!empty($service)) {
            // If the token available then show the token.
            $tokenfield = eb_create_token_field($service, $token);
        }

        // 5th field Token
        $mform->addElement(
            'static',
            'eb_mform_token_wrap',
            get_string('token', 'local_edwiserbridge'),
            '<b id="id_eb_token_wrap">' . $tokenfield . '</b>'
        );

        $mform->addHelpButton('eb_mform_token_wrap', 'eb_mform_token_desc', 'local_edwiserbridge');
        $mform->addElement(
            'static',
            'eb_mform_common_error',
            '',
            '<div id="eb_common_err"></div><div id="eb_common_success"></div>'
        );
        $mform->addElement('button', 'eb_mform_create_service', get_string("link", 'local_edwiserbridge'));

        if (!class_exists('webservice')) {
            require_once($CFG->dirroot . "/webservice/lib.php");
        }

        // Set default values.
        if (!empty($service)) {
            $mform->setDefault("eb_sevice_list", $service);
        }

        $mform->addElement(
            'html',
            '<div class="eb_connection_btns"><a href="'
                . $CFG->wwwroot . '/local/edwiserbridge/edwiserbridge.php?tab=connection'
                . '" class="btn btn-primary eb_setting_btn" > '
                . get_string("next", 'local_edwiserbridge')
                . '</a></div>'
        );
    }
}
