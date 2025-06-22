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

namespace tiny_premium\form;

use moodleform;

/**
 * Form for configuring Tiny Premium plugin settings.
 *
 * @package    tiny_premium
 * @copyright  2025 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tiny_premium_settings_form extends moodleform {

    #[\Override]
    protected function definition(): void {
        $mform = $this->_form;
        $returnurl = $this->_customdata['returnurl'] ?? null;
        $plugin = $this->_customdata['plugin'] ?? '';
        $this->add_settings($mform, $plugin);

        $mform->addElement('hidden', 'plugin', $plugin);
        $mform->setType('plugin', PARAM_ALPHA);
        if ($returnurl) {
            $mform->addElement('hidden', 'returnurl', $returnurl);
            $mform->setType('returnurl', PARAM_LOCALURL);
        }

        $this->add_action_buttons();
    }

    /**
     * Add settings to the form.
     * This method can be overridden to add additional settings for specific plugins.
     *
     * @param \MoodleQuickForm $mform The Moodle QuickForm instance to which settings will be added.
     * @param string $plugin The plugin name for which settings are being added.
     */
    protected function add_settings(
        \MoodleQuickForm $mform,
        string $plugin = '',
    ): void {
        $pluginname = '';
        if (!empty($plugin)) {
            $configs = \tiny_premium\manager::get_plugin_config($plugin);
            $pluginname = get_string('premiumplugin:' . $plugin, 'tiny_premium');
        }

        $mform->addElement('header', 'serversidesettingssheader', get_string('serverside:service', 'tiny_premium'));
        $settingshelp = \html_writer::tag('p', get_string('serverside:desc', 'tiny_premium', $pluginname));
        $mform->addElement('html', $settingshelp);

        $radiogroup = [
            $mform->createElement('radio', 'server_side_service', '', get_string('serverside:service:tinycloud', 'tiny_premium'),
                '1'),
            $mform->createElement('radio', 'server_side_service', '', get_string('serverside:service:selfhosted', 'tiny_premium'),
                '2'),
        ];

        $mform->addGroup($radiogroup, 'server_side_service_group', get_string('serverside:service', 'tiny_premium'), '<br>',
            false);
        if (empty($configs->service_url)) {
            $mform->setDefault('server_side_service', 1);
        } else {
            $mform->setDefault('server_side_service', 2);
        }

        $mform->addElement('text', 'service_url', get_string('serviceurl', 'tiny_premium'));
        $mform->setType('service_url', PARAM_TEXT);
        $mform->hideIf('service_url', 'server_side_service', 'eq', '1');
        if (isset($configs->service_url)) {
            $mform->setDefault('service_url', $configs->service_url);
        }

        $mform->addElement('static', 'description', '', get_string('serviceurl:desc', 'tiny_premium', $pluginname));
        $mform->hideIf('description', 'server_side_service', 'eq', '1');
    }

    #[\Override]
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);

        // Validate the service URL if self-hosted service is selected.
        if (isset($data['server_side_service']) && $data['server_side_service'] == 2) {
            if (empty($data['service_url'])) {
                $errors['service_url'] = get_string('required');
            } else {
                // Validate the URL format.
                if (!filter_var($data['service_url'], FILTER_VALIDATE_URL)) {
                    $errors['service_url'] = get_string('invalidurl', 'tiny_premium');
                }
            }
        }

        return $errors;
    }

    #[\Override]
    public function get_data(): ?\stdClass {
        $data = parent::get_data();

        if ($data) {
            // If the server-side service is Tiny Cloud, unset the service URL.
            if (isset($data->server_side_service) && $data->server_side_service == 1) {
                unset($data->service_url);
            }
        }

        return $data;
    }
}
