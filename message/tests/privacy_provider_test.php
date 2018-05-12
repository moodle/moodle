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
 * Privacy provider tests.
 *
 * @package    core_message
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_privacy\local\metadata\collection;
use core_message\privacy\provider;
use \core_privacy\local\request\writer;
use \core_privacy\local\request\transform;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy provider tests class.
 *
 * @package    core_message
 * @copyright  2018 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_message_privacy_provider_testcase extends \core_privacy\tests\provider_testcase {

    /**
     * Test for provider::get_metadata().
     */
    public function test_get_metadata() {
        $collection = new collection('core_message');
        $newcollection = provider::get_metadata($collection);
        $itemcollection = $newcollection->get_collection();
        $this->assertCount(4, $itemcollection);

        $messagetable = array_shift($itemcollection);
        $this->assertEquals('message', $messagetable->get_name());

        $messagereadtable = array_shift($itemcollection);
        $this->assertEquals('message_read', $messagereadtable->get_name());

        $messagecontacts = array_shift($itemcollection);
        $this->assertEquals('message_contacts', $messagecontacts->get_name());

        $usersettings = array_shift($itemcollection);
        $this->assertEquals('core_message_messageprovider_settings', $usersettings->get_name());

        $privacyfields = $messagetable->get_privacy_fields();
        $this->assertArrayHasKey('useridfrom', $privacyfields);
        $this->assertArrayHasKey('useridto', $privacyfields);
        $this->assertArrayHasKey('subject', $privacyfields);
        $this->assertArrayHasKey('fullmessage', $privacyfields);
        $this->assertArrayHasKey('fullmessageformat', $privacyfields);
        $this->assertArrayHasKey('fullmessagehtml', $privacyfields);
        $this->assertArrayHasKey('smallmessage', $privacyfields);
        $this->assertArrayHasKey('component', $privacyfields);
        $this->assertArrayHasKey('eventtype', $privacyfields);
        $this->assertArrayHasKey('contexturl', $privacyfields);
        $this->assertArrayHasKey('contexturlname', $privacyfields);
        $this->assertArrayHasKey('timecreated', $privacyfields);
        $this->assertEquals('privacy:metadata:messages', $messagetable->get_summary());

        $privacyfields = $messagereadtable->get_privacy_fields();
        $this->assertArrayHasKey('useridfrom', $privacyfields);
        $this->assertArrayHasKey('useridto', $privacyfields);
        $this->assertArrayHasKey('subject', $privacyfields);
        $this->assertArrayHasKey('fullmessage', $privacyfields);
        $this->assertArrayHasKey('fullmessageformat', $privacyfields);
        $this->assertArrayHasKey('fullmessagehtml', $privacyfields);
        $this->assertArrayHasKey('smallmessage', $privacyfields);
        $this->assertArrayHasKey('component', $privacyfields);
        $this->assertArrayHasKey('eventtype', $privacyfields);
        $this->assertArrayHasKey('contexturl', $privacyfields);
        $this->assertArrayHasKey('contexturlname', $privacyfields);
        $this->assertArrayHasKey('timeread', $privacyfields);
        $this->assertArrayHasKey('timecreated', $privacyfields);
        $this->assertEquals('privacy:metadata:messages', $messagereadtable->get_summary());

        $privacyfields = $messagecontacts->get_privacy_fields();
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('contactid', $privacyfields);
        $this->assertArrayHasKey('blocked', $privacyfields);
        $this->assertEquals('privacy:metadata:message_contacts', $messagecontacts->get_summary());
    }

    /**
     * Test for provider::export_user_preferences().
     */
    public function test_export_user_preferences_no_pref() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        provider::export_user_preferences($user->id);

        $writer = writer::with_context(\context_system::instance());

        $this->assertFalse($writer->has_any_data());
    }

    /**
     * Test for provider::export_user_preferences().
     */
    public function test_export_user_preferences() {
        global $USER;

        $this->resetAfterTest();

        $this->setAdminUser();

        // Create another user to set a preference for who we won't be exporting.
        $user = $this->getDataGenerator()->create_user();

        // Set some message user preferences.
        set_user_preference('message_provider_moodle_instantmessage_loggedin', 'airnotifier', $USER->id);
        set_user_preference('message_provider_moodle_instantmessage_loggedoff', 'popup', $USER->id);
        set_user_preference('message_blocknoncontacts', 1, $USER->id);
        set_user_preference('message_provider_moodle_instantmessage_loggedoff', 'inbound', $user->id);

        // Set an unrelated preference.
        set_user_preference('block_myoverview_last_tab', 'courses', $USER->id);

        provider::export_user_preferences($USER->id);

        $writer = writer::with_context(\context_system::instance());

        $this->assertTrue($writer->has_any_data());

        $prefs = (array) $writer->get_user_preferences('core_message');

        // Check only 3 preferences exist.
        $this->assertCount(3, $prefs);
        $this->assertArrayHasKey('message_provider_moodle_instantmessage_loggedin', $prefs);
        $this->assertArrayHasKey('message_provider_moodle_instantmessage_loggedoff', $prefs);
        $this->assertArrayHasKey('message_blocknoncontacts', $prefs);

        foreach ($prefs as $key => $pref) {
            if ($key == 'message_provider_moodle_instantmessage_loggedin') {
                $this->assertEquals('airnotifier', $pref->value);
            } else if ($key == 'message_provider_moodle_instantmessage_loggedoff') {
                $this->assertEquals('popup', $pref->value);
            } else {
                $this->assertEquals(1, $pref->value);
            }
        }
    }

    /**
     * Test for provider::get_contexts_for_userid().
     */
    public function test_get_contexts_for_userid() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $contextlist = provider::get_contexts_for_userid($user->id);
        $this->assertCount(1, $contextlist);
        $contextforuser = $contextlist->current();
        $this->assertEquals(SYSCONTEXTID, $contextforuser->id);
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_for_context_with_contacts() {
        $this->resetAfterTest();

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        // This user will not be added as a contact.
        $this->getDataGenerator()->create_user();

        message_add_contact($user2->id, 0, $user1->id);
        message_add_contact($user3->id, 0, $user1->id);
        message_add_contact($user4->id, 1, $user1->id);

        $this->export_context_data_for_user($user1->id, \context_system::instance(), 'core_message');

        $writer = writer::with_context(\context_system::instance());

        $contacts = (array) $writer->get_data([get_string('contacts', 'core_message')]);
        usort($contacts, ['static', 'sort_contacts']);

        $this->assertCount(3, $contacts);

        $contact1 = array_shift($contacts);
        $this->assertEquals($user2->id, $contact1->contact);
        $this->assertEquals(get_string('no'), $contact1->blocked);

        $contact2 = array_shift($contacts);
        $this->assertEquals($user3->id, $contact2->contact);
        $this->assertEquals(get_string('no'), $contact2->blocked);

        $contact3 = array_shift($contacts);
        $this->assertEquals($user4->id, $contact3->contact);
        $this->assertEquals(get_string('yes'), $contact3->blocked);
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_for_context_with_messages() {
        global $DB;

        $this->resetAfterTest();

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $now = time();

        // Send messages from user 1 to user 2.
        $m1 = $this->create_message_or_notification($user1->id, $user2->id, $now - (9 * DAYSECS), false, $now);
        $m2 = $this->create_message_or_notification($user2->id, $user1->id, $now - (8 * DAYSECS));
        $m3 = $this->create_message_or_notification($user1->id, $user2->id, $now - (7 * DAYSECS));

        // Send messages from user 3 to user 1.
        $m4 = $this->create_message_or_notification($user3->id, $user1->id, $now - (6 * DAYSECS), false, $now);
        $m5 = $this->create_message_or_notification($user1->id, $user3->id, $now - (5 * DAYSECS));
        $m6 = $this->create_message_or_notification($user3->id, $user1->id, $now - (4 * DAYSECS));

        // Send messages from user 3 to user 2 - these should not be included in the export.
        $m7 = $this->create_message_or_notification($user3->id, $user2->id, $now - (3 * DAYSECS), false, $now);
        $m8 = $this->create_message_or_notification($user2->id, $user3->id, $now - (2 * DAYSECS));
        $m9 = $this->create_message_or_notification($user3->id, $user2->id, $now - (1 * DAYSECS));

        // Mark message 2 and 5 as deleted.
        $dbm2 = $DB->get_record('message', ['id' => $m2]);
        $dbm5 = $DB->get_record('message', ['id' => $m5]);
        message_delete_message($dbm2, $user1->id);
        message_delete_message($dbm5, $user1->id);

        $this->export_context_data_for_user($user1->id, \context_system::instance(), 'core_message');

        $writer = writer::with_context(\context_system::instance());

        $this->assertTrue($writer->has_any_data());

        // Confirm the messages with user 2 are correct.
        $messages = (array) $writer->get_data([get_string('messages', 'core_message'), fullname($user2)]);
        $this->assertCount(3, $messages);

        $dbm1 = $DB->get_record('message_read', ['id' => $m1]);
        $dbm2 = $DB->get_record('message', ['id' => $m2]);
        $dbm3 = $DB->get_record('message', ['id' => $m3]);

        usort($messages, ['static', 'sort_messages']);
        $m1 = array_shift($messages);
        $m2 = array_shift($messages);
        $m3 = array_shift($messages);

        $this->assertEquals(get_string('yes'), $m1->sender);
        $this->assertEquals(message_format_message_text($dbm1), $m1->message);
        $this->assertEquals(transform::datetime($now - (9 * DAYSECS)), $m1->timecreated);
        $this->assertNotEquals('-', $m1->timeread);
        $this->assertArrayNotHasKey('timedeleted', (array) $m1);

        $this->assertEquals(get_string('no'), $m2->sender);
        $this->assertEquals(message_format_message_text($dbm2), $m2->message);
        $this->assertEquals(transform::datetime($now - (8 * DAYSECS)), $m2->timecreated);
        $this->assertEquals('-', $m2->timeread);
        $this->assertArrayHasKey('timedeleted', (array) $m2);

        $this->assertEquals(get_string('yes'), $m3->sender);
        $this->assertEquals(message_format_message_text($dbm3), $m3->message);
        $this->assertEquals(transform::datetime($now - (7 * DAYSECS)), $m3->timecreated);
        $this->assertEquals('-', $m3->timeread);

        // Confirm the messages with user 3 are correct.
        $messages = (array) $writer->get_data([get_string('messages', 'core_message'), fullname($user3)]);
        $this->assertCount(3, $messages);

        $dbm4 = $DB->get_record('message_read', ['id' => $m4]);
        $dbm5 = $DB->get_record('message', ['id' => $m5]);
        $dbm6 = $DB->get_record('message', ['id' => $m6]);

        usort($messages, ['static', 'sort_messages']);
        $m4 = array_shift($messages);
        $m5 = array_shift($messages);
        $m6 = array_shift($messages);

        $this->assertEquals(get_string('no'), $m4->sender);
        $this->assertEquals(message_format_message_text($dbm4), $m4->message);
        $this->assertEquals(transform::datetime($now - (6 * DAYSECS)), $m4->timecreated);
        $this->assertNotEquals('-', $m4->timeread);
        $this->assertArrayNotHasKey('timedeleted', (array) $m4);

        $this->assertEquals(get_string('yes'), $m5->sender);
        $this->assertEquals(message_format_message_text($dbm5), $m5->message);
        $this->assertEquals(transform::datetime($now - (5 * DAYSECS)), $m5->timecreated);
        $this->assertEquals('-', $m5->timeread);
        $this->assertArrayHasKey('timedeleted', (array) $m5);

        $this->assertEquals(get_string('no'), $m6->sender);
        $this->assertEquals(message_format_message_text($dbm6), $m6->message);
        $this->assertEquals(transform::datetime($now - (4 * DAYSECS)), $m6->timecreated);
        $this->assertEquals('-', $m6->timeread);
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_for_context_with_notifications() {
        $this->resetAfterTest();

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $now = time();
        $timeread = $now - DAYSECS;

        // Send notifications from user 1 to user 2.
        $this->create_message_or_notification($user1->id, $user2->id, $now + (9 * DAYSECS), true, $timeread);
        $this->create_message_or_notification($user2->id, $user1->id, $now + (8 * DAYSECS), true);
        $this->create_message_or_notification($user1->id, $user2->id, $now + (7 * DAYSECS), true);

        // Send notifications from user 3 to user 1.
        $this->create_message_or_notification($user3->id, $user1->id, $now + (6 * DAYSECS), true, $timeread);
        $this->create_message_or_notification($user1->id, $user3->id, $now + (5 * DAYSECS), true);
        $this->create_message_or_notification($user3->id, $user1->id, $now + (4 * DAYSECS), true);

        // Send notifications from user 3 to user 2 - should not be part of the export.
        $this->create_message_or_notification($user3->id, $user2->id, $now + (3 * DAYSECS), true, $timeread);
        $this->create_message_or_notification($user2->id, $user3->id, $now + (2 * DAYSECS), true);
        $this->create_message_or_notification($user3->id, $user2->id, $now + (1 * DAYSECS), true);

        $this->export_context_data_for_user($user1->id, \context_system::instance(), 'core_message');

        $writer = writer::with_context(\context_system::instance());

        $this->assertTrue($writer->has_any_data());

        // Confirm the notifications.
        $notifications = (array) $writer->get_data([get_string('notifications', 'core_message')]);

        $this->assertCount(6, $notifications);
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $this->resetAfterTest();

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $now = time();
        $timeread = $now - DAYSECS;

        $systemcontext = \context_system::instance();

        // Create contacts.
        message_add_contact($user1->id, 0, $user2->id);
        message_add_contact($user2->id, 0, $user1->id);

        // Create messages.
        $m1 = $this->create_message_or_notification($user1->id, $user2->id, $now + (9 * DAYSECS), false, $timeread);
        $m2 = $this->create_message_or_notification($user2->id, $user1->id, $now + (8 * DAYSECS));
        $m3 = $this->create_message_or_notification($user2->id, $user1->id, $now + (7 * DAYSECS), false, $timeread);
        $m4 = $this->create_message_or_notification($user1->id, $user2->id, $now + (6 * DAYSECS));

        // Create notifications.
        $n1 = $this->create_message_or_notification($user1->id, $user2->id, $now + (9 * DAYSECS), true, $timeread);
        $n2 = $this->create_message_or_notification($user2->id, $user1->id, $now + (8 * DAYSECS), true);
        $m3 = $this->create_message_or_notification($user2->id, $user1->id, $now + (7 * DAYSECS), true, $timeread);
        $m4 = $this->create_message_or_notification($user1->id, $user2->id, $now + (6 * DAYSECS), true);

        // Delete one of the messages.
        $dbm2 = $DB->get_record('message', ['id' => $m2]);
        message_delete_message($dbm2, $user1->id);

        // There should be two contacts.
        $this->assertEquals(2, $DB->count_records('message_contacts'));

        // There should be two unread messages.
        $this->assertEquals(2, $DB->count_records('message', ['notification' => 0]));

        // There should be two read messages.
        $this->assertEquals(2, $DB->count_records('message_read', ['notification' => 0]));

        // There should be two unread notifications.
        $this->assertEquals(2, $DB->count_records('message', ['notification' => 1]));

        // There should be two read notifications.
        $this->assertEquals(2, $DB->count_records('message_read', ['notification' => 1]));

        provider::delete_data_for_all_users_in_context($systemcontext);

        // Confirm all has been deleted.
        $this->assertEquals(0, $DB->count_records('message_contacts'));
        $this->assertEquals(0, $DB->count_records('message'));
        $this->assertEquals(0, $DB->count_records('message_read'));
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user() {
        global $DB;

        $this->resetAfterTest();

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $now = time();
        $timeread = $now - DAYSECS;

        // Create contacts.
        message_add_contact($user1->id, 0, $user2->id);
        message_add_contact($user2->id, 0, $user1->id);
        message_add_contact($user2->id, 0, $user3->id);

        // Create messages.
        $m1 = $this->create_message_or_notification($user1->id, $user2->id, $now + (9 * DAYSECS), false, $timeread);
        $m2 = $this->create_message_or_notification($user2->id, $user1->id, $now + (8 * DAYSECS));
        $m3 = $this->create_message_or_notification($user2->id, $user1->id, $now + (7 * DAYSECS), false, $timeread);
        $m4 = $this->create_message_or_notification($user1->id, $user2->id, $now + (6 * DAYSECS));

        // Create notifications.
        $n1 = $this->create_message_or_notification($user1->id, $user2->id, $now + (9 * DAYSECS), true, $timeread);
        $n2 = $this->create_message_or_notification($user2->id, $user1->id, $now + (8 * DAYSECS), true);
        $n3 = $this->create_message_or_notification($user2->id, $user3->id, $now + (8 * DAYSECS), true);
        $n4 = $this->create_message_or_notification($user3->id, $user2->id, $now + (8 * DAYSECS), true, $timeread);

        // Delete one of the messages.
        $dbm2 = $DB->get_record('message', ['id' => $m2]);
        message_delete_message($dbm2, $user1->id);

        // There should be three contacts.
        $this->assertEquals(3, $DB->count_records('message_contacts'));

        // There should be two unread messages.
        $this->assertEquals(2, $DB->count_records('message', ['notification' => 0]));

        // There should be two read messages.
        $this->assertEquals(2, $DB->count_records('message_read', ['notification' => 0]));

        // There should be two unread notifications.
        $this->assertEquals(2, $DB->count_records('message', ['notification' => 1]));

        // There should be two read notifications.
        $this->assertEquals(2, $DB->count_records('message_read', ['notification' => 1]));

        $systemcontext = \context_system::instance();
        $contextlist = new \core_privacy\local\request\approved_contextlist($user1, 'core_message',
            [$systemcontext->id]);
        provider::delete_data_for_user($contextlist);

        // Confirm the user 2 data still exists.
        $contacts = $DB->get_records('message_contacts');
        $messages = $DB->get_records('message', ['notification' => 0]);
        $messagesread = $DB->get_records('message_read', ['notification' => 0]);
        $notifications = $DB->get_records('message', ['notification' => 1]);
        $notificationsread = $DB->get_records('message_read', ['notification' => 1]);

        $this->assertCount(1, $contacts);
        $contact = reset($contacts);
        $this->assertEquals($user3->id, $contact->userid);
        $this->assertEquals($user2->id, $contact->contactid);

        $this->assertCount(1, $messages);
        $message = reset($messages);
        $this->assertEquals($m2, $message->id);

        $this->assertCount(1, $messagesread);
        $messagesread = reset($messagesread);
        $this->assertEquals($m3, $messagesread->id);

        $this->assertCount(1, $notifications);
        $notifications = reset($notifications);
        $this->assertEquals($n3, $notifications->id);

        $this->assertCount(1, $notificationsread);
        $notificationsread = reset($notificationsread);
        $this->assertEquals($n4, $notificationsread->id);
    }

    /**
     * Creates a message or notification to be used for testing.
     *
     * @param int $useridfrom The user id from
     * @param int $useridto The user id to
     * @param int $timecreated
     * @param bool $notification
     * @param int|null $timeread The time the message/notification was read, null if it hasn't been.
     * @return int The id of the message (in either the message or message_read table)
     * @throws dml_exception
     */
    private function create_message_or_notification($useridfrom, $useridto, $timecreated = null,
                                                    $notification = false, $timeread = null) {
        global $DB;

        $tabledata = new \stdClass();

        if (is_null($timecreated)) {
            $timecreated = time();
        }

        if (!is_null($timeread)) {
            $table = 'message_read';
            $tabledata->timeread = $timeread;
        } else {
            $table = 'message';
        }

        if ($notification) {
            $tabledata->eventtype = 'assign_notification';
            $tabledata->component = 'mod_assign';
            $tabledata->notification = 1;
            $tabledata->contexturl = 'https://www.google.com';
            $tabledata->contexturlname = 'google';
        } else {
            $tabledata->eventtype = 'instantmessage';
            $tabledata->component = 'moodle';
            $tabledata->notification = 0;
        }

        $tabledata->useridfrom = $useridfrom;
        $tabledata->useridto = $useridto;
        $tabledata->subject = 'Subject ' . $timecreated;
        $tabledata->fullmessage = 'Full message ' . $timecreated;
        $tabledata->fullmessageformat = FORMAT_PLAIN;
        $tabledata->fullmessagehtml = 'Full message HTML ' . $timecreated;
        $tabledata->smallmessage = 'Small message ' . $timecreated;
        $tabledata->timecreated = $timecreated;

        return $DB->insert_record($table, $tabledata);
    }

    /**
     * Comparison function for sorting messages.
     *
     * @param   \stdClass $a
     * @param   \stdClass $b
     * @return  bool
     */
    protected static function sort_messages($a, $b) {
        return $a->message > $b->message;
    }

    /**
     * Comparison function for sorting contacts.
     *
     * @param   \stdClass $a
     * @param   \stdClass $b
     * @return  bool
     */
    protected static function sort_contacts($a, $b) {
        return $a->contact > $b->contact;
    }
}
