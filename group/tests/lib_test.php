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
 * Unit tests for group lib.
 *
 * @package    core_group
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_group;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/group/lib.php');

/**
 * Group lib testcase.
 *
 * @package    core_group
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lib_test extends \advanced_testcase {

    public function test_member_added_event() {
        $this->resetAfterTest();

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $group = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $sink = $this->redirectEvents();
        groups_add_member($group->id, $user->id, 'mod_workshop', '123');
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        $expected = new \stdClass();
        $expected->groupid = $group->id;
        $expected->userid  = $user->id;
        $expected->component = 'mod_workshop';
        $expected->itemid = '123';
        $this->assertEventLegacyData($expected, $event);
        $this->assertSame('groups_member_added', $event->get_legacy_eventname());
        $this->assertInstanceOf('\core\event\group_member_added', $event);
        $this->assertEquals($user->id, $event->relateduserid);
        $this->assertEquals(\context_course::instance($course->id), $event->get_context());
        $this->assertEquals($group->id, $event->objectid);
        $url = new \moodle_url('/group/members.php', array('group' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
    }

    public function test_member_removed_event() {
        $this->resetAfterTest();

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_user();
        $group = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $this->getDataGenerator()->enrol_user($user->id, $course->id);
        $this->getDataGenerator()->create_group_member(array('userid' => $user->id, 'groupid' => $group->id));

        $sink = $this->redirectEvents();
        groups_remove_member($group->id, $user->id);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        $expected = new \stdClass();
        $expected->groupid = $group->id;
        $expected->userid  = $user->id;
        $this->assertEventLegacyData($expected, $event);
        $this->assertSame('groups_member_removed', $event->get_legacy_eventname());
        $this->assertInstanceOf('\core\event\group_member_removed', $event);
        $this->assertEquals($user->id, $event->relateduserid);
        $this->assertEquals(\context_course::instance($course->id), $event->get_context());
        $this->assertEquals($group->id, $event->objectid);
        $url = new \moodle_url('/group/members.php', array('group' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
    }

    public function test_group_created_event() {
        $this->resetAfterTest();

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();

        $sink = $this->redirectEvents();
        $group = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        $this->assertInstanceOf('\core\event\group_created', $event);
        $this->assertEventLegacyData($group, $event);
        $this->assertSame('groups_group_created', $event->get_legacy_eventname());
        $this->assertEquals(\context_course::instance($course->id), $event->get_context());
        $this->assertEquals($group->id, $event->objectid);
        $url = new \moodle_url('/group/index.php', array('id' => $event->courseid));
        $this->assertEquals($url, $event->get_url());
    }

    public function test_grouping_created_event() {
        $this->resetAfterTest();

        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();

        $sink = $this->redirectEvents();
        $group = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        $this->assertInstanceOf('\core\event\grouping_created', $event);

        $this->assertEventLegacyData($group, $event);
        $this->assertSame('groups_grouping_created', $event->get_legacy_eventname());

        $this->assertEquals(\context_course::instance($course->id), $event->get_context());
        $this->assertEquals($group->id, $event->objectid);
        $url = new \moodle_url('/group/groupings.php', array('id' => $event->courseid));
        $this->assertEquals($url, $event->get_url());
    }

    public function test_group_updated_event() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $group = $this->getDataGenerator()->create_group(array('courseid' => $course->id));

        $sink = $this->redirectEvents();
        $data = new \stdClass();
        $data->id = $group->id;
        $data->courseid = $course->id;
        $data->name = 'Backend team';
        $this->setCurrentTimeStart();
        groups_update_group($data);
        $group = $DB->get_record('groups', array('id'=>$group->id)); // Fetch record with modified timestamp.
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertTimeCurrent($group->timemodified);

        $this->assertInstanceOf('\core\event\group_updated', $event);
        $group->name = $data->name;
        $this->assertEventLegacyData($group, $event);
        $this->assertSame('groups_group_updated', $event->get_legacy_eventname());
        $this->assertEquals(\context_course::instance($course->id), $event->get_context());
        $this->assertEquals($group->id, $event->objectid);
        $url = new \moodle_url('/group/group.php', array('id' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
    }

    public function test_group_updated_event_does_not_require_names() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $group = $this->getDataGenerator()->create_group(array('courseid' => $course->id));

        $sink = $this->redirectEvents();
        $data = new \stdClass();
        $data->id = $group->id;
        $data->courseid = $course->id;
        $this->setCurrentTimeStart();
        groups_update_group($data);
        $group = $DB->get_record('groups', array('id'=>$group->id)); // Fetch record with modified timestamp.
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);
        $this->assertTimeCurrent($group->timemodified);

        $this->assertInstanceOf('\core\event\group_updated', $event);
        $this->assertEventLegacyData($group, $event);
        $this->assertSame('groups_group_updated', $event->get_legacy_eventname());
        $this->assertEquals(\context_course::instance($course->id), $event->get_context());
        $this->assertEquals($group->id, $event->objectid);
        $url = new \moodle_url('/group/group.php', array('id' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
    }

    public function test_grouping_updated_event() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $grouping = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));

        $sink = $this->redirectEvents();
        $data = new \stdClass();
        $data->id = $grouping->id;
        $data->courseid = $course->id;
        $data->name = 'Backend team';
        $this->setCurrentTimeStart();
        groups_update_grouping($data);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        $this->assertInstanceOf('\core\event\grouping_updated', $event);

        // Get the timemodified from DB for comparison with snapshot.
        $data->timemodified = $DB->get_field('groupings', 'timemodified', array('id'=>$grouping->id));
        $this->assertTimeCurrent($data->timemodified);
        // Following fields were not updated so the snapshot should have them the same as in original group.
        $data->description = $grouping->description;
        $data->descriptionformat = $grouping->descriptionformat;
        $data->configdata = $grouping->configdata;
        $data->idnumber = $grouping->idnumber;
        $data->timecreated = $grouping->timecreated;
        // Assert legacy event data.
        $this->assertEventLegacyData($data, $event);
        $this->assertSame('groups_grouping_updated', $event->get_legacy_eventname());

        $this->assertEquals(\context_course::instance($course->id), $event->get_context());
        $this->assertEquals($grouping->id, $event->objectid);
        $url = new \moodle_url('/group/grouping.php', array('id' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
    }

    public function test_grouping_updated_event_does_not_require_names() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $grouping = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));

        $sink = $this->redirectEvents();
        $data = new \stdClass();
        $data->id = $grouping->id;
        $data->courseid = $course->id;
        $this->setCurrentTimeStart();
        groups_update_grouping($data);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        $this->assertInstanceOf('\core\event\grouping_updated', $event);

        // Get the timemodified from DB for comparison with snapshot.
        $data->timemodified = $DB->get_field('groupings', 'timemodified', array('id'=>$grouping->id));
        $this->assertTimeCurrent($data->timemodified);
        // Following fields were not updated so the snapshot should have them the same as in original group.
        $data->description = $grouping->description;
        $data->descriptionformat = $grouping->descriptionformat;
        $data->configdata = $grouping->configdata;
        $data->idnumber = $grouping->idnumber;
        $data->name = $grouping->name;
        $data->timecreated = $grouping->timecreated;
        // Assert legacy event data.
        $this->assertEventLegacyData($data, $event);
        $this->assertSame('groups_grouping_updated', $event->get_legacy_eventname());

        $this->assertEquals(\context_course::instance($course->id), $event->get_context());
        $this->assertEquals($grouping->id, $event->objectid);
        $url = new \moodle_url('/group/grouping.php', array('id' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
    }

    public function test_group_deleted_event() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $group = $this->getDataGenerator()->create_group(array('courseid' => $course->id));

        $sink = $this->redirectEvents();
        groups_delete_group($group->id);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        $this->assertInstanceOf('\core\event\group_deleted', $event);
        $this->assertEventLegacyData($group, $event);
        $this->assertSame('groups_group_deleted', $event->get_legacy_eventname());
        $this->assertEquals(\context_course::instance($course->id), $event->get_context());
        $this->assertEquals($group->id, $event->objectid);
        $url = new \moodle_url('/group/index.php', array('id' => $event->courseid));
        $this->assertEquals($url, $event->get_url());
    }

    public function test_grouping_deleted_event() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $group = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));

        $sink = $this->redirectEvents();
        groups_delete_grouping($group->id);
        $events = $sink->get_events();
        $this->assertCount(1, $events);
        $event = reset($events);

        $this->assertInstanceOf('\core\event\grouping_deleted', $event);
        $this->assertEventLegacyData($group, $event);
        $this->assertSame('groups_grouping_deleted', $event->get_legacy_eventname());
        $this->assertEquals(\context_course::instance($course->id), $event->get_context());
        $this->assertEquals($group->id, $event->objectid);
        $url = new \moodle_url('/group/groupings.php', array('id' => $event->courseid));
        $this->assertEquals($url, $event->get_url());
    }

    public function test_groups_delete_group_members() {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id);
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));

        // Test deletion of all the users.
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user2->id));

        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $group1->id, 'userid' => $user1->id)));
        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $group2->id, 'userid' => $user1->id)));
        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $group1->id, 'userid' => $user2->id)));
        groups_delete_group_members($course->id);
        $this->assertFalse($DB->record_exists('groups_members', array('groupid' => $group1->id, 'userid' => $user1->id)));
        $this->assertFalse($DB->record_exists('groups_members', array('groupid' => $group2->id, 'userid' => $user1->id)));
        $this->assertFalse($DB->record_exists('groups_members', array('groupid' => $group1->id, 'userid' => $user2->id)));

        // Test deletion of a specific user.
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user2->id));

        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $group1->id, 'userid' => $user1->id)));
        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $group2->id, 'userid' => $user1->id)));
        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $group1->id, 'userid' => $user2->id)));
        groups_delete_group_members($course->id, $user2->id);
        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $group1->id, 'userid' => $user1->id)));
        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $group2->id, 'userid' => $user1->id)));
        $this->assertFalse($DB->record_exists('groups_members', array('groupid' => $group1->id, 'userid' => $user2->id)));
    }

    public function test_groups_remove_member() {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id);
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));

        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user2->id));

        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $group1->id, 'userid' => $user1->id)));
        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $group2->id, 'userid' => $user1->id)));
        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $group1->id, 'userid' => $user2->id)));
        groups_remove_member($group1->id, $user1->id);
        $this->assertFalse($DB->record_exists('groups_members', array('groupid' => $group1->id, 'userid' => $user1->id)));
        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $group2->id, 'userid' => $user1->id)));
        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $group1->id, 'userid' => $user2->id)));
        groups_remove_member($group1->id, $user2->id);
        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $group2->id, 'userid' => $user1->id)));
        $this->assertFalse($DB->record_exists('groups_members', array('groupid' => $group1->id, 'userid' => $user2->id)));
        groups_remove_member($group2->id, $user1->id);
        $this->assertFalse($DB->record_exists('groups_members', array('groupid' => $group2->id, 'userid' => $user1->id)));
    }

    public function test_groups_delete_groupings_groups() {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group1c2 = $this->getDataGenerator()->create_group(array('courseid' => $course2->id));
        $grouping1 = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));
        $grouping2 = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));
        $grouping1c2 = $this->getDataGenerator()->create_grouping(array('courseid' => $course2->id));

        $this->getDataGenerator()->create_grouping_group(array('groupingid' => $grouping1->id, 'groupid' => $group1->id));
        $this->getDataGenerator()->create_grouping_group(array('groupingid' => $grouping1->id, 'groupid' => $group2->id));
        $this->getDataGenerator()->create_grouping_group(array('groupingid' => $grouping2->id, 'groupid' => $group1->id));
        $this->getDataGenerator()->create_grouping_group(array('groupingid' => $grouping1c2->id, 'groupid' => $group1c2->id));
        $this->assertTrue($DB->record_exists('groupings_groups', array('groupid' => $group1->id, 'groupingid' => $grouping1->id)));
        $this->assertTrue($DB->record_exists('groupings_groups', array('groupid' => $group2->id, 'groupingid' => $grouping1->id)));
        $this->assertTrue($DB->record_exists('groupings_groups', array('groupid' => $group1->id, 'groupingid' => $grouping2->id)));
        $this->assertTrue($DB->record_exists('groupings_groups',
            array('groupid' => $group1c2->id, 'groupingid' => $grouping1c2->id)));
        groups_delete_groupings_groups($course->id);
        $this->assertFalse($DB->record_exists('groupings_groups', array('groupid' => $group1->id, 'groupingid' => $grouping1->id)));
        $this->assertFalse($DB->record_exists('groupings_groups', array('groupid' => $group2->id, 'groupingid' => $grouping1->id)));
        $this->assertFalse($DB->record_exists('groupings_groups', array('groupid' => $group1->id, 'groupingid' => $grouping2->id)));
        $this->assertTrue($DB->record_exists('groupings_groups',
            array('groupid' => $group1c2->id, 'groupingid' => $grouping1c2->id)));
    }

    public function test_groups_delete_groups() {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group1c2 = $this->getDataGenerator()->create_group(array('courseid' => $course2->id));
        $grouping1 = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));
        $grouping2 = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));
        $user1 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_grouping_group(array('groupid' => $group1->id, 'groupingid' => $grouping1->id));

        $this->assertTrue($DB->record_exists('groups', array('id' => $group1->id, 'courseid' => $course->id)));
        $this->assertTrue($DB->record_exists('groups', array('id' => $group2->id, 'courseid' => $course->id)));
        $this->assertTrue($DB->record_exists('groups', array('id' => $group1c2->id, 'courseid' => $course2->id)));
        $this->assertTrue($DB->record_exists('groups_members', array('groupid' => $group1->id, 'userid' => $user1->id)));
        $this->assertTrue($DB->record_exists('groupings', array('id' => $grouping1->id, 'courseid' => $course->id)));
        $this->assertTrue($DB->record_exists('groupings_groups', array('groupid' => $group1->id, 'groupingid' => $grouping1->id)));
        groups_delete_groups($course->id);
        $this->assertFalse($DB->record_exists('groups', array('id' => $group1->id, 'courseid' => $course->id)));
        $this->assertFalse($DB->record_exists('groups', array('id' => $group2->id, 'courseid' => $course->id)));
        $this->assertTrue($DB->record_exists('groups', array('id' => $group1c2->id, 'courseid' => $course2->id)));
        $this->assertFalse($DB->record_exists('groups_members', array('groupid' => $group1->id, 'userid' => $user1->id)));
        $this->assertTrue($DB->record_exists('groupings', array('id' => $grouping1->id, 'courseid' => $course->id)));
        $this->assertFalse($DB->record_exists('groupings_groups', array('groupid' => $group1->id, 'groupingid' => $grouping1->id)));
    }

    public function test_groups_delete_groupings() {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $grouping1 = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));
        $grouping2 = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));
        $grouping1c2 = $this->getDataGenerator()->create_grouping(array('courseid' => $course2->id));
        $this->getDataGenerator()->create_grouping_group(array('groupid' => $group1->id, 'groupingid' => $grouping1->id));

        $this->assertTrue($DB->record_exists('groups', array('id' => $group1->id, 'courseid' => $course->id)));
        $this->assertTrue($DB->record_exists('groupings', array('id' => $grouping1->id, 'courseid' => $course->id)));
        $this->assertTrue($DB->record_exists('groupings', array('id' => $grouping2->id, 'courseid' => $course->id)));
        $this->assertTrue($DB->record_exists('groupings', array('id' => $grouping1c2->id, 'courseid' => $course2->id)));
        $this->assertTrue($DB->record_exists('groupings_groups', array('groupid' => $group1->id, 'groupingid' => $grouping1->id)));
        groups_delete_groupings($course->id);
        $this->assertTrue($DB->record_exists('groups', array('id' => $group1->id, 'courseid' => $course->id)));
        $this->assertFalse($DB->record_exists('groupings', array('id' => $grouping1->id, 'courseid' => $course->id)));
        $this->assertFalse($DB->record_exists('groupings', array('id' => $grouping2->id, 'courseid' => $course->id)));
        $this->assertTrue($DB->record_exists('groupings', array('id' => $grouping1c2->id, 'courseid' => $course2->id)));
        $this->assertFalse($DB->record_exists('groupings_groups', array('groupid' => $group1->id, 'groupingid' => $grouping1->id)));
    }

    public function test_groups_create_autogroups () {
        global $DB;
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group3 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $grouping1 = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));
        $this->getDataGenerator()->create_grouping_group(array('groupid' => $group2->id, 'groupingid' => $grouping1->id));
        $this->getDataGenerator()->create_grouping_group(array('groupid' => $group3->id, 'groupingid' => $grouping1->id));
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course->id);
        $this->getDataGenerator()->enrol_user($user4->id, $course->id);
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2->id, 'userid' => $user3->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group3->id, 'userid' => $user4->id));

        // Test autocreate group based on all course users.
        $users = groups_get_potential_members($course->id);
        $group4 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        foreach ($users as $user) {
            $this->getDataGenerator()->create_group_member(array('groupid' => $group4->id, 'userid' => $user->id));
        }
        $this->assertEquals(4, $DB->count_records('groups_members', array('groupid' => $group4->id)));

        // Test autocreate group based on existing group.
        $source = array();
        $source['groupid'] = $group1->id;
        $users = groups_get_potential_members($course->id, 0, $source);
        $group5 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        foreach ($users as $user) {
            $this->getDataGenerator()->create_group_member(array('groupid' => $group5->id, 'userid' => $user->id));
        }
        $this->assertEquals(2, $DB->count_records('groups_members', array('groupid' => $group5->id)));

        // Test autocreate group based on existing grouping.
        $source = array();
        $source['groupingid'] = $grouping1->id;
        $users = groups_get_potential_members($course->id, 0, $source);
        $group6 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        foreach ($users as $user) {
            $this->getDataGenerator()->create_group_member(array('groupid' => $group6->id, 'userid' => $user->id));
        }
        $this->assertEquals(2, $DB->count_records('groups_members', array('groupid' => $group6->id)));
    }

    /**
     * Test groups_create_group enabling a group conversation.
     */
    public function test_groups_create_group_with_conversation() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $course1 = $this->getDataGenerator()->create_course();
        $coursecontext1 = \context_course::instance($course1->id);

        // Create two groups and only one group with enablemessaging = 1.
        $group1a = $this->getDataGenerator()->create_group(array('courseid' => $course1->id, 'enablemessaging' => 1));
        $group1b = $this->getDataGenerator()->create_group(array('courseid' => $course1->id, 'enablemessaging' => 0));

        $conversations = $DB->get_records('message_conversations',
            [
                'contextid' => $coursecontext1->id,
                'component' => 'core_group',
                'itemtype' => 'groups',
                'enabled' => \core_message\api::MESSAGE_CONVERSATION_ENABLED
            ]
        );
        $this->assertCount(1, $conversations);

        $conversation = reset($conversations);
        // Check groupid was stored in itemid on conversation area.
        $this->assertEquals($group1a->id, $conversation->itemid);

        $conversations = $DB->get_records('message_conversations', ['id' => $conversation->id]);
        $this->assertCount(1, $conversations);

        $conversation = reset($conversations);

        // Check group name was stored in conversation.
        $this->assertEquals($group1a->name, $conversation->name);
    }

    /**
     * Test groups_update_group enabling and disabling a group conversation.
     */
    public function test_groups_update_group_conversation() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $course1 = $this->getDataGenerator()->create_course();
        $coursecontext1 = \context_course::instance($course1->id);

        // Create two groups and only one group with enablemessaging = 1.
        $group1a = $this->getDataGenerator()->create_group(array('courseid' => $course1->id, 'enablemessaging' => 1));
        $group1b = $this->getDataGenerator()->create_group(array('courseid' => $course1->id, 'enablemessaging' => 0));

        $conversations = $DB->get_records('message_conversations',
            [
                'contextid' => $coursecontext1->id,
                'component' => 'core_group',
                'itemtype' => 'groups',
                'enabled' => \core_message\api::MESSAGE_CONVERSATION_ENABLED
            ]
        );
        $this->assertCount(1, $conversations);

        // Check that the conversation area is created when group messaging is enabled in the course group.
        $group1b->enablemessaging = 1;
        groups_update_group($group1b);

        $conversations = $DB->get_records('message_conversations',
            [
                'contextid' => $coursecontext1->id,
                'component' => 'core_group',
                'itemtype' => 'groups',
                'enabled' => \core_message\api::MESSAGE_CONVERSATION_ENABLED
            ],
        'id ASC');
        $this->assertCount(2, $conversations);

        $conversation1a = array_shift($conversations);
        $conversation1b = array_shift($conversations);

        $conversation1b = $DB->get_record('message_conversations', ['id' => $conversation1b->id]);

        // Check for group1b that group name was stored in conversation.
        $this->assertEquals($group1b->name, $conversation1b->name);

        $group1b->enablemessaging = 0;
        groups_update_group($group1b);
        $this->assertEquals(0, $DB->get_field("message_conversations", "enabled", ['id' => $conversation1b->id]));

        // Check that the name of the conversation is changed when the name of the course group is updated.
        $group1b->name = 'New group name';
        groups_update_group($group1b);
        $conversation1b = $DB->get_record('message_conversations', ['id' => $conversation1b->id]);
        $this->assertEquals($group1b->name, $conversation1b->name);
    }

    /**
     * Test groups_add_member to conversation.
     */
    public function test_groups_add_member_conversation() {
        global $DB;
        $this->resetAfterTest();

        $this->setAdminUser();

        $course1 = $this->getDataGenerator()->create_course();
        $coursecontext1 = \context_course::instance($course1->id);

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id);

        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course1->id, 'enablemessaging' => 1));

        // Add users to group1.
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user2->id));

        $conversation = \core_message\api::get_conversation_by_area(
            'core_group',
            'groups',
            $group1->id,
            $coursecontext1->id
        );

        // Check if the users has been added to the conversation.
        $this->assertEquals(2, $DB->count_records('message_conversation_members', ['conversationid' => $conversation->id]));

        // Check if the user has been added to the conversation when the conversation is disabled.
        \core_message\api::disable_conversation($conversation->id);
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user3->id));
        $this->assertEquals(3, $DB->count_records('message_conversation_members', ['conversationid' => $conversation->id]));
    }

    /**
     * Test groups_remove_member to conversation.
     */
    public function test_groups_remove_member_conversation() {
        global $DB;

        $this->resetAfterTest();

        $this->setAdminUser();

        $course1 = $this->getDataGenerator()->create_course();
        $coursecontext1 = \context_course::instance($course1->id);

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id);

        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course1->id, 'enablemessaging' => 1));

        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user3->id));

        $conversation = \core_message\api::get_conversation_by_area(
            'core_group',
            'groups',
            $group1->id,
            $coursecontext1->id
        );

        // Check if there are three users in the conversation.
        $this->assertEquals(3, $DB->count_records('message_conversation_members', ['conversationid' => $conversation->id]));

        // Check if after removing one member in the conversation there are two members.
        groups_remove_member($group1->id, $user1->id);
        $this->assertEquals(2, $DB->count_records('message_conversation_members', ['conversationid' => $conversation->id]));

        // Check if the user has been removed from the conversation when the conversation is disabled.
        \core_message\api::disable_conversation($conversation->id);
        groups_remove_member($group1->id, $user2->id);
        $this->assertEquals(1, $DB->count_records('message_conversation_members', ['conversationid' => $conversation->id]));
    }

    /**
     * Test if you enable group messaging in a group with members these are added to the conversation.
     */
    public function test_add_members_group_updated_conversation_enabled() {
        global $DB;

        $this->resetAfterTest();

        $this->setAdminUser();

        $course1 = $this->getDataGenerator()->create_course();
        $coursecontext1 = \context_course::instance($course1->id);

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id);

        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course1->id, 'enablemessaging' => 0));

        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user3->id));

        $conversation = \core_message\api::get_conversation_by_area(
            'core_group',
            'groups',
            $group1->id,
            $coursecontext1->id
        );

        // No conversation should exist as 'enablemessaging' was set to 0.
        $this->assertFalse($conversation);

        // Check that the three users are in the conversation when group messaging is enabled in the course group.
        $group1->enablemessaging = 1;
        groups_update_group($group1);

        $conversation = \core_message\api::get_conversation_by_area(
            'core_group',
            'groups',
            $group1->id,
            $coursecontext1->id
        );

        $this->assertEquals(3, $DB->count_records('message_conversation_members', ['conversationid' => $conversation->id]));
    }

    public function test_groups_get_members_by_role(): void {
        $this->resetAfterTest();

        $this->setAdminUser();

        $course1 = $this->getDataGenerator()->create_course();

        $user1 = $this->getDataGenerator()->create_user(['username' => 'user1', 'idnumber' => 1]);
        $user2 = $this->getDataGenerator()->create_user(['username' => 'user2', 'idnumber' => 2]);
        $user3 = $this->getDataGenerator()->create_user(['username' => 'user3', 'idnumber' => 3]);

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, 0);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id, 1);
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id, 1);

        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course1->id]);

        $this->getDataGenerator()->create_group_member(['groupid' => $group1->id, 'userid' => $user1->id]);
        $this->getDataGenerator()->create_group_member(['groupid' => $group1->id, 'userid' => $user2->id]);
        $this->getDataGenerator()->create_group_member(['groupid' => $group1->id, 'userid' => $user3->id]);

        // Test basic usage.
        $result = groups_get_members_by_role($group1->id, $course1->id);
        $this->assertEquals(1, count($result[0]->users));
        $this->assertEquals(2, count($result[1]->users));
        $this->assertEquals($user1->firstname, reset($result[0]->users)->firstname);
        $this->assertEquals($user1->username, reset($result[0]->users)->username);

        // Test with specified fields.
        $result = groups_get_members_by_role($group1->id, $course1->id, 'u.firstname, u.lastname');
        $this->assertEquals(1, count($result[0]->users));
        $this->assertEquals($user1->firstname, reset($result[0]->users)->firstname);
        $this->assertEquals($user1->lastname, reset($result[0]->users)->lastname);
        $this->assertEquals(false, isset(reset($result[0]->users)->username));

        // Test with sorting.
        $result = groups_get_members_by_role($group1->id, $course1->id, 'u.username', 'u.username DESC');
        $this->assertEquals(1, count($result[0]->users));
        $this->assertEquals($user3->username, reset($result[1]->users)->username);
        $result = groups_get_members_by_role($group1->id, $course1->id, 'u.username', 'u.username ASC');
        $this->assertEquals(1, count($result[0]->users));
        $this->assertEquals($user2->username, reset($result[1]->users)->username);

        // Test with extra WHERE.
        $result = groups_get_members_by_role(
            $group1->id,
            $course1->id,
            'u.username',
            null,
            'u.idnumber > :number',
            ['number' => 2]);
        $this->assertEquals(1, count($result));
        $this->assertEquals(1, count($result[1]->users));
        $this->assertEquals($user3->username, reset($result[1]->users)->username);

        // Test with join.
        set_user_preference('reptile', 'snake', $user1);
        $result = groups_get_members_by_role($group1->id, $course1->id, 'u.username, up.value', null, 'up.name = :prefname',
                ['prefname' => 'reptile'], 'JOIN {user_preferences} up ON up.userid = u.id');
        $this->assertEquals('snake', reset($result[0]->users)->value);
    }
}
