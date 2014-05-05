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
 * Memcache based session handler.
 *
 * This is based on the memcached code. It lacks some features, such as
 * locking options, but appears to work in practice.
 *
 * @package core
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\session;

defined('MOODLE_INTERNAL') || die();

/**
 * Memcache based session handler.
 *
 * @package core
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class memcache extends handler {
    /**
     * Creates new instance of handler.
     */
    public function __construct() {
        global $CFG;

        if (empty($CFG->session_memcache_save_path)) {
            $this->savepath = '';
        } else {
            $this->savepath = $CFG->session_memcache_save_path;
        }

        if (empty($this->savepath)) {
            $this->servers = array();
        } else {
            $this->servers = util::connection_string_to_memcache_servers($this->savepath);
        }

        if (!empty($CFG->session_memcache_acquire_lock_timeout)) {
            $this->acquiretimeout = (int)$CFG->session_memcache_acquire_lock_timeout;
        }
    }

    /**
     * Starts the session.
     *
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
     * Inits session handler.
     */
    public function init() {
        if (!extension_loaded('memcache')) {
            throw new exception('sessionhandlerproblem', 'error', '', null,
                    'memcache extension is not loaded');
        }
        $version = phpversion('memcache');
        if (!$version or version_compare($version, '2.2') < 0) {
            throw new exception('sessionhandlerproblem', 'error', '', null,
                    'memcache extension version must be at least 2.2');
        }
        if (empty($this->savepath)) {
            throw new exception('sessionhandlerproblem', 'error', '', null,
                    '$CFG->session_memcache_save_path must be specified in config.php');
        }
        // Check in case anybody mistakenly includes tcp://, which you
        // would do in the raw PHP config. We require the same format as
        // for memcached (without tcp://). Otherwse the code that splits it into
        // individual servers won't have worked properly.
        if (strpos($this->savepath, 'tcp://') !== false) {
            throw new exception('sessionhandlerproblem', 'error', '', null,
                    '$CFG->session_memcache_save_path should not contain tcp://');
        }

        ini_set('session.save_handler', 'memcache');

        // The format of save_path is different for memcache (compared to memcached).
        // We are using the same format in config.php to avoid confusion.
        // It has to have tcp:// at the start of each entry.
        $memcacheformat = preg_replace('~(^|,\s*)~','$1tcp://', $this->savepath);
        ini_set('session.save_path', $memcacheformat);
    }

    /**
     * Checks for existing session with given id.
     *
     * Note: this verifies the storage backend only, not the actual session records.
     *
     * @param string $sid PHP session ID
     * @return bool true if session found.
     */
    public function session_exists($sid) {
        if (!$this->servers) {
            return false;
        }

        $memcache = $this->get_memcache();
        $value = $memcache->get($sid);
        $memcache->close();

        return ($value !== false);
    }

    /**
     * Gets the memcache object with all the servers added to it.
     *
     * @return \Memcache Initialised memcache object
     */
    protected function get_memcache() {
        $memcache = new \Memcache();
        foreach ($this->servers as $server) {
            $memcache->addServer($server[0], $server[1]);
        }
        return $memcache;
    }

    /**
     * Kills all active sessions, the core sessions table is purged afterwards.
     */
    public function kill_all_sessions() {
        global $DB;
        if (!$this->servers) {
            return;
        }

        $memcache = $this->get_memcache();

        // Note: this can be significantly improved by fetching keys from memcache,
        // but we need to make sure we are not deleting somebody else's sessions.

        $rs = $DB->get_recordset('sessions', array(), 'id DESC', 'id, sid');
        foreach ($rs as $record) {
            $memcache->delete($record->sid);
        }
        $rs->close();

        $memcache->close();
    }

    /**
     * Kills one session, the session record is removed afterwards.
     *
     * @param string $sid PHP session ID
     */
    public function kill_session($sid) {
        if (!$this->servers) {
            return;
        }

        $memcache = $this->get_memcache();
        $memcache->delete($sid);
        $memcache->close();
    }
}
