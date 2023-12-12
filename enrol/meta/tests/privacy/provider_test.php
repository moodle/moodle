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
 * Base class for unit tests for enrol_meta.
 *
 * @package    enrol_meta
 * @category   test
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace enrol_meta\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\request\writer;
use core_privacy\local\request\approved_contextlist;
use enrol_meta\privacy\provider;

/**
 * Unit tests for the enrol_meta implementation of the privacy API.
 *
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends \core_privacy\tests\provider_testcase {

    /**
     * Enable enrol_meta plugin.
     */
    protected function enable_plugin() {
        $enabled = enrol_get_plugins(true);
        $enabled['meta'] = true;
        $enabled = array_keys($enabled);
        set_config('enrol_plugins_enabled', implode(',', $enabled));
    }

    /**
     * Test getting the context for the user ID related to this plugin.
     */
    public function test_get_contexts_for_userid() {
        global $DB;

        $this->resetAfterTest();
        $metaplugin = enrol_get_plugin('meta');
        $user1 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        $this->enable_plugin();
        $metaplugin->add_instance($course1, array('customint1' => $course2->id, 'customint2' => $group1->id));
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, $studentrole->id);
        // Check if user1 is enrolled into course1 in group 1.
        $this->assertTrue(groups_is_member($group1->id, $user1->id));
        $this->assertTrue($DB->record_exists('groups_members',
            array(
                'groupid' => $group1->id,
                'userid' => $user1->id,
                'component' => 'enrol_meta'
            )
        ));
        // Check context course fro provider to user1.
        $context = \context_course::instance($course1->id);
        $contextlist = provider::get_contexts_for_userid($user1->id);
        $this->assertEquals($context->id, $contextlist->current()->id);
    }

    /**
     * Test that user data is exported correctly.
     */
    public function test_export_user_data() {
        global $DB;

        $this->resetAfterTest();
        $metaplugin = enrol_get_plugin('meta');
        $user1 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        $this->enable_plugin();
        $metaplugin->add_instance($course1, array('customint1' => $course2->id, 'customint2' => $group1->id));
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, $studentrole->id);

        $this->setUser($user1);
        $contextlist = provider::get_contexts_for_userid($user1->id);
        $approvedcontextlist = new approved_contextlist($user1, 'enrol_meta', $contextlist->get_contextids());
        provider::export_user_data($approvedcontextlist);
        foreach ($contextlist as $context) {
            /** @var \core_privacy\tests\request\content_writer $writer */
            $writer = writer::with_context($context);
            $data = $writer->get_data([
                get_string('pluginname', 'enrol_meta'),
                get_string('groups', 'core_group')
            ]);
            $this->assertTrue($writer->has_any_data());
            if ($context->contextlevel == CONTEXT_COURSE) {
                $exportedgroups = $data->groups;
                // User1 only belongs to group1 via enrol_meta.
                $this->assertCount(1, $exportedgroups);
                $exportedgroup = reset($exportedgroups);
                $this->assertEquals($group1->name, $exportedgroup->name);
            }
        }
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $this->resetAfterTest();
        $metaplugin = enrol_get_plugin('meta');
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        $this->enable_plugin();
        $metaplugin->add_instance($course1, array('customint1' => $course2->id, 'customint2' => $group1->id));
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course2->id, $studentrole->id);
        $this->assertEquals(
                3,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                      FROM {groups_members} gm
                                      JOIN {groups} g ON gm.groupid = g.id
                                     WHERE g.courseid = ?", [$course1->id])
        );

        $coursecontext1 = \context_course::instance($course1->id);
        provider::delete_data_for_all_users_in_context($coursecontext1);
        $this->assertEquals(
            0,
            $DB->count_records_sql("SELECT COUNT(gm.id)
                                      FROM {groups_members} gm
                                      JOIN {groups} g ON gm.groupid = g.id
                                     WHERE g.courseid = ?", [$course1->id])
        );
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user() {
        global $DB;

        $this->resetAfterTest();
        $metaplugin = enrol_get_plugin('meta');
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));

        $this->enable_plugin();
        $metaplugin->add_instance($course1, array('customint1' => $course2->id, 'customint2' => $group1->id));
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course2->id, $studentrole->id);

        $this->assertEquals(
                3,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                      FROM {groups_members} gm
                                      JOIN {groups} g ON gm.groupid = g.id
                                     WHERE g.courseid = ?", [$course1->id])
        );

        $this->setUser($user1);
        $coursecontext1 = \context_course::instance($course1->id);
        $coursecontext2 = \context_course::instance($course2->id);
        $approvedcontextlist = new \core_privacy\tests\request\approved_contextlist($user1, 'enrol_meta',
                [$coursecontext1->id]);
        provider::delete_data_for_user($approvedcontextlist);
        // Check we have 2 users in groups because we are deleted user1.
        $this->assertEquals(
                2,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course1->id])
        );
    }

    /**
     * Test for provider::delete_data_for_users().
     */
    public function test_delete_data_for_users() {
        global $DB;

        $this->resetAfterTest();

        $metaplugin = enrol_get_plugin('meta');

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));

        $this->enable_plugin();
        $metaplugin->add_instance($course1, array('customint1' => $course2->id, 'customint2' => $group1->id));

        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id, 'student');
        $this->getDataGenerator()->enrol_user($user3->id, $course2->id, 'student');

        $this->assertEquals(
                3,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                      FROM {groups_members} gm
                                      JOIN {groups} g ON gm.groupid = g.id
                                     WHERE g.courseid = ?", [$course1->id])
        );

        $coursecontext1 = \context_course::instance($course1->id);

        $approveduserlist = new \core_privacy\local\request\approved_userlist($coursecontext1, 'enrol_meta',
                [$user1->id, $user2->id]);
        provider::delete_data_for_users($approveduserlist);

        // Check we have 1 user in groups because we have deleted user1 and user2.
        $this->assertEquals(
                1,
                $DB->count_records_sql("SELECT COUNT(gm.id)
                                          FROM {groups_members} gm
                                          JOIN {groups} g ON gm.groupid = g.id
                                         WHERE g.courseid = ?", [$course1->id])
        );
    }

    /**
     * Test for provider::get_users_in_context().
     */
    public function test_get_users_in_context() {
        global $DB;

        $this->resetAfterTest();

        $metaplugin = enrol_get_plugin('meta');

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course1->id));

        $this->enable_plugin();
        $metaplugin->add_instance($course1, array('customint1' => $course2->id, 'customint2' => $group1->id));

        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id, 'student');

        // Check if user1 is enrolled into course1 in group 1.
        $this->assertTrue(groups_is_member($group1->id, $user1->id));
        $this->assertTrue($DB->record_exists('groups_members',
                array(
                    'groupid' => $group1->id,
                    'userid' => $user1->id,
                    'component' => 'enrol_meta'
                )
        ));

        $context = \context_course::instance($course1->id);

        $userlist = new \core_privacy\local\request\userlist($context, 'enrol_meta');
        \enrol_meta\privacy\provider::get_users_in_context($userlist);

        $this->assertEqualsCanonicalizing(
                [$user1->id, $user2->id],
                $userlist->get_userids());
    }
}
