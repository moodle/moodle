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

namespace core_reportbuilder\external;

use advanced_testcase;
use core_reportbuilder_generator;
use core_reportbuilder\manager;
use core_course\reportbuilder\datasource\courses;

/**
 * Unit tests for custom report conditions exporter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\external\custom_report_conditions_exporter
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class custom_report_conditions_exporter_test extends advanced_testcase {

    /**
     * Test exported data structure
     */
    public function test_export(): void {
        global $PAGE;

        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => courses::class, 'default' => false]);
        $generator->create_condition(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:shortname']);

        $reportinstance = manager::get_report_from_persistent($report);

        $exporter = new custom_report_conditions_exporter(null, ['report' => $reportinstance]);
        $export = $exporter->export($PAGE->get_renderer('core_reportbuilder'));

        $this->assertTrue($export->hasavailableconditions);

        // The root of the available conditions property should contain each entity.
        $this->assertCount(4, $export->availableconditions);
        [$conditionscategory, $conditionscourse, $conditionstag, $conditionsfile] = $export->availableconditions;

        // Course category conditions, assert structure of first item.
        $this->assertEquals('Course category', $conditionscategory['optiongroup']['text']);
        $this->assertGreaterThanOrEqual(1, count($conditionscategory['optiongroup']['values']));
        $this->assertEquals([
            'value' => 'course_category:name',
            'visiblename' => 'Select category',
        ], $conditionscategory['optiongroup']['values'][0]);

        // Course conditions, assert structure of first item.
        $this->assertEquals('Course', $conditionscourse['optiongroup']['text']);
        $this->assertGreaterThanOrEqual(1, count($conditionscourse['optiongroup']['values']));
        $this->assertEquals([
            'value' => 'course:fullname',
            'visiblename' => 'Course full name',
        ], $conditionscourse['optiongroup']['values'][0]);

        // Make sure the active condition we added, isn't present in available conditions.
        $this->assertNotContains('course:shortname', array_column($conditionscourse['optiongroup']['values'], 'value'));

        // Tag conditions, assert structure of first item.
        $this->assertEquals('Tag', $conditionstag['optiongroup']['text']);
        $this->assertGreaterThanOrEqual(1, count($conditionstag['optiongroup']['values']));
        $this->assertEquals([
            'value' => 'tag:name',
            'visiblename' => 'Tag name',
        ], $conditionstag['optiongroup']['values'][0]);

        // File conditions, assert structure of first item.
        $this->assertEquals('Course image', $conditionsfile['optiongroup']['text']);
        $this->assertGreaterThanOrEqual(1, count($conditionsfile['optiongroup']['values']));
        $this->assertEquals([
            'value' => 'file:name',
            'visiblename' => 'Filename',
        ], $conditionsfile['optiongroup']['values'][0]);

        // The active conditions are contained inside form HTML, just assert there's something present.
        $this->assertTrue($export->hasactiveconditions);
        $this->assertNotEmpty($export->activeconditionsform);
        $this->assertNotEmpty($export->helpicon);
    }

    /**
     * Test exported data structure for report with no conditions
     */
    public function test_export_no_conditions(): void {
        global $PAGE;

        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => courses::class, 'default' => false]);

        $reportinstance = manager::get_report_from_persistent($report);

        $exporter = new custom_report_conditions_exporter(null, ['report' => $reportinstance]);
        $export = $exporter->export($PAGE->get_renderer('core_reportbuilder'));

        $this->assertFalse($export->hasactiveconditions);
        $this->assertEmpty($export->activeconditionsform);
    }
}
