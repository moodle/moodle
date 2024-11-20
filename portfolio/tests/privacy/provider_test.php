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
 * @package    core_portfolio
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_portfolio\privacy;

defined('MOODLE_INTERNAL') || die();

use core_portfolio\privacy\provider;
use core_privacy\local\request\approved_userlist;

/**
 * Privacy provider tests class.
 *
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends \core_privacy\tests\provider_testcase {

    protected function create_portfolio_data($plugin, $name, $user, $preference, $value) {
        global $DB;
        $portfolioinstance = (object) [
            'plugin' => $plugin,
            'name' => $name,
            'visible' => 1
        ];
        $portfolioinstance->id = $DB->insert_record('portfolio_instance', $portfolioinstance);
        $userinstance = (object) [
            'instance' => $portfolioinstance->id,
            'userid' => $user->id,
            'name' => $preference,
            'value' => $value
        ];
        $DB->insert_record('portfolio_instance_user', $userinstance);

        $DB->insert_record('portfolio_log', [
            'portfolio' => $portfolioinstance->id,
            'userid' => $user->id,
            'caller_class' => 'forum_portfolio_caller',
            'caller_component' => 'mod_forum',
            'time' => time(),
        ]);

        $DB->insert_record('portfolio_log', [
            'portfolio' => $portfolioinstance->id,
            'userid' => $user->id,
            'caller_class' => 'workshop_portfolio_caller',
            'caller_component' => 'mod_workshop',
            'time' => time(),
        ]);
    }

    /**
     *  Verify that a collection of metadata is returned for this component and that it just returns the righ types for 'portfolio'.
     */
    public function test_get_metadata(): void {
        $collection = new \core_privacy\local\metadata\collection('core_portfolio');
        $collection = provider::get_metadata($collection);
        $this->assertNotEmpty($collection);
        $items = $collection->get_collection();
        $this->assertEquals(4, count($items));
        $this->assertInstanceOf(\core_privacy\local\metadata\types\database_table::class, $items[0]);
        $this->assertInstanceOf(\core_privacy\local\metadata\types\database_table::class, $items[1]);
        $this->assertInstanceOf(\core_privacy\local\metadata\types\database_table::class, $items[2]);
        $this->assertInstanceOf(\core_privacy\local\metadata\types\plugintype_link::class, $items[3]);
    }

    /**
     * Test that the export for a user id returns a user context.
     */
    public function test_get_contexts_for_userid(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $this->create_portfolio_data('googledocs', 'Google Docs', $user, 'visible', 1);
        $contextlist = provider::get_contexts_for_userid($user->id);
        $this->assertEquals($context->id, $contextlist->current()->id);
    }

    /**
     * Test that exporting user data works as expected.
     */
    public function test_export_user_data(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $context = \context_user::instance($user->id);
        $this->create_portfolio_data('googledocs', 'Google Docs', $user, 'visible', 1);
        $contextlist = new \core_privacy\local\request\approved_contextlist($user, 'core_portfolio', [$context->id]);
        provider::export_user_data($contextlist);
        $writer = \core_privacy\local\request\writer::with_context($context);
        $portfoliodata = $writer->get_data([get_string('privacy:path', 'portfolio')]);
        $this->assertEquals('Google Docs', $portfoliodata->{'Google Docs'}->name);
    }

    /**
     * Test that deleting only results in the one context being removed.
     */
    public function test_delete_data_for_all_users_in_context(): void {
        global $DB;

        $this->resetAfterTest();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $this->create_portfolio_data('googledocs', 'Google Docs', $user1, 'visible', 1);
        $this->create_portfolio_data('onedrive', 'Microsoft onedrive', $user2, 'visible', 1);
        // Check a system context sent through.
        $systemcontext = \context_system::instance();
        provider::delete_data_for_all_users_in_context($systemcontext);
        $records = $DB->get_records('portfolio_instance_user');
        $this->assertCount(2, $records);
        $this->assertCount(4, $DB->get_records('portfolio_log'));
        $context = \context_user::instance($user1->id);
        provider::delete_data_for_all_users_in_context($context);
        $records = $DB->get_records('portfolio_instance_user');
        // Only one entry should remain for user 2.
        $this->assertCount(1, $records);
        $data = array_shift($records);
        $this->assertEquals($user2->id, $data->userid);
        $this->assertCount(2, $DB->get_records('portfolio_log'));
    }

    /**
     * Test that deleting only results in one user's data being removed.
     */
    public function test_delete_data_for_user(): void {
        global $DB;

        $this->resetAfterTest();
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $this->create_portfolio_data('googledocs', 'Google Docs', $user1, 'visible', 1);
        $this->create_portfolio_data('onedrive', 'Microsoft onedrive', $user2, 'visible', 1);

        $records = $DB->get_records('portfolio_instance_user');
        $this->assertCount(2, $records);
        $this->assertCount(4, $DB->get_records('portfolio_log'));

        $context = \context_user::instance($user1->id);
        $contextlist = new \core_privacy\local\request\approved_contextlist($user1, 'core_portfolio', [$context->id]);
        provider::delete_data_for_user($contextlist);
        $records = $DB->get_records('portfolio_instance_user');
        // Only one entry should remain for user 2.
        $this->assertCount(1, $records);
        $data = array_shift($records);
        $this->assertEquals($user2->id, $data->userid);
        $this->assertCount(2, $DB->get_records('portfolio_log'));
    }

    /**
     * Test that only users with a user context are fetched.
     */
    public function test_get_users_in_context(): void {
        $this->resetAfterTest();

        $component = 'core_portfolio';
        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $usercontext = \context_user::instance($user->id);
        // The list of users should not return anything yet (related data still haven't been created).
        $userlist = new \core_privacy\local\request\userlist($usercontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);

        // Create portfolio data for user.
        $this->create_portfolio_data('googledocs', 'Google Docs', $user,
            'visible', 1);

        // The list of users for user context should return the user.
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $expected = [$user->id];
        $actual = $userlist->get_userids();
        $this->assertEquals($expected, $actual);

        // The list of users for system context should not return any users.
        $systemcontext = \context_system::instance();
        $userlist = new \core_privacy\local\request\userlist($systemcontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);
    }

    /**
     * Test that data for users in approved userlist is deleted.
     */
    public function test_delete_data_for_users(): void {
        $this->resetAfterTest();

        $component = 'core_portfolio';
        // Create user1.
        $user1 = $this->getDataGenerator()->create_user();
        $usercontext1 = \context_user::instance($user1->id);
        // Create user1.
        $user2 = $this->getDataGenerator()->create_user();
        $usercontext2 = \context_user::instance($user2->id);

        // Create portfolio data for user1 and user2.
        $this->create_portfolio_data('googledocs', 'Google Docs', $user1,
            'visible', 1);
        $this->create_portfolio_data('onedrive', 'Microsoft onedrive', $user2,
            'visible', 1);

        // The list of users for usercontext1 should return user1.
        $userlist1 = new \core_privacy\local\request\userlist($usercontext1, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(1, $userlist1);
        $expected = [$user1->id];
        $actual = $userlist1->get_userids();
        $this->assertEquals($expected, $actual);
        // The list of users for usercontext2 should return user2.
        $userlist2 = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);
        $expected = [$user2->id];
        $actual = $userlist2->get_userids();
        $this->assertEquals($expected, $actual);

        // Add userlist1 to the approved user list.
        $approvedlist = new approved_userlist($usercontext1, $component, $userlist1->get_userids());
        // Delete user data using delete_data_for_user for usercontext1.
        provider::delete_data_for_users($approvedlist);
        // Re-fetch users in usercontext1 - The user list should now be empty.
        $userlist1 = new \core_privacy\local\request\userlist($usercontext1, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(0, $userlist1);
        // Re-fetch users in usercontext2 - The user list should not be empty (user2).
        $userlist2 = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist2);
        $this->assertCount(1, $userlist2);

        // User data should be only removed in the user context.
        $systemcontext = \context_system::instance();
        // Add userlist2 to the approved user list in the system context.
        $approvedlist = new approved_userlist($systemcontext, $component, $userlist2->get_userids());
        // Delete user1 data using delete_data_for_user.
        provider::delete_data_for_users($approvedlist);
        // Re-fetch users in usercontext2 - The user list should not be empty (user2).
        $userlist1 = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(1, $userlist1);
    }
}
