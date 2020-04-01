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
 * Defines abstract factory class for generating locks.
 *
 * @package    core
 * @copyright  Damyon Wiese 2013
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\lock;

defined('MOODLE_INTERNAL') || die();

/**
 * Defines abstract factory class for generating locks.
 *
 * @package   core
 * @category  lock
 * @copyright Damyon Wiese 2013
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface lock_factory {

    /**
     * Define the constructor signature required by the lock_config class.
     *
     * @param string $type - The type this lock is used for (e.g. cron, cache)
     */
    public function __construct($type);

    /**
     * Return information about the blocking behaviour of the locks on this platform.
     *
     * @return boolean - False if attempting to get a lock will block indefinitely.
     */
    public function supports_timeout();

    /**
     * Will this lock be automatically released when the process ends.
     * This should never be relied upon in code - but is useful in the case of
     * fatal errors. If a lock type does not support this auto release,
     * the max lock time parameter must be obeyed to eventually clean up a lock.
     *
     * @return boolean - True if this lock type will be automatically released when the current process ends.
     */
    public function supports_auto_release();

    /**
     * Supports recursion.
     *
     * @deprecated since Moodle 4.0.
     * @return boolean - True if attempting to get 2 locks on the same resource will "stack"
     */
    public function supports_recursion();

    /**
     * Is available.
     *
     * @return boolean - True if this lock type is available in this environment.
     */
    public function is_available();

    /**
     * Get a lock within the specified timeout or return false.
     *
     * @param string $resource - The identifier for the lock. Should use frankenstyle prefix.
     * @param int $timeout - The number of seconds to wait for a lock before giving up.
     *                       Not all lock types will support this.
     * @param int $maxlifetime - The number of seconds to wait before reclaiming a stale lock.
     *                       Not all lock types will use this - e.g. if they support auto releasing
     *                       a lock when a process ends.
     * @return \core\lock\lock|boolean - An instance of \core\lock\lock if the lock was obtained, or false.
     */
    public function get_lock($resource, $timeout, $maxlifetime = 86400);

    /**
     * Release a lock that was previously obtained with @lock.
     *
     * @param lock $lock - The lock to release.
     * @return boolean - True if the lock is no longer held (including if it was never held).
     */
    public function release_lock(lock $lock);

    /**
     * Extend the timeout on a held lock.
     *
     * @deprecated since Moodle 4.0.
     * @param lock $lock - lock obtained from this factory
     * @param int $maxlifetime - new max time to hold the lock
     * @return boolean - True if the lock was extended.
     */
    public function extend_lock(lock $lock, $maxlifetime = 86400);
}
