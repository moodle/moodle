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

namespace core_blog\reportbuilder\datasource;

use core_blog_generator;
use core_comment_generator;
use core\context\{system, user};
use core_reportbuilder_generator;
use core_reportbuilder\local\filters\{boolean_select, date, select, text};
use core_reportbuilder\tests\core_reportbuilder_testcase;

/**
 * Unit tests for blogs datasource
 *
 * @package     core_blog
 * @covers      \core_blog\reportbuilder\datasource\blogs
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class blogs_test extends core_reportbuilder_testcase {

    /**
     * Test default datasource
     */
    public function test_datasource_default(): void {
        $this->resetAfterTest();

        /** @var core_blog_generator $blogsgenerator */
        $blogsgenerator = $this->getDataGenerator()->get_plugin_generator('core_blog');

        // Our first user will create a course blog.
        $course = $this->getDataGenerator()->create_course();
        $userone = $this->getDataGenerator()->create_and_enrol($course, 'student', ['firstname' => 'Zoe']);
        $courseblog = $blogsgenerator->create_entry(['publishstate' => 'site', 'userid' => $userone->id,
            'subject' => 'Course', 'summary' => 'Course summary', 'courseid' => $course->id]);

        // Our second user will create a personal and site blog.
        $usertwo = $this->getDataGenerator()->create_user(['firstname' => 'Amy']);
        $personalblog = $blogsgenerator->create_entry(['publishstate' => 'draft', 'userid' => $usertwo->id,
            'subject' => 'Personal', 'summary' => 'Personal summary']);

        $this->waitForSecond(); // For consistent ordering we need distinct time for second user blogs.
        $siteblog = $blogsgenerator->create_entry(['publishstate' => 'public', 'userid' => $usertwo->id,
            'subject' => 'Site', 'summary' => 'Site summary']);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Blogs', 'source' => blogs::class, 'default' => 1]);

        $content = $this->get_custom_report_content($report->get('id'));

        // Default columns are user, course, title, time created. Sorted by user and time created.
        $this->assertEquals([
            [fullname($usertwo), '', $personalblog->subject, userdate($personalblog->created)],
            [fullname($usertwo), '', $siteblog->subject, userdate($siteblog->created)],
            [fullname($userone), $course->fullname, $courseblog->subject, userdate($courseblog->created)],
        ], array_map('array_values', $content));
    }

    /**
     * Test datasource columns that aren't added by default
     */
    public function test_datasource_non_default_columns(): void {
        global $DB;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        /** @var core_blog_generator $blogsgenerator */
        $blogsgenerator = $this->getDataGenerator()->get_plugin_generator('core_blog');
        $blog = $blogsgenerator->create_entry(['publishstate' => 'draft', 'userid' => $user->id, 'subject' => 'My blog',
            'summary' => 'Horses', 'tags' => ['horse']]);

        // Add an attachment.
        $blog->attachment = 1;
        get_file_storage()->create_file_from_string([
            'contextid' => system::instance()->id,
            'component' => 'blog',
            'filearea' => 'attachment',
            'itemid' => $blog->id,
            'filepath' => '/',
            'filename' => 'hello.txt',
        ], 'hello');

        /** @var core_comment_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_comment');
        $generator->create_comment([
            'context' => user::instance($user->id),
            'component' => 'blog',
            'area' => 'format_blog',
            'itemid' => $blog->id,
            'content' => 'Cool',
        ]);

        // Manually update the created/modified date of the blog.
        $blog->created = 1654038000;
        $blog->lastmodified = $blog->created + HOURSECS;
        $DB->update_record('post', $blog);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Blogs', 'source' => blogs::class, 'default' => 0]);

        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'blog:titlewithlink']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'blog:body']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'blog:attachment']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'blog:publishstate']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'blog:timemodified']);

        // Tag entity (course/user presence already checked by default columns).
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'tag:name']);

        // File entity.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'file:size']);

        // Comment entity.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'comment:content']);

        // Commenter entity.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'commenter:fullname']);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(1, $content);

        [
            $link,
            $body,
            $attachment,
            $publishstate,
            $timemodified,
            $tags,
            $filesize,
            $comment,
            $commenter,
        ] = array_values($content[0]);

        $this->assertEquals("<a href=\"https://www.example.com/moodle/blog/index.php?entryid={$blog->id}\">{$blog->subject}</a>",
            $link);
        $this->assertStringContainsString('Horses', $body);
        $this->assertStringContainsString('hello.txt', $attachment);
        $this->assertEquals('Draft', $publishstate);
        $this->assertEquals(userdate($blog->lastmodified), $timemodified);
        $this->assertEquals('horse', $tags);
        $this->assertEquals("5\xc2\xa0bytes", $filesize);
        $this->assertEquals(format_text('Cool'), $comment);
        $this->assertEquals(fullname($user), $commenter);
    }

    /**
     * Data provider for {@see test_datasource_filters}
     *
     * @return array[]
     */
    public static function datasource_filters_provider(): array {
        return [
            'Filter title' => ['subject', 'Cool', 'blog:title', [
                'blog:title_operator' => text::CONTAINS,
                'blog:title_value' => 'Cool',
            ], true],
            'Filter title (no match)' => ['subject', 'Cool', 'blog:title', [
                'blog:title_operator' => text::CONTAINS,
                'blog:title_value' => 'Beans',
            ], false],
            'Filter body' => ['summary', 'Awesome', 'blog:body', [
                'blog:body_operator' => select::EQUAL_TO,
                'blog:body_value' => 'Awesome',
            ], true],
            'Filter body (no match)' => ['summary', 'Awesome', 'blog:body', [
                'blog:body_operator' => select::EQUAL_TO,
                'blog:body_value' => 'Beans',
            ], false],
            'Filter attachment' => ['attachment', 1, 'blog:attachment', [
                'blog:attachment_operator' => boolean_select::CHECKED,
            ], true],
            'Filter attachment (no match)' => ['attachment', 1, 'blog:attachment', [
                'blog:attachment_operator' => boolean_select::NOT_CHECKED,
            ], false],
            'Filter publish state' => ['publishstate', 'site', 'blog:publishstate', [
                'blog:publishstate_operator' => select::EQUAL_TO,
                'blog:publishstate_value' => 'site',
            ], true],
            'Filter publish state (no match)' => ['publishstate', 'site', 'blog:publishstate', [
                'blog:publishstate_operator' => select::EQUAL_TO,
                'blog:publishstate_value' => 'draft',
            ], false],
            'Filter time created' => ['created', 1654038000, 'blog:timecreated', [
                'blog:timecreated_operator' => date::DATE_RANGE,
                'blog:timecreated_from' => 1622502000,
            ], true],
            'Filter time created (no match)' => ['created', 1654038000, 'blog:timecreated', [
                'blog:timecreated_operator' => date::DATE_RANGE,
                'blog:timecreated_to' => 1622502000,
            ], false],
            'Filter time modified' => ['lastmodified', 1654038000, 'blog:timemodified', [
                'blog:timemodified_operator' => date::DATE_RANGE,
                'blog:timemodified_from' => 1622502000,
            ], true],
            'Filter time modified (no match)' => ['lastmodified', 1654038000, 'blog:timemodified', [
                'blog:timemodified_operator' => date::DATE_RANGE,
                'blog:timemodified_to' => 1622502000,
            ], false],
        ];
    }

    /**
     * Test datasource filters
     *
     * @param string $field
     * @param mixed $value
     * @param string $filtername
     * @param array $filtervalues
     * @param bool $expectmatch
     *
     * @dataProvider datasource_filters_provider
     */
    public function test_datasource_filters(
        string $field,
        $value,
        string $filtername,
        array $filtervalues,
        bool $expectmatch
    ): void {
        global $DB;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();

        /** @var core_blog_generator $blogsgenerator */
        $blogsgenerator = $this->getDataGenerator()->get_plugin_generator('core_blog');

        // Create default blog, then manually override one of it's properties to use for filtering.
        $blog = $blogsgenerator->create_entry(['userid' => $user->id, 'subject' => 'My blog', 'summary' => 'Horses']);
        $DB->set_field('post', $field, $value, ['id' => $blog->id]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create report containing single user column, and given filter.
        $report = $generator->create_report(['name' => 'Blogs', 'source' => blogs::class, 'default' => 0]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:fullname']);

        // Add filter, set it's values.
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => $filtername]);
        $content = $this->get_custom_report_content($report->get('id'), 0, $filtervalues);

        if ($expectmatch) {
            $this->assertCount(1, $content);
            $this->assertEquals(fullname($user), reset($content[0]));
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

        $user = $this->getDataGenerator()->create_user();

        /** @var core_blog_generator $blogsgenerator */
        $blogsgenerator = $this->getDataGenerator()->get_plugin_generator('core_blog');
        $blogsgenerator->create_entry(['userid' => $user->id, 'subject' => 'My blog', 'summary' => 'Horses']);

        $this->datasource_stress_test_columns(blogs::class);
        $this->datasource_stress_test_columns_aggregation(blogs::class);
        $this->datasource_stress_test_conditions(blogs::class, 'blog:title');
    }
}
