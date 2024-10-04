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

namespace smsgateway_modica;

use core_sms\hook\after_sms_gateway_form_hook;

/**
 * Hook listener for Modica sms gateway.
 *
 * @package    smsgateway_modica
 * @copyright  2025 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_listener {
    /**
     * Hook listener for the sms gateway setup form.
     *
     * @param after_sms_gateway_form_hook $hook The hook to add to sms gateway setup.
     */
    public static function set_form_definition_for_modica_sms_gateway(after_sms_gateway_form_hook $hook): void {
        if ($hook->plugin !== 'smsgateway_modica') {
            return;
        }

        $mform = $hook->mform;

        $mform->addElement('static', 'information', get_string('modica_information', 'smsgateway_modica'));

        $mform->addElement(
            'text',
            'modica_url',
            get_string('modica_url', 'smsgateway_modica'),
            'maxlength="255" size="20"',
        );
        $mform->setType('modica_url', PARAM_URL);
        $mform->addRule('modica_url', get_string('maximumchars', '', 255), 'maxlength', 255);
        $mform->addRule('modica_url', null, 'required');
        $mform->setDefault(
            elementName: 'modica_url',
            defaultValue: gateway::MODICA_DEFAULT_API,
        );

        $mform->addElement(
            'text',
            'modica_application_name',
            get_string('modica_application_name', 'smsgateway_modica'),
            'maxlength="255" size="20"',
        );
        $mform->setType('modica_application_name', PARAM_TEXT);
        $mform->addRule('modica_application_name', get_string('maximumchars', '', 255), 'maxlength', 255);
        $mform->addRule('modica_application_name', null, 'required');
        $mform->setDefault(
            elementName: 'modica_application_name',
            defaultValue: '',
        );

        $mform->addElement(
            'passwordunmask',
            'modica_application_password',
            get_string('modica_application_password', 'smsgateway_modica'),
            'maxlength="255" size="20"',
        );
        $mform->setType('modica_application_password', PARAM_TEXT);
        $mform->addRule('modica_application_password', get_string('maximumchars', '', 255), 'maxlength', 255);
        $mform->addRule('modica_application_password', null, 'required');
        $mform->setDefault(
            elementName: 'modica_application_password',
            defaultValue: '',
        );
    }
}
