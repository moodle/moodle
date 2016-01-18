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
 * External calendar functions unit tests
 *
 * @package    core_calendar
 * @category   external
 * @copyright  2012 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * External course functions unit tests
 *
 * @package    core_calendar
 * @category   external
 * @copyright  2012 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.5
 */
class core_calendar_externallib_testcase extends externallib_advanced_testcase {

    /**
     * Tests set up
     */
    protected function setUp() {
        global $CFG;
        require_once($CFG->dirroot . '/calendar/externallib.php');
    }

    /** Create calendar events or update them
     * Set $prop->id, if you want to do an update instead of creating an new event
     *
     * @param string $name        Event title
     * @param int    $userid      User id
     * @param string $type        Event type
     * @param int    $repeats     Number of repeated events to create
     * @param int    $timestart   Time stamp of the event start
     * @param mixed  $prop        List of event properties as array or object
     * @return mixed              Event object or false;
     * @since Moodle 2.5
     */

    public static function create_calendar_event($name, $userid = 0, $type = 'user', $repeats = 0, $timestart  = null, $prop = null) {
        global $CFG, $DB, $USER, $SITE;

        require_once("$CFG->dirroot/calendar/lib.php");
        if (!empty($prop)) {
            if (is_array($prop)) {
                $prop = (object)$prop;
            }
        } else {
            $prop = new stdClass();
        }
        $prop->name = $name;
        if (empty($prop->eventtype)) {
            $prop->eventtype = $type;
        }
        if (empty($prop->repeats)) {
            $prop->repeats = $repeats;
        }
        if (empty($prop->timestart)) {
            $prop->timestart = time();
        }
        if (empty($prop->timeduration)) {
            $prop->timeduration = 0;
        }
        if (empty($prop->repeats)) {
            $prop->repeat = 0;
        } else {
            $prop->repeat = 1;
        }
        if (empty($prop->userid)) {
            if (!empty($userid)) {
                $prop->userid = $userid;
            } else {
                return false;
            }
        }
        if (!isset($prop->courseid)) {
            $prop->courseid = $SITE->id;
        }
        $event = new calendar_event($prop);
        return $event->create($prop);
    }

    public function test_create_calendar_events () {
        global $DB, $USER;

        $this->setAdminUser();
        $this->resetAfterTest();
        $prevcount = count($DB->get_records("event"));

        // Create a few events and do asserts.
        $this->create_calendar_event('test', $USER->id);
        $where = $DB->sql_compare_text('name') ." = ?";
        $count = count($DB->get_records_select("event", $where, array('test')));
        $this->assertEquals(1, $count);
        $aftercount = count($DB->get_records("event"));
        $this->assertEquals($prevcount + 1, $aftercount);

        $this->create_calendar_event('user', $USER->id, 'user', 3);
        $where = $DB->sql_compare_text('name') ." = ?";
        $count = count($DB->get_records_select("event", $where, array('user')));

        $this->assertEquals(3, $count);
        $aftercount = count($DB->get_records("event"));
        $this->assertEquals($prevcount + 4, $aftercount);

    }

    /**
     * Test delete_calendar_events
     */
    public function test_delete_calendar_events() {
        global $DB, $USER;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Create a few stuff to test with.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $record = new stdClass();
        $record->courseid = $course->id;
        $group = $this->getDataGenerator()->create_group($record);

        $notdeletedcount = $DB->count_records('event');

        // Let's create a few events.
        $siteevent = $this->create_calendar_event('site', $USER->id, 'site');
        $record = new stdClass();
        $record->courseid = $course->id;
        $courseevent = $this->create_calendar_event('course', $USER->id, 'course', 2, time(), $record);
        $userevent = $this->create_calendar_event('user', $USER->id);
        $record = new stdClass();
        $record->courseid = $course->id;
        $record->groupid = $group->id;
        $groupevent = $this->create_calendar_event('group', $USER->id, 'group', 0, time(), $record);

        // Now lets try to delete stuff with proper rights.
        $events = array(
                array('eventid' => $siteevent->id, 'repeat' => 0),
                array('eventid' => $courseevent->id, 'repeat' => 1),
                array('eventid' => $userevent->id, 'repeat' => 0),
                array('eventid' => $groupevent->id, 'repeat' => 0)
                );
        core_calendar_external::delete_calendar_events($events);

        // Check to see if things were deleted properly.
        $deletedcount = $DB->count_records('event');
        $this->assertEquals($notdeletedcount, $deletedcount);

        // Let's create a few events.
        $siteevent = $this->create_calendar_event('site', $USER->id, 'site');
        $record = new stdClass();
        $record->courseid = $course->id;
        $courseevent = $this->create_calendar_event('course', $USER->id, 'course', 3, time(), $record);
        $userevent = $this->create_calendar_event('user', $USER->id);
        $record = new stdClass();
        $record->courseid = $course->id;
        $record->groupid = $group->id;
        $groupevent = $this->create_calendar_event('group', $USER->id, 'group', 0, time(), $record);

        $this->setuser($user);
        $sitecontext = context_system::instance();
        $coursecontext = context_course::instance($course->id);
        $usercontext = context_user::instance($user->id);
        $role = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $role->id);

