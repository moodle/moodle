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

/**
 * Trait to wrap connect() method of database driver classes that gives
 * ability to use read only slave instances for SELECT queries. For the
 * databases that support replication and read only connections to the slave.
 * If the slave connection is configured there will be two database handles
 * created, one for the master and another one for the slave. If there's no
 * slave specified everything uses master handle.
 *
 * Classes that use this trait need to rename existing connect() method to
 * raw_connect(). In addition, they need to provide get_db_handle() and
 * set_db_handle() methods, due to dbhandle attributes not being named
 * consistently across the database driver classes.
 *
 * Read only slave connection is configured in the $CFG->dboptions['readonly']
 * array.
 * - It supports multiple 'instance' entries, in case one is not accessible,
 *   but only one (first connectable) instance is used.
 * - 'latency' option: master -> slave sync latency in seconds (will probably
 *   be a fraction of a second). If specified, a table being written to is
 *   deemed fully synced and suitable for slave read.
 * - 'exclude_tables' option: a list of tables that never go to the slave for
 *   querying. The feature is meant to be used in emergency only, so the
 *   readonly feature can still be used in case there is a rogue query that
 *   does not go through the standard dml interface or some other unaccounted
 *   situation. It should not be used under normal circumstances, and its use
 *   indicates a problem in the system that needs addressig.
 *
 * Choice of the database handle is based on following:
 * - SQL_QUERY_INSERT, UPDATE and STRUCTURE record table from the query
 *   in the $written array and microtime() the event if the 'latency' option
 *   is set. For those queries master write handle is used.
 * - SQL_QUERY_AUX queries will always use the master write handle because they
 *   are used for transactionstart/end, locking etc. In that respect, query_start() and
 *   query_end() *must not* be used during the connection phase.
 * - SELECT queries will use the master write handle if:
 *   -- any of the tables involved is a temp table
 *   -- any of the tables involved is listed in the 'exclude_tables' option
 *   -- any of the tables involved is in the $written array:
 *      * If the 'latency' option is set then the microtime() is compared to
 *        the write microrime, and if more then latency time has passed the slave
 *        handle is used.
 *      * Otherwise (not enough time passed or 'latency' option not set)
 *        we choose the master write handle
 *   If none of the above conditions are met the slave instance is used.
 *
 * A 'latency' example:
 *  - we have set $CFG->dboptions['readonly']['latency'] to 0.2.
 *  - a SQL_QUERY_UPDATE to table tbl_x happens, and it is recorded in
 *    the $written array
 *  - 0.15 seconds later SQL_QUERY_SELECT with tbl_x is requested - the master
 *    connection is used
 *  - 0.10 seconds later (0.25 seconds after SQL_QUERY_UPDATE) another
 *    SQL_QUERY_SELECT with tbl_x is requested - this time more than 0.2 secs
 *    has gone and master -> slave sync is assumed, so the slave connection is
 *    used again
 */

trait moodle_read_slave_trait {

    /** @var resource master write database handle */
    protected $dbhwrite;

    /** @var resource slave read only database handle */
    protected $dbhreadonly;

    private $wantreadslave = false;
    private $readsslave = 0;
    private $slavelatency = 0;

    private $written = []; // Track tables being written to.
    private $readexclude = []; // Tables to exclude from using dbhreadonly.

    // Store original params.
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
    abstract protected function get_db_handle();

    /**
     * Sets db handle to be used with subsequent queries
     * @param resource $dbh
     * @return void
     */
    abstract protected function set_db_handle($dbh): void;

    /**
     * Connect to db
     * The real connection establisment, called from connect() and set_dbhwrite()
     * @param string $dbhost The database host.
     * @param string $dbuser The database username.
     * @param string $dbpass The database username's password.
     * @param string $dbname The name of the database being connected to.
     * @param mixed $prefix string means moodle db prefix, false used for external databases where prefix not used
     * @param array $dboptions driver specific options
     * @return bool true
     * @throws dml_connection_exception if error
     */
    abstract protected function raw_connect(string $dbhost, string $dbuser, string $dbpass, string $dbname, $prefix, array $dboptions = null): bool;

