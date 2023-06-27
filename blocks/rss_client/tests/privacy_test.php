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
 * Base class for unit tests for block_rss_client.
 *
 * @package    block_rss_client
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \core_privacy\tests\provider_testcase;
use \block_rss_client\privacy\provider;
use \core_privacy\local\request\approved_userlist;

/**
 * Unit tests for blocks\rss_client\classes\privacy\provider.php
 *
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_rss_client_testcase extends provider_testcase {

    /**
     * Basic setup for these tests.
     */
    public function setUp(): void {
        $this->resetAfterTest(true);
    }

    /**
     * Test getting the context for the user ID related to this plugin.
     */
    public function test_get_contexts_for_userid() {

        $user = $this->getDataGenerator()->create_user();
        $context = context_user::instance($user->id);

        $this->add_rss_feed($user);

        $contextlist = provider::get_contexts_for_userid($user->id);

        $this->assertEquals($context, $contextlist->current());
    }

    /**
     * Test that data is exported correctly for this plugin.
     */
    public function test_export_user_data() {

        $user = $this->getDataGenerator()->create_user();
        $context = context_user::instance($user->id);

        $this->add_rss_feed($user);
        $this->add_rss_feed($user);

        $writer = \core_privacy\local\request\writer::with_context($context);
        $this->assertFalse($writer->has_any_data());
        $this->export_context_data_for_user($user->id, $context, 'block_rss_client');

        $data = $writer->get_data([get_string('pluginname', 'block_rss_client')]);
        $this->assertCount(2, $data->feeds);
        $feed1 = reset($data->feeds);
        $this->assertEquals('BBC News - World', $feed1->title);
        $this->assertEquals('World News', $feed1->preferredtitle);
        $this->assertEquals('Description: BBC News - World', $feed1->description);
        $this->assertEquals(get_string('no'), $feed1->shared);
        $this->assertEquals('http://feeds.bbci.co.uk/news/world/rss.xml?edition=uk', $feed1->url);
    }

    /**
     * Test that only users within a course context are fetched.
     */
    public function test_get_users_in_context() {
        $component = 'block_rss_client';

        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $usercontext = context_user::instance($user->id);

        $userlist = new \core_privacy\local\request\userlist($usercontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);

        $this->add_rss_feed($user);

        // The list of users within the user context should contain user.
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $expected = [$user->id];
        $actual = $userlist->get_userids();
        $this->assertEquals($expected, $actual);

        // The list of users within the system context should be empty.
        $systemcontext = context_system::instance();
        $userlist2 = new \core_privacy\local\request\userlist($systemcontext, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(0, $userlist2);
    }

    /**
     * Test that data for users in approved userlist is deleted.
     */
    public function test_delete_data_for_users() {
        $component = 'block_rss_client';

        $user1 = $this->getDataGenerator()->create_user();
        $usercontext1 = context_user::instance($user1->id);
        $user2 = $this->getDataGenerator()->create_user();
        $usercontext2 = context_user::instance($user2->id);

        $this->add_rss_feed($user1);
        $this->add_rss_feed($user2);

        $userlist1 = new \core_privacy\local\request\userlist($usercontext1, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(1, $userlist1);
        $expected = [$user1->id];
        $actual = $userlist1->get_userids();
        $this->assertEquals($expected, $actual);

        $userlist2 = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);
        $expected = [$user2->id];
        $actual = $userlist2->get_userids();
        $this->assertEquals($expected, $actual);

        // Convert $userlist1 into an approved_contextlist.
        $approvedlist1 = new approved_userlist($usercontext1, $component, $userlist1->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist1);

        // Re-fetch users in usercontext1.
        $userlist1 = new \core_privacy\local\request\userlist($usercontext1, $component);
        provider::get_users_in_context($userlist1);
        // The user data in usercontext1 should be deleted.
        $this->assertCount(0, $userlist1);

        // Re-fetch users in usercontext2.
        $userlist2 = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist2);
        // The user data in usercontext2 should be still present.
        $this->assertCount(1, $userlist2);

        // Convert $userlist2 into an approved_contextlist in the system context.
        $systemcontext = context_system::instance();
        $approvedlist2 = new approved_userlist($systemcontext, $component, $userlist2->get_userids());
        // Delete using delete_data_for_user.
        provider::delete_data_for_users($approvedlist2);
        // Re-fetch users in usercontext2.
        $userlist2 = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist2);
        // The user data in systemcontext should not be deleted.
        $this->assertCount(1, $userlist2);
    }

    /**
     * Test that user data is deleted using the context.
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $context = context_user::instance($user->id);

        $this->add_rss_feed($user);

        // Check that we have an entry.
        $rssfeeds = $DB->get_records('block_rss_client', ['userid' => $user->id]);
        $this->assertCount(1, $rssfeeds);

        provider::delete_data_for_all_users_in_context($context);

        // Check that it has now been deleted.
        $rssfeeds = $DB->get_records('block_rss_client', ['userid' => $user->id]);
        $this->assertCount(0, $rssfeeds);
    }

    /**
     * Test that user data is deleted for this user.
     */
    public function test_delete_data_for_user() {
        global $DB;

        $user = $this->getDataGenerator()->create_user();
        $context = context_user::instance($user->id);

        $this->add_rss_feed($user);

        // Check that we have an entry.
        $rssfeeds = $DB->get_records('block_rss_client', ['userid' => $user->id]);
        $this->assertCount(1, $rssfeeds);

        $approvedlist = new \core_privacy\local\request\approved_contextlist($user, 'block_rss_feed',
                [$context->id]);
        provider::delete_data_for_user($approvedlist);

        // Check that it has now been deleted.
        $rssfeeds = $DB->get_records('block_rss_client', ['userid' => $user->id]);
        $this->assertCount(0, $rssfeeds);
    }

    /**
     * Add dummy rss feed.
     *
     * @param object $user User object
     */
    private function add_rss_feed($user) {
        global $DB;

        $rssfeeddata = array(
            'userid' => $user->id,
            'title' => 'BBC News - World',
            'preferredtitle' => 'World News',
            'description' => 'Description: BBC News - World',
            'shared' => 0,
            'url' => 'http://feeds.bbci.co.uk/news/world/rss.xml?edition=uk',
        );

        $DB->insert_record('block_rss_client', $rssfeeddata);
    }
}
