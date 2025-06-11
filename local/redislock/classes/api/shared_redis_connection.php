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
 * Shared redis connection handling class.
 *
 * @package   local_redislock
 * @author    David Castro
 * @copyright Copyright (c) 2020 Open LMS. (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_redislock\api;

use Redis;

/**
 * Shared redis connection handling class.
 *
 * @package   local_redislock
 * @author    David Castro
 * @copyright Copyright (c) 2020 Open LMS. (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class shared_redis_connection {

    /**
     * @var shared_redis_connection
     */
    private static $instance;

    /**
     * @var Redis
     */
    private $redis;

    /**
     * @var int
     */
    private $factorycount;

    /**
     * @var boolean
     */
    private $logging;

    /**
     * Singleton constructor.
     */
    private function __construct() {
        $this->factorycount = 0;
        // Logging enabled only for CLI, web gets damaged by lock logs.
        $this->logging = (CLI_SCRIPT && debugging() && !PHPUNIT_TEST);
        if (isset($CFG->local_redislock_logging)) {
            $this->logging = $this->logging && ((bool) $CFG->local_redislock_logging);
        }
    }

    /**
     * @return shared_redis_connection
     */
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new shared_redis_connection();
        }
        return self::$instance;
    }

    /**
     * @param Redis $redis
     */
    public function set_redis(Redis $redis) {
        $this->redis = $redis;
    }

    /**
     * @return Redis
     */
    public function get_redis(): ?Redis {
        return $this->redis;
    }

    /**
     * Closes the shared connection.
     */
    public function close() {
        if (!is_null($this->redis) && $this->redis->isConnected()) {
            $this->redis->close();
            $this->redis = null;
            $this->log("Shared Redis connection is closed.");
        }
    }

    /**
     * Clears the redis attribute. Use only when the connection has become unresponsive.
     */
    public function clear() {
        $this->redis = null;
    }

    /**
     * Adds a factory to the count.
     */
    public function add_factory() {
        $this->factorycount++;
    }

    /**
     * @throws \coding_exception
     */
    public function remove_factory() {
        if (empty($this->factorycount)) {
            throw new \coding_exception('Can\'t remove a factory, count is 0.');
        }
        $this->factorycount--;
    }

    /**
     * @return int
     */
    public function get_factory_count() {
        return $this->factorycount;
    }

    /**
     * Log message
     *
     * @param $message
     */
    private function log($message) {
        if ($this->logging) {
            mtrace(sprintf('Redis lock; pid=%d; %s', getmypid(), $message));
        }
    }
}
