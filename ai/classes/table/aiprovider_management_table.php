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

namespace core_ai\table;

use moodle_url;

/**
 * Table to manage AI Provider plugins.
 *
 * @package core_ai
 * @copyright 2024 Matt Porritt <matt.porritt@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class aiprovider_management_table extends \core_admin\table\plugin_management_table {
    #[\Override]
    protected function get_plugintype(): string {
        return 'aiprovider';
    }

    #[\Override]
    protected function get_action_url(array $params = []): moodle_url {
        return new moodle_url('/admin/ai.php', $params);
    }

    #[\Override]
    protected function get_column_list(): array {
        $columns = [
            'name' => get_string('provider', 'core_ai'),
        ];

        if ($this->supports_disabling()) {
            $columns['enabled'] = get_string('pluginenabled', 'core_plugin');
        }

        if ($this->supports_ordering()) {
            $columns['order'] = get_string('order', 'core');
        }

        $columns['settings'] = get_string('settings', 'core');
        $columns['uninstall'] = get_string('uninstallplugin', 'core_admin');

        return $columns;
    }
}