        // Remove all caps.
        $this->unassignUserCapability('moodle/calendar:manageentries', $sitecontext->id, $role->id);
        $this->unassignUserCapability('moodle/calendar:manageentries', $coursecontext->id, $role->id);
        $this->unassignUserCapability('moodle/calendar:managegroupentries', $coursecontext->id, $role->id);
        $this->unassignUserCapability('moodle/calendar:manageownentries', $usercontext->id, $role->id);

        // Assign proper caps and attempt delete.
         $this->assignUserCapability('moodle/calendar:manageentries', $sitecontext->id, $role->id);
         $events = array(
                array('eventid' => $siteevent->id, 'repeat' => 0),
                );
        core_calendar_external::delete_calendar_events($events);
        $deletedcount = $DB->count_records('event');
        $count = $notdeletedcount+5;
        $this->assertEquals($count, $deletedcount);

         $this->assignUserCapability('moodle/calendar:manageentries', $sitecontext->id, $role->id);
         $events = array(
                array('eventid' => $courseevent->id, 'repeat' => 0),
                );
        core_calendar_external::delete_calendar_events($events);
        $deletedcount = $DB->count_records('event');
        $count = $notdeletedcount+4;
        $this->assertEquals($count, $deletedcount);

         $this->assignUserCapability('moodle/calendar:manageownentries', $usercontext->id, $role->id);
         $events = array(
                array('eventid' => $userevent->id, 'repeat' => 0),
                );
        core_calendar_external::delete_calendar_events($events);
        $deletedcount = $DB->count_records('event');
        $count = $notdeletedcount+3;
        $this->assertEquals($count, $deletedcount);

         $this->assignUserCapability('moodle/calendar:managegroupentries', $coursecontext->id, $role->id);
         $events = array(
                array('eventid' => $groupevent->id, 'repeat' => 0),
                );
        core_calendar_external::delete_calendar_events($events);
        $deletedcount = $DB->count_records('event');
        $count = $notdeletedcount+2;
        $this->assertEquals($count, $deletedcount);

        $notdeletedcount = $deletedcount;

        // Let us try deleting without caps.

        $siteevent = $this->create_calendar_event('site', $USER->id, 'site');
        $record = new stdClass();
        $record->courseid = $course->id;
        $courseevent = $this->create_calendar_event('course', $USER->id, 'course', 3, time(), $record);
        $userevent = $this->create_calendar_event('user', $USER->id);
        $record = new stdClass();
        $record->courseid = $course->id;
        $record->groupid = $group->id;
        $groupevent = $this->create_calendar_event('group', $USER->id, 'group', 0, time(), $record);

