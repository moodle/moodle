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

namespace core\privacy;

use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\writer;
use core_privacy\tests\provider_testcase;
use core_privacy\local\request\approved_userlist;
use core\moodlenet\share_recorder;

/**
 * Privacy provider tests class.
 *
 * @package    core
 * @category   test
 * @copyright  2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core\privacy\provider
 */
final class provider_test extends provider_testcase {

    /**
     * Check that a user context is returned if there is any user data for this user.
     *
     * @covers ::get_contexts_for_userid
     */
    public function test_get_contexts_for_userid(): void {
        $this->resetAfterTest();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Check that there are no contexts used for the user yet.
        $this->assertEmpty(provider::get_contexts_for_userid($user1->id));

        // Insert a record.
        $this->insert_dummy_moodlenet_share_progress_record($user1->id);
        $this->insert_dummy_shortlink_record($user2->id);

        // Check that we only get back one context for user1.
        $contextlist = provider::get_contexts_for_userid($user1->id);
        $this->assertCount(1, $contextlist);

        // Check that the context returned is the expected one.
        $usercontext = \context_user::instance($user1->id);
        $this->assertEquals($usercontext->id, $contextlist->get_contextids()[0]);

        // Check that we only get back one context for user2.
        $contextlist = provider::get_contexts_for_userid($user2->id);
        $this->assertCount(1, $contextlist);

        // Check that the context returned is the expected one.
        $usercontext = \context_user::instance($user2->id);
        $this->assertEquals($usercontext->id, $contextlist->get_contextids()[0]);
    }

    /**
     * Test that only users within a user context are fetched.
     *
     * @covers ::get_users_in_context
     */
    public function test_get_users_in_context(): void {
        $this->resetAfterTest();

        // Create some users.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $usercontext1 = \context_user::instance($user1->id);
        $usercontext2 = \context_user::instance($user2->id);
        $usercontext3 = \context_user::instance($user3->id);

        // Get userlists and check they are empty for now.
        $userlist1 = new \core_privacy\local\request\userlist($usercontext1, 'core');
        provider::get_users_in_context($userlist1);
        $this->assertCount(0, $userlist1);

        $userlist2 = new \core_privacy\local\request\userlist($usercontext2, 'core');
        provider::get_users_in_context($userlist2);
        $this->assertCount(0, $userlist2);

        $userlist3 = new \core_privacy\local\request\userlist($usercontext3, 'core');
        provider::get_users_in_context($userlist3);
        $this->assertCount(0, $userlist3);

        // Insert records for both users.
        $this->insert_dummy_moodlenet_share_progress_record($user1->id);
        $this->insert_dummy_moodlenet_share_progress_record($user2->id);
        $this->insert_dummy_shortlink_record($user3->id);

        // Check the userlists contain the correct users.
        $userlist1 = new \core_privacy\local\request\userlist($usercontext1, 'core');
        provider::get_users_in_context($userlist1);
        $this->assertCount(1, $userlist1);
        $this->assertEquals($user1->id, $userlist1->get_userids()[0]);

        $userlist2 = new \core_privacy\local\request\userlist($usercontext2, 'core');
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);
        $this->assertEquals($user2->id, $userlist2->get_userids()[0]);

