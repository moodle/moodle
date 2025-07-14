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
 * Unit tests for custom report column cards exporter
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\external\custom_report_column_cards_exporter
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class custom_report_column_cards_exporter_test extends advanced_testcase {

    /**
     * Test exported data structure
     */
    public function test_export(): void {
        global $PAGE;

        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => courses::class]);

        $reportinstance = manager::get_report_from_persistent($report);

        $exporter = new custom_report_column_cards_exporter(null, ['report' => $reportinstance]);
        $export = $exporter->export($PAGE->get_renderer('core_reportbuilder'));

        // The root of the menu cards property should contain each entity.
        $this->assertCount(4, $export->menucards);
        [$menucardcategory, $menucardcourse, $menucardtag, $menucardfile] = $export->menucards;

        // Course category entity menu card.
        $this->assertEquals('Course category', $menucardcategory['name']);
        $this->assertEquals('course_category', $menucardcategory['key']);
        $this->assertNotEmpty($menucardcategory['items']);

        // Test the structure of the first menu card item.
        $menucarditem = reset($menucardcategory['items']);
        $this->assertEquals([
            'name' => 'Category name',
            'identifier' => 'course_category:name',
            'title' => 'Add column \'Category name\'',
            'action' => 'report-add-column',
        ], $menucarditem);

        // Course entity menu card.
        $this->assertEquals('Course', $menucardcourse['name']);
        $this->assertEquals('course', $menucardcourse['key']);
        $this->assertNotEmpty($menucardcourse['items']);

        // Test the structure of the first menu card item.
        $menucarditem = reset($menucardcourse['items']);
        $this->assertEquals([
            'name' => 'Course full name with link',
            'identifier' => 'course:coursefullnamewithlink',
            'title' => 'Add column \'Course full name with link\'',
            'action' => 'report-add-column',
        ], $menucarditem);

        // Tag entity menu card.
        $this->assertEquals('Tag', $menucardtag['name']);
        $this->assertEquals('tag', $menucardtag['key']);
        $this->assertNotEmpty($menucardtag['items']);

        // Test the structure of the first menu card item.
        $menucarditem = reset($menucardtag['items']);
        $this->assertEquals([
            'name' => 'Tag name',
            'identifier' => 'tag:name',
            'title' => 'Add column \'Tag name\'',
            'action' => 'report-add-column',
        ], $menucarditem);

        // File entity menu card.
        $this->assertEquals('Course image', $menucardfile['name']);
        $this->assertEquals('file', $menucardfile['key']);
        $this->assertNotEmpty($menucardfile['items']);

        // Test the structure of the first menu card item.
        $menucarditem = reset($menucardfile['items']);
        $this->assertEquals([
            'name' => 'Filename',
            'identifier' => 'file:name',
            'title' => 'Add column \'Filename\'',
            'action' => 'report-add-column',
        ], $menucarditem);
    }
}
