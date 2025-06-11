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
 * RedisCluster lock_factory
 *
 * @package    cachestore_rediscluster
 * @copyright  Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace cachestore_rediscluster;

use core\lock\lock as lock;

defined('MOODLE_INTERNAL') || die();

/**
 * This is a rediscluster locking factory.
 *
 * It supports timeouts and autorelease.
 */
class lock_factory extends sharedconn implements \core\lock\lock_factory {

    /** @var string $type Used to prefix lock keys */
    protected $type;

    /** @var array $openlocks - List of held locks - used by auto-release */
    protected $openlocks = [];

    /**
     * Is available.
     * @return boolean - True if this lock type is available in this environment.
     */
    public function is_available() {
        global $CFG;
        return PHPUNIT_TEST || !empty($CFG->redis);
    }

    /**
     * Almighty constructor.
     * @param string $type - Used to prefix lock keys.
     */
    public function __construct($type) {
        $this->type = $type;
        parent::__construct();

        \core_shutdown_manager::register_function([$this, 'auto_release']);
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
     * Create and get a lock
     * @param string $resource - The identifier for the lock. Should use frankenstyle prefix.
     * @param int $timeout - The number of seconds to wait for a lock before giving up.
     * @param int $maxlifetime - Unused by this lock type.
     * @return boolean|lock - true if a lock was obtained.
     */
    public function get_lock($resource, $timeout, $maxlifetime = 86400) {
        $now = time();
        $giveuptime = $now + $timeout;

        $params = ['nx'];
        if ($maxlifetime > 0) {
            $params['ex'] = $maxlifetime;
        }

        do {
            $key = $this->parse_key($resource);
            $locked = static::$connection->command('set', $key, 1, $params);
            if (!$locked) {
                usleep(rand(10000, 250000)); // Sleep between 10 and 250 milliseconds.
            }
            // Try until the giveup time.
        } while (!$locked && time() < $giveuptime);

        if ($locked) {
            $this->openlocks[$key] = 1;
            return new lock($key, $this);
        }

        return false;
    }

    /**
     * Extend a lock that was previously obtained with @lock.
     * @param lock $lock - a lock obtained from this factory.
     * @param int $maxlifetime - the new lifetime for the lock (in seconds).
     * @return boolean - true if the lock was extended.
     */
    public function extend_lock(lock $lock, $maxlifetime = 86400) {
        $key = $this->parse_key($lock->get_key());
        return static::$connection->command('expire', $key, $maxlifetime);
    }

    /**
     * Release a lock that was previously obtained with @lock.
     * @param lock $lock - a lock obtained from this factory.
     * @return boolean - true if the lock is no longer held (including if it was never held).
     */
    public function release_lock(lock $lock) {
        $result = static::$connection->command('del', $lock->get_key());
        if ($result) {
            unset($this->openlocks[$lock->get_key()]);
        }
        return $result > 0;
    }

    /**
     * Auto release any open locks on shutdown.
     * This is required, because we may be using persistent DB connections.
     */
    public function auto_release() {
        // Called from the shutdown handler. Must release all open locks.
        foreach (array_keys($this->openlocks) as $key) {
            $lock = new lock($key, $this);
            $lock->release();
        }
    }
}
