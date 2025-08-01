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

use core\context\course;
use core_reportbuilder_generator;
use core_reportbuilder\local\filters\{boolean_select, date, select, tags, text};
use core_reportbuilder\tests\core_reportbuilder_testcase;

/**
 * Unit tests for courses datasources
 *
 * @package     core_course
 * @covers      \core_course\reportbuilder\datasource\courses
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class courses_test extends core_reportbuilder_testcase {

    /**
     * Test default datasource
     */
    public function test_datasource_default(): void {
        $this->resetAfterTest();

        // Test subject.
        $category = $this->getDataGenerator()->create_category(['name' => 'My cats']);
        $courseone = $this->getDataGenerator()->create_course([
            'category' => $category->id,
            'fullname' => 'Feline fine',
            'shortname' => 'C102',
            'idnumber' => 'CAT102'
        ]);
        $coursetwo = $this->getDataGenerator()->create_course([
            'category' => $category->id,
            'fullname' => 'All about cats',
            'shortname' => 'C101',
            'idnumber' => 'CAT101'
        ]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Courses', 'source' => courses::class, 'default' => 1]);

        $content = $this->get_custom_report_content($report->get('id'));

        // Default columns are category, shortname, fullname, idnumber. Sorted by category, shortname, fullname.
        $this->assertEquals([
            [$category->name, $coursetwo->shortname, $coursetwo->fullname, $coursetwo->idnumber],
            [$category->name, $courseone->shortname, $courseone->fullname, $courseone->idnumber],
        ], array_map('array_values', $content));
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
            'lang' => 'en',
            'theme' => 'boost',
            'tags' => ['Horses'],
        ]);

        // Add a course image.
        get_file_storage()->create_file_from_string([
            'contextid' => course::instance($course->id)->id,
            'component' => 'course',
            'filearea' => 'overviewfiles',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'HelloWorld.jpg',
        ], 'HelloWorld');

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Courses', 'source' => courses::class, 'default' => 0]);

        // Course.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:coursefullnamewithlink']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:courseshortnamewithlink']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:courseidnumberewithlink']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:url']);
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
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:timecreated']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:timemodified']);

        // Tags.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'tag:name']);

        // File entity.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'file:name']);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(1, $content);

        [
            $coursenamewithlink,
            $courseshortnamewithlink,
            $courseidnumberwithlink,
            $courseurl,
            $coursesummary,
            $courseformat,
            $coursestartdate,
            $courseenddate,
            $coursevisible,
            $coursegroupmode,
            $coursegroupmodeforce,
            $courselang,
            $coursecalendar,
            $coursetheme,
            $coursecompletion,
            $coursedownload,
            $coursetimecreated,
            $coursetimemodified,
            $tagname,
            $filename,
        ] = array_values($content[0]);

        // Course.
        $expectedcourseurl = (string) course_get_url($course);
        $this->assertEquals("<a href=\"{$expectedcourseurl}\">{$course->fullname}</a>", $coursenamewithlink);
        $this->assertEquals("<a href=\"{$expectedcourseurl}\">{$course->shortname}</a>", $courseshortnamewithlink);
        $this->assertEquals("<a href=\"{$expectedcourseurl}\">{$course->idnumber}</a>", $courseidnumberwithlink);
        $this->assertEquals($expectedcourseurl, $courseurl);
        $this->assertEquals(format_text($course->summary, $course->summaryformat), $coursesummary);
        $this->assertEquals('Custom sections', $courseformat);
        $this->assertEquals(userdate($course->startdate), $coursestartdate);
        $this->assertEmpty($courseenddate);
        $this->assertEquals('Yes', $coursevisible);
        $this->assertEquals('No groups', $coursegroupmode);
        $this->assertEquals('No', $coursegroupmodeforce);
        $this->assertEquals('English ‎(en)‎', $courselang);
        $this->assertEquals('Do not force', $coursecalendar);
        $this->assertEquals('Boost', $coursetheme);
        $this->assertEquals('No', $coursecompletion);
        $this->assertEmpty($coursedownload);
        $this->assertEquals(userdate($course->timecreated), $coursetimecreated);
        $this->assertEquals(userdate($course->timemodified), $coursetimemodified);

        // Tags.
        $this->assertEquals('Horses', $tagname);

        // File.
        $this->assertEquals('HelloWorld.jpg', $filename);
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
    public static function datasource_filters_provider(): array {
        return [
            // Category.
            'Filter category name' => ['course_category:text', [
                'course_category:text_operator' => text::IS_EQUAL_TO,
                'course_category:text_value' => 'Animals',
            ], true],
            'Filter category name (no match)' => ['course_category:text', [
                'course_category:text_operator' => text::IS_EQUAL_TO,
                'course_category:text_value' => 'Fruit',
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
                'course:format_value' => 'weeks',
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
                'course:lang_value' => '',
            ], false],
            'Filter course calendartype' => ['course:calendartype', [
                'course:calendartype_operator' => select::EQUAL_TO,
                'course:calendartype_value' => 'gregorian',
            ], true],
            'Filter course calendartype (no match)' => ['course:calendartype', [
                'course:calendartype_operator' => select::EQUAL_TO,
                'course:calendartype_value' => '',
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
            'Filter course timecreated' => ['course:timecreated', [
                'course:timecreated_operator' => date::DATE_RANGE,
                'course:timecreated_from' => 1622502000,
            ], true],
            'Filter course timecreated (no match)' => ['course:timecreated', [
                'course:timecreated_operator' => date::DATE_RANGE,
                'course:timecreated_to' => 1622502000,
            ], false],
            'Filter course timemodified' => ['course:timemodified', [
                'course:timemodified_operator' => date::DATE_RANGE,
                'course:timemodified_from' => 1622502000,
            ], true],
            'Filter course timemodified (no match)' => ['course:timemodified', [
                'course:timemodified_operator' => date::DATE_RANGE,
                'course:timemodified_to' => 1622502000,
            ], false],

            // Tags.
            'Filter tag name' => ['tag:name', [
                'tag:name_operator' => tags::EQUAL_TO,
                'tag:name_value' => [-1],
            ], false],
            'Filter tag name not empty' => ['tag:name', [
                'tag:name_operator' => tags::NOT_EMPTY,
            ], true],

            // File.
            'Filter file name empty' => ['file:name', [
                'file:name_operator' => text::IS_EMPTY,
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

        $category = $this->getDataGenerator()->create_category(['name' => 'Animals']);
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
        $report = $generator->create_report(['name' => 'Courses', 'source' => courses::class, 'default' => 0]);
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

        $category = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(['category' => $category->id]);

        $this->datasource_stress_test_columns(courses::class);
        $this->datasource_stress_test_columns_aggregation(courses::class);
        $this->datasource_stress_test_conditions(courses::class, 'course:idnumber');
    }
}
