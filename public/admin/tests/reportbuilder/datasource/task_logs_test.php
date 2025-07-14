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

use core\task\database_logger;
use core_reportbuilder_generator;
use core_reportbuilder\local\filters\{date, duration, number, select, text};
use core_reportbuilder\task\send_schedules;
use core_reportbuilder\tests\core_reportbuilder_testcase;

/**
 * Unit tests for task logs datasource
 *
 * @package     core_admin
 * @covers      \core_admin\reportbuilder\datasource\task_logs
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class task_logs_test extends core_reportbuilder_testcase {

    /**
     * Test default datasource
     */
    public function test_datasource_default(): void {
        $this->resetAfterTest();

        $this->generate_task_log_data(true, 3, 2, 1654038000, 1654038060);
        $this->generate_task_log_data(false, 5, 1, 1654556400, 1654556700);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Tasks', 'source' => task_logs::class, 'default' => 1]);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(2, $content);

        // Default columns are name, start time, duration, result. Sorted by start time descending.
        [$name, $timestart, $duration, $result] = array_values($content[0]);
        $this->assertStringContainsString(send_schedules::class, $name);
        $this->assertEquals('7/06/22, 07:00:00', $timestart);
        $this->assertEquals('5 mins', $duration);
        $this->assertEquals('Fail', $result);

        [$name, $timestart, $duration, $result] = array_values($content[1]);
        $this->assertStringContainsString(send_schedules::class, $name);
        $this->assertEquals('1/06/22, 07:00:00', $timestart);
        $this->assertEquals('1 min', $duration);
        $this->assertEquals('Success', $result);
    }

    /**
     * Test datasource columns that aren't added by default
     */
    public function test_datasource_non_default_columns(): void {
        $this->resetAfterTest();

        $this->generate_task_log_data(true, 3, 2, 1654038000, 1654038060, 'hi', 'core_reportbuilder', 'test', 43);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Tasks', 'source' => task_logs::class, 'default' => 0]);

        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'task_log:component']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'task_log:type']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'task_log:endtime']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'task_log:hostname']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'task_log:pid']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'task_log:database']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'task_log:dbreads']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'task_log:dbwrites']);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(1, $content);

        $this->assertEquals([
            'core_reportbuilder',
            'Scheduled',
            '1/06/22, 07:01:00',
            'test',
            '43',
            '<div>3 reads</div><div>2 writes</div>',
            '3',
            '2',
        ], array_values($content[0]));
    }

    /**
     * Data provider for {@see test_datasource_filters}
     *
     * @return array[]
     */
    public static function datasource_filters_provider(): array {
        return [
            'Filter name' => ['task_log:name', [
                'task_log:name_values' => [send_schedules::class],
            ], true],
            'Filter name (no match)' => ['task_log:name', [
                'task_log:name_values' => ['invalid'],
            ], false],
            'Filter component' => ['task_log:component', [
                'task_log:component_operator' => select::EQUAL_TO,
                'task_log:component_value' => 'core_reportbuilder',
            ], true],
            'Filter component (no match)' => ['task_log:component', [
                'task_log:component_operator' => select::NOT_EQUAL_TO,
                'task_log:component_value' => 'core_reportbuilder',
            ], false],
            'Filter type' => ['task_log:type', [
                'task_log:type_operator' => select::EQUAL_TO,
                'task_log:type_value' => database_logger::TYPE_SCHEDULED,
            ], true],
            'Filter type (no match)' => ['task_log:type', [
                'task_log:type_operator' => select::EQUAL_TO,
                'task_log:type_value' => database_logger::TYPE_ADHOC,
            ], false],
            'Filter output' => ['task_log:output', [
                'task_log:output_operator' => text::IS_NOT_EMPTY,
            ], true],
            'Filter output (no match)' => ['task_log:output', [
                'task_log:output_operator' => text::IS_EMPTY,
            ], false],
            'Filter result' => ['task_log:result', [
                'task_log:result_operator' => select::EQUAL_TO,
                'task_log:result_value' => 0,
            ], true],
            'Filter result (no match)' => ['task_log:result', [
                'task_log:result_operator' => select::EQUAL_TO,
                'task_log:result_value' => 1,
            ], false],
            'Filter time start' => ['task_log:timestart', [
                'task_log:timestart_operator' => date::DATE_RANGE,
                'task_log:timestart_from' => 1622502000,
            ], true],
            'Filter time start (no match)' => ['task_log:timestart', [
                'task_log:timestart_operator' => date::DATE_RANGE,
                'task_log:timestart_to' => 1622502000,
            ], false],
            'Filter time end' => ['task_log:timeend', [
                'task_log:timeend_operator' => date::DATE_RANGE,
                'task_log:timeend_from' => 1622502000,
            ], true],
            'Filter time end (no match)' => ['task_log:timeend', [
                'task_log:timeend_operator' => date::DATE_RANGE,
                'task_log:timeend_to' => 1622502000,
            ], false],
            'Filter duration' => ['task_log:duration', [
                'task_log:duration_operator' => duration::DURATION_MAXIMUM,
                'task_log:duration_unit' => MINSECS,
                'task_log:duration_value' => 2,
            ], true],
            'Filter duration (no match)' => ['task_log:duration', [
                'task_log:duration_operator' => duration::DURATION_MINIMUM,
                'task_log:duration_unit' => MINSECS,
                'task_log:duration_value' => 2,
            ], false],
            'Filter database reads' => ['task_log:dbreads', [
                'task_log:dbreads_operator' => number::LESS_THAN,
                'task_log:dbreads_value1' => 4,
            ], true],
            'Filter database reads (no match)' => ['task_log:dbreads', [
                'task_log:dbreads_operator' => number::GREATER_THAN,
                'task_log:dbreads_value1' => 4,
            ], false],
            'Filter database writes' => ['task_log:dbwrites', [
                'task_log:dbwrites_operator' => number::LESS_THAN,
                'task_log:dbwrites_value1' => 4,
            ], true],
            'Filter database writes (no match)' => ['task_log:dbwrites', [
                'task_log:dbwrites_operator' => number::GREATER_THAN,
                'task_log:dbwrites_value1' => 4,
            ], false],
        ];
    }

    /**
     * Test datasource filters
     *
     * @param string $filtername
     * @param array $filtervalues
     * @param bool $expectmatch
     *
     * @dataProvider datasource_filters_provider
     */
    public function test_datasource_filters(
        string $filtername,
        array $filtervalues,
        bool $expectmatch
    ): void {
        $this->resetAfterTest();

        $this->generate_task_log_data(true, 3, 2, 1654038000, 1654038060, 'hi', 'core_reportbuilder', 'test', 43);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create report containing single component column, and given filter.
        $report = $generator->create_report(['name' => 'Tasks', 'source' => task_logs::class, 'default' => 0]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'task_log:component']);

        // Add filter, set it's values.
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => $filtername]);
        $content = $this->get_custom_report_content($report->get('id'), 0, $filtervalues);

        if ($expectmatch) {
            $this->assertCount(1, $content);
            $this->assertEquals('core_reportbuilder', reset($content[0]));
        } else {
            $this->assertEmpty($content);
        }
    }

    /**
     * Stress test datasource
     *
     * In order to execute this test PHPUNIT_LONGTEST should be defined as true in phpunit.xml or directly in config.php
     */
    public function test_stress_datasource(): void {
        if (!PHPUNIT_LONGTEST) {
            $this->markTestSkipped('PHPUNIT_LONGTEST is not defined');
        }

        $this->resetAfterTest();

        $this->generate_task_log_data(true, 3, 2, 1654038000, 1654038060, 'hi', 'core_reportbuilder', 'test', 43);

        $this->datasource_stress_test_columns(task_logs::class);
        $this->datasource_stress_test_columns_aggregation(task_logs::class);
        $this->datasource_stress_test_conditions(task_logs::class, 'task_log:name');
    }

    /**
     * Helper to generate some task logs data
     *
     * @param bool $success
     * @param int $dbreads
     * @param int $dbwrites
     * @param float $timestart
     * @param float $timeend
     * @param string $logoutput
     * @param string $component
     * @param string $hostname
     * @param int $pid
     */
    private function generate_task_log_data(
        bool $success,
        int $dbreads,
        int $dbwrites,
        float $timestart,
        float $timeend,
        string $logoutput = 'hello',
        string $component = 'moodle',
        string $hostname = 'phpunit',
        int $pid = 42
    ): void {

        $logpath = make_request_directory() . '/log.txt';
        file_put_contents($logpath, $logoutput);

        $task = new send_schedules();
        $task->set_component($component);
        $task->set_hostname($hostname);
        $task->set_pid($pid);

        database_logger::store_log_for_task($task, $logpath, !$success, $dbreads, $dbwrites, $timestart, $timeend);
    }
}
