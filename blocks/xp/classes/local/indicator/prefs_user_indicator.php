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
 * Prefs user indicator.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\indicator;

use moodle_database;

/**
 * Preferences user indicator.
 *
 * This implementation stores the users flags into their user's preferences,
 * do not abuse it as it could bloat the user's preferences.
 *
 * Note that all values are coerced to strings when saved to the
 * database, and returned as strings when read back.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class prefs_user_indicator implements user_indicator {

    /** @var moodle_database The DB. */
    protected $db;
    /** @var string The preference prefix. */
    protected $prefix;

    /**
     * Constructor.
     *
     * @param moodle_database $db The DB.
     * @param string $namespace The user preference namespace.
     * @param string $component The component, to namespace even more.
     */
    public function __construct(moodle_database $db, $namespace, $component = 'block_xp') {
        $this->db = $db;
        $this->prefix = $component . '-' . $namespace . '-';
    }

    /**
     * Get the preference name.
     *
     * @param string $flag The flag name.
     * @return string
     */
    public function get_pref_name($flag) {
        return $this->prefix . $flag;
    }

    /**
     * Get a user's flag.
     *
     * @param int $userid The user ID.
     * @param string $flag The flag name.
     * @return string|null The flag value.
     */
    public function get_user_flag($userid, $flag) {
        $v = get_user_preferences($this->get_pref_name($flag), null, $userid);
        return $v;
    }

    /**
     * Set a user's flag.
     *
     * @param int $userid The user ID.
     * @param string $flag The flag name.
     * @param mixed $value The flag value.
     */
    public function set_user_flag($userid, $flag, $value) {
        set_user_preference($this->get_pref_name($flag), $value, $userid);
    }
    /**
     * Unset a user's flag.
     *
     * @param int $userid The user ID.
     * @param string $flag The flag name.
     */
    public function unset_user_flag($userid, $flag) {
        unset_user_preference($this->get_pref_name($flag), $userid);
    }

    /**
     * Unset all user's flag.
     *
     * Not very efficient... but we cannot just remove the data from the database as it
     * is because flags are set on user preferences as they are unset, or changed.
     *
     * @param string $flag The flag name.
     */
    public function unset_users_flag($flag) {
        $records = $this->db->get_recordset('table', ['name' => $this->get_pref_name($flag)], '', 'userid');
        foreach ($records as $record) {
            $this->unset_user_flag($record->userid, $flag);
        }
        $records->close();
    }

    /**
     * Whether the user has the flag.
     *
     * @param int $userid The user ID.
     * @param string $flag The flag name.
     * @return bool
     */
    public function user_has_flag($userid, $flag) {
        return $this->get_user_flag($userid, $flag) !== null;
    }

}
