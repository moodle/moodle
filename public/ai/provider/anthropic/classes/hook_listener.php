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

namespace aiprovider_anthropic;

use core_ai\hook\after_ai_action_settings_form_hook;
use core_ai\hook\after_ai_provider_form_hook;

/**
 * Hook listener for the Anthropic Claude provider.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_listener {
    /**
     * Hook listener for the Anthropic provider instance setup form.
     *
     * Adds the API key field to the provider instance configuration form.
     *
     * @param after_ai_provider_form_hook $hook The hook to add to the AI instance setup.
     */
    public static function set_form_definition_for_aiprovider_anthropic(after_ai_provider_form_hook $hook): void {
        if ($hook->plugin !== 'aiprovider_anthropic') {
            return;
        }

        $mform = $hook->mform;

        $mform->addElement(
            'passwordunmask',
            'apikey',
            get_string('apikey', 'aiprovider_anthropic'),
            ['size' => 75],
        );
        $mform->addHelpButton('apikey', 'apikey', 'aiprovider_anthropic');
        $mform->addRule('apikey', get_string('required'), 'required', null, 'client');
    }

    /**
     * Hook listener for the Anthropic AI action settings form.
     *
     * Delegates the shared max_tokens/temperature fields to the selected model class.
     *
     * @param after_ai_action_settings_form_hook $hook The hook to add to config action settings.
     */
    public static function set_model_form_definition_for_aiprovider_anthropic(after_ai_action_settings_form_hook $hook): void {
        if ($hook->plugin !== 'aiprovider_anthropic') {
            return;
        }

        $mform = $hook->mform;
        if (!$mform->elementExists('model')) {
            return;
        }

        $model = $mform->getElementValue('model');
        if (is_array($model)) {
            $model = $model[0];
        }

        $targetmodel = helper::resolve_model($model);
        if ($targetmodel->has_model_settings()) {
            $mform->addElement('header', 'modelsettingsheader', get_string('settings', 'aiprovider_anthropic'));
            $targetmodel->add_model_settings($mform);
        }
    }
}
