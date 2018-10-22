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
 * Privacy provider tests.
 *
 * @package    core_group
 * @category   test
 * @copyright  2018 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core_privacy\tests\provider_testcase;
use core_privacy\local\metadata\collection;
use core_group\privacy\provider;
use core_privacy\local\request\writer;

/**
 * Class core_group_privacy_provider_testcase.
 *
 * @copyright  2018 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_group_privacy_provider_testcase extends provider_testcase {

    /**
     * Test for provider::get_metadata().
     */
    public function test_get_metadata() {
        $collection = new collection('core_group');
        $newcollection = provider::get_metadata($collection);
        $itemcollection = $newcollection->get_collection();
        $this->assertCount(1, $itemcollection);

        $table = reset($itemcollection);

        $this->assertEquals('groups_members', $table->get_name());
        $this->assertEquals('privacy:metadata:groups', $table->get_summary());

        $privacyfields = $table->get_privacy_fields();
        $this->assertArrayHasKey('groupid', $privacyfields);
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('timeadded', $privacyfields);
    }

    /**
     * Test for provider::export_groups() to export manual group memberships.
     */
    public function test_export_groups() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group3 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group4 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id);

        // Add user1 to group1 and group2.
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2->id, 'userid' => $user1->id));

        // Add user2 to group2 and group3.
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group3->id, 'userid' => $user2->id));

        $context = context_course::instance($course->id);

        // Retrieve groups for user1.
        $this->setUser($user1);
        $writer = writer::with_context($context);
        provider::export_groups($context, '');

        $data = $writer->get_data([get_string('groups', 'core_group')]);
        $exportedgroups = $data->groups;

        // User1 belongs to group1 and group2.
        $this->assertEquals(
                [$group1->name, $group2->name],
                array_column($exportedgroups, 'name'),
                '', 0.0, 10, true);
    }

    /**
     * Test for provider::export_groups() to export group memberships of a component.
     */
    public function test_export_groups_for_component() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group3 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group4 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group5 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, null, 'self');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, null, 'self');

        // Add user1 to group1 (via enrol_self) and group2 and group3.
        $this->getDataGenerator()->create_group_member(
                array('groupid' => $group1->id, 'userid' => $user1->id, 'component' => 'enrol_self'));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group3->id, 'userid' => $user1->id));

        // Add user2 to group3 (via enrol_self) and group4.
        $this->getDataGenerator()->create_group_member(
                array('groupid' => $group3->id, 'userid' => $user2->id, 'component' => 'enrol_self'));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group4->id, 'userid' => $user2->id));

        $context = context_course::instance($course->id);

        // Retrieve groups for user1.
        $this->setUser($user1);
        $writer = writer::with_context($context);
        provider::export_groups($context, 'enrol_self');

        $data = $writer->get_data([get_string('groups', 'core_group')]);
        $exportedgroups = $data->groups;

        // User1 only belongs to group1 via enrol_self.
        $this->assertCount(1, $exportedgroups);
        $exportedgroup = reset($exportedgroups);
        $this->assertEquals($group1->name, $exportedgroup->name);
    }

    /**
     * Test for provider::delete_groups_for_all_users() to delete manual group memberships.
     */
    public function test_delete_groups_for_all_users() {
        global $DB;

        $this->resetAfterTest();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        $group1a = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));
        $group1b = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));
        $group2a = $this->getDataGenerator()->create_group(array('courseid' => $course2->id));
        $group2b = $this->getDataGenerator()->create_group(array('courseid' => $course2->id));

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id);

        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1b->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2b->id, 'userid' => $user2->id));

        $this->assertEquals(
                2,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                      FROM {groups_members} gm
                                      JOIN {groups} g ON gm.groupid = g.id
                                     WHERE g.courseid = ?", [$course1->id])
        );
        $this->assertEquals(
                2,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                      FROM {groups_members} gm
                                      JOIN {groups} g ON gm.groupid = g.id
                                     WHERE g.courseid = ?", [$course2->id])
        );

        $coursecontext1 = context_course::instance($course1->id);
        provider::delete_groups_for_all_users($coursecontext1, '');

        $this->assertEquals(
            0,
            $DB->count_records_sql("SELECT COUNT(gm.id)
                                      FROM {groups_members} gm
                                      JOIN {groups} g ON gm.groupid = g.id
                                     WHERE g.courseid = ?", [$course1->id])
        );
        $this->assertEquals(
            2,
            $DB->count_records_sql("SELECT COUNT(gm.id)
                                      FROM {groups_members} gm
                                      JOIN {groups} g ON gm.groupid = g.id
                                     WHERE g.courseid = ?", [$course2->id])
        );
    }

    /**
     * Test for provider::delete_groups_for_all_users() to delete group memberships of a component.
     */
    public function test_delete_groups_for_all_users_for_component() {
        global $DB;

        $this->resetAfterTest();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        $group1a = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));
        $group1b = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));
        $group2a = $this->getDataGenerator()->create_group(array('courseid' => $course2->id));
        $group2b = $this->getDataGenerator()->create_group(array('courseid' => $course2->id));

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, null, 'self');
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, null, 'self');
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id, null, 'self');
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id, null, 'self');

        $this->getDataGenerator()->create_group_member(
                array('groupid' => $group1a->id, 'userid' => $user1->id, 'component' => 'enrol_self'));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1b->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(
                array('groupid' => $group2a->id, 'userid' => $user1->id, 'component' => 'enrol_self'));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2b->id, 'userid' => $user2->id));

        $this->assertEquals(
                2,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                      FROM {groups_members} gm
                                      JOIN {groups} g ON gm.groupid = g.id
                                     WHERE g.courseid = ?", [$course1->id])
        );
        $this->assertEquals(
                2,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                      FROM {groups_members} gm
                                      JOIN {groups} g ON gm.groupid = g.id
                                     WHERE g.courseid = ?", [$course2->id])
        );

        $coursecontext1 = context_course::instance($course1->id);
        provider::delete_groups_for_all_users($coursecontext1, 'enrol_self');

        $this->assertEquals(
            1,
            $DB->count_records_sql("SELECT COUNT(gm.id)
                                      FROM {groups_members} gm
                                      JOIN {groups} g ON gm.groupid = g.id
                                     WHERE g.courseid = ?", [$course1->id])
        );
        $this->assertEquals(
            2,
            $DB->count_records_sql("SELECT COUNT(gm.id)
                                      FROM {groups_members} gm
                                      JOIN {groups} g ON gm.groupid = g.id
                                     WHERE g.courseid = ?", [$course2->id])
        );
    }

    /**
     * Test for provider::delete_groups_for_all_users() to check deleting from cache.
     */
    public function test_delete_groups_for_all_users_deletes_cache() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user1->id, $course->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id);

        $this->getDataGenerator()->create_group_member(array('userid' => $user1->id, 'groupid' => $group1->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $user1->id, 'groupid' => $group2->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $user2->id, 'groupid' => $group1->id));

        $this->assertEquals([[$group1->id, $group2->id]], groups_get_user_groups($course->id, $user1->id), '', 0.0, 10, true);
        $this->assertEquals([[$group1->id]], groups_get_user_groups($course->id, $user2->id));

        $coursecontext = context_course::instance($course->id);
        provider::delete_groups_for_all_users($coursecontext, '');

        $this->assertEquals([[]], groups_get_user_groups($course->id, $user1->id));
        $this->assertEquals([[]], groups_get_user_groups($course->id, $user2->id));
    }

    /**
     * Test for provider::delete_groups_for_user() to delete manual group memberships.
     */
    public function test_delete_groups_for_user() {
        global $DB;

        $this->resetAfterTest();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();

        $group1a = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));
        $group1b = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));
        $group2a = $this->getDataGenerator()->create_group(array('courseid' => $course2->id));
        $group2b = $this->getDataGenerator()->create_group(array('courseid' => $course2->id));
        $group3a = $this->getDataGenerator()->create_group(array('courseid' => $course3->id));
        $group3b = $this->getDataGenerator()->create_group(array('courseid' => $course3->id));

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course3->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course3->id);

        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1b->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2b->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group3a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group3b->id, 'userid' => $user2->id));

        $this->assertEquals(
                2,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course1->id])
        );
        $this->assertEquals(
                2,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course2->id])
        );
        $this->assertEquals(
                2,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course2->id])
        );
        $this->assertEquals(
                3,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE gm.userid = ?", [$user1->id])
        );

        $this->setUser($user1);
        $coursecontext1 = context_course::instance($course1->id);
        $coursecontext2 = context_course::instance($course2->id);
        $approvedcontextlist = new \core_privacy\tests\request\approved_contextlist($user1, 'core_group',
                [$coursecontext1->id, $coursecontext2->id]);
        provider::delete_groups_for_user($approvedcontextlist, '');

        $this->assertEquals(
                1,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course1->id])
        );
        $this->assertEquals(
                1,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course2->id])
        );
        $this->assertEquals(
                2,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course3->id])
        );
        $this->assertEquals(
                1,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE gm.userid = ?", [$user1->id])
        );
    }

    /**
     * Test for provider::delete_groups_for_user() to delete group memberships of a component.
     */
    public function test_delete_groups_for_user_for_component() {
        global $DB;

        $this->resetAfterTest();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();

        $group1a = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));
        $group1b = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));
        $group2a = $this->getDataGenerator()->create_group(array('courseid' => $course2->id));
        $group2b = $this->getDataGenerator()->create_group(array('courseid' => $course2->id));
        $group3a = $this->getDataGenerator()->create_group(array('courseid' => $course3->id));
        $group3b = $this->getDataGenerator()->create_group(array('courseid' => $course3->id));

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, null, 'self');
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, null, 'self');
        $this->getDataGenerator()->enrol_user($user1->id, $course3->id, null, 'self');
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id, null, 'self');
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id, null, 'self');
        $this->getDataGenerator()->enrol_user($user2->id, $course3->id, null, 'self');

        $this->getDataGenerator()->create_group_member(
                array('groupid' => $group1a->id, 'userid' => $user1->id, 'component' => 'enrol_self'));
        $this->getDataGenerator()->create_group_member(
                array('groupid' => $group1b->id, 'userid' => $user2->id, 'component' => 'enrol_self'));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2b->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group3a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group3b->id, 'userid' => $user2->id));

        $this->assertEquals(
                2,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course1->id])
        );
        $this->assertEquals(
                2,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course2->id])
        );
        $this->assertEquals(
                2,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course2->id])
        );
        $this->assertEquals(
                3,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE gm.userid = ?", [$user1->id])
        );

        $this->setUser($user1);
        $coursecontext1 = context_course::instance($course1->id);
        $coursecontext2 = context_course::instance($course2->id);
        $approvedcontextlist = new \core_privacy\tests\request\approved_contextlist($user1, 'core_group',
                [$coursecontext1->id, $coursecontext2->id]);
        provider::delete_groups_for_user($approvedcontextlist, 'enrol_self');

        $this->assertEquals(
                1,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course1->id])
        );
        $this->assertEquals(
                2,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course2->id])
        );
        $this->assertEquals(
                2,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course3->id])
        );
        $this->assertEquals(
                2,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE gm.userid = ?", [$user1->id])
        );
    }

    /**
     * Test for provider::delete_groups_for_user() to check deleting from cache.
     */
    public function test_delete_groups_for_user_deletes_cache() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();

        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));

        $user = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        $this->getDataGenerator()->create_group_member(array('userid' => $user->id, 'groupid' => $group1->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $user->id, 'groupid' => $group2->id));

        $this->assertEquals([[$group1->id, $group2->id]], groups_get_user_groups($course->id, $user->id), '', 0.0, 10, true);

        $this->setUser($user);
        $coursecontext = context_course::instance($course->id);
        $approvedcontextlist = new \core_privacy\tests\request\approved_contextlist($user, 'core_group', [$coursecontext->id]);
        provider::delete_groups_for_user($approvedcontextlist, '');

        $this->assertEquals([[]], groups_get_user_groups($course->id, $user->id));
    }

    /**
     * Test for provider::get_contexts_for_userid().
     */
    public function test_get_contexts_for_userid() {
        $this->resetAfterTest();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();

        $group1a = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));
        $group1b = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));
        $group2a = $this->getDataGenerator()->create_group(array('courseid' => $course2->id));
        $group2b = $this->getDataGenerator()->create_group(array('courseid' => $course2->id));
        $group3a = $this->getDataGenerator()->create_group(array('courseid' => $course3->id));
        $group3b = $this->getDataGenerator()->create_group(array('courseid' => $course3->id));

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course3->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course3->id);

        $this->getDataGenerator()->create_group_member(array('userid' => $user1->id, 'groupid' => $group1a->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $user1->id, 'groupid' => $group2a->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $user2->id, 'groupid' => $group1b->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $user2->id, 'groupid' => $group2b->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $user2->id, 'groupid' => $group3b->id));

        $coursecontext1 = context_course::instance($course1->id);
        $coursecontext2 = context_course::instance($course2->id);

        // User1 is member of some groups in course1 and course2.
        $contextlist = provider::get_contexts_for_userid($user1->id);
        $this->assertCount(2, $contextlist);
        $this->assertEquals(
                [$coursecontext1->id, $coursecontext2->id],
                $contextlist->get_contextids(),
                '', 0.0, 10, true);
    }

    /**
     * Test for provider::get_contexts_for_userid() when there are group memberships from other components.
     */
    public function test_get_contexts_for_userid_component() {
        $this->resetAfterTest();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course2->id));

        $user = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user->id, $course2->id);

        $this->getDataGenerator()->create_group_member(
                array(
                    'userid' => $user->id,
                    'groupid' => $group1->id
                ));
        $this->getDataGenerator()->create_group_member(
                array(
                    'userid' => $user->id,
                    'groupid' => $group2->id,
                    'component' => 'enrol_meta'
                ));

        $coursecontext1 = context_course::instance($course1->id);

        // User is member of some groups in course1 and course2,
        // but only the membership in course1 is directly managed by core_group.
        $contextlist = provider::get_contexts_for_userid($user->id);
        $this->assertEquals([$coursecontext1->id], $contextlist->get_contextids());
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_user_data() {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group3 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group4 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $this->getDataGenerator()->enrol_user($user1->id, $course->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course->id);

        // Add user1 to group1 and group2.
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2->id, 'userid' => $user1->id));

        // Add user2 to group2 and group3.
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group3->id, 'userid' => $user2->id));

        $context = context_course::instance($course->id);

        $this->setUser($user1);

        // Export all of the data for the context.
        $this->export_context_data_for_user($user1->id, $context, 'core_group');

        $writer = writer::with_context($context);
        $this->assertTrue($writer->has_any_data());

        $data = $writer->get_data([get_string('groups', 'core_group')]);
        $exportedgroups = $data->groups;

        // User1 belongs to group1 and group2.
        $this->assertEquals(
                [$group1->name, $group2->name],
                array_column($exportedgroups, 'name'),
                '', 0.0, 10, true);
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $this->resetAfterTest();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        $group1a = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));
        $group1b = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));
        $group2a = $this->getDataGenerator()->create_group(array('courseid' => $course2->id));
        $group2b = $this->getDataGenerator()->create_group(array('courseid' => $course2->id));

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id);

        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1b->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2b->id, 'userid' => $user2->id));

        $this->assertEquals(
                2,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course1->id])
        );
        $this->assertEquals(
                2,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course2->id])
        );

        $coursecontext1 = context_course::instance($course1->id);
        provider::delete_data_for_all_users_in_context($coursecontext1);

        $this->assertEquals(
                0,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course1->id])
        );
        $this->assertEquals(
                2,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course2->id])
        );
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user() {
        global $DB;

        $this->resetAfterTest();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $course3 = $this->getDataGenerator()->create_course();

        $group1a = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));
        $group1b = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));
        $group2a = $this->getDataGenerator()->create_group(array('courseid' => $course2->id));
        $group2b = $this->getDataGenerator()->create_group(array('courseid' => $course2->id));
        $group3a = $this->getDataGenerator()->create_group(array('courseid' => $course3->id));
        $group3b = $this->getDataGenerator()->create_group(array('courseid' => $course3->id));

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course3->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course3->id);

        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1b->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2b->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group3a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group3b->id, 'userid' => $user2->id));

        $this->assertEquals(
                2,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course1->id])
        );
        $this->assertEquals(
                2,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course2->id])
        );
        $this->assertEquals(
                2,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course2->id])
        );
        $this->assertEquals(
                3,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE gm.userid = ?", [$user1->id])
        );

        $this->setUser($user1);
        $coursecontext1 = context_course::instance($course1->id);
        $coursecontext2 = context_course::instance($course2->id);
        $approvedcontextlist = new \core_privacy\tests\request\approved_contextlist($user1, 'core_group',
                [$coursecontext1->id, $coursecontext2->id]);
        provider::delete_data_for_user($approvedcontextlist);

        $this->assertEquals(
                1,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course1->id])
        );
        $this->assertEquals(
                1,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course2->id])
        );
        $this->assertEquals(
                2,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course3->id])
        );
        $this->assertEquals(
                1,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE gm.userid = ?", [$user1->id])
        );
    }

    /**
     * Test for provider::delete_data_for_users().
     */
    public function test_delete_data_for_users() {
        global $DB;

        $this->resetAfterTest();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        $group1a = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));
        $group1b = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));
        $group1c = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));
        $group2a = $this->getDataGenerator()->create_group(array('courseid' => $course2->id));
        $group2b = $this->getDataGenerator()->create_group(array('courseid' => $course2->id));
        $group2c = $this->getDataGenerator()->create_group(array('courseid' => $course2->id));

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course2->id);

        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1b->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1c->id, 'userid' => $user3->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2b->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2c->id, 'userid' => $user3->id));

        $this->assertEquals(
                3,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course1->id])
        );
        $this->assertEquals(
                3,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course2->id])
        );

        $coursecontext1 = context_course::instance($course1->id);
        $approveduserlist = new \core_privacy\local\request\approved_userlist($coursecontext1, 'core_group',
                [$user1->id, $user2->id]);
        provider::delete_data_for_users($approveduserlist);

        $this->assertEquals(
                [$user3->id],
                $DB->get_fieldset_sql("SELECT gm.userid
                                         FROM {groups_members} gm
                                         JOIN {groups} g ON gm.groupid = g.id
                                        WHERE g.courseid = ?", [$course1->id])
        );
        $this->assertEquals(
                3,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course2->id])
        );
    }

    /**
     * Test for provider::get_users_in_context().
     */
    public function test_get_users_in_context() {
        $this->resetAfterTest();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        $group1a = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));
        $group1b = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));
        $group2a = $this->getDataGenerator()->create_group(array('courseid' => $course2->id));
        $group2b = $this->getDataGenerator()->create_group(array('courseid' => $course2->id));

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course2->id);

        $this->getDataGenerator()->create_group_member(array('userid' => $user1->id, 'groupid' => $group1a->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $user1->id, 'groupid' => $group2a->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $user2->id, 'groupid' => $group1b->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $user2->id, 'groupid' => $group2b->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $user3->id, 'groupid' => $group2a->id));

        $coursecontext1 = context_course::instance($course1->id);

        $userlist = new \core_privacy\local\request\userlist($coursecontext1, 'core_group');
        \core_group\privacy\provider::get_users_in_context($userlist);

        // Only user1 and user2. User3 is not member of any group in course1.
        $this->assertCount(2, $userlist);
        $this->assertEquals(
                [$user1->id, $user2->id],
                $userlist->get_userids(),
                '', 0.0, 10, true);
    }
}
