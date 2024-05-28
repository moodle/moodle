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

namespace profilefield_checkbox;

use advanced_testcase;
use profile_field_checkbox;

/**
 * Unit tests for the field class
 *
 * @package     profilefield_checkbox
 * @covers      \profile_field_checkbox
 * @copyright   2024 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profile_field_checkbox_test extends advanced_testcase {

    /**
     * Load required test libraries
     */
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once("{$CFG->dirroot}/user/profile/lib.php");
        parent::setUpBeforeClass();
    }

    /**
     * Data provider for {@see test_is_empty}
     *
     * @return array[]
     */
    public static function is_empty_provider(): array {
        return [
            'No value' => [
                [],
                true,
            ],
            'Value equals 0' => [
                ['profile_field_check' => 0],
                false,
            ],
            'Value equals 1' => [
                ['profile_field_check' => 1],
                false,
            ],
        ];
    }

    /**
     * Test field empty state
     *
     * @param array $userrecord
     * @param bool $expected
     *
     * @dataProvider is_empty_provider
     */
    public function test_is_empty(array $userrecord, bool $expected): void {
        $this->resetAfterTest();

        $this->getDataGenerator()->create_custom_profile_field([
            'datatype' => 'checkbox',
            'name' => 'My check',
            'shortname' => 'check',
        ]);

        $user = $this->getDataGenerator()->create_user($userrecord);

        /** @var profile_field_checkbox[] $fields */
        $fields = profile_get_user_fields_with_data($user->id);
        $fieldinstance = reset($fields);

        $this->assertEquals($expected, $fieldinstance->is_empty());
    }

    /**
     * Test whether to show field content
     */
    public function test_show_field_content(): void {
        $this->resetAfterTest();

        $this->getDataGenerator()->create_custom_profile_field([
            'datatype' => 'checkbox',
            'name' => 'My check',
            'shortname' => 'check',
            'visible' => PROFILE_VISIBLE_PRIVATE,
        ]);

        // User can view their own value.
        $userwith = $this->getDataGenerator()->create_user(['profile_field_check' => 1]);
        $this->setUser($userwith);

        /** @var profile_field_checkbox[] $fields */
        $fields = profile_get_user_fields_with_data($userwith->id);
        $fieldinstance = reset($fields);

        $this->assertTrue($fieldinstance->show_field_content());

        // Another user cannot view the value.
        $userview = $this->getDataGenerator()->create_user();
        $this->setUser($userview);

        $this->assertFalse($fieldinstance->show_field_content());

        // Another user with appropriate access can view the value.
        $this->setAdminUser();
        $this->assertTrue($fieldinstance->show_field_content());

    }
}
