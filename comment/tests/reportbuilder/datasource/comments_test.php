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

namespace core_comment\reportbuilder\datasource;

use context_course;
use core_comment_generator;
use core_reportbuilder_generator;
use core_reportbuilder_testcase;
use core_reportbuilder\local\filters\{date, select, text};

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/reportbuilder/tests/helpers.php");

/**
 * Unit tests for comments datasource
 *
 * @package     core_comment
 * @covers      \core_comment\reportbuilder\datasource\comments
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class comments_test extends core_reportbuilder_testcase {

    /**
     * Test default datasource
     */
    public function test_datasource_default(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        /** @var core_comment_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_comment');

        // Our first user will create a single comment.
        $userone = $this->getDataGenerator()->create_and_enrol($course, 'student', ['firstname' => 'Zoe']);
        $this->setUser($userone);
        $useronecomment = $generator->create_comment([
            'context' => $coursecontext,
            'component' => 'block_comments',
            'area' => 'page_comments',
        ])->add('Cool');

        // Our second user will create a couple of comments.
        $usertwo = $this->getDataGenerator()->create_and_enrol($course, 'student', ['firstname' => 'Amy']);
        $this->setUser($usertwo);
        $usertwocommentfirst = $generator->create_comment([
            'context' => $coursecontext,
            'component' => 'block_comments',
            'area' => 'page_comments',
        ])->add('Super');

        $this->waitForSecond(); // For consistent ordering we need distinct time for second user comments.
        $usertwocommentsecond = $generator->create_comment([
            'context' => $coursecontext,
            'component' => 'block_comments',
            'area' => 'page_comments',
        ])->add('Awesome');

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Blogs', 'source' => comments::class, 'default' => 1]);

        $content = $this->get_custom_report_content($report->get('id'));

        // Default columns are user, context, content, time created. Sorted by user and time created.
        $contextname = $coursecontext->get_context_name();
        $this->assertEquals([
            [fullname($usertwo), $contextname, format_text('Super'), userdate($usertwocommentfirst->timecreated)],
            [fullname($usertwo), $contextname, format_text('Awesome'), userdate($usertwocommentsecond->timecreated)],
            [fullname($userone), $contextname, format_text('Cool'), userdate($useronecomment->timecreated)],
        ], array_map('array_values', $content));
    }

    /**
     * Test datasource columns that aren't added by default
     */
    public function test_datasource_non_default_columns(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $courseurl = course_get_url($course);
        $coursecontext = context_course::instance($course->id);

        /** @var core_comment_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_comment');
        $generator->create_comment([
            'context' => $coursecontext,
            'component' => 'block_comments',
            'area' => 'page_comments',
            'content' => 'Cool',
        ]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Blogs', 'source' => comments::class, 'default' => 0]);

        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'context:link']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'comment:component']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'comment:area']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'comment:itemid']);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(1, $content);

        $this->assertEquals([
            "<a href=\"{$courseurl}\">{$coursecontext->get_context_name()}</a>",
            'block_comments',
            'page_comments',
            0,
        ], array_values($content[0]));
    }

    /**
     * Data provider for {@see test_datasource_filters}
     *
     * @return array[]
     */
    public static function datasource_filters_provider(): array {
        return [
            // Comment.
            'Filter content' => ['comment:content', [
                'comment:content_operator' => text::CONTAINS,
                'comment:content_value' => 'Cool',
            ], true],
            'Filter content (no match)' => ['comment:content', [
                'comment:content_operator' => text::IS_EQUAL_TO,
                'comment:content_value' => 'Beans',
            ], false],
            'Filter time created' => ['comment:timecreated', [
                'comment:timecreated_operator' => date::DATE_RANGE,
                'comment:timecreated_from' => 1622502000,
            ], true],
            'Filter time created (no match)' => ['comment:timecreated', [
                'comment:timecreated_operator' => date::DATE_RANGE,
                'comment:timecreated_to' => 1622502000,
            ], false],

            // Context.
            'Context level' => ['context:level', [
                'context:level_operator' => select::EQUAL_TO,
                'context:level_value' => CONTEXT_COURSE,
            ], true],
            'Context level (no match)' => ['context:level', [
                'context:level_operator' => select::EQUAL_TO,
                'context:level_value' => CONTEXT_BLOCK,
            ], false],

            // User.
            'Filter user' => ['user:username', [
                'user:username_operator' => text::IS_EQUAL_TO,
                'user:username_value' => 'admin',
            ], true],
            'Filter user (no match)' => ['user:username', [
                'user:username_operator' => text::IS_EQUAL_TO,
                'user:username_value' => 'lionel',
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
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        /** @var core_comment_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_comment');
        $generator->create_comment([
            'context' => $coursecontext,
            'component' => 'block_comments',
            'area' => 'page_comments',
            'content' => 'Cool',
        ]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create report containing single column, and given filter.
        $report = $generator->create_report(['name' => 'Tasks', 'source' => comments::class, 'default' => 0]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'comment:component']);

        // Add filter, set it's values.
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => $filtername]);
        $content = $this->get_custom_report_content($report->get('id'), 0, $filtervalues);

        if ($expectmatch) {
            $this->assertCount(1, $content);
            $this->assertEquals('block_comments', reset($content[0]));
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
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        /** @var core_comment_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_comment');
        $generator->create_comment([
            'context' => $coursecontext,
            'component' => 'block_comments',
            'area' => 'page_comments',
            'content' => 'Cool',
        ]);

        $this->datasource_stress_test_columns(comments::class);
        $this->datasource_stress_test_columns_aggregation(comments::class);
        $this->datasource_stress_test_conditions(comments::class, 'comment:component');
    }
}
