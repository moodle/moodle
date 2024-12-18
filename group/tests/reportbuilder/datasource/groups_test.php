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

namespace core_group\reportbuilder\datasource;

use core_customfield_generator;
use core_reportbuilder_generator;
use core_reportbuilder\local\filters\{boolean_select, date, select, text};
use core_reportbuilder\tests\core_reportbuilder_testcase;

/**
 * Unit tests for groups datasource
 *
 * @package     core_group
 * @covers      \core_group\reportbuilder\datasource\groups
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class groups_test extends core_reportbuilder_testcase {

    /**
     * Test default datasource
     */
    public function test_datasource_default(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $userone = $this->getDataGenerator()->create_and_enrol($course, 'student', ['firstname' => 'Zoe']);
        $usertwo = $this->getDataGenerator()->create_and_enrol($course, 'student', ['firstname' => 'Amy']);

        $groupone = $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'Zebras']);
        $grouptwo = $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'Aardvarks']);

        $this->getDataGenerator()->create_group_member(['groupid' => $groupone->id, 'userid' => $userone->id]);
        $this->getDataGenerator()->create_group_member(['groupid' => $groupone->id, 'userid' => $usertwo->id]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Groups', 'source' => groups::class, 'default' => 1]);

        $content = $this->get_custom_report_content($report->get('id'));

        // Default columns are course, group, user. Sorted by each.
        $courseurl = course_get_url($course);
        $this->assertEquals([
            ["<a href=\"{$courseurl}\">{$course->fullname}</a>", $grouptwo->name, ''],
            ["<a href=\"{$courseurl}\">{$course->fullname}</a>", $groupone->name, fullname($usertwo)],
            ["<a href=\"{$courseurl}\">{$course->fullname}</a>", $groupone->name, fullname($userone)],
        ], array_map('array_values', $content));
    }

    /**
     * Test datasource groupings reports
     */
    public function test_datasource_groupings(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        // Create group, add to grouping.
        $groupone = $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'Group A']);
        $grouping = $this->getDataGenerator()->create_grouping(['courseid' => $course->id]);
        $this->getDataGenerator()->create_grouping_group(['groupingid' => $grouping->id, 'groupid' => $groupone->id]);

        // Create second group, no grouping.
        $grouptwo = $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'Group B']);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Groups', 'source' => groups::class, 'default' => 0]);

        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:fullname']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'grouping:name']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'group:name', 'sortenabled' => 1]);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(2, $content);

        $this->assertEquals([
            [
                $course->fullname,
                $grouping->name,
                $groupone->name,
            ],
            [
                $course->fullname,
                null,
                $grouptwo->name,
            ],
        ], array_map('array_values', $content));
    }

    /**
     * Test datasource columns that aren't added by default
     */
    public function test_datasource_non_default_columns(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');

        /** @var core_customfield_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_customfield');

        // Create group with custom field, and single group member.
        $groupfieldcategory = $generator->create_category(['component' => 'core_group', 'area' => 'group']);
        $generator->create_field(['categoryid' => $groupfieldcategory->get('id'), 'shortname' => 'hi']);

        $group = $this->getDataGenerator()->create_group([
            'courseid' => $course->id,
            'idnumber' => 'G101',
            'enrolmentkey' => 'S',
            'description' => 'My group',
            'customfield_hi' => 'Hello',
        ]);
        $this->getDataGenerator()->create_group_member(['userid' => $user->id, 'groupid' => $group->id]);

        // Create grouping with custom field, and single group.
        $groupingfieldcategory = $generator->create_category(['component' => 'core_group', 'area' => 'grouping']);
        $generator->create_field(['categoryid' => $groupingfieldcategory->get('id'), 'shortname' => 'bye']);

        $grouping = $this->getDataGenerator()->create_grouping([
            'courseid' => $course->id,
            'idnumber' => 'GR101',
            'description' => 'My grouping',
            'customfield_bye' => 'Goodbye',
        ]);
        $this->getDataGenerator()->create_grouping_group(['groupingid' => $grouping->id, 'groupid' => $group->id]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Groups', 'source' => groups::class, 'default' => 0]);

        // Course (just to test join).
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:shortname']);

        // Group.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'group:idnumber']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'group:description']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'group:enrolmentkey']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'group:visibility']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'group:participation']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'group:picture']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'group:timecreated']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'group:timemodified']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'group:customfield_hi']);

        // Grouping.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'grouping:name']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'grouping:idnumber']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'grouping:description']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'grouping:timecreated']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'grouping:timemodified']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'grouping:customfield_bye']);

        // Group member.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'group_member:timeadded']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'group_member:component']);

        // User (just to test join).
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:username']);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(1, $content);

        [
            $courseshortname,
            $groupidnumber,
            $groupdescription,
            $groupenrolmentkey,
            $groupvisibility,
            $groupparticipation,
            $grouppicture,
            $grouptimecreated,
            $grouptimemodified,
            $groupcustomfield,
            $groupingname,
            $groupingidnumber,
            $groupingdescription,
            $groupingtimecreated,
            $groupingtimemodified,
            $groupingcustomfield,
            $groupmembertimeadded,
            $groupmemebercomponent,
            $userusername,
        ] = array_values($content[0]);

        $this->assertEquals($course->shortname, $courseshortname);
        $this->assertEquals('G101', $groupidnumber);
        $this->assertEquals(format_text($group->description), $groupdescription);
        $this->assertEquals('S', $groupenrolmentkey);
        $this->assertEquals('Visible', $groupvisibility);
        $this->assertEquals('Yes', $groupparticipation);
        $this->assertEmpty($grouppicture);
        $this->assertNotEmpty($grouptimecreated);
        $this->assertNotEmpty($grouptimemodified);
        $this->assertEquals('Hello', $groupcustomfield);
        $this->assertEquals($grouping->name, $groupingname);
        $this->assertEquals('GR101', $groupingidnumber);
        $this->assertEquals(format_text($grouping->description), $groupingdescription);
        $this->assertNotEmpty($groupingtimecreated);
        $this->assertNotEmpty($groupingtimemodified);
        $this->assertEquals('Goodbye', $groupingcustomfield);
        $this->assertNotEmpty($groupmembertimeadded);
        $this->assertEmpty($groupmemebercomponent);
        $this->assertEquals($user->username, $userusername);
    }

    /**
     * Data provider for {@see test_datasource_filters}
     *
     * @return array[]
     */
    public static function datasource_filters_provider(): array {
        return [
            // Course (just to test join).
            'Filter course name' => ['course:fullname', [
                'course:fullname_operator' => text::IS_EQUAL_TO,
                'course:fullname_value' => 'Test course',
            ], true],
            'Filter course name (no match)' => ['course:fullname', [
                'course:fullname_operator' => text::IS_NOT_EQUAL_TO,
                'course:fullname_value' => 'Test course',
            ], false],

            // Group.
            'Filter group name' => ['group:name', [
                'group:name_operator' => text::IS_EQUAL_TO,
                'group:name_value' => 'Test group',
            ], true],
            'Filter group name (no match)' => ['group:name', [
                'group:name_operator' => text::IS_NOT_EQUAL_TO,
                'group:name_value' => 'Test group',
            ], false],
            'Filter group idnumber' => ['group:idnumber', [
                'group:idnumber_operator' => text::IS_EQUAL_TO,
                'group:idnumber_value' => 'G101',
            ], true],
            'Filter group idnumber (no match)' => ['group:idnumber', [
                'group:idnumber_operator' => text::IS_NOT_EQUAL_TO,
                'group:idnumber_value' => 'G101',
            ], false],
            'Filter group visibility' => ['group:visibility', [
                'group:visibility_operator' => select::EQUAL_TO,
                'group:visibility_value' => 0, // Visible to everyone.
            ], true],
            'Filter group visibility (no match)' => ['group:visibility', [
                'group:visibility_operator' => select::EQUAL_TO,
                'group:visibility_value' => 1, // Visible to members only.
            ], false],
            'Filter group participation' => ['group:participation', [
                'group:participation_operator' => boolean_select::CHECKED,
            ], true],
            'Filter group participation (no match)' => ['group:participation', [
                'group:participation_operator' => boolean_select::NOT_CHECKED,
            ], false],
            'Filter group time created' => ['group:timecreated', [
                'group:timecreated_operator' => date::DATE_RANGE,
                'group:timecreated_from' => 1622502000,
            ], true],
            'Filter group time created (no match)' => ['group:timecreated', [
                'group:timecreated_operator' => date::DATE_RANGE,
                'group:timecreated_to' => 1622502000,
            ], false],

            // Grouping.
            'Filter grouping name' => ['grouping:name', [
                'grouping:name_operator' => text::IS_EQUAL_TO,
                'grouping:name_value' => 'Test grouping',
            ], true],
            'Filter grouping name (no match)' => ['grouping:name', [
                'grouping:name_operator' => text::IS_NOT_EQUAL_TO,
                'grouping:name_value' => 'Test grouping',
            ], false],
            'Filter grouping idnumber' => ['grouping:idnumber', [
                'grouping:idnumber_operator' => text::IS_EQUAL_TO,
                'grouping:idnumber_value' => 'GR101',
            ], true],
            'Filter grouping idnumber (no match)' => ['grouping:idnumber', [
                'grouping:idnumber_operator' => text::IS_NOT_EQUAL_TO,
                'grouping:idnumber_value' => 'GR101',
            ], false],
            'Filter grouping time created' => ['grouping:timecreated', [
                'grouping:timecreated_operator' => date::DATE_RANGE,
                'grouping:timecreated_from' => 1622502000,
            ], true],
            'Filter grouping time created (no match)' => ['grouping:timecreated', [
                'grouping:timecreated_operator' => date::DATE_RANGE,
                'grouping:timecreated_to' => 1622502000,
            ], false],

            // Group member.
            'Filter group member time added' => ['group_member:timeadded', [
                'group_member:timeadded_operator' => date::DATE_RANGE,
                'group_member:timeadded_from' => 1622502000,
            ], true],
            'Filter group member time added (no match)' => ['group_member:timeadded', [
                'group_member:timeadded_operator' => date::DATE_RANGE,
                'group_member:timeadded_to' => 1622502000,
            ], false],

            // User (just to test join).
            'Filter user username' => ['user:username', [
                'user:username_operator' => text::IS_EQUAL_TO,
                'user:username_value' => 'testuser',
            ], true],
            'Filter user username (no match)' => ['user:username', [
                'user:username_operator' => text::IS_NOT_EQUAL_TO,
                'user:username_value' => 'testuser',
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

        $course = $this->getDataGenerator()->create_course(['fullname' => 'Test course']);
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student', ['username' => 'testuser']);

        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id, 'idnumber' => 'G101', 'name' => 'Test group']);
        $this->getDataGenerator()->create_group_member(['userid' => $user->id, 'groupid' => $group->id]);

        $grouping = $this->getDataGenerator()->create_grouping(['courseid' => $course->id, 'idnumber' => 'GR101',
            'name' => 'Test grouping']);
        $this->getDataGenerator()->create_grouping_group(['groupingid' => $grouping->id, 'groupid' => $group->id]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create report containing single column, and given filter.
        $report = $generator->create_report(['name' => 'Groups', 'source' => groups::class, 'default' => 0]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'group:idnumber']);

        // Add filter, set it's values.
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => $filtername]);
        $content = $this->get_custom_report_content($report->get('id'), 0, $filtervalues);

        if ($expectmatch) {
            $this->assertCount(1, $content);
            $this->assertEquals('G101', reset($content[0]));
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

        $course = $this->getDataGenerator()->create_course(['fullname' => 'Test course']);
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student', ['username' => 'testuser']);

        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id, 'idnumber' => 'G101', 'name' => 'Test group']);
        $this->getDataGenerator()->create_group_member(['userid' => $user->id, 'groupid' => $group->id]);

        $grouping = $this->getDataGenerator()->create_grouping(['courseid' => $course->id, 'idnumber' => 'GR101',
            'name' => 'Test grouping']);
        $this->getDataGenerator()->create_grouping_group(['groupingid' => $grouping->id, 'groupid' => $group->id]);

        $this->datasource_stress_test_columns(groups::class);
        $this->datasource_stress_test_columns_aggregation(groups::class);
        $this->datasource_stress_test_conditions(groups::class, 'group:idnumber');
    }
}
