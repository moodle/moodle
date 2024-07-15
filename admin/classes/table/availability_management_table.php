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
use html_writer;
use get_string_manager;

/**
 * Availability admin settings.
 *
 * @package core_admin
 * @copyright 2024 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class availability_management_table extends plugin_management_table {

    protected function get_table_id(): string {
        return 'availabilityconditions_administration_table';
    }

    protected function get_plugintype(): string {
        return 'availability';
    }

    protected function get_action_url(array $params = []): moodle_url {
        return new moodle_url('/admin/tool/availabilityconditions/', $params);
    }

    public function guess_base_url(): void {
        $this->define_baseurl(
            new moodle_url('/admin/tool/availabilityconditions/')
        );
    }

    protected function get_column_list(): array {
        return [
            'name' => get_string('plugin'),
            'version' => get_string('version'),
            'enabled' => get_string('enabled', 'admin'),
            'defaultdisplaymode' => get_string('defaultdisplaymode', 'tool_availabilityconditions'),
        ];
    }

    public function setup(): void {
        $this->set_attribute('id', 'availabilityconditions_administration_table');
        $this->set_attribute('class', 'admintable generaltable');
        parent::setup();
    }

    protected function col_name(stdClass $row): string {
        return html_writer::span(
           get_string('pluginname', 'availability_' . $row->plugininfo->name)
        );
    }

    protected function col_defaultdisplaymode(stdClass $row): string {
        global $OUTPUT, $CFG;
        $displaymode = get_config('availability_' . $row->plugininfo->name, 'defaultdisplaymode') ? 'show' : 'hide';
        $paramsdisplaymode = [
            'sesskey' => sesskey(),
            'plugin' => $row->plugininfo->name,
            'displaymode' => $displaymode,
        ];
        $urldisplaymode = new moodle_url('/' . $CFG->admin . '/tool/availabilityconditions/', $paramsdisplaymode);

        return html_writer::link($urldisplaymode, $OUTPUT->pix_icon('t/' . $displaymode,
            get_string($displaymode)), ['class' => 'display-mode-' . $row->plugininfo->name]);
    }
}
