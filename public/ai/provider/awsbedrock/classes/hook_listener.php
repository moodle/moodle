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

namespace aiprovider_awsbedrock;

use core\output\html_writer;
use core_ai\hook\after_ai_action_settings_form_hook;
use core_ai\hook\after_ai_provider_form_hook;

/**
 * Hook listener for AWS Bedrock Provider.
 *
 * @package    aiprovider_awsbedrock
 * @copyright  2025 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_listener {
    /**
     * Hook listener for the Bedrock AI instance setup form.
     *
     * @param after_ai_provider_form_hook $hook The hook to add to the AI instance setup.
     */
    public static function set_form_definition_for_aiprovider_awsbedrock(after_ai_provider_form_hook $hook): void {
        if ($hook->plugin !== 'aiprovider_awsbedrock') {
            return;
        }

        $mform = $hook->mform;
        // Required setting to store AWS API key.
        $mform->addElement(
            'text',
            'apikey',
            get_string('apikey', 'aiprovider_awsbedrock'),
            ['size' => 32],
        );
        $mform->setType('apikey', PARAM_TEXT);
        $mform->addHelpButton('apikey', 'apikey', 'aiprovider_awsbedrock');
        $mform->addRule('apikey', get_string('required'), 'required', null, 'client');

        // Setting to store AWS API secret.
        $mform->addElement(
            'passwordunmask',
            'apisecret',
            get_string('apisecret', 'aiprovider_awsbedrock'),
            ['size' => 32],
        );
        $mform->addHelpButton('apisecret', 'apisecret', 'aiprovider_awsbedrock');
        $mform->addRule('apisecret', get_string('required'), 'required', null, 'client');
    }

    /**
     * Hook listener for the Bedrock AI action settings form.
     *
     * @param after_ai_action_settings_form_hook $hook The hook to add to config action settings.
     */
    public static function set_model_form_definition_for_aiprovider_awsbedrock(after_ai_action_settings_form_hook $hook): void {
        if ($hook->plugin !== 'aiprovider_awsbedrock') {
            return;
        }

        $mform = $hook->mform;
        if (isset($mform->_elementIndex['modeltemplate'])) {
            $model = $mform->getElementValue('modeltemplate');
            if (is_array($model)) {
                $model = $model[0];
            }

            if ($model == 'custom') {
                $mform->addElement('header', 'modelsettingsheader', get_string('settings', 'aiprovider_awsbedrock'));
                $settingshelp = html_writer::tag('p', get_string('settings_help', 'aiprovider_awsbedrock'));
                $mform->addElement('html', $settingshelp);
                $mform->addElement(
                    'textarea',
                    'modelextraparams',
                    get_string('extraparams', 'aiprovider_awsbedrock'),
                    ['rows' => 5, 'cols' => 20],
                );
                $mform->setType('modelextraparams', PARAM_TEXT);
                $mform->addElement(
                    'static',
                    'modelextraparams_help',
                    null,
                    get_string('extraparams_help', 'aiprovider_awsbedrock')
                );
            } else {
                $targetmodel = helper::get_model_class($model);
                if ($targetmodel) {
                    if ($targetmodel->has_model_settings()) {
                        $mform->addElement('header', 'modelsettingsheader', get_string('settings', 'aiprovider_awsbedrock'));
                        $settingshelp = html_writer::tag('p', get_string('settings_help', 'aiprovider_awsbedrock'));
                        $mform->addElement('html', $settingshelp);
                        $targetmodel->add_model_settings($mform);
                    }
                }
            }
        }
    }
}
