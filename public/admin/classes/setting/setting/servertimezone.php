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
 * Server timezone setting.
 *
 * @copyright 2015 Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Petr Skoda <petr.skoda@totaralms.com>
 */
class admin_setting_servertimezone extends admin_setting_configselect {
    /**
     * Constructor.
     */
    public function __construct() {
        $default = core_date::get_default_php_timezone();
        if ($default === 'UTC') {
            // Nobody really wants UTC, so instead default selection to the country that is confused by the UTC the most.
            $default = 'Europe/London';
        }

        parent::__construct('timezone',
            new lang_string('timezone', 'core_admin'),
            new lang_string('configtimezone', 'core_admin'), $default, null);
    }

    /**
     * Lazy load timezone options.
     * @return bool true if loaded, false if error
     */
    public function load_choices() {
        global $CFG;
        if (is_array($this->choices)) {
            return true;
        }

        $current = isset($CFG->timezone) ? $CFG->timezone : null;
        $this->choices = core_date::get_list_of_timezones($current, false);
        if ($current == 99) {
            // Do not show 99 unless it is current value, we want to get rid of it over time.
            $this->choices['99'] = new lang_string('timezonephpdefault', 'core_admin',
                core_date::get_default_php_timezone());
        }

        return true;
    }
}
