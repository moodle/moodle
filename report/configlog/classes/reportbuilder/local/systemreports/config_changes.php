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

namespace report_configlog\reportbuilder\local\systemreports;

use context_system;
use report_configlog\reportbuilder\local\entities\config_change;
use core_reportbuilder\system_report;
use core_reportbuilder\local\entities\user;
use stdClass;

/**
 * Config changes system report class implementation
 *
 * @package    report_configlog
 * @copyright  2020 Paul Holden <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class config_changes extends system_report {

    /**
     * Initialise report, we need to set the main table, load our entities and set columns/filters
     */
    protected function initialise(): void {
        // Our main entity, it contains all of the column definitions that we need.
        $entitymain = new config_change();
        $entitymainalias = $entitymain->get_table_alias('config_log');

        $this->set_main_table('config_log', $entitymainalias);
        $this->add_entity($entitymain);

        // We can join the "user" entity to our "main" entity using standard SQL JOIN.
        $entityuser = new user();
        $entityuseralias = $entityuser->get_table_alias('user');
        $this->add_entity($entityuser
            ->add_join("LEFT JOIN {user} {$entityuseralias} ON {$entityuseralias}.id = {$entitymainalias}.userid")
        );

        // Now we can call our helper methods to add the content we want to include in the report.
        $this->add_columns();
        $this->add_filters();

        // Set if report can be downloaded.
        $this->set_downloadable(true, get_string('pluginname', 'report_configlog'));
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
     * Adds the columns we want to display in the report
     *
     * They are all provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier
     */
    protected function add_columns(): void {
        $columns = [
            'config_change:timemodified',
            'user:fullnamewithlink',
            'config_change:plugin',
            'config_change:setting',
            'config_change:newvalue',
            'config_change:oldvalue',
        ];

        $this->add_columns_from_entities($columns);

        // Default sorting.
        $this->set_initial_sort_column('config_change:timemodified', SORT_DESC);

        // Custom callback to show 'CLI or install' in fullname column when there is no user.
        if ($column = $this->get_column('user:fullnamewithlink')) {
            $column->add_callback(static function(string $fullname, stdClass $row): string {
                return $fullname ?: get_string('usernone', 'report_configlog');
            });
        }
    }

    /**
     * Adds the filters we want to display in the report
     *
     * They are all provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier
     */
    protected function add_filters(): void {
        $filters = [
            'config_change:plugin',
            'config_change:setting',
            'config_change:value',
            'config_change:oldvalue',
            'user:fullname',
            'config_change:timemodified',
        ];

        $this->add_filters_from_entities($filters);
    }
}
