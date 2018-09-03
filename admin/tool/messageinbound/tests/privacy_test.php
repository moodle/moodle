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

defined('MOODLE_INTERNAL') || die();
global $CFG;

use core_privacy\tests\provider_testcase;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;
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
class tool_messageinbound_privacy_testcase extends provider_testcase {

    public function setUp() {
        global $CFG;
        $this->resetAfterTest();

        // Pretend the system is enabled.
        $CFG->messageinbound_enabled = true;
        $CFG->messageinbound_mailbox = 'mailbox';
        $CFG->messageinbound_domain = 'example.com';
    }

    public function test_get_contexts_for_userid() {
        $dg = $this->getDataGenerator();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u1ctx = context_user::instance($u1->id);
        $u2ctx = context_user::instance($u2->id);

        $contexts = provider::get_contexts_for_userid($u1->id)->get_contexts();
        $this->assertCount(1, $contexts);
        $this->assertEquals($u1ctx->id, $contexts[0]->id);

        $contexts = provider::get_contexts_for_userid($u2->id)->get_contexts();
        $this->assertCount(1, $contexts);
        $this->assertEquals($u2ctx->id, $contexts[0]->id);
    }

    public function test_delete_data_for_user() {
        global $DB;
        $dg = $this->getDataGenerator();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u1ctx = context_user::instance($u1->id);
        $u2ctx = context_user::instance($u2->id);

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

    public function test_delete_data_for_all_users_in_context() {
        global $DB;
        $dg = $this->getDataGenerator();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u1ctx = context_user::instance($u1->id);
        $u2ctx = context_user::instance($u2->id);

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

    public function test_export_data_for_user() {
        $dg = $this->getDataGenerator();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u1ctx = context_user::instance($u1->id);
        $u2ctx = context_user::instance($u2->id);

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
