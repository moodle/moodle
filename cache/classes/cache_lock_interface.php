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

/**
 * Cache lock interface.
 *
 * This interface needs to be inherited by all cache lock plugins.
 *
 * @package core_cache
 * @copyright Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface cache_lock_interface {
    /**
     * Constructs an instance of the cache lock given its name and its configuration data
     *
     * @param string $name The unique name of the lock instance
     * @param array $configuration
     */
    public function __construct($name, array $configuration = []);

    /**
     * Acquires a lock on a given key.
     *
     * @param string $key The key to acquire a lock for.
     * @param string $ownerid An unique identifier for the owner of this lock. It is entirely optional for the cache lock plugin
     *      to use this. Each implementation can decide for themselves.
     * @param bool $block If set to true the application will wait until a lock can be acquired
     * @return bool True if the lock can be acquired false otherwise.
     */
    public function lock($key, $ownerid, $block = false);

    /**
     * Releases the lock held on a certain key.
     *
     * @param string $key The key to release the lock for.
     * @param string $ownerid An unique identifier for the owner of this lock. It is entirely optional for the cache lock plugin
     *      to use this. Each implementation can decide for themselves.
     * @param bool $forceunlock If set to true the lock will be removed if it exists regardless of whether or not we own it.
     */
    public function unlock($key, $ownerid, $forceunlock = false);

    /**
     * Checks the state of the given key.
     *
     * Returns true if the key is locked and belongs to the ownerid.
     * Returns false if the key is locked but does not belong to the ownerid.
     * Returns null if there is no lock
     *
     * @param string $key The key we are checking for.
     * @param string $ownerid The identifier so we can check if we have the lock or if it is someone else.
     * @return bool True if this code has the lock, false if there is a lock but this code doesn't have it, null if there
     *      is no lock.
     */
    public function check_state($key, $ownerid);

    /**
     * Cleans up any left over locks.
     *
     * This function MUST clean up any locks that have been acquired and not released during processing.
     * Although the situation of acquiring a lock and not releasing it should be insanely rare we need to deal with it.
     * Things such as unfortunate timeouts etc could cause this situation.
     */
    public function __destruct();
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(cache_lock_interface::class, \cache_lock_interface::class);
