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
class core_calendar_external_testcase extends externallib_advanced_testcase {

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
        if (empty($prop->courseid)) {
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
        $count = count($DB->get_records("event", array('name' => 'test')));
        $this->assertEquals(1, $count);
        $aftercount = count($DB->get_records("event"));
        $this->assertEquals($prevcount + 1, $aftercount);

        $this->create_calendar_event('user', $USER->id, 'user', 3);
        $count = count($DB->get_records("event", array('name' => 'user')));
        $this->assertEquals(3, $count);
        $aftercount = count($DB->get_records("event"));
        $this->assertEquals($prevcount + 4, $aftercount);

    }

    /**
     * Test delete_courses
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

}