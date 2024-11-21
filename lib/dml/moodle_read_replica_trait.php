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
 * Trait that adds read-only replica connection capability.
 *
 * Trait to wrap connect() method of database driver classes that gives
 * ability to use read only replica instances for SELECT queries. For the
 * databases that support replication and read only connections to the replica.
 * If the replica connection is configured there will be two database handles
 * created, one for the primary and another one for the replica. If there's no
 * replica specified everything uses primary handle.
 *
 * Classes that use this trait need to rename existing connect() method to
 * raw_connect(). In addition, they need to provide get_db_handle() and
 * set_db_handle() methods, due to dbhandle attributes not being named
 * consistently across the database driver classes.
 *
 * Read only replica connection is configured in the $CFG->dboptions['readonly']
 * array.
 * - It supports multiple 'instance' entries, in case one is not accessible,
 *   but only one (first connectable) instance is used.
 * - 'latency' option: primary -> replica sync latency in seconds (will probably
 *   be a fraction of a second). A table being written to is deemed fully synced
 *   after that period and suitable for replica read. Defaults to 1 sec.
 * - 'exclude_tables' option: a list of tables that never go to the replica for
 *   querying. The feature is meant to be used in emergency only, so the
 *   readonly feature can still be used in case there is a rogue query that
 *   does not go through the standard dml interface or some other unaccounted
 *   situation. It should not be used under normal circumstances, and its use
 *   indicates a problem in the system that needs addressig.
 *
 * Choice of the database handle is based on following:
 * - SQL_QUERY_INSERT, UPDATE and STRUCTURE record table from the query
 *   in the $written array and microtime() the event. For those queries primary
 *   write handle is used.
 * - SQL_QUERY_AUX queries will always use the primary write handle because they
 *   are used for transaction start/end, locking etc. In that respect, query_start() and
 *   query_end() *must not* be used during the connection phase.
 * - SQL_QUERY_AUX_READONLY queries will use the primary write handle if in a transaction.
 * - SELECT queries will use the primary write handle if:
 *   -- any of the tables involved is a temp table
 *   -- any of the tables involved is listed in the 'exclude_tables' option
 *   -- any of the tables involved is in the $written array:
 *      * current microtime() is compared to the write microrime, and if more than
 *        latency time has passed the replica handle is used
 *      * otherwise (not enough time passed) we choose the primary write handle
 *   If none of the above conditions are met the replica instance is used.
 *
 * A 'latency' example:
 *  - we have set $CFG->dboptions['readonly']['latency'] to 0.2.
 *  - a SQL_QUERY_UPDATE to table tbl_x happens, and it is recorded in
 *    the $written array
 *  - 0.15 seconds later SQL_QUERY_SELECT with tbl_x is requested - the primary
 *    connection is used
 *  - 0.10 seconds later (0.25 seconds after SQL_QUERY_UPDATE) another
 *    SQL_QUERY_SELECT with tbl_x is requested - this time more than 0.2 secs
 *    has gone and primary -> replica sync is assumed, so the replica connection is
 *    used again.
 *
 * @package    core
 * @category   dml
 * @copyright  2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait moodle_read_replica_trait {

    /** @var resource Primary write database handle. */
    protected $dbhwrite;

    /** @var resource Replica read only database handle. */
    protected $dbhreadonly;

    /** @var bool Connect to replica database for read queries. */
    private $wantreadreplica = false;

    /** @var int The number of reads done by the read only database. */
    private $readsreplica = 0;

    /** @var int Replica letency in seconds. */
    private $replicalatency = 1;

    /** @var bool Structure changed status. */
    private $structurechange = false;

    /** @var array Track tables being written to. */
    private $written = [];

    /** @var array Tables to exclude from using dbhreadonly. */
    private $readexclude = [];

    /** @var string The database host. */
    private $pdbhost;

    /** @var string The database username. */
    private $pdbuser;

    /** @var string The database username's password. */
    private $pdbpass;

    /** @var string The name of the database being connected to. */
    private $pdbname;

    /** @var mixed String means moodle db prefix, false used for external databases where prefix not used. */
    private $pprefix;

    /** @var array|null Driver specific options. */
    private $pdboptions;

    /**
     * Gets db handle currently used with queries.
     *
     * @return resource
     */
    abstract protected function get_db_handle();

    /**
     * Sets db handle to be used with subsequent queries.
     *
     * @param resource $dbh
     */
    abstract protected function set_db_handle($dbh): void;

    /**
     * Connect to db.
     *
     * The real connection establisment, called from connect() and set_dbhwrite().
     *
     * @param string $dbhost The database host.
     * @param string $dbuser The database username.
     * @param string $dbpass The database username's password.
     * @param string $dbname The name of the database being connected to.
     * @param mixed $prefix String means moodle db prefix, false used for external databases where prefix not used.
     * @param array|null $dboptions Driver specific options.
     * @return bool
     * @throws dml_connection_exception
     */
    abstract protected function raw_connect(
        string $dbhost,
        string $dbuser,
        string $dbpass,
        string $dbname,
        $prefix,
        ?array $dboptions = null
    ): bool;

    /**
     * Connect to db.
     *
     * The connection parameters processor that sets up stage for primary write and replica readonly handles.
     * Must be called before other methods.
     *
     * @param string $dbhost The database host.
     * @param string $dbuser The database username.
     * @param string $dbpass The database username's password.
     * @param string $dbname The name of the database being connected to.
     * @param mixed $prefix String means moodle db prefix, false used for external databases where prefix not used.
     * @param array|null $dboptions Driver specific options.
     * @return bool
     * @throws dml_connection_exception
     */
    public function connect($dbhost, $dbuser, $dbpass, $dbname, $prefix, ?array $dboptions = null) {
        $this->pdbhost = $dbhost;
        $this->pdbuser = $dbuser;
        $this->pdbpass = $dbpass;
        $this->pdbname = $dbname;
        $this->pprefix = $prefix;
        $this->pdboptions = $dboptions;

        $logconnection = false;
        if ($dboptions) {
            if (isset($dboptions['readonly'])) {
                $this->wantreadreplica = true;
                $dboptionsro = $dboptions['readonly'];

                if (isset($dboptionsro['connecttimeout'])) {
                    $dboptions['connecttimeout'] = $dboptionsro['connecttimeout'];
                } else if (!isset($dboptions['connecttimeout'])) {
                    $dboptions['connecttimeout'] = 2; // Default readonly connection timeout.
                }
                if (isset($dboptionsro['latency'])) {
                    $this->replicalatency = $dboptionsro['latency'];
                }
                if (isset($dboptionsro['exclude_tables'])) {
                    $this->readexclude = $dboptionsro['exclude_tables'];
                    if (!is_array($this->readexclude)) {
                        throw new configuration_exception('exclude_tables must be an array');
                    }
                }
                $dbport = isset($dboptions['dbport']) ? $dboptions['dbport'] : null;

                $replicas = $dboptionsro['instance'];
                if (!is_array($replicas) || !isset($replicas[0])) {
                    $replicas = [$replicas];
                }

                if (count($replicas) > 1) {
                    // Don't shuffle for unit tests as order is important for them to pass.
                    if (!PHPUNIT_TEST) {
                        // Randomise things a bit.
                        shuffle($replicas);
                    }
                }

                // Find first connectable readonly replica.
                $rodb = [];
                foreach ($replicas as $replica) {
                    if (!is_array($replica)) {
                        $replica = ['dbhost' => $replica];
                    }
                    foreach (['dbhost', 'dbuser', 'dbpass'] as $dbparam) {
                        $rodb[$dbparam] = isset($replica[$dbparam]) ? $replica[$dbparam] : $$dbparam;
                    }
                    $dboptions['dbport'] = isset($replica['dbport']) ? $replica['dbport'] : $dbport;

                    try {
                        $this->raw_connect($rodb['dbhost'], $rodb['dbuser'], $rodb['dbpass'], $dbname, $prefix, $dboptions);
                        $this->dbhreadonly = $this->get_db_handle();
                        if ($logconnection) {
                            debugging(
                                "Readonly db connection succeeded for host {$rodb['dbhost']}"
                            );
                        }
                        break;
                    } catch (dml_connection_exception $e) {
                        debugging(
                            "Readonly db connection failed for host {$rodb['dbhost']}: {$e->debuginfo}"
                        );
                        $logconnection = true;
                    }
                }
                // ... lock_db queries always go to primary.
                // Since it is a lock and as such marshalls concurrent connections,
                // it is best to leave it out and avoid primary/replica latency.
                $this->readexclude[] = 'lock_db';
                // ... and sessions.
                $this->readexclude[] = 'sessions';
            }
        }
        if (!$this->dbhreadonly) {
            try {
                $this->set_dbhwrite();
            } catch (dml_connection_exception $e) {
                debugging(
                    "Readwrite db connection failed for host {$this->pdbhost}: {$e->debuginfo}"
                );
                throw $e;
            }
            if ($logconnection) {
                debugging(
                    "Readwrite db connection succeeded for host {$this->pdbhost}"
                );
            }
        }

        return true;
    }

    /**
     * Set database handle to readwrite primary.
     *
     * Will connect if required. Calls set_db_handle().
     */
    private function set_dbhwrite(): void {
        // Lazy connect to read/write primary.
        if (!$this->dbhwrite) {
            $temptables = $this->temptables;
            $this->raw_connect($this->pdbhost, $this->pdbuser, $this->pdbpass, $this->pdbname, $this->pprefix, $this->pdboptions);
            if ($temptables) {
                $this->temptables = $temptables; // Restore temptables, so we don't get separate sets for rw and ro.
            }
            $this->dbhwrite = $this->get_db_handle();
        }
        $this->set_db_handle($this->dbhwrite);
    }

    /**
     * Returns whether we want to connect to replica database for read queries.
     *
     * @return bool Want read only connection.
     */
    public function want_read_replica(): bool {
        return $this->wantreadreplica;
    }

    /**
     * Returns the number of reads done by the read only database.
     *
     * @return int Number of reads.
     */
    public function perf_get_reads_replica(): int {
        return $this->readsreplica;
    }

    /**
     * On DBs that support it, switch to transaction mode and begin a transaction.
     *
     * @return moodle_transaction
     */
    public function start_delegated_transaction() {
        $this->set_dbhwrite();
        return parent::start_delegated_transaction();
    }

    /**
     * Called before each db query.
     *
     * @param string $sql
     * @param array|null $params An array of parameters.
     * @param int $type type of query
     * @param mixed $extrainfo driver specific extra information
     */
    protected function query_start($sql, ?array $params, $type, $extrainfo = null) {
        parent::query_start($sql, $params, $type, $extrainfo);
        $this->select_db_handle($type, $sql);
    }

    /**
     * This should be called immediately after each db query. It does a clean up of resources.
     *
     * @param mixed $result The db specific result obtained from running a query.
     */
    protected function query_end($result) {
        if ($this->written) {
            // Adjust the written time.
            array_walk($this->written, function (&$val) {
                if ($val === true) {
                    $val = microtime(true);
                }
            });
        }

        parent::query_end($result);
    }

    /**
     * Select appropriate db handle - readwrite or readonly.
     *
     * @param int $type Type of query.
     * @param string $sql The sql to use.
     */
    protected function select_db_handle(int $type, string $sql): void {
        if ($this->dbhreadonly && $this->can_use_readonly($type, $sql)) {
            $this->readsreplica++;
            $this->set_db_handle($this->dbhreadonly);
            return;
        }
        $this->set_dbhwrite();
    }

    /**
     * Check if The query qualifies for readonly connection execution.
     *
     * Logging queries are exempt, those are write operations that circumvent standard query_start/query_end paths.
     *
     * @param int $type Type of query.
     * @param string $sql The sql to use.
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
            case SQL_QUERY_AUX_READONLY:
                // SQL_QUERY_AUX_READONLY may read the structure data.
                // We don't have a way to reliably determine whether it is safe to go to readonly if the structure has changed.
                return !$this->structurechange;
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
                        $now = $now ?: microtime(true);

                        if ($now - $this->written[$tablename] < $this->replicalatency) {
                            return false;
                        }
                        unset($this->written[$tablename]);
                    }
                }

                return true;
            case SQL_QUERY_INSERT:
            case SQL_QUERY_UPDATE:
                foreach ($this->table_names($sql) as $tablename) {
                    $this->written[$tablename] = true;
                }
                return false;
            case SQL_QUERY_STRUCTURE:
                $this->structurechange = true;
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
     *
     * Set written times after outermost transaction finished.
     *
     * @param moodle_transaction $transaction The transaction to commit.
     * @throws dml_transaction_exception Creates and throws transaction related exceptions.
     */
    public function commit_delegated_transaction(moodle_transaction $transaction) {
        if ($this->written) {
            // Adjust the written time.
            $now = microtime(true);
            foreach ($this->written as $tablename => $when) {
                $this->written[$tablename] = $now;
            }
        }

        parent::commit_delegated_transaction($transaction);
    }

    /**
     * Parse table names from query.
     *
     * @param string $sql The sql to use.
     * @return array
     */
    protected function table_names(string $sql): array {
        preg_match_all('/\b'.$this->prefix.'([a-z][A-Za-z0-9_]*)/', $sql, $match);
        return $match[1];
    }
}
