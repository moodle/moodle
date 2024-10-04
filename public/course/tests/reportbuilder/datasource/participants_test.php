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

use completion_completion;
use completion_criteria_self;
use core_reportbuilder_generator;
use core_reportbuilder\local\filters\{boolean_select, date, duration, select, text};
use core_reportbuilder\tests\core_reportbuilder_testcase;
use core\user;
use grade_item;

/**
 * Course participants datasource tests
 *
 * @package     core_course
 * @covers      \core_course\reportbuilder\datasource\participants
 * @copyright   2022 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class participants_test extends core_reportbuilder_testcase {

    /**
     * Load required test libraries
     */
    public static function setUpBeforeClass(): void {
        global $CFG;

        require_once("{$CFG->libdir}/gradelib.php");
        require_once("{$CFG->dirroot}/completion/criteria/completion_criteria_self.php");
        parent::setUpBeforeClass();
    }

    /**
     * Test default datasource
     */
    public function test_datasource_default(): void {
        global $DB;

        $this->resetAfterTest();

        // Course one, two manually enrolled users.
        $courseone = $this->getDataGenerator()->create_course(['fullname' => 'Zebras']);
        $userone = $this->getDataGenerator()->create_and_enrol($courseone, 'student', ['firstname' => 'Zoe']);
        $usertwo = $this->getDataGenerator()->create_and_enrol($courseone, 'student', ['firstname' => 'Amy']);

        // Course two, two self enrolled users (one inactive).
        $coursetwo = $this->getDataGenerator()->create_course(['fullname' => 'Aardvarks']);

        $enrol = $DB->get_record('enrol', ['courseid' => $coursetwo->id, 'enrol' => 'self']);
        enrol_get_plugin($enrol->enrol)->update_status($enrol, ENROL_INSTANCE_ENABLED);

        $this->getDataGenerator()->enrol_user($userone->id, $coursetwo->id, null, 'self');
        $this->getDataGenerator()->enrol_user($usertwo->id, $coursetwo->id, null, 'self', 0, 0, ENROL_USER_SUSPENDED);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Participants', 'source' => participants::class, 'default' => 1]);

        $content = $this->get_custom_report_content($report->get('id'));

        // Default columns are course, user, method. Sorted by each.
        $courseoneurl = course_get_url($courseone);
        $coursetwourl = course_get_url($coursetwo);

        $useroneurl = user::get_profile_url($userone);
        $usertwourl = user::get_profile_url($usertwo);

        $this->assertEquals([
            ["<a href=\"{$coursetwourl}\">{$coursetwo->fullname}</a>",
                "<a href=\"{$useroneurl}\">" . fullname($userone) . "</a>", 'Self enrolment (Student)'],
            ["<a href=\"{$courseoneurl}\">{$courseone->fullname}</a>",
                "<a href=\"{$usertwourl}\">" . fullname($usertwo) . "</a>", 'Manual enrolments'],
            ["<a href=\"{$courseoneurl}\">{$courseone->fullname}</a>",
                "<a href=\"{$useroneurl}\">" . fullname($userone) . "</a>", 'Manual enrolments'],
        ], array_map('array_values', $content));
    }

    /**
     * Test datasource columns that aren't added by default
     */
    public function test_datasource_non_default_columns(): void {
        global $DB;
        $this->resetAfterTest();

        $timestart = time() - DAYSECS;
        $timeend = $timestart + 3 * DAYSECS;
        $timecompleted = $timestart + 2 * DAYSECS;
        $timelastaccess = time() + 4 * DAYSECS;

        $category = $this->getDataGenerator()->create_category(['name' => 'Music']);
        $course = $this->getDataGenerator()->create_course([
            'category' => $category->id,
            'fullname' => 'All about Lionel at the work place',
            'enablecompletion' => true,
            'startdate' => $timestart,
            'enddate' => $timeend,
        ]);

        $user1 = self::getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student',
            'manual', $timestart, $timeend, ENROL_USER_ACTIVE);

        // Add them to a group.
        $group = self::getDataGenerator()->create_group(['courseid' => $course->id]);
        self::getDataGenerator()->create_group_member(['groupid' => $group->id, 'userid' => $user1->id]);

        // Create self completion, mark as complete for the user.
        $criteriaconfig = (object) ['id' => $course->id, 'criteria_self' => true];
        (new completion_criteria_self())->update_config($criteriaconfig);

        $ccompletion = new completion_completion(['course' => $course->id, 'userid' => $user1->id]);
        $ccompletion->mark_enrolled($timestart);
        $ccompletion->mark_complete($timecompleted);

        // Update final grade for the user.
        $courseitem = grade_item::fetch_course_item($course->id);
        $courseitem->update_final_grade($user1->id, 42.5);

        // Add some cohort data.
        $cohort = $this->getDataGenerator()->create_cohort(['name' => 'My cohort']);
        cohort_add_member($cohort->id, $user1->id);

        // Set some last access value for the user in the course.
        $DB->insert_record('user_lastaccess',
            ['userid' => $user1->id, 'courseid' => $course->id, 'timeaccess' => $timelastaccess]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Participants', 'source' => participants::class, 'default' => false]);

        $generator->create_column(['reportid' => $report->get('id'),
            'uniqueidentifier' => 'course:fullname']);
        $generator->create_column(['reportid' => $report->get('id'),
            'uniqueidentifier' => 'course_category:name']);
        $generator->create_column(['reportid' => $report->get('id'),
            'uniqueidentifier' => 'user:fullname']);

        // Enrol entity (report ordering by enrolment name).
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'enrol:name', 'sortenabled' => 1]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'enrol:plugin']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'enrol:enabled']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'enrol:period']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'enrol:startdate']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'enrol:enddate']);

        // Role entity.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'role:name']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'role:shortname']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'role:description']);

        $generator->create_column(['reportid' => $report->get('id'),
            'uniqueidentifier' => 'group:name']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'cohort:name']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'completion:criteria']);
        $generator->create_column(['reportid' => $report->get('id'),
            'uniqueidentifier' => 'completion:completed']);
        $generator->create_column(['reportid' => $report->get('id'),
            'uniqueidentifier' => 'access:timeaccess']);
        $generator->create_column(['reportid' => $report->get('id'),
            'uniqueidentifier' => 'completion:progresspercent']);
        $generator->create_column(['reportid' => $report->get('id'),
            'uniqueidentifier' => 'completion:timeenrolled']);
        $generator->create_column(['reportid' => $report->get('id'),
            'uniqueidentifier' => 'completion:timestarted']);
        $generator->create_column(['reportid' => $report->get('id'),
            'uniqueidentifier' => 'completion:timecompleted']);
        $generator->create_column(['reportid' => $report->get('id'),
            'uniqueidentifier' => 'completion:reaggregate']);
        $generator->create_column(['reportid' => $report->get('id'),
            'uniqueidentifier' => 'completion:dayscourse']);
        $generator->create_column(['reportid' => $report->get('id'),
            'uniqueidentifier' => 'completion:daysuntilcompletion']);
        $generator->create_column(['reportid' => $report->get('id'),
            'uniqueidentifier' => 'completion:grade']);

        // It should get 3 records (manual enrolment, self and guest).
        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(3, $content);

        // Filter by Manual enrolment method.
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => 'enrol:plugin']);
        $content = $this->get_custom_report_content($report->get('id'), 30, [
            'enrol:plugin_operator' => select::EQUAL_TO,
            'enrol:plugin_value' => 'manual',
        ]);

        $this->assertCount(1, $content);

        $this->assertEquals([
            'All about Lionel at the work place', // Course name.
            'Music', // Course category name.
            fullname($user1), // User fullname.
            'Manual enrolments', // Enrolment method.
            'Manual enrolments', // Enrolment plugin.
            'Yes', // Enrolment enabled.
            '', // Enrolment period.
            '', // Enrolment start date.
            '', // Enrolment end date.
            'Student', // Role name.
            'student', // Role shortname.
            'Students generally have fewer privileges within a course.', // Role description.
            $group->name, // Group name.
            $cohort->name, // Cohort name.
            "All criteria below are required<ul>\n<li>Self completion: Self completion</li>\n</ul>", // Completion criteria.
            'Yes', // Course completed.
            userdate($timelastaccess), // Time last access.
            '100.0%', // Progress percentage.
            userdate($timestart), // Time enrolled.
            '', // Time started.
            userdate($timecompleted), // Time completed.
            '', // Reagreggate.
            '2 days', // Days taking course.
            '2 days', // Days until completion.
            '42.50', // Grade.
        ], array_values($content[0]));
    }


    /**
     * Test creating participants report, with aggregated last access date (minimum and maximum)
     */
    public function test_course_last_access_aggregation(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        $userone = $this->getDataGenerator()->create_and_enrol($course);
        $useronelastaccess = $this->getDataGenerator()->create_user_course_lastaccess($userone, $course, 1622502000);

        $usertwo = $this->getDataGenerator()->create_and_enrol($course);
        $usertwolastaccess = $this->getDataGenerator()->create_user_course_lastaccess($usertwo, $course, 1622847600);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report(['name' => 'Participants', 'source' => participants::class, 'default' => 0]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:fullname']);
        $column = $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'access:timeaccess']);

        // Course aggregated with "Minimum" last access.
        $column->set('aggregation', 'min')->update();
        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertEquals([
            [$course->fullname, userdate($useronelastaccess->timeaccess)],
        ], array_map('array_values', $content));

        // Course aggregated with "Maximum" last access.
        $column->set('aggregation', 'max')->update();
        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertEquals([
            [$course->fullname, userdate($usertwolastaccess->timeaccess)],
        ], array_map('array_values', $content));
    }

    /**
     * Test creating participants report, with aggregated days taking course column
     */
    public function test_completion_days_taking_course_aggregation(): void {
        $this->resetAfterTest();

        $courseone = $this->getDataGenerator()->create_course(['fullname' => 'Course 1', 'startdate' => 1622502000]);
        $coursetwo = $this->getDataGenerator()->create_course(['fullname' => 'Course 2']);

        // User one completed the course in two days.
        $userone = $this->getDataGenerator()->create_and_enrol($courseone);
        $completion = new completion_completion(['course' => $courseone->id, 'userid' => $userone->id]);
        $completion->mark_complete(1622502000 + (2 * DAYSECS));

        // User two completed the course in three days (lazy bum).
        $usertwo = $this->getDataGenerator()->create_and_enrol($courseone);
        $completion = new completion_completion(['course' => $courseone->id, 'userid' => $usertwo->id]);
        $completion->mark_complete(1622502000 + (3 * DAYSECS));

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report(['name' => 'Participants', 'source' => participants::class, 'default' => 0]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course:fullname', 'sortenabled' => 1]);
        $generator->create_column([
            'reportid' => $report->get('id'),
            'uniqueidentifier' => 'completion:dayscourse',
            'aggregation' => 'avg',
        ]);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertEquals([
            [$courseone->fullname, '2 days 12 hours'],
            [$coursetwo->fullname, ''],
        ], array_map('array_values', $content));
    }

    /**
     * Data provider for {@see test_datasource_filters}
     *
     * @return array
     */
    public static function datasource_filters_provider(): array {
        global $DB;

        return [
            [
                'enrolment:status',
                [
                    'enrolment:status_operator' => select::EQUAL_TO,
                    'enrolment:status_value' => 1,
                ],
                ['Luna'],
            ],
            [
                'enrolment:timecreated',
                [
                    'enrolment:timecreated_operator' => date::DATE_CURRENT,
                    'enrolment:timecreated_unit' => date::DATE_UNIT_DAY,
                ],
                ['Kira'],
            ],
            [
                'enrolment:timestarted',
                [
                    'enrolment:timestarted_operator' => date::DATE_CURRENT,
                    'enrolment:timecreated_unit' => date::DATE_UNIT_DAY,
                ],
                ['Luna'],
            ],
            [
                'enrolment:timeended',
                [
                    'enrolment:timeended_operator' => date::DATE_CURRENT,
                    'enrolment:timeended_unit' => date::DATE_UNIT_DAY,
                ],
                ['Luna'],
            ],
            [
                'enrol:enabled',
                [
                    'completion:enabled_operator' => boolean_select::CHECKED,
                ],
                ['Lionel', 'Kira', 'Luna'],
            ],
            [
                'enrol:period',
                [
                    'enrol:period_operator' => duration::DURATION_MAXIMUM,
                    'enrol:period_unit' => MINSECS,
                    'enrol:period_value' => 2,
                ],
                ['Lionel', 'Kira', 'Luna'],
            ],
            [
                'enrol:startdate',
                [
                    'enrol:startdate_operator' => date::DATE_EMPTY,
                ],
                ['Lionel', 'Kira', 'Luna'],
            ],
            [
                'enrol:enddate',
                [
                    'enrol:enddate_operator' => date::DATE_EMPTY,
                ],
                ['Lionel', 'Kira', 'Luna'],
            ],
            [
                'enrol:customname',
                [
                    'enrol:customname_operator' => text::IS_EMPTY,
                ],
                ['Luna', 'Kira', 'Lionel'],
            ],
            [
                'enrol:customname',
                [
                    'enrol:customname_operator' => text::IS_EQUAL_TO,
                    'enrol:customname_value' => 'All night long'
                ],
                [],
            ],
            [
                'role:name',
                [
                    'role:name_operator' => select::EQUAL_TO,
                    'role:name_value' => $DB->get_field('role', 'id', ['shortname' => 'editingteacher']),
                ],
                ['Luna'],
            ],
            [
                'group:name',
                [
                    'group:name_operator' => text::IS_EQUAL_TO,
                    'group:name_value' => 'My group',
                ],
                ['Lionel'],
            ],
            [
                'cohort:name',
                [
                    'cohort:name_operator' => text::IS_EQUAL_TO,
                    'cohort:name_value' => 'My cohort',
                ],
                ['Kira'],
            ],
            [
                'completion:completed',
                [
                    'completion:completed_operator' => boolean_select::CHECKED,
                ],
                ['Lionel'],
            ],
            [
                'completion:timecompleted',
                [
                    'completion:timecompleted_operator' => date::DATE_NOT_EMPTY,
                ],
                ['Lionel'],
            ],
            [
                'completion:timeenrolled',
                [
                    'completion:timeenrolled_operator' => date::DATE_NOT_EMPTY,
                ],
                ['Lionel'],
            ],
            [
                'completion:timestarted',
                [
                    'completion:timestarted_operator' => date::DATE_NOT_EMPTY,
                ],
                ['Lionel'],
            ],
            [
                'completion:reaggregate',
                [
                    'completion:reaggregate_operator' => date::DATE_NOT_EMPTY,
                ],
                ['Lionel'],
            ],
        ];
    }

    /**
     * Test getting filter SQL
     *
     * @param string $filter
     * @param array $filtervalues
     * @param string[] $expected
     *
     * @dataProvider datasource_filters_provider
     */
    public function test_datasource_filters(string $filter, array $filtervalues, array $expected): void {
        global $DB;
        $this->resetAfterTest();

        $timestart = time() - DAYSECS;
        $timeend = $timestart + 3 * DAYSECS;
        $timecompleted = $timestart + 2 * DAYSECS;
        $timelastaccess = time() + 4 * DAYSECS;

        $category = $this->getDataGenerator()->create_category(['name' => 'Music']);
        $course = $this->getDataGenerator()->create_course([
            'category' => $category->id,
            'fullname' => 'All about Lionel at the work place',
            'enablecompletion' => true,
            'startdate' => $timestart,
            'enddate' => $timeend,
        ]);

        $user1 = self::getDataGenerator()->create_user(['firstname' => 'Lionel']);
        $user2 = self::getDataGenerator()->create_user(['firstname' => 'Kira']);
        $user3 = self::getDataGenerator()->create_user(['firstname' => 'Luna']);

        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student',
            'manual', $timestart - 8 * DAYSECS, $timeend, ENROL_USER_ACTIVE);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student',
            'manual', $timestart, $timeend, ENROL_USER_ACTIVE);
        $this->getDataGenerator()->enrol_user($user3->id, $course->id, 'editingteacher',
            'manual', time(), time(), ENROL_USER_SUSPENDED);

        // Add user1 to a group.
        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'My group']);
        $this->getDataGenerator()->create_group_member(['groupid' => $group->id, 'userid' => $user1->id]);

        // Add some cohort data.
        $cohort = $this->getDataGenerator()->create_cohort(['name' => 'My cohort']);
        cohort_add_member($cohort->id, $user2->id);

        // Mark course as completed for the user.
        $ccompletion = new completion_completion(array('course' => $course->id, 'userid' => $user1->id));
        $ccompletion->mark_enrolled($timestart);
        $ccompletion->mark_inprogress($timestart);
        $ccompletion->mark_complete($timecompleted);

        // Set some last access value for the user in the course.
        $DB->insert_record('user_lastaccess',
            ['userid' => $user1->id, 'courseid' => $course->id, 'timeaccess' => $timelastaccess]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Participants', 'source' => participants::class, 'default' => false]);

        // Add user firstname column to the report.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:firstname']);

        $DB->set_field('user_enrolments', 'timecreated', 0, ['userid' => $user1->id]);
        $DB->set_field('user_enrolments', 'timecreated', 0, ['userid' => $user3->id]);

        // Add filters to the report.
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => 'enrol:plugin']);
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => $filter]);

        // Apply filters.
        $filtermanual = ['enrol:plugin_operator' => select::EQUAL_TO, 'enrol:plugin_value' => 'manual'];
        $content = $this->get_custom_report_content($report->get('id'), 30, $filtermanual + $filtervalues);

        $this->assertEqualsCanonicalizing($expected, array_column($content, 'c0_firstname'));
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

        $this->datasource_stress_test_columns(participants::class);
        $this->datasource_stress_test_columns_aggregation(participants::class);
        $this->datasource_stress_test_conditions(participants::class, 'course:idnumber');
    }
}
