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
 * Unit tests for the tool_cohortroles implementation of the privacy API.
 *
 * @package    tool_cohortroles
 * @category   test
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

use \core_privacy\local\request\writer;
use \core_privacy\local\request\approved_contextlist;
use \tool_cohortroles\api;
use \tool_cohortroles\privacy\provider;
use core_privacy\local\request\approved_userlist;

/**
 * Unit tests for the tool_cohortroles implementation of the privacy API.
 *
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_cohortroles_privacy_testcase extends \core_privacy\tests\provider_testcase {

    /**
     * Overriding setUp() function to always reset after tests.
     */
    public function setUp() {
        $this->resetAfterTest(true);
    }

    /**
     * Test for provider::get_contexts_for_userid().
     */
    public function test_get_contexts_for_userid() {
        global $DB;

        // Test setup.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $this->setAdminUser();

        $nocohortroles = 3;
        $this->setup_test_scenario_data($user->id, $nocohortroles);

        // Test the User's assigned cohortroles matches 3.
        $cohortroles = $DB->get_records('tool_cohortroles', ['userid' => $user->id]);
        $this->assertCount($nocohortroles, $cohortroles);

        // Test the User's retrieved contextlist contains only one context.
        $contextlist = provider::get_contexts_for_userid($user->id);
        $contexts = $contextlist->get_contexts();
        $this->assertCount(1, $contexts);

        // Test the User's contexts equal the User's own context.
        $context = reset($contexts);
        $this->assertEquals(CONTEXT_USER, $context->contextlevel);
        $this->assertEquals($user->id, $context->instanceid);
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_user_data() {
        // Test setup.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $this->setAdminUser();

        $nocohortroles = 3;
        $this->setup_test_scenario_data($user->id, $nocohortroles);

        // Test the User's retrieved contextlist contains only one context.
        $contextlist = provider::get_contexts_for_userid($user->id);
        $contexts = $contextlist->get_contexts();
        $this->assertCount(1, $contexts);

        // Test the User's contexts equal the User's own context.
        $context = reset($contexts);
        $this->assertEquals(CONTEXT_USER, $context->contextlevel);
        $this->assertEquals($user->id, $context->instanceid);

        // Retrieve the User's tool_cohortroles data.
        $approvedcontextlist = new approved_contextlist($user, 'tool_cohortroles', $contextlist->get_contextids());
        provider::export_user_data($approvedcontextlist);

        // Test the tool_cohortroles data is exported at the User context level.
        $writer = writer::with_context($context);
        $this->assertTrue($writer->has_any_data());
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        // Test setup.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $this->setAdminUser();

        $nocohortroles = 4;
        $this->setup_test_scenario_data($user->id, $nocohortroles);

        // Test the User's assigned cohortroles matches 4.
        $cohortroles = $DB->get_records('tool_cohortroles', ['userid' => $user->id]);
        $this->assertCount($nocohortroles, $cohortroles);

        // Test the User's retrieved contextlist contains only one context.
        $contextlist = provider::get_contexts_for_userid($user->id);
        $contexts = $contextlist->get_contexts();
        $this->assertCount(1, $contexts);

        // Test the User's contexts equal the User's own context.
        $context = reset($contexts);
        $this->assertEquals(CONTEXT_USER, $context->contextlevel);
        $this->assertEquals($user->id, $context->instanceid);

        // Delete all the User's records in mdl_tool_cohortroles table by the specified User context.
        provider::delete_data_for_all_users_in_context($context);

        // Test the cohort roles records in mdl_tool_cohortroles table is equals zero.
        $cohortroles = $DB->get_records('tool_cohortroles', ['userid' => $user->id]);
        $this->assertCount(0, $cohortroles);
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user() {
        global $DB;

        // Test setup.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $this->setAdminUser();

        $nocohortroles = 4;
        $this->setup_test_scenario_data($user->id, $nocohortroles);

        // Test the User's assigned cohortroles matches 4.
        $cohortroles = $DB->get_records('tool_cohortroles', ['userid' => $user->id]);
        $this->assertCount($nocohortroles, $cohortroles);

        // Test the User's retrieved contextlist contains only one context.
        $contextlist = provider::get_contexts_for_userid($user->id);
        $contexts = $contextlist->get_contexts();
        $this->assertCount(1, $contexts);

        // Test the User's contexts equal the User's own context.
        $context = reset($contexts);
        $this->assertEquals(CONTEXT_USER, $context->contextlevel);
        $this->assertEquals($user->id, $context->instanceid);

        // Delete all the User's records in mdl_tool_cohortroles table by the specified User approved context list.
        $approvedcontextlist = new approved_contextlist($user, 'tool_cohortroles', $contextlist->get_contextids());
        provider::delete_data_for_user($approvedcontextlist);

        // Test the records in mdl_tool_cohortroles table is equals zero.
        $cohortroles = $DB->get_records('tool_cohortroles', ['userid' => $user->id]);
        $this->assertCount(0, $cohortroles);
    }

    /**
     * Test that only users within a course context are fetched.
     */
    public function test_get_users_in_context() {
        $component = 'tool_cohortroles';

        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $usercontext = context_user::instance($user->id);

        $systemcontext = context_system::instance();

        $this->setAdminUser();

        $userlist = new \core_privacy\local\request\userlist($systemcontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);

        $nocohortroles = 3;
        $this->setup_test_scenario_data($user->id, $nocohortroles);

        // The list of users within the system context should contain user.
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $this->assertTrue(in_array($user->id, $userlist->get_userids()));

        // The list of users within the user context should be empty.
        $userlist2 = new \core_privacy\local\request\userlist($usercontext, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(0, $userlist2);
    }

    /**
     * Test that data for users in approved userlist is deleted.
     */
    public function test_delete_data_for_users() {
        $component = 'tool_cohortroles';

        // Create user1.
        $user1 = $this->getDataGenerator()->create_user();
        // Create user2.
        $user2 = $this->getDataGenerator()->create_user();
        // Create user3.
        $user3 = $this->getDataGenerator()->create_user();
        $usercontext3 = context_user::instance($user3->id);

        $systemcontext = context_system::instance();

        $this->setAdminUser();

        $nocohortroles = 3;
        $this->setup_test_scenario_data($user1->id, $nocohortroles);
        $this->setup_test_scenario_data($user2->id, $nocohortroles, 'Sausage roll 2',
                'sausageroll2');
        $this->setup_test_scenario_data($user3->id, $nocohortroles, 'Sausage roll 3',
                'sausageroll3');

        $userlist1 = new \core_privacy\local\request\userlist($systemcontext, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(3, $userlist1);
        $this->assertTrue(in_array($user1->id, $userlist1->get_userids()));
        $this->assertTrue(in_array($user2->id, $userlist1->get_userids()));
        $this->assertTrue(in_array($user3->id, $userlist1->get_userids()));

        // Convert $userlist1 into an approved_contextlist.
        $approvedlist1 = new approved_userlist($systemcontext, $component, [$user1->id, $user2->id]);
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist1);

        // Re-fetch users in systemcontext.
        $userlist1 = new \core_privacy\local\request\userlist($systemcontext, $component);
        provider::get_users_in_context($userlist1);
        // The user data of user1 and user2 in systemcontext should be deleted.
        // The user data of user3 in systemcontext should be still present.
        $this->assertCount(1, $userlist1);
        $this->assertTrue(in_array($user3->id, $userlist1->get_userids()));

        // Convert $userlist1 into an approved_contextlist in the user context.
        $approvedlist2 = new approved_userlist($usercontext3, $component, $userlist1->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist2);
        // Re-fetch users in systemcontext.
        $userlist1 = new \core_privacy\local\request\userlist($systemcontext, $component);
        provider::get_users_in_context($userlist1);
        // The user data in systemcontext should not be deleted.
        $this->assertCount(1, $userlist1);
    }

    /**
     * Helper function to setup tool_cohortroles records for testing a specific user.
     *
     * @param int $userid           The ID of the user used for testing.
     * @param int $nocohortroles    The number of tool_cohortroles to create for the user.
     * @param string $rolename      The name of the role to be created.
     * @param string $roleshortname The short name of the role to be created.
     * @throws \core_competency\invalid_persistent_exception
     * @throws coding_exception
     */
    protected function setup_test_scenario_data($userid, $nocohortroles, $rolename = 'Sausage Roll',
                                                $roleshortname = 'sausageroll') {
        $roleid = create_role($rolename, $roleshortname, 'mmmm');

        for ($c = 0; $c < $nocohortroles; $c++) {
            $cohort = $this->getDataGenerator()->create_cohort();

            $params = (object)array(
                'userid' => $userid,
                'roleid' => $roleid,
                'cohortid' => $cohort->id
            );

            api::create_cohort_role_assignment($params);
        }
    }

}
