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
 * Base class for unit tests for tool_mobile.
 *
 * @package    tool_mobile
 * @category   test
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_mobile\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\request\writer;
use core_privacy\local\request\transform;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use tool_mobile\privacy\provider;

/**
 * Unit tests for the tool_mobile implementation of the privacy API.
 *
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends \core_privacy\tests\provider_testcase {

    /**
     * Basic setup for these tests.
     */
    public function setUp(): void {
        $this->resetAfterTest(true);
    }

    /**
     * Test to check export_user_preferences.
     * returns user preferences data.
     */
    public function test_export_user_preferences() {
        $user = $this->getDataGenerator()->create_user();
        $expectedtime = time();
        set_user_preference('tool_mobile_autologin_request_last', time(), $user);
        provider::export_user_preferences($user->id);
        $writer = writer::with_context(\context_system::instance());
        $prefs = $writer->get_user_preferences('tool_mobile');
        $time = transform::datetime($expectedtime);
        $this->assertEquals($time, $prefs->tool_mobile_autologin_request_last->value);
        $this->assertEquals(get_string('privacy:metadata:preference:tool_mobile_autologin_request_last', 'tool_mobile'),
            $prefs->tool_mobile_autologin_request_last->description);
    }

    /**
     * Test getting the context for the user ID related to this plugin.
     */
    public function test_get_contexts_for_userid() {
        // Create user and Mobile user keys.
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $key = get_user_key('tool_mobile', $user->id);
        $contextlist = provider::get_contexts_for_userid($user->id);
        $this->assertEquals($context->id, $contextlist->current()->id);
    }

    /**
     * Test getting the users for a context related to this plugin.
     */
    public function test_get_users_in_context() {
        $component = 'tool_mobile';

        // Create users and Mobile user keys.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $context1 = \context_user::instance($user1->id);
        $context2 = \context_user::instance($user2->id);
        $key1 = get_user_key('tool_mobile', $user1->id);
        $key2 = get_user_key('tool_mobile', $user2->id);

        // Ensure only user1 is found in context1.
        $userlist = new \core_privacy\local\request\userlist($context1, $component);
        provider::get_users_in_context($userlist);
        $userids = $userlist->get_userids();
        $userid = reset($userids);

        $this->assertCount(1, $userids);
        $this->assertEquals($user1->id, $userid);
    }

    /**
     * Test that data is exported correctly for this plugin.
     */
    public function test_export_user_data() {
        global $DB;
        // Create user and Mobile user keys.
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $keyvalue = get_user_key('tool_mobile', $user->id);
        $key = $DB->get_record('user_private_key', ['value' => $keyvalue]);
        // Validate exported data.
        $this->setUser($user);
        $writer = writer::with_context($context);
        $this->assertFalse($writer->has_any_data());
        $this->export_context_data_for_user($user->id, $context, 'tool_mobile');
        $userkeydata = $writer->get_related_data([], 'userkeys');
        $this->assertCount(1, $userkeydata->keys);
        $this->assertEquals($key->script, reset($userkeydata->keys)->script);
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;
        // Create user and Mobile user keys.
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $keyvalue = get_user_key('tool_mobile', $user->id);
        $key = $DB->get_record('user_private_key', ['value' => $keyvalue]);
        // Before deletion, we should have 1 user_private_key.
        $count = $DB->count_records('user_private_key', ['script' => 'tool_mobile']);
        $this->assertEquals(1, $count);
        // Delete data.
        provider::delete_data_for_all_users_in_context($context);
        // After deletion, the user_private_key entries should have been deleted.
        $count = $DB->count_records('user_private_key', ['script' => 'tool_mobile']);
        $this->assertEquals(0, $count);
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user() {
        global $DB;
        // Create user and Mobile user keys.
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $keyvalue = get_user_key('tool_mobile', $user->id);
        $key = $DB->get_record('user_private_key', ['value' => $keyvalue]);
        // Before deletion, we should have 1 user_private_key.
        $count = $DB->count_records('user_private_key', ['script' => 'tool_mobile']);
        $this->assertEquals(1, $count);
        // Delete data.
        $contextlist = provider::get_contexts_for_userid($user->id);
        $approvedcontextlist = new approved_contextlist($user, 'tool_mobile', $contextlist->get_contextids());
        provider::delete_data_for_user($approvedcontextlist);
        // After deletion, the user_private_key entries should have been deleted.
        $count = $DB->count_records('user_private_key', ['script' => 'tool_mobile']);
        $this->assertEquals(0, $count);
    }

    /**
     * Test for provider::test_delete_data_for_users().
     */
    public function test_delete_data_for_users() {
        global $DB;
        $component = 'tool_mobile';

        // Create users and Mobile user keys.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $context1 = \context_user::instance($user1->id);
        $context2 = \context_user::instance($user2->id);
        $keyvalue1 = get_user_key('tool_mobile', $user1->id);
        $keyvalue2 = get_user_key('tool_mobile', $user2->id);
        $key1 = $DB->get_record('user_private_key', ['value' => $keyvalue1]);

        // Before deletion, we should have 2 user_private_keys.
        $count = $DB->count_records('user_private_key', ['script' => 'tool_mobile']);
        $this->assertEquals(2, $count);

        // Ensure deleting wrong user in the user context does nothing.
        $approveduserids = [$user2->id];
        $approvedlist = new approved_userlist($context1, $component, $approveduserids);
        provider::delete_data_for_users($approvedlist);

        $count = $DB->count_records('user_private_key', ['script' => 'tool_mobile']);
        $this->assertEquals(2, $count);

        // Delete for user1 in context1.
        $approveduserids = [$user1->id];
        $approvedlist = new approved_userlist($context1, $component, $approveduserids);
        provider::delete_data_for_users($approvedlist);

        // Ensure only user1's data is deleted, user2's remains.
        $count = $DB->count_records('user_private_key', ['script' => 'tool_mobile']);
        $this->assertEquals(1, $count);

        $params = ['script' => $component];
        $userid = $DB->get_field_select('user_private_key', 'userid', 'script = :script', $params);
        $this->assertEquals($user2->id, $userid);
    }
}
