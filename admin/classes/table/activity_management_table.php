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

use core_plugin_manager;
use dml_exception;
use html_writer;
use moodle_url;
use stdClass;

/**
 * Activity Module admin settings.
 *
 * @package core_admin
 * @copyright 2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity_management_table extends plugin_management_table {

    public function setup() {
        $this->set_attribute('id', 'modules');
        $this->set_attribute('class', 'admintable generaltable');
        parent::setup();
    }

    protected function get_table_id(): string {
        return 'module-administration-table';
    }

    protected function get_plugintype(): string {
        return 'mod';
    }

    public function guess_base_url(): void {
        $this->define_baseurl(
            new moodle_url('/admin/modules.php')
        );
    }

    protected function get_action_url(array $params = []): moodle_url {
        return new moodle_url('/admin/modules.php', $params);
    }

    protected function get_column_list(): array {
        $columns = parent::get_column_list();
        return array_merge(
            array_slice($columns, 0, 1, true),
            ['activities' => get_string('activities')],
            array_slice($columns, 1, null, true),
        );
    }

    protected function col_name(stdClass $row): string {
        global $OUTPUT;

        $status = $row->plugininfo->get_status();
        if ($status === core_plugin_manager::PLUGIN_STATUS_MISSING) {
            return html_writer::span(
                get_string('pluginmissingfromdisk', 'core', $row->plugininfo),
                'notifyproblem'
            );
        }

        return html_writer::span(
            html_writer::img(
                $OUTPUT->image_url('monologo', $row->plugininfo->name),
                '',
                [
                    'class' => 'icon',
                ],
            ) . get_string('modulename', $row->plugininfo->name)
        );
    }

    /**
     * Show the number of activities present, with a link to courses containing activity if relevant.
     *
     * @param mixed $row
     * @return string
     */
    protected function col_activities(stdClass $row): string {
        global $DB, $OUTPUT;
        try {
            $count = $DB->count_records_select($row->plugininfo->name, "course <> 0");
        } catch (dml_exception $e) {
            $count = -1;
        }

        if ($count > 0) {
            return $OUTPUT->action_link(
                new moodle_url('/course/search.php', [
                    'modulelist' => $row->plugininfo->name,
                ]),
                $count,
                null,
                ['title' => get_string('showmodulecourse')]
            );
        } else if ($count < 0) {
            return get_string('error');
        } else {
            return $count;
        }
    }
}
