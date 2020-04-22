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
     * may continue execution, ignored if PECL memcached is below version 2.2.0.
     */
    protected $lockexpire = 7200;

    /**
     * @var integer $lockretrysleep Used for memcached 3.x (PHP7), the amount of time to
     * sleep between attempts to acquire the session lock. Mimics the deprecated config
     * memcached.sess_lock_wait.
     */
    protected $lockretrysleep = 150;

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
            $this->servers = self::connection_string_to_memcache_servers($this->savepath);
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

        if (!empty($CFG->session_memcached_lock_retry_sleep)) {
            $this->lockretrysleep = (int)$CFG->session_memcached_lock_retry_sleep;
        }
    }

    /**
     * Start the session.
     * @return bool success
     */
    public function start() {
        ini_set('memcached.sess_locking', $this->requires_write_lock() ? '1' : '0');

        // NOTE: memcached before 2.2.0 expires session locks automatically after max_execution_time,
        //       this leads to major difference compared to other session drivers that timeout
        //       and stop the second request execution instead.

        $default = ini_get('max_execution_time');
        set_time_limit($this->acquiretimeout);

        $isnewsession = empty($_COOKIE[session_name()]);
        $starttimer = microtime(true);

        $result = parent::start();

        // If session_start returned TRUE, but it took as long
        // as the timeout value, and the $_SESSION returned is
        // empty when should not have been (isnewsession false)
        // then assume it did timeout and is invalid.
        // Add 1 second to elapsed time to account for inexact
        // timings in php_memcached_session.c.
        // @TODO Remove this check when php-memcached is fixed
        // to return false after key lock acquisition timeout.
        if (!$isnewsession && $result && count($_SESSION) == 0
            && (microtime(true) - $starttimer + 1) >= floatval($this->acquiretimeout)) {
            $result = false;
        }

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
        ini_set('memcached.sess_lock_expire', $this->lockexpire);

        if (version_compare($version, '3.0.0-dev') >= 0) {
            // With memcached 3.x (PHP 7) we configure the max retries to make and the time to sleep between each retry.
            // There are two sleep config values, an initial and a max value.
            // After each attempt the memcached module adjusts the sleep value to be the lesser of the configured max
            // value, or 2X the previous value.
            // With default memcached.ini configs (5, 1s, 2s) the result is only 5 attempts to lock over 9 sec.
            // To mimic the behavior of the 2.2.x module so we get more attempts and much more frequently, config both
            // sleep values to the old default value of 150 msec (making it constant) and calculate number of retries
            // using the existing Moodle config $CFG->session_memcached_acquire_lock_timeout.
            // Doing this so admins configure session lock attempt timeout in familiar terms, and more straight-forward
            // to detect if lock attempt timeout has occurred in start().
            // If _min and _max values are not equal, the actual lock acquire timeout will not be the expected
            // configured value in $CFG->session_memcached_acquire_lock_timeout; this will cause session data loss when
            // failure to acquire the lock is not detected.
            ini_set('memcached.sess_lock_wait_min', $this->lockretrysleep);
            ini_set('memcached.sess_lock_wait_max', $this->lockretrysleep);
            ini_set('memcached.sess_lock_retries', (int)(($this->acquiretimeout * 1000) / $this->lockretrysleep) + 1);
        } else {
            // With memcached 2.2.x we configure max time to attempt lock, and accept default value (in memcached.ini)
            // for sleep time between each attempt (usually 150 msec), then memcached calculates the max number of
            // retries to make.
            ini_set('memcached.sess_lock_max_wait', $this->acquiretimeout);
        }

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

    /**
     * Convert a connection string to an array of servers.
     *
     * "abc:123, xyz:789" to
     *  [
     *      ['abc', '123'],
     *      ['xyz', '789'],
     *  ]
     *
     * @param   string  $str save_path value containing memcached connection string
     * @return  array[]
     */
    protected static function connection_string_to_memcache_servers(string $str) : array {
        $servers = [];
        $parts   = explode(',', $str);
        foreach ($parts as $part) {
            $part = trim($part);
            $pos  = strrpos($part, ':');
            if ($pos !== false) {
                $host = substr($part, 0, $pos);
                $port = substr($part, ($pos + 1));
            } else {
                $host = $part;
                $port = 11211;
            }
            $servers[] = [$host, $port];
        }
        return $servers;
    }
}
