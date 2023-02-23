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

namespace core_reportbuilder\local\helpers;

use stdClass;
use invalid_parameter_exception;
use core\persistent;
use core_reportbuilder\datasource;
use core_reportbuilder\manager;
use core_reportbuilder\local\models\column;
use core_reportbuilder\local\models\filter;
use core_reportbuilder\local\models\report as report_model;

/**
 * Helper class for manipulating custom reports and their elements (columns, filters, conditions, etc)
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report {

    /**
     * Create custom report
     *
     * @param stdClass $data
     * @param bool $default If $default is set to true it will populate report with default layout as defined by the selected
     *                      source. These include pre-defined columns, filters and conditions.
     * @return report_model
     */
    public static function create_report(stdClass $data, bool $default = true): report_model {
        $data->name = trim($data->name);
        $data->type = datasource::TYPE_CUSTOM_REPORT;

        $reportpersistent = manager::create_report_persistent($data);

        // Add datasource default columns, filters and conditions to the report.
        if ($default) {
            $source = $reportpersistent->get('source');
            /** @var datasource $datasource */
            $datasource = new $source($reportpersistent, []);
            $datasource->add_default_columns();
            $datasource->add_default_filters();
            $datasource->add_default_conditions();
        }

        return $reportpersistent;
    }

    /**
     * Update custom report
     *
     * @param stdClass $data
     * @return report_model
     */
    public static function update_report(stdClass $data): report_model {
        $report = report_model::get_record(['id' => $data->id, 'type' => datasource::TYPE_CUSTOM_REPORT]);
        if ($report === false) {
            throw new invalid_parameter_exception('Invalid report');
        }

        $report->set_many([
            'name' => trim($data->name),
            'uniquerows' => $data->uniquerows,
        ])->update();

        return $report;
    }

    /**
     * Delete custom report
     *
     * @param int $reportid
     * @return bool
     * @throws invalid_parameter_exception
     */
    public static function delete_report(int $reportid): bool {
        $report = report_model::get_record(['id' => $reportid, 'type' => datasource::TYPE_CUSTOM_REPORT]);
        if ($report === false) {
            throw new invalid_parameter_exception('Invalid report');
        }

        return $report->delete();
    }

    /**
     * Add given column to report
     *
     * @param int $reportid
     * @param string $uniqueidentifier
     * @return column
     * @throws invalid_parameter_exception
     */
    public static function add_report_column(int $reportid, string $uniqueidentifier): column {
        $report = manager::get_report_from_id($reportid);

        if (!array_key_exists($uniqueidentifier, $report->get_columns())) {
            throw new invalid_parameter_exception('Invalid column');
        }

        $column = new column(0, (object) [
            'reportid' => $reportid,
            'uniqueidentifier' => $uniqueidentifier,
            'columnorder' => column::get_max_columnorder($reportid, 'columnorder') + 1,
            'sortorder' => column::get_max_columnorder($reportid, 'sortorder') + 1,
        ]);

        return $column->create();
    }

    /**
     * Delete given column from report
     *
     * @param int $reportid
     * @param int $columnid
     * @return bool
     * @throws invalid_parameter_exception
     */
    public static function delete_report_column(int $reportid, int $columnid): bool {
        global $DB;

        $column = column::get_record(['id' => $columnid, 'reportid' => $reportid]);
        if ($column === false) {
            throw new invalid_parameter_exception('Invalid column');
        }

        // After deletion, re-index remaining report columns.
        if ($result = $column->delete()) {
            $sqlupdateorder = '
                UPDATE {' . column::TABLE . '}
                   SET columnorder = columnorder - 1
                 WHERE reportid = :reportid
                   AND columnorder > :columnorder';

            $DB->execute($sqlupdateorder, ['reportid' => $reportid, 'columnorder' => $column->get('columnorder')]);
        }

        return $result;
    }

    /**
     * Re-order given column within a report
     *
     * @param int $reportid
     * @param int $columnid
     * @param int $position
     * @return bool
     * @throws invalid_parameter_exception
     */
    public static function reorder_report_column(int $reportid, int $columnid, int $position): bool {
        $column = column::get_record(['id' => $columnid, 'reportid' => $reportid]);
        if ($column === false) {
            throw new invalid_parameter_exception('Invalid column');
        }

        // Get the rest of the report columns, excluding the one we are moving.
        $columns = column::get_records_select('reportid = :reportid AND id <> :id', [
            'reportid' => $reportid,
            'id' => $columnid,
        ], 'columnorder');

        return static::reorder_persistents_by_field($column, $columns, $position, 'columnorder');
    }

    /**
     * Re-order given column sorting within a report
     *
     * @param int $reportid
     * @param int $columnid
     * @param int $position
     * @return bool
     * @throws invalid_parameter_exception
     */
    public static function reorder_report_column_sorting(int $reportid, int $columnid, int $position): bool {
        $column = column::get_record(['id' => $columnid, 'reportid' => $reportid]);
        if ($column === false) {
            throw new invalid_parameter_exception('Invalid column');
        }

        // Get the rest of the report columns, excluding the one we are moving.
        $columns = column::get_records_select('reportid = :reportid AND id <> :id', [
            'reportid' => $reportid,
            'id' => $columnid,
        ], 'sortorder');

        return static::reorder_persistents_by_field($column, $columns, $position, 'sortorder');
    }

    /**
     * Toggle sorting options for given column within a report
     *
     * @param int $reportid
     * @param int $columnid
     * @param bool $enabled
     * @param int $direction
     * @return bool
     * @throws invalid_parameter_exception
     */
    public static function toggle_report_column_sorting(int $reportid, int $columnid, bool $enabled,
            int $direction = SORT_ASC): bool {

        $column = column::get_record(['id' => $columnid, 'reportid' => $reportid]);
        if ($column === false) {
            throw new invalid_parameter_exception('Invalid column');
        }

        return $column->set_many([
            'sortenabled' => $enabled,
            'sortdirection' => $direction,
        ])->update();
    }

    /**
     * Add given condition to report
     *
     * @param int $reportid
     * @param string $uniqueidentifier
     * @return filter
     * @throws invalid_parameter_exception
     */
    public static function add_report_condition(int $reportid, string $uniqueidentifier): filter {
        $report = manager::get_report_from_id($reportid);

        if (!array_key_exists($uniqueidentifier, $report->get_conditions())) {
            throw new invalid_parameter_exception('Invalid condition');
        }

        $condition = new filter(0, (object) [
            'reportid' => $reportid,
            'uniqueidentifier' => $uniqueidentifier,
            'iscondition' => true,
            'filterorder' => filter::get_max_filterorder($reportid, true) + 1,
        ]);

        return $condition->create();
    }

    /**
     * Delete given condition from report
     *
     * @param int $reportid
     * @param int $conditionid
     * @return bool
     * @throws invalid_parameter_exception
     */
    public static function delete_report_condition(int $reportid, int $conditionid): bool {
        global $DB;

        $condition = filter::get_condition_record($reportid, $conditionid);
        if ($condition === false) {
            throw new invalid_parameter_exception('Invalid condition');
        }

        // After deletion, re-index remaining report conditions.
        if ($result = $condition->delete()) {
            $sqlupdateorder = '
                UPDATE {' . filter::TABLE . '}
                   SET filterorder = filterorder - 1
                 WHERE reportid = :reportid
                   AND filterorder > :filterorder
                   AND iscondition = 1';

            $DB->execute($sqlupdateorder, ['reportid' => $reportid, 'filterorder' => $condition->get('filterorder')]);
        }

        return $result;
    }

    /**
     * Re-order given condition within a report
     *
     * @param int $reportid
     * @param int $conditionid
     * @param int $position
     * @return bool
     * @throws invalid_parameter_exception
     */
    public static function reorder_report_condition(int $reportid, int $conditionid, int $position): bool {
        $condition = filter::get_condition_record($reportid, $conditionid);
        if ($condition === false) {
            throw new invalid_parameter_exception('Invalid condition');
        }

        // Get the rest of the report conditions, excluding the one we are moving.
        $conditions = filter::get_records_select('reportid = :reportid AND iscondition = 1 AND id <> :id', [
            'reportid' => $reportid,
            'id' => $conditionid,
        ], 'filterorder');

        return static::reorder_persistents_by_field($condition, $conditions, $position, 'filterorder');
    }

    /**
     * Add given filter to report
     *
     * @param int $reportid
     * @param string $uniqueidentifier
     * @return filter
     * @throws invalid_parameter_exception
     */
    public static function add_report_filter(int $reportid, string $uniqueidentifier): filter {
        $report = manager::get_report_from_id($reportid);

        $reportfilters = $report->get_filters();
        if (!array_key_exists($uniqueidentifier, $reportfilters)) {
            throw new invalid_parameter_exception('Invalid filter');
        }

        $filter = new filter(0, (object) [
            'reportid' => $reportid,
            'uniqueidentifier' => $uniqueidentifier,
            'filterorder' => filter::get_max_filterorder($reportid) + 1,
        ]);

        return $filter->create();
    }

    /**
     * Delete given filter from report
     *
     * @param int $reportid
     * @param int $filterid
     * @return bool
     * @throws invalid_parameter_exception
     */
    public static function delete_report_filter(int $reportid, int $filterid): bool {
        global $DB;

        $filter = filter::get_filter_record($reportid, $filterid);
        if ($filter === false) {
            throw new invalid_parameter_exception('Invalid filter');
        }

        // After deletion, re-index remaining report filters.
        if ($result = $filter->delete()) {
            $sqlupdateorder = '
                UPDATE {' . filter::TABLE . '}
                   SET filterorder = filterorder - 1
                 WHERE reportid = :reportid
                   AND filterorder > :filterorder
                   AND iscondition = 0';

            $DB->execute($sqlupdateorder, ['reportid' => $reportid, 'filterorder' => $filter->get('filterorder')]);
        }

        return $result;
    }

    /**
     * Re-order given filter within a report
     *
     * @param int $reportid
     * @param int $filterid
     * @param int $position
     * @return bool
     * @throws invalid_parameter_exception
     */
    public static function reorder_report_filter(int $reportid, int $filterid, int $position): bool {
        $filter = filter::get_filter_record($reportid, $filterid);
        if ($filter === false) {
            throw new invalid_parameter_exception('Invalid filter');
        }

        // Get the rest of the report filters, excluding the one we are moving.
        $filters = filter::get_records_select('reportid = :reportid AND iscondition = 0 AND id <> :id', [
            'reportid' => $reportid,
            'id' => $filterid,
        ], 'filterorder');

        return static::reorder_persistents_by_field($filter, $filters, $position, 'filterorder');
    }

    /**
     * Get available columns for a given report
     *
     * @param report_model $persistent
     * @return array
     *
     * @deprecated since Moodle 4.1 - please do not use this function any more, {@see custom_report_column_cards_exporter}
     */
    public static function get_available_columns(report_model $persistent) : array {
        debugging('The function ' . __FUNCTION__ . '() is deprecated, please do not use it any more. ' .
            'See \'custom_report_column_cards_exporter\' class for replacement', DEBUG_DEVELOPER);

        $available = [];

        $report = manager::get_report_from_persistent($persistent);

        // Get current report columns.
        foreach ($report->get_columns() as $column) {
            $entityname = $column->get_entity_name();
            $entitytitle = $column->get_title();
            if (!array_key_exists($entityname, $available)) {
                $available[$entityname] = [
                    'name' => (string) $report->get_entity_title($entityname),
                    'key' => $entityname,
                    'items' => [],
                ];
            }

            $available[$entityname]['items'][] = [
                'name' => $entitytitle,
                'identifier' => $column->get_unique_identifier(),
                'title' => get_string('addcolumn', 'core_reportbuilder', $entitytitle),
                'action' => 'report-add-column'
            ];
        }

        return array_values($available);
    }

    /**
     * Helper method for re-ordering given persistents (columns, filters, etc)
     *
     * @param persistent $persistent The persistent we are moving
     * @param persistent[] $persistents The rest of the persistents
     * @param int $position
     * @param string $field The field we need to update
     * @return bool
     */
    private static function reorder_persistents_by_field(persistent $persistent, array $persistents, int $position,
            string $field): bool {

        // Splice into new position.
        array_splice($persistents, $position - 1, 0, [$persistent]);

        $fieldorder = 1;
        foreach ($persistents as $persistent) {
            $persistent->set($field, $fieldorder++)
                ->update();
        }

        return true;
    }
}
