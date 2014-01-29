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
 * MySQL GET_LOCK locking factory.
 *
 * @package    core
 * @category   lock
 * @copyright  Damyon Wiese 2013
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\lock;

defined('MOODLE_INTERNAL') || die();

/**
 * MySQL GET_LOCK locking factory.
 *
 * Use MySQL GET_LOCK functions to support locking. Supports auto-release and timeouts and should be fairly quick.
 *
 * @package   core
 * @category  lock
 * @copyright Damyon Wiese 2013
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mysql_lock_factory implements lock_factory {

    /** @var moodle_database $db Hold a reference to the global $DB */
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
        return $this->db->get_dbfamily() === 'mysql';
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
     * @return boolean - Defer to the DB driver.
     */
    public function supports_timeout() {
        return true;
    }

    /**
     * Will this lock type will be automatically released when a process ends.
     *
     * @return boolean - Defer to the DB driver.
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
     * Create and get a lock
     * @param string $resource - The identifier for the lock. Should use frankenstyle prefix.
     * @param int $timeout - The number of seconds to wait for a lock before giving up.
     * @param int $maxlifetime - Unused by this lock type.
     * @return boolean - true if a lock was obtained.
     */
    public function get_lock($resource, $timeout, $maxlifetime = 86400) {
        $params = array(
            'key' => $resource,
            'timeout' => $timeout
        );
        $result = $this->db->get_record_sql('SELECT GET_LOCK(:key, :timeout) AS locked',
                                            $params);
        $locked = (bool)($result->locked);

        if ($locked) {
            $this->openlocks[$resource] = 1;
            return new lock($resource, $this);
        }
        return false;
    }

    /**
     * Release a lock that was previously obtained with @lock.
     * @param lock $lock - a lock obtained from this factory.
     * @return boolean - true if the lock is no longer held (including if it was never held).
     */
    public function release_lock(lock $lock) {
        $result = $this->db->get_record_sql('SELECT RELEASE_LOCK(:key) AS unlocked', array('key' => $lock->get_key()));
        $result = (bool)$result->unlocked;
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
            $this->release_lock($lock);
        }
    }
}
