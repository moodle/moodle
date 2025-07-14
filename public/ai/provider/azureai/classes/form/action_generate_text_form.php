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

namespace aiprovider_azureai\form;

use core_ai\form\action_settings_form;

/**
 * Generate text action provider settings form.
 *
 * @package    aiprovider_azureai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action_generate_text_form extends action_settings_form {
    #[\Override]
    protected function definition() {
        $mform = $this->_form;
        $actionconfig = $this->_customdata['actionconfig']['settings'] ?? [];
        $returnurl = $this->_customdata['returnurl'] ?? null;
        $actionname = $this->_customdata['actionname'];
        $action = $this->_customdata['action'];
        $providerid = $this->_customdata['providerid'] ?? 0;

        // Add API deployment name.
        $mform->addElement(
            'text',
            'deployment',
            get_string("action:{$actionname}:deployment", 'aiprovider_azureai'),
            'maxlength="255" size="20"',
        );
        $mform->setType('deployment', PARAM_ALPHANUMEXT);
        $mform->addRule('deployment', null, 'required', null, 'client');
        $mform->setDefault('deployment', $actionconfig['deployment'] ?? '');
        $mform->addHelpButton('deployment', "action:{$actionname}:deployment", 'aiprovider_azureai');

        // Add API version.
        $mform->addElement(
            'text',
            'apiversion',
            get_string("action:{$actionname}:apiversion", 'aiprovider_azureai'),
            'maxlength="255" size="30"',
        );
        $mform->setType('apiversion', PARAM_ALPHANUMEXT);
        $mform->addRule('apiversion', null, 'required', null, 'client');
        $mform->setDefault('apiversion', $actionconfig['apiversion'] ?? '2024-06-01');

        // System Instructions.
        $mform->addElement(
            'textarea',
            'systeminstruction',
            get_string("action:{$actionname}:systeminstruction", 'aiprovider_azureai'),
            'wrap="virtual" rows="5" cols="20"',
        );
        $mform->setType('systeminstruction', PARAM_TEXT);
        $mform->setDefault('systeminstruction', $actionconfig['systeminstruction'] ?? $action::get_system_instruction());
        $mform->addHelpButton('systeminstruction', "action:{$actionname}:systeminstruction", 'aiprovider_azureai');

        if ($returnurl) {
            $mform->addElement('hidden', 'returnurl', $returnurl);
            $mform->setType('returnurl', PARAM_LOCALURL);
        }

        // Add the action class as a hidden field.
        $mform->addElement('hidden', 'action', $action);
        $mform->setType('action', PARAM_TEXT);

        // Add the provider class as a hidden field.
        $mform->addElement('hidden', 'provider', 'aiprovider_azureai');
        $mform->setType('provider', PARAM_TEXT);

        // Add the provider id as a hidden field.
        $mform->addElement('hidden', 'providerid', $providerid);
        $mform->setType('providerid', PARAM_INT);

        $this->set_data($actionconfig);
    }
}
