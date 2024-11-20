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
use core_reportbuilder\local\helpers\report;
use core_course\reportbuilder\datasource\courses;

/**
 * Unit tests for custom report filters exporter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\external\custom_report_filters_exporter
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_report_filters_exporter_test extends advanced_testcase {

    /**
     * Test exported data structure
     */
    public function test_export(): void {
        global $PAGE;

        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => courses::class, 'default' => false]);

        // Add a couple of filters.
        $filtercategoryname = $generator->create_filter([
            'reportid' => $report->get('id'),
            'uniqueidentifier' => 'course_category:name',
        ]);
        $filtercourseidnumber = $generator->create_filter([
            'reportid' => $report->get('id'),
            'uniqueidentifier' => 'course:idnumber',
        ]);

        // Move course ID number filter to first place.
        report::reorder_report_filter($report->get('id'), $filtercourseidnumber->get('id'), 1);

        $reportinstance = manager::get_report_from_persistent($report);

        $exporter = new custom_report_filters_exporter(null, ['report' => $reportinstance]);
        $export = $exporter->export($PAGE->get_renderer('core_reportbuilder'));

        $this->assertTrue($export->hasavailablefilters);

        // The root of the available filters property should contain each entity.
        $this->assertCount(4, $export->availablefilters);
        [$filterscategory, $filterscourse, $filterstag, $filtersfile] = $export->availablefilters;

        // Course category filters, assert structure of first item.
        $this->assertEquals('Course category', $filterscategory['optiongroup']['text']);
        $this->assertGreaterThanOrEqual(1, count($filterscategory['optiongroup']['values']));
        $this->assertEquals([
            'value' => 'course_category:text',
            'visiblename' => 'Category name',
        ], $filterscategory['optiongroup']['values'][0]);

        // Course filters, assert structure of first item.
        $this->assertEquals('Course', $filterscourse['optiongroup']['text']);
        $this->assertGreaterThanOrEqual(1, count($filterscourse['optiongroup']['values']));
        $this->assertEquals([
            'value' => 'course:fullname',
            'visiblename' => 'Course full name',
        ], $filterscourse['optiongroup']['values'][0]);

        // Make sure the active filters we added, aren't present in available filters.
        $filterscourseavailable = array_column($filterscourse['optiongroup']['values'], 'value');
        $this->assertNotContains('course_category:name', $filterscourseavailable);
        $this->assertNotContains('course:idnumber', $filterscourseavailable);

        // Tag filters, assert structure of first item.
        $this->assertEquals('Tag', $filterstag['optiongroup']['text']);
        $this->assertGreaterThanOrEqual(1, count($filterstag['optiongroup']['values']));
        $this->assertEquals([
            'value' => 'tag:name',
            'visiblename' => 'Tag name',
        ], $filterstag['optiongroup']['values'][0]);

        // File filters, assert structure of first item.
        $this->assertEquals('Course image', $filtersfile['optiongroup']['text']);
        $this->assertGreaterThanOrEqual(1, count($filtersfile['optiongroup']['values']));
        $this->assertEquals([
            'value' => 'file:name',
            'visiblename' => 'Filename',
        ], $filtersfile['optiongroup']['values'][0]);

        $this->assertTrue($export->hasactivefilters);
        $this->assertCount(2, $export->activefilters);
        [$activefiltercourseidnumber, $activefiltercategoryname] = $export->activefilters;

        // Course ID number filter.
        $this->assertEquals($filtercourseidnumber->get('id'), $activefiltercourseidnumber['id']);
        $this->assertEquals('Course', $activefiltercourseidnumber['entityname']);
        $this->assertEquals('Course ID number', $activefiltercourseidnumber['heading']);
        $this->assertEquals(1, $activefiltercourseidnumber['sortorder']);

        // Course category filter.
        $this->assertEquals($filtercategoryname->get('id'), $activefiltercategoryname['id']);
        $this->assertEquals('Course category', $activefiltercategoryname['entityname']);
        $this->assertEquals('Select category', $activefiltercategoryname['heading']);
        $this->assertEquals(2, $activefiltercategoryname['sortorder']);

        $this->assertNotEmpty($export->helpicon);
    }

    /**
     * Test exported data structure for report with no filters
     */
    public function test_export_no_filters(): void {
        global $PAGE;

        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => courses::class, 'default' => false]);

        $reportinstance = manager::get_report_from_persistent($report);

        $exporter = new custom_report_filters_exporter(null, ['report' => $reportinstance]);
        $export = $exporter->export($PAGE->get_renderer('core_reportbuilder'));

        $this->assertFalse($export->hasactivefilters);
        $this->assertEmpty($export->activefilters);
    }
}
