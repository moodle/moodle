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
 * File based session handler.
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\session;

defined('MOODLE_INTERNAL') || die();

/**
 * File based session handler.
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file extends handler {
    /** @var string session dir */
    protected $sessiondir;

    /**
     * Create new instance of handler.
     */
    public function __construct() {
        global $CFG;

        if (!empty($CFG->session_file_save_path)) {
            $this->sessiondir = $CFG->session_file_save_path;
        } else {
            $this->sessiondir = "$CFG->dataroot/sessions";
        }
    }

    /**
     * Init session handler.
     */
    public function init() {
        if (preg_match('/^[0-9]+;/', $this->sessiondir)) {
            throw new exception('sessionhandlerproblem', 'error', '', null, 'Multilevel session directories are not supported');
        }
        // Make sure session directory exists and is writable.
        make_writable_directory($this->sessiondir, false);
        if (!is_writable($this->sessiondir)) {
            throw new exception('sessionhandlerproblem', 'error', '', null, 'Session directory is not writable');
        }
        // Need to disable debugging since disk_free_space()
        // will fail on very large partitions (see MDL-19222).
        $freespace = @disk_free_space($this->sessiondir);
        // MDL-43039: disk_free_space() returns null if disabled.
        if (!($freespace > 2048) and ($freespace !== false) and ($freespace !== null)) {
            throw new exception('sessiondiskfull', 'error');
        }

        // NOTE: we cannot set any lock acquiring timeout here - bad luck.
        ini_set('session.save_handler', 'files');
        ini_set('session.save_path', $this->sessiondir);
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
        $sid = clean_param($sid, PARAM_FILE);
        if (!$sid) {
            return false;
        }
        $sessionfile = "$this->sessiondir/sess_$sid";
        return file_exists($sessionfile);
    }

    /**
     * Kill all active sessions, the core sessions table is
     * purged afterwards.
     */
    public function kill_all_sessions() {
        if (is_dir($this->sessiondir)) {
            foreach (glob("$this->sessiondir/sess_*") as $filename) {
                @unlink($filename);
            }
        }
    }

    /**
     * Kill one session, the session record is removed afterwards.
     * @param string $sid
     */
    public function kill_session($sid) {
        $sid = clean_param($sid, PARAM_FILE);
        if (!$sid) {
            return;
        }
        $sessionfile = "$this->sessiondir/sess_$sid";
        if (file_exists($sessionfile)) {
            @unlink($sessionfile);
        }
    }
}
