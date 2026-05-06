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
 * Forced user timezone setting.
 *
 * @copyright 2015 Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Petr Skoda <petr.skoda@totaralms.com>
 */
namespace core_admin\setting\setting;

class forcetimezone extends \core_admin\setting\setting\configselect {
    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct('forcetimezone',
            new \lang_string('forcetimezone', 'core_admin'),
            new \lang_string('helpforcetimezone', 'core_admin'), '99', null);
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

        $current = isset($CFG->forcetimezone) ? $CFG->forcetimezone : null;
        $this->choices = \core_date::get_list_of_timezones($current, true);
        $this->choices['99'] = new \lang_string('timezonenotforced', 'core_admin');

        return true;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(forcetimezone::class, \admin_setting_forcetimezone::class);
