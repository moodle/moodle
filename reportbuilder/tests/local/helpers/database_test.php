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

namespace core_reportbuilder\local\helpers;

use advanced_testcase;
use coding_exception;
use core_user;

/**
 * Unit tests for the database helper class
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\helpers\database
 * @copyright   2020 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class database_test extends advanced_testcase {

    /**
     * Test generating table alias and parameter names
     */
    public function test_generate_alias_params(): void {
        global $DB;

        $admin = core_user::get_user_by_username('admin');

        $usertablealias = database::generate_alias();
        $usertablealiasjoin = database::generate_alias();
        $useridalias = database::generate_alias();

        $paramuserid = database::generate_param_name();
        $paramuserdeleted = database::generate_param_name();

        // Ensure they are different.
        $this->assertNotEquals($usertablealias, $usertablealiasjoin);
        $this->assertNotEquals($paramuserid, $paramuserdeleted);

        $sql = "SELECT {$usertablealias}.id AS {$useridalias}
                  FROM {user} {$usertablealias}
                  JOIN {user} {$usertablealiasjoin} ON {$usertablealiasjoin}.id = {$usertablealias}.id
                 WHERE {$usertablealias}.id = :{$paramuserid} AND {$usertablealias}.deleted = :{$paramuserdeleted}";
        $params = [$paramuserid => $admin->id, $paramuserdeleted => 0];

        $validated = database::validate_params($params);
        $this->assertTrue($validated);

        $record = $DB->get_record_sql($sql, $params);
        $this->assertEquals($admin->id, $record->{$useridalias});
    }

    /**
     * Test parameter validation
     */
    public function test_validate_params(): void {
        $params = [
            database::generate_param_name() => 1,
            'invalidfoo' => 2,
            'invalidbar' => 4,
        ];

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage('Invalid parameter names (invalidfoo, invalidbar)');
        database::validate_params($params);
    }

    /**
     * Test replacement of parameter names within SQL statements
     */
    public function test_sql_replace_parameter_names(): void {
        global $DB;

        // Predefine parameter names, to ensure they don't overwrite each other.
        [$param0, $param1, $param10] = ['rbparam0', 'rbparam1', 'rbparam10'];

        $sql = "SELECT :{$param0} AS field0, :{$param1} AS field1, :{$param10} AS field10" . $DB->sql_null_from_clause();
        $sql = database::sql_replace_parameter_names($sql, [$param0, $param1, $param10], static function(string $param): string {
            return "prefix_{$param}";
        });

        $record = $DB->get_record_sql($sql, [
            "prefix_{$param0}" => 'Zero',
            "prefix_{$param1}" => 'One',
            "prefix_{$param10}" => 'Ten',
        ]);

        $this->assertEquals((object) [
            'field0' => 'Zero',
            'field1' => 'One',
            'field10' => 'Ten',
        ], $record);
    }
}
