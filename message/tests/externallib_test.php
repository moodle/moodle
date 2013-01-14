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

class core_message_external_testcase extends externallib_advanced_testcase {

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

        // Login as user1.
        $this->setUser($user1);
        $this->assertEquals(array(), core_message_external::create_contacts(
            array($user_offline1->id, $user_offline2->id, $user_offline3->id, $user_online->id)));

        // User_stranger sends a couple of messages to user1.
        $this->send_message($user_stranger, $user1, 'Hello there!');
        $this->send_message($user_stranger, $user1, 'How you goin?');
        $this->send_message($user_stranger, $user1, 'Cya!');

        // User_blocked sends a message to user1.
        $this->send_message($user_blocked, $user1, 'Here, have some spam.');

        // Retrieve the contacts of the user.
        $this->setUser($user1);
        $contacts = core_message_external::get_contacts();
        $contacts = external_api::clean_returnvalue(core_message_external::get_contacts_returns(), $contacts);
        $this->assertCount(3, $contacts['offline']);
        $this->assertCount(1, $contacts['online']);
        $this->assertCount(2, $contacts['strangers']);
        core_message_external::block_contacts(array($user_blocked->id));
        $contacts = core_message_external::get_contacts();
        $contacts = external_api::clean_returnvalue(core_message_external::get_contacts_returns(), $contacts);
        $this->assertCount(3, $contacts['offline']);
        $this->assertCount(1, $contacts['online']);
        $this->assertCount(1, $contacts['strangers']);

        // Checking some of the fields returned.
        $stranger = array_pop($contacts['strangers']);
        $this->assertEquals($user_stranger->id, $stranger['id']);
        $this->assertEquals(3, $stranger['unread']);
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
}
