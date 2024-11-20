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

use core_reportbuilder\manager;
use core_reportbuilder\local\helpers\aggregation;
use core_reportbuilder\local\helpers\report;
use core_reportbuilder\local\helpers\user_filter_manager;
use core_reportbuilder\table\custom_report_table_view;

/**
 * Helper base class for reportbuilder unit tests
 *
 * @package     core_reportbuilder
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class core_reportbuilder_testcase extends advanced_testcase {

    /**
     * Retrieve content for given report as array of report data
     *
     * @param int $reportid
     * @param int $pagesize
     * @param array $filtervalues
     * @return array[]
     */
    protected function get_custom_report_content(int $reportid, int $pagesize = 30, array $filtervalues = []): array {
        $records = [];

        // Apply filter values.
        user_filter_manager::set($reportid, $filtervalues);

        // Create table instance.
        $table = custom_report_table_view::create($reportid);
        $table->setup();
        $table->query_db($pagesize, false);

        // Extract raw data.
        foreach ($table->rawdata as $record) {
            $records[] = $table->format_row($record);
        }

        $table->close_recordset();

        return $records;
    }

    /**
     * Stress test a report source by iterating over all it's columns, enabling sorting where possible and asserting we can
     * create a report for each
     *
     * @param string $source
     */
    protected function datasource_stress_test_columns(string $source): void {

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report(['name' => 'Stress columns', 'source' => $source, 'default' => 0]);
        $instance = manager::get_report_from_persistent($report);

        // Iterate over each available column, ensure each works correctly independent of any others.
        foreach ($instance->get_columns() as $columnidentifier => $columninstance) {
            $column = report::add_report_column($report->get('id'), $columnidentifier);

            // Enable sorting of the column where possible.
            if ($columninstance->get_is_sortable()) {
                report::toggle_report_column_sorting($report->get('id'), $column->get('id'), true, SORT_DESC);
            }

            // We are only asserting the report returns content without errors, not the content itself.
            try {
                $content = $this->get_custom_report_content($report->get('id'));
                $this->assertNotEmpty($content);

                // Ensure appropriate debugging was triggered for deprecated column.
                if ($columninstance->get_is_deprecated()) {
                    $this->assertDebuggingCalled(null, DEBUG_DEVELOPER);
                }
            } catch (Throwable $exception) {
                $this->fail("Error for column '{$columnidentifier}': " . $exception->getMessage());
            }

            report::delete_report_column($report->get('id'), $column->get('id'));
        }
    }

    /**
     * Stress test a report source by iterating over all columns and asserting we can create a report while aggregating each
     *
     * @param string $source
     */
    protected function datasource_stress_test_columns_aggregation(string $source): void {

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report(['name' => 'Stress aggregation', 'source' => $source, 'default' => 0]);
        $instance = manager::get_report_from_persistent($report);

        // Add every column.
        $columndeprecatedcount = 0;
        foreach ($instance->get_columns() as $columnidentifier => $column) {
            $columndeprecatedcount += (int) $column->get_is_deprecated();
            report::add_report_column($report->get('id'), $columnidentifier);
        }

        // Now iterate over each column, and apply all suitable aggregation types.
        $columns = $instance->get_active_columns();
        $this->assertDebuggingCalledCount($columndeprecatedcount, null,
            array_fill(0, $columndeprecatedcount, DEBUG_DEVELOPER));
        foreach ($columns as $column) {
            $aggregations = aggregation::get_column_aggregations($column->get_type(), $column->get_disabled_aggregation());
            foreach (array_keys($aggregations) as $aggregation) {
                $column->get_persistent()->set('aggregation', $aggregation)->update();

                // We are only asserting the report returns content without errors, not the content itself.
                try {
                    $content = $this->get_custom_report_content($report->get('id'));
                    $this->assertNotEmpty($content);

                    // Ensure appropriate debugging was triggered for deprecated columns.
                    $this->assertDebuggingCalledCount($columndeprecatedcount, null,
                        array_fill(0, $columndeprecatedcount, DEBUG_DEVELOPER));
                } catch (Throwable $exception) {
                    $this->fail("Error for column '{$column->get_unique_identifier()}' with aggregation '{$aggregation}': " .
                        $exception->getMessage());
                }
            }

            // Reset the column aggregation.
            $column->get_persistent()->set('aggregation', null)->update();
        }
    }

    /**
     * Stress test a report source by iterating over all it's conditions and asserting we can create a report using each
     *
     * @param string $source
     * @param string $columnidentifier Should be a simple column, with as few fields and joins as possible, ideally selected
     *      from the base table itself
     */
    protected function datasource_stress_test_conditions(string $source, string $columnidentifier): void {

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report(['name' => 'Stress conditions', 'source' => $source, 'default' => 0]);
        $instance = manager::get_report_from_persistent($report);

        // Add single column only (to ensure no conditions have reliance on any columns).
        report::add_report_column($report->get('id'), $columnidentifier);

        // Iterate over each available condition, ensure each works correctly independent of any others.
        $conditionidentifiers = array_keys($instance->get_conditions());
        foreach ($conditionidentifiers as $conditionidentifier) {
            $condition = report::add_report_condition($report->get('id'), $conditionidentifier);
            $conditioninstance = $instance->get_condition($condition->get('uniqueidentifier'));

            /** @var \core_reportbuilder\local\filters\base $conditionclass */
            $conditionclass = $conditioninstance->get_filter_class();

            // Set report condition values in order to activate it.
            $conditionvalues = $conditionclass::create($conditioninstance)->get_sample_values();
            if (empty($conditionvalues)) {
                debugging("Missing sample values from filter '{$conditionclass}'", DEBUG_DEVELOPER);
            }
            $instance->set_condition_values($conditionvalues);

            // We are only asserting the report returns content without errors, not the content itself.
            try {
                $content = $this->get_custom_report_content($report->get('id'));
                $this->assertIsArray($content);

                // Ensure appropriate debugging was triggered for deprecated condition.
                if ($conditioninstance->get_is_deprecated()) {
                    $this->assertDebuggingCalled(null, DEBUG_DEVELOPER);
                }
            } catch (Throwable $exception) {
                $this->fail("Error for condition '{$conditionidentifier}': " . $exception->getMessage());
            }

            report::delete_report_condition($report->get('id'), $condition->get('id'));
        }
    }
}
