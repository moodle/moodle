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

namespace smsgateway_aws;

use core_sms\hook\after_sms_gateway_form_hook;

/**
 * Hook listener for aws sms gateway.
 *
 * @package    smsgateway_aws
 * @copyright  2024 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_listener {

    /**
     * Hook listener for the sms gateway setup form.
     *
     * @param after_sms_gateway_form_hook $hook The hook to add to sms gateway setup.
     */
    public static function set_form_definition_for_aws_sms_gateway(after_sms_gateway_form_hook $hook): void {
        if ($hook->plugin !== 'smsgateway_aws') {
            return;
        }

        $mform = $hook->mform;

        $mform->addElement('static', 'information', get_string('aws_information', 'smsgateway_aws'));

        $gateways = [
            'aws_sns' => get_string('aws_sns', 'smsgateway_aws'),
        ];
        $mform->addElement(
            'select',
            'gateway',
            get_string('gateway', 'smsgateway_aws'),
            $gateways,
        );
        $mform->setDefault(
            elementName: 'gateway',
            defaultValue: 'aws_sns',
        );
        // Remove this if more aws gateway implemented, eg sqs.
        $mform->hardFreeze('gateway');

        $mform->addElement(
            'checkbox',
            'usecredchain',
            get_string('usecredchain', 'smsgateway_aws'),
            ' ',
        );
        $mform->setDefault(
            elementName: 'usecredchain',
            defaultValue: 0,
        );

        $mform->addElement(
            'text',
            'api_key',
            get_string('api_key', 'smsgateway_aws'),
            'maxlength="255" size="20"',
        );
        $mform->setType('api_key', PARAM_TEXT);
        $mform->addRule('api_key', get_string('maximumchars', '', 255), 'maxlength', 255);
        $mform->setDefault(
            elementName: 'api_key',
            defaultValue: '',
        );
        $mform->addElement(
            'passwordunmask',
            'api_secret',
            get_string('api_secret', 'smsgateway_aws'),
            'maxlength="255" size="20"',
        );
        $mform->setType('api_secret', PARAM_TEXT);
        $mform->addRule('api_secret', get_string('maximumchars', '', 255), 'maxlength', 255);
        $mform->setDefault(
            elementName: 'api_secret',
            defaultValue: '',
        );

        $mform->addElement(
            'text',
            'api_region',
            get_string('api_region', 'smsgateway_aws'),
            'maxlength="255" size="20"',
        );
        $mform->setType('api_region', PARAM_TEXT);
        $mform->addRule('api_region', get_string('maximumchars', '', 255), 'maxlength', 255);
        $mform->setDefault(
            elementName: 'api_region',
            defaultValue: 'ap-southeast-2',
        );
    }

}
