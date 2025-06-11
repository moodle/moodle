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
use block_quickmail_string;

class notification_condition_summary {

    public $langstringkey;
    public $params;

    public function __construct($langstringkey, $params = []) {
        $this->lang_string_key = $langstringkey;
        $this->params = $params;
    }

    /**
     * Returns an intelligently formatted condition summary string for a model
     *
     * @param  string $notificationtype
     * @param  string $modelkey
     * @param  array  $params
     * @return string
     */
    public static function get_model_condition_summary($notificationtype, $modelkey, $params = []) {
        // Get this model's supported keys.
        if (!$keys = notification_model_helper::get_condition_keys_for_model($notificationtype, $modelkey)) {
            return '';
        }

        // Get this model's condition summary lang string key.
        $langstringkey = notification_model_helper::get_condition_summary_lang_string($notificationtype, $modelkey);

        // Filter out any unnecessary params.
        $params = \block_quickmail_plugin::array_filter_key($params, function ($key) use ($keys) {
            return in_array($key, $keys);
        });

        // Instantiate this summary class.
        $summary = new self($langstringkey, $params);

        // Return formatted condition string.
        return $summary->format();
    }

    /**
     * Returns a formatted string
     *
     * @return string
     */
    public function format() {
        $params = $this->params;

        $langarray = [];

        // Iterate through each value, formatting and adding to the final array.
        foreach (array_keys($params) as $key) {
            $langarray[$key] = $this->format_condition_value($key, $params);
        }

        return block_quickmail_string::get($this->lang_string_key, (object) $langarray);
    }

    /**
     * Returns a formatted value for the given condition key
     *
     * @param  string  $key
     * @param  array   $values
     * @return string
     */
    private function format_condition_value($key, $values) {
        switch ($key) {
            case 'time_unit':
                if (array_key_exists('time_amount', $values)) {
                    // Check if needs to be pluralized.
                    return is_numeric($values['time_amount']) && $values['time_amount'] > 1
                        ? $values[$key] . 's'
                        : $values[$key];
                }
                break;

            // Time_amount.
            // Time_relation.
            // Grade_greater_than.
            // Grade_less_than.

            default:
                return $values[$key];
                break;
        }
    }

}
