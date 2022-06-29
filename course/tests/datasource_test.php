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

namespace core_course\reportbuilder\datasource;

use core_reportbuilder_testcase;
use core_reportbuilder_generator;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/reportbuilder/tests/helpers.php");

/**
 * Unit tests for component datasources
 *
 * @package     core_course
 * @covers      \core_course\reportbuilder\datasource\courses
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class datasource_test extends core_reportbuilder_testcase {

    /**
     * Test courses datasource
     */
    public function test_courses_datasource(): void {
        $this->resetAfterTest();

        // Test subject.
        $category = $this->getDataGenerator()->create_category(['name' => 'My cats']);
        $course = $this->getDataGenerator()->create_course([
            'category' => $category->id,
            'fullname' => 'All about cats',
            'shortname' => 'C101',
            'idnumber' => 'CAT101'
        ]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Courses', 'source' => courses::class]);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(1, $content);

        $contentrow = array_values(reset($content));
        $this->assertEquals([
            'My cats', // Category name.
            'C101', // Course shortname.
            'All about cats', // Course fullname.
            'CAT101', // Course ID number.
        ], $contentrow);
    }

    /**
     * Tests courses datasource using multilang filters
     */
    public function test_courses_datasource_multilang_filters(): void {
        $this->resetAfterTest();

        filter_set_global_state('multilang', TEXTFILTER_ON);
        filter_set_applies_to_strings('multilang', true);

        // Test subject.
        $category = $this->getDataGenerator()->create_category([
            'name' => '<span class="multilang" lang="en">Cat (en)</span><span class="multilang" lang="es">Cat (es)</span>',
        ]);
        $course = $this->getDataGenerator()->create_course([
            'category' => $category->id,
            'fullname' => '<span class="multilang" lang="en">Crs (en)</span><span class="multilang" lang="es">Crs (es)</span>',
        ]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create a report containing columns that support multilang content.
        $report = $generator->create_report(['name' => 'Courses', 'source' => courses::class, 'default' => 0]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course_category:name']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:fullname']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:coursefullnamewithlink']);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(1, $content);

        $contentrow = array_values(reset($content));
        $this->assertEquals([
            'Cat (en)',
            'Crs (en)',
            '<a href="' . (string) course_get_url($course->id) . '">Crs (en)</a>',
        ], $contentrow);
    }
}
