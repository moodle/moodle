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

namespace aiprovider_openai\form;

use core_ai\form\action_settings_form;

/**
 * Generate image action provider settings form.
 *
 * @package    aiprovider_openai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action_generate_image_form extends action_settings_form {
    #[\Override]
    protected function definition() {
        $mform = $this->_form;
        $actionconfig = $this->_customdata['actionconfig']['settings'] ?? [];
        $returnurl = $this->_customdata['returnurl'] ?? null;
        $actionname = $this->_customdata['actionname'];
        $action = $this->_customdata['action'];
        $providerid = $this->_customdata['providerid'] ?? 0;

        // Action model to use.
        $mform->addElement(
            'text',
            'model',
            get_string("action:{$actionname}:model", 'aiprovider_openai'),
            'maxlength="255" size="20"',
        );
        $mform->setType('model', PARAM_TEXT);
        $mform->addRule('model', null, 'required', null, 'client');
        $mform->setDefault('model', $actionconfig['model'] ?? 'dall-e-3');
        $mform->addHelpButton('model', "action:{$actionname}:model", 'aiprovider_openai');

        // API endpoint.
        $mform->addElement(
            'text',
            'endpoint',
            get_string("action:{$actionname}:endpoint", 'aiprovider_openai'),
            'maxlength="255" size="30"',
        );
        $mform->setType('endpoint', PARAM_URL);
        $mform->addRule('endpoint', null, 'required', null, 'client');
        $mform->setDefault('endpoint', $actionconfig['endpoint'] ?? 'https://api.openai.com/v1/images/generations');

        if ($returnurl) {
            $mform->addElement('hidden', 'returnurl', $returnurl);
            $mform->setType('returnurl', PARAM_LOCALURL);
        }

        // Add the action class as a hidden field.
        $mform->addElement('hidden', 'action', $action);
        $mform->setType('action', PARAM_TEXT);

        // Add the provider class as a hidden field.
        $mform->addElement('hidden', 'provider', 'aiprovider_openai');
        $mform->setType('provider', PARAM_TEXT);

        // Add the provider id as a hidden field.
        $mform->addElement('hidden', 'providerid', $providerid);
        $mform->setType('providerid', PARAM_INT);

        $this->add_action_buttons();

        $this->set_data($actionconfig);
    }
}
