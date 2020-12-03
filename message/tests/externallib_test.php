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
 * External message functions unit tests
 *
 * @package    core_message
 * @category   external
 * @copyright  2012 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/message/externallib.php');

use \core_message\tests\helper as testhelper;

class core_message_externallib_testcase extends externallib_advanced_testcase {

    /**
     * Tests set up
     */
    protected function setUp() {
        global $CFG;

        require_once($CFG->dirroot . '/message/lib.php');
    }

    /**
     * Send a fake message.
     *
     * {@link message_send()} does not support transaction, this function will simulate a message
     * sent from a user to another. We should stop using it once {@link message_send()} will support
     * transactions. This is not clean at all, this is just used to add rows to the table.
     *
     * @param stdClass $userfrom user object of the one sending the message.
     * @param stdClass $userto user object of the one receiving the message.
     * @param string $message message to send.
     * @param int $notification is the message a notification.
     * @param int $time the time the message was sent
     */
    protected function send_message($userfrom, $userto, $message = 'Hello world!', $notification = 0, $time = 0) {
        global $DB;

        if (empty($time)) {
            $time = time();
        }

        if ($notification) {
            $record = new stdClass();
            $record->useridfrom = $userfrom->id;
            $record->useridto = $userto->id;
            $record->subject = 'No subject';
            $record->fullmessage = $message;
            $record->smallmessage = $message;
            $record->timecreated = $time;

            return $DB->insert_record('notifications', $record);
        }

        if (!$conversationid = \core_message\api::get_conversation_between_users([$userfrom->id, $userto->id])) {
            $conversation = \core_message\api::create_conversation(
                \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
                [
                    $userfrom->id,
                    $userto->id
                ]
            );
            $conversationid = $conversation->id;
        }

        // Ok, send the message.
        $record = new stdClass();
        $record->useridfrom = $userfrom->id;
        $record->conversationid = $conversationid;
        $record->subject = 'No subject';
        $record->fullmessage = $message;
        $record->smallmessage = $message;
        $record->timecreated = $time;

        return $DB->insert_record('messages', $record);
    }

    /**
     * Test send_instant_messages.
     */
    public function test_send_instant_messages() {
        global $DB, $USER;

        $this->resetAfterTest();

        // Transactions used in tests, tell phpunit use alternative reset method.
        $this->preventResetByRollback();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        // Create test message data.
        $message1 = array();
        $message1['touserid'] = $user2->id;
        $message1['text'] = 'the message.';
        $message1['clientmsgid'] = 4;
        $messages = array($message1);

        $sentmessages = core_message_external::send_instant_messages($messages);
        $sentmessages = external_api::clean_returnvalue(core_message_external::send_instant_messages_returns(), $sentmessages);
        $this->assertEquals(
            get_string('usercantbemessaged', 'message', fullname(\core_user::get_user($message1['touserid']))),
            array_pop($sentmessages)['errormessage']
        );

        // Add the user1 as a contact.
        \core_message\api::add_contact($user1->id, $user2->id);

        // Send message again. Now it should work properly.
        $sentmessages = core_message_external::send_instant_messages($messages);
        // We need to execute the return values cleaning process to simulate the web service server.
        $sentmessages = external_api::clean_returnvalue(core_message_external::send_instant_messages_returns(), $sentmessages);

        $sentmessage = reset($sentmessages);

        $sql = "SELECT m.*, mcm.userid as useridto
                 FROM {messages} m
           INNER JOIN {message_conversations} mc
                   ON m.conversationid = mc.id
           INNER JOIN {message_conversation_members} mcm
                   ON mcm.conversationid = mc.id
                WHERE mcm.userid != ?
                  AND m.id = ?";
        $themessage = $DB->get_record_sql($sql, [$USER->id, $sentmessage['msgid']]);

        // Confirm that the message was inserted correctly.
        $this->assertEquals($themessage->useridfrom, $user1->id);
        $this->assertEquals($themessage->useridto, $message1['touserid']);
        $this->assertEquals($themessage->smallmessage, $message1['text']);
        $this->assertEquals($sentmessage['clientmsgid'], $message1['clientmsgid']);
    }

    /**
     * Test send_instant_messages to a user who has blocked you.
     */
    public function test_send_instant_messages_blocked_user() {
        global $DB;

        $this->resetAfterTest();

        // Transactions used in tests, tell phpunit use alternative reset method.
        $this->preventResetByRollback();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        \core_message\api::block_user($user2->id, $user1->id);

        // Create test message data.
        $message1 = array();
        $message1['touserid'] = $user2->id;
        $message1['text'] = 'the message.';
        $message1['clientmsgid'] = 4;
        $messages = array($message1);

        $sentmessages = core_message_external::send_instant_messages($messages);
        $sentmessages = external_api::clean_returnvalue(core_message_external::send_instant_messages_returns(), $sentmessages);

        $sentmessage = reset($sentmessages);

        $this->assertEquals(get_string('usercantbemessaged', 'message', fullname($user2)), $sentmessage['errormessage']);

        $this->assertEquals(0, $DB->count_records('messages'));
    }

    /**
     * Test send_instant_messages when sending a message to a non-contact who has blocked non-contacts.
     */
    public function test_send_instant_messages_block_non_contacts() {
        global $DB;

        $this->resetAfterTest(true);

        // Transactions used in tests, tell phpunit use alternative reset method.
        $this->preventResetByRollback();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        // Set the user preference so user 2 does not accept messages from non-contacts.
        set_user_preference('message_blocknoncontacts', \core_message\api::MESSAGE_PRIVACY_ONLYCONTACTS, $user2);

        // Create test message data.
        $message1 = array();
        $message1['touserid'] = $user2->id;
        $message1['text'] = 'the message.';
        $message1['clientmsgid'] = 4;
        $messages = array($message1);

        $sentmessages = core_message_external::send_instant_messages($messages);
        $sentmessages = external_api::clean_returnvalue(core_message_external::send_instant_messages_returns(), $sentmessages);

        $sentmessage = reset($sentmessages);

        $this->assertEquals(get_string('usercantbemessaged', 'message', fullname($user2)), $sentmessage['errormessage']);

        $this->assertEquals(0, $DB->count_records('messages'));
    }

    /**
     * Test send_instant_messages when sending a message to a contact who has blocked non-contacts.
     */
    public function test_send_instant_messages_block_non_contacts_but_am_contact() {
        global $DB, $USER;

        $this->resetAfterTest(true);

        // Transactions used in tests, tell phpunit use alternative reset method.
        $this->preventResetByRollback();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        // Set the user preference so user 2 does not accept messages from non-contacts.
        set_user_preference('message_blocknoncontacts', \core_message\api::MESSAGE_PRIVACY_ONLYCONTACTS, $user2);

        \core_message\api::add_contact($user1->id, $user2->id);

        // Create test message data.
        $message1 = array();
        $message1['touserid'] = $user2->id;
        $message1['text'] = 'the message.';
        $message1['clientmsgid'] = 4;
        $messages = array($message1);

        $sentmessages = core_message_external::send_instant_messages($messages);
        $sentmessages = external_api::clean_returnvalue(core_message_external::send_instant_messages_returns(), $sentmessages);

        $sentmessage = reset($sentmessages);

        $sql = "SELECT m.*, mcm.userid as useridto
                 FROM {messages} m
           INNER JOIN {message_conversations} mc
                   ON m.conversationid = mc.id
           INNER JOIN {message_conversation_members} mcm
                   ON mcm.conversationid = mc.id
                WHERE mcm.userid != ?
                  AND m.id = ?";
        $themessage = $DB->get_record_sql($sql, [$USER->id, $sentmessage['msgid']]);

        // Confirm that the message was inserted correctly.
        $this->assertEquals($themessage->useridfrom, $user1->id);
        $this->assertEquals($themessage->useridto, $message1['touserid']);
        $this->assertEquals($themessage->smallmessage, $message1['text']);
        $this->assertEquals($sentmessage['clientmsgid'], $message1['clientmsgid']);
    }

    /**
     * Test send_instant_messages with no capabilities
     */
    public function test_send_instant_messages_no_capability() {
        global $DB;

        $this->resetAfterTest(true);

        // Transactions used in tests, tell phpunit use alternative reset method.
        $this->preventResetByRollback();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        // Unset the required capabilities by the external function.
        $contextid = context_system::instance()->id;
        $userrole = $DB->get_record('role', array('shortname' => 'user'));
        $this->unassignUserCapability('moodle/site:sendmessage', $contextid, $userrole->id);

        // Create test message data.
        $message1 = array();
        $message1['touserid'] = $user2->id;
        $message1['text'] = 'the message.';
        $message1['clientmsgid'] = 4;
        $messages = array($message1);

        $this->expectException('required_capability_exception');
        core_message_external::send_instant_messages($messages);
    }

    /**
     * Test send_instant_messages when messaging is disabled.
     */
    public function test_send_instant_messages_messaging_disabled() {
        global $CFG;

        $this->resetAfterTest(true);

        // Transactions used in tests, tell phpunit use alternative reset method.
        $this->preventResetByRollback();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        // Disable messaging.
        $CFG->messaging = 0;

        // Create test message data.
        $message1 = array();
        $message1['touserid'] = $user2->id;
        $message1['text'] = 'the message.';
        $message1['clientmsgid'] = 4;
        $messages = array($message1);

        $this->expectException('moodle_exception');
        core_message_external::send_instant_messages($messages);
    }

    /**
     * Test create_contacts.
     */
    public function test_create_contacts() {
        $this->resetAfterTest(true);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();
        $user5 = self::getDataGenerator()->create_user();
        $this->setUser($user1);

        // Adding a contact.
        $return = core_message_external::create_contacts(array($user2->id));
        $this->assertDebuggingCalled();
        $return = external_api::clean_returnvalue(core_message_external::create_contacts_returns(), $return);
        $this->assertEquals(array(), $return);

        // Adding a contact who is already a contact.
        $return = core_message_external::create_contacts(array($user2->id));
        $this->assertDebuggingCalled();
        $return = external_api::clean_returnvalue(core_message_external::create_contacts_returns(), $return);
        $this->assertEquals(array(), $return);

        // Adding multiple contacts.
        $return = core_message_external::create_contacts(array($user3->id, $user4->id));
        $this->assertDebuggingCalledCount(2);
        $return = external_api::clean_returnvalue(core_message_external::create_contacts_returns(), $return);
        $this->assertEquals(array(), $return);

        // Adding a non-existing user.
        $return = core_message_external::create_contacts(array(99999));
        $this->assertDebuggingCalled();
        $return = external_api::clean_returnvalue(core_message_external::create_contacts_returns(), $return);
        $this->assertCount(1, $return);
        $return = array_pop($return);
        $this->assertEquals($return['warningcode'], 'contactnotcreated');
        $this->assertEquals($return['itemid'], 99999);

        // Adding contacts with valid and invalid parameters.
        $return = core_message_external::create_contacts(array($user5->id, 99999));
        $this->assertDebuggingCalledCount(2);
        $return = external_api::clean_returnvalue(core_message_external::create_contacts_returns(), $return);
        $this->assertCount(1, $return);
        $return = array_pop($return);
        $this->assertEquals($return['warningcode'], 'contactnotcreated');
        $this->assertEquals($return['itemid'], 99999);

        // Try to add a contact to another user, should throw an exception.
        // All assertions must be added before this point.
        $this->expectException('required_capability_exception');
        core_message_external::create_contacts(array($user2->id), $user3->id);
    }

    /**
     * Test delete_contacts.
     */
    public function test_delete_contacts() {
        $this->resetAfterTest(true);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();
        $user5 = self::getDataGenerator()->create_user();
        $user6 = self::getDataGenerator()->create_user();
        $this->setUser($user1);

        \core_message\api::add_contact($user1->id, $user3->id);
        \core_message\api::add_contact($user1->id, $user4->id);
        \core_message\api::add_contact($user1->id, $user5->id);
        \core_message\api::add_contact($user1->id, $user6->id);

        // Removing a non-contact.
        $return = core_message_external::delete_contacts(array($user2->id));
        $this->assertNull($return);

        // Removing one contact.
        $return = core_message_external::delete_contacts(array($user3->id));
        $this->assertNull($return);

        // Removing multiple contacts.
        $return = core_message_external::delete_contacts(array($user4->id, $user5->id));
        $this->assertNull($return);

        // Removing contact from unexisting user.
        $return = core_message_external::delete_contacts(array(99999));
        $this->assertNull($return);

        // Removing mixed valid and invalid data.
        $return = core_message_external::delete_contacts(array($user6->id, 99999));
        $this->assertNull($return);

        // Try to delete a contact of another user contact list, should throw an exception.
        // All assertions must be added before this point.
        $this->expectException('required_capability_exception');
        core_message_external::delete_contacts(array($user2->id), $user3->id);
    }

    /**
     * Test block_contacts.
     */
    public function test_block_contacts() {
        $this->resetAfterTest(true);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();
        $user5 = self::getDataGenerator()->create_user();
        $this->setUser($user1);

        \core_message\api::add_contact($user1->id, $user3->id);
        \core_message\api::add_contact($user1->id, $user4->id);
        \core_message\api::add_contact($user1->id, $user5->id);

        // Blocking a contact.
        $return = core_message_external::block_contacts(array($user2->id));
        $this->assertDebuggingCalled();
        $return = external_api::clean_returnvalue(core_message_external::block_contacts_returns(), $return);
        $this->assertEquals(array(), $return);

        // Blocking a contact who is already a contact.
        $return = core_message_external::block_contacts(array($user2->id));
        $this->assertDebuggingCalled();
        $return = external_api::clean_returnvalue(core_message_external::block_contacts_returns(), $return);
        $this->assertEquals(array(), $return);

        // Blocking multiple contacts.
        $return = core_message_external::block_contacts(array($user3->id, $user4->id));
        $this->assertDebuggingCalledCount(2);
        $return = external_api::clean_returnvalue(core_message_external::block_contacts_returns(), $return);
        $this->assertEquals(array(), $return);

        // Blocking a non-existing user.
        $return = core_message_external::block_contacts(array(99999));
        $this->assertDebuggingCalled();
        $return = external_api::clean_returnvalue(core_message_external::block_contacts_returns(), $return);
        $this->assertCount(1, $return);
        $return = array_pop($return);
        $this->assertEquals($return['warningcode'], 'contactnotblocked');
        $this->assertEquals($return['itemid'], 99999);

        // Blocking contacts with valid and invalid parameters.
        $return = core_message_external::block_contacts(array($user5->id, 99999));
        $this->assertDebuggingCalledCount(2);
        $return = external_api::clean_returnvalue(core_message_external::block_contacts_returns(), $return);
        $this->assertCount(1, $return);
        $return = array_pop($return);
        $this->assertEquals($return['warningcode'], 'contactnotblocked');
        $this->assertEquals($return['itemid'], 99999);

        // Try to block a contact of another user contact list, should throw an exception.
        // All assertions must be added before this point.
        $this->expectException('required_capability_exception');
        core_message_external::block_contacts(array($user2->id), $user3->id);
    }

    /**
     * Test unblock_contacts.
     */
    public function test_unblock_contacts() {
        $this->resetAfterTest(true);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();
        $user5 = self::getDataGenerator()->create_user();
        $user6 = self::getDataGenerator()->create_user();
        $this->setUser($user1);

        \core_message\api::add_contact($user1->id, $user3->id);
        \core_message\api::add_contact($user1->id, $user4->id);
        \core_message\api::add_contact($user1->id, $user5->id);
        \core_message\api::add_contact($user1->id, $user6->id);

        // Removing a non-contact.
        $return = core_message_external::unblock_contacts(array($user2->id));
        $this->assertDebuggingCalled();
        $this->assertNull($return);

        // Removing one contact.
        $return = core_message_external::unblock_contacts(array($user3->id));
        $this->assertDebuggingCalled();
        $this->assertNull($return);

        // Removing multiple contacts.
        $return = core_message_external::unblock_contacts(array($user4->id, $user5->id));
        $this->assertDebuggingCalledCount(2);
        $this->assertNull($return);

        // Removing contact from unexisting user.
        $return = core_message_external::unblock_contacts(array(99999));
        $this->assertDebuggingCalled();
        $this->assertNull($return);

        // Removing mixed valid and invalid data.
        $return = core_message_external::unblock_contacts(array($user6->id, 99999));
        $this->assertDebuggingCalledCount(2);
        $this->assertNull($return);

        // Try to unblock a contact of another user contact list, should throw an exception.
        // All assertions must be added before this point.
        $this->expectException('required_capability_exception');
        core_message_external::unblock_contacts(array($user2->id), $user3->id);
        $this->assertDebuggingCalled();
    }

    /**
     * Test getting contact requests.
     */
    public function test_get_contact_requests() {
        global $PAGE;

        $this->resetAfterTest();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        // Block one user, their request should not show up.
        \core_message\api::block_user($user1->id, $user3->id);

        \core_message\api::create_contact_request($user2->id, $user1->id);
        \core_message\api::create_contact_request($user3->id, $user1->id);

        $requests = core_message_external::get_contact_requests($user1->id);
        $requests = external_api::clean_returnvalue(core_message_external::get_contact_requests_returns(), $requests);

        $this->assertCount(1, $requests);

        $request = reset($requests);
        $userpicture = new \user_picture($user2);
        $profileimageurl = $userpicture->get_url($PAGE)->out(false);

        $this->assertEquals($user2->id, $request['id']);
        $this->assertEquals(fullname($user2), $request['fullname']);
        $this->assertArrayHasKey('profileimageurl', $request);
        $this->assertArrayHasKey('profileimageurlsmall', $request);
        $this->assertArrayHasKey('isonline', $request);
        $this->assertArrayHasKey('showonlinestatus', $request);
        $this->assertArrayHasKey('isblocked', $request);
        $this->assertArrayHasKey('iscontact', $request);
    }

    /**
     * Test the get_contact_requests() function when the user has blocked the sender of the request.
     */
    public function test_get_contact_requests_blocked_sender() {
        $this->resetAfterTest();
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // User1 blocks User2.
        \core_message\api::block_user($user1->id, $user2->id);

        // User2 tries to add User1 as a contact.
        \core_message\api::create_contact_request($user2->id, $user1->id);

        // Verify we don't see the contact request from the blocked user User2 in the requests for User1.
        $this->setUser($user1);
        $requests = core_message_external::get_contact_requests($user1->id);
        $requests = external_api::clean_returnvalue(core_message_external::get_contact_requests_returns(), $requests);

        $this->assertCount(0, $requests);
    }

    /**
     * Test getting contact requests when there are none.
     */
    public function test_get_contact_requests_no_requests() {
        $this->resetAfterTest();

        $user1 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        $requests = core_message_external::get_contact_requests($user1->id);
        $requests = external_api::clean_returnvalue(core_message_external::get_contact_requests_returns(), $requests);

        $this->assertEmpty($requests);
    }

    /**
     * Test getting contact requests with limits.
     */
    public function test_get_contact_requests_with_limits() {
        $this->resetAfterTest();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        \core_message\api::create_contact_request($user2->id, $user1->id);
        \core_message\api::create_contact_request($user3->id, $user1->id);

        $requests = core_message_external::get_contact_requests($user1->id, 0, 1);
        $requests = external_api::clean_returnvalue(core_message_external::get_contact_requests_returns(), $requests);

        $this->assertCount(1, $requests);
    }

    /**
     * Test getting contact requests with messaging disabled.
     */
    public function test_get_contact_requests_messaging_disabled() {
        global $CFG;

        $this->resetAfterTest();

        // Create some skeleton data just so we can call the WS.
        $user1 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        // Disable messaging.
        $CFG->messaging = 0;

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::get_contact_requests($user1->id);
    }

    /**
     * Test getting contact requests with no permission.
     */
    public function test_get_contact_requests_no_permission() {
        $this->resetAfterTest();

        // Create some skeleton data just so we can call the WS.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        $this->setUser($user3);

        // Ensure an exception is thrown.
        $this->expectException('required_capability_exception');
        core_message_external::create_contact_request($user1->id, $user2->id);
    }

    /**
     * Test getting the number of received contact requests.
     */
    public function test_get_received_contact_requests_count() {
        $this->resetAfterTest();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        $contactrequestnumber = core_message_external::get_received_contact_requests_count($user1->id);
        $contactrequestnumber = external_api::clean_returnvalue(
            core_message_external::get_received_contact_requests_count_returns(), $contactrequestnumber);
        $this->assertEquals(0, $contactrequestnumber);

        \core_message\api::create_contact_request($user2->id, $user1->id);

        $contactrequestnumber = core_message_external::get_received_contact_requests_count($user1->id);
        $contactrequestnumber = external_api::clean_returnvalue(
            core_message_external::get_received_contact_requests_count_returns(), $contactrequestnumber);
        $this->assertEquals(1, $contactrequestnumber);

        \core_message\api::create_contact_request($user3->id, $user1->id);

        $contactrequestnumber = core_message_external::get_received_contact_requests_count($user1->id);
        $contactrequestnumber = external_api::clean_returnvalue(
            core_message_external::get_received_contact_requests_count_returns(), $contactrequestnumber);
        $this->assertEquals(2, $contactrequestnumber);

        \core_message\api::create_contact_request($user1->id, $user4->id);

        // Web service should ignore sent requests.
        $contactrequestnumber = core_message_external::get_received_contact_requests_count($user1->id);
        $contactrequestnumber = external_api::clean_returnvalue(
            core_message_external::get_received_contact_requests_count_returns(), $contactrequestnumber);
        $this->assertEquals(2, $contactrequestnumber);
    }

    /**
     * Test the get_received_contact_requests_count() function when the user has blocked the sender of the request.
     */
    public function test_get_received_contact_requests_count_blocked_sender() {
        $this->resetAfterTest();
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // User1 blocks User2.
        \core_message\api::block_user($user1->id, $user2->id);

        // User2 tries to add User1 as a contact.
        \core_message\api::create_contact_request($user2->id, $user1->id);

        // Verify we don't see the contact request from the blocked user User2 in the count for User1.
        $this->setUser($user1);
        $contactrequestnumber = core_message_external::get_received_contact_requests_count($user1->id);
        $contactrequestnumber = external_api::clean_returnvalue(
            core_message_external::get_received_contact_requests_count_returns(), $contactrequestnumber);
        $this->assertEquals(0, $contactrequestnumber);
    }

    /**
     * Test getting the number of received contact requests with no permissions.
     */
    public function test_get_received_contact_requests_count_no_permission() {
        $this->resetAfterTest();

        // Create some skeleton data just so we can call the WS.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $this->setUser($user2);

        // Ensure an exception is thrown.
        $this->expectException('required_capability_exception');
        core_message_external::get_received_contact_requests_count($user1->id);
    }

    /**
     * Test getting the number of received contact requests with messaging disabled.
     */
    public function test_get_received_contact_requests_count_messaging_disabled() {
        global $CFG;

        $this->resetAfterTest();

        // Create some skeleton data just so we can call the WS.
        $user1 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        // Disable messaging.
        $CFG->messaging = 0;

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::get_received_contact_requests_count($user1->id);
    }

    /**
     * Test creating a contact request.
     */
    public function test_create_contact_request() {
        global $CFG, $DB;

        $this->resetAfterTest();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        // Allow users to message anyone site-wide.
        $CFG->messagingallusers = 1;

        $return = core_message_external::create_contact_request($user1->id, $user2->id);
        $return = external_api::clean_returnvalue(core_message_external::create_contact_request_returns(), $return);
        $this->assertEquals([], $return['warnings']);

        $request = $DB->get_records('message_contact_requests');

        $this->assertCount(1, $request);

        $request = reset($request);

        $this->assertEquals($request->id, $return['request']['id']);
        $this->assertEquals($request->userid, $return['request']['userid']);
        $this->assertEquals($request->requesteduserid, $return['request']['requesteduserid']);
        $this->assertEquals($request->timecreated, $return['request']['timecreated']);
    }

    /**
     * Test creating a contact request when not allowed.
     */
    public function test_create_contact_request_not_allowed() {
        global $CFG;

        $this->resetAfterTest();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        $CFG->messagingallusers = 0;

        $return = core_message_external::create_contact_request($user1->id, $user2->id);
        $return = external_api::clean_returnvalue(core_message_external::create_contact_request_returns(), $return);

        $warning = reset($return['warnings']);

        $this->assertEquals('user', $warning['item']);
        $this->assertEquals($user2->id, $warning['itemid']);
        $this->assertEquals('cannotcreatecontactrequest', $warning['warningcode']);
        $this->assertEquals('You are unable to create a contact request for this user', $warning['message']);
    }

    /**
     * Test creating a contact request with messaging disabled.
     */
    public function test_create_contact_request_messaging_disabled() {
        global $CFG;

        $this->resetAfterTest();

        // Create some skeleton data just so we can call the WS.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        // Disable messaging.
        $CFG->messaging = 0;

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::create_contact_request($user1->id, $user2->id);
    }

    /**
     * Test creating a contact request with no permission.
     */
    public function test_create_contact_request_no_permission() {
        $this->resetAfterTest();

        // Create some skeleton data just so we can call the WS.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        $this->setUser($user3);

        // Ensure an exception is thrown.
        $this->expectException('required_capability_exception');
        core_message_external::create_contact_request($user1->id, $user2->id);
    }

    /**
     * Test confirming a contact request.
     */
    public function test_confirm_contact_request() {
        global $DB;

        $this->resetAfterTest();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        \core_message\api::create_contact_request($user1->id, $user2->id);

        $this->setUser($user2);

        $return = core_message_external::confirm_contact_request($user1->id, $user2->id);
        $return = external_api::clean_returnvalue(core_message_external::confirm_contact_request_returns(), $return);
        $this->assertEquals(array(), $return);

        $this->assertEquals(0, $DB->count_records('message_contact_requests'));

        $contact = $DB->get_records('message_contacts');

        $this->assertCount(1, $contact);

        $contact = reset($contact);

        $this->assertEquals($user1->id, $contact->userid);
        $this->assertEquals($user2->id, $contact->contactid);
    }

    /**
     * Test confirming a contact request with messaging disabled.
     */
    public function test_confirm_contact_request_messaging_disabled() {
        global $CFG;

        $this->resetAfterTest();

        // Create some skeleton data just so we can call the WS.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        // Disable messaging.
        $CFG->messaging = 0;

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::confirm_contact_request($user1->id, $user2->id);
    }

    /**
     * Test confirming a contact request with no permission.
     */
    public function test_confirm_contact_request_no_permission() {
        $this->resetAfterTest();

        // Create some skeleton data just so we can call the WS.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        $this->setUser($user3);

        // Ensure an exception is thrown.
        $this->expectException('required_capability_exception');
        core_message_external::confirm_contact_request($user1->id, $user2->id);
    }

    /**
     * Test declining a contact request.
     */
    public function test_decline_contact_request() {
        global $DB;

        $this->resetAfterTest();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        \core_message\api::create_contact_request($user1->id, $user2->id);

        $this->setUser($user2);

        $return = core_message_external::decline_contact_request($user1->id, $user2->id);
        $return = external_api::clean_returnvalue(core_message_external::decline_contact_request_returns(), $return);
        $this->assertEquals(array(), $return);

        $this->assertEquals(0, $DB->count_records('message_contact_requests'));
        $this->assertEquals(0, $DB->count_records('message_contacts'));
    }

    /**
     * Test declining a contact request with messaging disabled.
     */
    public function test_decline_contact_request_messaging_disabled() {
        global $CFG;

        $this->resetAfterTest();

        // Create some skeleton data just so we can call the WS.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        // Disable messaging.
        $CFG->messaging = 0;

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::decline_contact_request($user1->id, $user2->id);
    }

    /**
     * Test declining a contact request with no permission.
     */
    public function test_decline_contact_request_no_permission() {
        $this->resetAfterTest();

        // Create some skeleton data just so we can call the WS.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        $this->setUser($user3);

        // Ensure an exception is thrown.
        $this->expectException('required_capability_exception');
        core_message_external::decline_contact_request($user1->id, $user2->id);
    }

    /**
     * Test muting conversations.
     */
    public function test_mute_conversations() {
        global $DB;

        $this->resetAfterTest(true);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $conversation = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [$user1->id, $user2->id]);

        $this->setUser($user1);

        // Muting a conversation.
        $return = core_message_external::mute_conversations($user1->id, [$conversation->id]);
        $return = external_api::clean_returnvalue(core_message_external::mute_conversations_returns(), $return);
        $this->assertEquals(array(), $return);

        // Get list of muted conversations.
        $mca = $DB->get_record('message_conversation_actions', []);

        $this->assertEquals($user1->id, $mca->userid);
        $this->assertEquals($conversation->id, $mca->conversationid);
        $this->assertEquals(\core_message\api::CONVERSATION_ACTION_MUTED, $mca->action);

        // Muting a conversation that is already muted.
        $return = core_message_external::mute_conversations($user1->id, [$conversation->id]);
        $return = external_api::clean_returnvalue(core_message_external::mute_conversations_returns(), $return);
        $this->assertEquals(array(), $return);

