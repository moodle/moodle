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

    /**
     * Get the type of plugin this table manages.
     *
     * @return string The type of plugin this table manages.
     */
    protected function get_plugintype(): string {
        return 'aiprovider';
    }

    /**
     * Get the URL to the action.
     *
     * @param array $params The parameters to pass to the URL.
     * @return moodle_url The URL to the action.
     * @throws \core\exception\moodle_exception
     */
    protected function get_action_url(array $params = []): moodle_url {
        return new moodle_url('/admin/ai.php', $params);
    }

    /**
     * Get the column list for this table.
     *
     * @return array The column list for this table.
     */
    protected function get_column_list(): array {
        $columns = [
                'name' => get_string('name', 'core'),
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
