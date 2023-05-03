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
 * Tiny admin settings.
 *
 * @package core_admin
 * @copyright 2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editor_management_table extends \core_admin\table\plugin_management_table {
    protected function get_plugintype(): string {
        return 'editor';
    }

    public function guess_base_url(): void {
        $this->define_baseurl(
            new moodle_url('/admin/settings.php', ['section' => 'manageeditors'])
        );
    }

    protected function get_action_url(array $params = []): moodle_url {
        return new moodle_url('/admin/editors.php', $params);
    }

    protected function order_plugins(array $plugins): array {
        global $CFG;

        // The Editor list is stored in an ordered string.
        $activeeditors = explode(',', $CFG->texteditors);

        $sortedplugins = [];
        foreach ($activeeditors as $editor) {
            if (isset($plugins[$editor])) {
                $sortedplugins[$editor] = $plugins[$editor];
                unset($plugins[$editor]);
            }
        }

        $otherplugins = parent::order_plugins($plugins);
        return array_merge(
            $sortedplugins,
            $otherplugins
        );
    }
}