        $this->assertEquals(1, $DB->count_records('message_conversation_actions'));
    }

    /**
     * Test muting a conversation with messaging disabled.
     */
    public function test_mute_conversations_messaging_disabled() {
        global $CFG;

        $this->resetAfterTest();

        // Create some skeleton data just so we can call the WS.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $conversation = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [$user1->id, $user2->id]);

        $this->setUser($user1);

        // Disable messaging.
        $CFG->messaging = 0;

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::mute_conversations($user1->id, [$conversation->id]);
    }

    /**
     * Test muting a conversation with no permission.
     */
    public function test_mute_conversations_no_permission() {
        $this->resetAfterTest();

        // Create some skeleton data just so we can call the WS.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        $conversation = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [$user1->id, $user2->id]);

        $this->setUser($user3);

        // Ensure an exception is thrown.
        $this->expectException('required_capability_exception');
        core_message_external::mute_conversations($user1->id, [$conversation->id]);
    }

    /**
     * Test unmuting conversations.
     */
    public function test_unmute_conversations() {
        global $DB;

        $this->resetAfterTest(true);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $conversation = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [$user1->id, $user2->id]);

        $this->setUser($user1);

        // Mute the conversation.
        \core_message\api::mute_conversation($user1->id, $conversation->id);

        // Unmuting a conversation.
        $return = core_message_external::unmute_conversations($user1->id, [$conversation->id]);
        $return = external_api::clean_returnvalue(core_message_external::unmute_conversations_returns(), $return);
        $this->assertEquals(array(), $return);

        $this->assertEquals(0, $DB->count_records('message_conversation_actions'));

        // Unmuting a conversation which is already unmuted.
        $return = core_message_external::unmute_conversations($user1->id, [$conversation->id]);
        $return = external_api::clean_returnvalue(core_message_external::unmute_conversations_returns(), $return);
        $this->assertEquals(array(), $return);

        $this->assertEquals(0, $DB->count_records('message_conversation_actions'));
    }

    /**
     * Test unmuting a conversation with messaging disabled.
     */
    public function test_unmute_conversation_messaging_disabled() {
        global $CFG;

        $this->resetAfterTest();

        // Create some skeleton data just so we can call the WS.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $conversation = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [$user1->id, $user2->id]);

        $this->setUser($user1);

        // Disable messaging.
        $CFG->messaging = 0;

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::unmute_conversations($user1->id, [$user2->id]);
    }

    /**
     * Test unmuting a conversation with no permission.
     */
    public function test_unmute_conversation_no_permission() {
        $this->resetAfterTest();

        // Create some skeleton data just so we can call the WS.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        $conversation = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [$user1->id, $user2->id]);

        $this->setUser($user3);

        // Ensure an exception is thrown.
        $this->expectException('required_capability_exception');
        core_message_external::unmute_conversations($user1->id, [$conversation->id]);
    }

    /**
     * Test blocking a user.
     */
    public function test_block_user() {
        global $DB;

        $this->resetAfterTest(true);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        // Blocking a user.
        $return = core_message_external::block_user($user1->id, $user2->id);
        $return = external_api::clean_returnvalue(core_message_external::block_user_returns(), $return);
        $this->assertEquals(array(), $return);

        // Get list of blocked users.
        $record = $DB->get_record('message_users_blocked', []);

        $this->assertEquals($user1->id, $record->userid);
        $this->assertEquals($user2->id, $record->blockeduserid);

        // Blocking a user who is already blocked.
        $return = core_message_external::block_user($user1->id, $user2->id);
        $return = external_api::clean_returnvalue(core_message_external::block_user_returns(), $return);
        $this->assertEquals(array(), $return);

        $this->assertEquals(1, $DB->count_records('message_users_blocked'));
    }

    /**
     * Test blocking a user.
     */
    public function test_block_user_when_ineffective() {
        global $DB;

        $this->resetAfterTest(true);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        $authenticateduser = $DB->get_record('role', array('shortname' => 'user'));
        assign_capability('moodle/site:messageanyuser', CAP_ALLOW, $authenticateduser->id, context_system::instance(), true);

        // Blocking a user.
        $return = core_message_external::block_user($user1->id, $user2->id);
        $return = external_api::clean_returnvalue(core_message_external::block_user_returns(), $return);
        $this->assertEquals(array(), $return);

        $this->assertEquals(0, $DB->count_records('message_users_blocked'));
    }

    /**
     * Test blocking a user with messaging disabled.
     */
    public function test_block_user_messaging_disabled() {
        global $CFG;

        $this->resetAfterTest();

        // Create some skeleton data just so we can call the WS.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        // Disable messaging.
        $CFG->messaging = 0;

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::block_user($user1->id, $user2->id);
    }

    /**
     * Test blocking a user with no permission.
     */
    public function test_block_user_no_permission() {
        $this->resetAfterTest();

        // Create some skeleton data just so we can call the WS.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        $this->setUser($user3);

        // Ensure an exception is thrown.
        $this->expectException('required_capability_exception');
        core_message_external::block_user($user1->id, $user2->id);
    }

    /**
     * Test unblocking a user.
     */
    public function test_unblock_user() {
        global $DB;

        $this->resetAfterTest(true);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        // Block the user.
        \core_message\api::block_user($user1->id, $user2->id);

        // Unblocking a user.
        $return = core_message_external::unblock_user($user1->id, $user2->id);
        $return = external_api::clean_returnvalue(core_message_external::unblock_user_returns(), $return);
        $this->assertEquals(array(), $return);

        $this->assertEquals(0, $DB->count_records('message_users_blocked'));

        // Unblocking a user who is already unblocked.
        $return = core_message_external::unblock_user($user1->id, $user2->id);
        $return = external_api::clean_returnvalue(core_message_external::unblock_user_returns(), $return);
        $this->assertEquals(array(), $return);

        $this->assertEquals(0, $DB->count_records('message_users_blocked'));
    }

    /**
     * Test unblocking a user with messaging disabled.
     */
    public function test_unblock_user_messaging_disabled() {
        global $CFG;

        $this->resetAfterTest();

        // Create some skeleton data just so we can call the WS.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        // Disable messaging.
        $CFG->messaging = 0;

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::unblock_user($user1->id, $user2->id);
    }

    /**
     * Test unblocking a user with no permission.
     */
    public function test_unblock_user_no_permission() {
        $this->resetAfterTest();

        // Create some skeleton data just so we can call the WS.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        $this->setUser($user3);

        // Ensure an exception is thrown.
        $this->expectException('required_capability_exception');
        core_message_external::unblock_user($user1->id, $user2->id);
    }

    /**
     * Test get_contacts.
     */
    public function test_get_contacts() {
        $this->resetAfterTest(true);

        $user1 = self::getDataGenerator()->create_user();
        $user_stranger = self::getDataGenerator()->create_user();
        $user_offline1 = self::getDataGenerator()->create_user();
        $user_offline2 = self::getDataGenerator()->create_user();
        $user_offline3 = self::getDataGenerator()->create_user();
        $user_online = new stdClass();
        $user_online->lastaccess = time();
        $user_online = self::getDataGenerator()->create_user($user_online);
        $user_blocked = self::getDataGenerator()->create_user();
        $noreplyuser = core_user::get_user(core_user::NOREPLY_USER);

        // Login as user1.
        $this->setUser($user1);
        \core_message\api::add_contact($user1->id, $user_offline1->id);
        \core_message\api::add_contact($user1->id, $user_offline2->id);
        \core_message\api::add_contact($user1->id, $user_offline3->id);
        \core_message\api::add_contact($user1->id, $user_online->id);

        // User_stranger sends a couple of messages to user1.
        $this->send_message($user_stranger, $user1, 'Hello there!');
        $this->send_message($user_stranger, $user1, 'How you goin?');
        $this->send_message($user_stranger, $user1, 'Cya!');
        $this->send_message($noreplyuser, $user1, 'I am not a real user');

        // User_blocked sends a message to user1.
        $this->send_message($user_blocked, $user1, 'Here, have some spam.');

        // Retrieve the contacts of the user.
        $this->setUser($user1);
        $contacts = core_message_external::get_contacts();
        $contacts = external_api::clean_returnvalue(core_message_external::get_contacts_returns(), $contacts);
        $this->assertCount(3, $contacts['offline']);
        $this->assertCount(1, $contacts['online']);
        $this->assertCount(3, $contacts['strangers']);
        core_message_external::block_contacts(array($user_blocked->id));
        $this->assertDebuggingCalled();
        $contacts = core_message_external::get_contacts();
        $contacts = external_api::clean_returnvalue(core_message_external::get_contacts_returns(), $contacts);
        $this->assertCount(3, $contacts['offline']);
        $this->assertCount(1, $contacts['online']);
        $this->assertCount(2, $contacts['strangers']);

        // Checking some of the fields returned.
        $stranger = array_pop($contacts['strangers']);

        $this->assertEquals(core_user::NOREPLY_USER, $stranger['id']);
        $this->assertEquals(1, $stranger['unread']);

        // Check that deleted users are not returned.
        delete_user($user_offline1);
        delete_user($user_stranger);
        delete_user($user_online);
        $contacts = core_message_external::get_contacts();
        $contacts = external_api::clean_returnvalue(core_message_external::get_contacts_returns(), $contacts);
        $this->assertCount(2, $contacts['offline']);
        $this->assertCount(0, $contacts['online']);
        $this->assertCount(1, $contacts['strangers']);
    }

    /**
     * Test search_contacts.
     * @expectedException moodle_exception
     */
    public function test_search_contacts() {
        global $DB;
        $this->resetAfterTest(true);

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        $user1 = new stdClass();
        $user1->firstname = 'X';
        $user1->lastname = 'X';
        $user1 = $this->getDataGenerator()->create_user($user1);
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id);

        $user2 = new stdClass();
        $user2->firstname = 'Eric';
        $user2->lastname = 'Cartman';
        $user2 = self::getDataGenerator()->create_user($user2);
        $user3 = new stdClass();
        $user3->firstname = 'Stan';
        $user3->lastname = 'Marsh';
        $user3 = self::getDataGenerator()->create_user($user3);
        self::getDataGenerator()->enrol_user($user3->id, $course1->id);
        $user4 = new stdClass();
        $user4->firstname = 'Kyle';
        $user4->lastname = 'Broflovski';
        $user4 = self::getDataGenerator()->create_user($user4);
        $user5 = new stdClass();
        $user5->firstname = 'Kenny';
        $user5->lastname = 'McCormick';
        $user5 = self::getDataGenerator()->create_user($user5);
        self::getDataGenerator()->enrol_user($user5->id, $course2->id);

        $this->setUser($user1);

        $results = core_message_external::search_contacts('r');
        $results = external_api::clean_returnvalue(core_message_external::search_contacts_returns(), $results);
        $this->assertCount(5, $results); // Users 2 through 5 + admin

        $results = core_message_external::search_contacts('r', true);
        $results = external_api::clean_returnvalue(core_message_external::search_contacts_returns(), $results);
        $this->assertCount(2, $results);

        $results = core_message_external::search_contacts('Kyle', false);
        $results = external_api::clean_returnvalue(core_message_external::search_contacts_returns(), $results);
        $this->assertCount(1, $results);
        $result = reset($results);
        $this->assertEquals($user4->id, $result['id']);

        $results = core_message_external::search_contacts('y', false);
        $results = external_api::clean_returnvalue(core_message_external::search_contacts_returns(), $results);
        $this->assertCount(2, $results);

        $results = core_message_external::search_contacts('y', true);
        $results = external_api::clean_returnvalue(core_message_external::search_contacts_returns(), $results);
        $this->assertCount(1, $results);
        $result = reset($results);
        $this->assertEquals($user5->id, $result['id']);

        // Empty query, will throw an exception.
        $results = core_message_external::search_contacts('');
    }

    /**
     * Test get_messages.
     */
    public function test_get_messages() {
        global $CFG, $DB;
        $this->resetAfterTest(true);

        $this->preventResetByRollback();
        // This mark the messages as read!.
        $sink = $this->redirectMessages();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        $course = self::getDataGenerator()->create_course();

        // Send a message from one user to another.
        message_post_message($user1, $user2, 'some random text 1', FORMAT_MOODLE);
        message_post_message($user1, $user3, 'some random text 2', FORMAT_MOODLE);
        message_post_message($user2, $user3, 'some random text 3', FORMAT_MOODLE);
        message_post_message($user3, $user2, 'some random text 4', FORMAT_MOODLE);
        message_post_message($user3, $user1, 'some random text 5', FORMAT_MOODLE);

        $this->setUser($user1);
        // Get read conversations from user1 to user2.
        $messages = core_message_external::get_messages($user2->id, $user1->id, 'conversations', true, true, 0, 0);
        $messages = external_api::clean_returnvalue(core_message_external::get_messages_returns(), $messages);
        $this->assertCount(1, $messages['messages']);

        // Delete the message.
        $message = array_shift($messages['messages']);
        \core_message\api::delete_message($user1->id, $message['id']);

        $messages = core_message_external::get_messages($user2->id, $user1->id, 'conversations', true, true, 0, 0);
        $messages = external_api::clean_returnvalue(core_message_external::get_messages_returns(), $messages);
        $this->assertCount(0, $messages['messages']);

        // Get unread conversations from user1 to user2.
        $messages = core_message_external::get_messages($user2->id, $user1->id, 'conversations', false, true, 0, 0);
        $messages = external_api::clean_returnvalue(core_message_external::get_messages_returns(), $messages);
        $this->assertCount(0, $messages['messages']);

        // Get read messages send from user1.
        $messages = core_message_external::get_messages(0, $user1->id, 'conversations', true, true, 0, 0);
        $messages = external_api::clean_returnvalue(core_message_external::get_messages_returns(), $messages);
        $this->assertCount(1, $messages['messages']);

        $this->setUser($user2);
        // Get read conversations from any user to user2.
        $messages = core_message_external::get_messages($user2->id, 0, 'conversations', true, true, 0, 0);
        $messages = external_api::clean_returnvalue(core_message_external::get_messages_returns(), $messages);
        $this->assertCount(2, $messages['messages']);

        // Conversations from user3 to user2.
        $messages = core_message_external::get_messages($user2->id, $user3->id, 'conversations', true, true, 0, 0);
        $messages = external_api::clean_returnvalue(core_message_external::get_messages_returns(), $messages);
        $this->assertCount(1, $messages['messages']);

        // Delete the message.
        $message = array_shift($messages['messages']);
        \core_message\api::delete_message($user2->id, $message['id']);

        $messages = core_message_external::get_messages($user2->id, $user3->id, 'conversations', true, true, 0, 0);
        $messages = external_api::clean_returnvalue(core_message_external::get_messages_returns(), $messages);
        $this->assertCount(0, $messages['messages']);

        $this->setUser($user3);
        // Get read notifications received by user3.
        $messages = core_message_external::get_messages($user3->id, 0, 'notifications', true, true, 0, 0);
        $messages = external_api::clean_returnvalue(core_message_external::get_messages_returns(), $messages);
        $this->assertCount(0, $messages['messages']);

        // Now, create some notifications...
        // We are creating fake notifications but based on real ones.

        // This one comes from a disabled plugin's provider and therefore is not sent.
        $eventdata = new \core\message\message();
        $eventdata->courseid          = $course->id;
        $eventdata->notification      = 1;
        $eventdata->modulename        = 'moodle';
        $eventdata->component         = 'enrol_paypal';
        $eventdata->name              = 'paypal_enrolment';
        $eventdata->userfrom          = get_admin();
        $eventdata->userto            = $user1;
        $eventdata->subject           = "Moodle: PayPal payment";
        $eventdata->fullmessage       = "Your PayPal payment is pending.";
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml   = '';
        $eventdata->smallmessage      = '';
        message_send($eventdata);
        $this->assertDebuggingCalled('Attempt to send msg from a provider enrol_paypal/paypal_enrolment '.
            'that is inactive or not allowed for the user id='.$user1->id);

        // This one omits notification = 1.
        $message = new \core\message\message();
        $message->courseid          = $course->id;
        $message->component         = 'enrol_manual';
        $message->name              = 'expiry_notification';
        $message->userfrom          = $user2;
        $message->userto            = $user1;
        $message->subject           = 'Test: This is not a notification but otherwise is valid';
        $message->fullmessage       = 'Test: Full message';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml   = markdown_to_html($message->fullmessage);
        $message->smallmessage      = $message->subject;
        $message->contexturlname    = $course->fullname;
        $message->contexturl        = (string)new moodle_url('/course/view.php', array('id' => $course->id));
        message_send($message);

        $message = new \core\message\message();
        $message->courseid          = $course->id;
        $message->notification      = 1;
        $message->component         = 'enrol_manual';
        $message->name              = 'expiry_notification';
        $message->userfrom          = $user2;
        $message->userto            = $user1;
        $message->subject           = 'Enrolment expired';
        $message->fullmessage       = 'Enrolment expired blah blah blah';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml   = markdown_to_html($message->fullmessage);
        $message->smallmessage      = $message->subject;
        $message->contexturlname    = $course->fullname;
        $message->contexturl        = (string)new moodle_url('/course/view.php', array('id' => $course->id));
        message_send($message);

        $userfrom = core_user::get_noreply_user();
        $userfrom->maildisplay = true;
        $eventdata = new \core\message\message();
        $eventdata->courseid          = $course->id;
        $eventdata->component         = 'moodle';
        $eventdata->name              = 'badgecreatornotice';
        $eventdata->userfrom          = $userfrom;
        $eventdata->userto            = $user1;
        $eventdata->notification      = 1;
        $eventdata->subject           = 'New badge';
        $eventdata->fullmessage       = format_text_email($eventdata->subject, FORMAT_HTML);
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml   = $eventdata->subject;
        $eventdata->smallmessage      = $eventdata->subject;
        message_send($eventdata);

        $eventdata = new \core\message\message();
        $eventdata->courseid         = $course->id;
        $eventdata->name             = 'submission';
        $eventdata->component        = 'mod_feedback';
        $eventdata->userfrom         = $user1;
        $eventdata->userto           = $user2;
        $eventdata->subject          = 'Feedback submitted';
        $eventdata->fullmessage      = 'Feedback submitted from an user';
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml  = '<strong>Feedback submitted</strong>';
        $eventdata->smallmessage     = '';
        $eventdata->customdata         = ['datakey' => 'data'];
        message_send($eventdata);

        $this->setUser($user1);
        // Get read notifications from any user to user1.
        $messages = core_message_external::get_messages($user1->id, 0, 'notifications', true, true, 0, 0);
        $messages = external_api::clean_returnvalue(core_message_external::get_messages_returns(), $messages);
        $this->assertCount(3, $messages['messages']);

        // Get one read notifications from any user to user1.
        $messages = core_message_external::get_messages($user1->id, 0, 'notifications', true, true, 0, 1);
        $messages = external_api::clean_returnvalue(core_message_external::get_messages_returns(), $messages);
        $this->assertCount(1, $messages['messages']);

        // Get unread notifications from any user to user1.
        $messages = core_message_external::get_messages($user1->id, 0, 'notifications', false, true, 0, 0);
        $messages = external_api::clean_returnvalue(core_message_external::get_messages_returns(), $messages);
        $this->assertCount(0, $messages['messages']);

        // Get read both type of messages from any user to user1.
        $messages = core_message_external::get_messages($user1->id, 0, 'both', true, true, 0, 0);
        $messages = external_api::clean_returnvalue(core_message_external::get_messages_returns(), $messages);
        $this->assertCount(4, $messages['messages']);

        // Get read notifications from no-reply-user to user1.
        $messages = core_message_external::get_messages($user1->id, $userfrom->id, 'notifications', true, true, 0, 0);
        $messages = external_api::clean_returnvalue(core_message_external::get_messages_returns(), $messages);
        $this->assertCount(1, $messages['messages']);

        // Get notifications send by user1 to any user.
        $messages = core_message_external::get_messages(0, $user1->id, 'notifications', true, true, 0, 0);
        $messages = external_api::clean_returnvalue(core_message_external::get_messages_returns(), $messages);
        $this->assertCount(1, $messages['messages']);
        // Check we receive custom data as a unserialisable json.
        $this->assertObjectHasAttribute('datakey', json_decode($messages['messages'][0]['customdata']));
        $this->assertEquals('mod_feedback', $messages['messages'][0]['component']);
        $this->assertEquals('submission', $messages['messages'][0]['eventtype']);

        // Test warnings.
        $CFG->messaging = 0;

        $messages = core_message_external::get_messages(0, $user1->id, 'both', true, true, 0, 0);
        $messages = external_api::clean_returnvalue(core_message_external::get_messages_returns(), $messages);
        $this->assertCount(1, $messages['warnings']);

        // Test exceptions.

        // Messaging disabled.
        try {
            $messages = core_message_external::get_messages(0, $user1->id, 'conversations', true, true, 0, 0);
            $this->fail('Exception expected due messaging disabled.');
        } catch (moodle_exception $e) {
            $this->assertEquals('disabled', $e->errorcode);
        }

        $CFG->messaging = 1;

        // Invalid users.
        try {
            $messages = core_message_external::get_messages(0, 0, 'conversations', true, true, 0, 0);
            $this->fail('Exception expected due invalid users.');
        } catch (moodle_exception $e) {
            $this->assertEquals('accessdenied', $e->errorcode);
        }

        // Invalid user ids.
        try {
            $messages = core_message_external::get_messages(2500, 0, 'conversations', true, true, 0, 0);
            $this->fail('Exception expected due invalid users.');
        } catch (moodle_exception $e) {
            $this->assertEquals('invaliduser', $e->errorcode);
        }

        // Invalid users (permissions).
        $this->setUser($user2);
        try {
            $messages = core_message_external::get_messages(0, $user1->id, 'conversations', true, true, 0, 0);
            $this->fail('Exception expected due invalid user.');
        } catch (moodle_exception $e) {
            $this->assertEquals('accessdenied', $e->errorcode);
        }

    }

    /**
     * Test get_messages where we want all messages from a user, sent to any user.
     */
    public function test_get_messages_useridto_all() {
        $this->resetAfterTest(true);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        // Send a message from user 1 to two other users.
        $this->send_message($user1, $user2, 'some random text 1', 0, 1);
        $this->send_message($user1, $user3, 'some random text 2', 0, 2);

        // Get messages sent from user 1.
        $messages = core_message_external::get_messages(0, $user1->id, 'conversations', false, false, 0, 0);
        $messages = external_api::clean_returnvalue(core_message_external::get_messages_returns(), $messages);

        // Confirm the data is correct.
        $messages = $messages['messages'];
        $this->assertCount(2, $messages);

        $message1 = array_shift($messages);
        $message2 = array_shift($messages);

        $this->assertEquals($user1->id, $message1['useridfrom']);
        $this->assertEquals($user2->id, $message1['useridto']);

        $this->assertEquals($user1->id, $message2['useridfrom']);
        $this->assertEquals($user3->id, $message2['useridto']);
    }

    /**
     * Test get_messages where we want all messages to a user, sent by any user.
     */
    public function test_get_messages_useridfrom_all() {
        $this->resetAfterTest();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        // Send a message to user 1 from two other users.
        $this->send_message($user2, $user1, 'some random text 1', 0, 1);
        $this->send_message($user3, $user1, 'some random text 2', 0, 2);

        // Get messages sent to user 1.
        $messages = core_message_external::get_messages($user1->id, 0, 'conversations', false, false, 0, 0);
        $messages = external_api::clean_returnvalue(core_message_external::get_messages_returns(), $messages);

        // Confirm the data is correct.
        $messages = $messages['messages'];
        $this->assertCount(2, $messages);

        $message1 = array_shift($messages);
        $message2 = array_shift($messages);

        $this->assertEquals($user2->id, $message1['useridfrom']);
        $this->assertEquals($user1->id, $message1['useridto']);

        $this->assertEquals($user3->id, $message2['useridfrom']);
        $this->assertEquals($user1->id, $message2['useridto']);
    }

    /**
     * Test get_blocked_users.
     */
    public function test_get_blocked_users() {
        $this->resetAfterTest(true);

        $user1 = self::getDataGenerator()->create_user();
        $userstranger = self::getDataGenerator()->create_user();
        $useroffline1 = self::getDataGenerator()->create_user();
        $useroffline2 = self::getDataGenerator()->create_user();
        $userblocked = self::getDataGenerator()->create_user();

        // Login as user1.
        $this->setUser($user1);

        \core_message\api::add_contact($user1->id, $useroffline1->id);
        \core_message\api::add_contact($user1->id, $useroffline2->id);

        // The userstranger sends a couple of messages to user1.
        $this->send_message($userstranger, $user1, 'Hello there!');
        $this->send_message($userstranger, $user1, 'How you goin?');

        // The userblocked sends a message to user1.
        // Note that this user is not blocked at this point.
        $this->send_message($userblocked, $user1, 'Here, have some spam.');

        // Retrieve the list of blocked users.
        $this->setUser($user1);
        $blockedusers = core_message_external::get_blocked_users($user1->id);
        $blockedusers = external_api::clean_returnvalue(core_message_external::get_blocked_users_returns(), $blockedusers);
        $this->assertCount(0, $blockedusers['users']);

        // Block the $userblocked and retrieve again the list.
        core_message_external::block_contacts(array($userblocked->id));
        $this->assertDebuggingCalled();
        $blockedusers = core_message_external::get_blocked_users($user1->id);
        $blockedusers = external_api::clean_returnvalue(core_message_external::get_blocked_users_returns(), $blockedusers);
        $this->assertCount(1, $blockedusers['users']);

        // Remove the $userblocked and check that the list now is empty.
        delete_user($userblocked);
        $blockedusers = core_message_external::get_blocked_users($user1->id);
        $blockedusers = external_api::clean_returnvalue(core_message_external::get_blocked_users_returns(), $blockedusers);
        $this->assertCount(0, $blockedusers['users']);
    }

    /**
     * Test mark_message_read.
     */
    public function test_mark_message_read() {
        $this->resetAfterTest(true);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        // Login as user1.
        $this->setUser($user1);
        \core_message\api::add_contact($user1->id, $user2->id);
        \core_message\api::add_contact($user1->id, $user3->id);

        // The user2 sends a couple of messages to user1.
        $this->send_message($user2, $user1, 'Hello there!');
        $this->send_message($user2, $user1, 'How you goin?');
        $this->send_message($user3, $user1, 'How you goin?');
        $this->send_message($user3, $user2, 'How you goin?');

        // Retrieve all messages sent by user2 (they are currently unread).
        $lastmessages = message_get_messages($user1->id, $user2->id, 0, false);

        $messageids = array();
        foreach ($lastmessages as $m) {
            $messageid = core_message_external::mark_message_read($m->id, time());
            $messageids[] = external_api::clean_returnvalue(core_message_external::mark_message_read_returns(), $messageid);
        }

        // Retrieve all messages sent (they are currently read).
        $lastmessages = message_get_messages($user1->id, $user2->id, 0, true);
        $this->assertCount(2, $lastmessages);
        $this->assertArrayHasKey($messageids[0]['messageid'], $lastmessages);
        $this->assertArrayHasKey($messageids[1]['messageid'], $lastmessages);

        // Retrieve all messages sent by any user (that are currently unread).
        $lastmessages = message_get_messages($user1->id, 0, 0, false);
        $this->assertCount(1, $lastmessages);

        // Invalid message ids.
        try {
            $messageid = core_message_external::mark_message_read(1337, time());
            $this->fail('Exception expected due invalid messageid.');
        } catch (dml_missing_record_exception $e) {
            $this->assertEquals('invalidrecordunknown', $e->errorcode);
        }

        // A message to a different user.
        $lastmessages = message_get_messages($user2->id, $user3->id, 0, false);
        $messageid = array_pop($lastmessages)->id;
        try {
            $messageid = core_message_external::mark_message_read($messageid, time());
            $this->fail('Exception expected due invalid messageid.');
        } catch (invalid_parameter_exception $e) {
            $this->assertEquals('invalidparameter', $e->errorcode);
        }
    }

    /**
     * Test mark_notification_read.
     */
    public function test_mark_notification_read() {
        $this->resetAfterTest(true);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        // Login as user1.
        $this->setUser($user1);
        \core_message\api::add_contact($user1->id, $user2->id);
        \core_message\api::add_contact($user1->id, $user3->id);

        // The user2 sends a couple of notifications to user1.
        $this->send_message($user2, $user1, 'Hello there!', 1);
        $this->send_message($user2, $user1, 'How you goin?', 1);
        $this->send_message($user3, $user1, 'How you goin?', 1);
        $this->send_message($user3, $user2, 'How you goin?', 1);

        // Retrieve all notifications sent by user2 (they are currently unread).
        $lastnotifications = message_get_messages($user1->id, $user2->id, 1, false);

        $notificationids = array();
        foreach ($lastnotifications as $n) {
            $notificationid = core_message_external::mark_notification_read($n->id, time());
            $notificationids[] = external_api::clean_returnvalue(core_message_external::mark_notification_read_returns(),
                $notificationid);
        }

        // Retrieve all notifications sent (they are currently read).
        $lastnotifications = message_get_messages($user1->id, $user2->id, 1, true);
        $this->assertCount(2, $lastnotifications);
        $this->assertArrayHasKey($notificationids[1]['notificationid'], $lastnotifications);
        $this->assertArrayHasKey($notificationids[0]['notificationid'], $lastnotifications);

        // Retrieve all notifications sent by any user (that are currently unread).
        $lastnotifications = message_get_messages($user1->id, 0, 1, false);
        $this->assertCount(1, $lastnotifications);

        // Invalid notification ids.
        try {
            $notificationid = core_message_external::mark_notification_read(1337, time());
            $this->fail('Exception expected due invalid notificationid.');
        } catch (dml_missing_record_exception $e) {
            $this->assertEquals('invalidrecord', $e->errorcode);
        }

        // A notification to a different user.
        $lastnotifications = message_get_messages($user2->id, $user3->id, 1, false);
        $notificationid = array_pop($lastnotifications)->id;
        try {
            $notificationid = core_message_external::mark_notification_read($notificationid, time());
            $this->fail('Exception expected due invalid notificationid.');
        } catch (invalid_parameter_exception $e) {
            $this->assertEquals('invalidparameter', $e->errorcode);
        }
    }

    /**
     * Test delete_message.
     */
    public function test_delete_message() {
        global $DB;
        $this->resetAfterTest(true);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();

        // Login as user1.
        $this->setUser($user1);
        \core_message\api::add_contact($user1->id, $user2->id);
        \core_message\api::add_contact($user1->id, $user3->id);

        // User user1 does not interchange messages with user3.
        $m1to2 = message_post_message($user1, $user2, 'some random text 1', FORMAT_MOODLE);
        $m2to3 = message_post_message($user2, $user3, 'some random text 3', FORMAT_MOODLE);
        $m3to2 = message_post_message($user3, $user2, 'some random text 4', FORMAT_MOODLE);
        $m3to4 = message_post_message($user3, $user4, 'some random text 4', FORMAT_MOODLE);

        // Retrieve all messages sent by user2 (they are currently unread).
        $lastmessages = message_get_messages($user1->id, $user2->id, 0, false);

        // Delete a message not read, as a user from.
        $result = core_message_external::delete_message($m1to2, $user1->id, false);
        $result = external_api::clean_returnvalue(core_message_external::delete_message_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertCount(0, $result['warnings']);
        $mua = $DB->get_record('message_user_actions', array('messageid' => $m1to2, 'userid' => $user1->id));
        $this->assertEquals(\core_message\api::MESSAGE_ACTION_DELETED, $mua->action);

        // Try to delete the same message again.
        $result = core_message_external::delete_message($m1to2, $user1->id, false);
        $result = external_api::clean_returnvalue(core_message_external::delete_message_returns(), $result);
        $this->assertFalse($result['status']);

        // Try to delete a message that does not belong to me.
        try {
            $messageid = core_message_external::delete_message($m2to3, $user3->id, false);
            $this->fail('Exception expected due invalid messageid.');
        } catch (moodle_exception $e) {
            $this->assertEquals('You do not have permission to delete this message', $e->errorcode);
        }

        $this->setUser($user3);
        // Delete a message not read, as a user to.
        $result = core_message_external::delete_message($m2to3, $user3->id, false);
        $result = external_api::clean_returnvalue(core_message_external::delete_message_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertCount(0, $result['warnings']);
        $this->assertTrue($DB->record_exists('message_user_actions', array('messageid' => $m2to3, 'userid' => $user3->id,
            'action' => \core_message\api::MESSAGE_ACTION_DELETED)));

        // Delete a message read.
        $message = $DB->get_record('messages', ['id' => $m3to2]);
        \core_message\api::mark_message_as_read($user3->id, $message, time());
        $result = core_message_external::delete_message($m3to2, $user3->id);
        $result = external_api::clean_returnvalue(core_message_external::delete_message_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertCount(0, $result['warnings']);
        $this->assertTrue($DB->record_exists('message_user_actions', array('messageid' => $m3to2, 'userid' => $user3->id,
            'action' => \core_message\api::MESSAGE_ACTION_DELETED)));

        // Invalid message ids.
        try {
            $result = core_message_external::delete_message(-1, $user1->id);
            $this->fail('Exception expected due invalid messageid.');
        } catch (dml_missing_record_exception $e) {
            $this->assertEquals('invalidrecord', $e->errorcode);
        }

        // Invalid user.
        try {
            $result = core_message_external::delete_message($m1to2, -1, false);
            $this->fail('Exception expected due invalid user.');
        } catch (moodle_exception $e) {
            $this->assertEquals('invaliduser', $e->errorcode);
        }

        // Not active user.
        delete_user($user2);
        try {
            $result = core_message_external::delete_message($m1to2, $user2->id, false);
            $this->fail('Exception expected due invalid user.');
        } catch (moodle_exception $e) {
            $this->assertEquals('userdeleted', $e->errorcode);
        }

        // Now, as an admin, try to delete any message.
        $this->setAdminUser();
        $result = core_message_external::delete_message($m3to4, $user4->id, false);
        $result = external_api::clean_returnvalue(core_message_external::delete_message_returns(), $result);
        $this->assertTrue($result['status']);
        $this->assertCount(0, $result['warnings']);
        $this->assertTrue($DB->record_exists('message_user_actions', array('messageid' => $m3to4, 'userid' => $user4->id,
            'action' => \core_message\api::MESSAGE_ACTION_DELETED)));

    }

    public function test_mark_all_notifications_as_read_invalid_user_exception() {
        $this->resetAfterTest(true);

        $this->expectException('moodle_exception');
        core_message_external::mark_all_notifications_as_read(-2132131, 0);
    }

    public function test_mark_all_notifications_as_read_access_denied_exception() {
        $this->resetAfterTest(true);

        $sender = $this->getDataGenerator()->create_user();
        $user = $this->getDataGenerator()->create_user();

        $this->setUser($user);
        $this->expectException('moodle_exception');
        core_message_external::mark_all_notifications_as_read($sender->id, 0);
    }

    public function test_mark_all_notifications_as_read_missing_from_user_exception() {
        $this->resetAfterTest(true);

        $sender = $this->getDataGenerator()->create_user();

        $this->setUser($sender);
        $this->expectException('moodle_exception');
        core_message_external::mark_all_notifications_as_read($sender->id, 99999);
    }

    public function test_mark_all_notifications_as_read() {
        global $DB;

        $this->resetAfterTest(true);

        $sender1 = $this->getDataGenerator()->create_user();
        $sender2 = $this->getDataGenerator()->create_user();
        $sender3 = $this->getDataGenerator()->create_user();
        $recipient = $this->getDataGenerator()->create_user();

        $this->setUser($recipient);

        $this->send_message($sender1, $recipient, 'Notification', 1);
        $this->send_message($sender1, $recipient, 'Notification', 1);
        $this->send_message($sender2, $recipient, 'Notification', 1);
        $this->send_message($sender2, $recipient, 'Notification', 1);
        $this->send_message($sender3, $recipient, 'Notification', 1);
        $this->send_message($sender3, $recipient, 'Notification', 1);

        core_message_external::mark_all_notifications_as_read($recipient->id, $sender1->id);
        $readnotifications = $DB->get_records_select('notifications', 'useridto = ? AND timeread IS NOT NULL', [$recipient->id]);
        $unreadnotifications = $DB->get_records_select('notifications', 'useridto = ? AND timeread IS NULL', [$recipient->id]);

        $this->assertCount(2, $readnotifications);
        $this->assertCount(4, $unreadnotifications);

        core_message_external::mark_all_notifications_as_read($recipient->id, 0);
        $readnotifications = $DB->get_records_select('notifications', 'useridto = ? AND timeread IS NOT NULL', [$recipient->id]);
        $unreadnotifications = $DB->get_records_select('notifications', 'useridto = ? AND timeread IS NULL', [$recipient->id]);

        $this->assertCount(6, $readnotifications);
        $this->assertCount(0, $unreadnotifications);
    }

    public function test_mark_all_notifications_as_read_time_created_to() {
        global $DB;

        $this->resetAfterTest(true);

        $sender1 = $this->getDataGenerator()->create_user();
        $sender2 = $this->getDataGenerator()->create_user();

        $recipient = $this->getDataGenerator()->create_user();
        $this->setUser($recipient);

        // Record messages as sent on one second intervals.
        $time = time();

        $this->send_message($sender1, $recipient, 'Message 1', 1, $time);
        $this->send_message($sender2, $recipient, 'Message 2', 1, $time + 1);
        $this->send_message($sender1, $recipient, 'Message 3', 1, $time + 2);
        $this->send_message($sender2, $recipient, 'Message 4', 1, $time + 3);

        // Mark notifications sent from sender1 up until the second message; should only mark the first notification as read.
        core_message_external::mark_all_notifications_as_read($recipient->id, $sender1->id, $time + 1);

        $params = [$recipient->id];

        $this->assertEquals(1, $DB->count_records_select('notifications', 'useridto = ? AND timeread IS NOT NULL', $params));
        $this->assertEquals(3, $DB->count_records_select('notifications', 'useridto = ? AND timeread IS NULL', $params));

        // Mark all notifications as read from any sender up to the time the third message was sent.
        core_message_external::mark_all_notifications_as_read($recipient->id, 0, $time + 2);

        $this->assertEquals(3, $DB->count_records_select('notifications', 'useridto = ? AND timeread IS NOT NULL', $params));
        $this->assertEquals(1, $DB->count_records_select('notifications', 'useridto = ? AND timeread IS NULL', $params));

        // Mark all notifications as read from any sender with a time after all messages were sent.
        core_message_external::mark_all_notifications_as_read($recipient->id, 0, $time + 10);

        $this->assertEquals(4, $DB->count_records_select('notifications', 'useridto = ? AND timeread IS NOT NULL', $params));
        $this->assertEquals(0, $DB->count_records_select('notifications', 'useridto = ? AND timeread IS NULL', $params));
    }

    /**
     * Test get_user_notification_preferences
     */
    public function test_get_user_notification_preferences() {
        $this->resetAfterTest(true);

        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);

        // Set a couple of preferences to test.
        set_user_preference('message_provider_mod_assign_assign_notification_loggedin', 'popup', $user);
        set_user_preference('message_provider_mod_assign_assign_notification_loggedoff', 'email', $user);

        $prefs = core_message_external::get_user_notification_preferences();
        $prefs = external_api::clean_returnvalue(core_message_external::get_user_notification_preferences_returns(), $prefs);
        // Check processors.
        $this->assertGreaterThanOrEqual(2, count($prefs['preferences']['processors']));
        $this->assertEquals($user->id, $prefs['preferences']['userid']);

        // Check components.
        $this->assertGreaterThanOrEqual(8, count($prefs['preferences']['components']));

        // Check some preferences that we previously set.
        $found = 0;
        foreach ($prefs['preferences']['components'] as $component) {
            foreach ($component['notifications'] as $prefdata) {
                if ($prefdata['preferencekey'] != 'message_provider_mod_assign_assign_notification') {
                    continue;
                }
                foreach ($prefdata['processors'] as $processor) {
                    if ($processor['name'] == 'popup') {
                        $this->assertTrue($processor['loggedin']['checked']);
                        $found++;
                    } else if ($processor['name'] == 'email') {
                        $this->assertTrue($processor['loggedoff']['checked']);
                        $found++;
                    }
                }
            }
        }
        $this->assertEquals(2, $found);
    }

    /**
     * Test get_user_notification_preferences permissions
     */
    public function test_get_user_notification_preferences_permissions() {
        $this->resetAfterTest(true);

        $user = self::getDataGenerator()->create_user();
        $otheruser = self::getDataGenerator()->create_user();
        $this->setUser($user);

        $this->expectException('moodle_exception');
        $prefs = core_message_external::get_user_notification_preferences($otheruser->id);
    }

    /**
     * Tests searching users in a course.
     */
    public function test_data_for_messagearea_search_users_in_course() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = new stdClass();
        $user1->firstname = 'User';
        $user1->lastname = 'One';
        $user1 = self::getDataGenerator()->create_user($user1);

        // The person doing the search.
        $this->setUser($user1);

        // Set the second user's status to online by setting their last access to now.
        $user2 = new stdClass();
        $user2->firstname = 'User';
        $user2->lastname = 'Two';
        $user2->lastaccess = time();
        $user2 = self::getDataGenerator()->create_user($user2);

        // Block the second user.
        \core_message\api::block_user($user1->id, $user2->id);

        $user3 = new stdClass();
        $user3->firstname = 'User';
        $user3->lastname = 'Three';
        $user3 = self::getDataGenerator()->create_user($user3);

        // Create a course.
        $course1 = new stdClass();
        $course1->fullname = 'Course';
        $course1->shortname = 'One';
        $course1 = $this->getDataGenerator()->create_course();

        // Enrol the user we are doing the search for and one user in the course.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);

        // Perform a search.
        $result = core_message_external::data_for_messagearea_search_users_in_course($user1->id, $course1->id, 'User');

        // We need to execute the return values cleaning process to simulate the web service.
        $result = external_api::clean_returnvalue(core_message_external::data_for_messagearea_search_users_in_course_returns(),
            $result);

        // Check that we only retrieved a user that was enrolled, and that the user performing the search was not returned.
        $users = $result['contacts'];
        $this->assertCount(1, $users);

        $user = $users[0];
        $this->assertEquals($user2->id, $user['userid']);
        $this->assertEquals(fullname($user2), $user['fullname']);
        $this->assertFalse($user['ismessaging']);
        $this->assertFalse($user['sentfromcurrentuser']);
        $this->assertNull($user['lastmessage']);
        $this->assertNull($user['messageid']);
        $this->assertNull($user['isonline']);
        $this->assertFalse($user['isread']);
        $this->assertTrue($user['isblocked']);
        $this->assertNull($user['unreadcount']);
    }

    /**
     * Tests searching users in course as another user.
     */
    public function test_data_for_messagearea_search_users_in_course_as_other_user() {
        $this->resetAfterTest(true);

        // The person doing the search for another user.
        $this->setAdminUser();

        // Create some users.
        $user1 = new stdClass();
        $user1->firstname = 'User';
        $user1->lastname = 'One';
        $user1 = self::getDataGenerator()->create_user($user1);

        $user2 = new stdClass();
        $user2->firstname = 'User';
        $user2->lastname = 'Two';
        $user2 = self::getDataGenerator()->create_user($user2);

        $user3 = new stdClass();
        $user3->firstname = 'User';
        $user3->lastname = 'Three';
        $user3 = self::getDataGenerator()->create_user($user3);

        // Create a course.
        $course1 = new stdClass();
        $course1->fullname = 'Course';
        $course1->shortname = 'One';
        $course1 = $this->getDataGenerator()->create_course();

        // Enrol the user we are doing the search for and one user in the course.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);

        // Perform a search.
        $result = core_message_external::data_for_messagearea_search_users_in_course($user1->id, $course1->id, 'User');

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::data_for_messagearea_search_users_in_course_returns(),
            $result);

        // Check that we got the user enrolled, and that the user we are performing the search on behalf of was not returned.
        $users = $result['contacts'];
        $this->assertCount(1, $users);

        $user = $users[0];
        $this->assertEquals($user2->id, $user['userid']);
        $this->assertEquals(fullname($user2), $user['fullname']);
        $this->assertFalse($user['ismessaging']);
        $this->assertFalse($user['sentfromcurrentuser']);
        $this->assertNull($user['lastmessage']);
        $this->assertNull($user['messageid']);
        $this->assertFalse($user['isonline']);
        $this->assertFalse($user['isread']);
        $this->assertFalse($user['isblocked']);
        $this->assertNull($user['unreadcount']);
    }

    /**
     * Tests searching users in course as another user without the proper capabilities.
     */
    public function test_data_for_messagearea_search_users_in_course_as_other_user_without_cap() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person doing the search for another user.
        $this->setUser($user1);

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::data_for_messagearea_search_users_in_course($user2->id, $course->id, 'User');
        $this->assertDebuggingCalled();
    }

    /**
     * Tests searching users in course with messaging disabled.
     */
    public function test_data_for_messagearea_search_users_in_course_messaging_disabled() {
        global $CFG;

        $this->resetAfterTest(true);

        // Create some skeleton data just so we can call the WS..
        $user = self::getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course();

        // The person doing the search for another user.
        $this->setUser($user);

        // Disable messaging.
        $CFG->messaging = 0;

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::data_for_messagearea_search_users_in_course($user->id, $course->id, 'User');
        $this->assertDebuggingCalled();
    }

    /**
     * Tests searching users.
     */
    public function test_data_for_messagearea_search_users() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = new stdClass();
        $user1->firstname = 'User';
        $user1->lastname = 'One';
        $user1 = self::getDataGenerator()->create_user($user1);

        // Set as the user performing the search.
        $this->setUser($user1);

        $user2 = new stdClass();
        $user2->firstname = 'User search';
        $user2->lastname = 'Two';
        $user2 = self::getDataGenerator()->create_user($user2);

        $user3 = new stdClass();
        $user3->firstname = 'User search';
        $user3->lastname = 'Three';
        $user3 = self::getDataGenerator()->create_user($user3);

        $user4 = new stdClass();
        $user4->firstname = 'User';
        $user4->lastname = 'Four';
        $user4 = self::getDataGenerator()->create_user($user4);

        $user5 = new stdClass();
        $user5->firstname = 'User search';
        $user5->lastname = 'Five';
        $user5 = self::getDataGenerator()->create_user($user5);

        $user6 = new stdClass();
        $user6->firstname = 'User';
        $user6->lastname = 'Six';
        $user6 = self::getDataGenerator()->create_user($user6);

        // Create some courses.
        $course1 = new stdClass();
        $course1->fullname = 'Course search';
        $course1->shortname = 'One';
        $course1 = $this->getDataGenerator()->create_course($course1);

        $course2 = new stdClass();
        $course2->fullname = 'Course';
        $course2->shortname = 'Two';
        $course2 = $this->getDataGenerator()->create_course($course2);

        $course3 = new stdClass();
        $course3->fullname = 'Course';
        $course3->shortname = 'Three search';
        $course3 = $this->getDataGenerator()->create_course($course3);

        $course4 = new stdClass();
        $course4->fullname = 'Course Four';
        $course4->shortname = 'CF100';
        $course4 = $this->getDataGenerator()->create_course($course4);

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, 'student');
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id, 'student');
        $this->getDataGenerator()->enrol_user($user1->id, $course3->id, 'student');

        // Add some users as contacts.
        \core_message\api::add_contact($user1->id, $user2->id);
        \core_message\api::add_contact($user1->id, $user3->id);
        \core_message\api::add_contact($user1->id, $user4->id);

        // Perform a search $CFG->messagingallusers setting enabled.
        set_config('messagingallusers', 1);
        $result = core_message_external::data_for_messagearea_search_users($user1->id, 'search');

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::data_for_messagearea_search_users_returns(),
            $result);

        // Confirm that we returns contacts, courses and non-contacts.
        $contacts = $result['contacts'];
        $courses = $result['courses'];
        $noncontacts = $result['noncontacts'];

        // Check that we retrieved the correct contacts.
        $this->assertCount(2, $contacts);
        $this->assertEquals($user3->id, $contacts[0]['userid']);
        $this->assertEquals($user2->id, $contacts[1]['userid']);

        // Check that we retrieved the correct courses.
        $this->assertCount(2, $courses);
        $this->assertEquals($course3->id, $courses[0]['id']);
        $this->assertEquals($course1->id, $courses[1]['id']);

        // Check that we retrieved the correct non-contacts.
        $this->assertCount(1, $noncontacts);
        $this->assertEquals($user5->id, $noncontacts[0]['userid']);
    }

    /**
     * Tests searching users as another user.
     */
    public function test_data_for_messagearea_search_users_as_other_user() {
        $this->resetAfterTest(true);

        // The person doing the search.
        $this->setAdminUser();

        // Create some users.
        $user1 = new stdClass();
        $user1->firstname = 'User';
        $user1->lastname = 'One';
        $user1 = self::getDataGenerator()->create_user($user1);

        $user2 = new stdClass();
        $user2->firstname = 'User search';
        $user2->lastname = 'Two';
        $user2 = self::getDataGenerator()->create_user($user2);

        $user3 = new stdClass();
        $user3->firstname = 'User search';
        $user3->lastname = 'Three';
        $user3 = self::getDataGenerator()->create_user($user3);

        $user4 = new stdClass();
        $user4->firstname = 'User';
        $user4->lastname = 'Four';
        $user4 = self::getDataGenerator()->create_user($user4);

        $user5 = new stdClass();
        $user5->firstname = 'User search';
        $user5->lastname = 'Five';
        $user5 = self::getDataGenerator()->create_user($user5);

        $user6 = new stdClass();
        $user6->firstname = 'User';
        $user6->lastname = 'Six';
        $user6 = self::getDataGenerator()->create_user($user6);

        // Create some courses.
        $course1 = new stdClass();
        $course1->fullname = 'Course search';
        $course1->shortname = 'One';
        $course1 = $this->getDataGenerator()->create_course($course1);

        $course2 = new stdClass();
        $course2->fullname = 'Course';
        $course2->shortname = 'Two';
        $course2 = $this->getDataGenerator()->create_course($course2);

        $course3 = new stdClass();
        $course3->fullname = 'Course';
        $course3->shortname = 'Three search';
        $course3 = $this->getDataGenerator()->create_course($course3);

        // Add some users as contacts.
        \core_message\api::add_contact($user1->id, $user2->id);
        \core_message\api::add_contact($user1->id, $user3->id);
        \core_message\api::add_contact($user1->id, $user4->id);

        // Perform a search $CFG->messagingallusers setting enabled.
        set_config('messagingallusers', 1);
        $result = core_message_external::data_for_messagearea_search_users($user1->id, 'search');

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::data_for_messagearea_search_users_returns(),
            $result);

        // Confirm that we returns contacts, courses and non-contacts.
        $contacts = $result['contacts'];
        $courses = $result['courses'];
        $noncontacts = $result['noncontacts'];

        // Check that we retrieved the correct contacts.
        $this->assertCount(2, $contacts);
        $this->assertEquals($user3->id, $contacts[0]['userid']);
        $this->assertEquals($user2->id, $contacts[1]['userid']);

        // Check that we retrieved the correct courses.
        $this->assertCount(0, $courses);

        // Check that we retrieved the correct non-contacts.
        $this->assertCount(1, $noncontacts);
        $this->assertEquals($user5->id, $noncontacts[0]['userid']);
    }

    /**
     * Tests searching users as another user without the proper capabilities.
     */
    public function test_data_for_messagearea_search_users_as_other_user_without_cap() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person doing the search for another user.
        $this->setUser($user1);

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::data_for_messagearea_search_users($user2->id, 'User');
        $this->assertDebuggingCalled();
    }

    /**
     * Tests searching users with messaging disabled.
     */
    public function test_data_for_messagearea_search_users_messaging_disabled() {
        global $CFG;

        $this->resetAfterTest(true);

        // Create some skeleton data just so we can call the WS.
        $user = self::getDataGenerator()->create_user();

        // The person doing the search.
        $this->setUser($user);

        // Disable messaging.
        $CFG->messaging = 0;

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::data_for_messagearea_search_users($user->id, 'User');
        $this->assertDebuggingCalled();
    }

    /**
     * Tests searching for users when site-wide messaging is disabled.
     *
     * This test verifies that any contacts are returned, as well as any non-contacts whose profile we can view.
     * If checks this by placing some users in the same course, where default caps would permit a user to view another user's
     * profile.
     */
    public function test_message_search_users_messagingallusers_disabled() {
        global $DB;
        $this->resetAfterTest();

        // Create some users.
        $users = [];
        foreach (range(1, 8) as $i) {
            $user = new stdClass();
            $user->firstname = ($i == 4) ? 'User' : 'User search'; // Ensure the fourth user won't match the search term.
            $user->lastname = $i;
            $user = $this->getDataGenerator()->create_user($user);
            $users[$i] = $user;
        }

        // Enrol a few users in the same course, but leave them as non-contacts.
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        $this->setAdminUser();
        $this->getDataGenerator()->enrol_user($users[1]->id, $course1->id);
        $this->getDataGenerator()->enrol_user($users[6]->id, $course1->id);
        $this->getDataGenerator()->enrol_user($users[7]->id, $course1->id);

        // Add some other users as contacts.
        \core_message\api::add_contact($users[1]->id, $users[2]->id);
        \core_message\api::add_contact($users[3]->id, $users[1]->id);
        \core_message\api::add_contact($users[1]->id, $users[4]->id);

        // Enrol a user as a teacher in the course, and make the teacher role a course contact role.
        $this->getDataGenerator()->enrol_user($users[8]->id, $course2->id, 'editingteacher');
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        set_config('coursecontact', $teacherrole->id);

        // Create individual conversations between some users, one contact and one non-contact.
        \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [$users[1]->id, $users[2]->id]);
        \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [$users[6]->id, $users[1]->id]);

        // Create a group conversation between 4 users, including a contact and a non-contact.
        \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$users[1]->id, $users[2]->id, $users[4]->id, $users[7]->id], 'Project chat');

        // Set as the user performing the search.
        $this->setUser($users[1]);

        // Perform a search with $CFG->messagingallusers disabled.
        set_config('messagingallusers', 0);
        $result = core_message_external::message_search_users($users[1]->id, 'search');
        $result = external_api::clean_returnvalue(core_message_external::message_search_users_returns(), $result);

        // Confirm that we returns contacts and non-contacts.
        $this->assertArrayHasKey('contacts', $result);
        $this->assertArrayHasKey('noncontacts', $result);
        $contacts = $result['contacts'];
        $noncontacts = $result['noncontacts'];

        // Check that we retrieved the correct contacts.
        $this->assertCount(2, $contacts);
        $this->assertEquals($users[2]->id, $contacts[0]['id']);
        $this->assertEquals($users[3]->id, $contacts[1]['id']);

        // Verify the correct conversations were returned for the contacts.
        $this->assertCount(2, $contacts[0]['conversations']);
        // We can't rely on the ordering of conversations within the results, so sort by id first.
        usort($contacts[0]['conversations'], function($a, $b) {
            return $a['id'] < $b['id'];
        });
        $this->assertEquals(\core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP, $contacts[0]['conversations'][0]['type']);
        $this->assertEquals(\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL, $contacts[0]['conversations'][1]['type']);

        $this->assertCount(0, $contacts[1]['conversations']);

        // Check that we retrieved the correct non-contacts.
        // When site wide messaging is disabled, we expect to see only those users who we share a course with and whose profiles
        // are visible in that course. This excludes users like course contacts.
        $this->assertCount(3, $noncontacts);
        // Self-conversation first.
        $this->assertEquals($users[1]->id, $noncontacts[0]['id']);
        $this->assertEquals($users[6]->id, $noncontacts[1]['id']);
        $this->assertEquals($users[7]->id, $noncontacts[2]['id']);

        // Verify the correct conversations were returned for the non-contacts.
        $this->assertCount(1, $noncontacts[1]['conversations']);
        $this->assertEquals(\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL, $noncontacts[1]['conversations'][0]['type']);

        $this->assertCount(1, $noncontacts[2]['conversations']);
        $this->assertEquals(\core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP, $noncontacts[2]['conversations'][0]['type']);
    }

    /**
     * Tests searching for users when site-wide messaging is enabled.
     *
     * This test verifies that any contacts are returned, as well as any non-contacts, regardless of whether the searching user
     * can view their respective profile.
     */
    public function test_message_search_users_messagingallusers_enabled() {
        global $DB;
        $this->resetAfterTest();

        // Create some users.
        $users = [];
        foreach (range(1, 9) as $i) {
            $user = new stdClass();
            $user->firstname = ($i == 4) ? 'User' : 'User search'; // Ensure the fourth user won't match the search term.
            $user->lastname = $i;
            $user = $this->getDataGenerator()->create_user($user);
            $users[$i] = $user;
        }

        // Enrol a few users in the same course, but leave them as non-contacts.
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();

        $this->setAdminUser();
        $this->getDataGenerator()->enrol_user($users[1]->id, $course1->id);
        $this->getDataGenerator()->enrol_user($users[6]->id, $course1->id);
        $this->getDataGenerator()->enrol_user($users[7]->id, $course1->id);

        // Add some other users as contacts.
        \core_message\api::add_contact($users[1]->id, $users[2]->id);
        \core_message\api::add_contact($users[3]->id, $users[1]->id);
        \core_message\api::add_contact($users[1]->id, $users[4]->id);

        // Enrol a user as a teacher in the course, and make the teacher role a course contact role.
        $this->getDataGenerator()->enrol_user($users[9]->id, $course2->id, 'editingteacher');
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        set_config('coursecontact', $teacherrole->id);

        // Create individual conversations between some users, one contact and one non-contact.
        \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [$users[1]->id, $users[2]->id]);
        \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [$users[6]->id, $users[1]->id]);

        // Create a group conversation between 5 users, including a contact and a non-contact, and a user NOT in a shared course.
        \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$users[1]->id, $users[2]->id, $users[4]->id, $users[7]->id, $users[8]->id], 'Project chat');

        // Set as the user performing the search.
        $this->setUser($users[1]);

        // Perform a search with $CFG->messagingallusers enabled.
        set_config('messagingallusers', 1);
        $result = core_message_external::message_search_users($users[1]->id, 'search');
        $result = external_api::clean_returnvalue(core_message_external::message_search_users_returns(), $result);

        // Confirm that we returns contacts and non-contacts.
        $this->assertArrayHasKey('contacts', $result);
        $this->assertArrayHasKey('noncontacts', $result);
        $contacts = $result['contacts'];
        $noncontacts = $result['noncontacts'];

        // Check that we retrieved the correct contacts.
        $this->assertCount(2, $contacts);
        $this->assertEquals($users[2]->id, $contacts[0]['id']);
        $this->assertEquals($users[3]->id, $contacts[1]['id']);

        // Verify the correct conversations were returned for the contacts.
        $this->assertCount(2, $contacts[0]['conversations']);
        // We can't rely on the ordering of conversations within the results, so sort by id first.
        usort($contacts[0]['conversations'], function($a, $b) {
            return $a['id'] < $b['id'];
        });
        $this->assertEquals(\core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP, $contacts[0]['conversations'][0]['type']);
        $this->assertEquals(\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL, $contacts[0]['conversations'][1]['type']);

        $this->assertCount(0, $contacts[1]['conversations']);

        // Check that we retrieved the correct non-contacts.
        // If site wide messaging is enabled, we expect to be able to search for any users whose profiles we can view.
        // In this case, as a student, that's the course contact for course2 and those noncontacts sharing a course with user1.
        $this->assertCount(4, $noncontacts);
        $this->assertEquals($users[1]->id, $noncontacts[0]['id']);
        $this->assertEquals($users[6]->id, $noncontacts[1]['id']);
        $this->assertEquals($users[7]->id, $noncontacts[2]['id']);
        $this->assertEquals($users[9]->id, $noncontacts[3]['id']);

        // Verify the correct conversations were returned for the non-contacts.
        $this->assertCount(1, $noncontacts[1]['conversations']);
        $this->assertCount(1, $noncontacts[2]['conversations']);
        $this->assertEquals(\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL, $noncontacts[1]['conversations'][0]['type']);
        $this->assertEquals(\core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP, $noncontacts[2]['conversations'][0]['type']);
        $this->assertCount(0, $noncontacts[3]['conversations']);
    }

    /**
     * Verify searching for users find themselves when they have self-conversations.
     */
    public function test_message_search_users_self_conversations() {
        $this->resetAfterTest();

        // Create some users.
        $user1 = new stdClass();
        $user1->firstname = 'User';
        $user1->lastname = 'One';
        $user1 = $this->getDataGenerator()->create_user($user1);
        $user2 = new stdClass();
        $user2->firstname = 'User';
        $user2->lastname = 'Two';
        $user2 = $this->getDataGenerator()->create_user($user2);

        // Get self-conversation for user1.
        $sc1 = \core_message\api::get_self_conversation($user1->id);
        testhelper::send_fake_message_to_conversation($user1, $sc1->id, 'Hi myself!');

        // Perform a search as user1.
        $this->setUser($user1);
        $result = core_message_external::message_search_users($user1->id, 'One');
        $result = external_api::clean_returnvalue(core_message_external::message_search_users_returns(), $result);

        // Check results are empty.
        $this->assertCount(0, $result['contacts']);
        $this->assertCount(1, $result['noncontacts']);
    }

    /**
     * Verify searching for users works even if no matching users from either contacts, or non-contacts can be found.
     */
    public function test_message_search_users_with_empty_result() {
        $this->resetAfterTest();

        // Create some users, but make sure neither will match the search term.
        $user1 = new stdClass();
        $user1->firstname = 'User';
        $user1->lastname = 'One';
        $user1 = $this->getDataGenerator()->create_user($user1);
        $user2 = new stdClass();
        $user2->firstname = 'User';
        $user2->lastname = 'Two';
        $user2 = $this->getDataGenerator()->create_user($user2);

        // Perform a search as user1.
        $this->setUser($user1);
        $result = core_message_external::message_search_users($user1->id, 'search');
        $result = external_api::clean_returnvalue(core_message_external::message_search_users_returns(), $result);

        // Check results are empty.
        $this->assertCount(0, $result['contacts']);
        $this->assertCount(0, $result['noncontacts']);
    }

    /**
     * Test verifying that limits and offsets work for both the contacts and non-contacts return data.
     */
    public function test_message_search_users_limit_offset() {
        $this->resetAfterTest();

        // Create 20 users.
        $users = [];
        foreach (range(1, 20) as $i) {
            $user = new stdClass();
            $user->firstname = "User search";
            $user->lastname = $i;
            $user = $this->getDataGenerator()->create_user($user);
            $users[$i] = $user;
        }

        // Enrol the first 8 users in the same course, but leave them as non-contacts.
        $this->setAdminUser();
        $course1 = $this->getDataGenerator()->create_course();
        foreach (range(1, 8) as $i) {
            $this->getDataGenerator()->enrol_user($users[$i]->id, $course1->id);
        }

        // Add 5 users, starting at the 11th user, as contacts for user1.
        foreach (range(11, 15) as $i) {
            \core_message\api::add_contact($users[1]->id, $users[$i]->id);
        }

        // Set as the user performing the search.
        $this->setUser($users[1]);

        // Search using a limit of 3.
        // This tests the case where we have more results than the limit for both contacts and non-contacts.
        $result = core_message_external::message_search_users($users[1]->id, 'search', 0, 3);
        $result = external_api::clean_returnvalue(core_message_external::message_search_users_returns(), $result);
        $contacts = $result['contacts'];
        $noncontacts = $result['noncontacts'];

        // Check that we retrieved the correct contacts.
        $this->assertCount(3, $contacts);
        $this->assertEquals($users[11]->id, $contacts[0]['id']);
        $this->assertEquals($users[12]->id, $contacts[1]['id']);
        $this->assertEquals($users[13]->id, $contacts[2]['id']);

        // Check that we retrieved the correct non-contacts.
        // Consider first conversation is self-conversation.
        $this->assertCount(3, $noncontacts);
        $this->assertEquals($users[1]->id, $noncontacts[0]['id']);
        $this->assertEquals($users[2]->id, $noncontacts[1]['id']);
        $this->assertEquals($users[3]->id, $noncontacts[2]['id']);

        // Now, offset to get the next batch of results.
        // We expect to see 2 contacts, and 3 non-contacts.
        $result = core_message_external::message_search_users($users[1]->id, 'search', 3, 3);
        $result = external_api::clean_returnvalue(core_message_external::message_search_users_returns(), $result);
        $contacts = $result['contacts'];
        $noncontacts = $result['noncontacts'];
        $this->assertCount(2, $contacts);
        $this->assertEquals($users[14]->id, $contacts[0]['id']);
        $this->assertEquals($users[15]->id, $contacts[1]['id']);

        $this->assertCount(3, $noncontacts);
        $this->assertEquals($users[4]->id, $noncontacts[0]['id']);
        $this->assertEquals($users[5]->id, $noncontacts[1]['id']);
        $this->assertEquals($users[6]->id, $noncontacts[2]['id']);

        // Now, offset to get the next batch of results.
        // We expect to see 0 contacts, and 2 non-contacts.
        $result = core_message_external::message_search_users($users[1]->id, 'search', 6, 3);
        $result = external_api::clean_returnvalue(core_message_external::message_search_users_returns(), $result);
        $contacts = $result['contacts'];
        $noncontacts = $result['noncontacts'];
        $this->assertCount(0, $contacts);

        $this->assertCount(2, $noncontacts);
        $this->assertEquals($users[7]->id, $noncontacts[0]['id']);
        $this->assertEquals($users[8]->id, $noncontacts[1]['id']);
    }

    /**
     * Tests searching users as another user having the 'moodle/user:viewdetails' capability.
     */
    public function test_message_search_users_with_cap() {
        $this->resetAfterTest();
        global $DB;

        // Create some users.
        $users = [];
        foreach (range(1, 8) as $i) {
            $user = new stdClass();
            $user->firstname = ($i == 4) ? 'User' : 'User search'; // Ensure the fourth user won't match the search term.
            $user->lastname = $i;
            $user = $this->getDataGenerator()->create_user($user);
            $users[$i] = $user;
        }

        // Enrol a few users in the same course, but leave them as non-contacts.
        $course1 = $this->getDataGenerator()->create_course();
        $this->setAdminUser();
        $this->getDataGenerator()->enrol_user($users[1]->id, $course1->id);
        $this->getDataGenerator()->enrol_user($users[6]->id, $course1->id);
        $this->getDataGenerator()->enrol_user($users[7]->id, $course1->id);

        // Add some other users as contacts.
        \core_message\api::add_contact($users[1]->id, $users[2]->id);
        \core_message\api::add_contact($users[3]->id, $users[1]->id);
        \core_message\api::add_contact($users[1]->id, $users[4]->id);

        // Set as the user performing the search.
        $this->setUser($users[1]);

        // Grant the authenticated user role the capability 'user:viewdetails' at site context.
        $authenticatedrole = $DB->get_record('role', ['shortname' => 'user'], '*', MUST_EXIST);
        assign_capability('moodle/user:viewdetails', CAP_ALLOW, $authenticatedrole->id, context_system::instance());

        // Perform a search with $CFG->messagingallusers disabled.
        set_config('messagingallusers', 0);
        $result = core_message_external::message_search_users($users[1]->id, 'search');
        $result = external_api::clean_returnvalue(core_message_external::message_search_users_returns(), $result);
        $contacts = $result['contacts'];
        $noncontacts = $result['noncontacts'];

        // Check that we retrieved the correct contacts.
        $this->assertCount(2, $contacts);
        $this->assertEquals($users[2]->id, $contacts[0]['id']);
        $this->assertEquals($users[3]->id, $contacts[1]['id']);

        // Check that we retrieved the correct non-contacts.
        // Site-wide messaging is disabled, so we expect to be able to search for any users whose profile we can view.
        // Consider first conversations is self-conversation.
        $this->assertCount(3, $noncontacts);
        $this->assertEquals($users[1]->id, $noncontacts[0]['id']);
        $this->assertEquals($users[6]->id, $noncontacts[1]['id']);
        $this->assertEquals($users[7]->id, $noncontacts[2]['id']);
    }

    /**
     * Tests searching users as another user without the 'moodle/user:viewdetails' capability.
     */
    public function test_message_search_users_without_cap() {
        $this->resetAfterTest();

        // Create some users.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // The person doing the search for another user.
        $this->setUser($user1);

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::message_search_users($user2->id, 'User');
        $this->assertDebuggingCalled();
    }

    /**
     * Tests searching users with messaging disabled.
     */
    public function test_message_search_users_messaging_disabled() {
        $this->resetAfterTest();

        // Create some skeleton data just so we can call the WS.
        $user = $this->getDataGenerator()->create_user();

        // The person doing the search.
        $this->setUser($user);

        // Disable messaging.
        set_config('messaging', 0);

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::message_search_users($user->id, 'User');
    }

    /**
     * Tests searching messages.
     */
    public function test_messagearea_search_messages() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person doing the search.
        $this->setUser($user1);

        // Send some messages back and forth.
        $time = time();
        $this->send_message($user1, $user2, 'Yo!', 0, $time);
        $this->send_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $this->send_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);
        $this->send_message($user2, $user1, 'Word.', 0, $time + 3);
        $convid = \core_message\api::get_conversation_between_users([$user1->id, $user2->id]);

        // Perform a search.
        $result = core_message_external::data_for_messagearea_search_messages($user1->id, 'o');

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::data_for_messagearea_search_messages_returns(), $result);

        // Confirm the data is correct.
        $messages = $result['contacts'];
        $this->assertCount(2, $messages);

        $message1 = $messages[0];
        $message2 = $messages[1];

        $this->assertEquals($user2->id, $message1['userid']);
        $this->assertEquals(fullname($user2), $message1['fullname']);
        $this->assertTrue($message1['ismessaging']);
        $this->assertFalse($message1['sentfromcurrentuser']);
        $this->assertEquals('Word.', $message1['lastmessage']);
        $this->assertNotEmpty($message1['messageid']);
        $this->assertNull($message1['isonline']);
        $this->assertFalse($message1['isread']);
        $this->assertFalse($message1['isblocked']);
        $this->assertNull($message1['unreadcount']);
        $this->assertEquals($convid, $message1['conversationid']);

        $this->assertEquals($user2->id, $message2['userid']);
        $this->assertEquals(fullname($user2), $message2['fullname']);
        $this->assertTrue($message2['ismessaging']);
        $this->assertTrue($message2['sentfromcurrentuser']);
        $this->assertEquals('Yo!', $message2['lastmessage']);
        $this->assertNotEmpty($message2['messageid']);
        $this->assertNull($message2['isonline']);
        $this->assertTrue($message2['isread']);
        $this->assertFalse($message2['isblocked']);
        $this->assertNull($message2['unreadcount']);
        $this->assertEquals($convid, $message2['conversationid']);
    }

    /**
     * Tests searching messages as another user.
     */
    public function test_messagearea_search_messages_as_other_user() {
        $this->resetAfterTest(true);

        // The person doing the search.
        $this->setAdminUser();

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // Send some messages back and forth.
        $time = time();
        $this->send_message($user1, $user2, 'Yo!', 0, $time);
        $this->send_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $this->send_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);
        $this->send_message($user2, $user1, 'Word.', 0, $time + 3);

        // Perform a search.
        $result = core_message_external::data_for_messagearea_search_messages($user1->id, 'o');

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::data_for_messagearea_search_messages_returns(),
            $result);

        // Confirm the data is correct.
        $messages = $result['contacts'];
        $this->assertCount(2, $messages);

        $message1 = $messages[0];
        $message2 = $messages[1];

        $this->assertEquals($user2->id, $message1['userid']);
        $this->assertEquals(fullname($user2), $message1['fullname']);
        $this->assertTrue($message1['ismessaging']);
        $this->assertFalse($message1['sentfromcurrentuser']);
        $this->assertEquals('Word.', $message1['lastmessage']);
        $this->assertNotEmpty($message1['messageid']);
        $this->assertFalse($message1['isonline']);
        $this->assertFalse($message1['isread']);
        $this->assertFalse($message1['isblocked']);
        $this->assertNull($message1['unreadcount']);

        $this->assertEquals($user2->id, $message2['userid']);
        $this->assertEquals(fullname($user2), $message2['fullname']);
        $this->assertTrue($message2['ismessaging']);
        $this->assertTrue($message2['sentfromcurrentuser']);
        $this->assertEquals('Yo!', $message2['lastmessage']);
        $this->assertNotEmpty($message2['messageid']);
        $this->assertFalse($message2['isonline']);
        $this->assertTrue($message2['isread']);
        $this->assertFalse($message2['isblocked']);
        $this->assertNull($message2['unreadcount']);
    }

    /**
     * Tests searching messages as another user without the proper capabilities.
     */
    public function test_messagearea_search_messages_as_other_user_without_cap() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person doing the search for another user.
        $this->setUser($user1);

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::data_for_messagearea_search_messages($user2->id, 'Search');
    }

    /**
     * Tests searching messages with messaging disabled
     */
    public function test_messagearea_search_messages_messaging_disabled() {
        global $CFG;

        $this->resetAfterTest(true);

        // Create some skeleton data just so we can call the WS.
        $user = self::getDataGenerator()->create_user();

        // The person doing the search .
        $this->setUser($user);

        // Disable messaging.
        $CFG->messaging = 0;

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::data_for_messagearea_search_messages($user->id, 'Search');
    }

    /**
     * Tests retrieving conversations.
     */
    public function test_messagearea_conversations() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();

        // The person retrieving the conversations.
        $this->setUser($user1);

        // Send some messages back and forth, have some different conversations with different users.
        $time = time();
        $this->send_message($user1, $user2, 'Yo!', 0, $time);
        $this->send_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $this->send_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);
        $messageid1 = $this->send_message($user2, $user1, 'Word.', 0, $time + 3);

        $this->send_message($user1, $user3, 'Booyah', 0, $time + 4);
        $this->send_message($user3, $user1, 'Whaaat?', 0, $time + 5);
        $this->send_message($user1, $user3, 'Nothing.', 0, $time + 6);
        $messageid2 = $this->send_message($user3, $user1, 'Cool.', 0, $time + 7);

        $this->send_message($user1, $user4, 'Hey mate, you see the new messaging UI in Moodle?', 0, $time + 8);
        $this->send_message($user4, $user1, 'Yah brah, it\'s pretty rad.', 0, $time + 9);
        $messageid3 = $this->send_message($user1, $user4, 'Dope.', 0, $time + 10);

        // Retrieve the conversations.
        $result = core_message_external::data_for_messagearea_conversations($user1->id);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::data_for_messagearea_conversations_returns(),
            $result);

        // Confirm the data is correct.
        $messages = $result['contacts'];
        $this->assertCount(3, $messages);

        $message1 = $messages[0];
        $message2 = $messages[1];
        $message3 = $messages[2];

        $this->assertEquals($user4->id, $message1['userid']);
        $this->assertTrue($message1['ismessaging']);
        $this->assertTrue($message1['sentfromcurrentuser']);
        $this->assertEquals('Dope.', $message1['lastmessage']);
        $this->assertEquals($messageid3, $message1['messageid']);
        $this->assertNull($message1['isonline']);
        $this->assertFalse($message1['isread']);
        $this->assertFalse($message1['isblocked']);
        $this->assertEquals(1, $message1['unreadcount']);

        $this->assertEquals($user3->id, $message2['userid']);
        $this->assertTrue($message2['ismessaging']);
        $this->assertFalse($message2['sentfromcurrentuser']);
        $this->assertEquals('Cool.', $message2['lastmessage']);
        $this->assertEquals($messageid2, $message2['messageid']);
        $this->assertNull($message2['isonline']);
        $this->assertFalse($message2['isread']);
        $this->assertFalse($message2['isblocked']);
        $this->assertEquals(2, $message2['unreadcount']);

        $this->assertEquals($user2->id, $message3['userid']);
        $this->assertTrue($message3['ismessaging']);
        $this->assertFalse($message3['sentfromcurrentuser']);
        $this->assertEquals('Word.', $message3['lastmessage']);
        $this->assertEquals($messageid1, $message3['messageid']);
        $this->assertNull($message3['isonline']);
        $this->assertFalse($message3['isread']);
        $this->assertFalse($message3['isblocked']);
        $this->assertEquals(2, $message3['unreadcount']);
    }

    /**
     * Tests retrieving conversations as another user.
     */
    public function test_messagearea_conversations_as_other_user() {
        $this->resetAfterTest(true);

        // Set as admin.
        $this->setAdminUser();

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();

        // Send some messages back and forth, have some different conversations with different users.
        $time = time();
        $this->send_message($user1, $user2, 'Yo!', 0, $time);
        $this->send_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $this->send_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);
        $messageid1 = $this->send_message($user2, $user1, 'Word.', 0, $time + 3);

        $this->send_message($user1, $user3, 'Booyah', 0, $time + 4);
        $this->send_message($user3, $user1, 'Whaaat?', 0, $time + 5);
        $this->send_message($user1, $user3, 'Nothing.', 0, $time + 6);
        $messageid2 = $this->send_message($user3, $user1, 'Cool.', 0, $time + 7);

        $this->send_message($user1, $user4, 'Hey mate, you see the new messaging UI in Moodle?', 0, $time + 8);
        $this->send_message($user4, $user1, 'Yah brah, it\'s pretty rad.', 0, $time + 9);
        $messageid3 = $this->send_message($user1, $user4, 'Dope.', 0, $time + 10);

        // Retrieve the conversations.
        $result = core_message_external::data_for_messagearea_conversations($user1->id);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::data_for_messagearea_conversations_returns(),
            $result);

        // Confirm the data is correct.
        $messages = $result['contacts'];
        $this->assertCount(3, $messages);

        $message1 = $messages[0];
        $message2 = $messages[1];
        $message3 = $messages[2];

        $this->assertEquals($user4->id, $message1['userid']);
        $this->assertTrue($message1['ismessaging']);
        $this->assertTrue($message1['sentfromcurrentuser']);
        $this->assertEquals('Dope.', $message1['lastmessage']);
        $this->assertEquals($messageid3, $message1['messageid']);
        $this->assertFalse($message1['isonline']);
        $this->assertFalse($message1['isread']);
        $this->assertFalse($message1['isblocked']);
        $this->assertEquals(1, $message1['unreadcount']);

        $this->assertEquals($user3->id, $message2['userid']);
        $this->assertTrue($message2['ismessaging']);
        $this->assertFalse($message2['sentfromcurrentuser']);
        $this->assertEquals('Cool.', $message2['lastmessage']);
        $this->assertEquals($messageid2, $message2['messageid']);
        $this->assertFalse($message2['isonline']);
        $this->assertFalse($message2['isread']);
        $this->assertFalse($message2['isblocked']);
        $this->assertEquals(2, $message2['unreadcount']);

        $this->assertEquals($user2->id, $message3['userid']);
        $this->assertTrue($message3['ismessaging']);
        $this->assertFalse($message3['sentfromcurrentuser']);
        $this->assertEquals('Word.', $message3['lastmessage']);
        $this->assertEquals($messageid1, $message3['messageid']);
        $this->assertFalse($message3['isonline']);
        $this->assertFalse($message3['isread']);
        $this->assertFalse($message3['isblocked']);
        $this->assertEquals(2, $message3['unreadcount']);
    }

    /**
     * Tests retrieving conversations as another user without the proper capabilities.
     */
    public function test_messagearea_conversations_as_other_user_without_cap() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person retrieving the conversations for another user.
        $this->setUser($user1);

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::data_for_messagearea_conversations($user2->id);
    }

    /**
     * Tests retrieving conversations with messaging disabled.
     */
    public function test_messagearea_conversations_messaging_disabled() {
        global $CFG;

        $this->resetAfterTest(true);

        // Create some skeleton data just so we can call the WS.
        $user = self::getDataGenerator()->create_user();

        // The person retrieving the conversations.
        $this->setUser($user);

        // Disable messaging.
        $CFG->messaging = 0;

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::data_for_messagearea_conversations($user->id);
    }

    /**
     * Tests retrieving contacts.
     */
    public function test_messagearea_contacts() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();

        // Set as the user.
        $this->setUser($user1);

        $user2 = new stdClass();
        $user2->firstname = 'User';
        $user2->lastname = 'A';
        $user2 = self::getDataGenerator()->create_user($user2);

        $user3 = new stdClass();
        $user3->firstname = 'User';
        $user3->lastname = 'B';
        $user3 = self::getDataGenerator()->create_user($user3);

        $user4 = new stdClass();
        $user4->firstname = 'User';
        $user4->lastname = 'C';
        $user4 = self::getDataGenerator()->create_user($user4);

        $user5 = new stdClass();
        $user5->firstname = 'User';
        $user5->lastname = 'D';
        $user5 = self::getDataGenerator()->create_user($user5);

        // Add some users as contacts.
        \core_message\api::add_contact($user1->id, $user2->id);
        \core_message\api::add_contact($user1->id, $user3->id);
        \core_message\api::add_contact($user1->id, $user4->id);

        // Retrieve the contacts.
        $result = core_message_external::data_for_messagearea_contacts($user1->id);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::data_for_messagearea_contacts_returns(),
            $result);

        // Confirm the data is correct.
        $contacts = $result['contacts'];
        usort($contacts, ['static', 'sort_contacts']);
        $this->assertCount(3, $contacts);

        $contact1 = $contacts[0];
        $contact2 = $contacts[1];
        $contact3 = $contacts[2];

        $this->assertEquals($user2->id, $contact1['userid']);
        $this->assertFalse($contact1['ismessaging']);
        $this->assertFalse($contact1['sentfromcurrentuser']);
        $this->assertNull($contact1['lastmessage']);
        $this->assertNull($contact1['messageid']);
        $this->assertNull($contact1['isonline']);
        $this->assertFalse($contact1['isread']);
        $this->assertFalse($contact1['isblocked']);
        $this->assertNull($contact1['unreadcount']);

        $this->assertEquals($user3->id, $contact2['userid']);
        $this->assertFalse($contact2['ismessaging']);
        $this->assertFalse($contact2['sentfromcurrentuser']);
        $this->assertNull($contact2['lastmessage']);
        $this->assertNull($contact2['messageid']);
        $this->assertNull($contact2['isonline']);
        $this->assertFalse($contact2['isread']);
        $this->assertFalse($contact2['isblocked']);
        $this->assertNull($contact2['unreadcount']);

        $this->assertEquals($user4->id, $contact3['userid']);
        $this->assertFalse($contact3['ismessaging']);
        $this->assertFalse($contact3['sentfromcurrentuser']);
        $this->assertNull($contact3['lastmessage']);
        $this->assertNull($contact3['messageid']);
        $this->assertNull($contact3['isonline']);
        $this->assertFalse($contact3['isread']);
        $this->assertFalse($contact3['isblocked']);
        $this->assertNull($contact3['unreadcount']);
    }

    /**
     * Tests retrieving contacts as another user.
     */
    public function test_messagearea_contacts_as_other_user() {
        $this->resetAfterTest(true);

        $this->setAdminUser();

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();

        $user2 = new stdClass();
        $user2->firstname = 'User';
        $user2->lastname = 'A';
        $user2 = self::getDataGenerator()->create_user($user2);

        $user3 = new stdClass();
        $user3->firstname = 'User';
        $user3->lastname = 'B';
        $user3 = self::getDataGenerator()->create_user($user3);

        $user4 = new stdClass();
        $user4->firstname = 'User';
        $user4->lastname = 'C';
        $user4 = self::getDataGenerator()->create_user($user4);

        $user5 = new stdClass();
        $user5->firstname = 'User';
        $user5->lastname = 'D';
        $user5 = self::getDataGenerator()->create_user($user5);

        // Add some users as contacts.
        \core_message\api::add_contact($user1->id, $user2->id);
        \core_message\api::add_contact($user1->id, $user3->id);
        \core_message\api::add_contact($user1->id, $user4->id);

        // Retrieve the contacts.
        $result = core_message_external::data_for_messagearea_contacts($user1->id);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::data_for_messagearea_contacts_returns(),
            $result);

        // Confirm the data is correct.
        $contacts = $result['contacts'];
        usort($contacts, ['static', 'sort_contacts']);
        $this->assertCount(3, $contacts);

        $contact1 = $contacts[0];
        $contact2 = $contacts[1];
        $contact3 = $contacts[2];

        $this->assertEquals($user2->id, $contact1['userid']);
        $this->assertFalse($contact1['ismessaging']);
        $this->assertFalse($contact1['sentfromcurrentuser']);
        $this->assertNull($contact1['lastmessage']);
        $this->assertNull($contact1['messageid']);
        $this->assertFalse($contact1['isonline']);
        $this->assertFalse($contact1['isread']);
        $this->assertFalse($contact1['isblocked']);
        $this->assertNull($contact1['unreadcount']);

        $this->assertEquals($user3->id, $contact2['userid']);
        $this->assertFalse($contact2['ismessaging']);
        $this->assertFalse($contact2['sentfromcurrentuser']);
        $this->assertNull($contact2['lastmessage']);
        $this->assertNull($contact2['messageid']);
        $this->assertFalse($contact2['isonline']);
        $this->assertFalse($contact2['isread']);
        $this->assertFalse($contact2['isblocked']);
        $this->assertNull($contact2['unreadcount']);

        $this->assertEquals($user4->id, $contact3['userid']);
        $this->assertFalse($contact3['ismessaging']);
        $this->assertFalse($contact3['sentfromcurrentuser']);
        $this->assertNull($contact3['lastmessage']);
        $this->assertNull($contact3['messageid']);
        $this->assertFalse($contact3['isonline']);
        $this->assertFalse($contact3['isread']);
        $this->assertFalse($contact3['isblocked']);
        $this->assertNull($contact3['unreadcount']);
    }

    /**
     * Tests retrieving contacts as another user without the proper capabilities.
     */
    public function test_messagearea_contacts_as_other_user_without_cap() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person retrieving the contacts for another user.
        $this->setUser($user1);

        // Perform the WS call and ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::data_for_messagearea_contacts($user2->id);
    }

    /**
     * Tests retrieving contacts with messaging disabled.
     */
    public function test_messagearea_contacts_messaging_disabled() {
        global $CFG;

        $this->resetAfterTest(true);

        // Create some skeleton data just so we can call the WS.
        $user = self::getDataGenerator()->create_user();

        // The person retrieving the contacts.
        $this->setUser($user);

        // Disable messaging.
        $CFG->messaging = 0;

        // Perform the WS call and ensure we are shown that it is disabled.
        $this->expectException('moodle_exception');
        core_message_external::data_for_messagearea_contacts($user->id);
    }

    /**
     * Tests retrieving contacts.
     */
    public function test_get_user_contacts() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();

        // Set as the user.
        $this->setUser($user1);

        $user2 = new stdClass();
        $user2->firstname = 'User';
        $user2->lastname = 'A';
        $user2 = self::getDataGenerator()->create_user($user2);

        $user3 = new stdClass();
        $user3->firstname = 'User';
        $user3->lastname = 'B';
        $user3 = self::getDataGenerator()->create_user($user3);

        $user4 = new stdClass();
        $user4->firstname = 'User';
        $user4->lastname = 'C';
        $user4 = self::getDataGenerator()->create_user($user4);

        $user5 = new stdClass();
        $user5->firstname = 'User';
        $user5->lastname = 'D';
        $user5 = self::getDataGenerator()->create_user($user5);

        // Add some users as contacts.
        \core_message\api::add_contact($user1->id, $user2->id);
        \core_message\api::add_contact($user1->id, $user3->id);
        \core_message\api::add_contact($user1->id, $user4->id);

        // Retrieve the contacts.
        $result = core_message_external::get_user_contacts($user1->id);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::get_user_contacts_returns(),
            $result);

        // Confirm the data is correct.
        $contacts = $result;
        usort($contacts, ['static', 'sort_contacts_id']);
        $this->assertCount(3, $contacts);

        $contact1 = array_shift($contacts);
        $contact2 = array_shift($contacts);
        $contact3 = array_shift($contacts);

        $this->assertEquals($user2->id, $contact1['id']);
        $this->assertEquals(fullname($user2), $contact1['fullname']);
        $this->assertTrue($contact1['iscontact']);

        $this->assertEquals($user3->id, $contact2['id']);
        $this->assertEquals(fullname($user3), $contact2['fullname']);
        $this->assertTrue($contact2['iscontact']);

        $this->assertEquals($user4->id, $contact3['id']);
        $this->assertEquals(fullname($user4), $contact3['fullname']);
        $this->assertTrue($contact3['iscontact']);
    }

    /**
     * Tests retrieving contacts as another user.
     */
    public function test_get_user_contacts_as_other_user() {
        $this->resetAfterTest(true);

        $this->setAdminUser();

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();

        $user2 = new stdClass();
        $user2->firstname = 'User';
        $user2->lastname = 'A';
        $user2 = self::getDataGenerator()->create_user($user2);

        $user3 = new stdClass();
        $user3->firstname = 'User';
        $user3->lastname = 'B';
        $user3 = self::getDataGenerator()->create_user($user3);

        $user4 = new stdClass();
        $user4->firstname = 'User';
        $user4->lastname = 'C';
        $user4 = self::getDataGenerator()->create_user($user4);

        $user5 = new stdClass();
        $user5->firstname = 'User';
        $user5->lastname = 'D';
        $user5 = self::getDataGenerator()->create_user($user5);

        // Add some users as contacts.
        \core_message\api::add_contact($user1->id, $user2->id);
        \core_message\api::add_contact($user1->id, $user3->id);
        \core_message\api::add_contact($user1->id, $user4->id);

        // Retrieve the contacts.
        $result = core_message_external::get_user_contacts($user1->id);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::get_user_contacts_returns(),
            $result);

        // Confirm the data is correct.
        $contacts = $result;
        usort($contacts, ['static', 'sort_contacts_id']);
        $this->assertCount(3, $contacts);

        $contact1 = array_shift($contacts);
        $contact2 = array_shift($contacts);
        $contact3 = array_shift($contacts);

        $this->assertEquals($user2->id, $contact1['id']);
        $this->assertEquals(fullname($user2), $contact1['fullname']);
        $this->assertTrue($contact1['iscontact']);

        $this->assertEquals($user3->id, $contact2['id']);
        $this->assertEquals(fullname($user3), $contact2['fullname']);
        $this->assertTrue($contact2['iscontact']);

        $this->assertEquals($user4->id, $contact3['id']);
        $this->assertEquals(fullname($user4), $contact3['fullname']);
        $this->assertTrue($contact3['iscontact']);
    }

    /**
     * Tests retrieving contacts as another user without the proper capabilities.
     */
    public function test_get_user_contacts_as_other_user_without_cap() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person retrieving the contacts for another user.
        $this->setUser($user1);

        // Perform the WS call and ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::get_user_contacts($user2->id);
    }

    /**
     * Tests retrieving contacts with messaging disabled.
     */
    public function test_get_user_contacts_messaging_disabled() {
        global $CFG;

        $this->resetAfterTest(true);

        // Create some skeleton data just so we can call the WS.
        $user = self::getDataGenerator()->create_user();

        // The person retrieving the contacts.
        $this->setUser($user);

        // Disable messaging.
        $CFG->messaging = 0;

        // Perform the WS call and ensure we are shown that it is disabled.
        $this->expectException('moodle_exception');
        core_message_external::get_user_contacts($user->id);
    }

    /**
     * Test getting contacts when there are no results.
     */
    public function test_get_user_contacts_no_results() {
        $this->resetAfterTest();

        $user1 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        $requests = core_message_external::get_user_contacts($user1->id);
        $requests = external_api::clean_returnvalue(core_message_external::get_user_contacts_returns(), $requests);

        $this->assertEmpty($requests);
    }

    /**
     * Tests retrieving messages.
     */
    public function test_messagearea_messages() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person asking for the messages.
        $this->setUser($user1);

        // Send some messages back and forth.
        $time = time();
        $this->send_message($user1, $user2, 'Yo!', 0, $time);
        $this->send_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $this->send_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);
        $this->send_message($user2, $user1, 'Word.', 0, $time + 3);

        // Retrieve the messages.
        $result = core_message_external::data_for_messagearea_messages($user1->id, $user2->id);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::data_for_messagearea_messages_returns(),
            $result);

        // Check the results are correct.
        $this->assertTrue($result['iscurrentuser']);
        $this->assertEquals($user1->id, $result['currentuserid']);
        $this->assertEquals($user2->id, $result['otheruserid']);
        $this->assertEquals(fullname($user2), $result['otheruserfullname']);
        $this->assertNull($result['isonline']);

        // Confirm the message data is correct.
        $messages = $result['messages'];
        $this->assertCount(4, $messages);

        $message1 = $messages[0];
        $message2 = $messages[1];
        $message3 = $messages[2];
        $message4 = $messages[3];

        $this->assertEquals($user1->id, $message1['useridfrom']);
        $this->assertEquals($user2->id, $message1['useridto']);
        $this->assertTrue($message1['displayblocktime']);
        $this->assertContains('Yo!', $message1['text']);

        $this->assertEquals($user2->id, $message2['useridfrom']);
        $this->assertEquals($user1->id, $message2['useridto']);
        $this->assertFalse($message2['displayblocktime']);
        $this->assertContains('Sup mang?', $message2['text']);

        $this->assertEquals($user1->id, $message3['useridfrom']);
        $this->assertEquals($user2->id, $message3['useridto']);
        $this->assertFalse($message3['displayblocktime']);
        $this->assertContains('Writing PHPUnit tests!', $message3['text']);

        $this->assertEquals($user2->id, $message4['useridfrom']);
        $this->assertEquals($user1->id, $message4['useridto']);
        $this->assertFalse($message4['displayblocktime']);
        $this->assertContains('Word.', $message4['text']);
    }

    /**
     * Tests retrieving messages.
     */
    public function test_messagearea_messages_timefrom() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person asking for the messages.
        $this->setUser($user1);

        // Send some messages back and forth.
        $time = time();
        $this->send_message($user1, $user2, 'Message 1', 0, $time - 4);
        $this->send_message($user2, $user1, 'Message 2', 0, $time - 3);
        $this->send_message($user1, $user2, 'Message 3', 0, $time - 2);
        $this->send_message($user2, $user1, 'Message 4', 0, $time - 1);

        // Retrieve the messages from $time - 3, which should be the 3 most recent messages.
        $result = core_message_external::data_for_messagearea_messages($user1->id, $user2->id, 0, 0, false, $time - 3);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::data_for_messagearea_messages_returns(),
            $result);

        // Confirm the message data is correct. We shouldn't get 'Message 1' back.
        $messages = $result['messages'];
        $this->assertCount(3, $messages);

        $message1 = $messages[0];
        $message2 = $messages[1];
        $message3 = $messages[2];

        $this->assertContains('Message 2', $message1['text']);
        $this->assertContains('Message 3', $message2['text']);
        $this->assertContains('Message 4', $message3['text']);
    }

    /**
     * Tests retrieving messages as another user.
     */
    public function test_messagearea_messages_as_other_user() {
        $this->resetAfterTest(true);

        // Set as admin.
        $this->setAdminUser();

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // Send some messages back and forth.
        $time = time();
        $this->send_message($user1, $user2, 'Yo!', 0, $time);
        $this->send_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $this->send_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);
        $this->send_message($user2, $user1, 'Word.', 0, $time + 3);

        // Retrieve the messages.
        $result = core_message_external::data_for_messagearea_messages($user1->id, $user2->id);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::data_for_messagearea_messages_returns(),
            $result);

        // Check the results are correct.
        $this->assertFalse($result['iscurrentuser']);
        $this->assertEquals($user1->id, $result['currentuserid']);
        $this->assertEquals($user2->id, $result['otheruserid']);
        $this->assertEquals(fullname($user2), $result['otheruserfullname']);
        $this->assertFalse($result['isonline']);

        // Confirm the message data is correct.
        $messages = $result['messages'];
        $this->assertCount(4, $messages);

        $message1 = $messages[0];
        $message2 = $messages[1];
        $message3 = $messages[2];
        $message4 = $messages[3];

        $this->assertEquals($user1->id, $message1['useridfrom']);
        $this->assertEquals($user2->id, $message1['useridto']);
        $this->assertTrue($message1['displayblocktime']);
        $this->assertContains('Yo!', $message1['text']);

        $this->assertEquals($user2->id, $message2['useridfrom']);
        $this->assertEquals($user1->id, $message2['useridto']);
        $this->assertFalse($message2['displayblocktime']);
        $this->assertContains('Sup mang?', $message2['text']);

        $this->assertEquals($user1->id, $message3['useridfrom']);
        $this->assertEquals($user2->id, $message3['useridto']);
        $this->assertFalse($message3['displayblocktime']);
        $this->assertContains('Writing PHPUnit tests!', $message3['text']);

        $this->assertEquals($user2->id, $message4['useridfrom']);
        $this->assertEquals($user1->id, $message4['useridto']);
        $this->assertFalse($message4['displayblocktime']);
        $this->assertContains('Word.', $message4['text']);
    }

    /**
     * Tests retrieving messages as another user without the proper capabilities.
     */
    public function test_messagearea_messages_as_other_user_without_cap() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        // The person asking for the messages for another user.
        $this->setUser($user1);

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::data_for_messagearea_messages($user2->id, $user3->id);
    }

    /**
     * Tests retrieving messages with messaging disabled.
     */
    public function test_messagearea_messages_messaging_disabled() {
        global $CFG;

        $this->resetAfterTest(true);

        // Create some skeleton data just so we can call the WS.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person asking for the messages for another user.
        $this->setUser($user1);

        // Disable messaging.
        $CFG->messaging = 0;

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::data_for_messagearea_messages($user1->id, $user2->id);
    }

    /**
     * Tests get_conversation_messages for retrieving messages.
     */
    public function test_get_conversation_messages() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();
        $user5 = self::getDataGenerator()->create_user();

        // Create group conversation.
        $conversation = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$user1->id, $user2->id, $user3->id, $user4->id]
        );

        // The person asking for the messages.
        $this->setUser($user1);

        // Send some messages back and forth.
        $time = time();
        testhelper::send_fake_message_to_conversation($user1, $conversation->id, 'Yo!', $time);
        testhelper::send_fake_message_to_conversation($user3, $conversation->id, 'Sup mang?', $time + 1);
        testhelper::send_fake_message_to_conversation($user2, $conversation->id, 'Writing PHPUnit tests!', $time + 2);
        testhelper::send_fake_message_to_conversation($user1, $conversation->id, 'Word.', $time + 3);

        // Retrieve the messages.
        $result = core_message_external::get_conversation_messages($user1->id, $conversation->id);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::get_conversation_messages_returns(),
            $result);

        // Check the results are correct.
        $this->assertEquals($conversation->id, $result['id']);

        // Confirm the members data is correct.
        $members = $result['members'];
        $this->assertCount(3, $members);
        $membersid = [$members[0]['id'], $members[1]['id'], $members[2]['id']];
        $this->assertContains($user1->id, $membersid);
        $this->assertContains($user2->id, $membersid);
        $this->assertContains($user3->id, $membersid);

        $membersfullnames = [$members[0]['fullname'], $members[1]['fullname'], $members[2]['fullname']];
        $this->assertContains(fullname($user1), $membersfullnames);
        $this->assertContains(fullname($user2), $membersfullnames);
        $this->assertContains(fullname($user3), $membersfullnames);

        // Confirm the messages data is correct.
        $messages = $result['messages'];
        $this->assertCount(4, $messages);

        $message1 = $messages[0];
        $message2 = $messages[1];
        $message3 = $messages[2];
        $message4 = $messages[3];

        $this->assertEquals($user1->id, $message1['useridfrom']);
        $this->assertContains('Yo!', $message1['text']);

        $this->assertEquals($user3->id, $message2['useridfrom']);
        $this->assertContains('Sup mang?', $message2['text']);

        $this->assertEquals($user2->id, $message3['useridfrom']);
        $this->assertContains('Writing PHPUnit tests!', $message3['text']);

        $this->assertEquals($user1->id, $message4['useridfrom']);
        $this->assertContains('Word.', $message4['text']);
    }

    /**
     * Tests get_conversation_messages for retrieving messages using timefrom parameter.
     */
    public function test_get_conversation_messages_timefrom() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();

        // Create group conversation.
        $conversation = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$user1->id, $user2->id, $user3->id]
        );

        // The person asking for the messages.
        $this->setUser($user1);

        // Send some messages back and forth.
        $time = time();
        testhelper::send_fake_message_to_conversation($user1, $conversation->id, 'Message 1', $time - 4);
        testhelper::send_fake_message_to_conversation($user2, $conversation->id, 'Message 2', $time - 3);
        testhelper::send_fake_message_to_conversation($user2, $conversation->id, 'Message 3', $time - 2);
        testhelper::send_fake_message_to_conversation($user2, $conversation->id, 'Message 4', $time - 1);

        // Retrieve the messages from $time - 3, which should be the 3 most recent messages.
        $result = core_message_external::get_conversation_messages($user1->id, $conversation->id, 0, 0, false, $time - 3);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::get_conversation_messages_returns(),
            $result);

        // Check the results are correct.
        $this->assertEquals($conversation->id, $result['id']);

        // Confirm the messages data is correct.
        $messages = $result['messages'];
        $this->assertCount(3, $messages);

        $message1 = $messages[0];
        $message2 = $messages[1];
        $message3 = $messages[2];

        $this->assertContains('Message 2', $message1['text']);
        $this->assertContains('Message 3', $message2['text']);
        $this->assertContains('Message 4', $message3['text']);

        // Confirm the members data is correct.
        $members = $result['members'];
        $this->assertCount(1, $members);
        $this->assertEquals($user2->id, $members[0]['id']);
    }

    /**
     * Tests get_conversation_messages for retrieving messages as another user.
     */
    public function test_get_conversation_messages_as_other_user() {
        $this->resetAfterTest(true);

        // Set as admin.
        $this->setAdminUser();

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();

        // Create group conversation.
        $conversation = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$user1->id, $user2->id, $user3->id, $user4->id]
        );

        // Send some messages back and forth.
        $time = time();
        testhelper::send_fake_message_to_conversation($user1, $conversation->id, 'Yo!', $time);
        testhelper::send_fake_message_to_conversation($user3, $conversation->id, 'Sup mang?', $time + 1);
        testhelper::send_fake_message_to_conversation($user2, $conversation->id, 'Writing PHPUnit tests!', $time + 2);
        testhelper::send_fake_message_to_conversation($user1, $conversation->id, 'Word.', $time + 3);

        // Retrieve the messages.
        $result = core_message_external::get_conversation_messages($user1->id, $conversation->id);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::get_conversation_messages_returns(),
            $result);

        // Check the results are correct.
        $this->assertEquals($conversation->id, $result['id']);

        // Confirm the members data is correct.
        $members = $result['members'];
        $this->assertCount(3, $members);
        $membersid = [$members[0]['id'], $members[1]['id'], $members[2]['id']];
        $this->assertContains($user1->id, $membersid);
        $this->assertContains($user2->id, $membersid);
        $this->assertContains($user3->id, $membersid);

        // Confirm the message data is correct.
        $messages = $result['messages'];
        $this->assertCount(4, $messages);

        $message1 = $messages[0];
        $message2 = $messages[1];
        $message3 = $messages[2];
        $message4 = $messages[3];

        $this->assertEquals($user1->id, $message1['useridfrom']);
        $this->assertContains('Yo!', $message1['text']);

        $this->assertEquals($user3->id, $message2['useridfrom']);
        $this->assertContains('Sup mang?', $message2['text']);

        $this->assertEquals($user2->id, $message3['useridfrom']);
        $this->assertContains('Writing PHPUnit tests!', $message3['text']);

        $this->assertEquals($user1->id, $message4['useridfrom']);
        $this->assertContains('Word.', $message4['text']);
    }

    /**
     * Tests get_conversation_messages for retrieving messages as another user without the proper capabilities.
     */
    public function test_get_conversation_messages_as_other_user_without_cap() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();

        // Create group conversation.
        $conversation = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$user1->id, $user2->id, $user3->id, $user4->id]
        );

        // The person asking for the messages for another user.
        $this->setUser($user1);

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::get_conversation_messages($user2->id, $conversation->id);
    }

    /**
     * Tests get_conversation_messages for retrieving messages as another user not in the conversation.
     */
    public function test_get_conversation_messages_as_user_not_in_conversation() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user(); // Not in group.

        // Create group conversation.
        $conversation = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$user1->id, $user2->id]
        );

        // The person asking for the messages for a conversation he does not belong to.
        $this->setUser($user3);

        // Ensure an exception is thrown.
        $this->expectExceptionMessage('User is not part of conversation.');
        core_message_external::get_conversation_messages($user3->id, $conversation->id);
    }

    /**
     * Tests get_conversation_messages for retrieving messages with messaging disabled.
     */
    public function test_get_conversation_messages_messaging_disabled() {
        $this->resetAfterTest(true);

        // Create some skeleton data just so we can call the WS.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();

        // Create group conversation.
        $conversation = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$user1->id, $user2->id, $user3->id, $user4->id]
        );

        // The person asking for the messages for another user.
        $this->setUser($user1);

        // Disable messaging.
        set_config('messaging', 0);

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::get_conversation_messages($user1->id, $conversation->id);
    }

    /**
     * Tests retrieving most recent message.
     */
    public function test_messagearea_get_most_recent_message() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person doing the search.
        $this->setUser($user1);

        // Send some messages back and forth.
        $time = time();
        $this->send_message($user1, $user2, 'Yo!', 0, $time);
        $this->send_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $this->send_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);
        $this->send_message($user2, $user1, 'Word.', 0, $time + 3);

        // Get the most recent message.
        $result = core_message_external::data_for_messagearea_get_most_recent_message($user1->id, $user2->id);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::data_for_messagearea_get_most_recent_message_returns(),
            $result);

        // Check the results are correct.
        $this->assertEquals($user2->id, $result['useridfrom']);
        $this->assertEquals($user1->id, $result['useridto']);
        $this->assertContains('Word.', $result['text']);
    }

    /**
     * Tests retrieving most recent message as another user.
     */
    public function test_messagearea_get_most_recent_message_as_other_user() {
        $this->resetAfterTest(true);

        // The person doing the search.
        $this->setAdminUser();

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // Send some messages back and forth.
        $time = time();
        $this->send_message($user1, $user2, 'Yo!', 0, $time);
        $this->send_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $this->send_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);
        $this->send_message($user2, $user1, 'Word.', 0, $time + 3);

        // Get the most recent message.
        $result = core_message_external::data_for_messagearea_get_most_recent_message($user1->id, $user2->id);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::data_for_messagearea_get_most_recent_message_returns(),
            $result);

        // Check the results are correct.
        $this->assertEquals($user2->id, $result['useridfrom']);
        $this->assertEquals($user1->id, $result['useridto']);
        $this->assertContains('Word.', $result['text']);
    }

    /**
     * Tests retrieving most recent message as another user without the proper capabilities.
     */
    public function test_messagearea_get_most_recent_message_as_other_user_without_cap() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        // The person asking for the most recent message for another user.
        $this->setUser($user1);

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::data_for_messagearea_get_most_recent_message($user2->id, $user3->id);
    }

    /**
     * Tests retrieving most recent message with messaging disabled.
     */
    public function test_messagearea_get_most_recent_message_messaging_disabled() {
        global $CFG;

        $this->resetAfterTest(true);

        // Create some skeleton data just so we can call the WS.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person asking for the most recent message.
        $this->setUser($user1);

        // Disable messaging.
        $CFG->messaging = 0;

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::data_for_messagearea_get_most_recent_message($user1->id, $user2->id);
    }

    /**
     * Tests retrieving a user's profile.
     */
    public function test_messagearea_get_profile() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person asking for the profile information.
        $this->setUser($user1);

        // Get the profile.
        $result = core_message_external::data_for_messagearea_get_profile($user1->id, $user2->id);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::data_for_messagearea_get_profile_returns(),
            $result);

        $this->assertEquals($user2->id, $result['userid']);
        $this->assertEmpty($result['email']);
        $this->assertEmpty($result['country']);
        $this->assertEmpty($result['city']);
        $this->assertEquals(fullname($user2), $result['fullname']);
        $this->assertNull($result['isonline']);
        $this->assertFalse($result['isblocked']);
        $this->assertFalse($result['iscontact']);
    }

    /**
     * Tests retrieving a user's profile as another user.
     */
    public function test_messagearea_profile_as_other_user() {
        $this->resetAfterTest(true);

        // The person asking for the profile information.
        $this->setAdminUser();

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();

        $user2 = new stdClass();
        $user2->country = 'AU';
        $user2->city = 'Perth';
        $user2 = self::getDataGenerator()->create_user($user2);

        // Get the profile.
        $result = core_message_external::data_for_messagearea_get_profile($user1->id, $user2->id);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::data_for_messagearea_get_profile_returns(),
            $result);

        $this->assertEquals($user2->id, $result['userid']);
        $this->assertEquals($user2->email, $result['email']);
        $this->assertEquals(get_string($user2->country, 'countries'), $result['country']);
        $this->assertEquals($user2->city, $result['city']);
        $this->assertEquals(fullname($user2), $result['fullname']);
        $this->assertFalse($result['isonline']);
        $this->assertFalse($result['isblocked']);
        $this->assertFalse($result['iscontact']);
    }

    /**
     * Tests retrieving a user's profile as another user without the proper capabilities.
     */
    public function test_messagearea_profile_as_other_user_without_cap() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        // The person asking for the profile information for another user.
        $this->setUser($user1);

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::data_for_messagearea_get_profile($user2->id, $user3->id);
    }

    /**
     * Tests retrieving a user's profile with messaging disabled.
     */
    public function test_messagearea_profile_messaging_disabled() {
        global $CFG;

        $this->resetAfterTest(true);

        // Create some skeleton data just so we can call the WS.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person asking for the profile information.
        $this->setUser($user1);

        // Disable messaging.
        $CFG->messaging = 0;

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::data_for_messagearea_get_profile($user1->id, $user2->id);
    }

    /**
     * Test marking all message as read with an invalid user.
     */
    public function test_mark_all_messages_as_read_invalid_user_exception() {
        $this->resetAfterTest(true);

        $this->expectException('moodle_exception');
        core_message_external::mark_all_messages_as_read(-2132131, 0);
    }

    /**
     * Test marking all message as read without proper access.
     */
    public function test_mark_all_messages_as_read_access_denied_exception() {
        $this->resetAfterTest(true);

        $sender = $this->getDataGenerator()->create_user();
        $user = $this->getDataGenerator()->create_user();

        $this->setUser($user);
        $this->expectException('moodle_exception');
        core_message_external::mark_all_messages_as_read($sender->id, 0);
    }

    /**
     * Test marking all message as read with missing from user.
     */
    public function test_mark_all_messages_as_read_missing_from_user_exception() {
        $this->resetAfterTest(true);

        $sender = $this->getDataGenerator()->create_user();

        $this->setUser($sender);
        $this->expectException('moodle_exception');
        core_message_external::mark_all_messages_as_read($sender->id, 99999);
    }

    /**
     * Test marking all message as read.
     */
    public function test_mark_all_messages_as_read() {
        global $DB;

        $this->resetAfterTest(true);

        $sender1 = $this->getDataGenerator()->create_user();
        $sender2 = $this->getDataGenerator()->create_user();
        $sender3 = $this->getDataGenerator()->create_user();
        $recipient = $this->getDataGenerator()->create_user();

        $this->setUser($recipient);

        $this->send_message($sender1, $recipient, 'Message');
        $this->send_message($sender1, $recipient, 'Message');
        $this->send_message($sender2, $recipient, 'Message');
        $this->send_message($sender2, $recipient, 'Message');
        $this->send_message($sender3, $recipient, 'Message');
        $this->send_message($sender3, $recipient, 'Message');

        core_message_external::mark_all_messages_as_read($recipient->id, $sender1->id);
        $this->assertEquals(2, $DB->count_records('message_user_actions'));

        core_message_external::mark_all_messages_as_read($recipient->id, 0);
        $this->assertEquals(6, $DB->count_records('message_user_actions'));
    }

    /**
     * Test marking all conversation messages as read with an invalid user.
     */
    public function test_mark_all_conversation_messages_as_read_invalid_user_exception() {
        $this->resetAfterTest(true);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // Send some messages back and forth.
        $time = time();
        $this->send_message($user1, $user2, 'Yo!', 0, $time);
        $this->send_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $this->send_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);
        $this->send_message($user2, $user1, 'Word.', 0, $time + 3);

        $conversationid = \core_message\api::get_conversation_between_users([$user1->id, $user2->id]);

        $this->expectException('moodle_exception');
        core_message_external::mark_all_conversation_messages_as_read(-2132131, $conversationid);
    }

    /**
     * Test marking all conversation messages as read without proper access.
     */
    public function test_mark_all_conversation_messages_as_read_access_denied_exception() {
        $this->resetAfterTest(true);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        // Send some messages back and forth.
        $time = time();
        $this->send_message($user1, $user2, 'Yo!', 0, $time);
        $this->send_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $this->send_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);
        $this->send_message($user2, $user1, 'Word.', 0, $time + 3);

        $conversationid = \core_message\api::get_conversation_between_users([$user1->id, $user2->id]);

        // User 3 is not in the conversation.
        $this->expectException('moodle_exception');
        core_message_external::mark_all_conversation_messages_as_read($user3->id, $conversationid);
    }

    /**
     * Test marking all conversation messages as read for another user.
     */
    public function test_mark_all_conversation_messages_as_read_wrong_user() {
        $this->resetAfterTest(true);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // Send some messages back and forth.
        $time = time();
        $this->send_message($user1, $user2, 'Yo!', 0, $time);
        $this->send_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $this->send_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);
        $this->send_message($user2, $user1, 'Word.', 0, $time + 3);

        $conversationid = \core_message\api::get_conversation_between_users([$user1->id, $user2->id]);

        // Can't mark the messages as read for user 2.
        $this->setUser($user1);
        $this->expectException('moodle_exception');
        core_message_external::mark_all_conversation_messages_as_read($user2->id, $conversationid);
    }

    /**
     * Test marking all conversation messages as admin.
     */
    public function test_mark_all_conversation_messages_as_admin() {
        global $DB;

        $this->resetAfterTest(true);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // Send some messages back and forth.
        $time = time();
        $this->send_message($user1, $user2, 'Yo!', 0, $time);
        $this->send_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $this->send_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);
        $this->send_message($user2, $user1, 'Word.', 0, $time + 3);

        $conversationid = \core_message\api::get_conversation_between_users([$user1->id, $user2->id]);

        // Admin can do anything.
        $this->setAdminUser();
        core_message_external::mark_all_conversation_messages_as_read($user2->id, $conversationid);
        $this->assertEquals(2, $DB->count_records('message_user_actions'));
    }

    /**
     * Test marking all conversation messages.
     */
    public function test_mark_all_conversation_messages_as_read() {
        global $DB;

        $this->resetAfterTest(true);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // Send some messages back and forth.
        $time = time();
        $this->send_message($user1, $user2, 'Yo!', 0, $time);
        $this->send_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $this->send_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);
        $this->send_message($user2, $user1, 'Word.', 0, $time + 3);

        $conversationid = \core_message\api::get_conversation_between_users([$user1->id, $user2->id]);

        // We are the user we want to mark the messages for and we are in the conversation, all good.
        $this->setUser($user1);
        core_message_external::mark_all_conversation_messages_as_read($user1->id, $conversationid);
        $this->assertEquals(2, $DB->count_records('message_user_actions'));
    }

    /**
     * Test getting unread conversation count.
     */
    public function test_get_unread_conversations_count() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();

        // The person wanting the conversation count.
        $this->setUser($user1);

        // Send some messages back and forth, have some different conversations with different users.
        $this->send_message($user1, $user2, 'Yo!');
        $this->send_message($user2, $user1, 'Sup mang?');
        $this->send_message($user1, $user2, 'Writing PHPUnit tests!');
        $this->send_message($user2, $user1, 'Word.');

        $this->send_message($user1, $user3, 'Booyah');
        $this->send_message($user3, $user1, 'Whaaat?');
        $this->send_message($user1, $user3, 'Nothing.');
        $this->send_message($user3, $user1, 'Cool.');

        $this->send_message($user1, $user4, 'Hey mate, you see the new messaging UI in Moodle?');
        $this->send_message($user4, $user1, 'Yah brah, it\'s pretty rad.');
        $this->send_message($user1, $user4, 'Dope.');

        // Get the unread conversation count.
        $result = core_message_external::get_unread_conversations_count($user1->id);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::get_unread_conversations_count_returns(),
            $result);

        $this->assertEquals(3, $result);
    }

    /**
     * Test getting unread conversation count as other user.
     */
    public function test_get_unread_conversations_count_as_other_user() {
        $this->resetAfterTest(true);

        // The person wanting the conversation count.
        $this->setAdminUser();

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();

        // Send some messages back and forth, have some different conversations with different users.
        $this->send_message($user1, $user2, 'Yo!');
        $this->send_message($user2, $user1, 'Sup mang?');
        $this->send_message($user1, $user2, 'Writing PHPUnit tests!');
        $this->send_message($user2, $user1, 'Word.');

        $this->send_message($user1, $user3, 'Booyah');
        $this->send_message($user3, $user1, 'Whaaat?');
        $this->send_message($user1, $user3, 'Nothing.');
        $this->send_message($user3, $user1, 'Cool.');

        $this->send_message($user1, $user4, 'Hey mate, you see the new messaging UI in Moodle?');
        $this->send_message($user4, $user1, 'Yah brah, it\'s pretty rad.');
        $this->send_message($user1, $user4, 'Dope.');

        // Get the unread conversation count.
        $result = core_message_external::get_unread_conversations_count($user1->id);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::get_unread_conversations_count_returns(),
            $result);

        $this->assertEquals(3, $result);
    }

    /**
     * Test getting unread conversation count as other user without proper capability.
     */
    public function test_get_unread_conversations_count_as_other_user_without_cap() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person wanting the conversation count.
        $this->setUser($user1);

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::get_unread_conversations_count($user2->id);
    }

    /**
     * Test deleting conversation.
     */
    public function test_delete_conversation() {
        global $DB;

        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person wanting to delete the conversation.
        $this->setUser($user1);

        // Send some messages back and forth.
        $time = time();
        $m1id = $this->send_message($user1, $user2, 'Yo!', 0, $time);
        $m2id = $this->send_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $m3id = $this->send_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);
        $m4id = $this->send_message($user2, $user1, 'Word.', 0, $time + 3);

        // Delete the conversation.
        core_message_external::delete_conversation($user1->id, $user2->id);

        $muas = $DB->get_records('message_user_actions', array(), 'timecreated ASC');
        $this->assertCount(4, $muas);
        // Sort by id.
        ksort($muas);

        $mua1 = array_shift($muas);
        $mua2 = array_shift($muas);
        $mua3 = array_shift($muas);
        $mua4 = array_shift($muas);

        $this->assertEquals($user1->id, $mua1->userid);
        $this->assertEquals($m1id, $mua1->messageid);
        $this->assertEquals(\core_message\api::MESSAGE_ACTION_DELETED, $mua1->action);

        $this->assertEquals($user1->id, $mua2->userid);
        $this->assertEquals($m2id, $mua2->messageid);
        $this->assertEquals(\core_message\api::MESSAGE_ACTION_DELETED, $mua2->action);

        $this->assertEquals($user1->id, $mua3->userid);
        $this->assertEquals($m3id, $mua3->messageid);
        $this->assertEquals(\core_message\api::MESSAGE_ACTION_DELETED, $mua3->action);

        $this->assertEquals($user1->id, $mua4->userid);
        $this->assertEquals($m4id, $mua4->messageid);
        $this->assertEquals(\core_message\api::MESSAGE_ACTION_DELETED, $mua4->action);
    }

    /**
     * Test deleting conversation as other user.
     */
    public function test_delete_conversation_as_other_user() {
        global $DB;

        $this->resetAfterTest(true);

        $this->setAdminUser();

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // Send some messages back and forth.
        $time = time();
        $m1id = $this->send_message($user1, $user2, 'Yo!', 0, $time);
        $m2id = $this->send_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $m3id = $this->send_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);
        $m4id = $this->send_message($user2, $user1, 'Word.', 0, $time + 3);

        // Delete the conversation.
        core_message_external::delete_conversation($user1->id, $user2->id);

        $muas = $DB->get_records('message_user_actions', array(), 'timecreated ASC');
        $this->assertCount(4, $muas);
        // Sort by id.
        ksort($muas);

        $mua1 = array_shift($muas);
        $mua2 = array_shift($muas);
        $mua3 = array_shift($muas);
        $mua4 = array_shift($muas);

        $this->assertEquals($user1->id, $mua1->userid);
        $this->assertEquals($m1id, $mua1->messageid);
        $this->assertEquals(\core_message\api::MESSAGE_ACTION_DELETED, $mua1->action);

        $this->assertEquals($user1->id, $mua2->userid);
        $this->assertEquals($m2id, $mua2->messageid);
        $this->assertEquals(\core_message\api::MESSAGE_ACTION_DELETED, $mua2->action);

        $this->assertEquals($user1->id, $mua3->userid);
        $this->assertEquals($m3id, $mua3->messageid);
        $this->assertEquals(\core_message\api::MESSAGE_ACTION_DELETED, $mua3->action);

        $this->assertEquals($user1->id, $mua4->userid);
        $this->assertEquals($m4id, $mua4->messageid);
        $this->assertEquals(\core_message\api::MESSAGE_ACTION_DELETED, $mua4->action);
    }

    /**
     * Test deleting conversation as other user without proper capability.
     */
    public function test_delete_conversation_as_other_user_without_cap() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        // Send some messages back and forth.
        $time = time();
        $this->send_message($user1, $user2, 'Yo!', 0, $time);
        $this->send_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $this->send_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);
        $this->send_message($user2, $user1, 'Word.', 0, $time + 3);

        // The person wanting to delete the conversation.
        $this->setUser($user3);

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::delete_conversation($user1->id, $user2->id);
    }

    /**
     * Test deleting conversation with messaging disabled.
     */
    public function test_delete_conversation_messaging_disabled() {
        global $CFG;

        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person wanting to delete the conversation.
        $this->setUser($user1);

        // Disable messaging.
        $CFG->messaging = 0;

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::delete_conversation($user1->id, $user2->id);
    }

    /**
     * Test deleting conversations.
     */
    public function test_delete_conversations_by_id() {
        global $DB;

        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // The person wanting to delete the conversation.
        $this->setUser($user1);

        // Send some messages back and forth.
        $time = time();
        $m1id = $this->send_message($user1, $user2, 'Yo!', 0, $time);
        $m2id = $this->send_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $m3id = $this->send_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);
        $m4id = $this->send_message($user2, $user1, 'Word.', 0, $time + 3);

        $conversationid = \core_message\api::get_conversation_between_users([$user1->id, $user2->id]);

        // Delete the conversation.
        core_message_external::delete_conversations_by_id($user1->id, [$conversationid]);

        $muas = $DB->get_records('message_user_actions', array(), 'timecreated ASC');
        $this->assertCount(4, $muas);
        // Sort by id.
        ksort($muas);

        $mua1 = array_shift($muas);
        $mua2 = array_shift($muas);
        $mua3 = array_shift($muas);
        $mua4 = array_shift($muas);

        $this->assertEquals($user1->id, $mua1->userid);
        $this->assertEquals($m1id, $mua1->messageid);
        $this->assertEquals(\core_message\api::MESSAGE_ACTION_DELETED, $mua1->action);

        $this->assertEquals($user1->id, $mua2->userid);
        $this->assertEquals($m2id, $mua2->messageid);
        $this->assertEquals(\core_message\api::MESSAGE_ACTION_DELETED, $mua2->action);

        $this->assertEquals($user1->id, $mua3->userid);
        $this->assertEquals($m3id, $mua3->messageid);
        $this->assertEquals(\core_message\api::MESSAGE_ACTION_DELETED, $mua3->action);

        $this->assertEquals($user1->id, $mua4->userid);
        $this->assertEquals($m4id, $mua4->messageid);
        $this->assertEquals(\core_message\api::MESSAGE_ACTION_DELETED, $mua4->action);
    }

    /**
     * Test deleting conversations as other user.
     */
    public function test_delete_conversations_by_id_as_other_user() {
        global $DB;

        $this->resetAfterTest(true);

        $this->setAdminUser();

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // Send some messages back and forth.
        $time = time();
        $m1id = $this->send_message($user1, $user2, 'Yo!', 0, $time);
        $m2id = $this->send_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $m3id = $this->send_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);
        $m4id = $this->send_message($user2, $user1, 'Word.', 0, $time + 3);

        $conversationid = \core_message\api::get_conversation_between_users([$user1->id, $user2->id]);

        // Delete the conversation.
        core_message_external::delete_conversations_by_id($user1->id, [$conversationid]);

        $muas = $DB->get_records('message_user_actions', array(), 'timecreated ASC');
        $this->assertCount(4, $muas);
        // Sort by id.
        ksort($muas);

        $mua1 = array_shift($muas);
        $mua2 = array_shift($muas);
        $mua3 = array_shift($muas);
        $mua4 = array_shift($muas);

        $this->assertEquals($user1->id, $mua1->userid);
        $this->assertEquals($m1id, $mua1->messageid);
        $this->assertEquals(\core_message\api::MESSAGE_ACTION_DELETED, $mua1->action);

        $this->assertEquals($user1->id, $mua2->userid);
        $this->assertEquals($m2id, $mua2->messageid);
        $this->assertEquals(\core_message\api::MESSAGE_ACTION_DELETED, $mua2->action);

        $this->assertEquals($user1->id, $mua3->userid);
        $this->assertEquals($m3id, $mua3->messageid);
        $this->assertEquals(\core_message\api::MESSAGE_ACTION_DELETED, $mua3->action);

        $this->assertEquals($user1->id, $mua4->userid);
        $this->assertEquals($m4id, $mua4->messageid);
        $this->assertEquals(\core_message\api::MESSAGE_ACTION_DELETED, $mua4->action);
    }

    /**
     * Test deleting conversations as other user without proper capability.
     */
    public function test_delete_conversations_by_id_as_other_user_without_cap() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        // Send some messages back and forth.
        $time = time();
        $this->send_message($user1, $user2, 'Yo!', 0, $time);
        $this->send_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $this->send_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);
        $this->send_message($user2, $user1, 'Word.', 0, $time + 3);

        $conversationid = \core_message\api::get_conversation_between_users([$user1->id, $user2->id]);

        // The person wanting to delete the conversation.
        $this->setUser($user3);

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::delete_conversations_by_id($user1->id, [$conversationid]);
    }

    /**
     * Test deleting conversations with messaging disabled.
     */
    public function test_delete_conversations_by_id_messaging_disabled() {
        global $CFG;

        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // Send some messages back and forth.
        $time = time();
        $this->send_message($user1, $user2, 'Yo!', 0, $time);
        $this->send_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $this->send_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);
        $this->send_message($user2, $user1, 'Word.', 0, $time + 3);

        $conversationid = \core_message\api::get_conversation_between_users([$user1->id, $user2->id]);

        // The person wanting to delete the conversation.
        $this->setUser($user1);

        // Disable messaging.
        $CFG->messaging = 0;

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::delete_conversations_by_id($user1->id, [$conversationid]);
    }

    /**
     * Test get message processor.
     */
    public function test_get_message_processor() {
        $this->resetAfterTest(true);

        // Create a user.
        $user1 = self::getDataGenerator()->create_user();

        // Set you as the user.
        $this->setUser($user1);

        // Get the message processors.
        $result = core_message_external::get_message_processor($user1->id, 'popup');

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::get_message_processor_returns(), $result);

        $this->assertNotEmpty($result['systemconfigured']);
        $this->assertNotEmpty($result['userconfigured']);
    }

    /**
     * Test get_user_notification_preferences
     */
    public function test_get_user_message_preferences() {
        $this->resetAfterTest(true);

        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);

        // Enable site-wide messagging privacy setting. The user will be able to receive messages from everybody.
        set_config('messagingallusers', true);

        // Set a couple of preferences to test.
        set_user_preference('message_provider_moodle_instantmessage_loggedin', 'email', $user);
        set_user_preference('message_provider_moodle_instantmessage_loggedoff', 'email', $user);
        set_user_preference('message_blocknoncontacts', \core_message\api::MESSAGE_PRIVACY_SITE, $user);

        $prefs = core_message_external::get_user_message_preferences();
        $prefs = external_api::clean_returnvalue(core_message_external::get_user_message_preferences_returns(), $prefs);
        $this->assertEquals($user->id, $prefs['preferences']['userid']);

        // Check components.
        $this->assertCount(1, $prefs['preferences']['components']);
        $this->assertEquals(\core_message\api::MESSAGE_PRIVACY_SITE, $prefs['blocknoncontacts']);

        // Check some preferences that we previously set.
        $found = false;
        foreach ($prefs['preferences']['components'] as $component) {
            foreach ($component['notifications'] as $prefdata) {
                if ($prefdata['preferencekey'] != 'message_provider_moodle_instantmessage') {
                    continue;
                }
                foreach ($prefdata['processors'] as $processor) {
                    if ($processor['name'] == 'email') {
                        $this->assertTrue($processor['loggedin']['checked']);
                        $this->assertTrue($processor['loggedoff']['checked']);
                        $found = true;
                    }
                }
            }
        }
        $this->assertTrue($found);
    }

    /**
     * Test get_user_message_preferences permissions
     */
    public function test_get_user_message_preferences_permissions() {
        $this->resetAfterTest(true);

        $user = self::getDataGenerator()->create_user();
        $otheruser = self::getDataGenerator()->create_user();
        $this->setUser($user);

        $this->expectException('moodle_exception');
        $prefs = core_message_external::get_user_message_preferences($otheruser->id);
    }

    /**
     * Comparison function for sorting contacts.
     *
     * @param array $a
     * @param array $b
     * @return bool
     */
    protected static function sort_contacts($a, $b) {
        return $a['userid'] > $b['userid'];
    }

    /**
     * Comparison function for sorting contacts.
     *
     * @param array $a
     * @param array $b
     * @return bool
     */
    protected static function sort_contacts_id($a, $b) {
        return $a['id'] > $b['id'];
    }

    /**
     * Test verifying that conversations can be marked as favourite conversations.
     */
    public function test_set_favourite_conversations_basic() {
        $this->resetAfterTest();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        // Now, create some conversations.
        $time = time();
        $this->send_message($user1, $user2, 'Yo!', 0, $time);
        $this->send_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $this->send_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);

        $this->send_message($user1, $user3, 'Booyah');
        $this->send_message($user3, $user1, 'Whaaat?');
        $this->send_message($user1, $user3, 'Nothing.');

        $this->send_message($user1, $user4, 'Hey mate, you see the new messaging UI in Moodle?');
        $this->send_message($user4, $user1, 'Yah brah, it\'s pretty rad.');

        // Favourite 2 conversations as user 1.
        $conversation1 = \core_message\api::get_conversation_between_users([$user1->id, $user2->id]);
        $conversation2 = \core_message\api::get_conversation_between_users([$user1->id, $user3->id]);
        $result = core_message_external::set_favourite_conversations($user1->id, [$conversation1, $conversation2]);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::set_favourite_conversations_returns(), $result);
        $this->assertCount(0, $result);
    }

    /**
     * Test confirming that a user can't favourite a conversation on behalf of another user.
     */
    public function test_set_favourite_conversations_another_users_conversation() {
        $this->resetAfterTest();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        $this->setUser($user3);

        // Now, create some conversations.
        $time = time();
        $this->send_message($user1, $user2, 'Yo!', 0, $time);
        $this->send_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $this->send_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);

        $this->send_message($user1, $user3, 'Booyah');
        $this->send_message($user3, $user1, 'Whaaat?');
        $this->send_message($user1, $user3, 'Nothing.');

        // Try to favourite conversation 1 for user 2, as user3.
        $conversation1 = \core_message\api::get_conversation_between_users([$user1->id, $user2->id]);
        $this->expectException(\moodle_exception::class);
        $result = core_message_external::set_favourite_conversations($user2->id, [$conversation1]);
    }

    /**
     * Test confirming that a user can't mark a conversation as their own favourite if it's a conversation they're not a member of.
     */
    public function test_set_favourite_conversations_non_member() {
        $this->resetAfterTest();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        $this->setUser($user3);

        // Now, create some conversations.
        $time = time();
        $this->send_message($user1, $user2, 'Yo!', 0, $time);
        $this->send_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $this->send_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);

        $this->send_message($user1, $user3, 'Booyah');
        $this->send_message($user3, $user1, 'Whaaat?');
        $this->send_message($user1, $user3, 'Nothing.');

        // Try to favourite conversation 1 as user 3.
        $conversation1 = \core_message\api::get_conversation_between_users([$user1->id, $user2->id]);
        $conversation2 = \core_message\api::get_conversation_between_users([$user1->id, $user3->id]);
        $this->expectException(\moodle_exception::class);
        $result = core_message_external::set_favourite_conversations($user3->id, [$conversation1]);
    }

    /**
     * Test confirming that a user can't favourite a non-existent conversation.
     */
    public function test_set_favourite_conversations_non_existent_conversation() {
        $this->resetAfterTest();

        $user1 = self::getDataGenerator()->create_user();
        $this->setUser($user1);

        // Try to favourite a non-existent conversation.
        $this->expectException(\moodle_exception::class);
        $result = core_message_external::set_favourite_conversations($user1->id, [0]);
    }

    /**
     * Test confirming that a user can unset a favourite conversation, or list of favourite conversations.
     */
    public function test_unset_favourite_conversations_basic() {
        $this->resetAfterTest();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();

        $this->setUser($user1);

        // Now, create some conversations.
        $time = time();
        $this->send_message($user1, $user2, 'Yo!', 0, $time);
        $this->send_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $this->send_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);

        $this->send_message($user1, $user3, 'Booyah');
        $this->send_message($user3, $user1, 'Whaaat?');
        $this->send_message($user1, $user3, 'Nothing.');

        $this->send_message($user1, $user4, 'Hey mate, you see the new messaging UI in Moodle?');
        $this->send_message($user4, $user1, 'Yah brah, it\'s pretty rad.');

        // Favourite 2 conversations as user 1.
        $conversation1 = \core_message\api::get_conversation_between_users([$user1->id, $user2->id]);
        $conversation2 = \core_message\api::get_conversation_between_users([$user1->id, $user3->id]);
        \core_message\api::set_favourite_conversation($conversation1, $user1->id);
        \core_message\api::set_favourite_conversation($conversation2, $user1->id);
        // Consider first conversations is self-conversation.
        $this->assertCount(3, \core_message\api::get_conversations($user1->id, 0, 20, null, true));

        // Unset favourite self-conversation.
        $selfconversation = \core_message\api::get_self_conversation($user1->id);
        $result = core_message_external::unset_favourite_conversations($user1->id, [$selfconversation->id]);
        $this->assertCount(2, \core_message\api::get_conversations($user1->id, 0, 20, null, true));

        // Now, using the web service, unset the favourite conversations.
        $result = core_message_external::unset_favourite_conversations($user1->id, [$conversation1, $conversation2]);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::unset_favourite_conversations_returns(), $result);
        $this->assertCount(0, $result);
    }

    /**
     * Test confirming that a user can't unfavourite a conversation for another user.
     */
    public function test_unset_favourite_conversations_another_users_conversation() {
        $this->resetAfterTest();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        $this->setUser($user3);

        // Now, create some conversations.
        $time = time();
        $this->send_message($user1, $user2, 'Yo!', 0, $time);
        $this->send_message($user2, $user1, 'Sup mang?', 0, $time + 1);
        $this->send_message($user1, $user2, 'Writing PHPUnit tests!', 0, $time + 2);

        $this->send_message($user1, $user3, 'Booyah');
        $this->send_message($user3, $user1, 'Whaaat?');
        $this->send_message($user1, $user3, 'Nothing.');

        // Favourite conversation 1 for user1. The current user ($USER) isn't checked for this action.
        $conversation1 = \core_message\api::get_conversation_between_users([$user1->id, $user2->id]);
        \core_message\api::set_favourite_conversation($conversation1, $user1->id);
        // Consider first conversations is self-conversation.
        $this->assertCount(2, \core_message\api::get_conversations($user1->id, 0, 20, null, true));

        // Try to unfavourite conversation 1 for user 2, as user3.
        $conversation1 = \core_message\api::get_conversation_between_users([$user1->id, $user2->id]);
        $this->expectException(\moodle_exception::class);
        $result = core_message_external::unset_favourite_conversations($user2->id, [$conversation1]);
    }

    /**
     * Test confirming that a user can't unfavourite a non-existent conversation.
     */
    public function test_unset_favourite_conversations_non_existent_conversation() {
        $this->resetAfterTest();

        $user1 = self::getDataGenerator()->create_user();
        $this->setUser($user1);

        // Try to unfavourite a non-existent conversation.
        $this->expectException(\moodle_exception::class);
        $result = core_message_external::unset_favourite_conversations($user1->id, [0]);
    }

    /**
     * Helper to seed the database with initial state.
     */
    protected function create_conversation_test_data() {
        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();

        $time = 1;

        // Create some conversations. We want:
        // 1) At least one of each type (group, individual) of which user1 IS a member and DID send the most recent message.
        // 2) At least one of each type (group, individual) of which user1 IS a member and DID NOT send the most recent message.
        // 3) At least one of each type (group, individual) of which user1 IS NOT a member.
        // 4) At least two group conversation having 0 messages, of which user1 IS a member (To confirm conversationid ordering).
        // 5) At least one group conversation having 0 messages, of which user1 IS NOT a member.

        // Individual conversation, user1 is a member, last message from other user.
        $ic1 = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [$user1->id, $user2->id]);
        testhelper::send_fake_message_to_conversation($user1, $ic1->id, 'Message 1', $time);
        testhelper::send_fake_message_to_conversation($user2, $ic1->id, 'Message 2', $time + 1);

        // Individual conversation, user1 is a member, last message from user1.
        $ic2 = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [$user1->id, $user3->id]);
        testhelper::send_fake_message_to_conversation($user3, $ic2->id, 'Message 3', $time + 2);
        testhelper::send_fake_message_to_conversation($user1, $ic2->id, 'Message 4', $time + 3);

        // Individual conversation, user1 is not a member.
        $ic3 = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [$user2->id, $user3->id]);
        testhelper::send_fake_message_to_conversation($user2, $ic3->id, 'Message 5', $time + 4);
        testhelper::send_fake_message_to_conversation($user3, $ic3->id, 'Message 6', $time + 5);

        // Group conversation, user1 is not a member.
        $gc1 = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$user2->id, $user3->id, $user4->id], 'Project discussions');
        testhelper::send_fake_message_to_conversation($user2, $gc1->id, 'Message 7', $time + 6);
        testhelper::send_fake_message_to_conversation($user4, $gc1->id, 'Message 8', $time + 7);

        // Group conversation, user1 is a member, last message from another user.
        $gc2 = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$user1->id, $user3->id, $user4->id], 'Group chat');
        testhelper::send_fake_message_to_conversation($user1, $gc2->id, 'Message 9', $time + 8);
        testhelper::send_fake_message_to_conversation($user3, $gc2->id, 'Message 10', $time + 9);
        testhelper::send_fake_message_to_conversation($user4, $gc2->id, 'Message 11', $time + 10);

        // Group conversation, user1 is a member, last message from user1.
        $gc3 = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$user1->id, $user2->id, $user3->id, $user4->id], 'Group chat again!');
        testhelper::send_fake_message_to_conversation($user4, $gc3->id, 'Message 12', $time + 11);
        testhelper::send_fake_message_to_conversation($user3, $gc3->id, 'Message 13', $time + 12);
        testhelper::send_fake_message_to_conversation($user1, $gc3->id, 'Message 14', $time + 13);

        // Empty group conversations (x2), user1 is a member.
        $gc4 = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$user1->id, $user2->id, $user3->id], 'Empty group');
        $gc5 = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$user1->id, $user2->id, $user4->id], 'Another empty group');

        // Empty group conversation, user1 is NOT a member.
        $gc6 = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$user2->id, $user3->id, $user4->id], 'Empty group 3');

        return [$user1, $user2, $user3, $user4, $ic1, $ic2, $ic3, $gc1, $gc2, $gc3, $gc4, $gc5, $gc6];
    }

    /**
     * Test confirming the basic use of get_conversations, with no limits, nor type or favourite restrictions.
     */
    public function test_get_conversations_no_restrictions() {
        $this->resetAfterTest(true);

        // Get a bunch of conversations, some group, some individual and in different states.
        list($user1, $user2, $user3, $user4, $ic1, $ic2, $ic3,
            $gc1, $gc2, $gc3, $gc4, $gc5, $gc6) = $this->create_conversation_test_data();

        // The user making the request.
        $this->setUser($user1);

        // Get all conversations for user1.
        $result = core_message_external::get_conversations($user1->id);
        $result = external_api::clean_returnvalue(core_message_external::get_conversations_returns(), $result);
        $conversations = $result['conversations'];

        $selfconversation = \core_message\api::get_self_conversation($user1->id);

        // Verify there are 7 conversations: 2 individual, 2 group with message, 2 group without messages
        // and a self-conversation.
        // The conversations with the most recent messages should be listed first, followed by the most newly created
        // conversations without messages.
        $this->assertCount(7, $conversations);
        $this->assertEquals($gc3->id, $conversations[0]['id']);
        $this->assertEquals($gc2->id, $conversations[1]['id']);
        $this->assertEquals($ic2->id, $conversations[2]['id']);
        $this->assertEquals($ic1->id, $conversations[3]['id']);
        $this->assertEquals($gc5->id, $conversations[4]['id']);
        $this->assertEquals($gc4->id, $conversations[5]['id']);
        $this->assertEquals($selfconversation->id, $conversations[6]['id']);

        foreach ($conversations as $conv) {
            $this->assertArrayHasKey('id', $conv);
            $this->assertArrayHasKey('name', $conv);
            $this->assertArrayHasKey('subname', $conv);
            $this->assertArrayHasKey('imageurl', $conv);
            $this->assertArrayHasKey('type', $conv);
            $this->assertArrayHasKey('membercount', $conv);
            $this->assertArrayHasKey('isfavourite', $conv);
            $this->assertArrayHasKey('isread', $conv);
            $this->assertArrayHasKey('unreadcount', $conv);
            $this->assertArrayHasKey('members', $conv);
            foreach ($conv['members'] as $member) {
                $this->assertArrayHasKey('id', $member);
                $this->assertArrayHasKey('fullname', $member);
                $this->assertArrayHasKey('profileimageurl', $member);
                $this->assertArrayHasKey('profileimageurlsmall', $member);
                $this->assertArrayHasKey('isonline', $member);
                $this->assertArrayHasKey('showonlinestatus', $member);
                $this->assertArrayHasKey('isblocked', $member);
                $this->assertArrayHasKey('iscontact', $member);
                $this->assertArrayHasKey('isdeleted', $member);
                $this->assertArrayHasKey('canmessage', $member);
                $this->assertArrayHasKey('requirescontact', $member);
                $this->assertArrayHasKey('contactrequests', $member);
            }
            $this->assertArrayHasKey('messages', $conv);
            foreach ($conv['messages'] as $message) {
                $this->assertArrayHasKey('id', $message);
                $this->assertArrayHasKey('useridfrom', $message);
                $this->assertArrayHasKey('text', $message);
                $this->assertArrayHasKey('timecreated', $message);
            }
        }
    }

    /**
     * Test verifying that html format messages are supported, and that message_format_message_text() is being called appropriately.
     */
    public function test_get_conversations_message_format() {
        $this->resetAfterTest();

        global $DB;
        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // Create conversation.
        $conversation = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [$user1->id, $user2->id]
        );

        // Send some messages back and forth.
        $time = 1;
        testhelper::send_fake_message_to_conversation($user2, $conversation->id, 'Sup mang?', $time + 1);
        $mid = testhelper::send_fake_message_to_conversation($user1, $conversation->id, '<a href="#">A link</a>', $time + 2);
        $message = $DB->get_record('messages', ['id' => $mid]);

        // The user in scope.
        $this->setUser($user1);

        // Verify the format of the html message.
        $expectedmessagetext = message_format_message_text($message);
        $result = core_message_external::get_conversations($user1->id);
        $result = external_api::clean_returnvalue(core_message_external::get_conversations_returns(), $result);
        $conversations = $result['conversations'];
        $messages = $conversations[0]['messages'];
        $this->assertEquals($expectedmessagetext, $messages[0]['text']);
    }

    /**
     * Tests retrieving conversations with a limit and offset to ensure pagination works correctly.
     */
    public function test_get_conversations_limit_offset() {
        $this->resetAfterTest(true);

        // Get a bunch of conversations, some group, some individual and in different states.
        list($user1, $user2, $user3, $user4, $ic1, $ic2, $ic3,
            $gc1, $gc2, $gc3, $gc4, $gc5, $gc6) = $this->create_conversation_test_data();

        // The user making the request.
        $this->setUser($user1);

        // Get all conversations for user1.
        $result = core_message_external::get_conversations($user1->id, 0, 1);
        $result = external_api::clean_returnvalue(core_message_external::get_conversations_returns(), $result);
        $conversations = $result['conversations'];

        // Verify the first conversation.
        $this->assertCount(1, $conversations);
        $conversation = array_shift($conversations);
        $this->assertEquals($gc3->id, $conversation['id']);

        // Verify the next conversation.
        $result = core_message_external::get_conversations($user1->id, 1, 1);
        $result = external_api::clean_returnvalue(core_message_external::get_conversations_returns(), $result);
        $conversations = $result['conversations'];
        $this->assertCount(1, $conversations);
        $this->assertEquals($gc2->id, $conversations[0]['id']);

        // Verify the next conversation.
        $result = core_message_external::get_conversations($user1->id, 2, 1);
        $result = external_api::clean_returnvalue(core_message_external::get_conversations_returns(), $result);
        $conversations = $result['conversations'];
        $this->assertCount(1, $conversations);
        $this->assertEquals($ic2->id, $conversations[0]['id']);

        // Skip one and get both empty conversations.
        $result = core_message_external::get_conversations($user1->id, 4, 2);
        $result = external_api::clean_returnvalue(core_message_external::get_conversations_returns(), $result);
        $conversations = $result['conversations'];
        $this->assertCount(2, $conversations);
        $this->assertEquals($gc5->id, $conversations[0]['id']);
        $this->assertEmpty($conversations[0]['messages']);
        $this->assertEquals($gc4->id, $conversations[1]['id']);
        $this->assertEmpty($conversations[1]['messages']);

        // Ask for an offset that doesn't exist and verify no conversations are returned.
        $conversations = \core_message\api::get_conversations($user1->id, 10, 1);
        $this->assertCount(0, $conversations);
    }

    /**
     * Test verifying the type filtering behaviour of the get_conversations external method.
     */
    public function test_get_conversations_type_filter() {
        $this->resetAfterTest(true);

        // Get a bunch of conversations, some group, some individual and in different states.
        list($user1, $user2, $user3, $user4, $ic1, $ic2, $ic3,
            $gc1, $gc2, $gc3, $gc4, $gc5, $gc6) = $this->create_conversation_test_data();

        // The user making the request.
        $this->setUser($user1);

        // Verify we can ask for only individual conversations.
        $result = core_message_external::get_conversations($user1->id, 0, 20,
            \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL);
        $result = external_api::clean_returnvalue(core_message_external::get_conversations_returns(), $result);
        $conversations = $result['conversations'];
        $this->assertCount(2, $conversations);

        // Verify we can ask for only group conversations.
        $result = core_message_external::get_conversations($user1->id, 0, 20,
            \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP);
        $result = external_api::clean_returnvalue(core_message_external::get_conversations_returns(), $result);
        $conversations = $result['conversations'];
        $this->assertCount(4, $conversations);

        // Verify an exception is thrown if an unrecognized type is specified.
        $this->expectException(\moodle_exception::class);
        core_message_external::get_conversations($user1->id, 0, 20, 0);
    }

    /**
     * Tests retrieving conversations when a 'self' conversation exists.
     */
    public function test_get_conversations_self_conversations() {
        global $DB;
        $this->resetAfterTest();

        // Create a conversation between one user and themself.
        $user1 = self::getDataGenerator()->create_user();
        $conversation = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_SELF,
            [$user1->id]);
        testhelper::send_fake_message_to_conversation($user1, $conversation->id, 'Test message to self!');

        // Verify we are in a 'self' conversation state.
        $members = $DB->get_records('message_conversation_members', ['conversationid' => $conversation->id]);
        $this->assertCount(1, $members);
        $member = array_pop($members);
        $this->assertEquals($user1->id, $member->userid);

        // Verify this conversation is returned by the method.
        $this->setUser($user1);
        $result = core_message_external::get_conversations($user1->id, 0, 20);
        $result = external_api::clean_returnvalue(core_message_external::get_conversations_returns(), $result);
        $conversations = $result['conversations'];
        $this->assertCount(1, $conversations);
    }

    /**
     * Tests retrieving conversations when a conversation contains a deleted user.
     */
    public function test_get_conversations_deleted_user() {
        $this->resetAfterTest(true);

        // Get a bunch of conversations, some group, some individual and in different states.
        list($user1, $user2, $user3, $user4, $ic1, $ic2, $ic3,
            $gc1, $gc2, $gc3, $gc4, $gc5, $gc6) = $this->create_conversation_test_data();

        // The user making the request.
        $this->setUser($user1);

        $selfconversation = \core_message\api::get_self_conversation($user1->id);

        // Delete the second user and retrieve the conversations.
        // We should have 6 still, as conversations with soft-deleted users are still returned.
        // Group conversations are also present, albeit with less members.
        delete_user($user2);
        $result = core_message_external::get_conversations($user1->id);
        $result = external_api::clean_returnvalue(core_message_external::get_conversations_returns(), $result);
        $conversations = $result['conversations'];
        $this->assertCount(7, $conversations);
        $this->assertEquals($gc3->id, $conversations[0]['id']);
        $this->assertcount(1, $conversations[0]['members']);
        $this->assertEquals($gc2->id, $conversations[1]['id']);
        $this->assertcount(1, $conversations[1]['members']);
        $this->assertEquals($ic2->id, $conversations[2]['id']);
        $this->assertEquals($ic1->id, $conversations[3]['id']);
        $this->assertEquals($gc5->id, $conversations[4]['id']);
        $this->assertEquals($gc4->id, $conversations[5]['id']);
        $this->assertEquals($selfconversation->id, $conversations[6]['id']);

        // Delete a user from a group conversation where that user had sent the most recent message.
        // This user will still be present in the members array, as will the message in the messages array.
        delete_user($user4);
        $result = core_message_external::get_conversations($user1->id);
        $result = external_api::clean_returnvalue(core_message_external::get_conversations_returns(), $result);
        $conversations = $result['conversations'];
        $this->assertCount(7, $conversations);
        $this->assertEquals($gc2->id, $conversations[1]['id']);
        $this->assertcount(1, $conversations[1]['members']);
        $this->assertEquals($user4->id, $conversations[1]['members'][0]['id']);
        $this->assertcount(1, $conversations[1]['messages']);
        $this->assertEquals($user4->id, $conversations[1]['messages'][0]['useridfrom']);

        // Delete the third user and retrieve the conversations.
        // We should have 7 still (including self-conversation), as conversations with soft-deleted users are still returned.
        // Group conversations are also present, albeit with less members.
        delete_user($user3);
        $result = core_message_external::get_conversations($user1->id);
        $result = external_api::clean_returnvalue(core_message_external::get_conversations_returns(), $result);
        $conversations = $result['conversations'];
        $this->assertCount(7, $conversations);
        $this->assertEquals($gc3->id, $conversations[0]['id']);
        $this->assertcount(1, $conversations[0]['members']);
        $this->assertEquals($gc2->id, $conversations[1]['id']);
        $this->assertcount(1, $conversations[1]['members']);
        $this->assertEquals($ic2->id, $conversations[2]['id']);
        $this->assertEquals($ic1->id, $conversations[3]['id']);
        $this->assertEquals($gc5->id, $conversations[4]['id']);
        $this->assertEquals($gc4->id, $conversations[5]['id']);
        $this->assertEquals($selfconversation->id, $conversations[6]['id']);
    }

    /**
     * Tests retrieving conversations when a conversation contains a deleted from the database user.
     */
    public function test_get_conversations_deleted_user_from_database() {
        global $DB;

        $this->resetAfterTest();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        $conversation1 = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [
                $user1->id,
                $user2->id
            ],
            'Individual conversation 1'
        );

        testhelper::send_fake_message_to_conversation($user1, $conversation1->id, 'A');
        testhelper::send_fake_message_to_conversation($user2, $conversation1->id, 'B');
        testhelper::send_fake_message_to_conversation($user1, $conversation1->id, 'C');

        $conversation2 = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [
                $user1->id,
                $user3->id
            ],
            'Individual conversation 2'
        );

        testhelper::send_fake_message_to_conversation($user1, $conversation2->id, 'A');
        testhelper::send_fake_message_to_conversation($user3, $conversation2->id, 'B');
        testhelper::send_fake_message_to_conversation($user1, $conversation2->id, 'C');

        $this->setUser($user1);

        // Delete the second user (from DB as well as this could happen in the past).
        delete_user($user2);
        $DB->delete_records('user', ['id' => $user2->id]);
        $result = core_message_external::get_conversations($user1->id, 0, 20, 1, false);
        $result = external_api::clean_returnvalue(core_message_external::get_conversations_returns(), $result);

        $conversation = $result['conversations'];

        $this->assertCount(1, $conversation);

        $conversation = reset($conversation);

        $this->assertEquals($conversation2->id, $conversation['id']);
    }

    /**
     * Test verifying the behaviour of get_conversations() when fetching favourite conversations.
     */
    public function test_get_conversations_favourite_conversations() {
        $this->resetAfterTest(true);

        // Get a bunch of conversations, some group, some individual and in different states.
        list($user1, $user2, $user3, $user4, $ic1, $ic2, $ic3,
            $gc1, $gc2, $gc3, $gc4, $gc5, $gc6) = $this->create_conversation_test_data();

        // The user making the request.
        $this->setUser($user1);

        // Unset favourite self-conversation.
        $selfconversation = \core_message\api::get_self_conversation($user1->id);
        \core_message\api::unset_favourite_conversation($selfconversation->id, $user1->id);

        // Try to get ONLY favourite conversations, when no favourites exist.
        $result = core_message_external::get_conversations($user1->id, 0, 20, null, true);
        $result = external_api::clean_returnvalue(core_message_external::get_conversations_returns(), $result);
        $conversations = $result['conversations'];
        $this->assertEquals([], $conversations);

        // Try to get NO favourite conversations, when no favourites exist.
        $result = core_message_external::get_conversations($user1->id, 0, 20, null, false);
        $result = external_api::clean_returnvalue(core_message_external::get_conversations_returns(), $result);
        $conversations = $result['conversations'];
        // Consider first conversations is self-conversation.
        $this->assertCount(7, $conversations);

        // Mark a few conversations as favourites.
        \core_message\api::set_favourite_conversation($ic1->id, $user1->id);
        \core_message\api::set_favourite_conversation($gc2->id, $user1->id);
        \core_message\api::set_favourite_conversation($gc5->id, $user1->id);

        // Get the conversations, first with no restrictions, confirming the favourite status of the conversations.
        $result = core_message_external::get_conversations($user1->id);
        $result = external_api::clean_returnvalue(core_message_external::get_conversations_returns(), $result);
        $conversations = $result['conversations'];
        $this->assertCount(7, $conversations);
        foreach ($conversations as $conv) {
            if (in_array($conv['id'], [$ic1->id, $gc2->id, $gc5->id])) {
                $this->assertTrue($conv['isfavourite']);
            }
        }

        // Now, get ONLY favourite conversations.
        $result = core_message_external::get_conversations($user1->id, 0, 20, null, true);
        $result = external_api::clean_returnvalue(core_message_external::get_conversations_returns(), $result);
        $conversations = $result['conversations'];
        $this->assertCount(3, $conversations);
        foreach ($conversations as $conv) {
            $this->assertTrue($conv['isfavourite']);
        }

        // Now, try ONLY favourites of type 'group'.
        $conversations = \core_message\api::get_conversations($user1->id, 0, 20,
            \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP, true);
        $this->assertCount(2, $conversations);
        foreach ($conversations as $conv) {
            $this->assertTrue($conv->isfavourite);
        }

        // And NO favourite conversations.
        $result = core_message_external::get_conversations($user1->id, 0, 20, null, false);
        $result = external_api::clean_returnvalue(core_message_external::get_conversations_returns(), $result);
        $conversations = $result['conversations'];
        $this->assertCount(4, $conversations);
        foreach ($conversations as $conv) {
            $this->assertFalse($conv['isfavourite']);
        }
    }

    /**
     * Test verifying that group linked conversations are returned and contain a subname matching the course name.
     */
    public function test_get_conversations_group_linked() {
        $this->resetAfterTest();
        global $CFG, $DB;

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        $course1 = $this->getDataGenerator()->create_course();

        // Create a group with a linked conversation.
        $this->setAdminUser();
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id);
        $group1 = $this->getDataGenerator()->create_group([
            'courseid' => $course1->id,
            'enablemessaging' => 1,
            'picturepath' => $CFG->dirroot . '/lib/tests/fixtures/gd-logo.png'
        ]);

        // Add users to group1.
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user2->id));

        $result = core_message_external::get_conversations($user1->id, 0, 20, null, false);
        $result = external_api::clean_returnvalue(core_message_external::get_conversations_returns(), $result);
        $conversations = $result['conversations'];

        $this->assertEquals(2, $conversations[0]['membercount']);
        $this->assertEquals($course1->shortname, $conversations[0]['subname']);
        $groupimageurl = get_group_picture_url($group1, $group1->courseid, true);
        $this->assertEquals($groupimageurl, $conversations[0]['imageurl']);

        // Now, disable the conversation linked to the group and verify it's no longer returned.
        $DB->set_field('message_conversations', 'enabled', 0, ['id' => $conversations[0]['id']]);
        $result = core_message_external::get_conversations($user1->id, 0, 20, null, false);
        $result = external_api::clean_returnvalue(core_message_external::get_conversations_returns(), $result);
        $conversations = $result['conversations'];
        $this->assertCount(0, $conversations);
    }

    /**
     * Test that group conversations containing MathJax don't break the WebService.
     */
    public function test_get_conversations_group_with_mathjax() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        // Enable MathJax filter in content and headings.
        $this->configure_filters([
            ['name' => 'mathjaxloader', 'state' => TEXTFILTER_ON, 'move' => -1, 'applytostrings' => true],
        ]);

        // Create some users, a course and a group with a linked conversation.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $coursename = 'Course $$(a+b)=2$$';
        $groupname = 'Group $$(a+b)=2$$';
        $course1 = $this->getDataGenerator()->create_course(['shortname' => $coursename]);

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $group1 = $this->getDataGenerator()->create_group([
            'name' => $groupname,
            'courseid' => $course1->id,
            'enablemessaging' => 1,
        ]);

        // Add users to group1.
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user2->id));

        // Call the WebService.
        $result = core_message_external::get_conversations($user1->id, 0, 20, null, false);
        $result = external_api::clean_returnvalue(core_message_external::get_conversations_returns(), $result);
        $conversations = $result['conversations'];

        // Format original data.
        $coursecontext = \context_course::instance($course1->id);
        $coursename = external_format_string($coursename, $coursecontext->id);
        $groupname = external_format_string($groupname, $coursecontext->id);

        $this->assertStringContainsString('<span class="filter_mathjaxloader_equation">', $conversations[0]['name']);
        $this->assertStringContainsString('<span class="filter_mathjaxloader_equation">', $conversations[0]['subname']);
        $this->assertEquals($groupname, $conversations[0]['name']);
        $this->assertEquals($coursename, $conversations[0]['subname']);
    }

    /**
     * Test verifying get_conversations when there are users in a group and/or individual conversation. The reason this
     * test is performed is because we do not need as much data for group conversations (saving DB calls), so we want
     * to confirm this happens.
     */
    public function test_get_conversations_user_in_group_and_individual_chat() {
        $this->resetAfterTest();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        $conversation = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [
                $user1->id,
                $user2->id
            ],
            'Individual conversation'
        );

        testhelper::send_fake_message_to_conversation($user1, $conversation->id);

        $conversation = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [
                $user1->id,
                $user2->id,
            ],
            'Group conversation'
        );

        testhelper::send_fake_message_to_conversation($user1, $conversation->id);

        \core_message\api::create_contact_request($user1->id, $user2->id);
        \core_message\api::create_contact_request($user1->id, $user3->id);

        $this->setUser($user2);
        $result = core_message_external::get_conversations($user2->id);
        $result = external_api::clean_returnvalue(core_message_external::get_conversations_returns(), $result);
        $conversations = $result['conversations'];

        $groupconversation = array_shift($conversations);
        $individualconversation = array_shift($conversations);

        $this->assertEquals('Group conversation', $groupconversation['name']);
        $this->assertEquals('Individual conversation', $individualconversation['name']);

        $this->assertCount(1, $groupconversation['members']);
        $this->assertCount(1, $individualconversation['members']);

        $groupmember = reset($groupconversation['members']);
        $this->assertNull($groupmember['requirescontact']);
        $this->assertNull($groupmember['canmessage']);
        $this->assertEmpty($groupmember['contactrequests']);

        $individualmember = reset($individualconversation['members']);
        $this->assertNotNull($individualmember['requirescontact']);
        $this->assertNotNull($individualmember['canmessage']);
        $this->assertNotEmpty($individualmember['contactrequests']);
    }

    /**
     * Test verifying get_conversations identifies if a conversation is muted or not.
     */
    public function test_get_conversations_some_muted() {
        $this->resetAfterTest();

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        $conversation1 = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [$user1->id, $user2->id]);
        testhelper::send_fake_message_to_conversation($user1, $conversation1->id, 'Message 1');
        testhelper::send_fake_message_to_conversation($user2, $conversation1->id, 'Message 2');
        \core_message\api::mute_conversation($user1->id, $conversation1->id);

        $conversation2 = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [$user1->id, $user3->id]);
        testhelper::send_fake_message_to_conversation($user1, $conversation2->id, 'Message 1');
        testhelper::send_fake_message_to_conversation($user2, $conversation2->id, 'Message 2');

        $conversation3 = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$user1->id, $user2->id]);
        \core_message\api::mute_conversation($user1->id, $conversation3->id);

        $conversation4 = \core_message\api::create_conversation(\core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$user1->id, $user3->id]);

        $this->setUser($user1);
        $result = core_message_external::get_conversations($user1->id);
        $result = external_api::clean_returnvalue(core_message_external::get_conversations_returns(), $result);
        $conversations = $result['conversations'];

        usort($conversations, function($first, $second){
            return $first['id'] > $second['id'];
        });

        $selfconversation = array_shift($conversations);
        $conv1 = array_shift($conversations);
        $conv2 = array_shift($conversations);
        $conv3 = array_shift($conversations);
        $conv4 = array_shift($conversations);

        $this->assertTrue($conv1['ismuted']);
        $this->assertFalse($conv2['ismuted']);
        $this->assertTrue($conv3['ismuted']);
        $this->assertFalse($conv4['ismuted']);
    }

    /**
     * Test returning members in a conversation with no contact requests.
     */
    public function test_get_conversation_members_messaging_disabled() {
        global $CFG;

        $this->resetAfterTest();

        $CFG->messaging = 0;

        $this->expectException('moodle_exception');
        core_message_external::get_conversation_members(1, 2);
    }

    /**
     * Test returning members in a conversation with no contact requests.
     */
    public function test_get_conversation_members_wrong_user() {
        $this->resetAfterTest();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $this->setUser($user2);

        $this->expectException('moodle_exception');
        core_message_external::get_conversation_members($user1->id, 2);
    }

    /**
     * Test returning members in a conversation with no contact requests.
     */
    public function test_get_conversation_members() {
        $this->resetAfterTest();

        $lastaccess = new stdClass();
        $lastaccess->lastaccess = time();

        $user1 = self::getDataGenerator()->create_user($lastaccess);
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        // This user will not be in the conversation, but a contact request will exist for them.
        $user4 = self::getDataGenerator()->create_user();

        // Add some contact requests.
        \core_message\api::create_contact_request($user1->id, $user3->id);
        \core_message\api::create_contact_request($user1->id, $user4->id);
        \core_message\api::create_contact_request($user2->id, $user3->id);

        // User 1 and 2 are already contacts.
        \core_message\api::add_contact($user1->id, $user2->id);

        // User 1 has blocked user 3.
        \core_message\api::block_user($user1->id, $user3->id);

        $conversation = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [
                $user1->id,
                $user2->id,
                $user3->id
            ]
        );
        $conversationid = $conversation->id;

        $this->setAdminUser();

        $members = core_message_external::get_conversation_members($user1->id, $conversationid, false);
        external_api::clean_returnvalue(core_message_external::get_conversation_members_returns(), $members);

        // Sort them by id.
        ksort($members);
        $this->assertCount(3, $members);
        $member1 = array_shift($members);
        $member2 = array_shift($members);
        $member3 = array_shift($members);

        // Confirm the standard fields are OK.
        $this->assertEquals($user1->id, $member1->id);
        $this->assertEquals(fullname($user1), $member1->fullname);
        $this->assertEquals(true, $member1->isonline);
        $this->assertEquals(true, $member1->showonlinestatus);
        $this->assertEquals(false, $member1->iscontact);
        $this->assertEquals(false, $member1->isblocked);
        $this->assertObjectHasAttribute('contactrequests', $member1);
        $this->assertEmpty($member1->contactrequests);

        $this->assertEquals($user2->id, $member2->id);
        $this->assertEquals(fullname($user2), $member2->fullname);
        $this->assertEquals(false, $member2->isonline);
        $this->assertEquals(true, $member2->showonlinestatus);
        $this->assertEquals(true, $member2->iscontact);
        $this->assertEquals(false, $member2->isblocked);
        $this->assertObjectHasAttribute('contactrequests', $member2);
        $this->assertEmpty($member2->contactrequests);

        $this->assertEquals($user3->id, $member3->id);
        $this->assertEquals(fullname($user3), $member3->fullname);
        $this->assertEquals(false, $member3->isonline);
        $this->assertEquals(true, $member3->showonlinestatus);
        $this->assertEquals(false, $member3->iscontact);
        $this->assertEquals(true, $member3->isblocked);
        $this->assertObjectHasAttribute('contactrequests', $member3);
        $this->assertEmpty($member3->contactrequests);
    }

    /**
     * Test returning members in a conversation with contact requests.
     */
    public function test_get_conversation_members_with_contact_requests() {
        $this->resetAfterTest();

        $lastaccess = new stdClass();
        $lastaccess->lastaccess = time();

        $user1 = self::getDataGenerator()->create_user($lastaccess);
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        // This user will not be in the conversation, but a contact request will exist for them.
        $user4 = self::getDataGenerator()->create_user();

        // Add some contact requests.
        \core_message\api::create_contact_request($user1->id, $user2->id);
        \core_message\api::create_contact_request($user1->id, $user3->id);
        \core_message\api::create_contact_request($user1->id, $user4->id);
        \core_message\api::create_contact_request($user2->id, $user3->id);

        // User 1 and 2 are already contacts.
        \core_message\api::add_contact($user1->id, $user2->id);
        // User 1 has blocked user 3.
        \core_message\api::block_user($user1->id, $user3->id);

        $conversation = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [
                $user1->id,
                $user2->id,
                $user3->id
            ]
        );
        $conversationid = $conversation->id;

        $this->setAdminUser();

        $members = core_message_external::get_conversation_members($user1->id, $conversationid, true);
        external_api::clean_returnvalue(core_message_external::get_conversation_members_returns(), $members);

        // Sort them by id.
        ksort($members);
        $this->assertCount(3, $members);
        $member1 = array_shift($members);
        $member2 = array_shift($members);
        $member3 = array_shift($members);

        // Confirm the standard fields are OK.
        $this->assertEquals($user1->id, $member1->id);
        $this->assertEquals(fullname($user1), $member1->fullname);
        $this->assertEquals(true, $member1->isonline);
        $this->assertEquals(true, $member1->showonlinestatus);
        $this->assertEquals(false, $member1->iscontact);
        $this->assertEquals(false, $member1->isblocked);
        $this->assertCount(2, $member1->contactrequests);

        $this->assertEquals($user2->id, $member2->id);
        $this->assertEquals(fullname($user2), $member2->fullname);
        $this->assertEquals(false, $member2->isonline);
        $this->assertEquals(true, $member2->showonlinestatus);
        $this->assertEquals(true, $member2->iscontact);
        $this->assertEquals(false, $member2->isblocked);
        $this->assertCount(1, $member2->contactrequests);

        $this->assertEquals($user3->id, $member3->id);
        $this->assertEquals(fullname($user3), $member3->fullname);
        $this->assertEquals(false, $member3->isonline);
        $this->assertEquals(true, $member3->showonlinestatus);
        $this->assertEquals(false, $member3->iscontact);
        $this->assertEquals(true, $member3->isblocked);
        $this->assertCount(1, $member3->contactrequests);

        // Confirm the contact requests are OK.
        $request1 = array_shift($member1->contactrequests);
        $request2 = array_shift($member1->contactrequests);

        $this->assertEquals($user1->id, $request1->userid);
        $this->assertEquals($user2->id, $request1->requesteduserid);

        $this->assertEquals($user1->id, $request2->userid);
        $this->assertEquals($user3->id, $request2->requesteduserid);

        $request1 = array_shift($member2->contactrequests);

        $this->assertEquals($user1->id, $request1->userid);
        $this->assertEquals($user2->id, $request1->requesteduserid);

        $request1 = array_shift($member3->contactrequests);

        $this->assertEquals($user1->id, $request1->userid);
        $this->assertEquals($user3->id, $request1->requesteduserid);
    }

    /**
     * Test returning members in a conversation when you are not a member.
     */
    public function test_get_conversation_members_not_a_member() {
        $this->resetAfterTest();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // This user will not be in the conversation.
        $user3 = self::getDataGenerator()->create_user();

        $conversation = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [
                $user1->id,
                $user2->id,
            ]
        );
        $conversationid = $conversation->id;

        $this->setUser($user3);

        $this->expectException('moodle_exception');
        core_message_external::get_conversation_members($user3->id, $conversationid);
    }

    /**
     * Test verifying multiple messages can be sent to an individual conversation.
     */
    public function test_send_messages_to_conversation_individual() {
        $this->resetAfterTest(true);

        // Get a bunch of conversations, some group, some individual and in different states.
        list($user1, $user2, $user3, $user4, $ic1, $ic2, $ic3,
            $gc1, $gc2, $gc3, $gc4, $gc5, $gc6) = $this->create_conversation_test_data();

        // Enrol the users in the same course, so the default privacy controls (course + contacts) can be used.
        $course1 = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user4->id, $course1->id);

        // The user making the request.
        $this->setUser($user1);

        // Try to send a message as user1 to a conversation user1 is a a part of.
        $messages = [
            [
                'text' => 'a message from user 1',
                'textformat' => FORMAT_MOODLE
            ],
            [
                'text' => 'another message from user 1',
                'textformat' => FORMAT_MOODLE
            ],
        ];
        // Redirect messages.
        // This marks messages as read, but we can still observe and verify the number of conversation recipients,
        // based on the message_viewed events generated as part of marking the message as read for each user.
        $this->preventResetByRollback();
        $sink = $this->redirectMessages();
        $writtenmessages = core_message_external::send_messages_to_conversation($ic1->id, $messages);

        external_api::clean_returnvalue(core_message_external::send_messages_to_conversation_returns(), $writtenmessages);

        $this->assertCount(2, $writtenmessages);
        $this->assertObjectHasAttribute('id', $writtenmessages[0]);
        $this->assertEquals($user1->id, $writtenmessages[0]->useridfrom);
        $this->assertEquals('<p>a message from user 1</p>', $writtenmessages[0]->text);
        $this->assertNotEmpty($writtenmessages[0]->timecreated);

        $this->assertObjectHasAttribute('id', $writtenmessages[1]);
        $this->assertEquals($user1->id, $writtenmessages[1]->useridfrom);
        $this->assertEquals('<p>another message from user 1</p>', $writtenmessages[1]->text);
        $this->assertNotEmpty($writtenmessages[1]->timecreated);
    }

    /**
     * Test verifying multiple messages can be sent to an group conversation.
     */
    public function test_send_messages_to_conversation_group() {
        $this->resetAfterTest(true);

        // Get a bunch of conversations, some group, some individual and in different states.
        list($user1, $user2, $user3, $user4, $ic1, $ic2, $ic3,
            $gc1, $gc2, $gc3, $gc4, $gc5, $gc6) = $this->create_conversation_test_data();

        // Enrol the users in the same course, so the default privacy controls (course + contacts) can be used.
        $course1 = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user4->id, $course1->id);

        // The user making the request.
        $this->setUser($user1);

        // Try to send a message as user1 to a conversation user1 is a a part of.
        $messages = [
            [
                'text' => 'a message from user 1 to group conv',
                'textformat' => FORMAT_MOODLE
            ],
            [
                'text' => 'another message from user 1 to group conv',
                'textformat' => FORMAT_MOODLE
            ],
        ];
        // Redirect messages.
        // This marks messages as read, but we can still observe and verify the number of conversation recipients,
        // based on the message_viewed events generated as part of marking the message as read for each user.
        $this->preventResetByRollback();
        $sink = $this->redirectMessages();
        $writtenmessages = core_message_external::send_messages_to_conversation($gc2->id, $messages);

        external_api::clean_returnvalue(core_message_external::send_messages_to_conversation_returns(), $writtenmessages);

        $this->assertCount(2, $writtenmessages);
        $this->assertObjectHasAttribute('id', $writtenmessages[0]);
        $this->assertEquals($user1->id, $writtenmessages[0]->useridfrom);
        $this->assertEquals('<p>a message from user 1 to group conv</p>', $writtenmessages[0]->text);
        $this->assertNotEmpty($writtenmessages[0]->timecreated);

        $this->assertObjectHasAttribute('id', $writtenmessages[1]);
        $this->assertEquals($user1->id, $writtenmessages[1]->useridfrom);
        $this->assertEquals('<p>another message from user 1 to group conv</p>', $writtenmessages[1]->text);
        $this->assertNotEmpty($writtenmessages[1]->timecreated);
    }

    /**
     * Test verifying multiple messages can not be sent to a non existent conversation.
     */
    public function test_send_messages_to_conversation_non_existent_conversation() {
        $this->resetAfterTest(true);

        // Get a bunch of conversations, some group, some individual and in different states.
        list($user1, $user2, $user3, $user4, $ic1, $ic2, $ic3,
            $gc1, $gc2, $gc3, $gc4, $gc5, $gc6) = $this->create_conversation_test_data();

        // The user making the request.
        $this->setUser($user1);

        // Try to send a message as user1 to a conversation user1 is a a part of.
        $messages = [
            [
                'text' => 'a message from user 1',
                'textformat' => FORMAT_MOODLE
            ],
            [
                'text' => 'another message from user 1',
                'textformat' => FORMAT_MOODLE
            ],
        ];
        $this->expectException(\moodle_exception::class);
        $writtenmessages = core_message_external::send_messages_to_conversation(0, $messages);
    }

    /**
     * Test verifying multiple messages can not be sent to a conversation by a non-member.
     */
    public function test_send_messages_to_conversation_non_member() {
        $this->resetAfterTest(true);

        // Get a bunch of conversations, some group, some individual and in different states.
        list($user1, $user2, $user3, $user4, $ic1, $ic2, $ic3,
            $gc1, $gc2, $gc3, $gc4, $gc5, $gc6) = $this->create_conversation_test_data();

        // Enrol the users in the same course, so the default privacy controls (course + contacts) can be used.
        $course1 = $this->getDataGenerator()->create_course();
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user4->id, $course1->id);

        // The user making the request. This user is not a member of group conversation 1 (gc1).
        $this->setUser($user1);

        // Try to send a message as user1 to a conversation user1 is a a part of.
        $messages = [
            [
                'text' => 'a message from user 1 to group conv',
                'textformat' => FORMAT_MOODLE
            ],
            [
                'text' => 'another message from user 1 to group conv',
                'textformat' => FORMAT_MOODLE
            ],
        ];
        $this->expectException(\moodle_exception::class);
        $writtenmessages = core_message_external::send_messages_to_conversation($gc1->id, $messages);
    }

    /**
     * Test getting a conversation that doesn't exist.
     */
    public function test_get_conversation_no_conversation() {
        $this->resetAfterTest();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $name = 'lol conversation';
        $conversation = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [
                $user1->id,
                $user2->id,
            ],
            $name
        );
        $conversationid = $conversation->id;

        $this->setUser($user1);

        $this->expectException('moodle_exception');
        $conv = core_message_external::get_conversation($user1->id, $conversationid + 1);
        external_api::clean_returnvalue(core_message_external::get_conversation_returns(), $conv);
    }

    /**
     * Test verifying that the correct favourite information is returned for a non-linked converastion at user context.
     */
    public function test_get_conversation_favourited() {
        $this->resetAfterTest();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // Create a conversation between the 2 users.
        $conversation = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [
                $user1->id,
                $user2->id,
            ],
            'An individual conversation'
        );

        // Favourite the conversation as user 1 only.
        \core_message\api::set_favourite_conversation($conversation->id, $user1->id);

        // Get the conversation for user1 and confirm it's favourited.
        $this->setUser($user1);
        $conv = core_message_external::get_conversation($user1->id, $conversation->id);
        $conv = external_api::clean_returnvalue(core_message_external::get_conversation_returns(), $conv);
        $this->assertTrue($conv['isfavourite']);

        // Get the conversation for user2 and confirm it's NOT favourited.
        $this->setUser($user2);
        $conv = core_message_external::get_conversation($user2->id, $conversation->id);
        $conv = external_api::clean_returnvalue(core_message_external::get_conversation_returns(), $conv);
        $this->assertFalse($conv['isfavourite']);
    }

    /**
     * Test verifying that the correct favourite information is returned for a group-linked conversation at course context.
     */
    public function test_get_conversation_favourited_group_linked() {
        $this->resetAfterTest();
        global $DB;

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        $course1 = $this->getDataGenerator()->create_course();
        $course1context = \context_course::instance($course1->id);

        // Create a group with a linked conversation and a valid image.
        $this->setAdminUser();
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id);
        $group1 = $this->getDataGenerator()->create_group([
            'courseid' => $course1->id,
            'enablemessaging' => 1
        ]);

        // Add users to group1.
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1->id, 'userid' => $user2->id));

        // Verify that the conversation is a group linked conversation in the course context.
        $conversationrecord = $DB->get_record('message_conversations', ['component' => 'core_group', 'itemtype' => 'groups']);
        $this->assertEquals($course1context->id, $conversationrecord->contextid);

        // Favourite the conversation as user 1 only.
        \core_message\api::set_favourite_conversation($conversationrecord->id, $user1->id);

        // Get the conversation for user1 and confirm it's favourited.
        $this->setUser($user1);
        $conv = core_message_external::get_conversation($user1->id, $conversationrecord->id);
        $conv = external_api::clean_returnvalue(core_message_external::get_conversation_returns(), $conv);
        $this->assertTrue($conv['isfavourite']);

        // Get the conversation for user2 and confirm it's NOT favourited.
        $this->setUser($user2);
        $conv = core_message_external::get_conversation($user2->id, $conversationrecord->id);
        $conv = external_api::clean_returnvalue(core_message_external::get_conversation_returns(), $conv);
        $this->assertFalse($conv['isfavourite']);
    }

    /**
     * Test getting a conversation with no messages.
     */
    public function test_get_conversation_no_messages() {
        $this->resetAfterTest();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        $name = 'lol conversation';
        $conversation = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [
                $user1->id,
                $user2->id,
            ],
            $name
        );
        $conversationid = $conversation->id;

        $this->setUser($user1);

        $conv = core_message_external::get_conversation($user1->id, $conversationid);
        external_api::clean_returnvalue(core_message_external::get_conversation_returns(), $conv);

        $conv = (array) $conv;
        $this->assertEquals($conversationid, $conv['id']);
        $this->assertEquals($name, $conv['name']);
        $this->assertArrayHasKey('subname', $conv);
        $this->assertEquals(\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL, $conv['type']);
        $this->assertEquals(2, $conv['membercount']);
        $this->assertEquals(false, $conv['isfavourite']);
        $this->assertEquals(true, $conv['isread']);
        $this->assertEquals(0, $conv['unreadcount']);
        $this->assertCount(1, $conv['members']);
        foreach ($conv['members'] as $member) {
            $member = (array) $member;
            $this->assertArrayHasKey('id', $member);
            $this->assertArrayHasKey('fullname', $member);
            $this->assertArrayHasKey('profileimageurl', $member);
            $this->assertArrayHasKey('profileimageurlsmall', $member);
            $this->assertArrayHasKey('isonline', $member);
            $this->assertArrayHasKey('showonlinestatus', $member);
            $this->assertArrayHasKey('isblocked', $member);
            $this->assertArrayHasKey('iscontact', $member);
        }
        $this->assertEmpty($conv['messages']);
    }

    /**
     * Test getting a conversation with messages.
     */
    public function test_get_conversation_with_messages() {
        $this->resetAfterTest();

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        // Some random conversation.
        $otherconversation = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [
                $user1->id,
                $user3->id,
            ]
        );

        $conversation = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [
                $user1->id,
                $user2->id,
            ]
        );
        $conversationid = $conversation->id;

        $time = time();
        $message1id = testhelper::send_fake_message_to_conversation($user1, $conversation->id, 'A', $time - 10);
        $message2id = testhelper::send_fake_message_to_conversation($user2, $conversation->id, 'B', $time - 5);
        $message3id = testhelper::send_fake_message_to_conversation($user1, $conversation->id, 'C', $time);

        // Add some messages to the other convo to make sure they aren't included.
        testhelper::send_fake_message_to_conversation($user1, $otherconversation->id, 'foo');

        $this->setUser($user1);

        // Test newest first.
        $conv = core_message_external::get_conversation(
            $user1->id,
            $conversationid,
            false,
            false,
            0,
            0,
            0,
            0,
            true
        );
        external_api::clean_returnvalue(core_message_external::get_conversation_returns(), $conv);

        $conv = (array) $conv;
        $this->assertEquals(false, $conv['isread']);
        $this->assertEquals(1, $conv['unreadcount']);
        $this->assertCount(3, $conv['messages']);
        $this->assertEquals($message3id, $conv['messages'][0]->id);
        $this->assertEquals($user1->id, $conv['messages'][0]->useridfrom);
        $this->assertEquals($message2id, $conv['messages'][1]->id);
        $this->assertEquals($user2->id, $conv['messages'][1]->useridfrom);
        $this->assertEquals($message1id, $conv['messages'][2]->id);
        $this->assertEquals($user1->id, $conv['messages'][2]->useridfrom);

        // Test newest last.
        $conv = core_message_external::get_conversation(
            $user1->id,
            $conversationid,
            false,
            false,
            0,
            0,
            0,
            0,
            false
        );
        external_api::clean_returnvalue(core_message_external::get_conversation_returns(), $conv);

        $conv = (array) $conv;
        $this->assertCount(3, $conv['messages']);
        $this->assertEquals($message3id, $conv['messages'][2]->id);
        $this->assertEquals($user1->id, $conv['messages'][2]->useridfrom);
        $this->assertEquals($message2id, $conv['messages'][1]->id);
        $this->assertEquals($user2->id, $conv['messages'][1]->useridfrom);
        $this->assertEquals($message1id, $conv['messages'][0]->id);
        $this->assertEquals($user1->id, $conv['messages'][0]->useridfrom);

        // Test message offest and limit.
        $conv = core_message_external::get_conversation(
            $user1->id,
            $conversationid,
            false,
            false,
            0,
            0,
            1,
            1,
            true
        );
        external_api::clean_returnvalue(core_message_external::get_conversation_returns(), $conv);

        $conv = (array) $conv;
        $this->assertCount(1, $conv['messages']);
        $this->assertEquals($message2id, $conv['messages'][0]->id);
        $this->assertEquals($user2->id, $conv['messages'][0]->useridfrom);
    }

    /**
     * Data provider for test_get_conversation_counts().
     */
    public function test_get_conversation_counts_test_cases() {
        $typeindividual = \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL;
        $typegroup = \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP;
        $typeself = \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF;
        list($user1, $user2, $user3, $user4, $user5, $user6, $user7, $user8) = [0, 1, 2, 3, 4, 5, 6, 7];
        $conversations = [
            [
                'type' => $typeindividual,
                'users' => [$user1, $user2],
                'messages' => [$user1, $user2, $user2],
                'favourites' => [$user1],
                'enabled' => null // Individual conversations cannot be disabled.
            ],
            [
                'type' => $typeindividual,
                'users' => [$user1, $user3],
                'messages' => [$user1, $user3, $user1],
                'favourites' => [],
                'enabled' => null // Individual conversations cannot be disabled.
            ],
            [
                'type' => $typegroup,
                'users' => [$user1, $user2, $user3, $user4],
                'messages' => [$user1, $user2, $user3, $user4],
                'favourites' => [],
                'enabled' => true
            ],
            [
                'type' => $typegroup,
                'users' => [$user2, $user3, $user4],
                'messages' => [$user2, $user3, $user4],
                'favourites' => [],
                'enabled' => true
            ],
            [
                'type' => $typegroup,
                'users' => [$user6, $user7],
                'messages' => [$user6, $user7, $user7],
                'favourites' => [$user6],
                'enabled' => false
            ],
            [
                'type' => $typeself,
                'users' => [$user8],
                'messages' => [$user8],
                'favourites' => [],
                'enabled' => null // Individual conversations cannot be disabled.
            ],
        ];

        return [
            'No conversations' => [
                'conversationConfigs' => $conversations,
                'deletemessagesuser' => null,
                'deletemessages' => [],
                'arguments' => [$user5],
                'expectedcounts' => ['favourites' => 1, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 0,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 0,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'expectedunreadcounts' => ['favourites' => 0, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 0,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 0,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'deletedusers' => []
            ],
            'No individual conversations, 2 group conversations' => [
                'conversationConfigs' => $conversations,
                'deletemessagesuser' => null,
                'deletemessages' => [],
                'arguments' => [$user4],
                'expectedcounts' => ['favourites' => 1, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 0,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 2,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'expectedunreadcounts' => ['favourites' => 0, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 0,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 2,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'deletedusers' => []
            ],
            '2 individual conversations (one favourited), 1 group conversation' => [
                'conversationConfigs' => $conversations,
                'deletemessagesuser' => null,
                'deletemessages' => [],
                'arguments' => [$user1],
                'expectedcounts' => ['favourites' => 2, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'expectedunreadcounts' => ['favourites' => 1, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'deletedusers' => []
            ],
            '1 individual conversation, 2 group conversations' => [
                'conversationConfigs' => $conversations,
                'deletemessagesuser' => null,
                'deletemessages' => [],
                'arguments' => [$user2],
                'expectedcounts' => ['favourites' => 1, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 2,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'expectedunreadcounts' => ['favourites' => 0, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 2,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'deletedusers' => []
            ],
            '2 group conversations only' => [
                'conversationConfigs' => $conversations,
                'deletemessagesuser' => null,
                'deletemessages' => [],
                'arguments' => [$user4],
                'expectedcounts' => ['favourites' => 1, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 0,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 2,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'expectedunreadcounts' => ['favourites' => 0, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 0,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 2,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'deletedusers' => []
            ],
            'All conversation types, delete a message from individual favourited, messages remaining' => [
                'conversationConfigs' => $conversations,
                'deletemessagesuser' => $user1,
                'deletemessages' => [0],
                'arguments' => [$user1],
                'expectedcounts' => ['favourites' => 2, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'expectedunreadcounts' => ['favourites' => 1, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'deletedusers' => []
            ],
            'All conversation types, delete a message from individual non-favourited, messages remaining' => [
                'conversationConfigs' => $conversations,
                'deletemessagesuser' => $user1,
                'deletemessages' => [3],
                'arguments' => [$user1],
                'expectedcounts' => ['favourites' => 2, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'expectedunreadcounts' => ['favourites' => 1, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'deletedusers' => []
            ],
            'All conversation types, delete all messages from individual favourited, no messages remaining' => [
                'conversationConfigs' => $conversations,
                'deletemessagesuser' => $user1,
                'deletemessages' => [0, 1, 2],
                'arguments' => [$user1],
                'expectedcounts' => ['favourites' => 1, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'expectedunreadcounts' => ['favourites' => 0, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'deletedusers' => []
            ],
            'All conversation types, delete all messages from individual non-favourited, no messages remaining' => [
                'conversationConfigs' => $conversations,
                'deletemessagesuser' => $user1,
                'deletemessages' => [3, 4, 5],
                'arguments' => [$user1],
                'expectedcounts' => ['favourites' => 2, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 0,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'expectedunreadcounts' => ['favourites' => 1, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 0,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'deletedusers' => []
            ],
            'All conversation types, delete all messages from individual favourited, no messages remaining, different user' => [
                'conversationConfigs' => $conversations,
                'deletemessagesuser' => $user1,
                'deletemessages' => [0, 1, 2],
                'arguments' => [$user2],
                'expectedcounts' => ['favourites' => 1, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 2,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'expectedunreadcounts' => ['favourites' => 0, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 2,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'deletedusers' => []
            ],
            'All conversation types, delete all messages from individual non-favourited, no messages remaining, different user' => [
                'conversationConfigs' => $conversations,
                'deletemessagesuser' => $user1,
                'deletemessages' => [3, 4, 5],
                'arguments' => [$user3],
                'expectedcounts' => ['favourites' => 1, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 2,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'expectedunreadcounts' => ['favourites' => 0, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 2,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'deletedusers' => []
            ],
            'All conversation types, delete some messages from group non-favourited, messages remaining,' => [
                'conversationConfigs' => $conversations,
                'deletemessagesuser' => $user1,
                'deletemessages' => [6, 7],
                'arguments' => [$user1],
                'expectedcounts' => ['favourites' => 2, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'expectedunreadcounts' => ['favourites' => 1, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'deletedusers' => []
            ],
            'All conversation types, delete all messages from group non-favourited, no messages remaining,' => [
                'conversationConfigs' => $conversations,
                'deletemessagesuser' => $user1,
                'deletemessages' => [6, 7, 8, 9],
                'arguments' => [$user1],
                'expectedcounts' => ['favourites' => 2, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'expectedunreadcounts' => ['favourites' => 1, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 0,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'deletedusers' => []
            ],
            'All conversation types, another user soft deleted' => [
                'conversationConfigs' => $conversations,
                'deletemessagesuser' => null,
                'deletemessages' => [],
                'arguments' => [$user1],
                'expectedcounts' => ['favourites' => 2, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'expectedunreadcounts' => ['favourites' => 1, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'deletedusers' => [$user2]
            ],
            'All conversation types, all group users soft deleted' => [
                'conversationConfigs' => $conversations,
                'deletemessagesuser' => null,
                'deletemessages' => [],
                'arguments' => [$user1],
                'expectedcounts' => ['favourites' => 2, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'expectedunreadcounts' => ['favourites' => 1, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 1,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'deletedusers' => [$user2, $user3, $user4]
            ],
            'Group conversation which is disabled, favourited' => [
                'conversationConfigs' => $conversations,
                'deletemessagesuser' => null,
                'deletemessages' => [],
                'arguments' => [$user6],
                'expectedcounts' => ['favourites' => 1, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 0,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 0,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'expectedunreadcounts' => ['favourites' => 0, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 0,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 0,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'deletedusers' => []
            ],
            'Group conversation which is disabled, non-favourited' => [
                'conversationConfigs' => $conversations,
                'deletemessagesuser' => null,
                'deletemessages' => [],
                'arguments' => [$user7],
                'expectedcounts' => ['favourites' => 1, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 0,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 0,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'expectedunreadcounts' => ['favourites' => 0, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 0,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 0,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'deletedusers' => []
            ],
            'Conversation with self' => [
                'conversationConfigs' => $conversations,
                'deletemessagesuser' => null,
                'deletemessages' => [],
                'arguments' => [$user8],
                'expectedcounts' => ['favourites' => 0, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 0,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 0,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 1
                ]],
                'expectedunreadcounts' => ['favourites' => 0, 'types' => [
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL => 0,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP => 0,
                    \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF => 0
                ]],
                'deletedusers' => []
            ],
        ];
    }

    /**
     * Test the get_conversation_counts() function.
     *
     * @dataProvider test_get_conversation_counts_test_cases()
     * @param array $conversationconfigs Conversations to create
     * @param int $deletemessagesuser The user who is deleting the messages
     * @param array $deletemessages The list of messages to delete (by index)
     * @param array $arguments Arguments for the count conversations function
     * @param array $expectedcounts the expected conversation counts
     * @param array $expectedunreadcounts the expected unread conversation counts
     * @param array $deletedusers the array of users to soft delete.
     */
    public function test_get_conversation_counts(
        $conversationconfigs,
        $deletemessagesuser,
        $deletemessages,
        $arguments,
        $expectedcounts,
        $expectedunreadcounts,
        $deletedusers
    ) {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $users = [
            $generator->create_user(),
            $generator->create_user(),
            $generator->create_user(),
            $generator->create_user(),
            $generator->create_user(),
            $generator->create_user(),
            $generator->create_user(),
            $generator->create_user()
        ];

        $deleteuser = !is_null($deletemessagesuser) ? $users[$deletemessagesuser] : null;
        $this->setUser($users[$arguments[0]]);
        $arguments[0] = $users[$arguments[0]]->id;
        $systemcontext = \context_system::instance();
        $conversations = [];
        $messageids = [];

        foreach ($conversationconfigs as $config) {
            $conversation = \core_message\api::create_conversation(
                $config['type'],
                array_map(function($userindex) use ($users) {
                    return $users[$userindex]->id;
                }, $config['users']),
                null,
                ($config['enabled'] ?? true)
            );

            foreach ($config['messages'] as $userfromindex) {
                $userfrom = $users[$userfromindex];
                $messageids[] = testhelper::send_fake_message_to_conversation($userfrom, $conversation->id);
            }

            // Remove the self conversations created by the generator,
            // so we can choose to set that ourself and honour the original intention of the test.
            $userids = array_map(function($userindex) use ($users) {
                return $users[$userindex]->id;
            }, $config['users']);
            foreach ($userids as $userid) {
                if ($conversation->type == \core_message\api::MESSAGE_CONVERSATION_TYPE_SELF) {
                    \core_message\api::unset_favourite_conversation($conversation->id, $userid);
                }
            }

            foreach ($config['favourites'] as $userfromindex) {
                $userfrom = $users[$userfromindex];
                $usercontext = \context_user::instance($userfrom->id);
                $ufservice = \core_favourites\service_factory::get_service_for_user_context($usercontext);
                $ufservice->create_favourite('core_message', 'message_conversations', $conversation->id, $systemcontext);
            }

            $conversations[] = $conversation;
        }

        foreach ($deletemessages as $messageindex) {
            \core_message\api::delete_message($deleteuser->id, $messageids[$messageindex]);
        }

        foreach ($deletedusers as $deleteduser) {
            delete_user($users[$deleteduser]);
        }

        $counts = core_message_external::get_conversation_counts(...$arguments);
        $counts = external_api::clean_returnvalue(core_message_external::get_conversation_counts_returns(), $counts);

        $this->assertEquals($expectedcounts['favourites'], $counts['favourites']);
        $this->assertEquals($expectedcounts['types'][\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL],
            $counts['types'][\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL]);
        $this->assertEquals($expectedcounts['types'][\core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP],
            $counts['types'][\core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP]);
        $this->assertEquals($expectedcounts['types'][\core_message\api::MESSAGE_CONVERSATION_TYPE_SELF],
            $counts['types'][\core_message\api::MESSAGE_CONVERSATION_TYPE_SELF]);
    }

    /**
     * Test the get_unread_conversation_counts() function.
     *
     * @dataProvider test_get_conversation_counts_test_cases()
     * @param array $conversationconfigs Conversations to create
     * @param int $deletemessagesuser The user who is deleting the messages
     * @param array $deletemessages The list of messages to delete (by index)
     * @param array $arguments Arguments for the count conversations function
     * @param array $expectedcounts the expected conversation counts
     * @param array $expectedunreadcounts the expected unread conversation counts
     * @param array $deletedusers the list of users to soft-delete.
     */
    public function test_get_unread_conversation_counts(
        $conversationconfigs,
        $deletemessagesuser,
        $deletemessages,
        $arguments,
        $expectedcounts,
        $expectedunreadcounts,
        $deletedusers
    ) {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $users = [
            $generator->create_user(),
            $generator->create_user(),
            $generator->create_user(),
            $generator->create_user(),
            $generator->create_user(),
            $generator->create_user(),
            $generator->create_user(),
            $generator->create_user()
        ];

        $deleteuser = !is_null($deletemessagesuser) ? $users[$deletemessagesuser] : null;
        $this->setUser($users[$arguments[0]]);
        $arguments[0] = $users[$arguments[0]]->id;
        $systemcontext = \context_system::instance();
        $conversations = [];
        $messageids = [];

        foreach ($conversationconfigs as $config) {
            $conversation = \core_message\api::create_conversation(
                $config['type'],
                array_map(function($userindex) use ($users) {
                    return $users[$userindex]->id;
                }, $config['users']),
                null,
                ($config['enabled'] ?? true)
            );

            foreach ($config['messages'] as $userfromindex) {
                $userfrom = $users[$userfromindex];
                $messageids[] = testhelper::send_fake_message_to_conversation($userfrom, $conversation->id);
            }

            foreach ($config['favourites'] as $userfromindex) {
                $userfrom = $users[$userfromindex];
                $usercontext = \context_user::instance($userfrom->id);
                $ufservice = \core_favourites\service_factory::get_service_for_user_context($usercontext);
                $ufservice->create_favourite('core_message', 'message_conversations', $conversation->id, $systemcontext);
            }

            $conversations[] = $conversation;
        }

        foreach ($deletemessages as $messageindex) {
            \core_message\api::delete_message($deleteuser->id, $messageids[$messageindex]);
        }

        foreach ($deletedusers as $deleteduser) {
            delete_user($users[$deleteduser]);
        }

        $counts = core_message_external::get_unread_conversation_counts(...$arguments);
        $counts = external_api::clean_returnvalue(core_message_external::get_unread_conversation_counts_returns(), $counts);

        $this->assertEquals($expectedunreadcounts['favourites'], $counts['favourites']);
        $this->assertEquals($expectedunreadcounts['types'][\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL],
            $counts['types'][\core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL]);
        $this->assertEquals($expectedunreadcounts['types'][\core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP],
            $counts['types'][\core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP]);
        $this->assertEquals($expectedunreadcounts['types'][\core_message\api::MESSAGE_CONVERSATION_TYPE_SELF],
            $counts['types'][\core_message\api::MESSAGE_CONVERSATION_TYPE_SELF]);
    }

    /**
     * Test delete_message for all users.
     */
    public function test_delete_message_for_all_users() {
        global $DB;

        $this->resetAfterTest(true);

        // Create fake data to test it.
        list($user1, $user2, $user3, $convgroup, $convindividual) = $this->create_delete_message_test_data();

        // Send message as user1 to group conversation.
        $messageid1 = testhelper::send_fake_message_to_conversation($user1, $convgroup->id);
        $messageid2 = testhelper::send_fake_message_to_conversation($user2, $convgroup->id);

        // User1 deletes the first message for all users of group conversation.
        // First, we have to allow user1 (Teacher) can delete messages for all users.
        $editingteacher = $DB->get_record('role', ['shortname' => 'editingteacher']);
        assign_capability('moodle/site:deleteanymessage', CAP_ALLOW, $editingteacher->id, context_system::instance());

        $this->setUser($user1);

        // Now, user1 deletes message for all users.
        $return = core_message_external::delete_message_for_all_users($messageid1, $user1->id);
        $return = external_api::clean_returnvalue(core_message_external::delete_message_for_all_users_returns(), $return);
        // Check if everything is ok.
        $this->assertEquals(array(), $return);

        // Check we have 3 records on message_user_actions with the mark MESSAGE_ACTION_DELETED.
        $muas = $DB->get_records('message_user_actions', array('messageid' => $messageid1), 'userid ASC');
        $this->assertCount(3, $muas);
        $mua1 = array_shift($muas);
        $mua2 = array_shift($muas);
        $mua3 = array_shift($muas);

        $this->assertEquals($user1->id, $mua1->userid);
        $this->assertEquals($messageid1, $mua1->messageid);
        $this->assertEquals(\core_message\api::MESSAGE_ACTION_DELETED, $mua1->action);
        $this->assertEquals($user2->id, $mua2->userid);
        $this->assertEquals($messageid1, $mua2->messageid);
        $this->assertEquals(\core_message\api::MESSAGE_ACTION_DELETED, $mua2->action);
        $this->assertEquals($user3->id, $mua3->userid);
        $this->assertEquals($messageid1, $mua3->messageid);
        $this->assertEquals(\core_message\api::MESSAGE_ACTION_DELETED, $mua3->action);
    }

    /**
     * Test delete_message for all users with messaging disabled.
     */
    public function test_delete_message_for_all_users_messaging_disabled() {
        global $CFG;

        $this->resetAfterTest();

        // Create fake data to test it.
        list($user1, $user2, $user3, $convgroup, $convindividual) = $this->create_delete_message_test_data();

        // Send message as user1 to group conversation.
        $messageid = testhelper::send_fake_message_to_conversation($user1, $convgroup->id);

        $this->setUser($user1);

        // Disable messaging.
        $CFG->messaging = 0;

        // Ensure an exception is thrown.
        $this->expectException('moodle_exception');
        core_message_external::delete_message_for_all_users($messageid, $user1->id);
    }

    /**
     * Test delete_message for all users with no permission.
     */
    public function test_delete_message_for_all_users_no_permission() {
        $this->resetAfterTest();

        // Create fake data to test it.
        list($user1, $user2, $user3, $convgroup, $convindividual) = $this->create_delete_message_test_data();

        // Send message as user1 to group conversation.
        $messageid = testhelper::send_fake_message_to_conversation($user1, $convgroup->id);

        $this->setUser($user2);

        // Try as user2 to delete a message for all users without permission to do it.
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage('You do not have permission to delete this message for everyone.');
        core_message_external::delete_message_for_all_users($messageid, $user2->id);
    }

    /**
     * Test delete_message for all users in a private conversation.
     */
    public function test_delete_message_for_all_users_private_conversation() {
        global $DB;

        $this->resetAfterTest();

        // Create fake data to test it.
        list($user1, $user2, $user3, $convgroup, $convindividual) = $this->create_delete_message_test_data();

        // Send message as user1 to private conversation.
        $messageid = testhelper::send_fake_message_to_conversation($user1, $convindividual->id);

        // First, we have to allow user1 (Teacher) can delete messages for all users.
        $editingteacher = $DB->get_record('role', ['shortname' => 'editingteacher']);
        assign_capability('moodle/site:deleteanymessage', CAP_ALLOW, $editingteacher->id, context_system::instance());

        $this->setUser($user1);

        // Try as user1 to delete a private message for all users on individual conversation.
        // User1 should not delete message for all users in a private conversations despite being a teacher.
        // Because is a teacher in a course and not in a system context.
        $this->expectException('moodle_exception');
        $this->expectExceptionMessage('You do not have permission to delete this message for everyone.');
        core_message_external::delete_message_for_all_users($messageid, $user1->id);
    }

    /**
     * Test retrieving conversation messages by providing a timefrom higher than last message timecreated. It should return no
     * messages but keep the return structure to not break when called from the ws.
     */
    public function test_get_conversation_messages_timefrom_higher_than_last_timecreated() {
        $this->resetAfterTest(true);

        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();

        // Create group conversation.
        $conversation = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$user1->id, $user2->id, $user3->id, $user4->id]
        );

        // The person asking for the messages for another user.
        $this->setUser($user1);

        // Send some messages back and forth.
        $time = 1;
        testhelper::send_fake_message_to_conversation($user1, $conversation->id, 'Message 1', $time + 1);
        testhelper::send_fake_message_to_conversation($user2, $conversation->id, 'Message 2', $time + 2);
        testhelper::send_fake_message_to_conversation($user1, $conversation->id, 'Message 3', $time + 3);
        testhelper::send_fake_message_to_conversation($user3, $conversation->id, 'Message 4', $time + 4);

        // Retrieve the messages.
        $result = core_message_external::get_conversation_messages($user1->id, $conversation->id, 0, 0, '', $time + 5);

        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(core_message_external::get_conversation_messages_returns(), $result);

        // Check the results are correct.
        $this->assertEquals($conversation->id, $result['id']);

        // Confirm the message data is correct.
        $messages = $result['messages'];
        $this->assertEquals(0, count($messages));

        // Confirm that members key is present.
        $this->assertArrayHasKey('members', $result);
    }

    /**
     * Helper to seed the database with initial state with data.
     */
    protected function create_delete_message_test_data() {
        // Create some users.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();

        // Create a course and enrol the users.
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user3->id, $course->id, 'student');

        // Create a group and added the users into.
        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        groups_add_member($group1->id, $user1->id);
        groups_add_member($group1->id, $user2->id);
        groups_add_member($group1->id, $user3->id);

        // Create a group conversation linked with the course.
        $convgroup = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_GROUP,
            [$user1->id, $user2->id, $user3->id],
            'Group test delete for everyone', \core_message\api::MESSAGE_CONVERSATION_ENABLED,
            'core_group',
            'groups',
            $group1->id,
            context_course::instance($course->id)->id
        );

        // Create and individual conversation.
        $convindividual = \core_message\api::create_conversation(
            \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
            [$user1->id, $user2->id]
        );

        return [$user1, $user2, $user3, $convgroup, $convindividual];
    }
}