    /**
     * Connect to db
     * The connection parameters processor that sets up stage for master write and slave readonly handles.
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
    public function connect($dbhost, $dbuser, $dbpass, $dbname, $prefix, array $dboptions = null) {
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

                $slaves = $dboptionsro['instance'];
                if (!is_array($slaves) || !isset($slaves[0])) {
                    $slaves = [$slaves];
                }

                if (count($slaves) > 1) {
                    // Randomise things a bit.
                    shuffle($slaves);
                }

                // Find first connectable readonly slave.
                $rodb = [];
                foreach ($slaves as $slave) {
                    if (!is_array($slave)) {
                        $slave = ['dbhost' => $slave];
                    }
                    foreach (['dbhost', 'dbuser', 'dbpass'] as $dbparam) {
                        $rodb[$dbparam] = isset($slave[$dbparam]) ? $slave[$dbparam] : $$dbparam;
                    }
                    $dboptions['dbport'] = isset($slave['dbport']) ? $slave['dbport'] : $dbport;

                    // @codingStandardsIgnoreStart
                    try {
                        $this->raw_connect($rodb['dbhost'], $rodb['dbuser'], $rodb['dbpass'], $dbname, $prefix, $dboptions);
                        $this->dbhreadonly = $this->get_db_handle();
                        break;
                    } catch (dml_connection_exception $e) {
                        // If readonly slave is not connectable we'll have to do without it.
                    }
                    // @codingStandardsIgnoreEnd
                }
                // ... lock_db queries always go to master.
                // Since it is a lock and as such marshalls concurrent connections,
                // it is best to leave it out and avoid master/slave latency.
                $this->readexclude[] = 'lock_db';
                // ... and sessions.
                $this->readexclude[] = 'sessions';
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
    private function set_dbhwrite(): void {
        // Late connect to read/write master if needed.
        if (!$this->dbhwrite) {
            $this->raw_connect($this->pdbhost, $this->pdbuser, $this->pdbpass, $this->pdbname, $this->pprefix, $this->pdboptions);
            $this->dbhwrite = $this->get_db_handle();
        }
        $this->set_db_handle($this->dbhwrite);
    }

    /**
     * Returns whether we want to connect to slave database for read queries.
     * @return bool Want read only connection
     */
    public function want_read_slave(): bool {
        return $this->wantreadslave;
    }

    /**
     * Returns the number of reads done by the read only database.
     * @return int Number of reads.
     */
    public function perf_get_reads_slave(): int {
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
    protected function query_start($sql, array $params = null, $type, $extrainfo = null) {
        parent::query_start($sql, $params, $type, $extrainfo);
        $this->select_db_handle($type, $sql);
    }

    /**
     * Select appropriate db handle - readwrite or readonly
     * @param int $type type of query
     * @param string $sql
     * @return void
     */
    protected function select_db_handle(int $type, string $sql): void {
        if ($this->dbhreadonly && $this->can_use_readonly($type, $sql)) {
            $this->readsslave++;
            $this->set_db_handle($this->dbhreadonly);
            return;
        }
        $this->set_dbhwrite();
    }

    /**
     * Check if The query qualifies for readonly connection execution
     * Logging queries are exempt, those are write operations that circumvent
     * standard query_start/query_end paths.
     * @param int $type type of query
     * @param string $sql
     * @return bool
     */
    protected function can_use_readonly(int $type, string $sql): bool {
        if ($this->loggingquery) {
            return false;
        }

        if (during_initial_install()) {
            return false;
        }

        // Transactions are done as AUX, we cannot play with that.
        switch ($type) {
            case SQL_QUERY_SELECT:
                if ($this->transactions) {
                    return false;
                }

                $now = null;
                foreach ($this->table_names($sql) as $tablename) {
                    if (in_array($tablename, $this->readexclude)) {
                        return false;
                    }

                    if ($this->temptables && $this->temptables->is_temptable($tablename)) {
                        return false;
                    }

                    if (isset($this->written[$tablename])) {
                        if ($this->slavelatency) {
                            $now = $now ?: microtime(true);
                            if ($now - $this->written[$tablename] < $this->slavelatency) {
                                return false;
                            }
                            unset($this->written[$tablename]);
                        } else {
                            return false;
                        }
                    }
                }

                return true;
            case SQL_QUERY_INSERT:
            case SQL_QUERY_UPDATE:
                // If we are in transaction we cannot set the written time yet.
                $now = $this->slavelatency && !$this->transactions ? microtime(true) : true;
                foreach ($this->table_names($sql) as $tablename) {
                    $this->written[$tablename] = $now;
                }
                return false;
            case SQL_QUERY_STRUCTURE:
                foreach ($this->table_names($sql) as $tablename) {
                    if (!in_array($tablename, $this->readexclude)) {
                        $this->readexclude[] = $tablename;
                    }
                }
                return false;
        }
        return false;
    }

    /**
     * Indicates delegated transaction finished successfully.
     * Set written times after outermost transaction finished
     * @param moodle_transaction $transaction The transaction to commit
     * @return void
     * @throws dml_transaction_exception Creates and throws transaction related exceptions.
     */
    public function commit_delegated_transaction(moodle_transaction $transaction) {
        parent::commit_delegated_transaction($transaction);

        if ($this->transactions) {
            return;
        }

        if (!$this->slavelatency) {
            return;
        }

        $now = null;
        foreach ($this->written as $tablename => $when) {
            if ($when === true) {
                $now = $now ?: microtime(true);
                $this->written[$tablename] = $now;
            }
        }
    }

    /**
     * Parse table names from query
     * @param string $sql
     * @return array
     */
    protected function table_names(string $sql): array {
        preg_match_all('/\b'.$this->prefix.'([a-z][A-Za-z0-9_]*)/', $sql, $match);
        return $match[1];
    }
}
