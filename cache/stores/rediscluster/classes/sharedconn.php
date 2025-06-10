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
 * RedisCluster shared connection
 *
 * @package    cachestore_rediscluster
 * @copyright  Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace cachestore_rediscluster;

defined('MOODLE_INTERNAL') || die();

/**
 * RedisCluster shared connection
 *
 * Designed for reusing a single connection to redis between features.
 */
class sharedconn {

    /**
     * The connection config for RedisCluster.
     *
     * @var string
     */
    protected $config;

    /**
     * The RedisCluster connection object.
     *
     * @var \cachestore_rediscluster
     */
    protected static $connection = null;

    protected $isready = false;

    /**
     * Create new instance of sharedconn. Private since this is a singleton.
     */
    protected function __construct() {
        global $CFG;

        $siteident = !empty($CFG->forcedsiteident) ? $CFG->forcedsiteident : "{$CFG->dbname}:{$CFG->prefix}";

        $server = empty($CFG->redis) ? null : $CFG->redis;

        $this->config = [
            'server' => $server,
            'serversecondary' => !empty($CFG->redis_seeds) ? $CFG->redis_seeds : null,
            'prefix' => $siteident,
            'timeout' => !empty($CFG->redis_timeout) ? $CFG->redis_timeout : 1.0,
            'persist' => !empty($CFG->redis_persist) ? $CFG->redis_persist : false,
        ];

        if (PHPUNIT_TEST) {
            $this->config = \cachestore_rediscluster::unit_test_configuration();
        }

        $this->isready = self::$connection !== null;
        if (!$this->isready) {
            $this->isready = $this->init();
        }
    }

    /**
     * Init session handler.
     */
    public function init() {
        global $CFG;

        require_once("{$CFG->dirroot}/cache/stores/rediscluster/lib.php");

        if (!extension_loaded('redis') || empty($this->config['server']) || !class_exists('RedisCluster')) {
            return false;
        }

        self::$connection = new \cachestore_rediscluster('sharedconn', $this->config);
        return true;
    }

    /**
     * Get the singleton for sharedconn.
     *
     * @return static
     */
    public static function get_instance() {
        static $instance = null;
        if ($instance === null) {
            $instance = new static();
        }
        return $instance;
    }

    /**
     * We want to prefix all keys with the type of tool we're using.
     *
     * So a key of "abc" for the ratelimiter becomes "ratelimiter:abc".
     *
     * This is to avoid collisions between tool types.
     *
     * @param string $key
     * @return string
     */
    public function parse_key($key) {
        $class = preg_replace('#^'.__NAMESPACE__.'\\\#', '', static::class);
        return "{$class}:{$key}";
    }

    /**
     * Cleanup redis after unit testing. Its upto the tests to track which keys to clean up at the end.
     *
     * @param array $keys List of keys to delete.
     */
    public function testing_cleanup($keys) {
        if (!PHPUNIT_TEST) {
            debugging('This should only be called during unit tests.');
        }
        foreach ($keys as $key) {
            $key = $this->parse_key($key);
            static::$connection->command('del', $key);
        }
    }
}