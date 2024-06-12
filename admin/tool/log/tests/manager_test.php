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

namespace tool_log;

/**
 * Log manager and log API tests.
 *
 * @package    tool_log
 * @copyright  2014 Petr Skoda {@link http://skodak.org/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager_test extends \advanced_testcase {
    public function test_get_log_manager(): void {
        global $CFG;
        $this->resetAfterTest();

        $manager = get_log_manager();
        $this->assertInstanceOf('core\log\manager', $manager);

        $stores = $manager->get_readers();
        $this->assertIsArray($stores);
        $this->assertCount(0, $stores);

        $this->assertFileExists("$CFG->dirroot/$CFG->admin/tool/log/store/standard/version.php");

        set_config('enabled_stores', 'logstore_standard', 'tool_log');
        $manager = get_log_manager(true);
        $this->assertInstanceOf('core\log\manager', $manager);

        $stores = $manager->get_readers();
        $this->assertIsArray($stores);
        $this->assertCount(1, $stores);
        foreach ($stores as $key => $store) {
            $this->assertIsString($key);
            $this->assertInstanceOf('core\log\sql_reader', $store);
        }

        $stores = $manager->get_readers('core\log\sql_internal_table_reader');
        $this->assertIsArray($stores);
        $this->assertCount(1, $stores);
        foreach ($stores as $key => $store) {
            $this->assertIsString($key);
            $this->assertSame('logstore_standard', $key);
            $this->assertInstanceOf('core\log\sql_internal_table_reader', $store);
        }

        $stores = $manager->get_readers('core\log\sql_reader');
        $this->assertIsArray($stores);
        $this->assertCount(1, $stores);
        foreach ($stores as $key => $store) {
            $this->assertIsString($key);
            $this->assertInstanceOf('core\log\sql_reader', $store);
        }
    }
}
