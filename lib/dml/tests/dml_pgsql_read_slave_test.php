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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../pgsql_native_moodle_database.php');
require_once(__DIR__.'/../moodle_temptables.php');

/**
 * Database driver mock test class that exposes some methods
 *
 * @package    core
 * @category   dml
 * @copyright  2018 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class read_slave_moodle_database_mock extends pgsql_native_moodle_database {
    /**
     * @var string
     */
    protected $prefix = 't_';

    /**
     * Constructs a mock db driver
     *
     * @param bool $external
     */
    public function __construct($external=false) {
        parent::__construct($external);

        $this->dbhwrite = 'test_rw';
        $this->dbhreadonly = 'test_ro';
        $this->set_db_handle($this->dbhwrite);

        $this->temptables = new moodle_temptables($this);
    }

    // @codingStandardsIgnoreStart
    /**
     * Upgrade to public
     * @return resource
     */
    public function db_handle() {
        return parent::db_handle();
    }

    /**
     * Upgrade to public
     * @param string $sql
     * @param array $params
     * @param int $type
     * @param array $extrainfo
     */
    public function query_start($sql, array $params=null, $type, $extrainfo=null) {
        return parent::query_start($sql, $params, $type);
    }

    /**
     * Upgrade to public
     * @param mixed $result
     */
    public function query_end($result) {
        $this->set_db_handle($this->dbhwrite);
    }

    /**
     * Upgrade to public
     */
    public function dispose() {
    }
    // @codingStandardsIgnoreEnd
}

/**
 * DML pgsql_native_moodle_database read slave specific tests
 *
 * @package    core
 * @category   dml
 * @copyright  2018 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_dml_pgsql_read_slave_testcase extends base_testcase {
    /**
     * Constructs a test case with the given name.
     *
     * @param string $name
     * @param array  $data
     * @param string $dataname
     */
    final public function __construct($name = null, array $data = array(), $dataname = '') {
        parent::__construct($name, $data, $dataname);

        $this->setBackupGlobals(false);
        $this->setBackupStaticAttributes(false);
        $this->setRunTestInSeparateProcess(false);
    }

    public function test_cursors() {
        $DB = new read_slave_moodle_database_mock();

        $sql = 'DECLARE crs1 NO SCROLL CURSOR WITH HOLD FOR SELECT * FROM t_table';
        $DB->query_start($sql, null, SQL_QUERY_SELECT);
        $DB->query_end(null);

        $DB->query_start("INSERT INTO t_table2 (name) VALUES ('blah')", null, SQL_QUERY_INSERT);
        $sql = 'DECLARE crs2 NO SCROLL CURSOR WITH HOLD FOR SELECT * FROM t_table2';
        $DB->query_start($sql, null, SQL_QUERY_SELECT);
        $DB->query_end(null);

        $sql = 'FETCH 1 FROM crs1';
        $DB->query_start($sql, null, SQL_QUERY_AUX);
        $this->assertEquals('test_ro', $DB->db_handle());
        $DB->query_end(null);

        $sql = 'FETCH 1 FROM crs2';
        $DB->query_start($sql, null, SQL_QUERY_AUX);
        $this->assertEquals('test_rw', $DB->db_handle());
        $DB->query_end(null);
    }

    public function test_read_pg_table() {
        $DB = new read_slave_moodle_database_mock();

        $this->assertEquals(0, $DB->perf_get_reads_slave());

        $DB->query_start('SELECT pg_whatever(1)', null, SQL_QUERY_SELECT);
        $this->assertEquals('test_ro', $DB->db_handle());
        $DB->query_end(null);
        $this->assertEquals(1, $DB->perf_get_reads_slave());
    }

    public function test_read_pg_lock_table() {
        $DB = new read_slave_moodle_database_mock();

        $this->assertEquals(0, $DB->perf_get_reads_slave());

        foreach (['pg_try_advisory_lock', 'pg_advisory_unlock'] as $fn) {
            $DB->query_start("SELECT $fn(1)", null, SQL_QUERY_SELECT);
            $this->assertEquals('test_rw', $DB->db_handle());
            $DB->query_end(null);
            $this->assertEquals(0, $DB->perf_get_reads_slave());
        }
    }

    public function test_temp_table() {
        $DB = new read_slave_moodle_database_mock();

        $this->assertEquals(0, $DB->perf_get_reads_slave());

        $dbman = $DB->get_manager();
        $table = new xmldb_table('silly_test_table');
        $table->add_field('id', XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('msg', XMLDB_TYPE_CHAR, 255);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $dbman->create_temp_table($table);

        $DB->get_columns('silly_test_table');
        $DB->get_records('silly_test_table');
        $this->assertEquals(0, $DB->perf_get_reads_slave());
    }
}
