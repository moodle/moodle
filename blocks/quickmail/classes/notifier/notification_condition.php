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

namespace block_quickmail\notifier;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\notifier\models\notification_model_helper;

class notification_condition {

    public $conditions;

    public function __construct($conditions = []) {
        $this->conditions = $conditions;
    }

    /**
     * Returns an instantiated notification_condition from a given condition_string
     *
     * @param  string  $conditionstring
     * @return notification_condition
     */
    public static function from_condition_string($conditionstring) {
        $conditions = self::decode_condition_string($conditionstring);

        return new self($conditions);
    }

    /**
     * Returns a string appropriate for db storage given raw notification condition params
     *
     * @param  array  $params  optional, if none will return empty string
     * @return string
     */
    public static function format_for_storage($params = []) {
        if (!count($params)) {
            return '';
        }

        $value = array_reduce(array_keys($params), function($carry, $key) use ($params) {
            return $carry .= $key . ':' . $params[$key] . ',';
        }, '');

        return rtrim($value, ',');
    }

    /**
     * Returns a key/value array of conditions from a formatted condition string
     *
     * @param  string $conditionstring
     * @return array
     */
    public static function decode_condition_string($conditionstring = '') {
        $conditions = [];

        if (!$conditionstring) {
            return $conditions;
        }

        $exploded = explode(',', $conditionstring);

        foreach ($exploded as $ex) {
            list($key, $value) = explode(':', $ex);

            $conditions[$key] = $value;
        }

        return $conditions;
    }

    /**
     * Returns an array of condition keys for the given notification type and model key
     *
     * @param  string $notificationtype
     * @param  string $modelkey
     * @param  string $prepend   optional, if set will prepend output keys with $prepend followed by underscore
     * @return array
     */
    public static function get_required_condition_keys($notificationtype, $modelkey, $prepend = '') {
        $modelclass = notification_model_helper::get_full_model_class_name($notificationtype, $modelkey);

        $keys = $modelclass::$conditionkeys;

        return ! $prepend
            ? $keys
            : array_map(function($key) use ($prepend) {
                return $prepend . '_' . $key;
            }, $keys);
    }

    /**
     * Returns a condition value from a set condition key
     *
     * @param  string  $key
     * @return mixed  string, or null if no set condition value
     */
    public function get_value($key) {
        return isset($this->conditions[$key])
            ? $this->conditions[$key]
            : null;
    }

    /**
     * Returns a timestamp which is offset from the current time
     *
     * @param  string  $relation  before|after
     * @return int
     */
    public function get_offset_timestamp_from_now($relation) {
        return $this->get_offset_timestamp_from_timestamp(time(), $relation);
    }

    /**
     * Returns a timestamp which is offset from the given original timestamp
     *
     * Note: When calculating the offset, this uses set "time_amount" and "time_unit" values
     *
     * @param  string  $relation  before|after
     * @return int
     */

    public function get_offset_timestamp_from_timestamp($originaltimestamp, $relation) {
        // Get time offset (timestamp of condition-defined amount of time before current time).
        $date = \DateTime::createFromFormat('U', $originaltimestamp, \core_date::get_server_timezone_object());
        $date->modify($this->get_relation_symbol($relation)
            . $this->get_value('time_amount') . ' ' . $this->get_value('time_unit'));
        $offsettimestamp = $date->getTimestamp();

        return $offsettimestamp;
    }

    /**
     * Returns a "+" or "-" from the readable relation value
     *
     * Note: this is intended to be used internally for calculating time offsets
     *
     * @param  string  $relation  before|after
     * @return string
     */
    private function get_relation_symbol($relation) {
        return $relation == 'after'
            ? '+'
            : '-';
    }

}
