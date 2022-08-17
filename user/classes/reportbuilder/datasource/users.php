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

namespace core_user\reportbuilder\datasource;

use core_reportbuilder\datasource;
use core_reportbuilder\local\entities\user;
use core_reportbuilder\local\helpers\database;
use core_reportbuilder\manager;
use core_reportbuilder\local\helpers\report;

/**
 * Users datasource
 *
 * @package   core_reportbuilder
 * @copyright 2021 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class users extends datasource {

    /**
     * Return user friendly name of the datasource
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('users');
    }

    /**
     * Initialise report
     */
    protected function initialise(): void {
        global $CFG;

        $userentity = new user();
        $usertablealias = $userentity->get_table_alias('user');

        $this->set_main_table('user', $usertablealias);

        $userparamguest = database::generate_param_name();
        $this->add_base_condition_sql("{$usertablealias}.id != :{$userparamguest} AND {$usertablealias}.deleted = 0", [
            $userparamguest => $CFG->siteguest,
        ]);

        // Add all columns from entities to be available in custom reports.
        $this->add_entity($userentity);

        $userentityname = $userentity->get_entity_name();
        $this->add_columns_from_entity($userentityname);
        $this->add_filters_from_entity($userentityname);
        $this->add_conditions_from_entity($userentityname);
    }

    /**
     * Return the columns that will be added to the report once is created
     *
     * @return string[]
     */
    public function get_default_columns(): array {
        return ['user:fullname', 'user:username', 'user:email'];
    }

    /**
     * Return the filters that will be added to the report once is created
     *
     * @return string[]
     */
    public function get_default_filters(): array {
        return ['user:fullname', 'user:username', 'user:email'];
    }

    /**
     * Return the conditions that will be added to the report once is created
     *
     * @return string[]
     */
    public function get_default_conditions(): array {
        return ['user:fullname', 'user:username', 'user:email'];
    }

    /**
     * Set default columns and the sortorder
     */
    public function add_default_columns(): void {
        parent::add_default_columns();

        $persistent = $this->get_report_persistent();
        $report = manager::get_report_from_persistent($persistent);
        foreach ($report->get_active_columns() as $column) {
            if ($column->get_unique_identifier() === 'user:fullname') {
                report::toggle_report_column_sorting($persistent->get('id'), $column->get_persistent()->get('id'), true);
            }
        }
    }
}
