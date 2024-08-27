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

namespace core_cache;

use core\exception\moodle_exception;

/**
 * Cache Loader supporting locking.
 *
 * This interface should be given to classes already implementing core_cache\loader_interface that also wish to support locking.
 * It outlines the required structure for utilising locking functionality when using a cache.
 *
 * Can be implemented by any class already implementing the core_cache\loader_interface interface.
 * @package core_cache
 * @copyright Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface loader_with_locking_interface {
    /**
     * Acquires a lock for the given key.
     *
     * Please note that this happens automatically if the cache definition requires locking.
     * it is still made a public method so that adhoc caches can use it if they choose.
     * However this doesn't guarantee consistent access. It will become the responsibility of the calling code to ensure
     * locks are acquired, checked, and released.
     *
     * Prior to Moodle 4,3 this function used to return false if the lock cannot be obtained. It
     * now always returns true, and throws an exception if the lock cannot be obtained.
     *
     * @param string|int $key
     * @return bool Always returns true (for backwards compatibility)
     * @throws moodle_exception If the lock cannot be obtained after a timeout
     */
    public function acquire_lock($key);

    /**
     * Checks if the cache loader owns the lock for the given key.
     *
     * Please note that this happens automatically if the cache definition requires locking.
     * it is still made a public method so that adhoc caches can use it if they choose.
     * However this doesn't guarantee consistent access. It will become the responsibility of the calling code to ensure
     * locks are acquired, checked, and released.
     *
     * @param string|int $key
     * @return bool True if this code has the lock, false if there is a lock but this code doesn't have it,
     *      null if there is no lock.
     */
    public function check_lock_state($key);

    /**
     * Releases the lock for the given key.
     *
     * Please note that this happens automatically if the cache definition requires locking.
     * it is still made a public method so that adhoc caches can use it if they choose.
     * However this doesn't guarantee consistent access. It will become the responsibility of the calling code to ensure
     * locks are acquired, checked, and released.
     *
     * @param string|int $key
     * @return bool True if the lock has been released, false if there was a problem releasing the lock.
     */
    public function release_lock($key);
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(loader_with_locking_interface::class, \cache_loader_with_locking::class);
