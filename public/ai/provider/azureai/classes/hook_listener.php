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

namespace aiprovider_azureai;

use core_ai\hook\after_ai_provider_form_hook;

/**
 * Hook listener for Azure AI provider.
 *
 * @package    aiprovider_azureai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_listener {

    /**
     * Hook listener for the Azure AI instance setup form.
     *
     * @param after_ai_provider_form_hook $hook The hook to add to the AI instance setup.
     */
    public static function set_form_definition_for_aiprovider_azureai(after_ai_provider_form_hook $hook): void {
        if ($hook->plugin !== 'aiprovider_azureai') {
            return;
        }

        $mform = $hook->mform;

        // Required setting to store azureai API key.
        $mform->addElement(
            'passwordunmask',
            'apikey',
            get_string('apikey', 'aiprovider_azureai'),
            ['size' => 75],
        );
        $mform->addHelpButton('apikey', 'apikey', 'aiprovider_azureai');
        $mform->addRule('apikey', get_string('required'), 'required', null, 'client');

        // Setting to store AzureAI endpoint URL.
        $mform->addElement(
            'text',
            'endpoint',
            get_string('endpoint', 'aiprovider_azureai'),
            ['size' => 25],
        );
        $mform->setType('endpoint', PARAM_URL);
        $mform->addHelpButton('endpoint', 'endpoint', 'aiprovider_azureai');
        $mform->addRule('endpoint', get_string('required'), 'required', null, 'client');
    }
}
