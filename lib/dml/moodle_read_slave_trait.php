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
 * Trait that adds read-only slave connection capability
 *
 * @package    core
 * @category   dml
 * @copyright  2018 Srdjan Janković, Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

trait moodle_read_slave_trait {

    /** @var resource master write database handle */
    protected $dbhwrite;

    /** @var resource slave read only database handle */
    protected $dbhreadonly;

    private $wantreadslave = false;
    private $readsslave = 0;
    private $slavelatency = 0;

    private $written = [];
    private $readexclude = [];

    private $pdbhost;
    private $pdbuser;
    private $pdbpass;
    private $pdbname;
    private $pprefix;
    private $pdboptions;

    /**
     * Gets db handle currently used with queries
     * @return resource
     */
    abstract protected function db_handle();

    /**
     * Sets db handle to be used with subsequent queries
     * @param resource $dbh
     * @return void
     */
    abstract protected function set_db_handle($dbh);

    /**
     * Connect to db
     * @param string $dbhost The database host.
     * @param string $dbuser The database username.
     * @param string $dbpass The database username's password.
     * @param string $dbname The name of the database being connected to.
     * @param mixed $prefix string means moodle db prefix, false used for external databases where prefix not used
     * @param array $dboptions driver specific options
     * @return bool true
     * @throws dml_connection_exception if error
     */
    abstract protected function _connect($dbhost, $dbuser, $dbpass, $dbname, $prefix, array $dboptions=null);

    /**
     * Connect to db
     * Must be called before other methods.
     * @param string $dbhost The database host.
     * @param string $dbuser The database username.
     * @param string $dbpass The database username's password.
     * @param string $dbname The name of the database being connected to.
     * @param mixed $prefix string means moodle db prefix, false used for external databases where prefix not used
     * @param array $dboptions driver specific options
     * @return bool true
     * @throws dml_connection_exception if error
     */
    public function connect($dbhost, $dbuser, $dbpass, $dbname, $prefix, array $dboptions=null) {
        $this->pdbhost = $dbhost;
        $this->pdbuser = $dbuser;
        $this->pdbpass = $dbpass;
        $this->pdbname = $dbname;
        $this->pprefix = $prefix;
        $this->pdboptions = $dboptions;

        if ($dboptions) {
            if (isset($dboptions['readonly'])) {
                $this->wantreadslave = true;
                $dboptionsro = $dboptions['readonly'];

                if (isset($dboptionsro['connecttimeout'])) {
                    $dboptions['connecttimeout'] = $dboptionsro['connecttimeout'];
                } else if (!isset($dboptions['connecttimeout'])) {
                    $dboptions['connecttimeout'] = 2; // Default readonly connection timeout.
                }
                if (isset($dboptionsro['latency'])) {
                    $this->slavelatency = $dboptionsro['latency'];
                }
                if (isset($dboptionsro['exclude_tables'])) {
                    $this->readexclude = $dboptionsro['exclude_tables'];
                    if (!is_array($this->readexclude)) {
                        throw new configuration_exception('exclude_tables must be an array');
                    }
                }
                $dbport = isset($dboptions['dbport']) ? $dboptions['dbport'] : null;

                $ro = $dboptionsro['instance'];
                if (!is_array($ro) || !isset($ro[0])) {
                    $ro = [$ro];
                }
                foreach ($ro as $ro1) {
                    if (!is_array($ro1)) {
                        $ro1 = ['dbhost' => $ro1];
                    }
                    foreach (['dbhost', 'dbuser', 'dbpass'] as $v) {
                        $vro = "${v}ro";
                        $$vro = isset($ro1[$v]) ? $ro1[$v] : $$v;
                    }
                    $dboptions['dbport'] = isset($ro1['dbport']) ? $ro1['dbport'] : $dbport;

                    // @codingStandardsIgnoreStart
                    try {
                        $this->_connect($dbhostro, $dbuserro, $dbpassro, $dbname, $prefix, $dboptions);
                        $this->dbhreadonly = $this->db_handle();
                        break;
                    } catch (dml_connection_exception $e) {
                        // If readonly slave is not connectable we'll have to do without it.
                    }
                    // @codingStandardsIgnoreEnd
                }
            }
        }
        if (!$this->dbhreadonly) {
            $this->set_dbhwrite();
        }

        return true;
    }

