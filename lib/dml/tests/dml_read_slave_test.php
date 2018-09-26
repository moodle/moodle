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

require_once(__DIR__.'/fixtures/test_moodle_database.php');
require_once(__DIR__.'/../moodle_read_slave_trait.php');

/**
 * Database driver test class with moodle_read_slave_trait
 *
 * @package    core
 * @category   dml
 * @copyright  2018 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class read_slave_moodle_database extends test_moodle_database {
    use moodle_read_slave_trait;

    /** @var string */
    protected $handle;

    /**
     * Does not connect to the database. Sets handle property to $dbhost
     * @param string $dbhost
     * @param string $dbuser
     * @param string $dbpass
     * @param string $dbname
     * @param mixed $prefix
     * @param array $dboptions
     * @return bool true
     */
    public function _connect($dbhost, $dbuser, $dbpass, $dbname, $prefix, array $dboptions=null) {
        $dbport = isset($dboptions['dbport']) ? $dboptions['dbport'] : "";
        $this->handle = implode(':', [$dbhost, $dbport, $dbuser, $dbpass]);
        $this->prefix = $prefix;

        if ($dbhost == 'test_ro_fail') {
            throw new dml_connection_exception($dbhost);
        }

        return true;
    }

    /**
     * Begin database transaction
     * @return void
     */
    protected function begin_transaction() {
    }

    /**
     * Commit database transaction
     * @return void
     */
    protected function commit_transaction() {
    }

    /**
     * Abort database transaction
     * @return void
     */
    protected function rollback_transaction() {
        $this->txnhandle = $this->handle;
    }

    /**
     * Query wrapper that calls query_start() and query_end()
     * @param string $sql
     * @param array $params
     * @param int $querytype
     * @return string $handle handle property
     */
    private function with_query_start_end($sql, array $params=null, $querytype) {
        $this->query_start($sql, $params, $querytype);
        $ret = $this->handle;
        $this->query_end(null);
        return $ret;
    }

    /**
     * get_dbhwrite()
     * @return string $dbhwrite handle property
     */
    public function get_dbhwrite() {
        return $this->dbhwrite;
    }

    /**
     * get_records_sql() override, calls with_query_start_end()
     * @param string $sql the SQL select query to execute.
     * @param array $params array of sql parameters
     * @param int $limitfrom return a subset of records, starting at this point (optional).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return string $handle handle property
     */
    public function get_records_sql($sql, array $params=null, $limitfrom=0, $limitnum=0) {
        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);
        return $this->with_query_start_end($sql, $params, SQL_QUERY_SELECT);
    }

    /**
     * Calls with_query_start_end()
     * Default implementation, throws Exception
     * @param string $table
     * @param array $params
     * @param bool $returnid
     * @param bool $bulk
     * @param bool $customsequence
     * @return string $handle handle property
     */
    public function insert_record_raw($table, $params, $returnid=true, $bulk=false, $customsequence=false) {
        $fields = implode(',', array_keys($params));
        $i = 1;
        foreach ($params as $value) {
            $values[] = "\$".$i++;
        }
        $values = implode(',', $values);
        $sql = "INSERT INTO {$this->prefix}$table ($fields) VALUES($values)";
        return $this->with_query_start_end($sql, $params, SQL_QUERY_INSERT);
    }

    /**
     * Calls with_query_start_end()
     * Default implementation, throws Exception
     * @param string $table
     * @param array $params
     * @param bool $bulk
     * @return string $handle handle property
     */
    public function update_record_raw($table, $params, $bulk=false) {
        $id = $params['id'];
        unset($params['id']);
        $i = 1;
        $sets = array();
        foreach ($params as $field => $value) {
            $sets[] = "$field = \$".$i++;
        }
        $params[] = $id;
        $sets = implode(',', $sets);
        $sql = "UPDATE {$this->prefix}$table SET $sets WHERE id=\$".$i;
        return $this->with_query_start_end($sql, $params, SQL_QUERY_UPDATE);
    }

    /**
     * Gets handle property
     * @return string $handle handle property
     */
    protected function db_handle() {
        return $this->handle;
    }

    /**
     * Sets handle property
     * @param string $dbh
     * @return void
     */
    protected function set_db_handle($dbh) {
        $this->handle = $dbh;
    }

    /**
     * Add temptable
     * @param string $temptable
     * @return void
     */
    public function add_temptable($temptable) {
        $this->temptables->add_temptable($temptable);
    }

    /**
     * Remove temptable
     * @param string $temptable
     * @return void
     */
    public function delete_temptable($temptable) {
        $this->temptables->delete_temptable($temptable);
    }
}

