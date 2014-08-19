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
 * Memcached based session handler.
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\session;

defined('MOODLE_INTERNAL') || die();

/**
 * Memcached based session handler.
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class memcached extends handler {
    /** @var string $savepath save_path string  */
    protected $savepath;
    /** @var array $servers list of servers parsed from save_path */
    protected $servers;
    /** @var string $prefix session key prefix  */
    protected $prefix;
    /** @var int $acquiretimeout how long to wait for session lock */
    protected $acquiretimeout = 120;
    /**
     * @var int $lockexpire how long to wait before expiring the lock so that other requests
     * may continue execution, ignored if memcached <= 2.1.0.
     */
    protected $lockexpire = 7200;

    /**
     * Create new instance of handler.
     */
    public function __construct() {
        global $CFG;

        if (empty($CFG->session_memcached_save_path)) {
            $this->savepath = '';
        } else {
            $this->savepath =  $CFG->session_memcached_save_path;
        }

        if (empty($this->savepath)) {
            $this->servers = array();
        } else {
            $this->servers = util::connection_string_to_memcache_servers($this->savepath);
        }

        if (empty($CFG->session_memcached_prefix)) {
            $this->prefix = ini_get('memcached.sess_prefix');
        } else {
            $this->prefix = $CFG->session_memcached_prefix;
        }

        if (!empty($CFG->session_memcached_acquire_lock_timeout)) {
            $this->acquiretimeout = (int)$CFG->session_memcached_acquire_lock_timeout;
        }

        if (!empty($CFG->session_memcached_lock_expire)) {
            $this->lockexpire = (int)$CFG->session_memcached_lock_expire;
        }
    }

    /**
     * Start the session.
     * @return bool success
     */
    public function start() {
        // NOTE: memcached <= 2.1.0 expires session locks automatically after max_execution_time,
        //       this leads to major difference compared to other session drivers that timeout
        //       and stop the second request execution instead.

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
        if (!extension_loaded('memcached')) {
            throw new exception('sessionhandlerproblem', 'error', '', null, 'memcached extension is not loaded');
        }
        $version = phpversion('memcached');
        if (!$version or version_compare($version, '2.0') < 0) {
            throw new exception('sessionhandlerproblem', 'error', '', null, 'memcached extension version must be at least 2.0');
        }
        if (empty($this->savepath)) {
            throw new exception('sessionhandlerproblem', 'error', '', null, '$CFG->session_memcached_save_path must be specified in config.php');
        }

        ini_set('session.save_handler', 'memcached');
        ini_set('session.save_path', $this->savepath);
        ini_set('memcached.sess_prefix', $this->prefix);
        ini_set('memcached.sess_locking', '1'); // Locking is required!

        // Try to configure lock and expire timeouts - ignored if memcached <=2.1.0.
        ini_set('memcached.sess_lock_max_wait', $this->acquiretimeout);
        ini_set('memcached.sess_lock_expire', $this->lockexpire);
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

        // Go through the list of all servers because
        // we do not know where the session handler put the
        // data.

        foreach ($this->servers as $server) {
            list($host, $port) = $server;
            $memcached = new \Memcached();
            $memcached->addServer($host, $port);
            $value = $memcached->get($this->prefix . $sid);
            $memcached->quit();
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
            return;
        }

        // Go through the list of all servers because
        // we do not know where the session handler put the
        // data.

        $memcacheds = array();
        foreach ($this->servers as $server) {
            list($host, $port) = $server;
            $memcached = new \Memcached();
            $memcached->addServer($host, $port);
            $memcacheds[] = $memcached;
        }

        // Note: this can be significantly improved by fetching keys from memcached,
        //       but we need to make sure we are not deleting somebody else's sessions.

        $rs = $DB->get_recordset('sessions', array(), 'id DESC', 'id, sid');
        foreach ($rs as $record) {
            foreach ($memcacheds as $memcached) {
                $memcached->delete($this->prefix . $record->sid);
            }
        }
        $rs->close();

        foreach ($memcacheds as $memcached) {
            $memcached->quit();
        }
    }

    /**
     * Kill one session, the session record is removed afterwards.
     * @param string $sid
     */
    public function kill_session($sid) {
        if (!$this->servers) {
            return;
        }

        // Go through the list of all servers because
        // we do not know where the session handler put the
        // data.

        foreach ($this->servers as $server) {
            list($host, $port) = $server;
            $memcached = new \Memcached();
            $memcached->addServer($host, $port);
            $memcached->delete($this->prefix . $sid);
            $memcached->quit();
        }
    }

}
