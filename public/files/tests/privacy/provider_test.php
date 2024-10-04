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
 * Base class for unit tests for core_cohort.
 *
 * @package    core_files
 * @category   test
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_files\privacy;

defined('MOODLE_INTERNAL') || die();

use core_files\privacy\provider;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\writer;
use core_privacy\tests\provider_testcase;
use core_privacy\local\request\approved_userlist;

/**
 * Unit tests for files\classes\privacy\provider.php
 *
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class provider_test extends provider_testcase {

    /**
     * Test getting the context for the user ID related to this plugin.
     */
    public function test_get_contexts_for_userid(): void {
        $this->resetAfterTest();

        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $userctx = \context_user::instance($user->id);

        create_user_key('core_files', $user->id);

        $contextlist = provider::get_contexts_for_userid($user->id);
        $this->assertCount(1, (array) $contextlist->get_contextids());
        $this->assertContainsEquals($userctx->id, $contextlist->get_contextids());
    }

    /**
     * Test that data is exported correctly for this plugin.
     */
    public function test_export_user_data(): void {
        global $DB;

        $this->resetAfterTest();

        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $usercontext = \context_user::instance($user->id);

        $keyvalue = get_user_key('core_files', $user->id);
        $key = $DB->get_record('user_private_key', ['value' => $keyvalue]);

        // Validate exported data.
        $this->setUser($user);
        $writer = writer::with_context($usercontext);
        $this->assertFalse($writer->has_any_data());
        $this->export_context_data_for_user($user->id, $usercontext, 'core_files');
        $subcontext = [
            get_string('files')
        ];
        $userkeydata = $writer->get_related_data($subcontext, 'userkeys');
        $this->assertCount(1, $userkeydata->keys);
        $this->assertEquals($key->script, reset($userkeydata->keys)->script);
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context(): void {
        global $DB;

        $this->resetAfterTest();

        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $usercontext = \context_user::instance($user->id);

        create_user_key('core_files', $user->id);

        // Before deletion, we should have 1 user_private_key.
        $count = $DB->count_records('user_private_key', ['script' => 'core_files']);
        $this->assertEquals(1, $count);

        // Delete data.
        provider::delete_data_for_all_users_in_context($usercontext);

        // After deletion, the user_private_key entries should have been deleted.
        $count = $DB->count_records('user_private_key', ['script' => 'core_files']);
        $this->assertEquals(0, $count);
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user(): void {
        global $DB;

        $this->resetAfterTest();

        // Create a user.
        $user = $this->getDataGenerator()->create_user();

        create_user_key('core_files', $user->id);

        // Before deletion, we should have 1 user_private_key.
        $count = $DB->count_records('user_private_key', ['script' => 'core_files']);
        $this->assertEquals(1, $count);

        // Delete data.
        $contextlist = provider::get_contexts_for_userid($user->id);
        $approvedcontextlist = new approved_contextlist($user, 'core_files', $contextlist->get_contextids());

        provider::delete_data_for_user($approvedcontextlist);
        // After deletion, the user_private_key entries should have been deleted.
        $count = $DB->count_records('user_private_key', ['script' => 'core_files']);
        $this->assertEquals(0, $count);
    }

    /**
     * Test that only users within a course context are fetched.
     */
    public function test_get_users_in_context(): void {
        $this->resetAfterTest();

        $component = 'core_files';

        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $userctx = \context_user::instance($user->id);

        $userlist = new \core_privacy\local\request\userlist($userctx, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);

        create_user_key('core_files', $user->id);

        // The list of users within the userctx context should contain user.
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $expected = [$user->id];
        $actual = $userlist->get_userids();
        $this->assertEquals($expected, $actual);

        // The list of users within contexts different than user should be empty.
        $systemctx = \context_system::instance();
        $userlist = new \core_privacy\local\request\userlist($systemctx, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);
    }

    /**
     * Test that data for users in approved userlist is deleted.
     */
    public function test_delete_data_for_users(): void {
        $this->resetAfterTest();

        $component = 'core_files';

        // Create user1.
        $user1 = $this->getDataGenerator()->create_user();
        $userctx1 = \context_user::instance($user1->id);
        // Create user2.
        $user2 = $this->getDataGenerator()->create_user();
        $userctx2 = \context_user::instance($user2->id);

        create_user_key('core_files', $user1->id);
        create_user_key('core_files', $user2->id);

        $userlist1 = new \core_privacy\local\request\userlist($userctx1, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(1, $userlist1);

        $userlist2 = new \core_privacy\local\request\userlist($userctx2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);

        // Convert $userlist1 into an approved_contextlist.
        $approvedlist1 = new approved_userlist($userctx1, $component, $userlist1->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist1);

        // Re-fetch users in userctx1.
        $userlist1 = new \core_privacy\local\request\userlist($userctx1, $component);
        provider::get_users_in_context($userlist1);
        // The user data in coursecategoryctx should be deleted.
        $this->assertCount(0, $userlist1);

        // Re-fetch users in userctx2.
        $userlist2 = new \core_privacy\local\request\userlist($userctx2, $component);
        provider::get_users_in_context($userlist2);
        // The user data in userctx2 should be still present.
        $this->assertCount(1, $userlist2);

        // Convert $userlist2 into an approved_contextlist in the system context.
        $systemctx = \context_system::instance();
        $approvedlist3 = new approved_userlist($systemctx, $component, $userlist2->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist3);
        // Re-fetch users in userctx2.
        $userlist3 = new \core_privacy\local\request\userlist($userctx2, $component);
        provider::get_users_in_context($userlist3);
        // The user data in userctx2 should not be deleted.
        $this->assertCount(1, $userlist3);
    }
}
