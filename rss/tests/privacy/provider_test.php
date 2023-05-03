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
 * Base class for unit tests for core_rss.
 *
 * @package    core_rss
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_rss\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\tests\provider_testcase;
use core_rss\privacy\provider;
use core_privacy\local\request\writer;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;

/**
 * Unit tests for rss\classes\privacy\provider.php
 *
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends provider_testcase {

    /**
     * Basic setup for these tests.
     */
    public function setUp(): void {
        $this->resetAfterTest(true);
    }

    /**
     * Test getting the context for the user ID related to this plugin.
     */
    public function test_get_contexts_for_userid() {
        // Create user and RSS user keys.
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $key = get_user_key('rss', $user->id);

        $contextlist = provider::get_contexts_for_userid($user->id);
        $this->assertEquals($context->id, $contextlist->current()->id);
    }

    /**
     * Test that data is exported correctly for this plugin.
     */
    public function test_export_user_data() {
        global $DB;

        // Create user and RSS user keys.
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $keyvalue = get_user_key('rss', $user->id);
        $key = $DB->get_record('user_private_key', ['value' => $keyvalue]);

        // Validate exported data.
        $this->setUser($user);
        /** @var \core_privacy\tests\request\content_writer $writer */
        $writer = writer::with_context($context);
        $this->assertFalse($writer->has_any_data());
        $this->export_context_data_for_user($user->id, $context, 'core_rss');
        $userkeydata = $writer->get_related_data([], 'userkeys');
        $this->assertCount(1, $userkeydata->keys);
        $this->assertEquals($key->script, reset($userkeydata->keys)->script);
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        // Create user and RSS user keys.
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $keyvalue = get_user_key('rss', $user->id);
        $key = $DB->get_record('user_private_key', ['value' => $keyvalue]);

        // Before deletion, we should have 1 user_private_key.
        $count = $DB->count_records('user_private_key', ['script' => 'rss']);
        $this->assertEquals(1, $count);

        // Delete data.
        provider::delete_data_for_all_users_in_context($context);

        // After deletion, the user_private_key entries should have been deleted.
        $count = $DB->count_records('user_private_key', ['script' => 'rss']);
        $this->assertEquals(0, $count);
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user() {
        global $DB;

        // Create user and RSS user keys.
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $keyvalue = get_user_key('rss', $user->id);
        $key = $DB->get_record('user_private_key', ['value' => $keyvalue]);

        // Before deletion, we should have 1 user_private_key.
        $count = $DB->count_records('user_private_key', ['script' => 'rss']);
        $this->assertEquals(1, $count);

        // Delete data.
        $contextlist = provider::get_contexts_for_userid($user->id);
        $approvedcontextlist = new approved_contextlist($user, 'rss', $contextlist->get_contextids());
        provider::delete_data_for_user($approvedcontextlist);

        // After deletion, the user_private_key entries should have been deleted.
        $count = $DB->count_records('user_private_key', ['script' => 'rss']);
        $this->assertEquals(0, $count);
    }

    /**
     * Test that only users with a user context are fetched.
     */
    public function test_get_users_in_context() {
        $component = 'core_rss';
        // Create a user.
        $user = $this->getDataGenerator()->create_user();

        $usercontext = \context_user::instance($user->id);
        $userlist = new \core_privacy\local\request\userlist($usercontext, $component);
        // The list of users should not return anything yet (related data still haven't been created).
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);
        // Create private access key for user.
        get_user_key('rss', $user->id);

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
        $component = 'core_rss';
        // Create a user1.
        $user1 = $this->getDataGenerator()->create_user();
        $usercontext1 = \context_user::instance($user1->id);
        // Create list of users with a related user data in usercontext1.
        $userlist1 = new \core_privacy\local\request\userlist($usercontext1, $component);

        // Create a user1.
        $user2 = $this->getDataGenerator()->create_user();
        $usercontext2 = \context_user::instance($user2->id);
        // Create list of users with a related user data in usercontext2.
        $userlist2 = new \core_privacy\local\request\userlist($usercontext2, $component);

        // Create private access key for user1.
        get_user_key('rss', $user1->id);
        // Create private access key for user2.
        get_user_key('rss', $user2->id);

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

        // Re-fetch users in usercontext1 - The user list should now be empty.
        $userlist1 = new \core_privacy\local\request\userlist($usercontext1, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(0, $userlist1);
        // Re-fetch users in usercontext2 - The user list should not be empty (user2).
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
        $userlist1 = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(1, $userlist1);
    }
}
