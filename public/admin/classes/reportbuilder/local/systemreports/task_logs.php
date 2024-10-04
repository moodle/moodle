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

namespace core_admin\reportbuilder\local\systemreports;

use context_system;
use core_admin\reportbuilder\local\entities\task_log;
use core_reportbuilder\local\entities\user;
use core_reportbuilder\local\report\action;
use core_reportbuilder\system_report;
use html_writer;
use lang_string;
use moodle_url;
use pix_icon;
use stdClass;

/**
 * Task logs system report class implementation
 *
 * @package    core_admin
 * @copyright  2021 David Matamoros <davidmc@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class task_logs extends system_report {

    /**
     * Initialise report, we need to set the main table, load our entities and set columns/filters
     */
    protected function initialise(): void {
        // Our main entity, it contains all of the column definitions that we need.
        $entitymain = new task_log();
        $entitymainalias = $entitymain->get_table_alias('task_log');

        $this->set_main_table('task_log', $entitymainalias);
        $this->add_entity($entitymain);

        // Any columns required by actions should be defined here to ensure they're always available.
        $this->add_base_fields("{$entitymainalias}.id");

        // We can join the "user" entity to our "main" entity and use the fullname column from the user entity.
        $entityuser = new user();
        $entituseralias = $entityuser->get_table_alias('user');
        $this->add_entity($entityuser->add_join(
            "LEFT JOIN {user} {$entituseralias} ON {$entituseralias}.id = {$entitymainalias}.userid"
        ));

        // Now we can call our helper methods to add the content we want to include in the report.
        $this->add_columns();
        $this->add_filters();
        $this->add_actions();

        // Set if report can be downloaded.
        $this->set_downloadable(true, get_string('tasklogs', 'admin'));
    }

    /**
     * Validates access to view this report
     *
     * @return bool
     */
    protected function can_view(): bool {
        return has_capability('moodle/site:config', context_system::instance());
    }

    /**
     * Get the visible name of the report
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('entitytasklog', 'admin');
    }

    /**
     * Adds the columns we want to display in the report
     *
     * They are all provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier
     */
    public function add_columns(): void {
        $entitymainalias = $this->get_entity('task_log')->get_table_alias('task_log');

        $this->add_columns_from_entities([
            'task_log:name',
            'task_log:type',
            'user:fullname',
            'task_log:starttime',
            'task_log:duration',
            'task_log:hostname',
            'task_log:pid',
            'task_log:database',
            'task_log:result',
        ]);

        // Wrap the task name in a link.
        $this->get_column('task_log:name')
            ->add_field("{$entitymainalias}.id")
            ->add_callback(static function(string $output, stdClass $row): string {
                return html_writer::link(new moodle_url('/admin/tasklogs.php', ['logid' => $row->id]), $output);
            });

        // Rename the user fullname column.
        $this->get_column('user:fullname')
            ->set_title(new lang_string('user', 'admin'));

        // It's possible to set a default initial sort direction for one column.
        $this->set_initial_sort_column('task_log:starttime', SORT_DESC);
    }

    /**
     * Adds the filters we want to display in the report
     *
     * They are all provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier
     */
    protected function add_filters(): void {
        $this->add_filters_from_entities([
            'task_log:name',
            'task_log:type',
            'task_log:output',
            'task_log:result',
            'task_log:timestart',
            'task_log:duration',
        ]);
    }

    /**
     * Add the system report actions. An extra column will be appended to each row, containing all actions added here
     *
     * Note the use of ":id" placeholder which will be substituted according to actual values in the row
     */
    protected function add_actions(): void {

        // Action to view individual task log on a popup window.
        $this->add_action((new action(
            new moodle_url('/admin/tasklogs.php', ['logid' => ':id']),
            new pix_icon('e/search', ''),
            [],
            false,
            new lang_string('view'),
        )));

        // Action to download individual task log.
        $this->add_action((new action(
            new moodle_url('/admin/tasklogs.php', ['logid' => ':id', 'download' => true]),
            new pix_icon('t/download', ''),
            [],
            false,
            new lang_string('download'),
        )));
    }
}
