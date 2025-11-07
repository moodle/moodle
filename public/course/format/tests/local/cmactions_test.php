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

namespace core_courseformat\local;

use core_courseformat\hook\after_cm_name_edited;

/**
 * Course module format actions class tests.
 *
 * @package    core_courseformat
 * @copyright  2024 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\core_courseformat\local\cmactions::class)]
final class cmactions_test extends \advanced_testcase {
    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        parent::setUpBeforeClass();
    }

    /**
     * Test renaming a course module.
     *
     * @param string $newname The new name for the course module.
     * @param bool $expected Whether the course module was renamed.
     * @param bool $expectexception Whether an exception is expected.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provider_test_rename')]
    public function test_rename(string $newname, bool $expected, bool $expectexception): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['format' => 'topics']);
        $activity = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id, 'name' => 'Old name']
        );

        $cmactions = new cmactions($course);

        if ($expectexception) {
            $this->expectException(\moodle_exception::class);
        }
        $result = $cmactions->rename($activity->cmid, $newname);
        $this->assertEquals($expected, $result);

        $cminfo = get_fast_modinfo($course)->get_cm($activity->cmid);
        if ($result) {
            $this->assertEquals($newname, $cminfo->name);
        } else {
            $this->assertEquals('Old name', $cminfo->name);
        }
    }

    /**
     * Data provider for test_rename.
     *
     * @return \Generator
     */
    public static function provider_test_rename(): \Generator {
        yield 'Empty name' => [
            'newname' => '',
            'expected' => false,
            'expectexception' => false,
        ];
        yield 'Maximum length' => [
            'newname' => str_repeat('a', 1333),
            'expected' => true,
            'expectexception' => false,
        ];
        yield 'Beyond maximum length' => [
            'newname' => str_repeat('a', 1334),
            'expected' => false,
            'expectexception' => true,
        ];
        yield 'Valid name' => [
            'newname' => 'New name',
            'expected' => true,
            'expectexception' => false,
        ];
    }

    /**
     * Test rename an activity also rename the calendar events.
     */
    public function test_rename_calendar_events(): void {
        global $DB;
        $this->resetAfterTest();

        $this->setAdminUser();
        set_config('enablecompletion', 1);

        $course = $this->getDataGenerator()->create_course(['enablecompletion' => COMPLETION_ENABLED]);
        $activity = $this->getDataGenerator()->create_module(
            'assign',
            [
                'name' => 'Old name',
                'course' => $course,
                'completionexpected' => time(),
                'duedate' => time(),
            ]
        );
        $cm = get_coursemodule_from_instance('assign', $activity->id, $course->id);

        // Validate course events naming.
        $this->assertEquals(2, $DB->count_records('event'));

        $event = $DB->get_record(
            'event',
            ['modulename' => 'assign', 'instance' => $activity->id, 'eventtype' => 'due']
        );
        $this->assertEquals(
            get_string('calendardue', 'assign', 'Old name'),
            $event->name
        );

        $event = $DB->get_record(
            'event',
            ['modulename' => 'assign', 'instance' => $activity->id, 'eventtype' => 'expectcompletionon']
        );
        $this->assertEquals(
            get_string('completionexpectedfor', 'completion', (object) ['instancename' => 'Old name']),
            $event->name
        );

        // Rename activity.
        $cmactions = new cmactions($course);
        $result = $cmactions->rename($activity->cmid, 'New name');
        $this->assertTrue($result);

        // Validate event renaming.
        $event = $DB->get_record(
            'event',
            ['modulename' => 'assign', 'instance' => $activity->id, 'eventtype' => 'due']
        );
        $this->assertEquals(
            get_string('calendardue', 'assign', 'New name'),
            $event->name
        );

        $event = $DB->get_record(
            'event',
            ['modulename' => 'assign', 'instance' => $activity->id, 'eventtype' => 'expectcompletionon']
        );
        $this->assertEquals(
            get_string('completionexpectedfor', 'completion', (object) ['instancename' => 'New name']),
            $event->name
        );
    }

    /**
     * Test renaming an activity trigger a course update log event.
     */
    public function test_rename_course_module_updated_event(): void {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id, 'name' => 'Old name']
        );

        $sink = $this->redirectEvents();

        $cmactions = new cmactions($course);
        $result = $cmactions->rename($activity->cmid, 'New name');
        $this->assertTrue($result);

        $events = $sink->get_events();
        $event = reset($events);

        // Check that the event data is valid.
        $this->assertInstanceOf('\core\event\course_module_updated', $event);
        $this->assertEquals(\context_module::instance($activity->cmid), $event->get_context());
    }

    /**
     * Test renaming an activity triggers the after_cm_name_edited hook.
     */
    public function test_rename_after_cm_name_edited_hook(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id, 'name' => 'Old name']
        );

        $executedhook = null;

        $testcallback = function(after_cm_name_edited $hook) use (&$executedhook): void {
            $executedhook = $hook;
        };
        $this->redirectHook(after_cm_name_edited::class, $testcallback);

        $cmactions = new cmactions($course);
        $result = $cmactions->rename($activity->cmid, 'New name');
        $this->assertTrue($result);

        $this->assertEquals($activity->cmid, $executedhook->get_cm()->id);
        $this->assertEquals('New name', $executedhook->get_newname());
    }


    /**
     * Tests the function that deletes a course module.
     */
    public function test_delete(): void {
        global $DB, $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Generate an assignment with due date (will generate a course event).
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => COMPLETION_ENABLED]);
        $module = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id, 'duedate' => time()],
            ['completion' => COMPLETION_TRACKING_MANUAL],
        );
        $modcontext = \context_module::instance($module->cmid);
        $cm = $DB->get_record('course_modules', ['id' => $module->cmid]);
        $this->assertInstanceOf('context_module', $modcontext);
        $this->assertEquals(1, $DB->count_records('event', ['instance' => $module->id, 'modulename' => 'assign']));

        // Create blog entry associated to the module.
        /** @var \core_blog_generator $blogsgenerator */
        $blogsgenerator = $this->getDataGenerator()->get_plugin_generator('core_blog');
        $user = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $blogentry = $blogsgenerator->create_entry([
            'publishstate' => 'site',
            'userid' => $user->id,
            'subject' => 'My blog',
            'summary' => 'Animals',
            'tags' => ['cat', 'fish'],
            'courseid' => $course->id,
        ]);
        $blogentry->add_association($modcontext->id);
        $this->assertEquals(1, $DB->count_records('post', ['id' => $blogentry->id]));
        $this->assertEquals(1, $DB->count_records('blog_association', ['contextid' => $modcontext->id]));
        $this->assertEquals(2, $DB->count_records('tag_instance', ['itemid' => $blogentry->id]));

        // Completion data.
        $completion = new \completion_info($course);
        $completion->update_state($cm, COMPLETION_COMPLETE);
        $this->assertEquals(1, $DB->count_records('course_modules_completion', ['coursemoduleid' => $cm->id]));

        // Add some tags to this module.
        \core_tag_tag::set_item_tags('mod_assign', 'assign', $module->id, $modcontext, ['Tag 1', 'Tag 2', 'Tag 3']);
        \core_tag_tag::set_item_tags('core', 'course_modules', $module->cmid, $modcontext, ['Tag 3', 'Tag 4', 'Tag 5']);
        $criteria = ['component' => 'mod_assign', 'itemtype' => 'assign', 'contextid' => $modcontext->id];
        $this->assertEquals(3, $DB->count_records('tag_instance', $criteria));
        $criteria = ['component' => 'core', 'itemtype' => 'course_modules', 'contextid' => $modcontext->id];
        $this->assertEquals(3, $DB->count_records('tag_instance', $criteria));

        // To capture the event.
        $sink = $this->redirectEvents();

        // Run delete.
        $cmactions = new cmactions($course);
        $cmactions->delete($module->cmid);

        // Verify the context has been removed.
        $this->assertFalse(\context_module::instance($module->cmid, IGNORE_MISSING));

        // Verify the course_module record has been deleted.
        $this->assertEmpty($DB->count_records('course_modules', ['id' => $module->cmid]));

        // Verify the course_modules_completion record has been deleted.
        $this->assertEmpty($DB->count_records('course_modules_completion', ['coursemoduleid' => $module->cmid]));

        // Verify the blog_association record has been deleted.
        $this->assertEmpty($DB->count_records('blog_association', ['contextid' => $modcontext->id]));

        // Verify the blog post record has been deleted.
        $this->assertEmpty($DB->count_records('post', ['id' => $blogentry->id]));

        // Verify the tag instance record has been deleted.
        $this->assertEmpty($DB->count_records('tag_instance', ['itemid' => $blogentry->id]));

        // Verify events have been removed.
        $this->assertEmpty($DB->count_records('event', ['instance' => $module->id, 'modulename' => 'assign']));

        // Verify the tag instances were deleted.
        $criteria = ['component' => 'mod_assign', 'contextid' => $modcontext->id];
        $this->assertEmpty($DB->count_records('tag_instance', $criteria));
        $criteria = ['component' => 'core', 'itemtype' => 'course_modules', 'contextid' => $modcontext->id];
        $this->assertEmpty($DB->count_records('tag_instance', $criteria));

        // Check the event is triggered.
        $events = $sink->get_events();
        $sink->close();
        $count = 0;
        while (!empty($events)) {
            $event = array_pop($events);
            if ($event instanceof \core\event\course_module_deleted) {
                $count++;
                // Check that the event data is valid.
                $this->assertInstanceOf('\core\event\course_module_deleted', $event);
                $this->assertEquals($module->cmid, $event->objectid);
                $this->assertEquals($USER->id, $event->userid);
                $this->assertEquals('course_modules', $event->objecttable);
                $this->assertEquals(null, $event->get_url());
                $this->assertEquals($cm, $event->get_record_snapshot('course_modules', $module->cmid));
            }
        }
        $this->assertEquals(1, $count);
    }

    /**
     * Tests the function that deletes a course module.
     */
    public function test_delete_module_with_questions(): void {
        global $DB, $USER;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Generate a quiz.
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => COMPLETION_ENABLED]);
        $module = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id]);
        $modcontext = \context_module::instance($module->cmid);

        // Add some questions to this module.
        /** @var \core_question_generator $qgen */
        $qgen = $this->getDataGenerator()->get_plugin_generator('core_question');
        $qcat = $qgen->create_question_category(['contextid' => $modcontext->id]);
        $qgen->create_question('shortanswer', null, ['category' => $qcat->id]);
        $qgen->create_question('shortanswer', null, ['category' => $qcat->id]);
        $this->assertEquals(2, $DB->count_records('question'));

        // Run delete.
        $cmactions = new cmactions($course);
        $cmactions->delete($module->cmid);

        // Verify the context has been removed.
        $this->assertFalse(\context_module::instance($module->cmid, IGNORE_MISSING));

        // Verify the course_module record has been deleted.
        $this->assertEmpty($DB->count_records('course_modules', ['id' => $module->cmid]));

        // Verify events have been removed.
        $this->assertEmpty($DB->count_records('event', ['instance' => $module->id, 'modulename' => 'quiz']));

        // Verify the category and questions were deleted.
        $this->assertEquals(0, $DB->count_records('question_categories', ['contextid' => $modcontext->id]));
        $this->assertEquals(0, $DB->count_records('question'));
    }

    /**
     * Tests the function that deletes a course module with wrong cmid.
     */
    public function test_delete_wrong_cmid(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'duedate' => time()]);
        $this->assertEquals(1, $DB->count_records('course_modules'));

        $cmactions = new cmactions($course);
        $cmactions->delete(99999); // Non existing cmid.

        // Verify the course_module record has not been deleted.
        $this->assertEquals(1, $DB->count_records('course_modules'));
    }

    /**
     * Tests the function that deletes a course module with missing lib.php file.
     */
    public function test_delete_missinglib(): void {
        global $DB;
        $this->resetAfterTest();

        // Generate test data.
        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'duedate' => time()]);
        $cm = $DB->get_record('course_modules', ['id' => $module->cmid]);
        $this->assertEquals(1, $DB->count_records('course_modules'));

        // Modify module name to make an exception when deleting.
        $module = $DB->get_record('modules', ['id' => $cm->module], 'id, name', MUST_EXIST);
        $module->name = 'TestModuleToDelete';
        $DB->update_record('modules', $module);

        // Delete the module.
        $cmactions = new cmactions($course);
        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage('Missing file mod/TestModuleToDelete/lib.php');
        $cmactions->delete($cm->id);

        // Verify the course_module record has not been deleted.
        $this->assertEquals(1, $DB->count_records('course_modules'));
    }


    /**
     * Tests the function that deletes a course module async way.
     */
    public function test_async_module_deletion_hook_implemented(): void {
        // Async module deletion depends on the 'true' being returned by at least one plugin implementing the hook,
        // 'course_module_adhoc_deletion_recommended'. In core, is implemented by the course recyclebin, which will only return
        // true if the recyclebin plugin is enabled. To make sure async deletion occurs, this test force-enables the recyclebin.
        global $DB, $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Ensure recyclebin is enabled.
        set_config('coursebinenable', true, 'tool_recyclebin');

        // Create course, module and context.
        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $modcontext = \context_module::instance($module->cmid);

        // Check events generated when deleting module.
        $sink = $this->redirectEvents();

        // Try to delete the module asynchronously.
        $cmactions = new cmactions($course);
        $cmactions->delete($module->cmid, true);

        // Verify that no event has been generated yet.
        $events = $sink->get_events();
        $event = array_pop($events);
        $sink->close();
        $this->assertEmpty($event);

        // Verify the course_module hasn't been deleted yet.
        $this->assertEquals(1, $DB->count_records('course_modules', ['id' => $module->cmid]));

        // Grab the record, in it's final state before hard deletion, for comparison with the event snapshot.
        // We need to do this because the 'deletioninprogress' flag has changed from '0' to '1'.
        $cm = $DB->get_record('course_modules', ['id' => $module->cmid], '*', MUST_EXIST);

        // Verify the course_module is marked as 'deletioninprogress'.
        $this->assertNotEquals($cm, false);
        $this->assertEquals($cm->deletioninprogress, '1');

        // Verify the context has not yet been removed.
        $this->assertEquals($modcontext, \context_module::instance($module->cmid, IGNORE_MISSING));

        // Set up a sink to catch the 'course_module_deleted' event.
        $sink = $this->redirectEvents();

        // Now, run the adhoc task which performs the hard deletion.
        \phpunit_util::run_all_adhoc_tasks();

        // Fetch and validate the event data.
        $events = $sink->get_events();
        $event = array_pop($events);
        $sink->close();
        $this->assertInstanceOf('\core\event\course_module_deleted', $event);
        $this->assertEquals($module->cmid, $event->objectid);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals('course_modules', $event->objecttable);
        $this->assertEquals(null, $event->get_url());
        $this->assertEquals($cm, $event->get_record_snapshot('course_modules', $module->cmid));

        // Verify the context has been removed.
        $this->assertFalse(\context_module::instance($module->cmid, IGNORE_MISSING));

        // Verify the course_module record has been deleted.
        $this->assertEquals(0, $DB->count_records('course_modules', ['id' => $module->cmid]));
    }

    /**
     * Tests the function that deletes a course module async way when no plugin implements the hook.
     */
    public function test_async_module_deletion_hook_not_implemented(): void {
        // Only proceed if we are sure that no plugin is going to advocate async removal of a module. I.e. no plugin returns
        // 'true' from the 'course_module_adhoc_deletion_recommended' hook.
        // In the case of core, only recyclebin implements this hook, and it will only return true if enabled, so disable it.
        global $DB, $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Ensure recyclebin is disabled.
        set_config('coursebinenable', false, 'tool_recyclebin');

        // Non-core plugins might implement the 'course_module_adhoc_deletion_recommended' hook and spoil this test.
        // If at least one plugin still returns true, then skip this test.
        if ($pluginsfunction = get_plugins_with_function('course_module_background_deletion_recommended')) {
            foreach ($pluginsfunction as $plugintype => $plugins) {
                foreach ($plugins as $pluginfunction) {
                    if ($pluginfunction()) {
                        $this->markTestSkipped();
                    }
                }
            }
        }

        // Create course, module and context.
        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('assign', ['course' => $course->id]);
        $cm = $DB->get_record('course_modules', ['id' => $module->cmid], '*', MUST_EXIST);

        // Check events generated when deleting module.
        $sink = $this->redirectEvents();

        // Try to delete the module asynchronously.
        $cmactions = new cmactions($course);
        $cmactions->delete($module->cmid, true);

        // Fetch and validate the event data.
        $events = $sink->get_events();
        $event = array_pop($events);
        $sink->close();
        $this->assertInstanceOf('\core\event\course_module_deleted', $event);
        $this->assertEquals($module->cmid, $event->objectid);
        $this->assertEquals($USER->id, $event->userid);
        $this->assertEquals('course_modules', $event->objecttable);
        $this->assertEquals(null, $event->get_url());
        $this->assertEquals($cm, $event->get_record_snapshot('course_modules', $module->cmid));

        // Verify the context has been removed.
        $this->assertFalse(\context_module::instance($module->cmid, IGNORE_MISSING));

        // Verify the course_module record has been deleted.
        $this->assertEquals(0, $DB->count_records('course_modules', ['id' => $module->cmid]));
    }

    /**
     * Test setting group mode on a course module.
     */
    public function test_set_set_groupmode(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id, 'name' => 'Old name']
        );
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $this->assertEquals(NOGROUPS, $cm->groupmode);

        $cmactions = new cmactions($course);
        $result = $cmactions->set_groupmode($activity->cmid, VISIBLEGROUPS);
        $this->assertTrue($result);
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $this->assertEquals(VISIBLEGROUPS, $cm->groupmode);

        $result = $cmactions->set_groupmode($activity->cmid, SEPARATEGROUPS);
        $this->assertTrue($result);
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $this->assertEquals(SEPARATEGROUPS, $cm->groupmode);

        // Check that return value is false when setting the same group mode.
        $returnval = $cmactions->set_groupmode($activity->cmid, SEPARATEGROUPS);
        $this->assertFalse($returnval);
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $this->assertEquals(SEPARATEGROUPS, $cm->groupmode); // Not changed.
    }

    /**
     * Test setting group mode on a course module that does not exist.
     */
    public function test_set_set_groupmode_cm_doesnotexist(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $this->expectException(\dml_missing_record_exception::class);
        $cmactions = new cmactions($course);
        $cmactions->set_groupmode(10000, VISIBLEGROUPS);
    }
}
