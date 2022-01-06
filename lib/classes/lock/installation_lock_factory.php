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
 * Lock factory for use during installation.
 *
 * @package    core
 * @category   lock
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\lock;

defined('MOODLE_INTERNAL') || die();

/**
 * Lock factory for use during installation.
 *
 * @package   core
 * @category  lock
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class installation_lock_factory implements lock_factory {

    /**
     * Create this lock factory.
     *
     * @param string $type - The type, e.g. cron, cache, session
     */
    public function __construct($type) {
    }

    /**
     * Return information about the blocking behaviour of the lock type on this platform.
     *
     * @return boolean - False if attempting to get a lock will block indefinitely.
     */
    public function supports_timeout() {
        return true;
    }

    /**
     * This lock type will be automatically released when a process ends.
     *
     * @return boolean - True
     */
    public function supports_auto_release() {
        return true;
    }

    /**
     * This lock factory is only available during the initial installation.
     * To use it at any other time would be potentially dangerous.
     *
     * @return boolean
     */
    public function is_available() {
        return during_initial_install();
    }

    /**
     * Multiple locks for the same resource cannot be held from a single process.
     *
     * @deprecated since Moodle 3.10.
     * @return boolean - False
     */
    public function supports_recursion() {
        debugging('The function supports_recursion() is deprecated, please do not use it anymore.',
            DEBUG_DEVELOPER);
        return false;
    }

    /**
     * Get some info that might be useful for debugging.
     * @return boolean - string
     */
    protected function get_debug_info() {
        return 'host:' . php_uname('n') . ', pid:' . getmypid() . ', time:' . time();
    }

    /**
     * Get a lock within the specified timeout or return false.
     *
     * @param string $resource - The identifier for the lock. Should use frankenstyle prefix.
     * @param int $timeout - The number of seconds to wait for a lock before giving up.
     * @param int $maxlifetime - Unused by this lock type.
     * @return boolean - true if a lock was obtained.
     */
    public function get_lock($resource, $timeout, $maxlifetime = 86400) {
        return new lock($resource, $this);
    }

    /**
     * Release a lock that was previously obtained with @lock.
     *
     * @param lock $lock - A lock obtained from this factory.
     * @return boolean - true if the lock is no longer held (including if it was never held).
     */
    public function release_lock(lock $lock) {
        return true;
    }

    /**
     * Extend a lock that was previously obtained with @lock.
     *
     * @deprecated since Moodle 3.10.
     * @param lock $lock - not used
     * @param int $maxlifetime - not used
     * @return boolean - true if the lock was extended.
     */
    public function extend_lock(lock $lock, $maxlifetime = 86400) {
        debugging('The function extend_lock() is deprecated, please do not use it anymore.',
            DEBUG_DEVELOPER);
        // Not supported by this factory.
        return false;
    }

}
