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
use core_reportbuilder\local\filters\boolean_select;
use core_reportbuilder\local\filters\date;
use core_reportbuilder\local\filters\select;
use core_reportbuilder\local\filters\tags;
use core_reportbuilder\local\filters\text;

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

        $contentrow = array_values($content[0]);

        $this->assertEquals([
            $category->get_formatted_name(),
            $course->shortname,
            $course->fullname,
            $course->idnumber,
        ], $contentrow);
    }

    /**
     * Test datasource columns that aren't added by default
     */
    public function test_datasource_non_default_columns(): void {
        $this->resetAfterTest();

        $category = $this->getDataGenerator()->create_category([
            'name' => 'Animals',
            'idnumber' => 'CAT101',
            'description' => 'Category description',
        ]);
        $course = $this->getDataGenerator()->create_course([
            'category' => $category->id,
            'fullname' => 'Cats',
            'summary' => 'Course description',
            'tags' => ['Horses'],
        ]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Courses', 'source' => courses::class, 'default' => 0]);

        // Category.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course_category:namewithlink']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course_category:path']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course_category:idnumber']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course_category:description']);

        // Course.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:coursefullnamewithlink']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:courseshortnamewithlink']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:courseidnumberewithlink']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:summary']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:format']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:startdate']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:enddate']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:visible']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:groupmode']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:groupmodeforce']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:lang']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:calendartype']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:theme']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:enablecompletion']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:downloadcontent']);

        // Tags.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'tag:name']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'tag:namewithlink']);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(1, $content);

        $courserow = array_values($content[0]);

        // Category.
        $this->assertStringContainsString($category->get_formatted_name(), $courserow[0]);
        $this->assertEquals($category->get_nested_name(false), $courserow[1]);
        $this->assertEquals($category->idnumber, $courserow[2]);
        $this->assertEquals(format_text($category->description, $category->descriptionformat), $courserow[3]);

        // Course.
        $this->assertStringContainsString($course->fullname, $courserow[4]);
        $this->assertStringContainsString($course->shortname, $courserow[5]);
        $this->assertStringContainsString($course->idnumber, $courserow[6]);
        $this->assertEquals(format_text($course->summary, $course->summaryformat), $courserow[7]);
        $this->assertEquals('Topics format', $courserow[8]);
        $this->assertEquals(userdate($course->startdate), $courserow[9]);
        $this->assertEmpty($courserow[10]);
        $this->assertEquals('Yes', $courserow[11]);
        $this->assertEquals('No groups', $courserow[12]);
        $this->assertEquals('No', $courserow[13]);
        $this->assertEmpty($courserow[14]);
        $this->assertEmpty($courserow[15]);
        $this->assertEmpty($courserow[16]);
        $this->assertEquals('No', $courserow[17]);
        $this->assertEmpty($courserow[18]);

        // Tags.
        $this->assertEquals('Horses', $courserow[19]);
        $this->assertStringContainsString('Horses', $courserow[20]);
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
            // Category.
            'Filter category' => ['course_category:name', [
                'course_category:name_value' => -1,
            ], false],
            'Filter category name' => ['course_category:text', [
                'course_category:text_operator' => text::IS_EQUAL_TO,
                'course_category:text_value' => 'Animals',
            ], true],
            'Filter category name (no match)' => ['course_category:text', [
                'course_category:text_operator' => text::IS_EQUAL_TO,
                'course_category:text_value' => 'Fruit',
            ], false],
            'Filter category idnumber' => ['course_category:idnumber', [
                'course_category:idnumber_operator' => text::IS_EQUAL_TO,
                'course_category:idnumber_value' => 'CAT101',
            ], true],
            'Filter category idnumber (no match)' => ['course_category:idnumber', [
                'course_category:idnumber_operator' => text::CONTAINS,
                'course_category:idnumber_value' => 'FRUIT',
            ], false],

            // Course.
            'Filter course' => ['course:courseselector', [
                'course:courseselector_values' => [-1],
            ], false],
            'Filter course fullname' => ['course:fullname', [
                'course:fullname_operator' => text::IS_EQUAL_TO,
                'course:fullname_value' => 'Equine',
            ], true],
            'Filter course fullname (no match)' => ['course:fullname', [
                'course:fullname_operator' => text::IS_EQUAL_TO,
                'course:fullname_value' => 'Foxes',
            ], false],
            'Filter course shortname' => ['course:shortname', [
                'course:shortname_operator' => text::IS_EQUAL_TO,
                'course:shortname_value' => 'EQ101',
            ], true],
            'Filter course shortname (no match)' => ['course:shortname', [
                'course:shortname_operator' => text::IS_EQUAL_TO,
                'course:shortname_value' => 'FX101',
            ], false],
            'Filter course idnumber' => ['course:idnumber', [
                'course:idnumber_operator' => text::IS_EQUAL_TO,
                'course:idnumber_value' => 'E-101AB',
            ], true],
            'Filter course idnumber (no match)' => ['course:idnumber', [
                'course:idnumber_operator' => text::IS_EQUAL_TO,
                'course:idnumber_value' => 'F-101XT',
            ], false],
            'Filter course summary' => ['course:summary', [
                'course:summary_operator' => text::CONTAINS,
                'course:summary_value' => 'Lorem ipsum',
            ], true],
            'Filter course summary (no match)' => ['course:summary', [
                'course:summary_operator' => text::IS_EQUAL_TO,
                'course:summary_value' => 'Fiat',
            ], false],
            'Filter course format' => ['course:format', [
                'course:format_operator' => select::EQUAL_TO,
                'course:format_value' => 'topics',
            ], true],
            'Filter course format (no match)' => ['course:format', [
                'course:format_operator' => select::EQUAL_TO,
                'course:format_value' => 'weekly',
            ], false],
            'Filter course startdate' => ['course:startdate', [
                'course:startdate_operator' => date::DATE_RANGE,
                'course:startdate_from' => 1622502000,
            ], true],
            'Filter course startdate (no match)' => ['course:startdate', [
                'course:startdate_operator' => date::DATE_RANGE,
                'course:startdate_to' => 1622502000,
            ], false],
            'Filter course enddate' => ['course:enddate', [
                'course:enddate_operator' => date::DATE_EMPTY,
            ], true],
            'Filter course enddate (no match)' => ['course:enddate', [
                'course:enddate_operator' => date::DATE_NOT_EMPTY,
            ], false],
            'Filter course visible' => ['course:visible', [
                'course:visible_operator' => boolean_select::CHECKED,
            ], true],
            'Filter course visible (no match)' => ['course:visible', [
                'course:visible_operator' => boolean_select::NOT_CHECKED,
            ], false],
            'Filter course groupmode' => ['course:groupmode', [
                'course:groupmode_operator' => select::EQUAL_TO,
                'course:groupmode_value' => 0, // No groups.
            ], true],
            'Filter course groupmode (no match)' => ['course:groupmode', [
                'course:groupmode_operator' => select::EQUAL_TO,
                'course:groupmode_value' => 1, // Separate groups.
            ], false],
            'Filter course groupmodeforce' => ['course:groupmodeforce', [
                'course:groupmodeforce_operator' => boolean_select::NOT_CHECKED,
            ], true],
            'Filter course groupmodeforce (no match)' => ['course:groupmodeforce', [
                'course:groupmodeforce_operator' => boolean_select::CHECKED,
            ], false],
            'Filter course lang' => ['course:lang', [
                'course:lang_operator' => select::EQUAL_TO,
                'course:lang_value' => 'en',
            ], true],
            'Filter course lang (no match)' => ['course:lang', [
                'course:lang_operator' => select::EQUAL_TO,
                'course:lang_value' => 'de',
            ], false],
            'Filter course calendartype' => ['course:calendartype', [
                'course:calendartype_operator' => select::EQUAL_TO,
                'course:calendartype_value' => 'gregorian',
            ], true],
            'Filter course calendartype (no match)' => ['course:calendartype', [
                'course:calendartype_operator' => select::EQUAL_TO,
                'course:calendartype_value' => 'hijri',
            ], false],
            'Filter course theme' => ['course:theme', [
                'course:theme_operator' => select::EQUAL_TO,
                'course:theme_value' => 'boost',
            ], true],
            'Filter course theme (no match)' => ['course:theme', [
                'course:theme_operator' => select::EQUAL_TO,
                'course:theme_value' => 'classic',
            ], false],
            'Filter course enablecompletion' => ['course:enablecompletion', [
                'course:enablecompletion_operator' => boolean_select::NOT_CHECKED,
            ], true],
            'Filter course enablecompletion (no match)' => ['course:enablecompletion', [
                'course:enablecompletion_operator' => boolean_select::CHECKED,
            ], false],
            'Filter course downloadcontent' => ['course:downloadcontent', [
                'course:downloadcontent_operator' => boolean_select::CHECKED,
            ], true],
            'Filter course downloadcontent (no match)' => ['course:downloadcontent', [
                'course:downloadcontent_operator' => boolean_select::NOT_CHECKED,
            ], false],

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
    public function test_datasource_filters(string $filtername, array $filtervalues, bool $expectmatch): void {
        $this->resetAfterTest();

        $category = $this->getDataGenerator()->create_category(['name' => 'Animals', 'idnumber' => 'CAT101']);
        $course = $this->getDataGenerator()->create_course([
            'category' => $category->id,
            'fullname' => 'Equine',
            'shortname' => 'EQ101',
            'idnumber' => 'E-101AB',
            'lang' => 'en',
            'calendartype' => 'gregorian',
            'theme' => 'boost',
            'downloadcontent' => 1,
            'tags' => ['Horses'],
        ]);

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
