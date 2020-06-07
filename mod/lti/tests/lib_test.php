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
 * Unit tests for mod_lti lib
 *
 * @package    mod_lti
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for mod_lti lib
 *
 * @package    mod_lti
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */
class mod_lti_lib_testcase extends advanced_testcase {

    /**
     * Prepares things before this test case is initialised
     * @return void
     */
    public static function setUpBeforeClass() {
        global $CFG;
        require_once($CFG->dirroot . '/mod/lti/lib.php');
    }

    /**
     * Test lti_view
     * @return void
     */
    public function test_lti_view() {
        global $CFG;

        $CFG->enablecompletion = 1;
        $this->resetAfterTest();

        $this->setAdminUser();
        // Setup test data.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $lti = $this->getDataGenerator()->create_module('lti', array('course' => $course->id),
                                                            array('completion' => 2, 'completionview' => 1));
        $context = context_module::instance($lti->cmid);
        $cm = get_coursemodule_from_instance('lti', $lti->id);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();

        lti_view($lti, $course, $cm, $context);

        $events = $sink->get_events();
        // 2 additional events thanks to completion.
        $this->assertCount(3, $events);
        $event = array_shift($events);

        // Checking that the event contains the expected values.
        $this->assertInstanceOf('\mod_lti\event\course_module_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $moodleurl = new \moodle_url('/mod/lti/view.php', array('id' => $cm->id));
        $this->assertEquals($moodleurl, $event->get_url());
        $this->assertEventContextNotUsed($event);
        $this->assertNotEmpty($event->get_name());

        // Check completion status.
        $completion = new completion_info($course);
        $completiondata = $completion->get_data($cm);
        $this->assertEquals(1, $completiondata->completionstate);

    }

    /**
     * Test deleting LTI instance.
     */
    public function test_lti_delete_instance() {
        $this->resetAfterTest();

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course(array());
        $lti = $this->getDataGenerator()->create_module('lti', array('course' => $course->id));
        $cm = get_coursemodule_from_instance('lti', $lti->id);

        // Must not throw notices.
        course_delete_module($cm->id);
    }

    public function test_lti_core_calendar_provide_event_action() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $lti = $this->getDataGenerator()->create_module('lti', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $lti->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_lti_core_calendar_provide_event_action($event, $factory);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('view'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_lti_core_calendar_provide_event_action_as_non_user() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $lti = $this->getDataGenerator()->create_module('lti', array('course' => $course->id));

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $lti->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Now, log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_lti_core_calendar_provide_event_action($event, $factory);

        // Confirm the event is not shown at all.
        $this->assertNull($actionevent);
    }

    public function test_lti_core_calendar_provide_event_action_for_user() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        // Create the activity.
        $course = $this->getDataGenerator()->create_course();
        $lti = $this->getDataGenerator()->create_module('lti', array('course' => $course->id));

        // Enrol a student in the course.
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $lti->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Now, log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for the student.
        $actionevent = mod_lti_core_calendar_provide_event_action($event, $factory, $student->id);

        // Confirm the event was decorated.
        $this->assertInstanceOf('\core_calendar\local\event\value_objects\action', $actionevent);
        $this->assertEquals(get_string('view'), $actionevent->get_name());
        $this->assertInstanceOf('moodle_url', $actionevent->get_url());
        $this->assertEquals(1, $actionevent->get_item_count());
        $this->assertTrue($actionevent->is_actionable());
    }

