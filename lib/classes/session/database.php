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
 * Database based session handler.
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\session;

defined('MOODLE_INTERNAL') || die();

/**
 * Database based session handler.
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class database extends handler {
    /** @var \stdClass $record session record */
    protected $recordid = null;

    /** @var \moodle_database $database session database */
    protected $database = null;

    /** @var bool $failed session read/init failed, do not write back to DB */
    protected $failed = false;

    /** @var string $lasthash hash of the session data content */
    protected $lasthash = null;

    /** @var int $acquiretimeout how long to wait for session lock */
    protected $acquiretimeout = 120;

    /**
     * Create new instance of handler.
     */
    public function __construct() {
        global $DB, $CFG;
        // Note: we store the reference here because we need to modify database in shutdown handler.
        $this->database = $DB;

        if (!empty($CFG->session_database_acquire_lock_timeout)) {
            $this->acquiretimeout = (int)$CFG->session_database_acquire_lock_timeout;
        }
    }

    /**
     * Init session handler.
     */
    public function init() {
        if (!$this->database->session_lock_supported()) {
            throw new exception('sessionhandlerproblem', 'error', '', null, 'Database does not support session locking');
        }

        $result = session_set_save_handler(array($this, 'handler_open'),
            array($this, 'handler_close'),
            array($this, 'handler_read'),
            array($this, 'handler_write'),
            array($this, 'handler_destroy'),
            array($this, 'handler_gc'));
        if (!$result) {
            throw new exception('dbsessionhandlerproblem', 'error');
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
        // It was already checked in the calling code that the record in sessions table exists.
        return true;
    }

    /**
     * Kill all active sessions, the core sessions table is
     * purged afterwards.
     */
    public function kill_all_sessions() {
        // Nothing to do, the sessions table is cleared from core.
        return;
    }

    /**
     * Kill one session, the session record is removed afterwards.
     * @param string $sid
     */
    public function kill_session($sid) {
        // Nothing to do, the sessions table is purged afterwards.
        return;
    }

    /**
     * Open session handler.
     *
     * {@see http://php.net/manual/en/function.session-set-save-handler.php}
     *
     * @param string $save_path
     * @param string $session_name
     * @return bool success
     */
    public function handler_open($save_path, $session_name) {
        // Note: we use the already open database.
        return true;
    }

    /**
     * Close session handler.
     *
     * {@see http://php.net/manual/en/function.session-set-save-handler.php}
     *
     * @return bool success
     */
    public function handler_close() {
        if ($this->recordid) {
            try {
                $this->database->release_session_lock($this->recordid);
            } catch (\Exception $ex) {
                // Ignore any problems.
            }
        }
        $this->recordid = null;
        $this->lasthash = null;
        return true;
    }

    /**
     * Read session handler.
     *
     * {@see http://php.net/manual/en/function.session-set-save-handler.php}
     *
     * @param string $sid
     * @return string
     */
    public function handler_read($sid) {
        try {
            if (!$record = $this->database->get_record('sessions', array('sid'=>$sid), 'id')) {
                // Let's cheat and skip locking if this is the first access,
                // do not create the record here, let the manager do it after session init.
                $this->failed = false;
                $this->recordid = null;
                $this->lasthash = sha1('');
                return '';
            }
            if ($this->recordid and $this->recordid != $record->id) {
                error_log('Second session read with different record id detected, cannot read session');
                $this->failed = true;
                $this->recordid = null;
                return '';
            }
            if (!$this->recordid) {
                // Lock session if exists and not already locked.
                if ($this->requires_write_lock()) {
                    $this->database->get_session_lock($record->id, $this->acquiretimeout);
                }
                $this->recordid = $record->id;
            }
        } catch (\dml_sessionwait_exception $ex) {
            // This is a fatal error, better inform users.
            // It should not happen very often - all pages that need long time to execute
            // should close session immediately after access control checks.
            error_log('Cannot obtain session lock for sid: '.$sid);
            $this->failed = true;
            throw $ex;

        } catch (\Exception $ex) {
            // Do not rethrow exceptions here, this should not happen.
            error_log('Unknown exception when starting database session : '.$sid.' - '.$ex->getMessage());
            $this->failed = true;
            $this->recordid = null;
            return '';
        }

        // Finally read the full session data because we know we have the lock now.
        if (!$record = $this->database->get_record('sessions', array('id'=>$record->id), 'id, sessdata')) {
            // Ignore - something else just deleted the session record.
            $this->failed = true;
            $this->recordid = null;
            return '';
        }
        $this->failed = false;

        if (is_null($record->sessdata)) {
            $data = '';
            $this->lasthash = sha1('');
        } else {
            $data = base64_decode($record->sessdata);
            $this->lasthash = sha1($record->sessdata);
        }

        return $data;
    }

    /**
     * Write session handler.
     *
     * {@see http://php.net/manual/en/function.session-set-save-handler.php}
     *
     * NOTE: Do not write to output or throw any exceptions!
     *       Hopefully the next page is going to display nice error or it recovers...
     *
     * @param string $sid
     * @param string $session_data
     * @return bool success
     */
    public function handler_write($sid, $session_data) {
        if ($this->failed) {
            // Do not write anything back - we failed to start the session properly.
            return false;
        }

        $sessdata = base64_encode($session_data); // There might be some binary mess :-(
        $hash = sha1($sessdata);

        if ($hash === $this->lasthash) {
            return true;
        }

        try {
            if ($this->recordid) {
                $this->database->set_field('sessions', 'sessdata', $sessdata, array('id'=>$this->recordid));
            } else {
                // This happens in the first request when session record was just created in manager.
                $this->database->set_field('sessions', 'sessdata', $sessdata, array('sid'=>$sid));
            }
        } catch (\Exception $ex) {
            // Do not rethrow exceptions here, this should not happen.
            error_log('Unknown exception when writing database session data : '.$sid.' - '.$ex->getMessage());
        }

        return true;
    }

    /**
     * Destroy session handler.
     *
     * {@see http://php.net/manual/en/function.session-set-save-handler.php}
     *
     * @param string $sid
     * @return bool success
     */
    public function handler_destroy($sid) {
        if (!$session = $this->database->get_record('sessions', array('sid'=>$sid), 'id, sid')) {
            if ($sid == session_id()) {
                $this->recordid = null;
                $this->lasthash = null;
            }
            return true;
        }

        if ($this->recordid and $session->id == $this->recordid) {
            try {
                $this->database->release_session_lock($this->recordid);
            } catch (\Exception $ex) {
                // Ignore problems.
            }
            $this->recordid = null;
            $this->lasthash = null;
        }

        $this->database->delete_records('sessions', array('id'=>$session->id));

        return true;
    }

    /**
     * GC session handler.
     *
     * {@see http://php.net/manual/en/function.session-set-save-handler.php}
     *
     * @param int $ignored_maxlifetime moodle uses special timeout rules
     * @return bool success
     */
    public function handler_gc($ignored_maxlifetime) {
        // This should do something only if cron is not running properly...
        if (!$stalelifetime = ini_get('session.gc_maxlifetime')) {
            return true;
        }
        $params = array('purgebefore' => (time() - $stalelifetime));
        $this->database->delete_records_select('sessions', 'userid = 0 AND timemodified < :purgebefore', $params);
        return true;
    }
}
