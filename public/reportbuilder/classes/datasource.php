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

    /** @var float[] $elementsmodified Track the time elements of specific reports have been added, updated, removed */
    private static $elementsmodified = [];

    /** @var array $activecolumns */
    private $activecolumns;

    /** @var array $activefilters */
    private $activefilters;

    /** @var array $activeconditions */
    private $activeconditions;

    /**
     * Add default datasource columns to the report
     *
     * Uses column data returned by the source {@see get_default_columns} and {@see get_default_column_sorting} methods
     *
     * @throws coding_exception If default column sorting refers to an invalid column
     */
    public function add_default_columns(): void {
        $reportid = $this->get_report_persistent()->get('id');

        // Retrieve default column sorting, and track index of both sorted/non-sorted columns.
        $columnidentifiers = $this->get_default_columns();

        $defaultcolumnsorting = $this->get_default_column_sorting();
        $defaultcolumnsortinginvalid = array_diff_key($defaultcolumnsorting,
            array_fill_keys($columnidentifiers, 1));

        if (count($defaultcolumnsortinginvalid) > 0) {
            throw new coding_exception('Invalid column name', array_key_first($defaultcolumnsortinginvalid));
        }

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
     * Return the default columns that will be added to the report upon creation, by {@see add_default_columns}
     *
     * @return string[]
     */
    abstract public function get_default_columns(): array;

    /**
     * Return the default column sorting that will be set for the report upon creation, by {@see add_default_columns}
     *
     * When overriding this method in child classes, column identifiers specified must refer to default columns returned from
     * the {@see get_default_columns} method
     *
     * @return int[] array [column identifier => SORT_ASC/SORT_DESC]
     */
    public function get_default_column_sorting(): array {
        return [];
    }

    /**
     * Override parent method, returning only those columns specifically added to the custom report (rather than all that are
     * available)
     *
     * @return column[]
     */
    public function get_active_columns(): array {
        $reportid = $this->get_report_persistent()->get('id');

        // Determine whether we already retrieved the columns since the report was last modified.
        self::$elementsmodified += [$reportid => -1];
        if ($this->activecolumns !== null && $this->activecolumns['builttime'] > self::$elementsmodified[$reportid]) {
            return $this->activecolumns['values'];
        }

        $this->activecolumns = ['builttime' => microtime(true), 'values' => []];

        $activecolumns = column_model::get_records(['reportid' => $reportid], 'columnorder');
        foreach ($activecolumns as $index => $column) {
            $instance = $this->get_column($column->get('uniqueidentifier'));

            // Ensure the column is still present and available.
            if ($instance !== null && $instance->get_is_available()) {
                if ($instance->get_is_deprecated()) {
                    debugging("The column '{$instance->get_unique_identifier()}' is deprecated, please do not use it any more." .
                        " {$instance->get_is_deprecated_message()}", DEBUG_DEVELOPER);
                }

                $columnaggregation = $column->get('aggregation');

                // We should clone the report column to ensure if it's added twice to a report, each operates independently.
                $this->activecolumns['values'][] = clone $instance
                    ->set_index($index)
                    ->set_persistent($column)
                    ->set_aggregation($columnaggregation, $instance->get_aggregation_options($columnaggregation));
            }
        }

        return $this->activecolumns['values'];
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
     * Override parent method, returning only those filters specifically added to the custom report (rather than all that are
     * available)
     *
     * @return filter[]
     */
    public function get_active_filters(): array {
        $reportid = $this->get_report_persistent()->get('id');

        // Determine whether we already retrieved the filters since the report was last modified.
        self::$elementsmodified += [$reportid => -1];
        if ($this->activefilters !== null && $this->activefilters['builttime'] > self::$elementsmodified[$reportid]) {
            return $this->activefilters['values'];
        }

        $this->activefilters = ['builttime' => microtime(true), 'values' => []];

        $activefilters = filter_model::get_filter_records($reportid, 'filterorder');
        foreach ($activefilters as $filter) {
            $instance = $this->get_filter($filter->get('uniqueidentifier'));

            // Ensure the filter is still present and available.
            if ($instance !== null && $instance->get_is_available()) {
                if ($instance->get_is_deprecated()) {
                    debugging("The filter '{$instance->get_unique_identifier()}' is deprecated, please do not use it any more." .
                        " {$instance->get_is_deprecated_message()}", DEBUG_DEVELOPER);
                }

                $this->activefilters['values'][$instance->get_unique_identifier()] = $instance->set_persistent($filter);
            }
        }

        return $this->activefilters['values'];
    }

    /**
     * Add conditions from the given entity name to be available to use in a custom report
     *
     * Wildcard matching is supported with '*' in both $include and $exclude, e.g. ['customfield*']
     *
     * @param string $entityname
     * @param string[] $include Include only these conditions, if omitted then include all
     * @param string[] $exclude Exclude these conditions, if omitted then exclude none
     * @throws coding_exception If both $include and $exclude are non-empty
     */
    final protected function add_conditions_from_entity(string $entityname, array $include = [], array $exclude = []): void {
        if (!empty($include) && !empty($exclude)) {
            throw new coding_exception('Cannot specify conditions to include and exclude simultaneously');
        }

        $entity = $this->get_entity($entityname);

        // Retrieve filtered conditions from entity, respecting given $include/$exclude parameters.
        $conditions = array_filter($entity->get_conditions(), function(filter $condition) use ($include, $exclude): bool {
            if (!empty($include)) {
                return $this->report_element_search($condition->get_name(), $include);
            }

            if (!empty($exclude)) {
                return !$this->report_element_search($condition->get_name(), $exclude);
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
     * Override parent method, returning only those conditions specifically added to the custom report (rather than all that are
     * available)
     *
     * @param bool $checkavailable
     * @return filter[]
     */
    public function get_active_conditions(bool $checkavailable = true): array {
        $reportid = $this->get_report_persistent()->get('id');

        // Determine whether we already retrieved the conditions since the report was last modified.
        self::$elementsmodified += [$reportid => -1];
        if ($this->activeconditions !== null && $this->activeconditions['builttime'] > self::$elementsmodified[$reportid]) {
            return $this->activeconditions['values'];
        }

        $this->activeconditions = ['builttime' => microtime(true), 'values' => []];

        $activeconditions = filter_model::get_condition_records($reportid, 'filterorder');
        foreach ($activeconditions as $condition) {
            $instance = $this->get_condition($condition->get('uniqueidentifier'));

            // Ensure the condition is still present and available (if checking available status).
            if ($instance !== null && (!$checkavailable || $instance->get_is_available())) {
                if ($instance->get_is_deprecated()) {
                    debugging("The condition '{$instance->get_unique_identifier()}' is deprecated, please do not use it any more." .
                        " {$instance->get_is_deprecated_message()}", DEBUG_DEVELOPER);
                }

                $this->activeconditions['values'][$instance->get_unique_identifier()] = $instance->set_persistent($condition);
            }
        }

        return $this->activeconditions['values'];
    }

    /**
     * Adds all columns/filters/conditions from the given entity to the report at once
     *
     * @param string $entityname
     * @param string[] $limitcolumns Include only these columns
     * @param string[] $limitfilters Include only these filters
     * @param string[] $limitconditions Include only these conditions
     */
    final protected function add_all_from_entity(
        string $entityname,
        array $limitcolumns = [],
        array $limitfilters = [],
        array $limitconditions = [],
    ): void {
        $this->add_columns_from_entity($entityname, $limitcolumns);
        $this->add_filters_from_entity($entityname, $limitfilters);
        $this->add_conditions_from_entity($entityname, $limitconditions);
    }

    /**
     * Adds all columns/filters/conditions from all the entities added to the report at once
     *
     * @param string[] $entitynames If specified, then only these entity elements are added (otherwise all)
     */
    final protected function add_all_from_entities(array $entitynames = []): void {
        foreach ($this->get_entities() as $entity) {
            $entityname = $entity->get_entity_name();
            if (!empty($entitynames) && array_search($entityname, $entitynames) === false) {
                continue;
            }
            $this->add_all_from_entity($entityname);
        }
    }

    /**
     * Indicate that report elements have been modified, e.g. columns/filters/conditions have been added, removed or updated
     *
     * @param int $reportid
     */
    final public static function report_elements_modified(int $reportid): void {
        self::$elementsmodified[$reportid] = microtime(true);
    }
}