        $this->setGuestUser();
        $this->setExpectedException('moodle_exception');
        $events = array(
            array('eventid' => $siteevent->id, 'repeat' => 0),
            array('eventid' => $courseevent->id, 'repeat' => 0),
            array('eventid' => $userevent->id, 'repeat' => 0),
            array('eventid' => $groupevent->id, 'repeat' => 0)
        );
        core_calendar_external::delete_calendar_events($events);
    }

    /**
     * Test get_calendar_events
     */
    public function test_get_calendar_events() {
        global $DB, $USER;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Create a few stuff to test with.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $record = new stdClass();
        $record->courseid = $course->id;
        $group = $this->getDataGenerator()->create_group($record);

        $beforecount = $DB->count_records('event');

        // Let's create a few events.
        $siteevent = $this->create_calendar_event('site', $USER->id, 'site');
        $record = new stdClass();
        $record->courseid = $course->id;
        $courseevent = $this->create_calendar_event('course', $USER->id, 'course', 2, time(), $record);
        $userevent = $this->create_calendar_event('user', $USER->id);
        $record = new stdClass();
        $record->courseid = $course->id;
        $record->groupid = $group->id;
        $groupevent = $this->create_calendar_event('group', $USER->id, 'group', 0, time(), $record);

        $paramevents = array ('eventids' => array($siteevent->id), 'courseids' => array($course->id), 'groupids' => array($group->id));
        $options = array ('siteevents' => true, 'userevents' => true);
        $events = core_calendar_external::get_calendar_events($paramevents, $options);
        $events = external_api::clean_returnvalue(core_calendar_external::get_calendar_events_returns(), $events);

        // Check to see if we got all events.
        $this->assertEquals(5, count($events['events']));
        $this->assertEquals(0, count($events['warnings']));
        $options = array ('siteevents' => true, 'userevents' => true, 'timeend' => time() + 7*WEEKSECS);
        $events = core_calendar_external::get_calendar_events($paramevents, $options);
        $events = external_api::clean_returnvalue(core_calendar_external::get_calendar_events_returns(), $events);
        $this->assertEquals(5, count($events['events']));
        $this->assertEquals(0, count($events['warnings']));

        // Let's play around with caps.
        $this->setUser($user);
        $events = core_calendar_external::get_calendar_events($paramevents, $options);
        $events = external_api::clean_returnvalue(core_calendar_external::get_calendar_events_returns(), $events);
        $this->assertEquals(2, count($events['events'])); // site, user.
        $this->assertEquals(2, count($events['warnings'])); // course, group.

        $role = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $role->id);
        $events = core_calendar_external::get_calendar_events($paramevents, $options);
        $events = external_api::clean_returnvalue(core_calendar_external::get_calendar_events_returns(), $events);
        $this->assertEquals(4, count($events['events'])); // site, user, both course events.
        $this->assertEquals(1, count($events['warnings'])); // group.

        $options = array ('siteevents' => true, 'userevents' => true, 'timeend' => time() + HOURSECS);
        $events = core_calendar_external::get_calendar_events($paramevents, $options);
        $events = external_api::clean_returnvalue(core_calendar_external::get_calendar_events_returns(), $events);
        $this->assertEquals(3, count($events['events'])); // site, user, one course event.
        $this->assertEquals(1, count($events['warnings'])); // group.

        groups_add_member($group, $user);
        $events = core_calendar_external::get_calendar_events($paramevents, $options);
        $events = external_api::clean_returnvalue(core_calendar_external::get_calendar_events_returns(), $events);
        $this->assertEquals(4, count($events['events'])); // site, user, group, one course event.
        $this->assertEquals(0, count($events['warnings']));

        $paramevents = array ('courseids' => array($course->id), 'groupids' => array($group->id));
        $events = core_calendar_external::get_calendar_events($paramevents, $options);
        $events = external_api::clean_returnvalue(core_calendar_external::get_calendar_events_returns(), $events);
        $this->assertEquals(4, count($events['events'])); // site, user, group, one course event.
        $this->assertEquals(0, count($events['warnings']));

        $paramevents = array ('groupids' => array($group->id, 23));
        $events = core_calendar_external::get_calendar_events($paramevents, $options);
        $events = external_api::clean_returnvalue(core_calendar_external::get_calendar_events_returns(), $events);
        $this->assertEquals(3, count($events['events'])); // site, user, group.
        $this->assertEquals(1, count($events['warnings']));

        $paramevents = array ('courseids' => array(23));
        $events = core_calendar_external::get_calendar_events($paramevents, $options);
        $events = external_api::clean_returnvalue(core_calendar_external::get_calendar_events_returns(), $events);
        $this->assertEquals(2, count($events['events'])); // site, user.
        $this->assertEquals(1, count($events['warnings']));

        $paramevents = array ();
        $options = array ('siteevents' => false, 'userevents' => false, 'timeend' => time() + 7*WEEKSECS);
        $events = core_calendar_external::get_calendar_events($paramevents, $options);
        $events = external_api::clean_returnvalue(core_calendar_external::get_calendar_events_returns(), $events);
        $this->assertEquals(0, count($events['events'])); // nothing returned.
        $this->assertEquals(0, count($events['warnings']));

        $paramevents = array ('eventids' => array($siteevent->id, $groupevent->id));
        $options = array ('siteevents' => false, 'userevents' => false, 'timeend' => time() + 7*WEEKSECS);
        $events = core_calendar_external::get_calendar_events($paramevents, $options);
        $events = external_api::clean_returnvalue(core_calendar_external::get_calendar_events_returns(), $events);
        $this->assertEquals(2, count($events['events'])); // site, group.
        $this->assertEquals(0, count($events['warnings']));

        $paramevents = array ('eventids' => array($siteevent->id));
        $events = core_calendar_external::get_calendar_events($paramevents, $options);
        $events = external_api::clean_returnvalue(core_calendar_external::get_calendar_events_returns(), $events);
        $this->assertEquals(1, count($events['events'])); // site.
        $this->assertEquals(0, count($events['warnings']));

        // Try getting a course event by its id.
        $paramevents = array ('eventids' => array($courseevent->id));
        $events = core_calendar_external::get_calendar_events($paramevents, $options);
        $events = external_api::clean_returnvalue(core_calendar_external::get_calendar_events_returns(), $events);
        $this->assertEquals(1, count($events['events']));
        $this->assertEquals(0, count($events['warnings']));

        // Now, create an activity event.
        $this->setAdminUser();
        $nexttime = time() + DAYSECS;
        $assign = $this->getDataGenerator()->create_module('assign', array('course' => $course->id, 'duedate' => $nexttime));

        $this->setUser($user);
        $paramevents = array ('courseids' => array($course->id));
        $options = array ('siteevents' => true, 'userevents' => true, 'timeend' => time() + WEEKSECS);
        $events = core_calendar_external::get_calendar_events($paramevents, $options);
        $events = external_api::clean_returnvalue(core_calendar_external::get_calendar_events_returns(), $events);

        $this->assertCount(5, $events['events']);

        // Hide the assignment.
        set_coursemodule_visible($assign->cmid, 0);
        // Empty all the caches that may be affected  by this change.
        accesslib_clear_all_caches_for_unit_testing();
        course_modinfo::clear_instance_cache();

        $events = core_calendar_external::get_calendar_events($paramevents, $options);
        $events = external_api::clean_returnvalue(core_calendar_external::get_calendar_events_returns(), $events);
        // Expect one less.
        $this->assertCount(4, $events['events']);
    }

    /**
     * Test core_calendar_external::create_calendar_events
     */
    public function test_core_create_calendar_events() {
        global $DB, $USER, $SITE;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Create a few stuff to test with.
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();
        $record = new stdClass();
        $record->courseid = $course->id;
        $group = $this->getDataGenerator()->create_group($record);

        $prevcount = $DB->count_records('event');

        // Let's create a few events.
        $events = array (
                array('name' => 'site', 'courseid' => $SITE->id, 'eventtype' => 'site'),
                array('name' => 'course', 'courseid' => $course->id, 'eventtype' => 'course', 'repeats' => 2),
                array('name' => 'group', 'courseid' => $course->id, 'groupid' => $group->id, 'eventtype' => 'group'),
                array('name' => 'user')
                );
        $eventsret = core_calendar_external::create_calendar_events($events);
        $eventsret = external_api::clean_returnvalue(core_calendar_external::create_calendar_events_returns(), $eventsret);

        // Check to see if things were created properly.
        $aftercount = $DB->count_records('event');
        $this->assertEquals($prevcount + 5, $aftercount);
        $this->assertEquals(5, count($eventsret['events']));
        $this->assertEquals(0, count($eventsret['warnings']));

        $sitecontext = context_system::instance();
        $coursecontext = context_course::instance($course->id);

        $this->setUser($user);
        $prevcount = $aftercount;
        $events = array (
                array('name' => 'course', 'courseid' => $course->id, 'eventtype' => 'course', 'repeats' => 2),
                array('name' => 'group', 'courseid' => $course->id, 'groupid' => $group->id, 'eventtype' => 'group'),
                array('name' => 'user')
        );
        $role = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $role->id);
        groups_add_member($group, $user);
        $this->assignUserCapability('moodle/calendar:manageentries', $coursecontext->id, $role->id);
        $this->assignUserCapability('moodle/calendar:managegroupentries', $coursecontext->id, $role->id);
        $eventsret = core_calendar_external::create_calendar_events($events);
        $eventsret = external_api::clean_returnvalue(core_calendar_external::create_calendar_events_returns(), $eventsret);
        // Check to see if things were created properly.
        $aftercount = $DB->count_records('event');
        $this->assertEquals($prevcount + 4, $aftercount);
        $this->assertEquals(4, count($eventsret['events']));
        $this->assertEquals(0, count($eventsret['warnings']));

        // Check to see nothing was created without proper permission.
        $this->setGuestUser();
        $prevcount = $DB->count_records('event');
        $eventsret = core_calendar_external::create_calendar_events($events);
        $eventsret = external_api::clean_returnvalue(core_calendar_external::create_calendar_events_returns(), $eventsret);
        $aftercount = $DB->count_records('event');
        $this->assertEquals($prevcount, $aftercount);
        $this->assertEquals(0, count($eventsret['events']));
        $this->assertEquals(3, count($eventsret['warnings']));

        $this->setUser($user);
        $this->unassignUserCapability('moodle/calendar:manageentries', $coursecontext->id, $role->id);
        $this->unassignUserCapability('moodle/calendar:managegroupentries', $coursecontext->id, $role->id);
        $prevcount = $DB->count_records('event');
        $eventsret = core_calendar_external::create_calendar_events($events);
        $eventsret = external_api::clean_returnvalue(core_calendar_external::create_calendar_events_returns(), $eventsret);
        $aftercount = $DB->count_records('event');
        $this->assertEquals($prevcount + 1, $aftercount); // User event.
        $this->assertEquals(1, count($eventsret['events']));
        $this->assertEquals(2, count($eventsret['warnings']));
    }
}
