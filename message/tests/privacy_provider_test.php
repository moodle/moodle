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
use \core_privacy\local\request\contextlist;
use \core_privacy\local\request\writer;
use \core_privacy\local\request\transform;
use \core_message\tests\helper as testhelper;

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
        $this->assertCount(10, $itemcollection);

        $messagestable = array_shift($itemcollection);
        $this->assertEquals('messages', $messagestable->get_name());

        $messageuseractionstable = array_shift($itemcollection);
        $this->assertEquals('message_user_actions', $messageuseractionstable->get_name());

        $messageconversationmemberstable = array_shift($itemcollection);
        $this->assertEquals('message_conversation_members', $messageconversationmemberstable->get_name());

        $messageconversationactions = array_shift($itemcollection);
        $this->assertEquals('message_conversation_actions', $messageconversationactions->get_name());

        $messagecontacts = array_shift($itemcollection);
        $this->assertEquals('message_contacts', $messagecontacts->get_name());

        $messagecontactrequests = array_shift($itemcollection);
        $this->assertEquals('message_contact_requests', $messagecontactrequests->get_name());

        $messageusersblocked = array_shift($itemcollection);
        $this->assertEquals('message_users_blocked', $messageusersblocked->get_name());

        $notificationstable = array_shift($itemcollection);
        $this->assertEquals('notifications', $notificationstable->get_name());

        $usersettings = array_shift($itemcollection);
        $this->assertEquals('core_message_messageprovider_settings', $usersettings->get_name());

        $favouriteconversations = array_shift($itemcollection);
        $this->assertEquals('core_favourites', $favouriteconversations->get_name());
        $this->assertEquals('privacy:metadata:core_favourites', $favouriteconversations->get_summary());

        $privacyfields = $messagestable->get_privacy_fields();
        $this->assertArrayHasKey('useridfrom', $privacyfields);
        $this->assertArrayHasKey('conversationid', $privacyfields);
        $this->assertArrayHasKey('subject', $privacyfields);
        $this->assertArrayHasKey('fullmessage', $privacyfields);
        $this->assertArrayHasKey('fullmessageformat', $privacyfields);
        $this->assertArrayHasKey('fullmessagehtml', $privacyfields);
        $this->assertArrayHasKey('smallmessage', $privacyfields);
        $this->assertArrayHasKey('timecreated', $privacyfields);
        $this->assertArrayHasKey('customdata', $privacyfields);
        $this->assertEquals('privacy:metadata:messages', $messagestable->get_summary());

        $privacyfields = $messageuseractionstable->get_privacy_fields();
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('messageid', $privacyfields);
        $this->assertArrayHasKey('action', $privacyfields);
        $this->assertArrayHasKey('timecreated', $privacyfields);
        $this->assertEquals('privacy:metadata:message_user_actions', $messageuseractionstable->get_summary());

        $privacyfields = $messageconversationmemberstable->get_privacy_fields();
        $this->assertArrayHasKey('conversationid', $privacyfields);
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('timecreated', $privacyfields);
        $this->assertEquals('privacy:metadata:message_conversation_members', $messageconversationmemberstable->get_summary());

        $privacyfields = $messagecontacts->get_privacy_fields();
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('contactid', $privacyfields);
        $this->assertArrayHasKey('timecreated', $privacyfields);
        $this->assertEquals('privacy:metadata:message_contacts', $messagecontacts->get_summary());

        $privacyfields = $messagecontactrequests->get_privacy_fields();
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('requesteduserid', $privacyfields);
        $this->assertArrayHasKey('timecreated', $privacyfields);
        $this->assertEquals('privacy:metadata:message_contact_requests', $messagecontactrequests->get_summary());

        $privacyfields = $messageusersblocked->get_privacy_fields();
        $this->assertArrayHasKey('userid', $privacyfields);
        $this->assertArrayHasKey('blockeduserid', $privacyfields);
        $this->assertArrayHasKey('timecreated', $privacyfields);
        $this->assertEquals('privacy:metadata:message_users_blocked', $messageusersblocked->get_summary());

        $privacyfields = $notificationstable->get_privacy_fields();
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
        $this->assertArrayHasKey('customdata', $privacyfields);
        $this->assertEquals('privacy:metadata:notifications', $notificationstable->get_summary());
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
        set_user_preference('message_blocknoncontacts', \core_message\api::MESSAGE_PRIVACY_ONLYCONTACTS, $USER->id);
        set_user_preference('message_entertosend', true, $USER->id);
        set_user_preference('message_provider_moodle_instantmessage_loggedoff', 'inbound', $user->id);

        // Set an unrelated preference.
        set_user_preference('some_unrelated_preference', 'courses', $USER->id);

        provider::export_user_preferences($USER->id);

        $writer = writer::with_context(\context_system::instance());

        $this->assertTrue($writer->has_any_data());

        $prefs = (array) $writer->get_user_preferences('core_message');

        // Check only 3 preferences exist.
        $this->assertCount(4, $prefs);
        $this->assertArrayHasKey('message_provider_moodle_instantmessage_loggedin', $prefs);
        $this->assertArrayHasKey('message_provider_moodle_instantmessage_loggedoff', $prefs);
        $this->assertArrayHasKey('message_blocknoncontacts', $prefs);
        $this->assertArrayHasKey('message_entertosend', $prefs);

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
     * Test for provider::get_contexts_for_userid() when there is no message or notification.
     */
    public function test_get_contexts_for_userid_no_data() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();

        $contextlist = provider::get_contexts_for_userid($user->id);
        $this->assertCount(1, $contextlist);

        $this->remove_user_self_conversation($user->id);

        $contextlist = provider::get_contexts_for_userid($user->id);
        $this->assertEmpty($contextlist);
    }

    /**
     * Test for provider::get_contexts_for_userid() when there is a private message between users.
     */
    public function test_get_contexts_for_userid_with_private_messages() {
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        // Remove user self-conversations.
        $this->remove_user_self_conversation($user1->id);
        $this->remove_user_self_conversation($user2->id);
        $this->remove_user_self_conversation($user3->id);

        // Test nothing is found before group conversations is created or message is sent.
        $contextlist = provider::get_contexts_for_userid($user1->id);
        $this->assertCount(0, $contextlist);
        $contextlist = provider::get_contexts_for_userid($user2->id);
        $this->assertCount(0, $contextlist);

        // Send some private messages.
        $pm1id = $this->create_message($user1->id, $user2->id, time() - (9 * DAYSECS));

        // Test for the sender (user1).
        $contextlist = provider::get_contexts_for_userid($user1->id);
        $this->assertCount(1, $contextlist);
        $contextforuser = $contextlist->current();
        $this->assertEquals(
                \context_user::instance($user1->id)->id,
                $contextforuser->id);

        // Test for the receiver (user2).
        $contextlist = provider::get_contexts_for_userid($user2->id);
        $this->assertCount(1, $contextlist);
        $contextforuser = $contextlist->current();
        $this->assertEquals(
                \context_user::instance($user2->id)->id,
                $contextforuser->id);

        // Test for user3 (no private messages).
        $contextlist = provider::get_contexts_for_userid($user3->id);
        $this->assertCount(0, $contextlist);
    }

    /**
     * Test for provider::get_contexts_for_userid() when there is several messages (private and group).
     */
    public function test_get_contexts_for_userid_with_messages() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        // Remove user self-conversations.
        $this->remove_user_self_conversation($user1->id);
        $this->remove_user_self_conversation($user2->id);
        $this->remove_user_self_conversation($user3->id);
        $this->remove_user_self_conversation($user4->id);

        // Test nothing is found before group conversations is created or message is sent.
        $contextlist = provider::get_contexts_for_userid($user1->id);
        $this->assertCount(0, $contextlist);
        $contextlist = provider::get_contexts_for_userid($user2->id);
        $this->assertCount(0, $contextlist);

        // Create course.
        $course1 = $this->getDataGenerator()->create_course();
        $coursecontext1 = context_course::instance($course1->id);

        // Enrol users to courses.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id);

        // Create groups (only one with enablemessaging = 1).
        $group1a = $this->getDataGenerator()->create_group(array('courseid' => $course1->id, 'enablemessaging' => 1));

        // Add users to groups.
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user3->id));

        // Get conversation.
        $component = 'core_group';
        $itemtype = 'groups';
        $conversation1 = \core_message\api::get_conversation_by_area(
            $component,
            $itemtype,
            $group1a->id,
            $coursecontext1->id
        );

        // Send some messages to the group conversation.
        $now = time();
        $m1id = testhelper::send_fake_message_to_conversation($user1, $conversation1->id, 'Message 1', $now + 1);
        $m2id = testhelper::send_fake_message_to_conversation($user1, $conversation1->id, 'Message 2', $now + 2);
        $m3id = testhelper::send_fake_message_to_conversation($user2, $conversation1->id, 'Message 3', $now + 3);

        // Test for user1 (although is member of the conversation, hasn't any private message).
        $contextlist = provider::get_contexts_for_userid($user1->id);
        $this->assertCount(0, $contextlist);

        // Test for user2 (although is member of the conversation, hasn't any private message).
        $contextlist = provider::get_contexts_for_userid($user2->id);
        $this->assertCount(0, $contextlist);

        // Test for user3 (although is member of the conversation, hasn't any private message).
        $contextlist = provider::get_contexts_for_userid($user3->id);
        $this->assertCount(0, $contextlist);

        // Test for user4 (doesn't belong to the conversation).
        $contextlist = provider::get_contexts_for_userid($user4->id);
        $this->assertCount(0, $contextlist);

        // Send some private messages.
        $pm1id = $this->create_message($user1->id, $user2->id, time() - (9 * DAYSECS));

        // Test user1 now has the user context because of the private message.
        $contextlist = provider::get_contexts_for_userid($user1->id);
        $this->assertCount(1, $contextlist);
        $contextforuser = $contextlist->current();
        $this->assertEquals(
                \context_user::instance($user1->id)->id,
                $contextforuser->id);

        // Test user2 now has the user context because of the private message.
        $contextlist = provider::get_contexts_for_userid($user2->id);
        $this->assertCount(1, $contextlist);
        $contextforuser = $contextlist->current();
        $this->assertEquals(
                \context_user::instance($user2->id)->id,
                $contextforuser->id);

        // Test for user3 (although is member of the conversation, hasn't still any private message).
        $contextlist = provider::get_contexts_for_userid($user3->id);
        $this->assertCount(0, $contextlist);

        // Test for user4 (doesn't belong to the conversation and hasn't any private message).
        $contextlist = provider::get_contexts_for_userid($user4->id);
        $this->assertCount(0, $contextlist);
    }

    /**
     * Test for provider::get_contexts_for_userid() when there is a notification between users.
     */
    public function test_get_contexts_for_userid_with_notification() {
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Remove user self-conversations.
        $this->remove_user_self_conversation($user1->id);
        $this->remove_user_self_conversation($user2->id);

        // Test nothing is found before notification is created.
        $contextlist = provider::get_contexts_for_userid($user1->id);
        $this->assertCount(0, $contextlist);
        $contextlist = provider::get_contexts_for_userid($user2->id);
        $this->assertCount(0, $contextlist);

        $this->create_notification($user1->id, $user2->id, time() - (9 * DAYSECS));

        // Test for the sender.
        $contextlist = provider::get_contexts_for_userid($user1->id);
        $this->assertCount(1, $contextlist);
        $contextforuser = $contextlist->current();
        $this->assertEquals(
                context_user::instance($user1->id)->id,
                $contextforuser->id);

        // Test for the receiver.
        $contextlist = provider::get_contexts_for_userid($user2->id);
        $this->assertCount(1, $contextlist);
        $contextforuser = $contextlist->current();
        $this->assertEquals(
                context_user::instance($user2->id)->id,
                $contextforuser->id);
    }

    /**
     * Test for provider::get_contexts_for_userid() when a users has a contact.
     */
    public function test_get_contexts_for_userid_with_contact() {
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Remove user self-conversations.
        $this->remove_user_self_conversation($user1->id);
        $this->remove_user_self_conversation($user2->id);

        // Test nothing is found before contact is created.
        $contextlist = provider::get_contexts_for_userid($user1->id);
        $this->assertCount(0, $contextlist);
        $contextlist = provider::get_contexts_for_userid($user2->id);
        $this->assertCount(0, $contextlist);

        \core_message\api::add_contact($user1->id, $user2->id);

        // Test for the user adding the contact.
        $contextlist = provider::get_contexts_for_userid($user1->id);
        $this->assertCount(1, $contextlist);
        $contextforuser = $contextlist->current();
        $this->assertEquals(
                context_user::instance($user1->id)->id,
                $contextforuser->id);

        // Test for the user who is the contact.
        $contextlist = provider::get_contexts_for_userid($user2->id);
        $this->assertCount(1, $contextlist);
        $contextforuser = $contextlist->current();
        $this->assertEquals(
                context_user::instance($user2->id)->id,
                $contextforuser->id);
    }

    /**
     * Test for provider::get_contexts_for_userid() when a user makes a contact request.
     */
    public function test_get_contexts_for_userid_with_contact_request() {
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Remove user self-conversations.
        $this->remove_user_self_conversation($user1->id);
        $this->remove_user_self_conversation($user2->id);

        // Test nothing is found before request is created.
        $contextlist = provider::get_contexts_for_userid($user1->id);
        $this->assertCount(0, $contextlist);
        $contextlist = provider::get_contexts_for_userid($user2->id);
        $this->assertCount(0, $contextlist);

        \core_message\api::create_contact_request($user1->id, $user2->id);

        // Test for the user requesting the contact.
        $contextlist = provider::get_contexts_for_userid($user1->id);
        $this->assertCount(1, $contextlist);
        $contextforuser = $contextlist->current();
        $this->assertEquals(
                context_user::instance($user1->id)->id,
                $contextforuser->id);

        // Test for the user receiving the contact request.
        $contextlist = provider::get_contexts_for_userid($user2->id);
        $this->assertCount(1, $contextlist);
        $contextforuser = $contextlist->current();
        $this->assertEquals(
                context_user::instance($user2->id)->id,
                $contextforuser->id);
    }

    /**
     * Test for provider::get_contexts_for_userid() when a user is blocked.
     */
    public function test_get_contexts_for_userid_with_blocked_contact() {
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Remove user self-conversations.
        $this->remove_user_self_conversation($user1->id);
        $this->remove_user_self_conversation($user2->id);

        // Test nothing is found before user is blocked.
        $contextlist = provider::get_contexts_for_userid($user1->id);
        $this->assertCount(0, $contextlist);
        $contextlist = provider::get_contexts_for_userid($user2->id);
        $this->assertCount(0, $contextlist);

        \core_message\api::block_user($user1->id, $user2->id);

        // Test for the blocking user.
        $contextlist = provider::get_contexts_for_userid($user1->id);
        $this->assertCount(1, $contextlist);
        $contextforuser = $contextlist->current();
        $this->assertEquals(
                context_user::instance($user1->id)->id,
                $contextforuser->id);

        // Test for the user who is blocked.
        $contextlist = provider::get_contexts_for_userid($user2->id);
        $this->assertCount(1, $contextlist);
        $contextforuser = $contextlist->current();
        $this->assertEquals(
                context_user::instance($user2->id)->id,
                $contextforuser->id);
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

        // Remove user self-conversations.
        $this->remove_user_self_conversation($user1->id);
        $this->remove_user_self_conversation($user2->id);
        $this->remove_user_self_conversation($user3->id);
        $this->remove_user_self_conversation($user4->id);

        \core_message\api::add_contact($user1->id, $user2->id);
        \core_message\api::add_contact($user1->id, $user3->id);
        \core_message\api::add_contact($user1->id, $user4->id);

        $user1context = context_user::instance($user1->id);

        $this->export_context_data_for_user($user1->id, $user1context, 'core_message');

        $writer = writer::with_context($user1context);

        $contacts = (array) $writer->get_data([get_string('contacts', 'core_message')]);
        usort($contacts, ['static', 'sort_contacts']);

        $this->assertCount(3, $contacts);

        $contact1 = array_shift($contacts);
        $this->assertEquals($user2->id, $contact1->contact);

        $contact2 = array_shift($contacts);
        $this->assertEquals($user3->id, $contact2->contact);

        $contact3 = array_shift($contacts);
        $this->assertEquals($user4->id, $contact3->contact);
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_for_context_with_contact_requests() {
        $this->resetAfterTest();

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        // Remove user self-conversations.
        $this->remove_user_self_conversation($user1->id);
        $this->remove_user_self_conversation($user2->id);
        $this->remove_user_self_conversation($user3->id);
        $this->remove_user_self_conversation($user4->id);

        \core_message\api::create_contact_request($user1->id, $user2->id);
        \core_message\api::create_contact_request($user3->id, $user1->id);
        \core_message\api::create_contact_request($user1->id, $user4->id);

        $user1context = context_user::instance($user1->id);

        $this->export_context_data_for_user($user1->id, $user1context, 'core_message');

        $writer = writer::with_context($user1context);

        $contactrequests = (array) $writer->get_data([get_string('contactrequests', 'core_message')]);

        $this->assertCount(3, $contactrequests);

        $contactrequest1 = array_shift($contactrequests);
        $this->assertEquals($user2->id, $contactrequest1->contactrequest);
        $this->assertEquals(get_string('yes'), $contactrequest1->maderequest);

        $contactrequest2 = array_shift($contactrequests);
        $this->assertEquals($user3->id, $contactrequest2->contactrequest);
        $this->assertEquals(get_string('no'), $contactrequest2->maderequest);

        $contactrequest3 = array_shift($contactrequests);
        $this->assertEquals($user4->id, $contactrequest3->contactrequest);
        $this->assertEquals(get_string('yes'), $contactrequest3->maderequest);
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_for_context_with_blocked_users() {
        $this->resetAfterTest();

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        // Remove user self-conversations.
        $this->remove_user_self_conversation($user1->id);
        $this->remove_user_self_conversation($user2->id);
        $this->remove_user_self_conversation($user3->id);
        $this->remove_user_self_conversation($user4->id);

        \core_message\api::block_user($user1->id, $user2->id);
        \core_message\api::block_user($user1->id, $user3->id);
        \core_message\api::block_user($user1->id, $user4->id);

        $user1context = context_user::instance($user1->id);

        $this->export_context_data_for_user($user1->id, $user1context, 'core_message');

        $writer = writer::with_context($user1context);

        $blockedusers = (array) $writer->get_data([get_string('blockedusers', 'core_message')]);

        $this->assertCount(3, $blockedusers);

        $blockeduser1 = array_shift($blockedusers);
        $this->assertEquals($user2->id, $blockeduser1->blockeduser);

        $blockeduser2 = array_shift($blockedusers);
        $this->assertEquals($user3->id, $blockeduser2->blockeduser);

        $blockeduser3 = array_shift($blockedusers);
        $this->assertEquals($user4->id, $blockeduser3->blockeduser);
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_for_context_with_private_messages() {
        global $DB;

        $this->resetAfterTest();

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        // Remove user self-conversations.
        $this->remove_user_self_conversation($user1->id);
        $this->remove_user_self_conversation($user2->id);
        $this->remove_user_self_conversation($user3->id);

        $now = time();

        // Send messages from user 1 to user 2.
        $m1 = $this->create_message($user1->id, $user2->id, $now - (9 * DAYSECS), true);
        $m2 = $this->create_message($user2->id, $user1->id, $now - (8 * DAYSECS));
        $m3 = $this->create_message($user1->id, $user2->id, $now - (7 * DAYSECS));

        // Send messages from user 3 to user 1.
        $m4 = $this->create_message($user3->id, $user1->id, $now - (6 * DAYSECS), true);
        $m5 = $this->create_message($user1->id, $user3->id, $now - (5 * DAYSECS));
        $m6 = $this->create_message($user3->id, $user1->id, $now - (4 * DAYSECS));

        // Send messages from user 3 to user 2 - these should not be included in the export.
        $m7 = $this->create_message($user3->id, $user2->id, $now - (3 * DAYSECS), true);
        $m8 = $this->create_message($user2->id, $user3->id, $now - (2 * DAYSECS));
        $m9 = $this->create_message($user3->id, $user2->id, $now - (1 * DAYSECS));

        // Mark message 2 and 5 as deleted.
        \core_message\api::delete_message($user1->id, $m2);
        \core_message\api::delete_message($user1->id, $m5);

        $user1context = context_user::instance($user1->id);

        $this->export_context_data_for_user($user1->id, $user1context, 'core_message');

        $writer = writer::with_context($user1context);

        $this->assertTrue($writer->has_any_data());

        // Confirm the messages with user 2 are correct.
        $messages = (array) $writer->get_data([get_string('messages', 'core_message'), $user2->id]);
        $this->assertCount(3, $messages);

        $dbm1 = $DB->get_record('messages', ['id' => $m1]);
        $dbm2 = $DB->get_record('messages', ['id' => $m2]);
        $dbm3 = $DB->get_record('messages', ['id' => $m3]);

        usort($messages, ['static', 'sort_messages']);
        $m1 = array_shift($messages);
        $m2 = array_shift($messages);
        $m3 = array_shift($messages);

        $this->assertEquals(get_string('yes'), $m1->issender);
        $this->assertEquals(message_format_message_text($dbm1), $m1->message);
        $this->assertEquals(transform::datetime($now - (9 * DAYSECS)), $m1->timecreated);
        $this->assertEquals('-', $m1->timeread);
        $this->assertArrayNotHasKey('timedeleted', (array) $m1);

        $this->assertEquals(get_string('no'), $m2->issender);
        $this->assertEquals(message_format_message_text($dbm2), $m2->message);
        $this->assertEquals(transform::datetime($now - (8 * DAYSECS)), $m2->timecreated);
        $this->assertEquals('-', $m2->timeread);
        $this->assertArrayHasKey('timedeleted', (array) $m2);

        $this->assertEquals(get_string('yes'), $m3->issender);
        $this->assertEquals(message_format_message_text($dbm3), $m3->message);
        $this->assertEquals(transform::datetime($now - (7 * DAYSECS)), $m3->timecreated);
        $this->assertEquals('-', $m3->timeread);

        // Confirm the messages with user 3 are correct.
        $messages = (array) $writer->get_data([get_string('messages', 'core_message'), $user3->id]);
        $this->assertCount(3, $messages);

        $dbm4 = $DB->get_record('messages', ['id' => $m4]);
        $dbm5 = $DB->get_record('messages', ['id' => $m5]);
        $dbm6 = $DB->get_record('messages', ['id' => $m6]);

        usort($messages, ['static', 'sort_messages']);
        $m4 = array_shift($messages);
        $m5 = array_shift($messages);
        $m6 = array_shift($messages);

        $this->assertEquals(get_string('no'), $m4->issender);
        $this->assertEquals(message_format_message_text($dbm4), $m4->message);
        $this->assertEquals(transform::datetime($now - (6 * DAYSECS)), $m4->timecreated);
        $this->assertNotEquals('-', $m4->timeread);
        $this->assertArrayNotHasKey('timedeleted', (array) $m4);

        $this->assertEquals(get_string('yes'), $m5->issender);
        $this->assertEquals(message_format_message_text($dbm5), $m5->message);
        $this->assertEquals(transform::datetime($now - (5 * DAYSECS)), $m5->timecreated);
        $this->assertEquals('-', $m5->timeread);
        $this->assertArrayHasKey('timedeleted', (array) $m5);

        $this->assertEquals(get_string('no'), $m6->issender);
        $this->assertEquals(message_format_message_text($dbm6), $m6->message);
        $this->assertEquals(transform::datetime($now - (4 * DAYSECS)), $m6->timecreated);
        $this->assertEquals('-', $m6->timeread);
    }

    /**
     * Test for provider::export_user_data().
     */
    public function test_export_for_context_with_messages() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $now = time();
        $systemcontext = \context_system::instance();

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user1context = \context_user::instance($user1->id);

        // Remove user self-conversations.
        $this->remove_user_self_conversation($user1->id);
        $this->remove_user_self_conversation($user2->id);
        $this->remove_user_self_conversation($user3->id);

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $coursecontext1 = \context_course::instance($course1->id);
        $coursecontext2 = \context_course::instance($course2->id);

        // Enrol users to courses.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id);

        // Create course groups with group messaging enabled.
        $group1a = $this->getDataGenerator()->create_group(array('courseid' => $course1->id, 'enablemessaging' => 1));
        $group2a = $this->getDataGenerator()->create_group(array('courseid' => $course2->id, 'enablemessaging' => 1));

        // Add users to groups.
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user3->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2a->id, 'userid' => $user1->id));

        // Get conversation.
        $component = 'core_group';
        $itemtype = 'groups';
        $conversation = \core_message\api::get_conversation_by_area(
            $component,
            $itemtype,
            $group1a->id,
            $coursecontext1->id
        );

        // Send some private messages between user 1 and user 2.
        $pm1id = $this->create_message($user1->id, $user2->id, $now);

        $dbpm1 = $DB->get_record('messages', ['id' => $pm1id]);

        // Send some messages to the conversation.
        $m1 = testhelper::send_fake_message_to_conversation($user1, $conversation->id, 'Message 1', $now + 1);
        $m2 = testhelper::send_fake_message_to_conversation($user1, $conversation->id, 'Message 2', $now + 2);
        $m3 = testhelper::send_fake_message_to_conversation($user2, $conversation->id, 'Message 3', $now + 3);

        $dbm1 = $DB->get_record('messages', ['id' => $m1]);
        $dbm2 = $DB->get_record('messages', ['id' => $m2]);
        $dbm3 = $DB->get_record('messages', ['id' => $m3]);

        // Mark as read and delete some messages.
        \core_message\api::mark_message_as_read($user2->id, $dbm1);
        \core_message\api::delete_message($user1->id, $m2);

        // Confirm the user1 has no data in any course context because private messages are related to user context.
        $this->export_context_data_for_user($user1->id, $coursecontext2, 'core_message');

        // Check that system context hasn't been exported.
        $writer = writer::with_context($systemcontext);
        $this->assertFalse($writer->has_any_data());

        // Check that course1 context hasn't been exported.
        $writer = writer::with_context($coursecontext1);
        $this->assertFalse($writer->has_any_data());

        // Check that course2 context has been exported and contains data.
        $writer = writer::with_context($coursecontext2);
        $this->assertFalse($writer->has_any_data());

        // Confirm the user1 has only private messages in the user context.
        $this->export_context_data_for_user($user1->id, $user1context, 'core_message');
        $writer = writer::with_context($user1context);
        $this->assertTrue($writer->has_any_data());

        // Confirm the messages with user 2 are correct.
        $messages = (array) $writer->get_data([get_string('messages', 'core_message'), $user2->id]);
        $this->assertCount(1, $messages);
        $m1 = reset($messages);

        $this->assertEquals(get_string('yes'), $m1->issender);
        $this->assertEquals(message_format_message_text($dbpm1), $m1->message);
        $this->assertEquals(transform::datetime($now), $m1->timecreated);
        $this->assertEquals('-', $m1->timeread);
        $this->assertArrayNotHasKey('timedeleted', (array) $m1);

        // Confirm the messages with user 3 are correct.
        $messages = (array) $writer->get_data([get_string('messages', 'core_message'), fullname($user3)]);
        $this->assertCount(0, $messages);
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

        // Remove user self-conversations.
        $this->remove_user_self_conversation($user1->id);
        $this->remove_user_self_conversation($user2->id);
        $this->remove_user_self_conversation($user3->id);

        $now = time();
        $timeread = $now - DAYSECS;

        // Send notifications from user 1 to user 2.
        $this->create_notification($user1->id, $user2->id, $now + (9 * DAYSECS), $timeread);
        $this->create_notification($user2->id, $user1->id, $now + (8 * DAYSECS));
        $this->create_notification($user1->id, $user2->id, $now + (7 * DAYSECS));

        // Send notifications from user 3 to user 1.
        $this->create_notification($user3->id, $user1->id, $now + (6 * DAYSECS), $timeread);
        $this->create_notification($user1->id, $user3->id, $now + (5 * DAYSECS));
        $this->create_notification($user3->id, $user1->id, $now + (4 * DAYSECS));

        // Send notifications from user 3 to user 2 - should not be part of the export.
        $this->create_notification($user3->id, $user2->id, $now + (3 * DAYSECS), $timeread);
        $this->create_notification($user2->id, $user3->id, $now + (2 * DAYSECS));
        $this->create_notification($user3->id, $user2->id, $now + (1 * DAYSECS));

        $user1context = context_user::instance($user1->id);

        $this->export_context_data_for_user($user1->id, $user1context, 'core_message');

        $writer = writer::with_context($user1context);

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
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();
        $user5 = $this->getDataGenerator()->create_user();

        $now = time();
        $timeread = $now - DAYSECS;

        $user1context = context_user::instance($user1->id);

        // Create contacts.
        \core_message\api::add_contact($user1->id, $user2->id);
        \core_message\api::add_contact($user2->id, $user3->id);

        // Create contact requests.
        \core_message\api::create_contact_request($user1->id, $user3->id);
        \core_message\api::create_contact_request($user2->id, $user4->id);

        // Block a user.
        \core_message\api::block_user($user1->id, $user3->id);
        \core_message\api::block_user($user3->id, $user4->id);

        // Create messages.
        $m1 = $this->create_message($user1->id, $user2->id, $now + (9 * DAYSECS), true);
        $m2 = $this->create_message($user2->id, $user1->id, $now + (8 * DAYSECS));
        $m3 = $this->create_message($user2->id, $user3->id, $now + (7 * DAYSECS));

        // Create notifications.
        $n1 = $this->create_notification($user1->id, $user2->id, $now + (9 * DAYSECS), $timeread);
        $n2 = $this->create_notification($user2->id, $user1->id, $now + (8 * DAYSECS));
        $n3 = $this->create_notification($user2->id, $user3->id, $now + (7 * DAYSECS));

        // Delete one of the messages.
        \core_message\api::delete_message($user1->id, $m2);

        // There should be 2 contacts.
        $this->assertEquals(2, $DB->count_records('message_contacts'));

        // There should be 2 contact requests.
        $this->assertEquals(2, $DB->count_records('message_contact_requests'));

        // There should be 2 blocked users.
        $this->assertEquals(2, $DB->count_records('message_users_blocked'));

        // There should be 3 messages.
        $this->assertEquals(3, $DB->count_records('messages'));

        // There should be 2 user actions - one for reading the message, one for deleting.
        $this->assertEquals(2, $DB->count_records('message_user_actions'));

        // There should be 4 conversation members + 5 self-conversations.
        $this->assertEquals(9, $DB->count_records('message_conversation_members'));

        // There should be 5 notifications (3 from create_notification and 2 from create_contact_request).
        $this->assertEquals(5, $DB->count_records('notifications'));

        provider::delete_data_for_all_users_in_context($user1context);

        // Confirm there is only 1 contact left.
        $this->assertEquals(1, $DB->count_records('message_contacts'));
        // And it is not related to user1.
        $this->assertEquals(0,
                $DB->count_records_select('message_contacts', 'userid = ? OR contactid = ?', [$user1->id, $user1->id]));

        // Confirm there is only 1 contact request left.
        $this->assertEquals(1, $DB->count_records('message_contact_requests'));
        // And it is not related to user1.
        $this->assertEquals(0,
                $DB->count_records_select('message_contact_requests', 'userid = ? OR requesteduserid = ?',
                        [$user1->id, $user1->id]));

        // Confirm there is only 1 blocked user left.
        $this->assertEquals(1, $DB->count_records('message_users_blocked'));
        // And it is not related to user1.
        $this->assertEquals(0,
                $DB->count_records_select('message_users_blocked', 'userid = ? OR blockeduserid = ?', [$user1->id, $user1->id]));

        // Confirm there are only 2 messages left.
        $this->assertEquals(2, $DB->count_records('messages'));
        // And none of them are from user1.
        $this->assertEquals(0, $DB->count_records('messages', ['useridfrom' => $user1->id]));

        // Confirm there is 0 user action left.
        $this->assertEquals(0, $DB->count_records('message_user_actions'));
        // And it is not for user1.
        $this->assertEquals(0, $DB->count_records('message_user_actions', ['userid' => $user1->id]));

        // Confirm there are only 3 conversation members left + 4 self-conversations.
        $this->assertEquals(7, $DB->count_records('message_conversation_members'));
        // And user1 is not in any conversation.
        $this->assertEquals(0, $DB->count_records('message_conversation_members', ['userid' => $user1->id]));

        // Confirm there are only 2 notifications.
        $this->assertEquals(2, $DB->count_records('notifications'));
        // And it is not related to user1.
        $this->assertEquals(0,
                $DB->count_records_select('notifications', 'useridfrom = ? OR useridto = ? ', [$user1->id, $user1->id]));

        // Delete user self-conversations.
        $this->remove_user_self_conversation($user2->id);
        $this->remove_user_self_conversation($user3->id);
        $this->remove_user_self_conversation($user4->id);
        $this->remove_user_self_conversation($user5->id);

        // Confirm there are only 3 conversation members left.
        $this->assertEquals(3, $DB->count_records('message_conversation_members'));
        // And user1 is not in any conversation.
        $this->assertEquals(0, $DB->count_records('message_conversation_members', ['userid' => $user1->id]));

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
        $user4 = $this->getDataGenerator()->create_user();
        $user5 = $this->getDataGenerator()->create_user();
        $user6 = $this->getDataGenerator()->create_user();

        $now = time();
        $timeread = $now - DAYSECS;

        // Create contacts.
        \core_message\api::add_contact($user1->id, $user2->id);
        \core_message\api::add_contact($user2->id, $user3->id);

        // Create contact requests.
        \core_message\api::create_contact_request($user1->id, $user3->id);
        \core_message\api::create_contact_request($user2->id, $user4->id);

        // Block users.
        \core_message\api::block_user($user1->id, $user5->id);
        \core_message\api::block_user($user2->id, $user6->id);

        // Create messages.
        $m1 = $this->create_message($user1->id, $user2->id, $now + (9 * DAYSECS), $timeread);
        $m2 = $this->create_message($user2->id, $user1->id, $now + (8 * DAYSECS));

        // Create notifications.
        $n1 = $this->create_notification($user1->id, $user2->id, $now + (9 * DAYSECS), $timeread);
        $n2 = $this->create_notification($user2->id, $user1->id, $now + (8 * DAYSECS));
        $n2 = $this->create_notification($user2->id, $user3->id, $now + (8 * DAYSECS));

        // Delete one of the messages.
        \core_message\api::delete_message($user1->id, $m2);

        // There should be 2 contacts.
        $this->assertEquals(2, $DB->count_records('message_contacts'));

        // There should be 1 contact request.
        $this->assertEquals(2, $DB->count_records('message_contact_requests'));

        // There should be 1 blocked user.
        $this->assertEquals(2, $DB->count_records('message_users_blocked'));

        // There should be two messages.
        $this->assertEquals(2, $DB->count_records('messages'));

        // There should be two user actions - one for reading the message, one for deleting.
        $this->assertEquals(2, $DB->count_records('message_user_actions'));

        // There should be two conversation members + 6 self-conversations.
        $this->assertEquals(8, $DB->count_records('message_conversation_members'));

        // There should be 5 notifications (3 from create_notification and 2 from create_contact_request).
        $this->assertEquals(5, $DB->count_records('notifications'));

        $user1context = context_user::instance($user1->id);
        $contextlist = new \core_privacy\local\request\approved_contextlist($user1, 'core_message',
            [$user1context->id]);
        provider::delete_data_for_user($contextlist);

        // Confirm the user 2 data still exists.
        $contacts = $DB->get_records('message_contacts');
        $contactrequests = $DB->get_records('message_contact_requests');
        $blockedusers = $DB->get_records('message_users_blocked');
        $messages = $DB->get_records('messages');
        $muas = $DB->get_records('message_user_actions');
        $mcms = $DB->get_records('message_conversation_members');
        $notifications = $DB->get_records('notifications');

        $this->assertCount(1, $contacts);
        $contact = reset($contacts);
        $this->assertEquals($user2->id, $contact->userid);
        $this->assertEquals($user3->id, $contact->contactid);

        $this->assertCount(1, $contactrequests);
        $contactrequest = reset($contactrequests);
        $this->assertEquals($user2->id, $contactrequest->userid);
        $this->assertEquals($user4->id, $contactrequest->requesteduserid);

        $this->assertCount(1, $blockedusers);
        $blockeduser = reset($blockedusers);
        $this->assertEquals($user2->id, $blockeduser->userid);
        $this->assertEquals($user6->id, $blockeduser->blockeduserid);

        $this->assertCount(1, $messages);
        $message = reset($messages);
        $this->assertEquals($m2, $message->id);

        $this->assertCount(0, $muas);

        $this->assertCount(6, $mcms);
        $members = array_map(function($member) {
            return $member->userid;
        }, $mcms);
        $this->assertContains($user2->id, $members);

        $this->assertCount(2, $notifications);
        ksort($notifications);

        $notification = array_pop($notifications);
        $this->assertEquals($user2->id, $notification->useridfrom);
        $this->assertEquals($user3->id, $notification->useridto);
    }

    /**
     * Test for provider::get_users_in_context() when there is no message or notification.
     */
    public function test_get_users_in_context_no_data() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $usercontext = context_user::instance($user->id);
        $this->remove_user_self_conversation($user->id);

        $userlist = new \core_privacy\local\request\userlist($usercontext, 'core_message');
        \core_message\privacy\provider::get_users_in_context($userlist);

        $this->assertEmpty($userlist->get_userids());
    }

    /**
     * Test for provider::get_users_in_context() when there is a message between users.
     */
    public function test_get_users_in_context_with_message() {
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $user1context = context_user::instance($user1->id);
        $user2context = context_user::instance($user2->id);

        // Delete user self-conversations.
        $this->remove_user_self_conversation($user1->id);
        $this->remove_user_self_conversation($user2->id);

        // Test nothing is found before message is sent.
        $userlist = new \core_privacy\local\request\userlist($user1context, 'core_message');
        \core_message\privacy\provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);
        $userlist = new \core_privacy\local\request\userlist($user2context, 'core_message');
        \core_message\privacy\provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);

        $this->create_message($user1->id, $user2->id, time() - (9 * DAYSECS));

        // Test for the sender.
        $userlist = new \core_privacy\local\request\userlist($user1context, 'core_message');
        \core_message\privacy\provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $userincontext = $userlist->current();
        $this->assertEquals($user1->id, $userincontext->id);

        // Test for the receiver.
        $userlist = new \core_privacy\local\request\userlist($user2context, 'core_message');
        \core_message\privacy\provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $userincontext = $userlist->current();
        $this->assertEquals($user2->id, $userincontext->id);
    }

    /**
     * Test for provider::get_users_in_context() when there is a notification between users.
     */
    public function test_get_users_in_context_with_notification() {
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $user1context = context_user::instance($user1->id);
        $user2context = context_user::instance($user2->id);

        // Delete user self-conversations.
        $this->remove_user_self_conversation($user1->id);
        $this->remove_user_self_conversation($user2->id);

        // Test nothing is found before notification is created.
        $userlist = new \core_privacy\local\request\userlist($user1context, 'core_message');
        \core_message\privacy\provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);
        $userlist = new \core_privacy\local\request\userlist($user2context, 'core_message');
        \core_message\privacy\provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);

        $this->create_notification($user1->id, $user2->id, time() - (9 * DAYSECS));

        // Test for the sender.
        $userlist = new \core_privacy\local\request\userlist($user1context, 'core_message');
        \core_message\privacy\provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $userincontext = $userlist->current();
        $this->assertEquals($user1->id, $userincontext->id);

        // Test for the receiver.
        $userlist = new \core_privacy\local\request\userlist($user2context, 'core_message');
        \core_message\privacy\provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $userincontext = $userlist->current();
        $this->assertEquals($user2->id, $userincontext->id);
    }

    /**
     * Test for provider::get_users_in_context() when a users has a contact.
     */
    public function test_get_users_in_context_with_contact() {
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $user1context = context_user::instance($user1->id);
        $user2context = context_user::instance($user2->id);

        // Delete user self-conversations.
        $this->remove_user_self_conversation($user1->id);
        $this->remove_user_self_conversation($user2->id);

        // Test nothing is found before contact is created.
        $userlist = new \core_privacy\local\request\userlist($user1context, 'core_message');
        \core_message\privacy\provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);
        $userlist = new \core_privacy\local\request\userlist($user2context, 'core_message');
        \core_message\privacy\provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);

        \core_message\api::add_contact($user1->id, $user2->id);

        // Test for the user adding the contact.
        $userlist = new \core_privacy\local\request\userlist($user1context, 'core_message');
        \core_message\privacy\provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $userincontext = $userlist->current();
        $this->assertEquals($user1->id, $userincontext->id);

        // Test for the user who is the contact.
        $userlist = new \core_privacy\local\request\userlist($user2context, 'core_message');
        \core_message\privacy\provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $userincontext = $userlist->current();
        $this->assertEquals($user2->id, $userincontext->id);
    }

    /**
     * Test for provider::get_users_in_context() when a user makes a contact request.
     */
    public function test_get_users_in_context_with_contact_request() {
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $user1context = context_user::instance($user1->id);
        $user2context = context_user::instance($user2->id);

        // Delete user self-conversations.
        $this->remove_user_self_conversation($user1->id);
        $this->remove_user_self_conversation($user2->id);

        // Test nothing is found before request is created.
        $userlist = new \core_privacy\local\request\userlist($user1context, 'core_message');
        \core_message\privacy\provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);
        $userlist = new \core_privacy\local\request\userlist($user2context, 'core_message');
        \core_message\privacy\provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);

        \core_message\api::create_contact_request($user1->id, $user2->id);

        // Test for the user requesting the contact.
        $userlist = new \core_privacy\local\request\userlist($user1context, 'core_message');
        \core_message\privacy\provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $userincontext = $userlist->current();
        $this->assertEquals($user1->id, $userincontext->id);

        // Test for the user receiving the contact request.
        $userlist = new \core_privacy\local\request\userlist($user2context, 'core_message');
        \core_message\privacy\provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $userincontext = $userlist->current();
        $this->assertEquals($user2->id, $userincontext->id);
    }

    /**
     * Test for provider::get_users_in_context() when a user is blocked.
     */
    public function test_get_users_in_context_with_blocked_contact() {
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $user1context = context_user::instance($user1->id);
        $user2context = context_user::instance($user2->id);

        // Delete user self-conversations.
        $this->remove_user_self_conversation($user1->id);
        $this->remove_user_self_conversation($user2->id);

        // Test nothing is found before user is blocked.
        $userlist = new \core_privacy\local\request\userlist($user1context, 'core_message');
        \core_message\privacy\provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);
        $userlist = new \core_privacy\local\request\userlist($user2context, 'core_message');
        \core_message\privacy\provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);

        \core_message\api::block_user($user1->id, $user2->id);

        // Test for the blocking user.
        $userlist = new \core_privacy\local\request\userlist($user1context, 'core_message');
        \core_message\privacy\provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $userincontext = $userlist->current();
        $this->assertEquals($user1->id, $userincontext->id);

        // Test for the user who is blocked.
        $userlist = new \core_privacy\local\request\userlist($user2context, 'core_message');
        \core_message\privacy\provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $userincontext = $userlist->current();
        $this->assertEquals($user2->id, $userincontext->id);
    }

    /**
     * Test for provider::delete_data_for_users().
     */
    public function test_delete_data_for_users() {
        global $DB;

        $this->resetAfterTest();

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();
        $user5 = $this->getDataGenerator()->create_user();
        $user6 = $this->getDataGenerator()->create_user();

        $now = time();
        $timeread = $now - DAYSECS;

        // Create contacts.
        \core_message\api::add_contact($user1->id, $user2->id);
        \core_message\api::add_contact($user2->id, $user3->id);

        // Create contact requests.
        \core_message\api::create_contact_request($user1->id, $user3->id);
        \core_message\api::create_contact_request($user2->id, $user4->id);

        // Block users.
        \core_message\api::block_user($user1->id, $user5->id);
        \core_message\api::block_user($user2->id, $user6->id);

        // Create messages.
        $m1 = $this->create_message($user1->id, $user2->id, $now + (9 * DAYSECS), $timeread);
        $m2 = $this->create_message($user2->id, $user1->id, $now + (8 * DAYSECS));

        // Create notifications.
        $n1 = $this->create_notification($user1->id, $user2->id, $now + (9 * DAYSECS), $timeread);
        $n2 = $this->create_notification($user2->id, $user1->id, $now + (8 * DAYSECS));
        $n2 = $this->create_notification($user2->id, $user3->id, $now + (8 * DAYSECS));

        // Delete one of the messages.
        \core_message\api::delete_message($user1->id, $m2);

        // There should be 2 contacts.
        $this->assertEquals(2, $DB->count_records('message_contacts'));

        // There should be 1 contact request.
        $this->assertEquals(2, $DB->count_records('message_contact_requests'));

        // There should be 1 blocked user.
        $this->assertEquals(2, $DB->count_records('message_users_blocked'));

        // There should be two messages.
        $this->assertEquals(2, $DB->count_records('messages'));

        // There should be two user actions - one for reading the message, one for deleting.
        $this->assertEquals(2, $DB->count_records('message_user_actions'));

        // There should be two conversation members + 6 self-conversations.
        $this->assertEquals(8, $DB->count_records('message_conversation_members'));

        // There should be three notifications + two for the contact requests.
        $this->assertEquals(5, $DB->count_records('notifications'));

        $user1context = context_user::instance($user1->id);
        $approveduserlist = new \core_privacy\local\request\approved_userlist($user1context, 'core_message',
                [$user1->id, $user2->id]);
        provider::delete_data_for_users($approveduserlist);

        // Only user1's data should be deleted. User2 should be skipped as user2 is an invalid user for user1context.

        // Confirm the user 2 data still exists.
        $contacts = $DB->get_records('message_contacts');
        $contactrequests = $DB->get_records('message_contact_requests');
        $blockedusers = $DB->get_records('message_users_blocked');
        $messages = $DB->get_records('messages');
        $muas = $DB->get_records('message_user_actions');
        $mcms = $DB->get_records('message_conversation_members');
        $notifications = $DB->get_records('notifications');

        $this->assertCount(1, $contacts);
        $contact = reset($contacts);
        $this->assertEquals($user2->id, $contact->userid);
        $this->assertEquals($user3->id, $contact->contactid);

        $this->assertCount(1, $contactrequests);
        $contactrequest = reset($contactrequests);
        $this->assertEquals($user2->id, $contactrequest->userid);
        $this->assertEquals($user4->id, $contactrequest->requesteduserid);

        $this->assertCount(1, $blockedusers);
        $blockeduser = reset($blockedusers);
        $this->assertEquals($user2->id, $blockeduser->userid);
        $this->assertEquals($user6->id, $blockeduser->blockeduserid);

        $this->assertCount(1, $messages);
        $message = reset($messages);
        $this->assertEquals($m2, $message->id);

        $this->assertCount(0, $muas);

        $this->assertCount(6, $mcms);
        $memberids = array_map(function($convmember) {
                return $convmember->userid;
        }, $mcms);
        $this->assertContains($user2->id, $memberids);

        $this->assertCount(2, $notifications);
        ksort($notifications);

        $notification = array_pop($notifications);
        $this->assertEquals($user2->id, $notification->useridfrom);
        $this->assertEquals($user3->id, $notification->useridto);
    }

    /**
     * Test for provider::add_contexts_for_conversations().
     */
    public function test_add_contexts_for_conversations() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $component = 'core_group';
        $itemtype = 'groups';

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        // Delete user self-conversations.
        $this->remove_user_self_conversation($user1->id);
        $this->remove_user_self_conversation($user2->id);
        $this->remove_user_self_conversation($user3->id);
        $this->remove_user_self_conversation($user4->id);

        // Test nothing is found before group conversations is created or message is sent.
        $contextlist = new contextlist();
        provider::add_contexts_for_conversations($contextlist, $user1->id, $component, $itemtype);
        $this->assertCount(0, $contextlist);
        provider::add_contexts_for_conversations($contextlist, $user2->id, $component, $itemtype);
        $this->assertCount(0, $contextlist);

        // Create courses.
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $coursecontext1 = \context_course::instance($course1->id);
        $coursecontext2 = \context_course::instance($course2->id);

        // Enrol users to courses.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id);

        // Create course groups with messaging enabled.
        $group1a = $this->getDataGenerator()->create_group(array('courseid' => $course1->id, 'enablemessaging' => 1));
        $group2a = $this->getDataGenerator()->create_group(array('courseid' => $course2->id, 'enablemessaging' => 1));

        // Add users to groups.
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user3->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2a->id, 'userid' => $user1->id));

        // Get conversation.
        $conversation1 = \core_message\api::get_conversation_by_area(
            $component,
            $itemtype,
            $group1a->id,
            $coursecontext1->id
        );

        // Send some messages to the group conversation.
        $now = time();
        $m1id = testhelper::send_fake_message_to_conversation($user1, $conversation1->id, 'Message 1', $now + 1);
        $m2id = testhelper::send_fake_message_to_conversation($user1, $conversation1->id, 'Message 2', $now + 2);
        $m3id = testhelper::send_fake_message_to_conversation($user2, $conversation1->id, 'Message 3', $now + 3);

        // Test for user1 (is member of the conversation and has sent a message).
        $contextlist = new contextlist();
        provider::add_contexts_for_conversations($contextlist, $user1->id, $component, $itemtype);
        $this->assertCount(2, $contextlist);
        $this->assertContainsEquals($coursecontext1->id, $contextlist->get_contextids());
        $this->assertContainsEquals($coursecontext2->id, $contextlist->get_contextids());

        // Test for user2 (is member of the conversation and has sent a message).
        $contextlist = new contextlist();
        provider::add_contexts_for_conversations($contextlist, $user2->id, $component, $itemtype);
        $this->assertCount(1, $contextlist);
        $this->assertEquals($coursecontext1, $contextlist->current());

        // Test for user3 (is member of the conversation).
        $contextlist = new contextlist();
        provider::add_contexts_for_conversations($contextlist, $user3->id, $component, $itemtype);
        $this->assertCount(1, $contextlist);
        $this->assertEquals($coursecontext1, $contextlist->current());

        // Test for user4 (doesn't belong to the conversation).
        $contextlist = new contextlist();
        provider::add_contexts_for_conversations($contextlist, $user4->id, $component, $itemtype);
        $this->assertCount(0, $contextlist);
    }

    /**
     * Test for provider::add_conversations_in_context().
     */
    public function test_add_conversations_in_context() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $component = 'core_group';
        $itemtype = 'groups';

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();

        // Create courses.
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $coursecontext1 = \context_course::instance($course1->id);
        $coursecontext2 = \context_course::instance($course2->id);

        // Test nothing is found before group conversations is created or message is sent.
        $userlist1 = new \core_privacy\local\request\userlist($coursecontext1, 'core_message');
        provider::add_conversations_in_context($userlist1, $component, $itemtype);
        $this->assertCount(0, $userlist1);

        // Enrol users to courses.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user4->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id);

        // Create course groups with messaging enabled.
        $group1a = $this->getDataGenerator()->create_group(array('courseid' => $course1->id, 'enablemessaging' => 1));
        $group2a = $this->getDataGenerator()->create_group(array('courseid' => $course2->id, 'enablemessaging' => 1));

        // Add users to groups.
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user3->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2a->id, 'userid' => $user1->id));

        // Get conversation.
        $conversation1 = \core_message\api::get_conversation_by_area(
            $component,
            $itemtype,
            $group1a->id,
            $coursecontext1->id
        );

        // Send some messages to the group conversation.
        $now = time();
        $m1id = testhelper::send_fake_message_to_conversation($user1, $conversation1->id, 'Message 1', $now + 1);
        $m2id = testhelper::send_fake_message_to_conversation($user1, $conversation1->id, 'Message 2', $now + 2);
        $m3id = testhelper::send_fake_message_to_conversation($user2, $conversation1->id, 'Message 3', $now + 3);

        // Test for users with any group conversation in course1.
        provider::add_conversations_in_context($userlist1, $component, $itemtype);
        $this->assertCount(3, $userlist1);
        $this->assertEqualsCanonicalizing([$user1->id, $user2->id, $user3->id], $userlist1->get_userids());

        // Test for users with any group conversation in course2.
        $userlist2 = new \core_privacy\local\request\userlist($coursecontext2, 'core_message');
        provider::add_conversations_in_context($userlist2, $component, $itemtype);
        $this->assertCount(1, $userlist2);
        $this->assertEquals(
                [$user1->id],
                $userlist2->get_userids());
    }

    /**
     * Test for provider::export_conversations().
     */
    public function test_export_conversations() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $now = time();
        $systemcontext = \context_system::instance();

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user1context = \context_user::instance($user1->id);

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $coursecontext1 = \context_course::instance($course1->id);
        $coursecontext2 = \context_course::instance($course2->id);

        // Enrol users to courses.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id);

        // Create course groups with group messaging enabled.
        $group1a = $this->getDataGenerator()->create_group(array('courseid' => $course1->id, 'enablemessaging' => 1));
        $group2a = $this->getDataGenerator()->create_group(array('courseid' => $course2->id, 'enablemessaging' => 1));

        // Add users to groups.
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user3->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2a->id, 'userid' => $user1->id));

        // Send some private messages between user 1 and user 2.
        $pm1id = $this->create_message($user1->id, $user2->id, $now);

        // Get conversation.
        $iconversation1id = \core_message\api::get_conversation_between_users([$user1->id, $user2->id]);
        $component = 'core_group';
        $itemtype = 'groups';
        $conversation1 = \core_message\api::get_conversation_by_area(
            $component,
            $itemtype,
            $group1a->id,
            $coursecontext1->id
        );

        // Make favourite some conversations.
        \core_message\api::set_favourite_conversation($conversation1->id, $user1->id);
        \core_message\api::set_favourite_conversation($iconversation1id, $user2->id);

        // Mute some conversations.
        \core_message\api::mute_conversation($user1->id, $conversation1->id);
        \core_message\api::mute_conversation($user2->id, $iconversation1id);

        // Send some messages to the conversation.
        $m1 = testhelper::send_fake_message_to_conversation($user1, $conversation1->id, 'Message 1', $now + 1);
        $m2 = testhelper::send_fake_message_to_conversation($user1, $conversation1->id, 'Message 2', $now + 2);
        $m3 = testhelper::send_fake_message_to_conversation($user2, $conversation1->id, 'Message 3', $now + 3);

        $dbm1 = $DB->get_record('messages', ['id' => $m1]);
        $dbm2 = $DB->get_record('messages', ['id' => $m2]);
        $dbm3 = $DB->get_record('messages', ['id' => $m3]);

        // Mark as read and delete some messages.
        \core_message\api::mark_message_as_read($user1->id, $dbm3, $now + 5);
        \core_message\api::delete_message($user1->id, $m2);

        // Export all the conversations related to the groups in course1 for user1.
        provider::export_conversations($user1->id, 'core_group', 'groups', $coursecontext1);

        // Check that system context hasn't been exported.
        $writer = writer::with_context($systemcontext);
        $this->assertFalse($writer->has_any_data());

        // Check that course2 context hasn't been exported.
        $writer = writer::with_context($coursecontext2);
        $this->assertFalse($writer->has_any_data());

        // Check that course1 context has been exported for user1 and contains data.
        $writer = writer::with_context($coursecontext1);
        $this->assertTrue($writer->has_any_data());

        // Confirm the messages for conversation1 are correct.
        $messages = (array) $writer->get_data([
            get_string('messages', 'core_message'),
            get_string($conversation1->itemtype, $conversation1->component),
            get_string('privacy:export:conversationprefix', 'core_message') . $conversation1->name
        ]);
        $this->assertCount(3, $messages);

        usort($messages, ['static', 'sort_messages']);
        $m1 = array_shift($messages);
        $m2 = array_shift($messages);
        $m3 = array_shift($messages);

        // Check message 1 is correct.
        $this->assertEquals(get_string('yes'), $m1->issender);
        $this->assertEquals(message_format_message_text($dbm1), $m1->message);
        $this->assertEquals(transform::datetime($now + 1), $m1->timecreated);
        $this->assertEquals('-', $m1->timeread);
        $this->assertArrayNotHasKey('timedeleted', (array) $m1);

        // Check message 2 is correct.
        $this->assertEquals(get_string('yes'), $m2->issender);
        $this->assertEquals(message_format_message_text($dbm2), $m2->message);
        $this->assertEquals(transform::datetime($now + 2), $m2->timecreated);
        $this->assertEquals('-', $m2->timeread);
        $this->assertArrayHasKey('timedeleted', (array) $m2);

        // Check message 3 is correct.
        $this->assertEquals(get_string('no'), $m3->issender);
        $this->assertEquals(message_format_message_text($dbm3), $m3->message);
        $this->assertEquals(transform::datetime($now + 3), $m3->timecreated);
        $this->assertEquals(transform::datetime($now + 5), $m3->timeread);
        $this->assertArrayNotHasKey('timedeleted', (array) $m3);

        // Confirm the muted group conversation is correct.
        $mutedconversations = (array) $writer->get_related_data([
            get_string('messages', 'core_message'),
            get_string($conversation1->itemtype, $conversation1->component),
            get_string('privacy:export:conversationprefix', 'core_message') . $conversation1->name
        ], 'muted');
        $this->assertCount(2, $mutedconversations);
        $this->assertEquals(get_string('yes'), $mutedconversations['muted']);

        // Confirm the favourite group conversation is correct.
        $favourite = (array) $writer->get_related_data([
            get_string('messages', 'core_message'),
            get_string($conversation1->itemtype, $conversation1->component),
            get_string('privacy:export:conversationprefix', 'core_message') . $conversation1->name
        ], 'starred');
        $this->assertCount(4, $favourite);
        $this->assertEquals(get_string('yes'), $favourite['starred']);

        // Reset writer before exporting conversations for user2.
        writer::reset();

        // Export all the conversations related to the groups in course1 for user2.
        provider::export_conversations($user2->id, 'core_group', 'groups', $coursecontext1);

        // Check that system context hasn't been exported.
        $writer = writer::with_context($systemcontext);
        $this->assertFalse($writer->has_any_data());

        // Check that course2 context hasn't been exported.
        $writer = writer::with_context($coursecontext2);
        $this->assertFalse($writer->has_any_data());

        // Check that course1 context has been exported for user2 and contains data.
        $writer = writer::with_context($coursecontext1);
        $this->assertTrue($writer->has_any_data());

        // Confirm the messages for conversation1 are correct.
        $messages = (array) $writer->get_data([
            get_string('messages', 'core_message'),
            get_string($conversation1->itemtype, $conversation1->component),
            get_string('privacy:export:conversationprefix', 'core_message') . $conversation1->name
        ]);
        $this->assertCount(3, $messages);

        usort($messages, ['static', 'sort_messages']);
        $m1 = array_shift($messages);
        $m2 = array_shift($messages);
        $m3 = array_shift($messages);

        // Check message 1 is correct.
        $this->assertEquals(get_string('no'), $m1->issender);
        $this->assertEquals(message_format_message_text($dbm1), $m1->message);
        $this->assertEquals(transform::datetime($now + 1), $m1->timecreated);
        $this->assertEquals('-', $m1->timeread);
        $this->assertArrayNotHasKey('timedeleted', (array) $m1);

        // Check message 2 is correct.
        $this->assertEquals(get_string('no'), $m2->issender);
        $this->assertEquals(message_format_message_text($dbm2), $m2->message);
        $this->assertEquals(transform::datetime($now + 2), $m2->timecreated);
        $this->assertEquals('-', $m2->timeread);
        $this->assertArrayNotHasKey('timedeleted', (array) $m2);

        // Check message 3 is correct.
        $this->assertEquals(get_string('yes'), $m3->issender);
        $this->assertEquals(message_format_message_text($dbm3), $m3->message);
        $this->assertEquals(transform::datetime($now + 3), $m3->timecreated);
        $this->assertEquals('-', $m3->timeread);
        $this->assertArrayNotHasKey('timedeleted', (array) $m3);

        // Confirm the muted group conversation is correct.
        $mutedconversations = (array) $writer->get_related_data([
            get_string('messages', 'core_message'),
            get_string($conversation1->itemtype, $conversation1->component),
            $conversation1->name
        ], 'muted');
        $this->assertCount(0, $mutedconversations);

        // Confirm there are no favourite group conversation for user2.
        $favourite = (array) $writer->get_related_data([
            get_string('messages', 'core_message'),
            get_string($conversation1->itemtype, $conversation1->component),
            $conversation1->name
        ], 'starred');
        $this->assertCount(0, $favourite);
    }

    /**
     * Test for provider::delete_conversations_for_all_users().
     */
    public function test_delete_conversations_for_all_users() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $now = time();
        $timeread = $now - DAYSECS;
        $component = 'core_group';
        $itemtype = 'groups';

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();
        $user5 = $this->getDataGenerator()->create_user();
        $user1context = \context_user::instance($user1->id);

        // Create contacts.
        \core_message\api::add_contact($user1->id, $user2->id);
        \core_message\api::add_contact($user2->id, $user3->id);

        // Create contact requests.
        \core_message\api::create_contact_request($user1->id, $user3->id);
        \core_message\api::create_contact_request($user2->id, $user4->id);

        // Block a user.
        \core_message\api::block_user($user1->id, $user3->id);
        \core_message\api::block_user($user3->id, $user4->id);

        // Create individual messages.
        $im1 = $this->create_message($user1->id, $user2->id, $now + (9 * DAYSECS), true);
        $im2 = $this->create_message($user2->id, $user1->id, $now + (8 * DAYSECS), true);
        $im3 = $this->create_message($user2->id, $user3->id, $now + (7 * DAYSECS));

        // Create notifications.
        $n1 = $this->create_notification($user1->id, $user2->id, $now + (9 * DAYSECS), $timeread);
        $n2 = $this->create_notification($user2->id, $user1->id, $now + (8 * DAYSECS));
        $n3 = $this->create_notification($user2->id, $user3->id, $now + (7 * DAYSECS));

        // Delete one of the messages.
        \core_message\api::delete_message($user1->id, $im2);

        // Create course2.
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $coursecontext1 = \context_course::instance($course1->id);
        $coursecontext2 = \context_course::instance($course2->id);

        // Enrol users to courses.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id);

        // Create course groups with group messaging enabled.
        $group1a = $this->getDataGenerator()->create_group(array('courseid' => $course1->id, 'enablemessaging' => 1));
        $group2a = $this->getDataGenerator()->create_group(array('courseid' => $course2->id, 'enablemessaging' => 1));

        // Add users to groups.
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user3->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2a->id, 'userid' => $user2->id));

        // Get conversations.
        $iconversation1id = \core_message\api::get_conversation_between_users([$user1->id, $user2->id]);
        $conversation1 = \core_message\api::get_conversation_by_area(
            $component,
            $itemtype,
            $group1a->id,
            $coursecontext1->id
        );
        $conversation2 = \core_message\api::get_conversation_by_area(
            $component,
            $itemtype,
            $group2a->id,
            $coursecontext2->id
        );

        // Make favourite some conversations.
        \core_message\api::set_favourite_conversation($iconversation1id, $user1->id);
        \core_message\api::set_favourite_conversation($conversation1->id, $user1->id);
        \core_message\api::set_favourite_conversation($conversation1->id, $user2->id);

        // Send some messages to the conversation.
        $gm1 = testhelper::send_fake_message_to_conversation($user1, $conversation1->id, 'Message 1.1', $now + 1);
        $gm2 = testhelper::send_fake_message_to_conversation($user1, $conversation1->id, 'Message 1.2', $now + 2);
        $gm3 = testhelper::send_fake_message_to_conversation($user2, $conversation1->id, 'Message 1.3', $now + 3);
        $gm4 = testhelper::send_fake_message_to_conversation($user1, $conversation2->id, 'Message 2.1', $now + 4);
        $gm5 = testhelper::send_fake_message_to_conversation($user2, $conversation2->id, 'Message 2.2', $now + 5);

        $dbgm1 = $DB->get_record('messages', ['id' => $gm1]);
        $dbgm2 = $DB->get_record('messages', ['id' => $gm2]);
        $dbgm3 = $DB->get_record('messages', ['id' => $gm3]);
        $dbgm4 = $DB->get_record('messages', ['id' => $gm4]);
        $dbgm5 = $DB->get_record('messages', ['id' => $gm5]);

        // Mark as read one of the conversation messages.
        \core_message\api::mark_message_as_read($user1->id, $dbgm3, $now + 5);

        // Mark some conversations as muted by two users.
        \core_message\api::mute_conversation($user1->id, $iconversation1id);
        \core_message\api::mute_conversation($user1->id, $conversation1->id);
        \core_message\api::mute_conversation($user2->id, $conversation1->id);

        // There should be 2 contacts.
        $this->assertEquals(2, $DB->count_records('message_contacts'));

        // There should be 2 contact requests.
        $this->assertEquals(2, $DB->count_records('message_contact_requests'));

        // There should be 2 blocked users.
        $this->assertEquals(2, $DB->count_records('message_users_blocked'));

        // There should be 8 messages.
        $this->assertEquals(8, $DB->count_records('messages'));

        // There should be 4 user actions - 3 for reading the message, 1 for deleting.
        $this->assertEquals(4, $DB->count_records('message_user_actions'));

        // There should be 3 muted conversations.
        $this->assertEquals(3, $DB->count_records('message_conversation_actions'));

        // There should be 4 conversations - 2 individual + 2 group + 5 self-conversations.
        $this->assertEquals(9, $DB->count_records('message_conversations'));

        // There should be 9 conversation members - (2 + 2) individual + (3 + 2) group + 5 self-conversations.
        $this->assertEquals(14 , $DB->count_records('message_conversation_members'));

        // There should be 5 notifications (3 from create_notification and 2 from create_contact_request).
        $this->assertEquals(5, $DB->count_records('notifications'));

        // There should be 3 favourite conversations + 5 self-conversations.
        $this->assertEquals(8, $DB->count_records('favourite'));

        // Delete conversations for all users in course1.
        provider::delete_conversations_for_all_users($coursecontext1, $component, $itemtype);

        // There should be still 2 contacts.
        $this->assertEquals(2, $DB->count_records('message_contacts'));

        // There should be still 2 contact requests.
        $this->assertEquals(2, $DB->count_records('message_contact_requests'));

        // There should be still 2 blocked users.
        $this->assertEquals(2, $DB->count_records('message_users_blocked'));

        // There should be 1 muted conversation.
        $this->assertEquals(1, $DB->count_records('message_conversation_actions'));

        // There should be 3 notifications.
        $this->assertEquals(5, $DB->count_records('notifications'));

        // There should be 5 messages - 3 individual - 2 group (course2).
        $this->assertEquals(5, $DB->count_records('messages'));
        $messages = $DB->get_records('messages');
        $this->assertArrayHasKey($im1, $messages);
        $this->assertArrayHasKey($im2, $messages);
        $this->assertArrayHasKey($im3, $messages);
        $this->assertArrayHasKey($gm4, $messages);
        $this->assertArrayHasKey($gm5, $messages);

        // There should be 3 user actions - 2 for reading the message, 1 for deleting.
        $this->assertEquals(3, $DB->count_records('message_user_actions'));
        $useractions = $DB->get_records('message_user_actions');
        $useractions = array_map(function($action) {
                return $action->messageid;
        }, $useractions);
        $this->assertNotContains($gm3, $useractions);

        // There should be 3 conversations - 2 individual + 1 group (course2) + 5 self-conversations.
        $this->assertEquals(8, $DB->count_records('message_conversations'));
        $conversations = $DB->get_records('message_conversations');
        $this->assertArrayNotHasKey($conversation1->id, $conversations);

        // There should be 6 conversation members - (2 + 2) individual + 2 group + 5 self-conversations.
        $this->assertEquals(11, $DB->count_records('message_conversation_members'));

        // There should be 1 favourite conversation - the individual one + 5 self-conversations.
        $this->assertEquals(6, $DB->count_records('favourite'));
    }

    /**
     * Test for provider::delete_conversations_for_all_users() in the system context.
     */
    public function test_delete_conversations_for_all_users_systemcontext() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $now = time();
        $timeread = $now - DAYSECS;
        $systemcontext = \context_system::instance();
        $component = 'core_group';
        $itemtype = 'groups';

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();
        $user5 = $this->getDataGenerator()->create_user();

        // Create contacts.
        \core_message\api::add_contact($user1->id, $user2->id);
        \core_message\api::add_contact($user2->id, $user3->id);

        // Create contact requests.
        \core_message\api::create_contact_request($user1->id, $user3->id);
        \core_message\api::create_contact_request($user2->id, $user4->id);

        // Block a user.
        \core_message\api::block_user($user1->id, $user3->id);
        \core_message\api::block_user($user3->id, $user4->id);

        // Create individual messages.
        $im1 = $this->create_message($user1->id, $user2->id, $now + (9 * DAYSECS), true);
        $im2 = $this->create_message($user2->id, $user1->id, $now + (8 * DAYSECS), true);
        $im3 = $this->create_message($user2->id, $user3->id, $now + (7 * DAYSECS));

        // Create notifications.
        $n1 = $this->create_notification($user1->id, $user2->id, $now + (9 * DAYSECS), $timeread);
        $n2 = $this->create_notification($user2->id, $user1->id, $now + (8 * DAYSECS));
        $n3 = $this->create_notification($user2->id, $user3->id, $now + (7 * DAYSECS));

        // Delete one of the messages.
        \core_message\api::delete_message($user1->id, $im2);

        // Create course2.
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $coursecontext1 = \context_course::instance($course1->id);
        $coursecontext2 = \context_course::instance($course2->id);

        // Enrol users to courses.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id);

        // Create course groups with group messaging enabled.
        $group1a = $this->getDataGenerator()->create_group(array('courseid' => $course1->id, 'enablemessaging' => 1));
        $group2a = $this->getDataGenerator()->create_group(array('courseid' => $course2->id, 'enablemessaging' => 1));

        // Add users to groups.
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user3->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2a->id, 'userid' => $user2->id));

        // Get conversations.
        $iconversation1id = \core_message\api::get_conversation_between_users([$user1->id, $user2->id]);
        $conversation1 = \core_message\api::get_conversation_by_area(
            $component,
            $itemtype,
            $group1a->id,
            $coursecontext1->id
        );
        $conversation2 = \core_message\api::get_conversation_by_area(
            $component,
            $itemtype,
            $group2a->id,
            $coursecontext2->id
        );

        // Make favourite some conversations.
        \core_message\api::set_favourite_conversation($iconversation1id, $user1->id);
        \core_message\api::set_favourite_conversation($conversation1->id, $user1->id);
        \core_message\api::set_favourite_conversation($conversation1->id, $user2->id);

        // Send some messages to the conversation.
        $gm1 = testhelper::send_fake_message_to_conversation($user1, $conversation1->id, 'Message 1.1', $now + 1);
        $gm2 = testhelper::send_fake_message_to_conversation($user1, $conversation1->id, 'Message 1.2', $now + 2);
        $gm3 = testhelper::send_fake_message_to_conversation($user2, $conversation1->id, 'Message 1.3', $now + 3);
        $gm4 = testhelper::send_fake_message_to_conversation($user1, $conversation2->id, 'Message 2.1', $now + 4);
        $gm5 = testhelper::send_fake_message_to_conversation($user2, $conversation2->id, 'Message 2.2', $now + 5);

        $dbgm3 = $DB->get_record('messages', ['id' => $gm3]);

        // Mark some conversations as muted by two users.
        \core_message\api::mute_conversation($user1->id, $iconversation1id);
        \core_message\api::mute_conversation($user1->id, $conversation1->id);
        \core_message\api::mute_conversation($user2->id, $conversation1->id);

        // Mark as read one of the conversation messages.
        \core_message\api::mark_message_as_read($user1->id, $dbgm3, $now + 5);

        // There should be 2 contacts.
        $this->assertEquals(2, $DB->count_records('message_contacts'));

        // There should be 2 contact requests.
        $this->assertEquals(2, $DB->count_records('message_contact_requests'));

        // There should be 2 blocked users.
        $this->assertEquals(2, $DB->count_records('message_users_blocked'));

        // There should be 8 messages.
        $this->assertEquals(8, $DB->count_records('messages'));

        // There should be 4 user actions - 3 for reading the message, 1 for deleting.
        $this->assertEquals(4, $DB->count_records('message_user_actions'));

        // There should be 3 muted conversations.
        $this->assertEquals(3, $DB->count_records('message_conversation_actions'));

        // There should be 4 conversations - 2 individual + 2 group + 5 self-conversations.
        $this->assertEquals(9, $DB->count_records('message_conversations'));

        // There should be 9 conversation members - (2 + 2) individual + (3 + 2) group + 5 self-conversations.
        $this->assertEquals(14, $DB->count_records('message_conversation_members'));

        // There should be 5 notifications (3 from create_notification and 2 from create_contact_request).
        $this->assertEquals(5, $DB->count_records('notifications'));

        // There should be 3 favourite conversations + 5 self-conversations.
        $this->assertEquals(8, $DB->count_records('favourite'));

        // Delete group conversations for all users in system context.
        provider::delete_conversations_for_all_users($systemcontext, $component, $itemtype);

        // No conversations should be removed, because they are in the course context.
        $this->assertEquals(2, $DB->count_records('message_contacts'));
        $this->assertEquals(2, $DB->count_records('message_contact_requests'));
        $this->assertEquals(2, $DB->count_records('message_users_blocked'));
        $this->assertEquals(8, $DB->count_records('messages'));
        $this->assertEquals(4, $DB->count_records('message_user_actions'));
        $this->assertEquals(3, $DB->count_records('message_conversation_actions'));
        $this->assertEquals(9, $DB->count_records('message_conversations'));
        $this->assertEquals(14, $DB->count_records('message_conversation_members'));
        $this->assertEquals(5, $DB->count_records('notifications'));
        $this->assertEquals(8, $DB->count_records('favourite'));

        // Delete individual conversations for all users in system context.
        provider::delete_conversations_for_all_users($systemcontext, '', '');

        // No conversations should be removed, because they've been moved to user context.
        $this->assertEquals(2, $DB->count_records('message_contacts'));
        $this->assertEquals(2, $DB->count_records('message_contact_requests'));
        $this->assertEquals(2, $DB->count_records('message_users_blocked'));
        $this->assertEquals(8, $DB->count_records('messages'));
        $this->assertEquals(4, $DB->count_records('message_user_actions'));
        $this->assertEquals(3, $DB->count_records('message_conversation_actions'));
        $this->assertEquals(9, $DB->count_records('message_conversations'));
        $this->assertEquals(14, $DB->count_records('message_conversation_members'));
        $this->assertEquals(5, $DB->count_records('notifications'));
        $this->assertEquals(8, $DB->count_records('favourite'));
    }

    /**
     * Test for provider::delete_conversations_for_all_users() in the user context.
     */
    public function test_delete_conversations_for_all_users_usercontext() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $now = time();
        $timeread = $now - DAYSECS;
        $component = 'core_group';
        $itemtype = 'groups';

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();
        $user5 = $this->getDataGenerator()->create_user();
        $user1context = \context_user::instance($user1->id);

        // Create contacts.
        \core_message\api::add_contact($user1->id, $user2->id);
        \core_message\api::add_contact($user2->id, $user3->id);

        // Create contact requests.
        \core_message\api::create_contact_request($user1->id, $user3->id);
        \core_message\api::create_contact_request($user2->id, $user4->id);

        // Block a user.
        \core_message\api::block_user($user1->id, $user3->id);
        \core_message\api::block_user($user3->id, $user4->id);

        // Create individual messages.
        $im1 = $this->create_message($user1->id, $user2->id, $now + (9 * DAYSECS), true);
        $im2 = $this->create_message($user2->id, $user1->id, $now + (8 * DAYSECS), true);
        $im3 = $this->create_message($user2->id, $user3->id, $now + (7 * DAYSECS));

        // Create notifications.
        $n1 = $this->create_notification($user1->id, $user2->id, $now + (9 * DAYSECS), $timeread);
        $n2 = $this->create_notification($user2->id, $user1->id, $now + (8 * DAYSECS));
        $n3 = $this->create_notification($user2->id, $user3->id, $now + (7 * DAYSECS));

        // Delete one of the messages.
        \core_message\api::delete_message($user1->id, $im2);

        // Create course2.
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $coursecontext1 = \context_course::instance($course1->id);
        $coursecontext2 = \context_course::instance($course2->id);

        // Enrol users to courses.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course2->id);

        // Create course groups with group messaging enabled.
        $group1a = $this->getDataGenerator()->create_group(array('courseid' => $course1->id, 'enablemessaging' => 1));
        $group2a = $this->getDataGenerator()->create_group(array('courseid' => $course2->id, 'enablemessaging' => 1));

        // Add users to groups.
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user3->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group2a->id, 'userid' => $user2->id));

        // Get conversation.
        $iconversation1id = \core_message\api::get_conversation_between_users([$user1->id, $user2->id]);
        $iconversation2id = \core_message\api::get_conversation_between_users([$user2->id, $user3->id]);
        $conversation1 = \core_message\api::get_conversation_by_area(
            $component,
            $itemtype,
            $group1a->id,
            $coursecontext1->id
        );
        $conversation2 = \core_message\api::get_conversation_by_area(
            $component,
            $itemtype,
            $group2a->id,
            $coursecontext2->id
        );

        // Make favourite some conversations.
        \core_message\api::set_favourite_conversation($iconversation1id, $user1->id);
        \core_message\api::set_favourite_conversation($conversation1->id, $user1->id);
        \core_message\api::set_favourite_conversation($conversation1->id, $user2->id);

        // Send some messages to the conversation.
        $gm1 = testhelper::send_fake_message_to_conversation($user1, $conversation1->id, 'Message 1.1', $now + 1);
        $gm2 = testhelper::send_fake_message_to_conversation($user1, $conversation1->id, 'Message 1.2', $now + 2);
        $gm3 = testhelper::send_fake_message_to_conversation($user2, $conversation1->id, 'Message 1.3', $now + 3);
        $gm4 = testhelper::send_fake_message_to_conversation($user1, $conversation2->id, 'Message 2.1', $now + 4);
        $gm5 = testhelper::send_fake_message_to_conversation($user2, $conversation2->id, 'Message 2.2', $now + 5);

        $dbgm3 = $DB->get_record('messages', ['id' => $gm3]);

        // Mark as read one of the conversation messages.
        \core_message\api::mark_message_as_read($user1->id, $dbgm3, $now + 5);

        // Mark some of the conversations as muted by two users.
        \core_message\api::mute_conversation($user1->id, $iconversation1id);
        \core_message\api::mute_conversation($user1->id, $conversation1->id);
        \core_message\api::mute_conversation($user2->id, $conversation1->id);

        // There should be 2 contacts.
        $this->assertEquals(2, $DB->count_records('message_contacts'));

        // There should be 2 contact requests.
        $this->assertEquals(2, $DB->count_records('message_contact_requests'));

        // There should be 2 blocked users.
        $this->assertEquals(2, $DB->count_records('message_users_blocked'));

        // There should be 8 messages - 3 individual + 5 group.
        $this->assertEquals(8, $DB->count_records('messages'));

        // There should be 4 user actions - 3 for reading the message, 1 for deleting.
        $this->assertEquals(4, $DB->count_records('message_user_actions'));

        // There should be 3 muted conversations.
        $this->assertEquals(3, $DB->count_records('message_conversation_actions'));

        // There should be 4 conversations - 2 individual + 2 group + 5 self-conversations.
        $this->assertEquals(9, $DB->count_records('message_conversations'));

        // There should be 9 conversation members - (2 + 2) individual + (3 + 2) group + 5 self-conversations.
        $this->assertEquals(14, $DB->count_records('message_conversation_members'));

        // There should be 5 notifications (3 from create_notification and 2 from create_contact_request).
        $this->assertEquals(5, $DB->count_records('notifications'));

        // There should be 3 favourite conversations + 5 self-conversations.
        $this->assertEquals(8, $DB->count_records('favourite'));

        // Delete group conversations for all users in user context.
        provider::delete_conversations_for_all_users($user1context, $component, $itemtype);

        // No conversations should be removed, because they are in the course context.
        $this->assertEquals(2, $DB->count_records('message_contacts'));
        $this->assertEquals(2, $DB->count_records('message_contact_requests'));
        $this->assertEquals(2, $DB->count_records('message_users_blocked'));
        $this->assertEquals(8, $DB->count_records('messages'));
        $this->assertEquals(4, $DB->count_records('message_user_actions'));
        $this->assertEquals(3, $DB->count_records('message_conversation_actions'));
        $this->assertEquals(9, $DB->count_records('message_conversations'));
        $this->assertEquals(14, $DB->count_records('message_conversation_members'));
        $this->assertEquals(5, $DB->count_records('notifications'));
        $this->assertEquals(8, $DB->count_records('favourite'));

        // Delete individual conversations for all users in user context.
        provider::delete_conversations_for_all_users($user1context, '', '');

        // No conversations should be removed, because they are in the course context.
        $this->assertEquals(2, $DB->count_records('message_contacts'));
        $this->assertEquals(2, $DB->count_records('message_contact_requests'));
        $this->assertEquals(2, $DB->count_records('message_users_blocked'));
        $this->assertEquals(8, $DB->count_records('messages'));
        $this->assertEquals(4, $DB->count_records('message_user_actions'));
        $this->assertEquals(3, $DB->count_records('message_conversation_actions'));
        $this->assertEquals(9, $DB->count_records('message_conversations'));
        $this->assertEquals(14, $DB->count_records('message_conversation_members'));
        $this->assertEquals(5, $DB->count_records('notifications'));
        $this->assertEquals(8, $DB->count_records('favourite'));
    }

    /**
     * Test for provider::delete_conversations_for_user().
     */
    public function test_delete_conversations_for_user() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $now = time();
        $timeread = $now - DAYSECS;
        $systemcontext = \context_system::instance();
        $component = 'core_group';
        $itemtype = 'groups';

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();
        $user5 = $this->getDataGenerator()->create_user();
        $user1context = \context_user::instance($user1->id);

        // Create contacts.
        \core_message\api::add_contact($user1->id, $user2->id);
        \core_message\api::add_contact($user2->id, $user3->id);

        // Create contact requests.
        \core_message\api::create_contact_request($user1->id, $user3->id);
        \core_message\api::create_contact_request($user2->id, $user4->id);

        // Block a user.
        \core_message\api::block_user($user1->id, $user3->id);
        \core_message\api::block_user($user3->id, $user4->id);

        // Create private messages.
        $pm1 = $this->create_message($user1->id, $user2->id, $now + (9 * DAYSECS), true);
        $pm2 = $this->create_message($user2->id, $user1->id, $now + (8 * DAYSECS), true);
        $pm3 = $this->create_message($user2->id, $user3->id, $now + (7 * DAYSECS));

        // Create notifications.
        $n1 = $this->create_notification($user1->id, $user2->id, $now + (9 * DAYSECS), $timeread);
        $n2 = $this->create_notification($user2->id, $user1->id, $now + (8 * DAYSECS));
        $n3 = $this->create_notification($user2->id, $user3->id, $now + (7 * DAYSECS));

        // Delete one of the messages.
        \core_message\api::delete_message($user1->id, $pm2);

        // Create course.
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $coursecontext1 = \context_course::instance($course1->id);
        $coursecontext2 = \context_course::instance($course2->id);

        // Enrol users to courses.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id);

        // Create course groups with group messaging enabled.
        $group1a = $this->getDataGenerator()->create_group(array('courseid' => $course1->id, 'enablemessaging' => 1));

        // Add users to groups.
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user3->id));

        // Get conversation.
        $iconversation1id = \core_message\api::get_conversation_between_users([$user1->id, $user2->id]);
        $conversation1 = \core_message\api::get_conversation_by_area(
            $component,
            $itemtype,
            $group1a->id,
            $coursecontext1->id
        );

        // Make favourite some conversations.
        \core_message\api::set_favourite_conversation($iconversation1id, $user1->id);
        \core_message\api::set_favourite_conversation($conversation1->id, $user1->id);
        \core_message\api::set_favourite_conversation($conversation1->id, $user2->id);

        // Send some messages to the conversation.
        $gm1 = testhelper::send_fake_message_to_conversation($user1, $conversation1->id, 'Message 1', $now + 1);
        $gm2 = testhelper::send_fake_message_to_conversation($user1, $conversation1->id, 'Message 2', $now + 2);
        $gm3 = testhelper::send_fake_message_to_conversation($user2, $conversation1->id, 'Message 3', $now + 3);

        $dbm3 = $DB->get_record('messages', ['id' => $gm3]);

        // Mark as read one of the conversation messages.
        \core_message\api::mark_message_as_read($user1->id, $dbm3, $now + 5);

        // Mark some of the conversations as muted by two users.
        \core_message\api::mute_conversation($user1->id, $iconversation1id);
        \core_message\api::mute_conversation($user1->id, $conversation1->id);
        \core_message\api::mute_conversation($user2->id, $conversation1->id);

        // There should be 2 contacts.
        $this->assertEquals(2, $DB->count_records('message_contacts'));

        // There should be 2 contact requests.
        $this->assertEquals(2, $DB->count_records('message_contact_requests'));

        // There should be 2 blocked users.
        $this->assertEquals(2, $DB->count_records('message_users_blocked'));

        // There should be 5 notifications.
        $this->assertEquals(5, $DB->count_records('notifications'));

        // There should be 6 messages.
        $this->assertEquals(6, $DB->count_records('messages'));

        // There should be 4 user actions - 3 for reading the message, one for deleting.
        $this->assertEquals(4, $DB->count_records('message_user_actions'));

        // There should be 3 users muting a conversation.
        $this->assertEquals(3, $DB->count_records('message_conversation_actions'));

        // There should be 3 conversations - 2 private + 1 group + 5 self-conversations.
        $this->assertEquals(8, $DB->count_records('message_conversations'));

        // There should be 7 conversation members - 2 + 2 private conversations + 3 group conversation + 5 self-conversations.
        $this->assertEquals(12, $DB->count_records('message_conversation_members'));
        $members = $DB->get_records('message_conversation_members', ['conversationid' => $conversation1->id]);
        $members = array_map(function($member) {
                return $member->userid;
        }, $members);
        $this->assertContains($user1->id, $members);

        // There should be three favourite conversations + 5 self-conversations.
        $this->assertEquals(8, $DB->count_records('favourite'));

        // Delete group conversations for user1 in course1 and course2.
        $approvedcontextlist = new \core_privacy\tests\request\approved_contextlist($user1, 'core_message',
                [$coursecontext1->id, $coursecontext2->id]);
        provider::delete_conversations_for_user($approvedcontextlist, $component, $itemtype);

        // There should be still 2 contacts.
        $this->assertEquals(2, $DB->count_records('message_contacts'));

        // There should be still 2 contact requests.
        $this->assertEquals(2, $DB->count_records('message_contact_requests'));

        // There should be still 2 blocked users.
        $this->assertEquals(2, $DB->count_records('message_users_blocked'));

        // There should be 2 muted conversation.
        $this->assertEquals(2, $DB->count_records('message_conversation_actions'));

        // There should be 3 notifications.
        $this->assertEquals(5, $DB->count_records('notifications'));

        // There should be 4 messages - 3 private + 1 group sent by user2.
        $this->assertEquals(4, $DB->count_records('messages'));
        $messages = $DB->get_records('messages');
        $this->assertArrayHasKey($pm1, $messages);
        $this->assertArrayHasKey($pm2, $messages);
        $this->assertArrayHasKey($pm3, $messages);
        $this->assertArrayHasKey($gm3, $messages);

        // There should be 3 user actions - 2 for reading the message, one for deleting.
        $this->assertEquals(3, $DB->count_records('message_user_actions'));
        $useractions = $DB->get_records('message_user_actions');
        $useractions = array_map(function($action) {
                return $action->messageid;
        }, $useractions);
        $this->assertNotContains($gm3, $useractions);

        // There should be still 3 conversations - 2 private + 1 group + 5 self-conversations.
        $this->assertEquals(8, $DB->count_records('message_conversations'));

        // There should be 6 conversation members - 2 + 2 private conversations + 2 group conversation + 5 self-conversations.
        $this->assertEquals(11, $DB->count_records('message_conversation_members'));
        $members = $DB->get_records('message_conversation_members', ['conversationid' => $conversation1->id]);
        $members = array_map(function($member) {
                return $member->userid;
        }, $members);
        $this->assertNotContains($user1->id, $members);

        // Unset favourite self-conversations.
        $this->remove_user_self_conversation($user1->id);
        $this->remove_user_self_conversation($user2->id);
        $this->remove_user_self_conversation($user3->id);
        $this->remove_user_self_conversation($user4->id);
        $this->remove_user_self_conversation($user5->id);

        // There should be 2 favourite conversations - 2 group.
        $this->assertEquals(2, $DB->count_records('favourite'));
        $favourites = $DB->get_records('favourite');
        foreach ($favourites as $favourite) {
            if ($favourite->userid == $user1->id) {
                $this->assertEquals($iconversation1id, $favourite->itemid);
            } else if ($favourite->userid == $user2->id) {
                $this->assertEquals($conversation1->id, $favourite->itemid);
            }
        }
    }


    /**
     * Test for provider::delete_conversations_for_users().
     */
    public function test_delete_conversations_for_users() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();
        $now = time();
        $timeread = $now - DAYSECS;
        $systemcontext = \context_system::instance();
        $component = 'core_group';
        $itemtype = 'groups';

        // Create users to test with.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $user4 = $this->getDataGenerator()->create_user();
        $user5 = $this->getDataGenerator()->create_user();
        $user1context = \context_user::instance($user1->id);

        // Create contacts.
        \core_message\api::add_contact($user1->id, $user2->id);
        \core_message\api::add_contact($user2->id, $user3->id);

        // Create contact requests.
        \core_message\api::create_contact_request($user1->id, $user3->id);
        \core_message\api::create_contact_request($user2->id, $user4->id);

        // Block a user.
        \core_message\api::block_user($user1->id, $user3->id);
        \core_message\api::block_user($user3->id, $user4->id);

        // Create private messages.
        $pm1 = $this->create_message($user1->id, $user2->id, $now + (9 * DAYSECS), true);
        $pm2 = $this->create_message($user2->id, $user1->id, $now + (8 * DAYSECS), true);
        $pm3 = $this->create_message($user2->id, $user3->id, $now + (7 * DAYSECS));

        // Create notifications.
        $n1 = $this->create_notification($user1->id, $user2->id, $now + (9 * DAYSECS), $timeread);
        $n2 = $this->create_notification($user2->id, $user1->id, $now + (8 * DAYSECS));
        $n3 = $this->create_notification($user2->id, $user3->id, $now + (7 * DAYSECS));

        // Delete one of the messages.
        \core_message\api::delete_message($user1->id, $pm2);

        // Create course.
        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $coursecontext1 = \context_course::instance($course1->id);
        $coursecontext2 = \context_course::instance($course2->id);

        // Enrol users to courses.
        $this->getDataGenerator()->enrol_user($user1->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user3->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user4->id, $course1->id);
        $this->getDataGenerator()->enrol_user($user1->id, $course2->id);

        // Create course groups with group messaging enabled.
        $group1a = $this->getDataGenerator()->create_group(array('courseid' => $course1->id, 'enablemessaging' => 1));
        $group2a = $this->getDataGenerator()->create_group(array('courseid' => $course2->id, 'enablemessaging' => 1));

        // Add users to groups.
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user1->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user2->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user3->id));
        $this->getDataGenerator()->create_group_member(array('groupid' => $group1a->id, 'userid' => $user4->id));

        // Get conversation.
        $iconversation1id = \core_message\api::get_conversation_between_users([$user1->id, $user2->id]);
        $conversation1 = \core_message\api::get_conversation_by_area(
            $component,
            $itemtype,
            $group1a->id,
            $coursecontext1->id
        );

        // Make favourite some conversations.
        \core_message\api::set_favourite_conversation($iconversation1id, $user1->id);
        \core_message\api::set_favourite_conversation($conversation1->id, $user1->id);
        \core_message\api::set_favourite_conversation($conversation1->id, $user3->id);

        // Send some messages to the conversation.
        $gm1 = testhelper::send_fake_message_to_conversation($user1, $conversation1->id, 'Message 1', $now + 1);
        $gm2 = testhelper::send_fake_message_to_conversation($user2, $conversation1->id, 'Message 2', $now + 2);
        $gm3 = testhelper::send_fake_message_to_conversation($user3, $conversation1->id, 'Message 3', $now + 3);

        $dbm3 = $DB->get_record('messages', ['id' => $gm3]);

        // Mark as read one of the conversation messages.
        \core_message\api::mark_message_as_read($user1->id, $dbm3, $now + 5);

        // Mark some of the conversations as muted by two users.
        \core_message\api::mute_conversation($user1->id, $iconversation1id);
        \core_message\api::mute_conversation($user1->id, $conversation1->id);
        \core_message\api::mute_conversation($user2->id, $conversation1->id);

        // There should be 2 contacts.
        $this->assertEquals(2, $DB->count_records('message_contacts'));

        // There should be 2 contact requests.
        $this->assertEquals(2, $DB->count_records('message_contact_requests'));

        // There should be 2 blocked users.
        $this->assertEquals(2, $DB->count_records('message_users_blocked'));

        // There should be 5 notifications (3 from create_notification and 2 from create_contact_request).
        $this->assertEquals(5, $DB->count_records('notifications'));

        // There should be 6 messages.
        $this->assertEquals(6, $DB->count_records('messages'));

        // There should be 4 user actions - 3 for reading the message, one for deleting.
        $this->assertEquals(4, $DB->count_records('message_user_actions'));

        // There should be 3 muted conversation.
        $this->assertEquals(3, $DB->count_records('message_conversation_actions'));

        // There should be 3 conversations - 2 private + 2 group + 5 self-conversations.
        $this->assertEquals(9, $DB->count_records('message_conversations'));

        // There should be 8 conversation members - (2 + 2) private + 4 group + 5 self-conversations.
        $this->assertEquals(13, $DB->count_records('message_conversation_members'));
        $members = $DB->get_records('message_conversation_members', ['conversationid' => $conversation1->id]);
        $members = array_map(function($member) {
                return $member->userid;
        }, $members);
        $this->assertContains($user1->id, $members);
        $this->assertContains($user4->id, $members);

        // There should be 3 favourite conversations + 5 self-conversations.
        $this->assertEquals(8, $DB->count_records('favourite'));

        // Delete group conversations for user1 and user2 in course2 context.
        $approveduserlist = new \core_privacy\local\request\approved_userlist($coursecontext2, 'core_message',
                [$user1->id, $user2->id]);
        provider::delete_conversations_for_users($approveduserlist, $component, $itemtype);

        // There should be exactly the same content, because $user1 and $user2 don't belong to any group in course2).
        $this->assertEquals(2, $DB->count_records('message_contacts'));
        $this->assertEquals(2, $DB->count_records('message_contact_requests'));
        $this->assertEquals(2, $DB->count_records('message_users_blocked'));
        $this->assertEquals(5, $DB->count_records('notifications'));
        $this->assertEquals(6, $DB->count_records('messages'));
        $this->assertEquals(4, $DB->count_records('message_user_actions'));
        $this->assertEquals(3, $DB->count_records('message_conversation_actions'));
        $this->assertEquals(9, $DB->count_records('message_conversations'));
        $this->assertEquals(13, $DB->count_records('message_conversation_members'));
        $this->assertEquals(8, $DB->count_records('favourite'));

        // Delete group conversations for user4 in course1 context.
        $approveduserlist = new \core_privacy\local\request\approved_userlist($coursecontext1, 'core_message',
                [$user4->id]);
        provider::delete_conversations_for_users($approveduserlist, $component, $itemtype);

        // There should be the same content except for the members (to remove user4 from the group1 in course1).
        $this->assertEquals(2, $DB->count_records('message_contacts'));
        $this->assertEquals(2, $DB->count_records('message_contact_requests'));
        $this->assertEquals(2, $DB->count_records('message_users_blocked'));
        $this->assertEquals(5, $DB->count_records('notifications'));
        $this->assertEquals(6, $DB->count_records('messages'));
        $this->assertEquals(4, $DB->count_records('message_user_actions'));
        $this->assertEquals(3, $DB->count_records('message_conversation_actions'));
        $this->assertEquals(9, $DB->count_records('message_conversations'));
        $this->assertEquals(8, $DB->count_records('favourite'));
        // There should be 7 conversation members - (2 + 2) private + 3 group + 5 self-conversations.
        $this->assertEquals(12, $DB->count_records('message_conversation_members'));

        // Delete group conversations for user1 and user2 in course1 context.
        $approveduserlist = new \core_privacy\local\request\approved_userlist($coursecontext1, 'core_message',
                [$user1->id, $user2->id]);
        provider::delete_conversations_for_users($approveduserlist, $component, $itemtype);

        // There should be still 2 contacts.
        $this->assertEquals(2, $DB->count_records('message_contacts'));

        // There should be still 2 contact requests.
        $this->assertEquals(2, $DB->count_records('message_contact_requests'));

        // There should be still 2 blocked users.
        $this->assertEquals(2, $DB->count_records('message_users_blocked'));

        // There should be 5 notifications.
        $this->assertEquals(5, $DB->count_records('notifications'));

        // There should be 4 messages - 3 private + 1 group sent by user3.
        $this->assertEquals(4, $DB->count_records('messages'));
        $messages = $DB->get_records('messages');
        $this->assertArrayHasKey($pm1, $messages);
        $this->assertArrayHasKey($pm2, $messages);
        $this->assertArrayHasKey($pm3, $messages);
        $this->assertArrayHasKey($gm3, $messages);

        // There should be 3 user actions - 2 for reading the message, one for deleting.
        $this->assertEquals(3, $DB->count_records('message_user_actions'));
        $useractions = $DB->get_records('message_user_actions');
        $useractions = array_map(function($action) {
                return $action->messageid;
        }, $useractions);
        $this->assertNotContains($gm3, $useractions);

        // There should be 1 muted conversation.
        $this->assertEquals(1, $DB->count_records('message_conversation_actions'));

        // There should be still 4 conversations - 2 private + 2 group + 5 self-conversations.
        $this->assertEquals(9, $DB->count_records('message_conversations'));

        // There should be 5 conversation members - (2 + 2) private + 1 group + 5 self-conversations.
        $this->assertEquals(10, $DB->count_records('message_conversation_members'));
        $members = $DB->get_records('message_conversation_members', ['conversationid' => $conversation1->id]);
        $members = array_map(function($member) {
                return $member->userid;
        }, $members);
        $this->assertNotContains($user1->id, $members);
        $this->assertNotContains($user2->id, $members);

        // Unset favourite self-conversations.
        $this->remove_user_self_conversation($user1->id);
        $this->remove_user_self_conversation($user2->id);
        $this->remove_user_self_conversation($user3->id);
        $this->remove_user_self_conversation($user4->id);
        $this->remove_user_self_conversation($user5->id);

        // There should be 2 favourite conversations - user1 individual + user3 group.
        $this->assertEquals(2, $DB->count_records('favourite'));
        $favourites = $DB->get_records('favourite');
        foreach ($favourites as $favourite) {
            if ($favourite->userid == $user1->id) {
                $this->assertEquals($iconversation1id, $favourite->itemid);
            } else if ($favourite->userid == $user3->id) {
                $this->assertEquals($conversation1->id, $favourite->itemid);
            }
        }
    }

    /**
     * Creates a message to be used for testing.
     *
     * @param int $useridfrom The user id from
     * @param int $useridto The user id to
     * @param int $timecreated
     * @param bool $read Do we want to mark the message as read?
     * @return int The id of the message
     * @throws dml_exception
     */
    private function create_message(int $useridfrom, int $useridto, int $timecreated = null, bool $read = false) {
        global $DB;

        static $i = 1;

        if (is_null($timecreated)) {
            $timecreated = time();
        }

        if (!$conversationid = \core_message\api::get_conversation_between_users([$useridfrom, $useridto])) {
            $conversation = \core_message\api::create_conversation(
                \core_message\api::MESSAGE_CONVERSATION_TYPE_INDIVIDUAL,
                [
                    $useridfrom,
                    $useridto
                ]
            );
            $conversationid = $conversation->id;
        }

        // Ok, send the message.
        $record = new stdClass();
        $record->useridfrom = $useridfrom;
        $record->conversationid = $conversationid;
        $record->subject = 'No subject';
        $record->fullmessage = 'A rad message ' . $i;
        $record->smallmessage = 'A rad message ' . $i;
        $record->timecreated = $timecreated;
        $record->customdata = json_encode(['akey' => 'avalue']);

        $i++;

        $record->id = $DB->insert_record('messages', $record);

        if ($read) {
            \core_message\api::mark_message_as_read($useridto, $record);
        }

        return $record->id;
    }

    /**
     * Creates a notification to be used for testing.
     *
     * @param int $useridfrom The user id from
     * @param int $useridto The user id to
     * @param int|null $timecreated The time the notification was created
     * @param int|null $timeread The time the notification was read, null if it hasn't been.
     * @return int The id of the notification
     * @throws dml_exception
     */
    private function create_notification(int $useridfrom, int $useridto, int $timecreated = null, int $timeread = null) {
        global $DB;

        static $i = 1;

        if (is_null($timecreated)) {
            $timecreated = time();
        }

        $record = new stdClass();
        $record->useridfrom = $useridfrom;
        $record->useridto = $useridto;
        $record->subject = 'No subject';
        $record->fullmessage = 'Some rad notification ' . $i;
        $record->smallmessage = 'Yo homie, you got some stuff to do, yolo. ' . $i;
        $record->timeread = $timeread;
        $record->timecreated = $timecreated;
        $record->customdata = json_encode(['akey' => 'avalue']);

        $i++;

        return $DB->insert_record('notifications', $record);
    }

    /**
     * Comparison function for sorting messages.
     *
     * @param   \stdClass $a
     * @param   \stdClass $b
     * @return  bool
     */
    protected static function sort_messages($a, $b) {
        return strcmp($a->message, $b->message);
    }

    /**
     * Comparison function for sorting contacts.
     *
     * @param   \stdClass $a
     * @param   \stdClass $b
     * @return  bool
     */
    protected static function sort_contacts($a, $b) {
        // Contact attribute contains user id.
        return $a->contact <=> $b->contact;
    }

    /**
     * Function to unset favourite and delete all conversation data for a user's self-conversation.
     *
     * @param int $userid The user id
     * @return  void
     * @throws moodle_exception
     */
    protected static function remove_user_self_conversation(int $userid) {
        $selfconversation = \core_message\api::get_self_conversation($userid);
        \core_message\api::unset_favourite_conversation($selfconversation->id, $userid);
        \core_message\api::delete_all_conversation_data($selfconversation->id);
    }
}
