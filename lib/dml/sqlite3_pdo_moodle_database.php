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
 * Experimental pdo database class.
 *
 * @package    core_dml
 * @copyright  2008 Andrei Bautu
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/pdo_moodle_database.php');

/**
 * Experimental pdo database class
 *
 * @package    core_dml
 * @copyright  2008 Andrei Bautu
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sqlite3_pdo_moodle_database extends pdo_moodle_database {
    protected $database_file_extension = '.sq3.php';
    /**
     * Detects if all needed PHP stuff installed.
     * Note: can be used before connect()
     * @return mixed true if ok, string if something
     */
    public function driver_installed() {
        if (!extension_loaded('pdo_sqlite') || !extension_loaded('pdo')){
            return get_string('sqliteextensionisnotpresentinphp', 'install');
        }
        return true;
    }

    /**
     * Returns database family type - describes SQL dialect
     * Note: can be used before connect()
     * @return string db family name (mysql, postgres, mssql, oracle, etc.)
     */
    public function get_dbfamily() {
        return 'sqlite';
    }

    /**
     * Returns more specific database driver type
     * Note: can be used before connect()
     * @return string db type mysqli, pgsql, oci, mssql, sqlsrv
     */
    protected function get_dbtype() {
        return 'sqlite3';
    }

    protected function configure_dbconnection() {
        // try to protect database file against web access;
        // this is required in case that the moodledata folder is web accessible and
        // .htaccess is not in place; requires that the database file extension is php
        $this->pdb->exec('CREATE TABLE IF NOT EXISTS "<?php die?>" (id int)');
        $this->pdb->exec('PRAGMA synchronous=OFF');
        $this->pdb->exec('PRAGMA short_column_names=1');
        $this->pdb->exec('PRAGMA encoding="UTF-8"');
        $this->pdb->exec('PRAGMA case_sensitive_like=0');
        $this->pdb->exec('PRAGMA locking_mode=NORMAL');
    }

    /**
     * Attempt to create the database
     * @param string $dbhost
     * @param string $dbuser
     * @param string $dbpass
     * @param string $dbname
     *
     * @return bool success
     */
    public function create_database($dbhost, $dbuser, $dbpass, $dbname, array $dboptions=null) {
        global $CFG;

        $this->dbhost = $dbhost;
        $this->dbuser = $dbuser;
        $this->dbpass = $dbpass;
        $this->dbname = $dbname;
        $filepath = $this->get_dbfilepath();
        $dirpath = dirname($filepath);
        @mkdir($dirpath, $CFG->directorypermissions, true);
        return touch($filepath);
    }

    /**
     * Returns the driver-dependent DSN for PDO based on members stored by connect.
     * Must be called after connect (or after $dbname, $dbhost, etc. members have been set).
     * @return string driver-dependent DSN
     */
    protected function get_dsn() {
        return 'sqlite:'.$this->get_dbfilepath();
    }

    /**
     * Returns the file path for the database file, computed from dbname and/or dboptions.
     * If dboptions['file'] is set, then it is used (use :memory: for in memory database);
     * else if dboptions['path'] is set, then the file will be <dboptions path>/<dbname>.sq3.php;
     * else if dbhost is set and not localhost, then the file will be <dbhost>/<dbname>.sq3.php;
     * else the file will be <moodle data path>/<dbname>.sq3.php
     * @return string file path to the SQLite database;
     */
    public function get_dbfilepath() {
        global $CFG;
        if (!empty($this->dboptions['file'])) {
            return $this->dboptions['file'];
        }
        if ($this->dbhost && $this->dbhost != 'localhost') {
            $path = $this->dbhost;
        } else {
            $path = $CFG->dataroot;
        }
        $path = rtrim($path, '\\/').'/';
        if (!empty($this->dbuser)) {
            $path .= $this->dbuser.'_';
        }
        $path .= $this->dbname.'_'.md5($this->dbpass).$this->database_file_extension;
        return $path;
    }

    /**
     * Return tables in database WITHOUT current prefix.
     * @param bool $usecache if true, returns list of cached tables.
     * @return array of table names in lowercase and without prefix
     */
    public function get_tables($usecache=true) {
        $tables = array();

        $sql = 'SELECT name FROM sqlite_master WHERE type="table" UNION ALL SELECT name FROM sqlite_temp_master WHERE type="table" ORDER BY name';
        if ($this->debug) {
            $this->debug_query($sql);
        }
        $rstables = $this->pdb->query($sql);
        foreach ($rstables as $table) {
            $table = $table['name'];
            $table = strtolower($table);
            if ($this->prefix !== false && $this->prefix !== '') {
                if (strpos($table, $this->prefix) !== 0) {
                    continue;
                }
                $table = substr($table, strlen($this->prefix));
            }
            $tables[$table] = $table;
        }
        return $tables;
    }

    /**
     * Return table indexes - everything lowercased
     * @param string $table The table we want to get indexes from.
     * @return array of arrays
     */
    public function get_indexes($table) {
        $indexes = array();
        $sql = 'PRAGMA index_list('.$this->prefix.$table.')';
        if ($this->debug) {
            $this->debug_query($sql);
        }
        $rsindexes = $this->pdb->query($sql);
        foreach($rsindexes as $index) {
            $unique = (boolean)$index['unique'];
            $index = $index['name'];
            $sql = 'PRAGMA index_info("'.$index.'")';
            if ($this->debug) {
                $this->debug_query($sql);
            }
            $rscolumns = $this->pdb->query($sql);
            $columns = array();
            foreach($rscolumns as $row) {
                $columns[] = strtolower($row['name']);
            }
            $index = strtolower($index);
            $indexes[$index]['unique'] = $unique;
            $indexes[$index]['columns'] = $columns;
        }
        return $indexes;
    }

    /**
     * Returns detailed information about columns in table.
     *
     * @param string $table name
     * @return array array of database_column_info objects indexed with column names
     */
    protected function fetch_columns(string $table): array {
        $structure = array();

        // get table's CREATE TABLE command (we'll need it for autoincrement fields)
        $sql = 'SELECT sql FROM sqlite_master WHERE type="table" AND tbl_name="'.$this->prefix.$table.'"';
        if ($this->debug) {
            $this->debug_query($sql);
        }
        $createsql = $this->pdb->query($sql)->fetch();
        if (!$createsql) {
            return false;
        }
        $createsql = $createsql['sql'];

        $sql = 'PRAGMA table_info("'. $this->prefix.$table.'")';
        if ($this->debug) {
            $this->debug_query($sql);
        }
        $rscolumns = $this->pdb->query($sql);
        foreach ($rscolumns as $row) {
            $columninfo = array(
                'name' => strtolower($row['name']), // colum names must be lowercase
                'not_null' =>(boolean)$row['notnull'],
                'primary_key' => (boolean)$row['pk'],
                'has_default' => !is_null($row['dflt_value']),
                'default_value' => $row['dflt_value'],
                'auto_increment' => false,
                'binary' => false,
                //'unsigned' => false,
            );
            $type = explode('(', $row['type']);
            $columninfo['type'] = strtolower($type[0]);
            if (count($type) > 1) {
                $size = explode(',', trim($type[1], ')'));
                $columninfo['max_length'] = $size[0];
                if (count($size) > 1) {
                    $columninfo['scale'] = $size[1];
                }
            }
            // SQLite does not have a fixed set of datatypes (ie. it accepts any string as
            // datatype in the CREATE TABLE command. We try to guess which type is used here
            switch(substr($columninfo['type'], 0, 3)) {
                case 'int': // int integer
                    if ($columninfo['primary_key'] && preg_match('/'.$columninfo['name'].'\W+integer\W+primary\W+key\W+autoincrement/im', $createsql)) {
                        $columninfo['meta_type'] = 'R';
                        $columninfo['auto_increment'] = true;
                    } else {
                        $columninfo['meta_type'] = 'I';
                    }
                    break;
                case 'num': // number numeric
                case 'rea': // real
                case 'dou': // double
                case 'flo': // float
                    $columninfo['meta_type'] = 'N';
                    break;
                case 'var': // varchar
                case 'cha': // char
                    $columninfo['meta_type'] = 'C';
                    break;
                case 'enu': // enums
                    $columninfo['meta_type'] = 'C';
                    break;
                case 'tex': // text
                case 'clo': // clob
                    $columninfo['meta_type'] = 'X';
                    break;
                case 'blo': // blob
                case 'non': // none
                    $columninfo['meta_type'] = 'B';
                    $columninfo['binary'] = true;
                    break;
                case 'boo': // boolean
                case 'bit': // bit
                case 'log': // logical
                    $columninfo['meta_type'] = 'L';
                    $columninfo['max_length'] = 1;
                    break;
                case 'tim': // timestamp
                    $columninfo['meta_type'] = 'T';
                    break;
                case 'dat': // date datetime
                    $columninfo['meta_type'] = 'D';
                    break;
            }
            if ($columninfo['has_default'] && ($columninfo['meta_type'] == 'X' || $columninfo['meta_type']== 'C')) {
                // trim extra quotes from text default values
                $columninfo['default_value'] = substr($columninfo['default_value'], 1, -1);
            }
            $structure[$columninfo['name']] = new database_column_info($columninfo);
        }

        return $structure;
    }

    /**
     * Normalise values based in RDBMS dependencies (booleans, LOBs...)
     *
     * @param database_column_info $column column metadata corresponding with the value we are going to normalise
     * @param mixed $value value we are going to normalise
     * @return mixed the normalised value
     */
    protected function normalise_value($column, $value) {
        return $value;
    }

    /**
     * Returns the sql statement with clauses to append used to limit a recordset range.
     * @param string $sql the SQL statement to limit.
     * @param int $limitfrom return a subset of records, starting at this point (optional, required if $limitnum is set).
     * @param int $limitnum return a subset comprising this many records (optional, required if $limitfrom is set).
     * @return string the SQL statement with limiting clauses
     */
    protected function get_limit_clauses($sql, $limitfrom=0, $limitnum=0) {
        if ($limitnum) {
            $sql .= ' LIMIT '.$limitnum;
            if ($limitfrom) {
                $sql .= ' OFFSET '.$limitfrom;
            }
        }
        return $sql;
    }

    /**
     * Delete the records from a table where all the given conditions met.
     * If conditions not specified, table is truncated.
     *
     * @param string $table the table to delete from.
     * @param array $conditions optional array $fieldname=>requestedvalue with AND in between
     * @return returns success.
     */
    public function delete_records($table, array $conditions=null) {
        if (is_null($conditions)) {
            return $this->execute("DELETE FROM {{$table}}");
        }
        list($select, $params) = $this->where_clause($table, $conditions);
        return $this->delete_records_select($table, $select, $params);
    }

    /**
     * Returns the proper SQL to do CONCAT between the elements passed
     * Can take many parameters
     *
     * @param string $elements,...
     * @return string
     */
    public function sql_concat(...$elements) {
        return implode('||', $elements);
    }

    /**
     * Returns the proper SQL to do CONCAT between the elements passed
     * with a given separator
     *
     * @param string $separator
     * @param array  $elements
     * @return string
     */
    public function sql_concat_join($separator="' '", $elements=array()) {
        // Intersperse $elements in the array.
        // Add items to the array on the fly, walking it
        // _backwards_ splicing the elements in. The loop definition
        // should skip first and last positions.
        for ($n=count($elements)-1; $n > 0; $n--) {
            array_splice($elements, $n, 0, $separator);
        }
        return implode('||', $elements);
    }

    /**
     * Returns the SQL text to be used in order to perform one bitwise XOR operation
     * between 2 integers.
     *
     * @param integer int1 first integer in the operation
     * @param integer int2 second integer in the operation
     * @return string the piece of SQL code to be used in your statement.
     */
    public function sql_bitxor($int1, $int2) {
        return '( ~' . $this->sql_bitand($int1, $int2) . ' & ' . $this->sql_bitor($int1, $int2) . ')';
    }
}
