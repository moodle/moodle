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

namespace core_role\reportbuilder\datasource;

use core\context\course;
use core_reportbuilder_generator;
use core_reportbuilder_testcase;
use core_reportbuilder\local\filters\{date, select, text};

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/reportbuilder/tests/helpers.php");

/**
 * Unit tests for roles datasource
 *
 * @package     core_role
 * @covers      \core_role\reportbuilder\datasource\roles;
 * @copyright   2024 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class roles_test extends core_reportbuilder_testcase {

    /**
     * Test default datasource
     */
    public function test_datasource_default(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $context = course::instance($course->id);

        $studentone = $this->getDataGenerator()->create_and_enrol($course, 'student', ['firstname' => 'Zoe']);
        $studenttwo = $this->getDataGenerator()->create_and_enrol($course, 'student', ['firstname' => 'Amy']);
        $manager = $this->getDataGenerator()->create_and_enrol($course, 'manager');

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Roles', 'source' => roles::class, 'default' => 1]);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(3, $content);

        // Default columns are context link, original role name and user link. Sorted by each.
        [$contextlink, $rolename, $userlink] = array_values($content[0]);
        $this->assertStringContainsString($context->get_context_name(), $contextlink);
        $this->assertEquals('Manager', $rolename);
        $this->assertStringContainsString(fullname($manager), $userlink);

        [$contextlink, $rolename, $userlink] = array_values($content[1]);
        $this->assertStringContainsString($context->get_context_name(), $contextlink);
        $this->assertEquals('Student', $rolename);
        $this->assertStringContainsString(fullname($studenttwo), $userlink);

        [$contextlink, $rolename, $userlink] = array_values($content[2]);
        $this->assertStringContainsString($context->get_context_name(), $contextlink);
        $this->assertEquals('Student', $rolename);
        $this->assertStringContainsString(fullname($studentone), $userlink);
    }

    /**
     * Test datasource columns that aren't added by default
     */
    public function test_datasource_non_default_columns(): void {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $context = course::instance($course->id);

        // Create an alias for our role.
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'manager']);
        $DB->insert_record('role_names', (object) [
            'contextid' => $context->id,
            'roleid' => $roleid,
            'name' => 'Moocher',
        ]);

        $manager = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($manager->id, $course->id, $roleid);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Roles', 'source' => roles::class, 'default' => 0]);

        // Role.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'role:name']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'role:shortname']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'role:description']);

        // Role assignment.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'role_assignment:timemodified']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'role_assignment:component']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'role_assignment:itemid']);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(1, $content);

        [$rolename, $roleshortname, $roledescription, $timemodified, $component, $itemid] = array_values($content[0]);

        // Role.
        $this->assertEquals('Moocher (Manager)', $rolename);
        $this->assertEquals('manager', $roleshortname);
        $this->assertEquals('Managers can access courses and modify them, but usually do not participate in them.',
            $roledescription);

        // Role assignment.
        $this->assertNotEmpty($timemodified);
        $this->assertEquals('', $component);
        $this->assertEquals(0, $itemid);
    }

    /**
     * Data provider for {@see test_datasource_filters}
     *
     * @return array[]
     */
    public static function datasource_filters_provider(): array {
        global $DB;

        return [
            // Role.
            'Filter role name' => ['role:name', [
                'role:name_operator' => select::EQUAL_TO,
                'role:name_value' => $DB->get_field('role', 'id', ['shortname' => 'student']),
            ], true],
            'Filter role name (no match)' => ['role:name', [
                'role:name_operator' => select::EQUAL_TO,
                'role:name_value' => -1,
            ], false],

            // Role assignment.
            'Filter role assignment time modified' => ['role_assignment:timemodified', [
                'role_assignment:timemodified_operator' => date::DATE_RANGE,
                'role_assignment:timemodified_from' => 1622502000,
            ], true],
            'Filter role assignment time modified (no match)' => ['role_assignment:timemodified', [
                'role_assignment:timemodified_operator' => date::DATE_RANGE,
                'role_assignment:timemodified_to' => 1622502000,
            ], false],

            // Context.
            'Filter context level' => ['context:level', [
                'context:level_operator' => select::EQUAL_TO,
                'context:level_value' => CONTEXT_COURSE,
            ], true],
            'Filter context level (no match)' => ['context:level', [
                'context:level_operator' => select::EQUAL_TO,
                'context:level_value' => CONTEXT_COURSECAT,
            ], false],

            // User.
            'Filter user firstname' => ['user:firstname', [
                'user:firstname_operator' => text::IS_EQUAL_TO,
                'user:firstname_value' => 'Zoe',
            ], true],
            'Filter user firstname (no match)' => ['user:firstname', [
                'user:firstname_operator' => text::IS_EQUAL_TO,
                'user:firstname_value' => 'Amy',
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
        bool $expectmatch,
    ): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->create_and_enrol($course, 'student', ['firstname' => 'Zoe']);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create report containing single column, and given filter.
        $report = $generator->create_report(['name' => 'Roles', 'source' => roles::class, 'default' => 0]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'role:shortname']);

        // Add filter, set it's values.
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => $filtername]);
        $content = $this->get_custom_report_content($report->get('id'), 0, $filtervalues);

        if ($expectmatch) {
            $this->assertCount(1, $content);
            $this->assertEquals('student', reset($content[0]));
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

        $course = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->create_and_enrol($course);

        $this->datasource_stress_test_columns(roles::class);
        $this->datasource_stress_test_columns_aggregation(roles::class);
        $this->datasource_stress_test_conditions(roles::class, 'role:shortname');
    }
}
