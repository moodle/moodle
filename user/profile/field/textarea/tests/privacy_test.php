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
 * Base class for unit tests for profilefield_textarea.
 *
 * @package    profilefield_textarea
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \core_privacy\tests\provider_testcase;

/**
 * Unit tests for user\profile\field\textarea\classes\privacy\provider.php
 *
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profilefield_textarea_testcase extends provider_testcase {

    /**
     * Basic setup for these tests.
     */
    public function setUp() {
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
        $profilefieldid = $this->add_profile_field($categoryid, 'textarea');
        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $this->add_user_info_data($user->id, $profilefieldid, 'test data');
        // Get the field that was created.
        $userfielddata = $DB->get_records('user_info_data', array('userid' => $user->id));
        // Confirm we got the right number of user field data.
        $this->assertCount(1, $userfielddata);
        $context = context_user::instance($user->id);
        $contextlist = \profilefield_textarea\privacy\provider::get_contexts_for_userid($user->id);
        $this->assertEquals($context, $contextlist->current());
    }

    /**
     * Test that data is exported correctly for this plugin.
     */
    public function test_export_user_data() {
        // Create profile category.
        $categoryid = $this->add_profile_category();
        // Create textarea profile field.
        $textareaprofilefieldid = $this->add_profile_field($categoryid, 'textarea');
        // Create checkbox profile field.
        $checkboxprofilefieldid = $this->add_profile_field($categoryid, 'checkbox');
        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $context = context_user::instance($user->id);
        // Add textarea user info data.
        $this->add_user_info_data($user->id, $textareaprofilefieldid, 'test textarea');
        // Add checkbox user info data.
        $this->add_user_info_data($user->id, $checkboxprofilefieldid, 'test data');
        $writer = \core_privacy\local\request\writer::with_context($context);
        $this->assertFalse($writer->has_any_data());
        $this->export_context_data_for_user($user->id, $context, 'profilefield_textarea');
        $data = $writer->get_data([get_string('pluginname', 'profilefield_textarea')]);
        $this->assertCount(3, (array) $data);
        $this->assertEquals('Test field', $data->name);
        $this->assertEquals('This is a test.', $data->description);
        $this->assertEquals('test textarea', $data->data);
    }

    /**
     * Test that user data is deleted using the context.
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;
        // Create profile category.
        $categoryid = $this->add_profile_category();
        // Create textarea profile field.
        $textareaprofilefieldid = $this->add_profile_field($categoryid, 'textarea');
        // Create checkbox profile field.
        $checkboxprofilefieldid = $this->add_profile_field($categoryid, 'checkbox');
        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $context = context_user::instance($user->id);
        // Add textarea user info data.
        $this->add_user_info_data($user->id, $textareaprofilefieldid, 'test textarea');
        // Add checkbox user info data.
        $this->add_user_info_data($user->id, $checkboxprofilefieldid, 'test data');
        // Check that we have two entries.
        $userinfodata = $DB->get_records('user_info_data', ['userid' => $user->id]);
        $this->assertCount(2, $userinfodata);
        \profilefield_textarea\privacy\provider::delete_data_for_all_users_in_context($context);
        // Check that the correct profile field has been deleted.
        $userinfodata = $DB->get_records('user_info_data', ['userid' => $user->id]);
        $this->assertCount(1, $userinfodata);
        $this->assertNotEquals('test textarea', reset($userinfodata)->data);
    }

    /**
     * Test that user data is deleted for this user.
     */
    public function test_delete_data_for_user() {
        global $DB;
        // Create profile category.
        $categoryid = $this->add_profile_category();
        // Create textarea profile field.
        $textareaprofilefieldid = $this->add_profile_field($categoryid, 'textarea');
        // Create checkbox profile field.
        $checkboxprofilefieldid = $this->add_profile_field($categoryid, 'checkbox');
        // Create a user.
        $user = $this->getDataGenerator()->create_user();
        $context = context_user::instance($user->id);
        // Add textarea user info data.
        $this->add_user_info_data($user->id, $textareaprofilefieldid, 'test textarea');
        // Add checkbox user info data.
        $this->add_user_info_data($user->id, $checkboxprofilefieldid, 'test data');
        // Check that we have two entries.
        $userinfodata = $DB->get_records('user_info_data', ['userid' => $user->id]);
        $this->assertCount(2, $userinfodata);
        $approvedlist = new \core_privacy\local\request\approved_contextlist($user, 'profilefield_textarea',
            [$context->id]);
        \profilefield_textarea\privacy\provider::delete_data_for_user($approvedlist);
        // Check that the correct profile field has been deleted.
        $userinfodata = $DB->get_records('user_info_data', ['userid' => $user->id]);
        $this->assertCount(1, $userinfodata);
        $this->assertNotEquals('test textarea', reset($userinfodata)->data);
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
        global $DB;
        // Create a new profile category.
        $cat = new stdClass();
        $cat->name = 'Test category';
        $cat->sortorder = 1;

        return $DB->insert_record('user_info_category', $cat);
    }

    /**
     * Add dummy profile field.
     *
     * @param int $categoryid The ID of the profile category
     * @param string $datatype The datatype of the profile field
     * @return int The ID of the profile field
     */
    private function add_profile_field($categoryid, $datatype) {
        global $DB;
        // Create a new profile field.
        $data = new stdClass();
        $data->datatype = $datatype;
        $data->shortname = 'tstField';
        $data->name = 'Test field';
        $data->description = 'This is a test.';
        $data->required = false;
        $data->locked = false;
        $data->forceunique = false;
        $data->signup = false;
        $data->visible = '0';
        $data->categoryid = $categoryid;

        return $DB->insert_record('user_info_field', $data);
    }
}
