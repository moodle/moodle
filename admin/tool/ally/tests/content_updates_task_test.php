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

/**
 * Tests for content updates task.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @group     tool_ally
 * @group     ally
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use Prophecy\Argument;
use \core\event\course_module_updated;
use tool_ally\push_config;
use tool_ally\push_content_updates;
use tool_ally\task\content_updates_task;
use tool_ally\prophesize_deprecation_workaround_mixin;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/abstract_testcase.php');
require_once(__DIR__.'/prophesize_deprecation_workaround_mixin.php');

/**
 * Tests for content updates task.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @group     tool_ally
 * @group     ally
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content_updates_task_test extends abstract_testcase {
    use prophesize_deprecation_workaround_mixin;

    /**
     * First run should set the timestamp then exit.
     */
    public function test_initial_run() {
        $this->resetAfterTest();

        $this->assertEmpty(get_config('tool_ally', 'push_content_timestamp'));

        $task          = new content_updates_task();
        $task->config  = new push_config('url', 'key', 'sceret');
        $task->updates = $this->createMock(push_content_updates::class);

        $expected = time();
        $task->execute();

        $this->assertGreaterThanOrEqual($expected, get_config('tool_ally', 'push_content_timestamp'));
    }

    /**
     * Nothing should happen if config is invalid.
     */
    public function test_invalid_config() {
        $task          = new content_updates_task();
        $task->updates = $this->createMock(push_content_updates::class);

        $task->execute();

        $this->assertEmpty(get_config('tool_ally', 'push_content_timestamp'));
    }

    /**
     * Ensure that basic execution and timestamp management is working.
     */
    public function test_push_updates() {
        global $DB;

        $this->resetAfterTest();

        $this->setAdminUser();

        set_config('push_cli_only', 1, 'tool_ally');
        set_config('push_content_timestamp', time() - (WEEKSECS * 2), 'tool_ally');

        $course      = $this->getDataGenerator()->create_course();
        $label    = $this->getDataGenerator()->create_module('label',
                ['introformat' => FORMAT_HTML, 'course' => $course->id]);

        // Wipe out content queue - it will already have been populated by events triggered whilst creating course, etc.
        $DB->delete_records('tool_ally_content_queue');

        list ($course, $cm) = get_course_and_cm_from_cmid($label->cmid);
        course_module_updated::create_from_cm($cm)->trigger();

        $task          = new content_updates_task();
        $task->config  = new push_config('url', 'key', 'sceret');
        $updates = $this->createMock(push_content_updates::class);
        $updates->expects($this->once())
            ->method('send')
            ->with($this->isType('array'));
        $task->updates = $updates;

        $task->execute();
    }

    /**
     * Ensure that our batch looping is working as expected.
     */
    public function test_push_updates_batching() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        set_config('push_content_timestamp', time() - (WEEKSECS * 2), 'tool_ally');

        $course = $this->getDataGenerator()->create_course();

        // Wipe out content queue - it will already have been populated by events triggered whilst creating course.
        $DB->delete_records('tool_ally_content_queue');

        // Create 5 supported components.
        for ($i = 0; $i < 5; $i++) {
            $this->getDataGenerator()->create_module('label',
                    ['introformat' => FORMAT_HTML, 'course' => $course->id]);
        }

        $updates = $this->createMock(push_content_updates::class);
        $updates->expects($this->exactly(3))
            ->method('send')
            ->with($this->isType('array'));

        $task          = new content_updates_task();
        $task->config  = new push_config('url', 'key', 'sceret', 2);
        $task->updates = $updates;

        $task->execute();
    }

    /**
     * Test pushing of content deletions.
     */
    public function test_push_deletes() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        set_config('push_content_timestamp', time() - (WEEKSECS * 2), 'tool_ally');

        $this->dataset_from_array(include(__DIR__.'/fixtures/deleted_content.php'))->to_database();

        $updates = $this->createMock(push_content_updates::class);
        $updates->expects($this->exactly(3))
            ->method('send')
            ->with($this->isType('array'));

        $task          = new content_updates_task();
        $task->config  = new push_config('url', 'key', 'sceret', 2);
        $task->updates = $updates;

        $task->execute();

        // The deleted content queue should still be populated at this point.
        $this->assertNotEmpty($DB->get_records('tool_ally_deleted_content'));

        $task->execute();

        // After a second run of the task, the deleted content queue should now be empty.
        $this->assertEmpty($DB->get_records('tool_ally_deleted_content'));
    }

    private function assert_deletion_queue_contains($component, $table, $field, $id) {
        global $DB;

        if (!$DB->get_record('tool_ally_deleted_content', [
            'component' => $component,
            'comptable' => $table,
            'compfield' => $field,
            'comprowid' => $id
        ])) {
            $msg = 'Searched deletion queue, failed to find component "'.$component.
                    '" table "'.$table.'" field "'.$field.'" id "'.$id.'"';
            $this->fail($msg);
        }
    }

    public function pre_course_module_delete_forum($forumtype = 'forum') {
        global $DB, $USER;

        // Prevent it from creating a backup of the deleted module.
        set_config('coursebinenable', 0, 'tool_recyclebin');

        $this->resetAfterTest();
        $this->setAdminUser();

        $course   = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module($forumtype, ['course' => $course->id]);

        // Add a discussion / post by admin user - should show up in results.
        $this->setAdminUser();
        $record = new \stdClass();
        $record->course = $course->id;
        $record->forum = $forum->id;
        $record->userid = $USER->id;
        $discussion = self::getDataGenerator()->get_plugin_generator('mod_'.$forumtype)->create_discussion($record);

        // A post is automatically created when a discussion is created.
        $post = $DB->get_record($forumtype.'_posts', ['discussion' => $discussion->id]);

        course_delete_module($forum->cmid);

        $task          = new content_updates_task();
        $task->config  = new push_config('url', 'key', 'sceret');
        $updates = $this->createMock(push_content_updates::class);
        $updates->expects($this->once())
            ->method('send')
            ->with($this->isType('array'));
        $task->updates = $updates;

        $task->execute();

        $this->assert_deletion_queue_contains($forumtype, $forumtype, 'intro', $forum->id);
        $this->assert_deletion_queue_contains($forumtype, $forumtype.'_posts', 'message', $post->id);

        // Make sure we have some deletion queue records but that none of them are processed.
        $deleted = $DB->get_records_select('tool_ally_deleted_content', 'timeprocessed IS NULL');
        $this->assertCount(2, $deleted);
        $deleted = $DB->get_records_select('tool_ally_deleted_content', 'timeprocessed IS NOT NULL');
        $this->assertCount(0, $deleted);

        $task->execute();

        // Make sure we have some deletion queue records and all of them are processed.
        $deleted = $DB->get_records_select('tool_ally_deleted_content', 'timeprocessed IS NOT NULL');
        $this->assertCount(2, $deleted);
        $deleted = $DB->get_records_select('tool_ally_deleted_content', 'timeprocessed IS NULL');
        $this->assertCount(0, $deleted);

        $task->execute();

        // On 3rd execution, deletion queue should now be empty.
        $deleted = $DB->get_records('tool_ally_deleted_content');
        $this->assertCount(0, $deleted);
    }

    public function test_pre_course_module_delete_forum() {
        $this->pre_course_module_delete_forum();
    }

    public function test_pre_course_module_delete_hsuforum() {
        $this->pre_course_module_delete_forum('hsuforum');
    }

    public function test_pre_course_module_delete_glossary() {
        global $DB, $USER;

        // Prevent it from creating a backup of the deleted module.
        set_config('coursebinenable', 0, 'tool_recyclebin');

        $this->resetAfterTest();
        $this->setAdminUser();

        $course   = $this->getDataGenerator()->create_course();
        $glossary = $this->getDataGenerator()->create_module('glossary',
            [
                'course' => $course->id,
                'introformat' => FORMAT_HTML
            ]
        );

        $record = [
            'course' => $course->id,
            'glossary' => $glossary->id,
            'userid' => $USER->id,
            'definitionformat' => FORMAT_HTML
        ];
        $this->setAdminUser();
        $entry = self::getDataGenerator()->get_plugin_generator(
            'mod_glossary')->create_content($glossary, $record);

        course_delete_module($glossary->cmid);

        $task          = new content_updates_task();
        $task->config  = new push_config('url', 'key', 'sceret');
        $updates = $this->createMock(push_content_updates::class);
        $updates->expects($this->once())
            ->method('send')
            ->with($this->isType('array'));
        $task->updates = $updates;

        $task->execute();

        // Make sure we have some deletion queue records but that none of them are processed.
        $deleted = $DB->get_records_select('tool_ally_deleted_content', 'timeprocessed IS NULL');
        $this->assertCount(2, $deleted);
        $deleted = $DB->get_records_select('tool_ally_deleted_content', 'timeprocessed IS NOT NULL');
        $this->assertCount(0, $deleted);

        $this->assert_deletion_queue_contains('glossary', 'glossary', 'intro', $glossary->id);
        $this->assert_deletion_queue_contains('glossary', 'glossary_entries', 'definition', $entry->id);

        $task->execute();

        // Make sure we have some deletion queue records and all of them are processed.
        $deleted = $DB->get_records_select('tool_ally_deleted_content', 'timeprocessed IS NOT NULL');
        $this->assertCount(2, $deleted);
        $deleted = $DB->get_records_select('tool_ally_deleted_content', 'timeprocessed IS NULL');
        $this->assertCount(0, $deleted);

        $task->execute();

        // On 3rd execution, deletion queue should now be empty.
        $deleted = $DB->get_records('tool_ally_deleted_content');
        $this->assertCount(0, $deleted);
    }

    public function test_performance_delete_glossary() {
        global $DB, $USER;

        // Prevent it from creating a backup of the deleted module.
        set_config('coursebinenable', 0, 'tool_recyclebin');

        $this->resetAfterTest();
        $this->setAdminUser();

        $course   = $this->getDataGenerator()->create_course();
        $glossary = $this->getDataGenerator()->create_module('glossary',
            [
                'course' => $course->id,
                'introformat' => FORMAT_HTML
            ]
        );

        $record = [
            'course' => $course->id,
            'glossary' => $glossary->id,
            'userid' => $USER->id,
            'definitionformat' => FORMAT_HTML
        ];
        $this->setAdminUser();

        $entries = 10001; // Increase this to test larger volumes (100,001 entires = 13 seconds).
        $pushcount = $entries + 1; // Includes the module itself.

        // Create 1001 glossary entries for performance testing.
        for ($e = 0; $e < $entries; $e ++) {
            $entry = self::getDataGenerator()->get_plugin_generator(
                'mod_glossary')->create_content($glossary, $record);
        }
        $start = microtime(true);
        course_delete_module($glossary->cmid);
        fwrite(STDOUT, "\nGlossary deletion took " . (microtime(true) - $start));
        $task          = new content_updates_task();
        $task->config  = new push_config('url', 'key', 'sceret');
        $updates = $this->createMock(push_content_updates::class);
        $sendcount = ceil($pushcount / $task->config->get_batch_size());
        $updates->expects($this->exactly($sendcount))
            ->method('send')
            ->with($this->isType('array'));
        $task->updates = $updates;

        $start = microtime(true);
        $task->execute();
        $execution = (microtime(true) - $start);
        fwrite(STDOUT, "\nFirst task execution took " . $execution . ' seconds');
        $this->assert_deletion_queue_contains('glossary', 'glossary', 'intro', $glossary->id);
        $this->assert_deletion_queue_contains('glossary', 'glossary_entries', 'definition', $entry->id);

        // Make sure we have some deletion queue records but that none of them are processed.
        $deleted = $DB->get_records_select('tool_ally_deleted_content', 'timeprocessed IS NULL');
        $this->assertCount($pushcount, $deleted);
        $deleted = $DB->get_records_select('tool_ally_deleted_content', 'timeprocessed IS NOT NULL');
        $this->assertCount(0, $deleted);

        $start = microtime(true);
        $task->execute();
        $execution = (microtime(true) - $start);
        fwrite(STDOUT, "\n2nd task execution took " . $execution . ' seconds');

        // Make sure we have some deletion queue records and all of them are processed.
        $deleted = $DB->get_records_select('tool_ally_deleted_content', 'timeprocessed IS NOT NULL');
        $this->assertCount($pushcount, $deleted);
        $deleted = $DB->get_records_select('tool_ally_deleted_content', 'timeprocessed IS NULL');
        $this->assertCount(0, $deleted);

        $start = microtime(true);
        $task->execute();
        $execution = (microtime(true) - $start);
        fwrite(STDOUT, "\n3rd task execution took " . $execution . ' seconds');

        // On 3rd execution, deletion queue should now be empty.
        $deleted = $DB->get_records('tool_ally_deleted_content');
        $this->assertCount(0, $deleted);
    }

}
