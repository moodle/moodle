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

namespace core_tag\reportbuilder\datasource;

use context_course;
use context_user;
use core_collator;
use core_reportbuilder_generator;
use core_reportbuilder_testcase;
use core_reportbuilder\local\filters\{boolean_select, date, select};
use core_reportbuilder\local\filters\tags as tags_filter;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/reportbuilder/tests/helpers.php");

/**
 * Unit tests for tags datasource
 *
 * @package     core_tag
 * @covers      \core_tag\reportbuilder\datasource\tags
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tags_test extends core_reportbuilder_testcase {

    /**
     * Test default datasource
     */
    public function test_datasource_default(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['tags' => ['Horses']]);
        $coursecontext = context_course::instance($course->id);

        $user = $this->getDataGenerator()->create_user(['interests' => ['Pies']]);
        $usercontext = context_user::instance($user->id);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Notes', 'source' => tags::class, 'default' => 1]);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(2, $content);

        // Consistent order (course, user), just in case.
        core_collator::asort_array_of_arrays_by_key($content, 'c3_contextid');
        $content = array_values($content);

        // Default columns are collection, tag name, tag standard, instance context.
        [$courserow, $userrow] = array_map('array_values', $content);

        $this->assertEquals('Default collection', $courserow[0]);
        $this->assertStringContainsString('Horses', $courserow[1]);
        $this->assertEquals('No', $courserow[2]);
        $this->assertEquals($coursecontext->get_context_name(), $courserow[3]);

        $this->assertEquals('Default collection', $userrow[0]);
        $this->assertStringContainsString('Pies', $userrow[1]);
        $this->assertEquals('No', $courserow[2]);
        $this->assertEquals($usercontext->get_context_name(), $userrow[3]);
    }

    /**
     * Test datasource columns that aren't added by default
     */
    public function test_datasource_non_default_columns(): void {
        $this->resetAfterTest();

        $this->getDataGenerator()->create_tag(['name' => 'Horses', 'description' => 'Neigh', 'flag' => 2]);
        $course = $this->getDataGenerator()->create_course(['tags' => ['Horses']]);
        $coursecontext = context_course::instance($course->id);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Notes', 'source' => tags::class, 'default' => 0]);

        // Collection.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'collection:default']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'collection:component']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'collection:searchable']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'collection:customurl']);

        // Tag.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'tag:name']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'tag:description']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'tag:flagged']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'tag:timemodified']);

        // Instance.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'instance:contexturl']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'instance:area']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'instance:component']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'instance:itemtype']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'instance:itemid']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'instance:timecreated']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'instance:timemodified']);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(1, $content);

        $courserow = array_values($content[0]);

        // Collection.
        $this->assertEquals('Yes', $courserow[0]);
        $this->assertEmpty($courserow[1]);
        $this->assertEquals('Yes', $courserow[2]);
        $this->assertEmpty($courserow[3]);

        // Tag.
        $this->assertEquals('Horses', $courserow[4]);
        $this->assertEquals('<div class="text_to_html">Neigh</div>', $courserow[5]);
        $this->assertEquals('Yes', $courserow[6]);
        $this->assertNotEmpty($courserow[7]);

        // Instance.
        $this->assertEquals('<a href="' . $coursecontext->get_url()  . '">' . $coursecontext->get_context_name()  . '</a>',
            $courserow[8]);
        $this->assertEquals('Courses', $courserow[9]);
        $this->assertEquals('core', $courserow[10]);
        $this->assertEquals('course', $courserow[11]);
        $this->assertEquals($course->id, $courserow[12]);
        $this->assertNotEmpty($courserow[13]);
        $this->assertNotEmpty($courserow[14]);
    }

    /**
     * Data provider for {@see test_datasource_filters}
     *
     * @return array[]
     */
    public function datasource_filters_provider(): array {
        return [
            // Collection.
            'Filter collection name' => ['collection:name', [
                'collection:name_operator' => select::NOT_EQUAL_TO,
                'collection:name_value' => -1,
            ], true],
            'Filter collection default' => ['collection:default', [
                'collection:default_operator' => boolean_select::CHECKED,
            ], true],
            'Filter collection default (no match)' => ['collection:default', [
                'collection:default_operator' => boolean_select::NOT_CHECKED,
            ], false],
            'Filter collection searchable' => ['collection:searchable', [
                'collection:searchable_operator' => boolean_select::CHECKED,
            ], true],
            'Filter collection searchable (no match)' => ['collection:searchable', [
                'collection:searchable_operator' => boolean_select::NOT_CHECKED,
            ], false],

            // Tag.
            'Filter tag name equal to' => ['tag:name', [
                'tag:name_operator' => tags_filter::EQUAL_TO,
                'tag:name_value' => [-1],
            ], false],
            'Filter tag name not equal to' => ['tag:name', [
                'tag:name_operator' => tags_filter::NOT_EQUAL_TO,
                'tag:name_value' => [-1],
            ], true],
            'Filter tag name empty' => ['tag:name', [
                'tag:name_operator' => tags_filter::EMPTY,
            ], false],
            'Filter tag name not empty' => ['tag:name', [
                'tag:name_operator' => tags_filter::NOT_EMPTY,
            ], true],
            'Filter tag standard' => ['tag:standard', [
                'tag:standard_operator' => boolean_select::NOT_CHECKED,
            ], true],
            'Filter tag standard (no match)' => ['tag:standard', [
                'tag:standard_operator' => boolean_select::CHECKED,
            ], false],
            'Filter tag flagged' => ['tag:flagged', [
                'tag:flagged_operator' => boolean_select::CHECKED,
            ], true],
            'Filter tag flagged (no match)' => ['tag:flagged', [
                'tag:flagged_operator' => boolean_select::NOT_CHECKED,
            ], false],
            'Filter tag time modified' => ['tag:timemodified', [
                'tag:timemodified_operator' => date::DATE_RANGE,
                'tag:timemodified_from' => 1622502000,
            ], true],
            'Filter tag time modified (no match)' => ['tag:timemodified', [
                'tag:timemodified_operator' => date::DATE_RANGE,
                'tag:timemodified_to' => 1622502000,
            ], false],

            // Instance.
            'Filter instance tag area' => ['instance:area', [
                'instance:area_operator' => select::EQUAL_TO,
                'instance:area_value' => 'core/course',
            ], true],
            'Filter instance tag area (no match)' => ['instance:area', [
                'instance:area_operator' => select::NOT_EQUAL_TO,
                'instance:area_value' => 'core/course',
            ], false],
            'Filter instance time created' => ['instance:timecreated', [
                'instance:timecreated_operator' => date::DATE_RANGE,
                'instance:timecreated_from' => 1622502000,
            ], true],
            'Filter instance time created (no match)' => ['instance:timecreated', [
                'instance:timecreated_operator' => date::DATE_RANGE,
                'instance:timecreated_to' => 1622502000,
            ], false],
            'Filter instance time modified' => ['instance:timemodified', [
                'instance:timemodified_operator' => date::DATE_RANGE,
                'instance:timemodified_from' => 1622502000,
            ], true],
            'Filter instance time modified (no match)' => ['instance:timemodified', [
                'instance:timemodified_operator' => date::DATE_RANGE,
                'instance:timemodified_to' => 1622502000,
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

        $this->getDataGenerator()->create_tag(['name' => 'Horses', 'flag' => 2]);
        $this->getDataGenerator()->create_course(['tags' => ['Horses']]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create report containing single tag name, and given filter.
        $report = $generator->create_report(['name' => 'Tasks', 'source' => tags::class, 'default' => 0]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'tag:name']);

        // Add filter, set it's values.
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => $filtername]);
        $content = $this->get_custom_report_content($report->get('id'), 0, $filtervalues);

        if ($expectmatch) {
            $this->assertCount(1, $content);
            $this->assertEquals('Horses', reset($content[0]));
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

        $this->getDataGenerator()->create_course(['tags' => ['Horses']]);

        $this->datasource_stress_test_columns(tags::class);
        $this->datasource_stress_test_columns_aggregation(tags::class);
        $this->datasource_stress_test_conditions(tags::class, 'tag:name');
    }
}
