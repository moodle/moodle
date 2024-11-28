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
 * DML read/read-write database handle tests for mysqli_native_moodle_database
 *
 * @package    core
 * @category   dml
 * @copyright  2018 Srdjan Janković, Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core;

use moodle_database;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/fixtures/read_replica_moodle_database_mock_mysqli.php');

/**
 * DML mysqli_native_moodle_database read replica specific tests
 *
 * @package    core
 * @category   dml
 * @copyright  2018 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mysqli_native_moodle_database
 */
final class dml_mysqli_read_replica_test extends \database_driver_testcase {
    /**
     * Test readonly handle is not used for reading from special pg_*() call queries,
     * pg_try_advisory_lock and pg_advisory_unlock.
     *
     * @return void
     */
    public function test_lock(): void {
        $DB = new read_replica_moodle_database_mock_mysqli();

        $this->assertEquals(0, $DB->perf_get_reads_replica());

        $DB->query_start("SELECT GET_LOCK('lock',1)", null, SQL_QUERY_SELECT);
        $this->assertTrue($DB->db_handle_is_rw());
        $DB->query_end(null);
        $this->assertEquals(0, $DB->perf_get_reads_replica());

        $DB->query_start("SELECT RELEASE_LOCK('lock',1)", null, SQL_QUERY_SELECT);
        $this->assertTrue($DB->db_handle_is_rw());
        $DB->query_end(null);
        $this->assertEquals(0, $DB->perf_get_reads_replica());
    }

    /**
     * Test readonly handle is used for SQL_QUERY_AUX_READONLY queries.
     *
     * @return void
     */
    public function test_aux_readonly(): void {
        global $DB;

        if ($DB->get_dbfamily() != 'mysql') {
            $this->markTestSkipped("Not mysql");
        }

        // Open second connection.
        $cfg = $DB->export_dbconfig();
        if (!isset($cfg->dboptions)) {
            $cfg->dboptions = [];
        }
        $cfg->dboptions['readonly'] = [
            'instance' => [$cfg->dbhost]
        ];
        $cfg->dboptions['dbengine'] = null;
        $cfg->dboptions['bulkinsertsize'] = null;

        // Get a separate disposable db connection handle with guaranteed 'readonly' config.
        $db2 = moodle_database::get_driver_instance($cfg->dbtype, $cfg->dblibrary);
        $db2->connect($cfg->dbhost, $cfg->dbuser, $cfg->dbpass, $cfg->dbname, $cfg->prefix, $cfg->dboptions);

        $reads = $db2->perf_get_reads();
        $readsprimary = $reads - $db2->perf_get_reads_replica();

        // Readonly handle queries.

        $db2->setup_is_unicodedb();
        $this->assertGreaterThan($reads, $reads = $db2->perf_get_reads());
        $this->assertEquals($readsprimary, $reads - $db2->perf_get_reads_replica());

        $db2->get_tables();
        $this->assertGreaterThan($reads, $reads = $db2->perf_get_reads());
        $this->assertEquals($readsprimary, $reads - $db2->perf_get_reads_replica());

        $db2->get_indexes('course');
        $this->assertGreaterThan($reads, $reads = $db2->perf_get_reads());
        $this->assertEquals($readsprimary, $reads - $db2->perf_get_reads_replica());

        $db2->get_columns('course');
        $this->assertGreaterThan($reads, $reads = $db2->perf_get_reads());
        $this->assertEquals($readsprimary, $reads - $db2->perf_get_reads_replica());

        // Readwrite handle queries.

        if (PHP_INT_SIZE !== 4) {
            $rc = new \ReflectionClass(\mysqli_native_moodle_database::class);
            $rcm = $rc->getMethod('insert_chunk_size');

            $rcm->invoke($db2);
            $this->assertGreaterThan($reads, $reads = $db2->perf_get_reads());
            $this->assertGreaterThan($readsprimary, $readsprimary = $reads - $db2->perf_get_reads_replica());
        }

        $db2->get_dbengine();
        $this->assertGreaterThan($reads, $reads = $db2->perf_get_reads());
        $this->assertGreaterThan($readsprimary, $readsprimary = $reads - $db2->perf_get_reads_replica());

        $db2->get_row_format('course');
        $this->assertGreaterThan($reads, $reads = $db2->perf_get_reads());
        $this->assertGreaterThan($readsprimary, $readsprimary = $reads - $db2->perf_get_reads_replica());
    }

