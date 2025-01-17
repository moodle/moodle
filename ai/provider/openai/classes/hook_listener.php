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

namespace aiprovider_openai;

use core_ai\hook\after_ai_provider_form_hook;

/**
 * Hook listener for Open AI Provider.
 *
 * @package    aiprovider_openai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_listener {

    /**
     * Hook listener for the Open AI instance setup form.
     *
     * @param after_ai_provider_form_hook $hook The hook to add to the AI instance setup.
     */
    public static function set_form_definition_for_aiprovider_openai(after_ai_provider_form_hook $hook): void {
        if ($hook->plugin !== 'aiprovider_openai') {
            return;
        }

        $mform = $hook->mform;

        // Required setting to store OpenAI API key.
        $mform->addElement(
            'passwordunmask',
            'apikey',
            get_string('apikey', 'aiprovider_openai'),
            ['size' => 75],
        );
        $mform->addHelpButton('apikey', 'apikey', 'aiprovider_openai');
        $mform->addRule('apikey', get_string('required'), 'required', null, 'client');

        // Setting to store OpenAI organization ID.
        $mform->addElement(
            'text',
            'orgid',
            get_string('orgid', 'aiprovider_openai'),
            ['size' => 25],
        );
        $mform->setType('orgid', PARAM_TEXT);
        $mform->addHelpButton('orgid', 'orgid', 'aiprovider_openai');

    }

}
