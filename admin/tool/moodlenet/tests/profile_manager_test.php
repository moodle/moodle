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

namespace tool_moodlenet;

/**
 * Unit tests for the profile manager
 *
 * @package    tool_moodlenet
 * @category   test
 * @copyright  2020 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profile_manager_test extends \advanced_testcase {

    /**
     * Test that on this site we use the user table to hold moodle net profile information.
     */
    public function test_official_profile_exists(): void {
        $this->assertTrue(\tool_moodlenet\profile_manager::official_profile_exists());
    }

    /**
     * Test a null is returned when the user's mnet profile field is not set.
     */
    public function test_get_moodlenet_user_profile_no_profile_set(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();

        $result = \tool_moodlenet\profile_manager::get_moodlenet_user_profile($user->id);
        $this->assertNull($result);
    }

    /**
     * Test a null is returned when the user's mnet profile field is not set.
     */
    public function test_moodlenet_user_profile_creation_no_profile_set(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage(get_string('invalidmoodlenetprofile', 'tool_moodlenet'));
        $result = new \tool_moodlenet\moodlenet_user_profile("", $user->id);
    }

    /**
     * Test the return of a moodle net profile.
     */
    public function test_get_moodlenet_user_profile(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user(['moodlenetprofile' => '@matt@hq.mnet']);

        $result = \tool_moodlenet\profile_manager::get_moodlenet_user_profile($user->id);
        $this->assertEquals($user->moodlenetprofile, $result->get_profile_name());
    }

    /**
     * Test the creation of a user profile category.
     */
    public function test_create_user_profile_category(): void {
        global $DB;
        $this->resetAfterTest();

        $basecategoryname = get_string('pluginname', 'tool_moodlenet');

        \tool_moodlenet\profile_manager::create_user_profile_category();
        $categoryname = \tool_moodlenet\profile_manager::get_category_name();
        $this->assertEquals($basecategoryname, $categoryname);
        \tool_moodlenet\profile_manager::create_user_profile_category();

        $recordcount = $DB->count_records('user_info_category', ['name' => $basecategoryname]);
        $this->assertEquals(1, $recordcount);

        // Test the duplication of categories to ensure a unique name is always used.
        $categoryname = \tool_moodlenet\profile_manager::get_category_name();
        $this->assertEquals($basecategoryname . 1, $categoryname);
        \tool_moodlenet\profile_manager::create_user_profile_category();
        $categoryname = \tool_moodlenet\profile_manager::get_category_name();
        $this->assertEquals($basecategoryname . 2, $categoryname);
    }

    /**
     * Test the creating of the custom user profile field to hold the moodle net profile.
     */
    public function test_create_user_profile_text_field(): void {
        global $DB;
        $this->resetAfterTest();

        $shortname = 'mnetprofile';

        $categoryid = \tool_moodlenet\profile_manager::create_user_profile_category();
        \tool_moodlenet\profile_manager::create_user_profile_text_field($categoryid);

        $record = $DB->get_record('user_info_field', ['shortname' => $shortname]);
        $this->assertEquals($shortname, $record->shortname);
        $this->assertEquals($categoryid, $record->categoryid);

        // Test for a unique name if 'mnetprofile' is already in use.
        \tool_moodlenet\profile_manager::create_user_profile_text_field($categoryid);
        $profilename = \tool_moodlenet\profile_manager::get_profile_field_name();
        $this->assertEquals($shortname . 1, $profilename);
        \tool_moodlenet\profile_manager::create_user_profile_text_field($categoryid);
        $profilename = \tool_moodlenet\profile_manager::get_profile_field_name();
        $this->assertEquals($shortname . 2, $profilename);
    }

    /**
     * Test that the user moodlenet profile is saved.
     */
    public function test_save_moodlenet_user_profile(): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $profilename = '@matt@hq.mnet';

        $moodlenetprofile = new \tool_moodlenet\moodlenet_user_profile($profilename, $user->id);

        \tool_moodlenet\profile_manager::save_moodlenet_user_profile($moodlenetprofile);

        $userdata = \core_user::get_user($user->id);
        $this->assertEquals($profilename, $userdata->moodlenetprofile);
    }
}
