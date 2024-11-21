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
 * User notice indicator.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\indicator;

use moodle_database;

/**
 * User notice indicator.
 *
 * Flags whether a user has seen a notice.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_notice_indicator extends proxy_user_indicator implements user_indicator_with_acceptance {

    /** @var bool Whether we require the flag to be accepted. */
    private $requiresflag = false;

    /**
     * Constructor.
     *
     * @param moodle_database $db The DB.
     */
    public function __construct(moodle_database $db) {
        parent::__construct(new prefs_user_indicator($db, 'notice'));
    }

    /**
     * Specifies that we can accept a certain flag.
     *
     * @param string $flag The flag name.
     * @return void
     */
    public function set_acceptable_user_flag($flag) {
        global $SESSION;
        if (!isset($SESSION->block_xp_user_notice_indicator)) {
            $SESSION->block_xp_user_notice_indicator = [];
        }
        $SESSION->block_xp_user_notice_indicator[$flag] = true;
    }

    /**
     * Specifies that we can accept a certain flag.
     *
     * @param bool $value Whether we require the flag or not.
     * @return void
     */
    public function set_requires_acceptable_user_flag($value) {
        $this->requiresflag = (bool) $value;
    }

    /**
     * Set a user's flag.
     *
     * @param int $userid The user ID.
     * @param string $flag The flag name.
     * @param int $value The flag value.
     */
    public function set_user_flag($userid, $flag, $value) {
        global $SESSION;
        if ($this->requiresflag && !isset($SESSION->block_xp_user_notice_indicator[$flag])) {
            // Silent ignore.
            return;
        }
        return parent::set_user_flag($userid, $flag, $value);
    }

}
