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

namespace core\session;

use core\clock;
use core\di;
use stdClass;

/**
 * Session handler base.
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class handler {
    /** @var boolean $requireswritelock does the session need and/or have a lock? */
    protected $requireswritelock = false;

    /**
     * Start the session.
     * @return bool success
     */
    public function start() {
        return session_start();
    }

    /**
     * Write the session and release lock. If the session was not intentionally opened
     * with a write lock, then we will abort the session instead if able.
     */
    public function write_close() {
        if ($this->requires_write_lock()) {
            session_write_close();
            $this->requireswritelock = false;
        } else {
            $this->abort();
        }
    }

    /**
     * Release lock on the session without writing it.
     * May not be possible in older versions of PHP. If so, session may be written anyway
     * so that any locks are released.
     */
    public function abort() {
        session_abort();
        $this->requireswritelock = false;
    }

    /**
     * This is called after init() and before start() to indicate whether the session
     * opened should be writable or not. This is intentionally captured even if your
     * handler doesn't support non-locking sessions, so that behavior (upon session close)
     * matches closely between handlers.
     * @param bool $requireswritelock true if needs to be open for writing
     */
    public function set_requires_write_lock($requireswritelock) {
        $this->requireswritelock = $requireswritelock;
    }

    /**
     * Returns all session records.
     *
     * @return \Iterator
     */
    public function get_all_sessions(): \Iterator {
        global $DB;

        $rs = $DB->get_recordset('sessions');
        foreach ($rs as $row) {
            yield $row;
        }
        $rs->close();
    }

    /**
     * Returns a single session record for this session id.
     *
     * @param string $sid
     * @return stdClass
     */
    public function get_session_by_sid(string $sid): stdClass {
        global $DB;

        return $DB->get_record('sessions', ['sid' => $sid]) ?: new stdClass();
    }

    /**
     * Returns all the session records for this user id.
     *
     * @param int $userid
     * @return array
     */
    public function get_sessions_by_userid(int $userid): array {
        global $DB;

        return $DB->get_records('sessions', ['userid' => $userid]);
    }

    /**
     * Insert new empty session record.
     *
     * @param int $userid
     * @return stdClass the new record
     */
    public function add_session(int $userid): stdClass {
        global $DB;

        $record = new stdClass();
        $record->state       = 0;
        $record->sid         = session_id();
        $record->sessdata    = null;
        $record->userid      = $userid;
        $record->timecreated = $record->timemodified = di::get(clock::class)->time();
        $record->firstip     = $record->lastip = getremoteaddr();

        $record->id = $DB->insert_record('sessions', $record);

        return $record;
    }

    /**
     * Update a session record.
     *
     * @param stdClass $record
     * @return bool
     */
    public function update_session(stdClass $record): bool {
        global $DB;

        if (!isset($record->id) && isset($record->sid)) {
            $record->id = $DB->get_field('sessions', 'id', ['sid' => $record->sid]);
        }

        return $DB->update_record('sessions', $record);
    }

    /**
     * Destroy a specific session and delete this session record for this session id.
     *
     * @param string $id session id
     * @return bool
     */
    public function destroy(string $id): bool {
        global $DB;

        return $DB->delete_records('sessions', ['sid' => $id]);
    }

    /**
     * Destroy all sessions, and delete all the session data.
     *
     * @return bool
     */
    public function destroy_all(): bool {
        global $DB;

        return $DB->delete_records('sessions');
    }

    /**
     * Clean up expired sessions.
     *
     * @param int $purgebefore Sessions that have not updated for the last purgebefore timestamp will be removed.
     * @param int $userid
     */
    protected function destroy_expired_user_sessions(int $purgebefore, int $userid): void {
        $sessions = $this->get_sessions_by_userid($userid);
        foreach ($sessions as $session) {
            if ($session->timemodified < $purgebefore) {
                $this->destroy($session->sid);
            }
        }
    }

    /**
     * Clean up all expired sessions.
     *
     * @param int $purgebefore
     */
    protected function destroy_all_expired_sessions(int $purgebefore): void {
        global $DB, $CFG;

        $authsequence = get_enabled_auth_plugins();
        $authsequence = array_flip($authsequence);
        unset($authsequence['nologin']); // No login means user cannot login.
        $authsequence = array_flip($authsequence);
        $authplugins = [];
        foreach ($authsequence as $authname) {
            $authplugins[$authname] = get_auth_plugin($authname);
        }
        $sql = "SELECT u.*, s.sid, s.timecreated AS s_timecreated, s.timemodified AS s_timemodified
                  FROM {user} u
                  JOIN {sessions} s ON s.userid = u.id
                 WHERE s.timemodified < :purgebefore AND u.id <> :guestid";
        $params = ['purgebefore' => $purgebefore, 'guestid' => $CFG->siteguest];

        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $user) {
            foreach ($authplugins as $authplugin) {
                if ($authplugin->ignore_timeout_hook($user, $user->sid, $user->s_timecreated, $user->s_timemodified)) {
                    continue 2;
                }
            }
            $this->destroy($user->sid);
        }
        $rs->close();
    }

    /**
     * Destroy all sessions for a given plugin.
     * Typically used when a plugin is disabled or uninstalled, so all sessions (users) for that plugin are logged out.
     *
     * @param string $pluginname Auth plugin name.
     */
    public function destroy_by_auth_plugin(string $pluginname): void {
        global $DB;

        $rs = $DB->get_recordset('user', ['auth' => $pluginname], 'id ASC', 'id');
        foreach ($rs as $user) {
            $sessions = $this->get_sessions_by_userid($user->id);
            foreach ($sessions as $session) {
                $this->destroy($session->sid);
            }
        }
        $rs->close();
    }

    // phpcs:disable moodle.NamingConventions.ValidVariableName.VariableNameUnderscore
    /**
     * Periodic timed-out session cleanup.
     *
     * @param int $max_lifetime Sessions that have not updated for the last max_lifetime seconds will be removed.
     * @return int|false Number of deleted sessions or false if an error occurred.
     */
    public function gc(int $max_lifetime = 0): int|false {
        global $CFG;

        // This may take a long time.
        \core_php_time_limit::raise();

        if ($max_lifetime === 0) {
            $max_lifetime = (int) $CFG->sessiontimeout;
        }

        try {
            // Calculate the timestamp before which sessions are considered expired.
            $purgebefore = di::get(clock::class)->time() - $max_lifetime;

            // Delete expired sessions for guest user account.
            $this->destroy_expired_user_sessions($purgebefore, $CFG->siteguest);

            // Delete expired sessions for userid = 0 (not logged in), better kill them asap to release memory.
            $this->destroy_expired_user_sessions($purgebefore, 0);

            // Clean up expired sessions for real users only.
            $this->destroy_all_expired_sessions($purgebefore);

            // Cleanup leftovers from the first browser access because it may set multiple cookies and then use only one.
            $purgebefore = di::get(clock::class)->time() - (60 * 3);
            $sessions = $this->get_sessions_by_userid(0);
            foreach ($sessions as $session) {
                if ($session->timemodified == $session->timecreated && $session->timemodified < $purgebefore) {
                    $this->destroy($session->sid);
                }
            }

        } catch (\Exception $ex) {
            debugging('Error gc-ing sessions: '.$ex->getMessage(), DEBUG_NORMAL, $ex->getTrace());
        }

        return 0;
    }
    // phpcs:enable

    /**
     * Has this session been opened with a writelock? Your handler should call this during
     * start() if you support read-only sessions.
     * @return bool true if session is intended to have a write lock.
     */
    public function requires_write_lock() {
        return $this->requireswritelock;
    }

    /**
     * Init session handler.
     */
    abstract public function init();

    /**
     * Check the backend contains data for this session id.
     *
     * Note: this is intended to be called from manager::session_exists() only.
     *
     * @param string $sid
     * @return bool true if session found.
     */
    abstract public function session_exists($sid);
}
