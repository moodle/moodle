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

use block_quickmail_string;

class notification_schedule_summary {

    public $params;

    public static $dateformat = 'M d Y, h:ia';

    public function __construct($params = []) {
        $this->params = $params;
    }

    /**
     * Returns an intelligently formatted schedule summary string from params
     *
     * @param  array  $params
     * @return string
     */
    public static function get_from_params($params = []) {
        // Instantiate this summary class.
        $summary = new self($params);

        // Return formatted schedule string.
        return $summary->format();
    }

    /**
     * Returns a formatted string
     *
     * @return string
     */
    public function format() {
        $params = $this->params;

        if (!array_key_exists('time_amount', $this->params)) {
            return '';
        }

        if (!$this->params['time_amount']) {
            return '';
        }

        // Append time unit/amount details.
        $summary = $this->params['time_amount'] == 1
            ? block_quickmail_string::get('time_once_a') . ' ' . $this->display_time_unit($this->params['time_unit'])
            : block_quickmail_string::get('time_every') . ' '
            . $this->params['time_amount'] . ' '
            . $this->display_time_unit($this->params['time_unit'], $this->params['time_amount']);

        // If there is a begin date, format and append it.
        if (array_key_exists('begin_at', $this->params)) {
            if (is_numeric($this->params['begin_at'])) {
                $beginat = \DateTime::createFromFormat('U', $this->params['begin_at'], \core_date::get_server_timezone_object());

                $summary .= ', ' . block_quickmail_string::get('time_beginning') . ' ' . $beginat->format(self::$dateformat);
            }
        }

        // If there is an end date, format and append it.
        if (array_key_exists('end_at', $this->params)) {
            if (is_numeric($this->params['end_at'])) {
                $endat = \DateTime::createFromFormat('U', $this->params['end_at'], \core_date::get_server_timezone_object());

                $summary .= ', ' . block_quickmail_string::get('time_ending') . ' ' . $endat->format(self::$dateformat);
            }
        }

        return $summary;
    }

    private function display_time_unit($key, $amount = 0) {
        return $amount > 1
            ? block_quickmail_string::get('time_unit_' . $key . 's')
            : block_quickmail_string::get('time_unit_' . $key);
    }

}
