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

namespace core_files\reportbuilder\datasource;

use core\context\{course, coursecat, user};
use core_reportbuilder_generator;
use core_reportbuilder_testcase;
use core_reportbuilder\local\filters\{boolean_select, date, filesize, select, text};

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/reportbuilder/tests/helpers.php");

/**
 * Unit tests for files datasource
 *
 * @package     core_files
 * @covers      \core_files\reportbuilder\datasource\files
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class files_test extends core_reportbuilder_testcase {

    /**
     * Test default datasource
     */
    public function test_datasource_default(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = course::instance($course->id);

        $user = $this->getDataGenerator()->create_user();
        $usercontext = user::instance($user->id);

        $this->setUser($user);

        $this->generate_test_files($coursecontext);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Files', 'source' => files::class, 'default' => 1]);

        $content = $this->get_custom_report_content($report->get('id'));
        $content = $this->filter_custom_report_content($content, static function(array $row): bool {
            return $row['c0_ctxid'] !== 'System';
        });

        $this->assertCount(2, $content);

        // Default columns are context, user, name, type, size, time created. Sorted by context and time created.
        [$contextname, $userfullname, $filename, $mimetype, $filesize, $timecreated] = array_values($content[0]);
        $this->assertEquals($coursecontext->get_context_name(), $contextname);
        $this->assertEquals(fullname($user), $userfullname);
        $this->assertEquals('Hello.txt', $filename);
        $this->assertEquals('Text file', $mimetype);
        $this->assertEquals("5\xc2\xa0bytes", $filesize);
        $this->assertNotEmpty($timecreated);

        [$contextname, $userfullname, $filename, $mimetype, $filesize, $timecreated] = array_values($content[1]);
        $this->assertEquals($usercontext->get_context_name(), $contextname);
        $this->assertEquals(fullname($user), $userfullname);
        $this->assertEquals('Hello.txt', $filename);
        $this->assertEquals('Text file', $mimetype);
        $this->assertEquals("5\xc2\xa0bytes", $filesize);
        $this->assertNotEmpty($timecreated);
    }

    /**
     * Test datasource columns that aren't added by default
     */
    public function test_datasource_non_default_columns(): void {
        global $OUTPUT;

        $this->resetAfterTest();

        $category = $this->getDataGenerator()->create_category();
        $categorycontext = coursecat::instance($category->id);

        $course = $this->getDataGenerator()->create_course(['category' => $category->id]);
        $coursecontext = course::instance($course->id);

        $user = $this->getDataGenerator()->create_user();
        $usercontext = user::instance($user->id);

        $this->setUser($user);

        $draftitemid = $this->generate_test_files($coursecontext);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Files', 'source' => files::class, 'default' => 0]);

        // Consistent order, sorted by context and content hash.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'context:link',
            'sortenabled' => 1, 'sortorder' => 1]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'context:name']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'context:level']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'context:path']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'context:parent']);

        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'file:icon']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'file:path']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'file:author']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'file:license']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'file:contenthash',
            'sortenabled' => 1, 'sortorder' => 2]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'file:component']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'file:area']);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'file:itemid']);

        $content = $this->get_custom_report_content($report->get('id'));
        $content = $this->filter_custom_report_content($content, static function(array $row): bool {
            return stripos($row['c0_ctxid'], 'System') === false;
        });

        // There should be two entries (directory & file) for each context.
        $this->assertEquals([
            [
                "<a href=\"{$coursecontext->get_url()}\">{$coursecontext->get_context_name()}</a>",
                $coursecontext->get_context_name(),
                'Course',
                $coursecontext->path,
                $categorycontext->get_context_name(),
                '<img class="icon iconsize-medium" alt="Directory" title="Directory" src="' .
                    $OUTPUT->image_url('f/folder')->out() . '" />',
                '/',
                null,
                '',
                'da39a3ee5e6b4b0d3255bfef95601890afd80709',
                'course',
                'summary',
                0,
            ],
            [
                "<a href=\"{$coursecontext->get_url()}\">{$coursecontext->get_context_name()}</a>",
                $coursecontext->get_context_name(),
                'Course',
                $coursecontext->path,
                $categorycontext->get_context_name(),
                '<img class="icon iconsize-medium" alt="Text file" title="Text file" src="' .
                    $OUTPUT->image_url('f/text')->out() . '" />',
                '/',
                null,
                '',
                'f7ff9e8b7bb2e09b70935a5d785e0cc5d9d0abf0',
                'course',
                'summary',
                0,
            ],
            [
                "<a href=\"{$usercontext->get_url()}\">{$usercontext->get_context_name()}</a>",
                $usercontext->get_context_name(),
                'User',
                $usercontext->path,
                'System',
                '<img class="icon iconsize-medium" alt="Directory" title="Directory" src="' .
                    $OUTPUT->image_url('f/folder')->out() . '" />',
                '/',
                null,
                '',
                'da39a3ee5e6b4b0d3255bfef95601890afd80709',
                'user',
                'draft',
                $draftitemid,
            ],
            [
                "<a href=\"{$usercontext->get_url()}\">{$usercontext->get_context_name()}</a>",
                $usercontext->get_context_name(),
                'User',
                $usercontext->path,
                'System',
                '<img class="icon iconsize-medium" alt="Text file" title="Text file" src="' .
                    $OUTPUT->image_url('f/text')->out() . '" />',
                '/',
                null,
                '',
                'f7ff9e8b7bb2e09b70935a5d785e0cc5d9d0abf0',
                'user',
                'draft',
                $draftitemid,
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
            // File.
            'Filter directory' => ['file:directory', [
                'file:directory_operator' => boolean_select::CHECKED,
            ], 2],
            'Filter draft' => ['file:draft', [
                'file:draft_operator' => boolean_select::CHECKED,
            ], 2],
            'Filter name' => ['file:name', [
                'file:name_operator' => text::IS_EQUAL_TO,
                'file:name_value' => 'Hello.txt',
            ], 2],
            'Filter size' => ['file:size', [
                'file:size_operator' => filesize::GREATER_THAN,
                'file:size_value1' => 2,
                'file:size_unit' => filesize::SIZE_UNIT_BYTE,
            ], 2],
            'Filter type' => ['file:type', [
                'file:type_operator' => select::EQUAL_TO,
                'file:type_value' => 'text/plain',
            ], 2],
            'Filter type (non match)' => ['file:type', [
                'file:type_operator' => select::EQUAL_TO,
                'file:type_value' => 'image/png',
            ], 0],
            'Filter author' => ['file:author', [
                'file:author_operator' => text::IS_EMPTY,
            ], 4],
            'Filter author (non match)' => ['file:author', [
                'file:author_operator' => text::IS_NOT_EMPTY,
            ], 0],
            'Filter license' => ['file:license', [
                'file:license_operator' => select::EQUAL_TO,
                'file:license_value' => 'unknown',
            ], 4],
            'Filter license (non match)' => ['file:license', [
                'file:license_operator' => select::EQUAL_TO,
                'file:license_value' => 'public',
            ], 0],
            'Filter content hash' => ['file:contenthash', [
                'file:contenthash_operator' => text::IS_EQUAL_TO,
                'file:contenthash_value' => 'f7ff9e8b7bb2e09b70935a5d785e0cc5d9d0abf0',
            ], 2],
            'Filter content hash (no match)' => ['file:contenthash', [
                'file:contenthash_operator' => text::IS_EQUAL_TO,
                'file:contenthash_value' => 'f00f',
            ], 0],
            'Filter component' => ['file:component', [
                'file:component_operator' => text::IS_EQUAL_TO,
                'file:component_value' => 'course',
            ], 2],
            'Filter component (no match)' => ['file:component', [
                'file:component_operator' => text::IS_EQUAL_TO,
                'file:component_value' => 'reportbuilder',
            ], 0],
            'Filter area' => ['file:area', [
                'file:area_operator' => text::IS_EQUAL_TO,
                'file:area_value' => 'summary',
            ], 2],
            'Filter area (no match)' => ['file:area', [
                'file:area_operator' => text::IS_EQUAL_TO,
                'file:area_value' => 'report',
            ], 0],
            'Filter time created' => ['file:timecreated', [
                'file:timecreated_operator' => date::DATE_RANGE,
                'file:timecreated_from' => 1622502000,
            ], 4],
            'Filter time created (non match)' => ['file:timecreated', [
                'file:timecreated_operator' => date::DATE_RANGE,
                'file:timecreated_to' => 1622502000,
            ], 0],

            // Context.
            'Context level' => ['context:level', [
                'context:level_operator' => select::EQUAL_TO,
                'context:level_value' => CONTEXT_COURSE,
            ], 2],
            'Context level (no match)' => ['context:level', [
                'context:level_operator' => select::EQUAL_TO,
                'context:level_value' => CONTEXT_BLOCK,
            ], 0],
            'Context path' => ['context:path', [
                'context:path_operator' => text::STARTS_WITH,
                'context:path_value' => '/1/',
            ], 4],
            'Context path (no match)' => ['context:path', [
                'context:path_operator' => text::STARTS_WITH,
                'context:path_value' => '/1/2/3/',
            ], 0],

            // User.
            'Filter user' => ['user:username', [
                'user:username_operator' => text::IS_EQUAL_TO,
                'user:username_value' => 'alfie',
            ], 4],
            'Filter user (no match)' => ['user:username', [
                'user:username_operator' => text::IS_EQUAL_TO,
                'user:username_value' => 'lionel',
            ], 0],
        ];
    }

    /**
     * Test datasource filters
     *
     * @param string $filtername
     * @param array $filtervalues
     * @param int $expectmatchcount
     *
     * @dataProvider datasource_filters_provider
     */
    public function test_datasource_filters(
        string $filtername,
        array $filtervalues,
        int $expectmatchcount
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $user = $this->getDataGenerator()->create_user(['username' => 'alfie']);
        $this->setUser($user);

        $course = $this->getDataGenerator()->create_course();
        $coursecontext = course::instance($course->id);

        $this->generate_test_files($coursecontext);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Create report containing single column, and given filter.
        $report = $generator->create_report(['name' => 'Files', 'source' => files::class, 'default' => 0]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'context:name']);

        // Add filter, set it's values.
        $generator->create_filter(['reportid' => $report->get('id'), 'uniqueidentifier' => $filtername]);
        $content = $this->get_custom_report_content($report->get('id'), 0, $filtervalues);
        $content = $this->filter_custom_report_content($content, static function(array $row): bool {
            return stripos($row['c0_ctxid'], 'System') === false;
        });

        $this->assertCount($expectmatchcount, $content);
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
        $coursecontext = course::instance($course->id);

        $this->generate_test_files($coursecontext);

        $this->datasource_stress_test_columns(files::class);
        $this->datasource_stress_test_columns_aggregation(files::class);
        $this->datasource_stress_test_conditions(files::class, 'file:path');
    }

    /**
     * Ensuring report content only includes files we have explicitly created within the test
     *
     * @param array $content
     * @param callable $callback
     * @return array
     */
    protected function filter_custom_report_content(array $content, callable $callback): array {
        $content = array_filter($content, $callback);
        return array_values($content);
    }

    /**
     * Helper method to generate some test files (a user draft and course summary file) for reporting on
     *
     * @param course $context
     * @return int Draft item ID
     */
    protected function generate_test_files(course $context): int {
        global $USER;

        $draftitemid = file_get_unused_draft_itemid();

        // Populate user draft.
        get_file_storage()->create_file_from_string([
            'contextid' => user::instance($USER->id)->id,
            'userid' => $USER->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => $draftitemid,
            'filepath' => '/',
            'filename' => 'Hello.txt',
        ], 'Hello');

        // Save draft to course summary file area.
        file_save_draft_area_files($draftitemid, $context->id, 'course', 'summary', 0);

        return $draftitemid;
    }
}