    /**
     * Set database handle to readwrite master
     * Will connect if required. Calls set_db_handle()
     * @return void
     */
    private function set_dbhwrite() {
        // Late connect to read/write master if needed.
        if (!$this->dbhwrite) {
            $this->_connect($this->pdbhost, $this->pdbuser, $this->pdbpass, $this->pdbname, $this->pprefix, $this->pdboptions);
            $this->dbhwrite = $this->db_handle();
        }
        $this->set_db_handle($this->dbhwrite);
    }

    /**
     * Returns whether we want to connect to slave database for read queries.
     * @return bool Want read only connection
     */
    public function want_read_slave() {
        return $this->wantreadslave;
    }

    /**
     * Returns the number of reads done by the read only database.
     * @return int Number of reads.
     */
    public function perf_get_reads_slave() {
        return $this->readsslave;
    }

    /**
     * On DBs that support it, switch to transaction mode and begin a transaction
     * @return moodle_transaction
     */
    public function start_delegated_transaction() {
        $this->set_dbhwrite();
        return parent::start_delegated_transaction();
    }

    /**
     * Called before each db query.
     * @param string $sql
     * @param array $params array of parameters
     * @param int $type type of query
     * @param mixed $extrainfo driver specific extra information
     * @return void
     */
    protected function query_start($sql, array $params=null, $type, $extrainfo=null) {
        parent::query_start($sql, $params, $type, $extrainfo);
        $this->select_db_handle($type, $sql);
    }

    /**
     * Select appropriate db handle - readwrite or readonly
     * @param int $type type of query
     * @param string $sql
     * @return void
     */
    protected function select_db_handle($type, $sql) {
        if ($this->dbhreadonly && $this->_query_is_ro($type, $sql)) {
                $this->readsslave++;
                $this->set_db_handle($this->dbhreadonly);
                return;
        }
        $this->set_dbhwrite();
    }

    /**
     * Check if The query qualifies for readonly connection execution
     * @param int $type type of query
     * @param string $sql
     * @return bool
     */
    private function _query_is_ro($type, $sql) {
        if ($this->transactions) {
            return false;
        }

        if ($this->loggingquery) {
            return false;
        }

        // ... lock_db queries always go to master.
        if (preg_match('/lock_db\b/', $sql)) {
            return false;
        }

        // Transactions are done as AUX, we cannot play with that.
        switch ($type) {
            case SQL_QUERY_SELECT:
                $now = null;
                foreach ($this->table_names($sql) as $t) {
                    if (in_array($t, $this->readexclude)) {
                        return false;
                    }

                    if ($this->temptables && $this->temptables->is_temptable($t)) {
                        return false;
                    }

                    if (isset($this->written[$t])) {
                        if ($this->slavelatency) {
                            $now = $now ?: microtime(true);
                            if ($now - $this->written[$t] < $this->slavelatency) {
                                return false;
                            }
                        } else {
                            return false;
                        }
                    }
                }

                return true;
            case SQL_QUERY_INSERT:
            case SQL_QUERY_UPDATE:
                $now = $this->slavelatency ? microtime(true) : true;
                foreach ($this->table_names($sql) as $t) {
                    $this->written[$t] = $now;
                }
                return false;
            case SQL_QUERY_STRUCTURE:
                foreach ($this->table_names($sql) as $t) {
                    if (!in_array($t, $this->readexclude)) {
                        $this->readexclude[] = $t;
                    }
                }
                return false;
        }
        return false;
    }

    /**
     * Parse table names from query
     * @param string $sql
     * @return array
     */
    protected function table_names($sql) {
        preg_match_all('/\b'.$this->prefix.'([a-z][A-Za-z0-9_]*)/', $sql, $match);
        return $match[1];
    }
}