    public function test_lti_core_calendar_provide_event_action_already_completed() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $CFG->enablecompletion = 1;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $lti = $this->getDataGenerator()->create_module('lti', array('course' => $course->id),
            array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Get some additional data.
        $cm = get_coursemodule_from_instance('lti', $lti->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $lti->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed.
        $completion = new completion_info($course);
        $completion->set_module_viewed($cm);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_lti_core_calendar_provide_event_action($event, $factory);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    public function test_lti_core_calendar_provide_event_action_already_completed_as_non_user() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $CFG->enablecompletion = 1;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $lti = $this->getDataGenerator()->create_module('lti', array('course' => $course->id),
            array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Get some additional data.
        $cm = get_coursemodule_from_instance('lti', $lti->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $lti->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed.
        $completion = new completion_info($course);
        $completion->set_module_viewed($cm);

        // Now, log out.
        $CFG->forcelogin = true; // We don't want to be logged in as guest, as guest users might still have some capabilities.
        $this->setUser();

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event.
        $actionevent = mod_lti_core_calendar_provide_event_action($event, $factory);

        // Ensure result was null.
        $this->assertNull($actionevent);
    }

    public function test_lti_core_calendar_provide_event_action_already_completed_for_user() {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();

        $CFG->enablecompletion = 1;

        // Create the activity.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $lti = $this->getDataGenerator()->create_module('lti', array('course' => $course->id),
            array('completion' => 2, 'completionview' => 1, 'completionexpected' => time() + DAYSECS));

        // Enrol 2 students in the course.
        $student1 = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $student2 = $this->getDataGenerator()->create_and_enrol($course, 'student');

        // Get some additional data.
        $cm = get_coursemodule_from_instance('lti', $lti->id);

        // Create a calendar event.
        $event = $this->create_action_event($course->id, $lti->id,
            \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED);

        // Mark the activity as completed for $student1.
        $completion = new completion_info($course);
        $completion->set_module_viewed($cm, $student1->id);

        // Now, log in as $student2.
        $this->setUser($student2);

        // Create an action factory.
        $factory = new \core_calendar\action_factory();

        // Decorate action event for $student1.
        $actionevent = mod_lti_core_calendar_provide_event_action($event, $factory, $student1->id);

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
        $event = new stdClass();
        $event->name = 'Calendar event';
        $event->modulename  = 'lti';
        $event->courseid = $courseid;
        $event->instance = $instanceid;
        $event->type = CALENDAR_EVENT_TYPE_ACTION;
        $event->eventtype = $eventtype;
        $event->timestart = time();

        return calendar_event::create($event);
    }

    /**
     * Test verifying the output of the lti_get_course_content_items and lti_get_all_content_items callbacks.
     */
    public function test_content_item_callbacks() {
        $this->resetAfterTest();
        global $DB, $CFG;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');

        $admin = get_admin();
        $time = time();
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $course2 = $this->getDataGenerator()->create_course();
        $teacher2 = $this->getDataGenerator()->create_and_enrol($course2, 'editingteacher');

        // Create some preconfigured tools.
        $sitetoolrecord = (object) [
            'name' => 'Site level tool which is available in the activity chooser',
            'baseurl' => 'http://example.com',
            'createdby' => $admin->id,
            'course' => SITEID,
            'ltiversion' => 'LTI-1p0',
            'timecreated' => $time,
            'timemodified' => $time,
            'state' => LTI_TOOL_STATE_CONFIGURED,
            'coursevisible' => LTI_COURSEVISIBLE_ACTIVITYCHOOSER
        ];
        $sitetoolrecordnonchooser = (object) [
            'name' => 'Site level tool which is NOT available in the course activity chooser',
            'baseurl' => 'http://example2.com',
            'createdby' => $admin->id,
            'course' => SITEID,
            'ltiversion' => 'LTI-1p0',
            'timecreated' => $time,
            'timemodified' => $time,
            'state' => LTI_TOOL_STATE_CONFIGURED,
            'coursevisible' => LTI_COURSEVISIBLE_PRECONFIGURED
        ];
        $course1toolrecord = (object) [
            'name' => 'Course created tool which is available in the activity chooser',
            'baseurl' => 'http://example3.com',
            'createdby' => $teacher->id,
            'course' => $course->id,
            'ltiversion' => 'LTI-1p0',
            'timecreated' => $time,
            'timemodified' => $time,
            'state' => LTI_TOOL_STATE_CONFIGURED,
            'coursevisible' => LTI_COURSEVISIBLE_ACTIVITYCHOOSER
        ];
        $course2toolrecord = (object) [
            'name' => 'Course created tool which is available in the activity chooser',
            'baseurl' => 'http://example4.com',
            'createdby' => $teacher2->id,
            'course' => $course2->id,
            'ltiversion' => 'LTI-1p0',
            'timecreated' => $time,
            'timemodified' => $time,
            'state' => LTI_TOOL_STATE_CONFIGURED,
            'coursevisible' => LTI_COURSEVISIBLE_ACTIVITYCHOOSER
        ];
        $tool1id = $DB->insert_record('lti_types', $sitetoolrecord);
        $tool2id = $DB->insert_record('lti_types', $sitetoolrecordnonchooser);
        $tool3id = $DB->insert_record('lti_types', $course1toolrecord);
        $tool4id = $DB->insert_record('lti_types', $course2toolrecord);
        $sitetoolrecord->id = $tool1id;
        $sitetoolrecordnonchooser->id = $tool2id;
        $course1toolrecord->id = $tool3id;
        $course2toolrecord->id = $tool4id;

        $defaultmodulecontentitem = new \core_course\local\entity\content_item(
            '1',
            'default module content item',
            new \core_course\local\entity\string_title('Content item title'),
            new moodle_url(''),
            'icon',
            'Description of the module',
            MOD_ARCHETYPE_OTHER,
            'mod_lti'
        );

        // The lti_get_lti_types_by_course method (used by the callbacks) assumes the global user.
        $this->setUser($teacher);

        // Teacher in course1 should be able to see the default module item ('external tool'),
        // the site preconfigured tool and the tool created in course1.
        $courseitems = lti_get_course_content_items($defaultmodulecontentitem, $teacher, $course);
        $this->assertCount(3, $courseitems);
        $ids = [];
        foreach ($courseitems as $item) {
            $ids[] = $item->get_id();
        }
        $this->assertContains(1, $ids);
        $this->assertContains($sitetoolrecord->id + 1, $ids);
        $this->assertContains($course1toolrecord->id + 1, $ids);
        $this->assertNotContains($sitetoolrecordnonchooser->id + 1, $ids);

        // The content items for teacher2 in course2 include the default module content item ('external tool'),
        // the site preconfigured tool and the tool created in course2.
        $this->setUser($teacher2);
        $course2items = lti_get_course_content_items($defaultmodulecontentitem, $teacher2, $course2);
        $this->assertCount(3, $course2items);
        $ids = [];
        foreach ($course2items as $item) {
            $ids[] = $item->get_id();
        }
        $this->assertContains(1, $ids);
        $this->assertContains($sitetoolrecord->id + 1, $ids);
        $this->assertContains($course2toolrecord->id + 1, $ids);
        $this->assertNotContains($sitetoolrecordnonchooser->id + 1, $ids);

        // When fetching all content items, we expect to see all items available in activity choosers (in any course),
        // plus the default module content item ('external tool').
        $this->setAdminUser();
        $allitems = mod_lti_get_all_content_items($defaultmodulecontentitem);
        $this->assertCount(4, $allitems);
        $ids = [];
        foreach ($allitems as $item) {
            $ids[] = $item->get_id();
        }
        $this->assertContains(1, $ids);
        $this->assertContains($sitetoolrecord->id + 1, $ids);
        $this->assertContains($course1toolrecord->id + 1, $ids);
        $this->assertContains($course2toolrecord->id + 1, $ids);
        $this->assertNotContains($sitetoolrecordnonchooser->id + 1, $ids);
    }
}
