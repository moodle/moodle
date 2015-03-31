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
 * This is a db record locking factory.
 *
 * @package    core
 * @category   lock
 * @copyright  Damyon Wiese 2013
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\lock;

defined('MOODLE_INTERNAL') || die();

/**
 * This is a db record locking factory.
 *
 * This lock factory uses record locks relying on sql of the form "SET XXX where YYY" and checking if the
 * value was set. It supports timeouts, autorelease and can work on any DB. The downside - is this
 * will always be slower than some shared memory type locking function.
 *
 * @package   core
 * @category  lock
 * @copyright Damyon Wiese 2013
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class db_record_lock_factory implements lock_factory {

    /** @var \moodle_database $db Hold a reference to the global $DB */
    protected $db;

    /** @var string $type Used to prefix lock keys */
    protected $type;

    /** @var array $openlocks - List of held locks - used by auto-release */
    protected $openlocks = array();

    /**
     * Is available.
     * @return boolean - True if this lock type is available in this environment.
     */
    public function is_available() {
        return true;
    }

    /**
     * Almighty constructor.
     * @param string $type - Used to prefix lock keys.
     */
    public function __construct($type) {
        global $DB;

        $this->type = $type;
        // Save a reference to the global $DB so it will not be released while we still have open locks.
        $this->db = $DB;

        \core_shutdown_manager::register_function(array($this, 'auto_release'));
    }

    /**
     * Return information about the blocking behaviour of the lock type on this platform.
     * @return boolean - True
     */
    public function supports_timeout() {
        return true;
    }

    /**
     * Will this lock type will be automatically released when a process ends.
     *
     * @return boolean - True (shutdown handler)
     */
    public function supports_auto_release() {
        return true;
    }

    /**
     * Multiple locks for the same resource can be held by a single process.
     * @return boolean - False - not process specific.
     */
    public function supports_recursion() {
        return false;
    }

    /**
     * This function generates a unique token for the lock to use.
     * It is important that this token is not solely based on time as this could lead
     * to duplicates in a clustered environment (especially on VMs due to poor time precision).
     */
    protected function generate_unique_token() {
        return generate_uuid();
    }

    /**
     * Create and get a lock
     * @param string $resource - The identifier for the lock. Should use frankenstyle prefix.
     * @param int $timeout - The number of seconds to wait for a lock before giving up.
     * @param int $maxlifetime - Unused by this lock type.
     * @return boolean - true if a lock was obtained.
     */
    public function get_lock($resource, $timeout, $maxlifetime = 86400) {

        $token = $this->generate_unique_token();
        $now = time();
        $giveuptime = $now + $timeout;
        $expires = $now + $maxlifetime;

        if (!$this->db->record_exists('lock_db', array('resourcekey' => $resource))) {
            $record = new \stdClass();
            $record->resourcekey = $resource;
            $result = $this->db->insert_record('lock_db', $record);
        }

        $params = array('expires' => $expires,
                        'token' => $token,
                        'resourcekey' => $resource,
                        'now' => $now);
        $sql = 'UPDATE {lock_db}
                   SET
                       expires = :expires,
                       owner = :token
                 WHERE
                       resourcekey = :resourcekey AND
                       (owner IS NULL OR expires < :now)';

        do {
            $now = time();
            $params['now'] = $now;
            $this->db->execute($sql, $params);

            $countparams = array('owner' => $token, 'resourcekey' => $resource);
            $result = $this->db->count_records('lock_db', $countparams);
            $locked = $result === 1;
            if (!$locked) {
                usleep(rand(10000, 250000)); // Sleep between 10 and 250 milliseconds.
            }
            // Try until the giveup time.
        } while (!$locked && $now < $giveuptime);

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
        $params = array('noexpires' => null,
                        'token' => $lock->get_key(),
                        'noowner' => null);

        $sql = 'UPDATE {lock_db}
                    SET
                        expires = :noexpires,
                        owner = :noowner
                    WHERE
                        owner = :token';
        $result = $this->db->execute($sql, $params);
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
        $now = time();
        $expires = $now + $maxlifetime;
        $params = array('expires' => $expires,
                        'token' => $lock->get_key());

        $sql = 'UPDATE {lock_db}
                    SET
                        expires = :expires,
                    WHERE
                        owner = :token';

        $this->db->execute($sql, $params);
        $countparams = array('owner' => $lock->get_key());
        $result = $this->count_records('lock_db', $countparams);

        return $result === 0;
    }

    /**
     * Auto release any open locks on shutdown.
     * This is required, because we may be using persistent DB connections.
     */
    public function auto_release() {
        // Called from the shutdown handler. Must release all open locks.
        foreach ($this->openlocks as $key => $unused) {
            $lock = new lock($key, $this);
            $this->release_lock($lock);
        }
    }
}
