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
 * DML read/read-write database handle use tests
 *
 * @package    core
 * @category   dml
 * @copyright  2018 Srdjan Janković, Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/fixtures/read_slave_moodle_database_table_names.php');
require_once(__DIR__.'/fixtures/read_slave_moodle_database_special.php');
require_once(__DIR__.'/../../tests/fixtures/event_fixtures.php');

/**
 * DML read/read-write database handle use tests
 *
 * @package    core
 * @category   dml
 * @copyright  2018 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \moodle_read_slave_trait
 */
class dml_read_slave_test extends \base_testcase {

    /** @var float */
    static private $dbreadonlylatency = 0.8;

    /**
     * Instantiates a test database interface object.
     *
     * @param bool $wantlatency
     * @param mixed $readonly
     * @param mixed $dbclass
     * @return read_slave_moodle_database $db
     */
    public function new_db(
        $wantlatency = false,
        $readonly = [
            ['dbhost' => 'test_ro1', 'dbport' => 1, 'dbuser' => 'test1', 'dbpass' => 'test1'],
            ['dbhost' => 'test_ro2', 'dbport' => 2, 'dbuser' => 'test2', 'dbpass' => 'test2'],
            ['dbhost' => 'test_ro3', 'dbport' => 3, 'dbuser' => 'test3', 'dbpass' => 'test3'],
        ],
        $dbclass = read_slave_moodle_database::class
    ): read_slave_moodle_database {
        $dbhost = 'test_rw';
        $dbname = 'test';
        $dbuser = 'test';
        $dbpass = 'test';
        $prefix = 'test_';
        $dboptions = ['readonly' => ['instance' => $readonly, 'exclude_tables' => ['exclude']]];
        if ($wantlatency) {
            $dboptions['readonly']['latency'] = self::$dbreadonlylatency;
        }

        $db = new $dbclass();
        $db->connect($dbhost, $dbuser, $dbpass, $dbname, $prefix, $dboptions);
        return $db;
    }

    /**
     * Asert that the mock handle returned from read_slave_moodle_database methods
     * is a readonly slave handle.
     *
     * @param string $handle
     * @return void
     */
    private function assert_readonly_handle($handle): void {
        $this->assertMatchesRegularExpression('/^test_ro\d:\d:test\d:test\d$/', $handle);
    }

