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
 * Database repository migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2022
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata;

use local_intellidata\helpers\SettingsHelper;
use local_intellidata\repositories\database_repository;

/**
 * Export_id_repository migration test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2022
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class database_repository_test extends \advanced_testcase {

    /**
     * Test init() method.
     *
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     * @covers \local_intellidata\repositories\database_repository::init
     */
    public function test_init() {
        $this->resetAfterTest(true);

        // Validate empty.
        $this->assertNull(database_repository::$encriptionservice);
        $this->assertNull(database_repository::$exportservice);
        $this->assertNull(database_repository::$exportlogrepository);
        $this->assertNull(database_repository::$writerecordslimits);

        database_repository::init();

        // Validate empty table.
        $this->assertInstanceOf('local_intellidata\services\encryption_service', database_repository::$encriptionservice);
        $this->assertInstanceOf('local_intellidata\services\export_service', database_repository::$exportservice);
        $this->assertInstanceOf('local_intellidata\repositories\export_log_repository', database_repository::$exportlogrepository);
        $this->assertEquals(
            database_repository::$writerecordslimits,
            (int)SettingsHelper::get_setting('migrationwriterecordslimit')
        );
    }
}
