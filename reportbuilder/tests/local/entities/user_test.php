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
     * Data provider for {@see test_get_name_fields_select}
     *
     * @return array
     */
    public function get_name_fields_select_provider(): array {
        return [
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

        $fields = user::get_name_fields_select('u');
        $user = $DB->get_record_sql("SELECT {$fields} FROM {user} u WHERE username = :username", ['username' => 'admin']);

        // Ensure we received back all name fields.
        $this->assertEquals($expecteduserfields, array_keys((array) $user));
    }
}
