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
 * Postgres advisory locking factory.
 *
 * @package    core
 * @category   lock
 * @copyright  Damyon Wiese 2013
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\lock;

defined('MOODLE_INTERNAL') || die();

/**
 * Postgres advisory locking factory.
 *
 * Postgres locking implementation using advisory locks. Some important points. Postgres has
 * 2 different forms of lock functions, some accepting a single int, and some accepting 2 ints. This implementation
 * uses the 2 int version so that it uses a separate namespace from the session locking. The second note,
 * is because postgres uses integer keys for locks, we first need to map strings to a unique integer. This is done
 * using a prefix of a sha1 hash converted to an integer.
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

    /** @var array $openlocks - List of held locks - used by auto-release */
    protected $openlocks = array();

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
     * Multiple locks for the same resource can be held by a single process.
     * @return boolean - Defer to the DB driver.
     */
    public function supports_recursion() {
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
     * @param string $resource - The identifier for the lock. Should use frankenstyle prefix.
     * @param int $timeout - The number of seconds to wait for a lock before giving up.
     * @param int $maxlifetime - Unused by this lock type.
     * @return boolean - true if a lock was obtained.
     */
    public function get_lock($resource, $timeout, $maxlifetime = 86400) {
        $giveuptime = time() + $timeout;

        $token = $this->get_index_from_key($this->type . '_' . $resource);

        $params = array('locktype' => $this->dblockid,
                        'token' => $token);

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
            $this->openlocks[$token] = 1;
            return new lock($token, $this);
        }
        return false;
    }

    /**
     * Release a lock that was previously obtained with @lock.
     * @param lock $lock - a lock obtained from this factory.
     * @return boolean - true if the lock is no longer held (including if it was never held).
     */
    public function release_lock(lock $lock) {
        $params = array('locktype' => $this->dblockid,
                        'token' => $lock->get_key());
        $result = $this->db->get_record_sql('SELECT pg_advisory_unlock(:locktype, :token) AS unlocked', $params);
        $result = $result->unlocked === 't';
        if ($result) {
            unset($this->openlocks[$lock->get_key()]);
        }
        return $result;
    }

    /**
     * Extend a lock that was previously obtained with @lock.
     * @param lock $lock - a lock obtained from this factory.
     * @param int $maxlifetime - the new lifetime for the lock (in seconds).
     * @return boolean - true if the lock was extended.
     */
    public function extend_lock(lock $lock, $maxlifetime = 86400) {
        // Not supported by this factory.
        return false;
    }

    /**
     * Auto release any open locks on shutdown.
     * This is required, because we may be using persistent DB connections.
     */
    public function auto_release() {
        // Called from the shutdown handler. Must release all open locks.
        foreach ($this->openlocks as $key => $unused) {
            $lock = new lock($key, $this);
            $lock->release();
        }
    }

}
