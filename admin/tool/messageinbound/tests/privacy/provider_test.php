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
 * Data provider tests.
 *
 * @package    tool_messageinbound
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_messageinbound\privacy;

defined('MOODLE_INTERNAL') || die();
global $CFG;

use core_privacy\tests\provider_testcase;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use tool_messageinbound\privacy\provider;

/**
 * Data provider testcase class.
 *
 * @package    tool_messageinbound
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends provider_testcase {

    public function setUp(): void {
        global $CFG;
        parent::setUp();
        $this->resetAfterTest();

        // Pretend the system is enabled.
        $CFG->messageinbound_enabled = true;
        $CFG->messageinbound_mailbox = 'mailbox';
        $CFG->messageinbound_domain = 'example.com';
    }

    public function test_get_contexts_for_userid(): void {
        $dg = $this->getDataGenerator();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u1ctx = \context_user::instance($u1->id);
        $u2ctx = \context_user::instance($u2->id);

        $contexts = provider::get_contexts_for_userid($u1->id)->get_contexts();
        $this->assertCount(1, $contexts);
        $this->assertEquals($u1ctx->id, $contexts[0]->id);

        $contexts = provider::get_contexts_for_userid($u2->id)->get_contexts();
        $this->assertCount(1, $contexts);
        $this->assertEquals($u2ctx->id, $contexts[0]->id);
    }

    /**
     * Test for provider::test_get_users_in_context().
     */
    public function test_get_users_in_context(): void {
        $component = 'tool_messageinbound';
        $dg = $this->getDataGenerator();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u1ctx = \context_user::instance($u1->id);
        $u2ctx = \context_user::instance($u2->id);
        $u3ctx = \context_user::instance($u3->id);

        $addressmanager = new \core\message\inbound\address_manager();
        $addressmanager->set_handler('\tool_messageinbound\message\inbound\invalid_recipient_handler');
        $addressmanager->set_data(123);

        // Create a user key for user 1.
        $addressmanager->generate($u1->id);

        // Create a messagelist for user 2.
        $this->create_messagelist(['userid' => $u2->id, 'address' => 'u2@example1.com']);

        $userlist1 = new userlist($u1ctx, $component);
        provider::get_users_in_context($userlist1);
        $userlist2 = new userlist($u2ctx, $component);
        provider::get_users_in_context($userlist2);
        $userlist3 = new userlist($u3ctx, $component);
        provider::get_users_in_context($userlist3);

        // Ensure user 1 is found from userkey.
        $userids = $userlist1->get_userids();
        $this->assertCount(1, $userids);
        $this->assertEquals($u1->id, $userids[0]);

        // Ensure user 2 is found from messagelist.
        $userids = $userlist2->get_userids();
        $this->assertCount(1, $userids);
        $this->assertEquals($u2->id, $userids[0]);

        // User 3 has neither, so should not be found.
        $userids = $userlist3->get_userids();
        $this->assertCount(0, $userids);
    }

    public function test_delete_data_for_user(): void {
        global $DB;
        $dg = $this->getDataGenerator();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u1ctx = \context_user::instance($u1->id);
        $u2ctx = \context_user::instance($u2->id);

        $addressmanager = new \core\message\inbound\address_manager();
        $addressmanager->set_handler('\tool_messageinbound\message\inbound\invalid_recipient_handler');
        $addressmanager->set_data(123);

        // Create a user key for both users.
        $addressmanager->generate($u1->id);
        $addressmanager->generate($u2->id);

        // Create a messagelist for both users.
        $this->create_messagelist(['userid' => $u1->id]);
        $this->create_messagelist(['userid' => $u2->id]);

        $this->assertTrue($DB->record_exists('user_private_key', ['userid' => $u1->id, 'script' => 'messageinbound_handler']));
        $this->assertTrue($DB->record_exists('user_private_key', ['userid' => $u2->id, 'script' => 'messageinbound_handler']));
        $this->assertTrue($DB->record_exists('messageinbound_messagelist', ['userid' => $u1->id]));
        $this->assertTrue($DB->record_exists('messageinbound_messagelist', ['userid' => $u2->id]));

        // Passing another user's context does not do anything.
        provider::delete_data_for_user(new approved_contextlist($u1, 'tool_messageinbound', [$u2ctx->id]));
        $this->assertTrue($DB->record_exists('user_private_key', ['userid' => $u1->id, 'script' => 'messageinbound_handler']));
        $this->assertTrue($DB->record_exists('user_private_key', ['userid' => $u2->id, 'script' => 'messageinbound_handler']));
        $this->assertTrue($DB->record_exists('messageinbound_messagelist', ['userid' => $u1->id]));
        $this->assertTrue($DB->record_exists('messageinbound_messagelist', ['userid' => $u2->id]));

        // Deleting user 1.
        provider::delete_data_for_user(new approved_contextlist($u1, 'tool_messageinbound', [$u1ctx->id]));
        $this->assertFalse($DB->record_exists('user_private_key', ['userid' => $u1->id, 'script' => 'messageinbound_handler']));
        $this->assertTrue($DB->record_exists('user_private_key', ['userid' => $u2->id, 'script' => 'messageinbound_handler']));
        $this->assertFalse($DB->record_exists('messageinbound_messagelist', ['userid' => $u1->id]));
        $this->assertTrue($DB->record_exists('messageinbound_messagelist', ['userid' => $u2->id]));
    }

    /**
     * Test for provider::test_delete_data_for_users().
     */
    public function test_delete_data_for_users(): void {
        global $DB;
        $component = 'tool_messageinbound';
        $dg = $this->getDataGenerator();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u1ctx = \context_user::instance($u1->id);
        $u2ctx = \context_user::instance($u2->id);

        $addressmanager = new \core\message\inbound\address_manager();
        $addressmanager->set_handler('\tool_messageinbound\message\inbound\invalid_recipient_handler');
        $addressmanager->set_data(123);

        // Create a user key for both users.
        $addressmanager->generate($u1->id);
        $addressmanager->generate($u2->id);

        // Create a messagelist for both users.
        $this->create_messagelist(['userid' => $u1->id]);
        $this->create_messagelist(['userid' => $u2->id]);

        // Ensure data exists for both users.
        $this->assertTrue($DB->record_exists('user_private_key', ['userid' => $u1->id, 'script' => 'messageinbound_handler']));
        $this->assertTrue($DB->record_exists('user_private_key', ['userid' => $u2->id, 'script' => 'messageinbound_handler']));
        $this->assertTrue($DB->record_exists('messageinbound_messagelist', ['userid' => $u1->id]));
        $this->assertTrue($DB->record_exists('messageinbound_messagelist', ['userid' => $u2->id]));

        // Ensure passing another user's ID does not do anything.
        $approveduserids = [$u2->id];
        $approvedlist = new approved_userlist($u1ctx, $component, $approveduserids);
        provider::delete_data_for_users($approvedlist);

        $this->assertTrue($DB->record_exists('user_private_key', ['userid' => $u1->id, 'script' => 'messageinbound_handler']));
        $this->assertTrue($DB->record_exists('user_private_key', ['userid' => $u2->id, 'script' => 'messageinbound_handler']));
        $this->assertTrue($DB->record_exists('messageinbound_messagelist', ['userid' => $u1->id]));
        $this->assertTrue($DB->record_exists('messageinbound_messagelist', ['userid' => $u2->id]));

        // Delete u1's data.
        $approveduserids = [$u1->id];
        $approvedlist = new approved_userlist($u1ctx, $component, $approveduserids);
        provider::delete_data_for_users($approvedlist);

        // Confirm only u1's data is deleted.
        $this->assertFalse($DB->record_exists('user_private_key', ['userid' => $u1->id, 'script' => 'messageinbound_handler']));
        $this->assertTrue($DB->record_exists('user_private_key', ['userid' => $u2->id, 'script' => 'messageinbound_handler']));
        $this->assertFalse($DB->record_exists('messageinbound_messagelist', ['userid' => $u1->id]));
        $this->assertTrue($DB->record_exists('messageinbound_messagelist', ['userid' => $u2->id]));
    }

    public function test_delete_data_for_all_users_in_context(): void {
        global $DB;
        $dg = $this->getDataGenerator();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u1ctx = \context_user::instance($u1->id);
        $u2ctx = \context_user::instance($u2->id);

        $addressmanager = new \core\message\inbound\address_manager();
        $addressmanager->set_handler('\tool_messageinbound\message\inbound\invalid_recipient_handler');
        $addressmanager->set_data(123);

        // Create a user key for both users.
        $addressmanager->generate($u1->id);
        $addressmanager->generate($u2->id);

        // Create a messagelist for both users.
        $this->create_messagelist(['userid' => $u1->id]);
        $this->create_messagelist(['userid' => $u2->id]);

        $this->assertTrue($DB->record_exists('user_private_key', ['userid' => $u1->id, 'script' => 'messageinbound_handler']));
        $this->assertTrue($DB->record_exists('user_private_key', ['userid' => $u2->id, 'script' => 'messageinbound_handler']));
        $this->assertTrue($DB->record_exists('messageinbound_messagelist', ['userid' => $u1->id]));
        $this->assertTrue($DB->record_exists('messageinbound_messagelist', ['userid' => $u2->id]));

        // Deleting user 1.
        provider::delete_data_for_all_users_in_context($u1ctx);
        $this->assertFalse($DB->record_exists('user_private_key', ['userid' => $u1->id, 'script' => 'messageinbound_handler']));
        $this->assertTrue($DB->record_exists('user_private_key', ['userid' => $u2->id, 'script' => 'messageinbound_handler']));
        $this->assertFalse($DB->record_exists('messageinbound_messagelist', ['userid' => $u1->id]));
        $this->assertTrue($DB->record_exists('messageinbound_messagelist', ['userid' => $u2->id]));
    }

    public function test_export_data_for_user(): void {
        $dg = $this->getDataGenerator();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u1ctx = \context_user::instance($u1->id);
        $u2ctx = \context_user::instance($u2->id);

        $addressmanager = new \core\message\inbound\address_manager();
        $addressmanager->set_handler('\tool_messageinbound\message\inbound\invalid_recipient_handler');
        $addressmanager->set_data(123);

        // Create a user key for both users.
        $addressmanager->generate($u1->id);
        $addressmanager->generate($u2->id);

        // Create a messagelist for both users.
        $this->create_messagelist(['userid' => $u1->id, 'address' => 'u1@example1.com']);
        $this->create_messagelist(['userid' => $u1->id, 'address' => 'u1@example2.com']);
        $this->create_messagelist(['userid' => $u2->id, 'address' => 'u2@example1.com']);

        // Export for user.
        $this->setUser($u1);
        provider::export_user_data(new approved_contextlist($u1, 'tool_messageinbound', [$u1ctx->id, $u2ctx->id]));
        $data = writer::with_context($u2ctx)->get_data([get_string('messageinbound', 'tool_messageinbound')]);
        $this->assertEmpty($data);
        $data = writer::with_context($u1ctx)->get_data([get_string('messageinbound', 'tool_messageinbound')]);
        $this->assertCount(2, $data->messages_pending_validation);
        $this->assertEquals('u1@example1.com', $data->messages_pending_validation[0]['received_at']);
        $this->assertEquals('u1@example2.com', $data->messages_pending_validation[1]['received_at']);

        $data = writer::with_context($u2ctx)->get_related_data([get_string('messageinbound', 'tool_messageinbound')], 'userkeys');
        $this->assertEmpty($data);
        $data = writer::with_context($u1ctx)->get_related_data([get_string('messageinbound', 'tool_messageinbound')], 'userkeys');
        $this->assertCount(1, $data->keys);
        $this->assertEquals('messageinbound_handler', $data->keys[0]->script);
    }

    /**
     * Create a message to validate.
     *
     * @param array $params The params.
     * @return stdClass
     */
    protected function create_messagelist(array $params) {
        global $DB, $USER;
        $record = (object) array_merge([
            'messageid' => 'abc',
            'userid' => $USER->id,
            'address' => 'text@example.com',
            'timecreated' => time(),
        ], $params);
        $record->id = $DB->insert_record('messageinbound_messagelist', $record);
        return $record;
    }

}