    /**
     * Test readonly connection failure with real mysqli connection
     *
     * @return void
     */
    public function test_real_readreplica_connect_fail_host(): void {
        global $DB;

        if ($DB->get_dbfamily() != 'mysql') {
            $this->markTestSkipped('Not mysql');
        }

        $invalidhost = 'host.that.is.not';

        // Open second connection.
        $cfg = $DB->export_dbconfig();
        if (!isset($cfg->dboptions)) {
            $cfg->dboptions = [];
        }
        $cfg->dboptions['readonly'] = [
            'instance' => [$invalidhost],
            'connecttimeout' => 1
        ];

        $this->resetDebugging();
        $db2 = \moodle_database::get_driver_instance($cfg->dbtype, $cfg->dblibrary);
        $db2->connect($cfg->dbhost, $cfg->dbuser, $cfg->dbpass, $cfg->dbname, $cfg->prefix, $cfg->dboptions);
        $this->assertNotEmpty($db2->get_records('user'));

        $debugging = array_map(function ($d) {
            return $d->message;
        }, $this->getDebuggingMessages());
        $this->resetDebugging();
        $this->assertCount(2, $debugging);
        $this->assertMatchesRegularExpression(
            sprintf(
                '/%s%s/',
                preg_quote("Readonly db connection failed for host {$invalidhost}:"),
                '.*'
            ),
            $debugging[0]
        );
        $this->assertEquals("Readwrite db connection succeeded for host {$cfg->dbhost}", $debugging[1]);
    }

    /**
     * Test connection failure
     *
     * @return void
     */
    public function test_real_readreplica_connect_fail_dbname(): void {
        global $DB;

        if ($DB->get_dbfamily() != 'mysql') {
            $this->markTestSkipped("Not mysql");
        }

        $invaliddb = 'cannot-exist-really';

        // Open second connection.
        $cfg = $DB->export_dbconfig();
        $cfg->dbname = $invaliddb;
        if (!isset($cfg->dboptions)) {
            $cfg->dboptions = [];
        }
        $cfg->dboptions['readonly'] = [
            'instance' => [$cfg->dbhost],
            'connecttimeout' => 1,
        ];

        $this->resetDebugging();
        $db2 = \moodle_database::get_driver_instance($cfg->dbtype, $cfg->dblibrary);
        try {
            $db2->connect($cfg->dbhost, $cfg->dbuser, $cfg->dbpass, $cfg->dbname, $cfg->prefix, $cfg->dboptions);
        } catch (\dml_connection_exception $e) {  // phpcs:ignore
            // We cannot go with expectException() because it would skip the rest.
        }

        $debugging = array_map(function ($d) {
            return $d->message;
        }, $this->getDebuggingMessages());
        $this->resetDebugging();
        $this->assertCount(2, $debugging);
        // Read-only attempt to connect to the non-existent replica database will fail.
        // Note: The expected regex pattern is a bit generic because the actual error message may vary between operating systems.
        $this->assertMatchesRegularExpression(
            sprintf(
                '/%s%s/',
                preg_quote("Readonly db connection failed for host {$cfg->dbhost}: "),
                ".* database '$invaliddb'"
            ),
            $debugging[0]
        );
        // Read-write attempt to connect to the non-existent replica database will also fail.
        $this->assertMatchesRegularExpression(
            sprintf(
                '/%s%s/',
                preg_quote("Readwrite db connection failed for host {$cfg->dbhost}: "),
                ".* '$invaliddb"
            ),
            $debugging[1]
        );
    }
}
