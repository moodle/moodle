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
 * Tasks helper test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/local/intellidata/tests/setup_helper.php');
require_once($CFG->dirroot . '/local/intellidata/tests/generator.php');
require_once($CFG->dirroot . '/local/intellidata/tests/test_helper.php');

use local_intellidata\helpers\MigrationHelper;
use local_intellidata\helpers\SettingsHelper;

/**
 * Tasks helper test case.
 *
 * @package    local_intellidata
 * @subpackage intellidata
 * @copyright  2023
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class migration_helper_test extends \advanced_testcase {

    /**
     * Test migration_disabled method when setting enabled.
     *
     * @return void
     * @throws \dml_exception
     * @covers \local_intellidata\helpers\MigrationHelper::migration_disabled
     */
    public function test_migration_disabled_when_enabled() {
        $this->resetAfterTest();

        SettingsHelper::set_setting('forcedisablemigration', 1);
        $this->assertTrue(MigrationHelper::migration_disabled());
    }

    /**
     * Test migration_disabled method when setting disabled.
     *
     * @return void
     * @throws \dml_exception
     * @covers \local_intellidata\helpers\MigrationHelper::migration_disabled
     */
    public function test_migration_disabled_when_disabled() {
        $this->resetAfterTest();

        SettingsHelper::set_setting('forcedisablemigration', 0);
        $this->assertFalse(MigrationHelper::migration_disabled());
    }
}
