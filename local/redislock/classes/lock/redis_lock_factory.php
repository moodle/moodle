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
 * Redis-backed lock factory.
 *
 * @package   local_redislock
 * @author    Sam Chaffee
 * @copyright Copyright (c) 2015 Blackboard Inc. (http://www.blackboard.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_redislock\lock;

defined('MOODLE_INTERNAL') || die();

use core\lock\lock_factory;
use core\lock\lock;
use local_redislock\api\shared_redis_connection;

/**
 * Redis-backed lock factory class.
 *
 * @package   local_redislock
 * @author    Sam Chaffee
 * @copyright Copyright (c) 2015 Blackboard Inc. (http://www.blackboard.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class redis_lock_factory implements lock_factory {
    /**
     * @var \Redis An instance of the PHPRedis extension class.
     */
    protected $redis;

    /**
     * @var string The type this lock is used for (e.g. cron, cache).
     */
    protected $type;

    /**
     * @var lock[] Keeps track of open locks to be closed on shutdown if not properly closed.
     */
    protected $openlocks = [];

    /**
     * @var boolean Should verbose logs be emitted.
     */
    protected $logging;

    /**
     * @var string Redis server connection string.
     */
    private $redisserver;

    /**
     * @var boolean Shared connection enabled.
     */
    private $shareconnection;

    /**
     * @var int Connection count.
     */
    private static $conncount = 0;

    /**
     * @var string|null Redis password.
     */
    private $auth;

    /**
     * @param string $type The type this lock is used for (e.g. cron, cache).
     * @param \Redis|null $redis An instance of the PHPRedis extension class.
     * @param boolean|null $logging Should verbose logs be emitted.
     * @throws \coding_exception
     */
    public function __construct($type, \Redis $redis = null, $logging = null) {
        global $CFG;

        $this->type = $type;
        $this->redisserver = $CFG->local_redislock_redis_server ?? null;
        $this->shareconnection = empty($CFG->local_redislock_disable_shared_connection);
        $this->auth = $CFG->local_redislock_redis_auth ?? null;
        if (is_null($redis)) {
            shared_redis_connection::get_instance()->add_factory();
            $redis = $this->bootstrap_redis();
        } else {
            // If a Redis instance is set, we shouldn't share it as we don't know who else is using it.
            $this->shareconnection = false;
        }
        if (is_null($logging)) {
            if (isset($CFG->local_redislock_logging)) {
                $logging = (bool) $CFG->local_redislock_logging;
            } else {
                $logging = (CLI_SCRIPT && debugging() && !PHPUNIT_TEST);
            }
        }
        $this->redis   = $redis;
        $this->logging = $logging;

        if (!PHPUNIT_TEST) {
            \core_shutdown_manager::register_function(array($this, 'auto_release'));
        }
    }

    /**
     * Is this lock factory available.
     *
     * @return boolean True if this lock type is available in this environment.
     */
    public function is_available() {
        return $this->redis instanceof \Redis;
    }

    /**
     * Return information about the blocking behaviour of the locks on this platform.
     *
     * @return boolean False if attempting to get a lock will block indefinitely.
     */
    public function supports_timeout() {
        return true;
    }

    /**
     * Will this lock be automatically released when the process ends.
     * This should never be relied upon in code - but is useful in the case of
     * fatal errors. If a lock type does not support this auto release,
     * the max lock time parameter must be obeyed to eventually clean up a lock.
     *
     * @return boolean True if this lock type will be automatically released when the current process ends.
     */
    public function supports_auto_release() {
        return true;
    }

    /**
     * Supports recursion.
     *
     * @return boolean True if attempting to get 2 locks on the same resource will "stack"
     */
    public function supports_recursion() {
        return false;
    }

    /**
     * Get a lock within the specified timeout or return false.
     *
     * @param string $resource The identifier for the lock. Should use frankenstyle prefix.
     * @param int    $timeout The number of seconds to wait for a lock before giving up.
     * @param int    $maxlifetime The number of seconds to wait before reclaiming a stale lock.
     * @return lock|boolean An instance of \core\lock\lock if the lock was obtained, or false.
     */
    public function get_lock($resource, $timeout, $maxlifetime = 86400) {
        global $CFG;
        $giveuptime = time() + $timeout;

        $resource = $this->type . '_' . $resource;

        if (!empty($CFG->MR_SHORT_NAME)) {
            $resource = $CFG->MR_SHORT_NAME . '_' . $resource;
        } else {
            $resource = $CFG->dbname . '_' . $resource;
        }

        if ($this->shareconnection) {
            // Re-get the Redis shared connection in case it's be cleared or recreated elsewhere.
            $this->redis = $this->bootstrap_redis();
        }

        $this->log('Waiting to get '.$resource.' lock');

        $exception = false;
        $locked = false;
        do {
            $now = time();
            try {
                $locked = $this->redis->setnx($resource, $this->get_lock_value());
                $exception = false;
            } catch (\RedisException $e) {
                // If there has been a redis exception, we will try to reconnect.
                $exception = $e;
                if (!$this->shareconnection) {
                    self::$conncount--;
                }
                shared_redis_connection::get_instance()->clear(); // Delete shared connection.
                $this->log("Got exception while trying to get lock: {$e->getMessage()}");
                $this->log("Attempting to reconnect to Redis");
                $this->redis = $this->bootstrap_redis();
            }

            if (!$locked && $timeout !== 0) {
                usleep(rand(500000, 1000000)); // Sleep between 0.5 and 1 second.
            }
        } while (!$locked && $now < $giveuptime);

        if (!$locked && $exception) {
            // Error and return.
            $this->log("Could not get lock on {$resource}. Got exception while trying: {$e->getMessage()}");
            return false;
        }

        if ($locked) {
            $this->log('Obtained '.$resource.' lock with value '.$this->get_lock_value());

            $lock = new lock($resource, $this);
            $this->openlocks[$resource] = $lock;
            return $lock;
        }
        $this->log('Lock timeout, did not obtain '.$resource.' lock');

        return false;
    }

    /**
     * Release a lock that was previously obtained with @get_lock.
     *
     * @param lock $lock The lock to release.
     * @return boolean True if the lock is no longer held (including if it was never held).
     */
    public function release_lock(lock $lock) {
        $resource = $lock->get_key();

        if ($this->shareconnection) {
            // Re-get the Redis shared connection in case it's be cleared or recreated elsewhere.
            $this->redis = $this->bootstrap_redis();
        }

        // We will retry connecting and releasing up to 5 times.
        $failcount = 0;
        $value = false;
        do {
            $exception = false;
            try {
                $value = $this->redis->get($resource);
            } catch (\RedisException $e) {
                $exception = true;
                $failcount++;
                if ($failcount >= 5) {
                    throw $e;
                }

                // If there has been a redis exception, we will try to reconnect.
                if (!$this->shareconnection) {
                    self::$conncount--;
                }
                shared_redis_connection::get_instance()->clear(); // Delete shared connection.
                $this->log("Got exception while trying to release lock: {$e->getMessage()}");
                $this->log('Attempting to reconnect to Redis');
                $this->redis = $this->bootstrap_redis();

                // Sleep the loop for a bit so we don't spam connections.
                usleep(rand(500000, 1000000));
            }
        } while ($exception);

        if ($value) {
            if ($value == $this->get_lock_value()) {
                // This is the process' lock, release it.
                $unlocked = $this->redis->del($resource);

                if ($unlocked) {
                    $this->log('Released '.$resource.' lock');
                } else {
                    $this->log('Failed to release '.$resource.' lock');
                }
            } else {
                // Don't release another process' lock.
                $this->log('Tried to release '.$resource.' lock, but key value belongs to another process; Expected '.
                    $this->get_lock_value().' but got '.$value);
                $unlocked = false;
            }
        } else {
            // Never held that lock or it's already released.
            $this->log('Tried to release '.$resource.' lock, but key does not exist in Redis anymore');
            $unlocked = true;
        }

        if ($unlocked) {
            unset($this->openlocks[$resource]);
        }

        return (bool) $unlocked;
    }

    /**
     * Extend the timeout on a held lock.
     *
     * @param lock $lock lock obtained from this factory.
     * @param int  $maxlifetime new max time to hold the lock.
     * @return boolean True if the lock was extended.
     */
    public function extend_lock(lock $lock, $maxlifetime = 86400) {
        return false;
    }

    /**
     * Auto release any open locks on shutdown.
     */
    public function auto_release() {
        $this->log('Auto-release called, releasing '.count($this->openlocks).' locks');

        // Called from the shutdown handler. Must release all open locks.
        foreach ($this->openlocks as $lock) {
            $lock->release();
        }

        if (!$this->shareconnection) {
            // Connection is not shared. Closing now!
            $this->redis->close();
            self::$conncount--;
            $conncount = self::$conncount;
            $this->log("Connection to Redis from factory type {$this->type} is closed, {$conncount} remaining.");
        } else {
            shared_redis_connection::get_instance()->remove_factory();
            if (empty(shared_redis_connection::get_instance()->get_factory_count())) {
                shared_redis_connection::get_instance()->close();
            }
        }
    }

    /**
     * Returns the TTL for a lock.
     *
     * @param lock $lock
     * @return int
     */
    public function get_ttl(lock $lock) {
        $resource = $lock->get_key();
        return $this->redis->ttl($resource);
    }

    /**
     * Bootstraps a \Redis instance.
     *
     * @return \Redis
     * @throws \coding_exception
     */
    protected function bootstrap_redis() {
        if (!is_null($redis = shared_redis_connection::get_instance()->get_redis())) {
            // Reuse the connection if available.
            return $redis;
        }

        if (!class_exists('Redis')) {
            throw new \coding_exception('Redis class not found, Redis PHP Extension is probably not installed on host: '
                    . $this->get_hostname());
        }
        if (empty($this->redisserver)) {
            throw new \coding_exception('Redis connection string is not configured in $CFG->local_redislock_redis_server');
        }

        try {
            // Default port.
            $port = 6379;
            $server = $this->redisserver;
            if (strpos($this->redisserver, ':')) {
                $serverconf = explode(':', $this->redisserver);
                $server = $serverconf[0];
                $port = $serverconf[1];
            }
            $redis = new \Redis();
            $redis->connect($server, $port);
            if (!empty($this->auth)) {
                $redis->auth($this->auth);
            }
            if ($this->shareconnection) {
                shared_redis_connection::get_instance()->set_redis($redis); // Reusing the connection.
            } else {
                self::$conncount++;
            }
        } catch (\RedisException $e) {
            throw new \coding_exception("RedisException caught on host {$this->get_hostname()} with message: {$e->getMessage()}");
        }

        return $redis;
    }

    /**
     * Log message
     *
     * @param $message
     */
    protected function log($message) {
        if ($this->logging) {
            mtrace(sprintf('Redis lock; pid=%d; %s', getmypid(), $message));
        }
    }

    /**
     * Returns the hostname or 'UNKNOWN' for use in the lock value.
     *
     * @return string
     */
    protected function get_hostname() {
        if (($hostname = gethostname()) === false) {
            $hostname = 'UNKNOWN';
        }
        return $hostname;
    }

    /**
     * Get the value that should be used for the lock.
     *
     * @return string
     */
    protected function get_lock_value() {
        return http_build_query(array(
            'hostname' => $this->get_hostname(),
            'processid' => getmypid(),
        ), null, '&');
    }
}
