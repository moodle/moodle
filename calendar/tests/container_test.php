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
 * Event container tests.
 *
 * @package    core_calendar
 * @copyright  2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/calendar/lib.php');

use core_calendar\local\event\entities\action_event;
use core_calendar\local\event\entities\event;
use core_calendar\local\event\entities\event_interface;
use core_calendar\local\event\factories\event_factory;
use core_calendar\local\event\factories\event_factory_interface;
use core_calendar\local\event\mappers\event_mapper;
use core_calendar\local\event\mappers\event_mapper_interface;

/**
 * Core container testcase.
 *
 * @copyright 2017 Cameron Ball <cameron@cameron1729.xyz>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_calendar_container_testcase extends advanced_testcase {

    /**
     * Test setup.
     */
    public function setUp() {
        $this->resetAfterTest();
        $this->setAdminUser();
    }

    /**
     * Test getting the event factory.
     */
    public function test_get_event_factory() {
        $factory = \core_calendar\local\event\container::get_event_factory();

        // Test that the container is returning the right type.
        $this->assertInstanceOf(event_factory_interface::class, $factory);
        // Test that the container is returning the right implementation.
        $this->assertInstanceOf(event_factory::class, $factory);

        // Test that getting the factory a second time returns the same instance.
        $factory2 = \core_calendar\local\event\container::get_event_factory();
        $this->assertTrue($factory === $factory2);
    }

    /**
     * Test that the event factory correctly creates instances of events.
     *
     * @dataProvider get_event_factory_testcases()
     * @param \stdClass $dbrow Row from the "database".
     */
    public function test_event_factory_create_instance($dbrow) {
        $legacyevent = $this->create_event($dbrow);
        $factory = \core_calendar\local\event\container::get_event_factory();
        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $moduleinstance = $generator->create_instance(['course' => $course->id]);

        // Set some of the fake dbrow properties to match real data in the DB
        // this is necessary as the factory hides things that modinfo doesn't
        // know about.
        $dbrow->id = $legacyevent->id;
        $dbrow->courseid = $course->id;
        $dbrow->instance = $moduleinstance->id;
        $dbrow->modulename = 'assign';
        $event = $factory->create_instance($dbrow);

        // Test that the factory is returning the right type.
        $this->assertInstanceOf(event_interface::class, $event);
        // Test that the factory is returning the right implementation.
        $this->assertTrue($event instanceof event || $event instanceof action_event);

        // Test that the event created has the correct properties.
        $this->assertEquals($legacyevent->id, $event->get_id());
        $this->assertEquals($dbrow->description, $event->get_description()->get_value());
        $this->assertEquals($dbrow->format, $event->get_description()->get_format());
        $this->assertEquals($dbrow->courseid, $event->get_course()->get('id'));

        if ($dbrow->groupid == 0) {
            $this->assertNull($event->get_group());
        } else {
            $this->assertEquals($dbrow->groupid, $event->get_group()->get('id'));
        }

        $this->assertEquals($dbrow->userid, $event->get_user()->get('id'));
        $this->assertEquals(null, $event->get_repeats());
        $this->assertEquals($dbrow->modulename, $event->get_course_module()->get('modname'));
        $this->assertEquals($dbrow->instance, $event->get_course_module()->get('instance'));
        $this->assertEquals($dbrow->timestart, $event->get_times()->get_start_time()->getTimestamp());
        $this->assertEquals($dbrow->timemodified, $event->get_times()->get_modified_time()->getTimestamp());
        $this->assertEquals($dbrow->timesort, $event->get_times()->get_sort_time()->getTimestamp());

        if ($dbrow->visible == 1) {
            $this->assertTrue($event->is_visible());
        } else {
            $this->assertFalse($event->is_visible());
        }

        if (!$dbrow->subscriptionid) {
            $this->assertNull($event->get_subscription());
        } else {
            $this->assertEquals($event->get_subscription()->get('id'));
        }
    }

    /**
     * Test that the event factory deals with invisible modules properly as admin.
     *
     * @dataProvider get_event_factory_testcases()
     * @param \stdClass $dbrow Row from the "database".
     */
    public function test_event_factory_when_module_visibility_is_toggled_as_admin($dbrow) {
        $legacyevent = $this->create_event($dbrow);
        $factory = \core_calendar\local\event\container::get_event_factory();
        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $moduleinstance = $generator->create_instance(['course' => $course->id]);

        $dbrow->id = $legacyevent->id;
        $dbrow->courseid = $course->id;
        $dbrow->instance = $moduleinstance->id;
        $dbrow->modulename = 'assign';

        set_coursemodule_visible($moduleinstance->cmid, 0);

        $event = $factory->create_instance($dbrow);

        // Test that the factory is returning an event as the admin can see hidden course modules.
        $this->assertInstanceOf(event_interface::class, $event);
    }

    /**
     * Test that the event factory deals with invisible modules properly as a guest.
     *
     * @dataProvider get_event_factory_testcases()
     * @param \stdClass $dbrow Row from the "database".
     */
    public function test_event_factory_when_module_visibility_is_toggled_as_guest($dbrow) {
        $legacyevent = $this->create_event($dbrow);
        $factory = \core_calendar\local\event\container::get_event_factory();
        $course = $this->getDataGenerator()->create_course();
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $moduleinstance = $generator->create_instance(['course' => $course->id]);

        $dbrow->id = $legacyevent->id;
        $dbrow->courseid = $course->id;
        $dbrow->instance = $moduleinstance->id;
        $dbrow->modulename = 'assign';

        set_coursemodule_visible($moduleinstance->cmid, 0);

        // Set to a user who can not view hidden course modules.
        $this->setGuestUser();

        $event = $factory->create_instance($dbrow);

        // Module is invisible to guest users so this should return null.
        $this->assertNull($event);
    }

    /**
     * Test that the event factory deals with invisible courses as an admin.
     *
     * @dataProvider get_event_factory_testcases()
     * @param \stdClass $dbrow Row from the "database".
     */
    public function test_event_factory_when_course_visibility_is_toggled_as_admin($dbrow) {
        $legacyevent = $this->create_event($dbrow);
        $factory = \core_calendar\local\event\container::get_event_factory();

        // Create a hidden course with an assignment.
        $course = $this->getDataGenerator()->create_course(['visible' => 0]);
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $moduleinstance = $generator->create_instance(['course' => $course->id]);

        $dbrow->id = $legacyevent->id;
        $dbrow->courseid = $course->id;
        $dbrow->instance = $moduleinstance->id;
        $dbrow->modulename = 'assign';
        $event = $factory->create_instance($dbrow);

        // Module is still visible to admins even if the course is invisible.
        $this->assertInstanceOf(event_interface::class, $event);
    }

    /**
     * Test that the event factory deals with invisible courses as a student.
     *
     * @dataProvider get_event_factory_testcases()
     * @param \stdClass $dbrow Row from the "database".
     */
    public function test_event_factory_when_course_visibility_is_toggled_as_student($dbrow) {
        $legacyevent = $this->create_event($dbrow);
        $factory = \core_calendar\local\event\container::get_event_factory();

        // Create a hidden course with an assignment.
        $course = $this->getDataGenerator()->create_course(['visible' => 0]);
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $moduleinstance = $generator->create_instance(['course' => $course->id]);

        // Enrol a student into this course.
        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id);

        // Set the user to the student.
        $this->setUser($student);

        $dbrow->id = $legacyevent->id;
        $dbrow->courseid = $course->id;
        $dbrow->instance = $moduleinstance->id;
        $dbrow->modulename = 'assign';
        $event = $factory->create_instance($dbrow);

        // Module is invisible to students if the course is invisible.
        $this->assertNull($event);
    }

    /**
     * Test that the event factory deals with invisible categorys as an admin.
     */
    public function test_event_factory_when_category_visibility_is_toggled_as_admin() {
        // Create a hidden category.
        $category = $this->getDataGenerator()->create_category(['visible' => 0]);

        $eventdata = [
                'categoryid' => $category->id,
                'eventtype' => 'category',
            ];
        $legacyevent = $this->create_event($eventdata);

        $dbrow = $this->get_dbrow_from_skeleton((object) $eventdata);
        $dbrow->id = $legacyevent->id;

        $factory = \core_calendar\local\event\container::get_event_factory();
        $event = $factory->create_instance($dbrow);

        // Module is still visible to admins even if the category is invisible.
        $this->assertInstanceOf(event_interface::class, $event);
    }

    /**
     * Test that the event factory deals with invisible categorys as an user.
     */
    public function test_event_factory_when_category_visibility_is_toggled_as_user() {
        // Create a hidden category.
        $category = $this->getDataGenerator()->create_category(['visible' => 0]);

        $eventdata = [
                'categoryid' => $category->id,
                'eventtype' => 'category',
            ];
        $legacyevent = $this->create_event($eventdata);

        $dbrow = $this->get_dbrow_from_skeleton((object) $eventdata);
        $dbrow->id = $legacyevent->id;

        // Use a standard user.
        $user = $this->getDataGenerator()->create_user();

        // Set the user to the student.
        $this->setUser($user);

        $factory = \core_calendar\local\event\container::get_event_factory();
        $event = $factory->create_instance($dbrow);

        // Module is invisible to non-privileged users.
        $this->assertNull($event);
    }

    /**
     * Test that the event factory deals with invisible categorys as an guest.
     */
    public function test_event_factory_when_category_visibility_is_toggled_as_guest() {
        // Create a hidden category.
        $category = $this->getDataGenerator()->create_category(['visible' => 0]);

        $eventdata = [
                'categoryid' => $category->id,
                'eventtype' => 'category',
            ];
        $legacyevent = $this->create_event($eventdata);

        $dbrow = $this->get_dbrow_from_skeleton((object) $eventdata);
        $dbrow->id = $legacyevent->id;

        // Set the user to the student.
        $this->setGuestUser();

        $factory = \core_calendar\local\event\container::get_event_factory();
        $event = $factory->create_instance($dbrow);

        // Module is invisible to guests.
        $this->assertNull($event);
    }

    /**
     * Test that the event factory deals with completion related events properly.
     */
    public function test_event_factory_with_completion_related_event() {
        global $CFG;

        $CFG->enablecompletion = true;

        // Create the course we will be using.
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));

        // Add the assignment.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $assign = $generator->create_instance(array('course' => $course->id), array('completion' => 1));

        // Create a completion event.
        $event = new \stdClass();
        $event->name = 'An event';
        $event->description = 'Event description';
        $event->format = FORMAT_HTML;
        $event->eventtype = \core_completion\api::COMPLETION_EVENT_TYPE_DATE_COMPLETION_EXPECTED;
        $event->userid = 1;
        $event->modulename = 'assign';
        $event->instance = $assign->id;
        $event->categoryid = 0;
        $event->courseid = $course->id;
        $event->groupid = 0;
        $event->timestart = time();
        $event->timesort = time();
        $event->timemodified = time();
        $event->timeduration = 0;
        $event->subscriptionid = null;
        $event->repeatid = 0;
        $legacyevent = $this->create_event($event);

        // Update the id of the event that was created.
        $event->id = $legacyevent->id;

        // Create the factory we are going to be testing the behaviour of.
        $factory = \core_calendar\local\event\container::get_event_factory();

        // Check that we get the correct instance.
        $this->assertInstanceOf(event_interface::class, $factory->create_instance($event));

        // Now, disable completion.
        $CFG->enablecompletion = false;

        // The result should now be null since we have disabled completion.
        $this->assertNull($factory->create_instance($event));
    }

    /**
     * Test that the event factory only returns an event if the logged in user
     * is enrolled in the course.
     */
    public function test_event_factory_unenrolled_user() {
        $user = $this->getDataGenerator()->create_user();
        // Create the course we will be using.
        $course = $this->getDataGenerator()->create_course();

        // Add the assignment.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');
        $lesson = $generator->create_instance(array('course' => $course->id));

        // Create a user override event for the lesson.
        $event = new \stdClass();
        $event->name = 'An event';
        $event->description = 'Event description';
        $event->format = FORMAT_HTML;
        $event->eventtype = 'close';
        $event->userid = $user->id;
        $event->modulename = 'lesson';
        $event->instance = $lesson->id;
        $event->categoryid = 0;
        $event->courseid = $course->id;
        $event->groupid = 0;
        $event->timestart = time();
        $event->timesort = time();
        $event->timemodified = time();
        $event->timeduration = 0;
        $event->subscriptionid = null;
        $event->repeatid = 0;
        $legacyevent = $this->create_event($event);

        // Update the id of the event that was created.
        $event->id = $legacyevent->id;

        // Set the logged in user to the one we created.
        $this->setUser($user);

        // Create the factory we are going to be testing the behaviour of.
        $factory = \core_calendar\local\event\container::get_event_factory();

        // The result should be null since the user is not enrolled in the
        // course the event is for.
        $this->assertNull($factory->create_instance($event));

        // Now enrol the user in the course.
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        // Check that we get the correct instance.
        $this->assertInstanceOf(event_interface::class, $factory->create_instance($event));
    }

    /**
     * Test that when course module is deleted all events are also deleted.
     */
    public function test_delete_module_delete_events() {
        global $DB;
        $user = $this->getDataGenerator()->create_user();
        // Create the course we will be using.
        $course = $this->getDataGenerator()->create_course();
        $group = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        foreach (core_component::get_plugin_list('mod') as $modname => $unused) {
            try {
                $generator = $this->getDataGenerator()->get_plugin_generator('mod_'.$modname);
            } catch (coding_exception $e) {
                // Module generator is not implemented.
                continue;
            }
            $module = $generator->create_instance(['course' => $course->id]);

            // Create bunch of events of different type (user override, group override, module event).
            $this->create_event(['userid' => $user->id, 'modulename' => $modname, 'instance' => $module->id]);
            $this->create_event(['groupid' => $group->id, 'modulename' => $modname, 'instance' => $module->id]);
            $this->create_event(['modulename' => $modname, 'instance' => $module->id]);
            $this->create_event(['modulename' => $modname, 'instance' => $module->id, 'courseid' => $course->id]);

            // Delete module and make sure all events are deleted.
            course_delete_module($module->cmid);
            $this->assertEmpty($DB->get_record('event', ['modulename' => $modname, 'instance' => $module->id]));
        }
    }

    /**
     * Test getting the event mapper.
     */
    public function test_get_event_mapper() {
        $mapper = \core_calendar\local\event\container::get_event_mapper();

        $this->assertInstanceOf(event_mapper_interface::class, $mapper);
        $this->assertInstanceOf(event_mapper::class, $mapper);

        $mapper2 = \core_calendar\local\event\container::get_event_mapper();

        $this->assertTrue($mapper === $mapper2);
    }

    /**
     * Test cases for the get event factory test.
     */
    public function get_event_factory_testcases() {
        return [
            'Data set 1' => [
                'dbrow' => (object)[
                    'name' => 'Test event',
                    'description' => 'Hello',
                    'format' => 1,
                    'categoryid' => 0,
                    'courseid' => 1,
                    'groupid' => 0,
                    'userid' => 1,
                    'repeatid' => 0,
                    'modulename' => 'assign',
                    'instance' => 2,
                    'eventtype' => 'due',
                    'timestart' => 1486396800,
                    'timeduration' => 0,
                    'timesort' => 1486396800,
                    'visible' => 1,
                    'timemodified' => 1485793098,
                    'subscriptionid' => null
                ]
            ],

            'Data set 2' => [
                'dbrow' => (object)[
                    'name' => 'Test event',
                    'description' => 'Hello',
                    'format' => 1,
                    'categoryid' => 0,
                    'courseid' => 1,
                    'groupid' => 1,
                    'userid' => 1,
                    'repeatid' => 0,
                    'modulename' => 'assign',
                    'instance' => 2,
                    'eventtype' => 'due',
                    'timestart' => 1486396800,
                    'timeduration' => 0,
                    'timesort' => 1486396800,
                    'visible' => 1,
                    'timemodified' => 1485793098,
                    'subscriptionid' => null
                ]
            ]
        ];
    }

    /**
     * Helper function to create calendar events using the old code.
     *
     * @param array $properties A list of calendar event properties to set
     * @return calendar_event|bool
     */
    protected function create_event($properties = []) {
        $record = new \stdClass();
        $record->name = 'event name';
        $record->eventtype = 'global';
        $record->timestart = time();
        $record->timeduration = 0;
        $record->timesort = 0;
        $record->type = 1;
        $record->courseid = 0;
        $record->categoryid = 0;

        foreach ($properties as $name => $value) {
            $record->$name = $value;
        }

        $event = new calendar_event($record);
        return $event->create($record, false);
    }

    /**
     * Pad out a basic DB row with basic information.
     *
     * @param   \stdClass   $skeleton the current skeleton
     * @return  \stdClass
     */
    protected function get_dbrow_from_skeleton($skeleton) {
        $dbrow = (object) [
            'name' => 'Name',
            'description' => 'Description',
            'format' => 1,
            'categoryid' => 0,
            'courseid' => 0,
            'groupid' => 0,
            'userid' => 0,
            'repeatid' => 0,
            'modulename' => '',
            'instance' => 0,
            'eventtype' => 'user',
            'timestart' => 1486396800,
            'timeduration' => 0,
            'timesort' => 1486396800,
            'visible' => 1,
            'timemodified' => 1485793098,
            'subscriptionid' => null
        ];

        foreach ((array) $skeleton as $key => $value) {
            $dbrow->$key = $value;
        }

        return $dbrow;
    }
}
