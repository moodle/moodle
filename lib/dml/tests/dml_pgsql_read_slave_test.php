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
 * DML read/read-write database handle tests for pgsql_native_moodle_database
 *
 * @package    core
 * @category   dml
 * @copyright  2018 Srdjan Janković, Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \pgsql_native_moodle_database
 */

namespace core;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/fixtures/read_slave_moodle_database_mock_pgsql.php');

/**
 * DML pgsql_native_moodle_database read slave specific tests
 *
 * @package    core
 * @category   dml
 * @copyright  2018 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dml_pgsql_read_slave_test extends \base_testcase {
    /**
     * Test correct database handles are used for cursors
     *
     * @return void
     */
    public function test_cursors() : void {
        $DB = new read_slave_moodle_database_mock_pgsql();

        // Declare a cursor on a table that has not been written to.
        list($sql, $params, $type) = $DB->fix_sql_params("SELECT * FROM {table}");
        $sql = "DECLARE crs1 NO SCROLL CURSOR WITH HOLD FOR $sql";
        $DB->query_start($sql, null, SQL_QUERY_SELECT);
        $DB->query_end(null);

        // Declare a cursor on a table that has been written to.
        list($sql, $params, $type) = $DB->fix_sql_params("INSERT INTO {table2} (name) VALUES ('blah')");
        $DB->query_start($sql, null, SQL_QUERY_INSERT);
        $DB->query_end(null);
        list($sql, $params, $type) = $DB->fix_sql_params("SELECT * FROM {table2}");
        $sql = "DECLARE crs2 NO SCROLL CURSOR WITH HOLD FOR $sql";
        $DB->query_start($sql, null, SQL_QUERY_SELECT);
        $DB->query_end(null);

        // Read from the non-written to table cursor.
        $sql = 'FETCH 1 FROM crs1';
        $DB->query_start($sql, null, SQL_QUERY_AUX);
        $this->assertTrue($DB->db_handle_is_ro());
        $DB->query_end(null);

        // Read from the written to table cursor.
        $sql = 'FETCH 1 FROM crs2';
        $DB->query_start($sql, null, SQL_QUERY_AUX);
        $this->assertTrue($DB->db_handle_is_rw());
        $DB->query_end(null);
    }

    /**
     * Test readonly handle is used for reading from random pg_*() call queries.
     *
     * @return void
     */
    public function test_read_pg_table() : void {
        $DB = new read_slave_moodle_database_mock_pgsql();

        $this->assertEquals(0, $DB->perf_get_reads_slave());

        $DB->query_start('SELECT pg_whatever(1)', null, SQL_QUERY_SELECT);
        $this->assertTrue($DB->db_handle_is_ro());
        $DB->query_end(null);
        $this->assertEquals(1, $DB->perf_get_reads_slave());
    }

    /**
     * Test readonly handle is not used for reading from special pg_*() call queries,
     * pg_try_advisory_lock and pg_advisory_unlock.
     *
     * @return void
     */
    public function test_read_pg_lock_table() : void {
        $DB = new read_slave_moodle_database_mock_pgsql();

        $this->assertEquals(0, $DB->perf_get_reads_slave());

        foreach (['pg_try_advisory_lock', 'pg_advisory_unlock'] as $fn) {
            $DB->query_start("SELECT $fn(1)", null, SQL_QUERY_SELECT);
            $this->assertTrue($DB->db_handle_is_rw());
            $DB->query_end(null);
            $this->assertEquals(0, $DB->perf_get_reads_slave());
        }
    }

    /**
     * Test readonly handle is not used for reading from temptables
     * and getting temptables metadata.
     * This test is only possible because of no pg_query error reporting.
     * It may need to be removed in the future if we decide to handle null
     * results in pgsql_native_moodle_database differently.
     *
     * @return void
     */
    public function test_temp_table() : void {
        global $DB;

        if ($DB->get_dbfamily() != 'postgres') {
            $this->markTestSkipped("Not postgres");
        }

        // Open second connection.
        $cfg = $DB->export_dbconfig();
        if (!isset($cfg->dboptions)) {
            $cfg->dboptions = [];
        }
        if (!isset($cfg->dboptions['readonly'])) {
            $cfg->dboptions['readonly'] = [
                'instance' => [$cfg->dbhost]
            ];
        }

        // Get a separate disposable db connection handle with guaranteed 'readonly' config.
        $db2 = \moodle_database::get_driver_instance($cfg->dbtype, $cfg->dblibrary);
        $db2->connect($cfg->dbhost, $cfg->dbuser, $cfg->dbpass, $cfg->dbname, $cfg->prefix, $cfg->dboptions);

        $dbman = $db2->get_manager();

        $table = new \xmldb_table('silly_test_table');
        $table->add_field('id', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('msg', XMLDB_TYPE_CHAR, 255);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $dbman->create_temp_table($table);

        // We need to go through the creation proces twice.
        // create_temp_table() performs some reads before the temp table is created.
        // First time around those reads should go to ro ...
        $reads = $db2->perf_get_reads_slave();

        $db2->get_columns('silly_test_table');
        $db2->get_records('silly_test_table');
        $this->assertEquals($reads, $db2->perf_get_reads_slave());

        $table2 = new \xmldb_table('silly_test_table2');
        $table2->add_field('id', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table2->add_field('msg', XMLDB_TYPE_CHAR, 255);
        $table2->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $dbman->create_temp_table($table2);

        // ... but once the first temp table is created no more ro reads should occur.
        $db2->get_columns('silly_test_table2');
        $db2->get_records('silly_test_table2');
        $this->assertEquals($reads, $db2->perf_get_reads_slave());

        // Make database driver happy.
        $dbman->drop_table($table2);
        $dbman->drop_table($table);
    }

    /**
     * Test readonly connection failure with real pgsql connection
     *
     * @return void
     */
    public function test_real_readslave_connect_fail() : void {
        global $DB;

        if ($DB->get_dbfamily() != 'postgres') {
            $this->markTestSkipped("Not postgres");
        }

        // Open second connection.
        $cfg = $DB->export_dbconfig();
        if (!isset($cfg->dboptions)) {
            $cfg->dboptions = array();
        }
        $cfg->dboptions['readonly'] = [
            'instance' => ['host.that.is.not'],
            'connecttimeout' => 1
        ];

        $db2 = \moodle_database::get_driver_instance($cfg->dbtype, $cfg->dblibrary);
        $db2->connect($cfg->dbhost, $cfg->dbuser, $cfg->dbpass, $cfg->dbname, $cfg->prefix, $cfg->dboptions);
        $this->assertTrue(count($db2->get_records('user')) > 0);
    }
}
