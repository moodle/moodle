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

use core_reportbuilder_generator;
use core_reportbuilder_testcase;
use core_reportbuilder\local\filters\{select, text};

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/reportbuilder/tests/helpers.php");

/**
 * Unit tests for course categories datasource
 *
 * @package     core_course
 * @covers      \core_course\reportbuilder\datasource\categories
 * @copyright   2023 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class categories_test extends core_reportbuilder_testcase {

    /**
     * Test default datasource
     */
    public function test_datasource_default(): void {
        $this->resetAfterTest();

        $category = $this->getDataGenerator()->create_category(['name' => 'Zoo', 'idnumber' => 'Z01']);
        $course = $this->getDataGenerator()->create_course(['category' => $category->id]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => categories::class, 'default' => 1]);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(2, $content);

        // Default columns are name, idnumber, coursecount. Sorted by name ascending.
        $this->assertEquals([
            [get_string('defaultcategoryname'), '', 0],
            [$category->get_formatted_name(), $category->idnumber, 1],
        ], array_map('array_values', $content));
    }

    /**
     * Test datasource columns that aren't added by default
     */
    public function test_datasource_non_default_columns(): void {
        global $DB;

        $this->resetAfterTest();

        $category = $this->getDataGenerator()->create_category(['name' => 'Zoo', 'idnumber' => 'Z01', 'description' => 'Animals']);
        $course = $this->getDataGenerator()->create_course(['category' => $category->id, 'fullname' => 'Zebra']);

        // Add a cohort.
        $cohort = $this->getDataGenerator()->create_cohort(['contextid' => $category->get_context()->id, 'name' => 'My cohort']);

        // Assign a role.
        $managerrole = $DB->get_field('role', 'id', ['shortname' => 'manager']);
        $user = $this->getDataGenerator()->create_user();
        role_assign($managerrole, $user->id, $category->get_context()->id);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => categories::class, 'default' => 0]);

        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course_category:namewithlink',
            'sortenabled' => 1]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course_category:path']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course_category:description']);

        // Add column from each of our entities.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:fullname']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'cohort:name']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'role:name']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:fullname']);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(2, $content);

        [$namewithlink, $path, $description, $coursename, $cohortname, $rolename, $userfullname] = array_values($content[0]);
        $this->assertStringContainsString(get_string('defaultcategoryname'), $namewithlink);
        $this->assertEquals(get_string('defaultcategoryname'), $path);
        $this->assertEmpty($description);
        $this->assertEmpty($coursename);
        $this->assertEmpty($cohortname);
        $this->assertEmpty($rolename);
        $this->assertEmpty($userfullname);

        [$namewithlink, $path, $description, $coursename, $cohortname, $rolename, $userfullname] = array_values($content[1]);
        $this->assertStringContainsString($category->get_formatted_name(), $namewithlink);
        $this->assertEquals($category->get_nested_name(false), $path);
        $this->assertEquals(format_text($category->description, $category->descriptionformat), $description);
        $this->assertEquals($course->fullname, $coursename);
        $this->assertEquals($cohort->name, $cohortname);
        $this->assertEquals('Manager', $rolename);
        $this->assertEquals(fullname($user), $userfullname);
    }

    /**
     * Data provider for {@see test_datasource_filters}
     *
     * @return array[]
     */
    public static function datasource_filters_provider(): array {
        global $DB;

        return [
            // Category.
            'Filter category (no match)' => ['course_category:name', [
                'course_category:name_value' => -1,
            ], false],
            'Filter category name' => ['course_category:text', [
                'course_category:text_operator' => text::IS_EQUAL_TO,
                'course_category:text_value' => 'Zoo',
            ], true],
            'Filter category name (no match)' => ['course_category:text', [
                'course_category:text_operator' => text::IS_EQUAL_TO,
                'course_category:text_value' => 'Plants',
            ], false],
            'Filter category idnumber' => ['course_category:idnumber', [
                'course_category:idnumber_operator' => text::IS_EQUAL_TO,
                'course_category:idnumber_value' => 'Z01',
            ], true],
            'Filter category idnumber (no match)' => ['course_category:idnumber', [
                'course_category:idnumber_operator' => text::IS_EQUAL_TO,
                'course_category:idnumber_value' => 'P01',
            ], false],

            // Course.
            'Filter course fullname' => ['course:fullname', [
                'course:fullname_operator' => text::IS_EQUAL_TO,
                'course:fullname_value' => 'Zebra',
            ], true],
            'Filter course fullname (no match)' => ['course:fullname', [
                'course:fullname_operator' => text::IS_EQUAL_TO,
                'course:fullname_value' => 'Python',
            ], false],

            // Cohort.
            'Filter cohort name' => ['cohort:name', [
                'cohort:name_operator' => text::IS_EQUAL_TO,
                'cohort:name_value' => 'My cohort',
            ], true],
            'Filter cohort name (no match)' => ['cohort:name', [
                'cohort:name_operator' => text::IS_EQUAL_TO,
                'cohort:name_value' => 'Not my cohort',
            ], false],

            // Role.
            'Filter role' => ['role:name', [
                'role:name_operator' => select::EQUAL_TO,
                'role:name_value' => $DB->get_field('role', 'id', ['shortname' => 'manager']),
            ], true],
            'Filter role (no match)' => ['role:name', [
                'role:name_operator' => select::EQUAL_TO,
                'role:name_value' => -1,
            ], false],

            // User.
            'Filter user firstname' => ['user:firstname', [
                'user:firstname_operator' => text::IS_EQUAL_TO,
                'user:firstname_value' => 'Zoe',
            ], true],
            'Filter user firstname (no match)' => ['user:firstname', [
                'user:firstname_operator' => text::IS_EQUAL_TO,
                'user:firstname_value' => 'Pedro',
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
    public function test_datasource_filters(string $filtername, array $filtervalues, bool $expectmatch): void {
        global $DB;

        $this->resetAfterTest();

        $category = $this->getDataGenerator()->create_category(['name' => 'Zoo', 'idnumber' => 'Z01']);
        $course = $this->getDataGenerator()->create_course(['category' => $category->id, 'fullname' => 'Zebra']);

        // Add a cohort.
        $cohort = $this->getDataGenerator()->create_cohort(['contextid' => $category->get_context()->id, 'name' => 'My cohort']);

        // Assign a role.
        $managerrole = $DB->get_field('role', 'id', ['shortname' => 'manager']);
        $user = $this->getDataGenerator()->create_user(['firstname' => 'Zoe']);
        role_assign($managerrole, $user->id, $category->get_context()->id);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create report containing single idnumber column, and given filter.
        $report = $generator->create_report(['name' => 'My report', 'source' => categories::class, 'default' => 0]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course_category:idnumber']);

        // Add filter, set it's values.
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => $filtername]);
        $content = $this->get_custom_report_content($report->get('id'), 0, $filtervalues);

        if ($expectmatch) {
            $this->assertCount(1, $content);
            $this->assertEquals($category->idnumber, reset($content[0]));
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

        $category = $this->getDataGenerator()->create_category(['name' => 'My category']);

        $this->datasource_stress_test_columns(categories::class);
        $this->datasource_stress_test_columns_aggregation(categories::class);
        $this->datasource_stress_test_conditions(categories::class, 'course_category:idnumber');
    }
}
