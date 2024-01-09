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

declare(strict_types=1);

namespace core_reportbuilder\local\entities;

use advanced_testcase;

/**
 * Unit tests for user entity
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\entities\user
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_test extends advanced_testcase {

    /**
     * Test getting user identity column
     */
    public function test_get_identity_column(): void {
        $this->resetAfterTest();

        $this->getDataGenerator()->create_custom_profile_field(['datatype' => 'text', 'name' => 'Hi', 'shortname' => 'hi']);

        $user = new user();
        $user->initialise();

        $columnusername = $user->get_identity_column('username');
        $this->assertEquals('user:username', $columnusername->get_unique_identifier());

        $columnprofilefield = $user->get_identity_column('profile_field_hi');
        $this->assertEquals('user:profilefield_hi', $columnprofilefield->get_unique_identifier());
    }

    /**
     * Test getting user identity filter
     */
    public function test_get_identity_filter(): void {
        $this->resetAfterTest();

        $this->getDataGenerator()->create_custom_profile_field(['datatype' => 'text', 'name' => 'Hi', 'shortname' => 'hi']);

        $user = new user();
        $user->initialise();

        $filterusername = $user->get_identity_filter('username');
        $this->assertEquals('user:username', $filterusername->get_unique_identifier());

        $filterprofilefield = $user->get_identity_filter('profile_field_hi');
        $this->assertEquals('user:profilefield_hi', $filterprofilefield->get_unique_identifier());
    }

    /**
     * Data provider for {@see test_get_name_fields_select}
     *
     * @return array
     */
    public function get_name_fields_select_provider(): array {
        return [
            ['firstname', ['firstname']],
            ['firstname lastname', ['firstname', 'lastname']],
            ['firstname middlename lastname', ['firstname', 'middlename', 'lastname']],
            ['alternatename lastname firstname', ['alternatename', 'lastname', 'firstname']],
        ];
    }

    /**
     * Tests the helper method for selecting all of a users' name fields
     *
     * @param string $fullnamedisplay
     * @param string[] $expecteduserfields
     *
     * @dataProvider get_name_fields_select_provider
     */
    public function test_get_name_fields_select(string $fullnamedisplay, array $expecteduserfields): void {
        global $DB;

        $this->resetAfterTest(true);

        set_config('alternativefullnameformat', $fullnamedisplay);

        // As a user without permission to view all fields we always get the standard ones.
        $fields = user::get_name_fields_select('u');
        $user = $DB->get_record_sql("SELECT {$fields} FROM {user} u WHERE username = :username", ['username' => 'admin']);
        $this->assertEquals(['firstname', 'lastname'], array_keys((array) $user));

        // As the admin we get all name fields from alternativefullnameformat.
        $this->setAdminUser();
        $fields = user::get_name_fields_select('u');
        $user = $DB->get_record_sql("SELECT {$fields} FROM {user} u WHERE username = :username", ['username' => 'admin']);
        $this->assertEquals($expecteduserfields, array_keys((array) $user));
    }
}
