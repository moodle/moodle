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
class core_group_lib_testcase extends advanced_testcase {

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

        $expected = new stdClass();
        $expected->groupid = $group->id;
        $expected->userid  = $user->id;
        $expected->component = 'mod_workshop';
        $expected->itemid = '123';
        $this->assertEventLegacyData($expected, $event);
        $this->assertSame('groups_member_added', $event->get_legacy_eventname());
        $this->assertInstanceOf('\core\event\group_member_added', $event);
        $this->assertEquals($user->id, $event->relateduserid);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEquals($group->id, $event->objectid);
        $url = new moodle_url('/group/members.php', array('group' => $event->objectid));
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

        $expected = new stdClass();
        $expected->groupid = $group->id;
        $expected->userid  = $user->id;
        $this->assertEventLegacyData($expected, $event);
        $this->assertSame('groups_member_removed', $event->get_legacy_eventname());
        $this->assertInstanceOf('\core\event\group_member_removed', $event);
        $this->assertEquals($user->id, $event->relateduserid);
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEquals($group->id, $event->objectid);
        $url = new moodle_url('/group/members.php', array('group' => $event->objectid));
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
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEquals($group->id, $event->objectid);
        $url = new moodle_url('/group/index.php', array('id' => $event->courseid));
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

        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEquals($group->id, $event->objectid);
        $url = new moodle_url('/group/groupings.php', array('id' => $event->courseid));
        $this->assertEquals($url, $event->get_url());
    }

    public function test_group_updated_event() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $group = $this->getDataGenerator()->create_group(array('courseid' => $course->id));

        $sink = $this->redirectEvents();
        $data = new stdClass();
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
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEquals($group->id, $event->objectid);
        $url = new moodle_url('/group/group.php', array('id' => $event->objectid));
        $this->assertEquals($url, $event->get_url());
    }

    public function test_grouping_updated_event() {
        global $DB;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $grouping = $this->getDataGenerator()->create_grouping(array('courseid' => $course->id));

        $sink = $this->redirectEvents();
        $data = new stdClass();
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

        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEquals($grouping->id, $event->objectid);
        $url = new moodle_url('/group/grouping.php', array('id' => $event->objectid));
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
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEquals($group->id, $event->objectid);
        $url = new moodle_url('/group/index.php', array('id' => $event->courseid));
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
        $this->assertEquals(context_course::instance($course->id), $event->get_context());
        $this->assertEquals($group->id, $event->objectid);
        $url = new moodle_url('/group/groupings.php', array('id' => $event->courseid));
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
}
