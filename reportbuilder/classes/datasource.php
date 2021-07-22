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

namespace core_reportbuilder;

use coding_exception;
use core_reportbuilder\local\helpers\report;
use core_reportbuilder\local\models\filter as filter_model;
use core_reportbuilder\local\report\base;
use core_reportbuilder\local\report\column;
use core_reportbuilder\local\report\filter;

/**
 * Class datasource
 *
 * @package   core_reportbuilder
 * @copyright 2021 David Matamoros <davidmc@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class datasource extends base {

    /**
     * Return user friendly name of the datasource
     *
     * @return string
     */
    abstract public static function get_name(): string;

    /**
     * Add columns from the given entity name to be available to use in a custom report
     *
     * @param string $entityname
     * @param array $include Include only these columns, if omitted then include all
     * @param array $exclude Exclude these columns, if omitted then exclude none
     * @throws coding_exception If both $include and $exclude are non-empty
     */
    final protected function add_columns_from_entity(string $entityname, array $include = [], array $exclude = []): void {
        if (!empty($include) && !empty($exclude)) {
            throw new coding_exception('Cannot specify columns to include and exclude simultaneously');
        }

        $entity = $this->get_entity($entityname);

        // Retrieve filtered columns from entity, respecting given $include/$exclude parameters.
        $columns = array_filter($entity->get_columns(), static function(column $column) use ($include, $exclude): bool {
            if (!empty($include)) {
                return in_array($column->get_name(), $include);
            }

            if (!empty($exclude)) {
                return !in_array($column->get_name(), $exclude);
            }

            return true;
        });

        foreach ($columns as $column) {
            $this->add_column($column);
        }
    }

    /**
     * Add default datasource columns to the report
     *
     * This method is optional and can be called when the report is created to add the default columns defined in the
     * selected datasource.
     */
    public function add_default_columns(): void {
        $reportid = $this->get_report_persistent()->get('id');
        $columnidentifiers = $this->get_default_columns();
        foreach ($columnidentifiers as $uniqueidentifier) {
            report::add_report_column($reportid, $uniqueidentifier);
        }
    }

    /**
     * Return the columns that will be added to the report once is created
     *
     * @return string[]
     */
    abstract public function get_default_columns(): array;
}
