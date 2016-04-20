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
 * Redis based session handler.
 *
 * @package    core
 * @copyright  2016 Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\session;

defined('MOODLE_INTERNAL') || die();

/**
 * Redis based session handler.
 *
 * @package    core
 * @copyright  2016 Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class redis extends handler {
    /** @var string $savepath save_path string */
    protected $savepath;

    /** @var array $servers list of servers parsed from save_path */
    protected $servers;

    /** @var int $acquiretimeout how long to wait for session lock */
    protected $acquiretimeout = 120;

    /**
     * Create new instance of handler.
     */
    public function __construct() {
        global $CFG;

        if (!empty($CFG->session_redis_acquire_lock_timeout)) {
            $this->acquiretimeout = $CFG->session_redis_acquire_lock_timeout;
        }

        if (empty($CFG->session_redis_save_path)) {
            $this->savepath = '';
        } else {
            $this->savepath = $CFG->session_redis_save_path;
        }

        if (empty($this->savepath)) {
            $this->servers = array();
        } else {
            $this->servers = $this->connection_string_to_redis_servers($this->savepath);
        }

    }

    /**
     * Start the session.
     * @return bool success
     */
    public function start() {
        $default = ini_get('max_execution_time');
        set_time_limit($this->acquiretimeout);

        $result = parent::start();

        set_time_limit($default);

        return $result;
    }

    /**
     * Init session handler.
     */
    public function init() {
        if (!extension_loaded('Redis')) {
            throw new exception('sessionhandlerproblem', 'error', '', null, 'redis extension is not loaded');
        }

        // The session handler requires a version of Redis with the SETEX command (at least 2.0).
        $version = phpversion('Redis');
        if (!$version or version_compare($version, '2.0') <= 0) {
            throw new exception('sessionhandlerproblem', 'error', '', null, 'redis extension version must be at least 2.0');
        }

        if (empty($this->savepath)) {
            throw new exception('sessionhandlerproblem', 'error', '', null,
                '$CFG->session_redis_save_path must be specified in config.php');
        }

        ini_set('session.save_handler', 'redis');
        ini_set('session.save_path', $this->savepath);
    }

    /**
     * Check the backend contains data for this session id.
     *
     * Note: this is intended to be called from manager::session_exists() only.
     *
     * @param string $sid
     * @return bool true if session found.
     */
    public function session_exists($sid) {
        if (!$this->servers) {
            return false;
        }

        foreach ($this->servers as $server) {
            if ($redis = $this->redis_connect($server)) {
                $value = $redis->get($server['prefix'] . $sid);
                $redis->close();
            }

            if ($value !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Kill all active sessions, the core sessions table is
     * purged afterwards.
     */
    public function kill_all_sessions() {
        global $DB;
        if (!$this->servers) {
            return false;
        }

        $serverlist = array();
        foreach ($this->servers as $server) {
            if ($redis = $this->redis_connect($server)) {
                $serverlist[] = array($redis, $server['prefix']);
            }
        }

        $rs = $DB->get_recordset('sessions', array(), 'id DESC', 'id, sid');
        foreach ($rs as $record) {
            foreach ($serverlist as $arr) {
                list($server, $prefix) = $arr;
                $server->delete($prefix . $sid);
            }
        }

        foreach ($serverlist as $arr) {
            list($server, $prefix) = $arr;
            $server->close();
        }
    }

    /**
     * Kill one session, the session record is removed afterwards.
     * @param string $sid
     */
    public function kill_session($sid) {
        if (!$this->servers) {
            return false;
        }

        // Go through the list of all servers because
        // we do not know where the session handler put the
        // data.

        foreach ($this->servers as $server) {
            if ($redis = $this->redis_connect($server)) {
                $redis->delete($server['prefix'] . $sid);
                $redis->close();
            }
        }
    }

    /**
     * Convert a connection string to an array of servers
     *
     * Example conversion,
     * "tcp://host1:123?database=0, unix:///var/run/redis/redis.sock?database=0" to
     *
     *  array(
     *      (
     *          [scheme]   => 'tcp',
     *          [host]     => 'host1',
     *          [port]     => 123,
     *          [database] => 0,
     *          [prefix]   => 'PHPREDIS_SESSION:'
     *      ),
     *      (
     *          [scheme]   => 'unix',
     *          [path]     => '/var/run/redis/redis.sock',
     *          [database] => 0,
     *          [prefix]   => 'PHPREDIS_SESSION:'
     *      )
     *  )
     *
     * @copyright  2016 Nicholas Hoobin <nicholashoobin@catalyst-au.net>
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     * @author     Nicholas Hoobin
     *
     * @param string $str save_path value containing redis connection string
     * @return array
     */
    public function connection_string_to_redis_servers($str) {
        $servers     = array();
        $connections = array_map('trim', explode(',', $str));

        foreach ($connections as $con) {
            if (strpos($con, "unix:///") !== false) {
                $fields = $this->parse_unix_sock($con);

            } else if (strpos($con, "tcp://") !== false) {
                $fields = $this->parse_url_tcp($con);

            } else {
                $fields = false;
                debugging("Invalid Redis schema in connection savepath");

            }

            // Parsing failed.
            if ($fields === false) {
                continue;
            }

            // Setting the default prefix.
            if (!isset($fields['prefix'])) {
                $fields['prefix'] = 'PHPREDIS_SESSION:';
            }

            // Setting the default database.
            if (!isset($fields['database'])) {
                $fields['database'] = 0;
            }

            // Setting the default timeout.
            if (!isset($fields['timeout'])) {
                $fields['timeout'] = 86400;
            }

            $servers[] = $fields;
        }

        return $servers;
    }

    /**
     * Parses the tcp connection string and returns an object.
     * @param string $con connection string
     * @return object $con connection data object
     */
    private function parse_url_tcp($con) {
        $con = parse_url($con);

        // Seriously wrong url, parsing failed.
        if ($con === false) {
            return false;
        }

        // Parsing the query string.
        if (isset($con['query'])) {
            $query = $con['query'];
            $parts = explode('&', $query);

            foreach ($parts as $part) {
                list($key, $value) = explode('=', $part);
                $con[$key] = $value;
            }
        }

        // Setting the default port.
        if (!isset($con['port'])) {
            $con['port'] = 6379;
        }

        return $con;
    }

    /**
     * Parses the unix domain socket connection string and returns an object.
     * @param string $con connection string
     * @return object $con connection data object
     */
    private function parse_unix_sock($con) {
        // Lets use parse_url to get the bits we need.
        // To use this, replace the three slashes with two slashes.
        $con = str_replace(":///", "://", $con);
        $con = parse_url($con);

        // Seriously wrong url, parsing failed.
        if ($con === false) {
            return false;
        }

        /* Eg. host = var
               path = run/redis/redis.sock
               new path = /var/run/redis/redis.sock
        */
        $con['path'] = '/' . $con['host'] . $con['path'];
        unset($con['host']);

        // Parsing the query string.
        if (isset($con['query'])) {
            $query = $con['query'];
            $parts = explode('&', $query);

            foreach ($parts as $part) {
                list($key, $value) = explode('=', $part);
                $con[$key] = $value;
            }
        }

        return $con;
    }

    /**
     * Connects to the Redis server with the details from the connection object.
     * @param object $con connection details object
     * @return redis $redis redis connection
     */
    private function redis_connect($con) {
        $redis = new \Redis();

        $func = isset($con['persistent']) ? 'pconnect' : 'connect';

        if ($con['scheme'] === 'tcp') {
            // Only TCP connections will have a port, default 6379.
            $result = $redis->$func($con['host'], $con['port'], $con['timeout']);
        } else if ($con['scheme'] === 'unix') {
            // Unix domain socket.
            $result = $redis->$func($con['path']);
        }

        $result = true ? $redis->select($con['database']) : false;

        return $redis;
    }
}

