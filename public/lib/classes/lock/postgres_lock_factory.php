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

namespace core\lock;

use coding_exception;

/**
 * Postgres advisory locking factory.
 *
 * Postgres locking implementation using advisory locks. Some important points. Postgres has
 * 2 different forms of lock functions, some accepting a single int, and some accepting 2 ints. This implementation
 * uses the 2 int version so that it uses a separate namespace from the session locking. The second note,
 * is because postgres uses integer keys for locks, we first need to map strings to a unique integer. This is done
 * using a prefix of a sha1 hash converted to an integer. There is a realistic chance of collisions by using this
 * prefix when locking multiple resources at the same time (multiple resource identifiers leading to the
 * same token/prefix). We need to deal with that.
 *
 * @package   core
 * @category  lock
 * @copyright Damyon Wiese 2013
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class postgres_lock_factory implements lock_factory {

    /** @var int $dblockid - used as a namespace for these types of locks (separate from session locks) */
    protected $dblockid = -1;

    /** @var array $lockidcache - static cache for string -> int conversions required for pg advisory locks. */
    protected static $lockidcache = array();

    /** @var \moodle_database $db Hold a reference to the global $DB */
    protected $db;

    /** @var string $type Used to prefix lock keys */
    protected $type;

    /** @var int[] $resourcetokens Mapping of held locks (resource identifier => internal token) */
    protected $resourcetokens = [];

    /** @var int[] $locksbytoken Mapping of held locks (db connection => internal token => number of locks held) */
    static protected $locksbytoken = [];

    /**
     * Calculate a unique instance id based on the database name and prefix.
     * @return int.
     */
    protected function get_unique_db_instance_id() {
        global $CFG;

        $strkey = $CFG->dbname . ':' . $CFG->prefix;
        $intkey = crc32($strkey);
        // Normalize between 64 bit unsigned int and 32 bit signed ints. Php could return either from crc32.
        if (PHP_INT_SIZE == 8) {
            if ($intkey > 0x7FFFFFFF) {
                $intkey -= 0x100000000;
            }
        }

        return $intkey;
    }

    /**
     * Almighty constructor.
     * @param string $type - Used to prefix lock keys.
     */
    public function __construct($type) {
        global $DB;

        $this->type = $type;
        $this->dblockid = $this->get_unique_db_instance_id();
        // Save a reference to the global $DB so it will not be released while we still have open locks.
        $this->db = $DB;

        \core_shutdown_manager::register_function(array($this, 'auto_release'));
    }

    /**
     * Is available.
     * @return boolean - True if this lock type is available in this environment.
     */
    public function is_available() {
        return $this->db->get_dbfamily() === 'postgres';
    }

    /**
     * Return information about the blocking behaviour of the lock type on this platform.
     * @return boolean - Defer to the DB driver.
     */
    public function supports_timeout() {
        return true;
    }

    /**
     * Will this lock type will be automatically released when a process ends.
     *
     * @return boolean - Via shutdown handler.
     */
    public function supports_auto_release() {
        return true;
    }

    /**
     * This function generates the unique index for a specific lock key using
     * a sha1 prefix converted to decimal.
     *
     * @param string $key
     * @return int
     * @throws \moodle_exception
     */
    protected function get_index_from_key($key) {

        // A prefix of 7 hex chars is chosen as fffffff is the largest hex code
        // which when converted to decimal (268435455) fits inside a 4 byte int
        // which is the second param to pg_try_advisory_lock().
        $hash = substr(sha1($key), 0, 7);
        $index = hexdec($hash);
        return $index;
    }

    /**
     * Create and get a lock
     *
     * @param string $resource - The identifier for the lock. Should use frankenstyle prefix.
     * @param int $timeout - The number of seconds to wait for a lock before giving up.
     * @param int $maxlifetime - Unused by this lock type.
     * @return \core\lock\lock|boolean - An instance of \core\lock\lock if the lock was obtained, or false.
     */
    public function get_lock($resource, $timeout, $maxlifetime = 86400) {
        $dbid = spl_object_id($this->db);
        $giveuptime = time() + $timeout;
        $resourcekey = $this->type . '_' . $resource;
        $token = $this->get_index_from_key($resourcekey);

        if (isset($this->resourcetokens[$resourcekey])) {
            return false;
        }

        if (isset(self::$locksbytoken[$dbid][$token])) {
            // There is a hash collision, another resource identifier leads to the same token.
            // As we already hold an advisory lock for this token, we just increase the counter.
            self::$locksbytoken[$dbid][$token]++;
            $this->resourcetokens[$resourcekey] = $token;
            return new lock($resourcekey, $this);
        }

        $params = [
            'locktype' => $this->dblockid,
            'token' => $token
        ];

        $locked = false;

        do {
            $result = $this->db->get_record_sql('SELECT pg_try_advisory_lock(:locktype, :token) AS locked', $params);
            $locked = $result->locked === 't';
            if (!$locked && $timeout > 0) {
                usleep(rand(10000, 250000)); // Sleep between 10 and 250 milliseconds.
            }
            // Try until the giveup time.
        } while (!$locked && time() < $giveuptime);

        if ($locked) {
            self::$locksbytoken[$dbid][$token] = 1;
            $this->resourcetokens[$resourcekey] = $token;
            return new lock($resourcekey, $this);
        }
        return false;
    }

    /**
     * Release a lock that was previously obtained with @lock.
     * @param lock $lock - a lock obtained from this factory.
     * @return boolean - true if the lock is no longer held (including if it was never held).
     */
    public function release_lock(lock $lock) {
        $dbid = spl_object_id($this->db);
        $resourcekey = $lock->get_key();

        if (isset($this->resourcetokens[$resourcekey])) {
            $token = $this->resourcetokens[$resourcekey];
        } else {
            return true;
        }

        if (self::$locksbytoken[$dbid][$token] > 1) {
            // There are multiple resource identifiers that lead to the same token.
            // We will unlock the token later when the last resource is released.
            unset($this->resourcetokens[$resourcekey]);
            self::$locksbytoken[$dbid][$token]--;
            return true;
        }

        $params = [
            'locktype' => $this->dblockid,
            'token' => $token,
        ];
        $result = $this->db->get_record_sql('SELECT pg_advisory_unlock(:locktype, :token) AS unlocked', $params);
        $result = $result->unlocked === 't';
        if ($result) {
            unset($this->resourcetokens[$resourcekey]);
            unset(self::$locksbytoken[$dbid][$token]);
        }
        return $result;
    }

    /**
     * Auto release any open locks on shutdown.
     * This is required, because we may be using persistent DB connections.
     */
    public function auto_release() {
        // Called from the shutdown handler. Must release all open locks.
        foreach ($this->resourcetokens as $resourcekey => $unused) {
            $lock = new lock($resourcekey, $this);
            $lock->release();
        }
    }

}
