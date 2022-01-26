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
 * Unit tests for the core_repository implementation of the privacy API.
 *
 * @package    core_repository
 * @category   test
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_repository\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;
use core_privacy\local\request\approved_contextlist;
use core_repository\privacy\provider;
use core_privacy\local\request\approved_userlist;

/**
 * Unit tests for the core_repository implementation of the privacy API.
 *
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends \core_privacy\tests\provider_testcase {

    /**
     * Overriding setUp() function to always reset after tests.
     */
    public function setUp(): void {
        $this->resetAfterTest(true);
    }

    /**
     * Test for provider::get_contexts_for_userid().
     */
    public function test_get_contexts_for_userid() {
        // Test setup.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Test the User's retrieved contextlist is empty because no repository_instances have added for the User yet.
        $contextlist = provider::get_contexts_for_userid($user->id);
        $contexts = $contextlist->get_contexts();
        $this->assertCount(0, $contexts);

        // Create 3 repository_instances records for the User.
        $this->setup_test_scenario_data($user->id, 3);

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

        // Create 3 repository_instances records for the User.
        $this->setup_test_scenario_data($user->id, 3);

        // Test the User's retrieved contextlist contains only one context.
        $contextlist = provider::get_contexts_for_userid($user->id);
        $contexts = $contextlist->get_contexts();
        $this->assertCount(1, $contexts);

        // Test the User's contexts equal the User's own context.
        $context = reset($contexts);
        $this->assertEquals(CONTEXT_USER, $context->contextlevel);
        $this->assertEquals($user->id, $context->instanceid);

        // Retrieve repository_instances data only for this user.
        $approvedcontextlist = new approved_contextlist($user, 'core_repository', $contextlist->get_contextids());
        provider::export_user_data($approvedcontextlist);

        // Test the repository_instances data is exported at the User context level.
        $user = $approvedcontextlist->get_user();
        $contextuser = \context_user::instance($user->id);
        $writer = writer::with_context($contextuser);
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

        // Create 3 repository_instances records for the User.
        $this->setup_test_scenario_data($user->id, 3);

        // Test the User's retrieved contextlist contains only one context.
        $contextlist = provider::get_contexts_for_userid($user->id);
        $contexts = $contextlist->get_contexts();
        $this->assertCount(1, $contexts);

        // Test the User's contexts equal the User's own context.
        $context = reset($contexts);
        $this->assertEquals(CONTEXT_USER, $context->contextlevel);

        // Delete all the User's records in mdl_repository_instances table by the specified User context.
        provider::delete_data_for_all_users_in_context($context);

        // Test the cohort roles records in mdl_repository_instances table is equals zero.
        $repositoryinstances = $DB->get_records('repository_instances', ['userid' => $user->id]);
        $this->assertCount(0, $repositoryinstances);
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user() {
        global $DB;

        // Test setup.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Create 3 repository_instances records for the User.
        $this->setup_test_scenario_data($user->id, 3);

        // Test the User's retrieved contextlist contains only one context.
        $contextlist = provider::get_contexts_for_userid($user->id);
        $contexts = $contextlist->get_contexts();
        $this->assertCount(1, $contexts);

        // Test the User's contexts equal the User's own context.
        $context = reset($contexts);
        $this->assertEquals(CONTEXT_USER, $context->contextlevel);

        // Delete all the User's records in mdl_repository_instances table by the specified User approved context list.
        $approvedcontextlist = new approved_contextlist($user, 'repository_instances', $contextlist->get_contextids());
        provider::delete_data_for_user($approvedcontextlist);

        // Test the cohort roles records in mdl_repository_instances table is equals zero.
        $repositoryinstances = $DB->get_records('repository_instances', ['userid' => $user->id]);
        $this->assertCount(0, $repositoryinstances);
    }

    /**
     * Test that only users with a user context are fetched.
     */
    public function test_get_users_in_context() {
        $this->resetAfterTest();

        $component = 'core_repository';
        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $usercontext = \context_user::instance($user->id);
        $userlist = new \core_privacy\local\request\userlist($usercontext, $component);
        // The list of users should not return anything yet (related data still haven't been created).
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);

        // Create 3 repository_instances records for user.
        $this->setup_test_scenario_data($user->id, 3);

        // The list of users for user context should return the user.
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $expected = [$user->id];
        $actual = $userlist->get_userids();
        $this->assertEquals($expected, $actual);

        // The list of users for system context should not return any users.
        $systemcontext = \context_system::instance();
        $userlist = new \core_privacy\local\request\userlist($systemcontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);
    }

    /**
     * Test that data for users in approved userlist is deleted.
     */
    public function test_delete_data_for_users() {
        $this->resetAfterTest();

        $component = 'core_repository';
        // Create user1.
        $user1 = $this->getDataGenerator()->create_user();
        $this->setUser($user1);
        $usercontext1 = \context_user::instance($user1->id);
        // Create list of users with a related user data in usercontext1.
        $userlist1 = new \core_privacy\local\request\userlist($usercontext1, $component);

        // Create a user2.
        $user2 = $this->getDataGenerator()->create_user();
        $this->setUser($user2);
        $usercontext2 = \context_user::instance($user2->id);
        // Create list of users with a related user data in usercontext2.
        $userlist2 = new \core_privacy\local\request\userlist($usercontext2, $component);

        // Create repository_instances record for user1.
        $this->setup_test_scenario_data($user1->id, 1);
        // Create repository_instances record for user2.
        $this->setup_test_scenario_data($user2->id, 1);

        // Ensure the user list for usercontext1 contains user1.
        provider::get_users_in_context($userlist1);
        $this->assertCount(1, $userlist1);
        // Ensure the user list for usercontext2 contains user2.
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);

        // Convert $userlist1 into an approved_contextlist.
        $approvedlist = new approved_userlist($usercontext1, $component, $userlist1->get_userids());

        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist);

        // Re-fetch users in the usercontext1 - The user list should now be empty.
        $userlist1 = new \core_privacy\local\request\userlist($usercontext1, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(0, $userlist1);
        // Re-fetch users in the usercontext2 - The user list should not be empty.
        $userlist2 = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);

        // User data should be only removed in the user context.
        $systemcontext = \context_system::instance();
        // Add userlist2 to the approved user list in the system context.
        $approvedlist = new approved_userlist($systemcontext, $component, $userlist2->get_userids());
        // Delete user1 data using delete_data_for_user.
        provider::delete_data_for_users($approvedlist);
        // Re-fetch users in usercontext2 - The user list should not be empty (user2).
        $userlist2 = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);
    }

    /**
     * Helper function to setup repository_instances records for testing a specific user.
     *
     * @param int $userid       The Id of the User used for testing.
     * @param int $noscenarios  The number of repository_instance records to create for the User.
     * @throws dml_exception
     */
    private function setup_test_scenario_data($userid, $noscenarios) {
        global $DB;

        for ($i = 0; $i < $noscenarios; $i++) {
            $repositoryinstance = (object)[
                'typeid' => ($i + 1),
                'name' => 'My Test Repo',
                'userid' => $userid,
                'contextid' => 1,
                'username' => 'some username',
                'password' => 'some password',
                'timecreated' => date('u'),
                'timemodified' => date('u'),
                'readonly' => 0
            ];
            $DB->insert_record('repository_instances', $repositoryinstance);
        }
    }

}
