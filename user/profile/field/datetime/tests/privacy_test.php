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
 * Base class for unit tests for profilefield_datetime.
 *
 * @package    profilefield_datetime
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \core_privacy\tests\provider_testcase;
use profilefield_datetime\privacy\provider;
use core_privacy\local\request\approved_userlist;

/**
 * Unit tests for user\profile\field\datetime\classes\privacy\provider.php
 *
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profilefield_datetime_testcase extends provider_testcase {

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
        global $DB;
        // Create profile category.
        $categoryid = $this->add_profile_category();
        // Create profile field.
        $profilefieldid = $this->add_profile_field($categoryid, 'datetime');
        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $this->add_user_info_data($user->id, $profilefieldid, 'test data');
        // Get the field that was created.
        $userfielddata = $DB->get_records('user_info_data', array('userid' => $user->id));
        // Confirm we got the right number of user field data.
        $this->assertCount(1, $userfielddata);
        $context = context_user::instance($user->id);
        $contextlist = provider::get_contexts_for_userid($user->id);
        $this->assertEquals($context, $contextlist->current());
    }

    /**
     * Test that data is exported correctly for this plugin.
     */
    public function test_export_user_data() {
        // Create profile category.
        $categoryid = $this->add_profile_category();
        // Create datetime profile field.
        $datetimeprofilefieldid = $this->add_profile_field($categoryid, 'datetime');
        // Create checkbox profile field.
        $checkboxprofilefieldid = $this->add_profile_field($categoryid, 'checkbox');
        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $context = context_user::instance($user->id);
        // Add datetime user info data.
        $this->add_user_info_data($user->id, $datetimeprofilefieldid, '1524067200');
        // Add checkbox user info data.
        $this->add_user_info_data($user->id, $checkboxprofilefieldid, 'test data');

        $writer = \core_privacy\local\request\writer::with_context($context);
        $this->assertFalse($writer->has_any_data());
        $this->export_context_data_for_user($user->id, $context, 'profilefield_datetime');
        $data = $writer->get_data([get_string('pluginname', 'profilefield_datetime')]);
        $this->assertCount(3, (array) $data);
        $this->assertEquals('Test field', $data->name);
        $this->assertEquals('This is a test.', $data->description);
        $this->assertEquals('19 April 2018', $data->data);
    }

    /**
     * Test that user data is deleted using the context.
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;
        // Create profile category.
        $categoryid = $this->add_profile_category();
        // Create datetime profile field.
        $datetimeprofilefieldid = $this->add_profile_field($categoryid, 'datetime');
        // Create checkbox profile field.
        $checkboxprofilefieldid = $this->add_profile_field($categoryid, 'checkbox');
        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $context = context_user::instance($user->id);
        // Add datetime user info data.
        $this->add_user_info_data($user->id, $datetimeprofilefieldid, '1524067200');
        // Add checkbox user info data.
        $this->add_user_info_data($user->id, $checkboxprofilefieldid, 'test data');
        // Check that we have two entries.
        $userinfodata = $DB->get_records('user_info_data', ['userid' => $user->id]);
        $this->assertCount(2, $userinfodata);
        provider::delete_data_for_all_users_in_context($context);
        // Check that the correct profile field has been deleted.
        $userinfodata = $DB->get_records('user_info_data', ['userid' => $user->id]);
        $this->assertCount(1, $userinfodata);
        $this->assertNotEquals('1524067200', reset($userinfodata)->data);
    }

    /**
     * Test that user data is deleted for this user.
     */
    public function test_delete_data_for_user() {
        global $DB;
        // Create profile category.
        $categoryid = $this->add_profile_category();
        // Create datetime profile field.
        $datetimeprofilefieldid = $this->add_profile_field($categoryid, 'datetime');
        // Create checkbox profile field.
        $checkboxprofilefieldid = $this->add_profile_field($categoryid, 'checkbox');
        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $context = context_user::instance($user->id);
        // Add datetime user info data.
        $this->add_user_info_data($user->id, $datetimeprofilefieldid, '1524067200');
        // Add checkbox user info data.
        $this->add_user_info_data($user->id, $checkboxprofilefieldid, 'test data');
        // Check that we have two entries.
        $userinfodata = $DB->get_records('user_info_data', ['userid' => $user->id]);
        $this->assertCount(2, $userinfodata);
        $approvedlist = new \core_privacy\local\request\approved_contextlist($user, 'profilefield_datetime',
            [$context->id]);
        provider::delete_data_for_user($approvedlist);
        // Check that the correct profile field has been deleted.
        $userinfodata = $DB->get_records('user_info_data', ['userid' => $user->id]);
        $this->assertCount(1, $userinfodata);
        $this->assertNotEquals('1524067200', reset($userinfodata)->data);
    }

    /**
     * Test that only users with a user context are fetched.
     */
    public function test_get_users_in_context() {
        $this->resetAfterTest();

        $component = 'profilefield_datetime';

        // Create profile category.
        $categoryid = $this->add_profile_category();
        // Create profile field.
        $profilefieldid = $this->add_profile_field($categoryid, 'datetime');

        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $usercontext = context_user::instance($user->id);

        // The list of users should not return anything yet (related data still haven't been created).
        $userlist = new \core_privacy\local\request\userlist($usercontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);

        $this->add_user_info_data($user->id, $profilefieldid, 'test data');

        // The list of users for user context should return the user.
        provider::get_users_in_context($userlist);
        $this->assertCount(1, $userlist);
        $expected = [$user->id];
        $actual = $userlist->get_userids();
        $this->assertEquals($expected, $actual);

        // The list of users for system context should not return any users.
        $systemcontext = context_system::instance();
        $userlist = new \core_privacy\local\request\userlist($systemcontext, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(0, $userlist);
    }

    /**
     * Test that data for users in approved userlist is deleted.
     */
    public function test_delete_data_for_users() {
        $this->resetAfterTest();

        $component = 'profilefield_datetime';
        // Create profile category.
        $categoryid = $this->add_profile_category();
        // Create profile field.
        $profilefieldid = $this->add_profile_field($categoryid, 'datetime');

        // Create user1.
        $user1 = $this->getDataGenerator()->create_user();
        $usercontext1 = context_user::instance($user1->id);

        // Create user2.
        $user2 = $this->getDataGenerator()->create_user();
        $usercontext2 = context_user::instance($user2->id);

        $this->add_user_info_data($user1->id, $profilefieldid, 'test data');
        $this->add_user_info_data($user2->id, $profilefieldid, 'test data');

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
        $systemcontext = context_system::instance();
        // Add userlist2 to the approved user list in the system context.
        $approvedlist = new approved_userlist($systemcontext, $component, $userlist2->get_userids());
        // Delete user1 data using delete_data_for_user.
        provider::delete_data_for_users($approvedlist);
        // Re-fetch users in usercontext2 - The user list should not be empty (user2).
        $userlist1 = new \core_privacy\local\request\userlist($usercontext2, $component);
        provider::get_users_in_context($userlist1);
        $this->assertCount(1, $userlist1);
    }

    /**
     * Add dummy user info data.
     *
     * @param int $userid The ID of the user
     * @param int $fieldid The ID of the field
     * @param string $data The data
     */
    private function add_user_info_data($userid, $fieldid, $data) {
        global $DB;
        $userinfodata = array(
            'userid' => $userid,
            'fieldid' => $fieldid,
            'data' => $data,
            'dataformat' => 0
        );

        $DB->insert_record('user_info_data', $userinfodata);
    }

    /**
     * Add dummy profile category.
     *
     * @return int The ID of the profile category
     */
    private function add_profile_category() {
        $cat = $this->getDataGenerator()->create_custom_profile_field_category(['name' => 'Test category']);
        return $cat->id;
    }

    /**
     * Add dummy profile field.
     *
     * @param int $categoryid The ID of the profile category
     * @param string $datatype The datatype of the profile field
     * @return int The ID of the profile field
     */
    private function add_profile_field($categoryid, $datatype) {
        $data = $this->getDataGenerator()->create_custom_profile_field([
            'datatype' => $datatype,
            'shortname' => 'tstField',
            'name' => 'Test field',
            'description' => 'This is a test.',
            'categoryid' => $categoryid,
        ]);
        return $data->id;
    }
}
