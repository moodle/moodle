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
 * Session handler base.
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\session;

defined('MOODLE_INTERNAL') || die();

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
    public abstract function init();

    /**
     * Check the backend contains data for this session id.
     *
     * Note: this is intended to be called from manager::session_exists() only.
     *
     * @param string $sid
     * @return bool true if session found.
     */
    public abstract function session_exists($sid);

    /**
     * Kill all active sessions, the core sessions table is
     * purged afterwards.
     */
    public abstract function kill_all_sessions();

    /**
     * Kill one session, the session record is removed afterwards.
     * @param string $sid
     */
    public abstract function kill_session($sid);
}
