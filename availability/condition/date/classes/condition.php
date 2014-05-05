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
 * Date condition.
 *
 * @package availability_date
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_date;

defined('MOODLE_INTERNAL') || die();

/**
 * Date condition.
 *
 * @package availability_date
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class condition extends \core_availability\condition {
    /** @var string Availabile only from specified date. */
    const DIRECTION_FROM = '>=';

    /** @var string Availabile only until specified date. */
    const DIRECTION_UNTIL = '<';

    /** @var string One of the DIRECTION_xx constants. */
    private $direction;

    /** @var int Time (Unix epoch seconds) for condition. */
    private $time;

    /** @var int Forced current time (for unit tests) or 0 for normal. */
    private static $forcecurrenttime = 0;

    /**
     * Constructor.
     *
     * @param stdClass $structure Data structure from JSON decode
     * @throws coding_exception If invalid data structure.
     */
    public function __construct($structure) {
        // Get direction.
        if (isset($structure->d) && in_array($structure->d,
                array(self::DIRECTION_FROM, self::DIRECTION_UNTIL))) {
            $this->direction = $structure->d;
        } else {
            throw new \coding_exception('Missing or invalid ->d for date condition');
        }

        // Get time.
        if (isset($structure->t) && is_int($structure->t)) {
            $this->time = $structure->t;
        } else {
            throw new \coding_exception('Missing or invalid ->t for date condition');
        }
    }

    public function save() {
        return (object)array('type' => 'date',
                'd' => $this->direction, 't' => $this->time);
    }

    public function is_available($not, \core_availability\info $info, $grabthelot, $userid) {
        return $this->is_available_for_all($not);
    }

    public function is_available_for_all($not = false) {
        // Check condition.
        $now = self::get_time();
        switch ($this->direction) {
            case self::DIRECTION_FROM:
                $allow = $now >= $this->time;
                break;
            case self::DIRECTION_UNTIL:
                $allow = $now < $this->time;
                break;
            default:
                throw new \coding_exception('Unexpected direction');
        }
        if ($not) {
            $allow = !$allow;
        }

        return $allow;
    }

    /**
     * Obtains the actual direction of checking based on the $not value.
     *
     * @param bool $not True if condition is negated
     * @return string Direction constant
     * @throws \coding_exception
     */
    protected function get_logical_direction($not) {
        switch ($this->direction) {
            case self::DIRECTION_FROM:
                return $not ? self::DIRECTION_UNTIL : self::DIRECTION_FROM;
            case self::DIRECTION_UNTIL:
                return $not ? self::DIRECTION_FROM : self::DIRECTION_UNTIL;
            default:
                throw new \coding_exception('Unexpected direction');
        }
    }

    public function get_description($full, $not, \core_availability\info $info) {
        return $this->get_either_description($not, false);
    }

    public function get_standalone_description(
            $full, $not, \core_availability\info $info) {
        return $this->get_either_description($not, true);
    }

    /**
     * Shows the description using the different lang strings for the standalone
     * version or the full one.
     *
     * @param bool $not True if NOT is in force
     * @param bool $standalone True to use standalone lang strings
     */
    protected function get_either_description($not, $standalone) {
        $direction = $this->get_logical_direction($not);
        $midnight = self::is_midnight($this->time);
        $midnighttag = $midnight ? '_date' : '';
        $satag = $standalone ? 'short_' : 'full_';
        switch ($direction) {
            case self::DIRECTION_FROM:
                return get_string($satag . 'from' . $midnighttag, 'availability_date',
                        self::show_time($this->time, $midnight, false));
            case self::DIRECTION_UNTIL:
                return get_string($satag . 'until' . $midnighttag, 'availability_date',
                        self::show_time($this->time, $midnight, true));
        }
    }

    protected function get_debug_string() {
        return $this->direction . ' ' . gmdate('Y-m-d H:i:s', $this->time);
    }

    /**
     * Gets time. This function is implemented here rather than calling time()
     * so that it can be overridden in unit tests. (Would really be nice if
     * Moodle had a generic way of doing that, but it doesn't.)
     *
     * @return int Current time (seconds since epoch)
     */
    protected static function get_time() {
        if (self::$forcecurrenttime) {
            return self::$forcecurrenttime;
        } else {
            return time();
        }
    }

    /**
     * Forces the current time for unit tests.
     *
     * @param int $forcetime Time to return from the get_time function
     */
    public static function set_current_time_for_test($forcetime = 0) {
        self::$forcecurrenttime = $forcetime;
    }

    /**
     * Shows a time either as a date or a full date and time, according to
     * user's timezone.
     *
     * @param int $time Time
     * @param bool $dateonly If true, uses date only
     * @param bool $until If true, and if using date only, shows previous date
     * @return string Date
     */
    protected function show_time($time, $dateonly, $until = false) {
        // For 'until' dates that are at midnight, e.g. midnight 5 March, it
        // is better to word the text as 'until end 4 March'.
        $daybefore = false;
        if ($until && $dateonly) {
            $daybefore = true;
            $time = strtotime('-1 day', $time);
        }
        return userdate($time,
                get_string($dateonly ? 'strftimedate' : 'strftimedatetime', 'langconfig'));
    }

    /**
     * Checks whether a given time refers exactly to midnight (in current user
     * timezone).
     *
     * @param int $time Time
     * @return bool True if time refers to midnight, false otherwise
     */
    protected static function is_midnight($time) {
        return usergetmidnight($time) == $time;
    }
}
