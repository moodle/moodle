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
 * Test for user extractor class.
 *
 * @package    auth_iomadsaml2
 * @author     Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright  2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use auth_iomadsaml2\user_extractor;

/**
 * Test for user extractor class.
 *
 * @package    auth_iomadsaml2
 * @author     Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright  2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_iomadsaml2_user_extractor_test extends advanced_testcase {

    /**
     * A helper function to create a custom profile field.
     *
     * @param string $shortname Short name of the field.
     * @param string $datatype Type of the field, e.g. text, checkbox, datetime, menu and etc.
     * @param bool $unique Should the field to be unique?
     *
     * @return \stdClass
     */
    protected function add_user_profile_field(string $shortname, string $datatype, bool $unique = false) : stdClass {
        global $DB;

        // Create a new profile field.
        $data = new \stdClass();
        $data->shortname = $shortname;
        $data->datatype = $datatype;
        $data->name = 'Test ' . $shortname;
        $data->description = 'This is a test field';
        $data->required = false;
        $data->locked = false;
        $data->forceunique = $unique;
        $data->signup = false;
        $data->visible = '0';
        $data->categoryid = '0';

        $DB->insert_record('user_info_field', $data);

        return $data;
    }

    /**
     * Test we can extract users using fields from {user} table.
     */
    public function test_get_user_by_field_from_user_table() {
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $actual = user_extractor::get_user('id', $user1->id);
        $this->assertNotFalse($actual);
        $this->assertSame($user1->id, $actual->id);

        $actual = user_extractor::get_user('username', $user2->username);
        $this->assertNotFalse($actual);
        $this->assertSame($user2->id, $actual->id);

        $actual = user_extractor::get_user('username', 'random string');
        $this->assertFalse($actual);

        $actual = user_extractor::get_user('notexists', $user2->username);
        $this->assertFalse($actual);
    }

    /**
     * Test we can extract users using fields from {user} table when multiple users found.
     */
    public function test_get_user_by_field_from_user_table_when_multiple_users_found() {
        $this->resetAfterTest();

        // Two users with empty idnumber.
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        $actual = user_extractor::get_user('idnumber', '');
        $this->assertFalse($actual);
    }

    /**
     * Test we can extract users using custom profile fields.
     */
    public function test_get_user_by_custom_profile_field() {
        $this->resetAfterTest();

        // Unique fields.
        $field1 = $this->add_user_profile_field('field1', 'text', true);
        $field2 = $this->add_user_profile_field('field2', 'text', true);

        $user1 = $this->getDataGenerator()->create_user();
        profile_save_data((object)['id' => $user1->id, 'profile_field_' . $field1->shortname => 'User 1 Field 1']);
        profile_save_data((object)['id' => $user1->id, 'profile_field_' . $field2->shortname => 'User 1 Field 2']);

        $user2 = $this->getDataGenerator()->create_user();
        profile_save_data((object)['id' => $user2->id, 'profile_field_' . $field1->shortname => 'User 2 Field 1']);
        profile_save_data((object)['id' => $user2->id, 'profile_field_' . $field2->shortname => 'User 2 Field 2']);

        // Should find users.
        $actual = user_extractor::get_user('profile_field_field1', 'User 1 Field 1');
        $this->assertNotFalse($actual);
        $this->assertSame($user1->id, $actual->id);

        $actual = user_extractor::get_user('profile_field_field2', 'User 1 Field 2');
        $this->assertNotFalse($actual);
        $this->assertSame($user1->id, $actual->id);

        $actual = user_extractor::get_user('profile_field_field1', 'User 2 Field 1');
        $this->assertNotFalse($actual);
        $this->assertSame($user2->id, $actual->id);

        $actual = user_extractor::get_user('profile_field_field2', 'User 2 Field 2');
        $this->assertNotFalse($actual);
        $this->assertSame($user2->id, $actual->id);

        // Shouldn't find users.
        $actual = user_extractor::get_user('profile_field_field1', 'User 3 Field 1');
        $this->assertFalse($actual);

        $actual = user_extractor::get_user('profile_field_field3', 'User 3 Field 1');
        $this->assertFalse($actual);

        $actual = user_extractor::get_user('field1', 'User 1 Field 1');
        $this->assertFalse($actual);

        $actual = user_extractor::get_user('random field', 'User 1 Field 1');
        $this->assertFalse($actual);
    }

    /**
     * Tests for case insensitive match on custom user fields.
     */
    public function test_get_user_case_insensitive_custom_fields() {
        global $CFG;
        $this->resetAfterTest();

        // Arrange data.
        $field = $this->add_user_profile_field('vehicleplate', 'text', true);
        $expecteduser = $this->getDataGenerator()->create_user();
        profile_save_data((object)['id' => $expecteduser->id, 'profile_field_vehicleplate' => 'HD4999']);

        // Should match with same case.
        $actualuser = user_extractor::get_user('profile_field_vehicleplate', 'HD4999', true);
        $this->assertNotFalse($actualuser);
        $this->assertSame($expecteduser->id, $actualuser->id);

        // Should match with different case.
        $actualuser = user_extractor::get_user('profile_field_vehicleplate', 'hd4999', true);
        $this->assertNotFalse($actualuser);
        $this->assertSame($expecteduser->id, $actualuser->id);

        // Should not match with different value (obviously).
        $actualuser = user_extractor::get_user('profile_field_vehicleplate', 'Some other value entirely', true);
        $this->assertFalse($actualuser);

        // Should not match when case sensitive.
        $actualuser = user_extractor::get_user('profile_field_vehicleplate', 'hd4999', false);
        $this->assertFalse($actualuser);

        // Should not match by default (case sensitive = false).
        $actualuser = user_extractor::get_user('profile_field_vehicleplate', 'hd4999');
        $this->assertFalse($actualuser);
    }

    /**
     * Tests for case and accent insensitive match on custom user fields.
     */
    public function test_get_user_case_and_accent_insensitive_custom_fields() {
        $this->resetAfterTest();

        // Arrange data.
        $field = $this->add_user_profile_field('vehicleplate', 'text', true);
        $expecteduser = $this->getDataGenerator()->create_user();
        profile_save_data((object)['id' => $expecteduser->id, 'profile_field_vehicleplate' => 'HD4999á']);

        // Should match with same case and accent.
        $actualuser = user_extractor::get_user('profile_field_vehicleplate', 'HD4999á', true, true);
        $this->assertNotFalse($actualuser);
        $this->assertSame($expecteduser->id, $actualuser->id);

        // Should not match with same case and without accent.
        $actualuser = user_extractor::get_user('profile_field_vehicleplate', 'HD4999a', true, true);
        $this->assertFalse($actualuser);

        // Should match with same case and accent.
        $actualuser = user_extractor::get_user('profile_field_vehicleplate', 'HD4999á', true, false);
        $this->assertNotFalse($actualuser);
        $this->assertSame($expecteduser->id, $actualuser->id);

        // Should match with different case and accent.
        $actualuser = user_extractor::get_user('profile_field_vehicleplate', 'hd4999á', true, true);
        $this->assertNotFalse($actualuser);
        $this->assertSame($expecteduser->id, $actualuser->id);

        // Should not match with different case and without accent.
        $actualuser = user_extractor::get_user('profile_field_vehicleplate', 'hd4999a', true, true);
        $this->assertFalse($actualuser);

        // Should not match with different value (obviously).
        $actualuser = user_extractor::get_user('profile_field_vehicleplate', 'Some other value entirely', true, false);
        $this->assertFalse($actualuser);

        // Should not match by default (case sensitive = false).
        $actualuser = user_extractor::get_user('profile_field_vehicleplate', 'hd4999á');
        $this->assertFalse($actualuser);
    }

    /**
     * Tests for case insensitive match on core user fields.
     */
    public function test_get_user_case_insensitive_core_fields() {
        global $CFG, $DB;
        $this->resetAfterTest();

        // Arrange data.
        $expecteduser = $this->getDataGenerator()->create_user(['idnumber' => 'NB256']);

        // Should match with same case.
        $actualuser = user_extractor::get_user('idnumber', 'NB256', true);
        $this->assertNotFalse($actualuser);
        $this->assertSame($expecteduser->id, $actualuser->id);

        // Should match with different case.
        $actualuser = user_extractor::get_user('idnumber', 'nb256', true);
        $this->assertNotFalse($actualuser);
        $this->assertSame($expecteduser->id, $actualuser->id);

        // Should not match with different value (obviously).
        $actualuser = user_extractor::get_user('idnumber', 'Some other value entirely', true);
        $this->assertFalse($actualuser);

        if ($DB->get_dbfamily() !== 'mysql') {
            // Should not match when case sensitive.
            $actualuser = user_extractor::get_user('idnumber', 'nb256', false);
            $this->assertFalse($actualuser);

            // Should not match by default (case sensitive = false).
            $actualuser = user_extractor::get_user('idnumber', 'nb256');
            $this->assertFalse($actualuser);
        }
    }

    /**
     * Tests for case insensitive match on core user fields.
     */
    public function test_get_user_case_and_accent_insensitive_core_fields() {
        global $CFG, $DB;
        $this->resetAfterTest();

        // Arrange data.
        $expecteduser = $this->getDataGenerator()->create_user(['idnumber' => 'NB256á']);

        // Should match with same case and with accent (accentsensitive = true).
        $actualuser = user_extractor::get_user('idnumber', 'NB256á', true);
        $this->assertNotFalse($actualuser);
        $this->assertSame($expecteduser->id, $actualuser->id);

        // Should match with different case and with accent (accentsensitive = true).
        $actualuser = user_extractor::get_user('idnumber', 'nb256á', true);
        $this->assertNotFalse($actualuser);
        $this->assertSame($expecteduser->id, $actualuser->id);

        // Should not match with different case and accent  (accentsensitive = true).
        $actualuser = user_extractor::get_user('idnumber', 'nb256á', false);
        $this->assertFalse($actualuser);

        // Should not match with different value (obviously).
        $actualuser = user_extractor::get_user('idnumber', 'Some other value entirely', true);
        $this->assertFalse($actualuser);

        if ($DB->get_dbfamily() === 'mysql') {
            // Should match with different case and without accent.
            $actualuser = user_extractor::get_user('idnumber', 'nb256a', true, false);
            $this->assertNotFalse($actualuser);
            $this->assertSame($expecteduser->id, $actualuser->id);
        }

        if ($DB->get_dbfamily() !== 'mysql') {
            // Should not match when case sensitive.
            $actualuser = user_extractor::get_user('idnumber', 'nb256á', false);
            $this->assertFalse($actualuser);

            // Should not match by default (case sensitive = false).
            $actualuser = user_extractor::get_user('idnumber', 'nb256á');
            $this->assertFalse($actualuser);

            // Should not match with different case and without accent.
            $actualuser = user_extractor::get_user('idnumber', 'nb256a', true, false);
            $this->assertFalse($actualuser);
        }
    }

    /**
     * Test we can extract users using custom profile fields when found multiple users.
     */
    public function test_get_user_by_custom_profile_field_when_multiple_users_found() {
        $this->resetAfterTest();

        // Non unique fields.
        $field1 = $this->add_user_profile_field('field1', 'text');
        $field2 = $this->add_user_profile_field('field2', 'text');

        $user1 = $this->getDataGenerator()->create_user();
        profile_save_data((object)['id' => $user1->id, 'profile_field_' . $field1->shortname => 'User 1 Field 1']);
        profile_save_data((object)['id' => $user1->id, 'profile_field_' . $field2->shortname => 'User 1 Field 2']);

        $user2 = $this->getDataGenerator()->create_user();
        profile_save_data((object)['id' => $user2->id, 'profile_field_' . $field1->shortname => 'User 1 Field 1']);
        profile_save_data((object)['id' => $user2->id, 'profile_field_' . $field2->shortname => 'User 1 Field 2']);

        $actual = user_extractor::get_user('profile_field_field1', 'User 1 Field 1');
        $this->assertFalse($actual);

        $actual = user_extractor::get_user('profile_field_field2', 'User 1 Field 2');
        $this->assertFalse($actual);
    }

}
