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
 * RedisCluster store simpleSAMLphp class for auth/saml2.
 *
 * @package    cachestore_rediscluster
 * @author     Adam Olley
 * @copyright  Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace cachestore_rediscluster;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/auth/saml2/.extlib/simplesamlphp/lib/SimpleSAML/Store.php');

/**
 * RedisCluster store simpleSAMLphp class for auth/saml2.
 *
 * @package    cachestore_rediscluster
 * @copyright  Copyright (c) 2021 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_saml2_store extends \SimpleSAML\Store {

    /**
     * The connection config for RedisCluster.
     *
     * @var string
     */
    protected $config;

    /**
     * The RedisCluster cachestore object.
     *
     * @var cachestore_rediscluster
     */
    protected $connection = null;

    /**
     * Create new instance of handler.
     */
    public function __construct() {
        global $CFG;

        $this->config = [
            'compression' => \Redis::COMPRESSION_NONE,
            'failover' => \RedisCluster::FAILOVER_NONE,
            'persist' => false,
            'preferrednodes' => null,
            'prefix' => 'simpleSAMLphp.' . $CFG->dbname . '.',
            'readtimeout' => 3.0,
            'serializer' => \Redis::SERIALIZER_PHP,
            'server' => null,
            'serversecondary' => null,
            'session' => false,
            'timeout' => 3.0,
        ];

        foreach (array_keys($this->config) as $key) {
            if (!empty($CFG->auth_saml2_rediscluster[$key])) {
                $this->config[$key] = $CFG->auth_saml2_rediscluster[$key];
            }
        }

        if (!$this->init()) {
            throw new \coding_exception("Could not configure rediscluster for auth_saml2");
        }
    }

    /**
     * Init connection.
     */
    protected function init() {
        global $CFG;

        require_once("{$CFG->dirroot}/cache/stores/rediscluster/lib.php");

        if (!extension_loaded('redis') || empty($this->config['server']) || !class_exists('RedisCluster')) {
            return false;
        }

        $this->connection = new \cachestore_rediscluster(null, $this->config);
        return true;
    }

    /**
     * @param string   $type
     * @param string   $key
     * @param mixed    $value
     * @param int|null $expire
     */
    public function set($type, $key, $value, $expire = null) {
        $now = time();
        if ($expire !== null && $expire > $now) {
            $this->connection->command('setex', $this->make_key($type, $key), $expire - $now, $value);
        } else {
            $this->connection->command('set', $this->make_key($type, $key), $value);
        }
    }

    /**
     * @param string $type
     * @param string $key
     * @return mixed|null
     */
    public function get($type, $key) {
        $value = $this->connection->command('get', $this->make_key($type, $key));
        if ($value === false) {
            $value = null;
        }

        return $value;
    }

    /**
     * @param string $type
     * @param string $key
     */
    public function delete($type, $key) {
        $this->connection->command('unlink', $this->make_key($type, $key));
    }

    /**
     * @param string $type
     * @param string $key
     * @return string
     */
    protected function make_key($type, $key) {
        return $type . '.' . $key;
    }
}