    /**
     * moodle_read_slave_trait::table_names() test data provider
     *
     * @return array
     * @dataProvider table_names_provider
     */
    public function table_names_provider(): array {
        return [
            [
                "SELECT *
                 FROM {user} u
                 JOIN (
                     SELECT DISTINCT u.id FROM {user} u
                     JOIN {user_enrolments} ue1 ON ue1.userid = u.id
                     JOIN {enrol} e ON e.id = ue1.enrolid
                     WHERE u.id NOT IN (
                         SELECT DISTINCT ue.userid FROM {user_enrolments} ue
                         JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = 1)
                         WHERE ue.status = 'active'
                           AND e.status = 'enabled'
                           AND ue.timestart < now()
                           AND (ue.timeend = 0 OR ue.timeend > now())
                     )
                 ) je ON je.id = u.id
                 JOIN (
                     SELECT DISTINCT ra.userid
                       FROM {role_assignments} ra
                      WHERE ra.roleid IN (1, 2, 3)
                        AND ra.contextid = 'ctx'
                  ) rainner ON rainner.userid = u.id
                  WHERE u.deleted = 0",
                [
                    'user',
                    'user',
                    'user_enrolments',
                    'enrol',
                    'user_enrolments',
                    'enrol',
                    'role_assignments',
                ]
            ],
        ];
    }

    /**
     * Test moodle_read_slave_trait::table_names() query parser.
     *
     * @param string $sql
     * @param array $tables
     * @return void
     * @dataProvider table_names_provider
     */
    public function test_table_names($sql, $tables): void {
        $db = new read_slave_moodle_database_table_names();

        $this->assertEquals($tables, $db->table_names($db->fix_sql_params($sql)[0]));
    }

    /**
     * Test correct database handles are used in a read-read-write-read scenario.
     * Test lazy creation of the write handle.
     *
     * @return void
     */
    public function test_read_read_write_read(): void {
        $DB = $this->new_db(true);

        $this->assertEquals(0, $DB->perf_get_reads_slave());
        $this->assertNull($DB->get_dbhwrite());

        $handle = $DB->get_records('table');
        $this->assert_readonly_handle($handle);
        $readsslave = $DB->perf_get_reads_slave();
        $this->assertGreaterThan(0, $readsslave);
        $this->assertNull($DB->get_dbhwrite());

        $handle = $DB->get_records('table2');
        $this->assert_readonly_handle($handle);
        $readsslave = $DB->perf_get_reads_slave();
        $this->assertGreaterThan(1, $readsslave);
        $this->assertNull($DB->get_dbhwrite());

        $now = microtime(true);
        $handle = $DB->insert_record_raw('table', array('name' => 'blah'));
        $this->assertEquals('test_rw::test:test', $handle);

        if (microtime(true) - $now < self::$dbreadonlylatency) {
            $handle = $DB->get_records('table');
            $this->assertEquals('test_rw::test:test', $handle);
            $this->assertEquals($readsslave, $DB->perf_get_reads_slave());

            sleep(1);
        }

        $handle = $DB->get_records('table');
        $this->assert_readonly_handle($handle);
        $this->assertEquals($readsslave + 1, $DB->perf_get_reads_slave());
    }

    /**
     * Test correct database handles are used in a read-write-write scenario.
     *
     * @return void
     */
    public function test_read_write_write(): void {
        $DB = $this->new_db();

        $this->assertEquals(0, $DB->perf_get_reads_slave());
        $this->assertNull($DB->get_dbhwrite());

        $handle = $DB->get_records('table');
        $this->assert_readonly_handle($handle);
        $readsslave = $DB->perf_get_reads_slave();
        $this->assertGreaterThan(0, $readsslave);
        $this->assertNull($DB->get_dbhwrite());

        $handle = $DB->insert_record_raw('table', array('name' => 'blah'));
        $this->assertEquals('test_rw::test:test', $handle);

        $handle = $DB->update_record_raw('table', array('id' => 1, 'name' => 'blah2'));
        $this->assertEquals('test_rw::test:test', $handle);
        $this->assertEquals($readsslave, $DB->perf_get_reads_slave());
    }

    /**
     * Test correct database handles are used in a write-read-read scenario.
     *
     * @return void
     */
    public function test_write_read_read(): void {
        $DB = $this->new_db();

        $this->assertEquals(0, $DB->perf_get_reads_slave());
        $this->assertNull($DB->get_dbhwrite());

        $handle = $DB->insert_record_raw('table', array('name' => 'blah'));
        $this->assertEquals('test_rw::test:test', $handle);
        $this->assertEquals(0, $DB->perf_get_reads_slave());

        $handle = $DB->get_records('table');
        $this->assertEquals('test_rw::test:test', $handle);
        $this->assertEquals(0, $DB->perf_get_reads_slave());

        $handle = $DB->get_records_sql("SELECT * FROM {table2} JOIN {table}");
        $this->assertEquals('test_rw::test:test', $handle);
        $this->assertEquals(0, $DB->perf_get_reads_slave());

        sleep(1);

        $handle = $DB->get_records('table');
        $this->assert_readonly_handle($handle);
        $this->assertEquals(1, $DB->perf_get_reads_slave());

        $handle = $DB->get_records('table2');
        $this->assert_readonly_handle($handle);
        $this->assertEquals(2, $DB->perf_get_reads_slave());

        $handle = $DB->get_records_sql("SELECT * FROM {table2} JOIN {table}");
        $this->assert_readonly_handle($handle);
        $this->assertEquals(3, $DB->perf_get_reads_slave());
    }

    /**
     * Test readonly handle is not used for reading from temptables.
     *
     * @return void
     */
    public function test_read_temptable(): void {
        $DB = $this->new_db();
        $DB->add_temptable('temptable1');

        $this->assertEquals(0, $DB->perf_get_reads_slave());
        $this->assertNull($DB->get_dbhwrite());

        $handle = $DB->get_records('temptable1');
        $this->assertEquals('test_rw::test:test', $handle);
        $this->assertEquals(0, $DB->perf_get_reads_slave());

        $DB->delete_temptable('temptable1');
    }

    /**
     * Test readonly handle is not used for reading from excluded tables.
     *
     * @return void
     */
    public function test_read_excluded_tables(): void {
        $DB = $this->new_db();

        $this->assertEquals(0, $DB->perf_get_reads_slave());
        $this->assertNull($DB->get_dbhwrite());

        $handle = $DB->get_records('exclude');
        $this->assertEquals('test_rw::test:test', $handle);
        $this->assertEquals(0, $DB->perf_get_reads_slave());
    }

    /**
     * Test readonly handle is not used during transactions.
     * Test last written time is adjusted post-transaction,
     * so the latency parameter is applied properly.
     *
     * @return void
     * @covers ::can_use_readonly
     * @covers ::commit_delegated_transaction
     */
    public function test_transaction(): void {
        $DB = $this->new_db(true);

        $this->assertNull($DB->get_dbhwrite());

        $skip = false;
        $transaction = $DB->start_delegated_transaction();
        $now = microtime(true);
        $handle = $DB->get_records_sql("SELECT * FROM {table}");
        // Use rw handle during transaction.
        $this->assertEquals('test_rw::test:test', $handle);

        $handle = $DB->insert_record_raw('table', array('name' => 'blah'));
        // Introduce delay so we can check that table write timestamps
        // are adjusted properly.
        sleep(1);
        $transaction->allow_commit();
        // This condition should always evaluate true, however we need to
        // safeguard from an unaccounted delay that can break this test.
        if (microtime(true) - $now < 1 + self::$dbreadonlylatency) {
            // Not enough time passed, use rw handle.
            $handle = $DB->get_records_sql("SELECT * FROM {table}");
            $this->assertEquals('test_rw::test:test', $handle);

            // Make sure enough time passes.
            sleep(1);
        } else {
            $skip = true;
        }

        // Exceeded latency time, use ro handle.
        $handle = $DB->get_records_sql("SELECT * FROM {table}");
        $this->assert_readonly_handle($handle);

        if ($skip) {
            $this->markTestSkipped("Delay too long to test write handle immediately after transaction");
        }
    }

    /**
     * Test readonly handle is not used immediately after update
     * Test last written time is adjusted post-write,
     * so the latency parameter is applied properly.
     *
     * @return void
     * @covers ::can_use_readonly
     * @covers ::query_end
     */
    public function test_long_update(): void {
        $DB = $this->new_db(true);

        $this->assertNull($DB->get_dbhwrite());

        $skip = false;

        list($sql, $params, $ptype) = $DB->fix_sql_params("UPDATE {table} SET a = 1 WHERE id = 1");
        $DB->with_query_start_end($sql, $params, SQL_QUERY_UPDATE, function ($dbh) use (&$now) {
            sleep(1);
            $now = microtime(true);
        });

        // This condition should always evaluate true, however we need to
        // safeguard from an unaccounted delay that can break this test.
        if (microtime(true) - $now < self::$dbreadonlylatency) {
            // Not enough time passed, use rw handle.
            $handle = $DB->get_records_sql("SELECT * FROM {table}");
            $this->assertEquals('test_rw::test:test', $handle);

            // Make sure enough time passes.
            sleep(1);
        } else {
            $skip = true;
        }

        // Exceeded latency time, use ro handle.
        $handle = $DB->get_records_sql("SELECT * FROM {table}");
        $this->assert_readonly_handle($handle);

        if ($skip) {
            $this->markTestSkipped("Delay too long to test write handle immediately after transaction");
        }
    }

    /**
     * Test readonly handle is not used with events
     * when the latency parameter is applied properly.
     *
     * @return void
     * @covers ::can_use_readonly
     * @covers ::commit_delegated_transaction
     */
    public function test_transaction_with_events(): void {
        $this->with_global_db(function () {
            global $DB;

            $DB = $this->new_db(true, ['test_ro'], read_slave_moodle_database_special::class);
            $DB->set_tables([
                'config_plugins' => [
                    'columns' => [
                        'plugin' => (object)['meta_type' => ''],
                    ]
                ]
            ]);

            $this->assertNull($DB->get_dbhwrite());

            $called = false;
            $transaction = $DB->start_delegated_transaction();
            $now = microtime(true);

            $observers = [
                [
                    'eventname'   => '\core_tests\event\unittest_executed',
                    'callback'    => function (\core_tests\event\unittest_executed $event) use ($DB, $now, &$called) {
                        $called = true;
                        $this->assertFalse($DB->is_transaction_started());

                        // This condition should always evaluate true, however we need to
                        // safeguard from an unaccounted delay that can break this test.
                        if (microtime(true) - $now < 1 + self::$dbreadonlylatency) {
                            // Not enough time passed, use rw handle.
                            $handle = $DB->get_records_sql_p("SELECT * FROM {table}");
                            $this->assertEquals('test_rw::test:test', $handle);

                            // Make sure enough time passes.
                            sleep(1);
                        } else {
                            $this->markTestSkipped("Delay too long to test write handle immediately after transaction");
                        }

                        // Exceeded latency time, use ro handle.
                        $handle = $DB->get_records_sql_p("SELECT * FROM {table}");
                        $this->assertEquals('test_ro::test:test', $handle);
                    },
                    'internal'    => 0,
                ],
            ];
            \core\event\manager::phpunit_replace_observers($observers);

            $handle = $DB->get_records_sql_p("SELECT * FROM {table}");
            // Use rw handle during transaction.
            $this->assertEquals('test_rw::test:test', $handle);

            $handle = $DB->insert_record_raw('table', array('name' => 'blah'));
            // Introduce delay so we can check that table write timestamps
            // are adjusted properly.
            sleep(1);
            $event = \core_tests\event\unittest_executed::create([
                'context' => \context_system::instance(),
                'other' => ['sample' => 1]
            ]);
            $event->trigger();
            $transaction->allow_commit();

            $this->assertTrue($called);
        });
    }

    /**
     * Test failed readonly connection falls back to write connection.
     *
     * @return void
     */
    public function test_read_only_conn_fail(): void {
        $DB = $this->new_db(false, 'test_ro_fail');

        $this->assertEquals(0, $DB->perf_get_reads_slave());
        $this->assertNotNull($DB->get_dbhwrite());

        $handle = $DB->get_records('table');
        $this->assertEquals('test_rw::test:test', $handle);
        $readsslave = $DB->perf_get_reads_slave();
        $this->assertEquals(0, $readsslave);
    }

    /**
     * In multiple slaves scenario, test failed readonly connection falls back to
     * another readonly connection.
     *
     * @return void
     */
    public function test_read_only_conn_first_fail(): void {
        $DB = $this->new_db(false, ['test_ro_fail', 'test_ro_ok']);

        $this->assertEquals(0, $DB->perf_get_reads_slave());
        $this->assertNull($DB->get_dbhwrite());

        $handle = $DB->get_records('table');
        $this->assertEquals('test_ro_ok::test:test', $handle);
        $readsslave = $DB->perf_get_reads_slave();
        $this->assertEquals(1, $readsslave);
    }

    /**
     * Helper to restore global $DB
     *
     * @param callable $test
     * @return void
     */
    private function with_global_db($test) {
        global $DB;

        $dbsave = $DB;
        try {
            $test();
        }
        finally {
            $DB = $dbsave;
        }
    }

    /**
     * Test lock_db table exclusion
     *
     * @return void
     */
    public function test_lock_db(): void {
        $this->with_global_db(function () {
            global $DB;

            $DB = $this->new_db(true, ['test_ro'], read_slave_moodle_database_special::class);
            $DB->set_tables([
                'lock_db' => [
                    'columns' => [
                        'resourcekey' => (object)['meta_type' => ''],
                        'owner' => (object)['meta_type' => ''],
                    ]
                ]
            ]);

            $this->assertEquals(0, $DB->perf_get_reads_slave());
            $this->assertNull($DB->get_dbhwrite());

            $lockfactory = new \core\lock\db_record_lock_factory('default');
            if (!$lockfactory->is_available()) {
                $this->markTestSkipped("db_record_lock_factory not available");
            }

            $lock = $lockfactory->get_lock('abc', 2);
            $lock->release();
            $this->assertEquals(0, $DB->perf_get_reads_slave());
            $this->assertTrue($DB->perf_get_reads() > 0);
        });
    }

    /**
     * Test sessions table exclusion
     *
     * @return void
     */
    public function test_sessions(): void {
        $this->with_global_db(function () {
            global $DB, $CFG;

            $CFG->dbsessions = true;
            $DB = $this->new_db(true, ['test_ro'], read_slave_moodle_database_special::class);
            $DB->set_tables([
                'sessions' => [
                    'columns' => [
                        'sid' => (object)['meta_type' => ''],
                    ]
                ]
            ]);

            $this->assertEquals(0, $DB->perf_get_reads_slave());
            $this->assertNull($DB->get_dbhwrite());

            $session = new \core\session\database();
            $session->read('dummy');

            $this->assertEquals(0, $DB->perf_get_reads_slave());
            $this->assertTrue($DB->perf_get_reads() > 0);
        });

        \core\session\manager::restart_with_write_lock(false);
    }
}
