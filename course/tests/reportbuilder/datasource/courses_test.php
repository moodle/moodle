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

use core_customfield_generator;
use core_reportbuilder_testcase;
use core_reportbuilder_generator;
use core_reportbuilder\local\filters\tags;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/reportbuilder/tests/helpers.php");

/**
 * Unit tests for courses datasources
 *
 * @package     core_course
 * @covers      \core_course\reportbuilder\datasource\courses
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class courses_test extends core_reportbuilder_testcase {

    /**
     * Test default datasource
     */
    public function test_datasource_default(): void {
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
        $report = $generator->create_report(['name' => 'Courses', 'source' => courses::class, 'default' => 1]);

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
     * Test datasource columns that aren't added by default
     */
    public function test_datasource_non_default_columns(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['tags' => ['Horses']]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Courses', 'source' => courses::class, 'default' => 0]);

        // Category.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course_category:path']);

        // Course.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:fullname']);

        // Tags.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'tag:name']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'tag:namewithlink']);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(1, $content);

        $courserow = array_values($content[0]);
        $this->assertEquals('Category 1', $courserow[0]);
        $this->assertEquals($course->fullname, $courserow[1]);
        $this->assertEquals('Horses', $courserow[2]);
        $this->assertStringContainsString('Horses', $courserow[3]);
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

    /**
     * Data provider for {@see test_datasource_filters}
     *
     * @return array[]
     */
    public function datasource_filters_provider(): array {
        return [
            // Tags.
            'Filter tag name' => ['tag:name', [
                'tag:name_operator' => tags::EQUAL_TO,
                'tag:name_value' => [-1],
            ], false],
            'Filter tag name not empty' => ['tag:name', [
                'tag:name_operator' => tags::NOT_EMPTY,
            ], true],
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

        $course = $this->getDataGenerator()->create_course(['tags' => ['Horses']]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create report containing single column, and given filter.
        $report = $generator->create_report(['name' => 'Tasks', 'source' => courses::class, 'default' => 0]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:fullname']);

        // Add filter, set it's values.
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => $filtername]);
        $content = $this->get_custom_report_content($report->get('id'), 0, $filtervalues);

        if ($expectmatch) {
            $this->assertCount(1, $content);
            $this->assertEquals($course->fullname, reset($content[0]));
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

        /** @var core_customfield_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_customfield');
        $customfieldcategory = $generator->create_category();
        $generator->create_field(['categoryid' => $customfieldcategory->get('id'), 'shortname' => 'hi']);

        $category = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(['category' => $category->id, 'customfield_hi' => 'Hello']);

        $this->datasource_stress_test_columns(courses::class);
        $this->datasource_stress_test_columns_aggregation(courses::class);
        $this->datasource_stress_test_conditions(courses::class, 'course:idnumber');
    }
}
