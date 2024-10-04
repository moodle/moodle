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

namespace core_admin\table;

use moodle_url;

/**
 * Admin tool settings.
 *
 * @package core_admin
 * @copyright 2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_plugin_management_table extends \core_admin\table\plugin_management_table {
    protected function get_plugintype(): string {
        return 'tool';
    }

    protected function get_column_list(): array {
        $columns = parent::get_column_list();

        unset($columns['settings']);

        return $columns;
    }

    protected function get_action_url(array $params = []): moodle_url {
        return new moodle_url('/admin/settings.php', array_merge(['section' => 'toolsmanagement'], $params));
    }
}
