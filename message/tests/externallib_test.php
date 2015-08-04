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
     */
    protected function send_message($userfrom, $userto, $message = 'Hello world!') {
        global $DB;
        $record = new stdClass();
        $record->useridfrom = $userfrom->id;
        $record->useridto = $userto->id;
        $record->subject = 'No subject';
        $record->fullmessage = $message;
        $record->timecreated = time();
        $insert = $DB->insert_record('message', $record);
    }

    /**
     * Test send_instant_messages
     */
    public function test_send_instant_messages() {

        global $DB, $USER, $CFG;

        $this->resetAfterTest(true);
        // Transactions used in tests, tell phpunit use alternative reset method.
        $this->preventResetByRollback();

        // Turn off all message processors (so nothing is really sent)
        require_once($CFG->dirroot . '/message/lib.php');
        $messageprocessors = get_message_processors();
        foreach($messageprocessors as $messageprocessor) {
            $messageprocessor->enabled = 0;
            $DB->update_record('message_processors', $messageprocessor);
        }

        // Set the required capabilities by the external function
        $contextid = context_system::instance()->id;
        $roleid = $this->assignUserCapability('moodle/site:sendmessage', $contextid);

        $user1 = self::getDataGenerator()->create_user();

        // Create test message data.
        $message1 = array();
        $message1['touserid'] = $user1->id;
        $message1['text'] = 'the message.';
        $message1['clientmsgid'] = 4;
        $messages = array($message1);

        $sentmessages = core_message_external::send_instant_messages($messages);

        // We need to execute the return values cleaning process to simulate the web service server.
        $sentmessages = external_api::clean_returnvalue(core_message_external::send_instant_messages_returns(), $sentmessages);

        $themessage = $DB->get_record('message', array('id' => $sentmessages[0]['msgid']));

        // Confirm that the message was inserted correctly.
        $this->assertEquals($themessage->useridfrom, $USER->id);
        $this->assertEquals($themessage->useridto, $message1['touserid']);
        $this->assertEquals($themessage->smallmessage, $message1['text']);
        $this->assertEquals($sentmessages[0]['clientmsgid'], $message1['clientmsgid']);
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
        $return = external_api::clean_returnvalue(core_message_external::create_contacts_returns(), $return);
        $this->assertEquals(array(), $return);

        // Adding a contact who is already a contact.
        $return = core_message_external::create_contacts(array($user2->id));
        $return = external_api::clean_returnvalue(core_message_external::create_contacts_returns(), $return);
        $this->assertEquals(array(), $return);

        // Adding multiple contacts.
        $return = core_message_external::create_contacts(array($user3->id, $user4->id));
        $return = external_api::clean_returnvalue(core_message_external::create_contacts_returns(), $return);
        $this->assertEquals(array(), $return);

        // Adding a non-existing user.
        $return = core_message_external::create_contacts(array(99999));
        $return = external_api::clean_returnvalue(core_message_external::create_contacts_returns(), $return);
        $this->assertCount(1, $return);
        $return = array_pop($return);
        $this->assertEquals($return['warningcode'], 'contactnotcreated');
        $this->assertEquals($return['itemid'], 99999);

        // Adding contacts with valid and invalid parameters.
        $return = core_message_external::create_contacts(array($user5->id, 99999));
        $return = external_api::clean_returnvalue(core_message_external::create_contacts_returns(), $return);
        $this->assertCount(1, $return);
        $return = array_pop($return);
        $this->assertEquals($return['warningcode'], 'contactnotcreated');
        $this->assertEquals($return['itemid'], 99999);
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
        $this->assertEquals(array(), core_message_external::create_contacts(
            array($user3->id, $user4->id, $user5->id, $user6->id)));

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
        $this->assertEquals(array(), core_message_external::create_contacts(array($user3->id, $user4->id, $user5->id)));

        // Blocking a contact.
        $return = core_message_external::block_contacts(array($user2->id));
        $return = external_api::clean_returnvalue(core_message_external::block_contacts_returns(), $return);
        $this->assertEquals(array(), $return);

        // Blocking a contact who is already a contact.
        $return = core_message_external::block_contacts(array($user2->id));
        $return = external_api::clean_returnvalue(core_message_external::block_contacts_returns(), $return);
        $this->assertEquals(array(), $return);

        // Blocking multiple contacts.
        $return = core_message_external::block_contacts(array($user3->id, $user4->id));
        $return = external_api::clean_returnvalue(core_message_external::block_contacts_returns(), $return);
        $this->assertEquals(array(), $return);

        // Blocking a non-existing user.
        $return = core_message_external::block_contacts(array(99999));
        $return = external_api::clean_returnvalue(core_message_external::block_contacts_returns(), $return);
        $this->assertCount(1, $return);
        $return = array_pop($return);
        $this->assertEquals($return['warningcode'], 'contactnotblocked');
        $this->assertEquals($return['itemid'], 99999);

        // Blocking contacts with valid and invalid parameters.
        $return = core_message_external::block_contacts(array($user5->id, 99999));
        $return = external_api::clean_returnvalue(core_message_external::block_contacts_returns(), $return);
        $this->assertCount(1, $return);
        $return = array_pop($return);
        $this->assertEquals($return['warningcode'], 'contactnotblocked');
        $this->assertEquals($return['itemid'], 99999);
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
        $this->assertEquals(array(), core_message_external::create_contacts(
            array($user3->id, $user4->id, $user5->id, $user6->id)));

        // Removing a non-contact.
        $return = core_message_external::unblock_contacts(array($user2->id));
        $this->assertNull($return);

        // Removing one contact.
        $return = core_message_external::unblock_contacts(array($user3->id));
        $this->assertNull($return);

        // Removing multiple contacts.
        $return = core_message_external::unblock_contacts(array($user4->id, $user5->id));
        $this->assertNull($return);

        // Removing contact from unexisting user.
        $return = core_message_external::unblock_contacts(array(99999));
        $this->assertNull($return);

        // Removing mixed valid and invalid data.
        $return = core_message_external::unblock_contacts(array($user6->id, 99999));
        $this->assertNull($return);

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
        $this->assertEquals(array(), core_message_external::create_contacts(
            array($user_offline1->id, $user_offline2->id, $user_offline3->id, $user_online->id)));

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
        $this->setExpectedException('moodle_exception');
        $results = core_message_external::search_contacts('');
    }

    /**
     * Test get_messages.
     */
    public function test_get_messages() {
        global $CFG;
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

        // Get unread conversations from user1 to user2.
        $messages = core_message_external::get_messages($user2->id, $user1->id, 'conversations', false, true, 0, 0);
        $messages = external_api::clean_returnvalue(core_message_external::get_messages_returns(), $messages);
        $this->assertCount(0, $messages['messages']);

        // Get read messages send from user1.
        $messages = core_message_external::get_messages(0, $user1->id, 'conversations', true, true, 0, 0);
        $messages = external_api::clean_returnvalue(core_message_external::get_messages_returns(), $messages);
        $this->assertCount(2, $messages['messages']);

        $this->setUser($user2);
        // Get read conversations from any user to user2.
        $messages = core_message_external::get_messages($user2->id, 0, 'conversations', true, true, 0, 0);
        $messages = external_api::clean_returnvalue(core_message_external::get_messages_returns(), $messages);
        $this->assertCount(2, $messages['messages']);

        $this->setUser($user3);
        // Get read notifications received by user3.
        $messages = core_message_external::get_messages($user3->id, 0, 'notifications', true, true, 0, 0);
        $messages = external_api::clean_returnvalue(core_message_external::get_messages_returns(), $messages);
        $this->assertCount(0, $messages['messages']);

        // Now, create some notifications...
        // We are creating fake notifications but based on real ones.

        // This one omits notification = 1.
        $eventdata = new stdClass();
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

        $message = new stdClass();
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
        $eventdata = new stdClass();
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

        $eventdata = new stdClass();
        $eventdata->name             = 'submission';
        $eventdata->component        = 'mod_feedback';
        $eventdata->userfrom         = $user1;
        $eventdata->userto           = $user2;
        $eventdata->subject          = 'Feedback submitted';
        $eventdata->fullmessage      = 'Feedback submitted from an user';
        $eventdata->fullmessageformat = FORMAT_PLAIN;
        $eventdata->fullmessagehtml  = '<strong>Feedback submitted</strong>';
        $eventdata->smallmessage     = '';
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
        $this->assertEquals(array(), core_message_external::create_contacts(
            array($useroffline1->id, $useroffline2->id)));

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
        $this->assertEquals(array(), core_message_external::create_contacts(
            array($user2->id, $user3->id)));

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
            $messageid = core_message_external::mark_message_read($messageids[0]['messageid'] * 2, time());
            $this->fail('Exception expected due invalid messageid.');
        } catch (dml_missing_record_exception $e) {
            $this->assertEquals('invalidrecord', $e->errorcode);
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

}
