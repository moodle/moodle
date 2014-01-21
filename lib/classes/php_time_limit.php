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
 * PHP time limit management.
 *
 * @package core
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Utility class to manage PHP time limit.
 */
class core_php_time_limit {
    /**
     * @var int Current end time of time limit (-1 if not set)
     */
    protected static $currentend = -1;

    /**
     * @var array Data for unit testing
     */
    protected static $unittestdata = array();

    /**
     * Sets the PHP time limit to a number of seconds from now.
     *
     * This function will always extend the time limit (in other words, if the time
     * limit has already been set further in the future, it will do nothing).
     *
     * In order to support front-end servers which may time out silently if no
     * output is displayed, you should ideally only call this function if you expect
     * some output to be displayed at the same time. (I.e. if you call this function
     * each time around a loop, also display some output each time around the loop,
     * such as a progress bar update.)
     *
     * @param int $newlimit Limit in seconds from now (0 = infinite)
     */
    public static function raise($newlimit = 0) {
        global $CFG;

        // Special behaviour in unit tests so that we can check the value.
        if (PHPUNIT_TEST) {
            self::$unittestdata[] = $newlimit;
        }

        // If the time limit has already been set to 'infinite', ignore. Also do
        // nothing in CLI scripts (including unit testing) which are set to
        // infinite by default.
        if (self::$currentend === 0 || CLI_SCRIPT) {
            return;
        }

        // Maximum time limit can be set in config. This can be useful for front-end
        // server systems; if the front-end server has a timeout without receiving
        // data, it's helpful to set this timeout lower to ensure that a suitable
        // error gets logged.
        if (!empty($CFG->maxtimelimit)) {
            $realtimeout = max(1, $CFG->maxtimelimit);
            if ($newlimit === 0) {
                $newlimit = $realtimeout;
            } else {
                $newlimit = min($newlimit, $realtimeout);
            }
        }

        // If new time limit is infinite, just set that.
        if ($newlimit === 0) {
            self::$currentend = 0;
            @set_time_limit(0);
            return;
        }

        // Calculate time limits to make sure it's longer than previous.
        $now = time();
        $newend = $now + $newlimit;
        if (self::$currentend !== -1 && self::$currentend > $newend) {
            // Existing time limit is already longer, so do nothing.
            return;
        }

        // Set time limit and update current value.
        @set_time_limit($newlimit);
        self::$currentend = $newend;
    }

    /**
     * For unit testing, returns an array of the values set during test.
     *
     * @return array Array of values set
     */
    public static function get_and_clear_unit_test_data() {
        $data = self::$unittestdata;
        self::$unittestdata = array();
        return $data;
    }
}
