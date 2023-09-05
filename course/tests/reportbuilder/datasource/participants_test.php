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
use core_reportbuilder\local\filters\boolean_select;
use core_reportbuilder\local\filters\date;
use core_reportbuilder\local\filters\select;
use core_reportbuilder_generator;
use core_reportbuilder_testcase;
use grade_item;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/reportbuilder/tests/helpers.php");
require_once("{$CFG->libdir}/gradelib.php");

/**
 * Course participants datasource tests
 *
 * @package     core_course
 * @covers      \core_course\reportbuilder\datasource\participants
 * @covers      \core_course\reportbuilder\local\formatters\completion
 * @covers      \core_course\reportbuilder\local\formatters\enrolment
 * @copyright   2022 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class participants_test extends core_reportbuilder_testcase {

    /**
     * Test participants datasource
     */
    public function test_participants_datasource(): void {
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

        // Mark course as completed for the user.
        $ccompletion = new completion_completion(array('course' => $course->id, 'userid' => $user1->id));
        $ccompletion->mark_enrolled($timestart);
        $ccompletion->mark_complete($timecompleted);

        // Update final grade for the user.
        $courseitem = grade_item::fetch_course_item($course->id);
        $courseitem->update_final_grade($user1->id, 42.5);

        // Set some last access value for the user in the course.
        $DB->insert_record('user_lastaccess',
            ['userid' => $user1->id, 'courseid' => $course->id, 'timeaccess' => $timelastaccess]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Courses', 'source' => participants::class, 'default' => false]);

        $generator->create_column(['reportid' => $report->get('id'),
            'uniqueidentifier' => 'course:fullname']);
        $generator->create_column(['reportid' => $report->get('id'),
            'uniqueidentifier' => 'course_category:name']);
        $generator->create_column(['reportid' => $report->get('id'),
            'uniqueidentifier' => 'user:fullname']);
        // Order by enrolment method.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'enrolment:method', 'sortenabled' => 1]);
        $generator->create_column(['reportid' => $report->get('id'),
            'uniqueidentifier' => 'group:name']);
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

        // Add filter to the report.
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => 'enrolment:method']);

        $content = $this->get_custom_report_content($report->get('id'));

        // It should get 3 records (manual enrolment, self and guest).
        $this->assertCount(3, $content);

        // Filter by Manual enrolment method.
        $content = $this->get_custom_report_content($report->get('id'), 30, [
            'enrolment:method_operator' => select::EQUAL_TO,
            'enrolment:method_value' => 'manual',
        ]);

        $this->assertCount(1, $content);

        $this->assertEquals([
            'All about Lionel at the work place', // Course name.
            'Music', // Course category name.
            fullname($user1), // User fullname.
            'Manual enrolments', // Enrolment method.
            $group->name, // Group name.
            'Yes', // Course completed.
            userdate($timelastaccess), // Time last access.
            '100.0%', // Progress percentage.
            userdate($timestart), // Time enrolled.
            '', // Time started.
            userdate($timecompleted), // Time completed.
            '', // Reagreggate.
            '2', // Days taking course.
            '2', // Days until completion.
            '42.50', // Grade.
        ], array_values($content[0]));
    }

    /**
     * Data provider for {@see test_report_filters}
     *
     * @return array
     */
    public function filters_data_provider(): array {
        return [
            [
                'enrolment:status',
                [
                    'enrolment:status_operator' => select::EQUAL_TO,
                    'enrolment:status_value' => 1,
                ],
                'Luna'
            ],
            [
                'enrolment:timecreated',
                [
                    'enrolment:timecreated_operator' => date::DATE_CURRENT,
                    'enrolment:timecreated_unit' => date::DATE_UNIT_DAY,
                ],
                'Kira'
            ],
            [
                'enrolment:timestarted',
                [
                    'enrolment:timestarted_operator' => date::DATE_CURRENT,
                    'enrolment:timecreated_unit' => date::DATE_UNIT_DAY,
                ],
                'Luna'
            ],
            [
                'enrolment:timeended',
                [
                    'enrolment:timeended_operator' => date::DATE_CURRENT,
                    'enrolment:timeended_unit' => date::DATE_UNIT_DAY,
                ],
                'Luna'
            ],
            [
                'completion:completed',
                [
                    'completion:completed_operator' => boolean_select::CHECKED,
                    'completion:completed_unit' => 1,
                ],
                'Lionel'
            ],
            [
                'completion:timecompleted',
                [
                    'completion:timecompleted_operator' => date::DATE_NOT_EMPTY,
                ],
                'Lionel'
            ],
            [
                'completion:timeenrolled',
                [
                    'completion:timeenrolled_operator' => date::DATE_NOT_EMPTY,
                ],
                'Lionel'
            ],
            [
                'completion:timestarted',
                [
                    'completion:timestarted_operator' => date::DATE_NOT_EMPTY,
                ],
                'Lionel'
            ],
            [
                'completion:reaggregate',
                [
                    'completion:reaggregate_operator' => date::DATE_NOT_EMPTY,
                ],
                'Lionel'
            ],
        ];
    }

    /**
     * Test getting filter SQL
     *
     * @param string $filter
     * @param array $filtervalues
     * @param string $expected
     *
     * @dataProvider filters_data_provider
     */
    public function test_report_filters(string $filter, array $filtervalues, string $expected): void {
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
        $this->getDataGenerator()->enrol_user($user3->id, $course->id, 'student',
            'manual', time(), time(), ENROL_USER_SUSPENDED);

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
        $report = $generator->create_report(['name' => 'Courses', 'source' => participants::class, 'default' => false]);

        // Add user firstname column to the report.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:firstname']);

        $DB->set_field('user_enrolments', 'timecreated', 0, ['userid' => $user1->id]);
        $DB->set_field('user_enrolments', 'timecreated', 0, ['userid' => $user3->id]);

        // Add filters to the report.
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => 'enrolment:method']);
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => $filter]);

        // Apply filters.
        $filtermanual = ['enrolment:method_operator' => select::EQUAL_TO, 'enrolment:method_value' => 'manual'];
        $content = $this->get_custom_report_content($report->get('id'), 30, $filtermanual + $filtervalues);

        $this->assertCount(1, $content);
        $this->assertEquals($expected, $content[0]['c0_firstname']);
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
