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
class adminlib_test extends \advanced_testcase {

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

    /**
     * Test method used by upgradesettings.php to make sure
     * there are no missing settings in PHPUnit and Behat tests.
     *
     * @covers ::admin_output_new_settings_by_page
     */
    public function test_admin_output_new_settings_by_page() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Add settings not set during PHPUnit init.
        set_config('supportemail', 'support@example.com');
        $frontpage = new \admin_setting_special_frontpagedesc();
        $frontpage->write_setting('test test');

        // NOTE: if this test fails then it is most likely extra setting in
        // some additional plugin without default - developer needs to add
        // a workaround into their db/install.php for PHPUnit and Behat.

        $root = admin_get_root(true, true);
        $new = admin_output_new_settings_by_page($root);
        $this->assertSame([], $new);

        unset_config('numbering', 'book');
        unset_config('supportemail');
        $root = admin_get_root(true, true);
        $new = admin_output_new_settings_by_page($root);
        $this->assertCount(2, $new);
    }

    /**
     * Test repeated recursive application of default settings.
     *
     * @covers ::admin_apply_default_settings
     */
    public function test_admin_apply_default_settings() {
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
