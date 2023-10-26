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

namespace core\testing;

/**
 * Testing util tests.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class util_test extends \advanced_testcase {
    /**
     * Note: This test is required for the other two parts because the first time
     * a table is written to it may not have had the initial value reset.
     *
     * @coversNothing
     */
    public function test_increment_reset_part_one(): void {
        global $DB;

        switch ($DB->get_dbfamily()) {
            case 'mssql':
                $this->markTestSkipped('MSSQL does not support sequences');
                return;
            case 'mysql':
                $version = $DB->get_server_info();
                if (version_compare($version['version'], '5.7.4', '<')) {
                    return;
                }
        }

        $this->resetAfterTest();
        $DB->insert_record('config_plugins', [
            'plugin' => 'example',
            'name' => 'test_increment',
            'value' => 0,
        ]);
    }

    /**
     * @coversNothing
     * @depends test_increment_reset_part_one
     */
    public function test_increment_reset_part_two(): int {
        global $DB;

        $this->resetAfterTest();
        return $DB->insert_record('config_plugins', [
            'plugin' => 'example',
            'name' => 'test_increment',
            'value' => 0,
        ]);
    }

    /**
     * @depends test_increment_reset_part_two
     */
    public function test_increment_reset_part_three(int $previousid): void {
        global $DB;

        $this->resetAfterTest();
        $id = $DB->insert_record('config_plugins', [
            'plugin' => 'example',
            'name' => 'test_increment',
            'value' => 0,
        ]);
        $this->assertEquals($previousid, $id);
    }
}
