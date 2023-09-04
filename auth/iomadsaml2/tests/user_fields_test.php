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
 * Test for user fields class.
 *
 * @package    auth_iomadsaml2
 * @author     Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright  2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use auth_iomadsaml2\user_fields;

/**
 * Test for user fields class.
 *
 * @package    auth_iomadsaml2
 * @author     Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright  2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_iomadsaml2_user_fields_test extends advanced_testcase {

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
     * Test class constants.
     */
    public function test_constants() {
        $this->assertSame([
            'username',
            'idnumber',
            'email',
            'alternatename',
        ], user_fields::MATCH_FIELDS_FROM_USER_TABLE);

        $this->assertSame(['text'], user_fields::SUPPORTED_TYPES_OF_PROFILE_FIELDS);
        $this->assertSame('profile_field_', user_fields::PROFILE_FIELD_PREFIX);
    }

    /**
     * Test values for user match fields when no profile fields in the system.
     */
    public function test_get_supported_fields_without_profile_fields() {
        $expected = [
            'username' => 'Username',
            'idnumber' => 'ID number',
            'email' => 'Email address',
            'alternatename' => 'Alternate name'
        ];
        $actual = user_fields::get_supported_fields();
        $this->assertSame($expected, $actual);
    }

    /**
     * Test values for user match fields when there are profile fields in the system.
     */
    public function test_get_supported_fields_with_profile_fields() {
        $this->resetAfterTest();

        // Create bunch of profile fields.
        $this->add_user_profile_field('text1', 'text', true);
        $this->add_user_profile_field('checkbox1', 'checkbox', true);
        $this->add_user_profile_field('checkbox2', 'checkbox');
        $this->add_user_profile_field('text2', 'text', false);
        $this->add_user_profile_field('datetime1', 'datetime');
        $this->add_user_profile_field('menu1', 'menu');
        $this->add_user_profile_field('textarea1', 'textarea');
        $this->add_user_profile_field('text3', 'text', true);

        $userfields = [
            'username' => 'Username',
            'idnumber' => 'ID number',
            'email' => 'Email address',
            'alternatename' => 'Alternate name'
        ];

        $profilefields = [
            'profile_field_text1' => 'Test text1',
            'profile_field_text3' => 'Test text3'
        ];
        $expected = array_merge($userfields, $profilefields);

        $this->assertEquals($expected, user_fields::get_supported_fields());
    }

    /**
     * Test data for self::test_is_custom_profile_field().
     * @return array
     */
    public function is_custom_profile_field_data_provider(): array {
        return [
            ['profile_field_test', true],
            ['profiletest', false],
            ['profile', false],
            ['profile_field_', true],
            ['profile_field_profile_field_', true],
            ['Test', false],
            [0, false],
            [false, false],
        ];
    }

    /**
     * Test that we can find put if the field is a custom profile field.
     *
     * @dataProvider is_custom_profile_field_data_provider
     *
     * @param mixed $value Test value.
     * @param bool $expected Expected value.
     */
    public function test_is_custom_profile_field($value, bool $expected) {
        $this->assertSame($expected, user_fields::is_custom_profile_field($value));
    }

    /**
     * Test data for self::test_get_short_name().
     * @return array
     */
    public function get_short_name_data_provider(): array {
        return [
            ['profile_field_test', 'test'],
            ['profile_field_profile_field_test', 'profile_field_test'],
            ['test', 'test'],
            ['profile_field_', ''],
        ];
    }

    /**
     * Test that we can get field shortname from the profile field name.
     *
     * @dataProvider get_short_name_data_provider
     *
     * @param string $value Test value.
     * @param string $expected Expected value.
     */
    public function test_get_short_name(string $value, string $expected) {
        $this->assertSame($expected, user_fields::get_field_short_name($value));
    }
}
