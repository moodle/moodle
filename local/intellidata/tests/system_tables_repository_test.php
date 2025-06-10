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
 * System tables repository test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata;

use local_intellidata\repositories\system_tables_repository;

/**
 * System tables repository test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class system_tables_repository_test extends \advanced_testcase {

    /**
     * Set up the test.
     *
     * @return void
     */
    public function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * Test get_excluded_tables() method.
     *
     * @return void
     * @covers \local_intellidata\repositories\system_tables_repository::get_excluded_tables
     */
    public function test_get_excluded_tables() {
        $dbtables = [
            'analytics',
            'analytics_logs',
            'assign_overrides',
            'users',
            'quiz',
            'local_intellidata_config',
        ];

        $expected = [
            'analytics_logs',
            'assign_overrides',
            'local_intellidata_config',
        ];

        $result = system_tables_repository::get_excluded_tables($dbtables);

        $this->assertEquals(array_values($expected), array_values($result));
    }

    /**
     * Test exclude_tables() method.
     *
     * @return void
     * @covers \local_intellidata\repositories\system_tables_repository::exclude_tables
     */
    public function test_exclude_tables() {
        $dbtables = [
            'analytics' => 'analytics',
            'analytics_logs' => 'analytics_logs',
            'assign_overrides' => 'assign_overrides',
            'users' => 'users',
            'quiz' => 'quiz',
            'local_intellidata_config' => 'local_intellidata_config',
        ];

        $expected = [
            'analytics',
            'users',
            'quiz',
        ];

        $result = system_tables_repository::exclude_tables($dbtables);

        $this->assertEquals(array_values($expected), array_values($result));
    }
}
