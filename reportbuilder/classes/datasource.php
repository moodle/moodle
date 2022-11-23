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
use core_reportbuilder\local\models\column as column_model;
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

        // Retrieve default column sorting, and track index of both sorted/non-sorted columns.
        $columnidentifiers = $this->get_default_columns();
        $defaultcolumnsorting = array_intersect_key($this->get_default_column_sorting(),
            array_fill_keys($columnidentifiers, 1));
        $columnnonsortingindex = count($defaultcolumnsorting) + 1;

        foreach ($columnidentifiers as $uniqueidentifier) {
            $column = report::add_report_column($reportid, $uniqueidentifier);

            // After adding the column, toggle sorting according to defaults provided by the datasource.
            $sortorder = array_search($uniqueidentifier, array_keys($defaultcolumnsorting));
            if ($sortorder !== false) {
                $column->set_many([
                    'sortenabled' => true,
                    'sortdirection' => $defaultcolumnsorting[$uniqueidentifier],
                    'sortorder' => $sortorder + 1,
                ])->update();
            } else if (!empty($defaultcolumnsorting)) {
                $column->set('sortorder', $columnnonsortingindex++)->update();
            }
        }
    }

    /**
     * Return the columns that will be added to the report once is created
     *
     * @return string[]
     */
    abstract public function get_default_columns(): array;

    /**
     * Return the default sorting that will be added to the report once it is created
     *
     * @return int[] array [column identifier => SORT_ASC/SORT_DESC]
     */
    public function get_default_column_sorting(): array {
        return [];
    }

    /**
     * Return all configured report columns
     *
     * @return column[]
     */
    public function get_active_columns(): array {
        $columns = [];

        $activecolumns = column_model::get_records(['reportid' => $this->get_report_persistent()->get('id')], 'columnorder');
        foreach ($activecolumns as $index => $column) {
            $instance = $this->get_column($column->get('uniqueidentifier'));
            if ($instance !== null && $instance->get_is_available()) {
                $instance->set_persistent($column);

                // We should clone the report column to ensure if it's added twice to a report, each operates independently.
                $columns[] = clone $instance
                    ->set_index($index)
                    ->set_aggregation($column->get('aggregation'));
            }
        }

        return $columns;
    }

    /**
     * Add filters from the given entity name to be available to use in a custom report
     *
     * @param string $entityname
     * @param array $include Include only these filters, if omitted then include all
     * @param array $exclude Exclude these filters, if omitted then exclude none
     * @throws coding_exception If both $include and $exclude are non-empty
     */
    final protected function add_filters_from_entity(string $entityname, array $include = [], array $exclude = []): void {
        if (!empty($include) && !empty($exclude)) {
            throw new coding_exception('Cannot specify filters to include and exclude simultaneously');
        }

        $entity = $this->get_entity($entityname);

        // Retrieve filtered filters from entity, respecting given $include/$exclude parameters.
        $filters = array_filter($entity->get_filters(), static function(filter $filter) use ($include, $exclude): bool {
            if (!empty($include)) {
                return in_array($filter->get_name(), $include);
            }

            if (!empty($exclude)) {
                return !in_array($filter->get_name(), $exclude);
            }

            return true;
        });

        foreach ($filters as $filter) {
            $this->add_filter($filter);
        }
    }

    /**
     * Add default datasource filters to the report
     *
     * This method is optional and can be called when the report is created to add the default filters defined in the
     * selected datasource.
     */
    public function add_default_filters(): void {
        $reportid = $this->get_report_persistent()->get('id');
        $filteridentifiers = $this->get_default_filters();
        foreach ($filteridentifiers as $uniqueidentifier) {
            report::add_report_filter($reportid, $uniqueidentifier);
        }
    }

    /**
     * Return the filters that will be added to the report once is created
     *
     * @return string[]
     */
    abstract public function get_default_filters(): array;

    /**
     * Return all configured report filters
     *
     * @return filter[]
     */
    public function get_active_filters(): array {
        $filters = [];

        $activefilters = filter_model::get_filter_records($this->get_report_persistent()->get('id'), 'filterorder');
        foreach ($activefilters as $filter) {
            $instance = $this->get_filter($filter->get('uniqueidentifier'));
            if ($instance !== null && $instance->get_is_available()) {
                $filters[$instance->get_unique_identifier()] = $instance
                    ->set_persistent($filter);
            }
        }

        return $filters;
    }

    /**
     * Add conditions from the given entity name to be available to use in a custom report
     *
     * @param string $entityname
     * @param array $include Include only these conditions, if omitted then include all
     * @param array $exclude Exclude these conditions, if omitted then exclude none
     * @throws coding_exception If both $include and $exclude are non-empty
     */
    final protected function add_conditions_from_entity(string $entityname, array $include = [], array $exclude = []): void {
        if (!empty($include) && !empty($exclude)) {
            throw new coding_exception('Cannot specify conditions to include and exclude simultaneously');
        }

        $entity = $this->get_entity($entityname);

        // Retrieve filtered conditions from entity, respecting given $include/$exclude parameters.
        $conditions = array_filter($entity->get_conditions(), static function(filter $condition) use ($include, $exclude): bool {
            if (!empty($include)) {
                return in_array($condition->get_name(), $include);
            }

            if (!empty($exclude)) {
                return !in_array($condition->get_name(), $exclude);
            }

            return true;
        });

        foreach ($conditions as $condition) {
            $this->add_condition($condition);
        }
    }

    /**
     * Add default datasource conditions to the report
     *
     * This method is optional and can be called when the report is created to add the default conditions defined in the
     * selected datasource.
     */
    public function add_default_conditions(): void {
        $reportid = $this->get_report_persistent()->get('id');
        $conditionidentifiers = $this->get_default_conditions();
        foreach ($conditionidentifiers as $uniqueidentifier) {
            report::add_report_condition($reportid, $uniqueidentifier);
        }

        // Set the default condition values if they have been set in the datasource.
        $this->set_condition_values($this->get_default_condition_values());
    }

    /**
     * Return the conditions that will be added to the report once is created
     *
     * @return string[]
     */
    abstract public function get_default_conditions(): array;

    /**
     * Return the default condition values that will be added to the report once is created
     *
     * For any of the default conditions returned by the method {@see get_default_conditions} is
     * possible to set the initial values.
     *
     * @return array
     */
    public function get_default_condition_values(): array {
        return [];
    }

    /**
     * Return all configured report conditions
     *
     * @return filter[]
     */
    public function get_active_conditions(): array {
        $conditions = [];

        $activeconditions = filter_model::get_condition_records($this->get_report_persistent()->get('id'), 'filterorder');
        foreach ($activeconditions as $condition) {
            $instance = $this->get_condition($condition->get('uniqueidentifier'));
            if ($instance !== null && $instance->get_is_available()) {
                $conditions[$instance->get_unique_identifier()] = $instance->set_persistent($condition);
            }
        }

        return $conditions;
    }

    /**
     * Adds all columns/filters/conditions from the given entity to the report at once
     *
     * @param string $entityname
     */
    final protected function add_all_from_entity(string $entityname): void {
        $this->add_columns_from_entity($entityname);
        $this->add_filters_from_entity($entityname);
        $this->add_conditions_from_entity($entityname);
    }

    /**
     * Adds all columns/filters/conditions from all the entities added to the report at once
     */
    final protected function add_all_from_entities(): void {
        foreach ($this->get_entities() as $entity) {
            $this->add_all_from_entity($entity->get_entity_name());
        }
    }
}
