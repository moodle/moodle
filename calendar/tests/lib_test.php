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

namespace core_calendar;

/**
 * Class contaning unit tests for the calendar lib.
 *
 * @package    core_calendar
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lib_test extends \advanced_testcase {

    /**
     * Load required test libraries
     */
    public static function setUpBeforeClass(): void {
        global $CFG;

        require_once("{$CFG->dirroot}/calendar/tests/helpers.php");
        parent::setUpBeforeClass();
    }

    /**
     * Tests set up
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    /**
     * Test that the get_events() function only returns activity events that are enabled.
     */
    public function test_get_events_with_disabled_module(): void {
        global $DB;
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $assigngenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $assigninstance = $assigngenerator->create_instance(['course' => $course->id]);
        $lessongenerator = $this->getDataGenerator()->get_plugin_generator('mod_lesson');
        $lessoninstance = $lessongenerator->create_instance(['course' => $course->id]);
        $student = $generator->create_user();
        $generator->enrol_user($student->id, $course->id, 'student');
        $this->setUser($student);
        $events = [
            [
                'name' => 'Start of assignment',
                'description' => '',
                'location' => 'Test',
                'format' => 1,
                'courseid' => $course->id,
                'groupid' => 0,
                'userid' => 2,
                'modulename' => 'assign',
                'instance' => $assigninstance->id,
                'eventtype' => 'due',
                'timestart' => time(),
                'timeduration' => 86400,
                'visible' => 1
            ], [
                'name' => 'Start of lesson',
                'description' => '',
                'location' => 'Test',
                'format' => 1,
                'courseid' => $course->id,
                'groupid' => 0,
                'userid' => 2,
                'modulename' => 'lesson',
                'instance' => $lessoninstance->id,
                'eventtype' => 'end',
                'timestart' => time(),
                'timeduration' => 86400,
                'visible' => 1
            ]
        ];
        foreach ($events as $event) {
            \calendar_event::create($event, false);
        }
        $timestart = time() - 60;
        $timeend = time() + 60;
        // Get all events.
        $events = calendar_get_events($timestart, $timeend, true, 0, true);
        $this->assertCount(2, $events);
        // Disable the lesson module.
        $modulerecord = $DB->get_record('modules', ['name' => 'lesson']);
        $modulerecord->visible = 0;
        $DB->update_record('modules', $modulerecord);
        // Check that we only return the assign event.
        $events = calendar_get_events($timestart, $timeend, true, 0, true);
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertEquals('assign', $event->modulename);
    }

    public function test_get_course_cached(): void {
        // Setup some test courses.
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();

        // Load courses into cache.
        $coursecache = null;
        calendar_get_course_cached($coursecache, $course1->id);
        calendar_get_course_cached($coursecache, $course2->id);
        calendar_get_course_cached($coursecache, $course3->id);

        // Verify the cache.
        $this->assertArrayHasKey($course1->id, $coursecache);
        $cachedcourse1 = $coursecache[$course1->id];
        $this->assertEquals($course1->id, $cachedcourse1->id);
        $this->assertEquals($course1->shortname, $cachedcourse1->shortname);
        $this->assertEquals($course1->fullname, $cachedcourse1->fullname);

        $this->assertArrayHasKey($course2->id, $coursecache);
        $cachedcourse2 = $coursecache[$course2->id];
        $this->assertEquals($course2->id, $cachedcourse2->id);
        $this->assertEquals($course2->shortname, $cachedcourse2->shortname);
        $this->assertEquals($course2->fullname, $cachedcourse2->fullname);

        $this->assertArrayHasKey($course3->id, $coursecache);
        $cachedcourse3 = $coursecache[$course3->id];
        $this->assertEquals($course3->id, $cachedcourse3->id);
        $this->assertEquals($course3->shortname, $cachedcourse3->shortname);
        $this->assertEquals($course3->fullname, $cachedcourse3->fullname);
    }

    /**
     * Test the update_subscription() function.
     */
    public function test_update_subscription(): void {
        $this->resetAfterTest(true);

        $subscription = new \stdClass();
        $subscription->eventtype = 'site';
        $subscription->name = 'test';
        $id = calendar_add_subscription($subscription);

        $subscription = calendar_get_subscription($id);
        $subscription->name = 'awesome';
        calendar_update_subscription($subscription);
        $sub = calendar_get_subscription($id);
        $this->assertEquals($subscription->name, $sub->name);

        $subscription = calendar_get_subscription($id);
        $subscription->name = 'awesome2';
        $subscription->pollinterval = 604800;
        calendar_update_subscription($subscription);
        $sub = calendar_get_subscription($id);
        $this->assertEquals($subscription->name, $sub->name);
        $this->assertEquals($subscription->pollinterval, $sub->pollinterval);

        $subscription = new \stdClass();
        $subscription->name = 'awesome4';
        $this->expectException('coding_exception');
        calendar_update_subscription($subscription);
    }

    public function test_add_subscription(): void {
        global $DB, $CFG;

        require_once($CFG->dirroot . '/lib/bennu/bennu.inc.php');

        $this->resetAfterTest(true);

        // Test for Microsoft Outlook 2010.
        $subscription = new \stdClass();
        $subscription->name = 'Microsoft Outlook 2010';
        $subscription->importfrom = CALENDAR_IMPORT_FROM_FILE;
        $subscription->eventtype = 'site';
        $id = calendar_add_subscription($subscription);

        $calendar = file_get_contents($CFG->dirroot . '/lib/tests/fixtures/ms_outlook_2010.ics');
        $ical = new \iCalendar();
        $ical->unserialize($calendar);
        $this->assertEquals($ical->parser_errors, array());

        $sub = calendar_get_subscription($id);
        calendar_import_events_from_ical($ical, $sub->id);
        $count = $DB->count_records('event', array('subscriptionid' => $sub->id));
        $this->assertEquals($count, 1);

        // Test for OSX Yosemite.
        $subscription = new \stdClass();
        $subscription->name = 'OSX Yosemite';
        $subscription->importfrom = CALENDAR_IMPORT_FROM_FILE;
        $subscription->eventtype = 'site';
        $id = calendar_add_subscription($subscription);

        $calendar = file_get_contents($CFG->dirroot . '/lib/tests/fixtures/osx_yosemite.ics');
        $ical = new \iCalendar();
        $ical->unserialize($calendar);
        $this->assertEquals($ical->parser_errors, array());

        $sub = calendar_get_subscription($id);
        calendar_import_events_from_ical($ical, $sub->id);
        $count = $DB->count_records('event', array('subscriptionid' => $sub->id));
        $this->assertEquals($count, 1);

        // Test for Google Gmail.
        $subscription = new \stdClass();
        $subscription->name = 'Google Gmail';
        $subscription->importfrom = CALENDAR_IMPORT_FROM_FILE;
        $subscription->eventtype = 'site';
        $id = calendar_add_subscription($subscription);

        $calendar = file_get_contents($CFG->dirroot . '/lib/tests/fixtures/google_gmail.ics');
        $ical = new \iCalendar();
        $ical->unserialize($calendar);
        $this->assertEquals($ical->parser_errors, array());

        $sub = calendar_get_subscription($id);
        calendar_import_events_from_ical($ical, $sub->id);
        $count = $DB->count_records('event', array('subscriptionid' => $sub->id));
        $this->assertEquals($count, 1);

        // Test for ICS file with repeated events.
        $subscription = new \stdClass();
        $subscription->name = 'Repeated events';
        $subscription->importfrom = CALENDAR_IMPORT_FROM_FILE;
        $subscription->eventtype = 'site';
        $id = calendar_add_subscription($subscription);
        $calendar = file_get_contents($CFG->dirroot . '/lib/tests/fixtures/repeated_events.ics');
        $ical = new \iCalendar();
        $ical->unserialize($calendar);
        $this->assertEquals($ical->parser_errors, []);

        $sub = calendar_get_subscription($id);
        $output = calendar_import_events_from_ical($ical, $sub->id);
        $this->assertArrayHasKey('eventsimported', $output);
        $this->assertArrayHasKey('eventsskipped', $output);
        $this->assertArrayHasKey('eventsupdated', $output);
        $this->assertArrayHasKey('eventsdeleted', $output);
        $this->assertEquals(1, $output['eventsimported']);
        $this->assertEquals(0, $output['eventsskipped']);
        $this->assertEquals(0, $output['eventsupdated']);
        $this->assertEquals(0, $output['eventsdeleted']);
    }

    /**
     * Test for calendar_get_legacy_events() when there are user and group overrides.
     */
    public function test_get_legacy_events_with_overrides(): void {
        $generator = $this->getDataGenerator();

        $course = $generator->create_course();

        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        if (!isset($params['course'])) {
            $params['course'] = $course->id;
        }

        $instance = $plugingenerator->create_instance($params);

        // Create users.
        $useroverridestudent = $generator->create_user();
        $group1student = $generator->create_user();
        $group2student = $generator->create_user();
        $group12student = $generator->create_user();
        $nogroupstudent = $generator->create_user();

        // Enrol users.
        $generator->enrol_user($useroverridestudent->id, $course->id, 'student');
        $generator->enrol_user($group1student->id, $course->id, 'student');
        $generator->enrol_user($group2student->id, $course->id, 'student');
        $generator->enrol_user($group12student->id, $course->id, 'student');
        $generator->enrol_user($nogroupstudent->id, $course->id, 'student');

        // Create groups.
        $group1 = $generator->create_group(['courseid' => $course->id]);
        $group2 = $generator->create_group(['courseid' => $course->id]);

        // Add members to groups.
        $generator->create_group_member(['groupid' => $group1->id, 'userid' => $group1student->id]);
        $generator->create_group_member(['groupid' => $group2->id, 'userid' => $group2student->id]);
        $generator->create_group_member(['groupid' => $group1->id, 'userid' => $group12student->id]);
        $generator->create_group_member(['groupid' => $group2->id, 'userid' => $group12student->id]);
        $now = time();

        // Events with the same module name, instance and event type.
        $events = [
            [
                'name' => 'Assignment 1 due date',
                'description' => '',
                'location' => 'Test',
                'format' => 0,
                'courseid' => $course->id,
                'groupid' => 0,
                'userid' => 2,
                'modulename' => 'assign',
                'instance' => $instance->id,
                'eventtype' => 'due',
                'timestart' => $now,
                'timeduration' => 0,
                'visible' => 1
            ], [
                'name' => 'Assignment 1 due date - User override',
                'description' => '',
                'location' => 'Test',
                'format' => 1,
                'courseid' => 0,
                'groupid' => 0,
                'userid' => $useroverridestudent->id,
                'modulename' => 'assign',
                'instance' => $instance->id,
                'eventtype' => 'due',
                'timestart' => $now + 86400,
                'timeduration' => 0,
                'visible' => 1,
                'priority' => CALENDAR_EVENT_USER_OVERRIDE_PRIORITY
            ], [
                'name' => 'Assignment 1 due date - Group A override',
                'description' => '',
                'location' => 'Test',
                'format' => 1,
                'courseid' => $course->id,
                'groupid' => $group1->id,
                'userid' => 2,
                'modulename' => 'assign',
                'instance' => $instance->id,
                'eventtype' => 'due',
                'timestart' => $now + (2 * 86400),
                'timeduration' => 0,
                'visible' => 1,
                'priority' => 1,
            ], [
                'name' => 'Assignment 1 due date - Group B override',
                'description' => '',
                'location' => 'Test',
                'format' => 1,
                'courseid' => $course->id,
                'groupid' => $group2->id,
                'userid' => 2,
                'modulename' => 'assign',
                'instance' => $instance->id,
                'eventtype' => 'due',
                'timestart' => $now + (3 * 86400),
                'timeduration' => 0,
                'visible' => 1,
                'priority' => 2,
            ],
        ];

        foreach ($events as $event) {
            \calendar_event::create($event, false);
        }

        $timestart = $now - 100;
        $timeend = $now + (3 * 86400);
        $groups = [$group1->id, $group2->id];

        // Get user override events.
        $this->setUser($useroverridestudent);
        $events = calendar_get_legacy_events($timestart, $timeend, $useroverridestudent->id, $groups, $course->id);
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertEquals('Assignment 1 due date - User override', $event->name);

        // Get event for user with override but with the timestart and timeend parameters only covering the original event.
        $events = calendar_get_legacy_events($timestart, $now, $useroverridestudent->id, $groups, $course->id);
        $this->assertCount(0, $events);

        // Get events for user that does not belong to any group and has no user override events.
        $this->setUser($nogroupstudent);
        $events = calendar_get_legacy_events($timestart, $timeend, $nogroupstudent->id, $groups, $course->id);
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertEquals('Assignment 1 due date', $event->name);

        // Get events for user that belongs to groups A and B and has no user override events.
        $this->setUser($group12student);
        $events = calendar_get_legacy_events($timestart, $timeend, $group12student->id, $groups, $course->id);
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertEquals('Assignment 1 due date - Group A override', $event->name);

        // Get events for user that belongs to group A and has no user override events.
        $this->setUser($group1student);
        $events = calendar_get_legacy_events($timestart, $timeend, $group1student->id, $groups, $course->id);
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertEquals('Assignment 1 due date - Group A override', $event->name);

        // Add repeating events.
        $repeatingevents = [
            [
                'name' => 'Repeating site event',
                'description' => '',
                'location' => 'Test',
                'format' => 1,
                'courseid' => SITEID,
                'groupid' => 0,
                'userid' => 2,
                'repeatid' => $event->id,
                'modulename' => '0',
                'instance' => 0,
                'eventtype' => 'site',
                'timestart' => $now + 86400,
                'timeduration' => 0,
                'visible' => 1,
            ],
            [
                'name' => 'Repeating site event',
                'description' => '',
                'location' => 'Test',
                'format' => 1,
                'courseid' => SITEID,
                'groupid' => 0,
                'userid' => 2,
                'repeatid' => $event->id,
                'modulename' => '0',
                'instance' => 0,
                'eventtype' => 'site',
                'timestart' => $now + (2 * 86400),
                'timeduration' => 0,
                'visible' => 1,
            ],
        ];

        foreach ($repeatingevents as $event) {
            \calendar_event::create($event, false);
        }

        // Make sure repeating events are not filtered out.
        $events = calendar_get_legacy_events($timestart, $timeend, true, true, true);
        $this->assertCount(3, $events);
    }

    public function test_calendar_get_default_courses(): void {
        global $USER, $CFG;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course1 = $generator->create_course();
        $course2 = $generator->create_course();
        $course3 = $generator->create_course();
        $context = \context_course::instance($course1->id);

        $this->setAdminUser();
        $admin = clone $USER;

        $teacher = $generator->create_user();
        $generator->enrol_user($teacher->id, $course1->id, 'teacher');
        $generator->enrol_user($admin->id, $course1->id, 'teacher');

        $CFG->calendar_adminseesall = false;

        $courses = calendar_get_default_courses();
        // Only enrolled in one course.
        $this->assertCount(1, $courses);
        $courses = calendar_get_default_courses($course2->id);
        // Enrolled course + current course.
        $this->assertCount(2, $courses);
        $CFG->calendar_adminseesall = true;
        $courses = calendar_get_default_courses();
        // All courses + SITE.
        $this->assertCount(4, $courses);
        $courses = calendar_get_default_courses($course2->id);
        // All courses + SITE.
        $this->assertCount(4, $courses);

        $this->setUser($teacher);

        $CFG->calendar_adminseesall = false;

        $courses = calendar_get_default_courses();
        // Only enrolled in one course.
        $this->assertCount(1, $courses);
        $courses = calendar_get_default_courses($course2->id);
        // Enrolled course only (ignore current).
        $this->assertCount(1, $courses);
        // This setting should not affect teachers.
        $CFG->calendar_adminseesall = true;
        $courses = calendar_get_default_courses();
        // Only enrolled in one course.
        $this->assertCount(1, $courses);
        $courses = calendar_get_default_courses($course2->id);
        // Enrolled course only (ignore current).
        $this->assertCount(1, $courses);

        // Now, log out and test again.
        $this->setUser();

        $CFG->calendar_adminseesall = false;

        $courses = calendar_get_default_courses(null, '*', false, $teacher->id);
        // Only enrolled in one course.
        $this->assertCount(1, $courses);
        $courses = calendar_get_default_courses($course2->id, '*', false, $teacher->id);
        // Enrolled course only (ignore current).
        $this->assertCount(1, $courses);
        // This setting should not affect teachers.
        $CFG->calendar_adminseesall = true;
        $courses = calendar_get_default_courses(null, '*', false, $teacher->id);
        // Only enrolled in one course.
        $this->assertCount(1, $courses);
        $courses = calendar_get_default_courses($course2->id, '*', false, $teacher->id);
        // Enrolled course only (ignore current).
        $this->assertCount(1, $courses);

    }

    /**
     * Confirm that the skip events flag causes the calendar_get_view function
     * to avoid querying for the calendar events.
     */
    public function test_calendar_get_view_skip_events(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $skipnavigation = true;
        $skipevents = true;
        $event = create_event([
            'eventtype' => 'user',
            'userid' => $user->id
        ]);

        $this->setUser($user);
        $calendar = \calendar_information::create(time() - 10, SITEID, null);

        list($data, $template) = calendar_get_view($calendar, 'day', $skipnavigation, $skipevents);
        $this->assertEmpty($data->events);

        $skipevents = false;
        list($data, $template) = calendar_get_view($calendar, 'day', $skipnavigation, $skipevents);

        $this->assertEquals($event->id, $data->events[0]->id);
    }

    public function test_calendar_get_allowed_event_types_course(): void {
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course1 = $generator->create_course(); // Has capability.
        $course2 = $generator->create_course(); // Doesn't have capability.
        $course3 = $generator->create_course(); // Not enrolled.
        $context1 = \context_course::instance($course1->id);
        $context2 = \context_course::instance($course2->id);
        $context3 = \context_course::instance($course3->id);
        $roleid = $generator->create_role();
        $contexts = [$context1, $context2, $context3];
        $enrolledcourses = [$course1, $course2];

        foreach ($enrolledcourses as $course) {
            $generator->enrol_user($user->id, $course->id, 'student');
        }

        foreach ($contexts as $context) {
            $generator->role_assign($roleid, $user->id, $context->id);
        }

        $this->setUser($user);

        // In general for all courses, they don't have the ability to add course events yet.
        $types = calendar_get_allowed_event_types();
        $this->assertFalse($types['course']);

        assign_capability('moodle/calendar:manageentries', CAP_ALLOW, $roleid, $context1, true);
        assign_capability('moodle/calendar:manageentries', CAP_PROHIBIT, $roleid, $context2, true);

        // The user only has the correct capability in course 1 so that is the only
        // one that should be in the results.
        $types = calendar_get_allowed_event_types($course1->id);
        $this->assertTrue($types['course']);

        // If calling function without specified course,  there is still a course where they have it.
        $types = calendar_get_allowed_event_types();
        $this->assertTrue($types['course']);

        assign_capability('moodle/calendar:manageentries', CAP_PROHIBIT, $roleid, $context1, true);

        // The user only now has the correct capability in both course 1 and 2 so we
        // expect both to be in the results.
        $types = calendar_get_allowed_event_types($course3->id);
        $this->assertFalse($types['course']);

        // They now do not have permission in any course.
        $types = calendar_get_allowed_event_types();
        $this->assertFalse($types['course']);
    }

    public function test_calendar_get_allowed_event_types_group_no_acces_to_diff_groups(): void {
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $generator->create_course();
        $context = \context_course::instance($course->id);
        $roleid = $generator->create_role();

        $generator->enrol_user($user->id, $course->id, 'student');
        $generator->role_assign($roleid, $user->id, $context->id);

        $this->setUser($user);

        assign_capability('moodle/calendar:manageentries', CAP_ALLOW, $roleid, $context, true);
        assign_capability('moodle/site:accessallgroups', CAP_PROHIBIT, $roleid, $context, true);

        // The user has the correct capability in the course but they aren't a member
        // of any of the groups and don't have the accessallgroups capability.
        $types = calendar_get_allowed_event_types($course->id);
        $this->assertTrue($types['course']);
        $this->assertFalse($types['group']);

        // Same result applies when not providing a specific course as they are only on one course.
        $types = calendar_get_allowed_event_types();
        $this->assertTrue($types['course']);
        $this->assertFalse($types['group']);
    }

    public function test_calendar_get_allowed_event_types_group_no_groups(): void {
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $generator->create_course();
        $context = \context_course::instance($course->id);
        $roleid = $generator->create_role();
        $generator->enrol_user($user->id, $course->id, 'student');
        $generator->role_assign($roleid, $user->id, $context->id);
        $this->setUser($user);
        assign_capability('moodle/calendar:manageentries', CAP_ALLOW, $roleid, $context, true);
        // The user has the correct capability in the course but there are
        // no groups so we shouldn't see a group type.
        $types = calendar_get_allowed_event_types($course->id);
        $this->assertTrue($types['course']);
        $this->assertFalse($types['group']);

        // Same result applies when not providing a specific course as they are only on one course.
        $types = calendar_get_allowed_event_types();
        $this->assertTrue($types['course']);
        $this->assertFalse($types['group']);
    }

    public function test_calendar_get_allowed_event_types_group_access_all_groups(): void {
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course1 = $generator->create_course();
        $course2 = $generator->create_course();
        $generator->create_group(array('courseid' => $course1->id));
        $generator->create_group(array('courseid' => $course2->id));
        $context1 = \context_course::instance($course1->id);
        $context2 = \context_course::instance($course2->id);
        $roleid = $generator->create_role();
        $generator->enrol_user($user->id, $course1->id, 'student');
        $generator->enrol_user($user->id, $course2->id, 'student');
        $generator->role_assign($roleid, $user->id, $context1->id);
        $generator->role_assign($roleid, $user->id, $context2->id);
        $this->setUser($user);
        assign_capability('moodle/calendar:manageentries', CAP_ALLOW, $roleid, $context1, true);
        assign_capability('moodle/calendar:manageentries', CAP_ALLOW, $roleid, $context2, true);
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $roleid, $context1, true);
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $roleid, $context2, true);
        // The user has the correct capability in the course and has
        // the accessallgroups capability.
        $types = calendar_get_allowed_event_types($course1->id);
        $this->assertTrue($types['group']);

        // Same result applies when not providing a specific course as they are only on one course.
        $types = calendar_get_allowed_event_types();
        $this->assertTrue($types['group']);
    }

    public function test_calendar_get_allowed_event_types_group_no_access_all_groups(): void {
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $generator->create_course();
        $context = \context_course::instance($course->id);
        $group1 = $generator->create_group(array('courseid' => $course->id));
        $group2 = $generator->create_group(array('courseid' => $course->id));
        $roleid = $generator->create_role();
        $generator->enrol_user($user->id, $course->id, 'student');
        $generator->role_assign($roleid, $user->id, $context->id);
        $generator->create_group_member(array('groupid' => $group1->id, 'userid' => $user->id));
        $generator->create_group_member(array('groupid' => $group2->id, 'userid' => $user->id));
        $this->setUser($user);
        assign_capability('moodle/site:accessallgroups', CAP_PROHIBIT, $roleid, $context, true);
        // The user has the correct capability in the course but can't access
        // groups that they are not a member of.
        $types = calendar_get_allowed_event_types($course->id);
        $this->assertFalse($types['group']);

        // Same result applies when not providing a specific course as they are only on one course.
        $types = calendar_get_allowed_event_types();
        $this->assertFalse($types['group']);

        assign_capability('moodle/calendar:manageentries', CAP_ALLOW, $roleid, $context, true);
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $roleid, $context, true);
        $types = calendar_get_allowed_event_types($course->id);
        $this->assertTrue($types['group']);

        // Same result applies when not providing a specific course as they are only on one course.
        $types = calendar_get_allowed_event_types();
        $this->assertTrue($types['group']);
    }

    public function test_calendar_get_allowed_event_types_group_cap_no_groups(): void {
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $generator->create_course();
        $context = \context_course::instance($course->id);
        $roleid = $generator->create_role();
        $group = $generator->create_group(['courseid' => $course->id]);
        $generator->enrol_user($user->id, $course->id, 'student');
        $generator->role_assign($roleid, $user->id, $context->id);
        assign_capability('moodle/calendar:managegroupentries', CAP_ALLOW, $roleid, $context, true);

        $this->setUser($user);
        $types = calendar_get_allowed_event_types($course->id);
        $this->assertFalse($types['course']);
        $this->assertFalse($types['group']);

        // Check without specifying a course (same result as user only has one course).
        $types = calendar_get_allowed_event_types();
        $this->assertFalse($types['course']);
        $this->assertFalse($types['group']);
    }

    public function test_calendar_get_allowed_event_types_group_cap_has_group(): void {
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $generator->create_course();
        $context = \context_course::instance($course->id);
        $roleid = $generator->create_role();
        $group = $generator->create_group(['courseid' => $course->id]);
        $generator->enrol_user($user->id, $course->id, 'student');
        $generator->role_assign($roleid, $user->id, $context->id);
        groups_add_member($group, $user);
        assign_capability('moodle/calendar:managegroupentries', CAP_ALLOW, $roleid, $context, true);

        $this->setUser($user);
        $types = calendar_get_allowed_event_types($course->id);
        $this->assertFalse($types['course']);
        $this->assertTrue($types['group']);

        // Check without specifying a course (same result as user only has one course).
        $types = calendar_get_allowed_event_types();
        $this->assertFalse($types['course']);
        $this->assertTrue($types['group']);
    }

    public function test_calendar_get_allowed_event_types_group_cap_access_all_groups(): void {
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $course = $generator->create_course();
        $context = \context_course::instance($course->id);
        $roleid = $generator->create_role();
        $group = $generator->create_group(['courseid' => $course->id]);
        $generator->enrol_user($user->id, $course->id, 'student');
        $generator->role_assign($roleid, $user->id, $context->id);
        assign_capability('moodle/calendar:managegroupentries', CAP_ALLOW, $roleid, $context, true);
        assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $roleid, $context, true);

        $this->setUser($user);
        $types = calendar_get_allowed_event_types($course->id);
        $this->assertFalse($types['course']);
        $this->assertTrue($types['group']);

        // Check without specifying a course (same result as user only has one course).
        $types = calendar_get_allowed_event_types();
        $this->assertFalse($types['course']);
        $this->assertTrue($types['group']);
    }

    /**
     * This is a setup helper function that create some users, courses, groups and group memberships.
     * This is useful to prepare the environment for testing the calendar_set_filters function.
     *
     * @return array An array of ($users, $courses, $coursegroups)
     */
    protected function setup_test_calendar_set_filters() {
        $generator = $this->getDataGenerator();

        // Create some users.
        $users = [];
        $users[] = $generator->create_user();
        $users[] = $generator->create_user();
        $users[] = $generator->create_user();

        // Create some courses.
        $courses = [];
        $courses[] = $generator->create_course();
        $courses[] = $generator->create_course();
        $courses[] = $generator->create_course();
        $courses[] = $generator->create_course();

        // Create some groups.
        $coursegroups = [];
        $coursegroups[$courses[0]->id] = [];
        $coursegroups[$courses[0]->id][] = $generator->create_group(['courseid' => $courses[0]->id]);
        $coursegroups[$courses[0]->id][] = $generator->create_group(['courseid' => $courses[0]->id]);
        $coursegroups[$courses[2]->id] = [];
        $coursegroups[$courses[2]->id][] = $generator->create_group(['courseid' => $courses[2]->id]);
        $coursegroups[$courses[2]->id][] = $generator->create_group(['courseid' => $courses[2]->id]);
        $coursegroups[$courses[3]->id] = [];
        $coursegroups[$courses[3]->id][] = $generator->create_group(['courseid' => $courses[3]->id]);
        $coursegroups[$courses[3]->id][] = $generator->create_group(['courseid' => $courses[3]->id]);

        // Create some enrolments and group memberships.
        $generator->enrol_user($users[0]->id, $courses[0]->id, 'student');
        $generator->create_group_member(['groupid' => $coursegroups[$courses[0]->id][0]->id, 'userid' => $users[0]->id]);
        $generator->enrol_user($users[1]->id, $courses[0]->id, 'student');
        $generator->create_group_member(['groupid' => $coursegroups[$courses[0]->id][1]->id, 'userid' => $users[1]->id]);
        $generator->enrol_user($users[0]->id, $courses[1]->id, 'student');
        $generator->enrol_user($users[0]->id, $courses[2]->id, 'student');

        return array($users, $courses, $coursegroups);
    }

    /**
     * This function tests calendar_set_filters for the case when user is not logged in.
     */
    public function test_calendar_set_filters_not_logged_in(): void {
        $this->resetAfterTest();

        list($users, $courses, $coursegroups) = $this->setup_test_calendar_set_filters();

        $defaultcourses = calendar_get_default_courses(null, '*', false, $users[0]->id);
        list($courseids, $groupids, $userid) = calendar_set_filters($defaultcourses);

        $this->assertEqualsCanonicalizing(
                [$courses[0]->id, $courses[1]->id, $courses[2]->id, SITEID],
                array_values($courseids));
        $this->assertFalse($groupids);
        $this->assertFalse($userid);
    }

    /**
     * This function tests calendar_set_filters for the case when no one is logged in, but a user id is provided.
     */
    public function test_calendar_set_filters_not_logged_in_with_user(): void {
        $this->resetAfterTest();

        list($users, $courses, $coursegroups) = $this->setup_test_calendar_set_filters();

        $defaultcourses = calendar_get_default_courses(null, '*', false, $users[1]->id);
        list($courseids, $groupids, $userid) = calendar_set_filters($defaultcourses, false, $users[1]);

        $this->assertEquals(array($courses[0]->id, SITEID), array_values($courseids));
        $this->assertEquals(array($coursegroups[$courses[0]->id][1]->id), $groupids);
        $this->assertEquals($users[1]->id, $userid);

        $defaultcourses = calendar_get_default_courses(null, '*', false, $users[0]->id);
        list($courseids, $groupids, $userid) = calendar_set_filters($defaultcourses, false, $users[0]);

        $this->assertEqualsCanonicalizing(
                [$courses[0]->id, $courses[1]->id, $courses[2]->id, SITEID],
                array_values($courseids));
        $this->assertEquals(array($coursegroups[$courses[0]->id][0]->id), $groupids);
        $this->assertEquals($users[0]->id, $userid);

    }

    /**
     * This function tests calendar_set_filters for the case when user is logged in, but no user id is provided.
     */
    public function test_calendar_set_filters_logged_in_no_user(): void {
        $this->resetAfterTest();

        list($users, $courses, $coursegroups) = $this->setup_test_calendar_set_filters();

        $this->setUser($users[0]);
        $defaultcourses = calendar_get_default_courses(null, '*', false, $users[0]->id);
        list($courseids, $groupids, $userid) = calendar_set_filters($defaultcourses, false);
        $this->assertEqualsCanonicalizing([$courses[0]->id, $courses[1]->id, $courses[2]->id, SITEID], array_values($courseids));
        $this->assertEquals(array($coursegroups[$courses[0]->id][0]->id), $groupids);
        $this->assertEquals($users[0]->id, $userid);
    }

    /**
     * This function tests calendar_set_filters for the case when a user is logged in, but another user id is provided.
     */
    public function test_calendar_set_filters_logged_in_another_user(): void {
        $this->resetAfterTest();

        list($users, $courses, $coursegroups) = $this->setup_test_calendar_set_filters();

        $this->setUser($users[0]);
        $defaultcourses = calendar_get_default_courses(null, '*', false, $users[1]->id);
        list($courseids, $groupids, $userid) = calendar_set_filters($defaultcourses, false, $users[1]);

        $this->assertEquals(array($courses[0]->id, SITEID), array_values($courseids));
        $this->assertEquals(array($coursegroups[$courses[0]->id][1]->id), $groupids);
        $this->assertEquals($users[1]->id, $userid);
    }

    /**
     * This function tests calendar_set_filters for courses with separate group mode.
     */
    public function test_calendar_set_filters_with_separate_group_mode(): void {
        global $DB;
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();

        // Create users.
        $student1 = $generator->create_user();
        $student2 = $generator->create_user();
        $teacher1 = $generator->create_user();
        $teacher2 = $generator->create_user();

        // Create courses.
        $course1 = $generator->create_course([
            'shortname' => 'C1',
            'groupmode' => 1,
            'groupmodeforce' => 1,
        ]);
        $course2 = $generator->create_course([
            'shortname' => 'C2',
            'groupmode' => 1,
            'groupmodeforce' => 1,
        ]);
        $course1context = \context_course::instance($course1->id);
        $course2context = \context_course::instance($course2->id);

        // Create groups.
        $group1 = $generator->create_group([
            'name' => 'G1-C1',
            'courseid' => $course1->id,
        ]);
        $group2 = $generator->create_group([
            'name' => 'G1-C2',
            'courseid' => $course2->id,
        ]);
        $group3 = $generator->create_group([
            'name' => 'G2-C2',
            'courseid' => $course2->id,
        ]);

        // Modify the capabilities.
        $editingteacherroleid = $DB->get_field('role', 'id', ['shortname' => 'editingteacher']);
        assign_capability(
            'moodle/site:accessallgroups',
            CAP_PREVENT,
            $editingteacherroleid,
            $course1context->id,
            true
        );
        assign_capability(
            'moodle/site:accessallgroups',
            CAP_PREVENT,
            $editingteacherroleid,
            $course2context->id,
            true
        );

        // Enrol users.
        $generator->enrol_user($student1->id, $course1->id, 'student');
        $generator->enrol_user($teacher1->id, $course1->id, 'editingteacher');
        $generator->enrol_user($student1->id, $course2->id, 'student');
        $generator->enrol_user($student2->id, $course2->id, 'student');
        $generator->enrol_user($teacher1->id, $course2->id, 'editingteacher');
        $generator->enrol_user($teacher2->id, $course2->id, 'editingteacher');

        // Group memberships.
        $generator->create_group_member([
            'groupid' => $group1->id,
            'userid' => $student1->id,
        ]);
        $generator->create_group_member([
            'groupid' => $group1->id,
            'userid' => $teacher1->id,
        ]);
        $generator->create_group_member([
            'groupid' => $group2->id,
            'userid' => $student1->id,
        ]);
        $generator->create_group_member([
            'groupid' => $group2->id,
            'userid' => $teacher1->id,
        ]);
        $generator->create_group_member([
            'groupid' => $group3->id,
            'userid' => $student2->id,
        ]);
        $generator->create_group_member([
            'groupid' => $group3->id,
            'userid' => $teacher2->id,
        ]);

        // Test teacher1.
        $this->setUser($teacher1);
        $defaultcourses = calendar_get_default_courses(
            null,
            '*',
            false,
            $teacher1->id
        );
        [$courseids, $groupids] = calendar_set_filters(
            $defaultcourses,
            false,
            $teacher1
        );
        // Teacher1 can see SITE, C1, G1-C1, C2, G1-C2.
        $this->assertCount(3, $courseids); // SITE, C1, C2.
        $this->assertCount(2, $groupids); // G1-C1, G1-C2.

        $courseidskey = array_fill_keys($courseids, null);
        $this->assertArrayHasKey(SITEID, $courseidskey);
        $this->assertArrayHasKey($course1->id, $courseidskey);
        $this->assertArrayHasKey($course2->id, $courseidskey);

        $groupidskey = array_fill_keys($groupids, null);
        $this->assertArrayHasKey($group1->id, $groupidskey);
        $this->assertArrayHasKey($group2->id, $groupidskey);
        $this->assertArrayNotHasKey($group3->id, $groupidskey);

        // Test teacher2.
        $this->setUser($teacher2);
        $defaultcourses = calendar_get_default_courses(
            null,
            '*',
            false,
            $teacher2->id
        );
        [$courseids, $groupids] = calendar_set_filters(
            $defaultcourses,
            false,
            $teacher2
        );
        // Teacher2 can see SITE, C2, G2-C2.
        $this->assertCount(2, $courseids); // SITE, C2.
        $this->assertCount(1, $groupids); // G2-C2.

        $courseidskey = array_fill_keys($courseids, null);
        $this->assertArrayHasKey(SITEID, $courseidskey);
        $this->assertArrayHasKey($course2->id, $courseidskey);

        $groupidskey = array_fill_keys($groupids, null);
        $this->assertArrayHasKey($group3->id, $groupidskey);
        $this->assertArrayNotHasKey($group1->id, $groupidskey);
        $this->assertArrayNotHasKey($group2->id, $groupidskey);

        // Modify the capabilities.
        assign_capability(
            'moodle/site:accessallgroups',
            CAP_ALLOW,
            $editingteacherroleid,
            $course2context->id,
            true
        );

        $defaultcourses = calendar_get_default_courses(
            null,
            '*',
            false,
            $teacher2->id
        );
        [$courseids, $groupids] = calendar_set_filters(
            $defaultcourses,
            false,
            $teacher2
        );
        // Teacher2 can see SITE, C2, G1-C2, G2-C2.
        $this->assertCount(2, $courseids); // SITE, C2.
        $this->assertCount(2, $groupids); // G1-C2, G2-C2.

        $groupidskey = array_fill_keys($groupids, null);
        $this->assertArrayHasKey($group2->id, $groupidskey);
        $this->assertArrayHasKey($group3->id, $groupidskey);
        $this->assertArrayNotHasKey($group1->id, $groupidskey);
    }

    /**
     *  Test for calendar_view_event_allowed for course event types.
     */
    public function test_calendar_view_event_allowed_course_event(): void {
        global $USER;

        $this->setAdminUser();

        $generator = $this->getDataGenerator();

        // A student in a course.
        $student = $generator->create_user();
        // Some user not enrolled in any course.
        $someuser = $generator->create_user();

        // A course with manual enrolments.
        $manualcourse = $generator->create_course();

        // Enrol the student to the manual enrolment course.
        $generator->enrol_user($student->id, $manualcourse->id);

        // A course that allows guest access.
        $guestcourse = $generator->create_course(
            (object)[
                'shortname' => 'guestcourse',
                'enrol_guest_status_0' => ENROL_INSTANCE_ENABLED,
                'enrol_guest_password_0' => ''
            ]);

        $manualevent = (object)[
            'name' => 'Manual course event',
            'description' => '',
            'format' => 1,
            'categoryid' => 0,
            'courseid' => $manualcourse->id,
            'groupid' => 0,
            'userid' => $USER->id,
            'modulename' => 0,
            'instance' => 0,
            'eventtype' => 'course',
            'timestart' => time(),
            'timeduration' => 86400,
            'visible' => 1
        ];
        $caleventmanual = \calendar_event::create($manualevent, false);

        // Create a course event for the course with guest access.
        $guestevent = clone $manualevent;
        $guestevent->name = 'Guest course event';
        $guestevent->courseid = $guestcourse->id;
        $caleventguest = \calendar_event::create($guestevent, false);

        // Viewing as admin.
        $this->assertTrue(calendar_view_event_allowed($caleventmanual));
        $this->assertTrue(calendar_view_event_allowed($caleventguest));

        // Viewing as someone enrolled in a course.
        $this->setUser($student);
        $this->assertTrue(calendar_view_event_allowed($caleventmanual));

        // Viewing as someone not enrolled in any course.
        $this->setUser($someuser);
        // Viewing as someone not enrolled in a course without guest access on.
        $this->assertFalse(calendar_view_event_allowed($caleventmanual));
        // Viewing as someone not enrolled in a course with guest access on.
        $this->assertTrue(calendar_view_event_allowed($caleventguest));
    }

    /**
     *  Test for calendar_get_export_token for current user.
     */
    public function test_calendar_get_export_token_for_current_user(): void {
        global $USER, $DB, $CFG;

        $this->setAdminUser();

        // Get my token.
        $authtoken = calendar_get_export_token($USER);
        $expected = sha1($USER->id . $DB->get_field('user', 'password', ['id' => $USER->id]) . $CFG->calendar_exportsalt);

        $this->assertEquals($expected, $authtoken);
    }

    /**
     *  Test for calendar_get_export_token for another user.
     */
    public function test_calendar_get_export_token_for_another_user(): void {
        global $CFG;

        // Get any user token.
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();

        // Get other user token.
        $authtoken = calendar_get_export_token($user);
        $expected = sha1($user->id . $user->password . $CFG->calendar_exportsalt);

        $this->assertEquals($expected, $authtoken);
    }

    /**
     *  Test calendar_can_manage_user_event for different users.
     *
     * @covers ::calendar_can_manage_user_event
     */
    public function test_calendar_can_manage_user_event(): void {
        global $DB, $USER;
        $generator = $this->getDataGenerator();
        $sitecontext = \context_system::instance();
        $this->resetAfterTest();
        $this->setAdminUser();
        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $adminevent = create_event([
            'eventtype' => 'user',
            'userid' => $USER->id,
        ]);

        $this->setUser($user1);
        $user1event = create_event([
            'name' => 'user1 event',
            'eventtype' => 'user',
            'userid' => $user1->id,
        ]);
        $this->setUser($user2);
        $user2event = create_event([
            'name' => 'user2 event',
            'eventtype' => 'user',
            'userid' => $user2->id,
        ]);
        $this->setUser($user1);
        $result = calendar_can_manage_user_event($user1event);
        $this->assertEquals(true, $result);
        $result = calendar_can_manage_user_event($user2event);
        $this->assertEquals(false, $result);

        $sitemanager = $generator->create_user();

        $managerroleid = $DB->get_field('role', 'id', ['shortname' => 'manager']);
        role_assign($managerroleid, $sitemanager->id, $sitecontext->id);

        $this->setUser($sitemanager);

        $result = calendar_can_manage_user_event($user1event);
        $this->assertEquals(true, $result);
        $result = calendar_can_manage_user_event($adminevent);
        $this->assertEquals(false, $result);
    }

    /**
     * Data provider for {@see test_calendar_format_event_location}
     *
     * @return array[]
     */
    public static function calendar_format_event_location_provider(): array {
        return [
            'Empty' => ['', ''],
            'Text' => ['Barcelona', 'Barcelona'],
            'Link (http)' => ['http://example.com', '<a title=".*" href="http://example.com">http://example.com</a>'],
            'Link (https)' => ['https://example.com', '<a title=".*" href="https://example.com">https://example.com</a>'],
        ];
    }

    /**
     * Test formatting event location
     *
     * @param string $location
     * @param string $expectedpattern
     *
     * @covers ::calendar_format_event_location
     * @dataProvider calendar_format_event_location_provider
     */
    public function test_calendar_format_event_location(string $location, string $expectedpattern): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $event = create_event(['location' => $location]);
        $this->assertMatchesRegularExpression("|^({$expectedpattern})$|", calendar_format_event_location($event));
    }
}
