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

use core_courseformat\formatactions;
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

        $testcallback = function (after_cm_name_edited $hook) use (&$executedhook): void {
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
     * Test moving an activity at the end of a section or course.
     */
    public function test_move_end_section(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['numsections' => 3]);
        $activities = [];
        for ($activityindex = 0; $activityindex < 3; $activityindex++) {
            $activities[] = $this->getDataGenerator()->create_module(
                'assign',
                ['course' => $course->id, 'name' => 'Activity ' . $activityindex, 'section' => 1]
            );
        }
        $cms = get_fast_modinfo($course)->get_cms();
        // Just assert that the activities are in the expected initial order.
        $this->assertEquals(
            [
                'Activity 0',
                'Activity 1',
                'Activity 2',
            ],
            array_values(array_map(fn($cminfo) => $cminfo->name, $cms))
        );
        $cmactions = new cmactions($course);
        $section2 = get_fast_modinfo($course)->get_section_info(2);
        // Move to section 2, at the end of the section.
        $this->assertTrue($cmactions->move_end_section($activities[1]->cmid, $section2->id));
        $cms = get_fast_modinfo($course)->get_cms();
        $this->assertEquals(
            [
                'Activity 0',
                'Activity 2',
                'Activity 1',
            ],
            array_values(array_map(fn($cminfo) => $cminfo->name, $cms))
        );
        // Activity 1 is now in section 2.
        $this->assertEquals(2, $cms[$activities[1]->cmid]->sectionnum);

        // Create an extra invisible section to test moving to non visible sections.
        $section4 = $this->getDataGenerator()->create_course_section(['course' => $course->id, 'section' => 4]);
        formatactions::section($course)->update($section4, ['visible' => 0]);
        // Make sure we retrieve fresh data.
        $section4 = get_fast_modinfo($course)->get_section_info(4);
        $this->assertTrue($cmactions->move_end_section($activities[1]->cmid, $section4->id));
        // Activity 1 is now in section 4.
        $this->assertEquals(4, get_fast_modinfo($course)->get_cm($activities[1]->cmid)->sectionnum);
        $cms = get_fast_modinfo($course)->get_cms();
        $this->assertEquals(0, $cms[$activities[1]->cmid]->visible);

        // Now move it back to section 1, at the end of the section, and check that it becomes visible again.
        $section1 = get_fast_modinfo($course)->get_section_info(1);
        $this->assertTrue($cmactions->move_end_section($activities[1]->cmid, $section1->id));
        $this->assertEquals(1, get_fast_modinfo($course)->get_cm($activities[1]->cmid)->sectionnum);
        $cms = get_fast_modinfo($course)->get_cms();
        $this->assertEquals(1, $cms[$activities[1]->cmid]->visible);
    }

    /**
     * Test moving an activity before another activity.
     */
    public function test_move_before(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['numsections' => 3]);
        $activities = [];
        for ($activityindex = 0; $activityindex < 3; $activityindex++) {
            $activities[] = $this->getDataGenerator()->create_module(
                'assign',
                ['course' => $course->id, 'name' => 'Activity ' . $activityindex, 'section' => 1]
            );
        }
        $cms = get_fast_modinfo($course)->get_cms();
        // Just assert that the activities are in the expected initial order.
        $this->assertEquals(
            [
                'Activity 0',
                'Activity 1',
                'Activity 2',
            ],
            array_values(array_map(fn($cminfo) => $cminfo->name, $cms))
        );
        $cmactions = new cmactions($course);
        // Invalid move, cannot move after itself.
        $this->assertFalse($cmactions->move_before($activities[0]->cmid, $activities[0]->cmid));
        // Move activity 0 before activity 2.
        $this->assertTrue($cmactions->move_before($activities[0]->cmid, $activities[2]->cmid));
        $cms = get_fast_modinfo($course)->get_cms();
        $this->assertEquals(
            [
                'Activity 1',
                'Activity 0',
                'Activity 2',
            ],
            array_values(array_map(fn($cminfo) => $cminfo->name, $cms))
        );

        $this->assertTrue($cmactions->move_before($activities[1]->cmid, $activities[0]->cmid));
        $cms = get_fast_modinfo($course)->get_cms();
        $this->assertEquals(
            [
                'Activity 1',
                'Activity 0',
                'Activity 2',
            ],
            array_values(array_map(fn($cminfo) => $cminfo->name, $cms))
        );

        // Create an extra invisible section to test moving to non visible sections.
        $section4 = $this->getDataGenerator()->create_course_section(['course' => $course->id, 'section' => 4]);
        formatactions::section($course)->update($section4, ['visible' => 0]);
        $activitysection4 = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id, 'name' => 'Activity section 4', 'section' => 4]
        );
        // Make sure we retrieve fresh data.
        $section4 = get_fast_modinfo($course)->get_section_info(4);
        $this->assertTrue($cmactions->move_before($activities[1]->cmid, $activitysection4->cmid));
        // Activity 1 is now in section 4.
        $this->assertEquals(4, get_fast_modinfo($course)->get_cm($activities[1]->cmid)->sectionnum);
        $cms = get_fast_modinfo($course)->get_cms();
        // And not visible.
        $this->assertEquals(0, $cms[$activities[1]->cmid]->visible);

        // Now move it back to section 1, at the end of the section, and check that it becomes visible again.
        $this->assertTrue($cmactions->move_before($activities[1]->cmid, $activities[0]->cmid));
        $this->assertEquals(1, get_fast_modinfo($course)->get_cm($activities[1]->cmid)->sectionnum);
        $cms = get_fast_modinfo($course)->get_cms();
        // And visible again.
        $this->assertEquals(1, $cms[$activities[1]->cmid]->visible);
    }
    /**
     * Test moving an activity with feature_can_display = false before another activity.
     */
    public function test_move_before_feature_can_display(): void {
        global $DB;
        $this->resetAfterTest();
        $generator = self::getDataGenerator();

        // Create course with 1 section.
        $course = self::getDataGenerator()->create_course(['numsections' => 2], ['createsections' => true]);

        // Create the module and assert in section 0.
        $sectionzero = $DB->get_record('course_sections', ['course' => $course->id, 'section' => 0], '*', MUST_EXIST);
        $module = $generator->create_module('qbank', ['course' => $course, 'section' => $sectionzero->section]);
        $beforemodule = $generator->create_module(
            'assign',
            ['course' => $course->id, 'name' => 'Activity before', 'section' => 1]
        );
        // Try to add to section 1.
        $this->expectExceptionMessage("Modules with FEATURE_CAN_DISPLAY set to false can not be moved from section 0");

        $cmactions = new cmactions($course);
        // Invalid move, cannot move module with feature_can_display = false to section other than 0.
        $cmactions->move_before($module->cmid, $beforemodule->cmid);
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

    /**
     * Test duplicating a course module.
     *
     * @param array $coursedata Array defining the course structure. Keys are section names, values are arrays of cm names.
     * @param string $cmname Name of the course module to duplicate.
     * @param string|null $sectionname Name of the section to duplicate into, or null to duplicate into the same section.
     * @param string|null $newname New name for the duplicated course module, or null to use default naming
     * (original name + ' (copy)').
     * @param array $expected Expected result array with keys: 'section' (int), 'position' (int), 'name' (string).
     * @return void
     *
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('duplicate_provider')]
    public function test_duplicate(
        array $coursedata,
        string $cmname,
        ?string $sectionname,
        ?string $newname,
        array $expected
    ): void {
        $this->resetAfterTest();

        $course = $this->create_course_from_data($coursedata);
        // Lookup cmid and sectionid based on names.
        $cmactions = new cmactions($course);
        $modinfo = get_fast_modinfo($course);
        $targetsectionid = null;
        $allcms = $modinfo->get_cms();
        $allcmsbyname = array_combine(
            array_map(fn($cminfo) => $cminfo->get_name(), $allcms),
            $allcms
        );
        $cmid = $allcmsbyname[$cmname]->id;

        $allsectionsbyname = $this->get_sections_by_name($course);
        if ($sectionname !== null) {
            $targetsectionid = $allsectionsbyname[$sectionname]->id;
        }
        // For backup/restore operations, we need to be logged in.
        $this->setAdminUser();
        $newcm = $cmactions->duplicate(
            cmid: $cmid,
            targetsectionid: $targetsectionid,
            newname: $newname,
        );
        // Verify expected result.
        $mappedcourse = [];
        $modinfo = get_fast_modinfo($course); // Refresh modinfo.
        foreach ($modinfo->get_section_info_all() as $sectioninfo) {
            if (empty($sectioninfo->name)) {
                continue; // Ignore sections without a name.
            }
            $mappedcourse[$sectioninfo->name] = [];
            foreach ($sectioninfo->get_sequence_cm_infos() as $cminfo) {
                $mappedcourse[$sectioninfo->name][] = $cminfo->name;
            }
        }
        $this->assertEquals($expected, $mappedcourse);

        // We ignore obvious differences and also sections information as it is already tested above (and
        // can differ due to section movements).
        $ignoredproperties = ['id', 'url', 'instance', 'added', 'context', 'section', 'sectionid', 'sectionnum'];
        // Make sure they are the same, except obvious id changes.
        foreach ($modinfo->get_cm($cmid) as $prop => $value) {
            if (in_array($prop, $ignoredproperties, true)) {
                // Ignore obviously different properties.
                continue;
            }
            if ($prop == 'name') {
                if (empty($newname)) {
                    $value = get_string('duplicatedmodule', 'moodle', $value);
                } else {
                    $value = $newname;
                }
            }
            $this->assertEquals($value, $newcm->$prop);
        }
    }

    /**
     * Data provider for test_duplicate.
     *
     * @return \Generator
     */
    public static function duplicate_provider(): \Generator {
        yield 'duplicate after current module, no name provided' => [
            'coursedata' => [
                'Section 1' => [
                    'cm1',
                    'cm2',
                    'cm3',
                ],
            ],
            'cmname' => 'cm1',
            'sectionname' => null,
            'newname' => null,
            'expected' => [
                'Section 1' => [
                    'cm1',
                    'cm1 (copy)',
                    'cm2',
                    'cm3',
                ],
            ],
        ];

        yield 'duplicate after current module, name provided' => [
            'coursedata' => [
                'Section 1' => [
                    'cm1',
                    'cm2',
                    'cm3',
                ],
            ],
            'cmname' => 'cm1',
            'sectionname' => null,
            'newname' => 'New name',
            'expected' => [
                'Section 1' => [
                    'cm1',
                    'New name',
                    'cm2',
                    'cm3',
                ],
            ],
        ];
        yield 'duplicate at the end of a section, name not provided' => [
            'coursedata' => [
                'Section 1' => [
                    'cm1',
                ],
                'Section 2' => [
                    'cm2',
                ],
            ],
            'cmname' => 'cm1',
            'sectionname' => 'Section 2',
            'newname' => null,
            'expected' => [
                'Section 1' => [
                    'cm1',
                ],
                'Section 2' => [
                    'cm2',
                    'cm1 (copy)',
                ],
            ],
        ];
        yield 'duplicate at the end of a section, name provided' => [
            'coursedata' => [
                'Section 1' => [
                    'cm1',
                ],
                'Section 2' => [
                    'cm2',
                ],
            ],
            'cmname' => 'cm1',
            'sectionname' => 'Section 2',
            'newname' => 'New name',
            'expected' => [
                'Section 1' => [
                    'cm1',
                ],
                'Section 2' => [
                    'cm2',
                    'New name',
                ],
            ],
        ];
    }

    /**
     * Test duplicating a course with wrong cmid (from another course).
     *
     * @return void
     */
    public function test_duplicate_wrong_cm(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();

        $course1  = $generator->create_course();
        $course2  = $generator->create_course();
        $cm5 = $generator->create_module(
            'assign',
            ['course' => $course2->id, 'name' => 'cm5', 'section' => 1],
        );

        // Lookup cmid and sectionid based on names.
        $cmactions = new cmactions($course1);
        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage('Invalid course module ID: ' . $cm5->cmid);
        // For backup/restore operations, we need to be logged in.
        $this->setAdminUser();
        $this->assertFalse(
            $cmactions->duplicate(
                cmid: $cm5->cmid,
            )
        );
    }

    /**
     * Test duplicating a course with wrong targetsectionid.
     *
     * @return void
     */
    public function test_duplicate_wrong_targetsectionid(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['numsections' => 2]);
        $cm = $this->getDataGenerator()->create_module(
            'assign',
            ['course' => $course->id, 'name' => 'cm1', 'section' => 1],
        );
        $cmactions = new cmactions($course);
        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage('This section does not exist');
        // For backup/restore operations, we need to be logged in.
        $this->setAdminUser();
        $this->assertFalse(
            $cmactions->duplicate(
                cmid: $cm->cmid,
                targetsectionid: 99999,
            )
        );
    }

    /**
     * Test that duplicating a module triggers the expected event.
     */
    public function test_duplicate_module_created_event(): void {
        global $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create an assign module.
        $sink = $this->redirectEvents();
        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('assign', ['course' => $course]);
        $sink->clear(); // Make sure we only capture events from duplication.
        // Lookup cmid and sectionid based on names.
        $cmactions = new cmactions($course);
        $newcm = $cmactions->duplicate($module->cmid);
        $events = $sink->get_events();
        $eventscount = 0;
        $sink->close();

        foreach ($events as $event) {
            if ($event instanceof \core\event\course_module_created) {
                $eventscount++;
                // Validate event data.
                $this->assertInstanceOf('\core\event\course_module_created', $event);
                $this->assertEquals($newcm->id, $event->objectid);
                $this->assertEquals($USER->id, $event->userid);
                $this->assertEquals($course->id, $event->courseid);
                $url = new \core\url('/mod/assign/view.php', ['id' => $newcm->id]);
                $this->assertEquals($url, $event->get_url());
            }
        }
        // Only one \core\event\course_module_created event should be triggered.
        $this->assertEquals(1, $eventscount);
    }

    /**
     * Test that permissions are correctly duplicated when duplicating a module.
     */
    public function test_duplicate_module_permissions(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create course and course module.
        $course = self::getDataGenerator()->create_course();
        $res = self::getDataGenerator()->create_module('assign', ['course' => $course]);
        $cm = get_coursemodule_from_id('assign', $res->cmid, 0, false, MUST_EXIST);
        $cmcontext = \context_module::instance($cm->id);

        // Enrol student user.
        $user = self::getDataGenerator()->create_user();
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST);
        self::getDataGenerator()->enrol_user($user->id, $course->id, $roleid);

        // Add capability to original course module.
        assign_capability('gradereport/grader:view', CAP_ALLOW, $roleid, $cmcontext->id);

        // Duplicate module.
        $cmactions = new cmactions($course);
        $newcm = $cmactions->duplicate($res->cmid);
        $newcmcontext = \context_module::instance($newcm->id);

        // Assert that user still has capability.
        $this->assertTrue(has_capability('gradereport/grader:view', $newcmcontext, $user));

        // Assert that both modules contain the same count of overrides.
        $overrides = $DB->get_records('role_capabilities', ['contextid' => $cmcontext->id]);
        $newoverrides = $DB->get_records('role_capabilities', ['contextid' => $newcmcontext->id]);
        $this->assertEquals(count($overrides), count($newoverrides));
    }

    /**
     * Test that calendar events are correctly duplicated when duplicating a module with a due date.
     */
    public function test_duplicate_calendar_event(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = self::getDataGenerator()->create_course();
        $duedate = time() + 3600;
        $module = self::getDataGenerator()->create_module('assign', ['course' => $course, 'duedate' => $duedate]);

        $event = $DB->get_record('event', [
            'modulename' => 'assign',
            'instance' => $module->id,
            'eventtype' => 'due',
        ], '*', MUST_EXIST);
        $this->assertEquals($duedate, $event->timestart);

        $cmactions = new cmactions($course);
        $newcm = $cmactions->duplicate($module->cmid);

        $newevent = $DB->get_record('event', [
            'modulename' => 'assign',
            'instance' => $newcm->instance,
            'eventtype' => 'due',
        ], '*', MUST_EXIST);
        $this->assertEquals($duedate, $newevent->timestart);
    }

    /**
     * Test that local permissions are correctly duplicated when duplicating a module.
     */
    public function test_duplicate_module_role_assignments(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create course and course module.
        $course = self::getDataGenerator()->create_course();
        $res = self::getDataGenerator()->create_module('assign', ['course' => $course]);
        $cm = get_coursemodule_from_id('assign', $res->cmid, 0, false, MUST_EXIST);
        $cmcontext = \context_module::instance($cm->id);

        // Enrol student user.
        $user = self::getDataGenerator()->create_user();
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST);
        self::getDataGenerator()->enrol_user($user->id, $course->id, $roleid);

        // Assign user a new local role.
        $newroleid = $DB->get_field('role', 'id', ['shortname' => 'editingteacher'], MUST_EXIST);
        role_assign($newroleid, $user->id, $cmcontext->id);

        // Duplicate module.
        $cmactions = new cmactions($course);
        $newcm = $cmactions->duplicate($res->cmid);
        $newcmcontext = \context_module::instance($newcm->id);

        // Assert that user still has role assigned.
        $this->assertTrue(user_has_role_assignment($user->id, $newroleid, $newcmcontext->id));

        // Assert that both modules contain the same count of overrides.
        $overrides = $DB->get_records('role_assignments', ['contextid' => $cmcontext->id]);
        $newoverrides = $DB->get_records('role_assignments', ['contextid' => $newcmcontext->id]);
        $this->assertEquals(count($overrides), count($newoverrides));
    }

    /**
     * Helper function to create a course from given data.
     *
     * @param array $coursedata Array defining the course structure.
     * @return \stdClass The created course object.
     */
    private function create_course_from_data(array $coursedata): \stdClass {
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['numsections' => count($coursedata), 'initsections' => 1]);
        $allsections = $this->get_sections_by_name($course);
        $allsectionsbyname = array_filter($allsections, fn($section) => !empty($section->name));
        // Create course modules as per $coursedata.
        foreach ($coursedata as $sectionname => $cmlist) {
            $section  = $allsectionsbyname[$sectionname];
            foreach ($cmlist as $cm) {
                $generator->create_module(
                    'assign',
                    ['course' => $course->id, 'name' => $cm, 'section' => $section->section],
                );
            }
        }
        return $course;
    }

    /**
     * Helper function to get sections by name.
     *
     * @param \stdClass $course The course object.
     * @return array Array of sections indexed by their names.
     */
    private function get_sections_by_name(\stdClass $course): array {
        $modinfo = get_fast_modinfo($course);
        $allsections = $modinfo->get_section_info_all();
        return array_combine(
            array_map(fn($section) => $section->name, $allsections),
            $allsections
        );
    }
}
