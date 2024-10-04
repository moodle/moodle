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

namespace core;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/adminlib.php');

/**
 * Unit tests for parts of adminlib.php.
 *
 * @package    core
 * @copyright  2020 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class adminlib_test extends \advanced_testcase {

    /**
     * Data provider of serialized string.
     *
     * @return array
     */
    public static function db_should_replace_dataprovider(): array {
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
    public function test_db_should_replace(string $table, string $column, bool $expected): void {
        $actual = db_should_replace($table, $column);
        $this->assertSame($actual, $expected);
    }

    /**
     * Data provider for additional skip tables.
     *
     * @covers ::db_should_replace
     * @return array
     */
    public static function db_should_replace_additional_skip_tables_dataprovider(): array {
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
    public function test_db_should_replace_additional_skip_tables(string $table, string $column, bool $expected): void {
        $this->resetAfterTest();
        $additionalskiptables = 'context, quiz_attempts, role_assignments ';
        $actual = db_should_replace($table, $column, $additionalskiptables);
        $this->assertSame($actual, $expected);
    }

    /**
     * Test admin_output_new_settings_by_page method.
     *
     * @covers ::admin_output_new_settings_by_page
     */
    public function test_admin_output_new_settings_by_page(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $root = admin_get_root(true, true);
        // The initial list of html pages with no default settings.
        $initialsettings = admin_output_new_settings_by_page($root);
        $this->assertArrayHasKey('supportcontact', $initialsettings);
        $this->assertArrayHasKey('frontpagesettings', $initialsettings);
        // Existing default setting.
        $this->assertArrayNotHasKey('modsettingbook', $initialsettings);

        // Add settings not set during PHPUnit init.
        set_config('supportemail', 'support@example.com');
        $frontpage = new \admin_setting_special_frontpagedesc();
        $frontpage->write_setting('test test');
        // Remove a default setting.
        unset_config('numbering', 'book');

        $root = admin_get_root(true, true);
        $new = admin_output_new_settings_by_page($root);
        $this->assertArrayNotHasKey('supportcontact', $new);
        $this->assertArrayNotHasKey('frontpagesettings', $new);
        $this->assertArrayHasKey('modsettingbook', $new);
    }

    /**
     * Test repeated recursive application of default settings.
     *
     * @covers ::admin_apply_default_settings
     */
    public function test_admin_apply_default_settings(): void {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        // There should not be any pending new defaults.
        $saved = admin_apply_default_settings(null, false);
        $this->assertSame([], $saved);

        // Emulation of upgrades from CLI.
        unset_config('logocompact', 'core_admin');
        unset_config('grade_aggregationposition');
        unset_config('numbering', 'book');
        unset_config('enabled', 'core_competency');
        unset_config('pushcourseratingstouserplans', 'core_competency');
        $saved = admin_apply_default_settings(null, false);
        $expected = [
            'core_competency/enabled' => '1',
            'grade_aggregationposition' => '1',
            'book/numbering' => '1',
            'core_admin/logocompact' => '',
            'core_competency/pushcourseratingstouserplans' => '1',
        ];
        $this->assertEquals($expected, $saved);

        // Repeated application of defaults - not done usually.
        $saved = admin_apply_default_settings(null, true);
        $this->assertGreaterThan(500, count($saved));
        $saved = admin_apply_default_settings();
        $this->assertGreaterThan(500, count($saved));

        // Emulate initial application of defaults.
        $DB->delete_records('config', []);
        $DB->delete_records('config_plugins', []);
        purge_all_caches();
        $saved = admin_apply_default_settings(null, true);
        $this->assertGreaterThan(500, count($saved));

        // Make sure there were enough repetitions.
        $saved = admin_apply_default_settings(null, false);
        $this->assertSame([], $saved);
    }
}