/**
 * Database driver test class that exposes table_names()
 *
 * @package    core
 * @category   dml
 * @copyright  2018 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class read_slave_moodle_database_table_names extends read_slave_moodle_database {
    /**
     * @var string
     */
    protected $prefix = 't_';

    // @codingStandardsIgnoreStart
    /**
     * Upgrade to public
     * @param string $sql
     * @return array
     */
    public function table_names($sql) {
        return parent::table_names($sql);
    }
    // @codingStandardsIgnoreEnd
}

/**
 * DML read/read-write database handle use tests
 *
 * @package    core
 * @category   dml
 * @copyright  2018 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_dml_read_slave_testcase extends base_testcase {

    /** @var float */
    static private $dbreadonlylatency = 0.8;

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

    /**
     * Instantiates a test database interface object
     *
     * @param bool $wantlatency
     * @param mixed $readonly
     * @return read_slave_moodle_database $db
     */
    public function new_db(
        $wantlatency=false,
        $readonly=[
            ['dbhost' => 'test_ro1', 'dbport' => 1, 'dbuser' => 'test1', 'dbpass' => 'test1'],
            ['dbhost' => 'test_ro2', 'dbport' => 2, 'dbuser' => 'test2', 'dbpass' => 'test2'],
        ]
    ) {
        $dbhost = 'test_rw';
        $dbname = 'test';
        $dbuser = 'test';
        $dbpass = 'test';
        $prefix = 'test_';
        $dboptions = ['readonly' => ['instance' => $readonly, 'exclude_tables' => ['exclude']]];
        if ($wantlatency) {
            $dboptions['readonly']['latency'] = self::$dbreadonlylatency;
        }

        $db = new read_slave_moodle_database();
        $db->connect($dbhost, $dbuser, $dbpass, $dbname, $prefix, $dboptions);
        return $db;
    }

    public function test_table_names() {
        $t = array(
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
              WHERE u.deleted = 0" => [
                'user',
                'user',
                'user_enrolments',
                'enrol',
                'user_enrolments',
                'enrol',
                'role_assignments',
            ],
        );

        $db = new read_slave_moodle_database_table_names();
        foreach ($t as $sql => $tables) {
            $this->assertEquals($tables, $db->table_names($db->fix_sql_params($sql)[0]));
        }
    }

    public function test_read_read_write_read() {
        $DB = $this->new_db(true);

        $this->assertEquals(0, $DB->perf_get_reads_slave());
        $this->assertNull($DB->get_dbhwrite());

        $handle = $DB->get_records('table');
        $this->assertEquals('test_ro1:1:test1:test1', $handle);
        $readsslave = $DB->perf_get_reads_slave();
        $this->assertGreaterThan(0, $readsslave);
        $this->assertNull($DB->get_dbhwrite());

        $handle = $DB->get_records('table2');
        $this->assertEquals('test_ro1:1:test1:test1', $handle);
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
        $this->assertEquals('test_ro1:1:test1:test1', $handle);
        $this->assertEquals($readsslave + 1, $DB->perf_get_reads_slave());
    }

    public function test_read_write_write() {
        $DB = $this->new_db();

        $this->assertEquals(0, $DB->perf_get_reads_slave());
        $this->assertNull($DB->get_dbhwrite());

        $handle = $DB->get_records('table');
        $this->assertEquals('test_ro1:1:test1:test1', $handle);
        $readsslave = $DB->perf_get_reads_slave();
        $this->assertGreaterThan(0, $readsslave);
        $this->assertNull($DB->get_dbhwrite());

        $handle = $DB->insert_record_raw('table', array('name' => 'blah'));
        $this->assertEquals('test_rw::test:test', $handle);

        $handle = $DB->update_record_raw('table', array('id' => 1, 'name' => 'blah2'));
        $this->assertEquals('test_rw::test:test', $handle);
        $this->assertEquals($readsslave, $DB->perf_get_reads_slave());
    }

    public function test_write_read_read() {
        $DB = $this->new_db();

        $this->assertEquals(0, $DB->perf_get_reads_slave());
        $this->assertNull($DB->get_dbhwrite());

        $handle = $DB->insert_record_raw('table', array('name' => 'blah'));
        $this->assertEquals('test_rw::test:test', $handle);
        $this->assertEquals(0, $DB->perf_get_reads_slave());

        sleep(1);
        $handle = $DB->get_records('table');
        $this->assertEquals('test_rw::test:test', $handle);
        $this->assertEquals(0, $DB->perf_get_reads_slave());

        $handle = $DB->get_records('table2');
        $this->assertEquals('test_ro1:1:test1:test1', $handle);
        $this->assertEquals(1, $DB->perf_get_reads_slave());

        $handle = $DB->get_records_sql("SELECT * FROM {table2} JOIN {table}");
        $this->assertEquals('test_rw::test:test', $handle);
        $this->assertEquals(1, $DB->perf_get_reads_slave());
    }

    public function test_read_temptable() {
        $DB = $this->new_db();
        $DB->add_temptable('temptable1');

        $this->assertEquals(0, $DB->perf_get_reads_slave());
        $this->assertNull($DB->get_dbhwrite());

        $handle = $DB->get_records('temptable1');
        $this->assertEquals('test_rw::test:test', $handle);
        $this->assertEquals(0, $DB->perf_get_reads_slave());

        $DB->delete_temptable('temptable1');
    }

    public function test_read_excluded_tables() {
        $DB = $this->new_db();

        $this->assertEquals(0, $DB->perf_get_reads_slave());
        $this->assertNull($DB->get_dbhwrite());

        $handle = $DB->get_records('exclude');
        $this->assertEquals('test_rw::test:test', $handle);
        $this->assertEquals(0, $DB->perf_get_reads_slave());
    }

    public function test_transaction() {
        $DB = $this->new_db();

        $this->assertNull($DB->get_dbhwrite());

        $transaction = $DB->start_delegated_transaction();
        $handle = $DB->get_records_sql("SELECT * FROM {table}");
        $this->assertEquals('test_rw::test:test', $handle);
    }

    public function test_read_only_conn_fail() {
        $DB = $this->new_db(false, 'test_ro_fail');

        $this->assertEquals(0, $DB->perf_get_reads_slave());
        $this->assertNotNull($DB->get_dbhwrite());

        $handle = $DB->get_records('table');
        $this->assertEquals('test_rw::test:test', $handle);
        $readsslave = $DB->perf_get_reads_slave();
        $this->assertEquals(0, $readsslave);
    }

    public function test_read_only_conn_first_fail() {
        $DB = $this->new_db(false, ['test_ro_fail', 'test_ro_ok']);

        $this->assertEquals(0, $DB->perf_get_reads_slave());
        $this->assertNull($DB->get_dbhwrite());

        $handle = $DB->get_records('table');
        $this->assertEquals('test_ro_ok::test:test', $handle);
        $readsslave = $DB->perf_get_reads_slave();
        $this->assertEquals(1, $readsslave);
    }
}
