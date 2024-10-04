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

namespace core_ai\form;

use moodleform;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * AI provider instance form.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ai_provider_form extends moodleform {

    /**
     * Get the custom data.
     *
     * @return mixed The custom data.
     */
    public function get_customdata(): mixed {
        return $this->_customdata;
    }

    #[\Override]
    protected function definition() {
        global $PAGE;
        $PAGE->requires->js_call_amd('core_ai/providerchooser', 'init');

        $mform = $this->_form;
        $providerconfigs = $this->_customdata['providerconfigs'] ?? [];
        $returnurl = $this->_customdata['returnurl'] ?? null;

        // AI provider chooser.
        // Get all enabled AI provider plugins. Users can select one of them to create a new AI provider instance.
        $providerplugins = [];
        $enabledproviderplugins = \core\plugininfo\aiprovider::get_enabled_plugins();
        foreach ($enabledproviderplugins as $pluginname => $notusing) {
            $plugin = 'aiprovider_' . $pluginname;
            $providerplugins[$plugin] = get_string('pluginname', $plugin);
        }

        // Provider chooser.
        $mform->addElement(
            'select',
            'aiprovider',
            get_string('providertype', 'core_ai'),
            $providerplugins,
            ['data-aiproviderchooser-field' => 'selector'],
        );
        if (isset($providerconfigs['id'])) {
            $mform->hardFreeze('aiprovider');
        }

        // Provider instance name.
        $mform->addElement(
            'text',
            'name',
            get_string('providername', 'core_ai'),
            'maxlength="255" size="20"',
        );
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255);

        $mform->registerNoSubmitButton('updateaiprovider');
        $mform->addElement(
            'submit',
            'updateaiprovider',
            'update AI provider',
            ['data-aiproviderchooser-field' => 'updateButton', 'class' => 'd-none']
        );

        // Dispatch a hook for plugins to add their fields.
        $providerplugindefault = array_key_first($providerplugins);
        $hook = new \core_ai\hook\after_ai_provider_form_hook(
            mform: $mform,
            plugin: $providerconfigs['aiprovider'] ?? $providerplugindefault,
        );
        \core\di::get(\core\hook\manager::class)->dispatch($hook);

        // Add the provider plugin name to form customdata.
        $this->_customdata['aiprovider'] = $hook->plugin;

        // Add rate limiting settings.
        // Setting to enable/disable global rate limiting.
        $mform->addElement(
            'checkbox',
            'enableglobalratelimit',
            get_string('enableglobalratelimit', 'core_ai'),
            get_string('enableglobalratelimit_help', 'core_ai'),
        );
        // Setting to set how many requests per hour are allowed for the global rate limit.
        // Should only be enabled when global rate limiting is enabled.
        $mform->addElement(
            'text',
            'globalratelimit',
            get_string('globalratelimit', 'core_ai'),
            'maxlength="10" size="4"',
        );
        $mform->setType('globalratelimit', PARAM_INT);
        $mform->addHelpButton('globalratelimit', 'globalratelimit', 'core_ai');
        $mform->hideIf('globalratelimit', 'enableglobalratelimit', 'notchecked');

        // Setting to enable/disable user rate limiting.
        $mform->addElement(
            'checkbox',
            'enableuserratelimit',
            get_string('enableuserratelimit', 'core_ai'),
            get_string('enableuserratelimit_help', 'core_ai'),
        );
        // Setting to set how many requests per hour are allowed for the user rate limit.
        // Should only be enabled when user rate limiting is enabled.
        $mform->addElement(
            'text',
            'userratelimit',
            get_string('userratelimit', 'core_ai'),
            'maxlength="10" size="4"',
        );
        $mform->setType('userratelimit', PARAM_INT);
        $mform->addHelpButton('userratelimit', 'userratelimit', 'core_ai');
        $mform->hideIf('userratelimit', 'enableuserratelimit', 'notchecked');

        // Form buttons.
        $buttonarray = [];
        // If provider config is empty this is a new instance.
        if (empty($providerconfigs['id'])) {
            $buttonarray[] = $mform->createElement('submit', 'createandreturn',
                get_string('btninstancecreate', 'core_ai'));
        } else {
            // We're updating an existing provider.
            $buttonarray[] = $mform->createElement('submit', 'updateandreturn',
                get_string('btninstanceupdate', 'core_ai'));
        }

        $buttonarray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', [' '], false);
        $mform->closeHeaderBefore('buttonar');

        if ($returnurl) {
            $mform->addElement('hidden', 'returnurl', $returnurl);
            $mform->setType('returnurl', PARAM_LOCALURL);
        }

        if (isset($providerconfigs['id'])) {
            $mform->addElement('hidden', 'id', $providerconfigs['id']);
            $mform->setType('id', PARAM_INT);
        }

        $this->set_data($providerconfigs);
    }

    #[\Override]
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Ensure both global/user rate limits (if enabled) contain positive values.
        if (!empty($data['enableglobalratelimit']) && $data['globalratelimit'] <= 0) {
            $errors['globalratelimit'] = get_string('err_positiveint', 'core_form');
        }
        if (!empty($data['enableuserratelimit']) && $data['userratelimit'] <= 0) {
            $errors['userratelimit'] = get_string('err_positiveint', 'core_form');
        }

        return $errors;
    }
}