        $userlist3 = new \core_privacy\local\request\userlist($usercontext3, 'core');
        provider::get_users_in_context($userlist3);
        $this->assertCount(1, $userlist3);
        $this->assertEquals($user3->id, $userlist3->get_userids()[0]);
    }

    /**
     * Test that user data is exported correctly.
     *
     * @covers ::export_user_data
     */
    public function test_export_user_data(): void {
        global $DB;
        $this->resetAfterTest();

        // Create some users.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        // Insert a record for each user.
        $this->insert_dummy_moodlenet_share_progress_record($user1->id);
        $this->insert_dummy_moodlenet_share_progress_record($user2->id);
        $this->insert_dummy_shortlink_record($user3->id);

        $subcontexts = [
            get_string('privacy:metadata:moodlenet_share_progress', 'moodle')
        ];

        // Check if user1 has any exported data yet.
        $usercontext1 = \context_user::instance($user1->id);
        $writer = writer::with_context($usercontext1);
        $this->assertFalse($writer->has_any_data());

        // Export user1's data and check the count.
        $approvedlist = new approved_contextlist($user1, 'core', [$usercontext1->id]);
        provider::export_user_data($approvedlist);
        $data = (array)$writer->get_data($subcontexts);
        $this->assertCount(1, $data);

        // Get the inserted data.
        $userdata = $DB->get_record('moodlenet_share_progress', ['userid' => $user1->id]);

        // Check exported data against the inserted data.
        $this->assertEquals($userdata->id, reset($data)->id);
        $this->assertEquals($userdata->type, reset($data)->type);
        $this->assertEquals($userdata->courseid, reset($data)->courseid);
        $this->assertEquals($userdata->cmid, reset($data)->cmid);
        $this->assertEquals($userdata->userid, reset($data)->userid);
        $this->assertEquals($userdata->timecreated, reset($data)->timecreated);
        $this->assertEquals($userdata->resourceurl, reset($data)->resourceurl);
        $this->assertEquals($userdata->status, reset($data)->status);

        $subcontexts = [
            get_string('privacy:metadata:shortlink', 'moodle')
        ];

        // Check if user3 has any exported data yet.
        $usercontext3 = \context_user::instance($user3->id);
        $writer = writer::with_context($usercontext3);
        $this->assertFalse($writer->has_any_data());

        // Export user3's data and check the count.
        $approvedlist = new approved_contextlist($user3, 'core', [$usercontext3->id]);
        provider::export_user_data($approvedlist);
        $data = (array)$writer->get_data($subcontexts);
        $this->assertCount(1, $data);

        // Get the inserted data.
        $userdata = $DB->get_record('shortlink', ['userid' => $user3->id]);

        // Check exported data against the inserted data.
        $this->assertEquals($userdata->id, reset($data)->id);
        $this->assertEquals($userdata->shortcode, reset($data)->shortcode);
        $this->assertEquals($userdata->userid, reset($data)->userid);
        $this->assertEquals($userdata->component, reset($data)->component);
        $this->assertEquals($userdata->linktype, reset($data)->linktype);
        $this->assertEquals($userdata->identifier, reset($data)->identifier);
    }

    /**
     * Test deleting all user data for a specific context.
     *
     * @covers ::delete_data_for_all_users_in_context
     */
    public function test_delete_data_for_all_users_in_context(): void {
        global $DB;
        $this->resetAfterTest();

        // Create some users.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Insert a record for each user.
        $this->insert_dummy_moodlenet_share_progress_record($user1->id);
        $this->insert_dummy_moodlenet_share_progress_record($user2->id);
        $this->insert_dummy_shortlink_record($user1->id);
        $this->insert_dummy_shortlink_record($user2->id);

        // Get all users' data.
        $usersdata = $DB->get_records('moodlenet_share_progress', []);
        $this->assertCount(2, $usersdata);
        $usersdata = $DB->get_records('shortlink', []);
        $this->assertCount(2, $usersdata);

        // Delete everything for a user1 in context.
        $usercontext1 = \context_user::instance($user1->id);
        provider::delete_data_for_all_users_in_context($usercontext1);

        // Check what is remaining belongs to user2.
        $usersdata = $DB->get_records('moodlenet_share_progress', []);
        $this->assertCount(1, $usersdata);
        $this->assertEquals($user2->id, reset($usersdata)->userid);
        $usersdata = $DB->get_records('shortlink', []);
        $this->assertCount(1, $usersdata);
        $this->assertEquals($user2->id, reset($usersdata)->userid);
    }

    /**
     * Test deleting a user's data for a specific context.
     *
     * @covers ::delete_data_for_user
     */
    public function test_delete_data_for_user(): void {
        global $DB;
        $this->resetAfterTest();

        // Create some users.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Insert a record for each user.
        $this->insert_dummy_moodlenet_share_progress_record($user1->id);
        $this->insert_dummy_moodlenet_share_progress_record($user2->id);
        $this->insert_dummy_shortlink_record($user1->id);
        $this->insert_dummy_shortlink_record($user2->id);

        // Get all users' data.
        $usersdata = $DB->get_records('moodlenet_share_progress', []);
        $this->assertCount(2, $usersdata);
        $usersdata = $DB->get_records('shortlink', []);
        $this->assertCount(2, $usersdata);

        // Delete everything for user1.
        $usercontext1 = \context_user::instance($user1->id);
        $approvedlist = new approved_contextlist($user1, 'core', [$usercontext1->id]);
        provider::delete_data_for_user($approvedlist);

        // Check what is remaining belongs to user2.
        $usersdata = $DB->get_records('moodlenet_share_progress', []);
        $this->assertCount(1, $usersdata);
        $this->assertEquals($user2->id, reset($usersdata)->userid);
        $usersdata = $DB->get_records('shortlink', []);
        $this->assertCount(1, $usersdata);
        $this->assertEquals($user2->id, reset($usersdata)->userid);
    }

    /**
     * Test that data for users in an approved userlist is deleted.
     *
     * @covers ::delete_data_for_users
     */
    public function test_delete_data_for_users(): void {
        global $DB;
        $this->resetAfterTest();

        // Create some users.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $usercontext1 = \context_user::instance($user1->id);
        $usercontext2 = \context_user::instance($user2->id);

        // Insert a record for each user.
        $this->insert_dummy_moodlenet_share_progress_record($user1->id);
        $this->insert_dummy_moodlenet_share_progress_record($user2->id);
        $this->insert_dummy_shortlink_record($user1->id);
        $this->insert_dummy_shortlink_record($user2->id);

        // Check the count on all user's data.
        $usersdata = $DB->get_records('moodlenet_share_progress', []);
        $this->assertCount(2, $usersdata);
        $usersdata = $DB->get_records('shortlink', []);
        $this->assertCount(2, $usersdata);

        // Attempt to delete data for user1 using user2's context (should have no effect).
        $approvedlist = new approved_userlist($usercontext2, 'core', [$user1->id]);
        provider::delete_data_for_users($approvedlist);
        $usersdata = $DB->get_records('moodlenet_share_progress', []);
        $this->assertCount(2, $usersdata);
        $usersdata = $DB->get_records('shortlink', []);
        $this->assertCount(2, $usersdata);

        // Delete data for user1 using its correct context.
        $approvedlist = new approved_userlist($usercontext1, 'core', [$user1->id]);
        provider::delete_data_for_users($approvedlist);

        // Check what is remaining belongs to user2.
        $usersdata = $DB->get_records('moodlenet_share_progress', []);
        $this->assertCount(1, $usersdata);
        $this->assertEquals($user2->id, reset($usersdata)->userid);
        $usersdata = $DB->get_records('shortlink', []);
        $this->assertCount(1, $usersdata);
        $this->assertEquals($user2->id, reset($usersdata)->userid);
    }

    /**
     * Helper function to insert a MoodleNet share progress record for use in the tests.
     *
     * @param int $userid The ID of the user to link the record to.
     */
    protected function insert_dummy_moodlenet_share_progress_record(int $userid): void {
        $sharetype = share_recorder::TYPE_ACTIVITY;
        $courseid = 123;
        $cmid = 456;
        share_recorder::insert_share_progress($sharetype, $userid, $courseid, $cmid);
    }

    /**
     * Helper function to insert a shortlink record for use in the tests.
     *
     * @param int $userid The ID of the user to link the record to.
     */
    protected function insert_dummy_shortlink_record(
        int $userid,
    ): void {
        // Mock the handler.
        $handler = $this->createMock(\core\shortlink_handler_interface::class);
        $handler->method('get_valid_linktypes')
            ->willReturn(['view']);
        $handler->method('process_shortlink')
            ->with(
                $this->equalTo('view'),
                $this->equalTo(123),
            )
            ->willReturn(new \core\url('https://example.com'));
        \core\di::set("mod_example\\shortlink_handler", $handler);

        // Create a shortlink for the user.
        $manager = \core\di::get(\core\shortlink::class);
        $manager->create_shortlink('mod_example', 'view', 123, $userid);
    }
}
