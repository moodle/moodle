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

namespace core_communication\admin;

use admin_setting;
use core_plugin_manager;
use core_text;
use html_table;
use html_table_row;
use html_writer;
use moodle_url;

/**
 * Communication providers manager. Allow enable/disable communication providers and jump to settings.
 *
 * @package    core_communication
 * @copyright  2023 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manage_communication_providers_page extends admin_setting {
    public function __construct() {
        $this->nosave = true;
        parent::__construct(
            'managecommunications',
            new \lang_string('managecommunicationproviders', 'core_communication'),
            '',
            ''
        );
    }

    public function get_setting(): bool {
        return true;
    }

    public function write_setting($data): string {
        // Do not write any setting.
        return '';
    }

    public function output_html($data, $query = ''): string {
        global $OUTPUT;

        $pluginmanager = core_plugin_manager::instance();
        $plugins = $pluginmanager->get_plugins_of_type('communication');
        if (empty($plugins)) {
            return get_string('nocommunicationprovider', 'core_communication');
        }

        $table = new html_table();
        $table->head = [
            get_string('name'),
            get_string('enable'),
            get_string('settings'),
            get_string('uninstallplugin', 'core_admin'),
        ];
        $table->align = ['left', 'center', 'center', 'center'];
        $table->attributes['class'] = 'managecommunicationtable generaltable admintable';
        $table->data = [];

        foreach ($plugins as $plugin) {
            $class = '';
            $actionurl = new moodle_url('/admin/communication.php', ['sesskey' => sesskey(), 'name' => $plugin->name]);
            if (
                $pluginmanager->get_plugin_info('communication_' . $plugin->name)->get_status() ===
                core_plugin_manager::PLUGIN_STATUS_MISSING
            ) {
                $strtypename = $plugin->displayname . ' (' . get_string('missingfromdisk') . ')';
            } else {
                $strtypename = $plugin->displayname;
            }

            if ($plugin->is_enabled()) {
                $hideshow = html_writer::link(
                    $actionurl->out(false, ['action' => 'disable']),
                    $OUTPUT->pix_icon('t/hide', get_string('disable'), 'moodle', ['class' => 'iconsmall'])
                );
            } else {
                $class = 'dimmed_text';
                $hideshow = html_writer::link(
                    $actionurl->out(false, ['action' => 'enable']),
                    $OUTPUT->pix_icon('t/show', get_string('enable'), 'moodle', ['class' => 'iconsmall'])
                );
            }

            $settings = '';
            if ($plugin->get_settings_url()) {
                $settings = html_writer::link($plugin->get_settings_url(), get_string('settings'));
            }

            $uninstall = '';
            if (
                $uninstallurl = core_plugin_manager::instance()->get_uninstall_url(
                    'communication_' . $plugin->name,
                    'manage'
                )
            ) {
                $uninstall = html_writer::link($uninstallurl, get_string('uninstallplugin', 'core_admin'));
            }

            $row = new html_table_row([$strtypename, $hideshow, $settings, $uninstall]);
            if ($class) {
                $row->attributes['class'] = $class;
            }
            $table->data[] = $row;
        }

        return highlight($query, html_writer::table($table));
    }

    public function is_related($query): bool {
        if (parent::is_related($query)) {
            return true;
        }
        $types = core_plugin_manager::instance()->get_plugins_of_type('communication');
        foreach ($types as $type) {
            if (
                strpos($type->component, $query) !== false ||
                strpos(core_text::strtolower($type->displayname), $query) !== false
            ) {
                return true;
            }
        }
        return false;
    }
}
