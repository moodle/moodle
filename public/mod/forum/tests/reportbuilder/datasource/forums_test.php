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

namespace mod_forum\reportbuilder\datasource;

use core\clock;
use core_reportbuilder_generator;
use core_reportbuilder\local\filters\{date, number, select, text};
use core_reportbuilder\tests\core_reportbuilder_testcase;
use mod_forum_generator;

/**
 * Unit tests for forums datasource
 *
 * @package     mod_forum
 * @covers      \mod_forum\reportbuilder\datasource\forums
 * @copyright   2025 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class forums_test extends core_reportbuilder_testcase {
    /** @var clock $clock */
    private readonly clock $clock;

    /**
     * Mock the clock
     */
    protected function setUp(): void {
        parent::setUp();
        $this->clock = $this->mock_clock_with_frozen(1622502000);
    }

    /**
     * Test default datasource
     */
    public function test_datasource_default(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course);

        /** @var mod_forum_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_forum');

        $forum = $generator->create_instance(['course' => $course->id, 'name' => 'My forum']);
        $discussion = $generator->create_discussion([
            'course' => $course->id,
            'forum' => $forum->id,
            'userid' => $user->id,
            'name' => 'Hello',
            'message' => 'I\'ve just got to let you know',
            'messageformat' => FORMAT_MOODLE,
        ]);

        $this->clock->bump(HOURSECS);
        $generator->create_post([
            'discussion' => $discussion->id,
            'userid' => $user->id,
            'subject' => 'Cause I wonder where you are',
            'message' => 'And I wonder what you do',
            'messageformat' => FORMAT_MOODLE,
        ]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Forums', 'source' => forums::class, 'default' => 1]);

        $content = $this->get_custom_report_content($report->get('id'));

        // Default columns are course, forum, discussion, created, message, user. Sorted by course, forum, discussion, created desc.
        $this->assertEquals([
            [
                $course->fullname,
                'My forum',
                'Hello',
                'Tuesday, 1 June 2021, 8:00 AM',
                '<div class="text_to_html">And I wonder what you do</div>',
                fullname($user),
            ],
            [
                $course->fullname,
                'My forum',
                'Hello',
                'Tuesday, 1 June 2021, 7:00 AM',
                '<div class="text_to_html">I\'ve just got to let you know</div>',
                fullname($user),
            ],
        ], array_map('array_values', $content));
    }

    /**
     * Test datasource columns that aren't added by default
     */
    public function test_datasource_non_default_columns(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $category = $this->getDataGenerator()->create_category();
        $course = $this->getDataGenerator()->create_course(['category' => $category->id]);
        $user = $this->getDataGenerator()->create_and_enrol($course);

        /** @var mod_forum_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_forum');

        $forum = $generator->create_instance([
            'course' => $course->id,
            'idnumber' => 'FORUM1',
            'intro' => 'My cool forum',
            'duedate' => $this->clock->time() + DAYSECS,
            'cutoffdate' => $this->clock->time() + WEEKSECS,
        ]);
        $discussion = $generator->create_discussion([
            'course' => $course->id,
            'forum' => $forum->id,
            'userid' => $user->id,
            'name' => 'My discussion',
            'message' => 'My message',
            'timestart' => $this->clock->time() + HOURSECS,
            'timeend' => $this->clock->time() + DAYSECS,
        ]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Forums', 'source' => forums::class, 'default' => 0]);

        // Course category.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course_category:name']);

        // Course module.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'course_module:idnumber']);

        // Forum.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'forum:description']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'forum:type']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'forum:duedate']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'forum:cutoffdate']);

        // Discussion.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'discussion:timestart']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'discussion:timeend']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'discussion:timemodified']);

        // Post.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'post:subject']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'post:timecreated']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'post:timemodified']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'post:wordcount']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'post:charcount']);

        $content = $this->get_custom_report_content($report->get('id'));

        $this->assertEquals([
            [
                $category->get_formatted_name(),
                'FORUM1',
                '<div class="text_to_html">My cool forum</div>',
                'Standard forum for general use',
                'Wednesday, 2 June 2021, 7:00 AM',
                'Tuesday, 8 June 2021, 7:00 AM',
                'Tuesday, 1 June 2021, 8:00 AM',
                'Wednesday, 2 June 2021, 7:00 AM',
                'Tuesday, 1 June 2021, 7:00 AM',
                'My discussion',
                'Tuesday, 1 June 2021, 7:00 AM',
                'Tuesday, 1 June 2021, 7:00 AM',
                '2',
                '9',
            ],
        ], array_map('array_values', $content));
    }

    /**
     * Data provider for {@see test_datasource_filters}
     *
     * @return array[]
     */
    public static function datasource_filters_provider(): array {
        return [
            // Course category.
            'Course category name' => ['course_category:text', [
                'course_category:text_operator' => text::IS_EQUAL_TO,
                'course_category:text_value' => 'My category',
            ], true],
            'Course category name (no match)' => ['course_category:text', [
                'course_category:text_operator' => text::IS_EQUAL_TO,
                'course_category:text_value' => 'Another category',
            ], false],

            // Course.
            'Course fullname' => ['course:fullname', [
                'course:fullname_operator' => text::IS_EQUAL_TO,
                'course:fullname_value' => 'My course',
            ], true],
            'Course fullname (no match)' => ['course:fullname', [
                'course:fullname_operator' => text::IS_EQUAL_TO,
                'course:fullname_value' => 'Another course',
            ], false],

            // Course module.
            'Course module ID number' => ['course_module:idnumber', [
                'course_module:idnumber_operator' => text::IS_EQUAL_TO,
                'course_module:idnumber_value' => 'FORUM1',
            ], true],
            'Course module ID number (no match)' => ['course_module:idnumber', [
                'course_module:idnumber_operator' => text::IS_EQUAL_TO,
                'course_module:idnumber_value' => 'FORUM2',
            ], false],

            // Forum.
            'Forum name' => ['forum:name', [
                'forum:name_operator' => text::IS_EQUAL_TO,
                'forum:name_value' => 'My forum',
            ], true],
            'Forum name (no match)' => ['forum:name', [
                'forum:name_operator' => text::IS_EQUAL_TO,
                'forum:name_value' => 'Another forum',
            ], false],
            'Forum description' => ['forum:description', [
                'forum:description_operator' => text::CONTAINS,
                'forum:description_value' => 'My cool forum',
            ], true],
            'Forum description (no match)' => ['forum:description', [
                'forum:description_operator' => text::CONTAINS,
                'forum:description_value' => 'Another cool forum',
            ], false],
            'Forum type' => ['forum:type', [
                'forum:type_operator' => select::EQUAL_TO,
                'forum:type_value' => 'general',
            ], true],
            'Forum type (no match)' => ['forum:type', [
                'forum:type_operator' => select::EQUAL_TO,
                'forum:type_value' => 'news',
            ], false],
            'Forum due date' => ['forum:duedate', [
                'forum:duedate_operator' => date::DATE_RANGE,
                'forum:duedate_from' => 1622502000,
            ], true],
            'Forum due date (no match)' => ['forum:duedate', [
                'forum:duedate_operator' => date::DATE_RANGE,
                'forum:duedate_to' => 1622502000,
            ], false],
            'Forum cut-off date' => ['forum:cutoffdate', [
                'forum:cutoffdate_operator' => date::DATE_RANGE,
                'forum:cutoffdate_from' => 1622502000,
            ], true],
            'Forum cutoff date (no match)' => ['forum:cutoffdate', [
                'forum:cutoffdate_operator' => date::DATE_RANGE,
                'forum:cutoffdate_to' => 1622502000,
            ], false],

            // Discussion.
            'Discussion name' => ['discussion:name', [
                'discussion:name_operator' => text::IS_EQUAL_TO,
                'discussion:name_value' => 'My discussion',
            ], true],
            'Discussion name (no match)' => ['discussion:name', [
                'discussion:name_operator' => text::IS_EQUAL_TO,
                'discussion:name_value' => 'Another discussion',
            ], false],
            'Discussion time start' => ['discussion:timestart', [
                'discussion:timestart_operator' => date::DATE_RANGE,
                'discussion:timestart_from' => 1622502000,
            ], true],
            'Discussion time start (no match)' => ['discussion:timestart', [
                'discussion:timestart_operator' => date::DATE_RANGE,
                'discussion:timestart_to' => 1622502000,
            ], false],
            'Discussion time end' => ['discussion:timeend', [
                'discussion:timeend_operator' => date::DATE_RANGE,
                'discussion:timeend_from' => 1622502000,
            ], true],
            'Discussion time end (no match)' => ['discussion:timeend', [
                'discussion:timeend_operator' => date::DATE_RANGE,
                'discussion:timeend_to' => 1622502000,
            ], false],
            'Discussion time modified' => ['discussion:timemodified', [
                'discussion:timemodified_operator' => date::DATE_RANGE,
                'discussion:timemodified_from' => 1622502000,
            ], true],
            'Discussion time modified (no match)' => ['discussion:timemodified', [
                'discussion:timemodified_operator' => date::DATE_RANGE,
                'discussion:timemodified_to' => 1622502000,
            ], false],

            // Post.
            'Post subject' => ['post:subject', [
                'post:subject_operator' => text::IS_EQUAL_TO,
                'post:subject_value' => 'My discussion',
            ], true],
            'Post subject (no match)' => ['post:subject', [
                'post:subject_operator' => text::IS_EQUAL_TO,
                'post:subject_value' => 'Another discussion',
            ], false],
            'Post message' => ['post:message', [
                'post:subject_operator' => text::CONTAINS,
                'post:subject_value' => 'My message',
            ], true],
            'Post message (no match)' => ['post:message', [
                'post:message_operator' => text::CONTAINS,
                'post:message_value' => 'Another message',
            ], false],
            'Post time created' => ['post:timecreated', [
                'post:timecreated_operator' => date::DATE_RANGE,
                'post:timecreated_from' => 1622502000,
            ], true],
            'Post time created (no match)' => ['post:timecreated', [
                'post:timecreated_operator' => date::DATE_RANGE,
                'post:timecreated_to' => 1622502000,
            ], false],
            'Post time modified' => ['post:timemodified', [
                'post:timemodified_operator' => date::DATE_RANGE,
                'post:timemodified_from' => 1622502000,
            ], true],
            'Post time modified (no match)' => ['post:timemodified', [
                'post:timemodified_operator' => date::DATE_RANGE,
                'post:timemodified_to' => 1622502000,
            ], false],
            'Post word count' => ['post:wordcount', [
                'post:wordcount_operator' => number::EQUAL_TO,
                'post:wordcount_value1' => 2,
            ], true],
            'Post word count (no match)' => ['post:wordcount', [
                'post:wordcount_operator' => number::GREATER_THAN,
                'post:wordcount_value1' => 2,
            ], false],
            'Post character count' => ['post:charcount', [
                'post:charcount_operator' => number::EQUAL_TO,
                'post:charcount_value1' => 9,
            ], true],
            'Post character count (no match)' => ['post:charcount', [
                'post:charcount_operator' => number::GREATER_THAN,
                'post:charcount_value1' => 9,
            ], false],

            // User.
            'User firstname' => ['user:firstname', [
                'user:firstname_operator' => text::IS_EQUAL_TO,
                'user:firstname_value' => 'Zoe',
            ], true],
            'User firstname (no match)' => ['user:firstname', [
                'user:firstname_operator' => text::IS_EQUAL_TO,
                'user:firstname_value' => 'Alice',
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
        $this->resetAfterTest();
        $this->setAdminUser();

        $category = $this->getDataGenerator()->create_category(['name' => 'My category']);
        $course = $this->getDataGenerator()->create_course(['category' => $category->id, 'fullname' => 'My course']);
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student', ['firstname' => 'Zoe']);

        /** @var mod_forum_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_forum');

        $forum = $generator->create_instance([
            'course' => $course->id,
            'idnumber' => 'FORUM1',
            'name' => 'My forum',
            'intro' => 'My cool forum',
            'duedate' => $this->clock->time() + DAYSECS,
            'cutoffdate' => $this->clock->time() + WEEKSECS,
        ]);
        $discussion = $generator->create_discussion([
            'course' => $course->id,
            'forum' => $forum->id,
            'userid' => $user->id,
            'name' => 'My discussion',
            'message' => 'My message',
            'timestart' => $this->clock->time() + HOURSECS,
            'timeend' => $this->clock->time() + DAYSECS,
        ]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create report containing single username column, and given filter.
        $report = $generator->create_report(['name' => 'Forums', 'source' => forums::class, 'default' => 0]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'forum:name']);

        // Add filter, set it's values.
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => $filtername]);
        $content = $this->get_custom_report_content($report->get('id'), 0, $filtervalues);

        if ($expectmatch) {
            $this->assertEquals([
                [$forum->name],
            ], array_map('array_values', $content));
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
        $user = $this->getDataGenerator()->create_and_enrol($course);

        /** @var mod_forum_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_forum');

        $forum = $generator->create_instance(['course' => $course->id]);
        $discussion = $generator->create_discussion(['course' => $course->id, 'forum' => $forum->id, 'userid' => $user->id]);

        $this->datasource_stress_test_columns(forums::class);
        $this->datasource_stress_test_columns_aggregation(forums::class);
        $this->datasource_stress_test_conditions(forums::class, 'forum:type');
    }
}
