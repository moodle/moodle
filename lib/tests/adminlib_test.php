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
 * Unit tests for parts of adminlib.php.
 *
 * @package    core
 * @subpackage admin
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/adminlib.php');

/**
 * Unit tests for parts of adminlib.php.
 *
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_adminlib_testcase extends advanced_testcase {

    /**
     * Data provider of serialized string.
     *
     * @return array
     */
    public function db_should_replace_dataprovider() {
        return [
            // Skipped tables.
            ['block_instances', '', false],
            ['config',          '', false],
            ['config_plugins',  '', false],
            ['config_log',      '', false],
            ['events_queue',    '', false],
            ['filter_config',   '', false],
            ['log',             '', false],
            ['repository_instance_config', '', false],
            ['sessions',        '', false],
            ['upgrade_log',     '', false],

            // Unknown skipped tables.
            ['foobar_log',      '', false],
            ['foobar_logs',     '', false],

            // Unknown ok tables.
            ['foobar_logical',  '', true],

            // Normal tables.
            ['assign',          '', true],

            // Normal tables with excluded columns.
            ['message_conversations', 'convhash',   false],
            ['user_password_history', 'hash',       false],
            ['foo',                   'barhash',    false],
        ];
    }

    /**
     * Test which tables and column should be replaced.
     *
     * @dataProvider db_should_replace_dataprovider
     * @covers ::db_should_replace
     * @param string $table name
     * @param string $column name
     * @param bool $expected whether it should be replaced
     */
    public function test_db_should_replace(string $table, string $column, bool $expected) {
        $actual = db_should_replace($table, $column);
        $this->assertSame($actual, $expected);
    }

    /**
     * Data provider for additional skip tables.
     *
     * @covers ::db_should_replace
     * @return array
     */
    public function db_should_replace_additional_skip_tables_dataprovider() {
        return [
            // Skipped tables.
            ['block_instances', '', false],
            ['config',          '', false],
            ['config_plugins',  '', false],
            ['config_log',      '', false],
            ['events_queue',    '', false],
            ['filter_config',   '', false],
            ['log',             '', false],
            ['repository_instance_config', '', false],
            ['sessions',        '', false],
            ['upgrade_log',     '', false],

            // Additional skipped tables.
            ['context',      '', false],
            ['quiz_attempts',     '', false],
            ['role_assignments',     '', false],

            // Normal tables.
            ['assign',          '', true],
            ['book',          '', true],
        ];
    }

    /**
     * Test additional skip tables.
     *
     * @dataProvider db_should_replace_additional_skip_tables_dataprovider
     * @covers ::db_should_replace
     * @param string $table name
     * @param string $column name
     * @param bool $expected whether it should be replaced
     */
    public function test_db_should_replace_additional_skip_tables(string $table, string $column, bool $expected) {
        $this->resetAfterTest();
        $additionalskiptables = 'context, quiz_attempts, role_assignments ';
        $actual = db_should_replace($table, $column, $additionalskiptables);
        $this->assertSame($actual, $expected);
    }
}
