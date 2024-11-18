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
 * Redis store simpleSAMLphp class for auth/iomadsaml2.
 *
 * @package    auth_iomadsaml2
 * @author     Sam Chaffee
 * @copyright  Copyright (c) 2017 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_iomadsaml2;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../.extlib/simplesamlphp/lib/SimpleSAML/Store.php');

/**
 * Redis store simpleSAMLphp class for auth/iomadsaml2.
 *
 * @package    auth_iomadsaml2
 * @copyright  Copyright (c) 2017 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class redis_store extends \SimpleSAML\Store {

    /**
     * @var \Redis
     */
    protected $redis;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * Constructs the redis store
     *
     * @param \Redis $redis
     */
    public function __construct($redis = null) {
        global $CFG;

        $this->prefix = 'simpleSAMLphp.' . $CFG->dbname . '.';

        if (!$redis instanceof \Redis) {
            $redis = $this->bootstrap_redis();
        }
        $this->redis = $redis;
    }

    /**
     * Set the redis key details
     *
     * @param string   $type
     * @param string   $key
     * @param mixed    $value
     * @param int|null $expire
     */
    public function set($type, $key, $value, $expire = null) {
        $this->redis->set($this->make_key($type, $key), $value, $this->get_set_options($expire));
    }

    /**
     * Get the redis key details
     *
     * @param string $type
     * @param string $key
     * @return mixed|null
     */
    public function get($type, $key) {
        $value = $this->redis->get($this->make_key($type, $key));
        if ($value === false) {
            $value = null;
        }

        return $value;
    }

    /**
     * Delete the redis key
     *
     * @param string $type
     * @param string $key
     */
    public function delete($type, $key) {
        $this->redis->del($this->make_key($type, $key));
    }

    /**
     * Bootstraps a \Redis instance.
     *
     * @return \Redis
     * @throws \coding_exception
     */
    protected function bootstrap_redis() {
        global $CFG;

        if (!class_exists('Redis')) {
            throw new \coding_exception('Redis class not found, Redis PHP Extension is probably not installed');
        }
        if (empty($CFG->auth_iomadsaml2_redis_server)) {
            throw new \coding_exception('Redis connection string is not configured in $CFG->auth_iomadsaml2_redis_server');
        }

        try {
            $redis = new \Redis();
            $redis->connect($CFG->auth_iomadsaml2_redis_server);
        } catch (\RedisException $e) {
            throw new \coding_exception("RedisException caught with message: {$e->getMessage()}");
        }

        if (!$redis->setOption(\Redis::OPT_PREFIX, $this->prefix)) {
            throw new \coding_exception('Could not set Redis prefix option: ' . $this->prefix);
        }
        if (!$redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP)) {
            throw new \coding_exception('Could not set Redis serializer option to PHP Serializer');
        }
        return $redis;
    }

    /**
     * Make the redis key
     *
     * @param string $type
     * @param string $key
     * @return string
     */
    protected function make_key($type, $key) {
        return $type . '.' . $key;
    }

    /**
     * Get/set expiry option
     *
     * @param null|int $expire
     * @return array
     */
    protected function get_set_options($expire) {
        $options = [];

        $now = time();
        if ($expire !== null && $expire > $now) {
            $options['ex'] = $expire - $now;
        }

        return $options;
    }
}
