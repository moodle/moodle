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
 * Database driver test class for testing moodle_read_slave_trait
 *
 * @package    core
 * @category   dml
 * @copyright  2018 Srdjan Janković, Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/test_moodle_database.php');
require_once(__DIR__.'/../../moodle_read_slave_trait.php');

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
    public function raw_connect(string $dbhost, string $dbuser, string $dbpass, string $dbname, $prefix, array $dboptions = null): bool {
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
    private function with_query_start_end($sql, array $params = null, $querytype) {
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
     * Calls with_query_start_end()
     * @param string $sql
     * @param array $params
     * @return bool true
     * @throws Exception
     */
    public function execute($sql, array $params = null) {
        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);
        return $this->with_query_start_end($sql, $params, SQL_QUERY_UPDATE);
    }

    /**
     * get_records_sql() override, calls with_query_start_end()
     * @param string $sql the SQL select query to execute.
     * @param array $params array of sql parameters
     * @param int $limitfrom return a subset of records, starting at this point (optional).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return string $handle handle property
     */
    public function get_records_sql($sql, array $params = null, $limitfrom = 0, $limitnum = 0) {
        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);
        return $this->with_query_start_end($sql, $params, SQL_QUERY_SELECT);
    }

    /**
     * Calls with_query_start_end()
     * @param string $sql
     * @param array $params
     * @param int $limitfrom
     * @param int $limitnum
     * @return bool true
     */
    public function get_recordset_sql($sql, array $params = null, $limitfrom = 0, $limitnum = 0) {
        list($sql, $params, $type) = $this->fix_sql_params($sql, $params);
        return $this->with_query_start_end($sql, $params, SQL_QUERY_SELECT);
    }

    /**
     * Calls with_query_start_end()
     * @param string $table
     * @param array $params
     * @param bool $returnid
     * @param bool $bulk
     * @param bool $customsequence
     * @return string $handle handle property
     */
    public function insert_record_raw($table, $params, $returnid = true, $bulk = false, $customsequence = false) {
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
     * @param string $table
     * @param array $params
     * @param bool $bulk
     * @return string $handle handle property
     */
    public function update_record_raw($table, $params, $bulk = false) {
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
    protected function get_db_handle() {
        return $this->handle;
    }

    /**
     * Sets handle property
     * @param string $dbh
     * @return void
     */
    protected function set_db_handle($dbh): void {
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

    /**
     * Is session lock supported in this driver?
     * @return bool
     */
    public function session_lock_supported() {
        return true;
    }
}
