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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\persistents;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\persistents\concerns\enhanced_persistent;
use block_quickmail\persistents\concerns\sanitizes_input;
use block_quickmail\persistents\concerns\can_be_soft_deleted;

class schedule extends \block_quickmail\persistents\persistent {

    use enhanced_persistent,
        sanitizes_input,
        can_be_soft_deleted;

    /** Table name for the persistent. */
    const TABLE = 'block_quickmail_schedules';

    public static $requiredcreationkeys = [
        'unit',
        'amount',
        'begin_at',
    ];

    public static $defaultcreationparams = [
        'end_at' => null,
    ];

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'unit' => [
                'type' => PARAM_TEXT,
            ],
            'amount' => [
                'type' => PARAM_INT,
            ],
            'begin_at' => [
                'type' => PARAM_INT,
            ],
            'end_at' => [
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED,
            ],
            'timedeleted' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
        ];
    }

    /**
     * Returns the begin_at time as an int
     *
     * @return int
     */
    public function get_begin_time() {
        return (int) $this->get('begin_at');
    }

    /**
     * Returns the end_at time as an int
     *
     * @return mixed  (returns int, or null if not set)
     */
    public function get_end_time() {
        return empty($this->get('end_at'))
            ? null
            : (int) $this->get('end_at');
    }

    /**
     * Returns this schedule's "increment" in a datetime-modify-friendly string format
     *
     * @return string
     */
    public function get_increment_string() {
        return '+' . $this->get('amount') . ' ' . $this->get('unit');
    }

    /**
     * Returns a timestamp representing the next time this schedule should run
     *
     * Note: if the calculated time is after this schedule's end time (if any), then null will be returned
     *
     * @param  int  $lastruntimestamp
     * @return mixed  int|null
     */
    public function calculate_next_time_from($lastruntimestamp) {
        // Return next run time according to schedule.
        $date = \DateTime::createFromFormat('U', $lastruntimestamp, \core_date::get_server_timezone_object());
        $date->modify($this->get_increment_string());

        $nextruntime = $date->getTimestamp();

        // If this schedule has no end time.
        if (empty($this->get_end_time())) {
            return $nextruntime;

            // Otherwise, calculate the next run time according to schedule.
        } else {
            if ($nextruntime > $this->get_end_time()) {
                // Schedule has expired, set to null.
                return null;
            } else {
                return $nextruntime;
            }
        }
    }

    /**
     * Creates and returns a schedule from the given params
     *
     * @param  array   $params
     * @return schedule
     */
    public static function create_from_params($params) {
        $params = self::sanitize_creation_params($params);

        $schedule = self::create_new([
            'unit' => $params['unit'],
            'amount' => (int) $params['amount'],
            'begin_at' => (int) $params['begin_at'],
            'end_at' => (int) $params['end_at'],
        ]);

        return $schedule;
    }

    /**
     * Returns a timestamp from a given moodle date time selector array, defaulting to null
     *
     * @param  array  $input
     * @param  mixed  $default  default value to return, defaults to null
     * @return mixed
     */
    public static function get_sanitized_date_time_selector_value($input, $default = null) {
        if (!is_array($input)) {
            return $default;
        }

        if (array_key_exists('enabled', $input)) {
            if (!$input['enabled']) {
                return $default;
            }
        }

        $day = $input['day'];
        $month = $input['month'];
        $year = $input['year'];
        $hour = $input['hour'];
        $minute = $input['minute'] == '0' ? '00' : $input['minute'];

        $date = \DateTime::createFromFormat('j n Y H i', implode(
                                                             ' ',
                                                             [$day, $month, $year, $hour, $minute]),
                                                             \core_date::get_server_timezone_object());

        return $date->getTimestamp();
    }

}
