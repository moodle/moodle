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
 * Privacy tests for core_calendar.
 *
 * @package    core_calendar
 * @category   test
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->dirroot . '/calendar/lib.php');
require_once($CFG->dirroot . '/calendar/tests/externallib_test.php');

use \core_calendar\privacy\provider;
use \core_privacy\local\metadata\collection;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\writer;
use \core_privacy\tests\provider_testcase;
use \core_privacy\local\request\approved_userlist;

/**
 * Unit tests for calendar/classes/privacy/provider
 *
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_calendar_privacy_testcase extends provider_testcase {

    /**
     * Overriding setUp() function to always reset after tests.
     */
    public function setUp() {
        $this->resetAfterTest(true);
    }

    /**
     * Test for provider::get_contexts_for_userid().
     *
     * @throws coding_exception
     */
    public function test_get_contexts_for_userid() {
        // Create test user to create Calendar Events and Subscriptions.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Create a Category and Courses to assign Calendar Events and Subscriptions.
        $category = $this->getDataGenerator()->create_category();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();
        $grouprecord = (object)[
            'courseid' => $course3->id,
            'name' => 'test_group'
        ];
        $course3group = $this->getDataGenerator()->create_group($grouprecord);

        // Get contexts.
        $usercontext = context_user::instance($user->id);
        $categorycontext = context_coursecat::instance($category->id);
        $course1context = context_course::instance($course1->id);
        $course2context = context_course::instance($course2->id);
        $course3context = context_course::instance($course3->id);

        // Add Category Calendar Events for Category.
        $this->create_test_standard_calendar_event('category', 0, time(), '', $category->id);
        $this->create_test_standard_calendar_event('category', 0, time(), '', $category->id);

        // Add User Calendar Events for User.
        $this->create_test_standard_calendar_event('user', $user->id, time(), '');
        $this->create_test_standard_calendar_event('user', $user->id, time(), '', 0, $course1->id);
        $this->create_test_standard_calendar_event('user', $user->id, time(), '', 0, $course2->id);

        // Add a Course Calendar Event for Course 1.
        $this->create_test_standard_calendar_event('course', 0, time(), '', 0, $course1->id);

        // Add a Course Assignment Action Calendar Event for Course 2.
        $assigngenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $params['course'] = $course2->id;
        $params['assignsubmission_onlinetext_enabled'] = 1;
        $assign1 = $assigngenerator->create_instance($params);
        $assign1cm = get_coursemodule_from_instance('assign', $assign1->id);
        $assign1context = context_module::instance($assign1cm->id);
        $this->create_test_action_calendar_event('duedate', $course2->id, $assign1->id, 'assign', 0, time());
        $this->create_test_action_calendar_event('gradingduedate', $course2->id, $assign1->id, 'assign', 0, time());

        // Add a due date event similar to a user override in another assign instance.
        $assign2 = $assigngenerator->create_instance($params);
        $assign2cm = get_coursemodule_from_instance('assign', $assign2->id);
        $assign2context = context_module::instance($assign2cm->id);
        $this->create_test_action_calendar_event('duedate', $course2->id, $assign2->id, 'assign', $user->id, time());
        $this->create_test_action_calendar_event('gradingduedate', $course2->id, $assign2->id, 'assign', $user->id, time());

        // Add a Calendar Subscription and Group Calendar Event to Course 3.
        $this->create_test_calendar_subscription('course', 'https://calendar.google.com/', 0, 0, $course3->id);
        $this->create_test_standard_calendar_event('group', 0, time(), '', 0, $course3->id, $course3group->id);

        // The user will be in these contexts.
        $usercontextids = [
            $usercontext->id,
            $assign2context->id,
        ];
        // Retrieve the user's context ids.
        $contextids = provider::get_contexts_for_userid($user->id);

        // Check the user context list and retrieved user context lists contains the same number of records.
        $this->assertCount(count($usercontextids), $contextids->get_contextids());
        // There should be no difference between the contexts.
        $this->assertEmpty(array_diff($usercontextids, $contextids->get_contextids()));
        // Check that the module context for the assignment without user override event is not included.
        $this->assertNotContains($assign1context->id, $contextids->get_contextids());
    }

    /**
     * Test for provider::export_user_data().
     *
     * @throws coding_exception
     */
    public function test_export_user_data() {
        global $DB;

        // Create test user to create Calendar Events and Subscriptions with.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Create a Category to test creating a Category Calendar Event.
        $category = $this->getDataGenerator()->create_category();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();
        $grouprecord = (object)[
            'courseid' => $course3->id,
            'name' => 'test_group'
        ];
        $course3group = $this->getDataGenerator()->create_group($grouprecord);

        // Add User Calendar Events for User.
        $event1 = $this->create_test_standard_calendar_event('user', $user->id, time(), '');

        // Add Category Calendar Events for Category.
        $event2 = $this->create_test_standard_calendar_event('category', 0, time(), '', $category->id);

        // Add two Course Calendar Event for Course 1 and set the same time (1 day a head).
        $time = strtotime('+1 day', time());
        $event3 = $this->create_test_standard_calendar_event('course', 0, $time, 'ABC', 0, $course1->id);
        $event4 = $this->create_test_standard_calendar_event('course', 0, $time, 'DEF', 0, $course1->id);

        // Add a Course Assignment Action Calendar Event for Course 2.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $params['course'] = $course2->id;
        $params['assignsubmission_onlinetext_enabled'] = 1;
        $instance = $generator->create_instance($params);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $modulecontext = context_module::instance($cm->id);
        $assign = new assign($modulecontext, $cm, $course2);
        $event5 = $this->create_test_action_calendar_event('duedate', $course2->id, $instance->id, 'assign', $user->id, time());

        // Add a Calendar Subscription and Group Calendar Event to Course 3.
        $subscription1 = $this->create_test_calendar_subscription('course', 'https://calendar.google.com/', $user->id, 0, $course3->id);
        $event6 = $this->create_test_standard_calendar_event('group', 0, time(), '', 0, $course3->id, $course3group->id);

        // Retrieve the user's context ids.
        $contextlist = provider::get_contexts_for_userid($user->id);
        $approvedcontextlist = new approved_contextlist($user, 'core_calendar', $contextlist->get_contextids());

        // Retrieve Calendar Event and Subscriptions data only for this user.
        provider::export_user_data($approvedcontextlist);

        foreach ($contextlist as $context) {
            $writer = writer::with_context($context);
            $this->assertTrue($writer->has_any_data());

            // Test event 1 that was created for the test User.
            if ($context->instanceid == $user->id && $context->contextlevel == CONTEXT_USER) {
                // Test the content contains Calendar Event user data.
                $subcontexts = [
                    get_string('calendar', 'calendar'),
                    get_string('events', 'calendar'),
                    date('c', $event1->timestart)
                ];
                $name = "user-event";
                $data = $writer->get_related_data($subcontexts, $name);
                $this->assertEquals('Standard Calendar Event user', $data->name);
            }

            // Test event 2 that was created for the test Category.
            if ($context->instanceid == $category->id && $context->contextlevel == CONTEXT_COURSECAT) {
                // Test the content contains Calendar Event category data.
                $subcontexts = [
                    get_string('calendar', 'calendar'),
                    get_string('events', 'calendar'),
                    date('c', $event2->timestart)
                ];
                $name = "category-event";
                $data = $writer->get_related_data($subcontexts, $name);
                $this->assertEquals('Standard Calendar Event category', $data->name);
            }

            // Test events 3, 4, and 5 that were created for the test Course 1.
            if ($context->instanceid == $course1->id && $context->contextlevel == CONTEXT_COURSE) {
                // Test the content contains Calendar Event course data set with the same time, and the exported files are uniquely identified.
                $subcontext1 = [
                    get_string('calendar', 'calendar'),
                    get_string('events', 'calendar'),
                    date('c', $event3->timestart)
                ];
                $name1 = "course-event-1";
                $data1 = $writer->get_related_data($subcontext1, $name1);
                $this->assertEquals('Standard Calendar Event course -- ABC', $data1->name);

                $subcontext2 = [
                    get_string('calendar', 'calendar'),
                    get_string('events', 'calendar'),
                    date('c', $event4->timestart)
                ];
                $name2 = "course-event-2";
                $data2 = $writer->get_related_data($subcontext2, $name2);
                $this->assertEquals('Standard Calendar Event course -- DEF', $data2->name);
            }

            // Test action event that were created for the test Course 2.
            if ($context->instanceid == $cm->id  && $context->contextlevel == CONTEXT_MODULE) {
                // Test the content contains Calendar Action Event course data.
                $subcontexts = [
                    get_string('calendar', 'calendar'),
                    get_string('events', 'calendar'),
                    date('c', $event5->timestart)
                ];
                $name = "duedate-event";
                $data = $writer->get_related_data($subcontexts, $name);
                $this->assertEquals('Action Calendar Event duedate -- assign', $data->name);
            }

            // Test Calendar Subscription and Event that were created for the test Course 3.
            if ($context->instanceid == $course3->id && $context->contextlevel == CONTEXT_COURSE) {
                // Test the content contains Calendar Subscription data also created for the test Course 3.
                $subcontexts = [
                    get_string('calendar', 'calendar'),
                    get_string('subscriptions', 'calendar')
                ];
                $name = "course-subscription";
                $data = $writer->get_related_data($subcontexts, $name);
                $this->assertEquals('Calendar Subscription course', $data->name);

                // Test the content contains Calendar Event group data also created for the test Course 3.
                $subcontexts = [
                    get_string('calendar', 'calendar'),
                    get_string('events', 'calendar'),
                    date('c', $event6->timestart)
                ];
                $name = "group-event";
                $data = $writer->get_related_data($subcontexts, $name);
                $this->assertEquals('Standard Calendar Event group', $data->name);
            }
        }

    }

    /**
     * Test for provider::test_export_user_preferences().
     */
    public function test_export_user_preferences() {
        global $DB;

        // Test setup.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Add a user home page preference for the User.
        set_user_preference('calendar_savedflt', 'true', $user);

        // Test the user preference exists.
        $params = [
            'userid' => $user->id,
            'name' => 'calendar_savedflt'
        ];

        // Test the user preferences export contains 1 user preference record for the User.
        provider::export_user_preferences($user->id);
        $contextuser = context_user::instance($user->id);
        $writer = writer::with_context($contextuser);
        $this->assertTrue($writer->has_any_data());

        $exportedpreferences = $writer->get_user_preferences('core_calendar');
        $this->assertCount(1, (array) $exportedpreferences);
        $this->assertEquals('true', $exportedpreferences->calendarsavedflt->value);
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     *
     * @throws dml_exception
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        // Create test user to create Calendar Events and Subscriptions with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Create a Course to test creating a Category Calendar Event.
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        // Get contexts.
        $course1context = context_course::instance($course1->id);
        $course2context = context_course::instance($course2->id);

        // Add a Course Calendar Event by User 1 for Course 1 and Course 2.
        $this->setUser($user1);
        $this->create_test_standard_calendar_event('course', 0, time(), '', 0, $course1->id);
        $this->create_test_standard_calendar_event('course', 0, time(), '', 0, $course2->id);

        // Add a Calendar Subscription by User 1 for Course 1.
        $this->create_test_calendar_subscription('course', 'https://calendar.google.com/', $user1->id, 0, $course1->id);

        // Add a Course Calendar Event by User 2 for Course 1 and Course 2.
        $this->setUser($user2);
        $this->create_test_standard_calendar_event('course', 0, time(), '', 0, $course1->id);
        $this->create_test_standard_calendar_event('course', 0, time(), '', 0, $course2->id);

        // Add a Calendar Subscription by User 2 for Course 2.
        $this->create_test_calendar_subscription('course', 'https://calendar.google.com/', $user2->id, 0, $course2->id);

        // Add a Course Assignment Action Calendar Event by User 2 for Course 2.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $params['course'] = $course2->id;
        $params['assignsubmission_onlinetext_enabled'] = 1;
        $instance = $generator->create_instance($params);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $modulecontext = context_module::instance($cm->id);
        $assign = new assign($modulecontext, $cm, $course2);
        $this->create_test_action_calendar_event('duedate', $course2->id, $instance->id, 'assign', 0, time());
        $this->create_test_action_calendar_event('gradingduedate', $course2->id, $instance->id, 'assign', $user2->id, time());

        // Delete all Calendar Events for all Users by Context for Course 1.
        provider::delete_data_for_all_users_in_context($course1context);

        // The course1 events should still exist, since it belongs to the course and not to the user who created them.
        $events = $DB->get_records('event', array('courseid' => $course1->id));
        $this->assertCount(2, $events);
        // Course1 subscription should still exist, since it belongs to the course and not to the user who created them.
        $subscriptions = $DB->get_records('event_subscriptions', array('courseid' => $course1->id));
        $this->assertCount(1, $subscriptions);

        // The course2 events should still exist, since it belongs to the course and not to the user who created them.
        $events = $DB->get_records('event', array('courseid' => $course2->id));
        $this->assertCount(4, $events);
        // Course2 subscription should still exist, since it belongs to the course and not to the user who created them.
        $subscriptions = $DB->get_records('event_subscriptions', array('courseid' => $course2->id));
        $this->assertCount(1, $subscriptions);

        // Delete all Calendar Events for all Users by Context for Course 2.
        provider::delete_data_for_all_users_in_context($course2context);

        // Verify all Calendar Events for Course 2 context were deleted.
        $events = $DB->get_records('event', array('courseid' => $course2->id, 'modulename' => '0'));
        $this->assertCount(2, $events);
        // Verify all Calendar Subscriptions for Course 2 were deleted.
        $subscriptions = $DB->get_records('event_subscriptions', array('courseid' => $course2->id));
        $this->assertCount(1, $subscriptions);

        // Verify all Calendar Events for the assignment exists still.
        $events = $DB->get_records('event', array('modulename' => 'assign'));
        $this->assertCount(2, $events);

        // Delete all Calendar Events for all Users by Context for the assignment.
        provider::delete_data_for_all_users_in_context($modulecontext);

        // Verify that the action event still exists since it is not a user override.
        $events = $DB->get_records('event', array('modulename' => 'assign'));
        $this->assertCount(1, $events);
    }

    /**
     * Test for provider::delete_data_for_user().
     *
     * @throws dml_exception
     */
    public function test_delete_data_for_user() {
        global $DB;

        // Create test user to create Calendar Events and Subscriptions with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Create a Category and Courses to test creating a Category Calendar Event.
        $category = $this->getDataGenerator()->create_category();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        // Add 6 events for user 1 for various contexts.
        $this->setUser($user1);
        $this->create_test_standard_calendar_event('user', $user1->id, time(), '');
        $this->create_test_standard_calendar_event('user', $user1->id, time(), '');
        $this->create_test_standard_calendar_event('site', 0, time(), '', 0, 1);
        $this->create_test_standard_calendar_event('category', 0, time(), '', $category->id);
        $this->create_test_standard_calendar_event('course', 0, time(), '', 0, $course1->id);
        $this->create_test_standard_calendar_event('course', 0, time(), '', 0, $course2->id);

        // Add two subscription for user 1 at course and user contexts.
        $this->create_test_calendar_subscription('course', 'https://calendar.google.com/', 0, 0, $course2->id);
        $this->create_test_calendar_subscription('user', 'https://calendar.google.com/', $user1->id);

        // Add four events for user 2 for various contexts.
        $this->setUser($user2);
        $this->create_test_standard_calendar_event('user', $user2->id, time(), '');
        $this->create_test_standard_calendar_event('user', $user2->id, time(), '');
        $this->create_test_standard_calendar_event('category', 0, time(), '', $category->id);
        $this->create_test_standard_calendar_event('course', 0, time(), '', 0, $course1->id);

        // Add a calendar Subscription for User 2 at course context.
        $this->create_test_calendar_subscription('user', 'https://calendar.google.com/', $user2->id, 0, $course2->id);

        // Retrieve the user's context ids.
        $contextlist = provider::get_contexts_for_userid($user1->id);
        $approvedcontextlist = new approved_contextlist($user1, 'core_calendar', $contextlist->get_contextids());

        // Delete all Calendar data for User 1.
        provider::delete_data_for_user($approvedcontextlist);

        // All user events created by user1 should have been removed.
        $events = $DB->get_records('event', ['userid' => $user1->id]);
        $this->assertCount(0, $events);
        // The subscription created by user1 should have been deleted.
        $eventsubscriptions = $DB->get_records('event_subscriptions', ['userid' => $user1->id]);
        $this->assertCount(0, $eventsubscriptions);

        // Test all calendar events and subscriptions for user2 still exists and matches the same number created.
        $events = $DB->get_records('event', ['userid' => $user2->id]);
        $this->assertCount(2, $events);
        // The subscription created by user2 should have not been deleted.
        $eventsubscriptions = $DB->get_records('event_subscriptions', ['userid' => $user2->id]);
        $this->assertCount(1, $eventsubscriptions);

        // There should be only six "shared" events created (userid equal zero).
        // One site, one category and two course events that were created by user1.
        // One category and one course event created by user2.
        $events = $DB->get_records('event', ['userid' => 0]);
        $this->assertCount(6, $events);

        // There should be eight event created in total.
        // One site, one category and two course events that were created by user1.
        // One category, one course and two user events created by user2.
        $events = $DB->get_records('event');
        $this->assertCount(8, $events);
    }

    /**
     * Test that only users with a user context are fetched.
     */
    public function test_get_users_in_context() {
        $component = 'core_calendar';

        // Create user1 to create Calendar Events and Subscriptions.
        $user1 = $this->getDataGenerator()->create_user();
        $usercontext1 = context_user::instance($user1->id);
        // Create user2 to create Calendar Events and Subscriptions.
        $user2 = $this->getDataGenerator()->create_user();
        $usercontext2 = context_user::instance($user2->id);
        // Create user3 to create Calendar Events and Subscriptions.
        $user3 = $this->getDataGenerator()->create_user();
        $usercontext3 = context_user::instance($user3->id);

        // Create a Category and Courses to assign Calendar Events and Subscriptions.
        $category = $this->getDataGenerator()->create_category();
        $categorycontext = context_coursecat::instance($category->id);
        $course1 = $this->getDataGenerator()->create_course();
        $course1context = context_course::instance($course1->id);
        $course2 = $this->getDataGenerator()->create_course();
        $course2context = context_course::instance($course2->id);
        $course3 = $this->getDataGenerator()->create_course();
        $course3context = context_course::instance($course3->id);
        $grouprecord = (object)[
            'courseid' => $course3->id,
            'name' => 'test_group'
        ];
        $course3group = $this->getDataGenerator()->create_group($grouprecord);

        // Add Category Calendar Events for Category.
        $this->setUser($user1);
        $this->create_test_standard_calendar_event('category', 0, time(), '',
                $category->id);
        $this->setUser($user2);
        $this->create_test_standard_calendar_event('category', 0, time(), '',
                $category->id);

        // Add User Calendar Events for user1 and user2.
        $this->setUser($user1);
        $this->create_test_standard_calendar_event('user', $user1->id, time(), '');
        $this->create_test_standard_calendar_event('user', $user1->id, time(), '',
                0, $course1->id);
        $this->create_test_standard_calendar_event('user', $user1->id, time(), '',
                0, $course2->id);
        $this->setUser($user2);
        $this->create_test_standard_calendar_event('user', $user2->id, time(), '',
            0, $course1->id);

        // Add a Course Calendar Events for Course 1.
        $this->setUser($user1);
        $this->create_test_standard_calendar_event('course', 0, time(), '',
                0, $course1->id);
        $this->setUser($user2);
        $this->create_test_standard_calendar_event('course', 0, time(), '',
            0, $course1->id);

        // Add a Course Assignment Action Calendar Event for Course 2.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $params['course'] = $course2->id;
        $params['assignsubmission_onlinetext_enabled'] = 1;
        $instance = $generator->create_instance($params);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $modulecontext = context_module::instance($cm->id);
        $assign = new assign($modulecontext, $cm, $course2);
        $this->setUser($user2);
        $this->create_test_action_calendar_event('duedate', $course2->id, $instance->id,
                'assign', $user2->id, time());
        $this->create_test_action_calendar_event('gradingduedate', $course2->id, $instance->id,
                'assign', $user2->id, time());

        // Add a Calendar Subscription and Group Calendar Event to Course 3.
        $this->create_test_standard_calendar_event('group', 0, time(), '', 0,
                $course3->id, $course3group->id);
        $this->setUser($user3);
        $this->create_test_calendar_subscription('course', 'https://calendar.google.com/', $user3->id,
                0, $course3->id);

        // The user list for usercontext1 should return user1.
        $userlist1 = new \core_privacy\local\request\userlist($usercontext1, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(1, $userlist1);
        $this->assertTrue(in_array($user1->id, $userlist1->get_userids()));
        // The user list for usercontext2 should return user2.
        $userlist2 = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);
        $this->assertTrue(in_array($user2->id, $userlist2->get_userids()));
        // The user list for course1context should not return any users.
        $userlist3 = new \core_privacy\local\request\userlist($course1context, $component);
        provider::get_users_in_context($userlist3);
        $this->assertCount(0, $userlist3);
        // The user list for course2context should not return any users.
        $userlist4 = new \core_privacy\local\request\userlist($course2context, $component);
        provider::get_users_in_context($userlist4);
        $this->assertCount(0, $userlist4);
        // The user list for course3context should not return any users.
        $userlist5 = new \core_privacy\local\request\userlist($course3context, $component);
        provider::get_users_in_context($userlist5);
        $this->assertCount(0, $userlist5);
        // The user list for categorycontext should not return any users.
        $userlist6 = new \core_privacy\local\request\userlist($categorycontext, $component);
        provider::get_users_in_context($userlist6);
        $this->assertCount(0, $userlist6);
        // The user list for modulecontext should return user2.
        $userlist7 = new \core_privacy\local\request\userlist($modulecontext, $component);
        provider::get_users_in_context($userlist7);
        $this->assertCount(1, $userlist7);
        $this->assertTrue(in_array($user2->id, $userlist7->get_userids()));
        // The user list for usercontext3 should not return any users.
        $userlist8 = new \core_privacy\local\request\userlist($usercontext3, $component);
        provider::get_users_in_context($userlist8);
        $this->assertCount(0, $userlist8);
    }

    /**
     * Test that data for users in approved userlist is deleted.
     */
    public function test_delete_data_for_users() {
        $component = 'core_calendar';

        // Create user1 to create Calendar Events and Subscriptions.
        $user1 = $this->getDataGenerator()->create_user();
        $usercontext1 = context_user::instance($user1->id);
        // Create user2 to create Calendar Events and Subscriptions.
        $user2 = $this->getDataGenerator()->create_user();
        $usercontext2 = context_user::instance($user2->id);
        // Create user3 to create Calendar Events and Subscriptions.
        $user3 = $this->getDataGenerator()->create_user();
        $usercontext3 = context_user::instance($user3->id);

        // Create a Category and Courses to assign Calendar Events and Subscriptions.
        $category = $this->getDataGenerator()->create_category();
        $categorycontext = context_coursecat::instance($category->id);
        $course1 = $this->getDataGenerator()->create_course();
        $course1context = context_course::instance($course1->id);
        $course2 = $this->getDataGenerator()->create_course();
        $course2context = context_course::instance($course2->id);
        $course3 = $this->getDataGenerator()->create_course();
        $course3context = context_course::instance($course3->id);
        $grouprecord = (object)[
            'courseid' => $course3->id,
            'name' => 'test_group'
        ];
        $course3group = $this->getDataGenerator()->create_group($grouprecord);

        // Add Category Calendar Events for Category.
        $this->setUser($user1);
        $this->create_test_standard_calendar_event('category', 0, time(), '',
            $category->id);
        $this->setUser($user2);
        $this->create_test_standard_calendar_event('category', 0, time(), '',
            $category->id);

        // Add User Calendar Events for user1 and user2.
        $this->setUser($user1);
        $this->create_test_standard_calendar_event('user', $user1->id, time(), '');
        $this->create_test_standard_calendar_event('user', $user1->id, time(), '',
            0, $course1->id);
        $this->create_test_standard_calendar_event('user', $user1->id, time(), '',
            0, $course2->id);
        $this->setUser($user2);
        $this->create_test_standard_calendar_event('user', $user2->id, time(), '',
            0, $course1->id);

        // Add a Course Calendar Events for Course 1.
        $this->setUser($user1);
        $this->create_test_standard_calendar_event('course', 0, time(), '',
            0, $course1->id);
        $this->setUser($user2);
        $this->create_test_standard_calendar_event('course', 0, time(), '',
            0, $course1->id);

        // Add a Course Assignment Action Calendar Event for Course 2.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $params['course'] = $course2->id;
        $params['assignsubmission_onlinetext_enabled'] = 1;
        $instance = $generator->create_instance($params);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $modulecontext = context_module::instance($cm->id);
        $assign = new assign($modulecontext, $cm, $course2);
        $this->setUser($user2);
        $this->create_test_action_calendar_event('duedate', $course2->id, $instance->id,
            'assign', $user2->id, time());
        $this->create_test_action_calendar_event('gradingduedate', $course2->id, $instance->id,
            'assign', $user2->id, time());

        // Add a Calendar Subscription and Group Calendar Event to Course 3.
        $this->create_test_standard_calendar_event('group', 0, time(), '', 0,
            $course3->id, $course3group->id);
        $this->setUser($user3);
        $this->create_test_calendar_subscription('course', 'https://calendar.google.com/', $user3->id,
            0, $course3->id);

        // The user list for usercontext1 should return user1.
        $userlist1 = new \core_privacy\local\request\userlist($usercontext1, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(1, $userlist1);
        // The user list for usercontext2 should return user2.
        $userlist2 = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);
        // The user list for course1context should not return any users.
        $userlist3 = new \core_privacy\local\request\userlist($course1context, $component);
        provider::get_users_in_context($userlist3);
        $this->assertCount(0, $userlist3);
        // The user list for course2context should not return any users.
        $userlist4 = new \core_privacy\local\request\userlist($course2context, $component);
        provider::get_users_in_context($userlist4);
        $this->assertCount(0, $userlist4);
        // The user list for course3context should not return any users.
        $userlist5 = new \core_privacy\local\request\userlist($course3context, $component);
        provider::get_users_in_context($userlist5);
        $this->assertCount(0, $userlist5);
        // The user list for categorycontext should not return any users.
        $userlist6 = new \core_privacy\local\request\userlist($categorycontext, $component);
        provider::get_users_in_context($userlist6);
        $this->assertCount(0, $userlist6);
        // The user list for modulecontext should return user2.
        $userlist7 = new \core_privacy\local\request\userlist($modulecontext, $component);
        provider::get_users_in_context($userlist7);
        $this->assertCount(1, $userlist7);
        // The user list for usercontext3 should not return any users.
        $userlist8 = new \core_privacy\local\request\userlist($usercontext3, $component);
        provider::get_users_in_context($userlist8);
        $this->assertCount(0, $userlist8);

        // Convert $userlist1 into an approved_contextlist.
        $approvedlist1 = new approved_userlist($usercontext1, $component, $userlist1->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist1);
        // The user list for usercontext1 should not return any users.
        $userlist1 = new \core_privacy\local\request\userlist($usercontext1, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(0, $userlist1);
        // The user list for usercontext2 should still return users2.
        $userlist2 = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);

        // Convert $userlist3 into an approved_contextlist.
        // Pass an empty array as a value for the approved user list.
        $approvedlist2 = new approved_userlist($course1context, $component, []);
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist2);
        // The user list for course1context should not return any users.
        $userlist3 = new \core_privacy\local\request\userlist($course1context, $component);
        provider::get_users_in_context($userlist3);
        $this->assertCount(0, $userlist3);

        // Convert $userlist3 into an approved_contextlist.
        // Pass the ID of user1 as a value for the approved user list.
        $approvedlist2 = new approved_userlist($course1context, $component, [$user1->id]);
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist2);
        // The user list for course1context should not return any users.
        $userlist3 = new \core_privacy\local\request\userlist($course1context, $component);
        provider::get_users_in_context($userlist3);
        $this->assertCount(0, $userlist3);

        // The user list for course3context should not return any users.
        $userlist5 = new \core_privacy\local\request\userlist($course3context, $component);
        provider::get_users_in_context($userlist5);
        $this->assertCount(0, $userlist5);

        // Convert $userlist6 into an approved_contextlist.
        $approvedlist3 = new approved_userlist($categorycontext, $component, $userlist6->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist3);
        // The user list for categorycontext should not return any users.
        $userlist6 = new \core_privacy\local\request\userlist($categorycontext, $component);
        provider::get_users_in_context($userlist6);
        $this->assertCount(0, $userlist6);

        // Convert $userlist7 into an approved_contextlist.
        $approvedlist4 = new approved_userlist($modulecontext, $component, $userlist7->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist4);
        // The user list for modulecontext should not return any users.
        $userlist7 = new \core_privacy\local\request\userlist($modulecontext, $component);
        provider::get_users_in_context($userlist7);
        $this->assertCount(0, $userlist7);
    }

    // Start of helper functions.

    /**
     * Helper function to create a Standard Calendar Event.
     *
     * @param string    $eventtype  Calendar event type
     * @param int       $userid     User Id
     * @param int       $time       Timestamp value
     * @param string    $customname Custom name
     * @param int       $categoryid Course Category Id
     * @param int       $courseid   Course Id
     * @param int       $groupid    Group Id
     * @return bool|calendar_event  Standard Calendar Event created.
     * @throws coding_exception
     */
    protected function create_test_standard_calendar_event($eventtype, $userid, $time, $customname = '', $categoryid = 0, $courseid = 0, $groupid = 0) {
        // Create a standard calendar event.
        $name = "Standard Calendar Event $eventtype";
        if ($customname != '') {
            $name .= " -- $customname";
        }

        $event = (object)[
            'name' => $name,
            'categoryid' => $categoryid,
            'courseid' => $courseid,
            'groupid' => $groupid,
            'userid' => ($eventtype == 'user') ? $userid : 0,
            'modulename' => 0,
            'instance' => 0,
            'eventtype' => $eventtype,
            'type' => CALENDAR_EVENT_TYPE_STANDARD,
            'timestart' => $time,
            'visible' => 1
        ];
        return calendar_event::create($event, false);
    }

    /**
     * Helper function to create an Action Calendar Event.
     *
     * @param string    $eventtype  Calendar event type
     * @param int       $courseid   Course Id
     * @param int       $instanceid Activity Module instance id
     * @param string    $modulename Activity Module name
     * @param int       $userid     User Id
     * @param int       $time       Timestamp value
     * @return bool|calendar_event  Action Calendar Event created.
     * @throws coding_exception
     */
    protected function create_test_action_calendar_event($eventtype, $courseid, $instanceid, $modulename, $userid, $time) {
        // Create an action calendar event.
        $event = (object)[
            'name' => "Action Calendar Event $eventtype -- $modulename",
            'categoryid' => 0,
            'courseid' => $courseid,
            'groupid' => 0,
            'userid' => $userid,
            'modulename' => $modulename,
            'instance' => $instanceid,
            'eventtype' => $eventtype,
            'type' => CALENDAR_EVENT_TYPE_ACTION,
            'timestart' => $time,
            'visible' => 1
        ];
        return calendar_event::create($event, false);
    }

    /**
     * Helper function to create a Calendar Subscription.
     *
     * @param string    $eventtype  Calendar Subscription event type
     * @param string    $url        Calendar Subscription URL
     * @param int       $userid     User Id
     * @param int       $categoryid Category Id
     * @param int       $courseid   Course Id
     * @param int       $groupid    Group Id
     * @return int  Calendar Subscription Id
     */
    protected function create_test_calendar_subscription($eventtype, $url, $userid, $categoryid = 0, $courseid = 0, $groupid = 0) {
        // Create a subscription calendar event.
        $subscription = (object)[
            'name' => "Calendar Subscription " . $eventtype,
            'url' => $url,
            'categoryid' => $categoryid,
            'courseid' => $courseid,
            'groupid' => $groupid,
            'userid' => ($eventtype == 'user') ? $userid : 0,
            'eventtype' => $eventtype
        ];

        return calendar_add_subscription($subscription);
    }

}
