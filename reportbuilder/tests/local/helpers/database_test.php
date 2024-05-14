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
final class database_test extends advanced_testcase {

    /**
     * Test generating alias
     */
    public function test_generate_alias(): void {
        $this->assertMatchesRegularExpression('/^rbalias(\d+)$/', database::generate_alias());

        // Specify a suffix.
        $this->assertMatchesRegularExpression('/^rbalias(\d+)_$/', database::generate_alias('_'));
    }

    /**
     * Test generating multiple aliases
     */
    public function test_generate_aliases(): void {
        $aliases = database::generate_aliases(3);

        $this->assertCount(3, $aliases);
        [$aliasone, $aliastwo, $aliasthree] = $aliases;

        $this->assertMatchesRegularExpression('/^rbalias(\d+)$/', $aliasone);
        $this->assertMatchesRegularExpression('/^rbalias(\d+)$/', $aliastwo);
        $this->assertMatchesRegularExpression('/^rbalias(\d+)$/', $aliasthree);

        // Ensure they are different.
        $this->assertNotEquals($aliasone, $aliastwo);
        $this->assertNotEquals($aliasone, $aliasthree);
        $this->assertNotEquals($aliastwo, $aliasthree);

        // Specify a suffix.
        [$aliasfour, $aliasfive] = database::generate_aliases(2, '_');
        $this->assertNotEquals($aliasfour, $aliasfive);
        $this->assertMatchesRegularExpression('/^rbalias(\d+)_$/', $aliasfour);
        $this->assertMatchesRegularExpression('/^rbalias(\d+)_$/', $aliasfive);
    }

    /**
     * Test generating parameter name
     */
    public function test_generate_param_name(): void {
        $this->assertMatchesRegularExpression('/^rbparam(\d+)$/', database::generate_param_name());

        // Specify a suffix.
        $this->assertMatchesRegularExpression('/^rbparam(\d+)_$/', database::generate_param_name('_'));
    }

    /**
     * Test generating multiple parameter names
     */
    public function test_generate_param_names(): void {
        $params = database::generate_param_names(3);

        $this->assertCount(3, $params);
        [$paramone, $paramtwo, $paramthree] = $params;

        $this->assertMatchesRegularExpression('/^rbparam(\d+)$/', $paramone);
        $this->assertMatchesRegularExpression('/^rbparam(\d+)$/', $paramtwo);
        $this->assertMatchesRegularExpression('/^rbparam(\d+)$/', $paramthree);

        // Ensure they are different.
        $this->assertNotEquals($paramone, $paramtwo);
        $this->assertNotEquals($paramone, $paramthree);
        $this->assertNotEquals($paramtwo, $paramthree);

        // Specify a suffix.
        [$paramfour, $paramfive] = database::generate_param_names(2, '_');
        $this->assertNotEquals($paramfour, $paramfive);
        $this->assertMatchesRegularExpression('/^rbparam(\d+)_$/', $paramfour);
        $this->assertMatchesRegularExpression('/^rbparam(\d+)_$/', $paramfive);
    }

    /**
     * Test parameter validation
     */
    public function test_validate_params(): void {
        [$paramone, $paramtwo] = database::generate_param_names(2);

        $params = [
            $paramone => 1,
            $paramtwo => 2,
        ];

        $this->assertTrue(database::validate_params($params));
    }

    /**
     * Test parameter validation for invalid parameters
     */
    public function test_validate_params_invalid(): void {
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
     * Generate aliases and parameters and confirm they can be used within a query
     */
    public function test_generated_data_in_query(): void {
        global $DB;

        // Unique aliases.
        [
            $usertablealias,
            $userfieldalias,
        ] = database::generate_aliases(2);

        // Unique parameters.
        [
            $paramuserid,
            $paramuserdeleted,
        ] = database::generate_param_names(2);

        // Simple query to retrieve the admin user.
        $sql = "SELECT {$usertablealias}.id AS {$userfieldalias}
                  FROM {user} {$usertablealias}
                 WHERE {$usertablealias}.id = :{$paramuserid}
                   AND {$usertablealias}.deleted = :{$paramuserdeleted}";

        $admin = core_user::get_user_by_username('admin');

        $params = [
            $paramuserid => $admin->id,
            $paramuserdeleted => 0,
        ];

        $record = $DB->get_record_sql($sql, $params);
        $this->assertEquals($admin->id, $record->{$userfieldalias});
    }

    /**
     * Test replacement of parameter names within SQL statements
     */
    public function test_sql_replace_parameter_names(): void {
        global $DB;

        // Predefine parameter names, to ensure they don't overwrite each other.
        [$param0, $param1, $param10] = ['rbparam0', 'rbparam1', 'rbparam10'];

        $sql = "SELECT :{$param0} AS field0, :{$param1} AS field1, :{$param10} AS field10" . $DB->sql_null_from_clause();
        $sql = database::sql_replace_parameter_names(
            $sql,
            [$param0, $param1, $param10],
            fn(string $param) => "prefix_{$param}",
        );

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

    /**
     * Test replacement of parameter names within query, returning both modified query and parameters
     */
    public function test_sql_replace_parameters(): void {
        global $DB;

        // Predefine parameter names, to ensure they don't overwrite each other.
        [$param0, $param1, $param10] = ['rbparam0', 'rbparam1', 'rbparam10'];

        $sql = "SELECT :{$param0} AS field0, :{$param1} AS field1, :{$param10} AS field10" . $DB->sql_null_from_clause();
        [$sql, $params] = database::sql_replace_parameters(
            $sql,
            [$param0 => 'Zero', $param1 => 'One', $param10 => 'Ten'],
            fn(string $param) => "prefix_{$param}",
        );

        $record = $DB->get_record_sql($sql, $params);

        $this->assertEquals((object) [
            'field0' => 'Zero',
            'field1' => 'One',
            'field10' => 'Ten',
        ], $record);
    }
}
