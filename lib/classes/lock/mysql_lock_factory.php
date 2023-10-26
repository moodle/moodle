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
 * MySQL / MariaDB locking factory.
 *
 * @package    core
 * @category   lock
 * @copyright  Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\lock;

defined('MOODLE_INTERNAL') || die();

/**
 * MySQL / MariaDB locking factory.
 *
 * @package   core
 * @category  lock
 * @copyright Brendan Heywood <brendan@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mysql_lock_factory implements lock_factory {

    /** @var string $dbprefix - used as a namespace for these types of locks */
    protected $dbprefix = '';

    /** @var \moodle_database $db Hold a reference to the global $DB */
    protected $db;

    /** @var array $openlocks - List of held locks - used by auto-release */
    protected $openlocks = [];

    /**
     * Return a unique prefix based on the database name and prefix.
     * @param string $type - Used to prefix lock keys.
     * @return string.
     */
    protected function get_unique_db_prefix($type) {
        global $CFG;
        $prefix = $CFG->dbname . ':';
        if (isset($CFG->prefix)) {
            $prefix .= $CFG->prefix;
        }
        $prefix .= '_' . $type . '_';
        return $prefix;
    }

    /**
     * Lock constructor.
     * @param string $type - Used to prefix lock keys.
     */
    public function __construct($type) {
        global $DB;

        $this->dbprefix = $this->get_unique_db_prefix($type);
        // Save a reference to the global $DB so it will not be released while we still have open locks.
        $this->db = $DB;

        \core_shutdown_manager::register_function([$this, 'auto_release']);
    }

    /**
     * Is available.
     * @return boolean - True if this lock type is available in this environment.
     */
    public function is_available() {
        return $this->db->get_dbfamily() === 'mysql';
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
     * Multiple locks for the same resource can NOT be held by a single process.
     *
     * Hard coded to false and workaround inconsistent support in different
     * versions of MySQL / MariaDB.
     *
     * @deprecated since Moodle 3.10.
     * @return boolean - false
     */
    public function supports_recursion() {
        debugging('The function supports_recursion() is deprecated, please do not use it anymore.',
            DEBUG_DEVELOPER);
        return false;
    }

    /**
     * Create and get a lock
     * @param string $resource - The identifier for the lock. Should use frankenstyle prefix.
     * @param int $timeout - The number of seconds to wait for a lock before giving up.
     * @param int $maxlifetime - Unused by this lock type.
     * @return boolean - true if a lock was obtained.
     */
    public function get_lock($resource, $timeout, $maxlifetime = 86400) {

        // We sha1 to avoid long key names hitting the mysql str limit.
        $resourcekey = sha1($this->dbprefix . $resource);

        // Even though some versions of MySQL and MariaDB can support stacked locks
        // just never stack them and always fail fast.
        if (isset($this->openlocks[$resourcekey])) {
            return false;
        }

        $params = [
            'resourcekey' => $resourcekey,
            'timeout' => $timeout
        ];

        $result = $this->db->get_record_sql('SELECT GET_LOCK(:resourcekey, :timeout) AS locked', $params);
        $locked = $result->locked == 1;

        if ($locked) {
            $this->openlocks[$resourcekey] = 1;
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

        $params = [
            'resourcekey' => $lock->get_key()
        ];
        $result = $this->db->get_record_sql('SELECT RELEASE_LOCK(:resourcekey) AS unlocked', $params);
        $result = $result->unlocked == 1;
        if ($result) {
            unset($this->openlocks[$lock->get_key()]);
        }
        return $result;
    }

    /**
     * Extend a lock that was previously obtained with @lock.
     *
     * @deprecated since Moodle 3.10.
     * @param lock $lock - a lock obtained from this factory.
     * @param int $maxlifetime - the new lifetime for the lock (in seconds).
     * @return boolean - true if the lock was extended.
     */
    public function extend_lock(lock $lock, $maxlifetime = 86400) {
        debugging('The function extend_lock() is deprecated, please do not use it anymore.',
            DEBUG_DEVELOPER);
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
