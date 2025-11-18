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
 * Unit tests for mod_folder lib
 *
 * @package    mod_folder
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
namespace mod_folder;

use context_user;
use context_module;

defined('MOODLE_INTERNAL') || die();


/**
 * Unit tests for mod_folder lib
 *
 * @package    mod_folder
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
final class lib_test extends \advanced_testcase {

    /**
     * Setup.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();
    }

    /**
     * Prepares things before this test case is initialised
     * @return void
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/mod/folder/lib.php');
        parent::setUpBeforeClass();
    }

    /**
     * Test folder_view
     * @return void
     */
    public function test_folder_view(): void {
        global $CFG;

        $CFG->enablecompletion = 1;

        // Setup test data.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $folder = $this->getDataGenerator()->create_module('folder', array('course' => $course->id),
                                                            array('completion' => 2, 'completionview' => 1));
        $context = \context_module::instance($folder->cmid);
        $cm = get_coursemodule_from_instance('folder', $folder->id);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        folder_view($folder, $course, $cm, $context);

        $events = $sink->get_events();
        // 2 additional events thanks to completion.
        $this->assertCount(3, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_folder\event\course_module_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $moodleurl = new \moodle_url('/mod/folder/view.php', array('id' => $cm->id));
        $this->assertEquals($moodleurl, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());

        // Check completion status.
        $completion = new \completion_info($course);
        $completiondata = $completion->get_data($cm);
        $this->assertEquals(1, $completiondata->completionstate);
    }

    public function test_folder_core_calendar_provide_event_action(): void {
        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $folder = $this->getDataGenerator()->create_module('folder', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $folder->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_folder_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('view'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_folder_core_calendar_provide_event_action_for_non_user(): void {
        global $CFG;

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create the activity.
        $folder = $this->getDataGenerator()->create_module('folder', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $folder->id,
                \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Now, log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_folder_core_calendar_provide_event_action($event, $factory);

        // Confirm the event is not shown at all.
        $this->assertNull($actionevent);
    }

    public function test_folder_core_calendar_provide_event_action_in_hidden_section(): void {
        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a student.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create the activity.
        $folder = $this->getDataGenerator()->create_module('folder', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $folder->id,
                \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Set sections 0 as hidden.
        $sectioninfo = get_fast_modinfo($course->id)->get_section_info(0);
        \core_courseformat\formatactions::section($course->id)->set_visibility($sectioninfo, false);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_folder_core_calendar_provide_event_action($event, $factory, $student->id);

        // Confirm the event is not shown at all.
        $this->assertNull($actionevent);
    }

    public function test_folder_core_calendar_provide_event_action_for_user(): void {
        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Create a student.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create the activity.
        $folder = $this->getDataGenerator()->create_module('folder', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $folder->id,
                \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Now, log out.
        $this->setUser();

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_folder_core_calendar_provide_event_action($event, $factory, $student->id);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('view'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_folder_core_calendar_provide_event_action_already_completed(): void {
        global $CFG;

        $CFG->enablecompletion = 1;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $folder = $this->getDataGenerator()->create_module('folder', array('course' => $course->id),
            array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Get some additional data.
        $cm = get_coursemodule_from_instance('folder', $folder->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $folder->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed.
        $completion = new \completion_info($course);
        $completion->set_module_viewed($cm);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_folder_core_calendar_provide_event_action($event, $factory);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    public function test_folder_core_calendar_provide_event_action_already_completed_for_user(): void {
        global $CFG;

        $CFG->enablecompletion = 1;

        // Create a course.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));

        // Create a student.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create the activity.
        $folder = $this->getDataGenerator()->create_module('folder', array('course' => $course->id),
                array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Get some additional data.
        $cm = get_coursemodule_from_instance('folder', $folder->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $folder->id,
                \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed for the student.
        $completion = new \completion_info($course);
        $completion->set_module_viewed($cm, $student->id);

        // Now, log out.
        $this->setUser();

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_folder_core_calendar_provide_event_action($event, $factory, $student->id);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    /**
     * Creates an action event.
     *
     * @param int $courseid The course id.
     * @param int $instanceid The instance id.
     * @param string $eventtype The event type.
     * @return bool|calendar_event
     */
    private function create_action_event($courseid, $instanceid, $eventtype) {
        $event = new \stdClass();
        $event->name = 'Calendar event';
        $event->modulename  = 'folder';
        $event->courseid = $courseid;
        $event->instance = $instanceid;
        $event->type = CALENDAR_EVENT_TYPE_ACTION;
        $event->eventtype = $eventtype;
        $event->timestart = time();

        return \calendar_event::create($event);
    }

    /**
     * Test Get recent mod activity method.
     * @covers ::folder_get_recent_mod_activity
     * @dataProvider folder_get_recent_mod_activity_provider
     *
     * @param int $forcedownload The forcedownload option.
     * @param bool $hascapability if the user has the mod/folder:view capability
     * @param int $count The expected recent activities entries.
     */
    public function test_folder_get_recent_mod_activity(int $forcedownload, bool $hascapability, int $count): void {
        global $USER, $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();

        // Add files to draft area.
        $filesitem = file_get_unused_draft_itemid();
        $usercontext = context_user::instance($USER->id);
        $filerecord = [
            'component' => 'user',
            'filearea' => 'draft',
            'contextid' => $usercontext->id,
            'itemid' => $filesitem,
            'filename' => 'file1.txt', 'filepath' => '/',
        ];
        $fs = get_file_storage();
        $fs->create_file_from_string($filerecord, 'First test file contents');
        // And a second file.
        $filerecord['filename'] = 'file2.txt';
        $fs->create_file_from_string($filerecord, 'Second test file contents');

        // Create the activity.
        $module = $this->getDataGenerator()->create_module(
            'folder',
            ['course' => $course->id, 'forcedownload' => $forcedownload, 'files' => $filesitem]
        );

        // Get some additional data.
        $cm = get_coursemodule_from_instance('folder', $module->id);
        $context = context_module::instance($cm->id);

        // Add user with the specific capability.
        $user = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user->id, $course->id, 'editingteacher');
        if (!$hascapability) {
            // The recent activiy uses "folder:view" capability which is allowed by default.
            $role = $DB->get_record('role', ['shortname' => 'editingteacher'], '*', MUST_EXIST);
            assign_capability('mod/folder:view', CAP_PROHIBIT, $role->id, $context->id, true);
        }
        $this->setUser($user);

        // Get the recent activity.
        $index = 1;
        $activities = [];
        folder_get_recent_mod_activity($activities, $index, time() - HOURSECS, $course->id, $cm->id);

        // Check recent activity.
        $this->assertCount($count, $activities);
        foreach ($activities as $index => $activity) {
            $this->assertEquals('folder', $activity->type);
            $content = $activity->content;
            $this->assertEquals("file{$index}.txt", $content->filename);
            $urlparams = $content->url->params();
            if ($forcedownload) {
                $this->assertEquals(1, $urlparams['forcedownload']);
            } else {
                $this->assertArrayNotHasKey('forcedownload', $urlparams);
            }
        }
    }

    /**
     * Data provider for test_folder_get_recent_mod_activity().
     *
     * @return array
     */
    public static function folder_get_recent_mod_activity_provider(): array {
        return [
            'Teacher with force download' => [
                'forcedownload' => 1,
                'hascapability' => true,
                'count' => 2,
            ],
            'Teacher with no force download' => [
                'forcedownload' => 0,
                'hascapability' => true,
                'count' => 2,
            ],
            'Invalid user with force download' => [
                'forcedownload' => 1,
                'hascapability' => false,
                'count' => 0,
            ],
            'Invalid user with no force download' => [
                'forcedownload' => 0,
                'hascapability' => false,
                'count' => 0,
            ],
        ];
    }
}
