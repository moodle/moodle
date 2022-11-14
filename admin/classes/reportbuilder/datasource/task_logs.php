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

declare(strict_types=1);

namespace core_admin\reportbuilder\datasource;

use core_admin\reportbuilder\local\entities\task_log;
use core_reportbuilder\datasource;
use core_reportbuilder\local\entities\user;
use core_reportbuilder\local\filters\select;

/**
 * Task logs datasource
 *
 * @package     core_admin
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class task_logs extends datasource {

    /**
     * Return user friendly name of the report source
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('tasklogs', 'core_admin');
    }

    /**
     * Initialise report
     */
    protected function initialise(): void {
        $tasklogentity = new task_log();

        $tasklogalias = $tasklogentity->get_table_alias('task_log');
        $this->set_main_table('task_log', $tasklogalias);

        $this->add_entity($tasklogentity);

        // Join the user entity to represent the associated user.
        $userentity = new user();
        $useralias = $userentity->get_table_alias('user');
        $this->add_entity($userentity->add_join("
            LEFT JOIN {user} {$useralias}
                   ON {$useralias}.id = {$tasklogalias}.userid")
        );

        // Add report elements from each of the entities we added to the report.
        $this->add_all_from_entities();
    }

    /**
     * Return the columns that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_columns(): array {
        return [
            'task_log:name',
            'task_log:starttime',
            'task_log:duration',
            'task_log:result',
        ];
    }

    /**
     * Return the column sorting that will be added to the report upon creation
     *
     * @return int[]
     */
    public function get_default_column_sorting(): array {
        return [
            'task_log:starttime' => SORT_DESC,
        ];
    }

    /**
     * Return the filters that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_filters(): array {
        return [
            'task_log:timestart',
            'task_log:result',
        ];
    }

    /**
     * Return the conditions that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_conditions(): array {
        return [
            'task_log:type',
            'task_log:timestart',
            'task_log:result',
        ];
    }

    /**
     * Return the condition values that will be set for the report upon creation
     *
     * @return array
     */
    public function get_default_condition_values(): array {
        return [
            'task_log:type_operator' => select::EQUAL_TO,
            'task_log:type_value' => \core\task\database_logger::TYPE_SCHEDULED,
        ];
    }
}
