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
use stdClass;

/**
 * Media plugin admin settings.
 *
 * @package core_admin
 * @copyright 2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class media_management_table extends \core_admin\table\plugin_management_table {
    /** @var array The list of used extensions */
    protected array $usedextensions = [];

    protected function get_plugintype(): string {
        return 'media';
    }

    protected function get_action_url(array $params = []): moodle_url {
        return new moodle_url('/admin/media.php', $params);
    }

    protected function get_column_list(): array {
        $columns = parent::get_column_list();
        return array_merge(
            array_slice($columns, 0, 1, true),
            ['supports' => get_string('supports', 'core_media')],
            array_slice($columns, 1, null, true),
        );
    }

    protected function col_name(stdClass $row): string {
        global $OUTPUT, $PAGE;

        $name = $row->plugininfo->name;
        if ($PAGE->theme->resolve_image_location('icon', 'media_' . $name, false)) {
            $icon = $OUTPUT->pix_icon('icon', '', "media_{$name}", ['class' => 'icon pluginicon']);
        } else {
            $icon = $OUTPUT->pix_icon('spacer', '', 'moodle', ['class' => 'icon pluginicon noicon']);
        }

        $help = '';
        if (get_string_manager()->string_exists('pluginname_help', 'media_' . $name)) {
            $help = '&nbsp;' . $OUTPUT->help_icon('pluginname', 'media_' . $name);
        }

        return $icon . $row->plugininfo->displayname . $help;
    }

    protected function col_supports(stdClass $row): string {
        return $row->plugininfo->supports($this->usedextensions);
    }
}
