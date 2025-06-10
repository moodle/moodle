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
 * RedisCluster lock_maxwaiters
 *
 * @package    cachestore_rediscluster
 * @copyright  Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace cachestore_rediscluster;

use \core\lock\lock as lock;

defined('MOODLE_INTERNAL') || die();

/**
 * This is a rediscluster locking factory.
 * This *will* return a 429 and throw an exception if it fails to get a lock.
 *
 * It supports timeouts and autorelease.
 */
class lock_maxwaiters extends sharedconn implements \core\lock\lock_factory {

    /** @var string $type Used to prefix lock keys */
    protected $type;

    /** @var array $openlocks - List of held locks - used by auto-release */
    protected $openlocks = array();

    /**
     * The maximum amount of threads a given resource can have active.
     *
     * @var int
     */
    protected $maxactive = 2;

    /**
     * The maximum amount of threads a given resource can have queued waiting for
     * the lock.
     *
     * @var int
     */
    protected $maxwaiters = 10;

    /**
     * Flag that indicates if we're currently waiting for a lock or not.
     *
     * @var bool
     */
    protected $waiting = false;

    /**
     * Is available.
     * @return boolean - True if this lock type is available in this environment.
     */
    public function is_available() {
        return $this->isready;
    }

    /**
     * Almighty constructor.
     * @param string $type - Used to prefix lock keys.
     */
    public function __construct($type) {
        $this->type = $type;

        parent::__construct();

        \core_shutdown_manager::register_function(array($this, 'auto_release'));
    }

    /**
     * Set a new number of max active requests.
     * @param int $newmax The new maximum number of active requests. Non-negative.
     * @returns int The new maxwaiters value.
     */
    public function set_maxactive($newmax) {
        if ((int)$newmax >= 0) {
            $this->maxactive = (int)$newmax;
        }
        return $this->maxactive;
    }

    /**
     * Set a new number of max waiters.
     * @param int $newmax The new maximum number of waiters. Non-negative.
     * @returns int The new maxwaiters value.
     */
    public function set_maxwaiters($newmax) {
        if ((int)$newmax >= 0) {
            $this->maxwaiters = (int)$newmax;
        }
        return $this->maxwaiters;
    }

    /**
     * Set config from an array in $CFG.
     */
    public function config_from_cfg($key) {
        global $CFG;

        if (!isset($CFG->$key)) {
            return;
        }

        if (!empty(($CFG->$key)['max_active']) && (int)(($CFG->$key)['max_active']) >= 0) {
            $this->maxactive = (int)(($CFG->$key)['max_active']);
        }
        if (!empty(($CFG->$key)['max_waiters']) && (int)(($CFG->$key)['max_waiters']) >= 0) {
            $this->maxwaiters = (int)(($CFG->$key)['max_waiters']);
        }
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
     * @param string $resource The identifier for the lock. Should use frankenstyle prefix.
     * @param int $timeout The number of seconds to wait for a lock before giving up.
     * @param int $maxlifetime How long before the lock releases itself automatically.
     * @return boolean|lock - true if a lock was obtained.
     */
    public function get_lock($resource, $timeout, $maxlifetime = 300) {
        $key = $this->parse_key($resource);
        $lockkey = "{$key}.lock";
        $waitkey = "{$lockkey}.waiting";

        $haslock = isset($this->openlocks[$key]) && time() < $this->openlocks[$key];
        $startlocktime = time();

        // Create the waiting key, or increment it if we end up queued.
        $waitpos = $this->increment($waitkey, $maxlifetime);
        $this->waiting = true;

        if ($waitpos > $this->maxwaiters) {
            $this->decrement($waitkey);
            $this->waiting = false;
            $this->error();
        }

        // Ensure on timeout or exception that we try to decrement the waiter count.
        \core_shutdown_manager::register_function([$this, 'release_waiter'], [$waitkey]);

        while (!$haslock) {
            $expiry = time() + $maxlifetime;

            // Check we have room for another active request.
            if ($this->get_active($lockkey) < $this->maxactive) {
                $haslock = $this->increment($lockkey, $maxlifetime);
            }
            if ($haslock) {
                $this->openlocks[$key] = $expiry;
                break;
            }

            usleep(rand(100000, 1000000));
            if (time() > $startlocktime + $timeout) {
                // This is a fatal error, better inform users.
                // It should not happen very often - all pages that need long time to execute
                // should close session immediately after access control checks.
                debugging('Cannot obtain maxwaiters lock for key: '.$key.' within '.$timeout, DEBUG_DEVELOPER);
                break;
            }
        }

        $this->decrement($waitkey);
        $this->waiting = false;

        if (!$haslock) {
            $this->error();
        }

        return new lock($key, $this);
    }

    /**
     * Extend a lock that was previously obtained with @lock.
     * @param lock $lock - a lock obtained from this factory.
     * @param int $maxlifetime - the new lifetime for the lock (in seconds).
     * @return boolean - true if the lock was extended.
     */
    public function extend_lock(lock $lock, $maxlifetime = 86400) {
        return false;
    }

    /**
     * Release a lock that was previously obtained with @lock.
     * @param lock $lock - a lock obtained from this factory.
     * @return boolean - true if the lock is no longer held (including if it was never held).
     */
    public function release_lock(lock $lock) {
        $key = $lock->get_key();

        if (isset($this->openlocks[$key])) {
            $lockkey = "{$key}.lock";
            self::$connection->set_retry_limit(1); // Try extra hard to unlock.
            $this->decrement($lockkey);
            unset($this->openlocks[$key]);
        }
        return true;
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

    protected function error($error = 'sessionwaiterr') {
        if (!defined('NO_MOODLE_COOKIES')) {
            define('NO_MOODLE_COOKIES', true);
        }
        header('HTTP/1.1 429 Too Many Requests');
        throw new \Exception($error);
    }

    /**
     * Performs a direct decrement call, we need to do it this way to avoid any serialization getting in the way.
     *
     * @param int $k The key value to decrement in redis.
     * @returns int The new value associated with the key after decrementing.
     */
    protected function decrement($k) {
        $v = self::$connection->command('decr', $k);
        if ($v !== false) {
            return $v;
        }
        return 0;
    }

    /**
     * Performs a direct increment call, we need to do it this way to avoid any serialization getting in the way.
     *
     * @param int $k The key value to increment in redis.
     * @returns int The new value associated with the key after incrementing.
     */
    protected function increment($k, $ttl) {
        // Ensure key is created with ttl before proceeding.
        if (empty(self::$connection->command('exists', $k))) {
            // We don't want to potentially lose the expiry, so do it in a transaction.
            self::$connection->command('multi');
            self::$connection->command('incr', $k);
            self::$connection->command('expire', $k, $ttl);
            self::$connection->command('exec');
            return 1;
        }

        // Use simple form of increment as we cannot use binary protocol.
        $v = self::$connection->command('incr', $k);
        if ($v !== false) {
            return $v;
        }

        throw new \Exception('lockproblem', 'error', '', null,
                    'Unable to get a waiter lock.');
    }

    protected function get_active($lockkey) {
        $active = self::$connection->command('get', $lockkey);
        if (empty($active)) {
            $active = 0;
        }
        return $active;
    }
}
